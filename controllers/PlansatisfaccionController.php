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
use app\models\Plangeneralsatu;
use app\models\Plansecundariosatu;
use app\models\Planconceptos;
use app\models\Planmejoras;
use app\models\Planacciones;
use app\models\Planeficacia;
use Exception;

  class PlansatisfaccionController extends Controller {

    public function behaviors(){
        return[
          'access' => [
              'class' => AccessControl::classname(),
              'only' => ['index','agregarplan','registrarplan','agregarsatisfaccion','verplan','modificarplan'],
              'rules' => [
                [
                  'allow' => true,
                  'roles' => ['@'],
                  'matchCallback' => function() {
                              return Yii::$app->user->identity->isAdminSistema() ||  Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isControlProcesoCX() || Yii::$app->user->identity->isVerdirectivo();
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

      $varListPlanes = (new \yii\db\Query())
                        ->select([
                        'tbl_plan_generalsatu.id_generalsatu',
                        'tbl_plan_procesos.proceso',
                        'if(tbl_plan_generalsatu.id_actividad=1,"Área","Operación") AS varActividad',
                        'tbl_plan_generalsatu.id_dp_clientes', 'tbl_plan_generalsatu.id_dp_area',
                        'tbl_usuarios_evalua.nombre_completo',
                        'if(tbl_plan_generalsatu.estado=1,"Abierto","Cerrado") AS varEstado'
                        ])
                        ->from(['tbl_plan_generalsatu'])
                        ->join('LEFT OUTER JOIN', 'tbl_plan_procesos',
                              'tbl_plan_procesos.id_procesos = tbl_plan_generalsatu.id_proceso')
                        ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                              'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                        ->where(['=','tbl_plan_generalsatu.anulado',0])
                        ->all(); 


        $varCantidadProcesos = (new \yii\db\Query())
                                ->select([
                                  'tbl_plan_procesos.id_procesos',
                                  'tbl_plan_procesos.proceso',
                                  'COUNT(tbl_plan_procesos.id_procesos) AS varCantidad'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_plan_procesos',
                                  'tbl_plan_procesos.id_procesos = tbl_plan_generalsatu.id_proceso')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->groupBy(['tbl_plan_procesos.id_procesos'])
                                ->all();

        $varCantidadActividad = (new \yii\db\Query())
                                ->select([
                                  'tbl_plan_generalsatu.id_actividad',
                                  'if(tbl_plan_generalsatu.id_actividad = 1, "Área","Operación") AS varActivdad',
                                  'COUNT(tbl_plan_generalsatu.id_actividad) AS varCantidadActividad'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->groupBy(['tbl_plan_generalsatu.id_actividad'])
                                ->all();
      
      return $this->render('index',[
        'varListPlanes' => $varListPlanes,
        'varCantidadProcesos' => $varCantidadProcesos,
        'varCantidadActividad' => $varCantidadActividad,
      ]);
    }

    public function actionAgregarplan(){
      $model = new Plangeneralsatu();
      $varListActividad = ['1'=>'Área','2'=>'Operación'];

      $varListApoyo_String = (new \yii\db\Query())
                                ->select(['id_areaapoyo', 'nombre'])
                                ->from(['tbl_areasapoyo_gptw'])
                                ->where(['=','anulado',0])
                                ->All();
      $varListApoyo = ArrayHelper::map($varListApoyo_String, 'id_areaapoyo', 'nombre');

      $varListOperacion_String = (new \yii\db\Query())
                ->select(['tbl_usuarios_evalua.idusuarioevalua', 'tbl_usuarios_evalua.clientearea'])
                ->from(['tbl_usuarios_evalua'])
                ->where(['IS not','tbl_usuarios_evalua.clientearea',NULL])
                ->andwhere(['<>','tbl_usuarios_evalua.idusuarioevalua',2202])
                ->groupBy('tbl_usuarios_evalua.clientearea')
                ->orderBY ('tbl_usuarios_evalua.clientearea')
                ->All();
      $varListOperacion = ArrayHelper::map($varListOperacion_String, 'idusuarioevalua', 'clientearea');

      $varListResponsable_String = (new \yii\db\Query())
                                    ->select(['tbl_usuarios_jarvis_cliente.idusuarioevalua', "UPPER(trim(replace(tbl_usuarios_jarvis_cliente.nombre_completo,'\n',''))) AS nombre"])
                                    ->from(['tbl_usuarios_jarvis_cliente'])
                                    ->where(['not in','tbl_usuarios_jarvis_cliente.id_dp_funciones',[364, 312, 206, 981]])
                                    ->orderBY ('nombre')
                                    ->All();   
      $varListResponsable = ArrayHelper::map($varListResponsable_String, 'idusuarioevalua', 'nombre');

      $form = Yii::$app->request->post();
      if ($model->load($form)) {

        $varProcesosid = $model->id_proceso;
        $varActividadid = $model->id_actividad;
        $varClienteid = $model->id_dp_clientes;
        $varAreaid = $model->id_dp_area;
        $varResponsable = $model->cc_responsable;
        $varEstado = 1;

        Yii::$app->db->createCommand()->insert('tbl_plan_generalsatu',[
                      'id_proceso' => $varProcesosid,
                      'id_actividad' => $varActividadid, 
                      'id_dp_clientes' => $varClienteid,
                      'id_dp_area' => $varAreaid,
                      'cc_responsable' => $varResponsable,
                      'estado' => $varEstado,
                      'anulado' => 0,
                      'usua_id' =>  Yii::$app->user->identity->id,   
                      'fechacreacion' => date('Y-m-d'),                
        ])->execute();

        $varIdGeneral_Plan = (new \yii\db\Query())
                                ->select(['MAX(tbl_plan_generalsatu.id_generalsatu)'])
                                ->from(['tbl_plan_generalsatu'])
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.usua_id',Yii::$app->user->identity->id])
                                ->andwhere(['=','tbl_plan_generalsatu.id_proceso',$varProcesosid])
                                ->andwhere(['=','tbl_plan_generalsatu.id_actividad',$varActividadid])
                                ->andwhere(['=','tbl_plan_generalsatu.cc_responsable',$varResponsable])
                                ->scalar();  
        
        return $this->redirect(array('registrarplan','id_plan'=>$varIdGeneral_Plan));
      }

      return $this->renderAjax('agregarplan',[
        'model' => $model,
        'varListActividad' => $varListActividad,
        'varListApoyo' => $varListApoyo,
        'varListOperacion' => $varListOperacion,
        'varListResponsable' => $varListResponsable,
      ]);
    }

    public function actionRegistrarplan($id_plan){
      $model = new Plansecundariosatu();

      $varProcesos = (new \yii\db\Query())
                                ->select([
                                  'tbl_plan_procesos.proceso'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_plan_procesos',
                                  'tbl_plan_procesos.id_procesos = tbl_plan_generalsatu.id_proceso')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();  

      $varActividad = (new \yii\db\Query())
                                ->select([
                                  'if(tbl_plan_generalsatu.id_actividad=1,"Área","Operación") AS varActividad'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $vaResponsable = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_evalua.nombre_completo'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                  'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $varRolResponsable = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_jarvis_cliente.posicion'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_jarvis_cliente',
                                  'tbl_usuarios_jarvis_cliente.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varAreas_String = (new \yii\db\Query())
                                ->select([
                                  'tbl_areasapoyo_gptw.nombre'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_areasapoyo_gptw',
                                  'tbl_areasapoyo_gptw.id_areaapoyo = tbl_plan_generalsatu.id_dp_area')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varOperacion_String = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_evalua.clientearea'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                  'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.id_dp_clientes')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varEstados = (new \yii\db\Query())
                                ->select([
                                  'if(tbl_plan_generalsatu.estado=1,"Abierto","Cerrado") AS varEstado'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $varListIndicadores_String = (new \yii\db\Query())
                            ->select(['id_indicador', 'nombre'])
                            ->from(['tbl_indicadores_satisfaccion_cliente'])
                            ->where(['=','anulado',0])
                            ->All();
      $varListIndicadores = ArrayHelper::map($varListIndicadores_String, 'id_indicador', 'nombre');

      $varListAcciones = ['Correctiva'=>'Correctiva','Mejora'=>'Mejora','Preventiva'=>'Preventiva'];

      $varListaConceptos = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_conceptos'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListaMejoras = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_mejoras'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListaAcciones = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_acciones'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListArchivos = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_subirarchivos'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varFecha_implementacion = $model->fecha_implementacion;
        $varIndicador = $model->indicador;
        $varAcciones = $model->acciones;
        $varPuntaje_meta = $model->puntaje_meta;
        $varPuntaje_actual = $model->puntaje_actual;
        $varPuntaje_final = $model->puntaje_final;

        Yii::$app->db->createCommand()->insert('tbl_plan_secundariosatu',[
                      'id_generalsatu' => $id_plan,
                      'fecha_implementacion' => $varFecha_implementacion, 
                      'fecha_definicion' => date('Y-m-d'),
                      'indicador' => $varIndicador,
                      'acciones' => $varAcciones,
                      'puntaje_meta' => $varPuntaje_meta,
                      'puntaje_actual' => $varPuntaje_actual,
                      'puntaje_final' => $varPuntaje_final,
                      'anulado' => 0,
                      'usua_id' =>  Yii::$app->user->identity->id,   
                      'fechacreacion' => date('Y-m-d'),                
        ])->execute();

        return $this->redirect(['index']);
      }


      return $this->render('registrarplan',[
        'id_plan' => $id_plan,
        'varProcesos' => $varProcesos,
        'varActividad' => $varActividad,
        'vaResponsable' => $vaResponsable,
        'varRolResponsable' => $varRolResponsable,
        'varAreas_String' => $varAreas_String,
        'varOperacion_String' => $varOperacion_String,
        'varEstados' => $varEstados,
        'model' => $model,
        'varListIndicadores' => $varListIndicadores,
        'varListAcciones' => $varListAcciones,
        'varListaConceptos' => $varListaConceptos,
        'varListaMejoras' => $varListaMejoras,
        'varListaAcciones' => $varListaAcciones,
        'varListArchivos' => $varListArchivos,
      ]);
    }

    public function actionAgregarconceptos($id){
      $model = new Planconceptos();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varConcepto = $model->concepto;

        Yii::$app->db->createCommand()->insert('tbl_plan_conceptos',[
                      'id_generalsatu' => $id,
                      'concepto' => $varConcepto, 
                      'anulado' => 0,
                      'usua_id' =>  Yii::$app->user->identity->id,   
                      'fechacreacion' => date('Y-m-d'),                
        ])->execute();

        return $this->redirect(array('registrarplan','id_plan'=>$id));
      }

      return $this->renderAjax('agregarconceptos',[
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionEliminarconceptos($id_conceptos,$id_plan){

      Yii::$app->db->createCommand('DELETE FROM tbl_plan_conceptos WHERE id_conceptos=:id')->bindParam(':id',$id_conceptos)->execute();

      return $this->redirect(array('registrarplan','id_plan'=>$id_plan));
    }

    public function actionAgregarcausas($id){
      $model = new Planmejoras();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varCausas = $model->mejoras;

        Yii::$app->db->createCommand()->insert('tbl_plan_mejoras',[
                      'id_generalsatu' => $id,
                      'mejoras' => $varCausas, 
                      'anulado' => 0,
                      'usua_id' =>  Yii::$app->user->identity->id,   
                      'fechacreacion' => date('Y-m-d'),                
        ])->execute();

        return $this->redirect(array('registrarplan','id_plan'=>$id));
      }

      return $this->renderAjax('agregarcausas',[
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionEliminarmejoras($id_mejoras,$id_plan){

      Yii::$app->db->createCommand('DELETE FROM tbl_plan_mejoras WHERE id_mejoras=:id')->bindParam(':id',$id_mejoras)->execute();

      return $this->redirect(array('registrarplan','id_plan'=>$id_plan));
    }

    public function actionAgregaracciones($id){
      $model = new Planacciones();

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varAcciones = $model->acciones;

        Yii::$app->db->createCommand()->insert('tbl_plan_acciones',[
                      'id_generalsatu' => $id,
                      'acciones' => $varAcciones, 
                      'anulado' => 0,
                      'usua_id' =>  Yii::$app->user->identity->id,   
                      'fechacreacion' => date('Y-m-d'),                
        ])->execute();

        return $this->redirect(array('registrarplan','id_plan'=>$id));
      }

      return $this->renderAjax('agregaracciones',[
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionEliminaracciones($id_acciones,$id_plan){

      Yii::$app->db->createCommand('DELETE FROM tbl_plan_acciones WHERE id_acciones=:id')->bindParam(':id',$id_acciones)->execute();

      return $this->redirect(array('registrarplan','id_plan'=>$id_plan));
    }

    public function actionSubirarchivos($id){
      $model = new UploadForm2();
      $ruta = null;

      $form = Yii::$app->request->post();     

      if($model->load($form)){
        $model->file = UploadedFile::getInstance($model, 'file');
        if ($model->file && $model->validate()) {
          var_dump("Ingresa");
          foreach ($model->file as $file) {
            $ruta = 'images/planes/'."plan_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
            $nombrearchivo = "plan_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
            $model->file->saveAs( $ruta ); 
          }
        } 

        if ($ruta != null) {
          Yii::$app->db->createCommand()->insert('tbl_plan_subirarchivos',[
                      'id_generalsatu' => $id,
                      'nombre_archivo' => $nombrearchivo, 
                      'ruta_archivos' => $ruta,   
                      'anulado' => 0,
                      'usua_id' =>  Yii::$app->user->identity->id,   
                      'fechacreacion' => date('Y-m-d'),                
          ])->execute();
        }

        return $this->redirect(array('registrarplan','id_plan'=>$id));
      }

      return $this->renderAjax('subirarchivos',[
        'model' => $model,
        'id' => $id,
      ]);
    }

    public function actionEliminararchivos($id_subirarchivos,$id_plan){

      Yii::$app->db->createCommand('DELETE FROM tbl_plan_subirarchivos WHERE id_subirarchivos=:id')->bindParam(':id',$id_subirarchivos)->execute();

      return $this->redirect(array('registrarplan','id_plan'=>$id_plan));
    }

    public function actionAgregarsatisfaccion($id_plan){
      $model = new Planeficacia();   

      $varListasatisfac = (new \yii\db\Query())
                            ->select([
                              '*'
                            ])
                            ->from(['tbl_plan_eficacia'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();


      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $txtobservaciones = $model->eficacia;
        
        Yii::$app->db->createCommand()->insert('tbl_plan_eficacia',[
                        'id_generalsatu' => $id_plan,
                        'eficacia' => $txtobservaciones,
                        'fechacreacion' => date("Y-m-d"),
                        'anulado' => 0,
                        'usua_id' => Yii::$app->user->identity->id,
        ])->execute();
                                    
        return $this->redirect(array('agregarsatisfaccion','id_plan'=>$id_plan)); 
      
      }

      return $this->render('agregarsatisfaccion',[
        'model' => $model,
        'id_plan' => $id_plan,
        'varListasatisfac' => $varListasatisfac,
      ]);
    }

    public function actionDeleteprocesosatisfac($id,$id_plan){
                      
      Yii::$app->db->createCommand('DELETE FROM tbl_plan_eficacia WHERE id_eficacia=:id')->bindParam(':id',$id)->execute();

      return $this->redirect(array('agregarsatisfaccion','id_plan'=>$id_plan));
      
    }

    public function actionVerplan($id_plan){

      $varPlanProcesos = (new \yii\db\Query())
                                ->select([
                                  'tbl_plan_procesos.proceso'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_plan_procesos',
                                  'tbl_plan_procesos.id_procesos = tbl_plan_generalsatu.id_proceso')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();  

      $varPlanActividad = (new \yii\db\Query())
                                ->select([
                                  'if(tbl_plan_generalsatu.id_actividad=1,"Área","Operación") AS varActividad'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $varPlanResponsable = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_evalua.nombre_completo'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                  'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $varPlanRolResponsable = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_jarvis_cliente.posicion'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_jarvis_cliente',
                                  'tbl_usuarios_jarvis_cliente.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varPlanAreas_String = (new \yii\db\Query())
                                ->select([
                                  'tbl_areasapoyo_gptw.nombre'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_areasapoyo_gptw',
                                  'tbl_areasapoyo_gptw.id_areaapoyo = tbl_plan_generalsatu.id_dp_area')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varPlanOperacion_String = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_evalua.clientearea'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                  'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.id_dp_clientes')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varPlanEstados = (new \yii\db\Query())
                                ->select([
                                  'if(tbl_plan_generalsatu.estado=1,"Abierto","Cerrado") AS varEstado'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $varPlanListaSecundaria = (new \yii\db\Query())
                                ->select([
                                  '*'
                                ])
                                ->from(['tbl_plan_secundariosatu'])
                                ->where(['=','tbl_plan_secundariosatu.anulado',0])
                                ->andwhere(['=','tbl_plan_secundariosatu.id_generalsatu',$id_plan])
                                ->all(); 

      $varListaPlanConceptos = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_conceptos'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListaPlanMejoras = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_mejoras'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListaPlanAcciones = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_acciones'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListPlanArchivos = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_subirarchivos'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListaPlansatisfac = (new \yii\db\Query())
                            ->select([
                              '*'
                            ])
                            ->from(['tbl_plan_eficacia'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      return $this->render('verplan',[
        'id_plan' => $id_plan,
        'varPlanProcesos' => $varPlanProcesos,
        'varPlanActividad' => $varPlanActividad,
        'varPlanResponsable' => $varPlanResponsable,
        'varPlanRolResponsable' => $varPlanRolResponsable,
        'varPlanAreas_String' => $varPlanAreas_String,
        'varPlanOperacion_String' => $varPlanOperacion_String,
        'varPlanEstados' => $varPlanEstados,
        'varPlanListaSecundaria' => $varPlanListaSecundaria,
        'varListaPlanConceptos' => $varListaPlanConceptos,
        'varListaPlanMejoras' => $varListaPlanMejoras,
        'varListaPlanAcciones' => $varListaPlanAcciones,
        'varListPlanArchivos' => $varListPlanArchivos,
        'varListaPlansatisfac' => $varListaPlansatisfac,
      ]);
    }

    public function actionModificarplan($id_plan){
      $varIdinfo = (new \yii\db\Query())
                                ->select([
                                  'tbl_plan_secundariosatu.id_secundariosatu'
                                ])
                                ->from(['tbl_plan_secundariosatu'])
                                ->where(['=','tbl_plan_secundariosatu.anulado',0])
                                ->andwhere(['=','tbl_plan_secundariosatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $model =  Plansecundariosatu::findOne($varIdinfo); 

      $varPlanProcesos_Modificar = (new \yii\db\Query())
                                ->select([
                                  'tbl_plan_procesos.proceso'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_plan_procesos',
                                  'tbl_plan_procesos.id_procesos = tbl_plan_generalsatu.id_proceso')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();  

      $varPlanActividad_Modificar = (new \yii\db\Query())
                                ->select([
                                  'if(tbl_plan_generalsatu.id_actividad=1,"Área","Operación") AS varActividad'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $varPlanResponsable_Modificar = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_evalua.nombre_completo'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                  'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $varPlanRolResponsable_Modificar = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_jarvis_cliente.posicion'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_jarvis_cliente',
                                  'tbl_usuarios_jarvis_cliente.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varPlanAreas_String_Modificar = (new \yii\db\Query())
                                ->select([
                                  'tbl_areasapoyo_gptw.nombre'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_areasapoyo_gptw',
                                  'tbl_areasapoyo_gptw.id_areaapoyo = tbl_plan_generalsatu.id_dp_area')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varPlanOperacion_String_Modificar = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_evalua.clientearea'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                  'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.id_dp_clientes')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar();

      $varPlanEstados_Modificar = (new \yii\db\Query())
                                ->select([
                                  'if(tbl_plan_generalsatu.estado=1,"Abierto","Cerrado") AS varEstado'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$id_plan])
                                ->scalar(); 

      $varListaPlanConceptos_Modificar = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_conceptos'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListaPlanMejoras_Modificar = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_mejoras'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListaPlanAcciones_Modificar = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_acciones'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->All();

      $varListPlanArchivos_Modificar = (new \yii\db\Query())
                            ->select(['*'])
                            ->from(['tbl_plan_subirarchivos'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_generalsatu',$id_plan])
                            ->all();

      $varListIndicadores_String = (new \yii\db\Query())
                            ->select(['id_indicador', 'nombre'])
                            ->from(['tbl_indicadores_satisfaccion_cliente'])
                            ->where(['=','anulado',0])
                            ->All();
      $varListIndicadores = ArrayHelper::map($varListIndicadores_String, 'id_indicador', 'nombre');

      $varListAcciones = ['Correctiva'=>'Correctiva','Mejora'=>'Mejora','Preventiva'=>'Preventiva'];

      $form = Yii::$app->request->post();
      if ($model->load($form)) {
        $varFecha_implementacion_Modificar = $model->fecha_implementacion;
        $varIndicador_Modificar = $model->indicador;
        $varAcciones_Modificar = $model->acciones;
        $varPuntaje_meta_Modificar = $model->puntaje_meta;
        $varPuntaje_actual_Modificar = $model->puntaje_actual;
        $varPuntaje_final_Modificar = $model->puntaje_final;

        Yii::$app->db->createCommand()->update('tbl_plan_secundariosatu',[
                      'fecha_implementacion' => $varFecha_implementacion_Modificar,                   
                      'indicador' => $varIndicador_Modificar,
                      'acciones' => $varAcciones_Modificar,
                      'puntaje_meta' => $varPuntaje_meta_Modificar,
                      'puntaje_actual' => $varPuntaje_actual_Modificar,
                      'puntaje_final' => $varPuntaje_final_Modificar,                      
        ],'id_secundariosatu ='.$varIdinfo.' AND id_generalsatu = '.$id_plan.'')->execute();

        return $this->redirect(['index']);
      }


      return $this->render('modificarplan',[
        'model' => $model,
        'id_plan' => $id_plan,
        'varPlanProcesos_Modificar' => $varPlanProcesos_Modificar,
        'varPlanActividad_Modificar' => $varPlanActividad_Modificar,
        'varPlanResponsable_Modificar' => $varPlanResponsable_Modificar,
        'varPlanRolResponsable_Modificar' => $varPlanRolResponsable_Modificar,
        'varPlanAreas_String_Modificar' => $varPlanAreas_String_Modificar,
        'varPlanOperacion_String_Modificar' => $varPlanOperacion_String_Modificar,
        'varPlanEstados_Modificar' => $varPlanEstados_Modificar,
        'varListaPlanConceptos_Modificar' => $varListaPlanConceptos_Modificar,
        'varListaPlanMejoras_Modificar' => $varListaPlanMejoras_Modificar,
        'varListaPlanAcciones_Modificar' => $varListaPlanAcciones_Modificar,
        'varListPlanArchivos_Modificar' => $varListPlanArchivos_Modificar,
        'varListIndicadores' => $varListIndicadores,
        'varListAcciones' => $varListAcciones,

      ]);
    }

    public function actionDescargarplanes(){

      $varListaPlanesSatu = (new \yii\db\Query())
                                ->select([
                                  'tbl_plan_generalsatu.id_generalsatu',
                                  'tbl_plan_generalsatu.id_proceso', 
                                  'tbl_plan_generalsatu.id_actividad', 
                                  'tbl_plan_generalsatu.id_dp_clientes',
                                  'tbl_plan_generalsatu.id_dp_area', 
                                  'tbl_plan_generalsatu.cc_responsable', 
                                  'tbl_plan_generalsatu.estado',
                                  'tbl_plan_secundariosatu.fecha_implementacion',
                                  'tbl_plan_secundariosatu.fecha_definicion', 
                                  'tbl_plan_secundariosatu.fecha_cierre', 
                                  'tbl_plan_secundariosatu.indicador', 
                                  'tbl_plan_secundariosatu.acciones',
                                  'tbl_plan_secundariosatu.puntaje_meta', 
                                  'tbl_plan_secundariosatu.puntaje_actual', 
                                  'tbl_plan_secundariosatu.puntaje_final'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_plan_secundariosatu',
                                  'tbl_plan_secundariosatu.id_generalsatu = tbl_plan_generalsatu.id_generalsatu')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->all();  

      return $this->renderAjax('descargarplanes',[
        'varListaPlanesSatu' => $varListaPlanesSatu,
      ]);
    }

}

?>


