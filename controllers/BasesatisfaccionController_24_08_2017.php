<?php

namespace app\controllers;

use Yii;
use app\models\BaseSatisfaccion;
use app\models\BaseSatisfaccionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\Formularios;

/**
 * BaseSatisfaccionController implements the CRUD actions for BaseSatisfaccion model.
 */
class BasesatisfaccionController extends Controller {

    /**
     * Setiar acción para el Webservice de ingreso de baseinicial     
     */
    public function actions() {
        return [

            'baseinicial' => [
                'class' => 'mongosoft\soapserver\Action',
            ],
        ];
    }

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
                    $msg = \Yii::t('app', 'The requested Item could not be found.');
                    Yii::$app->session->setFlash('danger', $msg);
                    $url = \yii\helpers\Url::to(['site/index']);
                    return $action->controller->redirect($url);
                },
                        'rules' => [
                            [
                                'actions' => ['create', 'delete', 'eliminarencuesta',
                                    'encuestatelefonica', 'usuariolist', 'view',
                                    'formulariogestionsatisfaccion', 'getarbolesbypcrc',
                                    'guardarencuesta', 'index', 'reglanegocio',
                                    'showencuestatelefonica', 'update', 'guardarformulario', 'showsubtipif', 'cancelarformulario',
                                    'reabrirformulariogestionsatisfaccion', 'clientebasesatisfaccion', 'limpiarfiltro', 'buscarllamadas', 'showformulariogestion',
                                    'guardaryenviarformulariogestion', 'eliminartmpform', 'buscarllamadasmasivas', 'recalculartipologia','consultarcalificacionsubi', 'metricalistmultipleform'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isHacerMonitoreo();
                        },
                            ],						
							[
                                'actions' => ['getarbolesbypcrc'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isReportes();
                        },
                            ],
                            [
                                'actions' => ['inboxaleatorio', 'buscarllamadasmasivas'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isVerInboxAleatorio();
                        },
                            ],
                            [
                                'actions' => ['baseinicial', 'showencuestaamigo', 'clientebasesatisfaccion',
                                    'showformulariogestionamigo',
                                    'controlinboxaleatorio'],
                                'allow' => true,
                            //'roles' => ['?'],
                            ],
                        ],
                    ],
                ];
            }

            /**
             * Lists all BaseSatisfaccion models.
             * @return mixed
             */
            public function actionIndex() {

                $searchModel = new BaseSatisfaccionSearch();
                $dataProvider = $searchModel->searchGestion();

                Yii::$app->session['iboxPage'] = Yii::$app->request->url;

                if (isset(Yii::$app->session['searchInbox'])) {
                    $searchModel->load(Yii::$app->session['searchInbox']);
                    $dataProvider = $searchModel->searchGestion();
                }
                if (Yii::$app->request->get('page')) {
                    $searchModel->load(Yii::$app->session['searchInbox']);
                    $dataProvider = $searchModel->searchGestion();
                }

                if ($searchModel->load(Yii::$app->request->post())) {
                    $dataProvider = $searchModel->searchGestion();
                    Yii::$app->session['searchInbox'] = Yii::$app->request->post();
                }

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                ]);
            }

            /**
             * Lists all BaseSatisfaccion models.
             * @return mixed
             */
            public function actionInboxaleatorio() {
                $searchModel = new BaseSatisfaccionSearch();
                $dataProvider = $searchModel->searchGestion("ALEATORIO");

                Yii::$app->session['iboxPage'] = Yii::$app->request->url;

                if (isset(Yii::$app->session['searchInboxA'])) {
                    $searchModel->load(Yii::$app->session['searchInboxA']);
                    $dataProvider = $searchModel->searchGestion("ALEATORIO");
                }
                if (Yii::$app->request->get('page')) {
                    $searchModel->load(Yii::$app->session['searchInboxA']);
                    $dataProvider = $searchModel->searchGestion("ALEATORIO");
                }

                if ($searchModel->load(Yii::$app->request->post())) {
                    $dataProvider = $searchModel->searchGestion("ALEATORIO");
                    Yii::$app->session['searchInboxA'] = Yii::$app->request->post();
                }

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'aleatorio' => true,
                ]);
            }

            /**
             * Acción para limpiar el filtro de búsqueda
             */
            public function actionLimpiarfiltro($aleatorio = false) {
                if ($aleatorio == "1") {
                    if (isset(Yii::$app->session['searchInboxA'])) {
                        $session = Yii::$app->session;
                        $session->remove('searchInboxA');
                    }
                    $this->redirect(['inboxaleatorio']);
                } else {
                    if (isset(Yii::$app->session['searchInbox'])) {
                        $session = Yii::$app->session;
                        $session->remove('searchInbox');
                    }
                    $this->redirect(['index']);
                }
            }

            /**
             * Displays a single BaseSatisfaccion model.
             * @param integer $id
             * @return mixed
             */
            public function actionView($id) {
                return $this->render('view', [
                            'model' => $this->findModel($id),
                ]);
            }

            /**
             * Creates a new BaseSatisfaccion model.
             * If creation is successful, the browser will be redirected to the 'view' page.
             * @return mixed
             */
            public function actionCreate() {
                $model = new BaseSatisfaccion();

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('create', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Updates an existing BaseSatisfaccion model.
             * If update is successful, the browser will be redirected to the 'view' page.
             * @param integer $id
             * @return mixed
             */
            public function actionUpdate($id) {
                $model = $this->findModel($id);

                if ($model->load(Yii::$app->request->post()) && $model->save()) {
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    return $this->render('update', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Deletes an existing BaseSatisfaccion model.
             * If deletion is successful, the browser will be redirected to the 'index' page.
             * @param integer $id
             * @return mixed
             */
            public function actionDelete($id) {
                $model = $this->findModel($id);
                $redct = ($model->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
                $msg = "";
                try {/*
                  \app\models\Ejecucionfeedbacks::deleteAll(["basessatisfaccion_id" => $model->id]);
                  \app\models\Tmpejecucionfeedbacks::deleteAll(["basessatisfaccion_id" => $model->id]);
                  \app\models\RespuestaBasesatisfaccionSubtipificacion::deleteAll(["id_basesatisfaccion" => $model->id]);
                  \app\models\RespuestaBasesatisfaccionTipificacion::deleteAll(["id_basesatisfaccion" => $model->id]);
                  \app\models\RespuestaBaseSatisfaccion::deleteAll(["id_basesatisfaccion" => $model->id]); */
                    $ejecucion = \app\models\Ejecucionformularios::findOne(['basesatisfaccion_id' => $model->id]);
                    if (!is_null($ejecucion)) {
                        $ejecucion->delete();
                    }
                    $msg = \Yii::t('app', 'Encuesta Eliminada con éxito: ' . $model->id);
                    $model->delete();
                } catch (Exception $exc) {
                    $msg = \Yii::t('app', 'Error al realizar la operación: ' . $model->id);
                }
                Yii::$app->session->setFlash('danger', $msg);
                return $this->redirect([$redct]);
            }

            /**
             * Finds the BaseSatisfaccion model based on its primary key value.
             * If the model is not found, a 404 HTTP exception will be thrown.
             * @param integer $id
             * @return BaseSatisfaccion the loaded model
             * @throws NotFoundHttpException if the model cannot be found
             */
            protected function findModel($id) {
                if (($model = BaseSatisfaccion::findOne($id)) !== null) {
                    return $model;
                } else {
                    throw new NotFoundHttpException('The requested page does not exist.');
                }
            }

            /**
             * Obtiene el listado de arboles dependiente del rol
             * @param type $search
             * @param type $id
             */
            public function actionGetarbolesbypcrc($search = null, $id = null) {

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
             * Metodo que permite visualizar las vista para llenar los campos requeridos de la encuesta telefonica
             * @return array
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionEncuestatelefonica() {
                $model = new BaseSatisfaccion();
                $model->scenario = 'encuestamanual';
                if ($model->load(Yii::$app->request->post())) {
                    $modelEvaluado = \app\models\Evaluados::findOne($model->agente);
                    /* $modelRN = \app\models\Reglanegocio::findOne(["rn" => $model->rn, "cod_institucion" => $model->institucion,
                      "cod_industria" => $model->industria]); */
                    $modelRN = \app\models\Reglanegocio::findOne(["pcrc" => $model->pcrc]);
                    if (!isset($modelRN)) {
                        $msg = \Yii::t('app', 'error telephone survey');
                        Yii::$app->session->setFlash('danger', $msg);
                        return $this->render('encuestatelefonica', [
                                    'model' => $model,
                        ]);
                    }
                    $model->agente = $modelEvaluado->dsusuario_red;
                    $model->rn = $modelRN->rn;
                    $model->institucion = (string) $modelRN->cod_institucion;
                    $model->industria = (string) $modelRN->cod_industria;
                    //$model->agente = Yii::$app->user->identity->username;
                    $model->ano = date("Y");
                    $model->mes = date("n");
                    $model->dia = date("j");
                    $model->hora = date("His");
                    $model->pcrc = $modelRN->pcrc;
                    $model->cliente = $modelRN->cliente;
                    $model->connid = '123abc';
                    $model->created = date('Y-m-d H:i:s');
                    $model->fecha_satu = date('Y-m-d H:i:s');
                    $model->save();
                    return $this->redirect([
                                "showencuestatelefonica",
                                "id" => $model->id]);
                } else {
                    return $this->render('encuestatelefonica', [
                                'model' => $model,
                    ]);
                }
            }

            /**
             * Metodo que permite cargar la vista de la encuesta 
             * @return array
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowencuestatelefonica($id) {
                //DATOS QUE SERAN ENVIADOS AL FORMULARIO

                $model = \app\models\BaseSatisfaccion::findOne($id);
                $modelRN = \app\models\Reglanegocio::findOne(["rn" => $model->rn, "cod_industria" => $model->industria,
                            "cod_institucion" => $model->institucion]);
                $data = new \stdClass();
                $data->datoFormulario = \app\models\Formularios::findOne(Yii::$app->params["IdFormEncuestaTele"]);
                $data->datoSeccion = \app\models\Seccions::getSeccionsByFormulario($data->datoFormulario->id);
                $data->datoBloque = \app\models\Bloques::getBloqueBySeccion($data->datoSeccion[0]->id);
                $data->datoBloqueDetalle = \app\models\Bloquedetalles::getBloqueDetaByBloque($data->datoBloque[0]->id);
                $data->preguntas = \app\models\Preguntas::find()->select('pre_indicador,categoria,enunciado_pre,id_parametrizacion')
                                ->join('INNER JOIN', 'tbl_parametrizacion_encuesta', 'id_parametrizacion = tbl_parametrizacion_encuesta.id')
                                ->where(["cliente" => $modelRN->cliente, "programa" => $modelRN->pcrc])->all();
                foreach ($data->datoBloqueDetalle as $value) {
                    $value->calificaciones = \app\models\Calificacions::find()->select('tbl_calificaciondetalles.id,tbl_calificaciondetalles.name')
                                    ->join('INNER JOIN', 'tbl_calificaciondetalles', 'tbl_calificacions.id = tbl_calificaciondetalles.calificacion_id')
                                    ->where('tbl_calificacions.id = ' . $value->calificacion_id)->asArray()->all();
                }
                return $this->render('show-encuesta', [
                            'model' => $model,
                            'data' => $data
                ]);
            }

            /**
             * Metodo que retorna el rn de la tabla Reglas de negocio
             * @return array
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionReglanegocio($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }
                $out = ['more' => true];
                if (!is_null($search)) {
                    $data = \app\models\Reglanegocio::find()
                            ->select(['id' => 'rn', 'text' => 'UPPER(rn)'])
                            ->where('rn LIKE "%' . $search . '%"')
                            ->orderBy('rn')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Reglanegocio::find()
                            ->select(['id' => 'rn', 'text' => 'UPPER(rn)'])
                            ->where('rn = ' . $id)
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Metodo que permite guarda la encuesta telefonica diligenciada
             * @return array
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGuardarencuesta($id) {
                $model = \app\models\BaseSatisfaccion::findOne($id);
                if (Yii::$app->request->post()) {
                    $datosForm = Yii::$app->request->post();
                    foreach ($datosForm as $key => $value) {
                        $model->$key = $value;
                    }
                    Yii::$app->session->setFlash('success', Yii::t('app', 'guardado encuesta'));
                    $model->comentario = $datosForm['comentario'];


                    //Consulta y modificacion de tipologia -----------------------------
                    $model->tipo_servicio = 'telefónico';
                    $model->tipologia = 'NEUTRO';
                    \Yii::error($model->pcrc0->name, 'basesatisfaccion');
                    if (!empty($model->pcrc) && !empty($model->cliente)) {
                        $sql = '
        SELECT ca.nombre, dp.categoria, p.pre_indicador, 
            dp.configuracion, dp.addNA, cg.name, cg.prioridad, cg.id
        FROM tbl_detalleparametrizacion dp
        JOIN tbl_categoriagestion cg 
            ON dp.id_categoriagestion = cg.id
        JOIN tbl_parametrizacion_encuesta pe 
            ON pe.id = cg.id_parametrizacion
        LEFT JOIN tbl_preguntas p 
            ON p.id_parametrizacion = pe.id 
            AND p.categoria = dp.categoria
        JOIN tbl_categorias ca ON ca.id = dp.categoria 
        WHERE pe.cliente = ' . $model->cliente
                                . ' AND pe.programa = ' . $model->pcrc;
                        $config = \Yii::$app->db->createCommand($sql)->queryAll();

                        $prioridades = ArrayHelper::map($config, 'prioridad', 'name');
                        $arrayCumpleRegla = [];

                        if (count($config) > 0) {
                            $conditon = '';
                            $comando = '';
                            $i = 1;
                            $errorConfig = false;
                            //Validamos si hay una mala configuracion-------------------                    
                            foreach ($config as $value) {
                                if (is_null($value['pre_indicador']) || empty($value['pre_indicador'])) {
                                    $errorConfig = true;
                                    \Yii::error('Categoria (' . $value['nombre']
                                            . '), Cliente(' . $model->cliente0->name
                                            . ') Programa(' . $model->pcrc0->name
                                            . ')  mal configuada, Por favor revise la '
                                            . 'configuracion', 'basesatisfaccion');
                                }
                            }

                            if (!$errorConfig) {
                                foreach ($config as $key => $value) {
                                    if (!empty($value['configuracion'])) {
                                        $preExplode = explode('-', $value['configuracion']);
                                        $explode = explode('||', $preExplode[0]);
                                        if (isset($explode[0]) && isset($explode[1]) && isset($explode[2])) {

                                            if (str_replace(['(', ')'], '', $explode[0]) == BaseSatisfaccion::OP_AND) {
                                                if (isset($preExplode[1])) {
                                                    $explodeAddNA = explode('||', $preExplode[1]);
                                                    $tmpConditon = ' && ($model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explode[1]
                                                            . ' "'
                                                            . $explode[2] . '" ';
                                                    $tmpConditon .= ' || $model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explodeAddNA[1]
                                                            . ' "'
                                                            . $explodeAddNA[2] . '" )';
                                                } else {
                                                    $tmpConditon = ' && (is_numeric($model->' . $value['pre_indicador'] . ') && $model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explode[1]
                                                            . ' "'
                                                            . $explode[2] . '") ';
                                                }
                                            } else {
                                                $tmpConditon = ' || $model->'
                                                        . $value['pre_indicador']
                                                        . ' '
                                                        . $explode[1]
                                                        . ' "'
                                                        . $explode[2] . '" ';
                                            }

                                            if (!isset($config[$i]['id']) || $value['id'] != $config[$i]['id']) {
                                                $conditon = substr($conditon, 4);
												
												
                                                if (!$conditon) {
                                                    $tmpConditon = substr($tmpConditon, 4);
                                                }
                                                $eval = '('
                                                        . $conditon
                                                        . $tmpConditon
                                                        . ')';
                                                $conditon = '';				
                                                
												
												$comando .= '#####' . $eval;
                                                eval("\$restCond = $eval;");
                                                if ($restCond) {
                                                    $arrayCumpleRegla[] = 'true';
                                                    $model->tipologia = $value['name'];
                                                    //$nModel->tipologia = $value['name'];
                                                } else {
                                                    $arrayCumpleRegla[] = 'false';
                                                }
                                            } else {
                                                $conditon .= $tmpConditon;
                                            }
                                        }
                                    }
                                    $i++;
                                }

                                $contarValores = array_count_values($arrayCumpleRegla);

                                //Contamos el numero de true en $arrayCumpleRegla---------------
                                if (isset($contarValores['true']) && $contarValores['true'] > 1) {
                                    //sacamos el que tenga prioridad mas alta ------------------                    
                                    $model->tipologia = $prioridades[min(array_keys($prioridades))];
                                }
                            }
                        }
                    }
                    $nModel = $model;
                    $this->enviarwebservice($nModel);
                    $model->save();
                    return $this->redirect([
                                "encuestatelefonica"]);
                }
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
                            ->select(['id' => 'tbl_usuarios.usua_usuario', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_nombre LIKE "%' . $search . '%"')
                            ->orderBy('usua_nombre')
                            ->asArray()
                            ->all();
                    //agrego el usuario no definido solo para la visualizacion  en la inbox
                    $data[] = ['id' => '1', 'text' => 'NO DEFINIDO'];
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_usuario', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_usuario = "' . $id . '"')
                            ->asArray()
                            ->all();
                    //agrego el usuario no definido solo para la visualizacion  en la inbox
                    $data[] = ['id' => '1', 'text' => 'NO DEFINIDO'];
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Acción SOAP para guardar la baseinicial
             * 
             * @param array $datos datos de la baseinicial
             * @return array respuesta entregada [codigo, mensaje]
             * 
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$             
             */
            public function insertBasesatisfaccion($datos) {
                if (empty($datos) || count($datos) < 1) {
                    return[
                        "codigo" => -1,
                        "mensaje" => "Debe ingresar datos de entrada"
                    ];
                }
                $model = new BaseSatisfaccion();
                $model->scenario = 'webservice';
                //INGRESO LOS DATOS QUE ME LLEGARON POR EL IVR EN EL MODELO DE SATISFACCION
                foreach ($datos as $key => $value) {
                    $model->$key = $value;
                }
                //BUSCO LA REGLA DE NEGOCIO PARA SABER SI EXITE ANTES DE CREAR EL REGISTRO
                $sql = "SELECT `pcrc`,`cliente`, `encu_diarias`, `encu_mes`, rango_encuestas
        FROM `tbl_reglanegocio` AS R 
        WHERE `rn` = '" . $model->rn . "'
        AND `cod_industria` = " . $model->industria . " 
        AND `cod_institucion`= " . $model->institucion . "
        LIMIT 1;";
                $validRn = \Yii::$app->db->createCommand($sql)->queryAll();
                if (count($validRn) <= 0) {
                    $msj = "Error guardando los datos: ";
                    $msj .= "la regla de negocio '" . $model->rn . "' no se encuentra "
                            . "parametrizada en la aplicación.";
                    //ESCRIBO EN EL LOG
                    \Yii::error($msj, 'basesatisfaccion');
                    //RETORNO EL ERROR
                    return[
                        "codigo" => -1,
                        "mensaje" => $msj
                    ];
                }
                //ALEATORIO-----------------------------------------------------
                //RANGO ENCUESTA
                $rangoEncu = $validRn[0]['rango_encuestas'];

                //TRAIGO TOTAL DE ENCUESTAS ALEATORIAS DEL DIA
                $totBuzAleDia = BaseSatisfaccion::find()
                        ->select("id")
                        ->where([
                            'tipo_inbox' => 'ALEATORIO',
                            'pcrc' => $validRn[0]['pcrc'],
                            'dia' => date('d'),
                            'ano' => date('Y'),
                            'mes' => date('m')
                        ])
                        ->all();
                $totEncuAleDia = count($totBuzAleDia);

                //TRAIGO TOTAL DE ENCUESTAS DEL DIA SIN IMPORTAR INBOX
                $totBuzDia = BaseSatisfaccion::find()
                        ->select("id")
                        ->where([
                            'pcrc' => $validRn[0]['pcrc'],
                            'dia' => date('d'),
                            'ano' => date('Y'),
                            'mes' => date('m')
                        ])
                        ->all();
                $totEncuDia = count($totBuzDia);

                //TOTAL DE ENCUESTAS DEL MES
                $totAle = BaseSatisfaccion::find()
                        ->select("id")
                        ->where([
                            'tipo_inbox' => 'ALEATORIO',
                            'pcrc' => $validRn[0]['pcrc'],
                            'ano' => date('Y'),
                            'mes' => date('m')
                        ])
                        ->all();

                //SI ES MULTIPLO DEL RANGO DE HORAS Y MENOR Q EL LIMITE DEL DIA Y MES
                if ($rangoEncu > 0) {
                    if ((($totEncuDia + 1) % $rangoEncu == 0) &&
                            $totEncuAleDia < $validRn[0]['encu_diarias'] &&
                            count($totAle) < $validRn[0]['encu_mes'] &&
                            ($model->tipo_encuesta == '' ||
                            $model->tipo_encuesta == 'A')) {
                        $model->tipo_inbox = 'ALEATORIO';
                    }
                }

                //FIN ALEATORIO-------------------------------------------------
                $model->created = date('Y-m-d H:i:s');
                if (strlen($datos['hora']) <= 4) {
                    $horaSatu = '00:00:00';
                } else {
                    if (strlen($datos['hora']) % 2 == 0) {
                        $array = str_split($datos['hora'], 2);
                        $horaSatu = $array[0] . ":" . $array[1] . ":" . $array[2];
                    } else {
                        $array = str_split(substr($datos['hora'], 1, 4), 2);
                        $horaSatu = substr($datos['hora'], 0, 1) . ":" . $array[0] . ":" . $array[1];
                    }
                }

                $model->fecha_satu = $datos['ano'] . '-' . $datos['mes'] . '-' . $datos['dia'] . ' ' . $horaSatu;

                //VUELVO A VALIDAR EL CONNID
                $valConid = (int) BaseSatisfaccion::find()->where(["connid" => $datos['connid']])->count();
                if ($valConid > 0) {
                    $msg = "MENSAJE: El connid (" . $datos['connid'] . "), ya está registrado en el sistema";
                    return[
                        "codigo" => -1,
                        "mensaje" => $msg
                    ];
                }
                //GUARDO LOS DATOS


                if (!$model->save()) {
                    //SI HAY ERROR DEVUELVO LA RESPUESTA -1 CON LOS ERRORES
                    $msj = "Error guardando los datos: ";
                    foreach ($model->getErrors() as $key => $value) {
                        $msj .= $key . ": " . $value[0] . "<br />";
                    }
                    //ESCRIBO EN EL LOG
                    \Yii::error($msj, 'basesatisfaccion');
                    //RETORNO EL ERROR
                    return[
                        "codigo" => -1,
                        "mensaje" => $msj
                    ];
                } else {
                    $msj = "Registro creado con éxito";
                    //Consulta y modificacion de tipologia -----------------------------
                    $nModel = BaseSatisfaccion::findOne($model->id);
                    $nModel->tipologia = 'NEUTRO';

                    \Yii::error($nModel->pcrc0->name, 'basesatisfaccion');
                    if (!empty($nModel->pcrc) && !empty($nModel->cliente)) {
                        $sql = '
        SELECT ca.nombre, dp.categoria, p.pre_indicador, 
            dp.configuracion, dp.addNA, cg.name, cg.prioridad, cg.id
        FROM tbl_detalleparametrizacion dp
        JOIN tbl_categoriagestion cg 
            ON dp.id_categoriagestion = cg.id
        JOIN tbl_parametrizacion_encuesta pe 
            ON pe.id = cg.id_parametrizacion
        LEFT JOIN tbl_preguntas p 
            ON p.id_parametrizacion = pe.id 
            AND p.categoria = dp.categoria
        JOIN tbl_categorias ca ON ca.id = dp.categoria 
        WHERE pe.cliente = ' . $nModel->cliente
                                . ' AND pe.programa = ' . $nModel->pcrc;
                        $config = \Yii::$app->db->createCommand($sql)->queryAll();

                        $prioridades = ArrayHelper::map($config, 'prioridad', 'name');
                        $arrayCumpleRegla = $prioridadesReales = [];

                        if (count($config) > 0) {
                            $conditon = '';
                            $comando = '';
                            $i = 1;
                            $errorConfig = false;
                            //Validamos si hay una mala configuracion-------------------                    
                            foreach ($config as $value) {
                                if (is_null($value['pre_indicador']) || empty($value['pre_indicador'])) {
                                    $errorConfig = true;
                                    \Yii::error('Categoria (' . $value['nombre']
                                            . '), Cliente(' . $nModel->cliente0->name
                                            . ') Programa(' . $nModel->pcrc0->name
                                            . ')  mal configuada, Por favor revise la '
                                            . 'configuracion', 'basesatisfaccion');
                                }
                            }

                            if (!$errorConfig) {
                                foreach ($config as $key => $value) {
                                    if (!empty($value['configuracion'])) {
                                        $preExplode = explode('-', $value['configuracion']);
                                        $explode = explode('||', $preExplode[0]);
                                        if (isset($explode[0]) && isset($explode[1]) && isset($explode[2])) {

                                            if (str_replace(['(', ')'], '', $explode[0]) == BaseSatisfaccion::OP_AND) {
                                                if (isset($preExplode[1])) {
                                                    $explodeAddNA = explode('||', $preExplode[1]);
                                                    $tmpConditon = ' && ($model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explode[1]
                                                            . ' "'
                                                            . $explode[2] . '" ';
                                                    $tmpConditon .= ' || $model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explodeAddNA[1]
                                                            . ' "'
                                                            . $explodeAddNA[2] . '" )';
                                                } else {
                                                    $tmpConditon = ' && (is_numeric($model->' . $value['pre_indicador'] . ') && $model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explode[1]
                                                            . ' "'
                                                            . $explode[2] . '") ';
                                                }
                                            } else {
                                                $tmpConditon = ' || $model->'
                                                        . $value['pre_indicador']
                                                        . ' '
                                                        . $explode[1]
                                                        . ' "'
                                                        . $explode[2] . '" ';
                                            }

                                            if (!isset($config[$i]['id']) || $value['id'] != $config[$i]['id']) {
                                                $conditon = substr($conditon, 4);
                                                if (!$conditon) {
                                                    $tmpConditon = substr($tmpConditon, 4);
                                                }
                                                $eval = '('
                                                        . $conditon
                                                        . $tmpConditon
                                                        . ')';
                                                $conditon = '';
                                                $comando .= '#####' . $eval;
                                                eval("\$restCond = $eval;");
                                                if ($restCond) {
													$prioridadesReales[$value['prioridad']] = $value['name'];
                                                    $arrayCumpleRegla[] = 'true';
                                                    $nModel->tipologia = $value['name'];
                                                } else {
                                                    $arrayCumpleRegla[] = 'false';
                                                }
                                            } else {
                                                $conditon .= $tmpConditon;
                                            }
                                        }
                                    }
                                    $i++;
                                }


                                $contarValores = array_count_values($arrayCumpleRegla);

                                //Contamos el numero de true en $arrayCumpleRegla---------------
                                if (isset($contarValores['true']) && $contarValores['true'] > 1) {
                                    //sacamos el que tenga prioridad mas alta ------------------                    
                                    //$nModel->tipologia = $prioridades[min(array_keys($prioridades))];
                                    $nModel->tipologia = $prioridadesReales[min(array_keys($prioridadesReales))];
                                }
                            }
                        }

                        $this->enviarwebservice($nModel);

                        // if($nModel->tipologia == 'CRITICA PENALIZABLE'){
                        //     //Enviar al lider y al asesor
                        //     $nModel->agente
                        // }else if($nModel->tipologia == 'FELICITACION'){
                        //     $params = [];
                        //     $params['titulo'] = 'tienes una nueva encuesta con ' . $nModel->tipologia;
                        //     $params['pcrc'] = '';
                        //     $params['descripcion'] = '';
                        //     $params['notificacion'] = 'SI';
                        //     $params['muro'] = 'NO';
                        //     $params['usuariored'] = $nModel->agente;
                        //     $params['cedula'] = '';
                        //     $params['plataforma'] = 'QA';
                        //     $params['url'] = '' . Url::to(['formularios/showformulariodiligenciadoamigo']) . '?form_id=' . base64_encode($nModel->id);
                        //     $webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
                        //     $tmp_basesatisfaccion = $nModel->id;
                        //     if (!$webservicesresponse && $tmp_basesatisfaccion == '') {
                              
                             
                        //     Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));                  
                        //     }
                        // }

                        /* INICIO WEBSERVICE */


                        /* FIN WEBSERVICE */ 

                         //echo "<pre>";
                         //print_r($nModel->tipologia); 
                         //print_r($nModel->agente); die;
                    }
/* DESCOMENTAR HASTA LINEA 937
                    //Consulta de llamadas ----------------------------------------------
                    //Buscamos en medellin----------------------------------------------
                    $formularios = new \app\models\Formularios;

                    $server = Yii::$app->params["server"];
                    $user = Yii::$app->params["user"];
                    $pass = Yii::$app->params["pass"];
                    $db = Yii::$app->params["db"];
                    //print_r($nModel->connid ."serv". $server ."user". $user ."pass". $pass ."db". $db); die;
                    $idRel = $this->_consultDB($nModel->connid, $server, $user, $pass, $db);
                    $arrayLlamada = "";
                    if (!$idRel) {

                        //CONSULTA EN BD BOGOTA --------------------------------------------
                        $server = Yii::$app->params["serverBog"];
                        $user = Yii::$app->params["userBog"];
                        $pass = Yii::$app->params["passBog"];
                        $db = Yii::$app->params["dbBog"];

                        $idRel = $this->_consultDB($nModel->connid, $server, $user, $pass, $db);

                        if (is_numeric($idRel)) {
                            $wsdl = \Yii::$app->params["wsdl_redbox_bogota"];
                            $arrayLlamada = $formularios->getDataWS($idRel, $wsdl);
                        }
                    } else {
                        $wsdl = \Yii::$app->params["wsdl_redbox"];
                        $arrayLlamada = $formularios->getDataWS($idRel, $wsdl);
                    }

                    //Gaurdamos la llamada ---------------------------------------------
                    $nModel->llamada = (is_array($arrayLlamada) && count($arrayLlamada) >
                            0) ? json_encode($arrayLlamada) : null;
                    //Consulta de buzon ------------------------------------------------
                    $nModel->buzon = $this->_buscarArchivoBuzon(
                            sprintf("%02s", $nModel->dia) . "_" . sprintf("%02s", $nModel->mes) . "_" . $nModel->ano, $nModel->connid);
*/
                    //GUARDAMOS LOS DATOS-----------------------------------------------
                    if (!$nModel->save()) {
                        \Yii::error($msj, 'basesatisfaccion');
                        $error = "Error guardando los datos: ";
                        foreach ($nModel->getErrors() as $key => $value) {
                            $error .= $key . ": " . $value[0] . "<br />";
                        }
                        //ESCRIBO EN EL LOG
                        \Yii::error($error, 'basesatisfaccion');
                    }
                    //------------------------------------------------------------------            


                    return[
                        "codigo" => 1,
                        "mensaje" => $msj
                    ];
                }
            }



            public function enviarwebservice($nModel='')
            {

                $usuario = \app\models\Usuarios::findOne(['usua_id' => $nModel->id_lider_equipo]);
                //print_r($usuario->usua_usuario); die;
                $enviararray= array();
                array_push($enviararray, $nModel->agente);

                if($nModel->tipologia != 'NEUTRO'){
                    array_push($enviararray, $usuario->usua_usuario);
                    foreach ($enviararray as $value) {
                        if($value == $nModel->agente && $nModel->tipologia == 'CRITICA PENALIZABLE' || $nModel->tipologia == 'CRITICA' || $nModel->tipologia == 'CRITICA POR BUZÓN'){
                            $titulo = '¡Atención! Un cliente no quedó satisfecho en una de tus relaciones.';
                        }else if($value == $nModel->agente){
                            $titulo = '¡Qué bien! Un cliente quedó satisfecho en una de tus relaciones.';
                        }

                        if($value == $usuario->usua_usuario && $nModel->tipologia == 'CRITICA PENALIZABLE' || $nModel->tipologia == 'CRITICA' || $nModel->tipologia == 'CRITICA POR BUZÓN'){
                            $titulo = '¡Atención! Un cliente no quedó satisfecho en una de las relaciones de tu equipo.';
                        }else if($value == $usuario->usua_usuario){
                            $titulo = '¡Qué bien! En una de las relaciones de tu equipo se generó satisfacción.';
                        }
                        $params = [];
                        $params['titulo'] = $titulo;
                        $params['pcrc'] = '';
                        $params['descripcion'] = '';
                        $params['notificacion'] = 'SI';
                        $params['muro'] = 'NO';
                        $params['usuariored'] = $value;
                        $params['cedula'] = '';
                        $params['plataforma'] = 'QA';
                        $params['url'] = 'http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/showencuestaamigo?form_id=' . base64_encode($nModel->id);
                        $webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
                        $tmp_basesatisfaccion = $nModel->id;
                        if (!$webservicesresponse && $tmp_basesatisfaccion == '') {
                          
                         
                        Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));                  
                        }
                    }
                }
                // $enviara = array($enviararray);


            }



            /**
             * Metodo para mostrar el formulario diligenciado desde amigo
             * 
             * @param int $tmp_id
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowencuestaamigo($form_id) {

                //NUEVO LAYOUT
                // $this->layout = "formulario";
                $nuevo = new \stdClass();

                //DECODIFICTO EL FORMULARIO ID
                $id = base64_decode($form_id);
                $modelBase = BaseSatisfaccion::findOne(["id" => $id]);
                //echo "<pre>";
                //print_r($modelBase); die;

                $model = \app\models\BaseSatisfaccion::findOne($id);
                $nuevo->pcrc = \app\models\Arboles::findOne($model->pcrc);
                $nuevo->cliente = \app\models\Arboles::findOne($modelBase->cliente);
                $nuevo->evaluado = \app\models\Evaluados::findOne(["dsusuario_red" => trim($model->agente)]);

                $preguntas = \app\models\ParametrizacionEncuesta::find()->select("tbl_preguntas.id,tbl_preguntas.pre_indicador,tbl_preguntas.enunciado_pre,tbl_preguntas.categoria,tbl_categorias.nombre")
                                ->join("INNER JOIN", "tbl_preguntas", "tbl_parametrizacion_encuesta.id = tbl_preguntas.id_parametrizacion")
                                ->join("INNER JOIN", "tbl_categorias", "tbl_categorias.id = tbl_preguntas.categoria")
                                ->where(["cliente" => $modelBase->cliente, "programa" => $modelBase->pcrc])->asArray()->all();
                //echo "<pre>";
                //print_r($nuevo->dimension); die;
                
                return $this->render("showencuestamigo", ["data" => $model, "nuevo" => $nuevo, "preguntas" => $preguntas]);
            }


            /**
             * Metodo que permite cargar la vista del formulario de gestion
             * @return array
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionFormulariogestionsatisfaccion($id) {
                $model = BaseSatisfaccion::findOne(["id" => $id]);
                $redct = ($model->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
                /* if (Yii::$app->user->identity->isAdminSistema() && $model->estado == 'Cerrado') {
                  //MARCO EL REGISTRO COMO TOMADO
                  $model->estado = "Abierto";
                  $model->save();
                  } */
                if ($model->usado == "SI" && $model->responsable != Yii::$app->user->identity->username) {
                    return $this->redirect([$redct]);
                }
                $modelReglaNegocio = \app\models\Reglanegocio::find()->where(["pcrc" => $model->pcrc, "cliente" => $model->cliente, "cod_industria" => $model->industria, "cod_institucion" => $model->institucion])->asArray()->all();
                if (count($modelReglaNegocio) == 0) {
                    $msg = \Yii::t('app', 'Este registro no tiene una parametrización asociada:' . $model->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                $data = new \stdClass();
                $data->datoFormulario = \app\models\Formularios::findOne($modelReglaNegocio['0']['id_formulario']);
                $data->recategorizar = BaseSatisfaccion::getCategorias($model->id);
                $data->respuestas = \app\models\RespuestaBaseSatisfaccion::find()->where(["id_basesatisfaccion" => $model->id])->all();
                $data->respuestasTipificacion = \app\models\RespuestaBasesatisfaccionTipificacion::find()->where(["id_basesatisfaccion" => $model->id])->all();
                $data->respuestaSubtipificacion = \app\models\RespuestaBasesatisfaccionSubtipificacion::find()->where(["id_basesatisfaccion" => $model->id])->all();
                $data->selecBloque = \app\models\RespuestaBaseSatisfaccion::find()->where(["id_basesatisfaccion" => $model->id, "respuesta" => "on"])->all();
                $data->preguntas = \app\models\ParametrizacionEncuesta::find()->select("tbl_preguntas.id,tbl_preguntas.pre_indicador,tbl_preguntas.enunciado_pre,tbl_preguntas.categoria,tbl_categorias.nombre")
                                ->join("INNER JOIN", "tbl_preguntas", "tbl_parametrizacion_encuesta.id = tbl_preguntas.id_parametrizacion")
                                ->join("INNER JOIN", "tbl_categorias", "tbl_categorias.id = tbl_preguntas.categoria")
                                ->where(["cliente" => $model->cliente, "programa" => $model->pcrc])->asArray()->all();
                if ($model->estado == 'Cerrado') {
                    $data->bandera = false;
                } else {
                    $data->bandera = true;
                }
                if (empty($data->datoFormulario)) {
                    $msg = \Yii::t('app', 'No existe formulario asociada para esta gestión:' . $model->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->render("formulariogestionsatisfaccion", ["model" => $model, "data" => $data, "formulario" => false]);
                }
                $data->datoSeccion = \app\models\Seccions::getSeccionsByFormulario($data->datoFormulario->id);
                foreach ($data->datoSeccion as $seccion) {
                    $data->datoBloque[] = \app\models\Bloques::getBloqueBySeccion($seccion->id);
                }
                if (count($data->preguntas) == 0) {
                    $msg = \Yii::t('app', 'No existe parametrización asociada para este formulario:' . $model->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                if ($model->estado != 'Cerrado') {
                    //MARCO EL REGISTRO COMO TOMADO
                    $model->usado = "SI";
                    $model->responsable = Yii::$app->user->identity->username;
                    $model->save();
                }
                $data->datoBloque = call_user_func_array('array_merge', $data->datoBloque);
                foreach ($data->datoBloque as $bloque) {
                    $data->datoBloqueDetalle[] = \app\models\Bloquedetalles::getBloqueDetaByBloque($bloque->id);
                }
                foreach ($data->datoBloqueDetalle as $value) {
                    for ($index = 0; $index < count($value); $index++) {
                        $value[$index]->calificaciones = \app\models\Calificacions::find()->select('tbl_calificaciondetalles.id,tbl_calificaciondetalles.name,tbl_calificaciondetalles.sndespliega_tipificaciones')
                                        ->join('INNER JOIN', 'tbl_calificaciondetalles', 'tbl_calificacions.id = tbl_calificaciondetalles.calificacion_id')
                                        ->where('tbl_calificacions.id = ' . $value[$index]->calificacion_id)->asArray()->all();
                        if (!empty($value[$index]->tipificacion_id)) {
                            $value[$index]->tipificaciones = \app\models\Tipificaciones::find()->select('tbl_tipificaciondetalles.id,tbl_tipificaciondetalles.name,tbl_tipificaciondetalles.subtipificacion_id')
                                            ->join('INNER JOIN', 'tbl_tipificaciondetalles', 'tbl_tipificacions.id = tbl_tipificaciondetalles.tipificacion_id')
                                            ->where('tbl_tipificacions.id = ' . $value[$index]->tipificacion_id)->asArray()->all();
                        }
                    }
                }
                return $this->render("formulariogestionsatisfaccion", ["model" => $model, "data" => $data, "formulario" => true]);
            }

            public function actionReabrirformulariogestionsatisfaccion($id) {
                $model = BaseSatisfaccion::findOne(["id" => $id]);
                if (Yii::$app->user->identity->isAdminSistema() && $model->estado == 'Cerrado') {
                    //MARCO EL REGISTRO COMO TOMADO
                    $model->estado = "Abierto";
                    $model->save();
                }
                return $this->redirect(['basesatisfaccion/showformulariogestion',
                            'basesatisfaccion_id' => $model->id, 'preview' => 0, 'fill_values' => false, 'banderaescalado' => false]);
            }

            /**
             * Metodo que permite guardar el formulario de gestion
             * @return array
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGuardarformulario($id, $ajax) {
                $model = BaseSatisfaccion::findOne($id);
                $redct = ($model->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
                $modelReglaNegocio = \app\models\Reglanegocio::find()->where(["pcrc" => $model->pcrc, "cliente" => $model->cliente, "cod_industria" => $model->industria, "cod_institucion" => $model->institucion])->asArray()->all();
                $datoFormulario = \app\models\Formularios::findOne($modelReglaNegocio['0']['id_formulario']);
                $datoSeccion = \app\models\Seccions::getSeccionsByFormulario($datoFormulario->id);
                $modelRespuestaSubtipi = \app\models\RespuestaBasesatisfaccionSubtipificacion::deleteAll(["id_basesatisfaccion" => $id]);
                $modelRespuestaTipi = \app\models\RespuestaBasesatisfaccionTipificacion::deleteAll(["id_basesatisfaccion" => $id]);
                $modelRespuesta = \app\models\RespuestaBaseSatisfaccion::deleteAll(["id_basesatisfaccion" => $id]);
                $formulario = Yii::$app->request->post();
                unset($formulario['_csrf']);
                foreach ($formulario['bloque'] as $idBloque => $bloque) {
                    foreach ($bloque as $value) {
                        foreach ($value as $key => $bloquedetalle) {
                            if ($key != 'tipificaciones') {
                                $modelRespuesta = new \app\models\RespuestaBaseSatisfaccion();
                                $modelRespuesta->id_basesatisfaccion = $model->id;
                                $modelRespuesta->text_pregunta = "" . $key;
                                $modelRespuesta->respuesta = "" . $bloquedetalle;
                                $modelRespuesta->id_bloquedetalle = $idBloque;
                                $modelRespuesta->save();
                            } else {
                                foreach ($bloquedetalle as $tipif) {
                                    foreach ($tipif as $keytipifDetalle => $tipifDetalle) {
                                        if ($keytipifDetalle != 'subtipificaciones') {
                                            $modelRespuestaTipi = new \app\models\RespuestaBasesatisfaccionTipificacion();
                                            $modelRespuestaTipi->id_basesatisfaccion = $model->id;
                                            $modelRespuestaTipi->tipificacion_name = $tipifDetalle;
                                            $modelRespuestaTipi->tipificacion_id = $keytipifDetalle;
                                            $modelRespuestaTipi->id_respuesta = $modelRespuesta->id;
                                            $modelRespuestaTipi->save();
                                        } else {
                                            foreach ($tipifDetalle as $keySubtipi => $subtipif) {
                                                $modelRespuestaSubtipi = new \app\models\RespuestaBasesatisfaccionSubtipificacion();
                                                $modelRespuestaSubtipi->subtipificacion_id = $keySubtipi;
                                                $modelRespuestaSubtipi->subtificacion_name = "" . $subtipif;
                                                $modelRespuestaSubtipi->id_basesatisfaccion = $model->id;
                                                $modelRespuestaSubtipi->tipificacion_id = $modelRespuestaTipi->id;
                                                $modelRespuestaSubtipi->save();
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if (isset($formulario['despliega'])) {
                    foreach ($formulario['despliega'] as $idDespliega => $despliega) {
                        $modelRespuesta = new \app\models\RespuestaBaseSatisfaccion();
                        $modelRespuesta->id_basesatisfaccion = $model->id;
                        $modelRespuesta->text_pregunta = "" . $idDespliega;
                        $modelRespuesta->respuesta = "" . $despliega;
                        $modelRespuesta->save();
                    }
                }
                foreach ($datoSeccion as $seccion) {
                    if (isset($formulario['comentarioseccion_' . $seccion->id])) {
                        $modelRespuesta = new \app\models\RespuestaBaseSatisfaccion();
                        $modelRespuesta->id_basesatisfaccion = $model->id;
                        $modelRespuesta->text_pregunta = "comentarioseccion_" . $seccion->id;
                        $modelRespuesta->respuesta = "" . $formulario['comentarioseccion_' . $seccion->id];
                        $modelRespuesta->save();
                    }
                }
                $model->usado = "NO";
                $model->estado = $formulario['estado'];
                $model->tipologia = $formulario['categoria'];
                $model->comentario = (isset($formulario['comentario'])) ? $formulario['comentario'] : '';
                $model->save();
                if (!$ajax) {
                    $msg = \Yii::t('app', 'Formulario guardada satisfactoriamente');
                    Yii::$app->session->setFlash('success', $msg);
                    return $this->redirect([$redct]);
                }
            }

            /**
             * Metodo para consultar en SQlL las llamadas 
             * 
             * @param type $connId
             * @param type $server
             * @param type $user
             * @param type $pass
             * @param type $db
             * 
             * @return boolean
             */
            private function _consultDB($connId, $server, $user, $pass, $db) {
                if (!empty($connId) && !empty($server) && !empty($user) && !empty($pass) && !empty($db)) {
                    try {
                        $table = "Llamada" . date('Ym');
                        $numDias = \Yii::$app->params["dias_llamadas"];

                        $dia = date('d');
                        $endDate = date('Y-m-d');
                        if ($dia > 4) {
                            $startDate = date('Y-m-d', strtotime('-' . $numDias . ' day'));
                        } elseif ($dia == 2 || $dia == 3 || $dia == 4) {
                            $startDate = date('Y-m') . '-01';
                        } elseif ($dia == 1) {
                            $startDate = date('Y-m-d', strtotime('-' . $numDias . ' day'));
                            $table = "Llamada" . date('Ym', strtotime('-1 month'));
                        }

                        //CONECTO A AL SERVIDOR
                        $connection = mssql_connect($server, $user, $pass);

                        //SI NO HUBO CONEXION
                        if (!$connection) {
                            \Yii::error(__FILE__ . ':' . __LINE__
                                    . ': ##### Not connected : ' . mssql_get_last_message() . ' #####', 'redbox');
                            return false;
                        }

                        //SELECCIONO LA BASE DE DATOS
                        $db_selected = mssql_select_db($db, $connection);
                        if (!$db_selected) {
                            \Yii::error(__FILE__ . ':' . __LINE__
                                    . ': ##### Can\'t use db : ' . mssql_get_last_message() . ' #####', 'redbox');
                            return false;
                        }

                        //QUERY QUE ME RETORNA LOS DATOS
                        $query = "SELECT TOP 1 IdReLL FROM "
                                . $table . " WHERE Anotacion1 = '"
                                . $connId . "'";
                        $result = mssql_query($query);
                        $idRelF = mssql_fetch_array($result);
                        $idRel = $idRelF[0];

                        if (!is_null($idRel)) {
                            return $idRel;
                        } else {
                            \Yii::error(__FILE__ . ':' . __LINE__
                                    . ': ##### ConnId (' . $connId . ') no encontrado '
                                    . 'en Tabla SQL '
                                    . $table . ' #####', 'redbox');
                            return false;
                        }
                    } catch (\yii\base\Exception $exc) {
                        \Yii::error('#####' . __FILE__ . ':' . __LINE__
                                . $exc->getMessage() . '#####', 'redbox');
                        return false;
                    } catch (\PDOException $exc) {
                        \Yii::error('#####' . __FILE__ . ':' . __LINE__
                                . $exc->getMessage() . '#####', 'redbox');
                        return false;
                    }
                }
            }

            /**
             * Metodo que retorna el html con las subtipificaciones
             * 
             * @param int $id_detalle
             * @param int $id_tipificacion
             * @param int $preview
             * @return html
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowsubtipif($id_detalle, $id_tipificacion_padre, $preview = 0, $id = 0, $id_bloque = 0, $id_tipif, $bandera) {
                $subtipificaciones = \app\models\Tipificaciones::find()->select('tbl_tipificaciondetalles.id,tbl_tipificaciondetalles.name,tbl_tipificaciondetalles.subtipificacion_id')
                                ->join('INNER JOIN', 'tbl_tipificaciondetalles', 'tbl_tipificacions.id = tbl_tipificaciondetalles.tipificacion_id')
                                ->where('tbl_tipificacions.id = ' . $id_tipificacion_padre)->asArray()->all();
                $respuestas = \app\models\RespuestaBasesatisfaccionSubtipificacion::find()->where(["id_basesatisfaccion" => $id])->all();
                $html = '';
                foreach ($subtipificaciones as $objTipif) {
                    $checked = '';
                    $disabled = '';
                    if ($preview == 1) {
                        foreach ($respuestas as $key => $value) {
                            if ($objTipif['id'] == $value->subtipificacion_id) {
                                $checked = 'checked="checked"';
                            }
                            if ($bandera == 0) {
                                $disabled = 'disabled="disabled"';
                            }
                        }
                        $html.= '&nbsp;&nbsp;&nbsp;<input ' . $checked . ' ' . $disabled . ' '
                                . 'name="bloque[' . $id_bloque . '][' . $id_detalle . '][tipificaciones][' . $id_tipif . '][subtipificaciones][' . $objTipif["id"] . ']" '
                                . 'type="checkbox" '
                                . 'value="' . $objTipif["name"] . '">'
                                . '&nbsp;' . $objTipif["name"] . '<br/>';
                    } else {
                        $html.= '&nbsp;&nbsp;&nbsp;<input ' . $checked . ' ' . $disabled . ' '
                                . 'name="bloque[' . $id_bloque . '][' . $id_detalle . '][tipificaciones][' . $id_tipif . '][subtipificaciones][' . $objTipif["id"] . ']" '
                                . 'type="checkbox" '
                                . 'value="' . $objTipif["name"] . '">'
                                . '&nbsp;' . $objTipif["name"] . '<br/>';
                    }
                }
                echo $html;
            }

            /**
             * Acción para cerrar una gestión sin necesidad de diligenciarla
             * 
             * @param int $id datos de la baseinicial
             * 
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionCancelarformulario($id, $tmp_form = null) {

                $model = \app\models\BaseSatisfaccion::findOne($id);
                $redct = ($model->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
                if (Yii::$app->user->identity->username == $model->responsable) {
                    $model->usado = "NO";
                    $model->save();
                }
                if (!is_null($tmp_form)) {
                    \app\models\Tmpejecucionformularios::deleteAll(["id" => $tmp_form]);
                }
                return $this->redirect(Yii::$app->session['iboxPage']);
            }

            /**
             * 
             * Acción que sirve como cliente para consumir el servicio de base
             * 
             * @param string $ano
             * @param string $mes
             * @param string $dia
             * @param string $hora
             * @param string $ani
             * @param string $identificacion
             * @param string $nombre
             * @param string $agente
             * @param string $ext
             * @param string $rn
             * @param string $pregunta1
             * @param string $pregunta2
             * @param string $pregunta3
             * @param string $pregunta4
             * @param string $pregunta5
             * @param string $pregunta6
             * @param string $pregunta7
             * @param string $pregunta8
             * @param string $pregunta9
             * @param string $pregunta10
             * @param string $connid
             * @param string $industria
             * @param string $institucion
             * @throws \yii\web\HttpException
             */
            public function actionClientebasesatisfaccion($ano, $mes, $dia, $hora
            , $ani, $identificacion, $nombre, $agente, $ext, $rn
            , $pregunta1 = 'NO APLICA', $pregunta2 = 'NO APLICA', $pregunta3 = 'NO APLICA'
            , $pregunta4 = 'NO APLICA', $pregunta5 = 'NO APLICA'
            , $pregunta6 = 'NO APLICA', $pregunta7 = 'NO APLICA'
            , $pregunta8 = 'NO APLICA', $pregunta9 = 'NO APLICA'
            , $pregunta10 = 'NO APLICA', $connid, $industria, $institucion, $tipo_encuesta = '') {

                //LOG CON DATOS DE ENTRADA DEL IVR
                \Yii::warning("PARAMETROS INGRESADOS: " . print_r(func_get_args(), true), 'basesatisfaccion');

                $nm = str_replace("'", "", $nombre);
                //VALIDACIONES BÁSICAS DE LOS PARÁMETROS - FECHA NUMERICA
                if (!is_numeric($ano) || !is_numeric($mes) ||
                        !is_numeric($dia) || !is_numeric($hora)) {

                    $msg = "CODIGO: -1\r\n";
                    $msg .= "MENSAJE: La fecha y hora deben ser datos numéricos";
                    $this->setErrorSatu(print_r(func_get_args(), true), $msg);
                    throw new \yii\web\HttpException(500, $msg);
                }
                //VALIDACIONES BÁSICAS DE LOS PARÁMETROS - INDS Y INST NUMERICA
                if (!is_numeric($industria) || !is_numeric($institucion)) {

                    $msg = "CODIGO: -1\r\n";
                    $msg .= "MENSAJE: Códigos de industria e institución deben ser numéricos";
                    $this->setErrorSatu(print_r(func_get_args(), true), $msg);
                    throw new \yii\web\HttpException(500, $msg);
                }

                //VALIDO TIPO ENCUESTA
                if ($tipo_encuesta != '' && $tipo_encuesta != "A" && $tipo_encuesta != "R" && $tipo_encuesta != "M") {
                    $msg = "CODIGO: -1\r\n";
                    $msg .= "MENSAJE: Tipo de encuesta debe ser 'A', 'M' o 'R'";
                    $this->setErrorSatu(print_r(func_get_args(), true), $msg);
                    throw new \yii\web\HttpException(500, $msg);
                }

                //VALIDO QUE EL CONNID NO EXISTA PARA EVITAR LAS ENCUESTAS DOBLES
                $valConid = (int) BaseSatisfaccion::find()->where(["connid" => $connid])->count();
                if ($valConid > 0) {
                    $msg = "CODIGO: -1\r\n";
                    $msg .= "MENSAJE: El connid (" . $connid . "), ya está registrado en el sistema";
                    $this->setErrorSatu(print_r(func_get_args(), true), $msg);
                    throw new \yii\web\HttpException(500, $msg);
                }

                //ARMO EL ARRAY DE CONSULTA
                $data = [
                    'ano' => $ano,
                    'mes' => $mes,
                    'dia' => $dia,
                    'hora' => $hora,
                    'ani' => $ani,
                    'identificacion' => $identificacion,
                    'nombre' => $nm,
                    'agente' => $agente,
                    'ext' => $ext,
                    'rn' => $rn,
                    'pregunta1' => $pregunta1,
                    'pregunta2' => $pregunta2,
                    'pregunta3' => $pregunta3,
                    'pregunta4' => $pregunta4,
                    'pregunta5' => $pregunta5,
                    'pregunta6' => $pregunta6,
                    'pregunta7' => $pregunta7,
                    'pregunta8' => $pregunta8,
                    'pregunta9' => $pregunta9,
                    'pregunta10' => $pregunta10,
                    'connid' => $connid,
                    'industria' => $industria,
                    'institucion' => $institucion,
                    'tipo_servicio' => "IVR",
                    'tipo_encuesta' => $tipo_encuesta,
                ];

                //CONSUMO EL SERVICIO WEB PARA INGRESAR LA ENCUESTA
                /* $wsdl = \yii\helpers\Url::toRoute('basesatisfaccion/baseinicial', true);
                  $client = new \SoapClient($wsdl);
                  $respuesta = $client->insertBasesatisfaccion($data); */
                $respuesta = $this->insertBasesatisfaccion($data);

                /* INICIO WEB SERVICE AMIGO */

                // echo "<pre>";
                // print_r($nModel->tipologia); 


                /* FIN WEB SERVICE AMIGO */

                //SI HUBO ALGUN ERROR MUESTRO EN PANTALLA
                if ($respuesta['codigo'] == -1) {
                    $this->setErrorSatu($data, $respuesta['mensaje']);
                    throw new \yii\web\HttpException(500, $respuesta['mensaje']);
                }

                //RESPUESTA EXITOSA
                if ($respuesta['codigo'] == 1) {
                    $MSG = '<div style="background-color: #dff0d8; '
                            . 'border-color: #d6e9c6; color: #3c763d;'
                            . ' padding: 10px;">';
                    $MSG .= utf8_decode($respuesta['mensaje']);
                    $MSG .= '</div>';
                    echo $MSG;
                }
            }

            /**
             * Funcion para buscar el buzon del registro
             * 
             * @param string $fechaEncuesta Fecha del registro
             * @param string $connId        ConnId
             * 
             * @return string
             */
            private function _buscarArchivoBuzon($fechaEncuesta, $connId) {
                $output = NULL;
                try {
                    $rutaPrincipalBuzonesLlamadas = \Yii::$app->params["ruta_buzon"];
                    $command = "find {$rutaPrincipalBuzonesLlamadas}/Buzones_{$fechaEncuesta} -iname *{$connId}*.wav";
                    \Yii::error("COMANDO BUZON: " . $command, 'basesatisfaccion');
                    file_put_contents("A.TXT", $command);
                    $output = exec($command);
                } catch (\yii\base\Exception $exc) {
                    \Yii::error($exc->getTraceAsString(), 'basesatisfaccion');
                    return $output;
                }
                return $output;
            }

            /**
             * Funcion para buscar llamadas
             * 
             * @param sring $connid
             * @return boolean
             */
            public function actionBuscarllamadas($id, $connid,$view = null) {
                $nModel = \app\models\BaseSatisfaccion::findOne($id);
                $msgError = "";
                $error = false;

                //VALIDO QUE ME ENVIEN UN MODULO
                if (is_null($nModel)) {
                    $msg = \Yii::t('app', 'Formulario no exite');
                    Yii::$app->session->setFlash('warning', $msg);
                    return $this->redirect(Yii::$app->session['iboxPage']);
                }

                //VALIDO QUE ME ENVIEN UN CONNID
                if (empty($connid)) {
                    $msg = \Yii::t('app', 'Ingrese el connid');
                    Yii::$app->session->setFlash('warning', $msg);
                    return $this->redirect(Yii::$app->session['iboxPage']);
                }

                //VALIDO QUE NO ME BUSQUEN LLAMADAS Y BUZONES YA EXISTENTES
                if (!is_null($nModel->llamada) && (!is_null($nModel->buzon) && $nModel->buzon != "")) {
                    $msg = \Yii::t('app', 'Llamadas y buzones ya existentes');
                    Yii::$app->session->setFlash('warning', $msg);
                    return $this->redirect(Yii::$app->session['iboxPage']);
                }

                //SI BUSCAN LLAMADA
                if (is_null($nModel->llamada)) {
                    $formularios = new \app\models\Formularios;

                    $server = Yii::$app->params["server"];
                    $user = Yii::$app->params["user"];
                    $pass = Yii::$app->params["pass"];
                    $db = Yii::$app->params["db"];

                    $idRel = $this->_consultDB($nModel->connid, $server, $user, $pass, $db);
                    $arrayLlamada = "";
                    if (!$idRel) {

                        //CONSULTA EN BD BOGOTA --------------------------------------------
                        $server = Yii::$app->params["serverBog"];
                        $user = Yii::$app->params["userBog"];
                        $pass = Yii::$app->params["passBog"];
                        $db = Yii::$app->params["dbBog"];

                        $idRel = $this->_consultDB($nModel->connid, $server, $user, $pass, $db);

                        if (is_numeric($idRel)) {
                            $wsdl = \Yii::$app->params["wsdl_redbox_bogota"];
                            $arrayLlamada = $formularios->getDataWS($idRel, $wsdl);
                        }
                    } else {
                        $wsdl = \Yii::$app->params["wsdl_redbox"];
                        $arrayLlamada = $formularios->getDataWS($idRel, $wsdl);
                    }

                    //Gaurdamos la llamada ---------------------------------------------
                    $nModel->llamada = (is_array($arrayLlamada) && count($arrayLlamada) >
                            0) ? json_encode($arrayLlamada) : null;

                    if (is_null($nModel->llamada)) {
                        $msgError .= "<li>" . \Yii::t('app', 'No se encontraron llamadas') . "</li>";
                        $error = true;
                    } else {
                        Yii::$app->session->setFlash('success', \Yii::t('app', 'llamada encontrada con exito'));
                    }
                }

                //SI BUSCAN BUZÓN
                if (is_null($nModel->buzon) || empty($nModel->buzon) || $nModel->buzon == "") {

                    //Consulta de buzon ------------------------------------------------
                    $nModel->buzon = $this->_buscarArchivoBuzon(
                            sprintf("%02s", $nModel->dia) . "_" . sprintf("%02s", $nModel->mes) . "_" . $nModel->ano, $nModel->connid);

                    if (empty($nModel->buzon) || $nModel->buzon == "") {
                        $msgError .= "<li>" . \Yii::t('app', 'No se encontraron buzones') . "</li>";
                        $error = true;
                    } else {
                        Yii::$app->session->setFlash('success', \Yii::t('app', 'buzon encontrado con exito'));
                    }
                }

                if (!$nModel->save()) {
                    $msg = \Yii::t('app', 'No se encontraron llamadas');
                    Yii::$app->session->setFlash('warning', $msg);
                    return $this->redirect(Yii::$app->session['iboxPage']);
                } else {
                    if ($error) {
                        Yii::$app->session->setFlash('warning', "<ul>" . $msgError . "</ul>");
                    } else {
                        Yii::$app->session->setFlash('success', \Yii::t('app', 'encontrados con exito'));
                    }
                }
                if (is_null($view)) {
                    return $this->redirect(Yii::$app->session['iboxPage']);
                }else{
                    return $this->redirect([$view]);
                }
            }

            /**
             * Action para mostrar el formulario de satisfaccion
             * 
             * @param int $basesatisfaccion_id
             * @param int $preview
             * @param boolean $fill_values
             * @return mixed
             * @author Felipe Echeverri <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowformulariogestion($basesatisfaccion_id, $preview, $fill_values, $view = "index",$banderaescalado = false,$idtmp = null) {

                $modelBase = BaseSatisfaccion::findOne($basesatisfaccion_id);
                //REDIRECT CORRECTO
                $redct = ($modelBase->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
                //DATOS QUE SERAN ENVIADOS AL FORMULARIO
                $data = new \stdClass();
                //CONSULTAS PARA COMPLETAR INFO DE TMPEJECUCIONFORMULARIO
                $evaluado = \app\models\Evaluados::findOne(["dsusuario_red" => trim($modelBase->agente)]);
                if ((is_null($evaluado) || empty($evaluado) || $evaluado == '') && ($preview != 1 && $fill_values != 1)) {
                    $msg = \Yii::t('app', 'No se encuentra el evaluado asociado: ' . $modelBase->agente . '. Para realizar la gestión haga la creación de los datos del evaluado y asígnele un equipo y lider');
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                $modelReglaNegocio = \app\models\Reglanegocio::findOne(["cod_industria" => $modelBase->industria, "cod_institucion" => $modelBase->institucion, "pcrc" => $modelBase->pcrc, "rn" => $modelBase->rn]);
                $usua_id = Yii::$app->user->identity->id;
                if ($modelBase->usado == "SI" && $modelBase->responsable != Yii::$app->user->identity->username && $preview != 1) {
                    return $this->redirect([$redct]);
                }
                if ($modelBase->estado != 'Cerrado' && $preview != 1) {
                    //MARCO EL REGISTRO COMO TOMADO
                    $modelBase->usado = "SI";
                    $modelBase->responsable = Yii::$app->user->identity->username;
                    $modelBase->save();
                }
                if (count($modelReglaNegocio) == 0) {
                    $msg = \Yii::t('app', 'Este registro no tiene una parametrización asociada:' . $modelBase->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }

                /* Recalcular el lider apartir del evaluado */
                //Primero valido que el evaluado pertenezca a un equipo
                $equipoevaluado = null;
                if ((!is_null($evaluado) || !empty($evaluado) || $evaluado != '')) {
                    $equipoevaluado = \app\models\EquiposEvaluados::find()->select('eq.evaluado_id, eq.equipo_id')
                            ->from('tbl_equipos_evaluados eq')
                            ->join('INNER JOIN', 'tbl_evaluados e', 'e.id = eq.evaluado_id')
                            ->where('e.dsusuario_red = "' . $evaluado->dsusuario_red . '"')
                            ->one();
                }
                if ((is_null($equipoevaluado) || empty($equipoevaluado) || $equipoevaluado == '') && ($preview != 1 && $fill_values != 1)) {
                    $msg = \Yii::t('app', 'El evaluado ' . $modelBase->agente . ' no está incluido en algún equipo');
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                if ((!is_null($equipoevaluado) || !empty($equipoevaluado) || $equipoevaluado != '')) {
                    $equipo = \app\models\Equipos::findOne(['id' => $equipoevaluado->equipo_id]);
                    $usuario = \app\models\Usuarios::findOne(['usua_id' => $equipo->usua_id]);
                    $modelBase->id_lider_equipo = ($modelBase->id_lider_equipo == '' || is_null($modelBase->id_lider_equipo)) ? $equipo->usua_id : $modelBase->id_lider_equipo;
                    $modelBase->lider_equipo = ($modelBase->lider_equipo == '' || is_null($modelBase->lider_equipo)) ? $usuario->usua_nombre : $modelBase->lider_equipo;
                    $modelBase->cc_lider = ($modelBase->cc_lider == '' || is_null($modelBase->cc_lider)) ? $usuario->usua_identificacion : $modelBase->cc_lider;
                    $modelBase->save();
                }
                /* FIN Recalcular el lider apartir del evaluado */
                /*se valida la variable $banderaescalado, en el caso de ser true es una valoracion escalada y cargo el
                tmpejecucionformulario dependiendo del id tmp en la variable $idtmp. Si es false continua
                con la ejecucion que tiene actualmente*/
                if ($banderaescalado) {
                    $TmpForm = \app\models\Tmpejecucionformularios::findOne(['id' => $idtmp, 'basesatisfaccion_id' => $modelBase->id]);
                       $data->formulario = \app\models\Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
                       if (!isset($TmpForm->subi_calculo)) {
                           //$TmpForm->subi_calculo = $data->formulario->subi_calculo
                           if (isset($data->formulario->subi_calculo)) {
                               $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                               //$TmpForm->save();                        
                               $array_indices_TmpForm = \app\models\Textos::find()
                                       ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                       ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                       ->asArray()
                                       ->all();
                               foreach ($array_indices_TmpForm as $value) {
                                   $data->indices_calcular[$value['id']] = $value['text'];
                               }
                           }
                       } else {
                           if (isset($data->formulario->subi_calculo)) {
                               $array_indices_TmpForm = \app\models\Textos::find()
                                       ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                       ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                       ->asArray()
                                       ->all();
                               foreach ($array_indices_TmpForm as $value) {
                                   $data->indices_calcular[$value['id']] = $value['text'];
                               }
                           }
                       }
                       //$data->banderaescalado = $banderaescalado;
                }else{
                    $validarEjecucionForm = \app\models\Ejecucionformularios::findOne(['basesatisfaccion_id' => $modelBase->id]);
                    //$data->banderaescalado = "";
                   //OBTENGO EL FORMULARIO
                   if (is_null($validarEjecucionForm)) {
						/* luego de la validacion en la tabla de ejecucionformularios,
						valido en tmpejecucionformularios si existe para evitar creacion de un tmp adicional */
						$sneditable = 1;
						$condition = [
							"usua_id" => Yii::$app->user->id,
							"arbol_id" => $modelBase->pcrc,
							//"evaluado_id" => $evaluado_id,
							//"dimension_id" => $dimension_id,
							"basesatisfaccion_id" => $modelBase->id,
							"sneditable" => $sneditable,
						];
						$validarTmpejecucionForm = \app\models\Tmpejecucionformularios::findOne($condition);
						
						if (is_null($validarTmpejecucionForm)) {
							$TmpForm = new \app\models\Tmpejecucionformularios();
						   $TmpForm->dimension_id = 1;
						   //$TmpForm->subi_calculo = '1';
						   $TmpForm->arbol_id = $modelBase->pcrc;
						   $TmpForm->usua_id = Yii::$app->user->id;
						   $TmpForm->formulario_id = $modelReglaNegocio->id_formulario;
						   $TmpForm->created = date("Y-m-d H:i:s");
						   $TmpForm->basesatisfaccion_id = $modelBase->id;
						   if ((!is_null($equipoevaluado) || !empty($equipoevaluado) || $equipoevaluado != '') && (!is_null($evaluado) || !empty($evaluado) || $evaluado != '')) {
							   $TmpForm->usua_id_lider = $equipo->usua_id;
							   $TmpForm->evaluado_id = $evaluado->id;
						   }
						   //busco el formulario al cual esta atado la valoracion a cargar
						   //y valido de q si tenga un formulario, de lo contrario se fija 
						   //en 1 por defecto
						   $data->formulario = \app\models\Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
						   if (!isset($TmpForm->subi_calculo)) {
							   //$TmpForm->subi_calculo = $data->formulario->subi_calculo;
							   if (isset($data->formulario->subi_calculo)) {
								   $TmpForm->subi_calculo = $data->formulario->subi_calculo;
								   //$TmpForm->save();                        
								   $array_indices_TmpForm = \app\models\Textos::find()
										   ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
										   ->where('id IN (' . $TmpForm->subi_calculo . ')')
										   ->asArray()
										   ->all();
								   foreach ($array_indices_TmpForm as $value) {
									   $data->indices_calcular[$value['id']] = $value['text'];
								   }
							   }
						   } else {
							   if (isset($data->formulario->subi_calculo)) {
								   $array_indices_TmpForm = \app\models\Textos::find()
										   ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
										   ->where('id IN (' . $TmpForm->subi_calculo . ')')
										   ->asArray()
										   ->all();
								   foreach ($array_indices_TmpForm as $value) {
									   $data->indices_calcular[$value['id']] = $value['text'];
								   }
							   }
						   }
						   $TmpForm->save();
						   $TmpForm = \app\models\Tmpejecucionformularios::findOne($TmpForm->id);
						   $TmpForm->dimension_id = ($modelBase->tipo_inbox == 'NORMAL') ? "" : 1;
						} else {
							$TmpForm = $validarTmpejecucionForm;
							$data->formulario = \app\models\Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
							   if (!isset($TmpForm->subi_calculo)) {
								   //$TmpForm->subi_calculo = $data->formulario->subi_calculo;
								   if (isset($data->formulario->subi_calculo)) {
									   $TmpForm->subi_calculo = $data->formulario->subi_calculo;
									   //$TmpForm->save();                        
									   $array_indices_TmpForm = \app\models\Textos::find()
											   ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
											   ->where('id IN (' . $TmpForm->subi_calculo . ')')
											   ->asArray()
											   ->all();
									   foreach ($array_indices_TmpForm as $value) {
										   $data->indices_calcular[$value['id']] = $value['text'];
									   }
								   }
							   } else {
								   if (isset($data->formulario->subi_calculo)) {
									   $array_indices_TmpForm = \app\models\Textos::find()
											   ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
											   ->where('id IN (' . $TmpForm->subi_calculo . ')')
											   ->asArray()
											   ->all();
									   foreach ($array_indices_TmpForm as $value) {
										   $data->indices_calcular[$value['id']] = $value['text'];
									   }
								   }
							   }
						}	   
                   } else {
                       $formId = \app\models\Ejecucionformularios::llevarATmp($validarEjecucionForm->id, $usua_id);
                       $TmpForm = \app\models\Tmpejecucionformularios::findOne(['id' => $formId['0']['tmp_id'], 'basesatisfaccion_id' => $modelBase->id]);
                       $data->formulario = \app\models\Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
                       if (!isset($TmpForm->subi_calculo)) {
                           //$TmpForm->subi_calculo = $data->formulario->subi_calculo;
                           if (isset($data->formulario->subi_calculo)) {
                               $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                               //$TmpForm->save();                        
                               $array_indices_TmpForm = \app\models\Textos::find()
                                       ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                       ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                       ->asArray()
                                       ->all();
                               foreach ($array_indices_TmpForm as $value) {
                                   $data->indices_calcular[$value['id']] = $value['text'];
                               }
                           }
                       } else {
                           if (isset($data->formulario->subi_calculo)) {
                               $array_indices_TmpForm = \app\models\Textos::find()
                                       ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                       ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                       ->asArray()
                                       ->all();
                               foreach ($array_indices_TmpForm as $value) {
                                   $data->indices_calcular[$value['id']] = $value['text'];
                               }
                           }
                       }
                   }   
                }
                
                $data->tmp_formulario = $TmpForm;
                $data->basesatisfaccion = $modelBase;
                if ((!is_null($equipoevaluado) || !empty($equipoevaluado) || $equipoevaluado != '') && (!is_null($evaluado) || !empty($evaluado) || $evaluado != '')) {
                    $data->equipo_id = $equipoevaluado->equipo_id;
                    $data->usua_id_lider = $equipo->usua_id;
                    //NOMBRE DEL EVALUADO
                    $data->evaluado = $evaluado->name;
                } else {
                    $data->equipo_id = "";
                    $data->usua_id_lider = "";
                }

                //INFORMACION ADICIONAL
                $arbol = \app\models\Arboles::findOne($TmpForm->arbol_id);
                $data->info_adicional = [
                    'problemas' => $arbol->snactivar_problemas,
                    'tipo_llamada' => $arbol->snactivar_tipo_llamada
                ];
                $data->ruta_arbol = $arbol->dsname_full;
                $data->dimension = \app\models\Dimensiones::findOne($TmpForm->dimension_id);
                $data->detalles = \app\models\Tmpejecucionbloquedetalles::getAllByFormId($TmpForm->id);
                $data->tmpBloques = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $TmpForm->id, 'snnousado' => 1]);
                $data->totalBloques = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $TmpForm->id]);
                //CALIFICACIONES
                $tmp_calificaciones_ids = $tmp_tipificaciones_ids = array();
                foreach ($data->detalles as $j => $d) {
                    if (!in_array($d->calificacion_id, $tmp_calificaciones_ids)) {
                        $tmp_calificaciones_ids[] = $d->calificacion_id;
                    }
                    if (!in_array($d->tipificacion_id, $tmp_tipificaciones_ids)) {
                        $tmp_tipificaciones_ids[] = $d->tipificacion_id;
                    }
                    if ($d->tipificacion_id != null) {
                        $data->detalles[$j]->tipif_seleccionados = \app\models\TmpejecucionbloquedetallesTipificaciones::getTipificaciones($d->id);
                    } else {
                        $data->detalles[$j]->tipif_seleccionados = array();
                    }
                }

                //CALIFICACIONES Y TIPIFICACIONES
                $data->calificaciones = \app\models\Calificaciondetalles::getDetallesFromIds($tmp_calificaciones_ids);
                $data->calificacionesArray = \app\models\Calificaciondetalles::getDetallesFromIdsAsArray($tmp_calificaciones_ids);
                $data->tipificaciones = \app\models\Tipificaciondetalles::getDetallesFromIds($tmp_tipificaciones_ids);

                //TRANSACCIONES Y ENFOQUES
                $data->transacciones = \yii\helpers\ArrayHelper::map(\app\models\Transacions::find()->all(), 'id', 'name');
                $data->enfoques = \app\models\Tableroenfoques::find()->asArray()->all();

                //FORMULARIO ID
                $data->formulario_id = $TmpForm->id;
                /* OBTIENE EL LISTADO DETALLADO DE TABLERO DE EXPERIENCIAS Y LLAMADA
                  EN MODO VISUALIZACIÓN FORMULARIO. */
                $data->tablaproblemas = \app\models\Ejecuciontableroexperiencias::
                                find()
                                ->where(["ejecucionformulario_id" => $TmpForm->ejecucionformulario_id])->all();
                $data->tablallamadas = \app\models\Ejecuciontiposllamada::getTabLlamByIdEjeForm($TmpForm->ejecucionformulario_id);
                $data->list_Add_feedbacks = \app\models\Tmpejecucionfeedbacks::getJoinTipoFeedbacks($TmpForm->id);
                $data->preguntas = \app\models\ParametrizacionEncuesta::find()->select("tbl_preguntas.id,tbl_preguntas.pre_indicador,tbl_preguntas.enunciado_pre,tbl_preguntas.categoria,tbl_categorias.nombre")
                                ->join("INNER JOIN", "tbl_preguntas", "tbl_parametrizacion_encuesta.id = tbl_preguntas.id_parametrizacion")
                                ->join("INNER JOIN", "tbl_categorias", "tbl_categorias.id = tbl_preguntas.categoria")
                                ->where(["cliente" => $modelBase->cliente, "programa" => $modelBase->pcrc])->asArray()->all();
                $data->recategorizar = BaseSatisfaccion::getCategorias($modelBase->id);
                $data->dimension = \app\models\Dimensiones::getDimensionsListForm();
                if (count($data->preguntas) == 0) {
                    $msg = \Yii::t('app', 'No existe parametrización asociada para este formulario:' . $modelBase->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                //PREVIEW
                $data->preview = $preview == 1 ? true : false;
                $data->fill_values = $fill_values;
                //print_r($data); die;
                //VALIDO Q  LA REGLA DE NEGOCIO TENGA UN FORMULARIO ASOCIADO
                $form_val = \app\models\Formularios::findOne($modelReglaNegocio->id_formulario);
                //$TmpForm->subi_calculo = $form_val->subi_calculo;
                if (empty($form_val)) {
                    $msg = \Yii::t('app', 'No existe formulario asociada para esta gestión:' . $modelBase->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    //var_dump($data); die;
                    return $this->render("showformulariosatisfaccion", ["data" => $data,"view" => $view, "formulario" => false, 'banderaescalado' => false]);
                }

                /* CONSULTO LA TABLA DE RESPOSABILIDAD */
                $data->responsabilidad = ArrayHelper::map(
                                \app\models\Responsabilidad::find()
                                        ->where([
                                            'arbol_id' => $modelBase->pcrc,
                                        ])
                                        ->asArray()->all(), 'nombre', 'nombre', 'tipo'
                );
                /* if (!isset($data->responsabilidad['EQUIVOCACION'])||!isset($data->responsabilidad['CANAL'])||!isset($data->responsabilidad['MARCA'])) {
                  $msg = \Yii::t('app', 'Por favor verifique la configuración de la PROTECCIÓN DE LA EXPERIENCIA, ya que falta parametrizar alguna de sus opciones');
                  Yii::$app->session->setFlash('danger', $msg);
                  } */
                // echo "<pre>";
                // print_r($data); die;
                return $this->render('showformulariosatisfaccion', [
                            'data' => $data,
							'view' => $view,
                            'formulario' => true,
                            'banderaescalado' => false
                ]);
            }

            /**
             * Action para guardar y enviar el formulario
             *      
             * @return mixed
             * @author Felipe Echeverri <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionGuardaryenviarformulariogestion() {

                $arrCalificaciones = !$_POST['calificaciones'] ? array() : $_POST['calificaciones'];
                $arrTipificaciones = !isset($_POST['tipificaciones']) ? array() : $_POST['tipificaciones'];
                $arrSubtipificaciones = !isset($_POST['subtipificaciones']) ? array() : $_POST['subtipificaciones'];
                $arrComentariosSecciones = !$_POST['comentarioSeccion'] ? array() : $_POST['comentarioSeccion'];
                $arrCheckPits = !isset($_POST['checkPits']) ? array() : $_POST['checkPits'];
                $arrayForm = $_POST;
                $arrFormulario = [];
                /* Variables para conteo de bloques */
                $arrayCountBloques = [];
                $arrayBloques = [];
                $count = 0;
                /* fin de variables */
                $tmp_id = $_POST['tmp_formulario_id'];
                $basesatisfaccion_id = $_POST['basesatisfaccion_id'];
                $arrFormulario["dimension_id"] = $_POST['dimension'];
                $arrFormulario["dsruta_arbol"] = $_POST['ruta_arbol'];
                $arrFormulario["dscomentario"] = $_POST['comentarios_gral'];
                $arrFormulario["dsfuente_encuesta"] = $_POST['fuente'];
                $arrFormulario["transacion_id"] = $_POST['transacion_id'];
                $arrFormulario["sn_mostrarcalculo"] = 1;
		$view = (isset($_POST['view']))?$_POST['view']:null;
                $modelBase = BaseSatisfaccion::findOne($basesatisfaccion_id);
                /* $modelBase->comentario = $arrFormulario["dscomentario"];
                  $modelBase->tipologia = $_POST['categoria'];
                  $modelBase->estado = $_POST['estado'];
                  $modelBase->usado = "NO";
                  $modelBase->responsabilidad = (isset($_POST['responsabilidad'])) ? $_POST['responsabilidad'] : "";
                  $modelBase->canal = (isset($_POST['canal'])) ? implode(", ", $_POST['canal']) : "";
                  $modelBase->marca = (isset($_POST['marca'])) ? implode(", ", $_POST['marca']) : "";
                  $modelBase->equivocacion = (isset($_POST['equivocacion'])) ? implode(", ", $_POST['equivocacion']) : "";
                  $modelBase->save(); */
                $arrFormulario["usua_id_lider"] = $_POST['form_lider_id'];
                $arrFormulario["equipo_id"] = $_POST['form_equipo_id'];
                //$arrFormulario["sn_mostrarcalculo"] = 1;
                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);
                if ($_POST['subi_calculo'] != '') {
                    $data->subi_calculo .=',' . $_POST['subi_calculo'];
                    $data->save();
                }
                /*                 * if ($modelBase->tipo_inbox != 'NORMAL') {
                  $arrFormulario["dimension_id"] = 1;
                  } */
                //IF TODOS LOS BLOQUES ESTAN USADOS SETEO ARRAY VACIO
                if (!isset($arrayForm['bloque'])) {
                    $arrayForm['bloque'] = [];
                }
                /* INTENTO GUARDAR LOS FORMULARIOS */
                try {
                    /* EDITO EL TMP FORMULARIO */
                    $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
                    $model->usua_id_actual = Yii::$app->user->identity->id;
                    $model->save();
                    
                    \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                    \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                    \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);

                    $bloquesFormtmp = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                    
                    foreach ($bloquesFormtmp as $bloquetmp) {
                        if (array_key_exists($bloquetmp->bloque_id, $arrayForm['bloque'])) {
                            $bloquetmp->snnousado = 1;
                            $bloquetmp->save();
                            $arrDetalleForm = [];
                            $arrDetalleForm["calificacion_id"] = -1;
                            $arrDetalleForm["calificaciondetalle_id"] = -1;
                            \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ['tmpejecucionformulario_id' => $tmp_id,
                                'bloque_id' => $bloquetmp->bloque_id]);
                        } else {
                            $bloquetmp->snnousado = 0;
                            $bloquetmp->save();
                        }
                    }
                    /* GUARDO LAS CALIFICACIONES */
                    foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                        $arrDetalleForm = [];
                        //se valida que existan check de pits seleccionaddos y se valida
                        //que exista el del bloquedetalle actual para actualizarlo
                        if (count($arrCheckPits) > 0) {
                            if (isset($arrCheckPits[$form_detalle_id])) {
                                $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                            }
                        }
                        if (empty($calif_detalle_id)) {
                            $arrDetalleForm["calificaciondetalle_id"] = -1;
                        } else {
                            $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                        }
                        \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["id" => $form_detalle_id]);
                        $calificacion = \app\models\Tmpejecucionbloquedetalles::findOne(["id" => $form_detalle_id]);
                        $calificacionDetalle = \app\models\Calificaciondetalles::findOne(['id' => $calificacion->calificaciondetalle_id]);
                        //Cuento las preguntas en las cuales esta seleccionado el NA
                        //lleno $arrayBloques para tener marcados en que bloques no se selecciono el check
                        if (!in_array($calificacion->bloque_id, $arrayBloques) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                            $arrayBloques[] = $calificacion->bloque_id;
                            //inicio $arrayCountBloques
                            $arrayCountBloques[$count] = [($calificacion->bloque_id) => 1];
                            $count++;
                        } else {
                            if (count($arrayCountBloques) != 0) {
                                //actualizo $arrayCountBloques sumandole 1 cada q encuentra un NA de ese bloque
                                if ((array_key_exists($calificacion->bloque_id, $arrayCountBloques[count($arrayCountBloques) - 1])) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                                    $arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] = ($arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] + 1);
                                }
                            }
                        }
                    }
                    //$arrayCountBloques = call_user_func_array('array_merge', $arrayCountBloques);
                    //Actualizo los bloques en los cuales el total de sus preguntas esten seleccionadas en NA
                    foreach ($arrayCountBloques as $dato) {
                        $totalPreguntasBloque = \app\models\Tmpejecucionbloquedetalles::find()->select("COUNT(id) as preguntas")
                                        ->from("tbl_tmpejecucionbloquedetalles")
                                        ->where(['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)])->asArray()->all();
                        if ($dato[key($dato)] == $totalPreguntasBloque["0"]["preguntas"]) {
                            \app\models\Tmpejecucionbloques::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)]);
                        }
                    }
                    //actualizo las secciones, la cuales tienen todos sus bloques con la opcion snna en 1
                    $secciones = \app\models\Tmpejecucionsecciones::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                    foreach ($secciones as $seccion) {
                        $bloquessnna = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                        ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                        ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                        ->where(['b.snna' => 1, 's.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                        ->groupBy("s.id")->asArray()->all();
                        $totalBloques = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                        ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                        ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                        ->where(['s.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                        ->groupBy("s.id")->asArray()->all();
                        if (count($bloquessnna) > 0) {
                            if ($bloquessnna[0]['conteo'] == $totalBloques[0]['conteo']) {
                                \app\models\Tmpejecucionsecciones::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'seccion_id' => ($seccion->seccion_id)]);
                            }
                        }
                    }
                    /* GUARDO TIPIFICACIONES */
                    foreach ($arrTipificaciones as $form_detalle_id => $tipif_array) {
                        if (empty($tipif_array))
                            continue;

                        \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 0]
                                , ["tmpejecucionbloquedetalle_id" => $form_detalle_id]);

                        \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 1]
                                , "tmpejecucionbloquedetalle_id = '" . $form_detalle_id . "' "
                                . "AND tipificaciondetalle_id IN(" . implode(",", $tipif_array) . ")");
                    }

                    /* GUARDO SUBTIPIFICACIONES */
                    foreach ($arrSubtipificaciones as $form_detalle_id => $subtipif_array) {
                        $sql = "UPDATE `tbl_tmpejecucionbloquedetalles_subtipificaciones` a ";
                        $sql .= "INNER JOIN tbl_tmpejecucionbloquedetalles_tipificaciones b ";
                        $sql .= "ON a.tmpejecucionbloquedetalles_tipificacion_id = b.id ";
                        $sql .= "SET a.sncheck = 1 ";
                        $sql .= "WHERE b.tmpejecucionbloquedetalle_id = " . $form_detalle_id;
                        $sql .= " AND a.tipificaciondetalle_id IN (" . implode(",", $subtipif_array) . ")";
                        $command = \Yii::$app->db->createCommand($sql);
                        $command->execute();
                    }

                    foreach ($arrComentariosSecciones as $secc_id => $comentario) {

                        \app\models\Tmpejecucionsecciones::updateAll(["dscomentario" => $comentario]
                                , [
                            "seccion_id" => $secc_id
                            , "tmpejecucionformulario_id" => $tmp_id
                        ]);
                    }
                    //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                    $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                    /* GUARDAR EL TMP FOMULARIO A LAS EJECUCIONES */
					/* GUARDAR en una variable el retorno de la funcion */
                    $validarPasoejecucionform = \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);
                    /* validacion de guardado exitoso del tmp y paso a las tablas de ejecucion
                      en caso de no cumplirla, se redirige nuevamente al formulario */

                    if (!$validarPasoejecucionform) {
                        Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception tmpejecucion to ejecucion'));
                        return $this->redirect(['basesatisfaccion/showformulariogestion',
                            'basesatisfaccion_id' => $modelBase->id, 'preview' => 0, 'fill_values' => false, 'banderaescalado' => false]);
                    }
                    $modelBase->comentario = $arrFormulario["dscomentario"];
                    $modelBase->tipologia = $_POST['categoria'];
                    $modelBase->estado = $_POST['estado'];
                    $modelBase->usado = "NO";
                    $modelBase->responsabilidad = (isset($_POST['responsabilidad'])) ? $_POST['responsabilidad'] : "";
                    $modelBase->canal = (isset($_POST['canal'])) ? implode(", ", $_POST['canal']) : "";
                    $modelBase->marca = (isset($_POST['marca'])) ? implode(", ", $_POST['marca']) : "";
                    $modelBase->equivocacion = (isset($_POST['equivocacion'])) ? implode(", ", $_POST['equivocacion']) : "";
                    $modelBase->save();

                    Yii::$app->session->setFlash('success', Yii::t('app', 'Formulario guardado'));
                    /* TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                     
					 * */
                      $modelEvaluado = \app\models\Evaluados::findOne(["id" => $tmp_ejecucion->evaluado_id]);
                      $ejecucion = \app\models\Ejecucionformularios::find()->where(['evaluado_id' => $tmp_ejecucion->evaluado_id, 'usua_id' => $tmp_ejecucion->usua_id])->orderBy('id DESC')->all();
                      $params = [];
                      $params['titulo'] = 'Te han realizado una valoración';
                      $params['pcrc'] = '';
                      $params['descripcion'] = '';
                      $params['notificacion'] = 'SI';
                      $params['muro'] = 'NO';
                      $params['usuariored'] = $modelEvaluado->dsusuario_red;
                      $params['cedula'] = '';
                      $params['plataforma'] = 'QA';
                      $params['url'] = '' . Url::to(['formularios/showformulariodiligenciadoamigo']) . '?form_id=' . base64_encode($ejecucion[0]->id);
                      $webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
                      $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                      if (!$webservicesresponse && $tmp_ejecucion == '') {
                      
					  
					  Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));				  
					  }
                } catch (\Exception $exc) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception') . ": " . $exc->getMessage());
                }

                //REDIRECT CORRECTO
                if ($view == "index") {
                    $redct = ($modelBase->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';

                    return $this->redirect(Yii::$app->session['iboxPage']);
                } else {
                    $redct = $view;
                    return $this->redirect([$redct]);
                }
            }

            /**
             * Metodo para borrar el formulario temporal
             * 
             * @param int $tmp_form
             * @return mixed
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionEliminartmpform($id) {

                \app\models\Tmpejecucionformularios::deleteAll(["id" => $id]);

                Yii::$app->session->setFlash('success', Yii::t('app', 'Formulario borrado'));
                return $this->redirect(['index']);
            }

            /**
             * Metodo guardar el log de cargas SATU
             * 
             * @param array $datos
             * @param string $error             
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function setErrorSatu($datos, $error) {
                $satu = new \app\models\ErroresSatu();
                $satu->created = date('Y-m-d H:i:s');
                $satu->fecha_satu = date('Y-m-d H:i:s');
                $horaSatu = '00:00:00';
                if (isset($datos['hora'])) {
                    if (strlen($datos['hora']) <= 4) {
                        $horaSatu = '00:00:00';
                    } else {
                        if (strlen($datos['hora']) % 2 == 0) {
                            $array = str_split($datos['hora'], 2);
                            $horaSatu = $array[0] . ":" . $array[1] . ":" . $array[2];
                        } else {
                            $array = str_split(substr($datos['hora'], 1, 4), 2);
                            $horaSatu = substr($datos['hora'], 0, 1) . ":" . $array[0] . ":" . $array[1];
                        }
                    }
                    $satu->fecha_satu = $datos['ano'] . '-' . $datos['mes'] . '-' . $datos['dia'] . ' ' . $horaSatu;
                }

                $satu->datos = print_r($datos, true);
                $satu->error = $error;
                $satu->save();
            }

            /**
             * Metodo hacer control del inbox aleatorio por cada PCRC a las 11:59pm
             *                      
             * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionControlinboxaleatorio() {

                //ARRAY DE INFORMES
                $EncuestasMalas = $InformeGeneral = [];

                //TRAIGO TODAS LAS PCRC Y REGLAS DE NEGOCIO
                $pcrcs = \app\models\Reglanegocio::find()
                        ->joinWith('pcrc0')
                        ->groupBy('`pcrc`')
                        ->orderBy('`pcrc`')
                        ->asArray()
                        ->all();

                //PARA CADA PCRC CALCULO SUS ENCUESTAS DIARIAS
                foreach ($pcrcs as $pcrc) {
                    //NOMBRE PCRC
                    $nmPcrc = $pcrc['pcrc0']['name'];
                    if ($pcrc['encu_diarias'] == 0 || $pcrc['encu_mes'] == 0) {
                        continue;
                    }
                    //TRAIGO EL TOTAL DE ENCUSTAS DEL DIA DE HOY PARA LA PCRC
                    $tot = (int) BaseSatisfaccion::find()
                                    ->where([
                                        'pcrc' => $pcrc['pcrc'],
                                        'mes' => date('m'),
                                        'dia' => date('d'),
                                        'ano' => date('Y'),
                                        'tipo_inbox' => 'ALEATORIO'
                                    ])
                                    ->count();
                    //TRAIGO EL TOTAL DE ENCUESTAS DEL MES PARA LA PCRC
                    //TOTAL DE ENCUESTAS DEL MES
                    $totAleMes = BaseSatisfaccion::find()
                            ->select("id")
                            ->where([
                                'tipo_inbox' => 'ALEATORIO',
                                'pcrc' => $pcrc['pcrc'],
                                'ano' => date('Y'),
                                'mes' => date('m')
                            ])
                            ->all();
                    //SI LAS ENCUESTAS DE HOY SON MENORES QUE LA CUOTA DEL DIA
                    if ($tot < $pcrc['encu_diarias'] && count($totAleMes) < $pcrc['encu_mes']) {

                        //ENCUESTAS FALTANTES DEL DIA Y EL MES
                        $encuFaltantesDia = $pcrc['encu_diarias'] - $tot;
                        //Validar si hay  encuestas disponibles en el inbox General
                        $totNomalHoy = BaseSatisfaccion::find()
                                ->where([
                                    'pcrc' => $pcrc['pcrc'],
                                    'mes' => date('m'),
                                    'dia' => date('d'),
                                    'ano' => date('Y'),
                                    'tipo_inbox' => 'NORMAL'
                                ])
                                ->andWhere("`tipo_encuesta`='A' || `tipo_encuesta`='R'")
                                ->andWhere("`responsable` IS NULL || `responsable` = ''")
                                ->all();
                        $faltaron = count($totNomalHoy) - $encuFaltantesDia;

                        if ($faltaron < 0) {
                            $EncuestasMalas[] = [
                                'pcrc' => $nmPcrc,
                                'correos' => $pcrc['correos_notificacion'],
                                'encu_diarias_pcrc' => $pcrc['encu_diarias'],
                                'encu_diarias_totales' => $tot,
                                'encu_mes_pcrc' => $pcrc['encu_mes'],
                                'encu_mes_totales' => count($totAleMes),
                                'faltaron' => $encuFaltantesDia,
                                'disponibles' => count($totNomalHoy),
                                'estado' => 'CRITICA POR CAPACIDAD DEL PROCESO',
                            ];
                        } else {
                            $InformeGeneral[] = [
                                'pcrc' => $nmPcrc,
                                'encu_diarias_pcrc' => $pcrc['encu_diarias'],
                                'encu_diarias_totales' => $tot,
                                'encu_mes_pcrc' => $pcrc['encu_mes'],
                                'encu_mes_totales' => count($totAleMes),
                                'faltaron' => $encuFaltantesDia,
                                'disponibles' => count($totNomalHoy),
                                'estado' => 'AUTOCOMPLETA LA CUOTA',
                            ];
                        }

                        //PASO AL INBOX ALEATORIO MIESTRAS NO SUPERE EL NUMERO DE DIAS NI DE MES
                        foreach ($totNomalHoy as $value) {
                            //TOTAL DE ENCUESTAS DEL MES
                            $totAle = BaseSatisfaccion::find()
                                    ->select("id")
                                    ->where([
                                        'tipo_inbox' => 'ALEATORIO',
                                        'pcrc' => $pcrc['pcrc'],
                                        'ano' => date('Y'),
                                        'mes' => date('m')
                                    ])
                                    ->all();
                            if (($pcrc['encu_diarias'] + $encuFaltantesDia) > $pcrc['encu_diarias'] && count($totAle) < $pcrc['encu_mes']) {
                                $base = BaseSatisfaccion::findOne($value->id);
                                $base->tipo_inbox = 'ALEATORIO';
                                $base->update();
                                $encuFaltantesDia--;
                            } else {
                                break;
                            }
                        }
                    } else {
                        $InformeGeneral[] = [
                            'pcrc' => $nmPcrc,
                            'encu_diarias_pcrc' => $pcrc['encu_diarias'],
                            'encu_diarias_totales' => $tot,
                            'encu_mes_pcrc' => $pcrc['encu_mes'],
                            'encu_mes_totales' => count($totAleMes),
                            'faltaron' => 0,
                            'estado' => 'NORMAL'
                        ];
                    }
                }
                //REALIZO INFORME GENERAL
                foreach ($InformeGeneral as $value) {
                    $informe = new \app\models\InformeInboxAleatorio();
                    $informe->pcrc = $value['pcrc'];
                    $informe->encu_diarias_pcrc = $value['encu_diarias_pcrc'];
                    $informe->encu_diarias_totales = $value['encu_diarias_totales'];
                    $informe->encu_mes_pcrc = $value['encu_mes_pcrc'];
                    $informe->encu_mes_totales = $value['encu_mes_totales'];
                    $informe->faltaron = $value['faltaron'];
                    $informe->disponibles = (isset($value['disponibles'])) ? $value['disponibles'] : null;
                    $informe->estado = $value['estado'];
                    $informe->fecha_creacion = date('Y-m-d H:i:s');
                    $informe->save();
                }

                //REALIZO INFORME DE ERRORES Y MANDO POR CORREO
                foreach ($EncuestasMalas as $value) {

                    $html = "Estos servicios presentan notificación por capacidad del proceso.";
                    $html .= "<br /><br />";
                    $html .= "<table style='border-collapse: collapse;'>";
                    $html .= "<tr>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>Fecha creacion</th>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>PCRC</th>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>Cantidad meta diaria</th>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>Cantidad real diaria</th>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>Cantidad meta mes</th>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>Cantidad real mes</th>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>Faltaron para la meta diaria</th>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>Disponibles para completar meta diaria</th>";
                    $html .= "<th style='background: red none no-repeat scroll 0 0; border: 1px solid #000000; color: #ffffff; padding: 10px;'>Estado</th>";
                    $html .= "</tr>";

                    $informe = new \app\models\InformeInboxAleatorio();
                    $informe->pcrc = $value['pcrc'];
                    $informe->encu_diarias_pcrc = $value['encu_diarias_pcrc'];
                    $informe->encu_diarias_totales = $value['encu_diarias_totales'];
                    $informe->encu_mes_pcrc = $value['encu_mes_pcrc'];
                    $informe->encu_mes_totales = $value['encu_mes_totales'];
                    $informe->faltaron = $value['faltaron'];
                    $informe->disponibles = $value['disponibles'];
                    $informe->estado = $value['estado'];
                    $informe->fecha_creacion = date('Y-m-d H:i:s');
                    $informe->save();

                    $html .= "<tr>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . date('Y-m-d H:i:s') . "</td>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . $value['pcrc'] . "</td>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . $value['encu_diarias_pcrc'] . "</td>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . $value['encu_diarias_totales'] . "</td>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . $value['encu_mes_pcrc'] . "</td>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . $value['encu_mes_totales'] . "</td>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . $value['faltaron'] . "</td>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . $value['disponibles'] . "</td>";
                    $html .= "<td style=' border: 1px solid #000000;  padding: 10px;'>" . $value['estado'] . "</td>";
                    $html .= "</tr>";

                    $html .= "</table>";

                    if (!is_null($value['correos'])) {
                        $to = explode(",", $value['correos']);
                        //ENVIO POR CORREO LAS MALAS                
                        Yii::$app->mailer->compose()
                                ->setTo($to)
                                ->setFrom(Yii::$app->params['email_satu_from'])
                                ->setSubject('CRITICA POR CAPACIDAD DEL PROCESO QA')
                                ->setHtmlBody($html)
                                ->send();
                    }
                }
            }

            /**
             * Action para mostrar el formulario de satisfaccion
             * 
             * @param int $basesatisfaccion_id
             * @param int $preview
             * @param boolean $fill_values
             * @return mixed
             * @author Felipe Echeverri <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionShowformulariogestionamigo($basesatisfaccion_id, $preview, $fill_values, $view = "index") {
                $basesatisfaccion_id = base64_decode($basesatisfaccion_id);
                $modelBase = BaseSatisfaccion::findOne($basesatisfaccion_id);
                //REDIRECT CORRECTO
                $redct = ($modelBase->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
                //DATOS QUE SERAN ENVIADOS AL FORMULARIO
                $data = new \stdClass();
                //CONSULTAS PARA COMPLETAR INFO DE TMPEJECUCIONFORMULARIO
                $evaluado = \app\models\Evaluados::findOne(["dsusuario_red" => trim($modelBase->agente)]);
                if (is_null($evaluado)) {
                    $msg = \Yii::t('app', 'No se encuentra el evaluado asociado: ' . $modelBase->agente);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                $modelReglaNegocio = \app\models\Reglanegocio::findOne(["cod_industria" => $modelBase->industria, "cod_institucion" => $modelBase->institucion, "pcrc" => $modelBase->pcrc, "rn" => $modelBase->rn]);
                //USUARIO GENERICO
                $usua_id = 0;
                if ($modelBase->usado == "SI" && $modelBase->responsable != Yii::$app->user->identity->username && $preview != 1) {
                    return $this->redirect([$redct]);
                }
                if ($modelBase->estado != 'Cerrado' && $preview != 1) {
                    //MARCO EL REGISTRO COMO TOMADO
                    $modelBase->usado = "SI";
                    $modelBase->responsable = Yii::$app->user->identity->username;
                    $modelBase->save();
                }
                if (count($modelReglaNegocio) == 0) {
                    $msg = \Yii::t('app', 'Este registro no tiene una parametrización asociada:' . $modelBase->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                $validarEjecucionForm = \app\models\Ejecucionformularios::findOne(['basesatisfaccion_id' => $modelBase->id]);

                //OBTENGO EL FORMULARIO
                if (is_null($validarEjecucionForm)) {

                    $TmpForm = new \app\models\Tmpejecucionformularios();
                    $TmpForm->dimension_id = 1;
                    $TmpForm->arbol_id = $modelBase->pcrc;
                    $TmpForm->usua_id = Yii::$app->user->id;
                    $TmpForm->evaluado_id = $evaluado->id;
                    $TmpForm->formulario_id = $modelReglaNegocio->id_formulario;
                    $TmpForm->created = date("Y-m-d H:i:s");
                    $TmpForm->basesatisfaccion_id = $modelBase->id;
                    $data->formulario = Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
                    if (!isset($TmpForm->subi_calculo)) {
                        //$TmpForm->subi_calculo = $data->formulario->subi_calculo;
                        if (isset($data->formulario->subi_calculo)) {
                            $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                            $TmpForm->save();
                            $array_indices_TmpForm = \app\models\Textos::find()
                                    ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                    ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                    ->asArray()
                                    ->all();
                            foreach ($array_indices_TmpForm as $value) {
                                $data->indices_calcular[$value['id']] = $value['text'];
                            }
                        }
                    } else {
                        if (isset($data->formulario->subi_calculo)) {
                            $array_indices_TmpForm = \app\models\Textos::find()
                                    ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                    ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                    ->asArray()
                                    ->all();
                            foreach ($array_indices_TmpForm as $value) {
                                $data->indices_calcular[$value['id']] = $value['text'];
                            }
                        }
                    }
                    $TmpForm->save();
                    $TmpForm = \app\models\Tmpejecucionformularios::findOne($TmpForm->id);
                    $TmpForm->dimension_id = ($modelBase->tipo_inbox == 'NORMAL') ? "" : 1;
                } else {
                    $formId = \app\models\Ejecucionformularios::llevarATmp($validarEjecucionForm->id, $usua_id);
                    $TmpForm = \app\models\Tmpejecucionformularios::findOne(['id' => $formId['0']['tmp_id'], 'basesatisfaccion_id' => $modelBase->id]);
                    $data->formulario = Formularios::find()->where(['id' => $modelReglaNegocio->id_formulario])->one();
                    if (!isset($TmpForm->subi_calculo)) {
                        //$TmpForm->subi_calculo = $data->formulario->subi_calculo;
                        if (isset($data->formulario->subi_calculo)) {
                            $TmpForm->subi_calculo = $data->formulario->subi_calculo;
                            $TmpForm->save();
                            $array_indices_TmpForm = \app\models\Textos::find()
                                    ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                    ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                    ->asArray()
                                    ->all();
                            foreach ($array_indices_TmpForm as $value) {
                                $data->indices_calcular[$value['id']] = $value['text'];
                            }
                        }
                    } else {
                        if (isset($data->formulario->subi_calculo)) {
                            $array_indices_TmpForm = \app\models\Textos::find()
                                    ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                                    ->where('id IN (' . $TmpForm->subi_calculo . ')')
                                    ->asArray()
                                    ->all();
                            foreach ($array_indices_TmpForm as $value) {
                                $data->indices_calcular[$value['id']] = $value['text'];
                            }
                        }
                    }
                }

                $data->tmp_formulario = $TmpForm;
                $data->basesatisfaccion = $modelBase;

                //OBTEGO EL ID DEL EQUIPO Y EL ID DEL LIDER
                $datos_eq_li = \app\models\Equipos::getEquipoLider($TmpForm->evaluado_id, $TmpForm->arbol_id);

                if (count($datos_eq_li) > 0) {
                    $data->equipo_id = $datos_eq_li["equipo_id"];
                    $data->usua_id_lider = $datos_eq_li["lider"];
                } else {
                    $data->equipo_id = "";
                    $data->usua_id_lider = "";
                }

                //NOMBRE DEL EVALUADO
                $data->evaluado = $evaluado->name;

                //INFORMACION ADICIONAL
                $arbol = \app\models\Arboles::findOne($TmpForm->arbol_id);
                $data->info_adicional = [
                    'problemas' => $arbol->snactivar_problemas,
                    'tipo_llamada' => $arbol->snactivar_tipo_llamada
                ];
                $data->ruta_arbol = $arbol->dsname_full;
                $data->dimension = \app\models\Dimensiones::findOne($TmpForm->dimension_id);
                $data->detalles = \app\models\Tmpejecucionbloquedetalles::getAllByFormId($TmpForm->id);
                $data->tmpBloques = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $TmpForm->id, 'snnousado' => 1]);
                $data->totalBloques = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $TmpForm->id]);
                //CALIFICACIONES
                $tmp_calificaciones_ids = $tmp_tipificaciones_ids = array();
                foreach ($data->detalles as $j => $d) {
                    if (!in_array($d->calificacion_id, $tmp_calificaciones_ids)) {
                        $tmp_calificaciones_ids[] = $d->calificacion_id;
                    }
                    if (!in_array($d->tipificacion_id, $tmp_tipificaciones_ids)) {
                        $tmp_tipificaciones_ids[] = $d->tipificacion_id;
                    }
                    if ($d->tipificacion_id != null) {
                        $data->detalles[$j]->tipif_seleccionados = \app\models\TmpejecucionbloquedetallesTipificaciones::getTipificaciones($d->id);
                    } else {
                        $data->detalles[$j]->tipif_seleccionados = array();
                    }
                }

                //CALIFICACIONES Y TIPIFICACIONES
                $data->calificaciones = \app\models\Calificaciondetalles::getDetallesFromIds($tmp_calificaciones_ids);
                $data->calificacionesArray = \app\models\Calificaciondetalles::getDetallesFromIdsAsArray($tmp_calificaciones_ids);
                $data->tipificaciones = \app\models\Tipificaciondetalles::getDetallesFromIds($tmp_tipificaciones_ids);

                //TRANSACCIONES Y ENFOQUES
                $data->transacciones = \yii\helpers\ArrayHelper::map(\app\models\Transacions::find()->all(), 'id', 'name');
                $data->enfoques = \app\models\Tableroenfoques::find()->asArray()->all();

                //FORMULARIO ID
                $data->formulario_id = $TmpForm->id;
                /* OBTIENE EL LISTADO DETALLADO DE TABLERO DE EXPERIENCIAS Y LLAMADA
                  EN MODO VISUALIZACIÓN FORMULARIO. */
                $data->tablaproblemas = \app\models\Ejecuciontableroexperiencias::
                                find()
                                ->where(["ejecucionformulario_id" => $TmpForm->ejecucionformulario_id])->all();
                $data->tablallamadas = \app\models\Ejecuciontiposllamada::getTabLlamByIdEjeForm($TmpForm->ejecucionformulario_id);
                $data->list_Add_feedbacks = \app\models\Tmpejecucionfeedbacks::getJoinTipoFeedbacks($TmpForm->id);
                $data->preguntas = \app\models\ParametrizacionEncuesta::find()->select("tbl_preguntas.id,tbl_preguntas.pre_indicador,tbl_preguntas.enunciado_pre,tbl_preguntas.categoria,tbl_categorias.nombre")
                                ->join("INNER JOIN", "tbl_preguntas", "tbl_parametrizacion_encuesta.id = tbl_preguntas.id_parametrizacion")
                                ->join("INNER JOIN", "tbl_categorias", "tbl_categorias.id = tbl_preguntas.categoria")
                                ->where(["cliente" => $modelBase->cliente, "programa" => $modelBase->pcrc])->asArray()->all();
                $data->recategorizar = BaseSatisfaccion::getCategorias($modelBase->id);
                $data->dimension = \app\models\Dimensiones::getDimensionsListForm();
                if (count($data->preguntas) == 0) {
                    $msg = \Yii::t('app', 'No existe parametrización asociada para este formulario:' . $modelBase->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->redirect([$redct]);
                }
                //PREVIEW
                $data->preview = $preview == 1 ? true : false;
                $data->fill_values = $fill_values;
                //VALIDO Q  LA REGLA DE NEGOCIO TENGA UN FORMULARIO ASOCIADO
                $form_val = \app\models\Formularios::findOne($modelReglaNegocio->id_formulario);
                if (empty($form_val)) {
                    $msg = \Yii::t('app', 'No existe formulario asociada para esta gestión:' . $modelBase->id);
                    Yii::$app->session->setFlash('danger', $msg);
                    return $this->render("showformulariosatisfaccion", ["data" => $data, "formulario" => false, 'banderaescalado' => false]);
                }

                /* CONSULTO LA TABLA DE RESPOSABILIDAD */
                $data->responsabilidad = ArrayHelper::map(
                                \app\models\Responsabilidad::find()
                                        ->where([
                                            'arbol_id' => $modelBase->pcrc,
                                        ])
                                        ->asArray()->all(), 'nombre', 'nombre', 'tipo'
                );

				/* ES ASESOR DE AMIGO */
                $data->esAsesor = true;
                return $this->render('showformulariosatisfaccion', [
                            'data' => $data,
							'view' => $view,
                            'formulario' => true,
                            'banderaescalado' => false
                ]);
            }

            /**
             * Funcion para buscar llamadas
             * 
             * @param sring $connid
             * @return boolean
             */
            public function actionBuscarllamadasmasivas($aleatorio) {
                //$aleatorio = Yii::$app->request->get();
                $arregloFiltro = Yii::$app->request->post('BaseSatisfaccionSearch');
                if ($arregloFiltro['fecha'] != '') {
                    $dates = explode(' - ', $arregloFiltro['fecha']);
                    $afecha = $dates[0] . ' 00:00:00';
                    $fecha = $dates[1] . ' 23:59:59';
                } else {
                    $fecha = date('Y-m-d H:i:s');
                    $afecha = strtotime('-2 month', strtotime($fecha));
                    $afecha = date('Y-m-d H:i:s', $afecha);
                }
                if ($aleatorio == "1") {
                    $allModels = BaseSatisfaccion::find()->where(['tipo_inbox' => 'ALEATORIO'])
                            ->andWhere('(llamada IS NULL OR buzon IS NULL)')
                            ->andWhere('fecha_satu BETWEEN "' . $afecha . '" AND "' . $fecha . '"');
                } else {
                    $allModels = BaseSatisfaccion::find()->where(['tipo_inbox' => 'NORMAL'])
                            ->andWhere('(llamada IS NULL OR buzon IS NULL)')
                            ->andWhere('fecha_satu BETWEEN "' . $afecha . '" AND "' . $fecha . '"');
                }

                if ($arregloFiltro['pcrc'] != '') {
                    $allModels->andFilterWhere([
                        'pcrc' => $arregloFiltro['pcrc'],]);
                }
                if ($arregloFiltro['responsable'] != '') {
                    $allModels->andFilterWhere([
                        'responsable' => $arregloFiltro['responsable'],]);
                }
                if ($arregloFiltro['estado'] != '') {
                    $allModels->andFilterWhere([
                        'estado' => $arregloFiltro['estado'],]);
                }
                if ($arregloFiltro['tipologia'] != '') {
                    $allModels->andFilterWhere([
                        'tipologia' => $arregloFiltro['tipologia'],]);
                }
                if ($arregloFiltro['id_lider_equipo'] != '') {
                    $allModels->andFilterWhere([
                        'id_lider_equipo' => $arregloFiltro['id_lider_equipo'],]);
                }
                if ($arregloFiltro['agente'] != '') {
                    $evaluado = \app\models\Evaluados::findOne(['id' => $arregloFiltro['agente']]);
                    $allModels->andFilterWhere([
                        'agente' => $evaluado->dsusuario_red,]);
                }
                try {
                    $allModels = $allModels->all();
                } catch (Exception $exc) {
                    \Yii::error('Error en consulta Masiva: *****' . $exc->getMessage(), 'redbox');
                }
                //$allModels = $allModels->all();        
                //$msgError = "";
                $count = 0;
                //$error = false;
                foreach ($allModels as $nModel) {
                    //SI BUSCAN LLAMADA
                    if (is_null($nModel->llamada)) {
                        $formularios = new \app\models\Formularios;

                        $server = Yii::$app->params["server"];
                        $user = Yii::$app->params["user"];
                        $pass = Yii::$app->params["pass"];
                        $db = Yii::$app->params["db"];

                        $idRel = $this->_consultDB($nModel->connid, $server, $user, $pass, $db);
                        $arrayLlamada = "";
                        if (!$idRel) {

                            //CONSULTA EN BD BOGOTA --------------------------------------------
                            $server = Yii::$app->params["serverBog"];
                            $user = Yii::$app->params["userBog"];
                            $pass = Yii::$app->params["passBog"];
                            $db = Yii::$app->params["dbBog"];

                            $idRel = $this->_consultDB($nModel->connid, $server, $user, $pass, $db);

                            if (is_numeric($idRel)) {
                                $wsdl = \Yii::$app->params["wsdl_redbox_bogota"];
                                $arrayLlamada = $formularios->getDataWS($idRel, $wsdl);
                            }
                        } else {
                            $wsdl = \Yii::$app->params["wsdl_redbox"];
                            $arrayLlamada = $formularios->getDataWS($idRel, $wsdl);
                        }

                        //Gaurdamos la llamada ---------------------------------------------
                        $nModel->llamada = (is_array($arrayLlamada) && count($arrayLlamada) >
                                0) ? json_encode($arrayLlamada) : null;
                    }

                    //SI BUSCAN BUZÓN
                    if (is_null($nModel->buzon) || empty($nModel->buzon) || $nModel->buzon == "") {

                        //Consulta de buzon ------------------------------------------------
                        $nModel->buzon = $this->_buscarArchivoBuzon(
                                sprintf("%02s", $nModel->dia) . "_" . sprintf("%02s", $nModel->mes) . "_" . $nModel->ano, $nModel->connid);
                    }

                    if (!is_null($nModel->llamada) || (!empty($nModel->buzon) || $nModel->buzon != "")) {
                        $count++;
                    }
                    try {
                        $nModel->save();
                    } catch (Exception $exc) {
                        \Yii::error('Error al momento de guardar el registro: ' . $nModel->id . ' ' . $exc->getMessage() . '#####', 'redbox');
                    }
                }
                $msg = \Yii::t('app', 'La operación se realizó con éxito para ' . $count . ' registros');
                Yii::$app->session->setFlash('warning', $msg);
                $redct = ($aleatorio == '1') ? 'inboxaleatorio' : 'index';
                return $this->redirect([$redct]);
            }

            /**
             * Action Recalcular la tipologia
             *             
             * @author Felipe Echeverri <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionRecalculartipologia() {

                //TRAIGO LAS ENCUESTAS SIN TIPOLOGIA
                $encuestas = BaseSatisfaccion::find()->select('id')->where("`tipologia` IS NULL")->all();

                if (empty($encuestas)) {
                    echo "No hay encuestas";
                    exit;
                }
                //MENSAJE DE CONTROL                               
                $errores = "";
                foreach ($encuestas as $encuesta) {
                    $model = BaseSatisfaccion::findOne($encuesta->id);
                    $model->tipologia = 'NEUTRO';

                    if (!empty($model->pcrc) && !empty($model->cliente)) {
                        $sql = '
        SELECT ca.nombre, dp.categoria, p.pre_indicador, 
            dp.configuracion, dp.addNA, cg.name, cg.prioridad, cg.id
        FROM tbl_detalleparametrizacion dp
        JOIN tbl_categoriagestion cg 
            ON dp.id_categoriagestion = cg.id
        JOIN tbl_parametrizacion_encuesta pe 
            ON pe.id = cg.id_parametrizacion
        LEFT JOIN tbl_preguntas p 
            ON p.id_parametrizacion = pe.id 
            AND p.categoria = dp.categoria
        JOIN tbl_categorias ca ON ca.id = dp.categoria 
        WHERE pe.cliente = ' . $model->cliente
                                . ' AND pe.programa = ' . $model->pcrc;
                        $config = \Yii::$app->db->createCommand($sql)->queryAll();

                        $prioridades = ArrayHelper::map($config, 'prioridad', 'name');
                        $arrayCumpleRegla = [];

                        if (count($config) > 0) {
                            $conditon = '';
                            $comando = '';
                            $i = 1;
                            $errorConfig = false;
                            //Validamos si hay una mala configuracion-------------------                    
                            foreach ($config as $value) {
                                if (is_null($value['pre_indicador']) || empty($value['pre_indicador'])) {
                                    $errorConfig = true;
                                    \Yii::error('Categoria (' . $value['nombre']
                                            . '), Cliente(' . $model->cliente0->name
                                            . ') Programa(' . $model->pcrc0->name
                                            . ')  mal configuada, Por favor revise la '
                                            . 'configuracion', 'basesatisfaccion');
                                }
                            }

                            if (!$errorConfig) {
                                foreach ($config as $key => $value) {
                                    if (!empty($value['configuracion'])) {
                                        $preExplode = explode('-', $value['configuracion']);
                                        $explode = explode('||', $preExplode[0]);
                                        if (isset($explode[0]) && isset($explode[1]) && isset($explode[2])) {

                                            if (str_replace(['(', ')'], '', $explode[0]) == BaseSatisfaccion::OP_AND) {
                                                if (isset($preExplode[1])) {
                                                    $explodeAddNA = explode('||', $preExplode[1]);
                                                    $tmpConditon = ' && ($model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explode[1]
                                                            . ' "'
                                                            . $explode[2] . '" ';
                                                    $tmpConditon .= ' || $model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explodeAddNA[1]
                                                            . ' "'
                                                            . $explodeAddNA[2] . '" )';
                                                } else {
                                                    $tmpConditon = ' && (is_numeric($model->' . $value['pre_indicador'] . ') && $model->'
                                                            . $value['pre_indicador']
                                                            . ' '
                                                            . $explode[1]
                                                            . ' "'
                                                            . $explode[2] . '") ';
                                                }
                                            } else {
                                                $tmpConditon = ' || $model->'
                                                        . $value['pre_indicador']
                                                        . ' '
                                                        . $explode[1]
                                                        . ' "'
                                                        . $explode[2] . '" ';
                                            }

                                            if (!isset($config[$i]['id']) || $value['id'] != $config[$i]['id']) {
                                                $conditon = substr($conditon, 4);
                                                if (!$conditon) {
                                                    $tmpConditon = substr($tmpConditon, 4);
                                                }
                                                $eval = '('
                                                        . $conditon
                                                        . $tmpConditon
                                                        . ')';
                                                $conditon = '';
                                                $comando .= '#####' . $eval;
                                                eval("\$restCond = $eval;");
                                                if ($restCond) {
                                                    $arrayCumpleRegla[] = 'true';
                                                    $model->tipologia = $value['name'];
                                                } else {
                                                    $arrayCumpleRegla[] = 'false';
                                                }
                                            } else {
                                                $conditon .= $tmpConditon;
                                            }
                                        }
                                    }
                                    $i++;
                                }

                                $contarValores = array_count_values($arrayCumpleRegla);

                                //Contamos el numero de true en $arrayCumpleRegla---------------
                                if (isset($contarValores['true']) && $contarValores['true'] > 1) {
                                    //sacamos el que tenga prioridad mas alta ------------------                    
                                    $model->tipologia = $prioridades[min(array_keys($prioridades))];
                                }
                            }
                        }
                    }



                    //GUARDAMOS LOS DATOS-----------------------------------------------
                    if (!$model->save()) {
                        $errores .= "<br />ID Encuesta: " . $encuesta->id . "<br />";
                    }
                }

                if (!empty($errores)) {
                    echo '<div style="background-color: #f2dede; '
                    . 'border-color: #ebccd1; color: #a94442;'
                    . ' padding: 10px;">'
                    . 'ENCUESTAS QUE NO PUDIERON SER RECALCULADAS: '
                    . $errores
                    . '</div>';
                } else {
                    echo '<div style="background-color: #dff0d8; '
                    . 'border-color: #d6e9c6; color: #3c763d;'
                    . ' padding: 10px;">'
                    . 'PROCESO TERMINADO CON &Eacute;XITO'
                    . '</div>';
                }
            }

            /**
             * Obtiene el listado de Metricas
             * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @param type $search
             * @param type $id
             */
            public function actionMetricalistmultipleform($search = null, $ids_selec = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }
                $out = ['more' => false];
                if (!is_null($search)) {

                    $data = \app\models\Textos::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                            ->where('detexto LIKE "%' . $search . '%"')
                            ->andWhere('id NOT IN (' . $ids_selec . ',11,12)')
                            ->orderBy('detexto')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    if (isset($_POST['ids_selec'])) {
                        $ids_selec.=$_POST['ids_selec'] . ',11,12';
                    } else {
                        $ids_selec = '11,12';
                    }
                    $data = \app\models\Textos::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                            ->where('id IN (' . $id . ')')
                            ->andWhere('id NOT IN (' . $ids_selec . ')')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            /**
             * Action para guardar y enviar el formulario
             *      
             * @return mixed
             * @author Felipe Echeverri <sebastian.orozco@ingeneo.com.co>
             * @copyright 2015 INGENEO S.A.S.
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionConsultarcalificacionsubi() {

                $arrCalificaciones = !$_POST['calificaciones'] ? array() : $_POST['calificaciones'];
                $arrTipificaciones = !isset($_POST['tipificaciones']) ? array() : $_POST['tipificaciones'];
                $arrSubtipificaciones = !isset($_POST['subtipificaciones']) ? array() : $_POST['subtipificaciones'];
                $arrComentariosSecciones = !$_POST['comentarioSeccion'] ? array() : $_POST['comentarioSeccion'];
                $arrCheckPits = !isset($_POST['checkPits']) ? array() : $_POST['checkPits'];
                $arrayForm = $_POST;
                $arrFormulario = [];
                /* Variables para conteo de bloques */
                $arrayCountBloques = [];
                $arrayBloques = [];
                $count = 0;
                /* fin de variables */
                $tmp_id = $_POST['tmp_formulario_id'];
                $basesatisfaccion_id = $_POST['basesatisfaccion_id'];
                $arrFormulario["dimension_id"] = $_POST['dimension'];
                $arrFormulario["dsruta_arbol"] = $_POST['ruta_arbol'];
                $arrFormulario["dscomentario"] = $_POST['comentarios_gral'];
                $arrFormulario["dsfuente_encuesta"] = $_POST['fuente'];
                $arrFormulario["transacion_id"] = $_POST['transacion_id'];
                $arrFormulario["sn_mostrarcalculo"] = 1;
                $modelBase = BaseSatisfaccion::findOne($basesatisfaccion_id);
                /* $modelBase->comentario = $arrFormulario["dscomentario"];
                  $modelBase->tipologia = $_POST['categoria'];
                  $modelBase->estado = $_POST['estado'];
                  $modelBase->usado = "NO";
                  $modelBase->responsabilidad = (isset($_POST['responsabilidad'])) ? $_POST['responsabilidad'] : "";
                  $modelBase->canal = (isset($_POST['canal'])) ? implode(", ", $_POST['canal']) : "";
                  $modelBase->marca = (isset($_POST['marca'])) ? implode(", ", $_POST['marca']) : "";
                  $modelBase->equivocacion = (isset($_POST['equivocacion'])) ? implode(", ", $_POST['equivocacion']) : "";
                  $modelBase->save(); */
                $arrFormulario["usua_id_lider"] = $_POST['form_lider_id'];
                $arrFormulario["equipo_id"] = $_POST['form_equipo_id'];
                //$arrFormulario["sn_mostrarcalculo"] = 1;
                //CONSULTA DEL FORMULARIO
                $data = \app\models\Tmpejecucionformularios::findOne($tmp_id);

                if ($_POST['subi_calculo'] != '') {
                    $data->subi_calculo .=',' . $_POST['subi_calculo'];
                    $data->save();
                }
                /*                 * if ($modelBase->tipo_inbox != 'NORMAL') {
                  $arrFormulario["dimension_id"] = 1;
                  } */
                //IF TODOS LOS BLOQUES ESTAN USADOS SETEO ARRAY VACIO
                if (!isset($arrayForm['bloque'])) {
                    $arrayForm['bloque'] = [];
                }
                
                /* INTENTO GUARDAR LOS FORMULARIOS */
                try {
                    /* EDITO EL TMP FORMULARIO */
                    $model = \app\models\Tmpejecucionformularios::find()->where(["id" => $tmp_id])->one();
                    $model->usua_id_actual = Yii::$app->user->identity->id;
                    $model->save();
                    \app\models\Tmpejecucionformularios::updateAll($arrFormulario, ["id" => $tmp_id]);
                    \app\models\Tmpejecucionsecciones::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);
                    \app\models\Tmpejecucionbloques::updateAll(['snna' => 0], ['tmpejecucionformulario_id' => $tmp_id]);

                    $bloquesFormtmp = \app\models\Tmpejecucionbloques::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                    foreach ($bloquesFormtmp as $bloquetmp) {
                        if (array_key_exists($bloquetmp->bloque_id, $arrayForm['bloque'])) {
                            $bloquetmp->snnousado = 1;
                            $bloquetmp->save();
                            $arrDetalleForm = [];
                            $arrDetalleForm["calificacion_id"] = -1;
                            $arrDetalleForm["calificaciondetalle_id"] = -1;
                            \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ['tmpejecucionformulario_id' => $tmp_id,
                                'bloque_id' => $bloquetmp->bloque_id]);
                        } else {
                            $bloquetmp->snnousado = 0;
                            $bloquetmp->save();
                        }
                    }
                    /* GUARDO LAS CALIFICACIONES */
                    foreach ($arrCalificaciones as $form_detalle_id => $calif_detalle_id) {
                        $arrDetalleForm = [];
                        //se valida que existan check de pits seleccionaddos y se valida
                        //que exista el del bloquedetalle actual para actualizarlo
                        if (count($arrCheckPits) > 0) {
                            if (isset($arrCheckPits[$form_detalle_id])) {
                                $arrDetalleForm["c_pits"] = $arrCheckPits[$form_detalle_id];
                            }
                        }
                        if (empty($calif_detalle_id)) {
                            $arrDetalleForm["calificaciondetalle_id"] = -1;
                        } else {
                            $arrDetalleForm["calificaciondetalle_id"] = $calif_detalle_id;
                        }
                        \app\models\Tmpejecucionbloquedetalles::updateAll($arrDetalleForm, ["id" => $form_detalle_id]);
                        $calificacion = \app\models\Tmpejecucionbloquedetalles::findOne(["id" => $form_detalle_id]);
                        $calificacionDetalle = \app\models\Calificaciondetalles::findOne(['id' => $calificacion->calificaciondetalle_id]);
                        //Cuento las preguntas en las cuales esta seleccionado el NA
                        //lleno $arrayBloques para tener marcados en que bloques no se selecciono el check
                        if (!in_array($calificacion->bloque_id, $arrayBloques) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                            $arrayBloques[] = $calificacion->bloque_id;
                            //inicio $arrayCountBloques
                            $arrayCountBloques[$count] = [($calificacion->bloque_id) => 1];
                            $count++;
                        } else {
                            if (count($arrayCountBloques) != 0) {
                                //actualizo $arrayCountBloques sumandole 1 cada q encuentra un NA de ese bloque
                                if ((array_key_exists($calificacion->bloque_id, $arrayCountBloques[count($arrayCountBloques) - 1])) && (strtoupper($calificacionDetalle->name) == 'NA')) {
                                    $arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] = ($arrayCountBloques[count($arrayCountBloques) - 1][$calificacion->bloque_id] + 1);
                                }
                            }
                        }
                    }
                    //$arrayCountBloques = call_user_func_array('array_merge', $arrayCountBloques);
                    //Actualizo los bloques en los cuales el total de sus preguntas esten seleccionadas en NA
                    foreach ($arrayCountBloques as $dato) {
                        $totalPreguntasBloque = \app\models\Tmpejecucionbloquedetalles::find()->select("COUNT(id) as preguntas")
                                        ->from("tbl_tmpejecucionbloquedetalles")
                                        ->where(['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)])->asArray()->all();
                        if ($dato[key($dato)] == $totalPreguntasBloque["0"]["preguntas"]) {
                            \app\models\Tmpejecucionbloques::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'bloque_id' => key($dato)]);
                        }
                    }
                    //actualizo las secciones, la cuales tienen todos sus bloques con la opcion snna en 1
                    $secciones = \app\models\Tmpejecucionsecciones::findAll(['tmpejecucionformulario_id' => $tmp_id]);
                    foreach ($secciones as $seccion) {
                        $bloquessnna = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                        ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                        ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                        ->where(['b.snna' => 1, 's.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                        ->groupBy("s.id")->asArray()->all();
                        $totalBloques = \app\models\Tmpejecucionformularios::find()->select("s.seccion_id AS id,COUNT(b.id) AS conteo")
                                        ->from("tbl_tmpejecucionformularios f")->join("LEFT JOIN", "tbl_tmpejecucionsecciones s", "s.tmpejecucionformulario_id = f.id")
                                        ->join("LEFT JOIN", "tbl_tmpejecucionbloques b", "b.tmpejecucionseccion_id=s.id")
                                        ->where(['s.seccion_id' => ($seccion->seccion_id), 'f.id' => $tmp_id])
                                        ->groupBy("s.id")->asArray()->all();
                        if (count($bloquessnna) > 0) {
                            if ($bloquessnna[0]['conteo'] == $totalBloques[0]['conteo']) {
                                \app\models\Tmpejecucionsecciones::updateAll(['snna' => 1], ['tmpejecucionformulario_id' => $tmp_id, 'seccion_id' => ($seccion->seccion_id)]);
                            }
                        }
                    }
                    /* GUARDO TIPIFICACIONES */
                    foreach ($arrTipificaciones as $form_detalle_id => $tipif_array) {
                        if (empty($tipif_array))
                            continue;

                        \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 0]
                                , ["tmpejecucionbloquedetalle_id" => $form_detalle_id]);

                        \app\models\TmpejecucionbloquedetallesTipificaciones::updateAll(["sncheck" => 1]
                                , "tmpejecucionbloquedetalle_id = '" . $form_detalle_id . "' "
                                . "AND tipificaciondetalle_id IN(" . implode(",", $tipif_array) . ")");
                    }

                    /* GUARDO SUBTIPIFICACIONES */
                    foreach ($arrSubtipificaciones as $form_detalle_id => $subtipif_array) {
                        $sql = "UPDATE `tbl_tmpejecucionbloquedetalles_subtipificaciones` a ";
                        $sql .= "INNER JOIN tbl_tmpejecucionbloquedetalles_tipificaciones b ";
                        $sql .= "ON a.tmpejecucionbloquedetalles_tipificacion_id = b.id ";
                        $sql .= "SET a.sncheck = 1 ";
                        $sql .= "WHERE b.tmpejecucionbloquedetalle_id = " . $form_detalle_id;
                        $sql .= " AND a.tipificaciondetalle_id IN (" . implode(",", $subtipif_array) . ")";
                        $command = \Yii::$app->db->createCommand($sql);
                        $command->execute();
                    }

                    foreach ($arrComentariosSecciones as $secc_id => $comentario) {

                        \app\models\Tmpejecucionsecciones::updateAll(["dscomentario" => $comentario]
                                , [
                            "seccion_id" => $secc_id
                            , "tmpejecucionformulario_id" => $tmp_id
                        ]);
                    }
                    //TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                    //$tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                    /* GUARDAR EL TMP FOMULARIO A LAS EJECUCIONES */
                    \app\models\Tmpejecucionformularios::guardarFormulario($tmp_id);
                    //$data->generarCalculos($tmp_id);
                    $modelBase->comentario = $arrFormulario["dscomentario"];
                    $modelBase->tipologia = $_POST['categoria'];
                    $modelBase->estado = $_POST['estado'];
                    $modelBase->usado = "NO";
                    $modelBase->responsabilidad = (isset($_POST['responsabilidad'])) ? $_POST['responsabilidad'] : "";
                    $modelBase->canal = (isset($_POST['canal'])) ? implode(", ", $_POST['canal']) : "";
                    $modelBase->marca = (isset($_POST['marca'])) ? implode(", ", $_POST['marca']) : "";
                    $modelBase->equivocacion = (isset($_POST['equivocacion'])) ? implode(", ", $_POST['equivocacion']) : "";
                    $modelBase->save();

                    Yii::$app->session->setFlash('success', Yii::t('app', 'Indices calculados'));
                    /* TODO: descomentar esta linea cuando se quiera usar las notificaciones a Amigo v1
                     * 
                      $modelEvaluado = \app\models\Evaluados::findOne(["id" => $tmp_ejecucion->evaluado_id]);
                      $ejecucion = \app\models\Ejecucionformularios::find()->where(['evaluado_id' => $tmp_ejecucion->evaluado_id, 'usua_id' => $tmp_ejecucion->usua_id])->orderBy('id DESC')->all();
                      $params = [];
                      $params['titulo'] = 'Te han realizado una valoración';
                      $params['pcrc'] = '';
                      $params['descripcion'] = '';
                      $params['notificacion'] = 'SI';
                      $params['muro'] = 'NO';
                      $params['usuariored'] = $modelEvaluado->dsusuario_red;
                      $params['cedula'] = '';
                      $params['plataforma'] = 'QA';
                      $params['url'] = '' . Url::to(['formularios/showformulariodiligenciadoamigo']) . '?form_id=' . base64_encode($ejecucion[0]->id);
                      $webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
                      $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                      if (!$webservicesresponse && $tmp_ejecucion == '') {
                      Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));
                      } */
                } catch (\Exception $exc) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception') . ": " . $exc->getMessage());
                }

                //REDIRECT CORRECTO
                return $this->redirect(['basesatisfaccion/showformulariogestion',
                            'basesatisfaccion_id' => $modelBase->id, 'preview' => 0, 'fill_values' => false, 'banderaescalado' => false]);
            }

        }
        