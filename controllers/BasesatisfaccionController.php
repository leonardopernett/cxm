<?php

namespace app\controllers;

ini_set('upload_max_filesize', '50M');

use Yii;
use DateTime;
use app\models\BaseSatisfaccion; 
use app\models\BaseSatisfaccionSearch;
use app\models\Desempeno;
use app\models\Despido;
use app\models\Permanencia;
use app\models\Notificaciones;
use app\models\Gestionpreguntas;
use app\models\Cargadesempeno;
use app\models\Alertas;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use app\models\Formularios;
use app\models\FormUpload;
use app\models\FormUploadalert;
use yii\web\UploadedFile;
use yii\data\ArrayDataProvider;
use app\models\UploadForm;
use app\models\Correogrupal;
use app\models\Controlcorreogrupal;
use app\models\Usuarios;
use app\models\UsuariosSearch;
use yii\base\Model;
use app\models\Reglanegocio;
use app\models\Equipos;
use app\models\UploadForm2;

/**
 * BaseSatisfaccionController implements the CRUD actions for BaseSatisfaccion model.
 */
class BasesatisfaccionController extends Controller {
    private $flagServer = false;

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
                                    'encuestatelefonica', 'usuariolist', 'usuariolista', 'view','pruebas',
                                    'formulariogestionsatisfaccion', 'getarbolesbypcrc',
                                    'guardarencuesta', 'index', 'reglanegocio',
                                    'showencuestatelefonica', 'update', 'guardarformulario', 'showsubtipif', 'cancelarformulario', 'declinarformulario',
                                    'reabrirformulariogestionsatisfaccion', 'clientebasesatisfaccion', 'limpiarfiltro', 'buscarllamadas', 'showformulariogestion',
                                    'guardaryenviarformulariogestion', 'eliminartmpform', 'buscarllamadasmasivas', 'recalculartipologia','consultarcalificacionsubi', 'metricalistmultipleform', 'cronalertadesempenolider', 'cronalertadesempenoasesor', 'showlistadesempenolider','correogrupal','prueba','actualizarcorreos','comprobacion','pruebaactualizar','comprobacionlista','importarencuesta','listasformulario','enviarvalencias','buscarllamadasbuzones'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos() || Yii::$app->user->identity->isHacerMonitoreo() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],						
							[
                                'actions' => ['getarbolesbypcrc'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isReportes() || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],
                            [
                                'actions' => ['inboxaleatorio', 'inboxdeclinadas', 'buscarllamadasmasivas','buscarllamadasbuzones'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isVerInboxAleatorio() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],
                            [
                                'actions' => ['showlistadesempenolider'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isVerDesempeno() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],
                            [
                                'actions' => ['showlistadesempenocompleto', 'gestionarpreguntas'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isVerAbogado();
                        },
                            ],
                            [
                                'actions' => ['upload'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isverTecDesempeno() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],
                            [
                                'actions' => ['alertas', 'alertasvaloracion', 'veralertas', 'getarbolesbypcrc'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isverAlertas() || Yii::$app->user->identity->isVerdirectivo();
                        },
                            ],
                            [
                                'actions' => ['showlistadesempenojefeop', 'solicitardespido', 'solicitarpermanencia'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isjefeOP();
                        },
                            ],
                            [
                                'actions' => ['baseinicial', 'showencuestaamigo', 'clientebasesatisfaccion',
                                    'showformulariogestionamigo',
                                    'controlinboxaleatorio', 'controlinboxtramos', 'borrarsegundocalificador', 'showlistadesempenoasesor', 'showalertadesempeno', 'controldesempeno', 'lidereslist', 'asesorlist', 'cedulalist'],
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
             * Lists all BaseSatisfaccion models.
             * @return mixed
             */
            public function actionInboxdeclinadas() {
                $searchModel = new BaseSatisfaccionSearch();
                $dataProvider = $searchModel->searchGestion("DECLINADA");

                Yii::$app->session['iboxPage'] = Yii::$app->request->url;

                if (isset(Yii::$app->session['searchInboxA'])) {
                    $searchModel->load(Yii::$app->session['searchInboxA']);
                    $dataProvider = $searchModel->searchGestion("DECLINADA");
                }
                if (Yii::$app->request->get('page')) {
                    $searchModel->load(Yii::$app->session['searchInboxA']);
                    $dataProvider = $searchModel->searchGestion("DECLINADA");
                }

                if ($searchModel->load(Yii::$app->request->post())) {
                    $dataProvider = $searchModel->searchGestion("DECLINADA");
                    Yii::$app->session['searchInboxA'] = Yii::$app->request->post();
                }

                return $this->render('index', [
                            'searchModel' => $searchModel,
                            'dataProvider' => $dataProvider,
                            'declinadas' => true,
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

            public function actionImportarencuesta(){
                $model = new UploadForm2();

                if (Yii::$app->request->isPost) {
                    $model->file = UploadedFile::getInstance($model, 'file');

                    if ($model->file && $model->validate()) {
                        $model->file->saveAs('avonencuestas/' . $model->file->baseName . '.' . $model->file->extension);

                        $fila = 1;
                        if (($gestor = fopen('avonencuestas/' . $model->file->baseName . '.' . $model->file->extension, "r")) !== false) {
                            while (($datos = fgetcsv($gestor)) !== false) {
                                $numero = count($datos);

                                $fila++;
                                for ($c=0; $c < $numero; $c++) { 
                                    $varArray = $datos[$c]; 
                                    $varDatos = explode(";", utf8_encode($varArray));
				    $varidnps = $varDatos[0];
                                    $varFechaHoraD = $varDatos[1];

                                    $varHora1 = null;
                                    $varHora2 = null;
                                    $varFechaHora = null;
                                    $varFechaAno = null;
                                    $varFechaMes = null;
                                    $varFechaDia = null;

                                    if (strlen($varFechaHoraD) >= 16) {
                                        // var_dump("Tarde");
                                        $varHora1 = substr($varFechaHoraD, -2);
                                        $varHora2 = substr($varFechaHoraD, -5,-3);
                                        $varFechaHora = $varHora2.$varHora1.'00';

                                        $varFechaAno = substr($varFechaHoraD, -10, -6);
                                        $varFechaMes = substr($varFechaHoraD, -13, -11);
                                        $varFechaDia = substr($varFechaHoraD, -16, -14);
                                    }else{
                                        // var_dump("Mañana");
                                        $varHora1 = substr($varFechaHoraD, -2);
                                        $varHora2 = '0'.substr($varFechaHoraD, -4,-3);
                                        $varFechaHora = $varHora2.$varHora1.'00';

                                        $varFechaAno = substr($varFechaHoraD, -9, -5);
                                        $varFechaMes = substr($varFechaHoraD, -12, -10);
                                        $varFechaDia = substr($varFechaHoraD, -15, -13);
                                    }
                                    
                                    $varIdentificacion = $varDatos[2];
                                    $varNombreCliente = $varDatos[3];
                                    
                                    //$varPcrc = Yii::$app->db->createCommand("select id from tbl_arbols where activo = 0 and arbol_id = 358 and name in ('120211-1 AVON SAC INFORMACIÓN GENERAL')")->queryScalar();
                                    $varPcrc = 3104;

                                    $varemail = $varDatos[4];
                                    $varAgente = $varDatos[5];
                                    $varnmusuario = $varDatos[6];
                                    $varuserseg = $varDatos[7];
                                    $varAni = $varDatos[8];
                                    $varRN = $varDatos[9];
                                    $varCodIndustria = Yii::$app->db->createCommand("select cod_industria from tbl_reglanegocio where rn = '$varRN' and cliente = 358 and pcrc = $varPcrc")->queryScalar();
                                    $varCodInstitucion = Yii::$app->db->createCommand("select cod_institucion from tbl_reglanegocio where rn = '$varRN' and cliente = 358 and pcrc = $varPcrc")->queryScalar();
                                    $varTipoServicio = 'telefónico';
                                    $varPregunta1 = $varDatos[10];
                                    $varPregunta = 'NO APLICA';
                                    $varConnid = '123abc';
                                    $varTipoEncuesta = 'M';
                                    $varnota_anterior = $varDatos[11];
                                    $varComentario = $varDatos[12];
				    $varTipologia = null;
                                    if($varPregunta1 >= 0 && $varPregunta1 <=6) {
                                        $varTipologia = 'CRITICA';
                                    }
                                    if($varPregunta1 > 6 && $varPregunta1 < 9) {
                                        $varTipologia = 'NEUTRO';
                                    }
                                    if($varPregunta1 > 8) {
                                        $varTipologia = 'FELICITACION';
                                    }
                                    //$varTipologia = $varDatos[8];
                                    $varEstado = 'Abierto';
                                    $varUsado = 'NO';
                                    $varGestionado = $varFechaAno .'/'.$varFechaMes.'/'.$varFechaDia.'/'.' '.$varHora2.':'.$varHora1.':01';
                                    $txtfechacreacion = date('Y-m-d');
                                    $hora = date("His");
                                    $varCreated = date('Y-m-d H:i:s');
                                    $varInbox = 'NORMAL';
                                    $varaliados = 'KNT';

                                    $varEvaluadoId = Yii::$app->db->createCommand("select distinct id from tbl_evaluados where dsusuario_red in ('$varAgente')")->queryScalar();
                                    $varIdLider = Yii::$app->db->createCommand("select distinct tbl_usuarios.usua_id from tbl_usuarios inner join tbl_equipos on tbl_usuarios.usua_id = tbl_equipos.usua_id inner join tbl_equipos_evaluados on tbl_equipos.id = tbl_equipos_evaluados.equipo_id where   tbl_equipos_evaluados.evaluado_id = $varEvaluadoId")->queryScalar();
                                    $varNomLider = Yii::$app->db->createCommand("select distinct tbl_usuarios.usua_nombre from tbl_usuarios inner join tbl_equipos on tbl_usuarios.usua_id = tbl_equipos.usua_id inner join tbl_equipos_evaluados on tbl_equipos.id = tbl_equipos_evaluados.equipo_id where   tbl_equipos_evaluados.evaluado_id = $varEvaluadoId")->queryScalar();
                                    $varCCLider = Yii::$app->db->createCommand("select distinct tbl_usuarios.usua_identificacion from tbl_usuarios inner join tbl_equipos on tbl_usuarios.usua_id = tbl_equipos.usua_id inner join tbl_equipos_evaluados on tbl_equipos.id = tbl_equipos_evaluados.equipo_id where   tbl_equipos_evaluados.evaluado_id = $varEvaluadoId")->queryScalar();
                                    

                                    Yii::$app->db->createCommand()->insert('tbl_base_satisfaccion',[
                                           'identificacion' => $varIdentificacion,
                                           'nombre' => $varNombreCliente,
                                           'ani' => $varAni,
                                           'agente' => $varAgente,
                                           'cc_agente' => null,
                                           'agente2' => null,
                                           'ano' => $varFechaAno,
                                           'mes' => $varFechaMes,
                                           'dia' => $varFechaDia,
                                           'hora' => $varFechaHora,
                                           'chat_transfer' => null,
                                           'ext' => null,
                                           'rn' => $varRN,
                                           'industria' => $varCodIndustria,
                                           'institucion' => $varCodInstitucion,
                                           'pcrc' => 358,
                                           'cliente' => $varPcrc,
                                           'tipo_servicio' => $varTipoServicio,
                                           'pregunta1' => $varPregunta1,
                                           'pregunta2' => $varPregunta,
                                           'pregunta3' => $varPregunta,
                                           'pregunta4' => $varPregunta,
                                           'pregunta5' => $varPregunta,
                                           'pregunta6' => $varPregunta,
                                           'pregunta7' => $varPregunta,
                                           'pregunta8' => $varPregunta,
                                           'pregunta9' => $varPregunta,
                                           'pregunta10' => $varPregunta,
                                           'connid' => $varConnid,
                                           'tipo_encuesta' => $varTipoEncuesta,
                                           'comentario' => $varComentario,
                                           'id_lider_equipo' => $varIdLider,
                                           'lider_equipo' => $varNomLider,
                                           'cc_lider' => $varCCLider,
                                           'coordinador' => null,
                                           'jefe_operaciones' => null,
                                           'tipologia' => $varTipologia,
                                           'estado' => $varEstado,
                                           'llamada' => null,
                                           'buzon' => null,
                                           'responsable' => null,
                                           'usado' => $varUsado,
                                           'fecha_gestion' => $varGestionado,
                                           'created' => $varCreated,
                                           'tipo_inbox' => $varInbox,
                                           'responsabilidad' => null,
                                           'canal' => null,
                                           'marca' => null,
                                           'equivocacion' => null,
                                           'fecha_satu' => $varCreated,
                                           'aliados' => $varaliados,
                                           'modalidad_encuesta' => null,
                                    ])->execute();

                    $varIdBase = Yii::$app->db->createCommand("select max(id) from tbl_base_satisfaccion where ano = $varFechaAno and mes = $varFechaMes and dia = $varFechaDia and hora = $varFechaHora and agente like '$varAgente' and identificacion like '$varIdentificacion'")->queryScalar();

                                    //$varFormulario = Yii::$app->db->createCommand("select id from tbl_formularios where name like '120211-1 AVON SAC INFORMACIÓN GENERAL'")->queryScalar();
                    $varFormulario = 4537;

                                    Yii::$app->db->createCommand()->insert('tbl_base_Avon',[
                                        'id' => $varIdBase, 
                                        'idarbol' => 358,
                                        'arbol_id' => $varPcrc,
                                        'idformulario' => $varFormulario,
                                        'reason3' => $varDatos[13],
                                        'reason4' => $varDatos[14],
                                        'etapas' => null,
                                        'idnps' => $varidnps,
                                        'email' => $varemail,
                                        'nmusuario' => $varnmusuario,
                                        'user_seg' => $varuserseg,
                                        'nota_anterior' => $varnota_anterior,
                                        'anulado' => 0,
                                        'fechacreacion' => date("Y-m-d"),
                                        'usua_id' => Yii::$app->user->identity->id,
                                    ])->execute();

                                }
                            }
                return $this->redirect('encuestatelefonica');
                            fclose($gestor);
                        }
                    }
                }

                return $this->renderAjax('importarencuesta',[
                    'model' => $model,
                ]);

            }
            public function actionListasformulario(){
                $txtvidformulario = Yii::$app->request->post("txtvidformulario");
                $txtvarbols = Yii::$app->request->post("txtvarbols");
                $txtvencuesta = Yii::$app->request->post("txtvencuesta");

                $varIdBaseAvon = Yii::$app->db->createCommand("select distinct idbaseavon from tbl_base_Avon where id = $txtvencuesta and arbol_id = $txtvarbols")->queryScalar();

                Yii::$app->db->createCommand()->update('tbl_base_Avon',[
                                          'formulario' => $txtvidformulario,
                                      ],'idbaseavon ='.$varIdBaseAvon.'')->execute(); 

                die(json_encode($varResultado));
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
             * Obtiene el listado de evaluadores-id
             * @param type $search
             * @param type $id
             */
            public function actionUsuariolista($search = null, $id = null) {
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
                    //agrego el usuario no definido solo para la visualizacion  en la inbox
                    //$data[] = ['id' => '1', 'text' => 'NO DEFINIDO'];
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Usuarios::find()
                            ->select(['id' => 'tbl_usuarios.usua_id', 'text' => 'UPPER(usua_nombre)'])
                            ->where('usua_usuario = "' . $id . '"')
                            ->asArray()
                            ->all();
                    //agrego el usuario no definido solo para la visualizacion  en la inbox
                    //$data[] = ['id' => '1', 'text' => 'NO DEFINIDO'];
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

            public function actionAsesorlist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.dsusuario_red', 'text' => 'UPPER(name)'])
                            ->where('name LIKE "%' . $search . '%"')
                            ->orderBy('name')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.dsusuario_red', 'text' => 'UPPER(name)'])
                            ->where('tbl_evaluados.id = ' . $id)
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

            public function actionCedulalist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    //print_r("matar"); die;
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.identificacion', 'text' => 'UPPER(identificacion)'])
                            ->where('identificacion LIKE "%' . $search . '%"')
                            ->orderBy('identificacion')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Evaluados::find()
                            ->select(['id' => 'tbl_evaluados.identificacion', 'text' => 'UPPER(identificacion)'])
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
                $modalidad_encuesta = '';
                $this->flagServer = false;
                if (empty($datos) || count($datos) < 1) {
                    return[
                        "codigo" => -1,
                        "mensaje" => "Debe ingresar datos de entrada"
                    ];
                }
                $model = new BaseSatisfaccion();
                $model->scenario = 'webservice';

                /*MODELO NUEVO PARA CLARO*/
                $modelClaro = new BaseSatisfaccion();
                $modelClaro->scenario = 'webservice';
                $datosClaro = $datos;
                unset($datos["url_buzon"]);
                unset($datos["url_llamada"]);
                unset($datos["modalidad_encuesta"]);

                //INGRESO LOS DATOS QUE ME LLEGARON POR EL IVR EN EL MODELO DE SATISFACCION KONECTA
                foreach ($datos as $key => $value) {
                    $model->$key = $value;
                }
                //BUSCO LA REGLA DE NEGOCIO PARA SABER SI EXITE ANTES DE CREAR EL REGISTRO
                $sql = "SELECT `pcrc`,`cliente`, `encu_diarias`, `encu_mes`, rango_encuestas, tramo1, tramo2, tramo3, tramo4, tramo5, tramo6, tramo7, tramo8, tramo9, tramo10, tramo11, tramo12, tramo13, tramo14, tramo15, tramo16, tramo17, tramo18, tramo19, tramo20, tramo21, tramo22, tramo23, tramo24
        FROM `tbl_reglanegocio` AS R 
        WHERE `rn` = '" . $model->rn . "'
        AND `cod_industria` = " . $model->industria . " 
        AND `cod_institucion`= " . $model->institucion . "
        LIMIT 1;";
                $validRn = \Yii::$app->db->createCommand($sql)->queryAll();
                //print_r($validRn); die;
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
                //print_r($validRn); die;
                //if($validRn[0]['pcrc'] == "694" || $validRn[0]['pcrc'] == "1024"){
        if($validRn[0]['pcrc'] == "694"){
                    $model->agente = "NULL";
                }
                //ALEATORIO-----------------------------------------------------
                //RANGO ENCUESTA


                // $rangoEncu = $validRn[0]['rango_encuestas'];

                // //TRAIGO TOTAL DE ENCUESTAS ALEATORIAS DEL DIA
                // $totBuzAleDia = BaseSatisfaccion::find()
                //         ->select("id")
                //         ->where([
                //             'tipo_inbox' => 'ALEATORIO',
                //             'pcrc' => $validRn[0]['pcrc'],
                //             'dia' => date('d'),
                //             'ano' => date('Y'),
                //             'mes' => date('m')
                //         ])
                //         ->all();
                // $totEncuAleDia = count($totBuzAleDia);

                // //TRAIGO TOTAL DE ENCUESTAS DEL DIA SIN IMPORTAR INBOX
                // $totBuzDia = BaseSatisfaccion::find()
                //         ->select("id")
                //         ->where([
                //             'pcrc' => $validRn[0]['pcrc'],
                //             'dia' => date('d'),
                //             'ano' => date('Y'),
                //             'mes' => date('m')
                //         ])
                //         ->all();
                // $totEncuDia = count($totBuzDia);

                // //TOTAL DE ENCUESTAS DEL MES
                // $totAle = BaseSatisfaccion::find()
                //         ->select("id")
                //         ->where([
                //             'tipo_inbox' => 'ALEATORIO',
                //             'pcrc' => $validRn[0]['pcrc'],
                //             'ano' => date('Y'),
                //             'mes' => date('m')
                //         ])
                //         ->all();

                // //SI ES MULTIPLO DEL RANGO DE HORAS Y MENOR Q EL LIMITE DEL DIA Y MES
                // if ($rangoEncu > 0) {
                //     if ((($totEncuDia + 1) % $rangoEncu == 0) &&
                //             $totEncuAleDia < $validRn[0]['encu_diarias'] &&
                //             count($totAle) < $validRn[0]['encu_mes'] &&
                //             ($model->tipo_encuesta == '' ||
                //             $model->tipo_encuesta == 'A')) {
                //         $model->tipo_inbox = 'ALEATORIO';
                //     }
                // }

                // //FIN ALEATORIO-------------------------------------------------
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

                //print_r($horaSatu); die; 

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
                //print_r($model->hora); die;

                if($datosClaro['modalidad_encuesta']){
                    if($datosClaro['modalidad_encuesta'] == 'V')
                        $model->tipo_servicio = 'SV';
                    else
                        $model->tipo_servicio = 'IVR';

                    $model->aliados = 'CLARO';
                    $model->buzon = $datosClaro['url_buzon'];
                    $model->llamada = $datosClaro['url_llamada'];
                    //$model->llamada = '[{"llamada":"' . $datosClaro['url_llamada'] . '"}]';
                    $model->modalidad_encuesta = $datosClaro['modalidad_encuesta'];
                    $this->flagServer = true;
                }
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

                    // \Yii::error($nModel->pcrc0->name, 'basesatisfaccion');
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
/* DESCOMENTAR HASTA LINEA 937 */
                    //Consulta de llamadas ----------------------------------------------
                    //Buscamos en medellin----------------------------------------------
                    $formularios = new \app\models\Formularios;

                    $server = Yii::$app->params["server"];
                    $user = Yii::$app->params["user"];
                    $pass = Yii::$app->params["pass"];
                    $db = Yii::$app->params["db"];
                    //print_r($nModel->connid ."serv". $server ."user". $user ."pass". $pass ."db". $db); die;
                if(!$this->flagServer){
                    $idRel = $this->_consultDB($nModel->connid, $server, $user, $pass, $db);
                    $arrayLlamada = "";
                    if (!$idRel) {

                        //CONSULTA EN BD BOGOTA --------------------------------------------
                        $server = Yii::$app->params["serverBog"];
                        $user = Yii::$app->params["userBog"];
                        $pass = Yii::$app->params["passBog"];
                        $db = Yii::$app->params["dbBog"];

                        //$idRel = $this->_consultDB($nModel->connid, $server, $user, $pass, $db);

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
                }
                else{//DLLO GERENCIA IVR BANCO
                    $agenteCL = Usuarios::find()->select(['usua_id','usua_identificacion'])->where(['trim(usua_usuario)'=> $nModel->agente])->one();
                    if(isset($agenteCL->usua_id))
                    {
                        $equipoCL = Equipos::find()->where(['usua_id'=> $agenteCL->usua_id])->all();
                    }
                    $nModel->pcrc = $validRn[0]['pcrc'];
                    $nModel->cliente = $validRn[0]['cliente'];
                    $nModel->id_lider_equipo = isset($equipoCL->id) ? $equipoCL->id : null;
                    $nModel->lider_equipo = isset($equipoCL->name) ? $equipoCL->name : null;
                    //$nModel->tipo_inbox = 'NORMAL'; //SOLO PARA PRUEBAS
                    $nModel->save();
                    // echo '<pre>';
                    // print_r($nModel);
                    // print_r($agenteCL);
                    // print_r($equipoCL);
                    // echo '</pre>';
                    // die;
                }          


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
                if (empty($usuario->usua_usuario)) {
                    
                }else{
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
                            $params['url'] = '../basesatisfaccion/showencuestaamigo?form_id=' . base64_encode($nModel->id);
                //Se comenta webservicesresponse para QA por caida de Amigo - 13-02-2019 -
                            //$webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
                            $webservicesresponse = null;
                            $tmp_basesatisfaccion = $nModel->id;
                            if (!$webservicesresponse && $tmp_basesatisfaccion == '') {
                              
                             
                            Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));                  
                            }
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
                            'basesatisfaccion_id' => $model->id, 'preview' => 0, 'fill_values' => false, 'banderaescalado' => false, 'aleatorio' => 3]);
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

            public function actionDeclinarformulario($id, $tmp_form = null) {

                $model = \app\models\BaseSatisfaccion::findOne($id);
                $model->tipo_inbox = "DECLINADA";
                $model->usado = "NO";
                //echo "<pre>";
                //print_r($model); die;
                $model->save();

                $inicio = "000000";
                $final = "010000" - "1";

                for ($x = 0; $x <= 24; $x++) {

                    if ($inicio <= $model["hora"] && $final >= $model["hora"]){
                        $x = 24;
                    }else{
                        $inicio = $inicio + "010000";
                        $final = $final + "010000";
                    }
                    
                }

                if($model['pcrc'] != "18" || $model['pcrc'] != '49' || $model['pcrc'] != '190' || $model['pcrc'] != '181' || $model['pcrc'] != '179' || $model['pcrc'] != '1673' || $model['pcrc'] != '29' || $model['pcrc'] != '2408' || $model['pcrc'] != '198' || $model['pcrc'] != '34' || $model['pcrc'] != '1927' || $model['pcrc'] != '1868' || $model['pcrc'] != '2450' || $model['pcrc'] != '2424' || $model['pcrc'] != '2320' || $model['pcrc'] != '119' || $model['pcrc'] != '335' || $model['pcrc'] != '1623' || $model['pcrc'] != '137' || $model['pcrc'] != '1971' || $model['pcrc'] != '289' || $model['pcrc'] != '287' || $model['pcrc'] != '2092' || $model['pcrc'] != '119' || $model['pcrc'] != '179' || $model['pcrc'] != '2327' || $model['pcrc'] != '2320' || $model['pcrc'] != '2328' || $model['pcrc'] != '293' || $model['pcrc'] != '153' || $model['pcrc'] != '2728' || $model['pcrc'] != '2727' || $model['pcrc'] != '2696' || $model['pcrc'] != '1716' || $model['pcrc'] != '192' || $model['pcrc'] != '2853'){

                    if ($model['cliente'] == "17" || $model['cliente'] == "118"){
                            // $where = " AND pregunta1 != 'NO APLICA' and pregunta2 !='NO APLICA' ";
                            $where = " AND pregunta1 != 'NO APLICA' ";
                        }else{
                            $where = " ";
                        }
                }else{
                            $where = " ";
                }
                    //echo "<pre>";
                //print_r($inicio);
                //echo "<pre>";
                //print_r($final); die;
                
// aca voy German91# toca traer la hora de la llamada que se acaba de declinar para traer la llamada en el mismo rango horario.


                $sql = "select id from tbl_base_satisfaccion where ano = ".$model["ano"]." AND mes = ".$model["mes"]." AND dia = ".$model["dia"]." AND LPAD(hora,6,'0') >= '".$inicio."' AND LPAD(hora,6,'0') <= '".$final."' AND pcrc = ".$model['pcrc']." AND tipo_inbox = 'NORMAL' ".$where." ORDER BY RAND() LIMIT 1;";
                //print_r($sql); die;
                $validRn = \Yii::$app->db->createCommand($sql)->queryAll();
                
                    if(count($validRn) == 0){
                            //print_r("no trajo nada"); die;
                            $msg = \Yii::t('app', 'No existe encuesta disponible para reemplazar');
                            Yii::$app->session->setFlash('warning', $msg);
                            return $this->redirect(Yii::$app->session['iboxPage']);
                        }else{
                    
                        $base = BaseSatisfaccion::findOne($validRn[0]['id']);
                        $base->tipo_inbox = 'ALEATORIO';
                        $base->update();
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
            , $pregunta10 = 'NO APLICA', $connid, $industria, $institucion, $tipo_encuesta = '', $url_buzon= '', $url_llamada= '', $modalidad_encuesta= '') { //DLLO GERENCIA IVR BANCO

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
                if ($tipo_encuesta != '' && $tipo_encuesta != "A" && $tipo_encuesta != "R" && $tipo_encuesta != "M" && $tipo_encuesta != "VH"  && $tipo_encuesta != "GA" && $tipo_encuesta != "GR" && $tipo_encuesta != "GM") {
                    $msg = "CODIGO: -1\r\n";
                    $msg .= "MENSAJE: Tipo de encuesta debe ser 'A', 'M' o 'R' o 'VH' o 'GA' o 'GR' o 'GM'";
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
                //print_r($hora); die;
                //ARMO EL ARRAY DE CONSULTA //DLLO GERENCIA IVR BANCO
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
                    'url_buzon' => $url_buzon, 
                    'url_llamada' => $url_llamada, 
                    'modalidad_encuesta' => $modalidad_encuesta
                ];

                /*Modalidad encuesta lo recibe T = Telefonico V = Virtual*/
                // echo '<pre>';
                // print_r($data);
                // echo '</pre>';
                // die;

                //CONSUMO EL SERVICIO WEB PARA INGRESAR LA ENCUESTA
                /* $wsdl = \yii\helpers\Url::toRoute('basesatisfaccion/baseinicial', true);
                  $client = new \SoapClient($wsdl);
                  $respuesta = $client->insertBasesatisfaccion($data); */

                $respuesta = $this->insertBasesatisfaccion($data); ////DLLO GERENCIA IVR BANCO

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
            public function actionShowformulariogestion($basesatisfaccion_id, $preview, $aleatorio = null, $fill_values, $view = "index",$banderaescalado = false, $idtmp = null) {

                $txtbasefuente = null;
                if ($preview == 5) {
                    $txtpreview = $basesatisfaccion_id;
                    $txtidbase = Yii::$app->db->createCommand("SELECT b.connid FROM tbl_base_satisfaccion b WHERE b.id = $txtpreview")->queryScalar();
                    $txtbasefuente = Yii::$app->db->createCommand("SELECT DISTINCT CONCAT(d.callId,', ',d.fechareal) AS dsfuente FROM tbl_dashboardspeechcalls d WHERE d.anulado = 0 AND d.connid = '$txtidbase'")->queryScalar();                    
                }

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
                // echo '<pre>';
                // print_r($modelReglaNegocio);
                // echo '</pre>';
                // die;
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
                    //echo "<pre>"
                    //print_r($validarEjecucionForm); die;
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
                           			   $TmpForm->dsfuente_encuesta = $txtbasefuente;
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
                           date_default_timezone_set('America/Bogota');
                           $TmpForm->hora_inicial = date("Y-m-d H:i:s");
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

                            if($validarTmpejecucionForm->hora_inicial == ""){
                                date_default_timezone_set('America/Bogota');
                                $validarTmpejecucionForm->hora_inicial = date("Y-m-d H:i:s");
                                $validarTmpejecucionForm->save();
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
                $data->aleatorio = $aleatorio == 1 ? true : false;
                $data->fill_values = $fill_values;
                //print_r($data); die;
                //VALIDO Q  LA REGLA DE NEGOCIO TENGA UN FORMULARIO ASOCIADO
                $form_val = \app\models\Formularios::findOne($modelReglaNegocio->id_formulario);
                //$TmpForm->subi_calculo = $form_val->subi_calculo;

                // $data->fecha_inicial = "";
                // $data->fecha_final = "";
                // $data->minutes = "";

                if($data->tmp_formulario->hora_inicial != "" AND $data->tmp_formulario->hora_final != ""){
                    $inicial = new DateTime($data->tmp_formulario->hora_inicial);
                    $final = new DateTime($data->tmp_formulario->hora_final);

                    $dteDiff  = $inicial->diff($final);

                    $dteDiff->format("Y-m-d H:i:s");

                    //print_r($dteDiff); die;

                    $data->fecha_inicial = $data->tmp_formulario->hora_inicial;
                    $data->fecha_final = $data->tmp_formulario->hora_final;

                    if ($dteDiff->h <= 9){
                        $hour = "0".$dteDiff->h;
                    }else{
                        $hour = $dteDiff->h;
                    }

                    if ($dteDiff->i <= 9){
                        $minute = "0".$dteDiff->i;
                    }else{
                        $minute = $dteDiff->i;
                    }

                    if ($dteDiff->s <= 9){
                        $seconds = "0".$dteDiff->s;
                    }else{
                        $seconds = $dteDiff->s;
                    }

                    $data->minutes = $hour . ":" . $minute . ":" . $seconds;
                }

                
                //echo "<pre>";
                //print_r($data); die;

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
                //echo "<pre>";
                //print_r($data->tmp_formulario->hora_inicial); die;
                //print_r($data); die;
                $varbuzon = $modelBase->buzon;
                $varConnids = $modelBase->connid;
                $vartexto = null;
                $varvalencia = null;
                $varcontenido = null;
                
                    ob_start();
                    $curl = curl_init();

                        curl_setopt_array($curl, array(
                            CURLOPT_SSL_VERIFYPEER=> false,
                            CURLOPT_SSL_VERIFYHOST => false,
                            CURLOPT_URL => 'https://api-kaliope.analiticagrupokonectacloud.com/status-by-connid',
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => '',
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 0,
                            CURLOPT_FOLLOWLOCATION => true,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => 'POST',
                            CURLOPT_POSTFIELDS =>'{"connid": "'.$varConnids.'"}',
                            CURLOPT_HTTPHEADER => array(
                              'Content-Type: application/json'
                                ),
                        ));

                    $response = curl_exec($curl);

                    curl_close($curl);
                    ob_clean();

                    if (!$response) {
                        $vartexto = "Error al buscar transcipcion";
                        $varvalencia = "Error al buscar valencia emocioanl";
                        $varcontenido = 0;
                    }

                    $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

                    if (count($response) == 0) {
                        $vartexto = "Transcripcion no encontrada";
                        $varvalencia = "Valencia emocional no encontrada";
                        $varcontenido = 0;
                    }else{
                        $vartexto = $response[0]['transcription'];
                        $varvalencia = $response[0]['valencia'];

                        if ($varvalencia == "NULL") {
                            $varvalencia = "Buz? sin informaci?";
                        }

                        $varverificaconnid = Yii::$app->db->createCommand("SELECT COUNT(connid) FROM tbl_kaliope_transcipcion k WHERE k.connid IN ('$varConnids')")->queryScalar();

                        if ($varverificaconnid == 0) { 
                            Yii::$app->db->createCommand()->insert('tbl_kaliope_transcipcion',[
                                'connid' => $varConnids,
                                'transcripcion' => $vartexto,
                                'valencia' => $varvalencia,
                                'fechagenerada' => $modelBase->fecha_satu,
                                'fechacreacion' => date("Y-m-d"),
                                'usua_id' => Yii::$app->user->identity->id,
                                'anulado' => 0,
                            ])->execute();
                        }

                        $varcontenido = 1;
                    } 


                return $this->render('showformulariosatisfaccion', [
                            'data' => $data,
                            'view' => $view,
                            'formulario' => true,
                            'banderaescalado' => false,
                            'vartexto' => $vartexto,
                            'varvalencia' => $varvalencia,
                            'preview' => $preview,
                            'varConnids' => $varConnids,
                            'varcontenido' => $varcontenido,
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
                date_default_timezone_set('America/Bogota');
                if($data['hora_final'] != ""){
                        $inicial = new DateTime($_POST['hora_modificacion']);
                        $final = new DateTime(date("Y-m-d H:i:s"));

                        $dteDiff  = $inicial->diff($final);

                        $dteDiff->format("Y-m-d H:i:s");

                        $tiempo_modificacion_actual = $dteDiff->h . ":" . $dteDiff->i . ":" . $dteDiff->s;

                        $data->cant_modificaciones = $data->cant_modificaciones + 1;

                        // $suma = strtotime($data->tiempo_modificaciones) + strtotime($tiempo_modificacion_actual);

                        // $suma1 = date("h:i:s", $suma); //01:57:48
                        $date = new DateTime($tiempo_modificacion_actual);
                        //print_r($data); die;
                        $suma2 = $this->sumarhoras($data->tiempo_modificaciones, $date->format('H:i:s'));
                        // //$data->tiempo_modificaciones = $dt->format('H:i:s');
                        // print_r("este: " . $data->tiempo_modificaciones . " mas : " . $tiempo_modificacion_actual . " es igual a : " .  $suma2); die;

                        $data->tiempo_modificaciones = $suma2;

                        $data->save();
                }else{
                    $pruebafecha = date("Y-m-d H:i:s");
                    $data->hora_final = $pruebafecha;
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
                            'basesatisfaccion_id' => $modelBase->id, 'preview' => 0, 'fill_values' => false, 'aleatorio' => 3, 'banderaescalado' => false]);
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
                      //Se comenta webservicesresponse  para QA por caida de Amigo - 13-02-2019 -
                      //$webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
                      $webservicesresponse = null;
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


            public function sumarhoras($hora1, $hora2){

                $hora1=explode(":",$hora1); 
                $hora2=explode(":",$hora2); 
                $temp=0; 
                 
                //sumo segundos 
                $segundos=(int)$hora1[2]+(int)$hora2[2]; 
                while($segundos>=60){         
                    $segundos=$segundos-60; 
                    $temp++; 
                } 
                     
                //sumo minutos 
                $minutos=(int)$hora1[1]+(int)$hora2[1]+$temp; 
                $temp=0; 
                while($minutos>=60){         
                    $minutos=$minutos-60; 
                    $temp++; 
                } 
                 
                //sumo horas 
                $horas=(int)$hora1[0]+(int)$hora2[0]+$temp; 
                 
                if($horas<10) 
                    $horas= '0'.$horas; 
                 
                if($minutos<10) 
                    $minutos= '0'.$minutos; 
                 
                if($segundos<10) 
                    $segundos= '0'.$segundos; 
                     
                $sum_hrs = $horas.':'.$minutos.':'.$segundos; 
                 
                return ($sum_hrs);
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
            public function actionControlinboxaleatorio($ano = NULL, $mes = NULL, $dia = NULL) {

                if (is_null($ano)) {
                    $ano = date('Y');
                }

                if (is_null($mes)) {
                    $mes = date('m');
                }

                if (is_null($dia)) {
                    $dia = date('d');
                }

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
                                        'mes' => $mes,
                                        'dia' => $dia,
                                        'ano' => $ano,
                                        'tipo_inbox' => 'ALEATORIO'
                                    ])
                                    ->count();
                    
                    //SI LAS ENCUESTAS DE HOY SON MENORES QUE LA CUOTA DEL DIA
                    if ($tot < $pcrc['encu_diarias']) {

                        //ENCUESTAS FALTANTES DEL DIA Y EL MES
                        $encuFaltantesDia = $pcrc['encu_diarias'] - $tot;
                        //Validar si hay  encuestas disponibles en el inbox General
                        $totNomalHoy = BaseSatisfaccion::find()
                                ->where([
                                    'pcrc' => $pcrc['pcrc'],
                                    'mes' => $mes,
                                    'dia' => $dia,
                                    'ano' => $ano,
                                    'tipo_inbox' => 'NORMAL',
                                    'estado' => 'Abierto'
                                ])
                                ->all();


                        $faltaron = count($totNomalHoy) - $encuFaltantesDia;

                        if ($faltaron > 0) {
                            $EncuestasMalas[] = [
                                'pcrc' => $nmPcrc,
                                'correos' => $pcrc['correos_notificacion'],
                                'encu_diarias_pcrc' => $pcrc['encu_diarias'],
                                'encu_diarias_totales' => $tot,
                                'encu_mes_pcrc' => $pcrc['encu_mes'],
                                'encu_mes_totales' => 0,
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
                                'encu_mes_totales' => 0,
                                'faltaron' => $encuFaltantesDia,
                                'disponibles' => count($totNomalHoy),
                                'estado' => 'AUTOCOMPLETA LA CUOTA',
                            ];
                        }

                        //PASO AL INBOX ALEATORIO MIESTRAS NO SUPERE EL NUMERO DE DIAS NI DE MES
                        foreach ($totNomalHoy as $value) {
                            
                            if (($pcrc['encu_diarias'] + $encuFaltantesDia) > $pcrc['encu_diarias']) {
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
                            'encu_mes_totales' => 0,
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
                
                $totNingunoHoy = BaseSatisfaccion::find()
                                ->where([
                                    'mes' => $mes,
                                    'dia' => $dia,
                                    'ano' => $ano,
                                    'tipo_inbox' => 'NINGUNO'
                                ])
                                ->all();

                foreach ($totNingunoHoy as $borrar) {
                                    //print_r("enbtro"); die;             
                                    $base = BaseSatisfaccion::findOne($borrar->id);
                                    $base->delete();
                            }


                $mensaje = "Culminado con exito";

                $MSG = '<div style="background-color: #dff0d8; '
                        . 'border-color: #d6e9c6; color: #3c763d;'
                        . ' padding: 10px;">';
                $MSG .= utf8_decode($mensaje);
                $MSG .= '</div>';
                echo $MSG;
            }

            /**
             * Lists all BaseSatisfaccion models.
             * @return mixed
             */
            public function actionBorrarsegundocalificador() {
                

                $sql = "DELETE FROM tbl_segundo_calificador where not exists (SELECT * from tbl_ejecucionformularios 
where tbl_segundo_calificador.id_ejecucion_formulario = tbl_ejecucionformularios.id);";

                $validRn = \Yii::$app->db->createCommand($sql)->query();
                    if(!$validRn){

                    $mensaje = "Ocurrio un error, Contacte con el administrador.";

                    $MSG = '<div style="background-color: #f0d8d8; '
                            . 'border-color: #e9c6c6; color: #763c3c;'
                            . ' padding: 10px;">';
                    $MSG .= utf8_decode($mensaje);
                    $MSG .= '</div>';
                    echo $MSG;
                }else{
                    $mensaje = "Error Segundo calificador Solucionado con exito.";

                    $MSG = '<div style="background-color: #dff0d8; '
                            . 'border-color: #d6e9c6; color: #3c763d;'
                            . ' padding: 10px;">';
                    $MSG .= utf8_decode($mensaje);
                    $MSG .= '</div>';
                    echo $MSG;
                }
            }


            /**
             * Metodo hacer control del inbox aleatorio por cada PCRC a las 11:59pm
             *                      
             * @author German Mejia Vieco <german.mejia@allus.com.co>
             * @copyright 2017 Konecta
             * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
             * @version Release: $Id$
             */
            public function actionControlinboxtramos($inihoratramo, $finhoratramo, $ano = NULL, $mes = NULL, $dia = NULL) {


                if (is_null($ano)) {
                    $ano = date('Y');
                }

                if (is_null($mes)) {
                    $mes = date('m');
                }

                if (is_null($dia)) {
                    $dia = date('d');
                }

                //ARRAY DE INFORMES
                $EncuestasMalas = $InformeGeneral = [];

                //TRAIGO TODAS LAS PCRC Y REGLAS DE NEGOCIO
                $pcrcs = \app\models\Reglanegocio::find()
                        ->joinWith('pcrc0')
                        ->groupBy('`pcrc`')
                        ->orderBy('`pcrc`')
                        ->asArray()
                        ->all();

                $tramo = "";
                
                //PARA CADA PCRC CALCULO SUS ENCUESTAS DIARIAS

                foreach ($pcrcs as $pcrc) {

                    switch(true) {
                        case ($inihoratramo >= '000000') && ($finhoratramo <= '010000'):
                            $tramo = $pcrc['tramo1'];
                            break;
                        case ($inihoratramo >= '010001') && ($finhoratramo <= '020000'):
                            $tramo = $pcrc['tramo2'];
                            break;
                        case ($inihoratramo >= '020001') && ($finhoratramo <= '030000'):
                            $tramo = $pcrc['tramo3'];
                            break;
                        case ($inihoratramo >= '030001') && ($finhoratramo <= '040000'):
                            $tramo = $pcrc['tramo4'];
                            break;
                        case ($inihoratramo >= '040001') && ($finhoratramo <= '050000'):
                            $tramo = $pcrc['tramo5'];
                            break;
                        case ($inihoratramo >= '050001') && ($finhoratramo <= '060000'):
                            $tramo = $pcrc['tramo6'];
                            break;
                        case ($inihoratramo >= '060001') && ($finhoratramo <= '070000'):
                            $tramo = $pcrc['tramo7'];
                            break;
                        case ($inihoratramo >= '070001') && ($finhoratramo <= '080000'):
                            $tramo = $pcrc['tramo8'];
                            break;
                        case ($inihoratramo >= '080001') && ($finhoratramo <= '090000'):
                            $tramo = $pcrc['tramo9'];
                            break;
                        case ($inihoratramo >= '090001') && ($finhoratramo <= '100000'):
                            $tramo = $pcrc['tramo10'];
                            break;
                        case ($inihoratramo >= '100001') && ($finhoratramo <= '110000'):
                            $tramo = $pcrc['tramo11'];
                            break;
                        case ($inihoratramo >= '110001') && ($finhoratramo <= '120000'):
                            $tramo = $pcrc['tramo12'];
                            break;
                        case ($inihoratramo >= '120001') && ($finhoratramo <= '130000'):
                            $tramo = $pcrc['tramo13'];
                            break;
                        case ($inihoratramo >= '130001') && ($finhoratramo <= '140000'):
                            $tramo = $pcrc['tramo14'];
                            break;
                        case ($inihoratramo >= '140001') && ($finhoratramo <= '150000'):
                            $tramo = $pcrc['tramo15'];
                            break;
                        case ($inihoratramo >= '150001') && ($finhoratramo <= '160000'):
                            $tramo = $pcrc['tramo16'];
                            break;
                        case ($inihoratramo >= '160001') && ($finhoratramo <= '170000'):
                            $tramo = $pcrc['tramo17'];
                            break;
                        case ($inihoratramo >= '170001') && ($finhoratramo <= '180000'):
                            $tramo = $pcrc['tramo18'];
                            break;
                        case ($inihoratramo >= '180001') && ($finhoratramo <= '190000'):
                            $tramo = $pcrc['tramo19'];
                            break;
                        case ($inihoratramo >= '190001') && ($finhoratramo <= '200000'):
                            $tramo = $pcrc['tramo20'];
                            break;
                        case ($inihoratramo >= '200001') && ($finhoratramo <= '210000'):
                            $tramo = $pcrc['tramo21'];
                            break;
                        case ($inihoratramo >= '210001') && ($finhoratramo <= '220000'):
                            $tramo = $pcrc['tramo22'];
                            break;
                        case ($inihoratramo >= '220001') && ($finhoratramo <= '230000'):
                            $tramo = $pcrc['tramo23'];
                            break;
                        case ($inihoratramo >= '230001') && ($finhoratramo <= '235959'):
                            $tramo = $pcrc['tramo24'];
                            break;

                    }

                    
                    //NOMBRE PCRC
                    $nmPcrc = $pcrc['pcrc0']['name'];

                    $sql2 = "select * from tbl_base_satisfaccion where ano = ".$ano." AND mes = ".$mes." AND dia = ".$dia." AND LPAD(hora,6,'0') >= '".$inihoratramo."' AND pcrc = '".$pcrc['pcrc']."' AND LPAD(hora,6,'0') <= '".$finhoratramo."' AND tipo_inbox = 'ALEATORIO';";
                    
                    $tot = \Yii::$app->db->createCommand($sql2)->queryAll();

                    if(count($tot) >= $tramo || $tramo == 0){

                        if($pcrc['pcrc'] != "18" ||  $pcrc['pcrc'] != '49' || $pcrc['pcrc'] != '190' || $pcrc['pcrc'] != '181' || $pcrc['pcrc'] != '179' || $pcrc['pcrc'] != '1673' || $pcrc['pcrc'] != '29' || $pcrc['pcrc'] != '2408' || $pcrc['pcrc'] != '198' || $pcrc['pcrc'] != '34' || $pcrc['pcrc'] != '1927' || $pcrc['pcrc'] != '1868' || $pcrc['pcrc'] != '2450' || $pcrc['pcrc'] != '2424' || $pcrc['pcrc'] != '2320' || $pcrc['pcrc'] != '119' || $pcrc['pcrc'] != '335' || $pcrc['pcrc'] != '1623' || $pcrc['pcrc'] != '137' || $pcrc['pcrc'] != '1971' || $pcrc['pcrc'] != '289' || $pcrc['pcrc'] != '287' || $pcrc['pcrc'] != '2092' || $pcrc['pcrc'] != '119' || $pcrc['pcrc'] != '179' || $pcrc['pcrc'] != '2327' || $pcrc['pcrc'] != '2320' || $pcrc['pcrc'] != '2328' || $pcrc['pcrc'] != '293' || $pcrc['pcrc'] != '153' || $pcrc['pcrc'] != '2728' || $pcrc['pcrc'] != '2727' || $pcrc['pcrc'] != '2696' || $pcrc['pcrc'] != '1716' || $pcrc['pcrc'] != '192' || $pcrc['pcrc'] != '2853'){
                            //print_r($pcrc); die;
                            
                            if ($pcrc['cliente'] == "17" || $pcrc['cliente'] == "118"){
                                $totNomalHoy2 = BaseSatisfaccion::find()
                                ->where([
                                    'pcrc' => $pcrc['pcrc'],
                                    'mes' => $mes,
                                    'dia' => $dia, // Cambiar para hacerlo del dia que yo quiera
                                    'ano' => $ano,
                                    'tipo_inbox' => 'NINGUNO'
                                ])
                                ->andWhere("`hora`>='".$inihoratramo."' AND `hora`<='".$finhoratramo."'")
                                ->andWhere("pregunta1 != 'NO APLICA'")
                                // ->andWhere("pregunta2 != 'NO APLICA'")
                                ->all(); 
                            }else{
                                $totNomalHoy2 = BaseSatisfaccion::find()
                                ->where([
                                    'pcrc' => $pcrc['pcrc'],
                                    'mes' => $mes,
                                    'dia' => $dia, // Cambiar para hacerlo del dia que yo quiera
                                    'ano' => $ano,
                                    'tipo_inbox' => 'NINGUNO'
                                ])
                                ->andWhere("`hora`>='".$inihoratramo."' AND `hora`<='".$finhoratramo."'")
                                ->all(); 
                            }
                        }else{
                            //print_r("2"); die;
                                $totNomalHoy2 = BaseSatisfaccion::find()
                                ->where([
                                    'pcrc' => $pcrc['pcrc'],
                                    'mes' => $mes,
                                    'dia' => $dia, // Cambiar para hacerlo del dia que yo quiera
                                    'ano' => $ano,
                                    'tipo_inbox' => 'NINGUNO'
                                ])
                                ->andWhere("`hora`>='".$inihoratramo."' AND `hora`<='".$finhoratramo."'")
                                ->all(); 
                            }

                                                           

                            foreach ($totNomalHoy2 as $value2) {
                                    //print_r("enbtro"); die;             
                                    $base = BaseSatisfaccion::findOne($value2->id);
                                    $base->tipo_inbox = 'NORMAL';
                                    $base->update();
                            }
                    }else{

                        $encuFaltantesDia = $tramo - count($tot);

                        $totNomalHoy = BaseSatisfaccion::find()
                                ->where([
                                    'pcrc' => $pcrc['pcrc'],
                                    'mes' => $mes,
                                    'dia' => $dia, // Cambiar para hacerlo del dia que yo quiera
                                    'ano' => $ano,
                                    'tipo_inbox' => 'NINGUNO'
                                ])
                                ->andWhere("`hora`>='".$inihoratramo."' AND `hora`<='".$finhoratramo."'")
                                ->all();
                        
                        if(count($totNomalHoy) != 0){

                            while($encuFaltantesDia != 0) {
                                if($pcrc['pcrc'] != "18" || $pcrc['pcrc'] != '49' || $pcrc['pcrc'] != '190' || $pcrc['pcrc'] != '181' || $pcrc['pcrc'] != '179' || $pcrc['pcrc'] != '1673' || $pcrc['pcrc'] != '29' || $pcrc['pcrc'] != '2408' || $pcrc['pcrc'] != '198' || $pcrc['pcrc'] != '34' || $pcrc['pcrc'] != '1927' || $pcrc['pcrc'] != '1868' || $pcrc['pcrc'] != '2450' || $pcrc['pcrc'] != '2424' || $pcrc['pcrc'] != '2320' || $pcrc['pcrc'] != '119' || $pcrc['pcrc'] != '335' || $pcrc['pcrc'] != '1623' || $pcrc['pcrc'] != '137' || $pcrc['pcrc'] != '1971' || $pcrc['pcrc'] != '289' || $pcrc['pcrc'] != '287' || $pcrc['pcrc'] != '2092' || $pcrc['pcrc'] != '119' || $pcrc['pcrc'] != '179' || $pcrc['pcrc'] != '2327' || $pcrc['pcrc'] != '2320' || $pcrc['pcrc'] != '2328' || $pcrc['pcrc'] != '293' || $pcrc['pcrc'] != '153'){

                                    if ($pcrc['cliente'] == "17" || $pcrc['cliente'] == "118"){
                                        // $where = " AND pregunta1 != 'NO APLICA' and pregunta2 !='NO APLICA' ";
                                        // $where2 = "pregunta1 != 'NO APLICA' and pregunta2 !='NO APLICA' ";
                                        $where = " AND pregunta1 != 'NO APLICA' ";
                                        $where2 = "pregunta1 != 'NO APLICA' ";
                                    }else{
                                        $where = " ";
                                        $where2 = " ";
                                    }
                                }else{
                                        $where = " ";
                                        $where2 = " ";
                                    }

                                $sql = "select id from tbl_base_satisfaccion where ano = ".$ano." AND mes = ".$mes." AND dia = ".$dia." AND pcrc = '".$pcrc['pcrc']."' AND LPAD(hora,6,'0') >= '".$inihoratramo."' AND LPAD(hora,6,'0') <= '".$finhoratramo."' AND tipo_inbox = 'NINGUNO' ".$where." ORDER BY RAND()
                                LIMIT 1;";

                                $validRn = \Yii::$app->db->createCommand($sql)->queryAll();

                                    if (($tramo + $encuFaltantesDia) != $tramo AND !empty($validRn)){
                                        $base = BaseSatisfaccion::findOne($validRn[0]['id']);
                                        $base->tipo_inbox = 'ALEATORIO';
                                        $base->update();
                                        $encuFaltantesDia--;
                                    } else {
                                        $encuFaltantesDia = 0;
                                    }
                            } 
                            if($pcrc['pcrc'] != "18"){
                                if ($pcrc['cliente'] == "17" || $pcrc['cliente'] == "118"){
                                    $totNomalHoy2 = BaseSatisfaccion::find()
                                    ->where([
                                        'pcrc' => $pcrc['pcrc'],
                                        'mes' => $mes,
                                        'dia' => $dia, // Cambiar para hacerlo del dia que yo quiera
                                        'ano' => $ano,
                                        'tipo_inbox' => 'NINGUNO'
                                    ])
                                    ->andWhere("`hora`>='".$inihoratramo."' AND `hora`<='".$finhoratramo."'")
                                    ->andWhere("pregunta1 != 'NO APLICA'")
                                    // ->andWhere("pregunta2 != 'NO APLICA'")
                                    ->all(); 
                                }else{
                                    $totNomalHoy2 = BaseSatisfaccion::find()
                                    ->where([
                                        'pcrc' => $pcrc['pcrc'],
                                        'mes' => $mes,
                                        'dia' => $dia, // Cambiar para hacerlo del dia que yo quiera
                                        'ano' => $ano,
                                        'tipo_inbox' => 'NINGUNO'
                                    ])
                                    ->andWhere("`hora`>='".$inihoratramo."' AND `hora`<='".$finhoratramo."'")
                                    ->all();
                                }
                            }else{
                                $totNomalHoy2 = BaseSatisfaccion::find()
                                ->where([
                                    'pcrc' => $pcrc['pcrc'],
                                    'mes' => $mes,
                                    'dia' => $dia, // Cambiar para hacerlo del dia que yo quiera
                                    'ano' => $ano,
                                    'tipo_inbox' => 'NINGUNO'
                                ])
                                ->andWhere("`hora`>='".$inihoratramo."' AND `hora`<='".$finhoratramo."'")
                                ->all();
                            }

                            foreach ($totNomalHoy2 as $value2) {
                                    //print_r("enbtro"); die;             
                                    $base = BaseSatisfaccion::findOne($value2->id);
                                    $base->tipo_inbox = 'NORMAL';
                                    $base->update();
                            }
                        }
                                                        
                    }
                }
                $mensaje = "Culminado con exito";

                $MSG = '<div style="background-color: #dff0d8; '
                        . 'border-color: #d6e9c6; color: #3c763d;'
                        . ' padding: 10px;">';
                $MSG .= utf8_decode($mensaje);
                $MSG .= '</div>';
                echo $MSG;
                
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
                $vartexto = null;
                $varvalencia = null;
                return $this->render('showformulariosatisfaccion', [
                            'data' => $data,
                            'view' => $view,
                            'formulario' => true,
                            'banderaescalado' => false,
                            'vartexto' => $vartexto,
                            'varvalencia' => $varvalencia,
                ]);
            }

            /**
             * Funcion para buscar llamadas
             * 
             * @param sring $connid
             * @return boolean
             */
            public function actionBuscarllamadasmasivas($aleatorio) {
                
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
                $count = 0;
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
             * Funcion para buscar Buzones
             * 
             * @param sring $connid
             * @return boolean
             */
            public function actionBuscarllamadasbuzones($aleatorio) {
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
                            ->andWhere('(buzon = "")')
                            ->andWhere('fecha_satu BETWEEN "' . $afecha . '" AND "' . $fecha . '"');
                } else {
                    $allModels = BaseSatisfaccion::find()->where(['tipo_inbox' => 'NORMAL'])
                            ->andWhere('(buzon = "")')
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
                $count = 0;
                foreach ($allModels as $nModel) {
                    
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
             * Funcion Para cargar, guardar, actualizar el desempeno de los asesores
             * * @author German Mejia Vieco
             */

            public function Importexcel($name)
            {
                $inputFile = 'archivos/' . $name . '.xls';
                try{
                    $inputFileType = \PHPExcel_IOFactory::identify($inputFile);
                    $objReader = \PHPExcel_IOFactory::createReader($inputFileType);
                    $objPHPExcel = $objReader->load($inputFile);

                }catch(Exception $e)
                {
                    die('Error');
                }

                $sheet = $objPHPExcel->getSheet(0);
                $highestRow = $sheet->getHighestRow();
                $highestcolumn = $sheet->getHighestColumn();

                for( $row = 1; $row <= $highestRow; $row++)
                {
                    $rowData = $sheet -> rangeToArray('A'.$row.':'.$highestcolumn.$row,NULL,TRUE,FALSE);

                    if($row == 1){
                        continue;
                    }


                    $desempeno= new Desempeno();
                    $mes = $rowData[0][0];
                    $ano = $rowData[0][1];
                    $usuario_red = $rowData[0][2];

                $desempenoValidar = $desempeno->validarExistencia($mes, $ano, $usuario_red);
                if(is_null($desempenoValidar)){
                    $desempenoValidar = new Desempeno();
                    $desempenoValidar->mes = $mes;
                    $desempenoValidar->ano = $ano;
                    $desempenoValidar->usuario_red = $usuario_red;
                    $desempenoValidar->usuario_carga = Yii::$app->user->identity->id;
                    $desempenoValidar->desempeno = $rowData[0][3];
                } else {
                    $desempenoValidar->usuario_carga = Yii::$app->user->identity->id;
                    $desempenoValidar->desempeno = $rowData[0][3];
                }
                if ($desempenoValidar->save()){
                                Yii::$app->session->setFlash('success', \Yii::t('app', 'Guardado con exito'));
                            }
                }
            }

            /**
             * Funcion Para cargar, guardar, actualizar el desempeno de los asesores
             * * @author German Mejia Vieco
             */

            public function actionUpload()
            {
                
                $model = new FormUpload;
                $msg = null;

                if ($model->load(Yii::$app->request->post()))
                {
                    $model->file = UploadedFile::getInstances($model, 'file');

                    if ($model->file && $model->validate()) {
                        foreach ($model->file as $file) {
                            $fecha = date('Y-m-d-h-i-s');
                            $user = Yii::$app->user->identity->username;
                            $name = $fecha . '-' . $user;
                            $file->saveAs('archivos/' . $name . '.' . $file->extension);
                            $this->Importexcel($name);

                            //Registro de subida
                            $modelup = new Cargadesempeno;
                            $modelup->ano = date("Y");
                            $modelup->mes = date("n");
                            $modelup->dia = date("j");
                            $modelup->responsable = Yii::$app->user->identity->username;
                            $modelup->nombre = $name . '.' . $file->extension;
                            $modelup->save();

                            $msg = "<div class='alert alert-success'>Carga Masiva realizada con exito.</div>";
                        }
                    }
                }
                return $this->render("upload", ["model" => $model, "msg" => $msg]);
            }


            /**
             * Cron de Alerta por desempeño a los asesores
             * * @author German Mejia Vieco
             */

            public function actionControldesempeno()
            {
                $fecha = date('Y-m');
                
                $nuevafecha1 = strtotime ( '-1 month' , strtotime ( $fecha ) ) ;
                $nuevafecha1 = date ( 'Y-m' , $nuevafecha1 );
                
                $separar1 = explode("-", $nuevafecha1);

                //print_r("mes: " . $separar1[0] . " ano: " . $separar1[1]); die;

                $totNomalHoy1 = desempeno::find()
                ->where([
                    'mes' => $separar1[1],
                    'ano' => $separar1[0]
                    ])
                ->andWhere("`desempeno`<='2'")
                ->all();
                //print_r($totNomalHoy1); die;
                //print_r("expression"); die;
                foreach ($totNomalHoy1 as $value1) {


                    $nuevafecha2 = strtotime ( '-2 month' , strtotime ( $fecha ) ) ;
                    $nuevafecha2 = date ( 'Y-m' , $nuevafecha2 );

                    $separar2 = explode("-", $nuevafecha2);


                    $totNomalHoy2 = desempeno::find()
                    ->where([
                        'mes' => $separar2[1],
                        'ano' => $separar2[0],
                        'usuario_red' => $value1->usuario_red
                        ])
                    ->andWhere("`desempeno`<='2'")
                    ->all();

                    if(count($totNomalHoy2) == 0){
                        $alert = 1;
                        $this->alertadesempeno($alert, $value1->usuario_red, $separar1[1], $separar1[0]);
                    }

                    foreach ($totNomalHoy2 as $value2) {
                        $nuevafecha3 = strtotime ( '-3 month' , strtotime ( $fecha ) ) ;
                        $nuevafecha3 = date ( 'Y-m' , $nuevafecha3 );

                        $separar3 = explode("-", $nuevafecha3);

                        $totNomalHoy3 = desempeno::find()
                        ->where([
                            'mes' => $separar3[1],
                            'ano' => $separar3[0],
                            'usuario_red' => $value2->usuario_red
                            ])
                        ->andWhere("`desempeno`<='2'")
                        ->all();

                        if (count($totNomalHoy3) == 0){
                            $alert = 2;
                            $this->alertadesempeno($alert, $value2->usuario_red, $separar2[1], $separar1[0]);
                        }

                        foreach ($totNomalHoy3 as $value3) {
                            $alert = 3;
                            $this->alertadesempeno($alert, $value3->usuario_red, $separar3[1], $separar1[0]);
                        }
                    }
                }
            }

            /**
             * notificacion a asesores y lideres
             * * @author German Mejia Vieco
             */

            public function alertadesempeno($alert, $usuario, $mes, $ano)
            {
                $iduser = \app\models\Evaluados::findOne(["dsusuario_red" => $usuario]);
                //print_r($iduser); die;
                
                    //$prueba = \app\models\desempeno::findOne(["usuario_red" => $usuario]);
                    //print_r("bu"); die;
                if ($iduser != ""){

                    $notificar = new Notificaciones();
                    
                    $idlider = \app\models\Equipos::find()
                            ->select('usua_id')
                            ->from('tbl_equipos')
                            ->join('JOIN', 'tbl_equipos_evaluados', 'tbl_equipos.id = tbl_equipos_evaluados.equipo_id')
                            ->where(['tbl_equipos_evaluados.evaluado_id' => $iduser['id']])
                            ->all();

                    $userlider = \app\models\Usuarios::findOne(['usua_id' => $idlider[0]['usua_id']]);
                    
                    //print_r($userlider['usua_usuario']); die;

                    $lider = $userlider['usua_usuario'];

                    $notificacion = $notificar->validarExistencia($mes, $ano, $usuario);
                    
                    if(is_null($notificacion)){
                        $notificacion = new Notificaciones();
                        $notificacion->asesor = $usuario;
                        $notificacion->lider = $lider;
                        $notificacion->ano = $ano;
                        $notificacion->mes = $mes;
                        $notificacion->notificacion = $alert;
                        $notificacion->fecha_ingreso = date("Y-m-d H:i:s");
                        $notificacion->save();
                    }

                    $idnoti = \app\models\Notificaciones::findOne(["asesor" => $usuario, "ano" => $ano, "mes" => $mes]);


                    $url = '../basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($idnoti['id']) . '&lider=no';
                    if($alert == 3){
                        $titulo = 'Acabas de recibir un pliego de cargos';
                    }else{                        
                        $titulo = 'Te han realizado una alerta por bajo desempeño';
                    }

                    $this->notificar($usuario, $titulo, $url); // Notificar usuario

                    if($alert == 1){
                        $titulo = 'Un asesor de tu grupo ha recibido una notificacion por bajo desempeño';
                        $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($idnoti['id']) . '&lider=si';
                        $this->notificar($lider, $titulo, $url); // Notificar Lider
                    }else if($alert == 2){
                        $titulo = 'Un asesor de tu grupo ha recibido una notificacion por bajo desempeño';
                        $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($idnoti['id']) . '&lider=si';
                        $this->notificar($lider, $titulo, $url); // Notificar Lider 
                    }else{
                        $titulo = 'Un asesor de tu grupo ha recibido un pliego de cargos';
                        $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($idnoti['id']) . '&lider=si';
                        $this->notificar($lider, $titulo, $url); // Notificar Lider
                        //$this->enviarcorreo($usuario, $lider, $url);
                    }


                    //Comentar
                    //print_r("el usuario: " . $usuario . " genero una alerta nivel: " . $alert . "<br/>");
                    //print_r("para el lider: " . $lider . " el titulo de la notificacion es: " . $titulo . "<br/>");
                    //print_r("con la url: " . $url . "<br/><br/>");
                }

            }

            /**
             * notificacion oficial
             * * @author German Mejia Vieco
             */

            public function notificar($usuario, $titulo, $url){
                $params = [];
                $params['titulo'] = $titulo;
                $params['pcrc'] = '';
                $params['descripcion'] = '';
                $params['notificacion'] = 'SI';
                $params['muro'] = 'NO';
                $params['usuariored'] = $usuario;
                $params['cedula'] = '';
                $params['plataforma'] = 'QA';
                $params['url'] = $url;
                //Se comenta webservicesresponse para QA por caida de Amigo - 13-02-2019 -
                //$webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
                $webservicesresponse = null;
                if (!$webservicesresponse) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));                  
                }
            }

            /**
             * Envio de correo, actualmente no se esta trabajando
             * * @author German Mejia Vieco
             */

            public function enviarcorreo($usuario, $lider, $url, $justificacion, $motivo){
                $html = "El coordinador: " . $lider . " ha solicitado ". $motivo ." de: " . $usuario . "para visualizar el seguimiento ir a: " . $url . " La justificacion es: " . $justificacion;
                //print_r($html); die;
                        Yii::$app->mailer->compose()
                        ->setTo(Yii::$app->params['email_reporte_desempeno'])
                        ->setFrom(Yii::$app->params['email_reporte_desempeno'])
                        ->setSubject('Solicitud Despido')
                        ->setHtmlBody($html)
                        ->send();
            }

            /**
             * notificacion a lideres por no gestion de alerta
             * * @author German Mejia Vieco
             */

            public function actionCronalertadesempenolider(){

                $fecha = date('Y-m');
                
                $nuevafecha = strtotime ( '-1 month' , strtotime ( $fecha ) ) ;
                $nuevafecha = date ( 'Y-m' , $nuevafecha );
                
                $separar = explode("-", $nuevafecha);

                $lideres = Notificaciones::find()
                    ->where([
                        'mes' => $separar[1],
                        'ano' => $separar[0],
                        'notificado_asesor' => "si"
                        ])
                    ->andWhere("`notificado_lider`='no'")
                    ->andWhere(['!=', 'notificacion', '3'])
                    ->all();

                foreach ($lideres as $value1) {
                    $titulo = "te queda poco tiempo para responder a esta alerta!!!";
                    $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($value1['id']) . '&lider=si';
                    $usuario = $value1['lider'];
                    $this->notificar($usuario, $titulo, $url);
                }
            }

            /**
             * notificacion a asesores por no gestion de alerta
             * * @author German Mejia Vieco
             */

            public function actionCronalertadesempenoasesor(){

                $fecha = date('Y-m');
                
                $nuevafecha = strtotime ( '-1 month' , strtotime ( $fecha ) ) ;
                $nuevafecha = date ( 'Y-m' , $nuevafecha );
                
                $separar = explode("-", $nuevafecha);

                $asesores = Notificaciones::find()
                    ->where([
                        'mes' => $separar[1],
                        'ano' => $separar[0]
                        ])
                    ->andWhere("`notificado_asesor`='no'")
                    ->andWhere(['!=', 'notificacion', '3'])
                    ->all();

                foreach ($asesores as $value1) {
                    $titulo = "te queda poco tiempo para responder a esta alerta!!!";
                    $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($value1['id']) . '&lider=no';
                    $usuario = $value1['lider'];
                    $this->notificar($usuario, $titulo, $url);
                }
            }

            /**
             * visualizacion de las alertas
             * * @author German Mejia Vieco
             */

            public function actionShowalertadesempeno($form_id, $lider, $jefeop = NULL) {
                
                $id = base64_decode($form_id);

                $model = new Notificaciones();

                $model = Notificaciones::findOne($id);

                $asesor = $model->asesor;

                $permanencia = \app\models\Permanencia::findOne(['p_id_notificacion' => $id]);

                $despido = \app\models\Despido::findOne(['d_id_notificacion' => $id]);


                $fecha = date('Y-m');
                
                $desempeno = new desempeno;

                $prueba = $desempeno->traerDatos($fecha, $model->asesor);

                $datos = \app\models\Evaluados::findOne(['dsusuario_red' => $asesor]);

                $preguntas = \app\models\Gestionpreguntas::find()->one();

                $nombrelider = \app\models\Usuarios::findOne(['usua_usuario' => $model->lider]);


                if ($model->load(Yii::$app->request->post())) {

                    $respuestas = Yii::$app->request->post('notificaciones');
                    // echo "<pre>";
                    // print_r($respuestas); die;
                    $model->id = $id;
                    if(isset($respuestas['respuesta_lider'])){
                        $model->respuesta_lider = $respuestas['respuesta_lider'];
                        $model->notificado_lider = "si";
                        $model->fecha_finalizacion = date("Y-m-d H:i:s");
                        $titulo = "El lider ha respondido a tu compromiso por bajo desempeño!!!";
                        $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($id) . '&lider=no';
                        $usuario = $model->asesor;
                        $this->notificar($usuario, $titulo, $url);
                    }

                    if(isset($respuestas['puntovista_lider'])){
                        $model->puntovista_lider = $respuestas['puntovista_lider'];


                    }

                    if(isset($respuestas['respuesta_asesor'])){
                        $model->respuesta_asesor = $respuestas['respuesta_asesor'];
                        $model->notificado_asesor = "si";
                    }

                    if($model->notificacion=="3"){
                        if(isset($respuestas['apregunta1'])){
                            $model->apregunta1 = $respuestas['apregunta1'];
                        }
                        if(isset($respuestas['apregunta2'])){
                            $model->apregunta2 = $respuestas['apregunta2'];
                        }
                        if(isset($respuestas['apregunta3'])){
                            $model->apregunta3 = $respuestas['apregunta3'];
                        }
                        if(isset($respuestas['apregunta4'])){
                            $model->apregunta4 = $respuestas['apregunta4'];
                        }
                        if(isset($respuestas['apregunta5'])){
                            $model->apregunta5 = $respuestas['apregunta5'];
                        }
                        if(isset($respuestas['apregunta6'])){
                            $model->apregunta6 = $respuestas['apregunta6'];
                        }
                        if(isset($respuestas['apregunta7'])){
                            $model->apregunta7 = $respuestas['apregunta7'];
                        }
                        if(isset($respuestas['apregunta8'])){
                            $model->apregunta8 = $respuestas['apregunta8'];
                        }


                        if(isset($respuestas['rac_meta'])){
                            $model->rac_meta = $respuestas['rac_meta'];
                        }
                        if(isset($respuestas['rac_pcrc'])){
                            $model->rac_pcrc = $respuestas['rac_pcrc'];
                        }
                        if(isset($respuestas['rac_cumple'])){
                            $model->rac_cumple = $respuestas['rac_cumple'];
                        }
                        if(isset($respuestas['meta'])){
                            $model->meta = $respuestas['meta'];
                        }
                        if(isset($respuestas['empleado'])){
                            $model->empleado = $respuestas['empleado'];
                        }
                        if(isset($respuestas['grupo'])){
                            $model->grupo = $respuestas['grupo'];
                        }
                        if(isset($respuestas['dif_empleado_meta'])){
                            $model->dif_empleado_meta = $respuestas['dif_empleado_meta'];
                        }
                        if(isset($respuestas['dif_empleado_grupo'])){
                            $model->dif_empleado_grupo = $respuestas['dif_empleado_grupo'];
                        }

                        $preguntas = \app\models\Gestionpreguntas::find()->one();

                        //print_r($preguntas->pregunta1); die;

                        $model->ppregunta1 = $preguntas->pregunta1;
                        $model->ppregunta2 = $preguntas->pregunta2;
                        $model->ppregunta3 = $preguntas->pregunta3;
                        $model->ppregunta4 = $preguntas->pregunta4;
                        $model->ppregunta5 = $preguntas->pregunta5;
                        $model->ppregunta6 = $preguntas->pregunta6;
                        $model->ppregunta7 = $preguntas->pregunta7;
                        $model->ppregunta8 = $preguntas->pregunta8;


                    }
                    // echo "<pre>";
                    // print_r($model);
                    // die;
                    if($model->save()){
                        if(isset($respuestas['respuesta_asesor'])){
                            $lider = $model['lider'];
                            $titulo = "Un asesor de tu grupo ha generado un compromiso por bajo desempeño";
                            $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($id) . '&lider=si';

                            $this->notificar($lider, $titulo, $url); // Notificar Lider
                        }

                    }

                    Yii::$app->session->setFlash('enviado');

                    return $this->render('showalertadesempeno', [

                        'data' => $model,
                        'lider' => $lider,
                        'jefeop' => $jefeop,
                        'permanencia' => $permanencia,
                        'despido' => $despido,
                        'prueba' => $prueba[0]['desempeno'],
                        'datos' => $datos,
                        'nombrelider' => $nombrelider,
                        'preguntas' => $preguntas,

                        ]);

                } else {

                    return $this->render('showalertadesempeno', [

                        'data' => $model,
                        'lider' => $lider,
                        'jefeop' => $jefeop,
                        'permanencia' => $permanencia,
                        'despido' => $despido,
                        'prueba' => $prueba[0]['desempeno'],
                        'datos' => $datos,
                        'nombrelider' => $nombrelider,
                        'preguntas' => $preguntas,

                        ]);

                }
            }

            public function actionSolicitardespido() {
                $justificacion = \Yii::$app->request->get('escalado');
                $asesor = \Yii::$app->request->get('asesor');
                $id = \Yii::$app->request->get('id');
                $motivo = \Yii::$app->request->get('motivo');

                $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($id) . '&lider=no&jefeop=si';
                //print_r($asesor . " justificacion: " . $justificacion); die;
                $coordinador = Yii::$app->user->identity->username;
                $despedir = new Despido();
                $despedir->d_id_notificacion = $id;
                $despedir->d_fecha_solicitud = date("Y-m-d H:i:s");
                $despedir->d_coordinador =  $coordinador;
                $despedir->d_asesor = $asesor;
                $despedir->d_justificacion = $justificacion;

                if($despedir->save()){
                    //Comentar
                    //print_r("Se solicita despido del asesor");
                    $this->enviarcorreo($asesor, $coordinador, $url, $justificacion, $motivo);

                    $model = Notificaciones::findOne($id);

                    $model->solicitud_despido = "si";

                    $model->save();

                }
            }

            public function actionSolicitarpermanencia() {
                $justificacion = \Yii::$app->request->get('escalado');
                $asesor = \Yii::$app->request->get('asesor');
                $id = \Yii::$app->request->get('id');
                $motivo = \Yii::$app->request->get('motivo');

                //print_r($justificacion); die;

                $url = '/qa_managementv2/web/index.php/basesatisfaccion/showalertadesempeno?form_id=' . base64_encode($id) . '&lider=no&jefeop=si';
                //print_r($asesor . " justificacion: " . $justificacion); die;

                $coordinador = Yii::$app->user->identity->username;
                $permanencia = new Permanencia();
                $permanencia->p_id_notificacion = $id;
                $permanencia->p_fecha_solicitud = date("Y-m-d H:i:s");
                $permanencia->p_coordinador =  $coordinador;
                $permanencia->p_asesor = $asesor;
                $permanencia->p_justificacion = $justificacion;

                if($permanencia->save()){
                    //Comentar
                    print_r("Se solicita permanencia del asesor"); die;
                    //$this->enviarcorreo($asesor, $coordinador, $url, $justificacion, $motivo);

                    $model = Notificaciones::findOne($id);

                    $model->solicitud_permanencia = "si";

                    $model->save();
                }
            }

            /**
             * visualizacion alertas lider
             * * @author German Mejia Vieco
             */

            public function actionShowlistadesempenolider() {

                $user = Yii::$app->user->identity->username;
                $model = new \app\models\Notificaciones();
                $dataProvider = $model->all();

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $prueba = Yii::$app->request->post();
                    $dates = explode(' - ', $prueba['notificaciones']['fecha_ingreso']);
                    
                    if($prueba['notificaciones']['fecha_ingreso'] != ""){
                        $fecha1 = $dates[0];
                        $fecha2 = $dates[1];
                    }else{
                        $fecha1 = "";
                        $fecha2 = "";
                    }

                    $asesor = $prueba['notificaciones']['asesor'];


                    $dataProvider = $model->all($fecha1, $fecha2, $asesor);
                    
                }


                return $this->render('listadesempeno', ['model' => $model, 'dataProvider' => $dataProvider]);
            }

            /**
             * visualizacion alertas asesor
             * * @author German Mejia Vieco
             */

            public function actionShowlistadesempenoasesor($evaluado_usuared) {
            

                $fecha1 = "";
                $fecha2 = "";
                $asesor = base64_decode($evaluado_usuared); 
                $tipo = "asesor";

                $model = new \app\models\Notificaciones();
                $dataProvider = $model->all($fecha1, $fecha2, $asesor, $tipo);

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $prueba = Yii::$app->request->post();
                    $dates = explode(' - ', $prueba['notificaciones']['fecha_ingreso']);
                    
                    if($prueba['notificaciones']['fecha_ingreso'] != ""){
                        $fecha1 = $dates[0];
                        $fecha2 = $dates[1];
                    }

                    $dataProvider = $model->all($fecha1, $fecha2, $asesor, $tipo);
                    
                }

                return $this->render('listadesempenoasesor', ['model' => $model, 'dataProvider' => $dataProvider]);

            }

            /**
             * visualizacion alertas departamento juridico
             * * @author German Mejia Vieco
             */

            public function actionShowlistadesempenocompleto() {
            

                $fecha1 = "";
                $fecha2 = "";
                $asesor = ""; 
                $tipo = "asesor";
                $lider = "";
                $identificacion = "";

                $model = new \app\models\Notificaciones();
                $dataProvider = $model->all($fecha1, $fecha2, $asesor, $tipo, $lider);

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    $prueba = Yii::$app->request->post();
                    //print_r($prueba); die;
                    $dates = explode(' - ', $prueba['notificaciones']['fecha_ingreso']);
                    
                    if($prueba['notificaciones']['fecha_ingreso'] != ""){
                        $fecha1 = $dates[0];
                        $fecha2 = $dates[1];
                    }

                    if($prueba['notificaciones']['lider'] != ""){
                        $lider = $prueba['notificaciones']['lider'];
                    }

                    if($prueba['notificaciones']['Identificacion'] != ""){
                        $identificacion = $prueba['notificaciones']['Identificacion'];
                    }


                    $dataProvider = $model->all($fecha1, $fecha2, $asesor, $tipo, $lider, $identificacion);
                    
                }

                //echo "<pre>";
                //print_r($model); die;
                return $this->render('listadesempenocompleto', ['model' => $model, 'dataProvider' => $dataProvider]);

            }

            /**
             * visualizacion alertas coordinador
             * * @author German Mejia Vieco
             */

            public function actionShowlistadesempenojefeop() {
            
                $idcoor = Yii::$app->user->identity->id;
                $arboles = \app\models\ArbolsUsuarios::find()->where(['usua_id' => $idcoor])->all();
                $lodeinArr = array();
                $lodein = "";
                $fecha1 = "";
                $fecha2 = "";
                $asesor = "";

                foreach ($arboles as $arb) {
                    $arbol = $arb['arbol_id'];

                    $equipos = \app\models\ArbolsEquipos::find()->where(['arbol_id' => $arbol])->all();
                    
                    foreach ($equipos as $eqp) {
                        $idequipo = $eqp['equipo_id'];

                        $lider = \app\models\Equipos::find()->where(['id' => $idequipo])->all();

                        foreach ($lider as $ldr) {

                            $usuario = \app\models\Usuarios::find()->where(['usua_id' => $ldr['usua_id']])->all();
                            $lodein .= "'" . $usuario[0]['usua_usuario'] . "', ";
                            array_push($lodeinArr, $usuario[0]['usua_usuario']);
                        }
                    }



                }

                $lodein .= "'asdas'";


                $model = new \app\models\Notificaciones();
                $dataProvider = $model->coordinador($lodeinArr);

                 // echo "<pre>";
                 // print_r($dataProvider); die;

                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    //print_r(Yii::$app->request->post()); die;

                    $prueba = Yii::$app->request->post();
                    $dates = explode(' - ', $prueba['notificaciones']['fecha_ingreso']);
                    
                    if($prueba['notificaciones']['fecha_ingreso'] != ""){
                        $fecha1 = $dates[0];
                        $fecha2 = $dates[1];
                    }

                    if($prueba['notificaciones']['asesor'] != ""){
                        $asesor = $prueba['notificaciones']['asesor'];
                    }

                    $dataProvider = $model->coordinador($lodeinArr, $fecha1, $fecha2, $asesor);

                }

                return $this->render('listadesempenojefeop', ['model' => $model, 'dataProvider' => $dataProvider]);

            }

            /**
             * listar lideres
             * * @author German Mejia Vieco
             */

            public function actionLidereslist($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Equipos::find()
                            ->select(['id' => 'tbl_usuarios.usua_usuario', 'text' => 'UPPER(usua_nombre)'])
                            ->join('JOIN', 'tbl_usuarios', 'tbl_usuarios.usua_id = tbl_equipos.usua_id')
                            ->where('usua_nombre LIKE "%' . $search . '%"')
                            ->groupBy('id')
                            ->orderBy('usua_nombre')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Equipos::find()
                            ->select(['id' => 'tbl_usuarios.usua_usuario', 'text' => 'UPPER(usua_nombre)'])
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
             * Gestion Preguntas
             * * @author German Mejia Vieco
             */

            public function actionGestionarpreguntas() {

                $user = Yii::$app->user->identity->username;
                //$model = new \app\models\Gestionpreguntas();
                $model = \app\models\Gestionpreguntas::find()->one();

                //$model = \app\models\Gestionpreguntas::find()->where(['id' => 1])->all();
                //print_r($model->id); die;
                //$model = new \app\models\Gestionpreguntas();


                if ($model->load(Yii::$app->request->post())) {
                    
                    $preguntas = Yii::$app->request->post();
//$respuestas = Yii::$app->request->post('notificaciones');
                    //print_r($preguntas['gestionpreguntas']['pregunta1']); die;

                    $model->pregunta1 = $preguntas['gestionpreguntas']['pregunta1'];
                    $model->pregunta2 = $preguntas['gestionpreguntas']['pregunta2'];
                    $model->pregunta3 = $preguntas['gestionpreguntas']['pregunta3'];
                    $model->pregunta4 = $preguntas['gestionpreguntas']['pregunta4'];
                    $model->pregunta5 = $preguntas['gestionpreguntas']['pregunta5'];
                    $model->pregunta6 = $preguntas['gestionpreguntas']['pregunta6'];
                    $model->pregunta7 = $preguntas['gestionpreguntas']['pregunta7'];
                    $model->pregunta8 = $preguntas['gestionpreguntas']['pregunta8'];

                    if($model->save()){
                        Yii::$app->session->setFlash('enviado');
                        
                        return $this->render('gestionpreguntas', ['model' => $model]);
                    }

                    
                }


                return $this->render('gestionpreguntas', ['model' => $model]);
            }







            /** Desempeño German Mejia Vieco **/



            /** Alertas German Mejia Vieco **/

            /**
             * Creacion Alertas
             * * @author German Mejia Vieco
             */

            public function actionAlertas() {

                $model = new UploadForm();

                $searchModel = new BaseSatisfaccionSearch();
                $listo = 0;

                if (Yii::$app->request->isPost) {
                    //print_r(Yii::$app->request->post('remitentes')); die;
                    $model->archivo_adjunto = UploadedFile::getInstances($model, 'archivo_adjunto');
                    $user = Yii::$app->user->identity->username;
                    $archivo = date("YmdHis") . $user . str_replace(' ', '', $model->archivo_adjunto['0']->name); 
                    //print_r($archivo); die;
                    if ($model->upload()) {
                // file is uploaded successfully

                            $modelup = new Alertas();

                            
                            $modelup->fecha = date("Y-m-d H:i:s");
                            $modelup->pcrc = Yii::$app->request->post('BaseSatisfaccionSearch')['pcrc'];
                            $modelup->valorador = Yii::$app->user->identity->id;
                            $modelup->tipo_alerta = Yii::$app->request->post('tipo_alerta');
                            $modelup->archivo_adjunto = $archivo;
                            $modelup->remitentes = Yii::$app->request->post('remitentes');
                            $modelup->asunto = Yii::$app->request->post('asunto');
                            $modelup->comentario = Yii::$app->request->post('comentario');

                            $listo = 1;

                            $this->enviarcorreoalertas($modelup->fecha, $modelup->pcrc, $modelup->valorador, $modelup->tipo_alerta, $modelup->archivo_adjunto, $modelup->remitentes, $modelup->asunto, $modelup->comentario);

                            $modelup->save();
                            return $this->render('alertas', [
                            'searchModel' => $searchModel,
                            'model' => $model,
                            'listo' => $listo,
                ]);
                    }else{
                        
                        $listo = 2;
                    }
                }

                return $this->render('alertas', [
                            'searchModel' => $searchModel,
                            'model' => $model,
                            'listo' => $listo,
                ]);
            }

            /**
             * Envio de correo para alertas
             * * @author German Mejia Vieco
             */

            public function enviarcorreoalertas($fecha, $pcrc, $valorador, $tipo_alerta, $archivo_adjunto, $remitentes, $asunto, $comentario){

                //$fecha, $pcrc, $valorador, $tipo_alerta, $archivo_adjunto, $remitentes, $asunto, $comentario

                $equipos = \app\models\Arboles::find()->where(['id' => $pcrc])->all();
                $usuario = \app\models\Usuarios::find()->where(['usua_id' => $valorador])->all();
                //echo "<pre>";
                //print_r($usuario['0']->usua_nombre); die;

                //print_r($remitentes); 
                $destinatario = explode(",", $remitentes); 


                $target_path = "alertas/" . $archivo_adjunto;
                //print_r($target_path); die;

                $sessiones = Yii::$app->user->identity->id;

                $varNombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = '$sessiones'")->queryScalar();
                $varCorreo = Yii::$app->db->createCommand("select usua_email from tbl_usuarios where usua_id = '$sessiones'")->queryScalar();

            $html = "
            Correo enviado por: ".$varNombre." con correo: ".$varCorreo."
            <br>
            <br>
            <br>
<table align='center' border='2'>
                <tr>
                    <th style='padding: 10px;'>Fecha de Envio</th>
                    <th style='padding: 10px;'>Programa</th>
                    <th style='padding: 10px;'>Valorador</th>
                    <th style='padding: 10px;'>Tipo de Alerta</th>
                    <th style='padding: 10px;'>Asunto</th>
                    <th style='padding: 10px;'>Comentario</th>
                </tr>
                <tr>
                    <td style='padding: 10px;'>" . $fecha . "</td>
                    <td style='padding: 10px;'>" . $equipos['0']->name . "</td>
                    <td style='padding: 10px;'>" . $usuario['0']->usua_nombre . "</td>
                    <td style='padding: 10px;'>" . $tipo_alerta . "</td>
                    <td style='padding: 10px;'>" . $asunto  . "</td>
                    <td style='padding: 10px;'>" . $comentario  . "</td>
                </tr>
            </table>";

            //print_r($html); die;

                //$html = $comentario;

                foreach ($destinatario as $send) {
                    Yii::$app->mailer->compose()
                        ->setTo($send)
                        ->setFrom(Yii::$app->params['email_satu_from'])
                        ->setSubject($asunto)
                        ->attach($target_path)
                        ->setHtmlBody($html)
                        ->send();
                }
                //print_r($html); die;
                        
            }

            /** Alertas German Mejia Vieco **/

            /**
             * Creacion Alertas
             * * @author German Mejia Vieco
             */

            public function actionAlertasvaloracion() {


                if(Yii::$app->request->get('prueba') == "exportar"){
                    
                }else{
                    
                }

                
                $model = new BaseSatisfaccionSearch();


                //$model->scenario = 'reporte';
                $dataProvider = (new \yii\db\Query())
                            ->select('a.id as xid, fecha, b.name AS Programa, tipo_alerta, d.usua_nombre AS Tecnico')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            //->andWhere('valorador ="' . $responsable . '"')
                ->orderBy(['fecha' => SORT_DESC])
                            ->all();

                $resumenFeedback = (new \yii\db\Query())
                            ->select('b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->groupBy('a.pcrc')
                            ->all();
                            

                $detalleLiderFeedback = (new \yii\db\Query())
                            ->select('d.usua_nombre AS Tecnico, b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            ->groupBy('a.valorador, a.pcrc')
                            //->andWhere('valorador ="' . $responsable . '"')
                            ->all();


                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    //print_r(Yii::$app->request->post('BaseSatisfaccionSearch')); die;

                    $post = Yii::$app->request->post('BaseSatisfaccionSearch');

                    //print_r($post['responsable']); die;

                    $fecha = $post['fecha'];
                    $pcrc = $post['pcrc'];
                    $responsable = $post['responsable'];

                    if ($fecha != ""){
                        //print_r("entro"); die;
                        $dates = explode(' - ', $fecha);
                        $startDate = $dates[0];
                        $endDate = $dates[1];
                        $xfecha = 'date(a.fecha) BETWEEN "' . $startDate . '" AND "' . $endDate . '"';
                        //print_r($where); die;
                    }else{
                        $xfecha = "";
                    }

                    if($pcrc != ""){
                        $xpcrc = 'pcrc ="' . $pcrc . '"';
                    }else{
                        $xpcrc = "";
                    }

                    if($responsable != ""){
                        $xresponsable = 'a.valorador ="' . $responsable . '"';
                    }else{
                        $xresponsable = "";
                    }



                    //print_r("la fecha inicial : " . $startDate . " la fecha final : " . $endDate . " el pcrc es : " . $pcrc . " el responsable es : " . $responsable); die;
                    //print_r($endDate); die;
                    $detalleLiderFeedback = (new \yii\db\Query())
                            ->select('d.usua_nombre AS Tecnico, b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            ->andWhere($xfecha)
                            ->andWhere($xpcrc)
                            ->andWhere($xresponsable)
                            ->groupBy('a.valorador, a.pcrc')
                            //->andWhere('valorador ="' . $responsable . '"')
                            ->all();




                    //print_r($detalleLiderFeedback); die;

                    $resumenFeedback = (new \yii\db\Query())
                            ->select('b.name AS Programa, c.name AS Cliente, count(a.pcrc) AS Count')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_arbols c', 'b.arbol_id = c.id')
                            ->andWhere($xfecha)
                            ->andWhere($xpcrc)
                            ->andWhere($xresponsable)
                            ->groupBy('a.pcrc')
                            //->andWhere('valorador ="' . $responsable . '"')
                            ->all();

                    $dataProvider = (new \yii\db\Query())
                            ->select('a.id as xid, fecha, b.name AS Programa, tipo_alerta, d.usua_nombre AS Tecnico')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            //->andWhere('valorador ="' . $responsable . '"')
                            ->andWhere($xfecha)
                            ->andWhere($xpcrc)
                            ->andWhere($xresponsable)
                ->orderBy(['fecha' => SORT_DESC])
                            ->all();



                    //$programa = Yii::$app->request->post();

                }
                //echo "<pre>";
                //print_r($dataProvider); die;
                $showGrid = true;
                return $this->render('alertasview', [
                            'model' => $model,
                            'showGrid' => $showGrid,
                            'dataProvider' => $dataProvider,
                            'resumenFeedback' => $resumenFeedback,
                            'detalleLiderFeedback' => $detalleLiderFeedback,
                        ]);
            }


            /**
             * Visualizacion de Alertas
             * * @author German Mejia Vieco
             */

            // public function actionVeralertas($id){

            //     //print_r($id); die;

            //     $model = Alertas::findOne($id);
                

            //     //$fecha, $pcrc, $valorador, $tipo_alerta, $archivo_adjunto, $remitentes, $asunto, $comentario

            //     //print_r($modelBase); die;
            //     // $destinatario = explode(",", $remitentes); 

            //     // $target_path = "alertas/" . $archivo_adjunto;
            //     // //print_r($target_path); die;

            //     // $html = $comentario;

            //     return $this->render('veralerta', [
            //                 'model' => $model,
            //     ]);

            //     //print_r($html); die;
                        
            // }

            /**
             * Displays a single BaseSatisfaccion model.
             * @param integer $id
             * @return mixed
             */
            public function actionVeralertas($id) {


                $model = (new \yii\db\Query())
                            ->select('a.fecha AS Fecha, b.name AS Programa, d.usua_nombre AS Tecnico, a.tipo_alerta AS Tipoalerta, a.archivo_adjunto AS Adjunto, a.remitentes AS Destinatarios, a.asunto AS Asunto, a.comentario AS Comentario')
                            ->from('tbl_alertascx a')
                            ->join('INNER JOIN', 'tbl_arbols b', 'b.id = a.pcrc')
                            ->join('INNER JOIN', 'tbl_usuarios d', 'a.valorador = d.usua_id')
                            ->andWhere('a.id ="' . $id . '"')
                            ->all();


                return $this->render('veralertas', [
                    'model' => $model['0'],
                ]);

            }


            /**
             * pruebas permite realizar el borrado o el delete de los datos en base de datos.
             * 
         * Andersson Moreno     
             * @return mixed
             */ 
            public function actionPruebas() {
        $varAlertas = Yii::$app->request->post("alertas_cx");   
        
        $model = Yii::$app->db->createCommand("delete from tbl_alertascx where id = $varAlertas")->execute();

        if($model){
            die("1");
        }else{
            die("0");
        }
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
              //Se comenta webservicesresponse para QA por caida de Amigo - 13-02-2019 -
                      //$webservicesresponse = Yii::$app->webservicesamigo->webServicesAmigo(Yii::$app->params['wsAmigo'], "setNotification", $params);
              $webservicesresponse = null;
                      $tmp_ejecucion = \app\models\Tmpejecucionformularios::findOne(['id' => $tmp_id]);
                      if (!$webservicesresponse && $tmp_ejecucion == '') {
                      Yii::$app->session->setFlash('danger', Yii::t('app', 'No se pudo realizar conexión con la plataforma Amigo'));
                      } */
                } catch (\Exception $exc) {
                    Yii::$app->session->setFlash('danger', Yii::t('app', 'error exception') . ": " . $exc->getMessage());
                }

                //REDIRECT CORRECTO
                return $this->redirect(['basesatisfaccion/showformulariogestion',
                            'basesatisfaccion_id' => $modelBase->id, 'preview' => 0, 'aleatorio' => 3, 'fill_values' => false, 'banderaescalado' => false]);
            }


           public function actionCorreogrupal(){
                $model = new Controlcorreogrupal();
                $model2 = new Correogrupal();

                $dataProvider = $model->obtenercorreogrupal(Yii::$app->request->post());


                $formData = Yii::$app->request->post();

                if ($model2->load($formData)) {
                    $txtNombre = $model2->nombre;
                    $txtNombre2 = $model2->nombre;
                    $txtUsuarios = explode(",",$model2->usua_id);
                    $txtFecha = $model2->fechacreacion;
                    $arrayUsu = array();

                    foreach ($txtUsuarios as $key => $value) {
                        array_push($arrayUsu, array("nombre"=>$txtNombre,"nombre2"=>$txtNombre2,"usua_id"=> $value,"fechacreacion"=>$txtFecha));
                    }

                    foreach ($arrayUsu as $key => $value) {
                        $varNom = $value["nombre"];
                        $varNom2 = $value["nombre2"];
                        $varUsu = $value["usua_id"];
                        $varfecha = $value["fechacreacion"];

                        Yii::$app->db->createCommand()->insert('tbl_correogrupal',[
                            'nombre' => $varNom,
                            'nombre2' => $varNom2,
                            'usua_id' => $varUsu,
                            'fechacreacion' => $varfecha,
                            ])->execute();
                    }

                    return $this->redirect(['alertas']);

                }

                return $this->renderAjax('correogrupal', [
                    'model'=>$model,
                    'model2'=>$model2,
                    'dataProvider'=>$dataProvider,
                    ]);
            }

            public function actionPrueba(){
                $varUsuarios = Yii::$app->request->post("varcorreos");
                //$varUsuarios = "Experiencia1";


                $varIdUsu = Yii::$app->db->createCommand("select usua_id from tbl_correogrupal where nombre like '$varUsuarios'")->queryAll();   

        $varRta1 = null;
        $varcorreos = null;
        $varEmail = null;
        $varRta = null;
                foreach ($varIdUsu as $key => $value) {
                    $varRta = $value['usua_id'];
                    $varEmail = Yii::$app->db->createCommand("select usua_email from tbl_usuarios where usua_id = $varRta")->queryAll(); 

                    foreach ($varEmail as $key => $value) {
                        $varRta1[] = $value['usua_email'];
            $varcorreos = implode(", ", $varRta1);
                    }
                }                

                die(json_encode($varcorreos));
            }

            public function actionActualizarcorreos(){
                $model = new UsuariosSearch();

                $dataProvider = $model->search(Yii::$app->request->post());  
                $varIdUsu = $model->usua_id;
                $varEmail = null;

                if ($varIdUsu != null) {
                     $varEmail = Yii::$app->db->createCommand("select usua_email from tbl_usuarios where usua_id = $varIdUsu")->queryScalar();   
                }                                          

                return $this->render('actualizarcorreos',[
                        'model' => $model,
                        'varEmail' => $varEmail,
                    ]);
            }

            public function actionComprobacion(){
                $varUsuarios = Yii::$app->request->post("varcorreos");
                $varIdUsu = explode(",",$varUsuarios);
                $varWord1 = 'allus';
                $varWord2 = 'multienlace';
                $varWord3 = null;
                $varRespuesta = 0;
                $txtRtaEmail = null;

                $arrayUsu = array();
                foreach ($varIdUsu as $key => $value) {
                    array_push($arrayUsu, array("usua_id"=>$value));
                }

                foreach ($arrayUsu as $key => $value) {
                    $txtIdUsu = $value["usua_id"];
                    (string)$txtRtaEmail = Yii::$app->db->createCommand("select usua_email from tbl_usuarios where usua_id = $txtIdUsu")->queryScalar(); 

                    if ($txtRtaEmail != null || $txtRtaEmail != "") {
                        $txtWord1 = strpos($txtRtaEmail, $varWord1);
                        $txtWord2 = strpos($txtRtaEmail, $varWord2);
                        $txtWord3 = strpos($txtRtaEmail, $varWord3);

                        if ($txtWord1 == true) {
                            $varRespuesta = 1;
                        }else{
                            if ($txtWord2 == true) {
                                $varRespuesta = 1;
                            }else{
                                if ($txtWord3 == true) {
                                    $varRespuesta = 1;
                                }
                            }
                        }
                    }
                    else
                    {
                        $varRespuesta = 1;
                    }

                }

                die(json_encode($varRespuesta));
            }


            public function actionPruebaactualizar(){
                $varUsuarios = Yii::$app->request->post("varusuarios");
                (string)$varCorreos = Yii::$app->request->post("varcorreos");

                $varResultados = Yii::$app->db->createCommand("update tbl_usuarios set usua_email = '$varCorreos' where usua_id = $varUsuarios")->execute(); 

                die(json_encode($varResultados));
            }

            public function actionComprobacionlista(){
                $varUsuarios = Yii::$app->request->post("varcorreos");
                $varIdUsu = explode(",",$varUsuarios);
                $txtRtaEmail = null;

                $arrayUsu = array();
                foreach ($varIdUsu as $key => $value) {
                    array_push($arrayUsu, array("usua_id"=>$value));
                }

                foreach ($arrayUsu as $key => $value) {
                    $txtIdUsu = $value["usua_id"];
                    (string)$txtRtaName = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $txtIdUsu")->queryScalar(); 
                    (string)$txtRtaEmail = Yii::$app->db->createCommand("select usua_email from tbl_usuarios where usua_id = $txtIdUsu")->queryScalar(); 

                    (string)$varResultados[] = $txtRtaName." - ".$txtRtaEmail;
                 }

                 

                die(json_encode($varResultados));
            }


            public function actionEnviarvalencias(){
                $varvalencia = Yii::$app->request->post("txtvaridselectvalencias");
                $varconnids = Yii::$app->request->post("txtvarconnid");

                $curl = curl_init();

                curl_setopt_array($curl, array(
                CURLOPT_URL => 'https://api-kaliope.analiticagrupokonectacloud.com/update/emotional-valence',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 0,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_SSL_VERIFYPEER=> false,
                CURLOPT_SSL_VERIFYHOST => false,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'POST',
                CURLOPT_POSTFIELDS =>'{"connid":"'.$varconnids.'", "valencia": "'.$varvalencia.'"}',
                CURLOPT_HTTPHEADER => array(
                    'Content-Type: application/json'
                  ),
                ));

                $response = curl_exec($curl);

                curl_close($curl);

                $response = json_decode(iconv( "Windows-1252", "UTF-8", $response ),true);

                Yii::$app->db->createCommand()->update('tbl_kaliope_transcipcion',[
                                          'valencia' => $varvalencia,
                                      ],'connid = "'.$varconnids.'"')->execute(); 
                
                die(json_encode($response));
            }


        }
        