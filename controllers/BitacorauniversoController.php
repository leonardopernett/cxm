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
use app\models\ProcesosClienteCentrocosto;
use app\models\ProcesosDirectores;
use app\models\Controlmomento;
use app\models\Controldetallemomento;
use app\models\ControlProcesosReportebitacorauni;
use app\models\Controlbitacorauniv;


use app\models\ControlvocBloque1;
use app\models\ControlvocBloque2;
use app\models\Ejecucionfeedbacks;
use app\models\ControlalinearSessionlista;
use app\models\Controlalinearcategorialista;
use app\models\Controlalinearatributolista;
use app\models\ControlalinearParticipantelista;
use app\models\ControlProcesosReportAlinearVOC;
use app\models\SpeechParametrizar;

    class BitacorauniversoController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['listarpcrc','momento','createmomentolis','detallemomento','createdetallemomentolis','listarmomentos','createbitacora','actualbitacora', 'reportebitacorauni', 'editarbitacora', 'updatebitacora'],
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
        
    public function actionIndex(){
                                        
        $model = new SpeechParametrizar();
        $model2 = new Controldetallemomento();  

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

    public function actionListarpcrc(){            
        $txtAnulado = 0; 
        $txtId = Yii::$app->request->post('id');           

        if ($txtId) {
               $txtControl = \app\models\ProcesosClienteCentrocosto::find()->distinct()
                       ->where(['id_dp_clientes' => $txtId])
                       ->count();            

               if ($txtControl > 0) {
                       $varListaPcrc = \app\models\ProcesosClienteCentrocosto::find()
                        ->select(['cod_pcrc','pcrc'])->distinct()
                        ->where(['id_dp_clientes' => $txtId])
                        ->andWhere("anulado = 0")
                        ->andWhere("estado = 1")                            
                        ->orderBy(['cod_pcrc' => SORT_DESC])
                        ->all();            
                    echo "<option value='' disabled selected> Seleccione Centro de Costo...</option>";
                    foreach ($varListaPcrc as $key => $value) {
                           echo "<option value='" . $value->cod_pcrc . "'>" . $value->cod_pcrc." - ".$value->pcrc . "</option>";
                           }
               }else{
                       echo "<option>-</option>";
               }
               }else{
                       echo "<option>Seleccionar...</option>";
               }
            }

            public function actionMomento(){
                $model2 = new Controlmomento();
    
                return $this->renderAjax('createmomento',[
                    'model2' => $model2,
                    ]);
            }
    
            public function actionCreatemomentolis(){            
                $txtName = Yii::$app->request->post("txtName");
                $txtUsua = Yii::$app->request->post("txtName");
                $txtfecha = Yii::$app->request->post("txtFecha");
                $txtanulado = Yii::$app->request->post("txtAnula");
                $txtRta = 1;
                            
                Yii::$app->db->createCommand()->insert('tbl_momento_bit_uni',[
                                    'nombre_momento' => $txtName,
                                    'usua_id' => $txtUsua,
                                    'fechacreacion' => $txtfecha,
                                    'anulado' => $txtanulado,
                                ])->execute();
    
    
                die(json_encode($txtRta));
            }
    
            public function actionDetallemomento(){
                $model3 = new Controldetallemomento();
    
                return $this->renderAjax('createdetallemomento',[
                     'model3' => $model3,
                    ]);
            }
    
            public function actionCreatedetallemomentolis(){
                
                $txtvmomentoid = Yii::$app->request->post("txtvmomentoid");
                $txtvanomdet = Yii::$app->request->post("txtvanomdet");
                $txtvusuaid = Yii::$app->request->post("txtvusuaid");
                $txtfecha = Yii::$app->request->post("txtvfechas");
                $txtanulado = Yii::$app->request->post("txtvanular");
                
                         
                Yii::$app->db->createCommand()->insert('tbl_detalle_momento_bit_uni',[
                                    'id_momento' => $txtvmomentoid,
                                    'detalle_momento' => $txtvanomdet,
                                    'usua_id' => $txtvusuaid,
                                    'fechacreacion' => $txtfecha,
                                    'anulado' => $txtanulado,
                                ])->execute();
    
            $txtRta = 1;
                die(json_encode($txtRta));
            }
    
            public function actionListarmomentos(){            
                $txtAnulado = 0; 
                $txtId = Yii::$app->request->post('id');           
        
                if ($txtId) {
                       $txtControl = \app\models\Controldetallemomento::find()->distinct()
                               ->where(['id_momento' => $txtId])
                               ->count();            
                       // var_dump($txtControl);
                       if ($txtControl > 0) {
                               $varListamomento = \app\models\Controldetallemomento::find()
                                ->select(['id_detalle_momento','detalle_momento'])->distinct()
                                ->where(['id_momento' => $txtId])
                                ->andWhere("anulado = 0")                          
                                ->orderBy(['id_detalle_momento' => SORT_DESC])
                                ->all();            
                            echo "<option value='' disabled selected> Seleccione Motivo...</option>";
                            foreach ($varListamomento as $key => $value) {
                                echo "<option value='" . $value->id_detalle_momento . "'>" . ''.$value->detalle_momento . "</option>";
                                   }
                       }else{
                               echo "<option>-</option>";
                       }
                       }else{
                               echo "<option>Seleccionar...</option>";
                       }
                    }
                    public function actionCargadatocc(){ 
                        $txtIdcc = Yii::$app->request->post('idcentrocos');
                        $txtRta = Yii::$app->db->createCommand("Select ciudad, director_programa, gerente_cuenta FROM tbl_proceso_cliente_centrocosto WHERE cod_pcrc = '$txtIdcc'")->queryAll();
                        die(json_encode($txtRta));
                    
                    }
                    
                    public function actionCreatebitacora(){

                        $txtvaloradorID = Yii::$app->user->identity->id;                       
            
                        $txtvcliente = Yii::$app->request->post("txtvcliente");
                        $txtvcentrocosto = Yii::$app->request->post("txtvcentrocosto");
                        $txtvCiudad = Yii::$app->request->post("txtvCiudad");
                        $txtvDirector = Yii::$app->request->post("txtvDirector");
                        $txtvGerente = Yii::$app->request->post("txtvGerente");
                        $txtvMedio = Yii::$app->request->post("txtvMedio");
                        $txtvCedula = Yii::$app->request->post("txtvCedula");
                        $txtvNombre = Yii::$app->request->post("txtvNombre");
                        $txtvCelular = Yii::$app->request->post("txtvCelular");
                        $txtvFechar = Yii::$app->request->post("txtvFechar");
                        $txtvGrupo = Yii::$app->request->post("txtvGrupo");
                        $txtvNivel = Yii::$app->request->post("txtvNivel");
                        $txtvmomento = Yii::$app->request->post("txtvmomento");
                        $txtvmotivo = Yii::$app->request->post("txtvmotivo");
                        $txtvNombretutor = Yii::$app->request->post("txtvNombretutor");
                        $txtvNombrelider = Yii::$app->request->post("txtvNombrelider");
                        $txtvCaso = Yii::$app->request->post("txtvCaso");
                        $txtvrequiere = Yii::$app->request->post("txtvrequiere");
                        $txtvResponsable = Yii::$app->request->post("txtvResponsable");
                        $txtvFechaesc = Yii::$app->request->post("txtvFechaesc");
                        $txtvFechacierre = Yii::$app->request->post("txtvFechacierre");
                        $txtvestado = Yii::$app->request->post("txtvestado");
                        $txtvRespuestar = Yii::$app->request->post("txtvRespuestar");

                        $txtvFechacreacion = date("Y-m-d");
                        $txtanulado = 0;

                        Yii::$app->db->createCommand()->insert('tbl_bitacora_universo',[
                                            'id_cliente' => $txtvcliente,
                                            'pcrc' => $txtvcentrocosto,
                                            'ciudad' => $txtvCiudad,
                                            'director' => $txtvDirector,
                                            'gerente' => $txtvGerente,
                                            'medio_contacto' => $txtvMedio,
                                            'cedula' => $txtvCedula,
                                            'nombre' => $txtvNombre,
                                            'telefono_movil' => $txtvCelular,
                                            'fecha_registro' => $txtvFechar,
                                            'grupo' => $txtvGrupo,
                                            'nivel_caso' => $txtvNivel,
                                            'id_momento' => $txtvmomento,
                                            'id_detalle_momento' => $txtvmotivo,
                                            'nombre_tutor' => $txtvNombretutor,
                                            'nombre_lider' => $txtvNombrelider,
                                            'descripcion_caso' => $txtvCaso,
                                            'escalamiento' => $txtvrequiere,
                                            'responsable' => $txtvResponsable,
                                            'fecha_escalamiento' => $txtvFechaesc,
                                            'fecha_cierre' => $txtvFechacierre,
                                            'respuesta' => $txtvRespuestar,
                                            'estado' => $txtvestado,
                                            'usua_id' => $txtvaloradorID,                                            
                                            'fecha_creacion' => $txtvFechacreacion,
                                            'anulado' => $txtanulado,
                                        ])->execute();
                        $resp = 1;
                        die(json_encode($resp));

                    }
                    
                    public function actionReportebitacorauni(){
                        $model = new ControlProcesosReportebitacorauni();                                         
                        $model2 = new Controlbitacorauniv(); 
                        $model3 = new SpeechParametrizar(); 

                        $txtIdBlo = null;
                        $formData = Yii::$app->request->post();
                        $arbolid1 = 0;
                        $fechacreacion1 = 0;
                        $valorador_id1 = 0;
                        $tecnico_id1 = 0;
                        
                        //var_dump($formData);
                        $dataProvider = $model->buscarbitacorauni($formData);
                         
                                  
                        
                        return $this->render('reportebitacorauni',[
                            'model' => $model,
                            'model2' => $model2,
                            'model3' => $model3,
                            'dataProvider' => $dataProvider,
                            //'txtIdBloques1' => $arrayUsu,                
                            ]);        
                        }

                    public function actionUpdatebitacora(){

                        $txtvaridbitacora = Yii::$app->request->post("txtvaridbitacora");

                        die(json_encode($txtvaridbitacora));
                    }    

                    public function actionActualbitacora(){                
                            $txtvidbitauni = Yii::$app->request->post("txtvidbitauni");
                            $txtvFechacierre = Yii::$app->request->post("txtvFechacierre");
                            $txtvestado = Yii::$app->request->post("txtvestado");
                            $txtvRespuestar = Yii::$app->request->post("txtvRespuestar");
                            
                            Yii::$app->db->createCommand()->update('tbl_bitacora_universo',[
                                            'fecha_cierre' => $txtvFechacierre,                                
                                            'respuesta' => $txtvRespuestar,
                                            'estado' => $txtvestado,
                                          ],"id_bitacora_uni = '$txtvidbitauni'")->execute();                
                           
                            die(json_encode($txtvidbitauni));
                        }    
            
                    public function actionEditarbitacora($id){
                            $id_bitacora = $id;
                            $txtQuery2 =  new Query;
                            $txtQuery2  ->select(['tbl_proceso_cliente_centrocosto.cliente', 'tbl_proceso_cliente_centrocosto.cod_pcrc','tbl_proceso_cliente_centrocosto.pcrc',                                        'tbl_bitacora_universo.ciudad', 'tbl_bitacora_universo.director', 'tbl_bitacora_universo.gerente', 
                                        'tbl_bitacora_universo.medio_contacto', 'tbl_bitacora_universo.cedula', 'tbl_bitacora_universo.nombre',
                                        'tbl_bitacora_universo.telefono_movil', 'tbl_bitacora_universo.fecha_registro', 'tbl_bitacora_universo.grupo', 'tbl_bitacora_universo.nivel_caso',
                                        'tbl_momento_bit_uni.nombre_momento', 'tbl_detalle_momento_bit_uni.detalle_momento', 'tbl_bitacora_universo.nombre_tutor',
                                        'tbl_bitacora_universo.nombre_lider', 'tbl_bitacora_universo.descripcion_caso','tbl_bitacora_universo.escalamiento',
                                        'tbl_bitacora_universo.responsable', 'tbl_bitacora_universo.fecha_escalamiento', 'tbl_bitacora_universo.id_bitacora_uni'])
                                        ->from('tbl_bitacora_universo')            
                                        ->join('LEFT JOIN', 'tbl_proceso_cliente_centrocosto', 'tbl_bitacora_universo.id_cliente = tbl_proceso_cliente_centrocosto.id_dp_clientes and tbl_bitacora_universo.pcrc = tbl_proceso_cliente_centrocosto.cod_pcrc' )
                                        ->join('LEFT JOIN', 'tbl_momento_bit_uni', 'tbl_bitacora_universo.id_momento = tbl_momento_bit_uni.id_momento')
                                        ->join('LEFT JOIN', 'tbl_detalle_momento_bit_uni', 'tbl_bitacora_universo.id_detalle_momento = tbl_detalle_momento_bit_uni.id_detalle_momento')
                                        ->Where('tbl_bitacora_universo.id_bitacora_uni ='.$id_bitacora.'');
                           
                            $command = $txtQuery2->createCommand();
                            $dataProvider = $command->queryAll();

                            //$varValorador = Yii::$app->db->createCommand("select valorador_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
                            return $this->render('editarbitacora', ['dataprovider' => $dataProvider]);
                            
                        }

        
        
    }

?>