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
use app\models\Dashboardspeechcalls;
use app\models\Formularios;
use \yii\base\Exception;

  class DashboardspeechdosController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['prueba', 'importarexcel', 'indexvoice', 'mportarexcel2','categoriasvoice','listashijo','categoriasgeneral','asignararbol','categoriasconfig','categoriasoption','categoriasview','categoriasupdate','categoriasdelete','export','categoriaspermisos','export2','seleccionservicio','registrarcategorias','listacategorias','exportarcategorias','parametrizarcategorias','listaracciones','categoriasverificar', 'elegirprograma','generarformula','listashijos','listashijoss','categoriasida','ingresardashboard','categoriashalla','ingresarhallazgo','categoriasdefinicion','ingresardefinicion','marcacionpcrc','categoriasentto','importarentto','cantidadentto','automaticspeecha','searchllamadas','viewcalls','totalagente', 'totalizaragentes','llamadafocalizada'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo()  || Yii::$app->user->identity->isVerexterno();
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
      $model = new Dashboardcategorias(); 
      $model2 = new ProcesosVolumendirector();
      $model3 = new SpeechCategorias();
      $txtanulado = 0;
      $txtfechacreacion = date("Y-m-d");
      $array_cadena = 0; 
      $txtvarNew = null; 

      $data = Yii::$app->request->post();     
      if ($model3->load($data)) {

        $varIdDpCliente = $model3->clientecategoria;
        $varListaCodPcrc = explode(",", str_replace(array("#", "'", ";", " "), '', $model3->cod_pcrc));
        $varTipoDashboard = $model3->idciudad;
        $varListaExtensiones = explode(",", str_replace(array("#", "'", ";", " "), '', $model3->pcrc));
        $varFechaIdeal = explode(" ", $model3->fechacreacion);

        $varFechaInicioTres = $varFechaIdeal[0];
        $varFechaFinTres = date('Y-m-d',strtotime($varFechaIdeal[2]."+ 1 days"));

        $varGeneralLlamada = (new \yii\db\Query())
                              ->select(['idllamada'])
                              ->from(['tbl_speech_servicios'])            
                              ->where(['=','id_dp_clientes',$varIdDpCliente])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['idllamada'])
                              ->Scalar();

        $varServicios = (new \yii\db\Query())
                              ->select(['programacategoria'])
                              ->from(['tbl_speech_categorias'])            
                              ->where(['in','cod_pcrc',$varListaCodPcrc])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['programacategoria'])
                              ->Scalar();

        $varCantidadLlamada = (new \yii\db\Query())
                                ->select(['callId'])
                                ->from(['tbl_dashboardspeechcalls'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varServicios])
                                ->andwhere(['between','fechallamada',$varFechaInicioTres.' 05:00:00',$varFechaFinTres .' 05:00:00'])
                                ->andwhere(['=','idcategoria',$varGeneralLlamada])
                                ->andwhere(['in','extension',$varListaExtensiones])
                                ->count();        
        
        if ($varCantidadLlamada != 0) {

          return $this->redirect(array('indexvoice','txtidcliente'=>$varIdDpCliente,'txtGeneral'=>$varGeneralLlamada,'txtServicio'=>$varServicios,'txtFechas'=>$model3->fechacreacion,'txtExtensiones'=>$model3->pcrc,'txtTipodash'=>$varTipoDashboard,'txtCantidad'=>$varCantidadLlamada,'txtCodPcrcs'=>$model3->cod_pcrc));
          
        }else{
          $txtvarNew = 1;
        }        

      }

      return $this->render('index',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'txtvarNew' => $txtvarNew,
      ]);
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

      public function actionIndexvoice($txtidcliente,$txtGeneral,$txtServicio,$txtFechas,$txtExtensiones,$txtTipodash,$txtCantidad,$txtCodPcrcs){
        $model = new SpeechCategorias();
      $model2 = new SpeechCategorias();
      $model3 = new SpeechCategorias();
      $varIndicadorVariableVoice = null;
      $varVariableMotivo = null;
      $varListAsesores = null;
      $varIndicadorVariableNombreVoice = null;
      $varVariableMotivoNombre = 'Variable';
      $varVariableAsesorNombre = null;
      $varVariableAsesor = null;

      $txtTipoparametros = $txtTipodash;

      $varidLllamada = $txtGeneral;

      $varNombreServicioVoice = (new \yii\db\Query())
                              ->select(['nameArbol'])
                              ->from(['tbl_speech_servicios'])            
                              ->where(['=','id_dp_clientes',$txtidcliente])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['nameArbol'])
                              ->Scalar();

      if ($txtidcliente == '255' || $txtidcliente == '262' || $txtidcliente == '310') {
        $txtServicioImg = $txtServicio.'_'.$txtidcliente;
      }else{
        $txtServicioImg = $txtServicio;
      }

      $varListaCodPcrcVoice = explode(",", str_replace(array("#", "'", ";", " "), '', $txtCodPcrcs));

      $varNombrePcrcVoice =   (new \yii\db\Query())
                              ->select(['CONCAT(cod_pcrc," - ",pcrc) as NombrePcrc'])
                              ->from(['tbl_proceso_cliente_centrocosto'])            
                              ->where(['in','cod_pcrc',$varListaCodPcrcVoice])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->Scalar();                      

      $varFechaIdealVoice = explode(" ", $txtFechas);

      $varFechaInicioVoice = $varFechaIdealVoice[0];
      $varFechaFinTresVoice = date('Y-m-d',strtotime($varFechaIdealVoice[1]."+ 1 days"));

      $varListaExtensionesVoice = explode(",", str_replace(array("#", "'", ";", " "), '', $txtExtensiones));

       

      $varListCallidsVoice = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$txtServicio])
                                ->andwhere(['in','extension',$varListaExtensionesVoice])
                                ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                ->groupby(['callid'])
                                ->all();

      $varArrayCallidV = array();
      foreach ($varListCallidsVoice as $key => $value) {
        array_push($varArrayCallidV, $value['callid']);
      }
      $varCallidsListV = implode(", ", $varArrayCallidV);
      $arrayCallids_downV = str_replace(array("#", "'", ";", " "), '', $varCallidsListV);
      $varCallidsIndicadoresVoice = explode(",", $arrayCallids_downV);

      $varListarIndicadoresVoice = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','idcategorias',1])
                                ->all();

      $data = Yii::$app->request->post();     
      if ($model->load($data)) {
        $varIndicadorVariableVoice = $model->idcategoria;
        
        $varIndicadorVariableNombreVoice = (new \yii\db\Query())
                                ->select(['nombre'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','idcategorias',1])
                                ->andwhere(['=','idcategoria',$varIndicadorVariableVoice])
                                ->groupby(['nombre'])
                                ->Scalar();
      }
      if ($model2->load($data)) {
        $varVariableMotivo = $model2->cod_pcrc;

        $varVariableMotivoNombre = (new \yii\db\Query())
                                ->select(['nombre'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','idcategoria',$varVariableMotivo])
                                ->groupby(['nombre'])
                                ->Scalar();
      }

      $varListarMotivosVoice = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','idcategorias',3])
                                ->all(); 

      $varArrayMotivosV = array();
      foreach ($varListarMotivosVoice as $key => $value) {
        array_push($varArrayMotivosV, $value['idcategoria']);
      }
      $varMotivosListV = implode(", ", $varArrayMotivosV);
      $arrayMotivos_downV = str_replace(array("#", "'", ";", " "), '', $varMotivosListV);
      $varMotivosVoice = explode(",", $arrayMotivos_downV);

      $varListCallidsVoice = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$txtServicio])
                                ->andwhere(['in','extension',$varListaExtensionesVoice])
                                ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                ->andwhere(['in','idcategoria',$varMotivosVoice])
                                ->groupby(['callid'])                                
                                ->count();

      $varVerificarCantidadLlamadas = (new \yii\db\Query())
                                ->select(['cantidadllamada'])
                                ->from(['tbl_speech_tmpmotivos'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','id_dp_cliente',$txtidcliente])
                                ->andwhere(['=','extension',$txtTipoparametros])
                                ->andwhere(['>=','fechainiciotmp',$varFechaInicioVoice])
                                ->andwhere(['<=','fechafintmp',$varFechaFinTresVoice])
                                ->groupby(['cantidadllamada'])
                                ->Scalar(); 

      if ($varVerificarCantidadLlamadas != $varListCallidsVoice) {

        Yii::$app->db->createCommand('DELETE FROM tbl_speech_tmpmotivos WHERE cod_pcrc=:id AND extension=:idparams AND fechainiciotmp >= :idfechaini AND fechafintmp <= :idfechafin')->bindParam(':id',$txtCodPcrcs)->bindParam(':idparams',$txtTipoparametros)->bindParam(':idfechaini',$varFechaInicioVoice)->bindParam(':idfechafin',$varFechaFinTresVoice)->execute();

        foreach ($varListarMotivosVoice as $key => $value) {
          $varIdMotivoVoice = $value['idcategoria'];
          $varNombreMotivoVoice = $value['nombre'];
          $varMotivoIdVoice = intval($varIdMotivoVoice);

          $varConteoPorMotivosVoice =  (new \yii\db\Query())
                                    ->select(['callid'])
                                    ->from(['tbl_dashboardspeechcalls'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','servicio',$txtServicio])
                                    ->andwhere(['in','extension',$varListaExtensionesVoice])
                                    ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                    ->andwhere(['=','idcategoria',$varMotivoIdVoice])
                                    ->groupby(['callid'])
                                    ->count();

          $varDuracionLlamadaVoice = (new \yii\db\Query())
                                    ->select(['AVG(callduracion)'])
                                    ->from(['tbl_dashboardspeechcalls'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','servicio',$txtServicio])
                                    ->andwhere(['in','extension',$varListaExtensionesVoice])
                                    ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                    ->andwhere(['=','idcategoria',$varMotivoIdVoice])
                                    ->groupby(['callid'])
                                    ->scalar();

          if ($varConteoPorMotivosVoice != 0 && $txtCantidad != 0) {
            if ($varConteoPorMotivosVoice != null) {
              $txtRtaPorcentajeMotivo = (round(($varConteoPorMotivosVoice / $txtCantidad) * 100, 1));
            }else{
              $txtRtaPorcentajeMotivo = 0;
            }
          }else{
            $txtRtaPorcentajeMotivo = 0;
          }

          Yii::$app->db->createCommand()->insert('tbl_speech_tmpmotivos',[
                      'id_dp_cliente' => $txtidcliente,
                      'cod_pcrc' => $txtCodPcrcs,
                      'extension' => $txtTipoparametros,
                      'id_motivo' => $varMotivoIdVoice,
                      'motivo' => $varNombreMotivoVoice,
                      'porcentaje' => $txtRtaPorcentajeMotivo,
                      'cantidadmotivos' => $varConteoPorMotivosVoice,
                      'duracionllamada' => $varDuracionLlamadaVoice,
                      'cantidadllamada' => $varListCallidsVoice,
                      'fechainiciotmp' => $varFechaInicioVoice,
                      'fechafintmp' => $varFechaFinTresVoice,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
            ])->execute(); 

          $varListarCategoriasVoice = (new \yii\db\Query())
                                      ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                      ->from(['tbl_speech_categorias'])            
                                      ->where(['=','anulado',0])
                                      ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                      ->andwhere(['in','idcategorias',[1,2]])
                                      ->all(); 

        }
      }
      
      $varlistaMotivosTmp = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_speech_tmpmotivos'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','id_dp_cliente',$txtidcliente])
                                ->andwhere(['=','cantidadllamada',$varListCallidsVoice])
                                ->andwhere(['=','extension',$txtTipoparametros])
                                ->andwhere(['>=','fechainiciotmp',$varFechaInicioVoice])
                                ->andwhere(['<=','fechafintmp',$varFechaFinTresVoice])
                                ->All(); 

      if ($model3->load($data)) {
        $varVariableAsesor = intval($model2->usua_id);

        $varVariableAsesorNombre = (new \yii\db\Query())
                                ->select(['nombre'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcVoice])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','idcategoria',$varVariableAsesor])
                                ->groupby(['nombre'])
                                ->Scalar();

        $varListAsesores =  (new \yii\db\Query())
                                    ->select(['login_id','count(callid) as cantidad'])
                                    ->from(['tbl_dashboardspeechcalls'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','servicio',$txtServicio])
                                    ->andwhere(['in','extension',$varListaExtensionesVoice])
                                    ->andwhere(['between','fechallamada',$varFechaInicioVoice.' 05:00:00',$varFechaFinTresVoice.' 05:00:00'])
                                    ->andwhere(['=','idcategoria',$varVariableAsesor])
                                    ->groupby(['login_id'])
                                    ->all();
      }

      return $this->render('indexvoice',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'varNombreServicioVoice' => $varNombreServicioVoice,
        'varNombrePcrcVoice' => $varNombrePcrcVoice,
        'txtCantidad' => $txtCantidad,
        'txtServicio' => $txtServicio,
        'txtFechas' => $txtFechas,
        'txtExtensiones' => $txtExtensiones,
        'varCallidsIndicadoresVoice' => $varCallidsIndicadoresVoice,
        'varListarIndicadoresVoice' => $varListarIndicadoresVoice,
        'varListaCodPcrcVoice' => $varListaCodPcrcVoice,
        'varFechaInicioVoice' => $varFechaInicioVoice,
        'varFechaFinTresVoice' => $varFechaFinTresVoice,
        'txtServicioImg' => $txtServicioImg,
        'varListaExtensionesVoice' => $varListaExtensionesVoice,
        'varIndicadorVariableVoice' => $varIndicadorVariableVoice,
        'varListarMotivosVoice' => $varListarMotivosVoice,
        'txtCodPcrcs' => $txtCodPcrcs,
        'varVariableMotivo' => $varVariableMotivo,
        'varlistaMotivosTmp' => $varlistaMotivosTmp,
        'varIndicadorVariableNombreVoice' => $varIndicadorVariableNombreVoice,
        'varVariableMotivoNombre' => $varVariableMotivoNombre,
        'varVariableAsesorNombre' => $varVariableAsesorNombre,
        'varVariableAsesor' => $varVariableAsesor,
        'varListAsesores' => $varListAsesores,
        'varidLllamada' => $varidLllamada,
        'txtidcliente' => $txtidcliente,
        'txtTipoparametros' => $txtTipoparametros,
      ]);
    }

    public function actionListarvariables(){
      $txtanulado = 0;
      $txtid = Yii::$app->request->get('id');

      if ($txtid) {
        $txtControl = (new \yii\db\Query())
                                ->select(['idspeechcategoria'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','idspeechcategoria',$txtid])
                                ->count(); 

        if ($txtControl > 0) {
          $varIndicadorNombre = (new \yii\db\Query())
                                ->select(['nombre'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','idspeechcategoria',$txtid])
                                ->scalar(); 

          $varCentroCostos = (new \yii\db\Query())
                                ->select(['cod_pcrc'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','idspeechcategoria',$txtid])
                                ->scalar(); 
          
          $varListaVariable = (new \yii\db\Query())
                                ->select(['idcategoria','nombre'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','tipoindicador',$varIndicadorNombre])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','cod_pcrc',$varCentroCostos])
                                ->all();

              echo "<option value='' disabled selected>Seleccionar Variable...</option>";
              foreach ($varListaVariable as $key => $value) {
                echo "<option value='" . $value['idcategoria']. "'>" . $value['nombre']. "</option>";
              }
            }else{
              echo "<option>--</option>";
            }
          }else{
            echo "<option>Seleccionar...</option>";
          }          
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
                                          'fechareal' => $varDatos[8],
                                          'idredbox'  => $varDatos[9],
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

      public function actionCategoriasvoice($arbol_idV, $parametros_idV, $codigoPCRC, $codparametrizar,  $nomFechaI, $nomFechaF,$indicador){        
        $model = new Dashboardcategorias();       
        $varArbol_idV = $arbol_idV;
        $varParametros_idV = $parametros_idV;
        $varCodparametrizar = $codparametrizar;
        $varFechaI = $nomFechaI;
        $varFechaF = $nomFechaF;
        $varCodigPcrc = $codigoPCRC;
        $varExensionParams = $indicador;

      return $this->renderAjax('categoriasvoice',[
          'model' => $model,
          'varArbol_idV' => $arbol_idV,
          'varParametros_idV' => $parametros_idV,
          'varCodparametrizar' => $codparametrizar,
          'varFechaI' => $varFechaI,
          'varFechaF' => $varFechaF,
          'varCodigPcrc' => $varCodigPcrc,
          'varExensionParams' => $varExensionParams,
          ]);
      }

      public function actionCategoriasgeneral($arbol_idV, $parametros_idV, $codparametrizar, $codigoPCRC, $nomFechaI, $nomFechaF){
        $model = new Dashboardcategorias();       
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
        $txtvvariables = Yii::$app->request->get("txtvvariables");
        $txtvservicios = Yii::$app->request->get("txtvservicios");
        $txtvcodigos = Yii::$app->request->get("txtvcodigos");


        $txtidvariables = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categorias where anulado = 0 and idcategorias = 2  and cod_pcrc in ('$txtvcodigos') and programacategoria in ('$txtvservicios') and nombre like '$txtvvariables'")->queryScalar();

        die(json_encode($txtidvariables));
      }

      public function actionListashijo(){
              $txtvindicador = Yii::$app->request->get("txtvindicador");
              $txtvservicios = Yii::$app->request->get("txtvservicios");
              $txtvcodigo = Yii::$app->request->get("txtvcodigo");


              
              $txtIndicador = Yii::$app->db->createCommand("select distinct nombre from tbl_speech_categorias where anulado = 0 and idcategorias = 1 and idcategoria = $txtvindicador  and programacategoria in ('$txtvservicios')")->queryScalar();
                         

              $txtRta = Yii::$app->db->createCommand("select distinct * from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and tipoindicador in ('$txtIndicador') and cod_pcrc in ('$txtvcodigo') and programacategoria in ('$txtvservicios')")->queryAll();       

            $arrayUsu = array();
            foreach ($txtRta as $key => $value) {
                array_push($arrayUsu, array("nombre"=>$value['nombre']));
            }

            die(json_encode($arrayUsu));
        }

  public function actionListashijo1(){
          $txtvindicador = Yii::$app->request->get("txtvindicador");
          $txtvservicios = Yii::$app->request->get("txtvservicios");
          $txtvcodigo = Yii::$app->request->get("txtvcodigo");


          
          $txtIndicador = Yii::$app->db->createCommand("select distinct nombre from tbl_speech_categorias where anulado = 0 and idcategorias = 1 and idcategoria = $txtvindicador  and programacategoria in ('$txtvservicios')")->queryScalar();
           

          $txtRta = Yii::$app->db->createCommand("select distinct * from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and tipoindicador in ('$txtIndicador') and cod_pcrc in ('$txtvcodigo') and programacategoria in ('$txtvservicios')")->queryAll();       

        $arrayUsu = array();
        foreach ($txtRta as $key => $value) {
            array_push($arrayUsu, array("nombre"=>$value['nombre']));
        }

        die(json_encode($arrayUsu));
        }
  public function actionListashijos1(){
        $txtvvariables = Yii::$app->request->get("txtvvariables");
        $txtvservicios = Yii::$app->request->get("txtvservicios");
        $txtvcodigos = Yii::$app->request->get("txtvcodigos");



        $txtidvariables = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categorias where anulado = 0 and idcategorias = 2  and cod_pcrc in ('$txtvcodigos') and programacategoria in ('$txtvservicios') and nombre like '$txtvvariables'")->queryScalar();

        die(json_encode($txtidvariables));
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

          $model = $this->findModel($txtServicioCategorias);
          if ($model->load(Yii::$app->request->post()) && $model->save()) {
              Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
              return $this->redirect('categoriasconfig');
          } else {
                  return $this->render('categoriasupdate', [
                    'model' => $model,
                  ]);
          }
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

        $var_FechaIni = null;
        $var_FechaFin = null;
        $varCorreo = null;
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

        $fechaComoEntero = strtotime($varInicioF);

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
             $lastColumn = 'G';
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

        $lastColumn = 'G'; 
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

        $lastColumn = 'G'; 
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

                    //Diego para calcular promedio Agente positivas                    
                    
                    $vartotalrespo = count($varlistarespo);
                    $vartotalindica = count($varlistaindica);
                    // fin Diego

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

      public function actionCategoriaspermisos(){
        $model = new Dashboardpermisos();
        $txtanulado = 0;
        $txtfechacreacion = date("Y-m-d");        

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
      $var_FechaIni = Yii::$app->request->get("var_FechaIni");
      $var_FechaFin = Yii::$app->request->get("var_FechaFin");
      $txtServicio = Yii::$app->request->get("varArbol_idV");
      $txtParametros = Yii::$app->request->get("varParametros_idV");
      $varCorreo = Yii::$app->request->get("var_Destino");
      $txtCodPcrcok = Yii::$app->request->get("var_CodsPcrc");  
      $txtextensionParams = Yii::$app->request->get("var_extensionparams");            

      $varListaCodPcrcExport = explode(",", str_replace(array("#", "'", ";", " "), '', $txtCodPcrcok));

      $varidLllamadaExport = (new \yii\db\Query())
                              ->select(['idllamada'])
                              ->from(['tbl_speech_servicios']) 
                              ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes')           
                              ->where(['in','tbl_speech_parametrizar.cod_pcrc',$varListaCodPcrcExport])
                              ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                              ->groupby(['idllamada'])
                              ->Scalar();

      $varNombreServicioExport = (new \yii\db\Query())
                                  ->select(['nameArbol'])
                                  ->from(['tbl_speech_servicios']) 
                                  ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                      'tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes')           
                                  ->where(['in','tbl_speech_parametrizar.cod_pcrc',$varListaCodPcrcExport])
                                  ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                  ->groupby(['nameArbol'])
                                  ->Scalar();

      $varNombrePcrcExport =   (new \yii\db\Query())
                            ->select(['CONCAT(cod_pcrc," - ",pcrc) as NombrePcrc'])
                            ->from(['tbl_proceso_cliente_centrocosto'])            
                            ->where(['in','cod_pcrc',$varListaCodPcrcExport])
                            ->andwhere(['=','anulado',0])
                            ->groupby(['cod_pcrc'])
                            ->Scalar();


      $varFechaInicioExport = $var_FechaIni;
      $varFechaFinExport = date('Y-m-d',strtotime($var_FechaFin."+ 1 days"));

      $varListaExtensionesExport = explode(",", str_replace(array("#", "'", ";", " "), '', $txtParametros));

      $varCantidadExport = (new \yii\db\Query())
                              ->select(['callid'])
                              ->from(['tbl_dashboardspeechcalls'])            
                              ->where(['=','anulado',0])
                              ->andwhere(['=','servicio',$txtServicio])
                              ->andwhere(['in','extension',$varListaExtensionesExport])
                              ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                              ->andwhere(['in','idcategoria',$varidLllamadaExport])
                              ->groupby(['callid'])                                
                              ->count();

      $varListCallidsExport = (new \yii\db\Query())
                              ->select(['callid'])
                              ->from(['tbl_speech_general'])            
                              ->where(['=','anulado',0])
                              ->andwhere(['=','programacliente',$txtServicio])
                              ->andwhere(['in','extension',$varListaExtensionesExport])
                              ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                              ->groupby(['callid'])
                              ->all();

      $varArrayCallidExport = array();
      foreach ($varListCallidsExport as $key => $value) {
        array_push($varArrayCallidExport, $value['callid']);
      }
      $varCallidsListExport = implode(", ", $varArrayCallidExport);
      $arrayCallids_downExport = str_replace(array("#", "'", ";", " "), '', $varCallidsListExport);
      $varCallidsIndicadoresExport = explode(",", $arrayCallids_downExport);

      $varListarIndicadoresExport = (new \yii\db\Query())
                              ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                              ->from(['tbl_speech_categorias'])            
                              ->where(['=','anulado',0])
                              ->andwhere(['in','cod_pcrc',$varListaCodPcrcExport])
                              ->andwhere(['=','idcategorias',1])
                              ->all();


      $phpExc = new \PHPExcel();
      $phpExc->getProperties()
              ->setCreator("Konecta")
              ->setLastModifiedBy("Konecta")
              ->setTitle("Dashboard Escuchar + ".$varNombreServicioExport)
              ->setSubject("Procesos Dashboard Escuchar + ".$varNombreServicioExport)
              ->setDescription("Este archivo contiene el proceso de las comparaciones con las categorias y las llamadas en Speech,")
              ->setKeywords("Archivo Escuchar + ".$varNombreServicioExport);
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

      $phpExc->getActiveSheet()->SetCellValue('A2','INFORME ESCUCHAR + '.$varNombreServicioExport);
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
      $phpExc->getActiveSheet()->setCellValue('A5', $varNombreServicioExport);
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
      $phpExc->getActiveSheet()->setCellValue('H5', $varCantidadExport);
      $phpExc->setActiveSheetIndex(0)->mergeCells('H5:J5');

      $phpExc->getActiveSheet()->SetCellValue('A6','Programas Seleccionados');
      $phpExc->setActiveSheetIndex(0)->mergeCells('A6:D6');
      $phpExc->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleArrayTitle);
      $phpExc->getActiveSheet()->setCellValue('A7', $varNombrePcrcExport);
      $phpExc->setActiveSheetIndex(0)->mergeCells('A7:D7');

      $phpExc->getActiveSheet()->SetCellValue('E6','Parametros Seleccionados');
      $phpExc->setActiveSheetIndex(0)->mergeCells('E6:G6');
      $phpExc->getActiveSheet()->getStyle('E6')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleArrayTitle);
      $phpExc->getActiveSheet()->setCellValue('E7', $txtParametros);
      $phpExc->setActiveSheetIndex(0)->mergeCells('E7:G7');

      $phpExc->getActiveSheet()->SetCellValue('H6','Cantidad de Llamadas');
      $phpExc->setActiveSheetIndex(0)->mergeCells('H6:J6');
      $phpExc->getActiveSheet()->getStyle('H6')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleArrayTitle);        
      $phpExc->getActiveSheet()->setCellValue('H7', $varCantidadExport);
      $phpExc->setActiveSheetIndex(0)->mergeCells('H7:J7');

      $phpExc->getActiveSheet()->SetCellValue('A8','GESTION GRAFICA');
      $phpExc->setActiveSheetIndex(0)->mergeCells('A8:J8');
      $phpExc->getActiveSheet()->getStyle('A8')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A8')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('A8')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A8')->applyFromArray($styleArrayTitle);

      $lastColumn = 'A';
      $numCell = 9;
      foreach ($varListarIndicadoresExport as $key => $value) {
        $txtIdIndicadores = $value['idcategoria'];
        $varNombreIndicador = $value['nombre'];
        $varTipoParametro = $value['tipoparametro'];
        $txtTipoFormIndicador = $value['orientacionform'];

        $varListVariablesIExport = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform','responsable'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcExport])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','tipoindicador',$varNombreIndicador])
                                ->all();

        $arrayListOfVar = array();
        $arraYListOfVarMas = array();
        $arraYListOfVarMenos = array();

        $varSumarPositivas = 0;
        $varSumarNegativas = 0;

        foreach ($varListVariablesIExport as $key => $value) {
          $varOrienta = $value['orientacionsmart'];
          array_push($arrayListOfVar, $value['idcategoria']);

          if ($varOrienta == 1) {
            array_push($arraYListOfVarMenos, $value['idcategoria']);
            $varSumarNegativas = $varSumarNegativas + 1;
          }else{
            array_push($arraYListOfVarMas, $value['idcategoria']);
            $varSumarPositivas = $varSumarPositivas + 1;
          }

        }

        $arrayVariableList = implode(", ", $arrayListOfVar);
        $arrayVariable_down = str_replace(array("#", "'", ";", " "), '', $arrayVariableList);
        $arrayVariable = explode(",", $arrayVariable_down);

        $arrayVariableMasLit = implode(", ", $arraYListOfVarMas);
        $arrayVariableMas_down = str_replace(array("#", "'", ";", " "), '', $arrayVariableMasLit);
        $arrayVariableMas = explode(",", $arrayVariableMas_down);

        $arrayVariableMenosList = implode(", ", $arraYListOfVarMenos);              
        $arrayVariableMenos_down = str_replace(array("#", "'", ";", " "), '', $arrayVariableMenosList);
        $arrayVariableMenos = explode(",", $arrayVariableMenos_down);

        $varTotalvariables = count($varListVariablesIExport);

        if ($varTipoParametro == "2") {
          
          if ($varSumarPositivas == $varTotalvariables) {
            $varconteo = (new \yii\db\Query())
                                        ->select(['callid','SUM(cantproceso)'])
                                        ->from(['tbl_speech_general'])            
                                        ->where(['=','anulado',0])
                                        ->andwhere(['=','programacliente',$txtServicio])
                                        ->andwhere(['in','extension',$varListaExtensionesExport])
                                        ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                        ->andwhere(['in','callid',$varListCallidsExport])
                                        ->andwhere(['in','idindicador',$arrayVariable])
                                        ->andwhere(['in','idvariable',$arrayVariable])
                                        ->groupby(['callid'])
                                        ->count();

            if ($varconteo == null) {
              $varconteo = 0;
            }

          }else{
            
            $varconteo = (new \yii\db\Query())  
                                        ->select(['callid','SUM(cantproceso)'])
                                        ->from(['tbl_speech_general'])            
                                        ->where(['=','anulado',0])
                                        ->andwhere(['=','programacliente',$txtServicio])
                                        ->andwhere(['in','extension',$varListaExtensionesExport])
                                        ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                        ->andwhere(['in','callid',$varListCallidsExport])
                                        ->andwhere(['in','idindicador',$arrayVariableMenos])
                                        ->andwhere(['in','idvariable',$arrayVariableMenos])
                                        ->groupby(['callid'])
                                        ->count();

            if ($varconteo != null) {
              $varconteo = round(count($varListCallidsExport) - $varconteo);                
            }else{
              $varconteo = 0;
            }

          }

        }else{

          if ($arrayVariableMas != "") {
            $varconteo = (new \yii\db\Query())
                                        ->select(['callid','SUM(cantproceso)'])
                                        ->from(['tbl_speech_general'])            
                                        ->where(['=','anulado',0])
                                        ->andwhere(['=','programacliente',$txtServicio])
                                        ->andwhere(['in','extension',$varListaExtensionesExport])
                                        ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                        ->andwhere(['in','callid',$varListCallidsExport])
                                        ->andwhere(['in','idindicador',$arrayVariableMas])
                                        ->andwhere(['in','idvariable',$arrayVariableMas])
                                        ->groupby(['callid'])
                                        ->count();
          }else{
            $varconteo = 0;
          }

          if ($arrayVariableMenos != "") {
            $varconteo = (new \yii\db\Query())
                                        ->select(['callid','SUM(cantproceso)'])
                                        ->from(['tbl_speech_general'])            
                                        ->where(['=','anulado',0])
                                        ->andwhere(['=','programacliente',$txtServicio])
                                        ->andwhere(['in','extension',$varListaExtensionesExport])
                                        ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                        ->andwhere(['in','callid',$varListCallidsExport])
                                        ->andwhere(['in','idindicador',$arrayVariableMenos])
                                        ->andwhere(['in','idvariable',$arrayVariableMenos])
                                        ->groupby(['callid'])
                                        ->count();
          }else{
            $varconteo = 0;
          }

        }


        if ($varconteo != 0 && $varCantidadExport != 0) {
          if ($txtTipoFormIndicador == 0) {
            $txtRtaProcentaje = (round(($varconteo / $varCantidadExport) * 100, 1));
          }else{
            $txtRtaProcentaje = (100 - (round(($varconteo / $varCantidadExport) * 100, 1)));
          }
        }else{
          if ($txtTipoFormIndicador == 0) {
            $txtRtaProcentaje = 100;
          }else{
            $txtRtaProcentaje = 0;
          }
        }

        $phpExc->getActiveSheet()->setCellValue($lastColumn.'9', $varNombreIndicador);           
        $phpExc->getActiveSheet()->getStyle($lastColumn.'9')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle($lastColumn.'9')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle($lastColumn.'9')->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle($lastColumn.'9')->applyFromArray($styleArrayTitle); 

        $phpExc->getActiveSheet()->setCellValue($lastColumn.'10', $txtRtaProcentaje); 
        $lastColumn++;
      }
      
      $phpExc->getActiveSheet()->SetCellValue('A11','INDICADORES POR VARIABLE');
      $phpExc->setActiveSheetIndex(0)->mergeCells('A11:J11');
      $phpExc->getActiveSheet()->getStyle('A11')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A11')->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('A11')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A11')->applyFromArray($styleArrayTitle);
      

      $phpExc->getActiveSheet()->SetCellValue('A12','Indicador');
      $phpExc->setActiveSheetIndex(0)->mergeCells('A12:B12');
      $phpExc->getActiveSheet()->getStyle('A12')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A12')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A12')->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('A12')->applyFromArray($styleArrayTitle);

      $phpExc->getActiveSheet()->SetCellValue('C12','Variable');
      $phpExc->setActiveSheetIndex(0)->mergeCells('C12:D12');
      $phpExc->getActiveSheet()->getStyle('C12')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('C12')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('C12')->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('C12')->applyFromArray($styleArrayTitle);

      $phpExc->getActiveSheet()->SetCellValue('E12','% de participacion');
      $phpExc->setActiveSheetIndex(0)->mergeCells('E12:F12');
      $phpExc->getActiveSheet()->getStyle('E12')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('E12')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('E12')->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('E12')->applyFromArray($styleArrayTitle);

      $phpExc->getActiveSheet()->SetCellValue('G12','Cantidad de llamadas');
      $phpExc->setActiveSheetIndex(0)->mergeCells('G12:H12');
      $phpExc->getActiveSheet()->getStyle('G12')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('G12')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('G12')->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('G12')->applyFromArray($styleArrayTitle);

      $phpExc->getActiveSheet()->SetCellValue('I12','Duracion (Segundos)');
      $phpExc->setActiveSheetIndex(0)->mergeCells('I12:J12');
      $phpExc->getActiveSheet()->getStyle('I12')->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('I12')->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('I12')->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('I12')->applyFromArray($styleArrayTitle);
      
      $lastColumn = 'A';
      $numCell = 13;
      foreach ($varListarIndicadoresExport as $key => $value) {
        $txtIdIndicadoresExport = $value['idcategoria'];
        $varNombreIndicadorExport = $value['nombre'];

        $varListVariablesV = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform','responsable'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCodPcrcExport])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','tipoindicador',$varNombreIndicadorExport])
                                ->all();

        foreach ($varListVariablesV as $key => $value) {
          $varVariables = $value['idcategoria'];
          $varNombreVariable = $value['nombre'];

          $varConteoPorVariable =  (new \yii\db\Query())
                                          ->select(['callid','SUM(cantproceso)'])
                                          ->from(['tbl_speech_general'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','programacliente',$txtServicio])
                                          ->andwhere(['in','extension',$varListaExtensionesExport])
                                          ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                          ->andwhere(['in','callid',$varCallidsIndicadoresExport])
                                          ->andwhere(['in','idindicador',$varVariables])
                                          ->andwhere(['in','idvariable',$varVariables])
                                          ->groupby(['callid'])
                                          ->count();

          if ($varConteoPorVariable != 0 && $varCantidadExport != 0) {
            if ($txtTipoFormIndicador == 0) {
              $txtRtaPorcentajeVariable = (round(($varConteoPorVariable / $varCantidadExport) * 100, 1));
            }else{
              $txtRtaPorcentajeVariable = (100 - (round(($varConteoPorVariable / $varCantidadExport) * 100, 1)));
            }
          }else{
            $txtRtaPorcentajeVariable = 0;
          }

          $varLlamadasVariable =  (new \yii\db\Query())
                                          ->select(['idcategoria'])
                                          ->from(['tbl_dashboardspeechcalls'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','servicio',$txtServicio])
                                          ->andwhere(['in','extension',$varListaExtensionesExport])
                                          ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                          ->andwhere(['in','idcategoria',$varVariables])
                                          ->groupby(['callid'])
                                          ->count();

          $varLlamadasVariableSegundos =  (new \yii\db\Query())
                                          ->select(['avg(callduracion)'])
                                          ->from(['tbl_dashboardspeechcalls'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','servicio',$txtServicio])
                                          ->andwhere(['in','extension',$varListaExtensionesExport])
                                          ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                          ->andwhere(['in','idcategoria',$varVariables])
                                          ->Scalar();

          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $varNombreIndicadorExport); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':B'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $varNombreVariable); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('C'.$numCell.':D'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $txtRtaPorcentajeVariable.' %'); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('E'.$numCell.':F'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $varConteoPorVariable); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('G'.$numCell.':H'.$numCell);

          $phpExc->getActiveSheet()->setCellValue('I'.$numCell, round($varLlamadasVariableSegundos)); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('I'.$numCell.':J'.$numCell);
          $numCell++;
        }

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

      $varListCategoriasExports = (new \yii\db\Query())
                              ->select(['idcategoria','nombre','orientacionsmart'])
                              ->from(['tbl_speech_categorias'])            
                              ->where(['=','anulado',0])
                              ->andwhere(['in','cod_pcrc',$varListaCodPcrcExport])
                              ->andwhere(['in','idcategorias',[1,2]])
                              ->all();

      $lastColumn = 'I';
      foreach ($varListCategoriasExports as $key => $value) {

        $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['nombre']); 
        $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle);
        $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayTitle);
        $lastColumn++; 

      }
      $numCell = $numCell + 1;

      $varidClienteExport = (new \yii\db\Query())
                              ->select(['id_dp_clientes'])
                              ->from(['tbl_speech_parametrizar'])          
                              ->where(['in','tbl_speech_parametrizar.cod_pcrc',$varListaCodPcrcExport])
                              ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                              ->groupby(['id_dp_clientes'])
                              ->Scalar();

      $varlistaMotivosTmp = (new \yii\db\Query())
                              ->select(['*'])
                              ->from(['tbl_speech_tmpmotivos'])            
                              ->where(['=','anulado',0])
                              ->andwhere(['in','cod_pcrc',$varListaCodPcrcExport])
                              ->andwhere(['=','id_dp_cliente',$varidClienteExport])
                              ->andwhere(['>=','fechainiciotmp',$varFechaInicioExport])
                              ->andwhere(['<=','fechafintmp',$varFechaFinExport])
                              ->andwhere(['=','extension',$txtextensionParams])
                              ->groupby(['id_motivo'])
                              ->All(); 

      foreach ($varlistaMotivosTmp as $key => $value) {
          $varMotivoVoice = intval($value['id_motivo']);
                                  
          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['motivo']); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':B'.$numCell);
                        
          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['porcentaje'].' %'); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('C'.$numCell.':D'.$numCell);
                        
          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['cantidadmotivos']); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('E'.$numCell.':F'.$numCell);
                        
          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['duracionllamada']); 
          $phpExc->setActiveSheetIndex(0)->mergeCells('G'.$numCell.':H'.$numCell);
                        
          $lastColumn = 'I';
          foreach ($varListCategoriasExports as $key => $value) {
              $varVariableId = intval($value['idcategoria']);
              $varArregloCategoria = [$varMotivoVoice,$varVariableId];
              $varSmart = $value['orientacionsmart'];
                        
              $txtRtaPorcentajeMotivoVariable =  (new \yii\db\Query())
                                                  ->select(['porcentaje'])
                                                  ->from(['tbl_speech_tmpmotivosvariables'])            
                                                  ->where(['=','anulado',0])
                                                  ->andwhere(['=','id_dp_cliente',$varidClienteExport])
                                                  ->andwhere(['in','cod_pcrc',$varListaCodPcrcExport])
                                                  ->andwhere(['=','id_motivo',$varMotivoVoice])
                                                  ->andwhere(['=','id_categoria',$varVariableId])
                                                  ->scalar();

              if ($txtRtaPorcentajeMotivoVariable == null) {
                $varConteoPorMotivosVariable =  (new \yii\db\Query())
                                                ->select(['callid'])
                                                ->from(['tbl_dashboardspeechcalls'])            
                                                ->where(['=','anulado',0])
                                                ->andwhere(['=','servicio',$txtServicio])
                                                ->andwhere(['in','extension',$varListaExtensionesExport])
                                                ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                                ->andwhere(['in','idcategoria',$varArregloCategoria])
                                                ->groupby(['callid'])
                                                ->count();
                                      
                $txtvCantMotivos = (new \yii\db\Query())
                                    ->select(['callid'])
                                    ->from(['tbl_dashboardspeechcalls'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','servicio',$txtServicio])
                                    ->andwhere(['in','extension',$varListaExtensionesExport])
                                    ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                    ->andwhere(['=','idcategoria',$varMotivoVoice])
                                    ->groupby(['callid'])
                                    ->count();
                                      
                if ($varConteoPorMotivosVariable != 0 && $txtvCantMotivos != 0) {
                  $txtRtaPorcentajeMotivoVariable = (round(($varConteoPorMotivosVariable / $txtvCantMotivos), 1));
                }else{
                  $txtRtaPorcentajeMotivoVariable = 0;
                }
              }
                        
                        
              if ($varSmart == 1) {
                  if ($txtRtaPorcentajeMotivoVariable <= 10) {
                      $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaPorcentajeMotivoVariable.' %'); 
                      $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorhigh);
                  }else{
                      if ($txtRtaPorcentajeMotivoVariable >= 20) {
                          $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaPorcentajeMotivoVariable.' %'); 
                          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorLess);
                      }else{
                          $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaPorcentajeMotivoVariable.' %'); 
                          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorMiddle);
                      }
                  }
              }else{
                  if ($varSmart == 2) {
                      if ($txtRtaPorcentajeMotivoVariable <= 80) {
                          $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaPorcentajeMotivoVariable.' %'); 
                          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorLess);
                      }else{
                          if ($txtRtaPorcentajeMotivoVariable >= 90) {
                              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaPorcentajeMotivoVariable.' %'); 
                              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorhigh);
                          }else{
                              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $txtRtaPorcentajeMotivoVariable.' %'); 
                              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColorMiddle);
                          }
                      }
                  }
              }
                        
              $lastColumn++; 
                        
          }          
                        
          $numCell++;
      }
                        
      $numCell = $numCell + 1;
      $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'TOTAL CATEGORIZACION POR ASESOR');
      $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':J'.$numCell);
      $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArray);            
      $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
                        
      $numCell = $numCell + 1;
      $phpExc->getActiveSheet()->SetCellValue('A'.$numCell,'Usuario de red');
      $phpExc->getActiveSheet()->getStyle('A'.$numCell)->getFont()->setBold(true);
      $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleColor);
      $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArraySubTitle);
      $phpExc->getActiveSheet()->getStyle('A'.$numCell)->applyFromArray($styleArrayTitle);
                        
      $varListSoloVariablesExports = (new \yii\db\Query())
                                      ->select(['idcategoria','nombre','orientacionsmart'])
                                      ->from(['tbl_speech_categorias'])            
                                      ->where(['=','anulado',0])
                                      ->andwhere(['in','cod_pcrc',$varListaCodPcrcExport])
                                      ->andwhere(['=','idcategorias',2])
                                      ->all();
                        
      $lastColumn = 'B';
      foreach ($varListSoloVariablesExports as $key => $value) {
          $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['nombre']); 
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayTitle);
          $lastColumn++; 
      }
                        
      $varListAsesoresExport =  (new \yii\db\Query())
                                  ->select(['login_id'])
                                  ->from(['tbl_dashboardspeechcalls'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','servicio',$txtServicio])
                                  ->andwhere(['in','extension',$varListaExtensionesExport])
                                  ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                  ->groupby(['login_id'])
                                  ->all();
                        
      $lastColumn = 'A';
      $numCell = $numCell + 1;
      foreach ($varListAsesoresExport as $key => $value) {
          $lastColumn = 'A';
          $varlogin = $value['login_id'];
          $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varlogin);

          $lastColumn = 'B';
          foreach ($varListSoloVariablesExports as $key => $value) {
            $varidcateg = $value['idcategoria'];

            $varCatidad =  (new \yii\db\Query())
                                            ->select(['callid'])
                                            ->from(['tbl_dashboardspeechcalls'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','servicio',$txtServicio])
                                            ->andwhere(['in','extension',$varListaExtensionesExport])
                                            ->andwhere(['between','fechallamada',$varFechaInicioExport.' 05:00:00',$varFechaFinExport.' 05:00:00'])
                                            ->andwhere(['=','login_id',$varlogin])
                                            ->andwhere(['=','idcategoria',$varidcateg])
                                            ->groupby(['callid'])
                                            ->count();

            $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varCatidad);
            $lastColumn++;
          }

          $numCell++;
      }
                        
      $numCell = $numCell + 1;

      $hoy = getdate();
      $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_DashBoard_Escuchar+_".$varNombreServicioExport;
            
      $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
              
      $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
      $tmpFile.= ".xls";

      $objWriter->save($tmpFile);

      $message = "<html><body>";
      $message .= "<h3>Se ha realizado el envio correcto del archivo del programa DashBoard Escuchar +</h3>";
      $message .= "</body></html>";

      Yii::$app->mailer->compose()
                      ->setTo($varCorreo)
                      ->setFrom(Yii::$app->params['email_satu_from'])
                      ->setSubject("Envio Dashboard Escuchar + ".$varNombreServicioExport)
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

                      $txtvarServicio = $varDatos[0];
                      $txtvarPcrc = $varDatos[1];
                      $txtvarParametros = "";
                      $txtvarCategoria = $varDatos[6];
                      $txtvarNombre = $varDatos[7];

                      $varClienteJarvis = Yii::$app->db->createCommand("select distinct cliente from tbl_procesos_volumendirector where cod_pcrc like '$varDatos[1]' and estado = 1 and anulado = 0 ")->queryScalar();

                      $txtvaridcategorias = null;
                      $txtvarTipoC = $varDatos[8];
                      if ($txtvarTipoC == "Indicador") {
                        $txtvaridcategorias = 1;
                      }else{
                        if ($txtvarTipoC == "indicador") {
                          $txtvaridcategorias = 1;
                        }else{
                          if ($txtvarTipoC == "Variable") {
                            $txtvaridcategorias = 2;
                          }else{
                            if ($txtvarTipoC == "variable") {
                              $txtvaridcategorias = 2;
                            }else{
                              if ($txtvarTipoC == "Motivos de contacto") {
                                $txtvaridcategorias = 3;
                              }else{
                                if ($txtvarTipoC == "Motivo de contacto") {
                                  $txtvaridcategorias = 3;
                                }else{
                                  if ($txtvarTipoC == "Motivos de Contacto") {
                                    $txtvaridcategorias = 3;
                                  }else{
                                    if ($txtvarTipoC == "Motivos") {
                                      $txtvaridcategorias = 3;
                                    }else{
                                      if ($txtvarTipoC == "Motivo") {
                                        $txtvaridcategorias = 3;
                                      }else{
                                        if ($txtvarTipoC == "Programa") {
                                          $txtvaridcategorias = 0;
                                        }else{
                                          if ($txtvarTipoC == "programa") {
                                            $txtvaridcategorias = 0;
                                          }else{
                                            if ($txtvarTipoC== "Motivos de contacto") {
                                              $txtvaridcategorias = 3;
                                            }else{
                                              $txtvaridcategorias = 4;
                                            }                                            
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }                    

                      $txtvarTipoC2 = $varDatos[9];

                      $txtvarOSmart = null;
                      $txtvarSmart = strtoupper($varDatos[10]);
                      if ($txtvarSmart == "") {
                        $txtvarOSmart = 0;
                      }else{
                        if ($txtvarSmart == "null") {
                          $txtvarOSmart = 0;
                        }else{
                          if ($txtvarSmart == "0") {
                            $txtvarOSmart = 0;
                          }else{
                            if ($txtvarSmart == "POSITIVO") {
                              $txtvarOSmart = 1;
                            }else{
                              if ($txtvarSmart == "POSITIVOS") {
                                $txtvarOSmart = 1;
                              }else{
                                if ($txtvarSmart == "NEGATIVO") {
                                  $txtvarOSmart = 2;
                                }else{
                                  if ($txtvarSmart == "NEGATIVOS") {
                                    $txtvarOSmart = 2;
                                  }else{
                                    if ($txtvarSmart == "POSITIVA") {
                                      $txtvarOSmart = 1;
                                    }else{
                                      if ($txtvarSmart == "NEGATIVA") {
                                        $txtvarOSmart = 2;
                                      }else{
                                        if ($txtvarSmart == "POSITIVAS") {
                                          $txtvarOSmart = 1;
                                        }else{
                                          if ($txtvarSmart == "NEGATIVAS") {
                                            $txtvarOSmart = 2;
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }

                      $txtvarNormal = null;
                      $txtvarTipoParam = $varDatos[11];
                      if ($txtvarTipoParam == "0" && $txtvaridcategorias == 1) {
                        $txtvarNormal = 2;
                      }else{
                        if ($txtvarTipoParam == "2" && $txtvaridcategorias == 1) {
                          $txtvarNormal = 2;
                        }else{
                          if ($txtvarTipoParam == "1" && $txtvaridcategorias == 1) {
                            $txtvarNormal = 1;
                          }else{
                            $txtvarNormal = 0;
                          }
                        }                        
                      }

                      $txtvarQA = null;
                      $txtvarCXM = strtoupper($varDatos[12]);
                      if ($txtvarCXM == "") {
                        $txtvarQA = 0;
                      }else{
                        if ($txtvarCXM == "0") {
                          $txtvarQA = 0;
                        }else{
                          if ($txtvarCXM == "POSITIVO") {
                            $txtvarQA = 1;
                          }else{
                            if ($txtvarCXM == "POSITIVOS") {
                              $txtvarQA = 1;
                            }else{
                              if ($txtvarCXM == "NEGATIVO") {
                                $txtvarQA = 0;
                              }else{
                                if ($txtvarCXM == "NEGATIVOS") {
                                  $txtvarQA = 0;
                                }else{
                                  if ($txtvarCXM == "POSITIVA") {
                                    $txtvarQA = 1;
                                  }else{
                                    if ($txtvarCXM == "NEGATIVA") {
                                      $txtvarQA = 0;
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }

                      $txtvarIdUsu = null;
                      $txtvarUsu = $varDatos[13];
                      $txtvarIdUsu = Yii::$app->db->createCommand("select distinct usua_id from tbl_usuarios where usua_usuario in ('$txtvarUsu')")->queryScalar();

                      $txtvarUsuabilidad = 1;

                      $varcity = Yii::$app->db->createCommand("select distinct ciudad from tbl_procesos_volumendirector where cod_pcrc like '$varDatos[1]'")->queryScalar();

                      $varCiudad = null;
                      if ($varcity == 'BOGOTÁ') {
                        $varCiudad = 1;
                      }else{
                        $varCiudad = 2;
                      }

                      $txtvarDashboard = 1;

                      $txtvarBolsita = $varDatos[14];

                      Yii::$app->db->createCommand()->insert('tbl_speech_categorias',[
                          'pcrc' => $txtvarServicio,
                          'cod_pcrc' => $txtvarPcrc,
                          'rn' => $txtvarParametros,
                          'extension' => $txtvarParametros,
                          'usua_usuario' => $txtvarParametros,
                          'otros' => $txtvarParametros,
                          'idcategoria' => $txtvarCategoria,
                          'nombre' => $txtvarNombre,
                          'tipocategoria' => $txtvarTipoC,
                          'tipoindicador' => $txtvarTipoC2,
                          'clientecategoria' => $txtvarTipoC2,
                          'orientacionsmart' => $txtvarOSmart,
                          'tipoparametro' => $txtvarNormal,
                          'orientacionform' => $txtvarQA,
                          'usua_id' => $txtvarIdUsu,
                          'usabilidad' => $txtvarUsuabilidad,
                          'idcategorias' => $txtvaridcategorias,
                          'idciudad' => $varCiudad,
                          'fechacreacion' => $txtfechacreacion,
                          'anulado' => $txtanulado,
                          'dashboard' => $txtvarDashboard,
                          'programacategoria' => $txtvarBolsita,
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
                        
                    }
                }else{
                    echo "<option>-</option>";
                }
            }else{
                    echo "<option>No hay datos</option>";
            }

        }

         public function actionListarprogramaindex(){            
           
          $txtvariddashboard = Yii::$app->request->get("txtvariddashboard");
          $txtCodpcrc = Yii::$app->request->get('cod_pcrc');

          if ($txtvariddashboard == 3) {
            $txtRta = Yii::$app->db->createCommand("select distinct sc.programacategoria, sp.rn, sp.ext, sp.usuared, sp.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sp.cod_pcrc in ('$txtCodpcrc') and sp.anulado = 0")->queryAll();
          }else{
            if ($txtvariddashboard == 0) {
              $txtRta = Yii::$app->db->createCommand("select distinct sc.programacategoria, sp.rn, sp.ext, sp.usuared, sp.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sp.cod_pcrc in ('$txtCodpcrc') and sp.anulado = 0 and sp.tipoparametro is null")->queryAll();
            }else{
              $txtRta = Yii::$app->db->createCommand("select distinct sc.programacategoria, sp.rn, sp.ext, sp.usuared, sp.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sp.cod_pcrc in ('$txtCodpcrc') and sp.anulado = 0 and sp.tipoparametro = $txtvariddashboard")->queryAll();
            }
          }

          

          $arrayUsu = array();
          foreach ($txtRta as $key => $value) {
            if($value['rn']!=""){
                array_push($arrayUsu, array("programacategoria"=>$value['programacategoria'],"rn"=>$value['rn'],"tipoparametro"=>$value['tipoparametro']));
            }elseif($value['ext']!=""){
                array_push($arrayUsu, array("programacategoria"=>$value['programacategoria'],"rn"=>$value['ext'],"tipoparametro"=>$value['tipoparametro']));
            }elseif($value['usuared']!=""){
                array_push($arrayUsu, array("programacategoria"=>$value['programacategoria'],"rn"=>$value['usuared'],"tipoparametro"=>$value['tipoparametro']));
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

          
            var_dump($varcod);

             return $this->renderAjax('createelegirprograma',[
                 'model2'=>$model2,
                ]);

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

            $varFechaF = date('Y-m-d',strtotime($varMes."+ 1 month"));
            $varFechaFin = $varFechaF.' 05:00:00';

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

                  Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Delete',
                    'tabla' => 'tbl_speech_general'
                  ])->execute();
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

                  Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Create',
                    'tabla' => 'tbl_speech_general'
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
          $txtvardash = Yii::$app->request->get("vardash");

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
                                          'responsable' => $txtvarcont,
                                      ],'idspeechcategoria ='.$resultado.'')->execute();
        
          $varconteoList = resultado;

          die(json_encode($varconteoList));

        }

        public function actionCategoriasparametros($arbol_idV) {
          $model = new SpeechParametrizar();
          $txtServid = $arbol_idV;

           return $this->renderAjax('categoriasparametros',[
            'model' => $model,
            'txtServid' => $txtServid,
            ]);
        }

        public function actionAutomaticspeecha($varNumber) {
          $txtProblemas = 0;
          ini_set("max_execution_time", "900");
          ini_set("memory_limit", "1024M");
          ini_set( 'post_max_size', '1024M' );

          ignore_user_abort(true);
          set_time_limit(900);

          $txtfechacreacion = date("Y-m-d");
          $txtProblemas = null;
          $varMesActual2 = date('m');
          $varYearActual2 = date('Y');

          // Agregar manualamente Bolsitas y rango de fechas  
          // Nota: Borrar este proceso o mejorarlo cuando se suba a produccion mientras es manual


            $varPcrc = Yii::$app->get('dbQA')->createCommand("select cod_pcrc from tbl_speech_parametrizar where anulado = 0 and id_dp_clientes = 185 group by cod_pcrc")->queryAll(); 

            $txtlistarn = null;
            $txtlistaext = null;
            $txtlistausua = null;

            foreach ($varPcrc as $key => $value) {
              $varIdCodpcrc = $value['cod_pcrc'];              

              $varRN = Yii::$app->get('dbQA')->createCommand("select rn from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$varIdCodpcrc') and rn != '' group by rn")->queryAll();
              $varExt = Yii::$app->get('dbQA')->createCommand("select ext from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$varIdCodpcrc') and ext != '' group by ext")->queryAll();
              $varUsu = Yii::$app->get('dbQA')->createCommand("select usuared from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$varIdCodpcrc') and usuared != '' group by usuared")->queryAll();


              $varListCategorias = Yii::$app->get('dbQA')->createCommand("select distinct idcategoria from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$varIdCodpcrc') group by idcategoria")->queryAll();

              $arraylistcategorias = array();
              foreach ($varListCategorias as $key => $value) {
                array_push($arraylistcategorias, $value['idcategoria']);
              }
              $txtlistcategorias = implode(", ", $arraylistcategorias);
              

              $varprograma = Yii::$app->get('dbQA')->createCommand("select programacategoria from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$varIdCodpcrc') group by programacategoria")->queryScalar();
              $varYearActual = date('Y');

              $varFechaDiaVencido = $varYearActual.'-11-01 05:00:00';
              $varFechaDiaActual = $varYearActual.'-12-01 05:00:00';

              $varIdllamada = Yii::$app->get('dbQA')->createCommand("select idllamada from tbl_speech_servicios where anulado = 0 and id_dp_clientes = 185")->queryScalar();


              if (count($varRN) != 0) {
                $arraylistrn = array();
                foreach ($varRN  as $key => $value) {
                  array_push($arraylistrn, $value['rn']);
                }
                $txtlistarn = implode("', '", $arraylistrn);

                if ($varIdllamada == "1105") {
                  $varListLlamadas = Yii::$app->get('dbSpeechE1')->createCommand("SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox  FROM [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a, [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b, [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1, [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr WHERE DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaDiaVencido' AND '$varFechaDiaActual' AND e.name = '$varprograma' AND a.categoryId in ($varIdllamada, $txtlistcategorias) AND d.fieldName='regla_negocio' AND dd.fieldName='rbstarttime' AND dr.fieldName='idredbox' AND d.fieldValue in ('$txtlistarn') AND d1.fieldName='login_id' AND a.categoryId = b.categoryId AND b.callId=c.callId AND d.callId=c.callId AND d1.callId=c.callId AND e.programId=c.programId AND dd.callId=c.callId AND dr.callId=c.callId ORDER BY Fecha_Llamada DESC")->queryAll();

                  $txtProblemas = 0;
                }else{
                  if ($varIdllamada == "1114") {
                    $varListLlamadas = Yii::$app->get('dbSpeechA2')->createCommand("SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox  FROM [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a, [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b, [speechminer_8_5_512_A2].[dbo].[callMetaTbl] c, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1, [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr WHERE DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaDiaVencido' AND '$varFechaDiaActual' AND e.name = '$varprograma' AND a.categoryId in ($varIdllamada, $txtlistcategorias) AND d.fieldName='regla_negocio' AND dd.fieldName='rbstarttime' AND dr.fieldName='idredbox' AND d.fieldValue in ('$txtlistarn') AND d1.fieldName='login_id' AND a.categoryId = b.categoryId AND b.callId=c.callId AND d.callId=c.callId AND d1.callId=c.callId AND e.programId=c.programId AND dd.callId=c.callId AND dr.callId=c.callId ORDER BY Fecha_Llamada DESC")->queryAll();

                    $txtProblemas = 0;
                  }else{
                    $txtProblemas = 1;
                  }
                }
                
              }else{
                if (count($varExt) != 0) {
                  $arralistext = array();
                  foreach ($varExt as $key => $value) {
                    array_push($arralistext, $value['ext']);
                  }
                  $txtlistaext = implode("', '", $arralistext);

                  if ($varIdllamada == "1105") {
                    $varListLlamadas = Yii::$app->get('dbSpeechE1')->createCommand("SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox  FROM [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a, [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b, [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1, [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr WHERE DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaDiaVencido' AND '$varFechaDiaActual' AND e.name = '$varprograma' AND a.categoryId in ($varIdllamada, $txtlistcategorias) AND d.fieldName='extension' AND dd.fieldName='rbstarttime' AND dr.fieldName='idredbox' AND d.fieldValue in ('$txtlistaext') AND d1.fieldName='login_id' AND a.categoryId = b.categoryId AND b.callId=c.callId AND d.callId=c.callId AND d1.callId=c.callId AND e.programId=c.programId AND dd.callId=c.callId AND dr.callId=c.callId ORDER BY Fecha_Llamada DESC")->queryAll();

                    $txtProblemas = 0;
                  }else{
                    if ($varIdllamada == "1114") {
                      $varListLlamadas = Yii::$app->get('dbSpeechA2')->createCommand("SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox  FROM [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a, [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b, [speechminer_8_5_512_A2].[dbo].[callMetaTbl] c, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1, [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr WHERE DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaDiaVencido' AND '$varFechaDiaActual' AND e.name = '$varprograma' AND a.categoryId in ($varIdllamada, $txtlistcategorias) AND d.fieldName='extension' AND dd.fieldName='rbstarttime' AND dr.fieldName='idredbox' AND d.fieldValue in ('$txtlistaext') AND d1.fieldName='login_id' AND a.categoryId = b.categoryId AND b.callId=c.callId AND d.callId=c.callId AND d1.callId=c.callId AND e.programId=c.programId AND dd.callId=c.callId AND dr.callId=c.callId ORDER BY Fecha_Llamada DESC")->queryAll();

                      $txtProblemas = 0;
                    }else{
                      $txtProblemas = 1;
                    }
                  }

                }else{
                  if (count($varUsu) != 0) {
                    $arralistusua = array();
                    foreach ($varUsu as $key => $value) {
                      array_push($arralistusua, $value['usuared']);
                    }
                    $txtlistausua = implode("', '", $arralistusua);

                    if ($varIdllamada == "1105") {
                      $varListLlamadas = Yii::$app->get('dbSpeechE1')->createCommand("SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox  FROM [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a, [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b, [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1, [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr WHERE DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaDiaVencido' AND '$varFechaDiaActual' AND e.name = '$varprograma' AND a.categoryId in ($varIdllamada, $txtlistcategorias) AND d.fieldName='regla_negocio' AND dd.fieldName='rbstarttime' AND dr.fieldName='idredbox' AND d.fieldValue in ('$txtlistausua') AND d1.fieldName='login_id' AND a.categoryId = b.categoryId AND b.callId=c.callId AND d.callId=c.callId AND d1.callId=c.callId AND e.programId=c.programId AND dd.callId=c.callId AND dr.callId=c.callId ORDER BY Fecha_Llamada DESC")->queryAll();

                      $txtProblemas = 0;
                    }else{
                      if ($varIdllamada == "1114") {
                        $varListLlamadas = Yii::$app->get('dbSpeechA2')->createCommand("SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox  FROM [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a, [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b, [speechminer_8_5_512_A2].[dbo].[callMetaTbl] c, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1, [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd, [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr WHERE DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaDiaVencido' AND '$varFechaDiaActual' AND e.name = '$varprograma' AND a.categoryId in ($varIdllamada, $txtlistcategorias) AND d.fieldName='regla_negocio' AND dd.fieldName='rbstarttime' AND dr.fieldName='idredbox' AND d.fieldValue in ('$txtlistausua') AND d1.fieldName='login_id' AND a.categoryId = b.categoryId AND b.callId=c.callId AND d.callId=c.callId AND d1.callId=c.callId AND e.programId=c.programId AND dd.callId=c.callId AND dr.callId=c.callId ORDER BY Fecha_Llamada DESC")->queryAll();

                        $txtProblemas = 0;
                      }else{
                        $txtProblemas = 1;
                      }
                    }
                  }
                }
              }  

              if ($txtProblemas == 0) {       

                if (count($varListLlamadas) != 0) {

                  if (count($varRN) != 0) {
                    $varListRepetidas = Yii::$app->get('dbQA')->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varprograma') and fechallamada between '$varFechaDiaVencido' AND '$varFechaDiaActual' and extension in ('$txtlistarn')")->queryScalar();
                  }else{
                    if (count($varExt) != 0) {
                      $varListRepetidas = Yii::$app->get('dbQA')->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varprograma') and fechallamada between '$varFechaDiaVencido' AND '$varFechaDiaActual' and extension in ('$txtlistaext')")->queryScalar();
                    }else{
                      if (count($varUsu) != 0) {
                        $varListRepetidas = Yii::$app->get('dbQA')->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varprograma') and fechallamada between '$varFechaDiaVencido' AND '$varFechaDiaActual' and extension in ('$txtlistausua')")->queryScalar();
                      }
                    }
                  }

                  if ($varListRepetidas == 0) {
                    foreach ($varListLlamadas as $key => $value) {
                      Yii::$app->get('dbQA')->createCommand()->insert('tbl_dashboardspeechcalls',[
                                                         'callId' => $value['callId'],
                                                         'idcategoria' => $value['CAtegoriaID'],
                                                         'nombreCategoria' => $value['Nombre_Categoria'],
                                                         'extension' => $value['extension'],
                                                         'login_id' => $value['login_id'],
                                                         'fechallamada' => $value['Fecha_Llamada'],
                                                         'callduracion' => $value['cantidadllamadas'],
                                                         'servicio' => $value['Servicio'],
                                                         'fechareal' => $value['Fechareal'],
                                                         'idredbox' => $value['idredbox'],
                                                         'fechacreacion' => $txtfechacreacion,
                                                         'anulado' => 0,
                                                      ])->execute();
                    }

                    if (count($varRN) != 0) {
                      $varListProceso = Yii::$app->get('dbQA')->createCommand("SELECT * FROM (select llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria as llamacategoria, cate.idcategoria as catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) as encuentra, llama.nombreCategoria from tbl_dashboardspeechcalls llama left join (select idcategoria, tipoindicador, programacategoria, cod_pcrc from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and programacategoria in ('$varprograma') order by cod_pcrc, tipoindicador) cate on llama.servicio = cate.programacategoria where llama.servicio in ('$varprograma') and llama.extension in ('$txtlistarn') and llama.fechallamada between '$varFechaDiaVencido' and '$varFechaDiaActual' group by llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  order by encuentra DESC) datos WHERE llamacategoria = catecategoria")->queryAll();
                    }else{
                      if (count($varExt) != 0) {
                        $varListProceso = Yii::$app->get('dbQA')->createCommand("SELECT * FROM (select llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria as llamacategoria, cate.idcategoria as catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) as encuentra, llama.nombreCategoria from tbl_dashboardspeechcalls llama left join (select idcategoria, tipoindicador, programacategoria, cod_pcrc from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and programacategoria in ('$varprograma') order by cod_pcrc, tipoindicador) cate on llama.servicio = cate.programacategoria where llama.servicio in ('$varprograma') and llama.extension in ('$txtlistaext') and llama.fechallamada between '$varFechaDiaVencido' and '$varFechaDiaActual' group by llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  order by encuentra DESC) datos WHERE llamacategoria = catecategoria")->queryAll();
                      }else{
                        if (count($varUsu) != 0) {
                          $varListProceso = Yii::$app->get('dbQA')->createCommand("SELECT * FROM (select llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria as llamacategoria, cate.idcategoria as catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) as encuentra, llama.nombreCategoria from tbl_dashboardspeechcalls llama left join (select idcategoria, tipoindicador, programacategoria, cod_pcrc from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and programacategoria in ('$varprograma') order by cod_pcrc, tipoindicador) cate on llama.servicio = cate.programacategoria where llama.servicio in ('$varprograma') and llama.extension in ('$txtlistausua') and llama.fechallamada between '$varFechaDiaVencido' and '$varFechaDiaActual' group by llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  order by encuentra DESC) datos WHERE llamacategoria = catecategoria")->queryAll();
                        }
                      }
                    }                

                    foreach ($varListProceso as $key => $value) {
                      Yii::$app->get('dbQA')->createCommand()->insert('tbl_speech_general',[
                                                           'programacliente' => $value['servicio'],
                                                           'fechainicio' => $varYearActual2.'-'.$varMesActual2.'-01',
                                                           'fechafin' => NULL,
                                                           'callid' => $value['callid'],
                                                           'fechallamada' => $value['fechallamada'],
                                                           'extension' => $value['extension'],
                                                           'idindicador' => $value['llamacategoria'],
                                                           'idvariable' => $value['catecategoria'],
                                                           'cantproceso' => $value['encuentra'],
                                                           'fechacreacion' => $txtfechacreacion,
                                                           'anulado' => 0,
                                                           'usua_id' => 2953,
                                                           'arbol_id' => 308,
                                                        ])->execute();
                    }
                  }                  
                }  
              }              
            }

          return $this->render('automaticspeech',[
            'txtProblemas' => $txtProblemas,
            ]);
        }

        public function actionCategoriashalla($txtServicioCategorias) {
          $model = new SpeechCategorias();
          $txtCodPcrc = $txtServicioCategorias;
          $txtmes = date("n");

          $txtidcliente = Yii::$app->db->createCommand("select distinct id_dp_clientes from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$txtCodPcrc')")->queryScalar(); 

          return $this->render('categoriashalla',[
              'txtCodPcrc' => $txtCodPcrc,
              'txtidcliente' => $txtidcliente,
              'txtmes' => $txtmes,
              'model' => $model,
            ]);
        }
        public function actionIngresarhallazgo() {
          $txtfechacreacion = date("Y-m-d");  
          $sessiones = Yii::$app->user->identity->id;  
          $txtvaridspeechcat = Yii::$app->request->post("varidspeechcat");
          $txtvarhallazgocat = Yii::$app->request->post("varhallazgocat");
          $txtmes = Yii::$app->request->post("varmes");

          $txtres = Yii::$app->db->createCommand("select hallazgo from tbl_speech_hallazgos where idspeechcategoria = '$txtvaridspeechcat' and mes = $txtmes")->queryScalar();
          if($txtres){
                      Yii::$app->db->createCommand()->update('tbl_speech_hallazgos',[
                                          'hallazgo' => $txtvarhallazgocat,
                                      ],'idspeechcategoria ='.$txtvaridspeechcat.'')->execute();
        
            $varres = 0;

            die(json_encode($varres));
          }
          else{
            
            Yii::$app->db->createCommand()->insert('tbl_speech_hallazgos',[
              'idspeechcategoria' => $txtvaridspeechcat,
              'hallazgo' => $txtvarhallazgocat,
              'mes' => $txtmes,
              'usua_id' => $sessiones,
              'fechacreacion' => $txtfechacreacion,
              ])->execute();

              $varres = 0;

            die(json_encode($varres));
          }

        }

        public function actionCategoriasdefinicion($txtServicioCategorias) {
          $model = new SpeechCategorias();
          $txtCodPcrc = $txtServicioCategorias;
          $txtidcliente = Yii::$app->db->createCommand("select distinct id_dp_clientes from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$txtCodPcrc')")->queryScalar(); 

          return $this->render('categoriasdefinicion',[
              'txtCodPcrc' => $txtCodPcrc,
              'txtidcliente' => $txtidcliente,
              'model' => $model,
            ]);
        }
        public function actionIngresardefinicion() {
          $txtvaridspeechcat = Yii::$app->request->post("varidspeechcat");
          $txtvardefiniciont = Yii::$app->request->post("vardefinicioncat");
          
           Yii::$app->db->createCommand()->update('tbl_speech_categorias',[
                                          'definicion' => $txtvardefiniciont,
                                      ],'idspeechcategoria ='.$txtvaridspeechcat.'')->execute();        
            $varres = 0;
            die(json_encode($varres));
        }

        public function actionMarcacionpcrc(){
          $model = new SpeechCategorias();

          return $this->render('marcacionpcrc',[
            'model' => $model,
            ]);
        }

        public function actionCategoriasentto(){    
          $model = new SpeechCategorias();
          $txtservicio = null;
          $txtfinicio = null;
          $txtffin = null;

          $form = Yii::$app->request->post();

          if ($model->load($form)) {
            $txtservicio = $model->programacategoria;
            $varMes = $model->nombre;

            $month = $varMes;
            $year = date('Y');
            $day = date("d", mktime(0,0,0, $month+1, 0, $year));
                 
            $txtfinicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));

            $txtffin = date('Y-m-d', mktime(0,0,0, $month, $day + 1, $year));

          }

          return $this->render('categoriasentto',[
            'model' => $model,
            'txtservicio' => $txtservicio,
            'txtfinicio' => $txtfinicio,
            'txtffin' => $txtffin,
            ]);
        }

        public function actionImportarentto(){
          $model = new UploadForm2();

          if (Yii::$app->request->isPost) {
            $model->file = UploadedFile::getInstance($model, 'file');

            if ($model->file && $model->validate()){
              $model->file->saveAs('categorias/' . $model->file->baseName . '.' . $model->file->extension);

              $fila = 1;
              if (($gestor = fopen('categorias/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false){
                while (($datos = fgetcsv($gestor)) !== false){
                   $numero = count($datos);

                   $fila++;
                  for ($c=0; $c < $numero; $c++){
                    $varArray = $datos[$c]; 
                    $varDatos = explode(";", utf8_encode($varArray));

                    $varTipo = $varDatos[2];
                    $varIdCategorias = null;
                    if ($varTipo == "Indicador") {
                      $varIdCategorias = 1;
                    }else{
                      if ($varTipo == "indicador") {
                        $varIdCategorias = 1;
                      }else{
                        if ($varTipo == "indicadores") {
                          $varIdCategorias = 1;
                        }else{
                          if ($varTipo == "Indicadores") {
                            $varIdCategorias = 1;
                          }else{
                            if ($varTipo == "variable") {
                              $varIdCategorias = 2;
                            }else{
                              if ($varTipo == "variables") {
                                $varIdCategorias = 2;
                              }else{
                                if ($varTipo == "Variable") {
                                  $varIdCategorias = 2;
                                }else{
                                  if ($varTipo == "Variables") {
                                    $varIdCategorias = 2;
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }

                    $varOrientacion = $varDatos[5];
                    $txtOrient = null;
                    if ($varOrientacion == "positivo") {
                      $txtOrient = 0;
                    }else{
                      if ($varOrientacion == "positiva") {
                        $txtOrient = 0;
                      }else{
                        if ($varOrientacion == "Positivo") {
                          $txtOrient = 0;
                        }else{
                          if ($varOrientacion == "negativo") {
                            $txtOrient = 1;
                          }else{
                            if ($varOrientacion == "negativa") {
                              $txtOrient = 1;
                            }else{
                              if ($varOrientacion == "Negativo") {
                                $txtOrient = 1;
                              }
                            }
                          }
                        }
                      }
                    }

                    Yii::$app->db->createCommand()->insert('tbl_speech_categoriascalidad',[
                                           'idcategoria' => $varDatos[0],
                                           'nombre' => $varDatos[1],
                                           'tipocategoria' => $varDatos[2],
                                           'tipoindicador' => $varDatos[3],
                                           'clientecategoria' => null,
                                           'orientacionentto' => $txtOrient,
                                           'usua_id' => Yii::$app->user->identity->id,
                                           'idcategorias' => $varIdCategorias,
                                           'idciudad' => 0,
                                           'fechacreacion' => date("Y-m-d"),
                                           'anulado' => 0,
                                           'bolsitacategoria' => $varDatos[4],
                                       ])->execute();
                  }
                }
                fclose($gestor);
                return $this->redirect('categoriasentto');
              }
            }
          }                

          return $this->renderAjax('importarentto',[
            'model' => $model,
            ]);
        }

public function actionCantidadentto(){
          $txtLlamadas = Yii::$app->request->get("txtLlamadas");
          $varBolsita = Yii::$app->request->get("txtBolsitas");
          ini_set("max_execution_time", "900");
          ini_set("memory_limit", "1024M");
          ini_set( 'post_max_size', '1024M' );

          ignore_user_abort(true);
          set_time_limit(900);

          $month = $txtLlamadas;
          $year = date('Y');
          $day = date("d", mktime(0,0,0, $month+1, 0, $year));
               
          $txtfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));

          $txtfechafin = date('Y-m-d', mktime(0,0,0, $month, $day + 1, $year));

          Yii::$app->db->createCommand("delete from tbl_dashboardcategoriascalls where anulado = 0 and servicio in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00'")->execute();
          
          $varlistaentto = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categoriascalidad where anulado = 0 and bolsitacategoria in ('$varBolsita') group by idcategoria")->queryAll();

          $vararraycategorias = array();
          foreach ($varlistaentto as $key => $value) {
            array_push($vararraycategorias, $value['idcategoria']);
          }
          $txtlistidcategoria = implode(", ", $vararraycategorias);

          if ($varBolsita == "CX_Directv") {
            $varListLlamadas = Yii::$app->get('dbSpeechE1')->createCommand("SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue AS login_id, dd.fieldValue as extension, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio FROM [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a, [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b, [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dd, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] ddd, [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e  WHERE DATEADD(s,c.callTime,'19700101') BETWEEN '$txtfechainicio 05:00:00.000' AND '$txtfechafin 05:00:00.000' AND e.name = '$varBolsita' AND a.categoryId in (1105, $txtlistidcategoria) AND d.fieldName in ('login_id')  AND dd.fieldName in ('extension') AND ddd.fieldName in ('segment') AND ddd.fieldValue like ('%cal_%') AND a.categoryId = b.categoryId AND b.callId=c.callId AND d.callId=c.callId AND dd.callId=c.callId AND ddd.callId=c.callId AND e.programId=c.programId ORDER BY Fecha_Llamada DESC")->queryAll();
          }else{
            $varListLlamadas = Yii::$app->get('dbSpeechE1')->createCommand("SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue AS login_id, dd.fieldValue as extension, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio FROM [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a, [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b, [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d, [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dd, [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e  WHERE DATEADD(s,c.callTime,'19700101') BETWEEN '$txtfechainicio 05:00:00.000' AND '$txtfechafin 05:00:00.000' AND e.name = '$varBolsita' AND a.categoryId in (1105, $txtlistidcategoria) AND d.fieldName in ('login_id')  AND dd.fieldName in ('extension')  AND a.categoryId = b.categoryId AND b.callId=c.callId AND d.callId=c.callId AND dd.callId=c.callId AND e.programId=c.programId ORDER BY Fecha_Llamada DESC")->queryAll();
          }          


          foreach ($varListLlamadas as $key => $value) {
            Yii::$app->get('dbQA')->createCommand()->insert('tbl_dashboardcategoriascalls',[
                                                           'callId' => $value['callId'],
                                                           'idcategoria' => $value['CAtegoriaID'],
                                                           'nombreCategoria' => $value['Nombre_Categoria'],
                                                           'extension' => $value['extension'],
                                                           'login_id' => $value['login_id'],
                                                           'fechallamada' => $value['Fecha_Llamada'],
                                                           'callduracion' => $value['cantidadllamadas'],
                                                           'servicio' => $value['Servicio'],
                                                           'fechacreacion' => date("Y-m-d"),
                                                           'anulado' => 0,
                                                           'usua_id' => Yii::$app->user->identity->id,
                                                        ])->execute();
          }

          Yii::$app->db->createCommand("delete from tbl_speech_generalcalls where anulado = 0 and programacliente in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00'")->execute();

          $varListProcesamiento = Yii::$app->db->createCommand("SELECT * FROM (select llama.callid, llama.login_id, llama.fechallamada, llama.servicio, llama.idcategoria as llamacategoria, cate.idcategoria as catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) as encuentra, llama.nombreCategoria from tbl_dashboardcategoriascalls llama left join (select idcategoria, tipoindicador, bolsitacategoria from tbl_speech_categoriascalidad where anulado = 0 and idcategorias in (1,2) and bolsitacategoria in ('$varBolsita') order by  tipoindicador) cate on llama.servicio = cate.bolsitacategoria where llama.servicio in ('$varBolsita') and llama.fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' group by llama.callid, llama.login_id, llama.idcategoria, cate.idcategoria  order by encuentra DESC) datos WHERE llamacategoria = catecategoria")->queryAll();

          foreach ($varListProcesamiento as $key => $value) {
            Yii::$app->get('dbQA')->createCommand()->insert('tbl_speech_generalcalls',[
                'programacliente' => $value['servicio'],
                'fechainicio' => $txtfechainicio,
                'fechafin' => null,
                'callid' => $value['callid'],
                'fechallamada' => $value['fechallamada'],
                'extension' => $value['login_id'],
                'idindicador' => $value['llamacategoria'],
                'idvariable' => $value['catecategoria'],
                'cantproceso' => $value['encuentra'],
                'fechacreacion' => date("Y-m-d"),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,
            ])->execute();
          }


          $varcategoriad1 = 9624;
          $varcategoriad2 = 9620;
          $varcategoriad3 = 9615;
          $varcategoriad4 = 9602;
          $varcategoriad5 = 9603;
          $varcategoriad6 = 9604;
          $varcategoriad7 = 9605;
          $varcategoriad8 = 9606;
          $varcategoriad9 = 9695;
          $varcategoriad10 = 9697;
          $varcategoriad11 = 9696;
          $varcategoriad12 = 9698;
          $varcategoriad13 = 9616;

          $varcategoriae1 = 9600;
          $varcategoriae2 = 9608;
          $varcategoriae3 = 9610;
          $varcategoriae4 = 9609;
          $varcategoriae5 = 6343;
          $varcategoriae6 = 6385;
          $varcategoriae7 = 6423;
          $varcategoriae8 = 6424;
          $varcategoriae9 = 6425;
          $varcategoriae10 = 6426;
          $varcategoriae11 = 6373;
          $varcategoriae12 = 9542;
          $varcategoriae13 = 9541;
          $varcategoriae14 = 6379;

          $varrtas = 0;

          if ($varBolsita == "CX_Directv") {
            $varListLogin = Yii::$app->db->createCommand("select d.login_id 'login', ce.cod_pcrc 'codpcrc' from tbl_calidad_entto ce   inner join tbl_dashboardcategoriascalls  d on ce.extension = d.login_id where d.anulado = 0 and d.servicio in ('$varBolsita') and d.fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and d.idcategoria = 1105 group by d.login_id")->queryAll();
          }else{
            $varListLogin = Yii::$app->db->createCommand("select d.login_id 'login', ce.cod_pcrc 'codpcrc' from tbl_calidad_entto ce   inner join tbl_dashboardcategoriascalls  d on ce.usuario_red = d.login_id where d.anulado = 0 and d.servicio in ('$varBolsita') and d.fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and d.idcategoria = 1105 group by d.login_id")->queryAll();
          }
          

          
          foreach ($varListLogin as $key => $value) {
            $varUsuarios = $value['login'];
            $varCodpcrcida = $value['codpcrc'];
            $varvaloraautomatica = null;

            $varvaloraautomatica = Yii::$app->db->createCommand("select count(callId) from tbl_dashboardcategoriascalls where anulado = 0 and idcategoria = 1105 and servicio in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and login_id in ('$varUsuarios')")->queryScalar();

            $varcortellamadad = null;
            $varevitademorad = null;
            $varevitareiteratividadd = null;
            $varseguirdadd = null;
            $varcedulad = null;
            $varcorreod = null;
            $varnombred = null;
            $vartelefonod = null;
            $varenviatiemposd = null;
            $var60a90d = null;
            $var30a60d = null;
            $var90a120d = null;
            $varevitavocabulariod = null;

            $varcortellamadae = null;
            $varevitademorae = null;
            $vardemora10e = null;
            $vardemora5a10e = null;
            $varevitareiteratividade = null;
            $varseguirdade = null;
            $varcedulae = null;
            $varcorreoe = null;
            $varnombree = null;
            $vartelefonoe = null;
            $varenviasilenciose = null;
            $varsilencios90e = null;
            $varsilencios60a90e = null;
            $varevitavocabularioe = null;
            
            Yii::$app->get('dbQA')->createCommand()->insert('tbl_categorias_ida',[
              'usuario_red' => $varUsuarios,
              'evita_corte_llamada_d' => $varcortellamadad,
              'evita_demora_contestar_d' => $varevitademorad,
              'evita_reiteratividad_silencios_d' => $varevitareiteratividadd,
              'seguridad_d' => $varseguirdadd,
              'cedula_d' => $varcedulad,
              'correo_electrónico_d' => $varcorreod,
              'nombre_completo_d' => $varnombred,
              'telefonos_d' => $vartelefonod,
              'evita_tiempos_espera_d' => $varenviatiemposd,
              'te_sil_60_90_seg_d' => $var60a90d,
              'te_sil_30_60_seg_d' => $var30a60d,
              'te_sil_90_120_seg_d' => $var90a120d,
              'evita_vocabulario_inadecuado_d' => $varevitavocabulariod,
              'evita_corte_llamada' => $varcortellamadae,
              'evita_demora_contestar' => $varevitademorae,
              'demora_mas_10_seg' => $vardemora10e,
              'demora_5_10_seg' => $vardemora5a10e,
              'evita_reiteratividad_silencios' => $varevitareiteratividade,
              'Seguridad' => $varseguirdade,
              'cedula' => $varcedulae,
              'correo_electrónico' => $varcorreoe,
              'nombre_completo' => $varnombree,
              'telefonos' => $vartelefonoe,
              'evita_silencios' => $varenviasilenciose,
              'silencios_mas_90_seg' => $varsilencios90e,
              'silencios_60_90_seg' => $varsilencios60a90e,
              'evita_vocabulario_inadecuado' => $varevitavocabularioe,
              'programa_pcrc' => $varBolsita,
              'valora_automatica' => $varvaloraautomatica,
              'ida' => null,
              'usua_id' => Yii::$app->user->identity->id,
              'fechacreacion' => $txtfechainicio,
              'anulado' => 0,
            ])->execute();

            $varcategorias = Yii::$app->db->createCommand("select * from tbl_speech_binario where anulado = 0 and bolsita in ('$varBolsita') and cod_pcrc in ('$varCodpcrcida') and speechelegir = 1")->queryAll();

            if (count($varcategorias) > 0) {
              
              $varListCallidida = Yii::$app->db->createCommand("select callId from tbl_dashboardcategoriascalls where anulado = 0 and idcategoria = 1105 and servicio in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and login_id in ('$varUsuarios')")->queryAll();  
              $varArraycategoriaida = 0;
              $varArraycategoriaidatotal = null;
              foreach ($varcategorias as $key => $value) {
                $varnamecategorias = $value['categorias'];
                $varidcategoriasida = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categoriascalidad where anulado = 0 and nombre in ('$varnamecategorias')")->queryScalar();

                $varorientacion = Yii::$app->db->createCommand("select orientacionentto from tbl_speech_categoriascalidad where anulado = 0 and idcategoria = $varidcategoriasida")->queryScalar();

                $varcategoriaidas = Yii::$app->db->createCommand("select idcategorias from tbl_speech_categoriascalidad where anulado = 0 and nombre in ('$varnamecategorias')")->queryScalar();

                $varArrayPromediocallid = array();
                if ($varcategoriaidas == 2) {
                  foreach ($varListCallidida as $key => $value) {
                    $varOneCallid = $value['callId'];

                    $varconteoida = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_generalcalls where anulado = 0 and programacliente in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and callid in ($varOneCallid) and idvariable in ($varidcategoriasida)")->queryScalar();

                    // Si la orientacion de la categoria es positiva (0)
                    // Se coloca uno, siempre y cuando se encuentre coincidiencias entre el callidid y categoria
                    // 1
                    // Si no encontra coincidencia entonces es 0

                    // Si la orientacion de la categoria es negativa (1)
                    // Se coloca cero, siempre y cuando no encuentre coincidencias entre el callid y categoria
                    // 1
                    // Si encuentra la coincidencia entonces es 0

                    if ($varorientacion == 0) {
                      if ($varconteoida != null || $varconteoida > 0) {
                        $rtaconteovar = 1;
                      }else{
                        $rtaconteovar = 0;
                      }
                    }else{
                      if ($varorientacion == 1) {
                        if ($varconteoida != null || $varconteoida > 0) {
                          $rtaconteovar = 0;
                        }else{
                          $rtaconteovar = 1;
                        }
                      }
                    }

                    array_push($varArrayPromediocallid, $rtaconteovar);
                  }
                }else{
                  if ($varcategoriaidas == 1) {
                    $vartienevar = Yii::$app->db->createCommand("select count(*) from tbl_speech_categoriascalidad where anulado = 0 and bolsitacategoria in ('$varBolsita') and tipoindicador in ('$varnamecategorias') and idcategorias = 2")->queryScalar();

                    // Si el indicador no tiene variables hago lo mismo de arriba
                    if ($vartienevar == 0) {
                      foreach ($varListCallidida as $key => $value) {
                        $varOneCallid = $value['callId'];

                        $varconteoida = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_generalcalls where anulado = 0 and programacliente in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and callid in ($varOneCallid) and idvariable in ($varidcategoriasida)")->queryScalar();

                        if ($varorientacion == 0) {
                          if ($varconteoida != null || $varconteoida > 0) {
                            $rtaconteovar = 1;
                          }else{
                            $rtaconteovar = 0;
                          }
                        }else{
                          if ($varorientacion == 1) {
                            if ($varconteoida != null || $varconteoida > 0) {
                              $rtaconteovar = 0;
                            }else{
                              $rtaconteovar = 1;
                            }
                          }
                        }

                        array_push($varArrayPromediocallid, $rtaconteovar);
                      }
                    }else{
                      $varlistindicadoresida = Yii::$app->db->createCommand("select * from tbl_speech_categoriascalidad where anulado = 0 and bolsitacategoria in ('$varBolsita') and tipoindicador in ($varnamecategorias') and idcategorias = 2")->queryAll();

                      foreach ($varlistindicadoresida as $key => $value) {
                        $varnombreida = $value['nombre'];

                        $varnombreidacount = Yii::$app->db->createCommand("select count(*) from tbl_speech_binario where anulado = 0 and bolsita in ('$varBolsita') and cod_pcrc in ('$varCodpcrcida') and categorias in ('$varnombreida ') and  and speechelegir = 1")->queryAll();

                        if ($varnombreidacount > 0) {
                          $varididacategoria = $value['idcategoria'];

                          $varorientaciones = Yii::$app->db->createCommand("select orientacionentto from tbl_speech_categoriascalidad where anulado = 0 and idcategoria = $varididacategoria")->queryScalar();

                          foreach ($varListCallidida as $key => $value) {
                            $varOneCallid = $value['callId'];

                            // Si la variable es positiva, buscar la categorizacion y sumar.
                            // Si la variable es negativa, buscar la categorizacion y sumar.

                            // El indicador es 1 si las variables positivas es mayor > 0 o si el contador de las variables negativas es 0
                            // El indicador es 0 si las variables positivas es 0 o las negativas son mayores a 0
                            if ($varorientacion == 0) {
                              $varconteoida = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_generalcalls where anulado = 0 and programacliente in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and callid in ($varOneCallid) and idvariable in ($varididacategoria)")->queryScalar();

                              if ($varconteoida != null || $varconteoida > 0) {
                                $rtaconteovar = 1;
                              }else{
                                $rtaconteovar = 0;
                              }
                            }else{
                              if ($varorientacion == 1) {
                                $varconteoida = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_generalcalls where anulado = 0 and programacliente in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and callid in ($varOneCallid) and idvariable in ($varididacategoria)")->queryScalar();

                                if ($varconteoida != null || $varconteoida > 0) {
                                  $rtaconteovar = 0;
                                }else{
                                  $rtaconteovar = 1;
                                }
                              }
                            }

                            array_push($varArrayPromediocallid, $rtaconteovar);

                          }
                        }else{
                          foreach ($varListCallidida as $key => $value) {
                            $varOneCallid = $value['callId'];

                            $varconteoida = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_generalcalls where anulado = 0 and programacliente in ('$varBolsita') and fechallamada between '$txtfechainicio 05:00:00' and '$txtfechafin 05:00:00' and callid in ($varOneCallid) and idvariable in ($varidcategoriasida)")->queryScalar();

                            if ($varorientacion == 0) {
                              if ($varconteoida != null || $varconteoida > 0) {
                                $rtaconteovar = 1;
                              }else{
                                $rtaconteovar = 0;
                              }
                            }else{
                              if ($varorientacion == 1) {
                                if ($varconteoida != null || $varconteoida > 0) {
                                  $rtaconteovar = 0;
                                }else{
                                  $rtaconteovar = 1;
                                }
                              }
                            }

                            array_push($varArrayPromediocallid, $rtaconteovar);
                          }
                        }
                      }
                    }
                  }
                }               
                

                $varArraycategoriaida = array_sum($varArrayPromediocallid) / count($varListCallidida);


                if ($varcategoriad1 == $varidcategoriasida) {
                  $varcortellamadad = $varArraycategoriaida;

                  Yii::$app->db->createCommand("update tbl_categorias_ida set evita_corte_llamada_d = $varcortellamadad where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                }else{
                  if ($varcategoriad2 == $varidcategoriasida) {
                    $varevitademorad = $varArraycategoriaida;

                    Yii::$app->db->createCommand("update tbl_categorias_ida set evita_demora_contestar_d = $varevitademorad where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                  }else{
                    if ($varcategoriad3 == $varidcategoriasida) {
                      $varevitareiteratividadd = $varArraycategoriaida;

                      Yii::$app->db->createCommand("update tbl_categorias_ida set evita_reiteratividad_silencios_d = $varevitareiteratividadd where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                    }else{
                      if ($varcategoriad4 == $varidcategoriasida) {
                        $varseguirdadd =  $varArraycategoriaida;

                        Yii::$app->db->createCommand("update tbl_categorias_ida set seguridad_d = $varseguirdadd where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                      }else{
                        if ($varcategoriad5 == $varidcategoriasida) {
                          $varcedulad =  $varArraycategoriaida;

                          Yii::$app->db->createCommand("update tbl_categorias_ida set cedula_d = $varcedulad where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                        }else{
                          if ($varcategoriad6 == $varidcategoriasida) {
                            $varcorreod  =  $varArraycategoriaida;

                            Yii::$app->db->createCommand("update tbl_categorias_ida set correo_electrónico_d = $varcorreod where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                          }else{
                            if ($varcategoriad7 == $varidcategoriasida) {
                              $varnombred =  $varArraycategoriaida;

                              Yii::$app->db->createCommand("update tbl_categorias_ida set nombre_completo_d = $varnombred where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                            }else{
                              if ($varcategoriad8 == $varidcategoriasida) {
                                $vartelefonod =  $varArraycategoriaida;

                                Yii::$app->db->createCommand("update tbl_categorias_ida set telefonos_d = $vartelefonod where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                              }else{
                                if ($varcategoriad9 == $varidcategoriasida) {
                                  $varenviatiemposd = $varArraycategoriaida;

                                  Yii::$app->db->createCommand("update tbl_categorias_ida set evita_tiempos_espera_d = $varenviatiemposd where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                                }else{
                                  if ($varcategoriad10 == $varidcategoriasida) {
                                    $var60a90d = $varArraycategoriaida;

                                    Yii::$app->db->createCommand("update tbl_categorias_ida set  te_sil_60_90_seg_d = $var60a90d where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                                  }else{
                                    if ($varcategoriad11 == $varidcategoriasida) {
                                      $var30a60d = $varArraycategoriaida;

                                      Yii::$app->db->createCommand("update tbl_categorias_ida set te_sil_30_60_seg_d = $var30a60d where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                                    }else{
                                      if ($varcategoriad12 == $varidcategoriasida) {
                                        $var90a120d = $varArraycategoriaida;

                                        Yii::$app->db->createCommand("update tbl_categorias_ida set te_sil_90_120_seg_d = $var90a120d where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                                      }else{
                                        if ($varcategoriad13 == $varidcategoriasida) {
                                          $varevitavocabulariod = $varArraycategoriaida;

                                          Yii::$app->db->createCommand("update tbl_categorias_ida set evita_vocabulario_inadecuado_d = $varevitavocabulariod where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                                        }else{
                                          if ($varcategoriae1 == $varidcategoriasida) {
                                            $varcortellamadae = $varArraycategoriaida;

                                            Yii::$app->db->createCommand("update tbl_categorias_ida set evita_corte_llamada = $varcortellamadae where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                                          }else{
                                            if ($varcategoriae2 == $varidcategoriasida) {
                                              $varevitademorae = $varArraycategoriaida;

                                              Yii::$app->db->createCommand("update tbl_categorias_ida set  evita_demora_contestar = $varevitademorae where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

                                            }else{
                                              if ($varcategoriae3 == $varidcategoriasida) {
                                                $vardemora10e = $varArraycategoriaida;

                                                Yii::$app->db->createCommand("update tbl_categorias_ida set demora_mas_10_seg = $vardemora10e where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                              }else{
                                                if ($varcategoriae4 == $varidcategoriasida) {
                                                  $vardemora5a10e = $varArraycategoriaida;

                                                  Yii::$app->db->createCommand("update tbl_categorias_ida set demora_5_10_seg = $vardemora5a10e where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                }else{
                                                  if ($varcategoriae5 == $varidcategoriasida) {
                                                    $varevitareiteratividade = $varArraycategoriaida;

                                                    Yii::$app->db->createCommand("update tbl_categorias_ida set  evita_reiteratividad_silencios = $varevitareiteratividade where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                  }else{
                                                    if ($varcategoriae6 == $varidcategoriasida) {
                                                      $varseguirdade = $varArraycategoriaida;

                                                      Yii::$app->db->createCommand("update tbl_categorias_ida set Seguridad = $varseguirdade where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                    }else{
                                                      if ($varcategoriae7 == $varidcategoriasida) {
                                                        $varcedulae = $varArraycategoriaida;

                                                        Yii::$app->db->createCommand("update tbl_categorias_ida set  cedula = $varcedulae where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                      }else{
                                                        if ($varcategoriae8 == $varidcategoriasida) {
                                                          $varcorreoe = $varArraycategoriaida;

                                                          Yii::$app->db->createCommand("update tbl_categorias_ida set  correo_electrónico = $varcorreoe where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                        }else{
                                                          if ($varcategoriae9 == $varidcategoriasida) {
                                                            $varnombree = $varArraycategoriaida;

                                                            Yii::$app->db->createCommand("update tbl_categorias_ida set  nombre_completo = $varnombree where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                          }else{
                                                            if ($varcategoriae10 == $varidcategoriasida) {
                                                              $vartelefonoe = $varArraycategoriaida;

                                                              Yii::$app->db->createCommand("update tbl_categorias_ida set  telefonos = $vartelefonoe where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                            }else{
                                                              if ($varcategoriae11 == $varidcategoriasida) {
                                                                $varenviasilenciose = $varArraycategoriaida;

                                                                Yii::$app->db->createCommand("update tbl_categorias_ida set  evita_silencios = $varenviasilenciose where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                              }else{
                                                                if ($varcategoriae12 == $varidcategoriasida) {
                                                                  $varsilencios90e = $varArraycategoriaida;

                                                                  Yii::$app->db->createCommand("update tbl_categorias_ida set  silencios_mas_90_seg = $varsilencios90e where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                                }else{
                                                                  if ($varcategoriae13 == $varidcategoriasida) {
                                                                    $varsilencios60a90e = $varArraycategoriaida;

                                                                    Yii::$app->db->createCommand("update tbl_categorias_ida set silencios_60_90_seg = $varsilencios60a90e where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                                  }else{
                                                                    if ($varcategoriae14 == $varidcategoriasida) {
                                                                      $varevitavocabularioe = $varArraycategoriaida;

                                                                      Yii::$app->db->createCommand("update tbl_categorias_ida set evita_vocabulario_inadecuado = $varevitavocabularioe where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();
                                              
                                                                    }
                                                                  }
                                                                }
                                                              }
                                                            }
                                                          }
                                                        }
                                                      }
                                                    }
                                                  }
                                                }
                                              }
                                            }
                                          }
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
                }
              }


              $varArraycategoriaidatotal = ($varcortellamadad + $varevitademorad + $varevitareiteratividadd + $varseguirdadd + $varcedulad + $varcorreod + $varnombred + $vartelefonod + $varenviatiemposd + $var60a90d + $var30a60d + $var90a120d + $varevitavocabulariod + $varcortellamadae + $varevitademorae + $vardemora10e + $vardemora5a10e + $varevitareiteratividade + $varseguirdade + $varcedulae + $varcorreoe + $varnombree + $vartelefonoe + $varenviasilenciose + $varsilencios90e + $varsilencios60a90e + $varevitavocabularioe) / count($varcategorias);

              Yii::$app->db->createCommand("update tbl_categorias_ida set ida = $varArraycategoriaidatotal where anulado = 0 and usuario_red in ('$varUsuarios') and programa_pcrc in ('$varBolsita') and fechacreacion = '$txtfechainicio'")->execute();

            }
          }
          
          

          die(json_encode(count($varrtas)));
        }


    public function actionGuardarindicadores(){
      $varServicio = Yii::$app->request->get('txtvarvarServicio');
      $txtCodPcrcokc1 = Yii::$app->request->get('txtvartxtCodPcrcokc1');
      $txtnombrepcrc = Yii::$app->request->get('txtvartxtnombrepcrc');
      $txtFechaIni = Yii::$app->request->get('txtvartxtFechaIni');
      $txtFechaFin = Yii::$app->request->get('txtvartxtFechaFin');
      $varParams = Yii::$app->request->get('txtvarvarParams');
      $txtTotalLlamadas = Yii::$app->request->get('txtvartxtTotalLlamadas');
      $txtvaridnameindi = Yii::$app->request->get('txtvaridnameindi');
      $txtvaridporcentaje = Yii::$app->request->get('txtvaridporcentaje');

      $txtYears = date("Y", strtotime($txtFechaIni));
      $txtMeses = date("m", strtotime($txtFechaIni));

      Yii::$app->db->createCommand()->insert('tbl_speech_evolutivos',[
                                           'ev_servicio' => $varServicio,
                                           'ev_cod_pcrc' => $txtCodPcrcokc1,
                                           'ev_pcrc' => $txtnombrepcrc,
                                           'ev_extension' => $varParams,
                                           'ev_fechainicio' => $txtFechaIni,
                                           'ev_fechafin' => $txtFechaFin,
                                           'ev_mes' => $txtMeses,
                                           'ev_year' => $txtYears,
                                           'ev_llamadas' => $txtTotalLlamadas,
                                           'ev_indicador' => $txtvaridnameindi,
                                           'ev_porcentaje' => $txtvaridporcentaje,
                                           'usua_id' => Yii::$app->user->identity->id,
                                           'anulado' => 0,
                                           'fechacreacion' => date("Y-m-d"),                                           
                                       ])->execute();



      die(json_encode(0));
    }

        public function actionSearchllamadas($varprograma,$varcodigopcrc,$varidcategoria,$varextension,$varfechasinicio,$varfechasfin,$varcantllamadas,$varfechainireal,$varfechafinreal,$varcodigos,$varaleatorios){
          $model = new Dashboardspeechcalls();
          $txtvarprograma = $varprograma;
          $txtvarcodigopcrc = $varcodigopcrc;
          $txtvaridcategoria = $varidcategoria;
          $txtvarextension = $varextension;
          $txtvarfechasinicio = $varfechasinicio;
          $txtvarfechasfin = $varfechasfin;
          $txtvarcantllamadas = $varcantllamadas;
          $txtvarfechainireal = $varfechainireal;
          $txtvarfechafinreal = $varfechafinreal;
          $txtvarcodigos = $varcodigos;
          $txttxtvarcantllamadasb = 0;
          $varcategoriass = null;   
          $varidloginid = null;       
          $vartipologia = null;
          $varAelatorio = $varaleatorios;

          $paramscalls = Yii::$app->db->createCommand("select ss.idllamada from tbl_speech_servicios ss  inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtvarcodigopcrc') group by sp.cod_pcrc")->queryScalar(); 

          $form = Yii::$app->request->post();
          if ($model->load($form)) {            
            $varidspeechindi = null;
            $varidspeechvar = null;
            $varidmotivos = null;
            
            $varidspeechindi = $model->idcategoria;
            $varidspeechvar = $model->nombreCategoria;
            $varidmotivos = $model->extension;
            $varidloginid = $model->login_id;      
            $varlider = $model->servicio;
            $varasesor = $model->fechallamada;

            // Nuevo Filtro - Tipologias
            $vartipologia = $model->idredbox;

            // Si el filtro es Contiene dejo el proceso normal.
            if ($varidloginid == "1") {
              if ($varidspeechindi != null && $varidspeechvar == null && $varidmotivos == null) {
                $varcategoriass = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varidspeechindi group by idcategoria")->queryScalar();
              }

              if ($varidspeechindi != null && $varidspeechvar != null && $varidmotivos == null) {
                $varcategoriass = $varidspeechvar;
              }

              if ($varidspeechindi == null && $varidspeechvar == null && $varidmotivos != null) {
                $varcategoriass = $varidmotivos;
              }

              if ($varidspeechindi != null && $varidspeechvar == null && $varidmotivos != null) {
                $varconeto = 1;
                $varidspeechindicador = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varidspeechindi group by idcategoria")->queryScalar();
                $varcategoriass = $varidspeechindicador.", ".$varidmotivos;
              }

              if ($varidspeechindi != null && $varidspeechvar != null && $varidmotivos != null) {
                $varconeto = 2;
                $varcategoriass = $varidspeechvar.", ".$varidmotivos;
              } 
            }else{

              if ($varidspeechindi != null && $varidspeechvar == null && $varidmotivos == null) {
                $varcategoriass = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varidspeechindi group by idcategoria")->queryScalar();
              }

              if ($varidspeechindi != null && $varidspeechvar != null && $varidmotivos == null) {
                $varcategoriass = $varidspeechvar;
              }

              if ($varidspeechindi == null && $varidspeechvar == null && $varidmotivos != null) {
                $varcategoriass = $varidmotivos;
              }

              if ($varidspeechindi != null && $varidspeechvar == null && $varidmotivos != null) {                
                $varidspeechindicador = Yii::$app->db->createCommand("select idcategoria from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varidspeechindi group by idcategoria")->queryScalar();
                $varcategoriass = $varidspeechindicador.", ".$varidmotivos;
              }

              if ($varidspeechindi != null && $varidspeechvar != null && $varidmotivos != null) {
                $varcategoriass = $varidspeechvar.", ".$varidmotivos;
              } 

            }
            
            $params1 = $txtvarprograma;
            $params2 = $txtvarextension;
            $params3 = $txtvarfechasinicio;
            $params4 = $txtvarfechasfin;

            $dataProvider = $model->buscarsllamadas($params1,$params2,$params3,$params4,$varcategoriass,$varidloginid,$paramscalls,$varlider,$varasesor,$vartipologia);

            $txtresultadoasesor = null;

            if ($varasesor == "") {
                $txtresultadoasesor = Yii::$app->db->createCommand("select distinct e.dsusuario_red from tbl_evaluados e     inner join tbl_equipos_evaluados ee on e.id = ee.evaluado_id where ee.equipo_id in ('$varlider') and e.dsusuario_red not like '%usar%'")->queryAll();

                $arraylistasesores = array();
                foreach ($txtresultadoasesor as $key => $value) {
                    array_push($arraylistasesores, $value['dsusuario_red']);
                }
                $txtarrayasesores = implode("', '", $arraylistasesores);
            }else{
                $txtarrayasesores = $varasesor;
            }
                        
            $txttxtvarcantllamadasb = $dataProvider->getTotalCount();

          }else{
            $params1 = $txtvarprograma;
            $params2 = $txtvarextension;
            $params3 = $txtvarfechasinicio;
            $params4 = $txtvarfechasfin;  
            $params5 = $txtvarcodigopcrc;
            $params6 = $varAelatorio;                     

            $dataProvider = $model->buscarsllamadasmodel($params1,$params2,$params3,$params4,$paramscalls,$params5,$params6);

            if ($params6 == "1") {
              $txttxtvarcantllamadasb = $dataProvider->getTotalCount();
            }

            $varcategoriass = 0;
            $varidloginid = 0;
          }

          return $this->render('searchllamadas',[
            'txtvarprograma' => $txtvarprograma,
            'txtvarcodigopcrc' => $txtvarcodigopcrc,
            'txtvaridcategoria' => $txtvaridcategoria,
            'txtvarextension' => $txtvarextension,
            'txtvarfechasinicio' => $txtvarfechasinicio,
            'txtvarfechasfin' => $txtvarfechasfin,
            'txtvarcantllamadas' => $txtvarcantllamadas,
            'model' => $model,
            'txtvarfechainireal' => $txtvarfechainireal,
            'txtvarfechafinreal' => $txtvarfechafinreal,
            'dataProvider' => $dataProvider,
            'txttxtvarcantllamadasb' => $txttxtvarcantllamadasb,
            'txtvarcodigos' => $txtvarcodigos,
            'varcategoriass' => $varcategoriass,
            'varidloginid' => $varidloginid,
            ]);
        }

        public function actionListarvariablesx(){
          $txtidspeech = Yii::$app->request->get('id');

          if ($txtidspeech) {
            $txtcodigopcrcx = Yii::$app->db->createCommand("select s.cod_pcrc from tbl_speech_categorias s where s.idspeechcategoria = $txtidspeech and s.anulado = 0")->queryScalar();
            $txtindicadorx = Yii::$app->db->createCommand("select s.nombre from tbl_speech_categorias s where s.idspeechcategoria = $txtidspeech and s.anulado = 0")->queryScalar();

            $txtControl = \app\models\SpeechCategorias::find()->distinct()  
            ->select(['idcategoria','nombre'])        
            ->where("cod_pcrc in ('$txtcodigopcrcx')")  
            ->andwhere("tipoindicador like '$txtindicadorx'")  
            ->andwhere("idcategorias = 2")
            ->andwhere("anulado = 0")  
            ->count(); 

            if ($txtControl > 0) {
              $varListaVariablesx = \app\models\SpeechCategorias::find()->distinct()  
              ->select(['idcategoria','nombre'])        
              ->where(['cod_pcrc' => $txtcodigopcrcx])  
              ->andwhere("tipoindicador like '$txtindicadorx'")  
              ->andwhere("idcategorias = 2")
              ->andwhere("anulado = 0")  
              ->orderBy(['nombre' => SORT_DESC])
              ->all();   

              echo "<option value='' disabled selected>Seleccionar variable...</option>";
              foreach ($varListaVariablesx as $key => $value) {
                echo "<option value='" . $value->idcategoria. "'>" . $value->nombre . "</option>";
              }
            }else{
              echo "<option>--</option>";
            }
          }else{
            echo "<option>Seleccionar variable</option>";
          }                    
        }

    public function actionDescargarcalls($varprograma,$varcodigopcrc,$varidcategoria,$varextension,$varfechasinicio,$varfechasfin,$varcantllamadas,$varfechainireal,$varfechafinreal,$consinmotivos){
      $model = new Dashboardspeechcalls();
      $txtvarprograma = $varprograma;
      $txtvarcodigopcrc = $varcodigopcrc;
      $txtvarextension = $varextension;
      $txtvarfechasinicio = $varfechasinicio;
      $txtvarfechasfin = $varfechasfin;
      $txtvarcantllamadas = $varcantllamadas;
      $txtvarfechainireal = $varfechainireal;
      $txtvarfechafinreal = $varfechafinreal;
      $txttotalllamadasd = null;
      $txtnombrepcrc = null;
      $varlistcalls = null;
      $txtcentrocostos = null;
      $varCorreo = null;

      $txtnombrepcrc = Yii::$app->db->createCommand("SELECT programacategoria FROM tbl_speech_categorias WHERE cod_pcrc IN ('$txtvarcodigopcrc') GROUP BY programacategoria")->queryScalar();
      $txtcentrocostos = Yii::$app->db->createCommand("SELECT CONCAT(pcrc,' - ',cod_pcrc) FROM tbl_speech_categorias WHERE cod_pcrc IN ('$txtvarcodigopcrc') GROUP BY programacategoria")->queryScalar();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varCorreo = $model->servicio;
        $varnombre = 'Gestion del tecnico escuchar + 2.0 - '.$txtcentrocostos.' -';
        $varKonecta = "Konecta";

        $phpExc = new \PHPExcel();
        $phpExc->getProperties()
                ->setCreator($varKonecta)
                ->setLastModifiedBy($varKonecta)
                ->setTitle($varnombre)
                ->setSubject($varnombre)
                ->setDescription("Archivo que permite verificar la gestion del tecnico con valoraciones automaticas.")
                ->setKeywords($varnombre);
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
        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:O1');

        $phpExc->getActiveSheet()->SetCellValue('A2',$varnombre);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySize);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A2:O2');

        $phpExc->getActiveSheet()->SetCellValue('A3','Rango de Fecha');
        $phpExc->setActiveSheetIndex(0)->mergeCells('A3:C3');
        $phpExc->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('D3','Servicio');
        $phpExc->setActiveSheetIndex(0)->mergeCells('D3:F3');
        $phpExc->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('G3','Programa/Pcrc');
        $phpExc->setActiveSheetIndex(0)->mergeCells('G3:N3');
        $phpExc->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('O3','Llamadas General');
        $phpExc->getActiveSheet()->getStyle('O3')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('O3')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('O3')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('O3')->applyFromArray($styleArrayTitle);

        $numCell = 4;
        $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $txtvarfechainireal.' - '.$txtvarfechafinreal);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A'.$numCell.':C'.$numCell);

        $varnombreservicio = Yii::$app->db->createCommand("SELECT ss.nameArbol FROM tbl_speech_servicios ss INNER JOIN tbl_speech_parametrizar sp ON ss.id_dp_clientes = sp.id_dp_clientes  WHERE sp.cod_pcrc IN ('$txtvarcodigopcrc') AND sp.anulado = 0 GROUP BY ss.arbol_id")->queryScalar();
        $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $varnombreservicio);
        $phpExc->setActiveSheetIndex(0)->mergeCells('D'.$numCell.':F'.$numCell);
        
        $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $txtcentrocostos);
        $phpExc->setActiveSheetIndex(0)->mergeCells('G'.$numCell.':N'.$numCell);

        $phpExc->getActiveSheet()->setCellValue('O'.$numCell, $txtvarcantllamadas);

        $phpExc->getActiveSheet()->SetCellValue('A5','');
        $phpExc->getActiveSheet()->getStyle('A5')->applyFromArray($styleArraySize);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A5:O5');

        $phpExc->getActiveSheet()->SetCellValue('A6','ID de la Llamada');
        $phpExc->getActiveSheet()->getStyle('A6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('B6','Fecha de la Llamada');
        $phpExc->getActiveSheet()->getStyle('B6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('B6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('B6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('B6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('C6','Asesor');
        $phpExc->getActiveSheet()->getStyle('C6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('C6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('D6','Id Redbox');
        $phpExc->getActiveSheet()->getStyle('D6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('D6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('E6','Encuesta');
        $phpExc->getActiveSheet()->getStyle('E6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('F6','Tipologia');
        $phpExc->getActiveSheet()->getStyle('F6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('F6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('F6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('F6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('G6','Buzon');
        $phpExc->getActiveSheet()->getStyle('G6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('G6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('H6','Estado');
        $phpExc->getActiveSheet()->getStyle('H6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('H6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('I6','Valorador');
        $phpExc->getActiveSheet()->getStyle('I6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('I6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('I6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('I6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('J6','Marca');
        $phpExc->getActiveSheet()->getStyle('J6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('J6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('J6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('J6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('K6','Canal');
        $phpExc->getActiveSheet()->getStyle('K6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('K6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('K6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('K6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('L6','Agente');
        $phpExc->getActiveSheet()->getStyle('L6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('L6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('L6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('L6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('M6','Resultado IDA');
        $phpExc->getActiveSheet()->getStyle('M6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('M6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('M6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('M6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('N6','Calidad & Consistencia');
        $phpExc->getActiveSheet()->getStyle('N6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('N6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('N6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('N6')->applyFromArray($styleArrayTitle);

        $phpExc->getActiveSheet()->SetCellValue('O6','Score Valoracion');
        $phpExc->getActiveSheet()->getStyle('O6')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('O6')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('O6')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('O6')->applyFromArray($styleArrayTitle);

        $arraydasboardlist = Yii::$app->db->createCommand("SELECT iddashboardspeechcalls, callId, login_id, fechareal, idredbox, connid  FROM tbl_dashboardspeechcalls WHERE anulado = 0 AND servicio IN ('$txtvarprograma')
        AND extension IN ('$txtvarextension') AND fechallamada BETWEEN '$txtvarfechasinicio' AND '$txtvarfechasfin'")->queryAll();
        
        $numCell = 7;
        foreach ($arraydasboardlist as $key => $value) {
          $varconnid = $value['connid'];
          $varidspeech = $value['iddashboardspeechcalls'];
          
          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['callId']);
          $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['fechareal']);
          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['login_id']);
          $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['idredbox']);

          $varbaseencuesta = Yii::$app->db->createCommand("select b.id from tbl_base_satisfaccion b where b.connid in ('$varconnid')")->queryScalar();
          if ($varbaseencuesta == "") {
            $varbaseencuesta = "--";
          }
          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $varbaseencuesta);

          $varbasetipologia = Yii::$app->db->createCommand("select b.tipologia from tbl_base_satisfaccion b where b.connid in ('$varconnid')")->queryScalar();
          if ($varbasetipologia == "") {
            $varbasetipologia = "--";
          }
          $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $varbasetipologia);

          $varbasebuzon = Yii::$app->db->createCommand("select b.buzon from tbl_base_satisfaccion b where b.connid in ('$varconnid')")->queryScalar();
          if ($varbasebuzon == "") {
            $varbasebuzon = "--";
          }
          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $varbasebuzon);

          $concatenarspeech = Yii::$app->db->createCommand("SELECT DISTINCT CONCAT(d.callId,'; ',d.fechareal) FROM  tbl_dashboardspeechcalls d WHERE d.iddashboardspeechcalls in ('$varidspeech')")->queryScalar();
          $txttempejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_tmpejecucionformularios te WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();
          $txtejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_ejecucionformularios te WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();
          if ($txttempejecucion == 0 && $txtejecucion == 0) {
              $dataEstado = "Abierto";
          }else{
              if ($txttempejecucion == 1 && $txtejecucion == 0) {
                  $dataEstado = "En Proceso";
              }else{
                  if ($txttempejecucion == 0 && $txtejecucion == 1) {
                      $dataEstado = "Cerrado";
                  }
              }
          }
          $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $dataEstado);


          if ($txttempejecucion == 0 && $txtejecucion == 0) {              
              $dataValorador = "--";
          }else{
              if ($txttempejecucion == 1 && $txtejecucion == 0) {
                  $dataValorador = Yii::$app->db->createCommand("SELECT DISTINCT u.usua_nombre FROM tbl_usuarios u INNER JOIN tbl_tmpejecucionformularios te ON u.usua_id = te.usua_id WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();
              }else{
                  if ($txttempejecucion == 0 && $txtejecucion == 1) {
                      $dataValorador = Yii::$app->db->createCommand("SELECT DISTINCT u.usua_nombre FROM tbl_usuarios u INNER JOIN tbl_ejecucionformularios te ON u.usua_id = te.usua_id WHERE te.dsfuente_encuesta = '$concatenarspeech'")->queryScalar();
                  }
              }
          }
          $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $dataValorador);

          $dataResponsabilidadM = null;
          $dataResponsabilidadC = null;
          $dataResponsabilidadA = null;
                           
          
          $phpExc->getActiveSheet()->setCellValue('J'.$numCell, $dataResponsabilidadM);
          $phpExc->getActiveSheet()->setCellValue('K'.$numCell, $dataResponsabilidadC);
          $phpExc->getActiveSheet()->setCellValue('L'.$numCell, $dataResponsabilidadA);

          




          $numCell++;
        }




        $hoy = getdate();
        $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_GestionTecnico_".$txtcentrocostos;
              
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
        $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
        $tmpFile.= ".xls";

        $objWriter->save($tmpFile);

        $message = "<html><body>";
        $message .= "<h3>Se ha realizado el envio correcto del archivo de la gestion del tecnico, procesamiento automatico.</h3>";
        $message .= "</body></html>";

        Yii::$app->mailer->compose()
                        ->setTo($varCorreo)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Envio de la gestion del tecnico ".$txtcentrocostos)
                        ->attach($tmpFile)
                        ->setHtmlBody($message)
                        ->send();
        
        
      }     



      return $this->renderAjax('descargarcalls',[
        'model' => $model,
        'txttotalllamadasd' => $txttotalllamadasd,
        'txtvarcantllamadas' => $txtvarcantllamadas,
        'txtnombrepcrc' => $txtnombrepcrc,
        'varlistcalls' => $varlistcalls,
        'txtvarcodigopcrc' => $txtvarcodigopcrc,
        'txtvarfechainireal' => $txtvarfechainireal,
        'txtvarfechafinreal' => $txtvarfechafinreal,
        ]);
    }

    public function actionViewcalls(){
      $varidlogin = Yii::$app->request->get('idlogin');
      $varidredbox = Yii::$app->request->get('idredbox');  
      $varidgrabadora = Yii::$app->request->get('idgrabadora');
      $varidconnid = Yii::$app->request->get('idconnid');

      $varidcallids = Yii::$app->request->get('idcallids');
      $varvarfechareal = Yii::$app->request->get('varfechareal');
      $varvarcategolias = Yii::$app->request->get('varcategolias');      

      $varResultado = null;
      $vartexto = $varidconnid;
      $varvalencia = null;


      if ($varidredbox != "" && $varidgrabadora != "") {
        ob_start();
        $curl = curl_init();
        curl_setopt_array($curl, array(
          CURLOPT_URL => 'http://172.20.212.12/ASULWSRedboxReproducirAudio/ASULWSREDBOXReproducirAudioPantalla.asmx',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'<?xml version="1.0" encoding="utf-8"?>
            <soap12:Envelope xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:soap12="http://www.w3.org/2003/05/soap-envelope">
              <soap12:Body>
                <ASULObtenerURLReproduccionREDBOX xmlns="http://tempuri.org/">
                  <strIdGrabadora>'.$varidgrabadora.'</strIdGrabadora>
                  <strIdLlamada>'.$varidredbox.'</strIdLlamada>
                </ASULObtenerURLReproduccionREDBOX>
              </soap12:Body>
            </soap12:Envelope>',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/soap+xml; charset=utf-8'
          ),
          ));

        $response = curl_exec($curl);
        curl_close($curl);
        ob_clean();

        $elementos = array_values(explode( "URL" ,  (string)$response ) )[3];
        $elementos = substr( $elementos,4,-5 );

        $varResultado = $elementos;

      }else{
        $varidredbox = 0;
        $varidgrabadora = 0;        
      }

      if ($varidconnid != null) {
        $varidconnid = Yii::$app->db->createCommand("SELECT b.connid FROM tbl_base_satisfaccion b WHERE b.connid in ('$varidconnid') ")->queryScalar();
        ob_start();
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_SSL_VERIFYPEER=> false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_URL => 'https://api-kaliope.analiticagrupokonectacloud.com/status-by-connid',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{"connid": "'.$varidconnid.'"}',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        var_dump($response);

        curl_close($curl);
        ob_clean();

        if (!$response) {
          
          $vartexto = "Error al buscar transcipcion";
          $varvalencia = "Error al buscar valencia emocioanl";
        }

        $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

        if (count($response) == 0) {
          
          $vartexto = "Transcripcion no encontrada";
          $varvalencia = "Valencia emocional no encontrada";
        }else{
          $vartexto = $response[0]['transcription'];
          $varvalencia = $response[0]['valencia'];
        }

      }else{
        $vartexto = "No aplica";
        $varvalencia = "No aplica";
      }      

      $paramsBusquedaExtension = [':varCallid' => $varidcallids, ':varFecha' => $varvarfechareal, ':varCategoria' => $varvarcategolias, ':varAnulado' => 0];

      $varextensionnum = Yii::$app->db->createCommand('
        SELECT d.extensiones FROM tbl_dashboardspeechcalls d
          WHERE d.anulado = :varAnulado 
            AND d.callId = :varCallid
              AND d.fechareal = :varFecha 
                AND d.idcategoria = :varCategoria
          GROUP BY d.extensiones
      ')->bindValues($paramsBusquedaExtension)->queryScalar();     

      return $this->renderAjax('viewcalls',[
        'varidlogin' => $varidlogin,
        'varidredbox' => $varidredbox,
        'varidgrabadora' => $varidgrabadora,
        'varResultado' => $varResultado,
        'vartexto' => $vartexto,
        'varvalencia' => $varvalencia,
        'varextensionnum' => $varextensionnum,
        'varidcallids' => $varidcallids,
        'varvarfechareal' => $varvarfechareal,
        ]);
    }

    public function actionViewna(){

      return $this->renderAjax('viewna');
    }

    public function actionClonarextension(){
      $model = new SpeechParametrizar();        

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $txtiddpcliente = $model->id_dp_clientes;

        $varlistidclientes = Yii::$app->db->createCommand("select * from tbl_speech_parametrizar s where s.id_dp_clientes = $txtiddpcliente and s.anulado = 0")->queryAll();

        foreach ($varlistidclientes as $key => $value) {
          $txtreglanegocioc = $value['rn'];
          $txtextensionesc = $value['ext'];
          $txtusuaredc = $value['usuared'];
          $txtotrosc = $value['comentarios'];

          $txtcodpcrc = $value['cod_pcrc'];

          $txtreglanegocioo = $value['rn'];
          $txtextensioneso = $value['ext'];
          $txtusuaredo = $value['usuared'];
          $txtotroso = $value['comentarios'];




          if ($txtreglanegocioc != "") {
            $txtreglanegocioc = $txtreglanegocioc.'C';
          }
          if ($txtextensionesc != "") {
            $txtextensionesc = $txtextensionesc.'C';
          }
          if ($txtusuaredc != "") {
            $txtusuaredc = $txtusuaredc.'C';
          }
          if ($txtotrosc != "") {
            $txtotrosc = $txtotrosc.'C';
          }

          Yii::$app->db->createCommand()->insert('tbl_speech_parametrizar',[
                                           'id_dp_clientes' => $txtiddpcliente,
                                           'rn' => $txtreglanegocioc,
                                           'ext' => $txtextensionesc,
                                           'usuared' => $txtusuaredc,
                                           'comentarios' => $txtotrosc,
                                           'cod_pcrc' => $txtcodpcrc,
                                           'usua_id' => Yii::$app->user->identity->id,
                                           'anulado' => 0,
                                           'fechacreacion' => date("Y-m-d"),     
                                           'tipoparametro' => 1,                                        
                                       ])->execute();

          if ($txtreglanegocioo != "") {
            $txtreglanegocioo = $txtreglanegocioo.'O';
          }
          if ($txtextensioneso != "") {
            $txtextensioneso = $txtextensioneso.'O';
          }
          if ($txtusuaredo != "") {
            $txtusuaredo = $txtusuaredo.'O';
          }
          if ($txtotroso != "") {
            $txtotroso = $txtotroso.'O';
          }

          Yii::$app->db->createCommand()->insert('tbl_speech_parametrizar',[
                                           'id_dp_clientes' => $txtiddpcliente,
                                           'rn' => $txtreglanegocioo,
                                           'ext' => $txtextensioneso,
                                           'usuared' => $txtusuaredo,
                                           'comentarios' => $txtotroso,
                                           'cod_pcrc' => $txtcodpcrc,
                                           'usua_id' => Yii::$app->user->identity->id,
                                           'anulado' => 0,
                                           'fechacreacion' => date("Y-m-d"),   
                                           'tipoparametro' => 2,                                          
                                       ])->execute();

        }
        
      }

      return $this->render('clonarextension',[
        'model' => $model,
      ]);
    }

// Diego responsabilidad

      public function actionTotalizaragentes($arbol_idV, $parametros_idV, $codparametrizar, $codigoPCRC, $nomFechaI, $nomFechaF){
        $model = new Dashboardcategorias();
        $varFechaI = $nomFechaI;
        $varFechaF = $nomFechaF;
        $varCodigPcrc = $codigoPCRC;

      return $this->renderAjax('totalizaragentes',[
          'model' => $model,
          'varFechaI' => $varFechaI,
          'varFechaF' => $varFechaF,
          'varCodigPcrc' => $varCodigPcrc,          
          ]);
      }


  public function actionTotalagente(){
          $var_FechaIni = null;
          $var_FechaFin = null;
          $varCodparametrizar = null;
          $VarCodsPcrc = null;
          $txtServicio = null;
          $txtParametros = null;

          $var_FechaIni = Yii::$app->request->get("var_FechaIni");
          $var_FechaFin = Yii::$app->request->get("var_FechaFin");
          $txtServicio = Yii::$app->request->get("varArbol_idV");
          $txtParametros = Yii::$app->request->get("varParametros_idV");
          $varCodparametrizar = Yii::$app->request->get("varCodparametrizar");
          $varCorreo = Yii::$app->request->get("var_Destino");
          $VarCodsPcrc = Yii::$app->request->get("var_CodsPcrc");

  
          $sessiones = Yii::$app->user->identity->id;
  
          $varInicioF = $var_FechaIni.' 05:00:00';
          $varFecha = date('Y-m-d',strtotime($var_FechaFin."+ 1 days"));
          $varFinF = $varFecha.' 05:00:00';
  
          $fechaComoEntero = strtotime($varInicioF);
  
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
  
          //Calculos  
          $txtcodigoCC = $VarCodsPcrc;
  
          $varListIndiVari = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias, responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2,3) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria order by idcategorias asc")->queryAll();
          $varListadorespo = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias, responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2,3) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') and responsable is not null group by idcategoria order by idcategorias asc")->queryAll();
          $varlistarespo = Yii::$app->db->createCommand("select responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria,responsable order by idcategorias asc")->queryAll();
          $varlistaindica = Yii::$app->db->createCommand("select responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') group by idcategoria,responsable order by idcategorias asc")->queryAll();
          $vartotalrespo = count($varlistarespo);
          $vartotalindica = count($varlistaindica);

      //Diego para lo de responsabilidad IDA
          if($varListadorespo) {
              $varlistaresponsable = array();              
              foreach ($varListIndiVari as $key => $value) {
  
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
                array_push($varlistaresponsable, $varnomresponsable);
                

              }
              
              
            }
          }

      // fin Diego
 
      $numcol1 = 0;
      $varlistasigno = array();
      $varvalormas = 'Positivo';
      $varvalormenos = 'Negativo';
      foreach ($varListIndiVari as $key => $value) {
        $varidCate = $value['idcategoria'];
        $numcol1++;
        $varNumero = Yii::$app->db->createCommand("select orientacionsmart from tbl_speech_categorias where anulado = 0 and idcategoria  = $varidCate and cod_pcrc in ('$txtcodigoCC') and programacategoria in ('$txtServicio')")->queryScalar();

        if ($varNumero == 0) {
           #code...
        }else{
          if ($varNumero == 2) {             
            array_push($varlistasigno, $varvalormenos);
          }else{
            if ($varNumero == 1) {
              array_push($varlistasigno, $varvalormas); 
            }
          }
        }
      }

  // colocar fin comentario aqui

  
          // Diego Para calculo de porcentahe de Agentes IDA
        
        $varListIndiVari2 = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias, orientacionsmart, responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2,3) and programacategoria in ('$txtServicio') and cod_pcrc in ('$txtcodigoCC') and responsable = 1 group by idcategoria order by idcategorias asc")->queryAll();
                      
        $arrayListaVar = array();
        $arraYListaVarMas = array();
        $arraYListaVarMenos = array();
        foreach ($varListIndiVari2 as $key => $value) {
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
            
  
            $varTimes = Yii::$app->db->createCommand("select round(AVG(callduracion))  from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio')  and fechallamada = '$txtFecha' and callid = $txtCallid and extension in ('$txtExtensionid')")->queryScalar();
  

            if ($varCodigo == 1) {
              $varCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where anulado = 0 and rn in ('$txtExtensionid')")->queryScalar();          
            }else{
              if ($varCodigo == 2) {
                $varCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where anulado = 0 and ext in ('$txtExtensionid')")->queryScalar();
              }else{ 
                $varCod = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_parametrizar where anulado = 0 and usuared in ('$txtExtensionid')")->queryScalar();
              }
            }

           $varcallidR = $value['callid'];
           $varlogin_id = $value['login_id'];

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
  
              }else{
                if ($varIdcategorias == 2) {
                  $varConteo = Yii::$app->db->createCommand("select count(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio')   and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and callid = $txtCallid and idindicador = $varVariables and idvariable = $varVariables")->queryScalar();
                }else{
                  if ($varIdcategorias == 3) {
                    $varConteo = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtExtensionid') and fechallamada = '$txtFecha' and callid = $txtCallid   and idcategoria = $varVariables")->queryScalar();
                  }
                }
              }
    
  
              if($varListadorespo) {
                  $cuentavari++;
                  //calculo % agentes
     
    
                  
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
                    if($cuentanegativoR == 0) {
                      $totalpondeR = $sumapositivoR / $varTotalvariables;
                    }
                    if($cuentanegativoR == $varTotalvariables) {
                      $totalpondeR = (($cuentanegativoR - $sumanegativoR) / $varTotalvariables);
                    }
                    if($cuentanegativoR != $varTotalvariables && $cuentanegativoR > 0) {
                      $totalpondeR = (($sumapositivoR + ($cuentanegativoR - $sumanegativoR)) / $varTotalvariables);
                    }              
              // insertar en la tabla temporal
              
                    Yii::$app->db->createCommand()->insert('tbl_tmpcategoriaagente',[
                      'call_id' => $varcallidR,
                      'id_pcrc' => $VarCodsPcrc,
                      'usuario_red' => $varlogin_id,
                      'subtotalagente' => $totalpondeR,
                      'fecha_ini' => $var_FechaIni,
                      'fecha_fin' => $var_FechaFin,
                      'fecha_creacion' => date("Y-m-d"),
                      'usuario_id' => $sessiones,
                    ])->execute();
                  }
              }
             
            }
            $cuentavari = 0;
            $cuentanegativoR = 0;
            $sumapositivoR = 0;
            $sumapositivoR = 0;
            $cuentanegativoR = 0;
            $sumanegativoR = 0;
            
          }
          $varres = 1;
          die(json_encode($varres));
                    
      }public function actionGetarbolesbyroles($search = null, $id = null) {
                $out = ['more' => false];
                $grupo = Yii::$app->user->identity->grupousuarioid;
                if (!is_null($search)) {
                    $data = \app\models\Arboles::find()
                            ->joinWith('permisosGruposArbols')
                            ->join('INNER JOIN', 'tbl_grupos_usuarios', 'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                            ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                            ->where([
                                "sncrear_formulario" => 1,
                                "snhoja" => 1,
                                "grupousuario_id" => $grupo])
                            ->andWhere(['not', ['formulario_id' => null]])
                            ->andWhere('name LIKE "%' . $search . '%" ')
                            ->andWhere('tbl_grupos_usuarios.per_realizar_valoracion = 1')
                            ->orderBy("dsorden ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Arboles::find()
                            ->joinWith('permisosGruposArbols')
                            ->join('INNER JOIN', 'tbl_grupos_usuarios', 'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                            ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                            ->where([
                                "sncrear_formulario" => 1,
                                "snhoja" => 1,
                                "grupousuario_id" => $grupo])
                            ->andWhere(['not', ['formulario_id' => null]])
                            ->andWhere('tbl_arbols.id = ' . $id)
                            ->andWhere('tbl_grupos_usuarios.per_realizar_valoracion = 1')
                            ->orderBy("dsorden ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
    }

    public function actionValoraspeech($varloginid,$varinteraccion,$varfechasreals,$varcodpcrc,$varservisioname){
      // Aqui se genera un cambio con el nuevo Escucha Focalizada
      $modelA = new \app\models\Arboles();
      $modelD = new \app\models\Dimensiones();
      $modelE = new \app\models\Evaluados;

      $paramBuscarPcrc = [':varPcrcs'=>$varcodpcrc];
      
      $txtvarservisioname = $varservisioname;
      $vardocumento = null;

      $txtvarcodpcrc = Yii::$app->db->createCommand('
        SELECT CONCAT(sc.cod_pcrc," - ",sc.nombre) AS nombre FROM tbl_speech_categorias sc
          WHERE 
            sc.cod_pcrc IN (:varPcrcs)
          GROUP BY sc.cod_pcrc')->bindValues($paramBuscarPcrc)->queryScalar();

      $txtLoginId = $varloginid;
      $paramsBuscarLogin = [':varLoginID'=>$varloginid];
      $varcomprobacion = is_numeric($txtLoginId);

      if ($varcomprobacion == false) {
        $varlistjarvis = Yii::$app->get('dbjarvis2')->createCommand('
          SELECT ur.documento FROM dp_usuarios_red ur 
            WHERE 
              ur.usuario_red IN (:varLoginID)')->bindValues($paramsBuscarLogin)->queryScalar();

        if ($varlistjarvis == "") {
          $vardocumento = Yii::$app->get('dbjarvis2')->createCommand('
            SELECT ur.documento FROM dp_usuarios_red ur 
              INNER JOIN dp_usuarios_actualizacion ua ON  
                ur.documento = ua.documento  
              WHERE 
                ua.usuario IN (:varLoginID) 
              GROUP  BY  ua.usuario')->bindValues($paramsBuscarLogin)->queryScalar();
        }else{
          $vardocumento = $varlistjarvis;
        }

      }else{
        $vardocumento = $txtLoginId;
      }

      $paramsBuscaDocumento = [':varDocumentos'=>$vardocumento];

      $txtEvaluado = Yii::$app->db->createCommand('
        SELECT DISTINCT e.name FROM tbl_evaluados e  
          WHERE  e.identificacion IN (:varDocumentos)')->bindValues($paramsBuscaDocumento)->queryScalar();

      $txtEvaluadoid = Yii::$app->db->createCommand('
          SELECT DISTINCT e.id FROM tbl_evaluados e  
            WHERE  e.identificacion IN (:varDocumentos)')->bindValues($paramsBuscaDocumento)->queryScalar();

      $txtConjuntoSpeech = $varinteraccion.'; '.$varfechasreals;

      return $this->render('valoraspeech',[
        'modelA' => $modelA,
        'modelD' => $modelD,
        'modelE' => $modelE,
        'txtEvaluado' => $txtEvaluado,
        'txtConjuntoSpeech' => $txtConjuntoSpeech,
        'txtEvaluadoid' => $txtEvaluadoid,
        'txtvarcodpcrc' => $txtvarcodpcrc,
        'txtvarservisioname' => $txtvarservisioname,
      ]);
    }

    public function actionGuardarpaso2($preview = 0) {
      $modelE = new \app\models\Evaluados;
      $modelE->scenario = "monitoreo";

      $post = Yii::$app->request->post();
      if (isset($post) && !empty($post)) {
        $arboles = Yii::$app->request->post('Arboles');
        $arbol_id = $arboles["arbol_id"];
        $infoArbol = \app\models\Arboles::findOne(["id" => $arbol_id]);
        $formulario_id = $infoArbol->formulario_id;
        $dimension = Yii::$app->request->post('Dimensiones');
        $dimension_id = $dimension["dimension_id"];
        $evaluado_id = Yii::$app->request->post("evaluado_id");
        $postTipoInteraccion =  Yii::$app->request->post('tipo_interaccion');
        $tipoInteraccion = (isset($postTipoInteraccion)) ? $postTipoInteraccion : 1;
        $usua_id = Yii::$app->user->identity->id;
        $created = ($preview == 1) ? 0 : date("Y-m-d H:i:s");
        $sneditable = 1;
        $dsfuente_encuesta = Yii::$app->request->post("dsfuente_encuesta");

        //CONSULTO SI YA EXISTE LA EVALUACION
        $condition = [
          "usua_id" => $usua_id,
          "arbol_id" => $arbol_id,
          "evaluado_id" => $evaluado_id,
          "dimension_id" => $dimension_id,
          "basesatisfaccion_id" => null,
          "sneditable" => $sneditable,
        ];

        $idTmpForm = \app\models\Tmpejecucionformularios::findOne($condition);

        //SI NO EXISTE EL TMP FORMULARIO LO CREO
        if (empty($idTmpForm)) {
          $tmpeje = new \app\models\Tmpejecucionformularios();
          $tmpeje->dimension_id = $dimension_id;
          $tmpeje->arbol_id = $arbol_id;
          $tmpeje->usua_id = $usua_id;
          $tmpeje->evaluado_id = $evaluado_id;
          $tmpeje->formulario_id = $formulario_id;
          $tmpeje->created = $created;
          $tmpeje->sneditable = $sneditable;
          $tmpeje->dsfuente_encuesta = $dsfuente_encuesta;
          date_default_timezone_set('America/Bogota');
          $tmpeje->hora_inicial = date("Y-m-d H:i:s");

          //EN CASO DE SELECCIONAR ITERACCION AUTOMATICA
          //CONSULTAMOS LA ITERACCION

          if ($tipoInteraccion == 0) {
            try {
              $modelFormularios = new Formularios;
              $enlaces = $modelFormularios->getEnlaces($evaluado_id);
              if ($enlaces && count($enlaces) > 0) {
                $json = json_encode($enlaces);
                $tmpeje->url_llamada = $json;
              }
            } catch (Exception $exc) {
              \Yii::error('#####' . __FILE__ . ':' . __LINE__
                  . $exc->getMessage() . '#####', 'redbox');
              $msg = Yii::t('app', 'Error redbox');
              Yii::$app->session->setFlash('danger', $msg);
            }

            $showInteraccion = 1;
            $showBtnIteraccion = 1;
          }else{
            $showInteraccion = 0;
            $showBtnIteraccion = 0;
          }
          $tmpeje->tipo_interaccion = $tipoInteraccion;
          $tmpeje->save();
          $idTmp = $tmpeje->id;
        }else{
          $idTmp = $idTmpForm->id;
          // EN CASO DE SELECCIONAR ITERACCION MANUAL
          // ELIMINAMOS EL REGSTRO ANTERIOR
          $showInteraccion = 1;
          $showBtnIteraccion = 1;
          //SI ES AUTOMATICA Y ES VACIA
          if ($tipoInteraccion == 0 && empty($idTmpForm->url_llamada)) {
            //CONSULTA DE LLAMADAS Y PANTALLAS CON WS 
            try {
              $modelFormularios = new Formularios;
              $enlaces = $modelFormularios->getEnlaces($evaluado_id);
              if ($enlaces && count($enlaces) > 0) {
                date_default_timezone_set('America/Bogota');
                $idTmpForm->hora_inicial = date("Y-m-d H:i:s");
                $json = json_encode($enlaces);
                $idTmpForm->url_llamada = $json;
                $idTmpForm->tipo_interaccion = $tipoInteraccion;
                $idTmpForm->save();
              }else{
                date_default_timezone_set('America/Bogota');
                $idTmpForm->hora_inicial = date("Y-m-d H:i:s");
                $idTmpForm->url_llamada = "";
                $idTmpForm->tipo_interaccion = $tipoInteraccion;
                $idTmpForm->save();
                $msg = Yii::t('app', 'Error redbox');
                Yii::$app->session->setFlash('danger', $msg);
              }
            } catch (Exception $exc) {
              \Yii::error('#####' . __FILE__ . ':' . __LINE__
                                        . $exc->getMessage() . '#####', 'redbox');
              $msg = Yii::t('app', 'Error redbox');
              Yii::$app->session->setFlash('danger', $msg);
            }

            // SI ES MANUAL
          }elseif ($tipoInteraccion == 1) {
            $idTmpForm->url_llamada = '';
            $idTmpForm->tipo_interaccion = $tipoInteraccion;
            date_default_timezone_set('America/Bogota');
            $idTmpForm->hora_inicial = date("Y-m-d H:i:s");

            $idTmpForm->save();
            $showInteraccion = 0;
            $showBtnIteraccion = 0;
          }else {
            #code
          }
        }

        return $this->redirect([
                                "showformulario",
                                "formulario_id" => $idTmp,
                                "preview" => $preview,
                                "escalado" => 0,
                                "showInteraccion" => base64_encode($showInteraccion),
                                "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);

      }
    }

    public function actionShowformulario($formulario_id, $preview, $fill_values = false) {
        //DATOS QUE SERAN ENVIADOS AL FORMULARIO
                $data = new \stdClass();                                
                $model = new SpeechParametrizar();

                //OBTENGO EL FORMULARIO
                $TmpForm = \app\models\Tmpejecucionformularios::findOne($formulario_id);

                if (is_null($TmpForm)) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'Formulario no exite'));
                    return $this->redirect(['interaccionmanual']);
                }

                $data->tmp_formulario = $TmpForm;

                //OBTEGO EL ID DEL EQUIPO Y EL ID DEL LIDER
                $datos_eq_li = \app\models\Equipos::getEquipoLider($TmpForm->evaluado_id, $TmpForm->arbol_id);

                if (count($datos_eq_li) > 0) {
                    $data->equipo_id = $datos_eq_li["equipo_id"];
                    $data->usua_id_lider = $datos_eq_li["lider"];
                } else {
                    $data->equipo_id = "";
                    $data->usua_id_lider = "";
                }

                //NOMBRE DEL EVALUADO
                $evaluado = \app\models\Evaluados::findOne($TmpForm->evaluado_id);
                $data->evaluado = $evaluado->name;

                //INFORMACION ADICIONAL
                $arbol = \app\models\Arboles::findOne($TmpForm->arbol_id);
                $data->info_adicional = [
                    'problemas' => $arbol->snactivar_problemas,
                    'tipo_llamada' => $arbol->snactivar_tipo_llamada
                ];
                $data->ruta_arbol = $arbol->dsname_full;
                $data->dimension = \yii\helpers\ArrayHelper::map(\app\models\Dimensiones::find()->all(), 'id', 'name');
                $data->detalles = \app\models\Tmpejecucionbloquedetalles::getAllByFormId($formulario_id);
                $data->totalBloques = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $TmpForm->id]);

                //CALIFICACIONES
                $tmp_calificaciones_ids = $tmp_tipificaciones_ids = array();
                foreach ($data->detalles as $j => $d) {
                    if (!in_array($d->calificacion_id, $tmp_calificaciones_ids)) {
                        $tmp_calificaciones_ids[] = $d->calificacion_id;
                    }
                    if (!in_array($d->tipificacion_id, $tmp_tipificaciones_ids)) {
                        $tmp_tipificaciones_ids[] = $d->tipificacion_id;
                    }
                    if ($d->tipificacion_id != null) {
                        $data->detalles[$j]->tipif_seleccionados = \app\models\TmpejecucionbloquedetallesTipificaciones::getTipificaciones($d->id);
                    } else {
                        $data->detalles[$j]->tipif_seleccionados = array();
                    }
                }

                //CALIFICACIONES Y TIPIFICACIONES
                $data->calificaciones = \app\models\Calificaciondetalles::getDetallesFromIds($tmp_calificaciones_ids);
                $data->calificacionesArray = \app\models\Calificaciondetalles::getDetallesFromIdsAsArray($tmp_calificaciones_ids);
                $data->tipificaciones = \app\models\Tipificaciondetalles::getDetallesFromIds($tmp_tipificaciones_ids);

                //TRANSACCIONES Y ENFOQUES
                $data->transacciones = \yii\helpers\ArrayHelper::map(\app\models\Transacions::find()->all(), 'id', 'name');
                $data->enfoques = \app\models\Tableroenfoques::find()->asArray()->all();

                //FORMULARIO ID
                $data->formulario_id = $formulario_id;

                /* OBTIENE EL LISTADO DETALLADO DE TABLERO DE EXPERIENCIAS Y LLAMADA
                  EN MODO VISUALIZACI�N FORMULARIO. */
                $data->tablaproblemas = \app\models\Ejecuciontableroexperiencias::
                                find()
                                ->where(["ejecucionformulario_id" => $TmpForm->ejecucionformulario_id])->all();
                $data->tablallamadas = \app\models\Ejecuciontiposllamada::getTabLlamByIdEjeForm($TmpForm->ejecucionformulario_id);
                $data->list_Add_feedbacks = \app\models\Tmpejecucionfeedbacks::getJoinTipoFeedbacks($formulario_id);

                //PREVIEW
                $data->preview = $preview == 1 ? true : false;
                $data->fill_values = $fill_values;
                //busco el formulario al cual esta atado la valoracion a cargar
                //y valido de q si tenga un formulario, de lo contrario se fija 
                //en 1 por defecto
                $data->formulario = Formularios::find()->where(['id' => $data->tmp_formulario->formulario_id])->one();
                if (!isset($TmpForm->subi_calculo)) {
                    if (isset($data->formulario->subi_calculo)) {
                        $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                        $TmpForm->save();
                        $array_indices_TmpForm = \app\models\Textos::find()
                                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                ->asArray()
                                ->all();
                        foreach ($array_indices_TmpForm as $value) {
                            $data->indices_calcular[$value['id']] = $value['text'];
                        }
                    }
                } else {
                    if (isset($data->formulario->subi_calculo)) {
                        $array_indices_TmpForm = \app\models\Textos::find()
                                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                ->asArray()
                                ->all();
                        foreach ($array_indices_TmpForm as $value) {
                            $data->indices_calcular[$value['id']] = $value['text'];
                        }
                    }
                }

                if($data->tmp_formulario->hora_inicial != "" AND $data->tmp_formulario->hora_final != ""){
                    $inicial = new DateTime($data->tmp_formulario->hora_inicial);
                    $final = new DateTime($data->tmp_formulario->hora_final);

                    $dteDiff1  = $inicial->diff($final);

                    $dteDiff1->format("Y-m-d H:i:s");


                    $data->fecha_inicial = $data->tmp_formulario->hora_inicial;
                    $data->fecha_final = $data->tmp_formulario->hora_final;
                    $data->minutes = $dteDiff1->h . ":" . $dteDiff1->i . ":" . $dteDiff1->s;
                }

                $varIdformu = Yii::$app->db->createCommand("select ejecucionformulario_id from tbl_tmpejecucionformularios where id = '$formulario_id'")->queryScalar();
             

    
        //DATOS GENERALES

                $varidarbol = Yii::$app->db->createCommand("select a.id FROM tbl_arbols a INNER JOIN tbl_arbols b ON a.id = b.arbol_id WHERE b.id = '$TmpForm->arbol_id'")->queryScalar();

                 $varIdclienteSel = Yii::$app->db->createCommand("select LEFT(ltrim(name),3) FROM tbl_arbols a WHERE a.id = '$TmpForm->arbol_id'")->queryScalar();

                $varIdcliente = Yii::$app->db->createCommand("select id_dp_clientes from tbl_registro_ejec_cliente where anulado = 0 and ejec_form_id = '$varIdformu'")->queryScalar();
                $varCodpcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_registro_ejec_cliente where anulado = 0 and ejec_form_id = '$varIdformu'")->queryScalar();
                if(is_numeric($varIdclienteSel)){
                    $varIdclienteSel = $varIdclienteSel;
                }else{
                    $varIdclienteSel = 0;
                }
        if($varIdclienteSel > 0){
                    $data->idcliente =  $varIdclienteSel;
                }else{
                    $data->idcliente =  $varIdcliente;
                }
                $data->varidarbol =  $varidarbol;
                $data->codpcrc =  $varCodpcrc;
                $data->IdclienteSel =$varIdclienteSel;
                $data->varIdformu =  $varIdformu;

                return $this->render('show-formulario', [
                                                        'data' => $data,                            
                                                        'model' => $model,
                ]);
    }

    public function actionEliminartmpform($tmp_form) {

      \app\models\Tmpejecucionformularios::deleteAll(["id" => $tmp_form]);

      Yii::$app->session->setFlash('success', Yii::t('app', 'Formulario borrado'));
      return $this->redirect(['index']);
    }

    public function actionGuardaryenviarformulario() {

      $txtanulado = 0;
                $calificaciones = Yii::$app->request->post('calificaciones');
                $tipificaciones = Yii::$app->request->post('tipificaciones');
                $subtipificaciones = Yii::$app->request->post('subtipificaciones');
                $comentarioSeccion = Yii::$app->request->post('comentarioSeccion');
                $checkPits = Yii::$app->request->post('checkPits');
                $txtfechacreacion = date("Y-m-d");
                $arrCalificaciones = !$calificaciones ? array() : Yii::$app->request->post('calificaciones');
                $arrTipificaciones = !isset($tipificaciones) ? array() : Yii::$app->request->post('tipificaciones');
                $arrSubtipificaciones = !isset($subtipificaciones) ? array() : Yii::$app->request->post('subtipificaciones');
                $arrComentariosSecciones = !$comentarioSeccion ? array() : Yii::$app->request->post('comentarioSeccion');
                $arrCheckPits = !isset($checkPits) ? array() : Yii::$app->request->post('checkPits');
                $arrFormulario = [];
                $arrayCountBloques = [];
                $arrayBloques = [];

                $varid_clientes = Yii::$app->request->post('id_dp_clientes');
                $varid_centro_costo = Yii::$app->request->post('requester');
                $count = 0;
                $tmp_id = Yii::$app->request->post('tmp_formulario_id');
                $arrFormulario["equipo_id"] = Yii::$app->request->post('form_equipo_id');
                $arrFormulario["usua_id_lider"] = Yii::$app->request->post('form_lider_id');
                $arrFormulario["dimension_id"] = Yii::$app->request->post('dimension_id');
                $arrFormulario["dsruta_arbol"] = Yii::$app->request->post('ruta_arbol');
                $arrFormulario["dscomentario"] = Yii::$app->request->post('comentarios_gral');
                $arrFormulario["dsfuente_encuesta"] = Yii::$app->request->post('fuente');      
                $txtFuentes = Yii::$app->request->post('fuente');  
                $arrFormulario["transacion_id"] = Yii::$app->request->post('transacion_id');
                $arrFormulario["sn_mostrarcalculo"] = 1;
                $postView = Yii::$app->request->post('view');
                $view = (isset($postView))?Yii::$app->request->post('view'):null;
                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                $subi_calculo = Yii::$app->request->post('subi_calculo');
                if (isset($subi_calculo) AND $subi_calculo != '') {
                    $data->subi_calculo .=',' . $subi_calculo;
                    $data->save();
                }

                /* EDITO EL TMP FORMULARIO  GERMAN*/
                $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();


                
                //TO-DO  : COMENTAR LINEA EN CASO DE NO NECESITAR LO DE ADICIONAR Y ESCALAR
                /* Guardo en la tabla tbl_registro_ejec para tener un seguimiento 
                 * de los diversos involucrados en la valoracion en el tiempo */
                $modelRegistro = \app\models\RegistroEjec::findOne(['ejec_form_id' => $model->ejecucionformulario_id, 'valorador_id' => $model->usua_id]);
                if (!isset($modelRegistro)) {
                    $modelRegistro = new \app\models\RegistroEjec();
                    $modelRegistro->ejec_form_id = $tmp_id;
                    $modelRegistro->descripcion = 'Primera valoraci�n';
                }
                $modelRegistro->dimension_id = Yii::$app->request->post('dimension_id');
                $modelRegistro->valorado_id = $data->evaluado_id;
                $modelRegistro->valorador_id = $data->usua_id;
                $modelRegistro->pcrc_id = $data->arbol_id;
                $modelRegistro->tipo_interaccion = $data->tipo_interaccion;
                $modelRegistro->fecha_modificacion = date("Y-m-d H:i:s");
                $fecha_inicial_mod = Yii::$app->request->post('hora_modificacion');
                $modelRegistro->save();
                //FIN
                \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                
                //Para cliente y centros de costos
                $varIdformu = Yii::$app->db->createCommand("select ejecucionformulario_id from tbl_tmpejecucionformularios where id = '$tmp_id'")->queryScalar();
                $varcliente = Yii::$app->db->createCommand("select cliente from tbl_proceso_cliente_centrocosto where cod_pcrc = '$varid_centro_costo'")->queryScalar();
                $varpcrc = Yii::$app->db->createCommand("select CONCAT_WS(' - ', cod_pcrc, pcrc) from tbl_proceso_cliente_centrocosto where cod_pcrc = '$varid_centro_costo'")->queryScalar();
                $vardirector = Yii::$app->db->createCommand("select director_programa from tbl_proceso_cliente_centrocosto where cod_pcrc = '$varid_centro_costo'")->queryScalar();
                $varcuidad = Yii::$app->db->createCommand("select ciudad from tbl_proceso_cliente_centrocosto where cod_pcrc = '$varid_centro_costo'")->queryScalar();
          $vargerente = Yii::$app->db->createCommand("select gerente_cuenta from tbl_proceso_cliente_centrocosto where cod_pcrc = '$varid_centro_costo'")->queryScalar();
                //fin
                
                
                /* GUARDO LAS CALIFICACIONES */
                foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                    $arrDetalleForm = [];
                    //se valida que existan check de pits seleccionaddos y se valida
                    //que exista el del bloquedetalle actual para actualizarlo
                    if (count($arrCheckPits) > 0) {
                        if (isset($arrCheckPits[$form_detalle_id])) {
                            $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                        }
                    }
                    if (empty($calif_detalle_id)) {
                        $arrDetalleForm["calificaciondetalle_id"] = -1;
                    } else {
                        $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                    }

                    \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["id" => $form_detalle_id]);
                    $calificacion = \app\models\Tmpejecucionbloquedetalles::findOne(["id" => $form_detalle_id]);
                    $calificacionDetalle = \app\models\Calificaciondetalles::findOne(['id' => $calificacion->calificaciondetalle_id]);
                    //Cuento las preguntas en las cuales esta seleccionado el NA
                    //lleno $arrayBloques para tener marcados en que bloques no se selecciono el check
                    if (!in_array($calificacion->bloque_id, $arrayBloques) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                        $arrayBloques[] = $calificacion->bloque_id;
                        //inicio $arrayCountBloques
                        $arrayCountBloques[$count] = [($calificacion->bloque_id) => 1];
                        $count++;
                    } else {
                        //actualizo $arrayCountBloques sumandole 1 cada q encuentra un NA de ese bloque
                        if (count($arrayCountBloques) != 0) {
                            if ((array_key_exists($calificacion->bloque_id, $arrayCountBloques[count($arrayCountBloques) - 1])) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                                $arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] = ($arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] + 1);
                            }
                        }
                    }
                }
                //Actualizo los bloques en los cuales el total de sus preguntas esten seleccionadas en NA
                foreach ($arrayCountBloques as $dato) {
                    $totalPreguntasBloque = \app\models\Tmpejecucionbloquedetalles::find()->select("COUNT(id) as preguntas")
                                    ->from("tbl_tmpejecucionbloquedetalles")
                                    ->where(['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)])->asArray()->all();
                    if ($dato[key($dato)] == $totalPreguntasBloque["0"]["preguntas"]) {
                        \app\models\Tmpejecucionbloques::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)]);
                    }
                }
                //actualizo las secciones, la cuales tienen todos sus bloques con la opcion snna en 1
                $secciones = \app\models\Tmpejecucionsecciones::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                foreach ($secciones as $seccion) {
                    $bloquessnna = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                    ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                    ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                    ->where(['b.snna' => 1, 's.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                    ->groupBy("s.id")->asArray()->all();
                    $totalBloques = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                    ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                    ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                    ->where(['s.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                    ->groupBy("s.id")->asArray()->all();
                    if (count($bloquessnna) > 0) {
                        if ($bloquessnna[0]['conteo'] == $totalBloques[0]['conteo']) {
                            \app\models\Tmpejecucionsecciones::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'seccion_id' => ($seccion->seccion_id)]);
                        }
                    }
                }
                /* GUARDO TIPIFICACIONES */
                foreach ($arrTipificaciones as $form_detalle_id => $tipif_array) {
                    if (empty($tipif_array))
                        continue;

                    \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 0]
                            , ["tmpejecucionbloquedetalle_id" => $form_detalle_id]);

                    \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 1]
                            , "tmpejecucionbloquedetalle_id = '" . $form_detalle_id . "' "
                            . "AND tipificaciondetalle_id IN(" . implode(",", $tipif_array) . ")");
                }

                /* GUARDO SUBTIPIFICACIONES */
                foreach ($arrSubtipificaciones as $form_detalle_id => $subtipif_array) {
                    $sql = "UPDATE `tbl_tmpejecucionbloquedetalles_subtipificaciones` a ";
                    $sql .= "INNER JOIN tbl_tmpejecucionbloquedetalles_tipificaciones b ";
                    $sql .= "ON a.tmpejecucionbloquedetalles_tipificacion_id = b.id ";
                    $sql .= "SET a.sncheck = 1 ";
                    $sql .= "WHERE b.tmpejecucionbloquedetalle_id = " . $form_detalle_id;
                    $sql .= " AND a.tipificaciondetalle_id IN (" . implode(",", $subtipif_array) . ")";
                    $command = \Yii::$app->db->createCommand($sql);
                    $command->execute();
                }
                foreach ($arrComentariosSecciones as $secc_id => $comentario) {

                    \app\models\Tmpejecucionsecciones::updateAll(["dscomentario" => $comentario]
                            , [
                        "seccion_id" => $secc_id
                        , "tmpejecucionformulario_id" => $tmp_id
                    ]);
                }
                //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                date_default_timezone_set('America/Bogota');
                
                if($data['hora_final'] != ""){
                        $inicial = new DateTime($fecha_inicial_mod);
                        $final = new DateTime(date("Y-m-d H:i:s"));

                        $dteDiff  = $inicial->diff($final);

                        $dteDiff->format("Y-m-d H:i:s");

                        $tiempo_modificacion_actual = $dteDiff->h . ":" . $dteDiff->i . ":" . $dteDiff->s;

                        $tmp_ejecucion->cant_modificaciones = $tmp_ejecucion->cant_modificaciones + 1;

                        $date = new DateTime($tiempo_modificacion_actual);
                        $suma2 = $this->sumarhoras($tmp_ejecucion->tiempo_modificaciones, $date->format('H:i:s'));
                        $tmp_ejecucion->tiempo_modificaciones = $suma2;

                        $tmp_ejecucion->save();
                }else{
                    $pruebafecha = date("Y-m-d H:i:s");
                    $tmp_ejecucion->hora_final = $pruebafecha;
                    $tmp_ejecucion->save();
                }

                /* GUARDAR EL TMP FOMULARIO A LAS EJECUCIONES */
                $validarPasoejecucionform = \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);

                $txtUsuaid = Yii::$app->user->identity->id;

                $dataIdFormulario = (new \yii\db\Query())
                        ->select(['id'])
                        ->from(['tbl_ejecucionformularios'])
                        ->where('created BETWEEN :varFechainicios AND :varFechafines',[':varFechainicios'=>date('Y-m-d').' 00:00:00',':varFechafines'=>date('Y-m-d').' 23:59:59'])
                        ->andwhere('dsfuente_encuesta IN (:varDsFuente)',[':varDsFuente'=>$txtFuentes])
                        ->andwhere('usua_id IN (:varUsuaid)',[':varUsuaid'=>$txtUsuaid])
                        ->scalar();

                $varExtraerData = explode("; ", $txtFuentes);
                for ($i=0; $i < count($varExtraerData); $i++) { 
                  $varCallids = $varExtraerData[0];
                  $varTiempoReal = $varExtraerData[1];
                }

                Yii::$app->db->createCommand()->insert('tbl_speech_mixta',[
                                'formulario_id' => $dataIdFormulario,
                                'callid' => $varCallids,
                                'fechareal' => $varTiempoReal,   
                                'anulado' => 0,
                                'usua_id' => $txtUsuaid,
                                'fechacreacion' => date('Y-m-d'),                                       
                            ])->execute();

    //Proceso para guardar clientes y centro de costos

                /* validacion de guardado exitoso del tmp y paso a las tablas de ejecucion
                en caso de no cumplirla, se redirige nuevamente al formulario */
                if (!$validarPasoejecucionform) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception tmpejecucion to ejecucion'));
                    if ($model->tipo_interaccion == 0) {
                        $showInteraccion = 1;
                        $showBtnIteraccion = 1;
                    } else {
                        $showInteraccion = 0;
                        $showBtnIteraccion = 0;
                    }
                    return $this->redirect(['showformulario'
                                , "formulario_id" => $model->id
                                , "preview" => 0
                                , "escalado" => 0
                                , "view" => $view
                                , "showInteraccion" => base64_encode($showInteraccion)
                                , "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                }
                /**
                 * Se envia datos a la aplicacion amigo, indicando que se realizo una valoracion
                 */
                //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                /**/
                $modelEvaluado = \app\models\Evaluados::findOne(["id" => $tmp_ejecucion->evaluado_id]);
                $ejecucion = \app\models\Ejecucionformularios::find()->where(['evaluado_id' => $tmp_ejecucion->evaluado_id, 'usua_id' => $tmp_ejecucion->usua_id])->orderBy('id DESC')->all();

                //Proceso para guardar clientes y centro de costos
               
    $varIdcliente = Yii::$app->db->createCommand("select id_dp_clientes from tbl_registro_ejec_cliente where anulado = 0 and ejec_form_id = '$varIdformu '")->queryScalar();
                
                if($varIdcliente){

                    Yii::$app->db->createCommand()->update('tbl_registro_ejec_cliente',[
                                                    'id_dp_clientes' => $varid_clientes,
                                                    'cod_pcrc' => $varid_centro_costo,
                                                    'cliente' => $varcliente,
                                                    'pcrc' => $varpcrc,
                                                    'ciudad' => $varcuidad,
                                                    'director_programa' => $vardirector,
                'gerente' => $vargerente,
                                                    'fechacreacion' => $txtfechacreacion,
                                                    'anulado' => $txtanulado,
                                                ],'ejec_form_id ='.$varIdformu .'')->execute();   
                }else{

        $txtidejec_formu = Yii::$app->db->createCommand("select MAX(id) from tbl_ejecucionformularios")->queryScalar(); 
                    Yii::$app->db->createCommand()->insert('tbl_registro_ejec_cliente',[
                        'ejec_form_id' => $txtidejec_formu,
                        'id_dp_clientes' => $varid_clientes,
                        'cod_pcrc' => $varid_centro_costo,
                        'cliente' => $varcliente,
                        'pcrc' => $varpcrc,
                        'ciudad' => $varcuidad,
                        'director_programa' => $vardirector,
      'gerente' => $vargerente,
                        'fechacreacion' => $txtfechacreacion,
                        'anulado' => $txtanulado,
                    ])->execute();
            }
               
                

                Yii::$app->session->setFlash('success', Yii::t('app', 'Formulario guardado'));

                return $this->redirect(['index']);


    }


    public function actionParamscategorias($txtServicioCategorias){
      $txtnamepcrc = Yii::$app->db->createCommand("SELECT DISTINCT  CONCAT(s.cod_pcrc,' - ',s.pcrc) AS Namecategoria FROM tbl_speech_categorias s WHERE s.anulado = 0 AND s.cod_pcrc IN ('$txtServicioCategorias')")->queryScalar();

      $txtspeechid = Yii::$app->db->createCommand("SELECT DISTINCT p.id_dp_clientes FROM tbl_speech_parametrizar p INNER JOIN tbl_speech_categorias s ON p.cod_pcrc = s.cod_pcrc WHERE s.anulado = 0 AND s.cod_pcrc IN ('$txtServicioCategorias')")->queryScalar();

      $txtserviciosp = Yii::$app->db->createCommand("SELECT s.nameArbol FROM tbl_speech_servicios s WHERE s.id_dp_clientes = $txtspeechid")->queryScalar();

      $txtlistindicadores = Yii::$app->db->createCommand("SELECT DISTINCT  * FROM tbl_speech_categorias s WHERE s.anulado = 0 AND s.cod_pcrc IN ('$txtServicioCategorias') and s.idcategorias = 1")->queryAll();

      return $this->render('paramscategorias',[
        'txtnamepcrc' => $txtnamepcrc,
        'txtspeechid' => $txtspeechid,
        'txtserviciosp' => $txtserviciosp,
        'txtlistindicadores' => $txtlistindicadores,
      ]);
    }

    public function actionEditarcompetencia(){
      $varpcrc = Yii::$app->request->get('varcodpcrc');
      $varidcategoria = Yii::$app->request->get('varidcategoria');  
      $varnombres = Yii::$app->request->get('varnombre');  
      $model = new SpeechCategorias();


      return $this->renderAjax('editarcompetencia',[
        'varpcrc' => $varpcrc,
        'varidcategoria' => $varidcategoria,
        'varnombres' => $varnombres,
        'model' => $model,
      ]);
    }

    public function actionActualizaindicador(){
      $idspeech = Yii::$app->request->get("idspeech");
      $varpcrc = Yii::$app->request->get("varpcrc");
      $txtvartxtConteoid = Yii::$app->request->get("txtvartxtConteoid");

      Yii::$app->db->createCommand("UPDATE tbl_speech_categorias sc SET sc.componentes = $txtvartxtConteoid WHERE sc.anulado = 0 AND sc.idspeechcategoria = $idspeech  AND sc.cod_pcrc IN ('$varpcrc')")->execute();

      $txtname = Yii::$app->db->createCommand("SELECT sc.nombre FROM tbl_speech_categorias sc  WHERE sc.anulado = 0 AND sc.idspeechcategoria = $idspeech  AND sc.cod_pcrc IN ('$varpcrc')")->queryScalar();


      Yii::$app->db->createCommand("UPDATE tbl_speech_categorias sc SET sc.componentes = $txtvartxtConteoid WHERE sc.anulado = 0 AND sc.cod_pcrc IN ('$varpcrc') AND sc.idcategorias in (2) AND sc.tipoindicador in ('$txtname')")->execute();

      $varrtas = 0;

      die(json_encode($varrtas));
    }

    public function actionViewrtas($idspeechcalls,$varcodpcrc,$idvarfechareal,$idvarloginid,$idvarconnid,$idvarextensiones){
      // Aqui se genera un cambio con el nuevo Escucha Focalizada
      $varcod_pcrc = $varcodpcrc;
      $varCallid = $idspeechcalls;
      $varfechareal = $idvarfechareal;
      $varLoginId = $idvarloginid;
      $varConnids = $idvarconnid;
      $varExtensiones = $idvarextensiones;
      $varencuestaid = null;
      $varbuzones = null;
      $vartexto = null;
      $varvalencia = null;
      $varNA = "No Aplica";

      $paramsAsesor = [':varAsesores'=>$varLoginId];

      $varNombreAsesor = Yii::$app->db->createCommand('
        SELECT e.name FROM tbl_evaluados e 
          WHERE 
            e.dsusuario_red IN (:varAsesores)')->bindValues($paramsAsesor)->queryScalar();
      
      if ($varNombreAsesor == "") {
        $varNombreAsesor = Yii::$app->db->createCommand('
          SELECT e.name FROM tbl_evaluados e 
            WHERE 
              e.identificacion IN (:varAsesores)')->bindValues($paramsAsesor)->queryScalar();
      }

      $varNombreLider = Yii::$app->db->createCommand('
        SELECT u.usua_nombre FROM tbl_usuarios u
          INNER JOIN tbl_equipos eq ON 
            u.usua_id = eq.usua_id
          INNER JOIN tbl_equipos_evaluados ev ON 
            eq.id = ev.equipo_id
          INNER JOIN tbl_evaluados e ON 
            ev.evaluado_id = e.id
          WHERE 
            e.dsusuario_red IN (:varAsesores)')->bindValues($paramsAsesor)->queryScalar();
      
      if ($varNombreLider == "") {
        $varNombreLider = Yii::$app->db->createCommand('
          SELECT u.usua_nombre FROM tbl_usuarios u
            INNER JOIN tbl_equipos eq ON 
              u.usua_id = eq.usua_id
            INNER JOIN tbl_equipos_evaluados ev ON 
              eq.id = ev.equipo_id
            INNER JOIN tbl_evaluados e ON 
              ev.evaluado_id = e.id
            WHERE 
              e.identificacion IN (:varAsesores)')->bindValues($paramsAsesor)->queryScalar();
      }

      $paramsCategorias = [':varPcrc'=>$varcod_pcrc,':varCategoria'=>2,':varResponsabilidad'=>1];

      $varListCategorias = Yii::$app->db->createCommand('
        SELECT idcategoria, orientacionsmart, responsable, programacategoria FROM tbl_speech_categorias 
          WHERE 
            cod_pcrc IN (:varPcrc)
              AND idcategorias IN (:varCategoria)
                AND responsable IN (:varResponsabilidad)')->bindValues($paramsCategorias)->queryAll();

      $varResultadosIDA = 0;
      $varContarNegativas = 0;
      $varTotalNegativas = 0;
      $varConteoNegativas = 0;
      $varTotalPositivas = 0;
      $varConteoPositivas = 0;
      foreach ($varListCategorias as $key => $value) {
        $varorientaciones = $value['orientacionsmart'];

        $paramsBuscarCategorias = [':varIdCategoria'=>$value['idcategoria'],':varProgramaCategoria'=>$value['programacategoria'],':varAnulado'=>0,':varCallid'=>$varCallid];

        if ($varorientaciones == '2') {

          $varContarNegativas += 1;
          $varTotalNegativas = Yii::$app->db->createCommand('
            SELECT COUNT(sg.idvariable) FROM tbl_speech_general sg
              WHERE
                sg.anulado = :varAnulado AND sg.callid IN (:varCallid)
                  AND sg.programacliente IN (:varProgramaCategoria)
                    AND sg.idvariable IN (:varIdCategoria)')->bindValues($paramsBuscarCategorias)->queryScalar();

          if ($varTotalNegativas == 1) {
            $varConteoNegativas += 1;
          }

        }else{

          $varTotalPositivas = Yii::$app->db->createCommand('
            SELECT COUNT(sg.idvariable) FROM tbl_speech_general sg
              WHERE
                sg.anulado = :varAnulado AND sg.callid IN (:varCallid)
                  AND sg.programacliente IN (:varProgramaCategoria)
                    AND sg.idvariable IN (:varIdCategoria)')->bindValues($paramsBuscarCategorias)->queryScalar();

          if ($varTotalPositivas == 1) {
            $varConteoPositivas += 1;
          }
        }
      }

      if (count($varListCategorias) != 0 && $varConteoNegativas != 0) {
        $varResultadosIDA = round(((($varConteoPositivas + ($varContarNegativas - $varConteoNegativas)) / count($varListCategorias))),2);
      }else{
        $varResultadosIDA = 0;
      }


      $varScore = (new \yii\db\Query())
                ->select(['round(tbl_ejecucionformularios.score,2)'])
                ->from(['tbl_ejecucionformularios'])
                ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                          'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')
                ->where(['=','tbl_speech_mixta.callid',$varCallid])
                ->andwhere(['=','tbl_speech_mixta.fechareal',$varfechareal])
                ->andwhere(['=','tbl_speech_mixta.anulado',0])
                ->scalar();

      
      if ($varScore) {
        $varScoreValoracion = $varScore;
      }else{
        $varScoreValoracion = "--";
      }
                                
      if ($varScoreValoracion != 0) {
        if ($varResultadosIDA != 0) {
          $varPromedioScore = round((($varResultadosIDA + $varScoreValoracion) / 2),2);
        }else{
          $varPromedioScore = $varScoreValoracion;
        }
        
      }else{
        $varPromedioScore = $varResultadosIDA;
      }

      if ($varConnids != "--") {
        $paramsBusquedaConnid = [':varConnid' => $varConnids];

        $varencuestaid = Yii::$app->db->createCommand('
          SELECT b.id FROM tbl_base_satisfaccion b 
            WHERE 
              b.connid = :varConnid
        ')->bindValues($paramsBusquedaConnid)->queryScalar();

        $varbuzones = Yii::$app->db->createCommand('
          SELECT b.buzon FROM tbl_base_satisfaccion b 
            WHERE 
              b.connid = :varConnid
        ')->bindValues($paramsBusquedaConnid)->queryScalar();

        $varidconnid = Yii::$app->db->createCommand('
          SELECT b.connid FROM tbl_base_satisfaccion b 
            WHERE 
              b.connid = :varConnid
        ')->bindValues($paramsBusquedaConnid)->queryScalar();

        ob_start();
        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_SSL_VERIFYPEER=> false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_URL => 'https://api-kaliope.analiticagrupokonectacloud.com/status-by-connid',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS =>'{"connid": "'.$varidconnid.'"}',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/json'
          ),
        ));

        $response = curl_exec($curl);
        var_dump($response);

        curl_close($curl);
        ob_clean();

        $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

        if (empty($response)) {          
          $vartexto = "Transcripcion no encontrada";
          $varvalencia = "Valencia emocional no encontrada";
        }else{
          $vartexto = $response[0]['transcription'];
          $varvalencia = $response[0]['valencia'];
        }

      }else{
        $varencuestaid = "Sin id Encuesta";
        $varbuzones = $varNA;
        $vartexto = $varNA;
        $varvalencia = $varNA;
      }

      return $this->renderAjax('viewrtas',[
        'varResultadosIDA' => $varResultadosIDA,
        'varScoreValoracion' => $varScoreValoracion,
        'varPromedioScore' => $varPromedioScore,
        'varfechareal' => $varfechareal,
        'varLoginId' => $varLoginId,
        'varencuestaid' => $varencuestaid,
        'varbuzones' => $varbuzones,
        'varExtensiones' => $varExtensiones,
        'varConnids' => $varConnids,
        'varCallid' => $varCallid,
        'varNombreAsesor' => $varNombreAsesor,
        'varNombreLider' => $varNombreLider,
        'vartexto' => $vartexto,
        'varvalencia' => $varvalencia,
      ]);
    }

    public function actionLlamadafocalizada($varprograma,$varcodigopcrc,$varidcategoria,$varextension,$varfechasinicio,$varfechasfin,$vartcantllamadas,$varfechainireal,$varfechafinreal,$varcodigos,$varaleatorios){

      $model = new Dashboardspeechcalls();
      $paramsvarcodigopcrc = $varcodigopcrc;
      $paramsvarextension = $varextension;
      $paramsvarfechasinicio = $varfechasinicio;
      $paramsvarfechasfin = $varfechasfin;
      $varcantllamadas = $vartcantllamadas;
      $varAnulados = 0;

      $datafechas = $varfechainireal.' - '.$varfechafinreal;

      $paramsBuscarCategoriaGeneral = [':varPcrc' => $paramsvarcodigopcrc];

      $arrayExt = str_replace(array("#", "'", ";", " "), '', $paramsvarextension);
      $arrayExtTwo = explode(",", $arrayExt);
      
      $dataidllamada = Yii::$app->db->createCommand('
        SELECT ss.idllamada FROM tbl_speech_servicios ss
          INNER JOIN tbl_speech_parametrizar sp ON 
            ss.id_dp_clientes = sp.id_dp_clientes
          WHERE 
            sp.cod_pcrc IN (:varPcrc)
          GROUP BY sp.cod_pcrc')->bindValues($paramsBuscarCategoriaGeneral)->queryScalar();
      $dataidgeneral = intval($dataidllamada);

      $datanamearbol = Yii::$app->db->createCommand('
        SELECT ss.nameArbol FROM tbl_speech_servicios ss
          INNER JOIN tbl_speech_parametrizar sp ON 
            ss.id_dp_clientes = sp.id_dp_clientes
          WHERE 
            sp.cod_pcrc IN (:varPcrc)
          GROUP BY sp.cod_pcrc')->bindValues($paramsBuscarCategoriaGeneral)->queryScalar();

      $dataprograma = Yii::$app->db->createCommand('
        SELECT CONCAT(sc.cod_pcrc," - ",sc.pcrc) AS Programa FROM tbl_speech_categorias sc
          WHERE 
            sc.cod_pcrc IN (:varPcrc)
          GROUP BY sc.cod_pcrc')->bindValues($paramsBuscarCategoriaGeneral)->queryScalar();

      $databolsita = Yii::$app->db->createCommand('
        SELECT sc.programacategoria AS Programa FROM tbl_speech_categorias sc
          WHERE 
            sc.cod_pcrc IN (:varPcrc)
          GROUP BY sc.cod_pcrc')->bindValues($paramsBuscarCategoriaGeneral)->queryScalar();

      $cantidadAleatoria = Yii::$app->db->createCommand('
          SELECT sa.cantidad FROM tbl_speech_aleatoridad sa
            WHERE
              sa.cod_pcrc IN (:varPcrc)')->bindValues($paramsBuscarCategoriaGeneral)->queryScalar();
      $cantidadAleatoria = intval($cantidadAleatoria);


      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varindicadores = [':varIndicadores' => $model->idcategoria];
        $dataIndicador = Yii::$app->db->createCommand('
          SELECT sc.idcategoria FROM tbl_speech_categorias sc
            WHERE 
              sc.idspeechcategoria = :varIndicadores')->bindValues($varindicadores)->queryScalar();
        $dataVariable = $model->nombreCategoria;
        $dataMotivo = $model->extension;
        $dataContenedor = $model->login_id;
        $dataLider = $model->servicio;
        $dataAsesor = $model->fechallamada;
        $dataTipologia = $model->idredbox;

        $arrayIdCategorias = null;
        $dataCategorias = null;
        $dataAsesores = null;
        $dataIdInteraccion = null;
        
        // Genero consultas para obtener las categorias
        if ($dataIndicador != null && $dataVariable == null && $dataMotivo == null) {

          $paramsbuscarvariables = [':varIndicador' => $dataIndicador, ':varCodPcrcs' => $paramsvarcodigopcrc];
          $listaVariables = Yii::$app->db->createCommand('
          SELECT scc.idcategoria FROM tbl_speech_categorias scc
            INNER JOIN tbl_speech_categorias sc ON 
              scc.tipoindicador = sc.nombre
            WHERE 
              sc.idcategoria = :varIndicador
                AND scc.cod_pcrc IN (:varCodPcrcs)
            GROUP BY scc.idcategoria')->bindValues($paramsbuscarvariables)->queryAll();

          $arralistvariables = array();
          foreach ($listaVariables as $key => $value) {
            array_push($arralistvariables, intval($value['idcategoria']));
          }
          $listavariablesarray = implode(", ", $arralistvariables);

          $arrayIdCategorias = intval($dataIndicador).', '.$listavariablesarray;
          $dataCategorias = explode(",", str_replace(array("#", "'", ";", " "), '', $arrayIdCategorias));


        }

        if ($dataIndicador != null && $dataVariable != null && $dataMotivo == null) {
          $arrayIdCategorias = intval($dataIndicador).', '.intval($dataVariable);
          $dataCategorias = explode(",", str_replace(array("#", "'", ";", " "), '', $arrayIdCategorias));
        }

        if ($dataIndicador != null && $dataVariable != null && $dataMotivo != null) {
          $arrayIdCategorias = intval($dataIndicador).', '.intval($dataVariable).','.intval($dataMotivo);
          $dataCategorias = explode(",", str_replace(array("#", "'", ";", " "), '', $arrayIdCategorias));
        }

        if ($dataIndicador == null && $dataVariable == null && $dataMotivo != null) {
          $arrayIdCategorias = intval($dataMotivo).', 0';
          $dataCategorias = explode(",", str_replace(array("#", "'", ";", " "), '', $arrayIdCategorias));
        }

        if ($dataIndicador != null && $dataVariable == null && $dataMotivo != null) {
          $paramsbuscarvariables = [':varIndicador' => $dataIndicador, ':varCodPcrcs' => $paramsvarcodigopcrc];
          $listaVariables = Yii::$app->db->createCommand('
          SELECT scc.idcategoria FROM tbl_speech_categorias scc
            INNER JOIN tbl_speech_categorias sc ON 
              scc.tipoindicador = sc.nombre
            WHERE 
              sc.idcategoria = :varIndicador
                AND scc.cod_pcrc IN (:varCodPcrcs)
            GROUP BY scc.idcategoria')->bindValues($paramsbuscarvariables)->queryAll();

          $arralistvariables = array();
          foreach ($listaVariables as $key => $value) {
            array_push($arralistvariables, intval($value['idcategoria']));
          }
          $listavariablesarray = implode(", ", $arralistvariables);

          $arrayIdCategorias = intval($dataIndicador).', '.$listavariablesarray.', '.intval($dataMotivo);
          $dataCategorias = explode(",", str_replace(array("#", "'", ";", " "), '', $arrayIdCategorias));
        }

        // Genero consultas para obtener los asesores
        if ($dataLider != null && $dataAsesor == null) {
          $paramsBuscaAsesores = [':varIdLider' => $dataLider];
          $listaAsesores = Yii::$app->db->createCommand('
            SELECT ev.dsusuario_red FROM tbl_evaluados ev
              INNER JOIN  tbl_equipos_evaluados ee ON 
                ev.id = ee.evaluado_id
              INNER JOIN tbl_equipos e ON
                ee.equipo_id = e.id
              WHERE 
                e.id = :varIdLider')->bindValues($paramsBuscaAsesores)->queryAll();

          $arralistasesores = array();
          foreach ($listaAsesores as $key => $value) {
            array_push($arralistasesores, $value['dsusuario_red']);
          }
          $listaasesoresarray = implode(", ", $arralistasesores);

          $dataAsesores = explode(",", str_replace(array("#", "'", ";", " "), '', $listaasesoresarray));

        }

        if ($dataLider != null && $dataAsesor != null) {
          $paramsBuscarAsesor = [':varIdAsesor' => $dataAsesor];
          $usuaRedAsesor = Yii::$app->db->createCommand('
            SELECT e.dsusuario_red FROM tbl_evaluados e
              WHERE 
                e.id = :varIdAsesor')->bindValues($paramsBuscarAsesor)->queryScalar();

          $concatasesor = $usuaRedAsesor.', 0';
          $dataAsesores = explode(",", str_replace(array("#", "'", ";", " "), '', $concatasesor));
        }

        // Genero consulta para obtener callids que tenga tipologias
        if ($dataTipologia != null) {
          $listaCallIds = (new \yii\db\Query())
            ->select(['tbl_dashboardspeechcalls.callId'])
            ->from(['tbl_base_satisfaccion'])
            ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                  'tbl_base_satisfaccion.connid = tbl_dashboardspeechcalls.connid')
            ->where(['=','tbl_dashboardspeechcalls.servicio',$databolsita])
            ->andwhere(['BETWEEN','tbl_dashboardspeechcalls.fechallamada',$paramsvarfechasinicio,$paramsvarfechasfin])
            ->andwhere(['=','tbl_dashboardspeechcalls.anulado',$varAnulados])
            ->andwhere(['IN','tbl_dashboardspeechcalls.extension',$arrayExtTwo])
            ->andwhere(['=','tbl_base_satisfaccion.tipologia',$dataTipologia])
            ->All();

            $arralistacallids = array();
            foreach ($listaCallIds as $key => $value) {
              array_push($arralistacallids, $value['callId']);
            }
            $listacallaidarray = implode(", ", $arralistacallids);
            $dataIdInteraccion = explode(",", str_replace(array("#", "'", ";", " "), '', $listacallaidarray));
        }

        
        // Condicional para buscar las categorizadas y las que no estan categorizadas
        if ($dataContenedor == "2") {
          $dataProvider = (new \yii\db\Query())
            ->select(['*'])
            ->from(['tbl_dashboardspeechcalls'])
            ->where(['=','servicio',$databolsita])
            ->andwhere(['BETWEEN','fechallamada',$paramsvarfechasinicio,$paramsvarfechasfin])
            ->andfilterwhere(['IN','idcategoria',$dataCategorias])
            ->andwhere(['=','anulado',$varAnulados])
            ->andwhere(['IN','extension',$arrayExtTwo])
            ->andfilterwhere(['IN','login_id',$dataAsesores])
            ->andfilterwhere(['IN','callId',$dataIdInteraccion])
            ->All();

        }else{

          $dataProvider = (new \yii\db\Query())
            ->select(['tbl_dashboardspeechcalls.callId','tbl_dashboardspeechcalls.idcategoria','tbl_dashboardspeechcalls.nombreCategoria','tbl_dashboardspeechcalls.extension','tbl_dashboardspeechcalls.login_id','tbl_dashboardspeechcalls.fechallamada','tbl_dashboardspeechcalls.callduracion','tbl_dashboardspeechcalls.servicio','tbl_dashboardspeechcalls.fechareal','tbl_dashboardspeechcalls.idredbox','tbl_dashboardspeechcalls.idgrabadora','tbl_dashboardspeechcalls.connid','tbl_dashboardspeechcalls.extensiones'])
            ->from(['tbl_speech_general'])
            ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                  'tbl_speech_general.callid = tbl_dashboardspeechcalls.callid')
            ->where(['=','tbl_dashboardspeechcalls.servicio',$databolsita])
            ->andwhere(['BETWEEN','tbl_dashboardspeechcalls.fechallamada',$paramsvarfechasinicio,$paramsvarfechasfin])            
            ->andwhere(['=','tbl_dashboardspeechcalls.anulado',$varAnulados])
            ->andwhere('tbl_dashboardspeechcalls.idcategoria != :varGeneralCategoria',[':varGeneralCategoria'=>$dataidgeneral])
            ->andwhere(['IN','tbl_dashboardspeechcalls.extension',$arrayExtTwo])
            ->andfilterwhere(['IN','tbl_dashboardspeechcalls.login_id',$dataAsesores])
            ->andfilterwhere(['IN','tbl_speech_general.idvariable',$dataCategorias])
            ->andfilterwhere(['IN','tbl_speech_general.callId',$dataIdInteraccion])
            ->andwhere(['=','tbl_speech_general.programacliente',$databolsita])
            ->andwhere(['IN','tbl_speech_general.extension',$arrayExtTwo])            
            ->groupBy(['tbl_dashboardspeechcalls.callId'])
            ->All();
          

        }
            
      }else{

        if ($varaleatorios == "1") {

          $dataProvider = (new \yii\db\Query())
            ->select(['*'])
            ->from(['tbl_dashboardspeechcalls'])
            ->where('servicio = :varServicio',[':varServicio'=>$databolsita])
            ->andwhere('fechallamada BETWEEN :varFechainicios AND :varFechafines',[':varFechainicios'=>$paramsvarfechasinicio,':varFechafines'=>$paramsvarfechasfin])
            ->andwhere('idcategoria = :varCategoriGeneral',[':varCategoriGeneral'=>$dataidgeneral])
            ->andwhere('anulado = :varAnulado',[':varAnulado'=>$varAnulados])
            ->andwhere(['IN','extension',$arrayExtTwo])
            ->orderBy(['rand()' => SORT_DESC])
            ->limit($cantidadAleatoria)
            ->All();

        }else{

          $dataProvider = (new \yii\db\Query())
            ->select(['*'])
            ->from(['tbl_dashboardspeechcalls'])
            ->where('servicio = :varServicio',[':varServicio'=>$databolsita])
            ->andwhere('fechallamada BETWEEN :varFechainicios AND :varFechafines',[':varFechainicios'=>$paramsvarfechasinicio,':varFechafines'=>$paramsvarfechasfin])
            ->andwhere('idcategoria = :varCategoriGeneral',[':varCategoriGeneral'=>$dataidgeneral])
            ->andwhere('anulado = :varAnulado',[':varAnulado'=>$varAnulados])
            ->andwhere(['IN','extension',$arrayExtTwo])
            ->All();

        }   
        
      }    

       

      return $this->render('llamadafocalizada',[
        'model' => $model,
        'dataProvider' => $dataProvider,
        'datafechas' => $datafechas,
        'datanamearbol' => $datanamearbol,
        'dataprograma' => $dataprograma,
        'paramsvarfechasinicio' => $paramsvarfechasinicio,
        'paramsvarfechasfin' => $paramsvarfechasfin,
        'paramsvarcodigopcrc' => $paramsvarcodigopcrc,
        'dataidgeneral' => $dataidgeneral,
        'paramsvarextension' => $paramsvarextension,
        'varcantllamadas' => $varcantllamadas,
        'varfechainireal' => $varfechainireal,
        'varfechafinreal' => $varfechafinreal,
        'varcodigos' => $varcodigos,
        'databolsita' => $databolsita,
        'arrayExt' => $arrayExt,
      ]);
    }

    public function actionListarlideresx(){
      $txtanulado = 0;
      $txtidlider = Yii::$app->request->get('id');


      if ($txtidlider) {
        $txtControl = \app\models\Evaluados::find()->distinct()
          ->select(['tbl_evaluados.id','tbl_evaluados.name'])
          ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                              'tbl_evaluados.id = tbl_equipos_evaluados.evaluado_id')
          ->where(['tbl_equipos_evaluados.equipo_id' => $txtidlider])
          ->count();

        if ($txtControl > 0) {
          $varListaLideresx = \app\models\Evaluados::find()->distinct()
          ->select(['tbl_evaluados.id','tbl_evaluados.name'])
          ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                              'tbl_evaluados.id = tbl_equipos_evaluados.evaluado_id')
          ->where(['tbl_equipos_evaluados.equipo_id' => $txtidlider]) 
          ->orderBy(['tbl_evaluados.name' => SORT_DESC])
          ->all();   

          echo "<option value='' disabled selected>Seleccionar Asesor...</option>";
          foreach ($varListaLideresx as $key => $value) {
            echo "<option value='" . $value->id. "'>" . $value->name . "</option>";
          }
        }else{
          echo "<option>--</option>";
        }
      }else{
        echo "<option>Seleccionar variable</option>";
      }                    
    }



  }

?>
