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
use yii\web\UploadedFile;
use app\models\Controldetallemomento;
use app\models\SpeechParametrizar;
use app\models\Plangptw;
use app\models\Usuarios;
use app\models\UsuariosEvalua;
use app\models\AreaGptw;
use app\models\Pilaresgptw;
use app\models\ProcesosSatisfaccion;
use app\models\UploadForm2;
use app\models\IndicadorSatisfaccion;
use app\models\UsuariosJarviscliente;

    class SatisfaccionclienteController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['updatesatisfaccion', 'agregarsatisfaccion', 'createsatisfaccion','reportesatisfaccion','deleteprocesosatisfac','importardocumento','viewimage','veranexometri','importardocumentoedit'],
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerdirectivo()  || Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isVerDesempeno();
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
            $model3 = new ProcesosSatisfaccion();
            $model4 = new UploadForm2();
            $model5 = new IndicadorSatisfaccion();
            $varidban = 0;
    
               
    
                return $this->render('index', [
                    'model' => $model,
                    'model2' => $model2,
                    'model3' => $model3,
                    'model4' => $model4,
                    'model5' => $model5,
                    'varidban' => $varidban,
                    ]);
        }    
        
                    public function actionCreatesatisfaccion(){
                        $model = new UploadForm2(); 
                        $model3 = new UploadForm2();
                        $txtvaloradorID = Yii::$app->user->identity->id;                       
                        $txtvestado = 'Abierto';
                        $ruta = null;
                        $id = 'cliente';
                        $txtvConcepto = Yii::$app->request->get("txtvConcepto");
                        $txtvAnalisis = Yii::$app->request->get("txtvAnalisis");
                        $txtvarea = Yii::$app->request->get("txtvarea");
                        //$txtvfile = Yii::$app->request->get("txtvFile");
                       // die(json_encode($txtvarea));
                       //   var_dump($txtvarea);
                        // die(json_encode($ruta));
                             //  var_dump($ruta);
                        /*foreach ($txtvfile as $file) {
                            $nombre = $file['name'];
                            die(json_encode($nombre));
                        }*/
                        /*for ($i = 0; $i < $txtvfile.length; $i++) {
                            die(json_encode("Filename " + $txtvfile[$i].name));
                          }*/
                         
                        //$txtvfile1 = substr($txtvfile, 12);
                       
                        //$txtvfile = 'C:\CXM 2022\Diego\cuestionario_carta.pdf';
                        //$model->file = UploadedFile::getInstance($model, $txtvfile);
                 // move_uploaded_file($_FILES[$txtvfile]['tmp_name'], 'images/documentos/archivo.pdf');
                       //$model->file = file($txtvfile);
                       
                        //$model3->file = $txtvfile;
                          
                       /* if ($model3->file && $model3->validate()) {
                            //  var_dump("Ingresa");
                             // foreach ($model->file as $file) {
                                $ruta = 'images/documentos/'."documento_".$id."_".time()."_".$txtvfile;
                              // die(json_encode($ruta));
                             //  var_dump($ruta);
                            // $ruta = "images\/documentos\/documento_cliente_1675869562__5306836254m13762.pdf";
                                $model3->file->saveAs( $ruta ); 
                            //  }
                            }  */
                            
                           /* if ($model->file && $model->validate()) {
                                //  var_dump("Ingresa");
                                  foreach ($model->file as $file) {
                                    $ruta = 'images/documentos/'."documento_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
                                   //$ruta = "images\/documentos\/documento_cliente_1675869562__5306836254m13762.pdf";
                                    //die(json_encode($ruta));
                                  //  var_dump($ruta);
                                    $model->file->saveAs( $ruta ); 
                                  }
                                }*/
                        $txtvopera = Yii::$app->request->get("txtvopera");
                        
                        $txtvAccionseguir = Yii::$app->request->get("txtvAccionseguir");
                        $txtvAccion = Yii::$app->request->get("txtvAccion");
                        $txtvResponsable = Yii::$app->request->get("txtvResponsable");
                        $txtvFechadefine = Yii::$app->request->get("txtvFechadefine");
                        $txtvFechaimplementa = Yii::$app->request->get("txtvFechaimplementa");                        
                        $txtvrEstado = Yii::$app->request->get("txtvrEstado");                        
                        $txtvFechacierre = Yii::$app->request->get("txtvFechacierre");
                        $txtvProceso = Yii::$app->request->get("txtvProceso");
                        $txtvIndicador = Yii::$app->request->get("txtvIndicador");
                        //die(json_encode($txtvIndicador));
                        //  var_dump($txtvIndicador);                        
                        $varPuntajemeta = Yii::$app->request->get("txtvPuntajemeta");                        
                        $varPuntajeactual = Yii::$app->request->get("txtvPuntajeactual");
                        $varPuntajefinal = Yii::$app->request->get("txtvPuntajefinal");
                        //die(json_encode($varPuntajefinal));
                         // var_dump($varPuntajefinal);
                    
                        $txtvFechacreacion = date("Y-m-d");
                        $txtanulado = 0;
                       
                        Yii::$app->db->createCommand()->insert('tbl_satisfaccion_cliente',[                                            
                                            'id_area_apoyo' => $txtvarea,
                                            'id_operacion' => $txtvopera,
                                            'concepto_mejora' => $txtvConcepto,
                                            'analisis_causa' => $txtvAnalisis,
                                            'accion_seguir' => $txtvAccionseguir,
                                            'accion' => $txtvAccion,
                                            'responsable_area' => $txtvResponsable,
                                            'fecha_definicion' => $txtvFechadefine,                                            
                                            'fecha_implementacion' => $txtvFechaimplementa,
                                            'estado' => $txtvestado,
                                            'anexo' => $ruta,
                                            'id_indicador' => $txtvIndicador,
                                            'puntaje_meta' => $varPuntajemeta,
                                            'puntaje_actual' => $varPuntajeactual,
                                            'puntaje_final' => $varPuntajefinal,
                                            'id_proceso_satis' => $txtvProceso,                                            
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
                            'tabla' => 'tbl_satisfaccion_cliente'
                        ])->execute();

                /*// Insertar tabla detalle
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
                            ])->execute();*/

                        $resp = 1;
                        die(json_encode($resp));

                    }                   
                    
                    public function actionReportesatisfaccion(){
                        $txtvaloradorID = Yii::$app->user->identity->id; 
                        $varListasatisfaccion = (new \yii\db\Query())
                                                    ->select(['*'])
                                                    ->from(['tbl_satisfaccion_cliente'])
                                                    ->orderBy(['id_satisfaccion' => SORT_DESC])
                                                    ->where(['=','anulado',0])          
                                                    ->all();

                                                                                             
                        $varListasatisdetalle= (new \yii\db\Query())
                                                    ->select(['tbl_satisfaccion_cliente.id_satisfaccion', 'tbl_areasapoyo_gptw.nombre', 'tbl_usuarios_evalua.clientearea', 'tbl_satisfaccion_cliente.concepto_mejora', 'tbl_satisfaccion_cliente.analisis_causa', 'tbl_satisfaccion_cliente.accion_seguir', 'tbl_satisfaccion_cliente.accion', 'tbl_satisfaccion_cliente.id_indicador', 'tbl_satisfaccion_cliente.puntaje_meta', 'tbl_satisfaccion_cliente.puntaje_actual', 'tbl_satisfaccion_cliente.puntaje_final', 'tbl_usuarios_jarvis_cliente.nombre_completo', 'tbl_usuarios_jarvis_cliente.idusuarioevalua', 'tbl_satisfaccion_cliente.fecha_definicion', 'tbl_satisfaccion_cliente.fecha_implementacion', 'tbl_satisfaccion_cliente.estado','tbl_detalle_satisfacion.eficacia','tbl_detalle_satisfacion.fecha_avance', 'tbl_procesos_satisfaccion_cliente.nombre nombre2','tbl_satisfaccion_archivos.anexo anexo1'])
                                                    ->from(['tbl_satisfaccion_cliente'])
                                                    ->join('LEFT JOIN', 'tbl_areasapoyo_gptw',
                                                    'tbl_satisfaccion_cliente.id_area_apoyo = tbl_areasapoyo_gptw.id_areaapoyo')
                                                    ->join('LEFT JOIN', 'tbl_usuarios_jarvis_cliente',
                                                    'tbl_satisfaccion_cliente.responsable_area = tbl_usuarios_jarvis_cliente.idusuarioevalua')
                                                    ->join('LEFT JOIN', 'tbl_detalle_satisfacion',
                                                    'tbl_satisfaccion_cliente.id_satisfaccion = tbl_detalle_satisfacion.id_satisfaccion')
                                                    ->join('LEFT JOIN', 'tbl_usuarios_evalua',
                                                    'tbl_satisfaccion_cliente.id_operacion = tbl_usuarios_evalua.idusuarioevalua')
                                                    ->join('LEFT JOIN', 'tbl_procesos_satisfaccion_cliente',
                                                    'tbl_satisfaccion_cliente.id_proceso_satis = tbl_procesos_satisfaccion_cliente.id_proceso_satis')
                                                    ->join('LEFT JOIN', 'tbl_satisfaccion_archivos',
                                                    'tbl_satisfaccion_cliente.id_satisfaccion = tbl_satisfaccion_archivos.id_satisfaccion')
                                                    ->where(['=','tbl_satisfaccion_cliente.usua_id',$txtvaloradorID])
                                                    ->All();
                                                    
                        return $this->render('reportesatisfaccion',[
                            'varListasatisfaccion' => $varListasatisfaccion,
                            'varListasatisdetalle' => $varListasatisdetalle,
                        ]);        
                    }

                    public function actionAgregarsatisfaccion($id_satisfac){
                        $model = new Plangptw();         
                        
                        $varParamsCodigo = [':txtId'=>$id_satisfac];
                        $varListasatisfac = Yii::$app->db->createCommand("Select tbl_detalle_satisfacion.id_detallesatisfaccion, tbl_detalle_satisfacion.id_satisfaccion, tbl_detalle_satisfacion.eficacia, tbl_detalle_satisfacion.fecha_avance 
                            FROM tbl_detalle_satisfacion WHERE tbl_detalle_satisfacion.id_satisfaccion = :txtId
                            ")->bindValues($varParamsCodigo )->queryAll();

                        $form = Yii::$app->request->post();
                        if ($model->load($form)) {
                            $txtfechaavance = $model->fecha_avance;
                            $txtobservaciones = $model->observaciones;
                            Yii::$app->db->createCommand()->insert('tbl_detalle_satisfacion',[
                                    'id_satisfaccion' => $id_satisfac,
                                    'eficacia' => $txtobservaciones,
                                    'fecha_avance' => $txtfechaavance,
                                    'fechacreacion' => date("Y-m-d"),
                                    'anulado' => 0,
                                    'usua_id' => Yii::$app->user->identity->id,
                                    ])->execute();
                                    return $this->redirect(array('agregarsatisfaccion','id_satisfac'=>$id_satisfac)); 
                                }

                        return $this->render('agregarsatisfaccion', [
                          'model'=> $model,                            
                          'id_satisfac'=> $id_satisfac,
                          'varListasatisfac' => $varListasatisfac,  
                         ]);
                  
                      }

                      public function actionUpdatesatisfaccion($id_satisfaccion){
                        $model = new Plangptw();
                        $model4 = new areaGptw();
                        $model3 = new UsuariosEvalua();
                        $model6 = new ProcesosSatisfaccion();                       
                        $model7 = new IndicadorSatisfaccion();

                        $model2 = new Controldetallemomento(); 

                        $variduser = (new \yii\db\Query())
                                    ->select(['id_operacion'])
                                    ->from(['tbl_satisfaccion_cliente'])                                       
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','id_satisfaccion',$id_satisfaccion])       
                                    ->Scalar();
                        if($variduser){
                            $model3 = UsuariosEvalua::findOne($variduser);
                        }
                       
                        $varidarea = (new \yii\db\Query())
                                    ->select(['id_area_apoyo'])
                                    ->from(['tbl_satisfaccion_cliente'])   
                                    ->where(['=','anulado',0])                                    
                                    ->andwhere(['=','id_satisfaccion',$id_satisfaccion])       
                                    ->Scalar();
                        if($varidarea){
                             $model4 = areaGptw::findOne($varidarea);
                        }
                     
                        $varindicador= (new \yii\db\Query())
                                    ->select(['id_indicador'])
                                    ->from(['tbl_satisfaccion_cliente'])
                                    ->where(['=','id_satisfaccion',$id_satisfaccion])   
                                    ->andwhere(['=','anulado',0])       
                                    ->Scalar(); 
                        if($varindicador){       
                             $model7 = IndicadorSatisfaccion::findOne($varindicador); 
                        }

                        $varproceso= (new \yii\db\Query())
                                    ->select(['id_proceso_satis'])
                                    ->from(['tbl_satisfaccion_cliente'])
                                    ->where(['=','id_satisfaccion',$id_satisfaccion])   
                                    ->andwhere(['=','anulado',0])       
                                    ->Scalar(); 
                        if($varproceso){       
                             $model6 = ProcesosSatisfaccion::findOne($varproceso); 
                        }

                        $varresponsable_area= (new \yii\db\Query())
                                    ->select(['responsable_area'])
                                    ->from(['tbl_satisfaccion_cliente'])
                                    ->where(['=','id_satisfaccion',$id_satisfaccion])   
                                    ->andwhere(['=','anulado',0])       
                                    ->Scalar();        
                        $model5 = UsuariosJarviscliente::findOne($varresponsable_area);
                        //$model5 = pilaresgptw::findOne($listData2);
                        //die(json_encode($model5));
                        $varListasatisfaccion = (new \yii\db\Query())
                                                    ->select(['*'])
                                                    ->from(['tbl_satisfaccion_cliente'])
                                                    ->where(['=','id_satisfaccion',$id_satisfaccion])   
                                                    ->andwhere(['=','anulado',0])       
                                                    ->all();                       

                        return $this->render('updatesatisfaccion', [
                          'model'=> $model,
                          'model2'=> $model2,
                          'model3'=> $model3,
                          'model4'=> $model4,
                          'model5'=> $model5,
                          'model6'=> $model6,
                          'model7'=> $model7,                           
                          'id'=> $id_satisfaccion,
                          'varListasatisfaccion' => $varListasatisfaccion,  
                         ]);
                  
                      }                  
                     

                    public function actionUpdateplanaccion(){   
                       /* txtvarea : varArea,
                    txtvopera : varOpera,
                    txtvConcepto : varConcepto,
                    txtvAnalisis : varAnalisis,
                    txtvAccionseguir : varAccionseguir,
                    txtvAccion : varAccion,
                    txtvResponsable : varResponsable,
                    txtvFechadefine : varFechadefine,
                    txtvFechaimplementa : varFechaimplementa,
                    txtvrEstado : varEstado,
                    txtvFechacierre : varFechacierre,
                    txtvindicador : varindicador,
                    txtvPuntajemeta : vartPuntajemeta,
                    txtvPuntajeactual : varPuntajeactual,
                    txtvPuntajefinal : varPuntajefinal,*/
                        

                        $txtvaloradorID = Yii::$app->user->identity->id;                       
                        $txtvestado = 'Abierto';
                        
                        $txtvaridsatisfa = Yii::$app->request->get("txtvaridsatisfa");
                        $txtvarea = Yii::$app->request->get("txtvarea");
                        $txtvopera = Yii::$app->request->get("txtvopera");
                        $txtvfocomejora = Yii::$app->request->get("txtvConcepto");
                        $txtvpuntajeactual = Yii::$app->request->get("txtvAnalisis");
                        $txtvpuntajemeta = Yii::$app->request->get("txtvAccionseguir");
                        $txtvaccion = Yii::$app->request->get("txtvaccion");
                        $txtvfechareg = Yii::$app->request->get("txtvFechadefine");
                        $txtvfechaavan = Yii::$app->request->get("txtvFechaimplementa");
                        $txtvfechacierre = Yii::$app->request->get("txtvFechacierre");
                        $txtvobservacion = Yii::$app->request->get("txtvrEstado");
                        $txtvresponsable = Yii::$app->request->get("txtvindicador");
                        $txtvresponsable = Yii::$app->request->get("txtvPuntajemeta");
                        $txtvresponsable = Yii::$app->request->get("txtvPuntajeactual");
                        $txtvresponsable = Yii::$app->request->get("txtvPuntajefinal");                        
                    
                        $txtvFechacreacion = date("Y-m-d");
                        $txtanulado = 0;
                            
                            Yii::$app->db->createCommand()->update('tbl_satisfaccion_cliente',[
                                            'id_operacion' => $txtvarea,
                                            'id_area_apoyo' => $txtvopera,
                                            'concepto_mejora' => $txtvfocomejora,
                                            'analisis_causa' => $txtvpuntajeactual,
                                            'accion_seguir' => $txtvpuntajemeta,
                                            'accion' => $txtvaccion,
                                            'responsable_area' => $txtvfechareg,
                                            'fecha_definicion' => $txtvfechaavan,
                                            'fecha_implementacion' => $txtvfechaavan,
                                            'fecha_cierre' => $txtvfechaavan,
                                            'estado' => $txtvobservacion,
                                            'anexo' => $txtvresponsable,
                                            'id_proceso_satis' => $txtvestado,
                                            'id_indicador' => $txtvestado,
                                            'puntaje_meta' => $txtvestado,
                                            'puntaje_actual' => $txtvestado,
                                            'puntaje_final' => $txtvestado,
                                            'usua_id' => $txtvaloradorID,                                            
                                            'fechacreacion' => $txtvFechacreacion,
                                            'anulado' => $txtanulado,
                                          ],"id_satisfaccion = '$txtvaridsatisfa'")->execute();                
                           
                                          $resp = 1;
                                          die(json_encode($resp));
                    } 

                    public function actionUsuarios_jarvis_cliente(){
                        $sessiones = Yii::$app->user->identity->id;
                        $txtanulado = 0;
                        $txtfechacreacion = date("Y-m-d");
                        $varnombrejefe = null;
                        $varidcargojefe = null;
                        $varcargo = null;
                        
                        Yii::$app->db->createCommand("truncate table tbl_usuarios_jarvis_cliente")->execute();
                  
                        $query = Yii::$app->get('dbjarvis3')->createCommand("Select f.nombre_completo as nombre, a.documento as documento, b.id_dp_cargos as idcargo,
                        b.id_dp_posicion as idposicion,b.id_dp_funciones as idfuncion,c.posicion as posicion,d.funcion as funcion,
                        e.usuario_red as usuariored, g.email_corporativo as correo, a.documento_jefe as documento_jefe,
                        TRIM(ifnull (if (a.id_dp_centros_costos != 0, dg3.nombre_completo, if (a.id_dp_centros_costos_adm != 0, ad.area_general, 'Sin información')), 'Sin información')) AS directorArea,
                        TRIM( if (a.id_dp_centros_costos != 0, cl2.cliente, if (a.id_dp_centros_costos_adm != 0, ad.area_general, 'Sin información'))) AS clienteArea
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
                        
                        LEFT JOIN dp_centros_costos AS cc
                        ON cc.id_dp_centros_costos = a.id_dp_centros_costos
                        
                        LEFT JOIN dp_datos_generales AS dg3
                        ON dg3.documento = cc.documento_director
                        
                        WHERE a.fecha_actual = (SELECT config.valor FROM jarvis_configuracion_general as config WHERE config.nombre = 'mes_activo_dp' )
                        AND a.id_dp_estados NOT IN (305,317,327)
                        AND f.fecha_alta_distribucion <= '2023-01-24'
                        AND c.posicion NOT IN('Aprendiz','Pusher', 'Cliente', 'agente', 'operador')
                        AND d.funcion NOT IN('Operaciónn', 'Visitador')
                        
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
                  
                            Yii::$app->db->createCommand()->insert('tbl_usuarios_jarvis_cliente',[
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
                    public function actionDeleteprocesosatisfac($id){
                        $paramsEliminar = $id;
                
                        Yii::$app->db->createCommand('
                            UPDATE tbl_detalle_plan_gptw 
                                SET anulado = :varAnulado
                                WHERE 
                                id_detallegptw = :VarId')
                            ->bindValue(':VarId', $paramsEliminar)
                            ->bindValue(':varAnulado', 1)
                            ->execute();        
                            return $this->redirect(['viewprocesossatisfaccion']);
                    }

                    public function actionCargadatocc(){ 
                        $txtvarid = Yii::$app->request->post('varid');
                        /*$txtRta = (new \yii\db\Query())
                                ->select(['tbl_roles.role_nombre', 'usua_nombre'])
                                ->from(['tbl_usuarios'])
                                ->join('LEFT JOIN', 'rel_usuarios_roles',
                                    'tbl_usuarios.usua_id = rel_usuarios_roles.rel_usua_id')
                                ->join('LEFT JOIN', 'tbl_roles',
                                    'rel_usuarios_roles.rel_role_id = tbl_roles.role_id')
                                ->where(['=','tbl_usuarios.usua_id',$txtvarid])
                                ->Scalar();*/
                        $txtRta = (new \yii\db\Query())
                            ->select(['tbl_usuarios_jarvis_cliente.posicion'])
                            ->from(['tbl_usuarios_jarvis_cliente'])
                            ->where(['=','tbl_usuarios_jarvis_cliente.idusuarioevalua',$txtvarid])
                            ->Scalar();
                        die(json_encode($txtRta));

                        
                    
                    }       

                    public function actionImportardocumento(){
                        $model1 = new Plangptw();                        
                        $model = new UploadForm2();                        
                        $ruta = null;
                        $id = 'cliente';
                  
                        $form = Yii::$app->request->post();     
                  
                        if($model->load($form)){
                  
                          $model->file = UploadedFile::getInstance($model, 'file');
                          if ($model->file && $model->validate()) {
                         
                            foreach ($model->file as $file) {
                              $ruta = 'images/documentos/'."documento_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
                            
                              $model->file->saveAs( $ruta ); 
                            }
                          } 
                           
                          if ($ruta != null) {
                            $varRutaAnexoBone = (new \yii\db\Query())
                                            ->select(['*'])
                                            ->from(['tbl_satisfaccion_archivos'])
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','anexo',$ruta])
                                            ->count();
                  
                            if ($varRutaAnexoBone == 0) {

                                $varidfinal = (new \yii\db\Query())
                                ->select(['max(id_satisfaccion)'])
                                ->from(['tbl_satisfaccion_cliente'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','usua_id',Yii::$app->user->identity->id])
                                ->Scalar();

                              Yii::$app->db->createCommand()->insert('tbl_satisfaccion_archivos',[
                                        'id_satisfaccion' => $varidfinal,
                                        'anexo' => $ruta,
                                        'fechacreacion' => date('Y-m-d'),
                                        'anulado' => 0,
                                        'usua_id' => Yii::$app->user->identity->id,                                       
                                    ])->execute(); 
                            }
                            return $this->redirect(array('index','varidban'=>0));
                          }else{
                            $ruta = null;
                          }
                          
                  
                        }                  
                        
                        return $this->renderAjax('importardocumento',[
                          'model1' => $model1,
                          'model' => $model,
                          'ruta' => $ruta,
                        ]);
                      }

                      public function actionImportardocumentoedit($varId){
                        $model1 = new Plangptw();                        
                        $model = new UploadForm2();                        
                        $ruta = null;
                        $id = 'cliente';
                  
                        $form = Yii::$app->request->post();     
                  
                        if($model->load($form)){
                  
                          $model->file = UploadedFile::getInstance($model, 'file');
                          if ($model->file && $model->validate()) {
                         
                            foreach ($model->file as $file) {
                              $ruta = 'images/documentos/'."documento_".$id."_".time()."_".$model->file->baseName. ".".$model->file->extension;
                            
                              $model->file->saveAs( $ruta ); 
                            }
                          } 
                           
                          if ($ruta != null) {
                            $varRutaAnexoBone = (new \yii\db\Query())
                                            ->select(['*'])
                                            ->from(['tbl_satisfaccion_archivos'])
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','anexo',$ruta])
                                            ->count();
                  
                            if ($varRutaAnexoBone == 0) {

                                $varidfinal = (new \yii\db\Query())
                                ->select(['max(id_satisfaccion)'])
                                ->from(['tbl_satisfaccion_cliente'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','usua_id',Yii::$app->user->identity->id])
                                ->Scalar();

                                Yii::$app->db->createCommand()->update('tbl_satisfaccion_archivos',[
                                    'anexo' => $ruta,                                  
                                    'usua_id' => Yii::$app->user->identity->id,                                            
                                    'fechacreacion' => date('Y-m-d'),
                                    'anulado' => 0,
                                  ],"id_satisfaccion = '$varId'")->execute();
                              
                            }
                            return $this->redirect(array('updatesatisfaccion','id_satisfaccion'=>$varId));
                          }else{
                            $ruta = null;
                          }
                  
                        }                  
                        
                        return $this->renderAjax('importardocumento',[
                          'model1' => $model1,
                          'model' => $model,
                          'ruta' => $ruta,
                        ]);
                      }

                      
                      public function actionViewimage($varid){
                        $varRuta = null;
                        
                        
                          $varRuta = (new \yii\db\Query())
                            ->select(['anexo'])
                            ->from(['tbl_satisfaccion_archivos'])
                            ->where(['=','anulado',0])
                            ->andwhere(['=','id_satisfaccion',$varid])
                            ->Scalar();
                        
                        return $this->render('viewimage', [
                          'varRuta'=> $varRuta, 
                         ]);
                  
                      }
                      public function actionVeranexometri($id){
                        $model = new UploadForm2();
                        $ruta = null;
                        $ruta = "/images/contratos/bloque1_2_1660677572_Jarvis.png";
                        return $this->renderAjax('veranexometri',[ 
                          'model' => $model,       
                          'ruta' => $ruta,
                        ]);
                      }
        
    }

?>