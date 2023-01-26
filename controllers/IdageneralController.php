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
use app\models\IdaGeneral;


  class IdageneralController extends \yii\web\Controller {

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
                'actions' => ['index','enviararchivo','procesarentto','procesarvalores','procesarasesor'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['procesarvalores','procesarasesor'],
                'allow' => true,

              ],
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
  
    public function init(){
      $this->enableCsrfValidation = false;
    }

    public function actionIndex(){ 
      $model = new IdaGeneral();
      $fechamodifica = date("Y-m-d H:i:s");

      $form = Yii::$app->request->post();
      if($model->load($form)){
        $vartipo = $model->tipoproceso;
        $varservicio = $model->servicio;

        $varcategeneral = Yii::$app->db->createCommand("SELECT DISTINCT idllamada FROM tbl_speech_servicios s WHERE s.id_dp_clientes = :varservicio")
        ->bindValue(':varservicio',$varservicio)
        ->queryScalar();

        $varusuarioslist = Yii::$app->db->createCommand("SELECT DISTINCT ce.usuario_de_red_o_extension, ce.fecha_de_ojt, ce.fecha_de_conexion from tbl_calidad_entto ce")->queryAll();

        if ($vartipo == 1) {

          $varservicios = Yii::$app->db->createCommand("SELECT DISTINCT sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sp.tipoparametro = 1 AND sc.responsable = 1 AND sc.idcategorias = 2 AND sp.id_dp_clientes = :varservicio")
          ->bindValue(':varservicio',$varservicio)
          ->queryScalar();
          
          $varlistextensions = Yii::$app->db->createCommand("SELECT if (sp.rn != '', sp.rn, if(sp.ext != '', sp.ext, if(sp.usuared != '', sp.usuared, if(sp.comentarios != '', sp.comentarios, 'N/A')))) AS Extension FROM tbl_speech_parametrizar sp WHERE sp.anulado = 0 AND sp.tipoparametro = 1 AND sp.id_dp_clientes = :varservicio")
          ->bindValue(':varservicio',$varservicio)
          ->queryAll();
          $vararrayext = array();
          foreach ($varlistextensions as $key => $value) {
            array_push($vararrayext, $value['Extension']);
          }
          $varextlist = implode("', '", $vararrayext);
          

          $varlistcategorias = Yii::$app->db->createCommand("SELECT DISTINCT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sp.tipoparametro = 1 AND sc.idcategorias = 2  AND sc.responsable = 1 AND sp.id_dp_clientes = :varservicio")
          ->bindValue(':varservicio',$varservicio)
          ->queryAll();


          $countpositivas = 0;
          $countnegativas = 0;
          $countpositicasc = 0;
          $countnegativasc = 0;

          foreach ($varusuarioslist as $key => $value) {
            $varasesor = $value['usuario_de_red_o_extension'];
            $varfechaconexion = $value['fecha_de_conexion'];
            $varfechainicio = null; 
            $varfechafin = null;           

            if ($varfechaconexion != "") {
              $varfechainicio = $varfechaconexion;
              $varfechafin = date("Y-m-d",strtotime($varfechainicio."+ 30 days")); 
            }else{
              
              $varfechainicio = date("Y-m-01");
              $varfechafin = date("Y-m-d",strtotime($varfechainicio."+ 30 days")); 
            }          
            
            $varlistcallid = Yii::$app->db->createCommand("SELECT DISTINCT d.callId FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.servicio IN (:varservicios) AND d.fechallamada BETWEEN ':varfechainicio 05:00:00' AND ':varfechafin 05:00:00' AND d.extension IN (:varextlist) AND d.login_id IN (:varasesor)")
            ->bindValue(':varservicios',$varservicios)
            ->bindValue(':varfechainicio',$varfechainicio)
            ->bindValue(':varfechafin',$varfechafin)
            ->bindValue(':varextlist',$varextlist)
            ->bindValue(':varasesor',$varasesor)
            ->queryAll();
            $varArraySumas = array();

            if (count($varlistcallid) > 0) {

              $varvalidaC = Yii::$app->db->createCommand("SELECT COUNT(ig.idcenttog) FROM tbl_ida_general ig WHERE ig.anulado = 0 AND ig.usuariored = :varasesor AND ig.tipoproceso = 'Calidad de Entrenamiento'")
              ->bindValue(':varasesor',$varasesor)
              ->queryScalar();

              if ($varvalidaC == 0) {
                Yii::$app->db->createCommand()->insert('tbl_ida_general',[
                        'usuariored' => $varasesor,
                        'tipoproceso' => "Calidad de Entrenamiento",
                        'fechainicio' => $varfechainicio,
                        'fechafin' => $varfechafin, 
                        'servicio' => $varservicio,
                        'vinsatu' => null,
                        'vsolucion' => null,
                        'vvalores' => null,
                        'vfacilidad' => null,
                        'vhabilidad' => null,
                        'totalentto' => null,
                        'totalojt' => null,
                        'cantidadvaloram' => null,
                        'totalvaloram' => null,
                        'cantidadvaloraa' => null,
                        'totalvaloraa' => null,
                        'fechamodificacion' => null,
                        'anulado' => 0,
                        'usua_id' => Yii::$app->user->identity->id,
                        'fechacreacion' => date("Y-m-d"),
                      ])->execute();
              }              

              $countgeneralcallid = 0;
              foreach ($varlistcallid as $key => $value) {
                $varCallid = $value['callId'];

                $varlistvariables = Yii::$app->db->createCommand("SELECT DISTINCT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sc.responsable = 1 AND sp.tipoparametro = 1 AND sc.idcategorias = 2  AND sp.id_dp_clientes = :varservicio")
                ->bindValue(':varservicio',$varservicio)
                ->queryAll();
                
                $countpositivas = 0;
                $countnegativas = 0;
                $countpositicasc = 0;
                $countnegativasc = 0;

                foreach ($varlistvariables as $key => $value) {
                  $varorientaciones = $value['orientacionsmart'];
                  $varidcategoriav = $value['idcategoria'];
                  $varcategoriap = $value['programacategoria'];

                  if ($varorientaciones == '2') {
                    $countnegativas = $countnegativas + 1;
                    $contarnegativas = Yii::$app->db->createCommand("SELECT COUNT(s.idvariable) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in (:varcategoriap) AND s.callid IN (:varCallid) AND s.idindicador IN (:varidcategoriav) AND s.idvariable IN (:varidcategoriav) and s.extension in (:varextlist) AND s.fechallamada BETWEEN ':varfechainicio 05:00:00' AND ':varfechafin 05:00:00'")
                    ->bindValue(':varcategoriap',$varcategoriap)
                    ->bindValue(':varCallid',$varCallid)
                    ->bindValue(':varidcategoriav',$varidcategoriav)
                    ->bindValue(':varextlist',$varextlist)
                    ->bindValue(':varfechainicio',$varfechainicio)
                    ->bindValue(':varfechafin',$varfechafin)
                    ->queryScalar();

                    if ($contarnegativas == '1') {
                      $countnegativasc = $countnegativasc + 1;
                    }
                  }else{
                    if ($varorientaciones == '1') {
                      $countpositivas = $countpositivas + 1;
                      $contarpositivas = Yii::$app->db->createCommand("SELECT COUNT(s.idvariable) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in (:varcategoriap) AND s.callid IN (:varCallid) AND s.idindicador IN (:varidcategoriav) AND s.idvariable IN (:varidcategoriav) and s.extension in (:varextlist) AND s.fechallamada BETWEEN ':varfechainicio 05:00:00' AND ':varfechafin 05:00:00'")
                      ->bindValue(':varcategoriap',$varcategoriap)
                      ->bindValue(':varCallid',$varCallid)
                      ->bindValue(':varidcategoriav',$varidcategoriav)
                      ->bindValue(':varextlist',$varextlist)
                      ->bindValue(':varfechainicio',$varfechainicio)
                      ->bindValue(':varfechafin',$varfechafin)
                      ->queryScalar();

                      if ($contarpositivas == '1') {
                        $countpositicasc = $countpositicasc + 1;
                      }
                    }
                  }
                }


                $totalvariables = count($varlistvariables);
                if ($totalvariables != 0 ) {
                  $resultadosIDA = round((($countpositicasc + ($countnegativas - $countnegativasc)) / count($varlistvariables)),2);
                }else{
                  $resultadosIDA = 0;
                }
                array_push($varArraySumas, $resultadosIDA);
              }

              $countgeneralcallid = round((array_sum($varArraySumas)/count($varlistcallid)) * 100,2);

              $varvaloram = Yii::$app->db->createCommand("SELECT round(AVG(ef.score) * 100,2) AS Score FROM tbl_ejecucionformularios ef INNER JOIN tbl_evaluados e ON ef.evaluado_id = e.id  WHERE ef.dimension_id IN (4) AND ef.arbol_id IN (3061)  AND ef.created BETWEEN ':varfechainicio 00:00:00' AND ':varfechafin 23:59:59'    AND e.dsusuario_red IN (:varasesor) GROUP BY ef.id")
              ->bindValue(':varfechainicio',$varfechainicio)
              ->bindValue(':varfechafin',$varfechafin)
              ->bindValue(':varasesor',$varasesor)
              ->queryScalar();

              if ($varvaloram == null) {
                $varvaloram = 0;
              }

              $varvaloracantidad = Yii::$app->db->createCommand("SELECT count(ef.id) AS Score FROM tbl_ejecucionformularios ef INNER JOIN tbl_evaluados e ON ef.evaluado_id = e.id  WHERE ef.dimension_id IN (4) AND ef.arbol_id IN (3061)  AND ef.created BETWEEN ':varfechainicio 00:00:00' AND ':varfechafin 23:59:59'    AND e.dsusuario_red IN (:varasesor) GROUP BY ef.id")
              ->bindValue(':varfechainicio',$varfechainicio)
              ->bindValue(':varfechafin',$varfechafin)
              ->bindValue(':varasesor',$varasesor)
              ->queryScalar();

              if ($varvaloracantidad == null) {
                $varvaloracantidad = 0;
              }

              $varcantidada = Yii::$app->db->createCommand("SELECT count(d.callId) FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.servicio IN ('$varservicios') AND d.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00' AND d.extension IN ('$varextlist') AND d.login_id IN ('$varasesor') AND d.idcategoria IN ($varcategeneral)")
              ->bindValue(':varservicios',$varservicios)
              ->bindValue(':varfechainicio',$varfechainicio)
              ->bindValue(':varfechafin',$varfechafin)
              ->bindValue(':varextlist',$varextlist)
              ->bindValue(':varasesor',$varasesor)
              ->bindValue(':varcategeneral',$varcategeneral)
              ->queryScalar();

              if ($varcantidada == null) {
                $varcantidada = 0;
              }


              Yii::$app->db->createCommand("UPDATE tbl_ida_general ig SET ig.totalentto = $countgeneralcallid, ig.fechamodificacion = '$fechamodifica', ig.cantidadvaloram = '$varvaloracantidad', ig.totalvaloram = '$varvaloram', ig.cantidadvaloraa = '$varcantidada' WHERE ig.anulado = 0 AND ig.usuariored = '$varasesor' AND ig.servicio = $varservicio AND ig.tipoproceso = 'Calidad de Entrenamiento'")->execute();        
              
            }

          }

          return $this->redirect('index');
        }else{

          $varservicios = Yii::$app->db->createCommand("SELECT DISTINCT sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sp.tipoparametro = 2 AND sc.responsable = 1 AND sc.idcategorias = 2 AND sp.id_dp_clientes = :varservicio")
          ->bindValue(':varservicio',$varservicio)
          ->queryScalar();
          
          $varlistextensions = Yii::$app->db->createCommand("SELECT if (sp.rn != '', sp.rn, if(sp.ext != '', sp.ext, if(sp.usuared != '', sp.usuared, if(sp.comentarios != '', sp.comentarios, 'N/A')))) AS Extension FROM tbl_speech_parametrizar sp WHERE sp.anulado = 0 AND sp.tipoparametro = 2 AND sp.id_dp_clientes = :varservicio")
          ->bindValue(':varservicio',$varservicio)
          ->queryAll();
          $vararrayext = array();
          foreach ($varlistextensions as $key => $value) {
            array_push($vararrayext, $value['Extension']);
          }
          $varextlist = implode("', '", $vararrayext);
          

          $varlistcategorias = Yii::$app->db->createCommand("SELECT DISTINCT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sp.tipoparametro = 2 AND sc.idcategorias = 2 AND sp.id_dp_clientes = :varservicio")
          ->bindValue(':varservicio',$varservicio)
          ->queryAll();

          $countpositivas = 0;
          $countnegativas = 0;
          $countpositicasc = 0;
          $countnegativasc = 0;

          foreach ($varusuarioslist as $key => $value) {
            $varasesor = $value['usuario_de_red_o_extension'];
            $varfechaojt = $value['fecha_de_ojt'];
            $varfechaconexion = $value['fecha_de_conexion'];
            $varfechainicio = null; 
            $varfechafin = null;           

            if ($varfechaojt != "") {
              $varfechainicio = $varfechaojt;
              if ($varfechaconexion != "") {
                $varfechafin = date("Y-m-d",strtotime($varfechainicio."-1 days"));
              }else{
                $varfechafin = date("Y-m-d",strtotime($varfechainicio."10 days"));
              }
               
            }else{
              $varfechainicio = date("Y-m-01");
              if ($varfechaconexion != "") {
                $varfechafin = date("Y-m-d",strtotime($varfechainicio."-1 days"));
              }else{
                $varfechafin = date("Y-m-d");
              } 
            }          
            
            $varlistcallid = Yii::$app->db->createCommand("SELECT DISTINCT d.callId FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.servicio IN (':varservicios') AND d.fechallamada BETWEEN ':varfechainicio 05:00:00' AND ':varfechafin 05:00:00' AND d.extension IN (':varextlist') AND d.login_id IN (':varasesor')")
            ->bindValue(':varservicios',$varservicios)
            ->bindValue(':varfechainicio',$varfechainicio)
            ->bindValue(':varfechafin',$varfechafin)
            ->bindValue(':varextlist',$varextlist)
            ->bindValue(':varasesor',$varasesor)
            ->queryAll();
            $varArraySumas = array();

            if (count($varlistcallid) > 0) {

              $varvalidaC = Yii::$app->db->createCommand("SELECT COUNT(ig.idcenttog) FROM tbl_ida_general ig WHERE ig.anulado = 0 AND ig.usuariored = :varasesor AND ig.tipoproceso = 'OJT'")
              ->bindValue(':varasesor',$varasesor)
              ->queryScalar();

              if ($varvalidaC == 0) {
                Yii::$app->db->createCommand()->insert('tbl_ida_general',[
                        'usuariored' => $varasesor,
                        'tipoproceso' => "OJT",
                        'fechainicio' => $varfechainicio,
                        'fechafin' => $varfechafin,
                        'servicio' => $varservicio,
                        'vinsatu' => null,
                        'vsolucion' => null,
                        'vvalores' => null,
                        'vfacilidad' => null,
                        'vhabilidad' => null,
                        'totalentto' => null,
                        'totalojt' => null,
                        'cantidadvaloram' => null,
                        'totalvaloram' => null,
                        'cantidadvaloraa' => null,
                        'totalvaloraa' => null,
                        'fechamodificacion' => null,
                        'anulado' => 0,
                        'usua_id' => Yii::$app->user->identity->id,
                        'fechacreacion' => date("Y-m-d"),
                      ])->execute();
              }

              $countgeneralcallid = 0;
              foreach ($varlistcallid as $key => $value) {
                $varCallid = $value['callId'];

                $varlistvariables = Yii::$app->db->createCommand("SELECT DISTINCT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.responsable = 1 AND sc.anulado = 0 AND sp.tipoparametro = 1 AND sc.idcategorias = 2  AND sp.id_dp_clientes = :varservicio")
                ->bindValue(':varservicio',$varservicio)
                ->queryAll();

                $countpositivas = 0;
                $countnegativas = 0;
                $countpositicasc = 0;
                $countnegativasc = 0;

                $contarpositivas = 0;
                $contarnegativas = 0;
                foreach ($varlistvariables as $key => $value) {
                  $varorientaciones = $value['orientacionsmart'];
                  $varidcategoriav = $value['idcategoria'];
                  $varcategoriap = $value['programacategoria'];

                  if ($varorientaciones == '1') {
                    $countnegativas = $countnegativas + 1;
                    $contarnegativas = Yii::$app->db->createCommand("SELECT COUNT(s.idvariable) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in (:varcategoriap) AND s.callid IN (:varCallid) AND s.idvariable IN (:varidcategoriav) and s.extension in (:varextlist) AND s.fechallamada BETWEEN ':varfechainicio 05:00:00' AND ':varfechafin 05:00:00'")
                    ->bindValue(':varcategoriap',$varcategoriap)
                    ->bindValue(':varCallid',$varCallid)
                    ->bindValue(':varidcategoriav',$varidcategoriav)
                    ->bindValue(':varextlist',$varextlist)
                    ->bindValue(':varfechainicio',$varfechainicio)
                    ->bindValue(':varfechafin',$varfechafin)
                    ->queryScalar();

                    if ($contarnegativas == '1') {
                      $countnegativasc = $countnegativasc + 1;
                    }
                  }else{
                    if ($varorientaciones == '2') {
                      $countpositivas = $countpositivas + 1;
                      $contarpositivas = Yii::$app->db->createCommand("SELECT COUNT(s.idvariable) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in (:varcategoriap) AND s.callid IN (:varCallid)  AND s.idvariable IN (:varidcategoriav) and s.extension in (:varextlist) AND s.fechallamada BETWEEN ':varfechainicio 05:00:00' AND ':varfechafin 05:00:00'")
                      ->bindValue(':varcategoriap',$varcategoriap)
                      ->bindValue(':varCallid',$varCallid)
                      ->bindValue(':varidcategoriav',$varidcategoriav)
                      ->bindValue(':varextlist',$varextlist)
                      ->bindValue(':varfechainicio',$varfechainicio)
                      ->bindValue(':varfechafin',$varfechafin)
                      ->queryScalar();

                      if ($contarpositivas == '1') {
                        $countpositicasc = $countpositicasc + 1;
                      }
                    }
                  }
                }

                $totalvariables = count($varlistvariables);
                if ($totalvariables != 0 ) {
                  $resultadosIDA = round((($countpositicasc + ($countnegativas - $countnegativasc)) / count($varlistvariables)),2);
                }else{
                  $resultadosIDA = 0;
                }

                array_push($varArraySumas, $resultadosIDA);
              }

              $countgeneralcallid = round((array_sum($varArraySumas)/count($varlistcallid)) * 100,2);

              $varvaloram = Yii::$app->db->createCommand("SELECT round(AVG(ef.score) * 100,2) AS Score FROM tbl_ejecucionformularios ef INNER JOIN tbl_evaluados e ON ef.evaluado_id = e.id  WHERE ef.dimension_id IN (4) AND ef.arbol_id IN (3061)  AND ef.created BETWEEN ':varfechainicio 00:00:00' AND ':varfechafin 23:59:59'    AND e.dsusuario_red IN (:varasesor) GROUP BY ef.id")
              ->bindValue(':varfechainicio',$varfechainicio)
              ->bindValue(':varfechafin',$varfechafin)
              ->bindValue(':varasesor',$varasesor)
              ->queryScalar();

              if ($varvaloram == null) {
                $varvaloram = 0;
              }

              $varvaloracantidad = Yii::$app->db->createCommand("SELECT count(ef.id) AS Score FROM tbl_ejecucionformularios ef INNER JOIN tbl_evaluados e ON ef.evaluado_id = e.id  WHERE ef.dimension_id IN (4) AND ef.arbol_id IN (3061)  AND ef.created BETWEEN ':varfechainicio 00:00:00' AND ':varfechafin 23:59:59'    AND e.dsusuario_red IN (:varasesor) GROUP BY ef.id")
              ->bindValue(':varfechainicio',$varfechainicio)
              ->bindValue(':varfechafin',$varfechafin)
              ->bindValue(':varasesor',$varasesor)
              ->queryScalar();

              if ($varvaloracantidad == null) {
                $varvaloracantidad = 0;
              }

              $varcantidada = Yii::$app->db->createCommand("SELECT count(d.callId) FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.servicio IN (:varservicios) AND d.fechallamada BETWEEN ':varfechainicio 05:00:00' AND ':varfechafin 05:00:00' AND d.extension IN (:varextlist) AND d.login_id IN (:varasesor) AND d.idcategoria IN (:varcategeneral)")
              ->bindValue(':varservicios',$varservicios)
              ->bindValue(':varfechainicio',$varfechainicio)
              ->bindValue(':varfechafin',$varfechafin)
              ->bindValue(':varextlist',$varextlist)
              ->bindValue(':varasesor',$varasesor)
              ->bindValue(':varcategeneral',$varcategeneral)
              ->queryScalar();

              if ($varcantidada == null) {
                $varcantidada = 0;
              }


              Yii::$app->db->createCommand("UPDATE tbl_ida_general ig SET ig.totalojt = :countgeneralcallid, ig.fechamodificacion = :fechamodifica, ig.cantidadvaloram = :varvaloracantidad, ig.totalvaloram = :varvaloram, ig.cantidadvaloraa = :varcantidada WHERE ig.anulado = 0 AND ig.usuariored = :varasesor AND ig.servicio = :varservicio AND ig.tipoproceso = 'OJT'")
              ->bindValue(':countgeneralcallid',$countgeneralcallid)
              ->bindValue(':fechamodifica',$fechamodifica)
              ->bindValue(':varvaloracantidad',$varvaloracantidad)
              ->bindValue(':varvaloram',$varvaloram)
              ->bindValue(':varcantidada',$varcantidada)
              ->bindValue(':varasesor',$varasesor)
              ->bindValue(':varservicio',$varservicio)
              ->execute();

            }
          }
        }

        return $this->redirect('index');
      }else{
        #code
    }

      return $this->render('index',[
        'model' => $model,
      ]);
    }

    public function actionEnviararchivo(){
      $model = new IdaGeneral();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        $varcorreo = $model->tipoproceso;

        $Listaida = Yii::$app->db->createCommand("SELECT ss.nameArbol, ig.usuariored, ig.tipoproceso, ig.fechainicio, ig.fechafin, ig.totalentto, ig.totalojt FROM tbl_speech_servicios ss INNER JOIN  tbl_ida_general ig ON ss.id_dp_clientes = ig.servicio")->queryAll();

        $phpExc = new \PHPExcel();
          $phpExc->getProperties()
                  ->setCreator("Konecta")
                  ->setLastModifiedBy("Konecta")
                  ->setTitle("Archivos Procesamiento Calidad Entto y OJT")
                  ->setSubject("Archivo Procesamiento Calidad Entto y OJT")
                  ->setDescription("Este archivo contiene el proceso de los registros en la calidad de entrenamiento y ojt en VOC")
                  ->setKeywords("Archivo Procesamientos Calidad Entto y OJT");
          $phpExc->setActiveSheetIndex(0);

          $phpExc->getActiveSheet()->setShowGridlines(False);

          $styleArray = array(
                  'alignment' => array(
                      'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
                  ),
              );


          $styleColor = array(
                  'fill' => array(
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                      'color' => array('rgb' => '28559B'),
                  )
              );

          $styleArrayTitle = array(
                  'font' => array(
                    'bold' => false,
                    'color' => array('rgb' => 'FFFFFF')
                  )
              );

          $styleArraySubTitle = array(
                  'fill' => array(
                          'type' => \PHPExcel_Style_Fill::FILL_SOLID,
                          'color' => array('rgb' => '4298B5'),
                  )
              );


          // ARRAY STYLE FONT COLOR AND TEXT ALIGN CENTER
          $styleArrayBody = array(
                  'font' => array(
                      'bold' => false,
                      'color' => array('rgb' => '2F4F4F')
                  ),
                  'borders' => array(
                      'allborders' => array(
                          'style' => \PHPExcel_Style_Border::BORDER_THIN,
                          'color' => array('rgb' => 'DDDDDD')
                      )
                  )
              );


          $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

          $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - QA MANAGEMENT');
          $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A1:G1');

          $phpExc->getActiveSheet()->SetCellValue('A2','Servicio');
          $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('B2','Usuario de Red');
          $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('C2','Tipo Proceso');
          $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('D2','Fecha Inicio');
          $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('E2','Fecha Fin');
          $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('F2','Total Calidad Entto');
          $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('G2','Total OJT');
          $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArrayTitle);


          $numCell = 3;
          foreach ($Listaida as $key => $value) {
            $varnombreArbil = $value['nameArbol'];
            $varasesor = $value['usuariored'];
            $vartipoproceso = $value['tipoproceso'];
            $varfechainicio = $value['fechainicio'];
            $varfechafin = $value['fechafin'];
            $vartotalentto = $value['totalentto'];
            $vartotalojt = $value['totalojt'];


            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $varnombreArbil);
            $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $varasesor);
            $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $vartipoproceso);
            $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $varfechainicio);
            $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $varfechafin);
            if ($vartotalentto != "") {
              $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $vartotalentto);
            }else{
              $phpExc->getActiveSheet()->setCellValue('F'.$numCell, '--');
            }
            if ($vartotalojt != "") {
              $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $vartotalojt);
            }else{
              $phpExc->getActiveSheet()->setCellValue('G'.$numCell, '--');
            }


            $numCell = $numCell + 1;
          }


           $hoy = getdate();
          $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_ArchivoEntto&OJT";

          $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');

          $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
          $tmpFile.= ".xls";

          $objWriter->save($tmpFile);

          $message = "<html><body>";
          $message .= "<h3>Se ha realizado el envio correcto del archivo del procesamiento calidad de entrenamiento y ojt.</h3>";
          $message .= "</body></html>";

          Yii::$app->mailer->compose()
                          ->setTo($varcorreo)
                          ->setFrom(Yii::$app->params['email_satu_from'])
                          ->setSubject("Archivo Procesamiento Calidad Entto y OJT")
                          ->attach($tmpFile)
                          ->setHtmlBody($message)
                          ->send();

          return $this->redirect('index');
      }else{
        #code
      }

      return $this->renderAjax('enviararchivo',[
        'model' => $model,
      ]);
    }

    public function actionProcesarentto(){
      $model = new IdaGeneral();

      return $this->render('procesarentto',[
        'model' => $model,
      ]);
    }

    public function actionProcesarvalores(){


      $datapost = file_get_contents('php://input');
      $data_post = json_decode($datapost,true);
  
      if (
           !isset($data_post["pcrc"]) 
        || !isset($data_post["dimension"]) 
        || !isset($data_post["fechaInicio"]) 
        || !isset($data_post["fechaFin"]) 
        || !isset($data_post["documentos"])
        || !isset($data_post["usuarios"])
        || empty($data_post["pcrc"]) 
        || empty($data_post["dimension"]) 
        || empty($data_post["fechaInicio"]) 
        || empty($data_post["fechaFin"]) 
        || empty($data_post["documentos"])
        || empty($data_post["usuarios"])
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varpcrc = $data_post["pcrc"];   

      $paramspcrc = [':cod_pcrc' => $varpcrc];
      $varExistPcrc = Yii::$app->db->createCommand('
        SELECT COUNT(cod_pcrc)  FROM tbl_speech_categorias 
          WHERE cod_pcrc IN (:cod_pcrc)
            ')->bindValues($paramspcrc)->queryScalar();
      
      if ($varExistPcrc == 0) {
        die(json_encode(array("status"=>"0","data"=>"Pcrc ingresado no esta parametrizado en CXM")));
      }

      $varDimension = $data_post["dimension"];
      $varFechaInicio = $data_post["fechaInicio"];
      $varFechaFin = $data_post["fechaFin"];
      $varDocumentos = $data_post["documentos"];
      $varUsuarios = $data_post["usuarios"];

      
      $listdimensiones = array();
      $array_dimensiones = count($varDimension);
      for ($i = 0; $i < $array_dimensiones; ++$i){
          array_push($listdimensiones, $varDimension[$i]);          
      }

      $varparametros = implode(", ", $listdimensiones);
      $varStrDimension = null;
      if ($varDimension == "1") {
        $varStrDimension = "Calidad de Entrenamiento";
      }else{
        if ($varDimension == "2") {
          $varStrDimension = "OJT";
        }else{
          $varStrDimension = "";
        }
      }
      


      $varextensiones = Yii::$app->db->createCommand("SELECT concat(sp.rn,sp.ext,sp.usuared) AS extensiones FROM tbl_speech_parametrizar sp WHERE sp.cod_pcrc IN ('$varpcrc') and sp.tipoparametro in ($varparametros)")->queryAll();

      $listextensiones = array();

      foreach ($varextensiones as $key => $value) {
        array_push($listextensiones, $value['extensiones']);
      }

      $txtParametros = implode("', '", $listextensiones);

      $txtIdCatagoria = Yii::$app->db->createCommand("SELECT ss.idllamada FROM tbl_speech_servicios ss INNER JOIN tbl_speech_parametrizar sp ON ss.id_dp_clientes = sp.id_dp_clientes WHERE sp.cod_pcrc IN ('$varpcrc') GROUP BY ss.arbol_id")->queryScalar();

      $txtServicio = \app\models\SpeechCategorias::find()->distinct()
                        ->select(['tbl_speech_categorias.programacategoria'])
                        ->where(['tbl_speech_categorias.cod_pcrc' => $varpcrc])
                        ->andwhere("tbl_speech_categorias.anulado = 0")
                        ->scalar();

      $listdocumentos = array();
      $array_documentos = count($varDocumentos);

      for ($i = 0; $i < $array_documentos; ++$i){
          array_push($listdocumentos, $varDocumentos[$i]);
      }

      $varlogindocumento = implode("', '", $listdocumentos);



      $listusuarios = array();
      $array_usuarios = count($varUsuarios);
      for ($i = 0; $i < $array_usuarios; ++$i){
          array_push($listusuarios, $varUsuarios[$i]);
      }

      $varloginusuarios = implode("', '", $listusuarios);


      $varInicioF = $varFechaInicio.' 05:00:00';
      $varFinF = date('Y-m-d',strtotime($varFechaFin."+ 1 day")).' 05:00:00';
      $varverificarusuarios = Yii::$app->db->createCommand("SELECT COUNT(callId) FROM tbl_dashboardspeechcalls WHERE anulado = 0 AND servicio IN ('$txtServicio')  AND fechallamada BETWEEN '$varInicioF' AND '$varFinF'  AND extension IN ('$txtParametros') AND login_id IN ('$varloginusuarios')")->queryScalar();

      if ($varverificarusuarios != 0) {
        $varlogin_id = explode("', '", $varloginusuarios);
      }else{
        $varlogin_id = explode("', '", $varlogindocumento);
      }
      
      $arraydata = array();
      $arra_login_id = count($varlogin_id);

      for ($i = 0; $i < $arra_login_id; ++$i){
        $varusuariologin = $varlogin_id[$i];

        $varpromedio = Yii::$app->db->createCommand("SELECT COUNT(callId) FROM tbl_dashboardspeechcalls WHERE anulado = 0 AND servicio IN ('$txtServicio') AND extension IN ('$txtParametros') AND fechallamada BETWEEN '$varInicioF' AND '$varFinF' AND idcategoria IN ($txtIdCatagoria) AND login_id IN ('$varusuariologin')")->queryScalar();


        $varcountarCallid = Yii::$app->db->createCommand("SELECT DISTINCT callId FROM tbl_dashboardspeechcalls WHERE anulado = 0 AND servicio IN ('$txtServicio') AND extension IN ('$txtParametros') AND fechallamada BETWEEN '$varInicioF' AND '$varFinF' AND login_id IN ('$varusuariologin')")->queryAll();

        $varindicadorarray = array();
        $varconteocallid = 0;
        foreach ($varcountarCallid as $key => $value) {
          $varcallids = $value['callId'];
          $varconteocallid = $varconteocallid + 1;

          $varlistvariables = Yii::$app->db->createCommand("SELECT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc WHERE sc.anulado = 0 AND sc.cod_pcrc IN ('$varpcrc') AND sc.idcategorias in (2) AND sc.responsable IN (1)")->queryAll();

          $varlistanegativo = array();
          $varlistapositivo = array();
          $varconteonegativas = 0;
          $varconteopositivas = 0;
          $varconteogeneral = 0;
          foreach ($varlistvariables as $key => $value) {
            $varorientacionsmart = $value['orientacionsmart'];
            $varcategoriaidspeech = $value['idcategoria'];
            $varconteogeneral = $varconteogeneral + 1;

            if ($varorientacionsmart == "2") {
              array_push($varlistanegativo, $varcategoriaidspeech);
              $varconteonegativas = $varconteonegativas + 1;
            }else{
              if ($varorientacionsmart == "1") {
                array_push($varlistapositivo, $varcategoriaidspeech);
                $varconteopositivas = $varconteopositivas + 1;
              }
            }
          }
          $varvariablesnegativas = implode(", ", $varlistanegativo);
          $varvariablespositivas = implode(", ", $varlistapositivo);

          if ($varvariablesnegativas != null) {
            $varcontarvarnegativas = Yii::$app->db->createCommand("SELECT SUM(s.cantproceso) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in ('$txtServicio') AND extension IN ('$txtParametros') AND s.callid in($varcallids) AND s.idvariable in ($varvariablesnegativas) AND s.fechallamada BETWEEN '$varInicioF' AND '$varFinF'")->queryScalar();
          }else{
            $varcontarvarnegativas = 0;
          }
            
          if ($varvariablespositivas != null) {
            $varcontarvarpositivas = Yii::$app->db->createCommand("SELECT SUM(s.cantproceso) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in ('$txtServicio') AND extension IN ('$txtParametros') AND s.callid in($varcallids) AND s.idvariable in ($varvariablespositivas) AND s.fechallamada BETWEEN '$varInicioF' AND '$varFinF'")->queryScalar();
          }else{
            $varcontarvarpositivas = 0;
          }                

          $varResultado = (($varconteonegativas - $varcontarvarnegativas) + $varcontarvarpositivas) / $varconteogeneral;

          array_push($varindicadorarray, $varResultado);
        }

        if ($varconteocallid != 0) {
          $resultadosIDA = round((array_sum($varindicadorarray) / $varconteocallid) * 100,2);
        }else{
          $resultadosIDA = 0;
        }
        

        array_push($arraydata, array("usuarios"=>$varusuariologin,"cantidadllamadas"=>$varpromedio,"score"=>$resultadosIDA,"dimension"=>$varStrDimension));


      }

      die( json_encode( array("status"=>"1","data"=>$arraydata) ) );
      
    }

    public function actionProcesarasesor(){

      $datapostasesor = file_get_contents('php://input');
      $data_post_agent = json_decode($datapostasesor,true);

      if (
           !isset($data_post_agent["pcrc"]) 
        || !isset($data_post_agent["dimension"]) 
        || !isset($data_post_agent["fechaInicio"]) 
        || !isset($data_post_agent["fechaFin"]) 
        || !isset($data_post_agent["documentos"])
        || !isset($data_post_agent["usuarios"])
        || empty($data_post_agent["pcrc"]) 
        || empty($data_post_agent["dimension"]) 
        || empty($data_post_agent["fechaInicio"]) 
        || empty($data_post_agent["fechaFin"]) 
        || empty($data_post_agent["documentos"])
        || empty($data_post_agent["usuarios"])
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }

      $varCodPcrcagent = $data_post_agent["pcrc"];

      $varExistCodPcrcagent = (new \yii\db\Query())
                        ->select([
                            'cod_pcrc'
                        ])
                        ->from(['tbl_speech_categorias'])  
                        ->where(['=','cod_pcrc',$varCodPcrcagent])
                        ->andwhere(['=','anulado',0]) 
                        ->count();

      if ($varExistCodPcrcagent == 0) {
        die(json_encode(array("status"=>"0","data"=>"Pcrc ingresado no esta parametrizado en CXM")));
      }

      $varDimensionagent = $data_post_agent["dimension"];
      $varFechaInicioagent = $data_post_agent["fechaInicio"];
      $varFechaFinagent = $data_post_agent["fechaFin"];
      $varDocumentosagent = $data_post_agent["documentos"];
      $varUsuariosagent = $data_post_agent["usuarios"];

      $listdimensionesagent = array();
      $array_dimensionesagent = count($varDimensionagent);
      for ($i = 0; $i < $array_dimensionesagent; ++$i){
          array_push($listdimensionesagent, $varDimensionagent[$i]);          
      }

      $varparametrosagent = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $listdimensionesagent)));
      $varStrDimensionagent = null;
      if ($varDimensionagent == "1") {
        $varStrDimensionagent = "Calidad de Entrenamiento";
      }else{
        if ($varDimensionagent == "2") {
          $varStrDimensionagent = "OJT";
        }else{
          $varStrDimensionagent = "Calidad de Entrenamiento & Ojt";
        }
      }

      $varextensionesagent = (new \yii\db\Query())
                        ->select([
                            'concat(tbl_speech_parametrizar.rn,tbl_speech_parametrizar.ext,tbl_speech_parametrizar.usuared) AS extensiones'
                        ])
                        ->from(['tbl_speech_parametrizar'])  
                        ->where(['=','tbl_speech_parametrizar.cod_pcrc',$varCodPcrcagent])
                        ->andwhere(['in','tbl_speech_parametrizar.tipoparametro',$varparametrosagent]) 
                        ->all();

      $listextensionesagent = array();
      foreach ($varextensionesagent as $key => $value) {
        array_push($listextensionesagent, $value['extensiones']);
      }
      $txtParametrosagent = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $listextensionesagent)));

      $txtIdCatagoriaagent = (new \yii\db\Query())
                        ->select([
                            'tbl_speech_servicios.idllamada'
                        ])
                        ->from(['tbl_speech_servicios'])  
                        ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                              'tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes')
                        ->where(['=','tbl_speech_parametrizar.cod_pcrc',$varCodPcrcagent])
                        ->groupby(['tbl_speech_servicios.arbol_id'])
                        ->Scalar();

      $txtServicioagent = (new \yii\db\Query())
                        ->select([
                            'tbl_speech_categorias.programacategoria'
                        ])
                        ->from(['tbl_speech_categorias'])  
                        ->where(['=','tbl_speech_categorias.cod_pcrc',$varCodPcrcagent])
                        ->andwhere(['=','tbl_speech_categorias.anulado',0])
                        ->groupby(['tbl_speech_categorias.programacategoria']) 
                        ->Scalar();

      $listdocumentosagent = array();
      $array_documentosagent = count($varDocumentosagent);

      for ($i = 0; $i < $array_documentosagent; ++$i){
          array_push($listdocumentosagent, $varDocumentosagent[$i]);
      }
      $varlogindocumentoagent = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $listdocumentosagent)));


      $listusuariosagent = array();
      $array_usuariosagent = count($varUsuariosagent);
      for ($i = 0; $i < $array_usuariosagent; ++$i){
          array_push($listusuariosagent, $varUsuariosagent[$i]);
      }
      $varloginusuariosagent = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $listusuariosagent)));

      $varInicioFagent = $varFechaInicioagent.' 05:00:00';
      $varFinFagent = date('Y-m-d',strtotime($varFechaFinagent."+ 1 day")).' 05:00:00';

      $varverificarusuariosagent = (new \yii\db\Query())
                        ->select([
                            'callId'
                        ])
                        ->from(['tbl_dashboardspeechcalls'])  
                        ->where(['=','anulado',0])
                        ->andwhere(['=','servicio',$txtServicioagent])
                        ->andwhere(['between','fechallamada',$varInicioFagent,$varFinFagent])
                        ->andwhere(['in','extension',$txtParametrosagent])
                        ->andwhere(['in','login_id',$varloginusuariosagent])
                        ->count();

      if ($varverificarusuariosagent != 0) {
        $varlogin_idagent = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $varloginusuariosagent)));
      }else{
        $varlogin_idagent = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $varlogindocumentoagent)));
      }


      $arraydataagent = array();
      $arra_login_idagent = count($varlogin_idagent);

      for ($i=0; $i < $arra_login_idagent; $i++) { 
        $varusuariologinagent = $varlogin_idagent[$i];

        // Aqui busca el asesor en Jarvis
        $varParamsLogin = [':varLoginSpeech'=>$varusuariologinagent];
        $varNombreAsesor = Yii::$app->dbjarvis->createCommand('
          SELECT dp_usuarios_red.nombre FROM dp_usuarios_red
            WHERE 
              dp_usuarios_red.usuario_red = :varLoginSpeech ')->bindValues($varParamsLogin)->queryScalar();

        $varCedulaAsesor = Yii::$app->dbjarvis->createCommand('
          SELECT dp_usuarios_red.documento FROM dp_usuarios_red
            WHERE 
              dp_usuarios_red.usuario_red = :varLoginSpeech ')->bindValues($varParamsLogin)->queryScalar();

        $varpromedioagent = (new \yii\db\Query())
                        ->select([
                            'tbl_dashboardspeechcalls.callId'
                        ])
                        ->from(['tbl_dashboardspeechcalls'])  
                        ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                        ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$txtServicioagent])
                        ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varInicioFagent,$varFinFagent])
                        ->andwhere(['in','tbl_dashboardspeechcalls.extension',$txtParametrosagent])
                        ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$txtIdCatagoriaagent])
                        ->andwhere(['=','tbl_dashboardspeechcalls.login_id',$varusuariologinagent])
                        ->count();

        $varcountarCallidagent = (new \yii\db\Query())
                        ->select([
                            'tbl_dashboardspeechcalls.callId',
                            'tbl_dashboardspeechcalls.fechallamada'
                        ])
                        ->from(['tbl_dashboardspeechcalls'])  
                        ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                        ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$txtServicioagent])
                        ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varInicioFagent,$varFinFagent])
                        ->andwhere(['in','tbl_dashboardspeechcalls.extension',$txtParametrosagent])
                        ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$txtIdCatagoriaagent])
                        ->andwhere(['=','tbl_dashboardspeechcalls.login_id',$varusuariologinagent])
                        ->groupby(['tbl_dashboardspeechcalls.callId'])
                        ->All();

        $varconteocallidagent = 0;
        foreach ($varcountarCallidagent as $key => $value) {
          $varcallidsagent = $value['callId'];
          $varfechaagent = $value['fechallamada'];
          $varconteocallidagent = $varconteocallidagent + 1;

          $varlistvariablesagent = (new \yii\db\Query())
                        ->select([
                            'idcategoria','orientacionsmart','programacategoria'
                        ])
                        ->from(['tbl_speech_categorias'])  
                        ->where(['=','anulado',0])
                        ->andwhere(['=','cod_pcrc',$varCodPcrcagent])
                        ->andwhere(['=','idcategorias',2])
                        ->andwhere(['=','responsable',1])
                        ->All();

          $varlistanegativoagent = array();
          $varlistapositivoagent = array();
          $varconteonegativasagent = 0;
          $varconteopositivasagent = 0;
          $varconteogeneralagent = 0;
          foreach ($varlistvariablesagent as $key => $value) {
            $varorientacionsmartagent = $value['orientacionsmart'];
            $varcategoriaidspeechagent = $value['idcategoria'];
            $varconteogeneralagent = $varconteogeneralagent + 1;

            if ($varorientacionsmartagent == "2") {
              array_push($varlistanegativoagent, $varcategoriaidspeechagent);
              $varconteonegativasagent = $varconteonegativasagent + 1;
            }else{
              if ($varorientacionsmartagent == "1") {
                array_push($varlistapositivoagent, $varcategoriaidspeechagent);
                $varconteopositivasagent = $varconteopositivasagent + 1;
              }
            }
          }
          $varvariablesnegativasagent = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $varlistanegativoagent)));
          $varvariablespositivasagent = explode(",", str_replace(array("#", "'", ";", " "), '', implode(", ", $varlistapositivoagent)));

          if (count($varvariablesnegativasagent) > 0) {
            $varcontarvarnegativasagent = (new \yii\db\Query())
                        ->select([
                            'SUM(tbl_speech_general.cantproceso)'
                        ])
                        ->from(['tbl_speech_general'])  
                        ->where(['=','tbl_speech_general.anulado',0])
                        ->andwhere(['=','tbl_speech_general.programacliente',$txtServicioagent])
                        ->andwhere(['between','tbl_speech_general.fechallamada',$varInicioFagent,$varFinFagent])
                        ->andwhere(['in','tbl_speech_general.extension',$txtParametrosagent])
                        ->andwhere(['=','tbl_speech_general.callId',$varcallidsagent])
                        ->andwhere(['in','tbl_speech_general.idvariable',$varvariablesnegativasagent])
                        ->scalar();
          }else{
            $varcontarvarnegativasagent = 0;
          }

          if (count($varvariablespositivasagent) > 0) {
            $varcontarvarpositivasagent = (new \yii\db\Query())
                        ->select([
                            'SUM(tbl_speech_general.cantproceso)'
                        ])
                        ->from(['tbl_speech_general'])  
                        ->where(['=','tbl_speech_general.anulado',0])
                        ->andwhere(['=','tbl_speech_general.programacliente',$txtServicioagent])
                        ->andwhere(['between','tbl_speech_general.fechallamada',$varInicioFagent,$varFinFagent])
                        ->andwhere(['in','tbl_speech_general.extension',$txtParametrosagent])
                        ->andwhere(['=','tbl_speech_general.callId',$varcallidsagent])
                        ->andwhere(['in','tbl_speech_general.idvariable',$varvariablespositivasagent])
                        ->scalar();
          }else{
            $varcontarvarpositivasagent = 0;
          }

          $varResultadoagent = (($varconteonegativasagent - $varcontarvarnegativasagent) + $varcontarvarpositivasagent) / $varconteogeneralagent;

          array_push($arraydataagent, array("Usuario_CC"=>$varCedulaAsesor,"Usuario_Nombre"=>$varNombreAsesor,"usuarios"=>$varusuariologinagent,"cantidadllamadas"=>$varpromedioagent,"score"=>$varResultadoagent,"dimension"=>$varStrDimensionagent,"llamada"=>$varcallidsagent,"fechainteraccion"=>$varfechaagent));
        }

      }


      die( json_encode( array("status"=>"1","data"=>$arraydataagent) ) );

    }




  }

?>
