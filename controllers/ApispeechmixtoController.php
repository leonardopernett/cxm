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


  class ApispeechmixtoController extends \yii\web\Controller {

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
                'actions' => ['apimixto'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apimixto'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApimixto(){
      $varAnnio = date('Y');
      $varMesActual = date('m');
      $varMesPasado = date('m',strtotime(date('Y-m-d')."- 1 month"));

      $varFechaMesPasado = $varAnnio.'-'.$varMesPasado.'-01';
      $varFechaMesActual = date('Y-m-d');

      $varListaMixta = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_speech_mixta'])
                        ->where(['=','tbl_speech_mixta.anulado',0])
                        ->andwhere(['between','tbl_speech_mixta.fechacreacion',$varFechaMesPasado,$varFechaMesActual])
                        ->all(); 

      foreach ($varListaMixta as $value) {
        $varIdMixta = $value['idmixta'];
        $varIdEjecucionFormularios = $value['formulario_id'];
        $varIdCallid = $value['callid'];
        
        $varVerificarMixta = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_ejecucionformularios'])
                        ->where(['=','tbl_ejecucionformularios.id',$varIdEjecucionFormularios])
                        ->count(); 

        if ($varVerificarMixta == 0) {
          Yii::$app->db->createCommand()->update('tbl_speech_mixta',[
                      'formulario_id' => 0,
                      'callid' => 0,
                      'fechareal' => $varIdCallid,
                      'anulado' => 1,                       
          ],'idmixta ='.$varIdMixta.'')->execute();
        }

      }

      die();
    }

  }

?>
