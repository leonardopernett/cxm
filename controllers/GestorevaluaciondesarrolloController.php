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

class GestorevaluaciondesarrolloController extends \yii\web\Controller {

    public function behaviors(){
        return[
        
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','parametrizador', 'cargardatostablapreguntas', 'crearpregunta', 'editarpregunta', 'eliminarpregunta',
                        'cargardatostablarespuestas', 'createrespuesta', 'editrespuesta', 'deleterespuesta',
                        'importardatoscargamasiva', 'detallecargamasiva',
                        'autoevaluacion',
                        'modalevaluacionacargo', 'evaluacionacargo'],
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
        $evaluacion_actual = Yii::$app->db->createCommand('
        SELECT idevaluacionnombre AS id_evalua, nombreeval
        FROM tbl_evaluacion_nombre 
        WHERE anulado=0
        ORDER BY fechacrecion
        ')->queryOne();

      return $this->render('index', [
          'id_evaluacion_actual' => $evaluacion_actual['id_evalua'],
          'nom_eval_actual' => $evaluacion_actual['nombreeval']
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
                        $this->Importexcelusuarios($name);

                        return $this->redirect('parametrizador');
                    }
                }
           }

        return $this->renderAjax('viewcargamasiva',[
            'model' => $model,
        ]);

    }


    public function actionImportardatoscargamasiva(){
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
                        unlink('categorias/' . $name . '.' . $file->extension);
                        return $this->redirect(['parametrizador']);
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
                   Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_jefe_colaborador',[
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
        $model =  new GestorEvaluacionPreguntas();

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
        $opcion_respuestas = ArrayHelper::map($array_respuestas,
        'valor',
        'nombre_respuesta');      

        //Lista de tiempo de desarrollo
        $option_tiempo_en_el_cargo = [
            '1' => 'Inferior a 6 meses',
            '2' => '6 meses a 1 año',
            '3' => '2 años a 3 años',
            '3' => '3 años en adelante',
        ];

        return $this->render('autoevaluacion',[
            'model' => $model,
            'datos_usuario' => $datos_usuario_logueado,
            'jefe' => $datos_jefe,
            'array_preguntas' => $array_preguntas,
            'array_respuestas'=> $array_respuestas,
            'opcion_respuestas'=> $opcion_respuestas,
            'lista_tiempo_en_cargo' => $option_tiempo_en_el_cargo
            
        ]);
      }



    //----EVALUACION A CARGO ------------------------------------------------------
    public function actionModalevaluacionacargo(){
        $model = new GestorEvaluacionPreguntas();
  
        // $form = Yii::$app->request->post();
        // if ($model->load($form)) {
            
        //   return $this->redirect(array('evaluaciondecargos','idparams'=>'cod'.$model->documento.'cargo'));
        // }
  
        return $this->renderAjax('modalevaluacionacargo',[
          'model' => $model
          ]);
      }

      public function actionEvaluacionacargo(){
        $model =  new GestorEvaluacionPreguntas();
        return $this->render('evaluacionacargo',[
            'model' => $model,
        ]);
      }




}
?>