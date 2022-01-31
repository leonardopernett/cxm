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
use app\models\SpeechCategorias;
use app\models\ControlVolumenxclientedq;

  class CategorizacionController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','registrarcategorias'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isControlProcesoCX();
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

    public function actions() {
      return [
          'error' => [
            'class' => 'yii\web\ErrorAction',
          ]
      ];
  }

  public function actionError() {

      //ERROR PRESENTADO
      $exception = Yii::$app->errorHandler->exception;

      if ($exception !== null) {
          //VARIABLES PARA LA VISTA ERROR
          $code = $exception->statusCode;
          $name = $exception->getName() . " (#$code)";
          $message = $exception->getMessage();
          //VALIDO QUE EL ERROR VENGA DEL CLIENTE DE IVR Y QUE SOLO APLIQUE
          // PARA LOS ERRORES 400
          $request = \Yii::$app->request->pathInfo;
          if ($request == "basesatisfaccion/clientebasesatisfaccion" && $code ==
                  400) {
              //GUARDO EN EL ERROR DE SATU
              $baseSat = new BasesatisfaccionController();
              $baseSat->setErrorSatu(\Yii::$app->request->url, $name . ": " . $message);
          }
          //RENDERIZO LA VISTA
          return $this->render('error', [
                      'name' => $name,
                      'message' => $message,
                      'exception' => $exception,
          ]);
      }
  }
    
    public function actionIndex(){      
      $model = new SpeechCategorias();
      $model2 = new ControlVolumenxclientedq();
      $varCateI = null;
      $varCateM = null;
      $varMesyear = null;

      $data = Yii::$app->request->post();
      if ($model2->load($data)) {
        $varMesyear = $model2->mesyear;

        $varCateI = Yii::$app->db->createCommand("select valorcategorizar from tbl_speech_categorizar where anulado = 0 and idcategorias = 1 and mesyear = '$varMesyear'")->queryScalar();

        $varCateM = Yii::$app->db->createCommand("select valorcategorizar from tbl_speech_categorizar where anulado = 0 and idcategorias = 2 and mesyear = '$varMesyear'")->queryScalar(); 
      }

      return $this->render('index',[
        'model' => $model,
        'model2' => $model2,
        'varCateI' => $varCateI,
        'varCateM' => $varCateM,
        'varMesyear' => $varMesyear,
        ]);
    }

    public function actionRegistrarcategorias(){
      $txtvarMes = Yii::$app->request->get("txtvarMes");
      $txtanulado = 0;
      $txtfechacreacion = date("Y-m-d");
      $txtRta = 0;
      $varMesyear = $txtvarMes;   
      $txtServicios = null; 
      $varFechaInicio = $varMesyear.' 05:00:00';
      $varFechaI = date("Y-m-t", strtotime($varFechaInicio));
      $varFechaF = date('Y-m-d',strtotime($varFechaI."+ 1 day"));
      $varFechaFin = $varFechaF.' 05:00:00';

      $varCountMesI = Yii::$app->db->createCommand("select count(mesyear) from tbl_speech_categorizar where mesyear = '$varMesyear' and idcategorias = 1")->queryScalar();   

      if ($varCountMesI == 0) {
        
        $txtServicios = Yii::$app->db->createCommand("select servicio from tbl_dashboardspeechcalls where anulado = 0 and fechallamada between '$varFechaInicio' and '$varFechaFin' group by servicio")->queryAll();

        foreach ($txtServicios as $value) {
          $varServicio = $value['servicio'];

          $txtVarCallid = Yii::$app->db->createCommand("select callid from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varServicio') and fechallamada between '$varFechaInicio' and '$varFechaFin' group by callid")->queryAll();

          $arralistCallid = array();
          foreach ($txtVarCallid as $value) {
            array_push($arralistCallid, $value['callid']);
          }
          $arraycallids = implode(", ", $arralistCallid);

          $txtVarIndicadores = Yii::$app->db->createCommand("select distinct idcategoria from tbl_speech_categorias where anulado = 0 and idcategorias = 1 and programacategoria in ('$varServicio') and idcategoria is not null")->queryAll();

          $varcountindicador = 0;
          $arraylistindicador = array();
          foreach ($txtVarIndicadores as $value) {
            $vararrayid = $value['idcategoria'];

            $varconteoindicador = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varServicio') and fechallamada between '$varFechaInicio' and '$varFechaFin' and idcategoria = $vararrayid and callid in ($arraycallids)")->queryScalar();

              if ($varconteoindicador > 0) {
                $varcountindicador = 1;
              }else{
                $varcountindicador = 0;
              }

              array_push($arraylistindicador, $varcountindicador);
          }

          $varArraysumaInidicador = array_sum($arraylistindicador);
        }

         $txtRtaProcentajeindicador = (round(($varArraysumaInidicador / count($txtVarIndicadores)) * 100, 1));

         Yii::$app->db->createCommand()->insert('tbl_speech_categorizar',[
                                  'valorcategorizar' => $txtRtaProcentajeindicador,
                                  'idcategorias' => 1,
                                  'mesyear' => $varMesyear,
                                  'fechacreacion' => $txtfechacreacion,
                                  'anulado' => $txtanulado,
                                  'usua_id' => Yii::$app->user->identity->id,
                              ])->execute(); 

        $txtRta = 1;
      }    



      $varCountMesM = Yii::$app->db->createCommand("select count(mesyear) from tbl_speech_categorizar where mesyear = '$varMesyear' and idcategorias = 2")->queryScalar();  

      if ($varCountMesM == 0) {
        
        $txtServicios = Yii::$app->db->createCommand("select servicio from tbl_dashboardspeechcalls where anulado = 0 and fechallamada between '$varFechaInicio' and '$varFechaFin' group by servicio")->queryAll();

        foreach ($txtServicios as $key => $value) {
          $varServicio = $value['servicio'];

          $txtVarCallid = Yii::$app->db->createCommand("select callid from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varServicio') and fechallamada between '$varFechaInicio' and '$varFechaFin' group by callid")->queryAll();

          $arralistCallid = array();
          foreach ($txtVarCallid as $key => $value) {
            array_push($arralistCallid, $value['callid']);
          }
          $arraycallids = implode(", ", $arralistCallid);

          $txtVarMotivos = Yii::$app->db->createCommand("select distinct idcategoria from tbl_speech_categorias where anulado = 0 and idcategorias = 3 and programacategoria in ('$varServicio') and idcategoria is not null")->queryAll();

          $varcountmotivo = 0;
          $arraylistmotivoc = array();
          foreach ($txtVarMotivos as $key => $value) {
            $vararrayidm = $value['idcategoria'];

            $varconteomotivo = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varServicio') and fechallamada between '$varFechaInicio' and '$varFechaFin' and idcategoria = $vararrayidm and callid in ($arraycallids)")->queryScalar();

            if ($varconteomotivo > 0) {
              $varcountmotivo = 1;
            }else{
              $varcountmotivo = 0;
            }

            array_push($arraylistmotivoc, $varcountmotivo);
          }

          $varArraysumaMotivos = array_sum($arraylistmotivoc);
        }

        $txtRtaProcentajeMotivos = (round(($varArraysumaMotivos / count($txtVarMotivos)) * 100, 1));

        Yii::$app->db->createCommand()->insert('tbl_speech_categorizar',[
                                  'valorcategorizar' => $txtRtaProcentajeMotivos,
                                  'idcategorias' => 2,
                                  'mesyear' => $varMesyear,
                                  'fechacreacion' => $txtfechacreacion,
                                  'anulado' => $txtanulado,
                                  'usua_id' => Yii::$app->user->identity->id,
                              ])->execute(); 

        $txtRta = 1;
      }


      die(json_encode($txtRta));
    }


  }

?>
