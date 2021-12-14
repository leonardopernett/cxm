<?php
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\bootstrap\ActiveForm;

$this->title = 'Alertas';
?>

<?php

if ($data->ppregunta1 != "") {
    $pregunta1 = $data->ppregunta1;
} else {
    $pregunta1 = $preguntas->pregunta1;
}

if ($data->ppregunta2 != "") {
    $pregunta2 = $data->ppregunta2;
} else {
    $pregunta2 = $preguntas->pregunta2;
}

if ($data->ppregunta3 != "") {
    $pregunta3 = $data->ppregunta3;
} else {
    $pregunta3 = $preguntas->pregunta3;
}

if ($data->ppregunta4 != "") {
    $pregunta4 = $data->ppregunta4;
} else {
    $pregunta4 = $preguntas->pregunta4;
}

if ($data->ppregunta5 != "") {
    $pregunta5 = $data->ppregunta5;
} else {
    $pregunta5 = $preguntas->pregunta5;
}

if ($data->ppregunta6 != "") {
    $pregunta6 = $data->ppregunta6;
} else {
    $pregunta6 = $preguntas->pregunta6;
}

if ($data->ppregunta7 != "") {
    $pregunta7 = $data->ppregunta7;
} else {
    $pregunta7 = $preguntas->pregunta7;
}

if ($data->ppregunta8 != "") {
    $pregunta8 = $data->ppregunta8;
} else {
    $pregunta8 = $preguntas->pregunta8;
}

?>

<div id="Row" class="row">
    <div class="page-header">
        <h3>Notificacion de bajo desempeño</h3>
    </div>
    <div id="Datogenerales" class="col-md-6 datogenerales">
        <div class="row seccion-informacion" class="col-md-12">
            <div class="col-md-10">
                <label class="labelseccion ">
                    <?= 'Informacion asesor' ?>
                </label>
            </div>
            <div class="col-md-2">
                <?= Html::a(Html::tag("span", "", ["aria-hidden" => "true", "class" => "glyphicon glyphicon-chevron-downForm",]) . "", "javascript:void(0)", ["class" => "openSeccion", "id" => "labelPartida"]) ?>
            </div>
            <?php $this->registerJs('$("#labelPartida").click(function () {
                                $("#datosPartida").toggle("slow");
                            });'); ?>
        </div>
        <div id="datosPartida" class="col-sm-12">
            <table class="table table-striped table-bordered detail-view formDinamico">
                <caption>Tabla datos partida</caption>
                <tbody>
                    <tr>
                        <th id="nombre"><?php echo Yii::t("app", "Nombre"); ?></th>
                        <td><?php echo $datos->name ?></td>
                    </tr>
                    <tr>
                        <th id="asesor"><?php echo Yii::t("app", "Asesor"); ?></th>
                        <td><?php echo $data->asesor ?></td>
                    </tr>
                    <tr>
                        <th id="identificacion"><?php echo Yii::t("app", "Identificacion"); ?></th>
                        <td><?php echo $datos->identificacion ?></td>
                    </tr>
                    <tr>
                        <th id="desempeno"><?php echo Yii::t("app", "Desempeño"); ?></th>
                        <td><?php echo $prueba ?></td>
                    </tr>
                    <tr>
                        <th id="lider"><?php echo Yii::t("app", "Lider"); ?></th>
                        <td><?php echo $nombrelider->usua_nombre; ?></td>
                    </tr>

                    <tr>
                        <th id="notificacion"><?php echo Yii::t("app", "# Notificacion"); ?></th>
                        <td><?php echo $data->notificacion ?></td>
                    </tr>

                    <tr>
                        <th id="fechaNotificacion"><?php echo Yii::t("app", "Fecha Notificacion"); ?></th>
                        <td><?php echo $data->fecha_ingreso ?></td>
                    </tr>

                    <tr>
                        <th id="fechaCierre"><?php echo Yii::t("app", "Fecha Cierre"); ?></th>
                        <td><?php echo $data->fecha_finalizacion ?></td>
                    </tr>
                    <?php if (isset($permanencia->p_justificacion)) : ?>
                        <?php if ($data->notificacion == 3 and ($lider == "si" or $lider == "abo" or isset($jefeop))) : ?>
                            <tr>
                                <th id="justificacionP"><?php echo Yii::t("app", "Justificacion Permanencia"); ?></th>
                                <td><?php echo $permanencia->p_justificacion ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($despido->d_justificacion)) : ?>
                        <?php if ($data->notificacion == 3 and ($lider == "si" or $lider == "abo" or isset($jefeop))) : ?>
                            <tr>
                                <th id="justificacionD"><?php echo Yii::t("app", "Justificacion Despido"); ?></th>
                                <td><?php echo $despido->d_justificacion ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endif; ?>

                    <?php if (isset($jefeop)) : ?>
                        <tr>
                            <?php if ($data->solicitud_despido == "si" or $data->solicitud_permanencia == "si") : ?>
                            <?php else : ?>
                                <td>
                                    <div class="alert alert-success alertdespido" style="display: none;">
                                        Despido Solicitado Satisfactoriamente.
                                    </div>
                                    <?= Html::submitButton('Solicitar Despido', ['id' => 'despido', 'class' => 'btn btn-success', 'name' => 'contact-button']) ?>
                                </td>
                                <td>
                                    <div class="alert alert-success alertpermanencia" style="display: none;">
                                        Permanencia Solicitado Satisfactoriamente.
                                    </div>
                                    <?= Html::submitButton('Solicitar Permanencia', ['id' => 'permanencia', 'class' => 'btn btn-success', 'name' => 'contact-button']) ?>
                                </td>
                            <?php endif; ?>
                        </tr>
                    <?php endif; ?>

                </tbody>
            </table>
        </div>
    </div>

    <!-- FIN Informacion de partida-->

    <div id="Guiainspiracion" class="col-md-6 guiainspiracion">

        <div class="row seccion-data" class="col-md-12">
            <div class="col-md-10">
                <label class="labelseccion ">
                    <?= 'Gestion de Notificacion' ?>
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

        <?php if (Yii::$app->session->hasFlash('enviado')) : ?>


            <div id="datosGenerales" class="col-sm-6">
                <table class="table table-striped table-bordered detail-view formDinamico">
                    <caption>Tabla datos generales</caption>
                    <tbody>
                        <tr>
                            <th scope="col" colspan="3">
                                <div class="alert alert-success">
                                    Respuesta Guardada Satisfactoriamente.
                                </div>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Compromisos de Gestion del Asesor:</strong> <?= $data->respuesta_asesor ?> </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Feedback Lider:</strong> <?= $data->respuesta_lider ?> </p>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <p><strong>Opinion Lider:</strong> <?= $data->puntovista_lider ?> </p>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>

            <?php if ($data->notificacion == 3) : ?>
                <div id="datosGenerales" class="col-sm-12">
                    <table class="table table-striped table-bordered detail-view formDinamico">
                        <caption>Tabla datos generales</caption>
                        <tbody>
                            <tr>
                                <th scope="col">
                                    <strong><?= $preguntas->pregunta1 ?> :</strong>
                                </th>
                                <td>
                                    <?= $data->apregunta1 ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong><?= $preguntas->pregunta2 ?></strong>
                                </td>
                                <td>
                                    <?= $data->apregunta2 ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong><?= $preguntas->pregunta3 ?></strong>
                                </td>
                                <td>
                                    <?= $data->apregunta3 ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong><?= $preguntas->pregunta4 ?></strong>
                                </td>
                                <td>
                                    <?= $data->apregunta4 ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong><?= $preguntas->pregunta5 ?></strong>
                                </td>
                                <td>
                                    <?= $data->apregunta5 ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong><?= $preguntas->pregunta6 ?></strong>
                                </td>
                                <td>
                                    <?= $data->apregunta6 ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong><?= $preguntas->pregunta7 ?></strong>
                                </td>
                                <td>
                                    <?= $data->apregunta7 ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <strong><?= $preguntas->pregunta8 ?></strong>
                                </td>
                                <td>
                                    <?= $data->apregunta8 ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>

        <?php else : ?>

            <div id="datosGenerales" class="col-sm-12">
                <table class="table table-striped table-bordered detail-view formDinamico">
                    <caption>Tabla datos generales</caption>
                    <tbody>
                        <?php $form = ActiveForm::begin([
                            'fieldConfig' => [
                                'inputOptions' => ['autocomplete' => 'off']
                              ]
                        ]); ?>

                        <?php if ($data->notificacion == "3") : ?>

                            <?php if ($data->notificacion == "3") : ?>

                            <?php else : ?>

                                <tr>
                                    <th scope="col">
                                        <?php if ($lider == "si" or isset($jefeop) or $data->respuesta_asesor != "") : ?>
                                            <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6, 'maxlength' => 200, 'disabled' => true])->label('Compromisos de Gestion del Asesor:') ?>
                                        <?php else : ?>
                                            <?php if ($data->respuesta_lider != "" and $data->rac_meta != "" and $data->rac_pcrc != "" and $data->rac_cumple != "" and $data->meta != "" and $data->empleado != "" and $data->grupo != "" and $data->dif_empleado_meta != "" and $data->dif_empleado_grupo != "") : ?>
                                                <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6, 'maxlength' => 200])->label('Compromisos de Gestion del Asesor:') ?>
                                            <?php else : ?>
                                                <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6, 'maxlength' => 200, 'disabled' => true])->label('Compromisos de Gestion del Asesor:') ?>
                                            <?php endif; ?>
                                        <?php endif; ?>
                                    </th>
                                </tr>
                            <?php endif; ?>
                            <tr>
                                <td>
                                    <?php if ($data->respuesta_lider == "" and $_GET['lider'] == "si") : ?>

                                        <?= $form->field($data, 'respuesta_lider')->textArea(['rows' => 6, 'maxlength' => 200])->label('Feedback Lider:') ?>

                                    <?php elseif ($data->respuesta_lider != "" or isset($jefeop) or $_GET['lider'] == "no") : ?>

                                        <?= $form->field($data, 'respuesta_lider')->textArea(['rows' => 6, 'maxlength' => 200, 'disabled' => true])->label('Feedback Lider:') ?>
                                        <?php else :?>

                                    <?php endif; ?>
                                </td>
                            </tr>





                            <?php if ($_GET['lider'] == "si" and $data->puntovista_lider == "") : ?>
                                <tr>
                                    <td>
                                        <?= $form->field($data, 'puntovista_lider')->textArea(['rows' => 6, 'maxlength' => 200])->label('Sugerencia del lider: (este campo es visible solo para el jefe de operacion y el departamento juridico)') ?>
                                    </td>
                                </tr>
                            <?php elseif ($lider != "no" or ($data->puntovista_lider != "" and isset($jefeop))) : ?>
                                <?php else :?>

                                <tr>
                                    <td>
                                        <?= $form->field($data, 'puntovista_lider')->textArea(['rows' => 6, 'disabled' => true, 'maxlength' => 200])->label('Sugerencia del lider: (este campo es visible solo para el jefe de operacion y el departamento juridico)') ?>
                                    </td>
                                </tr>
                            <?php endif; ?>


                        <?php else : ?>


                            <!--  -->


                            <tr>
                                <td>
                                    <?php if ($lider == "abo" or $data->respuesta_asesor != "" or $lider == "si") : ?>
                                        <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6, 'disabled' => true, 'maxlength' => 200])->label('Compromisos de Gestion del Asesor:') ?>
                                    <?php elseif ($data->respuesta_lider == "") : ?>
                                        <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6, 'maxlength' => 200])->label('Compromisos de Gestion del Asesor:') ?>
                                        <?php else :?>
                                    <?php endif; ?>
                                </td>
                            </tr>


                            <tr>
                                <td>
                                    <?php if ($lider == "si" and $data->respuesta_asesor != "" and $data->respuesta_lider == "") : ?>
                                        <?= $form->field($data, 'respuesta_lider')->textArea(['rows' => 6, 'maxlength' => 200])->label('Feedback Lider:') ?>
                                    <?php else : ?>
                                        <?= $form->field($data, 'respuesta_lider')->textArea(['rows' => 6, 'disabled' => true, 'maxlength' => 200])->label('Feedback Lider:') ?>
                                    <?php endif; ?>
                                </td>
                            </tr>

                        <?php endif; ?>
                    </tbody>
                </table>
            </div>



    </div>












    <?php if ($data->notificacion == 3) : ?>

        <?php if ($_GET['lider'] == "si" or isset($jefeop)) { ?>
            <div id="datosGenerales" class="col-sm-12">
                <table class="table table-striped table-bordered detail-view formDinamico">
                    <caption>Tabla datos generales</caption>
                    <tbody>
                        <tr>
                            <th scope="col"> 
                                <?= $form->field($data, 'apregunta1')->textInput(['maxlength' => 200, 'disabled' => true])->label($pregunta1) ?>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta2')->textInput(['maxlength' => 200, 'disabled' => true])->label($pregunta2) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta3')->textInput(['maxlength' => 200, 'disabled' => true])->label($pregunta3) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta4')->textInput(['maxlength' => 200, 'disabled' => true])->label($pregunta4) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta5')->textInput(['maxlength' => 200, 'disabled' => true])->label($pregunta5) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta6')->textInput(['maxlength' => 200, 'disabled' => true])->label($pregunta6) ?>
                            </td>
                        </tr>

                        <?php if ($_GET['lider'] == "si") { ?>
                            <tr>
                                <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                                    <caption>Tabla cumplimiento</caption>
                                    <tr>
                                        <th scope="col">Cumplimiento de la Meta</th>
                                        <th scope="col">Cantidad</th>
                                    </tr>
                                    <tr>
                                        <td>Total RAC que cumplen Meta</td>
                                        <?php if ($data->rac_meta == "") : ?>
                                            <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <?php else : ?>
                                            <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <?php endif; ?>

                                    </tr>
                                    <tr>
                                        <td>Total RAC en el mismo PCRC</td>
                                        <?php if ($data->rac_pcrc == "") : ?>
                                            <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <?php else : ?>
                                            <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <?php endif; ?>
                                    </tr>
                                    <tr>
                                        <td>% RAC que cumplen Meta</td>
                                        <?php if ($data->rac_cumple == "") : ?>
                                            <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <?php else : ?>
                                            <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <?php endif; ?>

                                    </tr>
                                </table>
                            </tr>
                            <tr>
                                <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                                    <caption>Tabla Comparativo</caption>
                                    <tr>
                                        <th scope="col">Comparativo del Cumplimiento</th>
                                        <th scope="col">Meta</th>
                                        <th scope="col">Empleado</th>
                                        <th scope="col">Grupo</th>
                                        <th scope="col">Dif Empleado vs. Meta</th>
                                        <th scope="col">Dif Empleado vs. Grupo</th>
                                    </tr>
                                    <tr>
                                        <td>Cumplimiento Promedio</td>
                                        <?php if ($data->meta == "") : ?>
                                            <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <?php else : ?>
                                            <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <?php endif; ?>
                                        <?php if ($data->empleado == "") : ?>
                                            <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <?php else : ?>
                                            <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <?php endif; ?>
                                        <?php if ($data->grupo == "") : ?>
                                            <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <?php else : ?>
                                            <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <?php endif; ?>
                                        <?php if ($data->dif_empleado_meta == "") : ?>
                                            <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <?php else : ?>
                                            <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <?php endif; ?>
                                        <?php if ($data->dif_empleado_grupo == "") : ?>
                                            <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <?php else : ?>
                                            <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <?php endif; ?>
                                    </tr>
                                </table>
                            </tr>
                        <?php } else { ?>

                            <tr>
                            <tr>
                                <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                                    <caption>Tabla cumplimiento</caption>
                                    <tr>
                                        <th scope="col">Cumplimiento de la Meta</th>
                                        <th scope="col">Cantidad</th>
                                    </tr>
                                    <tr>
                                        <td>Total RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total RAC en el mismo PCRC</td>
                                        <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>% RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                            <tr>
                                <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                                    <caption>Tabla Cumplimiento</caption>
                                    <tr>
                                        <th scope="col">Comparativo del Cumplimiento</th>
                                        <th scope="col">Meta</th>
                                        <th scope="col">Empleado</th>
                                        <th scope="col">Grupo</th>
                                        <th scope="col">Dif Empleado vs. Meta</th>
                                        <th scope="col">Dif Empleado vs. Grupo</th>
                                    </tr>
                                    <tr>
                                        <td>Cumplimiento Promedio</td>
                                        <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                            </tr>

                        <?php } ?>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta7')->textInput(['maxlength' => 200, 'disabled' => true])->label($pregunta7) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta8')->textInput(['maxlength' => 200, 'disabled' => true])->label($pregunta8) ?>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>













        <?php } elseif ($data->respuesta_lider != "" and $data->rac_meta != "" and $data->rac_pcrc != "" and $data->rac_cumple != "" and $data->meta != "" and $data->empleado != "" and $data->grupo != "" and $data->dif_empleado_meta != "" and $data->dif_empleado_grupo != "" and $data->puntovista_lider) { ?>
            <div id="datosGenerales" class="col-sm-12" style="display: inline;">
                <table class="table table-striped table-bordered detail-view formDinamico">
                    <caption>Tabla datos Generales</caption>
                    <tbody>
                        <tr>
                            <th scope="col">
                                <?php if ($data->apregunta1 != "") : ?>
                                    <?= $form->field($data, 'apregunta1')->textInput(['maxlength' => 200, 'disabled' => true])->label($data->ppregunta1) ?>
                                <?php else : ?>
                                    <?= $form->field($data, 'apregunta1')->textInput(['maxlength' => 200])->label($preguntas->pregunta1) ?>
                                <?php endif; ?>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($data->apregunta2 != "") : ?>
                                    <?= $form->field($data, 'apregunta2')->textInput(['maxlength' => 200, 'disabled' => true])->label($data->ppregunta2) ?>
                                <?php else : ?>
                                    <?= $form->field($data, 'apregunta2')->textInput(['maxlength' => 200])->label($preguntas->pregunta2) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($data->apregunta3 != "") : ?>
                                    <?= $form->field($data, 'apregunta3')->textInput(['maxlength' => 200, 'disabled' => true])->label($data->ppregunta3) ?>
                                <?php else : ?>
                                    <?= $form->field($data, 'apregunta3')->textInput(['maxlength' => 200])->label($preguntas->pregunta3) ?>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($data->apregunta4 != "") : ?>
                                    <?= $form->field($data, 'apregunta4')->textInput(['maxlength' => 200, 'disabled' => true])->label($data->ppregunta4) ?>
                                <?php else : ?>
                                    <?= $form->field($data, 'apregunta4')->textInput(['maxlength' => 200])->label($preguntas->pregunta4) ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($data->apregunta5 != "") : ?>
                                    <?= $form->field($data, 'apregunta5')->textInput(['maxlength' => 200, 'disabled' => true])->label($data->ppregunta5) ?>
                                <?php else : ?>
                                    <?= $form->field($data, 'apregunta5')->textInput(['maxlength' => 200])->label($preguntas->pregunta5) ?>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($data->apregunta6 != "") : ?>
                                    <?= $form->field($data, 'apregunta6')->textInput(['maxlength' => 200, 'disabled' => true])->label($data->ppregunta6) ?>
                                <?php else : ?>
                                    <?= $form->field($data, 'apregunta6')->textInput(['maxlength' => 200])->label($preguntas->pregunta6) ?>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <?php if ($data->respuesta_asesor != "" and $data->respuesta_lider == "" and $lider == "si") : ?>
                            <tr>
                                <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                                    <caption>Tabla</caption>
                                    <tr>
                                        <th scope="col">Cumplimiento de la Meta</th>
                                        <th scope="col">Cantidad</th>
                                    </tr>
                                    <tr>
                                        <td>Total RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total RAC en el mismo PCRC</td>
                                        <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>% RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                            <tr>
                                <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                                    <caption>Tabla</caption>
                                    <tr>
                                        <th scope="col">Comparativo del Cumplimiento</th>
                                        <th scope="col">Meta</th>
                                        <th scope="col">Empleado</th>
                                        <th scope="col">Grupo</th>
                                        <th scope="col">Dif Empleado vs. Meta</th>
                                        <th scope="col">Dif Empleado vs. Grupo</th>
                                    </tr>
                                    <tr>
                                        <td>Cumplimiento Promedio</td>
                                        <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                        <?php else : ?>
                            <tr>
                            <tr>
                                <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                                    <caption>Tabla cumplimiento</caption>
                                    <tr>
                                        <th scope="col">Cumplimiento de la Meta</th>
                                        <th scope="col">Cantidad</th>
                                    </tr>
                                    <tr>
                                        <td>Total RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total RAC en el mismo PCRC</td>
                                        <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>% RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                            <tr>
                                <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                                <caption>Cumplimiento</caption>
                                    <tr>
                                        <th scope="col">Comparativo del Cumplimiento</th>
                                        <th scope="col">Meta</th>
                                        <th scope="col">Empleado</th>
                                        <th scope="col">Grupo</th>
                                        <th scope="col">Dif Empleado vs. Meta</th>
                                        <th scope="col">Dif Empleado vs. Grupo</th>
                                    </tr>
                                    <tr>
                                        <td>Cumplimiento Promedio</td>
                                        <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td>
                                <?php if ($data->apregunta7 != "") : ?>
                                    <?= $form->field($data, 'apregunta7')->textInput(['maxlength' => 200, 'disabled' => true])->label($data->ppregunta7) ?>
                                <?php else : ?>
                                    <?= $form->field($data, 'apregunta7')->textInput(['maxlength' => 200])->label($preguntas->pregunta7) ?>
                                <?php endif; ?>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?php if ($data->apregunta8 != "") : ?>
                                    <?= $form->field($data, 'apregunta8')->textInput(['maxlength' => 200, 'disabled' => true])->label($data->ppregunta8) ?>
                                <?php else : ?>
                                    <?= $form->field($data, 'apregunta8')->textInput(['maxlength' => 200])->label($preguntas->pregunta8) ?>
                                <?php endif; ?>

                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>


        <?php } else { ?>
            <div id="datosGenerales" class="col-sm-12">
                <table class="table table-striped table-bordered detail-view formDinamico">
                <caption>Tabla datos generales</caption>
                    <tbody>
                        <tr>
                            <th scope="col">
                                <?= $form->field($data, 'apregunta1')->textInput(['maxlength' => 200, 'disabled' => true])->label($preguntas->pregunta1) ?>
                            </th>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta2')->textInput(['maxlength' => 200, 'disabled' => true])->label($preguntas->pregunta2) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta3')->textInput(['maxlength' => 200, 'disabled' => true])->label($preguntas->pregunta3) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta4')->textInput(['maxlength' => 200, 'disabled' => true])->label($preguntas->pregunta4) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta5')->textInput(['maxlength' => 200, 'disabled' => true])->label($preguntas->pregunta5) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta6')->textInput(['maxlength' => 200, 'disabled' => true])->label($preguntas->pregunta6) ?>
                            </td>
                        </tr>
                        <?php if ($data->respuesta_asesor != "" and $data->respuesta_lider == "" and $lider == "si") : ?>
                            <tr>
                                <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                                <caption>Tabla cumplimiento</caption>
                                    <tr>
                                        <th scope="col">Cumplimiento de la Meta</th>
                                        <th scope="col">Cantidad</th>
                                    </tr>
                                    <tr>
                                        <td>Total RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total RAC en el mismo PCRC</td>
                                        <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>% RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                            <tr>
                                <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                                <caption>Cumplimiento</caption>
                                <tr>
                                        <th scope="col">Comparativo del Cumplimiento</th>
                                        <th scope="col">Meta</th>
                                        <th scope="col">Empleado</th>
                                        <th scope="col">Grupo</th>
                                        <th scope="col">Dif Empleado vs. Meta</th>
                                        <th scope="col">Dif Empleado vs. Grupo</th>
                                    </tr>
                                    <tr>
                                        <td>Cumplimiento Promedio</td>
                                        <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 200]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                        <?php else : ?>
                            <tr>
                            <tr>
                                <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                                <caption>tabla cumplimiento</caption>
                                    <tr>
                                        <th scope="col">Cumplimiento de la Meta</th>
                                        <th scope="col">Cantidad</th>
                                    </tr>
                                    <tr>
                                        <td>Total RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>Total RAC en el mismo PCRC</td>
                                        <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                    <tr>
                                        <td>% RAC que cumplen Meta</td>
                                        <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                            <tr>
                                <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                                <caption>Cumplimiento</caption>
                                <tr>
                                        <th scope="col">Comparativo del Cumplimiento</th>
                                        <th scope="col">Meta</th>
                                        <th scope="col">Empleado</th>
                                        <th scope="col">Grupo</th>
                                        <th scope="col">Dif Empleado vs. Meta</th>
                                        <th scope="col">Dif Empleado vs. Grupo</th>
                                    </tr>
                                    <tr>
                                        <td>Cumplimiento Promedio</td>
                                        <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                        <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 200, 'disabled' => true]) ?></td>
                                    </tr>
                                </table>
                            </tr>
                            </tr>
                        <?php endif; ?>

                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta7')->textInput(['maxlength' => 200, 'disabled' => true])->label($preguntas->pregunta7) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <?= $form->field($data, 'apregunta8')->textInput(['maxlength' => 200, 'disabled' => true])->label($preguntas->pregunta8) ?>
                            </td>
                        </tr>

                    </tbody>
                </table>
            </div>



        <?php } ?>
    <?php endif; ?>
    <?php if ($data->notificacion != 3) { ?>
        <tr>
            <?php if ($_GET['lider'] == "abo" or $_GET['lider'] == "si") { ?>

                <?php if ($data->respuesta_asesor != "") { ?>

                    <td style="text-align: center;"><?= Html::submitButton('1Enviar y Guardar', ['id' => 'boton', 'class' => 'btn btn-primary', 'name' => 'contact-button']) ?></td>

                <?php } ?>

                <?php } else {

                    if ($data->respuesta_asesor == "") { ?>

                    <td style="text-align: center;"><?= Html::submitButton('2Enviar y Guardar', ['id' => 'boton', 'class' => 'btn btn-primary', 'name' => 'contact-button']) ?></td>
            <?php }
                } ?>

        </tr>
    <?php } ?>

    <?php if ($data->notificacion == 3) { ?>


        <table style="width:200%" class="table table-striped table-bordered detail-view formDinamico">
        <caption>Tabla</caption>
            <tr>
                <th scope="col">
                    <h5 style="text-align: center;">"Lo anterior, con fundamento en lo dispuesto en el artículo 62 numeral 9 del Código Sustantivo del Trabajo, el Decreto Reglamentario 1373 de 1966 y el contrato laboral.

                        Por lo tanto, le solicitamos dar respuesta a los planteamientos anteriormente enunciados, en un plazo máximo de ocho (8) días, contados a partir de la fecha de recibo del presente. "</h5>
                </th>
            </tr>
        </table>

        <tr>
            <?php if ($_GET['lider'] == "abo" or $_GET['lider'] == "si") { ?>

                <?php if ($data->respuesta_lider == "" or $data->puntovista_lider == "" or $data->rac_meta  == "" or $data->rac_pcrc  == "" or $data->rac_cumple  == "" or $data->meta  == "" or $data->empleado  == "" or $data->grupo  == "" or $data->dif_empleado_meta  == "" or $data->dif_empleado_grupo == "") { ?>

                    <td style="text-align: center;"><?= Html::submitButton('3Enviar y Guardar', ['id' => 'boton', 'class' => 'btn btn-primary', 'name' => 'contact-button']) ?></td>

                <?php } ?>
                <?php } else {

                    if ($data->respuesta_lider != "" and $data->respuesta_asesor == "") {
                ?>

                    <td style="text-align: center;"><?= Html::submitButton('4Enviar y Guardar', ['id' => 'boton', 'class' => 'btn btn-primary', 'name' => 'contact-button']) ?></td>
            <?php }
                } ?>

        </tr>
    <?php } ?>

    <?php ActiveForm::end(); ?>
<?php endif; ?>
</div>
</div>
</div>




<script type="text/javascript">
    $(document).ready(function() {

        $("#despido").click(function() {
            while (!variable) {
                var variable = prompt("Cual es el motivo del despido?");
                if (variable === null) {
                    return; //break out of the function early
                }
            };
            var asesor = '<?php echo $data->asesor ?>';
            var id = '<?php echo $data->id ?>';
            var motivo = 'Despido';

            ruta = '<?php echo Url::to(['solicitardespido']); ?>?&escalado=' + variable + '&asesor=' + asesor + '&id=' + id + '&motivo=' + motivo;
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: {},
                success: function(response) {
                    $("#despido").hide();
                    $("#permanencia").hide();
                    $(".alertdespido").show();
                }
            });
        });

        $("#confirpermanencia").click(function() {
            var variable = $('#pruprupru').val();

            if (variable == 0) {
                alert('Seleccione una opcion valida');
                return; //break out of the function early
            }

            var asesor = '<?php echo $data->asesor ?>';
            var id = '<?php echo $data->id ?>';
            var motivo = 'Permanencia';

            ruta = '<?php echo Url::to(['solicitarpermanencia']); ?>?&escalado=' + variable + '&asesor=' + asesor + '&id=' + id + '&motivo=' + motivo;
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: {},
                success: function(response) {
                    $("#permanencia").hide();
                    $("#despido").hide();
                    $(".alertpermanencia").show();
                }
            });

        });


        <?php if ($data->notificacion == 3) { ?>

            <?php if ($lider == 'si') { ?>

                $(".btn-primary").click(function(e) {

                    if ($("#notificaciones-puntovista_lider").val() == "" || $("#notificaciones-rac_meta").val() == "" || $("#notificaciones-rac_pcrc").val() == "" || $("#notificaciones-rac_cumple").val() == "" || $("#notificaciones-meta").val() == "" || $("#notificaciones-empleado").val() == "" || $("#notificaciones-grupo").val() == "" || $("#notificaciones-dif_empleado_meta").val() == "" || $("#notificaciones-dif_empleado_grupo").val() == "") {
                        e.preventDefault();
                    }

                });

            <?php } elseif ($lider == "no") { ?>
                $(".btn-primary").click(function(e) {

                    if ($("notificaciones-apregunta1").val() == "" || $("notificaciones-apregunta2").val() == "" || $("notificaciones-apregunta3").val() == "" || $("notificaciones-apregunta4").val() == "" || $("notificaciones-apregunta5").val() == "" || $("notificaciones-apregunta6").val() == "" || $("notificaciones-apregunta7").val() == "" || $("notificaciones-apregunta8").val() == "") {
                        e.preventDefault();
                    }else{
                        //code
                    }

                });

            <?php } ?>

        <?php } else { ?>

            $(".btn-primary").click(function(e) {


            });

        <?php } ?>



    });
</script>



<!DOCTYPE html>
<html lang="en">

<head>
<title>Alertas de desempeño</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <style>
        body {
            font-family: Arial, Helvetica, sans-serif;
        }

        /* The Modal (background) */
        .modal {
            display: none;
            /* Hidden by default */
            position: fixed;
            /* Stay in place */
            z-index: 1;
            /* Sit on top */
            padding-top: 300px;
            /* Location of the box */
            left: 0;
            top: 0;
            width: 100%;
            /* Full width */
            height: 100%;
            /* Full height */
            overflow: auto;
            /* Enable scroll if needed */
            background-color: rgb(0, 0, 0);
            /* Fallback color */
            background-color: rgba(0, 0, 0, 0.4);
            /* Black w/ opacity */
        }

        /* Modal Content */
        .modal-content {
            background-color: #fefefe;
            margin: auto;
            padding: 15px;
            border: 1px solid #888;
            width: 40%;
        }

        /* The Close Button */
        .close {
            color: #aaaaaa;
            float: right;
            font-size: 28px;
            font-weight: bold;
        }

        .close:hover,
        .close:focus {
            color: #000;
            text-decoration: none;
            cursor: pointer;
        }
    </style>
</head>

<body>
    <!-- The Modal -->
    <div id="myModal" class="modal">

        <!-- Modal content -->
        <div class="modal-content" style="text-align: center;">
            <span class="close">&times;</span>
            <p>
            <h1>Titulo</h1>
            </p>
            <p><select id="pruprupru" class="form-control">
                    <option value="0">Seleccionar Opcion...</option>
                    <option value="Motivo de declinacion 1">Motivo de declinacion 1</option>
                    <option value="Motivo de declinacion 2">Motivo de declinacion 2</option>
                    <option value="Motivo de declinacion 3">Motivo de declinacion 3</option>
                    <option value="Motivo de declinacion 4">Motivo de declinacion 4</option>
                </select></p>

            <?= Html::submitButton('Solicitar Permanencia', ['id' => 'confirpermanencia', 'class' => 'btn btn-success', 'name' => 'contact-button']) ?>


        </div>

    </div>

    <script>
        // Get the modal
        var modal = document.getElementById('myModal');

        // Get the button that opens the modal
        var btn = document.getElementById("permanencia");

        // Get the <span> element that closes the modal
        var span = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn.onclick = function() {
            modal.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span.onclick = function() {
            modal.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal) {
                modal.style.display = "none";
            }
        }




        var modal2 = document.getElementById('myModal2');

        // Get the button that opens the modal
        var btn2 = document.getElementById("permanencia");

        // Get the <span> element that closes the modal
        var span2 = document.getElementsByClassName("close")[0];

        // When the user clicks the button, open the modal 
        btn2.onclick = function() {
            modal2.style.display = "block";
        }

        // When the user clicks on <span> (x), close the modal
        span2.onclick = function() {
            modal2.style.display = "none";
        }

        // When the user clicks anywhere outside of the modal, close it
        window.onclick = function(event) {
            if (event.target == modal2) {
                modal2.style.display = "none";
            }
        }
    </script>

</body>

</html>