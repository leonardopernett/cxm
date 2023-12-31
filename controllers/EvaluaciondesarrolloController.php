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
use app\models\UploadForm2;
use app\models\EvaluacionNivel;
use app\models\EvaluacionNombre;
use app\models\EvaluacionTipoeval;
use app\models\EvaluacionCompetencias;
use app\models\EvaluacionComportamientos;
use app\models\EvaluacionRespuestas;
use app\models\EvaluacionBloque;
use app\models\EvaluacionRespuestas2;
use app\models\EvaluacionNovedadesauto;
use app\models\EvaluacionMensajes;
use app\models\EvaluacionNovedadeslog;
use app\models\EvaluacionCumplimiento;
use app\models\EvaluacionNovedadesgeneral;
use app\models\EvaluacionNovedadespares;
use app\models\EvaluacionDesarrollo;
use app\models\FormUploadtigo;
use yii\base\Exception;

  class EvaluacionDesarrolloController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['importarusuarios','parametrizardatos','createnivel','verniveles','createeval','verevaluacion','createtipo','vertipo','createcompetencia','vercompetencia','createpreguntas','vercomportamiento','usuarios_evalua', 'createrespuestas', 'verrespuesta', 'importarcompetencia','importarcomporta', 'createbloque', 'verbloque','evaluacionauto','createautoeva','createautodesarrollo','novedadauto','evaluacionjefe','createjefeeva','novedadjefe','importarusuarioseval','importarmensaje','createmensaje','evaluacionpar','evaluacionpares','createautopares','createpardesarrollo','createjefedesarrollo','restringirevalua','novedadpares','evaluacioncargo','evaluaciondecargos','createautocargos','createcargodesarrollo','novedadcargos','novedadesglobales','novedadgeneral','editarplannovedad','feedbackresultado','gestionnovedades','createnovedadgeneral', 'evaluacionfeedback', 'validaevaluado', 'crearresultadofb', 'resultadoevaluacion','eliminarnovedades','editarnovedaddelete','paramsevaluacion','exportarrtadashboard','importardocumentos'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isCuadroMando()  || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerevaluacion() || Yii::$app->user->identity->isVerdirectivo();
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

    public function actions() {
      return [
          'error' => [
            'class' => 'yii\web\ErrorAction',
          ]
      ];
  }

  public function actionError() {

      //ERROR PRESENTADO
      $exception = Yii::$app->errorHandler->exception;

      if ($exception !== null) {
          //VARIABLES PARA LA VISTA ERROR
          $code = $exception->statusCode;
          $name = $exception->getName() . " (#$code)";
          $message = $exception->getMessage();
          //VALIDO QUE EL ERROR VENGA DEL CLIENTE DE IVR Y QUE SOLO APLIQUE
          // PARA LOS ERRORES 400
          $request = \Yii::$app->request->pathInfo;
          if ($request == "basesatisfaccion/clientebasesatisfaccion" && $code ==
                  400) {
              //GUARDO EN EL ERROR DE SATU
              $baseSat = new BasesatisfaccionController();
              $baseSat->setErrorSatu(\Yii::$app->request->url, $name . ": " . $message);
          }
          //RENDERIZO LA VISTA
          return $this->render('error', [
                      'name' => $name,
                      'message' => $message,
                      'exception' => $exception,
          ]);
      }
  }
    
    public function actionIndex(){ 
      $model = new EvaluacionNovedadeslog(); 

      return $this->render('index',[
        'model' => $model,
        ]);
    }

    public function actionImportarusuarios(){   
      $model = new UploadForm2(); 

      return $this->renderAjax('importarusuarios',[
        'model' => $model,
        ]);
    }

    public function actionParametrizardatos(){
      $model = new EvaluacionNivel();
      $model2 = new EvaluacionNombre();
      $model3 = new EvaluacionTipoeval();
      $model4 = new EvaluacionCompetencias();
      $model5 = new EvaluacionComportamientos();
      $model6 = new EvaluacionRespuestas();      
      $model7 = new EvaluacionBloque();
      $model8 = new EvaluacionMensajes();

      return $this->render('parametrizardatos',[
        'model' => $model,
        'model2' => $model2,
        'model3' => $model3,
        'model4' => $model4,
        'model5' => $model5,
        'model6' => $model6,
        'model7' => $model7,
        'model8' => $model8,       
        ]); 
    }

    public function actionCreatenivel(){
      $txtvarnivel = Yii::$app->request->get("txtvarnivel");
      $txtvarcargo = Yii::$app->request->get("txtvarcargo");

      $txtvarnamecargo = Yii::$app->get('dbjarvis2')->createCommand("select posicion from dp_posicion where estado = 1 and id_dp_posicion = ':txtvarcargo'")
      ->bindValue(':txtvarcargo', $txtvarcargo)
      ->queryScalar(); 

      $txtverificanivel = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_nivel where anulado = 0 and nivel = ':txtvarnivel' and cargo = ':txtvarcargo'")
      ->bindValue(':txtvarnivel', $txtvarnivel)
      ->bindValue(':txtvarcargo', $txtvarcargo)
      ->queryScalar();

      $txtrta = null;
      if ($txtverificanivel > 0) {
        $txtrta = 1;
      }else{
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_nivel',[
              'nivel' => $txtvarnivel,
              'cargo' => $txtvarcargo,
              'nombrecargo' => $txtvarnamecargo,
              'anulado' => 0,
              'usua_id' => Yii::$app->user->identity->id,
              'fechacrecion' => date("Y-m-d"),
          ])->execute(); 

        Yii::$app->db->createCommand()->insert('tbl_logs', [
          'usua_id' => Yii::$app->user->identity->id,
          'usuario' => Yii::$app->user->identity->username,
          'fechahora' => date('Y-m-d h:i:s'),
          'ip' => Yii::$app->getRequest()->getUserIP(),
          'accion' => 'Create',
          'tabla' => 'tbl_evaluacion_nivel'
        ])->execute();
      }     

      
      die(json_encode($txtrta));
    }

    public function actionVerniveles(){

      return $this->renderAjax('verniveles');
    }

    public function actionCreateeval(){
      $txtvarIdEvaluacion = Yii::$app->request->get("txtvarIdEvaluacion");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_nombre',[
              'nombreeval' => $txtvarIdEvaluacion,
              'anulado' => 0,
              'usua_id' => Yii::$app->user->identity->id,
              'fechacrecion' => date("Y-m-d"),
          ])->execute(); 
          Yii::$app->db->createCommand()->insert('tbl_logs', [
            'usua_id' => Yii::$app->user->identity->id,
            'usuario' => Yii::$app->user->identity->username,
            'fechahora' => date('Y-m-d h:i:s'),
            'ip' => Yii::$app->getRequest()->getUserIP(),
            'accion' => 'Create',
            'tabla' => 'tbl_evaluacion_nombre'
          ])->execute();

      $txtrta = 1;
      die(json_encode($txtrta));
    }

    public function actionVerevaluacion(){

      return $this->renderAjax('verevaluacion');
    }

    public function actionCreatetipo(){
      $txtvarIdTipoEvaluacion = Yii::$app->request->get("txtvarIdTipoEvaluacion");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_tipoeval',[
                                'tipoevaluacion' => $txtvarIdTipoEvaluacion,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacrecion' => date("Y-m-d"),
                            ])->execute(); 

          Yii::$app->db->createCommand()->insert('tbl_logs', [
            'usua_id' => Yii::$app->user->identity->id,
            'usuario' => Yii::$app->user->identity->username,
            'fechahora' => date('Y-m-d h:i:s'),
            'ip' => Yii::$app->getRequest()->getUserIP(),
            'accion' => 'Create',
            'tabla' => 'tbl_evaluacion_tipoeval'
          ])->execute();

      $txtrta = 1;
      die(json_encode($txtrta));
    }

    public function actionVertipo(){

      return $this->renderAjax('vertipo');
    }

    public function actionCreatecompetencia(){
      $txtvaridnivel2 = Yii::$app->request->get("txtvaridnivel2");
      $txtvarIdnamecompetencia = Yii::$app->request->get("txtvarIdnamecompetencia");
      $txtvarIdTipoEvaluacion2 = Yii::$app->request->get("txtvarIdTipoEvaluacion2");
      $txtvarIdBloque = Yii::$app->request->get("txtvarIdBloque");


      Yii::$app->db->createCommand()->insert('tbl_evaluacion_competencias',[
                  'namecompetencia' => $txtvarIdnamecompetencia,
                  'idevaluacionnivel' => $txtvaridnivel2,
                  'idevaluaciontipo' => $txtvarIdTipoEvaluacion2,
                  'idevaluacionbloques' => $txtvarIdBloque,
                  'anulado' => 0,
                  'usua_id' => Yii::$app->user->identity->id,
                  'fechacrecion' => date("Y-m-d"),
              ])->execute(); 

              Yii::$app->db->createCommand()->insert('tbl_logs', [
                'usua_id' => Yii::$app->user->identity->id,
                'usuario' => Yii::$app->user->identity->username,
                'fechahora' => date('Y-m-d h:i:s'),
                'ip' => Yii::$app->getRequest()->getUserIP(),
                'accion' => 'Create',
                'tabla' => 'tbl_evaluacion_competencias'
              ])->execute();

      $txtrta = 1;
      die(json_encode($txtrta));
    }
    public function actionVercompetencia(){

      return $this->renderAjax('vercompetencia');
    }

    public function actionCreatepreguntas(){
      $txtvarIdnamepregunta = Yii::$app->request->get("txtvarIdnamepregunta");
      $txtvaridcompetencia = Yii::$app->request->get("txtvaridcompetencia");
      $txtvarIdEvaluacion2 = Yii::$app->request->get("txtvarIdEvaluacion2");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_comportamientos',[
          'namepregunta' => $txtvarIdnamepregunta,
          'idevaluacioncompetencia' => $txtvaridcompetencia,
          'idevaluacionnombre' => $txtvarIdEvaluacion2,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_comportamientos'
      ])->execute();

      $txtrta = 1;
      die(json_encode($txtrta));      
    }


    public function actionCreaterespuestas(){

      $txtvarIdnamerespuesta = Yii::$app->request->get("txtvarIdnamerespuesta");
      $txtvarvalor = Yii::$app->request->get("txtvarvalor");
      $txtvarIdEvaluacion2 = Yii::$app->request->get("txtvarIdEvaluacion2");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_respuestas',[
          'namerespuesta' => $txtvarIdnamerespuesta,
          'valor' => $txtvarvalor,
          'idevaluacionnombre' => $txtvarIdEvaluacion2,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute();
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_respuestas'
      ])->execute();

      $txtrta = 1;
      die(json_encode($txtrta));      
    }

    public function actionVercomportamiento(){

      return $this->renderAjax('vercomportamiento');
    }

    public function actionVerrespuesta(){

      return $this->renderAjax('verrespuesta');
    }
    public function actionVerbloque(){

      return $this->renderAjax('verbloque');
    }

    public function actionUsuarios_evalua(){
      $sessiones = Yii::$app->user->identity->id;
      $txtanulado = 0;
      $txtfechacreacion = date("Y-m-d");

      Yii::$app->db->createCommand("truncate table tbl_usuarios_evalua")->execute();

      $query = Yii::$app->get('dbjarvis2')->createCommand("Select f.nombre_completo as nombre, a.documento as documento, b.id_dp_cargos as idcargo,
      b.id_dp_posicion as idposicion,b.id_dp_funciones as idfuncion,c.posicion as posicion,d.funcion as funcion,
      e.usuario_red as usuariored,	g.email_corporativo as correo, a.documento_jefe as documento_jefe,     
      TRIM(ifnull (if (a.id_dp_centros_costos != 0, dg3.nombre_completo, if (a.id_dp_centros_costos_adm != 0, ar.area_general, 'Sin informaciÃ³n')), 'Sin informaciÃ³n')) AS directorArea,
      TRIM(if (a.cod_pcrc != 0, cl1.cliente, if (a.id_dp_centros_costos != 0, cl2.cliente, if (a.id_dp_centros_costos_adm != 0, ar.area_general, 'Sin informaciÃ³n')))) AS clienteArea 
		    FROM dp_distribucion_personal a  
    
        LEFT JOIN dp_cargos b
        ON b.id_dp_cargos = a.id_dp_cargos
        
        LEFT JOIN dp_posicion c
        ON c.id_dp_posicion = b.id_dp_posicion
        
        LEFT JOIN dp_funciones d
        ON d.id_dp_funciones = b.id_dp_funciones
        
        LEFT JOIN dp_usuarios_red e
        ON e.documento = a.documento
        
        LEFT JOIN dp_datos_generales f
        ON f.documento = a.documento
        
        LEFT JOIN dp_actualizacion_datos g
        ON g.documento = a.documento
        
        LEFT JOIN dp_pcrc AS pc1
		    ON pc1.cod_pcrc = a.cod_pcrc
		  
        LEFT JOIN dp_clientes AS cl1 
		    ON cl1.id_dp_clientes = pc1.id_dp_clientes
        
        LEFT JOIN dp_centros_costos AS cc1 
		    ON cc1.id_dp_centros_costos = a.id_dp_centros_costos
		  
        LEFT JOIN dp_clientes AS cl2 
		    ON cl2.id_dp_clientes = cc1.id_dp_clientes
        
	      LEFT JOIN dp_centros_costos_adm AS ad 
		    ON ad.id_dp_centros_costos_adm = a.id_dp_centros_costos_adm
        
        LEFT JOIN dp_centros_admin_area AS ar 
		    ON ar.id_dp_centros_admin_area = ad.id_dp_centros_admin_area
		  
        LEFT JOIN dp_centros_costos AS cc 
		    ON cc.id_dp_centros_costos = a.id_dp_centros_costos        
        
        LEFT JOIN dp_datos_generales AS dg3 
		    ON dg3.documento = cc.documento_director
        
        WHERE a.fecha_actual = (SELECT config.valor FROM jarvis_configuracion_general as config WHERE config.nombre = 'mes_activo_dp' )
        AND a.id_dp_estados NOT IN (305,317,327)
        AND f.fecha_alta_distribucion <= '2020-12-31'	
        AND c.posicion NOT IN('Aprendiz','Pusher', 'Cliente')	
        AND d.funcion NOT IN('Operaciï¿½n', 'Visitador' COLLATE utf8_unicode_ci)
        
        AND e.fecha_creacion_usuario = ( SELECT MAX(aa.fecha_creacion_usuario) FROM dp_usuarios_red aa WHERE aa.documento = a.documento ) AND b.id_dp_cargos != 39322")->queryAll();

      foreach ($query as $key => $value) {
        $vardocumentojefe = $value['documento_jefe'];
        $query2 = Yii::$app->get('dbjarvis2')->createCommand("Select  distinct f.nombre_completo as nombrejefe, a.documento as documento, b.id_dp_cargos as idcargo, b.id_dp_posicion as idposicion,
                  b.id_dp_funciones as idfuncion,c.posicion as posicion,d.funcion as funcion
                  FROM dp_distribucion_personal a 
                  LEFT JOIN dp_cargos b
                  ON b.id_dp_cargos = a.id_dp_cargos

                  LEFT JOIN dp_posicion c
                  ON c.id_dp_posicion = b.id_dp_posicion

                  LEFT JOIN dp_funciones d
                  ON d.id_dp_funciones = b.id_dp_funciones

		              LEFT JOIN dp_datos_generales f
                  ON f.documento = a.documento

                  WHERE a.documento = ':vardocumentojefe'")
                  ->bindValue(':vardocumentojefe', $vardocumentojefe)
                  ->queryAll();

                  foreach ($query2 as $key => $value2) {
                    $varidcargojefe = $value2['idcargo'];
                    $varcargo = $value2['posicion']." ".$value2['funcion'];
		                $varnombrejefe = $value2['nombrejefe'];
                  }

          Yii::$app->db->createCommand()->insert('tbl_usuarios_evalua',[
                                                   'nombre_completo' => $value['nombre'],
                                                   'documento' => $value['documento'],
                                                   'id_dp_cargos' => $value['idcargo'],
                                                   'id_dp_posicion' => $value['idposicion'],
                                                   'id_dp_funciones' => $value['idfuncion'],
                                                   'posicion' => $value['posicion'],
                                                   'funcion' => $value['funcion'],
                                                   'usuario_red' => $value['usuariored'],
                                                   'email_corporativo' => $value['correo'],
                                                   'documento_jefe' => $value['documento_jefe'],
                                                   'nombre_jefe'  => $varnombrejefe,
                                                   'id_cargo_jefe' => $varidcargojefe,
                                                   'cargo_jefe' => $varcargo,
                                                   'directorarea' => $value['directorArea'],
                                                   'clientearea' => $value['clienteArea'],
                                                   'fechacrecion' => $txtfechacreacion,
                                                   'anulado' => $txtanulado,
                                                   'usua_id' => $sessiones,
                                               ])->execute();

      }

      return $this->redirect('index');    
    }

    public function actionImportarcompetencia(){
      $model = new UploadForm2();
      $txtanulado = 0;
      $txtfechacreacion = date("Y-m-d");
      $sessiones = Yii::$app->user->identity->id;

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

                Yii::$app->db->createCommand()->insert('tbl_evaluacion_competencias',[
                                        'namecompetencia' => $varDatos[0],
                                        'idevaluacionnivel' => $varDatos[1],
                                        'idevaluaciontipo' => $varDatos[2],                                      
                                        'fechacrecion' => $txtfechacreacion,                                      
                                        'anulado' => $txtanulado,
                                        'usua_id' => $sessiones,
                                    ])->execute();                          
           
                } 
          }
          fclose($gestor);

          return $this->redirect('index');
                }
            }
        }

      return $this->renderAjax('importarcompetencia',[
        'model' => $model,
        ]);

    }

    public function actionImportarcomporta(){
      $model = new UploadForm2();
      $txtanulado = 0;
      $txtfechacreacion = date("Y-m-d");
      $sessiones = Yii::$app->user->identity->id;

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

                Yii::$app->db->createCommand()->insert('tbl_evaluacion_comportamientos',[
                                        'namepregunta' => $varDatos[0],
                                        'idevaluacioncompetencia' => $varDatos[1],
                                        'idevaluacionnombre' => $varDatos[2],
                                        'idevaluacionbloques' => $varDatos[3],                         
                                        'fechacrecion' => $txtfechacreacion,                                      
                                        'anulado' => $txtanulado,
                                        'usua_id' => $sessiones,
                                    ])->execute();                          
           
                } 
          }
          fclose($gestor);

          return $this->redirect('index');
                }
            }
        }

      return $this->renderAjax('importarcomporta',[
        'model' => $model,
        ]);

    }

    public function actionCreatebloque(){

      $txtvarIdnamebloque = Yii::$app->request->get("txtvarIdnamebloque");
      $txtvarIdEvaluacion2 = Yii::$app->request->get("txtvarIdEvaluacion2");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_bloques',[
          'namebloque' => $txtvarIdnamebloque,
          'idevaluacionnombre' => $txtvarIdEvaluacion2,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_bloques'
      ])->execute();

      $txtrta = 1;
      die(json_encode($txtrta));      
    }

    public function actionEvaluacionauto(){
      $model = new EvaluacionRespuestas2();

      return $this->render('evaluacionauto',[
        'model' => $model,
        ]);
    }

    public function actionCreateautoeva(){        
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      $txtvaridbloque = Yii::$app->request->get("txtvaridbloque");
      $txtvaridcompetencia = Yii::$app->request->get("txtvaridcompetencia");
      $txtvaridpreg = Yii::$app->request->get("txtvaridpreg");
      $txtvaridrta = Yii::$app->request->get("txtvaridrta");

      $varverifica = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where documento = ':txtvardocumento' and documentoevaluado = ':txtvardocumento' and idevaluacionbloques = ':txtvaridbloque' and idevaluacioncompetencia = ':txtvaridcompetencia' and idevaluacionpregunta = ':txtvaridpreg' and idevaluacionrespuesta = ':txtvaridrta' and anulado = 0")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->bindValue(':txtvaridbloque', $txtvaridbloque)
      ->bindValue(':txtvaridcompetencia', $txtvaridcompetencia)
      ->bindValue(':txtvaridpreg', $txtvaridpreg)
      ->bindValue(':txtvaridrta', $txtvaridrta)
      ->queryScalar();

      if ($varverifica == 0) {
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_solucionado',[
            'documento' => $txtvardocumento,
            'documentoevaluado' => $txtvardocumento,
            'idevaluacionbloques' => $txtvaridbloque,
            'idevaluacioncompetencia' => $txtvaridcompetencia,
            'idevaluacionpregunta' => $txtvaridpreg,
            'idevaluacionrespuesta' => $txtvaridrta,
            'idevaluaciontipo' => 1,
            'comentarios' => null,
            'anulado' => 0,
            'usua_id' => Yii::$app->user->identity->id,
            'fechacrecion' => date("Y-m-d"),
        ])->execute();
        
        Yii::$app->db->createCommand()->insert('tbl_logs', [
          'usua_id' => Yii::$app->user->identity->id,
          'usuario' => Yii::$app->user->identity->username,
          'fechahora' => date('Y-m-d h:i:s'),
          'ip' => Yii::$app->getRequest()->getUserIP(),
          'accion' => 'Create',
          'tabla' => 'tbl_evaluacion_solucionado'
        ])->execute();
      }

      die(json_encode($varverifica)); 

    }

    public function actionCreateautodesarrollo(){
      $txtvarocmentario = Yii::$app->request->get("txtvarocmentario");
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_solucionado',[
          'documento' => $txtvardocumento,
          'documentoevaluado' => $txtvardocumento,
          'idevaluacionbloques' => null,
          'idevaluacioncompetencia' => null,
          'idevaluacionpregunta' => null,
          'idevaluacionrespuesta' => null,
          'idevaluaciontipo' => 1,
          'comentarios' => $txtvarocmentario,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_solucionado'
      ])->execute();

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_desarrollo',[
          'idevaluador' => $txtvardocumento,
          'idevalados' => $txtvardocumento,
          'idevaluaciontipo' => 1,
          'realizada' => 1,
          'comentarios' => null,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_desarrollo'
      ])->execute();

      $varverifica = 1;

      die(json_encode($varverifica)); 

    }

    public function actionNovedadauto(){
      $model = new EvaluacionNovedadesauto();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_novedadesauto',[
                                'documento' => $model->documento,
                                'asunto' => $model->asunto,
                                'comentarios' => $model->comentarios,
                                'cambios' => $model->documento,
                                'aprobado' => 0,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacrecion' => date("Y-m-d"),
                            ])->execute(); 

        return $this->redirect('index');
      }

      return $this->renderAjax('novedadauto',[
        'model' => $model,
        ]);
    }

    public function actionEvaluacionjefe(){
      $model = new EvaluacionRespuestas2();

      return $this->render('evaluacionjefe',[
        'model' => $model,
        ]);
    }

    public function actionCreatejefeeva(){        
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      $txtvaridbloque = Yii::$app->request->get("txtvaridbloque");
      $txtvaridcompetencia = Yii::$app->request->get("txtvaridcompetencia");
      $txtvaridpreg = Yii::$app->request->get("txtvaridpreg");
      $txtvaridrta = Yii::$app->request->get("txtvaridrta");

      $vardocumentjefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua where documento = ':txtvardocumento' group by documento_jefe")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->queryScalar();

      $varverifica = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where documento = ':txtvardocumento' and documentoevaluado = ':vardocumentjefe' and idevaluacionbloques = ':txtvaridbloque' and idevaluacioncompetencia = ':txtvaridcompetencia' and idevaluacionpregunta = ':txtvaridpreg' and idevaluacionrespuesta = ':txtvaridrta' and anulado = 0")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->bindValue(':vardocumentjefe', $vardocumentjefe)
      ->bindValue(':txtvaridbloque', $txtvaridbloque)
      ->bindValue(':txtvaridcompetencia', $txtvaridcompetencia)
      ->bindValue(':txtvaridpreg', $txtvaridpreg)
      ->bindValue(':txtvaridrta', $txtvaridrta)
      ->queryScalar();

      if ($varverifica == 0) {
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_solucionado',[
            'documento' => $txtvardocumento,
            'documentoevaluado' => $vardocumentjefe,
            'idevaluacionbloques' => $txtvaridbloque,
            'idevaluacioncompetencia' => $txtvaridcompetencia,
            'idevaluacionpregunta' => $txtvaridpreg,
            'idevaluacionrespuesta' => $txtvaridrta,
            'idevaluaciontipo' => 2,
            'comentarios' => null,
            'anulado' => 0,
            'usua_id' => Yii::$app->user->identity->id,
            'fechacrecion' => date("Y-m-d"),
        ])->execute(); 

        Yii::$app->db->createCommand()->insert('tbl_logs', [
          'usua_id' => Yii::$app->user->identity->id,
          'usuario' => Yii::$app->user->identity->username,
          'fechahora' => date('Y-m-d h:i:s'),
          'ip' => Yii::$app->getRequest()->getUserIP(),
          'accion' => 'Create',
          'tabla' => 'tbl_evaluacion_solucionado'
        ])->execute();
      }

      die(json_encode($varverifica)); 

    }

    public function actionCreatejefedesarrollo(){
      $txtvarocmentario = Yii::$app->request->get("txtvarocmentario");
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");

      $vardocumentjefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua where documento = ':txtvardocumento' group by documento_jefe")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->queryScalar();

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_solucionado',[
          'documento' => $txtvardocumento,
          'documentoevaluado' => $vardocumentjefe,
          'idevaluacionbloques' => null,
          'idevaluacioncompetencia' => null,
          'idevaluacionpregunta' => null,
          'idevaluacionrespuesta' => null,
          'idevaluaciontipo' => 2,
          'comentarios' => $txtvarocmentario,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_solucionado'
      ])->execute();

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_desarrollo',[
          'idevaluador' => $txtvardocumento,
          'idevalados' => $vardocumentjefe,
          'idevaluaciontipo' => 2,
          'realizada' => 1,
          'comentarios' => null,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute();
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_desarrollo'
      ])->execute(); 


      $varverifica = 1;

      die(json_encode($varverifica)); 

    }

    public function actionNovedadjefe(){
      $model = new EvaluacionNovedadesauto();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_novedadesjefe',[
                                'documento' => $model->documento,
                                'asunto' => $model->asunto,
                                'comentarios' => $model->comentarios,
                                'cambios' => $model->cambios,
                                'aprobado' => 0,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacrecion' => date("Y-m-d"),
                            ])->execute(); 

        return $this->redirect('index');
      }

      return $this->renderAjax('novedadjefe',[
        'model' => $model,
        ]);
    }

    public function actionImportarusuarioseval(){
      $model = new UploadForm2();
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

                Yii::$app->db->createCommand()->insert('tbl_usuarios',[
                                        'usua_usuario' => $varDatos[0],
                                        'usua_nombre' => $varDatos[1],
                                        'usua_email' => $varDatos[2],
                                        'usua_identificacion' => $varDatos[3],
                                        'usua_activo' => $varDatos[4],                                                  
                                        'usua_estado' => $varDatos[5],
                                        'fechacreacion' => $txtfechacreacion,
                                    ])->execute();                          
           
                }
                $vargrupo_id = 1;
                $varrolid = 291;
              for ($c=0; $c < $numero; $c++) {
                  $varArray = $datos[$c]; 
                  $varDatos = explode(";", utf8_encode($varArray));

                  $varidusua = Yii::$app->db->createCommand("select usua_id from tbl_usuarios where usua_usuario = ':varDatos'")
                  ->bindValue(':varDatos', $varDatos[0])
                  ->queryScalar();
              
              
              
                  Yii::$app->db->createCommand()->insert('rel_grupos_usuarios',[
                                      'usuario_id' => intval($varidusua),
                                      'grupo_id' => $vargrupo_id,
                                  ])->execute();
                  Yii::$app->db->createCommand()->insert('rel_usuarios_roles',[
                                      'rel_usua_id' => intval($varidusua),
                                      'rel_role_id' => $varrolid,
                  ])->execute();                          
         
              }

          }
          fclose($gestor);

          return $this->redirect('index');
                }
            }
        }

      return $this->renderAjax('importarusuarioseval',[
        'model' => $model,
        ]);
    }

    public function actionImportarmensaje(){
      $model = new UploadForm2();
      $txtanulado = 0;
      $txtfechacreacion = date("Y-m-d");
      $sessiones = Yii::$app->user->identity->id;

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

              Yii::$app->db->createCommand()->insert('tbl_evaluacion_feedback_mensaje',[
                                      'idevaluacioncompetencia' => $varDatos[0],
                                      'mensaje' => $varDatos[1],
                                      'rol_competencia' => $varDatos[2],
                                      'tipocompetencia' => $varDatos[3],
                                      'idevaluacionnombre' => $varDatos[4],                         
                                      'fechacrecion' => $txtfechacreacion,                                      
                                      'anulado' => $txtanulado,
                                      'usua_id' => $sessiones,
                                  ])->execute();                          
         
              } 
        }
        fclose($gestor);

        return $this->redirect('index');
              }
          }
      }

    return $this->renderAjax('importarmensaje',[
      'model' => $model,
      ]);

  }

    public function actionCreatemensaje(){

      $txtvarIdcompetencia = Yii::$app->request->get("txtvarIdcompetencia");
      $txtvarmensaje = Yii::$app->request->get("txtvarmensaje");
      $txtvarIdcargo = Yii::$app->request->get("txtvarIdcargo");
      $txtvarIdtipocompetencia  = Yii::$app->request->get("txtvarIdtipocompetencia ");
      $txtvarIdEvaluacion2 = Yii::$app->request->get("txtvarIdEvaluacion2");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_feedback_mensaje',[
          'idevaluacioncompetencia' => $txtvarIdcompetencia,
          'mensaje' => $txtvarmensaje,
          'rol_competencia' => $txtvarIdcargo,
          'tipocompetencia' => $txtvarIdtipocompetencia,
          'idevaluacionnombre' => $txtvarIdEvaluacion2,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute();
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_feedback_mensaje'
      ])->execute(); 

      $txtrta = 1;
      die(json_encode($txtrta));      
    }

    public function actionEvaluacionpar(){
      $model = new EvaluacionNovedadesauto();

      $form = Yii::$app->request->post();
        if ($model->load($form)) {
          
          return $this->redirect(array('evaluacionpares','idparams'=>'cod'.$model->documento.'pares'));
        }

      return $this->renderAjax('evaluacionpar',[
        'model' => $model,
        ]);
    }

    public function actionEvaluacionpares($idparams){
      $model = new EvaluacionRespuestas2();
      $vardocument2 =  intval(preg_replace('/[^0-9]+/', '', $idparams), 10);

      return $this->render('evaluacionpares',[
          'model' => $model,
          'vardocument2' => $vardocument2,
          ]);    
    }

    public function actionCreateautopares(){        
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      $txtvaridbloque = Yii::$app->request->get("txtvaridbloque");
      $txtvaridcompetencia = Yii::$app->request->get("txtvaridcompetencia");
      $txtvaridpreg = Yii::$app->request->get("txtvaridpreg");
      $txtvaridrta = Yii::$app->request->get("txtvaridrta");

      $vardocumentjefe = Yii::$app->request->get("txtvardocument2");

      $varverifica = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where documento = ':txtvardocumento' and documentoevaluado = ':vardocumentjefe' and idevaluacionbloques = ':txtvaridbloque' and idevaluacioncompetencia = ':txtvaridcompetencia' and idevaluacionpregunta = ':txtvaridpreg' and idevaluacionrespuesta = ':txtvaridrta' and anulado = 0")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->bindValue(':vardocumentjefe', $vardocumentjefe)
      ->bindValue(':txtvaridbloque', $txtvaridbloque)
      ->bindValue(':txtvaridcompetencia', $txtvaridcompetencia)
      ->bindValue(':txtvaridpreg', $txtvaridpreg)
      ->bindValue(':txtvaridrta', $txtvaridrta)
      ->queryScalar();

      if ($varverifica == 0) {
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_solucionado',[
            'documento' => $txtvardocumento,
            'documentoevaluado' => $vardocumentjefe,
            'idevaluacionbloques' => $txtvaridbloque,
            'idevaluacioncompetencia' => $txtvaridcompetencia,
            'idevaluacionpregunta' => $txtvaridpreg,
            'idevaluacionrespuesta' => $txtvaridrta,
            'idevaluaciontipo' => 4,
            'comentarios' => null,
            'anulado' => 0,
            'usua_id' => Yii::$app->user->identity->id,
            'fechacrecion' => date("Y-m-d"),
        ])->execute(); 

        Yii::$app->db->createCommand()->insert('tbl_logs', [
          'usua_id' => Yii::$app->user->identity->id,
          'usuario' => Yii::$app->user->identity->username,
          'fechahora' => date('Y-m-d h:i:s'),
          'ip' => Yii::$app->getRequest()->getUserIP(),
          'accion' => 'Create',
          'tabla' => 'tbl_evaluacion_solucionado'
        ])->execute(); 
      }

      die(json_encode($varverifica)); 
    }

    public function actionCreatepardesarrollo(){
      $txtvarocmentario = Yii::$app->request->get("txtvarocmentario");
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");

      $vardocumentjefe = Yii::$app->request->get("txtvardocument2");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_solucionado',[
          'documento' => $txtvardocumento,
          'documentoevaluado' => $vardocumentjefe,
          'idevaluacionbloques' => null,
          'idevaluacioncompetencia' => null,
          'idevaluacionpregunta' => null,
          'idevaluacionrespuesta' => null,
          'idevaluaciontipo' => 4,
          'comentarios' => $txtvarocmentario,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_solucionado'
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_desarrollo',[
          'idevaluador' => $txtvardocumento,
          'idevalados' => $vardocumentjefe,
          'idevaluaciontipo' => 4,
          'realizada' => 1,
          'comentarios' => null,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute();
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_desarrollo'
      ])->execute(); 

      $varverifica = 1;

      die(json_encode($varverifica)); 

    }

    public function actionRestringirevalua(){
      $sessiones = Yii::$app->user->identity->id;

      $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = ':sessiones'")
      ->bindValue(':sessiones', $sessiones)
      ->queryScalar();

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_novedadesgeneral',[
                                'documento' => $vardocument,
                                'aprobado' => 1,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacrecion' => date("Y-m-d"),
                            ])->execute(); 

      return $this->redirect('index');

    }

    public function actionVerificapar(){
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      $txtvaridpares = Yii::$app->request->get("txtvaridpares");

      $varconteorta = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where  documento = ':txtvardocumento' and documentoevaluado = ':txtvaridpares' and idevaluaciontipo = 4 and anulado = 0")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->bindValue(':txtvaridpares', $txtvaridpares)
      ->queryScalar();

      die(json_encode($varconteorta)); 

    }

    public function actionNovedadpares($idvardocumentpar){
      $model = new EvaluacionNovedadesauto();
      $varidvardocumentpar = $idvardocumentpar;

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_novedadespares',[
                                'documento' => $model->documento,
                                'asunto' => $model->asunto,
                                'comentarios' => $model->comentarios,
                                'cambios' => $varidvardocumentpar,
                                'aprobado' => 0,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacrecion' => date("Y-m-d"),
                            ])->execute(); 

        return $this->redirect('index');
      }

      return $this->renderAjax('novedadpares',[
        'model' => $model,
      ]);
    }

    public function actionEvaluacioncargo(){
      $model = new EvaluacionNovedadesauto();
      $model2 = new EvaluacionNovedadespares();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
          
        return $this->redirect(array('evaluaciondecargos','idparams'=>'cod'.$model->documento.'cargo'));
      }

      return $this->renderAjax('evaluacioncargo',[
        'model' => $model,
        'model2' => $model2,
        ]);
    }

    public function actionVerificacargo(){
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      $txtvaridpares = Yii::$app->request->get("txtvaridpares");

      $varconteorta = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where  documento = ':txtvardocumento' and documentoevaluado = ':txtvaridpares' and idevaluaciontipo = 3 and anulado = 0")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->bindValue(':txtvaridpares', $txtvaridpares)
      ->queryScalar();

      die(json_encode($varconteorta)); 

    }

    public function actionEvaluaciondecargos($idparams){
      $model = new EvaluacionRespuestas2();
      $vardocument2 =  intval(preg_replace('/[^0-9]+/', '', $idparams), 10);

      return $this->render('evaluaciondecargos',[
          'model' => $model,
          'vardocument2' => $vardocument2,
          ]); 
    }

    public function actionCreateautocargos(){        
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      $txtvaridbloque = Yii::$app->request->get("txtvaridbloque");
      $txtvaridcompetencia = Yii::$app->request->get("txtvaridcompetencia");
      $txtvaridpreg = Yii::$app->request->get("txtvaridpreg");
      $txtvaridrta = Yii::$app->request->get("txtvaridrta");

      $vardocumentjefe = Yii::$app->request->get("txtvardocument2");

      $varverifica = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where documento = ':txtvardocumento' and documentoevaluado = ':vardocumentjefe' and idevaluacionbloques = ':txtvaridbloque' and idevaluacioncompetencia = ':txtvaridcompetencia' and idevaluacionpregunta = ':txtvaridpreg' and idevaluacionrespuesta = ':txtvaridrta' and anulado = 0")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->bindValue(':vardocumentjefe', $vardocumentjefe)
      ->bindValue(':txtvaridbloque', $txtvaridbloque)
      ->bindValue(':txtvaridcompetencia', $txtvaridcompetencia)
      ->bindValue(':txtvaridpreg', $txtvaridpreg)
      ->bindValue(':txtvaridrta', $txtvaridrta)
      ->queryScalar();

      if ($varverifica == 0) {
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_solucionado',[
            'documento' => $txtvardocumento,
            'documentoevaluado' => $vardocumentjefe,
            'idevaluacionbloques' => $txtvaridbloque,
            'idevaluacioncompetencia' => $txtvaridcompetencia,
            'idevaluacionpregunta' => $txtvaridpreg,
            'idevaluacionrespuesta' => $txtvaridrta,
            'idevaluaciontipo' => 3,
            'comentarios' => null,
            'anulado' => 0,
            'usua_id' => Yii::$app->user->identity->id,
            'fechacrecion' => date("Y-m-d"),
        ])->execute(); 

        Yii::$app->db->createCommand()->insert('tbl_logs', [
          'usua_id' => Yii::$app->user->identity->id,
          'usuario' => Yii::$app->user->identity->username,
          'fechahora' => date('Y-m-d h:i:s'),
          'ip' => Yii::$app->getRequest()->getUserIP(),
          'accion' => 'Create',
          'tabla' => 'tbl_evaluacion_solucionado'
        ])->execute(); 
      }

      die(json_encode($varverifica)); 
    }

    public function actionCreatecargodesarrollo(){
      $txtvarocmentario = Yii::$app->request->get("txtvarocmentario");
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");

      $vardocumentjefe = Yii::$app->request->get("txtvardocument2");

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_solucionado',[
          'documento' => $txtvardocumento,
          'documentoevaluado' => $vardocumentjefe,
          'idevaluacionbloques' => null,
          'idevaluacioncompetencia' => null,
          'idevaluacionpregunta' => null,
          'idevaluacionrespuesta' => null,
          'idevaluaciontipo' => 3,
          'comentarios' => $txtvarocmentario,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_solucionado'
      ])->execute(); 

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_desarrollo',[
          'idevaluador' => $txtvardocumento,
          'idevalados' => $vardocumentjefe,
          'idevaluaciontipo' => 3,
          'realizada' => 1,
          'comentarios' => null,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute();
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_desarrollo'
      ])->execute(); 

      $varverifica = 1;

      die(json_encode($varverifica)); 

    }

    public function actionNovedadcargos($idvardocumentpar){
      $model = new EvaluacionNovedadesauto();
      $varidvardocumentpar = $idvardocumentpar;

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_novedadescargo',[
                                'documento' => $model->documento,
                                'asunto' => $model->asunto,
                                'comentarios' => $model->comentarios,
                                'cambios' => $varidvardocumentpar,
                                'aprobado' => 0,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacrecion' => date("Y-m-d"),
                            ])->execute(); 

        return $this->redirect('index');
      }

      return $this->renderAjax('novedadcargos',[
        'model' => $model,
      ]);
    }

    public function actionNovedadgeneral(){
      $model = new EvaluacionTipoeval();
      
      $form = Yii::$app->request->post();
      if ($model->load($form)) {        
        return $this->redirect(array('novedadesglobales','idparams'=>intval($model->tipoevaluacion)));
      }

      return $this->renderAjax('novedadgeneral',[
        'model' => $model,
        ]);
    }

    public function actionNovedadesglobales($idparams){
      $model = new EvaluacionTipoeval();
      $varidtipo = $idparams;
      $varnombretipo  = Yii::$app->db->createCommand("select tipoevaluacion from tbl_evaluacion_tipoeval where anulado = 0 and idevaluaciontipo = ':varidtipo'")
      ->bindValue(':varidtipo', $varidtipo)
      ->queryScalar();      

      return $this->render('novedadesglobales',[
        'model' => $model,
        'varidtipo' => $varidtipo,
        'varnombretipo' => $varnombretipo,
        ]);
    }

    public function actionIngresarnovedadpares(){
      $txtvaridasuntosNcargo = Yii::$app->request->get("txtvaridasuntosNcargo");
      $txtvarIdcomentariosNovedad = Yii::$app->request->get("txtvarIdcomentariosNovedad");
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      $txtvaridpares = Yii::$app->request->get("txtvaridpares");
     
      Yii::$app->db->createCommand()->insert('tbl_evaluacion_novedadespares',[
                                'documento' => $txtvardocumento,
                                'asunto' => $txtvaridasuntosNcargo,
                                'comentarios' => $txtvarIdcomentariosNovedad,
                                'cambios' => $txtvaridpares,
                                'aprobado' => 0,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacrecion' => date("Y-m-d"),
                            ])->execute(); 


      $varverifica = 1;

      die(json_encode($varverifica)); 

    }

    public function actionVerificapersona(){
      $txtvarIdcambiosNcargo = Yii::$app->request->get("txtvarIdcambiosNcargo");

      $varname = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua where anulado = 0 and documento = ':txtvarIdcambiosNcargo' group by documento")
      ->bindValue(':txtvarIdcambiosNcargo', $txtvarIdcambiosNcargo)
      ->queryScalar();

      die(json_encode($varname));
    }

    public function actionIngresarnovedadcargos(){
      $txtvaridasuntosNcargo = Yii::$app->request->get("txtvaridasuntosNcargo");
      $txtvarIdcomentariosNovedad = Yii::$app->request->get("txtvarIdcomentariosNovedad");
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      $txtvaridpares = Yii::$app->request->get("txtvaridpares");
      $txtvartipo = Yii::$app->request->get("txtvartipo");
     
      Yii::$app->db->createCommand()->insert('tbl_evaluacion_novedadescargo',[
                                'documento' => $txtvardocumento,
                                'asunto' => $txtvaridasuntosNcargo,
                                'comentarios' => $txtvarIdcomentariosNovedad,
                                'cambios' => $txtvaridpares,
                                'aprobado' => 0,
                                'anulado' => 0,
                                'usua_id' => Yii::$app->user->identity->id,
                                'fechacrecion' => date("Y-m-d"),
                                'tipo' => $txtvartipo,
                            ])->execute(); 

      $varverifica = 1;

      die(json_encode($varverifica)); 

    }

    public function actionEditarplannovedad($idtipos, $varidplan, $varestado, $varcambios, $varsolicitante){
      $vartipoevalua = $idtipos;
      $varidevalua = $varidplan;
      $varEstado = $varestado;
      $varcambioJefe = $varcambios;
      $varsolicitantes = $varsolicitante;

      if ($vartipoevalua == 1) {
        Yii::$app->db->createCommand()->update('tbl_evaluacion_novedadesauto',[
                                          'aprobado' => $varEstado,
                                          'aprobadopor' => Yii::$app->user->identity->id,
                                      ],'idnovedades ='.$varidevalua.'')->execute(); 
      }else{
        if ($vartipoevalua == 2) {
          Yii::$app->db->createCommand()->update('tbl_evaluacion_novedadesjefe',[
                                          'aprobado' => $varEstado,
                                          'aprobadopor' => Yii::$app->user->identity->id,
                                      ],'idnovedadesj ='.$varidevalua.'')->execute(); 

          if ($varEstado == 1) {
            $varlistJefe = Yii::$app->db->createCommand("select documento_jefe, nombre_jefe, id_cargo_jefe, cargo_jefe from tbl_usuarios_evalua where anulado = 0 and documento_jefe = ':varcambioJefe' group by documento_jefe")
            ->bindValue(':varcambioJefe', $varcambioJefe)
            ->queryAll();

            foreach ($varlistJefe as $key => $value) {
               Yii::$app->db->createCommand()->update('tbl_usuarios_evalua',[
                                            'documento_jefe' => $value['documento_jefe'],
                                            'nombre_jefe' => $value['nombre_jefe'],
                                            'id_cargo_jefe' => $value['id_cargo_jefe'],
                                            'cargo_jefe' => $value['cargo_jefe'],
                                        ],'documento ='.$varsolicitantes.'')->execute();
             } 
          }          

           
        }else{
          if ($vartipoevalua == 3) {

            $vartipos = Yii::$app->db->createCommand("select distinct tipo from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = ':varidevalua'")
            ->bindValue(':varidevalua', $varidevalua)
            ->queryScalar();

            if ($varEstado == 1) {
              if ($vartipos == 1) {
                $vardocumentboss = Yii::$app->db->createCommand("select distinct documento from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = ':varidevalua'")
                ->bindValue(':varidevalua', $varidevalua)
                ->queryScalar();
                $vardocumentnewperson = Yii::$app->db->createCommand("select distinct cambios from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = ':varidevalua'")
                ->bindValue(':varidevalua', $varidevalua)
                ->queryScalar();

                $varlistnewboss = Yii::$app->db->createCommand("select distinct documento_jefe, nombre_jefe, id_cargo_jefe, cargo_jefe, directorarea, clientearea from tbl_usuarios_evalua where anulado = 0 and documento_jefe = ':vardocumentboss'")
                ->bindValue(':vardocumentboss', $vardocumentboss)
                ->queryAll();

                foreach ($varlistnewboss as $key => $value) {
                  Yii::$app->db->createCommand()->update('tbl_usuarios_evalua',[
                                            'documento_jefe' => $value['documento_jefe'],
                                            'nombre_jefe' => $value['nombre_jefe'],
                                            'id_cargo_jefe' => $value['id_cargo_jefe'],
                                            'cargo_jefe' => $value['cargo_jefe'],
                                            'directorarea' => $value['directorarea'],
                                            'clientearea' => $value['clientearea'],
                                        ],'documento ='.$vardocumentnewperson.'')->execute();
                }

              }else{
                $vardocumentnewboss = Yii::$app->db->createCommand("select distinct comentarios from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = ':varidevalua'")
                ->bindValue(':varidevalua', $varidevalua)
                ->queryScalar();
                $vardocumentperson = Yii::$app->db->createCommand("select distinct cambios from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = ':varidevalua'")
                ->bindValue(':varidevalua', $varidevalua)
                ->queryScalar();

                $varlistnewboss = Yii::$app->db->createCommand("select distinct documento_jefe, nombre_jefe, id_cargo_jefe, cargo_jefe, directorarea, clientearea from tbl_usuarios_evalua where anulado = 0 and documento_jefe = ':vardocumentnewboss'")
                ->bindValue(':vardocumentnewboss', $vardocumentnewboss)
                ->queryAll();

                foreach ($varlistnewboss as $key => $value) {
                  Yii::$app->db->createCommand()->update('tbl_usuarios_evalua',[
                                            'documento_jefe' => $value['documento_jefe'],
                                            'nombre_jefe' => $value['nombre_jefe'],
                                            'id_cargo_jefe' => $value['id_cargo_jefe'],
                                            'cargo_jefe' => $value['cargo_jefe'],
                                            'directorarea' => $value['directorarea'],
                                            'clientearea' => $value['clientearea'],
                                        ],'documento ='.$vardocumentperson.'')->execute();
                }
              }
            }            

            Yii::$app->db->createCommand()->update('tbl_evaluacion_novedadescargo',[
                                          'aprobado' => $varEstado,
                                          'aprobadopor' => Yii::$app->user->identity->id,
                                      ],'idnovedadesc ='.$varidevalua.'')->execute(); 
          }else{
            if ($vartipoevalua == 4) {
              Yii::$app->db->createCommand()->update('tbl_evaluacion_novedadespares',[
                                          'aprobado' => $varEstado,
                                          'aprobadopor' => Yii::$app->user->identity->id,
                                      ],'idnovedadesp ='.$varidevalua.'')->execute(); 
            }
          }
        }
      }

      
      return $this->redirect(array('novedadesglobales','idparams'=>$vartipoevalua));

    }

    public function actionFeedbackresultado(){

    return $this->render('feedbackresultado');
   }

    public function actionGestionnovedades(){

      return $this->render('gestionnovedades');
    }

    public function actionCreatenovedadgeneral(){
      $txtvarvardocument = Yii::$app->request->get("txtvarvardocument");
      $txtvaridasuntosNcargo = Yii::$app->request->get("txtvaridasuntosNcargo");
      $txtvaridasuntosN = Yii::$app->request->get("txtvaridasuntosN");
      $txtvarIdcomentarios = Yii::$app->request->get("txtvarIdcomentarios");
      $txtvaridpares = Yii::$app->request->get("txtvaridpares");

      if ($txtvaridasuntosNcargo == 1) {
        $txtvaridpares = $txtvarvardocument;
      }else{
        if ($txtvaridasuntosNcargo == 2) {
          $txtvaridpares = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua where documento = ':txtvarvardocument' group by documento_jefe")
          ->bindValue(':txtvarvardocument', $txtvarvardocument)
          ->queryScalar();
        }
      }

      Yii::$app->db->createCommand()->insert('tbl_evaluacion_novedadeslog',[
          'evaluadorid' => $txtvarvardocument,
          'evaluado' => $txtvaridpares,
          'tipo_eva' => $txtvaridasuntosNcargo,
          'asunto' => $txtvaridasuntosN,
          'comentarios' => $txtvarIdcomentarios,
          'aprobado' => 0,
          'anulado' => 0,
          'usua_id' => Yii::$app->user->identity->id,
          'fechacrecion' => date("Y-m-d"),
      ])->execute(); 
      
      Yii::$app->db->createCommand()->insert('tbl_logs', [
        'usua_id' => Yii::$app->user->identity->id,
        'usuario' => Yii::$app->user->identity->username,
        'fechahora' => date('Y-m-d h:i:s'),
        'ip' => Yii::$app->getRequest()->getUserIP(),
        'accion' => 'Create',
        'tabla' => 'tbl_evaluacion_novedadeslog'
      ])->execute(); 

      $varverifica = 1;

      die(json_encode($varverifica));

    }


    // Funcionalidades Diego

    public function actionEvaluacionfeedback($model, $documento){
      $model = new EvaluacionNovedadesauto();
      $txtdocumento = $documento;
      return $this->render('evaluacionfeedback',[
        'model' => $model,
        'documento' => $txtdocumento,
        ]);
    }

    public function actionValidaevaluado(){

    $txtvardocumento = Yii::$app->request->get("txtvardocumento");

    $vardocumento1 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = ':txtvardocumento' and idevaluaciontipo = 1 group by documentoevaluado")
    ->bindValue(':txtvardocumento', $txtvardocumento)
    ->queryScalar();
    $vardocumento2 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = ':txtvardocumento' and idevaluaciontipo = 3 group by documentoevaluado")
    ->bindValue(':txtvardocumento', $txtvardocumento)
    ->queryScalar();
    
    $vardocumentojefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua WHERE documento = ':txtvardocumento'")
    ->bindValue(':txtvardocumento', $txtvardocumento)
    ->queryScalar();

    $varevaluoaljefe = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = ':txtvardocumento' AND idevaluaciontipo = 3 group by documentoevaluado")
    ->bindValue(':txtvardocumento', $txtvardocumento)
    ->queryScalar();

    if($vardocumentojefe > 1){
      $varnopares = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_novedadesgeneral WHERE documento = ':txtvardocumento' AND aprobado = 1")
      ->bindValue(':txtvardocumento', $txtvardocumento)
      ->queryScalar();
      if($varnopares > 0){
        $vardocumento3 = 0;
      }else {
        $vardocumento3 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = ':txtvardocumento' and idevaluaciontipo = 3 group by documentoevaluado")
        ->bindValue(':txtvardocumento', $txtvardocumento)
        ->queryScalar();
      } 
    }else{
      #code
    }
    $varcantipares = Yii::$app->db->createCommand("select COUNT(documento_jefe) from tbl_usuarios_evalua WHERE documento_jefe = ':vardocumentojefe'")
    ->bindValue(':vardocumentojefe', $vardocumentojefe)
    ->queryScalar();
    if($vardocumento1 && $vardocumento2 && $varevaluoaljefe){
      $txtRta = 1;
    } else{
      $txtRta = 0;
    }
    
    die(json_encode($txtRta));
  }

   public function actionCrearresultadofb(){
    $txtvarobservafeedback = Yii::$app->request->get("varobservafeedback");
    $txtvarNotafinal = Yii::$app->request->get("varNotafinal");
    $txtvardocumento = Yii::$app->request->get("vardocumento");
    $txtvardocumentojefe = Yii::$app->request->get("vardocumentojefe");
    $txtvartipocoaching = Yii::$app->request->get("vartipocoaching");
    $txtvalidadocumento = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_resulta_feedback WHERE documento = ':txtvardocumento'")
    ->bindValue(':txtvardocumento', $txtvardocumento)
    ->queryScalar();
    $txtrta = 0;
    if($txtvalidadocumento == 0) {
        $txtEmail = "anmorenoa@grupokonecta.com";
        Yii::$app->db->createCommand()->insert('tbl_evaluacion_resulta_feedback',[
                                  'documento' => $txtvardocumento,
                                  'observacion_feedback' => $txtvarobservafeedback,
                                  'notafinal' => $txtvarNotafinal,
                                  'documento_jefe' => $txtvardocumentojefe,
				  'tipo_feedback' => $txtvartipocoaching,
                                  'fechacreacion' => date("Y-m-d"),
                                  'anulado' => 0,                              
                                  'usua_id' => Yii::$app->user->identity->id,
                              ])->execute(); 

        $varlistbloques = Yii::$app->db->createCommand("select * from tbl_evaluacion_bloques where anulado = 0")->queryAll();

        $varconteobloque = null;
        foreach ($varlistbloques as $key => $value) {          
          $varidbloque = $value['idevaluacionbloques'];
          $varconteobloque = $varconteobloque + 1;


          $valortotal1Auto = 0;
          $valortotal2Jefe = 0;
          $valortotal3Cargo = 0;
          $valortotal4Pares = 0;
          $listadocompetencias = Yii::$app->db->createCommand("select ec.namecompetencia, eb.idevaluacionbloques, eb.namebloque, es.idevaluacioncompetencia
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = ':txtvardocumento' AND es.idevaluaciontipo = 1 AND eb.idevaluacionbloques = ':varidbloque'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")
                                                        ->bindValue(':txtvardocumento', $txtvardocumento)
                                                        ->bindValue(':varidbloque', $varidbloque)
                                                        ->queryAll();

          $varconteocompetencia = 0;
          foreach ($listadocompetencias as $key => $value) {
            $nombrecompetencias = $value['namecompetencia'];
            $varidcompetencia = $value['idevaluacioncompetencia'];                                        
            
            $varconteocompetencia = $varconteocompetencia + 1;

            $listacompetencia1 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = ':txtvardocumento' AND es.idevaluaciontipo = 1 AND ec.namecompetencia = ':nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")
                                                        ->bindValue(':txtvardocumento', $txtvardocumento)
                                                        ->bindValue(':nombrecompetencias', $nombrecompetencias)
                                                        ->queryAll();

            foreach ($listacompetencia1 as $key => $value1) {
              $valortotal1Auto = $value1['%Competencia'];
            }

            $listacompetencia2 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $txtvardocumento AND es.idevaluaciontipo = 3 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

            foreach ($listacompetencia2 as $key => $value2) {
              $valortotal2Jefe = $value2['%Competencia'];
            }

            $listacompetencia3 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = ':txtvardocumento' AND es.idevaluaciontipo = 2 AND ec.namecompetencia = ':nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")
                                                        ->bindValue(':txtvardocumento', $txtvardocumento)
                                                        ->bindValue(':nombrecompetencias', $nombrecompetencias)
                                                        ->queryAll();

            foreach ($listacompetencia3 as $key => $value3) {
              $valortotal3Cargo = $value3['%Competencia'];
            }

            $listacompetencia4 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = ':txtvardocumento' AND es.idevaluaciontipo = 4 AND ec.namecompetencia = ':nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")
                                                        ->bindValue(':txtvardocumento', $txtvardocumento)
                                                        ->bindValue(':nombrecompetencias', $nombrecompetencias)
                                                        ->queryAll();

            foreach ($listacompetencia4 as $key => $value4) {
              $valortotal4Pares = $value4['%Competencia'];
            }

            $txtnotafinal1 = null;
            if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares == 0 && $valortotal3Cargo == 0) {
              $txtnotafinal1 = number_format((($valortotal1Auto * 20)/100) + (($valortotal2Jefe * 80) /100),2);
            }
            else{
              #code
            }

            if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares != 0 && $valortotal3Cargo == 0) {
              $txtnotafinal1 = number_format((($valortotal1Auto * 15)/100) + (($valortotal2Jefe * 70) /100) + (($valortotal4Pares * 15) /100),2); 
            }
            else{
              #code
            }

            if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares == 0 && $valortotal3Cargo != 0) {
              $txtnotafinal1 = number_format((($valortotal1Auto * 10)/100) + (($valortotal2Jefe * 60) /100) + (($valortotal3Cargo * 30) /100),2);
            }else{
              #code
            }

            if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares != 0 && $valortotal3Cargo != 0) {
              $txtnotafinal1 = number_format((($valortotal1Auto * 5)/100) + (($valortotal2Jefe * 60) /100) + (($valortotal4Pares * 5) /100) + (($valortotal3Cargo * 30) /100),2);                            
            }else{
              #code
            }

            Yii::$app->db->createCommand()->insert('tbl_evaluacion_rtafeedback_detalle',[
                                  'documento' => $txtvardocumento,
                                  'idevaluacionbloques' => $varidbloque,
                                  'idevaluacioncompetencia' => $varidcompetencia,
                                  'notacompetencia' => $txtnotafinal1,
                                  'fechacreacion' => date("Y-m-d"),
                                  'anulado' => 0,                              
                                  'usua_id' => Yii::$app->user->identity->id,
                              ])->execute(); 
          }
        } 

        $txtrta = 1;

        $message = "<html><body>";
                      $message .= "<h3>NotificaciÃ³n: Se concluye con el proceso de evaluaciones, puedes ingresar a revisar tus resultados y sugerencias. </h3>";
                      $message .= "</body></html>";

                Yii::$app->mailer->compose()
                                ->setTo($txtEmail)
                                ->setFrom(Yii::$app->params['email_satu_from'])
                                ->setSubject("NotificaciÃ³n, RevisiÃ³n de resultados de las evaluaciones")
                                ->setHtmlBody($message)
                                ->send();
        }else {
          $txtrta = 2;
        }                          
  
    die(json_encode($txtrta));      
    }

    public function actionResultadoevaluacion($model){
      $model = new EvaluacionNovedadesauto();
      return $this->render('resultadoevaluacion',[
        'model' => $model,
        ]);
    }

    public function actionEliminarnovedades(){

      return $this->render('eliminarnovedades');
    }

    public function actionEditarnovedaddelete($varidnombre,$varidevaluado,$varestado,$vartipoeva,$varidevalua){

      Yii::$app->db->createCommand()->update('tbl_evaluacion_novedadeslog',[
                                          'aprobado' => $varestado,
                                          'aprobadopor' => Yii::$app->user->identity->id,
                                      ],'idnovedadeslog ='.$varidevalua.'')->execute(); 

      if ($varestado == 1) {
	if ($varidevaluado != null) {
          Yii::$app->db->createCommand("delete from tbl_evaluacion_solucionado where documento = ':varidnombre' and documentoevaluado = ':varidevaluado' and idevaluaciontipo = ':vartipoeva' and anulado = 0")
          ->bindValue(':varidnombre', $varidnombre)
          ->bindValue(':varidevaluado', $varidevaluado)
          ->bindValue(':vartipoeva', $vartipoeva)
          ->execute();
        
          Yii::$app->db->createCommand("delete from tbl_evaluacion_desarrollo where idevaluador = ':varidnombre' and idevalados = ':varidevaluado' and idevaluaciontipo = ':vartipoeva' and anulado = 0")
          ->bindValue(':varidnombre', $varidnombre)
          ->bindValue(':varidevaluado', $varidevaluado)
          ->bindValue(':vartipoeva', $vartipoeva)
          ->execute();
        }

        if ($vartipoeva == 4 && $varidevaluado == null) {
          Yii::$app->db->createCommand("delete from tbl_evaluacion_novedadesgeneral where documento = ':varidnombre' and anulado = 0")
          ->bindValue(':varidnombre', $varidnombre)
          ->execute();
        }
      } 

      return $this->redirect('eliminarnovedades');


    }

    public function actionListacompetencia(){

    $txtCompetencia = Yii::$app->request->get("txtCompetencia");

    if ($txtCompetencia == 1){
      $txtRta = Yii::$app->db->createCommand("Select idevaluacionbloques, namebloque from tbl_evaluacion_bloques WHERE anulado = 0")->queryAll();
      $arrayLis = array();
      foreach ($txtRta as $key => $value) {
          array_push($arrayLis, array("id"=>$value['idevaluacionbloques'],"nombre"=>$value['namebloque']));
      }
    }
    if ($txtCompetencia == 2){
      $txtRta = Yii::$app->db->createCommand("select idevaluacionnivel, concat(nivel,'-',nombrecargo) nivel from tbl_evaluacion_nivel WHERE anulado = 0")->queryAll();
      $arrayLis = array();
      foreach ($txtRta as $key => $value) {
          array_push($arrayLis, array("id"=>$value['idevaluacionnivel'],"nombre"=>$value['nivel']));
      }
    }
    if ($txtCompetencia == 3){
      $txtRta = Yii::$app->db->createCommand("Select distinct clientearea from tbl_usuarios_evalua WHERE anulado = 0 AND clientearea IS NOT NULL ORDER BY clientearea")->queryAll();
      $arrayLis = array();
      foreach ($txtRta as $key => $value) {
          array_push($arrayLis, array("id"=>$value['clientearea'],"nombre"=>$value['clientearea']));
      }
    }

  die(json_encode($arrayLis));
  }

  public function actionListasopciones(){

    $txtvarCompetencia = Yii::$app->request->get("txtvarCompetencia");
    $txtvarOpciones = Yii::$app->request->get("txtvarOpciones");
    $arrayLis = array();
    array_push($arrayLis, array("idbloque"=>$txtvarCompetencia,"idopcion"=>$txtvarOpciones));
  
    die(json_encode($arrayLis));
  }

  public function actionResultadodashboard(){
    $model = new EvaluacionCompetencias();
    $varidbloque = null;
    $varidopcion = null;
    $data = Yii::$app->request->post();

    if ($model -> load($data)) {
      $varidbloque = $model->idevaluacionnivel;
      $varidopcion = $model->namecompetencia;  }
     return $this->render('resultadodashboard',[
      'model' => $model,
      'idbloque' => $varidbloque,
      'idopcion' => $varidopcion,
      ]);
  }

  public function actionExportarlist(){
     $varlistevalua = Yii::$app->get('dbslave')->createCommand("select ec.cedulaevaluador 'idevaluador', ue.nombre_completo 'evaluador', ec.cedulaevaluado 'idevaluado', (select distinct eu.nombre_completo from tbl_usuarios_evalua eu where eu.documento = ec.cedulaevaluado) 'nombre_evaluado',  et.tipoevaluacion 'tipo_evaluacion', if (ec.idresultado = 1, 'Realizado', 'Sin realizar') 'Resultado',  ec.directorarea 'Director', ec.clientearea 'Area' from tbl_usuarios_evalua ue inner join tbl_evaluacion_cumplimiento ec on ue.documento = ec.cedulaevaluador inner join tbl_evaluacion_tipoeval et on   ec.idtipoevalua = et.idevaluaciontipo where     ec.anulado = 0 group by ec.cedulaevaluador, ec.cedulaevaluado")->queryAll();

     return $this->renderAjax('exportarlist',[
       'varlistevalua' => $varlistevalua,
       ]);
   }

   public function actionExportarresultfb(){
   $varlistafeedback = Yii::$app->db->createCommand("SELECT e.documento documento, ue.nombre_completo nombreeva, e.observacion_feedback feedback, e.notafinal nota_final, e.documento_jefe documentojefe, ue2.nombre_completo nombrejefe, e.fechacreacion fecha 
						from tbl_evaluacion_resulta_feedback e
						INNER JOIN tbl_usuarios_evalua_feedback ue ON
						ue.documento = e.documento
						INNER JOIN tbl_usuarios_evalua_feedback ue2 ON
						ue2.documento_jefe = e.documento_jefe group by documento ORDER BY documentojefe")->queryAll();

  return $this->renderAjax('exportarresultfb',[
    'varlistafeedback' => $varlistafeedback,
    ]);
   }
    //  Fin Funcionalidades Diego

    public function actionParamsevaluacion(){
      $model = new EvaluacionCumplimiento();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {    
        Yii::$app->db->createCommand("truncate table tbl_evaluacion_cumplimiento ")->execute();

        $varlistusuarios = Yii::$app->db->createCommand("select ue.documento, ue.directorarea, ue.clientearea from tbl_usuarios_evalua ue where ue.anulado = 0 group by ue.documento")->queryAll();

        foreach ($varlistusuarios as $key => $value) {
          $varusuarioid = $value['documento'];
          $vardirectorarea = $value['directorarea'];
          $varclienarea =$value['clientearea'];

          $varrtaauto = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo ed where ed.anulado = 0 and ed.idevaluador = ':varusuarioid' and ed.idevalados = ':varusuarioid'")
          ->bindValue(':varusuarioid', $varusuarioid)
          ->queryscalar();

          // Guardo informacion de autoevaluacion
          Yii::$app->db->createCommand()->insert('tbl_evaluacion_cumplimiento',[
              'cedulaevaluador' => $varusuarioid,
              'cedulaevaluado' => $varusuarioid,
              'idtipoevalua' => 1,
              'idresultado' => $varrtaauto,
              'directorarea' => $vardirectorarea,
              'clientearea' => $varclienarea,
              'fechamodificacion' => date("Y-m-d H:i:s"),
              'fechacreacion' => date("Y-m-d H:i:s"),
              'anulado' => 0,
              'usua_id' => Yii::$app->user->identity->id,              
          ])->execute();

          $varjefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua where documento = ':varusuarioid' and anulado = 0 group by documento_jefe")
          ->bindValue(':varusuarioid', $varusuarioid)
          ->queryScalar();

          $varrtajefe = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo ed where ed.anulado = 0 and ed.idevaluador = ':varusuarioid' and ed.idevalados = ':varjefe'")
          ->bindValue(':varusuarioid', $varusuarioid)
          ->bindValue(':varjefe', $varjefe)
          ->queryscalar();

          // Guardo informacion de evaluacion Jefe
          Yii::$app->db->createCommand()->insert('tbl_evaluacion_cumplimiento',[
              'cedulaevaluador' => $varusuarioid,
              'cedulaevaluado' => $varjefe,
              'idtipoevalua' => 2,
              'idresultado' => $varrtajefe,
              'directorarea' => $vardirectorarea,
              'clientearea' => $varclienarea,
              'fechamodificacion' => date("Y-m-d H:i:s"),
              'fechacreacion' => date("Y-m-d H:i:s"),
              'anulado' => 0,
              'usua_id' => Yii::$app->user->identity->id,              
          ])->execute(); 

          $varlistpares = Yii::$app->db->createCommand("select ue.documento from tbl_usuarios_evalua ue where ue.documento_jefe = ':varjefe' and ue.documento != ':varusuarioid' group by ue.documento")
          ->bindValue(':varjefe', $varjefe)
          ->bindValue(':varusuarioid', $varusuarioid)
          ->queryAll();

          foreach ($varlistpares as $key => $value) {
            $varpar = $value['documento'];
            $varrtapar = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo ed where ed.anulado = 0 and ed.idevaluador = ':varusuarioid' and ed.idevalados = ':varpar'")
            ->bindValue(':varusuarioid', $varusuarioid)
            ->bindValue(':varpar', $varpar)
            ->queryscalar();

            // Guardo informacion de evaluacion Pares
            Yii::$app->db->createCommand()->insert('tbl_evaluacion_cumplimiento',[
                'cedulaevaluador' => $varusuarioid,
                'cedulaevaluado' => $varpar,
                'idtipoevalua' => 4,
                'idresultado' => $varrtapar,
                'directorarea' => $vardirectorarea,
               'clientearea' => $varclienarea,
                'fechamodificacion' => date("Y-m-d H:i:s"),
                'fechacreacion' => date("Y-m-d H:i:s"),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,              
            ])->execute(); 

          }

          $varlistcargo = Yii::$app->db->createCommand("select ue.documento from tbl_usuarios_evalua ue where ue.documento_jefe = ':varusuarioid' and ue.documento != ':varusuarioid' group by ue.documento")
          ->bindValue(':varusuarioid', $varusuarioid)
          ->queryAll();

          foreach ($varlistcargo as $key => $value) {
            $varcargo = $value['documento'];
            $varrtacargo = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo ed where ed.anulado = 0 and ed.idevaluador = ':varusuarioid' and ed.idevalados = ':varcargo'")
            ->bindValue(':varusuarioid', $varusuarioid)
            ->bindValue(':varcargo', $varcargo)
            ->queryscalar();

            // Guardo informacion de evaluacion Pares
            Yii::$app->db->createCommand()->insert('tbl_evaluacion_cumplimiento',[
                'cedulaevaluador' => $varusuarioid,
                'cedulaevaluado' => $varcargo,
                'idtipoevalua' => 3,
                'idresultado' => $varrtacargo,
                'directorarea' => $vardirectorarea,
              'clientearea' => $varclienarea,
                'fechamodificacion' => date("Y-m-d H:i:s"),
                'fechacreacion' => date("Y-m-d H:i:s"),
                'anulado' => 0,
                'usua_id' => Yii::$app->user->identity->id,              
            ])->execute(); 
          }


        }

        return $this->redirect('paramsevaluacion',['model' => $model]);
      }

      return $this->render('paramsevaluacion',[
        'model' => $model,
        ]);
    }

    public function actionActualizarbdparams(){
      $model = new EvaluacionCumplimiento();

      $varlistup = Yii::$app->db->createCommand("select * from tbl_evaluacion_cumplimiento where anulado = 0")->queryAll();

      foreach ($varlistup as $key => $value) {
        $varidlinea = $value['idevaluacioncumplimiento'];
        $varevaluador = $value['cedulaevaluador'];
        $varevaluado = $value['cedulaevaluado'];
        $vartipo = $value['idtipoevalua'];

        $varconteo = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo where anulado = 0 and idevaluador = ':varevaluador' and idevalados = ':varevaluado' and idevaluaciontipo = ':vartipo'")
        ->bindValue(':varevaluador', $varevaluador)
        ->bindValue(':varevaluado', $varevaluado)
        ->bindValue(':vartipo', $vartipo)
        ->queryScalar();

        if ($varconteo != 0) {
          Yii::$app->db->createCommand("update tbl_evaluacion_cumplimiento set idresultado = ':varconteo' where idevaluacioncumplimiento = ':varidlinea' and anulado = 0")
          ->bindValue(':varconteo', $varconteo)
          ->bindValue(':varidlinea', $varidlinea)
          ->execute();
        }
      }

      return $this->render('paramsevaluacion',[
        'model' => $model,
        ]);
    }

    public function actionListarcedulas(){            
            $txtId = Yii::$app->request->get('id'); 
            
            $sessiones = Yii::$app->user->identity->id;
            $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = ':sessiones'")
            ->bindValue(':sessiones', $sessiones)
            ->queryScalar();                     

            if ($txtId) {
                $txtControl = Yii::$app->db->createCommand("select count(1) from tbl_usuarios_evalua ue inner join tbl_evaluacion_solucionado es on ue.documento = es.documentoevaluado where es.documento = ':vardocument' and es.anulado = 0 and es.idevaluaciontipo = ':txtId' group by ue.documento")
                ->bindValue(':vardocument', $vardocument)
                ->bindValue(':txtId', $txtId)
                ->queryScalar();

                if ($txtControl > 0) {
                  $varListaPcrc = Yii::$app->db->createCommand("select ue.nombre_completo, ue.documento from tbl_usuarios_evalua ue inner join tbl_evaluacion_solucionado es on ue.documento = es.documentoevaluado where es.documento = ':vardocument' and es.anulado = 0 and es.idevaluaciontipo = ':txtId' group by ue.documento")
                  ->bindValue(':vardocument', $vardocument)
                  ->bindValue(':txtId', $txtId)
                  ->queryAll();
           
                    
                    foreach ($varListaPcrc as $key => $value) {
                        echo "<option value='" . $value['documento']. "'>" . $value['nombre_completo'] . "</option>";
                    }
                }else{
                    echo "<option>-</option>";
                }
            }else{
                    echo "<option>".$txtId."</option>";
            }

      }

      public function actionIngresarpersonaeliminar(){
        $txtvarvardocument = Yii::$app->request->get("txtvarvardocument");
        $txtvaridcargos = Yii::$app->request->get("txtvaridcargos");
        $txtvaridmotivosD = Yii::$app->request->get("txtvaridmotivosD");


        Yii::$app->db->createCommand()->insert('tbl_evaluacion_eliminarusuarios',[
                                  'ccsolicitante' => $txtvarvardocument,
                                  'ccevaluado' => $txtvaridcargos,
                                  'motivos' => $txtvaridmotivosD,
                                  'aprobado' => 0,
                                  'anulado' => 0,
                                  'usua_id' => Yii::$app->user->identity->id,
                                  'fechacreacion' => date("Y-m-d"),
                              ])->execute();        

        $varverifica = 1;

        die(json_encode($varverifica));

      }

      public function actionEliminarusuarios(){

        return $this->render('eliminarusuarios');
      }

      public function actionUsuariosdelete($varidnovedades,$varidevaluado,$varaprobado){
        $varidnov = $varidnovedades;
        $varaprobar = $varaprobado;
        $varevaluados = $varidevaluado;

        if ($varaprobar == 1) {
          Yii::$app->db->createCommand("update tbl_evaluacion_eliminarusuarios set aprobado = 1 where ideliminarusuarios = ':varidnov' and anulado = 0")
          ->bindValue(':varidnov', $varidnov)
          ->execute();

          $varlistusuario = Yii::$app->db->createCommand("select distinct * from tbl_usuarios_evalua u where u.documento = ':varevaluados' and u.anulado = 0")
          ->bindValue(':varevaluados', $varevaluados)
          ->queryAll();

          foreach ($varlistusuario as $key => $value) {
              Yii::$app->db->createCommand()->insert('tbl_usuarios_evalua_copy',[
                                  'nombre_completo' => $value['nombre_completo'],
                                  'documento' => $value['documento'],
                                  'id_dp_cargos' => $value['id_dp_cargos'],
                                  'id_dp_posicion' => $value['id_dp_posicion'],
                                  'id_dp_funciones' => $value['id_dp_funciones'],
                                  'posicion' => $value['posicion'],
                                  'funcion' => $value['funcion'],
                                  'usuario_red' => $value['usuario_red'],
                                  'email_corporativo' => $value['email_corporativo'],
                                  'documento_jefe' => $value['documento_jefe'],
                                  'nombre_jefe' => $value['nombre_jefe'],
                                  'id_cargo_jefe' => $value['id_cargo_jefe'],
                                  'cargo_jefe' => $value['cargo_jefe'],
                                  'directorarea' => $value['directorarea'],
                                  'clientearea' => $value['clientearea'],
                                  'fechacrecion' => date("Y-m-d"),
                                  'anulado' => 0,
                                  'usua_id' => Yii::$app->user->identity->id,
                              ])->execute(); 
          }

          Yii::$app->db->createCommand("delete from tbl_usuarios_evalua  where documento = ':varevaluados' and anulado = 0")
          ->bindValue(':varevaluados', $varevaluados)
          ->execute();
        }else{
          Yii::$app->db->createCommand("update tbl_evaluacion_eliminarusuarios set aprobado = 2 where ideliminarusuarios = ':varidnov' and anulado = 0")
          ->bindValue(':varidnov', $varidnov)
          ->execute();
        }

        return $this->redirect('eliminarusuarios');
      }

      public function actionHabilitarpares(){
        $model = new EvaluacionNovedadesgeneral();
        $varname = null;
        $vardocumento = null;

        $form = Yii::$app->request->post();
        if ($model->load($form)) {        
          $vardocumento = $model->documento;
          $varname = Yii::$app->db->createCommand("select distinct nombre_completo from tbl_usuarios_evalua u where u.documento = ':vardocumento' and u.anulado = 0")
          ->bindValue(':vardocumento', $vardocumento)
          ->queryScalar();
          return $this->render('habilitarpares',['model'=>$model,'varname'=>$varname,'vardocumento'=>$vardocumento]);
        }

        return $this->render('habilitarpares',[
          'model' => $model,
          'varname' => $varname,
          'vardocumento' => $vardocumento,
          ]);
      }

      public function actionHabilitarusuario(){
        $txtvardocumento = Yii::$app->request->get("txtvardocumento");
        $txtvars = $txtvardocumento.'s';

        Yii::$app->db->createCommand("update tbl_evaluacion_novedadesgeneral set documento = ':txtvars', anulado = 1 where documento = ':txtvardocumento' and anulado = 0")
        ->bindValue(':txtvars', $txtvars)
        ->bindValue(':txtvardocumento', $txtvardocumento)
        ->execute();       

        $varverifica = 1;

        die(json_encode($varverifica));
      }

      public function actionExportarlista2(){
        $varCorreo = Yii::$app->request->get("var_Destino");

        $varlistusuarios = Yii::$app->db->createCommand("select ec.cedulaevaluador 'idevaluador', ue.nombre_completo 'evaluador', ec.cedulaevaluado 'idevaluado', (select distinct eu.nombre_completo from tbl_usuarios_evalua eu where eu.documento = ec.cedulaevaluado) 'nombre_evaluado',  et.tipoevaluacion 'tipo_evaluacion', if (ec.idresultado = 1, 'Realizado', 'Sin realizar') 'Resultado',  ec.directorarea 'Director', ec.clientearea 'Area' from tbl_usuarios_evalua ue inner join tbl_evaluacion_cumplimiento ec on ue.documento = ec.cedulaevaluador inner join tbl_evaluacion_tipoeval et on   ec.idtipoevalua = et.idevaluaciontipo where ec.anulado = 0 group by ec.cedulaevaluador, ec.cedulaevaluado")->queryAll();

        $phpExc = new \PHPExcel();
        $phpExc->getProperties()
                ->setCreator("Konecta")
                ->setLastModifiedBy("Konecta")
                ->setTitle("Lista de usuarios - Evaluaciï¿½n Desarrollo")
                ->setSubject("Evaluaciï¿½n de Desarrollo")
                ->setDescription("Este archivo contiene el listado de los usuarios registrados para la evaluaciï¿½n de desarrollo")
                ->setKeywords("Lista de usuarios");
        $phpExc->setActiveSheetIndex(0);

        $phpExc->getActiveSheet()->setShowGridlines(False);


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

        $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

        $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
        $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
        $phpExc->setActiveSheetIndex(0)->mergeCells('A1:H1');

        $phpExc->getActiveSheet()->SetCellValue('A2','CEDULA EVALUADOR');
        $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('B2','NOMBRE EVALUADOR');
        $phpExc->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('B2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('C2','CEDULA EVALUADO');
        $phpExc->getActiveSheet()->getStyle('C2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('C2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('D2','NOMBRE EVALUADO');
        $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('E2','TIPO DE EVALUACION');
        $phpExc->getActiveSheet()->getStyle('E2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('E2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('F2','RESULTADOS');
        $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('G2','DIRECTOR');
        $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);

        $phpExc->getActiveSheet()->SetCellValue('H2','CLIENTE');
        $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArraySubTitle2);

        $numCell = 2;
        foreach ($varlistusuarios as $key => $value) {
          $numCell++;

          $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $value['idevaluador']); 
          $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $value['evaluador']); 
          $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $value['idevaluado']); 
          $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $value['nombre_evaluado']); 
          $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $value['tipo_evaluacion']); 
          $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $value['Resultado']); 
          $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['Director']);
          $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $value['Area']);

        }
        $numCell = $numCell;



        $hoy = getdate();
        $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."ListadoUsuarios_Evaluacion_Desarrollo";
              
        $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                
        $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
        $tmpFile.= ".xls";

        $objWriter->save($tmpFile);

        $message = "<html><body>";
        $message .= "<h3>Adjunto del archivo tipo listado de los usuarios en la evaluacion de desarrollo</h3>";
        $message .= "</body></html>";

        Yii::$app->mailer->compose()
                        ->setTo($varCorreo)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject("Envio Listado de usuarios - Evaluacion de Desarrollo")
                        ->attach($tmpFile)
                        ->setHtmlBody($message)
                        ->send();

        $rtaenvio = 1;
        die(json_encode($rtaenvio));

      }

      public function actionExportarseguimientofb(){
        $varlistaseguimientofeedback = Yii::$app->db->createCommand("SELECT u.documento_jefe, u.nombre_jefe, u.documento, u.nombre_completo, er.notafinal, er.tipo_feedback,
                                                      if(er.notafinal IS NULL, 'Sin Realizar', 'Realizado') AS 'Estado'
                                                      FROM tbl_usuarios_evalua_feedback u 
                                                      left JOIN tbl_evaluacion_resulta_feedback er ON
                                                      u.documento = er.documento
                                                      ORDER BY u.documento_jefe")->queryAll();

       return $this->renderAjax('exportarseguimientofb',[
       'varlistaseguimientofeedback' => $varlistaseguimientofeedback,
      ]);
  }

      public function actionExportarrtadashboard(){
        
        $model = new EvaluacionDesarrollo();
        $varlistrtadesarrollo = null;
        $varnombrec = null;
        $varrol = null;
        $varrtaA = 0;
        $varrtaJ = 0;
        $varrtaP = 0;
        $varrtaC = 0;
        $txtProcentaje = 0;
        $vardocumento = null;

        $form = Yii::$app->request->post();
        if($model->load($form)){
          $vardocumento = $model->idevaluador;
          $varlistrtadesarrollo = Yii::$app->db->createCommand("SELECT COUNT(*) FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = '$vardocumento'")->queryScalar();;

          $varnombrec = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua_feedback where documento = $vardocumento")->queryScalar();
          $varrol = Yii::$app->db->createCommand("select distinct concat(posicion,' - ',funcion) from  tbl_usuarios_evalua_feedback where documento in ('$vardocumento')")->queryScalar();

          $varrtaA = Yii::$app->db->createCommand("SELECT if(COUNT(*)=0,'No','Si') FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = '$vardocumento' AND ed.idevaluaciontipo = 1")->queryScalar();
          $varrtaJ = Yii::$app->db->createCommand("SELECT if(COUNT(*)=0,'No','Si') FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = '$vardocumento' AND ed.idevaluaciontipo = 3")->queryScalar();
          $varrtaP = Yii::$app->db->createCommand("SELECT if(COUNT(*)=0,'No','Si') FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = '$vardocumento' AND ed.idevaluaciontipo = 4")->queryScalar();
          $varrtaC = Yii::$app->db->createCommand("SELECT if(COUNT(*)=0,'No','Si') FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = '$vardocumento' AND ed.idevaluaciontipo = 2")->queryScalar();

          $varlistbloques = Yii::$app->db->createCommand("select * from tbl_evaluacion_bloques where anulado = 0")->queryAll();
          $varconteobloque = 0;
          $varconteocompetencia = 0;
          $varArraySumaB = array();
          foreach ($varlistbloques as $key => $value) {
            $varidbloque = $value['idevaluacionbloques'];
            $varconteobloque = $varconteobloque + 1;

            $totalcomp = 0;
            $varArrayPromedio = array();
            $valortotal1Auto = 0;
            $valortotal2Jefe = 0;
            $valortotal3Cargo = 0;
            $valortotal4Pares = 0;

            $listadocompetencias = Yii::$app->db->createCommand("select ec.namecompetencia, eb.idevaluacionbloques, eb.namebloque, es.idevaluacioncompetencia FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 1 AND eb.idevaluacionbloques = $varidbloque
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

            foreach ($listadocompetencias as $key => $value) {
              $nombrecompetencias = $value['namecompetencia'];
              $varidevaluacionbloques = $value['idevaluacionbloques'];
              $varidcompetencia = $value['idevaluacioncompetencia'];                                        
              $varconteocompetencia = $varconteocompetencia + 1;
              $varcolor2 = null;

              $listacompetencia1 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 1 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

              foreach ($listacompetencia1 as $key => $value1) {
                $valortotal1Auto = $value1['%Competencia'];
                $varmensaje = $value1['mensaje'];
              }

              $listacompetencia2 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 3 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

              foreach ($listacompetencia2 as $key => $value2) {
                $valortotal2Jefe = $value2['%Competencia'];
              }

              $listacompetencia3 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 2 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

              foreach ($listacompetencia3 as $key => $value3) {
                $valortotal3Cargo = $value3['%Competencia'];
              }

              $listacompetencia4 = Yii::$app->db->createCommand("select ue.nombre_completo nombre, es.documentoevaluado documento, sum(er.valor), 
                                                        FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', es.idevaluacioncompetencia,
                                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque,et.tipoevaluacion ,ef.mensaje, count(es.idevaluacioncompetencia) canti
                                                        FROM tbl_evaluacion_solucionado es
                                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                                        INNER JOIN tbl_usuarios_evalua_feedback ue ON es.documentoevaluado = ue.documento
                                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                                        LEFT JOIN tbl_evaluacion_feedback_mensaje ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                                        INNER JOIN tbl_evaluacion_tipoeval et ON es.idevaluaciontipo = et.idevaluaciontipo
                                                        WHERE es.documentoevaluado = $vardocumento AND es.idevaluaciontipo = 4 AND ec.namecompetencia = '$nombrecompetencias'
                                                        GROUP BY es.idevaluacioncompetencia ORDER BY eb.idevaluacionbloques, ec.namecompetencia")->queryAll();

              foreach ($listacompetencia4 as $key => $value4) {
                $valortotal4Pares = $value4['%Competencia'];
              }

              $txtnotafinal1 = null;
              if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares == 0 && $valortotal3Cargo == 0) {
                $txtnotafinal1 = number_format((($valortotal1Auto * 20)/100) + (($valortotal2Jefe * 80) /100),2);
              }
              if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares != 0 && $valortotal3Cargo == 0) {
                $txtnotafinal1 = number_format((($valortotal1Auto * 15)/100) + (($valortotal2Jefe * 70) /100) + (($valortotal4Pares * 15) /100),2);  
              }
              if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares == 0 && $valortotal3Cargo != 0) {
                $txtnotafinal1 = number_format((($valortotal1Auto * 10)/100) + (($valortotal2Jefe * 60) /100) + (($valortotal3Cargo * 30) /100),2); 
              }
              if($valortotal1Auto != 0 && $valortotal2Jefe != 0 && $valortotal4Pares != 0 && $valortotal3Cargo != 0) {
                $txtnotafinal1 = number_format((($valortotal1Auto * 5)/100) + (($valortotal2Jefe * 60) /100) + (($valortotal3Cargo * 5) /100) + (($valortotal4Pares * 30) /100),2);    
              }

              $totalcomp = $totalcomp + 1;
              array_push($varArrayPromedio, $txtnotafinal1);
            }

            if ($totalcomp != 0) {
              $varPromedios = round(array_sum($varArrayPromedio) / $totalcomp,2);
            }else{
              $varPromedios = 0;
            }

            if ($varidbloque == 1) {
              $vartotalb = round(($varPromedios * (40 / 100)),2);
            }else{
              if ($varidbloque == 2) {
                $vartotalb = round(($varPromedios * (20 / 100)),2);
              }else{
                if ($varidbloque == 3) {
                  $vartotalb = round(($varPromedios * (40 / 100)),2);
                }
              }
            }
            array_push($varArraySumaB, $vartotalb); 
          }

          $txtProcentaje =  round(array_sum($varArraySumaB),2);
        }

        return $this->render('exportarrtadashboard',[
          'model' => $model,
          'varlistrtadesarrollo' => $varlistrtadesarrollo,
          'varnombrec' => $varnombrec,
          'varrol' => $varrol,
          'varrtaA' => $varrtaA,
          'varrtaJ' => $varrtaJ,
          'varrtaP' => $varrtaP,
          'varrtaC' => $varrtaC,
          'txtProcentaje' => $txtProcentaje,
          'vardocumento' => $vardocumento,
        ]);
      }

      public function actionEnviararchivouno(){
        $model = new EvaluacionDesarrollo();
        $varlistrtadesarrollo = null;
        $varnombrec = null;
        $varrol = null;
        $varrtaA = 0;
        $varrtaJ = 0;
        $varrtaP = 0;
        $varrtaC = 0;
        $txtProcentaje = 0;
        $vardocumento = null;

        $form = Yii::$app->request->post();
        if($model->load($form)){
          $varcorreo = $model->comentarios;

          $phpExc = new \PHPExcel();
          $phpExc->getProperties()
                  ->setCreator("Konecta")
                  ->setLastModifiedBy("Konecta")
                  ->setTitle("Archivo Resultados Evaluacion Desarrollo Opcion 1")
                  ->setSubject("Archivo Resultados Evaluacion Desarrollo Opcion 1")
                  ->setDescription("Este archivo contiene el proceso de la evaluacion de desarrollo opcion 1")
                  ->setKeywords("Archivo Resultados Evaluacion Desarrollo Opcion 1");
          $phpExc->setActiveSheetIndex(0);

          $phpExc->getActiveSheet()->setShowGridlines(False);

          $styleArray = array(
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

          $styleColorBA = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => 'F9BD4C'),
                  )
                );

          $styleColorBB = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => '22D7CF'),
                  )
                );

          $styleColorBC = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => '49de70'),
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

          $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

          $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
          $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A1:AR1');

          $phpExc->getActiveSheet()->SetCellValue('A2','Datos Generales');
          $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A2:G2');
          
          $phpExc->getActiveSheet()->SetCellValue('A3','Cedula Evaluado');
          $phpExc->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('B3','Evaluado');
          $phpExc->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('C3','Auto');
          $phpExc->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('D3','Jefe');
          $phpExc->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('E3','Colaborador');
          $phpExc->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('F3','Par');
          $phpExc->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('G3','Nota Final');
          $phpExc->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('H2','Bloque Compentencias');
          $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColorBA);
          $phpExc->setActiveSheetIndex(0)->mergeCells('H2:AB2');

          $phpExc->getActiveSheet()->SetCellValue('AB3','Nota Bloque Compentencias');
          $phpExc->getActiveSheet()->getStyle('AB3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AB3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AB3')->applyFromArray($styleColorBA);

          $phpExc->getActiveSheet()->SetCellValue('AC2','Bloque Organizacional');
          $phpExc->getActiveSheet()->getStyle('AC2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleColorBB);
          $phpExc->setActiveSheetIndex(0)->mergeCells('AC2:AG2');

          $phpExc->getActiveSheet()->SetCellValue('AG3','Nota Bloque Organizacional');
          $phpExc->getActiveSheet()->getStyle('AG3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AG3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AG3')->applyFromArray($styleColorBB);

          $phpExc->getActiveSheet()->SetCellValue('AH2','Bloque Desempeno');
          $phpExc->getActiveSheet()->getStyle('AH2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleColorBC);
          $phpExc->setActiveSheetIndex(0)->mergeCells('AH2:AP2');

          $phpExc->getActiveSheet()->SetCellValue('AP3','Nota Bloque Desempeno');
          $phpExc->getActiveSheet()->getStyle('AP3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AP3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AP3')->applyFromArray($styleColorBC);

          $phpExc->getActiveSheet()->SetCellValue('AQ2','Comentarios y Observaciones');
          $phpExc->getActiveSheet()->getStyle('AQ2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AQ2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AQ2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('AQ2')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('AQ2:AR2');

          $phpExc->getActiveSheet()->SetCellValue('AQ3','Comentarios');
          $phpExc->getActiveSheet()->getStyle('AQ3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AQ3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AQ3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('AQ3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('AR3','Observaciones Feedback');
          $phpExc->getActiveSheet()->getStyle('AR3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AR3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AR3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('AR3')->applyFromArray($styleArrayTitle);

          $txtlistadoDNI = Yii::$app->db->createCommand("SELECT DISTINCT er.documento, er.notafinal FROM tbl_evaluacion_resulta_feedback er WHERE er.anulado = 0")->queryAll();

          $numCell = 4;
          foreach ($txtlistadoDNI as $key => $value) {
            $vardocumento = $value['documento'];

            $varnombrec = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua_feedback where documento = ':vardocumento'")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();
            $varrol = Yii::$app->db->createCommand("select distinct concat(posicion,' - ',funcion) from  tbl_usuarios_evalua_feedback where documento in (':vardocumento')")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $varrtaA = Yii::$app->db->createCommand("SELECT if(COUNT(*)=0,'No','Si') FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = ':vardocumento' AND ed.idevaluaciontipo = 1")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();
            $varrtaJ = Yii::$app->db->createCommand("SELECT if(COUNT(*)=0,'No','Si') FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = ':vardocumento' AND ed.idevaluaciontipo = 3")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();
            $varrtaP = Yii::$app->db->createCommand("SELECT if(COUNT(*)=0,'No','Si') FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = ':vardocumento' AND ed.idevaluaciontipo = 4")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();
            $varrtaC = Yii::$app->db->createCommand("SELECT if(COUNT(*)=0,'No','Si') FROM tbl_evaluacion_desarrollo ed WHERE ed.anulado = 0   AND ed.idevalados = ':vardocumento' AND ed.idevaluaciontipo = 2")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $vardocumento);
            $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $varnombrec);
            $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $varrtaA);
            $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $varrtaJ);
            $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $varrtaP);
            $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $varrtaC);
            $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['notafinal']);

            $varlistarBloqueA = Yii::$app->db->createCommand("SELECT * FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 1")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryAll();

            $lastColumn = 'H';
            $conteocolumnzBA = 0;
            foreach ($varlistarBloqueA as $key => $value) {

              $idcompetencia = $value['idevaluacioncompetencia'];
              $varcompetencia = Yii::$app->db->createCommand("SELECT ec.namecompetencia FROM tbl_evaluacion_competencias ec WHERE ec.anulado = 0 AND ec.idevaluacioncompetencia in (':idcompetencia')")
              ->bindValue(':idcompetencia', $idcompetencia)
              ->queryScalar();

              $conteocolumnzBA = $conteocolumnzBA + 1;
              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Competencia');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBA);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varcompetencia);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              
              $lastColumn++; 

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Nota Competencia');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBA);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['notacompetencia']);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              $lastColumn++;
            }

            $varNotaFinaBA = Yii::$app->db->createCommand("SELECT ROUND(AVG(erd.notacompetencia),2) AS NotaBA FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 1")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('AB'.$numCell, $varNotaFinaBA);


            $varlistarBloqueB = Yii::$app->db->createCommand("SELECT * FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 2")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryAll();

            $lastColumn = 'AC';
            $conteocolumnzBB = 0;
            foreach ($varlistarBloqueB as $key => $value) {
              $idcompetenciaB = $value['idevaluacioncompetencia'];
              $varcompetenciaB = Yii::$app->db->createCommand("SELECT ec.namecompetencia FROM tbl_evaluacion_competencias ec WHERE ec.anulado = 0 AND ec.idevaluacioncompetencia in (':idcompetenciaB')")
              ->bindValue(':idcompetenciaB', $idcompetenciaB)
              ->queryScalar();

              $conteocolumnzBB = $conteocolumnzBB + 1;
              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Organizacional');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBB);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varcompetenciaB);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              
              $lastColumn++; 

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Nota Organizacional');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBB);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['notacompetencia']);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              $lastColumn++;
            }

            $varNotaFinaBB = Yii::$app->db->createCommand("SELECT ROUND(AVG(erd.notacompetencia),2) AS NotaBA FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 2")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('AG'.$numCell, $varNotaFinaBB);


            $varlistarBloqueC = Yii::$app->db->createCommand("SELECT * FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 3")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryAll();

            $lastColumn = 'AH';
            $conteocolumnzBC = 0;
            foreach ($varlistarBloqueC as $key => $value) {
              $idcompetenciaC = $value['idevaluacioncompetencia'];
              $varcompetenciaC = Yii::$app->db->createCommand("SELECT ec.namecompetencia FROM tbl_evaluacion_competencias ec WHERE ec.anulado = 0 AND ec.idevaluacioncompetencia in (':idcompetenciaC')")
              ->bindValue(':idcompetenciaC', $idcompetenciaC)
              ->queryScalar();

              $conteocolumnzBC = $conteocolumnzBC + 1;
              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Desempeno');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBC);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varcompetenciaC);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              
              $lastColumn++; 

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Nota Desempeno');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBC);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['notacompetencia']);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
              
              $lastColumn++; 
            }

            $varNotaFinaBC = Yii::$app->db->createCommand("SELECT ROUND(AVG(erd.notacompetencia),2) AS NotaBA FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 3")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('AP'.$numCell, $varNotaFinaBC);

            $varcomentarios2 = null;
            $can = 0;
            $varcomentarios = Yii::$app->db->createCommand("select es.comentarios FROM tbl_evaluacion_solucionado es WHERE es.documentoevaluado = ':vardocumento' AND  es.comentarios != ''")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryAll();
            foreach ($varcomentarios as $key => $value) {
                $can = $can + 1;
                $varcomentarios2 = $varcomentarios2.' '.$can.'-. '.$value['comentarios'];            
            }

            $phpExc->getActiveSheet()->setCellValue('AQ'.$numCell, $varcomentarios2);

            $varcomentariosfeedback = null;
            $varcomentariosfeedback = Yii::$app->db->createCommand("SELECT er.observacion_feedback FROM tbl_evaluacion_resulta_feedback er WHERE er.anulado = 0 AND  er.documento IN (':vardocumento')")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('AR'.$numCell, $varcomentariosfeedback);



            $numCell = $numCell + 1;  
          }
          $hoy = getdate();
              $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_ArchivoEvalDlloOpcion1";
                  
              $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                    
            $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
              $tmpFile.= ".xls";

              $objWriter->save($tmpFile);

              $message = "<html><body>";
              $message .= "<h3>Se ha realizado el envio correcto del archivo de la evaluacion de desarrollo opcion 1</h3>";
              $message .= "</body></html>";

              Yii::$app->mailer->compose()
                            ->setTo($varcorreo)
                            ->setFrom(Yii::$app->params['email_satu_from'])
                            ->setSubject("Archivo Resultados Evaluacion Desarrollo Opcion 1")
                            ->attach($tmpFile)
                            ->setHtmlBody($message)
                            ->send();

              return $this->redirect('exportarrtadashboard',[
                'model' => $model,
                'varlistrtadesarrollo' => $varlistrtadesarrollo,
                'varnombrec' => $varnombrec,
                'varrol' => $varrol,
                'varrtaA' => $varrtaA,
                'varrtaJ' => $varrtaJ,
                'varrtaP' => $varrtaP,
                'varrtaC' => $varrtaC,
                'txtProcentaje' => $txtProcentaje,
                'vardocumento' => $vardocumento,
              ]);
        }else{
          #code
        }

        return $this->renderAjax('enviararchivouno',[
          'model' => $model,
        ]);
      }

      public function actionEnviararchivodos(){
        $model = new EvaluacionDesarrollo();
        $varlistrtadesarrollo = null;
        $varnombrec = null;
        $varrol = null;
        $varrtaA = 0;
        $varrtaJ = 0;
        $varrtaP = 0;
        $varrtaC = 0;
        $txtProcentaje = 0;
        $vardocumento = null;

        $form = Yii::$app->request->post();
        if($model->load($form)){
          $varcorreo = $model->comentarios;

          $phpExc = new \PHPExcel();
          $phpExc->getProperties()
                  ->setCreator("Konecta")
                  ->setLastModifiedBy("Konecta")
                  ->setTitle("Archivo Resultados Evaluacion Desarrollo Opcion 2")
                  ->setSubject("Archivo Resultados Evaluacion Desarrollo Opcion 2")
                  ->setDescription("Este archivo contiene el proceso de la evaluacion de desarrollo opcion 1")
                  ->setKeywords("Archivo Resultados Evaluacion Desarrollo Opcion 2");
          $phpExc->setActiveSheetIndex(0);

          $phpExc->getActiveSheet()->setShowGridlines(False);

          $styleArray = array(
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

          $styleColorBA = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => 'F9BD4C'),
                  )
                );

          $styleColorBB = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => '22D7CF'),
                  )
                );

          $styleColorBC = array( 
                  'fill' => array( 
                      'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                      'color' => array('rgb' => '49de70'),
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

          $phpExc->getDefaultStyle()->applyFromArray($styleArrayBody);

          $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
          $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A1:AR1');

          $phpExc->getActiveSheet()->SetCellValue('A2','Datos Generales');
          $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A2:G2');
          
          $phpExc->getActiveSheet()->SetCellValue('A3','Cedula Evaluado');
          $phpExc->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('B3','Evaluado');
          $phpExc->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('C3','--');
          $phpExc->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('D3','Cedula Evaluador');
          $phpExc->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('E3','Evaluador');
          $phpExc->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('F3','Tipo');
          $phpExc->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('G3','Nota Final');
          $phpExc->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('H2','Bloque Compentencias');
          $phpExc->getActiveSheet()->getStyle('H2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('H2')->applyFromArray($styleColorBA);
          $phpExc->setActiveSheetIndex(0)->mergeCells('H2:AB2');

          $phpExc->getActiveSheet()->SetCellValue('AB3','Nota Bloque Compentencias');
          $phpExc->getActiveSheet()->getStyle('AB3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AB3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AB3')->applyFromArray($styleColorBA);

          $phpExc->getActiveSheet()->SetCellValue('AC2','Bloque Organizacional');
          $phpExc->getActiveSheet()->getStyle('AC2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AC2')->applyFromArray($styleColorBB);
          $phpExc->setActiveSheetIndex(0)->mergeCells('AC2:AG2');

          $phpExc->getActiveSheet()->SetCellValue('AG3','Nota Bloque Organizacional');
          $phpExc->getActiveSheet()->getStyle('AG3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AG3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AG3')->applyFromArray($styleColorBB);

          $phpExc->getActiveSheet()->SetCellValue('AH2','Bloque Desempeno');
          $phpExc->getActiveSheet()->getStyle('AH2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AH2')->applyFromArray($styleColorBC);
          $phpExc->setActiveSheetIndex(0)->mergeCells('AH2:AP2');

          $phpExc->getActiveSheet()->SetCellValue('AP3','Nota Bloque Desempeno');
          $phpExc->getActiveSheet()->getStyle('AP3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AP3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AP3')->applyFromArray($styleColorBC);

          $phpExc->getActiveSheet()->SetCellValue('AQ2','Comentarios y Observaciones');
          $phpExc->getActiveSheet()->getStyle('AQ2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AQ2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AQ2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('AQ2')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('AQ2:AR2');

          $phpExc->getActiveSheet()->SetCellValue('AQ3','Comentarios');
          $phpExc->getActiveSheet()->getStyle('AQ3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AQ3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AQ3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('AQ3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('AR3','Observaciones Feedback');
          $phpExc->getActiveSheet()->getStyle('AR3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('AR3')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('AR3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('AR3')->applyFromArray($styleArrayTitle);


          $txtlistadoDNI = Yii::$app->db->createCommand("SELECT er.documento, ed.idevaluador, ed.idevaluaciontipo, er.notafinal FROM tbl_evaluacion_desarrollo ed INNER JOIN tbl_evaluacion_resulta_feedback er ON ed.idevalados = er.documento  WHERE er.anulado = 0")->queryAll();

          $numCell = 4;
          foreach ($txtlistadoDNI as $key => $value) {
            $vardocumento = $value['documento'];
            $varevaluador = $value['idevaluador'];
            $vartipoevaluacion = $value['idevaluaciontipo'];

            $varnombrec = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua_feedback where documento = ':vardocumento'")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $varrtaA = '--';
            $varrtaJ = $varevaluador;
            $varrtaP = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua_feedback where documento = ':varevaluador'")
            ->bindValue(':varevaluador', $varevaluador)
            ->queryScalar();

            $varrol = null;
            if ($vartipoevaluacion == 1) {
              $varrol = 'Auto';
            }else{
              if ($vartipoevaluacion == 3) {
                $varrol = 'Jefe';
              }else{
                if ($vartipoevaluacion == 4) {
                  $varrol = 'Par';
                }else{
                  if ($vartipoevaluacion == 2) {
                    $varrol = 'A cargo';
                  }
                }
              }
            }
            $varrtaC = $varrol;

            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $vardocumento);
            $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $varnombrec);
            $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $varrtaA);
            $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $varrtaJ);
            $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $varrtaP);
            $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $varrtaC);
            $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $value['notafinal']);

            $varlistarBloqueA = Yii::$app->db->createCommand("SELECT * FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 1")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryAll();

            $lastColumn = 'H';
            $conteocolumnzBA = 0;
            foreach ($varlistarBloqueA as $key => $value) {
              $idcompetencia = $value['idevaluacioncompetencia'];
              $varcompetencia = Yii::$app->db->createCommand("SELECT ec.namecompetencia FROM tbl_evaluacion_competencias ec WHERE ec.anulado = 0 AND ec.idevaluacioncompetencia in (':idcompetencia')")
              ->bindValue(':idcompetencia', $idcompetencia)
              ->queryScalar();

              $conteocolumnzBA = $conteocolumnzBA + 1;
              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Competencia');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBA);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varcompetencia);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              
              $lastColumn++; 

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Nota Competencia');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBA);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['notacompetencia']);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              $lastColumn++;
            }

            $varNotaFinaBA = Yii::$app->db->createCommand("SELECT ROUND(AVG(erd.notacompetencia),2) AS NotaBA FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 1")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('AB'.$numCell, $varNotaFinaBA);


            $varlistarBloqueB = Yii::$app->db->createCommand("SELECT * FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 2")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryAll();

            $lastColumn = 'AC';
            $conteocolumnzBB = 0;
            foreach ($varlistarBloqueB as $key => $value) {
              $idcompetenciaB = $value['idevaluacioncompetencia'];
              $varcompetenciaB = Yii::$app->db->createCommand("SELECT ec.namecompetencia FROM tbl_evaluacion_competencias ec WHERE ec.anulado = 0 AND ec.idevaluacioncompetencia in (':idcompetenciaB')")
              ->bindValue(':idcompetenciaB', $idcompetenciaB)
              ->queryScalar();

              $conteocolumnzBB = $conteocolumnzBB + 1;
              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Organizacional');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBB);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varcompetenciaB);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              
              $lastColumn++; 

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Nota Organizacional');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBB);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['notacompetencia']);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              $lastColumn++;
            }

            $varNotaFinaBB = Yii::$app->db->createCommand("SELECT ROUND(AVG(erd.notacompetencia),2) AS NotaBA FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 2")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('AG'.$numCell, $varNotaFinaBB);


            $varlistarBloqueC = Yii::$app->db->createCommand("SELECT * FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 3")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryAll();

            $lastColumn = 'AH';
            $conteocolumnzBC = 0;
            foreach ($varlistarBloqueC as $key => $value) {
              $idcompetenciaC = $value['idevaluacioncompetencia'];
              $varcompetenciaC = Yii::$app->db->createCommand("SELECT ec.namecompetencia FROM tbl_evaluacion_competencias ec WHERE ec.anulado = 0 AND ec.idevaluacioncompetencia in (':idcompetenciaC')")
              ->bindValue(':idcompetenciaC', $idcompetenciaC)
              ->queryScalar();

              $conteocolumnzBC = $conteocolumnzBC + 1;
              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Desempeno');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBC);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $varcompetenciaC);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);

              
              $lastColumn++; 

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.'3','Nota Desempeno');
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.'3')->applyFromArray($styleColorBC);

              $phpExc->getActiveSheet()->setCellValue($lastColumn.$numCell, $value['notacompetencia']);           
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
              
              $lastColumn++; 
            }

            $varNotaFinaBC = Yii::$app->db->createCommand("SELECT ROUND(AVG(erd.notacompetencia),2) AS NotaBA FROM tbl_evaluacion_rtafeedback_detalle erd WHERE erd.anulado = 0  AND erd.documento IN (':vardocumento') AND erd.idevaluacionbloques = 3")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('AP'.$numCell, $varNotaFinaBC);

            $varcomentarios2 = null;
            $can  = 0;
            $varcomentarios = Yii::$app->db->createCommand("select es.comentarios FROM tbl_evaluacion_solucionado es WHERE es.documentoevaluado = ':vardocumento' AND  es.comentarios != ''")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryAll();
            foreach ($varcomentarios as $key => $value) {
                $can = $can + 1;
                $varcomentarios2 = $varcomentarios2.' '.$can.'-. '.$value['comentarios'];            
            }

            $phpExc->getActiveSheet()->setCellValue('AQ'.$numCell, $varcomentarios2);

            $varcomentariosfeedback = null;
            $varcomentariosfeedback = Yii::$app->db->createCommand("SELECT er.observacion_feedback FROM tbl_evaluacion_resulta_feedback er WHERE er.anulado = 0 AND  er.documento IN (':vardocumento')")
            ->bindValue(':vardocumento', $vardocumento)
            ->queryScalar();

            $phpExc->getActiveSheet()->setCellValue('AR'.$numCell, $varcomentariosfeedback);


            $numCell = $numCell + 1; 
          }

          $hoy = getdate();
              $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_ArchivoEvalDlloOpcion2";
                  
              $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                    
            $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
              $tmpFile.= ".xls";

              $objWriter->save($tmpFile);

              $message = "<html><body>";
              $message .= "<h3>Se ha realizado el envio correcto del archivo de la evaluacion de desarrollo opcion 2</h3>";
              $message .= "</body></html>";

              Yii::$app->mailer->compose()
                            ->setTo($varcorreo)
                            ->setFrom(Yii::$app->params['email_satu_from'])
                            ->setSubject("Archivo Resultados Evaluacion Desarrollo Opcion 2")
                            ->attach($tmpFile)
                            ->setHtmlBody($message)
                            ->send();

              return $this->redirect('exportarrtadashboard',[
                'model' => $model,
                'varlistrtadesarrollo' => $varlistrtadesarrollo,
                'varnombrec' => $varnombrec,
                'varrol' => $varrol,
                'varrtaA' => $varrtaA,
                'varrtaJ' => $varrtaJ,
                'varrtaP' => $varrtaP,
                'varrtaC' => $varrtaC,
                'txtProcentaje' => $txtProcentaje,
                'vardocumento' => $vardocumento,
              ]);
        }else{
          #code
        }

        return $this->renderAjax('enviararchivodos',[
          'model' => $model,
        ]);
      }

      public function actionEnviargeneral(){
        $model = new EvaluacionDesarrollo();

        $form = Yii::$app->request->post();
        if($model->load($form)){
          $varcorreo = $model->comentarios;

          $paramsBusqueda = [':Anulado' => 0];
          $varListDocumentosGeneral = Yii::$app->db->createCommand('
          SELECT 
              ue.documento AS documentoevalua
                FROM tbl_usuarios_evalua ue 
                  WHERE 
                    ue.anulado = :Anulado')->bindValues($paramsBusqueda)->queryAll();

          $arraListGeneral = array();
          foreach ($varListDocumentosGeneral as $key => $value) {
            array_push($arraListGeneral, $value['documentoevalua']);
          }
          $varArrayDocumentos = implode(", ", $arraListGeneral);

          $paramsBuscarBloques = [':AnuladoBloque' => 0];
          $varListBloques = Yii::$app->db->createCommand('
          SELECT eb.idevaluacionbloques AS IdBloque, eb.namebloque AS NombreBloque FROM tbl_evaluacion_bloques eb 
            WHERE eb.anulado = :AnuladoBloque
                ORDER BY eb.idevaluacionbloques DESC')->bindValues($paramsBuscarBloques)->queryAll();

          $phpExc = new \PHPExcel();
          $phpExc->getProperties()
                        ->setCreator("Konecta")
                        ->setLastModifiedBy("Konecta")
                        ->setTitle("Archivo de los Resultados Evaluacion Desarrollo General Opcion 1")
                        ->setSubject("Archivo Resultados Evaluacion Desarrollo Opcion 1")
                        ->setDescription("Este archivo contiene el proceso de la evaluacion de desarrollo general opcion 2")
                        ->setKeywords("Archivo Resultados Evaluacion Desarrollo General de la Opcion 1");
          $phpExc->setActiveSheetIndex(0);
      
          $phpExc->getActiveSheet()->setShowGridlines(False);
      
                $styleArray = array(
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
      
                $styleArrayfirst = array(              
                        'fill' => array( 
                                'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                                'color' => array('rgb' => 'BBB8B8'),
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

          $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
          $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A1:BF1');

          $phpExc->getActiveSheet()->SetCellValue('A2','Datos Evaluados');
          $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A2:C2');

          $phpExc->getActiveSheet()->SetCellValue('A3','Fecha Evaluaciones');
          $phpExc->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('B3','Nombre Evaluado');
          $phpExc->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('C3','Documento Evaluado');
          $phpExc->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('D2','Tipo Evaluaciones');
          $phpExc->getActiveSheet()->getStyle('D2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('D2')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('D2:G2');

          $phpExc->getActiveSheet()->SetCellValue('D3','Auto Evaluacion');
          $phpExc->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('E3','Evaluacion Jefe');
          $phpExc->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('F3','Evaluacion Par');
          $phpExc->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('G3','Evaluacion a Cargo');
          $phpExc->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArrayTitle);

          $varListGeneral = Yii::$app->db->createCommand("
                            SELECT es.fechacrecion AS FechaEvaluacion, es.documentoevaluado AS CcEvaluado, ue.nombre_completo AS NombreEvaluado FROM tbl_usuarios_evalua ue
                                INNER JOIN tbl_evaluacion_solucionado es ON 
                                  ue.documento = es.documentoevaluado 
                              WHERE 
                                es.anulado = 0
                                  AND es.documentoevaluado IN (':varArrayDocumentos') 
                                  GROUP BY es.documentoevaluado 
                                    ORDER BY es.documentoevaluado")
                                    ->bindValue(':varArrayDocumentos', $varArrayDocumentos)
                                    ->queryAll();
          
          $numCell = 4;
          foreach ($varListGeneral as $key => $value) {
            $varfechaevalua = $value['FechaEvaluacion'];
            $varNombreEvaluado = $value['NombreEvaluado'];
            $varCCEvaluado = $value['CcEvaluado'];

            $paramsBuscaComentario = [':DocEvaluado' => $value['CcEvaluado']];
            $varListComentarios = Yii::$app->db->createCommand('
              SELECT 
                es.comentarios AS Comentarios
                  FROM tbl_evaluacion_solucionado es 
                    WHERE 
                      es.documentoevaluado = :DocEvaluado
                        AND es.comentarios != ""')->bindValues($paramsBuscaComentario)->queryAll();

            $arrayComentarios = array();
            foreach ($varListComentarios as $key => $value) {
              array_push($arrayComentarios, $value['Comentarios']);
            }
            $varComentarios = implode(" -- ", $arrayComentarios);

            $varFeedbacks = Yii::$app->db->createCommand('
              SELECT 
                er.observacion_feedback 
                  FROM tbl_evaluacion_resulta_feedback er 
                    WHERE 
                      er.documento = :DocEvaluado')->bindValues($paramsBuscaComentario)->queryScalar();
            
            $paramsBuscarEvaluaciones = [':documentoevaluado'=>$varCCEvaluado];
            $varAutoEvalua = Yii::$app->db->createCommand('
              SELECT if(COUNT(es.idevaluaciontipo)>1,"Si","--") AS AutEvaluaConteo 
                FROM tbl_evaluacion_solucionado es 
                  WHERE es.anulado = 0
                    AND es.documentoevaluado IN (:documentoevaluado)
                      AND es.idevaluaciontipo = 1')->bindValues($paramsBuscarEvaluaciones)->queryScalar();

            $varJefeEvalua = Yii::$app->db->createCommand('
              SELECT if(COUNT(es.idevaluaciontipo)>1,"Si","--") AS AutEvaluaConteo 
                FROM tbl_evaluacion_solucionado es 
                  WHERE es.anulado = 0
                    AND es.documentoevaluado IN (:documentoevaluado)
                      AND es.idevaluaciontipo = 3')->bindValues($paramsBuscarEvaluaciones)->queryScalar();

            $varParEvalua = Yii::$app->db->createCommand('
              SELECT if(COUNT(es.idevaluaciontipo)>1,"Si","--") AS AutEvaluaConteo 
                FROM tbl_evaluacion_solucionado es 
                  WHERE es.anulado = 0
                    AND es.documentoevaluado IN (:documentoevaluado)
                      AND es.idevaluaciontipo = 4')->bindValues($paramsBuscarEvaluaciones)->queryScalar();

            $varCargoEvalua = Yii::$app->db->createCommand('
              SELECT if(COUNT(es.idevaluaciontipo)>1,"Si","--") AS AutEvaluaConteo 
                FROM tbl_evaluacion_solucionado es 
                  WHERE es.anulado = 0
                    AND es.documentoevaluado IN (:documentoevaluado)
                      AND es.idevaluaciontipo = 2')->bindValues($paramsBuscarEvaluaciones)->queryScalar();
            
            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $varfechaevalua);
            $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $varNombreEvaluado);
            $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $varCCEvaluado);
            $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $varAutoEvalua);
            $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $varJefeEvalua);
            $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $varParEvalua);
            $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $varCargoEvalua);

            $lastColumn = "H";
            $varNFinalGenerico = 0;
            $varNotaOne = 0;
            $varNotaDos = 0;
            $varNotaTres = 0;
            $arrayFinal = 0;
            foreach ($varListBloques as $key => $value) {
              $varNombreBloque = $value['NombreBloque'];
              $varIDBloques = $value['IdBloque'];

              $varColor = null;
              if ($value['IdBloque'] == "3") {                    
                $varColor = array( 
                      'fill' => array( 
                          'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                          'color' => array('rgb' => '49de70'),
                      )
                );
              }
              if ($value['IdBloque'] == "2") {
                $varColor = array( 
                        'fill' => array( 
                            'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                            'color' => array('rgb' => '22D7CF'),
                        )
                );
              }
              if ($value['IdBloque'] == "1") {
                $varColor = array( 
                          'fill' => array( 
                              'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                              'color' => array('rgb' => 'fbcb70'),
                          )
                );
              }

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,' - Bloque '.$varNombreBloque);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

              $paramsBusquedaPorcentajes = [':DocumentoEvaluado' => $varCCEvaluado, ':IdBloques' => $varIDBloques];

              $varListPorcentaje = Yii::$app->db->createCommand('
                SELECT 
                  eb.idevaluacionbloques AS IdBloque, eb.namebloque AS NombreBloque, 
                  ec.namecompetencia AS NombreCompetencia, ec.idevaluacioncompetencia AS IdCompetencia
                    FROM tbl_evaluacion_bloques eb
                      INNER JOIN tbl_evaluacion_solucionado es ON 
                        eb.idevaluacionbloques = es.idevaluacionbloques
                      INNER JOIN tbl_evaluacion_competencias ec ON 
                        ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                      INNER JOIN tbl_evaluacion_respuestas2 er ON 
                        er.idevaluacionrespuesta = es.idevaluacionrespuesta
                    WHERE 
                      es.anulado = 0
                        AND es.documentoevaluado IN (:DocumentoEvaluado)
                          AND eb.idevaluacionbloques = :IdBloques
                    GROUP BY ec.idevaluacioncompetencia
                      ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsBusquedaPorcentajes)->queryAll();
              
              foreach ($varListPorcentaje as $key => $value) {
                $paramsNotasEval = [':CompetenciaID'=>$value['IdCompetencia'], ':DocumentoEvaluado' => $varCCEvaluado, ':IdBloques' => $varIDBloques];

                $varNotasGeneral = 0;

                if ($varAutoEvalua == "Si" && $varJefeEvalua == "Si" && $varParEvalua == "--" && $varCargoEvalua == "--") {
                  $varNotaAuto = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (20 / 100) ), 2) AS Auto
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 1
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                  
                  $varNotaJefe = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (80 / 100) ), 2) AS Jefe
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 3
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                  
                  $varNotasGeneral = $varNotaAuto + $varNotaJefe;
                }

                if ($varAutoEvalua == "Si" && $varJefeEvalua == "Si" && $varParEvalua == "Si" && $varCargoEvalua == "--") {

                  $varNotaAutoC = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (15 / 100) ), 2) AS Auto
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 1
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();

                  $varNotaJefeC = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (70 / 100) ), 2) AS Jefe
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 3
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                  
                  $varNotaParesC = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (15 / 100) ), 2) AS Jefe
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 4
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                  
                  $varNotasGeneral = $varNotaAutoC + $varNotaJefeC + $varNotaParesC;
                }

                if ($varAutoEvalua == "Si" && $varJefeEvalua == "Si" && $varParEvalua == "--" && $varCargoEvalua == "Si") {
                  
                  $varNotaAutoB = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (10 / 100) ), 2) AS Auto
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 1
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                  
                  $varNotaJefeB = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (60 / 100) ), 2) AS Jefe
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 3
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();

                  $varNotaCargoB = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (30 / 100) ), 2) AS Jefe
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 2
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();

                  $varNotasGeneral = $varNotaAutoB + $varNotaJefeB + $varNotaCargoB;
                }

                if ($varAutoEvalua == "Si" && $varJefeEvalua == "Si" && $varParEvalua == "Si" && $varCargoEvalua == "Si") {
                  
                  $varNotaAutoA = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (5 / 100) ), 2) AS Auto
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 1
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                                    
                  $varNotaJefeA = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (60 / 100) ), 2) AS Jefe
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 3
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                  
                  $varNotaParesA = Yii::$app->db->createCommand('
                    SELECT 
                      FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (5 / 100) ), 2) AS Jefe
                        FROM tbl_evaluacion_bloques eb
                          INNER JOIN tbl_evaluacion_solucionado es ON 
                            eb.idevaluacionbloques = es.idevaluacionbloques
                          INNER JOIN tbl_evaluacion_competencias ec ON 
                            ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                          INNER JOIN tbl_evaluacion_respuestas2 er ON 
                            er.idevaluacionrespuesta = es.idevaluacionrespuesta
                        WHERE 
                          es.anulado = 0
                            AND es.documentoevaluado IN (:DocumentoEvaluado)
                              AND eb.idevaluacionbloques = :IdBloques
                                AND es.idevaluaciontipo = 4
                                  AND ec.idevaluacioncompetencia = :CompetenciaID
                        GROUP BY ec.idevaluacioncompetencia
                          ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                  
                  $varNotaCargoA = Yii::$app->db->createCommand('
                      SELECT 
                        FORMAT( ( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5) * (30 / 100) ), 2) AS Jefe
                          FROM tbl_evaluacion_bloques eb
                            INNER JOIN tbl_evaluacion_solucionado es ON 
                              eb.idevaluacionbloques = es.idevaluacionbloques
                            INNER JOIN tbl_evaluacion_competencias ec ON 
                              ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                            INNER JOIN tbl_evaluacion_respuestas2 er ON 
                              er.idevaluacionrespuesta = es.idevaluacionrespuesta
                          WHERE 
                            es.anulado = 0
                              AND es.documentoevaluado IN (:DocumentoEvaluado)
                                AND eb.idevaluacionbloques = :IdBloques
                                  AND es.idevaluaciontipo = 2
                                    AND ec.idevaluacioncompetencia = :CompetenciaID
                          GROUP BY ec.idevaluacioncompetencia
                            ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsNotasEval)->queryScalar();
                  
                  $varNotasGeneral = $varNotaAutoA + $varNotaJefeA + $varNotaParesA + $varNotaCargoA;
                }

                if ($varIDBloques == 1) {
                  $varNotaOne = $varNotaOne + $varNotasGeneral;
                  $varNFinalGenerico = round( (($varNotaOne / count($varListPorcentaje)) * (40 / 100)), 2);
                }
                if ($varIDBloques == 2) {
                    $varNotaDos = $varNotaDos + $varNotasGeneral;
                    $varNFinalGenerico = round( (($varNotaDos / count($varListPorcentaje)) * (20 / 100)), 2);
                }
                if ($varIDBloques == 3) {
                    $varNotaTres = $varNotaTres + $varNotasGeneral;
                    $varNFinalGenerico = round( (($varNotaTres / count($varListPorcentaje)) * (40 / 100)), 2);
                }
                
                $lastColumn++;

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$value['NombreCompetencia']);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($varColor);

                $lastColumn++;

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$varNotasGeneral);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($varColor);

              }

              $lastColumn++;

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'Nota del bloque '.$varNombreBloque.': ');
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

              $lastColumn++;

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$varNFinalGenerico);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

              $lastColumn++;

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'--');

              $arrayFinal = $arrayFinal + $varNFinalGenerico; 

            }

            $lastColumn++;

            $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'--');

            $lastColumn++;

            $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'Nota Final: ');
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

            $lastColumn++;

            $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$arrayFinal);
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
            $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

            $lastColumn++;

            $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'--');

            $lastColumn++;

            $phpExc->getActiveSheet()->SetCellValue('BE'.$numCell,$varComentarios);

            $phpExc->getActiveSheet()->SetCellValue('BF'.$numCell,$varFeedbacks);

            $numCell++;
          }

          $hoy = getdate();
          $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_ArchivoEvalDlloGeneralOpcion2";
                  
          $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                    
          $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
          $tmpFile.= ".xls";

          $objWriter->save($tmpFile);

          $message = "<html><body>";
          $message .= "<h3>Se ha realizado el envio correcto del archivo de la evaluacion de desarrollo general opcion 2</h3>";
          $message .= "</body></html>";

          Yii::$app->mailer->compose()
                            ->setTo($varcorreo)
                            ->setFrom(Yii::$app->params['email_satu_from'])
                            ->setSubject("Archivo Resultados Evaluacion Desarrollo General Opcion 1")
                            ->attach($tmpFile)
                            ->setHtmlBody($message)
                            ->send();

          return $this->redirect('exportarrtadashboard',[
                'model' => $model,
          ]);

        }else{
          #code
        }

        return $this->renderAjax('enviargeneral',[
          'model' => $model,
        ]);
      }


      public function actionEnviargeneraldos(){
        $model = new EvaluacionDesarrollo();

        $form = Yii::$app->request->post();
        if($model->load($form)){
          $varcorreo = $model->comentarios;
          $varPosicion = $model->idevaluaciontipo;

          $paramsBuscarBloques = [':AnuladoBloque' => 0];
          $varListBloques = Yii::$app->db->createCommand('
          SELECT eb.idevaluacionbloques AS IdBloque, eb.namebloque AS NombreBloque FROM tbl_evaluacion_bloques eb 
            WHERE eb.anulado = :AnuladoBloque
                ORDER BY eb.idevaluacionbloques DESC')->bindValues($paramsBuscarBloques)->queryAll();

          $phpExc = new \PHPExcel();
          $phpExc->getProperties()
                  ->setCreator("Konecta")
                  ->setLastModifiedBy("Konecta")
                  ->setTitle("Archivo de los Resultados Evaluacion Desarrollo General Opcion 2")
                  ->setSubject("Archivo Resultados Evaluacion Desarrollo General Opcion 2")
                  ->setDescription("Este archivo contiene el proceso de la evaluacion de desarrollo general opcion 2")
                  ->setKeywords("Archivo Resultados Evaluacion Desarrollo General Opcion 1");
          $phpExc->setActiveSheetIndex(0);

          $phpExc->getActiveSheet()->setShowGridlines(False);

          $styleArray = array(
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

          $styleArrayfirst = array(              
                  'fill' => array( 
                          'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                          'color' => array('rgb' => 'BBB8B8'),
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

          $phpExc->getActiveSheet()->SetCellValue('A1','KONECTA - CX MANAGEMENT');
          $phpExc->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A1')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A1:BF1');

          $phpExc->getActiveSheet()->SetCellValue('A2','Datos Evaluados y Evaluadores');
          $phpExc->getActiveSheet()->getStyle('A2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A2')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('A2:E2');

          $phpExc->getActiveSheet()->SetCellValue('A3','Fecha Evaluaciones');
          $phpExc->getActiveSheet()->getStyle('A3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('A3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('B3','Nombre Evaluador');
          $phpExc->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('B3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('C3','Documento Evaluador');
          $phpExc->getActiveSheet()->getStyle('C3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('C3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('D3','Nombre Evaluado');
          $phpExc->getActiveSheet()->getStyle('D3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('D3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('E3','Documento Evaluado');
          $phpExc->getActiveSheet()->getStyle('E3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('E3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('F2','Tipo Evaluaciones');
          $phpExc->getActiveSheet()->getStyle('F2')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArray);
          $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArrayTitle);
          $phpExc->setActiveSheetIndex(0)->mergeCells('F2:I2');

          $phpExc->getActiveSheet()->SetCellValue('F3','Auto Evaluacion');
          $phpExc->getActiveSheet()->getStyle('F3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('F3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('G3','Evaluacion Jefe');
          $phpExc->getActiveSheet()->getStyle('G3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('G3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('H3','Evaluacion Par');
          $phpExc->getActiveSheet()->getStyle('H3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('H3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('H3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('H3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('I3','Evaluacion a Cargo');
          $phpExc->getActiveSheet()->getStyle('I3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('I3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('I3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('I3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('BB3','Nota Final');
          $phpExc->getActiveSheet()->getStyle('BB3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('BB3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('BB3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('BB3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('BC3','Resultado Final');
          $phpExc->getActiveSheet()->getStyle('BC3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('BC3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('BC3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('BC3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('BE3','Comentarios Evaluacion');
          $phpExc->getActiveSheet()->getStyle('BE3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('BE3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('BE3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('BE3')->applyFromArray($styleArrayTitle);

          $phpExc->getActiveSheet()->SetCellValue('BF3','Comentarios Feedbacks');
          $phpExc->getActiveSheet()->getStyle('BF3')->getFont()->setBold(true);
          $phpExc->getActiveSheet()->getStyle('BF3')->applyFromArray($styleColor);
          $phpExc->getActiveSheet()->getStyle('BF3')->applyFromArray($styleArraySubTitle);
          $phpExc->getActiveSheet()->getStyle('BF3')->applyFromArray($styleArrayTitle);

          $varRtaAuto = null;
          $varRtaJefe = null;
          $varRtaPares = null;
          $varRtaCargo = null;
          $varSi = "Si";
          $varNo = "--";
          
          $paramsAnulado = [':Anulado' => 0, ':idPosicion' => $varPosicion];
          $varListEvaluados = Yii::$app->db->createCommand('
            SELECT DISTINCT  es.fechacrecion AS FechaEvaluacion, es.documento AS CcEvaluador, es.documentoevaluado AS CcEvaluado, 
            ue.nombre_completo AS NombreEvaluado, es.idevaluaciontipo AS TipoEvaluacion
              FROM tbl_evaluacion_solucionado es
                INNER JOIN tbl_usuarios_evalua ue ON 
                  es.documentoevaluado = ue.documento
                WHERE 
                  ue.anulado = :Anulado
                    AND ue.id_dp_posicion IN (:idPosicion)')->bindValues($paramsAnulado)->queryAll();
            
          $numCell = 4;
          foreach ($varListEvaluados as $key => $value) {
            $varfechaevalua = $value['FechaEvaluacion'];
            $varCCEvaluador =  null;
            $varCCEvaluado = null;
            $varNombreEvaluado = null;

            $paramsBuscarEvaluado = [':DocumentoEvaluador' => $value['CcEvaluador']];
            $varNombreEvaluador = Yii::$app->db->createCommand('
                SELECT ue.nombre_completo AS NombreEvaluador 
                  FROM tbl_usuarios_evalua ue 
                    WHERE 
                      ue.anulado = 0
                        AND ue.documento IN (:DocumentoEvaluador)')->bindValues($paramsBuscarEvaluado)->queryScalar();

            $paramsBuscaComentario = [':DocEvaluador'  => $value['CcEvaluador'], ':DocEvaluado' => $value['CcEvaluado']];
            $varComentarios = Yii::$app->db->createCommand('
                SELECT es.comentarios AS Comentarios
                  FROM tbl_evaluacion_solucionado es 
                    WHERE 
                      es.documento = :DocEvaluador
                        AND es.documentoevaluado = :DocEvaluado
                          AND es.comentarios != ""')->bindValues($paramsBuscaComentario)->queryScalar();

            if ($varComentarios == "") {
              $varComentarios = "--";
            }         

            $varFeedbacks = Yii::$app->db->createCommand('
                SELECT er.observacion_feedback 
                  FROM tbl_evaluacion_resulta_feedback er 
                    WHERE 
                      er.documento_jefe = :DocEvaluador
                        AND er.documento = :DocEvaluado')->bindValues($paramsBuscaComentario)->queryScalar();

            if ($varFeedbacks == "") {
              $varFeedbacks = "--";
            }

            if ($value['TipoEvaluacion'] == "1") {
              $varRtaAuto = $varSi;
            }else{
              $varRtaAuto = $varNo;
            }
            if ($value['TipoEvaluacion'] == "3") {
              $varRtaJefe = $varSi;
            }else{
              $varRtaJefe = $varNo;
            }
            if ($value['TipoEvaluacion'] == "4") {
              $varRtaPares = $varSi;
            }else{
              $varRtaPares = $varNo;
            }
            if ($value['TipoEvaluacion'] == "2") {
              $varRtaCargo = $varSi;
            }else{
              $varRtaCargo = $varNo;
            }

            $varCCEvaluador =  $value['CcEvaluador'];
            $varCCEvaluado = $value['CcEvaluado'];
            $varNombreEvaluado = $value['NombreEvaluado'];


            $phpExc->getActiveSheet()->setCellValue('A'.$numCell, $varfechaevalua);
            $phpExc->getActiveSheet()->setCellValue('B'.$numCell, $varNombreEvaluador);
            $phpExc->getActiveSheet()->setCellValue('C'.$numCell, $varCCEvaluador);
            $phpExc->getActiveSheet()->setCellValue('D'.$numCell, $varNombreEvaluado);
            $phpExc->getActiveSheet()->setCellValue('E'.$numCell, $varCCEvaluado);
            $phpExc->getActiveSheet()->setCellValue('F'.$numCell, $varRtaAuto);
            $phpExc->getActiveSheet()->setCellValue('G'.$numCell, $varRtaJefe);
            $phpExc->getActiveSheet()->setCellValue('H'.$numCell, $varRtaPares);
            $phpExc->getActiveSheet()->setCellValue('I'.$numCell, $varRtaCargo);

            $arrayFinal = array();
            $lastColumn = "J";
            foreach ($varListBloques as $key => $value) {
              $varNombreBloque = $value['NombreBloque'];
              $varIDBloques = $value['IdBloque'];

              $varColor = null;
              if ($value['IdBloque'] == "3") {                    
                $varColor = array( 
                  'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => '49de70'),
                  )
                );
              }

              if ($value['IdBloque'] == "2") {
                $varColor = array( 
                  'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => '22D7CF'),
                  )
                );
              }

              if ($value['IdBloque'] == "1") {
                $varColor = array( 
                  'fill' => array( 
                    'type' => \PHPExcel_Style_Fill::FILL_SOLID, 
                    'color' => array('rgb' => 'fbcb70'),
                  )
                );
              }

              $paramsBusquedaPorcentajes = [':DocumentoEvaluador' => $varCCEvaluador, ':DocumentoEvaluado' => $varCCEvaluado, ':IdBloques' => $varIDBloques];

              $varListPorcentaje = Yii::$app->db->createCommand('
                SELECT 
                es.documento AS CcEvaluador, es.documentoevaluado AS CcEvaluado, eb.idevaluacionbloques AS IdBloque, eb.namebloque AS NombreBloque, 
                ec.namecompetencia AS NombreCompetencia, ec.idevaluacioncompetencia AS IdCompetencia, sum(er.valor) AS Valor,
                FORMAT( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5), 2) PorcentajeCompetencia
                  FROM tbl_evaluacion_bloques eb
                    INNER JOIN tbl_evaluacion_solucionado es ON 
                      eb.idevaluacionbloques = es.idevaluacionbloques
                    INNER JOIN tbl_evaluacion_competencias ec ON 
                      ec.idevaluacioncompetencia = es.idevaluacioncompetencia
                    INNER JOIN tbl_evaluacion_respuestas2 er ON 
                      er.idevaluacionrespuesta = es.idevaluacionrespuesta
                  WHERE 
                    es.anulado = 0
                      AND es.documento IN (:DocumentoEvaluador)
                        AND es.documentoevaluado IN (:DocumentoEvaluado)
                          AND eb.idevaluacionbloques = :IdBloques
                  GROUP BY ec.idevaluacioncompetencia
                    ORDER BY eb.idevaluacionbloques DESC ')->bindValues($paramsBusquedaPorcentajes)->queryAll();
              
              $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,' - Cantidad bloque '.$varNombreBloque.': '.count($varListPorcentaje));
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

              $varNFinalGenerico = 0;
              $varNotaOne = 0;
              $varNotaDos = 0;
              $varNotaTres = 0;

              if (count($varListPorcentaje) != 0) {
                
                foreach ($varListPorcentaje as $key => $value) {
                  if ($varIDBloques == 1) {
                    $varNotaOne = $varNotaOne + $value['PorcentajeCompetencia'];

                    if (count($varListPorcentaje) != 0) {
                      $varNFinalGenerico = round( (($varNotaOne / count($varListPorcentaje)) * (40 / 100)), 2);
                    }else{
                      $varNFinalGenerico = 0;
                    }
                  }

                  if ($varIDBloques == 2) {
                    $varNotaDos = $varNotaDos + $value['PorcentajeCompetencia'];

                    if (count($varListPorcentaje) != 0) {
                      $varNFinalGenerico = round( (($varNotaDos / count($varListPorcentaje)) * (20 / 100)), 2);
                    }else{
                      $varNFinalGenerico = 0;
                    }
                  }

                  if ($varIDBloques == 3) {
                    $varNotaTres = $varNotaTres + $value['PorcentajeCompetencia'];

                    if (count($varListPorcentaje) != 0) {
                      $varNFinalGenerico = round( (($varNotaTres / count($varListPorcentaje)) * (40 / 100)), 2);
                    }else{
                      $varNFinalGenerico = 0;
                    }
                  }

                  $lastColumn++;

                  $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$value['NombreCompetencia']);
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($varColor);

                  $lastColumn++;

                  $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$value['PorcentajeCompetencia']);
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($varColor);

                }

                $lastColumn++;

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'Nota bloque '.$varNombreBloque.': ');
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

                $lastColumn++;

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$varNFinalGenerico);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

                $lastColumn++;

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'--');

                array_push($arrayFinal, $varNFinalGenerico);

              }else{

                $paramsBusquedaPorcentajesB = [':DocumentoEvaluadoB' => $varCCEvaluado, ':IdBloquesB' => $value['IdBloque'], ':TipoEvaluacions' => 1];

                $varListPorcentajeBloque = Yii::$app->db->createCommand('
                  SELECT ec.namecompetencia AS NombreCompetenciaB,
                  FORMAT( (SUM(er.valor)*100) / (COUNT(ec.idevaluacioncompetencia)*5), 2) PorcentajeCompetenciaB
                    FROM tbl_evaluacion_respuestas2 er 
                      INNER JOIN tbl_evaluacion_solucionado es ON 
                        er.idevaluacionrespuesta = es.idevaluacionrespuesta
                      INNER JOIN tbl_evaluacion_competencias ec ON 
                        es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                      INNER JOIN tbl_evaluacion_bloques eb ON 
                        ec.idevaluacionbloques = eb.idevaluacionbloques
                    WHERE 
                      eb.idevaluacionbloques IN (:IdBloquesB)
                        AND es.idevaluaciontipo = :TipoEvaluacions
                          AND es.documento IN (:DocumentoEvaluadoB)
                            AND es.documentoevaluado IN (:DocumentoEvaluadoB)
                    GROUP BY ec.namecompetencia')->bindValues($paramsBusquedaPorcentajesB)->queryAll();

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,' - Cantidad bloque '.$varNombreBloque.': '.count($varListPorcentajeBloque));
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

                foreach ($varListPorcentajeBloque as $key => $value) {
                  
                  if ($varIDBloques == 1) {
                    if (count($varListPorcentajeBloque) != 0) {
                      $varNotaOne = $varNotaOne + $value['PorcentajeCompetenciaB'];

                      $varNFinalGenerico = round( (($varNotaOne / count($varListPorcentajeBloque)) * (40 / 100)), 2);
                    }else{
                      $varNFinalGenerico = 0;
                    }
                  }

                  if ($varIDBloques == 2) {
                    if (count($varListPorcentajeBloque) != 0) {
                      $varNotaDos = $varNotaDos + $value['PorcentajeCompetenciaB'];

                      $varNFinalGenerico = round( (($varNotaDos / count($varListPorcentajeBloque)) * (20 / 100)), 2);
                    }else{
                      $varNFinalGenerico = 0;
                    }
                  }

                  if ($varIDBloques == 3) {
                    if (count($varListPorcentajeBloque) != 0) {
                      $varNotaTres = $varNotaTres + $value['PorcentajeCompetenciaB'];

                      $varNFinalGenerico = round( (($varNotaTres / count($varListPorcentajeBloque)) * (20 / 100)), 2);
                    }else{
                      $varNFinalGenerico = 0; 
                    }
                  }

                  $lastColumn++;

                  $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$value['NombreCompetenciaB']);
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($varColor);

                  $lastColumn++;

                  $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$value['PorcentajeCompetenciaB']);
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                  $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($varColor);

                }

                $lastColumn++;

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'Nota bloque '.$varNombreBloque.': ');
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

                $lastColumn++;

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,$varNFinalGenerico);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->getFont()->setBold(true);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArray);
                $phpExc->getActiveSheet()->getStyle($lastColumn.$numCell)->applyFromArray($styleArrayfirst);

                $lastColumn++;

                $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'--');

                array_push($arrayFinal, $varNFinalGenerico);

              }

              $varNotasFinal = 0;
              $varNotasFinal = array_sum($arrayFinal);

              $phpExc->getActiveSheet()->SetCellValue($lastColumn.$numCell,'--');   
              
              $lastColumn++;

              $phpExc->getActiveSheet()->SetCellValue('BB'.$numCell,'Nota Final');
              $phpExc->getActiveSheet()->getStyle('BB'.$numCell)->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle('BB'.$numCell)->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle('BB'.$numCell)->applyFromArray($styleArrayfirst);              

              $phpExc->getActiveSheet()->SetCellValue('BC'.$numCell,$varNotasFinal);
              $phpExc->getActiveSheet()->getStyle('BC'.$numCell)->getFont()->setBold(true);
              $phpExc->getActiveSheet()->getStyle('BC'.$numCell)->applyFromArray($styleArray);
              $phpExc->getActiveSheet()->getStyle('BC'.$numCell)->applyFromArray($styleArrayfirst);              

              $phpExc->getActiveSheet()->SetCellValue('BD'.$numCell,'--');

              $phpExc->getActiveSheet()->SetCellValue('BE'.$numCell,$varComentarios);

              $phpExc->getActiveSheet()->SetCellValue('BF'.$numCell,$varFeedbacks);

            }

            $numCell = $numCell + 1; 
          }            

          $hoy = getdate();
          $hoy = $hoy['year']."_".$hoy['month']."_".$hoy['mday']."_ArchivoEvalDlloGeneralOpcion2";
                  
          $objWriter = \PHPExcel_IOFactory::createWriter($phpExc, 'Excel5');
                    
          $tmpFile = tempnam(sys_get_temp_dir(), $hoy);
          $tmpFile.= ".xls";

          $objWriter->save($tmpFile);

          $message = "<html><body>";
          $message .= "<h3>Se ha realizado el envio correcto del archivo de la evaluacion de desarrollo general opcion 2</h3>";
          $message .= "</body></html>";

          Yii::$app->mailer->compose()
                            ->setTo($varcorreo)
                            ->setFrom(Yii::$app->params['email_satu_from'])
                            ->setSubject("Archivo Resultados Evaluacion Desarrollo General Opcion 2")
                            ->attach($tmpFile)
                            ->setHtmlBody($message)
                            ->send();

          return $this->redirect('exportarrtadashboard',[
                'model' => $model,
          ]);

        }else{
          #code
        }


        return $this->renderAjax('enviargeneraldos',[
          'model' => $model,
        ]);


      }

      public function actionImportardocumentos(){
        $model = new FormUploadtigo();
        $paramsList = [':Anulado' => 0];
        $dataList = Yii::$app->db->createCommand('
          SELECT * FROM tbl_evaluacion_documentosna
            WHERE 
              anulado = :Anulado')->bindValues($paramsList)->queryAll();

        if ($model->load(Yii::$app->request->post())) {

          $model->file = UploadedFile::getInstances($model, 'file');

          if ($model->file && $model->validate()) {
            foreach ($model->file as $file) {
              $fecha = date('Y-m-d-h-i-s');
              $user = Yii::$app->user->identity->username;
              $name = $fecha . '-' . $user;
              $file->saveAs('categorias/' . $name . '.' . $file->extension);
              $this->Importexcelcedulas($name);

              return $this->redirect('importardocumentos');
            }
          }
        }

        return $this->render('importardocumentos',[
          'model' => $model,
          'dataList' => $dataList,
        ]);
      }

      public function Importexcelcedulas($name){
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

          Yii::$app->db->createCommand()->insert('tbl_evaluacion_documentosna',[
                                        'documentosna' => $sheet->getCell("A".$row)->getValue(),
                                        'anulado' => 0,
                                        'usua_id' => Yii::$app->user->identity->id,
                                        'fechacreacion' => date("Y-m-d"),
                                        ])->execute(); 

        }

      }      

      public function actionEliminardocumentos($id){
        Yii::$app->db->createCommand('DELETE FROM tbl_evaluacion_documentosna WHERE idevaldodumentosna=:id')
          ->bindParam(':id',$id )
          ->execute();
        Yii::$app->session->setFlash('list','Lista Eliminada Exitosamente');
        return $this->redirect(['importardocumentos']);
      }


  }

?>
