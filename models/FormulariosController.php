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
                                    'update', 'view', 'metricalistmultiple', 'usuariolist', 'borrarformulariodiligenciadoescalado'],
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
                                    'getarbolesbyroles', 'getarbolesbypermisos', 'getarboles', 'indexescalados', 'indexescaladosenviados', 'consultarcalificacionsubi', 'metricalistmultipleform',
                                    'adicionarform', 'escalarform', 'evaluadosbyform', 'getarbolesbyform', 'borrarformulariodiligenciadoescalado', 'evaluadoresbyarbolseleccescalado'],
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
                                    'editarformulariodiligenciadoescalado',
                                    'verformulariodiligenciadoescalado',
                                    'getarbolesbyroles',
                                    'getarboles',
                                    'getarbolesbypermisos',
                                    'getarbolesbyrolesreportes', 'metricalistmultipleform'],
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
                                "escalado" => 0,
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
            public function actionShowformulario($formulario_id, $preview, $fill_values = false) {



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
                //$data->dimension = \app\models\Dimensiones::findOne($TmpForm->dimension_id);
                $data->dimension = \yii\helpers\ArrayHelper::map(\app\models\Dimensiones::find()->all(), 'id', 'name');
                $data->detalles = \app\models\Tmpejecucionbloquedetalles::getAllByFormId($formulario_id);
                $data->totalBloques = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $TmpForm->id]);

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
                //busco el formulario al cual esta atado la valoracion a cargar
                //y valido de q si tenga un formulario, de lo contrario se fija 
                //en 1 por defecto
                $data->formulario = Formularios::find()->where(['id' => $data->tmp_formulario->formulario_id])->one();
                if (!isset($TmpForm->subi_calculo)) {
                    //$TmpForm->subi_calculo = $data->formulario->subi_calculo;
                    if (isset($data->formulario->subi_calculo)) {
                        $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                        $TmpForm->save();
                        $array_indices_TmpForm = \app\models\Textos::find()
                                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                ->asArray()
                                ->all();
                        foreach ($array_indices_TmpForm as $value) {
                            $data->indices_calcular[$value['id']] = $value['text'];
                        }
                    }
                } else {
                    if (isset($data->formulario->subi_calculo)) {
                        $array_indices_TmpForm = \app\models\Textos::find()
                                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                ->asArray()
                                ->all();
                        foreach ($array_indices_TmpForm as $value) {
                            $data->indices_calcular[$value['id']] = $value['text'];
                        }
                    }
                }

                //$data->indices_calcular = call_user_func_array('array_merge', $data->indices_calcular);
                /* if (!isset($data->formulario)) {
                  $data->formulario = new Formularios();
                  $data->formulario->id_plantilla_form = 1;
                  } */

                // echo "<pre>";
                // print_r($data); die;
                return $this->render('show-formulario', [
                                                        'data' => $data,
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
                } elseif (!empty($id)) {
                    
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
                $arrCheckPits = !isset($_POST['checkPits']) ? array() : $_POST['checkPits'];

                $arrFormulario = [];

                $tmp_id = $_POST['tmp_formulario_id'];
                $arrFormulario["equipo_id"] = $_POST['form_equipo_id'];
                $arrFormulario["usua_id_lider"] = $_POST['form_lider_id'];
                $arrFormulario["dimension_id"] = $_POST['dimension_id'];
                $arrFormulario["dsruta_arbol"] = $_POST['ruta_arbol'];
                $arrFormulario["dscomentario"] = $_POST['comentarios_gral'];
                $arrFormulario["dsfuente_encuesta"] = $_POST['fuente'];
                $arrFormulario["transacion_id"] = $_POST['transacion_id'];
                 $view = (isset($_POST['view']))?$_POST['view']:null;
                //$arrFormulario["subi_calculo"] = !isset($_POST['subi_calculo']) ? '' : $_POST['subi_calculo'];
                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                if ($_POST['subi_calculo'] != '') {
                    $data->subi_calculo .=',' . $_POST['subi_calculo'];
                    $data->save();
                }
                //TO-DO  : COMENTAR LINEA EN CASO DE NO NECESITAR LO DE ADICIONAR Y ESCALAR
                $modelRegistro = \app\models\RegistroEjec::findOne(['ejec_form_id' => $tmp_id, 'valorador_id' => $data->usua_id]);
                if (!isset($modelRegistro)) {
                    $modelRegistro = new \app\models\RegistroEjec();
                    $modelRegistro->ejec_form_id = $tmp_id;
                    $modelRegistro->descripcion = 'Primera valoración';
                }
                //$modelRegistro = new \app\models\RegistroEjec();
                $modelRegistro->dimension_id = $_POST['dimension_id'];
                $modelRegistro->valorado_id = $data->evaluado_id;
                $modelRegistro->valorador_id = $data->usua_id;
                $modelRegistro->pcrc_id = $data->arbol_id;
                $modelRegistro->tipo_interaccion = $data->tipo_interaccion;
                $modelRegistro->fecha_modificacion = date("Y-m-d H:i:s");
                $modelRegistro->save();
                //FIN
                //$formulario = Formularios::find()->where(['id' => $data->tmp_formulario->formulario_id])->one();
                /* if (isset($formulario->subi_calculo)) {
                  $arrFormulario["subi_calculo"] .= ','.$formulario->subi_calculo;
                  } */

                /* EDITO EL TMP FORMULARIO */
                \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);

                /* GUARDO LAS CALIFICACIONES */
                foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                    $arrDetalleForm = [];
                    if (count($arrCheckPits) > 0) {
                        if (isset($arrCheckPits[$form_detalle_id])) {
                            $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                        }
                    }
                    if (empty($calif_detalle_id)) {
                        $arrDetalleForm["calificaciondetalle_id"] = -1;
                    } else {
                        $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                    }
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
							"fill_values" => false,
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
                $arrCheckPits = !isset($_POST['checkPits']) ? array() : $_POST['checkPits'];
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
                $arrFormulario["sn_mostrarcalculo"] = 1;
                $view = (isset($_POST['view']))?$_POST['view']:null;
                //$arrFormulario["subi_calculo"] = !isset($_POST['subi_calculo']) ? '' : $_POST['subi_calculo'];
                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                if (isset($_POST['subi_calculo']) AND $_POST['subi_calculo'] != '') {
                    $data->subi_calculo .=',' . $_POST['subi_calculo'];
                }
                $data->usua_id_actual = Yii::$app->user->identity->id;
                $data->save();
                //$formulario = Formularios::find()->where(['id' => $data->tmp_formulario->formulario_id])->one();
                /* if (isset($formulario->subi_calculo)) {
                  $arrFormulario["subi_calculo"] .= ','.$formulario->subi_calculo;
                  } */
                /* EDITO EL TMP FORMULARIO */
                /*$model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
                $model->usua_id_actual = Yii::$app->user->identity->id;
                $model->save();*/
                //TO-DO  : COMENTAR LINEA EN CASO DE NO NECESITAR LO DE ADICIONAR Y ESCALAR
                /* Guardo en la tabla tbl_registro_ejec para tener un seguimiento 
                 * de los diversos involucrados en la valoracion en el tiempo */
                $modelRegistro = \app\models\RegistroEjec::findOne(['ejec_form_id' => $model->ejecucionformulario_id, 'valorador_id' => $model->usua_id]);
                if (!isset($modelRegistro)) {
                    $modelRegistro = new \app\models\RegistroEjec();
                    $modelRegistro->ejec_form_id = $tmp_id;
                    $modelRegistro->descripcion = 'Primera valoración';
                }
                //$modelRegistro = new \app\models\RegistroEjec();
                $modelRegistro->dimension_id = $_POST['dimension_id'];
                $modelRegistro->valorado_id = $data->evaluado_id;
                $modelRegistro->valorador_id = $data->usua_id;
                $modelRegistro->pcrc_id = $data->arbol_id;
                $modelRegistro->tipo_interaccion = $data->tipo_interaccion;
                $modelRegistro->fecha_modificacion = date("Y-m-d H:i:s");
                $modelRegistro->save();
                //FIN
                \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                /* GUARDO LAS CALIFICACIONES */
                foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                    $arrDetalleForm = [];
                    //se valida que existan check de pits seleccionaddos y se valida
                    //que exista el del bloquedetalle actual para actualizarlo
                    if (count($arrCheckPits) > 0) {
                        if (isset($arrCheckPits[$form_detalle_id])) {
                            $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                        }
                    }
                    if (empty($calif_detalle_id)) {
                        $arrDetalleForm["calificaciondetalle_id"] = -1;
                    } else {
                        $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                    }

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
                $validarPasoejecucionform = \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);
                /* validacion de guardado exitoso del tmp y paso a las tablas de ejecucion
                en caso de no cumplirla, se redirige nuevamente al formulario */
                if (!$validarPasoejecucionform) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception tmpejecucion to ejecucion'));
                    if ($model->tipo_interaccion == 0) {
                        $showInteraccion = 1;
                        $showBtnIteraccion = 1;
                    } else {
                        $showInteraccion = 0;
                        $showBtnIteraccion = 0;
                    }
                    return $this->redirect(['showformulario'
                                , "formulario_id" => $model->id
                                , "preview" => 0
                                , "escalado" => 0
                                , "view" => $view
                                , "showInteraccion" => base64_encode($showInteraccion)
                                , "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                }
                /**
                 * Se envia datos a la aplicacion amigo, indicando que se realizo una valoracion
                 */
                //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                /**/
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

                return $this->redirect(['interaccionmanual']);
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
            public function actionShowformulariobyarbol($tmp_id, $preview = 0, $fill_values = FALSE) {

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
                //busco el formulario al cual esta atado la valoracion a cargar
                //y valido de q si tenga un formulario, de lo contrario se fija 
                //en 1 por defecto
                $data->formulario = Formularios::find()->where(['id' => $data->tmp_formulario->formulario_id])->one();
                //$data->formulario = Formularios::find()->where(['id' => $data->tmp_formulario->formulario_id])->one();
                if (!isset($TmpForm->subi_calculo)) {
                    //$TmpForm->subi_calculo = $data->formulario->subi_calculo;
                    if (isset($data->formulario->subi_calculo)) {
                        $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                        $TmpForm->save();
                        $array_indices_TmpForm = \app\models\Textos::find()
                                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                ->asArray()
                                ->all();
                        foreach ($array_indices_TmpForm as $value) {
                            $data->indices_calcular[$value['id']] = $value['text'];
                        }
                    }
                } else {
                    if (isset($data->formulario->subi_calculo)) {
                        $array_indices_TmpForm = \app\models\Textos::find()
                                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                ->asArray()
                                ->all();
                        foreach ($array_indices_TmpForm as $value) {
                            $data->indices_calcular[$value['id']] = $value['text'];
                        }
                    }
                }
                return $this->render('show-formulario', [
                            'data' => $data,
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
            public function actionShowformulariodiligenciado($feedback_id) {

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
                            , "escalado" => 0
                            , "fill_values" => true
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
            public function actionShowformulariodiligenciadohistorico($tmp_id) {
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
                            , "escalado" => 0
                            , "fill_values" => true
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
            public function actionEditarformulariodiligenciado($tmp_id) {
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
                //TO-DO  : COMENTAR LINEA EN CASO DE NO NECESITAR LO DE ADICIONAR Y ESCALAR -> no se necesita
                /* $validarRegistro = \app\models\RegistroEjec::findOne(['ejec_form_id' => $tmp_id]);
                  if (isset($validarRegistro)) {
                  \app\models\RegistroEjec::updateAll(['ejec_form_id' => $formId[0]["tmp_id"]], ['ejec_form_id' => $tmp_id]);
                  } */
                //FIN
                  //$buuuu = 'aaaaa';
                return $this->redirect(['showformulario'
                            , "formulario_id" => $formId[0]["tmp_id"]
                            //, "este" => $buuuu
                            , "preview" => 0
                            , "escalado" => 0
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
            public function actionShowformulariodiligenciadoamigo($form_id) {

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
                    //busco el formulario al cual esta atado la valoracion a cargar
                    //y valido de q si tenga un formulario, de lo contrario se fija 
                    //en 1 por defecto
                    $data->formulario = Formularios::find()->where(['id' => $data->tmp_formulario->formulario_id])->one();
                    if (/* !isset($data->formulario->subi_calculo) || */!isset($TmpForm->subi_calculo)) {
                        //$TmpForm->subi_calculo = $data->formulario->subi_calculo;
                        if (isset($data->formulario->subi_calculo)) {
                            $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                            $TmpForm->save();
                            $array_indices_TmpForm = \app\models\Textos::find()
                                    ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                    ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                    ->asArray()
                                    ->all();
                            foreach ($array_indices_TmpForm as $value) {
                                $data->indices_calcular[$value['id']] = $value['text'];
                            }
                        }
                    } else {
                        if (isset($data->formulario->subi_calculo)) {
                            $array_indices_TmpForm = \app\models\Textos::find()
                                    ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                    ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                    ->asArray()
                                    ->all();
                            foreach ($array_indices_TmpForm as $value) {
                                $data->indices_calcular[$value['id']] = $value['text'];
                            }
                        }
                    }
                    return $this->render('show-formulario', [
                                'data' => $data,
                    ]);
                }
            }

            /**
             * Action para calcular los porcentajes de los sub i seleccionados
             * en el formulario
             *      
             * @return mixed
             * @author Sebastian  Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionConsultarcalificacionsubi() {

                $arrCalificaciones = !$_POST['calificaciones'] ? array() : $_POST['calificaciones'];
                $arrTipificaciones = !isset($_POST['tipificaciones']) ? array() : $_POST['tipificaciones'];
                $arrSubtipificaciones = !isset($_POST['subtipificaciones']) ? array() : $_POST['subtipificaciones'];
                $arrComentariosSecciones = !$_POST['comentarioSeccion'] ? array() : $_POST['comentarioSeccion'];
                $arrCheckPits = !isset($_POST['checkPits']) ? array() : $_POST['checkPits'];

                $arrFormulario = [];

                $tmp_id = $_POST['tmp_formulario_id'];
                $arrFormulario["equipo_id"] = $_POST['form_equipo_id'];
                $arrFormulario["usua_id_lider"] = $_POST['form_lider_id'];
                $arrFormulario["dimension_id"] = $_POST['dimension_id'];
                $arrFormulario["dsruta_arbol"] = $_POST['ruta_arbol'];
                $arrFormulario["dscomentario"] = $_POST['comentarios_gral'];
                $arrFormulario["dsfuente_encuesta"] = $_POST['fuente'];
                $arrFormulario["transacion_id"] = $_POST['transacion_id'];
                $arrFormulario["sn_mostrarcalculo"] = 1;
                //$arrFormulario["subi_calculo"] = !isset($_POST['subi_calculo']) ? '' : $_POST['subi_calculo'];
                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                if ($_POST['subi_calculo'] != '') {
                    $data->subi_calculo .=',' . $_POST['subi_calculo'];
                    $data->save();
                }
                //$formulario = Formularios::find()->where(['id' => $data->tmp_formulario->formulario_id])->one();
                /* if (isset($formulario->subi_calculo)) {
                  $arrFormulario["subi_calculo"] .= ','.$formulario->subi_calculo;
                  } */
                /* EDITO EL TMP FORMULARIO */
                \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);

                /* GUARDO LAS CALIFICACIONES */
                foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                    $arrDetalleForm = [];
                    if (count($arrCheckPits) > 0) {
                        if (isset($arrCheckPits[$form_detalle_id])) {
                            $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                        }
                    }
                    if (empty($calif_detalle_id)) {
                        continue;
                    }
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
                //$data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                //VALIDACION TIPO DE INTERACCION
                if ($data->tipo_interaccion == 0) {
                    $showInteraccion = $showBtnIteraccion = 1;
                } else {
                    $showInteraccion = $showBtnIteraccion = 0;
                }
                $data->generarCalculos($tmp_id);
                Yii::$app->session->setFlash('success', Yii::t('app', 'Indices calculados'));
                return $this->redirect([
                            'showformulario',
                            "formulario_id" => $tmp_id,
                            "preview" => 0,
                            "escalado" =>0,
                            "showInteraccion" => base64_encode($showInteraccion),
                            "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
            }

            /**
             * Obtiene el listado de Metricas
             * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @param type $search
             * @param type $id
             */
            public function actionMetricalistmultiple($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }
                $out = ['more' => false];
                //$id[]='11,12';
                if (!is_null($search)) {
                    $data = \app\models\Textos::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                            ->where('detexto LIKE "%' . $search . '%"')
                            ->andWhere('id NOT IN (11,12)')
                            ->orderBy('detexto')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Textos::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                            ->where('id IN (' . $id . ')')
                            ->andWhere('id NOT IN (11,12)')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de Metricas
             * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @param type $search
             * @param type $id
             */
            public function actionMetricalistmultipleform($search = null, $ids_selec = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }
                $out = ['more' => false];
                if (!is_null($search)) {

                    $data = \app\models\Textos::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                            ->where('detexto LIKE "%' . $search . '%"')
                            ->andWhere('id NOT IN (' . $ids_selec . ',11,12)')
                            ->orderBy('detexto')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    if (isset($_POST['ids_selec'])) {
                        $ids_selec.=$_POST['ids_selec'] . ',11,12';
                    } else {
                        $ids_selec = '11,12';
                    }
                    $data = \app\models\Textos::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                            ->where('id IN (' . $id . ')')
                            ->andWhere('id NOT IN (' . $ids_selec . ')')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            public function actionAdicionarform() {
                $tmp_id = \Yii::$app->request->get('tmp_form');
                $model = new \app\models\RegistroEjec();
                $model_tmp_ejec = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                $model->scenario = 'adicionar';
                if (Yii::$app->getRequest()->isAjax) {
                    $model->valorado_id = $model_tmp_ejec->evaluado_id;
                    $model->pcrc_id = $model_tmp_ejec->arbol_id;
                    $model->dimension_id = $model_tmp_ejec->dimension_id;
                    return $this->renderAjax('adicionarValoracion', ['model' => $model, 'modelTmpeje' => $model_tmp_ejec]);
                } else {
                    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                        $tmp_id = $model->ejec_form_id;
                        $arbol_id = $model->pcrc_id;
                        $dimension_id = $model->dimension_id;
                        $formulario_id = $model_tmp_ejec->formulario_id;
                        $evaluado_id = $model->valorado_id;
                        //$tipoInteraccion = $model_tmp_ejec->tipo_interaccion;
                        $usua_id = Yii::$app->user->identity->id;
                        $created = date("Y-m-d H:i:s");
                        $sneditable = 1;

                        $tmpeje = new \app\models\Tmpejecucionformularios();
                        $tmpeje->dimension_id = $dimension_id;
                        $tmpeje->arbol_id = $arbol_id;
                        $tmpeje->usua_id = $usua_id;
                        $tmpeje->evaluado_id = $evaluado_id;
                        $tmpeje->formulario_id = $formulario_id;
                        $tmpeje->created = $created;
                        $tmpeje->sneditable = $sneditable;
                        $tmpeje->basesatisfaccion_id = $model_tmp_ejec->basesatisfaccion_id;


                        $model->fecha_modificacion = $created;
                        //EN CASO DE SELECCIONAR ITERACCION AUTOMATICA
                        //CONSULTAMOS LA ITERACCION
                        if ($model->tipo_interaccion == 0) {
                            $tipoInteraccion = $model_tmp_ejec->tipo_interaccion;
                            if ($tipoInteraccion == 0) {
                                //CONSULTA DE LLAMADAS Y PANTALLAS CON WS
                                /* try {
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
                                  } */
                                $tmpeje->url_llamada = $model_tmp_ejec->url_llamada;

                                $showInteraccion = 1;
                                $showBtnIteraccion = 1;
                            } else {
                                $showInteraccion = 0;
                                $showBtnIteraccion = 0;
                            }
                        } else {
                            $showInteraccion = 0;
                            $showBtnIteraccion = 0;
                        }
                        $tmpeje->ejec_principal = $tmp_id;
                        $model->valorador_id = $usua_id;
                        $tmpeje->tipo_interaccion = $tipoInteraccion;
                        $model->save();
                        $tmpeje->save();
                        $model_tmp_ejec->save();
                        $idTmp = $tmpeje->id;
                        $preview = 0;
                        if ($tmpeje->basesatisfaccion_id != "") {
                            return $this->redirect([
                                        "basesatisfaccion/showformulariogestion",
                                        "basesatisfaccion_id" => $model_tmp_ejec->basesatisfaccion_id,
                                        "preview" => $preview,
                                        "fill_values" => false,
                                        "banderaescalado" => true,
                                        "idtmp" => $idTmp,
                                        "showInteraccion" => base64_encode($showInteraccion),
                                        "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                        }
                        return $this->redirect([
                                    "showformulario",
                                    "formulario_id" => $idTmp,
                                    "preview" => $preview,
                                    "showInteraccion" => base64_encode($showInteraccion),
                                    "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                    } else {
                        Yii::$app->session->setFlash('danger', 'Los campos Valorado, Programa/PCRC y Dimensión son obligatorios');
                        return $this->redirect('interaccionmanual');
                    }
                }
            }

            public function actionEscalarform() {
                $tmp_id = \Yii::$app->request->get('tmp_form');
                $escaladobase = \Yii::$app->request->get('banderaescalado');
                $escaladoform = \Yii::$app->request->get('escalado');
                
                $model = new \app\models\RegistroEjec();
                $model_tmp_ejec = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                $model->scenario = 'escalar';
                
                if (Yii::$app->getRequest()->isAjax) {
                    $model->valorado_id = $model_tmp_ejec->evaluado_id;
                    $model->pcrc_id = $model_tmp_ejec->arbol_id;
                    $model->dimension_id = $model_tmp_ejec->dimension_id;
                    //$model->valorador_id = $model_tmp_ejec->usua_id;
                    return $this->renderAjax('escalarValoracion', ['model' => $model, 'modelTmpeje' => $model_tmp_ejec]);
                } else {
                    //print_r('2'); die;
                    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                        //print_r('3'); die;
                        $datos = Yii::$app->request->post('RegistroEjec');
                        $enviar_new_form = '1';
                        $tmp_id = $model->ejec_form_id;
                        $arbol_id = $model->pcrc_id;
                        $dimension_id = $model_tmp_ejec->dimension_id;
                        $formulario_id = $model_tmp_ejec->formulario_id;
                        $evaluado_id = $model->valorado_id;
                        //$tipoInteraccion = $model_tmp_ejec->tipo_interaccion;
                        $usua_id = $model->valorador_id;
                        $created = date("Y-m-d H:i:s");
                        $sneditable = 1;
                        if ($enviar_new_form == 0) {
                            //print_r('4'); die;
                            $tmpeje = new \app\models\Tmpejecucionformularios();
                            $tmpeje->dimension_id = $dimension_id;
                            $tmpeje->arbol_id = $arbol_id;
                            $tmpeje->usua_id = $usua_id;
                            $tmpeje->evaluado_id = $evaluado_id;
                            $tmpeje->formulario_id = $formulario_id;
                            $tmpeje->created = $created;
                            $tmpeje->sneditable = $sneditable;
                            $tmpeje->estado = 'Abierto';
                            $model->fecha_modificacion = $created;
                            $tmpeje->basesatisfaccion_id = $model_tmp_ejec->basesatisfaccion_id;
                            $tmpeje->escalado = 1;
                            //EN CASO DE SELECCIONAR ITERACCION AUTOMATICA
                            //CONSULTAMOS LA ITERACCION
                            if ($model->tipo_interaccion == 0) {
                                //print_r('5'); die;
                                $tipoInteraccion = $model_tmp_ejec->tipo_interaccion;
                                if ($tipoInteraccion == 0) {
                                    //print_r('6'); die;
                                    $tmpeje->url_llamada = $model_tmp_ejec->url_llamada;
                                    $showInteraccion = 1;
                                    $showBtnIteraccion = 1;
                                } else {
                                    //print_r('7'); die;
                                    $showInteraccion = 0;
                                    $showBtnIteraccion = 0;
                                }
                            } else {
                                //print_r('8'); die;
                                $showInteraccion = 0;
                                $showBtnIteraccion = 0;
                            }
                            $tmpeje->ejec_principal = $tmp_id;
                            $model->valorador_id = $usua_id;
                            $tmpeje->tipo_interaccion = $tipoInteraccion;
                            $model->dimension_id = $dimension_id;
                            $model->save();
                            $tmpeje->save();
                            $model_tmp_ejec->save();
                            $idTmp = $tmpeje->id;
                            $preview = 0;
                            Yii::$app->session->setFlash('danger', 'Se creó y  escaló la valoración con éxito');
                            if ($tmpeje->basesatisfaccion_id != "") {
                                //print_r('9'); die;
                                return $this->redirect([
                                            "basesatisfaccion/showformulariogestion",
                                            "basesatisfaccion_id" => $model_tmp_ejec->basesatisfaccion_id,
                                            "preview" => $preview,
                                            "fill_values" => false,
                                            "banderaescalado" => true,
                                            "idtmp" => $idTmp,
                                            "showInteraccion" => base64_encode($showInteraccion),
                                            "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                            }
                            return $this->redirect([
                                        "showformulario",
                                        "formulario_id" => $model_tmp_ejec->id,
                                        "preview" => $preview,
                                        "escalado" => true,
                                        "showInteraccion" => base64_encode($showInteraccion),
                                        "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                        } else {
                            //print_r('10'); die;
                            //$model_tmp_ejec = new \app\models\Tmpejecucionformularios();
                            //$model_tmp_ejec->dimension_id = $dimension_id;
                            $model_tmp_ejec->arbol_id = $arbol_id;
                            $model_tmp_ejec->usua_id = $usua_id;
                            $model_tmp_ejec->evaluado_id = $evaluado_id;
                            $model_tmp_ejec->formulario_id = $formulario_id;
                            $model_tmp_ejec->created = $created;
                            $model_tmp_ejec->sneditable = $sneditable;
                            $model_tmp_ejec->estado = 'Abierto';
                            $model->fecha_modificacion = $created;
                            $model_tmp_ejec->basesatisfaccion_id = $model_tmp_ejec->basesatisfaccion_id;
                            $model_tmp_ejec->escalado = 1;
                            $model_tmp_ejec->save();
                            $model->dimension_id = $dimension_id;
                            $model->descripcion = $model['descripcion'];
                            $model->valorador_inicial_id = Yii::$app->user->identity->id;
                            $model->save();



                            if ($escaladobase != ""){
                                /* LIBERO LA VALORACION */
                                $prueba = \app\models\Tmpejecucionformularios::find('basesatisfaccion_id')->where(['id' => $tmp_id])->one();
                                $modelBase = \app\models\BaseSatisfaccion::findOne($prueba->basesatisfaccion_id);
                                $redct = ($modelBase->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
                                if (Yii::$app->user->identity->username == $modelBase->responsable) {
                                    //print_r('12'); die;
                                    $modelBase->escalado = 1;
                                    $modelBase->usado = "NO";
                                    $modelBase->save();
                                }

                            }


                            if ($escaladoform== 1 OR $escaladobase== 1){
                                /* Cuando la valoracion es escalada se actualiza la descripcion y el que envia */
                                $BSQid = \app\models\RegistroEjec::find('id')->where(['ejec_form_id' => $tmp_id])->one();
                                $model2 = \app\models\RegistroEjec::findOne($BSQid->id);
                                $model2->descripcion = $model['descripcion'];
                                $model2->valorador_inicial_id = Yii::$app->user->identity->id;
                                $model2->save();
                            }

                            
                            //$preview = 0;
                            Yii::$app->session->setFlash('danger', 'Se ha escalado la valoración con éxito');
                            return $this->redirect(["indexescaladosenviados"]);
                        }
                    } else {
                        //print_r('15'); die;
                        Yii::$app->session->setFlash('danger', 'Los campos Valorado, Programa/PCRC y Valorador son obligatorios');
                        $preview = 0;
                        $tipoInteraccion = $model_tmp_ejec->tipo_interaccion;
                        if ($tipoInteraccion == 0) {
                            //print_r('16'); die;
                            $showInteraccion = 1;
                            $showBtnIteraccion = 1;
                        } else {
                            //print_r('17'); die;
                            $showInteraccion = 0;
                            $showBtnIteraccion = 0;
                        }
                        if ($escaladobase != ""){
                        /* LIBERO LA VALORACION */
                            $prueba = \app\models\Tmpejecucionformularios::find('basesatisfaccion_id')->where(['id' => $tmp_id])->one();
                            $modelBase = \app\models\BaseSatisfaccion::findOne($prueba->basesatisfaccion_id);
                            $redct = ($modelBase->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
                            if (Yii::$app->user->identity->username == $modelBase->responsable) {
                                //print_r('12'); die;
                                $modelBase->usado = "NO";
                                $modelBase->save();
                            }

                        }


                        if ($escaladoform== 1 OR $escaladobase== 1){
                            /* Cuando la valoracion es escalada se actualiza la descripcion y el que envia */
                            $BSQid = \app\models\RegistroEjec::find('id')->where(['ejec_form_id' => $tmp_id])->one();
                            $model2 = \app\models\RegistroEjec::findOne($BSQid->id);
                            $model2->descripcion = $model['descripcion'];
                            $model2->valorador_inicial_id = Yii::$app->user->identity->id;
                            $model2->save();
                        }

                        if ($tmpeje->basesatisfaccion_id != "") {
                            //print_r('18'); die;
                            return $this->redirect([
                                        "basesatisfaccion/showformulariogestion",
                                        "basesatisfaccion_id" => $model_tmp_ejec->basesatisfaccion_id,
                                        "preview" => $preview,
                                        "fill_values" => false,
                                        "banderaescalado" => true,
                                        "idtmp" => $idTmp,
                                        "showInteraccion" => base64_encode($showInteraccion),
                                        "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                        }
                        return $this->redirect([
                                    "showformulario",
                                    "formulario_id" => $model_tmp_ejec->id,
                                    "preview" => $preview,
                                    "escalado" => true,
                                    "showInteraccion" => base64_encode($showInteraccion),
                                    "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                    }
                }
            }

            /**
             * Acción para cerrar una gestión sin necesidad de diligenciarla
             * 
             * @param int $id datos de la baseinicial
             * 
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionCancelarformulario($id) {
                    print_r($id); die;
                    $model = \app\models\BaseSatisfaccion::findOne($id);
                    $model->usado = "NO";
                    $model->save();
                
            }

            /**
             * Obtiene el evaluado seleccionado en el formulario previo
             * @param type $search
             * @param type $arbol_id
             */
            public function actionEvaluadosbyform($search = null, $id = null) {
                $out = ['more' => false];
                if (!empty($id)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                            ->where('id = ' . $id)
                            ->orderBy('name')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            public function actionIndexescalados() {
                $model = new \app\models\Tmpejecucionformularios();
                $dataProvider = $model->searchTmpejecucionform(Yii::$app->request->queryParams);
                // echo "<pre>";
                // print_r($dataProvider); die;
                $model->scenario = 'tmpejecucionescalado';


                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0] . " 00:00:01";
                    $model->endDate = $dates[1] . " 23:59:59";
                    Yii::$app->session['rptfilterescalados'] = Yii::$app->request->post();
                    $dataProvider = $model->searchTmpejecucionform();
                }

                //print_r($model); die;
                return $this->render('indexEscalados', ['model' => $model, 'dataProvider' => $dataProvider]);
            }

            public function actionIndexescaladosenviados() {
                //print_r("hola"); die;
                $model = new \app\models\Tmpejecucionformularios();
                $dataProvider = $model->searchTmpejecucionformenviados(Yii::$app->request->queryParams);
                $model->scenario = 'tmpejecucionescalado';
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0] . " 00:00:01";
                    $model->endDate = $dates[1] . " 23:59:59";
                    Yii::$app->session['rptfilterescalados'] = Yii::$app->request->post();
                    $dataProvider = $model->searchTmpejecucionformenviados();
                }
                return $this->render('indexEscaladosEnviados', ['model' => $model, 'dataProvider' => $dataProvider]);
            }

            /**
             * Metodo para mostrar el formulario enviado por escalamiento
             * 
             * @param int $tmp_id
             * @return mixed
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionEditarformulariodiligenciadoescalado($tmp_id) {
                $data = \app\models\Tmpejecucionformularios::findOne(['id'=>$tmp_id]);

                //VALIDACION TIPO DE INTERACCION
                if ($data->tipo_interaccion == 0) {
                    $showInteraccion = 1;
                    $showBtnIteraccion = 1;
                } else {
                    $showInteraccion = 0;
                    $showBtnIteraccion = 0;
                }

                return $this->redirect(['showformulario'
                            , "formulario_id" => $tmp_id
                            , "preview" => 0
                            , "escalado" => 1
                            , "showInteraccion" => base64_encode($showInteraccion)
                            , "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
            }

            /**
             * Metodo para mostrar el formulario enviado por escalamiento
             * 
             * @param int $tmp_id
             * @return mixed
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionVerformulariodiligenciadoescalado($tmp_id) {
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                //VALIDACION TIPO DE INTERACCION
                if ($data->tipo_interaccion == 0) {
                    $showInteraccion = 1;
                    $showBtnIteraccion = 1;
                } else {
                    $showInteraccion = 0;
                    $showBtnIteraccion = 0;
                }
                return $this->redirect(['showformulario'
                            , "formulario_id" => $tmp_id
                            , "preview" => 1
                            , "escalado" => 0
                            , "fill_values" => true
                            , "showInteraccion" => base64_encode($showInteraccion)
                            , "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
            }

            /**
             * Obtiene el listado de evaluadores-Usuarios
             * @param type $search
             * @param type $id
             */
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
                    //agrego el usuario no definido solo para la visualizacion  en la inbox
                    //$data[] = ['id' => '1', 'text' => 'NO DEFINIDO'];
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_id = "' . $id . '"')
                            ->asArray()
                            ->all();
                    //agrego el usuario no definido solo para la visualizacion  en la inbox
                    //$data[] = ['id' => '1', 'text' => 'NO DEFINIDO'];
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Metodo para borrar el formulario diligenciado
             * 
             * @param int $tmp_id
             * @return mixed
             * @author Sebastian orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionBorrarformulariodiligenciadoescalado($tmp_id) {
                $model = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                //BORRAR EL FORMULARIO
                if ($model->delete()) {
                    Yii::$app->getSession()->setFlash('success', Yii::t('app', 'Formulario borrado'));
                } else {
                    Yii::$app->getSession()->setFlash('danger', 'Error eliminando formulario');
                }
                return $this->redirect(['indexescalados']);
            }

            /**
             * Obtiene el valorador
             * @param type $search
             * @param type $arbol_id
             */
            public function actionEvaluadoresbyarbolseleccescalado($search = null, $id = null) {
                $out = ['more' => false];
                if (!empty($id)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_id= ' . $id)
                            ->orderBy('usua_nombre')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

        }
        