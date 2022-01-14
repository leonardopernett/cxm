<?php

namespace app\controllers;

use Yii;
use app\models\InformeInboxAleatorio;
use app\models\InformeInboxAleatorioSatuSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InformeinboxaleatorioController implements the CRUD actions for InformeInboxAleatorio model.
 */
class InformeinboxaleatorioController extends Controller {

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
                                'actions' => ['index', 'view'],
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
             * Lists all InformeInboxAleatorio models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new InformeInboxAleatorioSatuSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                if (Yii::$app->request->get('page')) {
                    $searchModel->load(Yii::$app->session['searchInforme']);
                    $dataProvider = $searchModel->searchInforme();
                }

                if ($searchModel->load(Yii::$app->request->post())) {
                    $dataProvider = $searchModel->searchInforme();
                    Yii::$app->session['searchInforme'] = Yii::$app->request->post();
                }

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single InformeInboxAleatorio model.
             * @param integer $id
             * @param string $pcrc
             * @return mixed
             */
            public function actionView($id, $pcrc) {
                return $this->render('view', [
                            'model' => $this->findModel($id, $pcrc),
                ]);
            }

            /**
             * Creates a new InformeInboxAleatorio model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new InformeInboxAleatorio();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id, 'pcrc' => $model->pcrc]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing InformeInboxAleatorio model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @param string $pcrc
             * @return mixed
             */
            public function actionUpdate($id, $pcrc) {
                $model = $this->findModel($id, $pcrc);

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id, 'pcrc' => $model->pcrc]);
                } else {
                    return $this->render('update', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Deletes an existing InformeInboxAleatorio model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @param string $pcrc
             * @return mixed
             */
            public function actionDelete($id, $pcrc) {
                $this->findModel($id, $pcrc)->delete();

                return $this->redirect(['index']);
            }

            /**
             * Finds the InformeInboxAleatorio model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @param string $pcrc
             * @return InformeInboxAleatorio the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id, $pcrc) {
                if (($model = InformeInboxAleatorio::findOne(['id' => $id, 'pcrc' => $pcrc])) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }
        