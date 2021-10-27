<?php

use yii\helpers\Html;
use yii\helpers\Url;
?>


<?php $this->title = Yii::t('app', 'Realizar monitoreo'); ?>

<div class="page-header">
    <?php if ($data->preview) : ?>
        <h3><?= Yii::t('app', 'Ver monitoreo') ?></h3>
    <?php else: ?>
        <h3><?= Yii::t('app', 'Realizar monitoreo') ?></h3>
    <?php endif; ?>
</div>

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

<?php
$banderaDim = ($data->basesatisfaccion->tipo_inbox == 'NORMAL') ? true : false;
$prev_bloque_descripcion = $prev_seccion = $prev_bloque = '';
$prev_id_bloque = 0;
$decisionSiConTipi = "";
$decisionNoConTipi = "";
$decisionSi = "";
$decisionNo = "";
$detalles_ids = array();
?>

<!-- DIVS Para carga de ajax -->
<?php echo Html::tag('div', '', ['id' => 'ajax_div_problemas']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_div_llamdas']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_div_feedbacks']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_result']); ?>
<?php if ($formulario): ?>
    <div class="formularios-form">

        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>

        <!-- ALERT PARA CAMPOS SIN LLENAR -->
        <?php
        \yii\bootstrap\Modal::begin([
            'id' => 'modalCampos'
            , 'header' => "Advertencia"
            , 'size' => \yii\bootstrap\Modal::SIZE_SMALL
        ]);
        echo Yii::t("app", "Campos sin seleccionar");
        \yii\bootstrap\Modal::end();
        ?>

        <?php
        \yii\bootstrap\Modal::begin([
            'id' => 'modalBloques'
            , 'header' => "Advertencia"
            , 'size' => \yii\bootstrap\Modal::SIZE_SMALL
        ]);
        echo Yii::t("app", "advertenciaBloques");
        \yii\bootstrap\Modal::end();
        ?>
        <?php if ($data->preview == true): ?>
            <div class="row">
                <div class="col-sm-12 well">
                    <?=
                    Html::a(Yii::t('app', 'Cancel')
                            , ['cancelarformulario'
                        , 'id' => $data->basesatisfaccion->id, 'tmp_form' => $data->formulario_id]
                            , ['class' => 'btn btn-default soloCancelar'])
                    ?>
                </div>        
            </div>
    <?php endif; ?>
        <div class="row seccion">
            <h3><?= 'Gestión de la Satisfacción' ?></h3>
        </div>
        <div id="divTablaPreguntas">
            <table id="tablaPreguntas" class="table table-striped table-bordered detail-view">
            <caption>Tabla preguntas</caption>
                <tr>
                    <th scope="col">
                        <?php
                        if (!empty($data->basesatisfaccion->buzon)) {
                            $url_buzon = explode("/web/", $data->basesatisfaccion->buzon);
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "Grabación buzón"), Url::to("@web/" . $url_buzon[1]), ['target' => "_blank"]);
                        } else {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "No se encontró buzón"), $data->basesatisfaccion->buzon);
                        }
                        ?>
                    </th>
                </tr>
                <tr>
                    <td>
                        <?php
                        if (!empty($data->basesatisfaccion->llamada)) {
                            $llamada = json_decode($data->basesatisfaccion->llamada);
                            if (count($llamada) > 1) {
                                $i = 1;
                                foreach ($llamada as $value) {
                                    echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                            . Yii::t("app", "Grabación Llamada") . " - " . $i . " ", $value->llamada, ['target' => "_blank"]);
                                    $i++;
                                }
                            } else {
                                echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                        . Yii::t("app", "Grabación Llamada"), $llamada[0]->llamada, ['target' => "_blank"]);
                            }
                        } else {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "No se encontró llamada"), $data->basesatisfaccion->llamada);
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>

        <?php if ($data->preview != true) : ?>        
            <?= Html::beginForm(Url::to(['formularios/guardaryenviarformulario']), "post", ["class" => "form-horizontal", "id" => "guardarFormulario"]); ?>
            <?php else: ?>
            <div class="form-horizontal">
            <?php endif; ?>

    <?php if ($data->preview == false): ?>
                <div class="form-group">
                    <div class="col-sm-12 well">
                        <?php /* = Html::submitButton(Yii::t('app', 'Guargar y enviar'), ['class' => 'btn btn-success']) */ ?>
                        <?= Html::a(Yii::t('app', 'Guardar y enviar'), "javascript:void(0)", ['class' => 'btn btn-success soloFinalizar'])
                        ?>     
        <?= Html::a(Yii::t('app', 'Cancel'), ['cancelarformulario', 'id' => $data->basesatisfaccion->id], ['class' => 'btn btn-default soloCancelar']) ?>

                    </div>        
                </div>
    <?php endif; ?>

            <div class="form-group row">
                <div class="col-sm-12">
                    <table class="table table-striped table-bordered detail-view">
                    <caption>Tabla Satisfacción</caption>
                        <tbody>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "ANI"); ?></th>
                                <td><?php echo $data->basesatisfaccion->ani ?></td>
                            </tr>
                            <tr>
                                <th id="identificacion"><?php echo Yii::t("app", "Identificación"); ?></th>
                                <td><?php echo $data->basesatisfaccion->identificacion ?></td>
                            </tr>
                            <tr>
                                <th id="nombre"><?php echo Yii::t("app", "Nombre"); ?></th>
                                <td><?php echo $data->basesatisfaccion->nombre ?></td>
                            </tr>
                            <tr>
                                <th id="ext"><?php echo Yii::t("app", "Ext"); ?></th>
                                <td><?php echo $data->basesatisfaccion->ext ?></td>
                            </tr>
                            <tr>
                                <th id="tipoServicio"><?php echo Yii::t("app", "Tipo Servicio"); ?></th>
                                <td><?php echo $data->basesatisfaccion->tipo_servicio ?></td>
                            </tr>
                            <tr>
                                <th id="tipoEncuesta"><?php echo Yii::t("app", "Tipo encuesta"); ?></th>
                                <td><?php echo $data->basesatisfaccion->tipo_encuesta ?></td>
                            </tr>
                            <tr>
                                <th id="liderEquipo"><?php echo Yii::t("app", "Lider Equipo"); ?></th>
                                <td><?php echo $data->basesatisfaccion->lider_equipo ?></td>
                            </tr>
                            <tr>
                                <th id="programaPcrc"><?php echo Yii::t("app", "Programa/PCRC"); ?></th>
                                <td><?php echo $data->basesatisfaccion->pcrc0->name ?></td>
                            </tr>
                            <tr>
                                <th id="cliente"><?php echo Yii::t("app", "Cliente"); ?></th>
                                <td><?php echo $data->basesatisfaccion->cliente0->name ?></td>
                            </tr>
                            <tr>
                                <th id="rn"><?php echo Yii::t("app", "RN"); ?></th>
                                <td><?php echo $data->basesatisfaccion->rn ?></td>
                            </tr>
                            <tr>
                                <th id="agente"><?php echo Yii::t("app", "Agente"); ?></th>
                                <td><?php echo $data->basesatisfaccion->agente ?></td>
                            </tr>
                            <tr>
                                <th id="tipologia"><?php echo Yii::t("app", "Tipología"); ?></th>
                                <td><?php
                                    echo Html::dropDownList("categoria"
                                            , $data->basesatisfaccion->tipologia
                                            , $data->recategorizar
                                            , ["id" => "categoria", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]);
                                    ?></td>
                            </tr>
                            <tr>
                                <th id="connid"><?php echo Yii::t("app", "Connid"); ?></th>
                                <td><?php echo $data->basesatisfaccion->connid; ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta1"><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->preguntas['0']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta1 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta2"><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->preguntas['1']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta2 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta3"><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->preguntas['2']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta3 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta4"><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->preguntas['3']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta4 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta5"><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->preguntas['4']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta5 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta6"><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->preguntas['5']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta6 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta7"><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->preguntas['6']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta7 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta8"><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->preguntas['7']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta8 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta9"><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->preguntas['8']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta9 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="pregunta10"><?php echo (strtoupper($data->preguntas['9']['nombre']) != 'NO APLICA') ? $data->preguntas['9']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['9']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta10 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th id="evaluadoId"><?php echo Yii::t("app", "Evaluado ID"); ?></th>
                                <td><?php echo (isset($data->evaluado))? $data->evaluado:''; ?></td>
                            </tr>
                            <tr>
                                <th id="instrumentoV"><?php echo Yii::t("app", "Instrumento para la Valoracion"); ?></th>
                                <td><?php echo $data->ruta_arbol ?></td>
                            </tr>
    <?php //if ($banderaDim) : ?>
                                <tr>
                                    <th id="dimension"><?php echo Yii::t("app", "Dimension"); ?></th>
                                    <td><?php
                                        echo Html::dropDownList("dimension"
                                                , $data->tmp_formulario->dimension_id
                                                , $data->dimension
                                                , ["id" => "dimension", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]);
                                        ?></td>
                                </tr>
    <?php //else: ?>
                                <!--<tr>
                                    <th><?php //echo Yii::t("app", "Dimension"); ?></th>
                                    <td><?php
                                        /**echo Html::dropDownList("dimension"
                                                , $data->tmp_formulario->dimension_id
                                                , $data->dimension
                                                , ["id" => "dimension", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => true, "readonly" => true]);*/
                                        ?>
                                    </td>
                                </tr>-->
    <?php //endif; ?>
                            <tr>
                                <th id="fuente"><?php echo Yii::t("app", "Fuente"); ?></th>
                                <td>
                                    <?php if ($data->preview) : ?>
                                        <?=
                                        Html::input("text"
                                                , "fuente"
                                                , $data->tmp_formulario->dsfuente_encuesta
                                                , [
                                            "id" => "fuente",
                                            "class" => "form-control",
                                            "placeholder" => Yii::t("app", "Ingrese la fuente"),
                                            "readonly" => "readonly"
                                        ]);
                                        ?>
                                    <?php else: ?>
                                        <?=
                                        Html::input("text"
                                                , "fuente"
                                                , $data->tmp_formulario->dsfuente_encuesta
                                                , [
                                            "id" => "fuente",
                                            "class" => "form-control",
                                            "placeholder" => Yii::t("app", "Ingrese la fuente")
                                        ]);
                                        ?>
    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th id="interaccion"><?php echo Yii::t("app", "Interaccion"); ?></th>
                                <td>
                                    <?php if ($data->preview) : ?>
                                        <?=
                                        Html::dropDownList("transacion_id"
                                                , $data->tmp_formulario->transacion_id
                                                , $data->transacciones
                                                , ["class" => "form-control", "disabled" => "disabled"]);
                                        ?>
                                    <?php else: ?>
                                        <?=
                                        Html::dropDownList("transacion_id"
                                                , $data->tmp_formulario->transacion_id
                                                , $data->transacciones
                                                , ["class" => "form-control"]);
                                        ?>
    <?php endif; ?>                                
                                </td>
                            </tr>
                            <?php
                            if (isset($_GET['showInteraccion']) && base64_decode($_GET['showInteraccion']) == 1):
                                ?>
                                <tr>
                                    <th id="enalcesInteraccion"><?php echo Yii::t("app", "Enalces Interaccion"); ?></th>
                                    <td>
                                        <?php
                                        if (!empty($data->tmp_formulario->url_llamada)) {
                                            $arrayLlamada = json_decode($data->tmp_formulario->url_llamada);
                                            if (count($arrayLlamada) >= 2) {
                                                echo '<ul style="list-style: none">';
                                                foreach ($arrayLlamada as $key => $object) {
                                                    if (!empty($object->llamada)) {
                                                        echo '<li>'
                                                        . Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "20px"]) . ' '
                                                                . Yii::t("app", "Reproducir Interaccion") . ' No. ' . ++$key, $object->llamada, ['target' => '_blank'])
                                                        . '</li>';
                                                    }
                                                }
                                                echo '</ul>';
                                            } elseif (count($arrayLlamada) == 1) {
                                                $object = $arrayLlamada[0];
                                                echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "20px"]) . ' '
                                                        . Yii::t("app", "Reproducir Interaccion"), $object->llamada, ['target' => '_blank']);
                                            } else {
                                                echo Yii::t("app", "No se encontro interaccion");
                                            }
                                        } else {
                                            echo Yii::t("app", "No se encontro interaccion");
                                        }
                                        ?>
                                    </td>
                                </tr>                        
    <?php endif; ?>    
                        </tbody>
                    </table>
                    <?php
                    if (isset($_GET['showBtnIteraccion']) && base64_decode($_GET['showBtnIteraccion']) == 1) {
                        echo Html::a(Html::img(Url::to("@web/images/actualizar.png"), ["width" => "20px"]) . ' '
                                . Yii::t("app", "Solicitar interaccion"), 'javascript:void(0)', ['class' => 'btn btn-default',
                            'data-pjax' => 'w0',
                            'onclick' => "                                    
                                    $.ajax({
                                        type     :'GET',
                                        cache    : false,
                                        data : {"
                            . "formulario_id : '"
                            . $data->formulario_id
                            . "', url: '"
                            . $data->tmp_formulario->url_llamada
                            . "', evaluado_id: '"
                            . $data->tmp_formulario->evaluado_id
                            . "', arbol_id:'"
                            . $data->tmp_formulario->arbol_id
                            . "', dimension_id:'"
                            . $data->tmp_formulario->dimension_id . "'},
                                        url  : '" . Url::to(['declinacionesusuarios/create']) . "',
                                        success  : function(response) {
                                            $('#ajax_result').html(response);
                                        }
                                    });
                                    return false;"
                        ]);
                    }
                    ?>  
                </div>
            </div>

            <?= Html::input("hidden", "tmp_formulario_id", $data->formulario_id, ["id" => "tmp_formulario_id"]); ?>
            <?= Html::input("hidden", "basesatisfaccion_id", $data->basesatisfaccion->id); ?>

            <?= Html::input("hidden", "arbol_id", $data->tmp_formulario->arbol_id); ?>
            <?php //if ($banderaDim) : ?>
                <?= Html::input("hidden", "dimension_id", $data->tmp_formulario->dimension_id); ?>
            <?php //else: ?>
                <?php //echo Html::input("hidden", "dimension", $data->tmp_formulario->dimension_id, ['id' => 'dimension']); ?>
            <?php //endif; ?> 
            <?= Html::input("hidden", "ruta_arbol", $data->ruta_arbol); ?>
            <?= Html::input("hidden", "form_equipo_id", (isset($data->equipo_id))? $data->equipo_id:''); ?>
    <?= Html::input("hidden", "form_lider_id", (isset($data->usua_id_lider))? $data->usua_id_lider:''); ?>

            <!-- CAMPO OCULTO PARA EVITAR SUBMIT NO CONTROLADO -->
    <?= Html::input("hidden", "submitcorrecto", "NO", ["id" => "submitcorrecto"]); ?>



            <?php foreach ($data->detalles as $detalle): ?>
                <?php $detalles_ids[] = $detalle->id ?>
                <?php if ($prev_seccion != $detalle->seccion_id): ?>
                    <?php if (!empty($prev_seccion)): ?>                
                        <div class="form-group row" <?php
                        if ($prev_sndesplegar_comentario == 0) {
                            echo 'style="display: none"';
                        }
                        ?>>
                            <div class="col-sm-12">
                                <?php if ($data->preview) : ?>
                                    <?=
                                    Html::textarea("comentarioSeccion[" . $prev_seccion . "]"
                                            , $prev_secccion_comentario
                                            , [
                                        "id" => "txt_comentarios",
                                        "class" => "form-control",
                                        "placeholder" => "Comentario para el Coaching",
                                        "readonly" => "readonly"
                                    ]);
                                    ?>
                                <?php else: ?>
                                    <?=
                                    Html::textarea("comentarioSeccion[" . $prev_seccion . "]"
                                            , $prev_secccion_comentario
                                            , [
                                        "id" => "txt_comentarios",
                                        "class" => "form-control",
                                        "placeholder" => "Comentario para el Coaching"
                                    ]);
                                    ?>
                <?php endif; ?>                            
                            </div>

                        </div>

                        <?php endif; ?>
                    <div class="row seccion">
                    <?php echo $detalle->seccion ?>
                    </div>
                <?php endif; ?>
                    <?php if ($prev_bloque != $detalle->bloque): ?>
                    <div class="row well">
                        <?php echo $detalle->bloque ?>
                        <?php
                        echo Html::tag('span', Html::img(Url::to("@web/images/Question.png")), [
                            'data-title' => Yii::t("app", "Bloques Detalles"),
                            'data-content' => $detalle->bloque_descripcion,
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                        ]);
                        ?>
                        <?php $chek = ""; ?>
                        <?php
                        foreach ($data->tmpBloques as $selecBloque) {
                            if ($selecBloque->bloque_id == $detalle->bloque_id) {
                                $chek = 'checked="checked"';
                            }
                        }
                        ?>       
                        <input type="checkbox" id="bloque_<?php echo $detalle->bloque_id; ?>" name="bloque[<?php echo $detalle->bloque_id; ?>]" <?php echo $chek ?>>
                    </div>
        <?php endif; ?>
                <div class="form-group" id="detalle_<?php echo $detalle->id ?>">
                    <div class="control-group">
                        <label class="control-label col-sm-9">
                        <?php echo $detalle->pregunta; ?>
                        </label>
                        <?php
                        $decisionSiConTipi .="$('#detalle_" . $detalle->id . "').hide('slow');
                                    $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',true);"
                                . "$('#tipificacion_" . $detalle->id . "').hide('slow')";
                        $decisionNoConTipi .= "$('#detalle_" . $detalle->id . "').show('slow');
                                    $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',false);";
                        $decisionSi .="$('#detalle_" . $detalle->id . "').hide('slow');
                                    $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',true);";
                        $decisionNo .= "$('#detalle_" . $detalle->id . "').show('slow');
                                    $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',false);";
                        ?>
                        <div class="col-sm-3">
                            <?php if ($data->fill_values == true): ?>
                                <?php echo isset($data->calificaciones[$detalle->calificacion_id][$detalle->calificaciondetalle_id]) ? $data->calificaciones[$detalle->calificacion_id][$detalle->calificaciondetalle_id]["name"] : '' ?>
        <?php else: ?>
                                <select 
                                    name="calificaciones[<?php echo $detalle->id ?>]" 
                                    class="form-control toggleTipificacion" 
                                    data-id-detalle="<?php echo $detalle->id ?>" 
                                    id="calificacion_<?php echo $detalle->id ?>"
                                    >
                                    <option value=""></option>
                                    <?php if (isset($data->calificaciones[$detalle->calificacion_id])): ?>
                                        <?php foreach ($data->calificaciones[$detalle->calificacion_id] as $id => $c): ?>
                                            <?php $selected = ($detalle->calificaciondetalle_id == $id) ? 'selected="selected"' : '' ?>
                                            <option value="<?php echo $id ?>" <?php echo $selected ?>><?php echo $c["name"] ?></option>
                                        <?php endforeach; ?>
                                <?php endif; ?>
                                </select>                       
        <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <?php if ($data->fill_values == true): ?>
            <?php if (isset($detalle->tipif_seleccionados) && !empty($detalle->tipif_seleccionados)): ?>
                            <fieldset id="tipificacion_<?php echo $detalle->id ?>" style="display: <?php echo (empty($detalle->tipif_seleccionados)) ? 'none' : '' ?>">
                                <legend></legend>                        
                <?php foreach ($detalle->tipif_seleccionados as $det_tipif): ?>
                                    <input type="checkbox" 
                                           checked="checked" 
                                           disabled="disabled"
                                           data-id-detalle="<?php echo $detalle->id ?>"
                                           data-det-tipif="<?php echo $det_tipif->id ?>"
                                           data-preview="1"
                                           class="showSubtipificaciones tipificacion_<?php echo $detalle->id ?> tipif" />
                                    &nbsp;
                                    <?php $nmTip = app\models\Tipificaciondetalles::findOne($det_tipif->id); ?>
                    <?php echo $nmTip->name; ?>
                                    <div style="" 
                                         id="div_subtipificaciones_<?php echo $detalle->id . $det_tipif->id ?>">
                                    </div>
                            <?php endforeach; ?>
                            </fieldset>
                        <?php endif; ?>
                    <?php else: ?>
            <?php if (isset($data->tipificaciones[$detalle->tipificacion_id]) && !empty($data->tipificaciones[$detalle->tipificacion_id])): ?>
                            <fieldset id="tipificacion_<?php echo $detalle->id ?>" <?php echo!empty($detalle->tipif_seleccionados) ? '' : 'style="display: none; "' ?>>
                                <legend></legend>
                                <?php foreach ($data->tipificaciones[$detalle->tipificacion_id] as $id => $name): ?>
                                    <?php
                                    $checked = "";
                                    foreach ($detalle->tipif_seleccionados as $dis) {
                                        if ($dis->id == $id) {
                                            $checked = 'checked="checked"';
                                            break;
                                        }
                                    }
                                    ?>
                                    <input type="checkbox" <?php echo $checked ?> 
                                           class="showSubtipificaciones tipificacion_<?php echo $detalle->id ?> tipif" 
                                           name="tipificaciones[<?php echo $detalle->id ?>][]" 
                                           data-id-detalle="<?php echo $detalle->id ?>"
                                           data-det-tipif="<?php echo $id ?>"
                                           data-preview="0"
                                           value="<?php echo $id ?>" />&nbsp;<?php echo $name ?>
                                    <div style="" 
                                         id="div_subtipificaciones_<?php echo $detalle->id . $id ?>">
                                    </div>
                                    <br/>
                            <?php endforeach; ?>
                            </fieldset>
                        <?php endif; ?>
                <?php endif; ?>
                </div>
                <?php $prev_seccion = $detalle->seccion_id ?>
                <?php $prev_sndesplegar_comentario = $detalle->sndesplegar_comentario ?>
                <?php $prev_bloque = $detalle->bloque ?>
                <?php
                $prev_id_bloque = $detalle->bloque_id;
                if ($prev_id_bloque != 0) {
                    $js = " $(document).ready(function(){
                            $('#bloque_" . $prev_id_bloque . "').click(function () {                     
                                      if( $('#bloque_" . $prev_id_bloque . "').is(':checked') ) {
									" . $decisionSiConTipi . "
                                }else{
                                    " . $decisionNoConTipi . "}  
                            });
                            if( $('#bloque_" . $prev_id_bloque . "').is(':checked') ) {
									" . $decisionSi . "
                                }else{
                                    " . $decisionNo . "}

                        });";
                    $decisionSiConTipi = "";
                    $decisionNoConTipi = "";
                    $decisionSi = "";
                    $decisionNo = "";
                    $this->registerJs($js);
                }
                ?>
                <?php $prev_secccion_comentario = trim($detalle->dscomentario); ?>
            <?php endforeach; ?>

    <?php if (!empty($prev_seccion)): ?>
                <div class="form-group row"
                     <?php
                    if ($prev_sndesplegar_comentario == 0) {
                        echo 'style="display: none"';
                    }
                    ?>>
                    <div class="col-sm-12">                
                        <?php if ($data->fill_values == true): ?>
                            <span style="color: #ff0000;"><?php echo $prev_secccion_comentario ?></span>
                        <?php else: ?>
                            <?php if ($data->preview) : ?>
                                <?=
                                Html::textarea("comentarioSeccion[" . $prev_seccion . "]"
                                        , $prev_secccion_comentario
                                        , [
                                    "id" => "txt_comentarios",
                                    "class" => "form-control",
                                    "placeholder" => "Comentario para el Coaching",
                                    "readonly" => "readonly"
                                ]);
                                ?>
                            <?php else: ?>
                                <?=
                                Html::textarea("comentarioSeccion[" . $prev_seccion . "]"
                                        , $prev_secccion_comentario
                                        , [
                                    "id" => "txt_comentarios",
                                    "class" => "form-control",
                                    "placeholder" => "Comentario para el Coaching"
                                ]);
                                ?>
                            <?php endif; ?>

        <?php endif; ?>
                    </div>
                </div>        
    <?php endif; ?>

            <!--<div class="row seccion" <?php
            /* if ($data->info_adicional['problemas'] == 0 &&
              $data->info_adicional['tipo_llamada'] == 0)
              echo "style='display: none'"; */
            ?>>
    <?php //echo Yii::t("app", "Informacion adicional");   ?>
            </div>-->

    <?php if (!empty($data->responsabilidad)) : ?>
                <!-- SECCION PROTECCIÓN DE LA EXPERIENCIA -->
                <div class="row seccion">
        <?php echo Yii::t("app", "PROTECCIÓN DE LA EXPERIENCIA"); ?>
                </div>
                <div class="row well">
                    <?php echo Yii::t("app", "Gestión de la mejora"); ?>
                    <?php
                    echo Html::tag('span', Html::img(Url::to("@web/images/Question.png")), [
                        'data-title' => Yii::t("app", "Bloques Detalles"),
                        'data-content' => Yii::t("app", "Gestión de la mejora"),
                        'data-toggle' => 'popover',
                        'style' => 'cursor:pointer;'
                    ]);
                    ?>                        
                </div>
                <div class="form-group">
                    <div class="control-group">
                        <label class="control-label col-sm-3">
        <?php echo Yii::t("app", "Seleccione la responsabilidad"); ?>                        
                        </label>
                        <div class="col-sm-9">
                            <?php
                            echo Html::dropDownList("responsabilidad"
                                    , $data->basesatisfaccion->responsabilidad
                                    , [
                                'CANAL' => 'CANAL',
                                'MARCA' => 'MARCA',
                                'COMPARTIDA' => 'COMPARTIDA',
                                'EQUIVOCADA' => 'EQUIVOCADA',
                                'NA' => 'N/A'
                                    ]
                                    , ["id" => "responsabilidad",
                                "class" => "form-control",
                                'prompt' => 'Seleccione ...',
                                "disabled" => ($data->preview) ? true : false]);
                            ?>                      
                        </div>
                    </div>
                </div>

                <div class="form-group" id="divcanal" style="display: none">
                    <div class="control-group">
                        <label class="control-label col-sm-3">
        <?php echo Yii::t("app", "Canal"); ?>                        
                        </label>
                        <div class="col-sm-9">
                            <?php
                            echo Html::checkboxList(
                                    'canal[]'
                                    , explode(", ", $data->basesatisfaccion->canal)
                                    , $data->responsabilidad['CANAL']
                                    , [
                                'id' => 'canal'
                                , 'disabled' => ($data->preview) ? true : false
                                , 'separator' => '<br />'
                                    ]
                            );
                            ?>
                            <?php
                            /* echo Html::dropDownList("canal"
                              , $data->basesatisfaccion->canal
                              , $data->responsabilidad['CANAL']
                              , ["id" => "canal",
                              "class" => "form-control",
                              'prompt' => 'Seleccione ...',
                              "disabled" => ($data->preview) ? true : false]); */
                            ?>                      
                        </div>
                    </div>
                </div>

                <div class="form-group" id="divmarca"  style="display: none">
                    <div class="control-group">
                        <label class="control-label col-sm-3">
        <?php echo Yii::t("app", "Marca"); ?>                          
                        </label>
                        <div class="col-sm-9">
                            <?php
                            /* echo Html::dropDownList("marca"
                              , $data->basesatisfaccion->marca
                              , $data->responsabilidad['MARCA']
                              , ["id" => "marca",
                              "class" => "form-control",
                              'prompt' => 'Seleccione ...',
                              "disabled" => ($data->preview) ? true : false]); */
                            ?>
                            <?php
                            echo Html::checkboxList(
                                    'marca[]'
                                    , explode(", ", $data->basesatisfaccion->marca)
                                    , $data->responsabilidad['MARCA']
                                    , [
                                'id' => 'marca'
                                , 'disabled' => ($data->preview) ? true : false
                                , 'separator' => '<br />'
                                    ]
                            );
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="divequivocacion" style="display: none">
                    <div class="control-group">
                        <label class="control-label col-sm-3">
        <?php echo Yii::t("app", "Equivocacion"); ?>                        
                        </label>
                        <div class="col-sm-9">
                            <?php
                            /* echo Html::dropDownList("equivocacion"
                              , $data->basesatisfaccion->equivocacion
                              , $data->responsabilidad['EQUIVOCACION']
                              , ["id" => "equivocacion",
                              "class" => "form-control",
                              'prompt' => 'Seleccione ...',
                              "disabled" => ($data->preview) ? true : false]); */
                            ?> 
                            <?php
                            echo Html::checkboxList(
                                    'equivocacion[]'
                                    , explode(", ", $data->basesatisfaccion->equivocacion)
                                    , $data->responsabilidad['EQUIVOCACION']
                                    , [
                                'id' => 'equivocacion'
                                , 'disabled' => ($data->preview) ? true : false
                                , 'separator' => '<br />'
                                    ]
                            );
                            ?>
                        </div>
                    </div>
                </div>
    <?php endif; ?>
            <!-- FIN SECCION PROTECCIÓN DE LA EXPERIENCIA -->

            <div class="row seccion">
    <?php echo Yii::t("app", "Informacion adicional"); ?>
            </div>

            <div class="row well" <?php
            if ($data->info_adicional['problemas'] == 0) {
                echo "style='display: none'";
            }
            ?>>
                <?php if ($data->fill_values): ?>
        <?php echo Yii::t("app", "Tablero de Experiencias"); ?><br /><br />
                    <table class="table table-striped table-bordered">
                    <caption>Tabla de experiencias</caption>
                        <thead>
                            <tr>
                                <th id="enfoque">Enfoque</th>
                                <th id="problema">Problema</th>  
                                <th id="comentarios">Comentarios</th>
                            </tr>
                        </thead>
                        <tbody>
        <?php foreach ($data->tablaproblemas as $listtablaproblemas): ?>                                                                     
                                <tr>
                                    <td><?php echo $listtablaproblemas->dsenfoque; ?></td>
                                    <td><?php echo $listtablaproblemas->dsproblema; ?></td>
                                    <td><?php echo $listtablaproblemas->detalle; ?></td>
                                </tr>
        <?php endforeach; ?>
                        </tbody>
                    </table>
                    <?php else: ?>
                    <div class="formulario-bloque" colspan="3"><?php echo Yii::t("app", "Tablero de Experiencias"); ?>
                        <?php if ($data->preview == false): ?>
                            <?php
                            echo Html::a(Yii::t("app", "View"), 'javascript:void(0)', [
                                'title' => Yii::t('app', 'Create'),
                                'class' => 'btn-sm btn-success',
                                //'data-pjax' => '0',
                                'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['tmptableroexperiencias/index'
                                    , 'tmp_formulario_id' => $data->formulario_id, 'arbol_id' => $data->tmp_formulario->arbol_id]) . "',
                                success  : function(response) {
                                    $('#ajax_div_problemas').html(response);
                                }
                               });
                               return false;",
                            ]);
                            ?>
                    <?php endif; ?>
                    </div>
    <?php endif; ?>
            </div>

            <div class="row well" <?php
            if ($data->info_adicional['tipo_llamada'] == 0) {
                echo "style='display: none'";
            }
            ?>>
                <?php if ($data->fill_values): ?>
        <?php echo Yii::t("app", "Tiposllamadas"); ?><br /><br />
                    <table class="table table-striped table-bordered">
                    <caption>Tipos llamadas</caption>
                        <thead>
                            <tr>
                                <th id="tipoLlamadas">Tipo de Llamada</th>
                                <th id="llamadas">Llamada</th>
                            </tr>
                        </thead>
                        <tbody>
        <?php foreach ($data->tablallamadas as $listtablallamadas): ?>                                                                       
                                <tr>
                                    <td><?php echo $listtablallamadas["name_tipo_llamada"]; ?></td>
                                    <td><?php echo $listtablallamadas["name_det_llamada"]; ?></td>
                                </tr>
        <?php endforeach; ?>
                        </tbody>
                    </table>                
                    <?php else: ?>
                    <div class="formulario-bloque" colspan="3"><?php echo Yii::t("app", "Tiposllamadas"); ?>
                        <?php if ($data->preview == false): ?>    
                            <?php
                            echo Html::a(Yii::t("app", "View"), 'javascript:void(0)', [
                                'title' => Yii::t('app', 'Create'),
                                'class' => 'btn-sm btn-success',
                                //'data-pjax' => '0',
                                'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['tmptiposllamada/index'
                                    , 'tmp_formulario_id' => $data->formulario_id]) . "',
                                success  : function(response) {
                                    $('#ajax_div_llamdas').html(response);
                                }
                               });
                               return false;",
                            ]);
                            ?>
                    <?php endif; ?>
                    </div>
    <?php endif; ?>
            </div>

            <div class="row well">
                <?php if ($data->fill_values): ?>
        <?php echo Yii::t("app", "Agregar feedback"); ?> <br /><br />
                    <table class="table table-striped table-bordered">
                    <caption>tabla Agregar feedback</caption>
                        <thead>
                            <tr>
                                <th id="comentarioFeedeback">Comentario Feedback</th>
                            </tr>
                        </thead>
                        <tbody>
        <?php foreach ($data->list_Add_feedbacks as $list): ?>                                                                     
                                <tr>                                
                                    <td><?php echo $list->dscomentario; ?></td>
                                </tr>
        <?php endforeach; ?>
                        </tbody>
                    </table>                
                    <?php else: ?>
                    <div class="formulario-bloque" colspan="3"><?php echo Yii::t("app", "Agregar feedback"); ?>
                        <?php if ($data->preview == false): ?>
                            <?php
                            echo Html::a(Yii::t("app", "View"), 'javascript:void(0)', [
                                'title' => Yii::t('app', 'Create'),
                                'class' => 'btn-sm btn-success',
                                //'data-pjax' => '0',
                                'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['tmpejecucionfeedbacks/index'
                                    , 'tmp_formulario_id' => $data->formulario_id
                                    , 'usua_id_lider' => $data->usua_id_lider
                                    , 'evaluado_id' => $data->tmp_formulario->evaluado_id
                                    , 'basesatisfacion_id' => $data->basesatisfaccion->id]) . "',
                                success  : function(response) {
                                    $('#ajax_div_feedbacks').html(response);
                                }
                               });
                               return false;",
                            ]);
                            ?>                
                    <?php endif; ?>
                    </div>
    <?php endif; ?>
            </div>


            <div class="row seccion">
    <?php echo Yii::t('app', 'Gestión del Caso') ?>
            </div>
            <div id="divTablaPreguntas">
                <table id="tablaPreguntas" class="table table-striped table-bordered detail-view">
                <caption>Tabla preguntas</caption>
                    <tr>
                        <th id="estado">Estado</th>
                        <td><?php
                            echo Html::dropDownList("estado"
                                    , $data->basesatisfaccion->estado
                                    , $data->basesatisfaccion->estadosList()
                                    , ["id" => "estado", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]);
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="form-group row">
                <div class="col-sm-12">
                    <?php if ($data->preview) : ?>
                        <?=
                        Html::textarea("comentarios_gral"
                                , $data->tmp_formulario->dscomentario
                                , [
                            "id" => "txt_comentarios_gral",
                            "class" => "form-control",
                            "placeholder" => "Comentario para el Coaching",
                            "readonly" => "readonly"
                        ]);
                        ?>
                    <?php else: ?>
                        <?=
                        Html::textarea("comentarios_gral"
                                , $data->tmp_formulario->dscomentario
                                , [
                            "id" => "txt_comentarios_gral",
                            "class" => "form-control",
                            "placeholder" => "Comentario para el Coaching"
                        ]);
                        ?>
    <?php endif; ?>                
                </div>
            </div>

    <?php if ($data->preview == false): ?>
                <div class="form-group">
                    <div class="col-sm-12 well">
                        <?php /* = Html::submitButton(Yii::t('app', 'Guargar y enviar'), ['class' => 'btn btn-success']) */ ?>
                        <?= Html::a(Yii::t('app', 'Guardar y enviar'), "javascript:void(0)", ['class' => 'btn btn-success soloFinalizar'])
                        ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['cancelarformulario', 'id' => $data->basesatisfaccion->id], ['class' => 'btn btn-default soloCancelar']) ?>

                    </div>        
                </div>
            <?php endif; ?>

            <?php if ($data->preview != true) : ?>
                <?php echo Html::endForm(); ?>
        <?php else: ?>
            </div>
    <?php endif; ?>

    </div>

    <script type="text/javascript">

        $(document).ready(function () {
            var valCalificacionesDespliegaTipificaciones = new Array();
    <?php foreach ($data->calificaciones as $cal_id => $detalle): ?>
        <?php foreach ($detalle as $det_id => $objDetalle): ?>
                    valCalificacionesDespliegaTipificaciones[<?php echo $det_id ?>] = <?php echo $objDetalle["sndespliega_tipificaciones"] ?>;
        <?php endforeach; ?>
    <?php endforeach; ?>
            var idsDetalles = '<?php echo json_encode($detalles_ids); ?>';

            /* MOSTRAR TIPIFICACIONES AL CAMBIAR **********************************/
            $(".toggleTipificacion").change(function () {
                var id_detalle = $(this).data("id-detalle");
                var id_calificacion = $(this).val();
                if (valCalificacionesDespliegaTipificaciones[id_calificacion] == 1) {
                    $("#tipificacion_" + id_detalle).show();
                } else {
                    $("#tipificacion_" + id_detalle).hide();
                    $(".tipificacion_" + id_detalle).each(function (index, check) {
                        //Quita Checkbox seleccionados.
                        check.checked = false;
                    });
                }
            });

            /* MOSTRAR TIPIFICACIONES AL CARGAR ***********************************/
            $(".toggleTipificacion").each(function () {
                var id_detalle = $(this).data("id-detalle");
                var id_calificacion = $(this).val();
                if (valCalificacionesDespliegaTipificaciones[id_calificacion] == 1) {
                    $("#tipificacion_" + id_detalle).show();
                } else {
                    $("#tipificacion_" + id_detalle).hide();
                    $(".tipificacion_" + id_detalle).each(function (index, check) {
                        //Quita Checkbox seleccionados.
                        check.checked = false;
                    });
                }
            });
            /* MOSTRAR SUBTIPIFICACIONES */
            $(".showSubtipificaciones").change(function () {
                var id_detalle = $(this).data("id-detalle");
                var id_tipif = $(this).data("det-tipif");
                var preview = $(this).data("preview");
                if ($(this).is(':checked')) {
                    ruta = '<?php echo Url::to(['formularios/showsubtipif']); ?>?id_detalle=' + id_detalle + '&id_tipificacion=' + id_tipif + '&preview=' + preview;
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).css("margin", "20px 0 0 50px");
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).addClass('divSubtipif');
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).addClass('well');
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).load(ruta, function (response, status, xhr) {
                        if (response == "") {
                            $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeClass("well");
                            $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).html("");
                            $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeAttr("style");
                        }
                    }
                    );
                } else {
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeAttr("style");
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeClass('divSubtipif');
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeClass('well');
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).html("");
                }
            });
            /* MOSTRAR SUBTIPIFICACIONES SI SE CARGA LA PAGINA CON DATOS CREADOS */
            $(".tipif").each(function () {
                if ($(this).is(':checked')) {
                    var id_detalle = $(this).data("id-detalle");
                    var id_tipif = $(this).data("det-tipif");
                    var preview = $(this).data("preview");
                    ruta = '<?php echo Url::to(['formularios/showsubtipif']); ?>?id_detalle=' + id_detalle + '&id_tipificacion=' + id_tipif + '&preview=' + preview;
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).css("margin", "20px 0 0 50px");
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).addClass('divSubtipif');
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).addClass('well');
                    $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).load(ruta, function (response, status, xhr) {
                        if (response == "") {
                            $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeClass("well");
                            $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).html("");
                            $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeAttr("style");
                        }
                    });
                }
            });

            $("#guardarFormulario").submit(function (e) {
                if ($("#submitcorrecto").val() == "NO") {
                    e.preventDefault();
                }
            });

            /* BOTÓN GUARDAR Y ENVIAR */
            $(".soloFinalizar").click(function () {
                $("#submitcorrecto").val("SI");
                $(this).attr("disabled", "disabled");
                //$(".soloGuardar").attr("disabled", "disabled");
                $(".soloCancelar").attr("disabled", "disabled");
                var guardarFormulario = $("#guardarFormulario");
                guardarFormulario.attr('action', '<?php echo Url::to(['basesatisfaccion/guardaryenviarformulariogestion']); ?>');
                var valid = validarFormulario();
                if (valid) {
                    guardarFormulario.submit();
                } else {
                    $("#submitcorrecto").val("NO");
                    $(this).removeAttr("disabled");
                    $(".soloGuardar").removeAttr("disabled");
                    $(".soloCancelar").removeAttr("disabled");
                }
            });


            /* BOTÓN PARA BORRAR EL FORMULARIO */
            $(".soloCancelar").click(function () {
                $("#submitcorrecto").val("SI");
                $(this).attr("disabled", "disabled");
                $(".soloFinalizar").attr("disabled", "disabled");
                $(".soloGuardar").attr("disabled", "disabled");
                var guardarFormulario = $("#guardarFormulario");
                var tmp_form = $("#tmp_formulario_id").val();
                ruta = '<?php echo Url::to(['eliminartmpform']); ?>?&tmp_form=' + tmp_form;
                guardarFormulario.attr('action', ruta);
                guardarFormulario.submit();
            });

            /* RESPONSABILIDAD */
            $("#responsabilidad").change(function () {
                if ($(this).val() === 'CANAL') {
                    $("#divcanal").show('slow');
                    $("#divmarca").hide('slow');
                    $("#divequivocacion").hide('slow');
                    $("#marca").val('');
                    $("#equivocacion").val('');

                }
                if ($(this).val() === 'MARCA') {
                    $("#divmarca").show('slow');
                    $("#divcanal").hide('slow');
                    $("#divequivocacion").hide('slow');
                    $("input[name='canal[]']:checked").attr('checked', false);
                    $("input[name='equivocacion[]']:checked").attr('checked', false);
                }
                if ($(this).val() === 'EQUIVOCADA') {
                    $("#divequivocacion").show('slow');
                    $("#divcanal").hide('slow');
                    $("#divmarca").hide('slow');
                    $("input[name='canal[]']:checked").attr('checked', false);
                    $("input[name='marca[]']:checked").attr('checked', false);
                }
                if ($(this).val() === 'COMPARTIDA') {
                    $("#divcanal").show('slow');
                    $("#divmarca").show('slow');
                    $("#divequivocacion").hide('slow');
                    $("input[name='equivocacion[]']:checked").attr('checked', false);
                }
                if ($(this).val() === 'NA') {
                    $("#divcanal").hide('slow');
                    $("#divmarca").hide('slow');
                    $("#divequivocacion").hide('slow');
                    $("input[name='equivocacion[]']:checked").attr('checked', false);
                    $("input[name='canal[]']:checked").attr('checked', false);
                    $("input[name='marca[]']:checked").attr('checked', false);
                }
            });

            if ($("#responsabilidad").val() !== '') {
                if ($("#responsabilidad").val() === 'CANAL') {
                    $("#divcanal").show('slow');
                }
                if ($("#responsabilidad").val() === 'MARCA') {
                    $("#divmarca").show('slow');
                }
                if ($("#responsabilidad").val() === 'EQUIVOCADA') {
                    $("#divequivocacion").show('slow');
                }
                if ($("#responsabilidad").val() === 'COMPARTIDA') {
                    $("#divcanal").show('slow');
                    $("#divmarca").show('slow');
                }
            }

        });

        function validarFormulario() {
            var hayErrores = false;
            var idsDetalles = '<?php echo json_encode($detalles_ids); ?>';
            var cont = 0;
            var contTotal = <?php echo count($data->totalBloques) ?>;
            var idsDetalles = new Array();
    <?php foreach ($detalles_ids as $key => $val): ?>
                idsDetalles[<?php echo $key ?>] = <?php echo $val ?>;
    <?php endforeach; ?>

            var valCalificacionesDespliegaTipificaciones = new Array();
    <?php foreach ($data->calificaciones as $cal_id => $detalle): ?>
        <?php foreach ($detalle as $det_id => $objDetalle): ?>
                    valCalificacionesDespliegaTipificaciones[<?php echo $det_id ?>] = <?php echo $objDetalle["sndespliega_tipificaciones"] ?>;
        <?php endforeach; ?>
    <?php endforeach; ?>
            var totalBloques = new Array();
    <?php foreach ($data->totalBloques as $tmpbloque): ?>
                totalBloques[<?php echo $tmpbloque->bloque_id ?>] = <?php echo $tmpbloque->bloque_id ?>;
    <?php endforeach; ?>

            try {
                //validar responsabilidad                
                if ($("#responsabilidad").val() === '') {
                    $("#responsabilidad").addClass('field-error');
                    hayErrores = true;
                } else {
                    $("#responsabilidad").removeClass('field-error');
                    if (($("#responsabilidad").val() === 'CANAL' && !$("input[name='canal[]']:checked").val())
                            || ($("#responsabilidad").val() === 'COMPARTIDA' && !$("input[name='canal[]']:checked").val())) {
                        $("#canal").addClass('field-error');
                        hayErrores = true;
                    } else {
                        $("#canal").removeClass('field-error');
                    }
                    if (($("#responsabilidad").val() === 'MARCA' && !$("input[name='marca[]']:checked").val())
                            || ($("#responsabilidad").val() === 'COMPARTIDA' && !$("input[name='marca[]']:checked").val())) {
                        $("#marca").addClass('field-error');
                        hayErrores = true;
                    } else {
                        $("#marca").removeClass('field-error');
                    }
                    if ($("#responsabilidad").val() === 'EQUIVOCADA' && !$("input[name='equivocacion[]']:checked").val()) {
                        $("#equivocacion").addClass('field-error');
                        hayErrores = true;
                    } else {
                        $("#equivocacion").removeClass('field-error');
                    }
                }



                $.each(totalBloques, function (l, sel) {
                    if ($('#bloque_' + sel).is(':checked')) {
                        cont++;
                    }
                });
                $.each(idsDetalles, function (i, val) {

                    //Primero se valida que se haya seleccionado la calificacion.
                    var a = $("#calificacion_" + val).attr('disabled');
                    if (a !== 'disabled') {
                        if ($("#calificacion_" + val).val() == '') {
                            $("#calificacion_" + val).addClass('field-error');
                            hayErrores = true;
                        } else {
                            $("#calificacion_" + val).removeClass('field-error');
                            var id_calificacion = $("#calificacion_" + val).val();
                            //Si se seleccionó calificacion y tiene la marca de desplega tipificaciones
                            // , se debe seleccionar tipificacion.
                            if (valCalificacionesDespliegaTipificaciones[id_calificacion] == 1) {
                                var AnyChecked = false;
                                var AnyExists = false;

                                $(".tipificacion_" + val).each(function (index, check) {
                                    AnyExists = true;
                                    if (check.checked == true) {
                                        AnyChecked = true;
                                    }
                                });

                                if (!AnyChecked && AnyExists) {
                                    $("#tipificacion_" + val).addClass("field-error");
                                    $("#tipificacion_" + val).show();
                                    hayErrores = true;
                                } else {
                                    $("#tipificacion_" + val).removeClass("field-error");
                                }

                                var AnySubTipifChecked = false;
                                var AnySubTipifExists = false;

                                $("#tipificacion_" + val + " .divSubtipif input[type=checkbox]").each(function (k, check) {
                                    AnySubTipifExists = true;
                                    if (check.checked == true) {
                                        AnySubTipifChecked = true;
                                    }
                                });

                                if (AnySubTipifExists && !AnySubTipifChecked) {
                                    $("#tipificacion_" + val + " .divSubtipif").addClass("field-error");
                                    $("#tipificacion_" + val + " .divSubtipif").show();
                                    hayErrores = true;
                                } else {
                                    $("#tipificacion_" + val + " .divSubtipif").removeClass("field-error");
                                }
                            }
                        }
                    }
                });
                if ($("#fuente").val() == '') {
                    $("#fuente").addClass('field-error');
                    hayErrores = true;
                } else
                    $("#fuente").removeClass("field-error");


                if ($("#dimension").val() == '') {
                    $("#dimension").addClass('field-error');
                    hayErrores = true;
                } else
                    $("#dimension").removeClass("field-error");

                if ($("#estado").val() == '') {
                    $("#estado").addClass('field-error');
                    hayErrores = true;
                } else
                    $("#estado").removeClass("field-error");

                if (hayErrores) {
                    $('#modalCampos').modal('show');
                    return false;
                }
                if (cont === contTotal) {
                    $('#modalBloques').modal('show');
                    return false;
                }
                return true;
                //return false;
            } catch (err) {
                alert("Error al validar el formulario." + err);
                return false;
            }
        }
    </script>
<?php else: ?>
    <div class="form-horizontal">
        <!-- ALERT PARA CAMPOS SIN LLENAR -->

        <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
        ?>
        <div class="row seccion">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>
        <div id="divTablaPreguntas">
            <table id="tablaPreguntas" class="table table-striped table-bordered detail-view">
            <caption>tabla preguntas</caption>
                <tr>
                    <th scope="col">
                        <?php
                        if (!empty($data->basesatisfaccion->buzon)) {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "Grabación buzón"), $data->basesatisfaccion->buzon, ['target' => $data->basesatisfaccion->buzon, "href" => $data->basesatisfaccion->buzon]);
                        } else {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "No se encontró buzón"), $data->basesatisfaccion->buzon);
                        }
                        ?>
                    </th>
                </tr>
                <tr>
                    <td>
                        <?php
                        if (!empty($data->basesatisfaccion->llamada)) {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "Grabación Llamada"), $data->basesatisfaccion->llamada, ['target' => $data->basesatisfaccion->llamada, "href" => $data->basesatisfaccion->llamada]);
                        } else {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "No se encontró llamada"), $data->basesatisfaccion->llamada);
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </div>
        <div class="row seccion">
        <?php echo Yii::t('app', 'Datos que se deben exportar') ?>
        </div>
    <?php echo Html::beginForm(Url::to(['']), "post", ["class" => "form-horizontal", "id" => "guardarFormulario"]); ?>
        <div class="form-group row">
            <div class="col-sm-12">
                <table class="table table-striped table-bordered detail-view">
                <caption>Tabla Satisfacción</caption>
                    <tbody>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "ANI"); ?></th>
                            <td><?php echo $data->basesatisfaccion->ani ?></td>
                        </tr>
                        <tr>
                            <th id="identificacion"><?php echo Yii::t("app", "Identificación"); ?></th>
                            <td><?php echo $data->basesatisfaccion->identificacion ?></td>
                        </tr>
                        <tr>
                            <th id="nombre"><?php echo Yii::t("app", "Nombre"); ?></th>
                            <td><?php echo $data->basesatisfaccion->nombre ?></td>
                        </tr>
                        <tr>
                            <th id="ext"><?php echo Yii::t("app", "Ext"); ?></th>
                            <td><?php echo $data->basesatisfaccion->ext ?></td>
                        </tr>
                        <tr>
                            <th id="tipoServicio"><?php echo Yii::t("app", "Tipo Servicio"); ?></th>
                            <td><?php echo $data->basesatisfaccion->tipo_servicio ?></td>
                        </tr>
                        <tr>
                            <th id="tipoEncuesta"><?php echo Yii::t("app", "Tipo encuesta"); ?></th>
                            <td><?php echo $data->basesatisfaccion->tipo_encuesta ?></td>
                        </tr>
                        <tr>
                            <th id="liderEquipo"><?php echo Yii::t("app", "Lider Equipo"); ?></th>
                            <td><?php echo $data->basesatisfaccion->lider_equipo ?></td>
                        </tr>
                        <tr>
                            <th id="programaPcrc"><?php echo Yii::t("app", "Programa/PCRC"); ?></th>
                            <td><?php echo $data->basesatisfaccion->pcrc0->name ?></td>
                        </tr>
                        <tr>
                            <th id="cliente"><?php echo Yii::t("app", "Cliente"); ?></th>
                            <td><?php echo $data->basesatisfaccion->cliente0->name ?></td>
                        </tr>
                        <tr>
                            <th id="rn"><?php echo Yii::t("app", "RN"); ?></th>
                            <td><?php echo $data->basesatisfaccion->rn ?></td>
                        </tr>
                        <tr>
                            <th id="agente"><?php echo Yii::t("app", "Agente"); ?></th>
                            <td><?php echo $data->basesatisfaccion->agente ?></td>
                        </tr>
                        <tr>
                            <th id="tipologia"><?php echo Yii::t("app", "Tipología"); ?></th>
                            <td><?php
                                echo Html::dropDownList("categoria"
                                        , $data->basesatisfaccion->tipologia
                                        , $data->recategorizar
                                        , ["id" => "categoria", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]);
                                ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta1"><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->preguntas['0']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta1 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta2"><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->preguntas['1']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta2 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta3"><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->preguntas['2']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta3 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta4"><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->preguntas['3']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta4 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta5"><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->preguntas['4']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta5 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta6"><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->preguntas['5']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta6 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta7"><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->preguntas['6']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta7 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta8"><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->preguntas['7']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta8 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th id="pregunta9"><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->preguntas['8']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta9 : 'NO APLICA' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12 well">
            <?=
            Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-danger'])
            ?>
        </div>        
    </div>
<?php endif; ?>
