<?php

namespace app\controllers;

ini_set('upload_max_filesize', '50M');

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
use GuzzleHttp;
use app\models\HojavidaEventos;
use app\models\HVCiudad;
use app\models\HvPais;


  class HojavidaController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','resumen','eventos','paisciudad'],
              'rules' => [
                [
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function() {
                              return Yii::$app->user->identity->isAdminSistema();
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
   
    public function actionIndex(){
      
      return $this->render('index');
    }

    public function actionResumen(){
      $id = Yii::$app->user->identity->id;


      return $this->render('resumen');
    }

    public function actionEventos(){
      $model = new HojavidaEventos();

      $form = Yii::$app->request->post();
      if($model->load($form)){

        $txtFecha = explode(" ", $model->fechacreacion);
        $varFechaInicio = $txtFecha[0];
        $varFechaFin = $txtFecha[2];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_eventos',[
                    'nombre_evento' => $model->nombre_evento,
                    'tipo_evento' => $model->tipo_evento,
                    'hv_idciudad' => $model->hv_idciudad,  
                    'fecha_evento_inicio' => $varFechaInicio,
                    'fecha_evento_fin' => $varFechaFin,
                    'asistencia' => $model->asistencia,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('eventos',['model'=>$model]);
      }

      $dataProvider = Yii::$app->db->createCommand("
        SELECT e.nombre_evento, e.tipo_evento, e.fecha_evento_inicio, e.fecha_evento_fin, c.ciudad, e.asistencia 
          FROM tbl_hojavida_eventos e
              INNER JOIN tbl_hv_ciudad c ON c.hv_idciudad = e.hv_idciudad ")->queryAll();


      return $this->render('eventos',[
        'model' => $model,
        'dataProvider' => $dataProvider,
      ]);
    }

    public function actionPaisciudad(){
      $modelpais = new HvPais();
      $modelciudad = new HvCiudad();

      return $this->render('paisciudad',[
        'modelpais' => $modelpais,
        'modelciudad' => $modelciudad,
      ]);
    }


  }

?>


