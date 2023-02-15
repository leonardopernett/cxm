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
use app\models\Controldetallemomento;
use app\models\SpeechParametrizar;
use app\models\Plangptw;
use app\models\Usuarios;
use app\models\UsuariosEvalua;
use app\models\AreaGptw;
use app\models\Pilaresgptw;
use app\models\DetallesPilaresGptw;


    class PlanacciongptwController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['updatecargaplan', 'updateplan','createplanaccion','createplanaccionfoco','createplanaccionnew','updateplanaccion','deleteacciongptw','listardetallepilar'],
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerdirectivo();
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
                                        
            $model = new SpeechParametrizar();
            $model2 = new Plangptw();  
    
                $data = Yii::$app->request->post();
                if ($model->load($data)) {
                    $txtPcrc = $model->arbol_id;
                    return $this->redirect(array('indexvoc','arbol_idV'=>$txtPcrc));
                }  
    
                return $this->render('index', [
                    'model' => $model,
                    'model2' => $model2,
                    ]);
        }    
                    
        public function actionCreateplanaccionnew(){
          //  $txtvpuntajeactual = Yii::$app->request->get("txtvpuntajeactual");
          //  die(json_encode($txtvpuntajeactual));
                        $txtvarea = null;   
                        $txtvopera = null;
                        $txtvfocomejora = null;
                        $txtvpuntajeactual = null;
                        $txtvpuntajemeta = null;
                        $txtvaccion = null;
                        $txtvfechacierre = null;
                        $txtvfechaavan = null;
                        $txtvobservacion = null;
                        $txtvresponsable = null;
                        $txtvfocomejora2 = null;
                        $txtvlistadetalle = null;
                        $txtvlistadetalle2 = null;

                        $txtvaloradorID = Yii::$app->user->identity->id;                       
                        $txtvestado = 'Abierto';

                        $txtvarea = Yii::$app->request->get("txtvarea");                       
                        
                        $txtvopera = Yii::$app->request->get("txtvopera");
                        $txtvfocomejora = Yii::$app->request->get("txtvfocomejora");
                        for ($i = 0; $i< count($txtvfocomejora); $i++) {
                            $element = $txtvfocomejora[$i];                            
                            $txtvfocomejora2 = $txtvfocomejora2 . $element . ', ';                            
                        }
                        $txtvfocomejora2 = substr($txtvfocomejora2, 0, -2);

                        $txtvpuntajeactual = Yii::$app->request->get("txtvpuntajeactual");
                        $txtvpuntajemeta = Yii::$app->request->get("txtvpuntajemeta");
                        $txtvaccion = Yii::$app->request->get("txtvaccion");
                        $txtvfechacierre = date(Yii::$app->request->get("txtvfechareg"));
                        $txtvfechaavan = date(Yii::$app->request->get("txtvfechaavan"));
                        $txtvobservacion = Yii::$app->request->get("txtvobservacion");
                        $txtvresponsable = Yii::$app->request->get("txtvresponsable");
                        $txtvlistadetalle = Yii::$app->request->get("txtvlistadetalle");
                        for ($i = 0; $i< count($txtvlistadetalle); $i++) {
                            $element = $txtvlistadetalle[$i];                            
                            $txtvlistadetalle2 = $txtvlistadetalle2 . $element . ', ';                            
                        }
                        $txtvlistadetalle2 = substr($txtvlistadetalle2, 0, -2);
                        
                        $txtvFechacreacion = date("Y-m-d");
                        $txtanulado = 0;
                        $txtvfechareg = date("Y-m-d");

                        //validacion pilares
                        if($txtvarea){
                           
                            $varId_pilares = (new \yii\db\Query())
                                ->select(['tbl_plan_gptw.id_pilares'])
                                ->from(['tbl_plan_gptw'])
                                ->where(['=','anulado',0])
                                ->andwhere(['is','tbl_plan_gptw.id_operacion',null])
                                ->andwhere(['=','tbl_plan_gptw.id_area_apoyo',$txtvarea]) 
                                ->andwhere(['=','tbl_plan_gptw.usua_id',$txtvaloradorID])         
                                ->All(); 
                            }else{
                                $varId_pilares = (new \yii\db\Query())
                                ->select(['tbl_plan_gptw.id_pilares'])
                                ->from(['tbl_plan_gptw'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','tbl_plan_gptw.id_operacion',$txtvopera])
                                ->andwhere(['is','tbl_plan_gptw.id_area_apoyo',null])
                                ->andwhere(['=','tbl_plan_gptw.usua_id',$txtvaloradorID])          
                                ->All(); 
                            }
                            $cantidadpilares = 0;
                            foreach ($varId_pilares as $key => $value) {
                                $varidpilares = $value['id_pilares'];
                                $cantidadpilares = $cantidadpilares + count($varidpilares);
                            }
                            $cantidadpilares = $cantidadpilares + count($txtvfocomejora);
                            //die(json_encode($cantidadpilares));

                        //if ($cantidadpilares < 3) {    
               
                            Yii::$app->db->createCommand()->insert('tbl_plan_gptw',[                                            
                                            'id_area_apoyo' => $txtvarea,
                                            'id_operacion' => $txtvopera,
                                            'id_pilares' => $txtvfocomejora2,
                                            'id_detalle_pilar' => $txtvlistadetalle2,                                            
                                            'porcentaje_actual' => $txtvpuntajeactual,
                                            'porcentaje_meta' => $txtvpuntajemeta,
                                            'acciones' => $txtvaccion,
                                            'fecha_registro' => $txtvfechareg,
                                            'fecha_cierre' => $txtvfechacierre,                                                                                      
                                            'responsable_area' => $txtvresponsable,
                                            'estado' => $txtvestado,
                                            'usua_id' => $txtvaloradorID,                                            
                                            'fechacreacion' => $txtvFechacreacion,
                                            'anulado' => $txtanulado,
                                        ])->execute();

                // insertar log
                            Yii::$app->db->createCommand()->insert('tbl_logs', [
                                'usua_id' => Yii::$app->user->identity->id,
                                'usuario' => Yii::$app->user->identity->username,
                                'fechahora' => date('Y-m-d h:i:s'),
                                'ip' => Yii::$app->getRequest()->getUserIP(),
                                'accion' => 'Create',
                                'tabla' => 'tbl_plan_gptw'
                        ])->execute();

                // Insertar tabla detalle
                        $varId_gptw1 = (new \yii\db\Query())
                            ->select(['MAX(tbl_plan_gptw.id_gptw)'])
                            ->from(['tbl_plan_gptw'])
                            ->where(['=','anulado',0])          
                            ->scalar(); 
                        Yii::$app->db->createCommand()->insert('tbl_detalle_plan_gptw',[
                            'id_gptw' => $varId_gptw1,
                            'observaciones' => $txtvobservacion,
                            'fecha_avance' => $txtvfechaavan,
                            'fechacreacion' => date("Y-m-d"),
                            'anulado' => 0,
                            'usua_id' => Yii::$app->user->identity->id,
                            ])->execute();

                        $resp = 1;
                    /*}else{
                        $resp = 2;
                    }*/

                        die(json_encode($resp));

                    
        }       

                    
                    public function actionReporteplanacciongptw(){
                        
                        $sessiones = Yii::$app->user->identity->id;
                        
                        if($sessiones == 3205 || $sessiones == 2953 || $sessiones == 3468 || $sessiones == 7756 || $sessiones == 69 || $sessiones == 6845 || $sessiones == "1290" || $sessiones == "6080" || $sessiones == "8103"){
                            $varListaplangptw = (new \yii\db\Query())
                                                    ->select(['*'])
                                                    ->from(['tbl_plan_gptw'])
                                                    ->orderBy(['id_gptw' => SORT_DESC])
                                                    ->where(['=','anulado',0])          
                                                    ->all();

                            $varListaplangptwrep= (new \yii\db\Query())
                                                    ->select(['tbl_plan_gptw.id_gptw', 'tbl_areasapoyo_gptw.nombre', 'tbl_usuarios_evalua.clientearea', 'tbl_plan_gptw.id_pilares', 'tbl_plan_gptw.id_detalle_pilar', 'tbl_plan_gptw.porcentaje_actual', 'tbl_plan_gptw.porcentaje_meta', 'tbl_plan_gptw.acciones', 'tbl_plan_gptw.fecha_registro', 'tbl_detalle_plan_gptw.fecha_avance', 'tbl_detalle_plan_gptw.observaciones', 'tbl_usuarios.usua_nombre'])
                                                    ->from(['tbl_plan_gptw'])
                                                    ->join('LEFT JOIN', 'tbl_areasapoyo_gptw',
                                                    'tbl_plan_gptw.id_area_apoyo = tbl_areasapoyo_gptw.id_areaapoyo')
                                                    ->join('LEFT JOIN', 'tbl_pilares_gptw',
                                                    'tbl_plan_gptw.id_pilares = tbl_pilares_gptw.id_pilares')
                                                    ->join('LEFT JOIN', 'tbl_usuarios',
                                                    'tbl_plan_gptw.responsable_area = tbl_usuarios.usua_id')
                                                    ->join('LEFT JOIN', 'tbl_detalle_plan_gptw',
                                                    'tbl_plan_gptw.id_gptw = tbl_detalle_plan_gptw.id_gptw')
                                                    ->join('LEFT JOIN', 'tbl_usuarios_evalua',
                                                    'tbl_plan_gptw.id_operacion = tbl_usuarios_evalua.idusuarioevalua')
                                                    ->join('LEFT JOIN', 'tbl_detalle_pilaresgptw',
                                                    'tbl_plan_gptw.id_detalle_pilar = tbl_detalle_pilaresgptw.id_detalle_pilar')
                                                    ->All();
                        }else{
                            $varid_usu = (new \yii\db\Query())
                                                    ->select(['id_operacion', 'id_area_apoyo'])
                                                    ->from(['tbl_plan_gptw'])
                                                    ->orderBy(['id_gptw' => SORT_DESC])
                                                    ->where(['=','anulado',0])
                                                    ->andwhere(['=','usua_id',$sessiones])
                                                    ->limit(1)          
                                                    ->all();
                             $varidopera = null;
                             $varidarea = null;                       
                            foreach ($varid_usu as $key => $value) {
                                     $varidopera = $value['id_operacion'];
                                     $varidarea = $value['id_area_apoyo'];
                                    }
                        if($varidopera){
                        $varListaplangptw = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_plan_gptw'])
                                    ->orderBy(['id_gptw' => SORT_DESC])
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_operacion',$varidopera])
                                    ->all();  
                        }else{
                        $varListaplangptw = (new \yii\db\Query())
                                    ->select(['*'])
                                    ->from(['tbl_plan_gptw'])
                                    ->orderBy(['id_gptw' => SORT_DESC])
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_area_apoyo',$varidarea]) 
                                    ->all();

                        }                                                  

                        $varListaplangptwrep= (new \yii\db\Query())
                                                    ->select(['tbl_plan_gptw.id_gptw', 'tbl_areasapoyo_gptw.nombre', 'tbl_usuarios_evalua.clientearea', 'tbl_plan_gptw.id_pilares', 'tbl_detalle_plan_gptw.id_detallegptw', 'tbl_plan_gptw.porcentaje_actual', 'tbl_plan_gptw.porcentaje_meta', 'tbl_plan_gptw.acciones', 'tbl_plan_gptw.fecha_registro', 'tbl_detalle_plan_gptw.fecha_avance', 'tbl_detalle_plan_gptw.observaciones', 'tbl_usuarios.usua_nombre'])
                                                    ->from(['tbl_plan_gptw'])
                                                    ->join('INNER JOIN', 'tbl_areasapoyo_gptw',
                                                    'tbl_plan_gptw.id_area_apoyo = tbl_areasapoyo_gptw.id_areaapoyo')
                                                    ->join('INNER JOIN', 'tbl_pilares_gptw',
                                                    'tbl_plan_gptw.id_pilares = tbl_pilares_gptw.id_pilares')
                                                    ->join('INNER JOIN', 'tbl_usuarios',
                                                    'tbl_plan_gptw.responsable_area = tbl_usuarios.usua_id')
                                                    ->join('INNER JOIN', 'tbl_detalle_plan_gptw',
                                                    'tbl_plan_gptw.id_gptw = tbl_detalle_plan_gptw.id_gptw')
                                                    ->join('LEFT JOIN', 'tbl_usuarios_evalua',
                                                    'tbl_plan_gptw.id_operacion = tbl_usuarios_evalua.idusuarioevalua')
                                                     ->join('LEFT JOIN', 'tbl_detalle_plan_gptw',
                                                    'tbl_plan_gptw.id_gptw = tbl_detalle_plan_gptw.id_gptw')
                                                    ->where(['=','tbl_plan_gptw.anulado',0])
                                                    ->andwhere(['=','tbl_plan_gptw.id_operacion',$varidopera])
                                                    ->andwhere(['=','tbl_plan_gptw.id_area_apoyo',$varidarea])
                                                    ->All();
                        }                        
                        return $this->render('reporteplanacciongptw',[
                            'varListaplangptw' => $varListaplangptw,
                            'varListaplangptwrep' => $varListaplangptwrep,
                        ]);        
                    }

                    public function actionUpdateplan($id_gptw){
                        $model = new Plangptw();                        
                            
                        $varParamsCodigo = [':txtIdgptw'=>$id_gptw];
                        $varListaplangptw = Yii::$app->db->createCommand("Select tbl_detalle_plan_gptw.id_detallegptw, tbl_detalle_plan_gptw.id_gptw, tbl_detalle_plan_gptw.observaciones, tbl_detalle_plan_gptw.fecha_avance 
                            FROM tbl_detalle_plan_gptw WHERE tbl_detalle_plan_gptw.id_gptw = :txtIdgptw and tbl_detalle_plan_gptw.anulado = 0
                            ")->bindValues($varParamsCodigo )->queryAll();

                     /*   $varListaplangptw = (new \yii\db\Query())
                                                    ->select(['*'])
                                                    ->from(['tbl_plan_gptw'])
                                                    ->andwhere(['=','id_gptw',$id_gptw])   
                                                    ->where(['=','anulado',0])       
                                                    ->all();*/

                        $form = Yii::$app->request->post();
                        if ($model->load($form)) {
                            $txtfechaavance = $model->fecha_avance;
                            $txtobservaciones = $model->observaciones;
                            Yii::$app->db->createCommand()->insert('tbl_detalle_plan_gptw',[
                                    'id_gptw' => $id_gptw,
                                    'observaciones' => $txtobservaciones,
                                    'fecha_avance' => $txtfechaavance,
                                    'fechacreacion' => date("Y-m-d"),
                                    'anulado' => 0,
                                    'usua_id' => Yii::$app->user->identity->id,
                                    ])->execute();
                                    return $this->redirect(array('updateplan','id_gptw'=>$id_gptw)); 
                                }

                        return $this->render('updateplan', [
                          'model'=> $model,                            
                          'id_gptw'=> $id_gptw,
                          'varListaplangptw' => $varListaplangptw,  
                         ]);
                  
                      }
                      public function actionUpdatecargaplan($id_gptw){
                        $model = new Plangptw();
                        $model6 = new UsuariosEvalua();
                        $model3 = new usuarios();
                        $model2 = new Controldetallemomento();
                        $model7 = new DetallesPilaresGptw();
                        $model4 = new areaGptw();
                        
                        //die(json_encode($id_gptw));
                        $variduser = (new \yii\db\Query())
                                    ->select(['responsable_area'])
                                    ->from(['tbl_plan_gptw'])   
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_gptw',$id_gptw])       
                                    ->Scalar();
                                  // die(json_encode($variduser));
                        if($variduser){
                          //  die(json_encode($variduser));
                          //  var_dump($variduser);
                            $model3 = usuarios::findOne($variduser);
                        }
                        $variduser1 = (new \yii\db\Query())
                                    ->select(['id_operacion'])
                                    ->from(['tbl_plan_gptw'])   
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_gptw',$id_gptw])       
                                    ->Scalar();
                        if($variduser1){
                           // die(json_encode($variduser1));
                         //   var_dump($variduser1);
                            $model6 = UsuariosEvalua::findOne($variduser1);
                        }
                        //die(json_encode($variduser1));
                        $varidarea = (new \yii\db\Query())
                                    ->select(['id_area_apoyo'])
                                    ->from(['tbl_plan_gptw'])   
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_gptw',$id_gptw])       
                                    ->Scalar();
                        if($varidarea){            
                           $model4 = areaGptw::findOne($varidarea); 
                        }
                       
                        $varpilares= (new \yii\db\Query())
                                    ->select(['id_pilares'])
                                    ->from(['tbl_plan_gptw'])   
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_gptw',$id_gptw])       
                                    ->Scalar();
                        $listData2 = explode(',',$varpilares);        
                        $model5 = pilaresgptw::find()->where(['id_pilares' => $listData2])->one();
                        $vardetallepilares= (new \yii\db\Query())
                                    ->select(['id_pilares'])
                                    ->from(['tbl_plan_gptw'])   
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_gptw',$id_gptw])       
                                    ->Scalar();
                        $listData3 = explode(',',$vardetallepilares);
                        if($vardetallepilares){        
                           $model7 = DetallesPilaresGptw::find()->where(['id_pilares' => $listData3])->one();
                        }
                       
                        $varListaplangptw = (new \yii\db\Query())
                                                    ->select(['*'])
                                                    ->from(['tbl_plan_gptw'])   
                                                    ->where(['=','anulado',0])
                                                    ->andwhere(['=','id_gptw',$id_gptw])       
                                                    ->all();                       

                        return $this->render('updatecargaplan', [
                          'model'=> $model,
                          'model2'=> $model2,
                          'model3'=> $model3,
                          'model4'=> $model4,
                          'model5'=> $model5,
                          'model6'=> $model6,
                          'model7'=> $model7,                            
                          'id'=> $id_gptw,
                          'varListaplangptw' => $varListaplangptw,  
                         ]);
                  
                      }                    
                     

                    public function actionUpdateplanaccion(){                
                        $txtvaloradorID = Yii::$app->user->identity->id;                       
                        $txtvestado = 'Abierto';
                        $txtvfocomejora2 = null;
                        $txtvlistadetalle2 = null;
                        $txtvidgptw = Yii::$app->request->get("txtvidgptw");
                        $txtvarea = Yii::$app->request->get("txtvarea");
                        $txtvopera = Yii::$app->request->get("txtvopera");
                        $txtvfocomejora = Yii::$app->request->get("txtvfocomejora");
                        for ($i = 0; $i< count($txtvfocomejora); $i++) {
                            $element = $txtvfocomejora[$i];                            
                            $txtvfocomejora2 = $txtvfocomejora2 . $element . ', ';                            
                        }
                        $txtvfocomejora2 = substr($txtvfocomejora2, 0, -2);
                        $txtvpuntajeactual = Yii::$app->request->get("txtvpuntajeactual");
                        $txtvpuntajemeta = Yii::$app->request->get("txtvpuntajemeta");
                        $txtvaccion = Yii::$app->request->get("txtvaccion");
                        $txtvfechareg = Yii::$app->request->get("txtvfechareg");
                        $txtvresponsable = Yii::$app->request->get("txtvresponsable");
                        $txtvlistadetalle = Yii::$app->request->get("txtvlistadetalle");

                        for ($i = 0; $i< count($txtvlistadetalle); $i++) {
                            $element = $txtvlistadetalle[$i];                            
                            $txtvlistadetalle2 = $txtvlistadetalle2 . $element . ', ';                            
                        }
                        $txtvlistadetalle2 = substr($txtvlistadetalle2, 0, -2);
                        
                        $txtvFechacreacion = date("Y-m-d");
                        $txtanulado = 0;
                            
                            Yii::$app->db->createCommand()->update('tbl_plan_gptw',[
                                            'id_area_apoyo' => $txtvarea,
                                            'id_operacion' => $txtvopera,
                                            'id_pilares' => $txtvfocomejora2,
                                            'id_detalle_pilar' => $txtvlistadetalle2,
                                            'porcentaje_actual' => $txtvpuntajeactual,
                                            'porcentaje_meta' => $txtvpuntajemeta,
                                            'acciones' => $txtvaccion,
                                            'fecha_registro' => $txtvfechareg,
                                            'responsable_area' => $txtvresponsable,
                                            'estado' => $txtvestado,
                                            'usua_id' => $txtvaloradorID,                                            
                                            'fechacreacion' => $txtvFechacreacion,
                                            'anulado' => $txtanulado,
                                          ],"id_gptw = '$txtvidgptw'")->execute();                
                           
                            $resp = 1;
                            die(json_encode($resp));
                        }
                        public function actionDeleteacciongptw($id){
                            $paramsEliminar = $id;
                    
                            Yii::$app->db->createCommand('
                                UPDATE tbl_detalle_plan_gptw 
                                    SET anulado = :varAnulado
                                    WHERE 
                                    id_detallegptw = :VarId')
                                ->bindValue(':VarId', $paramsEliminar)
                                ->bindValue(':varAnulado', 1)
                                ->execute();        
                                return $this->redirect(['updateplan']);
                        }
                        public function actionListardetallepilar(){
                            $txtId = Yii::$app->request->get('id');
                            $varStingData = implode(";", $txtId);
                            $varListData = explode(";", $varStingData);
                          
                            if ($txtId) {
                                $varListapilargptw = (new \yii\db\Query())
                                ->select(['tbl_detalle_pilaresgptw.id_detalle_pilar','tbl_pilares_gptw.nombre_pilar','tbl_detalle_pilaresgptw.nombre'])
                                ->from(['tbl_pilares_gptw'])
                                ->join('LEFT JOIN', 'tbl_detalle_pilaresgptw',
                                'tbl_pilares_gptw.id_pilares = tbl_detalle_pilaresgptw.id_pilares')   
                                ->where(['in','tbl_pilares_gptw.id_pilares',$varListData])
                                ->All(); 
                                foreach ($varListapilargptw as $key => $value) {
                                echo "<option value='" . $value['id_detalle_pilar']. "'>" . $value['nombre_pilar']. " - ".$value['nombre']. "</option>";
                                }
                              }
                      
                          }        
        
    }

?>