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
use PHPExcel;
use PHPExcel_IOFactory;
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;
use app\models\UploadForm2;
use app\models\DashboardtmpSpeech;
use app\models\Dashboardpermisos;
use app\models\ProcesosVolumendirector;
use app\models\ProcesosDirectores;
use app\models\SpeechServicios;
use app\models\SpeechCategorias; 
use app\models\SpeechParametrizar;

  class DashboardspeechController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['prueba', 'importarexcel', 'indexvoice', 'mportarexcel2','categoriasvoice','listashijo','categoriasgeneral','asignararbol','categoriasconfig','categoriasoption','categoriasview','categoriasupdate','categoriasdelete','export','categoriaspermisos','export2','seleccionservicio','registrarcategorias','listacategorias','exportarcategorias','parametrizarcategorias','listaracciones','categoriasverificar', 'elegirprograma','generarformula','listashijos','listashijoss','categoriasida','ingresardashboard','modificardashboard','graficatmo'],
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
    
    public function actionIndex(){
	die("ee");
      $model = new Dashboardcategorias(); 
      $model2 = new ProcesosVolumendirector();
      $model3 = new SpeechCategorias();
      $txtanulado = 0;
      $txtfechacreacion = date("Y-m-d");
      $array_cadena = 0; 
      $txtvarNew = null;     

      $data = Yii::$app->request->post();     
      if ($model3->load($data)) {
        $txtPcrc = $model3->clientecategoria;
        $txtPruebas = $model3->pcrc;
        $txtCodigos = $model3->cod_pcrc;
        $porciones = explode(",", $txtPruebas); 
        $countPorciones = count($porciones);

        $varArrayProgram = array();
        $varArrayparams = array();
        $varCod = 0;

        for ($i=0; $i < $countPorciones; $i++) { 
          if ($i%2 == 0) {                    
            array_push($varArrayProgram, $porciones[$i]);                    
          }else{
            $varcountWords = strlen($porciones[$i]);
            if ($varcountWords == 2) {
              array_push($varArrayparams, $porciones[$i]);
              $varCod = 1;
            }else{
              if ($varcountWords == 5 || $varcountWords == 6) {
                array_push($varArrayparams, $porciones[$i]);
                $varCod = 2;
              }else{
                array_push($varArrayparams, $porciones[$i]);
                $varCod = 3;
              }
            }                                
          }                  
        }

        $txtFecha = explode(" ", $model3->fechacreacion);
        $sessiones = Yii::$app->user->identity->id;                

        $varFechaInicio = $txtFecha[0].' 05:00:00';

        $varFechaF = date('Y-m-d',strtotime($txtFecha[2]."+ 1 days"));
        $varFechaFin = $varFechaF.' 05:00:00';

        $arrayProgram = implode("', '", $varArrayProgram);
        $arrayParams = implode("', '", $varArrayparams);  

        
        if ($varCod == 1) {
          $varListCC = Yii::$app->db->createCommand("select distinct cod_pcrc from tbl_speech_parametrizar where anulado = 0 and rn in ('$arrayParams') and id_dp_clientes = $txtPcrc")->queryAll();
        }else{
          if ($varCod == 2) {
            $varListCC = Yii::$app->db->createCommand("select distinct cod_pcrc from tbl_speech_parametrizar where anulado = 0 and ext in ('$arrayParams') and id_dp_clientes = $txtPcrc")->queryAll();
          }else{
            $varListCC = Yii::$app->db->createCommand("select distinct cod_pcrc from tbl_speech_parametrizar where anulado = 0 and usuared in ('$arrayParams') and id_dp_clientes = $txtPcrc")->queryAll();
          }
        }

        $vararrayCC = array();
        foreach ($varListCC as $key => $value) {
          array_push($vararrayCC, $value['cod_pcrc']);
        }
        // $varCC = implode("', '", $txtCodigos);
        $varCC = $txtCodigos;

        $varconteos = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$arrayProgram') and extension in ('$arrayParams') and fechallamada between '$varFechaInicio' and '$varFechaFin'")->queryScalar();

        // var_dump($arrayProgram);
        // var_dump($arrayParams);
        // var_dump($varFechaInicio.' - '.$varFechaFin);
        if ($varconteos > 0) {
          return $this->redirect(array('indexvoice','arbol_idV'=>$arrayProgram,'codpcrc'=>$varCC,'parametros_idV'=>$arrayParams,'codparametrizar'=>$varCod,'dateini'=>$txtFecha[0],'datefin'=>$txtFecha[2]));
        }else{
          $txtvarNew = 1;
          return $this->render('index',[
            'model' => $model,
            'model2' => $model2,
            'model3' => $model3,
            'txtvarNew' => $txtvarNew,
            ]);
        }        

      }  

      return $this->render('index',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'txtvarNew' => $txtvarNew,
        ]);
    }
      
      public function actionPrueba(){
        $txtvRta = Yii::$app->request->post("txtvRta");

        die(json_encode($users));
      }

      public function actionImportarexcel(){
        $model = new UploadForm2();
        $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");

          if (Yii::$app->request->isPost) {
              $model->file = UploadedFile::getInstance($model, 'file');

              if ($model->file && $model->validate()) {                
                  $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);

                  $fila = 1;
                  if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
                    while (($datos = fgetcsv($gestor)) !== false) {
                      $numero = count($datos);
                  $fila++;
                  for ($c=0; $c < $numero; $c++) {
                      $varArray = $datos[$c]; 
                      $varDatos = explode(";", utf8_encode($varArray));

                Yii::$app->db->createCommand()->insert('tbl_dashboardcategorias',[
                                           'idcategoria' => $varDatos[0],
                                           'nombre' => $varDatos[1],
                                           'tipocategoria' => $varDatos[2],
                                           'tipoindicador' => $varDatos[3],
                                           'clientecategoria' => $varDatos[4],
                                           'ciudadcategoria' => $varDatos[5],
                                           'fechacreacion' => $txtfechacreacion,
                                           'anulado' => $txtanulado,
                                           'usua_id' => $varDatos[6],
                                           'orientacion' => $varDatos[7],
                                           'usabilidad' => 1,
                                       ])->execute();

                $varClientePcrc = $varDatos[4];                       
             
                  } 
            }
            $varClientePcrc1 = $varClientePcrc;
            fclose($gestor);

            $txtServicios = Yii::$app->db->createCommand("select distinct count(clientecategoria) from tbl_dashboardservicios where anulado = 0 and clientecategoria like '%$varClientePcrc1%'")->queryScalar();
            
            if ($txtServicios != 0) {
              return $this->redirect('index');
            }else{
              $txtSeparados = explode("_", $varClientePcrc1);   

              if (count($txtSeparados) > 1) {
                $varServicio1 = $txtSeparados[0];
                $varServicio2 = $txtSeparados[1];
                $varServicio3 = $varServicio1." ".$varServicio2;

                Yii::$app->db->createCommand()->insert('tbl_dashboardservicios',[
                                            'nombreservicio' => $varServicio3,
                                            'clientecategoria' => $varClientePcrc1,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anulado' => $txtanulado,
                                        ])->execute();
              }else{
                $varServicio1 = $txtSeparados[0];

                Yii::$app->db->createCommand()->insert('tbl_dashboardservicios',[
                                            'nombreservicio' => $varServicio1,
                                            'clientecategoria' => $varClientePcrc1,
                                            'fechacreacion' => $txtfechacreacion,
                                            'anulado' => $txtanulado,
                                        ])->execute();
              } 
            } 

            return $this->redirect('index');
                  }
              }
          }

        return $this->renderAjax('importarexcel',[
          'model' => $model,
          ]);

      }

      public function actionIndexvoice($arbol_idV,$parametros_idV,$codparametrizar,$dateini,$datefin,$codpcrc){
        $model = new Dashboardcategorias();
        $model2 = new Dashboardcategorias();
        $txtServicio = $arbol_idV;
        $txtParametros = $parametros_idV;
        $txtCodParametrizar = $codparametrizar;
        $txtFechaIni = $dateini;
        $txtFechaFin = $datefin;
        $txtIndicador = null;
        $txtVariables1 = null;
        $txtCategoria = null;
        $txtCodPcrc = null;
        $varName = null;
        $varName2 = null;
        $varName3 = null;
        $txtCodPcrcok = $codpcrc;
        $txtContador = 0;

        $varVerificar = 0;

        $data = Yii::$app->request->post();
        if ($model->load($data)) {
          $varName = $model->idcategoria; 
          //$txtVariables1 = $model->nombre;
          $varcod_pcrc = $model->nombre;
          // $varName2 = $model->nombre;
          
          $varCate = Yii::$app->db->createCommand("select idcategorias from tbl_speech_categorias where anulado = 0 and idcategoria = $varName  and cod_pcrc in ('$varcod_pcrc')")->queryScalar();


          if ($varCate == 1) {
            if ($txtCodParametrizar == 1) {
              $txtContador = Yii::$app->db->createCommand("select count(*) from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sp.anulado = 0  and sc.idcategorias = 1 and sc.programacategoria in ('$txtServicio') and sp.rn in ('$txtParametros') and sc.cod_pcrc in ('$varcod_pcrc') and sc.idcategoria = $varName")->queryScalar();
            }else{
              if ($txtCodParametrizar == 2) {
                $txtContador = Yii::$app->db->createCommand("select count(*) from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sp.anulado = 0  and sc.idcategorias = 1 and sc.programacategoria in ('$txtServicio') and sp.ext in ('$txtParametros') and sc.cod_pcrc in ('$varcod_pcrc') and sc.idcategoria = $varName")->queryScalar();
              }else{
                $txtContador = Yii::$app->db->createCommand("select count(*) from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sp.anulado = 0  and sc.idcategorias = 1 and sc.programacategoria in ('$txtServicio') and sp.usuared in ('$txtParametros') and sc.cod_pcrc in ('$varcod_pcrc') and sc.idcategoria = $varName")->queryScalar();
              }
            } 
          }
          
          // var_dump($txtContador);
          if ($txtContador != 0) {
             if ($model->load($data)) {
                $varIdCategoria = $model->idcategoria;
                $txtIndicador = Yii::$app->db->createCommand("select distinct nombre from tbl_speech_categorias where anulado = 0 and idcategorias = 1 and idcategoria = $varIdCategoria and cod_pcrc in ('$varcod_pcrc')")->queryScalar();
                $varListCodPcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_categorias where anulado = 0 and idcategorias = 1 and idcategoria = $varIdCategoria and cod_pcrc in ('$varcod_pcrc')")->queryAll();
                $arrayCodigo = array();                
                foreach ($varListCodPcrc as $key => $value) {
                  array_push($arrayCodigo, $value['cod_pcrc']);
                }
                // $txtCodPcrc = implode("', '", $arrayCodigo);
                $txtCodPcrc = $varcod_pcrc;
             }
          }else{
              // $txtCategoria = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and nombre in ('$txtVariables1') and programacategoria in ('$txtServicio')")->queryScalar();
              $txtCategoria = $varName;
              $varName2 = Yii::$app->db->createCommand("select distinct nombre from tbl_speech_categorias where anulado = 0 and idcategoria = $varName and cod_pcrc in ('$varcod_pcrc')")->queryScalar();
              $varName3 = Yii::$app->db->createCommand("select distinct tipoindicador from tbl_speech_categorias where anulado = 0 and idcategoria = $varName and cod_pcrc in ('$varcod_pcrc')")->queryScalar();
          }
        }


      return $this->render('indexvoice',[
              'txtCodParametrizar' => $txtCodParametrizar,
              'txtServicio' => $txtServicio,
              'txtFechaIni' => $txtFechaIni,
              'txtFechaFin' => $txtFechaFin,
              'txtParametros' => $txtParametros,
              'model' => $model,
              'model2' => $model2,
              'varVerificar' => $varVerificar,
              'txtIndicador' => $txtIndicador,              
              'txtCodpcrc' => $txtCodPcrc,
              'txtVariables1' => $txtVariables1,
              'txtCategoria' => $txtCategoria,
              'varName' => $varName,
              'varName2' => $varName2,
              'varName3' => $varName3,
              'txtCodPcrcok' => $txtCodPcrcok,
              ]);
    }

      public function actionImportarexcel2(){
        $model = new UploadForm2();
        $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");

          if (Yii::$app->request->isPost) {
              $model->file = UploadedFile::getInstance($model, 'file');

              if ($model->file && $model->validate()) {                
                  $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);
                  

                  $fila = 1;
                  if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
                    while (($datos = fgetcsv($gestor)) !== false) {
                      $numero = count($datos);
                  $fila++;
                  for ($c=0; $c < $numero; $c++) {
                      $varArray = $datos[$c]; 
                      $varDatos = explode(";", utf8_encode($varArray));

                  Yii::$app->db->createCommand()->insert('tbl_dashboardspeechcalls',[
                                          'callId' => $varDatos[0],
                                          'idcategoria' => $varDatos[1],
                                          'nombreCategoria' => $varDatos[2],
                                          'extension' => $varDatos[3],
                                          'login_id' => $varDatos[4],
                                          'fechallamada' => $varDatos[5],
                                          'callduracion' => $varDatos[6],
                                          'servicio' => $varDatos[7],
                                          'fechacreacion' => $txtfechacreacion,
                                          'anulado' => $txtanulado,
                                      ])->execute();                          
             
                  } 
            }
            fclose($gestor);

            return $this->redirect('index');
                  }
              }
          }

        return $this->renderAjax('importarexcel2',[
          'model' => $model,
          ]);

      }

     public function actionCategoriasvoice($arbol_idV, $parametros_idV, $codigoPCRC, $codparametrizar,  $nomFechaI, $nomFechaF){        
        $model = new Dashboardcategorias();       
        $varArbol_idV = $arbol_idV;
        $varParametros_idV = $parametros_idV;
        $varCodparametrizar = $codparametrizar;
        $varFechaI = $nomFechaI;
        $varFechaF = $nomFechaF;
        $varCodigPcrc = $codigoPCRC;

      return $this->renderAjax('categoriasvoice',[
          'model' => $model,
          'varArbol_idV' => $arbol_idV,
          'varParametros_idV' => $parametros_idV,
          'varCodparametrizar' => $codparametrizar,
          'varFechaI' => $varFechaI,
          'varFechaF' => $varFechaF,
          'varCodigPcrc' => $varCodigPcrc,
          ]);
      }

      public function actionCategoriasgeneral($arbol_idV, $parametros_idV, $codparametrizar, $codigoPCRC, $nomFechaI, $nomFechaF){
        $model = new Dashboardcategorias();       
        $varArbol_idV = $arbol_idV;
        $varParametros_idV = $parametros_idV;
        $varCodparametrizar = $codparametrizar;
        $varFechaI = $nomFechaI;
        $varFechaF = $nomFechaF;
        $varCodigPcrc = $codigoPCRC;

      return $this->renderAjax('categoriasgeneral',[
          'model' => $model,
          'varArbol_idV' => $arbol_idV,
          'varParametros_idV' => $parametros_idV,
          'varCodparametrizar' => $codparametrizar,
          'varFechaI' => $varFechaI,
          'varFechaF' => $varFechaF,
          'varCodigPcrc' => $varCodigPcrc,
          ]);
      } 

      public function actionListashijoss(){
        $txtvllamadas = Yii::$app->request->post("txtvllamadas");
        $txtvfechas = Yii::$app->request->post("txtvfechas");
        $varFechaInicio = null;
        $varFechaFin = null;
        $varconteoList = null;
        $varServicios = null;
        $varListServicio = null;


        if ($txtvllamadas != "" && $txtvfechas != "") {
          $varListServicio = Yii::$app->db->createCommand("select sc.programacategoria from tbl_speech_categorias sc   inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sp.id_dp_clientes = $txtvllamadas  and    sp.anulado = 0 group by programacategoria")->queryAll();
          $varArrayServicio = array();
          foreach ($varListServicio as $key => $value) {
            array_push($varArrayServicio, $value['programacategoria']);
          }
          $varServicios = implode("', '", $varArrayServicio);

          $varOne = substr($txtvfechas, 0, -13);
          $varTwo = substr($txtvfechas, 13);
          $varFechaInicio = $varTwo.' 05:00:00';

          $varFechaF = date('Y-m-d',strtotime($varTwo."+ 1 days"));
          $varFechaFin = $varFechaF.' 05:00:00';

          $varconteoList = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varServicios') and fechallamada between '$varFechaInicio' and '$varFechaFin'")->queryScalar();
        }else{
          $varconteoList = 0;
        }
        

        die(json_encode($varconteoList));
      }

      public function actionListashijos(){
        $txtvindicador = Yii::$app->request->post("txtvindicador");
        $txtvvariables = Yii::$app->request->post("txtvvariables");
        $txtvservicios = Yii::$app->request->post("txtvservicios");
        $txtvparametros = Yii::$app->request->post("txtvparametros");
        $txtvcodigos = Yii::$app->request->post("txtvcodigos");

        // $varListCodPcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_categorias where anulado = 0 and idcategorias = 1 and idcategoria = $txtvindicador  and programacategoria in ('$txtvservicios')")->queryAll();
        //       $arrayCodigo = array();                
        //       foreach ($varListCodPcrc as $key => $value) {
        //         array_push($arrayCodigo, $value['cod_pcrc']);
        //       }
        //       $txtCodPcrc = implode("', '", $arrayCodigo);  

        $txtidvariables = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categorias where anulado = 0 and idcategorias = 2  and cod_pcrc in ('$txtvcodigos') and programacategoria in ('$txtvservicios') and nombre like '$txtvvariables'")->queryScalar();

        die(json_encode($txtidvariables));
      }

      public function actionListashijo(){
	      die("ok");
              $txtvindicador = Yii::$app->request->post("txtvindicador");
              $txtvservicios = Yii::$app->request->post("txtvservicios");
              $txtvparametros = Yii::$app->request->post("txtvparametros");
              $txtvfechainic = Yii::$app->request->post("txtvfechainic");
              $txtvfechafinc = Yii::$app->request->post("txtvfechafinc");
              $txtvcodigo = Yii::$app->request->post("txtvcodigo");


              
              $txtIndicador = Yii::$app->db->createCommand("select distinct nombre from tbl_speech_categorias where anulado = 0 and idcategorias = 1 and idcategoria = $txtvindicador  and programacategoria in ('$txtvservicios')")->queryScalar();
              // $varListCodPcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_categorias where anulado = 0 and idcategorias = 1 and idcategoria = $txtvindicador  and programacategoria in ('$txtvservicios')")->queryAll();
              // $arrayCodigo = array();                
              // foreach ($varListCodPcrc as $key => $value) {
              //   array_push($arrayCodigo, $value['cod_pcrc']);
              // }
              // $txtCodPcrc = implode("', '", $arrayCodigo);              

              $txtRta = Yii::$app->db->createCommand("select distinct * from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and tipoindicador in ('$txtIndicador') and cod_pcrc in ('$txtvcodigo') and programacategoria in ('$txtvservicios')")->queryAll();       

            $arrayUsu = array();
            foreach ($txtRta as $key => $value) {
                array_push($arrayUsu, array("nombre"=>$value['nombre']));
            }

            die(json_encode($arrayUsu));
        }

        public function actionAsignararbol(){
          $model = new Dashboardservicios();

      $form = Yii::$app->request->post();

      if($model->load($form)){
        $txtIdServicio = $model->idservicios;
        var_dump($txtIdServicio);
        $txtIdArbol = $model->arbol_id;
        var_dump($txtIdArbol);

        Yii::$app->db->createCommand()->update('tbl_dashboardservicios',[
                                          'arbol_id' => $txtIdArbol,
                                          'idservicios' => $txtIdServicio,
                                      ],'iddashboardservicios ='.$txtIdServicio.'')->execute();   

        return $this->redirect('index');      
      }

          return $this->renderAjax('asignararbol',[
            'model' => $model,
            ]);
        }




        public function actionCategoriasconfig(){
          $model = new SpeechParametrizar();
          $sessiones = Yii::$app->user->identity->id; 

          $dataProvider = $model->ObtenerCategorias($sessiones);

          return $this->render('categoriasconfig',[
            'dataProvider' => $dataProvider,
            ]);
        }

        public function actionCategoriasoption(){
          $model = new Dashboardcategorias();
          $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");
        $txtVariableCliente = 0;
        $sessiones = Yii::$app->user->identity->id; 
                

          $form = Yii::$app->request->post();

          if ($model->load($form)) {
            $txtIdCategoria = $model->idcategoria;            
            $txtNombreCategoria = $model->nombre;           
            $txtTipoCategoria = $model->tipocategoria;            
            $txtTipoInidicador = $model->tipoindicador;           
            $txtClienteCategoria = $model->clientecategoria;            
            $txtCiudad = $model->ciudadcategoria;           
            $txtOrientacion = $model->orientacion;     
            $txtUsabilidad = $model->usabilidad;

            $txtServicio = Yii::$app->db->createCommand("select idservicios from tbl_dashboardservicios where clientecategoria like '%$txtClienteCategoria%' and anulado = 0")->queryScalar();

            Yii::$app->db->createCommand()->insert('tbl_dashboardcategorias',[
                                             'idcategoria' => $txtIdCategoria,
                                             'nombre' => $txtNombreCategoria,
                                             'tipocategoria' => $txtTipoCategoria,
                                             'tipoindicador' => $txtTipoInidicador,
                                             'clientecategoria' => $txtClienteCategoria,
                                             'ciudadcategoria' => $txtCiudad,
                                             'fechacreacion' => $txtfechacreacion,
                                             'anulado' => $txtanulado,
                                             'usua_id' => $sessiones,
                                             'orientacion' => $txtOrientacion,
                                             'usabilidad' => $txtUsabilidad,
                                             'iddashservicio' => $txtServicio,
                                         ])->execute(); 

            return $this->redirect('categoriasconfig');
          }

      

          return $this->renderAjax('categoriasoption',[
            'model' => $model,
            ]);
        }

        public function actionCategoriasverificar($txtServicioCategorias){
          $model = new SpeechCategorias();
          $sessiones = Yii::$app->user->identity->id;

          $varcod = $txtServicioCategorias;

          return $this->render('categoriasverificar',[
            'varcod' => $varcod,
            'model' => $model,
            ]);
        }


        public function actionCategoriasview($txtServicioCategorias){
          $model = new SpeechParametrizar();
          $sessiones = Yii::$app->user->identity->id;
          $txtarbolid = $txtServicioCategorias;

          $dataProvider = $model->ObtenerCategorias2($sessiones,$txtServicioCategorias);

          return $this->render('categoriasview',[
            'dataProvider' => $dataProvider,
            'model' => $model,
            'txtarbolid' => $txtarbolid,
            ]);
        }

        public function actionCategoriasupdate($txtServicioCategorias){
          $model = new Dashboardcategorias();
          $sessiones = Yii::$app->user->identity->id;

          $model = $this->findModel($txtServicioCategorias);
      if ($model->load(Yii::$app->request->post()) && $model->save()) {
          Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
          return $this->redirect('categoriasconfig');
      } else {
              return $this->render('categoriasupdate', [
                'model' => $model,
              ]);
      }

          if (Yii::$app->request->get('txtServicioCategorias')) {
            $id_params = Html::encode($_GET['txtServicioCategorias']);

            if ((int)$id_params) {
              $table = Dashboardcategorias::findOne($id_params);

              if ($table) {
                $model->iddashcategorias = $table->iddashcategorias;
                $model->idcategoria = $table->idcategoria;
                $model->nombre = $table->nombre;
              }else{                
                return $this->redirect('categoriasconfig');
              }
            }else{
              return $this->redirect('categoriasconfig');
            }
          }else{
            return $this->redirect('categoriasconfig');
          }

          return $this->render('categoriasupdate',[
            'model' => $model,
            ]);
        }

        protected function findModel($txtServicioCategorias){
          if (($model = Dashboardcategorias::findOne($txtServicioCategorias)) !== null) {
              return $model;
          } else {
              throw new NotFoundHttpException('The requested page does not exist.');
          }
      }

      public function actionCategoriasdelete($txtServicioCategorias){
        $model = $this->findModel2($txtServicioCategorias);

      if ($model == null) {
        throw new NotFoundHttpException('El registro no existe.'); 
      }
      else
      {
        $model->delete();
        return $this->redirect('categoriasconfig');
      }
      }
      protected function findModel2($txtServicioCategorias){
          if (($model = Dashboardcategorias::findOne($txtServicioCategorias)) !== null) {
              return $model;
          } else {
              throw new NotFoundHttpException('The requested page does not exist.');
          }
      }

      public function actionPrueba2(){
        $varClienteCategoria = Yii::$app->request->post("arbol_id");
        $varAnulado = 0;

        $NomCiudad  =  new Query;
        $NomCiudad  ->select(['tbl_arbols.arbol_id'])
                      ->from('tbl_arbols')
                      ->join('LEFT OUTER JOIN', 'tbl_dashboardservicios',
                            'tbl_arbols.id = tbl_dashboardservicios.arbol_id')                    
                      ->where("tbl_dashboardservicios.clientecategoria like '%$varClienteCategoria%'")
                      ->andwhere('tbl_arbols.activo = '.$varAnulado.'');
        $command = $NomCiudad->createCommand();
        $vartxtCity = $command->queryScalar();

        $txtCity = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$vartxtCity' and activo = 0")->queryScalar();

        die(json_encode($txtCity));
      }

      public function actionExport(){
        $model = new DashboardtmpSpeech();

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
        $varIndicador = Yii::$app->request->post("varIndicador");
        $varCodparametrizar = Yii::$app->request->post("varCodparametrizar");
        $varCorreo = Yii::$app->request->post("var_Destino");
        $VarCodsPcrc = Yii::$app->request->post("var_CodsPcrc");

        $sessiones = Yii::$app->user->identity->id;

        $varInicioF = $var_FechaIni.' 05:00:00';
        $varFecha = date('Y-m-d',strtotime($var_FechaFin."+ 1 days"));
        $varFinF = $varFecha.' 05:00:00';

        $fechaComoEntero = strtotime($varInicioF);
        $fechaIniCat = date("Y", $fechaComoEntero).'-01-01'; 
        $fechaFinCat = date("Y", $fechaComoEntero).'-12-31'; 

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

        $phpExc->getActiveSheet()->SetCellValue('A4',$arrayVariable);
        $phpExc->getActiveSheet()->getStyle('A4')->applyFromArray($styleArray); 
        $phpExc->setActiveSheetIndex(0)->mergeCells('A4:F4');

        $phpExc->getActiveSheet()->SetCellValue('A5','Fecha llamada');
        $phpExc->getActiveSheet()->getStyle('A5')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A5')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A5')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('A5')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('B5','Call-Id');
        $phpExc->getActiveSheet()->getStyle('B5')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('B5')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('B5')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('B5')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('C5','Parametros');
        $phpExc->getActiveSheet()->getStyle('C5')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C5')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C5')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('C5')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('D5','Duracion (Segundos)');
        $phpExc->getActiveSheet()->getStyle('D5')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D5')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D5')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('D5')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('E5','Codigo PCRC');
        $phpExc->getActiveSheet()->getStyle('E5')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E5')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E5')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('E5')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('F5','Usuarios de Red');
        $phpExc->getActiveSheet()->getStyle('F5')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('F5')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('F5')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('F5')->applyFromArray($styleArrayTitle);

        // if ($varCodigo == 1) {
        //   $varListCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where rn in ('$txtParametros')")->queryAll();
        // }else{
        //   if ($varCodigo == 2) {
        //     $varListCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where ext in ('$txtParametros')")->queryAll();
        //   }else{
        //     $varListCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where usuared in ('$txtParametros')")->queryAll();
        //   }
        // }
        // $varListArray = array();
        // foreach ($varListCod as $key => $value) {
        //   array_push($varListArray, $value['cod_pcrc']);
        // }
        // $txtcodigoCC = implode("', '", $varListArray);

        $txtcodigoCC = $VarCodsPcrc;

        $varListIndiVari = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2,3) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria order by idcategorias asc")->queryAll();

        $lastColumn = 'G'; 
        $numCell = 4;
        foreach ($varListIndiVari as $key => $value) {
          $varidCate = $value['idcategoria'];

          $varNumero = Yii::$app->db->createCommand("select orientacionsmart from tbl_speech_categorias where anulado = 0 and idcategoria  = $varidCate and cod_pcrc in ('$txtcodigoCC') and programacategoria in ('$txtServicio')")->queryScalar();

          if ($varNumero == 0) {
            $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varidCate.' - N/A'); 
          }else{
            if ($varNumero == 1) {
              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varidCate.' - Negativo'); 
            }else{
              if ($varNumero == 2) {
                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varidCate.' - Positivo'); 
              }
            }
          }
          $lastColumn++;
          
        }

        $lastColumn = 'G'; 
        $numCell = 5;
        foreach ($varListIndiVari as $key => $value) {
          $varidColor = $value['idcategoria'];

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
          
        }


        $numCell = $numCell + 1;

        $varListMetadata = Yii::$app->db->createCommand("select callid, extension, fechallamada, login_id  from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtParametros') and  fechallamada between '$varInicioF' and '$varFinF' group by callid, extension")->queryAll();

        foreach ($varListMetadata as $key => $value) {
          $txtCallid = $value['callid'];
          $txtExtensionid = $value['extension'];
          $txtFecha = $value['fechallamada'];
          
          
          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['fechallamada']); 
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

          $lastColumn = 'G';
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
                  }else{
                    $txtRtaIndicador = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and  callid = $txtCallid and idindicador in ('$arrayVariableMenos') and idvariable in ('$arrayVariableMenos')")->queryScalar();

                    if ($txtRtaIndicador == 0 || $txtRtaIndicador == null) {                            
                      $varConteo = 1;
                    }else{                            
                      $varConteo = 0;
                    }
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


              // $varConteo = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and callid = $txtCallid   and idcategoria = $varVariables")->queryScalar();


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
          }

          $numCell++;
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

      public function actionCategoriaspermisos(){
        $model = new Dashboardpermisos();
        $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");        
        $sessiones = Yii::$app->user->identity->id;                 

          $form = Yii::$app->request->post();

          if ($model->load($form)) {
            $txtServicio = $model->iddashservicio;
            $txtUsua_id = $model->usuaid;
            $txtNombre = $model->nombreservicio;

            Yii::$app->db->createCommand()->insert('tbl_dashboardpermisos',[
                                             'iddashservicio' => $txtServicio,
                                             'usuaid' => $txtUsua_id,
                                             'nombreservicio' => $txtNombre,
                                             'fechacreacion' => $txtfechacreacion,
                                             'anulado' => $txtanulado,
                                         ])->execute(); 

            return $this->redirect('categoriasconfig');
          }

        return $this->renderAjax('categoriaspermisos',[
          'model' => $model,
          ]);
      }

      public function actionPrueba3(){
        $varClienteCategoria = Yii::$app->request->post("arbol_id");
        $varAnulado = 0;

        $NomCiudad  =  new Query;
        $NomCiudad  ->select(['tbl_dashboardservicios.clientecategoria'])
                      ->from('tbl_dashboardservicios')
                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_dashboardservicios.arbol_id = tbl_arbols.id')                    
                      ->where("tbl_dashboardservicios.iddashboardservicios = '$varClienteCategoria'")
                      ->andwhere('tbl_arbols.activo = '.$varAnulado.'');
        $command = $NomCiudad->createCommand();
        $vartxtCity = $command->queryScalar();
        
        die(json_encode($vartxtCity));
      } 

    public function actionExport2(){
        $model = new DashboardtmpSpeech();

        $var_FechaIni = null;
        $var_FechaFin = null;
        $varCorreo = null;
        $varArbol_idV = null;
        $varParametros_idV = null;
        $varCodparametrizar = null;

        $var_FechaIni = Yii::$app->request->post("var_FechaIni");
        $var_FechaFin = Yii::$app->request->post("var_FechaFin");
        $txtServicio = Yii::$app->request->post("varArbol_idV");
        $txtParametros = Yii::$app->request->post("varParametros_idV");
        $varIndicador = Yii::$app->request->post("varIndicador");
        $varCodparametrizar = Yii::$app->request->post("varCodparametrizar");
        $varCorreo = Yii::$app->request->post("var_Destino");
        $txtCodPcrcok = Yii::$app->request->post("var_CodsPcrc");

        $sessiones = Yii::$app->user->identity->id;

        $varInicioF = $var_FechaIni.' 05:00:00';
        $varFecha = date('Y-m-d',strtotime($var_FechaFin."+ 1 days"));
        $varFinF = $varFecha.' 05:00:00';

        $fechaComoEntero = strtotime($varInicioF);
        $fechaIniCat = date("Y", $fechaComoEntero).'-01-01'; 
        $fechaFinCat = date("Y", $fechaComoEntero).'-12-31'; 

        $varCodigo = $varCodparametrizar;

        $varListIndicadores = "select distinct sc.idcategoria, sc.nombre, sc.tipoparametro, sc.orientacionsmart, sc.orientacionform, sc.programacategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1  and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio') ";

        $txtvDatosMotivos = "select distinct sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 3 and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio')";

        $txtlistDatas = "select distinct  sp.rn, sp.ext, sp.usuared, sp.comentarios, sc.programacategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sp.cod_pcrc = sc.cod_pcrc where sc.anulado = 0 and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio')";

        if ($varCodigo == 1) {
          $varServicio = Yii::$app->db->createCommand("select distinct a.name from tbl_arbols a inner join tbl_speech_servicios ss on a.id = ss.arbol_id inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where     sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.rn in ('$txtParametros')")->queryScalar();

          $idArbol = Yii::$app->db->createCommand("select distinct ss.arbol_id from tbl_speech_servicios ss   inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.rn in ('$txtParametros')")->queryScalar();

          $varListIndicadores = Yii::$app->db->createCommand($varListIndicadores." and sp.rn in ('$txtParametros')")->queryAll();
          $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.rn in ('$txtParametros')")->queryAll();
          $txtlistDatas = Yii::$app->db->createCommand($txtlistDatas." and sp.rn in ('$txtParametros')")->queryAll();
        }else{
          if ($varCodigo == 2) {
            $varServicio = Yii::$app->db->createCommand("select distinct a.name from tbl_arbols a inner join tbl_speech_servicios ss on a.id = ss.arbol_id inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.ext in ('$txtParametros')")->queryScalar();

            $idArbol = Yii::$app->db->createCommand("select distinct ss.arbol_id from tbl_speech_servicios ss   inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.ext in ('$txtParametros')")->queryScalar();

            $varListIndicadores = Yii::$app->db->createCommand($varListIndicadores." and sp.ext in ('$txtParametros')")->queryAll();
            $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.ext in ('$txtParametros')")->queryAll();
            $txtlistDatas = Yii::$app->db->createCommand($txtlistDatas." and sp.ext in ('$txtParametros')")->queryAll();
          }else{        
            $varServicio = Yii::$app->db->createCommand("select distinct a.name from tbl_arbols a inner join tbl_speech_servicios ss on a.id = ss.arbol_id inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where  sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.usuared in ('$txtParametros')")->queryScalar();

            $idArbol = Yii::$app->db->createCommand("select distinct ss.arbol_id from tbl_speech_servicios ss   inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.usuared in ('$txtParametros')")->queryScalar();

            $varListIndicadores = Yii::$app->db->createCommand($varListIndicadores." and sp.usuared in ('$txtParametros')")->queryAll();
            $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.usuared in ('$txtParametros')")->queryAll();
            $txtlistDatas = Yii::$app->db->createCommand($txtlistDatas." and sp.usuared in ('$txtParametros')")->queryAll();
          }
        }

        $txtIdCatagoria1 = 0;
        if ($fechaIniCat < '2020-01-01') {
          $txtIdCatagoria1 = 2681;
        }else{
          if ($idArbol == '17' || $idArbol == '8' || $idArbol == '105' || $idArbol == '2575' || $idArbol == '1371' || $idArbol == '2253' || $idArbol == '675') {
            $txtIdCatagoria1 = 1105;
          }else{
            $txtIdCatagoria1 = 1114;
          }
        }  

        $txtConteoIndicador = count($varListIndicadores); 

        $txtTotalLlamadas = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and idcategoria = $txtIdCatagoria1")->queryScalar();

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
                    'color' => array('rgb' => 'E3AD48'),
                )
            );

        $styleColorhigh = array( 
                'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => 'DD6D5B'),
                )
            );

        $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

        $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - QA MANAGEMENT');
        $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:J1');

        $phpExc->getActiveSheet()->SetCellValue('A2','INFORME DASHBOARD SPEECH - '.$varServicio.' -');
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySize);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A2:J2');

        $phpExc->getActiveSheet()->SetCellValue('A3','INFORMACION GENERAL');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A3:J3');
        $phpExc->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('A4','Cliente/Servicio');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A4:D4');
        $phpExc->getActiveSheet()->getStyle('A4')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A4')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A4')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('A4')->applyFromArray($styleArrayTitle);
        $phpExc->getActiveSheet()->setCellValue('A5', $varServicio);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A5:D5');

        $phpExc->getActiveSheet()->SetCellValue('E4','Rango de fechas');
        $phpExc->setActiveSheetIndex(0)->mergeCells('E4:G4');
        $phpExc->getActiveSheet()->getStyle('E4')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E4')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E4')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('E4')->applyFromArray($styleArrayTitle);
        $phpExc->getActiveSheet()->setCellValue('E5', $var_FechaIni.' - '.$var_FechaFin);
        $phpExc->setActiveSheetIndex(0)->mergeCells('E5:G5');

        $phpExc->getActiveSheet()->SetCellValue('H4','Cantidad de Llamadas');
        $phpExc->setActiveSheetIndex(0)->mergeCells('H4:J4');
        $phpExc->getActiveSheet()->getStyle('H4')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('H4')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('H4')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('H4')->applyFromArray($styleArrayTitle);
        $phpExc->getActiveSheet()->setCellValue('H5', $txtTotalLlamadas);
        $phpExc->setActiveSheetIndex(0)->mergeCells('H5:J5');

        $phpExc->getActiveSheet()->SetCellValue('A6','Programas Seleccionados');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A6:D6');
        $phpExc->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('E6','Parametros Seleccionados');
        $phpExc->setActiveSheetIndex(0)->mergeCells('E6:G6');
        $phpExc->getActiveSheet()->getStyle('E6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('H6','Cantidad de Llamadas');
        $phpExc->setActiveSheetIndex(0)->mergeCells('H6:J6');
        $phpExc->getActiveSheet()->getStyle('H6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleArrayTitle);
        
        $numCell = 6;
        foreach ($txtlistDatas as $key => $value) {
          $txtnombrePrograma = $value['programacategoria'];

          if ($varCodigo == 1) {            
            $txtnombreParametro = $value['rn'];
          }else{
            if ($varCodigo == 2) {
              $txtnombreParametro = $value['ext'];
            }else{
              $txtnombreParametro = $value['usuared'];
            }
          }
          $numCell++;
          $txtTotalLlamadas2 = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtnombrePrograma') and extension in ('$txtnombreParametro') and fechallamada between '$varInicioF' and '$varFinF' and idcategoria = $txtIdCatagoria1")->queryScalar();

          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $txtnombrePrograma);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':D'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $txtnombreParametro);
          $phpExc->setActiveSheetIndex(0)->mergeCells('E'.$numCell.':G'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $txtTotalLlamadas2);
          $phpExc->setActiveSheetIndex(0)->mergeCells('H'.$numCell.':J'.$numCell);
        }
        $numCell = $numCell + 1;

        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'GESTION GRAFICA');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':J'.$numCell);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);

        $lastColumn = 'A';
        $numCell = $numCell + 1;
        foreach ($varListIndicadores as $key => $value) {
          $txtIndicadores = $value['nombre'];          
          
          $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtIndicadores);           
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayTitle); 
          $lastColumn++;        
        }
        $numCell = $numCell + 1;
        
        $txtRtaProcentaje = 0;
        $lastColumn = 'A';
        foreach ($varListIndicadores as $key => $value) {
                // $varCodPcrc = $value['cod_pcrc'];
                $txtIdIndicadores = $value['idcategoria'];
// var_dump($txtIdIndicadores);
                $txtNombreCategoria = $value['nombre']; 
                $txtTipoSmart2 = $value['orientacionsmart']; 
                $txtTipoFormIndicador = $value['orientacionform'];
                $txtPrograma = $value['programacategoria']; 

                // $arrayvarCodPcrc = array();
                // $varListCod_Pcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_categorias where anulado = 0 and programacategoria in ('$txtServicio') and idcategorias = 1 and idcategoria = $txtIdIndicadores")->queryAll();

                // foreach ($varListCod_Pcrc as $key => $value) {
                //   array_push($arrayvarCodPcrc, $value['cod_pcrc']);
                // }
                $varCodPcrc = $txtCodPcrcok;
                  
                  if ($varCodigo == 1) {
                    // var_dump("RN");
                    $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.rn in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores'")->queryScalar();

                    $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.rn in ('$txtParametros')    and sc.cod_pcrc in ('$varCodPcrc') group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();

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
                    // var_dump($arrayVariableMenos);


                  }else{
                    if ($varCodigo == 2) {
                      // var_dump("Ext");
                      $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.ext in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores'")->queryScalar();

                      $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.ext in ('$txtParametros')  and sc.cod_pcrc in ('$varCodPcrc') group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();

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
                    }else{
                      // var_dump("UsuaRed");
                      $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.usuared in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores'")->queryScalar();

                      $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.usuared in ('$txtParametros')  and sc.cod_pcrc in ('$varCodPcrc') group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();

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

                    }
                  }

                  $varArrayInidicador = 0;
                  $varArrayPromedio = array();
                  if (count($varListVariables) != 0) {
                    // Tipo indicador Normal
                    if ($varTipoPAram == 2) {                  
                      // Cantidad variables positivas y negativas
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
                      
                        $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                        $varconteo = 0;
                        foreach ($varListCallid as $key => $value) {
                          $txtCallid = $value['callid'];

                          $varconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable) and idvariable in ($arrayVariable)")->queryScalar();

                          if ($varconteo == 0 || $varconteo == null) {
                            $txtRtaIndicador = 0;
                          }else{
                            $txtRtaIndicador = 1;
                          }

                          array_push($varArrayPromedio, $txtRtaIndicador);                          
                        }

                        $varArrayInidicador = array_sum($varArrayPromedio);
                      }else{
                      
                        $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                        foreach ($varListCallid as $key => $value) {
                          $txtCallid = $value['callid'];
                          
                          $varconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariableMenos) and idvariable in ($arrayVariableMenos)")->queryScalar();

                          if ($varconteo == 0 || $varconteo == null) {                            
                            $txtRtaIndicador = 1;
                          }else{                            
                            $txtRtaIndicador = 0;
                          }

                          array_push($varArrayPromedio, $txtRtaIndicador);                          
                        }
                        $varArrayInidicador = array_sum($varArrayPromedio);
                      }                      
                    }else{
                      // Tipo indicador Auditoria
                      if ($varTipoPAram == 1) {      
                        // Cantidad variables positivas y negativas
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
                          $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                          foreach ($varListCallid as $key => $value) {
                            $txtCallid = $value['callid'];

                            $varconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable) and idvariable in ($arrayVariable)")->queryScalar();

                            if ($varconteo == $varTotalvariables || $varconteo != null) {
                              $txtRtaIndicador = 1;
                            }else{
                              $txtRtaIndicador = 0;
                            }

                            array_push($varArrayPromedio, $txtRtaIndicador); 
                          }
                          $varArrayInidicador = array_sum($varArrayPromedio);
                        }else{
                          $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();                          

                          foreach ($varListCallid as $key => $value) {
                            $txtCallid = $value['callid'];
                            
                            $varconteomas = 0;
                            $varconteomeno = 0;


                            if ($arrayVariableMas != "") {
                              $varconteomas = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariableMas) and idvariable in ($arrayVariableMas)")->queryScalar();
                            }else{
                              $varconteomas = 0;
                            }
                            

                            if ($arrayVariableMenos != "") {
                              $varconteomeno = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariableMenos) and idvariable in ($arrayVariableMenos)")->queryScalar();
                            }else{
                              $varconteomeno = 0;
                            }
                            

                            if ($varconteomeno == null || $varconteomeno == 0 && $varconteomas == $varTotalvariables) {
                              $txtRtaIndicador = 1;
                            }else{
                              $txtRtaIndicador = 0;
                            }

                            array_push($varArrayPromedio, $txtRtaIndicador); 
                          }
                          $varArrayInidicador = array_sum($varArrayPromedio);
                          // var_dump($varArrayInidicador);
                        }
                      }
                    }
                  }else{
                    // Indicador Normal
                    if ($varTipoPAram == 2) {
                      
                      $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                      $varconteo = 0;
                      foreach ($varListCallid as $key => $value) {
                        $txtCallid = $value['callid'];

                        $varcantidadproceso = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid   and idcategoria = $txtIdIndicadores")->queryScalar();
                        // $varcantidadproceso = Yii::$app->db->createCommand("select cantproceso from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid")->queryScalar();
                        if ($varcantidadproceso == null) {
                          $varcantidadproceso = 0;
                        }

                        array_push($varArrayPromedio, $varcantidadproceso);
                      }

                      $varArrayInidicador = array_sum($varArrayPromedio);                      
                    }else{
                      // Indicador Auditoria
                      if ($varTipoPAram == 1) {
                        $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                        $varconteo = 0;
                        foreach ($varListCallid as $key => $value) {
                          $txtCallid = $value['callid'];

                          $varcantidadproceso = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid   and idcategoria = $txtIdIndicadores")->queryScalar();

                          // $varcantidadproceso = Yii::$app->db->createCommand("select cantproceso from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid")->queryScalar();

                          if ($varcantidadproceso == null) {
                            $varcantidadproceso = 0;
                          }

                          array_push($varArrayPromedio, $varcantidadproceso);
                        }

                        $varArrayInidicador = array_sum($varArrayPromedio);
                      }
                    }
                  }



                  if ($varArrayInidicador != 0) { 
                    if ($txtTipoFormIndicador == 0) {
                      // var_dump($varArrayInidicador);
                      $txtRtaProcentaje = (round(($varArrayInidicador / $txtTotalLlamadas) * 100, 1));
                    }else{
                      if ($txtTipoFormIndicador == 1) {
                        // var_dump("Hola Uno");
                        $txtRtaProcentaje = (100 - (round(($varArrayInidicador / $txtTotalLlamadas) * 100, 1)));
                      }                      
                    }     
                  }else{
                    $txtRtaProcentaje = 0;
                  }


          $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaProcentaje); 
          $lastColumn++;        
        }
        $numCell = $numCell + 1;

        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'INDICADORES POR VARIABLE');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':J'.$numCell);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
        $numCell = $numCell + 1;

        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Indicador');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':B'.$numCell);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'Variable');
        $phpExc->setActiveSheetIndex(0)->mergeCells('C'.$numCell.':D'.$numCell);
        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('E'.$numCell,'% de participacion');
        $phpExc->setActiveSheetIndex(0)->mergeCells('E'.$numCell.':F'.$numCell);
        $phpExc->getActiveSheet()->getStyle('E'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('E'.$numCell)->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('G'.$numCell,'Cantidad de llamadas');
        $phpExc->setActiveSheetIndex(0)->mergeCells('G'.$numCell.':H'.$numCell);
        $phpExc->getActiveSheet()->getStyle('G'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('G'.$numCell)->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('I'.$numCell,'Duracion (Segundos)');
        $phpExc->setActiveSheetIndex(0)->mergeCells('I'.$numCell.':J'.$numCell);
        $phpExc->getActiveSheet()->getStyle('I'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('I'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('I'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('I'.$numCell)->applyFromArray($styleArrayTitle);

        $numCell = $numCell + 1;
        if ($varCodigo == 1) {
            $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria, sc.tipoindicador from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.rn in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio') group by sc.nombre, sc.idcategoria order by sc.tipoindicador desc")->queryAll();  
        }else{
          if ($varCodigo == 2) {
            $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria, sc.tipoindicador from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.ext in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio') group by sc.nombre, sc.idcategoria order by sc.tipoindicador desc")->queryAll();  
          }else{
            $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria, sc.tipoindicador from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.usuared in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio') group by sc.nombre, sc.idcategoria order by sc.tipoindicador desc")->queryAll();
          
          }
        }

        foreach ($txtvDatos as $key => $value) {
          $txtCodigoPcrc = $value['cod_pcrc'];
          $txtVariables = $value['nombre'];
          $txtIdCatagoria = $value['idcategoria']; 
          $txtTipoindicador = $value['tipoindicador'];                

          $txtvCantVari = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls   where idcategoria = $txtIdCatagoria and servicio in ('$txtServicio') and extension in ('$txtParametros')  and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar(); 

          $txtvCantSeg = Yii::$app->db->createCommand("select AVG(callduracion) from tbl_dashboardspeechcalls   where idcategoria = $txtIdCatagoria and servicio in ('$txtServicio') and extension in ('$txtParametros')  and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar();

          $varListValidar  = null;
                if ($varCodigo == 1) {
                  $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and sp.rn in ('$txtParametros')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();                  
                }else{
                  if ($varCodigo == 2) {
                    $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and sp.ext in ('$txtParametros')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();                    
                  }else{
                    $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and sp.usuared in ('$txtParametros')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();
                  }
                }

                $txtParticipacion = 0;
                if ($txtvCantVari != 0 && $txtTotalLlamadas != 0) {
                  foreach ($varListValidar as $key => $value) {
                    $varSmart = $value['orientacionsmart'];
                    $varForm = $value['orientacionform'];

                    if ($varSmart ==  2 && $varForm == 0) {                      
                      $txtParticipacion = round(($txtvCantVari / $txtTotalLlamadas) * 100,2);
                    }else{
                      if ($varSmart ==  1 && $varForm == 1) {
                        $txtParticipacion = round(($txtvCantVari / $txtTotalLlamadas) * 100,2);
                      }else{
                        $txtParticipacion = (100 - (round(($txtvCantVari / $txtTotalLlamadas) * 100, 1)));
                        // $txtParticipacion = 1 - $txtParticipacion;

                      }
                    }
                  }
                }else{
                  $txtParticipacion = 0;
                }

          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $txtTipoindicador); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':B'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $txtVariables); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('C'.$numCell.':D'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $txtParticipacion.' %'); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('E'.$numCell.':F'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $txtvCantVari); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('G'.$numCell.':H'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('I'.$numCell, round($txtvCantSeg)); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('I'.$numCell.':J'.$numCell);
          $numCell++;
        }
        $numCell = $numCell + 1;

        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'CATEGORIAS POR LLAMADAS');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':J'.$numCell);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
        $numCell = $numCell + 1;

        $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Motivos de llamadas');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':B'.$numCell);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('C'.$numCell,'% de Llamadas');
        $phpExc->setActiveSheetIndex(0)->mergeCells('C'.$numCell.':D'.$numCell);
        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('C'.$numCell)->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('E'.$numCell,'Cantidad de llamadas');
        $phpExc->setActiveSheetIndex(0)->mergeCells('E'.$numCell.':F'.$numCell);
        $phpExc->getActiveSheet()->getStyle('E'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('E'.$numCell)->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('G'.$numCell,'Promedio de duracion');
        $phpExc->setActiveSheetIndex(0)->mergeCells('G'.$numCell.':H'.$numCell);
        $phpExc->getActiveSheet()->getStyle('G'.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G'.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G'.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle('G'.$numCell)->applyFromArray($styleArrayTitle);

        
        // if ($varCodigo == 1) {
        //   $varListCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where rn in ('$txtParametros')")->queryAll();
        // }else{
        //   if ($varCodigo == 2) {
        //     $varListCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where ext in ('$txtParametros')")->queryAll();
        //   }else{
        //     $varListCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where usuared in ('$txtParametros')")->queryAll();
        //   }
        // }
        // $varListArray = array();
        // foreach ($varListCod as $key => $value) {
        //   array_push($varListArray, $value['cod_pcrc']);
        // }
        // $txtcodigoCC = implode("', '", $varListArray);
        $txtcodigoCC = $txtCodPcrcok;

        $varListIndiVari = Yii::$app->db->createCommand("select idcategoria, nombre from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria")->queryAll();

        $lastColumn = 'I'; 
        foreach ($varListIndiVari as $key => $value) {
          
          $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['nombre']); 
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayTitle);
          $lastColumn++; 
        }
        $numCell = $numCell + 1;

        foreach ($txtvDatosMotivos as $key => $value) {
          $varMotivos = $value['nombre'];               
          $varIdCatagoria = $value['idcategoria'];

          $txtvCantMotivos1 = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls  where idcategoria = '$varIdCatagoria' and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar();
          $txtvCantMotivos = intval($txtvCantMotivos1);
                  // var_dump($varIdCatagoria);

                  if ($txtvCantMotivos != 0 && $txtTotalLlamadas != 0) {
                    $txtParticipación2 = round(($txtvCantMotivos / $txtTotalLlamadas) * 100);
                  }else{
                    $txtParticipación2 = 0;
                  } 

          $txtvCantSeg2 = Yii::$app->db->createCommand("select AVG(callduracion) from tbl_dashboardspeechcalls   where idcategoria = '$varIdCatagoria' and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar(); 

          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['nombre']); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':B'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $txtParticipación2.' %'); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('C'.$numCell.':D'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $txtvCantMotivos); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('E'.$numCell.':F'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, round($txtvCantSeg2)); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('G'.$numCell.':H'.$numCell);
          
          $lastColumn = 'I'; 
          foreach ($varListIndiVari as $key => $value) {
            $txtVarIndi = $value['idcategoria'];
            
            $txtcoincidencia1 = Yii::$app->db->createCommand("select callId from tbl_dashboardspeechcalls where idcategoria in ($varIdCatagoria, $txtVarIndi) and servicio in ('$txtServicio') and extension in ('$txtParametros')  and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0 group by callId HAVING COUNT(1) > 1")->queryAll();
            $txtcoincidencia = count($txtcoincidencia1);

            if ($txtcoincidencia != 0 && $txtvCantMotivos != 0 && $txtTotalLlamadas != 0) {                    
              $txtRtaVar = round(($txtcoincidencia / $txtvCantMotivos) * 100,2);
            }else{
              $txtRtaVar = 0;
              $txtRtaVariable = 0;
            }

            $varSmart = Yii::$app->db->createCommand("select orientacionsmart  from tbl_speech_categorias where anulado = 0 and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') and idcategoria = $txtVarIndi")->queryScalar();
            
            if ($varSmart == 1) {
              if ($txtRtaVar <= 10) {
                $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaVar.' %'); 
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorLess);
              }else{
                if ($txtRtaVar >= 20) {
                  $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaVar.' %'); 
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorhigh);
                }else{
                  $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaVar.' %'); 
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorMiddle);
                }
              }
            }else{
              if ($varSmart == 2) {
                if ($txtRtaVar <= 80) {
                  $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaVar.' %'); 
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorhigh);
                }else{
                  if ($txtRtaVar >= 90) {
                    $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaVar.' %'); 
                    $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorLess);
                  }else{
                    $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaVar.' %'); 
                    $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorMiddle);
                  }
              }
              }
            }            
            $lastColumn++;
          }
          $numCell++;

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

    public function actionSeleccionservicio(){
      $model = new ProcesosVolumendirector();

            $form = Yii::$app->request->post();
            if($model->load($form)){
                $txtCity = $model->ciudad;
                $txtDocument = $model->documento_director;

                return $this->redirect(array('registrarcategorias','txtCityP'=>$txtCity,'txtRadicadoP'=>$txtDocument));
            }

      return $this->renderAjax('seleccionservicio',[
        'model' => $model,
        ]);
    }

    public function actionExportarcategorias(){
          $model2 = new UploadForm2();
        $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");

          if (Yii::$app->request->isPost) {

              $model2->file = UploadedFile::getInstance($model2, 'file');

              if ($model2->file && $model2->validate()) {                
                  $model2->file->saveAs('categorias/' . $model2->file->baseName . '.' . $model2->file->extension);
                  

                  $fila = 1;
                  if (($gestor = fopen('categorias/' . $model2->file->baseName . '.' . $model2->file->extension, "r")) !== false) {
                    while (($datos = fgetcsv($gestor)) !== false) {
                      $numero = count($datos);
                  $fila++;
                  for ($c=0; $c < $numero; $c++) {
                      $varArray = $datos[$c]; 
                      $varDatos = explode(";", utf8_encode($varArray));

                      $varValidar = $varDatos[13];
                      $varUsuarios = Yii::$app->db->createCommand("select usua_id from tbl_usuarios where usua_usuario like '$varValidar'")->queryScalar();

                      $varPresentacion = 0;
                      if ($varDatos[12] == "Negativo") {
                        $varPresentacion = 1;
                      }else{
                        $varPresentacion = 0;
                      }

                      $varCategorias = 0;
                      if ($varDatos[7] == 'Programa') {
                        $varCategorias = 0;
                      }else{
                        if ($varDatos[7] == 'Indicador') {
                          $varCategorias = 1;
                        }else{
                          if ($varDatos[7] == 'Variable') {
                            $varCategorias = 2;
                          }else{
                            if ($varDatos[7] == 'Detalle motivo contacto') {
                              $varCategorias = 4;
                            }else{                              
                              $varCategorias = 3;                             
                            }
                          }
                        }
                      }

                      $varcity = Yii::$app->db->createCommand("select distinct ciudad from tbl_procesos_volumendirector where cod_pcrc like '$varDatos[1]'")->queryScalar();

                      $varCiudad = 0;
                      if ($varcity == 'BOGOTÁ') {
                        $varCiudad = 1;
                      }else{
                        $varCiudad = 2;
                      }

                      $varSmart = 0;
                      if ($varDatos[10] == '0') {
                        $varSmart = 0;
                      }else{
                        if ($varDatos[10] == 'Negativo') {
                          $varSmart = 1;
                        }else{
                          $varSmart = 2;
                        }
                      }

                      $varParam = 0;
                      if ($varDatos[11] == 'Estrategico') {
                        $varParam = 1;
                      }else{
                        if ($varDatos[11] == '0') {
                          $varParam = 0;
                        }else{
                          $varParam = 2;
                        }
                      }

                      $varClienteJarvis = Yii::$app->db->createCommand("select distinct cliente from tbl_procesos_volumendirector where cod_pcrc like '$varDatos[1]' and estado = 1 and anulado = 0 ")->queryScalar();
 

                Yii::$app->db->createCommand()->insert('tbl_speech_categorias',[
                          'pcrc' => $varDatos[0],
                          'cod_pcrc' => $varDatos[1],
                          'rn' => $varDatos[2],
                          'extension' => $varDatos[3],
                          'usua_usuario' => $varDatos[4],
                          'otros' => $varDatos[5],
                          'idcategoria' => $varDatos[6],
                          'nombre' => $varDatos[7],
                          'tipocategoria' => $varDatos[8],
                          'tipoindicador' => $varDatos[9],
                          'clientecategoria' => $varClienteJarvis,
                          'orientacionsmart' => $varSmart,
                          'tipoparametro' => $varParam,
                          'orientacionform' => $varPresentacion,
                          'usua_id' => $varUsuarios,
                          'usabilidad' => 1,
                          'idcategorias' => $varCategorias,
                          'idciudad' => $varCiudad,
                          'fechacreacion' => $txtfechacreacion,
                          'anulado' => $txtanulado,
                          'dashboard' => 1,
                          'programacategoria' => $varDatos[14],
                                       ])->execute();                                     
             
                  } 
            }           
            fclose($gestor);              

            return $this->redirect('categoriasconfig');
                  }
              }
          }

          return $this->renderAjax('exportarcategorias',[
            'model2' => $model2,
            ]);
          
        }

    public function actionRegistrarcategorias(){
      $model = new ProcesosVolumendirector();
      $model3 = new SpeechCategorias(); 

      $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");        
        $sessiones = Yii::$app->user->identity->id;                 

          $form = Yii::$app->request->post();

          if ($model3->load($form)) {
            $varIdPcrc = $model3->cod_pcrc;
            $varPcrc = Yii::$app->db->createCommand("select distinct pcrc from tbl_procesos_volumendirector where cod_pcrc like '$varIdPcrc' and estado = 1 and anulado = 0 ")->queryScalar();
            $varRn = $model3->rn;
            $varExt = $model3->extension;
            $varRed = $model3->usua_usuario;
            $varIdCategoria = $model3->idcategoria;
            $varNombre = $model3->nombre;
            $varIdCategorias = $model3->tipocategoria;
            $varTipoCategoria = null;
            if ($varIdCategorias == "0") {
              $varTipoCategoria = "Programa";
            }else{
              if ($varIdCategorias == "1") {
                $varTipoCategoria = "Indicador";
              }else{
                if ($varIdCategorias == "2") {
                  $varTipoCategoria = "Variable";
                }else{
                  if ($varIdCategorias == "3") {
                    $varTipoCategoria = "Motivos de contacto";
                  }else{
                    if ($varIdCategorias == "4") {
                      $varTipoCategoria = "Detalle motivo contacto";
                    }
                  }
                }
              }
            }

            $varTipoIndicador = $model3->tipoindicador;
            $varClienteJarvis = Yii::$app->db->createCommand("select distinct cliente from tbl_procesos_volumendirector where cod_pcrc like '$varIdPcrc' and estado = 1 and anulado = 0 ")->queryScalar();
            $varOri = $model3->orientacionsmart;
            if ($varOri == null) {
              $varOrientacionS = 0;
            }else{
              $varOrientacionS = $varOri;
            }
            $varParam = $model3->tipoparametro;
            if ($varParam == null) {
              $varParametro = 0;
            }else{
              $varParametro = $varParam;
            }
            $varOriF = $model3->orientacionform;
            if ($varOriF == null) {
              $varOrientacion = 0;
            }else{
              $varOrientacion = $varOriF;
            }
            $varDashboard = $model3->dashboard;
            $VarNomCity = Yii::$app->db->createCommand("select distinct ciudad from tbl_procesos_volumendirector where cod_pcrc = '$varIdPcrc' and estado = 1 and anulado = 0")->queryScalar();
            $varCiudad = 0;
        if ($VarNomCity == 'BOGOTÁ') {
          $varCiudad = 1;
        }else{
          $varCiudad = 2;
        }
        $varPC = $model3->programacategoria;
        if ($varPC == null) {
          $varPrograma = 0;
        }else{
          $varPrograma = $varPC;
        }


            Yii::$app->db->createCommand()->insert('tbl_speech_categorias',[
                                        'pcrc' => $varPcrc,
                          'cod_pcrc' => $varIdPcrc,
                          'rn' => null,
                          'extension' => null,
                          'usua_usuario' => null,
                          'idcategoria' => $varIdCategoria,
                          'nombre' => $varNombre,
                          'tipocategoria' => $varTipoCategoria,
                          'tipoindicador' => $varTipoIndicador,
                          'clientecategoria' => $varClienteJarvis,
                          'orientacionsmart' => $varOrientacionS,
                          'tipoparametro' => $varParametro,
                          'orientacionform' => $varOrientacion,
                          'usua_id' => $sessiones,
                          'usabilidad' => 1,
                          'idcategorias' => $varIdCategorias,
                          'idciudad' => $varCiudad,
                          'fechacreacion' => $txtfechacreacion,
                          'anulado' => $txtanulado,
                          'dashboard' => 1,
                          'programacategoria' => $varPrograma,
                                         ])->execute(); 

            return $this->redirect('categoriasconfig');
          }     

      
      return $this->render('registrarcategorias',[
        'model' => $model,
        'model3' => $model3,
        ]);
    }

    

    public function actionListasciudad(){            
            $txtAnulado = 0;

            if ($txtCiudad = Yii::$app->request->post('id')) {
                $txtControl = \app\models\ProcesosVolumendirector::find()->distinct()
                            ->where(['ciudad' => $txtCiudad])
                            ->count();            

                if ($txtControl > 0) {
                    $txtLitadoDirectores = \app\models\ProcesosDirectores::find()->distinct()
                                ->where(['ciudad' => $txtCiudad])
                                ->andwhere(['anulado' => $txtAnulado])
                                ->all();

                    foreach ($txtLitadoDirectores as $key => $value) {
                        echo "<option value='" . $value->iddirectores. "'>" . $value->director_programa . "</option>";
                    }
                }else{
                    echo "<option>-</option>";
                }
            }else{
                    echo "<option>No hay datos</option>";
                }

        }

    public function actionListarpcrc(){            
            $txtAnulado = 0; 
            $txtId = Yii::$app->request->post('id');           

            if ($txtId) {
                $txtControl = \app\models\ProcesosVolumendirector::find()->distinct()
                            ->where(['id_dp_clientes' => $txtId])
                            ->count();            

                if ($txtControl > 0) {
                  $varListaPcrc = \app\models\ProcesosVolumendirector::find()
                      ->select(['cod_pcrc','pcrc'])->distinct()
                            ->where(['id_dp_clientes' => $txtId])
                            ->andwhere("anulado = 0")
                            ->andwhere("estado = 1")                            
                            ->orderBy(['cod_pcrc' => SORT_DESC])
                            ->all();            

                    foreach ($varListaPcrc as $key => $value) {
                        echo "<option value='" . $value->cod_pcrc . "'>" . $value->cod_pcrc." - ".$value->pcrc . "</option>";
                    }
                }else{
                    echo "<option>-</option>";
                }
            }else{
                    echo "<option>No hay datos</option>";
            }

        }

        public function actionListarpcrcindex(){            
            $txtAnulado = 0; 
            $txtId = Yii::$app->request->post('id');                       

            if ($txtId) {
                $txtControl = \app\models\ProcesosVolumendirector::find()->distinct()
                            ->select(['tbl_procesos_volumendirector.cod_pcrc','tbl_procesos_volumendirector.pcrc'])->distinct()
                      ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_procesos_volumendirector.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                      ->join('LEFT OUTER JOIN', 'tbl_speech_categorias',
                                  'tbl_speech_parametrizar.cod_pcrc = tbl_speech_categorias.cod_pcrc')
                            ->where(['tbl_procesos_volumendirector.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_procesos_volumendirector.anulado = 0")
                            ->andwhere("tbl_procesos_volumendirector.estado = 1") 
                            ->andwhere("tbl_speech_categorias.anulado = 0")  
                            ->count();            

                if ($txtControl > 0) {
                  $varListaPcrc = \app\models\ProcesosVolumendirector::find()
                      ->select(['tbl_procesos_volumendirector.cod_pcrc','tbl_procesos_volumendirector.pcrc'])->distinct()
                      ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_procesos_volumendirector.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                      ->join('LEFT OUTER JOIN', 'tbl_speech_categorias',
                                  'tbl_speech_parametrizar.cod_pcrc = tbl_speech_categorias.cod_pcrc')
                            ->where(['tbl_speech_parametrizar.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_procesos_volumendirector.anulado = 0")
                            ->andwhere("tbl_procesos_volumendirector.estado = 1") 
                            ->andwhere("tbl_speech_categorias.anulado = 0")                             
                            ->orderBy(['tbl_procesos_volumendirector.cod_pcrc' => SORT_DESC])
                            ->all();            
                    $valor = 0;
                    
                    foreach ($varListaPcrc as $key => $value) {
                      $valor = $valor + 1; 
                      $nombre = "lista_";
                      $clase = "listach";
                        $nombre = $nombre.$valor;
              echo "<input type='checkbox' id= '".$nombre."' value='".$value->cod_pcrc."' class='".$clase."'>";
              echo "<label for = '".$value->cod_pcrc."'>&nbsp;&nbsp; ".$value->cod_pcrc." - ".$value->pcrc . "</label> <br>";
                        // echo "<input type='checkbox' value='" . $value->cod_pcrc . "'>" . $value->cod_pcrc." - ".$value->pcrc . "</option>";
                    }
                }else{
                    echo "<option>-</option>";
                }
            }else{
                    echo "<option>No hay datos</option>";
            }

        }

         public function actionListarprogramaindex(){            
           

            // $txttxtvmotivo = Yii::$app->request->post("txtvmotivo");
             $txtCodpcrc = Yii::$app->request->post('cod_pcrc');
            // $txtRta = Yii::$app->db->createCommand("select distinct programacategoria, rn, extension, usua_usuario from tbl_speech_categorias where anulado = 0 and cod_pcrc in('$txtCodpcrc')")->queryAll();
            $txtRta = Yii::$app->db->createCommand("select distinct sc.programacategoria, sp.rn, sp.ext, sp.usuared from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sp.cod_pcrc in ('$txtCodpcrc') and sp.anulado = 0")->queryAll();

            $arrayUsu = array();
            foreach ($txtRta as $key => $value) {
              if($value['rn']!=""){
                  array_push($arrayUsu, array("programacategoria"=>$value['programacategoria'],"rn"=>$value['rn']));
              }elseif($value['ext']!=""){
                  array_push($arrayUsu, array("programacategoria"=>$value['programacategoria'],"rn"=>$value['ext']));
              }elseif($value['usuared']!=""){
                  array_push($arrayUsu, array("programacategoria"=>$value['programacategoria'],"rn"=>$value['usuared']));
              }
            }

            die(json_encode($arrayUsu));    

        }
        public function actionParametrizardatos(){
          $model = new SpeechServicios;
      $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");        
        $sessiones = Yii::$app->user->identity->id;                 

          $form = Yii::$app->request->post();

          if ($model->load($form)) {
            $varArbol_id = $model->arbol_id;
            $varName = Yii::$app->db->createCommand("select distinct name from tbl_arbols where activo = 0 and id = '$varArbol_id'")->queryScalar();
            $varId_Cliente = $model->id_dp_clientes;
            $varCliente = Yii::$app->db->createCommand("select distinct cliente  from tbl_procesos_volumendirector where estado = 1 and anulado = 0 and id_dp_clientes = '$varId_Cliente'")->queryScalar();
            

            Yii::$app->db->createCommand()->insert('tbl_speech_servicios',[
                                             'arbol_id' => $varArbol_id,
                                             'nameArbol' => $varName,
                                             'id_dp_clientes' => $varId_Cliente,
                                             'cliente' => $varCliente,
                                             'cod_pcrc' => null,
                                             'pcrc' => null,
                                             'comentarios' => null,
                                             'usua_id' => $sessiones,
                                             'fechacreacion' => $txtfechacreacion,
                                             'anulado' => $txtanulado,
                                         ])->execute(); 

            return $this->redirect('categoriasconfig');
          }


          return $this->renderAjax('parametrizardatos',[
            'model' => $model,
            ]);
        }

        public function actionListacategorias(){
          $txtidCategoria = Yii::$app->request->post("txtCategoria");
          $txtidCentroCostos = Yii::$app->request->post("txtCC");

          $arrayUsu = array();
          
          $txtRta = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where cod_pcrc like '$txtidCentroCostos' and idcategorias = 1 and anulado = 0")->queryAll();

          foreach ($txtRta as $key => $value) {
            array_push($arrayUsu, array("nombre"=>$value['nombre']));
          }

          die(json_encode($arrayUsu));          
        }

        public function actionParametrizarcategorias(){
          $model = new SpeechParametrizar();

          $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");        
        $sessiones = Yii::$app->user->identity->id;                 

          $form = Yii::$app->request->post();

          if ($model->load($form)) {
            $varid_dp_clientes = $model->id_dp_clientes;
            $varcod_pcrc = $model->cod_pcrc;
            $varrn = $model->rn;
            $varext = $model->ext;
            $varusuared = $model->usuared;
            $varcomentarios = $model->comentarios;


            Yii::$app->db->createCommand()->insert('tbl_speech_parametrizar',[
                                             'id_dp_clientes' => $varid_dp_clientes,
                                             'cod_pcrc' => $varcod_pcrc,
                                             'rn' => $varrn,
                                             'ext' => $varext,
                                             'usuared' => $varusuared,
                                             'comentarios' => $varcomentarios,
                                             'usua_id' => $sessiones,
                                             'fechacreacion' => $txtfechacreacion,
                                             'anulado' => $txtanulado,
                                         ])->execute(); 

            return $this->redirect('categoriasconfig');
          }

          return $this->renderAjax('parametrizarcategorias',[
            'model' => $model,
            ]);
        }

        public function actionListaracciones(){
          $txtidCentroCostos = Yii::$app->request->post("txtCC");
                    
          $txtRta1 = Yii::$app->db->createCommand("select rn from tbl_speech_parametrizar where cod_pcrc like '$txtidCentroCostos' and anulado = 0")->queryAll();
          $txtRta2 = Yii::$app->db->createCommand("select ext from tbl_speech_parametrizar where cod_pcrc like '$txtidCentroCostos' and anulado = 0")->queryAll();
          $txtRta3 = Yii::$app->db->createCommand("select usuared from tbl_speech_parametrizar where cod_pcrc like '$txtidCentroCostos' and anulado = 0")->queryAll();          

          die(json_encode(array($txtRta1,$txtRta2,$txtRta3))); 
        }

        public function actionListaprograma(){
          $txtidCentroCostos = Yii::$app->request->post("txtCC");

          $varRta = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where cod_pcrc like '$txtidCentroCostos' and idcategorias = 0 and anulado = 0")->queryScalar();

          die(json_encode($varRta)); 
        }

        public function actionElegirprograma($varcod, $varfecha){
          $model2 = new ProcesosVolumendirector(); 

           /*$varCod_pcrc = Yii::$app->request->post("cod_pcrc");
            $varFechacreacion = Yii::$app->request->post("fechacreacion");*/
          
            var_dump($varcod);

             return $this->renderAjax('createelegirprograma',[
                 'model2'=>$model2,
                 //'cod_pcrc' => $varCod_pcrc,
                 //'fechacreacion' => $varFechacreacion,
                ]);

        }
         public function actioncreateelegirprograma(){
        }

        public function actionGenerarformula(){
          $model = new SpeechServicios();
          $txtanulado = 0;
          $txtfechacreacion = date("Y-m-d");
          $sessiones = Yii::$app->user->identity->id;                 

          $form = Yii::$app->request->post();

          if ($model->load($form)) {
            $varCliente = $model->arbol_id;
            $varMes = $model->comentarios;
            $varFechainicio = $varMes.' 05:00:00';
            //$varFechainicio = '2020-03-01 05:00:00';

            $varFechaF = date('Y-m-d',strtotime($varMes."+ 1 month"));
            $varFechaFin = $varFechaF.' 05:00:00';
            //$varFechaFin = '2020-03-06 05:00:00';

            $varListparams = Yii::$app->db->createCommand("select distinct a.id, sp.id_dp_clientes, sc.programacategoria, sp.rn, sp.ext, sp.usuared, sp.comentarios  from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc inner join tbl_speech_servicios ss on sp.id_dp_clientes = ss.id_dp_clientes inner join tbl_arbols a on ss.arbol_id = a.id where a.id = '$varCliente' and a.activo = 0 and sp.anulado = 0")->queryAll();

            if (count($varListparams) != 0) {
              $varArrayProgram = array();
              $varArrayparams = array();

              foreach ($varListparams as $key => $value) {
                array_push($varArrayProgram, $value['programacategoria']);
                array_push($varArrayparams, $value['rn'], $value['ext'], $value['usuared'], $value['comentarios']);
              }
              $txtSerivicios = implode("', '", $varArrayProgram);
              $txtExtensiones = implode("', '", $varArrayparams);


              $varContarDataSpeech = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtSerivicios') and extension in ('$txtExtensiones') and fechallamada between '$varFechainicio' and '$varFechaFin'")->queryAll();

              if ($varContarDataSpeech != 0) {
                $varValidacionGeneral = Yii::$app->db->createCommand("select  count(*) from tbl_speech_general where anulado = 0 and programacliente in ('$txtSerivicios') and extension in ('$txtExtensiones') and fechallamada between '$varFechainicio' and '$varFechaFin' order by callid desc")->queryScalar();    

                if ($varValidacionGeneral != 0) {
                  Yii::$app->db->createCommand("delete from tbl_speech_general where anulado = 0 and programacliente in ('$txtSerivicios') and extension in ('$txtExtensiones') and fechallamada between '$varFechainicio' and '$varFechaFin'")->execute();
                }

                $varListConteos = Yii::$app->db->createCommand("select llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria as llamacategoria, cate.idcategoria as catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) as encuentra, llama.nombreCategoria from tbl_dashboardspeechcalls llama left join (select idcategoria, tipoindicador, programacategoria, cod_pcrc from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and programacategoria in ('$txtSerivicios') order by cod_pcrc, tipoindicador) cate on llama.servicio = cate.programacategoria where   llama.servicio in ('$txtSerivicios') and llama.extension in ('$txtExtensiones') and llama.fechallamada between '$varFechainicio' and '$varFechaFin'  group by llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  order by encuentra desc ")->queryAll(); 

                foreach ($varListConteos as $key => $value) {
                  $varCallid = $value['callid'];
                  $varExt = $value['extension'];
                  $varFechacall = $value['fechallamada'];
                  $varServicio = $value['servicio'];
                  $varIndiCa = $value['llamacategoria'];
                  $varCategoria = $value['catecategoria'];
                  $varConteo = $value['encuentra'];
                  $varNombre = $value['nombreCategoria'];
                  
                  Yii::$app->db->createCommand()->insert('tbl_speech_general',[
                                                     'programacliente' => $varServicio,
                                                     'fechainicio' => $varFechainicio,
                                                     'fechafin' => null,
                                                     'callid' => $varCallid,
                                                     'fechallamada' => $varFechacall,
                                                     'extension' => $varExt,
                                                     'idindicador' => $varIndiCa,
                                                     'idvariable' => $varCategoria,
                                                     'cantproceso' => $varConteo,
                                                     'fechacreacion' => $txtfechacreacion,
                                                     'anulado' => $txtanulado,
                                                     'usua_id' => $sessiones,
                                                     'arbol_id' => $varCliente,
                                                  ])->execute();
                }
              }

              
              return $this->redirect('categoriasconfig');
            }
          }

          return $this->renderAjax('generarformula',[
              'model' => $model,
            ]);
        }

        public function actionCategoriasida($txtServicioCategorias) {
          $model = new SpeechCategorias();
          $txtCodPcrc = $txtServicioCategorias;          

          $txtidcliente = Yii::$app->db->createCommand("select distinct id_dp_clientes from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$txtCodPcrc')")->queryScalar(); 

          return $this->render('categoriasida',[
              'txtCodPcrc' => $txtCodPcrc,
              'txtidcliente' => $txtidcliente,
              'model' => $model,
            ]);
        }

        public function actionIngresardashboard() {
          $txtvardash = Yii::$app->request->post("vardash");
          $txtvarcont = Yii::$app->request->post("varcont");

          $resultado = intval(preg_replace('/[^0-9]+/', '', $txtvardash), 10);
          $resultadol = substr($txtvardash, -1, 1); 
          $txtvarcont = 0;
          if ($resultadol == "A") {
            $txtvarcont = 1;
          }else{
            if ($resultadol == "B") {
              $txtvarcont = 2;
            }else{
              if ($resultadol == "C") {
                $txtvarcont = 3;
              }
            }
          }

          Yii::$app->db->createCommand()->update('tbl_speech_categorias',[
                                          'dashboard' => $txtvarcont,
                                      ],'idspeechcategoria ='.$resultado.'')->execute();
        
          $varconteoLists = 0;

          die(json_encode($varconteoLists));

        }

        public function actionCategoriasparametros($arbol_idV) {
          $model = new SpeechParametrizar();
          $txtServid = $arbol_idV;

           return $this->renderAjax('categoriasparametros',[
            'model' => $model,
            'txtServid' => $txtServid,
            ]);
        }

        public function actionModificardashboard() {
          $txtvardash = Yii::$app->request->post("vardash");
          $txtvarcont = Yii::$app->request->post("varcont");

          $resultado = intval(preg_replace('/[^0-9]+/', '', $txtvardash), 10);
          $resultadol = substr($txtvardash, -2, 2); 

          $varconteoList = 0;
          if ($resultadol == "rn") {
            Yii::$app->db->createCommand()->update('tbl_speech_parametrizar',[
                                          'rn' => $txtvarcont,
                                          'ext' => "",
                                          'usuared' => "",
                                          'comentarios' => "",
                                      ],'idspeechparametrizar ='.$resultado.'')->execute();            
          }else{
            if ($resultadol == "ex") {
              Yii::$app->db->createCommand()->update('tbl_speech_parametrizar',[
                                          'rn' => "",
                                          'ext' => $txtvarcont,
                                          'usuared' => "",
                                          'comentarios' => "",
                                      ],'idspeechparametrizar ='.$resultado.'')->execute();
            }else{
              if ($resultadol == "us") {
                Yii::$app->db->createCommand()->update('tbl_speech_parametrizar',[
                                          'rn' => "",
                                          'ext' => "",
                                          'usuared' => $txtvarcont,
                                          'comentarios' => "",
                                      ],'idspeechparametrizar ='.$resultado.'')->execute();
              }else{
                if ($resultadol == "ot") {
                  Yii::$app->db->createCommand()->update('tbl_speech_parametrizar',[
                                          'rn' => "",
                                          'ext' => "",
                                          'usuared' => "",
                                          'comentarios' => $txtvarcont,
                                      ],'idspeechparametrizar ='.$resultado.'')->execute();
                }else{
                  if ($resultadol == "na") {
                    Yii::$app->db->createCommand()->update('tbl_speech_parametrizar',[
                                          'anulado' => 1,
                                      ],'idspeechparametrizar ='.$resultado.'')->execute();
                  }
                }
              }
            }
          }         

          die(json_encode($varconteoList));

        }

        public function actionGraficatmo($llamadastotal, $arbol_idV, $parametros_idV, $codparametrizar, $codigoPCRC, $nomFechaI, $nomFechaF) {
          $model = new SpeechCategorias();

          $varCantidadllamadas = $llamadastotal;
          $varArbol_idV = $arbol_idV;
          $varParametros_idV = $parametros_idV;
          $varCodparametrizar = $codparametrizar;
          $varFechaI = $nomFechaI;
          $varFechaF = $nomFechaF;
          $varCodigPcrc = $codigoPCRC;

          return $this->renderAjax('graficatmo',[
            'varCantidadllamadas' => $varCantidadllamadas,
            'varArbol_idV' => $varArbol_idV,
            'varParametros_idV' => $varParametros_idV,
            'varCodparametrizar' => $varCodparametrizar,
            'varFechaI' => $varFechaI,
            'varFechaF' => $varFechaF,
            'varCodigPcrc' => $varCodigPcrc,
            ]);
        }


  }

?>
