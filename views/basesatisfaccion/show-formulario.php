<?php
include '../views/plantillasForm/plantilla' . $data->formulario->id_plantilla_form . '.php';

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
?>


<?php $this->title = Yii::t('app', 'Realizar monitoreo'); ?>

<?php if ($data->preview) : ?>
    <h3><?= Yii::t('app', 'Ver monitoreo') ?></h3>
<?php else : ?>
    <h3><?= Yii::t('app', 'Realizar monitoreo') ?></h3>
<?php endif; ?>

<?php
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='tooltip']").tooltip(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
?>
<?php
$prev_bloque_descripcion = $prev_seccion = $prev_bloque = '';
$detalles_ids = array();
$detallesseccion_id = array();
$contadorSecciones = 0;
?>

<!-- DIVS Para carga de ajax -->
<?php echo Html::tag('div', '', ['id' => 'ajax_div_problemas']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_div_llamdas']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_div_feedbacks']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_result']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_add_escalate_form']); ?>

<div class="formularios-form">

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>

    <!-- ALERT PARA CAMPOS SIN LLENAR -->
    <?php
    \yii\bootstrap\Modal::begin([
        'id' => 'modalCampos', 'header' => "Advertencia", 'size' => \yii\bootstrap\Modal::SIZE_SMALL
    ]);
    echo Yii::t("app", "Campos sin seleccionar");
    \yii\bootstrap\Modal::end();
    ?>

    <?php if ($data->preview != true) : ?>
        <?= Html::beginForm(Url::to(['formularios/guardaryenviarformulario']), "post", ["class" => "form-horizontal", "id" => "guardarFormulario"]); ?>
    <?php else : ?>
        <div class="form-horizontal">
        <?php endif; ?>

        <?php if ($data->preview == false) : ?>
            <div class="form-group">
                <div class="col-sm-12 well">
                    <?php /* = Html::submitButton(Yii::t('app', 'Guargar y enviar'), ['class' => 'btn btn-success']) */ ?>
                    <?= Html::a(Yii::t('app', 'Guardar y enviar'), "javascript:void(0)", ['class' => 'btn btn-success soloFinalizar'])
                    ?>
                    <?= Html::a(Yii::t('app', 'Guardar'), "javascript:void(0)", ['class' => 'btn btn-warning soloGuardar'])
                    ?>
                    <?php if (isset($data->formulario->subi_calculo)) : ?>
                        <?= Html::a(Yii::t('app', 'Calcular subi'), "javascript:void(0)", ['class' => 'btn  btn-primary soloCalcular'])
                        ?>
                    <?php endif; ?>
                    <?= Html::a('Desplegar', "javascript:void(0)", ['id' => 'prueba', 'class' => 'btn btn-info soloAbrir'])
                    ?>
                    <?php
                        $escalado = Yii::$app->request->get("escalado");
                        if (isset($escalado)) : ?>
                        <?php if ($escalado == 0) : ?>
                            <?= Html::a(Yii::t('app', 'Borrar'), "javascript:void(0)", ['class' => 'btn btn-danger soloCancelar'])
                            ?>
                        <?php endif; ?>
                    <?php endif; ?>

                </div>
            </div>
        <?php endif; ?>
        <?php if ($data->preview == true) : ?>
            <div class="form-group">
                <div class="col-sm-12 well">
                    <?= Html::a('Desplegar', "javascript:void(0)", ['id' => 'prueba', 'class' => 'btn btn-info soloAbrir'])
                    ?>

                </div>
            </div>
        <?php endif; ?>

        <?php
        $cont = 0;
        do {
            $detalle = $data->detalles[$cont];
        ?>

            <?php if ($contadorSecciones == $banderaDatogenerales) : ?>
                <?php
                if ($contadorSecciones == 0) {
                    echo $varRow;
                }
                ?>
                <?php
                $contadorSecciones++;
                ?>
                <?= Html::input("hidden", "tmp_formulario_id", $data->formulario_id, ["id" => "tmp_formulario_id"]); ?>
                <?= Html::input("hidden", "arbol_id", $data->tmp_formulario->arbol_id); ?>
                <?= Html::input("hidden", "dimension_id", $data->tmp_formulario->dimension_id); ?>
                <?= Html::input("hidden", "ruta_arbol", $data->ruta_arbol); ?>
                <?= Html::input("hidden", "form_equipo_id", $data->equipo_id); ?>
                <?= Html::input("hidden", "form_lider_id", $data->usua_id_lider); ?>

                <?= (isset($view)) ? Html::input("hidden", "view", $view) : ""; ?>
                <!-- CAMPO OCULTO PARA EVITAR SUBMIT NO CONTROLADO -->
                <?= Html::input("hidden", "submitcorrecto", "NO", ["id" => "submitcorrecto"]); ?>
                <?php echo $varGuiainspiracion ?>



                <div class="row seccion-data" class="col-md-12">
                    <div class="col-md-10">

                        <label class="labelseccion ">
                            DATOS GENERALES
                        </label>
                    </div>
                    <div class="col-md-2">
                        <?=
                        Html::a(
                            Html::tag("span", "", [
                                "aria-hidden" => "true",
                                "class" => "glyphicon glyphicon-chevron-downForm",
                            ]) . "",
                            "javascript:void(0)",
                            ["class" => "openSeccion", "id" => "labelGenerales"]
                        )
                        ?>
                    </div>
                    <?php $this->registerJs('$("#labelGenerales").click(function () {
                                $("#datosGenerales").toggle("slow");
                            });'); ?>
                </div>
                <div id="datosGenerales" class="col-sm-12" style="display: none">

                    <table class="table table-striped table-bordered detail-view formDinamico">
                    <caption>Tabla datos generales</caption>
                        <tbody>
                            <tr>
                                <th id="evaluadoId"><?php echo Yii::t("app", "Evaluado ID"); ?></th>
                                <td><?php echo $data->evaluado ?></td>
                            </tr>
                            <tr>
                                <th id="instrumentoV"><?php echo Yii::t("app", "Instrumento para la Valoracion"); ?></th>
                                <td><?php echo $data->ruta_arbol ?></td>
                            </tr>
                            <?php if (isset($data->fecha_inicial)) { ?>
                                <tr>
                                    <th id="fechaInicio"><?php echo Yii::t("app", "Fecha Inicio"); ?></th>
                                    <td><?php echo $data->fecha_inicial ?></td>
                                </tr>
                            <?php } ?>
                            <?php if (isset($data->fecha_final)) { ?>
                                <tr>
                                    <th id="fechaFin"><?php echo Yii::t("app", "Fecha Fin"); ?></th>
                                    <td><?php echo $data->fecha_final ?></td>
                                </tr>
                            <?php } ?>
                            <?php if (isset($data->minutes)) { ?>
                                <tr>
                                    <th id="diferencia"><?php echo Yii::t("app", "Diferencia"); ?></th>
                                    <td><?php echo $data->minutes ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <th id="dimension"><?php echo Yii::t("app", "Dimension"); ?></th>
                                <td>
                                    <?php if ($data->preview) : ?>
                                        <?=
                                        Html::dropDownList(
                                            "dimension_id",
                                            $data->tmp_formulario->dimension_id,
                                            $data->dimension,
                                            ["class" => "form-control droplabel", "disabled" => "disabled"]
                                        );
                                        ?>
                                    <?php else : ?>
                                        <?=
                                        Html::dropDownList(
                                            "dimension_id",
                                            $data->tmp_formulario->dimension_id,
                                            $data->dimension,
                                            ["class" => "form-control droplabel"]
                                        );
                                        ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th id="fuente"><?php echo Yii::t("app", "Fuente"); ?></th>
                                <td>
                                    <?php if ($data->preview) : ?>
                                        <?=
                                        Html::input(
                                            "text",
                                            "fuente",
                                            $data->tmp_formulario->dsfuente_encuesta,
                                            [
                                                "id" => "fuente",
                                                "class" => "form-control droplabel",
                                                "placeholder" => Yii::t("app", "Ingrese la fuente"),
                                                "readonly" => "readonly"
                                            ]
                                        );
                                        ?>
                                    <?php else : ?>
                                        <?=
                                        Html::input(
                                            "text",
                                            "fuente",
                                            $data->tmp_formulario->dsfuente_encuesta,
                                            [
                                                "id" => "fuente",
                                                "class" => "form-control droplabel",
                                                "placeholder" => Yii::t("app", "Ingrese la fuente")
                                            ]
                                        );
                                        ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th id="interaccion"><?php echo Yii::t("app", "Interaccion"); ?></th>
                                <td>
                                    <?php if ($data->preview) : ?>
                                        <?=
                                        Html::dropDownList(
                                            "transacion_id",
                                            $data->tmp_formulario->transacion_id,
                                            $data->transacciones,
                                            ["class" => "form-control droplabel", "disabled" => "disabled"]
                                        );
                                        ?>
                                    <?php else : ?>
                                        <?=
                                        Html::dropDownList(
                                            "transacion_id",
                                            $data->tmp_formulario->transacion_id,
                                            $data->transacciones,
                                            ["class" => "form-control droplabel"]
                                        );
                                        ?>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <?php
                            $showInteraccion = Yii::$app->request->get("showInteraccion");
                            if (isset($showInteraccion) && base64_decode($showInteraccion) == 1) :
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
                            <?php
                            if (isset($data->formulario->subi_calculo)) :
                            ?>
                                <tr>
                                    <th id="subiCalculo"><?php echo Yii::t("app", "subi_calculo"); ?></th>
                                    <td>
                                        <?php echo implode(',', $data->indices_calcular) ?>
                                    </td>
                                </tr>
                                <?php
                                if (count($data->indices_calcular) < 5 && !$data->preview) :
                                ?>
                                    <tr>
                                        <th id="agregarSubi"><?php echo Yii::t("app", "agregar subi"); ?></th>
                                        <td>
                                            <?php
                                            $max = 5 - count($data->indices_calcular);
                                            echo Select2::widget([
                                                'language' => 'es',
                                                'name' => 'subi_calculo',
                                                'options' => [
                                                    'placeholder' => Yii::t('app', 'Select ...'),
                                                    'id' => 'subi_calculo'
                                                ],
                                                'pluginOptions' => [
                                                    'multiple' => true,
                                                    'allowClear' => true,
                                                    'minimumInputLength' => 3,
                                                    'maximumSelectionSize' => $max,
                                                    'ajax' => [
                                                        'url' => \yii\helpers\Url::to(['metricalistmultipleform']),
                                                        'dataType' => 'json',
                                                        'data' => new JsExpression('function(term,page) { return {search:term,ids_selec:"' . $data->tmp_formulario->attributes['subi_calculo'] . '"}; }'),
                                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                                    ],
                                                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                var ids_select = "' . $data->tmp_formulario->attributes['subi_calculo'] . '";
                                if (id !== "") {
                                    $.ajax("' . Url::to(['metricalistmultipleform']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post",
                                        data:{
                                            ids_selec: ids_select
                                        }
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                                                ]
                                            ]);
                                            ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if ($data->tmp_formulario->sn_mostrarcalculo != 0) : ?>
                                    <?php foreach ($data->indices_calcular as $key => $value) : ?>
                                        <?php if ($value == 13) : ?>
                                            <tr>
                                                <th id="valor1"><?php echo $value ?></th>
                                                <td><?php echo ($data->tmp_formulario->attributes['score'] * 100) . '%' ?></td>
                                            </tr>
                                        <?php else : ?>
                                            <tr>
                                                <th id="valor2"><?php echo $value ?></th>
                                                <?php if ($data->tmp_formulario->attributes['i' . $key . '_nmcalculo'] != '') : ?>
                                                    <td><?php echo ($data->tmp_formulario->attributes['i' . $key . '_nmcalculo'] * 100) . '%' ?></td>
                                                <?php else : ?>
                                                    <td><?php echo 'No calculado' ?></td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endif; ?>

                                    <?php endforeach; ?>
                                <?php endif; ?>

                            <?php endif; ?>


                        </tbody>
                    </table>
                    <?php
                    $showBtnIteraccion = Yii::$app->request->get("showBtnIteraccion");
                    if (isset($showBtnIteraccion) && base64_decode($showBtnIteraccion) == 1) {
                        echo Html::a(Html::img(Url::to("@web/images/actualizar.png"), ["width" => "20px"]) . ' '
                            . Yii::t("app", "Solicitar interaccion"), 'javascript:void(0)', [
                            'class' => 'btn btn-default',
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

                <?php echo $varFin ?>
                <?php
                if (($contadorSecciones % $cantDivs) == 0) {
                    echo $varFin;
                }
                ?>
            <?php
            else :
                $cont++;
            ?>



                <?php $detalles_ids[] = $detalle->id ?>
                <?php $detallesseccion_id[] = [$detalle->id, $detalle->seccion_id, $detalle->isPits] ?>
                <?php if ($prev_seccion != $detalle->seccion_id) : ?>
                    <?php if (!empty($prev_seccion)) : ?>

                        <div class="form-group row" <?php
                                                    if ($prev_sndesplegar_comentario == 0) {
                                                        echo 'style="display: none"';
                                                    }
                                                    ?>>

                            <div class="col-sm-10" id="txt_comentarios<?php echo $prev_seccion ?>">
                                <?php if ($data->preview) : ?>
                                    <?=
                                    Html::textarea(
                                        "comentarioSeccion[" . $prev_seccion . "]",
                                        $prev_secccion_comentario,
                                        [
                                            "class" => "form-control droplabel",
                                            "placeholder" => "Comentario para el Coaching",
                                            "readonly" => "readonly"
                                        ]
                                    );
                                    ?>
                                <?php else : ?>
                                    <?=
                                    Html::textarea(
                                        "comentarioSeccion[" . $prev_seccion . "]",
                                        $prev_secccion_comentario,
                                        [
                                            "class" => "form-control droplabel",
                                            "placeholder" => "Comentario para el Coaching"
                                        ]
                                    );
                                    ?>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-2">
                                <?php if ($data->fill_values) : ?>
                                    <?php echo Html::checkbox('checkComentario[' . $prev_seccion . ']', true, ['id' => 'checkComentario' . $prev_seccion, 'disabled' => 'disabled']) ?>
                                <?php else : ?>
                                    <?php echo Html::checkbox('checkComentario[' . $prev_seccion . ']', true, ['id' => 'checkComentario' . $prev_seccion]) ?>
                                <?php endif; ?>
                                <label class="labelbloque" id="labelComentario<?php echo $prev_seccion ?>" style="display: none;">
                                    <?php echo "Desplegar Comentario" ?>
                                </label>
                                <?php $this->registerJs('$("#checkComentario' . $prev_seccion . '").click(function () {
                                if($(this).is(":checked")==true){
                                    $("#txt_comentarios' . $prev_seccion . '").show("slow");
                                    $("#labelComentario' . $prev_seccion . '").hide();                                    
                                }else{                                      
                                    $("#txt_comentarios' . $prev_seccion . '").hide("slow");
                                    $("#labelComentario' . $prev_seccion . '").show("slow");
                                }
                            });'); ?>
                            </div>
                        </div>
        </div>
        <?php echo $varFin ?>
        <?php
                        if (($contadorSecciones % $cantDivs) == 0) {
                            echo $varFin;
                        }
        ?>

    <?php endif; ?>
    <?php
                    if (($contadorSecciones % $cantDivs) == 0) {
                        echo $varRow;
                    }
    ?>
    <?php
                    $contadorSecciones++;
                    echo $arrayDivs[0]
    ?>
    <div <?php echo "id='seccion" . $detalle->seccion_id . "'" ?> class="row seccion" <?php
                                                                                        if ($detalle->isPits == 1) {
                                                                                            echo "style='display: none'";
                                                                                        }
                                                                                        ?>>
        <div class="col-md-10">
            <label class="labelseccion">
                <?php
                    echo Html::tag('span', $detalle->seccion, [
                        'data-title' => $detalle->sdescripcion,
                        'data-toggle' => 'tooltip',
                        'style' => 'cursor:pointer;'
                    ]);
                ?>
            </label>
        </div>
        <div class="col-md-2">
            <?=
                    Html::a(
                        Html::tag("span", "", [
                            "aria-hidden" => "true",
                            "class" => "glyphicon glyphicon-chevron-downForm",
                        ]) . "",
                        "javascript:void(0)",
                        ["class" => "openSeccion", "id" => "desplegarSeccion" . $detalle->seccion_id]
                    )
            ?>
            <?php $this->registerJs('$("#desplegarSeccion' . $detalle->seccion_id . '").click(function () {
                        $("#datosSeccion' . $detalle->seccion_id . '").toggle("slow");
                    });'); ?>
        </div>

    </div>
    <div <?php echo "id='datosSeccion" . $detalle->seccion_id . "'" ?> style="display: none">
    <?php endif; ?>
    <?php if ($prev_bloque != $detalle->bloque) : ?>
        <div class="row well">
            <label class="labelbloque">
                <?php
                    echo Html::tag('span', $detalle->bloque, [
                        'data-title' => $detalle->bloque_descripcion,
                        'data-toggle' => 'tooltip',
                        'style' => 'cursor:pointer;'
                    ]);
                ?>
            </label>
            <?php
                    echo Html::tag('span', Html::img(Url::to("@web/images/Question.png")), [
                        'data-title' => Yii::t("app", "Bloques Detalles"),
                        'data-content' => $detalle->bloque_descripcion,
                        'data-toggle' => 'popover',
                        'style' => 'cursor:pointer;'
                    ]);
            ?>

        </div>
        <?php if ($detalle->isPits == 1) : ?>
            <div class="form-group col-sm-12">
                <table <?php echo "id='tablapits" . $detalle->seccion_id . "'" ?> class="table table-striped table-bordered detail-view">
                <caption>Tabla pits</caption>
                    <tbody>
                    <th scope="col">
                    </th>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    <?php endif; ?>
    <div class="form-group">
        <div class="control-group">
            <label class="control-label-form <?php echo ($detalle->c_pitsBD == 1) ? 'col-sm-6' : 'col-sm-8'; ?>">
                <?php
                echo Html::tag('span', $detalle->pregunta, [
                    'data-title' => $detalle->bddecripcion,
                    'data-toggle' => 'tooltip',
                    'style' => 'cursor:pointer;'
                ]);
                ?>
            </label>
            <div class="col-sm-4">
                <?php if ($data->fill_values == true) : ?>
                    <?php echo isset($data->calificaciones[$detalle->calificacion_id][$detalle->calificaciondetalle_id]) ? $data->calificaciones[$detalle->calificacion_id][$detalle->calificaciondetalle_id]["name"] : '' ?>
                <?php else : ?>
                    <select name="calificaciones[<?php echo $detalle->id ?>]" class="form-control toggleTipificacion droplabel" data-id-detalle="<?php echo $detalle->id ?>" id="calificacion_<?php echo $detalle->id ?>">
                        <option value=""></option>
                        <?php if (isset($data->calificaciones[$detalle->calificacion_id])) : ?>
                            <?php foreach ($data->calificaciones[$detalle->calificacion_id] as $id => $c) : ?>
                                <?php $selected = ($detalle->calificaciondetalle_id == $id) ? 'selected="selected"' : '' ?>
                                <option value="<?php echo $id ?>" <?php echo $selected ?>><?php echo $c["name"] ?></option>
                                <?php if ($c['c_pits'] == 1 && $detalle->id_seccion_pits != '') : ?>
                                    <?php $this->registerJs('$("#calificacion_' . $detalle->id . '").on("change",function () {
                                            if($(this).val()==' . $id . '){
                                                $("#datosSeccion' . $detalle->id_seccion_pits . '").removeAttr("disabled","");
                                                $("#seccion' . $detalle->id_seccion_pits . '").show("slow");
                                                $("#trpreguntapits' . $detalle->id . '").remove();
                                                $("#tablapits' . $detalle->id_seccion_pits . '").append("<tr id = trpreguntapits' . $detalle->id . '><td>' . $detalle->seccion . ' - ' . $detalle->bloque . ' - ' . $detalle->pregunta . ' - "+$("#calificacion_' . $detalle->id . ' option:selected").html()+"</td></tr>"); 
                                                $("#check' . $detalle->id . '").prop("checked", true); 
                                            }else{                                   
                                                $("#trpreguntapits' . $detalle->id . '").remove();
                                                var rowCount = $("#tablapits' . $detalle->id_seccion_pits . ' tr").length;
                                                $("#check' . $detalle->id . '").prop("checked", false); 
                                                if(rowCount == 0){
                                                    $("#datosSeccion' . $detalle->id_seccion_pits . '").attr("disabled","disabled");
                                                    $("#datosSeccion' . $detalle->id_seccion_pits . '").hide("slow");
                                                    $("#seccion' . $detalle->id_seccion_pits . '").hide("slow");
                                                }                                   
                                            }
                                        });'); ?>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                <?php endif; ?>
            </div>
            <?php if ($detalle->c_pitsBD == 1) : ?>
                <div class="col-sm-2" style="padding: 0;">
                    <?php if (($data->fill_values == true)) : ?>
                        <?php echo Html::checkbox('checkPits[' . $detalle->id . ']', ($detalle->c_pits == 1) ? true : false, ['id' => 'check' . $detalle->id, 'disabled' => 'disabled']) ?>
                    <?php else : ?>
                        <?php echo Html::checkbox('checkPits[' . $detalle->id . ']', ($detalle->c_pits == 1) ? true : false, ['id' => 'check' . $detalle->id]) ?>
                    <?php endif; ?>
                    <?php $this->registerJs('$("#check' . $detalle->id . '").click(function () {
                                if($(this).is(":checked")==true){
                                    $("#datosSeccion' . $detalle->id_seccion_pits . '").removeAttr("disabled","");
                                    $("#seccion' . $detalle->id_seccion_pits . '").show("slow");
                                    $("#trpreguntapits' . $detalle->id . '").remove();
                                    $("#tablapits' . $detalle->id_seccion_pits . '").append("<tr id = trpreguntapits' . $detalle->id . '><td>' . $detalle->seccion . ' - ' . $detalle->bloque . ' - ' . $detalle->pregunta . ' - "+$("#calificacion_' . $detalle->id . ' option:selected").html()+"</td></tr>");  
                                }else{                                   
                                    $("#trpreguntapits' . $detalle->id . '").remove();
                                    var rowCount = $("#tablapits' . $detalle->id_seccion_pits . ' tr").length;
                                    if (confirm("' . Yii::t('app', 'confirm message checkpits') . '")) {
                                       if(rowCount == 0){
                                            $("#datosSeccion' . $detalle->id_seccion_pits . '").attr("disabled","disabled");
                                            $("#datosSeccion' . $detalle->id_seccion_pits . '").hide("slow");
                                            $("#seccion' . $detalle->id_seccion_pits . '").hide("slow");
                                        }
                                    }
                                                                       
                                }
                            });');
                    ?>
                    <?php if ($detalle->c_pits == 1) : ?>
                        <?php $this->registerJs('
                                if($("#check' . $detalle->id . '").is(":checked")==true){
                                    $("#datosSeccion' . $detalle->id_seccion_pits . '").removeAttr("disabled","");
                                    $("#seccion' . $detalle->id_seccion_pits . '").show();
                                    $("#tablapits' . $detalle->id_seccion_pits . '").append("<tr id = trpreguntapits' . $detalle->id . '><td>' . $detalle->seccion . ' - ' . $detalle->bloque . ' - ' . $detalle->pregunta . ' - "+$("#calificacion_' . $detalle->id . ' option:selected").html()+"</td></tr>");  
                                }
                            '); ?>
                    <?php endif; ?>
                    <label class="control-label-form"><?php echo Yii::t('app', 'pits'); ?></label>
                </div>
            <?php endif; ?>
        </div>
    </div>
    <div class="form-group">
        <?php if ($data->fill_values == true) : ?>
            <?php if (isset($detalle->tipif_seleccionados) && !empty($detalle->tipif_seleccionados)) : ?>
                <fieldset id="tipificacion_<?php echo $detalle->id ?>" style="display: <?php echo (empty($detalle->tipif_seleccionados)) ? 'none' : '' ?>">
                    <legend></legend>
                    <?php foreach ($detalle->tipif_seleccionados as $det_tipif) : ?>
                        <input type="checkbox" checked="checked" disabled="disabled" data-id-detalle="<?php echo $detalle->id ?>" data-det-tipif="<?php echo $det_tipif->id ?>" data-preview="1" class="showSubtipificaciones tipificacion_<?php echo $detalle->id ?> tipif" />
                        &nbsp;
                        <?php $nmTip = app\models\Tipificaciondetalles::findOne($det_tipif->id); ?>
                        <?php echo $nmTip->name; ?>
                        <div style="" id="div_subtipificaciones_<?php echo $detalle->id . $det_tipif->id ?>">
                        </div>
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>
        <?php else : ?>
            <?php if (isset($data->tipificaciones[$detalle->tipificacion_id]) && !empty($data->tipificaciones[$detalle->tipificacion_id])) : ?>
                <fieldset id="tipificacion_<?php echo $detalle->id ?>" <?php echo !empty($detalle->tipif_seleccionados) ? '' : 'style="display: none; "' ?>>
                    <legend></legend>
                    <?php foreach ($data->tipificaciones[$detalle->tipificacion_id] as $id => $name) : ?>
                        <?php
                            $checked = "";
                            foreach ($detalle->tipif_seleccionados as $dis) {
                                if ($dis->id == $id) {
                                    $checked = 'checked="checked"';
                                    break;
                                }
                            }
                        ?>
                        <input type="checkbox" <?php echo $checked ?> class="showSubtipificaciones tipificacion_<?php echo $detalle->id ?> tipif" name="tipificaciones[<?php echo $detalle->id ?>][]" data-id-detalle="<?php echo $detalle->id ?>" data-det-tipif="<?php echo $id ?>" data-preview="0" value="<?php echo $id ?>" />&nbsp;<?php echo $name ?>
                        <div style="" id="div_subtipificaciones_<?php echo $detalle->id . $id ?>">
                        </div>
                        <br />
                    <?php endforeach; ?>
                </fieldset>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <?php $prev_seccion = $detalle->seccion_id ?>
    <?php $prev_sndesplegar_comentario = $detalle->sndesplegar_comentario ?>
    <?php $prev_bloque = $detalle->bloque ?>
    <?php $prev_secccion_comentario = trim($detalle->dscomentario); ?>
<?php endif; ?>
<?php } while ($cont < count($data->detalles)); ?>


<?php if (!empty($prev_seccion)) : ?>
    <div class="form-group" <?php
                            if ($prev_sndesplegar_comentario == 0) {
                                echo 'style="display: none"';
                            }
                            ?>>
        <div class="col-sm-10">
            <?php if ($data->fill_values == true) : ?>
                <span style="color: #ff0000;"><?php echo $prev_secccion_comentario ?></span>
            <?php else : ?>
                <?php if ($data->preview) : ?>
                    <?=
                    Html::textarea(
                        "comentarioSeccion[" . $prev_seccion . "]",
                        $prev_secccion_comentario,
                        [
                            "id" => "txt_comentarios",
                            "class" => "form-control droplabel",
                            "placeholder" => "Comentario para el Coaching",
                            "readonly" => "readonly"
                        ]
                    );
                    ?>
                <?php else : ?>
                    <?=
                    Html::textarea(
                        "comentarioSeccion[" . $prev_seccion . "]",
                        $prev_secccion_comentario,
                        [
                            "id" => "txt_comentarios",
                            "class" => "form-control droplabel",
                            "placeholder" => "Comentario para el Coaching"
                        ]
                    );
                    ?>
                <?php endif; ?>

            <?php endif; ?>
        </div>
        <div class="col-md-2">
            <?php if ($data->fill_values) : ?>
                <?php echo Html::checkbox('checkComentario[' . $prev_seccion . ']', true, ['id' => 'checkComentario' . $prev_seccion, 'disabled' => 'disabled']) ?>
            <?php else : ?>
                <?php echo Html::checkbox('checkComentario[' . $prev_seccion . ']', true, ['id' => 'checkComentario' . $prev_seccion]) ?>
            <?php endif; ?>
            <label class="labelbloque" id="labelComentario<?php echo $prev_seccion ?>" style="display: none;">
                <?php echo "Desplegar Comentario" ?>
            </label>
            <?php $this->registerJs('$("#checkComentario' . $prev_seccion . '").click(function () {
                                if($(this).is(":checked")==true){
                                    $("#txt_comentarios' . $prev_seccion . '").show("slow");
                                    $("#labelComentario' . $prev_seccion . '").hide();                                    
                                }else{                                      
                                    $("#txt_comentarios' . $prev_seccion . '").hide("slow");
                                    $("#labelComentario' . $prev_seccion . '").show("slow");
                                }
                            });'); ?>
        </div>
    </div>
<?php endif; ?>
<?php
echo $varFin;
if (($contadorSecciones % $cantDivs) == 0) {
    echo $varFin;
}
?>
    </div>
    <?php
    if (($contadorSecciones % $cantDivs) == 0) {
        echo $varRow;
    }
    ?>
    <?php
    $contadorSecciones++;
    ?>

    <?php echo $arrayDivs[0] ?>
    <div class="row seccion">
        <div class="col-md-10">
            <label class="labelseccion">
                <?php echo Yii::t("app", "Informacion adicional"); ?>
            </label>
        </div>

        <div class="col-md-2">
            <?=
            Html::a(
                Html::tag("span", "", [
                    "aria-hidden" => "true",
                    "class" => "glyphicon glyphicon-chevron-downForm",
                ]) . "",
                "javascript:void(0)",
                ["class" => "openSeccion", "id" => "infoAdicionalSeccion"]
            )
            ?>
            <?php $this->registerJs('$("#infoAdicionalSeccion").click(function () {
                        $("#datosinfoAdicional").toggle("slow");
                    });'); ?>
        </div>
    </div>
    <div id="datosinfoAdicional" style="display: none;">

        <div class="row well" <?php
                                if ($data->info_adicional['problemas'] == 0) {
                                    echo "style='display: none'";
                                }
                                ?>>
            <?php if ($data->fill_values) : ?>
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
                        <?php foreach ($data->tablaproblemas as $listtablaproblemas) : ?>
                            <tr>
                                <td><?php echo $listtablaproblemas->dsenfoque; ?></td>
                                <td><?php echo $listtablaproblemas->dsproblema; ?></td>
                                <td><?php echo $listtablaproblemas->detalle; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="formulario-bloque" colspan="3"><?php echo Yii::t("app", "Tablero de Experiencias"); ?>
                    <?php if ($data->preview == false) : ?>
                        <?php
                        echo Html::a(Yii::t("app", "View"), 'javascript:void(0)', [
                            'title' => Yii::t('app', 'Create'),
                            'class' => 'btn-sm btn-success',
                            'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to([
                                'tmptableroexperiencias/index', 'tmp_formulario_id' => $data->formulario_id, 'arbol_id' => $data->tmp_formulario->arbol_id
                            ]) . "',
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
            <?php if ($data->fill_values) : ?>
                <?php echo Yii::t("app", "Tiposllamadas"); ?><br /><br />
                <table class="table table-striped table-bordered">
                <caption>Tabla tiposllamadas</caption>
                    <thead>
                        <tr>
                            <th id="tiposLlamadas">Tipo de Llamada</th>
                            <th id="llamada">Llamada</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->tablallamadas as $listtablallamadas) : ?>
                            <tr>
                                <td><?php echo $listtablallamadas["name_tipo_llamada"]; ?></td>
                                <td><?php echo $listtablallamadas["name_det_llamada"]; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="formulario-bloque" colspan="3"><?php echo Yii::t("app", "Tiposllamadas"); ?>
                    <?php if ($data->preview == false) : ?>
                        <?php
                        echo Html::a(Yii::t("app", "View"), 'javascript:void(0)', [
                            'title' => Yii::t('app', 'Create'),
                            'class' => 'btn-sm btn-success',
                            'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to([
                                'tmptiposllamada/index', 'tmp_formulario_id' => $data->formulario_id
                            ]) . "',
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
            <?php if ($data->fill_values) : ?>
                <?php echo Yii::t("app", "Agregar feedback"); ?> <br /><br />
                <table class="table table-striped table-bordered">
                <caption>Tabla feedback</caption>
                    <thead>
                        <tr>
                            <th id="comentariofeedback">Comentario Feedback</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data->list_Add_feedbacks as $list) : ?>
                            <tr>
                                <td><?php echo $list->dscomentario; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else : ?>
                <div class="formulario-bloque" colspan="3"><?php echo Yii::t("app", "Agregar feedback"); ?>
                    <?php if ($data->preview == false) : ?>
                        <?php
                        echo Html::a(Yii::t("app", "View"), 'javascript:void(0)', [
                            'title' => Yii::t('app', 'Create'),
                            'class' => 'btn-sm btn-success',
                            'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to([
                                'tmpejecucionfeedbacks/index', 'tmp_formulario_id' => $data->formulario_id, 'usua_id_lider' => $data->usua_id_lider, 'evaluado_id' => $data->tmp_formulario->evaluado_id, 'basesatisfacion_id' => null
                            ]) . "',
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
    </div>
    <?php echo $varFin ?>
    <?php
    if (($contadorSecciones % $cantDivs) == 0) {
        echo $varFin;
    }
    ?>
    <?php
    if (($contadorSecciones % $cantDivs) == 0) {
        echo $varRow;
    }
    ?>
    <?php
    $contadorSecciones++;
    ?>
    <?php echo $arrayDivs[0] ?>

    <div class="row seccion">
        <div class="col-md-10">

            <label class="labelseccion">
                <?php echo Yii::t("app", "General"); ?>
            </label>
        </div>

        <div class="col-md-2">
            <?=
            Html::a(
                Html::tag("span", "", [
                    "aria-hidden" => "true",
                    "class" => "glyphicon glyphicon-chevron-downForm",
                ]) . "",
                "javascript:void(0)",
                ["class" => "openSeccion", "id" => "generalseccion"]
            )
            ?>
            <?php $this->registerJs('$("#generalseccion").click(function () {
                        $("#datosGeneral").toggle("slow");
                    });'); ?>
        </div>
    </div>
    <div id="datosGeneral" style="display: none;">
        <div class="row well">
            <?php echo Yii::t("app", "General"); ?>
        </div>
        <div class="form-group row">
            <div class="col-sm-12">
                <?php if ($data->preview) : ?>
                    <?=
                    Html::textarea(
                        "comentarios_gral",
                        $data->tmp_formulario->dscomentario,
                        [
                            "id" => "txt_comentarios_gral",
                            "class" => "form-control droplabel",
                            "placeholder" => "Comentario para el Coaching",
                            "readonly" => "readonly"
                        ]
                    );
                    ?>
                <?php else : ?>
                    <?=
                    Html::textarea(
                        "comentarios_gral",
                        $data->tmp_formulario->dscomentario,
                        [
                            "id" => "txt_comentarios_gral",
                            "class" => "form-control droplabel",
                            "placeholder" => "Comentario para el Coaching"
                        ]
                    );
                    ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <?php echo $varFin ?>
    <?php echo $varFin ?>

    <?php if ($data->preview == false) : ?>
        <div class="form-group">
            <div class="col-sm-12 well">
                <?= Html::a(Yii::t('app', 'Guardar y enviar'), "javascript:void(0)", ['class' => 'btn btn-success soloFinalizar'])
                ?>
                <?= Html::a(Yii::t('app', 'Guardar'), "javascript:void(0)", ['class' => 'btn btn-warning soloGuardar'])
                ?>
                <?= Html::a(Yii::t('app', 'Calcular subi'), "javascript:void(0)", ['class' => 'btn  btn-primary soloCalcular'])
                ?>
                <?php 
                    $escalado = Yii::$app->request->get("escalado");
                    if (isset($escalado)) : ?>
                    <?php if ($escalado == 1) : ?>
                        <?= Html::a(Yii::t('app', 'Borrar'), "javascript:void(0)", ['class' => 'btn btn-danger soloCancelar'])
                        ?>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endif; ?>

    <?php if ($data->preview != true) : ?>
        <?php echo Html::endForm(); ?>
    <?php else : ?>
</div>
<?php endif; ?>
<!-- INICIO DE CAMBIO PARA ESCALAR O ADICIONAR VALORACION-->
<?php if ($data->preview == false) : ?>
    <div class="row">
        <div class="control-group">
            <div class="col-md-1" style="width: 30px;">
                <?php echo Html::checkbox('activarValoracion', false, ['id' => 'activarValoracion']) ?>

            </div>
            <div class="col-md-3">
                <label class="labelseccion">
                    <?php echo Yii::t("app", "add/escalate form"); ?>
                </label>
            </div>
        </div>
        <div id="checkformadd" class="control-group" style="display: none;">
            <div class="col-md-12">
                <div class="col-md-1" style="width: 30px;">
                    <?php echo Html::checkbox('addForm', false, ['id' => 'addForm']) ?>

                </div>
                <div class="col-md-3">
                    <label class="labelseccion">
                        <?php echo Yii::t("app", "add form"); ?>
                    </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="col-md-1" style="width: 30px;">
                    <?php echo Html::checkbox('escalateForm', false, ['id' => 'escalateForm']) ?>

                </div>
                <div class="col-md-3">
                    <label class="labelseccion">
                        <?php echo Yii::t("app", "escalate form"); ?>
                    </label>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>

<!-- -->
</div>

<script type="text/javascript">
    $(document).ready(function() {
        var valCalificacionesDespliegaTipificaciones = new Array();
        <?php foreach ($data->calificaciones as $cal_id => $detalle) : ?>
            <?php foreach ($detalle as $det_id => $objDetalle) : ?>
                valCalificacionesDespliegaTipificaciones[<?php echo $det_id ?>] = <?php echo $objDetalle["sndespliega_tipificaciones"] ?>;
            <?php endforeach; ?>
        <?php endforeach; ?>
        var idsDetalles = '<?php echo json_encode($detalles_ids); ?>';

        /* MOSTRAR TIPIFICACIONES AL CAMBIAR **********************************/
        $(".toggleTipificacion").change(function() {
            var id_detalle = $(this).data("id-detalle");
            var id_calificacion = $(this).val();
            if (valCalificacionesDespliegaTipificaciones[id_calificacion] == 1) {
                $("#tipificacion_" + id_detalle).show();
            } else {
                $("#tipificacion_" + id_detalle).hide();
                $(".tipificacion_" + id_detalle).each(function(index, check) {
                    //Quita Checkbox seleccionados.
                    check.checked = false;
                });
            }
        });

        /* MOSTRAR TIPIFICACIONES AL CARGAR ***********************************/
        $(".toggleTipificacion").each(function() {
            var id_detalle = $(this).data("id-detalle");
            var id_calificacion = $(this).val();
            if (valCalificacionesDespliegaTipificaciones[id_calificacion] == 1) {
                $("#tipificacion_" + id_detalle).show();
            } else {
                $("#tipificacion_" + id_detalle).hide();
                $(".tipificacion_" + id_detalle).each(function(index, check) {
                    //Quita Checkbox seleccionados.
                    check.checked = false;
                });
            }
        });
        /* MOSTRAR SUBTIPIFICACIONES */
        $(".showSubtipificaciones").change(function() {
            var id_detalle = $(this).data("id-detalle");
            var id_tipif = $(this).data("det-tipif");
            var preview = $(this).data("preview");
            if ($(this).is(':checked')) {
                ruta = '<?php echo Url::to(['formularios/showsubtipif']); ?>?id_detalle=' + id_detalle + '&id_tipificacion=' + id_tipif + '&preview=' + preview;
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).css("margin", "20px 0 0 50px");
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).addClass('divSubtipif');
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).addClass('well');
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).load(ruta, function(response, status, xhr) {
                    if (response == "") {
                        $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeClass("well");
                        $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).html("");
                        $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeAttr("style");
                    }
                });
            } else {
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeAttr("style");
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeClass('divSubtipif');
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeClass('well');
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).html("");
            }
        });
        /* MOSTRAR SUBTIPIFICACIONES SI SE CARGA LA PAGINA CON DATOS CREADOS */
        $(".tipif").each(function() {
            if ($(this).is(':checked')) {
                var id_detalle = $(this).data("id-detalle");
                var id_tipif = $(this).data("det-tipif");
                var preview = $(this).data("preview");
                ruta = '<?php echo Url::to(['formularios/showsubtipif']); ?>?id_detalle=' + id_detalle + '&id_tipificacion=' + id_tipif + '&preview=' + preview;
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).css("margin", "20px 0 0 50px");
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).addClass('divSubtipif');
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).addClass('well');
                $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).load(ruta, function(response, status, xhr) {
                    if (response == "") {
                        $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeClass("well");
                        $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).html("");
                        $("#div_subtipificaciones_" + id_detalle + '' + id_tipif).removeAttr("style");
                    }
                });
            }
        });

        $("#guardarFormulario").submit(function(e) {
            if ($("#submitcorrecto").val() == "NO") {
                e.preventDefault();
            }
        });

        /* BOTÃ“N GUARDAR Y ENVIAR */
        $(".soloFinalizar").click(function() {
            $("#submitcorrecto").val("SI");
            $(this).attr("disabled", "disabled");
            $(".soloGuardar").attr("disabled", "disabled");
            $(".soloCancelar").attr("disabled", "disabled");
            $(".soloCalcular").attr("disabled", "disabled");
            var guardarFormulario = $("#guardarFormulario");
            guardarFormulario.attr('action', '<?php echo Url::to(['formularios/guardaryenviarformulario']); ?>');
            var valid = validarFormulario();
            if (valid) {
                guardarFormulario.submit();
            } else {
                $("#submitcorrecto").val("NO");
                $(this).removeAttr("disabled");
                $(".soloGuardar").removeAttr("disabled");
                $(".soloCancelar").removeAttr("disabled");
                $(".soloCalcular").removeAttr("disabled");
            }
        });

        /* BOTÃ“N GUARDAR Y ENVIAR */
        $(".soloCalcular").click(function() {
            $("#submitcorrecto").val("SI");
            $(this).attr("disabled", "disabled");
            $(".soloGuardar").attr("disabled", "disabled");
            $(".soloCancelar").attr("disabled", "disabled");
            $(".soloFinalizar").attr("disabled", "disabled");
            var guardarFormulario = $("#guardarFormulario");
            guardarFormulario.attr('action', '<?php echo Url::to(['formularios/consultarcalificacionsubi']); ?>');
            var valid = validarFormulario();
            if (valid) {
                guardarFormulario.submit();
            } else {
                $("#submitcorrecto").val("NO");
                $(this).removeAttr("disabled");
                $(".soloGuardar").removeAttr("disabled");
                $(".soloCancelar").removeAttr("disabled");
                $(".soloFinalizar").removeAttr("disabled");
            }
        });

        /* BOTON DESPLEGAR SECCIONES */
        $("#prueba").on("click", function() {
            if ($("#prueba").text() == "Desplegar") {
                $("[id*=datos]").css('display', 'block');
                $("#prueba").text('Plegar');
            } else {
                $("[id*=datos]").css('display', 'none');
                $("#prueba").text('Desplegar');
            }
        });

        /* BOTÃ“N GUARDAR VALORACIÃ“N SIN ENVIAR */
        $(".soloGuardar").click(function() {
            $("#submitcorrecto").val("SI");
            $(this).attr("disabled", "disabled");
            $(".soloFinalizar").attr("disabled", "disabled");
            $(".soloCancelar").attr("disabled", "disabled");
            $(".soloCalcular").attr("disabled", "disabled");
            var guardarFormulario = $("#guardarFormulario");
            guardarFormulario.attr('action', '<?php echo Url::to(['formularios/guardarformulario']); ?>');
            guardarFormulario.submit();
        });

        /* BOTÃ“N PARA BORRAR EL FORMULARIO */
        $(".soloCancelar").click(function() {
            $("#submitcorrecto").val("SI");
            $(this).attr("disabled", "disabled");
            $(".soloFinalizar").attr("disabled", "disabled");
            $(".soloGuardar").attr("disabled", "disabled");
            $(".soloCalcular").attr("disabled", "disabled");
            var guardarFormulario = $("#guardarFormulario");
            var tmp_form = $("#tmp_formulario_id").val();
            ruta = '<?php echo Url::to(['formularios/eliminartmpform']); ?>?&tmp_form=' + tmp_form;
            guardarFormulario.attr('action', ruta);
            guardarFormulario.submit();
        });
        $("#activarValoracion").change(function() {
            if ($(this).is(":checked") == true) {
                $("#checkformadd").show('slow');
            } else {
                $("#checkformadd").hide('slow');
            }
        });
        $("#addForm").change(function() {
            var tmp_form = $("#tmp_formulario_id").val();
            if ($(this).is(":checked") == true) {
                ruta = '<?php echo Url::to(['adicionarform']); ?>?&tmp_form=' + tmp_form;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: ruta,
                    data: {},
                    success: function(response) {
                        $('#ajax_add_escalate_form').html(response);
                    }
                });
            }
        });
        $("#escalateForm").change(function() {
            var tmp_form = $("#tmp_formulario_id").val();
            var escalado = '<?php
                            $escalado = Yii::$app->request->get("escalado");
                            if (isset($escalado)) {
                                echo Yii::$app->request->get("escalado");
                            } ?>';
            if ($(this).is(":checked") == true) {
                ruta = '<?php echo Url::to(['escalarform']); ?>?&tmp_form=' + tmp_form + '?&escalado=' + escalado;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: ruta,
                    data: {},
                    success: function(response) {
                        $('#ajax_add_escalate_form').html(response);
                    }
                });
            }
        });
    });

    function validarFormulario() {
        var hayErrores = false;
        var idsDetalles = '<?php echo json_encode($detalles_ids); ?>';
        var visible = false;
        var idsDetalles = new Array();
        <?php foreach ($detalles_ids as $key => $val) : ?>
            idsDetalles[<?php echo $key ?>] = <?php echo $val ?>;
        <?php endforeach; ?>
        var idsDetallesSecciones = new Array();
        <?php foreach ($detallesseccion_id as $key1 => $val1) : ?>
            idsDetallesSecciones[<?php echo $val1[0] ?>] = [<?php echo $val1[1] ?>, <?php echo $val1[2] ?>];
        <?php endforeach; ?>
        var valCalificacionesDespliegaTipificaciones = new Array();
        <?php foreach ($data->calificaciones as $cal_id => $detalle) : ?>
            <?php foreach ($detalle as $det_id => $objDetalle) : ?>
                valCalificacionesDespliegaTipificaciones[<?php echo $det_id ?>] = <?php echo $objDetalle["sndespliega_tipificaciones"] ?>;
            <?php endforeach; ?>
        <?php endforeach; ?>
        var idseccionesError = new Array();
        try {
            $.each(idsDetalles, function(i, val) {
                visible = false;
                //Primero se valida que se haya seleccionado la calificacion.
                if (idsDetallesSecciones[val][1] == 1) {
                    if ($('#seccion' + idsDetallesSecciones[val][0]).css('display') != 'none') {
                        visible = true;
                    }
                } else {
                    visible = true;
                }
                if (visible) {
                    if ($("#calificacion_" + val).val() == '') {
                        $("#calificacion_" + val).addClass('field-error');
                        hayErrores = true;
                        idseccionesError.push(idsDetallesSecciones[val]);
                    } else {
                        $("#calificacion_" + val).removeClass('field-error');
                        var id_calificacion = $("#calificacion_" + val).val();
                        //Si se seleccionÃ³ calificacion y tiene la marca de desplega tipificaciones
                        // , se debe seleccionar tipificacion.
                        if (valCalificacionesDespliegaTipificaciones[id_calificacion] == 1) {
                            var AnyChecked = false;
                            var AnyExists = false;

                            $(".tipificacion_" + val).each(function(index, check) {
                                AnyExists = true;
                                if (check.checked == true) {
                                    AnyChecked = true;
                                }
                            });

                            if (!AnyChecked && AnyExists) {
                                $("#tipificacion_" + val).addClass("field-error");
                                $("#tipificacion_" + val).show();
                                hayErrores = true;
                                idseccionesError.push(idsDetallesSecciones[val]);
                            } else {
                                $("#tipificacion_" + val).removeClass("field-error");
                            }

                            var AnySubTipifChecked = false;
                            var AnySubTipifExists = false;

                            $("#tipificacion_" + val + " .divSubtipif input[type=checkbox]").each(function(k, check) {
                                AnySubTipifExists = true;
                                if (check.checked == true) {
                                    AnySubTipifChecked = true;
                                }
                            });

                            if (AnySubTipifExists && !AnySubTipifChecked) {
                                $("#tipificacion_" + val + " .divSubtipif").addClass("field-error");
                                $("#tipificacion_" + val + " .divSubtipif").show();
                                hayErrores = true;
                                idseccionesError.push(idsDetallesSecciones[val]);
                            } else {
                                $("#tipificacion_" + val + " .divSubtipif").removeClass("field-error");
                            }
                        }
                    }
                }
            });
            if ($("#fuente").val() == '') {
                $("#fuente").addClass('field-error');
                $("#datosGenerales").show("slow");
                hayErrores = true;
            } else
                $("#fuente").removeClass("field-error");

            if (hayErrores) {
                $('#modalCampos').modal('show');
                for (var b = 0; b < idseccionesError.length; b++) {
                    $("#seccion" + idseccionesError[b]).show("slow");
                    $("#datosSeccion" + idseccionesError[b]).show("slow");
                }
                return false;
            }
            return true;
        } catch (err) {
            alert("Error al validar el formulario." + err);
            return false;
        }
    }
</script>