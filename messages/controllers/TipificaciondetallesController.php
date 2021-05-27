<?php

namespace app\Controllers;

use Yii;
use app\models\Tipificaciondetalles;
use app\models\TipificaciondetallesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TipificaciondetallesController implements the CRUD actions for Tipificaciondetalles model.
 */
class TipificaciondetallesController extends Controller {

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

    /**
     * Lists all Tipificaciondetalles models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new TipificaciondetallesSearch();

        $tipificacionId = Yii::$app->request->get('tipificacion_id');
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['TipificaciondetallesSearch']['tipificacion_id'] = $tipificacionId;
        $dataProvider = $searchModel->search($queryParams);

        if (Yii::$app->getRequest()->isAjax) {

            return $this->renderAjax('index',
                            [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
            
        } else {
            return $this->redirect(['tipificaciones/index']);
        }
    }

    /**
     * Displays a single Tipificaciondetalles model.
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
            return $this->redirect(['tipificaciones/index']);
        }
    }

    /**
     * Creates a new Tipificaciondetalles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Tipificaciondetalles();
        
        if (Yii::$app->getRequest()->isAjax) {
            $tipificacionId = Yii::$app->request->get('tipificacion_id');
            $model->tipificacion_id = $tipificacionId;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view', ['model' => $model]);
            } else {
                return $this->renderPartial('create', ['model' => $model]);
            }
            Yii::$app->end();
        } else {
            return $this->redirect(['tipificaciones/index']);
        }       
    }

    /**
     * Updates an existing Tipificaciondetalles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if (Yii::$app->getRequest()->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view', ['model' => $model]);
            } else {
                return $this->renderPartial('update', ['model' => $model]);
            }
            Yii::$app->end();
        } else {
            return $this->redirect(['tipificaciones/index']);
        }
    }

    /**
     * Deletes an existing Tipificaciondetalles model.
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
                                    'No se pudo eliminar el detalle "'
                                    . $model->name
                                    . '" de la tipificación.'));
                }
            } catch (\yii\db\IntegrityException $exc) {
                \Yii::error($exc->getMessage(), 'db');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app',
                                    'No se pudo eliminar el detalle "'
                                    . $model->name
                                    . '" de la tipificación.'));
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app',
                                    'No se pudo eliminar el detalle "'
                                    . $model->name
                                    . '" de la tipificación.'));
            }

            Yii::$app->request->url = \yii\helpers\Url::to(['index', 'tipificacion_id'=>$model->tipificacion_id]);
            $this->run('index');
            Yii::$app->end();
        } else {
            return $this->redirect(['tipificaciones/index']);
        }
    }

    /**
     * Finds the Tipificaciondetalles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tipificaciondetalles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tipificaciondetalles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
