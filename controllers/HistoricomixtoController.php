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
use app\models\Dashboardspeechcalls;
use app\models\ProcesosVolumendirector;
use app\models\SpeechCategorias;
use app\models\SpeechParametrizar;


  class HistoricomixtoController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','indexvoice','descargarbase','exportbase'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo();
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
        $model = new SpeechParametrizar();
        $txtCumple = null;
        $varextensiones = ['0' => 'Procesos', '1' => 'Calidad de entrenamiento', '2' => 'Ojt'];

        $form = Yii::$app->request->post();
        if($model->load($form)){
            $varServicio = $model->id_dp_clientes;
            $varPcrc = $model->cod_pcrc;
            $varProcesos = $model->anulado;
            $varFecha = explode(" ",$model->fechacreacion);
            
            $varFechaInicio = $varFecha[0].' 05:00:00';
            $varFechaFin = date('Y-m-d',strtotime($varFecha[2]."+ 1 days")).' 05:00:00';

            $varBolsita = \app\models\SpeechCategorias::find()->distinct()
                  ->select(['programacategoria'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$varPcrc])
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->Scalar();

            if ($varProcesos == "2") {
                $listProcesos = \app\models\SpeechParametrizar::find()->distinct()
                  ->select(['rn','ext','usuared'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$varPcrc])
                  ->andwhere('tipoparametro = :varPametros',[':varPametros'=>2])
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->all();
            }elseif ($varProcesos == "1") {
                $listProcesos = \app\models\SpeechParametrizar::find()->distinct()
                  ->select(['rn','ext','usuared'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$varPcrc])
                  ->andwhere('tipoparametro = :varPametros',[':varPametros'=>1])
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->all();
            }else{
                $listProcesos = \app\models\SpeechParametrizar::find()->distinct()
                  ->select(['rn','ext','usuared'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$varPcrc])
                  ->andwhere('tipoparametro is null')
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->all();
            }

            $varCod = null;
            $arrayProcesos = array();
            foreach ($listProcesos as $key => $value) {
                if ($value['rn']!="") {
                    $varCod = 1;
                    array_push($arrayProcesos,$value['rn']);
                }elseif ($value['ext']!="") {
                    $varCod = 2;
                    array_push($arrayProcesos,$value['ext']);
                }elseif ($value['usuared']!="") {
                    $varCod = 3;
                    array_push($arrayProcesos,$value['usuared']);
                }else{
                    array_push($arrayProcesos,"0");
                }
            }
            $txtExtensiones = implode("', '", $arrayProcesos);
            $varExtensiones = explode(",", str_replace(array("#", "'", ";", " "), '', $txtExtensiones));

            $varDataList = (new \yii\db\Query())
            ->select(['*'])
            ->from(['tbl_dashboardspeechcalls'])
            ->where('servicio = :varServicio',[':varServicio'=>$varBolsita])
            ->andwhere('fechallamada BETWEEN :varFechainicios AND :varFechafines',[':varFechainicios'=>$varFechaInicio,':varFechafines'=>$varFechaFin])
            ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
            ->andwhere(['IN','extension',$varExtensiones])
            ->count();

            if ($varDataList != 0) {
                return $this->redirect(array('indexvoice','servicio'=>$varServicio,'codpcrc'=>$varPcrc,'extensiones'=>$txtExtensiones,'bolsitacxm'=>$varBolsita,'dateini'=>$varFechaInicio,'datefin'=>$varFechaFin,'rangofecha'=>$model->fechacreacion,'varCod'=>$varCod));
            }else{
                return $this->render('index',[
                    'model' => $model,
                    'txtCumple' => 1,
                    'varextensiones' => $varextensiones,
                ]);
            }

        }
        
        return $this->render('index',[
            'model' => $model,
            'txtCumple' => $txtCumple,
            'varextensiones' => $varextensiones,
        ]);
    }

    public function actionIndexvoice($servicio,$codpcrc,$extensiones,$bolsitacxm,$dateini,$datefin,$rangofecha,$varCod){
        $txtListaExtension = explode(",", str_replace(array("#", "'", ";", " "), '', $extensiones));

        $varFechaReal = explode(" ",$rangofecha);            
        $varFechaInicioReal = $varFechaReal[0];
        $varFechaFinReal = date('Y-m-d',strtotime($varFechaReal[2]));

        $varNombreServicio = (new \yii\db\Query())
            ->select(['nameArbol'])
            ->from(['tbl_speech_servicios'])
            ->where('id_dp_clientes = :varServicio',[':varServicio'=>$servicio])
            ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
            ->scalar();

        $varLlamadasGeneral = (new \yii\db\Query())
            ->select(['idllamada'])
            ->from(['tbl_speech_servicios'])
            ->where('id_dp_clientes = :varServicio',[':varServicio'=>$servicio])
            ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
            ->scalar();

        $varNombrePcrc = (new \yii\db\Query())
            ->select(['CONCAT(pcrc," - ",cod_pcrc)'])
            ->from(['tbl_speech_categorias'])
            ->where('cod_pcrc = :varpcrc',[':varpcrc'=>$codpcrc])
            ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
            ->scalar();

        $varCantidadLlamadas = (new \yii\db\Query())
            ->select(['*'])
            ->from(['tbl_dashboardspeechcalls'])
            ->where('servicio = :varServicio',[':varServicio'=>$bolsitacxm])
            ->andwhere('fechallamada BETWEEN :varFechainicios AND :varFechafines',[':varFechainicios'=>$dateini,':varFechafines'=>$datefin])
            ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
            ->andwhere(['IN','extension',$txtListaExtension])
            ->andwhere('idcategoria = :varGeneral',[':varGeneral'=>$varLlamadasGeneral])
            ->count();

        $varDataLlamadas = (new \yii\db\Query())
            ->select(['*'])
            ->from(['tbl_dashboardspeechcalls'])
            ->where('servicio = :varServicio',[':varServicio'=>$bolsitacxm])
            ->andwhere('fechallamada BETWEEN :varFechainicios AND :varFechafines',[':varFechainicios'=>$dateini,':varFechafines'=>$datefin])
            ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
            ->andwhere(['IN','extension',$txtListaExtension])
            ->andwhere('idcategoria = :varGeneral',[':varGeneral'=>$varLlamadasGeneral])
            ->all();

        return $this->render('indexvoice',[
            'varNombreServicio' => $varNombreServicio,
            'varNombrePcrc' => $varNombrePcrc,
            'rangofecha' => $rangofecha,
            'extensiones' => $extensiones,
            'varCod' => $varCod,
            'varLlamadasGeneral' => $varLlamadasGeneral,
            'varCantidadLlamadas' => $varCantidadLlamadas,
            'bolsitacxm' => $bolsitacxm,
            'varFechaInicioReal' => $varFechaInicioReal,
            'varFechaFinReal' => $varFechaFinReal,
            'dateini' => $dateini,
            'datefin' => $datefin,
            'varDataLlamadas' => $varDataLlamadas,
            'codpcrc' => $codpcrc,
        ]);
    }

    public function actionListarpcrcs(){
        $txtanulado = 0;
        $txtidcliente = Yii::$app->request->get('id');


          if ($txtidcliente) {
            $txtControl = \app\models\SpeechCategorias::find()->distinct()
              ->select(['tbl_speech_categorias.cod_pcrc'])
              ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
              ->where('tbl_speech_parametrizar.id_dp_clientes = :varCliente',[':varCliente'=>$txtidcliente])
              ->andwhere('tbl_speech_parametrizar.anulado = :varAnulado',[':varAnulado'=>$txtanulado])
              ->count();

            if ($txtControl > 0) {
              $varListaLideresx = \app\models\SpeechCategorias::find()->distinct()
                  ->select(['tbl_speech_categorias.cod_pcrc','tbl_speech_categorias.pcrc'])
                  ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                      'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                  ->where('tbl_speech_parametrizar.id_dp_clientes = :varCliente',[':varCliente'=>$txtidcliente])
                  ->andwhere('tbl_speech_parametrizar.anulado = :varAnulado',[':varAnulado'=>$txtanulado])
                  ->groupby(['tbl_speech_categorias.cod_pcrc'])                  
                  ->all(); 

              echo "<option value='' disabled selected>Seleccionar...</option>";
              foreach ($varListaLideresx as $key => $value) {
                echo "<option value='" . $value->cod_pcrc. "'>" . $value->cod_pcrc.' - '.$value->pcrc . "</option>";
              }
            }else{
              echo "<option>--</option>";
            }
          }else{
            echo "<option>Seleccionar...</option>";
          }          
    }

    public function actionExtensiones(){
        $txtCodpcrc = Yii::$app->request->get('cod_pcrc');
        
        $txtBolsita = \app\models\SpeechCategorias::find()->distinct()
                  ->select(['programacategoria'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$txtCodpcrc])
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->Scalar();

        $txtRn = \app\models\SpeechParametrizar::find()->distinct()
                  ->select(['rn','ext','usuared'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$txtCodpcrc])
                  ->andwhere('tipoparametro is null')
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->all();
       

        $arrayRn = array();
        foreach ($txtRn as $key => $value) {

            if ($value['rn']!="") {
                array_push($arrayRn,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['rn']));
            }elseif ($value['ext']!="") {
                array_push($arrayRn,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['ext']));
            }elseif ($value['usuared']!="") {
                array_push($arrayRn,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['usuared']));
            }else{
                array_push($arrayRn,array("bolsitaservicio"=>"--","extension"=>"--"));
            }

        }

        die(json_encode($arrayRn)); 
    }

    public function actionCalidadentto(){
        $txtCodpcrc = Yii::$app->request->get('cod_pcrc');
        
        $txtBolsita = \app\models\SpeechCategorias::find()->distinct()
                  ->select(['programacategoria'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$txtCodpcrc])
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->Scalar();

        $txtCalidad = \app\models\SpeechParametrizar::find()->distinct()
                  ->select(['rn','ext','usuared'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$txtCodpcrc])
                  ->andwhere('tipoparametro = :varPametros',[':varPametros'=>1])
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->all();
       

        $arrayCalidad = array();
        foreach ($txtCalidad as $key => $value) {

            if ($value['rn']!="") {
                array_push($arrayCalidad,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['rn']));
            }elseif ($value['ext']!="") {
                array_push($arrayCalidad,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['ext']));
            }elseif ($value['usuared']!="") {
                array_push($arrayCalidad,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['usuared']));
            }else{
                array_push($arrayCalidad,array("bolsitaservicio"=>"--","extension"=>"--"));
            }

        }

        die(json_encode($arrayCalidad)); 
    }

    public function actionOjts(){
        $txtCodpcrc = Yii::$app->request->get('cod_pcrc');
        
        $txtBolsita = \app\models\SpeechCategorias::find()->distinct()
                  ->select(['programacategoria'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$txtCodpcrc])
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->Scalar();

        $txtOjts = \app\models\SpeechParametrizar::find()->distinct()
                  ->select(['rn','ext','usuared'])
                  ->where('cod_pcrc = :varPcrc',[':varPcrc'=>$txtCodpcrc])
                  ->andwhere('tipoparametro = :varPametros',[':varPametros'=>2])
                  ->andwhere('anulado = :varAnulado',[':varAnulado'=>0])
                  ->all();
       

        $arrayOjt = array();
        foreach ($txtOjts as $key => $value) {

            if ($value['rn']!="") {
                array_push($arrayOjt,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['rn']));
            }elseif ($value['ext']!="") {
                array_push($arrayOjt,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['ext']));
            }elseif ($value['usuared']!="") {
                array_push($arrayOjt,array("bolsitaservicio"=>$txtBolsita,"extension"=>$value['usuared']));
            }else{
                array_push($arrayOjt,array("bolsitaservicio"=>"--","extension"=>"--"));
            }

        }

        die(json_encode($arrayOjt)); 
    }

    public function actionDescargarbase($arbol_idV, $parametros_idV, $codparametrizar, $codigoPCRC, $nomFechaI, $nomFechaF){
        $model = new SpeechCategorias();       
        $varArbol_idV = $arbol_idV;
        $varParametros_idV = $parametros_idV;
        $varCodparametrizar = $codparametrizar;
        $varFechaI = $nomFechaI;
        $varFechaF = $nomFechaF;
        $varCodigPcrc = $codigoPCRC;

      return $this->renderAjax('descargarbase',[
          'model' => $model,
          'varArbol_idV' => $arbol_idV,
          'varParametros_idV' => $parametros_idV,
          'varCodparametrizar' => $codparametrizar,
          'varFechaI' => $varFechaI,
          'varFechaF' => $varFechaF,
          'varCodigPcrc' => $varCodigPcrc,
          ]);
    }

    public function actionExportbase(){
        $var_FechaIni = null;
        $var_FechaFin = null;
        $varCorreo = null;
        $varArbol_idV = null;
        $varParametros_idV = null;
        $varCodparametrizar = null;
        $VarCodsPcrc = null;

        $var_FechaIni = Yii::$app->request->post("var_FechaIni");
        $var_FechaFin = Yii::$app->request->post("var_FechaFin");
        $txtServicio = Yii::$app->request->post("varArbol_idV");
        $txtParametros = Yii::$app->request->post("varParametros_idV");
        $varCodparametrizar = Yii::$app->request->post("varCodparametrizar");
        $varCorreo = Yii::$app->request->post("var_Destino");
        $VarCodsPcrc = Yii::$app->request->post("var_CodsPcrc");

        $varInicioF = $var_FechaIni.' 05:00:00';
        $varFecha = date('Y-m-d',strtotime($var_FechaFin."+ 1 days"));
        $varFinF = $varFecha.' 05:00:00';

        $varCodigo = $varCodparametrizar;

        if ($varCodigo == 1) {
          $varServicio = Yii::$app->db->createCommand("select distinct nameArbol from tbl_speech_servicios inner join tbl_speech_parametrizar on tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes where tbl_speech_parametrizar.rn in ('$txtParametros') and tbl_speech_parametrizar.cod_pcrc in ('$VarCodsPcrc')")->queryScalar();

          $idArbol = Yii::$app->db->createCommand("select distinct arbol_id from tbl_speech_servicios inner join tbl_speech_parametrizar on tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes where tbl_speech_parametrizar.rn in ('$txtParametros') and tbl_speech_parametrizar.cod_pcrc in ('$VarCodsPcrc')")->queryScalar();
          
        }else{
          if ($varCodigo == 2) {
            $varServicio = Yii::$app->db->createCommand("select distinct nameArbol from tbl_speech_servicios inner join tbl_speech_parametrizar on tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes where tbl_speech_parametrizar.ext in ('$txtParametros') and tbl_speech_parametrizar.cod_pcrc in ('$VarCodsPcrc')")->queryScalar();

            $idArbol = Yii::$app->db->createCommand("select distinct arbol_id from tbl_speech_servicios inner join tbl_speech_parametrizar on tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes where tbl_speech_parametrizar.ext in ('$txtParametros') and tbl_speech_parametrizar.cod_pcrc in ('$VarCodsPcrc')")->queryScalar();

          }else{ 
            $varServicio = Yii::$app->db->createCommand("select distinct nameArbol from tbl_speech_servicios inner join tbl_speech_parametrizar on tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes where tbl_speech_parametrizar.usuared in ('$txtParametros') and tbl_speech_parametrizar.cod_pcrc in ('$VarCodsPcrc')")->queryScalar();

            $idArbol = Yii::$app->db->createCommand("select distinct arbol_id from tbl_speech_servicios inner join tbl_speech_parametrizar on tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes where tbl_speech_parametrizar.usuared in ('$txtParametros') and tbl_speech_parametrizar.cod_pcrc in ('$VarCodsPcrc')")->queryScalar();
          }
        }

        $varListPcrc = Yii::$app->db->createCommand("select cod_pcrc, pcrc from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$VarCodsPcrc') group by cod_pcrc, pcrc")->queryAll();

        $varArrayListPcrc = array();
        foreach ($varListPcrc as $key => $value) {
          array_push($varArrayListPcrc, $value['cod_pcrc'], $value['pcrc']);
        }
        $arrayVariable = implode(" - ", $varArrayListPcrc);

        $phpExc = new \PHPExcel();
        $phpExc->getProperties()
                ->setCreator("Konecta")
                ->setLastModifiedBy("Konecta")
                ->setTitle("Dashboard Speech - ".$varServicio." -")
                ->setSubject("Dashboard Speech - ".$varServicio." -")
                ->setDescription("Este archivo contiene el proceso de las comparaciones con las categorias y las llamadas en Speech,")
                ->setKeywords("Dashboard Speech - ".$varServicio." -");
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

        $styleColor2 = array( 
              'fill' => array( 
                  'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                  'color' => array('rgb' => 'F7b252'),
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
        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:P1');

        $phpExc->getActiveSheet()->SetCellValue('A2','INFORME DASHBOARD SPEECH - '.$varServicio.' -');
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySize);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A2:P2');

        $phpExc->getActiveSheet()->SetCellValue('A3','INFORMACION GENERAL');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A3:P3');
        $phpExc->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('A5',$arrayVariable);
        $phpExc->getActiveSheet()->getStyle('A5')->applyFromArray($styleArray); 
        $phpExc->setActiveSheetIndex(0)->mergeCells('A5:F5');

        $phpExc->getActiveSheet()->SetCellValue('A6','Fecha llamada');
        $phpExc->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('B6','Call-Id');
        $phpExc->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('B6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('B6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('B6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('C6','Parametros');
        $phpExc->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('C6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('D6','Duracion (Segundos)');
        $phpExc->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('D6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('E6','Codigo PCRC');
        $phpExc->getActiveSheet()->getStyle('E6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('F6','Usuarios de Red');
        $phpExc->getActiveSheet()->getStyle('F6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('F6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('F6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('F6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('G6','Datos Asesor');
        $phpExc->getActiveSheet()->getStyle('G6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('G6')->applyFromArray($styleArrayTitle);



        $txtcodigoCC = $VarCodsPcrc;

        $varListIndiVari = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias, responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2,3) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria order by idcategorias asc")->queryAll();
        $varListIndi = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias, responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria order by idcategorias asc")->queryAll();
        $varListadorespo = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias, responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2,3) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') and responsable is not null group by idcategoria order by idcategorias asc")->queryAll();
        $varlistarespo = Yii::$app->db->createCommand("select responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria,responsable order by idcategorias asc")->queryAll();
        $varlistaindica = Yii::$app->db->createCommand("select responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria,responsable order by idcategorias asc")->queryAll();
        $vartotalrespo = count($varlistarespo);
        $vartotalindica = count($varlistaindica);
    //Diego para lo de responsabilidad IDA
        if($varListadorespo) {
             $lastColumn = 'H';
      foreach ($varListIndi as $key => $value) {
    $lastColumn++;
            }
           
            $numCell = 4;
            $varlistaresponsable = array();
            foreach ($varListIndiVari as $key => $value) {
            $varnomresponsable = ''; 
            $varresponsable = $value['responsable'];
            $varidcategoria1 = $value['idcategorias'];
            if ($varresponsable == 1){
              $varnomresponsable = 'Agente';
            }
            if ($varresponsable == 2){
              $varnomresponsable = 'Canal';
            }
            if ($varresponsable == 3){
              $varnomresponsable = 'Marca';
            }

            if ($varidcategoria1 == 2) {
              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varnomresponsable);
              array_push($varlistaresponsable, $varnomresponsable);
              $lastColumn++;
            }
            
            
          }
        }
    
    // fin Diego

        $lastColumn = 'H'; 
        $numCell = 5;
        $numcol1 = 0;
        $varlistasigno = array();
        $varvalormas = 'Positivo';
        $varvalormenos = 'Negativo';
        foreach ($varListIndiVari as $key => $value) {
          $varidCate = $value['idcategoria'];
          $numcol1++;
          $varNumero = Yii::$app->db->createCommand("select orientacionsmart from tbl_speech_categorias where anulado = 0 and idcategoria  = $varidCate and cod_pcrc in ('$txtcodigoCC') and programacategoria in ('$txtServicio')")->queryScalar();

          if ($varNumero == 0) {
            $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varidCate.' - N/A'); 
          }else{
            if ($varNumero == 2) {
              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varidCate.' - Negativo');              
              array_push($varlistasigno, $varvalormenos);
            }else{
              if ($varNumero == 1) {
                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varidCate.' - Positivo');
                array_push($varlistasigno, $varvalormas); 
              }
            }
          }
          $lastColumn++;
          // Diego para lo de responsabilidad
          if($varListadorespo) {
            if($vartotalrespo == $numcol1){
              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, ' ');
              $lastColumn++;
            }
          }  
          
        }

        $lastColumn = 'H'; 
        $numCell = 6;
        $numcol1 = 0;
        foreach ($varListIndiVari as $key => $value) {
          $varidColor = $value['idcategoria'];
          $numcol1++;
          $varColor = Yii::$app->db->createCommand("select idcategorias from tbl_speech_categorias where anulado = 0 and idcategoria  = $varidColor and cod_pcrc in ('$txtcodigoCC') and programacategoria in ('$txtServicio')")->queryScalar();
          
          if ($varColor == 1) {
            $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['nombre']); 
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle);
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorhigh);
          }else{
            if ($varColor == 2) {
              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['nombre']); 
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayTitle);              
            }else{
              if ($varColor == 3) {
                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['nombre']); 
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorMiddle);
              }
            }
          }
          $lastColumn++;
          // Diego para lo de responsabilidad
          if($varListadorespo) {
            if($vartotalrespo == $numcol1){
              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, ' %Total Agente');
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor2);
              $lastColumn++;
            }
          } 
          
        }


        $numCell = $numCell + 1;

        // Diego Para calculo de porcentahe de Agentes IDA
      
      $varListIndiVari2 = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias, orientacionsmart, responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2,3) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') and responsable = 1 group by idcategoria order by idcategorias asc")->queryAll();
                    
      $arrayListaVar = array();
      $arraYListaVarMas = array();
      $arraYListaVarMenos = array();
      foreach ($varListIndiVari2 as $key => $value) {
          $varidCate = $value['idcategoria'];
          $varNumero1 = $value['orientacionsmart'];
          array_push($arrayListaVar, $value['idcategoria']);
          if ($varNumero1 == 2) {
          //    - Negativo
              array_push($arraYListaVarMas, $value['idcategoria']);
          }else{
                if ($varNumero1 == 1) {
                  //     - Positivo
                    array_push($arraYListaVarMenos, $value['idcategoria']);
                        
                }
              }
      }
      $arrayVariableR = implode(", ", $arrayListaVar);
      $arrayVariableMasR = implode(", ", $arraYListaVarMas);
      $arrayVariableMenosR = implode(", ", $arraYListaVarMenos);
      $sumapositivoR = 0;
      $sumanegativoR = 0;
      $cuentanegativoR = 0;
      $cuentavari = 0;
  // fin

        $varListMetadata = Yii::$app->db->createCommand("select callid, extension, fechallamada, login_id, fechareal  from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtParametros') and  fechallamada between '$varInicioF' and '$varFinF' group by callid, extension")->queryAll();

        foreach ($varListMetadata as $key => $value) {
          $txtCallid = $value['callid'];
          $txtExtensionid = $value['extension'];
          $txtFecha = $value['fechallamada'];
          
          
          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['fechareal']); 
          $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['callid']); 
          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['extension']); 

          $varTimes = Yii::$app->db->createCommand("select round(AVG(callduracion))  from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio')  and fechallamada = '$txtFecha' and callid = $txtCallid and extension in ('$txtExtensionid')")->queryScalar();

          $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $varTimes);       


          if ($varCodigo == 1) {
            $varCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where anulado = 0 and rn in ('$txtExtensionid')")->queryScalar();          
          }else{
            if ($varCodigo == 2) {
              $varCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where anulado = 0 and ext in ('$txtExtensionid')")->queryScalar();
            }else{ 
              $varCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where anulado = 0 and usuared in ('$txtExtensionid')")->queryScalar();
            }
          }

          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $VarCodsPcrc); 
          $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['login_id']);

            if (is_numeric($value['login_id'])) {
                $varDocumento = Yii::$app->db->createCommand('
                    SELECT e.dsusuario_red FROM tbl_evaluados e 
                        WHERE 
                            e.identificacion IN (:varUsua)
                        GROUP BY e.identificacion')->bindValues($paramsRed)->queryScalar();
            }else{
                $varDocumento = Yii::$app->db->createCommand('
                    SELECT e.identificacion FROM tbl_evaluados e 
                        WHERE 
                            e.dsusuario_red IN (:varUsua)
                    GROUP BY e.identificacion')->bindValues($paramsRed)->queryScalar();
            }

            $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $varDocumento);

          $lastColumn = 'H';
          foreach ($varListIndiVari as $key => $value) {
            $varVariables = $value['idcategoria'];
            $varIdcategorias = $value['idcategorias'];

            if ($varIdcategorias == 1) {

              $varParametro = Yii::$app->db->createCommand("select distinct tipoparametro from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$txtcodigoCC') and idcategoria = $varVariables and idcategorias = $varIdcategorias")->queryScalar();

              $varNombre = Yii::$app->db->createCommand("select distinct nombre from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$txtcodigoCC') and idcategoria = $varVariables and idcategorias = $varIdcategorias")->queryScalar();

              $varListVariables = Yii::$app->db->createCommand("select distinct idcategoria, orientacionsmart from  tbl_speech_categorias where anulado = 0  and cod_pcrc in ('$txtcodigoCC') and idcategorias = 2 and tipoindicador like '$varNombre'")->queryAll();

              $arrayListOfVar = array();
              $arraYListOfVarMas = array();
              $arraYListOfVarMenos = array();
              foreach ($varListVariables as $key => $value) {
                $varOrienta = $value['orientacionsmart'];

                array_push($arrayListOfVar, $value['idcategoria']);

                if ($varOrienta == 1) {
                  array_push($arraYListOfVarMenos, $value['idcategoria']);
                }else{
                  if ($varOrienta == 2) {
                    array_push($arraYListOfVarMas, $value['idcategoria']);
                  }
                }                      
              }
              $arrayVariable = implode(", ", $arrayListOfVar);
              $arrayVariableMas = implode(", ", $arraYListOfVarMas);
              $arrayVariableMenos = implode(", ", $arraYListOfVarMenos);


              if (count($varListVariables) != 0) {

                if ($varParametro == 2) {

                  $varSumarPositivas = 0;
                  $varSumarNegativas = 0;
                  foreach ($varListVariables as $key => $value) {
                    $varSmart = $value['orientacionsmart'];

                    if ($varSmart == 2) {
                      $varSumarPositivas = $varSumarPositivas + 1;
                    }else{
                      if ($varSmart == 1) {
                        $varSumarNegativas = $varSumarNegativas + 1;
                      }
                    }
                  }

                  $varTotalvariables = count($varListVariables);

                  if ($varSumarPositivas == $varTotalvariables) {
                    $txtRtaIndicador = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and  callid = $txtCallid and idindicador in ('$arrayVariable') and idvariable in ('$arrayVariable')")->queryScalar();

                    if ($txtRtaIndicador == 0 || $txtRtaIndicador == null) {
                      $varConteo = 0;
                    }else{
                      $varConteo = 1;
                    }

                    //Diego para calcular promedio Agente positivas                    
                    
                    $vartotalrespo = count($varlistarespo);
                    $vartotalindica = count($varlistaindica);
                      //Positivo
                      $txtRtaIndicador = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and  callid = $txtCallid and idindicador in ('$arrayVariableMasR') and idvariable in ('$arrayVariableMasR')")->queryScalar();

                      

                    // fin Diego

                  }else{
                    $txtRtaIndicador = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and  callid = $txtCallid and idindicador in ('$arrayVariableMenos') and idvariable in ('$arrayVariableMenos')")->queryScalar();

                    if ($txtRtaIndicador == 0 || $txtRtaIndicador == null) {                            
                      $varConteo = 1;
                    }else{                            
                      $varConteo = 0;
                    }

                    //Negativo
                    $txtRtaIndicador = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and  callid = $txtCallid and idindicador in ('$arrayVariableMenosR') and idvariable in ('$arrayVariableMenosR')")->queryScalar();



                  }

                }else{
                  if ($varParametro == 1) {
                    
                    $varSumarPositivas = 0;
                    $varSumarNegativas = 0;
                    foreach ($varListVariables as $key => $value) {
                      $varSmart = $value['orientacionsmart'];

                      if ($varSmart == 2) {
                        $varSumarPositivas = $varSumarPositivas + 1;
                      }else{
                        if ($varSmart == 1) {
                          $varSumarNegativas = $varSumarNegativas + 1;
                        }
                      }
                    }

                    $varTotalvariables = count($varListVariables);

                    if ($varSumarPositivas == $varTotalvariables) {

                      $txtRtaIndicador = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and  callid = $txtCallid and idindicador in ('$arrayVariable') and idvariable in ('$arrayVariable')")->queryScalar();

                      if ($txtRtaIndicador == $varTotalvariables || $txtRtaIndicador != null) {
                        $varConteo = 1;
                      }else{
                        $varConteo = 0;
                      }

                    }else{

                      $varconteomas = 0;
                      $varconteomeno = 0;

                      if ($arrayVariableMas != "") {
                        $varconteomas = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and  callid = $txtCallid and idindicador in ('$arrayVariableMas') and idvariable in ('$arrayVariableMas')")->queryScalar();
                      }else{
                        $varconteomas = 0;
                      }                            

                      if ($arrayVariableMenos != "") {
                        $varconteomeno = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and  callid = $txtCallid and idindicador in ('$arrayVariableMenos') and idvariable in ('$arrayVariableMenos')")->queryScalar();
                      }else{
                        $varconteomeno = 0;
                      }
                            

                      if ($varconteomeno == null || $varconteomeno == 0 && $varconteomas == $varTotalvariables) {
                        $varConteo = 1;
                      }else{
                        $varConteo = 0;
                      }

                    }

                  }
                }

              }else{
                $varConteo = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and callid = $txtCallid   and idcategoria = $varVariables")->queryScalar();
              }



            }else{
              if ($varIdcategorias == 2) {
                $varConteo = Yii::$app->db->createCommand("select count(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio')   and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and callid = $txtCallid and idindicador = $varVariables and idvariable = $varVariables")->queryScalar();
              }else{
                if ($varIdcategorias == 3) {
                  $varConteo = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and callid = $txtCallid   and idcategoria = $varVariables")->queryScalar();
                }
              }
            }
            

            $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varConteo);
                  

            $lastColumn++;

            if($varListadorespo) {
                $cuentavari++;
                
                if($cuentavari > $vartotalindica && $cuentavari <= $vartotalrespo){
                  if ($varlistaresponsable[$cuentavari - ($vartotalindica + 1)] == 'Agente'){
                      if($varlistasigno[$cuentavari - 1] == 'Positivo'){
                        $sumapositivoR = $sumapositivoR + $varConteo;
                      }
                      if($varlistasigno[$cuentavari - 1] == 'Negativo'){
                        $sumanegativoR = $sumanegativoR + $varConteo;
                        $cuentanegativoR++;
                      }
                  }
                }

                //imprime total porcentaje Agente po callid
                $varTotalvariables = count($varListIndiVari2);
                if($cuentavari == ($vartotalrespo)) {
                  $varaqui = 'Aqui';
                  if($cuentanegativoR == 0) {
                    $totalpondeR = round((($sumapositivoR / $varTotalvariables) * 100),2);
                  }
                  if($cuentanegativoR == $varTotalvariables) {
                    $totalpondeR = round(((($cuentanegativoR - $sumanegativoR) / $varTotalvariables) * 100),2);
                  }
                  if($cuentanegativoR != $varTotalvariables && $cuentanegativoR > 0) {
                    $totalpondeR = round(((($sumapositivoR + ($cuentanegativoR - $sumanegativoR)) / $varTotalvariables) * 100),2);
                  }              
                  $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $totalpondeR); 
                $lastColumn++;
                }
            }
            
          }
          $numCell++;
          $cuentavari = 0;
          $cuentanegativoR = 0;
          $sumapositivoR = 0;
          $sumapositivoR = 0;
          $cuentanegativoR = 0;
          $sumanegativoR = 0;
          
        }


        $hoy = getdate();
        $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_DashBoard_Speech_".$varServicio;
              
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
        $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
        $tmpFile.= ".xls";

        $objWriter->save($tmpFile);

        $message = "<html><body>";
        $message .= "<h3>Se ha realizado el envio correcto del archivo del programa DashBoard Speech.</h3>";
        $message .= "</body></html>";

        Yii::$app->mailer->compose()
                        ->setTo($varCorreo)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Envio Dashboard Speech ".$varServicio)
                        ->attach($tmpFile)
                        ->setHtmlBody($message)
                        ->send();

        $rtaenvio = 1;
        die(json_encode($rtaenvio));
    }

    

  }

?>
