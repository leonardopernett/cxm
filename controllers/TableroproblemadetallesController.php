<?php

namespace app\Controllers;

use Yii;
use app\models\Tableroproblemadetalles;
use app\models\TableroproblemadetallesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;

/**
 * TableroproblemadetallesController implements the CRUD actions for Tableroproblemadetalles model.
 */
class TableroproblemadetallesController extends Controller {

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
                    $msg = \Yii::t('app',
                                    'The requested Item could not be found.');
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
             * Lists all Tableroproblemadetalles models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new TableroproblemadetallesSearch();

                $problemaId = Yii::$app->request->get('tableroproblema_id');
                $queryParams = array_merge(array(),
                        Yii::$app->request->getQueryParams());
                $queryParams['TableroproblemadetallesSearch']['tableroproblema_id']
                        = $problemaId;
                $dataProvider = $searchModel->search($queryParams);

                if (Yii::$app->getRequest()->isAjax) {

                    return $this->renderAjax('index',
                                    [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                    ]);
                    
                } else {
                    return $this->redirect(['tableroproblema/index']);
                }
            }

            /**
             * Displays a single Tableroproblemadetalles model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                $model = $this->findModel($id);
                if (Yii::$app->getRequest()->isAjax) {
                    return $this->renderPartial('view',
                                    [
                                'model' => $model,
                    ]);
                    
                } else {
                    return $this->redirect(['tableroproblema/index']);
                }
            }

            /**
             * Creates a new Tableroproblemadetalles model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Tableroproblemadetalles();

                if (Yii::$app->getRequest()->isAjax) {
                    $problemaId = Yii::$app->request->get('tableroproblema_id');
                    $model->tableroproblema_id = $problemaId;
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        \Yii::$app->db->createCommand()->insert('tbl_logs', [
                            'usua_id' => Yii::$app->user->identity->id,
                            'usuario' => Yii::$app->user->identity->username,
                            'fechahora' => date('Y-m-d h:i:s'),
                            'ip' => Yii::$app->getRequest()->getUserIP(),
                            'accion' => 'Create',
                            'tabla' => 'tbl_tableroproblemadetalles'
                        ])->execute();
                        return $this->renderPartial('view',
                                        [
                                    'model' => $model,
                        ]);
                    } else {
                        return $this->renderPartial('create',
                                        [
                                    'model' => $model,
                        ]);
                    }
                } else {
                    return $this->redirect(['tableroproblema/index']);
                }
            }

            /**
             * Updates an existing Tableroproblemadetalles model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);

                if (Yii::$app->getRequest()->isAjax) {
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        \Yii::$app->db->createCommand()->insert('tbl_logs', [
                            'usua_id' => Yii::$app->user->identity->id,
                            'usuario' => Yii::$app->user->identity->username,
                            'fechahora' => date('Y-m-d h:i:s'),
                            'ip' => Yii::$app->getRequest()->getUserIP(),
                            'accion' => 'Update',
                            'tabla' => 'tbl_tableroproblemadetalles'
                        ])->execute();
                        return $this->renderPartial('view',
                                        [
                                    'model' => $model,
                        ]);
                    } else {
                        return $this->renderPartial('update',
                                        [
                                    'model' => $model,
                        ]);
                    }
                } else {
                    return $this->redirect(['tableroproblema/index']);
                }
            }

            /**
             * Deletes an existing Tableroproblemadetalles model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {

                if (Yii::$app->getRequest()->isAjax) {
                    try {
                        $model = $this->findModel($id);
                        if (!$model->delete()) {
                            Yii::$app->getSession()->setFlash('danger',
                                    Yii::t('app',
                                            'No puede eliminar el problema "'
                                            . $model->name
                                            . '" porque corresponde al formulario de '
                                            . 'una o mas personas evaluadas'));
                        }else{
                            \Yii::$app->db->createCommand()->insert('tbl_logs', [
                                'usua_id' => Yii::$app->user->identity->id,
                                'usuario' => Yii::$app->user->identity->username,
                                'fechahora' => date('Y-m-d h:i:s'),
                                'ip' => Yii::$app->getRequest()->getUserIP(),
                                'accion' => 'Delete',
                                'tabla' => 'tbl_tableroproblemadetalles'
                            ])->execute();
                        }
                    } catch (\yii\db\IntegrityException $exc) {
                        \Yii::error($exc->getMessage(), 'db');
                        Yii::$app->getSession()->setFlash('danger',
                                Yii::t('app',
                                        'Integrity constraint violation seccions'));
                    } catch (Exception $exc) {
                        \Yii::error($exc->getMessage(), 'exception');
                        Yii::$app->getSession()->setFlash('danger',
                                Yii::t('app', 'error exception'));
                    }

                    Yii::$app->request->url = \yii\helpers\Url::to(['index', 'tableroproblema_id' => $model->tableroproblema_id]);
                    $this->run('index');
                    Yii::$app->end();
                } else {
                    return $this->redirect(['tableroproblema/index']);
                }
            }

            /**
             * Finds the Tableroproblemadetalles model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Tableroproblemadetalles the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Tableroproblemadetalles::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

        }
        