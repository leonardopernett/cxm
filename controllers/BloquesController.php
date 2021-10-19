<?php

namespace app\Controllers;

use Yii;
use app\models\Bloques;
use app\models\BloquesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;

/**
 * BloquesController implements the CRUD actions for Bloques model.
 */
class BloquesController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'delete' => ['post'],
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

    /**
     * Lists all Bloques models.
     * @return mixed
     */
    public function actionIndex() {


        $filterSeccion = false;
        $isAjax = false;
        //Tomamos el parametro por get------------------------------------------
        $seccionId = Yii::$app->request->get('seccion_id');

        if (!empty($seccionId)) {
            $filterSeccion = true;
        }
        //Modifiacion de dataptovider para busqueda por defecto ----------------       
        $searchModel = new BloquesSearch();
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['BloquesSearch']['seccion_id'] = $seccionId;
        $dataProvider = $searchModel->search($queryParams);
        //----------------------------------------------------------------------        

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
                    'filterSeccion' => $filterSeccion,
                    'isAjax' => $isAjax,
        ]);
    }

    /**
     * Displays a single Bloques model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        $filterSeccion = false;
        //Tomamos el parametro por get------------------------------------------
        $seccionId = Yii::$app->request->get('seccion_id');
        if (!empty($seccionId)) {
            $filterSeccion = true;
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
        //----------------------------------------------------------------------
        return $this->render('view',
                        [
                    'model' => $model,
                    'isAjax' => $isAjax,
                    'filterSeccion' => $filterSeccion,
        ]);
    }

    /**
     * Creates a new Bloques model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Bloques();
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

        $filterSeccion = false;
        $seccionId = Yii::$app->request->get('seccion_id');
        if (!empty($seccionId)) {
            $filterSeccion = true;
            $model->seccion_id = $seccionId;
        }

        $isAjax = false;
        //Render para request ajax----------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'seccion_id' => $seccionId,
                ]);
            } else {
                return $this->renderPartial('create',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'seccion_id' => $seccionId,
                ]);
            }
        }
        //----------------------------------------------------------------------
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view',
                            'id' => $model->id,
                            'filterSeccion' => $filterSeccion,
                            'seccion_id' => $seccionId,]);
            } else {
                return $this->render('create',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'filterSeccion' => $filterSeccion,
                            'seccion_id' => $seccionId,
                ]);
            }
        } catch (Exception $exc) {
            Yii::warning($exc->getMessage());
        }
    }

    /**
     * Updates an existing Bloques model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $filterSeccion = false;
        $seccionId = Yii::$app->request->get('seccion_id');
        if (!empty($seccionId)) {
            $filterSeccion = true;
        }
        $isAjax = false;
        //Render para request ajax----------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            $seccion_id = Yii::$app->request->get('seccion_id');
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'seccion_id' => $seccion_id,
                ]);
            } else {
                return $this->renderPartial('update',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'seccion_id' => $seccion_id,
                ]);
            }
        }
        //----------------------------------------------------------------------
        try {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->redirect(['view',
                            'id' => $model->id,
                            'filterSeccion' => $filterSeccion,
                            'seccion_id' => $seccionId]);
            } else {
                return $this->render('update',
                                [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'filterSeccion' => $filterSeccion,
                            'seccion_id' => $seccionId
                ]);
            }
        } catch (Exception $exc) {
            Yii::warning($exc->getMessage());
        }
    }

    /**
     * Deletes an existing Bloques model.
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
                                    'No puede eliminar el bloque "'
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

            Yii::$app->request->url = \yii\helpers\Url::to(['index', 'seccion_id' => $model->seccion_id]);
            $this->run('index');
            Yii::$app->end();
        }

        //Eliminacion por post--------------------------------------------------
        if (Yii::$app->getRequest()->isPost) {
            $filterSeccion = false;
            $seccionId = Yii::$app->request->get('seccion_id');
            if (!empty($seccionId)) {
                $filterSeccion = true;
            }

            try {
                $model = $this->findModel($id);
                if (!$model->delete()) {
                    Yii::$app->getSession()->setFlash('danger',
                            Yii::t('app',
                                    'No puede eliminar el bloque "'
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

            return $this->redirect(['index', 'seccion_id' => $seccionId]);
        }
    }

    /**
     * Finds the Bloques model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Bloques the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Bloques::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
