<?php

namespace app\controllers;

use Yii;
use app\models\Gruposusuarios;
use app\models\GruposusuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * GruposusuariosController implements the CRUD actions for Gruposusuarios model.
 */
class GruposusuariosController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
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
     * Lists all Gruposusuarios models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new GruposusuariosSearch();
        $isAjax = false;
        $model = new \app\models\GruposUsuarios();
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            $usuario = Yii::$app->request->get('usuario_id');
            $dataProvider = $searchModel->searchAjax($usuario);
            if (Yii::$app->request->post()) {
                $datos = Yii::$app->request->post('GruposUsuarios');
                $grupos = explode(',', $datos['grupos_id']);
                foreach ($grupos as $value) {
                    $relGrupousuario = new \app\models\RelGruposUsuarios();
                    $relGrupousuario->usuario_id = $usuario;
                    $relGrupousuario->grupo_id = $value;
                    $relGrupousuario->save();
                }
                $model = new \app\models\GruposUsuarios();
                return $this->renderPartial('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'usuario_id' => $usuario,
                            'isAjax' => $isAjax,
                            'model' => $model,
                ]);
            } else {
                return $this->renderAjax('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'usuario_id' => $usuario,
                            'isAjax' => $isAjax,
                            'model' => $model,
                ]);
            }
        }
		$paginacion = 0;
        if (Yii::$app->request->post()) {
            Yii::$app->session['rptFilterGruposUsuarios'] = Yii::$app->request->post();
        } else {
            $paginacion = Yii::$app->request->get();
        }
        $dataProvider = $searchModel->search(Yii::$app->session['rptFilterGruposUsuarios'], $paginacion);
        return $this->render('index', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
                    'isAjax' => $isAjax,
        ]);
    }

    /**
     * Displays a single Gruposusuarios model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id) {
        $isAjax = false;
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            $usuario_id = Yii::$app->request->get('usuario_id');

            return $this->renderPartial('view', [
                        'model' => $this->findModel($id),
                        'isAjax' => $isAjax,
                        'usuario_id' => $usuario_id,
            ]);
        }
        return $this->render('view', [
                    'model' => $this->findModel($id),
                    'isAjax' => $isAjax,
        ]);
    }

    /**
     * Creates a new Gruposusuarios model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {
        $model = new Gruposusuarios();
        $isAjax = false;
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            $usuario_id = Yii::$app->request->get('usuario_id');
            if ($model->load(Yii::$app->request->post())) {
                $relGrupousuario = new \app\models\RelGruposUsuarios();
                $relGrupousuario->usuario_id = $usuario_id;
                $model->save();
                $relGrupousuario->grupo_id = $model->grupos_id;
                $relGrupousuario->save();
                return $this->renderPartial('view', [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'usuario_id' => $usuario_id,
                ]);
            } else {
                return $this->renderPartial('create', [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'usuario_id' => $usuario_id,
                ]);
            }
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->grupos_id, 'isAjax' => $isAjax,
            ]);
        } else {
            return $this->render('create', [
                        'model' => $model,
                        'isAjax' => $isAjax,
            ]);
        }
    }

    /**
     * Updates an existing Gruposusuarios model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);
        $isAjax = false;
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            $usuario_id = Yii::$app->request->get('usuario_id');
            if ($model->load(Yii::$app->request->post()) && $model->save()) {
                return $this->renderPartial('view', [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'usuario_id' => $usuario_id,
                ]);
            } else {
                return $this->renderPartial('update', [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'usuario_id' => $usuario_id,
                ]);
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->grupos_id]);
        } else {
            return $this->render('update', [
                        'model' => $model,
                        'isAjax' => $isAjax,
            ]);
        }
    }

    /**
     * Deletes an existing Gruposusuarios model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $searchModel = new GruposusuariosSearch();
        $isAjax = false;
        if (Yii::$app->getRequest()->isAjax) {
            $usuario = Yii::$app->request->get('usuario_id');
            $model = $this->findModel($id);
            $modelRelacion = \app\models\RelGruposUsuarios::find()->where(['grupo_id' => $model->grupos_id, 'usuario_id' => $usuario])->one();
            $modelRelacion->delete();
            $isAjax = true;
            $dataProvider = $searchModel->searchAjax($usuario);
            $model = new \app\models\GruposUsuarios();
            return $this->renderAjax('index', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'usuario_id' => $usuario,
                        'isAjax' => $isAjax,
                        'model' => $model,
            ]);
        }
        $this->findModel($id)->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the Gruposusuarios model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Gruposusuarios the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Gruposusuarios::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * 
     * @param type $search
     * @param type $id
     */
    public function actionEquiposlist($search = null) {

        $out = ['more' => false];
        $usuario_id = Yii::$app->request->get('id');
        if (!is_null($search)) {
            $data = Gruposusuarios::find()
                    ->select(['id' => 'tbl_grupos_usuarios.grupos_id', 'text' => 'UPPER(tbl_grupos_usuarios.nombre_grupo)'])
                    ->where('nombre_grupo LIKE "%' . $search . '%"')
                    ->andWhere('tbl_grupos_usuarios.grupos_id NOT IN ( '
                            . 'SELECT rel_grupos_usuarios.grupo_id AS id '
                            . 'FROM rel_grupos_usuarios '
                            . 'WHERE usuario_id = ' . $usuario_id . ') ')
                    ->groupBy('tbl_grupos_usuarios.grupos_id')
                    ->orderBy('nombre_grupo')
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }
	
	 /**
     * Funcion que permite exportar la sabana de datos de los usuarios
     * teniendo en cuenta los filtros ingresados.
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function actionExport() {
        $searchModel = new GruposusuariosSearch();
        $dataProvider = $searchModel->searchExport(Yii::$app->request->post());
        if ($dataProvider !== false) {
            $searchModel->generarReporteUsuariosgrupos($dataProvider);
        }
    }
    
    /**
             * Funcion que permite limpiar los filtro de busqueda y redirecciona al index
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionLimpiarfiltros() {
                Yii::$app->session->remove('rptFilterGruposUsuarios');
                $this->redirect(['index']);
            }
            
            public function actionPermisosmasivos() {
                $arrDetalleForm = [];
                if (Yii::$app->request->post()) {    
                    $arrDetalleForm['per_realizar_valoracion'] = 1;
                    $grupos = Yii::$app->request->post('selection');
                    for ($index = 0; $index < count($grupos); $index++) {
                        $model = Gruposusuarios::findOne(['grupos_id'=>$grupos[$index]]);
                        if ($model->per_realizar_valoracion == 0) {
                            $model->per_realizar_valoracion = 1;
                        }else{
                            $model->per_realizar_valoracion = 0;
                        }
                        $model->save();
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', 'Proceso realizado con éxito'));
                }
                return $this->redirect('index');
            }
}
