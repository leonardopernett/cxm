<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\helpers\Url;

class SiteController extends Controller {

    public function behaviors() {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'dashboard', 'getgraph'],
                'rules' => [
                    [
                        'actions' => ['showestadonotificacion'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout', 'dashboard', 'getgraph', 'getRecursivearbols',
                            'dashboardalertas'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    public function actions() {
        return [
            /* 'error' => [
              'class' => 'yii\web\ErrorAction',
              ], */
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
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
            //RENERIZO LA VISTA
            return $this->render('error', [
                        'name' => $name,
                        'message' => $message,
                        'exception' => $exception,
            ]);
        }
    }

    public function actionIndex() {
        if (\Yii::$app->user->isGuest) {
            return $this->redirect(['site/login']);
        }
        $galeria = \app\models\Slides::find()->where(["activo" => "1"])->all();
        $noticias = \app\models\Noticias::find()->where(["activa" => "1"])->orderBy("id DESC")->all();
        return $this->render('index', ["noticias" => $noticias, "galeria" => $galeria]);
    }

    public function actionLogin() {
        $this->layout = "login";
        if (!\Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                        'model' => $model,
            ]);
        }
    }

    public function actionLogout() {
        $usuario_id = Yii::$app->user->identity->id;
        $usuario_red = Yii::$app->user->identity->username;
        $usuario_ip = Yii::$app->getRequest()->getUserIP();
        Yii::$app->user->logout();
        
        \Yii::$app->db->createCommand()->insert('tbl_usuarioslog', [
            'uslg_usua_id' => $usuario_id,
            'uslg_usuario' => $usuario_red,
            'uslg_fechahora' => date('Y-m-d h:i:s'),
            'uslg_ip' => $usuario_ip,
            'uslg_accion' => 'Desconexion',
            'uslg_estado' => 'Exitoso'
        ])->execute();
        return $this->goHome();
    }

    public function actionDashboard() {
        $dataAlertas = new \stdClass();
        $dataAlertas->alertasList = \app\models\Ejecucionfeedbacks::getAlertas(\Yii::$app->user->identity->id);
        $dataAlertas->requestType = 'FUNC';
        $filtros = new \stdClass();
        $data = new \stdClass();
        $arbIds = [];

        $controlador = Yii::$app->controller->id;
        $vista = Yii::$app->controller->action->id;
        $post = Yii::$app->request->post();
        if (empty($post)) {

            $filtrosForm = \app\models\FiltrosFormularios::findOne(['vista' => $controlador . '/' . $vista, 'usua_id' => Yii::$app->user->identity->id]);
            if (!empty($filtrosForm)) {
                $dataFiltos = json_decode($filtrosForm->parametros);
                $filtros->fecha = $fecha = date('Y-m-01') . ' - ' . date('Y-m-d');
                $filtros->dimension = $dataFiltos->dimension;
                $filtros->metrica = $dataFiltos->metrica;
                $arbIds = $dataFiltos->arbol_ids;

                $fecha = explode(' - ', $fecha);
                $fechaInicio = $fecha[0];
                $fechaFin = $fecha[1];

                $data->graph = $this->actionGetGraph(
                        $dataFiltos->arbol_ids
                        , $filtros->dimension
                        , $filtros->metrica
                        , $fechaInicio
                        , $fechaFin);
            } else {
                $filtros->dimension = '';
                $filtros->metrica = '';
            }
        } else {
            $postarbol_ids = Yii::$app->request->post('arbol_ids');
            if (isset($postarbol_ids) && count($postarbol_ids) > 0) {
                $filtros->fecha = $fecha = Yii::$app->request->post('selMesDesde');
                $filtros->dimension = Yii::$app->request->post("selDimension");
                $filtros->metrica = Yii::$app->request->post("selMetrica");
                $fecha = explode(' - ', $fecha);
                $fechaInicio = $fecha[0];
                $fechaFin = $fecha[1];

                //Guardar filtros --------------------------------------------------                    
                $filtrosDatos = new \stdClass();
                $filtrosDatos->fecha = Yii::$app->request->post("selMesDesde");
                $filtrosDatos->dimension = Yii::$app->request->post("selDimension");
                $filtrosDatos->metrica = Yii::$app->request->post("selMetrica");
                $filtrosDatos->arbol_ids = Yii::$app->request->post("arbol_ids");
                $arbIds = $filtrosDatos->arbol_ids;

                $filtrosForm = \app\models\FiltrosFormularios::findOne(['vista' => $controlador . '/' . $vista, 'usua_id' => Yii::$app->user->identity->id]);

                if (empty($filtrosForm)) {
                    $filtrosForm = new \app\models\FiltrosFormularios;
                }
                $filtrosForm->usua_id = Yii::$app->user->identity->id;
                $filtrosForm->vista = $controlador . '/' . $vista;
                $filtrosForm->parametros = json_encode($filtrosDatos);
                $filtrosForm->save();

                $data->graph = $this->actionGetGraph(
                        Yii::$app->request->post("arbol_ids")
                        , Yii::$app->request->post("selDimension")
                        , Yii::$app->request->post("selMetrica")
                        , $fechaInicio
                        , $fechaFin
                );
            } else {
                $filtros->dimension = Yii::$app->request->post("selDimension");
                $filtros->metrica = Yii::$app->request->post("selMetrica");
                $msg = \Yii::t('app', 'Seleccione un arbol');
                Yii::$app->session->setFlash('danger', $msg);
            }
        }

        $arboles = \app\models\Arboles::getArbolByUser();
        $arrArbRol = ArrayHelper::map($arboles, 'id', 'id');

        $data2 = $this->getRecursiveArbByRol('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', $arbIds, $arrArbRol);

        return $this->render('dashboard', [
                    'data' => $dataAlertas,
                    'data' => $data,
                    'data2' => $data = new \stdClass,
                    'data3' => $data2,
                    'filtros' => $filtros,
        ]);
    }

    public function actionGetgraph($arrIds, $dimension_id, $metrica, $fechaInicio, $fechaFin) {

        //Tomamos el numero de dias --------------------------------------------
        $step = '+1 day';
        $format = 'd';
        $dates = array();
        $current = strtotime($fechaInicio);
        $last = strtotime($fechaFin);

        while ($current <= $last) {
            $tempDate = date($format, $current);
            $dates[$tempDate] = null;
            $current = strtotime($step, $current);
        }
        //----------------------------------------------------------------------        

        if (count($arrIds) > 0) {
            \app\models\Preferencias::setEstadisticaDef($arrIds, $dimension_id);
        }

        $data = new \stdClass();
        $result = [];
        $resultCant = [];
        $data->noGraph = false;

        switch ($metrica) {
            case 1:
                $colMetrica = 'i1_nmcalculo';
                break;
            case 2:
                $colMetrica = 'i2_nmcalculo';
                break;
            case 3:
                $colMetrica = 'i3_nmcalculo';
                break;
            case 4:
                $colMetrica = 'i4_nmcalculo';
                break;
            case 5:
                $colMetrica = 'i5_nmcalculo';
                break;
            case 6:
                $colMetrica = 'i6_nmcalculo';
                break;
            case 7:
                $colMetrica = 'i7_nmcalculo';
                break;
            case 8:
                $colMetrica = 'i8_nmcalculor';
                break;
            case 9:
                $colMetrica = 'i9_nmcalculo';
                break;
            case 10:
                $colMetrica = 'i10_nmcalculo';
                break;
            case 11:
                $colMetrica = 'score';
                break;
            default :
                $colMetrica = '';
                break;
        }

        if (!empty($colMetrica)) {
            foreach ($arrIds as $arbol_id) {
                $tempData = $tempDataCant = $dates;
                $name = '';
                //Calculamos el procentaje -------------------------------------
                $sql = "SELECT DATE_FORMAT(e.created, '%d') as fecha, 
                    ROUND((avg(e.$colMetrica)*100), 2) promedio,                    
                    COUNT($colMetrica) cantidad,                    
                    a.name as arbol
                    FROM tbl_ejecucionformularios e
                    JOIN tbl_arbols a ON a.id = e.arbol_id
                    WHERE e.dimension_id = $dimension_id 
                        AND (e.created >= '$fechaInicio 00:00:00' 
                        AND e.created <= '$fechaFin 23:59:59')
                        AND e.arbol_id = $arbol_id
                    GROUP BY DATE_FORMAT(e.created, '%Y%m%d')";
                $resultData = \Yii::$app->db->createCommand($sql)->queryAll();

                if (count($resultData) > 0) {
                    foreach ($resultData as $value) {
                        $tempData[$value['fecha']] = (double) $value['promedio'];
                        $tempDataCant[$value['fecha']] = (int) $value['cantidad'];
                        $name = $value['arbol'];
                    }
                    $result[] = ['name' => $name, 'data' => array_values($tempData)];
                    $resultCant[] = ['name' => $name, 'data' => array_values($tempDataCant)];
                }
            }
        }
        
        $showGraf = (count($result) > 0 || count($resultCant) > 0);

        $data->dimensiones = \app\models\Dimensiones::find()->asArray()->all();
        $data->hojas = \app\models\Arboles::getArbolByPermisoGraficar();
        $data->arrIds = $arrIds;
        $data->arrData = $result;
        $data->arrDataCant = $resultCant;
        $data->dimension_id = $dimension_id;
        $data->showGraf = $showGraf;
        $data->nameMetrica = \app\models\Metrica::find()->where(['id' => $metrica])->one();
        return $data;
    }

    public function getRecursivearbolscopia($tabla, $id_field, $show_data, $link_field, $parent, $prefix, $arraArboles) {
        /* Armar query */
        if ($parent == 0) {
            $sql = 'select * from ' . $tabla . ' where ' . $link_field . ' is null';
        } else {
            $sql = 'select * from ' . $tabla . ' where ' . $link_field . '=' . $parent;
        }
        $rs = Yii::$app->db->createCommand($sql)->queryAll();
        $out = '<ol id="arbol_ids">';
        if ($rs) {
            foreach ($rs as $arr) {
                if (in_array($arr['id'], $arraArboles)) {
                    $out .= '<li data-value = "' . $arr['id'] . '" data-name = "arbol_ids[]" data-checked="checked">';
                } else {
                    $out .= '<li data-value = "' . $arr['id'] . '" data-name = "arbol_ids[]">';
                }
                $out .= $arr['name'];
                $out .=$this->getRecursivearbolscopia($tabla, $id_field, $show_data, $link_field, $arr[$id_field], $prefix . $prefix, $arraArboles);
            }
        }
        $out .= '</li></ol>';
        return $out;
    }

    public function getRecursiveArbByRol($tabla, $id_field, $show_data, $link_field, $parent, $prefix, $arraArboles, $arrArbRol) {
        /* Armar query */
        if ($parent == 0) {
            $sql = 'select * from ' . $tabla . ' where ' . $link_field . ' is null';
        } else {
            $sql = 'select * from ' . $tabla . ' where ' . $link_field . '=' . $parent;
        }
        $rs = Yii::$app->db->createCommand($sql)->queryAll();
        $out = '<ol id="arbol_ids">';
        if ($rs) {
            foreach ($rs as $arr) {
                if (in_array($arr['id'], $arrArbRol)) {
                    if (in_array($arr['id'], $arraArboles)) {
                        $out .= '<li data-value = "' . $arr['id'] . '" data-name = "arbol_ids[]" data-checked="checked">';
                    } else {
                        $out .= '<li data-value = "' . $arr['id'] . '" data-name = "arbol_ids[]">';
                    }
                    $out .= $arr['name'];
                    $out .=$this->getRecursiveArbByRol($tabla, $id_field, $show_data, $link_field, $arr[$id_field], $prefix . $prefix, $arraArboles, $arrArbRol);
                }
            }
        }
        $out .= '</li></ol>';
        return $out;
    }

    public function actionDashboardalertas() {
        $dataAlertas = new \stdClass();
        $dataAlertas->alertasList = \app\models\Ejecucionfeedbacks::getAlertasdashboard(\Yii::$app->user->identity->id);
        $dataAlertas->requestType = 'FUNC';
        return $this->render('dashboardalert', [
                    'data' => $dataAlertas,
        ]);
    }

    public function actionSegundocalificador() {
        $searchModel = new \app\models\SegundoCalificadorSearch();
        if (Yii::$app->request->get()) {
            $dataProvider = $searchModel->searchFilter(Yii::$app->request->get());
        } else {
            $dataProvider = $searchModel->search();
        }
        return $this->render('dashboardsegundocalificador', [
                    'searchModel' => $searchModel,
                    'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Funcion que sirve para los lideres y evaluadores ver sus notificaciones de segundo calificador
     * @param integer $id
     * @return mixed
     */
    public function actionViewnotificacion() {
        $isAjax = false;
        if (Yii::$app->getRequest()->isAjax) {
            if (Yii::$app->request->post()) {
                $model = new \app\models\SegundoCalificador();
                $scid = Yii::$app->request->post('scid');
                $modelEdit = $this->findModel($scid);
                $fid = Yii::$app->request->post('fid');
                $id_caso = Yii::$app->request->post('id_caso');
                $modelform = \app\models\Ejecucionformularios::find()->where(['id' => $fid])->one();
                $datos = Yii::$app->request->post('SegundoCalificador');
                if (($datos['estado_sc'] == 'Rechazado') && (Yii::$app->user->identity->id == $modelform->usua_id_lider && Yii::$app->user->identity->id == $modelEdit->id_evaluador)) {
                    $model->b_editar = 1;
                    $model->id_responsable = null;
                } else {
                    if ($datos['estado_sc'] == 'Escalado') {
                        $model->b_editar = 0;
                        $model->id_responsable = $modelform->usua_id;
                    }
                    if ($datos['estado_sc'] == 'Aceptado') {
                        $model->b_editar = 0;
                        $model->id_responsable = null;
                    }
                }
                $model->id_ejecucion_formulario = $fid;
                $model->id_evaluador = $modelform->usua_id;
                $model->id_solicitante = $modelform->evaluado_id;
                $model->s_fecha = date('Y-m-d H:i:s');
                $model->estado_sc = $datos['estado_sc'];
                $model->argumento = "<b>" . Yii::$app->user->identity->fullName . '</b>: ' . $datos['argumentoLider'];
                $model->id_caso = $id_caso;
                $model->save();
                $modelEdit->gestionado = "SI";
                $modelEdit->save();
                $this->llamarwsLiderAmigo($model, false);
                $this->redirect(['segundocalificador']);
            } else {
                $scid = Yii::$app->request->get('id');
                $id_caso = Yii::$app->request->get('id_caso');
                $modelCaso = \app\models\SegundoCalificador::find()
                                ->select("argumento, s_fecha")
                                ->where(['id_caso' => $id_caso])
                                ->orderBy("id_segundo_calificador ASC")->all();
                $model = $this->findModel($scid);
                $model->scenario = 'liderevaluado';
                $arrayCadena = explode('<br>', $model->argumento);
                $argumento = $model->argumento;
                return $this->renderAjax('viewnotificacionsc', [
                            'model' => $model,
                            'isAjax' => $isAjax,
                            'scid' => $scid,
                            'arrayCadena' => $arrayCadena,
                            'argumento' => $argumento,
                            'modelCaso' => $modelCaso,
                            'id_caso' => $model->id_caso,
                            'id_ejecucion_formulario' => $model->id_ejecucion_formulario,
                ]);
            }
        }
    }

    /**
     * funcion que permite la creacion y visualizacion desde la vista de historico amigo de segundas califcaciones
     * @return mixed
     */
    public function actionCreate() {
        $model = new \app\models\SegundoCalificador();
        $isAjax = false;
        $model->scenario = 'asesor';
        if (Yii::$app->getRequest()->isAjax) {
            $isAjax = true;
            if ($model->load(Yii::$app->request->post())) {
                $datos = Yii::$app->request->post('SegundoCalificador');
                $bandera = Yii::$app->request->post('bandera');
                $historico = Yii::$app->request->post('historico');
                $esLider = Yii::$app->request->post('esLider');                
                if ($bandera == 0) {
                    $fid = Yii::$app->request->post('fid');
                    $formulario = \app\models\Ejecucionformularios::find()->where(['id' => $fid])->one();
                    $evaluado = \app\models\Evaluados::find()->where(['id' => $formulario->evaluado_id])->one();
                    $model->id_ejecucion_formulario = $fid;
                    $model->id_evaluador = $formulario->usua_id;
                    $model->id_solicitante = $formulario->evaluado_id;
                    if($esLider == '1'){
                        $model->estado_sc = 'Escalado';
                        $model->id_responsable = $formulario->usua_id;
                        $model->argumento = "<b>" . Yii::$app->user->identity->fullName . '</b>: ' . $datos['argumentoAsesor'];
                    }else{
                        $model->estado_sc = 'Abierto';
                        $model->id_responsable = $formulario->usua_id_lider;
                        $model->argumento = "<b>" . $evaluado->name . '</b>: ' . $datos['argumentoAsesor'];
                    }
                    $model->s_fecha = date('Y-m-d H:i:s');
                    //ID DE CASO, UN NUMERO UNICO E IRREPETIBLE PUEDE SER LA FECHA
                    $model->id_caso = date('YmdHis');
                    $model->save();
                    if($esLider == '1'){
                        $this->llamarwsLiderAmigo($model, false);
                        return $this->redirect(['reportes/historicoformularios']);
                    }
                    return ($historico == 0) ? ($this->redirect(['reportes/historicoformulariosamigo',
                                "evaluado_usuared" => base64_encode($evaluado->dsusuario_red)])) : true;
                } else {
                    $modelForm = \app\models\SegundoCalificador::find()->where(['id_segundo_calificador' => Yii::$app->request->post('scid')])->one();
                    $formulario = \app\models\Ejecucionformularios::find()->where(['id' => $modelForm->id_ejecucion_formulario])->one();
                    $evaluado = \app\models\Evaluados::find()->where(['id' => $formulario->evaluado_id])->one();
                    $sql = 'SELECT u.usua_id,u.usua_usuario,r.grupo_id,g.usua_id_responsable AS resp FROM  tbl_usuarios u '
                            . 'INNER JOIN rel_grupos_usuarios r ON u.usua_id = r.usuario_id '
                            . 'INNER JOIN tbl_grupos_usuarios g ON g.grupos_id = r.grupo_id'
                            . ' INNER JOIN tbl_permisos_grupos_arbols pga ON pga.grupousuario_id = g.grupos_id'
                            . ' WHERE u.usua_id = ' . $formulario->usua_id . ' AND pga.arbol_id = ' . $formulario->arbol_id;
                    $liderEvaluador = \Yii::$app->db->createCommand($sql)->queryAll();
                    $model = new \app\models\SegundoCalificador();
                    $model->id_ejecucion_formulario = $modelForm->id_ejecucion_formulario;
                    $model->id_evaluador = $formulario->usua_id;
                    $model->id_solicitante = $formulario->evaluado_id;
                    $model->argumento = "<b>" . $evaluado->name . '</b>: ' . $datos['argumentoAsesor'];
                    $model->s_fecha = date('Y-m-d H:i:s');
                    //ID DE CASO, UN NUMERO UNICO E IRREPETIBLE PUEDE SER LA FECHA
                    $model->id_caso = $modelForm->id_caso;
                    $model->estado_sc = $datos['estado_sc'];
                    $model->b_segundo_envio = 1;
                    $model->b_editar = 0;
                    if (count($liderEvaluador) > 0) {
                        $model->id_responsable = $liderEvaluador[0]['resp'];
                        $model->save();
                    } else {
                        $msg = \Yii::t('app', 'No se encuentra el lider del evaluador asociado');
                        Yii::$app->session->setFlash('danger', $msg);
                    }
                    return ($historico == 0) ? ($this->redirect(['reportes/historicoformulariosamigo',
                                "evaluado_usuared" => base64_encode($evaluado->dsusuario_red)])) : true;
                }
            } else {
                $bandera = Yii::$app->request->get('bandera');
                $historico = Yii::$app->request->get('historico');
                $esLider = Yii::$app->request->get('esLider');                
                $arrayCadena = $modelCaso = [];
                if ($bandera == 0) {
                    return $this->renderAjax('create', [
                                'model' => $model,
                                'isAjax' => $isAjax,
                                'fid' => Yii::$app->request->get('id'),
                                'scid' => (isset($model->id_segundo_calificador)) ? $model->id_segundo_calificador : 0,
                                'bandera' => $bandera,
                                'historico' => $historico,
                                'esLider' => $esLider,
                                'arrayCadena' => $arrayCadena,
                                'modelCaso' => $modelCaso,
                    ]);
                } else {
                    $model = \app\models\SegundoCalificador::find()
                            ->where(['id_ejecucion_formulario' => Yii::$app->request->get('id')])
                            ->orderBy('id_segundo_calificador DESC')
                            ->one();
                    $modelCaso = \app\models\SegundoCalificador::find()
                                    ->select("argumento, s_fecha")
                                    ->where(['id_caso' => $model->id_caso])
                                    ->orderBy("id_segundo_calificador ASC")->all();
                    $arrayCadena = explode('<br>', $model->argumento);
                    return $this->renderAjax('create', [
                                'model' => $model,
                                'isAjax' => $isAjax,
                                'fid' => Yii::$app->request->get('id'),
                                'scid' => (isset($model->id_segundo_calificador)) ? $model->id_segundo_calificador : 0,
                                'bandera' => $bandera,
                                'historico' => $historico,
                                'esLider' => $esLider,
                                'arrayCadena' => $arrayCadena,
                                'modelCaso' => $modelCaso,
                    ]);
                }
            }
        }
    }

    /**
     * Updates an existing SegundoCalificador model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id) {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id_segundo_calificador]);
        } else {
            return $this->render('update', [
                        'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing SegundoCalificador model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id) {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the SegundoCalificador model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SegundoCalificador the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id) {
        if (($model = \app\models\SegundoCalificador::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }

    /**
     * FUNCION PARA MANDAR NOTIFICACIÓN A AMIGO INDICANDO DEL ESTADO DE LAS
     * SOLICITUDES DE SEGUNDO CALIFICADOR
     * 
     * @param type $notificacion
     * @param type $bandera_envio
     * 
     */
    public function llamarwsLiderAmigo($notificacion = null, $bandera_envio = null) {

        $modelLider = \app\models\Usuarios::findOne(["usua_id" => $notificacion->id_responsable]);
        $modelEvaluado = \app\models\Evaluados::findOne(["id" => $notificacion->id_solicitante]);
        $params = [];
        $params['titulo'] = ($bandera_envio) ?
                'Te han remitido una solicitud de segundo calificador' :
                'Tienes una actualización en tu solicitud de segundo calificador';
        $params['pcrc'] = '';
        $params['descripcion'] = '';
        $params['notificacion'] = 'SI';
        $params['muro'] = 'NO';
        $params['usuariored'] = ($bandera_envio) ? $modelLider->usua_usuario : $modelEvaluado->dsusuario_red;
        $params['cedula'] = '';
        $params['plataforma'] = 'QA';
        $params['url'] = '' . Url::to(['site/showestadonotificacion',
                    'notificacion' => base64_encode($notificacion->id_segundo_calificador),
                    'usuario' => base64_encode(($bandera_envio) ? $modelLider->usua_usuario : $modelEvaluado->dsusuario_red)], true);
        $webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
        if (!$webservicesresponse) {
            Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));
            Yii::$app->session->setFlash('danger', print_r($params, true));
        }
    }

    public function actionShowestadonotificacion() {
        $usuario_red = Yii::$app->request->get('usuario');
        $idnotificacion = Yii::$app->request->get('notificacion');
        $modelEvaluado = \app\models\Evaluados::findOne(['dsusuario_red' => base64_decode($usuario_red)]);
        $model = \app\models\SegundoCalificador::find()->where(['id_segundo_calificador' => base64_decode($idnotificacion),
                    'id_solicitante' => $modelEvaluado->id])->one();
        if (!isset($model) || !isset($modelEvaluado)) {
            $msg = \Yii::t('app', 'No se recibió o no existe un asesor para poder realizar la consulta');
            Yii::$app->session->setFlash('danger', $msg);
            $arrayCadena = $modelCaso = $formulario = $modelEvaluado = [];
            $dimension = $nmLider = $nmEvaluador = "";
            $model = new \app\models\SegundoCalificador();
        } else {
            $arrayCadena = explode('<br>', $model->argumento);
            $modelCaso = \app\models\SegundoCalificador::find()
                            ->select("argumento, s_fecha")
                            ->where(['id_caso' => $model->id_caso])
                            ->orderBy("id_segundo_calificador ASC")->all();
            //INFO FORMULARIO
            $formulario = \app\models\Ejecucionformularios::find()
                    ->where(['id' => $model->id_ejecucion_formulario])
                    ->one();
            //INFORMACION ADICIONAL            
            $dimension = \app\models\Dimensiones::findOne($formulario->dimension_id);
            $dimension = $dimension->name;
            $Lider = \app\models\Usuarios::findOne($formulario->usua_id_lider);
            $nmLider = $Lider->usua_nombre;
            $Evaluador = \app\models\Usuarios::findOne($formulario->usua_id);
            $nmEvaluador = $Evaluador->usua_nombre;
        }
        return $this->render('_viewnotificacionscAmigo', [
                    'model' => $model,
                    'arrayCadena' => $arrayCadena,
                    'modelCaso' => $modelCaso,
                    'formulario' => $formulario,
                    'modelEvaluado' => $modelEvaluado,                    
                    'dimension' => $dimension,
                    'nmLider' => $nmLider,
                    'nmEvaluador' => $nmEvaluador,
        ]);
    }

}
