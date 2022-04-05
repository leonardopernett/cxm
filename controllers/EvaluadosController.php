<?php

namespace app\Controllers;

use Yii;
use app\models\Evaluados;
use app\models\EvaluadosSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EvaluadosController implements the CRUD actions for Evaluados model.
 */
class EvaluadosController extends Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['post'],
                ],
            ],
            'access' => [
                'class' => \yii\filters\AccessControl::className(),
                'denyCallback' => function ($action) {
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
                },
                        'rules' => [
                            [
                                'actions' => ['index', 'create', 'update', 'view',
                                    'delete','export','limpiarfiltros'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isEdEqipoValorado() || Yii::$app->user->identity->isVerusuatlmast();
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
             * Lists all Evaluados models.
             * @return mixed
             */
            public function actionIndex() {
                $searchModel = new EvaluadosSearch();
                if (Yii::$app->request->post()) {
                    Yii::$app->session['rptFilterEvaluados'] = Yii::$app->request->post();
                    $dataProvider = $searchModel->search(Yii::$app->request->post());
                } else {
                    $dataProvider = $searchModel->search(Yii::$app->session['rptFilterEvaluados']);
                }
                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Displays a single Evaluados model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new Evaluados model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Evaluados();
                $query = Yii::$app->request->post();
                $query2 = null;
                $query3 = null;        

                if (count(Yii::$app->request->post()) > 0) {
                    $query3 = $query["Evaluados"]["dsusuario_red"];

                    $query2 = Yii::$app->db->createCommand("select count(*) from tbl_evaluados where dsusuario_red = ':query3'")
                    ->bindValue(':query3', $query3)
                    ->queryScalar();

                    if ($query2 == 0) {
                        if ($model->load($query)) {
                            if ($model->save()) {
                                Yii::$app->db->createCommand()->insert('tbl_logs', [
                                    'usua_id' => Yii::$app->user->identity->id,
                                    'usuario' => Yii::$app->user->identity->username,
                                    'fechahora' => date('Y-m-d h:i:s'),
                                    'ip' => Yii::$app->getRequest()->getUserIP(),
                                    'accion' => 'Create',
                                    'tabla' => 'tbl_evaluados'
                                  ])->execute(); 
                                Yii::$app->getsession()->setFlash('message','Registro agregado correctamente');
                                return $this->redirect(['view', 'id' => $model->id]);
                            }
                            else
                            {
                                Yii::$app->getsession()->setFlash('message','Fallo al agregar registro.');
                            }
                        }              
                    }
                }


                return $this->render('create', [
                                'model' => $model,
                                'query2' => $query2,
                    ]);
            }

            /**
             * Updates an existing Evaluados model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);
                $varUsu = $model->dsusuario_red;
                $query = 0;
                
                $txtUsu = Yii::$app->request->post();

                if (count(Yii::$app->request->post()) > 0) {
                    $query3 = $txtUsu["Evaluados"]["dsusuario_red"];

                    if ($varUsu != $query3) {
                        $query = Yii::$app->db->createCommand("select count(*) from tbl_evaluados where dsusuario_red = ':query3'")
                        ->bindValue(':query3', $query3)
                        ->queryScalar();

                        if ($query == 0) {
                            if ($model->load($txtUsu) && $model->save()) {
                                Yii::$app->db->createCommand()->insert('tbl_logs', [
                                    'usua_id' => Yii::$app->user->identity->id,
                                    'usuario' => Yii::$app->user->identity->username,
                                    'fechahora' => date('Y-m-d h:i:s'),
                                    'ip' => Yii::$app->getRequest()->getUserIP(),
                                    'accion' => 'Update',
                                    'tabla' => 'tbl_evaluados'
                                  ])->execute(); 
                                return $this->redirect(['view', 'id' => $model->id]);
                            }
                        } 
                    }else{
                        if ($model->load($txtUsu) && $model->save()) {
                                return $this->redirect(['view', 'id' => $model->id]);
                            }
                    }

   
                    return $this->render('update', [
                                'model' => $model,
                                'query' => $query,
                    ]);                
                }else{
                    return $this->render('update', [
                                'model' => $model,
                                'query' => $query,
                    ]);   
                }   
            }

            /**
             * Deletes an existing Evaluados model.
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
                    'tabla' => 'tbl_evaluados'
                  ])->execute(); 

                return $this->redirect(['index']);
            }

            /**
             * Finds the Evaluados model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return Evaluados the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = Evaluados::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

            /**
             * Funcion que permite exportar la sabana de datos de los evaluados y su respectivo equipo
             * teniendo en cuenta los filtros ingresados.
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionExport() {
                $searchModel = new EvaluadosSearch();
                $dataProvider = $searchModel->searchExport(Yii::$app->request->post());
                if ($dataProvider !== false) {
                    $searchModel->generarReporteEvaluados($dataProvider);
                }
            }

            /**
             * Funcion que permite limpiar los filtro de busqueda y redirecciona al index
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionLimpiarfiltros() {
                Yii::$app->session->remove('rptFilterEvaluados');
                $this->redirect(['index']);
            }
        }
        