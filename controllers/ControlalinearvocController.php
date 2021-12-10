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
use app\models\ControlalinearSessionlista;
use app\models\Controlalinearcategorialista;
use app\models\Controlalinearatributolista;
use app\models\ControlalinearParticipantelista;
use app\models\ControlProcesosReportAlinearVOC;

    class ControlalinearvocController extends \yii\web\Controller {

        public function behaviors(){
            return[
                'access' => [
                        'class' => AccessControl::classname(),
                        'only' => ['indexvoc'],
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
                $txtsesion = $model->extencion;
                return $this->redirect(array('createalinearvoc','arbol_idV'=>$txtPcrc,'tecnico_idV'=>$txtValorador,'dimensionsV'=>$txtDimensions, 'sesionV'=>$txtsesion));
            }            

            return $this->render('indexvoc', [
                'model' => $model,
                'varArbol' => $varArbol,
                ]);
        }

        public function actionCreatealinearvoc($arbol_idV, $tecnico_idV, $dimensionsV, $sesionV){
            $model = new ControlvocBloque1();
            $model3 = new ControlalinearParticipantelista();

            $txtPcrcS = $arbol_idV;
            $txtValoradorS = $tecnico_idV;
            $txtDimensionS = $dimensionsV;
            $txtsesionS = $sesionV;

            $txtNomPcrc = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtPcrcS'")->queryScalar();

            $txtNomValora = Yii::$app->db->createCommand("select name from tbl_evaluados where id = '$txtValoradorS'")->queryScalar();

            return $this->render('createalinearvoc', [
                'model' => $model,
                'txtPcrcS' => $txtPcrcS,
                'txtNomPcrc' => $txtNomPcrc,
                'txtValoradorS' => $txtValoradorS,
                'txtNomValora' => $txtNomValora,
                'txtDimensionS' => $txtDimensionS,
                'model2' => $model3,
                'txtsesionS' => $txtsesionS,
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
            $txtTipoC = Yii::$app->request->post("txtTipoF");
            $txtEval = Yii::$app->request->post("txtEval");
            $txtComentario = Yii::$app->request->post("txtComent");

            $txtRta = Yii::$app->db->createCommand("select equipo_id from tbl_equipos_evaluados where evaluado_id = '$txtEval'")->queryScalar();

            $txtLider = Yii::$app->db->createCommand("select usua_id from tbl_equipos where id = '$txtRta'")->queryScalar();

            Yii::$app->db->createCommand()->insert('tbl_ejecucionfeedbacks',[
                                'tipofeedback_id' => $txtTipoC,
                                'usua_id_lider' => $txtLider,
                                'evaluado_id' => $txtEval,
                                'dscomentario' => $txtComentario,
                                'usua_id' => Yii::$app->user->identity->id,
                                'created' => date("Y-m-d H:i:s"),
                            ])->execute(); 

            die(json_encode($txtEval));
        }

        public function actionSessionesalinear(){
            $model2 = new ControlalinearSessionlista();

            return $this->renderAjax('createsesionalinear',[
                'model2' => $model2,
                ]);
        }

        public function actionCreatesesionalinearvoc(){            
            $txtName = Yii::$app->request->post("txtName");
            $txtfecha = Yii::$app->request->post("txtFecha");
            $txtanulado = Yii::$app->request->post("txtAnula");
            $txtRta = 1;
                        
            Yii::$app->db->createCommand()->insert('tbl_sesion_alinear',[
                                'sesion_nombre' => $txtName,
                                'fechacreacion' => $txtfecha,
                                'anulado' => $txtanulado,
                            ])->execute();


            die(json_encode($txtRta));
        }

        public function actionParticipantealinear(){
            $model3 = new ControlalinearParticipantelista();

            return $this->renderAjax('createalinearparticipavoc',[
                 'model3' => $model3,
                ]);
        }

        public function actionCreateparticipantealinearvoc(){
            $txtName = Yii::$app->request->post("txtName");
            $txtfecha = Yii::$app->request->post("txtFecha");
            $txtanulado = Yii::$app->request->post("txtAnula");
            $txtRta = 1;
                        
            Yii::$app->db->createCommand()->insert('tbl_participantes',[
                                'participan_nombre' => $txtName,
                                'fechacreacion' => $txtfecha,
                                'anulado' => $txtanulado,
                            ])->execute();

            die(json_encode($txtRta));
        }

    public function actionCategoriaalinear(){
            $model3 = new Controlalinearcategorialista();

            return $this->renderAjax('createalinearcategoriavoc',[
                 'model3' => $model3,
                ]);
        }

        public function actionCreatecategoriaalinearvoc(){
            $txtvsesion1 = Yii::$app->request->post("txtvsesion");
            $txtvarbol1 = Yii::$app->request->post("txtvarbol");
            $txtName = Yii::$app->request->post("txtvname");
            $txtfecha = Yii::$app->request->post("txtvfechas");
            $txtanulado = Yii::$app->request->post("txtvanular");
            
                     
            Yii::$app->db->createCommand()->insert('tbl_categorias_alinear',[
                'sesion_id' => $txtvsesion1,
                'arbol_id' => $txtvarbol1,
                                'categoria_nombre' => $txtName,
                                'fechacreacion' => $txtfecha,
                                'anulado' => $txtanulado,
                            ])->execute();

        $txtRta = 1;
            die(json_encode($txtRta));
        }

        public function actionAtributoalinear($idAbol){

            $varidAbol = $idAbol;
            $model3 = new Controlalinearatributolista();

            return $this->renderAjax('createalinearatributovoc',[
                 'model3' => $model3,                                                 
                 'idAbol' => $varidAbol,
                ]);
        }

        public function actionCreateatributoalinearvoc(){
            $txtvsesion1 = Yii::$app->request->post("txtvsesion");
            $txtName = Yii::$app->request->post("txtvname");
            $txtfecha = Yii::$app->request->post("txtvfechas");
            $txtanulado = Yii::$app->request->post("txtvanular");
            
                     
            Yii::$app->db->createCommand()->insert('tbl_atributos_alinear',[
                'id_categ_ali' => $txtvsesion1,
                                'atributo_nombre' => $txtName,
                                'fechacreacion' => $txtfecha,
                                'anulado' => $txtanulado,
                            ])->execute();

        $txtRta = 1;
            die(json_encode($txtRta));
        }

         public function actionMediralinear($idAbol, $sesionId, $categoriaId, $modalhide, $idvalora){
            $model3 = new Controlalinearatributolista();
                   

            $varidAbol = $idAbol;
            $varsesionId = $sesionId;
            $varcategoriaId = $categoriaId;
            $varmodalhide = $modalhide;
            $varidvalora = $idvalora;

            return $this->renderAjax('createmediratributovoc',[
                 'model3' => $model3,
                 'idAbol' => $varidAbol,
                 'sesionId' => $varsesionId,
                 'categoriaId' => $varcategoriaId,
                 'modalhide' => $varmodalhide,
                 'modalhide' => $varmodalhide,
                 'idvalora' => $varidvalora,
                ]);
        }

        public function actionCreatemediratributoalinearvoc(){


           $arraydatos = (array)json_decode(Yii::$app->request->post("data"));

           foreach ($arraydatos as $key => $value) {
                $value = (array)$value;              
        $txtvacuerdo = $value['varAcuerdo'];
                $txtvmedir = $value['varMedir'];         
                $txtvatributo = $value['varIdatrubuti'];
                $txtidvalorado = $value['varIdvalorado'];
                $txtfechai = $value['varfechai'];            
                $txtanulado = $value['varanulado'];

               $txtRta = Yii::$app->db->createCommand()->insert('tbl_medir_alinear',[
                                    'id_atrib_alin' => $txtvatributo,
                                    'id_idvalorado' => $txtidvalorado,
                                    'medicion' => $txtvmedir,
                                    'acuerdo' => $txtvacuerdo,
                                    'fechacreacion' => $txtfechai,
                                    'anulado' => $txtanulado,
                                ])->execute(); 
                if(!$txtRta){
                    die(json_encode($txtRta));
                }else{
                    #code
                }
            }


            die(json_encode($txtRta));
        }


        public function actionMotivovoc(){            
            $model4 = new ControlvocListadohijo();

            return $this->renderAjax('createlisth',[
                'model4' => $model4,
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


            $txtSesion1 = Yii::$app->request->post("txtSesion");
            $txtConcepto_mejora = Yii::$app->request->post("txtConcepto_mejora");
            $txtAnalisis_causa  = Yii::$app->request->post("txtAnalisis_causa");
            $txtAccion_seguir   = Yii::$app->request->post("txtAccion_seguir");
            $txtTipo_accion   = Yii::$app->request->post("txtTipo_accion");
            $txtResponsable    = Yii::$app->request->post("txtResponsable");
            $txtFecha_plan = Yii::$app->request->post("txtFecha_plan");
            $txtFecha_implementa   = Yii::$app->request->post("txtFecha_implementa");
            $txtEstado = Yii::$app->request->post("txtEstado");
            $txtObservaciones     = Yii::$app->request->post("txtObservaciones");

            $txtIdparticipa     = Yii::$app->request->post("txtIdparticipa");


            $txtvFechacreacion = date("Y-m-d");
            $txtanulado = 0;


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
 
            

            $txtvIdBloque = Yii::$app->db->createCommand("select max(idbloque1) from tbl_controlvoc_bloque1 where valorador_id = '$txtvaloradorID' and arbol_id = '$txtvArbol' and tecnico_id = '$txtvValorado' and anulado = 0")->queryScalar();
                Yii::$app->db->createCommand()->update('tbl_medir_alinear',[
                               'idbloque1' => $txtvIdBloque,
                            ],"id_idvalorado = '$txtvValorado' and fechacreacion = '$txtvFechacreacion' and idbloque1 is null and anulado = 0")->execute();

            Yii::$app->db->createCommand()->insert('tbl_control_alinear_bloque3',[
                                'idbloque1' => $txtvIdBloque,
                                'concepto_mejora' => $txtConcepto_mejora,
                                'analisis_causa' => $txtAnalisis_causa,
                                'accion_seguir' => $txtAccion_seguir,
                                'tipo_accion' => $txtTipo_accion,
                                'responsable' => $txtResponsable,
                                'fecha_plan' => $txtFecha_plan,
                                'fecha_implementa' => $txtFecha_implementa,
                                'estado' => $txtEstado,
                                'observaciones' => $txtObservaciones,
                                'sesion_id' => $txtSesion1,                               
                                'fechacreacion' => $txtvFechacreacion,
                                'anulado' => $txtanulado,
                            ])->execute();

            Yii::$app->db->createCommand()->update('tbl_medir_alinear',[
                               'idbloque1' => $txtvIdBloque,
                            ],"id_idvalorado = '$txtvValorado' and fechacreacion = '$txtvFechacreacion' and idbloque1 is null")->execute();

             Yii::$app->db->createCommand()->insert('tbl_control_alinear_participa',[
                                'idbloque1' => $txtvIdBloque,
                                'codigo_partcipa' => $txtIdparticipa,                              
                                'fechacreacion' => $txtvFechacreacion,
                                'anulado' => $txtanulado,
                            ])->execute();
        die(json_encode($txtvFechacreacion));
            Yii::$app->db->createCommand()->update('tbl_medir_alinear',[
                             'idbloque1' => $txtvIdBloque,
              ],"id_idvalorado = '$txtvValorado' and fechacreacion = '$txtvFechacreacion' and idbloque1 is null")->execute();        
           
        }

        public function actionReportealinearvoc(){
            $model = new ControlProcesosReportAlinearVOC();
            $formData = Yii::$app->request->post();
            $txtValorador = null;
            $txtArbol_id = null;
            $txtFechacreacion = null;
            $txtTecnico = null;
          
            if ($model->load($formData)) {
                $dataProvider = $model->buscarAlinearVoc($formData);
                               
            }else{
                $dataProvider = $model->buscarAlinearVoc($formData);                                               
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
            $arrayPara[] = 0;
            
            return $this->render('reportealinearvoc',[
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

        public function actionParticipantemultiple($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\ControlalinearParticipantelista::find()
                            ->select(['id' => 'tbl_participantes.participan_id', 'text' => 'UPPER(participan_nombre)'])
                            ->where('participan_nombre LIKE "%' . $search . '%"')
                            ->orderBy('participan_nombre')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\ControlalinearParticipantelista::find()
                            ->select(['id' => 'tbl_participantes.participan_id', 'text' => 'UPPER(participan_nombre)'])
                            ->where('tbl_participantes.participan_id IN (' . $id . ')')
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

        public function actionVerlistasalinearvoc($id){
            $txtIdVoc = $id;
            $txtSesion2 = null;
            $txtResponsable = null;
            $txtEstado = null;
            $txtObservacion = null;
            $varArbol = null;
            $txtNombrePartic = null;
            $indicadorPrecic1 = null;
            $indicadorPrecic2 = null;

            $varValorador = Yii::$app->db->createCommand("select valorador_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtNomvalorador = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varValorador")->queryScalar(); 
            $varArbol = Yii::$app->db->createCommand("select arbol_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = $varArbol and activo = 0")->queryScalar(); 
            $varTecnico = Yii::$app->db->createCommand("select tecnico_id from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtNombreTecnico = Yii::$app->db->createCommand("select name from tbl_evaluados where id = $varTecnico")->queryScalar(); 
            $txtDimensiones = Yii::$app->db->createCommand("select dimensions from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtFecha = Yii::$app->db->createCommand("select fechahora from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtAgente = Yii::$app->db->createCommand("select usuagente from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtDureacion = Yii::$app->db->createCommand("select duracion from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtExtension = Yii::$app->db->createCommand("select extencion from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();
            $txtSpeech = Yii::$app->db->createCommand("select numidextsp from tbl_controlvoc_bloque1 where idbloque1 = '$txtIdVoc'")->queryScalar();


            $txtPlanAccion = Yii::$app->db->createCommand("select * from tbl_control_alinear_bloque3 where idbloque1 = '$txtIdVoc'")->queryAll();
 //Incluir en la base de datos el id de la sesion en tbl_control_alinear_bloque3 y pasarlo a verlistaalinearvoc para mostrar los atributs de esa sesion           

            foreach ($txtPlanAccion as $key => $value) {         
                $txtConcepto_mejora = $value['concepto_mejora'];
                $txtAnalisis_causa = $value['analisis_causa'];
                $txtAccion_seguir = $value['accion_seguir'];
                $txtTipo_accion = $value['tipo_accion'];
                $txtResponsable = $value['responsable'];
                $txtFecha_plan = $value['fecha_plan'];
                $txtFecha_implementa = $value['fecha_implementa'];
                $txtEstado = $value['estado'];           
                $txtObservacion = $value['observaciones'];
                $txtSesion2 = $value['sesion_id'];
            }

          $txtCategorias = Yii::$app->db->createCommand("select tbl_arbols.name, tbl_categorias_alinear.categoria_nombre, tbl_atributos_alinear.atributo_nombre, tbl_atributos_alinear.medicion, tbl_atributos_alinear.acuerdo  from tbl_categorias_alinear
                inner join tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                inner join tbl_atributos_alinear ON tbl_atributos_alinear.id_categ_ali = tbl_categorias_alinear.id_categ_ali
                WHERE tbl_categorias_alinear.arbol_id  = '$varArbol'")->queryAll();

            $varcodigos = Yii::$app->db->createCommand(" select tbl_control_alinear_participa.codigo_partcipa
                FROM tbl_control_alinear_participa
                WHERE tbl_control_alinear_participa.idbloque1 = '$txtIdVoc'")->queryScalar();
            $arrayCodigos = explode(",",$varcodigos);
            $txtNombrePartic = "";
            $txtContarSesion1 = "";
            $txtContarSesion2 = "";
            $txtContarNA1 = 0;
            $txtContarNA2 = 0;
            for ($i=0; $i <count($arrayCodigos); $i++) { 
                     $valor = $arrayCodigos[$i];
                 
            $txtNombrePartic2 = Yii::$app->db->createCommand("select tbl_participantes.participan_nombre from tbl_participantes WHERE tbl_participantes.participan_id = '$valor'")->queryScalar();
            $txtNombrePartic = $txtNombrePartic . ', ' . $txtNombrePartic2;
            }
            $txtNombrePartic = substr($txtNombrePartic, 1);
            
            if($txtSesion2 == 3){
                $txtContarSi1 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                    FROM tbl_categorias_alinear
                    INNER JOIN tbl_atributos_alinear ON tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali
                    INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                    INNER JOIN tbl_sesion_alinear on tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id
                    INNER JOIN tbl_medir_alinear ON tbl_medir_alinear.id_atrib_alin = tbl_atributos_alinear.id_atrib_alin
                    where tbl_arbols.id = '$varArbol' and tbl_medir_alinear.id_idvalorado = '$varTecnico' and tbl_categorias_alinear.sesion_id in(1, 2) AND tbl_medir_alinear.medicion = 'Si' AND tbl_medir_alinear.idbloque1 = '$txtIdVoc'")->queryScalar();

		$txtContarNA1 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                    FROM tbl_categorias_alinear
                    INNER JOIN tbl_atributos_alinear ON tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali
                    INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                    INNER JOIN tbl_sesion_alinear on tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id
                    INNER JOIN tbl_medir_alinear ON tbl_medir_alinear.id_atrib_alin = tbl_atributos_alinear.id_atrib_alin
                    where tbl_arbols.id = '$varArbol' and tbl_medir_alinear.id_idvalorado = '$varTecnico' and tbl_categorias_alinear.sesion_id in(1, 2) AND tbl_medir_alinear.medicion = 'NA' AND tbl_medir_alinear.idbloque1 = '$txtIdVoc'")->queryScalar();
    
                $txtContarSesion1 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                   FROM tbl_categorias_alinear 
                   INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                   INNER JOIN tbl_atributos_alinear ON tbl_atributos_alinear.id_categ_ali = tbl_categorias_alinear.id_categ_ali
                   INNER JOIN tbl_sesion_alinear ON tbl_sesion_alinear.sesion_id = tbl_categorias_alinear.sesion_id
                   WHERE tbl_categorias_alinear.arbol_id = '$varArbol' AND tbl_categorias_alinear.sesion_id in(1, 2) ")->queryScalar();
             }else{
                #code
            }
             if($txtSesion2 != 3){
                $txtContarSi2 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                    FROM tbl_categorias_alinear
                    INNER JOIN tbl_atributos_alinear ON tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali
                    INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                    INNER JOIN tbl_sesion_alinear on tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id
                    INNER JOIN tbl_medir_alinear ON tbl_medir_alinear.id_atrib_alin = tbl_atributos_alinear.id_atrib_alin
                    where tbl_arbols.id = '$varArbol' and tbl_medir_alinear.id_idvalorado = '$varTecnico' and tbl_categorias_alinear.sesion_id = '$txtSesion2' AND tbl_medir_alinear.medicion = 'Si' AND tbl_medir_alinear.idbloque1 = '$txtIdVoc'")->queryScalar();

		 $txtContarNA2 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                    FROM tbl_categorias_alinear
                    INNER JOIN tbl_atributos_alinear ON tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali
                    INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                    INNER JOIN tbl_sesion_alinear on tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id
                    INNER JOIN tbl_medir_alinear ON tbl_medir_alinear.id_atrib_alin = tbl_atributos_alinear.id_atrib_alin
                    where tbl_arbols.id = '$varArbol' and tbl_medir_alinear.id_idvalorado = '$varTecnico' and tbl_categorias_alinear.sesion_id = '$txtSesion2' AND tbl_medir_alinear.medicion = 'NA' AND tbl_medir_alinear.idbloque1 = '$txtIdVoc'")->queryScalar();

                 $txtContarSesion2 = Yii::$app->db->createCommand("select COUNT(tbl_atributos_alinear.atributo_nombre) AS cuentasi
                    FROM tbl_categorias_alinear 
                    INNER JOIN tbl_arbols ON tbl_categorias_alinear.arbol_id = tbl_arbols.id
                    INNER JOIN tbl_atributos_alinear ON tbl_atributos_alinear.id_categ_ali = tbl_categorias_alinear.id_categ_ali
                    INNER JOIN tbl_sesion_alinear ON tbl_sesion_alinear.sesion_id = tbl_categorias_alinear.sesion_id
                    WHERE tbl_categorias_alinear.arbol_id = '$varArbol' AND tbl_categorias_alinear.sesion_id =  '$txtSesion2' ")->queryScalar();
              }else{
                            #code
                        }
           

                $indicadorPrecic1 = "";
                $indicadorPrecic2 = "";
               
                if ($txtContarSesion1) {
                    $indicadorPrecic1 = number_format($txtContarSi1 / ($txtContarSesion1 - $txtContarNA1),2) * 100;
                }else{
                    #code
                }

                 if ($txtContarSesion2) {
                     $indicadorPrecic2 = number_format($txtContarSi2 / ($txtContarSesion2 - $txtContarNA2),2) * 100;
                }else{
                    #code
                }
                
               

            return $this->render('verlistasalinearvoc',[
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
                'varTecnico' => $varTecnico,                

                'txtSesion2' => $txtSesion2,  

                'txtConcepto_mejora' => $txtConcepto_mejora,
                'txtAnalisis_causa' => $txtAnalisis_causa,
                'txtAccion_seguir' => $txtAccion_seguir,
                'txtTipo_accion' => $txtTipo_accion,
                'txtResponsable' => $txtResponsable,
                'txtFecha_plan' => $txtFecha_plan,
                'txtFecha_implementa' => $txtFecha_implementa,
                'txtEstado' => $txtEstado,
                'txtObservacion' => $txtObservacion,
                'varArbol' => $varArbol,
                'txtNombrePartic' => $txtNombrePartic,
                'indicadorPrecic1' => $indicadorPrecic1,
                'indicadorPrecic2' => $indicadorPrecic2,

                ]);
        }

	public function actionUpdatevoc($txtPcrc){
           if($txtPcrc){
            $vartxtPcrc = $txtPcrc;
            $varSession = null;
           } else{
            $vartxtPcrc = Yii::$app->request->post("var_Pcrc");
            $varSession = Yii::$app->request->post("var_Sesiones");   
           }
            
            return $this->render('updatevoc',[
                //'model' => $model,
                'vartxtPcrc' => $vartxtPcrc,
                'varSession' => $varSession,
                ]);
        }

	 public function actionActualiza(){
            
             $varSession = Yii::$app->request->post("var_Sesiones");           
             
             die( json_encode($varSession));
         }

         public function actionUpdatevocalinear($txtPcrc,$varSession){
            $varpcrc = $txtPcrc;
            $varsession = $varSession;
                 
            return $this->render('updatevocalinear',[
              'txtPcrc'=>$varpcrc,
              'varSession'=>$varsession,
              ]);
          }

	public function actionEliminaralinearvoccat(){
            $varIdList = Yii::$app->request->post("var_Idlist");
            $valor = 1;
            Yii::$app->db->createCommand()->update('tbl_categorias_alinear',[
                'anulado' => $valor,
             ],"id_categ_ali = '$varIdList'")->execute();

            $rta = 1;
            die(json_encode($rta));
        }
        public function actionEliminaralinearvocatri(){
            $varIdList = Yii::$app->request->post("var_Idlist");
            $valor = 1;
            Yii::$app->db->createCommand()->update('tbl_atributos_alinear',[
                'anulado' => $valor,
             ],"id_atrib_alin = '$varIdList'")->execute();

            $rta = 1;
            die(json_encode($rta));
        }
	public function actionEditaralinearvoccat($var_idcat,$var_nombresesion){
            $txtNombresesion = $var_nombresesion;
            $txtIdcat = $var_idcat;

            $txtNombreList = Yii::$app->db->createCommand("select categoria_nombre from tbl_categorias_alinear where id_categ_ali = '$txtIdcat' and anulado = 0")->queryScalar(); 

            $model = $this->findModel($txtIdcat);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
                return $this->redirect(['index']);
            }

            return $this->render('editaralinearvoccat',[
                'model' => $model,
                'txtNombreList' => $txtNombreList,
                'txtIdCat' => $txtIdcat,
                'txtNomSes' => $txtNombresesion,
                ]);
        }
        protected function findModel($txtIdcat){
            if (($model = Controlalinearcategorialista::findOne($txtIdcat)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        } 

	public function actionEditaralinearvocatri($var_idatri,$var_nombrecateg,$var_idcateg){
            $txtNombrecate = $var_nombrecateg;
            $txtIdatri = $var_idatri;
            $txtIdcate = $var_idcateg;

            $txtNombreList = Yii::$app->db->createCommand("select atributo_nombre from tbl_atributos_alinear where id_atrib_alin = '$txtIdatri' and anulado = 0")->queryScalar(); 
            $txtidsesion = Yii::$app->db->createCommand("select sesion_id from tbl_categorias_alinear where id_categ_ali = '$txtIdcate' and anulado = 0")->queryScalar(); 
            $txtsesionnombre = Yii::$app->db->createCommand("select sesion_nombre from tbl_sesion_alinear where sesion_id = '$txtidsesion' and anulado = 0")->queryScalar(); 
            $txtNombresesion = $txtsesionnombre."-".$txtNombrecate;
     
            $model = $this->findModel1($txtIdatri);
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'Successful update!'));            
                return $this->redirect(['index']);
            }

            return $this->render('editaralinearvocatri',[
                'model' => $model,
                'txtNombreList' => $txtNombreList,
                'txtIdAtri' => $txtIdatri,
                'txtNomSes' => $txtNombresesion,
                ]);
        }
        protected function findModel1($txtIdatri){
            if (($model = Controlalinearatributolista::findOne($txtIdatri)) !== null) {
                return $model;
            } else {
                throw new NotFoundHttpException('The requested page does not exist.');
            }
        }
//Diego ini
         public function actionDatosformulariosalinearvoc(array $idbloque1){

            $txtidbloque = $idbloque1;          
                    
            return $this->renderAjax('datosformulariosalinearvoc',[
                    'txtidbloque'=>$txtidbloque,                       
                        ]);            
        }
//Diego fin
    }
