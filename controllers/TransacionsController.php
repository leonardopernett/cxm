<?php

namespace app\controllers;

use Yii;
use app\models\Transacions;
use app\models\TransacionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransacionsController implements the CRUD actions for Transacions model.
 */
class TransacionsController extends Controller {

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
                            return Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isVerusuatlmast() || Yii::$app->user->identity->isVermodificaformulario();
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
             * Lists all Transacions models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new TransacionsSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single Transacions model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new Transacions model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Transacions();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    \Yii::$app->db->createCommand()->insert('tbl_logs', [
                        'usua_id' => Yii::$app->user->identity->id,
                        'usuario' => Yii::$app->user->identity->username,
                        'fechahora' => date('Y-m-d h:i:s'),
                        'ip' => Yii::$app->getRequest()->getUserIP(),
                        'accion' => 'Create',
                        'tabla' => 'tbl_transacions'
                    ])->execute();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing Transacions model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    \Yii::$app->db->createCommand()->insert('tbl_logs', [
                        'usua_id' => Yii::$app->user->identity->id,
                        'usuario' => Yii::$app->user->identity->username,
                        'fechahora' => date('Y-m-d h:i:s'),
                        'ip' => Yii::$app->getRequest()->getUserIP(),
                        'accion' => 'Update',
                        'tabla' => 'tbl_transacions'
                    ])->execute();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('update', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Deletes an existing Transacions model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                $this->findModel($id)->delete();
                
                \Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Delete',
                    'tabla' => 'tbl_transacions'
                ])->execute();

                return $this->redirect(['index']);
            }

            /**
             * Finds the Transacions model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Transacions the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Transacions::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }
        