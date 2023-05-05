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
use app\models\ProcesosAdministrador;
use app\models\Categoriafeedbacks;
use app\models\Tipofeedbacks;
use app\models\Dashboardpermisos;
use app\models\BaseUsuariosip;
use app\models\FormUploadtigo;
use app\models\BaseSatisfaccion; 
use app\models\ControlProcesos;
use app\models\Equipos;
use app\models\ControlParams;
use app\models\IdealServicios;
use app\models\SpeechServicios;
use app\models\ProcesosClienteCentrocosto;
use app\models\SpeechParametrizar;
use \yii\base\Exception;

  class ProcesosqysController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','buscarporpersona','buscarporproceso','indexporpersona','indexporproceso'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerexterno();
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

        return $this->render('index');
    }

    public function actionBuscarporpersona(){
      $model = new ProcesosClienteCentrocosto();

      $varextensiones = ['0' => 'Procesos', '1' => 'Calidad de Entrenamiento', '2' => 'Ojt'];

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varDirectorPersona = $model->documento_director;
        $varFechaPersona = explode(" ", $model->fechacreacion);
        $varExtensionPersona  = $model->anulado;
        $varGerentesPersona = $model->documento_gerente;

        $varFechasIniciosP = $varFechaPersona[0];
        $varFechasFinsP = date('Y-m-d',strtotime($varFechaPersona[2]."+ 1 days")); 

        return $this->redirect(array('indexporpersona','varDirectoresP'=>$varDirectorPersona,'varIdExtension'=>$varExtensionPersona,'varGerentesP'=>$varGerentesPersona,'varFechainicial'=>$varFechasIniciosP,'varFechaFinal'=>$varFechasFinsP));
      }

      return $this->renderAjax('buscarporpersona',[
        'model' => $model,
        'varextensiones' => $varextensiones,
      ]);
    }

    public function actionIndexporpersona($varDirectoresP,$varIdExtension,$varGerentesP,$varFechainicial,$varFechaFinal){     

      $varListaExtServicios = null;

      $arrayGerentes_downM = str_replace(array("#", "'", ";", " "), '', $varGerentesP);
      $varListGerentes = explode(",", $arrayGerentes_downM);

      $varListaServicios = (new \yii\db\Query())
                                ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes','tbl_proceso_cliente_centrocosto.cliente'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['=','tbl_proceso_cliente_centrocosto.documento_director',$varDirectoresP])
                                ->andwhere(['in','tbl_proceso_cliente_centrocosto.documento_gerente',$varListGerentes])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                ->all();

      $arrayListsServicios = array();
      foreach ($varListaServicios as $key => $value) {
        array_push($arrayListsServicios, $value['id_dp_clientes']);
      }
      $varImplodeServicios = implode(", ", $arrayListsServicios);
      $arrayServiciosListas_downM = str_replace(array("#", "'", ";", " "), '', $varImplodeServicios);
      $varListadoServicios = explode(",", $arrayServiciosListas_downM);

      $varValida = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_ideal_llamadas'])            
                                ->where(['in','tbl_ideal_llamadas.id_dp_cliente',$varListadoServicios])
                                ->andwhere(['=','tbl_ideal_llamadas.tipoextension',intval($varIdExtension)])
                                ->andwhere(['>=','tbl_ideal_llamadas.fechainicio',$varFechainicial.' 05:00:00'])
                                ->andwhere(['<=','tbl_ideal_llamadas.fechafin',$varFechaFinal.' 05:00:00'])
                                ->count();

      if ($varValida == 0) {
        return $this->redirect('informacion');
      }

      $varListaNombresGerentes = (new \yii\db\Query())
                                ->select(['tbl_proceso_cliente_centrocosto.gerente_cuenta','tbl_proceso_cliente_centrocosto.cliente'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['in','tbl_proceso_cliente_centrocosto.documento_gerente',$varListGerentes])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                ->groupby(['tbl_proceso_cliente_centrocosto.gerente_cuenta'])
                                ->all();

      $varTextoDimensionp = null;
      $varIdExtensionc = $varIdExtension;
      $varDimensionesId = null;
      if ($varIdExtensionc == "0") {
        $varDimensionesId = [1,2,7,10,11];
        $varTextoDimensionp = "Procesos";
      }
      if ($varIdExtensionc == "1") {
        $varDimensionesId = [3];
        $varTextoDimensionp = "Calidad de Entrenamiento";
      }
      if ($varIdExtensionc == "2") {
        $varDimensionesId = [4];
        $varTextoDimensionp = "Ojt";
      }


      if ($varIdExtensionc == "0") {
        
        $varListaExtensionesRN = (new \yii\db\Query())
                                ->select(['tbl_speech_parametrizar.rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','tbl_speech_parametrizar.id_dp_clientes',$varListadoServicios])
                                ->andwhere(['is','tbl_speech_parametrizar.tipoparametro',null])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->all();

        $varArraExtensionesRN = array();
        foreach ($varListaExtensionesRN as $key => $value) {
          if ($value['rn'] != "") {
            array_push($varArraExtensionesRN, $value['rn']);
          }          
        }

        $varListaExtensionesExt = (new \yii\db\Query())
                                ->select(['tbl_speech_parametrizar.ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','tbl_speech_parametrizar.id_dp_clientes',$varListadoServicios])
                                ->andwhere(['is','tbl_speech_parametrizar.tipoparametro',null])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->all();

        $varArraExtensionesExt = array();
        foreach ($varListaExtensionesExt as $key => $value) {
          if ($value['ext'] != "") {
            array_push($varArraExtensionesExt, $value['ext']);
          }          
        }

        $varListaExtensionesUsuaRed = (new \yii\db\Query())
                                ->select(['tbl_speech_parametrizar.usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','tbl_speech_parametrizar.id_dp_clientes',$varListadoServicios])
                                ->andwhere(['is','tbl_speech_parametrizar.tipoparametro',null])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->all();

        $varArraExtensionesUsuaRed = array();
        foreach ($varListaExtensionesUsuaRed as $key => $value) {
          if ($value['usuared'] != "") {
            array_push($varArraExtensionesUsuaRed, $value['usuared']);
          }          
        }

      }else{

        $varListaExtensionesRN = (new \yii\db\Query())
                                ->select(['tbl_speech_parametrizar.rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','tbl_speech_parametrizar.id_dp_clientes',$varListadoServicios])
                                ->andwhere(['=','tbl_speech_parametrizar.tipoparametro',intval($varIdExtensionc)])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->all();

        $varArraExtensionesRN = array();
        foreach ($varListaExtensionesRN as $key => $value) {
          if ($value['rn'] != "") {
            array_push($varArraExtensionesRN, $value['rn']);
          }          
        }

        $varListaExtensionesExt = (new \yii\db\Query())
                                ->select(['tbl_speech_parametrizar.ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','tbl_speech_parametrizar.id_dp_clientes',$varListadoServicios])
                                ->andwhere(['=','tbl_speech_parametrizar.tipoparametro',intval($varIdExtensionc)])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->all();

        $varArraExtensionesExt = array();
        foreach ($varListaExtensionesExt as $key => $value) {
          if ($value['ext'] != "") {
            array_push($varArraExtensionesExt, $value['ext']);
          }          
        }

        $varListaExtensionesUsuaRed = (new \yii\db\Query())
                                ->select(['tbl_speech_parametrizar.usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','tbl_speech_parametrizar.id_dp_clientes',$varListadoServicios])
                                ->andwhere(['=','tbl_speech_parametrizar.tipoparametro',intval($varIdExtensionc)])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->all();

        $varArraExtensionesUsuaRed = array();
        foreach ($varListaExtensionesUsuaRed as $key => $value) {
          if ($value['usuared'] != "") {
            array_push($varArraExtensionesUsuaRed, $value['usuared']);
          }          
        }
      }

      $varImplodeRN = implode(", ", $varArraExtensionesRN);
      $arrayRN_downM = str_replace(array("#", "'", ";", " "), '', $varImplodeRN);
      $varExtensionRN = explode(",", $arrayRN_downM);

      $varImplodeExt = implode(", ", $varArraExtensionesExt);
      $arrayExt_downM = str_replace(array("#", "'", ";", " "), '', $varImplodeExt);
      $varExtensionExt = explode(",", $arrayExt_downM);

      $varImplodeUsua = implode(", ", $varArraExtensionesUsuaRed);
      $arrayUsua_downM = str_replace(array("#", "'", ";", " "), '', $varImplodeUsua);
      $varExtensionUsua = explode(",", $arrayUsua_downM);

      $varListaBolsitaCXM = (new \yii\db\Query())
                                ->select(['tbl_speech_categorias.programacategoria'])
                                ->from(['tbl_speech_categorias'])       

                                ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')

                                ->where(['in','tbl_speech_parametrizar.id_dp_clientes',$varListadoServicios])
                                ->andwhere(['is','tbl_speech_parametrizar.tipoparametro',null])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->groupby(['tbl_speech_categorias.programacategoria'])
                                ->all();

      $varArrayBolsita = array();
      foreach ($varListaBolsitaCXM as $key => $value) {
        array_push($varArrayBolsita, $value['programacategoria']);      
      }
      $varImplodeBolsita = implode(", ", $varArrayBolsita);
      $arrayBolsita_downM = str_replace(array("#", "'", ";", " "), '', $varImplodeBolsita);
      $varBolsitas = explode(",", $arrayBolsita_downM);

      $varListaidLlamada = (new \yii\db\Query())
                                ->select(['tbl_speech_servicios.idllamada'])
                                ->from(['tbl_speech_servicios'])       
                                ->where(['in','tbl_speech_servicios.id_dp_clientes',$varListadoServicios])
                                ->andwhere(['!=','tbl_speech_servicios.arbol_id',1])
                                ->andwhere(['=','tbl_speech_servicios.anulado',0])
                                ->all();

      $varArrayLlamada = array();
      foreach ($varListaidLlamada as $key => $value) {
        array_push($varArrayLlamada, $value['idllamada']);      
      }
      $varImplodeLlamada = implode(", ", $varArrayLlamada);
      $arrayLlamada_downM = str_replace(array("#", "'", ";", " "), '', $varImplodeLlamada);
      $varLlamadaId = explode(",", $arrayLlamada_downM);


      return $this->render('indexporpersona',[
        'varTextoDimensionp' => $varTextoDimensionp,
        'varListaServicios' => $varListaServicios,
        'varFechainicial' => $varFechainicial,
        'varFechaFinal' => $varFechaFinal,
        'varListaNombresGerentes' => $varListaNombresGerentes,
        'varIdExtensionc' => $varIdExtensionc,
        'varExtensionRN' => $varExtensionRN,
        'varExtensionExt' => $varExtensionExt,
        'varExtensionUsua' => $varExtensionUsua,
        'varBolsitas' => $varBolsitas,
        'varLlamadaId' => $varLlamadaId,
        'varDimensionesId' => $varDimensionesId,
      ]);
    }

    public function actionListargerentes(){
      $txtAnulado = 0; 
      $txtIdDirector = Yii::$app->request->post('id');

      if ($txtIdDirector) {
        $txtControl = \app\models\ProcesosClienteCentrocosto::find()
                      ->select(['gerente_cuenta','documento_gerente'])->distinct()
                      ->where(['=','documento_director', $txtIdDirector])
                      ->andwhere(['=','anulado',0])
                      ->andwhere(['=','estado',1])   
                      ->count(); 

        if ($txtControl > 0) {
          $txtListarGerentes = \app\models\ProcesosClienteCentrocosto::find()
                              ->select(['gerente_cuenta','documento_gerente'])->distinct()
                              ->where(['=','documento_director', $txtIdDirector])
                              ->andwhere(['=','anulado',0])
                              ->andwhere(['=','estado',1])   
                              ->all(); 

          $valor = 0;
          foreach ($txtListarGerentes as $key => $value) {
            $valor = $valor + 1; 
            $nombre = "lista_";
            $clase = "listach";
            $nombre = $nombre.$valor;
            
            echo "<input type='checkbox' id= '".$nombre."' value='".$value->documento_gerente."' class='".$clase."'>";
            echo "<label  style='font-size: 15px;' for = '".$value->documento_gerente."'>&nbsp;&nbsp; ".$value->gerente_cuenta . "</label> <br>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }

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

              foreach ($varListaLideresx as $key => $value) {
                echo "<option value='" . $value->cod_pcrc. "'>" . $value->cod_pcrc." - ".$value->pcrc. "</option>";
              }
            }else{
              echo "<option>--</option>";
            }
          }else{
            echo "<option>Seleccionar...</option>";
          }          
    }

    public function actionBuscarporproceso(){
      $model = new SpeechParametrizar();
      $varextensiones = ['0' => 'Procesos', '1' => 'Calidad de entrenamiento', '2' => 'Ojt'];

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varIdClienteProceso = $model->id_dp_clientes;
        $varFechaProceso = explode(" ", $model->fechacreacion);
        $varExtensionProceso  = $model->anulado;
        $varCentroCostosProcesos = implode(",", $model->cod_pcrc);


        $varFechasIniciosP = $varFechaProceso[0];
        $varFechasFinsP = date('Y-m-d',strtotime($varFechaProceso[2]."+ 1 days"));   

        return $this->redirect(array('indexporproceso','varIdDpCliente'=>$varIdClienteProceso,'varIdExtension'=>$varExtensionProceso,'varCentroCostos'=>$varCentroCostosProcesos,'varFechainicial'=>$varFechasIniciosP,'varFechaFinal'=>$varFechasFinsP));

      }

      return $this->renderAjax('buscarporproceso',[
        'model' => $model,
        'varextensiones' => $varextensiones,
      ]);
    }

    public function actionIndexporproceso($varIdDpCliente,$varIdExtension,$varCentroCostos,$varFechainicial,$varFechaFinal){
      $varTextoDimensionp = null;
      $varIdExtensionc = $varIdExtension;
      $varDimensionesId = null;

      $varListaCC = explode(",", str_replace(array("#", "'", ";", " "), '', $varCentroCostos));

      $varValida = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_ideal_llamadas'])            
                                ->where(['=','tbl_ideal_llamadas.id_dp_cliente',$varIdDpCliente])
                                ->andwhere(['in','tbl_ideal_llamadas.cod_pcrc',$varListaCC])
                                ->andwhere(['=','tbl_ideal_llamadas.tipoextension',intval($varIdExtension)])
                                ->andwhere(['>=','tbl_ideal_llamadas.fechainicio',$varFechainicial.' 05:00:00'])
                                ->andwhere(['<=','tbl_ideal_llamadas.fechafin',$varFechaFinal.' 05:00:00'])
                                ->count();


      if ($varValida == 0) {
        return $this->redirect('informacion');
      }


      if ($varIdExtensionc == "0") {
        $varDimensionesId = [1,2,7,10,11];
      }
      if ($varIdExtensionc == "1") {
        $varDimensionesId = [3];
      }
      if ($varIdExtensionc == "2") {
        $varDimensionesId = [4];
      }

      $varNombreServicio = (new \yii\db\Query())
                            ->select(['nameArbol'])
                            ->from(['tbl_speech_servicios'])            
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_dp_clientes',$varIdDpCliente])
                            ->Scalar();  

      $varLlamada = (new \yii\db\Query())
                            ->select(['idllamada'])
                            ->from(['tbl_speech_servicios'])            
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_dp_clientes',$varIdDpCliente])
                            ->Scalar();

      
      $varNombreCC = (new \yii\db\Query())
                            ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                            ->from(['tbl_speech_categorias'])            
                            ->where(['=','anulado',0])
                            ->andwhere(['in','cod_pcrc',$varListaCC])
                            ->groupby(['cod_pcrc'])
                            ->All(); 

      $varNombreSpeech = (new \yii\db\Query())
                            ->select(['programacategoria'])
                            ->from(['tbl_speech_categorias'])            
                            ->where(['=','anulado',0])
                            ->andwhere(['in','cod_pcrc',$varListaCC])
                            ->groupby(['cod_pcrc'])
                            ->Scalar(); 

      if ($varIdExtensionc == "0") {
        $varTextoDimensionp = "Procesos";
      }

      if ($varIdExtensionc == "1") {
        $varTextoDimensionp = "Calidad de Entrenamiento";
      }

      if ($varIdExtensionc == "2") {
        $varTextoDimensionp = "Ojt";
      }

      $varTipoParametroM = $varIdExtensionc;

          if ($varTipoParametroM > '1') {
            $varRnIdealM =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','cod_pcrc',$varListaCC])
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
                                ->where(['in','cod_pcrc',$varListaCC])
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

            if ($varTipoParametroM > '1') {
              $varExtM =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','cod_pcrc',$varListaCC])
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
                                ->where(['in','cod_pcrc',$varListaCC])
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

              if ($varTipoParametroM > '1') {
                $varUsuaM =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['in','cod_pcrc',$varListaCC])
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
                                ->where(['in','cod_pcrc',$varListaCC])
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

      $varPcrcLista = null;
      for ($i=0; $i < count($varListaCC); $i++) { 
        $varPcrcLista = $varListaCC[$i];

        if ($varTipoParametroM > '1') {
            $varRnIdealMLista =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcLista])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroM])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealMLista =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcLista])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }

          if (count($varRnIdealMLista) != 0) {
             $varArrayRnMLista = array();
            foreach ($varRnIdealMLista as $key => $value) {
              array_push($varArrayRnMLista, $value['rn']);
            }

            $varExtensionesArraysMLista = implode("', '", $varArrayRnMLista);
            $arrayExtensiones_downMLista = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysMLista);
            $varExtensionesMLista = explode(",", $arrayExtensiones_downMLista);
          }else{

            if ($varTipoParametroM > '1') {
              $varExtMLista =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcLista])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroM])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtMLista =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcLista])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtMLista) != 0) {
              $varArrayExtMLista = array();
              foreach ($varExtMLista as $key => $value) {
                array_push($varArrayExtMLista, $value['ext']);
              }

              $varExtensionesArraysMLista = implode("', '", $varArrayExtMLista);
              $arrayExtensiones_downMLista = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysMLista);
              $varExtensionesMLista = explode(",", $arrayExtensiones_downMLista);
            }else{

              if ($varTipoParametroM > '1') {
                $varUsuaMLista =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcLista])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varTipoParametroM])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaMLista =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','cod_pcrc',$varPcrcLista])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }


              if (count($varUsuaMLista) != 0) {
                $varArrayUsuaMLista = array();
                foreach ($varUsuaMLista as $key => $value) {
                  array_push($varArrayUsuaMLista, $value['usuared']);
                }

                $varExtensionesArraysMLista = implode("', '", $varArrayUsuaMLista);
                $arrayExtensiones_downMLista = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysMLista);
                $varExtensionesMLista = explode(",", $arrayExtensiones_downMLista);
              }else{
                $varExtensionesMLista = "N0A";
              }
            }
          }

        $varListadoLideresDistri = (new \yii\db\Query())
                                                ->select([
                                                  'tbl_usuarios.usua_id AS varLiderId',
                                                  'tbl_usuarios.usua_nombre AS varLider'
                                                ])
                                                ->from(['tbl_usuarios'])  

                                                ->join('LEFT OUTER JOIN', 'tbl_distribucion_asesores',
                                                  'tbl_usuarios.usua_identificacion = tbl_distribucion_asesores.cedulalider')

                                                ->where(['=','tbl_distribucion_asesores.id_dp_clientes',$varIdDpCliente])
                                                ->groupby(['tbl_distribucion_asesores.cedulalider'])
                                                ->all();

        foreach ($varListadoLideresDistri as $key => $value) {
          $varLider_id = $value['varLiderId'];

          $varListaIdFormsProceso = (new \yii\db\Query())
                                            ->select([
                                              'tbl_ejecucionformularios.id',
                                              'tbl_ejecucionformularios.usua_id_lider',
                                              'tbl_ejecucionformularios.evaluado_id'
                                              ])
                                            ->from(['tbl_ejecucionformularios']) 

                                            ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                    'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                                            ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                                    'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                                            ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                            ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                                            ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                            ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMLista])
                                            ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                                            ->andwhere(['=','tbl_ejecucionformularios.usua_id_lider',$varLider_id])
                                            ->all();

          $usua_id = Yii::$app->user->identity->id;      

          foreach ($varListaIdFormsProceso as $key => $value) {
            $formulario_idProceso = $value['id'];
            $varLiderid = $value['usua_id_lider'];
            $varAsesorid = $value['evaluado_id'];

            // //Eliminar los calculos anteriores -------------------------------------
            // \app\models\Tmpreportes::deleteAll(['usua_id' => $usua_id]);
            Yii::$app->db->createCommand('DELETE FROM tbl_ideal_tmpreportes WHERE usua_id=:idusua AND id_formulario=:id AND pcrc=:varpcrc AND id_dimension=:vardimension')
              ->bindParam(':id',$formulario_idProceso)
              ->bindParam(':varpcrc',$varPcrcLista)
              ->bindParam(':vardimension',$varTipoParametroM)
              ->bindParam(':idusua',$usua_id)->execute();

            // //Eliminar los listados anteriores -------------------------------------
            Yii::$app->db->createCommand('DELETE FROM tbl_ideal_tmploginreportes WHERE usua_id=:idusua AND id_formulario=:id AND lider_id=:idlider AND pcrc=:varpcrc  AND id_dimension=:vardimension')
              ->bindParam(':id',$formulario_idProceso)
              ->bindParam(':idusua',$usua_id)
              ->bindParam(':varpcrc',$varPcrcLista)              
              ->bindParam(':vardimension',$varTipoParametroM)
              ->bindParam(':idlider',$varLiderid)->execute();


            // //Generar el reporte de calificaciones----------------------------------
            try {
                $sql = "CALL sp_ideal_reporte_calificaciones($usua_id, $formulario_idProceso, '$varPcrcLista', $varTipoParametroM);";
                $command = \Yii::$app->db->createCommand($sql);
                $command->execute();
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Ocurrió un error, inténtelo mas tarde o '. 'comuníquese con el administrador'));
            }

            Yii::$app->db->createCommand()->insert('tbl_ideal_tmploginreportes',[
                      'id_dp_cliente' => $varIdDpCliente,
                      'bolsita' => $varNombreSpeech,
                      'pcrc' => $varPcrcLista,
                      'id_dimension' => $varTipoParametroM,
                      'id_formulario' => $formulario_idProceso, 
                      'lider_id' => $varLiderid,
                      'asesor_id' => $varAsesorid,
                      'usua_id' => $usua_id,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,                         
                  ])->execute();

            
          }
        }
      }
     

      $varListarEquipos = (new \yii\db\Query())
                          ->select(['tbl_usuarios.usua_id AS varLiderId', 'tbl_usuarios.usua_nombre AS varLider', 'tbl_evaluados.id AS varAsesor', 'tbl_dashboardspeechcalls.login_id AS varAsesorSpeech','COUNT(tbl_dashboardspeechcalls.callId) AS varCantidad'])

                          ->from(['tbl_usuarios']) 

                          ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                'tbl_usuarios.usua_id = tbl_equipos.usua_id')  

                          ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                'tbl_equipos.id = tbl_equipos_evaluados.equipo_id') 

                          ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id') 

                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                'tbl_evaluados.dsusuario_red = tbl_dashboardspeechcalls.login_id')   

                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesM])
                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varLlamada])
                          ->groupby(['tbl_dashboardspeechcalls.login_id'])
                          ->orderBy(['tbl_usuarios.usua_usuario' => SORT_DESC])
                          ->All();

      if ($varListarEquipos == null) {
        $varListarEquipos = (new \yii\db\Query())
                          ->select(['tbl_usuarios.usua_id AS varLiderId', 'tbl_usuarios.usua_nombre AS varLider', 'tbl_evaluados.id AS varAsesor', 'tbl_dashboardspeechcalls.login_id AS varAsesorSpeech','COUNT(tbl_dashboardspeechcalls.callId) AS varCantidad'])

                          ->from(['tbl_usuarios']) 

                          ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                'tbl_usuarios.usua_id = tbl_equipos.usua_id')  

                          ->join('LEFT OUTER JOIN', 'tbl_equipos_evaluados',
                                'tbl_equipos.id = tbl_equipos_evaluados.equipo_id') 

                          ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id') 

                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                'tbl_evaluados.identificacion = tbl_dashboardspeechcalls.login_id')   

                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varNombreSpeech])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesM])
                          ->groupby(['tbl_dashboardspeechcalls.login_id'])
                          ->orderBy(['tbl_usuarios.usua_usuario' => SORT_DESC])
                          ->All();
      }


      $varListasClienteIdealP = (new \yii\db\Query())
                                ->select(['cod_pcrc'])
                                ->from(['tbl_ideal_llamadas'])
                                ->where(['=','anulado',0])
                                ->andwhere(['in','cod_pcrc',$varListaCC])
                                ->andwhere(['>=','fechainicio',$varFechainicial.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaFinal.' 05:00:00'])
                                ->groupby(['cod_pcrc'])
                                ->All();

      $varPromedioResponsabilidadGeneralP = (new \yii\db\Query())
                                ->select(['round(AVG(agente),2) AS ProAgente','round(AVG(marca),2) AS ProMarca','round(AVG(canal),2) AS ProCanal'])
                                ->from(['tbl_ideal_responsabilidad'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','id_dp_cliente',$varIdDpCliente])
                                ->andwhere(['in','cod_pcrc',$varListaCC])
                                ->andwhere(['=','extension',$varIdExtensionc])
                                ->andwhere(['>=','fechainicio',$varFechainicial.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaFinal.' 05:00:00'])
                                ->all();  

      $varArrayAgenteP = null;
      $varArrayMarcaP = null;
      $varArrayCanalP = null;
      foreach ($varPromedioResponsabilidadGeneralP as $key => $value) {
        $varArrayAgenteP = $value['ProAgente'];
        $varArrayMarcaP = $value['ProMarca'];
        $varArrayCanalP = $value['ProCanal'];
      }

      return $this->render('indexporproceso',[
        'varNombreServicio' => $varNombreServicio,
        'varNombreCC' => $varNombreCC,
        'varFechainicial' => $varFechainicial,
        'varFechaFinal' => $varFechaFinal,
        'varIdDpCliente' => $varIdDpCliente,
        'varTextoDimensionp' => $varTextoDimensionp,
        'varListasClienteIdealP' => $varListasClienteIdealP,
        'varPromedioResponsabilidadGeneralP' => $varPromedioResponsabilidadGeneralP,
        'varArrayAgenteP' => $varArrayAgenteP,
        'varArrayMarcaP' => $varArrayMarcaP,
        'varArrayCanalP' => $varArrayCanalP,
        'varLlamada' => $varLlamada,
        'varIdExtensionc' => $varIdExtensionc,
        'varExtensionesM' => $varExtensionesM,
        'varNombreSpeech' => $varNombreSpeech,
        'varListarEquipos' => $varListarEquipos,
        'varDimensionesId' => $varDimensionesId,
      ]);
    }

    public function actionVerformulariomanual($liderid,$clienteid,$codpcrcid,$arbolsid,$extensionid,$llamadaid,$nombrespeechid,$fechainicioid,$fechafinid){

      $varCodPcrcManuales = $codpcrcid;
      $varIdExtensionIdeal = $extensionid;

      $varIdLiderIdeal = $liderid;    
      $varclienteid = $clienteid;       

      $varListaIdForms = (new \yii\db\Query())
                                          ->select([
                                            'tbl_ideal_tmploginreportes.lider_id', 
                                            'tbl_evaluados.id', 
                                            'tbl_evaluados.name',
                                            'COUNT(tbl_ideal_tmploginreportes.id_formulario) AS conteoValora'
                                            ])
                                          ->from(['tbl_ideal_tmploginreportes']) 

                                          ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                                  'tbl_evaluados.id = tbl_ideal_tmploginreportes.asesor_id')

                                          ->where(['=','tbl_ideal_tmploginreportes.id_dp_cliente',$varclienteid])
                                          ->andwhere(['=','tbl_ideal_tmploginreportes.lider_id',$varIdLiderIdeal])
                                          ->andwhere(['=','tbl_ideal_tmploginreportes.id_dimension',$varIdExtensionIdeal])
                                          ->groupby(['tbl_evaluados.id'])
                                          ->orderBy(['tbl_evaluados.name' => SORT_ASC])
                                          ->all();
      
      return $this->render('verformulariomanual',[
        'varListaIdForms' => $varListaIdForms,
        'varclienteid' => $varclienteid,
        'varIdExtensionIdeal' => $varIdExtensionIdeal,
      ]);
    }

    public function actionInformacion(){

      return $this->render('informacion');
    }
    

  }

?>
