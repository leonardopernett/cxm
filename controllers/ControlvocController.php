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
use app\models\ControlvocBloque1;
use app\models\ControlvocBloque2;
use app\models\Ejecucionfeedbacks;
use app\models\ControlvocSessionlista;
use app\models\ControlvocListadopadre;
use app\models\ControlvocListadohijo;
use app\models\ControlProcesosVOC;

    class ControlvocController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['indexvoc','evaluadolistmultiple','createvoc','sessionesvoc','listadosvoc','createlistavoc','motivovoc', 'createmotivo','listashijo','createfocalizada','getarboles','lidereslist','usuariolist','reportevoc','verlistasvoc','datosformulariosvoc','updatevoc','eliminarvoc','editarvocp','editarvoch','crearfeedback'],
                        'rules' => [
                            [
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo();
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
            $model = new ControlvocBloque1();   

            $data = Yii::$app->request->post();
            if ($model->load($data)) {
                $txtPcrc = $model->arbol_id;
                return $this->redirect(array('indexvoc','arbol_idV'=>$txtPcrc));
            }  

            return $this->render('index', [
                'model' => $model,
                ]);
    }

        public function actionIndexvoc($arbol_idV){
            $model = new ControlvocBloque1();
            $varArbol = $arbol_idV;

            $data = Yii::$app->request->post();

            if ($model->load($data)) {
                $txtPcrc = $model->arbol_id;
                $txtValorador = $model->tecnico_id;
                $txtDimensions = $model->dimensions;
                return $this->redirect(array('createvoc','arbol_idV'=>$txtPcrc,'tecnico_idV'=>$txtValorador,'dimensionsV'=>$txtDimensions));
            }            

            return $this->render('indexvoc', [
                'model' => $model,
                'varArbol' => $varArbol,
                ]);
        }

        public function actionCreatevoc($arbol_idV, $tecnico_idV, $dimensionsV){
            $model = new ControlvocBloque1();

            $txtPcrcS = $arbol_idV;
            $txtValoradorS = $tecnico_idV;
            $txtDimensionS = $dimensionsV;

            $txtNomPcrc = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtPcrcS'")->queryScalar();

            $txtNomValora = Yii::$app->db->createCommand("select name from tbl_evaluados where id = '$txtValoradorS'")->queryScalar();

            return $this->render('createvoc', [
                'model' => $model,
                'txtPcrcS' => $txtPcrcS,
                'txtNomPcrc' => $txtNomPcrc,
                'txtValoradorS' => $txtValoradorS,
                'txtNomValora' => $txtNomValora,
                'txtDimensionS' => $txtDimensionS,
                ]);
        }

        /**
             * Obtiene el listado de evaluados (funcion copia de actionEvaluadolist, se modifica el elseif
             * para que reciba varios ids )
             * @param type $search
             * @param type $id
        */
        public function actionEvaluadolistmultiple($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                            ->where('name LIKE "%' . $search . '%"')
                            ->orderBy('name')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                            ->where('tbl_evaluados.id IN (' . $id . ')')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
        }


        public function actionIndexfeedback($valoradoid, $varatributo){
            $model = new Ejecucionfeedbacks();
            $varvaloradoid = $valoradoid;
            $txtAtributo = $varatributo;
            

            return $this->renderAjax('createfeedback',[
                'model' => $model,
                'varvaloradoid' => $varvaloradoid,
                'txtAtributo' => $txtAtributo,
                ]);
        }

	public function actionCrearfeedback(){

            $txtvarid_causas = Yii::$app->request->get("txtvarid_causas");
            $txtvartipo_id = Yii::$app->request->get("txtvartipo_id");
            $txtvarcomen_id = Yii::$app->request->get("txtvarcomen_id");
            $txtvarvalorado = Yii::$app->request->get("txtvarvalorado");

            if ($txtvartipo_id == "") {
                $txtvartipo_id = 0;
            }

            $txtRta = Yii::$app->db->createCommand("select equipo_id from tbl_equipos_evaluados where evaluado_id = '$txtvarvalorado'")->queryScalar();

            $txtLider = Yii::$app->db->createCommand("select usua_id from tbl_equipos where id = '$txtRta'")->queryScalar();

            Yii::$app->db->createCommand()->insert('tbl_ejecucionfeedbacks',[
                                'tipofeedback_id' => $txtvartipo_id,
                                'usua_id_lider' => $txtLider,
                                'evaluado_id' => $txtvarvalorado,
                                'dscomentario' => $txtvarcomen_id,
                                'dscausa_raiz' => $txtvarid_causas,
                                'usua_id' => Yii::$app->user->identity->id,
                                'created' => date("Y-m-d H:i:s"),
                            ])->execute(); 

            die(json_encode(count($txtLider)));
        }

        public function actionSessionesvoc(){
            $model2 = new ControlvocSessionlista(); 

            return $this->renderAjax('createsesiones',[
                'model2' => $model2,
                ]);
        }

        public function actionCreatesesionvoc(){            
            $txtName = Yii::$app->request->post("txtName");
            $txtfecha = Yii::$app->request->post("txtFecha");
            $txtanulado = Yii::$app->request->post("txtAnula");
            $txtRta = 1;
                        
            Yii::$app->db->createCommand()->insert('tbl_controlvoc_sessionlista',[
                                'nombresession' => $txtName,
                                'fechacreacion' => $txtfecha,
                                'anulado' => $txtanulado,
                            ])->execute();


            die(json_encode($txtRta));
        }

        public function actionListadosvoc(){
            $model3 = new ControlvocListadopadre();
            $model4 = new ControlvocListadohijo();
            return $this->renderAjax('createlistados',[
                'model3' => $model3,
                'model4' => $model4,
                ]);
        }

        public function actionCreatelistavoc(){
            $txtvsesion1 = Yii::$app->request->post("txtvsesion");
            $txtvarbol1 = Yii::$app->request->post("txtvarbol");
            $txtvname1 = Yii::$app->request->post("txtvname");
            $txtvfechas1 = Yii::$app->request->post("txtvfechas");
            $txtvanular1 = Yii::$app->request->post("txtvanular");
            $txtRta = 1;

            Yii::$app->db->createCommand()->insert('tbl_controlvoc_listadopadre',[
                                'idsessionvoc' => $txtvsesion1,
                                'nombrelistap' => $txtvname1,
                                'arbol_id' => $txtvarbol1,
                                'fechacreacion' => $txtvfechas1,
                                'anulado' => $txtvanular1,
                            ])->execute();


            die(json_encode($txtRta));
        }


        public function actionMotivovoc($txtArbol){            
            $model4 = new ControlvocListadohijo();
            $vartxtArbol = $txtArbol;

            return $this->renderAjax('createlisth',[
                'model4' => $model4,
                'vartxtArbol' => $vartxtArbol,
                ]);
        }

        public function actionCreatemotivo(){
            $txtvsesion1 = Yii::$app->request->post("txtvsesion");
            $txtvarbol1 = Yii::$app->request->post("txtvarbol");
            $txtvname1 = Yii::$app->request->post("txtvname");
            $txtvfechas1 = Yii::$app->request->post("txtvfechas");
            $txtvanular1 = Yii::$app->request->post("txtvanular");
            $txtRta = 1;


            Yii::$app->db->createCommand()->insert('tbl_controlvoc_listadohijo',[
                                'idsessionvoc' => $txtvsesion1,
                                'idlistapadrevoc' => $txtvarbol1,
                                'nombrelistah' => $txtvname1,
                                'fechacreacion' => $txtvfechas1,
                                'anulado' => $txtvanular1,
                            ])->execute();


            die(json_encode($txtRta));
        }

        public function actionListashijo(){
            $txttxtvmotivo = Yii::$app->request->post("txtvmotivo");

            $txtRta = Yii::$app->db->createCommand("select idlistahijovoc, nombrelistah from tbl_controlvoc_listadohijo where anulado = 0 and idlistapadrevoc = '$txttxtvmotivo'")->queryAll();

            $arrayUsu = array();
            foreach ($txtRta as $key => $value) {
                array_push($arrayUsu, array("idlistahijovoc"=>$value['idlistahijovoc'],"nombrelistah"=>$value['nombrelistah']));
            }

            die(json_encode($arrayUsu));
        }

        public function actionCreatefocalizada(){
            $txtvaloradorID = Yii::$app->user->identity->id;

            $txtvArbol = Yii::$app->request->post("txtvArbol");
            $txtvValorado = Yii::$app->request->post("txtvValorado");
            $txtvSpeech = Yii::$app->request->post("txtvSpeech");
            $txtvFH = Yii::$app->request->post("txtvFH");
            $txtvAgenteu = Yii::$app->request->post("txtvAgenteu");
            $txtvDuracion = Yii::$app->request->post("txtvDuracion");
            $txtvExt = Yii::$app->request->post("txtvExt");
            $txtvDimension = Yii::$app->request->post("txtvDimension");
            $txtvLider = Yii::$app->request->post("txtvLider");

            $txtvIndicadorG = Yii::$app->request->post("txtvIndicadorG");
            $txtvVariable  = Yii::$app->request->post("txtvVariable");
            $txtvMotivoC   = Yii::$app->request->post("txtvMotivoC");
            $txtvMotivoL   = Yii::$app->request->post("txtvMotivoL");
            $txtvPuntoD    = Yii::$app->request->post("txtvPuntoD");
            $txtvCategoria = Yii::$app->request->post("txtvCategoria");
            $txtvAjusteC   = Yii::$app->request->post("txtvAjusteC");
            $txtvIndicador = Yii::$app->request->post("txtvIndicador");
            $txtvAgente    = Yii::$app->request->post("txtvAgente");
            $txtvMarca     = Yii::$app->request->post("txtvMarca");
            $txtvCanal     = Yii::$app->request->post("txtvCanal");
            $txtvDetalle   = Yii::$app->request->post("txtvDetalle");
            $txtvMapa1     = Yii::$app->request->post("txtvMapa1");
            $txtvMapa2     = Yii::$app->request->post("txtvMapa2");
        $txtvInteresados = Yii::$app->request->post("txtvInteresados");
            $txtvResponsabilidad = Yii::$app->request->post("txtvResponsabilidad");


            $txtvFechacreacion = date("Y-m-d");
            $txtanulado = 0;

            $txtVRta = null;

            Yii::$app->db->createCommand()->insert('tbl_controlvoc_bloque1',[
                                'valorador_id' => $txtvaloradorID,
                                'arbol_id' => $txtvArbol,
                                'dimensions' => $txtvDimension,
                                'lider_id' => $txtvLider,
                                'tecnico_id' => $txtvValorado,
                                'numidextsp' => $txtvSpeech,
                                'fechahora' => $txtvFH,
                                'usuagente' => $txtvAgenteu,
                                'duracion' => $txtvDuracion,
                                'extencion' => $txtvExt,
                                'fechacreacion' => $txtvFechacreacion,
                                'anulado' => $txtanulado,
                            ])->execute();

            $txtVRta = Yii::$app->db->createCommand("select count(*) from tbl_controlvoc_bloque1 where valorador_id = '$txtvaloradorID' and arbol_id = '$txtvArbol' and tecnico_id = '$txtvValorado' and anulado = 0")->queryScalar();

            $txtvIdBloque = Yii::$app->db->createCommand("select idbloque1 from tbl_controlvoc_bloque1 where valorador_id = '$txtvaloradorID' and arbol_id = '$txtvArbol' and tecnico_id = '$txtvValorado' and anulado = 0")->queryScalar();

            Yii::$app->db->createCommand()->insert('tbl_controlvoc_bloque2',[
                                'idbloque1' => $txtvIdBloque,
                                'indicadorglobal' => $txtvIndicadorG,
                                'variable' => $txtvVariable,
                                'moticocontacto' => $txtvMotivoC,
                                'motivollamadas' => $txtvMotivoL,
                                'puntodolor' => $txtvPuntoD,
                                'categoria' => $txtvCategoria,
                                'ajustecategoia' => $txtvAjusteC,
                                'indicadorvar' => $txtvIndicador,
                                'agente' => $txtvAgente,
                                'marca' => $txtvMarca,
                                'canal' => $txtvCanal,
                                'detalle' => $txtvDetalle,
                                'mapa1' => $txtvMapa1,
                                'mapa2' => $txtvMapa2,
                'interesados' => $txtvInteresados,
                                'responsabilidad' => $txtvResponsabilidad,
                                'fechacreacion' => $txtvFechacreacion,
                                'anulado' => $txtanulado,
                            ])->execute();

            die(json_encode($txtVRta));
        }

        public function actionReportevoc(){
            $model = new ControlProcesosVOC();
            $formData = Yii::$app->request->post();
            $txtValorador = null;
            $txtArbol_id = null;
	    $txtFechacreacion = null;
	    $txtTecnico = null;

            if ($model->load($formData)) {
                $dataProvider = $model->buscarVoc($formData);
                               
            }else{
                $dataProvider = $model->buscarVoc($formData);                                               
            }

             $txtValorador = $model->valorador_id;
             $txtArbol_id = $model->arbol_id;
	     $txtFechacreacion = $model->fechacreacion;
	     $txtTecnico = $model->tecnico_id; 
             $arrayUsu[] = 0;
             $arrayUsu[] = $txtValorador;
             $arrayUsu[] = $txtArbol_id;
	     $arrayUsu[] = $txtFechacreacion;
	     $arrayUsu[] = $txtTecnico;
            $arrayPara[] = 0
                     
            
            return $this->render('reportevoc',[
                'model' => $model,
                'dataProvider' => $dataProvider,
                'txtIdBloques1' => $arrayUsu,                
                ]);        }

        public function actionGetarboles($search = null, $id = null) {
                $out = ['more' => false];
                $user_id = Yii::$app->user->identity->id;
                if (!is_null($search)) {
                    $data = \app\models\TmpreportesArbol::find()
                            ->join("JOIN", "rel_grupos_usuarios", "rel_grupos_usuarios.usuario_id = tbl_tmpreportes_arbol.usua_id")
                            ->join("JOIN", "tbl_permisos_grupos_arbols", "tbl_tmpreportes_arbol.arbol_id = tbl_permisos_grupos_arbols.arbol_id")
                            ->select(['id' => 'tbl_tmpreportes_arbol.seleccion_arbol_id', 'text' => 'UPPER(tbl_tmpreportes_arbol.dsruta_arbol)'])
                            ->distinct()
                            ->where([
                                "tbl_tmpreportes_arbol.usua_id" => $user_id,
                                "tbl_permisos_grupos_arbols.snver_grafica" => 1])
                            ->andWhere("rel_grupos_usuarios.grupo_id = tbl_permisos_grupos_arbols.grupousuario_id")
                            ->andWhere("tbl_tmpreportes_arbol.seleccion_arbol_id = tbl_tmpreportes_arbol.arbol_id")
                            ->andWhere('tbl_tmpreportes_arbol.dsruta_arbol LIKE "%' . $search . '%" ')
                            ->orderBy("tbl_tmpreportes_arbol.dsruta_arbol ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\TmpreportesArbol::find()
                            ->join("JOIN", "rel_grupos_usuarios", "rel_grupos_usuarios.usuario_id = tbl_tmpreportes_arbol.usua_id")
                            ->join("JOIN", "tbl_permisos_grupos_arbols", "tbl_tmpreportes_arbol.arbol_id = tbl_permisos_grupos_arbols.arbol_id")
                            ->select(['id' => 'tbl_tmpreportes_arbol.seleccion_arbol_id', 'text' => 'UPPER(tbl_tmpreportes_arbol.dsruta_arbol)'])
                            ->distinct()
                            ->where([
                                "tbl_tmpreportes_arbol.usua_id" => $user_id,
                                "tbl_permisos_grupos_arbols.snver_grafica" => 1])
                            ->andWhere("rel_grupos_usuarios.grupo_id = tbl_permisos_grupos_arbols.grupousuario_id")
                            ->andWhere("tbl_tmpreportes_arbol.seleccion_arbol_id = tbl_tmpreportes_arbol.arbol_id")
                            ->andWhere('tbl_tmpreportes_arbol.seleccion_arbol_id IN (' . $id . ')')
                            ->orderBy("tbl_tmpreportes_arbol.dsruta_arbol ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
        }     

        public function actionLidereslist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Equipos::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->join('JOIN', 'tbl_usuarios', 'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                            ->where('usua_nombre LIKE "%' . $search . '%"')
                            ->groupBy('id')
                            ->orderBy('usua_nombre')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Equipos::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->join('JOIN', 'tbl_usuarios', 'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                            ->where('tbl_usuarios.usua_id = ' . $id)
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
        } 

        public function actionUsuariolist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => true];
                if (!is_null($search)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_nombre LIKE "%' . $search . '%"')
                            ->orderBy('usua_nombre')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_id IN (' . $id . ')')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
        }  

        public function actionVerlistasvoc($id){
            $txtIdVoc = $id;

            $varValorador = Yii::$app->db->createCommand("select valorador_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtNomvalorador = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varValorador")->queryScalar(); 
            $varArbol = Yii::$app->db->createCommand("select arbol_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = $varArbol and activo = 0")->queryScalar(); 
            $varTecnico = Yii::$app->db->createCommand("select tecnico_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtNombreTecnico = Yii::$app->db->createCommand("select name from tbl_evaluados where id = $varTecnico")->queryScalar(); 
            $txtDimensiones = Yii::$app->db->createCommand("select dimensions from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtFecha = Yii::$app->db->createCommand("select fechahora from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtDureacion = Yii::$app->db->createCommand("select duracion from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtExtension = Yii::$app->db->createCommand("select extencion from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtSpeech = Yii::$app->db->createCommand("select numidextsp from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();

            $varIndiGlo = Yii::$app->db->createCommand("select indicadorglobal from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtIndiGlo = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varIndiGlo'")->queryScalar();
            if ($varIndiGlo == 0){
            $txtIndiGlo = 'N/A';
            }
            $varVariable = Yii::$app->db->createCommand("select variable from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtVariable = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varVariable'")->queryScalar();
            if ($varVariable == 0){
            $txtVariable = 'N/A';
            }
            $varMotivoContacto = Yii::$app->db->createCommand("select moticocontacto from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar();  
            $txtMotivoContacto = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varMotivoContacto'")->queryScalar();
            if ($varMotivoContacto == 0){
            $txtMotivoContacto = 'N/A';
            }            
            $varMotivoLlamada = Yii::$app->db->createCommand("select motivollamadas from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtMotivoLlamada = Yii::$app->db->createCommand("select nombrelistah from tbl_controlvoc_listadohijo where idlistahijovoc = '$varMotivoLlamada'")->queryScalar();
            if ($varMotivoLlamada == 0){
            $txtMotivoLlamada = 'N/A';
            }
            $varPuntoDolor = Yii::$app->db->createCommand("select puntodolor from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtPuntoDolor = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varPuntoDolor'")->queryScalar();
            if ($varPuntoDolor == 0){
            $txtPuntoDolor = 'N/A';
            }
            $varLlamadaCategorizada = Yii::$app->db->createCommand("select categoria from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            if ($varLlamadaCategorizada != '1') {
                $txtLlamadaCategorizada = "Si";
            }else{
                $txtLlamadaCategorizada = "No";
            }
            if ($varLlamadaCategorizada == 0){
            $txtLlamadaCategorizada = 'N/A';
            }

            $txtPorcentaje = Yii::$app->db->createCommand("select indicadorvar from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $varAgente = Yii::$app->db->createCommand("select agente from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtAgente = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varAgente'")->queryScalar();
            if ($varAgente == 0){
            $txtAgente = 'N/A';
            }
            $varMarca = Yii::$app->db->createCommand("select marca from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtMarca = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varMarca'")->queryScalar(); 
            if ($varMarca == 0){
            $txtMarca = 'N/A';
            }
            $varCanal = Yii::$app->db->createCommand("select canal from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtCanal = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varCanal'")->queryScalar(); 
            if ($varCanal == 0){
            $txtCanal = 'N/A';
            }
            $txtDcualitativos = Yii::$app->db->createCommand("select detalle from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            
            $varMapaInteresados1 = Yii::$app->db->createCommand("select mapa1 from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtMapaInteresados1 = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varMapaInteresados1'")->queryScalar(); 
            if ($varMapaInteresados1 == 0){
            $txtMapaInteresados1 = 'N/A';
            }
            $varMapaInteresados2 = Yii::$app->db->createCommand("select mapa2 from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtMapaInteresados2 = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varMapaInteresados2'")->queryScalar();
            if ($varMapaInteresados2 == 0){
            $txtMapaInteresados2 = 'N/A';
            }
            $varatributos = Yii::$app->db->createCommand("select interesados from tbl_controlvoc_bloque2 where idbloque1 = '$txtIdVoc'")->queryScalar(); 
            $txtatributos = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$varatributos'")->queryScalar();  
            if ($varatributos == 0){
            $txtatributos = 'N/A';
            }
            return $this->render('verlistasvoc',[
                'txtIdVoc' => $txtIdVoc,
                'txtNomvalorador' => $txtNomvalorador,
                'txtArbol' => $txtArbol,
                'txtNombreTecnico' => $txtNombreTecnico,
                'txtDimensiones' => $txtDimensiones,
                'txtFecha' => $txtFecha,
                'txtAgente' => $txtAgente,
                'txtDureacion' => $txtDureacion,
                'txtExtension' => $txtExtension,
                'txtNomvalorador' => $txtNomvalorador,
                'txtSpeech' => $txtSpeech,
                'txtIndiGlo' => $txtIndiGlo,
                'txtVariable' => $txtVariable,
                'txtMotivoContacto' => $txtMotivoContacto,
                'txtMotivoLlamada' => $txtMotivoLlamada,
                'txtPuntoDolor' => $txtPuntoDolor,
                'txtLlamadaCategorizada' => $txtLlamadaCategorizada,
                'txtPorcentaje' => $txtPorcentaje,
                'txtAgente' => $txtAgente,
                'txtMarca' => $txtMarca,
                'txtCanal' => $txtCanal,
                'txtDcualitativos' => $txtDcualitativos,
                'txtMapaInteresados1' => $txtMapaInteresados1,
                'txtMapaInteresados2' => $txtMapaInteresados2,
                'txtatributos' => $txtatributos,
                ]);
        }
//Diego ini
         public function actionDatosformulariosvoc(array $idbloque1){

            $txtidbloque = $idbloque1;          
                    
            return $this->renderAjax('datosformulariosvoc',[
                    'txtidbloque'=>$txtidbloque,                       
                        ]);            
        }
//Diego fin


        public function actionUpdatevoc($txtPcrc){
            $model = new ControlvocListadopadre();
            $vartxtPcrc = $txtPcrc;
            $formData = Yii::$app->request->post();
            $varSession = null;

            if ($model->load($formData)) {
                $varSession = $model->nombrelistap;
            }

            return $this->render('updatevoc',[
                'model' => $model,
                'vartxtPcrc' => $vartxtPcrc,
                'varSession' => $varSession,
                ]);
        }

        public function actionEliminarvoc(){
            $varIdList = Yii::$app->request->post("var_Idlist");
            $varSesiones = Yii::$app->request->post("var_Sesiones");
            $varPCRC = Yii::$app->request->post("var_Pcrc");
            
            if ($varSesiones != '4') {		
                Yii::$app->db->createCommand("delete from tbl_controlvoc_listadopadre where idsessionvoc = '$varSesiones' and arbol_id = '$varPCRC' and anulado = 0 and idlistapadrevoc = '$varIdList'")->execute();
            }else{
                Yii::$app->db->createCommand("delete from tbl_controlvoc_listadohijo where idsessionvoc = '$varSesiones' and anulado = 0 and idlistahijovoc = '$varIdList'")->execute();
            }

            $rta = 1;
            die(json_encode($rta));
        }  

        public function actionEditarvocp($var_pcrc,$var_IdList){
            $txtIdList = $var_IdList;
            $txtPcrc = $var_pcrc;

            $txtNombreList = Yii::$app->db->createCommand("select nombrelistap from tbl_controlvoc_listadopadre where idlistapadrevoc = '$txtIdList' and anulado = 0")->queryScalar(); 

            $model = $this->findModel($var_IdList);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
                return $this->redirect(['index']);
            }

            return $this->render('editarvocp',[
                'model' => $model,
                'txtNombreList' => $txtNombreList,
                'txtIdList' => $txtIdList,
                'txtPcrc' => $txtPcrc,
                ]);
        }
        protected function findModel($var_IdList){
            if (($model = ControlvocListadopadre::findOne($var_IdList)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } 

        public function actionEditarvoch($var_pcrc,$var_IdList){
            $txtIdList = $var_IdList;
            $txtPcrc = $var_pcrc;


            $txtNombreList = Yii::$app->db->createCommand("select nombrelistah from tbl_controlvoc_listadohijo where idlistahijovoc = '$txtIdList' and anulado = 0")->queryScalar(); 


            $model = $this->findModel2($var_IdList);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
                return $this->redirect(['index']);
            }

            return $this->render('editarvoch',[
                'model' => $model,
                'txtNombreList' => $txtNombreList,
                'txtIdList' => $txtIdList,
                'txtPcrc' => $txtPcrc,
                ]);
        }
        protected function findModel2($var_IdList){
            if (($model = ControlvocListadohijo::findOne($var_IdList)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } 


    }

?>