<?php

namespace app\controllers;

use Yii;
use app\models\Tmptableroexperiencias;
use app\models\TmptableroexperienciasSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\Json;
use yii\base\Exception;

/**
 * TmptableroexperienciasController implements the CRUD actions for Tmptableroexperiencias model.
 */
class TmptableroexperienciasController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post', 'get'],
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
                                'actions' => ['index', 'create', 'update', 'view',
                                    'delete', 'gettableroproblemadetalle'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isReportes();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Tmptableroexperiencias models.
             * @return mixed
             */
            public function actionIndex() {

                $searchModel = new TmptableroexperienciasSearch();

                $tmp_formulario_id = Yii::$app->request->get('tmp_formulario_id');
                $arbol_id = Yii::$app->request->get('arbol_id');

                $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
                $queryParams['TmptableroexperienciasSearch']['tmpejecucionformulario_id'] = $tmp_formulario_id;
                $dataProvider = $searchModel->search($queryParams);

                if (Yii::$app->getRequest()->isAjax) {

                    return $this->renderAjax('index', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                                'arbol_id' => $arbol_id
                    ]);
                } else {
                    return $this->redirect(['tmptableroexperiencias/index']);
                }
            }

            /**
             * Displays a single Tmptableroexperiencias model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id,$arbol_id) {
                $model = $this->findModel($id);
                if (Yii::$app->getRequest()->isAjax) {
                    return $this->renderAjax('view', [
                                'model' => $model,
                                'arbol_id' => $arbol_id,
                    ]);
                } else {
                    return $this->redirect(['tmptableroexperiencias/index']);
                }
            }

            /**
             * Creates a new Tmptableroexperiencias model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Tmptableroexperiencias ();

                if (Yii::$app->getRequest()->isAjax) {
                    $tmp_formulario_id = Yii::$app->request->get('tmp_formulario_id');
                    //CONSULTO EL PROBLEMA ID
                    $problema_id = \app\models\Arboles::find()
                            ->select("tableroproblema_id")
                            ->where(["id" => Yii::$app->request->get('arbol_id')])
                            ->all();
                    $model->tmpejecucionformulario_id = $tmp_formulario_id;
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        return $this->renderAjax('view', [
                                    'model' => $model,
                                    'problema_id' => $problema_id[0]["tableroproblema_id"]
                        ]);
                    } else {
                        return $this->renderAjax('create', [
                                    'model' => $model,
                                    'problema_id' => $problema_id[0]["tableroproblema_id"],
                        ]);
                    }
                } else {
                    return $this->redirect(['tmptableroexperiencias/index']);
                }
            }

            /**
             * Updates an existing Tmptableroexperiencias model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id, $arbol_id) {
                $model = $this->findModel($id);

                if (Yii::$app->getRequest()->isAjax) {
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        return $this->renderAjax('view', [
                                    'model' => $model,
                            'arbol_id' => $arbol_id,
                        ]);
                    } else {
                        return $this->renderAjax('update', [
                                    'model' => $model,
                                    'arbol_id' => $arbol_id,
                        ]);
                    }
                } else {
                    return $this->redirect(['tmptableroexperiencias/index']);
                }
            }

            /**
             * Deletes an existing Tmptableroexperiencias model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                if (Yii::$app->getRequest()->isAjax) {
                    try {
                        $model = $this->findModel($id);
                        if (!$model->delete()) {
                            Yii::$app->getSession()->setFlash('danger'
                                    , Yii::t('app', 'No puede eliminar el registro'));
                        }
                    } catch (\yii\db\IntegrityException $exc) {
                        \Yii::error($exc->getMessage(), 'db');
                        Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Integrity constraint violation seccions'));
                    } catch (Exception $exc) {
                        \Yii::error($exc->getMessage(), 'exception');
                        Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'error exception'));
                    }

                    $searchModel = new TmptableroexperienciasSearch();

                    $tmp_formulario_id = Yii::$app->request->get('tmp_formulario_id');
                    $arbol_id = Yii::$app->request->get('arbol_id');

                    $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
                    $queryParams['TmptableroexperienciasSearch']['tmpejecucionformulario_id'] = $tmp_formulario_id;
                    $dataProvider = $searchModel->search($queryParams);

                    return $this->renderAjax('index', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                                'arbol_id' => $arbol_id
                    ]);
                } else {
                    return $this->redirect(['tmptableroexperiencias/index']);
                }
            }

            /**
             * Finds the Tmptableroexperiencias model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Tmptableroexperiencias the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Tmptableroexperiencias::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

            /**
             * Selector problema detalle
             * 
             * @return Json
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGettableroproblemadetalle() {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = [];
                $html = "";
                if (isset(Yii::$app->request->post("tab_id")) && !empty(Yii::$app->request->post("tab_id")) && is_numeric(Yii::$app->request->post("tab_id"))) {
                    $out = \app\models\Tableroproblemadetalles::getAllProblemsDetByEnfoqueID(Yii::$app->request->post("tab_id"));
                    if (count($out) > 0) {
                        foreach ($out as $value) {
                            $html .= "<option value='" . $value['id'] . "'>" . $value['name']
                                    . "</option>";
                        }
                    }
                }

                echo $html;
            }

        }
        