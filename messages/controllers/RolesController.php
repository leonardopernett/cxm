<?php

namespace app\Controllers;

use Yii;
use app\models\Roles;
use app\models\RolesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * RolesController implements the CRUD actions for Roles model.
 */
class RolesController extends Controller {

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
                                'actions' => ['index', 'create', 'update', 'view', 'delete', 'rolesmasivos', 'export'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        }
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Roles models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new RolesSearch();
                if (!Yii::$app->request->post()) {
                    $dataProvider = $searchModel->search(Yii::$app->session['rptFilterRoles']);
                } else {
                    $dataProvider = $searchModel->search(Yii::$app->request->post());
                    Yii::$app->session['rptFilterRoles'] = Yii::$app->request->post();
                }
                $model = new Roles();
                Yii::$app->session['rolPage'] = Yii::$app->request->url;

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'model' => $model,
                ]);
            }

            /**
             * Displays a single Roles model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new Roles model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Roles();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->role_id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing Roles model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(Yii::$app->session['rolPage']);
                } else {
                    return $this->render('update', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Deletes an existing Roles model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                $this->findModel($id)->delete();
                return $this->redirect(Yii::$app->session['rolPage']);
                //return $this->redirect(['index']);
            }

            /**
             * Finds the Roles model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Roles the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Roles::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

            public function actionRolesmasivos() {
                $model = new Roles();
                $arrDetalleForm = [];
                if ($model->load(Yii::$app->request->post())) {
                    $arrDetalleForm['per_cuadrodemando'] = $model->per_cuadrodemando;
                    $arrDetalleForm['per_adminprocesos'] = $model->per_adminprocesos;
                    $arrDetalleForm['per_adminsistema'] = $model->per_adminsistema;
                    $arrDetalleForm['per_editarequiposvalorados'] = $model->per_editarequiposvalorados;
                    $arrDetalleForm['per_estadisticaspersonas'] = $model->per_estadisticaspersonas;
                    $arrDetalleForm['per_hacermonitoreo'] = $model->per_hacermonitoreo;
                    $arrDetalleForm['per_inboxaleatorio'] = $model->per_inboxaleatorio;
                    $arrDetalleForm['per_modificarmonitoreo'] = $model->per_modificarmonitoreo;
                    $arrDetalleForm['per_reportes'] = $model->per_reportes;
                    $roles = Yii::$app->request->post('selection');
                    for ($index = 0; $index < count($roles); $index++) {
                        Roles::updateAll($arrDetalleForm, ['role_id' => $roles[$index]]);
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Proceso realizado con éxito'));
                }
                return $this->redirect('index');
            }

            /**
             * Funcion que permite exportar la sabana de datos del log de todos los roles con sus permisos
             * teniendo en cuenta los filtros ingresados.
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionExport() {
                $searchModel = new RolesSearch();
                Yii::$app->session['rptFilterRoles'] = Yii::$app->request->post();
                $dataProvider = $searchModel->searchExport(Yii::$app->request->post());
                if ($dataProvider !== false) {
                    $searchModel->generarReporteroles($dataProvider);
                }
            }

        }
        