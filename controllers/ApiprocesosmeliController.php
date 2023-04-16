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


  class ApiprocesosmeliController extends \yii\web\Controller {

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
                'actions' => ['apivaloraciones'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apivaloraciones'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApivaloraciones(){
        $datapost = file_get_contents('php://input');
        $data_post = json_decode($datapost,true);

        ini_set("max_execution_time", "900");
        ini_set("memory_limit", "1024M");
        ini_set( 'post_max_size', '1024M' );

        ignore_user_abort(true);
        set_time_limit(900);

        $varListDataValoracion = Yii::$app->get('dbmeli')->createCommand('
            SELECT
                m.submission_id,
                m.cx_queue_name AS formulario,
                m.user_ldap AS valorado,
                m.user_team_leader_ldap AS lider,
                m.analysis_owner_ldap AS valorador,
                m.analysis_reason AS dimensiones,
                m.pc_comment_analysis AS comentarios,
                m.oe_extra_mile AS scoregeneral,
                m.action_datetime AS fechacreacion
 
            FROM meli_178619_NRT_KTA_OE_ACTION_POINTS_REASONS_V3 m
            WHERE 
                m.pc_name = "client_problem_rep"
                    AND m.oe_extra_mile IS NOT NULL 
            ORDER BY m.action_datetime
        ')->queryAll();

        foreach ($varListDataValoracion as $key => $value) {
          
            $varExisteConexion = (new \yii\db\Query())
                                ->select([
                                'tbl_conexionvaloracion_datosorigen.identificador_origen'
                                ])
                                ->from(['tbl_conexionvaloracion_datosorigen'])
                                ->where(['=','tbl_conexionvaloracion_datosorigen.anulado',0])
                                ->andwhere(['=','tbl_conexionvaloracion_datosorigen.identificador_origen',$value['submission_id']])
                                ->count();

            if ($varExisteConexion == 0) {
                Yii::$app->db->createCommand()->insert('tbl_conexionvaloracion_datosorigen',[
                    'identificador_origen' => $value['submission_id'],
                    'formulario_origen' => $value['formulario'],
                    'valorado_origen' => $value['valorado'],
                    'lider_origen' => $value['lider'],
                    'valorador_origen' => $value['valorador'],
                    'dimensiones_origen' => $value['dimensiones'],
                    'comentarios_origen' => $value['comentarios'],
                    'score_origen' => $value['scoregeneral'],
                    'fechacreacion_origen' => $value['fechacreacion'],
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => 1,
                ])->execute();
            } 
  
        }



        die(json_encode("Aqui vamos"));
    }

  }

?>
