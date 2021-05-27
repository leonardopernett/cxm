<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
?>
<?php
$redct = ($model->tipo_inbox == 'ALEATORIO') ? 'inboxaleatorio' : 'index';
$this->title = Yii::t('app', 'Formulario');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Gestion Satisfaccion'), 'url' => [$redct]];
$this->params['breadcrumbs'][] = $this->title;
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
$selected = null;
?>
<?php echo Html::tag('div', '', ['id' => 'ajax_div_feedbacks']); ?>
<?php if ($formulario): ?>
    <div class="form-horizontal">
        <!-- ALERT PARA CAMPOS SIN LLENAR -->
        <?php
        \yii\bootstrap\Modal::begin([
            'id' => 'modalCampos'
            , 'header' => "Advertencia"
            , 'size' => \yii\bootstrap\Modal::SIZE_SMALL
        ]);
        echo Yii::t("app", "Campos sin seleccionar");
        \yii\bootstrap\Modal::end();

        \yii\bootstrap\Modal::begin([
            'id' => 'modalEstado'
            , 'header' => "Advertencia"
            , 'size' => \yii\bootstrap\Modal::SIZE_SMALL
        ]);
        echo Yii::t("app", "Este formulario no podrá ser gestionado con el estado seleccionado");
        \yii\bootstrap\Modal::end();
        ?>
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
                <tr>
                    <td>
                        <?php
                        if (!empty($model->buzon)) {
                            $url_buzon = explode("/web/", $model->buzon);                            
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "Grabación buzón"), Url::to("@web/" . $url_buzon[1]), ['target' => "_blank"]);
                        } else {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "No se encontró buzón"), $model->buzon);
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        if (!empty($model->llamada)) {
                            $llamada = json_decode($model->llamada);                            
                            if(count($llamada) > 1){
                                $i = 1;
                                foreach ($llamada as $value) {
                                    echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "Grabación Llamada") . " - " . $i . " ", $value->llamada, ['target' => "_blank"]);
                                    $i++;
                                }                                
                            }else{
                                echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "Grabación Llamada"), $llamada[0]->llamada, ['target' => "_blank"]);
                            }                           
                        } else {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "No se encontró llamada"), $model->llamada);
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
                    <tbody
                        <tr>
                            <th><?php echo Yii::t("app", "ANI"); ?></th>
                            <td><?php echo $model->ani ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Identificación"); ?></th>
                            <td><?php echo $model->identificacion ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Nombre"); ?></th>
                            <td><?php echo $model->nombre ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Ext"); ?></th>
                            <td><?php echo $model->ext ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Tipo Servicio"); ?></th>
                            <td><?php echo $model->tipo_servicio ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Tipo encuesta"); ?></th>
                            <td><?php echo $model->tipo_encuesta ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Lider Equipo"); ?></th>
                            <td><?php echo $model->lider_equipo ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Programa/PCRC"); ?></th>
                            <td><?php echo $model->pcrc0->name ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Cliente"); ?></th>
                            <td><?php echo $model->cliente0->name ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "RN"); ?></th>
                            <td><?php echo $model->rn ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Agente"); ?></th>
                            <td><?php echo $model->agente ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Tipología"); ?></th>
                            <td><?php
                                echo Html::dropDownList("categoria"
                                        , $model->tipologia
                                        , $data->recategorizar
                                        , ["id" => "categoria", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => (!$data->bandera) ? true : false]);
                                ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Connid"); ?></th>
                            <td><?php echo $model->connid; ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->preguntas['0']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $model->pregunta1 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->preguntas['1']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $model->pregunta2 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->preguntas['2']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $model->pregunta3 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->preguntas['3']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $model->pregunta4 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->preguntas['4']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $model->pregunta5 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->preguntas['5']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $model->pregunta6 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->preguntas['6']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $model->pregunta7 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->preguntas['7']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $model->pregunta8 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->preguntas['8']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $model->pregunta9 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['9']['nombre']) != 'NO APLICA') ? $data->preguntas['9']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['9']['nombre']) != 'NO APLICA') ? $model->pregunta10 : 'NO APLICA' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>



        <div class="col-sm-12">
            <?php foreach ($data->datoSeccion as $seccion): ?>
                <div class="row seccion">
                    <?php echo $seccion->name ?>
                </div>
                <?php foreach ($data->datoBloque as $bloque): ?>
                    <?php if ($bloque->seccion_id == $seccion->id): ?>
                        <div class="row well">
                            <?php echo $bloque->name; ?>
                            <?php
                            echo Html::tag('span', Html::img(Url::to("@web/images/Question.png")), [
                                'data-title' => Yii::t("app", "Bloques Detalles"),
                                'data-content' => $bloque->dsdescripcion,
                                'data-toggle' => 'popover',
                                'style' => 'cursor:pointer;'
                            ]);
                            ?>
                            <input type="checkbox" id="<?php echo $bloque->id; ?>" name="despliega[<?php echo $bloque->id; ?>]" <?php
                            foreach ($data->selecBloque as $key => $value) {
                                if ($bloque->id == $value->text_pregunta) {
                                    $js = " $(document).ready(function(){
                                        $('#bloque" . $bloque->id . "').hide('slow');
                                        $('#bloque" . $bloque->id . "').children().children().children().find('select').attr('disabled',true);
                                         });";
                                    echo 'checked="checked"';
                                    break;
                                }
                            }
                            $this->registerJs($js);
                            ?>>
                                   <?php
                                   $js = " $(document).ready(function(){
                            $('#" . $bloque->id . "').click(function () {
                               if( $('#" . $bloque->id . "').is(':checked') ) {
                                    $('#bloque" . $bloque->id . "').hide('slow');
                                    $('#bloque" . $bloque->id . "').children().children().children().find('select').attr('disabled',true);
                                }else{
                                    $('#bloque" . $bloque->id . "').show('slow');
                                    $('#bloque" . $bloque->id . "').children().children().children().find('select').attr('disabled',false);
                                }
                            });
                        });";
                                   $this->registerJs($js);
                                   ?>
                        </div>
                        <div id="bloque<?php echo $bloque->id; ?>" style="display:block;">
                            <input type="hidden" id="bloqueid_[<?php echo $bloque->id; ?>]" name="bloque_[<?php echo $bloque->id; ?>]" value="<?php echo $bloque->id; ?>"/>
                            <?php foreach ($data->datoBloqueDetalle as $detalles): ?>
                                <?php foreach ($detalles as $detalle): ?>
                                    <?php if ($detalle->bloque_id == $bloque->id): ?>

                                        <div class="form-group">
                                            <div class="control-group">
                                                <label class="control-label col-sm-9">
                                                    <?php echo Yii::t('app', "$detalle->name"); ?>
                                                </label>
                                                <div class="col-sm-3">
                                                    <select 
                                                        name="bloque[<?php echo $bloque->id; ?>][<?php echo $detalle->id ?>][<?php echo $detalle->id ?>]" 
                                                        class="form-control toggleTipificacion" 
                                                        data-id-detalle="<?php echo $detalle->id ?>" 
                                                        id="<?php echo $detalle->id ?>"
                                                        <?php echo (!$data->bandera) ? 'disabled="disabled"' : ""; ?>
                                                        >
                                                        <option value=""></option>
                                                        <?php foreach ($detalle->calificaciones as $calificacion): ?>
                                                            <?php
                                                            foreach ($data->respuestas as $key => $value) {
                                                                $selected = null;
                                                                if (($detalle->id === $value->text_pregunta) && ($calificacion['name'] === $value->respuesta)) {
                                                                    $selected = 'selected="selected"';
                                                                    break;
                                                                }
                                                            }
                                                            ?>
                                                            <option  value="<?php
                                                            echo $calificacion['name'];
                                                            ?>" <?php echo $selected ?>  datadespliega="<?php
                                                                     echo $calificacion['sndespliega_tipificaciones'];
                                                                     ?>"><?php echo $calificacion['name']; ?>
                                                            </option>
                                                        <?php endforeach; ?>
                                                    </select>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="form-group">
                                            <?php if (isset($detalle->tipificaciones)): ?>
                                                <fieldset id="tipificacion_<?php echo $detalle->id ?>" style="display: none;">
                                                    <?php foreach ($detalle->tipificaciones as $tipificacion): ?>
                                                        <?php
                                                        $checked = '';
                                                        foreach ($data->respuestasTipificacion as $key => $value) {
                                                            //var_dump($tipificacion['id'].' '.$value->tipificacion_id);
                                                            if (($tipificacion['id'] == $value->tipificacion_id)) {
                                                                $checked = 'checked="checked"';
                                                                break;
                                                            }
                                                        }
                                                        ?>

                                                        <input  data-id="<?php echo $tipificacion['id'] ?>" 
                                                        <?php echo $checked ?>
                                                        <?php echo (!$data->bandera) ? 'disabled="disabled"' : ""; ?>
                                                                type="checkbox" class="showSubtipificaciones tipificacion_<?php echo $detalle->id ?> tipif"
                                                                name="bloque[<?php echo $bloque->id; ?>][<?php echo $detalle->id ?>][tipificaciones][<?php echo $tipificacion['id'] ?>][<?php echo $tipificacion['id'] ?>]"
                                                                value="<?php echo $tipificacion['name'] ?>"
                                                                data-id-detalle="<?php echo $detalle->id ?>"
                                                                data-id-bloque="<?php echo $bloque->id; ?>"
                                                                data-det-tipif="<?php echo $tipificacion['subtipificacion_id'] ?>"
                                                                data-preview="1"
                                                                data-bandera="<?php echo ($data->bandera == 1) ? "1" : "0"; ?>"
                                                                />&nbsp;<?php echo $tipificacion['name'] ?>
                                                        <div style="" 
                                                             id="div_subtipificaciones_<?php echo $detalle->id . $tipificacion['id'] ?>">
                                                        </div>

                                                    <?php endforeach; ?>
                                                </fieldset>
                                            <?php endif; ?>
                                        </div>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>                              
                <div class="form-group row" <?php
                $comentario = '';
                foreach ($data->respuestas as $key => $value) {
                    if (($value->text_pregunta == ("comentarioseccion_" . $seccion->id))) {
                        $comentario = '' . $value->respuesta;
                        break;
                    }
                }
                if ($seccion->sndesplegar_comentario == 0 && $comentario == '') {
                    echo 'style="display: none"';
                }
                ?>>
                    <div class="col-sm-12"> 
                        <label id="comentariolabelseccion_<?php echo $seccion->id; ?>">Comentario para el Coaching</label>
                        <textarea <?php echo (!$data->bandera) ? 'disabled="disabled"' : ""; ?> id="comentarioseccion_<?php echo $seccion->id; ?>" name="comentarioseccion_<?php echo $seccion->id; ?>" style="width: 100%;"><?php echo $comentario; ?></textarea>
                    </div>
                </div>
            <?php endforeach; ?>

            <div class="row seccion">
                <?php echo Yii::t('app', 'Gestión del Caso') ?>
            </div>
            <div id="divTablaPreguntas">
                <table id="tablaPreguntas" class="table table-striped table-bordered detail-view">
                    <tr>
                        <th>Estado</th>
                        <td><?php
                            echo Html::dropDownList("estado"
                                    , $model->estado
                                    , $model->estadosList()
                                    , ["id" => "estado", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => (!$data->bandera) ? true : false]);
                            ?>
                        </td>
                    </tr>



                </table><div class="form-group row">
                    <div class="col-sm-12">
                        <?php
                        echo Html::label("Comentario");
                        ?>
                        <?php
                        echo Html::textarea("comentario", $model->comentario, [ "disabled" => (!$data->bandera) ? true : false, "style" => 'width:100%;']);
                        ?>
                    </div>
                </div>
            </div>
            <?php if ($data->bandera): ?>
                <div class="form-group">
                    <div class="col-sm-12 well">
                        <?php /* = Html::submitButton(Yii::t('app', 'Guargar y enviar'), ['class' => 'btn btn-success']) */ ?>
                        <?= Html::a(Yii::t('app', 'Guardar y enviar'), "javascript:void(0)", ['class' => 'btn btn-success soloFinalizar'])
                        ?>
                        <?= Html::a(Yii::t('app', 'Enviar Feedback'), "javascript:void(0)", ['class' => 'btn btn-warning enviarFeedback'])
                        ?>
                        <?= Html::a(Yii::t('app', 'Cancel'), ['cancelarformulario', 'id' => $model->id], ['class' => 'btn btn-default soloCancelar'])?>
                    </div>        
                </div>
            <?php endif; ?>
            <?php echo Html::endForm(); ?>
        </div>
    </div>
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
                <tr>
                    <td>
                        <?php
                        if (!empty($model->buzon)) {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "Grabación buzón"), $model->buzon, ['target' => $model->buzon, "href" => $model->buzon]);
                        } else {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "No se encontró buzón"), $model->buzon);
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php
                        if (!empty($model->llamada)) {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "Grabación Llamada"), $model->llamada, ['target' => $model->llamada, "href" => $model->llamada]);
                        } else {
                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                    . Yii::t("app", "No se encontró llamada"), $model->llamada);
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
                    <tbody
                        <tr>
                            <th><?php echo Yii::t("app", "ANI"); ?></th>
                            <td><?php echo $model->ani ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Identificación"); ?></th>
                            <td><?php echo $model->identificacion ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Nombre"); ?></th>
                            <td><?php echo $model->nombre ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Ext"); ?></th>
                            <td><?php echo $model->ext ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Tipo Servicio"); ?></th>
                            <td><?php echo $model->tipo_servicio ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Tipo encuesta"); ?></th>
                            <td><?php echo $model->tipo_encuesta ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Lider Equipo"); ?></th>
                            <td><?php echo $model->lider_equipo ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Programa/PCRC"); ?></th>
                            <td><?php echo $model->pcrc0->name ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Cliente"); ?></th>
                            <td><?php echo $model->cliente0->name ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "RN"); ?></th>
                            <td><?php echo $model->rn ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Agente"); ?></th>
                            <td><?php echo $model->agente ?></td>
                        </tr>
                        <tr>
                            <th><?php echo Yii::t("app", "Tipología"); ?></th>
                            <td><?php
                                echo Html::dropDownList("categoria"
                                        , $model->tipologia
                                        , $data->recategorizar
                                        , ["id" => "categoria", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => (!$data->bandera) ? true : false]);
                                ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->preguntas['0']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $model->pregunta1 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->preguntas['1']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $model->pregunta2 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->preguntas['2']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $model->pregunta3 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->preguntas['3']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $model->pregunta4 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->preguntas['4']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $model->pregunta5 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->preguntas['5']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $model->pregunta6 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->preguntas['6']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $model->pregunta7 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->preguntas['7']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $model->pregunta8 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->preguntas['8']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $model->pregunta9 : 'NO APLICA' ?></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="form-group">
        <div class="col-sm-12 well">
            <?=
            Html::a(Yii::t('app', 'Cancel'), [$redct], ['class' => 'btn btn-danger'])
            ?>
        </div>        
    </div>
<?php endif; ?>
<script type="text/javascript">
    $(document).ready(function () {
        /* MOSTRAR TIPIFICACIONES AL CAMBIAR **********************************/
        $(".toggleTipificacion").change(function () {
            var id_detalle = $(this).data("id-detalle");
            var id_calificacion = $(this).val();
            var sndespliega = $('#' + id_detalle + ' option:selected').attr('datadespliega');
            if (sndespliega == 1) {
                $("#tipificacion_" + id_detalle).show();
            } else {
                $("#tipificacion_" + id_detalle).hide();
                $(".tipificacion_" + id_detalle).each(function (index, check) {                     //Quita Checkbox seleccionados.
                    check.checked = false;
                });
            }
        });

        /* MOSTRAR TIPIFICACIONES AL CARGAR ***********************************/
        $(".toggleTipificacion").each(function () {
            var id_detalle = $(this).data("id-detalle");
            var id_calificacion = $(this).val();
            var sndespliega = $('#' + id_detalle + ' option:selected').attr('datadespliega');
            if (sndespliega == 1) {
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
            var id_bloque = $(this).data("id-bloque");
            var preview = $(this).data("preview");
            var id = $(this).data("id");
            var bandera = $(this).data("bandera");
            if (id_tipif == null || id_tipif == '') {
                return;
            }
            if ($(this).is(':checked')) {
                ruta = '<?php echo Url::to(['basesatisfaccion/showsubtipif']); ?>?id_detalle=' + id_detalle + '&id_tipificacion_padre=' + id_tipif + '&preview=' + preview + '&id=' +<?php echo $model->id ?> + '&id_bloque=' + id_bloque + '&id_tipif=' + id + '&bandera=' + bandera;
                $("#div_subtipificaciones_" + id_detalle + '' + id).css("margin", "20px 0 0 50px");
                $("#div_subtipificaciones_" + id_detalle + '' + id).addClass('divSubtipif');
                $("#div_subtipificaciones_" + id_detalle + '' + id).addClass('well');
                $("#div_subtipificaciones_" + id_detalle + '' + id).load(ruta, function (response, status, xhr) {
                    if (response == "") {
                        $("#div_subtipificaciones_" + id_detalle + '' + id).removeClass("well");
                        $("#div_subtipificaciones_" + id_detalle + '' + id).html("");
                        $("#div_subtipificaciones_" + id_detalle + '' + id).removeAttr("style");
                    }
                }
                );
            } else {
                $("#div_subtipificaciones_" + id_detalle + '' + id).removeAttr("style");
                $("#div_subtipificaciones_" + id_detalle + '' + id).removeClass('divSubtipif');
                $("#div_subtipificaciones_" + id_detalle + '' + id).removeClass('well');
                $("#div_subtipificaciones_" + id_detalle + '' + id).html("");
            }
        });

        /* MOSTRAR SUBTIPIFICACIONES SI SE CARGA LA PAGINA CON DATOS CREADOS */
        $(".tipif").each(function () {
            if ($(this).is(':checked')) {
                var id_detalle = $(this).data("id-detalle");
                var id_tipif = $(this).data("det-tipif");
                var preview = $(this).data("preview");
                var id_bloque = $(this).data("id-bloque");
                var id = $(this).data("id");
                var bandera = $(this).data("bandera");
                if (id_tipif == null || id_tipif == '') {
                    return;
                }
                ruta = '<?php echo Url::to(['basesatisfaccion/showsubtipif']); ?>?id_detalle=' + id_detalle + '&id_tipificacion_padre=' + id_tipif + '&preview=' + preview + '&id=' +<?php echo $model->id ?> + '&id_bloque=' + id_bloque + '&id_tipif=' + id + '&bandera=' + bandera;
                $("#div_subtipificaciones_" + id_detalle + '' + id).css("margin", "20px 0 0 50px");
                $("#div_subtipificaciones_" + id_detalle + '' + id).addClass('divSubtipif');
                $("#div_subtipificaciones_" + id_detalle + '' + id).addClass('well');
                $("#div_subtipificaciones_" + id_detalle + '' + id).load(ruta, function (response, status, xhr) {
                    if (response == "") {
                        $("#div_subtipificaciones_" + id_detalle + '' + id).removeClass("well");
                        $("#div_subtipificaciones_" + id_detalle + '' + id).html("");
                        $("#div_subtipificaciones_" + id_detalle + '' + id).removeAttr("style");
                    }
                });
            }
        });

        $(".soloFinalizar").click(function () {
            datos = fnForm2Array('guardarFormulario');
            var bandera = false;
            for (i = 0; i < datos.length; i++) {
                if (datos[i]['value'] === null || datos[i]['value'] === '') {
                    if (datos[i]['habilitado'] !== 'disabled') {
                        $('#modalCampos').modal('show');
                        bandera = true;
                    }
                }
            }
            var hayErrores = bandera;
            var idsDetalles = new Array();
            <?php $count = 0; ?>
            <?php foreach ($data->datoBloqueDetalle as $datos): ?>
                <?php for ($i = 0; $i < count($datos); $i++): ?>
                        idsDetalles[<?php echo $count ?>] = <?php echo $datos[$i]->id ?>;
                    <?php $count++; ?>
                <?php endfor; ?>
            <?php endforeach; ?>
            // volver a poner validacion q  tenia antes 
            if (bandera) {
                cambiarClass('guardarFormulario');
                $('#modalCampos').modal('show');
            }
            try {
                $.each(idsDetalles, function (i, val) {
                    //Primero se valida que se haya seleccionado la calificacion.
                    if ($("#" + val).val() == '') {
                        //hayErrores = true;
                    } else {

                        var sndespliega = $('#' + val + ' option:selected').attr('datadespliega');


                        //Si se seleccion󠣡lificacion y tiene la marca de desplega tipificaciones
                        // , se debe seleccionar tipificacion.
                        if (sndespliega == 1) {
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
                });
                if (hayErrores) {
                    $('#modalCampos').modal('show');
                    return;
                }
            } catch (err) {
                alert("Error al validar el formulario." + err);
                return false;
            }
            datos = fnForm2Array2('guardarFormulario');
            ruta = '<?php echo Url::to(['guardarformulario', "id" => $model->id, "ajax" => false]); ?>';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: datos
            });
        });
        $(".enviarFeedback").click(function () {
            datos = fnForm2Array2('guardarFormulario');
            ruta = '<?php echo Url::to(['guardarformulario', "id" => $model->id, "ajax" => true]); ?>';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: datos
            });
            ruta = '<?php echo Url::to(['/feedback/create']); ?>';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: {
                    'id':<?php echo $model->id ?>
                },
                success: function (response) {
                    $('#ajax_div_feedbacks').html(response);
                }
            });
        });

        $("#estado").change(function () {
            if ($("#estado").val() === "Cerrado") {
                $('#modalEstado').modal('show');
            }
        });
    });
    function fnForm2Array(strForm) {
        var arrData = new Array();
        $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select", $('#' + strForm)).each(function () {
            if ($(this).attr('name') && $(this).attr("disabled") !== 'disabled') {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val(), 'habilitado': $(this).attr("disabled")});
            }
        });
        return arrData;
    }
    function fnForm2Array2(strForm) {
        var arrData = new Array();
        $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
            if ($(this).attr('name') && $(this).attr("disabled") !== 'disabled') {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val(), 'habilitado': $(this).attr("disabled")});
            }
        });
        return arrData;
    }
    function cambiarClass(strForm) {
        var arrData = new Array();
        $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select", $('#' + strForm)).each(function () {
            if ($(this).val() == '') {
                $(this).addClass("field-error");
            } else {
                $(this).removeClass("field-error");
            }

        });
        return arrData;
    }
</script>