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
      return $this->render('index');
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
                  SELECT COUNT(jefe.id_gestor_evaluacion_jefes) AS num_registros_jefe FROM tbl_gestor_evaluacion_jefes jefe
                    WHERE 
                    jefe.identificacion IN (:varJefeCC)')->bindValues($paramsBusqueda)->queryScalar();

                
                if ($varExisteJefe == "0") {                               

                    Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_jefes',[
                                        'nombre_completo' => $sheet->getCell("B".$row)->getValue(),
                                        'identificacion' => $cc_jefe,
                                        'genero' =>  $sheet->getCell("F".$row)->getValue(),
                                        'cargo' => $sheet->getCell("A".$row)->getValue(),
                                        'area_operacion' => $sheet->getCell("D".$row)->getValue(),
                                        'ciudad' => $sheet->getCell("E".$row)->getValue(),
                                        'sociedad' => $sheet->getCell("G".$row)->getValue(),
                                        'fechacreacion' => date("Y-m-d"),                                        
                                        'usua_id' => Yii::$app->user->identity->id,
                                        'anulado' => 0,
                                        ])->execute();

                    // Obtener el valor de la clave primaria generada
                    $pk_jefe = Yii::$app->db->getLastInsertID();
                }  
                
                if ($varExisteJefe == "1") {

                    $pk_jefe = (new \yii\db\Query())
                            ->select('id_gestor_evaluacion_jefes')
                            ->from('tbl_gestor_evaluacion_jefes')
                            ->where(['identificacion' => $cc_jefe])
                            ->scalar();
                }
                 
            }

            $cc_colaborador = $sheet->getCell("J".$row)->getValue();

            if ($cc_colaborador != null) {
                $paramsBusqueda = [':varColaboradorCC' => $cc_colaborador];

                $varExisteColaborador = Yii::$app->db->createCommand('
                  SELECT COUNT(colaborador.id_gestor_evaluacion_colaboradores) AS num_registros FROM tbl_gestor_evaluacion_colaboradores colaborador
                    WHERE 
                    colaborador.identificacion IN (:varColaboradorCC)')->bindValues($paramsBusqueda)->queryScalar();

                if ($varExisteColaborador == "0") {                               

                    Yii::$app->db->createCommand()->insert('tbl_gestor_evaluacion_colaboradores',[
                                        'nombre_completo' => $sheet->getCell("I".$row)->getValue(),
                                        'identificacion' => $cc_colaborador,
                                        'genero' =>  $sheet->getCell("M".$row)->getValue(),
                                        'cargo' => $sheet->getCell("H".$row)->getValue(),
                                        'area_operacion' => $sheet->getCell("K".$row)->getValue(),
                                        'ciudad' => $sheet->getCell("L".$row)->getValue(),
                                        'sociedad' => $sheet->getCell("N".$row)->getValue(),
                                        'fechacreacion' => date("Y-m-d"),                                        
                                        'usua_id' => Yii::$app->user->identity->id,
                                        'anulado' => 0,
                                        ])->execute();

                    // Obtener el valor de la clave primaria generada
                    $pk_colaborador = Yii::$app->db->getLastInsertID();
                }       
                
                if ($varExisteColaborador == "1") {

                    $pk_colaborador = (new \yii\db\Query())
                            ->select('id_gestor_evaluacion_colaboradores')
                            ->from('tbl_gestor_evaluacion_colaboradores')
                            ->where(['identificacion' => $cc_colaborador])
                            ->scalar();
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
    public function actionAutoevaluacion(){ 
        $model =  new GestorEvaluacionPreguntas();
  
        return $this->render('autoevaluacion',[
            'model' => $model,
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