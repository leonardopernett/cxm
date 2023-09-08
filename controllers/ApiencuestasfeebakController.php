<?php

namespace app\controllers;


use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\db\mssql\PDO;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\helpers\Url;
use app\models\BaseSatisfaccion; 
use PHPExcel;
use PHPExcel_IOFactory;
use GuzzleHttp;


  class ApiencuestasfeebakController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'verbs' => [          
          'class' => VerbFilter::className(),
          'actions' => [
            'delete' => ['post'],
          ],
        ],

        'access' => [
            'class' => AccessControl::classname(),
            'denyCallback' => function ($rule, $action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
            },

            
            'rules' => [
              [
                'actions' => ['apignstemporal'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apignstemporal'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApignstemporal(){
      $varSinInfo = "Sin información";
      $varNoAplica = "NO APLICA";
      $varHoras = date("H");
      $varHora = null;

      if ($varHoras <= '10') {        
        $varHora = '0'.(strval(intval($varHoras) - 1));
      }else{        
        $varHora = strval(intval($varHoras) - 1);
      }

      $varHoraInicio = $varHora.':00:00';
      $varHoraFin = $varHora.':59:59';

      if ($varHoraFin == '23:59:59') {
        $varDias = strval(intval(date("d") -1));
        $varFecha = date("Y-m-").$varDias;
      }else{
        $varFecha = date("Y-m-d");
      }

      $varFechaInicioEspecial_BD = $varFecha.'T'.$varHoraInicio;
      $varFechaFinEspecial_BD = $varFecha.'T'.$varHoraFin;

      $varListaEncuestasFeebak = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_gnsfeebak_tmpencuestas']) 
                            ->where(['=','tbl_gnsfeebak_tmpencuestas.anulado',0])
                            ->andwhere(['>=','tbl_gnsfeebak_tmpencuestas.AddedDate',$varFechaInicioEspecial_BD])
                            ->andwhere(['is not','tbl_gnsfeebak_tmpencuestas.Answers_0',null])
                            ->andwhere(['is not','tbl_gnsfeebak_tmpencuestas.ConversationID',null])
                            ->all();

      foreach ($varListaEncuestasFeebak as $value) {
        $varConnid = $value['ConversationID'];

        $varConteoConnid = (new \yii\db\Query())
                            ->select(['tbl_base_satisfaccion.id'])
                            ->from(['tbl_base_satisfaccion']) 
                            ->where(['=','tbl_base_satisfaccion.connid',$varConnid])
                            ->count();

        if ($varConteoConnid == 0) {          

          // Información Cliente
          $varIdentificacion = $value['CustomerId'];
          if ($varIdentificacion == "") {
            $varIdentificacion = $varSinInfo;
          }

          $varNombre = $value['CustomerName'];
          if ($varNombre == "") {
            $varNombre = $varSinInfo;
          }

          // Informacion GNS
          $varAni = "GNS_Feebak";

          // Información Asesores
          $varAsesorUsuario = null;
          $varAsesorDoc = null;
          $varAgente_Gns = $value['AgentName'];
          $varParamsAsesor = [":varAsesorGns"=>'%'.$varAgente_Gns.'%'];

          $varDocumentoAsesor = Yii::$app->dbjarvis->createCommand("
            SELECT dp_datos_generales.documento FROM dp_datos_generales 
              WHERE 
                dp_datos_generales.nombre_completo LIKE :varAsesorGns
          ")->bindValues($varParamsAsesor)->queryScalar();

          if ($varDocumentoAsesor) {
            $varAsesorUsuario = (new \yii\db\Query())
                            ->select(['tbl_evaluados.dsusuario_red'])
                            ->from(['tbl_evaluados']) 
                            ->where(['=','tbl_evaluados.identificacion',$varDocumentoAsesor])
                            ->scalar();
            $varAsesorDoc = $varDocumentoAsesor;
          }else{
            $varAsesorUsuario = $varSinInfo;
            $varAsesorDoc = $varSinInfo;
          }

          // Informacion Tiempo
          $varAnnioGNS = date("Y",strtotime($value['AddedDate']));
          $varMesGNS = date("m",strtotime($value['AddedDate']));
          $vardiaGNS = substr($value['AddedDate'], 8, -14);

          $varReplaceHour = str_replace("T", " ", $value['AddedDate']);
          $varFehaHora = $varAnnioGNS."-".$varMesGNS."-".$vardiaGNS." ".substr($varReplaceHour, 11, -11).date(":i:s",strtotime($varReplaceHour));
          $varFechaInteraccion = date('Y-m-d H:i:s', strtotime('-5 hour', strtotime($varFehaHora)));

          $varHoraGNS = str_replace(":", "", date('H:i:s', strtotime($varFechaInteraccion)));

          // Informacion Servicio
          $varNombreCola = $value['QueueName'];
          $varPcrc = (new \yii\db\Query())
                            ->select(['tbl_genesys_formularios.arbol_id'])
                            ->from(['tbl_genesys_formularios']) 
                            ->where(['=','tbl_genesys_formularios.anulado',0])
                            ->andwhere(['like','tbl_genesys_formularios.cola_genesys',$varNombreCola])
                            ->groupby(['tbl_genesys_formularios.arbol_id'])
                            ->scalar();

          $varCliente = (new \yii\db\Query())
                            ->select(['tbl_arbols.arbol_id'])
                            ->from(['tbl_arbols']) 
                            ->where(['=','tbl_arbols.id',$varPcrc])
                            ->groupby(['tbl_arbols.arbol_id'])
                            ->scalar();


          $varListaParametrizaRn = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_reglanegocio']) 
                            ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                            ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                            ->groupby(['tbl_reglanegocio.pcrc'])
                            ->all();

          $varTipoServicio = "GNS";

          $varNoAplica = "NO APLICA";

          $varPregunta1 = $value['Answers_0'];
          $varPregunta2 = $value['Answers_1'];
          if ($varPregunta2 == null) {
            $varPregunta2 = $varNoAplica;
          }
          $varPregunta3 = $value['Answers_2'];
          if ($varPregunta3 == null) {
            $varPregunta3 = $varNoAplica;
          }

          $varComentarios = $value['FeedbackText'];

          $varScoreAnswer = $value['ScoreAnswer_0'];
          $varNombrePregunta = $value['NombrePregunta_0'];
          $varNombreNivel = $value['NombreNivel_0'];
          $varInteraccion = $value['Interaccion_0'];

          $varRn = null;
          $varConInstitucion = null;
          $varConIndustria = null;
          foreach ($varListaParametrizaRn as $value) {
            $varRn = $value['rn'];
            $varConInstitucion = $value['cod_institucion'];
            $varConIndustria = $value['cod_industria'];
          }

          $varListaEquipos = (new \yii\db\Query())
                                    ->select([
                                        'tbl_usuarios.usua_id', 'tbl_usuarios.usua_nombre', 'tbl_usuarios.usua_identificacion'
                                    ])
                                    ->from(['tbl_usuarios'])
                                    ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                          'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                                    ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                          'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')
                                    ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                          'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')
                                    ->where(['=','tbl_evaluados.identificacion',$varDocumentoAsesor])
                                    ->all();

          $varUsuaLider = null;
          $varLider = null;
          $varCCLider = null;
          foreach ($varListaEquipos as $value) {
            $varUsuaLider = $value['usua_id'];
            $varLider = $value['usua_nombre'];
            $varCCLider = $value['usua_identificacion'];
          }          

          if ($varRn) {
            Yii::$app->db->createCommand()->insert('tbl_base_satisfaccion',[
                        'identificacion' => $varIdentificacion,
                        'nombre' => $varNombre,
                        'ani' => $varAni,
                        'agente' => $varAsesorUsuario,
                        'cc_agente' => $varAsesorDoc,
                        'agente2' => null,
                        'ano' => $varAnnioGNS,
                        'mes' => $varMesGNS,
                        'dia' => $vardiaGNS,
                        'hora' => $varHoraGNS,
                        'chat_transfer' => null,
                        'ext' => null,
                        'rn' => $varRn,
                        'industria' => $varConIndustria,
                        'institucion' => $varConInstitucion,
                        'pcrc' => $varPcrc,
                        'cliente' => $varCliente,
                        'tipo_servicio' => $varTipoServicio,
                        'pregunta1' => $varPregunta1,
                        'pregunta2' => $varPregunta2,
                        'pregunta3' => $varPregunta3,
                        'pregunta4' => $varNoAplica,
                        'pregunta5' => $varNoAplica,
                        'pregunta6' => $varNoAplica,
                        'pregunta7' => $varNoAplica,
                        'pregunta8' => $varNoAplica,
                        'pregunta9' => $varNoAplica,
                        'pregunta10' => $varNoAplica,
                        'connid' => $varConnid,
                        'tipo_encuesta' => 'A',
                        'comentario' => $varComentarios,
                        'id_lider_equipo' => $varUsuaLider,
                        'lider_equipo' => $varLider,
                        'cc_lider' => $varCCLider,
                        'coordinador' => null,
                        'jefe_operaciones' => null,
                        'tipologia' => null,
                        'estado' => 'Abierto',
                        'llamada' => null,
                        'buzon' => null,
                        'responsable' => null,
                        'usado' => 'NO',
                        'fecha_gestion' => null,
                        'created' => date("Y-m-d h:i:s"),
                        'tipo_inbox' => 'NORMAL',
                        'responsabilidad' => null,
                        'canal' => null,
                        'marca' => null,
                        'equivocacion' => null,
                        'fecha_satu' => $varFechaInteraccion,
                        'aliados' => 'GNS',
                        'modalidad_encuesta' => null,
              ])->execute();

            
              Yii::$app->db->createCommand()->insert('tbl_base_genesysencuestas',[
                        'arbol_id' => $varPcrc,
                        'cola_genesys' => $varNombreCola,
                        'connid' => $varConnid,
                        'score_respuesta' => $varScoreAnswer,
                        'nombre_pregunta' => $varNombrePregunta,
                        'nombre_nivel' => $varNombreNivel,
                        'fecha_interaccion' => $varFechaInteraccion,
                        'call_id' => $varInteraccion,
                        'anulado' => 0,
                        'usua_id' => 1,
                        'fechacreacion' => date("Y-m-d"),
              ])->execute();
          }
          
        }
      }

      die();

    }    

  }

?>
