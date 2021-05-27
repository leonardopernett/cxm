<?php

namespace app\Controllers;

use Yii;
use app\models\Formularios;
use app\models\FormulariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Url;

/**
 * FormulariosController implements the CRUD actions for Formularios model.
 */
class FormulariosController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
                },
                        'rules' => [
                            [
                                'actions' => ['showformulariodiligenciadoamigo',
                                    'showsubtipif', 'borrarformulariodiligenciado'],
                                'allow' => true,
                            ],
                            [
                                'actions' => ['create', 'delete', 'duplicate', 'index',
                                    'update', 'view'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos();
                        },
                            ],
                            [
                                'actions' => ['eliminartmpform', 'evaluadosbyarbol',
                                    'guardarformulario', 'guardarpaso2',
                                    'guardaryenviarformulario', 'interaccionmanual',
                                    'showformulario', 'showsubtipif',
                                    'getarbolesbyroles', 'getarbolesbypermisos', 'getarboles'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo();
                        },
                            ],
                            [
                                'actions' => ['guardarpaso3', 'showformulariobyarbol'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos();
                        },
                            ],
                            [
                                'actions' => ['showformulariodiligenciado',
                                    'showformulario',
                                    'showformulariodiligenciadohistorico',
                                    'editarformulariodiligenciado',
                                    'getarbolesbyroles',
                                    'getarboles',
                                    'getarbolesbypermisos',
                                    'getarbolesbyrolesreportes'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isReportes();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Formularios models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new FormulariosSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single Formularios model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new Formularios model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Formularios();
                $model->nmorden = 1;
                $model->i1_nmfactor = 1;
                $model->i2_nmfactor = 1;
                $model->i3_nmfactor = 1;
                $model->i4_nmfactor = 1;
                $model->i5_nmfactor = 1;
                $model->i6_nmfactor = 1;
                $model->i7_nmfactor = 1;
                $model->i8_nmfactor = 1;
                $model->i9_nmfactor = 1;
                $model->i10_nmfactor = 1;

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing Formularios model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('update', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Duplicate an existing Formularios model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDuplicate($id) {
                $model = $this->findModel($id);
                $model->name = '';

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    if ($model->duplicateForm($model->id, $model->name)) {
                        Yii::$app->getSession()->setFlash('success', Yii::t('app', 'success duplicate form'));
                    } else {
                        Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'error duplicate form'));
                    }
                    return $this->redirect(['index']);
                } else {
                    return $this->render('_duplicate', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Deletes an existing Formularios model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                $model = $this->findModel($id);
                if (!$model->delete()) {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'No puede eliminar el formulario "'
                                    . $model->name
                                    . '" porque corresponde a una o mas personas '
                                    . 'evaluadas.'));
                }

                return $this->redirect(['index']);
            }

            /**
             * Action para iniciar una interacción manual
             * 
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionInteraccionmanual() {

                $modelA = new \app\models\Arboles();
                $modelD = new \app\models\Dimensiones();
                $modelD->scenario = "monitoreo";
                $modelE = new \app\models\Evaluados;
                $modelE->scenario = "monitoreo";

                if (isset($_POST) && !empty($_POST)) {

                    $arbol_id = $_POST["Arboles"]["arbol_id"];
                    $infoArbol = \app\models\Arboles::findOne(["id" => $arbol_id]);
                    $dimension_id = $_POST["Dimensiones"]["dimension_id"];
                    $nmArbol = \app\models\Arboles::findOne($arbol_id);
                    $nmDimension = \app\models\Dimensiones::findOne($dimension_id);
                    $formulario_id = $infoArbol->formulario_id;

                    return $this->render("show-paso2", [
                                "arbol_id" => $arbol_id,
                                "nmArbol" => $nmArbol,
                                "dimension_id" => $dimension_id,
                                "nmDimension" => $nmDimension,
                                "formulario_id" => $formulario_id,
                                "modelE" => $modelE,
                    ]);
                }

                return $this->render('interaccion-manual', [
                            'modelA' => $modelA,
                            'modelD' => $modelD
                ]);
            }

            /**
             * Action para guardar el paso 2 de la creación del formulario
             * 
             * @param int $preview
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGuardarpaso2($preview = 0) {

                $modelE = new \app\models\Evaluados;
                $modelE->scenario = "monitoreo";
                $showInteraccion = 0;
                $showBtnIteraccion = 0;

                if (isset($_POST) && !empty($_POST)) {

                    $arbol_id = $_POST["arbol_id"];
                    $dimension_id = $_POST["dimension_id"];
                    $formulario_id = $_POST["formulario_id"];
                    $evaluado_id = $_POST["Evaluados"]["evaluado_id"];
                    $tipoInteraccion = (isset($_POST["tipo_interaccion"])) ? $_POST["tipo_interaccion"] : 1;
                    $usua_id = ($preview == 1) ? 0 : Yii::$app->user->identity->id;
                    $created = date("Y-m-d H:i:s");
                    $sneditable = 1;

                    //CONSULTO SI YA EXISTE LA EVALUACION
                    $condition = [
                        "usua_id" => $usua_id,
                        "arbol_id" => $arbol_id,
                        "evaluado_id" => $evaluado_id,
                        "dimension_id" => $dimension_id,
                        "basesatisfaccion_id" => null,
                        "sneditable" => $sneditable,
                    ];

                    $idTmpForm = \app\models\Tmpejecucionformularios::findOne($condition);

                    //SI NO EXISTE EL TMP FORMULARIO LO CREO
                    if (empty($idTmpForm)) {
                        $tmpeje = new \app\models\Tmpejecucionformularios();
                        $tmpeje->dimension_id = $dimension_id;
                        $tmpeje->arbol_id = $arbol_id;
                        $tmpeje->usua_id = $usua_id;
                        $tmpeje->evaluado_id = $evaluado_id;
                        $tmpeje->formulario_id = $formulario_id;
                        $tmpeje->created = $created;
                        $tmpeje->sneditable = $sneditable;

                        //EN CASO DE SELECCIONAR ITERACCION AUTOMATICA
                        //CONSULTAMOS LA ITERACCION
                        if ($tipoInteraccion == 0) {
                            //CONSULTA DE LLAMADAS Y PANTALLAS CON WS
                            try {
                                $modelFormularios = new Formularios;
                                $enlaces = $modelFormularios->getEnlaces($evaluado_id);
                                if ($enlaces && count($enlaces) > 0) {
                                    $json = json_encode($enlaces);
                                    $tmpeje->url_llamada = $json;
                                }
                            } catch (\Exception $exc) {
                                \Yii::error('#####' . __FILE__ . ':' . __LINE__
                                        . $exc->getMessage() . '#####', 'redbox');
                                $msg = Yii::t('app', 'Error redbox');
                                Yii::$app->session->setFlash('danger', $msg);
                            }

                            $showInteraccion = 1;
                            $showBtnIteraccion = 1;
                        } else {
                            $showInteraccion = 0;
                            $showBtnIteraccion = 0;
                        }
                        $tmpeje->tipo_interaccion = $tipoInteraccion;
                        $tmpeje->save();
                        $idTmp = $tmpeje->id;
                    } else {
                        $idTmp = $idTmpForm->id;
                        //EN CASO DE SELECCIONAR ITERACCION MANUAL
                        // ELIMINAMOS EL REGSTRO ANTERIOR
                        $showInteraccion = 1;
                        $showBtnIteraccion = 1;
                        //SI ES AUTOMATICA Y ES VACIA
                        if ($tipoInteraccion == 0 && empty($idTmpForm->url_llamada)) {
                            //CONSULTA DE LLAMADAS Y PANTALLAS CON WS 
                            try {
                                $modelFormularios = new Formularios;
                                $enlaces = $modelFormularios->getEnlaces($evaluado_id);
                                if ($enlaces && count($enlaces) > 0) {
                                    $json = json_encode($enlaces);
                                    $idTmpForm->url_llamada = $json;
                                    $idTmpForm->tipo_interaccion = $tipoInteraccion;
                                    $idTmpForm->save();
                                } else {
                                    $idTmpForm->url_llamada = "";
                                    $idTmpForm->tipo_interaccion = $tipoInteraccion;
                                    $idTmpForm->save();
                                    $msg = Yii::t('app', 'Error redbox');
                                    Yii::$app->session->setFlash('danger', $msg);
                                }
                            } catch (\Exception $exc) {
                                \Yii::error('#####' . __FILE__ . ':' . __LINE__
                                        . $exc->getMessage() . '#####', 'redbox');
                                $msg = Yii::t('app', 'Error redbox');
                                Yii::$app->session->setFlash('danger', $msg);
                            }
                            // SI ES MANUAL
                        } elseif ($tipoInteraccion == 1) {
                            $idTmpForm->url_llamada = '';
                            $idTmpForm->tipo_interaccion = $tipoInteraccion;
                            $idTmpForm->save();
                            $showInteraccion = 0;
                            $showBtnIteraccion = 0;
                        }
                    }

                    return $this->redirect([
                                "showformulario",
                                "formulario_id" => $idTmp,
                                "preview" => $preview,
                                "showInteraccion" => base64_encode($showInteraccion),
                                "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                }
            }

            /**
             * Action para mostrar el formulario
             * 
             * @param int $formulario_id
             * @param int $preview
             * @param boolean $fill_values
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowformulario($formulario_id, $preview, $fill_values = false, $view = "interaccionmanual") {



                //DATOS QUE SERAN ENVIADOS AL FORMULARIO
                $data = new \stdClass();

                //OBTENGO EL FORMULARIO
                $TmpForm = \app\models\Tmpejecucionformularios::findOne($formulario_id);

                if (is_null($TmpForm)) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'Formulario no exite'));
                    return $this->redirect(['interaccionmanual']);
                }

                $data->tmp_formulario = $TmpForm;

                //OBTEGO EL ID DEL EQUIPO Y EL ID DEL LIDER
                $datos_eq_li = \app\models\Equipos::getEquipoLider($TmpForm->evaluado_id, $TmpForm->arbol_id);

                if (count($datos_eq_li) > 0) {
                    $data->equipo_id = $datos_eq_li["equipo_id"];
                    $data->usua_id_lider = $datos_eq_li["lider"];
                } else {
                    $data->equipo_id = "";
                    $data->usua_id_lider = "";
                }

                //NOMBRE DEL EVALUADO
                $evaluado = \app\models\Evaluados::findOne($TmpForm->evaluado_id);
                $data->evaluado = $evaluado->name;

                //INFORMACION ADICIONAL
                $arbol = \app\models\Arboles::findOne($TmpForm->arbol_id);
                $data->info_adicional = [
                    'problemas' => $arbol->snactivar_problemas,
                    'tipo_llamada' => $arbol->snactivar_tipo_llamada
                ];
                $data->ruta_arbol = $arbol->dsname_full;
                $data->dimension = \app\models\Dimensiones::findOne($TmpForm->dimension_id);
                $data->detalles = \app\models\Tmpejecucionbloquedetalles::getAllByFormId($formulario_id);

                //CALIFICACIONES
                $tmp_calificaciones_ids = $tmp_tipificaciones_ids = array();
                foreach ($data->detalles as $j => $d) {
                    if (!in_array($d->calificacion_id, $tmp_calificaciones_ids)) {
                        $tmp_calificaciones_ids[] = $d->calificacion_id;
                    }
                    if (!in_array($d->tipificacion_id, $tmp_tipificaciones_ids)) {
                        $tmp_tipificaciones_ids[] = $d->tipificacion_id;
                    }
                    if ($d->tipificacion_id != null) {
                        $data->detalles[$j]->tipif_seleccionados = \app\models\TmpejecucionbloquedetallesTipificaciones::getTipificaciones($d->id);
                    } else {
                        $data->detalles[$j]->tipif_seleccionados = array();
                    }
                }

                //CALIFICACIONES Y TIPIFICACIONES
                $data->calificaciones = \app\models\Calificaciondetalles::getDetallesFromIds($tmp_calificaciones_ids);
                $data->calificacionesArray = \app\models\Calificaciondetalles::getDetallesFromIdsAsArray($tmp_calificaciones_ids);
                $data->tipificaciones = \app\models\Tipificaciondetalles::getDetallesFromIds($tmp_tipificaciones_ids);

                //TRANSACCIONES Y ENFOQUES
                $data->transacciones = \yii\helpers\ArrayHelper::map(\app\models\Transacions::find()->all(), 'id', 'name');
                $data->enfoques = \app\models\Tableroenfoques::find()->asArray()->all();

                //FORMULARIO ID
                $data->formulario_id = $formulario_id;

                /* OBTIENE EL LISTADO DETALLADO DE TABLERO DE EXPERIENCIAS Y LLAMADA
                  EN MODO VISUALIZACIÓN FORMULARIO. */
                $data->tablaproblemas = \app\models\Ejecuciontableroexperiencias::
                                find()
                                ->where(["ejecucionformulario_id" => $TmpForm->ejecucionformulario_id])->all();
                $data->tablallamadas = \app\models\Ejecuciontiposllamada::getTabLlamByIdEjeForm($TmpForm->ejecucionformulario_id);
                $data->list_Add_feedbacks = \app\models\Tmpejecucionfeedbacks::getJoinTipoFeedbacks($formulario_id);

                //PREVIEW
                $data->preview = $preview == 1 ? true : false;
                $data->fill_values = $fill_values;

                return $this->render('show-formulario', [
                            'data' => $data,
                            'view' => $view
                ]);
            }

            /**
             * Obtiene el listado de evaluados
             * @param type $search
             * @param type $arbol_id
             */
            public function actionEvaluadosbyarbol($search = null, $arbol_id = null) {
                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Evaluados::find()
                            ->joinWith('equiposevaluados')
                            ->join('INNER JOIN', 'tbl_arbols_equipos', 'tbl_arbols_equipos.equipo_id = tbl_equipos_evaluados.equipo_id'
                            )
                            ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                            ->where('name LIKE "%' . $search . '%" AND tbl_arbols_equipos.arbol_id = ' . $arbol_id)
                            ->orderBy('name')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Finds the Formularios model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Formularios the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Formularios::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

            /**
             * Action para guardar el formulario
             *      
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGuardarformulario() {

                $arrCalificaciones = !$_POST['calificaciones'] ? array() : $_POST['calificaciones'];
                $arrTipificaciones = !isset($_POST['tipificaciones']) ? array() : $_POST['tipificaciones'];
                $arrSubtipificaciones = !isset($_POST['subtipificaciones']) ? array() : $_POST['subtipificaciones'];
                $arrComentariosSecciones = !$_POST['comentarioSeccion'] ? array() : $_POST['comentarioSeccion'];

                $arrFormulario = [];

                $tmp_id = $_POST['tmp_formulario_id'];
                $arrFormulario["equipo_id"] = $_POST['form_equipo_id'];
                $arrFormulario["usua_id_lider"] = $_POST['form_lider_id'];
                $arrFormulario["dimension_id"] = $_POST['dimension_id'];
                $arrFormulario["dsruta_arbol"] = $_POST['ruta_arbol'];
                $arrFormulario["dscomentario"] = $_POST['comentarios_gral'];
                $arrFormulario["dsfuente_encuesta"] = $_POST['fuente'];
                $arrFormulario["transacion_id"] = $_POST['transacion_id'];
                $view = $_POST['view'];

                /* EDITO EL TMP FORMULARIO */
                \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);

                /* GUARDO LAS CALIFICACIONES */
                foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                    if (empty($calif_detalle_id)) {
                        continue;
                    }
                    $arrDetalleForm = [];
                    $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                    \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["id" => $form_detalle_id]);
                }

                /* GUARDO TIPIFICACIONES */
                foreach ($arrTipificaciones as $form_detalle_id => $tipif_array) {
                    if (empty($tipif_array))
                        continue;

                    \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 0]
                            , ["tmpejecucionbloquedetalle_id" => $form_detalle_id]);

                    \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 1]
                            , "tmpejecucionbloquedetalle_id = '" . $form_detalle_id . "' "
                            . "AND tipificaciondetalle_id IN(" . implode(",", $tipif_array) . ")");
                }

                /* GUARDO SUBTIPIFICACIONES */
                foreach ($arrSubtipificaciones as $form_detalle_id => $subtipif_array) {
                    $sql = "UPDATE `tbl_tmpejecucionbloquedetalles_subtipificaciones` a ";
                    $sql .= "INNER JOIN tbl_tmpejecucionbloquedetalles_tipificaciones b ";
                    $sql .= "ON a.tmpejecucionbloquedetalles_tipificacion_id = b.id ";
                    $sql .= "SET a.sncheck = 1 ";
                    $sql .= "WHERE b.tmpejecucionbloquedetalle_id = " . $form_detalle_id;
                    $sql .= " AND a.tipificaciondetalle_id IN (" . implode(",", $subtipif_array) . ")";
                    $command = \Yii::$app->db->createCommand($sql);
                    $command->execute();
                }
                foreach ($arrComentariosSecciones as $secc_id => $comentario) {

                    \app\models\Tmpejecucionsecciones::updateAll(["dscomentario" => $comentario]
                            , [
                        "seccion_id" => $secc_id
                        , "tmpejecucionformulario_id" => $tmp_id
                    ]);
                }

                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                //VALIDACION TIPO DE INTERACCION
                if ($data->tipo_interaccion == 0) {
                    $showInteraccion = $showBtnIteraccion = 1;
                } else {
                    $showInteraccion = $showBtnIteraccion = 0;
                }

                Yii::$app->session->setFlash('success', Yii::t('app', 'Formulario guardado'));
                return $this->redirect([
                            'showformulario',
                            "formulario_id" => $tmp_id,
                            "preview" => 0,
                            "fill_values"=>false,
                            "view" => $view,
                            "showInteraccion" => base64_encode($showInteraccion),
                            "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
            }

            /**
             * Action para guardar y enviar el formulario
             *      
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGuardaryenviarformulario() {

                $arrCalificaciones = !$_POST['calificaciones'] ? array() : $_POST['calificaciones'];
                $arrTipificaciones = !isset($_POST['tipificaciones']) ? array() : $_POST['tipificaciones'];
                $arrSubtipificaciones = !isset($_POST['subtipificaciones']) ? array() : $_POST['subtipificaciones'];
                $arrComentariosSecciones = !$_POST['comentarioSeccion'] ? array() : $_POST['comentarioSeccion'];

                $arrFormulario = [];
                $arrayCountBloques = [];
                $arrayBloques = [];
                $count = 0;
                $tmp_id = $_POST['tmp_formulario_id'];
                $arrFormulario["equipo_id"] = $_POST['form_equipo_id'];
                $arrFormulario["usua_id_lider"] = $_POST['form_lider_id'];
                $arrFormulario["dimension_id"] = $_POST['dimension_id'];
                $arrFormulario["dsruta_arbol"] = $_POST['ruta_arbol'];
                $arrFormulario["dscomentario"] = $_POST['comentarios_gral'];
                $arrFormulario["dsfuente_encuesta"] = $_POST['fuente'];
                $arrFormulario["transacion_id"] = $_POST['transacion_id'];
                $view = $_POST['view'];

                /* EDITO EL TMP FORMULARIO */
                $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
                $model->usua_id_actual = Yii::$app->user->identity->id;
                $model->save();
                \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                /* GUARDO LAS CALIFICACIONES */
                foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                    if (empty($calif_detalle_id)) {
                        continue;
                    }
                    $arrDetalleForm = [];
                    $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                    \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["id" => $form_detalle_id]);
                    $calificacion = \app\models\Tmpejecucionbloquedetalles::findOne(["id" => $form_detalle_id]);
                    $calificacionDetalle = \app\models\Calificaciondetalles::findOne(['id' => $calificacion->calificaciondetalle_id]);
                    //Cuento las preguntas en las cuales esta seleccionado el NA
                    //lleno $arrayBloques para tener marcados en que bloques no se selecciono el check
                    if (!in_array($calificacion->bloque_id, $arrayBloques) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                        $arrayBloques[] = $calificacion->bloque_id;
                        //inicio $arrayCountBloques
                        $arrayCountBloques[$count] = [($calificacion->bloque_id) => 1];
                        $count++;
                    } else {
                        //actualizo $arrayCountBloques sumandole 1 cada q encuentra un NA de ese bloque
                        if (count($arrayCountBloques) != 0) {
                            if ((array_key_exists($calificacion->bloque_id, $arrayCountBloques[count($arrayCountBloques) - 1])) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                                $arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] = ($arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] + 1);
                            }
                        }
                    }
                }
                //$arrayCountBloques = call_user_func_array('array_merge', $arrayCountBloques);
                //Actualizo los bloques en los cuales el total de sus preguntas esten seleccionadas en NA
                foreach ($arrayCountBloques as $dato) {
                    $totalPreguntasBloque = \app\models\Tmpejecucionbloquedetalles::find()->select("COUNT(id) as preguntas")
                                    ->from("tbl_tmpejecucionbloquedetalles")
                                    ->where(['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)])->asArray()->all();
                    if ($dato[key($dato)] == $totalPreguntasBloque["0"]["preguntas"]) {
                        \app\models\Tmpejecucionbloques::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)]);
                    }
                }
                //actualizo las secciones, la cuales tienen todos sus bloques con la opcion snna en 1
                $secciones = \app\models\Tmpejecucionsecciones::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                foreach ($secciones as $seccion) {
                    $bloquessnna = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                    ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                    ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                    ->where(['b.snna' => 1, 's.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                    ->groupBy("s.id")->asArray()->all();
                    $totalBloques = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                    ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                    ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                    ->where(['s.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                    ->groupBy("s.id")->asArray()->all();
                    if (count($bloquessnna) > 0) {
                        if ($bloquessnna[0]['conteo'] == $totalBloques[0]['conteo']) {
                            \app\models\Tmpejecucionsecciones::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'seccion_id' => ($seccion->seccion_id)]);
                        }
                    }
                }
                /* GUARDO TIPIFICACIONES */
                foreach ($arrTipificaciones as $form_detalle_id => $tipif_array) {
                    if (empty($tipif_array))
                        continue;

                    \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 0]
                            , ["tmpejecucionbloquedetalle_id" => $form_detalle_id]);

                    \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 1]
                            , "tmpejecucionbloquedetalle_id = '" . $form_detalle_id . "' "
                            . "AND tipificaciondetalle_id IN(" . implode(",", $tipif_array) . ")");
                }

                /* GUARDO SUBTIPIFICACIONES */
                foreach ($arrSubtipificaciones as $form_detalle_id => $subtipif_array) {
                    $sql = "UPDATE `tbl_tmpejecucionbloquedetalles_subtipificaciones` a ";
                    $sql .= "INNER JOIN tbl_tmpejecucionbloquedetalles_tipificaciones b ";
                    $sql .= "ON a.tmpejecucionbloquedetalles_tipificacion_id = b.id ";
                    $sql .= "SET a.sncheck = 1 ";
                    $sql .= "WHERE b.tmpejecucionbloquedetalle_id = " . $form_detalle_id;
                    $sql .= " AND a.tipificaciondetalle_id IN (" . implode(",", $subtipif_array) . ")";
                    $command = \Yii::$app->db->createCommand($sql);
                    $command->execute();
                }
                foreach ($arrComentariosSecciones as $secc_id => $comentario) {

                    \app\models\Tmpejecucionsecciones::updateAll(["dscomentario" => $comentario]
                            , [
                        "seccion_id" => $secc_id
                        , "tmpejecucionformulario_id" => $tmp_id
                    ]);
                }
                //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);

                /* GUARDAR EL TMP FOMULARIO A LAS EJECUCIONES */
                \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);
                /**
                 * Se envia datos a la aplicacion amigo, indicando que se realizo una valoracion
                 */
                //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                /* */
                $modelEvaluado = \app\models\Evaluados::findOne(["id" => $tmp_ejecucion->evaluado_id]);
                $ejecucion = \app\models\Ejecucionformularios::find()->where(['evaluado_id' => $tmp_ejecucion->evaluado_id, 'usua_id' => $tmp_ejecucion->usua_id])->orderBy('id DESC')->all();
                $params = [];
                $params['titulo'] = 'Te han realizado una valoración';
                $params['pcrc'] = '';
                $params['descripcion'] = '';
                $params['notificacion'] = 'SI';
                $params['muro'] = 'NO';
                $params['usuariored'] = $modelEvaluado->dsusuario_red;
                $params['cedula'] = '';
                $params['plataforma'] = 'QA';
                $params['url'] = '' . Url::to(['formularios/showformulariodiligenciadoamigo'], true) . '?form_id=' . base64_encode($ejecucion[0]->id);
                $webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
                $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                if (!$webservicesresponse && $tmp_ejecucion == '') {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));
                }
                Yii::$app->session->setFlash('success', Yii::t('app', 'Formulario guardado'));

                return $this->redirect([$view]);
            }

            /**
             * Metodo el html con las subtipificaciones
             * 
             * @param int $id_detalle
             * @param int $id_tipificacion
             * @param int $preview
             * @return html
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowsubtipif($id_detalle, $id_tipificacion, $preview = 0) {
                $subtipificaciones = \app\models\TmpejecucionbloquedetallesSubtipificaciones::getSubTipificaciones($id_detalle, $id_tipificacion);
                $html = '';
                foreach ($subtipificaciones as $objTipif) {
                    $checked = '';
                    if ($objTipif["sncheck"] == 1) {
                        $checked = ' checked="checked" ';
                    }
                    if ($preview == 1) {
                        if ($objTipif["sncheck"] == 1) {
                            $html.= '&nbsp;&nbsp;&nbsp;<input ' . $checked . ' '
                                    . 'disabled="disabled" '
                                    . 'name="subtipificaciones[' . $id_detalle . '][]" '
                                    . 'type="checkbox" value="' . $objTipif["id"] . '">'
                                    . '&nbsp;' . $objTipif["name"] . '<br/>';
                        }
                    } else {
                        $html.= '&nbsp;&nbsp;&nbsp;<input ' . $checked . ' '
                                . 'name="subtipificaciones[' . $id_detalle . '][]" '
                                . 'type="checkbox" '
                                . 'value="' . $objTipif["id"] . '">'
                                . '&nbsp;' . $objTipif["name"] . '<br/>';
                    }
                }
                echo $html;
            }

            /**
             * Metodo para borrar el formulario temporal
             * 
             * @param int $tmp_form
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionEliminartmpform($tmp_form) {

                \app\models\Tmpejecucionformularios::deleteAll(["id" => $tmp_form]);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Formulario borrado'));
                return $this->redirect(['interaccionmanual']);
            }

            /**
             * Metodo para mostrar la vista previa del formulario
             * 
             * @param int $preview
             * @param int $arbol_id
             * @param int $formulario_id
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGuardarpaso3($arbol_id, $formulario_id, $preview = 1) {

                $usua_id = $evaluado_id = 0;
                $created = date("Y-m-d H:i:s");
                $dimension_id = 1;
                $sneditable = 1;

                $tmpeje = new \app\models\Tmpejecucionformularios();
                $tmpeje->dimension_id = $dimension_id;
                $tmpeje->arbol_id = $arbol_id;
                $tmpeje->usua_id = $usua_id;
                $tmpeje->evaluado_id = $evaluado_id;
                $tmpeje->formulario_id = $formulario_id;
                $tmpeje->created = $created;
                $tmpeje->sneditable = $sneditable;
                $tmpeje->save();
                $idTmp = $tmpeje->id;

                return $this->redirect(['showformulariobyarbol', "tmp_id" => $idTmp,
                            "preview" => $preview]);
            }

            /**
             * Metodo para mostrar la vista previa del formulario
             * 
             * @param int $tmp_id
             * @param int $preview
             * @param boolean $fill_values
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowformulariobyarbol($tmp_id, $preview = 0, $fill_values = FALSE, $view = "interaccionmanual") {

                //DATOS QUE SERAN ENVIADOS AL FORMULARIO
                $data = new \stdClass();

                //OBTENGO EL FORMULARIO
                $TmpForm = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                $data->tmp_formulario = $TmpForm;

                $data->evaluado = $data->equipo_id = $data->usua_id_lider = "";

                //INFORMACION ADICIONAL
                $arbol = \app\models\Arboles::findOne($TmpForm->arbol_id);
                $data->info_adicional = [
                    'problemas' => $arbol->snactivar_problemas,
                    'tipo_llamada' => $arbol->snactivar_tipo_llamada
                ];
                $data->ruta_arbol = $arbol->dsname_full;
                $data->dimension = \app\models\Dimensiones::findOne($TmpForm->dimension_id);
                $data->detalles = \app\models\Tmpejecucionbloquedetalles::getAllByFormId($tmp_id);

                //CALIFICACIONES
                $tmp_calificaciones_ids = $tmp_tipificaciones_ids = array();
                foreach ($data->detalles as $j => $d) {
                    if (!in_array($d->calificacion_id, $tmp_calificaciones_ids)) {
                        $tmp_calificaciones_ids[] = $d->calificacion_id;
                    }
                    if (!in_array($d->tipificacion_id, $tmp_tipificaciones_ids)) {
                        $tmp_tipificaciones_ids[] = $d->tipificacion_id;
                    }
                    if ($d->tipificacion_id != null) {
                        $data->detalles[$j]->tipif_seleccionados = \app\models\TmpejecucionbloquedetallesTipificaciones::getTipificaciones($d->id);
                    } else {
                        $data->detalles[$j]->tipif_seleccionados = array();
                    }
                }

                //CALIFICACIONES Y TIPIFICACIONES
                $data->calificaciones = \app\models\Calificaciondetalles::getDetallesFromIds($tmp_calificaciones_ids);
                $data->calificacionesArray = \app\models\Calificaciondetalles::getDetallesFromIdsAsArray($tmp_calificaciones_ids);
                $data->tipificaciones = \app\models\Tipificaciondetalles::getDetallesFromIds($tmp_tipificaciones_ids);

                //TRANSACCIONES Y ENFOQUES
                $data->transacciones = \yii\helpers\ArrayHelper::map(\app\models\Transacions::find()->all(), 'id', 'name');
                $data->enfoques = \app\models\Tableroenfoques::find()->asArray()->all();

                //FORMULARIO ID
                $data->formulario_id = $tmp_id;

                /* OBTIENE EL LISTADO DETALLADO DE TABLERO DE EXPERIENCIAS Y LLAMADA
                  EN MODO VISUALIZACIÓN FORMULARIO. */
                $data->tablaproblemas = \app\models\Ejecuciontableroexperiencias::
                                find()
                                ->where(["ejecucionformulario_id" => $TmpForm->ejecucionformulario_id])->all();
                $data->tablallamadas = \app\models\Ejecuciontiposllamada::getTabLlamByIdEjeForm($TmpForm->ejecucionformulario_id);
                $data->list_Add_feedbacks = \app\models\Tmpejecucionfeedbacks::getJoinTipoFeedbacks($tmp_id);

                //PREVIEW
                $data->preview = $preview == 1 ? true : false;
                $data->fill_values = $fill_values;

                return $this->render('show-formulario', [
                            'data' => $data,
                            'view' => $view
                ]);
            }

            /**
             * Metodo para mostrar el formulario diligenciado
             * 
             * @param int $feedback_id
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowformulariodiligenciado($feedback_id,$view = "interaccionmanual") {

                $ejefeedback = \app\models\Ejecucionfeedbacks::findOne($feedback_id);
                $usua_id = Yii::$app->user->identity->id;
                $formId = \app\models\Ejecucionformularios::llevarATmp($ejefeedback->ejecucionformulario_id, $usua_id);

                //CONSULTA DEL FORMULARIO
                $data = \app\models\Ejecucionformularios::findOne($ejefeedback->ejecucionformulario_id);
                //VALIDACION TIPO DE INTERACCION
                if ($data->tipo_interaccion == 0) {
                    $showInteraccion = 1;
                } else {
                    $showInteraccion = 0;
                }
                $showBtnIteraccion = 0;

                return $this->redirect(['showformulario'
                            , "formulario_id" => $formId[0]["tmp_id"]
                            , "preview" => 1
                            , "fill_values" => true
                            , "view" =>$view
                            , "showInteraccion" => base64_encode($showInteraccion)
                            , "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
            }

            /**
             * Metodo para mostrar el formulario diligenciado
             * 
             * @param int $tmp_id
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowformulariodiligenciadohistorico($tmp_id,$view = "interaccionmanual") {
                $usua_id = Yii::$app->user->identity->id;
                $form_id = \app\models\Ejecucionformularios::llevarATmp($tmp_id, $usua_id);

                //CONSULTA DEL FORMULARIO
                $data = \app\models\Ejecucionformularios::findOne($tmp_id);
                //VALIDACION TIPO DE INTERACCION
                if ($data->tipo_interaccion == 0) {
                    $showInteraccion = 1;
                } else {
                    $showInteraccion = 0;
                }
                $showBtnIteraccion = 0;

                return $this->redirect(['showformulario'
                            , "formulario_id" => $form_id[0]["tmp_id"]
                            , "preview" => 1
                            , "fill_values" => true
                            , "view"=>$view
                            , "showInteraccion" => base64_encode($showInteraccion)
                            , "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
            }

            /**
             * Metodo para mostrar el formulario diligenciado
             * 
             * @param int $tmp_id
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionEditarformulariodiligenciado($tmp_id, $view = "interaccionmanual") {
                $usua_id = Yii::$app->user->identity->id;
                $formId = \app\models\Ejecucionformularios::llevarATmp($tmp_id, $usua_id);

                //CONSULTA DEL FORMULARIO
                $data = \app\models\Ejecucionformularios::findOne($tmp_id);
                //VALIDACION TIPO DE INTERACCION
                if ($data->tipo_interaccion == 0) {
                    $showInteraccion = 1;
                    $showBtnIteraccion = 1;
                } else {
                    $showInteraccion = 0;
                    $showBtnIteraccion = 0;
                }

                return $this->redirect(['showformulario'
                            , "formulario_id" => $formId[0]["tmp_id"]
                            , "preview" => 0
                            , "view" => $view
                            , "showInteraccion" => base64_encode($showInteraccion)
                            , "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
            }

            /**
             * Metodo para borrar el formulario diligenciado
             * 
             * @param int $tmp_id
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionBorrarformulariodiligenciado($tmp_id) {
                $model = new \app\models\Ejecucionformularios();
                //BORRAR EL FORMULARIO
                if ($model->borrarForm($tmp_id)) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Formulario borrado'));
                } else {
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'error duplicate form'));
                }
                return $this->redirect(['reportes/historicoformularios']);
            }

            /**
             * 
             * @return array
             * @author Felipe echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGetarbolesbypermisos($search = null, $id = null) {
                $out = ['more' => false];
                $grupo = Yii::$app->user->identity->grupousuarioid;
                if (!is_null($search)) {
                    $data = \app\models\Arboles::find()
                            ->joinWith('permisosGruposArbols')
                            ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                            ->where([
                                "sncrear_formulario" => 1,
                                "snhoja" => 1,
                                "grupousuario_id" => $grupo,
                                "snver_grafica" => 1])
                            ->andWhere(['not', ['formulario_id' => null]])
                            ->andWhere('name LIKE "%' . $search . '%" ')
                            ->orderBy("dsorden ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Arboles::find()
                            ->joinWith('permisosGruposArbols')
                            ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                            ->where([
                                "sncrear_formulario" => 1,
                                "snhoja" => 1,
                                "grupousuario_id" => $grupo,
                                "snver_grafica" => 1])
                            ->andWhere(['not', ['formulario_id' => null]])
                            ->andWhere('tbl_arbols.id = ' . $id)
                            ->orderBy("dsorden ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de arboles dependiente del rol
             * @param type $search
             * @param type $arbol_id
             */
            public function actionGetarbolesbyroles($search = null, $id = null) {
                $out = ['more' => false];
                $grupo = Yii::$app->user->identity->grupousuarioid;
                if (!is_null($search)) {
                    $data = \app\models\Arboles::find()
                            ->joinWith('permisosGruposArbols')
                            ->join('INNER JOIN', 'tbl_grupos_usuarios', 'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                            ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                            ->where([
                                "sncrear_formulario" => 1,
                                "snhoja" => 1,
                                "grupousuario_id" => $grupo])
                            ->andWhere(['not', ['formulario_id' => null]])
                            ->andWhere('name LIKE "%' . $search . '%" ')
                            ->andWhere('tbl_grupos_usuarios.per_realizar_valoracion = 1')
                            ->orderBy("dsorden ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Arboles::find()
                            ->joinWith('permisosGruposArbols')
                            ->join('INNER JOIN', 'tbl_grupos_usuarios', 'tbl_permisos_grupos_arbols.grupousuario_id = tbl_grupos_usuarios.grupos_id')
                            ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                            ->where([
                                "sncrear_formulario" => 1,
                                "snhoja" => 1,
                                "grupousuario_id" => $grupo])
                            ->andWhere(['not', ['formulario_id' => null]])
                            ->andWhere('tbl_arbols.id = ' . $id)
                            ->andWhere('tbl_grupos_usuarios.per_realizar_valoracion = 1')
                            ->orderBy("dsorden ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de arboles dependiente del rol
             * @param type $search
             * @param type $arbol_id
             */
            public function actionGetarbolesbyrolesreportes($search = null, $id = null) {
                $out = ['more' => false];
                $grupo = Yii::$app->user->identity->grupousuarioid;
                if (!is_null($search)) {
                    $data = \app\models\Arboles::find()
                            ->joinWith('permisosGruposArbols')
                            ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                            ->where([
                                "snver_grafica" => 1,
                                "snhoja" => 1,
                                "grupousuario_id" => $grupo])
                            ->andWhere(['not', ['formulario_id' => null]])
                            ->andWhere('name LIKE "%' . $search . '%" ')
                            ->orderBy("dsorden ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Arboles::find()
                            ->joinWith('permisosGruposArbols')
                            ->select(['id' => 'tbl_arbols.id', 'text' => 'UPPER(tbl_arbols.dsname_full)'])
                            ->where([
                                "snver_grafica" => 1,
                                "snhoja" => 1,
                                "grupousuario_id" => $grupo])
                            ->andWhere(['not', ['formulario_id' => null]])
                            ->andWhere('tbl_arbols.id = ' . $id)
                            ->orderBy("dsorden ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de arboles de la tabla tbl_tmpreportes_arbol
             * @param type $search
             * @param type $id
             */
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
                            ->andWhere('tbl_tmpreportes_arbol.seleccion_arbol_id = ' . $id)
                            ->orderBy("tbl_tmpreportes_arbol.dsruta_arbol ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Metodo para mostrar el formulario diligenciado desde amigo
             * 
             * @param int $tmp_id
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowformulariodiligenciadoamigo($form_id,$view = "interaccionmanual") {

                //NUEVO LAYOUT
                $this->layout = "formulario";

                //USUARIO GENERICO
                $usua_id = 0;
                //DECODIFICTO EL FORMULARIO ID
                $tmp_id_decode = base64_decode($form_id);

                if (!empty($tmp_id_decode) && is_numeric($tmp_id_decode)) {
                    $tmp_id = \app\models\Ejecucionformularios::llevarATmp($tmp_id_decode, $usua_id);
                    $_GET["showInteraccion"] = base64_encode(1);
                    $_GET["showBtnIteraccion"] = base64_encode(0);
                    $preview = 1;
                    $fill_values = true;

                    //DATOS QUE SERAN ENVIADOS AL FORMULARIO
                    $data = new \stdClass();

                    //OBTENGO EL FORMULARIO
                    $TmpForm = \app\models\Tmpejecucionformularios::findOne($tmp_id[0]["tmp_id"]);
                    $data->tmp_formulario = $TmpForm;

                    //OBTEGO EL ID DEL EQUIPO Y EL ID DEL LIDER
                    $datos_eq_li = \app\models\Equipos::getEquipoLider($TmpForm->evaluado_id, $TmpForm->arbol_id);

                    if (count($datos_eq_li) > 0) {
                        $data->equipo_id = $datos_eq_li["equipo_id"];
                        $data->usua_id_lider = $datos_eq_li["lider"];
                    } else {
                        $data->equipo_id = "";
                        $data->usua_id_lider = "";
                    }

                    //NOMBRE DEL EVALUADO
                    $evaluado = \app\models\Evaluados::findOne($TmpForm->evaluado_id);
                    $data->evaluado = $evaluado->name;

                    //INFORMACION ADICIONAL
                    $arbol = \app\models\Arboles::findOne($TmpForm->arbol_id);
                    $data->info_adicional = [
                        'problemas' => $arbol->snactivar_problemas,
                        'tipo_llamada' => $arbol->snactivar_tipo_llamada
                    ];
                    $data->ruta_arbol = $arbol->dsname_full;
                    $data->dimension = \app\models\Dimensiones::findOne($TmpForm->dimension_id);
                    $data->detalles = \app\models\Tmpejecucionbloquedetalles::getAllByFormId($tmp_id[0]["tmp_id"]);

                    //CALIFICACIONES
                    $tmp_calificaciones_ids = $tmp_tipificaciones_ids = array();
                    foreach ($data->detalles as $j => $d) {
                        if (!in_array($d->calificacion_id, $tmp_calificaciones_ids)) {
                            $tmp_calificaciones_ids[] = $d->calificacion_id;
                        }
                        if (!in_array($d->tipificacion_id, $tmp_tipificaciones_ids)) {
                            $tmp_tipificaciones_ids[] = $d->tipificacion_id;
                        }
                        if ($d->tipificacion_id != null) {
                            $data->detalles[$j]->tipif_seleccionados = \app\models\TmpejecucionbloquedetallesTipificaciones::getTipificaciones($d->id);
                        } else {
                            $data->detalles[$j]->tipif_seleccionados = array();
                        }
                    }

                    //CALIFICACIONES Y TIPIFICACIONES
                    $data->calificaciones = \app\models\Calificaciondetalles::getDetallesFromIds($tmp_calificaciones_ids);
                    $data->calificacionesArray = \app\models\Calificaciondetalles::getDetallesFromIdsAsArray($tmp_calificaciones_ids);
                    $data->tipificaciones = \app\models\Tipificaciondetalles::getDetallesFromIds($tmp_tipificaciones_ids);

                    //TRANSACCIONES Y ENFOQUES
                    $data->transacciones = \yii\helpers\ArrayHelper::map(\app\models\Transacions::find()->all(), 'id', 'name');
                    $data->enfoques = \app\models\Tableroenfoques::find()->asArray()->all();

                    //FORMULARIO ID
                    $data->formulario_id = $tmp_id[0]["tmp_id"];

                    /* OBTIENE EL LISTADO DETALLADO DE TABLERO DE EXPERIENCIAS Y LLAMADA
                      EN MODO VISUALIZACIÃ“N FORMULARIO. */
                    $data->tablaproblemas = \app\models\Ejecuciontableroexperiencias::
                                    find()
                                    ->where(["ejecucionformulario_id" => $TmpForm->ejecucionformulario_id])->all();
                    $data->tablallamadas = \app\models\Ejecuciontiposllamada::getTabLlamByIdEjeForm($TmpForm->ejecucionformulario_id);
                    $data->list_Add_feedbacks = \app\models\Tmpejecucionfeedbacks::getJoinTipoFeedbacks($tmp_id[0]["tmp_id"]);

                    //PREVIEW
                    $data->preview = $preview == 1 ? true : false;
                    $data->fill_values = $fill_values;

                    return $this->render('show-formulario', [
                                'data' => $data,
                                'view' =>$view
                    ]);
                }
            }

        }
        