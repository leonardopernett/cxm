<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$variables  = Yii::$app->user->identity->id;

$this->title = Yii::t('app', 'Historico de Alertas');
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';
                
$varTipoAlerta = (new \yii\db\Query())
        ->select(['tipo_alerta,COUNT(tipo_alerta) AS cant_alertas'])
        ->from(['tbl_alertascx'])        
        ->groupBy('tipo_alerta'); 

$varTipoCliente = (new \yii\db\Query())
        ->select(['cliente,COUNT(cliente) AS cant_clientes'])
        ->from(['tbl_proceso_cliente_centrocosto'])        
        ->groupBy('cliente');  

$varConteo = (new \yii\db\Query())
                ->select(['*'])
                ->from(['tbl_alertascx'])
                ->count();
?> 
<style>
    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card2 {
            height: 295px;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card3 {
            height: 870px;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Alertas-Valoración.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 80px;
      height: 80px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

</style>
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>

<br><br>



<!-- Capa Ficha Tecnica -->
<div class="capaInfo" id="idCapaInfo" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Procesos de Información') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <?php 
            $form = ActiveForm::begin([
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'inputOptions' => ['autocomplete' => 'off']
                ]
            ]); 
        ?>
        <div class="col-md-6">
            <div class="card2 mb">

                <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Seleccionar Rango de Fecha') ?></label>
                <?=
                    $form->field($model, 'fecha', [
                        'labelOptions' => ['class' => 'col-md-12'],
                        'template' => '<div class="col-md-12">{label}</div>'
                        . '<div class="col-md-12"><div class="input-group">'
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
                        'pluginOptions' => [
                            'timePicker' => false,
                            'format' => 'Y-m-d',
                            'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                            'endDate' => date("Y-m-d"),
                            'opens' => 'right'
                    ]])->label('');
                ?>

                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Seleccionar Programa/Pcrc') ?></label>
                <?=
                    $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
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

                <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Seleccionar Responsable') ?></label>
                <?=
                    $form->field($model, 'responsable', ['template' => $template])
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 4,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
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
                                                    }).done(function(data) { callback(data.results[0]);});
                                                }
                                            }')
                                ]
                                    ] 
                        );
                ?>  

                <br>

                <?=
                    Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                ?>

            </div>
        </div>
        <?php ActiveForm::end(); ?>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Cantidad de Alertas') ?></label>
                <label style="font-size: 30px;" class="text-center"><?= Yii::t('app', $varConteo) ?></label>
            </div>

            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Descarga General') ?></label>
                <a id="dlink" style="display:none;"></a>
                <button  class="btn btn-info" style="background-color: #4298B4" id="btn_descargar"><?= Yii::t('app', 'Descargar') ?></button>
            </div>

            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #1993a5;"></em> <?= Yii::t('app', 'Buscar General') ?></label>
                <?= Html::a('Buscar',  ['basesatisfaccion/alertasvaloracion'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Buscar General']) 
                ?>
            </div>
        </div>
    </div>
    
</div>

<hr>

<!-- Capa Resumen Procesos -->
<div class="capaResumenProceso" id="idCapaResumenProceso" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resumen del Proceso') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px">
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #1993a5;"></em> <?= Yii::t('app', 'Resumen Proceso') ?></label></caption>
                    <thead>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Programa') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Cantidad de alertas') ?></label></th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($resumenFeedback as $value) {                           
                        ?>
                        <tr>
                            <td  class="text-center"><label style="font-size: 10px;"> <?= Yii::t('app', $value['Programa']) ?> </label></td>
                            <td  class="text-center"><label style="font-size: 10px;"> <?= Yii::t('app', $value['Cliente']) ?> </label></td>
                            <td  class="text-center"><label style="font-size: 10px;"> <?= Yii::t('app', $value['Count']) ?> </label></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Gráfica de Resumen de Procesos') ?></label>
                <div id="containeralerta" class="highcharts-container" style="height: 500;"></div> 
            </div>
        </div>
    </div>
    
</div>

<hr>

<!-- Capa Resumen Tecnico -->
<div class="capaResumenTecnico" id="idCapaResumenTecnico" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resumen Técnico') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <table id="myTablee" class="table table-hover table-bordered" style="margin-top:20px">
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #1993a5;"></em> <?= Yii::t('app', 'Resumen Técnico') ?></label></caption>
                    <thead>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Tecnico') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Programa') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Cliente') ?></label></th>
                        <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Cantidad de alertas') ?></label></th>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($detalleLiderFeedback as $value) {                           
                        ?>
                        <tr>
                            <td  class="text-center"><label style="font-size: 10px;"> <?= Yii::t('app', $value['Tecnico']) ?> </label></td>
                            <td  class="text-center"><label style="font-size: 10px;"> <?= Yii::t('app', $value['Programa']) ?> </label></td>
                            <td  class="text-center"><label style="font-size: 10px;"> <?= Yii::t('app', $value['Cliente']) ?> </label></td>
                            <td  class="text-center"><label style="font-size: 10px;"> <?= Yii::t('app', $value['Count']) ?> </label></td>
                        </tr>
                        <?php
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #1993a5;"></em><?= Yii::t('app', ' Gráfica de Resumen Técnico') ?></label>
                <div id="chartContainerClientes" class="highcharts-container" style="height: 500;"></div> 
            </div>
        </div>
    </div>

</div>

<hr>

<!-- Capa Generica -->
<div class="capaGeneral" id="idCapaGeneral" style="display: inline;" >
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resumen General') ?></label>
            </div>
        </div>
    </div>

    <br>

    <?php
        if ($variables == "70" || $variables == "415" || $variables == "7" || $variables == "438" || $variables == "991" || $variables == "1609" || $variables == "1796" || $variables == "223" || $variables == "556" || $variables == "1251" || $variables == "790" || $variables == "473" || $variables == "777" || $variables == "2953" || $variables == "3229" || $variables == "3468" || $variables == "2913" || $variables == "2911" || $variables == "2991" || $variables == "2990" || $variables == "57") {
    ?>

        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <table id="myTablee3" class="table table-hover table-bordered" style="margin-top:20px">
                        <caption><label><em class="fas fa-list" style="font-size: 20px; color: #1993a5;"></em> <?= Yii::t('app', 'Listado de Alertas') ?></label></caption>
                        <thead>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Fecha') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Servicio / PCRC') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Valorador') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Detalle') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Gestión') ?></label></th>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($dataProvider as $value) {                           
                            ?>
                            <tr>
                                <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['fecha']) ?> </label></td>
                                <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['Programa']) ?> </label></td>
                                <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['Tecnico']) ?> </label></td>
                                <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['tipo_alerta']) ?> </label></td>
                                <td><a href="veralertas/<?php echo $value["xid"] ?>"  href="veralertas/" . <?= $value["xid"]; ?>><img src="../../../web/images/ico-view.png" alt="icon-view"></a></td>
                                <td><input type="image" src="../../../web/images/ico-delete.png" alt="icon-delete" name="imagenes" style="cursor:hand" id="imagenes" onclick="eliminarDato(<?php echo $value["xid"] ?>);" /></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    <?php
        }else{
    ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <table id="myTablee3" class="table table-hover table-bordered" style="margin-top:20px">
                        <caption><label><em class="fas fa-list" style="font-size: 20px; color: #1993a5;"></em> <?= Yii::t('app', 'Listado de Alertas') ?></label></caption>
                        <thead>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Fecha') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Servicio / PCRC') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Valorador') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Detalle') ?></label></th>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($dataProvider as $value) {                           
                            ?>
                            <tr>
                                <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['fecha']) ?> </label></td>
                                <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['Programa']) ?> </label></td>
                                <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['Tecnico']) ?> </label></td>
                                <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['tipo_alerta']) ?> </label></td>
                                <td><a href="veralertas/<?php echo $value["xid"] ?>"  href="veralertas/" . <?= $value["xid"]; ?>><img src="../../../web/images/ico-view.png" alt="icon-view"></a></td>
                            </tr>
                            <?php
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php
        }
    ?>

</div>

<hr>

<!-- Capa Hidden a descargar -->
<div class="capaGeneralHidden" id="capaIdGeneralHidden" style="display: none;">
    
    <div class="row">
        <div class="col-md-12">
            <table id="myTableOcult"  hidden="hidden"  class="table table-hover table-bordered" style="margin-top:20px">
            <caption><label><?= Yii::t('app', 'Archivo Data General') ?></label></caption>
                <thead><!--Emcabezados de la tabla -->
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Fecha') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Servicio / PCRC') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Valorador') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Destinarios') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Asunto') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Respuesta') ?></label></th>
                    <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:10; "><?= Yii::t('app', 'Comentario Encuesta') ?></label></th>
                </thead>
                <tbody>
                    <?php
                    foreach ($dataTablaGlobal as $key => $value) {
                    ?>
                    <tr>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['fecha']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['name']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['usua_nombre']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['tipo_alerta']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['remitentes']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['asunto']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['comentario']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['resp_encuesta_saf']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['comentario_saf']) ?> </label></td>
                        <td  class="text-center"><label style="font-size: 12px;"> <?= Yii::t('app', $value['id_encuesta_saf']) ?> </label></td>
                    </tr>
                    <?php
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

</div>


<script type="text/javascript">
    function eliminarDato(params1){
        var cajaNum = params1;
        var opcion = confirm("Confirmar la eliminación de la alerta");

        if (opcion == true){
            $.ajax({
                method: "get",
                url: "pruebas",
                data : {
                    alertas_cx: cajaNum,
                },
                success : function(response){ 
                    console.log(response);
                    var respuesta = JSON.parse(response);
                    console.log(respuesta);
                    if(respuesta == 1){
                        window.location.href = "../basesatisfaccion/alertasvaloracion";
                    }else{
                        alert("Error al intentar eliminar la alerta");
                    }
                }
            });
        }
    };

    $('#containeralerta').highcharts({
        chart: {                
            type: 'bar'
        },

        yAxis: {
            title: {
                text: 'Cantidad de Alertas'
            }
        }, 

        title: {
            text: '',
            style: {
                color: '#3C74AA'
            }
        },

        xAxis: {
            categories: " ",
            title: {
                text: null
            }
        },

        series: [              
            <?php foreach($varTipoAlerta->each() as $alerta){ ?>
                {
                    name: "<?php echo $alerta['tipo_alerta'];?>",
                    data: [<?php echo $alerta['cant_alertas'];?> ]                         
                },
            <?php } ?> 
        ],
        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }  
    });

    $('#chartContainerClientes').highcharts({
        chart: {                
            type: 'column'
        },

        yAxis: {
            title: {
                text: 'Cantidad de Alertas'
            }
        }, 

        title: {
            text: '',
            style: {
                color: '#3C74AA'
            }
        },

        xAxis: {
            categories: " ",
            title: {
                text: null,
            }
        },

        series: [              
            <?php   foreach($varTipoCliente->each() as $cliente){   ?>
                {
                    name: "<?php echo $cliente['cliente'];?>",
                    data: [<?php echo $cliente['cant_clientes'];?> ]                         
                },
            <?php   }   ?> 
        ],                
    });

    $(document).ready( function () {
        $('#myTable').DataTable({
            responsive: true,
            fixedColumns: true,
            select: true,
            "language": {
                "lengthMenu": "Cantidad de Datos a Mostrar",
                "zeroRecords": "No se encontraron datos ",
                "info": "Mostrando Maximo de Registros",
                "infoEmpty": "No hay datos aun",
                "infoFiltered": "(Filtrado un Max total)",
                "search": "Buscar:",
                "paginate": {
                  "first":      "Primero",
                  "last":       "Ultimo",
                  "next":       "Siguiente",
                  "previous":   "Anterior"
                }
            } 
        });

        $('#myTablee').DataTable({
            responsive: true,
            fixedColumns: true,
            select: true,
            "language": {
                "lengthMenu": "Cantidad de Datos a Mostrar",
                "zeroRecords": "No se encontraron datos ",
                "info": "Mostrando Maximo de Registros",
                "infoEmpty": "No hay datos aun",
                "infoFiltered": "(Filtrado un Max total)",
                "search": "Buscar:",
                "paginate": {
                    "first":      "Primero",
                    "last":       "Ultimo",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                }
            } 
        });

        $('#myTablee3').DataTable({
            responsive: true,
            fixedColumns: true,
            select: true,
            "language": {
                "lengthMenu": "Cantidad de Datos a Mostrar",
                "zeroRecords": "No se encontraron datos ",
                "info": "Mostrando Maximo de Registros",
                "infoEmpty": "No hay datos aun",
                "infoFiltered": "(Filtrado un Max total)",
                "search": "Buscar:",
                "paginate": {
                    "first":      "Primero",
                    "last":       "Ultimo",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                }
            } 
        });
    });

    var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "Alertas";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('myTableOcult', 'Plantilla', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn_descargar");
    btn.addEventListener("click",download); 
    
</script>