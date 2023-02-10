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


  class ApidistribuciondosController extends \yii\web\Controller {

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
                'actions' => ['index','apirocesadistribucionauto'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apirocesadistribucionauto'],
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

    public function actionApirocesadistribucionauto(){

        $arraydatas = "Proceso realizado";

        ini_set("max_execution_time", "900");
        ini_set("memory_limit", "1024M");
        ini_set( 'post_max_size', '1024M' );

        ignore_user_abort(true);
        set_time_limit(900);

      
        $varListSecciones = Yii::$app->dbjarvis->createCommand("
        SELECT dp.documento AS CedulaAsesor, dp.documento_jefe AS CedulaLider, pc.id_dp_clientes AS id_dp_clientes, 
        dp.cod_pcrc AS CodPcrc, dp.fecha_actual AS FechaJarvis FROM dp_pcrc pc
          INNER JOIN dp_distribucion_personal dp ON 
            pc.cod_pcrc = dp.cod_pcrc
          INNER JOIN dp_cargos dc ON 
            dp.id_dp_cargos = dc.id_dp_cargos
          INNER JOIN dp_estados de ON 
            dp.id_dp_estados = de.id_dp_estados
          WHERE 
            dc.id_dp_posicion IN (39,18,40)
              AND dc.id_dp_funciones IN (322,783,190,909,915,323,324)
                AND dp.fecha_actual >= DATE_FORMAT(NOW() ,'%Y-%m-01')
                  AND de.tipo IN ('ACTIVO','GESTION')
                    AND pc.id_dp_clientes != 1
          GROUP BY dp.documento
        ")->queryAll();

        Yii::$app->db->createCommand()->truncateTable('tbl_distribucion_asesores')->execute();

        foreach ($varListSecciones as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_distribucion_asesores',[
                      'cedulaasesor' => $value['CedulaAsesor'],
                      'cedulalider' => $value['CedulaLider'],
                      'fechaactualjarvis' => $value['FechaJarvis'],  
                      'id_dp_clientes' => $value['id_dp_clientes'],
                      'cod_pcrc' => $value['CodPcrc'],
                      'fechamodificacxm' => date('Y-m-d'),
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,                                       
                  ])->execute();
        }
      
        die(json_encode(array("status"=>"1","data"=>$arraydatas)));
      
    }     

    
       
    

  }

?>
