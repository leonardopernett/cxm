<?php

namespace app\Controllers;

use Yii;
use app\models\Bloquedetalles;
use app\models\BloquedetallesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;

/**
 * BloquedetallesController implements the CRUD actions for Bloquedetalles model.
 */
class BloquedetallesController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
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
     * Lists all Bloquedetalles models.
     * @return mixed
     */
    public function actionIndex() {
        $filterBloque = false;
        $isAjax = false;
        //Tomamos el parametro por get------------------------------------------
        $bloqueId = Yii::$app->request->get('bloque_id');

        if (!empty($bloqueId)) {
            $filterBloque = true;
        }
        //Modifiacion de dataptovider para busqueda por defecto ----------------       
        $searchModel = new BloquedetallesSearch();
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['BloquedetallesSearch']['bloque_id'] = $bloqueId;
        $dataProvider = $searchModel->search($queryParams);        

        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            return $this->renderAjax('index',
                            [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'isAjax' => $isAjax,
            ]);
            
        }

        return $this->render('index',
                        [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'filterBloque' => $filterBloque,
                    'isAjax' => $isAjax,
        ]);
    }

    /**
     * Displays a single Bloquedetalles model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        $filterBloque = false;
        //Tomamos el parametro por get------------------------------------------
        $bloqueId = Yii::$app->request->get('bloque_id');
        if (!empty($bloqueId)) {
            $filterBloque = true;
        }
        $isAjax = false;
        //Render para request ajax----------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            return $this->renderPartial('view',
                            [
                        'model' => $model,
                        'isAjax' => $isAjax,
            ]);
            
        }
        return $this->render('view',
                        [
                    'model' => $model,
                    'isAjax' => $isAjax,
                    'filterBloque' => $filterBloque,
        ]);
    }

    /**
     * Creates a new Bloquedetalles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Bloquedetalles();
        $model->nmorden = 0;
        $model->i1_nmfactor = 0;
        $model->i2_nmfactor = 0;
        $model->i3_nmfactor = 0;
        $model->i4_nmfactor = 0;
        $model->i5_nmfactor = 0;
        $model->i6_nmfactor = 0;
        $model->i7_nmfactor = 0;
        $model->i8_nmfactor = 0;
        $model->i9_nmfactor = 0;
        $model->i10_nmfactor = 0;

        $filterBloque = false;
        $bloqeId = Yii::$app->request->get('bloque_id');
        if (!empty($bloqeId)) {
            $filterBloque = true;
            $model->bloque_id = $bloqeId;
        }

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
                    'accion' => 'Create',
                    'tabla' => 'tbl_bloquedetalles'
                ])->execute();
                return $this->renderPartial('view',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'bloque_id' => $bloqeId,
                ]);
            } else {
                return $this->renderPartial('create',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'bloque_id' => $bloqeId,
                ]);
            }
        }
        //----------------------------------------------------------------------

        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view',
                            'id' => $model->id,
                            'filterBloque' => $filterBloque,
                            'bloque_id' => $bloqeId,]);
            } else {
                return $this->render('create',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'filterBloque' => $filterBloque,
                            'bloque_id' => $bloqeId,
                ]);
            }
        } catch (Exception $exc) {
            Yii::warning($exc->getMessage());
        }
    }

    /**
     * Updates an existing Bloquedetalles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $filterBloque = false;
        $bloqeId = Yii::$app->request->get('bloque_id');
        if (!empty($bloqeId)) {
            $filterBloque = true;
        }
        $isAjax = false;
        //Render para request ajax----------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            $bloqeId = Yii::$app->request->get('bloque_id');
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                \Yii::$app->db->createCommand()->insert('tbl_logs', [
                    'usua_id' => Yii::$app->user->identity->id,
                    'usuario' => Yii::$app->user->identity->username,
                    'fechahora' => date('Y-m-d h:i:s'),
                    'ip' => Yii::$app->getRequest()->getUserIP(),
                    'accion' => 'Update',
                    'tabla' => 'tbl_bloquedetalles'
                ])->execute();
                return $this->renderPartial('view',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'bloque_id' => $bloqeId,
                ]);
            } else {
                return $this->renderPartial('update',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'bloque_id' => $bloqeId,
                ]);
            }
        }
        //----------------------------------------------------------------------
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view',
                            'id' => $model->id,
                            'filterBloque' => $filterBloque,
                            'bloque_id' => $bloqeId]);
            } else {
                return $this->render('update',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'filterBloque' => $filterBloque,
                            'bloque_id' => $bloqeId,
                ]);
            }
        } catch (Exception $exc) {
            Yii::warning($exc->getMessage());
        }
    }

    /**
     * Deletes an existing Bloquedetalles model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        //Eliminacion por ajax--------------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            try {                
                $model = $this->findModel($id);                
                if (!$model->delete()) {
                    Yii::$app->getSession()->setFlash('danger',
                            Yii::t('app',
                                    'No puede eliminar el detalle "'
                                    . $model->name
                                    . '" porque corresponde al formulario de '
                                    . 'una o mas personas evaluadas'));
                }else {
                    \Yii::$app->db->createCommand()->insert('tbl_logs', [
                        'usua_id' => Yii::$app->user->identity->id,
                        'usuario' => Yii::$app->user->identity->username,
                        'fechahora' => date('Y-m-d h:i:s'),
                        'ip' => Yii::$app->getRequest()->getUserIP(),
                        'accion' => 'Delete',
                        'tabla' => 'tbl_bloquedetalles'
                    ])->execute();
                }                      
            } catch (\yii\db\IntegrityException $exc) {
                \Yii::error($exc->getMessage(), 'db');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'Integrity constraint violation seccions'));
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'error exception'));
            }

            Yii::$app->request->url = \yii\helpers\Url::to(['index', 'bloque_id'=>$model->bloque_id]);
            $this->run('index');
            Yii::$app->end();
        }

        //Eliminacion por post--------------------------------------------------
        if (Yii::$app->getRequest()->isPost) {
            $bloqueId = Yii::$app->request->get('bloque_id');
            try {
                $model = $this->findModel($id);
                if (!$model->delete()) {
                    Yii::$app->getSession()->setFlash('danger',
                            Yii::t('app',
                                    'No puede eliminar el detalle "'
                                    . $model->name
                                    . '" porque corresponde al formulario de '
                                    . 'una o mas personas evaluadas'));
                }
            } catch (\yii\db\IntegrityException $exc) {
                \Yii::error($exc->getMessage(), 'db');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'Integrity constraint violation seccions'));
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'error exception'));
            }

            return $this->redirect(['index', 'bloque_id' => $bloqueId]);
        }
    }

    /**
     * Finds the Bloquedetalles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bloquedetalles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Bloquedetalles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
