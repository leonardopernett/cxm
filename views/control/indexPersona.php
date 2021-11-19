<?php

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Control persona');
$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Url::to("@web/js/FusionCharts.js"), ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile(Url::to("@web/js/jquery.bonsai.js"));
$this->registerJsFile(Url::to("@web/js/jquery.qubit.js"));
$this->registerCssFile(Url::to("@web/css/jquery.bonsai.css"))
?>

<?php
$template = '<div class="row"><div class="col-md-3">{label}</div><div class="col-md-9">'
        . ' {input}{error}{hint}</div></div>';
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='tooltip']").tooltip(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
?>
<?php
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);

?>
<div class="container-fluid">
    
    <!-- DIV PARA INICIO DE GRÁFICAS -->
    <div class="row">
        <div class="col-md-2 well" style="margin-right: 10px; text-align: center;">
            <?=
            Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                        "class" => "glyphicon glyphicon-chevron-down",
                    ]) . " " . Yii::t('app', 'VISTA UNICA'), "javascript:void(0)"
                    , ["class" => "openVistas", "id" => "graficar"])
            ?>
        </div>
        <div class="col-md-2 well" style="margin-right: 10px; text-align: center">
            <?=
            Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                        "class" => "glyphicon glyphicon-chevron-down",
                    ]) . " " . Yii::t('app', 'VISTA DETALLADA'), "javascript:void(0)"
                    , ["class" => "openVistas", "id" => "graficarDetallada"])
            ?>
        </div>
    </div>
    
    <div class="row">
        <div class="col-lg-12">

            <?php
            foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
            }
            ?> 
            <?php if ($data->showGraf): ?>
                <div id="divConfig" class="row" style="z-index:2; display: block" title="Parametros de Visualizaci&oacute;n" >
            <?php else: ?>
                <div id="divConfig" class="row" style="z-index:2; display: none" title="Parametros de Visualizaci&oacute;n" >
            <?php endif; ?>
                
                <a href="javascript:void(0)" id="show-filter">
                    <span class="glyphicon glyphicon-search" aria-hidden="true"></span>
                    Mostrar filtros
                </a>
                <?php if ($data->showGraf): ?>
                    <div class="well" style="display: none" id="filtrosControlproceso">
                <?php else: ?>
                    <div class="well" style="display: block" id="filtrosControlproceso">
                <?php endif; ?>
                <?php
                $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['id' => 'idVistaunicapersona']]);
                ?>
                <?= Html::input("hidden", "form", "0", ['id' => 'form']); ?>
                
                <!-- SELECCIONE GRÁFICAS -->
                <div class="col-md-12" id="divConfigChart" style="margin-bottom: 20px;">
                    <div class="row">
                        <div class="col-md-1">
                            <label style="margin-left: 20px;"><?= Yii::t('app', 'Opciones de Gráficas'); ?></label>
                        </div>
                        <div class="col-md-10 tipo_grafica">
                            <?=
                                $form->field($model, 'tipo_grafica')
                                ->radioList(
                                        array(
                                            'agru_dimen' => Html::img("@web/images/chart1.png", [
                                                "class" => "img-thumbnail img-check",
                                                "data-toggle" => "tooltip",
                                                "data-placement" => "top",
                                                "title" => Yii::t('app', 'Grafica dimension agrupada')
                                            ]),
                                            'sepa_dimen' => Html::img("@web/images/chart2.png", [
                                                "class" => "img-thumbnail img-check",
                                                "data-toggle" => "tooltip",
                                                "data-placement" => "top",
                                                "title" => Yii::t('app', 'Grafica dimension separada')
                                            ]),
                                            'tendencia' => Html::img("@web/images/chart3.png", [
                                                "class" => "img-thumbnail img-check",
                                                "data-toggle" => "tooltip",
                                                "data-placement" => "top",
                                                "title" => Yii::t('app', 'Grafica tendencia')
                                            ]),
                                        )
                                )
                                ->label(false);
                            ?>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="form-group">
                        <?php
                        echo $form->field($model, 'dimension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->widget(Select2::classname(), [
                            'language' => 'es',
                            'name' => 'dimension',
                            'options' => [
                                'placeholder' => Yii::t('app', 'Select ...'),
                                'id' => 'dimension'
                            ],
                            'pluginOptions' => [
                                'multiple' => true,
                                'allowClear' => true,
                                'maximumSelectionSize' => 3,
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['dimensionlistmultiple']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                ],
                                'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['dimensionlistmultiple']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                            ]
                        ]);
                        ?>

                    </div>
                    <div class="form-group">
                        <?php
                        echo $form->field($model, 'fecha', [
                            //'addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>']],                 
//                'inputTemplate' => '<div class="input-group col-md-12">'
//                . '<span class="input-group-addon">'
//                . '<i class="glyphicon glyphicon-calendar"></i>'
//                . '</span>{input}{error}{hint}</div>',
                            'labelOptions' => ['class' => 'col-md-12'],
                            'template' => '<div class="row"><div class="col-md-3">{label}</div>'
                            . '<div class="col-md-9"><div class="input-group">'
                            . '<span class="input-group-addon" id="basic-addon1">'
                            . '<i class="glyphicon glyphicon-calendar"></i>'
                            . '</span>{input}</div>{error}{hint}</div></div>',
                            'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                            'options' => ['class' => 'drp-container form-group']
                        ])->widget(DateRangePicker::classname(), [
                            'model' => $model,
                            'name' => 'fecha',
                            'id' => 'fecha',
                            'useWithAddon' => true,
                            'convertFormat' => true,
                            'presetDropdown' => true,
                            'readonly' => 'readonly',
                            'pluginOptions' => [
                                'timePicker' => false,
                                //'timePickerIncrement' => 15,
                                'format' => 'Y-m-d',
                                'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                'endDate' => date("Y-m-d"),
                                'opens' => 'center'
                        ]]);
                        ?>
                    </div>
                    <div class="form-group">
                        <?php
                        echo $form->field($model, 'corte'
                                        , ['labelOptions' => ['class' => 'col-md-12']
                                    , 'template' => $template])
                                ->dropDownList(['3' => 'Dia', '1' => 'Semana', '2' => 'Mes']
                                        , ["class" => "form-control", "id" => "selCorte"]);
                        ?>
                        <div id="configCorte" style="display: none; float: right">
                            <?php
                            echo Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                        "class" => "glyphicon glyphicon-cog",
                                        "id" => "graficar"]) . ' Configurar Corte', 'javascript:void(0)', [
                                //'title' => Yii::t('app', 'Tbl Opcions'),
                                //'data-pjax' => '0',
                                'onclick' => "  
                                        var tipo = $('#selCorte').val();
                                        var fecha = $('#filtroscontrol-fecha').val();
                                        if(fecha ==''){
                                            alert('Por favor diligencie el campo de fecha para poder configurar los cortes');
                                            return;
                                        }
                                    $.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '" . Url::to(['indexcorte', 'tipo' => ""]) . "'+tipo+'&fecha='+fecha,
                                    success  : function(response) {
                                        $('#ajax_result').html(response);
                                    }
                                   });
                                   return false;",
                            ]);
                            ?>
                        </div>
                    </div>                                        
                    <div class="form-group">
                        <?=
                        $form->field($model, 'metrica', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($data->metrica
                                , ["class" => "form-control", "id" => "selMetrica"]);
                        ?>
                    </div>
                    <div class="form-group" id="divRol">
                        <?=
                                $form->field($model, 'rol', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                ->widget(Select2::classname(), [
                                    //'data' => array_merge(["" => ""], $data),
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'multiple' => true,
                                        'allowClear' => true,
                                        'minimumInputLength' => 3,
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['reportes/rollistmultiple']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                        'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/rollistmultiple']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                                    ]
                                        ]
                        );
                        ?>
                    </div>
                    <div class="form-group" id="divValorador">            
                        <?=
                                $form->field($model, 'valorador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                ->widget(Select2::classname(), [
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'multiple' => true,
                                        'allowClear' => true,
                                        'minimumInputLength' => 3,
                                        'ajax' => [
                                            'url' => Url::to(['reportes/usuariolist']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                        'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                                    ]
                                        ]
                        );
                        ?>
                    </div> 
                    <div class="form-group" id="divEquiposvalorador">
                        <?=
                                $form->field($model, 'equiposvalorador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                ->widget(Select2::classname(), [
                                    //'data' => array_merge(["" => ""], $data),
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'multiple' => true,
                                        'allowClear' => true,
                                        'minimumInputLength' => 3,
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['equiposlistvaloradores']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                        'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['equiposlistvaloradores']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                                    ]
                                        ]
                        );
                        ?>
                    </div>

                </div>
                <div class="col-md-8">

                    <div class="form-group">
                        <div class="control-group">
                            <label class="control-label col-md-2">
                                <?= Yii::t('app', 'Arbol'); ?>:
                            </label>
                        </div>
                        <div class="col-md-10">
                            <div id="lstArbol"  class="lstArbolClass">                          
                                <?php echo $data->arboles[0] ?>
                            </div>
                        </div>
                    </div>
                    <div class="form-group"> 
                        <div style="float: left; margin: 5px 10px 0 0;"> 
                            <?php
                            echo Html::tag('span', Html::img(Url::to("@web/images/Question.png")), [
                                'data-title' => 'Agrupar',
                                'data-content' => Yii::t('app', 'mensaje agrupar'),
                                'data-toggle' => 'popover',
                                'style' => 'cursor:pointer;'
                            ]);
                            ?>
                        </div>
                        <div class="col-md-3">
                            <label class="checkbox">
                                <?php echo Html::checkbox('agrupar', false, ['id' => 'agrupar']) ?>
                                <label for="agrupar" style="padding: 0; font-weight: bold"><?= Yii::t('app', 'Agrupar Arboles'); ?></label>                                
                            </label>
                        </div>                       
                    </div>                    
                </div>
                <div class="row">
                    <div class="btn-groupp col-md-12 text-center">
                            <?=
                            Html::submitButton(Yii::t('app', 'Graficar')
                                    , ['class' => 'btn btn-success'])
                            ?>
                    </div>

                </div>

                <?php ActiveForm::end(); ?>   
                </div>
                <?php if ($data->showGraf && $tipo_grafica == 'agru_dimen'): ?>
                <div class="row">                    
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo Yii::t('app', 'mensaje rango') ?>
                    </div>
                    <?php if(!$data->segundoCalifPer) : ?>
                    <div class="col-md-10" id="selecGrafica">
                        <div class="form-group">
                            <div class="col-md-12">                            
                                <?=
                                $form->field($model, 'selecGrafica', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(["0" => "Árbol", "1" => "Dimensión"]
                                        , ["class" => "form-control", "id" => "selecGrafica"]);
                                ?>
                            </div>
                        </div>
                    </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
            
        <?php if ($data->showGraf): ?>
                <?php if ($tipo_grafica == 'agru_dimen'): ?>
                <?php if(!$data->segundoCalifPer) : ?>
                <div id="divArbolGraph" class="row col-md-<?php echo ($data->countCantidaArboles < 10) ? "6" : "12" ?>" style="z-index:1;display: block">
                    <div class="col-md-12">
                        <div class="border-graph">
                            <?php
                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],                            
                                'options' => [

                                    'chart' => [
                                        'type' => 'column',
                                        'zoomType' => 'xy'                                
                                    ],
                                    'colors' => [
                                        "#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9",
                                        "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1",
                                        "#b25042", "#ede2c2", "#8997a9", "#9d74bd", "#589b61",
                                        "#c9cdd0", "#4a624a", "#6a5848", "#efadb1", "#d7e782",
                                        "#fcff00", "#000cff", "#b50000", "#ff00c6", "#00fcff",
                                        "#00ff0c", "#af9d8b", "#cbe2f3", "#3c76b7", "#7d864f",
                                    ],
                                    'plotOptions' => [
                                        'series' => [
                                            'dataLabels' => [
                                                'enabled' => true,
                                                'format' => '{point.y:.1f}%'
                                            ]
                                        ]
                                    ],
                                    'legend' => [
                                        'enabled' => false
                                    ],
                                    'tooltip' => [
                                        'enabled' => true
                                    ],
                                    'title' => [
                                        'text' => 'Promedio por ' . Yii::t('app', $data->metricaSelecc),
                                    ],
                                    'xAxis' => [
                                        'type' => 'category',
                                        'labels' => [
                                            'style' => [
                                                'fontSize' => '10px',
                                                'fontFamily' => 'Verdana, sans-serif'
                                            ]
                                        ]
                                    ],
                                    'yAxis' => [
                                        'title' => ['text' => 'Promedio por ' . Yii::t('app', $data->metricaSelecc)],
                                    ],
                                    'series' => $data->infoArbol['datos'],
                                ]
                            ]);
                            ?>
                        </div>    
                    </div>
                </div>
                <?php endif; ?>        
                <div id="divDimGraph" class="row col-md-<?php echo ($data->countCantidaArboles < 10) ? "6" : "12" ?>" style="z-index:1;display: none">
                    <div class="col-md-12">
                        <div class="border-graph">
                            <?php
                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],                            
                                'options' => [
                                    'colors' => [
                                        "#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9",
                                        "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1",
                                        "#b25042", "#ede2c2", "#8997a9", "#9d74bd", "#589b61",
                                        "#c9cdd0", "#4a624a", "#6a5848", "#efadb1", "#d7e782",
                                        "#fcff00", "#000cff", "#b50000", "#ff00c6", "#00fcff",
                                        "#00ff0c", "#af9d8b", "#cbe2f3", "#3c76b7", "#7d864f",
                                    ],
                                    'chart' => [
                                        'type' => 'column',
                                        'zoomType' => 'xy'                                
                                    ],
                                    'legend' => [
                                        'enabled' => false
                                    ],
                                    'tooltip' => [
                                        'enabled' => true
                                    ],
                                    'plotOptions' => [
                                        'series' => [
                                            'dataLabels' => [
                                                'enabled' => true,
                                                'format' => '{point.y:.1f}%'
                                            ]
                                        ]
                                    ],
                                    'title' => [
                                        'text' => 'Promedio por ' . Yii::t('app', $data->metricaSelecc),
                                    ],
                                    'xAxis' => [
                                        'type' => 'category',
                                        'labels' => [
                                            'style' => [
                                                'fontSize' => '10px',
                                                'fontFamily' => 'Verdana, sans-serif'
                                            ]
                                        ]
                                    /* 'categories' => $data->infoDimension['categoria'] */
                                    ],
                                    'yAxis' => [
                                        'title' => ['text' => 'Promedio por ' . Yii::t('app', $data->metricaSelecc)],
                                    //'min' => $data->menorDimension
                                    ],
                                    'series' => $data->infoDimension['datos'],
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
                <div id="divCountGraph" class="row col-md-<?php echo ($data->countCantidaArboles < 10) ? "6" : "12" ?>" style="z-index:1">
                    <div class="col-md-12">
                        <div class="border-graph">
                            <?php
                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],                            
                                'options' => [
                                    'colors' => [
                                        "#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9",
                                        "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1",
                                        "#b25042", "#ede2c2", "#8997a9", "#9d74bd", "#589b61",
                                        "#c9cdd0", "#4a624a", "#6a5848", "#efadb1", "#d7e782",
                                        "#fcff00", "#000cff", "#b50000", "#ff00c6", "#00fcff",
                                        "#00ff0c", "#af9d8b", "#cbe2f3", "#3c76b7", "#7d864f",
                                    ],
                                    'chart' => [
                                        'type' => 'column',
                                        'zoomType' => 'xy'                                
                                    ],
                                    'legend' => [
                                        'enabled' => false
                                    ],
                                    'tooltip' => [
                                        'enabled' => true
                                    ],
                                    'title' => [
                                        'text' => 'Cantidad',
                                    ],
                                    'xAxis' => [
                                        'type' => 'category',
                                        'labels' => [
                                            'style' => [
                                                'fontSize' => '10px',
                                                'fontFamily' => 'Verdana, sans-serif'
                                            ]
                                        ]
                                    ],
                                    'yAxis' => [
                                        'title' => ['text' => 'Cantidad']
                                    ],
                                    'series' => $data->infoArbolTotal['datos'],
                                ]
                            ]);
                            ?>
                        </div>    
                    </div>
                </div>
                <?php 
                $arrTablaAgruDimen =[];            
                foreach ($data->infoArbol['datos'][0]['data'] as $value) {
                    
                    if (!array_key_exists($value[0], $arrTablaAgruDimen)) {
                        $arrTablaAgruDimen[$value[0]][] = $value[1] . " %";    
                    }else{
                        array_push($arrTablaAgruDimen[$value[0]], $value[1] . " %");
                    }
                }
                foreach ($data->infoArbolTotal['datos'][0]['data'] as $value) {
                   
                    if (!array_key_exists($value[0], $arrTablaAgruDimen)) {
                        $arrTablaAgruDimen[$value[0]][] = $value[1];    
                    }else{
                        array_push($arrTablaAgruDimen[$value[0]], $value[1]);
                    }
                }
                ?>
                    <!--tabla de datos-->
                    <br/>
                    <br/>
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered " style="margin-top: 20px;">
                        <caption>Datos</caption>
                            <tr>
                                <th scope="col"><?= Yii::t('app', 'Arbol ID'); ?></th>
                                <?php if(!$data->segundoCalifPer) : ?>
                                <th scope="col"><?= Yii::t('app', 'Resultado'); ?></th> 
                                <?php endif; ?>
                                <th scope="col"><?= Yii::t('app', 'Cantidad'); ?></th>                                
                            </tr>
                            <?php foreach ($arrTablaAgruDimen as $key => $value): ?>                                
                                <tr>   
                                    <th scope="row"><?= $key; ?></th>
                                    <?php  foreach ($value as $key2 => $val): ?>  
                                        <?php if($data->segundoCalifPer) : ?>
                                            <?php if($key2 == 0){ continue; } ?>
                                        <?php endif; ?>
                                        <td><?= $val; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>            
                <!--3 grafica de los puntos-->                
                <?php if ($tipo_grafica == 'tendencia'): ?>  
                <div class="row">                    
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo Yii::t('app', 'mensaje rango') ?>
                    </div>
                 </div>
                <?php if(!$data->segundoCalifPer) : ?>
                    <div class="col-md-6">
                        <div class="border-graph">
                            <?php                            
                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],                            
                                'options' => [
                                    'colors' => [
                                        "#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9",
                                        "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1",
                                        "#b25042", "#ede2c2", "#8997a9", "#9d74bd", "#589b61",
                                        "#c9cdd0", "#4a624a", "#6a5848", "#efadb1", "#d7e782",
                                        "#fcff00", "#000cff", "#b50000", "#ff00c6", "#00fcff",
                                        "#00ff0c", "#af9d8b", "#cbe2f3", "#3c76b7", "#7d864f",
                                    ],
                                    'chart' => [
                                        //'type' => 'column',
                                        'zoomType' => 'xy'                                
                                    ],
                                    'legend' => [
                                        'enabled' => true
                                    ],
                                    'tooltip' => [
                                        'enabled' => true
                                    ],
                                    'plotOptions' => [
                                        'series' => [
                                            'dataLabels' => [
                                                'enabled' => true,
                                                'format' => '{point.y:.1f}%'
                                            ]
                                        ]
                                    ],
                                    'title' => [
                                        'text' => 'Promedio por ' . Yii::t('app', $data->metricaSelecc),
                                    ],
                                    'xAxis' => [
                                        'title' => [
                                            'text' => 'Corte'
                                        ],
                                        'categories' => $data->dataX
                                    ],
                                    'yAxis' => [
                                        'title' => [
                                            'text' => 'Promedio por ' . Yii::t('app', $data->metricaSelecc)                                      
                                        ],
                                        'plotLines' => [
                                            ['value' => '0','width'=>1, 'color'=>'#808080' ]
                                        ]
                                    ],
                                    'series' => $data->dataY,
                                ]
                            ]);
                            ?>
                        </div>                                
                    </div>     
                <?php endif; ?>
                    <div class="col-md-6">
                        <div class="border-graph">
                            <?php                                                    
                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],                            
                                'options' => [
                                    'colors' => [
                                        "#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9",
                                        "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1",
                                        "#b25042", "#ede2c2", "#8997a9", "#9d74bd", "#589b61",
                                        "#c9cdd0", "#4a624a", "#6a5848", "#efadb1", "#d7e782",
                                        "#fcff00", "#000cff", "#b50000", "#ff00c6", "#00fcff",
                                        "#00ff0c", "#af9d8b", "#cbe2f3", "#3c76b7", "#7d864f",
                                    ],
                                    'chart' => [
                                        'zoomType' => 'xy'                                
                                    ],
                                    'legend' => [
                                        'enabled' => true
                                    ],
                                    'tooltip' => [
                                        'enabled' => true
                                    ],         
                                    'plotOptions' => [
                                        'series' => [
                                            'dataLabels' => [
                                                'enabled' => true,                                            
                                            ]
                                        ],
                                        'fillOpacity' => 0.4
                                    ],
                                    'title' => [
                                        'text' => 'Cantidad',
                                    ],
                                    'xAxis' => [
                                        'title' => [
                                            'text' => 'Corte'
                                        ],
                                        'categories' => $data->dataX
                                    ],
                                    'yAxis' => [
                                        'title' => [
                                            'text' => 'Cantidad'                                        
                                        ],
                                        'plotLines' => [
                                            ['value' => '0','width'=>1, 'color'=>'#808080' ]
                                        ]
                                    ],
                                    'series' => $data->dataYCont,                                
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                    <!--tabla de datos-->
                    <br/>
                    <div class="col-md-12">
                    <table class="table table-striped table-bordered ">
                    <caption>Datos</caption>
                        <tr>
                            <th scope="col">&nbsp;</th> 
                            <th scope="col">&nbsp;</th>
                            <?php foreach ($data->dataXtabla as $valueCorte) : ?>
                            <th scope="col"><?= $valueCorte ?></th>
                            <?php endforeach; ?>                                                        
                        </tr>     
                        <?php if(!$data->segundoCalifPer) : ?>
                        <?php foreach ($data->dataY as $key => $value) : ?>
                        <tr>
                            <?php if($key == 0): ?>
                            <td rowspan="<?= count($data->dataY) ?>">Porcentajes</td>    
                            <?php endif; ?>
                            <td><?= $value['name'] ?></td>
                            <?php foreach ($value['data'] as $valueData) : ?>
                            <td><?= (!is_null($valueData)) ? $valueData . " %" : " - " ?></td>
                            <?php endforeach; ?>                            
                        </tr>                          
                        <?php endforeach; ?>
                        <?php endif; ?>
                        <?php foreach ($data->dataYCont as $key => $value) : ?>
                        <tr>
                            <?php if($key == 0): ?>
                            <td rowspan="<?= count($data->dataYCont) ?>">Cantidades</td>    
                            <?php endif; ?>
                            <td><?= $value['name'] ?></td>
                            <?php foreach ($value['data'] as $valueData) : ?>
                            <td><?= $valueData ?></td>
                            <?php endforeach; ?>                            
                        </tr>                          
                        <?php endforeach; ?>
                    </table>
                    </div>
                <?php endif; ?>
                <?php if ($tipo_grafica == 'sepa_dimen'): ?> 
                    <div class="row">                    
                    <div class="alert alert-warning alert-dismissible" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <?php echo Yii::t('app', 'mensaje rango') ?>
                    </div>
                 </div>
                 <?php if(!$data->segundoCalifPer) : ?>
                    <div class="col-md-6">
                        <div class="border-graph">
                            <?php 
                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],                            
                                'options' => [

                                    'chart' => [
                                        'type' => 'column',
                                        'zoomType' => 'xy'                                
                                    ],
                                    'colors' => [
                                        "#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9",
                                        "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1",
                                        "#b25042", "#ede2c2", "#8997a9", "#9d74bd", "#589b61",
                                        "#c9cdd0", "#4a624a", "#6a5848", "#efadb1", "#d7e782",
                                        "#fcff00", "#000cff", "#b50000", "#ff00c6", "#00fcff",
                                        "#00ff0c", "#af9d8b", "#cbe2f3", "#3c76b7", "#7d864f",
                                    ],
                                    'plotOptions' => [
                                        'series' => [
                                            'dataLabels' => [
                                                'enabled' => true,
                                                'format' => '{point.y:.2f}%'
                                            ]
                                        ]
                                    ],
                                    'legend' => [
                                        'enabled' => true
                                    ],
                                    'tooltip' => [
                                        'enabled' => true
                                    ],
                                    'title' => [
                                        'text' => 'Promedio por ' . Yii::t('app', $data->metricaSelecc),
                                    ],
                                    'xAxis' => [
                                        'type' => 'category',
                                        'labels' => [
                                            'style' => [
                                                'fontSize' => '10px',
                                                'fontFamily' => 'Verdana, sans-serif'
                                            ]
                                        ],
                                        'categories' => $data->datosGrafica['arbolesEjeX'],
                                        'crosshair' => true
                                    ],
                                    'yAxis' => [
                                        'title' => ['text' => 'Promedio por ' . Yii::t('app', $data->metricaSelecc)],
                                    ],
                                    'series' => $data->datosGrafica['datosGrafiaSepaDimen_prom_graf_nueva'],
                                ]
                            ]);

                            ?>  
                        </div>
                    </div>   
                    <?php endif; ?>
                    <div class="col-md-6">
                        <div class="border-graph">
                            <?php     
                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],                            
                                'options' => [

                                    'chart' => [
                                        'type' => 'column',
                                        'zoomType' => 'xy'                                
                                    ],
                                    'colors' => [
                                        "#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9",
                                        "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1",
                                        "#b25042", "#ede2c2", "#8997a9", "#9d74bd", "#589b61",
                                        "#c9cdd0", "#4a624a", "#6a5848", "#efadb1", "#d7e782",
                                        "#fcff00", "#000cff", "#b50000", "#ff00c6", "#00fcff",
                                        "#00ff0c", "#af9d8b", "#cbe2f3", "#3c76b7", "#7d864f",
                                    ],
                                    'plotOptions' => [
                                        'series' => [
                                            'dataLabels' => [
                                                'enabled' => true,
                                                'format' => '{point.y}'
                                            ]
                                        ]
                                    ],
                                    'legend' => [
                                        'enabled' => true
                                    ],
                                    'tooltip' => [
                                        'enabled' => true
                                    ],
                                    'title' => [
                                        'text' => 'Cantidad',
                                    ],
                                    'xAxis' => [
                                        'type' => 'category',
                                        'labels' => [
                                            'style' => [
                                                'fontSize' => '10px',
                                                'fontFamily' => 'Verdana, sans-serif'
                                            ]
                                        ],
                                        'categories' => $data->datosGrafica['arbolesEjeX'],
                                        'crosshair' => true
                                    ],
                                    'yAxis' => [
                                        'title' => ['text' => 'Cantidad'],
                                    ],
                                    //'series' => $data->datosGrafica['datosGrafiaSepaDimen_cant'],
                                    'series' => $data->datosGrafica['datosGrafiaSepaDimen_cant_graf_nueva'],
                                ]
                            ]);                        
                            ?> 
                        </div>
                   </div>
                    <!--tabla de datos-->
                    <br/>
                    <br/>
                    <div class="col-md-12">
                        <table class="table table-striped table-bordered " style="margin-top: 20px;">
                        <caption>Datos</caption>
                            <tr>
                                <th scope="col"></th>
                                <?php $contResultados = 0; ?>
                                <?php  foreach ($data->datosGrafica['titulosTablaSepaDim'] as $val): ?>
                                    <?php                                     
                                    if($data->segundoCalifPer) {                                         
                                        $mystring = $val;
                                        $findme   = 'Resultado ';
                                        $pos = strpos($mystring, $findme);
                                        if($pos !== false){
                                            $contResultados++;
                                            continue;
                                        }
                                    }
                                    ?>
                                    <th scope="col"><?= $val; ?></th>
                                <?php endforeach; ?>
                            </tr>
                            <?php foreach ($data->datosGrafica['datosTablaSepaDim'] as $key => $value): ?>                                
                                <tr>      
                                    <?php
                                    if($contResultados > 0){
                                        for ($index = 0; $index < $contResultados; $index++) {
                                            unset($value[$index]);
                                        }
                                    }
                                    ?>
                                    <?php  foreach ($value as $val): ?>
                                        <?php 
                                        if($data->segundoCalifPer) { 
                                            $mystring2 = $val;
                                            $findme2   = '%';
                                            $pos2 = strpos($mystring2, $findme2);
                                            if($pos2 !== false){
                                                continue;
                                            }
                                        }
                                        ?>
                                        <td><?= $val; ?></td>
                                    <?php endforeach; ?>
                                </tr>
                            <?php endforeach; ?>
                        </table>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
            </div>
            
    </div>
</div>
<br>
<div class="row">
    <div class="col-lg-12">
        <div id="divConfigDetallada" class="well row" style="z-index:2; display: none" title="Parametros de Visualizaci&oacute;n" >
            <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['id' => 'idVistaDetalladapersona']]); ?>
            <?= Html::input("hidden", "form", "1", ['id' => 'form']); ?>
            <div class="col-md-12">
                <?php
                foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                    echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
                }
                Yii::$app->session->removeAllFlashes();
                ?>
            </div>                

            <div class="col-md-4">
                <div class="form-group">
                    <?php
                    echo $form->field($model, 'dimensionDetallada', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->widget(Select2::classname(), [
                        'language' => 'es',
                        'name' => 'dimensionDetallada',
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select ...'),
                            'id' => 'dimensionDetallada'
                        ],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'maximumSelectionLength' => 2,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['dimensionlistmultiple']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['dimensionlistmultiple']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                    ]);
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    echo $form->field($model, 'fechaDetallada', [
                        //'addon' => ['prepend' => ['content' => '<i class="glyphicon glyphicon-calendar"></i>']],                 
//                'inputTemplate' => '<div class="input-group col-md-12">'
//                . '<span class="input-group-addon">'
//                . '<i class="glyphicon glyphicon-calendar"></i>'
//                . '</span>{input}{error}{hint}</div>',
                        'labelOptions' => ['class' => 'col-md-12'],
                        'template' => '<div class="row"><div class="col-md-3">{label}</div>'
                        . '<div class="col-md-9"><div class="input-group">'
                        . '<span class="input-group-addon" id="basic-addon1">'
                        . '<i class="glyphicon glyphicon-calendar"></i>'
                        . '</span>{input}</div>{error}{hint}</div></div>',
                        'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                        'options' => ['class' => 'drp-container form-group']
                    ])->widget(DateRangePicker::classname(), [
                        'model' => $model,
                        'name' => 'fechaDetallada',
                        'id' => 'fechaDetallada',
                        'useWithAddon' => true,
                        'convertFormat' => true,
                        'presetDropdown' => true,
                        'readonly' => 'readonly',
                        'pluginOptions' => [
                            'timePicker' => false,
                            //'timePickerIncrement' => 15,
                            'format' => 'Y-m-d',
                            'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                            'endDate' => date("Y-m-d"),
                            'opens' => 'center'
                    ]]);
                    ?>
                </div>
                <div class="form-group">
                    <?php
                    echo $form->field($model, 'corteDetallada', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(['3' => 'Dia', '1' => 'Semana', '2' => 'Mes']
                            , ["class" => "form-control", "id" => "selCorteDetallado"]);
                    ?>
                    <div id="configCorteDetallado" style="display: none; float: right">
                        <div class="form-group">

                            <div class="col-md-12">                            
                                <?php
                                echo Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                            "class" => "glyphicon glyphicon-cog",
                                            "id" => "graficar"]) . ' Configurar Corte', 'javascript:void(0)', [
                                    //'title' => Yii::t('app', 'Tbl Opcions'),
                                    //'data-pjax' => '0',
                                    'onclick' => "  
                                        var tipo = $('#selCorte').val();
                                        var fecha = $('#filtroscontrol-fechadetallada').val();
                                        if(fecha ==''){
                                            alert('Por favor diligencie el campo de fecha para poder configurar los cortes');
                                            return;
                                        }
                                    $.ajax({
                                    type     :'POST',
                                    cache    : false,
                                    url  : '" . Url::to(['indexcorte', 'tipo' => ""]) . "'+tipo+'&fecha='+fecha,
                                    success  : function(response) {
                                        $('#ajax_result').html(response);
                                    }
                                   });
                                   return false;",
                                ]);
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php
                    echo $form->field($model, 'metricaDetallada', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->widget(Select2::classname(), [
                        'language' => 'es',
                        'name' => 'metricaDetallada',
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select ...'),
                            'id' => 'metricaDetallada'
                        ],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'maximumSelectionLength' => 2,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['metricalistmultipledetallada']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['metricalistmultiple']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                    ]);
                    ?>
                </div>


                <div class="form-group" id="divRolDetallada">
                    <?=
                            $form->field($model, 'rolDetallada', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                            ->widget(Select2::classname(), [
                                //'data' => array_merge(["" => ""], $data),
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                'pluginOptions' => [
                                    'multiple' => true,
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/rollistmultiple']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/rollistmultiple']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                                ]
                                    ]
                    );
                    ?>
                </div>
                <div class="form-group" id="divValoradorDetallada">            
                    <?=
                            $form->field($model, 'valoradorDetallada', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                'pluginOptions' => [
                                    'multiple' => true,
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'ajax' => [
                                        'url' => Url::to(['reportes/usuariolist']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                                ]
                                    ]
                    );
                    ?>
                </div> 
                <div class="form-group" id="divEquiposvaloradorDetallada">
                    <?=
                            $form->field($model, 'equiposvaloradorDetallada', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                            ->widget(Select2::classname(), [
                                //'data' => array_merge(["" => ""], $data),
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                'pluginOptions' => [
                                    'multiple' => true,
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['equiposlistvaloradores']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['equiposlistvaloradores']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                                ]
                                    ]
                    );
                    ?>
                </div>


                <div class="form-group">
                    <div class="col-md-12">
                        <a href="#" data-toggle="tooltip" data-placement="top" title="<?php echo Yii::t('app', 'envio correo control')?>">
                            <?php echo $form->field($model, 'guardar_filtro', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->checkbox() ?>
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="form-group">
                    <div class="control-group">
                        <label class="control-label col-md-2">
                            <?= Yii::t('app', 'Arbol'); ?>:
                        </label>
                    </div>
                    <div class="col-md-10">
                        <div id="lstArbol" class="lstArbolClass">                          
                            <?php echo $data->arboles[1] ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="btn-groupp col-md-12 text-center">
                        <?=
                        Html::submitButton(Yii::t('app', 'Generar Excel')
                                , ['class' => 'btn btn-success'])
                        ?>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>           
    </div>
</div>
</div>


<?php
echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">

    $(document).ready(function () {
<?php if (isset($data->banderaError)): ?>

    <?php if ($data->banderaError == 'vistaunica'): ?>
                $("#divConfig").show("slow");
    <?php else: ?>
                $("#divConfigDetallada").show("slow");
    <?php endif; ?>
<?php endif; ?>

        var valorIn = $('#selCorte').val();
        if (valorIn == 1 || valorIn == 2) {
            $("#configCorte").show('slow');
        } else {
            $("#configCorte").hide('slow');
        }
        $("#graficar").click(function () {
            $("#divConfig").toggle("slow");
            $("#divConfigDetallada").hide();
             $("#filtrosControlproceso").show("slow");
        });
        $("#graficarDetallada").click(function () {
            $("#divConfigDetallada").toggle("slow");
            $("#divConfig").hide();
            $("#filtrosControlproceso").hide("slow");
        });
        //jquery oara visualizar campos dependientes entre si
        $('#filtroscontrol-rol').change(function ()
        {
            if ($(this).val() == '') {
                $("#divValorador").show('slow');
                $("#divEquiposvalorador").show('slow');
            } else {
                $("#divValorador").hide('slow');
                $("#divEquiposvalorador").hide('slow');
            }
        });
        //jquery oara visualizar campos dependientes entre si
        $('#filtroscontrol-valorador').change(function ()
        {
            if ($(this).val() == '') {
                $("#divRol").show('slow');
                $("#divEquiposvalorador").show('slow');
            } else {
                $("#divRol").hide('slow');
                $("#divEquiposvalorador").hide('slow');
            }
        });
        //jquery oara visualizar campos dependientes entre si
        $('#filtroscontrol-equiposvalorador').change(function ()
        {
            if ($(this).val() == '') {
                $("#divRol").show('slow');
                $("#divValorador").show('slow');
            } else {
                $("#divRol").hide('slow');
                $("#divValorador").hide('slow');
            }
        });


        //jquery oara visualizar campos dependientes entre si
        $('#filtroscontrol-roldetallada').change(function ()
        {
            if ($(this).val() == '') {
                $("#divValoradorDetallada").show('slow');
                $("#divEquiposvaloradorDetallada").show('slow');
            } else {
                $("#divValoradorDetallada").hide('slow');
                $("#divEquiposvaloradorDetallada").hide('slow');
            }
        });
        //jquery oara visualizar campos dependientes entre si
        $('#filtroscontrol-valoradordetallada').change(function ()
        {
            if ($(this).val() == '') {
                $("#divRolDetallada").show('slow');
                $("#divEquiposvaloradorDetallada").show('slow');
            } else {
                $("#divRolDetallada").hide('slow');
                $("#divEquiposvaloradorDetallada").hide('slow');
            }
        });
        //jquery oara visualizar campos dependientes entre si
        $('#filtroscontrol-equiposvaloradordetallada').change(function ()
        {
            if ($(this).val() == '') {
                $("#divRolDetallada").show('slow');
                $("#divValoradorDetallada").show('slow');
            } else {
                $("#divRolDetallada").hide('slow');
                $("#divValoradorDetallada").hide('slow');
            }
        });
        if ($('#filtroscontrol-roldetallada').val() != '') {
            $("#divValoradorDetallada").hide();
            $("#divEquiposvaloradorDetallada").hide();
        }
        if ($('#filtroscontrol-valoradordetallada').val() != '') {
            $("#divRolDetallada").hide('slow');
            $("#divEquiposvaloradorDetallada").hide();
        }
        if ($('#filtroscontrol-equiposvaloradordetallada').val() != '') {
            $("#divRolDetallada").hide();
            $("#divValoradorDetallada").hide();
        }
        if ($('#filtroscontrol-rol').val() != '') {
            $("#divValorador").hide();
            $("#divEquiposvalorador").hide();
        }
        if ($('#filtroscontrol-valorador').val() != '') {
            $("#divRol").hide();
            $("#divEquiposvalorador").hide();
        }
        if ($('#filtroscontrol-equiposvalorador').val() != '') {
            $("#divRol").hide();
            $("#divValorador").hide();
        }
        $('#selCorte').change(function () {
            var val = $(this).val();
            if (val == 1 || val == 2) {
                $("#configCorte").show('slow');
            } else {
                $("#configCorte").hide('slow');
            }
        });
        $('#selCorteDetallado').change(function () {
            var val = $(this).val();
            if (val == 1 || val == 2) {
                $("#configCorteDetallado").show('slow');
            } else {
                $("#configCorteDetallado").hide('slow');
            }
        });

        $('select#selecGrafica').on('change', function () {
            var valor = $(this).val();
            if (valor == 0)
            {
                $("#divArbolGraph").show('slow');
                $("#divDimGraph").hide('slow');
            } else {
                $("#divArbolGraph").hide('slow');
                $("#divDimGraph").show('slow');

            }
        });

        $('#arbol_ids').bonsai({
            expandAll: false,
            checkboxes: true, // depends on jquery.qubit plugin
            createInputs: 'checkbox' // takes values from data-name and data-value, and data-name is inherited
        });
        $('#arbol_idsDetallada').bonsai({
            expandAll: false,
            checkboxes: true, // depends on jquery.qubit plugin
            createInputs: 'checkbox' // takes values from data-name and data-value, and data-name is inherited
        });
        
        $("#show-filter").click(function (){
            $("#filtrosControlproceso").toggle('fade');
        });

    });

    function fnForm2Array(strForm) {

        var arrData = new Array();

        $("select, textarea", $('#' + strForm)).each(function () {
            if ($(this).attr('name')) {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
            }
        });

        return arrData;

    }

    function fnForm2ArrayValidar(strForm) {

        var arrData = new Array();

        $("select", $('#' + strForm)).each(function () {
            if ($(this).attr('name')) {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
            }
        });

        return arrData;

    }
</script>