<?php

namespace app\Controllers;

use Yii;
use app\models\EquiposEvaluados;
use app\models\EquiposEvaluadosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EquiposEvaluadosController implements the CRUD actions for EquiposEvaluados model.
 */
class EquiposEvaluadosController extends Controller {

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
                        'actions' => ['index', 'evaluadolist', 'delete', 
                            'equipos', 'equiposlist', 'deleteequipo'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos() 
                                    || Yii::$app->user->identity->isEdEqipoValorado();
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all EquiposEvaluados models.
     * @return mixed
     */
    public function actionIndex() {        
        $searchModel = new EquiposEvaluadosSearch();
        $model = new EquiposEvaluados();
        
        $equipoId = Yii::$app->request->get('equipo_id');        
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['EquiposEvaluadosSearch']['equipo_id'] = $equipoId;        
        $dataProvider = $searchModel->search($queryParams);

        $model->equipo_id = $equipoId;

        //Validacion ajax ------------------------------------------------------
        if (Yii::$app->getRequest()->isAjax) {
            if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                if (!empty($model->evaluado_id)) {
                    $evaluados = explode(',', $model->evaluado_id);
                    if (count($evaluados) > 0) {
                        foreach ($evaluados as $evaluadoId) {
                            $modelTemp = new EquiposEvaluados();
                            $modelTemp->evaluado_id = $evaluadoId;
                            $modelTemp->equipo_id = $equipoId;
                            $modelTemp->save();
                        }
                    }
                }
                $model->evaluado_id = '';
                $dataProvider = $searchModel->search($queryParams);
                return $this->renderPartial('index',
                            [
                        'model' => $model,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
            
                
            }

            return $this->renderAjax('index',
                            [
                        'model' => $model,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
            
        }                     
        return $this->redirect(['equipos/index']);
    }

    /**
     * 
     * @param type $search
     * @param type $id
     */
    public function actionEvaluadolist($search = null, $id = null) {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->redirect(['equipos/index']);
        }
        $out = ['more' => false];
        $equipoId = Yii::$app->request->get('equipo_id');
        if (!is_null($search)) {
            $data = \app\models\Evaluados::find()
                    ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                    ->where('tbl_evaluados.id NOT IN ( '
                            . 'SELECT tbl_equipos_evaluados.evaluado_id AS id '
                            . 'FROM tbl_equipos_evaluados '
                            . 'WHERE equipo_id = ' . $equipoId . ') ' 
                            . 'AND name LIKE "%' . $search . '%"')
                    ->groupBy('tbl_evaluados.id')
                    ->orderBy('name')
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $ids = explode(',', $id);
            if (count($ids) > 0) {
                $data = \app\models\Evaluados::find()
                        ->select(['id', 'text' => 'name'])
                        ->where('id IN (' . $id . ')')
                        ->orderBy('name')
                        ->asArray()
                        ->all();
                $out['results'] = array_values($data);
            } else {
                $out['results'] = ['id' => 0, 'text' => Yii::t('app',
                            'No matching records found')];
            }            
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app',
                        'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }

    /**
     * Displays a single EquiposEvaluados model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view',
                        [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new EquiposEvaluados model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new EquiposEvaluados();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create',
                            [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing EquiposEvaluados model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update',
                            [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing EquiposEvaluados model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {

        if (Yii::$app->getRequest()->isAjax) {
            try {                
                $model = $this->findModel($id);                
                $model->delete();
                           
            } catch (\yii\db\IntegrityException $exc) {
                \Yii::error($exc->getMessage(), 'db');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'Integrity constraint violation seccions'));
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'error exception'));
            }
            
            Yii::$app->request->url = \yii\helpers\Url::to(['index', 'equipo_id'=>$model->equipo_id]);            
            $this->run('index');                                                    
            Yii::$app->end();
        }else{
            $this->run('index');
        }
    }
    
    /**
     * 
     * @return type
     */
    public function actionEquipos(){
        $searchModel = new EquiposEvaluadosSearch();
        $model = new EquiposEvaluados();
        
        $evaluadoId = Yii::$app->request->get('evaluado_id');              
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['EquiposEvaluadosSearch']['evaluado_id'] = $evaluadoId;        
        $dataProvider = $searchModel->searchEquipos($queryParams);
        $model->evaluado_id = $evaluadoId;
        
        if (Yii::$app->getRequest()->isAjax) {
             if ($model->load(Yii::$app->request->post()) && $model->validate()) {                 
                if (!empty($model->equipo_id)) {
                    $equipos = explode(',', $model->equipo_id);
                    if (count($equipos) > 0) {
                        foreach ($equipos as $equipoId) {
                            $modelTemp = new EquiposEvaluados();
                            $modelTemp->evaluado_id = $evaluadoId;
                            $modelTemp->equipo_id = $equipoId;
                            $modelTemp->save();
                        }
                    }
                }
                $model->equipo_id = '';
                $dataProvider = $searchModel->search($queryParams);
                return $this->renderPartial('equipos',
                            [
                        'model' => $model,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                ]);
                                
            }                                    
            return $this->renderAjax('equipos',
                            [
                        'model' => $model,
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
            ]);
            
        }
        return $this->redirect(['evaluados/index']);
    }
    
    /**
     * 
     * @param type $search
     * @param type $id
     */
    public function actionEquiposlist($search = null, $id = null) {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->redirect(['equipos/index']);
        }
        $out = ['more' => false];
        $evaluadoId = Yii::$app->request->get('evaluado_id');
        if (!is_null($search)) {
            $data = \app\models\Equipos::find()
                    ->select(['id' => 'tbl_equipos.id', 'text' => 'UPPER(name)'])
                    ->where('tbl_equipos.id NOT IN ( '
                            . 'SELECT tbl_equipos_evaluados.equipo_id AS id '
                            . 'FROM tbl_equipos_evaluados '
                            . 'WHERE evaluado_id = ' . $evaluadoId . ') ' 
                            . 'AND name LIKE "%' . $search . '%"')
                    ->groupBy('tbl_equipos.id')
                    ->orderBy('name')
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $ids = explode(',', $id);
            if (count($ids) > 0) {
                $data = \app\models\Equipos::find()
                        ->select(['id', 'text' => 'name'])
                        ->where('id IN (' . $id . ')')
                        ->orderBy('name')
                        ->asArray()
                        ->all();
                $out['results'] = array_values($data);
            } else {
                $out['results'] = ['id' => 0, 'text' => Yii::t('app',
                            'No matching records found')];
            }            
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app',
                        'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }
    
    public function actionDeleteequipo($id) {

        if (Yii::$app->getRequest()->isAjax) {
            try {                
                $model = $this->findModel($id);                
                $model->delete();
                           
            } catch (\yii\db\IntegrityException $exc) {
                \Yii::error($exc->getMessage(), 'db');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'Integrity constraint violation seccions'));
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'error exception'));
            }
            
            Yii::$app->request->url = \yii\helpers\Url::to(['equipos', 'evaluado_id'=>$model->evaluado_id]);            
            $this->run('equipos');                                                    
            Yii::$app->end();
        }else{
            $this->run('equipos');
        }
    }

    /**
     * Finds the EquiposEvaluados model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EquiposEvaluados the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = EquiposEvaluados::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

}
