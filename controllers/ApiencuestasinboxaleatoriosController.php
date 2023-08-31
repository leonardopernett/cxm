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
use yii\db\Expression;
use PHPExcel;
use PHPExcel_IOFactory;
use GuzzleHttp;


  class ApiencuestasinboxaleatoriosController extends \yii\web\Controller {

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
                'actions' => ['apialeatorios'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['apialeatorios'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionApialeatoriosencuesta(){
      // Inicio del procesos de los aleatorios
      $varAnnio = date('Y');
      $varMes = date('m');
      $varDia = date('d',strtotime("- 1 days"));

      $varListaProgramas = (new \yii\db\Query())
                            ->select([
                              'tbl_base_aleatorio.arbol_id AS varPcrc',
                              'tbl_base_aleatorio.form_id AS varFormulario'
                            ])
                            ->from(['tbl_base_aleatorio'])
                            ->where(['=','tbl_base_aleatorio.anulado',0])
                            ->all();

      foreach ($varListaProgramas as $value) {
        $varProgramaPcrc = $value['varPcrc'];
        $varFormularios = $value['varFormulario'];


        $varListaTramos = (new \yii\db\Query())
                            ->select([
                              '*'
                            ])
                            ->from(['tbl_reglanegocio'])
                            ->where(['=','tbl_reglanegocio.pcrc',$varProgramaPcrc])
                            ->andwhere(['=','tbl_reglanegocio.id_formulario',$varFormularios])
                            ->all();

        $varConteo = 0;
        foreach ($varListaTramos as $value) {
          $varRn = $value['rn'];
          $varCodIndustria = $value['cod_industria'];
          $varCodInstitucion = $value['cod_institucion'];
          $varPrograma_pcrc = $value['pcrc'];
          $varClientes = $value['cliente'];

          // Procesos de los tramos
          $varTramo1 = $value['tramo1'];
          $varTramo2 = $value['tramo2'];
          $varTramo3 = $value['tramo3'];
          $varTramo4 = $value['tramo4'];
          $varTramo5 = $value['tramo5'];
          $varTramo6 = $value['tramo6'];
          $varTramo7 = $value['tramo7'];
          $varTramo8 = $value['tramo8'];
          $varTramo9 = $value['tramo9'];
          $varTramo10 = $value['tramo10'];
          $varTramo11 = $value['tramo11'];
          $varTramo12 = $value['tramo12'];
          $varTramo13 = $value['tramo13'];
          $varTramo14 = $value['tramo14'];
          $varTramo15 = $value['tramo15'];
          $varTramo16 = $value['tramo16'];
          $varTramo17 = $value['tramo17'];
          $varTramo18 = $value['tramo18'];
          $varTramo19 = $value['tramo19'];
          $varTramo20 = $value['tramo20'];
          $varTramo21 = $value['tramo21'];
          $varTramo22 = $value['tramo22'];
          $varTramo23 = $value['tramo23'];
          $varTramo24 = $value['tramo24'];

          if ($varTramo1 != 0) {
            $varListadoTramo1 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','000000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','005959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo1)
                            ->all();

            foreach ($varListadoTramo1 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo2 != 0) {
            $varListadoTramo2 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','010000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','015959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo2)
                            ->all();

            foreach ($varListadoTramo2 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo3 != 0) {
            $varListadoTramo3 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','020000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','025959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo3)
                            ->all();

            foreach ($varListadoTramo3 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo4 != 0) {
            $varListadoTramo4 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','030000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','035959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo4)
                            ->all();

            foreach ($varListadoTramo4 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo5 != 0) {
            $varListadoTramo5 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','040000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','045959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo5)
                            ->all();

            foreach ($varListadoTramo5 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo6 != 0) {
            $varListadoTramo6 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','050000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','055959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo6)
                            ->all();

            foreach ($varListadoTramo6 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo7 != 0) {
            $varListadoTramo7 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','060000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','065959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo7)
                            ->all();

            foreach ($varListadoTramo7 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo8 != 0) {
            $varListadoTramo8 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','070000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','075959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo8)
                            ->all();

            foreach ($varListadoTramo8 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo9 != 0) {
            $varListadoTramo9 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','080000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','085959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo9)
                            ->all();

            foreach ($varListadoTramo9 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo10 != 0) {
            $varListadoTramo10 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','090000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','095959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo10)
                            ->all();

            foreach ($varListadoTramo10 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo11 != 0) {
            $varListadoTramo11 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','100000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','105959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo11)
                            ->all();

            foreach ($varListadoTramo11 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo12 != 0) {
            $varListadoTramo12 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','110000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','115959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo12)
                            ->all();

            foreach ($varListadoTramo12 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo13 != 0) {
            $varListadoTramo13 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','120000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','125959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo13)
                            ->all();

            foreach ($varListadoTramo13 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo14 != 0) {
            $varListadoTramo14 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','130000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','135959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo14)
                            ->all();

            foreach ($varListadoTramo14 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo15 != 0) {
            $varListadoTramo15 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','140000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','145959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo15)
                            ->all();

            foreach ($varListadoTramo15 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo16 != 0) {
            $varListadoTramo16 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','150000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','155959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo16)
                            ->all();

            foreach ($varListadoTramo16 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo17 != 0) {
            $varListadoTramo17 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','160000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','165959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo17)
                            ->all();

            foreach ($varListadoTramo17 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo18 != 0) {
            $varListadoTramo18 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','170000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','175959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo18)
                            ->all();

            foreach ($varListadoTramo18 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo19 != 0) {
            $varListadoTramo19 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','180000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','185959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo19)
                            ->all();

            foreach ($varListadoTramo19 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo20 != 0) {
            $varListadoTramo20 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','190000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','195959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo20)
                            ->all();

            foreach ($varListadoTramo20 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo21 != 0) {
            $varListadoTramo21 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','200000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','205959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo21)
                            ->all();

            foreach ($varListadoTramo21 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo22 != 0) {
            $varListadoTramo22 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','210000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','215959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo22)
                            ->all();

            foreach ($varListadoTramo22 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo23 != 0) {
            $varListadoTramo23 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','220000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','225959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo23)
                            ->all();

            foreach ($varListadoTramo23 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          if ($varTramo24 != 0) {
            $varListadoTramo24 = (new \yii\db\Query())
                            ->select([
                              'tbl_base_satisfaccion.id'
                            ])
                            ->from(['tbl_base_satisfaccion'])
                            ->where(['=','tbl_base_satisfaccion.rn',$varRn])
                            ->andwhere(['=','tbl_base_satisfaccion.industria',$varCodIndustria])
                            ->andwhere(['=','tbl_base_satisfaccion.institucion',$varCodInstitucion])
                            ->andwhere(['=','tbl_base_satisfaccion.pcrc',$varPrograma_pcrc])
                            ->andwhere(['=','tbl_base_satisfaccion.cliente',$varClientes])
                            ->andwhere(['=','tbl_base_satisfaccion.tipo_inbox','NORMAL'])
                            ->andwhere(['!=','tbl_base_satisfaccion.agente','Sin información'])
                            ->andwhere(['=','tbl_base_satisfaccion.ano',$varAnnio])
                            ->andwhere(['=','tbl_base_satisfaccion.mes',$varMes])
                            ->andwhere(['=','tbl_base_satisfaccion.dia',$varDia])
                            ->andwhere(['>=','tbl_base_satisfaccion.hora','230000'])
                            ->andwhere(['<=','tbl_base_satisfaccion.hora','235959'])
                            ->orderBy(new Expression('rand()'))
                            ->limit($varTramo24)
                            ->all();

            foreach ($varListadoTramo24 as $value) {
              Yii::$app->db->createCommand()->update('tbl_base_satisfaccion',[
                    'tipo_inbox' => 'ALEATORIO',                                                
              ],'id ='.$value['id'].'')->execute();
            }
          }

          
           
        }
        
      }

      die();
    }

  }

?>
