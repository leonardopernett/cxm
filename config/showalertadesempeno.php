<?php
//include '../views/plantillasForm/plantilla' . $data->formulario->id_plantilla_form . '.php';

//echo "<pre>";
//print_r($detallesseccion_id);
//echo "</pre>";
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;

$dos = '$model->apregunta1 != "" AND $model->apregunta2 != "" AND $model->apregunta3 != "" AND $model->apregunta4 != "" AND $model->apregunta5 != "" AND $model->apregunta6 != "" AND $model->apregunta6 != "" AND $model->apregunta6 != "" AND $model->rac_meta != "" AND $model->rac_pcrc != "" AND $model->rac_cumple != "" AND $model->meta != "" AND $model->empleado != "" AND $model->grupo != "" AND $model->dif_empleado_meta != "" AND $model->dif_empleado_grupo != ""';

//print_r($prueba); die;

use yii\bootstrap\ActiveForm;

$this->title = 'Alertas';
?>

<div id="Row" class="row">
<div class="page-header">
        <h3>Notificacion de bajo desempeño</h3>
    </div>
    <div id="Datogenerales" class="col-md-6 datogenerales">
        <div class="row seccion-informacion" class="col-md-12">
    <div   class="col-md-10">
        <label class="labelseccion ">
            <?= 'Informacion asesor' ?>  
        </label>
    </div>
    <div   class="col-md-2">
        <?= Html::a(Html::tag("span", "", ["aria-hidden" => "true", "class" => "glyphicon glyphicon-chevron-downForm", ]) . "", "javascript:void(0)", ["class" => "openSeccion", "id" => "labelPartida"]) ?>
    </div>
        <?php $this->registerJs('$("#labelPartida").click(function () {
                                $("#datosPartida").toggle("slow");
                            });'); ?>
</div>
        <div id="datosPartida" class="col-sm-12" style="">
            <table class="table table-striped table-bordered detail-view formDinamico">
                <tbody>
                    <tr>
                        <th><?php echo Yii::t("app", "Nombre"); ?></th>
                        <td><?php echo $datos->name ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t("app", "Asesor"); ?></th>
                        <td><?php echo $data->asesor ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t("app", "Identificacion"); ?></th>
                        <td><?php echo $datos->identificacion ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t("app", "Desempeño"); ?></th>
                        <td><?php echo $prueba ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t("app", "Lider"); ?></th>
                        <td><?php echo $data->lider ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t("app", "Año"); ?></th>
                        <td><?php echo $data->ano ?></td>
                    </tr>
                    <tr>
                        <th><?php echo Yii::t("app", "Mes"); ?></th>
                        <td><?php echo $data->mes ?></td>
                    </tr>

                    <tr>
                        <th><?php echo Yii::t("app", "# Notificacion"); ?></th>
                        <td><?php echo $data->notificacion ?></td>
                    </tr>

                    <tr>
                        <th><?php echo Yii::t("app", "Fecha Notificacion"); ?></th>
                        <td><?php echo $data->fecha_ingreso ?></td>
                    </tr>

                    <tr>
                        <th><?php echo Yii::t("app", "Fecha Cierre"); ?></th>
                        <td><?php echo $data->fecha_finalizacion ?></td>
                    </tr>
                    <?php if (isset($permanencia->p_justificacion) AND $data->notificacion == 3 AND $lider == "abo"): ?>
                        <tr>
                        <th><?php echo Yii::t("app", "Justificacion Permanencia"); ?></th>
                        <td><?php echo $permanencia->p_justificacion ?></td>
                    </tr>
                    <?php endif; ?>

                    <?php if (isset($despido->d_justificacion) AND $data->notificacion == 3 AND $lider == "abo"): ?>
                        <tr>
                        <th><?php echo Yii::t("app", "Justificacion Despido"); ?></th>
                        <td><?php echo $despido->d_justificacion ?></td>
                    </tr>
                    <?php endif; ?>

                        <?php if (isset($jefeop)): ?>
                    <tr>
                            <?php if ($data->solicitud_despido == "si" OR $data->solicitud_permanencia == "si"): ?>
                            <?php else: ?>
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
        <div   class="col-md-10">
            <label class="labelseccion ">
                <?= 'Gestion de Notificacion' ?>  
            </label>
        </div>
        <div   class="col-md-2">
            <?=
            Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                        "class" => "glyphicon glyphicon-chevron-downForm",
                    ]) . "", "javascript:void(0)"
                    , ["class" => "openSeccion", "id" => "labelGenerales"])
            ?>
        </div>
        <?php $this->registerJs('$("#labelGenerales").click(function () {
                $("#datosGenerales").toggle("slow");
            });'); ?>
    </div>

            <?php if (Yii::$app->session->hasFlash('enviado')): ?>


                <div id="datosGenerales" class="col-sm-6" style="">
                    <table class="table table-striped table-bordered detail-view formDinamico">
                        <tbody>
                            <tr>
                                <td colspan="3"><div class="alert alert-success">   
                                    Respuesta Guardada Satisfactoriamente.
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Compromisos de Gestion del Asesor:</b> <?=$data->respuesta_asesor?> </p>   
                                </td>
                            </tr>                     
                            <tr>
                                <td>
                                    <p><b>Feedback Lider:</b> <?=$data->respuesta_lider?> </p>   
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <p><b>Opinion Lider:</b> <?=$data->puntovista_lider?> </p>   
                                </td>
                            </tr>
                            
                        </tbody>
                    </table>
                </div>

                <?php if($data->notificacion == 3): ?>
                    <div id="datosGenerales" class="col-sm-12" style="">
                        <table class="table table-striped table-bordered detail-view formDinamico">
                            <tbody>
                                <tr>
                                    <td>
                                        <b>Pregunta 1:</b> <?=$data->apregunta1?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Pregunta 2:</b> <?=$data->apregunta2?>
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Pregunta 3:</b> <?=$data->apregunta3?>            
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Pregunta 4:</b> <?=$data->apregunta4?>            
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Pregunta 5:</b> <?=$data->apregunta5?>            
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Pregunta 6:</b> <?=$data->apregunta6?>            
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Pregunta 5:</b> <?=$data->apregunta7?>            
                                    </td>
                                </tr>
                                <tr>
                                    <td>
                                        <b>Pregunta 6:</b> <?=$data->apregunta8?>            
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                <?php endif; ?>

            <?php else: ?>

                <div id="datosGenerales" class="col-sm-12" style="">
                    <table class="table table-striped table-bordered detail-view formDinamico">
                        <tbody>
                            <?php $form = ActiveForm::begin(); ?>             
                            
                            <?php if($data->notificacion == "3"): ?>

                                <tr>
                                    <td>
                                        <?php if($lider == "si" OR isset($jefeop) OR $data->respuesta_asesor != ""): ?>
                                            <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6, 'disabled' => true])->label('Compromisos de Gestion del Asesor:') ?>
                                        <?php else: ?>
                                            <?php if($data->respuesta_lider != "" AND $data->rac_meta != "" AND $data->rac_pcrc != "" AND $data->rac_cumple != "" AND $data->meta != "" AND $data->empleado != "" AND $data->grupo != "" AND $data->dif_empleado_meta != "" AND $data->dif_empleado_grupo != ""): ?>
                                                <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6])->label('Compromisos de Gestion del Asesor:') ?>
                                            <?php else: ?>
                                                <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6, 'disabled' => true])->label('Compromisos de Gestion del Asesor:') ?>
                                            <?php endif; ?>
                                        <?php endif; ?>    
                                    </td>
                                </tr>                     
                                <tr>
                                    <td>
                                        <?php if($data->respuesta_lider == "" AND $_GET['lider'] == "si"): ?>

                                            <?= $form->field($data, 'respuesta_lider')->textArea(['rows' => 6])->label('Feedback Lider:') ?>

                                        <?php elseif($data->respuesta_lider != "" OR isset($jefeop) OR $_GET['lider'] == "no"): ?>

                                            <?= $form->field($data, 'respuesta_lider')->textArea(['rows' => 6, 'disabled' => true])->label('Feedback Lider:') ?>

                                        <?php endif; ?>
                                    </td>
                                </tr>





                                <?php if($_GET['lider'] == "si" AND $data->puntovista_lider == "" ): ?>
                                    <tr>
                                        <td>    
                                            <?= $form->field($data, 'puntovista_lider')->textArea(['rows' => 6])->label('Punto de vista Lider:') ?>
                                        </td>
                                    </tr>
                                <?php elseif($lider=="abo" OR ($data->puntovista_lider != "" AND isset($jefeop))): ?> 

                                    <tr>
                                        <td>
                                            <?= $form->field($data, 'puntovista_lider')->textArea(['rows' => 6, 'disabled' => true])->label('Punto de vista Lider:') ?>
                                        </td>
                                    </tr>
                                <?php endif; ?> 
                            

                            <?php else: ?> 


<!--  -->


                                <tr>
                                    <td>
                                        <?php if($lider =="abo" OR $data->respuesta_asesor != "" OR ($data->respuesta_lider == "" AND $lider == "si")): ?>
                                            <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6, 'disabled' => true])->label('Compromisos de Gestion del Asesor:') ?>
                                        <?php else: ?>
                                            <?= $form->field($data, 'respuesta_asesor')->textArea(['rows' => 6])->label('Compromisos de Gestion del Asesor:') ?>
                                        <?php endif; ?>  
                                    </td>
                                </tr>                     
                                <tr>
                                    <td>
                                        <?php if($lider == "si" AND $data->respuesta_asesor != "" AND $data->respuesta_lider == ""): ?>
                                            <?= $form->field($data, 'respuesta_lider')->textArea(['rows' => 6])->label('Feedback Lider:') ?>
                                        <?php else: ?>
                                            <?= $form->field($data, 'respuesta_lider')->textArea(['rows' => 6, 'disabled' => true])->label('Feedback Lider:') ?>
                                        <?php endif; ?>
                                    </td>
                                </tr>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                


            </div>
                











<?php if ($data->notificacion == 3): ?>

    <?php if ($_GET['lider'] == "si" OR isset($jefeop) ){ ?> 
    <div id="datosGenerales" class="col-sm-12" style="">
        <table class="table table-striped table-bordered detail-view formDinamico">
            <tbody>
                <tr>
                    <td>
                        <?= $form->field($data, 'apregunta1')->textInput(['maxlength' => 100, 'disabled' => true])->label('1. ¿Conoce Usted el Esquema de Seguimiento de sus resultados, definido por la compañía en relación al cumplimiento de las metas semanales?. Por favor, sírvase explicar.') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $form->field($data, 'apregunta2')->textInput(['maxlength' => 100, 'disabled' => true])->label('2. ¿Sabía Usted cuál era la meta mínima (o meta programada) fijada para el Mes?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $form->field($data, 'apregunta3')->textInput(['maxlength' => 100, 'disabled' => true])->label('3. ¿Sabe usted que sus resultados de el mes están por debajo de la meta mínima (o meta programada) establecida para su línea ?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $form->field($data, 'apregunta4')->textInput(['maxlength' => 100, 'disabled' => true])->label('4. Sírvase explicar ¿por qué Usted la semana anterior incumplió con la meta mínima (o meta programada) exigida?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $form->field($data, 'apregunta5')->textInput(['maxlength' => 100, 'disabled' => true])->label('5. Si se le ha recalcado en varias oportunidades la importancia de mejorar su rendimiento por medio de llamados de atención, ¿por qué no ha dado los resultados esperados por la compañía?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $form->field($data, 'apregunta6')->textInput(['maxlength' => 100, 'disabled' => true])->label('6. Conforme al cuadro comparativo que a continuación se anexa, sírvase explicar por qué sus compañeros de trabajo en igualdad de condiciones que Usted, sí cumplen con la meta mínima (o meta programada) exigida?') ?>
                    </td>
                </tr>

                <?php if ($_GET['lider'] == "si"){ ?> 
                    <tr>
                        <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                            <tr>
                                <th>Cumplimiento de la Meta</th>
                                <th>Cantidad</th>
                            </tr>
                            <tr>
                                <td>Total RAC que cumplen  Meta</td>
                                <?php if($data->rac_meta == ""): ?>
                                    <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <?php else: ?>
                                    <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <?php endif; ?>
                                
                            </tr>
                            <tr>
                                <td>Total RAC en el mismo PCRC</td>
                                <?php if($data->rac_pcrc == ""): ?>
                                    <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <?php else: ?>
                                    <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <?php endif; ?>
                            </tr>
                            <tr>
                                <td>% RAC que cumplen Meta</td>
                                <?php if($data->rac_cumple == ""): ?>
                                    <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <?php else: ?>
                                    <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <?php endif; ?>
                                
                            </tr>
                        </table>
                    </tr>
                    <tr>
                        <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                            <tr>
                                <th>Comparativo del Cumplimiento</th>
                                <th>Meta</th>
                                <th>Empleado</th>
                                <th>Grupo</th>
                                <th>Dif Empleado vs. Meta</th>
                                <th>Dif Empleado vs. Grupo</th>
                            </tr>
                            <tr>
                                <td>Cumplimiento Promedio</td>
                                <?php if($data->meta == ""): ?>
                                    <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <?php else: ?>
                                    <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <?php endif; ?>
                                <?php if($data->empleado == ""): ?>
                                    <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <?php else: ?>
                                    <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <?php endif; ?>
                                <?php if($data->grupo == ""): ?>
                                    <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <?php else: ?>
                                    <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <?php endif; ?>
                                <?php if($data->dif_empleado_meta == ""): ?>
                                    <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <?php else: ?>
                                    <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <?php endif; ?>
                                <?php if($data->dif_empleado_grupo == ""): ?>
                                    <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <?php else: ?>
                                    <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <?php endif; ?>
                            </tr>
                        </table>    
                    </tr>
                <?php } else { ?>

                <tr>
                    <tr>
                        <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                            <tr>
                                <th>Cumplimiento de la Meta</th>
                                <th>Cantidad</th>
                            </tr>
                            <tr>
                                <td>Total RAC que cumplen  Meta</td>
                                <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                            </tr>
                            <tr>
                                <td>Total RAC en el mismo PCRC</td>
                                <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                            </tr>
                            <tr>
                                <td>% RAC que cumplen Meta</td>
                                <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                            </tr>
                        </table>
                    </tr>
                    <tr>
                        <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                            <tr>
                                <th>Comparativo del Cumplimiento</th>
                                <th>Meta</th>
                                <th>Empleado</th>
                                <th>Grupo</th>
                                <th>Dif Empleado vs. Meta</th>
                                <th>Dif Empleado vs. Grupo</th>
                            </tr>
                            <tr>
                                <td>Cumplimiento Promedio</td>
                                <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                            </tr>
                        </table>    
                    </tr>
                </tr>

                <?php }?>
                <tr>
                    <td>
                        <?= $form->field($data, 'apregunta7')->textInput(['maxlength' => 100, 'disabled' => true])->label('7. ¿Sabe usted que con su bajo desempeño afecta los Objetivos Corporativos de KONECTA?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?= $form->field($data, 'apregunta8')->textInput(['maxlength' => 100, 'disabled' => true])->label('8. ¿Sabe usted las consecuencias que como trabajador podría acarrear su deficiente desempeño en ventas?') ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>













    <?php } elseif ($data->respuesta_lider != "" AND $data->rac_meta != "" AND $data->rac_pcrc != "" AND $data->rac_cumple != "" AND $data->meta != "" AND $data->empleado != "" AND $data->grupo != "" AND $data->dif_empleado_meta != "" AND $data->dif_empleado_grupo != "" AND $data->puntovista_lider) { ?>
    <div id="datosGenerales" class="col-sm-12" style="">
        <table class="table table-striped table-bordered detail-view formDinamico">
            <tbody>
                <tr>
                    <td>
                        <?php if($data->apregunta1 != ""): ?>
                            <?= $form->field($data, 'apregunta1')->textInput(['maxlength' => 100, 'disabled' => true])->label('1. ¿Conoce Usted el Esquema de Seguimiento de sus resultados, definido por la compañía en relación al cumplimiento de las metas semanales?. Por favor, sírvase explicar.') ?>
                        <?php else: ?>
                            <?= $form->field($data, 'apregunta1')->textInput(['maxlength' => 100])->label('1. ¿Conoce Usted el Esquema de Seguimiento de sus resultados, definido por la compañía en relación al cumplimiento de las metas semanales?. Por favor, sírvase explicar.') ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($data->apregunta2 != ""): ?>
                            <?= $form->field($data, 'apregunta2')->textInput(['maxlength' => 100, 'disabled' => true])->label('2. ¿Sabía Usted cuál era la meta mínima (o meta programada) fijada para el Mes?') ?>
                        <?php else: ?>
                            <?= $form->field($data, 'apregunta2')->textInput(['maxlength' => 100])->label('2. ¿Sabía Usted cuál era la meta mínima (o meta programada) fijada para el Mes?') ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($data->apregunta3 != ""): ?>
                            <?= $form->field($data, 'apregunta3')->textInput(['maxlength' => 100, 'disabled' => true])->label('3. ¿Sabe usted que sus resultados de el mes están por debajo de la meta mínima (o meta programada) establecida para su línea ?') ?>
                        <?php else: ?>
                            <?= $form->field($data, 'apregunta3')->textInput(['maxlength' => 100])->label('3. ¿Sabe usted que sus resultados de el mes están por debajo de la meta mínima (o meta programada) establecida para su línea ?') ?>
                        <?php endif; ?>

                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($data->apregunta4 != ""): ?>
                            <?= $form->field($data, 'apregunta4')->textInput(['maxlength' => 100, 'disabled' => true])->label('4. Sírvase explicar ¿por qué Usted la semana anterior incumplió con la meta mínima (o meta programada) exigida?') ?>
                        <?php else: ?>
                            <?= $form->field($data, 'apregunta4')->textInput(['maxlength' => 100])->label('4. Sírvase explicar ¿por qué Usted la semana anterior incumplió con la meta mínima (o meta programada) exigida?') ?>
                        <?php endif; ?>
                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($data->apregunta5 != ""): ?>
                            <?= $form->field($data, 'apregunta5')->textInput(['maxlength' => 100, 'disabled' => true])->label('5. Si se le ha recalcado en varias oportunidades la importancia de mejorar su rendimiento por medio de llamados de atención, ¿por qué no ha dado los resultados esperados por la compañía?') ?>
                        <?php else: ?>
                            <?= $form->field($data, 'apregunta5')->textInput(['maxlength' => 100])->label('5. Si se le ha recalcado en varias oportunidades la importancia de mejorar su rendimiento por medio de llamados de atención, ¿por qué no ha dado los resultados esperados por la compañía?') ?>
                        <?php endif; ?>

                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($data->apregunta6 != ""): ?>
                            <?= $form->field($data, 'apregunta6')->textInput(['maxlength' => 100, 'disabled' => true])->label('6. Conforme al cuadro comparativo que a continuación se anexa, sírvase explicar por qué sus compañeros de trabajo en igualdad de condiciones que Usted, sí cumplen con la meta mínima (o meta programada) exigida?') ?>
                        <?php else: ?>
                            <?= $form->field($data, 'apregunta6')->textInput(['maxlength' => 100])->label('6. Conforme al cuadro comparativo que a continuación se anexa, sírvase explicar por qué sus compañeros de trabajo en igualdad de condiciones que Usted, sí cumplen con la meta mínima (o meta programada) exigida?') ?>
                        <?php endif; ?>

                    </td>
                </tr>
                <?php if($data->respuesta_asesor != "" AND $data->respuesta_lider == "" AND $lider == "si" ): ?>
                    <tr>
                        <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                            <tr>
                                <th>Cumplimiento de la Meta</th>
                                <th>Cantidad</th>
                            </tr>
                            <tr>
                                <td>Total RAC que cumplen  Meta</td>
                                <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                            </tr>
                            <tr>
                                <td>Total RAC en el mismo PCRC</td>
                                <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                            </tr>
                            <tr>
                                <td>% RAC que cumplen Meta</td>
                                <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                            </tr>
                        </table>
                    </tr>
                    <tr>
                        <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                            <tr>
                                <th>Comparativo del Cumplimiento</th>
                                <th>Meta</th>
                                <th>Empleado</th>
                                <th>Grupo</th>
                                <th>Dif Empleado vs. Meta</th>
                                <th>Dif Empleado vs. Grupo</th>
                            </tr>
                            <tr>
                                <td>Cumplimiento Promedio</td>
                                <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                            </tr>
                        </table>    
                    </tr>
                <?php else: ?>
                    <tr>
                        <tr>
                            <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                                <tr>
                                    <th>Cumplimiento de la Meta</th>
                                    <th>Cantidad</th>
                                </tr>
                                <tr>
                                    <td>Total RAC que cumplen  Meta</td>
                                    <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                </tr>
                                <tr>
                                    <td>Total RAC en el mismo PCRC</td>
                                    <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                </tr>
                                <tr>
                                    <td>% RAC que cumplen Meta</td>
                                    <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                </tr>
                            </table>
                        </tr>
                        <tr>
                            <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                                <tr>
                                    <th>Comparativo del Cumplimiento</th>
                                    <th>Meta</th>
                                    <th>Empleado</th>
                                    <th>Grupo</th>
                                    <th>Dif Empleado vs. Meta</th>
                                    <th>Dif Empleado vs. Grupo</th>
                                </tr>
                                <tr>
                                    <td>Cumplimiento Promedio</td>
                                    <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                    <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                    <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                    <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                    <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                </tr>
                            </table>    
                        </tr>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td>
                        <?php if($data->apregunta7 != ""): ?>
                            <?= $form->field($data, 'apregunta7')->textInput(['maxlength' => 100, 'disabled' => true])->label('7. ¿Sabe usted que con su bajo desempeño afecta los Objetivos Corporativos de KONECTA?') ?>
                        <?php else: ?>
                            <?= $form->field($data, 'apregunta7')->textInput(['maxlength' => 100])->label('7. ¿Sabe usted que con su bajo desempeño afecta los Objetivos Corporativos de KONECTA?') ?>
                        <?php endif; ?>

                    </td>
                </tr>
                <tr>
                    <td>
                        <?php if($data->apregunta8 != ""): ?>
                            <?= $form->field($data, 'apregunta8')->textInput(['maxlength' => 100, 'disabled' => true])->label('8. ¿Sabe usted las consecuencias que como trabajador podría acarrear su deficiente desempeño en ventas?') ?>
                        <?php else: ?>
                            <?= $form->field($data, 'apregunta8')->textInput(['maxlength' => 100])->label('8. ¿Sabe usted las consecuencias que como trabajador podría acarrear su deficiente desempeño en ventas?') ?>
                        <?php endif; ?>

                    </td>
                </tr>

            </tbody>
        </table>
    </div>


    <?php } else { ?>











    <div id="datosGenerales" class="col-sm-12" style="">
        <table class="table table-striped table-bordered detail-view formDinamico">
            <tbody>
                <tr>
                    <td>
                            <?= $form->field($data, 'apregunta1')->textInput(['maxlength' => 100, 'disabled' => true])->label('1. ¿Conoce Usted el Esquema de Seguimiento de sus resultados, definido por la compañía en relación al cumplimiento de las metas semanales?. Por favor, sírvase explicar.') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                            <?= $form->field($data, 'apregunta2')->textInput(['maxlength' => 100, 'disabled' => true])->label('2. ¿Sabía Usted cuál era la meta mínima (o meta programada) fijada para el Mes?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                            <?= $form->field($data, 'apregunta3')->textInput(['maxlength' => 100, 'disabled' => true])->label('3. ¿Sabe usted que sus resultados de el mes están por debajo de la meta mínima (o meta programada) establecida para su línea ?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                            <?= $form->field($data, 'apregunta4')->textInput(['maxlength' => 100, 'disabled' => true])->label('4. Sírvase explicar ¿por qué Usted la semana anterior incumplió con la meta mínima (o meta programada) exigida?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                            <?= $form->field($data, 'apregunta5')->textInput(['maxlength' => 100, 'disabled' => true])->label('5. Si se le ha recalcado en varias oportunidades la importancia de mejorar su rendimiento por medio de llamados de atención, ¿por qué no ha dado los resultados esperados por la compañía?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                            <?= $form->field($data, 'apregunta6')->textInput(['maxlength' => 100, 'disabled' => true])->label('6. Conforme al cuadro comparativo que a continuación se anexa, sírvase explicar por qué sus compañeros de trabajo en igualdad de condiciones que Usted, sí cumplen con la meta mínima (o meta programada) exigida?') ?>
                    </td>
                </tr>
                <?php if($data->respuesta_asesor != "" AND $data->respuesta_lider == "" AND $lider == "si" ): ?>
                    <tr>
                        <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                            <tr>
                                <th>Cumplimiento de la Meta</th>
                                <th>Cantidad</th>
                            </tr>
                            <tr>
                                <td>Total RAC que cumplen  Meta</td>
                                <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                            </tr>
                            <tr>
                                <td>Total RAC en el mismo PCRC</td>
                                <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                            </tr>
                            <tr>
                                <td>% RAC que cumplen Meta</td>
                                <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                            </tr>
                        </table>
                    </tr>
                    <tr>
                        <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                            <tr>
                                <th>Comparativo del Cumplimiento</th>
                                <th>Meta</th>
                                <th>Empleado</th>
                                <th>Grupo</th>
                                <th>Dif Empleado vs. Meta</th>
                                <th>Dif Empleado vs. Grupo</th>
                            </tr>
                            <tr>
                                <td>Cumplimiento Promedio</td>
                                <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                                <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 100])?></td>
                            </tr>
                        </table>    
                    </tr>
                <?php else: ?>
                    <tr>
                        <tr>
                            <table style="width:50%" class="table table-striped table-bordered detail-view formDinamico">
                                <tr>
                                    <th>Cumplimiento de la Meta</th>
                                    <th>Cantidad</th>
                                </tr>
                                <tr>
                                    <td>Total RAC que cumplen  Meta</td>
                                    <td><?= $form->field($data, 'rac_meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                </tr>
                                <tr>
                                    <td>Total RAC en el mismo PCRC</td>
                                    <td><?= $form->field($data, 'rac_pcrc', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                </tr>
                                <tr>
                                    <td>% RAC que cumplen Meta</td>
                                    <td><?= $form->field($data, 'rac_cumple', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                </tr>
                            </table>
                        </tr>
                        <tr>
                            <table style="width:80%" class="table table-striped table-bordered detail-view formDinamico">
                                <tr>
                                    <th>Comparativo del Cumplimiento</th>
                                    <th>Meta</th>
                                    <th>Empleado</th>
                                    <th>Grupo</th>
                                    <th>Dif Empleado vs. Meta</th>
                                    <th>Dif Empleado vs. Grupo</th>
                                </tr>
                                <tr>
                                    <td>Cumplimiento Promedio</td>
                                    <td><?= $form->field($data, 'meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                    <td><?= $form->field($data, 'empleado', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                    <td><?= $form->field($data, 'grupo', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                    <td><?= $form->field($data, 'dif_empleado_meta', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                    <td><?= $form->field($data, 'dif_empleado_grupo', ['enableLabel' => false])->textInput(['maxlength' => 100, 'disabled' => true])?></td>
                                </tr>
                            </table>    
                        </tr>
                    </tr>
                <?php endif; ?>

                <tr>
                    <td>
                            <?= $form->field($data, 'apregunta7')->textInput(['maxlength' => 100, 'disabled' => true])->label('7. ¿Sabe usted que con su bajo desempeño afecta los Objetivos Corporativos de KONECTA?') ?>
                    </td>
                </tr>
                <tr>
                    <td>
                            <?= $form->field($data, 'apregunta8')->textInput(['maxlength' => 100, 'disabled' => true])->label('8. ¿Sabe usted las consecuencias que como trabajador podría acarrear su deficiente desempeño en ventas?') ?>
                    </td>
                </tr>

            </tbody>
        </table>
    </div>



    <?php } ?>
<?php endif; ?>
























































                <table style="width:100%" class="table table-striped table-bordered detail-view formDinamico">
                    <tr>
                        <td>
                            <h5 align="justify">"Lo anterior, con fundamento en lo dispuesto en el artículo 62 numeral 9 del Código Sustantivo del Trabajo, el Decreto Reglamentario 1373 de 1966 y el contrato laboral.

                                Por lo tanto, le solicitamos dar respuesta a los planteamientos anteriormente enunciados, en un plazo máximo de ocho (8) días, contados a partir de la fecha de recibo del presente. "</h5>                            
                            </td>
                        </tr>
                        <tr>
                            <?php if($_GET['lider'] == "abo"): ?>


                                
                            <?php else: ?>

                                <td align="center"><?= Html::submitButton('Submit', ['class' => 'btn btn-primary', 'name' => 'contact-button']) ?></td>

                            <?php endif; ?>

                        </tr>
                    </table> 

                <?php ActiveForm::end(); ?>
            <?php endif; ?>
        </div>
    </div>
</div>




<script type="text/javascript">
    $(document).ready(function () {

        $("#despido").click(function () {
            while(!variable){
                var variable = prompt ("Cual es el motivo del despido?");
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
                    data: {
                    },
                    success: function (response) {
                        $("#despido").hide();
                        $("#permanencia").hide();
                        $(".alertdespido").show();
                    }
                });
        });

        $("#permanencia").click(function () {
            while(!variable){
                var variable = prompt ("Cual es el motivo para solicitar Permanencia?");
                if (variable === null) {
                    return; //break out of the function early
                }
            };            
            var asesor = '<?php echo $data->asesor ?>';
            var id = '<?php echo $data->id ?>';
            var motivo = 'Permanencia';
            
            ruta = '<?php echo Url::to(['solicitarpermanencia']); ?>?&escalado=' + variable + '&asesor=' + asesor + '&id=' + id + '&motivo=' + motivo;
                $.ajax({
                    type: 'POST',
                    cache: false,
                    url: ruta,
                    data: {
                    },
                    success: function (response) {
                        $("#permanencia").hide();
                        $("#despido").hide();
                        $(".alertpermanencia").show();
                    }
                });
        });
    });
</script>


