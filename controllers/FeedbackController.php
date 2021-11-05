<?php

namespace app\controllers;

use Yii;
use app\models\Ejecucionfeedbacks;
use yii\helpers\Json;

class FeedbackController extends \yii\web\Controller {

    public function behaviors() {
        return [
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
                                'actions' => ['create', 'evaluadolist', 'lidereslist',
                                    'tipofeedback'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerdirectivo() || Yii::$app->user->identity->isVerexterno();
                        },
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Creates a new Slides model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new Ejecucionfeedbacks();
                $model->scenario = 'crear';
                $ajax = false;
                if (Yii::$app->getRequest()->isAjax) {
                    $modelBasesatisafaccion = \app\models\BaseSatisfaccion::findOne(Yii::$app->request->post("id"));
                    $model->scenario = 'crearAjax';
                    $ajax = true;
                    if ($model->load(Yii::$app->request->post())) {
                        $model->usua_id = Yii::$app->user->identity->id;
                        $modelEvaluado = \app\models\Evaluados::findOne(["dsusuario_red" => $modelBasesatisafaccion->agente]);
                        $model->evaluado_id = $modelEvaluado->id;
                        $model->created = date("Y-m-d H:i:s");
                        $model->usua_id_lider = $modelBasesatisafaccion->id_lider_equipo;
                        $model->basessatisfaccion_id = $modelBasesatisafaccion->id;
                        if ($model->save()) {
                            Yii::$app->session->setFlash('success', Yii::t('app', 'Feedback creado'));
                        }
                        return $this->redirect(['basesatisfaccion/formulariogestionsatisfaccion', 'id' => $modelBasesatisafaccion->id, 'bandera' => true]);
                    } else {
                        return $this->renderAjax('createAjax', [
                                    'model' => $model,
                                    'ajax' => $ajax,
                                    'id' => $modelBasesatisafaccion->id,
                        ]);
                    }
                } else {
                    if ($model->load(Yii::$app->request->post())) {

                        $vartipofeedback_id = $model->tipofeedback_id;
                        $varusua_id_lider = $model->usua_id_lider;
                        $varModel = explode(",", $model->evaluado_id);
                        $vardscomentario = $model->dscomentario;
                        $arrayUsu = array();

                        foreach ($varModel as $key => $value) {
                            array_push($arrayUsu, array("tipofeedback_id"=>$vartipofeedback_id,"usua_id_lider"=>$varusua_id_lider,"evaluado_id"=>$value,"dscomentario"=>$vardscomentario));
                        }

                        foreach ($arrayUsu as $key => $value) {
                            $mtipofeedback_id = $value["tipofeedback_id"];
                            $musua_id_lider = $value["usua_id_lider"];
                            $mevaluado_id = $value["evaluado_id"];
                            $mdscomentario = $value["dscomentario"];

                            Yii::$app->db->createCommand()->insert('tbl_ejecucionfeedbacks',[
                                'tipofeedback_id' => $mtipofeedback_id,
                                'usua_id_lider' => $musua_id_lider,
                                'evaluado_id' => $mevaluado_id,
                                'dscomentario' => $mdscomentario,
                                'usua_id' => Yii::$app->user->identity->id,
                                'created' => date("Y-m-d H:i:s"),
                            ])->execute();                            

                        }

                        return $this->redirect(['create']);

                    } else {
                        return $this->render('create', [
                                    'model' => $model,
                                    'ajax' => $ajax,
                        ]);
                    }
                }
            }

            public function actionTipofeedback() {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = [];
                if (isset($_POST['depdrop_parents'])) {
                    $parents = Yii::$app->request->post('depdrop_parents');
                    if ($parents != null && $parents[0] != "") {
                        $cat_id = $parents[0];
                        $out = \app\models\Tipofeedbacks::getTipofeedbacksListByID($cat_id);
                        echo Json::encode(['output' => $out, 'selected' => '']);
                        return;
                    }
                }
                echo Json::encode(['output' => '', 'selected' => '']);
            }

            public function actionLidereslist($search = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Equipos::getLideresList($search);
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo Json::encode($out);
            }

            public function actionEvaluadolist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Evaluados::getEvaluadosList($search);
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

        }
        