<?php

namespace app\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use DateTime;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;

class ControlController extends \yii\web\Controller {

    public function behaviors() {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                //'delete' => ['post', 'get'],
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
                                'actions' => ['index', 'dimensionlistmultiple', 'metricalistmultiple', 'vistacorte'
                                    , 'indexcorte', 'update', 'volver', 'delete', 'validarcortes', 'indexpersona', 'equiposlistvaloradores'],
                                'allow' => true,
                                'roles' => ['@'],
                                'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminProcesos();
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
            public function actionIndex() {
                $modelBusqueda = \app\models\FiltrosControl::find()->where(['usua_id' => Yii::$app->user->identity->id, 'guardar_filtro' => 1])->one();
                $arrArboles = [];
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
                $datos = [];
                $fechas = [];
                $data->showGraf = false;
                if (Yii::$app->request->post()) {
                    $form = Yii::$app->request->post('form');
                    $model->scenario = ($form == "0") ? 'filtroProceso' : 'filtroProcesoDetallado';
                    $idsArboles = ($form == "0") ? ((Yii::$app->request->post('arbol_ids') != null) ? Yii::$app->request->post('arbol_ids') : null) : ((Yii::$app->request->post('arbol_idsDetallada') != null) ? Yii::$app->request->post('arbol_idsDetallada') : null);
                    $model->arbol = ($idsArboles != null) ? implode(",", $idsArboles) : null;
                    if ($model->arbol == null) {
                        $msg = \Yii::t('app', 'Seleccione un arbol');
                        Yii::$app->session->setFlash('danger', $msg);
                    } else {
                        $arrArboles = explode(',', $model->arbol);
                    }
                    $data->banderaError = ($form == "0") ? 'vistaunica' : 'vistadetallada';
                }
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    /* Inicio construccion de graficas */
                    if ($form == "0") {
                        /* if (count($idsArboles) == 0) {
                          $msg = \Yii::t('app', 'Seleccione un arbol');
                          Yii::$app->session->setFlash('danger', $msg);
                          } else {
                          $model->arbol = implode(",", $idsArboles); */
                        $fechas [] = $model->fecha;
                        $ejecucionformulario = new \app\models\Ejecucionformularios();
                        $metrica = $this->validarMetrica($model->metrica);
                        $agrupar = Yii::$app->request->post('agrupar');
                        $arrayLabelsx = [
                            'enabled' => true,
                            'rotation' => ($agrupar == 1) ? 0 : -90,
                            'color' => ($agrupar == 1) ? '#000000' : '#FFFFFF',
                            'align' => ($agrupar == 1) ? 'center' : 'right',
                            'style' => [
                                'fontSize' => ($agrupar == 1) ? '18px' : '10px',
                                'fontFamily' => 'Verdana, sans-serif'
                            ]
                        ];
                        $menorarboles = 100;
                        $menordimensiones = 100;
                        if ($agrupar == 1) {
                            $arrayGrupos = $this->construirArbolgrupos($model->arbol);
                            foreach ($arrayGrupos as $key => $grupo) {

                                $consulta = $ejecucionformulario->getDatabygrafgrupo(implode(',', $grupo['hijos']), $model->dimension, $model->fecha, $metrica, $model);
                                if (count($consulta) > 0) {
                                    //foreach ($consulta as $value) {
                                    $arbolPadre = \app\models\Arboles::findOne($key);
                                    $promedio = ($consulta[0]['promedio'] * 100);
                                    $menorarboles = ($promedio < $menorarboles) ? $promedio : $menorarboles;
                                    $datos['arbol'][] = ['name' => $arbolPadre->name, 'data' => [(double) round($promedio, 2)]];
                                    $datos['count'][] = ['name' => $arbolPadre->name, 'data' => [(int) $consulta[0]['total']]];
                                }
                            }
                        } else {
                            $consulta = $ejecucionformulario->getDatabygraf($model->arbol, $model->dimension, $model->fecha, $metrica, true, $model, "proceso");
                            if (count($consulta) > 0) {
                                foreach ($consulta as $value) {
                                    $arbolPadre = \app\models\Arboles::findOne($value['arbol_id']);
                                    $promedio = ($value['promedio'] * 100);
                                    $menorarboles = ($promedio < $menorarboles) ? $promedio : $menorarboles;
                                    $datos['arbol'][] = ['name' => $arbolPadre->name, 'data' => [(double) round($promedio, 2)]];
                                    $datos['count'][] = ['name' => $arbolPadre->name, 'data' => [(int) $value['total']]];
                                }
                            }
                        }

                        if (count($consulta) > 0) {
                            if ($consulta[0]['total'] != 0) {
                                $arrayTemp = [];
                                foreach ($datos['arbol'] as $valueArbol) {
                                    $arrayTemp[] = [$valueArbol['name'], $valueArbol['data'][0]];
                                }
                                $datos['arbol2'][] = ['name' => 'Total', 'colorByPoint' => true, 'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx];
                                $arrayTemp = [];
                                foreach ($datos['count'] as $valueContador) {
                                    $arrayTemp[] = [$valueContador['name'], $valueContador['data'][0]];
                                }
                                $datos['count2'][] = ['name' => 'Total', 'colorByPoint' => true, 'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx];
                                $consulta1 = $ejecucionformulario->getDatabygraf($model->arbol, $model->dimension, $model->fecha, $metrica, false, $model, "proceso");
                                foreach ($consulta1 as $value) {
                                    $arbolPadre = \app\models\Dimensiones::findOne($value['dimension_id']);
                                    $promedio = ($value['promedio'] * 100);
                                    $menordimensiones = ($promedio < $menordimensiones) ? $promedio : $menordimensiones;
                                    $datos['dimension'][] = ['name' => $arbolPadre->name, 'data' => [(double) round($promedio, 2)]];
                                    //$arrNombres['dimension'][] = $arbolPadre->dsname_full;
                                }
                                $arrayTemp = [];
                                foreach ($datos['dimension'] as $valueDimension) {
                                    $arrayTemp[] = [$valueDimension['name'], $valueDimension['data'][0]];
                                }
                                $datos['dimension2'][] = ['name' => 'Total', 'colorByPoint' => true, 'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx];
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
                        
                    } else {
                        
                        /* if (count($idsArboles) == 0) {
                          $msg = \Yii::t('app', 'Seleccione un arbol');
                          Yii::$app->session->setFlash('danger', $msg);
                          } else { */
                        $ejecucionformulario = new \app\models\Ejecucionformularios();
                        $model->ids_arboles = $model->arbol;
                        $model->rango_fecha = $model->fechaDetallada;
                        $model->ids_metricas = $model->metricaDetallada;
                        $model->ids_dimensiones = $model->dimensionDetallada;
                        $model->corte_id = $model->corteDetallada;
                        $model->usua_id = Yii::$app->user->identity->id;
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
                            $data->datosTabla = $this->construirTabla($model->rango_fecha, Yii::$app->user->identity->id, $model->corte_id, $model->ids_metricas, $model->ids_dimensiones, $model->ids_arboles, $model, $form, "proceso");
                            $tablaExcel = $this->actionGenerartablaexcel($data->datosTabla['datos'], $model->ids_dimensiones, $data->datosTabla['total'], "proceso");
                            $this->generarExcel($tablaExcel, $data->datosTabla['cortes'], $model->ids_dimensiones, $model->ids_metricas, false, Yii::$app->user->identity->id, $model->corte_id, "proceso");
                        } else {
                            $data->showGraf = false;
                            $msg = \Yii::t('app', 'No se encontraron datos para los filtros ingresados');
                            Yii::$app->session->setFlash('danger', $msg);
                        }
                        
                    }
                }
                $data->arboles[] = $this->getRecursivearbolscopia('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', 'arbol_ids', $arrArboles);
                $data->arboles[] = $this->getRecursivearbolscopia('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', 'arbol_idsDetallada', $arrArboles);
                $data->metrica = ArrayHelper::map(
                                \app\models\Metrica::find()->limit(10)->asArray()->all()
                                , 'id', 'detexto');
                $data->metrica[] = 'Score';
                return $this->render('index', ['data' => $data, 'model' => $model, '']);
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
            public function getRecursivearbolscopia($tabla, $id_field, $show_data, $link_field, $parent, $prefix, $idetiqueta, $arraArboles) {
                /* Armar query */
                if ($parent == 0) {
                    $sql = 'select * from ' . $tabla . ' where ' . $link_field . ' is null';
                } else {
                    $sql = 'select * from ' . $tabla . ' where ' . $link_field . '=' . $parent;
                }
                $rs = Yii::$app->db->createCommand($sql)->queryAll();
                $out = '<ol id="' . $idetiqueta . '" name="' . $idetiqueta . '[]">';
                if ($rs) {
                    foreach ($rs as $arr) {
                        if (in_array($arr['id'], $arraArboles)) {
                            $out .= '<li data-value = "' . $arr['id'] . '" data-name = "' . $idetiqueta . '[]" data-checked="checked">';
                        } else {
                            $out .= '<li data-value = "' . $arr['id'] . '" data-name ="' . $idetiqueta . '[]">';
                        }
                        $out .= $arr['name'];
                        $out .=$this->getRecursivearbolscopia($tabla, $id_field, $show_data, $link_field, $arr[$id_field], $prefix . $prefix, $idetiqueta, $arraArboles);
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
            public function actionDimensionlistmultiple($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
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
             * Obtiene el listado de Metricas
             * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @param type $search
             * @param type $id
             */
            public function actionMetricalistmultiple($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }
                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\Metrica::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
                            ->where('detexto LIKE "%' . $search . '%"')
                            ->orderBy('detexto')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\Metrica::find()
                            ->select(['id' => 'id', 'text' => 'UPPER(detexto)'])
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
             * Funcion que permite renderizar la vista de cortes, la cual mostrara uno o 
             * varios campos para ingreso de fecha
             * * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             * @return type
             */
            public function actionVistacorte() {


                $modelCorte = new \app\models\CorteFecha();
                $modelSegmento = new \app\models\SegmentoCorte();
                $fechas = [];

                if (Yii::$app->getRequest()->isAjax) {
                    $modelSegmento->scenario = (Yii::$app->request->post('Tipo_corte') == 1) ? 'corteSemana' : 'corteMes';
                    $rangofecha = Yii::$app->request->get('rangofecha');
                    if (Yii::$app->request->post()) {
                        $rangofecha = Yii::$app->request->post('rangofecha');
                        if (($modelSegmento->load(Yii::$app->request->post())) &&
                                ($modelSegmento->validate())) {
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
            public function actionIndexcorte() {

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
            public function actionUpdate() {
                $modelCorte = \app\models\CorteFecha::find()->where(['corte_id' => Yii::$app->request->get('corte_id')])->one();
                $arraySegmento = \app\models\SegmentoCorte::find()->where(['corte_id' => $modelCorte->corte_id])->all();
                $modelSegmento = new \app\models\SegmentoCorte();
                $rangofecha = Yii::$app->request->get('rangofecha');
                if (Yii::$app->getRequest()->isAjax) {
                    $modelSegmento->scenario = (Yii::$app->request->post('Tipo_corte') == 1) ? 'corteSemana' : 'corteMes';
                    if (Yii::$app->request->post()) {
                        $rangofecha = Yii::$app->request->post('rangofecha');
                        if (($modelSegmento->load(Yii::$app->request->post())) &&
                                ($modelSegmento->validate() && $modelCorte->validate())) {
                            $modelCorte = \app\models\CorteFecha::find()->where(['corte_id' => Yii::$app->request->post('corte_id')])->one();
                            $datos = Yii::$app->request->post('SegmentoCorte');
                            $modelCorte->tipo_corte = Yii::$app->request->post('Tipo_corte');
                            $modelCorte->usua_id = Yii::$app->user->identity->id;
                            //CAMBIAR PARA RECIBIR DE VISTA
                            $modelCorte->band_repetir = 1;
                            $modelCorte->save();
                            $timeMes = 0;
                            if ($modelCorte->tipo_corte == 1) {
                                for ($i = 1; $i < count($datos); $i++) {
                                    $idSemana = Yii::$app->request->post('idsemana' . $i);
                                    if (isset($idSemana)) {
                                        $modelseg = \app\models\SegmentoCorte::find()->where(['segmento_corte_id' => $idSemana])->one();
                                    } else {
                                        if (isset($datos['semana' . $i])) {
                                            $modelseg = new \app\models\SegmentoCorte();
                                        }
                                    }
                                    $fechas = explode(' - ', $datos['semana' . $i]);
                                    $modelseg->segmento_fecha_inicio = $fechas[0] . ' 00:00:00';
                                    $modelseg->segmento_fecha_fin = $fechas[1] . ' 23:59:59';
                                    $modelseg->segmento_nombre = 'Semana ' . $i;
                                    $modelseg->corte_id = $modelCorte->corte_id;
                                    $timeMes = strtotime($fechas[1]);
                                    $timeMes = getdate($timeMes);
                                    $modelseg->save();
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
                                $modelseg->segmento_nombre = 'Mes';
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
            public function actionDelete() {
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
            public function actionVolver() {
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
            public function construirTabla($rangofecha = null, $id_usuario = null, $tipoCorte = null, $metrica = null, $dimension = null, $arbol = null, $model = null, $form = null, $control = null) {
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
                        for ($i = $fechaInicio; $i <= $fechaFin; $i+=86400) {
                            $cortes[] = ['fechaI' => date('Y-m-d', $i) . ' 00:00:00', 'fechaF' => date('Y-m-d', $i) . ' 23:59:59'];
                        }
                        break;
                    default:
                        break;
                }
                $dataTable = [];
                $ejecucionformulario = new \app\models\Ejecucionformularios();

                if ($form == "0") {
                    foreach ($cortes as $corte) {
                        $dataTable ['datos'][] = $ejecucionformulario->getDatabytable($dimension, $corte, $metrica, $arbol, $model);
                    }
                    $dataTable['total'][] = $ejecucionformulario->getDatabytabletotal($dimension, $rangofecha, $metrica, $arbol, $model);
                } else {
                    $metrica = explode(',', $metrica);
                    foreach ($cortes as $corte) {
                        for ($i = 0; $i < count($metrica); $i++) {
                            $validarMetrica = $this->validarMetrica($metrica[$i]);
                            if ($control == "proceso") {
                                $dataTable ['datos'][$corte['fechaI'] . " - " . $corte['fechaF']][$validarMetrica][] = $ejecucionformulario->getDatabytable($dimension, $corte, $validarMetrica, $arbol, $model);
                            } else {
                                $dataTable ['datos'][$corte['fechaI'] . " - " . $corte['fechaF']][$validarMetrica][] = $ejecucionformulario->getDatabygrafexcelpersona($arbol, $dimension, $corte['fechaI'] . " - " . $corte['fechaF'], $validarMetrica, $model);
                            }
                        }
                    }
                    for ($i = 0; $i < count($metrica); $i++) {
                        $validarMetrica = $this->validarMetrica($metrica[$i]);

                        if ($control == "proceso") {
                            $dataTable['total'][] = $ejecucionformulario->getDatabytabletotal($dimension, $rangofecha, $validarMetrica, $arbol, $model);
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
            public function calcularCortessemanaauto($rangofecha = null, $banderaAuto = null, $id_usuario = null, $tipo_corte = null) {
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
                $meses = ( $diferencia->y * 12 ) + $diferencia->m;
                $meses = ($meses == 0) ? 1 : $meses;
                for ($index = 1; $index <= $meses; $index++) {
                    $inicioMes = $inicioMes;
                    $mesfinal = explode('-', $inicioMes);
                    $mesfinal[2] = date("d", (mktime(0, 0, 0, $mesfinal[1] + 1, 1, $mesfinal[0]) - 1));
                    $mesfinal = implode('-', $mesfinal);
                    $mesfinal = $mesfinal . ' 23:59:59';
                    $cortesFecha = [];
                    if ($banderaAuto) {
                        $cortesFecha = \app\models\SegmentoCorte::find()->from('tbl_segmento_corte sc')
                                ->join('INNER JOIN', 'tbl_corte_fecha cf', 'sc.corte_id = cf.corte_id')
                                ->where('((segmento_fecha_inicio BETWEEN "' . $inicioMes . '" AND "' . $mesfinal . '") '
                                        . ' OR (segmento_fecha_fin BETWEEN "' . $inicioMes . '" AND "' . $mesfinal . '")) AND (cf.usua_id = ' . $id_usuario . ')')
                                ->andWhere('cf.tipo_corte = ' . $tipo_corte)
                                ->orderBy('segmento_fecha_inicio,segmento_fecha_fin ASC')
                                ->all();
                    }
                    if (count($cortesFecha) == 0) {
                        for ($i = 1; $i <= 5; $i++) {
                            $inicioMes = (isset($iniciosegmento)) ? $iniciosegmento : $inicioMes;
                            $mesfinalsegmento = explode('-', $inicioMes);
                            $mesfinalsegmento[2] = date("d", (mktime(0, 0, 0, $mesfinalsegmento[1] + 1, 1, $mesfinalsegmento[0]) - 1));
                            $mesfinalsegmento = implode('-', $mesfinalsegmento);
                            $cortesSemanaCalc [] = ['fechaI' => $inicioMes . ' 00:00:00', 'fechaF' => (($i == 5) ? $mesfinalsegmento . ' 23:59:59' : date('Y-m-d', (strtotime($inicioMes) + (86400 * 6))) . ' 23:59:59')];
                            $iniciosegmento = ($i == 5) ? date('Y-m-d', (strtotime('+1 day', strtotime($mesfinalsegmento)))) : date('Y-m-d', (strtotime('+1 day', strtotime($inicioMes) + (86400 * 6))));
                        }
                    } else {
                        foreach ($cortesFecha as $corte) {
                            $cortesSemanaCalc[] = ['fechaI' => $corte->segmento_fecha_inicio, 'fechaF' => $corte->segmento_fecha_fin];
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
            public function calcularCortesSemana($rangofecha = null, $id_usuario = null, $tipo_corte = null) {
                //busco los cortes que esten dentro del rango fecha
                $fechas = explode(' - ', $rangofecha);
                $fechas[0] = $fechas[0] . ' 00:00:00';
                $fechas[1] = $fechas[1] . ' 23:59:59';
                $cortesSemana = [];
                $corteAnterior = null;
                $cortesFecha = \app\models\SegmentoCorte::find()->from('tbl_segmento_corte sc')
                        ->join('INNER JOIN', 'tbl_corte_fecha cf', 'sc.corte_id = cf.corte_id')
                        ->where('((segmento_fecha_inicio BETWEEN "' . $fechas[0] . '" AND "' . $fechas[1] . '") '
                                . ' OR (segmento_fecha_fin BETWEEN "' . $fechas[0] . '" AND "' . $fechas[1] . '")) AND (cf.usua_id = ' . $id_usuario . ')')
                        ->andWhere('cf.tipo_corte = ' . $tipo_corte)
                        ->orderBy('segmento_fecha_inicio,segmento_fecha_fin ASC')
                        ->all();
                $cortesSemanaCalc = $this->calcularCortessemanaauto($rangofecha, (count($cortesFecha) > 0) ? true : false, $id_usuario, $tipo_corte);
                for ($i = 0; $i < count($cortesSemanaCalc); $i++) {
                    if (!is_null($corteAnterior)) {
                        $fechafincomparar = strtotime('-1 day', strtotime($cortesSemanaCalc[$i]['fechaI']));
                        $fechafincomparar = date('Y-m-d', $fechafincomparar);
                        $fechafincomparar = $fechafincomparar . ' 23:59:59';
                        if (($cortesSemanaCalc[$i]['fechaI'] >= $fechas[0] || $cortesSemanaCalc[$i]['fechaF'] >= $fechas[0]) && ($cortesSemanaCalc[$i]['fechaI'] <= $fechas[1] || $cortesSemanaCalc[$i]['fechaF'] <= $fechas[1])) {
                            if (!isset($cortesSemanaCalc[$i + 1])) {
                                if ($fechas[1] <= $cortesSemanaCalc[$i]['fechaF']) {
                                    $fechaFin = strtotime($fechas[1]);
                                    $fechaFin = date('Y-m-d H:i:s', $fechaFin);
                                    $cortesSemana[] = ['fechaI' => $cortesSemanaCalc[$i]['fechaI'], 'fechaF' => $fechaFin];
                                }
                                if ($fechas[1] > $cortesSemanaCalc[$i]['fechaF']) {
                                    $cortesSemana[] = ['fechaI' => $cortesSemanaCalc[$i]['fechaI'], 'fechaF' => $cortesSemanaCalc[$i]['fechaF']];
                                    $fechaI = strtotime('+1 day', strtotime($cortesSemanaCalc[$i]['fechaF']));
                                    $fechaI = date('Y-m-d', $fechaI);
                                    $cortesSemana[] = ['fechaI' => $fechaI . ' 00:00:00', 'fechaF' => $fechas[1]];
                                }
                            } else {
                                if ($fechas[0] >= $cortesSemanaCalc[$i]['fechaI']) {
                                    $fechaI = date('Y-m-d H:i:s', strtotime($fechas[0]));
                                    $cortesSemana[] = ['fechaI' => $fechaI, 'fechaF' => $cortesSemanaCalc[$i]['fechaF']];
                                } else {
                                    if ($fechas[1] < $cortesSemanaCalc[$i]['fechaF']) {
                                        $fechaFin = strtotime($fechas[1]);
                                        $fechaFin = date('Y-m-d H:i:s', $fechaFin);
                                        $cortesSemana[] = ['fechaI' => $cortesSemanaCalc[$i]['fechaI'], 'fechaF' => $fechaFin];
                                    } else {
                                        $cortesSemana[] = ['fechaI' => $cortesSemanaCalc[$i]['fechaI'], 'fechaF' => $cortesSemanaCalc[$i]['fechaF']];
                                    }
                                }
                            }
                        }
                    } else {
                        if (($cortesSemanaCalc[$i]['fechaI'] >= $fechas[0] || $cortesSemanaCalc[$i]['fechaF'] >= $fechas[0]) && ($cortesSemanaCalc[$i]['fechaI'] <= $fechas[1] || $cortesSemanaCalc[$i]['fechaF'] <= $fechas[1])) {
                            if ($fechas[0] >= $cortesSemanaCalc[$i]['fechaI']) {
                                $fechaI = strtotime($fechas[0]);
                                $fechaI = date('Y-m-d H:i:s', $fechaI);
                                $cortesSemana[] = ['fechaI' => $fechas[0], 'fechaF' => $cortesSemanaCalc[$i]['fechaF']];
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
            public function calcularCortesMes($rangofecha = null, $id_usuario = null, $tipo_corte = null) {
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
                $meses = ( $diferencia->y * 12 ) + $diferencia->m;
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
                            ->where('((segmento_fecha_inicio BETWEEN "' . $inicioMes . ' 00:00:00' . '" AND "' . $finMes . ' 23:59:59' . '") '
                                    . ' OR (segmento_fecha_fin BETWEEN "' . $inicioMes . ' 00:00:00' . '" AND "' . $finMes . ' 23:59:59' . '")) AND (cf.usua_id = ' . $id_usuario . ')')
                            ->andWhere('cf.tipo_corte = ' . $tipo_corte)
                            ->orderBy('segmento_fecha_inicio,segmento_fecha_fin ASC')
                            ->one();
                    $inicioMes = ($primeraIteraccion) ? $fechas[0] : $inicioMes;
                    if (count($cortesFecha) > 0) {
                        $arrayCorteTemp = ['fechaI' => $cortesFecha->segmento_fecha_inicio, 'fechaF' => $cortesFecha->segmento_fecha_fin];

                        if ($primeraIteraccion) {
                            if ($arrayCorteTemp['fechaI'] < $fechas[0]) {
                                $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($arrayCorteTemp['fechaF']))));
                                $cortesMesCalc[] = ['fechaI' => $fechas[0], 'fechaF' => $arrayCorteTemp['fechaF']];
                            } else {
                                $cortesMesCalc[] = ['fechaI' => $fechas[0], 'fechaF' => date('Y-m-d', (strtotime('-1 day', strtotime($arrayCorteTemp['fechaI'])))) . ' 23:59:59'];
                                $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($arrayCorteTemp['fechaF']))));
                                $cortesMesCalc[] = ['fechaI' => $arrayCorteTemp['fechaI'], 'fechaF' => $arrayCorteTemp['fechaF']];
                            }
                        } else {
                            if ($inicioMes < $arrayCorteTemp['fechaI']) {
                                $fechaTemp = date('Y-m-d', (strtotime('-1 day', strtotime($arrayCorteTemp['fechaI']))));
                                $cortesMesCalc[] = ['fechaI' => $inicioMes . ' 00:00:00', 'fechaF' => $fechaTemp . ' 23:59:59'];
                            }
                            if ($fechas[1] < $arrayCorteTemp['fechaF']) {
                                $cortesMesCalc[] = ['fechaI' => $arrayCorteTemp['fechaI'], 'fechaF' => $fechas[1]];
                                $banderaSalida = false;
                            } else {
                                $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($arrayCorteTemp['fechaF']))));
                                $cortesMesCalc[] = ['fechaI' => $arrayCorteTemp['fechaI'], 'fechaF' => $arrayCorteTemp['fechaF']];
                            }
                        }
                    } else {
                        if ($finMes < $fechas[1]) {
                            $cortesMesCalc[] = ['fechaI' => $inicioMes . ' 00:00:00', 'fechaF' => $finMes . ' 23:59:59'];
                            $inicioMes = date('Y-m-d', (strtotime('+1 day', strtotime($finMes))));
                        } else {
                            $cortesMesCalc[] = ['fechaI' => $inicioMes . ' 00:00:00', 'fechaF' => $fechas[1]];
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
            public function validarMetrica($metrica = null) {
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
                    default:
                    //fin
                    break;
                }
                return $baseConsulta;
            }

            /**
             * Funcion que permite validar los cortes que se van a parametrizar
             * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
             */
            public function actionValidarcortes() {
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
                                        break;
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
            public function actionGenerartablaexcel($datosTabla = null, $dimensiones = null, $arrayTotal = null, $control = null) {

                $dimensiones = \app\models\Dimensiones::find()->where("id IN(" . $dimensiones . ")")->orderBy("id ASC")->asArray()->all();
                $arrayTabla = [];
                try {
                    if ($control == "proceso") {
                        for ($index = 0; $index < count($dimensiones); $index++) {
                            foreach ($datosTabla as $key => $datoMetrica) {
                                foreach ($datoMetrica as $datoDimension) {

                                    if (count($datoDimension[0]) > 0) {
                                        foreach ($datoDimension[0] as $value) {
                                            if ($value['dimension_id'] == $dimensiones[$index]['id']) {
                                                $arrayTabla[$dimensiones[$index]['id']][$key][] = round($value['promedio'] * 100, 2) . '%';
                                            }
                                        }
                                    } else {
                                        $arrayTabla[$dimensiones[$index]['id']][$key][] = '-';
                                    }
                                }
                            }
                            foreach ($arrayTotal as $totalMetrica) {
                                if (count($totalMetrica[$index]) > 0) {
                                    if ($totalMetrica[$index]['dimension_id'] == $dimensiones[$index]['id']) {
                                        $arrayTabla[$dimensiones[$index]['id']]['Total'][] = round($totalMetrica[$index]['promedio'] * 100, 2) . '%';
                                    }
                                } else {
                                    $arrayTabla[$dimensiones[$index]['id']]['Total'][] = '-';
                                }
                            }
                        }
                    } else {
                        for ($index = 0; $index < count($dimensiones); $index++) {
                            foreach ($datosTabla as $key => $datoMetrica) {
                                foreach ($datoMetrica as $keyM => $datoDimension) {
                                    if (count($datoDimension[0]) > 0) {
                                        foreach ($datoDimension[0] as $value) {
                                            if ($value['dimension_id'] == $dimensiones[$index]['id']) {
                                                $arrayTabla[$dimensiones[$index]['id']][$key][$keyM][$value['usua_usuario']][] = round($value['promedio'] * 100, 2) . '%';
                                            }
                                        }
                                    } else {
                                        $arrayTabla[$dimensiones[$index]['id']][$key][$keyM][$value['usua_usuario']][] = '-';
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
            public function generarExcel($tablaExcel = null, $cortes = null, $id_dimensiones = null, $ids_metricas = null, $banderaEnvio = null, $id_usuario, $tipo_corte = null, $control = null) {
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
                $dimensiones = \app\models\Dimensiones::find()->where("id IN(" . $id_dimensiones . ")")->orderBy("id ASC")->asArray()->all();
                $metricas = explode(',', $ids_metricas);
                $arrayMetrica = [];
                for ($a = 0; $a < count($metricas); $a++) {
                    $arrayMetrica[] = \app\models\Metrica::find()->where("id = " . $metricas[$a])->asArray()->one();
                }
                $arrayCortes = [];
                for ($index = 0; $index < count($cortes); $index++) {
                    $arrayCortes[] = $cortes[$index]['fechaI'] . " - " . $cortes[$index]['fechaF'];
                }
                $arrayCortes[] = 'Total';
                set_time_limit(0);
                $objPHPexcel = new \PHPExcel();
                $objPHPexcel->setActiveSheetIndex(0);
                $column = 'A';
                $row = 1;
                $banderaValorado = false;
                try {
                    if ($control == "proceso") {
                        foreach ($dimensiones as $value) {
                            $column = 'A';
                            $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value['name']);
                            $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                            $inicioRow = $row;
                            $row++;
                            for ($i = 0; $i < count($arrayMetrica); $i++) {
                                $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $arrayMetrica[$i]['detexto']);
                                $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                                $row++;
                            }
                            $row = $inicioRow;
                            $column++;
                            foreach ($arrayCortes as $corte) {
                                $row = $inicioRow;
                                if (array_key_exists($corte, $tablaExcel[$value['id']])) {
                                    if ($tipo_corte == 3) {
                                        $etiquetaCorte = explode(' - ', str_replace(['00:00:00', '23:59:59'], '', $corte));
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $etiquetaCorte[0]));
                                    } else {
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, str_replace(['00:00:00', '23:59:59'], '', $corte));
                                    }
                                    $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                                    $row++;
                                    foreach ($tablaExcel[$value['id']][$corte] as $prom) {
                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $prom);
                                        $row++;
                                    }
                                }
                                $column++;
                            }
                            $row+=5;
                        }
                    } else {
                        foreach ($dimensiones as $key => $value) {
                            $arrayValorador = [];
                            $column = 'A';
                            $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value['name']);
                            $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                            $column++;
                            $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Valorador");
                            $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                            $column++;
                            $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, "Métrica");
                            $objPHPexcel->getActiveSheet()->getStyle($column . '' . $row)->applyFromArray($styleArray);
                            $column++;
                            $inicioRow = $row;
                            $row++;
                            $row = $inicioRow;
                            foreach ($arrayCortes as $corte) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']])) {
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
                            foreach ($arrayCortes as $corte) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']])) {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            foreach ($valueValorador as  $valueDato) {
                                                $column = "B";
                                                if ($banderaValorado == false) {
                                                    $banderaValorado = true;
                                                    $arrayValorador[$keyMetrica . '-' . $keyValorador] = $row;
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $keyValorador);
                                                    $column++;
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $keyMetrica));
                                                    $row+=1;
                                                    $ultimoDatos = $keyMetrica . '-' . $keyValorador;
                                                } else {
                                                    if (!array_key_exists($keyMetrica . '-' . $keyValorador, $arrayValorador)) {
                                                        $arrayValorador[$keyMetrica . '-' . $keyValorador] = $row;
                                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $keyValorador);
                                                        $column++;
                                                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, Yii::t('app', $keyMetrica));
                                                        $row+=1;
                                                        $ultimoDatos = $keyMetrica . '-' . $keyValorador;
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                            $column = "D";
                            foreach ($arrayCortes as $corte) {
                                if (array_key_exists($corte, $tablaExcel[$value['id']])) {
                                    foreach ($tablaExcel[$value['id']][$corte] as $keyMetrica => $metrica) {
                                        foreach ($metrica as $keyValorador => $valueValorador) {
                                            foreach ($valueValorador as $keyDato => $valueDato) {
                                                if (array_key_exists($keyMetrica . '-' . $keyValorador, $arrayValorador)) {
                                                    $row = $arrayValorador[$keyMetrica . '-' . $keyValorador];
                                                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $valueDato);
                                                }
                                            }
                                        }
                                    }
                                }
                                $column++;
                            }
                            $row = ($arrayValorador[$ultimoDatos] + 5);
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
            public function actionEnviarcorreo($control) {
                try {
                    $filtrosEnvio = \app\models\FiltrosControl::find()->all();
                    foreach ($filtrosEnvio as $value) {
                        if ($value->guardar_filtro == 1) {
                            $data = $this->construirTabla($value->rango_fecha, $value->usua_id, $value->corte_id, $value->ids_metricas, $value->ids_dimensiones, $value->ids_arboles, $value, "1", $control);
                            $tablaExcel = $this->actionGenerartablaexcel($data['datos'], $value->ids_dimensiones, $data['total'], $control);
                            $usuario = \app\models\Usuarios::find()->where(['usua_id' => $value->usua_id])->one();
                            if ($tablaExcel != false && $usuario->usua_email != "") {
                                $ruta = $this->generarExcel($tablaExcel, $data['cortes'], $value->ids_dimensiones, $value->ids_metricas, true, Yii::$app->user->identity->id, $value->corte_id, $control);
                                Yii::$app->mailer->compose('@app/views/control/plantillaCorreo')
                                        ->setFrom(\Yii::$app->params['email_envio_proceso'])
                                        ->setTo($usuario->usua_email)
                                        ->setSubject('Excel Control Proceso')
                                        ->attach($ruta)
                                        ->attach(Yii::$app->basePath . "/web/images/plantilla.png")
                                        ->send();
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

            public function repetirCorte($datos, $modelCorte, $rangofecha) {
                //$arrayFecha = explode(' - ', $rangofecha);
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
                                    $fechaInicial['mon']+=1;
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
                                    $fechaInicial['mon']+=1;
                                    $fechaFinal['mon']+=1;
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

            public function construirArbolgrupos($arraArboles) {
                $arrayArboles = [];
                $consultaArboles = \app\models\Arboles::find()->where('id IN (' . $arraArboles . ')')->orderBy('dsorden ASC')->asArray()->all();
                foreach ($consultaArboles as $value) {
                    $arraArboles .=',' . $value['arbol_id'];
                }
                $consultaArboles = \app\models\Arboles::find()->where('id IN (' . $arraArboles . ')')->orderBy('dsorden ASC')->asArray()->all();
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
            public function actionIndexpersona() {
                $modelBusqueda = \app\models\FiltrosControl::find()->where(['usua_id' => Yii::$app->user->identity->id, 'guardar_filtro' => 1])->one();
                $arrArboles = [];
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
                $datos = [];
                $fechas = [];
                $data->showGraf = false;
                $banderaValidacion = true;
                

                if (Yii::$app->request->post()) {
                    $form = Yii::$app->request->post('form');
                    $model->scenario = ($form == "0") ? 'filtroProceso' : 'filtroProcesoDetallado';
                    $idsArboles = ($form == "0") ? ((Yii::$app->request->post('arbol_ids') != null) ? Yii::$app->request->post('arbol_ids') : null) : ((Yii::$app->request->post('arbol_idsDetallada') != null) ? Yii::$app->request->post('arbol_idsDetallada') : null);
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
                    if ($model->rol == '' && $model->valorador == '' && $model->equiposvalorador == '') {
                        $banderaValidacion = false;
                        $msg = \Yii::t('app', 'Verifique que al menos uno de los siguientes campos esta diligenciado: rol, valorador o equipos');
                        Yii::$app->session->setFlash('danger', $msg);
                    }
                    if ($model->arbol == null) {
                        $msg = \Yii::t('app', 'Seleccione un arbol');
                        Yii::$app->session->setFlash('danger', $msg);
                    } else {
                        $arrArboles = explode(',', $model->arbol);
                    }
                    $data->banderaError = ($form == "0") ? 'vistaunica' : 'vistadetallada';
                }
                if ($model->load(Yii::$app->request->post()) && $model->validate()) {
                    /* Inicio construccion de graficas */
                    if ($banderaValidacion) {
                        if ($form == "0") {
                            /* if (count($idsArboles) == 0) {
                              $msg = \Yii::t('app', 'Seleccione un arbol');
                              Yii::$app->session->setFlash('danger', $msg);
                              } else {
                              $model->arbol = implode(",", $idsArboles); */
                            $fechas [] = $model->fecha;
                            $ejecucionformulario = new \app\models\Ejecucionformularios();
                            $metrica = $this->validarMetrica($model->metrica);
                            $agrupar = Yii::$app->request->post('agrupar');
                            $arrayLabelsx = [
                                'enabled' => true,
                                'rotation' => ($agrupar == 1) ? 0 : -90,
                                'color' => ($agrupar == 1) ? '#000000' : '#FFFFFF',
                                'align' => ($agrupar == 1) ? 'center' : 'right',
                                'style' => [
                                    'fontSize' => ($agrupar == 1) ? '18px' : '10px',
                                    'fontFamily' => 'Verdana, sans-serif'
                                ]
                            ];
                            $menorarboles = 100;
                            $menordimensiones = 100;
                            if ($agrupar == 1) {
                                $arrayGrupos = $this->construirArbolgrupos($model->arbol);
                                foreach ($arrayGrupos as $key => $grupo) {
                                    $consulta = $ejecucionformulario->getDatabygrafgrupo(implode(',', $grupo['hijos']), $model->dimension, $model->fecha, $metrica, $model);
                                    if (count($consulta) > 0) {
                                        //foreach ($consulta as $value) {
                                        $arbolPadre = \app\models\Arboles::findOne($key);
                                        $promedio = ($consulta[0]['promedio'] * 100);
                                        $menorarboles = ($promedio < $menorarboles) ? $promedio : $menorarboles;
                                        $datos['arbol'][] = ['name' => $arbolPadre->name, 'data' => [(double) round($promedio, 2)]];
                                        $datos['count'][] = ['name' => $arbolPadre->name, 'data' => [(int) $consulta[0]['total']]];
                                    }
                                }
                            } else {
                                $consulta = $ejecucionformulario->getDatabygraf($model->arbol, $model->dimension, $model->fecha, $metrica, true, $model, "persona");
                                if (count($consulta) > 0) {
                                    foreach ($consulta as $value) {
                                        $arbolPadre = \app\models\Arboles::findOne($value['arbol_id']);
                                        $promedio = ($value['promedio'] * 100);
                                        $menorarboles = ($promedio < $menorarboles) ? $promedio : $menorarboles;
                                        $datos['arbol'][] = ['name' => $arbolPadre->name, 'data' => [(double) round($promedio, 2)]];
                                        $datos['count'][] = ['name' => $arbolPadre->name, 'data' => [(int) $value['total']]];
                                    }
                                }
                            }
                            if (count($consulta) > 0) {
                                if ($consulta[0]['total'] != 0) {
                                    $arrayTemp = [];
                                    foreach ($datos['arbol'] as $valueArbol) {
                                        $arrayTemp[] = [$valueArbol['name'], $valueArbol['data'][0]];
                                    }
                                    $datos['arbol2'][] = ['name' => 'Total', 'colorByPoint' => true, 'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx];
                                    $arrayTemp = [];
                                    foreach ($datos['count'] as $valueContador) {
                                        $arrayTemp[] = [$valueContador['name'], $valueContador['data'][0]];
                                    }
                                    $datos['count2'][] = ['name' => 'Total', 'colorByPoint' => true, 'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx];
                                    $consulta1 = $ejecucionformulario->getDatabygraf($model->arbol, $model->dimension, $model->fecha, $metrica, false, $model, "persona");
                                    foreach ($consulta1 as $value) {
                                        $arbolPadre = \app\models\Dimensiones::findOne($value['dimension_id']);
                                        $promedio = ($value['promedio'] * 100);
                                        $menordimensiones = ($promedio < $menordimensiones) ? $promedio : $menordimensiones;
                                        $datos['dimension'][] = ['name' => $arbolPadre->name, 'data' => [(double) round($promedio, 2)]];
                                    }
                                    $arrayTemp = [];
                                    foreach ($datos['dimension'] as $valueDimension) {
                                        $arrayTemp[] = [$valueDimension['name'], $valueDimension['data'][0]];
                                    }
                                    $datos['dimension2'][] = ['name' => 'Total', 'colorByPoint' => true, 'data' => $arrayTemp, 'dataLabels' => $arrayLabelsx];
                                    $data->showGraf = true;
                                    $data->countCantidaArboles = count($datos['arbol']);
                                    $data->infoArbol['datos'] = (isset($datos['arbol2'])) ? $datos['arbol2'] : '';
                                    $data->infoArbol['categoria'] = $fechas;
                                    $data->infoDimension['datos'] = (isset($datos['dimension2'])) ? $datos['dimension2'] : '';
                                    $data->infoDimension['categoria'] = $fechas;
                                    $data->infoArbolTotal['datos'] = (isset($datos['count2'])) ? $datos['count2'] : '';
                                    $data->menorArbol = $menorarboles - 5;
                                    $data->menorDimension = $menordimensiones - 5;
                                    /* Fin construccion de graficas */
                                    $data->datosTabla = $this->construirTabla($model->fecha, Yii::$app->user->identity->id, $model->corte, $metrica, $model->dimension, $model->arbol, $model, $form, "persona");

                                    $tempDimension = explode(',', $model->dimension);
                                    $data->totalDimension = count($tempDimension);
                                    $data->metricaSelecc = $metrica;
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
                           
                        } else {
                            
                            /* if (count($idsArboles) == 0) {
                              $msg = \Yii::t('app', 'Seleccione un arbol');
                              Yii::$app->session->setFlash('danger', $msg);
                              } else { */
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
                            $metricas = \app\models\Metrica::find()->where("id IN(" . $model->ids_metricas . ")")->asArray()->all();
                            $metrica = $this->validarMetrica($metricas[0]['id']);
                            $consulta = $ejecucionformulario->getDatabygrafexcelpersona($model->ids_arboles, $model->ids_dimensiones, $model->rango_fecha, $metrica, $model);
                            if (count($consulta) > 0) {
                                $data->datosTabla = $this->construirTabla($model->rango_fecha, Yii::$app->user->identity->id, $model->corte_id, $model->ids_metricas, $model->ids_dimensiones, $model->ids_arboles, $model, $form, "persona");
                                $tablaExcel = $this->actionGenerartablaexcel($data->datosTabla['datos'], $model->ids_dimensiones, $data->datosTabla['total'], "persona");
                                $this->generarExcel($tablaExcel, $data->datosTabla['cortes'], $model->ids_dimensiones, $model->ids_metricas, false, Yii::$app->user->identity->id, $model->corte_id, "persona");
                            } else {
                                $data->showGraf = false;
                                $msg = \Yii::t('app', 'No se encontraron datos para los filtros ingresados');
                                Yii::$app->session->setFlash('danger', $msg);
                            }
                        }
                    }
                }
                $data->arboles[] = $this->getRecursivearbolscopia('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', 'arbol_ids', $arrArboles);
                $data->arboles[] = $this->getRecursivearbolscopia('tbl_arbols', 'id', 'name', 'arbol_id', 0, '-', 'arbol_idsDetallada', $arrArboles);
                $data->metrica = ArrayHelper::map(
                                \app\models\Metrica::find()->limit(10)->asArray()->all()
                                , 'id', 'detexto');
                $data->metrica[] = 'Score';
                return $this->render('indexpersona', ['data' => $data, 'model' => $model, '']);
            }

            /**
             * Obtiene el listado de equipos de valoradores (grupos)
             * @param type $search
             * @param type $id
             */
            public function actionEquiposlistvaloradores($search = null, $id = null) {
                if (!Yii::$app->getRequest()->isAjax) {
                    return $this->goHome();
                }

                $out = ['more' => false];
                if (!is_null($search)) {
                    $data = \app\models\GruposusuariosSearch::find()
                            ->select('tbl_grupos_usuarios.grupos_id AS id,UPPER(tbl_grupos_usuarios.nombre_grupo) AS text,tbl_grupos_usuarios.*,rg.*')
                            //->select(['id' => 'tbl_grupos_usuarios.grupos_id', 'text' => 'UPPER(tbl_grupos_usuarios.nombre_grupo)'])
                            ->join('INNER JOIN', 'rel_grupos_usuarios rg', 'rg.grupo_id = tbl_grupos_usuarios.grupos_id')
                            ->where('tbl_grupos_usuarios.nombre_grupo LIKE "%' . $search . '%"')
                            ->andWhere('rg.usuario_id = ' . Yii::$app->user->identity->id)
                            ->orderBy('tbl_grupos_usuarios.nombre_grupo')
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } elseif (!empty($id)) {
                    $data = \app\models\GruposusuariosSearch::find()
                            ->select('tbl_grupos_usuarios.grupos_id AS id,UPPER(tbl_grupos_usuarios.nombre_grupo) AS text,tbl_grupos_usuarios.*,rg.*')
                            //->select(['id' => 'tbl_grupos_usuarios.grupos_id', 'text' => 'UPPER(nombre_grupo)'])
                            ->join('INNER JOIN', 'rel_grupos_usuarios rg', 'rg.grupo_id = tbl_grupos_usuarios.grupos_id')
                            ->where('tbl_grupos_usuarios.grupos_id IN (' . $id . ')')
                            ->andWhere('rel_grupos_usuarios.usuario_id = ' . Yii::$app->user->identity->id)
                            ->asArray()
                            ->all();
                    $out['results'] = array_values($data);
                } else {
                    $out['results'] = ['id' => 0, 'text' => Yii::t('app', 'No matching records found')];
                }
                echo \yii\helpers\Json::encode($out);
            }

        }
        