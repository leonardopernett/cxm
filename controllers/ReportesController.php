<?php

namespace app\Controllers;

use Yii;
use yii\helpers\Json;
use yii\data\ArrayDataProvider;

class ReportesController extends \yii\web\Controller {

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
                                'actions' => ['feedbackexpressamigo', 'historicoformulariosamigo', 'historicoencuestasamigo', 'alertasamigo'],
                                'allow' => true,
                            ],
                            [
                                'actions' => ['calculatefeedback', 'equiposlist',
                                    'evaluadolist', 'extractarformulario',
                                    'feedbackexpress', 'historicoformularios',
                                    'lidereslist', 'preguntas', 'promcalificaciones',
                                    'tableroexperiencias', 'updatefeedback',
                                    'usuariolist', 'valorados', 'variables',
                                    'updatefeedbackcm', 'declinaciones', 'satisfaccion',
                                    'controlsatisfaccion', 'historicosatisfaccion', 'dimensionlist', 'evaluadolistmultiple',
                                    'getarboles', 'rollistmultiple', 'reportesegundocalificador'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],
                            [
                                'actions' => ['historicoformularios', 'usuariolist',
                                    'lidereslist', 'evaluadolist', 'equiposlist', 'dimensionlist', 'evaluadolistmultiple'
                                    , 'getarboles', 'rollistmultiple'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isModificarMonitoreo() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],
                            [
                                'actions' => ['updatefeedbackcm'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],
                        ],
                    ],
                ];
            }

            public function actionExtractarformulario() {
                $model = new \app\models\Ejecucionformularios();
                $model->scenario = 'extractar';
                $export = false;
                $dataProviderFinal = "";
                $titulos = array();

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    //$data = $model->extrctarFormulario();
                    //NUEVA FUNCION QUE CONSULTA Y TRASPONE
                    $data = $model->extractConsTrans();

                    if ($data) {
                        $export = true;
                    } else {
                        $export = false;
                    }

                    $_POST['exportextractar'] = 1;
                }

                return $this->render('extractar-formulario', [
                            'model' => $model,
                            'dataProviderFinal' => $dataProviderFinal,
                            'export' => $export,
                            'titulos' => $titulos,]);
            }

            /**
             * Generacion de reporte Feedback Express
             * 
             * @return string
             */
            public function actionFeedbackexpress() {
                $model = new \app\models\Ejecucionfeedbacks();
                $model->scenario = 'reporte';
                $dataProvider = $resumenFeedback = $detalleLiderFeedback = [];
                $showGrid = false;

                $export = false;
                if (Yii::$app->request->post('exportfeedback') || Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    if (Yii::$app->request->post('exportfeedback')) {
                        $export = true;
                    }
                    $model->load(Yii::$app->session['rptFilterFeedback']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportFeedbacks();
                    if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                        $resumenFeedback = $model->getResumenFeedback();
                        $detalleLiderFeedback = $model->getDetalleLiderFeedback();
                    }
                    $showGrid = true;
                }

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportFeedbacks();
                    $resumenFeedback = $model->getResumenFeedback();
                    $detalleLiderFeedback = $model->getDetalleLiderFeedback();
                    $showGrid = true;
                    Yii::$app->session['rptFilterFeedback'] = Yii::$app->request->post();
                }

                return $this->render('feedback-express', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'resumenFeedback' => $resumenFeedback,
                            'detalleLiderFeedback' => $detalleLiderFeedback,
                            'showGrid' => $showGrid,
                            'export'=>$export]);
            }

            /**
             * Actualizar el feedack
             * 
             * @param int $id Id feedback
             * 
             * @return string
             */
            public function actionUpdatefeedback($id) {
                $model = \app\models\Ejecucionfeedbacks::findOne($id);
                $activador = \app\models\Ejecucionfeedbacks::getSelectTipoFeedback($id);
                if (Yii::$app->getRequest()->isAjax) {
                    $model->feaccion_correctiva = date("Y-m-d H:i:s");
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        $page = Yii::$app->request->get('page');
                        $numPage = (empty($page)) ? 1 : $page;
                        return $this->redirect(['reportes/feedbackexpress', 'page' => $numPage]);
                    }
                }
                return $this->renderAjax('updateFeedback', ['model' => $model, 'activador' => $activador]);
            }

            /**
             * 
             * @param type $formulario_id
             * @return type
             */
            public function actionCalculatefeedback($formulario_id) {

                $usua_id = Yii::$app->user->identity->id;
                //Eliminar los calculos anteriores -------------------------------------
                \app\models\Tmpreportes::deleteAll(['usua_id' => $usua_id]);
                //Generar el reporte de calificaciones----------------------------------
                try {
                    $sql = "CALL sp_reporte_calificaciones($usua_id, $formulario_id);";
                    $command = \Yii::$app->db->createCommand($sql);
                    $command->execute();
                } catch (Exception $exc) {
                    \Yii::error($exc->getMessage(), 'exception');
                    Yii::$app->getSession()->setFlash('danger', Yii::t('app', 'Ocurrió un error, inténtelo mas tarde o '
                                    . 'comuníquese con el administrador'));
                }
                //Conulta de calificaciones --------------------------------------------        
                $data = \app\models\Tmpreportes::find()->where(['usua_id' => $usua_id])
                                ->orderBy('id ASC')->asArray()->all();

                return $this->renderAjax('calculateFeedback', ['data' => $data]);
            }

            /**
             * 
             * @return type
             */
            public function actionHistoricoformularios() {

                $model = new \app\models\Ejecucionformularios();
                /* Llama al SP sp_llenar_tmpreportes. */
                $rol = Yii::$app->user->identity->rolId;
                if ($rol == 1) {
                    $user_admin = 1;
                } else {
                    $user_admin = 0;
                }
                $id_evaluado = 0;
                $dataReport = new \stdClass();
                $dataReport->fingreso_formulario_ini = date("Y-m-d", strtotime('-2 months'));
                $dataReport->fingreso_formulario_fin = date("Y-m-d");
                $model->llenarTtmpReportes(
                        $user_admin, Yii::$app->user->identity->id, $id_evaluado, $dataReport->fingreso_formulario_ini, $dataReport->fingreso_formulario_fin);
                $model->scenario = 'historico';
                $dataProvider = [];
                $showGrid = false;
                $filtro = Yii::$app->session['rptFilterFormularios'];
                if (isset($filtro) && !Yii::$app->request->post()) {
                    $showGrid = true;
                    $model->load(Yii::$app->session['rptFilterFormularios']);
                    $dataProvider = $model->getReportFormularios(true);
                }
                //$dataProvider = $model->getReportFormularios(true);
                /* if (isset(Yii::$app->session['rptFilterFormularios']) || !is_null(Yii::$app->session['rptFilterFormularios'])) {
                  $model->load(Yii::$app->session['rptFilterFormularios']);
                  } */
                $export = false;
                if (Yii::$app->request->post('exportformularios')) {
                    $model->load(Yii::$app->session['rptFilterFormularios']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportFormularios(false);
                    $showGrid = true;
                    $export = true;                   
                }
                if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptFilterFormularios']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportFormularios(true);
                    $showGrid = true;                    
                }
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportFormularios(true);
                    $showGrid = true;
                    Yii::$app->session['rptFilterFormularios'] = Yii::$app->request->post();

                }
                // echo "<pre>";                
                // print_r($dataProvider); die;
                return $this->render('historico-formularios', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'export' => $export,
                            'showGrid' => $showGrid]);
            }

            public function actionPromcalificaciones() {
                $model = new \app\models\Ejecucionbloquedetalles();
                $dataProvider = [];
                $showGrid = false;

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0].' 00:00:00';
                    $model->endDate = $dates[1].' 23:59:59';
                    $showGrid = true;
                    $dataProvider = $model->reporteCalificaciones($model->arbol_id, $model->startDate, $model->endDate, true, $model->dimension);
                    Yii::$app->session['rptFilterCalificaciones'] = Yii::$app->request->post();
                }

                if (Yii::$app->request->post('exportcalificaciones')) {
                    $model->load(Yii::$app->session['rptFilterCalificaciones']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0].' 00:00:00';
                    $model->endDate = $dates[1].' 23:59:59';
                    $dataProvider = $model->reporteCalificaciones($model->arbol_id, $model->startDate, $model->endDate, false, $model->dimension);
                    $showGrid = true;
                }

                if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptFilterCalificaciones']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0].' 00:00:00';
                    $model->endDate = $dates[1].' 23:59:59';
                    $dataProvider = $model->reporteCalificaciones($model->arbol_id, $model->startDate, $model->endDate, true, $model->dimension);
                    $showGrid = true;
                }
                return $this->render('prom-calificaciones', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid]);
            }

            public function actionTableroexperiencias() {
                $model = new \app\models\Ejecucionformularios();
                $dataProvider = [];
                $showGrid = false;
                $model->scenario = 'experiencias';

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $showGrid = true;
                    $dataProvider = $model->reporteExperiencias($model->arbol_id, $model->startDate, $model->endDate, $model->tipoReporte, true);
                    Yii::$app->session['rptFilterExperiencias'] = Yii::$app->request->post();
                }

                if (Yii::$app->request->post('exporttablero')) {
                    $model->load(Yii::$app->session['rptFilterExperiencias']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->reporteExperiencias($model->arbol_id, $model->startDate, $model->endDate, $model->tipoReporte, false);
                    $showGrid = true;
                }

                if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptFilterExperiencias']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->reporteExperiencias($model->arbol_id, $model->startDate, $model->endDate, $model->tipoReporte, true);
                    $showGrid = true;
                }
                return $this->render('tablero-experiencias', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid]);
            }

            public function actionValorados() {
                $model = new \app\models\Tmpreportes();
                $dataProvider = [];
                $showGrid = false;
                if (Yii::$app->request->post('exportvalorados') || Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptFilterValorados']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportValorados();
                    $showGrid = true;
                }
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportValorados();
                    $showGrid = true;
                    Yii::$app->session['rptFilterValorados'] = Yii::$app->request->post();
                }

                return $this->render('valorados', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid]);
            }

            public function actionVariables() {

                $model = new \app\models\Tmpreportes();
                $dataProvider = [];
                $showGrid = false;
                $model->scenario = 'variables';
                $v_usuario = Yii::$app->user->identity->id;

                if (Yii::$app->request->post('exportvariables') || Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptFilterValorados']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0] . " 00:00:00";
                    $model->endDate = $dates[1] . " 23:59:59";
                    //Cambiar 1 por id de usuario logeado
                    $store = $model->reporteTrasponer($v_usuario, $model->startDate, $model->endDate, $model->arbol_id, $model->pregunta_id, $model->dimension_id);
                    if ($store) {
                        $dataProvider = $model->getReporteTrasponer($v_usuario);
                    }

                    $showGrid = true;
                }
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {

                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    //Cambiar 1 por id de usuario logeado
                    $store = $model->reporteTrasponer($v_usuario, $model->startDate, $model->endDate, $model->arbol_id, $model->pregunta_id, $model->dimension_id);
                    if ($store) {
                        $dataProvider = $model->getReporteTrasponer($v_usuario);
                    }
                    Yii::$app->session['rptFilterValorados'] = Yii::$app->request->post();
                    $showGrid = true;
                }

                return $this->render('variables', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid]);
            }

            /**
             * Obtiene el listado de evaluados
             * @param type $search
             * @param type $id
             */
            public function actionEvaluadolist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                            ->where('name LIKE "%' . $search . '%"')
                            ->orderBy('name')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                            ->where('tbl_evaluados.id = ' . $id)
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de equipos
             * @param type $search
             * @param type $id
             */
            public function actionEquiposlist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Equipos::find()
                            ->select(['id' => 'tbl_equipos.id', 'text' => 'UPPER(name)'])
                            ->where('name LIKE "%' . $search . '%"')
                            ->orderBy('name')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Equipos::find()
                            ->select(['id' => 'tbl_equipos.id', 'text' => 'UPPER(name)'])
                            ->where('tbl_equipos.id = ' . $id)
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de evaluadores-Usuarios
             * @param type $search
             * @param type $id
             */
            public function actionUsuariolist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => true];
                if (!is_null($search)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_nombre LIKE "%' . $search . '%"')
                            ->orderBy('usua_nombre')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_id IN (' . $id . ')')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de lideres
             * @param type $search
             * @param type $id
             */
            public function actionLidereslist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Equipos::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->join('JOIN', 'tbl_usuarios', 'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                            ->where('usua_nombre LIKE "%' . $search . '%"')
                            ->groupBy('id')
                            ->orderBy('usua_nombre')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Equipos::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->join('JOIN', 'tbl_usuarios', 'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                            ->where('tbl_usuarios.usua_id = ' . $id)
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Metodo que obtiene las preguntas de un arbol seleccionado en la vista de reporte por variables
             * 
             * 
             * @return array
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionPreguntas() {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = [];

                if (isset($_POST['depdrop_parents'])) {
                    $parents = $_POST['depdrop_parents'];
                    if ($parents != null && $parents[0] != "") {
                        $id = $parents[0];
                        $out = \app\models\Tmpreportes::getPreguntasByArbol($id);
                        echo Json::encode(['output' => $out, 'selected' => '']);
                        return;
                    }
                }
                echo Json::encode(['output' => '', 'selected' => '']);
            }

            /**
             * 
             * @param type $id
             * @return type
             */
            public function actionUpdatefeedbackcm($id) {
                $model = \app\models\Ejecucionfeedbacks::findOne($id);
                $activador = \app\models\Ejecucionfeedbacks::getSelectTipoFeedback($id);
                if (Yii::$app->getRequest()->isAjax) {
                    $model->feaccion_correctiva = date("Y-m-d H:i:s");
                    if ($model->load(Yii::$app->request->post()) && $model->save()) {
                        return $this->redirect(['site/dashboardalertas']);
                    }
                }
                return $this->renderAjax('updateFeedback', ['model' => $model, 'activador' => $activador]);
            }

            /**
             * 
             * @return type
             */
            public function actionDeclinaciones() {
                $model = new \app\models\DeclinacionesUsuariosSearch();
                $model->scenario = "declinacion";
                $dataProvider = [];
                $showGrid = false;
                $query = 'SELECT tgu.*,pga.*,rgu.* FROM tbl_grupos_usuarios tgu '
                        . 'INNER JOIN rel_grupos_usuarios rgu ON rgu.grupo_id = tgu.grupos_id '
                        . 'INNER JOIN tbl_permisos_grupos_arbols pga ON tgu.grupos_id = pga.grupousuario_id '
                        . ' INNER JOIN tbl_arbols a ON a.id = pga.arbol_id'
                        . ' WHERE rgu.usuario_id =' . Yii::$app->user->identity->id . '  GROUP BY pga.arbol_id';
                $queryGrupos = \Yii::$app->db->createCommand($query)->queryAll();
                foreach ($queryGrupos as $value) {
                    $idArbolesPermiso[] = $value['arbol_id'];
                }
                $cadenaIdarboles = implode(',', $idArbolesPermiso);
                //ESTADISTICAS 
                //Numero Declinaciones del mes------------------------------------------
                $numDeclinaciones = \app\models\DeclinacionesUsuarios::find()
                                //->join('INNER JOIN', 'rel_grupos_usuarios', 'usuario_id =  usua_id')
                                ->where("MONTH(fecha) = '" . date('m')
                                        . "' AND YEAR(fecha) = '" . date('Y') . "'")
                                ->andWhere('arbol_id IN (' . $cadenaIdarboles . ')')->count();
                // Top Declinacione ----------------------------------------------------
                /*
                 * 14/03/2016->Modificacion para obtener porcentaje de declinaciones en top declinaciones
                 * se agrega subconsulta
                 */
                $topDeclinaciones = \app\models\DeclinacionesUsuarios::find()->asArray()
                                ->select(['d.nombre', 'd.id', 'contar' => 'COUNT(*)', 'prom' => "(COUNT(*)/
                                     (SELECT COUNT(*) FROM tbl_declinaciones_usuarios duss
                                    JOIN  tbl_declinaciones dss ON dss.id = duss.declinacion_id
                                    WHERE MONTH(duss.fecha) = '" . date('m')
                                    . "'  AND YEAR(duss.fecha) = '" . date('Y') . "' AND duss.arbol_id IN (" . $cadenaIdarboles . "  ))*100)"])
                                ->from("tbl_declinaciones_usuarios du")
                                ->join('JOIN', 'tbl_declinaciones d', 'd.id = '
                                        . 'du.declinacion_id')
                                //->join('INNER JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id =  `du`.usua_id')
                                ->where("MONTH(du.fecha) = '" . date('m')
                                        . "' AND YEAR(du.fecha) = '" . date('Y') . "'")
                                ->andWhere('du.arbol_id IN (' . $cadenaIdarboles . ')')
                                ->groupBy('du.declinacion_id')
                                ->orderBy('contar DESC')
                                ->limit(3)->all();
                //Top Usuarios declinan ------------------------------------------------
                /*
                 * 14/03/2016->Modificacion para obtener porcentaje de declinaciones en top usuarios
                 * se agrega subconsulta
                 */
                $topUsuarios = \app\models\DeclinacionesUsuarios::find()->asArray()
                                ->select(['u.usua_nombre', 'id' => 'u.usua_id',
                                    'contar' => 'COUNT(*)', 'prom' => "(
                                    COUNT(*)
                                    /
                                    (SELECT COUNT(*)
                                    FROM tbl_declinaciones_usuarios dus
                                    JOIN tbl_usuarios us ON us.usua_id =dus.usua_id
                                    WHERE dus.arbol_id IN (" . $cadenaIdarboles . ")
                                    ))*100"])
                                ->from("tbl_declinaciones_usuarios du")
                                ->join('JOIN', 'tbl_usuarios u', 'u.usua_id= '
                                        . 'du.usua_id')
                                //->join('INNER JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id =  u.usua_id')
                                ->where('du.arbol_id IN (' . $cadenaIdarboles . ')')
                                ->groupBy('du.usua_id')
                                ->orderBy('contar DESC')
                                ->limit(3)->all();
                //----------------------------------------------------------------------
                if (Yii::$app->request->post('exportdeclinaciones')) {
                    $model->load(Yii::$app->session['rptDeclinaciones']);
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->search(Yii::$app->request->queryParams, false);
                    $showGrid = true;
                }
                if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptDeclinaciones']);
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0].' 00:00:01';
                    $model->endDate = $dates[1].' 23:59:59';
                    // Top Declinacione ----------------------------------------------------
                    /*
                     * 14/03/2016->Modificacion para obtener porcentaje de declinaciones en top declinaciones
                     * se agrega subconsulta
                     */
                    $topDeclinaciones = \app\models\DeclinacionesUsuarios::find()->asArray()
                                    ->select(['d.nombre', 'd.id', 'contar' => 'COUNT(*)', 'prom' => "(COUNT(*)/
                                     (SELECT COUNT(*) FROM tbl_declinaciones_usuarios duss
                                    JOIN  tbl_declinaciones dss ON dss.id = duss.declinacion_id
                                    WHERE duss.fecha BETWEEN '" . $dates[0].' 00:00:01'
                                        . "'  AND  '" . $dates[1].' 23:59:59' . "' AND duss.arbol_id IN (" . $cadenaIdarboles . "  ))*100)"])
                                    ->from("tbl_declinaciones_usuarios du")
                                    ->join('JOIN', 'tbl_declinaciones d', 'd.id = '
                                            . 'du.declinacion_id')
                                    //->join('INNER JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id =  `du`.usua_id')
                                    ->where("du.fecha BETWEEN '" . $dates[0].' 00:00:01'
                                            . "' AND '" . $dates[1].' 23:59:59' . "'")
                                    ->andWhere('du.arbol_id IN (' . $cadenaIdarboles . ')')
                                    ->groupBy('du.declinacion_id')
                                    ->orderBy('contar DESC')
                                    ->limit(3)->all();
                    //ESTADISTICAS 
                    //Numero Declinaciones del mes------------------------------------------
                    $numDeclinaciones = \app\models\DeclinacionesUsuarios::find()
                                    //->join('INNER JOIN', 'rel_grupos_usuarios', 'usuario_id =  usua_id')
                                    ->where("fecha BETWEEN '" . $dates[0].' 00:00:01'
                                            . "' AND '" .$dates[1].' 23:59:59' . "'")
                                    ->andWhere('arbol_id IN (' . $cadenaIdarboles . ')')->count();
                    //----------------------------------------------------------------------
                    //Top Usuarios declinan ------------------------------------------------
                    /*
                     * 14/03/2016->Modificacion para obtener porcentaje de declinaciones en top usuarios
                     * se agrega subconsulta
                     */
                    $topUsuarios = \app\models\DeclinacionesUsuarios::find()->asArray()
                                    ->select(['u.usua_nombre', 'id' => 'u.usua_id',
                                        'contar' => 'COUNT(*)', 'prom' => "(
                                    COUNT(*)
                                    /
                                    (SELECT COUNT(*)
                                    FROM tbl_declinaciones_usuarios dus
                                    JOIN tbl_usuarios us ON us.usua_id =dus.usua_id
                                    WHERE dus.arbol_id IN (" . $cadenaIdarboles . ")
                                        AND dus.fecha BETWEEN '" . $dates[0].' 00:00:01'
                                        . "' AND  '" . $dates[1].' 23:59:59'. " '
                                    ))*100"])
                                    ->from("tbl_declinaciones_usuarios du")
                                    ->join('JOIN', 'tbl_usuarios u', 'u.usua_id= '
                                            . 'du.usua_id')
                                    //->join('INNER JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id =  u.usua_id')
                                    ->where('du.arbol_id IN (' . $cadenaIdarboles . ')')
                                    ->andWhere("du.fecha BETWEEN '" . $dates[0].' 00:00:01'
                                            . "' AND '" . $dates[1].' 23:59:59' . "'")
                                    ->groupBy('du.usua_id')
                                    ->orderBy('contar DESC')
                                    ->limit(3)->all();
                    //----------------------------------------------------------------------

                    $dataProvider = $model->search(Yii::$app->request->queryParams);
                    $showGrid = true;
                }

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    // Top Declinacione ----------------------------------------------------
                    /*
                     * 14/03/2016->Modificacion para obtener porcentaje de declinaciones en top declinaciones
                     * se agrega subconsulta
                     */
                    $topDeclinaciones = \app\models\DeclinacionesUsuarios::find()->asArray()
                                    ->select(['d.nombre', 'd.id', 'contar' => 'COUNT(*)', 'prom' => "(COUNT(*)/
                                     (SELECT COUNT(*) FROM tbl_declinaciones_usuarios duss
                                    JOIN  tbl_declinaciones dss ON dss.id = duss.declinacion_id
                                    WHERE duss.fecha BETWEEN '" . $dates[0].' 00:00:01'
                                        . "'  AND  '" . $dates[1].' 23:59:59' . "' AND duss.arbol_id IN (" . $cadenaIdarboles . "  ))*100)"])
                                    ->from("tbl_declinaciones_usuarios du")
                                    ->join('JOIN', 'tbl_declinaciones d', 'd.id = '
                                            . 'du.declinacion_id')
                                    //->join('INNER JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id =  `du`.usua_id')
                                    ->where("du.fecha BETWEEN '" . $dates[0].' 00:00:01'
                                            . "' AND '" . $dates[1].' 23:59:59'. "'")
                                    ->andWhere('du.arbol_id IN (' . $cadenaIdarboles . ')')
                                    ->groupBy('du.declinacion_id')
                                    ->orderBy('contar DESC')
                                    ->limit(3)->all();
                    //ESTADISTICAS 
                    //Numero Declinaciones del mes------------------------------------------
                    $numDeclinaciones = \app\models\DeclinacionesUsuarios::find()
                                    //->join('INNER JOIN', 'rel_grupos_usuarios', 'usuario_id =  usua_id')
                                    ->where("fecha BETWEEN '" . $dates[0].' 00:00:01'
                                            . "' AND '" . $dates[1].' 23:59:59' . "'")
                                    ->andWhere('arbol_id IN (' . $cadenaIdarboles . ')')->count();
                    //----------------------------------------------------------------------
                    //Top Usuarios declinan ------------------------------------------------
                    /*
                     * 14/03/2016->Modificacion para obtener porcentaje de declinaciones en top usuarios
                     * se agrega subconsulta
                     */
                    $topUsuarios = \app\models\DeclinacionesUsuarios::find()->asArray()
                                    ->select(['u.usua_nombre', 'id' => 'u.usua_id',
                                        'contar' => 'COUNT(*)', 'prom' => "(
                                    COUNT(*)
                                    /
                                    (SELECT COUNT(*)
                                    FROM tbl_declinaciones_usuarios dus
                                    JOIN tbl_usuarios us ON us.usua_id =dus.usua_id
                                    WHERE dus.arbol_id IN (" . $cadenaIdarboles . ")
                                        AND dus.fecha BETWEEN '" . $dates[0].' 00:00:01'
                                        . "' AND  '" . $dates[1].' 23:59:59' . " '
                                    ))*100"])
                                    ->from("tbl_declinaciones_usuarios du")
                                    ->join('JOIN', 'tbl_usuarios u', 'u.usua_id= '
                                            . 'du.usua_id')
                                    //->join('INNER JOIN', 'rel_grupos_usuarios rgu', 'rgu.usuario_id =  u.usua_id')
                                    ->where('du.arbol_id IN (' . $cadenaIdarboles . ')')
                                    ->andWhere("du.fecha BETWEEN '" . $dates[0].' 00:00:01'
                                            . "' AND '" . $dates[1].' 23:59:59' . "'")
                                    ->groupBy('du.usua_id')
                                    ->orderBy('contar DESC')
                                    ->limit(3)->all();
                    //----------------------------------------------------------------------
                    $dataProvider = $model->search(Yii::$app->request->queryParams);
                    $showGrid = true;
                    Yii::$app->session['rptDeclinaciones'] = Yii::$app->request->post();
                }

                return $this->render('declinaciones', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid,
                            'numDeclinaciones' => $numDeclinaciones,
                            'topDeclinaciones' => $topDeclinaciones,
                            'topUsuarios' => $topUsuarios]);
            }

            /**
             * Reporte de satisfaccion
             * @return string
             */
            public function actionSatisfaccion() {
                $model = new \app\models\BaseSatisfaccionSearch();
                $dataProvider = [];
                $showGrid = false;
                $model->scenario = 'reporte_satisfaccion_indicadores';

                if (Yii::$app->request->post('exportsatisfaccion') || Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptSatisfaccion']);
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->reporteSatisfaccion();
                    $showGrid = true;
                }

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->reporteSatisfaccion();
                    $showGrid = true;
                    Yii::$app->session['rptSatisfaccion'] = Yii::$app->request->post();
                }

                return $this->render('satisfaccion', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid,
                ]);
            }

            public function actionControlsatisfaccion() {
                $model = new \app\models\BaseSatisfaccionSearch();
                $dataProvider = [];
                $showGrid = false;
                $model->scenario = 'reporte_satisfaccion';

                if (Yii::$app->request->post('exportControlsatisfaccion') || Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptControlSatisfaccion']);
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->rptControlSatisfaccion();
                    $showGrid = true;
                }

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->rptControlSatisfaccion();
                    $showGrid = true;
                    Yii::$app->session['rptControlSatisfaccion'] = Yii::$app->request->post();
                }

                return $this->render('controlSatisfaccion', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid,
                ]);
            }

            public function actionHistoricosatisfaccion() {
                $model = new \app\models\BaseSatisfaccionSearch();
                $dataProvider = "";
                $titulos = array();
                $export = false;
                $model->scenario = 'reporte_satisfaccion';

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->fecha);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $export = $model->extractConsTransSatisfaccion();
                    /*
                      $dataProvider = $datos[0];
                      if (count($dataProvider->allModels) > 0) {
                      $export = true;
                      } else {
                      $export = false;
                      }
                      $titulos = $datos[1];
                      $columns = array_keys($datos[1]);
                      $_POST['export_type'] = 'Excel2007';
                      $_POST['column_selector_enabled'] = 0;
                      $_POST['export_columns'] = implode(',', $columns); */
                    $_POST['exporthistorico'] = 1;
                }

                return $this->render('reportehistoricobasesatisfaccion', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'export' => $export,
                            'titulos' => $titulos,
                ]);
            }

            /**
             * Generacion de reporte Feedback Express
             * 
             * @return string
             */
            public function actionFeedbackexpressamigo($evaluado_usuared) {
                $model = new \app\models\Ejecucionfeedbacks();
                $modelEvaluado = \app\models\Evaluados::findOne(['dsusuario_red' => base64_decode($evaluado_usuared)]);
                $id_evaluado = (isset($modelEvaluado->id)) ? $modelEvaluado->id : '';
                $model->evaluado_id = $id_evaluado;
                $model->scenario = 'reporte';
                $dataProvider = [];
                $showGrid = false;
                if ($id_evaluado == '') {
                    $msg = \Yii::t('app', 'No se recibió o no existe un asesor para poder realizar la consulta');
                    Yii::$app->session->setFlash('danger', $msg);
                } else {
                    if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                        $model->load(Yii::$app->session['rptFilterFeedback']);
                        $dates = explode(' - ', $model->created);
                        /* $model->startDate = strtotime('-2 month', strtotime($dates[0]));
                          $model->startDate = date('Y-m-j', $model->startDate); */
                        $model->startDate = $dates[0] . " 00:00:00";
                        $model->endDate = $dates[1] . " 23:59:59";
                        $dataProvider = $model->getReportfeedbacksamigo();
                        $showGrid = true;
                    }

                    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                        $dates = explode(' - ', $model->created);
                        /* $model->startDate = strtotime('-2 month', strtotime($dates[0]));
                          $model->startDate = date('Y-m-j', $model->startDate); */
                        $model->startDate = $dates[0] . " 00:00:00";
                        $model->endDate = $dates[1] . " 23:59:59";
                        $dataProvider = $model->getReportfeedbacksamigo();
                        $showGrid = true;
                        Yii::$app->session['rptFilterFeedback'] = Yii::$app->request->post();
                    }
                }

                return $this->render('feedback-amigo', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid]);
            }

            public function actionHistoricoformulariosamigo($evaluado_usuared) {

                $model = new \app\models\Ejecucionformularios();
                $modelEvaluado = \app\models\Evaluados::findOne(['dsusuario_red' => base64_decode($evaluado_usuared)]);
                $id_evaluado = (isset($modelEvaluado->id)) ? $modelEvaluado->id : '';
                /* Llama al SP sp_llenar_tmpreportes. */
                //$rol = Yii::$app->user->identity->rolId;
                /*
                  if ($rol == 1) {
                  $user_admin = 1;
                  } else {
                  $user_admin = 0;
                  } */
                $model->scenario = 'historico';
                $dataProvider = [];
                $showGrid = false;
                if ($id_evaluado == '') {
                    $msg = \Yii::t('app', 'No se recibió o no existe un asesor para poder realizar la consulta');
                    Yii::$app->session->setFlash('danger', $msg);
                } else {
                    if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                        $model->load(Yii::$app->session['rptFilterFormulariosamigo']);
                        $model->evaluado_id = $id_evaluado;
                        $dates = explode(' - ', $model->created);
                        /* $model->startDate = strtotime('-2 month', strtotime($dates[0]));
                          $model->startDate = date('Y-m-j', $model->startDate); */
                        $model->startDate = $dates[0] . " 00:00:00";
                        $model->endDate = $dates[1] . " 23:59:59";
                        $dataProvider = $model->getReportformulariosamigo(true);
                        $showGrid = true;
                    }
                    if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                        $dates = explode(' - ', $model->created);
                        /* $model->startDate = strtotime('-2 month', strtotime($dates[0]));
                          $model->startDate = date('Y-m-j', $model->startDate); */
                        $model->startDate = $dates[0] . " 00:00:00";
                        $model->endDate = $dates[1] . " 23:59:59";
                        $model->evaluado_id = $id_evaluado;
                        $dataReport = new \stdClass();
                        $dataReport->fingreso_formulario_ini = $model->startDate;
                        $dataReport->fingreso_formulario_fin = $model->endDate;
                        $model->llenarTtmpReportes(
                                0, 0, $id_evaluado, $dataReport->fingreso_formulario_ini, $dataReport->fingreso_formulario_fin);
                        $dataProvider = $model->getReportformulariosamigo(true);
                        $showGrid = true;
                        Yii::$app->session['rptFilterFormulariosamigo'] = Yii::$app->request->post();
                    } else {
                        //BUSCAR LAS VALORACIONES DEL MES ACTUAL POR DEFECTO
                        $model->created = date('Y-m-01') . ' - ' . date('Y-m-d');
                        $model->startDate = date('Y-m-01') . " 00:00:00";
                        $model->endDate = date('Y-m-d') . " 23:59:59";
                        $model->evaluado_id = $id_evaluado;
                        $dataReport = new \stdClass();
                        $dataReport->fingreso_formulario_ini = $model->startDate;
                        $dataReport->fingreso_formulario_fin = $model->endDate;
                        $model->llenarTtmpReportes(
                                0, 0, $id_evaluado, $dataReport->fingreso_formulario_ini, $dataReport->fingreso_formulario_fin);
                        $dataProvider = $model->getReportformulariosamigo(true);
                        $showGrid = true;
                        Yii::$app->session['rptFilterFormulariosamigo'] = Yii::$app->request->post();
                    }
                }
                return $this->render('historico-amigo', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid]);
            }

            /**
             * Generacion de reporte Feedback Express
             * 
             * @return string
             */
            public function actionAlertasamigo($evaluado_usuared) {
                $modelEvaluado = \app\models\Notificaciones::findOne(['asesor' => base64_decode($evaluado_usuared)]);

                if ($evaluado_usuared == '') {
                    $msg = \Yii::t('app', 'No se recibió o no existe un asesor para poder realizar la consulta');
                    Yii::$app->session->setFlash('danger', $msg);
                }
                $showGrid = true;
                $dataProvider = [];

                return $this->render('alertas-amigo', [
                            'model' => $modelEvaluado,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid]);
            }


            public function actionHistoricoencuestasamigo($evaluado_usuared) {

                $model = new \app\models\Ejecucionformularios();
                $modelEvaluado = base64_decode($evaluado_usuared);
                $id_evaluado = $modelEvaluado;
                /* Llama al SP sp_llenar_tmpreportes. */
                //$rol = Yii::$app->user->identity->rolId;
                /*
                  if ($rol == 1) {
                  $user_admin = 1;
                  } else {
                  $user_admin = 0;
                  } */
                $model->scenario = 'historico';
                $dataProvider = [];
                $showGrid = false;
                if ($id_evaluado == '') {
                    $msg = \Yii::t('app', 'No se recibió o no existe un asesor para poder realizar la consulta');
                    Yii::$app->session->setFlash('danger', $msg);
                } else {
                        //BUSCAR LAS VALORACIONES DEL MES ACTUAL POR DEFECTO
                        $model->created = date('Y-m-01') . ' - ' . date('Y-m-d');
                        $model->startDate = date('Y-m-01') . " 00:00:00";
                        $model->endDate = date('Y-m-d') . " 23:59:59";
                        $model->evaluado_id = $id_evaluado;
                        $dataReport = new \stdClass();
                        $dataReport->fingreso_formulario_ini = $model->startDate;
                        $dataReport->fingreso_formulario_fin = $model->endDate;
                        $dataProvider = $model->getReportencuestasamigo(true);
                        //print_r($dataProvider); die;
                        $showGrid = true;
                        Yii::$app->session['rptFilterFormulariosamigo'] = Yii::$app->request->post();
                }
                //echo "<pre>";
                //print_r($dataProvider); die;
                return $this->render('encuestas-amigo', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,
                            'showGrid' => $showGrid]);
            }

            /**
             * Obtiene el listado de dimensiones
             * @param type $search
             * @param type $id
             */
            public function actionDimensionlist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => true];
                if (!is_null($search)) {
                    $data = \app\models\Dimensiones::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(name)'])
                            ->where('name LIKE "%' . $search . '%"')
                            ->orderBy('name')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Dimensiones::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(name)'])
                            ->where('id IN (' . $id . ')')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de evaluados (funcion copia de actionEvaluadolist, se modifica el elseif
             * para que reciba varios ids )
             * @param type $search
             * @param type $id
             */
            public function actionEvaluadolistmultiple($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                            ->where('name LIKE "%' . $search . '%"')
                            ->orderBy('name')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                            ->where('tbl_evaluados.id IN (' . $id . ')')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de arboles de la tabla tbl_tmpreportes_arbol
             * @param type $search
             * @param type $id
             */
            public function actionGetarboles($search = null, $id = null) {
                $out = ['more' => false];
                $user_id = Yii::$app->user->identity->id;
                if (!is_null($search)) {
                    $data = \app\models\TmpreportesArbol::find()
                            ->join("JOIN", "rel_grupos_usuarios", "rel_grupos_usuarios.usuario_id = tbl_tmpreportes_arbol.usua_id")
                            ->join("JOIN", "tbl_permisos_grupos_arbols", "tbl_tmpreportes_arbol.arbol_id = tbl_permisos_grupos_arbols.arbol_id")
                            ->select(['id' => 'tbl_tmpreportes_arbol.seleccion_arbol_id', 'text' => 'UPPER(tbl_tmpreportes_arbol.dsruta_arbol)'])
                            ->distinct()
                            ->where([
                                "tbl_tmpreportes_arbol.usua_id" => $user_id,
                                "tbl_permisos_grupos_arbols.snver_grafica" => 1])
                            ->andWhere("rel_grupos_usuarios.grupo_id = tbl_permisos_grupos_arbols.grupousuario_id")
                            ->andWhere("tbl_tmpreportes_arbol.seleccion_arbol_id = tbl_tmpreportes_arbol.arbol_id")
                            ->andWhere('tbl_tmpreportes_arbol.dsruta_arbol LIKE "%' . $search . '%" ')
                            ->orderBy("tbl_tmpreportes_arbol.dsruta_arbol ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\TmpreportesArbol::find()
                            ->join("JOIN", "rel_grupos_usuarios", "rel_grupos_usuarios.usuario_id = tbl_tmpreportes_arbol.usua_id")
                            ->join("JOIN", "tbl_permisos_grupos_arbols", "tbl_tmpreportes_arbol.arbol_id = tbl_permisos_grupos_arbols.arbol_id")
                            ->select(['id' => 'tbl_tmpreportes_arbol.seleccion_arbol_id', 'text' => 'UPPER(tbl_tmpreportes_arbol.dsruta_arbol)'])
                            ->distinct()
                            ->where([
                                "tbl_tmpreportes_arbol.usua_id" => $user_id,
                                "tbl_permisos_grupos_arbols.snver_grafica" => 1])
                            ->andWhere("rel_grupos_usuarios.grupo_id = tbl_permisos_grupos_arbols.grupousuario_id")
                            ->andWhere("tbl_tmpreportes_arbol.seleccion_arbol_id = tbl_tmpreportes_arbol.arbol_id")
                            ->andWhere('tbl_tmpreportes_arbol.seleccion_arbol_id IN (' . $id . ')')
                            ->orderBy("tbl_tmpreportes_arbol.dsruta_arbol ASC")
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Obtiene el listado de roles
             * @param type $search
             * @param type $id
             */
            public function actionRollistmultiple($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Roles::find()
                            ->select(['id' => 'tbl_roles.role_id', 'text' => 'UPPER(role_nombre)'])
                            ->where('role_nombre LIKE "%' . $search . '%"')
                            ->orderBy('role_nombre')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Roles::find()
                            ->select(['id' => 'tbl_roles.role_id', 'text' => 'UPPER(role_nombre)'])
                            ->where('tbl_roles.role_id IN (' . $id . ')')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }
	            /**
             * Generacion de reporte Feedback Express
             * 
             * @return string
             */
            public function actionReportesegundocalificador() {
                $model = new \app\models\SegundoCalificador();
                $model->scenario = 'reporte';
                $dataProvider = $model->getReportSegundoCalificador();
                $showGrid = true;
                $export = false;
                if (Yii::$app->request->post('exportsegundocalificador')){
                    $export = true;
                }
                /*if (Yii::$app->request->post('exportfeedback') || Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                    $model->load(Yii::$app->session['rptFilterFeedback']);
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportFeedbacks();
                    if (Yii::$app->request->get('page') || Yii::$app->request->get('sort')) {
                        $resumenFeedback = $model->getResumenFeedback();
                        $detalleLiderFeedback = $model->getDetalleLiderFeedback();
                    }
                    $showGrid = true;
                }

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $dates = explode(' - ', $model->created);
                    $model->startDate = $dates[0];
                    $model->endDate = $dates[1];
                    $dataProvider = $model->getReportFeedbacks();
                    $resumenFeedback = $model->getResumenFeedback();
                    $detalleLiderFeedback = $model->getDetalleLiderFeedback();
                    $showGrid = true;
                    Yii::$app->session['rptFilterFeedback'] = Yii::$app->request->post();
                }*/

                return $this->render('segundo-calificador', [
                            'model' => $model,
                            'dataProvider' => $dataProvider,                            
                            'showGrid' => $showGrid,
                            'export'=>$export]);
            }

        }
        