<?php

namespace app\controllers;

use Yii;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * UsuariosController implements the CRUD actions for Usuarios model.
 */
class UsuariosController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    //'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($rule, $action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
                },
                        'rules' => [
                            [
                                'actions' => ['index', 'create', 'update', 'view',
                                    'delete','deleterel', 'export', 'limpiarfiltros','usuarios_evalua'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all Usuarios models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new UsuariosSearch();
                $isAjax = false;
                if (Yii::$app->getRequest()->isAjax) {
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    $isAjax = true;
                    if (Yii::$app->request->post()) {
                        Yii::$app->session['rptFilterUsuarios'] = Yii::$app->request->post();
                        Yii::$app->session['rptFilterUsuarios']['grupo'] = Yii::$app->request->get('grupo_id');
                        $dataProvider = $searchModel->searchAjax(Yii::$app->session['rptFilterUsuarios']);
                    } else {
                        $dataProvider = $searchModel->searchAjax($grupo_id);
                    }
                    return $this->renderAjax('index', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                                'grupo_id' => $grupo_id,
                                'isAjax' => $isAjax,
                    ]);
                }
                if (Yii::$app->request->post()) {
                    Yii::$app->session['rptFilterUsuarios'] = Yii::$app->request->post();
                    $dataProvider = $searchModel->search(Yii::$app->request->post());
                } else {
                    $paginacion = Yii::$app->request->get(); 
                    $dataProvider = $searchModel->search(Yii::$app->session['rptFilterUsuarios'],$paginacion);
                }
                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'isAjax' => $isAjax,
                ]);
            }

            /**
             * Displays a single Usuarios model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                $isAjax = false;

                if (Yii::$app->getRequest()->isAjax) {
                    $isAjax = true;
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    return $this->renderPartial('view', [
                                'model' => $this->findModel($id),
                                'isAjax' => $isAjax,
                                'grupo_id' => $grupo_id,
                    ]);
                }
                return $this->render('view', [
                            'model' => $this->findModel($id),
                            'isAjax' => $isAjax,
                ]);
            }

            /**
             * Creates a new Usuarios model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Usuarios();
                $isAjax = false;
		$query = 0;
                if (Yii::$app->getRequest()->isAjax) {
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    $isAjax = true;

                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        $rolUsuario = new \app\models\RelUsuariosRoles();
                        $rolUsuario->rel_usua_id = $model->usua_id;
                        $rolUsuario->rel_role_id = $model->rol;
                        $rolUsuario->save();
                        $grupousuario = new \app\models\RelGruposUsuarios();
                        $grupousuario->grupo_id = $grupo_id;
                        $grupousuario->usuario_id = $model->usua_id;
                        $grupousuario->save();
                        return $this->renderPartial('view', [
                                    'model' => $model,
                                    'isAjax' => $isAjax,
                                    'grupo_id' => $grupo_id,
                        ]);
                    } else {
                        return $this->renderPartial('create', [
                                    'model' => $model,
                                    'grupo_id' => $grupo_id,
                                    'isAjax' => $isAjax,
                        ]);
                    }
                }

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    $rolUsuario = new \app\models\RelUsuariosRoles();
                    $rolUsuario->rel_usua_id = $model->usua_id;
                    $rolUsuario->rel_role_id = $model->rol;
                    $rolUsuario->save();
                    if (isset($model->grupo)) {
                        $grupousuaruio = new \app\models\RelGruposUsuarios();
                        $grupousuaruio->grupo_id = $model->grupo;
                        $grupousuaruio->usuario_id = $model->usua_id;
                        $grupousuaruio->save();
                    }
                    return $this->redirect(['view', 'id' => $model->usua_id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                                'isAjax' => $isAjax,
				'query' => $query,
                    ]);
                }
            }

            /**
             * Updates an existing Usuarios model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);
                $varUsu = $model->usua_usuario;
                $query = 0;

                $roles = \app\models\RelUsuariosRoles::find()->where(
                                ['rel_usua_id' => $model->usua_id])->one();
                $model->rol = (isset($roles->rel_role_id)) ? $roles->rel_role_id : '';
                $grupos = \app\models\RelGruposUsuarios::find()->where(
                                ['usuario_id' => $model->usua_id])->one();
                $isAjax = false;
                if (Yii::$app->getRequest()->isAjax) {
                    $isAjax = true;
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        if (!isset($roles)) {
                            $roles = new \app\models\RelUsuariosRoles();
                            $roles->rel_role_id = $model->rol;
                            $roles->rel_usua_id = $model->usua_id;
                        } else {
                            $roles->rel_role_id = $model->rol;
                        }
                        $roles->save();
                        return $this->renderPartial('view', [
                                    'model' => $model,
                                    'isAjax' => $isAjax,
                                    'grupo_id' => $grupo_id,
                        ]);
                    } else {
                        return $this->renderPartial('update', [
                                    'model' => $model,
                                    'grupo_id' => $grupo_id,
                                    'isAjax' => $isAjax,
                        ]);
                    }
                }

                $txtRegistros = Yii::$app->request->post();

                if (count(Yii::$app->request->post()) > 0) {
                    $query3 = $txtRegistros['Usuarios']['usua_usuario'];

                    if ($varUsu != $query3) {
                        $query =  Yii::$app->db->createCommand("select count(*) from tbl_usuarios where usua_usuario = ':query3'")
                        ->bindValue(':query3', $query3)
                        ->queryScalar();

                        if ($query == 0) {
                            if ($model->load($txtRegistros) && $model->save()) {
                                if (!isset($roles)) {
                                    $roles = new \app\models\RelUsuariosRoles();
                                    $roles->rel_role_id = $model->rol;
                                    $roles->rel_usua_id = $model->usua_id;
                                } else {
                                    $roles->rel_role_id = $model->rol;
                                }
                                if (!isset($grupos)) {
                                    $grupos = new \app\models\RelGruposUsuarios();
                                    $grupos->grupo_id = $model->grupo;
                                    $grupos->usuario_id= $model->usua_id;
                                } else {
                                    $grupos->grupo_id = $model->grupo;
                                }
                                $grupos->save();
                                $roles->save();
                                return $this->redirect(['view', 'id' => $model->usua_id]);
                            }
                        }else{
                            $model->grupo = (isset($grupos->grupo_id))?$grupos->grupo_id:'';
                            return $this->render('update', [
                                        'model' => $model,
                                        'isAjax' => $isAjax,
                                        'query' => $query,
                            ]);
                        }

                    }else{
                        if ($model->load($txtRegistros) && $model->save()) {
                            if (!isset($roles)) {
                                $roles = new \app\models\RelUsuariosRoles();
                                $roles->rel_role_id = $model->rol;
                                $roles->rel_usua_id = $model->usua_id;
                            } else {
                                $roles->rel_role_id = $model->rol;
                            }
                            if (!isset($grupos)) {
                                $grupos = new \app\models\RelGruposUsuarios();
                                $grupos->grupo_id = $model->grupo;
                                $grupos->usuario_id= $model->usua_id;
                            } else {
                                $grupos->grupo_id = $model->grupo;
                            }
                            $grupos->save();
                            $roles->save();
                            return $this->redirect(['view', 'id' => $model->usua_id]);
                        } else {
                            $model->grupo = (isset($grupos->grupo_id))?$grupos->grupo_id:'';
                            return $this->render('update', [
                                        'model' => $model,
                                        'isAjax' => $isAjax,
                                        'query' => $query,
                            ]);
                        }
                    }
                }else {
                    $model->grupo = (isset($grupos->grupo_id))?$grupos->grupo_id:'';
                    return $this->render('update', [
                                'model' => $model,
                                'isAjax' => $isAjax,
                                'query' => $query,
                    ]);
                }
            }

            /**
             * Deletes an existing Usuarios model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                return $this->redirect(['index']);
            }
            
            public function actionDeleterel() {
                if (Yii::$app->getRequest()->isAjax) {
                    $searchModel = new UsuariosSearch();
                    $usuario = Yii::$app->request->get('usuario_id');
                    $grupo_id = Yii::$app->request->get('grupo_id');
                    $model = $this->findModel($usuario);
                    $modelRelacion = \app\models\RelGruposUsuarios::find()->where(['grupo_id' => $grupo_id, 'usuario_id' => $usuario])->one();
                    $modelRelacion->delete();
                    $isAjax = true;
                    $dataProvider = $searchModel->searchAjax($grupo_id);
                    
                    return $this->renderPartial('index', [
                                'searchModel' => $searchModel,
                                'dataProvider' => $dataProvider,
                                'usuario_id' => $usuario,
                                'isAjax' => $isAjax,
                                'grupo_id' => $grupo_id,
                                'model' => $model,
                    ]);
                    
                }
                
            }

            /**
             * Finds the Usuarios model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Usuarios the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Usuarios::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

            /**
             * Funcion que permite exportar la sabana de datos de los usuarios
             * teniendo en cuenta los filtros ingresados.
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionExport() {
                $searchModel = new UsuariosSearch();
                $dataProvider = $searchModel->searchExport(Yii::$app->request->post());
                if ($dataProvider !== false) {
                    $searchModel->generarReporteUsuarios($dataProvider);
                }
            }

            /**
             * Funcion que permite limpiar los filtro de busqueda y redirecciona al index
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionLimpiarfiltros() {
                Yii::$app->session->remove('rptFilterUsuarios');
                $this->redirect(['index']);
            }

      public function actionUsuarios_evalua(){
      $sessiones = Yii::$app->user->identity->id;
      $txtanulado = 0;
      $txtfechacreacion = date("Y-m-d");

      Yii::$app->db->createCommand("truncate table tbl_usuarios_evalua")->execute();

      $query = Yii::$app->get('dbjarvis3')->createCommand("Select f.nombre_completo as nombre, a.documento as documento, b.id_dp_cargos as idcargo,
      b.id_dp_posicion as idposicion,b.id_dp_funciones as idfuncion,c.posicion as posicion,d.funcion as funcion,
      e.usuario_red as usuariored,	g.email_corporativo as correo, a.documento_jefe as documento_jefe,     
      ifnull (if (a.id_dp_centros_costos != 0, dg3.nombre_completo, if (a.id_dp_centros_costos_adm != 0, ar.area_general, 'Sin información')), 'Sin información') AS directorArea,
      if (a.cod_pcrc != 0, cl1.cliente, if (a.id_dp_centros_costos != 0, cl2.cliente, if (a.id_dp_centros_costos_adm != 0, ar.area_general, 'Sin información'))) AS clienteArea 
		    FROM dp_distribucion_personal a  
    
        LEFT JOIN dp_cargos b
        ON b.id_dp_cargos = a.id_dp_cargos
        
        LEFT JOIN dp_posicion c
        ON c.id_dp_posicion = b.id_dp_posicion
        
        LEFT JOIN dp_funciones d
        ON d.id_dp_funciones = b.id_dp_funciones
        
        LEFT JOIN dp_usuarios_red e
        ON e.documento = a.documento
        
        LEFT JOIN dp_datos_generales f
        ON f.documento = a.documento
        
        LEFT JOIN dp_actualizacion_datos g
        ON g.documento = a.documento
        
        LEFT JOIN dp_pcrc AS pc1
		    ON pc1.cod_pcrc = a.cod_pcrc
		  
        LEFT JOIN dp_clientes AS cl1 
		    ON cl1.id_dp_clientes = pc1.id_dp_clientes
        
        LEFT JOIN dp_centros_costos AS cc1 
		    ON cc1.id_dp_centros_costos = a.id_dp_centros_costos
		  
        LEFT JOIN dp_clientes AS cl2 
		    ON cl2.id_dp_clientes = cc1.id_dp_clientes
        
	      LEFT JOIN dp_centros_costos_adm AS ad 
		    ON ad.id_dp_centros_costos_adm = a.id_dp_centros_costos_adm
        
        LEFT JOIN dp_centros_admin_area AS ar 
		    ON ar.id_dp_centros_admin_area = ad.id_dp_centros_admin_area
		  
        LEFT JOIN dp_centros_costos AS cc 
		    ON cc.id_dp_centros_costos = a.id_dp_centros_costos        
        
        LEFT JOIN dp_datos_generales AS dg3 
		    ON dg3.documento = cc.documento_director
        
        WHERE a.fecha_actual = (SELECT config.valor FROM jarvis_configuracion_general as config WHERE config.nombre = 'mes_activo_dp' )
        AND a.id_dp_estados NOT IN (305,317,327)
        AND e.fecha_creacion_usuario = ( SELECT MAX(aa.fecha_creacion_usuario) FROM dp_usuarios_red aa WHERE aa.documento = a.documento ) AND b.id_dp_cargos != 39322")->queryAll();

      foreach ($query as $key => $value) {
        $vardocumentojefe = $value['documento_jefe'];
        $query2 = Yii::$app->get('dbjarvis3')->createCommand("Select  distinct f.nombre_completo as nombrejefe, a.documento as documento, b.id_dp_cargos as idcargo, b.id_dp_posicion as idposicion,
                  b.id_dp_funciones as idfuncion,c.posicion as posicion,d.funcion as funcion
                  FROM dp_distribucion_personal a 
                  LEFT JOIN dp_cargos b
                  ON b.id_dp_cargos = a.id_dp_cargos

                  LEFT JOIN dp_posicion c
                  ON c.id_dp_posicion = b.id_dp_posicion

                  LEFT JOIN dp_funciones d
                  ON d.id_dp_funciones = b.id_dp_funciones

		              LEFT JOIN dp_datos_generales f
                  ON f.documento = a.documento

                  WHERE a.documento = ':vardocumentojefe'")
                  ->bindValue(':vardocumentojefe', $vardocumentojefe)
                  ->queryAll();

                  foreach ($query2 as $key => $value2) {
                    $varidcargojefe = $value2['idcargo'];
                    $varcargo = $value2['posicion']." ".$value2['funcion'];
		                $varnombrejefe = $value2['nombrejefe'];
                  }

          Yii::$app->db->createCommand()->insert('tbl_usuarios_evalua',[
                                                   'nombre_completo' => $value['nombre'],
                                                   'documento' => $value['documento'],
                                                   'id_dp_cargos' => $value['idcargo'],
                                                   'id_dp_posicion' => $value['idposicion'],
                                                   'id_dp_funciones' => $value['idfuncion'],
                                                   'posicion' => $value['posicion'],
                                                   'funcion' => $value['funcion'],
                                                   'usuario_red' => $value['usuariored'],
                                                   'email_corporativo' => $value['correo'],
                                                   'documento_jefe' => $value['documento_jefe'],
                                                   'nombre_jefe'  => $varnombrejefe,
                                                   'id_cargo_jefe' => $varidcargojefe,
                                                   'cargo_jefe' => $varcargo,
                                                   'directorarea' => $value['directorArea'],
                                                   'clientearea' => $value['clienteArea'],
                                                   'fechacrecion' => $txtfechacreacion,
                                                   'anulado' => $txtanulado,
                                                   'usua_id' => $sessiones,
                                               ])->execute();

      }

      return $this->redirect('index');  

  }

        }
        