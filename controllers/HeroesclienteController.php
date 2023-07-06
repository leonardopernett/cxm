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
use GuzzleHttp;
use app\models\PostulacionHeroes;
use app\models\ProcesosVolumendirector;
use app\models\SpeechParametrizar;
use app\models\Formularios;
use Exception;

  class HeroesclienteController extends Controller {

    public function behaviors() {
      return [
          'access' => [
              'class' => \yii\filters\AccessControl::className(),
              'denyCallback' => function ($rule, $action) {
                  $msg = \Yii::t('app', 'The requested Item could not be found.');
                  Yii::$app->session->setFlash('danger', $msg);
                  $url = \yii\helpers\Url::to(['site/index']);
                  return $action->controller->redirect($url);
              },
                      'rules' => [
                          [
                              'actions' => ['index','listarpcrcs','postulajarvis', 'dasheroes', 'detalleheroes',
                              'historico','dasheroes','detalleheroes','evaluadolistmultiple','interaccionmanual','evaluadosbyarbol','guardarpaso2','showformulario','gracias'],
                              'allow' => true,
                          ],
                          [
                              'actions' => ['calculatefeedback', 'equiposlist',
                                  'evaluadolist', 'extractarformulario',
                                  'feedbackexpress', 'historicoformularios',
                                  'lidereslist', 'preguntas', 'promcalificaciones',
                                  'tableroexperiencias', 'updatefeedback',
                                  'usuariolist', 'valorados', 'variables',
                                  'updatefeedbackcm', 'declinaciones', 'satisfaccion',
                                  'controlsatisfaccion', 'historicosatisfaccion', 'dimensionlist', 'evaluadolistmultiple',
                                  'getarboles', 'rollistmultiple', 'reportesegundocalificador'],
                              'allow' => true,
                              'roles' => ['@'],
                              'matchCallback' => function() {
                          return Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast();
                      },
                          ],
                          [
                              'actions' => ['historicoformularios', 'usuariolist',
                                  'lidereslist', 'evaluadolist', 'equiposlist', 'dimensionlist', 'evaluadolistmultiple'
                                  , 'getarboles', 'rollistmultiple'],
                              'allow' => true,
                              'roles' => ['@'],
                              'matchCallback' => function() {
                          return Yii::$app->user->identity->isModificarMonitoreo() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast();
                      },
                          ],
                          [
                              'actions' => ['updatefeedbackcm'],
                              'allow' => true,
                              'roles' => ['@'],
                              'matchCallback' => function() {
                          return Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerusuatlmast();
                      },
                          ],
                      ],
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

        $model = new PostulacionHeroes();
        
        $varTipoPostu = ['Embajadores que Konectan' => 'Embajadores que Konectan', 'Gente Buena,Buena Gente' => 'Gente Buena,Buena Gente', 'Eureka' => 'Eureka'];

        $varTipoCargo = ['Analista de cuenta' => 'Analista de cuenta', 'Coordinador de Operaciones' => 'Coordinador de Operaciones', 'Coordinador de Experiencia' => 'Coordinador de Experiencia','Representante de servicio' => 'Representante de servicio', 'Tutor' => 'Tutor', 'Técnico Valorador' => 'Técnico Valorador', 'Líder de equipo' => 'Líder de equipo'];

        $varCiudad = ['Armenia' => 'Armenia','Bogotá' => 'Bogotá','Cali' => 'Cali','Ibague' => 'Ibague','Manizales' => 'Manizales','Medellín' => 'Medellín','Montería' => 'Montería','Pereira' => 'Pereira'];
 
        $form = Yii::$app->request->post();
          if ($model->load($form) ) {
            Yii::$app->db->createCommand()->insert('tbl_postulacion_heroes',[
              'tipodepostulacion' => $model->tipodepostulacion,
              'nombrepostula' => $model->nombrepostula,
              'cargopostula' => $model->cargopostula,
              'embajadorpostular' => $model->embajadorpostular,
              'ciudad' => $model->ciudad,
              'fechahorapostulacion' => $model->fechahorapostulacion, 
              'extensioniteracion' => $model->extensioniteracion,
              'usuariovivexperiencia' => $model->usuariovivexperiencia,
              'historiabuenagente' => $model->historiabuenagente,
              'idea' => $model->idea,
              'fechacreacion' => date("Y-m-d"),                    
              'anulado' => 0,
              'estado' => "Abierto",
              'valorador' => $model->valorador,
              'pcrc' => $model->pcrc,
              'cod_pcrc' => $model->cod_pcrc,
          ])->execute();

          return $this->redirect(['gracias']);
          }

        return $this->render('index',[
            'model' => $model,
            'varTipoPostu' => $varTipoPostu,
            'varCiudad' => $varCiudad,
            'varTipoCargo' => $varTipoCargo,
            
        ]);
    }
    
    public function actionGracias(){

        $model = new PostulacionHeroes();

        return $this->render('gracias',[
            'model' => $model,
        ]);
    }
    public function actionHistorico(){

       
        $model = new PostulacionHeroes();

        return $this->render('historico',[
            'model' => $model,
        ]);
    }

    public function actionDasheroes(){

      $model = new PostulacionHeroes();
                           
      $varData = (new \yii\db\Query())                                                                    
                        ->select(['rol','embajadorpostular','fechacreacion','id_postulacion','tipodepostulacion','nombrepostula','fechahorapostulacion','extensioniteracion','usuariovivexperiencia','estado','valorador'])
                        ->from(['tbl_postulacion_heroes'])
                        ->all();

      foreach ($varData as $key => $value) {
        $varNombre = "No Encontro Usuario de Red";
        if ($value['rol'] == "") {
          $varNombre = (new \yii\db\Query())
                        ->select(['name'])
                        ->from(['tbl_evaluados'])
                        ->where(['=','tbl_evaluados.id',$value["nombrepostula"]])
                        ->scalar();
        }else{
          $varNombre = (new \yii\db\Query())
                        ->select(['usua_nombre'])
                        ->from(['tbl_usuarios'])
                        ->where(['=','tbl_usuarios.usua_id',$value["nombrepostula"]])
                        ->scalar();
        }
       
          $varNombreValorador = (new \yii\db\Query())
                        ->select(['usua_nombre'])
                        ->from(['tbl_usuarios'])
                        ->where(['=','tbl_usuarios.usua_id',$value["valorador"]])
                        ->scalar();
        

        $varData[$key]['nombrepostula']= $varNombre;
        $varData[$key]['valorador']= $varNombreValorador;
        
      }

      $varTipoPostu = ['Embajadores que Konectan' => 'Embajadores que Konectan', 'Gente Buena,Buena Gente' => 'Gente Buena,Buena Gente', 'Eureka' => 'Eureka'];

      $varTipoEstado  = ['Abierto' => 'Abierto', 'Cerrado' => 'Cerrado'];

      $form = Yii::$app->request->post(); 
        if ($model->load($form) ) {

          $fechaGenrenal = explode(' ',$model->fechahorapostulacion);
          $vartipopostu = $model->tipodepostulacion;
          $vartipoestado = $model->estado;
          $varfechaini = $fechaGenrenal[0];
          $varfechafin =$fechaGenrenal[2];

          $varData = (new \yii\db\Query())                                                                    
                      ->select(['*'])
                      ->from(['tbl_postulacion_heroes'])
                      ->where(['=','anulado',0])
                      ->andfilterwhere(['=','tipodepostulacion',$vartipopostu])
                      ->andfilterwhere(['IN','estado',$vartipoestado])
                      ->andwhere(['BETWEEN','fechacreacion',$varfechaini.' 00:00:00',$varfechafin.' 23:59:59'])
                      ->all();
        }

        return $this->render('dasheroes',[
            'model' => $model,
            'varData' => $varData,
            'varTipoPostu' => $varTipoPostu,
            'varTipoEstado' => $varTipoEstado,
            ]);
    }
    
    public function actionDetalleheroes($id_postulacion){

        $varPcrc = (new \yii\db\Query())
                    ->select(['tbl_procesos_volumendirector.cliente'])
                    ->from(['tbl_postulacion_heroes'])
                    ->join('LEFT JOIN','tbl_procesos_volumendirector',
                    'tbl_procesos_volumendirector.id_dp_clientes = tbl_postulacion_heroes.pcrc')
                    ->where(['=','id_postulacion',$id_postulacion])
                    ->scalar();

        $varDatosDetalle  = (new \yii\db\Query())                                                                    
                    ->select([
                      'id_postulacion','tipodepostulacion','nombrepostula','cargopostula','embajadorpostular','ciudad','extensioniteracion','usuariovivexperiencia','fechahorapostulacion', 'historiabuenagente','idea','pcrc'])
                    ->from(['tbl_postulacion_heroes'])
                    ->where(['=','id_postulacion',$id_postulacion])
                    ->all();

        $varLista =  (new \yii\db\Query())
                      ->select(['*'])
                      ->from(['tbl_postulacion_heroes'])
                      ->where(['=','tbl_postulacion_heroes.anulado',0])
                      ->all();

        foreach ($varLista as $key => $value) {
            $varNombre = "No Encontro Usuario de Red";
          if ($value['rol'] == "") {
            $varNombre = (new \yii\db\Query())
                          ->select(['name'])
                          ->from(['tbl_evaluados'])
                          ->where(['=','tbl_evaluados.id',$varDatosDetalle[0]["nombrepostula"]])
                          ->scalar();
          }else{
            $varNombre = (new \yii\db\Query())
                          ->select(['usua_nombre'])
                          ->from(['tbl_usuarios'])
                          ->where(['=','tbl_usuarios.usua_id',$varDatosDetalle[0]["nombrepostula"]])
                          ->scalar();
          }        
            
        }
        foreach ($varLista as $key => $value) {
            $varNombrePostulador = "No Encontro Usuario de Red";
          if ($value['rol'] == "") {
            $varNombrePostulador = (new \yii\db\Query())
                          ->select(['name'])
                          ->from(['tbl_evaluados'])
                          ->where(['=','tbl_evaluados.id',$varDatosDetalle[0]["embajadorpostular"]])
                          ->scalar();
          }else{
            $varNombrePostulador = (new \yii\db\Query())
                          ->select(['usua_nombre'])
                          ->from(['tbl_usuarios'])
                          ->where(['=','tbl_usuarios.usua_id',$varDatosDetalle[0]["embajadorpostular"]])
                          ->scalar();
          }
          
        }
  
        return $this->render('detalleheroes',[
            'varDatosDetalle' => $varDatosDetalle,
            'varLista' => $varLista,
            'varNombre' => $varNombre,
            'varNombrePostulador' => $varNombrePostulador,
            'varPcrc' => $varPcrc,

        ]);
    }

    public function actionListapostu(){
         

        $model = new TipoPostulacion();

        return $this->render('detalleheroes',[
            'model' => $model,
        ]);
    }

    public function actionEvaluadolistmultiple($search = null, $id = null) {
      if (!Yii::$app->getRequest()->isAjax) {
          return $this->goHome();
      }

      $out = ['more' => false];
      if (!is_null($search)) {
          $data = \app\models\Evaluados::find()
                  ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                  ->where('name LIKE "%' . $search . '%"')
                  ->orderBy('name')
                  ->asArray()
                  ->all();
          $out['results'] = array_values($data);
      } elseif (!empty($id)) {
          $data = \app\models\Evaluados::find()
                  ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                  ->where('tbl_evaluados.id IN (' . $id . ')')
                  ->asArray()
                  ->all();
          $out['results'] = array_values($data);
      } else {
          $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
      }
      echo \yii\helpers\Json::encode($out);
  }

    public function actionPostulajarvis($evaluado_usuared){

        $model = new PostulacionHeroes();
        $id_evaluado = null;     
        $modelEvaluado1 =  null;
        $varMensaje = null;
        $varAsesor = null;
        $varIdDpCliente_Asesor = null;
        $varidCodPcrc_Asesor = null;
        $varNombreCliente_Asesor = null;
        $varCentroCosto_Asesor = null;

        $modelEvaluado = \app\models\Evaluados::findOne(['dsusuario_red' => base64_decode($evaluado_usuared)]);
        
        if ($modelEvaluado == null) {
            $modelUsuarios = \app\models\Usuarios::findOne(['usua_usuario' => base64_decode($evaluado_usuared)]);
            if ($modelUsuarios != null) {
                $id_evaluado = $modelUsuarios->usua_id;
                $modelEvaluado1 = $modelUsuarios->usua_nombre;
            }            
        }else{
            $id_evaluado = $modelEvaluado->id;
            $modelEvaluado1 = $modelEvaluado->name;
            
            $varListaClientes = (new \yii\db\Query())
                ->select([
                    'tbl_proceso_cliente_centrocosto.cliente', 
                    'tbl_proceso_cliente_centrocosto.id_dp_clientes',
                    'CONCAT(tbl_proceso_cliente_centrocosto.cod_pcrc," - ",tbl_proceso_cliente_centrocosto.pcrc) AS pcrc',
                    'tbl_proceso_cliente_centrocosto.cod_pcrc'
                ])
                ->from(['tbl_proceso_cliente_centrocosto'])
                ->join('LEFT OUTER JOIN', 'tbl_distribucion_asesores',
                    'tbl_proceso_cliente_centrocosto.id_dp_clientes = tbl_distribucion_asesores.id_dp_clientes
                        AND tbl_proceso_cliente_centrocosto.cod_pcrc = tbl_distribucion_asesores.cod_pcrc')

                ->where(['=','tbl_distribucion_asesores.cedulaasesor',$modelEvaluado->identificacion])
                ->andwhere(['=','tbl_distribucion_asesores.anulado',0])
                ->all();
            
            if (count($varListaClientes) != 0) {
                foreach ($varListaClientes as $value) {
                    $varNombreCliente_Asesor = $value['cliente'];
                    $varIdDpCliente_Asesor = $value['id_dp_clientes'];
                    $varCentroCosto_Asesor = $value['pcrc'];
                    $varidCodPcrc_Asesor = $value['cod_pcrc'];
                }
            }

            $varAsesor = 1;
        }
        


        if ($id_evaluado == null) {
            $varMensaje = 'Usuario no encontrado, por favor comunicarse con el administrador del sistema de CXM.';
        } else {
            if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                $model->nombrepostula = $id_evaluado;
            }
        }


        $varTipoPostu = ['Embajadores que Konectan' => 'Embajadores que Konectan', 'Gente Buena,Buena Gente' => 'Gente Buena,Buena Gente', 'Eureka' => 'Eureka'];


        $varTipoCargo = ['Analista de cuenta' => 'Analista de cuenta', 'Coordinador de Operaciones' => 'Coordinador de Operaciones', 'Coordinador de Experiencia' => 'Coordinador de Experiencia','Representante de servicio' => 'Representante de servicio', 'Tutor' => 'Tutor', 'Técnico Valorador' => 'Técnico Valorador', 'Líder de equipo' => 'Líder de equipo'];


        $varCiudad = ['Armenia' => 'Armenia','Bogotá' => 'Bogotá','Cali' => 'Cali','Ibague' => 'Ibague','Manizales' => 'Manizales','Medellín' => 'Medellín','Montería' => 'Montería','Pereira' => 'Pereira'];           

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            Yii::$app->db->createCommand()->insert('tbl_postulacion_heroes',[
                'tipodepostulacion' => $model->tipodepostulacion,
                'nombrepostula' => $id_evaluado,
                'cargopostula' => $model->cargopostula,
                'embajadorpostular' => $model->embajadorpostular,
                'ciudad' => $model->ciudad,
                'fechahorapostulacion' => $model->fechahorapostulacion, 
                'extensioniteracion' => $model->extensioniteracion,
                'usuariovivexperiencia' => $model->usuariovivexperiencia,
                'historiabuenagente' => $model->historiabuenagente,
                'idea' => $model->idea,
                'fechacreacion' => date("Y-m-d"),                    
                'anulado' => 0,
                'estado' => "Abierto",
                'valorador' => $model->valorador,
                'pcrc' => $model->pcrc,
                'cod_pcrc' => $model->cod_pcrc,
            ])->execute();

            return $this->redirect(['gracias']);
        }

      return $this->render('postulajarvis',[
        'modelEvaluado' => $id_evaluado,
        'model' => $model,
        'varTipoPostu' => $varTipoPostu,
        'varCiudad' => $varCiudad,
        'varTipoCargo' => $varTipoCargo,
        'modelEvaluado1' => $modelEvaluado1,
        'id_evaluado' => $id_evaluado,
        'varMensaje' => $varMensaje,
        'varAsesor' => $varAsesor,
        'varIdDpCliente_Asesor' => $varIdDpCliente_Asesor,
        'varidCodPcrc_Asesor' => $varidCodPcrc_Asesor,
        'varNombreCliente_Asesor' => $varNombreCliente_Asesor,
        'varCentroCosto_Asesor' => $varCentroCosto_Asesor,
    ]);
  
    } 

    public function actionInteraccionmanual($embajadorpostular) {

      $varNombreAsesor = (new \yii\db\Query())                                                                    
                    ->select(['name'])
                    ->from(['tbl_evaluados'])
                    ->where(['=','id',$embajadorpostular])
                    ->scalar();

      $model = new PostulacionHeroes();
      
          $arbol_id = 2119;
          $infoArbol = \app\models\Arboles::findOne(["id" => $arbol_id]);
          $dimension_id = 12;
          $nmArbol = \app\models\Arboles::findOne($arbol_id);
          $nmDimension = \app\models\Dimensiones::findOne($dimension_id);
          $modelE = new \app\models\Evaluados;
          $formulario_id = $infoArbol->formulario_id;

          

          return $this->render("show-paso2", [
                      "arbol_id" => $arbol_id,
                      "nmArbol" => $nmArbol,
                      "dimension_id" => $dimension_id,
                      "nmDimension" => $nmDimension,
                      "formulario_id" => $formulario_id,
                      "model" => $model,
                      "modelE" => $modelE,
                      'varNombreAsesor' => $varNombreAsesor,
                      'embajadorpostular' =>   $embajadorpostular,
          ]);
    }

    public function actionEvaluadosbyarbol($search = null, $arbol_id = null) {
      $out = ['more' => false];
      if (!is_null($search)) {
          $data = \app\models\Evaluados::find()
                  ->joinWith('equiposevaluados')
                  ->join('INNER JOIN', 'tbl_arbols_equipos', 'tbl_arbols_equipos.equipo_id = tbl_equipos_evaluados.equipo_id'
                  )
                  ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                  ->where('name LIKE "%":search"%" AND tbl_arbols_equipos.arbol_id = :arbol_id')
                  ->addParams([':search'=>$search,':arbol_id'=>$arbol_id])
                  ->orderBy('name')
                  ->asArray()
                  ->all();
          $out['results'] = array_values($data);
      } else {
          $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
      }
      echo \yii\helpers\Json::encode($out);
    }

    public function actionGuardarpaso2($preview = 0) {

      $modelE = new \app\models\Evaluados;;
      $showInteraccion = 0;
      $showBtnIteraccion = 0;

      if (isset($_POST) && !empty($_POST)) {

          $arbol_id = Yii::$app->request->post("arbol_id");
          $dimension_id = Yii::$app->request->post("dimension_id");
          $formulario_id = Yii::$app->request->post("formulario_id");
          $evaluados = Yii::$app->request->post("Evaluados");
          $evaluado_id = $evaluados["evaluado_id"];
          $tipoInteraccion = (isset($_POST["tipo_interaccion"])) ? Yii::$app->request->post("tipo_interaccion") : 1;
          $usua_id = ($preview == 1) ? 0 : Yii::$app->user->identity->id;
          $created = date("Y-m-d H:i:s");
          $sneditable = 1;

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
              date_default_timezone_set('America/Bogota');
              $tmpeje->hora_inicial = date("Y-m-d H:i:s");

              //EN CASO DE SELECCIONAR ITERACCION AUTOMATICA
              //CONSULTAMOS LA ITERACCION
              if ($tipoInteraccion == 0) {
                  //CONSULTA DE LLAMADAS Y PANTALLAS CON WS
                  try {
                      $modelFormularios = new Formularios;
                      $enlaces = $modelFormularios->getEnlaces($evaluado_id);
                      if ($enlaces && count($enlaces) > 0) {
                          $json = json_encode($enlaces);
                          $tmpeje->url_llamada = $json;
                      }
                  } catch (\Exception $exc) {
                      \Yii::error('#####' . __FILE__ . ':' . __LINE__
                              . $exc->getMessage() . '#####', 'redbox');
                      $msg = Yii::t('app', 'Error redbox');
                      Yii::$app->session->setFlash('danger', $msg);
                  }

                  $showInteraccion = 1;
                  $showBtnIteraccion = 1;
              } else {
                  $showInteraccion = 0;
                  $showBtnIteraccion = 0;
              }
              $tmpeje->tipo_interaccion = $tipoInteraccion;
              $tmpeje->save();
              $idTmp = $tmpeje->id;
          } else {
              $idTmp = $idTmpForm->id;
              //EN CASO DE SELECCIONAR ITERACCION MANUAL
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
                      } else {
                          date_default_timezone_set('America/Bogota');
                          $idTmpForm->hora_inicial = date("Y-m-d H:i:s");
                          $idTmpForm->url_llamada = "";
                          $idTmpForm->tipo_interaccion = $tipoInteraccion;
                          $idTmpForm->save();
                          $msg = Yii::t('app', 'Error redbox');
                          Yii::$app->session->setFlash('danger', $msg);
                      }
                  } catch (\Exception $exc) {
                      \Yii::error('#####' . __FILE__ . ':' . __LINE__
                              . $exc->getMessage() . '#####', 'redbox');
                      $msg = Yii::t('app', 'Error redbox');
                      Yii::$app->session->setFlash('danger', $msg);
                  }
                  // SI ES MANUAL
              } elseif ($tipoInteraccion == 1) {
                  $idTmpForm->url_llamada = '';
                  $idTmpForm->tipo_interaccion = $tipoInteraccion;
                  date_default_timezone_set('America/Bogota');
                  $idTmpForm->hora_inicial = date("Y-m-d H:i:s");

                  $idTmpForm->save();
                  $showInteraccion = 0;
                  $showBtnIteraccion = 0;
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

    $formulario = 2378;

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
      }else{
          #code
      }


      $varIdformu = Yii::$app->db->createCommand("select ejecucionformulario_id from tbl_tmpejecucionformularios where id = :formulario_id")
      ->bindValue(':formulario_id',$formulario_id)
      ->queryScalar();
    //DATOS GENERALES

      $varidarbol = Yii::$app->db->createCommand("select a.id FROM tbl_arbols a INNER JOIN tbl_arbols b ON a.id = b.arbol_id WHERE b.id = :TmpFormarbol_id")
      ->bindValue(':TmpFormarbol_id',$TmpForm->arbol_id)
      ->queryScalar();

      $varIdclienteSel = Yii::$app->db->createCommand("select LEFT(ltrim(name),3) FROM tbl_arbols a WHERE a.id = :TmpFormarbol_id")
      ->bindValue(':TmpFormarbol_id',$TmpForm->arbol_id)
      ->queryScalar();

      $varIdcliente = Yii::$app->db->createCommand("select id_dp_clientes from tbl_registro_ejec_cliente where anulado = 0 and ejec_form_id = :varIdformu")
      ->bindValue(':varIdformu',$varIdformu)
      ->queryScalar();
      $varCodpcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_registro_ejec_cliente where anulado = 0 and ejec_form_id = :varIdformu")
      ->bindValue(':varIdformu',$varIdformu)
      ->queryScalar();
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

    public function actionListarpcrcs(){
        $txtanulado = 0;
        $txtidcliente = Yii::$app->request->get('id');


          if ($txtidcliente) {
            $txtControl = \app\models\ProcesosClienteCentrocosto::find()->distinct()
                        ->select(['tbl_proceso_cliente_centrocosto.cod_pcrc','tbl_proceso_cliente_centrocosto.pcrc'])->distinct()                                
                        ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtidcliente])
                        ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                        ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])                            
                        ->orderBy(['tbl_proceso_cliente_centrocosto.cod_pcrc' => SORT_DESC])
                        ->all();   

            if ($txtControl > 0) {
              $varListaLideresx = \app\models\ProcesosClienteCentrocosto::find()
                                ->select(['tbl_proceso_cliente_centrocosto.cod_pcrc','tbl_proceso_cliente_centrocosto.pcrc'])->distinct()                                
                                ->where(['tbl_proceso_cliente_centrocosto.id_dp_clientes' => $txtidcliente])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.anulado',0])
                                ->andwhere(['=','tbl_proceso_cliente_centrocosto.estado',1])                          
                                ->orderBy(['tbl_proceso_cliente_centrocosto.cod_pcrc' => SORT_DESC])
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

  }
?>


