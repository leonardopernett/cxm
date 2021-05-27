<?php

namespace app\Controllers;

use Yii;
use app\models\Tipificaciones;
use app\models\TipificacionesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TipificacionesController implements the CRUD actions for Tipificaciones model.
 */
class TipificacionesController extends Controller {

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
             * Lists all Tipificaciones models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new TipificacionesSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single Tipificaciones model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new Tipificaciones model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Tipificaciones();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing Tipificaciones model.
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
             * Deletes an existing Tipificaciones model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                $model = $this->findModel($id);
                try {            
                    if (!$model->delete()) {
                        Yii::$app->getSession()->setFlash('danger',
                                Yii::t('app',
                                        'No se pudo eliminar la tipificación "'
                                        . $model->name. '" ya que se encuentra relacionada'));
                    }
                } catch (\yii\db\IntegrityException $exc) {
                    \Yii::error($exc->getMessage(), 'db');
                    Yii::$app->getSession()->setFlash('danger',
                                Yii::t('app',
                                        'No se pudo eliminar la tipificación "'
                                        . $model->name. '" ya que se encuentra relacionada'));
                } catch (Exception $exc) {
                    \Yii::error($exc->getMessage(), 'exception');
                    Yii::$app->getSession()->setFlash('danger',
                                Yii::t('app',
                                        'No se pudo eliminar la tipificación "'
                                        . $model->name. '" ya que se encuentra relacionada'));
                }

                return $this->redirect(['index']);
            }

            /**
             * Finds the Tipificaciones model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Tipificaciones the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Tipificaciones::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }
        