<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\BaseSatisfaccionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'valoracion escaladas');

//$this->params['breadcrumbs'][] = $this->title;
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
        
if (!isset($aleatorio) || !$aleatorio) {
    $aleatorio = false;
}
?>
<div class="base-satisfaccion-index">
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>

    <!--<div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>-->

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                        var id=$(element).val();
                        if (id !== "") {
                            $.ajax("' . Url::to(['basesatisfaccion/getarbolesbypcrc']) . '?id=" + id, {
                                dataType: "json",
                                type: "post"
                            }).done(function(data) { callback(data.results[0]);});
                        }
                    }')
                        ]
                            ]
            );
            ?>            
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'fecha', [
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-4">{label}</div>'
                . '<div class="col-md-8"><div class="input-group">'
                . '<span class="input-group-addon" id="basic-addon1">'
                . '<i class="glyphicon glyphicon-calendar"></i>'
                . '</span>{input}</div>{error}{hint}</div>',
                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                'options' => ['class' => 'drp-container form-group']
            ])->widget(DateRangePicker::classname(), [
                'useWithAddon' => true,
                'convertFormat' => true,
                'presetDropdown' => true,
                'readonly' => 'readonly',
                'useWithAddon' => true,
                'pluginOptions' => [
                    'autoApply' => true,
                    'clearBtn' => true,
                    //'useWithAddon'=>true,
                    'timePicker' => false,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'left'
                ],
                'pluginEvents' => [
                //'cancel.daterangepicker'=>"function(ev, picker) { $('#basesatisfaccionsearch-fecha').val('');}"
                ]
            ]);
            ?>
        </div>        
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['usuariolist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                    var id=$(element).val();
                    if (id !== "") {
                        $.ajax("' . Url::to(['usuariolist']) . '?id=" + id, {
                            dataType: "json",
                            type: "post"
                        }).done(function(data) { callback(data.results[0]);});
                    }
                }')
                        ]
                            ]
            );
            ?>            
        </div>
        <div class="col-md-6">
            <?=
                    $form->field($model, 'estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->dropDownList($model->estadosList(), ['prompt' => Yii::t('app', 'Select ...')]);
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($model, 'evaluado_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['usuariolist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                    var id=$(element).val();
                    if (id !== "") {
                        $.ajax("' . Url::to(['usuariolist']) . '?id=" + id, {
                            dataType: "json",
                            type: "post"
                        }).done(function(data) { callback(data.results[0]);});
                    }
                }')
                        ]
                            ]
            );
            ?>            
        </div>
        <div class="col-md-6">
            <?=
                    $form->field($model, 'usua_id_lider', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...'),],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/lidereslist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/lidereslist']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results[0]);});
                                }
                            }')
                        ]
                            ]
            );
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?php
            //agregar escalado por
            ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'dimension_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->getDimensionsList(), ['prompt' => 'Seleccione ...'])
            ?>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-primary'])
            ?>           
        </div>        
    </div>
    <?php ActiveForm::end(); ?>

    <?php
    echo GridView::widget([
        'dataProvider' => $dataProvider,
        //'filterModel' => $model,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\ActionColumn',
                                        'template' => '{preview}{update}{delete}',
                                        'buttons' => [
                                            'preview' => function ($url, $model) use ($aleatorio) {
                                                $ejecucion = \app\models\Tmpejecucionformularios::findOne(["id" => $model["id"]]);
                                                $fecha = date('Y-m-d H:i:s');
                                                $nuevafecha = strtotime('-2 month', strtotime($fecha));
                                                $nuevafecha = date('Y-m-d H:i:s', $nuevafecha);
                                                if (isset($ejecucion->basesatisfaccion_id)) {
                                                    $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                                                    
                                                }
                                                if ($model['created'] >= $nuevafecha) {
                                                    if ($ejecucion->basesatisfaccion_id == '' || empty($ejecucion->basesatisfaccion_id) || is_null($ejecucion->basesatisfaccion_id)) {
                                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>'
                                                                        , Url::to(['formularios/verformulariodiligenciadoescalado'
                                                                            , 'tmp_id' => $model["id"], 'aleatorio'=> $aleatorio]), [
                                                                    'title' => Yii::t('yii', 'ver formulario'),
                                                                    'target' => "_blank"
                                                        ]);
                                                    } else {
                                                        //if ($modelBase->estado == "Cerrado") {
                                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                                            , 'basesatisfaccion_id' => $modelBase->id, 'preview' => 1, 'fill_values' => true,'view' => 'formularios/indexescaladosenviados', 'banderaescalado' => true, 'idtmp' => $model["id"], 'aleatorio'=> $aleatorio]), [
                                                                    'title' => Yii::t('yii', 'ver formulario'),
                                                                    'target' => "_blank"
                                                        ]);
                                                        //}
                                                    }
                                                }
                                            },
                                                    'update' => function ($url, $model) use ($aleatorio) {
                                                $fecha = date('Y-m-d H:i:s');
                                                $nuevafecha = strtotime('-2 month', strtotime($fecha));
                                                $nuevafecha = date('Y-m-d H:i:s', $nuevafecha);
                                                $ejecucion = \app\models\Tmpejecucionformularios::findOne(["id" => $model["id"]]);
                                                if (isset($ejecucion->basesatisfaccion_id)) {
                                                    $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                                                }
                                                if ($model['created'] >= $nuevafecha) {
                                                    if ($ejecucion->basesatisfaccion_id == '' || empty($ejecucion->basesatisfaccion_id) || is_null($ejecucion->basesatisfaccion_id)) {

                                                        if (Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->id == $model['usua_id'] || Yii::$app->user->identity->isModificarMonitoreo()) {
                                                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>'
                                                                            , Url::to(['formularios/editarformulariodiligenciadoescalado'
                                                                                , 'tmp_id' => $model["id"], 'aleatorio'=> $aleatorio]), [
                                                                        'title' => Yii::t('yii', 'Update'),
                                                                        'target' => "_blank",
                                                            ]);
                                                        }
                                                    } else {
                                                        if ((Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->id == $model['usua_id'] || Yii::$app->user->identity->isModificarMonitoreo())) {
                                                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                                                , 'basesatisfaccion_id' => $modelBase->id, 'preview' => 0, 'fill_values' => false,'view' => 'formularios/indexescaladosenviados', 'banderaescalado' => true, 'idtmp' => $model["id"], 'aleatorio'=> $aleatorio]), [
                                                                        'title' => Yii::t('yii', 'Update'),
                                                                        'target' => "_blank"
                                                            ]);
                                                        }
                                                    }
                                                }
                                            },
                                                    'delete' => function ($url, $model) {
                                                //ENLACE PARA BORRAR VALORACIONES                   
                                                if (in_array(Yii::$app->user->identity->id, Yii::$app->params["idUsersDelete"])) {
                                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['formularios/borrarformulariodiligenciadoescalado',
                                                                        'tmp_id' => $model["id"]]), [
                                                                'title' => Yii::t('yii', 'delete'),
                                                                'data-pjax' => 'w0',
                                                                'onclick' => "
                                                if (confirm('"
                                                                . Yii::t('app', 'Are you sure '
                                                                        . 'you want to delete '
                                                                        . 'this item?') . "')) {                                                            
                                                    return true;
                                                }else{
                                                    return false;
                                                }",
                                                                    ]
                                                    );
                                                }
                                            },
                                                ]
                                            ],
                //'id',
                                                        [
                    'attribute' => 'id',
                    'enableSorting' => false,
                ],
                //'identificacion',
                //'created',
                [
                    'attribute' => 'created',
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'arbol_id',
                    'value' => 'cliente0.name',
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'evaluado_id',
                    'value' => 'evaluado.name',
                    'enableSorting' => false,
                ],
                [
                    'attribute' => 'Lider',
                    'value' => 'lider.usua_nombre'
                ], 
                
                [
                    'attribute' => 'descripcion',
                    'enableSorting' => false,
                ],
                [
                    'format' => 'html',
                    'attribute' => 'estado',
                    'value' => function($data) {
                    return $data->getEstados($data->estado);
                    }
                ],
                [
                    'header' => 'Escalado por',
                    'attribute' => 'valorador_inicial.usua_nombre',
                ],                   
            ],
        ]);
        ?>
</div>

