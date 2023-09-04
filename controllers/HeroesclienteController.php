<?php

namespace app\controllers;

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
use app\models\HeroesTipopostulacion;
use app\models\HeroesCiudadpostulacion;
use app\models\HeroesCargospostulacion;
use app\models\HeroesGeneralpostulacion;
use app\models\SpeechParametrizar;
use app\models\Formularios;
use app\models\FormUploadtigo;
use PHPExcel_Shared_Date;
use Exception;

  class HeroesclienteController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','parametrizarpostulacion','registrarpostulacion', 'reportepostulacion','valorapostulacion','verpostulacion','masivapostulacion','descargarpostulacion'],
              'rules' => [
                [
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function() {
                    return Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast();
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

    public function actions() {
      return [
          'error' => [
            'class' => 'yii\web\ErrorAction',
          ]
      ];
    }
   
    public function actionIndex(){  
      $varCantidades = (new \yii\db\Query())
                        ->select([
                          'tbl_heroes_generalpostulacion.fechacreacion', 
                          'COUNT(tbl_heroes_generalpostulacion.id_generalpostulacion) AS cantidad'
                        ])
                        ->from(['tbl_heroes_generalpostulacion'])
                        ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                        ->groupby(['tbl_heroes_generalpostulacion.fechacreacion'])
                        ->all();
      
      return $this->render('index',[
        'varCantidades' => $varCantidades,
      ]);
    }

    public function actionParametrizarpostulacion(){
      $modeltipos = new HeroesTipopostulacion();
      $modelciudad = new HeroesCiudadpostulacion();
      $modelcargos = new HeroesCargospostulacion();

      $varDataTipos = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_heroes_tipopostulacion'])
                        ->where(['=','tbl_heroes_tipopostulacion.anulado',0])
                        ->all();  

      $varDataCiudad =  (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_heroes_ciudadpostulacion'])
                        ->where(['=','tbl_heroes_ciudadpostulacion.anulado',0])
                        ->all();  

      $varDataCargos = (new \yii\db\Query())
                        ->select(['*'])
                        ->from(['tbl_heroes_cargospostulacion'])
                        ->where(['=','tbl_heroes_cargospostulacion.anulado',0])
                        ->all();  

      return $this->render('parametrizarpostulacion',[
        'modeltipos' => $modeltipos,
        'modelciudad' => $modelciudad,
        'varDataTipos' => $varDataTipos,
        'varDataCiudad' => $varDataCiudad,
        'modelcargos' => $modelcargos,
        'varDataCargos' => $varDataCargos,
      ]);
    }

    public function actionEliminartipo($id){

      Yii::$app->db->createCommand()->update('tbl_heroes_tipopostulacion',[
                    'anulado' => 1,                                                
                ],'id_tipopostulacion ='.$id.'')->execute();

      return $this->redirect(['parametrizarpostulacion']);
    }

    public function actionEliminarciudad($id){
      
      Yii::$app->db->createCommand()->update('tbl_heroes_ciudadpostulacion',[
                    'anulado' => 1,                                                
                ],'id_ciudadpostulacion ='.$id.'')->execute();

      return $this->redirect(['parametrizarpostulacion']);
    }

    public function actionEliminarcargo($id){
      
      Yii::$app->db->createCommand()->update('tbl_heroes_cargospostulacion',[
                    'anulado' => 1,                                                
                ],'id_cargospostulacion ='.$id.'')->execute();

      return $this->redirect(['parametrizarpostulacion']);
    }

    public function actionIngresartipopostula(){
      $txtvaridtipopostula = Yii::$app->request->get("txtvaridtipopostula");

      Yii::$app->db->createCommand()->insert('tbl_heroes_tipopostulacion',[
                    'tipopostulacion' => $txtvaridtipopostula,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtvaridtipopostula));

    }

    public function actionIngresarciudadpostula(){
      $txtvaridciudadpostula = Yii::$app->request->get("txtvaridciudadpostula");

      Yii::$app->db->createCommand()->insert('tbl_heroes_ciudadpostulacion',[
                    'ciudadpostulacion' => $txtvaridciudadpostula,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtvaridciudadpostula));

    }

    public function actionIngresarcargopostula(){
      $txtvaridcargopostula = Yii::$app->request->get("txtvaridcargopostula");

      Yii::$app->db->createCommand()->insert('tbl_heroes_cargospostulacion',[
                    'cargospostulacion' => $txtvaridcargopostula,
                    'fechacreacion' => date('Y-m-d'),
                    'anulado' => 0,
                    'usua_id' => Yii::$app->user->identity->id,                                       
                ])->execute(); 

      die(json_encode($txtvaridcargopostula));

    }

    public function actionListarcentrocostos(){
      $txtidcliente = Yii::$app->request->get('id');

      if ($txtidcliente) {
        $varCantidades = \app\models\ProcesosClienteCentrocosto::find()->distinct()
              ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])              
              ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$txtidcliente])
              ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
              ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
              ->count();

        if ($varCantidades > 0) {
          $varDataCentrosCostos = \app\models\ProcesosClienteCentrocosto::find()->distinct()
              ->select(['tbl_proceso_cliente_centrocosto.cod_pcrc','tbl_proceso_cliente_centrocosto.pcrc'])              
              ->where(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$txtidcliente])
              ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])
              ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
              ->all();

          echo "<option value='' disabled selected>Seleccionar Centro Costo...</option>";
          foreach ($varDataCentrosCostos as $value) {
            echo "<option value='" . $value['cod_pcrc']. "'>" . $value['cod_pcrc'].' - '.$value['pcrc'] . "</option>";
          }
        }else{
          echo "<option>--</option>";
        }
      }else{
        echo "<option>Seleccionar Centro Costo...</option>";
      }

    }

    public function actionRegistrarpostulacion($id_procesos){
      $model = new HeroesGeneralpostulacion();

      $varNombrePotulador = (new \yii\db\Query())
                              ->select(['tbl_usuarios.usua_nombre'])
                              ->from(['tbl_usuarios'])
                              ->where(['=','tbl_usuarios.usua_id',Yii::$app->user->identity->id])
                              ->scalar();  

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varFechaInteraccion = date("Y-m-d H:i:s",strtotime($model->fecha_interaccion));  


        Yii::$app->db->createCommand()->insert('tbl_heroes_generalpostulacion',[
                      'id_tipopostulacion' => $model->id_tipopostulacion,
                      'id_postulador' => $model->id_postulador, 
                      'id_cargospostulacion' => $model->id_cargospostulacion,
                      'id_postulante' => $model->id_postulante,
                      'id_dp_clientes' => $model->id_dp_clientes,
                      'cod_pcrc' => $model->cod_pcrc,
                      'id_ciudadpostulacion' => $model->id_ciudadpostulacion,
                      'fecha_interaccion' => $varFechaInteraccion,
                      'ext_interaccion' => $model->ext_interaccion,
                      'usuario_interaccion' => $model->usuario_interaccion,
                      'historia_interaccion' => $model->historia_interaccion,
                      'idea_postulacion' => $model->idea_postulacion,  
                      'estado' => 1,
                      'procesos' => 1,
                      'tipo_postulante' => $model->tipo_postulante,
                      'anulado' => 0,
                      'usua_id' =>  Yii::$app->user->identity->id,   
                      'fechacreacion' => date('Y-m-d'),                
        ])->execute();
        
        if ($id_procesos == 1) {
          return $this->redirect(['index']);
        }else{
          return $this->redirect(['reportepostulacion']);
        }
        
      }

      return $this->render('registrarpostulacion',[
        'model' => $model,
        'varNombrePotulador' => $varNombrePotulador,
      ]);
    }

    public function actionReportepostulacion(){
      $model = new HeroesGeneralpostulacion();
      $varEstados = ['1'=>'Abierto','2'=>'Cerrado'];
      $varDataResultado = null;
      $varCantidadTotal = null;
      $varCantidadEstados = array();
      $varCantidadTipos = array();
      $varCantidadValores = null;
      $arrayDataCentroCosto = array();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varFecha_BD = explode(" ", $model->fechacreacion);

        $varFechaInicio_BD = $varFecha_BD[0];
        $varFechaFin_BD = date('Y-m-d',strtotime($varFecha_BD[2]));
        
        if ($model->cod_pcrc) {
          for ($i=0; $i < count($model->cod_pcrc); $i++) { 
            array_push($arrayDataCentroCosto, $model->cod_pcrc[$i]);
          }
        }        

        $varDataResultado = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_generalpostulacion.id_generalpostulacion', 
                              'tbl_heroes_generalpostulacion.fechacreacion',
                              'tbl_heroes_tipopostulacion.tipopostulacion',
                              'tbl_heroes_cargospostulacion.cargospostulacion', 
                              'tbl_heroes_generalpostulacion.id_postulador',
                              'tbl_heroes_generalpostulacion.id_postulante',
                              'tbl_proceso_cliente_centrocosto.cliente', 
                              'CONCAT(tbl_proceso_cliente_centrocosto.cod_pcrc," - ",tbl_proceso_cliente_centrocosto.pcrc) AS pcrc',
                              'if(tbl_heroes_generalpostulacion.estado=1,"Abierto","Cerrado") AS estado',
                              'tbl_heroes_generalpostulacion.procesos'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_tipopostulacion',
                                  'tbl_heroes_tipopostulacion.id_tipopostulacion = tbl_heroes_generalpostulacion.id_tipopostulacion')
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_cargospostulacion',
                                  'tbl_heroes_cargospostulacion.id_cargospostulacion = tbl_heroes_generalpostulacion.id_cargospostulacion')
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_ciudadpostulacion',
                                  'tbl_heroes_ciudadpostulacion.id_ciudadpostulacion = tbl_heroes_generalpostulacion.id_ciudadpostulacion')
                            ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_heroes_generalpostulacion.id_dp_clientes
                                      AND  tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_heroes_generalpostulacion.cod_pcrc')
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.id_tipopostulacion',$model->id_tipopostulacion])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.estado',$model->estado])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.id_dp_clientes',$model->id_dp_clientes])
                            ->andfilterwhere(['in','tbl_heroes_generalpostulacion.cod_pcrc',$arrayDataCentroCosto])
                            ->andwhere(['between','tbl_heroes_generalpostulacion.fechacreacion',$varFechaInicio_BD,$varFechaFin_BD])
                            ->all(); 

        $varCantidadTotal = count($varDataResultado);

        $varCantidadEstados = (new \yii\db\Query())
                            ->select([
                              'if(tbl_heroes_generalpostulacion.estado=1,"Abierto","Cerrado") AS estado', 
                              'COUNT(tbl_heroes_generalpostulacion.estado) as cantidad'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.id_tipopostulacion',$model->id_tipopostulacion])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.estado',$model->estado])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.id_dp_clientes',$model->id_dp_clientes])
                            ->andfilterwhere(['in','tbl_heroes_generalpostulacion.cod_pcrc',$arrayDataCentroCosto])
                            ->andwhere(['between','tbl_heroes_generalpostulacion.fechacreacion',$varFechaInicio_BD,$varFechaFin_BD])
                            ->groupby(['tbl_heroes_generalpostulacion.estado'])
                            ->all(); 


        $varCantidadTipos = (new \yii\db\Query())
                            ->select([ 
                              'tbl_heroes_tipopostulacion.tipopostulacion',
                              'count(tbl_heroes_tipopostulacion.id_tipopostulacion) as cantidadtipo',
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_tipopostulacion',
                                  'tbl_heroes_tipopostulacion.id_tipopostulacion = tbl_heroes_generalpostulacion.id_tipopostulacion')
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.id_tipopostulacion',$model->id_tipopostulacion])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.estado',$model->estado])
                            ->andfilterwhere(['=','tbl_heroes_generalpostulacion.id_dp_clientes',$model->id_dp_clientes])
                            ->andfilterwhere(['in','tbl_heroes_generalpostulacion.cod_pcrc',$arrayDataCentroCosto])
                            ->andwhere(['between','tbl_heroes_generalpostulacion.fechacreacion',$varFechaInicio_BD,$varFechaFin_BD])
                            ->groupby(['tbl_heroes_tipopostulacion.id_tipopostulacion'])
                            ->all(); 


        $varCantidadValores = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_valoracionpostulacion.id_valoracionpostulacion'
                            ])
                            ->from(['tbl_ejecucionformularios'])
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_valoracionpostulacion',
                                  'tbl_ejecucionformularios.id = tbl_heroes_valoracionpostulacion.id_valoracion')
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_generalpostulacion',
                                  'tbl_heroes_valoracionpostulacion.id_generalpostulacion = tbl_heroes_generalpostulacion.id_generalpostulacion')
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0 ])
                            ->andwhere(['=','tbl_heroes_valoracionpostulacion.anulado',0])
                            ->groupby(['tbl_heroes_valoracionpostulacion.id_valoracionpostulacion'])
                            ->count(); 

      }

      return $this->render('reportepostulacion',[
        'model' => $model,
        'varEstados' => $varEstados,
        'varDataResultado' => $varDataResultado,
        'varCantidadTotal' => $varCantidadTotal,
        'varCantidadEstados' => $varCantidadEstados,
        'varCantidadValores' => $varCantidadValores,
        'varCantidadTipos' => $varCantidadTipos,
      ]);
    }

    public function actionPostulajarvis($evaluado_usuared){
      $model = new HeroesGeneralpostulacion();
      $varNombreCargo = null;
      $varIdCargo = null;
      $varNombreCliente_Asesor = null;
      $varIdDpCliente_Asesor = null;
      $varCentroCosto_Asesor = null;
      $varidCodPcrc_Asesor = null;

      $paramsBuscaDocumento = [':Documento'=>base64_decode($evaluado_usuared)];
      $varDocumento = Yii::$app->dbjarvis->createCommand('
        SELECT dp_usuarios_red.documento FROM dp_usuarios_red
        WHERE 
          dp_usuarios_red.usuario_red = :Documento
        GROUP BY dp_usuarios_red.documento ')->bindValues($paramsBuscaDocumento)->queryScalar();

      if ($varDocumento == null) {
        $varDocumento = Yii::$app->dbjarvis->createCommand('
        SELECT dp_usuarios_actualizacion.documento FROM dp_usuarios_actualizacion
        WHERE 
          dp_usuarios_actualizacion.usuario = :Documento
        GROUP BY dp_usuarios_actualizacion.documento ')->bindValues($paramsBuscaDocumento)->queryScalar();
      }


      $varUsuario = (new \yii\db\Query())
                            ->select([
                              'tbl_usuarios.usua_id'
                            ])
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_identificacion',$varDocumento ])
                            ->scalar();

      if ($varUsuario != null) {
        return $this->redirect(array('registrarpostulacion','id_procesos'=>2));
      }

      $varUsuario = (new \yii\db\Query())
                            ->select([
                              'tbl_evaluados.id'
                            ])
                            ->from(['tbl_evaluados'])
                            ->where(['=','tbl_evaluados.identificacion',$varDocumento])
                            ->scalar(); 

      $varNombrePotulador = (new \yii\db\Query())
                            ->select([
                              'tbl_evaluados.name'
                            ])
                            ->from(['tbl_evaluados'])
                            ->where(['=','tbl_evaluados.identificacion',$varDocumento])
                            ->scalar(); 

      $varCargosPostula = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_cargospostulacion.id_cargospostulacion',
                              'tbl_heroes_cargospostulacion.cargospostulacion'
                            ])
                            ->from(['tbl_heroes_cargospostulacion'])
                            ->where(['=','tbl_heroes_cargospostulacion.anulado',0])
                            ->andwhere(['=','tbl_heroes_cargospostulacion.id_cargospostulacion',4])
                            ->all();
      foreach ($varCargosPostula as $value) {
        $varNombreCargo = $value['cargospostulacion'];
        $varIdCargo = $value['id_cargospostulacion'];
       } 

      $varDataClientes = (new \yii\db\Query())
                            ->select([
                              'tbl_proceso_cliente_centrocosto.cliente',
                              'tbl_proceso_cliente_centrocosto.id_dp_clientes',
                              'tbl_proceso_cliente_centrocosto.cod_pcrc',
                              'CONCAT(tbl_proceso_cliente_centrocosto.cod_pcrc," - ",tbl_proceso_cliente_centrocosto.pcrc) AS pcrc'
                            ])
                            ->from(['tbl_distribucion_asesores'])
                            ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                  'tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_distribucion_asesores.cod_pcrc')
                            ->where(['=','tbl_distribucion_asesores.anulado',0])
                            ->andwhere(['=','tbl_distribucion_asesores.cedulaasesor',$varDocumento])
                            ->groupby(['tbl_proceso_cliente_centrocosto.cod_pcrc'])
                            ->all(); 
      foreach ($varDataClientes as $value) {
        $varNombreCliente_Asesor = $value['cliente'];
        $varIdDpCliente_Asesor = $value['id_dp_clientes'];
        $varCentroCosto_Asesor = $value['pcrc'];
        $varidCodPcrc_Asesor = $value['cod_pcrc'];
      }     
      
      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varFechaInteraccion = date("Y-m-d H:i:s",strtotime($model->fecha_interaccion));  


        Yii::$app->db->createCommand()->insert('tbl_heroes_generalpostulacion',[
                      'id_tipopostulacion' => $model->id_tipopostulacion,
                      'id_postulador' => $model->id_postulador, 
                      'id_cargospostulacion' => $model->id_cargospostulacion,
                      'id_postulante' => $model->id_postulante,
                      'id_dp_clientes' => $model->id_dp_clientes,
                      'cod_pcrc' => $model->cod_pcrc,
                      'id_ciudadpostulacion' => $model->id_ciudadpostulacion,
                      'fecha_interaccion' => $varFechaInteraccion,
                      'ext_interaccion' => $model->ext_interaccion,
                      'usuario_interaccion' => $model->usuario_interaccion,
                      'historia_interaccion' => $model->historia_interaccion,
                      'idea_postulacion' => $model->idea_postulacion,  
                      'estado' => 1,
                      'procesos' => 2,
                      'tipo_postulante' => 2,
                      'anulado' => 0,
                      'usua_id' =>  $model->id_postulador,   
                      'fechacreacion' => date('Y-m-d'),                
        ])->execute();
        
        
        $varBaseCod = base64_encode($varDocumento);
        return $this->redirect(array('agradecerpostulacion','documento_asesor'=>$varBaseCod));
                
      }


      return $this->render('postulajarvis',[
        'model' => $model,
        'varUsuario' => $varUsuario,
        'varNombrePotulador' => $varNombrePotulador,
        'varNombreCargo' => $varNombreCargo,
        'varIdCargo' => $varIdCargo,
        'varNombreCliente_Asesor' => $varNombreCliente_Asesor,
        'varIdDpCliente_Asesor' => $varIdDpCliente_Asesor,
        'varCentroCosto_Asesor' => $varCentroCosto_Asesor,
        'varidCodPcrc_Asesor' => $varidCodPcrc_Asesor,
      ]);
    }

    public function actionAgradecerpostulacion($documento_asesor){
      $varDocumentos = [':varDocumentoName'=>base64_decode($documento_asesor)];

      $varNameJarvis = Yii::$app->dbjarvis->createCommand('
        SELECT dp_datos_generales.primer_nombre FROM dp_datos_generales
        WHERE 
          dp_datos_generales.documento = :varDocumentoName
        GROUP BY dp_datos_generales.documento ')->bindValues($varDocumentos)->queryScalar();

      return $this->render('agradecerpostulacion',[
        'varNameJarvis' => $varNameJarvis,
      ]);
    }
    

    public function actionEditarpostulacion(){
      $model = new HeroesGeneralpostulacion();
      $varDataResultado_editar = array();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varFecha_BD = explode(" ", $model->fechacreacion);

        $varFechaInicio_BD = $varFecha_BD[0];
        $varFechaFin_BD = date('Y-m-d',strtotime($varFecha_BD[2]));
    

        $varDataResultado_editar = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_generalpostulacion.id_generalpostulacion', 
                              'tbl_heroes_generalpostulacion.fechacreacion',
                              'tbl_heroes_tipopostulacion.tipopostulacion',
                              'tbl_heroes_cargospostulacion.cargospostulacion', 
                              'tbl_heroes_generalpostulacion.id_postulador',
                              'tbl_heroes_generalpostulacion.id_postulante',
                              'tbl_proceso_cliente_centrocosto.cliente', 
                              'CONCAT(tbl_proceso_cliente_centrocosto.cod_pcrc," - ",tbl_proceso_cliente_centrocosto.pcrc) AS pcrc',
                              'if(tbl_heroes_generalpostulacion.estado=1,"Abierto","Cerrado") AS estado',
                              'tbl_heroes_generalpostulacion.procesos'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_tipopostulacion',
                                  'tbl_heroes_tipopostulacion.id_tipopostulacion = tbl_heroes_generalpostulacion.id_tipopostulacion')
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_cargospostulacion',
                                  'tbl_heroes_cargospostulacion.id_cargospostulacion = tbl_heroes_generalpostulacion.id_cargospostulacion')
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_ciudadpostulacion',
                                  'tbl_heroes_ciudadpostulacion.id_ciudadpostulacion = tbl_heroes_generalpostulacion.id_ciudadpostulacion')
                            ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_heroes_generalpostulacion.id_dp_clientes
                                      AND  tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_heroes_generalpostulacion.cod_pcrc')
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andwhere(['between','tbl_heroes_generalpostulacion.fechacreacion',$varFechaInicio_BD,$varFechaFin_BD])
                            ->all(); 
      }

      return $this->render('editarpostulacion',[
        'model' => $model,
        'varDataResultado_editar' => $varDataResultado_editar,
      ]);
    }

    public function actionCambiarpostulacion($id_postulacion){
      $model = new HeroesGeneralpostulacion();


      $varDataLista_Editar = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_generalpostulacion.id_tipopostulacion',
                              'tbl_heroes_tipopostulacion.tipopostulacion',
                              'tbl_heroes_generalpostulacion.id_postulador',
                              'tbl_heroes_cargospostulacion.cargospostulacion',
                              'tbl_heroes_generalpostulacion.id_postulante',
                              'tbl_proceso_cliente_centrocosto.cliente',
                              'tbl_proceso_cliente_centrocosto.cod_pcrc',
                              'tbl_heroes_ciudadpostulacion.ciudadpostulacion',
                              'tbl_heroes_generalpostulacion.fecha_interaccion',
                              'tbl_heroes_generalpostulacion.ext_interaccion',
                              'tbl_heroes_generalpostulacion.usuario_interaccion',
                              'tbl_heroes_generalpostulacion.historia_interaccion',
                              'tbl_heroes_generalpostulacion.idea_postulacion',
                              'tbl_heroes_generalpostulacion.estado',
                              'tbl_heroes_generalpostulacion.procesos',
                              'tbl_heroes_generalpostulacion.tipo_postulante'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])

                            ->join('LEFT OUTER JOIN', 'tbl_heroes_tipopostulacion',
                                  'tbl_heroes_tipopostulacion.id_tipopostulacion = tbl_heroes_generalpostulacion.id_tipopostulacion')

                            ->join('LEFT OUTER JOIN', 'tbl_heroes_cargospostulacion',
                                  'tbl_heroes_cargospostulacion.id_cargospostulacion = tbl_heroes_generalpostulacion.id_cargospostulacion')

                            ->join('LEFT OUTER JOIN', 'tbl_heroes_ciudadpostulacion',
                                  'tbl_heroes_ciudadpostulacion.id_ciudadpostulacion = tbl_heroes_generalpostulacion.id_ciudadpostulacion')

                            ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_heroes_generalpostulacion.id_dp_clientes
                                    AND tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_heroes_generalpostulacion.cod_pcrc')

                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andwhere(['=','tbl_heroes_generalpostulacion.id_generalpostulacion',$id_postulacion])
                            ->groupby(['tbl_heroes_generalpostulacion.cod_pcrc'])
                            ->all(); 

      $varid_tipopostulacion_Editar = null;
      $vartipopostulacion_Editar = null;
      $varNombrePotulador_Editar = null;
      $varcargospostulacion_Editar = null;
      $varNombrePostulado_Editar = null;
      $varcliente_Editar = null;
      $varcod_pcrc_Editar = null;
      $varciudadpostulacion_Editar = null;
      $varfecha_interaccion_Editar = null;
      $varext_interaccion_Editar = null;
      $varext_interaccion_Editar = null;
      $varusuario_interaccion_Editar = null;
      $varhistoria_interaccion_Editar = null;
      $varidea_postulacion_Editar = null;
      $varestado_Editar = null;
      $varprocesos_Editar = null;
      foreach ($varDataLista_Editar as $value) {
        $varid_tipopostulacion_Editar = $value['id_tipopostulacion'];
        $vartipopostulacion_Editar = $value['tipopostulacion'];

        $varprocesos_Editar = $value['procesos'];
        if ($varprocesos_Editar == 2) {
          $varNombrePotulador_Editar = (new \yii\db\Query())
                                    ->select([
                                      'tbl_evaluados.name'
                                    ])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','tbl_evaluados.id',$value['id_postulador'] ])
                                    ->scalar();

          $varNombrePostulado_Editar = $varNombrePotulador_Editar;

        }else{
          $varNombrePotulador_Editar = (new \yii\db\Query())
                                    ->select([
                                      'tbl_usuarios.usua_nombre'
                                    ])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','tbl_usuarios.usua_id',$value['id_postulador'] ])
                                    ->scalar();
          

          if ($value['tipo_postulante'] == 1) {
            $varNombrePostulado_Editar = (new \yii\db\Query())
                                    ->select([
                                      'tbl_usuarios.usua_nombre'
                                    ])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','tbl_usuarios.usua_id',$value['id_postulante'] ])
                                    ->scalar();
          }else{
            $varNombrePostulado_Editar = (new \yii\db\Query())
                                    ->select([
                                      'tbl_evaluados.name'
                                    ])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','tbl_evaluados.id',$value['id_postulante'] ])
                                    ->scalar();
          }


        }


        $varcargospostulacion_Editar = $value['cargospostulacion'];
        $varcliente_Editar = $value['cliente'];
        $varcod_pcrc_Editar = $value['cod_pcrc'];
        $varciudadpostulacion_Editar = $value['ciudadpostulacion'];
        $varfecha_interaccion_Editar = $value['fecha_interaccion'];
        $varext_interaccion_Editar = $value['ext_interaccion'];
        $varusuario_interaccion_Editar = $value['usuario_interaccion'];
        $varhistoria_interaccion_Editar = $value['historia_interaccion'];
        $varidea_postulacion_Editar = $value['idea_postulacion'];
        $varestado_Editar = $value['estado'];
        
      }

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        
        Yii::$app->db->createCommand()->update('tbl_heroes_generalpostulacion',[
                    'fecha_interaccion' => $model->fecha_interaccion,    
                    'ext_interaccion' => $model->ext_interaccion,
                    'usuario_interaccion' => $model->usuario_interaccion,
                    'historia_interaccion' => $model->historia_interaccion,
                    'idea_postulacion' => $model->idea_postulacion,                              
                ],'id_generalpostulacion ='.$id_postulacion.'')->execute();

        return $this->redirect(['index']);
      }

      return $this->render('cambiarpostulacion',[        
        'model' => $model,
        'varid_tipopostulacion_Editar' => $varid_tipopostulacion_Editar,
        'vartipopostulacion_Editar' => $vartipopostulacion_Editar,
        'varNombrePotulador_Editar' => $varNombrePotulador_Editar,
        'varcargospostulacion_Editar' => $varcargospostulacion_Editar,
        'varNombrePostulado_Editar' => $varNombrePostulado_Editar,
        'varcliente_Editar' => $varcliente_Editar,
        'varcod_pcrc_Editar' => $varcod_pcrc_Editar,
        'varciudadpostulacion_Editar' => $varciudadpostulacion_Editar,
        'varfecha_interaccion_Editar' => $varfecha_interaccion_Editar,
        'varext_interaccion_Editar' => $varext_interaccion_Editar,
        'varext_interaccion_Editar' => $varext_interaccion_Editar,
        'varusuario_interaccion_Editar' => $varusuario_interaccion_Editar,
        'varhistoria_interaccion_Editar' => $varhistoria_interaccion_Editar,
        'varidea_postulacion_Editar' => $varidea_postulacion_Editar,
        'varestado_Editar' => $varestado_Editar,
        'varprocesos_Editar' => $varprocesos_Editar,
      ]);
    }

    public function actionVerpostulacion($embajadorpostular,$id_postulacion,$id_procesos){
      $model = new HeroesGeneralpostulacion();
      $varDocumento_ver = null;
      if ($id_procesos == '2') {
        $varDocumento_ver = (new \yii\db\Query())
                            ->select([
                              'tbl_evaluados.identificacion'
                            ])
                            ->from(['tbl_evaluados'])
                            ->where(['=','tbl_evaluados.id',$embajadorpostular])
                            ->scalar(); 
      }else{
        $varDocumento_ver = (new \yii\db\Query())
                            ->select([
                              'tbl_usuarios.usua_identificacion'
                            ])
                            ->from(['tbl_usuarios'])
                            ->where(['=','tbl_usuarios.usua_id',$embajadorpostular ])
                            ->scalar();
      }

      $varDocumentos_ver = [':varDocumentoName_ver'=>$varDocumento_ver];

      $varNameJarvis_ver = Yii::$app->dbjarvis->createCommand('
        SELECT dp_datos_generales.primer_nombre FROM dp_datos_generales
        WHERE 
          dp_datos_generales.documento = :varDocumentoName_ver
        GROUP BY dp_datos_generales.documento ')->bindValues($varDocumentos_ver)->queryScalar();


      $varDataLista_Ver = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_generalpostulacion.id_tipopostulacion',
                              'tbl_heroes_tipopostulacion.tipopostulacion',
                              'tbl_heroes_generalpostulacion.id_postulador',
                              'tbl_heroes_cargospostulacion.cargospostulacion',
                              'tbl_heroes_generalpostulacion.id_postulante',
                              'tbl_proceso_cliente_centrocosto.cliente',
                              'tbl_proceso_cliente_centrocosto.cod_pcrc',
                              'tbl_heroes_ciudadpostulacion.ciudadpostulacion',
                              'tbl_heroes_generalpostulacion.fecha_interaccion',
                              'tbl_heroes_generalpostulacion.ext_interaccion',
                              'tbl_heroes_generalpostulacion.usuario_interaccion',
                              'tbl_heroes_generalpostulacion.historia_interaccion',
                              'tbl_heroes_generalpostulacion.idea_postulacion',
                              'tbl_heroes_generalpostulacion.estado',
                              'tbl_heroes_generalpostulacion.procesos',
                              'tbl_heroes_generalpostulacion.tipo_postulante'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])

                            ->join('LEFT OUTER JOIN', 'tbl_heroes_tipopostulacion',
                                  'tbl_heroes_tipopostulacion.id_tipopostulacion = tbl_heroes_generalpostulacion.id_tipopostulacion')

                            ->join('LEFT OUTER JOIN', 'tbl_heroes_cargospostulacion',
                                  'tbl_heroes_cargospostulacion.id_cargospostulacion = tbl_heroes_generalpostulacion.id_cargospostulacion')

                            ->join('LEFT OUTER JOIN', 'tbl_heroes_ciudadpostulacion',
                                  'tbl_heroes_ciudadpostulacion.id_ciudadpostulacion = tbl_heroes_generalpostulacion.id_ciudadpostulacion')

                            ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                  'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_heroes_generalpostulacion.id_dp_clientes
                                    AND tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_heroes_generalpostulacion.cod_pcrc')

                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andwhere(['=','tbl_heroes_generalpostulacion.id_generalpostulacion',$id_postulacion])
                            ->groupby(['tbl_heroes_generalpostulacion.cod_pcrc'])
                            ->all(); 

      $varid_tipopostulacion = null;
      $vartipopostulacion = null;
      $varNombrePotulador_ver = null;
      $varcargospostulacion = null;
      $varNombrePostulado_ver = null;
      $varcliente = null;
      $varcod_pcrc = null;
      $varciudadpostulacion = null;
      $varfecha_interaccion = null;
      $varext_interaccion = null;
      $varext_interaccion = null;
      $varusuario_interaccion = null;
      $varhistoria_interaccion = null;
      $varidea_postulacion = null;
      $varestado = null;
      $varprocesos = null;
      foreach ($varDataLista_Ver as $value) {
        $varid_tipopostulacion = $value['id_tipopostulacion'];
        $vartipopostulacion = $value['tipopostulacion'];

        $varprocesos = $value['procesos'];
        if ($varprocesos == 2) {
          $varNombrePotulador_ver = (new \yii\db\Query())
                                    ->select([
                                      'tbl_evaluados.name'
                                    ])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','tbl_evaluados.id',$value['id_postulador'] ])
                                    ->scalar();

          $varNombrePostulado_ver = $varNombrePotulador_ver;

        }else{
          $varNombrePotulador_ver = (new \yii\db\Query())
                                    ->select([
                                      'tbl_usuarios.usua_nombre'
                                    ])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','tbl_usuarios.usua_id',$value['id_postulador'] ])
                                    ->scalar();
          

          if ($value['tipo_postulante'] == 1) {
            $varNombrePostulado_ver = (new \yii\db\Query())
                                    ->select([
                                      'tbl_usuarios.usua_nombre'
                                    ])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','tbl_usuarios.usua_id',$value['id_postulante'] ])
                                    ->scalar();
          }else{
            $varNombrePostulado_ver = (new \yii\db\Query())
                                    ->select([
                                      'tbl_evaluados.name'
                                    ])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','tbl_evaluados.id',$value['id_postulante'] ])
                                    ->scalar();
          }


        }


        $varcargospostulacion = $value['cargospostulacion'];
        $varcliente = $value['cliente'];
        $varcod_pcrc = $value['cod_pcrc'];
        $varciudadpostulacion = $value['ciudadpostulacion'];
        $varfecha_interaccion = $value['fecha_interaccion'];
        $varext_interaccion = $value['ext_interaccion'];
        $varusuario_interaccion = $value['usuario_interaccion'];
        $varhistoria_interaccion = $value['historia_interaccion'];
        $varidea_postulacion = $value['idea_postulacion'];
        $varestado = $value['estado'];
        
      }


      return $this->render('verpostulacion',[
        'model' => $model,
        'varDocumento_ver' => $varDocumento_ver,
        'varNameJarvis_ver' => $varNameJarvis_ver,
        'varid_tipopostulacion' => $varid_tipopostulacion,
        'vartipopostulacion' => $vartipopostulacion,
        'varNombrePotulador_ver' => $varNombrePotulador_ver,
        'varcargospostulacion' => $varcargospostulacion,
        'varNombrePostulado_ver' => $varNombrePostulado_ver,
        'varcliente' => $varcliente,
        'varcod_pcrc' => $varcod_pcrc,
        'varciudadpostulacion' => $varciudadpostulacion,
        'varfecha_interaccion' => $varfecha_interaccion,
        'varext_interaccion' => $varext_interaccion,
        'varusuario_interaccion' => $varusuario_interaccion,
        'varhistoria_interaccion' => $varhistoria_interaccion,
        'varidea_postulacion' => $varidea_postulacion,
        'varestado' => $varestado,
        'varprocesos' => $varprocesos,
      ]);
    }

    public function actionMasivapostulacion(){
      $model = new FormUploadtigo();

      $varUltimaFecha = (new \yii\db\Query())
                        ->select([
                          'MAX(tbl_heroes_generalpostulacion.fechacreacion)'
                        ])
                        ->from(['tbl_heroes_generalpostulacion'])
                        ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                        ->andwhere(['=','tbl_heroes_generalpostulacion.excel',1])
                        ->scalar();

      $varcantidadMasivo = (new \yii\db\Query())
                        ->select([
                          'tbl_heroes_generalpostulacion.id_generalpostulacion'
                        ])
                        ->from(['tbl_heroes_generalpostulacion'])
                        ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                        ->andwhere(['=','tbl_heroes_generalpostulacion.excel',1])
                        ->count();

      $varListaExcel =  (new \yii\db\Query())
                        ->select([
                          'if(tbl_heroes_generalpostulacion.excel=2,"Normal","Excel") AS varIngreso',
                          'COUNT(tbl_heroes_generalpostulacion.excel) AS varCantidades'
                        ])
                        ->from(['tbl_heroes_generalpostulacion'])
                        ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                        ->groupby(['tbl_heroes_generalpostulacion.excel'])
                        ->all();

      if ($model->load(Yii::$app->request->post())) {
                
        $model->file = UploadedFile::getInstances($model, 'file');

        if ($model->file && $model->validate()) {
                    
          foreach ($model->file as $file) {
            $fecha = date('Y-m-d-h-i-s');
            $user = Yii::$app->user->identity->username;
            $name = $fecha . '-' . $user;
            $file->saveAs('categorias/' . $name . '.' . $file->extension);
            $this->Importarheroes($name);

            return $this->redirect(['masivapostulacion']);
          }
        }
      }

      return $this->render('masivapostulacion',[
        'model' => $model,
        'varUltimaFecha' => $varUltimaFecha,
        'varcantidadMasivo' => $varcantidadMasivo,
        'varListaExcel' => $varListaExcel,
      ]);
    }

    public function Importarheroes($name){
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
      
      for ($row = 3; $row <= $highestRow; $row++) { 
        
        $varTipoPostulacion_excel = (new \yii\db\Query())
                                    ->select([
                                      'tbl_heroes_tipopostulacion.id_tipopostulacion'
                                    ])
                                    ->from(['tbl_heroes_tipopostulacion'])
                                    ->where(['=','tbl_heroes_tipopostulacion.anulado',0])
                                    ->andwhere(['=','tbl_heroes_tipopostulacion.tipopostulacion',$sheet->getCell("A".$row)->getValue()])
                                    ->scalar();

        $varIdTipoPostulante_excel = null;
        $varIdPostulador_excel = null;
        $varIdPostulante_excel = null;
        $varTipoPostulante_excel = $sheet->getCell("C".$row)->getValue();
        if ($varTipoPostulante_excel == "Si" || $varTipoPostulante_excel == "si" || $varTipoPostulante_excel == "SI") {
          $varIdTipoPostulante_excel = 2;

          $varIdCargoPostula_excel = 4;

          $varIdPostulador_excel = (new \yii\db\Query())
                                    ->select([
                                      'tbl_evaluados.id'
                                    ])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','tbl_evaluados.identificacion',$sheet->getCell("B".$row)->getValue()])
                                    ->scalar();

          $varIdPostulante_excel = $varIdPostulador_excel;
        }else{
          $varIdTipoPostulante_excel = 1;

          $varIdCargoPostula_excel = 9;

          $varIdPostulador_excel = (new \yii\db\Query())
                                    ->select([
                                      'tbl_usuarios.usua_id'
                                    ])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','tbl_usuarios.usua_identificacion',$sheet->getCell("B".$row)->getValue()])
                                    ->scalar();

          $varIdPostulante_excel = (new \yii\db\Query())
                                    ->select([
                                      'tbl_evaluados.id'
                                    ])
                                    ->from(['tbl_evaluados'])
                                    ->where(['=','tbl_evaluados.identificacion',$sheet->getCell("D".$row)->getValue()])
                                    ->scalar();

          if ($varIdPostulante_excel == null) {

            $varIdPostulante_excel = (new \yii\db\Query())
                                    ->select([
                                      'tbl_usuarios.usua_id'
                                    ])
                                    ->from(['tbl_usuarios'])
                                    ->where(['=','tbl_usuarios.usua_identificacion',$sheet->getCell("D".$row)->getValue()])
                                    ->scalar();
          }

        }

        $varTxtCliente_excel = $sheet->getCell("E".$row)->getValue();
        $varIdCliente_excel = null;
        if (is_numeric($varTxtCliente_excel)) {
          $varIdCliente_excel = (new \yii\db\Query())
                            ->select([
                              'tbl_proceso_cliente_centrocosto.id_dp_clientes'
                            ])
                            ->from(['tbl_proceso_cliente_centrocosto'])
                            ->where(['=','tbl_proceso_cliente_centrocosto.estado',1])
                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.id_dp_clientes',$varTxtCliente_excel])
                            ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                            ->scalar();
        }else{
          $varIdCliente_excel = (new \yii\db\Query())
                            ->select([
                              'tbl_proceso_cliente_centrocosto.id_dp_clientes'
                            ])
                            ->from(['tbl_proceso_cliente_centrocosto'])
                            ->where(['=','tbl_proceso_cliente_centrocosto.estado',1])
                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.cliente',$varTxtCliente_excel])
                            ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                            ->scalar();
        }

        $varCod_pcrcr_excel = (new \yii\db\Query())
                            ->select([
                              'tbl_proceso_cliente_centrocosto.cod_pcrc'
                            ])
                            ->from(['tbl_proceso_cliente_centrocosto'])
                            ->where(['=','tbl_proceso_cliente_centrocosto.estado',1])
                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                            ->andwhere(['=','tbl_proceso_cliente_centrocosto.cod_pcrc',$sheet->getCell("F".$row)->getValue()])
                            ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                            ->scalar();

        $varIdCiudad_excel = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_ciudadpostulacion.id_ciudadpostulacion'
                            ])
                            ->from(['tbl_heroes_ciudadpostulacion'])
                            ->where(['=','tbl_heroes_ciudadpostulacion.anulado',0])
                            ->andwhere(['like','tbl_heroes_ciudadpostulacion.ciudadpostulacion',$sheet->getCell("G".$row)->getValue()])
                            ->scalar();

        $varTxtFechas_excel = null;
        $varTxtExtension_excel = null;
        $varTxtUsuario_excel = null;
        $varTxtIdea_excel = null;
        $varTxtHistoria_excel = null;
        if ($varTipoPostulacion_excel == 1) {
          $varTxtIdea_excel = $sheet->getCell("K".$row)->getValue();
        }else{
          if ($varTipoPostulacion_excel == 2) {
            $varDateChange_excel = $sheet->getCell("H".$row)->getValue();
            $varTxtFechas_excel = PHPExcel_Shared_Date::ExcelToPHPObject($varDateChange_excel)->format('Y-m-d H:i:s');

            $varTxtExtension_excel = $sheet->getCell("I".$row)->getValue();
            $varTxtUsuario_excel = $sheet->getCell("J".$row)->getValue();
          }else{
            $varTxtHistoria_excel= $sheet->getCell("L".$row)->getValue();
          }
        }

        Yii::$app->db->createCommand()->insert('tbl_heroes_generalpostulacion',[
          'id_tipopostulacion' => $varTipoPostulacion_excel,
          'id_postulador' => $varIdPostulador_excel, 
          'id_cargospostulacion' => $varIdCargoPostula_excel,
          'id_postulante' => $varIdPostulante_excel,
          'id_dp_clientes' => $varIdCliente_excel,
          'cod_pcrc' => $varCod_pcrcr_excel,
          'id_ciudadpostulacion' => $varIdCiudad_excel,
          'fecha_interaccion' => $varTxtFechas_excel,
          'ext_interaccion' => $varTxtExtension_excel,
          'usuario_interaccion' => $varTxtUsuario_excel,
          'historia_interaccion' => $varTxtHistoria_excel,
          'idea_postulacion' => $varTxtIdea_excel,  
          'estado' => 1,
          'procesos' => $varIdTipoPostulante_excel,
          'excel' => 1,
          'tipo_postulante' => $varIdTipoPostulante_excel,
          'anulado' => 0,
          'usua_id' =>  Yii::$app->user->identity->id,   
          'fechacreacion' => date('Y-m-d'),                
        ])->execute();
        
          
      }
    }

    public function actionValorapostulacion($embajadorpostular,$id_postulacion){
      // Aqui se genera un cambio con el nuevo Escucha Focalizada
      $modelA = new \app\models\Arboles();
      $modelD = new \app\models\Dimensiones();
      $modelE = new \app\models\Evaluados;

      $varPostulacion_id = $id_postulacion;
      $varTipoPostula = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_tipopostulacion.tipopostulacion'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_tipopostulacion',
                                  'tbl_heroes_tipopostulacion.id_tipopostulacion = tbl_heroes_generalpostulacion.id_tipopostulacion')
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andwhere(['=','tbl_heroes_generalpostulacion.id_generalpostulacion',$varPostulacion_id])
                            ->scalar(); 

      $varTipoPostulaId = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_tipopostulacion.id_tipopostulacion'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->join('LEFT OUTER JOIN', 'tbl_heroes_tipopostulacion',
                                  'tbl_heroes_tipopostulacion.id_tipopostulacion = tbl_heroes_generalpostulacion.id_tipopostulacion')
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andwhere(['=','tbl_heroes_generalpostulacion.id_generalpostulacion',$varPostulacion_id])
                            ->scalar(); 

      $varDatosPostula = null;
      if ($varTipoPostulaId == '1') {
        $varDatosPostula = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_generalpostulacion.historia_interaccion'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andwhere(['=','tbl_heroes_generalpostulacion.id_generalpostulacion',$varPostulacion_id])
                            ->scalar(); 
      }
      if ($varTipoPostulaId == '2') {
        $varDatosPostula = (new \yii\db\Query())
                            ->select([
                              'CONCAT("Fecha de interacción ",tbl_heroes_generalpostulacion.fecha_interaccion," y Extensión ",tbl_heroes_generalpostulacion.ext_interaccion) AS varData'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andwhere(['=','tbl_heroes_generalpostulacion.id_generalpostulacion',$varPostulacion_id])
                            ->scalar(); 
      }
      if ($varTipoPostulaId == '3') {
        $varDatosPostula = (new \yii\db\Query())
                            ->select([
                              'tbl_heroes_generalpostulacion.idea_postulacion'
                            ])
                            ->from(['tbl_heroes_generalpostulacion'])
                            ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                            ->andwhere(['=','tbl_heroes_generalpostulacion.id_generalpostulacion',$varPostulacion_id])
                            ->scalar(); 
      }

      $txtEvaluadoid = $embajadorpostular;
      $varNombreAsesor = (new \yii\db\Query())
                            ->select([
                              'tbl_evaluados.name'
                            ])
                            ->from(['tbl_evaluados'])
                            ->where(['=','tbl_evaluados.id',$txtEvaluadoid])
                            ->scalar(); 

      $vardimensiones = 12;
      $varNombreDimension = (new \yii\db\Query())
                            ->select([
                              'tbl_dimensions.name'
                            ])
                            ->from(['tbl_dimensions'])
                            ->where(['=','tbl_dimensions.id',$vardimensiones])
                            ->scalar(); 

      $varArbol_id = 2119;
      $varNombreArbol = (new \yii\db\Query())
                            ->select([
                              'tbl_arbols.name'
                            ])
                            ->from(['tbl_arbols'])
                            ->where(['=','tbl_arbols.id',$varArbol_id])
                            ->scalar(); 

      return $this->render('valorapostulacion',[        
        'modelA' => $modelA,
        'modelD' => $modelD,
        'modelE' => $modelE,
        'txtEvaluadoid' => $txtEvaluadoid,
        'varNombreAsesor' => $varNombreAsesor,
        'vardimensiones' => $vardimensiones,
        'varNombreDimension' => $varNombreDimension,
        'varArbol_id' => $varArbol_id,
        'varNombreArbol' => $varNombreArbol,
        'varPostulacion_id' => $varPostulacion_id,
        'varTipoPostula' => $varTipoPostula,
        'varDatosPostula' => $varDatosPostula,
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
                              ->where(["ejecucionformulario_id" => $TmpForm->ejecucionformulario_id])
                              ->all();
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

      $varExtraerData = explode(": ", $txtFuentes);
      for ($i=0; $i < count($varExtraerData); $i++) { 
        $varIdPostulacionGeneral = $varExtraerData[1];
      }

      Yii::$app->db->createCommand()->insert('tbl_heroes_valoracionpostulacion',[
                                'id_valoracion' => $dataIdFormulario,
                                'id_generalpostulacion' => $varIdPostulacionGeneral,
                                'anulado' => 0,
                                'usua_id' => $txtUsuaid,
                                'fechacreacion' => date('Y-m-d'),                                       
      ])->execute();

      Yii::$app->db->createCommand()->update('tbl_heroes_generalpostulacion',[
                    'estado' => 2,                                                
      ],'id_generalpostulacion ='.$varIdPostulacionGeneral.'')->execute();

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

    public function actionDescargarpostulacion(){
      $model = new HeroesGeneralpostulacion();
      $varDataResultado_Descargar = array();
  
      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varFecha_BD_D = explode(" ", $model->fechacreacion);
  
        $varFechaInicio_BD_D = $varFecha_BD_D[0];
        $varFechaFin_BD_D = date('Y-m-d',strtotime($varFecha_BD_D[2]));
  
        $varDataResultado_Descargar = (new \yii\db\Query())
                              ->select([
                                'tbl_heroes_generalpostulacion.id_generalpostulacion', 
                                'tbl_heroes_generalpostulacion.fechacreacion',
                                'tbl_heroes_tipopostulacion.tipopostulacion',
                                'tbl_heroes_cargospostulacion.cargospostulacion', 
                                'tbl_heroes_generalpostulacion.id_postulador',
                                'tbl_heroes_generalpostulacion.id_postulante',
                                'tbl_proceso_cliente_centrocosto.cliente', 
                                'CONCAT(tbl_proceso_cliente_centrocosto.cod_pcrc," - ",tbl_proceso_cliente_centrocosto.pcrc) AS pcrc',
                                'if(tbl_heroes_generalpostulacion.estado=1,"Abierto","Cerrado") AS estado',
                                'tbl_heroes_generalpostulacion.procesos'
                              ])
                              ->from(['tbl_heroes_generalpostulacion'])
                              ->join('LEFT OUTER JOIN', 'tbl_heroes_tipopostulacion',
                                    'tbl_heroes_tipopostulacion.id_tipopostulacion = tbl_heroes_generalpostulacion.id_tipopostulacion')
                              ->join('LEFT OUTER JOIN', 'tbl_heroes_cargospostulacion',
                                    'tbl_heroes_cargospostulacion.id_cargospostulacion = tbl_heroes_generalpostulacion.id_cargospostulacion')
                              ->join('LEFT OUTER JOIN', 'tbl_heroes_ciudadpostulacion',
                                    'tbl_heroes_ciudadpostulacion.id_ciudadpostulacion = tbl_heroes_generalpostulacion.id_ciudadpostulacion')
                              ->join('LEFT OUTER JOIN', 'tbl_proceso_cliente_centrocosto',
                                    'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_heroes_generalpostulacion.id_dp_clientes
                                        AND  tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_heroes_generalpostulacion.cod_pcrc')
                              ->where(['=','tbl_heroes_generalpostulacion.anulado',0])
                              ->andwhere(['between','tbl_heroes_generalpostulacion.fechacreacion',$varFechaInicio_BD_D,$varFechaFin_BD_D])
                              ->all(); 
      }
  
      return $this->render('descargarpostulacion',[
        'model' => $model,
        'varDataResultado_Descargar' => $varDataResultado_Descargar,
      ]);
    }


}

?>


