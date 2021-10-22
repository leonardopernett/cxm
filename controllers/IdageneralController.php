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
                'actions' => ['index','enviararchivo','procesarentto','procesarvalores'],
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
              ],
              [
                'actions' => ['procesarvalores'],
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
      $model = new IdaGeneral();
      $fechamodifica = date("Y-m-d H:i:s");

      $form = Yii::$app->request->post();
      if($model->load($form)){
        $vartipo = $model->tipoproceso;
        $varservicio = $model->servicio;

        $varcategeneral = Yii::$app->db->createCommand("SELECT DISTINCT idllamada FROM tbl_speech_servicios s WHERE s.id_dp_clientes = $varservicio")->queryScalar();

        $varusuarioslist = Yii::$app->db->createCommand("SELECT DISTINCT ce.usuario_de_red_o_extension, ce.fecha_de_ojt, ce.fecha_de_conexion from tbl_calidad_entto ce")->queryAll();

        if ($vartipo == 1) {

          $varservicios = Yii::$app->db->createCommand("SELECT DISTINCT sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sp.tipoparametro = 1 AND sc.responsable = 1 AND sc.idcategorias = 2 AND sp.id_dp_clientes = $varservicio")->queryScalar();
          
          $varlistextensions = Yii::$app->db->createCommand("SELECT if (sp.rn != '', sp.rn, if(sp.ext != '', sp.ext, if(sp.usuared != '', sp.usuared, if(sp.comentarios != '', sp.comentarios, 'N/A')))) AS Extension FROM tbl_speech_parametrizar sp WHERE sp.anulado = 0 AND sp.tipoparametro = 1 AND sp.id_dp_clientes = $varservicio")->queryAll();
          $vararrayext = array();
          foreach ($varlistextensions as $key => $value) {
            array_push($vararrayext, $value['Extension']);
          }
          $varextlist = implode("', '", $vararrayext);
          

          $varlistcategorias = Yii::$app->db->createCommand("SELECT DISTINCT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sp.tipoparametro = 1 AND sc.idcategorias = 2  AND sc.responsable = 1 AND sp.id_dp_clientes = $varservicio")->queryAll();

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
            
            $varlistcallid = Yii::$app->db->createCommand("SELECT DISTINCT d.callId FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.servicio IN ('$varservicios') AND d.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00' AND d.extension IN ('$varextlist') AND d.login_id IN ('$varasesor')")->queryAll();

            $varArraySumas = array();

            if (count($varlistcallid) > 0) {

              $varvalidaC = Yii::$app->db->createCommand("SELECT COUNT(ig.idcenttog) FROM tbl_ida_general ig WHERE ig.anulado = 0 AND ig.usuariored = '$varasesor' AND ig.tipoproceso = 'Calidad de Entrenamiento'")->queryScalar();

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

                $varlistvariables = Yii::$app->db->createCommand("SELECT DISTINCT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sc.responsable = 1 AND sp.tipoparametro = 1 AND sc.idcategorias = 2  AND sp.id_dp_clientes = $varservicio")->queryAll();

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
                    $contarnegativas = Yii::$app->db->createCommand("SELECT COUNT(s.idvariable) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in ('$varcategoriap') AND s.callid IN ($varCallid) AND s.idindicador IN ('$varidcategoriav') AND s.idvariable IN ($varidcategoriav) and s.extension in ('$varextlist') AND s.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00'")->queryScalar();

                    if ($contarnegativas == '1') {
                      $countnegativasc = $countnegativasc + 1;
                    }
                  }else{
                    if ($varorientaciones == '1') {
                      $countpositivas = $countpositivas + 1;
                      $contarpositivas = Yii::$app->db->createCommand("SELECT COUNT(s.idvariable) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in ('$varcategoriap') AND s.callid IN ($varCallid) AND s.idindicador IN ('$varidcategoriav') AND s.idvariable IN ($varidcategoriav) and s.extension in ('$varextlist') AND s.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00'")->queryScalar();

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

              $varvaloram = Yii::$app->db->createCommand("SELECT round(AVG(ef.score) * 100,2) AS Score FROM tbl_ejecucionformularios ef INNER JOIN tbl_evaluados e ON ef.evaluado_id = e.id  WHERE ef.dimension_id IN (4) AND ef.arbol_id IN (3061)  AND ef.created BETWEEN '$varfechainicio 00:00:00' AND '$varfechafin 23:59:59'    AND e.dsusuario_red IN ('$varasesor') GROUP BY ef.id")->queryScalar();

              if ($varvaloram == null) {
                $varvaloram = 0;
              }

              $varvaloracantidad = Yii::$app->db->createCommand("SELECT count(ef.id) AS Score FROM tbl_ejecucionformularios ef INNER JOIN tbl_evaluados e ON ef.evaluado_id = e.id  WHERE ef.dimension_id IN (4) AND ef.arbol_id IN (3061)  AND ef.created BETWEEN '$varfechainicio 00:00:00' AND '$varfechafin 23:59:59'    AND e.dsusuario_red IN ('$varasesor') GROUP BY ef.id")->queryScalar();

              if ($varvaloracantidad == null) {
                $varvaloracantidad = 0;
              }

              $varcantidada = Yii::$app->db->createCommand("SELECT count(d.callId) FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.servicio IN ('$varservicios') AND d.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00' AND d.extension IN ('$varextlist') AND d.login_id IN ('$varasesor') AND d.idcategoria IN ($varcategeneral)")->queryScalar();

              if ($varcantidada == null) {
                $varcantidada = 0;
              }


              Yii::$app->db->createCommand("UPDATE tbl_ida_general ig SET ig.totalentto = $countgeneralcallid, ig.fechamodificacion = '$fechamodifica', ig.cantidadvaloram = '$varvaloracantidad', ig.totalvaloram = '$varvaloram', ig.cantidadvaloraa = '$varcantidada' WHERE ig.anulado = 0 AND ig.usuariored = '$varasesor' AND ig.servicio = $varservicio AND ig.tipoproceso = 'Calidad de Entrenamiento'")->execute();        
              
            }

          }

          return $this->redirect('index');
        }else{

          $varservicios = Yii::$app->db->createCommand("SELECT DISTINCT sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sp.tipoparametro = 2 AND sc.responsable = 1 AND sc.idcategorias = 2 AND sp.id_dp_clientes = $varservicio")->queryScalar();
          
          $varlistextensions = Yii::$app->db->createCommand("SELECT if (sp.rn != '', sp.rn, if(sp.ext != '', sp.ext, if(sp.usuared != '', sp.usuared, if(sp.comentarios != '', sp.comentarios, 'N/A')))) AS Extension FROM tbl_speech_parametrizar sp WHERE sp.anulado = 0 AND sp.tipoparametro = 2 AND sp.id_dp_clientes = $varservicio")->queryAll();
          $vararrayext = array();
          foreach ($varlistextensions as $key => $value) {
            array_push($vararrayext, $value['Extension']);
          }
          $varextlist = implode("', '", $vararrayext);
          

          $varlistcategorias = Yii::$app->db->createCommand("SELECT DISTINCT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.anulado = 0 AND sp.tipoparametro = 2 AND sc.idcategorias = 2 AND sp.id_dp_clientes = $varservicio")->queryAll();

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
            
            $varlistcallid = Yii::$app->db->createCommand("SELECT DISTINCT d.callId FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.servicio IN ('$varservicios') AND d.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00' AND d.extension IN ('$varextlist') AND d.login_id IN ('$varasesor')")->queryAll();

            $varArraySumas = array();

            if (count($varlistcallid) > 0) {

              $varvalidaC = Yii::$app->db->createCommand("SELECT COUNT(ig.idcenttog) FROM tbl_ida_general ig WHERE ig.anulado = 0 AND ig.usuariored = '$varasesor' AND ig.tipoproceso = 'OJT'")->queryScalar();

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

                $varlistvariables = Yii::$app->db->createCommand("SELECT DISTINCT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc INNER JOIN tbl_speech_parametrizar sp ON sc.cod_pcrc = sp.cod_pcrc WHERE sp.anulado = 0 AND sc.responsable = 1 AND sc.anulado = 0 AND sp.tipoparametro = 1 AND sc.idcategorias = 2  AND sp.id_dp_clientes = $varservicio")->queryAll();

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
                    $contarnegativas = Yii::$app->db->createCommand("SELECT COUNT(s.idvariable) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in ('$varcategoriap') AND s.callid IN ($varCallid) AND s.idvariable IN ($varidcategoriav) and s.extension in ('$varextlist') AND s.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00'")->queryScalar();

                    if ($contarnegativas == '1') {
                      $countnegativasc = $countnegativasc + 1;
                    }
                  }else{
                    if ($varorientaciones == '2') {
                      $countpositivas = $countpositivas + 1;
                      $contarpositivas = Yii::$app->db->createCommand("SELECT COUNT(s.idvariable) FROM tbl_speech_general s WHERE s.anulado = 0 AND s.programacliente in ('$varcategoriap') AND s.callid IN ($varCallid)  AND s.idvariable IN ($varidcategoriav) and s.extension in ('$varextlist') AND s.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00'")->queryScalar();

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

              $varvaloram = Yii::$app->db->createCommand("SELECT round(AVG(ef.score) * 100,2) AS Score FROM tbl_ejecucionformularios ef INNER JOIN tbl_evaluados e ON ef.evaluado_id = e.id  WHERE ef.dimension_id IN (4) AND ef.arbol_id IN (3061)  AND ef.created BETWEEN '$varfechainicio 00:00:00' AND '$varfechafin 23:59:59'    AND e.dsusuario_red IN ('$varasesor') GROUP BY ef.id")->queryScalar();

              if ($varvaloram == null) {
                $varvaloram = 0;
              }

              $varvaloracantidad = Yii::$app->db->createCommand("SELECT count(ef.id) AS Score FROM tbl_ejecucionformularios ef INNER JOIN tbl_evaluados e ON ef.evaluado_id = e.id  WHERE ef.dimension_id IN (4) AND ef.arbol_id IN (3061)  AND ef.created BETWEEN '$varfechainicio 00:00:00' AND '$varfechafin 23:59:59'    AND e.dsusuario_red IN ('$varasesor') GROUP BY ef.id")->queryScalar();

              if ($varvaloracantidad == null) {
                $varvaloracantidad = 0;
              }

              $varcantidada = Yii::$app->db->createCommand("SELECT count(d.callId) FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.servicio IN ('$varservicios') AND d.fechallamada BETWEEN '$varfechainicio 05:00:00' AND '$varfechafin 05:00:00' AND d.extension IN ('$varextlist') AND d.login_id IN ('$varasesor') AND d.idcategoria IN ($varcategeneral)")->queryScalar();

              if ($varcantidada == null) {
                $varcantidada = 0;
              }


              Yii::$app->db->createCommand("UPDATE tbl_ida_general ig SET ig.totalojt = $countgeneralcallid, ig.fechamodificacion = '$fechamodifica', ig.cantidadvaloram = '$varvaloracantidad', ig.totalvaloram = '$varvaloram', ig.cantidadvaloraa = '$varcantidada' WHERE ig.anulado = 0 AND ig.usuariored = '$varasesor' AND ig.servicio = $varservicio AND ig.tipoproceso = 'OJT'")->execute();        
              
            }
          }
        }

        return $this->redirect('index');
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

          $styleArraySize = array(
                  'font' => array(
                          'bold' => true,
                          'size'  => 15,
                  ),
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

          $styleArraySubTitle2 = array(              
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => 'C6C6C6'),
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

          $styleColorLess = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => '92DD5B'),
                  )
              );

          $styleColorMiddle = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => 'CED9D5'),
                  )
              );

          $styleColorhigh = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => '22CCC4'),
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

      $data_post = json_decode(json_encode($datapost));
      die($data_post);
      

      $varpcrc = "122211-1";
      $varDimension = array(1,2);
      $varFechaInicio = "2021-09-13";
      $varFechaFin = "2021-10-23";
      $varDocumentos = array("1152472499","1049652987","1193566447","1026140988","1016105446","1000755044","1000018172","1000886354","1001138124","1035234702","1020466552","1000090511","1017203186");
      $varUsuarios = array("felipe.salgado","sergio.bernal","luisa.gomez.p","cristian.ceballo","manuela.valencia","angie.lopez.r","valentina.barragan","michel.moreno","maria.gomez","julieth.garcia","rosa.garcia.b","anlly.jaramillo","jaisson.atehortua");

      if (!isset($varDocumentos) 
        || !isset($varUsuarios) 
        || !isset($varpcrc) 
        || !isset($varFechaInicio) 
        || !isset($varFechaFin)
        || empty($varDocumentos)
        || empty($varUsuarios) 
        || empty($varpcrc) 
        || empty($varFechaInicio) 
        || empty($varFechaFin)
      ) {
        die(json_encode(array("status"=>"0","data"=>"Algunos de los campos obligatorios no se enviaron correctamente")));
      }


      if (count($varDocumentos) == count($varUsuarios)) {

          $listdimensiones = array();
          $array_dimensiones = count($varDimension);
          for ($i = 0; $i < $array_dimensiones; ++$i){
              array_push($listdimensiones, $varDimension[$i]);
          }
          $varparametros = implode(", ", $listdimensiones);

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
            

            array_push($arraydata, array("usuarios"=>$varusuariologin,"cantidadllamadas"=>$varpromedio,"score"=>$resultadosIDA));
          
      }else{
          
      }      
       
    }

  }

?>
