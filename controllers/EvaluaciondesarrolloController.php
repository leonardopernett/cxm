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

  class EvaluacionDesarrolloController extends \yii\web\Controller {

    public function behaviors(){
      return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['importarusuarios','parametrizardatos','createnivel','verniveles','createeval','verevaluacion','createtipo','vertipo','createcompetencia','vercompetencia','createpreguntas','vercomportamiento','usuarios_evalua', 'createrespuestas', 'verrespuesta', 'importarcompetencia','importarcomporta', 'createbloque', 'verbloque','evaluacionauto','createautoeva','createautodesarrollo','novedadauto','evaluacionjefe','createjefeeva','novedadjefe','importarusuarioseval','importarmensaje','createmensaje','evaluacionpar','evaluacionpares','createautopares','createpardesarrollo','createjefedesarrollo','restringirevalua','novedadpares','evaluacioncargo','evaluaciondecargos','createautocargos','createcargodesarrollo','novedadcargos','novedadesglobales','novedadgeneral','editarplannovedad','feedbackresultado','gestionnovedades','createnovedadgeneral', 'evaluacionfeedback', 'validaevaluado', 'crearresultadofb', 'resultadoevaluacion','eliminarnovedades','editarnovedaddelete','paramsevaluacion'],
            'rules' => [
              [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isCuadroMando()  || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerevaluacion();
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

      $txtvarnamecargo = Yii::$app->get('dbjarvis2')->createCommand("select posicion from dp_posicion where estado = 1 and id_dp_posicion = $txtvarcargo")->queryScalar(); 

      $txtverificanivel = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_nivel where anulado = 0 and nivel = $txtvarnivel and cargo = $txtvarcargo")->queryScalar();

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
      //$txtfechamodificiacion = date("Y-m-d");

      Yii::$app->db->createCommand("truncate table tbl_usuarios_evalua")->execute();

      $query = Yii::$app->get('dbjarvis2')->createCommand("Select f.nombre_completo as nombre, a.documento as documento, b.id_dp_cargos as idcargo,
      b.id_dp_posicion as idposicion,b.id_dp_funciones as idfuncion,c.posicion as posicion,d.funcion as funcion,
      e.usuario_red as usuariored,	g.email_corporativo as correo, a.documento_jefe as documento_jefe,     
      TRIM(ifnull (if (a.id_dp_centros_costos != 0, dg3.nombre_completo, if (a.id_dp_centros_costos_adm != 0, ar.area_general, 'Sin informaci贸n')), 'Sin informaci贸n')) AS directorArea,
      TRIM(if (a.cod_pcrc != 0, cl1.cliente, if (a.id_dp_centros_costos != 0, cl2.cliente, if (a.id_dp_centros_costos_adm != 0, ar.area_general, 'Sin informaci贸n')))) AS clienteArea 
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
        AND d.funcion NOT IN('Operaci髇', 'Visitador' COLLATE utf8_unicode_ci)
        
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

                  WHERE a.documento = '$vardocumentojefe'")->queryAll();

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

      $varverifica = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where documento = '$txtvardocumento' and documentoevaluado = '$txtvardocumento' and idevaluacionbloques = $txtvaridbloque and idevaluacioncompetencia = $txtvaridcompetencia and idevaluacionpregunta = $txtvaridpreg and idevaluacionrespuesta = $txtvaridrta and anulado = 0")->queryScalar();

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

      $vardocumentjefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua where documento = $txtvardocumento group by documento_jefe")->queryScalar();

      $varverifica = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where documento = '$txtvardocumento' and documentoevaluado = '$vardocumentjefe' and idevaluacionbloques = $txtvaridbloque and idevaluacioncompetencia = $txtvaridcompetencia and idevaluacionpregunta = $txtvaridpreg and idevaluacionrespuesta = $txtvaridrta and anulado = 0")->queryScalar();

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
      }

      die(json_encode($varverifica)); 

    }

    public function actionCreatejefedesarrollo(){
      $txtvarocmentario = Yii::$app->request->get("txtvarocmentario");
      $txtvardocumento = Yii::$app->request->get("txtvardocumento");

      $vardocumentjefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua where documento = $txtvardocumento group by documento_jefe")->queryScalar();

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

                  $varidusua = Yii::$app->db->createCommand("select usua_id from tbl_usuarios where usua_usuario = '$varDatos[0]'")->queryScalar();
              
              
              
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

      $varverifica = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where documento = '$txtvardocumento' and documentoevaluado = '$vardocumentjefe' and idevaluacionbloques = $txtvaridbloque and idevaluacioncompetencia = $txtvaridcompetencia and idevaluacionpregunta = $txtvaridpreg and idevaluacionrespuesta = $txtvaridrta and anulado = 0")->queryScalar();

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

      $varverifica = 1;

      die(json_encode($varverifica)); 

    }

    public function actionRestringirevalua(){
      $sessiones = Yii::$app->user->identity->id;

      $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();

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

      $varconteorta = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where  documento = $txtvardocumento and documentoevaluado = $txtvaridpares and idevaluaciontipo = 4 and anulado = 0")->queryScalar();

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

      $varconteorta = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where  documento = $txtvardocumento and documentoevaluado = $txtvaridpares and idevaluaciontipo = 3 and anulado = 0")->queryScalar();

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

      $varverifica = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_solucionado where documento = '$txtvardocumento' and documentoevaluado = '$vardocumentjefe' and idevaluacionbloques = $txtvaridbloque and idevaluacioncompetencia = $txtvaridcompetencia and idevaluacionpregunta = $txtvaridpreg and idevaluacionrespuesta = $txtvaridrta and anulado = 0")->queryScalar();

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
      $varnombretipo  = Yii::$app->db->createCommand("select tipoevaluacion from tbl_evaluacion_tipoeval where anulado = 0 and idevaluaciontipo = $varidtipo")->queryScalar();      

      return $this->render('novedadesglobales',[
        'model' => $model,
        'varidtipo' => $varidtipo,
        'varnombretipo' => $varnombretipo,
        ]);
    }

    public function actionIngresarnovedadpares(){
      // $txtvaridpares = Yii::$app->request->get("txtvaridpares");
      // $txtvardocumento = Yii::$app->request->get("txtvardocumento");
      // $txtnovedades = "Novedades de pares";
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

      $varname = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua where anulado = 0 and documento = $txtvarIdcambiosNcargo group by documento")->queryScalar();

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
            $varlistJefe = Yii::$app->db->createCommand("select documento_jefe, nombre_jefe, id_cargo_jefe, cargo_jefe from tbl_usuarios_evalua where anulado = 0 and documento_jefe = $varcambioJefe group by documento_jefe")->queryAll();

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

            $vartipos = Yii::$app->db->createCommand("select distinct tipo from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = $varidevalua")->queryScalar();

            if ($varEstado == 1) {
              if ($vartipos == 1) {
                $vardocumentboss = Yii::$app->db->createCommand("select distinct documento from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = $varidevalua")->queryScalar();
                $vardocumentnewperson = Yii::$app->db->createCommand("select distinct cambios from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = $varidevalua")->queryScalar();

                $varlistnewboss = Yii::$app->db->createCommand("select distinct documento_jefe, nombre_jefe, id_cargo_jefe, cargo_jefe, directorarea, clientearea from tbl_usuarios_evalua where anulado = 0 and documento_jefe = $vardocumentboss")->queryAll();

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
                $vardocumentnewboss = Yii::$app->db->createCommand("select distinct comentarios from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = $varidevalua")->queryScalar();
                $vardocumentperson = Yii::$app->db->createCommand("select distinct cambios from tbl_evaluacion_novedadescargo where anulado = 0 and idnovedadesc = $varidevalua")->queryScalar();

                $varlistnewboss = Yii::$app->db->createCommand("select distinct documento_jefe, nombre_jefe, id_cargo_jefe, cargo_jefe, directorarea, clientearea from tbl_usuarios_evalua where anulado = 0 and documento_jefe = $vardocumentnewboss")->queryAll();

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
          $txtvaridpares = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua where documento = $txtvarvardocument group by documento_jefe")->queryScalar();
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

    $vardocumento1 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = $txtvardocumento and idevaluaciontipo = 1 group by documentoevaluado")->queryScalar();
    $vardocumento2 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = $txtvardocumento and idevaluaciontipo = 3 group by documentoevaluado")->queryScalar();
    
    $vardocumentojefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua WHERE documento = $txtvardocumento")->queryScalar();
    
    $varevaluoaljefe = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documento = $txtvardocumento AND idevaluaciontipo = 2 group by documentoevaluado")->queryScalar();

    if($vardocumentojefe > 1){
      $varnopares = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_novedadesgeneral WHERE documento = $txtvardocumento AND aprobado = 1")->queryScalar();
      if($varnopares > 0){
        $vardocumento3 = 0;
      }else {
        $vardocumento3 = Yii::$app->db->createCommand("select documentoevaluado from tbl_evaluacion_solucionado where documentoevaluado = $txtvardocumento and idevaluaciontipo = 3 group by documentoevaluado")->queryScalar();
      } 
    }
    $varcantipares = Yii::$app->db->createCommand("select COUNT(documento_jefe) from tbl_usuarios_evalua WHERE documento_jefe = $vardocumentojefe")->queryScalar();
   
    if($vardocumento1 && $vardocumento2 && $varevaluoaljefe){
      $txtRta = 1;
    } else{
      $txtRta = 0;
    }
    /*if($vardocumentojefe > 1 && $vardocumento3 = 0){
      $txtRta = 1;
    } else if($vardocumentojefe > 1 && $vardocumento3){
        $txtRta = 1;
      } else {
        $txtRta = 0;
      }*/
    
    die(json_encode($txtRta));
  }

   public function actionCrearresultadofb(){
    $txtvarobservafeedback = Yii::$app->request->get("varobservafeedback");
    $txtvarNotafinal = Yii::$app->request->get("varNotafinal");
    $txtvardocumento = Yii::$app->request->get("vardocumento");
    $txtvardocumentojefe = Yii::$app->request->get("vardocumentojefe");
    $txtvartipocoaching = Yii::$app->request->get("vartipocoaching");
    $txtvalidadocumento = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_resulta_feedback WHERE documento = $txtvardocumento")->queryScalar();
    //$txtEmail = Yii::$app->db->createCommand("select email_corporativo from tbl_usuarios_evalua WHERE documento = $txtvardocumento")->queryScalar();
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

        $txtrta = 1;

        $message = "<html><body>";
                      $message .= "<h3>Notificaci贸n: Se concluye con el proceso de evaluaciones, puedes ingresar a revisar tus resultados y sugerencias. </h3>";
                      $message .= "</body></html>";

                Yii::$app->mailer->compose()
                                ->setTo($txtEmail)
                                ->setFrom(Yii::$app->params['email_satu_from'])
                                ->setSubject("Notificaci贸n, Revisi贸n de resultados de las evaluaciones")
                                //->attach("")
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
          Yii::$app->db->createCommand("delete from tbl_evaluacion_solucionado where documento = $varidnombre and documentoevaluado = $varidevaluado and idevaluaciontipo = $vartipoeva and anulado = 0")->execute();
        
          Yii::$app->db->createCommand("delete from tbl_evaluacion_desarrollo where idevaluador = $varidnombre and idevalados = $varidevaluado and idevaluaciontipo = $vartipoeva and anulado = 0")->execute();
        }

        if ($vartipoeva == 4 && $varidevaluado == null) {
          Yii::$app->db->createCommand("delete from tbl_evaluacion_novedadesgeneral where documento = $varidnombre and anulado = 0")->execute();
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

          $varrtaauto = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo ed where ed.anulado = 0 and ed.idevaluador = $varusuarioid and ed.idevalados = $varusuarioid")->queryscalar();

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

          $varjefe = Yii::$app->db->createCommand("select documento_jefe from tbl_usuarios_evalua where documento = $varusuarioid and anulado = 0 group by documento_jefe")->queryScalar();

          $varrtajefe = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo ed where ed.anulado = 0 and ed.idevaluador = $varusuarioid and ed.idevalados = $varjefe")->queryscalar();

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

          $varlistpares = Yii::$app->db->createCommand("select ue.documento from tbl_usuarios_evalua ue where ue.documento_jefe = '$varjefe' and ue.documento != '$varusuarioid' group by ue.documento")->queryAll();

          foreach ($varlistpares as $key => $value) {
            $varpar = $value['documento'];
            $varrtapar = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo ed where ed.anulado = 0 and ed.idevaluador = $varusuarioid and ed.idevalados = $varpar")->queryscalar();

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

          $varlistcargo = Yii::$app->db->createCommand("select ue.documento from tbl_usuarios_evalua ue where ue.documento_jefe = '$varusuarioid' and ue.documento != '$varusuarioid' group by ue.documento")->queryAll();

          foreach ($varlistcargo as $key => $value) {
            $varcargo = $value['documento'];
            $varrtacargo = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo ed where ed.anulado = 0 and ed.idevaluador = $varusuarioid and ed.idevalados = $varcargo")->queryscalar();

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

        $varconteo = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_desarrollo where anulado = 0 and idevaluador = $varevaluador and idevalados = $varevaluado and idevaluaciontipo = $vartipo")->queryScalar();

        if ($varconteo != 0) {
          Yii::$app->db->createCommand("update tbl_evaluacion_cumplimiento set idresultado = $varconteo where idevaluacioncumplimiento = $varidlinea and anulado = 0")->execute();
        }
      }

      return $this->render('paramsevaluacion',[
        'model' => $model,
        ]);
    }

    public function actionListarcedulas(){            
            $txtAnulado = 0; 
            $txtId = Yii::$app->request->get('id'); 
            
            $sessiones = Yii::$app->user->identity->id;
            $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();                     

            if ($txtId) {
                $txtControl = Yii::$app->db->createCommand("select count(1) from tbl_usuarios_evalua ue inner join tbl_evaluacion_solucionado es on ue.documento = es.documentoevaluado where es.documento = $vardocument and es.anulado = 0 and es.idevaluaciontipo = $txtId group by ue.documento")->queryScalar();
                          // \app\models\EvaluacionSolucionado::find()->distinct()
                          // ->select("tbl_usuarios_evalua.nombre_completo, tbl_usuarios_evalua.documento")
                          // ->join('LEFT OUTER JOIN','tbl_evaluacion_solucionado eu',
                          //     'eu.documentoevaluado  = tbl_usuarios_evalua.documento')
                          // ->where("eu.anulado = 0")
                          // ->andwhere("eu.idevaluaciontipo = $txtId")
                          // ->andwhere("eu.documento = '$vardocument'")
                          // ->count();

                if ($txtControl > 0) {
                  $varListaPcrc = Yii::$app->db->createCommand("select ue.nombre_completo, ue.documento from tbl_usuarios_evalua ue inner join tbl_evaluacion_solucionado es on ue.documento = es.documentoevaluado where es.documento = $vardocument and es.anulado = 0 and es.idevaluaciontipo = $txtId group by ue.documento")->queryAll();
                          // \app\models\EvaluacionSolucionado::find()->distinct()
                          // ->select("tbl_usuarios_evalua.nombre_completo as nombre_completo, tbl_usuarios_evalua.documento as documento")
                          // ->join('LEFT OUTER JOIN','tbl_evaluacion_solucionado',
                          //     'tbl_usuarios_evalua.documento = tbl_evaluacion_solucionado.documentoevaluado')
                          // ->where("tbl_evaluacion_solucionado.anulado = 0")
                          // ->andwhere("tbl_evaluacion_solucionado.idevaluaciontipo = $txtId")
                          // ->andwhere("tbl_evaluacion_solucionado.documento = '$vardocument'")
                          // ->orderBy(['tbl_usuarios_evalua.nombre_completo' => SORT_DESC])
                          // ->all();  
           
                    $valor = 0;
                    
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
          Yii::$app->db->createCommand("update tbl_evaluacion_eliminarusuarios set aprobado = 1 where ideliminarusuarios = $varidnov and anulado = 0")->execute();

          $varlistusuario = Yii::$app->db->createCommand("select distinct * from tbl_usuarios_evalua u where u.documento = '$varevaluados' and u.anulado = 0")->queryAll();
          // var_dump($varlistusuario);

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

          Yii::$app->db->createCommand("delete from tbl_usuarios_evalua  where documento = '$varevaluados' and anulado = 0")->execute();
        }else{
          Yii::$app->db->createCommand("update tbl_evaluacion_eliminarusuarios set aprobado = 2 where ideliminarusuarios = $varidnov and anulado = 0")->execute();
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
          $varname = Yii::$app->db->createCommand("select distinct nombre_completo from tbl_usuarios_evalua u where u.documento = '$vardocumento' and u.anulado = 0")->queryScalar();
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

        Yii::$app->db->createCommand("update tbl_evaluacion_novedadesgeneral set documento = '$txtvars', anulado = 1 where documento = '$txtvardocumento' and anulado = 0")->execute();       

        $varverifica = 1;

        die(json_encode($varverifica));
      }

      public function actionExportarlista2(){
        $varCorreo = Yii::$app->request->get("var_Destino");
        $sessiones = Yii::$app->user->identity->id;

        $varlistusuarios = Yii::$app->db->createCommand("select ec.cedulaevaluador 'idevaluador', ue.nombre_completo 'evaluador', ec.cedulaevaluado 'idevaluado', (select distinct eu.nombre_completo from tbl_usuarios_evalua eu where eu.documento = ec.cedulaevaluado) 'nombre_evaluado',  et.tipoevaluacion 'tipo_evaluacion', if (ec.idresultado = 1, 'Realizado', 'Sin realizar') 'Resultado',  ec.directorarea 'Director', ec.clientearea 'Area' from tbl_usuarios_evalua ue inner join tbl_evaluacion_cumplimiento ec on ue.documento = ec.cedulaevaluador inner join tbl_evaluacion_tipoeval et on   ec.idtipoevalua = et.idevaluaciontipo where ec.anulado = 0 group by ec.cedulaevaluador, ec.cedulaevaluado")->queryAll();

        $phpExc = new \PHPExcel();
        $phpExc->getProperties()
                ->setCreator("Konecta")
                ->setLastModifiedBy("Konecta")
                ->setTitle("Lista de usuarios - Evaluaci髇 Desarrollo")
                ->setSubject("Evaluaci髇 de Desarrollo")
                ->setDescription("Este archivo contiene el listado de los usuarios registrados para la evaluaci髇 de desarrollo")
                ->setKeywords("Lista de usuarios");
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
        $phpExc->getActiveSheet()->getStyle('F2')->applyFromArray($styleArraySubTitle2);;

        $phpExc->getActiveSheet()->SetCellValue('G2','DIRECTOR');
        $phpExc->getActiveSheet()->getStyle('G2')->getFont()->setBold(true);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArray);            
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleColor);
        $phpExc->getActiveSheet()->getStyle('G2')->applyFromArray($styleArraySubTitle2);;

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

  }

?>
