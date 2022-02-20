<?php

namespace app\controllers;

use Yii;
use app\models\ErroresSatu;
use app\models\ErroresSatuSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\base\Exception;

/**
 * ErroressatuController implements the CRUD actions for ErroresSatu model.
 */
class ErroressatuController extends Controller {

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
     * Lists all ErroresSatu models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ErroresSatuSearch();
        if (Yii::$app->request->post()) {
            Yii::$app->session['rptFilterErroressatu'] = Yii::$app->request->post();
            $dataProvider = $searchModel->search(Yii::$app->request->post());
        } else {
            $dataProvider = $searchModel->search(Yii::$app->session['rptFilterErroressatu']);
        }
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ErroresSatu model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        return $this->render('view', [
                    'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ErroresSatu model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new ErroresSatu();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->db->createCommand()->insert('tbl_logs', [
                'usua_id' => Yii::$app->user->identity->id,
                'usuario' => Yii::$app->user->identity->username,
                'fechahora' => date('Y-m-d h:i:s'),
                'ip' => Yii::$app->getRequest()->getUserIP(),
                'accion' => 'Create',
                'tabla' => 'tbl_errores_satu'
            ])->execute();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ErroresSatu model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            Yii::$app->db->createCommand()->insert('tbl_logs', [
                'usua_id' => Yii::$app->user->identity->id,
                'usuario' => Yii::$app->user->identity->username,
                'fechahora' => date('Y-m-d h:i:s'),
                'ip' => Yii::$app->getRequest()->getUserIP(),
                'accion' => 'Update',
                'tabla' => 'tbl_errores_satu'
            ])->execute();
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ErroresSatu model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        Yii::$app->db->createCommand()->insert('tbl_logs', [
            'usua_id' => Yii::$app->user->identity->id,
            'usuario' => Yii::$app->user->identity->username,
            'fechahora' => date('Y-m-d h:i:s'),
            'ip' => Yii::$app->getRequest()->getUserIP(),
            'accion' => 'Delete',
            'tabla' => 'tbl_errores_satu'
        ])->execute();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ErroresSatu model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ErroresSatu the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = ErroresSatu::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Funcion que permite exportar la sabana de datos del log de todos los errores en basesatisfaccion
     * teniendo en cuenta los filtros ingresados.
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function actionExport() {
        $searchModel = new ErroresSatuSearch();
        $dataProvider = $searchModel->searchExport(Yii::$app->request->post());
        if ($dataProvider !== false) {
            $searchModel->generarReporteErroressatu($dataProvider);
        }
    }

    public function actionEliminacionmasiva() {
        $datosEliminar = Yii::$app->request->post('selection');
        if (count($datosEliminar) > 0) {
            $model = new ErroresSatu();
            try {
                foreach ($datosEliminar as $key => $value) {
                    $model = $this->findModel($value);
                    $model->delete();
                }
            } catch (Exception $exc) {
                Yii::$app->session->setFlash('danger', Yii::t('app', 'Error eliminando elementos'));
            }
        } else {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'Seleccione un elemento para eliminar'));
        }

        $searchModel = new ErroresSatuSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->request->post()) {
            Yii::$app->session['rptFilterErroressatu'] = Yii::$app->request->post();
            $dataProvider = $searchModel->search(Yii::$app->request->post());
        } else {
            $dataProvider = $searchModel->search(Yii::$app->session['rptFilterErroressatu']);
        }
        Yii::$app->session->setFlash('success', Yii::t('app', 'Eliminación exitosa'));
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Funcion que permite limpiar los filtro de busqueda y redirecciona al index
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function actionLimpiarfiltros() {
        Yii::$app->session->remove('rptFilterErroressatu');
        $this->redirect(['index']);
    }

}
