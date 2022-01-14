<?php

namespace app\Controllers;

use Yii;
use app\models\Tiposllamadasdetalles;
use app\models\TiposllamadasdetallesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;

/**
 * TiposllamadasdetallesController implements the CRUD actions for Tiposllamadasdetalles model.
 */
class TiposllamadasdetallesController extends Controller {

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
     * Lists all Tiposllamadasdetalles models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new TiposllamadasdetallesSearch();

        $tipoLlamadaId = Yii::$app->request->get('tiposllamada_id');
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['TiposllamadasdetallesSearch']['tiposllamada_id'] = $tipoLlamadaId;
        $dataProvider = $searchModel->search($queryParams);

        if (Yii::$app->getRequest()->isAjax) {

            return $this->renderAjax('index',
                            [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
            
        } else {
            return $this->redirect(['tiposllamadas/index']);
        }
    }

    /**
     * Displays a single Tiposllamadasdetalles model.
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
            return $this->redirect(['tiposllamadas/index']);
        }
    }

    /**
     * Creates a new Tiposllamadasdetalles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Tiposllamadasdetalles();

        if (Yii::$app->getRequest()->isAjax) {
            $tipoLlamadaId = Yii::$app->request->get('tiposllamada_id');
            $model->tiposllamada_id = $tipoLlamadaId;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view', ['model' => $model]);
            } else {
                return $this->renderPartial('create', ['model' => $model]);
            }
        } else {
            return $this->redirect(['tiposllamadas/index']);
        }
    }

    /**
     * Updates an existing Tiposllamadasdetalles model.
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
            return $this->redirect(['tiposllamadas/index']);
        }
    }

    /**
     * Deletes an existing Tiposllamadasdetalles model.
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
                                    'No puede eliminar el detalle de la llamada"'
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

            Yii::$app->request->url = \yii\helpers\Url::to(['index', 'tiposllamada_id'=>$model->tiposllamada_id]);
            $this->run('index');
            Yii::$app->end();
        } else {
            return $this->redirect(['tiposllamadas/index']);
        }
    }

    /**
     * Finds the Tiposllamadasdetalles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tiposllamadasdetalles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tiposllamadasdetalles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
