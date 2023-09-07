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
use app\models\UploadForm2;


  class ApiprocesosplanosController extends \yii\web\Controller {

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
                'actions' => ['procesosplanos'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['procesosplanos'],
                'allow' => true,

              ],
            ],

        ],

           


        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionProcesosplanos(){
        $modelArchivo = new UploadForm2();

        $varFechainicio_P = date('Y-m-d H:00:00', strtotime('-1 hour'));
        $varFechafin_P = date('Y-m-d H:59:59', strtotime('-1 hour'));

        $fileName = Yii::$app->basePath . DIRECTORY_SEPARATOR . "web" .
                DIRECTORY_SEPARATOR . "valoracionescxm_comdata" . DIRECTORY_SEPARATOR
                . Yii::t('app', 'valoraciones_cxm') . '_' . date('Ymd') . "_" .
                Yii::$app->user->identity->id . ".xlsx";
      
        $objPHPexcel = new \PHPExcel();
        
        $sheet3 = $objPHPexcel->getActiveSheet();
        $sheet3->setTitle("NombreHojaCalculo");
        $sheet3->setCellValue('A1', 'Programa');
        $sheet3->setCellValue('B1', 'Cod. Materia');
        $sheet3->setCellValue('C1', 'Materia');

        $data =  (new \yii\db\Query())
                            ->select([
                                '*'
                            ])
                            ->from(['tbl_alertas_tipoencuestas'])
                            ->all(); 

        foreach ($data as $key => $value) {
            $sheet3->setCellValue('A' . ($key + 2), $value['id_tipoencuestas']);
            $sheet3->setCellValue('B' . ($key + 2), $value['tipoencuestas']);
            $sheet3->setCellValue('C' . ($key + 2), $value['peso']);
        }
        
        $objWriter = new \PHPExcel_Writer_Excel2007($objPHPexcel);
        $objWriter->save($fileName);


      die(json_encode("Aqui vamos"));

    }       
    

  }

?>
