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


class GestorevaluaciondesarrolloController extends \yii\web\Controller {

    public function behaviors(){
        return[
        
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','parametrizador', 'cargardatostablapreguntas', 'crearpregunta', 'editarpregunta', 'eliminarpregunta',
                        'cargardatostablarespuestas', 'createrespuesta', 'editrespuesta', 'deleterespuesta',
                        'importardatoscargamasiva', 'detallecargamasiva',
                        'autoevaluacion', 'crearautoevaluacion',
                        'modalevaluacionacargo', 'evaluacionacargo', 'crearevaluacionacargo',
                        'resultados', 'resultadoindividual',
                        'crearfeedback', 'modalfeedbackcolaborador'],
            'rules' => [
                [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isCuadroMando()  || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerevaluacion() || Yii::$app->user->identity->isVerdirectivo();
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
        $sessiones = Yii::$app->user->identity->id;
        $estado_evaluacion=0;
        $id_evalua_nombre = "";
        $evalua_nombre = "";
        $documento = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
        
        $documento = 2425;
        $existe_usuario = Yii::$app->db->createCommand("select count(u.identificacion) AS cant_registros, u.id_gestor_evaluacion_usuarios, u.es_jefe, u.es_colaborador from tbl_gestor_evaluacion_usuarios u where identificacion in ('$documento')")->queryOne();
        $evaluaciones_completadas = false;
        $no_tiene_evaluacion_a_cargo= false;

        $evaluacion_actual = $this->obtenerEvaluacionActual();

        if($evaluacion_actual){
            $id_evalua_nombre = $evaluacion_actual['id_evalua'];
            $evalua_nombre = $evaluacion_actual['nombreeval'];
        }   
        
        if($existe_usuario['cant_registros']=='1'){
            
            $esjefe = $existe_usuario['es_jefe'];
            $esColaborador = $existe_usuario['es_colaborador'];

            if($esjefe!=null){
                $id_usuario = $existe_usuario['id_gestor_evaluacion_usuarios'];    
            }

            if($esjefe==null && $esColaborador!=null){
                $id_usuario = $existe_usuario['id_gestor_evaluacion_usuarios'];    
            }   

            $evaluaciones_completadas = $this->verificarEstadoEvaluaciones($id_usuario, $id_evalua_nombre);
            $cant_evaluaciones_usuario= $this->obtenerTotalEvaluacionesParaUnUsuario($id_usuario);
           
            if($cant_evaluaciones_usuario==1){
                $no_tiene_evaluacion_a_cargo=true;                                
            }
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
                $estado_evaluacion = $varauto;
            }

        }
        //$id_estado = Yii::$app->db->createCommand("select id_gestor_evaluacion_estadoeval FROM tbl_gestor_evaluacion_estadoeval WHERE  estado= 'Incompleto' and anulado = 0")->queryScalar();
       
        //SI YA TIENE TODAS LAS CALIFICACIONES MOSTRAR MENSAJE DE QUE PUEDE IR  AVERLAS SINO EN ESPERA DE EVALAUCION A CARGO***

        //Si ya no tienes personas a cargo para evaluar mostrar completado
       

      return $this->render('index', [
          'id_evaluacion_actual' => $evaluacion_actual['id_evalua'],
          'varauto' => $estado_evaluacion,
          'existe_usuario' => $existe_usuario,
          'id_usuario' => $id_usuario,
          'esjefe' => $esjefe,
          'evaluaciones_completadas' => $evaluaciones_completadas,
          'no_tiene_evaluacion_a_cargo' => $no_tiene_evaluacion_a_cargo
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
        $model->usua_id = Yii::$app->user->identity->id;
        
        
        if ($model->save()) {            
            // $nuevaData = $model->attributes;
            // unset($nuevaData['fechacreacion']);
            // unset($nuevaData['usua_id']);
            // unset($nuevaData['anulado']);            

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
            $cc_jefe = $sheet->getCell("C".$row)->getValue(); 
                
            if ( $cc_jefe != null) {

                $paramsBusqueda = [':varJefeCC' => $cc_jefe];

                $varExisteJefe = Yii::$app->db->createCommand('
                  SELECT COUNT(usuario.id_gestor_evaluacion_usuarios) AS num_registros_jefe, usuario.es_jefe FROM tbl_gestor_evaluacion_usuarios usuario
                    WHERE 
                    usuario.identificacion IN (:varJefeCC)')->bindValues($paramsBusqueda)->queryOne();

                if ($varExisteJefe['num_registros_jefe'] == "0") {                               

                    Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_usuarios',[
                                        'nombre_completo' => $sheet->getCell("B".$row)->getValue(),
                                        'identificacion' => $cc_jefe,
                                        'genero' =>  $sheet->getCell("F".$row)->getValue(),
                                        'cargo' => $sheet->getCell("A".$row)->getValue(),
                                        'area_operacion' => $sheet->getCell("D".$row)->getValue(),
                                        'ciudad' => $sheet->getCell("E".$row)->getValue(),
                                        'sociedad' => $sheet->getCell("G".$row)->getValue(),
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

            $cc_colaborador = $sheet->getCell("J".$row)->getValue();

            if ($cc_colaborador != null) {
                $paramsBusqueda = [':varColaboradorCC' => $cc_colaborador];

                $varExisteColaborador = Yii::$app->db->createCommand('
                  SELECT COUNT(usuario.id_gestor_evaluacion_usuarios) AS num_registros, usuario.es_colaborador FROM tbl_gestor_evaluacion_usuarios usuario
                    WHERE 
                    usuario.identificacion IN (:varColaboradorCC)')->bindValues($paramsBusqueda)->queryOne();

                if ($varExisteColaborador['num_registros'] == "0") {                               

                    Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_usuarios',[
                                        'nombre_completo' => $sheet->getCell("I".$row)->getValue(),
                                        'identificacion' => $cc_colaborador,
                                        'genero' =>  $sheet->getCell("M".$row)->getValue(),
                                        'cargo' => $sheet->getCell("H".$row)->getValue(),
                                        'area_operacion' => $sheet->getCell("K".$row)->getValue(),
                                        'ciudad' => $sheet->getCell("L".$row)->getValue(),
                                        'sociedad' => $sheet->getCell("N".$row)->getValue(),
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
        
        $params_busqueda = [':id_jefe' => $id_jefe];
        $array_personas_a_cargo =  Yii::$app->db->createCommand('
        SELECT u.id_gestor_evaluacion_usuarios AS id_colaborador, 
            u.nombre_completo, u.identificacion, form.id_estado_evaluacion
        FROM tbl_gestor_evaluacion_jefe_colaborador relacion  
        INNER JOIN tbl_gestor_evaluacion_usuarios u
        ON relacion.id_usuario_colaborador = u.id_gestor_evaluacion_usuarios 
        LEFT JOIN tbl_gestor_evaluacion_formulario form
        ON relacion.id_usuario_colaborador = form.id_evaluado && relacion.id_usuario_jefe = form.id_evaluador
        WHERE relacion.anulado=0 AND form.id_estado_evaluacion IS NULL AND relacion.id_usuario_jefe IN (:id_jefe)')->bindValues($params_busqueda)->queryAll(); 

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

    public function actionResultadoindividual(){

        //Obtener id y documento del usuario logueado
        $sessiones = Yii::$app->user->identity->id;
        $documento = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
        $documento = 2223;
        
        //Validar usuario segun datos de la carga masiva
        $id_user = null;
        $mostrar_feedbacks='none';
        $existe_usuario = Yii::$app->db->createCommand("select count(u.identificacion) AS cant_registros, u.id_gestor_evaluacion_usuarios, u.es_jefe, u.es_colaborador from tbl_gestor_evaluacion_usuarios u where identificacion in ('$documento')")->queryOne();
        $registros_encontrados = $existe_usuario['cant_registros'];
          
        //variables locales
        $id_evalua_nombre = "------";
        $evalua_nombre = "------";
        $existe_calificacion_total=false;
        $promTotalEvaluacion = 0;
        $sumaTotalEvaluacion = 0;
        $data_competencias = [];
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
               
                if(!empty($data_form && count($data_form)>1 )){

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
            'feedback_acuerdo_final'=>$feedback_acuerdo_final
            ]);
                
    }
    
    public function actionResultados() {

        
        $model_feedback_entrada = new GestorEvaluacionFeedbackentradas();

        //Obtener id y documento del usuario logueado
        $sessiones = Yii::$app->user->identity->id;
        $documento = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
        $documento = 456;
        
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
                    'data' => 'Ocurrió un error creando un registro para el feedback'
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
             //Respuesta exitosa
             $response = [
                 'status' => 'success',
                 'data' => 'Creación exitosa'
             ];

         } else {
             $response = [
                 'status' => 'error',
                 'data' => 'Error creando comentario para el feedback'
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
        $id_evaluacion_a_cargo = $query = (new Query())
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

                Yii::$app->session->setFlash('success', 'Creación exitosa.');

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

    //Obtiene el número total de calificaciones para evaluacion 2023
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

    //Crea un registro en la tabla tbl_gestor_evaluacion_calificaciontotal
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

    //Funcion para verificar si tiene las dos evalauciones y estan en estado completadas
    public function verificarEstadoEvaluaciones($id_evaluado, $evalua_nom) {
     
        $evaluaciones_completadas = false;

        if( isset($id_evaluado) && !empty($id_evaluado) && isset($evalua_nom) && !empty($evalua_nom) ){
    
            $existe = (new \yii\db\Query())
            ->select([
                'form.id_gestor_evaluacion_formulario AS id_formulario',
                'form.id_estado_evaluacion',
            ])        
            ->from('tbl_gestor_evaluacion_formulario form')
            ->where([
                'form.id_evaluacionnombre' => $evalua_nom,
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
                'total.promedio_total_evalua'
            ])
            ->from('tbl_gestor_evaluacion_calificaciontotal total')
            ->innerJoin('tbl_gestor_evaluacion_usuarios usuario', 'total.id_evaluado = usuario.id_gestor_evaluacion_usuarios')
            ->where([
                'total.anulado' => 0,
                'total.id_evalua_nombre' => $id_evaluac_nombre,
            ])
            ->andWhere(['IN', 'total.id_evaluado', $id_users]);

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

}
?>