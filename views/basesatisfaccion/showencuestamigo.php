<?php
//include '../views/plantillasForm/plantilla' . $data->formulario->id_plantilla_form . '.php';

//echo "<pre>";
//print_r($detallesseccion_id);
//echo "</pre>";
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
?>


<div class="page-header">
            <h3>Encuesta Realizada</h3>
    </div>
<div id="Row" class="row">
    <div id="Datogenerales" class="col-md-6 datogenerales">
        <div class="row seccion-informacion" class="col-md-12">
    <div   class="col-md-10">
        <label class="labelseccion ">
            <?= 'Información de Partida' ?>  
        </label>
    </div>
    <div   class="col-md-2">
        <?= Html::a(Html::tag("span", "", ["aria-hidden" => "true", "class" => "glyphicon glyphicon-chevron-downForm", ]) . "", "javascript:void(0)", ["class" => "openSeccion", "id" => "labelPartida"]) ?>
    </div>
        <?php $this->registerJs('$("#labelPartida").click(function () {
                                $("#datosPartida").toggle("slow");
                            });'); ?>
</div>
        <div id="datosPartida" class="col-sm-12" style="display: none">
            <table class="table table-striped table-bordered detail-view formDinamico">
            <caption>Tabla datos partida</caption>
                <tbody>
                    <tr>
                        <th id="ani"><?php echo Yii::t("app", "ANI"); ?></th>
                        <td><?php echo $data->ani ?></td>
                    </tr>
                    <tr>
                        <th id="identificacion"><?php echo Yii::t("app", "Identificación"); ?></th>
                        <td><?php echo $data->identificacion ?></td>
                    </tr>
                    <tr>
                        <th id="nombre"><?php echo Yii::t("app", "Nombre"); ?></th>
                        <td><?php echo $data->nombre ?></td>
                    </tr>
                    <tr>
                        <th id="ext"><?php echo Yii::t("app", "Ext"); ?></th>
                        <td><?php echo $data->ext ?></td>
                    </tr>
                    <tr>
                        <th id="tipoServicio"><?php echo Yii::t("app", "Tipo Servicio"); ?></th>
                        <td><?php echo $data->tipo_servicio ?></td>
                    </tr>
                    <tr>
                        <th id="tipoEncuesta"><?php echo Yii::t("app", "Tipo encuesta"); ?></th>
                        <td><?php echo $data->tipo_encuesta ?></td>
                    </tr>
                    <tr>
                        <th id="rn"><?php echo Yii::t("app", "RN"); ?></th>
                        <td><?php echo $data->rn ?></td>
                    </tr>
                    <tr>
                        <th id="agente"><?php echo Yii::t("app", "Agente"); ?></th>
                        <td><?php echo $data->agente ?></td>
                    </tr>
                    <tr>
                        <th id="tipologia">Tipología</th>
                        <td>
                            <select id="categoria" class="form-control" name="categoria" disabled="">
                                <option value="<?php echo $data->tipologia ?>"><?php echo $data->tipologia ?></option>
                            </select>
                        </td>
                    </tr>
                    <tr>
                        <th id="connid"><?php echo Yii::t("app", "Connid"); ?></th>
                        <td><?php echo $data->connid; ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta1"><?php echo (strtoupper($preguntas['0']['nombre']) != 'NO APLICA') ? $preguntas['0']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['0']['nombre']) != 'NO APLICA') ? $data->pregunta1 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta2"><?php echo (strtoupper($preguntas['1']['nombre']) != 'NO APLICA') ? $preguntas['1']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['1']['nombre']) != 'NO APLICA') ? $data->pregunta2 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta3"><?php echo (strtoupper($preguntas['2']['nombre']) != 'NO APLICA') ? $preguntas['2']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['2']['nombre']) != 'NO APLICA') ? $data->pregunta3 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta4"><?php echo (strtoupper($preguntas['3']['nombre']) != 'NO APLICA') ? $preguntas['3']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['3']['nombre']) != 'NO APLICA') ? $data->pregunta4 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta4"><?php echo (strtoupper($preguntas['4']['nombre']) != 'NO APLICA') ? $preguntas['4']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['4']['nombre']) != 'NO APLICA') ? $data->pregunta5 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta5"><?php echo (strtoupper($preguntas['5']['nombre']) != 'NO APLICA') ? $preguntas['5']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['5']['nombre']) != 'NO APLICA') ? $data->pregunta6 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta6"><?php echo (strtoupper($preguntas['6']['nombre']) != 'NO APLICA') ? $preguntas['6']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['6']['nombre']) != 'NO APLICA') ? $data->pregunta7 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta7"><?php echo (strtoupper($preguntas['7']['nombre']) != 'NO APLICA') ? $preguntas['7']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['7']['nombre']) != 'NO APLICA') ? $data->pregunta8 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta8"><?php echo (strtoupper($preguntas['8']['nombre']) != 'NO APLICA') ? $preguntas['8']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['8']['nombre']) != 'NO APLICA') ? $data->pregunta9 : 'NO APLICA' ?></td>
                    </tr>
                    <tr>
                        <th id="pregunta9"><?php echo (strtoupper($preguntas['9']['nombre']) != 'NO APLICA') ? $preguntas['9']['enunciado_pre'] : 'NO APLICA' ?></th>
                        <td><?php echo (strtoupper($preguntas['9']['nombre']) != 'NO APLICA') ? $data->pregunta10 : 'NO APLICA' ?></td>
                    </tr>                                                    
                </tbody>
            </table>
        </div>
    </div>                                                                            
                                                                                    
<!-- FIN Informacion de partida-->
                    
    <div id="Guiainspiracion" class="col-md-6 guiainspiracion">
        <div class="row seccion-data" class="col-md-12">
    <div   class="col-md-10">
        <label class="labelseccion ">
            <?= 'Gestión de la Satisfacción' ?>  
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
                <div id="datosGenerales" class="col-sm-12" style="display: none">
                    <table class="table table-striped table-bordered detail-view formDinamico">
                    <caption>Tabla datos generales</caption>
                        <tbody>
                            <tr>
                                <th id="liderEquipo">Lider Equipo</th>
                                <td><?php echo $data->lider_equipo ?></td>
                            </tr>
                            <tr>
                                <th id="programaPcrc">Programa/PCRC</th>
                                <td><?php echo $nuevo->pcrc->name ?></td>
                            </tr>
                            <tr>
                                <th id="cliente">Cliente</th>
                                <td><?php echo $nuevo->cliente->name ?></td>
                            </tr>                     
                            <tr>
                                <th id="valorado">Valorado</th>
                                <td><?php echo $nuevo->evaluado->name ?></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>


