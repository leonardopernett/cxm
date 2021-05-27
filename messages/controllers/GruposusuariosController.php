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
                //'delete' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Lists all Gruposusuarios models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new GruposusuariosSearch();
        $isAjax = false;
        $model = new \app\models\GruposUsuarios();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
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
        //$dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        if (Yii::$app->getRequest()->isAjax) {
            $usuario = Yii::$app->request->get('usuario_id');
            $model = $this->findModel($id);
            $modelRelacion = \app\models\RelGruposUsuarios::find()->where(['grupo_id' => $model->grupos_id, 'usuario_id' => $usuario])->one();
            $modelRelacion->delete();
            $model->delete();
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
            $data = \app\models\GruposUsuarios::find()
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

}
