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
use PHPExcel;
use PHPExcel_IOFactory;
use app\models\UploadForm2;
use app\models\GenesysNovedades;
use app\models\GenesysParametroasesor;
use app\models\GenesysFormularios;
use GuzzleHttp;
use Exception;

  class ProcesosgenesysController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','novedadesasesor','editarasesor_one','buscarencuestas','buscarllamadas'],
              'rules' => [
                [
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function() {
                    return Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo();
                  },
                ],
              ]
            ],
          'verbs' => [          
            'class' => VerbFilter::className(),
            'actions' => [
              'delete' => ['get'],
            ],
          ],

          'corsFilter' => [
            'class' => \yii\filters\Cors::class,
        ],
        ];
    } 

    public function actions() {
      return [
          'error' => [
            'class' => 'yii\web\ErrorAction',
          ]
      ];
    }
   
    public function actionIndex(){
      $varListaColas = (new \yii\db\Query())
                                ->select(['tbl_genesys_formularios.id_genesysformularios'])
                                ->from(['tbl_genesys_formularios'])
                                ->where(['=','tbl_genesys_formularios.anulado',0])
                                ->groupby(['tbl_genesys_formularios.id_cola_genesys'])
                                ->all();
      $varCantidadCola = count($varListaColas);

      $varCantidadAsesores = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_parametroasesor'])
                                ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                                ->count();

      $varCantidadNovedades = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_novedades'])
                                ->where(['=','tbl_genesys_novedades.anulado',0])
                                ->count();


      return $this->render('index',[
        'varCantidadCola' => $varCantidadCola,
        'varCantidadAsesores' => $varCantidadAsesores,
        'varCantidadNovedades' => $varCantidadNovedades,
      ]);
    }

    public function actionNovedadesasesor(){
      $varListadoNovedades = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_novedades'])
                                ->where(['=','tbl_genesys_novedades.anulado',0])
                                ->all();

      $varCantidadNovedadesAsesor = count($varListadoNovedades);

      return $this->render('novedadesasesor',[
        'varCantidadNovedadesAsesor' => $varCantidadNovedadesAsesor,
        'varListadoNovedades' => $varListadoNovedades,
      ]);
    }

    public function actionEditarasesor_one($id){
      $model = new GenesysParametroasesor();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varidgenesys = $model->id_genesys;
        $varAsesor = $model->nombre_asesor;
        $varSelf = $model->selfUri;
        $varDocumento = $model->documento_asesor;
        $varCorreo = $model->username_asesor;

        $varVerificar = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_parametroasesor'])
                                ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                                ->andwhere(['=','tbl_genesys_parametroasesor.documento_asesor',$varDocumento])
                                ->count();

        if ($varVerificar == 0) {
          Yii::$app->db->createCommand()->insert('tbl_genesys_parametroasesor',[
                                        'id_genesys' => $varidgenesys,
                                        'nombre_asesor' => $varAsesor,
                                        'documento_asesor' => $varDocumento,
                                        'username_asesor' => $varCorreo,
                                        'selfUri' => $varSelf, 
                                        'usua_id' => Yii::$app->user->identity->id,
                                        'fechacreacion' => date('Y-m-d'),
                                        'anulado' => 0,                         
          ])->execute();

          Yii::$app->db->createCommand()->update('tbl_genesys_novedades',[
                      'anulado' => 1,                       
          ],'id_novedades ='.$id.'')->execute();
        }else{
          Yii::$app->db->createCommand()->update('tbl_genesys_novedades',[
                      'anulado' => 1,                       
          ],'id_novedades ='.$id.'')->execute();
        }
        

        return $this->redirect(['novedadesasesor']);
      }

      return $this->renderAjax('editarasesor_one',[
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionVerificarasesores(){
      $varListadoAsesores = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_parametroasesor'])
                                ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                                ->all();

      return $this->renderAjax('verificarasesores',[
        'varListadoAsesores' => $varListadoAsesores,
      ]);
    }

    public function actionVerificarcolas(){
      $varListadoColas = (new \yii\db\Query())
                                ->select([
                                  'tbl_arbols.name', 
                                  'tbl_genesys_formularios.cola_genesys',
                                  'tbl_genesys_formularios.id_cola_genesys'])
                                ->from(['tbl_arbols'])
                                ->join('LEFT OUTER JOIN', 'tbl_genesys_formularios',
                                  'tbl_arbols.id = tbl_genesys_formularios.arbol_id')
                                ->where(['=','tbl_genesys_formularios.anulado',0])
                                ->all();

      return $this->renderAjax('verificarcolas',[
        'varListadoColas' => $varListadoColas,
      ]);
    }

    public function actionBuscarencuestas(){
      $model = new GenesysFormularios();
      $varGenerarLista = 0;
      $varPaginadoTotal = 0;
      $varFechaInicio = null;
      $varFechaFinal = null;
      $varListadoColas = null;
      $varListaEncuestas = null;
      $varArrayEncuestas = array();
      $varNombreArbol = null;
      $varPreguntas = null;

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varArbol = $model->arbol_id;
        $varFechaGeneral = explode(" ", $model->fechacreacion);
        $varFechaInicio = $varFechaGeneral[0].'T00:00:00';
        $varFechaFinal = $varFechaGeneral[2].'T23:59:59';

        $varListarColas = (new \yii\db\Query())
                        ->select(['tbl_genesys_formularios.id_cola_genesys'])
                        ->from(['tbl_genesys_formularios'])
                        ->where(['=','tbl_genesys_formularios.anulado',0])
                        ->andwhere(['=','tbl_genesys_formularios.arbol_id',$varArbol])
                        ->groupby(['tbl_genesys_formularios.id_cola_genesys'])
                        ->all();

        if (count($varListarColas) != 0) {
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
              "FromDate": "'.$varFechaInicio.'",
              "ToDate": "'.$varFechaFinal.'",
              "Start": 0,
              "Limit": 100,
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
          $varTotalPaginado = json_decode($responsePaginado,true);

          $varPaginadoTotal = substr($varTotalPaginado['TotalCount'],0,1) + 1;

          
          if ($varPaginadoTotal != 0) {

            for ($i=0; $i < $varPaginadoTotal; $i++) { 
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
                      "FromDate": "'.$varFechaInicio.'",
                      "ToDate": "'.$varFechaFinal.'",
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
                  
                  var_dump($varListaEncuestas['Data']);
                  die();
                 /*if(is_array($varListaEncuestas['Data']) || is_object($varListaEncuestas['Data']) ){*/
                   foreach ($varListaEncuestas['Data'] as $value) {
                     array_push($varArrayEncuestas, array("AgentNames"=>$value['AgentName'],"AddDates"=>$value['AddedDate'],"QueueNames"=>$value['QueueName'],"ConversationIDs"=>$value['ConversationID'],"Answer1s"=>$value['Answers'][0]['Answer'],"Answer2s"=>$value['Answers'][1]['Answer'],"Answer3s"=>$value['Answers'][2]['Answer']));
                   }
                /* }*/
            }
            
            $varPreguntas = (new \yii\db\Query())
                        ->select(['tbl_preguntas.enunciado_pre'])
                        ->from(['tbl_preguntas'])
                        ->join('LEFT OUTER JOIN', 'tbl_parametrizacion_encuesta',
                                  'tbl_preguntas.id_parametrizacion = tbl_parametrizacion_encuesta.id')
                        ->where(['=','tbl_parametrizacion_encuesta.programa',$varArbol])
                        ->andwhere(['!=','tbl_preguntas.categoria',8])
                        ->all();

            $varNombreArbol = (new \yii\db\Query())
                        ->select(['tbl_arbols.name'])
                        ->from(['tbl_arbols'])
                        ->where(['=','tbl_arbols.id',$varArbol])
                        ->scalar();
            
            $varGenerarLista = 1;
          }else{
            $varGenerarLista = 2;
          }
          
        }else{
          $varGenerarLista = 2;
        }        

      }

      return $this->render('buscarencuestas',[
        'model' => $model,
        'varPreguntas' => $varPreguntas,
        'varNombreArbol' => $varNombreArbol,
        'varPaginadoTotal' => $varPaginadoTotal,
        'varGenerarLista' => $varGenerarLista,
        'varFechaInicio' => $varFechaInicio,
        'varFechaFinal' => $varFechaFinal,
        'varListadoColas' => $varListadoColas,
        'varArrayEncuestas' => $varArrayEncuestas,
      ]);
    }

    public function actionBuscarllamadas(){
      $model = new GenesysParametroasesor();
      $varGenerarLista = 0;
      $varNombreArbol = null;
      $varNombreAsesor = null;
      $varIdGenesys = null;
      $varArrayllamadas = array();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varIdAsesor = $model->nombre_asesor;
        $varArbol = $model->documento_asesor;
        $varFechaGeneral = explode(" ", $model->fechacreacion);
        $varFechaInicio = $varFechaGeneral[0].'T00:00:00';
        $varFechaFinal = $varFechaGeneral[2].'T23:59:59';

        $varFechasAsesorAuto = $varFechaInicio.".000Z/".$varFechaFinal.".000Z";

        $varVerificarAsesor = (new \yii\db\Query())
                        ->select([
                          'tbl_evaluados.name', 'tbl_genesys_parametroasesor.id_genesys'
                        ])
                        ->from(['tbl_genesys_parametroasesor'])
                        ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                            'tbl_genesys_parametroasesor.documento_asesor = tbl_evaluados.identificacion')
                        ->where(['=','tbl_genesys_parametroasesor.anulado',0])
                        ->andwhere(['=','tbl_evaluados.id',$varIdAsesor])
                        ->all();


        $varVerificarServicio = (new \yii\db\Query())
                        ->select(['tbl_genesys_formularios.id_cola_genesys'])
                        ->from(['tbl_genesys_formularios'])
                        ->where(['=','tbl_genesys_formularios.anulado',0])
                        ->andwhere(['=','tbl_genesys_formularios.arbol_id',$varArbol])
                        ->groupby(['tbl_genesys_formularios.id_cola_genesys'])
                        ->scalar();

        if (count($varVerificarAsesor) != 0 && count($varVerificarServicio) != 0) {

          foreach ($varVerificarAsesor as $value) {
            $varNombreAsesor = $value['name'];
            $varIdGenesys = $value['id_genesys'];
          }

          
          $varListadoColas = $varVerificarServicio;

          ob_start();
          $curlAuto = curl_init();

          curl_setopt_array($curlAuto, array(
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
              'Authorization: Basic YWI0ODEyMGYtMWMyYi00NDAxLTkzMzktYjFhM2JlMmYxY2UyOkNtX2loUDF5VE9oWTI3Sjl4ZmhReHJua2F0djQtUnB6bHpQLW1DdVQ5eEk=',
              'Content-Type: application/x-www-form-urlencoded'
            ),
          ));

          $responseAuto = curl_exec($curlAuto);

          curl_close($curlAuto);                        
          ob_clean();

          $varProcesosTokenUnoAuto = explode(",", $responseAuto);                  
          $varProcesosTokenDosAuto = explode(":",$varProcesosTokenUnoAuto[0]);
          $varrespuestaAuto = str_replace('"', '', $varProcesosTokenDosAuto[1]);

          if ($varrespuestaAuto != "") {
            ob_start();
            $curlAsesorAuto = curl_init();

            curl_setopt_array($curlAsesorAuto, array(
              CURLOPT_SSL_VERIFYPEER=> false,
              CURLOPT_SSL_VERIFYHOST => false,
              CURLOPT_URL => 'https://api.mypurecloud.com/api/v2/analytics/conversations/details/query',
              CURLOPT_RETURNTRANSFER => true,
              CURLOPT_ENCODING => '',
              CURLOPT_MAXREDIRS => 10,
              CURLOPT_TIMEOUT => 0,
              CURLOPT_FOLLOWLOCATION => true,
              CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
              CURLOPT_CUSTOMREQUEST => 'POST',
              CURLOPT_POSTFIELDS =>'{
                "interval": "'.$varFechasAsesorAuto.'",
                "order": "asc",
                "orderBy": "conversationStart",
                "paging": {
                "pageSize": "100",
                "pageNumber": "1"
              },
              "segmentFilters": [
                {
                  "type": "and",
                  "predicates": [
                    {
                      "type": "dimension",
                      "dimension": "userId",
                      "operator": "matches",
                      "value": "'.$varIdGenesys.'"
                    },
                    {
                      "type": "dimension",
                      "dimension": "queueId",
                      "operator": "matches",
                      "value": "'.$varListadoColas.'"
                    }
                  ]
                }
              ]
              }',
              CURLOPT_HTTPHEADER => array(
                'Authorization: Bearer '.$varrespuestaAuto.'',
                'Content-Type: application/json'
              ),
            ));

            $responseAsesorAuto = curl_exec($curlAsesorAuto);
            curl_close($curlAsesorAuto);
            ob_clean();

            $varListadoLlamadas_one = json_decode(iconv( "Windows-1252", "UTF-8//IGNORE", $responseAsesorAuto ),true);

            foreach ($varListadoLlamadas_one['conversations'] as $value) {
                    array_push($varArrayllamadas, array("varConnid"=>$value['conversationId'],"varOrigen"=>$value['originatingDirection']));
            }

            $varGenerarLista = 1;
            
          }else{
            $varGenerarLista = 2;
          }

        }else{
          $varGenerarLista = 2;
        }

      }
      
      return $this->render('buscarllamadas',[
        'model' => $model,
        'varGenerarLista' => $varGenerarLista,
        'varNombreArbol' => $varNombreArbol,
        'varNombreAsesor' => $varNombreAsesor,
        'varArrayllamadas' => $varArrayllamadas,
        'varIdGenesys' => $varIdGenesys,
      ]);
    }


}

?>


