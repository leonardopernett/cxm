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
use PHPExcel;
use PHPExcel_IOFactory;
use yii\base\Exception;

use app\models\GestorEvaluacionPreguntas;
use app\models\GestorEvaluacionRespuestas;
use app\models\FormUploadtigo;
use app\models\GestorEvaluacionFormulario;
use app\models\GestorEvaluacionEstadoEval;
use app\models\GestorEvaluacionRespuestasForm;
use app\models\GestorEvaluacionDatosForm;
use app\models\GestorEvaluacionCalificacionTotal;
use app\models\GestorEvaluacionCalificaPorPregunta;
use app\models\GestorEvaluacionFeedback;
use app\models\GestorEvaluacionFeedbackentradas;
use app\models\GestorEvaluacionNovedadGeneral;
use app\models\GestorEvaluacionNovedadJefeincorrecto;
use app\models\GestorEvaluacionNovedadJefecolaborador;
use app\models\GestorEvaluacionNovedadEliminareval;



class GestorevaluaciondesarrolloController extends \yii\web\Controller {

    public function behaviors(){
        return[
        
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','parametrizador', 'cargardatostablapreguntas', 'crearpregunta', 'editarpregunta', 'eliminarpregunta',
                        'cargardatostablarespuestas', 'createrespuesta', 'editrespuesta', 'deleterespuesta',
                        'autoevaluacion', 'crearautoevaluacion',
                        'modalevaluacionacargo', 'evaluacionacargo', 'crearevaluacionacargo',
                        'resultados', 'resultadoindividual',
                        'crearfeedback', 'feedbackfinal', 'crearfeedbackfinal', 'modalfeedbackcolaborador', 'modalnovedadauto',
                        'novedadjefeincorrecto', 'novedadpersonalacargo', 'novedadeliminarevaluacion' , 'novedadotrosinconvenientes' ,
                        'actualizarjefecorrecto', 'modalnovedadacargo'
                    ],
            'rules' => [
                [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isCuadroMando()  || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerevaluacion() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isReportes();
                        },
                ]
            ]
            ],
        'verbs' => [          
            'class' => VerbFilter::className(),
            'actions' => [
            'delete' => ['post']
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

        $model = new GestorEvaluacionNovedadEliminareval();
        $sessiones = Yii::$app->user->identity->id;
        $estado_autoevaluacion=0;
        $id_evalua_nombre = "";
        $evalua_nombre = "";        
        $nom_solicitante="Sin datos";
        $documento = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
        $existe_usuario = Yii::$app->db->createCommand("select count(u.identificacion) AS cant_registros, u.id_gestor_evaluacion_usuarios, u.nombre_completo, u.es_jefe, u.es_colaborador from tbl_gestor_evaluacion_usuarios u where identificacion in ('$documento')")->queryOne();
        $completo_todas_las_evaluaciones_asociadas = false;
        $no_tiene_jefe_directo= false;
        $novedades_autoevaluacion=0;
        $array_personas_a_cargo = [];
        $opcion_personas_a_cargo=[]; 
        $varcargo =0;    
        
        //Id de los tipos de evaluación
        $id_autoevaluacion = $this->obtener_id_autoevaluacion();
        $id_evaluacion_a_cargo = $this->obtener_id_evaluacion_a_cargo();
        
        //Información periodo de evaluación actual
        $evaluacion_actual = $this->obtenerEvaluacionActual();
        if($evaluacion_actual){
            $id_evalua_nombre = $evaluacion_actual['id_evalua'];
            $evalua_nombre = $evaluacion_actual['nombreeval'];
        }   
        
        if($existe_usuario['cant_registros']=='1'){
            
            $nom_solicitante = $existe_usuario['nombre_completo'];            
            
            $esjefe = $existe_usuario['es_jefe'];
            $esColaborador = $existe_usuario['es_colaborador'];

            if($esjefe!=null){
                $id_usuario = $existe_usuario['id_gestor_evaluacion_usuarios'];    
            }

            if($esjefe==null && $esColaborador!=null){
                $id_usuario = $existe_usuario['id_gestor_evaluacion_usuarios'];    
            }
            
            //Si es Jefe tiene personas a cargo
            $array_personas_a_cargo = $this->obtenerTodasLasPersonasAcargo($id_usuario);
            $opcion_personas_a_cargo = ArrayHelper::map($array_personas_a_cargo, 'id_colaborador', 'nom_colaborador'); 
            
            $array_personas_evaluadas = $this->obtenerPersonasAcargoSinEvaluar($id_usuario);
            $varcargo= count($array_personas_evaluadas);
           
            //Si completo todas las evaluaciones asociadas a un periodo de tiempo
            $completo_todas_las_evaluaciones_asociadas = $this->verificarEstadoEvaluaciones($id_usuario, $id_evalua_nombre);
            //Si tiene solo una evaluacion (solo es colaborador) retorna 1 si teine mas es un JEFE 
            $cant_evaluaciones_usuario= $this->obtenerTotalEvaluacionesParaUnUsuario($id_usuario);
           
            if($cant_evaluaciones_usuario==1){
                $no_tiene_jefe_directo=true; //Para mostrar si NO TIENE JEFE DIRECTO                          
            }
            //Si esta completada o imcompleta la autoevaluacion, para habilitar o no el boton
            $estado_autoevaluacion = $this->verificarEstadoCompletadoDeUnTipoDeEvaluacion($id_usuario, $id_autoevaluacion, $id_evalua_nombre);
            $novedades_autoevaluacion = $this->obtenerNombreEstadoNovedad($documento, $id_evalua_nombre);
                        
        }

        if($existe_usuario['cant_registros']==0){
            $id_usuario = false;
            $esjefe = false;
        }        

        if($existe_usuario['id_gestor_evaluacion_usuarios']!=null){
            $varauto = (new \yii\db\Query())
            ->select('id_estado_evaluacion')
            ->from('tbl_gestor_evaluacion_formulario')
            ->where(['id_evaluacionnombre' => $evaluacion_actual])
            ->andWhere(['id_tipo_evalua' => 1 ])
            ->andWhere(['id_evaluador' => $id_usuario ])
            ->scalar();         
                
            if($varauto){
                $estado_autoevaluacion = $varauto;
            }

        }
        //$id_estado = Yii::$app->db->createCommand("select id_gestor_evaluacion_estadoeval FROM tbl_gestor_evaluacion_estadoeval WHERE  estado= 'Incompleto' and anulado = 0")->queryScalar();
       
        //SI YA TIENE TODAS LAS CALIFICACIONES MOSTRAR MENSAJE DE QUE PUEDE IR  A VERLAS SINO EN ESPERA DE EVALAUCION A CARGO***

        //Si ya no tienes personas a cargo para evaluar mostrar completado
       

      return $this->render('index', [
          'model'=> $model,
          'id_evaluacion_actual' => $evaluacion_actual['id_evalua'],
          'estado_autoevaluacion' => $estado_autoevaluacion,
          'novedades_autoevaluacion'=>$novedades_autoevaluacion,
          'existe_usuario' => $existe_usuario,
          'id_usuario' => $id_usuario,
          'nom_solicitante'=> $nom_solicitante,
          'documento'=> $documento,
          'esjefe' => $esjefe,
          'completo_todas_las_evaluaciones_asociadas' => $completo_todas_las_evaluaciones_asociadas,
          'no_tiene_jefe_directo' => $no_tiene_jefe_directo,
          'opcion_personas_a_cargo'=> $opcion_personas_a_cargo,
          'varcargo'=>$varcargo
      ]);
    }

    public function actionParametrizador(){
        $modalPreguntas = new GestorEvaluacionPreguntas();
        $modalRespuestas = new GestorEvaluacionRespuestas();
        

        return $this->render('parametrizador', [
            'modalPreguntas' => $modalPreguntas,
            'modalRespuestas' => $modalRespuestas,
        ]);
    } 

    public function actionCargardatostablapreguntas(){ 

        $id_evaluacion = Yii::$app->request->get('id');

        $datos = GestorEvaluacionPreguntas::find()
        ->select(['id_evaluacionnombre', 'id_gestorevaluacionpreguntas', 'nombrepregunta', 'descripcionpregunta'])
        ->where(['id_evaluacionnombre' => $id_evaluacion,
                  'anulado'=>'0'])
        ->asArray()
        ->all();  

        $response = [
            'status' => 'success',
            'data' => $datos,
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $response;
    }

    public function actionCrearpregunta(){

        $model = new GestorEvaluacionPreguntas();

        // Asignar los valores al modelo
        $model->id_evaluacionnombre = Yii::$app->request->post('id_evaluacion');
        $model->nombrepregunta = Yii::$app->request->post('nom_pregunta');
        $model->descripcionpregunta =  Yii::$app->request->post('descripcion_pregunta');
        $model->fechacreacion = date("Y-m-d");
        $model->usua_id = Yii::$app->user->identity->id;
        
        
        if ($model->save()) {

            $response = [
                'status' => 'success',
                'data' => 'Los datos se guardaron correctamente.'
            ];

        } else {        
            // Ocurrió un error al guardar los datos
            $response = [
                'status' => 'error',
                'data' => 'Ocurrió un error al guardar los datos.',
            ];
        }        
         
        // return json_encode(['status' => 'success', 'nuevaFila' => $nuevaFila]);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Devuelve la respuesta en formato JSON

        return $response;
    }

    public function actionEditarpregunta(){

        $form = Yii::$app->request->post();

        $id_pregunta = $form['id_evaluacion_pregunta'];
        $nombre_editado = $form['pregunta_edit'];
        $descripcion_editada = $form['descripcion_edit']; 

        $actualizar_datos = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_preguntas',[
            'nombrepregunta' => $nombre_editado,
            'descripcionpregunta' => $descripcion_editada
        ],'id_gestorevaluacionpreguntas ='.$id_pregunta.'')->execute();
        
        if(!$actualizar_datos){
            $response = [
                'status' => 'error',
                'data' => 'Ocurrió un error al actualizar los datos',
            ];
        }

        $response = [
            'status' => 'success',
            'data' => 'Datos actualizados correctamente',
        ]; 

        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $response;     

    }

    public function actionEliminarpregunta() {

        $id_pregunta = Yii::$app->request->post('id_pregunta');

        $eliminar_logicamente_datos = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_preguntas',[
            'anulado' => 1,
        ],'id_gestorevaluacionpreguntas ='.$id_pregunta.'')->execute();

        if(!$eliminar_logicamente_datos){
            $response = [
                'status' => 'error',
                'data' => 'Ocurrió un error al eliminar los datos',
            ];
        }

        $response = [
            'status' => 'success',
            'data' => 'Datos eliminados correctamente',
        ];  

        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $response;  
        
    }

    // ---- RESPUESTAS -----

    public function actionCargardatostablarespuestas(){ 

        $id_evaluacion = Yii::$app->request->get('id');

        $datos = GestorEvaluacionRespuestas::find()
        ->select(['id_evaluacionnombre', 'id_gestorevaluacionrespuestas', 'nombre_respuesta', 'valornumerico_respuesta', 'descripcion_respuesta'])
        ->where(['id_evaluacionnombre' => $id_evaluacion,
                  'anulado'=>'0'])
        ->asArray()
        ->all();  

        $response = [
            'status' => 'success',
            'data' => $datos,
        ];

        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $response;
    }

    public function actionCreaterespuesta(){        

        // Asignar los valores al modelo
        $id_evaluacionnombre = Yii::$app->request->post('id_evaluacion');
        $nombre_respuesta = Yii::$app->request->post('nom_respuesta');
        $valornumerico_respuesta = Yii::$app->request->post('valor_respuesta');        
        $descripcion_respuesta =  Yii::$app->request->post('descripcion_respuesta');
        $usua_id = Yii::$app->user->identity->id; 
     

        $crear_pregunta = Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_respuestas',[
            'id_evaluacionnombre' => $id_evaluacionnombre,
            'nombre_respuesta' => $nombre_respuesta,
            'valornumerico_respuesta' => $valornumerico_respuesta,
            'descripcion_respuesta' => $descripcion_respuesta,
            'fechacreacion' => date("Y-m-d"),
            'usua_id' => Yii::$app->user->identity->id
        ])->execute();
        
        if ($crear_pregunta) {   

            $response = [
                'status' => 'success',
                'data' => 'Los datos se guardaron correctamente.'
            ];

        } else {  
            // Ocurrió un error al guardar los datos
            $response = [
                'status' => 'error',
                'data' => 'Ocurrió un error al guardar los datos.',
            ];
        }        
         
        // return json_encode(['status' => 'success', 'nuevaFila' => $nuevaFila]);
        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON; // Devuelve la respuesta en formato JSON

        return $response;
    }
    
    public function actionEditrespuesta(){

        $form = Yii::$app->request->post();

        $id_pregunta = $form['id_evaluacion_rta'];
        $nombre_editado = $form['nom_rta_edit'];
        $valor_editado = $form['valor_rta_edit'];
        $descripcion_editada = $form['descripcion_rta_edit']; 

        $actualizar_datos = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_respuestas',[
            'nombre_respuesta' => $nombre_editado,
            'valornumerico_respuesta' => $valor_editado,
            'descripcion_respuesta' => $descripcion_editada
        ],'id_gestorevaluacionrespuestas ='.$id_pregunta.'')->execute();
        
        if(!$actualizar_datos){
            $response = [
                'status' => 'error',
                'data' => 'Ocurrió un error al actualizar los datos',
            ];
        }

        $response = [
            'status' => 'success',
            'data' => 'Datos actualizados correctamente',
        ]; 

        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $response;     

    }

    public function actionDeleterespuesta() {

        $id_rta = Yii::$app->request->post('id_rta');

        $eliminar_logicamente_datos = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_respuestas',[
            'anulado' => 1,
        ],'id_gestorevaluacionrespuestas ='.$id_rta.'')->execute();

        if(!$eliminar_logicamente_datos){
            $response = [
                'status' => 'error',
                'data' => 'Ocurrió un error al eliminar los datos',
            ];
        }

        $response = [
            'status' => 'success',
            'data' => 'Datos eliminados correctamente',
        ];  

        
        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $response;  
        
    }
    

    // ---- CARGA MASIVA -----

    public function actionViewcargamasiva(){

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
                        $this->importExcelUsuarios($name);
                        // Carga masiva exitosa
                        Yii::$app->session->setFlash('success', 'Carga masiva subida exitosamente.');
                        
                        return $this->redirect('parametrizador');
                    }
                }
           }

        return $this->renderAjax('viewcargamasiva',[
            'model' => $model,
        ]);

    }

    public function importExcelUsuarios($name){

        ini_set("max_execution_time", "900");
        ini_set("memory_limit", "1024M");
        ini_set( 'post_max_size', '1024M' );

        ignore_user_abort(true);
        set_time_limit(900);

        $inputFile = 'categorias/' . $name . '.xlsx';
        $pk_jefe = null;
        $cc_jefe = null;
        $pk_colaborador = null;
        $cc_colaborador = null;

        try {
            $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
            $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
            $objPHPExcel = $objReader->load($inputFile);
        } catch (Exception $e) {
            die('Error');
        }

        $sheet = $objPHPExcel->getSheet(0); //Primer Hoja del Excel
        $highestRow = $sheet->getHighestRow(); //Numero de ultima fila
        
        for ($row = 3; $row <= $highestRow; $row++) { 
            $cc_jefe = trim($sheet->getCell("C".$row)->getValue()); 
                
            if ( $cc_jefe != null) {

                $paramsBusqueda = [':varJefeCC' => $cc_jefe];

                $varExisteJefe = Yii::$app->db->createCommand('
                  SELECT COUNT(usuario.id_gestor_evaluacion_usuarios) AS num_registros_jefe, usuario.es_jefe FROM tbl_gestor_evaluacion_usuarios usuario
                    WHERE 
                    usuario.identificacion IN (:varJefeCC)')->bindValues($paramsBusqueda)->queryOne();

                if ($varExisteJefe['num_registros_jefe'] == "0") {                               

                    Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_usuarios',[
                                        'nombre_completo' => trim($sheet->getCell("B".$row)->getValue()),
                                        'identificacion' => $cc_jefe,
                                        'genero' =>  trim($sheet->getCell("F".$row)->getValue()),
                                        'cargo' => trim($sheet->getCell("A".$row)->getValue()),
                                        'area_operacion' => trim($sheet->getCell("D".$row)->getValue()),
                                        'ciudad' => trim($sheet->getCell("E".$row)->getValue()),
                                        'sociedad' => trim($sheet->getCell("G".$row)->getValue()),
                                        'es_jefe' => 1,
                                        'fechacreacion' => date("Y-m-d"),                                        
                                        'usua_id' => Yii::$app->user->identity->id,
                                        'anulado' => 0,
                                        ])->execute();

                    // Obtener el valor de la clave primaria generada
                    $pk_jefe = Yii::$app->db->getLastInsertID();
                }  
                
                if ($varExisteJefe['num_registros_jefe'] == "1") {
                    
                    $pk_jefe = (new \yii\db\Query())
                            ->select('id_gestor_evaluacion_usuarios')
                            ->from('tbl_gestor_evaluacion_usuarios')
                            ->where(['identificacion' => $cc_jefe])
                            ->scalar();

                    if($varExisteJefe['es_jefe']==null) {
                    
                        Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_usuarios',[
                            'es_jefe' => 1,
                        ],'id_gestor_evaluacion_usuarios ='.$pk_jefe.'')->execute();

                    }
                    
                }
                 
            }

            $cc_colaborador = trim($sheet->getCell("J".$row)->getValue());

            if ($cc_colaborador != null) {
                $paramsBusqueda = [':varColaboradorCC' => $cc_colaborador];

                $varExisteColaborador = Yii::$app->db->createCommand('
                  SELECT COUNT(usuario.id_gestor_evaluacion_usuarios) AS num_registros, usuario.es_colaborador FROM tbl_gestor_evaluacion_usuarios usuario
                    WHERE 
                    usuario.identificacion IN (:varColaboradorCC)')->bindValues($paramsBusqueda)->queryOne();

                if ($varExisteColaborador['num_registros'] == "0") {                               

                    Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_usuarios',[
                                        'nombre_completo' => trim($sheet->getCell("I".$row)->getValue()),
                                        'identificacion' => $cc_colaborador,
                                        'genero' =>  trim($sheet->getCell("M".$row)->getValue()),
                                        'cargo' => trim($sheet->getCell("H".$row)->getValue()),
                                        'area_operacion' => trim($sheet->getCell("K".$row)->getValue()),
                                        'ciudad' => trim($sheet->getCell("L".$row)->getValue()),
                                        'sociedad' => trim($sheet->getCell("N".$row)->getValue()),
                                        'es_colaborador' => 1,
                                        'fechacreacion' => date("Y-m-d"),                                        
                                        'usua_id' => Yii::$app->user->identity->id,
                                        'anulado' => 0,
                                        ])->execute();

                    // Obtener el valor de la clave primaria generada
                    $pk_colaborador = Yii::$app->db->getLastInsertID();
                }       
                
                if ($varExisteColaborador['num_registros'] == "1") {

                    $pk_colaborador = (new \yii\db\Query())
                            ->select('id_gestor_evaluacion_usuarios')
                            ->from('tbl_gestor_evaluacion_usuarios')
                            ->where(['identificacion' => $cc_colaborador])
                            ->scalar();
                    
                    if($varExisteColaborador['es_colaborador']==null) {
            
                        Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_usuarios',[
                            'es_colaborador' => 1,
                        ],'id_gestor_evaluacion_usuarios ='.$pk_colaborador.'')->execute();

                    }

                    
                }
                 
            }          
            
            if($pk_jefe!=null && $pk_colaborador!=null){    
                            
                try {
                    
                   $insert_data = Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_jefe_colaborador',[
                        'id_usuario_jefe' => $pk_jefe,
                        'id_usuario_colaborador' => $pk_colaborador,                    
                        'fechacreacion' => date("Y-m-d"),                                        
                        'usua_id' => Yii::$app->user->identity->id
                        ])->execute();

                    
                } catch (\yii\db\IntegrityException $e) {
                    continue;
                } 
            }

        }

    }

    //----- EVALUACIÓN AUTOEVALUACION ------------
    public function actionAutoevaluacion($id_user, $id_evalua){       
       
        $id_usuario_logueado = $id_user;
        $id_evaluacion_actual = $id_evalua;
        $model_datos_form = new GestorEvaluacionDatosForm();
        $model_respuestas_form = new GestorEvaluacionRespuestasForm();
        $vartipoeva = Yii::$app->db->createCommand("select idevaluaciontipo as id, tipoevaluacion from tbl_evaluacion_tipoeval where idevaluaciontipo = 1  and anulado = 0")->queryOne();
       
        // Datos usuario de carga masiva
        $datos_usuario_logueado = Yii::$app->db->createCommand("select nombre_completo, identificacion, cargo, area_operacion, ciudad, sociedad FROM tbl_gestor_evaluacion_usuarios WHERE id_gestor_evaluacion_usuarios in ('$id_usuario_logueado')")->queryOne();
        
        // Query para obtener el jefe asociado
        $paramsBusqueda = [':id_usuario' => $id_usuario_logueado];
        $datos_jefe = Yii::$app->db->createCommand('
        SELECT jefe.id_gestor_evaluacion_usuarios AS id_jefe, jefe.nombre_completo AS nom_jefe, jefe.identificacion AS identificacion_jefe
        FROM tbl_gestor_evaluacion_jefe_colaborador jefe_x_colaborador
        INNER JOIN tbl_gestor_evaluacion_usuarios jefe
        ON jefe_x_colaborador.id_usuario_jefe = jefe.id_gestor_evaluacion_usuarios
        WHERE jefe_x_colaborador.anulado=0 AND jefe_x_colaborador.id_usuario_colaborador IN (:id_usuario)')->bindValues($paramsBusqueda)->queryAll();
      
        // Query para traer las preguntas (competencias) asociadas a un id_evaluacion
        $paramsEvaluacion = [':id_evalua' => $id_evalua];
        $array_preguntas = Yii::$app->db->createCommand('
        SELECT id_gestorevaluacionpreguntas AS id_pregunta, id_evaluacionnombre, nombrepregunta, descripcionpregunta 
        FROM tbl_gestor_evaluacion_preguntas
        WHERE anulado = 0 AND id_evaluacionnombre IN (:id_evalua)')->bindValues($paramsEvaluacion)->queryAll(); 
                
        // Query para obtener las respuestas asociadas a un id_evaluacion        
        $array_respuestas = Yii::$app->db->createCommand('
        SELECT id_gestorevaluacionrespuestas AS id_rta, nombre_respuesta, 
        descripcion_respuesta, valornumerico_respuesta AS valor 
        FROM tbl_gestor_evaluacion_respuestas
        WHERE anulado = 0 AND id_evaluacionnombre IN (:id_evalua)')->bindValues($paramsEvaluacion)->queryAll(); 
                
        // Nombre de la respuesta con su valor numerio    
        $opcion_respuestas = ArrayHelper::map($array_respuestas, 'id_rta', function ($item) {
            return $item['nombre_respuesta'] . ' - ' . $item['valor'];
        });     

        //Lista de tiempo de desarrollo
        $option_tiempo_en_el_cargo = [
            '1' => 'Inferior a 6 meses',
            '2' => '6 meses a 1 año',
            '3' => '2 años a 3 años',
            '4' => '3 años en adelante',
        ];
      
        return $this->render('autoevaluacion',[
            'model_rta_form' => $model_respuestas_form,
            'model_datos_form' => $model_datos_form,
            'datos_usuario' => $datos_usuario_logueado,
            'datos_jefe' => $datos_jefe,
            'array_preguntas' => $array_preguntas,
            'array_respuestas'=> $array_respuestas,
            'opcion_respuestas'=> $opcion_respuestas,
            'lista_tiempo_en_cargo' => $option_tiempo_en_el_cargo,
            'id_usuario_logueado' => $id_usuario_logueado,
            'id_evaluacion_actual' => $id_evaluacion_actual,
            'vartipoeva' => $vartipoeva
            
        ]);
    }

    public function actionCrearautoevaluacion() {

        //Obtener parametros
        $formData = Yii::$app->request->post();
        $id_user = $formData['id_user'];
        $id_evaluac_nombre = $formData['id_evalua_nombre'];
        $array_preguntas_rtas = $formData['array_preguntas_rtas'];       
       
        // Se crea un formulario de autoevaluacion en BD
        $model_evalua_desarrollo = new GestorEvaluacionFormulario();
        $model_evalua_desarrollo->id_evaluacionnombre = $id_evaluac_nombre;
        $model_evalua_desarrollo->id_tipo_evalua = $formData['id_tipo_evalua'];  
        $model_evalua_desarrollo->id_evaluador = $id_user;
        $model_evalua_desarrollo->id_evaluado = $id_user;
        $model_evalua_desarrollo->id_estado_evaluacion = 1; //id de completada
        $model_evalua_desarrollo->fechacreacion = date("Y-m-d");
        $model_evalua_desarrollo->usua_id = Yii::$app->user->identity->id;

        if( $model_evalua_desarrollo->validate() && $model_evalua_desarrollo->save() ){
            $pk_evaluacion_desarrollo = $model_evalua_desarrollo->id_gestor_evaluacion_formulario;            
        } else {                
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => 'error', 'data' => 'No se pudo crear el formulario'];
        };        

        //Se asigna ese id del formulario a la data ingresada por el usuario
        if ($pk_evaluacion_desarrollo !==null) {

            // datos del usuario (para historico) 
            $model_datos_form = new GestorEvaluacionDatosForm();                           
            $model_datos_form->id_gestor_evaluacion_formulario = $pk_evaluacion_desarrollo;
            $model_datos_form->tiempo_cargo = $formData['tiempo_cargo'];
            $model_datos_form->cargo = $formData['cargo'];
            $model_datos_form->nom_jefe = $formData['nom_jefe'];
            $model_datos_form->area_operacion = $formData['area_operacion'];
            $model_datos_form->ciudad = $formData['ciudad'];
            $model_datos_form->sociedad = $formData['sociedad'];
            $model_datos_form->fechacreacion = date("Y-m-d");
            $model_datos_form->usua_id = Yii::$app->user->identity->id;
            $model_datos_form->save();

            // respuestas de cada competencia 
            if ($array_preguntas_rtas !== null) {

                foreach ($array_preguntas_rtas as $datos) {
                    $id_pregunta = $datos['id_pregunta'];
                    $id_respuesta = $datos['id_respuesta'];
                    $observaciones = $datos['observaciones'];
                    $acuerdos = $datos['acuerdos'];

                    $model_respuestas_form = new GestorEvaluacionRespuestasForm();    
                    $model_respuestas_form->id_gestor_evaluacion_formulario = $pk_evaluacion_desarrollo;
                    $model_respuestas_form->id_pregunta = $id_pregunta;
                    $model_respuestas_form->id_respuesta = $id_respuesta;
                    $model_respuestas_form->observacion = $observaciones;
                    $model_respuestas_form->acuerdos = $acuerdos;
                    $model_respuestas_form->fechacreacion = date("Y-m-d");
                    $model_respuestas_form->usua_id = Yii::$app->user->identity->id;
                    $model_respuestas_form->save();
                }

                //Calificacion total: Suma de todas las respuestas asociadas a cada pregunta de la Autoevaluacion
                $array_id_respuestas = array_column($array_preguntas_rtas, "id_respuesta");               
                $crear_calificacion_total = $this->crearCalificacionPorEvaluacion($array_id_respuestas, $pk_evaluacion_desarrollo);
                
                if($crear_calificacion_total!==1){
                    // Respuesta error 
                    Yii::$app->response->format = Response::FORMAT_JSON; 
                    return ['status' => 'error', 'data' => 'Error creando la calificacion total de esta evaluacion']; 
                }

            } else {
                // Respuesta error 
                Yii::$app->response->format = Response::FORMAT_JSON;
                return ['status' => 'error', 'data' => 'No hay hay respuestas asociadas a las preguntas para el id_formulario ' . $pk_evaluacion_desarrollo ];
            }

        } else {
            // Respuesta error 
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => 'error', 'data' => 'No exite el id del formulario, error asociando los datos'];          
        }

        $verificar_completadas_evaluaciones = $this->verificarEstadoEvaluaciones($id_user, $id_evaluac_nombre);
        
        //Si ya completo todas las evaluaciones asociadas, calcular su promedio final 
        if($verificar_completadas_evaluaciones) {           

            $pk_calificacion_total = $this->crearCalificacionTotal($id_user, $id_evaluac_nombre);
        
            if($pk_calificacion_total !== ""){
                $crear_calificac_por_competencia = $this->calcularCalificacionTotalPorCompetencia($id_user, $id_evaluac_nombre, $pk_calificacion_total);
                
                if($crear_calificac_por_competencia['status']=='success'){
                    //Respuesta exitosa
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['status' => 'success', 'data' => 'Se registro exitosamente la autoevaluación'];
                }
                else {
                    // Respuesta error 
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['status' => 'error', 'data' => 'Error en crear calificacion total por competencia'];          
                }
            } 

        }    
        
        //Si aun le faltan la evaluacion del jefe, continuar flujo normal
        if(!$verificar_completadas_evaluaciones){
            //Respuesta exitosa
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => 'success', 'data' => 'Se registro exitosamente la autoevaluación'];
        }

    }    

    //----EVALUACION A CARGO ------------------------------------------------------
    public function actionModalevaluacionacargo($id_jefe,$id_evalua) {
       
        $model = new GestorEvaluacionPreguntas();
     
        $array_personas_a_cargo = $this->obtenerPersonasAcargoSinEvaluar($id_jefe);
        
        $opcion_personas_a_cargo = ArrayHelper::map($array_personas_a_cargo,
        'id_colaborador',
        'nombre_completo');   

  
        $form = Yii::$app->request->post();
        if ($model->load($form)) {
        
            $id_seleccionado = $model->id_evaluacionnombre;
            $id_evaluacion_actual = $id_evalua;

          return $this->redirect(['evaluacionacargo', 'id_user' => $id_jefe, 'id_selected'=> $id_seleccionado, 'id_evalua'=> $id_evaluacion_actual]);
        }
  
        return $this->renderAjax('modalevaluacionacargo',[
          'model' => $model,
          'opcion_personas_a_cargo' => $opcion_personas_a_cargo
          ]);
    }

    public function actionEvaluacionacargo($id_user, $id_selected, $id_evalua){      
        
        $model_datos_form = new GestorEvaluacionDatosForm();
        $model_respuestas_form = new GestorEvaluacionRespuestasForm();
        $vartipoeva = Yii::$app->db->createCommand("select idevaluaciontipo as id, tipoevaluacion from tbl_evaluacion_tipoeval where idevaluaciontipo = 3  and anulado = 0")->queryOne();
        
        // Datos usuario de carga masiva
        $datos_colaborador_seleccionado = Yii::$app->db->createCommand("select id_gestor_evaluacion_usuarios as id_user, nombre_completo, identificacion, cargo, area_operacion, ciudad, sociedad FROM tbl_gestor_evaluacion_usuarios WHERE id_gestor_evaluacion_usuarios in ('$id_selected')")->queryOne();
         
        // Query para obtener el jefe asociado
        $paramsBusqueda = [':id_usuario' => $id_selected];
        $datos_jefe = Yii::$app->db->createCommand('
        SELECT jefe.id_gestor_evaluacion_usuarios AS id_jefe, jefe.nombre_completo AS nom_jefe, jefe.identificacion AS identificacion_jefe
        FROM tbl_gestor_evaluacion_jefe_colaborador jefe_x_colaborador
        INNER JOIN tbl_gestor_evaluacion_usuarios jefe
        ON jefe_x_colaborador.id_usuario_jefe = jefe.id_gestor_evaluacion_usuarios
        WHERE jefe_x_colaborador.anulado=0 AND jefe_x_colaborador.id_usuario_colaborador IN (:id_usuario)')->bindValues($paramsBusqueda)->queryAll();

    
        // Query para traer las preguntas (competencias) asociadas a un id_evaluacion
        $paramsEvaluacion = [':id_evalua' => $id_evalua];
        $array_preguntas = Yii::$app->db->createCommand('
        SELECT id_gestorevaluacionpreguntas AS id_pregunta, id_evaluacionnombre, nombrepregunta, descripcionpregunta 
        FROM tbl_gestor_evaluacion_preguntas
        WHERE anulado = 0 AND id_evaluacionnombre IN (:id_evalua)')->bindValues($paramsEvaluacion)->queryAll(); 
                
        // Query para obtener las respuestas asociadas a un id_evaluacion        
        $array_respuestas = Yii::$app->db->createCommand('
        SELECT id_gestorevaluacionrespuestas AS id_rta, nombre_respuesta, 
        descripcion_respuesta, valornumerico_respuesta AS valor 
        FROM tbl_gestor_evaluacion_respuestas
        WHERE anulado = 0 AND id_evaluacionnombre IN (:id_evalua)')->bindValues($paramsEvaluacion)->queryAll(); 
                
        // Nombre de la respuesta con su valor numerio    
        $opcion_respuestas = ArrayHelper::map($array_respuestas, 'id_rta', function ($item) {
            return $item['nombre_respuesta'] . ' - ' . $item['valor'];
        });     

        //Lista de tiempo de desarrollo
        $option_tiempo_en_el_cargo = [
            '1' => 'Inferior a 6 meses',
            '2' => '6 meses a 1 año',
            '3' => '2 años a 3 años',
            '4' => '3 años en adelante',
        ];


        return $this->render('evaluacionacargo',[
            'model_datos_form' => $model_datos_form,
            'model_rta_form' => $model_respuestas_form, 
            'vartipoeva' => $vartipoeva,
            'datos_colaborador' => $datos_colaborador_seleccionado,
            'datos_jefe' => $datos_jefe,
            'array_preguntas' => $array_preguntas,
            'array_respuestas'=> $array_respuestas,
            'opcion_respuestas'=> $opcion_respuestas,
            'lista_tiempo_en_cargo' => $option_tiempo_en_el_cargo,
            'id_user' => $id_user,
            'id_colab' => $id_selected,
            'id_evaluac' => $id_evalua

        ]);
    }

    public function actionCrearevaluacionacargo(){
            
        // Obtener parametros
        $formData = Yii::$app->request->post();   
        $id_evaluado = $formData['id_evaluado'];
        $id_evaluacion_nombre = $formData['id_evalua_nombre'];
        $array_preguntas_rtas =  $formData['array_preguntas_rtas'];   

        // Se crea un formulario para la "evaluacion a cargo" en BD
        $model_evalua_desarrollo = new GestorEvaluacionFormulario();
        $model_evalua_desarrollo->id_evaluacionnombre = $id_evaluacion_nombre; 
        $model_evalua_desarrollo->id_tipo_evalua = $formData['id_tipo_evalua']; 
        $model_evalua_desarrollo->id_evaluador =  $formData['id_evaluador'];
        $model_evalua_desarrollo->id_evaluado = $id_evaluado; 
        $model_evalua_desarrollo->id_estado_evaluacion = 1; //id de completada
        $model_evalua_desarrollo->fechacreacion = date("Y-m-d");
        $model_evalua_desarrollo->usua_id = Yii::$app->user->identity->id;
        
        if($model_evalua_desarrollo->validate() && $model_evalua_desarrollo->save() ){
            $pk_evaluacion_desarrollo = $model_evalua_desarrollo->id_gestor_evaluacion_formulario; 
        } else {
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => 'error', 'data' => 'No se pudo crear el formulario'];
        }            

        //Se asigna ese id del formulario a la data ingresada por el usuario
        if ($pk_evaluacion_desarrollo !==null) {

            // datos del usuario (para historico)  
            $model_datos_form = new GestorEvaluacionDatosForm();                 
            $model_datos_form->id_gestor_evaluacion_formulario = $pk_evaluacion_desarrollo;
            $model_datos_form->tiempo_cargo = $formData['tiempo_cargo'];
            $model_datos_form->cargo = $formData['cargo'];
            $model_datos_form->nom_jefe = $formData['nom_jefe'];
            $model_datos_form->area_operacion = $formData['area_operacion'];
            $model_datos_form->ciudad = $formData['ciudad'];
            $model_datos_form->sociedad = $formData['sociedad'];
            $model_datos_form->fechacreacion = date("Y-m-d");
            $model_datos_form->usua_id = Yii::$app->user->identity->id;
            $model_datos_form->save();

            // respuestas de cada competencia 
            if ($array_preguntas_rtas !== null) {

                foreach ($array_preguntas_rtas as $datos) {
                    $id_pregunta = $datos['id_pregunta'];
                    $id_respuesta = $datos['id_respuesta'];
                    $observaciones = $datos['observaciones'];
                    $acuerdos = $datos['acuerdos'];

                    $model_respuestas_form = new GestorEvaluacionRespuestasForm();    
                    $model_respuestas_form->id_gestor_evaluacion_formulario = $pk_evaluacion_desarrollo;
                    $model_respuestas_form->id_pregunta = $id_pregunta;
                    $model_respuestas_form->id_respuesta = $id_respuesta;
                    $model_respuestas_form->observacion = $observaciones;
                    $model_respuestas_form->acuerdos = $acuerdos;
                    $model_respuestas_form->fechacreacion = date("Y-m-d");
                    $model_respuestas_form->usua_id = Yii::$app->user->identity->id;
                    $model_respuestas_form->save();
                }
                
                //Calificacion total: Suma de todas las respuestas asociadas a cada pregunta de la evaluacion
                $array_id_respuestas = array_column($array_preguntas_rtas, "id_respuesta");
                $crear_calificacion_total = $this->crearCalificacionPorEvaluacion($array_id_respuestas, $pk_evaluacion_desarrollo);

                if($crear_calificacion_total!==1){
                    // Respuesta error 
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['status' => 'error', 'data' => 'Error creando la calificacion total de esta evaluacion']; 
                }
            
            } else {
               // Respuesta error 
               Yii::$app->response->format = Response::FORMAT_JSON;
               return ['status' => 'error', 'data' => 'No hay hay respuestas asociadas a las preguntas para el id_formulario ' . $pk_evaluacion_desarrollo ];
            } 
        } else {

            // Respuesta error 
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => 'error', 'data' => 'No exite el id del formulario, error asociando los datos'];          
            
        }  

        $verificar_completadas_evaluaciones = $this->verificarEstadoEvaluaciones($id_evaluado, $id_evaluacion_nombre);
        
        //Si ya completo todas las evaluaciones asociadas, calcular su promedio final 
        if($verificar_completadas_evaluaciones) {           

            $pk_calificacion_total = $this->crearCalificacionTotal($id_evaluado, $id_evaluacion_nombre);
        
            if($pk_calificacion_total !== ""){
                $crear_calificac_por_competencia = $this->calcularCalificacionTotalPorCompetencia($id_evaluado, $id_evaluacion_nombre, $pk_calificacion_total);
                
                if($crear_calificac_por_competencia['status']=='success'){
                    //Respuesta exitosa
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['status' => 'success', 'data' => 'Se registro exitosamente la evaluación'];
                }
                else {
                    // Respuesta error 
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return ['status' => 'error', 'data' => 'Error en crear calificacion total por competencia'];          
                }
            } 

        }    
        
        //Si aun le faltan la evaluacion del jefe, continuar flujo normal
        if(!$verificar_completadas_evaluaciones){
            //Respuesta exitosa
            Yii::$app->response->format = Response::FORMAT_JSON;
            return ['status' => 'success', 'data' => 'Se registro exitosamente la evaluación'];
        }

    }

    // REPORTERIA  -------------------------------------------------------------------->

    public function actionResultadoindividual() {

        //Obtener id y documento del usuario logueado
        $sessiones = Yii::$app->user->identity->id;
        $documento = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
        
        //Validar usuario segun datos de la carga masiva
        $id_user = null;
        $mostrar_feedbacks='none';
        $existe_usuario = Yii::$app->db->createCommand("select count(u.identificacion) AS cant_registros, u.id_gestor_evaluacion_usuarios, u.es_jefe, u.es_colaborador from tbl_gestor_evaluacion_usuarios u where identificacion in ('$documento')")->queryOne();
        $registros_encontrados = $existe_usuario['cant_registros'];
        $deshabilitar_crear_feedback=false;
          
        //variables locales
        $id_evalua_nombre = "------";
        $evalua_nombre = "------";
        $existe_calificacion_total=false;
        $promTotalEvaluacion = 0;
        $sumaTotalEvaluacion = 0;
        $data_competencias = [];
        $feedbacks_usuario = [];
        $nombre_completo = "------";
        $numero_documento = "------";
        $cargo_dataform = "------";
        $nombre_jefe_dataform = "------";
        $fecha_autoevaluacion = "------";
        $fecha_evaluacion_jefe = "------";
        $id_jefe_form = null;
         
        $feedback_colaborador="-----------";
        $feedback_jefe="-----------";
        $feedback_acuerdo_final="-----------";


        //Si existe el usuario
        if($registros_encontrados==1){

            $esjefe = $existe_usuario['es_jefe']; //1 si cumple, null si no cumple
            $esColaborador = $existe_usuario['es_colaborador']; //1 si cumple, null si no cumple
            $id_user = $existe_usuario['id_gestor_evaluacion_usuarios'];

            $evaluacion_actual = $this->obtenerEvaluacionActual();
            if($evaluacion_actual) {
                $id_evalua_nombre = $evaluacion_actual['id_evalua'];
                $evalua_nombre = $evaluacion_actual['nombreeval'];
            } 
            
            $existe_calificacion_total = $this->existe_registros_calificacion_total_por_evaluacion($id_user, $id_evalua_nombre); 
           
            //Si existen datos del calculo consolidado
            if($existe_calificacion_total){
                
                //obtener data del formulario con sus respectivas fechas
                $tipo_evaluaciones_del_usuario = $this->obtener_tipo_evaluacion_por_periodo($id_evalua_nombre);
                $ids_tipo_evalua_del_usuario = array_column($tipo_evaluaciones_del_usuario, 'id_tipoeval');
                
                $data_form = $this->obtenerDataTipoEvaluacion($ids_tipo_evalua_del_usuario, $id_user);
               
                if(!empty($data_form) && count($data_form)>1 ){

                    foreach ($data_form as $row) {
                        $nombreTipoeval = $row['nombre_tipoeval'];
                        $fechaCreacion = $row['fechacreacion'];
                    
                        if ($nombreTipoeval === 'A cargo') {
                            $fecha_evaluacion_jefe = $fechaCreacion;
                            $id_jefe_form = $row['id_jefe'];
                        } elseif ($nombreTipoeval === 'Autoevaluacion') {
                            $fecha_autoevaluacion = $fechaCreacion;
                            $nombre_completo = $row['nombre_completo'];
                            $numero_documento = $row['identificacion'];
                            $cargo_dataform = $row['cargo'];
                            $nombre_jefe_dataform = $row['nom_jefe'];
                        }
                    }
                    
                }               

                //Solo tiene la autoevaluacion
                if(!empty($data_form && count($data_form)==1 )){
                    $nombre_completo = $data_form[0]['nombre_completo'];
                    $numero_documento = $data_form[0]['identificacion'];
                    $cargo_dataform = $data_form[0]['cargo'];
                    $nombre_jefe_dataform = $data_form[0]['nom_jefe'];
                    $fecha_autoevaluacion = $data_form[0]['fechacreacion'];
                    $id_jefe_form = $data_form[0]['id_jefe'];
                }

                //obtener valor consolidado por competencia
                $get_calificacion_x_competencia = $this->getValorPorCompetenciaUnUsuario($id_user, $id_evalua_nombre);         
                if(!empty($get_calificacion_x_competencia) && count($get_calificacion_x_competencia)>0) {
                    
                    $promTotalEvaluacion =  isset($get_calificacion_x_competencia[0]['prom_total_evaluacion']) ? $get_calificacion_x_competencia[0]['prom_total_evaluacion'] : 0;
                    $sumaTotalEvaluacion = isset($get_calificacion_x_competencia[0]['suma_total_evaluacion']) ? $get_calificacion_x_competencia[0]['suma_total_evaluacion'] : 0;

                    $data_competencias = array_map(function ($result) {
                        unset($result['prom_total_evaluacion']);
                        unset($result['suma_total_evaluacion']);
                        return $result;
                    }, $get_calificacion_x_competencia);
                }
            }

            //Consultar Feedbacks del Colaborador
            $feedbacks_usuario = $this->obtener_feedbacks_por_usuario($id_evalua_nombre, $id_user);           
            
            if(!empty($feedbacks_usuario)){
                $mostrar_feedbacks='inline';
                foreach ($feedbacks_usuario as $feedback) {                   
                    if ($feedback['id_remitente'] == $id_user) {
                        $feedback_colaborador = $feedback['comentario'];
                       
                    }
                    if ($feedback['id_remitente'] == $id_jefe_form) {
                        $feedback_jefe = $feedback['comentario'];

                        if ($feedback['acuerdo_final'] !== null) {
                            $feedback_acuerdo_final = $feedback['acuerdo_final'];
                        }
                    }
                    //deshabilitar boton si envio un msje a su jefe
                    if ($feedback['id_destinatario'] == $id_jefe_form) {
                        $deshabilitar_crear_feedback = true;
                    }                    
                }
            }
        }

        return $this->render('resultadoindividual',[
            'existe_usuario'=> $existe_usuario,
            'id_user'=>$id_user,
            'registros_encontrados'=> $registros_encontrados,
            'feedbacks_usuario'=>$feedbacks_usuario,
            'existe_calificacion_total' => $existe_calificacion_total,
            'promTotalEvaluacion'=> $promTotalEvaluacion,
            'sumaTotalEvaluacion'=> $sumaTotalEvaluacion,
            'data_competencias'=> $data_competencias,
            'nombre_completo' => $nombre_completo,
            'numero_documento'=> $numero_documento,
            'cargo_dataform' => $cargo_dataform,
            'nombre_jefe_dataform' => $nombre_jefe_dataform,
            'fecha_autoevaluacion' => $fecha_autoevaluacion,
            'fecha_evaluacion_jefe' => $fecha_evaluacion_jefe,
            'mostrar_feedbacks'=> $mostrar_feedbacks,
            'feedback_colaborador'=>$feedback_colaborador,
            'feedback_jefe'=>$feedback_jefe,
            'feedback_acuerdo_final'=>$feedback_acuerdo_final,
            'deshabilitar_crear_feedback'=>$deshabilitar_crear_feedback
            ]);
                
    }
    
    public function actionResultados() {

        
        $model_feedback_entrada = new GestorEvaluacionFeedbackentradas();

        //Obtener id y documento del usuario logueado
        $sessiones = Yii::$app->user->identity->id;
        $documento = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
        
        //Validar usuario segun datos de la carga masiva
        $existe_usuario = Yii::$app->db->createCommand("select count(u.identificacion) AS cant_registros, u.id_gestor_evaluacion_usuarios, u.es_jefe, u.es_colaborador from tbl_gestor_evaluacion_usuarios u where identificacion in ('$documento')")->queryOne();
        $registros_encontrados = $existe_usuario['cant_registros'];
        
        //variables locales
        $id_evalua_nombre = "";
        $evalua_nombre = "";
        $promTotalEvaluacion = 0;
        $sumaTotalEvaluacion = 0;
        $data_competencias = [];
        $personas_a_cargo = [];
        $numero_personas_a_cargo = "----";
        $data_evaluaciones_completadas = [];
        $data_calificacion_total = [];
        $id_user= null;

 
         //Si existe el usuario
        if($registros_encontrados==1){
 
            $esjefe = $existe_usuario['es_jefe']; //1 si cumple, null si no cumple
            $esColaborador = $existe_usuario['es_colaborador']; //1 si cumple, null si no cumple
            $id_user = $existe_usuario['id_gestor_evaluacion_usuarios'];

            $evaluacion_actual = $this->obtenerEvaluacionActual();
            if($evaluacion_actual){
                $id_evalua_nombre = $evaluacion_actual['id_evalua'];
                $evalua_nombre = $evaluacion_actual['nombreeval'];
            } 
            
            $personas_a_cargo = $this->obtenerTodasLasPersonasAcargo($id_user);
            
            if(!empty($personas_a_cargo)){               

                $numero_personas_a_cargo = count($personas_a_cargo);
                $idColaboradores = array_map('intval', array_column($personas_a_cargo, 'id_colaborador'));

                $data_calificacion_total = $this->obtenerCalificacionTotalporUsuarios($id_evalua_nombre, $idColaboradores);
               
                $data_competencias = $this->getValorPorCompetenciaVariosUsuarios($idColaboradores, $id_evalua_nombre);
              
            } 
        }        

        return $this->render('resultados',[   
            'id_user'=> $id_user,         
            'existe_usuario'=> $existe_usuario,
            'registros_encontrados'=> $registros_encontrados, 
            'personas_a_cargo'=> $personas_a_cargo,
            'data_calificacion_total'=> $data_calificacion_total,
            'data_competencias'=>json_encode($data_competencias),
            'model_feedback_entrada'=> $model_feedback_entrada,
            'numero_personas_a_cargo'=>$numero_personas_a_cargo      
        ]);
                
    }

    public function actionCrearfeedback() {   
        $response= [];
        $model_feedback = new GestorEvaluacionFeedback();        

        $form = Yii::$app->request->post();
        $id_jefe = $form['id_jefe']; 
        $id_colaborador = $form['id_colaborador'];
        $comentarios = $form['comentarios'];

        $obtener_evaluacion_actual = $this->obtenerEvaluacionActual();
        $id_evaluacion_actual = $obtener_evaluacion_actual['id_evalua'];    
                
        $existe_feedback_colaborador = $this->obtener_feedback_usuario($id_evaluacion_actual, $id_colaborador);
       
        //existe un id feedback asociado al colaborador
        if(!empty($existe_feedback_colaborador)){
            $pk_feedback = $existe_feedback_colaborador['id_feedback'];
            $pk_calificacion_total = $existe_feedback_colaborador['id_calificacion_total'];
        }

        if($pk_feedback==null){
            
            //crear un registro para el feedback (acuerdo final)
            $model_feedback->id_calificaciontotal = $pk_calificacion_total;
            $model_feedback->id_jefe = $id_jefe;
            $model_feedback->fechacreacion = date("Y-m-d");
            $model_feedback->usua_id = Yii::$app->user->identity->id;
        
            if($model_feedback->validate() && $model_feedback->save() ){
                $pk_feedback = $model_feedback->id_gestor_evaluacion_feedback; 
            } else {                
                $response = [
                    'status' => 'error',
                    'message' => 'Ocurrió un error creando un registro para el feedback'
                ];
            }

            //Actualizo con ese pk el campo asociado al feedback en la evaluacion total
            $model_calificacion_total = GestorEvaluacionCalificacionTotal::findOne($pk_calificacion_total);
            if ($model_calificacion_total !== null) {               
                $model_calificacion_total->id_feedback = $pk_feedback;
                $model_calificacion_total->save();                
            } 
        }

        //Creo el feedback del usuario (entrada)                   
        $model_feedback_entrada = new GestorEvaluacionFeedbackentradas();
        $model_feedback_entrada->id_feedback = $pk_feedback;
        $model_feedback_entrada->id_remitente = $id_jefe;
        $model_feedback_entrada->id_destinatario = $id_colaborador;
        $model_feedback_entrada->comentario = $comentarios;                    
        $model_feedback_entrada->fechacreacion = date("Y-m-d");
        $model_feedback_entrada->usua_id = Yii::$app->user->identity->id;
    
        if($model_feedback_entrada->validate() && $model_feedback_entrada->save() ){

            $personas_a_cargo = $this->obtenerTodasLasPersonasAcargo($id_jefe);
            $idColaboradores = array_map('intval', array_column($personas_a_cargo, 'id_colaborador'));
            $data_actualizada = $this->obtenerCalificacionTotalporUsuarios($id_evaluacion_actual, $idColaboradores);  
                    
            
            //Respuesta exitosa
            $response = [
                'status' => 'success',
                'message' => 'Creación exitosa',
                'data'=> $data_actualizada,
            ];

        } else {
            $response = [
                'status' => 'error',
                'message' => 'Error creando comentario para el feedback'
            ];
        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        
        return $response;
      
    }

    public function actionModalfeedbackcolaborador($id_user){

        $model_feedback = new GestorEvaluacionFeedback();
        $model_entrada_feedback = new GestorEvaluacionFeedbackentradas();

        $response= [];
        $id_colaborador = $id_user;
        $obtener_evaluacion_actual = $this->obtenerEvaluacionActual();
        $id_evaluacion_actual = $obtener_evaluacion_actual['id_evalua'];        
        
        //Obtener tipo evaluacion "A cargo" para saber el id del jefe para esta evaluacion
        $id_evaluacion_a_cargo = (new Query())
        ->select('tipo.idevaluaciontipo')
        ->from('tbl_evaluacion_tipoeval tipo')
        ->where(['tipo.tipoevaluacion' => 'A cargo'
        ])->scalar();
        
        //obtner Id del jefe asocaido para esa evaluacion
        $id_jefe = (new Query())
        ->select('form.id_evaluador')
        ->from('tbl_gestor_evaluacion_formulario form')
        ->innerJoin('tbl_gestor_evaluacion_datosform datosform', 'form.id_gestor_evaluacion_formulario = datosform.id_gestor_evaluacion_formulario')
        ->where([
            'form.id_evaluacionnombre' => $id_evaluacion_actual,
            'form.id_tipo_evalua' => $id_evaluacion_a_cargo,
            'form.id_evaluado' => $id_colaborador,
        ])->scalar();

        
        $existe_feedback_colaborador = $this->obtener_feedback_usuario($id_evaluacion_actual, $id_colaborador);

         // Si existe un id feedback asociado al colaborador
        if(!empty($existe_feedback_colaborador)){
            $pk_feedback = $existe_feedback_colaborador['id_feedback'];
            $pk_calificacion_total = $existe_feedback_colaborador['id_calificacion_total'];
        }

        if($pk_feedback==null) {
            //creo el PK del feedback que se usara para el acuerdo final
            $model_feedback->id_calificaciontotal = $pk_calificacion_total;
            $model_feedback->id_jefe = $id_jefe;
            $model_feedback->fechacreacion = date("Y-m-d");
            $model_feedback->usua_id = Yii::$app->user->identity->id;
        
            if($model_feedback->validate() && $model_feedback->save() ){
                $pk_feedback = $model_feedback->id_gestor_evaluacion_feedback; 
            } else {                
                $response = [
                    'status' => 'error',
                    'data' => 'Ocurrió un error creando un registro para el feedback'
                ];
            }

            //Actualizo con ese pk_feedback el campo asociado al feedback en la evaluacion total
            $model_calificacion_total = GestorEvaluacionCalificacionTotal::findOne($pk_calificacion_total);
            if ($model_calificacion_total !== null) {
                $model_calificacion_total->id_feedback = $pk_feedback;
                $model_calificacion_total->save();
            }
        }

        //Creo la entrada del feedback
        if ( $model_entrada_feedback->load(Yii::$app->request->post()) ) {
            
            //obtener valor ingresado en el campo comentarios del modal
            $comentarios_colaborador = $model_entrada_feedback->comentario;

            $model_entrada_feedback->id_feedback = $pk_feedback;
            $model_entrada_feedback->id_remitente = $id_colaborador;
            $model_entrada_feedback->id_destinatario = $id_jefe;
            $model_entrada_feedback->fechacreacion = date("Y-m-d");
            $model_entrada_feedback->usua_id = Yii::$app->user->identity->id;       
            
            if( $model_entrada_feedback->validate() && $model_entrada_feedback->save() ){
                //Respuesta exitosa
                $response = [
                    'status' => 'success',
                    'data' => 'Creación exitosa'
                ]; 

                Yii::$app->session->setFlash('success', 'Creación exitosa del feedback.');

                return $this->redirect('resultadoindividual', [
                    'pk_feedback_acuerdo'=>$pk_feedback
                ]);

            } else { 
                Yii::$app->session->setFlash('error', 'Error creando feedback.');
            } 
            
        }

        return $this->renderAjax('modalfeedbackcolaborador',[
            'model_entrada_feedback'=> $model_entrada_feedback
        ]);        

    }

    public function actionFeedbackfinal($id_jefe){
        $model = new GestorEvaluacionFeedback();

        //Parametros recibidos por el click del boton 
        $id_jefe= Yii::$app->request->get('id_jefe');
        
        //Periodo actual que realizan la evaluacion
        $evaluacion_actual = $this->obtenerEvaluacionActual();        
        $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
      
        //Traer datos existentes
        $data_feedbacks = $this->obtener_data_feedbacks_por_jefe($id_jefe, $id_evalua_nombre);       
        
        return $this->render('feedbackfinal', [
            'model'=> $model,
            'data_feedbacks'=> $data_feedbacks,
            'id_jefe'=>$id_jefe
        ]);

    }

    public function actionCrearfeedbackfinal(){

        $form = Yii::$app->request->post();

        $id_jefe = $form['id_jefe'];
        $id_acuerdo = $form['id_acuerdo'];
        $comentarios = $form['comentarios'];

        //falta validar datos

        $actualizar_datos = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_feedback',[
            'comentario' => $comentarios,
        ],'id_gestor_evaluacion_feedback ='.$id_acuerdo.'')->execute();
       
        if($actualizar_datos>0){
            
        //Periodo actual que realizan la evaluacion
        $evaluacion_actual = $this->obtenerEvaluacionActual();        
        $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
      
        //Traer datos existentes
        $data_feedbacks = $this->obtener_data_feedbacks_por_jefe($id_jefe, $id_evalua_nombre);

            $response = [
                'status' => 'success',
                'message' => 'Acuerdo final creado exitosamente',
                'data' => $data_feedbacks,
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'Ocurrió un error al gaurdar el acuerdo final',
            ];
        }

         
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response; 

    }



    // SECCION PARA NOVEDADES

    //Menú de las Novedades
    public function actionNovedades(){

        $sessiones = Yii::$app->user->identity->id;

        $rol =  new Query;
        $rol    ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                        'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                        'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
        $command = $rol->createCommand();
        $roles = $command->queryScalar();


        return $this->render('novedades',[
            'roles'=>$roles
        ] );
    }

    // Vista novedades jefe incorrecto
    public function actionNovedadjefeincorrecto(){

        //Periodo actual que realizan la evaluacion
        $evaluacion_actual = $this->obtenerEvaluacionActual();        
        $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                
        //Traer datos existentes
        $data_jefe_incorrecto = $this->obtener_data_jefe_incorrecto($id_evalua_nombre);       
        
        return $this->render('novedadjefeincorrecto', [
            'data_jefe_incorrecto'=> $data_jefe_incorrecto,
            'id_evalua_nombre'=> $id_evalua_nombre
        ]);
    } 

    public function actionNovedadpersonalacargo(){

        //Periodo actual que realizan la evaluacion
        $evaluacion_actual = $this->obtenerEvaluacionActual();        
        $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                
        //Traer datos existentes
        $data_datatable = $this->obtener_data_personal_a_cargo($id_evalua_nombre);       
        

        return $this->render('novedadpersonalacargo', [
            'data_datatable'=> $data_datatable,
            'id_evalua_nombre'=> $id_evalua_nombre
        ]);
    } 

    public function actionNovedadeliminarevaluacion(){

        //Periodo actual que realizan la evaluacion
        $evaluacion_actual = $this->obtenerEvaluacionActual();        
        $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                
        //Traer datos existentes
        $data_datatable = $this->obtener_data_eliminarevaluacion($id_evalua_nombre);       
       
        return $this->render('novedadeliminarevaluacion', [
            'id_evalua_nombre'=> $id_evalua_nombre,
            'data_datatable'=> $data_datatable
        ]);
    }
    
    public function actionNovedadotrosinconvenientes(){

        //Periodo actual que realizan la evaluacion
        $evaluacion_actual = $this->obtenerEvaluacionActual();        
        $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                
        //Traer datos existentes
        $data_datatable = $this->obtener_data_otros_inconvenientes($id_evalua_nombre);       
       
        return $this->render('novedadotrosinconvenientes', [
            'id_evalua_nombre'=> $id_evalua_nombre,
            'data_datatable'=> $data_datatable
        ]);
    }

    public function actionActualizarotrosinconvenientes(){

        $parametros = Yii::$app->request->post();
        $id_novedad = $parametros['id_novedad'];
        $aprobacion = $parametros['estado_aprobacion'];

        //variables locales 
        $response=[];

        $evaluacion_actual = $this->obtenerEvaluacionActual();       
        $id_evaluacion_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";       
        

        if($aprobacion==0){

            //Actualizar estado de la novedad a -> No aprobado
            $actualizar_estado_novedad = $this->actualizar_novedad_no_aprobada('tbl_gestor_evaluacion_novedad_general', $id_novedad);
            
            if($actualizar_estado_novedad==1){               
                
                $data_actualizada = $this->obtener_data_otros_inconvenientes($id_evaluacion_nombre);
                
                $response = [
                    'status' => 'success',
                    'message' => 'Actualización exitosa, estado no aprobado',
                    'data' => $data_actualizada,
                ]; 

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Ocurrió un error al actualizar el nuevo estado de la solicitud',
                ];
            }
        }

        if($aprobacion==1) {

            //Actualizar estado de la novedad a -> Aprobado
            $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_general', $id_novedad);
            
            if ($actualizar_estado_novedad === 1) {

                $data_actualizada = $this->obtener_data_otros_inconvenientes($id_evaluacion_nombre);
               
                    $response = [
                        'status' => 'success',
                        'message' => 'Actualizacion exitosa, estado aprobado',
                        'data' => $data_actualizada,

                    ];  
            } else {            
                $response = [
                    'status' => 'error',
                    'message' => 'Ocurrió un error al actualizar el nuevo estado de la novedad',
                ];
            }  

        }

        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;
    }


    public function actionModalnovedadauto(){
        $model_jefe_incorrecto = new GestorEvaluacionNovedadJefeincorrecto();
                
        //Parametros recibidos por la vista autoevaluación
        $id_evalua_nom= Yii::$app->request->get('id_evalua');
        $id_colaborador_solicitante = Yii::$app->request->get('id_colab');        
        $id_jefe_actual = Yii::$app->request->get('id_jefe');

        if($id_jefe_actual){
            $cc_jefe_actual = Yii::$app->db->createCommand("select identificacion FROM tbl_gestor_evaluacion_usuarios WHERE id_gestor_evaluacion_usuarios in ('$id_jefe_actual')")->queryScalar();
        }
               
        //validacion de parametros
        if(empty($id_evalua_nom) || empty($id_colaborador_solicitante) ){
            Yii::$app->session->setFlash('error_novedad', 'Error en envio de parámetros para procesar la solicitud, reportar error.');
            return $this->redirect(['index']);            
        }

         //Variables locales
        $cc_colaborador_solicitante = Yii::$app->db->createCommand("select identificacion FROM tbl_gestor_evaluacion_usuarios WHERE id_gestor_evaluacion_usuarios in ('$id_colaborador_solicitante')")->queryScalar();
     
        if($id_jefe_actual==""){
            $id_jefe_actual=null; 
            $cc_jefe_actual=null;              
        }
        
        //Puede Crear la novedad
        $opcion_seleccionada = Yii::$app->request->post('seleccion');

        $sql = 'SELECT tiponovedad.id_gestor_evaluacion_estadonovedades AS id_estado_novedad FROM tbl_gestor_evaluacion_estadonovedades tiponovedad WHERE tiponovedad.nombre="En espera"';
        $id_estado_en_espera = Yii::$app->db->createCommand($sql)->queryScalar();
    
        if ( $model_jefe_incorrecto->load(Yii::$app->request->post()) ) {
        
            if ($opcion_seleccionada=="jefe_incorrecto"){

                $validar_novedades_a_cargo = $this->contar_registros_novedad_personal_a_cargo($id_colaborador_solicitante, $cc_colaborador_solicitante);
       
                //Ya existe una novedad "En espera" asociada a este usuario por cambio de jefe
                if ($validar_novedades_a_cargo==1) {
                    Yii::$app->session->setFlash('error_novedad', 'Ya existe una novedad "en espera" por cambio de Jefe');
                    return $this->redirect(['index']);
                }

                $model_jefe_incorrecto->id_evaluacion_nombre = $id_evalua_nom;
                $model_jefe_incorrecto->id_estado_novedad = $id_estado_en_espera;
                $model_jefe_incorrecto->id_solicitante = $id_colaborador_solicitante;               
                $model_jefe_incorrecto->cc_colaborador = $cc_colaborador_solicitante;
                $model_jefe_incorrecto->id_jefe_actual = $id_jefe_actual;
                $model_jefe_incorrecto->cc_jefe_actual = $cc_jefe_actual;
                $model_jefe_incorrecto->fechacreacion = date("Y-m-d");
                $model_jefe_incorrecto->usua_id=Yii::$app->user->identity->id;
                
                if ($model_jefe_incorrecto->save()) {
                    Yii::$app->session->setFlash('success_novedad', 'Creación exitosa para: jefe incorrecto');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error_novedad', 'Ocurrió un error al guardar: jefe incorrecto');
                    return $this->redirect(['index']);
                }
            }            
        }
               

        return $this->renderAjax('modalnovedadauto',[
            'model_jefe_incorrecto'=>$model_jefe_incorrecto
        ]);        
    }

    public function actionModalnovedadacargo(){
        $model = new GestorEvaluacionNovedadJefecolaborador();
        
        //Parametros recibidos por el boton crear novedad de evaluacion a cargo
        $id_evalua_nom= Yii::$app->request->get('id_evalua');
        $id_jefe_solicitante = Yii::$app->request->get('id_jefe');
        $id_colaborador_actual = Yii::$app->request->get('id_colab');        
    
        //validacion de parametros
        if(empty($id_evalua_nom) || empty($id_jefe_solicitante) || empty($id_colaborador_actual) ){
            Yii::$app->session->setFlash('error_novedad', 'Problemas en envio de parámetros para procesar la solicitud, reportar error.');
            return $this->redirect(['index']);            
        }

        //Variables locales
        $cc_colaborador_actual = Yii::$app->db->createCommand("select identificacion FROM tbl_gestor_evaluacion_usuarios WHERE id_gestor_evaluacion_usuarios in ('$id_colaborador_actual')")->queryScalar();

        $opcion_seleccionada = Yii::$app->request->post('seleccion');

        $sql = 'SELECT tiponovedad.id_gestor_evaluacion_estadonovedades AS id_estado_novedad FROM tbl_gestor_evaluacion_estadonovedades tiponovedad WHERE tiponovedad.nombre="En espera"';
        $id_estado_en_espera = Yii::$app->db->createCommand($sql)->queryScalar();
              
        if ( $model->load(Yii::$app->request->post()) ) {

            $model->id_evaluacion_nombre = $id_evalua_nom;
            $model->id_estado_novedad = $id_estado_en_espera;
            $model->id_jefe_solicitante = $id_jefe_solicitante;            
            $model->fechacreacion = date("Y-m-d");
            $model->usua_id=Yii::$app->user->identity->id;
         
            if ($opcion_seleccionada=="falta_persona"){
                $cc_nuevo_colaborador = $model->cc_colaborador_nuevo;
                $validar_novedad_jefe_incorrecto = $this->contar_registros_novedad_jefe_incorrecto(1, $cc_nuevo_colaborador);
                $validar_tiponov_falta_personal = $this->contar_registros_novedad_personal_a_cargo(1, $cc_nuevo_colaborador);
            
                //Ya existe una novedad "En espera" asociada a este usuario por cambio de jefe
                if ($validar_novedad_jefe_incorrecto==1 || $validar_tiponov_falta_personal==1 ) {
                    Yii::$app->session->setFlash('error_novedad', 'Ya existe una novedad "en espera" relacionada con cambio de jefe de este colaborador.');
                    return $this->redirect(['index']);
                }

                $id_falta_persona_a_cargo = Yii::$app->db->createCommand('
                SELECT tiponovedad.id_gestor_evaluacion_tiponovedad_jefecolaborador AS id_tiponovedad
                FROM tbl_gestor_evaluacion_tiponovedadjefecolaborador tiponovedad
                WHERE tiponovedad.nombre_tipo_novedad="Falta persona a mi cargo" AND tiponovedad.anulado=0
                ')->queryScalar();

                $model->id_tipo_novedad = $id_falta_persona_a_cargo; 
                $model->id_colaborador_actual = null;  
                $model->cc_colaborador_actual = null;             
                
                if ($model->save()) {
                    Yii::$app->session->setFlash('success_novedad', 'Creación exitosa para: falta persona a mi cargo. Documento: '. $cc_nuevo_colaborador);
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error_novedad', 'Ocurrió un error al guardar: falta persona a mi cargo');
                }
            }   

            if ($opcion_seleccionada=="no_esta_a_mi_cargo"){

                $validar_novedad_jefe_incorrecto = $this->contar_registros_novedad_jefe_incorrecto($id_colaborador_actual, $cc_colaborador_actual);
                $validar_tiponov_falta_personal = $this->contar_registros_novedad_personal_a_cargo($id_colaborador_actual, $cc_colaborador_actual);
            
                //Ya existe una novedad "En espera" asociada a este usuario por cambio de jefe
                if ($validar_novedad_jefe_incorrecto==1 || $validar_tiponov_falta_personal==1 ) {
                    Yii::$app->session->setFlash('error_novedad', 'Ya existe una novedad "en espera" relacionada con cambio de jefe de este colaborador.');
                    return $this->redirect(['index']);
                }
                
                $id_no_esta_a_cargo = Yii::$app->db->createCommand('
                SELECT tiponovedad.id_gestor_evaluacion_tiponovedad_jefecolaborador AS id_tiponovedad
                FROM tbl_gestor_evaluacion_tiponovedadjefecolaborador tiponovedad
                WHERE tiponovedad.nombre_tipo_novedad="Persona no está a mi cargo" AND tiponovedad.anulado=0
                ')->queryScalar();

                $model->id_tipo_novedad = $id_no_esta_a_cargo; 
                $model->id_colaborador_actual = $id_colaborador_actual; 
                $model->cc_colaborador_actual = $cc_colaborador_actual;
                $model->cc_colaborador_nuevo = null;                              

                if ($model->save()) {
                    Yii::$app->session->setFlash('success_novedad', 'Creación exitosa para: persona no está a mi cargo');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error_novedad', 'Ocurrió un error al guardar: persona no está a mi cargo');
                }

            }

            if ($opcion_seleccionada=="persona_retirada"){
                
                $validar_novedad_jefe_incorrecto = $this->contar_registros_novedad_jefe_incorrecto($id_colaborador_actual, $cc_colaborador_actual);
                $validar_tiponov_falta_personal = $this->contar_registros_novedad_personal_a_cargo($id_colaborador_actual, $cc_colaborador_actual);
            
                //Ya existe una novedad "En espera" asociada a este usuario por cambio de jefe
                if ($validar_novedad_jefe_incorrecto==1 || $validar_tiponov_falta_personal==1 ) {
                    Yii::$app->session->setFlash('error_novedad', 'Ya existe una novedad "en espera" relacionada con cambio de jefe de este colaborador.');
                    return $this->redirect(['index']);
                }                

                $id_persona_retirada = Yii::$app->db->createCommand('
                SELECT tiponovedad.id_gestor_evaluacion_tiponovedad_jefecolaborador AS id_tiponovedad
                FROM tbl_gestor_evaluacion_tiponovedadjefecolaborador tiponovedad
                WHERE tiponovedad.nombre_tipo_novedad="Persona retirada" AND tiponovedad.anulado=0
                ')->queryScalar();

                $model->id_tipo_novedad = $id_persona_retirada;
                $model->id_colaborador_actual = $id_colaborador_actual;
                $model->cc_colaborador_actual = $cc_colaborador_actual;
                $model->cc_colaborador_nuevo=null;

                if ($model->save()) {
                    Yii::$app->session->setFlash('success_novedad', 'Creación exitosa para: persona retirada');
                    return $this->redirect(['index']);
                } else {
                    Yii::$app->session->setFlash('error_novedad', 'Ocurrió un error al guardar: persona retirada');
                }

            }

        }

        return $this->renderAjax('modalnovedadacargo',[
            'model'=>$model
        ]);        
    }
    
    //Funcion para crear novedades de eliminacion y generales
    public function actionCrearnovedadgeneral(){
        
        //Parámetros recibidos desde el ajax
        $tipo_novedad = Yii::$app->request->post("tipo_novedad");
        $id_tipo_evaluacion = Yii::$app->request->post("id_nom_evaluacion");
        $id_solicitante = Yii::$app->request->post("id_solicitante");
        $cc_solicitante = Yii::$app->request->post("cc_solicitante");
        $id_evaluado = Yii::$app->request->post("id_evaluado");
        $comentarios_solicitud = Yii::$app->request->post("comentarios_solicitud");
      
        //Periodo actual que realizan la evaluacion
        $evaluacion_actual = $this->obtenerEvaluacionActual();        
        $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
        
        //Traerme el id estado novedades: "En espera"
        $sql = 'SELECT tiponovedad.id_gestor_evaluacion_estadonovedades AS id_estado_novedad FROM tbl_gestor_evaluacion_estadonovedades tiponovedad WHERE tiponovedad.nombre="En espera"';
        $id_estado_en_espera = Yii::$app->db->createCommand($sql)->queryScalar();       

        //Eliminar autoevaluación
        if($tipo_novedad=="eliminacion_evaluacion" && $id_tipo_evaluacion==1 ) {

            //Si completo todas las evaluaciones asociadas a un periodo de tiempo
            $completo_todas_las_evaluaciones_asociadas = $this->verificarEstadoEvaluaciones($id_solicitante, $id_evalua_nombre);            
            $existeunaevaluacion = $this->existen_formularios_asociados_a_un_usuario($id_solicitante, $id_tipo_evaluacion, $id_evalua_nombre);
            $registro_evaluacion = $existeunaevaluacion['cant_registros'];

            //obtener informacion si ya crearon una novedad por el mismo motivo y aun esat en espera
            $existe_novedad_por_eliminacion = $this->verificar_estado_enespera_autoevaluacion($id_evalua_nombre, $id_tipo_evaluacion, $id_solicitante, $id_estado_en_espera);
            
            if(!empty($existe_novedad_por_eliminacion)){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    header('Content-Type: application/json');
                    die(json_encode(['status' => 'error', 'data' => 'Ya existe una solicitud: eliminar autoevaluación en estado "en espera"']));                
            }

            //Validar si ya hizo la evaluacion y si aun tiene permitido eliminar la evaluación (fecha creacion no puede ser mayor a 5 días)
            if($registro_evaluacion==1){
                $fechaCreacionFormulario= $existeunaevaluacion['fechacreacion'];
                $fechaActual = date('Y-m-d'); 
                
                if($fechaActual > $fechaCreacionFormulario){
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    header('Content-Type: application/json');
                    die(json_encode(['status' => 'error', 'data' => 'No es posible eliminar evaluación, supera el tiempo máximo permitido (5 dias)']));            

                }     
            }
            
            if($completo_todas_las_evaluaciones_asociadas){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'error', 'data' => 'Ya existen resultados asociados a esta evaluación, no es posible eliminarla']));             
            }

            if($registro_evaluacion==0){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'error', 'data' => 'No existe un registro de autoevaluación para eliminar']));             
            }



            $crear_registro = Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_novedad_eliminareval',[
                'id_evaluacion_nombre' => $id_evalua_nombre,   
                'id_estado_novedad' => $id_estado_en_espera,
                'id_tipo_evaluacion' => $id_tipo_evaluacion,
                'id_solicitante' => $id_solicitante,
                'cc_solicitante' => $cc_solicitante,
                'comentarios_solicitud' => $comentarios_solicitud,
                'fechacreacion' => date('Y-m-d'),
                'usua_id' => Yii::$app->user->identity->id,                                  
            ])->execute();
            
            if($crear_registro>0){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'success', 'data' => 'La novedad "eliminar autoevaluación" se creó exitosamente.']));                    
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'error', 'data' => 'Hubo un error al crear la novedad: eliminar autoevaluación'])); 
            }
        }

        //Eliminar evaluacion a cargo
        if($tipo_novedad=="eliminacion_evaluacion" && $id_tipo_evaluacion==3 ) { 
             //Si completo todas las evaluaciones asociadas a un periodo de tiempo
            $completo_todas_las_evaluaciones_asociadas = $this->verificarEstadoEvaluaciones($id_evaluado, $id_evalua_nombre);
            $existeunaevaluacion = $this->existen_formularios_asociados_a_un_usuario($id_evaluado, $id_tipo_evaluacion, $id_evalua_nombre);
            $registro_evaluacion = $existeunaevaluacion['cant_registros'];

            //Validar si aun tiene permitido eiminar la evaluación (no puede ser mayor a 5 días)
            if($registro_evaluacion==1){
                $fechaCreacionFormulario= $existeunaevaluacion['fechacreacion'];
                $fechaActual = date('Y-m-d'); 
                
                if($fechaActual > $fechaCreacionFormulario){
                    Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    header('Content-Type: application/json');
                    die(json_encode(['status' => 'error', 'data' => 'No es posible eliminar evaluación, supera el tiempo máximo permitido (5 dias)']));            

                }     
            }

            //obtener informacion si ya crearon una novedad por el mismo motivo y aun esta en espera
            $existe_novedad_por_eliminacion = $this->verificar_estado_novedad_evalua_a_cargo($id_evalua_nombre, $id_tipo_evaluacion, $id_solicitante, $id_evaluado, $id_estado_en_espera);
            
            if(!empty($existe_novedad_por_eliminacion)){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    header('Content-Type: application/json');
                    die(json_encode(['status' => 'error', 'data' => 'Ya existe una solicitud: eliminar evaluación a cargo en estado "en espera"']));                
            }

            if($completo_todas_las_evaluaciones_asociadas){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'error', 'data' => 'Ya existe una autoevaluación asociada a esta persona.']));             
            }

            if($registro_evaluacion==0){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'error', 'data' => 'No existe un registro de evaluación a cargo asociado a dicho usuario']));             
            }

            $cc_evaluado= $this->obtener_cc_usuario_carga_masiva($id_evaluado);
          
            $crear_registro = Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_novedad_eliminareval',[
                'id_evaluacion_nombre' => $id_evalua_nombre,   
                'id_estado_novedad' => $id_estado_en_espera,
                'id_tipo_evaluacion' => $id_tipo_evaluacion,
                'id_solicitante' => $id_solicitante,
                'cc_solicitante' => $cc_solicitante,
                'id_evaluado'=>$id_evaluado,
                'cc_evaluado'=>$cc_evaluado,
                'comentarios_solicitud' => $comentarios_solicitud,
                'fechacreacion' => date('Y-m-d'),
                'usua_id' => Yii::$app->user->identity->id,                                  
            ])->execute();   
            
            if($crear_registro>0){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'success', 'data' => 'La novedad "eliminar evaluación a cargo" se creó exitosamente.']));                    
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'error', 'data' => 'Hubo un error al crear la novedad: eliminar evaluación a cargo'])); 
            }

        }

        if($tipo_novedad=="otra_novedad") {
            $crear_registro = Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_novedad_general',[
                'id_evaluacion_nombre' => $id_evalua_nombre,   
                'id_estado_novedad' => $id_estado_en_espera,
                'id_solicitante' => $id_solicitante,
                'cc_solicitante' => $cc_solicitante,
                'comentarios_solicitud' => $comentarios_solicitud,
                'fechacreacion' => date('Y-m-d'),
                'usua_id' => Yii::$app->user->identity->id,                                  
            ])->execute();   
            
            if($crear_registro>0){
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'success', 'data' => 'Creación exitosa']));                    
            } else {
                Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                header('Content-Type: application/json');
                die(json_encode(['status' => 'error', 'data' => 'Hubo un error al crear la novedad'])); 
            }

        }
        

        Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                    header('Content-Type: application/json');
                    die(json_encode(['status' => 'error', 'data' => 'Hubo un error no ingresó a ninguna condición, revisar parámetros'])); 
        
    }


    public function actionActualizarjefecorrecto() {
        
        $parametros = Yii::$app->request->post();
        $id_novedad = $parametros['id_novedad'];
        $aprobacion = $parametros['estado_aprobacion'];

        //variables locales 
        $response=[];
        $id_evaluacion_nombre="";
        $cc_colaborador="";
        $cc_jefe_correcto=""; 
        
        if($aprobacion==0){

            //Actualizar estado de la novedad a -> No aprobado
            $actualizar_estado_novedad = $this->actualizar_novedad_no_aprobada('tbl_gestor_evaluacion_novedad_jefeincorrecto', $id_novedad);
            
            if($actualizar_estado_novedad==1){
                
                $evaluacion_actual = $this->obtenerEvaluacionActual();
                $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                $data_actualizada_jefe_correcto = $this->obtener_data_jefe_incorrecto($id_evalua_nombre);
                
                $response = [
                    'status' => 'success',
                    'message' => 'Actualización exitosa, estado no aprobado',
                    'data' => $data_actualizada_jefe_correcto,
                ]; 

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Ocurrió un error al actualizar el nuevo estado de la solicitud',
                ];
            }
        }


        if($aprobacion==1){

            $data_novedad = $this->obtener_data_jefe_incorrecto_por_id($id_novedad);
            if(!empty($data_novedad)){
                $id_evaluacion_nombre = $data_novedad['id_evaluacion_nombre'];
                $id_colaborador_solicitante = $data_novedad['id_solicitante'];
                $id_jefe_actual = $data_novedad['id_jefe_actual'];
                $cc_colaborador = $data_novedad['cc_colaborador']; 
                $cc_jefe_correcto = $data_novedad['cc_jefe_correcto'];
            }

            //Validacion de datos
            if ( empty($id_evaluacion_nombre) ) {
            
                $response = [
                    'status' => 'error',
                    'message' => 'Parámetros faltante id_evaluacion_nombre para la solicitud.',
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }

            if( empty($id_colaborador_solicitante) ){
                $response = [
                    'status' => 'error',
                    'message' => 'Parámetros faltante id_colaborador_solicitante para la solicitud.',
                ];

                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;

            }

            if( empty($cc_jefe_correcto ) ){
                $response = [
                    'status' => 'error',
                    'message' => 'Parámetros faltante cc_jefe_correcto para la solicitud.',
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;

            }

            //Verificar si existe el jefe correcto en nuestra BD
            $id_jefe_correcto = $this->obtener_id_usuario($cc_jefe_correcto) ?: false; 
            
            //No existe jefe correcto en nuestra BD
            if(!$id_jefe_correcto) {

                $response = [
                    'status' => 'error',
                    'message' => 'No encontramos el documento: ' . $cc_jefe_correcto . '. Por favor subir la relación jefe - colaborador por carga masiva e intentar aprobarla nuevamente',
                ];

                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }
                
            // Sí existe un jefe actual y un jefe correcto 
            if($id_jefe_actual && $id_jefe_correcto) {  
                $es_el_mismo_jefe= false;

                //obtener los roles de cada jefe
                $rol_jefe_actual = $this->obtener_roles_usuario_por_id($id_jefe_actual);
                $rol_jefe_correcto = $this->obtener_roles_usuario_por_id($id_jefe_correcto);

                if( !empty($rol_jefe_actual) && !empty($rol_jefe_correcto) ) {
                    $esjefe_actual = $rol_jefe_actual['es_jefe'];                
                    $esjefe_correcto = $rol_jefe_correcto['es_jefe'];
                } 

                //Verificar con que jefe tiene la relacion de jefe_colaborador
                $id_jefe_colaborador = $this->obtener_jefe_de_un_colaborador($id_colaborador_solicitante);

                if(!empty($id_jefe_colaborador)){                    

                    foreach ($id_jefe_colaborador as $jefe) {  

                        $pk_registro= $jefe['id_relacion'];                          
                
                        if ($jefe['id_usuario_jefe'] === $id_jefe_correcto) {
                            $es_el_mismo_jefe= true;                                                            
                            $this->actualizar_anulado_en_jefe_colaborador(0, $pk_registro); //Habilito de nuevo la relacion
                        } else {                                  
                        //Deshabilito el resto de relaciones porque solo debe tener un JEFE
                        $this->actualizar_anulado_en_jefe_colaborador(1, $pk_registro);
                        } 
                    }

                    if($es_el_mismo_jefe){
                        //actualizamos roles                            
                        $this->actualizar_rol_jefe($esjefe_actual, $id_jefe_actual);
                        $this->actualizar_rol_jefe($esjefe_correcto, $id_jefe_correcto);

                        //Actualizar estado de la novedad a -> Aprobado
                        $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_jefeincorrecto', $id_novedad);
                        
                        if ($actualizar_estado_novedad === 1) {
                            $data_actualizada = $this->obtener_data_jefe_incorrecto($id_evaluacion_nombre);

                            $response = [
                                'status' => 'success',
                                'message' => 'Se actualizó exitosamente la solicitud',
                                'data'=> $data_actualizada
                            ]; 

                        } else {

                            $response = [
                                'status' => 'error',
                                'message' => 'Ocurrió un error al actualizar la solicitud',
                            ];
                        
                        }
                    } else {
                        //crear la nueva relacion
                        $insert_data = Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_jefe_colaborador',[
                        'id_usuario_jefe' => $id_jefe_correcto,
                        'id_usuario_colaborador' => $id_colaborador_solicitante,                    
                        'fechacreacion' => date("Y-m-d"),                                        
                        'usua_id' => Yii::$app->user->identity->id
                        ])->execute();                        

                        if($insert_data==1){

                            //actualizamos roles                            
                            $this->actualizar_rol_jefe($esjefe_actual, $id_jefe_actual);
                            $this->actualizar_rol_jefe($esjefe_correcto, $id_jefe_correcto);

                            $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_jefeincorrecto', $id_novedad);
                        
                            if ($actualizar_estado_novedad === 1) {
                                $evaluacion_actual = $this->obtenerEvaluacionActual();
                                $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                                $data_actualizada = $this->obtener_data_jefe_incorrecto($id_evalua_nombre);

                                $response = [
                                    'status' => 'success',
                                    'message' => 'Se actualizó exitosamente la solicitud',
                                    'data'=> $data_actualizada
                                ]; 

                            } else {

                                $response = [
                                    'status' => 'error',
                                    'message' => 'Ocurrió un error al actualizar la solicitud',
                                ];
                            
                            }

                        }
                    }
                }
            } 


            if($id_jefe_actual==null && $id_jefe_correcto) { 
                $es_el_mismo_jefe= false;

                $rol_jefe_correcto = $this->obtener_roles_usuario_por_id($id_jefe_correcto);

                if( !empty($rol_jefe_correcto) ) {               
                    $esjefe_correcto = $rol_jefe_correcto['es_jefe'];
                }
                
                //Verificar con que jefe tiene la relacion de jefe_colaborador
                $id_jefe_colaborador = $this->obtener_jefe_de_un_colaborador($id_colaborador_solicitante);
    
                if( !empty($id_jefe_colaborador) ){                    

                    foreach ($id_jefe_colaborador as $jefe) {  

                        $pk_registro= $jefe['id_relacion'];                          
                
                        if ($jefe['id_usuario_jefe'] === $id_jefe_correcto) {
                            $es_el_mismo_jefe= true;                                                            
                            $this->actualizar_anulado_en_jefe_colaborador(0, $pk_registro); //Habilito de nuevo la relacion
                        } else {                                  
                        //Deshabilito el resto de relaciones porque solo debe tener un JEFE
                        $this->actualizar_anulado_en_jefe_colaborador(1, $pk_registro);
                        } 
                    }
                }

                if(!$es_el_mismo_jefe){
                    //crear la nueva relacion
                    $insert_data = Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_jefe_colaborador',[
                        'id_usuario_jefe' => $id_jefe_correcto,
                        'id_usuario_colaborador' => $id_colaborador_solicitante,                    
                        'fechacreacion' => date("Y-m-d"),                                        
                        'usua_id' => Yii::$app->user->identity->id
                        ])->execute();
                }

                //actualizar rol del nuevo Jefe 
                $this->actualizar_rol_jefe($esjefe_correcto, $id_jefe_correcto);

                //Actualizar estado de la novedad a -> Aprobado
                $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_jefeincorrecto', $id_novedad);
                        
                if ($actualizar_estado_novedad === 1) {

                    $data_actualizada = $this->obtener_data_jefe_incorrecto($id_evaluacion_nombre);

                        $response = [
                            'status' => 'success',
                            'message' => 'Actualizacion exitosa para el Jefe con documento: ' . $cc_jefe_correcto,
                            'data' => $data_actualizada,

                        ];  
                } else {            
                    $response = [
                        'status' => 'error',
                        'message' => 'Ocurrió un error al actualizar el nuevo estado de la novedad',
                    ];
                }  

            }            
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
        
    }

    public function actionEliminarevaluacionusuario() {

        $parametros = Yii::$app->request->post();
        $id_novedad = $parametros['id_novedad'];
        $aprobacion = $parametros['estado_aprobacion'];

        //variables locales 
        $response=[];
        $id_evaluacion_nombre="";
        $cc_colaborador="";
        $cc_jefe_correcto=""; 
        $evaluacion_actual = $this->obtenerEvaluacionActual();
        $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
        
        if($aprobacion==0){

            //Actualizar estado de la novedad a -> No aprobado
            $actualizar_estado_novedad = $this->actualizar_novedad_no_aprobada('tbl_gestor_evaluacion_novedad_eliminareval', $id_novedad);
            
            if($actualizar_estado_novedad==1){               
                
                $data_actualizada = $this->obtener_data_eliminarevaluacion($id_evalua_nombre);
                
                $response = [
                    'status' => 'success',
                    'message' => 'Actualización exitosa, estado no aprobado',
                    'data' => $data_actualizada,
                ]; 

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Ocurrió un error al actualizar el nuevo estado de la solicitud',
                ];
            }
        }

        if($aprobacion==1) {

            $data_novedad = $this->obtener_data_eliminarevaluacion_por_id($id_novedad);
            
            if(!empty($data_novedad)){
                $id_evaluacion_nombre = $data_novedad['id_evaluacion_nombre'];
                $id_solicitante = $data_novedad['id_solicitante'];
                $id_tipo_evaluacion = $data_novedad['id_tipo_evaluacion'];
                $id_evaluado = $data_novedad['id_evaluado'];
            }

             //Validacion de datos
             if ( empty($id_evaluacion_nombre) || empty($id_solicitante) || empty($id_tipo_evaluacion) ) {
            
                $response = [
                    'status' => 'error',
                    'message' => 'Parámetros faltantes para la solicitud. Reportar error.',
                ];
            }

            //autoevaluacion
            if($id_tipo_evaluacion==1){
                $eliminar_evalua = $this->eliminarevaluacion($id_evaluacion_nombre, $id_tipo_evaluacion, $id_solicitante);

                //Eliminacion exitosa
                if($eliminar_evalua==1){

                    //Actualizar estado de la novedad a -> Aprobado
                    $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_eliminareval', $id_novedad);
                        
                    if ($actualizar_estado_novedad === 1) {

                        $data_actualizada = $this->obtener_data_eliminarevaluacion($id_evalua_nombre);

                            $response = [
                                'status' => 'success',
                                'message' => 'Actualizacion exitosa al eliminar autoevaluación',
                                'data' => $data_actualizada,

                            ];  
                    } else {            
                        $response = [
                            'status' => 'error',
                            'message' => 'Ocurrió un error al actualizar el nuevo estado de la novedad',
                        ];
                    }
                    
                }
            }

            //Evaluación a cargo
            if($id_tipo_evaluacion==3){
                //Validar dator requerido
                if ( empty($id_evaluado) ) {            
                    $response = [
                        'status' => 'error',
                        'message' => 'Parámetros id_evaluado faltante para la solicitud. Reportar error.',
                    ];
                }

                //Eliminar evaluacion de la tabla formularios, la cual elimina en dataform y en respuestasform
                $eliminar_evalua = $this->eliminarevaluacion($id_evaluacion_nombre, $id_tipo_evaluacion, $id_evaluado);

                //Eliminacion exitosa
                if($eliminar_evalua==1){

                    //Actualizar estado de la novedad a -> Aprobado
                    $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_eliminareval', $id_novedad);
                        
                    if ($actualizar_estado_novedad === 1) {

                        $data_actualizada = $this->obtener_data_eliminarevaluacion($id_evalua_nombre);

                            $response = [
                                'status' => 'success',
                                'message' => 'Actualizacion exitosa al eliminar evaluación',
                                'data' => $data_actualizada,

                            ];  
                    } else {            
                        $response = [
                            'status' => 'error',
                            'message' => 'Ocurrió un error al actualizar el nuevo estado de la novedad',
                        ];
                    }
                }
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return $response;
    }

    public function actionGestionarpersonalacargo() {

        $parametros = Yii::$app->request->post();
        $id_novedad = $parametros['id_novedad'];
        $aprobacion = $parametros['estado_aprobacion'];

        //variables locales 
        $response=[];
        $id_evaluacion_nombre="";
        
        if($aprobacion==0){

            //Actualizar estado de la novedad a -> No aprobado
            $actualizar_estado_novedad = $this->actualizar_novedad_no_aprobada('tbl_gestor_evaluacion_novedad_jefecolaborador', $id_novedad);
            
            if($actualizar_estado_novedad==1){
                
                $evaluacion_actual = $this->obtenerEvaluacionActual();
                $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                $data_actualizada = $this->obtener_data_personal_a_cargo($id_evalua_nombre);
                
                $response = [
                    'status' => 'success',
                    'message' => 'Actualización exitosa, estado no aprobado',
                    'data' => $data_actualizada,
                ]; 

            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'Ocurrió un error al actualizar el nuevo estado de la solicitud',
                ];
            }

        }

        if($aprobacion==1){

            $data_novedad = $this->obtener_data_personal_a_cargo_por_pk($id_novedad);
                        
            //informacion de la novedad a gestionar
            if(!empty($data_novedad)){
                $nombre_tipo_novedad = $data_novedad['nombre_tipo_novedad'];
                $id_jefe_solicitante = $data_novedad['id_jefe_solicitante'];
                $id_colaborador_actual = $data_novedad['id_colaborador_actual'];
                $cc_colaborador_actual = $data_novedad['cc_colaborador_actual'];
                $cc_colaborador_nuevo = $data_novedad['cc_colaborador_nuevo'];
            }

            //Validacion de datos
            if ( empty($nombre_tipo_novedad) ) {            
                $response = [
                    'status' => 'error',
                    'message' => 'Parámetro tipo de novedad faltante para la solicitud con ID: ' . $id_novedad,
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            } 

            if ( empty($id_jefe_solicitante) ) {            
                $response = [
                    'status' => 'error',
                    'message' => 'Parámetro id jefe faltante para la solicitud con ID: ' . $id_novedad,
                ];
                Yii::$app->response->format = Response::FORMAT_JSON;
                return $response;
            }


            //TIPO DE NOVEDADES ----------------------------------------------------------------------

            if($nombre_tipo_novedad=="Persona no está a mi cargo") {
                
                if ( empty($id_colaborador_actual) ) {            
                    $response = [
                        'status' => 'error',
                        'message' => 'Parámetro id colaborador actual faltante para la solicitud con ID: ' . $id_novedad,
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                } 

                $pk_jefe_colaborador = $this->obtener_id_jefe_x_colaborador($id_colaborador_actual, $id_jefe_solicitante);

                if($pk_jefe_colaborador){
                    $remover_jefe_colaborador = $this->eliminar_logicamente_jefe_x_colaborador($pk_jefe_colaborador);
                
                    if($remover_jefe_colaborador==1){

                        //Obtener su rol si son o no son jefes
                        $rol_id_solicitante = $this->obtener_roles_usuario_por_id($id_jefe_solicitante);
                        $rol_id_colaborador_actual= $this->obtener_roles_usuario_por_id($id_colaborador_actual);
        
                        if( !empty($rol_id_solicitante) && !empty($rol_id_colaborador_actual) ) {
                            $es_jefe_solicitante = $rol_id_solicitante['es_jefe'];              
                            $es_jefe_colaborador = $rol_id_colaborador_actual['es_jefe'];
                        }

                        $this->actualizar_rol_jefe($es_jefe_solicitante, $id_jefe_solicitante);
                        $this->actualizar_rol_jefe($es_jefe_colaborador, $id_colaborador_actual);

                        //Actualizar estado de la novedad a -> Aprobado
                        $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_jefecolaborador', $id_novedad);
                        
                        if ($actualizar_estado_novedad === 1) {
                            $evaluacion_actual = $this->obtenerEvaluacionActual();
                            $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                            $data_actualizada = $this->obtener_data_personal_a_cargo($id_evalua_nombre);

                            $response = [
                                'status' => 'success',
                                'message' => 'Se desvinculó exitosamente el usuario a cargo',
                                'data'=> $data_actualizada
                            ]; 

                        } else { 

                            $response = [
                                'status' => 'error',
                                'message' => 'Ocurrió un error al eliminar vinculación',
                            ];
                           
                        } 

                    }

                }

            }

            if($nombre_tipo_novedad=="Falta persona a mi cargo"){

                //Validacion de datos
                if ( empty($cc_colaborador_nuevo) ) {            
                    $response = [
                        'status' => 'error',
                        'message' => 'Falta el número del documento de identidad del usuario para procesar la solicitud con ID: ' . $id_novedad,
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                } 

                //buscar si existe en los usuarios de la carga masiva con anulado = 0
                $id_usuario_faltante= $this->obtener_id_usuario($cc_colaborador_nuevo);
                
                //Si existe
                if($id_usuario_faltante){

                    //Verificar con que jefe tiene la relacion de jefe_colaborador
                    $id_jefe_colaborador = $this->obtener_jefe_de_un_colaborador($id_usuario_faltante);
                
                    if(!empty($id_jefe_colaborador)){
                        $es_el_mismo_jefe= false;

                        foreach ($id_jefe_colaborador as $jefe) {  

                            $pk_registro= $jefe['id_relacion'];                          
                    
                            if ($jefe['id_usuario_jefe'] === $id_jefe_solicitante) {
                                $es_el_mismo_jefe= true;                                                            
                                $this->actualizar_anulado_en_jefe_colaborador(0, $pk_registro); //Habilito de nuevo la relacion
                            } else {                                  
                            //Deshabilito el resto de relaciones porque solo debe tener un JEFE
                            $this->actualizar_anulado_en_jefe_colaborador(1, $pk_registro);
                            } 
                        }

                        if($es_el_mismo_jefe){
                            //No actualizo el rol de jefe porque ya es jefe
                            //Actualizar estado de la novedad a -> Aprobado
                            $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_jefecolaborador', $id_novedad);
                            
                            if ($actualizar_estado_novedad === 1) {
                                $evaluacion_actual = $this->obtenerEvaluacionActual();
                                $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                                $data_actualizada = $this->obtener_data_personal_a_cargo($id_evalua_nombre);

                                $response = [
                                    'status' => 'success',
                                    'message' => 'Se actualizó exitosamente la solicitud',
                                    'data'=> $data_actualizada
                                ]; 

                            } else {

                                $response = [
                                    'status' => 'error',
                                    'message' => 'Ocurrió un error al actualizar la solicitud',
                                ];
                            
                            }
                        } else {
                            //crear la nueva relacion
                            $insert_data = Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_jefe_colaborador',[
                                'id_usuario_jefe' => $id_jefe_solicitante,
                                'id_usuario_colaborador' => $id_usuario_faltante,                    
                                'fechacreacion' => date("Y-m-d"),                                        
                                'usua_id' => Yii::$app->user->identity->id
                                ])->execute();

                            if($insert_data==1){

                                $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_jefecolaborador', $id_novedad);
                            
                                if ($actualizar_estado_novedad === 1) {
                                    $evaluacion_actual = $this->obtenerEvaluacionActual();
                                    $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                                    $data_actualizada = $this->obtener_data_personal_a_cargo($id_evalua_nombre);

                                    $response = [
                                        'status' => 'success',
                                        'message' => 'Se actualizó exitosamente la solicitud',
                                        'data'=> $data_actualizada
                                    ]; 

                                } else {

                                    $response = [
                                        'status' => 'error',
                                        'message' => 'Ocurrió un error al actualizar la solicitud',
                                    ];
                                
                                }

                            }
                        }
                    }

                } else {
                    //carga masiva
                    $response = [
                        'status' => 'error',
                        'message' => 'No encontramos el documento: ' . $cc_colaborador_nuevo . '. Por favor subir la relación jefe - colaborador por carga masiva e intentar aprobarla nuevamente',
                    ];
                }
            }

            if($nombre_tipo_novedad=="Persona retirada"){

                //Validar datos requeridos
                if ( empty($id_colaborador_actual) ) {            
                    $response = [
                        'status' => 'error',
                        'message' => 'Parámetro id colaborador actual faltante para la solicitud con ID: ' . $id_novedad,
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }

                if ( empty($cc_colaborador_actual) ) {            
                    $response = [
                        'status' => 'error',
                        'message' => 'Parámetro faltante documento del colaborador actual, para la solicitud con ID: ' . $id_novedad,
                    ];
                    Yii::$app->response->format = Response::FORMAT_JSON;
                    return $response;
                }


                //Eliminar relacion logicamnete anulado =1
                $pk_jefe_colaborador = $this->obtener_id_jefe_x_colaborador($id_colaborador_actual, $id_jefe_solicitante);
                $remover_jefe_colaborador = $this->eliminar_logicamente_jefe_x_colaborador($pk_jefe_colaborador);

                //Confirmar si continua siento jefe
                $this->actualizar_rol_jefe(1, $id_jefe_solicitante);

                //Eliminar logicamente el usuario
                $this->eliminar_logicamente_un_usuario($id_colaborador_actual, "Retiro Konecta");
                
                //Actualizar estado de la novedad a -> Aprobado
                $actualizar_estado_novedad = $this->actualizar_novedad_aprobada('tbl_gestor_evaluacion_novedad_jefecolaborador', $id_novedad);
                            
                if ($actualizar_estado_novedad == 1) {
                    $evaluacion_actual = $this->obtenerEvaluacionActual();
                                    $id_evalua_nombre = (count($evaluacion_actual)>0) ? $evaluacion_actual['id_evalua']: "";
                    $data_actualizada = $this->obtener_data_personal_a_cargo($id_evalua_nombre);

                    $response = [
                        'status' => 'success',
                        'message' => 'Se actualizó exitosamente la solicitud',
                        'data'=> $data_actualizada
                    ]; 

                } else {

                    $response = [
                        'status' => 'error',
                        'message' => 'Ocurrió un error al actualizar la solicitud',
                    ];
                
                }
                                
            }
                        
        }
        
        Yii::$app->response->format = Response::FORMAT_JSON;
        return $response;

    }



    //FUNCIONES

    //Obtener la evaluacion habilitada
    public function obtenerEvaluacionActual(){

        $evaluacion_actual = Yii::$app->db->createCommand('
        SELECT idevaluacionnombre AS id_evalua, nombreeval
        FROM tbl_evaluacion_nombre 
        WHERE anulado=0
        ORDER BY fechacrecion
        ')->queryOne();

        return $evaluacion_actual;

    }

    public function obtener_id_autoevaluacion(){

        $id_de_la_evaluacion = Yii::$app->db->createCommand('
        SELECT eval.idevaluaciontipo FROM tbl_evaluacion_tipoeval eval
        WHERE eval.tipoevaluacion="Autoevaluacion" AND eval.anulado=0
        ')->queryScalar();

        return $id_de_la_evaluacion;        
    }

    public function obtener_id_evaluacion_a_cargo(){

        $id_de_la_evaluacion = Yii::$app->db->createCommand('
        SELECT eval.idevaluaciontipo FROM tbl_evaluacion_tipoeval eval
        WHERE eval.tipoevaluacion="A cargo" AND eval.anulado=0
        ')->queryScalar();

        return $id_de_la_evaluacion;        
    }

    
    public function obtener_id_usuario($documento){

        $result = Yii::$app->db->createCommand("
        SELECT u.id_gestor_evaluacion_usuarios AS id_user
        FROM tbl_gestor_evaluacion_usuarios u
        WHERE u.anulado=0 AND identificacion IN ('$documento')
        ")->queryScalar();

        return $result;
    }

    //Con id de usuario desde carga masiva
    public function obtener_cc_usuario_carga_masiva($id_usuario){
        $cc_usuario = Yii::$app->db->createCommand("
        select identificacion FROM tbl_gestor_evaluacion_usuarios 
        WHERE anulado=0 AND id_gestor_evaluacion_usuarios in ('$id_usuario')
        ")->queryScalar();

        return $cc_usuario;

    } 

    public function eliminar_logicamente_un_usuario($id_usuario, $motivo) {
        $filas_afectadas = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_usuarios',[
            'anulado' => 1,
            'motivo_anulado'=>$motivo,
            'fecha_modificado'=> date("Y-m-d"),
            'usua_id_modificador'=> Yii::$app->user->identity->id
        ],'id_gestor_evaluacion_usuarios ='.$id_usuario.'')->execute();

        if ($filas_afectadas>0) {
            return 1; // Actualización exitosa
        } else {
            return 0; // Ocurrió un error en la actualizacion
        }
    }

    // Funcion que retorna cantidad de registros, id_usuario,
    // si es jefe y si es colaborador segun la cedula ingresada
    public function obtener_roles_usuario_por_id($id_usuario){

        $result = Yii::$app->db->createCommand("
        select         
        u.es_jefe, u.es_colaborador
        from tbl_gestor_evaluacion_usuarios u 
        WHERE id_gestor_evaluacion_usuarios IN ('$id_usuario')
        ")->queryOne();

        return $result;

    }

    //Funcion que retorna el id de la relacion de jefe_colaborador si existe, sino retorna false
    public function obtener_id_jefe_x_colaborador($id_usuario_colaborador, $id_usuario_jefe_actual){
        $result = Yii::$app->db->createCommand('
        SELECT relacion.id_gestor_evaluacion_jefe_colaborador AS id_relacion
        FROM tbl_gestor_evaluacion_jefe_colaborador relacion
        WHERE relacion.anulado=0 AND relacion.id_usuario_colaborador = :id_colaborador AND relacion.id_usuario_jefe = :id_jefe
        ')
        ->bindValue(':id_colaborador', $id_usuario_colaborador)
        ->bindValue(':id_jefe', $id_usuario_jefe_actual)
        ->queryScalar();
        return $result;        
    }

    //Funcion para obente el jefe asociado a un colaborador
    public function obtener_jefe_de_un_colaborador($id_usuario_colaborador){
        $result = Yii::$app->db->createCommand('
        SELECT relacion.id_gestor_evaluacion_jefe_colaborador AS id_relacion,
        relacion.id_usuario_jefe
        FROM tbl_gestor_evaluacion_jefe_colaborador relacion
        WHERE relacion.id_usuario_colaborador = :id_colaborador
        ')
        ->bindValue(':id_colaborador', $id_usuario_colaborador)
        ->queryAll();

        return $result;        
    }

    // Actualizar el atributo anulado 
    public function actualizar_anulado_en_jefe_colaborador($valor_anulado, $pk_registro){

        $filas_afectadas = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_jefe_colaborador',[
            'anulado' => $valor_anulado,
        ],'id_gestor_evaluacion_jefe_colaborador ='.$pk_registro.'')->execute();

        if ($filas_afectadas>0) {
            return 1; // Actualización exitosa
        } else {
            return 0; // Ocurrió un error en la actualizacion
        }
    }    

    public function actualizar_jefe_correcto($pk_jefe_colaborador, $id_jefe_correcto) {

        // Ejecutar la actualización
        $filas_afectadas = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_jefe_colaborador',[
            'id_usuario_jefe' => $id_jefe_correcto,
        ],'id_gestor_evaluacion_jefe_colaborador ='.$pk_jefe_colaborador.'')->execute();

        if ($filas_afectadas === 1) {
            return 1; // Actualización exitosa
        } else {
            return 0; // Ocurrió un error en la actualizacion
        }

    }

    // Funcion para actualizar el rol de un jefe en la tabla de usuarios
    public function actualizar_rol_jefe($es_jefe, $id_usuario){
        
        if ($es_jefe == 1) {

            $cantidad_personas_a_cargo = (new \yii\db\Query())
            ->select('COUNT(relacion.id_gestor_evaluacion_jefe_colaborador)')
            ->from('tbl_gestor_evaluacion_jefe_colaborador relacion')
            ->where(['relacion.id_usuario_jefe' => $id_usuario, 'relacion.anulado'=>0])->scalar();
            
            //Si ya no tiene personas a cargo, cambiar rol a colaborador
            if ($cantidad_personas_a_cargo == 0) {
                Yii::$app->db->createCommand()
                    ->update('tbl_gestor_evaluacion_usuarios', ['es_jefe' => null], ['id_gestor_evaluacion_usuarios' => $id_usuario])
                    ->execute();
            }

        } elseif ($es_jefe === null) {
            //Si antes no era jefe, pero se agrego la nueva persona lo convierte en Jefe
            Yii::$app->db->createCommand()
                ->update('tbl_gestor_evaluacion_usuarios', ['es_jefe' => 1], ['id_gestor_evaluacion_usuarios' => $id_usuario])
                ->execute();
        }     
    }

    //Actualiza como estado aprobado 
    public function actualizar_novedad_aprobada($nombre_tabla_novedad, $pk_tabla){

        $fechaGestionado = date("Y-m-d");
        $gestionadopor = Yii::$app->user->identity->id;
        $id_aprobado = Yii::$app->db->createCommand('select estado_novedad.id_gestor_evaluacion_estadonovedades AS id_estado_novedad FROM tbl_gestor_evaluacion_estadonovedades estado_novedad
        WHERE estado_novedad.nombre ="Aprobado" AND estado_novedad.anulado=0')->queryScalar();
        
        $filasAfectadas = Yii::$app->db->createCommand()
            ->update($nombre_tabla_novedad, [
            'id_estado_novedad' => $id_aprobado,
            'aprobado' => 1,
            'fecha_gestionado' => $fechaGestionado,
            'gestionadopor' => $gestionadopor
            ], 'id = :id', [':id' => $pk_tabla])->execute();

               
        if ($filasAfectadas === 1) {
            return 1; // Actualización exitosa
        } else {
            return 0; // Error al actualizar
        }
        
    }

    public function actualizar_novedad_no_aprobada($nombre_tabla_novedad, $pk_tabla){

        $fechaGestionado = date("Y-m-d");
        $gestionadopor = Yii::$app->user->identity->id;

        $id_no_aprobado = Yii::$app->db->createCommand('select estado_novedad.id_gestor_evaluacion_estadonovedades AS id_estado_novedad FROM tbl_gestor_evaluacion_estadonovedades estado_novedad
        WHERE estado_novedad.nombre ="No aprobado" AND estado_novedad.anulado=0')->queryScalar();
        
        $command = Yii::$app->db->createCommand()
            ->update($nombre_tabla_novedad, [
            'id_estado_novedad' => $id_no_aprobado,
            'aprobado' => 0,
            'fecha_gestionado' => $fechaGestionado,
            'gestionadopor' => $gestionadopor
            ], 'id = :id', [':id' => $pk_tabla]);
        
        $filasAfectadas = $command->execute();
        
        if ($filasAfectadas > 0) {
            return 1; // Actualización exitosa
        } else {
            return 0; // Error al actualizar
        }
        
    }

    public function actualizar_novedad_error($nombre_tabla_novedad, $pk_tabla){

        $fechaGestionado = date("Y-m-d");
        $gestionadopor = Yii::$app->user->identity->id;

        $id_error = Yii::$app->db->createCommand('select estado_novedad.id_gestor_evaluacion_estadonovedades AS id_estado_novedad FROM tbl_gestor_evaluacion_estadonovedades estado_novedad
        WHERE estado_novedad.nombre ="Error" AND estado_novedad.anulado=0')->queryScalar();
        
        $command = Yii::$app->db->createCommand()
            ->update($nombre_tabla_novedad, [
            'id_estado_novedad' => $id_error,
            'aprobado' => null,
            'fecha_gestionado' => $fechaGestionado,
            'gestionadopor' => $gestionadopor
            ], 'id = :id', [':id' => $pk_tabla]);
        
        $filasAfectadas = $command->execute();
        
        if ($filasAfectadas > 0) {
            return 1; // Actualización exitosa
        } else {
            return 0; // Error al actualizar
        }
        
    }


    //Obtiene el número total de calificaciones para evaluacion solo 2023 (Modificar consulta con la nueva tabla**)
    public function obtenerTotalEvaluacionesParaUnUsuario($id_user){
        $contadorEvaluaciones = 1; //autoevaluacion

        $esColaborador = (new \yii\db\Query())
        ->select([
            'u.es_colaborador'
        ])        
        ->from('tbl_gestor_evaluacion_usuarios u')
        ->where([
            'u.id_gestor_evaluacion_usuarios' => $id_user               
        ])
        ->scalar();

        if($esColaborador==1){            
            $contadorEvaluaciones++; //tambien tiene asociada una evaluacion del jefe
        }

        return $contadorEvaluaciones;
    }

    //FUNCION PARA OBTENER EL ID Y NOMBRE DE LAS EVALUACIONES ASOCIADAS AL PERIODO DE EVALUACION 2023
    public function obtenerTipoEvaluacionPorUsuario($id_user, $id_evalua_nom){

        $contadorEvaluaciones=1; //autoevaluacion

        $esColaborador = (new \yii\db\Query())
        ->select([
            'u.es_colaborador'
        ])        
        ->from('tbl_gestor_evaluacion_usuarios u')
        ->where([
            'u.id_gestor_evaluacion_usuarios' => $id_user               
        ])
        ->scalar();

        if($esColaborador==1){            
            $contadorEvaluaciones++; //tambien tiene asociada una evaluacion del jefe
        }

        return $contadorEvaluaciones;
    }

    public function existen_formularios_asociados_a_un_usuario($id_user, $id_tipo_evalua, $id_evalua_nombre){
      
        $query = (new Query())
            ->select([
                'COUNT(id_gestor_evaluacion_formulario) as cant_registros',
                'fechacreacion',
            ])
            ->from('tbl_gestor_evaluacion_formulario')
            ->where([
                'id_evaluado' => $id_user,
                'id_tipo_evalua' => $id_tipo_evalua,
                'id_evaluacionnombre' => $id_evalua_nombre,
                'anulado' => 0,
            ]);
        
        // Obtener el resultado de la consulta
        $result = $query->one();
        
        // Verificar si se encontraron registros y obtener la fecha de creación del formulario
        if ($result) {
            $cant_registros = $result['cant_registros'];
            $fechaCreacion = $result['fechacreacion'];
        } else {
            // Si no se encontraron registros, asignar valores predeterminados o manejar el caso
            $cant_registros = 0;
            $fechaCreacion = null;
        }

        return $result;

    }

    //FUNCION QUE RETORNA LOS TIPOS DE EVALUACIONES ASOCIADAS A UN PERIODO DE TIEMPO DEFINIDO EN LA TABLA tbl_evaluacion_nombre
    public function obtener_tipo_evaluacion_por_periodo($id_evaluacion_nombre){
        
        $id_evaluacion_nombres = $id_evaluacion_nombre;

        $query = new \yii\db\Query();
        $query->select('tipo.idevaluaciontipo AS id_tipoeval, tipo.tipoevaluacion AS nom_tipoeval')
            ->from('tbl_gestor_evaluacion_nombre_tipoeval evaluacion')
            ->innerJoin('tbl_evaluacion_tipoeval tipo', 'evaluacion.id_evaluacion_tipoeval = tipo.idevaluaciontipo')
            ->where(['in', 'evaluacion.id_evaluacion_nombre', $id_evaluacion_nombres]);

        $result = $query->all();
        
        return $result;

    }

    //Obtener data del formulario ingresada en las diferentes evaluaciones realizadas del evaluado
    public function obtenerDataTipoEvaluacion($id_tipo_eval, $id_evaluado){

        $id_user_evaluado = $id_evaluado;
        $id_tipo_evaluacion = $id_tipo_eval;
       
        $result = (new \yii\db\Query())
        ->select([
            'tipoeval.idevaluaciontipo AS id_tipoeval',
            'tipoeval.tipoevaluacion AS nombre_tipoeval',
            'usuario.nombre_completo',
            'usuario.identificacion',
            'dataform.cargo',
            'IF(tipoeval.tipoevaluacion = "A cargo", form.id_evaluador, null) AS id_jefe',
            'dataform.nom_jefe',
            'form.fechacreacion'
        ])
        ->from('tbl_gestor_evaluacion_formulario form')
        ->rightJoin('tbl_gestor_evaluacion_datosform dataform', 'form.id_gestor_evaluacion_formulario = dataform.id_gestor_evaluacion_formulario')
        ->innerJoin('tbl_gestor_evaluacion_usuarios usuario', 'form.id_evaluado = usuario.id_gestor_evaluacion_usuarios')
        ->innerJoin('tbl_evaluacion_tipoeval tipoeval', 'form.id_tipo_evalua = tipoeval.idevaluaciontipo')
        ->innerJoin('tbl_evaluacion_nombre nombreval', 'form.id_evaluacionnombre = nombreval.idevaluacionnombre')
        ->where(['form.anulado' => 0])
        ->andWhere(['in', 'form.id_tipo_evalua', $id_tipo_evaluacion])
        ->andWhere(['in', 'form.id_evaluado', $id_user_evaluado])
        ->all();
            
        return $result;

    }

    //FUNCION: consulta el valor de las respuestas segun su id_rtas
    //retorna la suma, cantidad de rtas, promedio y actualiza el formulario asociado a pk_evaluacion
    public function crearCalificacionPorEvaluacion($id_rtas, $pk_evaluacion){

        //obtengo el valor numerico de las respuestas ingresadas para calcular la suma, cantidad_rtas y promedio 
        $query_valor_rtas = (new \yii\db\Query())
        ->select([
            'SUM(rta.valornumerico_respuesta) AS suma_rtas',
            'COUNT(rtasform.id_respuesta) AS cant_rtas',
            'ROUND(AVG(rta.valornumerico_respuesta), 2) AS promedio_rtas'
        ])
        ->from('tbl_gestor_evaluacion_respuestasform rtasform')
        ->innerJoin('tbl_gestor_evaluacion_respuestas rta', 'rta.id_gestorevaluacionrespuestas = rtasform.id_respuesta')
        ->innerJoin('tbl_gestor_evaluacion_formulario form', 'form.id_gestor_evaluacion_formulario = rtasform.id_gestor_evaluacion_formulario')
        ->where([
            'rtasform.id_gestor_evaluacion_formulario' => $pk_evaluacion,
            'rtasform.id_respuesta' => $id_rtas
        ]);
       
        $result = $query_valor_rtas->one();

        $suma_rtas = $result['suma_rtas'];
        $cant_rtas = $result['cant_rtas'];
        $promedio_rtas = $result['promedio_rtas'];

        //guardo sus valores asociandolos a su respectivo id del formulario
        $actualizar_puntajes = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_formulario',[
            'suma_respuestas' => $suma_rtas,
            'promedio_final' => $promedio_rtas,
        ],'id_gestor_evaluacion_formulario ='.$pk_evaluacion.'')->execute();

        if ($actualizar_puntajes==1) {
            return 1; // exitoso           
        } else {
            die( json_encode( array("status"=>"error","data"=>"Error actualizando los puntajes en la evaluacion con id: " . $pk_evaluacion ) ) );
        }
    }

    //Crea un registro en la tabla tbl_gestor_evaluacion_calificaciontotal y retorna el Pk 
    public function crearCalificacionTotal($id_user, $id_evaluac_nom){
        $pk_calificacion_total = "";
        
        $calificacion_total = $this->calcularCalificacionDeLaEvaluacion($id_user, $id_evaluac_nom);
          
            if(!empty($calificacion_total)){
                $model_calificacion_total = new GestorEvaluacionCalificacionTotal();
                $model_calificacion_total->id_evalua_nombre = $id_evaluac_nom;
                $model_calificacion_total->id_evaluado = $id_user;
                $model_calificacion_total->suma_total_evalua = $calificacion_total['suma_total'];
                $model_calificacion_total->promedio_total_evalua = $calificacion_total['promedio_total'];
                $model_calificacion_total->cant_evaluaciones = $calificacion_total['cant_evaluaciones'];
                $model_calificacion_total->fechacreacion = date("Y-m-d");
                $model_calificacion_total->usua_id = Yii::$app->user->identity->id;
                
                if ( $model_calificacion_total->save() ){
                    $pk_calificacion_total = $model_calificacion_total->id_gestor_evaluacion_calificaciontotal;                      
                } 
                
            }
        
        return $pk_calificacion_total;        
    }

    //Calcula el promedio total teniendo en cuenta la cantidad de tipo de evaluacion asociadas al usuario
    public function calcularCalificacionDeLaEvaluacion($id_evaluado, $id_evaluac_nom) {

        $response = [];        

        if( isset($id_evaluado) && !empty($id_evaluado) && isset($id_evaluac_nom) && !empty($id_evaluac_nom) ){
            $calcular_prom = (new \yii\db\Query())
            ->select([
                'form.id_evaluado', 
                'AVG(form.promedio_final) AS promedio_total',
                'SUM(form.suma_respuestas) AS suma_total',
                'COUNT(DISTINCT form.id_tipo_evalua) AS cant_evaluaciones'
            ])        
            ->from('tbl_gestor_evaluacion_formulario form')
            ->where([
                'form.anulado' => 0,
                'form.id_evaluacionnombre' => $id_evaluac_nom,
                'form.id_evaluado' => $id_evaluado                
            ]);

            $result = $calcular_prom->one();

            if ($result !== false) {
                $response = $result;
            } 
        }
        
        return $response;
    }

    //Funcion para verificar estado de Completado en TODOS los tipos de evaluaciones asociadas a un periodo de evaluacion
    //Se usa para saber si debe realizar o no el calculo total de cada competencia y el general de la evaluacion
    public function verificarEstadoEvaluaciones($id_evaluado, $id_evalua_nom) {
     
        $evaluaciones_completadas = false;

        if( isset($id_evaluado) && !empty($id_evaluado) && isset($id_evalua_nom) && !empty($id_evalua_nom) ){
    
            $existe = (new \yii\db\Query())
            ->select([
                'form.id_gestor_evaluacion_formulario AS id_formulario',
                'form.id_estado_evaluacion',
            ])        
            ->from('tbl_gestor_evaluacion_formulario form')
            ->where([
                'form.id_evaluacionnombre' => $id_evalua_nom,
                'form.id_evaluado' => $id_evaluado
            ]);

            $result = $existe->all();
            $numResultados = count($result);
     
            $cantidad_evaluaciones = $this->obtenerTotalEvaluacionesParaUnUsuario($id_evaluado);
            
            if (!empty($result) && $numResultados==$cantidad_evaluaciones) {
                $id_estado_evaluacion = array_column($result, 'id_estado_evaluacion');                
                if (!in_array(1, $id_estado_evaluacion)) {
                    $evaluaciones_completadas = false;
                }
                $evaluaciones_completadas= true;
            }
        }

        return $evaluaciones_completadas;

    }

    public function obtenerTodasLasPersonasAcargo($id_jefe){

        $query = Yii::$app->db->createCommand('
            SELECT colaborador.id_gestor_evaluacion_usuarios AS id_colaborador,
                colaborador.nombre_completo AS nom_colaborador,
                colaborador.identificacion AS cc_colaborador,
                colaborador.cargo,
                colaborador.area_operacion,
                colaborador.ciudad,
                colaborador.sociedad,
                jefe.nombre_completo AS nom_jefe
            FROM tbl_gestor_evaluacion_jefe_colaborador jefe_x_colaborador
            INNER JOIN tbl_gestor_evaluacion_usuarios jefe ON jefe_x_colaborador.id_usuario_jefe = jefe.id_gestor_evaluacion_usuarios
            INNER JOIN tbl_gestor_evaluacion_usuarios colaborador ON jefe_x_colaborador.id_usuario_colaborador = colaborador.id_gestor_evaluacion_usuarios
            WHERE jefe_x_colaborador.anulado = 0 AND jefe_x_colaborador.id_usuario_jefe = :idJefe
        ');
        $query->bindParam(':idJefe', $id_jefe);
        $result = $query->queryAll();

        return $result;

    }

    public function obtenerPersonasAcargoSinEvaluar($id_jefe){

        $query = "SELECT u.id_gestor_evaluacion_usuarios AS id_colaborador, 
        u.nombre_completo, u.identificacion, form.id_estado_evaluacion
        FROM tbl_gestor_evaluacion_jefe_colaborador relacion
        INNER JOIN tbl_gestor_evaluacion_usuarios u
        ON relacion.id_usuario_colaborador = u.id_gestor_evaluacion_usuarios 
        LEFT JOIN tbl_gestor_evaluacion_formulario form
        ON relacion.id_usuario_colaborador = form.id_evaluado && relacion.id_usuario_jefe = form.id_evaluador
        WHERE relacion.anulado=0 AND form.id_estado_evaluacion IS NULL AND relacion.id_usuario_jefe = :id_usuario_jefe";

        $result = Yii::$app->db->createCommand($query)
        ->bindValue(':id_usuario_jefe', $id_jefe)
        ->queryAll();

        return $result;

    }

    public function calcularCalificacionTotalPorCompetencia($id_evaluado, $id_evalua_nom, $pk_calificacion_total) {

        $query = Yii::$app->db->createCommand('
            SELECT
                form.id_evaluado,
                rtasform.id_pregunta,
                pregunta.nombrepregunta,
                SUM(rta.valornumerico_respuesta) AS suma_rtas_competencia,
                COUNT(DISTINCT form.id_tipo_evalua) AS cant_evaluaciones,
                ROUND(AVG(rta.valornumerico_respuesta), 2) AS prom_competencia
            FROM
                tbl_gestor_evaluacion_formulario form
                INNER JOIN tbl_gestor_evaluacion_respuestasform rtasform ON form.id_gestor_evaluacion_formulario = rtasform.id_gestor_evaluacion_formulario
                INNER JOIN tbl_gestor_evaluacion_preguntas pregunta ON rtasform.id_pregunta = pregunta.id_gestorevaluacionpreguntas
                INNER JOIN tbl_gestor_evaluacion_respuestas rta ON rtasform.id_respuesta = rta.id_gestorevaluacionrespuestas
            WHERE
                form.anulado = 0 AND
                form.id_evaluacionnombre = :id_evaluacionnombre AND
                form.id_evaluado = :id_evaluado
            GROUP BY
                rtasform.id_pregunta
        ');
        $query->bindValue(':id_evaluacionnombre', $id_evalua_nom);
        $query->bindValue(':id_evaluado', $id_evaluado);
        $result = $query->queryAll();

        if(!empty($result)){

            foreach ($result as $datos) {
                $id_pregunta = $datos['id_pregunta'];
                $suma_rtas_competencia = $datos['suma_rtas_competencia'];
                $cantidad_evaluaciones = $datos['cant_evaluaciones']; 
                $prom_total_por_pregunta = $datos['prom_competencia']; 

                $model_calificac_por_competencia = new GestorEvaluacionCalificaPorPregunta();                           
                $model_calificac_por_competencia->id_calificaciontotal = $pk_calificacion_total;
                $model_calificac_por_competencia->id_pregunta = $id_pregunta;
                $model_calificac_por_competencia->suma_total_por_pregunta = $suma_rtas_competencia;
                $model_calificac_por_competencia->cantidad_evaluaciones = $cantidad_evaluaciones;         
                $model_calificac_por_competencia->prom_total_por_pregunta = $prom_total_por_pregunta;         
                $model_calificac_por_competencia->fechacreacion = date("Y-m-d");
                $model_calificac_por_competencia->usua_id = Yii::$app->user->identity->id;
                $model_calificac_por_competencia->save();

            }
        
            // respuesta exitosa en formato JSON
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'status' => 'success',
                'message' => 'La calificación total de cada competencia se creó correctamente.',
            ];

        } else {
           // respuesta de error en formato JSON
            Yii::$app->response->format = Response::FORMAT_JSON;
            return [
                'status' => 'error',
                'message' => 'Error creando la calificación total de cada competencia.',
            ];
        }
    }


    //Retorna informacion de los usuarios con su promedio total de la evaluacion segun su periodo (evaluacion nombre)
    public function obtenerCalificacionTotalporUsuarios($id_evaluac_nombre, $id_users){
        $query = new Query();
        $query->select([
                'usuario.id_gestor_evaluacion_usuarios AS id_user',
                'usuario.nombre_completo',
                'usuario.identificacion',
                'total.promedio_total_evalua',
                'entrada_colab.id_remitente',
                'entrada_colab.id_destinatario'
            ])
            ->from('tbl_gestor_evaluacion_calificaciontotal total')
            ->leftJoin('tbl_gestor_evaluacion_feedback acuerdo', 'total.id_feedback = acuerdo.id_gestor_evaluacion_feedback')
            ->leftJoin('tbl_gestor_evaluacion_feedbackentradas entrada_colab', 'acuerdo.id_gestor_evaluacion_feedback = entrada_colab.id_feedback AND acuerdo.id_jefe = entrada_colab.id_remitente')
            ->innerJoin('tbl_gestor_evaluacion_usuarios usuario', 'total.id_evaluado = usuario.id_gestor_evaluacion_usuarios')
            ->where([
                'total.anulado' => 0,
                'total.id_evalua_nombre' => $id_evaluac_nombre,
            ])
            ->andWhere(['IN', 'total.id_evaluado', $id_users])
            ->groupBy('usuario.id_gestor_evaluacion_usuarios');

        $result = $query->all();

        return $result;
    }

    public function existe_registros_calificacion_total_por_evaluacion($id_user, $id_evalua_nombre){

        $response = false;

        $query = (new \yii\db\Query())
            ->select('COUNT(c.id_gestor_evaluacion_calificaciontotal) AS cant_registros')
            ->from('tbl_gestor_evaluacion_calificaciontotal c')
            ->where(['c.anulado' => '0',
            'c.id_evalua_nombre' => $id_evalua_nombre,
            'c.id_evaluado'=>$id_user])
            ->scalar(); 

        if($query==1){
            $response = true;
        }

        return $response;
    }

    public function getValorPorCompetenciaUnUsuario($id_evaluado, $id_evalua_nombre){

        $query = Yii::$app->db->createCommand('
            SELECT 
                total.id_evaluado, colaborador.nombre_completo, colaborador.identificacion,
                pregunta.nombrepregunta AS competencia,
                pregunta.descripcionpregunta AS descripcion_competencia, 
                com.prom_total_por_pregunta AS calificacion_competencia,      
                CASE
                    WHEN com.prom_total_por_pregunta BETWEEN 1 AND 1.5 THEN "La competencia esta en un nivel basico de desarrollo"
                    WHEN com.prom_total_por_pregunta BETWEEN 1.6 AND 2.5 THEN "La competencia esta en un nivel satisfactorio de desarrollo"
                    WHEN com.prom_total_por_pregunta BETWEEN 2.6 AND 3 THEN "La competencia esta en un nivel esperado y potencial de desarrollo"
                    ELSE "Sin descripción"
                END AS descripcion_respuesta,
                total.promedio_total_evalua AS prom_total_evaluacion,
                total.suma_total_evalua AS suma_total_evaluacion
            FROM tbl_gestor_evaluacion_calificaciontotal total
            INNER JOIN tbl_gestor_evaluacion_calificaporpregunta com ON total.id_gestor_evaluacion_calificaciontotal = com.id_calificaciontotal
            INNER JOIN tbl_gestor_evaluacion_preguntas pregunta ON com.id_pregunta = pregunta.id_gestorevaluacionpreguntas
            LEFT JOIN tbl_gestor_evaluacion_usuarios colaborador ON total.id_evaluado = colaborador.id_gestor_evaluacion_usuarios
            WHERE total.anulado = 0 AND total.id_evalua_nombre = :id_evalua_nombre AND total.id_evaluado = (:id_evaluado)
        ');

        $query->bindValue(':id_evaluado', $id_evaluado);
        $query->bindValue(':id_evalua_nombre', $id_evalua_nombre);
        $result = $query->queryAll();

        return $result;

    }


    public function getValorPorCompetenciaVariosUsuarios($id_evaluados, $id_evalua_nombre){

        // Convertir el array en una cadena de valores separados por comas
        $id_evaluados_str = implode(',', $id_evaluados);

        // Construir la consulta SQL con los marcadores de posición
        $sql = "SELECT 
                total.id_evaluado, colaborador.nombre_completo, colaborador.identificacion,
                pregunta.nombrepregunta AS competencia,
                pregunta.descripcionpregunta AS descripcion_competencia, 
                com.prom_total_por_pregunta AS calificacion_competencia,      
                CASE
                WHEN com.prom_total_por_pregunta BETWEEN 1 AND 1.5 THEN 'La competencia esta en un nivel basico de desarrollo'
                WHEN com.prom_total_por_pregunta BETWEEN 1.6 AND 2.5 THEN 'La competencia esta en un nivel satisfactorio de desarrollo'
                WHEN com.prom_total_por_pregunta BETWEEN 2.6 AND 3 THEN 'La competencia esta en un nivel esperado y potencial de desarrollo'
                ELSE 'Sin descripción'
                END AS descripcion_respuesta,
                total.promedio_total_evalua AS prom_total_evaluacion,
                total.suma_total_evalua AS suma_total_evaluacion
                FROM tbl_gestor_evaluacion_calificaciontotal total
                INNER JOIN tbl_gestor_evaluacion_calificaporpregunta com
                    ON total.id_gestor_evaluacion_calificaciontotal = com.id_calificaciontotal
                INNER JOIN tbl_gestor_evaluacion_preguntas pregunta
                    ON com.id_pregunta = pregunta.id_gestorevaluacionpreguntas
                LEFT JOIN tbl_gestor_evaluacion_usuarios colaborador
                    ON total.id_evaluado = colaborador.id_gestor_evaluacion_usuarios
                WHERE total.anulado = 0 AND total.id_evalua_nombre = :id_evalua_nombre AND total.id_evaluado IN ($id_evaluados_str)";

        // Ejecutar la consulta
        $query = Yii::$app->db->createCommand($sql);
        $query->bindValue(':id_evalua_nombre', $id_evalua_nombre);
        $result = $query->queryAll();

        return $result;
    }

    // SECCION FEEDBACK --------------------------------------------------------------------------------------- 
    
    //Obtiene el PK del feedback y el PK de la calificacion total de un usaurio en una periodo de evaluacion 
    public function obtener_feedback_usuario($id_evalua_nom, $id_user){        

        $result = (new \yii\db\Query())
        ->select(['total.id_feedback', 'total.id_gestor_evaluacion_calificaciontotal AS id_calificacion_total'])
        ->from('tbl_gestor_evaluacion_calificaciontotal total')
        ->where([
            'total.id_evalua_nombre' => $id_evalua_nom,
            'total.id_evaluado' => $id_user
        ])
        ->one();

        return $result;

    }

    //Obtiene los feedback del colaborador, del jefe y el acuerdo final asociado a un usuario en un periodo de evaluacion
    public function obtener_feedbacks_por_usuario($id_evalua_nom, $id_usuario){
        $query = (new \yii\db\Query())
        ->select([
            'feedback.id_remitente AS id_remitente',
            'feedback.id_destinatario AS id_destinatario',
            'remitente.nombre_completo AS nom_remitente',
            'destinatario.nombre_completo AS nom_destinatario',
            'remitente.identificacion AS cc_remitente',
            'destinatario.identificacion AS cc_destinatario',
            'feedback.comentario',
            'acuerdo.id_gestor_evaluacion_feedback AS id_acuerdo_final',
            'acuerdo.comentario AS acuerdo_final',
        ])
        ->from('tbl_gestor_evaluacion_feedbackentradas feedback')
        ->leftJoin('tbl_gestor_evaluacion_usuarios remitente', 'feedback.id_remitente = remitente.id_gestor_evaluacion_usuarios')
        ->leftJoin('tbl_gestor_evaluacion_usuarios destinatario', 'feedback.id_destinatario = destinatario.id_gestor_evaluacion_usuarios')
        ->leftJoin('tbl_gestor_evaluacion_feedback acuerdo', 'acuerdo.id_gestor_evaluacion_feedback = feedback.id_feedback')
        ->leftJoin('tbl_gestor_evaluacion_calificaciontotal total', 'acuerdo.id_calificaciontotal = total.id_gestor_evaluacion_calificaciontotal')
        ->where([
            'total.anulado' => 0,
            'total.id_evalua_nombre' => $id_evalua_nom,
        ])
        ->andWhere(['OR', ['feedback.id_remitente' => $id_usuario], ['feedback.id_destinatario' => $id_usuario]]);

        $result = $query->all();  
        
        return $result;
    }

    //FUNCIONES PARA NOVEDADES DE LA EVALUACION -----------------------------------------------------------
    
    //Funcion para obtener el nombre del estado de la novedad de un usuario existente en una determinada evaluacion 
    public function obtenerNombreEstadoNovedad($documento_usuario, $id_evaluacion_nombre){
        
        $query = new Query();
        $estado = $query->select('estado.nombre')
          ->from('tbl_gestor_evaluacion_novedad_jefeincorrecto novedad')
          ->innerJoin('tbl_gestor_evaluacion_estadonovedades estado', 'novedad.id_estado_novedad = estado.id_gestor_evaluacion_estadonovedades')
          ->where([
            'novedad.cc_colaborador' => $documento_usuario,
            'novedad.id_evaluacion_nombre' => $id_evaluacion_nombre,
            'novedad.anulado' => 0
          ])
          ->scalar();

          return $estado;

    } 

    //verificar si ya realizo la evaluacion "Completado" para habilitar o no el boton.
    
    public function verificarEstadoCompletadoDeUnTipoDeEvaluacion($id_evaluado, $id_tipo_eval, $id_evaluacion_nombre){
        $estado_completado_evaluacion= 0;

        $sql = "
        SELECT estadoeval.estado
        FROM tbl_gestor_evaluacion_formulario form
        INNER JOIN tbl_gestor_evaluacion_estadoeval estadoeval ON form.id_estado_evaluacion = estadoeval.id_gestor_evaluacion_estadoeval
        WHERE form.id_evaluado = :idEvaluado AND form.id_tipo_evalua = :idTipoEvalua AND form.id_evaluacionnombre = :idEvaluacionNombre AND form.anulado = 0
        ";

        $estado = Yii::$app->db->createCommand($sql)
            ->bindValues([
                ':idEvaluado' => $id_evaluado,
                ':idTipoEvalua' => $id_tipo_eval,
                ':idEvaluacionNombre' => $id_evaluacion_nombre
            ])
            ->queryScalar();

        //Si no tiene resultados o si esta en esta incompleto retornara 0, si existen resultados y es Completado retorna 1
        if( !empty($estado) ){
            $estado_completado_evaluacion = $estado=="Completado" ? 1 : 0;
        }

        return $estado_completado_evaluacion;

    }
    
    public function obtener_data_jefe_incorrecto($id_evaluacion_nombre){
        $sql = "SELECT
        tabla.id AS id_novedad,
        tabla.fechacreacion,
        nom_eval.nombreeval AS nombre_evaluacion,
        usuario.nombre_completo AS solicitante,
        tabla.cc_colaborador,
        tabla.cc_jefe_actual,
        tabla.cc_jefe_correcto,
        tabla.comentarios_solicitud,
        estado.nombre AS estado,
        tabla.aprobado,
        tabla.comentarios_no_aprobado
        FROM
        tbl_gestor_evaluacion_novedad_jefeincorrecto tabla
        LEFT JOIN tbl_gestor_evaluacion_usuarios usuario ON tabla.cc_colaborador = usuario.identificacion
        LEFT JOIN tbl_evaluacion_nombre nom_eval ON tabla.id_evaluacion_nombre = nom_eval.idevaluacionnombre
        LEFT JOIN tbl_gestor_evaluacion_estadonovedades estado ON tabla.id_estado_novedad = estado.id_gestor_evaluacion_estadonovedades
        WHERE
        tabla.id_evaluacion_nombre = :id_evaluacion_nombre";

        $comando = Yii::$app->db->createCommand($sql);
        $comando->bindValue(':id_evaluacion_nombre', $id_evaluacion_nombre);
        $resultado = $comando->queryAll();

        return $resultado;
    }

    public function obtener_data_jefe_incorrecto_por_id($id_novedad){
        
        $result = Yii::$app->db->createCommand("
          SELECT novedad.id_solicitante, novedad.id_jefe_actual, novedad.cc_colaborador, novedad.cc_jefe_correcto, novedad.id_evaluacion_nombre
          FROM tbl_gestor_evaluacion_novedad_jefeincorrecto novedad
          WHERE novedad.id = :idNovedad AND novedad.anulado = 0
        ")
        ->bindValue(':idNovedad', $id_novedad)
        ->queryOne();

        return $result;
    }

    public function obtener_data_personal_a_cargo($id_evaluacion_nombre){
    
        $command = Yii::$app->db->createCommand('
        SELECT
            tabla.id AS id_novedad,
            tabla.fechacreacion,
            nom_eval.nombreeval AS nombre_evaluacion,
            tipo_novedad.nombre_tipo_novedad,
            usuario.nombre_completo AS solicitante,
            colaborador_actual.nombre_completo AS colaborador_actual,
            tabla.cc_colaborador_nuevo,
            tabla.comentarios_solicitud,
            estado.nombre AS estado,
            tabla.aprobado,
            tabla.comentarios_no_aprobado
        FROM
            tbl_gestor_evaluacion_novedad_jefecolaborador tabla
            LEFT JOIN tbl_gestor_evaluacion_usuarios usuario ON tabla.id_jefe_solicitante = usuario.id_gestor_evaluacion_usuarios
            LEFT JOIN tbl_gestor_evaluacion_usuarios colaborador_actual ON tabla.id_colaborador_actual = colaborador_actual.id_gestor_evaluacion_usuarios
            LEFT JOIN tbl_gestor_evaluacion_tiponovedadjefecolaborador tipo_novedad ON tabla.id_tipo_novedad = tipo_novedad.id_gestor_evaluacion_tiponovedad_jefecolaborador
            LEFT JOIN tbl_evaluacion_nombre nom_eval ON tabla.id_evaluacion_nombre = nom_eval.idevaluacionnombre
            LEFT JOIN tbl_gestor_evaluacion_estadonovedades estado ON tabla.id_estado_novedad = estado.id_gestor_evaluacion_estadonovedades
        WHERE
            tabla.id_evaluacion_nombre = :evaluacion_nombre AND tabla.anulado = 0
        ');
        $command->bindValue(':evaluacion_nombre', $id_evaluacion_nombre);

        $result = $command->queryAll();

        return $result;
    }

    public function obtener_data_personal_a_cargo_por_pk($id_novedad){
        
        $result = Yii::$app->db->createCommand("
        SELECT tipo_novedad.nombre_tipo_novedad, novedad.id_jefe_solicitante,
        novedad.id_colaborador_actual, novedad.cc_colaborador_actual, novedad.cc_colaborador_nuevo
        FROM tbl_gestor_evaluacion_novedad_jefecolaborador novedad
        INNER JOIN tbl_gestor_evaluacion_tiponovedadjefecolaborador tipo_novedad
        ON novedad.id_tipo_novedad = tipo_novedad.id_gestor_evaluacion_tiponovedad_jefecolaborador
        WHERE novedad.id= :idNovedad AND novedad.anulado = 0
        ")
        ->bindValue(':idNovedad', $id_novedad)
        ->queryOne();

        return $result;
    }

    public function obtener_data_eliminarevaluacion($id_evaluacion_nombre){
    
        $command = Yii::$app->db->createCommand('
        SELECT
        tabla.id AS id_novedad,
        tabla.fechacreacion,
        nom_eval.nombreeval AS nombre_evaluacion,
        usuario.nombre_completo AS solicitante,
        tabla.cc_solicitante,
        tipoeval.tipoevaluacion,
        tabla.cc_evaluado,
        tabla.comentarios_solicitud,
        estado.nombre AS estado,
        tabla.aprobado,
        tabla.comentarios_no_aprobado
        FROM
        tbl_gestor_evaluacion_novedad_eliminareval tabla
        LEFT JOIN tbl_evaluacion_tipoeval tipoeval ON tabla.id_tipo_evaluacion = tipoeval.idevaluaciontipo
        LEFT JOIN tbl_gestor_evaluacion_usuarios usuario ON tabla.id_solicitante = usuario.id_gestor_evaluacion_usuarios                       
        LEFT JOIN tbl_evaluacion_nombre nom_eval ON tabla.id_evaluacion_nombre = nom_eval.idevaluacionnombre
        LEFT JOIN tbl_gestor_evaluacion_estadonovedades estado ON tabla.id_estado_novedad = estado.id_gestor_evaluacion_estadonovedades
        WHERE
        tabla.id_evaluacion_nombre = :evaluacion_nombre AND tabla.anulado = 0
        ');
        $command->bindValue(':evaluacion_nombre', $id_evaluacion_nombre);

        $result = $command->queryAll();

        return $result;
    }

    public function obtener_data_eliminarevaluacion_por_id($id_novedad){
        
        $result = Yii::$app->db->createCommand("
        SELECT novedad.id_evaluacion_nombre, novedad.id_solicitante,
        novedad.id_tipo_evaluacion, novedad.id_evaluado
        FROM tbl_gestor_evaluacion_novedad_eliminareval novedad
        WHERE novedad.id = :idNovedad AND novedad.anulado = 0
        ")
        ->bindValue(':idNovedad', $id_novedad)
        ->queryOne();

        return $result;
    }

    public function obtener_data_otros_inconvenientes($id_evaluacion_nombre){
    
        $command = Yii::$app->db->createCommand('
        SELECT
        tabla.id AS id_novedad,
        tabla.fechacreacion,
        nom_eval.nombreeval AS nombre_evaluacion,
        usuario.nombre_completo AS solicitante,
        tabla.cc_solicitante,
        tabla.comentarios_solicitud,
        estado.nombre AS estado,
        tabla.aprobado,
        tabla.comentarios_no_aprobado
        FROM tbl_gestor_evaluacion_novedad_general tabla
        LEFT JOIN tbl_evaluacion_tipoeval tipoeval ON tabla.id_tipo_evaluacion = tipoeval.idevaluaciontipo
        LEFT JOIN tbl_gestor_evaluacion_usuarios usuario ON tabla.id_solicitante = usuario.id_gestor_evaluacion_usuarios                       
        LEFT JOIN tbl_evaluacion_nombre nom_eval ON tabla.id_evaluacion_nombre = nom_eval.idevaluacionnombre
        LEFT JOIN tbl_gestor_evaluacion_estadonovedades estado ON tabla.id_estado_novedad = estado.id_gestor_evaluacion_estadonovedades
        WHERE
        tabla.id_evaluacion_nombre = :evaluacion_nombre AND tabla.anulado = 0
        ');
        $command->bindValue(':evaluacion_nombre', $id_evaluacion_nombre);

        $result = $command->queryAll();

        return $result;
    }

    //Obtener si existe una novedad por eliminacion de autoevaluacion en estado "En espera"
    public function verificar_estado_enespera_autoevaluacion($id_evalua_nombre, $id_tipo_eval, $id_solicitante, $id_estado_nov){

        $result = Yii::$app->db->createCommand('SELECT novedad.id
        FROM tbl_gestor_evaluacion_novedad_eliminareval novedad
        WHERE novedad.id_evaluacion_nombre=:id_evaluacion_nombre
        AND novedad.id_tipo_evaluacion=:id_tipo_evaluacion
        AND novedad.id_solicitante=:id_solicitante
        AND novedad.id_estado_novedad=:id_estado_novedad')
        ->bindValue(':id_evaluacion_nombre', $id_evalua_nombre)
        ->bindValue(':id_tipo_evaluacion', $id_tipo_eval)
        ->bindValue(':id_solicitante', $id_solicitante)
        ->bindValue(':id_estado_novedad', $id_estado_nov)
        ->queryScalar();

        return $result;

    }

    //Obtener el estado de una novedad por eliminacion de a cargo 
    public function verificar_estado_novedad_evalua_a_cargo($id_evalua_nombre, $id_tipo_eval, $id_solicitante, $id_evaluado, $id_estado_novedad){

        $result = Yii::$app->db->createCommand('SELECT novedad.id
        FROM tbl_gestor_evaluacion_novedad_eliminareval novedad
        WHERE novedad.id_evaluacion_nombre=:id_evaluacion_nombre
        AND novedad.id_tipo_evaluacion=:id_tipo_evaluacion
        AND novedad.id_solicitante=:id_solicitante
        AND novedad.id_evaluado=:id_evaluado
        AND novedad.id_estado_novedad=:id_estado_novedad')
        ->bindValue(':id_evaluacion_nombre', $id_evalua_nombre)
        ->bindValue(':id_tipo_evaluacion', $id_tipo_eval)
        ->bindValue(':id_solicitante', $id_solicitante)
        ->bindValue(':id_evaluado', $id_evaluado)
        ->bindValue(':id_estado_novedad', $id_estado_novedad)
        ->queryScalar();

        return $result;

    }

    public function eliminarevaluacion($id_evalua_nombre, $id_tipo_eval, $id_evaluado){
        
        $filasEliminadas = Yii::$app->db->createCommand()->delete('tbl_gestor_evaluacion_formulario', [
            'id_evaluacionnombre' => $id_evalua_nombre,
            'id_tipo_evalua' => $id_tipo_eval,
            'id_evaluado' => $id_evaluado,
        ])->execute();

        return $filasEliminadas;
    }

    public function obtener_data_feedbacks_por_jefe($id_jefe, $id_evaluacion_nombre) {
        
        $query = (new Query())
        ->select([
            'acuerdo.id_gestor_evaluacion_feedback AS id_acuerdo',
            'colab.nombre_completo',
            'colab.identificacion',
            'cal_total.promedio_total_evalua AS nota_final',
            'f_colab.comentario AS feedback_colaborador',
            'f_jefe.comentario AS feedback_jefe',
            'acuerdo.comentario AS acuerdo_final'
        ])
        ->from('tbl_gestor_evaluacion_calificaciontotal cal_total')
        ->innerJoin('tbl_gestor_evaluacion_feedback acuerdo', 'cal_total.id_feedback = acuerdo.id_gestor_evaluacion_feedback')
        ->innerJoin('tbl_gestor_evaluacion_feedbackentradas f_colab', 'acuerdo.id_gestor_evaluacion_feedback = f_colab.id_feedback')
        ->innerJoin('tbl_gestor_evaluacion_feedbackentradas f_jefe', 'acuerdo.id_gestor_evaluacion_feedback = f_jefe.id_feedback')
        ->innerJoin('tbl_gestor_evaluacion_usuarios colab', 'colab.id_gestor_evaluacion_usuarios = f_colab.id_remitente')
        ->where(['f_colab.id_destinatario' => $id_jefe])
        ->andWhere(['f_jefe.id_remitente' => $id_jefe])
        ->andWhere(['cal_total.id_evalua_nombre' => $id_evaluacion_nombre])
        ->groupBy('f_colab.id_feedback');

        $results = $query->all();

        return $results;
        
    }

    public function eliminar_logicamente_jefe_x_colaborador($pk_a_eliminar) {
        
        $filas_afectadas = Yii::$app->db->createCommand()->update('tbl_gestor_evaluacion_jefe_colaborador',[
            'anulado' => 1,
        ],'id_gestor_evaluacion_jefe_colaborador ='.$pk_a_eliminar.'')->execute();

        if ($filas_afectadas === 1) {
            return 1; // Actualización exitosa
        } else {
            return 0; // Ocurrió un error en la actualizacion
        }

    }

    public function contar_registros_novedad_jefe_incorrecto($id_usuario, $cc_usuario){

        $cantidad_registros = Yii::$app->db->createCommand("
            SELECT COUNT(novedad.id) AS cantidad_registros
            FROM tbl_gestor_evaluacion_novedad_jefeincorrecto novedad
            INNER JOIN tbl_gestor_evaluacion_estadonovedades estado
            ON novedad.id_estado_novedad = estado.id_gestor_evaluacion_estadonovedades
            WHERE estado.nombre = 'En espera'
            AND (novedad.id_solicitante = :id_solicitante OR novedad.cc_colaborador = :cc_colaborador)
        ")
        ->bindValue(':id_solicitante', $id_usuario)
        ->bindValue(':cc_colaborador', $cc_usuario)
        ->queryScalar();

        return $cantidad_registros;
    }

    public function contar_registros_novedad_personal_a_cargo($id_usuario, $cc_usuario){

        $cantidad_registros = Yii::$app->db->createCommand("
            SELECT COUNT(novedad.id) AS cantidad_registros
            FROM tbl_gestor_evaluacion_novedad_jefecolaborador novedad
            INNER JOIN tbl_gestor_evaluacion_estadonovedades estado
            ON novedad.id_estado_novedad = estado.id_gestor_evaluacion_estadonovedades
            WHERE estado.nombre = 'En espera'
            AND (novedad.id_colaborador_actual = :id_colaborador_actual OR novedad.cc_colaborador_nuevo = :cc_colaborador_nuevo)
        ")
        ->bindValue(':id_colaborador_actual', $id_usuario)
        ->bindValue(':cc_colaborador_nuevo', $cc_usuario)
        ->queryScalar();

        return $cantidad_registros;
    }

}
?>