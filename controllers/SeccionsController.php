<?php

namespace app\Controllers;

use Yii;
use app\models\Seccions;
use app\models\SeccionsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\db\Exception;

/**
 * SeccionsController implements the CRUD actions for Seccions model.
 */
class SeccionsController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'delete' => ['post', 'get'],
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
     * Lists all Seccions models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new SeccionsSearch();

        $formularioId = Yii::$app->request->get('formulario_id');
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['SeccionsSearch']['formulario_id'] = $formularioId;
        $dataProvider = $searchModel->search($queryParams);

        $isAjax = false;
        //Render para request ajax----------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            return $this->renderAjax('index',
                            [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'isAjax' => $isAjax,
            ]);
            
        }
        //----------------------------------------------------------------------
        return $this->render('index',
                        [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'isAjax' => $isAjax,
        ]);
    }

    /**
     * Displays a single Seccions model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        $isAjax = false;
        //Render para request ajax----------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            return $this->renderPartial('view',
                            [
                        'model' => $model,
                        'formulario_id' => $model->formulario_id,
                        'isAjax' => $isAjax,
            ]);
            
        }
        //----------------------------------------------------------------------

        return $this->render('view',
                        [
                    'model' => $model,
                    'isAjax' => $isAjax,
        ]);
    }

    /**
     * Creates a new Seccions model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Seccions();
        $model->nmorden = 1;
        $model->i1_nmfactor = 1;
        $model->i2_nmfactor = 1;
        $model->i3_nmfactor = 1;
        $model->i4_nmfactor = 1;
        $model->i5_nmfactor = 1;
        $model->i6_nmfactor = 1;
        $model->i7_nmfactor = 1;
        $model->i8_nmfactor = 1;
        $model->i9_nmfactor = 1;
        $model->i10_nmfactor = 1;

        $isAjax = false;
        //Render para request ajax----------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            $formulario_id = Yii::$app->request->get('formulario_id');
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                \Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Create',
                    'tabla' => 'tbl_seccions'
                ])->execute();
                return $this->renderPartial('view',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'formulario_id' => $formulario_id,
                ]);
            } else {
                return $this->renderPartial('create',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'formulario_id' => $formulario_id,
                ]);
            }
        }
        //----------------------------------------------------------------------

        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                return $this->render('create',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                ]);
            }
        } catch (Exception $exc) {
            Yii::warning($exc->getMessage());
        }
    }

    /**
     * Updates an existing Seccions model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        $isAjax = false;
        //Render para request ajax----------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                \Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Update',
                    'tabla' => 'tbl_seccions'
                ])->execute();
                return $this->renderPartial('view',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'formulario_id' => $model->formulario_id,
                ]);
            } else {
                return $this->renderPartial('update',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'formulario_id' => $model->formulario_id,
                ]);
            }
        }
        //----------------------------------------------------------------------

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update',
                            [
                        'model' => $model,
                        'isAjax' => $isAjax,
            ]);
        }
    }

    /**
     * Deletes an existing Seccions model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {

        //Eliminacion por ajax--------------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            try {
                $model = $this->findModel($id);
                $model->delete();

                \Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Delete',
                    'tabla' => 'tbl_seccions'
                ])->execute();
                            
            } catch (\yii\db\IntegrityException $exc) {
                \Yii::error($exc->getMessage(), 'db');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'Integrity constraint violation seccions'));
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'error exception'));
            }

            Yii::$app->request->url = \yii\helpers\Url::to(['index', 'formulario_id' => $model->formulario_id]);
            $this->run('index');
            Yii::$app->end();
        }

        //Eliminacion por post--------------------------------------------------
        if (Yii::$app->getRequest()->isPost) {
            try {
                $this->findModel($id)->delete();
            } catch (\yii\db\IntegrityException $exc) {
                \Yii::error($exc->getMessage(), 'db');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'Integrity constraint violation seccions'));
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'error exception'));
            }

            return $this->redirect(['index']);
        }
    }

    /**
     * Finds the Seccions model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Seccions the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Seccions::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
