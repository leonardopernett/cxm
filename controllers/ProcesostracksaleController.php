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
use app\models\TracksaleParametrizarformulario;
use GuzzleHttp;
use Exception;

  class ProcesostracksaleController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index'],
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
      $model = new TracksaleParametrizarformulario();
      $varListaDatos = null;
      $varNombreArbol = null;

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varArbol = $model->arbol_id;
        $varFechaGeneral = explode(" ", $model->fechacreacion);
        $varFechaInicio = $varFechaGeneral[0].'T00:00:00';
        $varFechaFinal = $varFechaGeneral[2].'T23:59:59';

        $varNombreArbol = (new \yii\db\Query())
                        ->select(['tbl_arbols.name'])
                        ->from(['tbl_arbols'])
                        ->where(['=','tbl_arbols.id',$varArbol])
                        ->scalar(); 

        $varCountServicioSale = (new \yii\db\Query())
                        ->select(['tbl_tracksale_parametrizarformulario.trackservicio'])
                        ->from(['tbl_tracksale_parametrizarformulario'])
                        ->where(['=','tbl_tracksale_parametrizarformulario.anulado',0])
                        ->andwhere(['=','tbl_tracksale_parametrizarformulario.arbol_id',$varArbol])
                        ->count(); 

        
                
        if ($varCountServicioSale != '0') {
          $varServicioSale = (new \yii\db\Query())
                        ->select(['tbl_tracksale_parametrizarformulario.trackservicio'])
                        ->from(['tbl_tracksale_parametrizarformulario'])
                        ->where(['=','tbl_tracksale_parametrizarformulario.anulado',0])
                        ->andwhere(['=','tbl_tracksale_parametrizarformulario.arbol_id',$varArbol])
                        ->scalar(); 
          
          ob_start();
          
          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_SSL_VERIFYPEER=> false,
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_URL => 'https://api.tracksale.co/v2/report/answer?start='.$varFechaInicio.'&end='.$varFechaFinal.'&codes='.$varServicioSale.'&tags=true',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
              'Authorization: Bearer 3e4585e710cea793dbbbdfb6fbd21ea0'
            ),
          ));

          $response = curl_exec($curl);

          curl_close($curl);
          ob_clean();

          $varListaDatos = json_decode($response,true);

        }else{
          $varListaDatos = 'NA';
        
        }        

      }

      return $this->render('index',[
        'model' => $model,
        'varListaDatos' => $varListaDatos,
        'varNombreArbol' => $varNombreArbol,
      ]);
    }

    public function actionVerificartracksale(){
      $varListaServicios = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_tracksale_parametrizarformulario'])
                        ->where(['=','tbl_tracksale_parametrizarformulario.anulado',0])
                        ->all(); 

      return $this->renderAjax('verificartracksale',[
        'varListaServicios' => $varListaServicios,
      ]);
    }

}

?>


