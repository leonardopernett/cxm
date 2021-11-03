<?php

namespace app\controllers;

/* ini_set('upload_max_filesize', '50M');
 */
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
use app\models\HojavidaEventos;
use app\models\HVCiudad;
use app\models\HvPais;
use app\models\HojavidaDatapersonal;
use app\models\HojavidaDatalaboral;
use app\models\HojavidaDataacademica;
use app\models\HojavidaDatacuenta;
use app\models\HojavidaDatapcrc;
use app\models\HojavidaDatadirector;
use app\models\HojavidaDatagerente;
use app\models\HojavidaDatacivil;
use app\models\HvDominancias;
use app\models\HvEstilosocial;



  class HojavidaController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','resumen','eventos','paisciudad','eliminarevento','creapais','creaciudad','eliminarpais','eliminarciudad','informacionpersonal','listarciudades','viewinfo','permisoshv','complementoshv'],
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
              'delete' => ['get'],
            ],
          ],

          'corsFilter' => [
            'class' => \yii\filters\Cors::class,
        ],
        ];
    } 
   
    public function actionIndex(){

      $dataProviderhv = Yii::$app->db->createCommand("
        SELECT dp.hv_idpersonal 'idHojaVida', pc.cliente, if(dl.tipo_afinidad = 1, 'Decisor','No Decisor') 'tipo', if(dl.nivel_afinidad = 1, 'Estrátegico','Operativo') 'nivel', dp.nombre_full, dl.rol, hp.pais, if(da.activo = 1, 'Activo','No Activo') 'estado' FROM tbl_hojavida_datapersonal dp
        INNER JOIN tbl_hojavida_datalaboral dl ON 
          dl.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_hv_pais hp ON 
          hp.hv_idpais = dp.hv_idpais
        LEFT JOIN tbl_hojavida_dataacademica da ON 
          da.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_hojavida_datacuenta dc ON 
          dc.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_proceso_cliente_centrocosto pc ON 
          pc.id_dp_clientes = dc.id_dp_cliente
          GROUP BY dp.hv_idpersonal
        ")->queryAll();
      
      return $this->render('index',[
        'dataProviderhv' => $dataProviderhv,
      ]);
    }

    public function actionResumen(){
      $id = Yii::$app->user->identity->id;


      return $this->render('resumen');
    }

    public function actionEventos(){
      $model = new HojavidaEventos();

      $form = Yii::$app->request->post();
      if($model->load($form)){

        $txtFecha = explode(" ", $model->fechacreacion);
        $varFechaInicio = $txtFecha[0];
        $varFechaFin = $txtFecha[2];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_eventos',[
                    'nombre_evento' => $model->nombre_evento,
                    'tipo_evento' => $model->tipo_evento,
                    'hv_idciudad' => $model->hv_idciudad,  
                    'fecha_evento_inicio' => $varFechaInicio,
                    'fecha_evento_fin' => $varFechaFin,
                    'asistencia' => $model->asistencia,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('eventos',['model'=>$model]);
      }

      $dataProvider = Yii::$app->db->createCommand("
        SELECT e.hv_ideventos, e.nombre_evento, e.tipo_evento, e.fecha_evento_inicio, e.fecha_evento_fin, c.ciudad, e.asistencia 
          FROM tbl_hojavida_eventos e
              INNER JOIN tbl_hv_ciudad c ON c.hv_idciudad = e.hv_idciudad ")->queryAll();


      return $this->render('eventos',[
        'model' => $model,
        'dataProvider' => $dataProvider,
      ]);
    }

    public function actionEliminarevento($ideventos){
      $model = new HojavidaEventos();
      $varideventos = $ideventos;

      if ($varideventos != null) {
        $eventos = HojavidaEventos::findOne($varideventos);
        $eventos->delete();
      }      

      return $this->redirect('eventos',['model'=>$model]);
    }

    public function actionPaisciudad(){

      $dataProviderPais = HvPais::find()
                            ->asArray()
                            ->all();

      $dataProviderCiudad = HvCiudad::find()
                            ->asArray()
                            ->all();

      return $this->render('paisciudad',[
        'dataProviderPais' => $dataProviderPais,
        'dataProviderCiudad' => $dataProviderCiudad,
      ]);
    }

    public function actionCreapais(){
      $modelpais = new HvPais();

      $form = Yii::$app->request->post();
      if($modelpais->load($form)){

        Yii::$app->db->createCommand()->insert('tbl_hv_pais',[
                    'pais' => $modelpais->pais,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('paisciudad');
      }

      return $this->renderAjax('creapais',[
        'modelpais' => $modelpais,
      ]);
    }

    public function actionEliminarpais($idpais){
      $varidpais = $idpais;

      if ($varidpais != null) {
        $pais = HvPais::findOne($varidpais);
        $pais->delete();
      }      

      return $this->redirect('paisciudad');
    }

    public function actionCreaciudad(){      
      $modelciudad = new HvCiudad();

      $form = Yii::$app->request->post();
      if($modelciudad->load($form)){

        Yii::$app->db->createCommand()->insert('tbl_hv_ciudad',[
                    'pais_id' => $modelciudad->pais_id,
                    'ciudad' => $modelciudad->ciudad,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

        return $this->redirect('paisciudad');
      }

      return $this->renderAjax('creaciudad',[
        'modelciudad' => $modelciudad,
      ]);
    }

    public function actionEliminarciudad($idciudad){
      $varidciudad = $idciudad;

      if ($varidciudad != null) {
        $ciudad = HvCiudad::findOne($varidciudad);
        $ciudad->delete();
      }      

      return $this->redirect('paisciudad');
    }

    public function actionInformacionpersonal(){
      $model = new HojavidaDatapersonal();
      $model2 = new HojavidaDatalaboral();
      $model3 = new HojavidaDataacademica();
      $model4 = new HojavidaDatapcrc();
      $model5 = new HojavidaDatadirector();
      $model6 = new HojavidaDatagerente();


      return $this->render('informacionpersonal',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'model4' => $model4,
        'model5' => $model5,
        'model6' => $model6,
      ]);
    }

    public function actionListarciudades(){
      $txtAnulado = 0; 
      $txtId = Yii::$app->request->get('id');

      if ($txtId) {
        $txtControl = \app\models\HvCiudad::find()->distinct()
                          ->where(['tbl_hv_ciudad.pais_id' => $txtId])
                          ->andwhere("tbl_hv_ciudad.anulado = 0")
                          ->count();            

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\HvCiudad::find()
                          ->select(['tbl_hv_ciudad.hv_idciudad','tbl_hv_ciudad.ciudad'])->distinct()
                            ->where(['tbl_hv_ciudad.pais_id' => $txtId])
                            ->andwhere("tbl_hv_ciudad.anulado = 0")                 
                            ->orderBy(['tbl_hv_ciudad.ciudad' => SORT_DESC])
                            ->all();            
          $valor = 0;
                    
          foreach ($varListaCiudad as $key => $value) {
            echo "<option value='" . $value->hv_idciudad. "'>" . $value->ciudad. "</option>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }

    }

    public function actionListardirectores(){
      $txtAnulado = 0; 
      $txtId = Yii::$app->request->get('id');

      if ($txtId) {
        $txtControl = \app\models\ProcesoClienteCentrocosto::find()->distinct()
                          ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                          ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1")
                          ->count();            

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\ProcesoClienteCentrocosto::find()
                          ->select(['tbl_proceso_cliente_centrocosto.documento_director','tbl_proceso_cliente_centrocosto.director_programa'])->distinct()
                            ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1") 
                            ->all();            
          $valor = 0;
                    
          foreach ($varListaCiudad as $key => $value) {
            echo "<option value='" . $value->documento_director. "'>" . $value->director_programa. "</option>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }
    }

    public function actionListargerentes(){
      $txtAnulado = 0; 
      $txtId = Yii::$app->request->get('id');

      if ($txtId) {
        $txtControl = \app\models\ProcesoClienteCentrocosto::find()->distinct()
                          ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                          ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1")
                          ->count();            

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\ProcesoClienteCentrocosto::find()
                          ->select(['tbl_proceso_cliente_centrocosto.documento_gerente','tbl_proceso_cliente_centrocosto.gerente_cuenta'])->distinct()
                            ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1") 
                            ->all();            
          $valor = 0;
                    
          foreach ($varListaCiudad as $key => $value) {
            echo "<option value='" . $value->documento_gerente. "'>" . $value->gerente_cuenta. "</option>";
          }
        }else{
          echo "<option>-</option>";
        }
      }else{
        echo "<option>No hay datos</option>";
      }
    }

    public function actionVerificacedula(){
      $paramsdocumento = [':documento' => Yii::$app->request->get("txtvarididentificacion")];
      $varVerifica = Yii::$app->db->createCommand('
          SELECT COUNT(dp.hv_idpersonal) FROM tbl_hojavida_datapersonal dp 
            WHERE dp.anulado = 0 AND dp.identificacion = :documento')->bindValues($paramsdocumento)->queryScalar();

      die(json_encode($varVerifica));      
    }

    public function actionGuardarpersonal(){
      $txtvaridnombrefull = Yii::$app->request->get("txtvaridnombrefull");
      $txtvarididentificacion = Yii::$app->request->get("txtvarididentificacion");
      $txtvaridemail = Yii::$app->request->get("txtvaridemail");
      $txtvaridnumeromovil = Yii::$app->request->get("txtvaridnumeromovil");
      $txtvaridnumerooficina = Yii::$app->request->get("txtvaridnumerooficina");
      $txtvaridmdoalidad = Yii::$app->request->get("txtvaridmdoalidad");
      $txtvariddireccionoficiona = Yii::$app->request->get("txtvariddireccionoficiona");
      $txtvariddireccioncasa = Yii::$app->request->get("txtvariddireccioncasa");
      $txtvaridautoriza = Yii::$app->request->get("txtvaridautoriza");
      $txtvaridpais = Yii::$app->request->get("txtvaridpais");
      $txtvarididciudad = Yii::$app->request->get("txtvarididciudad");
      $txtvaridsusceptible = Yii::$app->request->get("txtvaridsusceptible");
      $txtvaridsatu = Yii::$app->request->get("txtvaridsatu");

      $txtrta = 0;
      Yii::$app->db->createCommand()->insert('tbl_hojavida_datapersonal',[
                    'nombre_full' => $txtvaridnombrefull,
                    'identificacion' => $txtvarididentificacion,
                    'email' => $txtvaridemail,
                    'numero_movil' => $txtvaridnumeromovil,
                    'numero_fijo' => $txtvaridnumerooficina,
                    'direccion_oficina' => $txtvaridnumerooficina,
                    'direccion_casa' => $txtvariddireccioncasa,
                    'hv_idpais' => $txtvaridpais,
                    'hv_idciudad' => $txtvarididciudad,
                    'hv_idmodalidad' => $txtvaridmdoalidad,
                    'tratamiento_data' => $txtvaridautoriza,
                    'suceptible' => $txtvaridsusceptible,
                    'indicador_satu' => $txtvaridsatu,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

      
      die(json_encode($txtrta));
    }
    
    public function actionGuardarlaboral(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvarididentificacion = Yii::$app->request->get("txtvarididentificacion");
      $txtvaridrol = Yii::$app->request->get("txtvaridrol");
      $txtvaridantiguedad = Yii::$app->request->get("txtvaridantiguedad");
      $txtvaridfechainicio = Yii::$app->request->get("txtvaridfechainicio");
      $txtvaridnombrejefe = Yii::$app->request->get("txtvaridnombrejefe");
      $txtvaridcargojefe = Yii::$app->request->get("txtvaridcargojefe");
      $txtvaridtrabajoanterior = Yii::$app->request->get("txtvaridtrabajoanterior");
      $txtvaridafinidad = Yii::$app->request->get("txtvaridafinidad");
      $txtvaridtipoafinidad = Yii::$app->request->get("txtvaridtipoafinidad");
      $txtvaridnivelafinidad = Yii::$app->request->get("txtvaridnivelafinidad");
      
      $txtrta = 0;      

      Yii::$app->db->createCommand()->insert('tbl_hojavida_datalaboral',[
                    'rol' => $txtvaridrol,
                    'hv_idpersonal' => $txtvarautoincrement,
                    'hv_id_antiguedad' => $txtvaridantiguedad,
                    'fecha_inicio_contacto' => $txtvaridfechainicio,
                    'nombre_jefe' => $txtvaridnombrejefe,
                    'cargo_jefe' => $txtvaridcargojefe,
                    'trabajo_anterior' => $txtvaridtrabajoanterior,
                    'afinidad' => $txtvaridafinidad,
                    'tipo_afinidad' => $txtvaridtipoafinidad,
                    'nivel_afinidad' => $txtvaridnivelafinidad,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();      

      die(json_encode($txtrta));
    }

    public function actionGuardaracademicos(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvaridprofesion = Yii::$app->request->get("txtvaridprofesion");
      $txtvaridespecializacion = Yii::$app->request->get("txtvaridespecializacion");
      $txtvaridmaestria = Yii::$app->request->get("txtvaridmaestria");
      $txtvariddoctorado = Yii::$app->request->get("txtvariddoctorado");
      $txtvaridestado = Yii::$app->request->get("txtvaridestado");

      $txtrta = 0;      

      Yii::$app->db->createCommand()->insert('tbl_hojavida_dataacademica',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'idhvcursosacademico' => $txtvaridprofesion,
                    'activo' => $txtvaridestado,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_hojavida_dataacademica',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'idhvcursosacademico' => $txtvaridespecializacion,
                    'activo' => $txtvaridestado,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_hojavida_dataacademica',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'idhvcursosacademico' => $txtvaridmaestria,
                    'activo' => $txtvaridestado,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_hojavida_dataacademica',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'idhvcursosacademico' => $txtvariddoctorado,
                    'activo' => $txtvaridestado,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtrta));
    }

    public function actionGuardarcuentas(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvarid_dp_cliente = Yii::$app->request->get("txtvarid_dp_cliente");
      $txtvaridrequester = Yii::$app->request->get("txtvaridrequester");
      $txtvaridrequester2 = Yii::$app->request->get("txtvaridrequester2");
      $txtvaridrequester3 = Yii::$app->request->get("txtvaridrequester3");
      
      $txtrta = 0;      

      $array_codpcrc = count($txtvaridrequester);
      for ($i=0; $i < $array_codpcrc; $i++) { 
        $varcodpcrc = $txtvaridrequester[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_datapcrc',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'id_dp_cliente' => $txtvarid_dp_cliente,
                    'cod_pcrc' => $varcodpcrc,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();  
      }

      $array_director = count($txtvaridrequester2);
      for ($i=0; $i < $array_director; $i++) { 
        $vardirector = $txtvaridrequester2[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_datadirector',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'id_dp_cliente' => $txtvarid_dp_cliente,
                    'ccdirector' => $vardirector,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 
      }

      $array_gerente = count($txtvaridrequester3);
      for ($i=0; $i < $array_gerente; $i++) { 
        $vargerente = $txtvaridrequester3[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_datagerente',[
                    'hv_idpersonal' => $txtvarautoincrement,
                    'id_dp_cliente' => $txtvarid_dp_cliente,
                    'ccgerente' => $vargerente,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();  
      }          

      die(json_encode($txtrta));
    }

    public function actionViewinfo($idinfo){
      $model = new HojavidaDatapersonal();
      $model2 = new HojavidaDatalaboral();
      $model3 = new HojavidaDataacademica();
      $model4 = new HojavidaDatacuenta(); 

      return $this->render('viewinfo',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'model4' => $model4,
      ]);
    }

    public function actionComplementoshv(){
      $model = new HojavidaDatacivil();
      $model1 = new HvDominancias();
      $model2 = new HvEstilosocial();

      $dataProviderCivil = HojavidaDatacivil::find()
                            ->asArray()
                            ->all();

      $dataProviderDominancias = HvDominancias::find()
                                  ->asArray()
                                  ->all();

      $dataProviderEstilo = HvEstilosocial::find()
                              ->asArray()
                              ->all();


      return $this->render('complementoshv',[
        'model' => $model,
        'dataProviderCivil' => $dataProviderCivil,
        'model1' => $model1,
        'dataProviderDominancias' => $dataProviderDominancias,
        'model2' => $model2,
        'dataProviderEstilo' => $dataProviderEstilo,
      ]);
    }

    public function actionIngresarcivil(){
      $txtvaridcivil = Yii::$app->request->get("txtvaridcivil");

      Yii::$app->db->createCommand()->insert('tbl_hojavida_datacivil',[
                    'estadocivil' => $txtvaridcivil,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtvaridcivil));

    }

    public function actionIngresardominancia(){
      $txtiddominancia = Yii::$app->request->get("txtiddominancia");

      Yii::$app->db->createCommand()->insert('tbl_hv_dominancias',[
                    'dominancia' => $txtiddominancia,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtiddominancia));

    }

    public function actionIngresarestilo(){
      $txtvaridestio = Yii::$app->request->get("txtvaridestio");

      Yii::$app->db->createCommand()->insert('tbl_hv_estilosocial',[
                    'estilosocial' => $txtvaridestio,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtvaridestio));

    }

    public function actionPermisoshv(){

      $varConnid = Yii::$app->db->createCommand("
        SELECT * FROM tbl_base_satisfaccion b WHERE b.fecha_satu BETWEEN '2021-10-01 00:00:00' AND '2021-10-31 23:59:59'
  AND b.pcrc = 2727
    AND b.id = 14930572 ")->queryAll();

      foreach ($varConnid as $key => $value) {

        if (is_null($value['buzon']) || empty($value['buzon']) || $value['buzon'] == "") {
          var_dump("No tiene buzon en BD");
          $varBuzon = $this->_buscarArchivoBuzon(
                                  sprintf("%02s", $value['dia']) . "_" . sprintf("%02s", $value['mes']) . "_" . $value['ano'], $value['connid']);

          var_dump('Resultado: '.$varBuzon);
          
        }else{
          $varBuzon = $this->_buscarArchivoBuzon(
                                  sprintf("%02s", $value['dia']) . "_" . sprintf("%02s", $value['mes']) . "_" . $value['ano'], $value['connid']);
          var_dump('Resultado: '.$varBuzon);
          var_dump("No Consulto");
        }

      }
   
      

      return $this->render('permisoshv');
    }

    private function _buscarArchivoBuzon($fechaEncuesta, $connId) {
                $output = NULL;
                try {
                    $rutaPrincipalBuzonesLlamadas = \Yii::$app->params["ruta_buzon"];                    
                    $command = "find {$rutaPrincipalBuzonesLlamadas}/Buzones_{$fechaEncuesta} -iname *{$connId}*.wav";
                    \Yii::error("COMANDO BUZON: " . $command, 'basesatisfaccion');
                    
                    file_put_contents("A.TXT", $command);

                    $output = exec($command);
                    var_dump('Resultado funcion: '.$output);
                } catch (\yii\base\Exception $exc) {
                    \Yii::error($exc->getTraceAsString(), 'basesatisfaccion');
                    return $output;
                }
                return $output;
            }


  }

?>


