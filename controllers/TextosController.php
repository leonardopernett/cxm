<?php

namespace app\Controllers;

use Yii;
use app\models\Textos;
use app\models\TextosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TextosController implements the CRUD actions for Textos model.
 */
class TextosController extends Controller {

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
                                'actions' => ['index', 'update'],
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

            public function actions() {
                return [
                    'error' => [
                      'class' => 'yii\web\ErrorAction',
                    ]
                ];
            }
        
            public function actionError() {
        
                //ERROR PRESENTADO
                $exception = Yii::$app->errorHandler->exception;
        
                if ($exception !== null) {
                    //VARIABLES PARA LA VISTA ERROR
                    $code = $exception->statusCode;
                    $name = $exception->getName() . " (#$code)";
                    $message = $exception->getMessage();
                    //VALIDO QUE EL ERROR VENGA DEL CLIENTE DE IVR Y QUE SOLO APLIQUE
                    // PARA LOS ERRORES 400
                    $request = \Yii::$app->request->pathInfo;
                    if ($request == "basesatisfaccion/clientebasesatisfaccion" && $code ==
                            400) {
                        //GUARDO EN EL ERROR DE SATU
                        $baseSat = new BasesatisfaccionController();
                        $baseSat->setErrorSatu(\Yii::$app->request->url, $name . ": " . $message);
                    }
                    //RENDERIZO LA VISTA
                    return $this->render('error', [
                                'name' => $name,
                                'message' => $message,
                                'exception' => $exception,
                    ]);
                }
            }

            /**
             * Lists all Textos models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new TextosSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single Textos model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new Textos model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Textos();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing Textos model.
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
             * Deletes an existing Textos model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                $this->findModel($id)->delete();

                return $this->redirect(['index']);
            }

            /**
             * Finds the Textos model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Textos the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Textos::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }
        