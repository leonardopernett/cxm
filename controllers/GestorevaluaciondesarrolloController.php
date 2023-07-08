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
                        'resultados', 'resultadoindividual'],
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
        $var_document = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
        
        $var_document = 456  ;
        $existe_usuario = Yii::$app->db->createCommand("select count(u.identificacion) AS cant_registros, u.id_gestor_evaluacion_usuarios, u.es_jefe, u.es_colaborador from tbl_gestor_evaluacion_usuarios u where identificacion in ('$var_document')")->queryOne();
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
                        'usua_id' => Yii::$app->user->identity->id,
                        'anulado' => 0,
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

        $existe_usuario = false;
        $evaluac_completadas = [];

        $sessiones = Yii::$app->user->identity->id;
        $document = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
        $usuario_carga_masiva = Yii::$app->db->createCommand("select count(u.identificacion) AS cant_registros, u.id_gestor_evaluacion_usuarios from tbl_gestor_evaluacion_usuarios u where identificacion in ('$document')")->queryOne();
        
        if($usuario_carga_masiva['cant_registros']==1){
            $existe_usuario = true;
            $id_usuario = $usuario_carga_masiva['id_gestor_evaluacion_usuarios'];
            $evaluacion_actual = $this->obtenerEvaluacionActual();
            $id_evalua_nombre = $evaluacion_actual['id_evalua'];

           
      



            
        }

        $model = new GestorEvaluacionPreguntas();

    return $this->render('resultadoindividual',[
        'model' => $model,
        'existe_usuario'=> $existe_usuario
        
    ]);
                
    }
    
    public function actionResultados(){
        $model = new GestorEvaluacionPreguntas();

    return $this->render('resultados',[
        'model' => $model
        
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
            $contadorEvaluaciones++; //tiene asociada una evaluacion del jefe
        }

        return $contadorEvaluaciones;
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

    public function existe_registros_calificacion_total_por_evaluacion($id_user, $id_evalua_nombre){

        $response = false;

        $query = (new \yii\db\Query())
            ->select('COUNT(c.id_gestor_evaluacion_calificaciontotal) AS cant_registros')
            ->from('tbl_gestor_evaluacion_calificaciontotal c')
            ->where(['c.anulado' => '0',
            'c.id_evalua_nombre' => $id_evalua_nombre,
            'c.id_evaluado'=>$id_user])
            ->scalar(); 

        if($query!==0){
            $response = true;
        }

        return $response;
    }


}
?>