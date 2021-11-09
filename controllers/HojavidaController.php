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
use app\models\HvCiudad;
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
use app\models\HvHobbies;
use app\models\HvGustos;
use app\models\HojavidaPermisosacciones;
use app\models\HojavidaPermisoscliente;
use app\models\HojavidaDatacomplementos;
use app\models\ProcesosClienteCentrocosto;
use app\models\HojavidaDataclasificacion;


  class HojavidaController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','resumen','eventos','paisciudad','eliminarevento','creapais','creaciudad','eliminarpais','eliminarciudad','informacionpersonal','listarciudades','viewinfo','permisoshv','complementoshv','asignarpermisos','eliminarpermisos','editarpermisos','createdservicio','deleteinfo','editinfo','complementosaccion'],
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
      $sesiones = Yii::$app->user->identity->id;

      $rol =  new Query;
      $rol     ->select(['tbl_roles.role_id'])
                  ->from('tbl_roles')
                  ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                              'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                  ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                              'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                  ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
      $command = $rol->createCommand();
      $roles = $command->queryScalar();

      if ($roles == "270") {

        $dataProviderhv = Yii::$app->db->createCommand("
        SELECT dp.hv_idpersonal 'idHojaVida', pc.cliente, if(dl.tipo_afinidad = 1, 'Decisor','No Decisor') 'tipo', if(dl.nivel_afinidad = 1, 'Estrátegico','Operativo') 'nivel', dp.nombre_full, dl.rol, hp.pais, if(da.activo = 1, 'Activo','No Activo') 'estado' FROM tbl_hojavida_datapersonal dp
        INNER JOIN tbl_hojavida_datalaboral dl ON 
          dl.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_hv_pais hp ON 
          hp.hv_idpais = dp.hv_idpais
        LEFT JOIN tbl_hojavida_dataacademica da ON 
          da.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_hojavida_datapcrc dc ON 
          dc.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_proceso_cliente_centrocosto pc ON 
          pc.id_dp_clientes = dc.id_dp_cliente
        WHERE
          dp.anulado = 0
          GROUP BY dp.hv_idpersonal
        ")->queryAll();

      }else{
        $paramsuser = [':idsesion' => $sesiones ];
        $varidclientes = Yii::$app->db->createCommand('
          SELECT GROUP_CONCAT(id_dp_clientes SEPARATOR", ") servicios 
            FROM tbl_hojavida_permisoscliente hp
              WHERE   
                hp.usuario_registro = :idsesion')->bindValues($paramsuser)->queryScalar(); 

        $dataProviderhv = Yii::$app->db->createCommand("
        SELECT dp.hv_idpersonal 'idHojaVida', pc.cliente, if(dl.tipo_afinidad = 1, 'Decisor','No Decisor') 'tipo', if(dl.nivel_afinidad = 1, 'Estrátegico','Operativo') 'nivel', dp.nombre_full, dl.rol, hp.pais, if(da.activo = 1, 'Activo','No Activo') 'estado' FROM tbl_hojavida_datapersonal dp
        INNER JOIN tbl_hojavida_datalaboral dl ON 
          dl.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_hv_pais hp ON 
          hp.hv_idpais = dp.hv_idpais
        LEFT JOIN tbl_hojavida_dataacademica da ON 
          da.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_hojavida_datapcrc dc ON 
          dc.hv_idpersonal = dp.hv_idpersonal
        LEFT JOIN tbl_proceso_cliente_centrocosto pc ON 
          pc.id_dp_clientes = dc.id_dp_cliente
        WHERE 
          dc.id_dp_cliente IN ($varidclientes)
            AND dp.anulado = 0
          GROUP BY dp.hv_idpersonal
        ")->queryAll();

      }

      
      
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
      $model7 = new HojavidaEventos();


      return $this->render('informacionpersonal',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'model4' => $model4,
        'model5' => $model5,
        'model6' => $model6,
        'model7' => $model7,
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
        $txtControl = \app\models\ProcesosClienteCentrocosto::find()->distinct()
                          ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                          ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1")
                          ->count();            

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\ProcesosClienteCentrocosto::find()
                          ->select(['tbl_proceso_cliente_centrocosto.documento_director','tbl_proceso_cliente_centrocosto.director_programa'])->distinct()
                            ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1") 
                            ->all();            
          
                    
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
      $txtId = Yii::$app->request->get('id');

      if ($txtId) {
        $txtControl = \app\models\ProcesosClienteCentrocosto::find()->distinct()
                          ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                          ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1")
                          ->count();            

        if ($txtControl > 0) {
          $varListaCiudad = \app\models\ProcesosClienteCentrocosto::find()
                          ->select(['tbl_proceso_cliente_centrocosto.documento_gerente','tbl_proceso_cliente_centrocosto.gerente_cuenta'])->distinct()
                            ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_proceso_cliente_centrocosto.estado = 1") 
                            ->all();           
         
                    
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
      $txtvarclasificacion = Yii::$app->request->get("txtvarclasificacion");
      $txtvaridsatu = Yii::$app->request->get("txtvaridsatu");

      $txtrta = 0;
      Yii::$app->db->createCommand()->insert('tbl_hojavida_datapersonal',[
                    'nombre_full' => $txtvaridnombrefull,
                    'identificacion' => $txtvarididentificacion,
                    'email' => $txtvaridemail,
                    'numero_movil' => $txtvaridnumeromovil,
                    'numero_fijo' => $txtvaridnumerooficina,
                    'direccion_oficina' => $txtvariddireccionoficiona,
                    'direccion_casa' => $txtvariddireccioncasa,
                    'hv_idpais' => $txtvaridpais,
                    'hv_idciudad' => $txtvarididciudad,
                    'hv_idmodalidad' => $txtvaridmdoalidad,
                    'tratamiento_data' => $txtvaridautoriza,
                    'suceptible' => $txtvaridsusceptible,
                    'indicador_satu' => $txtvaridsatu,
                    'clasificacion' => $txtvarclasificacion,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();

      
      die(json_encode($txtrta));
    }
    
    public function actionGuardarlaboral(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
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

    public function actionAplicareventos(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
      $txtvarlisteventos = Yii::$app->request->get("txtvarlisteventos");

      Yii::$app->db->createCommand()->insert('tbl_hojavida_asignareventos',[
                    'hv_ideventos' => $txtvarlisteventos,
                    'hv_idpersonal' => $txtvarautoincrement,
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
                    'ccgerente' => $vargerente,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute();  
      }          

      die(json_encode($txtrta));
    }

    public function actionViewinfo($idinfo){
      $paramsinfo = [':varInfo' => $idinfo];
      $dataProviderInfo = Yii::$app->db->createCommand('
        SELECT 
          dp.hv_idpersonal, dp.nombre_full AS NombreFull, dp.identificacion AS Identificacion,
          dp.email AS Email, dp.numero_movil AS Movil, dp.numero_fijo AS Fijo, dp.direccion_oficina AS DireccioOficina,
          dp.direccion_casa AS DireccionCasa, p.pais AS Pais, c.ciudad AS Ciudad, m.modalidad AS Modalidad,
          if(dp.tratamiento_data = 1,"No","Si") AS TratamientoDatos, if(dp.suceptible = 1,"No","Si") AS Susceptible,
          dp.indicador_satu AS IndicadorSatu, l.rol AS Rol, a.antiguedad AS Antiguedad, l.fecha_inicio_contacto AS FechaContacto,
          l.nombre_jefe AS NombreJefe, l.cargo_jefe AS CargoJefe, l.trabajo_anterior AS TrabajoAnterior,
          if(l.afinidad = 1,"Relación Directa","Relación de Interes") AS Afinidad,  dp.clasificacion,
          if(l.tipo_afinidad = 1,"Decisor","No Decisor") AS TipoAfinidad, if(l.nivel_afinidad = 1,"Estratégio","Operativo") AS NivelAfinidad,
          pc.id_dp_cliente AS IdCliente
           FROM tbl_hojavida_datapersonal dp
            INNER JOIN tbl_hv_pais p ON 
              dp.hv_idpais = p.hv_idpais
            INNER JOIN tbl_hv_ciudad c ON 
              p.hv_idpais = c.pais_id
            INNER JOIN tbl_hv_modalidad_trabajo m ON 
              dp.hv_idmodalidad = m.hv_idmodalidad
            INNER JOIN tbl_hojavida_datalaboral l ON 
              dp.hv_idpersonal = l.hv_idpersonal
            INNER JOIN tbl_hv_antiguedad_rol a ON 
              l.hv_id_antiguedad = a.hv_id_antiguedad
            INNER JOIN tbl_hojavida_datapcrc pc ON 
              dp.hv_idpersonal = pc.hv_idpersonal
            WHERE 
              dp.hv_idpersonal = :varInfo
            GROUP BY dp.hv_idpersonal               
            ')->bindValues($paramsinfo)->queryAll();

      return $this->render('viewinfo',[
        'dataProviderInfo' => $dataProviderInfo,
      ]);
    }

    public function actionComplementoshv(){
      $model = new HojavidaDatacivil();
      $model1 = new HvDominancias();
      $model2 = new HvEstilosocial();
      $model3 = new HvHobbies();
      $model4 = new HvGustos();
      $model5 = new HojavidaDataclasificacion();

      $dataProviderCivil = HojavidaDatacivil::find()
                            ->asArray()
                            ->all();

      $dataProviderDominancias = HvDominancias::find()
                                  ->asArray()
                                  ->all();

      $dataProviderEstilo = HvEstilosocial::find()
                              ->asArray()
                              ->all();

      $dataProviderHobbies = HvHobbies::find()
                              ->asArray()
                              ->all();

      $dataProvidergustos = HvGustos::find()
                              ->asArray()
                              ->all();

      $dataProviderClasificacion = HojavidaDataclasificacion::find()
                                    ->asArray()
                                    ->all();

      return $this->render('complementoshv',[
        'model' => $model,
        'dataProviderCivil' => $dataProviderCivil,
        'model1' => $model1,
        'dataProviderDominancias' => $dataProviderDominancias,
        'model2' => $model2,
        'dataProviderEstilo' => $dataProviderEstilo,
        'model3' => $model3,
        'dataProviderHobbies' => $dataProviderHobbies,
        'model4' => $model4,
        'dataProvidergustos' => $dataProvidergustos,
        'model5' => $model5,
        'dataProviderClasificacion' => $dataProviderClasificacion,

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

    public function actionIngresarhobbie(){
      $txtvaridhobbies = Yii::$app->request->get("txtvaridhobbies");

      Yii::$app->db->createCommand()->insert('tbl_hv_hobbies',[
                    'text' => $txtvaridhobbies,                                     
                ])->execute(); 

      die(json_encode($txtvaridhobbies));

    }

    public function actionIngresargustos(){
      $txtvaridgustos = Yii::$app->request->get("txtvaridgustos");

      Yii::$app->db->createCommand()->insert('tbl_hv_gustos',[
                    'text' => $txtvaridgustos,                                     
                ])->execute(); 

      die(json_encode($txtvaridgustos));

    }

    public function actionAsignarpermisos(){

      $dataProviderPermisos = HojavidaPermisosacciones::find()
                              ->asArray()
                              ->all();


      return $this->renderAjax('asignarpermisos',[
        'dataProviderPermisos' => $dataProviderPermisos,
      ]);
    }


    public function actionPermisoshv(){
      $model = new HojavidaPermisosacciones(); 
      $model2 = new HojavidaPermisoscliente();
      $varUsuario = null;
      $varNombre = null;

      $form = Yii::$app->request->post();
      if($model->load($form)){

        $varUsuario = $model->usuario_registro;
        $paramsdocumento = [':documento' => $varUsuario];
        $varNombre = Yii::$app->db->createCommand('
          SELECT usua_nombre FROM tbl_usuarios
            WHERE usua_id = :documento
            ')->bindValues($paramsdocumento)->queryScalar();
          
      }

      return $this->render('permisoshv',[
        'model' => $model,
        'model2' => $model2,
        'varUsuario' => $varUsuario,
        'varNombre' => $varNombre,
      ]);
    }

    public function actionEliminarpermisos($id) {      

      $paramsdocumento = [':documento' => $id];
      $idDoslist = Yii::$app->db->createCommand('
          SELECT p.hv_idpermisocliente FROM tbl_hojavida_permisosacciones h 
            INNER JOIN tbl_hojavida_permisoscliente p ON 
              h.usuario_registro = p.usuario_registro
            WHERE h.hv_idacciones = :documento
            ')->bindValues($paramsdocumento)->queryAll();

      if (count($idDoslist) != 0) {
        foreach ($idDoslist as $key => $value) {
          $idDos = $value['hv_idpermisocliente'];
          $this->findModelDos($idDos)->delete(); 
        }        
      }

      $this->findModel($id)->delete(); 
           
      return $this->redirect(['index']);
    }

    public function actionEliminarservicio($idDos,$id){
      $this->findModelDos($idDos)->delete();

      return $this->redirect(['editarpermisos','id'=>$id]);
    }

    public function actionEditarpermisos($id) {
      $idacciones = $id;
      $model = $this->findModel($id);

      $paramsdocumento = [':idaccion' => $id];
      $varNombre = Yii::$app->db->createCommand('
          SELECT u.usua_nombre FROM tbl_usuarios u
            INNER JOIN tbl_hojavida_permisosacciones hp ON 
              u.usua_id = hp.usuario_registro
            WHERE hp.hv_idacciones = :idaccion
            ')->bindValues($paramsdocumento)->queryScalar();

      $varUsuario = Yii::$app->db->createCommand('
          SELECT hp.usuario_registro FROM tbl_hojavida_permisosacciones hp 
            WHERE hp.hv_idacciones = :idaccion
            ')->bindValues($paramsdocumento)->queryScalar();

      $model2 = new HojavidaPermisoscliente();

      $dataProviderClientes = Yii::$app->db->createCommand('
          SELECT hp.hv_idpermisocliente, pc.cliente FROM tbl_proceso_cliente_centrocosto pc
            INNER JOIN tbl_hojavida_permisoscliente hp ON 
              pc.id_dp_clientes = hp.id_dp_clientes
            INNER JOIN tbl_hojavida_permisosacciones pa ON 
              hp.usuario_registro = pa.usuario_registro
                WHERE pa.hv_idacciones = :idaccion
             GROUP BY pc.id_dp_clientes
            ')->bindValues($paramsdocumento)->queryAll();

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->render('editarpermisos',[
          'model' => $model,
          'varNombre' => $varNombre,
          'varUsuario' => $varUsuario,
          'model2' => $model2,
          'dataProviderClientes' => $dataProviderClientes,
          'idacciones' => $idacciones,
        ]);
      }


      return $this->render('editarpermisos',[
        'model' => $model,
        'varNombre' => $varNombre,
        'varUsuario' => $varUsuario,
        'model2' => $model2,
        'dataProviderClientes' => $dataProviderClientes,
        'idacciones' => $idacciones,
      ]);
    }

    protected function findModel($id) {
      if (($model = HojavidaPermisosacciones::findOne($id)) !== null) {
        return $model;
      } else {
        throw new NotFoundHttpException('El resultado de la bus no existe.');
      }
    }

    protected function findModelDos($idDos) {
      if (($model = HojavidaPermisoscliente::findOne($idDos)) !== null) {
        return $model;
      } else {
        throw new NotFoundHttpException('El resultado de la bus no existe.');
      }
    }

    public function actionPermisosaccion(){
      $txtvarideliminar = Yii::$app->request->get("txtvarideliminar");
      $txtvarideditar = Yii::$app->request->get("txtvarideditar");
      $txtvaridmasiva = Yii::$app->request->get("txtvaridmasiva");
      $txtvariddata = Yii::$app->request->get("txtvariddata");
      $txtvaridver = Yii::$app->request->get("txtvaridver");
      $txtvariduser = Yii::$app->request->get("txtvariduser");
      $txtvarverdata = Yii::$app->request->get("txtvarverdata");

      
        Yii::$app->db->createCommand()->insert('tbl_hojavida_permisosacciones',[
                    'usuario_registro' => $txtvariduser,   
                    'hveliminar' => $txtvarideliminar,
                    'hveditar' => $txtvarideditar,
                    'hvcasrgamasiva' => $txtvaridmasiva,
                    'hvdatapersonal' => $txtvariddata,
                    'hvverresumen' => $txtvaridver,
                    'hvverdata' => $txtvarverdata,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                  
                ])->execute();      

      die(json_encode($txtvarguardarpermisos));
    }

    public function actionPermisosaccioncliente(){
      $txtvaridservicio = Yii::$app->request->get("txtvaridservicio");
      $txtvariduser = Yii::$app->request->get("txtvariduser");

      $array_idclientes = count($txtvaridservicio);
      for ($i=0; $i < $array_idclientes; $i++) { 
        $variddpcliente = $txtvaridservicio[$i];

        Yii::$app->db->createCommand()->insert('tbl_hojavida_permisoscliente',[
                    'usuario_registro' => $txtvariduser,  
                    'id_dp_clientes' => $variddpcliente,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                  
        ])->execute();
      }       

      die(json_encode($txtvariduser));
    }

    public function actionCreatedservicio($idusuario,$idaccion){
      $model2 = new HojavidaPermisoscliente();
      $varidusua = $idusuario;

      $form = Yii::$app->request->post();
      if($model2->load($form)){
        
        $array_idclientes = count($model2->id_dp_clientes);
        for ($i=0; $i < $array_idclientes; $i++) { 
          $variddpcliente = $model2->id_dp_clientes[$i];

          Yii::$app->db->createCommand()->insert('tbl_hojavida_permisoscliente',[
                      'usuario_registro' => $varidusua,  
                      'id_dp_clientes' => $variddpcliente,
                      'fechacreacion' => date('Y-m-d'),
                      'anulado' => 0,
                      'usua_id' => Yii::$app->user->identity->id,                                  
          ])->execute();
        }    

        return $this->redirect(array('editarpermisos','id'=>$idaccion));
      }

      return $this->renderAjax('createdservicio',[
        'model2' => $model2,
        'varidusua' => $varidusua,
      ]);
    }

    public function actionDeleteinfo($idinfo){
      Yii::$app->db->createCommand()->update('tbl_hojavida_asignareventos',[
                                          'anulado' => 1,
                                      ],'hv_idpersonal ='.$idinfo.'')->execute();  

      Yii::$app->db->createCommand()->update('tbl_hojavida_dataacademica',[
                                          'anulado' => 1,
                                      ],'hv_idpersonal ='.$idinfo.'')->execute();  

      Yii::$app->db->createCommand()->update('tbl_hojavida_datadirector',[
                                          'anulado' => 1,
                                      ],'hv_idpersonal ='.$idinfo.'')->execute(); 

      Yii::$app->db->createCommand()->update('tbl_hojavida_datagerente',[
                                          'anulado' => 1,
                                      ],'hv_idpersonal ='.$idinfo.'')->execute();  

      Yii::$app->db->createCommand()->update('tbl_hojavida_datalaboral',[
                                          'anulado' => 1,
                                      ],'hv_idpersonal ='.$idinfo.'')->execute();  

      Yii::$app->db->createCommand()->update('tbl_hojavida_datapcrc',[
                                          'anulado' => 1,
                                      ],'hv_idpersonal ='.$idinfo.'')->execute();  

      Yii::$app->db->createCommand()->update('tbl_hojavida_datapersonal',[
                                          'anulado' => 1,
                                      ],'hv_idpersonal ='.$idinfo.'')->execute();

      return $this->redirect('index');
    }

    public function actionEditinfo($idinfo){
      $model = HojavidaDatapersonal::findOne($idinfo);
      $model2 = HojavidaDatalaboral::findOne($idinfo);
      $model3 = new HojavidaDataacademica();
      $model4 = new HojavidaDatapcrc();
      $model5 = new HojavidaDatadirector();
      $model6 = new HojavidaDatagerente();
      $model7 = new HojavidaEventos();


      return $this->render('editinfo',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'model4' => $model4,
        'model5' => $model5,
        'model6' => $model6,
        'model7' => $model7,
        'idinfo' => $idinfo,
      ]);
    }

    public function actionDeletepcrc($id,$idsinfo){
      HojavidaDatapcrc::findOne($id)->delete();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeletedirector($id,$idsinfo){
      HojavidaDatadirector::findOne($id)->delete();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeletegerente($id,$idsinfo){
      HojavidaDatagerente::findOne($id)->delete();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeleteacademico($id,$idsinfo){
      HojavidaDataacademica::findOne($id)->delete();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeleteeventos($id,$idsinfo){
      HojavidaEventos::findOne($id)->delete();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionDeletecomplementos($id,$idsinfo){
      HojavidaDatacomplementos::findOne($id)->delete();

      return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
    }

    public function actionComplementosaccion($idsinfo){
      $model = new HojavidaDatacomplementos();

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
      }

      return $this->renderAjax('complementosaccion',[
        'model' => $model,
        'idsinfo' => $idsinfo,
      ]);
    }

    public function actionActualizapersonal(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
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
      $txtvarclasificacion = Yii::$app->request->get("txtvarclasificacion");

      $txtrta = 0;
      

      Yii::$app->db->createCommand()->update('tbl_hojavida_datapersonal',[
                    'nombre_full' => $txtvaridnombrefull,
                    'identificacion' => $txtvarididentificacion,
                    'email' => $txtvaridemail,
                    'numero_movil' => $txtvaridnumeromovil,
                    'numero_fijo' => $txtvaridnumerooficina,
                    'direccion_oficina' => $txtvariddireccionoficiona,
                    'direccion_casa' => $txtvariddireccioncasa,
                    'hv_idpais' => $txtvaridpais,
                    'hv_idciudad' => $txtvarididciudad,
                    'hv_idmodalidad' => $txtvaridmdoalidad,
                    'tratamiento_data' => $txtvaridautoriza,
                    'suceptible' => $txtvaridsusceptible,
                    'indicador_satu' => $txtvaridsatu,
                    'clasificacion' => $txtvarclasificacion,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();

      
      die(json_encode($txtrta));
    }
    
    public function actionActualizalaboral(){
      $txtvarautoincrement = Yii::$app->request->get("txtvarautoincrement");
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

      Yii::$app->db->createCommand()->update('tbl_hojavida_datalaboral',[
                    'rol' => $txtvaridrol,
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
                ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();      

      die(json_encode($txtrta));
    }


    public function actionActualizacuentas(){
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
                    'id_dp_cliente' => $txtvarid_dp_cliente,
                    'cod_pcrc' => $varcodpcrc,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();  
      }

      $array_director = count($txtvaridrequester2);
      for ($i=0; $i < $array_director; $i++) { 
        $vardirector = $txtvaridrequester2[$i];

        Yii::$app->db->createCommand()->update('tbl_hojavida_datadirector',[
                    'ccdirector' => $vardirector,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute(); 
      }

      $array_gerente = count($txtvaridrequester3);
      for ($i=0; $i < $array_gerente; $i++) { 
        $vargerente = $txtvaridrequester3[$i];

        Yii::$app->db->createCommand()->update('tbl_hojavida_datagerente',[
                    'ccgerente' => $vargerente,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ],'hv_idpersonal ='.$txtvarautoincrement.'')->execute();  
      }          

      die(json_encode($txtrta));
    }

    public function actionEditcomplementos($id,$idsinfo){
      $model = HojavidaDatacomplementos::findOne($id);;

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        
        $varcivil = $model->hv_idcivil;
        Yii::$app->db->createCommand()->update('tbl_hojavida_datacomplementos',[
                    'hv_idcivil' => $varcivil,                                      
                ],'hv_idpersonal ='.$idsinfo.'')->execute(); 

        return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
      }

      return $this->render('editcomplementos',[
        'model' => $model,
        'idsinfo' => $idsinfo,
      ]);
    }
 
    public function actionComplementosadd($idsinfo){
      $model = new HojavidaDatacomplementos();      

      if ($model->load(Yii::$app->request->post()) && $model->save()) {
        return $this->redirect(['editinfo','idinfo'=>$idsinfo]);
      }

      $paramscomplement = [':idhvaccion' => $idsinfo];
      $varCivil = Yii::$app->db->createCommand('
        SELECT dc.hv_idcivil from tbl_hojavida_datacomplementos dc
          WHERE 
            dc.hv_idpersonal = :idhvaccion
          GROUP BY dc.hv_idcivil')->bindValues($paramscomplement)->queryScalar();

      return $this->renderAjax('complementosadd',[
        'model' => $model,
        'idsinfo' => $idsinfo,
        'varCivil' => $varCivil,
      ]);
    }

    public function actionVerficarConnid(){
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


