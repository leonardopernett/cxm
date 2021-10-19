<?php

namespace app\controllers;

use Yii;
use app\models\Detalleparametrizacion;
use app\models\DetalleparametrizacionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DetalleparametrizacionController implements the CRUD actions for Detalleparametrizacion model.
 */
class DetalleparametrizacionController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Detalleparametrizacion models.
     * @return mixed
     */
    public function actionIndex($id, $categoriagestion) {
        $id_parametrizacion = $id;
        if ($categoriagestion != 0) {
            $modelGestionCategoria = \app\models\Categoriagestion::findOne($categoriagestion);
        } else {
            $modelGestionCategoria = new \app\models\Categoriagestion();
        }
        $searchModel = new DetalleparametrizacionSearch();
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['DetalleparametrizacionSearch']['id_categoriagestion'] = $categoriagestion;
        $idCategoriaGestion = $categoriagestion;
        $dataProvider = $searchModel->search($queryParams);
        $isAjax = false;
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            return $this->renderAjax('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'isAjax' => $isAjax,
                        'id_parametrizacion' => $id_parametrizacion,
                        'modelCategoriaGestion' => $modelGestionCategoria,
                        'idcategoriagestion' => ($idCategoriaGestion != 0) ? $idCategoriaGestion : 0,
            ]);
        } else {
            $isAjax = true;
            return $this->renderAjax('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'isAjax' => $isAjax,
                        'id_parametrizacion' => $id_parametrizacion,
                        'modelCategoriaGestion' => $modelGestionCategoria,
                        'idcategoriagestion' => ($idCategoriaGestion != 0) ? $idCategoriaGestion : 0,
            ]);
        }
    }

    /**
     * Displays a single Detalleparametrizacion model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Detalleparametrizacion model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Detalleparametrizacion();
        $nombre = Yii::$app->request->post('nombre');
        $id_parametrizacion = Yii::$app->request->post('idparame');
        $idcategoriagestion = Yii::$app->request->post('idcategoriagestion');
        $prioridad = Yii::$app->request->post('prioridad');
        $searchModel = new DetalleparametrizacionSearch();
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $isAjax = false;
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            if ($model->load(Yii::$app->request->post())) {
                $datos = Yii::$app->request->post('Detalleparametrizacion');
                
                if (!is_null($datos['id']) && $datos['id'] != '') {
                    $model = $this->findModel($datos['id']);
                    $model->configuracion = $datos['configuracion'];
                    $model->categoria = $datos['categoria'];
                }
                if ($idcategoriagestion != 0) {
                    $modelGestionCategoria = \app\models\Categoriagestion::findOne($idcategoriagestion);
                    $modelGestionCategoria->name = $nombre;
                    $modelGestionCategoria->prioridad = $prioridad;
                } else {
                    $modelGestionCategoria = new \app\models\Categoriagestion();
                    $modelGestionCategoria->name = $nombre;
                    $modelGestionCategoria->prioridad = $prioridad;
                    $modelGestionCategoria->id_parametrizacion = $id_parametrizacion;
                }
                $modelGestionCategoria->save();
                $model->id_categoriagestion = $modelGestionCategoria->id;
                $model->addNA = $datos['addNA'];
                $model->save();
                $queryParams['DetalleparametrizacionSearch']['id_categoriagestion'] = $modelGestionCategoria->id;
                $dataProvider = $searchModel->search($queryParams);
                return $this->renderAjax('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'isAjax' => $isAjax,
                            'id_parametrizacion' => $id_parametrizacion,
                            'modelCategoriaGestion' => $modelGestionCategoria,
                            'idcategoriagestion' => $model->id_categoriagestion,
                ]);
            } else {
                return $this->renderAjax('create', [
                            'model' => $model,
                            'idparame' => $id_parametrizacion,
                            'nombre' => $nombre,
                            'idcategoriagestion' => $idcategoriagestion,
                            'prioridad' => $prioridad,
                ]);
            }
        }
    }

    /**
     * Updates an existing Detalleparametrizacion model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            $modelcategoriaGestion = \app\models\Categoriagestion::findOne($model->id_categoriagestion);
            return $this->renderPartial('update', [
                        'model' => $model,
                        'idparame' => $modelcategoriaGestion->id_parametrizacion,
                        'nombre' => $modelcategoriaGestion->name,
                        'idcategoriagestion' => $modelcategoriaGestion->id,
                        'prioridad' => $modelcategoriaGestion->prioridad,
            ]);
        }
    }

    /**
     * Deletes an existing Detalleparametrizacion model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete() {
        $id = Yii::$app->request->post('id');
        $id_parametrizacion = Yii::$app->request->post('idparame');
        $idcategoriagestion = Yii::$app->request->post('gestion');
        $this->findModel($id)->delete();
        if ($idcategoriagestion != 0) {
            $modelGestionCategoria = \app\models\Categoriagestion::findOne($idcategoriagestion);
        } else {
            $modelGestionCategoria = new \app\models\Categoriagestion();
        }
        $searchModel = new DetalleparametrizacionSearch();
        $queryParams = array_merge(array(), Yii::$app->request->getQueryParams());
        $queryParams['DetalleparametrizacionSearch']['id_categoriagestion'] = $idcategoriagestion;
        $idCategoriaGestion = $idcategoriagestion;
        $dataProvider = $searchModel->search($queryParams);
        $isAjax = true;
        return $this->renderAjax
                        ('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'isAjax' => $isAjax,
                    'id_parametrizacion' => $id_parametrizacion,
                    'modelCategoriaGestion' => $modelGestionCategoria,
                    'idcategoriagestion' => ($idCategoriaGestion != 0) ? $idCategoriaGestion : 0,
        ]);
    }

    /**
     * Finds the Detalleparametrizacion model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Detalleparametrizacion the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Detalleparametrizacion::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Metodo que se encarga de visualizar la creacion de nuevos detalles parametrizacion si es mediante peticion ajax.
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function actionDetalleparametrizacion($id) {
        $model = new Detalleparametrizacion();
        $isAjax = false;
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            return $this->renderAjax('newdetallepara', [
                        'model' => $model,
                        'isAjax' => $isAjax,
                        'idParame' => $id,
            ]);
        }
    }
    
    public function actionGuardarcategoria(){
        if (Yii::$app->request->post('idcategoriagestion')==0) {
          $model = new \app\models\Categoriagestion();  
        }
        else{
            $model = \app\models\Categoriagestion::findOne(Yii::$app->request->post('idcategoriagestion'));
        }
        $model->name=Yii::$app->request->post('nombre');
        $model->id_parametrizacion=Yii::$app->request->post('idparame');
        $model->prioridad=Yii::$app->request->post('prioridad');
        $model->save();
        return $this->redirect(['parametrizacion-encuesta/parametrizacionencuesta',"id"=>$model->id_parametrizacion]);
    }

}
