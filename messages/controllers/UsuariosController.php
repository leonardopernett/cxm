<?php

namespace app\controllers;

use Yii;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller {

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
                                'actions' => ['index', 'create', 'update', 'view',
                                    'delete', 'export', 'limpiarfiltros'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Usuarios models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new UsuariosSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
                $isAjax = false;
                if (Yii::$app->getRequest()->isAjax) {
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    $isAjax = true;
                    if (Yii::$app->request->post()) {
                        Yii::$app->session['rptFilterUsuarios'] = Yii::$app->request->post();
                        Yii::$app->session['rptFilterUsuarios']['grupo'] = Yii::$app->request->get('grupo_id');
                        $dataProvider = $searchModel->searchAjax(Yii::$app->session['rptFilterUsuarios']);
                    } else {
                        $dataProvider = $searchModel->searchAjax($grupo_id);
                    }
                    return $this->renderAjax('index', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                                'grupo_id' => $grupo_id,
                                'isAjax' => $isAjax,
                    ]);
                }
                if (Yii::$app->request->post()) {
                    Yii::$app->session['rptFilterUsuarios'] = Yii::$app->request->post();
                    $dataProvider = $searchModel->search(Yii::$app->request->post());
                } else {
                    $dataProvider = $searchModel->search(Yii::$app->session['rptFilterUsuarios']);
                }
                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'isAjax' => $isAjax,
                ]);
            }

            /**
             * Displays a single Usuarios model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                $isAjax = false;

                if (Yii::$app->getRequest()->isAjax) {
                    $isAjax = true;
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    return $this->renderPartial('view', [
                                'model' => $this->findModel($id),
                                'isAjax' => $isAjax,
                                'grupo_id' => $grupo_id,
                    ]);
                }
                return $this->render('view', [
                            'model' => $this->findModel($id),
                            'isAjax' => $isAjax,
                ]);
            }

            /**
             * Creates a new Usuarios model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Usuarios();
                $isAjax = false;
                if (Yii::$app->getRequest()->isAjax) {
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    $isAjax = true;

                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        $rolUsuario = new \app\models\RelUsuariosRoles();
                        $rolUsuario->rel_usua_id = $model->usua_id;
                        $rolUsuario->rel_role_id = $model->rol;
                        $rolUsuario->save();
                        $grupousuario = new \app\models\RelGruposUsuarios();
                        $grupousuario->grupo_id = $grupo_id;
                        $grupousuario->usuario_id = $model->usua_id;
                        $grupousuario->save();
                        return $this->renderPartial('view', [
                                    'model' => $model,
                                    'isAjax' => $isAjax,
                                    'grupo_id' => $grupo_id,
                        ]);
                    } else {
                        return $this->renderPartial('create', [
                                    'model' => $model,
                                    'grupo_id' => $grupo_id,
                                    'isAjax' => $isAjax,
                        ]);
                    }
                }

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    $rolUsuario = new \app\models\RelUsuariosRoles();
                    $rolUsuario->rel_usua_id = $model->usua_id;
                    $rolUsuario->rel_role_id = $model->rol;
                    $rolUsuario->save();
                    if (isset($model->grupo)) {
                        $grupousuaruio = new \app\models\RelGruposUsuarios();
                        $grupousuaruio->grupo_id = $model->grupo;
                        $grupousuaruio->usuario_id = $model->usua_id;
                        $grupousuaruio->save();
                    }
                    return $this->redirect(['view', 'id' => $model->usua_id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                                'isAjax' => $isAjax,
                    ]);
                }
            }

            /**
             * Updates an existing Usuarios model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);
                $roles = \app\models\RelUsuariosRoles::find()->where(
                                ['rel_usua_id' => $model->usua_id])->one();
                $model->rol = (isset($roles->rel_role_id)) ? $roles->rel_role_id : '';
                $grupos = \app\models\RelGruposUsuarios::find()->where(
                                ['usuario_id' => $model->usua_id])->one();
                $isAjax = false;
                if (Yii::$app->getRequest()->isAjax) {
                    $isAjax = true;
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        if (!isset($roles)) {
                            $roles = new \app\models\RelUsuariosRoles();
                            $roles->rel_role_id = $model->rol;
                            $roles->rel_usua_id = $model->usua_id;
                        } else {
                            $roles->rel_role_id = $model->rol;
                        }
                        $roles->save();
                        return $this->renderPartial('view', [
                                    'model' => $model,
                                    'isAjax' => $isAjax,
                                    'grupo_id' => $grupo_id,
                        ]);
                    } else {
                        return $this->renderPartial('update', [
                                    'model' => $model,
                                    'grupo_id' => $grupo_id,
                                    'isAjax' => $isAjax,
                        ]);
                    }
                }
                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    if (!isset($roles)) {
                        $roles = new \app\models\RelUsuariosRoles();
                        $roles->rel_role_id = $model->rol;
                        $roles->rel_usua_id = $model->usua_id;
                    } else {
                        $roles->rel_role_id = $model->rol;
                    }
                    $grupos->grupo_id = $model->grupo;
                    $grupos->save();
                    $roles->save();
                    return $this->redirect(['view', 'id' => $model->usua_id]);
                } else {
                    $model->grupo = $grupos->grupo_id;
                    return $this->render('update', [
                                'model' => $model,
                                'isAjax' => $isAjax,
                    ]);
                }
            }

            /**
             * Deletes an existing Usuarios model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                return $this->redirect(['index']);
            }

            /**
             * Finds the Usuarios model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Usuarios the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Usuarios::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

            /**
             * Funcion que permite exportar la sabana de datos de los usuarios
             * teniendo en cuenta los filtros ingresados.
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionExport() {
                $searchModel = new UsuariosSearch();
                $dataProvider = $searchModel->searchExport(Yii::$app->request->post());
                if ($dataProvider !== false) {
                    $searchModel->generarReporteUsuarios($dataProvider);
                }
            }

            /**
             * Funcion que permite limpiar los filtro de busqueda y redirecciona al index
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionLimpiarfiltros() {
                Yii::$app->session->remove('rptFilterUsuarios');
                $this->redirect(['index']);
            }

        }
        