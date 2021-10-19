<?php

namespace app\Controllers;

use Yii;
use app\models\Arboles;
use app\models\ArbolesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \yii\helpers\Html;
use yii\base\Exception;

/**
 * ArbolesController implements the CRUD actions for Arboles model.
 */
class ArbolesController extends Controller {

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
                        'actions' => ['create', 'delete', 'equiposlist',
                            'index', 'roles', 'update', 'usuarioslist',
                            'deleterol', 'roleslist'],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            //return Yii::$app->user->identity->isAdminProcesos();
			    return Yii::$app->user->identity->isEdEqipoValorado();
                        },
                    ],
                ],
            ],
        ];
    }

    /**
     * Lists all Arboles models.
     * @return mixed
     */
    public function actionIndex() {
        $searchModel = new ArbolesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index',
                        [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Arboles model.
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
     * Creates a new Arboles model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate() {

        if (Yii::$app->getRequest()->isAjax) {
            $model = new Arboles();
            
            $model->nmumbral_verde = 1;
            $model->nmumbral_amarillo = 1;
            $model->nmumbral_positivo = 1;
            
            if ($model->snhoja) {
                $model->scenario = 'checkHoja';
            } else {
                $model->scenario = 'create';
            }

            if ($model->load(Yii::$app->request->post())) {
                if ($model->snhoja) {
                    $model->scenario = 'checkHoja';
                } else {
                    $model->scenario = 'create';
                    $model->formulario_id = null;
                    $model->equipos = null;
                }

                if (!$model->snactivar_problemas) {
                    $model->tableroproblema_id = null;
                }

                if (!$model->snactivar_tipo_llamada) {
                    $model->tiposllamada_id = null;
                }

                if ($model->save()) {
                    //Reordenar arboles-----------------------------------------
                    $model->reordenar();
                    //Asignacion de responsables y roles------------------------
                    if (!empty($model->responsables)) {
                        $arbolUsuario = \app\models\ArbolsUsuarios::deleteAll(
                                        ['arbol_id' => $model->id]);
                        $arrayResponsables = explode(',', $model->responsables);
                        if (count($arrayResponsables) > 0) {
                            $model->asignarGrupos($model->id, $arrayResponsables);
                            foreach ($arrayResponsables as $resp) {
                                $arbolUsuario = new \app\models\ArbolsUsuarios();
                                $arbolUsuario->arbol_id = $model->id;
                                $arbolUsuario->usua_id = $resp;
                                $arbolUsuario->save();
                            }
                        }
                    }
                    //Asignacion de equipos-------------------------------------
                    $arbolEquipo = \app\models\ArbolsEquipos::deleteAll(
                                    ['arbol_id' => $model->id]);
                    if (!empty($model->equipos)) {
                        $arrayEquipos = explode(',', $model->equipos);
                        if (count($arrayEquipos) > 0) {
                            foreach ($arrayEquipos as $equipo) {
                                $arbolEquipo = new \app\models\ArbolsEquipos();
                                $arbolEquipo->arbol_id = $model->id;
                                $arbolEquipo->equipo_id = $equipo;
                                $arbolEquipo->save();
                            }
                        }
                    }
                    return $this->redirect(['index']);
                } else {
                    return $this->renderPartial('update', ['model' => $model]);                    
                }
            } else {
                return $this->renderAjax('update', ['model' => $model]);
            }
        } else {
            return $this->redirect(['arboles/index']);
        }
    }

    /**
     * Updates an existing Arboles model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {

        if (Yii::$app->getRequest()->isAjax) {
            $model = $this->findModel($id);

            $resposables = $model->getIdsResponsables($id);
            $model->responsables = implode(',', $resposables);

            $equipos = $model->getIdsEquipos($id);
            $model->equipos = implode(',', $equipos);

            if ($model->snhoja) {
                $model->scenario = 'checkHoja';
            } else {
                $model->scenario = 'update';
            }

            if ($model->load(Yii::$app->request->post())) {
                if ($model->snhoja) {
                    $model->scenario = 'checkHoja';
                } else {
                    $model->scenario = 'update';
                    $model->formulario_id = null;
                    $model->equipos = null;
                }

                if (!$model->snactivar_problemas) {
                    $model->tableroproblema_id = null;
                }

                if (!$model->snactivar_tipo_llamada) {
                    $model->tiposllamada_id = null;
                }

                if ($model->save()) {
                    //Reordenar arboles-----------------------------------------
                    $model->reordenar();
                    //Asignacion de responsables y roles------------------------
                    if (!empty($model->responsables)) {
                        $arbolUsuario = \app\models\ArbolsUsuarios::deleteAll(
                                        ['arbol_id' => $model->id]);
                        $arrayResponsables = explode(',', $model->responsables);
                        if (count($arrayResponsables) > 0) {
                            $model->asignarGrupos($model->id, $arrayResponsables);
                            foreach ($arrayResponsables as $resp) {
                                $arbolUsuario = new \app\models\ArbolsUsuarios();
                                $arbolUsuario->arbol_id = $model->id;
                                $arbolUsuario->usua_id = $resp;
                                $arbolUsuario->save();
                            }
                        }
                    }

                    //Asignacion de equipos-------------------------------------
                    $arbolEquipo = \app\models\ArbolsEquipos::deleteAll(
                                    ['arbol_id' => $model->id]);
                    if (!empty($model->equipos)) {
                        $arrayEquipos = explode(',', $model->equipos);
                        if (count($arrayEquipos) > 0) {
                            foreach ($arrayEquipos as $equipo) {
                                $arbolEquipo = new \app\models\ArbolsEquipos();
                                $arbolEquipo->arbol_id = $model->id;
                                $arbolEquipo->equipo_id = $equipo;
                                $arbolEquipo->save();
                            }
                        }
                    }
                    return $this->redirect(['index']);
                } else {
                    return $this->renderPartial('update', ['model' => $model]);                    
                }
            } else {
                return $this->renderAjax('update', ['model' => $model]);
            }
        } else {
            return $this->redirect(['arboles/index']);
        }
    }

    /**
     * Modificacion de roles en arboles
     * (18-03-2016) Se realiza el ajuste en el cual la tabla de permisos ya no apunta a roles
     * si no que esta asociada a grupos de usuario (sebastian.orozco@ingeneo.com.co)
     * @return mixed
     */
    public function actionRoles() {
        if (Yii::$app->getRequest()->isAjax) {
            $model = new \app\models\PermisosGruposArbols();
            
            if (Yii::$app->request->post()) {                                
                
                $permRolId = Yii::$app->request->post('id');
                if (!empty($permRolId) && is_numeric($permRolId)) {
                    $modelPermisos = \app\models\PermisosGruposArbols::findOne($permRolId);

                    if (isset($_POST['sncrear_formulario'])) {
                        $crearForm = Yii::$app->request->post('sncrear_formulario');
                        $modelPermisos->sncrear_formulario = Html::encode($crearForm);
                        $modelPermisos->save();
                    }

                    if (isset($_POST['snver_grafica'])) {
                        $crearForm = Yii::$app->request->post('snver_grafica');
                        $modelPermisos->snver_grafica = Html::encode($crearForm);
                        $modelPermisos->save();
                    }
                }

                
                if ($model->load(Yii::$app->request->post())) {
                    
                    if (!empty($model->grupousuario_id)) {                        
                        
                        $grupoExist = \yii\helpers\ArrayHelper::map(
                                \app\models\PermisosGruposArbols::find()
                                ->where('arbol_id =' . $model->arbol_id 
                                        . ' AND grupousuario_id IN (' . $model->grupousuario_id 
                                        . ')')->asArray()->all(),
                                        'id', 'grupousuario_id'); 
                        
                        $grupos = explode(',', $model->grupousuario_id);                                                
                        if (count($grupos) > 0) {
                            foreach ($grupos as $grupo) {
                                if (!in_array($grupo, $grupoExist)) {
                                    $modelRoles = new \app\models\PermisosGruposArbols();
                                    $modelRoles->arbol_id = $model->arbol_id;
                                    $modelRoles->grupousuario_id = $grupo;
                                    $modelRoles->sncrear_formulario = 1;
                                    $modelRoles->snver_grafica = 1;
                                    $modelRoles->save();
                                }
                            }
                        }
                    }                    

                    $resetModel = new \app\models\PermisosGruposArbols();
                    $resetModel->arbol_id = $model->arbol_id;
                    $queryParams['PermisosGruposArbols']['arbol_id'] = $model->arbol_id;
                    $dataProvider = $resetModel->search($queryParams);
                    return $this->renderPartial('roles',
                                    [
                                'searchModel' => $resetModel,
                                'dataProvider' => $dataProvider,]);
                }
            } else {
                $arbolId = Yii::$app->request->get('arbol_id');
                $queryParams = array_merge(array(),
                        Yii::$app->request->getQueryParams());
                $queryParams['PermisosGruposArbols']['arbol_id'] = $arbolId;
                $dataProvider = $model->search($queryParams);
                return $this->renderAjax('roles',
                                [
                            'searchModel' => $model,
                            'dataProvider' => $dataProvider,]);
            }
        } else {
            return $this->redirect(['arboles/index']);
        }
    }

    /**
     * Retorna el listado de los roles
     * 
     * @return json
     */
    public function actionRoleslist($search) {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->redirect(['arboles/index']);
        }
        $out = ['more' => false];
        if (!is_null($search)) {
            $arbolId = Yii::$app->request->get('arbol_id');
            
            $rolExist = \yii\helpers\ArrayHelper::map(
                                \app\models\PermisosGruposArbols::find()
                                ->where('arbol_id =' . $arbolId)
                                ->asArray()
                                ->all(),
                                        'id', 'grupousuario_id');
            $strRol = implode(',', $rolExist);         
            
            $notin = ($strRol != '') ? 'AND grupos_id NOT IN ('.$strRol.')' : '';
            $data = \app\models\Gruposusuarios::find()
                    ->select(['id' => 'grupos_id', 'text' => 'UPPER(grupo_descripcion)'])                    
                    ->where(' grupo_descripcion LIKE "%' . $search 
                            . '%" '. $notin)         
                    ->orderBy('grupo_descripcion')
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app',
                        'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }

    /**
     * Permite eliminar los roles de un arbol
     * 
     * @param int $id Id PermisosGruposArbols
     * 
     * @return void
     */
    public function actionDeleterol($id) {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->redirect(['arboles/index']);
        }

        $model = \app\models\PermisosGruposArbols::findOne($id);
        $model->delete();
        Yii::$app->request->url = \yii\helpers\Url::to(['roles', 'arbol_id' => $model->arbol_id]);
        $this->run('roles');
        Yii::$app->end();
    }

    /**
     * Deletes an existing Arboles model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $model = $this->findModel($id);

        $hijos = Arboles::find()->where(['arbol_id' => $id])->asArray()->all();
        if (count($hijos) > 0 || $model->snhoja || !empty($model->formulario_id)) {
            Yii::$app->getSession()->setFlash('danger',
                    Yii::t('app',
                            'El árbol (' . $model->name . ') esta '
                            . 'relacionado, puede contener hijos o está  '
                            . 'asociado a un formulario '));
        } else {
            try {

                \app\models\ArbolsEquipos::deleteAll(
                        ['arbol_id' => $model->id]);
                \app\models\ArbolsUsuarios::deleteAll(
                        ['arbol_id' => $model->id]);
                \app\models\PermisosGruposArbols::deleteAll(
                        ['arbol_id' => $model->id]);
                $model->delete();
                Yii::$app->getSession()->setFlash('success',
                        Yii::t('app',
                                'El árbol (' . $model->name . ') se eliminó con '
                                . 'éxito '));
            } catch (\yii\db\IntegrityException $exc) {
                \Yii::error($exc->getMessage(), 'db');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app',
                                'El árbol (' . $model->name . ') esta '
                                . 'relacionado con una o mas personas '
                                . 'responsables, por lo tanto no se pude '
                                . 'eliminar'));
            } catch (Exception $exc) {
                \Yii::error($exc->getMessage(), 'exception');
                Yii::$app->getSession()->setFlash('danger',
                        Yii::t('app', 'error exception'));
            }
        }

        return $this->redirect(['index']);
    }

    /**
     * Finds the Arboles model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Arboles the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = Arboles::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * Retorna en formato JSON el listado de los equipos
     * 
     * @param string $search Nombre del equipo
     * @param string $id     Ids de los equipos
     * 
     * @return string Json response
     */
    public function actionEquiposlist($search = null, $id = null) {
        $out = ['more' => false];
        if (!is_null($search)) {
            $data = \app\models\Equipos::find()
                    ->select(['id', 'text' => 'UPPER(name)'])
                    ->where('name LIKE "%' . $search . '%"')
                    ->orderBy('name')
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $ids = explode(',', $id);
            if (count($ids) > 0) {
                $data = \app\models\Equipos::find()
                        ->select(['id', 'text' => 'UPPER(name)'])
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
     * Listado de evaluados
     * 
     * @param string $search Nombre del usuario
     * @param string $id     Ids de los usuarios
     * 
     * @return string Json response
     */
    public function actionUsuarioslist($search = null, $id = null) {
        $out = ['more' => false];
        if (!is_null($search)) {
            $data = \app\models\Usuarios::find()
                    ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                    ->where('usua_nombre LIKE "%' . $search . '%"')
                    ->orderBy('usua_nombre')
                    ->asArray()
                    ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $ids = explode(',', $id);
            if (count($ids) > 0) {
                $data = \app\models\Usuarios::find()
                        ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                        ->where('usua_id IN (' . $id . ')')
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

}
