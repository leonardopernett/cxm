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


  class ApiinteraccionesController extends \yii\web\Controller {

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
                'actions' => ['apicallids','apicategorizaciones'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apicallids','apicategorizaciones'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApicallids(){
      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);

      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);


      $varBolsitaCX_String = 'VOC_CRUZ_VERDE';
      $varFechaInicioLlamadaEspecial_BD = '2023-07-01';
      $varHoraInicio = ' 00:00:00';
      $varFechaFinLlamadaEspecial_BD = '2023-07-30';
      $varHoraFinal = ' 23:59:59';
      $varExtensionComdata = 'CVD';


      $varListaInteracciones_Dash = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varBolsitaCX_String])
                                ->andwhere(['between','fechallamada',$varFechaInicioLlamadaEspecial_BD.$varHoraInicio,$varFechaFinLlamadaEspecial_BD.$varHoraFinal])
                                ->andwhere(['=','extension',$varExtensionComdata])
                                ->groupby(['callid'])
                                ->all();

      foreach ($varListaInteracciones_Dash as $key => $value) {
          Yii::$app->db->createCommand()->insert('tbl_dashboardspeechcalls',[
                'callId' => $value['callId'],
                'idcategoria' => 1114,
                'nombreCategoria' => 'CATEGORÍAS GENERALES',
                'extension' => $value['extension'],
                'login_id' => $value['login_id'],
                'fechallamada' => $value['fechallamada'],
                'callduracion' => $value['callduracion'],
                'servicio' => $value['servicio'],
                'fechareal' => $value['fechareal'],
                'idredbox' => $value['idredbox'],
                'idgrabadora' => $value['idgrabadora'],
                'connid' => $value['connid'],
                'extensiones' => 'NA',
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
          ])->execute();
      }

      die(json_encode("Proceso Realizado"));

    }


    public function actionApicategorizaciones(){
      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);

      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);


      $varBolsitaCX_StringC = 'VOC_CRUZ_VERDE';
      $varFechaInicioLlamadaEspecial_BDC = '2023-07-01 00:00:00';
      $varFechaFinLlamadaEspecial_BDC = '2023-07-30 23:59:59';
      $varExtensionComdataC = 'CVD';

      $varIdClienteLlamadaEspecial_BD = (new \yii\db\Query())
                          ->select([
                            'tbl_speech_parametrizar.id_dp_clientes'
                          ])
                          ->from(['tbl_speech_parametrizar'])
                          ->where(['=','tbl_speech_parametrizar.anulado',0])
                          ->andwhere(['=','tbl_speech_parametrizar.rn',$varExtensionComdataC])
                          ->andwhere(['=','tbl_speech_parametrizar.usabilidad',1])
                          ->groupby(['tbl_speech_parametrizar.id_dp_clientes'])
                          ->scalar();

      $varListaCategorizacionc = Yii::$app->db->createCommand("
            SELECT * FROM 
              (
                SELECT llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria AS llamacategoria, cate.idcategoria AS catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) AS encuentra, llama.nombreCategoria 
                FROM tbl_dashboardspeechcalls llama 
                  LEFT JOIN 
                    (
                      SELECT idcategoria, tipoindicador, programacategoria, cod_pcrc 
                        FROM tbl_speech_categorias 
                          WHERE anulado = 0 AND idcategorias = 2 
                            AND programacategoria IN ('$varBolsitaCX_StringC') 
                        ORDER BY cod_pcrc, tipoindicador
                    ) cate ON llama.servicio = cate.programacategoria 
                WHERE llama.servicio IN ('$varBolsitaCX_StringC') 
                  AND llama.extension = '$varExtensionComdataC' 
                    AND llama.fechallamada BETWEEN '$varFechaInicioLlamadaEspecial_BDC' AND '$varFechaFinLlamadaEspecial_BDC' 
                GROUP BY llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  
                  ORDER BY encuentra DESC
              ) datos 
            WHERE llamacategoria = catecategoria")->queryAll();


      if (count($varListaCategorizacionc) != 0) {
          foreach ($varListaCategorizacionc as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_speech_general',[
                                                'programacliente' => $value['servicio'],
                                                'fechainicio' => date('Y-m-01'),
                                                'fechafin' => NULL,
                                                'callid' => $value['callid'],
                                                'fechallamada' => $value['fechallamada'],
                                                'extension' => $value['extension'],
                                                'idindicador' => $value['llamacategoria'],
                                                'idvariable' => $value['catecategoria'],
                                                'cantproceso' => $value['encuentra'],
                                                'fechacreacion' => date('Y-m-d'),
                                                'anulado' => 0,
                                                'usua_id' => Yii::$app->user->identity->id,
                                                'arbol_id' => $varIdClienteLlamadaEspecial_BD,
            ])->execute();
          }
        }

    }

  }

?>
