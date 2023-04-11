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

class ApicontrolsipController extends \yii\web\Controller {

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
                  'actions' => ['index','apicontrolsipren'],
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function() {
                              return Yii::$app->user->identity->isAdminSistema();
                          },
                ],
                [
                  'actions' => ['apicontrolsipren'],
                  'allow' => true,
  
                ],
              ],
  
          ],
        ];
    }

    public function init(){
        $this->enableCsrfValidation = false;
    }

    public function actionApicontrolsipren(){
        
            $txtanulado = 0;
            $txtfechacreacion = date("Y-m-d");
            $varDiaActual = date('j');
            $varMesActual = date('m');
            $varYearActual = date('Y');
            $vartxtsip = ".sip";            
            $varHora = date("h") - 1;
            $varfechahorainicio = date("Y-m-d").' '.$varHora.":00:00";
            $varfechahorafin = date("Y-m-d").' '.$varHora.":59:59";
            $varlistaagente = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_base_satisfaccion_copia'])
                            ->where(['like','tbl_base_satisfaccion_copia.agente',$vartxtsip])
                            ->andwhere(['between','tbl_base_satisfaccion_copia.fecha_satu', $varfechahorainicio, $varfechahorafin])                           
                            ->All();  

            foreach ($varlistaagente as $key => $value) {
                $varredagente = $value['agente'];
                $varidsatisfaccion = $value['id'];
                $paramsBuscaAsesor = [':RedAsesor'=>$varredagente];
                $varDocumentoAsesor = Yii::$app->dbjarvis->createCommand('
                        SELECT du.documento FROM  dp_usuarios_actualizacion du 
                        WHERE 
                        du.usuario = :RedAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();
              
                $paramsBuscaAsesordoc = [':DocumentoAsesor'=>$varDocumentoAsesor];
                $varusuario = Yii::$app->dbjarvis->createCommand('
                        SELECT dur.usuario_red FROM  dp_usuarios_red dur 
                        WHERE 
                        dur.documento = :DocumentoAsesor ')->bindValues($paramsBuscaAsesordoc)->queryScalar();
                 

                Yii::$app->db->createCommand()->update('tbl_base_satisfaccion_copia',[
                            'agente' => $varusuario,
                        ],'id ='.$varidsatisfaccion.'')->execute();
               
                }                             
            

            die(json_encode(array("status"=>"1","data"=>"Proceso .sip exitoso")));
    }    

}
?>