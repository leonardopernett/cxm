<?php

namespace app\controllers;

use Yii;
use app\models\Tmpejecucionfeedbacks;
use app\models\TmpejecucionfeedbacksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;

/**
 * TmpejecucionfeedbacksController implements the CRUD actions for Tmpejecucionfeedbacks model.
 */
class TmpejecucionfeedbacksController extends Controller {

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
                                    'delete', 'gettipofeedback'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Tmpejecucionfeedbacks models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new TmpejecucionfeedbacksSearch();

                $tmp_formulario_id = Yii::$app->request->get('tmp_formulario_id');
                $usua_id_lider = Yii::$app->request->get('usua_id_lider');
                $evaluado_id = Yii::$app->request->get('evaluado_id');
                $basesatisfaccion_id = (Yii::$app->request->get('basesatisfacion_id') != '') ? Yii::$app->request->get('basesatisfacion_id') : '';
                $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
                $queryParams['TmpejecucionfeedbacksSearch']['tmpejecucionformulario_id'] = $tmp_formulario_id;
                $queryParams['TmpejecucionfeedbacksSearch']['usua_id_lider'] = $usua_id_lider;
                $queryParams['TmpejecucionfeedbacksSearch']['evaluado_id'] = $evaluado_id;
                $queryParams['TmpejecucionfeedbacksSearch']['basessatisfaccion_id'] = $basesatisfaccion_id;
                $dataProvider = $searchModel->search($queryParams);
                if (Yii::$app->getRequest()->isAjax) {

                    return $this->renderAjax('index', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                    ]);
                } else {
                    return $this->redirect(['tmpejecucionfeedbacks/index']);
                }
                /* $searchModel = new TmpejecucionfeedbacksSearch();
                  $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                  return $this->render('index', [
                  'searchModel' => $searchModel,
                  'dataProvider' => $dataProvider,
                  ]); */
            }

            /**
             * Displays a single Tmpejecucionfeedbacks model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                $model = $this->findModel($id);
                if (Yii::$app->getRequest()->isAjax) {
                    return $this->renderAjax('view', [
                                'model' => $model,
                    ]);
                } else {
                    return $this->redirect(['tmpejecucionfeedbacks/index']);
                }
                /* return $this->render('view', [
                  'model' => $this->findModel($id),
                  ]); */
            }

            /**
             * Creates a new Tmpejecucionfeedbacks model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Tmpejecucionfeedbacks();

                if (Yii::$app->getRequest()->isAjax) {

                    $tmp_formulario_id = Yii::$app->request->get('tmp_formulario_id');
                    $usua_id_lider = Yii::$app->request->get('usua_id_lider');
                    $evaluado_id = Yii::$app->request->get('evaluado_id');
                    $basesatisfaccion_id = Yii::$app->request->get('basessatisfaccion_id');
                    $model->tmpejecucionformulario_id = $tmp_formulario_id;
                    $model->usua_id_lider = $usua_id_lider;
                    $model->evaluado_id = $evaluado_id;
                    $model->basessatisfaccion_id = $basesatisfaccion_id;
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        return $this->renderAjax('view', [
                                    'model' => $model,
                        ]);
                    } else {
                        return $this->renderAjax('create', [
                                    'model' => $model,
                        ]);
                    }
                } else {
                    return $this->redirect(['tmpejecucionfeedbacks/index']);
                }
            }

            /**
             * Updates an existing Tmpejecucionfeedbacks model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);

                if (Yii::$app->getRequest()->isAjax) {
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        return $this->renderAjax('view', [
                                    'model' => $model,
                        ]);
                    } else {
                        return $this->renderAjax('update', [
                                    'model' => $model,
                        ]);
                    }
                } else {
                    return $this->redirect(['tmpejecucionfeedbacks/index']);
                }
            }

            /**
             * Deletes an existing Tmpejecucionfeedbacks model.
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
                    $searchModel = new TmpejecucionfeedbacksSearch();

                    $tmp_formulario_id = Yii::$app->request->get('tmp_formulario_id');
                    $usua_id_lider = Yii::$app->request->get('usua_id_lider');
                    $evaluado_id = Yii::$app->request->get('evaluado_id');
                    $basesatisfaccion_id = (Yii::$app->request->get('basesatisfacion_id') != '') ? Yii::$app->request->get('basesatisfacion_id') : '';
                    $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
                    $queryParams['TmpejecucionfeedbacksSearch']['tmpejecucionformulario_id'] = $tmp_formulario_id;
                    $queryParams['TmpejecucionfeedbacksSearch']['usua_id_lider'] = $usua_id_lider;
                    $queryParams['TmpejecucionfeedbacksSearch']['evaluado_id'] = $evaluado_id;
                    $queryParams['TmpejecucionfeedbacksSearch']['basessatisfaccion_id'] = $basesatisfaccion_id;
                    $dataProvider = $searchModel->search($queryParams);
                    return $this->renderAjax('index', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                    ]);
                    /*                     * Yii::$app->request->url = \yii\helpers\Url::to(['index'
                      , 'tmp_formulario_id' => $model->tmpejecucionformulario_id
                      , 'usua_id_lider' => $model->usua_id_lider
                      , 'evaluado_id' => $model->evaluado_id]);
                      $this->run('index'); */
                } else {
                    return $this->redirect(['tmpejecucionfeedbacks/index']);
                }
            }

            /**
             * Finds the Tmpejecucionfeedbacks model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Tmpejecucionfeedbacks the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Tmpejecucionfeedbacks::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

            /**
             * Selector tipo de feedback
             * 
             * @return Html
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGettipofeedback() {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = [];
                $html = "";
                if (isset($_POST["cat_id"]) && !empty($_POST["cat_id"]) && is_numeric($_POST["cat_id"])) {
                    $out = \app\models\Tipofeedbacks::getTipofeedbacksListByID($_POST["cat_id"]);
                    if (count($out)) {
                        foreach ($out as $value) {
                            $html .= "<option value='" . $value['id'] . "'>"
                                    . $value['name'] . "</option>";
                        }
                    }
                }

                echo $html;
            }

        }
        