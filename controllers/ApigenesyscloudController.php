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


  class ApigenesyscloudController extends \yii\web\Controller {

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
                'actions' => ['apignsasesores','apignsencuestas'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apignsasesores','apignsencuestas'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApignsencuestas(){
      $varTotalPaginado = null;
      $varListaEncuestas = null;
      $varHoras = date("H");
      $varHora = null;

      if ($varHoras < 10) {
        $varHora = '0'.(strval(intval($varHoras) - 1));
      }else{
        $varHora = strval(intval($varHoras) - 1);
      }

      $varHoraInicio = '00:00:00';
      $varHoraFin = $varHora.':59:59';

      if ($varHoraFin == '23:59:59') {
        $varDias = strval(intval(date("d") -1));
        $varFecha = date("Y-m-").$varDias;
      }else{
        $varFecha = date("Y-m-d");
      }

      $varFechaInicioEspecial_BD = $varFecha.'T'.$varHoraInicio;
      $varFechaFinEspecial_BD = $varFecha.'T'.$varHoraFin;

      $varListarColas = (new \yii\db\Query())
                        ->select(['tbl_genesys_formularios.id_cola_genesys'])
                        ->from(['tbl_genesys_formularios'])
                        ->where(['=','tbl_genesys_formularios.anulado',0])
                        ->groupby(['tbl_genesys_formularios.id_cola_genesys'])
                        ->all();

      $varArrayColas = array();
      foreach ($varListarColas as $value) {
        array_push($varArrayColas, $value['id_cola_genesys']);
      }
      $varListadoColas = implode('", "', $varArrayColas);

      // Primera accion se obtiene la cantidad de datos a utilizar
      ob_start();
      $curlPaginado = curl_init();

      curl_setopt_array($curlPaginado, array(
        CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_URL => 'https://app.feebak.com/v1/dataexport/interactions?organisationId=39',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
          "FromDate": "'.$varFechaInicioEspecial_BD.'",
          "ToDate": "'.$varFechaFinEspecial_BD.'",
          "Start": 0,
          "Limit": 1,
          "Completed": true,
          "Queueidentifiers": ["'.$varListadoColas.'"]
        }',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Authorization: Basic VVU0UkpQbUt1SFhVTnRramFPU0ZFdnY6SEhmb2Q2bXFOMjdYZUhwWjIyWTh1aEVE'
        ),
      ));

      $responsePaginado = curl_exec($curlPaginado);

      curl_close($curlPaginado);
      ob_clean();
      $varTotalPaginado_one = json_decode($responsePaginado,true); 
      $varTotalPaginado = substr($varTotalPaginado_one['TotalCount'],0,1) + 1;

      // Segunda accion para obtener los datos de acuerdo al paginado por dia o por hora.
      for ($i=0; $i < $varTotalPaginado; $i++) { 
      
        $varStart = $i.'0'.$i;
        $varLimit = ($i + 1).'00';

        ob_start();
        $curlEncuestas = curl_init();

        curl_setopt_array($curlEncuestas, array(
          CURLOPT_SSL_VERIFYPEER=> false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_URL => 'https://app.feebak.com/v1/dataexport/interactions?organisationId=39',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{
            "FromDate": "'.$varFechaInicioEspecial_BD.'",
            "ToDate": "'.$varFechaFinEspecial_BD.'",
            "Start": '.$varStart.',
            "Limit": '.$varLimit.',
            "Completed": true,
            "Queueidentifiers": ["'.$varListadoColas.'"]
          }',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Basic VVU0UkpQbUt1SFhVTnRramFPU0ZFdnY6SEhmb2Q2bXFOMjdYZUhwWjIyWTh1aEVE'
          ),
        ));

        $responseEncuestas = curl_exec($curlEncuestas);

        curl_close($curlEncuestas);
        ob_clean();
        $varListaEncuestas = json_decode($responseEncuestas,true);
        
        if (count($varListaEncuestas['Data']) != 0) {
          foreach ($varListaEncuestas['Data'] as $value) {

            if (count($value['Answers']) != 0 && $value['QueueName'] != "") {
              $varIdentificacion = $value['CustomerId'];
              if ($varIdentificacion == "") {
                $varIdentificacion = "Sin información";
              }
              $varNombre = $value['CustomerName'];
              if ($varNombre == "") {
                $varNombre = "Sin información";
              }

              // Se genera busqueda del proceso agente para la cc y el usuario de red 
              $varNombreAgente = $value['AgentName'];
              $paramsBusqueda = [':varAgenteCompleto' => $varNombreAgente];

              $varDataJarvis_CC = Yii::$app->dbjarvis->createCommand('
                SELECT dp_usuarios_red.documento FROM dp_usuarios_red 
                  WHERE 
                    dp_usuarios_red.nombre IN (:varAgenteCompleto)
                ')->bindValues($paramsBusqueda)->queryScalar();

              $varDataJarvis_User = Yii::$app->dbjarvis->createCommand('
                SELECT dp_usuarios_red.usuario_red FROM dp_usuarios_red 
                  WHERE 
                    dp_usuarios_red.nombre IN (:varAgenteCompleto)
                ')->bindValues($paramsBusqueda)->queryScalar();

              if ($varDataJarvis_CC != null) {
                $varDataJarvis_CC = (new \yii\db\Query())
                          ->select(['tbl_evaluados.identificacion'])
                          ->from(['tbl_evaluados']) 
                          ->where(['=','tbl_evaluados.identificacion',$varDataJarvis_CC])
                          ->scalar();

                $varDataJarvis_User = (new \yii\db\Query())
                          ->select(['tbl_evaluados.dsusuario_red'])
                          ->from(['tbl_evaluados']) 
                          ->where(['=','tbl_evaluados.identificacion',$varDataJarvis_CC])
                          ->scalar();
              }else{
                $varDataJarvis_CC = 'NA';
                $varDataJarvis_User = 'NA';
              }

              // // Se genera procesos para el detalle de Año, Mes, Dia y Hora
              $varAnnioGNS = date("Y",strtotime($value['AddedDate']));
              $varMesGNS = date("m",strtotime($value['AddedDate']));
              $vardiaGNS = substr($value['AddedDate'], 8, -14);

              $varReplaceHour = str_replace("T", " ", $value['AddedDate']);
              $varHoraGNS = substr($varReplaceHour, 11, -11).date("is",strtotime($varReplaceHour));

              $varTiempoInteraccion = $varAnnioGNS."-".$varMesGNS."-".$vardiaGNS." ".substr($varReplaceHour, 11, -11).date(":i:s",strtotime($varReplaceHour));

              $varTiempoInteraccionAjuste = date("Y-m-d h:i:s",strtotime($value['SurveyEndTime']));

              $varExt = $value['InviteJobId'];

              // Se genera proceso para revision de Cliente, Pcrc, RN, Cod_Ind y Cod_Ins
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

              $varRn = (new \yii\db\Query())
                            ->select(['tbl_reglanegocio.rn'])
                            ->from(['tbl_reglanegocio']) 
                            ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                            ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                            ->groupby(['tbl_reglanegocio.pcrc'])
                            ->scalar();

              $varConInstitucion = (new \yii\db\Query())
                            ->select(['tbl_reglanegocio.cod_institucion'])
                            ->from(['tbl_reglanegocio']) 
                            ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                            ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                            ->groupby(['tbl_reglanegocio.pcrc'])
                            ->scalar();

              $varConIndustria = (new \yii\db\Query())
                            ->select(['tbl_reglanegocio.cod_industria'])
                            ->from(['tbl_reglanegocio']) 
                            ->where(['=','tbl_reglanegocio.pcrc',$varPcrc])
                            ->andwhere(['=','tbl_reglanegocio.cliente',$varCliente])
                            ->groupby(['tbl_reglanegocio.pcrc'])
                            ->scalar();

              $varConnid = $value['ConversationID'];

              // Se genera proceso para repartir las preguntas  
              $varPreguntas_Uno = $value['Answers'][0]['Answer'];
              $varPreguntas_Dos = $value['Answers'][1]['Answer'];
              $varPreguntas_Tres = $value['Answers'][2]['Answer'];

              $varScoreAnswer = $value['Answers'][0]['AnswerScore'];
              $varNombrePregunta = $value['Answers'][0]['QuestionName'];
              $varNombreNivel = $value['Answers'][0]['LevelName'];
              $varInteraccion = $value['Answers'][0]['CallID'];

              // Se genera proceso para busqueda de Lider y Equipo
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
                                    ->where(['=','tbl_evaluados.identificacion',$varDataJarvis_CC])
                                    ->all();

              $varComentarios = $value['FeedbackText'];

              $varUsuaLider = null;
              $varLider = null;
              $varCCLider = null;
              foreach ($varListaEquipos as $value) {
                $varUsuaLider = $value['usua_id'];
                $varLider = $value['usua_nombre'];
                $varCCLider = $value['usua_identificacion'];
              }

              $varNoAplica = "NO APLICA";

              // Se genera proceso para verificar la existencia del Connid como encuesta
              $varExisteConnid = (new \yii\db\Query())
                                  ->select([
                                      'tbl_base_satisfaccion.connid'
                                  ])
                                  ->from(['tbl_base_satisfaccion'])
                                  ->where(['=','tbl_base_satisfaccion.connid',$varConnid])
                                  ->count();

              if ($varExisteConnid == 0) {
                if ($varPreguntas_Uno != "0") {
                  Yii::$app->db->createCommand()->insert('tbl_base_satisfaccion',[
                        'identificacion' => $varIdentificacion,
                        'nombre' => $varNombre,
                        'ani' => 'GNS_Feebak',
                        'agente' => $varDataJarvis_User,
                        'cc_agente' => $varDataJarvis_CC,
                        'agente2' => null,
                        'ano' => $varAnnioGNS,
                        'mes' => $varMesGNS,
                        'dia' => $vardiaGNS,
                        'hora' => $varHoraGNS,
                        'chat_transfer' => null,
                        'ext' => $varExt,
                        'rn' => $varRn,
                        'industria' => $varConIndustria,
                        'institucion' => $varConInstitucion,
                        'pcrc' => $varPcrc,
                        'cliente' => $varCliente,
                        'tipo_servicio' => 'GNS',
                        'pregunta1' => $varPreguntas_Uno,
                        'pregunta2' => $varPreguntas_Dos,
                        'pregunta3' => $varPreguntas_Tres,
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
                        'fecha_gestion' => $varTiempoInteraccionAjuste,
                        'created' => date("Y-m-d h:i:s"),
                        'tipo_inbox' => 'NORMAL',
                        'responsabilidad' => null,
                        'canal' => null,
                        'marca' => null,
                        'equivocacion' => null,
                        'fecha_satu' => $varTiempoInteraccion,
                        'aliados' => 'GNB',
                        'modalidad_encuesta' => null,
                  ])->execute();

                  Yii::$app->db->createCommand()->insert('tbl_base_genesysencuestas',[
                        'arbol_id' => $varPcrc,
                        'cola_genesys' => $varNombreCola,
                        'connid' => $varConnid,
                        'score_respuesta' => $varScoreAnswer,
                        'nombre_pregunta' => $varNombrePregunta,
                        'nombre_nivel' => $varNombreNivel,
                        'fecha_interaccion' => $varTiempoInteraccion,
                        'call_id' => $varInteraccion,
                        'anulado' => 0,
                        'usua_id' => 1,
                        'fechacreacion' => date("Y-m-d"),
                  ])->execute();
                }
              }

            }
            
          }
        }        

      }     

      die();
    }

    public function actionApignsasesores(){
      $varToken = null;
      $varCantidadPaginado = null;
      $varListaAsesores = null;

      // Primera accion Se genera el Token para ser usado en las APIs
      ob_start();
      $curlToken = curl_init();

      curl_setopt_array($curlToken, array(
        CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_URL => 'https://login.mypurecloud.com/oauth/token',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'grant_type=client_credentials',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/x-www-form-urlencoded',
          'Authorization: Basic YWI0ODEyMGYtMWMyYi00NDAxLTkzMzktYjFhM2JlMmYxY2UyOkNtX2loUDF5VE9oWTI3Sjl4ZmhReHJua2F0djQtUnB6bHpQLW1DdVQ5eEk='
        ),
      ));

      $responseToken = curl_exec($curlToken);
      curl_close($curlToken);
      ob_clean();
      $varToken = json_decode($responseToken,true); 

      // Segunda accion se genera cantidad de procesos que tiene la api de los usuarios
      ob_start();
      $curlCantidad = curl_init();

      curl_setopt_array($curlCantidad, array(
        CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_URL => 'https://api.mypurecloud.com/api/v2/users?pageSize=100&pageNumber=1',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/json',
          'Authorization: Bearer '.$varToken['access_token'].''
        ),
      ));

      $responseCantidad = curl_exec($curlCantidad);
      curl_close($curlCantidad);
      ob_clean();
      $varCantidadPaginado = json_decode($responseCantidad,true); 

      // Tercera accion se procede a recorrer cada pagina encontrando los asesores de konecta y guardando en la base.
      for ($i=1; $i <= $varCantidadPaginado; $i++) { 
      
        $varRuta = 'https://api.mypurecloud.com/api/v2/users?pageSize=100&pageNumber='.$i;
        
        ob_start();
        $curlAsesores = curl_init();

        curl_setopt_array($curlAsesores, array(
          CURLOPT_SSL_VERIFYPEER=> false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_URL => $varRuta,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'GET',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json',
            'Authorization: Bearer '.$varToken['access_token'].''
          ),
        ));

        $responseAsesores = curl_exec($curlAsesores);
        curl_close($curlAsesores);
        ob_clean();
        $varListaAsesores = json_decode($responseAsesores,true); 

        foreach ($varListaAsesores['entities'] as $value) {

          if (strlen(strstr($value['name'], 'Agente')) == 0) {
            
            $varSocidad = $value['division']['name'];

            if ($varSocidad == 'Konecta') {

              $varParams = [':varNombres'=>$value['name']];
              $varDocumento = Yii::$app->dbjarvis->createCommand('
                SELECT dp_usuarios_red.documento FROM dp_usuarios_red 
                  WHERE 
                    dp_usuarios_red.nombre IN (:varNombres)
                ')->bindValues($varParams)->queryScalar();

              if ($varDocumento != null) {
                $varComprobacionAsesorGns = (new \yii\db\Query())
                                  ->select(['tbl_genesys_parametroasesor.id_genesys'])
                                  ->from(['tbl_genesys_parametroasesor'])
                                  ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                                  ->andwhere(['=','tbl_genesys_parametroasesor.documento_asesor',$varDocumento])
                                  ->count();

                if ($varComprobacionAsesorGns == 0) {
                  Yii::$app->db->createCommand()->insert('tbl_genesys_parametroasesor',[
                                        'id_genesys' => $value['id'],
                                        'nombre_asesor' => $value['name'],
                                        'documento_asesor' => $varDocumento,
                                        'username_asesor' => $value['username'],
                                        'selfUri' => $value['selfUri'], 
                                        'usua_id' => 1,
                                        'fechacreacion' => date('Y-m-d'),
                                        'anulado' => 0,                         
                  ])->execute();
                }
              }else{
                $varComprobacionNovedadGns = (new \yii\db\Query())
                                  ->select(['tbl_genesys_novedades.id_genesys'])
                                  ->from(['tbl_genesys_novedades'])
                                  ->where(['=','tbl_genesys_novedades.anulado',0])
                                  ->andwhere(['=','tbl_genesys_novedades.id_genesys',$value['id']])
                                  ->count();

                if ($varComprobacionNovedadGns == 0) {
                  Yii::$app->db->createCommand()->insert('tbl_genesys_novedades',[
                                        'id_genesys' => $value['id'],
                                        'nombre_asesor' => $value['name'],
                                        'sociedad' => $varSocidad,
                                        'title' => 'NA',
                                        'selfUri' => $value['selfUri'], 
                                        'usua_id' => 1,
                                        'fechacreacion' => date('Y-m-d'),
                                        'anulado' => 0,                         
                  ])->execute();
                }
                
              }
            }
          }
          
        }

      }

      die();
    }


  }

?>
