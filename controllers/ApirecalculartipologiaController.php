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


  class ApirecalculartipologiaController extends \yii\web\Controller {

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
                'actions' => ['generarrecalculartipologia'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['generarrecalculartipologia'],
                'allow' => true,

              ],
            ],

        ],
        
      ];
    }
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionGenerarrecalculartipologia(){

      //TRAIGO LAS ENCUESTAS SIN TIPOLOGIA
      $encuestas = BaseSatisfaccion::find()->select('id')->where("`tipologia` IS NULL")->all();

      if (empty($encuestas)) {
        echo "No hay procesos de actualizacion de tipologias ya que no se encontraron nuevas encuestas con tipologia null";
        exit;
      }
      
      //MENSAJE DE CONTROL                               
      $errores = "";
      foreach ($encuestas as $encuesta) {
        
        $model = BaseSatisfaccion::findOne($encuesta->id);
        $model->tipologia = 'NEUTRO';

        if (!empty($model->pcrc) && !empty($model->cliente)) {
          $sql = '
            SELECT ca.nombre, dp.categoria, p.pre_indicador, 
              dp.configuracion, dp.addNA, cg.name, cg.prioridad, cg.id
            FROM tbl_detalleparametrizacion dp
              JOIN tbl_categoriagestion cg 
                ON dp.id_categoriagestion = cg.id
              JOIN tbl_parametrizacion_encuesta pe 
                ON pe.id = cg.id_parametrizacion
              LEFT JOIN tbl_preguntas p 
                ON p.id_parametrizacion = pe.id 
                  AND p.categoria = dp.categoria
              JOIN tbl_categorias ca ON ca.id = dp.categoria 
            WHERE 
              pe.cliente = ' . $model->cliente
                . ' AND pe.programa = ' . $model->pcrc;
          
          $config = \Yii::$app->db->createCommand($sql)->queryAll();

          $prioridades = ArrayHelper::map($config, 'prioridad', 'name');
          $arrayCumpleRegla = [];

          if (count($config) > 0) {
            $conditon = '';
            $comando = '';
            $i = 1;
            $errorConfig = false;
            
            //Validamos si hay una mala configuracion-------------------                    
            foreach ($config as $value) {
              if (is_null($value['pre_indicador']) || empty($value['pre_indicador'])) {
                $errorConfig = true;
                \Yii::error('Categoria (' . $value['nombre']
                . '), Cliente(' . $model->cliente0->name
                . ') Programa(' . $model->pcrc0->name
                . ')  mal configuada, Por favor revise la '
                . 'configuracion', 'basesatisfaccion');
              }
            }

            if (!$errorConfig) {
              
              foreach ($config as $value) {
              
                if (!empty($value['configuracion'])) {
                  
                  $preExplode = explode('-', $value['configuracion']);
                  $explode = explode('||', $preExplode[0]);
                  
                  if (isset($explode[0]) && isset($explode[1]) && isset($explode[2])) {

                    if (str_replace(['(', ')'], '', $explode[0]) == BaseSatisfaccion::OP_AND) {
                    
                      if (isset($preExplode[1])) {
                      
                        $explodeAddNA = explode('||', $preExplode[1]);
                        $tmpConditon = ' && ($model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explode[1]
                          . ' "'
                          . $explode[2] . '" ';
                          
                        $tmpConditon .= ' || $model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explodeAddNA[1]
                          . ' "'
                          . $explodeAddNA[2] . '" )';
                      
                      } else {
                        
                        $tmpConditon = ' && (is_numeric($model->' . $value['pre_indicador'] . ') && $model->'
                          . $value['pre_indicador']
                          . ' '
                          . $explode[1]
                          . ' "'
                          . $explode[2] . '") ';
                      }
                      
                    } else {
                      
                      $tmpConditon = ' || $model->'
                        . $value['pre_indicador']
                        . ' '
                        . $explode[1]
                        . ' "'
                        . $explode[2] . '" ';
                    
                    }

                    if (!isset($config[$i]['id']) || $value['id'] != $config[$i]['id']) {
                    
                      $conditon = substr($conditon, 4);
                      
                      if (!$conditon) {
                        $tmpConditon = substr($tmpConditon, 4);
                      }
                      
                      $eval = '('
                        . $conditon
                        . $tmpConditon
                        . ')';
                        
                      $conditon = '';
                      
                      $comando .= '#####' . $eval;
                        eval("\$restCond = $eval;");
                      
                      if ($restCond) {
                        
                        $arrayCumpleRegla[] = 'true';
                        $model->tipologia = $value['name'];
                      
                      } else {
                        
                        $arrayCumpleRegla[] = 'false';
                      
                      }
                                            
                    } else {
                      
                      $conditon .= $tmpConditon;
                    
                    }
                    
                  }
                
                }
                
                $i++;
              
              }

              $contarValores = array_count_values($arrayCumpleRegla);

              //Contamos el numero de true en $arrayCumpleRegla---------------
              if (isset($contarValores['true']) && $contarValores['true'] > 1) {
                //sacamos el que tenga prioridad mas alta ------------------                    
                $model->tipologia = $prioridades[min(array_keys($prioridades))];
              }
            }
          }
        }

        
        //GUARDAMOS LOS DATOS-----------------------------------------------
        if (!$model->save()) {
          $errores .= "<br />ID Encuesta: " . $encuesta->id . "<br />";
        }

      }

      if (!empty($errores)) {
      
        echo '<div style="background-color: #f2dede; '
        . 'border-color: #ebccd1; color: #a94442;'
        . ' padding: 10px;">'
        . 'ENCUESTAS QUE NO PUDIERON SER RECALCULADAS: '
        . $errores
        . '</div>';
        
      } else {
      
        echo '<div style="background-color: #dff0d8; '
        . 'border-color: #d6e9c6; color: #3c763d;'
        . ' padding: 10px;">'
        . 'PROCESO TERMINADO CON &Eacute;XITO'
        . '</div>';
      
      }

    }

  }

?>
