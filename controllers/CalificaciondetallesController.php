<?php

namespace app\Controllers;

use Yii;
use app\models\Calificaciondetalles;
use app\models\CalificaciondetallesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use Exception;

/**
 * CalificaciondetallesController implements the CRUD actions for Calificaciondetalles model.
 */
class CalificaciondetallesController extends Controller {

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
     * Lists all Calificaciondetalles models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new CalificaciondetallesSearch();

        $calificacionId = Yii::$app->request->get('calificacion_id');
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['CalificaciondetallesSearch']['calificacion_id'] = $calificacionId;
        $dataProvider = $searchModel->search($queryParams);

        if (Yii::$app->getRequest()->isAjax) {

            return $this->renderAjax('index',
                            [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
            
        } else {
            return $this->redirect(['calificacions/index']);
        }
    }

    /**
     * Displays a single Calificaciondetalles model.
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
            return $this->redirect(['calificacions/index']);
        }
    }

    /**
     * Creates a new Calificaciondetalles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Calificaciondetalles();
        $model->i1_povalor = 1;
        $model->i2_povalor = 1;
        $model->i3_povalor = 1;
        $model->i4_povalor = 1;
        $model->i5_povalor = 1;
        $model->i6_povalor = 1;
        $model->i7_povalor = 1;
        $model->i8_povalor = 1;
        $model->i9_povalor = 1;
        $model->i10_povalor = 1;

        if (Yii::$app->getRequest()->isAjax) {
            $calificacionId = Yii::$app->request->get('calificacion_id');
            $model->calificacion_id = $calificacionId;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view', ['model' => $model]);
            } else {
                return $this->renderPartial('create', ['model' => $model]);
            }
            
        } else {
            return $this->redirect(['calificacions/index']);
        }
    }    

    /**
     * Updates an existing Calificaciondetalles model.
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
            
        } else {
            return $this->redirect(['calificacions/index']);
        }
    }

    /**
     * Deletes an existing Calificaciondetalles model.
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

            Yii::$app->request->url = \yii\helpers\Url::to(['index', 'calificacion_id'=>$model->calificacion_id]);
            $this->run('index');
            Yii::$app->end();
        } else {
            return $this->redirect(['calificacions/index']);
        }
    }

    /**
     * Finds the Calificaciondetalles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Calificaciondetalles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Calificaciondetalles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
