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
use app\models\SpeechCategorias;
use app\models\SpeechServicios;
use app\models\FormvocAcciones;
use app\models\FormvocBloque1;


    class FormulariovocController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['index','createformvoc','crearacciones','createfocalizada', 'createfocalizadapart2','reportformvoc','formlistavoc', 'downloadlist'],
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
            $model = new SpeechCategorias();

            $data = Yii::$app->request->post();
            if ($model->load($data)) {
                $txtvarUsua = $model->usua_id;
                $txtvarClient = $model->programacategoria;
                $txtvarService = $model->cod_pcrc;

                return $this->redirect(array('createformvoc','varUsua'=>$txtvarUsua, 'varClient'=>$txtvarClient, 'varService'=>$txtvarService));
            }  

            return $this->render('index',[
                'model' => $model,
                ]);
        }

        public function actionCreateformvoc($varUsua, $varClient, $varService){
            $txtUsuario = $varUsua;
            $txtCliente = $varClient;
            $txtServicio = $varService;
            $model = new SpeechCategorias();

            return $this->render('createformvoc',[
                'txtUsuario' => $txtUsuario,
                'txtCliente' => $txtCliente,
                'txtServicio' => $txtServicio,
                'model' => $model,
                ]);
        }

        public function actionListarpcrcindex(){            
            $txtId = Yii::$app->request->post('id');                       

            if ($txtId) {
                $txtControl = \app\models\ProcesosVolumendirector::find()->distinct()
                            ->select(['tbl_procesos_volumendirector.cod_pcrc','tbl_procesos_volumendirector.pcrc'])->distinct()
                      ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_procesos_volumendirector.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                      ->join('LEFT OUTER JOIN', 'tbl_speech_categorias',
                                  'tbl_speech_parametrizar.cod_pcrc = tbl_speech_categorias.cod_pcrc')
                            ->where(['tbl_procesos_volumendirector.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_procesos_volumendirector.anulado = 0")
                            ->andwhere("tbl_procesos_volumendirector.estado = 1") 
                            ->andwhere("tbl_speech_categorias.anulado = 0")  
                            ->count();            

                if ($txtControl > 0) {
                  $varListaPcrc = \app\models\ProcesosVolumendirector::find()
                      ->select(['tbl_procesos_volumendirector.cod_pcrc','tbl_procesos_volumendirector.pcrc'])->distinct()
                      ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_procesos_volumendirector.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')
                      ->join('LEFT OUTER JOIN', 'tbl_speech_categorias',
                                  'tbl_speech_parametrizar.cod_pcrc = tbl_speech_categorias.cod_pcrc')
                            ->where(['tbl_speech_parametrizar.id_dp_clientes' => $txtId])
                            ->andwhere("tbl_procesos_volumendirector.anulado = 0")
                            ->andwhere("tbl_procesos_volumendirector.estado = 1") 
                            ->andwhere("tbl_speech_categorias.anulado = 0")                             
                            ->orderBy(['tbl_procesos_volumendirector.cod_pcrc' => SORT_DESC])
                            ->all();            
                    
                    foreach ($varListaPcrc as $key => $value) {
                        echo "<option value='" . $value->cod_pcrc. "'>" . $value->cod_pcrc." - ".$value->pcrc . "</option>";
                    }
                }else{
                    echo "<option>-</option>";
                }
            }else{
                    echo "<option>No hay datos</option>";
            }

        }

        public function actionListarvariables(){
            $txtId = Yii::$app->request->post('id');

            if ($txtId) {
                $txtNombre = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias    where anulado = 0 and idspeechcategoria = $txtId")->queryScalar();
                $txtCodpcrc = Yii::$app->db->createCommand("select distinct cod_pcrc from tbl_speech_categorias    where anulado = 0 and idspeechcategoria = $txtId")->queryScalar();
                $txtControl = Yii::$app->db->createCommand("select count(idcategoria) from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and tipoindicador in ('$txtNombre') and cod_pcrc in ('$txtCodpcrc')")->queryScalar();                

                if ($txtControl > 0) {
                    $varListaPcrc =  Yii::$app->db->createCommand("select idspeechcategoria, nombre from tbl_speech_categorias where anulado = 0 and idcategorias = 2 and tipoindicador in ('$txtNombre') and cod_pcrc in ('$txtCodpcrc')")->queryAll();
                    
                    foreach ($varListaPcrc as $key => $value) {
                        echo "<option value='" . $value['idspeechcategoria']. "'>" . $value['nombre'] . "</option>";
                    }
                }else{
                    echo "<option>-</option>";
                }

            }else{
                    echo "<option>No hay datos</option>";
            }

        }

        public function actionCrearacciones(){
            $model = new FormvocAcciones();

            $data = Yii::$app->request->post();
            if ($model->load($data)) {

                $varidaccion = $model->idacciones;
                $varaccion = $model->acciones;
                $varsessiones = Yii::$app->user->identity->id;
                $varfechacreacion = date("Y-m-d");

                if ($varidaccion == "1") {
                    $variddetalle = 4;
                }else{
                    if ($varidaccion == "2") {
                        $variddetalle = $model->iddetalle;
                    }else{
                        if ($varidaccion == "3") {
                            $variddetalle = 4;
                        }else{
                            if ($varidaccion == "4") {
                                $variddetalle = 4;
                            }
                        }
                    }
                }

                Yii::$app->db->createCommand()->insert('tbl_formvoc_acciones',[
                                'idacciones' => $varidaccion,
                                'iddetalle' => $variddetalle,
                                'acciones' => $varaccion,
                                'anulado' => 0,
                                'usua_id' => $varsessiones,
                                'fechacreacion' => $varfechacreacion,
                            ])->execute(); 

                return $this->redirect('index');
            } 

            return $this->renderAjax('crearacciones',[
                'model' => $model,
                ]);
        }

        public function actionCreatefocalizada(){
            $txtvaloradorID = Yii::$app->user->identity->id;

            $txtPcrc = Yii::$app->request->get("txtPcrc");
            $txtCXM = Yii::$app->db->createCommand("select distinct arbol_id from tbl_speech_servicios where anulado = 0 and id_dp_clientes = $txtPcrc")->queryScalar();
            $txtcodpcrc = Yii::$app->request->get("txtcodpcrc");
            $txtNompcrc = Yii::$app->db->createCommand("select distinct pcrc from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$txtcodpcrc')")->queryScalar();
            $txtIDExtSp = Yii::$app->request->get("txtIDExtSp");
            $txtFechaHora = Yii::$app->request->get("txtFechaHora");
            $txtUsuAge = Yii::$app->request->get("txtUsuAge");
            $txtDuracion = Yii::$app->request->get("txtDuracion");
            $txtExtencion = Yii::$app->request->get("txtExtencion");
            $txtDimension = Yii::$app->request->get("txtDimension");
            $txtValoraddo = Yii::$app->request->get("txtValoraddo");

            $txtvFechacreacion = date("Y-m-d");
            $txtanulado = 0;
            $txtVRta = null;
            $txthola = 0;

            $txtVRta = Yii::$app->db->createCommand("select count(idpcrccxm) from tbl_formvoc_bloque1 where anulado = 0 and idpcrccxm =  $txtCXM and idpcrcspeech = $txtPcrc and cod_pcrc in ('$txtcodpcrc') and idvalorado = $txtValoraddo and fechahora in ('$txtFechaHora') and fechacreacion = '$txtvFechacreacion'")->queryScalar();

            if ($txtVRta == 0 || $txtVRta == null) {
                Yii::$app->db->createCommand()->insert('tbl_formvoc_bloque1',[
                                'idpcrccxm' => $txtCXM,
                                'idpcrcspeech' => $txtPcrc,
                                'cod_pcrc' => $txtcodpcrc,
                                'pcrc' => $txtNompcrc,
                                'idvalorado' => $txtValoraddo,
                                'idspeech' => $txtIDExtSp,
                                'fechahora' => $txtFechaHora,
                                'usuarioagente' => $txtUsuAge,
                                'duracions' => $txtDuracion,
                                'extension' => $txtExtencion, 
                                'dimensionform' => $txtDimension,
                                'anulado' => $txtanulado,
                                'usua_id' => $txtvaloradorID,
                                'fechacreacion' => $txtvFechacreacion, 
                            ])->execute();
                $txthola = 1;
            }

            die(json_encode($txthola));
        }

        public function actionCreatefocalizadapart2(){

            $txtPcrc = Yii::$app->request->get("txtPcrc");
            $txtCXM = Yii::$app->db->createCommand("select distinct arbol_id from tbl_speech_servicios where anulado = 0 and id_dp_clientes = $txtPcrc")->queryScalar();
            $txtcodpcrc = Yii::$app->request->get("txtcodpcrc");
            $txtFechaHora = Yii::$app->request->get("txtFechaHora");
            $txtValoraddo = Yii::$app->request->get("txtValoraddo");

            $txtIndiGlo = Yii::$app->request->get("txtIndiGlo");
            $txtVariable = Yii::$app->request->get("txtVariable");
            $txtMotivoC = Yii::$app->request->get("txtMotivoC");
            $txtMotivoL = Yii::$app->request->get("txtMotivoL");
            $txtPuntoD = Yii::$app->request->get("txtPuntoD");
            $txtCategori = Yii::$app->request->get("txtCategorizada");
            $txtAjusteC = Yii::$app->request->get("txtAjusteC");
            $txtPorcentajeAfe = Yii::$app->request->get("txtPorcentajeAfe");
            $txtAgente = Yii::$app->request->get("txtAgente");
            $txtMarca = Yii::$app->request->get("txtMarca");
            $txtCanal = Yii::$app->request->get("txtCanal");
            $txtDcualitativo = Yii::$app->request->get("txtDcualitativo");
            $txtatributos = Yii::$app->request->get("txtatributos");
            $txtMapa1 = Yii::$app->request->get("txtMapa1");
            $txtMapa2 = Yii::$app->request->get("txtMapa2");
            $txtMapa3 = Yii::$app->request->get("txtMapa3");

            $txtvFechacreacion = date("Y-m-d");
            $txtanulado = 0;
            $txthola = 0;

             $txtvIdBloque = Yii::$app->db->createCommand("select idformvocbloque1 from tbl_formvoc_bloque1 where anulado = 0 and idpcrccxm =  $txtCXM and idpcrcspeech = $txtPcrc and cod_pcrc in ('$txtcodpcrc') and idvalorado = $txtValoraddo and fechahora in ('$txtFechaHora') and fechacreacion = '$txtvFechacreacion'")->queryScalar();

            Yii::$app->db->createCommand()->insert('tbl_formvoc_bloque2',[
                                'idformvocbloque1' => $txtvIdBloque,
                                'indicadorglobal' => $txtIndiGlo,
                                'variable' => $txtVariable,
                                'moticocontacto' => $txtMotivoC,
                                'motivollamadas' => $txtMotivoL,
                                'puntodolor' => $txtPuntoD,
                                'categoria' => $txtCategori,
                                'ajustecategoia' => $txtAjusteC,
                                'indicadorvar' => $txtPorcentajeAfe,
                                'agente' => $txtAgente,
                                'marca' => $txtMarca,
                                'canal' => $txtCanal,
                                'detalle' => $txtDcualitativo,
                                'mapa1' => $txtMapa1,
                                'mapa2' => $txtMapa2,
                                'interesados' => $txtMapa3,
                                'responsabilidad' => $txtatributos,
                                'fechacreacion' => $txtvFechacreacion,
                                'anulado' => $txtanulado,
                            ])->execute();  

            $txthola = 1;  

            die(json_encode($txthola));
        }

        public function actionReportformvoc(){
            $model = new FormvocBloque1();

            $dataProvider = $model->buscarformvoc(Yii::$app->request->post());  

             $txtidpcrcspeech = $model->idpcrcspeech;
             $txtcod_pcrc = $model->cod_pcrc;
         $txtusua_id = $model->usua_id;
         $txtidvalorado = $model->idvalorado;
         $txtfechar = $model->fechahora;  
             $arrayUsu[] = 0;
             $arrayUsu[] = $txtidpcrcspeech;
             $arrayUsu[] = $txtcod_pcrc;
         $arrayUsu[] = $txtusua_id;
         $arrayUsu[] = $txtidvalorado;
         $arrayUsu[] = $txtfechar;

            return $this->render('reportformvoc',[
                'model' => $model,
                'dataProvider' => $dataProvider,
        'datalista' => $arrayUsu,
                ]);
        }

        public function actionFormlistavoc($id){
            // Primer Bloque
            $txtNombreArbol = Yii::$app->db->createCommand("select a.name from tbl_arbols a inner join tbl_formvoc_bloque1 fb1 on a.id = fb1.idpcrccxm where fb1.idformvocbloque1 = $id and fb1.anulado = 0")->queryScalar();
            $varcodpcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_formvoc_bloque1 where idformvocbloque1 = $id and anulado = 0")->queryScalar();
            $varpcrc = Yii::$app->db->createCommand("select pcrc from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            $txtServicio = $varcodpcrc.' - '.$varpcrc;
            $txtNombreValorado = Yii::$app->db->createCommand("select name from tbl_evaluados inner join tbl_formvoc_bloque1 on tbl_evaluados.id = tbl_formvoc_bloque1.idvalorado where         tbl_formvoc_bloque1.idformvocbloque1 =  $id")->queryScalar();
            $txtSpeech = Yii::$app->db->createCommand("select idspeech from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            $txtFechaHor = Yii::$app->db->createCommand("select fechahora from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            $txtUsers = Yii::$app->db->createCommand("select usuarioagente from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            $txtextension = Yii::$app->db->createCommand("select extension from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            $txtDuraciones = Yii::$app->db->createCommand("select duracions from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            $txtDimensiones = Yii::$app->db->createCommand("select dimensionform from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();

            // Segundo Bloque
            $varIndicador = Yii::$app->db->createCommand("select indicadorglobal from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            $txtIndicador = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varIndicador")->queryScalar();

            $varVariable = Yii::$app->db->createCommand("select variable from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            $txtVariable = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varVariable")->queryScalar();

            $txtatributos = Yii::$app->db->createCommand("select fa.acciones from tbl_formvoc_acciones fa inner join tbl_formvoc_bloque2 fb2  on fa.idformvocacciones = fb2.responsabilidad where fb2.anulado = 0 and fb2.idformvocbloque1 = $id")->queryScalar();
            if ($txtatributos == null) {
                $txtatributos = "Sin registros";
            }

            $varMotivo = Yii::$app->db->createCommand("select moticocontacto from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varMotivo != null) {
                $txtMotivos = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varMotivo")->queryScalar();
            }else{
                $txtMotivos = "Sin registro";
            }            

            $varDetalle = Yii::$app->db->createCommand("select motivollamadas from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varDetalle != null) {
                $txtDetalles = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varDetalle")->queryScalar();
            }else{
                $txtDetalles = "Sin registro";
            }
            
            $varCategoria = Yii::$app->db->createCommand("select categoria from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varCategoria == 1) {
                $txtCategoria = "Si esta categorizada";
            }else{
                $txtCategoria = Yii::$app->db->createCommand("select ajustecategoia from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            }

            $txtPorcentaje = Yii::$app->db->createCommand("select indicadorvar from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();

            $varAgente = Yii::$app->db->createCommand("select agente from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varAgente != null) {
                $txtAgente = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varAgente")->queryScalar();
            }else{
                $txtAgente = "Sin registro";
            }  

            $varMarca = Yii::$app->db->createCommand("select marca from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varMarca != null) {
                $txtMarca = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMarca")->queryScalar();
            }else{
                $txtMarca = "Sin registro";
            } 

            $varCanal = Yii::$app->db->createCommand("select canal from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varCanal != null) {
                $txtCanal = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varCanal")->queryScalar();
            }else{
                $txtCanal = "Sin registro";
            }  

            $varMapa1 = Yii::$app->db->createCommand("select mapa1 from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varMapa1 != null) {
                $txtMapa1 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa1")->queryScalar();
            }else{
                $txtMapa1 = "Sin registro";
            }

            $varMapa2 = Yii::$app->db->createCommand("select mapa2 from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varMapa2 != null) {
                $txtMapa2 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa2")->queryScalar();
            }else{
                $txtMapa2 = "Sin registro";
            }

            $varMapa3 = Yii::$app->db->createCommand("select interesados from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();
            if ($varMapa3 != null) {
                $txtMapa3 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa3")->queryScalar();
            }else{
                $txtMapa3 = "Sin registro";
            }

            $txtDetalleCuali = Yii::$app->db->createCommand("select detalle from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar();

            $txtPuntodDolor = Yii::$app->db->createCommand("select puntodolor from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $id")->queryScalar(); 
            if ($txtPuntodDolor == null) {
                $txtPuntodDolor = "Sin registro";
            }

            return $this->render('formlistavoc',[
                'txtNombreArbol' => $txtNombreArbol,
                'txtServicio' => $txtServicio,
                'txtNombreValorado' => $txtNombreValorado,
                'txtSpeech' => $txtSpeech,
                'txtFechaHor' => $txtFechaHor,
                'txtUsers' => $txtUsers,
                'txtextension' => $txtextension,
                'txtDuraciones' => $txtDuraciones,
                'txtDimensiones' => $txtDimensiones,
                'txtIndicador' => $txtIndicador,
                'txtVariable' => $txtVariable,
                'txtatributos' => $txtatributos,
                'txtMotivos' => $txtMotivos,
                'txtDetalles' => $txtDetalles,
                'txtCategoria' => $txtCategoria,
                'txtPorcentaje' => $txtPorcentaje,
                'txtAgente' => $txtAgente,
                'txtMarca' => $txtMarca,
                'txtCanal' => $txtCanal,
                'txtMapa1' => $txtMapa1,
                'txtMapa2' => $txtMapa2,
                'txtMapa3' => $txtMapa3,
                'txtDetalleCuali' => $txtDetalleCuali,
                'txtPuntodDolor' => $txtPuntodDolor,
                'id' => $id,
                ]);
        }

    public function actionDownloadlist(array $datos){
            $txtidbloque = $datos;
            return $this->renderAjax('downloadlist',[
                'txtidbloque' => $txtidbloque,
                ]);
        }

        public function actionDownloadparameters($idform){
            // Primer Bloque
            $txtNombreArbol = Yii::$app->db->createCommand("select a.name from tbl_arbols a inner join tbl_formvoc_bloque1 fb1 on a.id = fb1.idpcrccxm where fb1.idformvocbloque1 = $idform and fb1.anulado = 0")->queryScalar();
            $varcodpcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_formvoc_bloque1 where idformvocbloque1 = $idform and anulado = 0")->queryScalar();
            $varpcrc = Yii::$app->db->createCommand("select pcrc from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtServicio = $varcodpcrc.' - '.$varpcrc;
            $txtNombreValorador = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios u inner join tbl_formvoc_bloque1 fb on u.usua_id = fb.usua_id where fb.idformvocbloque1 = $idform")->queryScalar();
            $txtNombreValorado = Yii::$app->db->createCommand("select u.name from tbl_evaluados u inner join tbl_formvoc_bloque1 fb on u.id = fb.idvalorado where fb.idformvocbloque1 = $idform")->queryScalar();
            $txtSpeech = Yii::$app->db->createCommand("select idspeech from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtFechaHor = Yii::$app->db->createCommand("select fechahora from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtUsers = Yii::$app->db->createCommand("select usuarioagente from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtextension = Yii::$app->db->createCommand("select extension from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtDuraciones = Yii::$app->db->createCommand("select duracions from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtDimensiones = Yii::$app->db->createCommand("select dimensionform from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
	    $txtfechavalora = Yii::$app->db->createCommand("select fechacreacion from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();


            // Segundo Bloque
            $varIndicador = Yii::$app->db->createCommand("select indicadorglobal from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtIndicador = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varIndicador")->queryScalar();

            $varVariable = Yii::$app->db->createCommand("select variable from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtVariable = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varVariable")->queryScalar();

            $txtatributos = Yii::$app->db->createCommand("select puntodolor from tbl_formvoc_bloque2 fb2  where fb2.anulado = 0 and fb2.idformvocbloque1 = $idform")->queryScalar();
            if ($txtatributos == null) {
                $txtatributos = "Sin registros";
            }

            $varMotivo = Yii::$app->db->createCommand("select moticocontacto from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMotivo != null) {
                $txtMotivos = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varMotivo")->queryScalar();
            }else{
                $txtMotivos = "Sin registro";
            }            

            $varDetalle = Yii::$app->db->createCommand("select motivollamadas from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varDetalle != null) {
                $txtDetalles = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varDetalle")->queryScalar();
            }else{
                $txtDetalles = "Sin registro";
            }
            
            $varCategoria = Yii::$app->db->createCommand("select categoria from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varCategoria == 1) {
                $txtCategoria = "Si esta categorizada";
            }else{
                $txtCategoria = Yii::$app->db->createCommand("select ajustecategoia from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            }

            $txtPorcentaje = Yii::$app->db->createCommand("select indicadorvar from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();

            $varAgente = Yii::$app->db->createCommand("select agente from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varAgente != null) {
                $txtAgente = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varAgente")->queryScalar();
            }else{
                $txtAgente = "Sin registro";
            }  

            $varMarca = Yii::$app->db->createCommand("select marca from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMarca != null) {
                $txtMarca = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMarca")->queryScalar();
            }else{
                $txtMarca = "Sin registro";
            } 

            $varCanal = Yii::$app->db->createCommand("select canal from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varCanal != null) {
                $txtCanal = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varCanal")->queryScalar();
            }else{
                $txtCanal = "Sin registro";
            }  

            $varMapa1 = Yii::$app->db->createCommand("select mapa1 from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMapa1 != null) {
                $txtMapa1 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa1")->queryScalar();
            }else{
                $txtMapa1 = "Sin registro";
            }

            $varMapa2 = Yii::$app->db->createCommand("select mapa2 from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMapa2 != null) {
                $txtMapa2 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa2")->queryScalar();
            }else{
                $txtMapa2 = "Sin registro";
            }

            $varMapa3 = Yii::$app->db->createCommand("select interesados from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMapa3 != null) {
                $txtMapa3 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa3")->queryScalar();
            }else{
                $txtMapa3 = "Sin registro";
            }

            $txtDetalleCuali = Yii::$app->db->createCommand("select detalle from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();

            return $this->renderAjax('downloadparameters',[
                'idform' => $idform,
                'txtNombreArbol' => $txtNombreArbol,
                'txtServicio' => $txtServicio,
                'txtNombreValorador' => $txtNombreValorador,
                'txtNombreValorado' => $txtNombreValorado,
                'txtSpeech' => $txtSpeech,
                'txtFechaHor' => $txtFechaHor,
                'txtUsers' => $txtUsers,
                'txtextension' => $txtextension,
                'txtDuraciones' => $txtDuraciones,
                'txtDimensiones' => $txtDimensiones,
                'txtIndicador' => $txtIndicador,
                'txtVariable' => $txtVariable,
                'txtatributos' => $txtatributos,
                'txtMotivos' => $txtMotivos,
                'txtDetalles' => $txtDetalles,
                'txtCategoria' => $txtCategoria,
                'txtPorcentaje' => $txtPorcentaje,
                'txtAgente' => $txtAgente,
                'txtMarca' => $txtMarca,
                'txtCanal' => $txtCanal,
                'txtMapa1' => $txtMapa1,
                'txtMapa2' => $txtMapa2,
                'txtMapa3' => $txtMapa3,
                'txtDetalleCuali' => $txtDetalleCuali,
		'txtfechavalora' => $txtfechavalora,
                ]);
        }



    }

?>