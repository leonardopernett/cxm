<?php

namespace app\controllers;

use Yii;
use app\models\Tmptiposllamada;
use app\models\TmptiposllamadaSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;

/**
 * TmptiposllamadaController implements the CRUD actions for Tmptiposllamada model.
 */
class TmptiposllamadaController extends Controller {

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
                            'delete', 'gettiposllamadasdetalles'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo() 
                                    || Yii::$app->user->identity->isReportes();
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Tmptiposllamada models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new TmptiposllamadaSearch();

        $tmp_formulario_id = Yii::$app->request->get('tmp_formulario_id');
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['TmptiposllamadaSearch']['tmpejecucionformulario_id'] = $tmp_formulario_id;
        $dataProvider = $searchModel->search($queryParams);

        if (Yii::$app->getRequest()->isAjax) {

            return $this->renderAjax('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
            
        } else {
            return $this->redirect(['tmptiposllamada/index']);
        }        
    }

    /**
     * Displays a single Tmptiposllamada model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $model = $this->findModel($id);
        if (Yii::$app->getRequest()->isAjax) {
            return $this->renderPartial('view', [
                        'model' => $model,
            ]);
            
        } else {
            return $this->redirect(['tmptiposllamada/index']);
        }
    }

    /**
     * Creates a new Tmptiposllamada model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Tmptiposllamada();

        if (Yii::$app->getRequest()->isAjax) {
            $tmp_formulario_id = Yii::$app->request->get('tmp_formulario_id');
            $model->tmpejecucionformulario_id = $tmp_formulario_id;
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view', [
                            'model' => $model,
                ]);
            } else {
                return $this->renderAjax('create', [
                            'model' => $model,
                ]);
            }
        } else {
            return $this->redirect(['tmptiposllamada/index']);
        }
    }

    /**
     * Updates an existing Tmptiposllamada model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if (Yii::$app->getRequest()->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view', [
                            'model' => $model,
                ]);
            } else {
                return $this->renderPartial('update', [
                            'model' => $model,
                ]);
            }
        } else {
            return $this->redirect(['tmptiposllamada/index']);
        }
    }

    /**
     * Deletes an existing Tmptiposllamada model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        if (Yii::$app->getRequest()->isAjax) {
            try {
                $model = $this->findModel($id);
                if (!$model->delete()) {
                    Yii::$app->getSession()->setFlash('danger'
                            , Yii::t('app', 'No puede eliminar el registro'));
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

            Yii::$app->request->url = \yii\helpers\Url::to(['index', 
                'tmp_formulario_id' => $model->tmpejecucionformulario_id]);
            $this->run('index');
            Yii::$app->end();
        } else {
            return $this->redirect(['tmptiposllamada/index']);
        }
    }

    /**
     * Finds the Tmptiposllamada model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Tmptiposllamada the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Tmptiposllamada::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Selector problema detalle
     * 
     * @return String
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionGettiposllamadasdetalles() {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->goHome();
        }
        
        $out = [];
        $html = "";
        if(isset(Yii::$app->request->post("tiposllamadas_id")) 
                && !empty(Yii::$app->request->post("tiposllamadas_id")) 
                && is_numeric(Yii::$app->request->post("tiposllamadas_id"))){
            
            $out = \app\models\Tiposllamadasdetalles::getAllLlamdasDetByLlamaID(Yii::$app->request->post("tiposllamadas_id"));
            if(count($out)>0){
                foreach ($out as $value) {
                    $html .= "<option value='" . $value['id'] . "'>" 
                            . $value['name'] . "</option>";
                }
            }            
        }
        
        echo $html;        
    }

}
