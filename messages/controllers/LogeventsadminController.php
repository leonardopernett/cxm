<?php

namespace app\controllers;

use Yii;
use app\models\Logeventsadmin;
use app\models\LogeventsadminSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * LogeventsadminController implements the CRUD actions for Logeventsadmin model.
 */
class LogeventsadminController extends Controller {

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
     * Lists all Logeventsadmin models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new LogeventsadminSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        if (Yii::$app->request->post()) {
            Yii::$app->session['rptFilterLogeventsadmin'] = Yii::$app->request->post();
            $dataProvider = $searchModel->search(Yii::$app->request->post());
        } else {
            $dataProvider = $searchModel->search(Yii::$app->session['rptFilterLogeventsadmin']);
        }
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Logeventsadmin model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Logeventsadmin model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Logeventsadmin();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_log]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing Logeventsadmin model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_log]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing Logeventsadmin model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Logeventsadmin model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Logeventsadmin the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Logeventsadmin::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Funcion que permite exportar la sabana de datos del log de todos los cambios realizados en el administrador
     * teniendo en cuenta los filtros ingresados.
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function actionExport() {
        $searchModel = new logeventsadminSearch();
        $dataProvider = $searchModel->searchExport(Yii::$app->request->post());
        if ($dataProvider !== false) {
            $banderaReporte = $searchModel->generarReporteLogAdmin($dataProvider);
        }
    }

    /**
     * Funcion que permite limpiar los filtro de busqueda y redirecciona al index
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function actionLimpiarfiltros() {
        Yii::$app->session->remove('rptFilterLogeventsadmin');
        $this->redirect(['index']);
    }

}
