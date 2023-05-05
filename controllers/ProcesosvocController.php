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
use GuzzleHttp;
use app\models\ProcesosAdministrador;
use app\models\SpeechCategorias;
use app\models\SpeechServicios;
use app\models\FormUploadtigo;
use app\models\Dashboardspeechcalls;
use app\models\IdealServicios;
use app\models\SpeechParametrizar;
use \yii\base\Exception;


  class ProcesosvocController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','configcategorias','registarcategorias','configconsultas','botconfigurar','actualizarllamadas','bdideal','bdideal_paso2','actualizaspeech','actualizaspeechespecial','actualizabaseideal','actualizacomdata'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
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
        $model = new ProcesosAdministrador();
      
        return $this->render('index',[
            'model' => $model,
        ]);
    }

    public function actionConfigcategorias(){
      $model = new SpeechCategorias();
      
      $varListadoServicios = (new \yii\db\Query())
            ->select(['id_dp_clientes','nameArbol'])
            ->from(['tbl_speech_servicios'])
            ->where('arbol_id != :varServicio',[':varServicio'=>1])
            ->groupby(['id_dp_clientes'])
            ->all(); 

      return $this->render('configcategorias',[
        'model' => $model,
        'varListadoServicios' => $varListadoServicios,
      ]);
    }

    public function actionRegistarcategorias(){
      $model = new SpeechCategorias();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        $txtCodPcrcs = $model->cod_pcrc;
        $txtPcrc = (new \yii\db\Query())
            ->select(['pcrc'])
            ->from(['tbl_proceso_cliente_centrocosto'])
            ->where('cod_pcrc = :varCodigoPcrc',[':varCodigoPcrc'=>$txtCodPcrcs])
            ->groupby(['pcrc'])
            ->scalar();

        $txtIdCategorias = $model->idcategoria;
        $txtNombreCategoria = $model->nombre;
        $txtTipoCategoria = $model->tipocategoria;
        $txtTipoIndicador = $model->tipoindicador;
        $txtSmart = $model->orientacionsmart;
        $txtForm = $model->orientacionform;
        $txtParametro = $model->tipoparametro;
        $txtPrograma = $model->programacategoria;

        if ($txtParametro == null) {
          $txtParametro = 0;
        }

        if ($txtForm == null) {
          $txtForm = 0;
        }

        if ($txtSmart == null) {
          $txtSmart = 0;
        }

        if ($txtTipoCategoria == "1") {
          $txtTiposCategorias = "Indicador";
        }else{
          if ($txtTipoCategoria == "2") {
            $txtTiposCategorias = "Variable";
          }else{
            $txtTiposCategorias = "Motivo de Contacto";
          }
        }

        if (!isset($txtTipoIndicador)) {
          if ($txtTipoCategoria == "1") {
            $txtTipoIndicador = "Indicador";
          }

          if ($txtTipoCategoria == "3") {
            $txtTipoIndicador = "Motivo de Contacto";
          }
        }

        Yii::$app->db->createCommand()->insert('tbl_speech_categorias',[
                    'pcrc' => $txtPcrc,
                    'cod_pcrc' => $txtCodPcrcs,
                    'idcategoria' => $txtIdCategorias, 
                    'nombre' => $txtNombreCategoria,
                    'tipocategoria' => $txtTiposCategorias,
                    'tipoindicador' => $txtTipoIndicador,
                    'clientecategoria' => null,
                    'orientacionsmart' => $txtSmart,
                    'tipoparametro' => $txtParametro,
                    'orientacionform' => $txtForm,
                    'usua_id' => Yii::$app->user->identity->id,
                    'usabilidad' => 1,
                    'idcategorias' => $txtTipoCategoria,
                    'idciudad' => 1,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'dashboard' => 1,
                    'programacategoria' => $txtPrograma,                         
                ])->execute();


        return $this->redirect('registarcategorias');
      }

      return $this->render('registarcategorias',[
        'model' => $model,
      ]);
    }

    public function actionListarindicadores(){
        $txtanulado = 0;
        $txtcodpcrc = Yii::$app->request->get('id');


          if ($txtcodpcrc) {
            $txtControl = \app\models\SpeechCategorias::find()->distinct()
              ->select(['nombre'])
              ->where('cod_pcrc = :varCodPcrc',[':varCodPcrc'=>$txtcodpcrc])
              ->andwhere('idcategorias = :varIdCategorias',[':varIdCategorias'=>1])
              ->count();

            if ($txtControl > 0) {
              $varListaCodPcrc = \app\models\SpeechCategorias::find()->distinct()
                  ->select(['nombre'])
                  ->where('cod_pcrc = :varCodPcrc',[':varCodPcrc'=>$txtcodpcrc])
                  ->andwhere('idcategorias = :varIdCategorias',[':varIdCategorias'=>1])                 
                  ->all(); 

              echo "<option value='' disabled selected>Seleccionar...</option>";
              foreach ($varListaCodPcrc as $key => $value) {
                echo "<option value='" . $value->nombre. "'>" . $value->nombre . "</option>";
              }
            }else{
              echo "<option>--</option>";
            }
          }else{
            echo "<option>Seleccionar...</option>";
          }          
    }

    public function actionImportarcategorias(){
      $model = new FormUploadtigo();

      if ($model->load(Yii::$app->request->post()))
        {
          $model->file = UploadedFile::getInstances($model, 'file');

          if ($model->file && $model->validate()) {
            foreach ($model->file as $file) {
              $fecha = date('Y-m-d-h-i-s');
              $user = Yii::$app->user->identity->username;
              $name = $fecha . '-' . $user;
              $file->saveAs('categorias/' . $name . '.' . $file->extension);
              $this->Importexcelcategorias($name);

              return $this->redirect('configcategorias');
            }
          }
        }

      return $this->renderAjax('importarcategorias',[
        'model' => $model,
      ]);
    }

    public function Importexcelcategorias($name){
      $inputFile = 'categorias/' . $name . '.xlsx';

      try {
        $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
        $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
        $objPHPExcel = $objReader->load($inputFile);
      } catch (Exception $e) {
        die('Error');
      }

      $sheet = $objPHPExcel->getSheet(0);
      $highestRow = $sheet->getHighestRow();

      for ($row = 1; $row <= $highestRow; $row++) { 
        if ($sheet->getCell("A".$row)->getValue() != null) {
          $varCategoriasId = 0;

          $varTipoCategorias = $sheet->getCell("E".$row)->getValue();
          if ($varTipoCategorias == "Indicador") {
            $varCategoriasId = 1;
          }

          if ($varTipoCategorias == "Variable") {
            $varCategoriasId = 2;
          }

          if ($varTipoCategorias == "Motivo de Contacto" || $varTipoCategorias == "Motivos de Contactos") {
            $varCategoriasId = 3;
          }

          $varSmart = $sheet->getCell("G".$row)->getValue();
          if ($varSmart == "Positivo" || $varSmart == "positivo") {
            $varValorSmart = 2; 
          }

          if ($varSmart == "Negativo" || $varSmart == "negativo") {
            $varValorSmart = 1; 
          }

          if ($varSmart == "0") {
            $varValorSmart = 0; 
          }

          $varParametro = $sheet->getCell("H".$row)->getValue();
          if ($varParametro == "Desempeño" || $varParametro == "desempeño") {
            $varValorParams = 2; 
          }

          if ($varParametro == "Auditoria" || $varParametro == "auditoria") {
            $varValorParams = 1; 
          }

          if ($varParametro == "0") {
            $varValorParams = 0; 
          }

          $varForm = $sheet->getCell("I".$row)->getValue();
          if ($varForm == "Positivo" || $varForm == "positivo") {
            $varValorForm = 0; 
          }

          if ($varForm == "Negativo" || $varForm == "negativo") {
            $varValorForm = 1; 
          }

          if ($varForm == "0") {
            $varValorForm = 0; 
          }

          
          Yii::$app->db->createCommand()->insert('tbl_speech_categorias',[
                    'pcrc' => $sheet->getCell("A".$row)->getValue(),
                    'cod_pcrc' => $sheet->getCell("B".$row)->getValue(),
                    'idcategoria' => $sheet->getCell("C".$row)->getValue(), 
                    'nombre' => $sheet->getCell("D".$row)->getValue(),
                    'tipocategoria' => $varTipoCategorias,
                    'tipoindicador' => $sheet->getCell("F".$row)->getValue(),
                    'orientacionsmart' => $varValorSmart,
                    'tipoparametro' => $varValorParams,
                    'orientacionform' => $varValorForm,
                    'usua_id' => Yii::$app->user->identity->id,
                    'usabilidad' => 1,
                    'idcategorias' => $varCategoriasId,
                    'idciudad' => 1,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'dashboard' => 1,
                    'programacategoria' => $sheet->getCell("I".$row)->getValue(),                         
                ])->execute();

        }
      }


    }

    public function actionConfigconsultas(){
      $model = new Dashboardspeechcalls();

      return $this->render('configconsultas',[
        'model' => $model,
      ]);
    }

    public function actionbotconfigurar(){
      $model = new Dashboardspeechcalls();

      return $this->render('botconfigurar',[
        'model' => $model,
      ]);
    }

    public function actionActualizarllamadas(){
      $model = new SpeechParametrizar();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        $txtiddpclientes = $model->id_dp_clientes;
        $varFechas = explode(" ", $model->fechacreacion);

        $txtFechaInicio = $varFechas[0];
        $txtFechaFin = date('Y-m-d',strtotime($varFechas[2]));

        return $this->redirect(['botspeech',
          'txtiddpclientes' => $txtiddpclientes,
          'txtFechaInicio' => $txtFechaInicio,
          'txtFechaFin' => $txtFechaFin,
        ]);
      }

      return $this->render('actualizarllamadas',[
        'model' => $model,
      ]);
    }


    public function actionBotspeech($txtiddpclientes,$txtFechaInicio,$txtFechaFin){
      $model = new Dashboardspeechcalls();

      $varBolsita = (new \yii\db\Query())
            ->select(['tbl_speech_categorias.programacategoria'])
            ->from(['tbl_speech_categorias'])
            ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
            ->where(['=','tbl_speech_parametrizar.id_dp_clientes',$txtiddpclientes])
            ->groupby(['tbl_speech_categorias.programacategoria'])
            ->all();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        $txtServicioSpeech = $model->servicio;
        $txtPcrcInfo = $model->extension;
        var_dump($txtPcrcInfo);

        if ($txtPcrcInfo != null) {

        }else{
          $varFechaInicioSpeech = $txtFechaInicio.' 05:00:00';
          $varFechaFinSpeech = date('Y-m-d',strtotime($txtFechaFin."+ 1 days")).' 05:00:00';

          $paramsEliminar = [':Anuado'=>0, ':Servicio_CXM'=>$txtServicioSpeech,':Fecha_Inicio'=>$varFechaInicioSpeech,':Fecha_Fin'=>$varFechaFinSpeech];          

          Yii::$app->db->createCommand('
              DELETE FROM tbl_dashboardspeechcalls 
                WHERE 
                  anulado = :Anuado
                    AND servicio IN (:Servicio_CXM)
                      AND fechallamada BETWEEN :Fecha_Inicio AND :Fecha_Fin')
            ->bindValues($paramsEliminar)
            ->execute();

          Yii::$app->db->createCommand('
              DELETE FROM tbl_speech_general 
                WHERE 
                  anulado = :Anuado
                    AND programacliente IN (:Servicio_CXM)
                      AND fechallamada BETWEEN :Fecha_Inicio AND :Fecha_Fin')
            ->bindValues($paramsEliminar)
            ->execute();
        }

       return $this->redirect('actualizarllamadas');
      }

      return $this->render('botspeech',[
        'model' => $model,
        'varBolsita' => $varBolsita,
      ]);
    }

    public function actionListarpcrcs(){
      $txtBolsita = Yii::$app->request->get('id');

      if ($txtBolsita) {
        $txtIdCliente = (new \yii\db\Query())
            ->select(['tbl_speech_parametrizar.id_dp_clientes'])
            ->from(['tbl_speech_parametrizar'])
            ->join('LEFT OUTER JOIN', 'tbl_speech_categorias',
                                  'tbl_speech_parametrizar.cod_pcrc = tbl_speech_categorias.cod_pcrc')
            ->where(['=','tbl_speech_categorias.programacategoria',$txtBolsita])
            ->andwhere(['=','tbl_speech_categorias.anulado',0])
            ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
            ->andwhere(['!=','tbl_speech_parametrizar.id_dp_clientes',1])
            ->groupby(['tbl_speech_parametrizar.id_dp_clientes'])
            ->scalar();

        $txtControl = $txtControl = \app\models\SpeechCategorias::find()->distinct()
              ->select(['tbl_speech_categorias.cod_pcrc'])
              ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
              ->where(['=','tbl_speech_parametrizar.id_dp_clientes',$txtIdCliente])
              ->andwhere(['=','tbl_speech_categorias.programacategoria',$txtBolsita])
              ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
              ->count();

        if ($txtControl > 0) {
          $txtListarPcrc = $txtControl = \app\models\SpeechCategorias::find()->distinct()
              ->select(['tbl_speech_categorias.cod_pcrc','tbl_speech_categorias.pcrc'])
              ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
              ->where(['=','tbl_speech_parametrizar.id_dp_clientes',$txtIdCliente])
              ->andwhere(['=','tbl_speech_categorias.programacategoria',$txtBolsita])
              ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
              ->all();

          echo "<option value='' disabled selected>Seleccionar...</option>";
          echo "<option value='001' disabled selected>Todos</option>";
          foreach ($txtListarPcrc as $key => $value) {
            echo "<option value='" . $value->cod_pcrc. "'>" . $value->cod_pcrc.' - '.$value->pcrc . "</option>";
          }
        }else{
          echo "<option>--</option>";
        }
      }else{
        echo "<option>Seleccionar...</option>";
      }

    }

    public function actionBdideal(){
      $model = new SpeechServicios();

      $form = Yii::$app->request->post();
      if($model->load($form)){
        ini_set("max_execution_time", "900");
        ini_set("memory_limit", "1024M");
        ini_set( 'post_max_size', '1024M' );

        ignore_user_abort(true);
        set_time_limit(900);

        $varIdCliente = $model->id_dp_clientes;        
        $varMes = $model->pcrc;

        $varFechaInicio = date('Y-'.$varMes.'-01');
        $varFechaFin = date('Y-m-t', strtotime($varFechaInicio));

        $varFechaInicioSpeech = $varFechaInicio.' 05:00:00';
        $varFechaFinSpeech = date('Y-m-d',strtotime($varFechaFin."+ 1 days")).' 05:00:00';

        $varCategoriaGeneral =  (new \yii\db\Query())
                                ->select(['idllamada'])
                                ->from(['tbl_speech_servicios'])            
                                ->where(['=','id_dp_clientes',$varIdCliente])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['idllamada'])
                                ->scalar();

        $varlistPcrc =  (new \yii\db\Query())
                        ->select(['cod_pcrc'])
                        ->from(['tbl_speech_parametrizar'])            
                        ->where(['=','id_dp_clientes',$varIdCliente])
                        ->andwhere(['=','anulado',0])
                        ->groupby(['cod_pcrc'])
                        ->all();

        foreach ($varlistPcrc as $key => $value) {
          $varExtensiones = null;
          $varCodPcrcs = $value['cod_pcrc'];

          $varBolsitas = (new \yii\db\Query())
                              ->select(['programacategoria'])
                              ->from(['tbl_speech_categorias'])            
                              ->where(['=','cod_pcrc',$varCodPcrcs])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['programacategoria'])
                              ->Scalar();          

          if ($varBolsitas) {

            $varExisteModificar = (new \yii\db\Query())
                              ->select(['cod_pcrc'])
                              ->from(['tbl_ideal_serviciogeneral'])            
                              ->where(['=','cod_pcrc',$varCodPcrcs])
                              ->andwhere(['=','anulado',0])
                              ->andwhere(['=','fecha_inicio',$varFechaInicioSpeech])
                              ->andwhere(['=','fecha_fin',$varFechaFinSpeech])
                              ->count();  

            if ($varExisteModificar != 0) {
               $paramsModificarGeneral = [':AnuladoCero'=>0, ':AnuladoOne'=>1, ':Servicio_CXM'=>$varIdCliente,':Fecha_Inicio'=>$varFechaInicioSpeech,':Fecha_Fin'=>$varFechaFinSpeech,':cod_pcrc'=>$varCodPcrcs];

              Yii::$app->db->createCommand('
                  UPDATE tbl_ideal_serviciogeneral 
                    SET
                      anulado = :AnuladoOne
                    WHERE 
                      anulado = :AnuladoCero
                        AND cod_pcrc = :cod_pcrc
                          AND id_dp_clientes IN (:Servicio_CXM)
                            AND fecha_inicio = :Fecha_Inicio
                              AND fecha_fin = :Fecha_Fin ')
                ->bindValues($paramsModificarGeneral)
                ->execute();
             }   

            // Se buscan las extensiones de cada pcrc
            $varReglaNegocio =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcs])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['rn'])
                                ->all();

            if (count($varReglaNegocio) != 0) {
              $varArrayRn = array();
              foreach ($varReglaNegocio as $key => $value) {
                array_push($varArrayRn, $value['rn']);
              }

              $varExtensionesArrays = implode("', '", $varArrayRn);
              $arrayExtensiones_down = str_replace(array("#", "'", ";", " "), '', $varExtensionesArrays);
              $varExtensiones = explode(",", $arrayExtensiones_down);
            }else{
              $varExt =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcs])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['ext'])
                                ->all();

              if (count($varExt) != 0) {
                $varArrayExt = array();
                foreach ($varExt as $key => $value) {
                  array_push($varArrayExt, $value['ext']);
                }
                $varExtensionesArrays = implode("', '", $varArrayExt);
                $arrayExtensiones_down = str_replace(array("#", "'", ";", " "), '', $varExtensionesArrays);
                $varExtensiones = explode(",", $arrayExtensiones_down);
              }else{
                $varUsua =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodPcrcs])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['usuared'])
                                ->all();

                if (count($varUsua) != 0) {
                  $varArrayUsua = array();
                  foreach ($varUsua as $key => $value) {
                    array_push($varArrayUsua, $value['usuared']);
                  }
                  $varExtensionesArrays = implode("', '", $varArrayUsua);
                  $arrayExtensiones_down = str_replace(array("#", "'", ";", " "), '', $varExtensionesArrays);
                  $varExtensiones = explode(",", $arrayExtensiones_down);
                }else{
                  $varExtensiones = "NA";
                }
              }
            }

            //  Se buscan los Callid
            $varListCallid = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$varBolsitas])
                                ->andwhere(['in','extension',$varExtensiones])
                                ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                ->groupby(['callid'])
                                ->all();

            $varArrayCallid = array();
            foreach ($varListCallid as $key => $value) {
              array_push($varArrayCallid, $value['callid']);
            }
            $varCallidsList = implode(", ", $varArrayCallid);
            $arrayCallids_down = str_replace(array("#", "'", ";", " "), '', $varCallidsList);
            $varCallids = explode(",", $arrayCallids_down);

            //  Se buscan la cantidad de llamadas por cada pcrc y sus extensiones
            $varCantidadLlamadas = (new \yii\db\Query())
                                ->select(['callId'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varBolsitas])
                                ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                ->andwhere(['=','idcategoria',$varCategoriaGeneral])
                                ->andwhere(['in','extension',$varExtensiones])
                                ->count();

            //  Se Buscan los porcentajes de cada Indicador
            $varlistIndicadores = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','cod_pcrc',$varCodPcrcs])
                                ->andwhere(['=','idcategorias',1])
                                ->all();                                        

            // Se guardan los datos de cada pcrc
            Yii::$app->db->createCommand()->insert('tbl_ideal_serviciogeneral',[
                    'id_dp_clientes' => $varIdCliente,
                    'cod_pcrc' => $varCodPcrcs,
                    'extensiones' => $varExtensionesArrays, 
                    'cantidad_llamadas' => $varCantidadLlamadas,
                    'fecha_inicio' => $varFechaInicioSpeech,
                    'fecha_fin' => $varFechaFinSpeech,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,                        
                ])->execute();

            // Se busca el id principal
            $varIdgeneral = (new \yii\db\Query())
                              ->select(['id_serviciogeneral'])
                              ->from(['tbl_ideal_serviciogeneral'])            
                              ->where(['=','cod_pcrc',$varCodPcrcs])
                              ->andwhere(['=','anulado',0])
                              ->andwhere(['=','fecha_inicio',$varFechaInicioSpeech])
                              ->andwhere(['=','fecha_fin',$varFechaFinSpeech])
                              ->scalar();  

            // Se elimina datos de los indicadores
            $varlistarGeneral = (new \yii\db\Query())
                                ->select(['id_serviciogeneral'])
                                ->from(['tbl_ideal_serviciogeneral'])            
                                ->where(['=','anulado',1])
                                ->andwhere(['=','cod_pcrc',$varCodPcrcs])
                                ->all();  
            foreach ($varlistarGeneral as $key => $value) {
              $paramsEliminarGeneral = [':idGenerico'=>$value['id_serviciogeneral']];          

              Yii::$app->db->createCommand('
                  DELETE FROM tbl_ideal_servicioindicadores 
                    WHERE 
                      id_serviciogeneral = :idGenerico')
                ->bindValues($paramsEliminarGeneral)
                ->execute();

              Yii::$app->db->createCommand('
                  DELETE FROM tbl_ideal_servicioresponsables 
                    WHERE 
                      id_serviciogeneral = :idGenerico')
                ->bindValues($paramsEliminarGeneral)
                ->execute();

              Yii::$app->db->createCommand('
                  DELETE FROM tbl_ideal_serviciovariables 
                    WHERE 
                      id_serviciogeneral = :idGenerico')
                ->bindValues($paramsEliminarGeneral)
                ->execute();

              Yii::$app->db->createCommand('
                  DELETE FROM tbl_ideal_serviciomotivos 
                    WHERE 
                      id_serviciogeneral = :idGenerico')
                ->bindValues($paramsEliminarGeneral)
                ->execute();
            }  
            
            // Se genera el proceso para verificar por indicadores
            foreach ($varlistIndicadores as $key => $value) {
              $txtIdIndicadores = $value['idcategoria'];
              $varNombreIndicador = $value['nombre'];
              $varTipoParametro = $value['tipoparametro'];
              $txtTipoFormIndicador = $value['orientacionform'];

              // Se realiza proceso de verificacion de variables positivas y negativas
              $varListVariables = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform','responsable'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','cod_pcrc',$varCodPcrcs])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','tipoindicador',$varNombreIndicador])
                                ->all();

              $arrayListOfVar = array();
              $arraYListOfVarMas = array();
              $arraYListOfVarMenos = array();
              $varSumarPositivas = 0;
              $varSumarNegativas = 0;
              $arrayRAgente = array();
              $arrayRMarca = array();
              $arrayRCanal = array();
              foreach ($varListVariables as $key => $value) {
                $varOrienta = $value['orientacionsmart'];
                $varResponsable = $value['responsable'];
                array_push($arrayListOfVar, $value['idcategoria']);

                if ($varOrienta == 1) {
                  array_push($arraYListOfVarMenos, $value['idcategoria']);
                  $varSumarNegativas = $varSumarNegativas + 1;
                }else{
                  array_push($arraYListOfVarMas, $value['idcategoria']);
                  $varSumarPositivas = $varSumarPositivas + 1;
                }

                if ($varResponsable == 1) {
                  array_push($arrayRAgente, $value['idcategoria']);
                }else{
                  if ($varResponsable == 2) {
                    array_push($arrayRCanal, $value['idcategoria']);
                  }else{
                    if ($varResponsable == 3) {
                      array_push($arrayRMarca, $value['idcategoria']);
                    }else{
                      $varna = 0;
                    }
                    
                  }
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

              $arrayRAgenteList = implode(", ", $arrayRAgente);
              $arrayRAgente_down = str_replace(array("#", "'", ";", " "), '', $arrayRAgenteList);
              $arrayAgente = explode(",", $arrayRAgente_down);

              $arrayRCanalList = implode(", ", $arrayRCanal);
              $arrayRCanal_down = str_replace(array("#", "'", ";", " "), '', $arrayRCanalList);
              $arrayCanal = explode(",", $arrayRCanal_down);

              $arrayRMarcaList = implode(", ", $arrayRMarca);
              $arrayRMarca_down = str_replace(array("#", "'", ";", " "), '', $arrayRMarcaList);
              $arrayMarca = explode(",", $arrayRMarca_down);              


              $varTotalvariables = count($varListVariables);

              // Se realiza proceso de conteo
              if ($varTipoParametro == "2") {
                
                if ($varSumarPositivas == $varTotalvariables) {
                  $varconteo = (new \yii\db\Query())
                                ->select(['callid','SUM(cantproceso)'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$varBolsitas])
                                ->andwhere(['in','extension',$varExtensiones])
                                ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                ->andwhere(['in','callid',$varCallids])
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
                                ->andwhere(['=','programacliente',$varBolsitas])
                                ->andwhere(['in','extension',$varExtensiones])
                                ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                ->andwhere(['in','callid',$varCallids])
                                ->andwhere(['in','idindicador',$arrayVariableMenos])
                                ->andwhere(['in','idvariable',$arrayVariableMenos])
                                ->groupby(['callid'])
                                ->count();
                  
                  if ($varconteo != null) {
                    $varconteo = round(count($varListCallid) - $varconteo);                
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
                                ->andwhere(['=','programacliente',$varBolsitas])
                                ->andwhere(['in','extension',$varExtensiones])
                                ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                ->andwhere(['in','callid',$varCallids])
                                ->andwhere(['in','idindicador',$arrayVariableMas])
                                ->andwhere(['in','idvariable',$arrayVariableMas])
                                ->groupby(['callid'])
                                ->count();
                }else{
                    $varconteo = 0;

                }

                if ($arrayVariableMenos != "") {
                  $varconteo = $varconteo = (new \yii\db\Query())
                                ->select(['callid','SUM(cantproceso)'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$varBolsitas])
                                ->andwhere(['in','extension',$varExtensiones])
                                ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                ->andwhere(['in','callid',$varCallids])
                                ->andwhere(['in','idindicador',$arrayVariableMas])
                                ->andwhere(['in','idvariable',$arrayVariableMas])
                                ->groupby(['callid'])
                                ->count();
                }else{
                  $varconteo = 0;
                }

              }             

              if ($varconteo != 0) {
                
                if ($txtTipoFormIndicador == 0) {
                  $txtRtaProcentaje = (round(($varconteo / $varCantidadLlamadas) * 100, 1));
                }else{
                  $txtRtaProcentaje = (100 - (round(($varconteo / $varCantidadLlamadas) * 100, 1)));
                }

              }else{

                if ($txtTipoFormIndicador == 0) {
                  $txtRtaProcentaje = 100;
                }else{
                  $txtRtaProcentaje = 0;
                }

              }     

              //  Se guardan los valores en porcentajes de cada indicador
              Yii::$app->db->createCommand()->insert('tbl_ideal_servicioindicadores',[
                    'id_serviciogeneral' => $varIdgeneral,
                    'id_categoria_indicador' => $txtIdIndicadores,
                    'nombre_indicador' => $varNombreIndicador, 
                    'porcentaje' => $txtRtaProcentaje,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,                        
                ])->execute(); 

              // Conteo Agente
              $varconteoAgente =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$varBolsitas])
                                  ->andwhere(['in','extension',$varExtensiones])
                                  ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                  ->andwhere(['in','callid',$varCallids])
                                  ->andwhere(['in','idindicador',$arrayAgente])
                                  ->andwhere(['in','idvariable',$arrayAgente])
                                  ->groupby(['callid'])
                                  ->count();

              if ($varconteoAgente == null) {
                $varconteoAgente = 0;
              }
                            
              if ($varconteoAgente != 0) {
                
                if ($txtTipoFormIndicador == 0) {
                  $txtRtaAgente = (round(($varconteoAgente / $varCantidadLlamadas) * 100, 1));
                }else{
                  $txtRtaAgente = (100 - (round(($varconteoAgente / $varCantidadLlamadas) * 100, 1)));
                }

              }else{
                $txtRtaAgente = 0;
              }

              //  Se guardan los valores en porcentajes de la responsabilidad Agente
              Yii::$app->db->createCommand()->insert('tbl_ideal_servicioresponsables',[
                    'id_serviciogeneral' => $varIdgeneral,
                    'id_categoria_indicador' => $txtIdIndicadores,
                    'responsable' => 'Agente', 
                    'porcentaje_responsable' => $txtRtaAgente,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,                        
                ])->execute(); 

              // Conteo Marca
              $varconteoMarca =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$varBolsitas])
                                  ->andwhere(['in','extension',$varExtensiones])
                                  ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                  ->andwhere(['in','callid',$varCallids])
                                  ->andwhere(['in','idindicador',$arrayMarca])
                                  ->andwhere(['in','idvariable',$arrayMarca])
                                  ->groupby(['callid'])
                                  ->count();

              if ($varconteoMarca == null) {
                $varconteoMarca = 0;
              }
              
              if ($varconteoMarca != 0) {
                
                if ($txtTipoFormIndicador == 0) {
                  $txtRtaMarca = (round(($varconteoMarca / $varCantidadLlamadas) * 100, 1));
                }else{
                  $txtRtaMarca = (100 - (round(($varconteoMarca / $varCantidadLlamadas) * 100, 1)));
                }

              }else{
                $txtRtaMarca = 0;
              }

              //  Se guardan los valores en porcentajes de la responsabilidad Marca
              Yii::$app->db->createCommand()->insert('tbl_ideal_servicioresponsables',[
                    'id_serviciogeneral' => $varIdgeneral,
                    'id_categoria_indicador' => $txtIdIndicadores,
                    'responsable' => 'Marca', 
                    'porcentaje_responsable' => $txtRtaMarca,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,                        
                ])->execute(); 

              // Conteo Canal
              $varconteoCanal =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$varBolsitas])
                                  ->andwhere(['in','extension',$varExtensiones])
                                  ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                  ->andwhere(['in','callid',$varCallids])
                                  ->andwhere(['in','idindicador',$arrayCanal])
                                  ->andwhere(['in','idvariable',$arrayCanal])
                                  ->groupby(['callid'])
                                  ->count();

              if ($varconteoCanal == null) {
                $varconteoCanal = 0;
              }
              
              if ($varconteoCanal != 0) {
                
                if ($txtTipoFormIndicador == 0) {
                  $txtRtaCanal = (round(($varconteoCanal / $varCantidadLlamadas) * 100, 1));
                }else{
                  $txtRtaCanal = (100 - (round(($varconteoCanal / $varCantidadLlamadas) * 100, 1)));
                }

              }else{
                $txtRtaCanal = 0;
              }

              //  Se guardan los valores en porcentajes de la responsabilidad Canal
              Yii::$app->db->createCommand()->insert('tbl_ideal_servicioresponsables',[
                    'id_serviciogeneral' => $varIdgeneral,
                    'id_categoria_indicador' => $txtIdIndicadores,
                    'responsable' => 'Canal', 
                    'porcentaje_responsable' => $txtRtaCanal,
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,                        
                ])->execute(); 

              // Se busca los porcentajes y la cantidad de llamadas por Variable
              foreach ($varListVariables as $key => $value) {
                $varVariables = $value['idcategoria'];
                $varNombreVariable = $value['nombre'];

                // Porcentajes Variable 
                $varConteoPorVariable =  (new \yii\db\Query())
                                          ->select(['callid','SUM(cantproceso)'])
                                          ->from(['tbl_speech_general'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','programacliente',$varBolsitas])
                                          ->andwhere(['in','extension',$varExtensiones])
                                          ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                          ->andwhere(['in','callid',$varCallids])
                                          ->andwhere(['in','idindicador',$varVariables])
                                          ->andwhere(['in','idvariable',$varVariables])
                                          ->groupby(['callid'])
                                          ->count();

                if ($txtTipoFormIndicador == 1) {
                  $txtRtaPorcentajeVariable = (round(($varConteoPorVariable / $varCantidadLlamadas) * 100, 1));
                }else{
                  $txtRtaPorcentajeVariable = (100 - (round(($varConteoPorVariable / $varCantidadLlamadas) * 100, 1)));
                }

                // Cantidad llamadas Variable
                $varLlamadasVariable =  (new \yii\db\Query())
                                          ->select(['idcategoria'])
                                          ->from(['tbl_dashboardspeechcalls'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','servicio',$varBolsitas])
                                          ->andwhere(['in','extension',$varExtensiones])
                                          ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                          ->andwhere(['in','idcategoria',$varVariables])
                                          ->groupby(['callid'])
                                          ->count();

                //  Se guardan los valores en porcentajes y cantidad de llamadas de las variables
                Yii::$app->db->createCommand()->insert('tbl_ideal_serviciovariables',[
                      'id_serviciogeneral' => $varIdgeneral,
                      'id_categoria_indicador' => $txtIdIndicadores,
                      'id_categoria_variable' => $varVariables,
                      'variable' => $varNombreVariable, 
                      'porcentaje_variable' => $txtRtaPorcentajeVariable,
                      'cantidad_llamadas' => $varLlamadasVariable,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
                  ])->execute(); 


              }             
              
            }

            // Se Busca el listado de los motivos de contacto
            $varListMotivos = (new \yii\db\Query())
                              ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                              ->from(['tbl_speech_categorias'])            
                              ->where(['=','anulado',0])
                              ->andwhere(['=','cod_pcrc',$varCodPcrcs])
                              ->andwhere(['=','idcategorias',3])
                              ->all();

            foreach ($varListMotivos as $key => $value) {
              $varMotivo = $value['idcategoria'];
              $varNombreMotivo = $value['nombre'];

              // Porcentajes Motivos 
              $varConteoPorMotivos =  (new \yii\db\Query())
                                      ->select(['callid'])
                                      ->from(['tbl_dashboardspeechcalls'])            
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','servicio',$varBolsitas])
                                      ->andwhere(['in','extension',$varExtensiones])
                                      ->andwhere(['between','fechallamada',$varFechaInicioSpeech,$varFechaFinSpeech])
                                      ->andwhere(['in','idcategoria',$varMotivo])
                                      ->groupby(['callid'])
                                      ->count();

              if ($varConteoPorMotivos != null) {
                $txtRtaPorcentajeMotivo = (round(($varConteoPorMotivos / $varCantidadLlamadas) * 100, 1));
              }else{
                $txtRtaPorcentajeMotivo = 0;
              }

              //  Se guardan los valores en porcentajes y cantidad de llamadas de los motivos de contacto
                Yii::$app->db->createCommand()->insert('tbl_ideal_serviciomotivos',[
                      'id_serviciogeneral' => $varIdgeneral,
                      'id_categoria_motivo' => $varMotivo,
                      'motivo' => $varNombreMotivo, 
                      'porcentaje_motivo' => $txtRtaPorcentajeMotivo,
                      'cantidad_llamadas_motivos' => $varConteoPorMotivos,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
                  ])->execute(); 

            }

          }
        }

        return $this->redirect(['bdideal_paso2',
          'txtidservicio' => $varIdCliente,
          'txtMeses' => $varMes,
          'txtFechaInicioSpeech' => $varFechaInicioSpeech,
          'txtFechaFinSpeech' => $varFechaFinSpeech,
        ]);

      }

      return $this->renderAjax('bdideal',[
        'model' => $model,
      ]);
    }

    public function actionBdideal_paso2($txtidservicio,$txtMeses,$txtFechaInicioSpeech,$txtFechaFinSpeech){

      return $this->render('bdideal_paso2');
    }

    public function actionActualizaspeech(){
      $model = new SpeechServicios();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varIdCliente = $model->cliente;
        $txtvarFecha = explode(" ", $model->fechacreacion);

        $varFechaInicio = $txtvarFecha[0];
        $varFechaFin = date('Y-m-d',strtotime($txtvarFecha[2]."+ 1 days"));

        $varllamadaid = $model->idllamada;
        $varTipoServicio = $model->anulado;

        if ($varTipoServicio == 1) {

          $varConcatenarLlamadas = $varIdCliente."; ".$varFechaInicio."; ".$varFechaFin."; ".$varllamadaid;
          $this->Actualizallamadasspeech($varConcatenarLlamadas);

        }else{
          $varConcatenarChat = $varIdCliente."; ".$varFechaInicio."; ".$varFechaFin."; ".$varllamadaid;
          $this->Actualizachatspeech($varConcatenarChat);          
        }

        return $this->redirect('actualizarllamadas');

      }

      return $this->renderAjax('actualizaspeech',[
        'model' => $model,
      ]);
    }

    public function Actualizachatspeech($varConcatenarChat){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteChat = null;
      $varFechaInicioChat = null;
      $varFechaFinChat = null;
      $varConnidChat = null;
      $varCategoriaGeneralChat = null;

      $varListaItemsChat = explode("; ", $varConcatenarChat);
      for ($i=0; $i < count($varListaItemsChat); $i++) { 
        $varIdClienteChat = $varListaItemsChat[0];
        $varFechaInicioChat = $varListaItemsChat[1];
        $varFechaFinChat = $varListaItemsChat[2];
        $varConnidChat = $varListaItemsChat[3];
      }

      $varCategoriaGeneralChat = (new \yii\db\Query())
                              ->select(['idllamada'])
                              ->from(['tbl_speech_servicios'])            
                              ->where(['=','id_dp_clientes',$varIdClienteChat])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['idllamada'])
                              ->Scalar();

      $varBaseRealChat = (new \yii\db\Query())
                              ->select(['base_real'])
                              ->from(['tbl_speech_servicios'])            
                              ->where(['=','id_dp_clientes',$varIdClienteChat])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['base_real'])
                              ->Scalar();

      $varListaPcrcChat = (new \yii\db\Query())
                    ->select(['cod_pcrc'])
                    ->from(['tbl_speech_parametrizar'])            
                    ->where(['=','id_dp_clientes',$varIdClienteChat])
                    ->andwhere(['=','anulado',0])
                    ->andwhere(['=','tipospeech',1])
                    ->andwhere(['=','usabilidad',1])
                    ->groupby(['cod_pcrc'])
                    ->all();

      foreach ($varListaPcrcChat as $key => $value) {
        $varCodpcrcChat = $value['cod_pcrc'];

        $varReglaNegocioChat =  (new \yii\db\Query())
                                  ->select(['rn'])
                                  ->from(['tbl_speech_parametrizar'])            
                                  ->where(['=','cod_pcrc',$varCodpcrcChat])
                                  ->andwhere(['=','anulado',0])
                                  ->groupby(['rn'])
                                  ->all();

        if (count($varReglaNegocioChat) != 0) {

          $varArrayRnChat = array();
          foreach ($varReglaNegocioChat as $key => $value) {
            array_push($varArrayRnChat, $value['rn']);
          }

          $varExtensionesChat = implode("', '", $varArrayRnChat); 
          $varExtensionSpeechChat = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesChat));
          $varTipoExtensionesChat = "regla_negocio";

        }else{

          $varExtChat =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcChat])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['ext'])
                                ->all();

          if (count($varExtChat) != 0) {
            
            $varArrayExtChat = array();
            foreach ($varExtChat as $key => $value) {
              array_push($varArrayExtChat, $value['ext']);
            }

            $varExtensionesChat = implode("', '", $varArrayExtChat);
            $varExtensionSpeechChat = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesChat)); 
            $varTipoExtensionesChat = "extension"; 

          }else{

            $varUsuaChat =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcChat])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['usuared'])
                                ->all();

            if (count($varUsuaChat) != 0) {
              
              $varArrayUsuaChat = array();
              foreach ($varUsuaChat as $key => $value) {
                array_push($varArrayUsuaChat, $value['usuared']);
              }

              $varExtensionesChat = implode("', '", $varArrayUsuaChat);
              $varExtensionSpeechChat = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesChat));
              $varTipoExtensionesChat = "login_id";

            }else{

              $varExtensionesChat = "NA";

            }
          }
        }

        $varListaCategoriasChat = (new \yii\db\Query())
                                  ->select(['idcategoria'])
                                  ->from(['tbl_speech_categorias'])            
                                  ->where(['=','cod_pcrc',$varCodpcrcChat])
                                  ->andwhere(['=','anulado',0])
                                  ->groupby(['idcategoria'])
                                  ->all();

        $arraylistcategoriasChat = array();
        foreach ($varListaCategoriasChat as $key => $value) {
          array_push($arraylistcategoriasChat, $value['idcategoria']);
        }
        $txtListaCategoriasChat = implode(", ", $arraylistcategoriasChat);

        $varBolsitaCXChat = (new \yii\db\Query())
                                ->select(['programacategoria'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','cod_pcrc',$varCodpcrcChat])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['programacategoria'])
                                ->Scalar(); 

        if ($varBaseRealChat == "1114") {
          
          $varListaChat = Yii::$app->get('dbSpeechA2')->createCommand("
            SELECT DISTINCT (b.callId), a.categoryid AS CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.originalTime,'19700101') AS Fecha_Llamada, dd.fieldValue AS COLUMN1, e.name AS Servicio, DATEADD(s,c.originalTime,'19700101') AS Fechareal, c.externalTextId AS idredbox, 'NA' AS idgrabadora

              FROM 
              [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b,                       
              [speechminer_8_5_512_A2].[dbo].[TextData] c,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e   
                                                                           
              WHERE 
                DATEADD(s,c.originalTime,'19700101') BETWEEN '$varFechaInicioChat 00:00:00' AND '$varFechaFinChat 00:00:00'
                  AND e.name IN ('$varBolsitaCXChat')
                    AND a.categoryid IN ($varBaseRealChat, $txtListaCategoriasChat)
                      AND d.fieldName = '$varTipoExtensionesChat'  
                        AND d.fieldValue IN ('$varExtensionesChat')
                          AND dd.fieldName = 'duracion_bot'
                            AND d1.fieldName = 'nombre_agentes'
                              AND a.categoryId = b.categoryId
                                AND b.callId=c.textId
                                  AND d.callId=c.textId
                                    AND dd.callId=c.textId
                                      AND d1.callId=c.textId
                                        AND e.programId=c.programId
            ")->queryAll();

        }

        if ($varBaseRealChat == "1105") {
          
          $varListaChat = Yii::$app->get('dbSpeechE1')->createCommand("
            SELECT DISTINCT (b.callId), a.categoryid AS CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.originalTime,'19700101') AS Fecha_Llamada, dd.fieldValue AS COLUMN1, e.name AS Servicio, DATEADD(s,c.originalTime,'19700101') AS Fechareal, c.externalTextId AS idredbox, 'NA' AS idgrabadora

              FROM 
              [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b,                       
              [speechminer_8_5_512_E1].[dbo].[TextData] c,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e   
                                                                           
              WHERE 
                DATEADD(s,c.originalTime,'19700101') BETWEEN '$varFechaInicioChat 00:00:00' AND '$varFechaFinChat 00:00:00'
                  AND e.name IN ('$varBolsitaCXChat')
                    AND a.categoryid IN ($varBaseRealChat, $txtListaCategoriasChat)
                      AND d.fieldName = '$varTipoExtensionesChat'  
                        AND d.fieldValue IN ('$varExtensionesChat')
                          AND dd.fieldName = 'duracion_bot'
                            AND d1.fieldName = 'nombre_agentes'
                              AND a.categoryId = b.categoryId
                                AND b.callId=c.textId
                                  AND d.callId=c.textId
                                    AND dd.callId=c.textId
                                      AND d1.callId=c.textId
                                        AND e.programId=c.programId
            ")->queryAll();

        }

        if ($varBaseRealChat == "7339") {
          
          $varListaChat = Yii::$app->get('dbSpeechA1')->createCommand("
            SELECT DISTINCT (b.callId), CASE WHEN a.categoryid = 7339 THEN $varCategoriaGeneralChat ELSE a.categoryid END AS CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.originalTime,'19700101') AS Fecha_Llamada, dd.fieldValue AS COLUMN1, e.name AS Servicio, DATEADD(s,c.originalTime,'19700101') AS Fechareal, c.externalTextId AS idredbox, 'NA' AS idgrabadora

              FROM 
              [speechminer_8_5_512_A1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A1].[dbo].[callCategoryTbl] b,                       
              [speechminer_8_5_512_A1].[dbo].[TextData] c,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_A1].[dbo].[programInfoTbl] e   
                                                                           
              WHERE 
                DATEADD(s,c.originalTime,'19700101') BETWEEN '$varFechaInicioChat 00:00:00' AND '$varFechaFinChat 00:00:00'
                  AND e.name IN ('$varBolsitaCXChat')
                    AND a.categoryid IN ($varBaseRealChat, $txtListaCategoriasChat)
                      AND d.fieldName = '$varTipoExtensionesChat'  
                        AND d.fieldValue IN ('$varExtensionesChat')
                          AND dd.fieldName = 'duracion_bot'
                            AND d1.fieldName = 'nombre_agentes'
                              AND a.categoryId = b.categoryId
                                AND b.callId=c.textId
                                  AND d.callId=c.textId
                                    AND dd.callId=c.textId
                                      AND d1.callId=c.textId
                                        AND e.programId=c.programId
            ")->queryAll();

        }

        $varVerificaChat = (new \yii\db\Query())
                                ->select(['callId'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','servicio',$varBolsitaCXChat])
                                ->andwhere(['BETWEEN','fechallamada',$varFechaInicioChat.' 05:00:00',$varFechaFinChat.' 05:00:00'])
                                ->andwhere(['IN','extension',$varExtensionesChat])
                                ->andwhere(['=','anulado',0])
                                ->count();

        if ($varVerificaChat == 0) {
          
          foreach ($varListaChat as $key => $value) {
            
            Yii::$app->db->createCommand()->insert('tbl_dashboardspeechcalls',[
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
                                                           'idgrabadora' => $value['idgrabadora'],
                                                           'connid' => null,
                                                           'extensiones' => null,
                                                           'fechacreacion' => date('Y-m-d'),
                                                           'anulado' => 0,
                                                        ])->execute();

          }

          $varListaCategorizacionChat = Yii::$app->db->createCommand("
            SELECT * FROM 
              (
                SELECT llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria AS llamacategoria, cate.idcategoria AS catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) AS encuentra, llama.nombreCategoria 
                FROM tbl_dashboardspeechcalls llama 
                  LEFT JOIN 
                    (
                      SELECT idcategoria, tipoindicador, programacategoria, cod_pcrc 
                        FROM tbl_speech_categorias 
                          WHERE anulado = 0 AND idcategorias = 2 
                            AND programacategoria IN ('$varBolsitaCXChat') 
                        ORDER BY cod_pcrc, tipoindicador
                    ) cate ON llama.servicio = cate.programacategoria 
                WHERE llama.servicio IN ('$varBolsitaCXChat') 
                  AND llama.extension IN ('$varExtensionesChat') 
                    AND llama.fechallamada BETWEEN '$varFechaInicioChat 05:00:00' AND '$varFechaFinChat  05:00:00' 
                GROUP BY llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  
                  ORDER BY encuentra DESC
              ) datos 
            WHERE llamacategoria = catecategoria")->queryAll();

          if (count($varListaCategorizacionChat) != 0) {
            
            foreach ($varListaCategorizacionChat as $key => $value) {
              Yii::$app->db->createCommand()->insert('tbl_speech_general',[
                                                           'programacliente' => $value['servicio'],
                                                           'fechainicio' => date('Y-m-01'),
                                                           'fechafin' => NULL,
                                                           'callid' => $value['callid'],
                                                           'fechallamada' => $value['fechallamada'],
                                                           'extension' => $value['extension'],
                                                           'idindicador' => $value['llamacategoria'],
                                                           'idvariable' => $value['catecategoria'],
                                                           'cantproceso' => $value['encuentra'],
                                                           'fechacreacion' => date('Y-m-d'),
                                                           'anulado' => 0,
                                                           'usua_id' => Yii::$app->user->identity->id,
                                                           'arbol_id' => $varIdClienteChat,
                                                        ])->execute();
            }

          }

        }


      }
    }

    public function Actualizallamadasspeech($varConcatenarLlamadas){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteLlamada = null;
      $varFechaInicioLlamada = null;
      $varFechaFinLlamada = null;
      $varConnidLlamada = null;

      $varListaItemsLlamadas = explode("; ", $varConcatenarLlamadas);
      for ($i=0; $i < count($varListaItemsLlamadas); $i++) { 
        $varIdClienteLlamada = $varListaItemsLlamadas[0];
        $varFechaInicioLlamada = $varListaItemsLlamadas[1];
        $varFechaFinLlamada = $varListaItemsLlamadas[2];
        $varConnidLlamada = $varListaItemsLlamadas[3];
      }

      $varCategoriaGeneral = (new \yii\db\Query())
                              ->select(['idllamada'])
                              ->from(['tbl_speech_servicios'])            
                              ->where(['=','id_dp_clientes',$varIdClienteLlamada])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['idllamada'])
                              ->Scalar();

      $varBaseReal = (new \yii\db\Query())
                              ->select(['base_real'])
                              ->from(['tbl_speech_servicios'])            
                              ->where(['=','id_dp_clientes',$varIdClienteLlamada])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['base_real'])
                              ->Scalar();

      $varListaPcrc = (new \yii\db\Query())
                    ->select(['cod_pcrc'])
                    ->from(['tbl_speech_parametrizar'])            
                    ->where(['=','id_dp_clientes',$varIdClienteLlamada])
                    ->andwhere(['=','anulado',0])
                    ->andwhere(['IS','tipospeech',NULL])
                    ->andwhere(['=','usabilidad',1])
                    ->groupby(['cod_pcrc'])
                    ->all();

      foreach ($varListaPcrc as $key => $value) {
        $varCodpcrc = $value['cod_pcrc'];

        $varReglaNegocioLlamadas =  (new \yii\db\Query())
                                  ->select(['rn'])
                                  ->from(['tbl_speech_parametrizar'])            
                                  ->where(['=','cod_pcrc',$varCodpcrc])
                                  ->andwhere(['=','anulado',0])
                                  ->groupby(['rn'])
                                  ->all();

        if (count($varReglaNegocioLlamadas) != 0) {

          $varArrayRnLlamada = array();
          foreach ($varReglaNegocioLlamadas as $key => $value) {
            array_push($varArrayRnLlamada, $value['rn']);
          }

          $varExtensionesLlamadas = implode("', '", $varArrayRnLlamada); 
          $varExtensionSpeech = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesLlamadas));
          $varTipoExtensiones = "regla_negocio";              

        }else{

          $varExtLlamadas =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrc])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['ext'])
                                ->all();

          if (count($varExtLlamadas) != 0) {
            
            $varArrayExtLlamadas = array();
            foreach ($varExtLlamadas as $key => $value) {
              array_push($varArrayExtLlamadas, $value['ext']);
            }
                
            $varExtensionesLlamadas = implode("', '", $varArrayExtLlamadas);
            $varExtensionSpeech = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesLlamadas)); 
            $varTipoExtensiones = "extension";           

          }else{

            $varUsuaLlamadas =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrc])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['usuared'])
                                ->all();

            if (count($varUsuaLlamadas) != 0) {
              
              $varArrayUsuaLlamadas = array();
              foreach ($varUsuaLlamadas as $key => $value) {
                array_push($varArrayUsuaLlamadas, $value['usuared']);
              }

              $varExtensionesLlamadas = implode("', '", $varArrayUsuaLlamadas);
              $varExtensionSpeech = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesLlamadas));
              $varTipoExtensiones = "login_id";
              
            }else{
              $varExtensionesLlamadas = "NA";
            }

          }

        }

        $varListaCategorias = (new \yii\db\Query())
                                ->select(['idcategoria'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','cod_pcrc',$varCodpcrc])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['idcategoria'])
                                ->all();

        $arraylistcategorias = array();
        foreach ($varListaCategorias as $key => $value) {
          array_push($arraylistcategorias, $value['idcategoria']);
        }
        $txtListaCategorias = implode(", ", $arraylistcategorias);


        $varBolsitaCX = (new \yii\db\Query())
                                ->select(['programacategoria'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','cod_pcrc',$varCodpcrc])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['programacategoria'])
                                ->Scalar();        

        // Se procede a buscar las llamadas BD A2 de Speech
        if ($varBaseReal == "1114") {
          
          if ($varConnidLlamada == 1) {

            $varListaLlamadas = Yii::$app->get('dbSpeechA2')->createCommand("
            SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, dn.fieldValue AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_A2].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dn,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamada 05:00:00' AND '$varFechaFinLlamada 05:00:00' 
              
              AND e.name = '$varBolsitaCX'
                AND a.categoryId IN ($varBaseReal, $txtListaCategorias)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensiones'
                      AND d.fieldValue IN ('$varExtensionesLlamadas')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND dn.fieldName = 'connid'
                                AND de.fieldName = 'extension'
                                  AND a.categoryId = b.categoryId 
                                    AND b.callId = c.callId 
                                      AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND dr.callId = c.callId
                                              AND dc.callId = c.callId
                                                AND dn.callId = c.callId
                                                  AND de.callId = c.callId
                                                    AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC")->queryAll();

          }else{

            $varListaLlamadas = Yii::$app->get('dbSpeechA2')->createCommand("
            SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, 'NA' AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_A2].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamada 05:00:00' AND '$varFechaFinLlamada 05:00:00' 
              
              AND e.name = '$varBolsitaCX'
                AND a.categoryId IN ($varBaseReal, $txtListaCategorias)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensiones'
                      AND d.fieldValue IN ('$varExtensionesLlamadas')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND de.fieldName = 'extension'
                                AND a.categoryId = b.categoryId 
                                  AND b.callId = c.callId 
                                    AND d.callId = c.callId 
                                      AND dd.callId = c.callId
                                        AND d1.callId = c.callId
                                          AND dr.callId = c.callId
                                            AND dc.callId = c.callId
                                              AND de.callId = c.callId
                                                AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC")->queryAll();

          }

        }

        // Se procede a buscar las llamadas BD E1 de Speech
        if ($varBaseReal == "1105") {

          if ($varConnidLlamada == 1) {

            $varListaLlamadas = Yii::$app->get('dbSpeechE1')->createCommand("
            SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, dn.fieldValue AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dn,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamada 05:00:00' AND '$varFechaFinLlamada 05:00:00' 
              
              AND e.name = '$varBolsitaCX'
                AND a.categoryId IN ($varBaseReal, $txtListaCategorias)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensiones'
                      AND d.fieldValue IN ('$varExtensionesLlamadas')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND dn.fieldName = 'connid'
                                AND de.fieldName = 'extension'
                                  AND a.categoryId = b.categoryId 
                                    AND b.callId = c.callId 
                                      AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND dr.callId = c.callId
                                              AND dc.callId = c.callId
                                                AND dn.callId = c.callId
                                                  AND de.callId = c.callId
                                                    AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC")->queryAll();

          }else{

            $varListaLlamadas = Yii::$app->get('dbSpeechE1')->createCommand("
            SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, 'NA' AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamada 05:00:00' AND '$varFechaFinLlamada 05:00:00' 
              
              AND e.name = '$varBolsitaCX'
                AND a.categoryId IN ($varCategoriaGeneral, $txtListaCategorias)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensiones'
                      AND d.fieldValue IN ('$varExtensionesLlamadas')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND de.fieldName = 'extension'
                                AND a.categoryId = b.categoryId 
                                  AND b.callId = c.callId 
                                    AND d.callId = c.callId 
                                      AND dd.callId = c.callId
                                        AND d1.callId = c.callId
                                          AND dr.callId = c.callId
                                            AND dc.callId = c.callId
                                              AND de.callId = c.callId
                                                AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC")->queryAll();

          }

        }

        // Se procede a buscar las llamadas BD A1 de Speech
        if ($varBaseReal == "7339") {

          if ($varConnidLlamada == 1) {
            
            $varListaLlamadas = Yii::$app->get('dbSpeechA1')->createCommand("
            SELECT distinct (b.callId), CASE WHEN a.categoryid = 7339 THEN $varCategoriaGeneral ELSE a.categoryid END as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, dn.fieldValue AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_A1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A1].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_A1].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dn,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_A1].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamada 05:00:00' AND '$varFechaFinLlamada 05:00:00' 
              
              AND e.name = '$varBolsitaCX'
                AND a.categoryId IN ($varBaseReal, $txtListaCategorias)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensiones'
                      AND d.fieldValue IN ('$varExtensionesLlamadas')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND dn.fieldName = 'connid'
                                AND de.fieldName = 'extension'
                                  AND a.categoryId = b.categoryId 
                                    AND b.callId = c.callId 
                                      AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND dr.callId = c.callId
                                              AND dc.callId = c.callId
                                                AND dn.callId = c.callId
                                                  AND de.callId = c.callId
                                                    AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC")->queryAll();

          }else{

            $varListaLlamadas = Yii::$app->get('dbSpeechA1')->createCommand("
            SELECT distinct (b.callId), CASE WHEN a.categoryid = 7339 THEN $varCategoriaGeneral ELSE a.categoryid END as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, 'NA' AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_A1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A1].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_A1].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_A1].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamada 05:00:00' AND '$varFechaFinLlamada 05:00:00' 
              
              AND e.name = '$varBolsitaCX'
                AND a.categoryId IN ($varBaseReal, $txtListaCategorias)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensiones'
                      AND d.fieldValue IN ('$varExtensionesLlamadas')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND de.fieldName = 'extension'
                                AND a.categoryId = b.categoryId 
                                  AND b.callId = c.callId 
                                    AND d.callId = c.callId 
                                      AND dd.callId = c.callId
                                        AND d1.callId = c.callId
                                          AND dr.callId = c.callId
                                            AND dc.callId = c.callId
                                              AND de.callId = c.callId
                                                AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC")->queryAll();

          }
        }

        $varVerificaLlamadas = (new \yii\db\Query())
                                ->select(['callId'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','servicio',$varBolsitaCX])
                                ->andwhere(['BETWEEN','fechallamada',$varFechaInicioLlamada.' 05:00:00',$varFechaFinLlamada.' 05:00:00'])
                                ->andwhere(['IN','extension',$varExtensionSpeech])
                                ->andwhere(['=','anulado',0])
                                ->count();

        if ($varVerificaLlamadas == 0) {
          
          foreach ($varListaLlamadas as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_dashboardspeechcalls',[
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
                                                           'idgrabadora' => $value['idgrabadora'],
                                                           'connid' => $value['connid'],
                                                           'extensiones' => $value['extensiones'],
                                                           'fechacreacion' => date('Y-m-d'),
                                                           'anulado' => 0,
                                                        ])->execute();
          }

          $varListaCategorizacion = Yii::$app->db->createCommand("
            SELECT * FROM 
              (
                SELECT llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria AS llamacategoria, cate.idcategoria AS catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) AS encuentra, llama.nombreCategoria 
                FROM tbl_dashboardspeechcalls llama 
                  LEFT JOIN 
                    (
                      SELECT idcategoria, tipoindicador, programacategoria, cod_pcrc 
                        FROM tbl_speech_categorias 
                          WHERE anulado = 0 AND idcategorias = 2 
                            AND programacategoria IN ('$varBolsitaCX') 
                        ORDER BY cod_pcrc, tipoindicador
                    ) cate ON llama.servicio = cate.programacategoria 
                WHERE llama.servicio IN ('$varBolsitaCX') 
                  AND llama.extension IN ('$varExtensionesLlamadas') 
                    AND llama.fechallamada BETWEEN '$varFechaInicioLlamada 05:00:00' AND '$varFechaFinLlamada 05:00:00' 
                GROUP BY llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  
                  ORDER BY encuentra DESC
              ) datos 
            WHERE llamacategoria = catecategoria")->queryAll();

          if (count($varListaCategorizacion) != 0) {
            
            foreach ($varListaCategorizacion as $key => $value) {
              Yii::$app->db->createCommand()->insert('tbl_speech_general',[
                                                           'programacliente' => $value['servicio'],
                                                           'fechainicio' => date('Y-m-01'),
                                                           'fechafin' => NULL,
                                                           'callid' => $value['callid'],
                                                           'fechallamada' => $value['fechallamada'],
                                                           'extension' => $value['extension'],
                                                           'idindicador' => $value['llamacategoria'],
                                                           'idvariable' => $value['catecategoria'],
                                                           'cantproceso' => $value['encuentra'],
                                                           'fechacreacion' => date('Y-m-d'),
                                                           'anulado' => 0,
                                                           'usua_id' => Yii::$app->user->identity->id,
                                                           'arbol_id' => $varIdClienteLlamada,
                                                        ])->execute();
            }

          }

        }
      }

    }

    public function actionListarpcrcespecial(){            
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
            $varNombres = (new \yii\db\Query())
                                ->select(['programacategoria'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','cod_pcrc',$value->cod_pcrc])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['programacategoria'])
                                ->scalar();
            
            echo "<input type='checkbox' id= '".$nombre."' value='".$value->cod_pcrc."' class='".$clase."'>";
            echo "<label  style='font-size: 12px;' for = '".$value->cod_pcrc."'>&nbsp;&nbsp; ".$value->cod_pcrc." - ".$value->pcrc." -- ".$varNombres . "</label> <br>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }

    }

    public function actionActualizaspeechespecial(){
      $model = new SpeechCategorias();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varBaseDatos = $model->orientacionsmart;
        $varTipoDato = $model->anulado;
        $varExtension = $model->nombre;
        $varTipoConnid = $model->componentes;
        $varFechaEspecial = explode(" ", $model->fechacreacion);

        $varFechaInicioEspecial = $varFechaEspecial[0];
        $varFechaFinEspecial = date('Y-m-d',strtotime($varFechaEspecial[2]."+ 1 days"));

        $varClienteEspecial = $model->tipoparametro;
        $varListaPcrcEspecial = $model->cod_pcrc;

        if ($varTipoDato == "1") {
          $varConcatenarLlamadasEspecial = $varClienteEspecial.'; '.$varListaPcrcEspecial.'; '.$varBaseDatos.'; '.$varExtension.'; '.$varTipoConnid.'; '.$varFechaInicioEspecial.'; '.$varFechaFinEspecial;

          $this->Actualizallamadasspeechespecial($varConcatenarLlamadasEspecial);

        }else{
          $varConcatenarChatEspecial = $varClienteEspecial.'; '.$varListaPcrcEspecial.'; '.$varBaseDatos.'; '.$varExtension.'; '.$varTipoConnid.'; '.$varFechaInicioEspecial.'; '.$varFechaFinEspecial;

          $this->Actualizachatspeechespecial($varConcatenarChatEspecial);

        }

        return $this->redirect('actualizarllamadas');
      }

      return $this->renderAjax('actualizaspeechespecial',[
        'model' => $model,
      ]);
    }

    public function Actualizallamadasspeechespecial($varConcatenarLlamadasEspecial){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteLlamadaEspecial = null;
      $varListaPcrcLlamadaEspecial = null;
      $varIdCategoriaLlamadaEspecial = null;
      $varExtensionEspecial = null;
      $varFechaInicioLlamadaEspecial = null;
      $varFechaFinLlamadaEspecial = null;
      $varConnidLlamadaEspecial = null;
      $varBaseRealLlamadaEspecial = null;
      $varTipoExtensionesLlamadaEspecial = null;

      $varListaItemsLlamadaEspecial = explode("; ", $varConcatenarLlamadasEspecial);
      for ($i=0; $i < count($varListaItemsLlamadaEspecial); $i++) { 
        $varIdClienteLlamadaEspecial = $varListaItemsLlamadaEspecial[0];
        $varListaPcrcLlamadaEspecial = $varListaItemsLlamadaEspecial[1];

        $varBaseRealLlamadaEspecial = $varListaItemsLlamadaEspecial[2];
        $varTipoExtensionesLlamadaEspecial = $varListaItemsLlamadaEspecial[3];

        $varConnidLlamadaEspecial = $varListaItemsLlamadaEspecial[4];
        $varFechaInicioLlamadaEspecial = $varListaItemsLlamadaEspecial[5];
        $varFechaFinLlamadaEspecial = $varListaItemsLlamadaEspecial[6];
        
      }

      $varCategoriaGeneralLlamadaEspecial = (new \yii\db\Query())
                                        ->select(['idllamada'])
                                        ->from(['tbl_speech_servicios'])            
                                        ->where(['=','id_dp_clientes',$varIdClienteLlamadaEspecial])
                                        ->andwhere(['=','anulado',0])
                                        ->groupby(['idllamada'])
                                        ->Scalar();

      $arrayListPcrcLlamadaEspeciales = explode(",", str_replace(array("#", "'", ";", " "), '', $varListaPcrcLlamadaEspecial));
      $varListasPcrcLlamadaEspecial = (new \yii\db\Query())
                                ->select(['cod_pcrc'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','id_dp_clientes',$varIdClienteLlamadaEspecial])
                                ->andwhere(['IN','cod_pcrc',$arrayListPcrcLlamadaEspeciales])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['IS','tipospeech',NULL])
                                ->andwhere(['=','usabilidad',1])
                                ->groupby(['cod_pcrc'])
                                ->all();

      foreach ($varListasPcrcLlamadaEspecial as $key => $value) {
        $varCodpcrcLlamadaEspecial = $value['cod_pcrc'];

        $varReglaNegocioLlamadaEspecial =  (new \yii\db\Query())
                                    ->select(['rn'])
                                    ->from(['tbl_speech_parametrizar'])            
                                    ->where(['=','cod_pcrc',$varCodpcrcLlamadaEspecial])
                                    ->andwhere(['=','anulado',0])
                                    ->andwhere(['!=','rn',""])
                                    ->groupby(['rn'])
                                    ->all();

        if (count($varReglaNegocioLlamadaEspecial) != 0) {

          $varArrayRnLlamadaEspecial = array();
          foreach ($varReglaNegocioLlamadaEspecial as $key => $value) {
            array_push($varArrayRnLlamadaEspecial, $value['rn']);
          }

          $varExtensionesLlamadaEspecial = implode("', '", $varArrayRnLlamadaEspecial); 
          $varExtensionSpeechLlamadaEspecial = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesLlamadaEspecial));

        }else{

          $varExtLlamadaEspecial =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcLlamadaEspecial])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['!=','ext',""])
                                ->groupby(['ext'])
                                ->all();

          if (count($varExtLlamadaEspecial) != 0) {
            
            $varArrayExtLlamadaEspecial = array();
            foreach ($varExtLlamadaEspecial as $key => $value) {
              array_push($varArrayExtLlamadaEspecial, $value['ext']);
            }

            $varExtensionesLlamadaEspecial = implode("', '", $varArrayExtLlamadaEspecial);
            $varExtensionSpeechLlamadaEspecial = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesLlamadaEspecial));

          }else{

            $varUsuaLlamadaEspecial =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcLlamadaEspecial])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['!=','usuared',""])
                                ->groupby(['usuared'])
                                ->all();

            if (count($varUsuaLlamadaEspecial) != 0) {
              
              $varArrayUsuaLlamadaEspecial = array();
              foreach ($varUsuaLlamadaEspecial as $key => $value) {
                array_push($varArrayUsuaLlamadaEspecial, $value['usuared']);
              }

              $varExtensionesLlamadaEspecial = implode("', '", $varArrayUsuaLlamadaEspecial);
              $varExtensionSpeechLlamadaEspecial = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesLlamadaEspecial));

            }else{
              $varExtensionesLlamadaEspecial = "NA";
            }
          }
        }

        $varListaCategoriasLlamadaEspecial = (new \yii\db\Query())
                                        ->select(['idcategoria'])
                                        ->from(['tbl_speech_categorias'])            
                                        ->where(['=','cod_pcrc',$varCodpcrcLlamadaEspecial])
                                        ->andwhere(['=','anulado',0])
                                        ->groupby(['idcategoria'])
                                        ->all();

        $arraylistcategoriasLlamadaEspecial = array();
        foreach ($varListaCategoriasLlamadaEspecial as $key => $value) {
          array_push($arraylistcategoriasLlamadaEspecial, $value['idcategoria']);
        }
        $txtListaCategoriasLlamadaEspecial = implode(", ", $arraylistcategoriasLlamadaEspecial);

        $varBolsitaCXLlamadaEspecial = (new \yii\db\Query())
                                  ->select(['programacategoria'])
                                  ->from(['tbl_speech_categorias'])            
                                  ->where(['=','cod_pcrc',$varCodpcrcLlamadaEspecial])
                                  ->andwhere(['=','anulado',0])
                                  ->groupby(['programacategoria'])
                                  ->Scalar();

        if ($varBaseRealLlamadaEspecial == "1114") {
          
          if ($varConnidLlamadaEspecial == "1") {

            $varListaLlamadaEspeciales = Yii::$app->get('dbSpeechA2')->createCommand("
            SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, dn.fieldValue AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_A2].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dn,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamadaEspecial 05:00:00' AND '$varFechaFinLlamadaEspecial 05:00:00' 
              
              AND e.name = '$varBolsitaCXLlamadaEspecial'
                AND a.categoryId IN ($varBaseRealLlamadaEspecial, $txtListaCategoriasLlamadaEspecial)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensionesLlamadaEspecial'
                      AND d.fieldValue IN ('$varExtensionesLlamadaEspecial')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND dn.fieldName = 'connid'
                                AND de.fieldName = 'extension'
                                  AND a.categoryId = b.categoryId 
                                    AND b.callId = c.callId 
                                      AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND dr.callId = c.callId
                                              AND dc.callId = c.callId
                                                AND dn.callId = c.callId
                                                  AND de.callId = c.callId
                                                    AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC
            ")->queryAll();

          }else{

            $varListaLlamadaEspeciales = Yii::$app->get('dbSpeechA2')->createCommand("
            SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, 'NA' AS idredbox, 'NA' AS idgrabadora, 'NA' AS connid, 'NA' AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_A2].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamadaEspecial 05:00:00' AND '$varFechaFinLlamadaEspecial 05:00:00' 
              
                AND e.name = '$varBolsitaCXLlamadaEspecial'
                  AND a.categoryId IN ($varBaseRealLlamadaEspecial, $txtListaCategoriasLlamadaEspecial)
                    AND dd.fieldName = 'rbstarttime'
                      AND d.fieldName = '$varTipoExtensionesLlamadaEspecial'
                          AND d.fieldValue IN ('$varExtensionesLlamadaEspecial')
                            AND d1.fieldName = 'login_id'
                            AND a.categoryId = b.categoryId 
                                  AND b.callId = c.callId 
                                    AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC
            ")->queryAll();

          }

        }

        if ($varBaseRealLlamadaEspecial == "1105") {
          
          if ($varConnidLlamadaEspecial == "1") {

            $varListaLlamadaEspeciales = Yii::$app->get('dbSpeechE1')->createCommand("
            SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, dn.fieldValue AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dn,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamadaEspecial 05:00:00' AND '$varFechaFinLlamadaEspecial 05:00:00' 
              
              AND e.name = '$varBolsitaCXLlamadaEspecial'
                AND a.categoryId IN ($varBaseRealLlamadaEspecial, $txtListaCategoriasLlamadaEspecial)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensionesLlamadaEspecial'
                      AND d.fieldValue IN ('$varExtensionesLlamadaEspecial')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND dn.fieldName = 'connid'
                                AND de.fieldName = 'extension'
                                  AND a.categoryId = b.categoryId 
                                    AND b.callId = c.callId 
                                      AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND dr.callId = c.callId
                                              AND dc.callId = c.callId
                                                AND dn.callId = c.callId
                                                  AND de.callId = c.callId
                                                    AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC
            ")->queryAll();

          }else{

            $varListaLlamadaEspeciales = Yii::$app->get('dbSpeechE1')->createCommand("
            SELECT distinct (b.callId), a.categoryId as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, 'NA' AS idredbox, 'NA' AS idgrabadora, 'NA' AS connid, 'NA' AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_E1].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamadaEspecial 05:00:00' AND '$varFechaFinLlamadaEspecial 05:00:00' 
              
                AND e.name = '$varBolsitaCXLlamadaEspecial'
                  AND a.categoryId IN ($varBaseRealLlamadaEspecial, $txtListaCategoriasLlamadaEspecial)
                    AND dd.fieldName = 'rbstarttime'
                      AND d.fieldName = '$varTipoExtensionesLlamadaEspecial'
                          AND d.fieldValue IN ('$varExtensionesLlamadaEspecial')
                            AND d1.fieldName = 'login_id'
                            AND a.categoryId = b.categoryId 
                                  AND b.callId = c.callId 
                                    AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC
            ")->queryAll();

          }

        }

        if ($varBaseRealLlamadaEspecial == "7339") {
          
          if ($varConnidLlamadaEspecial == "1") {

            $varListaLlamadaEspeciales = Yii::$app->get('dbSpeechA1')->createCommand("
            SELECT distinct (b.callId), CASE WHEN a.categoryid = 7339 THEN $varCategoriaGeneralLlamadaEspecial ELSE a.categoryid END as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, dr.fieldValue AS idredbox, dc.fieldValue AS idgrabadora, dn.fieldValue AS connid, de.fieldValue AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_A1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A1].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_A1].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d1, 
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dc,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dn,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] de,
              [speechminer_8_5_512_A1].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamadaEspecial 05:00:00' AND '$varFechaFinLlamadaEspecial 05:00:00' 
              
              AND e.name = '$varBolsitaCXLlamadaEspecial'
                AND a.categoryId IN ($varBaseRealLlamadaEspecial, $txtListaCategoriasLlamadaEspecial)
                  AND dd.fieldName = 'rbstarttime'
                    AND d.fieldName = '$varTipoExtensionesLlamadaEspecial'
                      AND d.fieldValue IN ('$varExtensionesLlamadaEspecial')
                        AND d1.fieldName = 'login_id'
                          AND dr.fieldName = 'idredbox'
                            AND dc.fieldName = 'idgrabadora'
                              AND dn.fieldName = 'connid'
                                AND de.fieldName = 'extension'
                                  AND a.categoryId = b.categoryId 
                                    AND b.callId = c.callId 
                                      AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND dr.callId = c.callId
                                              AND dc.callId = c.callId
                                                AND dn.callId = c.callId
                                                  AND de.callId = c.callId
                                                    AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC
            ")->queryAll();

          }else{

            $varListaLlamadaEspeciales = Yii::$app->get('dbSpeechA1')->createCommand("
            SELECT distinct (b.callId), CASE WHEN a.categoryid = 7339 THEN $varCategoriaGeneralLlamadaEspecial ELSE a.categoryid END as CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.callTime,'19700101') AS Fecha_Llamada, round(c.callduration,0) AS cantidadllamadas, e.name as Servicio, dd.fieldValue as Fechareal, 'NA' AS idredbox, 'NA' AS idgrabadora, 'NA' AS connid, 'NA' AS extensiones       

            FROM                                                                                 
              [speechminer_8_5_512_A1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A1].[dbo].[callCategoryTbl] b,
              [speechminer_8_5_512_A1].[dbo].[callMetaTbl] c,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_A1].[dbo].[programInfoTbl] e  

            WHERE
              DATEADD(s,c.callTime,'19700101') BETWEEN '$varFechaInicioLlamadaEspecial 05:00:00' AND '$varFechaFinLlamadaEspecial 05:00:00' 
              
                AND e.name = '$varBolsitaCXLlamadaEspecial'
                  AND a.categoryId IN ($varBaseRealLlamadaEspecial, $txtListaCategoriasLlamadaEspecial)
                    AND dd.fieldName = 'rbstarttime'
                      AND d.fieldName = '$varTipoExtensionesLlamadaEspecial'
                          AND d.fieldValue IN ('$varExtensionesLlamadaEspecial')
                            AND d1.fieldName = 'login_id'
                            AND a.categoryId = b.categoryId 
                                  AND b.callId = c.callId 
                                    AND d.callId = c.callId 
                                        AND dd.callId = c.callId
                                          AND d1.callId = c.callId
                                            AND e.programId = c.programId 
            ORDER BY Fecha_Llamada DESC
            ")->queryAll();

          }

        }

        $varVerificaLlamadaEspecial = (new \yii\db\Query())
                                  ->select(['callId'])
                                  ->from(['tbl_dashboardspeechcalls'])            
                                  ->where(['=','servicio',$varBolsitaCXLlamadaEspecial])
                                  ->andwhere(['BETWEEN','fechallamada',$varFechaInicioLlamadaEspecial.' 05:00:00',$varFechaFinLlamadaEspecial.' 05:00:00'])
                                  ->andwhere(['IN','extension',$varExtensionSpeechLlamadaEspecial])
                                  ->andwhere(['=','anulado',0])
                                  ->count();

        if (count($varVerificaLlamadaEspecial) != 0) {
          
          foreach ($varListaLlamadaEspeciales as $key => $value) {
            
            Yii::$app->db->createCommand()->insert('tbl_dashboardspeechcalls',[
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
                                                           'idgrabadora' => $value['idgrabadora'],
                                                           'connid' => $value['connid'],
                                                           'extensiones' => $value['extensiones'],
                                                           'fechacreacion' => date('Y-m-d'),
                                                           'anulado' => 0,
                                                        ])->execute();

          }

          $varListaCategorizacionLlamadaEspeciales = Yii::$app->db->createCommand("
            SELECT * FROM 
              (
                SELECT llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria AS llamacategoria, cate.idcategoria AS catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) AS encuentra, llama.nombreCategoria 
                FROM tbl_dashboardspeechcalls llama 
                  LEFT JOIN 
                    (
                      SELECT idcategoria, tipoindicador, programacategoria, cod_pcrc 
                        FROM tbl_speech_categorias 
                          WHERE anulado = 0 AND idcategorias = 2 
                            AND programacategoria IN ('$varBolsitaCXLlamadaEspecial') 
                        ORDER BY cod_pcrc, tipoindicador
                    ) cate ON llama.servicio = cate.programacategoria 
                WHERE llama.servicio IN ('$varBolsitaCXLlamadaEspecial') 
                  AND llama.extension IN ('$varExtensionesLlamadaEspecial') 
                    AND llama.fechallamada BETWEEN '$varFechaInicioLlamadaEspecial 05:00:00' AND '$varFechaFinLlamadaEspecial 05:00:00' 
                GROUP BY llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  
                  ORDER BY encuentra DESC
              ) datos 
            WHERE llamacategoria = catecategoria")->queryAll();


          if (count($varListaCategorizacionLlamadaEspeciales) != 0) {
          
            foreach ($varListaCategorizacionLlamadaEspeciales as $key => $value) {
              Yii::$app->db->createCommand()->insert('tbl_speech_general',[
                                                             'programacliente' => $value['servicio'],
                                                             'fechainicio' => date('Y-m-01'),
                                                             'fechafin' => NULL,
                                                             'callid' => $value['callid'],
                                                             'fechallamada' => $value['fechallamada'],
                                                             'extension' => $value['extension'],
                                                             'idindicador' => $value['llamacategoria'],
                                                             'idvariable' => $value['catecategoria'],
                                                             'cantproceso' => $value['encuentra'],
                                                             'fechacreacion' => date('Y-m-d'),
                                                             'anulado' => 0,
                                                             'usua_id' => Yii::$app->user->identity->id,
                                                             'arbol_id' => $varIdClienteLlamadaEspecial,
                                                          ])->execute();
            }

          }
        }

      }
    }

    public function Actualizachatspeechespecial($varConcatenarChatEspecial){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteChatEspecial = null;
      $varListaPcrcChatEspecial = null;
      $varIdCategoriaChatEspecial = null;
      $varExtensionChatEspecial = null;
      $varFechaInicioChatEspecial = null;
      $varFechaFinChatEspecial = null;
      $varConnidChatEspecial = null;
      $varBaseRealChatEspecial = null;
      $varTipoExtensionesChatEspecial = null;


      $varListaItemsChatEspecial = explode("; ", $varConcatenarChatEspecial);
      for ($i=0; $i < count($varListaItemsChatEspecial); $i++) { 
        $varIdClienteChatEspecial = $varListaItemsChatEspecial[0];
        $varListaPcrcChatEspecial = $varListaItemsChatEspecial[1];

        $varBaseRealChatEspecial = $varListaItemsChatEspecial[2];
        $varTipoExtensionesChatEspecial = $varListaItemsChatEspecial[3];

        $varConnidChatEspecial = $varListaItemsChatEspecial[4];
        $varFechaInicioChatEspecial = $varListaItemsChatEspecial[5];
        $varFechaFinChatEspecial = $varListaItemsChatEspecial[6];
        
      }

      $varCategoriaGeneralChatEspecial = (new \yii\db\Query())
                                        ->select(['idllamada'])
                                        ->from(['tbl_speech_servicios'])            
                                        ->where(['=','id_dp_clientes',$varIdClienteChatEspecial])
                                        ->andwhere(['=','anulado',0])
                                        ->groupby(['idllamada'])
                                        ->Scalar();


      $arrayListPcrcChatEspeciales = explode(",", str_replace(array("#", "'", ";", " "), '', $varListaPcrcChatEspecial));
      $varListaPcrcChatEspcial = (new \yii\db\Query())
                                ->select(['cod_pcrc'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','id_dp_clientes',$varIdClienteChatEspecial])
                                ->andwhere(['IN','cod_pcrc',$arrayListPcrcChatEspeciales])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','tipospeech',1])
                                ->andwhere(['=','usabilidad',1])
                                ->groupby(['cod_pcrc'])
                                ->all();

      foreach ($varListaPcrcChatEspcial as $key => $value) {        
        $varCodpcrcChatEspecial = $value['cod_pcrc'];

        $varReglaNegocioChatEspecial =  (new \yii\db\Query())
                                    ->select(['rn'])
                                    ->from(['tbl_speech_parametrizar'])            
                                    ->where(['=','cod_pcrc',$varCodpcrcChatEspecial])
                                    ->andwhere(['=','anulado',0])
                                    ->andwhere(['!=','rn',""])
                                    ->groupby(['rn'])
                                    ->all();

        if (count($varReglaNegocioChatEspecial) != 0) {
          
          $varArrayRnChatEspecial = array();
          foreach ($varReglaNegocioChatEspecial as $key => $value) {
            array_push($varArrayRnChatEspecial, $value['rn']);
          }

          $varExtensionesChatEspecial = implode("', '", $varArrayRnChatEspecial); 
          $varExtensionSpeechChatEspecial = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesChatEspecial));

        }else{

          $varExtChatEspecial =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcChatEspecial])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['!=','ext',""])
                                ->groupby(['ext'])
                                ->all();

          if (count($varExtChatEspecial) != 0) {

            $varArrayExtChatEspecial = array();
            foreach ($varExtChatEspecial as $key => $value) {
              array_push($varArrayExtChatEspecial, $value['ext']);
            }

            $varExtensionesChatEspecial = implode("', '", $varArrayExtChatEspecial);
            $varExtensionSpeechChatEspecial = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesChatEspecial));

          }else{

            $varUsuaChatEspecial =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcChatEspecial])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['!=','usuared',""])
                                ->groupby(['usuared'])
                                ->all();

            if (count($varUsuaChatEspecial) != 0) {              

              $varArrayUsuaChatEspecial = array();
              foreach ($varUsuaChatEspecial as $key => $value) {
                array_push($varArrayUsuaChatEspecial, $value['usuared']);
              }

              $varExtensionesChatEspecial = implode("', '", $varArrayUsuaChatEspecial);
              $varExtensionSpeechChatEspecial = explode(",", str_replace(array("#", "'", ";", " "), '', $varExtensionesChatEspecial));

            }else{
              $varExtensionesChatEspecial = "NA";
            }
          }
        }


        $varListaCategoriasChatEspecial = (new \yii\db\Query())
                                        ->select(['idcategoria'])
                                        ->from(['tbl_speech_categorias'])            
                                        ->where(['=','cod_pcrc',$varCodpcrcChatEspecial])
                                        ->andwhere(['=','anulado',0])
                                        ->groupby(['idcategoria'])
                                        ->all();

        $arraylistcategoriasChatEspecial = array();
        foreach ($varListaCategoriasChatEspecial as $key => $value) {
          array_push($arraylistcategoriasChatEspecial, $value['idcategoria']);
        }
        $txtListaCategoriasChatEspecial = implode(", ", $arraylistcategoriasChatEspecial);

        $varBolsitaCXChatEspecial = (new \yii\db\Query())
                                  ->select(['programacategoria'])
                                  ->from(['tbl_speech_categorias'])            
                                  ->where(['=','cod_pcrc',$varCodpcrcChatEspecial])
                                  ->andwhere(['=','anulado',0])
                                  ->groupby(['programacategoria'])
                                  ->Scalar();

        if ($varBaseRealChatEspecial == "1114") {

          $varListaChatEspeciales = Yii::$app->get('dbSpeechA2')->createCommand("
            SELECT DISTINCT (b.callId), a.categoryid AS CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.originalTime,'19700101') AS Fecha_Llamada, dd.fieldValue AS COLUMN1, e.name AS Servicio, DATEADD(s,c.originalTime,'19700101') AS Fechareal, c.externalTextId AS idredbox, 'NA' AS idgrabadora

              FROM 
              [speechminer_8_5_512_A2].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A2].[dbo].[callCategoryTbl] b,                       
              [speechminer_8_5_512_A2].[dbo].[TextData] c,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A2].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_A2].[dbo].[programInfoTbl] e   
                                                                           
              WHERE 
                DATEADD(s,c.originalTime,'19700101') BETWEEN '$varFechaInicioChatEspecial 00:00:00' AND '$varFechaFinChatEspecial 00:00:00'
                  AND e.name IN ('$varBolsitaCXChatEspecial')
                    AND a.categoryid IN ($varBaseRealChatEspecial, $txtListaCategoriasChatEspecial)
                      AND d.fieldName = '$varTipoExtensionesChatEspecial'  
                        AND d.fieldValue IN ('$varExtensionesChatEspecial')
                          AND dd.fieldName = 'duracion_bot'
                            AND d1.fieldName = 'nombre_agentes'
                              AND a.categoryId = b.categoryId
                                AND b.callId=c.textId
                                  AND d.callId=c.textId
                                    AND dd.callId=c.textId
                                      AND d1.callId=c.textId
                                        AND e.programId=c.programId
            ")->queryAll();

        }

        if ($varBaseRealChatEspecial == "1105") {

          $varListaChatEspeciales = Yii::$app->get('dbSpeechE1')->createCommand("
            SELECT DISTINCT (b.callId), a.categoryid AS CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.originalTime,'19700101') AS Fecha_Llamada, dd.fieldValue AS COLUMN1, e.name AS Servicio, DATEADD(s,c.originalTime,'19700101') AS Fechareal, c.externalTextId AS idredbox, 'NA' AS idgrabadora

              FROM 
              [speechminer_8_5_512_E1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_E1].[dbo].[callCategoryTbl] b,                       
              [speechminer_8_5_512_E1].[dbo].[TextData] c,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_E1].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_E1].[dbo].[programInfoTbl] e   
                                                                           
              WHERE 
                DATEADD(s,c.originalTime,'19700101') BETWEEN '$varFechaInicioChatEspecial 00:00:00' AND '$varFechaFinChatEspecial 00:00:00'
                  AND e.name IN ('$varBolsitaCXChatEspecial')
                    AND a.categoryid IN ($varBaseRealChatEspecial, $txtListaCategoriasChatEspecial)
                      AND d.fieldName = '$varTipoExtensionesChatEspecial'  
                        AND d.fieldValue IN ('$varExtensionesChatEspecial')
                          AND dd.fieldName = 'duracion_bot'
                            AND d1.fieldName = 'nombre_agentes'
                              AND a.categoryId = b.categoryId
                                AND b.callId=c.textId
                                  AND d.callId=c.textId
                                    AND dd.callId=c.textId
                                      AND d1.callId=c.textId
                                        AND e.programId=c.programId
            ")->queryAll();

        }

        if ($varBaseRealChatEspecial == "7339") {

          $varListaChatEspeciales = Yii::$app->get('dbSpeechA1')->createCommand("
            SELECT DISTINCT (b.callId), CASE WHEN a.categoryid = 7339 THEN $varCategoriaGeneralChatEspecial ELSE a.categoryid END AS CAtegoriaID, a.name AS Nombre_Categoria, d.fieldValue as extension, d1.fieldValue AS login_id, DATEADD(s,c.originalTime,'19700101') AS Fecha_Llamada, dd.fieldValue AS COLUMN1, e.name AS Servicio, DATEADD(s,c.originalTime,'19700101') AS Fechareal, c.externalTextId AS idredbox, 'NA' AS idgrabadora

              FROM 
              [speechminer_8_5_512_A1].[dbo].[categoryInfoTbl] a,
              [speechminer_8_5_512_A1].[dbo].[callCategoryTbl] b,                       
              [speechminer_8_5_512_A1].[dbo].[TextData] c,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dd,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] dr,
              [speechminer_8_5_512_A1].[dbo].[callMetaExTbl] d1,
              [speechminer_8_5_512_A1].[dbo].[programInfoTbl] e   
                                                                           
              WHERE 
                DATEADD(s,c.originalTime,'19700101') BETWEEN '$varFechaInicioChatEspecial 00:00:00' AND '$varFechaFinChatEspecial 00:00:00'
                  AND e.name IN ('$varBolsitaCXChatEspecial')
                    AND a.categoryid IN ($varBaseRealChatEspecial, $txtListaCategoriasChatEspecial)
                      AND d.fieldName = '$varTipoExtensionesChatEspecial'  
                        AND d.fieldValue IN ('$varExtensionesChatEspecial')
                          AND dd.fieldName = 'duracion_bot'
                            AND d1.fieldName = 'nombre_agentes'
                              AND a.categoryId = b.categoryId
                                AND b.callId=c.textId
                                  AND d.callId=c.textId
                                    AND dd.callId=c.textId
                                      AND d1.callId=c.textId
                                        AND e.programId=c.programId
            ")->queryAll();

        }

        $varVerificaChatEspecial = (new \yii\db\Query())
                                  ->select(['callId'])
                                  ->from(['tbl_dashboardspeechcalls'])            
                                  ->where(['=','servicio',$varBolsitaCXChatEspecial])
                                  ->andwhere(['BETWEEN','fechallamada',$varFechaInicioChatEspecial.' 05:00:00',$varFechaFinChatEspecial.' 05:00:00'])
                                  ->andwhere(['IN','extension',$varExtensionesChatEspecial])
                                  ->andwhere(['=','anulado',0])
                                  ->count();

        if ($varVerificaChatEspecial == 0) {
          
          foreach ($varListaChatEspeciales as $key => $value) {

            Yii::$app->db->createCommand()->insert('tbl_dashboardspeechcalls',[
                                                           'callId' => $value['callId'],
                                                           'idcategoria' => $value['CAtegoriaID'],
                                                           'nombreCategoria' => $value['Nombre_Categoria'],
                                                           'extension' => $value['extension'],
                                                           'login_id' => $value['login_id'],
                                                           'fechallamada' => $value['Fecha_Llamada'],
                                                           'callduracion' => $value['COLUMN1'],
                                                           'servicio' => $value['Servicio'],
                                                           'fechareal' => $value['Fechareal'],
                                                           'idredbox' => $value['idredbox'],
                                                           'idgrabadora' => $value['idgrabadora'],
                                                           'connid' => null,
                                                           'extensiones' => null,
                                                           'fechacreacion' => date('Y-m-d'),
                                                           'anulado' => 0,
                                                        ])->execute();

          }

        }

        $varListaCategorizacionChatEspeciales = Yii::$app->db->createCommand("
            SELECT * FROM 
              (
                SELECT llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria AS llamacategoria, cate.idcategoria AS catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) AS encuentra, llama.nombreCategoria 
                FROM tbl_dashboardspeechcalls llama 
                  LEFT JOIN 
                    (
                      SELECT idcategoria, tipoindicador, programacategoria, cod_pcrc 
                        FROM tbl_speech_categorias 
                          WHERE anulado = 0 AND idcategorias = 2 
                            AND programacategoria IN ('$varBolsitaCXChatEspecial') 
                        ORDER BY cod_pcrc, tipoindicador
                    ) cate ON llama.servicio = cate.programacategoria 
                WHERE llama.servicio IN ('$varBolsitaCXChatEspecial') 
                  AND llama.extension IN ('$varExtensionesChatEspecial') 
                    AND llama.fechallamada BETWEEN '$varFechaInicioChatEspecial 05:00:00' AND '$varFechaFinChatEspecial  05:00:00' 
                GROUP BY llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  
                  ORDER BY encuentra DESC
              ) datos 
            WHERE llamacategoria = catecategoria")->queryAll();

        if (count($varListaCategorizacionChatEspeciales) != 0) {
          
          foreach ($varListaCategorizacionChatEspeciales as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_speech_general',[
                                                           'programacliente' => $value['servicio'],
                                                           'fechainicio' => date('Y-m-01'),
                                                           'fechafin' => NULL,
                                                           'callid' => $value['callid'],
                                                           'fechallamada' => $value['fechallamada'],
                                                           'extension' => $value['extension'],
                                                           'idindicador' => $value['llamacategoria'],
                                                           'idvariable' => $value['catecategoria'],
                                                           'cantproceso' => $value['encuentra'],
                                                           'fechacreacion' => date('Y-m-d'),
                                                           'anulado' => 0,
                                                           'usua_id' => Yii::$app->user->identity->id,
                                                           'arbol_id' => $varIdClienteChatEspecial,
                                                        ])->execute();
          }

        }

      }

    }

    public function actionListarpcrcideal(){            
      $txtAnulado = 0; 
      $txtId = Yii::$app->request->post('id');                       

      if ($txtId) {
        $txtControlBolsita = \app\models\SpeechCategorias::find()->distinct()
                      ->select(['tbl_speech_categorias.programacategoria'])->distinct()
                      ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                      ->where(['tbl_speech_parametrizar.id_dp_clientes' => $txtId])
                      ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                      ->andwhere(['=','tbl_speech_parametrizar.usabilidad',1])
                      ->count();            

        if ($txtControlBolsita > 0) {
          $varListaBolsita = \app\models\SpeechCategorias::find()->distinct()
                        ->select(['tbl_speech_categorias.programacategoria'])->distinct()
                        ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                    'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                        ->where(['tbl_speech_parametrizar.id_dp_clientes' => $txtId])
                        ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                        ->andwhere(['=','tbl_speech_parametrizar.usabilidad',1])                           
                        ->orderBy(['tbl_speech_categorias.programacategoria' => SORT_DESC])
                        ->all();            
                    
          echo "<option value='' disabled selected>Seleccionar...</option>";
          echo "<option value='001' disabled selected>Todos</option>";
          foreach ($varListaBolsita as $key => $value) {
            echo "<option value='" . $value->programacategoria. "'>" . $value->programacategoria . "</option>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }

    }

    public function actionActualizabaseideal(){
      $model = new SpeechCategorias();

      $form = Yii::$app->request->post();     
      if($model->load($form)){
        $varClienteIdeal = $model->tipoparametro;
        $varCodPcrcIDeal = $model->cod_pcrc;
        $varListaCodPcrcIDeal = explode(",", str_replace(array("#", "'", ";", " "), '', $varCodPcrcIDeal));

        $varBolsitasIdeal = (new \yii\db\Query())
                              ->select(['programacategoria'])
                              ->from(['tbl_speech_categorias'])            
                              ->where(['in','cod_pcrc',$varListaCodPcrcIDeal])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['programacategoria'])                              
                              ->Scalar();

        $varFechaIdeal = explode(" ", $model->fechacreacion);

        $varFechaInicioIdeal = $varFechaIdeal[0];
        $varFechaFinIdeal = date('Y-m-d',strtotime($varFechaIdeal[2]."+ 1 days"));

        $varConcatenarIdeal = $varClienteIdeal."; ".$varBolsitasIdeal."; ".$varFechaInicioIdeal."; ".$varFechaFinIdeal."; ".$varCodPcrcIDeal;

        $this->Prepararideal($varConcatenarIdeal);

        $this->Registrallamadasideal($varConcatenarIdeal);

        $this->Registraindicadores($varConcatenarIdeal);

        $this->Registravariables($varConcatenarIdeal);

        // $this->Registramotivos($varConcatenarIdeal);

        $this->Registranovedades($varConcatenarIdeal); 

        // $this->Registratmpmotivos($varConcatenarIdeal);    

        // $this->Registratmpasesores($varConcatenarIdeal);

        $this->Registratmplogin($varConcatenarIdeal);   

        $this->Registralistaresponsable($varConcatenarIdeal);

        return $this->redirect(['actualizarllamadas']);

      }

      return $this->renderAjax('actualizabaseideal',[
        'model' => $model,
      ]);
    }

    public function Prepararideal($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdeal = null;
      $varServicioIdeal = null;
      $varFechaIdealInicio = null;
      $varFechaIdealFin = null;
      $varListasCodPcrc = null;

      $varListaItemsIdeal = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdeal); $i++) { 
        $varIdClienteIdeal = $varListaItemsIdeal[0];
        $varServicioIdeal = $varListaItemsIdeal[1];
        $varFechaIdealInicio = $varListaItemsIdeal[2];
        $varFechaIdealFin = $varListaItemsIdeal[3];
        $varListasCodPcrc = $varListaItemsIdeal[4];
      }

      $varModificaListaCodPcrc = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrc));
      
      // Preparar la tabla de llamadas ideal
      $varListaGeneralIdeal = (new \yii\db\Query())
                              ->select(['id_llamadas'])
                              ->from(['tbl_ideal_llamadas'])            
                              ->where(['=','id_dp_cliente',$varIdClienteIdeal])
                              ->andwhere(['=','bolsita',$varServicioIdeal])
                              ->andwhere(['in','cod_pcrc',$varModificaListaCodPcrc])
                              ->andwhere(['>=','fechainicio',$varFechaIdealInicio.' 05:00:00'])
                              ->andwhere(['<=','fechafin',$varFechaIdealFin.' 05:00:00'])
                              ->andwhere(['=','anulado',0])                              
                              ->All();

      if (count($varListaGeneralIdeal != 0)) {
        foreach ($varListaGeneralIdeal as $key => $value) {
          Yii::$app->db->createCommand('UPDATE tbl_ideal_llamadas SET anulado = 1 WHERE id_llamadas=:id')->bindParam(':id',$value['id_llamadas'])->execute();
        }
      }

      // Preparar la tabla de indicadores
      $varListaIndicadoresIdeal = (new \yii\db\Query())
                              ->select(['id_indicadores'])
                              ->from(['tbl_ideal_indicadores'])            
                              ->where(['=','id_dp_cliente',$varIdClienteIdeal])
                              ->andwhere(['=','bolsita',$varServicioIdeal])
                              ->andwhere(['in','cod_pcrc',$varModificaListaCodPcrc])
                              ->andwhere(['>=','fechainicio',$varFechaIdealInicio.' 05:00:00'])
                              ->andwhere(['<=','fechafin',$varFechaIdealFin.' 05:00:00'])
                              ->andwhere(['=','anulado',0])                              
                              ->All();

      if (count($varListaIndicadoresIdeal) != 0) {
        foreach ($varListaIndicadoresIdeal as $key => $value) {
          Yii::$app->db->createCommand('UPDATE tbl_ideal_indicadores SET anulado = 1 WHERE id_indicadores=:id')->bindParam(':id',$value['id_indicadores'])->execute();
        }
      }

      // Preparar la tabla de responsabilidades
      $varListaResponsablesIdeal = (new \yii\db\Query())
                              ->select(['id_responsabilidadesideal'])
                              ->from(['tbl_ideal_responsabilidad'])            
                              ->where(['=','id_dp_cliente',$varIdClienteIdeal])
                              ->andwhere(['=','bolsita',$varServicioIdeal])
                              ->andwhere(['in','cod_pcrc',$varModificaListaCodPcrc])
                              ->andwhere(['>=','fechainicio',$varFechaIdealInicio.' 05:00:00'])
                              ->andwhere(['<=','fechafin',$varFechaIdealFin.' 05:00:00'])
                              ->andwhere(['=','anulado',0])                              
                              ->All();

      if (count($varListaResponsablesIdeal) != 0) {
        foreach ($varListaResponsablesIdeal as $key => $value) {
          Yii::$app->db->createCommand('UPDATE tbl_ideal_responsabilidad SET anulado = 1 WHERE id_responsabilidadesideal=:id')->bindParam(':id',$value['id_responsabilidadesideal'])->execute();
        }
      }    

      // Preparar la tabla de login responsabilidades
      $varListaLoginResponsablesIdeal = (new \yii\db\Query())
                              ->select(['id_loginresponsabilidad'])
                              ->from(['tbl_ideal_loginresponsabilidad'])            
                              ->where(['=','id_dp_cliente',$varIdClienteIdeal])
                              ->andwhere(['=','bolsita',$varServicioIdeal])
                              ->andwhere(['in','cod_pcrc',$varModificaListaCodPcrc])
                              ->andwhere(['>=','fechainicio',$varFechaIdealInicio.' 05:00:00'])
                              ->andwhere(['<=','fechafin',$varFechaIdealFin.' 05:00:00'])
                              ->andwhere(['=','anulado',0])                              
                              ->All();

      if (count($varListaLoginResponsablesIdeal) != 0) {
        foreach ($varListaLoginResponsablesIdeal as $key => $value) {
          Yii::$app->db->createCommand('UPDATE tbl_ideal_loginresponsabilidad SET anulado = 1 WHERE id_loginresponsabilidad=:id')->bindParam(':id',$value['id_loginresponsabilidad'])->execute();
        }
      } 

      // Preparar la tabla de Variables
      $varListaVariablesIdeal = (new \yii\db\Query())
                              ->select(['id_variablesi'])
                              ->from(['tbl_ideal_variables'])            
                              ->where(['=','id_dp_cliente',$varIdClienteIdeal])
                              ->andwhere(['=','bolsita',$varServicioIdeal])
                              ->andwhere(['in','cod_pcrc',$varModificaListaCodPcrc])
                              ->andwhere(['>=','fechainicio',$varFechaIdealInicio.' 05:00:00'])
                              ->andwhere(['<=','fechafin',$varFechaIdealFin.' 05:00:00'])
                              ->andwhere(['=','anulado',0])                              
                              ->All();

      if (count($varListaVariablesIdeal) != 0) {
        foreach ($varListaVariablesIdeal as $key => $value) {
          Yii::$app->db->createCommand('UPDATE tbl_ideal_variables SET anulado = 1 WHERE id_variablesi=:id')->bindParam(':id',$value['id_variablesi'])->execute();
        }
      } 

      // Preparar la tabla de Motivos
      $varListaMotivosIdeal = (new \yii\db\Query())
                              ->select(['id_motivosi'])
                              ->from(['tbl_ideal_motivos'])            
                              ->where(['=','id_dp_cliente',$varIdClienteIdeal])
                              ->andwhere(['=','bolsita',$varServicioIdeal])
                              ->andwhere(['in','cod_pcrc',$varModificaListaCodPcrc])
                              ->andwhere(['>=','fechainicio',$varFechaIdealInicio.' 05:00:00'])
                              ->andwhere(['<=','fechafin',$varFechaIdealFin.' 05:00:00'])
                              ->andwhere(['=','anulado',0])                              
                              ->All();

      if (count($varListaMotivosIdeal) != 0) {
        foreach ($varListaMotivosIdeal as $key => $value) {
          Yii::$app->db->createCommand('UPDATE tbl_ideal_motivos SET anulado = 1 WHERE id_motivosi=:id')->bindParam(':id',$value['id_motivosi'])->execute();
        }
      }  


      // Preparar la tabla de Tmp Motivos
      $varListaMotivosIdealTmp = (new \yii\db\Query())
                              ->select(['id_tmpmotivos'])
                              ->from(['tbl_speech_tmpmotivos'])            
                              ->where(['=','id_dp_cliente',$varIdClienteIdeal])
                              ->andwhere(['in','cod_pcrc',$varModificaListaCodPcrc])
                              ->andwhere(['=','anulado',0])                              
                              ->All();

      if (count($varListaMotivosIdealTmp) != 0) {
        foreach ($varListaMotivosIdealTmp as $key => $value) {
          Yii::$app->db->createCommand('DELETE FROM tbl_speech_tmpmotivos WHERE id_tmpmotivos=:id')->bindParam(':id',$value['id_tmpmotivos'])->execute();
        }
      }

      // Preparar la tabla de Tmp Motivos Variables
      $varListaMotivosIdealTmpV = (new \yii\db\Query())
                              ->select(['id_tmpmotivosvariable'])
                              ->from(['tbl_speech_tmpmotivosvariables'])            
                              ->where(['=','id_dp_cliente',$varIdClienteIdeal])
                              ->andwhere(['in','cod_pcrc',$varModificaListaCodPcrc])
                              ->andwhere(['=','anulado',0])                              
                              ->All();

      if (count($varListaMotivosIdealTmpV) != 0) {
        foreach ($varListaMotivosIdealTmpV as $key => $value) {
          Yii::$app->db->createCommand('DELETE FROM tbl_speech_tmpmotivosvariables WHERE id_tmpmotivosvariable=:id')->bindParam(':id',$value['id_tmpmotivosvariable'])->execute();
        }
      }

    }

    public function Registrallamadasideal($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealG = null;
      $varServicioIdealG = null;
      $varFechaIdealInicioG = null;
      $varFechaIdealFinG = null;
      $varListasCodPcrcG = null;

      $varListaItemsIdealG = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealG); $i++) { 
        $varIdClienteIdealG = $varListaItemsIdealG[0];
        $varServicioIdealG = $varListaItemsIdealG[1];
        $varFechaIdealInicioG = $varListaItemsIdealG[2];
        $varFechaIdealFinG = $varListaItemsIdealG[3];
        $varListasCodPcrcG = $varListaItemsIdealG[4];
      }

      $varFechaFinalRealSpeech = date('Y-m-d',strtotime($varFechaIdealFinG."- 1 days"));
      $varDiasReales = date('d',strtotime($varFechaFinalRealSpeech));
      $varMesReal = date('m',strtotime($varFechaFinalRealSpeech));

      for ($i=1; $i <= intval($varDiasReales); $i++) { 
        if ($i<10) {
          $i = '0'.$i;
        }
        $varFechaIdealInicioG = date('Y-'.$varMesReal.'-'.$i);
        
        $varFechaIdealFinG = date('Y-m-d',strtotime($varFechaIdealInicioG."+ 1 days"));        

        $varListaPcrcIdealsG = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrcG));

        $varGeneralIdealG = (new \yii\db\Query())
                                ->select(['idllamada'])
                                ->from(['tbl_speech_servicios'])            
                                ->where(['=','id_dp_clientes',$varIdClienteIdealG])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['idllamada'])
                                ->Scalar();

        $varListaPcrcIdealG = (new \yii\db\Query())
                                ->select(['cod_pcrc'])
                                ->from(['tbl_speech_categorias']) 
                                ->where(['in','cod_pcrc',$varListaPcrcIdealsG])
                                ->andwhere(['=','programacategoria',$varServicioIdealG])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['cod_pcrc'])
                                ->All();


        foreach ($varListaPcrcIdealG as $key => $value) {
          $varPcrcCodIdealG = $value['cod_pcrc'];

          $varTipoParmsIdealG = (new \yii\db\Query())
                                ->select(['tipoparametro'])
                                ->from(['tbl_speech_parametrizar'])
                                ->where(['=','anulado',0])
                                ->groupby(['tipoparametro'])
                                ->all();

          foreach ($varTipoParmsIdealG as $key => $value) {
            $varTipoParametroIdealG = $value['tipoparametro'];

            if ($varTipoParametroIdealG != null) {
              $varReglaNegocioIdealG =  (new \yii\db\Query())
                                  ->select(['rn'])
                                  ->from(['tbl_speech_parametrizar'])            
                                  ->where(['=','cod_pcrc',$varPcrcCodIdealG])
                                  ->andwhere(['=','anulado',0])
                                  ->andwhere(['=','usabilidad',1])
                                  ->andwhere(['!=','rn',''])
                                  ->andwhere(['=','tipoparametro',$varTipoParametroIdealG])
                                  ->groupby(['rn'])
                                  ->all();
            }else{
              $varReglaNegocioIdealG =  (new \yii\db\Query())
                                  ->select(['rn'])
                                  ->from(['tbl_speech_parametrizar'])            
                                  ->where(['=','cod_pcrc',$varPcrcCodIdealG])
                                  ->andwhere(['=','anulado',0])
                                  ->andwhere(['=','usabilidad',1])
                                  ->andwhere(['!=','rn',''])
                                  ->andwhere(['is','tipoparametro',$varTipoParametroIdealG])
                                  ->groupby(['rn'])
                                  ->all();
            }


            if (count($varReglaNegocioIdealG) != 0) {
              foreach ($varReglaNegocioIdealG as $key => $value) {
                $varRnIdealG = $value['rn'];

                $varCantidadRnIdealG = (new \yii\db\Query())
                                  ->select(['callId'])
                                  ->from(['tbl_dashboardspeechcalls'])
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','servicio',$varServicioIdealG])
                                  ->andwhere(['between','fechallamada',$varFechaIdealInicioG.' 05:00:00',$varFechaIdealFinG .' 05:00:00'])
                                  ->andwhere(['=','idcategoria',$varGeneralIdealG])
                                  ->andwhere(['=','extension',$varRnIdealG])
                                  ->count();

                if ($varTipoParametroIdealG == null) {
                  $varTipoParametroIdealG = 0;
                }

                Yii::$app->db->createCommand()->insert('tbl_ideal_llamadas',[
                      'id_dp_cliente' => $varIdClienteIdealG,
                      'bolsita' => $varServicioIdealG,
                      'cod_pcrc' => $varPcrcCodIdealG,
                      'extension' => $varRnIdealG, 
                      'tipoextension' => $varTipoParametroIdealG,
                      'cantidad' => $varCantidadRnIdealG,
                      'fechainicio' => $varFechaIdealInicioG.' 05:00:00',
                      'fechafin' => $varFechaIdealFinG .' 05:00:00',
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
                  ])->execute();
              }
            }else{

              if ($varTipoParametroIdealG != null) {
                $varExtIdealG =  (new \yii\db\Query())
                                  ->select(['ext'])
                                  ->from(['tbl_speech_parametrizar'])            
                                  ->where(['=','cod_pcrc',$varPcrcCodIdealG])
                                  ->andwhere(['=','anulado',0])
                                  ->andwhere(['=','usabilidad',1])
                                  ->andwhere(['!=','ext',''])
                                  ->andwhere(['=','tipoparametro',$varTipoParametroIdealG])
                                  ->groupby(['ext'])
                                  ->all();
              }else{
                $varExtIdealG =  (new \yii\db\Query())
                                  ->select(['ext'])
                                  ->from(['tbl_speech_parametrizar'])            
                                  ->where(['=','cod_pcrc',$varPcrcCodIdealG])
                                  ->andwhere(['=','anulado',0])
                                  ->andwhere(['=','usabilidad',1])
                                  ->andwhere(['!=','ext',''])
                                  ->andwhere(['is','tipoparametro',null])
                                  ->groupby(['ext'])
                                  ->all();
              }

              if (count($varExtIdealG) != 0) {
                foreach ($varExtIdealG as $key => $value) {
                  $varExtIdealG = $value['ext'];

                  $varCantidadExtIdealG = (new \yii\db\Query())
                                    ->select(['callId'])
                                    ->from(['tbl_dashboardspeechcalls'])
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','servicio',$varServicioIdealG])
                                    ->andwhere(['between','fechallamada',$varFechaIdealInicioG.' 05:00:00',$varFechaIdealFinG .' 05:00:00'])
                                    ->andwhere(['=','idcategoria',$varGeneralIdealG])
                                    ->andwhere(['=','extension',$varExtIdealG])
                                    ->count();

                  if ($varTipoParametroIdealG == null) {
                    $varTipoParametroIdealG = 0;
                  }

                  Yii::$app->db->createCommand()->insert('tbl_ideal_llamadas',[
                        'id_dp_cliente' => $varIdClienteIdealG,
                        'bolsita' => $varServicioIdealG,
                        'cod_pcrc' => $varPcrcCodIdealG,
                        'extension' => $varExtIdealG, 
                        'tipoextension' => $varTipoParametroIdealG,
                        'cantidad' => $varCantidadExtIdealG,
                        'fechainicio' => $varFechaIdealInicioG.' 05:00:00',
                        'fechafin' => $varFechaIdealFinG .' 05:00:00',
                        'usua_id' => Yii::$app->user->identity->id,
                        'fechacreacion' => date('Y-m-d'),
                        'anulado' => 0,                        
                    ])->execute();
                }
              }else{

                if ($varTipoParametroIdealG != null) {
                  $varUsuaRedIdealG =  (new \yii\db\Query())
                                  ->select(['usuared'])
                                  ->from(['tbl_speech_parametrizar'])            
                                  ->where(['=','cod_pcrc',$varPcrcCodIdealG])
                                  ->andwhere(['=','anulado',0])
                                  ->andwhere(['=','usabilidad',1])
                                  ->andwhere(['!=','usuared',''])
                                  ->andwhere(['=','tipoparametro',$varTipoParametroIdealG])
                                  ->groupby(['usuared'])
                                  ->all();
                }else{
                  $varUsuaRedIdealG =  (new \yii\db\Query())
                                  ->select(['usuared'])
                                  ->from(['tbl_speech_parametrizar'])            
                                  ->where(['=','cod_pcrc',$varPcrcCodIdealG])
                                  ->andwhere(['=','anulado',0])
                                  ->andwhere(['=','usabilidad',1])
                                  ->andwhere(['!=','usuared',''])
                                  ->andwhere(['is','tipoparametro',null])
                                  ->groupby(['usuared'])
                                  ->all();
                }

                if (count($varUsuaRedIdealG) != 0) {
                  foreach ($varUsuaRedIdealG as $key => $value) {
                    $varUsuaIdealG = $value['usuared'];

                    $varCantidadUsuaIdealG = (new \yii\db\Query())
                                      ->select(['callId'])
                                      ->from(['tbl_dashboardspeechcalls'])
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','servicio',$varServicioIdealG])
                                      ->andwhere(['between','fechallamada',$varFechaIdealInicioG.' 05:00:00',$varFechaIdealFinG .' 05:00:00'])
                                      ->andwhere(['=','idcategoria',$varGeneralIdealG])
                                      ->andwhere(['=','extension',$varUsuaIdealG])
                                      ->count();

                    if ($varTipoParametroIdealG == null) {
                      $varTipoParametroIdealG = 0;
                    }

                    Yii::$app->db->createCommand()->insert('tbl_ideal_llamadas',[
                          'id_dp_cliente' => $varIdClienteIdealG,
                          'bolsita' => $varServicioIdealG,
                          'cod_pcrc' => $varPcrcCodIdealG,
                          'extension' => $varUsuaIdealG,  
                          'tipoextension' => $varTipoParametroIdealG,
                          'cantidad' => $varCantidadUsuaIdealG,
                          'fechainicio' => $varFechaIdealInicioG.' 05:00:00',
                          'fechafin' => $varFechaIdealFinG .' 05:00:00',
                          'usua_id' => Yii::$app->user->identity->id,
                          'fechacreacion' => date('Y-m-d'),
                          'anulado' => 0,                        
                      ])->execute();
                  }
                }else{
                  Yii::$app->db->createCommand()->insert('tbl_ideal_llamadas',[
                          'id_dp_cliente' => $varIdClienteIdealG,
                          'bolsita' => $varServicioIdealG,
                          'cod_pcrc' => $varPcrcCodIdealG,
                          'extension' => '', 
                          'cantidad' => 0,
                          'tipoextension' => null,
                          'fechainicio' => $varFechaIdealInicioG.' 05:00:00',
                          'fechafin' => $varFechaIdealFinG .' 05:00:00',
                          'usua_id' => Yii::$app->user->identity->id,
                          'fechacreacion' => date('Y-m-d'),
                          'anulado' => 0,                        
                      ])->execute();
                }
              }
            }
          }
          
        }
      }


    }

    public function Registraindicadores($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealI = null;
      $varServicioIdealI = null;
      $varFechaIdealInicioI = null;
      $varFechaIdealFinI = null;
      $varListasCodPcrcI = null;

      $varListaItemsIdealI = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealI); $i++) { 
        $varIdClienteIdealI = $varListaItemsIdealI[0];
        $varServicioIdealI = $varListaItemsIdealI[1];
        $varFechaIdealInicioI = $varListaItemsIdealI[2];
        $varFechaIdealFinI = $varListaItemsIdealI[3];
        $varListasCodPcrcI = $varListaItemsIdealI[4];
      }

      $varListaPcrcIdealsI = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrcI));

      $varListaPcrcIdealI = (new \yii\db\Query())
                              ->select(['cod_pcrc'])
                              ->from(['tbl_speech_categorias']) 
                              ->where(['in','cod_pcrc',$varListaPcrcIdealsI])
                              ->andwhere(['=','programacategoria',$varServicioIdealI])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->All();

      foreach ($varListaPcrcIdealI as $key => $value) {
        $varPcrcCodIdealI = $value['cod_pcrc'];

        $varTipoParmsI = (new \yii\db\Query())
                                ->select(['tipoparametro'])
                                ->from(['tbl_speech_parametrizar'])
                                ->where(['=','anulado',0])
                                ->groupby(['tipoparametro'])
                                ->all();

        foreach ($varTipoParmsI as $key => $value) {

          $varTipoParametroI = $value['tipoparametro'];

          if ($varTipoParametroI != null) {
            $varRnIdealI =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroI])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealI =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }


          if (count($varRnIdealI) != 0) {
            $varArrayRnI = array();
            foreach ($varRnIdealI as $key => $value) {
              array_push($varArrayRnI, $value['rn']);
            }

            $varExtensionesArraysI = implode("', '", $varArrayRnI);
            $arrayExtensiones_downI = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysI);
            $varExtensionesI = explode(",", $arrayExtensiones_downI);
          }else{

            if ($varTipoParametroI != null) {
              $varExtI =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroI])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtI =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtI) != 0) {
              $varArrayExtI = array();
              foreach ($varExtI as $key => $value) {
                array_push($varArrayExtI, $value['ext']);
              }

              $varExtensionesArraysI = implode("', '", $varArrayExtI);
              $arrayExtensiones_downI = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysI);
              $varExtensionesI = explode(",", $arrayExtensiones_downI);
            }else{

              if ($varTipoParametroI != null) {
                $varUsuaI =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroI])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaI =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }

              if (count($varUsuaI) != 0) {
                $varArrayUsuaI = array();
                foreach ($varUsuaI as $key => $value) {
                  array_push($varArrayUsuaI, $value['usuared']);
                }

                $varExtensionesArraysI = implode("', '", $varArrayUsuaI);
                $arrayExtensiones_downI = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysI);
                $varExtensionesI = explode(",", $arrayExtensiones_downI);
              }else{
                $varExtensionesI = "N0A";
              }
            }
          }

          $varListCallidIndicadores = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$varServicioIdealI])
                                ->andwhere(['in','extension',$varExtensionesI])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioI.' 05:00:00',$varFechaIdealFinI.' 05:00:00'])
                                ->groupby(['callid'])
                                ->all();

          $varArrayCallidI = array();
          foreach ($varListCallidIndicadores as $key => $value) {
            array_push($varArrayCallidI, $value['callid']);
          }
          $varCallidsListI = implode(", ", $varArrayCallidI);
          $arrayCallids_downI = str_replace(array("#", "'", ";", " "), '', $varCallidsListI);
          $varCallidsIndicadores = explode(",", $arrayCallids_downI);

          $varCantidadLlamadasIndicador = (new \yii\db\Query())
                                ->select(['sum(cantidad)'])
                                ->from(['tbl_ideal_llamadas'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_dp_cliente',$varIdClienteIdealI])
                                ->andwhere(['=','bolsita',$varServicioIdealI])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['in','extension',$varExtensionesI])
                                ->andwhere(['>=','fechainicio',$varFechaIdealInicioI.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaIdealFinI.' 05:00:00'])
                                ->Scalar();

          $varListarIndicadoresI = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['=','idcategorias',1])
                                ->all(); 


          foreach ($varListarIndicadoresI as $key => $value) {
            $txtIdIndicadores = $value['idcategoria'];
            $varNombreIndicador = $value['nombre'];
            $varTipoParametro = $value['tipoparametro'];
            $txtTipoFormIndicador = $value['orientacionform'];

            $varListVariablesI = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform','responsable'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealI])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','tipoindicador',$varNombreIndicador])
                                ->all();

            $arrayListOfVar = array();
            $arraYListOfVarMas = array();
            $arraYListOfVarMenos = array();

            $varSumarPositivas = 0;
            $varSumarNegativas = 0;

            $arrayRAgente = array();
            $arrayRMarca = array();
            $arrayRCanal = array();
            foreach ($varListVariablesI as $key => $value) {
              $varOrienta = $value['orientacionsmart'];
              $varResponsable = $value['responsable'];
              array_push($arrayListOfVar, $value['idcategoria']);

              if ($varOrienta == 1) {
                array_push($arraYListOfVarMenos, $value['idcategoria']);
                $varSumarNegativas = $varSumarNegativas + 1;
              }else{
                array_push($arraYListOfVarMas, $value['idcategoria']);
                $varSumarPositivas = $varSumarPositivas + 1;
              }

              if ($varResponsable == 1) {
                array_push($arrayRAgente, $value['idcategoria']);
              }else{
                if ($varResponsable == 2) {
                  array_push($arrayRCanal, $value['idcategoria']);
                }else{
                  if ($varResponsable == 3) {
                    array_push($arrayRMarca, $value['idcategoria']);
                  }else{
                    $varna = 0;
                  }                    
                }
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

            $arrayRAgenteList = implode(", ", $arrayRAgente);
            $arrayRAgente_down = str_replace(array("#", "'", ";", " "), '', $arrayRAgenteList);
            $arrayAgente = explode(",", $arrayRAgente_down);

            $arrayRCanalList = implode(", ", $arrayRCanal);
            $arrayRCanal_down = str_replace(array("#", "'", ";", " "), '', $arrayRCanalList);
            $arrayCanal = explode(",", $arrayRCanal_down);

            $arrayRMarcaList = implode(", ", $arrayRMarca);
            $arrayRMarca_down = str_replace(array("#", "'", ";", " "), '', $arrayRMarcaList);
            $arrayMarca = explode(",", $arrayRMarca_down);

            $varTotalvariables = count($varListVariablesI);

            if ($varTipoParametro == "2") {
              if ($varSumarPositivas == $varTotalvariables) {
                $varconteo = (new \yii\db\Query())
                                ->select(['callid','SUM(cantproceso)'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$varServicioIdealI])
                                ->andwhere(['in','extension',$varExtensionesI])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioI.' 05:00:00',$varFechaIdealFinI.' 05:00:00'])
                                ->andwhere(['in','callid',$varCallidsIndicadores])
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
                                ->andwhere(['=','programacliente',$varServicioIdealI])
                                ->andwhere(['in','extension',$varExtensionesI])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioI.' 05:00:00',$varFechaIdealFinI.' 05:00:00'])
                                ->andwhere(['in','callid',$varCallidsIndicadores])
                                ->andwhere(['in','idindicador',$arrayVariableMenos])
                                ->andwhere(['in','idvariable',$arrayVariableMenos])
                                ->groupby(['callid'])
                                ->count();

                if ($varconteo != null) {
                  $varconteo = round(count($varCallidsIndicadores) - $varconteo);                
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
                                ->andwhere(['=','programacliente',$varServicioIdealI])
                                ->andwhere(['in','extension',$varExtensionesI])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioI.' 05:00:00',$varFechaIdealFinI.' 05:00:00'])
                                ->andwhere(['in','callid',$varCallidsIndicadores])
                                ->andwhere(['in','idindicador',$arrayVariableMas])
                                ->andwhere(['in','idvariable',$arrayVariableMas])
                                ->groupby(['callid'])
                                ->count();
              }else{
                $varconteo = 0;
              }

              if ($arrayVariableMenos != "") {
                $varconteo = $varconteo = (new \yii\db\Query())
                                ->select(['callid','SUM(cantproceso)'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$varServicioIdealI])
                                ->andwhere(['in','extension',$varExtensionesI])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioI.' 05:00:00',$varFechaIdealFinI.' 05:00:00'])
                                ->andwhere(['in','callid',$varCallidsIndicadores])
                                ->andwhere(['in','idindicador',$arrayVariableMas])
                                ->andwhere(['in','idvariable',$arrayVariableMas])
                                ->groupby(['callid'])
                                ->count();
              }else{
                $varconteo = 0;
              }
            }

            if ($varconteo != 0 && $varCantidadLlamadasIndicador != 0) {
              if ($txtTipoFormIndicador == 0) {
                $txtRtaProcentaje = (round(($varconteo / $varCantidadLlamadasIndicador) * 100, 1));
              }else{

                $txtRtaProcentaje = (100 - (round(($varconteo / $varCantidadLlamadasIndicador) * 100, 1)));
              }
            }else{
              if ($txtTipoFormIndicador == 0) {
                $txtRtaProcentaje = 100;
              }else{
                $txtRtaProcentaje = 0;
              }
            }

            if ($varTipoParametroI == null) {
              $varTipoParametroI = 0;
            }

            Yii::$app->db->createCommand()->insert('tbl_ideal_indicadores',[
                      'id_dp_cliente' => $varIdClienteIdealI,
                      'bolsita' => $varServicioIdealI,
                      'cod_pcrc' => $varPcrcCodIdealI,
                      'extension' => $varTipoParametroI,
                      'id_categoriai' => $txtIdIndicadores,
                      'indicador' => $varNombreIndicador,
                      'cantidad_indicador' => $txtRtaProcentaje,
                      'fechainicio' => $varFechaIdealInicioI.' 05:00:00',
                      'fechafin' => $varFechaIdealFinI.' 05:00:00',
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
            ])->execute(); 

            $varconteoAgente =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$varServicioIdealI])
                                  ->andwhere(['in','extension',$varExtensionesI])
                                  ->andwhere(['between','fechallamada',$varFechaIdealInicioI.' 05:00:00',$varFechaIdealFinI.' 05:00:00'])
                                  ->andwhere(['in','callid',$varCallidsIndicadores])
                                  ->andwhere(['in','idindicador',$arrayAgente])
                                  ->andwhere(['in','idvariable',$arrayAgente])
                                  ->groupby(['callid'])
                                  ->count();

            if ($varconteoAgente == null) {
              $varconteoAgente = 0;
            }


            if ($varconteoAgente != 0) {
              if ($txtTipoFormIndicador == 0) {
                $txtRtaAgente = (round(($varconteoAgente / $varCantidadLlamadasIndicador) * 100, 1));
              }else{
                $txtRtaAgente = (100 - (round(($varconteoAgente / $varCantidadLlamadasIndicador) * 100, 1)));
              }
            }else{
              $txtRtaAgente = 0;
            }


            $varconteoMarca =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$varServicioIdealI])
                                  ->andwhere(['in','extension',$varExtensionesI])
                                  ->andwhere(['between','fechallamada',$varFechaIdealInicioI.' 05:00:00',$varFechaIdealFinI.' 05:00:00'])
                                  ->andwhere(['in','callid',$varCallidsIndicadores])
                                  ->andwhere(['in','idindicador',$arrayMarca])
                                  ->andwhere(['in','idvariable',$arrayMarca])
                                  ->groupby(['callid'])
                                  ->count();

            if ($varconteoMarca == null) {
              $varconteoMarca = 0;
            }

            if ($varconteoMarca != 0) {
              if ($txtTipoFormIndicador == 0) {
                $txtRtaMarca = (round(($varconteoMarca / $varCantidadLlamadasIndicador) * 100, 1));
              }else{
                $txtRtaMarca = (100 - (round(($varconteoMarca / $varCantidadLlamadasIndicador) * 100, 1)));
              }
            }else{
              $txtRtaMarca = 0;
            }

            $varconteoCanal =  (new \yii\db\Query())
                                  ->select(['callid','SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$varServicioIdealI])
                                  ->andwhere(['in','extension',$varExtensionesI])
                                  ->andwhere(['between','fechallamada',$varFechaIdealInicioI.' 05:00:00',$varFechaIdealFinI.' 05:00:00'])
                                  ->andwhere(['in','callid',$varCallidsIndicadores])
                                  ->andwhere(['in','idindicador',$arrayCanal])
                                  ->andwhere(['in','idvariable',$arrayCanal])
                                  ->groupby(['callid'])
                                  ->count();

            if ($varconteoCanal == null) {
              $varconteoCanal = 0;
            }

            if ($varconteoCanal != 0) {
              if ($txtTipoFormIndicador == 0) {
                $txtRtaCanal = (round(($varconteoCanal / $varCantidadLlamadasIndicador) * 100, 1));
              }else{
                $txtRtaCanal = (100 - (round(($varconteoCanal / $varCantidadLlamadasIndicador) * 100, 1)));
              }
            }else{
              $txtRtaCanal = 0;
            }

            if ($varTipoParametroI == null) {
              $varTipoParametroI = 0;
            }

            Yii::$app->db->createCommand()->insert('tbl_ideal_responsabilidad',[
                      'id_dp_cliente' => $varIdClienteIdealI,
                      'bolsita' => $varServicioIdealI,
                      'cod_pcrc' => $varPcrcCodIdealI,
                      'extension' => $varTipoParametroI,
                      'id_categoriai' => $txtIdIndicadores,
                      'marca' => $txtRtaMarca,
                      'canal' => $txtRtaCanal,
                      'agente' => $txtRtaAgente,
                      'fechainicio' => $varFechaIdealInicioI.' 05:00:00',
                      'fechafin' => $varFechaIdealFinI.' 05:00:00',
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
            ])->execute(); 


          }

        }

      }
      

    }

    public function Registravariables($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealV = null;
      $varServicioIdealV = null;
      $varFechaIdealInicioV = null;
      $varFechaIdealFinV = null;
      $varListasCodPcrcV = null;

      $varListaItemsIdealV = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealV); $i++) { 
        $varIdClienteIdealV = $varListaItemsIdealV[0];
        $varServicioIdealV = $varListaItemsIdealV[1];
        $varFechaIdealInicioV = $varListaItemsIdealV[2];
        $varFechaIdealFinV = $varListaItemsIdealV[3];
        $varListasCodPcrcV = $varListaItemsIdealV[4];
      }

      $varListaPcrcIdealsV = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrcV));

      $varListaPcrcIdealV = (new \yii\db\Query())
                              ->select(['cod_pcrc'])
                              ->from(['tbl_speech_categorias']) 
                              ->where(['in','cod_pcrc',$varListaPcrcIdealsV])
                              ->andwhere(['=','programacategoria',$varServicioIdealV])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->All();

      foreach ($varListaPcrcIdealV as $key => $value) {
        $varPcrcCodIdealV = $value['cod_pcrc'];

        $varTipoParmsV = (new \yii\db\Query())
                                ->select(['tipoparametro'])
                                ->from(['tbl_speech_parametrizar'])
                                ->where(['=','anulado',0])
                                ->groupby(['tipoparametro'])
                                ->all();

        foreach ($varTipoParmsV as $key => $value) {
          $varTipoParametroV = $value['tipoparametro'];

          if ($varTipoParametroV != null) {
            $varRnIdealV =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroV])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealV =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }

          if (count($varRnIdealV) != 0) {
            $varArrayRnV = array();
            foreach ($varRnIdealV as $key => $value) {
              array_push($varArrayRnV, $value['rn']);
            }

            $varExtensionesArraysV = implode("', '", $varArrayRnV);
            $arrayExtensiones_downV = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysV);
            $varExtensionesV = explode(",", $arrayExtensiones_downV);
          }else{

            if ($varTipoParametroV != null) {
              $varExtV =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroV])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtV =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtV) != 0) {
              $varArrayExtV = array();
              foreach ($varExtV as $key => $value) {
                array_push($varArrayExtV, $value['ext']);
              }

              $varExtensionesArraysV = implode("', '", $varArrayExtV);
              $arrayExtensiones_downV = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysV);
              $varExtensionesV = explode(",", $arrayExtensiones_downV);
            }else{

              if ($varTipoParametroV != null) {
                $varUsuaV =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroV])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaV =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }

              if (count($varUsuaV) != 0) {
                $varArrayUsuaV = array();
                foreach ($varUsuaV as $key => $value) {
                  array_push($varArrayUsuaV, $value['usuared']);
                }

                $varExtensionesArraysV = implode("', '", $varArrayUsuaV);
                $arrayExtensiones_downV = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysV);
                $varExtensionesV = explode(",", $arrayExtensiones_downV);
              }else{
                $varExtensionesV = "N0A";
              }
            }
          }

          $varListCallidVariables = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$varServicioIdealV])
                                ->andwhere(['in','extension',$varExtensionesV])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioV.' 05:00:00',$varFechaIdealFinV.' 05:00:00'])
                                ->groupby(['callid'])
                                ->all();

          $varArrayCallidV = array();
          foreach ($varListCallidVariables as $key => $value) {
            array_push($varArrayCallidV, $value['callid']);
          }
          $varCallidsListV = implode(", ", $varArrayCallidV);
          $arrayCallids_downV = str_replace(array("#", "'", ";", " "), '', $varCallidsListV);
          $varCallidsVar = explode(",", $arrayCallids_downV);

          $varCantidadLlamadasVar = (new \yii\db\Query())
                                ->select(['sum(cantidad)'])
                                ->from(['tbl_ideal_llamadas'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_dp_cliente',$varIdClienteIdealV])
                                ->andwhere(['=','bolsita',$varServicioIdealV])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['in','extension',$varExtensionesV])
                                ->andwhere(['>=','fechainicio',$varFechaIdealInicioV.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaIdealFinV.' 05:00:00'])
                                ->Scalar();

          $varListarIndicadoresV = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['=','idcategorias',1])
                                ->all();

          foreach ($varListarIndicadoresV as $key => $value) {
            $txtIdIndicadores = $value['idcategoria'];
            $varNombreIndicador = $value['nombre'];
            $varTipoParametro = $value['tipoparametro'];
            $txtTipoFormIndicador = $value['orientacionform'];

            $varListVariablesV = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform','responsable'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealV])
                                ->andwhere(['=','idcategorias',2])
                                ->andwhere(['=','tipoindicador',$varNombreIndicador])
                                ->all();

            foreach ($varListVariablesV as $key => $value) {
              $varVariables = $value['idcategoria'];
              $varNombreVariable = $value['nombre'];

              $varConteoPorVariable =  (new \yii\db\Query())
                                          ->select(['callid','SUM(cantproceso)'])
                                          ->from(['tbl_speech_general'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','programacliente',$varServicioIdealV])
                                          ->andwhere(['in','extension',$varExtensionesV])
                                          ->andwhere(['between','fechallamada',$varFechaIdealInicioV.' 05:00:00',$varFechaIdealFinV.' 05:00:00'])
                                          ->andwhere(['in','callid',$varCallidsVar])
                                          ->andwhere(['in','idindicador',$varVariables])
                                          ->andwhere(['in','idvariable',$varVariables])
                                          ->groupby(['callid'])
                                          ->count();

              if ($varConteoPorVariable != 0 && $varCantidadLlamadasVar != 0) {
                if ($txtTipoFormIndicador == 1) {
                  $txtRtaPorcentajeVariable = (round(($varConteoPorVariable / $varCantidadLlamadasVar) * 100, 1));
                }else{
                  $txtRtaPorcentajeVariable = (100 - (round(($varConteoPorVariable / $varCantidadLlamadasVar) * 100, 1)));
                }
              }else{
                $txtRtaPorcentajeVariable = 0;
              }

              $varLlamadasVariable =  (new \yii\db\Query())
                                          ->select(['idcategoria'])
                                          ->from(['tbl_dashboardspeechcalls'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','servicio',$varServicioIdealV])
                                          ->andwhere(['in','extension',$varExtensionesV])
                                          ->andwhere(['between','fechallamada',$varFechaIdealInicioV.' 05:00:00',$varFechaIdealFinV.' 05:00:00'])
                                          ->andwhere(['in','idcategoria',$varVariables])
                                          ->groupby(['callid'])
                                          ->count();

              if ($varTipoParametroV == null) {
                $varTipoParametroV = 0;
              }

              Yii::$app->db->createCommand()->insert('tbl_ideal_variables',[
                      'id_dp_cliente' => $varIdClienteIdealV,
                      'bolsita' => $varServicioIdealV,
                      'cod_pcrc' => $varPcrcCodIdealV,
                      'extension' => $varTipoParametroV,
                      'id_categoria_indicador' => $txtIdIndicadores,
                      'id_categoria_variable' => $varVariables,
                      'variable' => $varNombreVariable,
                      'cantidad_variable' => $varLlamadasVariable,
                      'porcentaje_variable' => $txtRtaPorcentajeVariable,
                      'fechainicio' => $varFechaIdealInicioV.' 05:00:00',
                      'fechafin' => $varFechaIdealFinV.' 05:00:00',
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
              ])->execute();
            }
          }


        }
      }

    }

    public function Registramotivos($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealM = null;
      $varServicioIdealM = null;
      $varFechaIdealInicioM = null;
      $varFechaIdealFinM = null;
      $varListasCodPcrcM = null;

      $varListaItemsIdealM = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealM); $i++) { 
        $varIdClienteIdealM = $varListaItemsIdealM[0];
        $varServicioIdealM = $varListaItemsIdealM[1];
        $varFechaIdealInicioM = $varListaItemsIdealM[2];
        $varFechaIdealFinM = $varListaItemsIdealM[3];
        $varListasCodPcrcM = $varListaItemsIdealM[4];
      }

      $varListaPcrcIdealsM = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrcM));

      $varListaPcrcIdealM = (new \yii\db\Query())
                              ->select(['cod_pcrc'])
                              ->from(['tbl_speech_categorias']) 
                              ->where(['in','cod_pcrc',$varListaPcrcIdealsM])
                              ->andwhere(['=','programacategoria',$varServicioIdealM])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->All();

      foreach ($varListaPcrcIdealM as $key => $value) {
        $varPcrcCodIdealM = $value['cod_pcrc'];

        $varTipoParmsM = (new \yii\db\Query())
                                ->select(['tipoparametro'])
                                ->from(['tbl_speech_parametrizar'])
                                ->where(['=','anulado',0])
                                ->groupby(['tipoparametro'])
                                ->all();

        foreach ($varTipoParmsM as $key => $value) {
          $varTipoParametroM = $value['tipoparametro'];

          if ($varTipoParametroM != null) {
            $varRnIdealM =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealM])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroM])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealM =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealM])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }

          if (count($varRnIdealM) != 0) {
             $varArrayRnM = array();
            foreach ($varRnIdealM as $key => $value) {
              array_push($varArrayRnM, $value['rn']);
            }

            $varExtensionesArraysM = implode("', '", $varArrayRnM);
            $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
            $varExtensionesM = explode(",", $arrayExtensiones_downM);
          }else{

            if ($varTipoParametroM != null) {
              $varExtM =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealM])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroM])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtM =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealM])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtM) != 0) {
              $varArrayExtM = array();
              foreach ($varExtM as $key => $value) {
                array_push($varArrayExtM, $value['ext']);
              }

              $varExtensionesArraysM = implode("', '", $varArrayExtM);
              $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
              $varExtensionesM = explode(",", $arrayExtensiones_downM);
            }else{

              if ($varTipoParametroM != null) {
                $varUsuaM =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealM])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroM])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaM =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealM])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }


              if (count($varUsuaM) != 0) {
                $varArrayUsuaM = array();
                foreach ($varUsuaM as $key => $value) {
                  array_push($varArrayUsuaM, $value['usuared']);
                }

                $varExtensionesArraysM = implode("', '", $varArrayUsuaM);
                $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
                $varExtensionesM = explode(",", $arrayExtensiones_downM);
              }else{
                $varExtensionesM = "N0A";
              }
            }
          }

          $varListCallidMotivos = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_speech_general'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','programacliente',$varServicioIdealM])
                                ->andwhere(['in','extension',$varExtensionesM])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioM.' 05:00:00',$varFechaIdealFinM.' 05:00:00'])
                                ->groupby(['callid'])
                                ->all();

          $varArrayCallidM = array();
          foreach ($varListCallidMotivos as $key => $value) {
            array_push($varArrayCallidM, $value['callid']);
          }
          $varCallidsListM = implode(", ", $varArrayCallidM);
          $arrayCallids_downM = str_replace(array("#", "'", ";", " "), '', $varCallidsListM);
          $varCallidsMotivo = explode(",", $arrayCallids_downM);

          $varCantidadLlamadaMotivo = (new \yii\db\Query())
                                ->select(['sum(cantidad)'])
                                ->from(['tbl_ideal_llamadas'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_dp_cliente',$varIdClienteIdealM])
                                ->andwhere(['=','bolsita',$varServicioIdealM])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealM])
                                ->andwhere(['in','extension',$varExtensionesM])
                                ->andwhere(['>=','fechainicio',$varFechaIdealInicioM.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaIdealFinM.' 05:00:00'])
                                ->Scalar();

          $varListarMotivos = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealM])
                                ->andwhere(['=','idcategorias',3])
                                ->all(); 

          foreach ($varListarMotivos as $key => $value) {
            $varMotivo = $value['idcategoria'];
            $varNombreMotivo = $value['nombre'];

            $varConteoPorMotivos =  (new \yii\db\Query())
                                      ->select(['callid'])
                                      ->from(['tbl_dashboardspeechcalls'])            
                                      ->where(['=','anulado',0])
                                      ->andwhere(['=','servicio',$varServicioIdealM])
                                      ->andwhere(['in','extension',$varExtensionesM])
                                      ->andwhere(['between','fechallamada',$varFechaIdealInicioM.' 05:00:00',$varFechaIdealFinM.' 05:00:00'])
                                      ->andwhere(['in','idcategoria',$varMotivo])
                                      ->groupby(['callid'])
                                      ->count();

            if ($varConteoPorMotivos != 0 && $varCantidadLlamadaMotivo != 0) {
              if ($varConteoPorMotivos != null) {
                $txtRtaPorcentajeMotivo = (round(($varConteoPorMotivos / $varCantidadLlamadaMotivo) * 100, 1));
              }else{
                $txtRtaPorcentajeMotivo = 0;
              }
            }else{
              $txtRtaPorcentajeMotivo = 0;
            }

            if ($varTipoParametroM == null) {
              $varTipoParametroM = 0;
            }

            Yii::$app->db->createCommand()->insert('tbl_ideal_motivos',[
                      'id_dp_cliente' => $varIdClienteIdealM,
                      'bolsita' => $varServicioIdealM,
                      'cod_pcrc' => $varPcrcCodIdealM,
                      'extension' => $varTipoParametroM,
                      'id_categoria_motivo' => $varMotivo,
                      'motivos' => $varNombreMotivo,
                      'cantidad_motivo' => $varConteoPorMotivos,
                      'porcentaje_motivo' => $txtRtaPorcentajeMotivo,
                      'fechainicio' => $varFechaIdealInicioM.' 05:00:00',
                      'fechafin' => $varFechaIdealFinM.' 05:00:00',
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
            ])->execute(); 
          }


        }
      }
    }

    public function Registranovedades($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealN = null;
      $varServicioIdealN = null;
      $varFechaIdealInicioN = null;
      $varFechaIdealFinN = null;
      $varListasCodPcrcN = null;

      $varListaItemsIdealN = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealN); $i++) { 
        $varIdClienteIdealN = $varListaItemsIdealN[0];
        $varServicioIdealN = $varListaItemsIdealN[1];
        $varFechaIdealInicioN = $varListaItemsIdealN[2];
        $varFechaIdealFinN = $varListaItemsIdealN[3];
        $varListasCodPcrcN = $varListaItemsIdealN[4];
      }

      $varListaPcrcIdealsN = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrcN));

      $varGeneralIdealN = (new \yii\db\Query())
                              ->select(['idllamada'])
                              ->from(['tbl_speech_servicios'])            
                              ->where(['=','id_dp_clientes',$varIdClienteIdealN])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['idllamada'])
                              ->Scalar();

      $varListaPcrcIdealN = (new \yii\db\Query())
                              ->select(['cod_pcrc'])
                              ->from(['tbl_speech_categorias']) 
                              ->where(['in','cod_pcrc',$varListaPcrcIdealsN])
                              ->andwhere(['=','programacategoria',$varServicioIdealN])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->All();

      foreach ($varListaPcrcIdealN as $key => $value) {
        $varPcrcCodIdealN = $value['cod_pcrc'];

        $varTipoParmsN = (new \yii\db\Query())
                                ->select(['tipoparametro'])
                                ->from(['tbl_speech_parametrizar'])
                                ->where(['=','anulado',0])
                                ->groupby(['tipoparametro'])
                                ->all();

        foreach ($varTipoParmsN as $key => $value) {
          $varTipoParametroN = $value['tipoparametro'];


          if ($varTipoParametroN != null) {
            $varRnIdealN =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealN])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroN])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealN =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealN])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }


          if (count($varRnIdealN) != 0) {
            $varArrayRnN = array();
            foreach ($varRnIdealN as $key => $value) {
              array_push($varArrayRnN, $value['rn']);
            }

            $varExtensionesArraysN = implode("', '", $varArrayRnN);
            $arrayExtensiones_downN = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysN);
            $varExtensionesN = explode(",", $arrayExtensiones_downN);
          }else{

            if ($varTipoParametroN != null) {
              $varExtN =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealN])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroN])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtN =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealN])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtN) != 0) {
              $varArrayExtN = array();
              foreach ($varExtN as $key => $value) {
                array_push($varArrayExtN, $value['ext']);
              }

              $varExtensionesArraysN = implode("', '", $varArrayExtN);
              $arrayExtensiones_downN = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysN);
              $varExtensionesN = explode(",", $arrayExtensiones_downN);
            }else{

              if ($varTipoParametroN != null) {
                $varUsuaN =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealN])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroN])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaN =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealN])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }


              if (count($varUsuaN) != 0) {
                $varArrayUsuaN = array();
                foreach ($varUsuaN as $key => $value) {
                  array_push($varArrayUsuaN, $value['usuared']);
                }

                $varExtensionesArraysN = implode("', '", $varArrayUsuaN);
                $arrayExtensiones_downN = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysN);
                $varExtensionesN = explode(",", $arrayExtensiones_downN);
              }else{
                $varExtensionesN = "N0A";
              }
            }
          }

          $varListarCallidN  = (new \yii\db\Query())
                                ->select(['callId'])
                                ->from(['tbl_dashboardspeechcalls'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varServicioIdealN])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioN.' 05:00:00',$varFechaIdealFinN.' 05:00:00'])
                                ->andwhere(['=','idcategoria',$varGeneralIdealN])
                                ->andwhere(['IN','extension',$varExtensionesN])
                                ->groupby(['callId'])
                                ->All();

          foreach ($varListarCallidN as $key => $value) {
            $varCallidsN = $value['callId'];
            
            $varFormulariosN = (new \yii\db\Query())
                                ->select(['formulario_id'])
                                ->from(['tbl_speech_mixta'])          
                                ->where(['=','anulado',0])
                                ->andwhere(['=','callid',$varCallidsN])
                                ->Scalar();

            if ($varTipoParametroN == null) {
              $varTipoParametroN = 0;
            }

            if ($varFormulariosN) {
              Yii::$app->db->createCommand()->insert('tbl_ideal_novedades',[
                    'id_dp_cliente' => $varIdClienteIdealN,
                    'bolsita' => $varServicioIdealN,
                    'cod_pcrc' => $varPcrcCodIdealN,
                    'extension' => $varTipoParametroN,
                    'formulario_id' => $varFormulariosN,
                    'fechainicio' => $varFechaIdealInicioN.' 05:00:00',
                    'fechafin' => $varFechaIdealFinN .' 05:00:00',
                    'usua_id' => Yii::$app->user->identity->id,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,                        
                ])->execute();
            }
          }

        }
      }

    }

    public function Registratmpmotivos($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealTmp = null;
      $varServicioIdealTmp = null;
      $varFechaIdealInicioTmp = null;
      $varFechaIdealFinTmp = null;
      $varListasCodPcrcTmp = null;

      $varListaItemsIdealTmp = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealTmp); $i++) { 
        $varIdClienteIdealTmp = $varListaItemsIdealTmp[0];
        $varServicioIdealTmp = $varListaItemsIdealTmp[1];
        $varFechaIdealInicioTmp = $varListaItemsIdealTmp[2];
        $varFechaIdealFinTmp = $varListaItemsIdealTmp[3];
        $varListasCodPcrcTmp = $varListaItemsIdealTmp[4];
      }

      $varListaPcrcIdealsTmp = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrcTmp));

      $varListaPcrcIdealTmp = (new \yii\db\Query())
                              ->select(['cod_pcrc'])
                              ->from(['tbl_speech_categorias']) 
                              ->where(['in','cod_pcrc',$varListaPcrcIdealsTmp])
                              ->andwhere(['=','programacategoria',$varServicioIdealTmp])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->All();


      foreach ($varListaPcrcIdealTmp as $key => $value) {
        $varPcrcCodIdealTmp = $value['cod_pcrc'];

        $varTipoParmsTmp = (new \yii\db\Query())
                                ->select(['tipoparametro'])
                                ->from(['tbl_speech_parametrizar'])
                                ->where(['=','anulado',0])
                                ->groupby(['tipoparametro'])
                                ->all();

        foreach ($varTipoParmsTmp as $key => $value) {
          $varTipoParametroTmp = $value['tipoparametro'];

          if ($varTipoParametroTmp != null) {
            $varRnIdealTmp =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroTmp])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealTmp =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }

          if (count($varRnIdealTmp) != 0) {
            $varArrayRnTmp = array();
            foreach ($varRnIdealTmp as $key => $value) {
              array_push($varArrayRnTmp, $value['rn']);
            }

            $varExtensionesArraysTmp = implode("', '", $varArrayRnTmp);
            $arrayExtensiones_downTmp = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysTmp);
            $varExtensionesTmp = explode(",", $arrayExtensiones_downTmp);
          }else{

            if ($varTipoParametroTmp != null) {
              $varExtTmp =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroTmp])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtTmp =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtTmp) != 0) {
              $varArrayExtTmp = array();
              foreach ($varExtTmp as $key => $value) {
                array_push($varArrayExtTmp, $value['ext']);
              }

              $varExtensionesArraysTmp = implode("', '", $varArrayExtTmp);
              $arrayExtensiones_downTmp = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysTmp);
              $varExtensionesTmp = explode(",", $arrayExtensiones_downTmp);
            }else{

              if ($varTipoParametroTmp != null) {
                $varUsuaTmp =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroTmp])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaTmp =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }

              if (count($varUsuaTmp) != 0) {
                $varArrayUsuaTmp = array();
                foreach ($varUsuaTmp as $key => $value) {
                  array_push($varArrayUsuaTmp, $value['usuared']);
                }

                $varExtensionesArraysTmp = implode("', '", $varArrayUsuaTmp);
                $arrayExtensiones_downTmp = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysTmp);
                $varExtensionesTmp = explode(",", $arrayExtensiones_downTmp);
              }else{
                $varExtensionesTmp = "N0A";
              }
            }
          }

          

          $txtCategoriaGeneralTmp = (new \yii\db\Query())
                                ->select(['idllamada'])
                                ->from(['tbl_speech_servicios']) 
                                ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                    'tbl_speech_servicios.id_dp_clientes = tbl_speech_parametrizar.id_dp_clientes')           
                                ->where(['in','tbl_speech_parametrizar.cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->groupby(['idllamada'])
                                ->Scalar();

          $txtCantidadTmp = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varServicioIdealTmp])
                                ->andwhere(['in','extension',$varExtensionesTmp])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioTmp .' 05:00:00',$varFechaIdealFinTmp .' 05:00:00'])
                                ->andwhere(['=','idcategoria',$txtCategoriaGeneralTmp])  
                                ->groupby(['callid'])   
                                ->count();

          $varListarMotivosTmp = (new \yii\db\Query())
                                ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                ->from(['tbl_speech_categorias'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','idcategorias',3])
                                ->all();

          $varArrayMotivosTmp = array();
          foreach ($varListarMotivosTmp as $key => $value) {
            array_push($varArrayMotivosTmp, $value['idcategoria']);
          }
          $varMotivosListTmp = implode(", ", $varArrayMotivosTmp);
          $arrayMotivos_downTmp = str_replace(array("#", "'", ";", " "), '', $varMotivosListTmp);
          $varMotivosTmp = explode(",", $arrayMotivos_downTmp);

          $varListCallidsTmp = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varServicioIdealTmp])
                                ->andwhere(['in','extension',$varExtensionesTmp])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioTmp .' 05:00:00',$varFechaIdealFinTmp .' 05:00:00'])
                                ->andwhere(['in','idcategoria',$varMotivosTmp])
                                ->groupby(['callid'])                                
                                ->count();

          $varVerificarCantidadLlamadas = (new \yii\db\Query())
                                ->select(['cantidadllamada'])
                                ->from(['tbl_speech_tmpmotivos'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varPcrcCodIdealTmp])
                                ->andwhere(['=','extension',$varTipoParametroTmp])
                                ->andwhere(['=','id_dp_cliente',$varIdClienteIdealTmp])
                                ->groupby(['cantidadllamada'])
                                ->Scalar();

          if ($varVerificarCantidadLlamadas != $varListCallidsTmp) {
            
            foreach ($varListarMotivosTmp as $key => $value) {
              $varIdMotivoVoice = $value['idcategoria'];
              $varNombreMotivoVoice = $value['nombre'];
              $varMotivoIdVoice = intval($varIdMotivoVoice);

              $varConteoPorMotivosVoice =  (new \yii\db\Query())
                                    ->select(['callid'])
                                    ->from(['tbl_dashboardspeechcalls'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','servicio',$varServicioIdealTmp])
                                    ->andwhere(['in','extension',$varExtensionesTmp])
                                    ->andwhere(['between','fechallamada',$varFechaIdealInicioTmp.' 05:00:00',$varFechaIdealFinTmp.' 05:00:00'])
                                    ->andwhere(['=','idcategoria',$varMotivoIdVoice])
                                    ->groupby(['callid'])
                                    ->count();

              $varDuracionLlamadaVoice = (new \yii\db\Query())
                                    ->select(['AVG(callduracion)'])
                                    ->from(['tbl_dashboardspeechcalls'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','servicio',$varServicioIdealTmp])
                                    ->andwhere(['in','extension',$varExtensionesTmp])
                                    ->andwhere(['between','fechallamada',$varFechaIdealInicioTmp.' 05:00:00',$varFechaIdealFinTmp.' 05:00:00'])
                                    ->andwhere(['=','idcategoria',$varMotivoIdVoice])
                                    ->groupby(['callid'])
                                    ->scalar();

              if ($varConteoPorMotivosVoice != 0 && $txtCantidadTmp != 0) {
                if ($varConteoPorMotivosVoice != null) {
                  $txtRtaPorcentajeMotivo = (round(($varConteoPorMotivosVoice / $txtCantidadTmp) * 100, 1));
                }else{
                  $txtRtaPorcentajeMotivo = 0;
                }
              }else{
                $txtRtaPorcentajeMotivo = 0;
              }

              if ($varTipoParametroTmp == null) {
                $varTipoParametroTmp = 0;
              }



              Yii::$app->db->createCommand()->insert('tbl_speech_tmpmotivos',[
                        'id_dp_cliente' => $varIdClienteIdealTmp,
                        'cod_pcrc' => $varPcrcCodIdealTmp,
                        'extension' => $varTipoParametroTmp,
                        'id_motivo' => $varMotivoIdVoice,
                        'motivo' => $varNombreMotivoVoice,
                        'porcentaje' => $txtRtaPorcentajeMotivo,
                        'cantidadmotivos' => $varConteoPorMotivosVoice,
                        'duracionllamada' => $varDuracionLlamadaVoice,
                        'cantidadllamada' => $varListCallidsTmp,
                        'fechainiciotmp' => $varFechaIdealInicioTmp,
                        'fechafintmp' => $varFechaIdealFinTmp,
                        'usua_id' => Yii::$app->user->identity->id,
                        'fechacreacion' => date('Y-m-d'),
                        'anulado' => 0,                        
              ])->execute(); 

              $varListarCategoriasVoice = (new \yii\db\Query())
                                      ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform'])
                                      ->from(['tbl_speech_categorias'])            
                                      ->where(['=','anulado',0])
                                      ->andwhere(['in','cod_pcrc',$varPcrcCodIdealTmp])
                                      ->andwhere(['in','idcategorias',[1,2]])
                                      ->all(); 

              foreach ($varListarCategoriasVoice as $key => $value) {
                $varCategoriaId = intval($value['idcategoria']);
                $varMotivosId = intval($varIdMotivoVoice);
                $varArregloCategoria = [$varCategoriaId,$varMotivosId];

                $varConteoPorMotivosVariable =  (new \yii\db\Query())
                                            ->select(['callid'])
                                            ->from(['tbl_dashboardspeechcalls'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','servicio',$varServicioIdealTmp])
                                            ->andwhere(['in','extension',$varExtensionesTmp])
                                            ->andwhere(['between','fechallamada',$varFechaIdealInicioTmp.' 05:00:00',$varFechaIdealFinTmp.' 05:00:00'])
                                            ->andwhere(['in','idcategoria',$varArregloCategoria])
                                            ->count();

                $varCantidadMotivosVoice =  (new \yii\db\Query())
                                            ->select(['callid'])
                                            ->from(['tbl_dashboardspeechcalls'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','servicio',$varServicioIdealTmp])
                                            ->andwhere(['in','extension',$varExtensionesTmp])
                                            ->andwhere(['between','fechallamada',$varFechaIdealInicioTmp.' 05:00:00',$varFechaIdealFinTmp.' 05:00:00'])
                                            ->andwhere(['=','idcategoria',$varMotivosId])
                                            ->count();

                if ($varConteoPorMotivosVariable != 0 && $txtCantidadTmp != 0 && $varCantidadMotivosVoice != 0) {
                  if ($varConteoPorMotivosVariable != null) {
                    $txtRtaPorcentajeMotivoVariable = (round(($varConteoPorMotivosVariable / $varCantidadMotivosVoice), 1));
                  }else{
                    $txtRtaPorcentajeMotivoVariable = 0;
                  }
                }else{
                  $txtRtaPorcentajeMotivoVariable = 0;
                }

                Yii::$app->db->createCommand()->insert('tbl_speech_tmpmotivosvariables',[
                          'id_dp_cliente' => $varIdClienteIdealTmp,
                          'cod_pcrc' => $varPcrcCodIdealTmp,
                          'extension' => $varTipoParametroTmp,
                          'id_motivo' => $varMotivosId,
                          'id_categoria' => $varCategoriaId,                      
                          'porcentaje' => $txtRtaPorcentajeMotivoVariable,
                          'usua_id' => Yii::$app->user->identity->id,
                          'fechacreacion' => date('Y-m-d'),
                          'anulado' => 0,                        
                ])->execute(); 
              }


            }
          }

        }
      }

    }

    public function Registratmpasesores($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealA = null;
      $varServicioIdealA = null;
      $varFechaIdealInicioA = null;
      $varFechaIdealFinA = null;
      $varListasCodPcrcA = null;

      $varListaItemsIdealA = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealA); $i++) { 
        $varIdClienteIdealA = $varListaItemsIdealA[0];
        $varServicioIdealA = $varListaItemsIdealA[1];
        $varFechaIdealInicioA = $varListaItemsIdealA[2];
        $varFechaIdealFinA = $varListaItemsIdealA[3];
        $varListasCodPcrcA = $varListaItemsIdealA[4];
      }

      $varListaPcrcIdealsA = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrcA));

      $varListaPcrcIdealA = (new \yii\db\Query())
                              ->select(['cod_pcrc'])
                              ->from(['tbl_speech_categorias']) 
                              ->where(['in','cod_pcrc',$varListaPcrcIdealsA])
                              ->andwhere(['=','programacategoria',$varServicioIdealA])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->All();

      foreach ($varListaPcrcIdealA as $key => $value) {
        $varPcrcCodIdealA = $value['cod_pcrc'];

        $varTipoParmsA = (new \yii\db\Query())
                                ->select(['tipoparametro'])
                                ->from(['tbl_speech_parametrizar'])
                                ->where(['=','anulado',0])
                                ->groupby(['tipoparametro'])
                                ->all();

        foreach ($varTipoParmsA as $key => $value) {
          $varTipoParametroA = $value['tipoparametro'];
          if ($varTipoParametroA == null) {
            $varTipoParametroA = 0;
          }

          if ($varTipoParametroA != 0) {
            $varRnIdealA =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealA])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroA])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealA =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealA])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }


          if (count($varRnIdealA) != 0) {
            $varArrayRnA = array();
            foreach ($varRnIdealA as $key => $value) {
              array_push($varArrayRnA, $value['rn']);
            }

            $varExtensionesArraysA = implode("', '", $varArrayRnA);
            $arrayExtensiones_downA = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysA);
            $varExtensionesA = explode(",", $arrayExtensiones_downA);
          }else{

            if ($varTipoParametroA != null) {
              $varExtA =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealA])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroA])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtA =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealA])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtA) != 0) {
              $varArrayExtA = array();
              foreach ($varExtA as $key => $value) {
                array_push($varArrayExtA, $value['ext']);
              }

              $varExtensionesArraysA = implode("', '", $varArrayExtA);
              $arrayExtensiones_downA = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysA);
              $varExtensionesA = explode(",", $arrayExtensiones_downA);
            }else{

              if ($varTipoParametroA != null) {
                $varUsuaA =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealA])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroA])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaA =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcCodIdealA])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }


              if (count($varUsuaA) != 0) {
                $varArrayUsuaA = array();
                foreach ($varUsuaA as $key => $value) {
                  array_push($varArrayUsuaA, $value['usuared']);
                }

                $varExtensionesArraysA = implode("', '", $varArrayUsuaA);
                $arrayExtensiones_downA = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysA);
                $varExtensionesA = explode(",", $arrayExtensiones_downA);
              }else{
                $varExtensionesA = "N0A";
              }
            }
          }
          
          $varCantidadLlamadaAsesores = (new \yii\db\Query())
                                ->select(['sum(cantidad)'])
                                ->from(['tbl_ideal_llamadas'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_dp_cliente',$varIdClienteIdealA])
                                ->andwhere(['=','bolsita',$varServicioIdealA])
                                ->andwhere(['=','cod_pcrc',$varPcrcCodIdealA])
                                ->andwhere(['in','extension',$varExtensionesA])
                                ->andwhere(['>=','fechainicio',$varFechaIdealInicioA.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaIdealFinA.' 05:00:00'])
                                ->Scalar();

          $varListarAsesores = (new \yii\db\Query())
                                ->select(['login_id'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varServicioIdealA])
                                ->andwhere(['in','extension',$varExtensionesA])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioA.' 05:00:00',$varFechaIdealFinA.' 05:00:00'])
                                ->groupby(['login_id'])
                                ->all();

          foreach ($varListarAsesores as $key => $value) {
            $varUsuarioLogin = $value['login_id'];

            $varConteoLoginCallid = (new \yii\db\Query())
                                ->select(['callid'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varServicioIdealA])
                                ->andwhere(['in','extension',$varExtensionesA])
                                ->andwhere(['between','fechallamada',$varFechaIdealInicioA.' 05:00:00',$varFechaIdealFinA.' 05:00:00'])
                                ->andwhere(['=','login_id',$varUsuarioLogin])
                                ->groupby(['callid'])
                                ->all();

            $varConteoLogin = count($varConteoLoginCallid);

            $varindicadorarray = array();
            foreach ($varConteoLoginCallid as $key => $value) {
              $varCallidsAsesores = $value['callid']; 

              $varListarAgentes = (new \yii\db\Query())
                                  ->select(['idcategoria','nombre','tipoparametro','orientacionsmart','orientacionform','responsable'])
                                  ->from(['tbl_speech_categorias'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','cod_pcrc',$varPcrcCodIdealA])
                                  ->andwhere(['=','idcategorias',2])
                                  ->andwhere(['=','responsable',1])
                                  ->all();

              $varlistanegativo = array();
              $varlistapositivo = array();
              $varconteonegativas = 0;
              $varconteopositivas = 0;
              $varconteogeneral = 0;
              foreach ($varListarAgentes as $key => $value) {
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

              $vararrayVarNegativas = implode(", ", $varlistanegativo);
              $arrayCallids_downNegativas = str_replace(array("#", "'", ";", " "), '', $vararrayVarNegativas);
              $varvariablesnegativas = explode(",", $arrayCallids_downNegativas);


              $vararrayVarPositivas = implode(", ", $varlistapositivo);
              $arrayCallids_downPositivas = str_replace(array("#", "'", ";", " "), '', $vararrayVarPositivas);
              $varvariablespositivas = explode(",", $arrayCallids_downPositivas);

              if ($varconteonegativas != 0) {
                $varcontarvarnegativas = (new \yii\db\Query())
                                  ->select(['SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$varServicioIdealA])
                                  ->andwhere(['in','extension',$varExtensionesA])
                                  ->andwhere(['between','fechallamada',$varFechaIdealInicioA.' 05:00:00',$varFechaIdealFinA.' 05:00:00'])
                                  ->andwhere(['=','callId',$varCallidsAsesores])
                                  ->andwhere(['in','idvariable',$varvariablesnegativas])
                                  ->scalar();
              }else{
                $varcontarvarnegativas = 0;
              }

              if ($varconteopositivas != 0) {
                $varcontarvarpositivas = (new \yii\db\Query())
                                  ->select(['SUM(cantproceso)'])
                                  ->from(['tbl_speech_general'])            
                                  ->where(['=','anulado',0])
                                  ->andwhere(['=','programacliente',$varServicioIdealA])
                                  ->andwhere(['in','extension',$varExtensionesA])
                                  ->andwhere(['between','fechallamada',$varFechaIdealInicioA.' 05:00:00',$varFechaIdealFinA.' 05:00:00'])
                                  ->andwhere(['in','callId',$varCallidsAsesores])
                                  ->andwhere(['in','idvariable',$varvariablespositivas])
                                  ->scalar();
              }else{
                $varcontarvarpositivas = 0;
              }

              if ($varconteonegativas != 0 && $varcontarvarnegativas != 0 && $varcontarvarpositivas != 0 && $varconteogeneral != 0) {
                $varResultado = (($varconteonegativas - $varcontarvarnegativas) + $varcontarvarpositivas) / $varconteogeneral;
              }else{
                $varResultado = 0;
              }

              

              array_push($varindicadorarray, $varResultado);
            }

            $resultadosIDA = round((array_sum($varindicadorarray) / $varConteoLogin) * 100,2);
            

            $paramsBuscarLogin = [':vaLoginId'=>$varUsuarioLogin];

            if (is_numeric($varUsuarioLogin)) {
              $varDocumentoAsesor = Yii::$app->get('dbjarvis2')->createCommand('
                SELECT documento FROM dp_usuarios_actualizacion 
                  WHERE 
                    documento = (:vaLoginId) 
                GROUP BY  documento')->bindValues($paramsBuscarLogin)->queryScalar();

              if (!$varDocumentoAsesor) {
                $varDocumentoAsesor = (new \yii\db\Query())
                                  ->select(['identificacion'])
                                  ->from(['tbl_evaluados'])
                                  ->where(['=','identificacion',$varUsuarioLogin])
                                  ->Scalar();
              }
            }else{
              $varDocumentoAsesor = Yii::$app->get('dbjarvis2')->createCommand('
                SELECT documento FROM dp_usuarios_actualizacion 
                  WHERE 
                    usuario = (:vaLoginId) 
                GROUP BY  documento')->bindValues($paramsBuscarLogin)->queryScalar();

              if (!$varDocumentoAsesor) {
                $varDocumentoAsesor = (new \yii\db\Query())
                                  ->select(['identificacion'])
                                  ->from(['tbl_evaluados'])
                                  ->where(['=','dsusuario_red',$varUsuarioLogin])
                                  ->Scalar();
              }
            }

            $varIdUsuaLider = 0;
            $varIdEvaluado = 0;

            if ($varDocumentoAsesor) {
              $varListaDataAsesor = (new \yii\db\Query())
                                  ->select(['tbl_usuarios.usua_id AS Lider', 'tbl_evaluados.id AS Asesor'])
                                  ->from(['tbl_usuarios'])   
                                  ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                      'tbl_usuarios.usua_id = tbl_equipos.usua_id') 

                                  ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                      'tbl_equipos.id = tbl_equipos_evaluados.equipo_id') 

                                  ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                      'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id') 

                                  ->where(['=','tbl_evaluados.identificacion',$varDocumentoAsesor])
                                  ->All();

              foreach ($varListaDataAsesor as $key => $value) {
                $varIdUsuaLider = $value['Lider'];
                $varIdEvaluado = $value['Asesor'];
              }

              
            }

            Yii::$app->db->createCommand()->insert('tbl_ideal_tmpasesores',[
                      'id_dp_cliente' => $varIdClienteIdealA,
                      'cod_pcrc' => $varPcrcCodIdealA,
                      'extension' => $varTipoParametroA,
                      'usua_id_lider' => $varIdUsuaLider,
                      'evaluado_id' => $varIdEvaluado,
                      'login_id' => $varUsuarioLogin,
                      'porcentaje' => $resultadosIDA,
                      'totalllamadas' => $varConteoLogin,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
              ])->execute();
          }

        }
      }

    }

    public function Registratmplogin($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealtL = null;
      $varServicioIdealtL = null;
      $varFechaIdealIniciotL = null;
      $varFechaIdealFintL = null;

      $varListaItemsIdealtL = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealtL); $i++) { 
        $varIdClienteIdealtL = $varListaItemsIdealtL[0];
        $varServicioIdealtL = $varListaItemsIdealtL[1];
        $varFechaIdealIniciotL = $varListaItemsIdealtL[2];
        $varFechaIdealFintL = $varListaItemsIdealtL[3];
      }

      $varListaLoginid = (new \yii\db\Query())
                                ->select(['login_id'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varServicioIdealtL])
                                ->andwhere(['between','fechallamada',$varFechaIdealIniciotL.' 05:00:00',$varFechaIdealFintL.' 05:00:00'])
                                ->groupby(['login_id'])
                                ->all();

      


      

      foreach ($varListaLoginid as $key => $value) {
        $varLogin_idtL = $value['login_id'];

        $paramsBuscaAsesor = [':LoginAsesor'=>$varLogin_idtL];


        if (is_numeric($varLogin_idtL)) {
          
          $varUsuaAsesor = Yii::$app->dbjarvis->createCommand('
              SELECT du.documento FROM  dp_usuarios_red du 
              WHERE 
                du.documento = :LoginAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();

        }else{
          
          $varUsuaAsesor = Yii::$app->dbjarvis->createCommand('
              SELECT du.documento FROM  dp_usuarios_red du 
              WHERE 
                du.usuario_red = :LoginAsesor ')->bindValues($paramsBuscaAsesor)->queryScalar();

        }

        if ($varUsuaAsesor == null) {
          $varUsuaAsesor = Yii::$app->get('dbjarvis2')->createCommand('
            SELECT ur.documento FROM dp_usuarios_red ur 
              INNER JOIN dp_usuarios_actualizacion ua ON  
                ur.documento = ua.documento  
              WHERE 
                ua.usuario = :LoginAsesor
              GROUP  BY  ua.usuario')->bindValues($paramsBuscaAsesor)->queryScalar();
        }

        $varIdLider = null;
        if ($varUsuaAsesor != null) {
          
          $varIdAsesor = (new \yii\db\Query())
                                ->select(['tbl_evaluados.id'])
                                ->from(['tbl_evaluados'])            
                                ->where(['=','tbl_evaluados.identificacion',$varUsuaAsesor])
                                ->scalar();

          $varIdLider = (new \yii\db\Query())
                                ->select(['tbl_equipos.usua_id'])
                                ->from(['tbl_equipos'])     

                                ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                      'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')

                                ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                      'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')

                                ->where(['=','tbl_evaluados.id',$varIdAsesor])
                                ->scalar();

        }else{
          $varIdAsesor = 0;
        }

        Yii::$app->db->createCommand()->insert('tbl_ideal_tmploginid',[
                      'id_dp_cliente' => $varIdClienteIdealtL,
                      'bolsita' => $varServicioIdealtL,
                      'usua_id_lider' => $varIdLider,
                      'evaluado_id' => $varIdAsesor,
                      'login_id' => $varLogin_idtL,
                      'fechainicio' => $varFechaIdealIniciotL.' 05:00:00',
                      'fechafin' => $varFechaIdealFintL.' 05:00:00',
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
        ])->execute();
      }

      

    }

    public function Registralistaresponsable($varConcatenarIdeal){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteIdealL = null;
      $varServicioIdealL = null;
      $varFechaIdealInicioL = null;
      $varFechaIdealFinL = null;
      $varListasCodPcrcL = null;

      $varListaItemsIdealL = explode("; ", $varConcatenarIdeal);
      for ($i=0; $i < count($varListaItemsIdealL); $i++) { 
        $varIdClienteIdealL = $varListaItemsIdealL[0];
        $varServicioIdealL = $varListaItemsIdealL[1];
        $varFechaIdealInicioL = $varListaItemsIdealL[2];
        $varFechaIdealFinL = $varListaItemsIdealL[3];
        $varListasCodPcrcL = $varListaItemsIdealL[4];
      }

      $varListaPcrcIdealsL = explode(",", str_replace(array("#", "'", ";", " "), '', $varListasCodPcrcL));

      $varListaPcrcIdealL = (new \yii\db\Query())
                              ->select(['cod_pcrc'])
                              ->from(['tbl_speech_categorias']) 
                              ->where(['in','cod_pcrc',$varListaPcrcIdealsL])
                              ->andwhere(['=','programacategoria',$varServicioIdealL])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->All();

      $varLlamadaIdeal = (new \yii\db\Query())
                            ->select(['idllamada'])
                            ->from(['tbl_speech_servicios'])            
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_dp_clientes',$varIdClienteIdealL])
                            ->Scalar();

      
      foreach ($varListaPcrcIdealL as $key => $value) {
        $varCodpcrcidealL = $value['cod_pcrc'];

        $varCodServicio = (new \yii\db\Query())
                              ->select(['programacategoria'])
                              ->from(['tbl_speech_categorias']) 
                              ->where(['=','cod_pcrc',$varCodpcrcidealL])
                              ->andwhere(['=','anulado',0])
                              ->groupby(['cod_pcrc'])
                              ->scalar();

        $varTipoParmsL = (new \yii\db\Query())
                                ->select(['tipoparametro'])
                                ->from(['tbl_speech_parametrizar'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','cod_pcrc',$varCodpcrcidealL])
                                ->groupby(['tipoparametro'])
                                ->all();

        foreach ($varTipoParmsL as $key => $value) {
          $varParametroTipoL = $value['tipoparametro'];

          if ($varParametroTipoL != null) {
            $varRnIdealL =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcidealL])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varParametroTipoL])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealL =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcidealL])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }


          if (count($varRnIdealL) != 0) {
            $varArrayRnL = array();
            foreach ($varRnIdealL as $key => $value) {
              array_push($varArrayRnL, $value['rn']);
            }

            $varExtensionesArraysL = implode("', '", $varArrayRnL);
            $arrayExtensiones_downL = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysL);
            $varExtensionesL = explode(",", $arrayExtensiones_downL);
          }else{
            if ($varParametroTipoL != null) {
              $varExtL =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcidealL])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varParametroTipoL])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtL =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcidealL])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtL) != 0) {
              $varArrayExtL = array();
              foreach ($varExtL as $key => $value) {
                array_push($varArrayExtL, $value['ext']);
              }

              $varExtensionesArraysL = implode("', '", $varArrayExtL);
              $arrayExtensiones_downL = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysL);
              $varExtensionesL = explode(",", $arrayExtensiones_downL);
            }else{
              if ($varParametroTipoL != null) {
                $varUsuaL =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcidealL])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varParametroTipoL])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaL =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varCodpcrcidealL])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }

              if (count($varUsuaL) != 0) {
                $varArrayUsuaAL = array();
                foreach ($varUsuaL as $key => $value) {
                  array_push($varArrayUsuaAL, $value['usuared']);
                }

                $varExtensionesArraysL = implode("', '", $varArrayUsuaAL);
                $arrayExtensiones_downL = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysL);
                $varExtensionesL = explode(",", $arrayExtensiones_downL);
              }else{
                $varExtensionesL = 'NA';
              }
            }
          }

          if ($varParametroTipoL == null) {
            $varParametroTipoL = 0;
          }


          $varListadoAsesoresL = (new \yii\db\Query())
                          ->select(['tbl_dashboardspeechcalls.login_id'])
                          ->from(['tbl_dashboardspeechcalls'])            
                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varCodServicio])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesL])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechaIdealInicioL.' 05:00:00',$varFechaIdealFinL.' 05:00:00'])
                          ->groupby(['tbl_dashboardspeechcalls.login_id'])
                          ->all();

          $varArrayCantidadSpeechMixta = (new \yii\db\Query())
                          ->select(['tbl_dashboardspeechcalls.callid'])
                          ->from(['tbl_dashboardspeechcalls'])            
                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varCodServicio])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesL])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechaIdealInicioL.' 05:00:00',$varFechaIdealFinL.' 05:00:00'])
                          ->groupby(['tbl_dashboardspeechcalls.callid'])
                          ->count();

          foreach ($varListadoAsesoresL as $key => $value) {
            $varLoginL = $value['login_id'];

            $varIdLogin = (new \yii\db\Query())
                          ->select(['evaluado_id'])
                          ->from(['tbl_ideal_tmploginid'])            
                          ->where(['=','anulado',0])
                          ->andwhere(['=','login_id',$varLoginL])
                          ->scalar();

            $varCallidL = (new \yii\db\Query())
                          ->select(['tbl_dashboardspeechcalls.callid'])
                          ->from(['tbl_dashboardspeechcalls'])            
                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varCodServicio])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesL])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechaIdealInicioL.' 05:00:00',$varFechaIdealFinL.' 05:00:00'])
                          ->andwhere(['=','tbl_dashboardspeechcalls.login_id',$varLoginL])
                          ->groupby(['tbl_dashboardspeechcalls.callid'])
                          ->all();

            $varArrayCallidL = array();
            foreach ($varCallidL as $key => $value) {
              array_push($varArrayCallidL, $value['callid']);
            }
            $varCallidsListMixtosL = implode(", ", $varArrayCallidL);
            $arrayCallids_downMixtosL = str_replace(array("#", "'", ";", " "), '', $varCallidsListMixtosL);
            $varCallidsIndicadoresMixtosL = explode(",", $arrayCallids_downMixtosL);

            $txtCantidadAgenteMixto = (new \yii\db\Query())
                                    ->select(['tbl_speech_general.callid'])
                                    ->from(['tbl_speech_categorias'])  

                                    ->join('LEFT OUTER JOIN', 'tbl_speech_general',
                                          'tbl_speech_categorias.idcategoria = tbl_speech_general.idvariable')   

                                    ->where(['=','tbl_speech_general.anulado',0])
                                    ->andwhere(['=','tbl_speech_general.programacliente',$varCodServicio])
                                    ->andwhere(['in','tbl_speech_general.extension',$varExtensionesL])
                                    ->andwhere(['between','tbl_speech_general.fechallamada',$varFechaIdealInicioL.' 05:00:00',$varFechaIdealFinL.' 05:00:00'])
                                    ->andwhere(['in','tbl_speech_general.callid',$varCallidsIndicadoresMixtosL])
                                    ->andwhere(['=','tbl_speech_categorias.cod_pcrc',$varCodpcrcidealL])
                                    ->andwhere(['=','tbl_speech_categorias.responsable',1])
                                    ->groupby(['callid'])
                                    ->count();

            if ($txtCantidadAgenteMixto != 0 && $varArrayCantidadSpeechMixta != 0) {
              $varTotalAgentesMixto = round((100 - (($txtCantidadAgenteMixto / $varArrayCantidadSpeechMixta) * 100)),2);
            }else{
              $varTotalAgentesMixto = 0;
            }

            $txtCantidadMarcaMixto = (new \yii\db\Query())
                                    ->select(['tbl_speech_general.callid'])
                                    ->from(['tbl_speech_categorias'])  

                                    ->join('LEFT OUTER JOIN', 'tbl_speech_general',
                                          'tbl_speech_categorias.idcategoria = tbl_speech_general.idvariable')   

                                    ->where(['=','tbl_speech_general.anulado',0])
                                    ->andwhere(['=','tbl_speech_general.programacliente',$varCodServicio])
                                    ->andwhere(['in','tbl_speech_general.extension',$varExtensionesL])
                                    ->andwhere(['between','tbl_speech_general.fechallamada',$varFechaIdealInicioL.' 05:00:00',$varFechaIdealFinL.' 05:00:00'])
                                    ->andwhere(['in','tbl_speech_general.callid',$varCallidsIndicadoresMixtosL])
                                    ->andwhere(['=','tbl_speech_categorias.cod_pcrc',$varCodpcrcidealL])
                                    ->andwhere(['=','tbl_speech_categorias.responsable',3])
                                    ->groupby(['callid'])
                                    ->count();

            if ($txtCantidadMarcaMixto != 0 && $varArrayCantidadSpeechMixta != 0) {
              $varTotalMarcaMixto = round((100 - (($txtCantidadMarcaMixto / $varArrayCantidadSpeechMixta) * 100)),2);
            }else{
              $varTotalMarcaMixto = 0;
            }

            $txtCantidadCanalMixto = (new \yii\db\Query())
                                    ->select(['tbl_speech_general.callid'])
                                    ->from(['tbl_speech_categorias'])  

                                    ->join('LEFT OUTER JOIN', 'tbl_speech_general',
                                          'tbl_speech_categorias.idcategoria = tbl_speech_general.idvariable')   

                                    ->where(['=','tbl_speech_general.anulado',0])
                                    ->andwhere(['=','tbl_speech_general.programacliente',$varCodServicio])
                                    ->andwhere(['in','tbl_speech_general.extension',$varExtensionesL])
                                    ->andwhere(['between','tbl_speech_general.fechallamada',$varFechaIdealInicioL.' 05:00:00',$varFechaIdealFinL.' 05:00:00'])
                                    ->andwhere(['in','tbl_speech_general.callid',$varCallidsIndicadoresMixtosL])
                                    ->andwhere(['=','tbl_speech_categorias.cod_pcrc',$varCodpcrcidealL])
                                    ->andwhere(['=','tbl_speech_categorias.responsable',2])
                                    ->groupby(['callid'])
                                    ->count();
                                      
            if ($txtCantidadCanalMixto != 0 && $varArrayCantidadSpeechMixta != 0) {
              $varTotalCanalMixto = round((100 - (($txtCantidadCanalMixto / $varArrayCantidadSpeechMixta) * 100)),2);
            }else{
              $varTotalCanalMixto = 0;
            }

            Yii::$app->db->createCommand()->insert('tbl_ideal_tmpasesores',[
                      'id_dp_cliente' => intval($varIdClienteIdealL),
                      'cod_pcrc' => $varCodpcrcidealL,
                      'extension' => intval($varParametroTipoL),
                      'evaluado_id' => intval($varIdLogin),
                      'login_id' => $varLoginL,
                      'porcentajeagente' => $varTotalAgentesMixto,
                      'porcentajemarca' => $varTotalMarcaMixto,
                      'porcentajecanal' => $varTotalCanalMixto,
                      'usua_id' => Yii::$app->user->identity->id,
                      'fechainicio' => $varFechaIdealInicioL.' 05:00:00',
                      'fechafin' => $varFechaIdealFinL.' 05:00:00',
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                        
            ])->execute();

          }
          
        }        

      }


    }

    public function actionActualizacomdata(){
      $model = new SpeechCategorias();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varFechaEspecial_BD = explode(" ", $model->fechacreacion);

        $varFechaInicioEspecial_BD = $varFechaEspecial_BD[0];
        $varFechaFinEspecial_BD = date('Y-m-d',strtotime($varFechaEspecial_BD[2]));

        $varClienteEspecial_BD = $model->tipoparametro;
        $varListaPcrcEspecial_BD = $model->cod_pcrc;

        $varConcatenarLlamadasEspecial_BD = $varClienteEspecial_BD.'; '.$varListaPcrcEspecial_BD.'; '.$varFechaInicioEspecial_BD.'; '.$varFechaFinEspecial_BD;

        $this->Actualizallamadasspeechespecial_comdata($varConcatenarLlamadasEspecial_BD);
        
        return $this->redirect('actualizarllamadas');
      }

      return $this->renderAjax('actualizacomdata',[
        'model' => $model,
      ]);
    }

    public function Actualizallamadasspeechespecial_comdata($varConcatenarLlamadasEspecial_BD){
      ini_set("max_execution_time", "900");
      ini_set("memory_limit", "1024M");
      ini_set( 'post_max_size', '1024M' );

      ignore_user_abort(true);
      set_time_limit(900);

      $varIdClienteLlamadaEspecial_BD = null;
      $varListaPcrcLlamadaEspecial_BD = null;
      $varFechaInicioLlamadaEspecial_BD = null;
      $varFechaFinLlamadaEspecial_BD = null;

      $varListaItemsLlamadaEspecial_BD = explode("; ", $varConcatenarLlamadasEspecial_BD);
      for ($i=0; $i < count($varListaItemsLlamadaEspecial_BD); $i++) { 
        $varIdClienteLlamadaEspecial_BD = $varListaItemsLlamadaEspecial_BD[0];
        $varListaPcrcLlamadaEspecial_BD = $varListaItemsLlamadaEspecial_BD[1];

        $varFechaInicioLlamadaEspecial_BD = $varListaItemsLlamadaEspecial_BD[2];
        $varFechaFinLlamadaEspecial_BD = $varListaItemsLlamadaEspecial_BD[3];
        
      }

      $arrayListPcrcLlamadaEspeciales = explode(",", str_replace(array("#", "'", ";", " "), '', $varListaPcrcLlamadaEspecial_BD));

      $varListaProyecto_BD = (new \yii\db\Query())
                                ->select([
                                    '*'
                                ])
                                ->from(['tbl_comdata_parametrizarapi'])
                                ->where(['=','tbl_comdata_parametrizarapi.anulado',0])
                                ->andwhere(['=','tbl_comdata_parametrizarapi.id_dp_clientes',$varIdClienteLlamadaEspecial_BD])
                                ->andwhere(['in','tbl_comdata_parametrizarapi.cod_pcrc',$arrayListPcrcLlamadaEspeciales])
                                ->all();

      $varIdProyecto_BD = null;
      $varDataSetId_BD = null;
      $varTableId_BD = null;
      $varLimitId_BD = null;
      $varOffsetId_BD = null;   
      $varCliente_BD = null; 
      foreach ($varListaProyecto_BD as $key => $value) {
        $varIdProyecto_BD = $value['proyecto_id'];
        $varDataSetId_BD = $value['dataset_id'];
        $varTableId_BD = $value['table_id'];
        $varLimitId_BD = $value['limit'];
        $varOffsetId_BD = $value['offset'];

        $varCodpcrc = $value['cod_pcrc'];

        $varExtensionComdata = $value['extension'];

        $varHoraInicio = (new \yii\db\Query())
                          ->select([
                            'if(tbl_speech_pcrcsociedades.id_sociedad=5," 00:00:00"," 05:00:00") AS varTiempoInicio'
                          ])
                          ->from(['tbl_speech_pcrcsociedades'])            
                          ->where(['in','cod_pcrc',$varCodpcrc])
                          ->andwhere(['=','anulado',0])
                          ->Scalar();

        $varHoraFinal = (new \yii\db\Query())
                          ->select([
                            'if(tbl_speech_pcrcsociedades.id_sociedad=5," 23:59:59"," 05:00:00") AS varTiempoInicio'
                          ])
                          ->from(['tbl_speech_pcrcsociedades'])            
                          ->where(['in','cod_pcrc',$varCodpcrc])
                          ->andwhere(['=','anulado',0])
                          ->Scalar();

        ob_start();

        $curl = curl_init();

        curl_setopt_array($curl, array(
          CURLOPT_SSL_VERIFYPEER=> false,
          CURLOPT_SSL_VERIFYHOST => false,
          CURLOPT_URL => 'https://wia-web-api-gw-5j8fyx1b.uc.gateway.dev/conectionDateCXM?key=AIzaSyClC9KoixrqyM3CcO24a29OI3u4e3Vzv4c',
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => '',
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 0,
          CURLOPT_FOLLOWLOCATION => true,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => 'POST',
          CURLOPT_POSTFIELDS => 'proyectID='.$varIdProyecto_BD.'&datasetId='.$varDataSetId_BD.'&tableId='.$varTableId_BD.'&limit='.$varLimitId_BD.'&offset='.$varOffsetId_BD.'&fecha_inicial='.$varFechaInicioLlamadaEspecial_BD.'&fecha_final='.$varFechaFinLlamadaEspecial_BD.'',
          CURLOPT_HTTPHEADER => array(
            'Content-Type: application/x-www-form-urlencoded'
          ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
              
        ob_clean();
        
        $objet_json = json_decode($response,true);



        foreach ($objet_json as $key => $value) {
           $varCambiaFechas = str_replace("Z", "", str_replace("T", " ", strval($value['fechallamada']['value'])));

          $varFechas = date('Y-m-d h:i:s:000',strtotime($varCambiaFechas));

          if (is_numeric($value['idredbox'])) {
            $varIdRedBox = $value['idredbox'];
          }else{
            $varIdRedBox = 'WIA_SAE';
          }

          $varGrabadora = (new \yii\db\Query())
                                ->select([
                                    'tbl_speech_urlgrabadoras.id_grabadora'
                                ])
                                ->from(['tbl_speech_urlgrabadoras'])
                                ->where(['=','tbl_speech_urlgrabadoras.anulado',0])
                                ->andwhere(['=','tbl_speech_urlgrabadoras.ipgrabadora',$value['idgrabadora']])
                                ->scalar();
          if ($varGrabadora == "") {
            $varGrabadora = $value['idgrabadora'];
          }

          
          Yii::$app->db->createCommand()->insert('tbl_dashboardspeechcalls',[
                'callId' => $value['callid'],
                'idcategoria' => $value['idcategoria'],
                'nombreCategoria' => $value['nombrecategoria'],
                'extension' => $value['extension'],
                'login_id' => $value['login_id'],
                'fechallamada' => $varFechas,
                'callduracion' => $value['callduracion'],
                'servicio' => $value['servicio'],
                'fechareal' => $varFechas,
                'idredbox' => $varIdRedBox,
                'idgrabadora' => $varGrabadora,
                'connid' => $value['connid'],
                'extensiones' => 'NA',
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
          ])->execute();

        }
        
        foreach ($objet_json as $key => $value) {
          $varGrabadora_count = (new \yii\db\Query())
                                  ->select([
                                      'tbl_speech_urlgrabadoras.id_grabadora'
                                  ])
                                  ->from(['tbl_speech_urlgrabadoras'])
                                  ->where(['=','tbl_speech_urlgrabadoras.anulado',0])
                                  ->andwhere(['=','tbl_speech_urlgrabadoras.ipgrabadora',$value['idgrabadora']])
                                  ->scalar();

          if ($varGrabadora_count == "") {
            Yii::$app->db->createCommand()->insert('tbl_comdata_llamadaurl',[
                    'callid' => $value['callid'],
                    'id_dp_clientes' => $varIdClienteLlamadaEspecial_BD,
                    'idredbox' => $value['idredbox'],
                    'servicio' => $value['servicio'],
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,
            ])->execute();
          }
            
        }

        $varBolsitaCX_String = (new \yii\db\Query())
                              ->select(['tbl_speech_categorias.programacategoria'])
                              ->from(['tbl_speech_categorias'])
                              ->where(['=','tbl_speech_categorias.anulado',0])
                              ->andwhere(['in','tbl_speech_categorias.cod_pcrc',$arrayListPcrcLlamadaEspeciales])
                              ->groupby(['tbl_speech_categorias.programacategoria'])
                              ->scalar(); 

        
        $varListaInteracciones_Dash = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_dashboardspeechcalls'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','servicio',$varBolsitaCX_String])
                                ->andwhere(['between','fechallamada',$varFechaInicioLlamadaEspecial_BD.$varHoraInicio,$varFechaFinLlamadaEspecial_BD.$varHoraFinal])
                                ->andwhere(['=','extension',$varExtensionComdata])
                                ->groupby(['callid'])
                                ->all();

        foreach ($varListaInteracciones_Dash as $key => $value) {
          Yii::$app->db->createCommand()->insert('tbl_dashboardspeechcalls',[
                'callId' => $value['callId'],
                'idcategoria' => 1114,
                'nombreCategoria' => 'CATEGORÍAS GENERALES',
                'extension' => $value['extension'],
                'login_id' => $value['login_id'],
                'fechallamada' => $value['fechallamada'],
                'callduracion' => $value['callduracion'],
                'servicio' => $value['servicio'],
                'fechareal' => $value['fechareal'],
                'idredbox' => $value['idredbox'],
                'idgrabadora' => $value['idgrabadora'],
                'connid' => $value['connid'],
                'extensiones' => 'NA',
                'fechacreacion' => date('Y-m-d'),
                'anulado' => 0,
          ])->execute();
        }

        $varFechaInicio_General = $varFechaInicioLlamadaEspecial_BD.$varHoraInicio;
        $varFechaFin_General = $varFechaFinLlamadaEspecial_BD.$varHoraFinal;

        $varListaCategorizacion = Yii::$app->db->createCommand("
            SELECT * FROM 
              (
                SELECT llama.callid, llama.extension, llama.fechallamada, llama.servicio, llama.idcategoria AS llamacategoria, cate.idcategoria AS catecategoria, if(llama.idcategoria = cate.idcategoria, 1, 0) AS encuentra, llama.nombreCategoria 
                FROM tbl_dashboardspeechcalls llama 
                  LEFT JOIN 
                    (
                      SELECT idcategoria, tipoindicador, programacategoria, cod_pcrc 
                        FROM tbl_speech_categorias 
                          WHERE anulado = 0 AND idcategorias = 2 
                            AND programacategoria IN ('$varBolsitaCX_String') 
                        ORDER BY cod_pcrc, tipoindicador
                    ) cate ON llama.servicio = cate.programacategoria 
                WHERE llama.servicio IN ('$varBolsitaCX_String') 
                  AND llama.extension = '$varExtensionComdata' 
                    AND llama.fechallamada BETWEEN '$varFechaInicio_General' AND '$varFechaFin_General' 
                GROUP BY llama.callid, llama.extension, llama.idcategoria, cate.idcategoria  
                  ORDER BY encuentra DESC
              ) datos 
            WHERE llamacategoria = catecategoria")->queryAll();


        if (count($varListaCategorizacion) != 0) {
          foreach ($varListaCategorizacion as $key => $value) {
            Yii::$app->db->createCommand()->insert('tbl_speech_general',[
                                                           'programacliente' => $value['servicio'],
                                                           'fechainicio' => date('Y-m-01'),
                                                           'fechafin' => NULL,
                                                           'callid' => $value['callid'],
                                                           'fechallamada' => $value['fechallamada'],
                                                           'extension' => $value['extension'],
                                                           'idindicador' => $value['llamacategoria'],
                                                           'idvariable' => $value['catecategoria'],
                                                           'cantproceso' => $value['encuentra'],
                                                           'fechacreacion' => date('Y-m-d'),
                                                           'anulado' => 0,
                                                           'usua_id' => Yii::$app->user->identity->id,
                                                           'arbol_id' => $varIdClienteLlamadaEspecial_BD,
                                                        ])->execute();
          }
        }

        
      }    
    }

  }

?>
