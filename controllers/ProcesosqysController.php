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

      $varextensiones = ['1' => 'Procesos', '2' => 'Calidad de Entrenamiento', '3' => 'Ojt'];

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varDirector = $model->documento_director;
        $varGerente = $model->documento_gerente;        
        $varDimension = $model->anulado;
        $varFechas = explode(" ", $model->fechacreacion);

        $varFechasInicios = $varFechas[0];
        $varFechasFins = date('Y-m-d',strtotime($varFechas[2]."+ 1 days"));                       

        return $this->redirect(array('indexporpersona','variddir'=>$varDirector,'varidger'=>$varGerente,'vardimension'=>$varDimension,'varfechainicio'=>$varFechasInicios,'varfechafin'=>$varFechasFins));
      }

      return $this->renderAjax('buscarporpersona',[
        'model' => $model,
        'varextensiones' => $varextensiones,
      ]);
    }

    public function actionIndexporpersona($variddir,$varidger,$vardimension,$varfechainicio,$varfechafin){
      $varDocDirector = $variddir;
      $varDocGerente = explode(",", str_replace(array("#", "'", ";", " "), '', $varidger));
      $varDimensions = $vardimension;
      $varFechaInicioPersona = $varfechainicio;
      $varFechaFinPersona = $varfechafin;

      if ($varDimensions == "1") {
        $varTextoDimension = "Procesos";
      }

      if ($varDimensions == "2") {
        $varTextoDimension = "Calidad de Entrenamiento";
      }

      if ($varDimensions == "3") {
        $varTextoDimension = "Ojt";
      }

      $varNombreDirector = null;
      $varNombresGerentes = null;
      $varListasNombresGerentes = null;

      $vaListIdClientes = null;
      $vaListIdPcrcs = null;

      $varNombreDirector = (new \yii\db\Query())
                                ->select(['director_programa'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['=','documento_director',$varDocDirector])
                                ->andwhere(['=','estado',1])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['director_programa'])
                                ->Scalar();

      if ($varidger == "") {
        
        $vaListIdClientes = (new \yii\db\Query())
                                ->select(['id_dp_clientes'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['=','documento_director',$varDocDirector])
                                ->andwhere(['=','estado',1])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['id_dp_clientes'])
                                ->all();

        $vaListIdPcrcs = (new \yii\db\Query())
                                ->select(['id_dp_clientes','cod_pcrc'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['=','documento_director',$varDocDirector])
                                ->andwhere(['=','estado',1])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['cod_pcrc'])
                                ->all();

      }else{
        
        $vaListIdClientes = (new \yii\db\Query())
                                ->select(['id_dp_clientes'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['IN','documento_gerente',$varDocGerente])
                                ->andwhere(['=','estado',1])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['id_dp_clientes'])
                                ->all();

        $vaListIdPcrcs = (new \yii\db\Query())
                                ->select(['id_dp_clientes','cod_pcrc'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['IN','documento_gerente',$varDocGerente])
                                ->andwhere(['=','estado',1])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['cod_pcrc'])
                                ->all();

        $varNombresGerentes = (new \yii\db\Query())
                                ->select(['gerente_cuenta'])
                                ->from(['tbl_proceso_cliente_centrocosto'])            
                                ->where(['IN','documento_gerente',$varDocGerente])
                                ->andwhere(['=','estado',1])
                                ->andwhere(['=','anulado',0])
                                ->groupby(['gerente_cuenta'])
                                ->all();

        $varArrayNombresGerentes = array();
        foreach ($varNombresGerentes as $key => $value) {
          array_push($varArrayNombresGerentes, $value['gerente_cuenta']);
        }
        $varListasNombresGerentes = implode(" -- ", $varArrayNombresGerentes);
      }      


      $varArrayCodPcrcs = array();
      foreach ($vaListIdPcrcs as $key => $value) {
        array_push($varArrayCodPcrcs, $value['cod_pcrc']);
      }
      $varLstasCodPcrcs = $varArrayCodPcrcs;

      $varListasClientesIdeal = (new \yii\db\Query())
                                ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes','tbl_proceso_cliente_centrocosto.cliente'])
                                ->from(['tbl_proceso_cliente_centrocosto'])
                                ->join('LEFT OUTER JOIN', 'tbl_ideal_llamadas',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_ideal_llamadas.id_dp_cliente')         
                                ->where(['=','tbl_ideal_llamadas.anulado',0])
                                ->andwhere(['in','tbl_ideal_llamadas.cod_pcrc',$varLstasCodPcrcs])
                                ->andwhere(['>=','tbl_ideal_llamadas.fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                ->andwhere(['<=','tbl_ideal_llamadas.fechafin',$varFechaFinPersona.' 05:00:00'])
                                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                                ->All();

      $varArrayClientesID = array();
      foreach ($varListasClientesIdeal as $key => $value) {
        array_push($varArrayClientesID, $value['id_dp_clientes']);
      }
      $varListasIdClientes = $varArrayClientesID;

      
      $varPromedioResponsabilidadGeneral = (new \yii\db\Query())
                                ->select(['round(AVG(agente),2) AS ProAgente','round(AVG(marca),2) AS ProMarca','round(AVG(canal),2) AS ProCanal'])
                                ->from(['tbl_ideal_responsabilidad'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['in','id_dp_cliente',$varListasIdClientes])
                                ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                ->all();  

      $varArrayAgente = null;
      $varArrayMarca = null;
      $varArrayCanal = null;
      foreach ($varPromedioResponsabilidadGeneral as $key => $value) {
        $varArrayAgente = $value['ProAgente'];
        $varArrayMarca = $value['ProMarca'];
        $varArrayCanal = $value['ProCanal'];
      }

      return $this->render('indexporpersona',[
        'varNombreDirector' => $varNombreDirector,
        'varNombresGerentes' => $varNombresGerentes,
        'varDocGerente' => $varDocGerente,
        'varTextoDimension' => $varTextoDimension,
        'varFechaInicioPersona' => $varFechaInicioPersona,
        'varFechaFinPersona' => $varFechaFinPersona,
        'varListasNombresGerentes' => $varListasNombresGerentes,
        'varPromedioResponsabilidadGeneral' => $varPromedioResponsabilidadGeneral,
        'varArrayAgente' => $varArrayAgente,
        'varArrayMarca' => $varArrayMarca,
        'varArrayCanal' => $varArrayCanal,
        'vaListIdClientes' => $vaListIdClientes,
        'varLstasCodPcrcs' => $varLstasCodPcrcs,
        'varListasClientesIdeal' => $varListasClientesIdeal,
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

              
              $valor = 0;
              foreach ($varListaLideresx as $key => $value) {
                $valor = $valor + 1; 
                $nombre = "lista_";
                $clase = "listach";
                $nombre = $nombre.$valor;
                echo "<input type='checkbox' id= '".$nombre."' value='".$value->cod_pcrc."' class='".$clase."'>";
                echo "<label style='font-size: 12px;' for = '".$value->cod_pcrc."'>&nbsp;&nbsp; ".$value->cod_pcrc." - ".$value->pcrc . "</label> <br>";                    
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
        $varCentroCostosProcesos = $model->cod_pcrc;

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

      $varListaCC = explode(",", str_replace(array("#", "'", ";", " "), '', $varCentroCostos));
      $varNombreCC = (new \yii\db\Query())
                            ->select(['concat(cod_pcrc," - ",pcrc) as NamePcrc'])
                            ->from(['tbl_speech_categorias'])            
                            ->where(['=','anulado',0])
                            ->andwhere(['in','cod_pcrc',$varListaCC])
                            ->groupby(['cod_pcrc'])
                            ->All(); 

      if ($varIdExtensionc == "0") {
        $varTextoDimensionp = "Procesos";
      }

      if ($varIdExtensionc == "1") {
        $varTextoDimensionp = "Calidad de Entrenamiento";
      }

      if ($varIdExtensionc == "2") {
        $varTextoDimensionp = "Ojt";
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
      ]);
    }
    

  }

?>
