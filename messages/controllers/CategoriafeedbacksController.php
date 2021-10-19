<?php

namespace app\controllers;

use Yii;
use yii\base\Exception;
use app\models\Categoriafeedbacks;
use app\models\CategoriafeedbacksSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CategoriafeedbacksController implements the CRUD actions for Categoriafeedbacks model.
 */
class CategoriafeedbacksController extends Controller {

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
                                    'delete'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Categoriafeedbacks models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new CategoriafeedbacksSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single Categoriafeedbacks model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new Categoriafeedbacks model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Categoriafeedbacks();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing Categoriafeedbacks model.
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
             * Deletes an existing Categoriafeedbacks model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                try {
                    $this->findModel($id)->delete();
                } catch (\yii\db\IntegrityException $exc) {
                    \Yii::error($exc->getMessage(), 'db');
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Integrity constraint violation categoria feedback'));
                } catch (Exception $exc) {
                    \Yii::error($exc->getMessage(), 'exception');
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'error exception'));
                }

                return $this->redirect(['index']);
            }

            /**
             * Finds the Categoriafeedbacks model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Categoriafeedbacks the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Categoriafeedbacks::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }
        