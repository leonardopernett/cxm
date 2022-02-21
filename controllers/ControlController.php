<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use DateTime;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\helpers\Html;
use yii\base\Exception;

class ControlController extends \yii\web\Controller
{

    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [],
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
                        'actions' => [
                            'index', 'dimensionlistmultiple', 'metricalistmultiple', 'metricalistmultipledetallada',
                            'vistacorte', 'indexcorte', 'update', 'volver', 'delete',
                            'validarcortes', 'indexpersona', 'equiposlistvaloradores'
                        ],
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function () {
                            return Yii::$app->user->identity->isControlProcesoCX();
                        },
                    ],
                    [
                        'actions' => ['enviarcorreo', 'delete'],
                        'allow' => true,
                        'roles' => ['@']
                    ],
                ],
            ],
        ];
    }

    /**
     * Funcion que permite visualizar el index de control proceso
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @return view 
     */
    public function actionIndex()
    {
        $modelBusqueda = \app\models\FiltrosControl::find()->where([
            'usua_id' => Yii::$app->user->identity->id,
            'guardar_filtro' => 1
        ])->one();
        $arrArboles = $arrArbolesUnica = [];
        $tipo_grafica = '';
        if (isset($modelBusqueda)) {
            $model = $modelBusqueda;
            $model->arbolDetallada = $model->ids_arboles;
            $model->fechaDetallada = $model->rango_fecha;
            $model->metricaDetallada = $model->ids_metricas;
            $model->dimensionDetallada = $model->ids_dimensiones;
            $model->corteDetallada = $model->corte_id;
            $arrArboles = explode(',', $model->arbolDetallada);
        } else {
            $model = new \app\models\FiltrosControl();
        }
        $data = new \stdClass();
        $data->showGraf = false;

        //VISTA Y CONTROLADOR PARA GUARDAR FILTROS
        $controlador = Yii::$app->controller->id;
        $vista = Yii::$app->controller->action->id;
        $filtros = new \stdClass();

        if (Yii::$app->request->post()) {
            //VARIABLA PARA DETERMINAR SI ES VISTA UNICA O DETALLADA
            $form = Yii::$app->request->post('form');
            $model->scenario = ($form == "0") ? 'filtroProceso' : 'filtroProcesoDetallado';
            $idsArboles = ($form == "0") ? ((Yii::$app->request->post('arbol_ids') !=
                null) ? Yii::$app->request->post('arbol_ids') : null) : ((Yii::$app->request->post('arbol_idsDetallada') !=
                null) ? Yii::$app->request->post('arbol_idsDetallada') : null);
            $model->arbol = ($idsArboles != null) ? implode(",", $idsArboles) : null;
            if ($model->arbol == null) {
                $msg = \Yii::t('app', 'Seleccione un arbol');
                Yii::$app->session->setFlash('danger', $msg);
            } else {
                $arrArboles = explode(',', $model->arbol);
                $arrArbolesUnica = explode(',', $model->arbol);
            }
            $data->banderaError = ($form == "0") ? 'vistaunica' : 'vistadetallada';
        } else {
            //PREGUNTO SI EXISTE UN FILTRO
            $filtrosForm = \app\models\FiltrosFormularios::findOne(['vista' => $controlador . '/' . $vista, 'usua_id' => Yii::$app->user->identity->id]);
            $modelBusqueda = \app\models\FiltrosControl::find()->where([
                'usua_id' => Yii::$app->user->identity->id,
                'guardar_filtro' => 1
            ])->one();
            $model = new \app\models\FiltrosControl();
            if (!empty($filtrosForm)) {
                $dataFiltos = json_decode($filtrosForm->parametros);
                $model->fecha = $dataFiltos->fecha;
                $model->dimension = $dataFiltos->dimension;
                $model->metrica = $dataFiltos->metrica;
                $model->corte = $dataFiltos->corte;
                $model->tipo_grafica = $dataFiltos->tipo_grafica;
                $arrArbolesUnica = explode(',', $dataFiltos->arbol_ids);
            } else {
                $model->tipo_grafica = '';
                $model->dimension = '';
                $model->fecha = '';
                $model->corte = '';
                $model->metrica = '';
            }
            if (isset($modelBusqueda)) {
                $model->arbolDetallada = $modelBusqueda->ids_arboles;
                $model->fechaDetallada = $modelBusqueda->rango_fecha;
                $model->metricaDetallada = $modelBusqueda->ids_metricas;
                $model->dimensionDetallada = $modelBusqueda->ids_dimensiones;
                $model->corteDetallada = $modelBusqueda->corte_id;
                $model->guardar_filtro = $modelBusqueda->guardar_filtro;
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {

            /* Inicio construccion de graficas */
            if ($form == "0") {
                /**
                 * TIPO DE GRAFICA
                 * agru_dimen: AGRUPANDO DIMENSIÓN
                 * sepa_dimen: SEPARANDO DIMENSIÓN
                 * tendencia: TENDENCIA EN PERIODOS DE CORTE
                 */
                $tipo_grafica = $model->tipo_grafica;
                /**
                 * FUNCION ENCARGADA DE GENERAR LOS DATOS PARA GRAFICAR
                 * DEPENDIENTE DEL TIPO DE GRAFICA SELECCIONADA
                 */
                $data = $this->datosGrafica($model, $tipo_grafica, Yii::$app->request->post('agrupar'), $idsArboles);

                if (!$data->showGraf) {
                    $msg = \Yii::t('app', 'No se encontraron datos para los filtros ingresados');
                    Yii::$app->session->setFlash('danger', $msg);
                }

                /* GUARDO FILTROS DE BÚSQUEDA */
                if (isset($model->arbol) && $model->arbol != "") {
                    $filtros->fecha = $fecha = $model->fecha;
                    $filtros->dimension = $model->dimension;
                    $filtros->metrica = $model->metrica;
                    $filtros->corte = $model->corte;
                    $fecha = explode(' - ', $fecha);

                    //Guardar filtros --------------------------------------------------                    
                    $filtrosDatos = new \stdClass();
                    $filtrosDatos->fecha = $model->fecha;
                    $filtrosDatos->dimension = $model->dimension;
                    $filtrosDatos->metrica = $model->metrica;
                    $filtrosDatos->corte = $model->corte;
                    $filtrosDatos->arbol_ids = $model->arbol;
                    $filtrosDatos->tipo_grafica = $model->tipo_grafica;
                    $arbIds = $filtrosDatos->arbol_ids;

                    $filtrosForm = \app\models\FiltrosFormularios::findOne(['vista' => $controlador . '/' . $vista, 'usua_id' => Yii::$app->user->identity->id]);

                    if (empty($filtrosForm)) {
                        $filtrosForm = new \app\models\FiltrosFormularios;
                    }
                    $filtrosForm->usua_id = Yii::$app->user->identity->id;
                    $filtrosForm->vista = $controlador . '/' . $vista;
                    $filtrosForm->parametros = json_encode($filtrosDatos);
                    $filtrosForm->save();
                }
            } else {
                $ejecucionformulario = new \app\models\Ejecucionformularios();
                $model->ids_arboles = $model->arbol;
                $model->rango_fecha = $model->fechaDetallada;
                $model->ids_metricas = $model->metricaDetallada;
                $model->ids_dimensiones = $model->dimensionDetallada;
                $model->corte_id = $model->corteDetallada;
                $model->usua_id = Yii::$app->user->identity->id;

                //DEJO SOLO UNA MÉTRICA DE VOLUMEN
                //BOLEANO PARA SABER SI YA ENCONTRÉ UNA MET DE VOLUMEN
                $boolMetVol = false;
                //ARRAY CON LAS NUEVAS METRICAS SOLO TENIENDO UNA DE VOLUMEN
                $newMet = [];
                //ARRAY CON MÉTRICAS SELECCIONADAS
                $arrMet = explode(",", $model->ids_metricas);
                //HAY MÉTRICAS DE VOLUMEN?
                $volumenes = false;
                foreach ($arrMet as $Met) {
                    switch ($Met) {
                        case '12':
                        case '13':
                        case '14':
                        case '15':
                        case '16':
                        case '17':
                        case '18':
                        case '19':
                        case '20':
                        case '21':
                        case '22':
                            $volumenes = true;
                            if (!$boolMetVol) {
                                $boolMetVol = true;
                                $newMet[] = $Met;
                            }
                            break;
                        default:
                            $newMet[] = $Met;
                            break;
                    }
                }
                $model->ids_metricas = implode(",", $newMet);


                if ($model->guardar_filtro == 1) {
                    $model->save();
                } else {
                    if (isset($modelBusqueda)) {
                        $modelBusqueda->delete();
                    }
                }
                $metricas = \app\models\Metrica::find()->where("id IN(" . $model->ids_metricas . ")")->asArray()->all();
                $metrica = $this->validarMetrica($metricas[0]['id']);
                $consulta = $ejecucionformulario->getDatabygraf($model->ids_arboles, $model->ids_dimensiones, $model->rango_fecha, $metrica, false, $model, "proceso");
                if (count($consulta) > 0) {
                    $data->datosTabla = $this->construirTabla($model->rango_fecha, Yii::$app->user->identity->id, $model->corte_id, $model->ids_metricas, $model->ids_dimensiones, $model->ids_arboles, $model, $form, "proceso", "reporte", $volumenes);
                    $tablaExcel = $this->actionGenerartablaexcel($data->datosTabla['datos'], $model->ids_dimensiones, $data->datosTabla['total'], "proceso");
                    $this->generarExcel($tablaExcel, $data->datosTabla['cortes'], $model->ids_dimensiones, $model->ids_metricas, false, Yii::$app->user->identity->id, $model->corte_id, "proceso", $model->ids_arboles);
                } else {
                    $data->showGraf = false;
                    $msg = \Yii::t('app', 'No se encontraron datos para los filtros ingresados');
                    Yii::$app->session->setFlash('danger', $msg);
                }
            }
        }

        $data->arboles[] = $this->getRecursivearbolscopia('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', 'arbol_ids', $arrArbolesUnica);
        $data->arboles[] = $this->getRecursivearbolscopia('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', 'arbol_idsDetallada', $arrArboles);
        $data->metrica = ArrayHelper::map(
            \app\models\Metrica::find()->limit(25)->asArray()->all(),
            'id',
            'detexto'
        ); //limite metrica vista unica German Index
        return $this->render('index', ['data' => $data, 'model' => $model, 'tipo_grafica' => $tipo_grafica]);
    }

    /**
     * Funcion que construye la lista de arboles para la visualizacion en la vista index de control
     * proceso y control persona
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $tabla
     * @param type $id_field
     * @param type $show_data
     * @param type $link_field
     * @param type $parent
     * @param type $prefix
     * @return string $out
     */
    public function getRecursivearbolscopia($tabla, $id_field, $show_data, $link_field, $parent, $prefix, $idetiqueta, $arraArboles)
    {
     
        /* Armar query */
        
        if ($parent == 0) {
        
        
           $sql = "select * from  tbl_arbols  where arbol_id is null ";

        } else {


            

            $sql = "select * from tbl_arbols  where arbol_id =  :parent ";


           
        }
        $rs = Yii::$app->db->createCommand($sql)
        ->bindValue(':parent',$parent)
        ->queryAll();
        $out = '<ol id="' . $idetiqueta . '" name="' . $idetiqueta . '[]">';
        if ($rs) {
            foreach ($rs as $arr) {
                if (in_array($arr['id'], $arraArboles)) {
                    $out .= '<li data-value = "' . $arr['id'] . '" data-name = "' . $idetiqueta . '[]" data-checked="checked">';
                } else {
                    $out .= '<li data-value = "' . $arr['id'] . '" data-name ="' . $idetiqueta . '[]">';
                }
                $out .= $arr['name'];
                $out .= $this->getRecursivearbolscopia($tabla, $id_field, $show_data, $link_field, $arr[$id_field], $prefix . $prefix, $idetiqueta, $arraArboles);
            }
        }
        $out .= '</li></ol>';
        return $out;
    }

    /**
     * Obtiene el listado de roles
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $search
     * @param type $id
     */
    public function actionDimensionlistmultiple($search = null, $id = null)
    {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->goHome();
        }

        $out = ['more' => false];
        if (!is_null($search)) {
            $data = \app\models\Dimensiones::find()
                ->select(['id' => 'id', 'text' => 'UPPER(name)'])
                ->where('name LIKE "%":search"%"')
                ->addParams([':search'=>$search])
                ->orderBy('name')
                ->asArray()
                ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $data = \app\models\Dimensiones::find()
                ->select(['id' => 'id', 'text' => 'UPPER(name)'])
                ->where('id IN (:id)')
                ->addParams([':id'=>$id])
                ->asArray()
                ->all();
            $out['results'] = array_values($data);
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }

    /**
     * Obtiene el listado de Metricas
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $search
     * @param type $id
     */
    public function actionMetricalistmultiple($search = null, $id = null)
    {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->goHome();
        }
        $out = ['more' => false];
        if (!is_null($search)) {
            //Metricas German Index
            $data = \app\models\Metrica::find()
                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                ->where('detexto LIKE "%":search"%"')->limit(25)
                ->addParams([':search'=>$search])
                ->orderBy('id ASC')
                ->asArray()
                ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $data = \app\models\Metrica::find()
                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                ->where('id IN (:id)')->limit(25)
                ->addParams([':id'=>$id])
                ->orderBy('id ASC')
                ->asArray()
                ->all();
            $out['results'] = array_values($data);
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }

    /**
     * Obtiene el listado de Metricas
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $search
     * @param type $id
     */
    public function actionMetricalistmultipledetallada($search = null, $id = null)
    {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->goHome();
        }
        $out = ['more' => false];
        if (!is_null($search)) {
            //Metricas German IndexPersonas
            $data = \app\models\Metrica::find()
                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                ->where('detexto LIKE "%":search"%"')
                ->addParams([':search'=>$search])
                ->andWhere('id IN (1,2,3,4,5,6,7,8,9,10,11,30)')
                ->orderBy('detexto')
                ->asArray()
                ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $data = \app\models\Metrica::find()
                ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                ->where('id IN (:id)')
                ->addParams([':id'=>$id])
                ->asArray()
                ->all();
            $out['results'] = array_values($data);
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }

    /**
     * Funcion que permite renderizar la vista de cortes, la cual mostrara uno o 
     * varios campos para ingreso de fecha
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @return type
     */
    public function actionVistacorte()
    {


        $modelCorte = new \app\models\CorteFecha();
        $modelSegmento = new \app\models\SegmentoCorte();
        $fechas = [];

        if (Yii::$app->getRequest()->isAjax) {
            $modelSegmento->scenario = (Yii::$app->request->post('Tipo_corte') ==
                1) ? 'corteSemana' : 'corteMes';
            $rangofecha = Yii::$app->request->get('rangofecha');
            if (Yii::$app->request->post()) {
                $rangofecha = Yii::$app->request->post('rangofecha');
                if (($modelSegmento->load(Yii::$app->request->post())) &&
                    ($modelSegmento->validate())
                ) {
                    $modelCorte->load(Yii::$app->request->post());
                    if ($modelCorte->band_repetir == 1) {
                        $datos = Yii::$app->request->post('SegmentoCorte');
                        $modelCorte->tipo_corte = Yii::$app->request->post('Tipo_corte');
                        $modelCorte->usua_id = Yii::$app->user->identity->id;
                        $rangofecha = Yii::$app->request->post('rangofecha');
                        $this->repetirCorte($datos, $modelCorte, $rangofecha);
                    } else {
                        $datos = Yii::$app->request->post('SegmentoCorte');
                        $modelCorte->tipo_corte = Yii::$app->request->post('Tipo_corte');
                        $modelCorte->usua_id = Yii::$app->user->identity->id;
                        $rangofecha = Yii::$app->request->post('rangofecha');
                        //CAMBIAR PARA RECIBIR DE VISTA
                        $modelCorte->save();
                        $timeMes = 0;
                        if ($modelCorte->tipo_corte == 1) {
                            for ($i = 1; $i <= count($datos); $i++) {
                                if ($datos['semana' . $i] != '') {
                                    $fechas = explode(' - ', $datos['semana' . $i]);
                                    $modelseg = new \app\models\SegmentoCorte();
                                    $modelseg->segmento_fecha_inicio = $fechas[0] . ' 00:00:00';
                                    $modelseg->segmento_fecha_fin = $fechas[1] . ' 23:59:59';
                                    $modelseg->segmento_nombre = 'semana' . $i;
                                    $modelseg->corte_id = $modelCorte->corte_id;
                                    $timeMes = strtotime($fechas[1]);
                                    $timeMes = getdate($timeMes);
                                    $modelseg->save();
                                }
                            }
                        } else {
                            $fechas = explode(' - ', $datos['mes']);
                            $modelseg = new \app\models\SegmentoCorte();
                            $modelseg->segmento_fecha_inicio = $fechas[0] . ' 00:00:00';
                            $modelseg->segmento_fecha_fin = $fechas[1] . ' 23:59:59';
                            $modelseg->segmento_nombre = 'mes';
                            $modelseg->corte_id = $modelCorte->corte_id;
                            $timeMes = strtotime($fechas[1]);
                            $timeMes = getdate($timeMes);
                            $modelseg->save();
                        }
                        $modelCorte->corte_descripcion = $timeMes['month'];
                        $modelCorte->save();
                    }
                    $modelSearch = new \app\models\CorteFecha();
                    //semana dataprovider
                    $dataProviderSemana = $modelSearch->search(Yii::$app->request->queryParams, 1);
                    //mes dataprovider
                    $dataproviderMes = $modelSearch->search(Yii::$app->request->queryParams, 2);
                    return $this->renderAjax('indexCortes', [
                        'modelCorte' => $modelCorte,
                        'searchModel' => $modelSearch,
                        'dataProviderSemana' => $dataProviderSemana,
                        'dataProviderMes' => $dataproviderMes,
                        'tipo_corte' => $modelCorte->tipo_corte,
                        'rangofecha' => $rangofecha,
                    ]);
                } else {
                    return $this->renderAjax('viewCortes', [
                        'modelCorte' => $modelCorte,
                        'modelSegmento' => $modelSegmento,
                        'tipo_corte' => Yii::$app->request->post('Tipo_corte'),
                        'rangofecha' => $rangofecha,
                    ]);
                }
            } else {
                return $this->renderAjax('viewCortes', [
                    'modelCorte' => $modelCorte,
                    'modelSegmento' => $modelSegmento,
                    'tipo_corte' => Yii::$app->request->get('Tipo_corte'),
                    'rangofecha' => $rangofecha,
                ]);
            }
        }
    }

    /**
     * Funcion que permite mostrar el index para configuracion de cortes
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @return type
     */
    public function actionIndexcorte()
    {

        if (Yii::$app->getRequest()->isAjax) {
            $tipo = Yii::$app->request->get('tipo');
            $rangofecha = Yii::$app->request->get('fecha');

            $modelCorte = new \app\models\CorteFecha();
            $modelSearch = new \app\models\CorteFecha();
            //semana dataprovider
            $dataProviderSemana = $modelSearch->search(Yii::$app->request->queryParams, 1);
            //mes dataprovider
            $dataproviderMes = $modelSearch->search(Yii::$app->request->queryParams, 2);
            return $this->renderAjax('indexCortes', [
                'modelCorte' => $modelCorte,
                'searchModel' => $modelSearch,
                'dataProviderSemana' => $dataProviderSemana,
                'dataProviderMes' => $dataproviderMes,
                'tipo_corte' => $tipo,
                'rangofecha' => $rangofecha,
            ]);
        }
    }

    /**
     * Funcion que permite visualizar el modal para atualizar y guardar la informacion
     * al actualizar un corte
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @return type
     */
    public function actionUpdate()
    {
        $modelCorte = \app\models\CorteFecha::find()->where(['corte_id' => Yii::$app->request->get('corte_id')])->one();
        $arraySegmento = \app\models\SegmentoCorte::find()->where(['corte_id' => $modelCorte->corte_id])->all();
        $modelSegmento = new \app\models\SegmentoCorte();
        $rangofecha = Yii::$app->request->get('rangofecha');
        if (Yii::$app->getRequest()->isAjax) {
            $modelSegmento->scenario = (Yii::$app->request->post('Tipo_corte') ==
                1) ? 'corteSemana' : 'corteMes';
            if (Yii::$app->request->post()) {
                $rangofecha = Yii::$app->request->post('rangofecha');
                if (($modelSegmento->load(Yii::$app->request->post())) &&
                    ($modelSegmento->validate() && $modelCorte->validate())
                ) {
                    $modelCorte = \app\models\CorteFecha::find()->where(['corte_id' => Yii::$app->request->post('corte_id')])->one();
                    $datos = Yii::$app->request->post('SegmentoCorte');
                    $modelCorte->tipo_corte = Yii::$app->request->post('Tipo_corte');
                    $modelCorte->usua_id = Yii::$app->user->identity->id;
                    //CAMBIAR PARA RECIBIR DE VISTA
                    $modelCorte->band_repetir = 1;
                    $modelCorte->save();
                    $timeMes = 0;
                    if ($modelCorte->tipo_corte == 1) {
                        for ($i = 1; $i <= count($datos); $i++) {
                            $idSemana = Yii::$app->request->post('idsemana' . $i);
                            if (isset($idSemana)) {
                                $modelseg = \app\models\SegmentoCorte::find()->where(['segmento_corte_id' => $idSemana])->one();
                            } else {
                                if (isset($datos['semana' . $i])) {
                                    $modelseg = new \app\models\SegmentoCorte();
                                }
                            }
                            if ($datos['semana' . $i] != '') {
                                $fechas = explode(' - ', $datos['semana' . $i]);
                                $modelseg->segmento_fecha_inicio = $fechas[0] . ' 00:00:00';
                                $modelseg->segmento_fecha_fin = $fechas[1] . ' 23:59:59';
                                $modelseg->segmento_nombre = 'semana' . $i;
                                $modelseg->corte_id = $modelCorte->corte_id;
                                $timeMes = strtotime($fechas[1]);
                                $timeMes = getdate($timeMes);
                                $modelseg->save();
                            }
                        }
                    } else {
                        $idmes = Yii::$app->request->post('idmes');
                        if (isset($idmes)) {
                            $modelseg = \app\models\SegmentoCorte::find()->where(['segmento_corte_id' => $idmes])->one();
                        } else {
                            $modelseg = new \app\models\SegmentoCorte();
                        }
                        $fechas = explode(' - ', $datos['mes']);
                        $modelseg->segmento_fecha_inicio = $fechas[0] . ' 00:00:00';
                        $modelseg->segmento_fecha_fin = $fechas[1] . ' 23:59:59';
                        $modelseg->segmento_nombre = 'mes';
                        $modelseg->corte_id = $modelCorte->corte_id;
                        $timeMes = strtotime($fechas[1]);
                        $timeMes = getdate($timeMes);
                        $modelseg->save();
                    }
                    $modelCorte->corte_descripcion = $timeMes['month'];
                    $modelCorte->save();
                    $modelSearch = new \app\models\CorteFecha();
                    //semana dataprovider
                    $dataProviderSemana = $modelSearch->search(Yii::$app->request->queryParams, 1);
                    //mes dataprovider
                    $dataproviderMes = $modelSearch->search(Yii::$app->request->queryParams, 2);
                    return $this->renderAjax('indexCortes', [
                        'modelCorte' => $modelCorte,
                        'searchModel' => $modelSearch,
                        'dataProviderSemana' => $dataProviderSemana,
                        'dataProviderMes' => $dataproviderMes,
                        'tipo_corte' => $modelCorte->tipo_corte,
                        'rangofecha' => $rangofecha,
                    ]);
                } else {

                    return $this->renderAjax('updateCortes', [
                        'modelCorte' => $modelCorte,
                        'modelSegmento' => $modelSegmento,
                        'arraySegmentos' => $arraySegmento,
                        'tipo_corte' => $modelCorte->tipo_corte,
                        'corte_id' => $modelCorte->corte_id,
                        'rangofecha' => $rangofecha,
                    ]);
                }
            } else {
                return $this->renderAjax('updateCortes', [
                    'modelCorte' => $modelCorte,
                    'modelSegmento' => $modelSegmento,
                    'arraySegmentos' => $arraySegmento,
                    'tipo_corte' => $modelCorte->tipo_corte,
                    'corte_id' => $modelCorte->corte_id,
                    'rangofecha' => $rangofecha,
                ]);
            }
        }
    }

    /**
     * Funcion que permite eliminar un corte con sus segmentos relacionados
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @return view
     */
    public function actionDelete()
    {
        $id = Yii::$app->request->get('id');
        \app\models\SegmentoCorte::deleteAll(['corte_id' => $id]);
        \app\models\CorteFecha::deleteAll(['corte_id' => $id]);
        $rangofecha = Yii::$app->request->get('rangofecha');
        $modelCorte = new \app\models\CorteFecha();
        $modelSearch = new \app\models\CorteFecha();
        //semana dataprovider
        $dataProviderSemana = $modelSearch->search(Yii::$app->request->queryParams, 1);
        //mes dataprovider
        $dataproviderMes = $modelSearch->search(Yii::$app->request->queryParams, 2);
        return $this->renderAjax('indexCortes', [
            'modelCorte' => $modelCorte,
            'searchModel' => $modelSearch,
            'dataProviderSemana' => $dataProviderSemana,
            'dataProviderMes' => $dataproviderMes,
            'tipo_corte' => 1,
            'rangofecha' => $rangofecha,
        ]);
    }

    /**
     * Funcion que permite devolverse en los modales 
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @return view
     */
    public function actionVolver()
    {
        $modelCorte = new \app\models\CorteFecha();
        $modelSearch = new \app\models\CorteFecha();
        //semana dataprovider
        $dataProviderSemana = $modelSearch->search(Yii::$app->request->queryParams, 1);
        //mes dataprovider
        $dataproviderMes = $modelSearch->search(Yii::$app->request->queryParams, 2);
        $tipo = Yii::$app->request->get('Tipo_corte');
        $rangofecha = Yii::$app->request->post('rangofecha');
        return $this->renderAjax('indexCortes', [
            'modelCorte' => $modelCorte,
            'searchModel' => $modelSearch,
            'dataProviderSemana' => $dataProviderSemana,
            'dataProviderMes' => $dataproviderMes,
            'tipo_corte' => $tipo,
            'rangofecha' => $rangofecha,
        ]);
    }

    /**
     * Funcion que permite construir la tabla de control proceso con datos y cortes
     * respectivos
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $rangofecha
     * @param type $id_usuario
     */
    public function construirTabla($rangofecha = null, $id_usuario = null, $tipoCorte = null, $metrica = null, $dimension = null, $arbol = null, $model = null, $form = null, $control = null, $viewdetalladaproceso = null, $volumenes = false)
    {
        $cortes = [];
        switch ($tipoCorte) {
            case 1:
                $cortes = $this->calcularCortesSemana($rangofecha, $id_usuario, $tipoCorte);
                break;
            case 2:
                $cortes = $this->calcularCortesMes($rangofecha, $id_usuario, $tipoCorte);
                break;
            case 3:
                $fechas = explode(' - ', $rangofecha);
                $fechas[0] = $fechas[0] . ' 00:00:00';
                $fechas[1] = $fechas[1] . ' 23:59:59';
                $fechaInicio = strtotime($fechas[0]);
                $fechaFin = strtotime($fechas[1]);
                for ($i = $fechaInicio; $i <= $fechaFin; $i += 86400) {
                    $cortes[] = [
                        'fechaI' => date('Y-m-d', $i) . ' 00:00:00',
                        'fechaF' => date('Y-m-d', $i) . ' 23:59:59'
                    ];
                }
                break;
            default:
                break;
        }
        $dataTable = [];
        $ejecucionformulario = new \app\models\Ejecucionformularios();

        if ($form == "0") {

            foreach ($cortes as $corte) {
                $dataTable['datos'][] = $ejecucionformulario->getDatabytable($dimension, $corte, $metrica, $arbol, $model);
            }
            $dataTable['total'][] = $ejecucionformulario->getDatabytabletotal($dimension, $rangofecha, $metrica, $arbol, $model);
        } else {
            $metrica = explode(',', $metrica);
            foreach ($cortes as $corte) {
                for ($i = 0; $i < count($metrica); $i++) {
                    $validarMetrica = $this->validarMetrica($metrica[$i]);

                    //id MÉTRICA VOLUMEN
                    $idMetrica = NULL;
                    if ($volumenes) {
                        $idMetrica = $metrica[$i];
                    } else {
                        #code
                    }

                    if ($control == "proceso") {
                        if (empty($viewdetalladaproceso)) {
                            $dataTable['datos'][$corte['fechaI'] . " - " . $corte['fechaF']][$validarMetrica][] = $ejecucionformulario->getDatabytable($dimension, $corte, $validarMetrica, $arbol, $model);
                        } else {
                            $dataTable['datos'][$corte['fechaI'] . " - " . $corte['fechaF']][$validarMetrica][] = $ejecucionformulario->getDatabytableexcel($dimension, $corte, $validarMetrica, $arbol, $model, $volumenes, $idMetrica);
                        }
                    } else {
                        $dataTable['datos'][$corte['fechaI'] . " - " . $corte['fechaF']][$validarMetrica][] = $ejecucionformulario->getDatabygrafexcelpersona($arbol, $dimension, $corte['fechaI'] . " - " . $corte['fechaF'], $validarMetrica, $model);
                    }
                }
            }
            for ($i = 0; $i < count($metrica); $i++) {
                $validarMetrica = $this->validarMetrica($metrica[$i]);

                if ($control == "proceso") {
                    if (empty($viewdetalladaproceso)) {
                        $dataTable['total'][] = $ejecucionformulario->getDatabytabletotal($dimension, $rangofecha, $validarMetrica, $arbol, $model);
                    } else {
                        $dataTable['total'][] = $ejecucionformulario->getDatabytabletotalexcel($dimension, $rangofecha, $validarMetrica, $arbol, $model, $volumenes, $idMetrica);
                    }
                } else {
                    $dataTable['total'][] = $ejecucionformulario->getDatabytabletotalpersona($dimension, $rangofecha, $validarMetrica, $arbol, $model);
                }
            }
        }
        $dataTable['cortes'] = $cortes;
        return $dataTable;
    }

    /**
     * Funcion que calcula las semanas automaticamnete
     * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $rangofecha
     * @param type $banderaAuto
     * @param type $id_usuario
     * @param type $tipo_corte
     * @return array
     */
    public function calcularCortessemanaauto($rangofecha = null, $banderaAuto = null, $id_usuario = null, $tipo_corte = null)
    {
        
        $fechas = explode(' - ', $rangofecha);
        $inicioMes = explode('-', $fechas[0]);
        $inicioMes[2] = '01';
        $inicioMes = implode('-', $inicioMes);
        $finMes = explode('-', $fechas[1]);
        $finMes[2] = date("d", (mktime(0, 0, 0, $finMes[1] + 1, 1, $finMes[0]) - 1));
        $finMes = implode('-', $finMes);
        $fechainicial = new DateTime($inicioMes);
        $fechafinal = new DateTime($finMes);
        $diferencia = $fechainicial->diff($fechafinal);
        $meses = ($diferencia->y * 12) + $diferencia->m;
        $meses = ($meses == 0) ? 1 : $meses;
        for ($index = 1; $index <= $meses; $index++) {
            $inicioMes = $inicioMes;
            $mesfinal = explode('-', $inicioMes);
            $mesfinal[2] = date("d", (mktime(0, 0, 0, $mesfinal[1] + 1, 1, $mesfinal[0]) -
                1));
            $mesfinal = implode('-', $mesfinal);
            $mesfinal = $mesfinal . ' 23:59:59';
            $cortesFecha = [];
            if ($banderaAuto) {
                $cortesFecha = \app\models\SegmentoCorte::find()->from('tbl_segmento_corte sc')
                    ->join('INNER JOIN', 'tbl_corte_fecha cf', 'sc.corte_id = cf.corte_id')
                    ->where(('segmento_fecha_inicio BETWEEN :inicioMes ')  AND (' :mesfinal ') 
                         OR ('segmento_fecha_fin BETWEEN :inicioMes')   AND (' :mesfinal ') AND ('cf.usua_id = :id_usuario'))
                    ->andWhere('cf.tipo_corte = :tipo_corte')
                    ->addParams([':inicioMes' => $inicioMes,':mesfinal' => $mesfinal,':id_usuario' => $id_usuario,':tipo_corte' => $tipo_corte])
                    ->orderBy('segmento_fecha_inicio,segmento_fecha_fin ASC')
                    ->all();
            }
            if (count($cortesFecha) == 0) {
                for ($i = 1; $i <= 5; $i++) {
                    $inicioMes = (isset($iniciosegmento)) ? $iniciosegmento : $inicioMes;
                    $mesfinalsegmento = explode('-', $inicioMes);
                    $mesfinalsegmento[2] = date("d", (mktime(0, 0, 0, $mesfinalsegmento[1] + 1, 1, $mesfinalsegmento[0]) - 1));
                    $mesfinalsegmento = implode('-', $mesfinalsegmento);
                    $cortesSemanaCalc[] = [
                        'fechaI' => $inicioMes . ' 00:00:00',
                        'fechaF' => (($i == 5) ? $mesfinalsegmento . ' 23:59:59' : date('Y-m-d', (strtotime($inicioMes) + (86400 *
                            6))) . ' 23:59:59')
                    ];
                    $iniciosegmento = ($i == 5) ? date('Y-m-d', (strtotime('+1 day', strtotime($mesfinalsegmento)))) : date('Y-m-d', (strtotime('+1 day', strtotime($inicioMes) + (86400 *
                        6))));
                }
            } else {
                foreach ($cortesFecha as $corte) {
                    $cortesSemanaCalc[] = [
                        'fechaI' => $corte->segmento_fecha_inicio,
                        'fechaF' => $corte->segmento_fecha_fin
                    ];
                }
                $iniciosegmento = $inicioMes;
                $mesfinalsegmento = explode('-', $iniciosegmento);
                $mesfinalsegmento[2] = date("d", (mktime(0, 0, 0, $mesfinalsegmento[1] + 1, 1, $mesfinalsegmento[0]) - 1));
                $mesfinalsegmento = implode('-', $mesfinalsegmento);
            }

            $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($mesfinalsegmento))));
        }
        return $cortesSemanaCalc;
    }

    /**
     * Funcion que toma las semanas calculadas automaticamente y anexa
     * las semanas parametrizadas
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $rangofecha
     * @param type $id_usuario
     * @param type $tipo_corte
     * @return array
     */
    public function calcularCortesSemana($rangofecha = null, $id_usuario = null, $tipo_corte = null)
    {
        //busco los cortes que esten dentro del rango fecha
        $fechas = explode(' - ', $rangofecha);
        $fechas[0] = $fechas[0] . ' 00:00:00';
        $fechas[1] = $fechas[1] . ' 23:59:59';
        $cortesSemana = [];
        $corteAnterior = null;
       $cortesFecha = \app\models\SegmentoCorte::find()->from('tbl_segmento_corte sc')
                    ->join('INNER JOIN', 'tbl_corte_fecha cf', 'sc.corte_id = cf.corte_id')
                    ->where(('segmento_fecha_inicio BETWEEN :fechas0 ')  AND (' :fechas1 ') 
                         OR ('segmento_fecha_fin BETWEEN :fechas0')   AND (' :fechas1 ') AND ('cf.usua_id = id_usuario'))
                    ->andWhere('cf.tipo_corte = :tipo_corte')
                    ->addParams([':fechas0' => $fechas[0],':fechas1' => $fechas[1],':id_usuario' => $id_usuario,':tipo_corte' => $tipo_corte])
                    ->orderBy('segmento_fecha_inicio,segmento_fecha_fin ASC')
                    ->all();
            

        $cortesSemanaCalc = $this->calcularCortessemanaauto($rangofecha, (count($cortesFecha) > 0) ? true : false, $id_usuario, $tipo_corte);
        for ($i = 0; $i < count($cortesSemanaCalc); $i++) {
            if (!is_null($corteAnterior)) {
                $fechafincomparar = strtotime('-1 day', strtotime($cortesSemanaCalc[$i]['fechaI']));
                $fechafincomparar = date('Y-m-d', $fechafincomparar);
                $fechafincomparar = $fechafincomparar . ' 23:59:59';
                if (($cortesSemanaCalc[$i]['fechaI'] >= $fechas[0] || $cortesSemanaCalc[$i]['fechaF'] >=
                    $fechas[0]) && ($cortesSemanaCalc[$i]['fechaI'] <=
                    $fechas[1] || $cortesSemanaCalc[$i]['fechaF'] <=
                    $fechas[1])) {
                    if (!isset($cortesSemanaCalc[$i + 1])) {
                        if ($fechas[1] <= $cortesSemanaCalc[$i]['fechaF']) {
                            $fechaFin = strtotime($fechas[1]);
                            $fechaFin = date('Y-m-d H:i:s', $fechaFin);
                            $cortesSemana[] = [
                                'fechaI' => $cortesSemanaCalc[$i]['fechaI'],
                                'fechaF' => $fechaFin
                            ];
                        }
                        if ($fechas[1] > $cortesSemanaCalc[$i]['fechaF']) {
                            $cortesSemana[] = [
                                'fechaI' => $cortesSemanaCalc[$i]['fechaI'],
                                'fechaF' => $cortesSemanaCalc[$i]['fechaF']
                            ];
                            $fechaI = strtotime('+1 day', strtotime($cortesSemanaCalc[$i]['fechaF']));
                            $fechaI = date('Y-m-d', $fechaI);
                            $cortesSemana[] = [
                                'fechaI' => $fechaI . ' 00:00:00',
                                'fechaF' => $fechas[1]
                            ];
                        }
                    } else {
                        if ($fechas[0] >= $cortesSemanaCalc[$i]['fechaI']) {
                            $fechaI = date('Y-m-d H:i:s', strtotime($fechas[0]));
                            $cortesSemana[] = ['fechaI' => $fechaI, 'fechaF' => $cortesSemanaCalc[$i]['fechaF']];
                        } else {
                            if ($fechas[1] < $cortesSemanaCalc[$i]['fechaF']) {
                                $fechaFin = strtotime($fechas[1]);
                                $fechaFin = date('Y-m-d H:i:s', $fechaFin);
                                $cortesSemana[] = [
                                    'fechaI' => $cortesSemanaCalc[$i]['fechaI'],
                                    'fechaF' => $fechaFin
                                ];
                            } else {
                                $cortesSemana[] = [
                                    'fechaI' => $cortesSemanaCalc[$i]['fechaI'],
                                    'fechaF' => $cortesSemanaCalc[$i]['fechaF']
                                ];
                            }
                        }
                    }
                }
            } else {
                if (($cortesSemanaCalc[$i]['fechaI'] >= $fechas[0] || $cortesSemanaCalc[$i]['fechaF'] >=
                    $fechas[0]) && ($cortesSemanaCalc[$i]['fechaI'] <=
                    $fechas[1] || $cortesSemanaCalc[$i]['fechaF'] <=
                    $fechas[1])) {
                    if ($fechas[0] >= $cortesSemanaCalc[$i]['fechaI']) {
                        $fechaI = strtotime($fechas[0]);
                        $fechaI = date('Y-m-d H:i:s', $fechaI);
                        $cortesSemana[] = ['fechaI' => $fechas[0], 'fechaF' => $cortesSemanaCalc[$i]['fechaF']];
                    } else {
                        $cortesSemana[] = ['fechaI' => $cortesSemanaCalc[$i]['fechaI'], 'fechaF' => $cortesSemanaCalc[$i]['fechaF']];
                    }
                }
            }
            $corteAnterior = $cortesSemanaCalc[$i];
        }

        return $cortesSemana;
    }

    /**
     * Funcion que calcula los cortes por meses 
     * @param type $rangofecha
     * @param type $id_usuario
     * @param type $tipo_corte
     * @return Array
     */
    public function calcularCortesMes($rangofecha = null, $id_usuario = null, $tipo_corte = null)
    {
        $fechas = explode(' - ', $rangofecha);
        $inicioMes = explode('-', $fechas[0]);
        $inicioMes[2] = '01';
        $inicioMes = implode('-', $inicioMes);
        $finMes = explode('-', $fechas[1]);
        $finMes[2] = date("d", (mktime(0, 0, 0, $finMes[1] + 1, 1, $finMes[0]) - 1));
        $finMes = implode('-', $finMes);
        $fechainicial = new DateTime($inicioMes);
        $fechafinal = new DateTime($finMes);
        $diferencia = $fechainicial->diff($fechafinal);
        $meses = ($diferencia->y * 12) + $diferencia->m;
        $meses = ($meses == 0) ? 1 : $meses + 1;
        $banderaSalida = true;
        $primeraIteraccion = true;
        $cortesMesCalc = [];
        do {
            $finMes = explode('-', $inicioMes);
            $finMes[2] = date("d", (mktime(0, 0, 0, $finMes[1] + 1, 1, $finMes[0]) - 1));
            $finMes = implode('-', $finMes);
            $cortesFecha = \app\models\SegmentoCorte::find()->from('tbl_segmento_corte sc')
                    ->join('INNER JOIN', 'tbl_corte_fecha cf', 'sc.corte_id = cf.corte_id')
                    ->where(('segmento_fecha_inicio BETWEEN :inicioMes ' . ' 00:00:00')  AND (' :finMes ' . ' 23:59:59') 
                         OR ('segmento_fecha_fin BETWEEN :inicioMes' . ' 00:00:00')   AND (' :finMes ' . ' 23:59:59') AND ('cf.usua_id = :id_usuario'))
                    ->andWhere('cf.tipo_corte = :tipo_corte')
                    ->addParams([':inicioMes' => $inicioMes,':finMes' => $finMes,':id_usuario' => $id_usuario,':tipo_corte' => $tipo_corte])
                    ->orderBy('segmento_fecha_inicio,segmento_fecha_fin ASC')
                    ->one();
            $inicioMes = ($primeraIteraccion) ? $fechas[0] : $inicioMes;
            if (count($cortesFecha) > 0) {
                $arrayCorteTemp = [
                    'fechaI' => $cortesFecha->segmento_fecha_inicio,
                    'fechaF' => $cortesFecha->segmento_fecha_fin
                ];

                if ($primeraIteraccion) {
                    if ($arrayCorteTemp['fechaI'] < $fechas[0]) {
                        $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($arrayCorteTemp['fechaF']))));
                        $cortesMesCalc[] = ['fechaI' => $fechas[0], 'fechaF' => $arrayCorteTemp['fechaF']];
                    } else {
                        $cortesMesCalc[] = ['fechaI' => $fechas[0], 'fechaF' => date('Y-m-d', (strtotime('-1 day', strtotime($arrayCorteTemp['fechaI'])))) . ' 23:59:59'];
                        $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($arrayCorteTemp['fechaF']))));
                        $cortesMesCalc[] = [
                            'fechaI' => $arrayCorteTemp['fechaI'],
                            'fechaF' => $arrayCorteTemp['fechaF']
                        ];
                    }
                } else {
                    if ($inicioMes < $arrayCorteTemp['fechaI']) {
                        $fechaTemp = date('Y-m-d', (strtotime('-1 day', strtotime($arrayCorteTemp['fechaI']))));
                        $cortesMesCalc[] = [
                            'fechaI' => $inicioMes . ' 00:00:00',
                            'fechaF' => $fechaTemp . ' 23:59:59'
                        ];
                    }
                    if ($fechas[1] < $arrayCorteTemp['fechaF']) {
                        $cortesMesCalc[] = [
                            'fechaI' => $arrayCorteTemp['fechaI'],
                            'fechaF' => $fechas[1]
                        ];
                        $banderaSalida = false;
                    } else {
                        $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($arrayCorteTemp['fechaF']))));
                        $cortesMesCalc[] = [
                            'fechaI' => $arrayCorteTemp['fechaI'],
                            'fechaF' => $arrayCorteTemp['fechaF']
                        ];
                    }
                }
            } else {
                if ($finMes < $fechas[1]) {
                    $cortesMesCalc[] = [
                        'fechaI' => $inicioMes . ' 00:00:00',
                        'fechaF' => $finMes . ' 23:59:59'
                    ];
                    $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($finMes))));
                } else {
                    $cortesMesCalc[] = [
                        'fechaI' => $inicioMes . ' 00:00:00',
                        'fechaF' => $fechas[1]
                    ];
                    $banderaSalida = false;
                }
            }
            $primeraIteraccion = false;
        } while ($banderaSalida);
        return $cortesMesCalc;
    }

    /**
     * Funcion que valida la metrica a calcular
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $metrica
     * @return string
     */
    public function validarMetrica($metrica = null)
    {
        $metrica = (int) $metrica;
        switch ($metrica) {
            case 1:
                $baseConsulta = 'i1_nmcalculo';
                break;
            case 2:
                $baseConsulta = 'i2_nmcalculo';
                break;
            case 3:
                $baseConsulta = 'i3_nmcalculo';
                break;
            case 4:
                $baseConsulta = 'i4_nmcalculo';
                break;
            case 5:
                $baseConsulta = 'i5_nmcalculo';
                break;
            case 6:
                $baseConsulta = 'i6_nmcalculo';
                break;
            case 7:
                $baseConsulta = 'i7_nmcalculo';
                break;
            case 8:
                $baseConsulta = 'i8_nmcalculo';
                break;
            case 9:
                $baseConsulta = 'i9_nmcalculo';
                break;
            case 10:
                $baseConsulta = 'i10_nmcalculo';
                break;
            case 11:
                $baseConsulta = 'score';
                break;
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
            case 18:
            case 19:
            case 20:
            case 21:
            case 22:
                $baseConsulta = 'basesatisfaccion_id';
                break;
            case 23:
            case 24:
            case 25:
            case 30:
                $baseConsulta = 'usua_id';
                break;
            default:
                echo "no se cumple ninguna";
        }
        return $baseConsulta;
    }

    /**
     * Funcion que permite validar los cortes que se van a parametrizar
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function actionValidarcortes()
    {
        $arrayCortes = Yii::$app->request->post('SegmentoCorte');
        $tipoCorte = Yii::$app->request->get('Tipo_corte');
        $rangofecha = Yii::$app->request->post('rangofecha');
        $fechas = explode(' - ', $rangofecha);
        $fechas[0] = $fechas[0];
        $fechas[1] = $fechas[1];
        $etiqueta = ($tipoCorte == 1) ? 'semana' : 'mes';
        $corteAnterior = null;
        $bandera = 0;
        $msg = '';
        if ($tipoCorte == 1) {
            for ($i = 1; $i <= count($arrayCortes); $i++) {
                $corteActual = explode(' - ', $arrayCortes[$etiqueta . $i]);
                if (!is_null($corteAnterior)) {

                    if ($corteAnterior[1] < $corteActual[0]) {
                        $fechafincomparar = strtotime('+1 day', strtotime($corteAnterior[1]));
                        $fechafincomparar = date('Y-m-d', $fechafincomparar);
                        $fechafincomparar = $fechafincomparar;
                        if ($fechafincomparar < $corteActual[0]) {
                            $msg = 'Existen días sin seleccionar, ¿Desea continuar?';
                            $bandera = 2;
                            break;
                        }
                    }
                    if ($corteActual[0] != 0) {
                        if ($corteAnterior[1] >= $corteActual[0]) {
                            $msg = 'Existen días seleccionados en varios cortes, ¿Desea continuar?';
                            $bandera = 1;
                            break;
                        }
                    } else {
                        break;
                    }

                    $index = $i + 1;
                    if (isset($arrayCortes[$etiqueta . $index])) {
                        if ($arrayCortes[$etiqueta . $index] == '') {
                            if ($corteActual[1] < $fechas[1]) {
                                $bandera = 1;
                                $msg = 'Existen días sin seleccionar, ¿Desea continuar?';
                            }
                        }
                    }
                } else {
                    if ($fechas[0] < $corteActual[0]) {
                        $bandera = 1;
                        $msg = 'Existen días sin seleccionar, ¿Desea continuar?';
                        break;
                    }
                }
                $corteAnterior = $corteActual;
            }
        } else {
            $corteActual = explode(' - ', $arrayCortes[$etiqueta]);
            if ($fechas[0] < $corteActual[0]) {
                $msg = 'Existen días sin seleccionar, ¿Desea continuar?';
                $bandera = 1;
            }
            if ($corteActual[1] < $fechas[1]) {
                $msg = 'Existen días sin seleccionar, ¿Desea continuar?';
                $bandera = 1;
            }
        }
        $out['results'] = $bandera;
        $out['msg'] = $msg;
        echo \yii\helpers\Json::encode($out);
    }

    /**
     * Funcion que genera la tabla que se enviara en el archivo excel
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $datosTabla
     * @param type $dimensiones
     * @return string|array
     */
    public function actionGenerartablaexcel($datosTabla = null, $dimensiones = null, $arrayTotal = null, $control = null)
    {

        
        $dimensiones = \app\models\Dimensiones::find()->where("id IN(:dimensiones)")
        ->addParams([':dimensiones' =>$dimensiones ])
        ->orderBy("id ASC")->asArray()->all();
        $arrayTabla = [];
        try {
            if ($control == "proceso") {
                for ($index = 0; $index < count($dimensiones); $index++) {
                    $arrayMetrica = [];
                    foreach ($datosTabla as $key => $datoMetrica) {
                        foreach ($datoMetrica as $keyMetrica => $datoDimension) {
                            if (count($datoDimension[0]) > 0) {
                                foreach ($datoDimension[0] as $value) {
                                    if ($value['dimension_id'] == $dimensiones[$index]['id']) {

                                        //IF VOLUMENES NO MOSTRAR PROMEDIO
                                        if ($keyMetrica == 'basesatisfaccion_id') {
                                            $arrayTabla[$dimensiones[$index]['id']][$key][$keyMetrica][$value['arbol_id']] = ['prom' => ' - ', 'cant' => $value['total']];
                                        } else {
                                            $arrayTabla[$dimensiones[$index]['id']][$key][$keyMetrica][$value['arbol_id']] = ['prom' => round($value['promedio'] *
                                                100, 2) . '%', 'cant' => $value['total']];
                                        }
                                        if (!in_array($keyMetrica, $arrayMetrica)) {
                                            $arrayMetrica[] = $keyMetrica;
                                        }
                                    }
                                }
                            } /* else {
                                      $arrayTabla[$dimensiones[$index]['id']][$key][0] = ['prom'=>"-",'cant'=>"-"];
                                      } */
                        }
                    }
                    foreach ($arrayTotal as $keyTotalMetrica => $totalMetrica) {
                        foreach ($totalMetrica as $keyM => $datoDimension) {
                            if (count($totalMetrica) > 0) {
                                if (
                                    $datoDimension['dimension_id'] ==
                                    $dimensiones[$index]['id']
                                ) {

                                    //IF VOLUMENES NO MOSTRAR PROMEDIO
                                    if ($arrayMetrica[$keyTotalMetrica] == 'basesatisfaccion_id') {
                                        $arrayTabla[$dimensiones[$index]['id']]['total'][$arrayMetrica[$keyTotalMetrica]][$datoDimension['arbol_id']] = ['prom' => ' - ', 'cant' => $datoDimension['total']];
                                    } else {
                                        $arrayTabla[$dimensiones[$index]['id']]['total'][$arrayMetrica[$keyTotalMetrica]][$datoDimension['arbol_id']] = ['prom' => round($datoDimension['promedio'] *
                                            100, 2) . '%', 'cant' => $datoDimension['total']];
                                    }
                                }
                            }/* else {
                                      $arrayTabla[$dimensiones[$index]['id']]['total'][] =['prom'=>"-",'cant'=>"-"];
                                      } */
                        }
                    }
                }
            } else {
                for ($index = 0; $index < count($dimensiones); $index++) {
                    $arrayMetrica = [];
                    foreach ($datosTabla as $key => $datoMetrica) {
                        foreach ($datoMetrica as $keyM => $datoDimension) {
                            if (count($datoDimension[0]) > 0) {
                                foreach ($datoDimension[0] as $value) {
                                    if ($value['dimension_id'] == $dimensiones[$index]['id']) {
                                        if ($keyM == 'usua_id') {
                                            $arrayTabla[$dimensiones[$index]['id']][$key][$keyM][$value['arbol_id']][$value['usua_usuario']][] = ' - ';
                                            $arrayTabla[$dimensiones[$index]['id']][$key][$keyM][$value['arbol_id']][$value['usua_usuario']][] = $value['total'];
                                        } else {
                                            $arrayTabla[$dimensiones[$index]['id']][$key][$keyM][$value['arbol_id']][$value['usua_usuario']][] = round($value['promedio'] *
                                                100, 2) . '%';
                                            $arrayTabla[$dimensiones[$index]['id']][$key][$keyM][$value['arbol_id']][$value['usua_usuario']][] = $value['total'];
                                        }
                                        if (!in_array($keyM, $arrayMetrica)) {
                                            $arrayMetrica[] = $keyM;
                                        }
                                    }
                                }
                            } else {
                                foreach ($datoDimension[0] as $value) {
                                    if ($value['dimension_id'] == $dimensiones[$index]['id']) {
                                        $arrayTabla[$dimensiones[$index]['id']][$key][$keyM][$value['arbol_id']][$value['usua_usuario']][] = '-';
                                        $arrayTabla[$dimensiones[$index]['id']][$key][$keyM][$value['arbol_id']][$value['usua_usuario']][] = '-';
                                        if (!in_array($keyM, $arrayMetrica)) {
                                            $arrayMetrica[] = $keyM;
                                        }
                                    }
                                }
                            }
                        }
                    }
                    foreach ($arrayTotal as $key => $datoMetrica) {
                        foreach ($datoMetrica as $keyM => $datoDimension) {
                            if ($datoDimension['dimension_id'] == $dimensiones[$index]['id']) {
                                if (!empty($arrayMetrica)) {
                                    if ($arrayMetrica[$key] == 'usua_id') {
                                        $arrayTabla[$dimensiones[$index]['id']]['total'][$arrayMetrica[$key]][$datoDimension['arbol_id']][$datoDimension['usua_usuario']][] = ' - ';
                                        $arrayTabla[$dimensiones[$index]['id']]['total'][$arrayMetrica[$key]][$datoDimension['arbol_id']][$datoDimension['usua_usuario']][] = $datoDimension['total'];
                                    } else {
                                        $arrayTabla[$dimensiones[$index]['id']]['total'][$arrayMetrica[$key]][$datoDimension['arbol_id']][$datoDimension['usua_usuario']][] = round($datoDimension['promedio'] *
                                            100, 2) . '%';
                                        $arrayTabla[$dimensiones[$index]['id']]['total'][$arrayMetrica[$key]][$datoDimension['arbol_id']][$datoDimension['usua_usuario']][] = $datoDimension['total'];
                                    }
                                }
                            } else {
                                #code
                            }
                        }
                    }
                }
            }
            return $arrayTabla;
        } catch (Exception $exc) {
            return false;
        }
        return false;
    }

    /**
     * Funcion que genera el archivo excel con los datos obtenidos.
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $tablaExcel
     * @param type $cortes
     * @param type $id_dimensiones
     * @param type $ids_metricas
     * @param type $banderaEnvio
     * @param type $id_usuario
     * @return boolean
     */
    public function generarExcel($tablaExcel = null, $cortes = null, $id_dimensiones = null, $ids_metricas = null, $banderaEnvio = null, $id_usuario, $tipo_corte = null, $control = null, $ids_arbols)
    {

        //QUITO DIMENSIONES SIN TOTAL                
        foreach ($tablaExcel as $key => $value) {
            if (!isset($value['total'])) {
                unset($tablaExcel[$key]);
            } else {
                #code
            }
        }

        $styleArray = array(
            'font' => array(
                'bold' => true,
            ),
            'alignment' => array(
                'horizontal' => \PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
            ),
            'borders' => array(
                'top' => array(
                    'style' => \PHPExcel_Style_Border::BORDER_THIN,
                ),
            ),
            'fill' => array(
                'type' => \PHPExcel_Style_Fill::FILL_GRADIENT_LINEAR,
                'rotation' => 90,
                'startcolor' => array(
                    'argb' => 'FFA0A0A0',
                ),
                'endcolor' => array(
                    'argb' => 'FFFFFFFF',
                ),
            ),
        );
        $dimensiones = \app\models\Dimensiones::find()->where("id IN(:id_dimensiones)")
        ->addParams([':id_dimensiones' =>$id_dimensiones ])
        ->orderBy("id ASC")->asArray()->all();
        $metricas = explode(',', $ids_metricas);
        $arrayMetrica = [];
        for ($a = 0; $a < count($metricas); $a++) {
            $arrayMetrica[] = \app\models\Metrica::find()->where("id = :metricas")
            ->addParams([':metricas' =>$metricas[$a] ])
            ->asArray()->one();
        }
        $arrayCortes = [];
        for ($index = 0; $index < count($cortes); $index++) {
            $arrayCortes[] = $cortes[$index]['fechaI'] . " - " . $cortes[$index]['fechaF'];
        }
        $arrayLabelMetrica = [];
        $arrayCortes[] = 'total';
        set_time_limit(0);
        $objPHPexcel = new \PHPExcel();
        $objPHPexcel->setActiveSheetIndex(0);
        $column = 'A';
        $row = 1;
        $banderaValorado = false;
        try {
            if ($control == "proceso") {
                foreach ($dimensiones as $key => $value) {
                    if (isset($tablaExcel[$value['id']])) {
                        $arrayValorador = [];
                        $column = 'A';
                        /* Se imprimen titulos */
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value['name']);
                        $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                        $column++;
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Métrica");
                        $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                        $column++;
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Árbol");
                        $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                        $column++;
                        $inicioRow = $row;
                        $row++;
                        $row = $inicioRow;
                        /* Se imprimen titulos  de los cortes */
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']])) {
                                    if ($tipo_corte == 3) {
                                        $etiquetaCorte = explode(' - ', str_replace(['00:00:00', '23:59:59'], '', $corte));
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $etiquetaCorte[0]));
                                    } else {
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $corte));
                                    }
                                }
                            } else {
                                if ($tipo_corte == 3) {
                                    $etiquetaCorte = explode(' - ', str_replace(['00:00:00', '23:59:59'], '', $corte));
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $etiquetaCorte[0]));
                                } else {
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $corte));
                                }
                            }
                            $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                            $column++;
                        }
                        $row++;
                        $ultimoDatos = '';
                        /* Se recorre todo el arreglo de datos buscando todos los arboles
                                  para agregarlos de una manera ordenada */
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']]) && $corte != 'total') {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            if (!in_array('dato' . '-' . $keyMetrica . '-' . $keyValorador, $arrayLabelMetrica)) {
                                                $arrayLabelMetrica[] = 'dato' . '-' . $keyMetrica . '-' . $keyValorador;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        asort($arrayLabelMetrica); //se ordena para visualizar las metricas en orden
                        /* Se imprimen titulos los nombres de las metricas y de los arboles, se guarda un array con las posiciones de cada combinacion
                                  arbol-metrica */
                        foreach ($arrayLabelMetrica as $label) {
                            $datoLabel = explode('-', $label);
                            $arr_arboles = \app\models\Arboles::find()->where("id IN(:datoLabel)")
                            ->addParams([':datoLabel'=>$datoLabel['2']])
                            ->orderBy("id ASC")->one();
                            $column = "B";

                            if (!array_key_exists($datoLabel['0'] . '-' . $datoLabel['1'] . '-' . $datoLabel['2'], $arrayValorador)) {
                                $arrayValorador[$datoLabel['0'] . '-' . $datoLabel['1'] . '-' . $datoLabel['2']] = $row;
                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $datoLabel['1']));
                                $column++;
                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $arr_arboles->name));
                                $row += 1;
                                $ultimoDatos = $datoLabel['0'] . '-' . $datoLabel['1'] . '-' . $datoLabel['2'];
                            }
                        }

                        /* Se imprimen los valores para cada combinacion arbol-metrica */
                        $column = "D";
                        $rowinicioCantidad = 0; // variable para manejar el indece donde se pondra el total
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']]) && $corte != 'total') {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            if (array_key_exists('dato' . '-' . $keyMetrica . '-' . $keyValorador, $arrayValorador)) {
                                                $row = $arrayValorador['dato' . '-' . $keyMetrica . '-' . $keyValorador];
                                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueValorador['prom']);
                                            }
                                        }
                                    }
                                } else {
                                    if ($corte == 'total') {
                                        foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                            foreach ($metrica as $keyValorador => $valueValorador) {
                                                if (array_key_exists('dato' . '-' . $keyMetrica . '-' . $keyValorador, $arrayValorador)) {
                                                    $row = $arrayValorador['dato' . '-' . $keyMetrica . '-' . $keyValorador];
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueValorador['prom']);
                                                    $row = ($rowinicioCantidad > $row) ? $rowinicioCantidad : $row;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $column++;
                        }
                        /* Se copia estructtura anterior para montar el total por cada usuario (valoraciones) */

                        /* Se imprimen titulos de arbol y cantidad y se guarda array con posicion de cada arbl-cantidad */
                        $row = $arrayValorador[$ultimoDatos];
                        $row++;
                        $ultimoDatos = '';
                        $arrayValorador = [];
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']]) && $corte != 'total') {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            $arr_arboles = \app\models\Arboles::find()->where("id IN(:keyValorador)")
                                            ->addParams([':keyValorador'=>$keyValorador])
                                            ->orderBy("id ASC")->one();
                                            $column = "B";
                                            if ($banderaValorado == false) {
                                                $banderaValorado = true;
                                                $arrayValorador[$keyValorador] = (!array_key_exists($keyValorador, $arrayValorador)) ? $row : $arrayValorador[$keyValorador];
                                                $arrayValorador['total' . '-' . $keyValorador] = (!array_key_exists('total' . '-' . $keyValorador, $arrayValorador)) ? $row : $arrayValorador['total' . '-' . $keyValorador];
                                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', "Cantidad"));
                                                $column++;
                                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $arr_arboles->name));
                                                $row += 1;
                                                $ultimoDatos = $keyValorador;
                                            } else {
                                                if (!array_key_exists($keyValorador, $arrayValorador)) {
                                                    $arrayValorador[$keyValorador] = (!array_key_exists($keyValorador, $arrayValorador)) ? $row : $arrayValorador[$keyValorador];
                                                    $arrayValorador['total' . '-' . $keyValorador] = (!array_key_exists('total' . '-' . $keyValorador, $arrayValorador)) ? $row : $arrayValorador['total' . '-' . $keyValorador];
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Cantidad");
                                                    $column++;
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $arr_arboles->name));
                                                    $row += 1;
                                                    $ultimoDatos = $keyValorador;
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($banderaValorado == false) {
                                    $banderaValorado = true;
                                    $arrayValorador['1'] = $row;
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                    $column++;
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                    $row += 1;
                                    $ultimoDatos = '1';
                                } else {
                                    if (!array_key_exists('1', $arrayValorador)) {
                                        $arrayValorador['1'] = $row;
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                        $column++;
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                        $row += 1;
                                        $ultimoDatos = '1';
                                    }
                                }
                            }
                        }
                        $column = "D";
                        /* Se imprimen valores para cada combinacion arbol-cantidad */
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {

                                if (array_key_exists($corte, $tablaExcel[$value['id']]) && $corte != 'total') {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            if (array_key_exists($keyValorador, $arrayValorador)) {
                                                $row = $arrayValorador[$keyValorador];
                                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueValorador['cant']);
                                            }
                                        }
                                    }
                                } else {
                                    if ($corte == 'total') {
                                        foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                            foreach ($metrica as $keyValorador => $valueValorador) {
                                                if (array_key_exists('total' . '-' . $keyValorador, $arrayValorador)) {
                                                    $row = $arrayValorador['total' . '-' . $keyValorador];
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueValorador['cant']);
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                            }
                            $column++;
                        }
                        $row = ($arrayValorador[$ultimoDatos] + 5);
                    }
                }
            } else {

                foreach ($dimensiones as $key => $value) {
                    if (isset($tablaExcel[$value['id']])) {
                        $arrayValorador = [];
                        $column = 'A';
                        /* Se imprimen titulos arbol-valorador y metrica */
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value['name']);
                        $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                        $column++;
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Valorador");
                        $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                        $column++;
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Métrica");
                        $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                        $column++;
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Árbol");
                        $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                        $column++;
                        $inicioRow = $row;
                        $row++;
                        $row = $inicioRow;
                        $inicioColumn = $column;
                        /* Se imprimen cortes */
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']])) {
                                    if ($tipo_corte == 3) {
                                        $etiquetaCorte = explode(' - ', str_replace(['00:00:00', '23:59:59'], '', $corte));
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $etiquetaCorte[0]));
                                    } else {
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $corte));
                                    }
                                }
                            } else {
                                if ($tipo_corte == 3) {
                                    $etiquetaCorte = explode(' - ', str_replace(['00:00:00', '23:59:59'], '', $corte));
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $etiquetaCorte[0]));
                                } else {
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $corte));
                                }
                            }
                            $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                            $column++;
                        }
                        $row++;
                        $ultimoDatos = '';
                        /* recorro el arreglo de datos buscando todos los ids de arboles */

                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']]) && $corte != 'total') {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            $arr_arboles = \app\models\Arboles::find()->where("id IN(:keyValorador)")
                                            ->addParams([':keyValorador'=>$keyValorador])
                                            ->orderBy("id ASC")->one();
                                            foreach ($valueValorador as $keyDato => $valueDato) {
                                                if (!in_array('datos' . '-' . $keyMetrica . '-' . $keyValorador . '-' . $keyDato, $arrayValorador)) {
                                                    $arrayLabelMetrica[] = 'datos' . '-' . $keyMetrica . '-' . $keyValorador . '-' . $keyDato;
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        //ordeno el arreglo de arboles para poner los labels ordenados
                        asort($arrayLabelMetrica);
                        /* Se imprimen valores de cada valorador-arbol-metrica y se guarda array de posicion de cada combinacion valorador-arbol-metrica */
                        foreach ($arrayLabelMetrica as $label) {
                            $datoLabel = explode('-', $label);
                            $arr_arboles = \app\models\Arboles::find()->where("id IN(:datoLabel)")
                            ->addParams([':datoLabel'=>$datoLabel['2']])
                            ->orderBy("id ASC")->one();
                            $column = "B";
                            if (!array_key_exists($datoLabel['0'] . '-' . $datoLabel['1'] . '-' . $datoLabel['3'] . '-' . $datoLabel['2'], $arrayValorador)) {
                                $arrayValorador[$datoLabel['0'] . '-' . $datoLabel['1'] . '-' . $datoLabel['3'] . '-' . $datoLabel['2']] = $row;
                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $datoLabel['3']);
                                $column++;
                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $datoLabel['1']));
                                $column++;
                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $arr_arboles->name));
                                $row += 1;
                                $ultimoDatos = $datoLabel['0'] . '-' . $datoLabel['1'] . '-' . $datoLabel['3'] . '-' . $datoLabel['2'];
                            }
                        }

                        $column = "E";
                        $rowinicioCantidad = 0; // variable para manejar el indece donde se pondra el total
                        /* Se imprimen valores de la combinacion guardad en el array */
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']]) && $corte != 'total') {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            foreach ($valueValorador as $keyDato => $valueDato) {
                                                if (array_key_exists('datos' . '-' . $keyMetrica . '-' . $keyDato . '-' . $keyValorador, $arrayValorador)) {
                                                    $row = $arrayValorador['datos' . '-' . $keyMetrica . '-' . $keyDato . '-' . $keyValorador];
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueDato[0]);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if ($corte == 'total') {
                                        foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                            foreach ($metrica as $keyValorador => $valueValorador) {
                                                foreach ($valueValorador as $keyDato => $valueDato) {
                                                    if (array_key_exists('datos' . '-' . $keyMetrica . '-' . $keyDato . '-' . $keyValorador, $arrayValorador)) {
                                                        $row = $arrayValorador['datos' . '-'  . $keyMetrica . '-' . $keyDato . '-' . $keyValorador];
                                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueDato[0]);
                                                        $row = ($rowinicioCantidad > $row) ? $rowinicioCantidad : $row;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $column++;
                        }
                        /* Se copia estructtura anterior para montar el total por cada usuario (valoraciones) */
                        $row = $arrayValorador[$ultimoDatos];
                        $row++;
                        $ultimoDatos = '';
                        $arrayValorador = [];
                        /* Se imprimen cada valorador-arbol y cantidad y se guarda la posicion en array de combinacion valorador-cantidad-arbol */
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']]) && $corte != 'total') {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            $arr_arboles = \app\models\Arboles::find()->where("id IN(:keyValorador)")
                                            ->addParams([':keyValorador'=>$keyValorador])
                                            ->orderBy("id ASC")->one();
                                            foreach ($valueValorador as $keyDato => $valueDato) {
                                                $column = "B";
                                                if ($banderaValorado == false) {
                                                    $banderaValorado = true;
                                                    $arrayValorador[$keyDato . '-' . $keyValorador] = $row;
                                                    $arrayValorador['total' . '-' . $keyDato . '-' . $keyValorador] = (!array_key_exists('total' . '-' . $keyDato . '-' . $keyValorador, $arrayValorador)) ? $row : $arrayValorador['total' . '-' . $keyDato . '-' . $keyValorador];
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $keyDato);
                                                    $column++;
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', "Cantidad"));
                                                    $column++;
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $arr_arboles->name));
                                                    $row += 1;
                                                    $ultimoDatos = $keyDato . '-' . $keyValorador;
                                                } else {
                                                    if (!array_key_exists($keyDato . '-' . $keyValorador, $arrayValorador)) {
                                                        $arrayValorador[$keyDato . '-' . $keyValorador] = $row;
                                                        $arrayValorador['total' . '-' . $keyDato . '-' . $keyValorador] = (!array_key_exists('total' . '-' . $keyDato . '-' . $keyValorador, $arrayValorador)) ? $row : $arrayValorador['total' . '-' . $keyDato . '-' . $keyValorador];
                                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $keyDato);
                                                        $column++;
                                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Cantidad");
                                                        $column++;
                                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $arr_arboles->name));
                                                        $row += 1;
                                                        $ultimoDatos = $keyDato . '-' . $keyValorador;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if ($banderaValorado == false) {
                                    $banderaValorado = true;
                                    $arrayValorador['1'] = $row;
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                    $column++;
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                    $column++;
                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                    $row += 1;
                                    $ultimoDatos = '1';
                                } else {
                                    if (!array_key_exists('1', $arrayValorador)) {
                                        $arrayValorador['1'] = $row;
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                        $column++;
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                        $column++;
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                                        $row += 1;
                                        $ultimoDatos = '1';
                                    }
                                }
                            }
                        }

                        $column = "E";
                        /* Se imprimen valores dependiendo del array */
                        foreach ($arrayCortes as $corte) {
                            if (isset($tablaExcel[$value['id']])) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']]) && $corte != 'total') {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            foreach ($valueValorador as $keyDato => $valueDato) {
                                                if (array_key_exists($keyDato . '-' . $keyValorador, $arrayValorador)) {
                                                    $row = $arrayValorador[$keyDato . '-' . $keyValorador];
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueDato[1]);
                                                }
                                            }
                                        }
                                    }
                                } else {
                                    if ($corte == 'total') {
                                        foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                            foreach ($metrica as $keyValorador => $valueValorador) {
                                                foreach ($valueValorador as $keyDato => $valueDato) {
                                                    if (array_key_exists('total' . '-' . $keyDato . '-' . $keyValorador, $arrayValorador)) {
                                                        $row = $arrayValorador['total' . '-' . $keyDato . '-' . $keyValorador];
                                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueDato[1]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, '-');
                            }
                            $column++;
                        }
                        $row = ($arrayValorador[$ultimoDatos] + 5);
                    }
                }
            }
            if (!$banderaEnvio) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                if ($control == "proceso") {
                    header('Content-Disposition: attachment;filename="ControlProceso.xlsx"');
                } else {
                    header('Content-Disposition: attachment;filename="ControlPersona.xlsx"');
                }
                header('Cache-Control: max-age=0');
                $objWriter = new \PHPExcel_Writer_Excel2007();
                $objWriter->setPHPExcel($objPHPexcel);
                $objWriter->save('php://output');
            } else {
                $objWriter = new \PHPExcel_Writer_Excel2007($objPHPexcel);
                if ($control == "proceso") {
                    $objWriter->save(\Yii::$app->basePath . '\\web\\excelEnvio\\ControlProceso' . $id_usuario . '.xlsx');
                    return \Yii::$app->basePath . '\\web\\excelEnvio\\ControlProceso' . $id_usuario . '.xlsx';
                } else {
                    $objWriter->save(\Yii::$app->basePath . '\\web\\excelEnvio\\ControlPersona' . $id_usuario . '.xlsx');
                    return \Yii::$app->basePath . '\\web\\excelEnvio\\ControlPersona' . $id_usuario . '.xlsx';
                }
            }
        } catch (Exception $exc) {
            return false;
        }
    }

    /**
     * Funcion que sera llamada en el cron, la cual permite enviar los correos 
     * a los usuarios que esten parametrizados
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     */
    public function actionEnviarcorreo($control)
    {
        try {
            $filtrosEnvio = \app\models\FiltrosControl::find()->all();
            $ejecucionformulario = new \app\models\Ejecucionformularios();
            foreach ($filtrosEnvio as $value) {
                $usuario = \app\models\Usuarios::find()->where(['usua_id' => $value->usua_id])->one();
                if ($value->guardar_filtro == 1) {
                    $metricas = \app\models\Metrica::find()->where("id IN(:value->ids_metricas)")
                    ->addParams([':value->ids_metricas'=>$value->ids_metricas])
                    ->asArray()->all();
                    $metrica = $this->validarMetrica($metricas[0]['id']);
                    $consulta = $ejecucionformulario->getDatabygrafexcelpersonacount($value->ids_arboles, $value->ids_dimensiones, $value->rango_fecha, $metrica, $value);
                    $value->valorador = $value->ids_valoradores;
                    $value->equiposvalorador = $value->ids_equipos_valoradores;
                    $value->rol = $value->ids_roles;
                    if ($consulta > 0) {
                        $data = $this->construirTabla($value->rango_fecha, $value->usua_id, $value->corte_id, $value->ids_metricas, $value->ids_dimensiones, $value->ids_arboles, $value, "1", $control, 'correo');
                        $tablaExcel = $this->actionGenerartablaexcel($data['datos'], $value->ids_dimensiones, $data['total'], $control);
                        if ($tablaExcel != false && $usuario->usua_email != "") {
                            $ruta = $this->generarExcel($tablaExcel, $data['cortes'], $value->ids_dimensiones, $value->ids_metricas, true, Yii::$app->user->identity->id, $value->corte_id, $control, $value->ids_arboles);
                            $correo = Yii::$app->mailer->compose('@app/views/control/plantillaCorreo')
                                ->setFrom(\Yii::$app->params['email_envio_proceso'])
                                ->setTo($usuario->usua_email)
                                ->attach($ruta)
                                ->attach(Yii::$app->basePath . "/web/images/plantilla.png");
                            if ($control == 'proceso') {
                                $correo->setSubject('Excel Control Proceso');
                            } else {
                                $correo->setSubject('Excel Productividad Individual');
                            }
                            $correo->send();
                        } else {
                            $msj = "Error enviando correo, verifique que el usuario " . $usuario->usua_usuario;
                            $msj .= " tiene un correo asociado.";
                            //ESCRIBO EN EL LOG
                            \Yii::error($msj, 'tableroControl');
                        }
                    } else {
                        $msj = "Error enviando correo, verifique que el usuario " . $usuario->usua_usuario;
                        $msj .= " tiene un correo asociado.";
                        //ESCRIBO EN EL LOG
                        \Yii::error($msj, 'tableroControl');
                    }
                }
            }
        } catch (Exception $exc) {
            $msj = "----------->Error enviando los correos";
            //ESCRIBO EN EL LOG
            \Yii::error($msj, 'tableroControl');
        }
    }

    public function repetirCorte($datos, $modelCorte, $rangofecha)
    {
        $arrayCortes = [];
        $index = 0;
        if ($modelCorte->tipo_corte == 1) {
            if ($datos['semana5'] == '') {
                unset($datos['semana5']);
            }
        }
        $datosReal = $datos;
        foreach ($datosReal as $key => $dato) {
            $tempSemana = explode(' - ', $dato);
            $tempoFechainicial = getdate(strtotime($tempSemana[1]));
            $DiaFinal = date("d", (mktime(0, 0, 0, $tempoFechainicial['mon'] + 1, 1, $tempoFechainicial['year']) - 1));
            if ($tempoFechainicial['mday'] == $DiaFinal) {
                $tempSemana[1] = 'finMes';
            }
            $datos[$key] = implode(' - ', $tempSemana);
        }

        do {
            $tempCorte = new \app\models\CorteFecha();
            $tempCorte->tipo_corte = $modelCorte->tipo_corte;
            $tempCorte->usua_id = $modelCorte->usua_id;
            $tempCorte->band_repetir = $modelCorte->band_repetir;
            $tempCorte->save();
            foreach ($datosReal as $key => $value) {
                if ($value != '') {
                    $tempSemana = explode(' - ', $value);
                    if ($index == 0) {
                        $tempSemana[0] = date('Y-m-d', (strtotime($tempSemana[0]))) . ' 00:00:00';
                        $tempSemana[1] = date('Y-m-d', (strtotime($tempSemana[1]))) . ' 23:59:59';
                    } else {
                        $banderaFin = explode(' - ', $datos[$key]);
                        if ($banderaFin[1] == 'finMes') {
                            $fechaInicial = getdate(strtotime($tempSemana[0]));
                            $fechaInicial['mon'] += 1;
                            if ($fechaInicial['mon'] > 12) {
                                $fechaInicial['mon'] = 1;
                                $fechaInicial['year'] += 1;
                            }
                            $ultimodiaFecha = date("d", (mktime(0, 0, 0, $fechaInicial['mon'] + 1, 1, $fechaInicial['year']) - 1));
                            $tempSemana[0] = $fechaInicial['year'] . '-' . $fechaInicial['mon'] . '-' . $fechaInicial['mday'];
                            $tempSemana[1] = $fechaInicial['year'] . '-' . $fechaInicial['mon'] . '-' . $ultimodiaFecha;
                        } else {
                            $fechaInicial = getdate(strtotime($tempSemana[0]));
                            $fechaFinal = getdate(strtotime($tempSemana[1]));
                            $fechaInicial['mon'] += 1;
                            $fechaFinal['mon'] += 1;
                            if ($fechaInicial['mon'] > 12) {
                                $fechaInicial['mon'] = 1;
                                $fechaInicial['year'] += 1;
                            }
                            if ($fechaFinal['mon'] > 12) {
                                $fechaFinal['mon'] = 1;
                                $fechaFinal['year'] += 1;
                            }
                            $inicialfecha = date("d", (mktime(0, 0, 0, $fechaInicial['mon'] + 1, 1, $fechaInicial['year']) - 1));
                            $ultimodiaFecha = date("d", (mktime(0, 0, 0, $fechaFinal['mon'] + 1, 1, $fechaFinal['year']) - 1));
                            if ($inicialfecha < $fechaInicial['mday']) {
                                $fechaInicial['mday'] = $inicialfecha;
                            }
                            if ($ultimodiaFecha < $fechaFinal['mday']) {
                                $fechaFinal['mday'] = $ultimodiaFecha;
                            }
                            $tempSemana[0] = $fechaInicial['year'] . '-' . $fechaInicial['mon'] . '-' . $fechaInicial['mday'];
                            $tempSemana[1] = $fechaFinal['year'] . '-' . $fechaFinal['mon'] . '-' . $fechaFinal['mday'];
                        }
                    }
                    $modelseg = new \app\models\SegmentoCorte();
                    $modelseg->segmento_fecha_inicio = $tempSemana[0] . ' 00:00:00';
                    $modelseg->segmento_fecha_fin = $tempSemana[1] . ' 23:59:59';
                    $modelseg->segmento_nombre = $key;
                    $modelseg->corte_id = $tempCorte->corte_id;
                    $modelseg->save();
                    $timeMes = strtotime($tempSemana[1]);
                    $timeMes = getdate($timeMes);
                    $arrayCortes[$index][$key] = implode(' - ', $tempSemana);
                }
            }
            $tempCorte->corte_descripcion = $timeMes['month'];
            $tempCorte->save();
            $modelseg->save();
            $datosReal = $arrayCortes[$index];
            $index++;
        } while ($index <= 11);
    }

    public function construirArbolgrupos($arraArboles)
    {
        $arrayArboles = [];
        $consultaArboles = \app\models\Arboles::find()->where('id IN (:arraArboles)')
        ->addParams([':arraArboles'=>$arraArboles])
        ->orderBy('dsorden ASC')->asArray()->all();
        foreach ($consultaArboles as $key => $value) {
            $arraArboles .= ',' . $value['arbol_id'];
        }
        $consultaArboles = \app\models\Arboles::find()->where('id IN (:arraArboles)')
        ->addParams([':arraArboles'=>$arraArboles])
        ->orderBy('dsorden ASC')->asArray()->all();
        $i = 0;
        $arbolBase = $consultaArboles[$i];
        $banderaArbol = $arbolBase['arbol_id'];
        $arrayArboles[$arbolBase['id']] = ['hijos' => []];
        $i++;
        do {
            if ($arbolBase['id'] == $consultaArboles[$i]['arbol_id']) {
                $arrayArboles[$arbolBase['id']]['hijos'][] = $consultaArboles[$i]['id'];
            } else {
                if ($consultaArboles[$i]['arbol_id'] == $banderaArbol) {
                    $arbolBase = $consultaArboles[$i];
                    $arrayArboles[$arbolBase['id']] = ['hijos' => []];
                } else {
                    $arrayArboles[$arbolBase['id']]['hijos'][] = $consultaArboles[$i]['id'];
                }
            }
            $i++;
        } while ($i < count($consultaArboles));
        return $arrayArboles;
    }

    /**
     * Funcion que permite visualizar el index de control persona
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @return view 
     */
    public function actionIndexpersona()
    {
        $modelBusqueda = \app\models\FiltrosControl::find()->where([
            'usua_id' => ':identity',
            'guardar_filtro' => 1
        ])
        ->addParams([':identity' => Yii::$app->user->identity->id])
        ->one();
        $arrArboles = $arrArbolesUnica = [];
        $tipo_grafica = '';
        if (isset($modelBusqueda)) {
            $model = $modelBusqueda;
            $model->arbolDetallada = $model->ids_arboles;
            $model->fechaDetallada = $model->rango_fecha;
            $model->metricaDetallada = $model->ids_metricas;
            $model->dimensionDetallada = $model->ids_dimensiones;
            $model->corteDetallada = $model->corte_id;
            $model->rolDetallada = $model->ids_roles;
            $model->valoradorDetallada = $model->ids_valoradores;
            $model->equiposvaloradorDetallada = $model->ids_equipos_valoradores;
            $arrArboles = explode(',', $model->arbolDetallada);
        } else {
            $model = new \app\models\FiltrosControl();
        }
        $data = new \stdClass();
        $data->showGraf = false;
        $banderaValidacion = true;

        //VISTA Y CONTROLADOR PARA GUARDAR FILTROS
        $controlador = Yii::$app->controller->id;
        $vista = Yii::$app->controller->action->id;
        $filtros = new \stdClass();

        if (Yii::$app->request->post()) {
            //VARIABLA PARA DETERMINAR SI ES VISTA UNICA O DETALLADA
            $form = Yii::$app->request->post('form');
            $model->scenario = ($form == "0") ? 'filtroProceso' : 'filtroProcesoDetallado';
            $idsArboles = ($form == "0") ? ((Yii::$app->request->post('arbol_ids') !=
                null) ? Yii::$app->request->post('arbol_ids') : null) : ((Yii::$app->request->post('arbol_idsDetallada') !=
                null) ? Yii::$app->request->post('arbol_idsDetallada') : null);
            $model->arbol = ($idsArboles != null) ? implode(",", $idsArboles) : null;
            $datosFiltros = Yii::$app->request->post('FiltrosControl');
            if ($form == "0") {
                $model->rol = $datosFiltros["rol"];
                $model->valorador = $datosFiltros["valorador"];
                $model->equiposvalorador = $datosFiltros["equiposvalorador"];
            } else {
                $model->rol = $datosFiltros["rolDetallada"];
                $model->valorador = $datosFiltros["valoradorDetallada"];
                $model->equiposvalorador = $datosFiltros["equiposvaloradorDetallada"];
            }
            if (
                $model->rol == '' && $model->valorador == '' && $model->equiposvalorador ==
                ''
            ) {
                $banderaValidacion = false;
                $msg = \Yii::t('app', 'Verifique que al menos uno de los siguientes campos esta diligenciado: rol, valorador o equipos');
                Yii::$app->session->setFlash('danger', $msg);
            }
            if ($model->arbol == null) {
                $msg = \Yii::t('app', 'Seleccione un arbol');
                Yii::$app->session->setFlash('danger', $msg);
            } else {
                $arrArboles = explode(',', $model->arbol);
                $arrArbolesUnica = explode(',', $model->arbol);
            }
            $data->banderaError = ($form == "0") ? 'vistaunica' : 'vistadetallada';
        } else {
            //PREGUNTO SI EXISTE UN FILTRO
            $filtrosForm = \app\models\FiltrosFormularios::findOne(['vista' => $controlador . '/' . $vista, 'usua_id' => Yii::$app->user->identity->id]);
            $modelBusqueda = \app\models\FiltrosControl::find()->where([
                'usua_id' => Yii::$app->user->identity->id,
                'guardar_filtro' => 1
            ])->one();
            $model = new \app\models\FiltrosControl();
            if (!empty($filtrosForm)) {
                $dataFiltos = json_decode($filtrosForm->parametros);
                $model->fecha = $dataFiltos->fecha;
                $model->dimension = $dataFiltos->dimension;
                $model->metrica = $dataFiltos->metrica;
                $model->corte = $dataFiltos->corte;
                $model->tipo_grafica = $dataFiltos->tipo_grafica;
                $model->rol = $dataFiltos->rol;
                $model->valorador = $dataFiltos->valorador;
                $model->equiposvalorador = $dataFiltos->equiposvalorador;
                $arrArbolesUnica = explode(',', $dataFiltos->arbol_ids);
            } else {
                $model->tipo_grafica = '';
                $model->dimension = '';
                $model->fecha = '';
                $model->corte = '';
                $model->metrica = '';
                $model->rol = '';
                $model->valorador = '';
                $model->equiposvalorador = '';
            }
            if (isset($modelBusqueda)) {
                $model->arbolDetallada = $modelBusqueda->ids_arboles;
                $model->fechaDetallada = $modelBusqueda->rango_fecha;
                $model->metricaDetallada = $modelBusqueda->ids_metricas;
                $model->dimensionDetallada = $modelBusqueda->ids_dimensiones;
                $model->corteDetallada = $modelBusqueda->corte_id;
                $model->rolDetallada = $modelBusqueda->ids_roles;
                $model->valoradorDetallada = $modelBusqueda->ids_valoradores;
                $model->equiposvaloradorDetallada = $modelBusqueda->ids_equipos_valoradores;
                $model->guardar_filtro = $modelBusqueda->guardar_filtro;
            }
        }
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            /* Inicio construccion de graficas */
            if ($banderaValidacion) {
                if ($form == "0") {
                    /**
                     * TIPO DE GRAFICA
                     * agru_dimen: AGRUPANDO DIMENSIÓN
                     * sepa_dimen: SEPARANDO DIMENSIÓN
                     * tendencia: TENDENCIA EN PERIODOS DE CORTE
                     */
                    $tipo_grafica = $model->tipo_grafica;
                    /**
                     * FUNCION ENCARGADA DE GENERAR LOS DATOS PARA GRAFICAR
                     * DEPENDIENTE DEL TIPO DE GRAFICA SELECCIONADA
                     */
                    $data = $this->datosGrafica($model, $tipo_grafica, Yii::$app->request->post('agrupar'), $idsArboles);

                    if (!$data->showGraf) {
                        $msg = \Yii::t('app', 'No se encontraron datos para los filtros ingresados');
                        Yii::$app->session->setFlash('danger', $msg);
                    }

                    /* GUARDO FILTROS DE BÚSQUEDA */
                    if (isset($model->arbol) && $model->arbol != "") {
                        $filtros->fecha = $fecha = $model->fecha;
                        $filtros->dimension = $model->dimension;
                        $filtros->metrica = $model->metrica;
                        $filtros->corte = $model->corte;
                        $fecha = explode(' - ', $fecha);

                        //Guardar filtros --------------------------------------------------                    
                        $filtrosDatos = new \stdClass();
                        $filtrosDatos->fecha = $model->fecha;
                        $filtrosDatos->dimension = $model->dimension;
                        $filtrosDatos->metrica = $model->metrica;
                        $filtrosDatos->corte = $model->corte;
                        $filtrosDatos->arbol_ids = $model->arbol;
                        $filtrosDatos->tipo_grafica = $model->tipo_grafica;
                        $filtrosDatos->rol = $model->rol;
                        $filtrosDatos->valorador = $model->valorador;
                        $filtrosDatos->equiposvalorador = $model->equiposvalorador;
                        $arbIds = $filtrosDatos->arbol_ids;

                        $filtrosForm = \app\models\FiltrosFormularios::findOne(['vista' => $controlador . '/' . $vista, 'usua_id' => Yii::$app->user->identity->id]);

                        if (empty($filtrosForm)) {
                            $filtrosForm = new \app\models\FiltrosFormularios;
                        }
                        $filtrosForm->usua_id = Yii::$app->user->identity->id;
                        $filtrosForm->vista = $controlador . '/' . $vista;
                        $filtrosForm->parametros = json_encode($filtrosDatos);
                        $filtrosForm->save();
                    }
                } else {

                    $ejecucionformulario = new \app\models\Ejecucionformularios();
                    $model->ids_arboles = $model->arbol;
                    $model->rango_fecha = $model->fechaDetallada;
                    $model->ids_metricas = $model->metricaDetallada;
                    $model->ids_dimensiones = $model->dimensionDetallada;
                    $model->corte_id = $model->corteDetallada;
                    $model->usua_id = Yii::$app->user->identity->id;
                    $model->ids_roles = $model->rolDetallada;
                    $model->ids_valoradores = $model->valoradorDetallada;
                    $model->ids_equipos_valoradores = $model->equiposvaloradorDetallada;
                    $model->rol = $model->ids_roles;
                    $model->valorador = $model->ids_valoradores;
                    $model->equiposvalorador = $model->ids_equipos_valoradores;
                    if ($model->guardar_filtro == 1) {
                        $model->save();
                    } else {
                        if (isset($modelBusqueda)) {
                            $modelBusqueda->delete();
                        }
                    }
                    $metricas = \app\models\Metrica::find()->where("id IN(:model->ids_metricas)")
                    ->addParams([':model->ids_metricas'=>$model->ids_metricas])
                    ->asArray()->all();
                    $metrica = $this->validarMetrica($metricas[0]['id']);
                    $consulta = $ejecucionformulario->getDatabygrafexcelpersona($model->ids_arboles, $model->ids_dimensiones, $model->rango_fecha, $metrica, $model);
                    if (count($consulta) > 0) {
                        $data->datosTabla = $this->construirTabla($model->rango_fecha, Yii::$app->user->identity->id, $model->corte_id, $model->ids_metricas, $model->ids_dimensiones, $model->ids_arboles, $model, $form, "persona");
                        $tablaExcel = $this->actionGenerartablaexcel($data->datosTabla['datos'], $model->ids_dimensiones, $data->datosTabla['total'], "persona");
                        $this->generarExcel($tablaExcel, $data->datosTabla['cortes'], $model->ids_dimensiones, $model->ids_metricas, false, Yii::$app->user->identity->id, $model->corte_id, "persona", $model->ids_arboles);
                    } else {
                        $data->showGraf = false;
                        $msg = \Yii::t('app', 'No se encontraron datos para los filtros ingresados');
                        Yii::$app->session->setFlash('danger', $msg);
                    }
                }
            }
        }
        $data->arboles[] = $this->getRecursivearbolscopia('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', 'arbol_ids', $arrArbolesUnica);
        $data->arboles[] = $this->getRecursivearbolscopia('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', 'arbol_idsDetallada', $arrArboles);
        $data->metrica = ArrayHelper::map(
            \app\models\Metrica::find()->limit(10)->asArray()->all(),
            'id',
            'detexto'
        );
        $data->metrica[] = 'Score';
        $data->metrica[30] = 'Cantidad de Segundos Calificadores';
        return $this->render('indexPersona', ['data' => $data, 'model' => $model, 'tipo_grafica' => $tipo_grafica]);
    }

    /**
     * Obtiene el listado de equipos de valoradores (grupos)
     * @param type $search
     * @param type $id
     */
    public function actionEquiposlistvaloradores($search = null, $id = null)
    {
        if (!Yii::$app->getRequest()->isAjax) {
            return $this->goHome();
        }

        $out = ['more' => false];
        if (!is_null($search)) {
            $data = \app\models\EquiposvaloradoresSearch::find()
                ->select('tbl_equipos_evaluadores.id AS id,UPPER(tbl_equipos_evaluadores.name) AS text')
                ->where('tbl_equipos_evaluadores.name LIKE "%":search"%"')
                ->addParams([':search'=>$search])
                ->orderBy('tbl_equipos_evaluadores.name')
                ->asArray()
                ->all();
            $out['results'] = array_values($data);
        } elseif (!empty($id)) {
            $data = \app\models\EquiposvaloradoresSearch::find()
                ->select('tbl_equipos_evaluadores.id AS id,UPPER(tbl_equipos_evaluadores.name) AS text')
                ->where('tbl_equipos_evaluadores.id IN (:id)')
                ->addParams([':id'=>$id])
                ->asArray()
                ->all();
            $out['results'] = array_values($data);
        } else {
            $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
        }
        echo \yii\helpers\Json::encode($out);
    }

    /**
     * Metodo para crear los datos para graficar dependiente de los filtros
     * y el tipo de grafica
     * 
     * @param object $model
     * @param string $tipo_grafica
     * @return object
     * @author Felipe Echevereii <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    private function datosGrafica($model, $tipo_grafica, $agrupar, $idsArboles)
    {

        $data = new \stdClass();
        switch ($tipo_grafica) {
            case "agru_dimen":
                $data = $this->getDataGraphAgrupDimen($model, $agrupar, $idsArboles);
                break;
            case "sepa_dimen":
                $data = $this->getDataGraphSepaDimen($model, $agrupar, $idsArboles);
                break;
            case "tendencia":
                $data = $this->getDataGrapTendencia($model, $agrupar, $idsArboles);
                break;
            default:
                break;
        }

        return $data;
    }

    /**
     * Metodo para crear los datos para graficar dependiente de los filtros
     * y el tipo de grafica
     * 
     * @param object $model
     * @param string $tipo_grafica
     * @return object
     * @author Felipe Echevereii <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    private function getDataGraphAgrupDimen($model, $agrupar, $idsArboles)
    {

        //OBJETO DE RESPUESTA CON LOS DATOS DE LA GRAFICA
        $data = new \stdClass();
        $datos = [];
        $fechas = [];
        $form = 0;
        $data->showGraf = false;
        $data->banderaError = ($form == "0") ? 'vistaunica' : 'vistadetallada';
        $fechas[] = $model->fecha;
        $ejecucionformulario = new \app\models\Ejecucionformularios();
        $metrica = $this->validarMetrica($model->metrica);
        $arrayLabelsx = [
            'enabled' => true,
            'color' => ($agrupar == 1) ? '#000000' : '#FFFFFF',
            'align' => ($agrupar == 1) ? 'center' : 'right',
            'style' => [
                'fontSize' => ($agrupar == 1) ? '18px' : ((count($idsArboles) <
                    10) ? '15px' : '10px'),
                'fontFamily' => 'Verdana, sans-serif',
                'verticalAlign' => 'middle',
            ]
        ];
        $menorarboles = 100;
        $menordimensiones = 100;
        $volumenes = false;
        $segundoCalifPer = false;
        switch ($model->metrica) {
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
            case 18:
            case 19:
            case 20:
            case 21:
            case 22:
                $volumenes = true;
                break;
            case 30:
                $segundoCalifPer = true;
                break;
            default:
                echo "no se cumple";
        }
        if ($agrupar == 1) {
            $arrayGrupos = $this->construirArbolgrupos($model->arbol);
            foreach ($arrayGrupos as $key => $grupo) {

                $consulta = $ejecucionformulario->getDatabygrafgrupo(implode(',', $grupo['hijos']), $model->dimension, $model->fecha, $metrica, $model, $volumenes, $segundoCalifPer);
                if (count($consulta) > 0) {
                    $arbolPadre = \app\models\Arboles::findOne($key);
                    $promedio = ($consulta[0]['promedio'] * 100);
                    $menorarboles = ($promedio < $menorarboles) ? $promedio : $menorarboles;
                    $datos['arbol'][] = ['name' => $arbolPadre->name, 'data' => [(float) round($promedio, 2)]];
                    $datos['count'][] = ['name' => $arbolPadre->name, 'data' => [(int) $consulta[0]['total']]];
                }
            }
        } else {
            $consulta = $ejecucionformulario->getDatabygraf($model->arbol, $model->dimension, $model->fecha, $metrica, true, $model, "proceso", $volumenes, $segundoCalifPer);
            if (count($consulta) > 0) {
                foreach ($consulta as $value) {
                    $arbolPadre = \app\models\Arboles::findOne($value['arbol_id']);
                    $promedio = ($value['promedio'] * 100);
                    $menorarboles = ($promedio < $menorarboles) ? $promedio : $menorarboles;
                    $datos['arbol'][] = ['name' => $arbolPadre->name, 'data' => [(float) round($promedio, 2)]];
                    $datos['count'][] = ['name' => $arbolPadre->name, 'data' => [(int) $value['total']]];
                }
            }
        }

        if (count($consulta) > 0) {
            //TEXTO VERTICAL SOLO SI ES MAYOR A 5
            if (count($consulta) > 8) {
                $arrayLabelsx['rotation'] = -90;
            }
            if ($consulta[0]['total'] != 0) {
                $arrayTemp = [];
                foreach ($datos['arbol'] as $valueArbol) {
                    $arrayTemp[] = [$valueArbol['name'], $valueArbol['data'][0]];
                }
                $datos['arbol2'][] = [
                    'name' => 'Total', 'colorByPoint' => true,
                    'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx
                ];
                $arrayTemp = [];
                foreach ($datos['count'] as $valueContador) {
                    $arrayTemp[] = [$valueContador['name'], $valueContador['data'][0]];
                }
                $datos['count2'][] = [
                    'name' => 'Total', 'colorByPoint' => true,
                    'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx
                ];
                $consulta1 = $ejecucionformulario->getDatabygraf($model->arbol, $model->dimension, $model->fecha, $metrica, false, $model, "proceso");
                foreach ($consulta1 as $value) {
                    $arbolPadre = \app\models\Dimensiones::findOne($value['dimension_id']);
                    $promedio = ($value['promedio'] * 100);
                    $menordimensiones = ($promedio < $menordimensiones) ? $promedio : $menordimensiones;
                    $datos['dimension'][] = [
                        'name' => $arbolPadre->name,
                        'data' => [(float) round($promedio, 2)]
                    ];
                }
                $arrayTemp = [];
                foreach ($datos['dimension'] as $valueDimension) {
                    $arrayTemp[] = [$valueDimension['name'], $valueDimension['data'][0]];
                }
                $datos['dimension2'][] = [
                    'name' => 'Total', 'colorByPoint' => true,
                    'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx
                ];
                $data->showGraf = true;
                $data->infoArbol['datos'] = (isset($datos['arbol2'])) ? $datos['arbol2'] : '';
                $data->countCantidaArboles = count($datos['arbol']);
                $data->infoArbol['categoria'] = $fechas;
                $data->infoDimension['datos'] = (isset($datos['dimension2'])) ? $datos['dimension2'] : '';
                $data->infoDimension['categoria'] = $fechas;
                $data->infoArbolTotal['datos'] = (isset($datos['count2'])) ? $datos['count2'] : '';
                $data->menorArbol = $menorarboles - 5;
                $data->menorDimension = $menordimensiones - 5;
                /* Fin construccion de graficas */
                $data->datosTabla = $this->construirTabla($model->fecha, Yii::$app->user->identity->id, $model->corte, $metrica, $model->dimension, $model->arbol, $model, $form, "proceso");
                $tempDimension = explode(',', $model->dimension);
                $data->totalDimension = count($tempDimension);
                $data->metricaSelecc = $metrica;
                //DATO PARA SABER SI ES VOLUMNE Y SOLO MOSTRAR GRAF CANTIDAD
                $data->volumenes = $volumenes;
                //DATO PARA SABER SI ES SEGUNDOCALIFICADRO Y SOLO MOSTRAR GRAF CANTIDAD
                $data->segundoCalifPer = $segundoCalifPer;
            } else {
                $data->showGraf = false;
                $msg = \Yii::t('app', 'No se encontraron datos para los filtros ingresados');
                Yii::$app->session->setFlash('danger', $msg);
            }
        } else {

            $data->showGraf = false;
            $msg = \Yii::t('app', 'No se encontraron datos para los filtros ingresados');
            Yii::$app->session->setFlash('danger', $msg);
        }

        return $data;
    }

    /**
     * Calcula la tendencia en el control del proceso
     * 
     * @param object $model      Modelo
     * @param int    $agrupar    Define si se agrupan los arboles     
     * @param array  $idsArboles Ids de arboles
     * 
     * @return \stdClass
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2016 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    private function getDataGrapTendencia($model, $agrupar, $idsArboles)
    {
        echo "hola";
        die;

        $cortes = [];
        $tipoCorte = $model->corte;
        $rangofecha = $model->fecha;
        $fecha = explode(' - ', $rangofecha);
        $fechaInicio = $fecha[0];
        $fechaFin = $fecha[1];
        $metrica = $model->metrica;
        $dimension = $model->dimension;
        $id_usuario = Yii::$app->user->identity->id;
        $data = new \stdClass();
        $showGraf = false;
        $ejeX = $ejeXtabla = [];
        $resultProm = [];
        $resultCant = [];
        //Traemos los tiempos  -------------------------------------------------
        switch ($tipoCorte) {
            case 1:
                $cortes = $this->calcularCortesSemana($rangofecha, $id_usuario, $tipoCorte);
                break;
            case 2:
                $cortes = $this->calcularCortesMes($rangofecha, $id_usuario, $tipoCorte);
                break;
            default:
                $fechas = explode(' - ', $rangofecha);
                $fechas[0] = $fechas[0] . ' 00:00:00';
                $fechas[1] = $fechas[1] . ' 23:59:59';
                $fechaI = strtotime($fechas[0]);
                $fechaF = strtotime($fechas[1]);
                for ($i = $fechaI; $i <= $fechaF; $i += 86400) {
                    $cortes[] = ['fechaI' => date('Y-m-d', $i)
                        . ' 00:00:00', 'fechaF' => date('Y-m-d', $i)
                        . ' 23:59:59'];
                }
                break;
        }
        //Obtenemos el tipo de metrica -----------------------------------------
        //PARA SABER SI SE SOLICITARON MÉTRICAS DE VOLUMEN
        $volumenes = false;
        $segundoCalifPer = false;
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
                $colMetrica = 'i8_nmcalculo';
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
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
            case 18:
            case 19:
            case 20:
            case 21:
            case 22:
                $volumenes = true;
                $colMetrica = 'basesatisfaccion_id';
                break;
            case 23:
            case 24:
            case 25:
            case 30:
                $segundoCalifPer = true;
                $colMetrica = 'score';
                break;
            default:
                $colMetrica = '';
                break;
        }
        //Guardamos en la variable para el eje X -------------------------------        
        if (count($cortes) > 0) {
            foreach ($cortes as $key => $value) {
                $ejeX[] = 'C' . ($key + 1);
                $ejeXtabla[] = Html::tag('span', "C" . ($key + 1), [
                    'data-title' => str_replace(['00:00:00'], '', $value['fechaI']) . " - " . str_replace(['23:59:59'], '', $value['fechaF']),
                    'data-toggle' => 'tooltip',
                    'style' => 'text-decoration: underline;cursor:pointer;'
                ]);
                $iniTempY[] = null;
            }
        }

        //Traemos datos para eje Y ---------------------------------------------
        if ($agrupar == 1) {
            //Agrupacion de arboles --------------------------------------------
            //DATOS DE ROL
            $selectPersonas = $wherePersonas = $joinPersonas = "";
            if ($model->valorador != '') {
                if ($segundoCalifPer) {
                    $wherePersonas .= " AND sc.id_responsable IN (" . $model->valorador . ") ";
                } else {
                    $wherePersonas .= " AND e.usua_id IN (" . $model->valorador . ") ";
                }
            }
            if ($model->rol != '') {
                $joinPersonas .= ' INNER JOIN rel_usuarios_roles rr on rr.rel_usua_id = e.usua_id ';
                $selectPersonas .= ',rr.*';
                $wherePersonas .= " AND rr.rel_role_id IN (" . $model->rol . ") ";
            }
            if ($model->equiposvalorador != '') {
                $modelequipoValoradores = \app\models\RelEquiposEvaluadores::find()->where('equipo_id IN (' . $model->equiposvalorador . ')')->asArray()->all();
                $arrayIdsusuarios = [];
                foreach ($modelequipoValoradores as $key => $value) {
                    $arrayIdsusuarios[] = $value['evaluadores_id'];
                }
                $idsUsuarios = implode(',', $arrayIdsusuarios);
                if ($segundoCalifPer) {
                    $wherePersonas .= " AND sc.id_responsable IN (" . $idsUsuarios . ") ";
                } else {
                    $wherePersonas .= " AND e.usua_id IN (" . $idsUsuarios . ") ";
                }
            }
            //DATOS DE VOLUMENES
            $joinVolumen = $whereVolumen = "";
            if ($volumenes) {
                $joinVolumen = "INNER JOIN tbl_base_satisfaccion satu ON satu.id = e.basesatisfaccion_id";
            }
            //WHERE VOLUMEN
            switch ($metrica) {
                case 13:
                    $whereVolumen = "AND satu.tipologia = 'FELICITACION'";
                    break;
                case 14:
                    $whereVolumen = "AND satu.tipologia = 'FELICITACIÓN CON BUZÓN'";
                    break;
                case 15:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA'";
                    break;
                case 16:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA POR BUZÓN'";
                    break;
                case 17:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA PENALIZABLE'";
                    break;
                case 19:
                    $whereVolumen = "AND satu.responsabilidad = 'MARCA'";
                    break;
                case 20:
                    $whereVolumen = "AND satu.responsabilidad = 'CANAL'";
                    break;
                case 21:
                    $whereVolumen = "AND satu.responsabilidad = 'COMPARTIDA'";
                    break;
                case 22:
                    $whereVolumen = "AND satu.responsabilidad = 'EQUIVOCACION'";
                    break;
                default:
                    break;
            }
            $sql = "SELECT 
            DATE_FORMAT(e.created, '%Y-%m-%d %H:%i:%s') as fecha, 
            SUM(e.$colMetrica) AS suma,                    
            COUNT($colMetrica) AS cantidad,                    
            e.arbol_id,
            a.name as arbol,
            e.dimension_id,                   
            d.name as dimension
            " . $selectPersonas . " 
            FROM tbl_ejecucionformularios e
            JOIN tbl_arbols a ON a.id = e.arbol_id
            JOIN tbl_dimensions d ON d.id = e.dimension_id
            " . $joinVolumen . "
            " . $joinPersonas . "
            WHERE e.dimension_id IN(:dimension) 
                AND (e.created >= ':fechaInicio 00:00:00' 
                AND e.created <= ':fechaFin 23:59:59')
                AND e.arbol_id IN(:marbol)
                " . $wherePersonas . "
                " . $whereVolumen . "
            GROUP BY DATE_FORMAT(e.created, '%Y%m%d'), e.dimension_id 
            ORDER BY e.dimension_id, e.created";

            if ($segundoCalifPer) {
                $sql = "SELECT 
                            DATE_FORMAT(sc.s_fecha, '%Y-%m-%d %H:%i:%s') as fecha, 
                            SUM(e.$colMetrica) AS suma,                    
                            COUNT(e.$colMetrica) AS cantidad,                    
                            e.arbol_id,
                            a.name as arbol,
                            e.dimension_id,                   
                            d.name as dimension
                            " . $selectPersonas . "
                            FROM `tbl_segundo_calificador` sc
                            JOIN `tbl_ejecucionformularios` e ON e.`id` = sc.`id_ejecucion_formulario`
                            JOIN tbl_arbols a ON a.id = e.arbol_id
                            JOIN tbl_dimensions d ON d.id = e.dimension_id
                            " . $joinPersonas . "
                            WHERE e.dimension_id IN(:dimension) 
                                AND (sc.s_fecha >= ':fechaInicio 00:00:00' 
                                AND sc.s_fecha <= ':fechaFin 23:59:59')
                                AND e.arbol_id IN(:marbol)
                                " . $wherePersonas . "     
                                " . $whereVolumen . "     
                            GROUP BY DATE_FORMAT(sc.s_fecha, '%Y%m%d'), e.dimension_id 
                            ORDER BY e.dimension_id, sc.s_fecha";
            }

            $resultData = \Yii::$app->db->createCommand($sql)
            ->bindValue(':dimension', $dimension)
            ->bindValue(':fechaInicio', $fechaInicio)
            ->bindValue(':fechaFin', $fechaFin)
            ->bindValue(':marbol', $model->arbol)
            ->queryAll();

            if (count($resultData) > 0) {
                $sumaTemp = $cantTemp = $restSuma = $iniTempY;
                foreach ($resultData as $keyData => $value) {
                    $idDimension = $value['dimension_id'];

                    foreach ($cortes as $keyRango => $rango) {
                        if ((strtotime($rango['fechaI']) <= strtotime($value['fecha'])) && (strtotime($rango['fechaF']) >= strtotime($value['fecha']))) {
                            $sumaTemp[$keyRango] = $sumaTemp[$keyRango] + $value['suma'];
                            $cantTemp[$keyRango] = (int) $cantTemp[$keyRango] + (int) $value['cantidad'];
                        }
                    }

                    if ((count($resultData) - 1) == $keyData) {
                        foreach ($sumaTemp as $keySum => $valueSum) {
                            if (!is_null($valueSum) && $cantTemp[$keySum] > 0) {
                                $restSuma[$keySum] = round(($valueSum / $cantTemp[$keySum]) * 100, 2);
                            }
                        }
                        $resultProm[] = ['name' => $value['dimension'], 'data' => $restSuma];
                        $resultCant[] = ['name' => $value['dimension'], 'data' => $cantTemp];
                        $sumaTemp = $cantTemp = $restSuma = $iniTempY;
                    } else {
                        $nextDimen = $resultData[$keyData + 1]['dimension_id'];
                        if ($nextDimen != $idDimension) {
                            foreach ($sumaTemp as $keySum => $valueSum) {
                                if (!is_null($valueSum) && $cantTemp[$keySum] > 0) {
                                    $restSuma[$keySum] = round(($valueSum / $cantTemp[$keySum]) * 100, 2);
                                }
                            }
                            $resultProm[] = ['name' => $value['dimension'], 'data' => $restSuma];
                            $resultCant[] = ['name' => $value['dimension'], 'data' => $cantTemp];
                            $sumaTemp = $cantTemp = $restSuma = $iniTempY;
                        }
                    }
                }
            }
        } else {

            //DATOS DE ROL
            $selectPersonas = $wherePersonas = $joinPersonas = "";
            if ($model->valorador != '') {
                if ($segundoCalifPer) {
                    $wherePersonas .= " AND sc.id_responsable IN (" . $model->valorador . ") ";
                } else {
                    $wherePersonas .= " AND e.usua_id IN (" . $model->valorador . ") ";
                }
            }
            if ($model->rol != '') {
                $joinPersonas .= ' INNER JOIN rel_usuarios_roles rr on rr.rel_usua_id = e.usua_id ';
                $selectPersonas .= ',rr.*';
                $wherePersonas .= " AND rr.rel_role_id IN (" . $model->rol . ") ";
            }
            if ($model->equiposvalorador != '') {
                $modelequipoValoradores = \app\models\RelEquiposEvaluadores::find()->where('equipo_id IN (' . $model->equiposvalorador . ')')->asArray()->all();
                $arrayIdsusuarios = [];
                foreach ($modelequipoValoradores as $key => $value) {
                    $arrayIdsusuarios[] = $value['evaluadores_id'];
                }
                $idsUsuarios = implode(',', $arrayIdsusuarios);
                if ($segundoCalifPer) {
                    $wherePersonas .= " AND sc.id_responsable IN (" . $idsUsuarios . ") ";
                } else {
                    $wherePersonas .= " AND e.usua_id IN (" . $idsUsuarios . ") ";
                }
            }

            //DATOS DE VOLUMENES
            $joinVolumen = $whereVolumen = "";
            if ($volumenes) {
                $joinVolumen = "INNER JOIN tbl_base_satisfaccion satu ON satu.id = e.basesatisfaccion_id";
            }
            //WHERE VOLUMEN
            switch ($metrica) {
                case 13:
                    $whereVolumen = "AND satu.tipologia = 'FELICITACION'";
                    break;
                case 14:
                    $whereVolumen = "AND satu.tipologia = 'FELICITACIÓN CON BUZÓN'";
                    break;
                case 15:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA'";
                    break;
                case 16:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA POR BUZÓN'";
                    break;
                case 17:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA PENALIZABLE'";
                    break;
                case 19:
                    $whereVolumen = "AND satu.responsabilidad = 'MARCA'";
                    break;
                case 20:
                    $whereVolumen = "AND satu.responsabilidad = 'CANAL'";
                    break;
                case 21:
                    $whereVolumen = "AND satu.responsabilidad = 'COMPARTIDA'";
                    break;
                case 22:
                    $whereVolumen = "AND satu.responsabilidad = 'EQUIVOCACION'";
                    break;
                default:
                    break;
            }

            $sql = "SELECT 
            DATE_FORMAT(e.created, '%Y-%m-%d %H:%i:%s') as fecha, 
            SUM(e.$colMetrica) AS suma,                    
            COUNT($colMetrica) AS cantidad,                    
            e.arbol_id,
            a.name as arbol,
            e.dimension_id,                   
            d.name as dimension
            " . $selectPersonas . " 
            FROM tbl_ejecucionformularios e
            JOIN tbl_arbols a ON a.id = e.arbol_id
            JOIN tbl_dimensions d ON d.id = e.dimension_id
            " . $joinVolumen . "
            " . $joinPersonas . "
            WHERE e.dimension_id IN(:dimension) 
                AND (e.created >= ':fechaInicio 00:00:00' 
                AND e.created <= ':fechaFin 23:59:59')
                AND e.arbol_id IN(:marbol)
                " . $wherePersonas . "
                " . $whereVolumen . "
            GROUP BY DATE_FORMAT(e.created, '%Y%m%d'), e.dimension_id 
            ORDER BY e.dimension_id, e.created";


            //QUERY PARA MÉTRICA DE SEGUNDO CALIFIADOR
            if ($segundoCalifPer) {
                $sql = "SELECT 
                            DATE_FORMAT(sc.s_fecha, '%Y-%m-%d %H:%i:%s') as fecha, 
                            SUM(e.$colMetrica) AS suma,                    
                            COUNT(e.$colMetrica) AS cantidad,                    
                            e.arbol_id,
                            a.name as arbol,
                            e.dimension_id,                   
                            d.name as dimension
                            " . $selectPersonas . "
                            FROM `tbl_segundo_calificador` sc
                            JOIN `tbl_ejecucionformularios` e ON e.`id` = sc.`id_ejecucion_formulario`
                            JOIN tbl_arbols a ON a.id = e.arbol_id
                            JOIN tbl_dimensions d ON d.id = e.dimension_id
                            " . $joinPersonas . "
                            WHERE e.dimension_id IN(:dimension) 
                                AND (sc.s_fecha >= ':fechaInicio 00:00:00' 
                                AND sc.s_fecha <= ':fechaFin 23:59:59')
                                AND e.arbol_id IN(:marbol)
                                " . $wherePersonas . "     
                                " . $whereVolumen . "     
                            GROUP BY DATE_FORMAT(sc.s_fecha, '%Y%m%d'), e.arbol_id, e.dimension_id 
                            ORDER BY e.arbol_id,  e.dimension_id, sc.s_fecha";
            }
            $resultData = \Yii::$app->db->createCommand($sql)
            ->bindValue(':dimension', $dimension)
            ->bindValue(':fechaInicio', $fechaInicio)
            ->bindValue(':fechaFin', $fechaFin)
            ->bindValue(':marbol', $model->arbol)
            ->queryAll();
            if (count($resultData) > 0) {
                $sumaTemp = $cantTemp = $restSuma = $iniTempY;
                foreach ($resultData as $keyData => $value) {
                    $idArbol = $value['arbol_id'];
                    $idDimension = $value['dimension_id'];

                    foreach ($cortes as $keyRango => $rango) {
                        if ((strtotime($rango['fechaI']) <= strtotime($value['fecha'])) && (strtotime($rango['fechaF']) >= strtotime($value['fecha']))) {
                            $sumaTemp[$keyRango] = $sumaTemp[$keyRango] + $value['suma'];
                            $cantTemp[$keyRango] = (int) $cantTemp[$keyRango] + (int) $value['cantidad'];
                        }
                    }

                    if ((count($resultData) - 1) == $keyData) {
                        foreach ($sumaTemp as $keySum => $valueSum) {
                            if (!is_null($valueSum) && $cantTemp[$keySum] > 0) {
                                $restSuma[$keySum] = round(($valueSum / $cantTemp[$keySum]) * 100, 2);
                            }
                        }
                        $resultProm[] = ['name' => $value['arbol'] . ' - '
                            . $value['dimension'], 'data' => $restSuma];
                        $resultCant[] = ['name' => $value['arbol'] . ' - '
                            . $value['dimension'], 'data' => $cantTemp];
                        $sumaTemp = $cantTemp = $restSuma = $iniTempY;
                    } else {
                        $nextArbol = $resultData[$keyData + 1]['arbol_id'];
                        $nextDimen = $resultData[$keyData + 1]['dimension_id'];
                        if ($nextArbol != $idArbol || $nextDimen != $idDimension) {
                            foreach ($sumaTemp as $keySum => $valueSum) {
                                if (!is_null($valueSum) && $cantTemp[$keySum] > 0) {
                                    $restSuma[$keySum] = round(($valueSum / $cantTemp[$keySum]) * 100, 2);
                                }
                            }

                            $resultProm[] = ['name' => $value['arbol'] . ' - '
                                . $value['dimension'], 'data' => $restSuma];
                            $resultCant[] = ['name' => $value['arbol'] . ' - '
                                . $value['dimension'], 'data' => $cantTemp];
                            $sumaTemp = $cantTemp = $restSuma = $iniTempY;
                        }
                    }
                }
            }
        }

        if (count($resultProm) > 0) {
            $showGraf = true;
        }
        //Datos de respuesta ---------------------------------------------------
        $data->dataX = $ejeX;
        $data->dataXtabla = $ejeXtabla;
        $data->dataY = $resultProm;
        $data->dataYCont = $resultCant;
        $data->showGraf = $showGraf;
        $data->countCantidaArboles = count($model->arbol);
        $data->metricaSelecc = $this->validarMetrica($metrica);
        //DATO PARA SABER SI ES VOLUMNE Y SOLO MOSTRAR GRAF CANTIDAD
        $data->volumenes = $volumenes;
        //DATO PARA SABER SI ES SEGUNDOCALIFICADRO Y SOLO MOSTRAR GRAF CANTIDAD
        $data->segundoCalifPer = $segundoCalifPer;
        return $data;
    }

    /**
     * Calcula la tendencia en el control del proceso
     * 
     * @param object $model      Modelo
     * @param int    $agrupar    Define si se agrupan los arboles     
     * @param array  $idsArboles Ids de arboles
     * 
     * @return \stdClass
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2016 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    private function getDataGraphSepaDimen($model, $agrupar, $idsArboles)
    {

        $rangofecha = $model->fecha;
        $fecha = explode(' - ', $rangofecha);
        $fechaInicio = $fecha[0];
        $fechaFin = $fecha[1];
        $metrica = $model->metrica;
        $dimension = $model->dimension;
        $data = new \stdClass();
        $showGraf = false;


        //ELIMINO LOS ARBOLES QUE NO SEAN PAPA
        foreach ($idsArboles as $keyArbol => $arbol) {
            $count = \app\models\Arboles::findOne($arbol);
            if ($count->snhoja == 0) {
                unset($idsArboles[$keyArbol]);
            } else {
                $arbolesEjeX[] = $count->name;
            }
        }
        //ARRAY CON DATOS DE LA TABLA: LLAVE EL ID ARBOL Y LOS VALORES LAS N
        //DIMENSIONES X CANTIDAD Y X METRICA
        $arrTabla = $arrNuevaGraficaProm = $arrNuevaGraficaCant = [];
        $arrTmoDimen = [
            'nombre' => ""
        ];
        $TotDimen = explode(",", $dimension);
        asort($TotDimen);
        foreach ($TotDimen as $value) {
            $nmDimension = \app\models\Dimensiones::find()
                ->select(['name'])
                ->where(['id' => $value])
                ->one();
            $titutabla['promedio'][] = 'Resultado dimensión ' . $nmDimension->name;
            $titutabla['cantidad'][] = 'Cantidad dimensión ' . $nmDimension->name;
            $arrTmoDimen[(int) $value] = " - ";

            $arrNuevaGraficaProm[(int) $value] = [
                'name' => $nmDimension->name,
                'data' => array_fill_keys($idsArboles, null)
            ];
        }
        foreach ($idsArboles as $value) {
            $arrTabla[$value] = $arrTmoDimen;
            if ($agrupar == 1) {
                break;
            }
        }
        $arrNuevaGraficaCant = $arrNuevaGraficaProm;

        //Obtenemos el tipo de metrica -----------------------------------------
        //PARA SABER SI SE SOLICITARON MÉTRICAS DE VOLUMEN
        $volumenes = false;
        $segundoCalifPer = false;
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
                $colMetrica = 'i8_nmcalculo';
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
            case 12:
            case 13:
            case 14:
            case 15:
            case 16:
            case 17:
            case 18:
            case 19:
            case 20:
            case 21:
            case 22:
                $volumenes = true;
                $colMetrica = 'basesatisfaccion_id';
                break;
            case 23:
            case 24:
            case 25:
            case 30:
                $segundoCalifPer = true;
                $colMetrica = 'score';
                break;
            default:
                $colMetrica = '';
                break;
        }

        //Traemos datos para eje Y ---------------------------------------------        
        if ($agrupar == 1) {
            //Agrupacion de arboles --------------------------------------------
            //DATOS DE ROL
            $selectPersonas = $wherePersonas = $joinPersonas = "";
            if ($model->valorador != '') {
                if ($segundoCalifPer) {
                    $wherePersonas .= " AND sc.id_responsable IN (" . $model->valorador . ") ";
                } else {
                    $wherePersonas .= " AND e.usua_id IN (" . $model->valorador . ") ";
                }
            }
            if ($model->rol != '') {
                $joinPersonas .= ' INNER JOIN rel_usuarios_roles rr on rr.rel_usua_id = e.usua_id ';
                $selectPersonas .= ',rr.*';
                $wherePersonas .= " AND rr.rel_role_id IN (" . $model->rol . ") ";
            }
            if ($model->equiposvalorador != '') {
                $modelequipoValoradores = \app\models\RelEquiposEvaluadores::find()->where('equipo_id IN (' . $model->equiposvalorador . ')')->asArray()->all();
                $arrayIdsusuarios = [];
                foreach ($modelequipoValoradores as $key => $value) {
                    $arrayIdsusuarios[] = $value['evaluadores_id'];
                }
                $idsUsuarios = implode(',', $arrayIdsusuarios);
                if ($segundoCalifPer) {
                    $wherePersonas .= " AND sc.id_responsable IN (" . $idsUsuarios . ") ";
                } else {
                    $wherePersonas .= " AND e.usua_id IN (" . $idsUsuarios . ") ";
                }
            }
            //DATOS DE VOLUMENES
            $joinVolumen = $whereVolumen = "";
            if ($volumenes) {
                $joinVolumen = "INNER JOIN tbl_base_satisfaccion satu ON satu.id = e.basesatisfaccion_id";
            } else {
                #code
            }
            //WHERE VOLUMEN
            switch ($metrica) {
                case 13:
                    $whereVolumen = "AND satu.tipologia = 'FELICITACION'";
                    break;
                case 14:
                    $whereVolumen = "AND satu.tipologia = 'FELICITACIÓN CON BUZÓN'";
                    break;
                case 15:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA'";
                    break;
                case 16:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA POR BUZÓN'";
                    break;
                case 17:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA PENALIZABLE'";
                    break;
                case 19:
                    $whereVolumen = "AND satu.responsabilidad = 'MARCA'";
                    break;
                case 20:
                    $whereVolumen = "AND satu.responsabilidad = 'CANAL'";
                    break;
                case 21:
                    $whereVolumen = "AND satu.responsabilidad = 'COMPARTIDA'";
                    break;
                case 22:
                    $whereVolumen = "AND satu.responsabilidad = 'EQUIVOCACION'";
                    break;
                default:
                    break;
            }
            $sql = "SELECT 
            DATE_FORMAT(e.created, '%Y-%m-%d %H:%i:%s') as fecha, 
            SUM(e.$colMetrica) AS suma,                    
            COUNT($colMetrica) AS cantidad,                    
            e.arbol_id,
            a.name as arbol,
            e.dimension_id,                   
            d.name as dimension
            " . $selectPersonas . "
            FROM tbl_ejecucionformularios e
            JOIN tbl_arbols a ON a.id = e.arbol_id
            JOIN tbl_dimensions d ON d.id = e.dimension_id
            " . $joinVolumen . "
            " . $joinPersonas . "
            WHERE e.dimension_id IN(:dimension) 
                AND (e.created >= ':fechaInicio 00:00:00' 
                AND e.created <= ':fechaFin 23:59:59')
                AND e.arbol_id IN(:marbol)
                " . $wherePersonas . "
                " . $whereVolumen . "     
            GROUP BY e.dimension_id 
            ORDER BY e.dimension_id, e.created";

            //QUERY PARA MÉTRICA DE SEGUNDO CALIFIADOR
            if ($segundoCalifPer) {
                $sql = "SELECT 
                            DATE_FORMAT(sc.s_fecha, '%Y-%m-%d %H:%i:%s') as fecha, 
                            SUM(e.$colMetrica) AS suma,                    
                            COUNT(e.$colMetrica) AS cantidad,                    
                            e.arbol_id,
                            a.name as arbol,
                            e.dimension_id,                   
                            d.name as dimension
                            " . $selectPersonas . "
                            FROM `tbl_segundo_calificador` sc
                            JOIN `tbl_ejecucionformularios` e ON e.`id` = sc.`id_ejecucion_formulario`
                            JOIN tbl_arbols a ON a.id = e.arbol_id
                            JOIN tbl_dimensions d ON d.id = e.dimension_id
                            " . $joinPersonas . "
                            WHERE e.dimension_id IN(:dimension) 
                                AND (sc.s_fecha >= ':fechaInicio 00:00:00' 
                                AND sc.s_fecha <= ':fechaFin 23:59:59')
                                AND e.arbol_id IN(:marbol)
                                " . $wherePersonas . "     
                                " . $whereVolumen . "     
                            GROUP BY e.dimension_id 
                            ORDER BY e.dimension_id, e.created";
            }

            $resultData = \Yii::$app->db->createCommand($sql)
            ->bindValue(':dimension', $dimension)
            ->bindValue(':fechaInicio', $fechaInicio)
            ->bindValue(':fechaFin', $fechaFin)
            ->bindValue(':marbol', $model->arbol)
            ->queryAll();
            $datos = [];
            if (count($resultData) > 0) {


                //DATOS DE LA GRAFICA 
                $arrayLabelsx = [
                    'enabled' => true,
                    'rotation' => ($agrupar == 1) ? 0 : -90,
                    'color' => ($agrupar == 1) ? '#000000' : '#FFFFFF',
                    'align' => ($agrupar == 1) ? 'center' : 'right',
                    'style' => [
                        'fontSize' => ($agrupar == 1) ? '18px' : ((count($idsArboles) <
                            10) ? '15px' : '10px'),
                        'fontFamily' => 'Verdana, sans-serif'
                    ]
                ];

                //INICIO NUEVO FOREACH                
                $arrayTempCant = $arrayTempProm = $arrNuevaGraficaCant = $arrNuevaGraficaProm = $arbolesEjeX = [];
                $promTemp = $cantTemp = $arrTabla;
                foreach ($resultData as $keyTabla => $value) {

                    //PROMEDIO
                    if (!is_null($value['cantidad']) && $value['cantidad'] > 0) {
                        $promedio = round(($value['suma'] / $value['cantidad']) * 100, 2);
                    } else {
                        $promedio = 0;
                    }
                    //GRAFICA NUEVA CANTIDAD
                    $arbolesEjeX[] = "AGRUPACIÓN DE ARBOLES";
                    if (!array_key_exists($value['dimension_id'], $arrNuevaGraficaCant)) {
                        $cantTemp[$value['arbol_id']]['nombre'] = "AGRUPACIÓN DE ARBOLES";
                        $arrNuevaGraficaCant[$value['dimension_id']]['name'] = $value['dimension'];
                        $arrNuevaGraficaCant[$value['dimension_id']]['data'][] = (int) $value['cantidad'];
                    } else {
                        $cantTemp[$value['arbol_id']]['nombre'] = "AGRUPACIÓN DE ARBOLES";
                        array_push($arrNuevaGraficaCant[$value['dimension_id']]['data'], (int) $value['cantidad']);
                    }
                    $arrayTempCant[] = [$value['arbol'] . " - " . $value['dimension'], (int) $value['cantidad']];
                    //FIN GRAFICA NUEVA CANTIDAD
                    //GRAFICA NUEVA PROMEDIO                                   
                    $arrayTempProm[] = [$value['arbol'] . " - " . $value['dimension'], (float) $promedio];
                    if (!array_key_exists($value['dimension_id'], $arrNuevaGraficaProm)) {
                        $promTemp[$value['arbol_id']]['nombre'] = "AGRUPACIÓN DE ARBOLES";
                        $arrNuevaGraficaProm[$value['dimension_id']]['name'] = $value['dimension'];
                        $arrNuevaGraficaProm[$value['dimension_id']]['data'][] = (float) $promedio;
                    } else {
                        $promTemp[$value['arbol_id']]['nombre'] = "AGRUPACIÓN DE ARBOLES";
                        array_push($arrNuevaGraficaProm[$value['dimension_id']]['data'], (float) $promedio);
                    }
                    //FIN GRAFICA NUEVA PROMEDIO
                    //DATOS TEMPRALES PARA TABLA SEPARADOS POR PROMEDIO Y CANT
                    $promTemp[$value['arbol_id']][$value['dimension_id']] = (float) $promedio . "%";
                    $cantTemp[$value['arbol_id']][$value['dimension_id']] = (int) $value['cantidad'];
                    $titutabla['promedio'][] = "Resultado dimensión " . $value['dimension'];
                    $titutabla['cantidad'][] = "Cantidad dimensión " . $value['dimension'];
                }

                //ELIMINO ENCABEZADOS DE TABLA REPEDITOS Y JUNTO AMBAS
                $arr_union_titu = array_merge($titutabla['promedio'], $titutabla['cantidad']);
                $titutabla = array_unique($arr_union_titu);

                //JUNTO LOS ARRAY TEMPORALES EN LA TABLA DEFINITIVA                
                foreach ($promTemp as $arbol => $datos) {
                    if ($promTemp[$arbol]['nombre'] == '' && $cantTemp[$arbol]['nombre'] == '') {
                        unset($arrTabla[$arbol]);
                        continue;
                    }
                    $arr_union = array_merge($promTemp[$arbol], $cantTemp[$arbol]);
                    $arrTabla[$arbol] = $arr_union;
                }
                //FIN NUEVO FOREACH
                //DATOS PARA GRAFICA CANTIDAD
                $datos['datosGrafiaSepaDimen_cant_graf_nueva'] = array_values($arrNuevaGraficaCant);
                $datos['datosGrafiaSepaDimen_cant'][] = [
                    'name' => 'Total', 'colorByPoint' => true,
                    'data' => $arrayTempCant, 'dataLabels' => $arrayLabelsx
                ];

                //DATOS PARA GRAFICA DE PROMEDIO
                $datos['datosGrafiaSepaDimen_prom_graf_nueva'] = array_values($arrNuevaGraficaProm);
                $datos['datosGrafiaSepaDimen_prom'][] = [
                    'name' => 'Total', 'colorByPoint' => true,
                    'data' => $arrayTempProm, 'dataLabels' => $arrayLabelsx
                ];

                //DATOS DE LA TABAL
                $datos['titulosTablaSepaDim'] = array_unique($titutabla);
                $datos['datosTablaSepaDim'] = $arrTabla;

                //EJE X ARBOLES
                $datos['arbolesEjeX'] = array_values(array_unique($arbolesEjeX));
            }
        } else {

            //DATOS DE ROL
            $selectPersonas = $wherePersonas = $joinPersonas = "";
            if ($model->valorador != '') {
                if ($segundoCalifPer) {
                    $wherePersonas .= " AND sc.id_responsable IN (" . $model->valorador . ") ";
                } else {
                    $wherePersonas .= " AND e.usua_id IN (" . $model->valorador . ") ";
                }
            }
            if ($model->rol != '') {
                $joinPersonas .= ' INNER JOIN rel_usuarios_roles rr on rr.rel_usua_id = e.usua_id ';
                $selectPersonas .= ',rr.*';
                $wherePersonas .= " AND rr.rel_role_id IN (" . $model->rol . ") ";
            }
            if ($model->equiposvalorador != '') {
                $modelequipoValoradores = \app\models\RelEquiposEvaluadores::find()->where('equipo_id IN (' . $model->equiposvalorador . ')')->asArray()->all();
                $arrayIdsusuarios = [];
                foreach ($modelequipoValoradores as $key => $value) {
                    $arrayIdsusuarios[] = $value['evaluadores_id'];
                }
                $idsUsuarios = implode(',', $arrayIdsusuarios);
                if ($segundoCalifPer) {
                    $wherePersonas .= " AND sc.id_responsable IN (" . $idsUsuarios . ") ";
                } else {
                    $wherePersonas .= " AND e.usua_id IN (" . $idsUsuarios . ") ";
                }
            }
            //DATOS DE VOLUMENES
            $joinVolumen = $whereVolumen = "";
            if ($volumenes) {
                $joinVolumen = "INNER JOIN tbl_base_satisfaccion satu ON satu.id = e.basesatisfaccion_id";
            }
            //WHERE VOLUMEN
            switch ($metrica) {
                case 13:
                    $whereVolumen = "AND satu.tipologia = 'FELICITACION'";
                    break;
                case 14:
                    $whereVolumen = "AND satu.tipologia = 'FELICITACIÓN CON BUZÓN'";
                    break;
                case 15:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA'";
                    break;
                case 16:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA POR BUZÓN'";
                    break;
                case 17:
                    $whereVolumen = "AND satu.tipologia = 'CRITICA PENALIZABLE'";
                    break;
                case 19:
                    $whereVolumen = "AND satu.responsabilidad = 'MARCA'";
                    break;
                case 20:
                    $whereVolumen = "AND satu.responsabilidad = 'CANAL'";
                    break;
                case 21:
                    $whereVolumen = "AND satu.responsabilidad = 'COMPARTIDA'";
                    break;
                case 22:
                    $whereVolumen = "AND satu.responsabilidad = 'EQUIVOCACION'";
                    break;
                default:
                    break;
            }
            $sql = "SELECT 
            DATE_FORMAT(e.created, '%Y-%m-%d %H:%i:%s') as fecha, 
            SUM(e.$colMetrica) AS suma,                    
            COUNT(e.$colMetrica) AS cantidad,                    
            e.arbol_id,
            a.name as arbol,
            e.dimension_id,                   
            d.name as dimension
            " . $selectPersonas . "
            FROM tbl_ejecucionformularios e
            JOIN tbl_arbols a ON a.id = e.arbol_id
            JOIN tbl_dimensions d ON d.id = e.dimension_id
            " . $joinVolumen . "
            " . $joinPersonas . "
            WHERE e.dimension_id IN(:dimension) 
                AND (e.created >= ':fechaInicio 00:00:00' 
                AND e.created <= ':fechaFin 23:59:59')
                AND e.arbol_id IN(:marbol)
                " . $wherePersonas . "
                " . $whereVolumen . "    
            GROUP BY e.arbol_id, e.dimension_id 
            ORDER BY e.arbol_id,  e.dimension_id, e.created";

            //QUERY PARA MÉTRICA DE SEGUNDO CALIFIADOR
            if ($segundoCalifPer) {
                $sql = "SELECT 
                            DATE_FORMAT(sc.s_fecha, '%Y-%m-%d %H:%i:%s') as fecha, 
                            SUM(e.$colMetrica) AS suma,                    
                            COUNT(e.$colMetrica) AS cantidad,                    
                            e.arbol_id,
                            a.name as arbol,
                            e.dimension_id,                   
                            d.name as dimension
                            " . $selectPersonas . "
                            FROM `tbl_segundo_calificador` sc
                            JOIN `tbl_ejecucionformularios` e ON e.`id` = sc.`id_ejecucion_formulario`
                            JOIN tbl_arbols a ON a.id = e.arbol_id
                            JOIN tbl_dimensions d ON d.id = e.dimension_id
                            " . $joinPersonas . "
                            WHERE e.dimension_id IN(:dimension) 
                                AND (sc.s_fecha >= ':fechaInicio 00:00:00' 
                                AND sc.s_fecha <= ':fechaFin 23:59:59')
                                AND e.arbol_id IN(:marbol)
                                " . $wherePersonas . "     
                                " . $whereVolumen . "     
                            GROUP BY e.arbol_id, e.dimension_id 
                            ORDER BY e.arbol_id,  e.dimension_id, e.created";
            }

            $resultData = \Yii::$app->db->createCommand($sql)
            ->bindValue(':dimension', $dimension)
            ->bindValue(':fechaInicio', $fechaInicio)
            ->bindValue(':fechaFin', $fechaFin)
            ->bindValue(':marbol', $model->arbol)
            ->queryAll();
            $datos = [];
            if (count($resultData) > 0) {

                $dataTabla = [];

                //DATOS DE LA TABAL
                $datos['datosTablaSepaDim'] = $resultData;
                //DATOS DE LA GRAFICA               
                $arrayLabelsx = [
                    'enabled' => true,
                    'rotation' => ($agrupar == 1) ? 0 : -90,
                    'color' => ($agrupar == 1) ? '#000000' : '#FFFFFF',
                    'align' => ($agrupar == 1) ? 'center' : 'right',
                    'style' => [
                        'fontSize' => ($agrupar == 1) ? '18px' : ((count($idsArboles) <
                            10) ? '15px' : '10px'),
                        'fontFamily' => 'Verdana, sans-serif'
                    ]
                ];

                //INICIO NUEVO FOREACH                
                $arrayTempCant = $arrayTempProm = /* $arrNuevaGraficaCant = $arrNuevaGraficaProm = $arbolesEjeX = */ [];
                $promTemp = $cantTemp = $arrTabla;
                foreach ($resultData as $keyTabla => $value) {

                    //PROMEDIO
                    if (!is_null($value['cantidad']) && $value['cantidad'] > 0) {
                        $promedio = round(($value['suma'] / $value['cantidad']) * 100, 2);
                    } else {
                        $promedio = 0;
                    }
                    //GRAFICA NUEVA CANTIDAD
                    $arbolesEjeX[] = $value['arbol'];
                    $cantTemp[$value['arbol_id']]['nombre'] = $value['arbol'];
                    $arrNuevaGraficaCant[$value['dimension_id']]['data'][$value['arbol_id']] = (int) $value['cantidad'];
                    $arrayTempCant[] = [$value['arbol'] . " - " . $value['dimension'], (int) $value['cantidad']];
                    //FIN GRAFICA NUEVA CANTIDAD
                    //GRAFICA NUEVA PROMEDIO                                   
                    $arrayTempProm[] = [$value['arbol'] . " - " . $value['dimension'], (float) $promedio];
                    $promTemp[$value['arbol_id']]['nombre'] = $value['arbol'];
                    $arrNuevaGraficaProm[$value['dimension_id']]['data'][$value['arbol_id']] = (float) $promedio;
                    //FIN GRAFICA NUEVA PROMEDIO
                    //DATOS TEMPRALES PARA TABLA SEPARADOS POR PROMEDIO Y CANT
                    $promTemp[$value['arbol_id']][$value['dimension_id']] = (float) $promedio . "%";
                    $cantTemp[$value['arbol_id']][$value['dimension_id']] = (int) $value['cantidad'];
                    $titutabla['promedio'][] = "Resultado dimensión " . $value['dimension'];
                    $titutabla['cantidad'][] = "Cantidad dimensión " . $value['dimension'];
                }

                //FORMATEO LOS INDICES DE LOS ARRAYS DE DATA
                foreach ($arrNuevaGraficaCant as $key => $value) {
                    $dataTemp = array_values($value['data']);
                    $arrNuevaGraficaCant[$key]['data'] = $dataTemp;
                }
                foreach ($arrNuevaGraficaProm as $key => $value) {
                    $dataTemp = array_values($value['data']);
                    $arrNuevaGraficaProm[$key]['data'] = $dataTemp;
                }
                //FIN FORMATEO LOS INDICES DE LOS ARRAYS DE DATA
                //ELIMINO ENCABEZADOS DE TABLA REPEDITOS Y JUNTO AMBAS
                $arr_union_titu = array_merge($titutabla['promedio'], $titutabla['cantidad']);
                $titutabla = array_unique($arr_union_titu);

                //JUNTO LOS ARRAY TEMPORALES EN LA TABLA DEFINITIVA                
                foreach ($promTemp as $arbol => $datos) {
                    if ($promTemp[$arbol]['nombre'] == '' && $cantTemp[$arbol]['nombre'] == '') {
                        unset($arrTabla[$arbol]);
                        continue;
                    }
                    $arr_union = array_merge($promTemp[$arbol], $cantTemp[$arbol]);
                    $arrTabla[$arbol] = $arr_union;
                }
                //FIN NUEVO FOREACH
                //DATOS PARA GRAFICA CANTIDAD
                $datos['datosGrafiaSepaDimen_cant_graf_nueva'] = array_values($arrNuevaGraficaCant);
                $datos['datosGrafiaSepaDimen_cant'][] = [
                    'name' => 'Total', 'colorByPoint' => true,
                    'data' => $arrayTempCant, 'dataLabels' => $arrayLabelsx
                ];

                //DATOS PARA GRAFICA DE PROMEDIO
                $datos['datosGrafiaSepaDimen_prom_graf_nueva'] = array_values($arrNuevaGraficaProm);
                $datos['datosGrafiaSepaDimen_prom'][] = [
                    'name' => 'Total', 'colorByPoint' => true,
                    'data' => $arrayTempProm, 'dataLabels' => $arrayLabelsx
                ];

                //DATOS DE LA TABAL
                $datos['titulosTablaSepaDim'] = array_unique($titutabla);
                $datos['datosTablaSepaDim'] = $arrTabla;

                //EJE X ARBOLES
                $datos['arbolesEjeX'] = array_values(array_unique($arbolesEjeX));
            }
        }

        if (count($resultData) > 0) {
            $showGraf = true;
        }
        //Datos de respuesta ---------------------------------------------------        
        $data->datosGrafica = $datos;
        $data->showGraf = $showGraf;
        $data->countCantidaArboles = count($model->arbol);
        $data->metricaSelecc = $this->validarMetrica($metrica);
        //DATO PARA SABER SI ES VOLUMNE Y SOLO MOSTRAR GRAF CANTIDAD
        $data->volumenes = $volumenes;
        //DATO PARA SABER SI ES SEGUNDOCALIFICADRO Y SOLO MOSTRAR GRAF CANTIDAD
        $data->segundoCalifPer = $segundoCalifPer;
        return $data;
    }
}
