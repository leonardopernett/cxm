<?php

namespace app\Controllers;

use Yii;
use app\models\Equipos;
use app\models\EquiposSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EquiposController implements the CRUD actions for Equipos model.
 */
class EquiposEvaluadoresController extends Controller {

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
                'denyCallback' => function ($action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
                },
                        'rules' => [
                            [
                                'actions' => ['index', 'create', 'update', 'view',
                                    'delete'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Equipos models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new \app\models\EquiposvaloradoresSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single Equipos model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new Equipos model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new \app\models\Equiposvaloradores();
                $model->nmumbral_verde = 1;
                $model->nmumbral_amarillo = 1;
                if ($model->load(Yii::$app->request->post())) {
                    $model->name = strtolower($model->name);
                    $model->save();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing Equipos model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);

                if ($model->load(Yii::$app->request->post())) {
                    $model->name = strtolower($model->name);
                    $model->save();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('update', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Deletes an existing Equipos model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                \app\models\RelEquiposEvaluadores::deleteAll(['equipo_id'=>$id]);
                $this->findModel($id)->delete();
                return $this->redirect(['index']);
            }

            /**
             * Finds the Equipos model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Equipos the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = \app\models\Equiposvaloradores::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }
        