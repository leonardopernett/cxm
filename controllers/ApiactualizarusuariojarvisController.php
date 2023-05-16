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
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\helpers\Url;
use PHPExcel;
use PHPExcel_IOFactory;
use GuzzleHttp;


  class ApiactualizarusuariojarvisController extends \yii\web\Controller {

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
                'actions' => ['apiusuarios'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apiusuarios'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApiusuarios(){   

      $varParamsBuscar = [':varBloqueado'=>0];     

      $varUsuariosJarvis = Yii::$app->dbjarvis->createCommand('
          SELECT * FROM dp_usuarios_red
            WHERE 
              dp_usuarios_red.bloqueado = :varBloqueado')->bindValues($varParamsBuscar)->queryall();

      Yii::$app->db->createCommand()->truncateTable('tbl_jarvis_usuariosred')->execute();

      foreach ($varUsuariosJarvis as $value) {
        Yii::$app->db->createCommand()->insert('tbl_jarvis_usuariosred',[
                    'id_dp_usuarios_red' => $value['id_dp_usuarios_red'],
                    'documento' => $value['documento'],
                    'usuario_red' => $value['usuario_red'],  
                    'nombre' => $value['nombre'],
                    'email' => $value['email'],
                    'fecha_creacion_usuario' => $value['fecha_creacion_usuario'],
                    'dominio' => $value['dominio'],
                    'bloqueado' => $value['bloqueado'],
                    'fecha_ingreso_registro' => $value['fecha_ingreso_registro'],
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => 1,
        ])->execute();
      }

      die();

    }


  }

?>
