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
use GuzzleHttp;


  class ApiwiasaeinteraccionesController extends \yii\web\Controller {

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
                'actions' => ['index','apidatageneral'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apidatageneral'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionIndex(){

      return $this->render('index');
    }

    public function actionApidatageneral(){

      ob_start();

      $curl = curl_init();

      curl_setopt_array($curl, array(
        CURLOPT_SSL_VERIFYPEER=> false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_URL => 'https://wia-web-api-gw-5j8fyx1b.uc.gateway.dev/conectionCXM?key=AIzaSyClC9KoixrqyM3CcO24a29OI3u4e3Vzv4c',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => 'proyectID=colmena-arl-11000&datasetId=ColmenaARL&tableId=colmena_arl_mxn&limit=20000&offset=0',
        CURLOPT_HTTPHEADER => array(
          'Content-Type: application/x-www-form-urlencoded'
        ),
      ));

      $response = curl_exec($curl);

      curl_close($curl);
            
      ob_clean();

      var_dump($response);

      die(json_encode("Aqui vamos"));

    }

  }

?>
