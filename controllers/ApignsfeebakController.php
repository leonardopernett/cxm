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


  class ApignsfeebakController extends \yii\web\Controller {

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
                'actions' => ['apignsencuestas'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apignsencuestas'],
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

      $varHora = date("H");

      $varHoraInicio = strval(intval($varHora) - 1).':00:00';
      $varHoraFin = strval(intval($varHora) - 1).':59:59';

      if ($varHoraFin == '23:59:59') {
        $varDias = strval(intval(date("d") -1));
        $varFecha = date("Y-m-").$varDias;
      }else{
        $varFecha = date("Y-m-d");
      }

      $varFechaInicioEspecial_BD = $varFecha.'T'.$varHoraInicio;
      $varFechaFinEspecial_BD = $varFecha.'T'.$varHoraFin;

      $curl = curl_init();

      curl_setopt_array($curl, array(
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
          "Start": 1,
          "Limit": 20000
        }',
        CURLOPT_HTTPHEADER => array(
          'Authorization: Basic VVU0UkpQbUt1SFhVTnRramFPU0ZFdnY6SEhmb2Q2bXFOMjdYZUhwWjIyWTh1aEVE',
          'Content-Type: application/json'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
      ob_clean();

      $objet_json = json_decode($response,true);

      if (count($objet_json['Data']) != 0) {
        
        foreach ($objet_json['Data'] as $value) {
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

            if ($varDataJarvis_CC == "") {
              $varDataJarvis_CC = (new \yii\db\Query())
                          ->select(['tbl_evaluados.identificacion'])
                          ->from(['tbl_evaluados']) 
                          ->where(['like','tbl_evaluados.name',$varNombreAgente])
                          ->scalar();

              $varDataJarvis_User = (new \yii\db\Query())
                          ->select(['tbl_evaluados.dsusuario_red'])
                          ->from(['tbl_evaluados']) 
                          ->where(['=','tbl_evaluados.identificacion',$varDataJarvis_CC])
                          ->scalar();
            }



            // Se genera procesos para el detalle de Año, Mes, Dia y Hora
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

      $this->Generarrecalculartipologia();

      die(json_encode("Proceso Realizado"));

    }

    public function Generarrecalculartipologia(){

      //TRAIGO LAS ENCUESTAS SIN TIPOLOGIA
      $encuestas = BaseSatisfaccion::find()->select('id')->where("`tipologia` IS NULL")->all();

      if (empty($encuestas)) {
        echo "No hay procesos de actualizacion de tipologias ya que no se encontraron nuevas encuestas con tipologia null";
        exit;
      }
      
      //MENSAJE DE CONTROL                               
      $errores = "";
      foreach ($encuestas as $encuesta) {
        
        $model = BaseSatisfaccion::findOne($encuesta->id);
        $model->tipologia = 'NEUTRO';

        if (!empty($model->pcrc) && !empty($model->cliente)) {
          $sql = '
            SELECT ca.nombre, dp.categoria, p.pre_indicador, 
              dp.configuracion, dp.addNA, cg.name, cg.prioridad, cg.id
            FROM tbl_detalleparametrizacion dp
              JOIN tbl_categoriagestion cg 
                ON dp.id_categoriagestion = cg.id
              JOIN tbl_parametrizacion_encuesta pe 
                ON pe.id = cg.id_parametrizacion
              LEFT JOIN tbl_preguntas p 
                ON p.id_parametrizacion = pe.id 
                  AND p.categoria = dp.categoria
              JOIN tbl_categorias ca ON ca.id = dp.categoria 
            WHERE 
              pe.cliente = ' . $model->cliente
                . ' AND pe.programa = ' . $model->pcrc;
          
          $config = \Yii::$app->db->createCommand($sql)->queryAll();

          $prioridades = ArrayHelper::map($config, 'prioridad', 'name');
          $arrayCumpleRegla = [];

          if (count($config) > 0) {
            $conditon = '';
            $comando = '';
            $i = 1;
            $errorConfig = false;
            
            //Validamos si hay una mala configuracion-------------------                    
            foreach ($config as $value) {
              if (is_null($value['pre_indicador']) || empty($value['pre_indicador'])) {
                $errorConfig = true;
                \Yii::error('Categoria (' . $value['nombre']
                . '), Cliente(' . $model->cliente0->name
                . ') Programa(' . $model->pcrc0->name
                . ')  mal configuada, Por favor revise la '
                . 'configuracion', 'basesatisfaccion');
              }
            }

            if (!$errorConfig) {
              
              foreach ($config as $value) {
              
                if (!empty($value['configuracion'])) {
                  
                  $preExplode = explode('-', $value['configuracion']);
                  $explode = explode('||', $preExplode[0]);
                  
                  if (isset($explode[0]) && isset($explode[1]) && isset($explode[2])) {

                    if (str_replace(['(', ')'], '', $explode[0]) == BaseSatisfaccion::OP_AND) {
                    
                      if (isset($preExplode[1])) {
                      
                        $explodeAddNA = explode('||', $preExplode[1]);
                        $tmpConditon = ' && ($model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explode[1]
                          . ' "'
                          . $explode[2] . '" ';
                          
                        $tmpConditon .= ' || $model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explodeAddNA[1]
                          . ' "'
                          . $explodeAddNA[2] . '" )';
                      
                      } else {
                        
                        $tmpConditon = ' && (is_numeric($model->' . $value['pre_indicador'] . ') && $model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explode[1]
                          . ' "'
                          . $explode[2] . '") ';
                      }
                      
                    } else {
                      
                      $tmpConditon = ' || $model->'
                        . $value['pre_indicador']
                        . ' '
                        . $explode[1]
                        . ' "'
                        . $explode[2] . '" ';
                    
                    }

                    if (!isset($config[$i]['id']) || $value['id'] != $config[$i]['id']) {
                    
                      $conditon = substr($conditon, 4);
                      
                      if (!$conditon) {
                        $tmpConditon = substr($tmpConditon, 4);
                      }
                      
                      $eval = '('
                        . $conditon
                        . $tmpConditon
                        . ')';
                        
                      $conditon = '';
                      
                      $comando .= '#####' . $eval;
                        eval("\$restCond = $eval;");
                      
                      if ($restCond) {
                        
                        $arrayCumpleRegla[] = 'true';
                        $model->tipologia = $value['name'];
                      
                      } else {
                        
                        $arrayCumpleRegla[] = 'false';
                      
                      }
                                            
                    } else {
                      
                      $conditon .= $tmpConditon;
                    
                    }
                    
                  }
                
                }
                
                $i++;
              
              }

              $contarValores = array_count_values($arrayCumpleRegla);

              //Contamos el numero de true en $arrayCumpleRegla---------------
              if (isset($contarValores['true']) && $contarValores['true'] > 1) {
                //sacamos el que tenga prioridad mas alta ------------------                    
                $model->tipologia = $prioridades[min(array_keys($prioridades))];
              }
            }
          }
        }

        
        //GUARDAMOS LOS DATOS-----------------------------------------------
        if (!$model->save()) {
          $errores .= "<br />ID Encuesta: " . $encuesta->id . "<br />";
        }

      }

      if (!empty($errores)) {
      
        echo '<div style="background-color: #f2dede; '
        . 'border-color: #ebccd1; color: #a94442;'
        . ' padding: 10px;">'
        . 'ENCUESTAS QUE NO PUDIERON SER RECALCULADAS: '
        . $errores
        . '</div>';
        
      } else {
      
        echo '<div style="background-color: #dff0d8; '
        . 'border-color: #d6e9c6; color: #3c763d;'
        . ' padding: 10px;">'
        . 'PROCESO TERMINADO CON &Eacute;XITO'
        . '</div>';
      
      }

    }

  }

?>
