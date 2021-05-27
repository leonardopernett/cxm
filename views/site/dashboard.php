<?php

use yii\helpers\Html;
use yii\helpers\Url;
use miloschuman\highcharts\Highcharts;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
$this->title = Yii::t('app', 'Resumen Graficado');
//$this->params['breadcrumbs'][] = $this->title;

$this->registerJsFile(Url::to("@web/js/FusionCharts.js"), ['position' => \yii\web\View::POS_HEAD]);
$this->registerJsFile(Url::to("@web/js/jquery.bonsai.js"));
$this->registerJsFile(Url::to("@web/js/jquery.qubit.js"));
$this->registerCssFile(Url::to("@web/css/jquery.bonsai.css"))
?>

<!-- ALERT PARA CAMPOS SIN LLENAR -->
<?php
\yii\bootstrap\Modal::begin([
    'id' => 'modalCampos'
    , 'header' => "Advertencia"
    , 'size' => \yii\bootstrap\Modal::SIZE_SMALL
]);
echo Yii::t("app", "Seleccione un rango valido");
\yii\bootstrap\Modal::end();
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
//echo Html::jsFile("js/qa.js");
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Resumen-graficado.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }
</style>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="container-fluid">
    <div class="well well-sm">
        <?=
        Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                    "class" => "glyphicon glyphicon-chevron-down",
                ]) . " " . Html::encode($this->title), "javascript:void(0)"
                , ["class" => "openVistas", "id" => "graficar", "style" => "text-transform: uppercase"])
        ?> 
    </div>    
    <div class="row">
        <div class="col-lg-12">

            <!-- FORMULARIO DE BUSQUEDA DE GRAFICAS -->

            <div id="divConfig" class="well resumenGest row" style="z-index:2; display: none" title="Parametros de Visualizaci&oacute;n" >
                <?php
                foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
                    echo '<div class="col-md-12"><div class="alert alert-' . $key . '">' . $message . '</div></div>';
                }
                Yii::$app->session->removeAllFlashes();
                ?>
                <?=
                Html::beginForm(Url::to(['site/dashboard']), "post"
                        , ["onSubmit" => "return validarFormulario(0);"
                    , "class" => "form-horizontal"
                    , "id" => "guardarFormulario"]);
                ?>
                <div class="col-md-4">

                    <?php
                    $meses = [
                        "1" => "Enero",
                        "2" => "Febrero",
                        "3" => "Marzo",
                        "4" => "Abril",
                        "5" => "Mayo",
                        "6" => "Junio",
                        "7" => "Julio",
                        "8" => "Agosto",
                        "9" => "Septiembre",
                        "10" => "Octubre",
                        "11" => "Noviembre",
                        "12" => "Diciembre",
                    ];
                    $anio = [];
                    for ($i = 0; $i <= 2; $i++):
                        $anio[date('Y') - $i] = date('Y') - $i;
                    endfor;
                    ?>
                    <div class="form-group">
                        <div class="control-group">
                            <label class="control-label col-sm-3">
                                <?php echo Yii::t('app', 'Fecha'); ?>:
                            </label>
                        </div>                        
                        <div class="col-sm-9"> 
                            <?php 
                            $fecha = date('Y-m-01').' - '.date('Y-m-d');                            
                            echo Html::input('text', 'selMesDesde', 
                                    $fecha, 
                                    ['id' => 'selMesDesde', 'readonly'=>true, "class" => "form-control"]) ?>                            
                        </div>
                    </div>                    
                    <div class="form-group">
                        <div class="control-group">
                            <label class="control-label col-sm-3">
                                <?= Yii::t('app', 'Dimension'); ?>:
                            </label>
                        </div>
                        <div class="col-sm-9">                            
                            <?=
                            Html::dropDownList("selDimension"
                                    , $filtros->dimension
                                    , yii\helpers\ArrayHelper::map(
                                            app\models\Dimensiones::find()->all()
                                            , 'id', 'name')
                                    , ["class" => "form-control", "id" => "selDimension"]);
                            ?>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="control-group">
                            <label class="control-label col-sm-3">
                                <?= Yii::t('app', 'Metrica'); ?>:
                            </label>
                        </div>
                        <div class="col-sm-9">
                            <?php 
                            $metricas = yii\helpers\ArrayHelper::map(
                                    \app\models\Textos::find()->limit(10)->asArray()->all()
                                    , 'id', 'detexto');
                            $metricas[] = 'Score';
                            ?>
                            <?=
                            Html::dropDownList("selMetrica"
                                    , $filtros->metrica
                                    , $metricas
                                    , ["class" => "form-control", "id" => "selMetrica"]);
                            ?>
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
                            <div id="lstArbol" style="overflow: auto; max-height: 300px;width: 95%;padding:0px; margin: 0px">                          
                                <?php echo $data3 ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="btn-groupp col-md-12">
                        <?=
                        Html::submitButton(Yii::t('app', 'Graficar')
                                , ['class' => 'btn btn-success'])
                        ?>
                    </div>
                </div>
                <?php echo Html::endForm(); ?>
            </div>
            <?php    
            if (isset($data->graph->showGraf) && $data->graph->showGraf): ?>                
                <div class="alert alert-warning alert-dismissible" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <?php echo Yii::t('app', 'mensaje rango') ?>
                </div>
                <div id="divArbolGraph1" style="min-width: 350px; min-height: 200px" class="col-md-6">
                    <div style="z-index:1;" >
                        <div class="border-graph">
                            <?php
                            //Calculo de fechas -----------------------------------
                            $step = '+1 day';
                            $format = 'd';
                            $fechaTemp = explode(' - ', $fecha);
                            $dates = array();
                            $current = strtotime( $fechaTemp[0] );
                            $last = strtotime( $fechaTemp[1] );

                            while( $current <= $last ) {
                                    $dates[] = date( $format, $current );
                                    $current = strtotime( $step, $current );
                            }                        
                            //------------------------------------------------------

                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],
                                'id' => 'grafica1',
                                'options' => [
                                    'chart' => [
                                        'zoomType' => 'xy',
                                        'spacingLeft' => 0                                
                                    ],
                                    'colors' => [
                                        "#7cb5ec", "#434348", "#90ed7d", "#f7a35c", "#8085e9",
                                        "#f15c80", "#e4d354", "#2b908f", "#f45b5b", "#91e8e1",
                                        "#b25042", "#ede2c2", "#8997a9", "#9d74bd", "#589b61",
                                        "#c9cdd0", "#4a624a", "#6a5848", "#efadb1", "#d7e782",
                                        "#fcff00", "#000cff", "#b50000", "#ff00c6", "#00fcff",
                                        "#00ff0c", "#af9d8b", "#cbe2f3", "#3c76b7", "#7d864f",
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
                                        'text' => 'Promedio de ' . $data->graph->nameMetrica->detexto,
                                    ],                                
                                    'xAxis' => [
                                        'title' => [
                                            'text' => 'Días'
                                        ],
                                        'categories' => $dates
                                    ],
                                    'yAxis' => [
                                        'title' => [
                                            'text' => 'Promedio de ' . $data->graph->nameMetrica->detexto,                                        
                                        ],
                                        'plotLines' => [
                                            ['value' => '0','width'=>1, 'color'=>'#808080' ]
                                        ]
                                    ],
                                    'series' => $data->graph->arrData
                                ]
                            ]);                        
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
            <?php if (isset($data->graph->showGraf) && $data->graph->showGraf): ?>
                <div id="divArbolGraph" class="col-md-6">
                    <div style="z-index:1;" >
                        <div class="border-graph">
                            <?php                                                

                            echo Highcharts::widget([
                                'scripts' => [
                                    'highcharts',
                                ],
                                'options' => [
                                    'chart' => [
    //                                    'type' => 'column',
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
                                                'format' => '{point.y}'
                                            ]
                                        ]
                                    ],
                                    'title' => [
                                        'text' => 'Cantidad',
                                    ],                               
                                    'xAxis' => [
                                        'categories'=>$dates
                                    ],
                                    'yAxis' => [
                                        'title' => ['text' => 'Cantidad',
                                        ]
                                    ],
                                    'series' => $data->graph->arrDataCant
                                ]
                            ]);
                            ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>             
    </div>
</div>

<script type="text/javascript">

    $(document).ready(function () {
        $("#graficar").click(function () {
            $("#divConfig").toggle("slow");
        });

        $('#arbol_ids').bonsai({
            expandAll: false,
            checkboxes: true, // depends on jquery.qubit plugin
            createInputs: 'checkbox' // takes values from data-name and data-value, and data-name is inherited
        });
    });

    function fnSeleccionarTodosNA(sw) {
        $(".arbol_item").each(function (i, obj) {
            obj.checked = sw;
        });
    }

    function validarFormulario(arbol_id) {

        $("#arbol_item_" + arbol_id).attr('checked', $("#arbol_item_" + arbol_id).attr('checked') == true ? false : true);
        hayErrores = false;
        
        var arbolChecked = $('input[name="arbol_ids[]"]:checked').length > 0;
        if(!arbolChecked){
            $('#modalCampos .modal-body').html('Seleccione un Programa/PCRC');
            $('#modalCampos').modal('show');
            return false;
        }
        console.log(arbolChecked);
        
        if ($("#selMesDesde").val() == '') {
            hayErrores = true;
            $("#selMesDesde").addClass('field-error');
        } else {
            $("#selMesDesde").removeClass('field-error');
        }
        if ($("#selAnioDesde").val() == '') {
            hayErrores = true;
            $("#selAnioDesde").addClass('field-error');
        } else {
            $("#selAnioDesde").removeClass('field-error');
        }
        if ($("#selMesHasta").val() == '') {
            hayErrores = true;
            $("#selMesHasta").addClass('field-error');
        } else {
            $("#selMesHasta").removeClass('field-error');
        }
        if ($("#selAnioHasta").val() == '') {
            hayErrores = true;
            $("#selAnioHasta").addClass('field-error');
        } else {
            $("#selAnioHasta").removeClass('field-error');
        }

        if (hayErrores) {
            $('#modalCampos').modal('show');
            return false;
        }

        var mesDesde = $("#selMesDesde").val();
        var anioDesde = $("#selAnioDesde").val();
        var mesHasta = $("#selMesHasta").val();
        var anioHasta = $("#selAnioHasta").val();

        if (((anioDesde * 100) + (mesDesde * 1)) > ((anioHasta * 100) + (mesHasta * 1))) {
            $('#modalCampos .modal-body').html('El mes y año inicial, deben ser menores al mes y año final');
            $('#modalCampos').modal('show');
            return false;
        }

        var dimension_id = $("#selDimension").val();
        if (dimension_id == '') {
            $("#selDimension").addClass('field-error');
            $('#modalCampos .modal-body').html('Seleccione una dimensión.');
            $('#modalCampos').modal('show');
            return false;
        } else {
            $("#selDimension").removeClass('field-error');
        }

        var arbol_ids = new Array();
        var j = 0;
        var first = true;
        $(".arbol_item").each(function (i, item) {
            if (item.checked) {
                arbol_ids[j] = item.value;
                j++;
                if (first)
                    arbol_id = item.value;

                first = false;
            }
        });
        return true;
    }

</script>