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
use app\models\Permisosfeedback;


  class PermisosfeedbackController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','viewexcuse'],
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
            'delete' => ['post'],
          ],
        ],
      ];
    }
    

    public function actionIndex(){ 
      $model = new Permisosfeedback();
      $txtConteo = Yii::$app->db->createCommand("SELECT COUNT(p.idusuarios) FROM tbl_permisosfeedback p WHERE p.anulado = 0")->queryScalar();

      $txtListMeses = Yii::$app->db->createCommand("SELECT YEAR(p.fechacreacion) AS Annio, ELT(MONTH(p.fechacreacion), 'Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre') AS Mes, COUNT(p.idusuarios) AS Conteo FROM tbl_permisosfeedback p WHERE p.anulado = 0   GROUP BY Mes    ORDER BY p.fechacreacion")->queryAll();
     

      return $this->render('index',[
        'txtConteo' => $txtConteo,
        'txtListMeses' => $txtListMeses,
        'model' => $model,
      ]);
    }

    public function actionViewexcuse(){
      $model = new Permisosfeedback();

      return $this->renderAjax('viewexcuse',[
        'model' => $model,
      ]);
    }

    public function actionGenerarregistro(){
      $varusuario = Yii::$app->request->get('txtusuarios');
      $txtvalida = null;
      $txtidusuario = Yii::$app->db->createCommand("SELECT DISTINCT usua_id FROM tbl_usuarios u WHERE u.usua_usuario = '$varusuario'")->queryScalar();
      $txtrta = 0;

      if ($txtidusuario != "") {
        $txtvalida = Yii::$app->db->createCommand("SELECT COUNT(p.idusuarios) FROM tbl_permisosfeedback p WHERE p.anulado = 0 AND p.idusuarios = $txtidusuario")->queryScalar();

        if ($txtvalida != 0) {
          $txtrta = 1;
        }else{
          Yii::$app->db->createCommand()->insert('tbl_permisosfeedback',[
                                           'idusuarios' => $txtidusuario,
                                           'usua_id' => Yii::$app->user->identity->id,
                                           'anulado' => 0,
                                           'fechacreacion' => date("Y-m-d"),                                           
                                       ])->execute();
        }        

      }else{
        $txtrta = 2;
      }

      die(json_encode($txtrta));
    }

    public function actionValidarregistro(){
      $varusuario = Yii::$app->request->get('txtusuarios');
      $txtvalida = null;
      $txtidusuario = Yii::$app->db->createCommand("SELECT DISTINCT usua_id FROM tbl_usuarios u WHERE u.usua_usuario = '$varusuario'")->queryScalar();

      if ($txtidusuario != "") {
        $txtvalida = Yii::$app->db->createCommand("SELECT COUNT(p.idusuarios) FROM tbl_permisosfeedback p WHERE p.anulado = 0 AND p.idusuarios = $txtidusuario")->queryScalar();
      }else{
        #code...
      }

      die(json_encode($txtvalida));
    }

  }

?>
