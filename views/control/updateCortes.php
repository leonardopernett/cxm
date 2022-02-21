<?php

use yii\helpers\Html;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewCortes
 *
 * @author ingeneo
 */
?>

<?php
Modal::begin([
    'id' => 'modal-updatecorte',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>

<?php if ($tipo_corte == 1): ?>
    <div class="row">
        <?php
        Pjax::begin(['id' => 'corteupdate-pj']);
        ?> 
        <div class="col-sm-12">


            <?php

            $form = ActiveForm::begin([
                'options' => ['data-pjax' => true, "id" => "formCortesupdate"],
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'inputOptions' => ['autocomplete' => 'off']
                  ]
                ])
            ?>
            <table class="table table-striped table-bordered">
            <caption>Cortes</caption>
                <thead>
                    <tr>
                        <th scope="col">Semanas</th>
                        <th scope="col">Corte</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Semana 1</td>
                        <td><?php
                            $fechainicio = str_replace(['00:00:00',  ' '], '', $arraySegmentos[0]->segmento_fecha_inicio);
                            $fechafin = str_replace(['23:59:59',  ' '], '', $arraySegmentos[0]->segmento_fecha_fin);
                            $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
                            echo '<div class="input-group drp-container">';
                            echo DateRangePicker::widget([
                                'model' => $modelSegmento,
                                'name' => 'SegmentoCorte[semana1]',
                                'value' => (isset($arraySegmentos[0])) ? $fechainicio . ' - ' . $fechafin : '',
                                'id' => 'semana1',
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'center'
                        ]]) . $addon;
                            echo '</div>';
                            ?>
                            <?php
                            if (isset($arraySegmentos[0])) {
                                echo Html::hiddenInput('idsemana1', $arraySegmentos[0]->segmento_corte_id);
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td>Semana 2</td>
                        <td><?php
                            $fechainicio = str_replace(['00:00:00',  ' '], '', $arraySegmentos[1]->segmento_fecha_inicio);
                            $fechafin = str_replace(['23:59:59',  ' '], '', $arraySegmentos[1]->segmento_fecha_fin);
                            $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
                            echo '<div class="input-group drp-container">';
                            echo DateRangePicker::widget([
                                'model' => $modelSegmento,
                                'name' => 'SegmentoCorte[semana2]',
                                'value' => (isset($arraySegmentos[1])) ? $fechainicio . ' - ' . $fechafin : '',
                                'id' => 'semana2',
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'center'
                        ]]) . $addon;
                            echo '</div>';
                            ?> <?php
                            if (isset($arraySegmentos[1])) {
                                echo Html::hiddenInput('idsemana2', $arraySegmentos[1]->segmento_corte_id);
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td>Semana 3</td>
                        <td><?php
                            $fechainicio = str_replace(['00:00:00',  ' '], '', $arraySegmentos[2]->segmento_fecha_inicio);
                            $fechafin = str_replace(['23:59:59',  ' '], '', $arraySegmentos[2]->segmento_fecha_fin);
                            $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
                            echo '<div class="input-group drp-container">';
                            echo DateRangePicker::widget([
                                'model' => $modelSegmento,
                                'name' => 'SegmentoCorte[semana3]',
                                'value' => (isset($arraySegmentos[2])) ? $fechainicio . ' - ' . $fechafin : '',
                                'id' => 'semana3',
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'center'
                        ]]) . $addon;
                            echo '</div>';
                            ?><?php
                            if (isset($arraySegmentos[2])) {
                                echo Html::hiddenInput('idsemana3', $arraySegmentos[2]->segmento_corte_id);
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td>Semana 4</td>
                        <td><?php
                            $fechainicio = str_replace(['00:00:00',  ' '], '', $arraySegmentos[3]->segmento_fecha_inicio);
                            $fechafin = str_replace(['23:59:59',  ' '], '', $arraySegmentos[3]->segmento_fecha_fin);
                            $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
                            echo '<div class="input-group drp-container">';
                            echo DateRangePicker::widget([
                                'model' => $modelSegmento,
                                'name' => 'SegmentoCorte[semana4]',
                                'value' => (isset($arraySegmentos[3])) ? $fechainicio . ' - ' . $fechafin : '',
                                'id' => 'semana4',
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'center'
                        ]]) . $addon;
                            echo '</div>';
                            ?><?php
                            if (isset($arraySegmentos[3])) {
                                echo Html::hiddenInput('idsemana4', $arraySegmentos[3]->segmento_corte_id);
                            }
                            ?></td>
                    </tr>
                    <tr>
                        <td>Semana 5</td>
                        <td><?php
                            if (isset($arraySegmentos[4])) {
                                $fechainicio = str_replace(['00:00:00',  ' '], '', $arraySegmentos[4]->segmento_fecha_inicio);
                                $fechafin = str_replace(['23:59:59',  ' '], '', $arraySegmentos[4]->segmento_fecha_fin);
                            }

                            $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
                            echo '<div class="input-group drp-container">';
                            echo DateRangePicker::widget([
                                'model' => $modelSegmento,
                                'name' => 'SegmentoCorte[semana5]',
                                'value' => (isset($arraySegmentos[4])) ? $fechainicio . ' - ' . $fechafin : '',
                                'id' => 'semana5',
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                        ]]) . $addon;
                            echo '</div>';
                            ?> <?php
                            if (isset($arraySegmentos[4])) {
                                echo Html::hiddenInput('idsemana5', $arraySegmentos[4]->segmento_corte_id);
                            }
                            ?></td>
                    </tr>
                </tbody>
            </table>
            <?php echo Html::hiddenInput('Tipo_corte', $tipo_corte) ?>
            <?php echo Html::hiddenInput('corte_id', $corte_id) ?>
            <?php echo Html::hiddenInput('rangofecha', $rangofecha) ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">


                    <?= Html::a(Yii::t('app', 'Actualizar'), 'javascript:void(0)', ['class' => 'btn btn-success crear']) ?>

                    <?= Html::a(Yii::t('app', 'Volver'), 'javascript:void(0)', ['class' => 'btn  btn-default volver']) ?>
                    <div id="gifcargando" style="margin-left: 10px; float: left; display: none">
                        <?= Html::img(Url::to("@web/images/ajax-loader.gif"), ['alt' => 'cargando', 'style' => 'width:20px;']); ?>
                    </div>
                </div>        
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
<?php else: ?>
    <div class="row">
        <?php
        Pjax::begin(['id' => 'corte-pj', 'timeout' => false,
            'enablePushState' => false]);
        ?> 
        <div class="col-sm-12">


            <?php

            $form = ActiveForm::begin([
                'options' => ['data-pjax' => true, "id" => "formCortesupdate"],
                'layout' => 'horizontal',
                'fieldConfig' => [
                    'inputOptions' => ['autocomplete' => 'off']
                  ]
                ])
            ?>
            <table class="table table-striped table-bordered">
            <caption>Cortes</caption>
                <thead>
                    <tr>
                        <th scope="col">Mes</th>
                        <th scope="col">Corte</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Mes</td>
                        <td><?php
        $fechainicio = str_replace(['00', ':', ' '], '', $arraySegmentos[0]->segmento_fecha_inicio);
        $fechafin = str_replace(['23', '59', ':', ' '], '', $arraySegmentos[0]->segmento_fecha_fin);
        $addon = <<< HTML
<span class="input-group-addon">
    <i class="glyphicon glyphicon-calendar"></i>
</span>
HTML;
        echo '<div class="input-group drp-container">';
        echo DateRangePicker::widget([
            'model' => $modelSegmento,
            'value' => (isset($arraySegmentos[0])) ? $fechainicio . ' - ' . $fechafin : '',
            'name' => 'SegmentoCorte[mes]',
            'id' => 'mes',
            'useWithAddon' => true,
            'convertFormat' => true,
            'readonly' => 'readonly',
            'pluginOptions' => [
                'timePicker' => false,
                'format' => 'Y-m-d',
                'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                'endDate' => date("Y-m-d"),
                'opens' => 'center'
    ]]) . $addon;
        echo '</div>';
            ?> <?php
                            if (isset($arraySegmentos[0])) {
                                echo Html::hiddenInput('idmes', $arraySegmentos[0]->segmento_corte_id);
                            }
                            ?></td>
                    </tr>

                </tbody>
            </table>
            <?php echo Html::hiddenInput('Tipo_corte', $tipo_corte) ?>
            <?php echo Html::hiddenInput('corte_id', $corte_id) ?>
            <?php echo Html::hiddenInput('rangofecha', $rangofecha) ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">


                    <?= Html::a(Yii::t('app', 'Agregar'), 'javascript:void(0)', ['class' => 'btn btn-success crear']) ?>

                    <?= Html::a(Yii::t('app', 'Volver'), 'javascript:void(0)', ['class' => 'btn  btn-default volver']) ?>
                    <div id="gifcargando" style="margin-left: 10px; float: left; display: none">
                        <?= Html::img(Url::to("@web/images/ajax-loader.gif"), ['alt' => 'cargando', 'style' => 'width:20px;']); ?>
                    </div>
                </div>        
            </div>

            <?php ActiveForm::end(); ?>
        </div>
        <?php Pjax::end(); ?>
    </div>
<?php endif; ?>
<script type="text/javascript">

    $(".crear").click(function () {
        datos = fnForm2Array('formCortesupdate');

        ruta = '<?php
echo Url::to(['update', 'corte_id' => $corte_id, 'Tipo_corte' => $tipo_corte]);
?>';
                rutaValid = '<?php
echo Url::to(['validarcortes', 'Tipo_corte' => $tipo_corte]);
?>';
        
        $.ajax({
            type: 'POST',
            url: rutaValid,
            dataType: "JSON",
            data: datos,
            beforeSend: function () {
                $('#gifcargando').show();
                $('.crear').attr('disabled', 'disabled');
            },
            success: function (data) {
                if (data.results != 0) {
                    bootbox.confirm({
                    message: data.msg,
                    buttons: {
                        cancel: {
                            label: '<i class="fa fa-times"></i> Cancelar',
                            className: 'btn-danger',
                        },
                        confirm: {
                            label: '<i class="fa fa-check"></i> Continuar',
                            className: 'btn-success',
                        }
                    },
                    callback: function (result) {
                        if (result) { // aca se genera alert
                        $.ajax({
                            type: 'POST',
                            cache: false,
                            url: ruta,
                            data: datos,
                            success: function (response) {
                                $('#ajax_result').html(response);
                            }
                        });
                        }else{
                            $('.crear').removeAttr('disabled');
                        }
                    }
                });
                } else {
                    $.ajax({
                        type: 'POST',
                        cache: false,
                        url: ruta,
                        data: datos,
                        success: function (response) {
                            $('#ajax_result').html(response);
                        }
                    });
                }

            }
        });      
        
        
        
    });
    $(".volver").click(function () {
        datos = fnForm2Array('formCortesupdate');

        ruta = '<?php
echo Url::to(['volver', 'Tipo_corte' => $tipo_corte]);
?>';
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            data: datos,
            success: function (response) {
                $('#ajax_result').html(response);
            }
        });
    });
    function fnForm2Array(strForm) {

        var arrData = new Array();

        $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
            if ($(this).attr('name')) {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
            }
        });

        return arrData;

    }
</script>
<?php Modal::end(); ?>