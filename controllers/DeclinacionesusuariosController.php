<?php

namespace app\controllers;

use Yii;
use app\models\DeclinacionesUsuarios;
use app\models\DeclinacionesUsuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DeclinacionesUsuariosController implements the CRUD actions for DeclinacionesUsuarios model.
 */
class DeclinacionesUsuariosController extends Controller {

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
                                'actions' => ['create'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isCuadroMando() || Yii::$app->user->identity->isReportes();
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
             * Lists all DeclinacionesUsuarios models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new DeclinacionesUsuariosSearch();
                $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single DeclinacionesUsuarios model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new DeclinacionesUsuarios model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new DeclinacionesUsuarios();

                if (Yii::$app->request->get()) {
                    $model->arbol_id = Yii::$app->request->get('arbol_id');
                    $model->dimension_id = Yii::$app->request->get('dimension_id');
                    $model->evaluado_id = Yii::$app->request->get('evaluado_id');
                    $model->url = Yii::$app->request->get('url');
                    $model->usua_id = Yii::$app->user->identity->id;
                    $model->fecha = date('Y-m-d H:i:s');
                    $model->formulario_id = Yii::$app->request->get('formulario_id');
                }

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    Yii::$app->db->createCommand()->insert('tbl_logs', [
                        'usua_id' => Yii::$app->user->identity->id,
                        'usuario' => Yii::$app->user->identity->username,
                        'fechahora' => date('Y-m-d h:i:s'),
                        'ip' => Yii::$app->getRequest()->getUserIP(),
                        'accion' => 'Create',
                        'tabla' => 'tbl_declinaciones_usuarios'
                    ])->execute();
                    $showInteraccion = 1;
                    $showBtnIteraccion = 1;
                    $msg = '';
                    try {
                        $modelFormularios = new \app\models\Formularios;
                        $enlaces = $modelFormularios->getEnlaces($model->evaluado_id);

                        if ($enlaces && count($enlaces) > 0) {
                            $json = json_encode($enlaces);
                            $array['url_llamada'] = $json;
                            \app\models\Tmpejecucionformularios::updateAll(
                                    $array, ['id' => $model->formulario_id]);
                        } else {
                            $array['url_llamada'] = "";
                            \app\models\Tmpejecucionformularios::updateAll(
                                    $array, ['id' => $model->formulario_id]);
                            $msg = Yii::t('app', 'Error redbox');
                            Yii::$app->session->setFlash('danger', $msg);
                        }
                    } catch (\Exception $exc) {
                        \Yii::error('#####' . __FILE__ . ':' . __LINE__
                                . $exc->getMessage() . '#####', 'redbox');
                        $msg = Yii::t('app', 'Error redbox');
                        Yii::$app->session->setFlash('danger', $msg);
                    }

                    return $this->redirect(["formularios/showformulario",
                                "formulario_id" => $model->formulario_id,
                                "preview" => 0,
                                "showInteraccion" => base64_encode($showInteraccion),
                                "showBtnIteraccion" => base64_encode($showBtnIteraccion)]);
                } else {
                    return $this->renderAjax('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing DeclinacionesUsuarios model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    Yii::$app->db->createCommand()->insert('tbl_logs', [
                        'usua_id' => Yii::$app->user->identity->id,
                        'usuario' => Yii::$app->user->identity->username,
                        'fechahora' => date('Y-m-d h:i:s'),
                        'ip' => Yii::$app->getRequest()->getUserIP(),
                        'accion' => 'Update',
                        'tabla' => 'tbl_declinaciones_usuarios'
                    ])->execute();
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('update', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Deletes an existing DeclinacionesUsuarios model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                $this->findModel($id)->delete();

                Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Delete',
                    'tabla' => 'tbl_declinaciones_usuarios'
                ])->execute();

                return $this->redirect(['index']);
            }

            /**
             * Finds the DeclinacionesUsuarios model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return DeclinacionesUsuarios the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = DeclinacionesUsuarios::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }
        