<?php
include '../views/plantillasForm/plantilla' . $data->formulario->id_plantilla_form . '.php';

use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\ArrayHelper;
use yii\bootstrap\ActiveForm;

$varPcrc = $data->tmp_formulario->arbol_id;
$varBase = $data->tmp_formulario->basesatisfaccion_id;

$varcontenidoKaliope = $varcontenido;
$varmotivos=null;
if(!isset($_GET['motivo'])){
    $motivo = null;
}

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$varIdClientes = (new \yii\db\Query())
                ->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                ->from(['tbl_proceso_cliente_centrocosto'])   
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                        'tbl_proceso_cliente_centrocosto.cliente = tbl_arbols.name
                            AND tbl_proceso_cliente_centrocosto.estado = 1')  

                ->where(['=','tbl_arbols.id',$data->basesatisfaccion->cliente])
                ->groupby(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])
                ->scalar();

if (!$varIdClientes) {
    $varIdClientes = substr($data->basesatisfaccion->pcrc0->name, 0,3);
}

?>


<?php $this->title = Yii::t('app', 'Realizar monitoreo'); ?>

<div class="page-header">
    <?php if ($data->preview) : ?>
        <h3><?= Yii::t('app', 'Ver monitoreo') ?></h3>
    <?php else : ?>
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
$detallesseccion_id = array();
$contadorSecciones = 0;
$banderaSaltoComentario = true;
$valor = "";

$datanew = (new \yii\db\Query())
->select(['tbl_declinacion_motivo.id_declina_motivo', 'tbl_declinacion_motivo.nombre'])
->from(['tbl_declinacion_motivo'])
->where(['=','anulado',0])
->All();

?>

<!-- DIVS Para carga de ajax -->
<?php echo Html::tag('div', '', ['id' => 'ajax_div_problemas']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_div_llamdas']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_div_feedbacks']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_result']); ?>
<?php echo Html::tag('div', '', ['id' => 'ajax_add_escalate_form']); ?>
<?php if ($formulario) : ?>
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

        <?php
        \yii\bootstrap\Modal::begin([
            'id' => 'modalBloques', 'header' => "Advertencia", 'size' => \yii\bootstrap\Modal::SIZE_SMALL
        ]);
        echo Yii::t("app", "advertenciaBloques");
        \yii\bootstrap\Modal::end();
        ?>
        <?php if ($data->preview == true && (!isset($data->esAsesor) || !$data->esAsesor)) : ?>
            <div class="row">
                <div class="col-sm-12 well">
                    <?=
                    Html::a(
                        Yii::t('app', 'Cancel'),
                        [
                            'cancelarformulario', 'id' => $data->basesatisfaccion->id, 'tmp_form' => $data->formulario_id
                        ],
                        ['class' => 'btn btn-default soloCancelar']
                    )
                    ?>
                    <?= Html::a('Desplegar', "javascript:void(0)", ['id' => 'prueba', 'class' => 'btn btn-info soloAbrir'])
                    ?>
                </div>
            </div>
        <?php endif; ?>
        <div class="row seccion">
            <h6><?= 'Gestión de la Satisfacción' ?></h6>
        </div>
        <div id="divTablaPreguntas">
            <table id="tablaPreguntas" class="table table-striped table-bordered detail-view">
                <caption>Tabla preguntas</caption>
                <tr>
                    <th scope="col">
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if (!empty($data->basesatisfaccion->buzon)) {
                                    if ($data->basesatisfaccion->aliados == 'CLARO') {
                                        $url_buzon = $data->basesatisfaccion->buzon;
                                        echo Html::label("Identificador buzon");
                                        echo " ";
                                        echo Html::input("text", "idbuzon", $data->basesatisfaccion->buzon, array('readonly' => true, 'style' => 'width:600px'));
                                    }
                                    if ($data->basesatisfaccion->aliados == 'KNT') {
                                        $url_buzon = explode("/web/", $data->basesatisfaccion->buzon);
                                        echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                            . Yii::t("app", "Grabación buzón"), Url::to("@web/" . $url_buzon[1]), ['target' => "_blank"]);
                                    }
                                } else {
                                    echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                        . Yii::t("app", "No se encontró buzón"), $data->basesatisfaccion->buzon);
                                }
                                ?>
                            </div>
                        </div>
                    </th>
                </tr>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-12">
                                <?php
                                if (!empty($data->basesatisfaccion->llamada)) {
                                    if ($data->basesatisfaccion->aliados == 'CLARO') {
                                        echo Html::label("Identificador llamada");
                                        echo " ";
                                        echo Html::input("text", "idllamada", $data->basesatisfaccion->llamada, array('readonly' => true, 'style' => 'width:600px'));
                                    } else {
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
                                    }
                                } else {
                                    if ($data->basesatisfaccion->aliados == "GNB") {

                                        echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                                        . Yii::t("app", "Grabación Llamada - Genesys"), 'https://apps.mypurecloud.com/directory/#/engage/admin/interactions/'.$data->basesatisfaccion->connid, ['target' => "_blank"]);

                                    }else{

                                        $varGenesysCloud = $data->basesatisfaccion->tipo_encuesta;

                                        if (strlen($varGenesysCloud) > 1) {
                                            $varGC = substr($varGenesysCloud, 1);

                                            $varGeneral = $data->basesatisfaccion->connid;
                                            $varParte1 = substr($varGeneral, -32, -24);
                                            $varParte2 = substr($varGeneral, -24, -20);
                                            $varParte3 = substr($varGeneral, -20, -16);
                                            $varParte4 = substr($varGeneral, -16, -12);
                                            $varParte5 = substr($varGeneral, -12);

                                            $varConnidGenesysCloud = $varParte1 . "-" . $varParte2 . "-" . $varParte3 . "-" . $varParte4 . "-" . $varParte5;
                                            $varUrlGenesysCloud = "https://apps.usw2.pure.cloud/directory/#/engage/admin/interactions/";

                                            if ($varGC = "G") {
                                                echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                                    . Yii::t("app", "Grabación Llamada"), $varUrlGenesysCloud . $varConnidGenesysCloud, ['target' => "_blank"]);
                                            } else {
                                                echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                                    . Yii::t("app", "No se encontró llamada"), $data->basesatisfaccion->llamada);
                                            }
                                        } else {
                                            echo Html::a(Html::img(Url::to("@web/images/inicio.png"), ["width" => "30px"]) . ' '
                                                . Yii::t("app", "No se encontró llamada"), $data->basesatisfaccion->llamada);
                                        }
                                    }
                                }
                                ?>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-12">
                                <strong><em class="fas fa-envelope" style="font-size: 25px; color: #002855;"></em> <?= Yii::t('app', 'Transcripción: ') ?></strong>
                            </div>
                            <div class="col-md-12">
                                <div class="row">
                                    <div class="col-md-8">
                                        <textarea id="idtranscripcion"  style="font-size: 12px;width: -webkit-fill-available;" rows="2"><?php echo $vartexto ?></textarea>
                                    </div>

                                    <div class="col-md-4">
                                        <div onclick="enviartexto();" class="btn btn-primary" style="height: 34px;" method='post' id="botones2" >
                                        Guardar
                                    </div>
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div class="row">
                            <div class="col-md-12">
                                <strong><em class="fas fa-smile" style="font-size: 25px; color: #002855;"></em> <?= Yii::t('app', 'Valencia emocional: ') ?></strong> <?php echo $varvalencia ?>
                            </div>
                        </div>
                    </td>
                </tr>

                <?php if ($varcontenidoKaliope != 0) { ?>

                    <tr>
                        <td>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong><em class="fas fa-file" style="font-size: 25px; color: #002855;"></em> <?= Yii::t('app', 'Ingresar nueva valencia emocional: ') ?></strong>
                                </div>
                                <div class="col-md-4">
                                    <select id="idselectvalencias" name="nuevavalencia" class="js-example-basic-single form-control">
                                        <option value="">Seleccione</option>
                                        <option value="Negativo">Negativo</option>
                                        <option value="Neutro">Neutro</option>
                                        <option value="Positivo">Positivo</option>
                                    </select>
                                </div>
                                <div class="col-md-4">
                                    <div onclick="enviarvalencia();" class="btn btn-primary" style="height: 34px;" method='post' id="botones2">
                                        Guardar
                                    </div>
                                </div>
                            </div>

                        </td>
                    </tr>

                <?php } ?>

            </table>
        </div>
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
                        <?php if (isset($data->formulario->subi_calculo)) : ?>
                            <?= Html::a(Yii::t('app', 'Calcular subi'), "javascript:void(0)", ['class' => 'btn  btn-primary soloCalcular'])
                            ?>
                        <?php endif; ?>
                        <?= Html::a('Desplegar', "javascript:void(0)", ['id' => 'prueba', 'class' => 'btn btn-info soloAbrir'])
                        ?>
                        <?= Html::a(Yii::t('app', 'Cancel'), ['cancelarformulario', 'id' => $data->basesatisfaccion->id], ['class' => 'btn btn-default soloCancelar']) ?>
                        <?php if ($data->aleatorio == true) : ?>
                            <?= Html::a('Declinar', "javascript:void(0)", ['id' => 'prueba11', 'class' => 'btn  btn-primary soloMostrar', 'style' => 'display: inline' ])?>  
                            <?= Html::a('Declinar', "javascript:void(0)", ['id' => 'prueba12', 'class' => 'btn  btn-primary soloMostrar1', 'style' => 'display: none'])?>                                                                                                 
                        <?php endif; ?> 
                    
                    </div>
                </div>               
                <div class="col-sm-12 well" id="tablesi" style="display: none">
                  <div class="row"> 
                        <div class="col-md-4">
                        <select name="txtmotivo" id="txtmotivo" style="font-size: 15px">
                            <option value="">Seleccione...</option>
                            <?php  foreach ($datanew as $r) : ?>
                                <option value = '<?php echo $r['id_declina_motivo']; ?>'><?php echo $r['nombre']; ?></option>
                            <?php endforeach; ?>
                        </select>                            
                        </div>
                        <div class="col-md-4 pull-left">
                         <?= Html::a(Yii::t('app', 'Declinar'), ['declinarformulario', 'id' => $data->basesatisfaccion->id, 'motivo' => $varmotivos], ['class' => 'btn  btn-primary  soloDeclinar']) ?>
                        </div>                 
                    
                  </div>
                  
                </div>
                <br>  
            <?php endif; ?>
            <br> 
            <?= Html::input("hidden", "tmp_formulario_id", $data->formulario_id, ["id" => "tmp_formulario_id"]); ?>
            <?= Html::input("hidden", "basesatisfaccion_id", $data->basesatisfaccion->id); ?>

            <?= Html::input("hidden", "arbol_id", $data->tmp_formulario->arbol_id); ?>
            <?= Html::input("hidden", "dimension_id", $data->tmp_formulario->dimension_id); ?>
            <?= Html::input("hidden", "ruta_arbol", $data->ruta_arbol); ?>
            <?= Html::input("hidden", "form_equipo_id", (isset($data->equipo_id)) ? $data->equipo_id : ''); ?>
            <?= Html::input("hidden", "form_lider_id", (isset($data->usua_id_lider)) ? $data->usua_id_lider : ''); ?>
            <? date_default_timezone_set('America/Bogota'); ?>
            <?= Html::input("hidden", "hora_modificacion", date("Y-m-d H:i:s")); ?>

            <!-- CAMPO OCULTO PARA EVITAR SUBMIT NO CONTROLADO -->
            <?= Html::input("hidden", "submitcorrecto", "NO", ["id" => "submitcorrecto"]); ?>
            <?= (isset($view)) ? Html::input("hidden", "view", $view) : ""; ?>
            <?php
            $cont = 0;
            $detalle = new stdClass();

            // do {
            //     $detalle = $data->detalles[$cont];
            if ($data->basesatisfaccion->aliados == 'CLARO') { //DLLO GERENCIA IVR BANCO
            ?>
                <?php if (($contadorSecciones == $banderaDatogenerales || $contadorSecciones == $banderaGuiaInspiracion)) : ?>




                    <!-- Informacion de partida-->
                    <?php if ($contadorSecciones == $banderaDatogenerales) : ?>
                        <?php
                        if ($contadorSecciones == 0) {
                            echo $varRow;
                        }
                        ?>
                        <?php
                        $contadorSecciones++;
                        ?>
                        <?php echo $varDatogenerales ?>
                        <div class="row seccion-informacion" class="col-md-12">
                            <div class="col-md-10">
                                <label class="labelseccion ">
                                    <?= 'Información de Partida' ?>
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
                                    ["class" => "openSeccion", "id" => "labelPartida"]
                                )
                                ?>
                            </div>
                            <?php $this->registerJs('$("#labelPartida").click(function () {
                                $("#datosPartida").toggle("slow");
                            });'); ?>
                        </div>
                        <div id="datosPartida" class="col-sm-12" style="display: none">

                            <table class="table table-striped table-bordered detail-view formDinamico">
                                <caption>Tabla partida</caption>
                                <tbody>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "ANI"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->ani ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Identificación"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->identificacion ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Nombre"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->nombre ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Ext"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->ext ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Tipo Servicio"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->tipo_servicio ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Tipo encuesta"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->tipo_encuesta ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "RN"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->rn ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Agente"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->agente ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Aliado"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->aliados ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Tipología"); ?></th>
                                        <td><?php
                                            echo Html::dropDownList(
                                                "categoria",
                                                $data->basesatisfaccion->tipologia,
                                                $data->recategorizar,
                                                ["id" => "categoria", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]
                                            );
                                            ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo Yii::t("app", "Connid"); ?></th>
                                        <td><?php echo $data->basesatisfaccion->connid; ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->preguntas['0']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta1 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->preguntas['1']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta2 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->preguntas['2']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta3 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->preguntas['3']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta4 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->preguntas['4']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta5 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->preguntas['5']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta6 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->preguntas['6']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta7 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->preguntas['7']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta8 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->preguntas['8']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta9 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col" ><?php echo (strtoupper($data->preguntas['9']['nombre']) != 'NO APLICA') ? $data->preguntas['9']['enunciado_pre'] : 'NO APLICA' ?></th>
                                        <td><?php echo (strtoupper($data->preguntas['9']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta10 : 'NO APLICA' ?></td>
                                    </tr>
                                    <tr>
                                        <th scope="col"><?php echo Yii::t("app", "Interaccion"); ?></th>
                                        <td>
                                            <?php if ($data->preview) : ?>
                                                <?=
                                                Html::dropDownList(
                                                    "transacion_id",
                                                    $data->tmp_formulario->transacion_id,
                                                    $data->transacciones,
                                                    ["class" => "form-control", "disabled" => "disabled"]
                                                );
                                                ?>
                                            <?php else : ?>
                                                <?=
                                                Html::dropDownList(
                                                    "transacion_id",
                                                    $data->tmp_formulario->transacion_id,
                                                    $data->transacciones,
                                                    ["class" => "form-control"]
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
                                            <th scope="col"><?php echo Yii::t("app", "Enalces Interaccion"); ?></th>
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
                        <?php
                        echo $varFin;
                        ?>
                        <?php
                        if (($contadorSecciones % $cantDivs) == 0) {
                            echo $varFin;
                        }
                        ?>
                    <?php endif; ?>
                    <?php if ($contadorSecciones == $banderaGuiaInspiracion) : ?>
                        <?php
                        if (!empty($prev_seccion) && $banderaSaltoComentario) :
                            $banderaSaltoComentario = false;
                        ?>

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
            <?php
                            $prev_bloque_descripcion = $prev_seccion = $prev_bloque = '';
                            $prev_id_bloque = 0;
                            echo $varFin
            ?>
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
        ?>

        <!-- FIN Informacion de partida-->
        <?php echo $varGuiainspiracion ?>
        <div class="row seccion-data" class="col-md-12">
            <div class="col-md-10">
                <label class="labelseccion ">
                    <?= 'Gestión de la Satisfacción' ?>
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
                <tbody <tr>
                    <th scope="col" ><?php echo Yii::t("app", "Lider Equipo"); ?></th>
                    <td><?php echo $data->basesatisfaccion->lider_equipo ?></td>
                    </tr>
                    <tr>
                        <th scope="col" ><?php echo Yii::t("app", "Programa/PCRC"); ?></th>
                        <td><?php echo $data->basesatisfaccion->pcrc0->name ?></td>
                    </tr>
                    <tr>
                        <th scope="col" ><?php echo Yii::t("app", "Cliente"); ?></th>
                        <td><?php echo $data->basesatisfaccion->cliente0->name ?></td>
                    </tr>
                    <tr>
                        <th scope="col" ><?php echo Yii::t("app", "Evaluado ID"); ?></th>
                        <td><?php echo (isset($data->evaluado)) ? $data->evaluado : ''; ?></td>
                    </tr>
                    <tr>
                        <th scope="col" ><?php echo Yii::t("app", "Fecha Inicio Valoracion"); ?></th>
                        <td><?php echo $data->tmp_formulario->hora_inicial; ?></td>
                    </tr>
                    <?php if (isset($data->fecha_final)) { ?>
                        <tr>
                            <th scope="col" ><?php echo Yii::t("app", "Fecha Fin Valoracion"); ?></th>
                            <td><?php echo $data->fecha_final ?></td>
                        </tr>
                    <?php } ?>
                    <?php if (isset($data->minutes)) { ?>
                        <tr>
                            <th scope="col" ><?php echo Yii::t("app", "Tiempo de Valoracion"); ?></th>
                            <td><?php echo $data->minutes ?></td>
                        </tr>
                    <?php } ?>
                    <tr>
                        <th scope="col" ><?php echo Yii::t("app", "Cantidad de Modificaciones"); ?></th>
                        <td><?php echo (isset($data->tmp_formulario->cant_modificaciones)) ? $data->tmp_formulario->cant_modificaciones : ''; ?></td>
                    </tr>
                    <?php if (isset($data->tmp_formulario->tiempo_modificaciones)) { ?>
                        <tr>
                            <th scope="col" ><?php echo Yii::t("app", "Tiempo total Modificaciones"); ?></th>
                            <td><?php echo $data->tmp_formulario->tiempo_modificaciones ?></td>
                        </tr>
                    <?php } ?>

                    <tr>
                        <th scope="col" ><?php echo Yii::t("app", "Dimension"); ?></th>
                        <td><?php
                            echo Html::dropDownList(
                                "dimension",
                                $data->tmp_formulario->dimension_id,
                                $data->dimension,
                                ["id" => "dimension", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]
                            );
                            ?></td>
                    </tr>
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "Fuente"); ?></th>
                        <td>
                            <?php if ($data->preview) : ?>
                                <?=
                                Html::input(
                                    "text",
                                    "fuente",
                                    $data->tmp_formulario->dsfuente_encuesta,
                                    [
                                        "id" => "fuente",
                                        "class" => "form-control",
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
                                    $data->basesatisfaccion->buzon,
                                    [
                                        "id" => "fuente",
                                        "class" => "form-control",
                                        "placeholder" => Yii::t("app", "Ingrese la fuente")
                                    ]
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
                            <th scope="col"><?php echo Yii::t("app", "Enalces Interaccion"); ?></th>
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
                            <th scope="col"><?php echo Yii::t("app", "subi_calculo"); ?></th>
                            <td>
                                <?php echo implode(',', $data->indices_calcular) ?>
                            </td>
                        </tr>
                        <?php
                            if (count($data->indices_calcular) < 5 && !$data->preview) :
                        ?>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "agregar subi"); ?></th>
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
                                        <th scope="col"><?php echo $value ?></th>
                                        <td><?php echo ($data->tmp_formulario->attributes['score'] * 100) . '%' ?></td>
                                    </tr>
                                <?php else : ?>
                                    <tr>
                                        <th scope="col"><?php echo $value ?></th>
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

        </div>



        <?php echo $varFin; ?>
        <?php
                        if (($contadorSecciones % $cantDivs) == 0) {
                            echo $varFin;
                        }
        ?>
    <?php endif; ?>





<?php else :
?>




    <?php $detalles_ids[] = $detalle->id ?>
    <?php $detallesseccion_id[] = [$detalle->id, $detalle->seccion_id, $detalle->isPits] ?>
    <?php if ($prev_seccion != $detalle->seccion_id) : ?>
        <?php if (!empty($prev_seccion) && $banderaSaltoComentario) : ?>

            <div class="form-group row" <?php
                                        if ($prev_sndesplegar_comentario == 0) {
                                            echo 'style="display: none"';
                                        }
                                        ?>>

                <?php
                            if ($varPcrc == 2774) {
                ?>

                    <div class="col-sm-10" id="txt_comentarios<?php echo $prev_seccion ?>">

                        <?php
                                if ($prev_seccion == 21387) {
                        ?>
                            <?php if ($data->preview) : ?>
                                <?=
                                        Html::textarea(
                                            "comentarioSeccion[" . $prev_seccion . "]",
                                            $prev_secccion_comentario,
                                            [
                                                "id" => "txt_comentarios_2",
                                                "style" => "margin: 0px -5.5px 0px 0px; height: 140px; width: 1000px;",
                                                "class" => "form-control droplabel",
                                                "placeholder" => "1. Detalle los comportamientos positivos que se indentifican en el embajador de Marca y aquellos interesados que aportaron en la gestion. \n\n\n\n\n2. Oportunidad de Mejora que impacten la Promesa de Relacion.",
                                                "readonly" => "readonly",
                                                "onclick" => "myFunction();"
                                            ]
                                        );
                                ?>
                            <?php else : ?>
                                <?=
                                        //Aqui es codigo            
                                        Html::textarea(
                                            "comentarioSeccion[" . $prev_seccion . "]",
                                            $prev_secccion_comentario,
                                            [
                                                "id" => "txt_comentarios_2",
                                                "style" => "margin: 0px -5.5px 0px 0px; height: 140px; width: 1000px;",
                                                "class" => "form-control droplabel",
                                                "placeholder" => "1. Detalle los comportamientos positivos que se indentifican en el embajador de Marca y aquellos interesados que aportaron en la gestion: \n\n\n\n\n2. Oportunidad de Mejora que impacten la Promesa de Relacion:",
                                                "data-toggle" => "tooltip",
                                                "data-original-title" => "1. Detalle los comportamientos positivos que se indentifican en el embajador de Marca y aquellos interesados que aportaron en la gestion. \n\n\n\n\n2. Oportunidad de Mejora que impacten la Promesa de Relacion.",
                                                "onclick" => "myFunction();"
                                            ]
                                        );
                                ?>
                            <?php endif; ?>
                        <?php
                                } else {
                        ?>
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
                                        //Aqui es codigo            
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
                        <?php
                                }
                        ?>

                    </div>

                <?php
                            } else {
                ?>

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
                                    //Aqui es codigo            
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
                <?php
                            }
                ?>


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
    <?php if ($detalle->isPits == 1) : ?>
        <div class="form-group col-sm-12">
            <table <?php echo "id='tablapits" . $detalle->seccion_id . "'" ?> class="table table-striped table-bordered detail-view">
                <caption>Tabla</caption>
                <tbody>
                    <th scope="col">
                    </th>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
<?php endif; ?>
<div class="form-group" id="detalle_<?php echo $detalle->id ?>">
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
        <?php
                    $decisionSiConTipi .= "$('#detalle_" . $detalle->id . "').hide('slow');
                                    $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',true);"
                        . "$('#tipificacion_" . $detalle->id . "').hide('slow')";
                    $decisionNoConTipi .= "$('#detalle_" . $detalle->id . "').show('slow');
                                    $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',false);";
                    $decisionSi .= "$('#detalle_" . $detalle->id . "').hide('slow');
                                    $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',true);";
                    $decisionNo .= "$('#detalle_" . $detalle->id . "').show('slow');
                                    $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',false);";
        ?>
        <div class="col-sm-4">
            <?php if ($data->fill_values == true) : ?>
                <?php echo isset($data->calificaciones[$detalle->calificacion_id][$detalle->calificaciondetalle_id]) ? $data->calificaciones[$detalle->calificacion_id][$detalle->calificaciondetalle_id]["name"] : '' ?>
            <?php else : ?>
                <select name="calificaciones[<?php echo $detalle->id ?>]" class="form-control toggleTipificacion" data-id-detalle="<?php echo $detalle->id ?>" id="calificacion_<?php echo $detalle->id ?>">
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
<?php
                    $prev_secccion_comentario = trim($detalle->dscomentario);
                    $cont++;
                    $banderaSaltoComentario = true;
?>
<?php endif; ?>
<?php }
            while ($cont < count($data->detalles)) { //DLLO GERENCIA IVR BANCO
                $detalle = $data->detalles[$cont];
?>

    <?php if (($contadorSecciones == $banderaDatogenerales || $contadorSecciones == $banderaGuiaInspiracion) && ($prev_seccion !=           $detalle->seccion_id)) : ?>

        <!-- Informacion de partida-->
        <?php if ($contadorSecciones == $banderaDatogenerales) : ?>
            <?php
                        if ($contadorSecciones == 0) {
                            echo $varRow;
                        }
            ?>
            <?php
                        $contadorSecciones++;
            ?>
            <?php echo $varDatogenerales ?>
            <div class="row seccion-informacion" class="col-md-12">
                <div class="col-md-10">
                    <label class="labelseccion ">
                        <?= 'Información de Partida' ?>
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
                            ["class" => "openSeccion", "id" => "labelPartida"]
                        )
                    ?>
                </div>
                <?php $this->registerJs('$("#labelPartida").click(function () {
                                $("#datosPartida").toggle("slow");
                            });'); ?>
            </div>
            <div id="datosPartida" class="col-sm-12" style="display: none">

                <table class="table table-striped table-bordered detail-view formDinamico">
                    <caption>Tabla partida</caption>
                    <tbody> 
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "ANI"); ?></th>
                            <td><?php echo $data->basesatisfaccion->ani ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Identificación"); ?></th>
                            <td><?php echo $data->basesatisfaccion->identificacion ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Nombre"); ?></th>
                            <td><?php echo $data->basesatisfaccion->nombre ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Ext"); ?></th>
                            <td><?php echo $data->basesatisfaccion->ext ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Tipo Servicio"); ?></th>
                            <td><?php echo $data->basesatisfaccion->tipo_servicio ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Tipo encuesta"); ?></th>
                            <td><?php echo $data->basesatisfaccion->tipo_encuesta ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "RN"); ?></th>
                            <td><?php echo $data->basesatisfaccion->rn ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Agente"); ?></th>
                            <td><?php echo $data->basesatisfaccion->agente ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Tipología"); ?></th>
                            <td><?php
                                echo Html::dropDownList(
                                    "categoria",
                                    $data->basesatisfaccion->tipologia,
                                    $data->recategorizar,
                                    ["id" => "categoria", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]
                                );
                                ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Connid"); ?></th>
                            <td><?php echo $data->basesatisfaccion->connid; ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->preguntas['0']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta1 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->preguntas['1']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta2 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->preguntas['2']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta3 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->preguntas['3']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta4 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->preguntas['4']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta5 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->preguntas['5']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta6 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->preguntas['6']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta7 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->preguntas['7']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta8 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->preguntas['8']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta9 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo (strtoupper($data->preguntas['9']['nombre']) != 'NO APLICA') ? $data->preguntas['9']['enunciado_pre'] : 'NO APLICA' ?></th>
                            <td><?php echo (strtoupper($data->preguntas['9']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta10 : 'NO APLICA' ?></td>
                        </tr>
                        <tr>
                            <th scope="col"><?php echo Yii::t("app", "Interaccion"); ?></th>
                            <td>
                                <?php if ($data->preview) : ?>
                                    <?=
                                    Html::dropDownList(
                                        "transacion_id",
                                        $data->tmp_formulario->transacion_id,
                                        $data->transacciones,
                                        ["class" => "form-control", "disabled" => "disabled"]
                                    );
                                    ?>
                                <?php else : ?>
                                    <?=
                                    Html::dropDownList(
                                        "transacion_id",
                                        $data->tmp_formulario->transacion_id,
                                        $data->transacciones,
                                        ["class" => "form-control"]
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
                                <th scope="col"><?php echo Yii::t("app", "Enalces Interaccion"); ?></th>
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
            <?php
                        echo $varFin;
            ?>
            <?php
                        if (($contadorSecciones % $cantDivs) == 0) {
                            echo $varFin;
                        }
            ?>
        <?php endif; ?>
        <?php if ($contadorSecciones == $banderaGuiaInspiracion) : ?>
            <?php
                        if (!empty($prev_seccion) && $banderaSaltoComentario) : $banderaSaltoComentario = false; ?>

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
<?php
                            $prev_bloque_descripcion = $prev_seccion = $prev_bloque = '';
                            $prev_id_bloque = 0;
                            echo $varFin
?>
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
?>

<!-- FIN Informacion de partida-->
<?php echo $varGuiainspiracion ?>
<div class="row seccion-data" class="col-md-12">
    <div class="col-md-10">
        <label class="labelseccion ">
            <?= 'Gestión de la Satisfacción' ?>
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
            <th scope="col"><?php echo Yii::t("app", "Lider Equipo"); ?></th>
            <td><?php echo $data->basesatisfaccion->lider_equipo ?></td>
            </tr>
            <tr>
                <th scope="col"><?php echo Yii::t("app", "Programa/PCRC"); ?></th>
                <td><?php echo $data->basesatisfaccion->pcrc0->name ?></td>
            </tr>
            <tr>
                <th scope="col"><?php echo Yii::t("app", "Cliente"); ?></th>
                <td><?php echo $data->basesatisfaccion->cliente0->name ?></td>
            </tr>
            <tr>
                <th scope="col"><?php echo Yii::t("app", "Evaluado ID"); ?></th>
                <td><?php echo (isset($data->evaluado)) ? $data->evaluado : ''; ?></td>
            </tr>
            <tr>
                <th scope="col"><?php echo Yii::t("app", "Fecha Inicio Valoracion"); ?></th>
                <td><?php echo $data->tmp_formulario->hora_inicial; ?></td>
            </tr>
            <?php if (isset($data->fecha_final)) { ?>
                <tr>
                    <th scope="col"><?php echo Yii::t("app", "Fecha Fin Valoracion"); ?></th>
                    <td><?php echo $data->fecha_final ?></td>
                </tr>
            <?php } ?>
            <?php if (isset($data->minutes)) { ?>
                <tr>
                    <th scope="col"><?php echo Yii::t("app", "Tiempo de Valoracion"); ?></th>
                    <td><?php echo $data->minutes ?></td>
                </tr>
            <?php } ?>
            <tr>
                <th scope="col"><?php echo Yii::t("app", "Cantidad de Modificaciones"); ?></th>
                <td><?php echo (isset($data->tmp_formulario->cant_modificaciones)) ? $data->tmp_formulario->cant_modificaciones : ''; ?></td>
            </tr>
            <?php if (isset($data->tmp_formulario->tiempo_modificaciones)) { ?>
                <tr>
                    <th scope="col"><?php echo Yii::t("app", "Tiempo total Modificaciones"); ?></th>
                    <td><?php echo $data->tmp_formulario->tiempo_modificaciones ?></td>
                </tr>
            <?php } ?>

            <tr>
                <th scope="col"><?php echo Yii::t("app", "Dimension"); ?></th>
                <td><?php
                        echo Html::dropDownList(
                            "dimension",
                            $data->tmp_formulario->dimension_id,
                            $data->dimension,
                            ["id" => "dimension", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]
                        );
                    ?></td>
            </tr>
            <tr>
                <th scope="col"><?php echo Yii::t("app", "Fuente"); ?></th>
                <td>
                    <?php if ($data->preview) : ?>
                        <?=
                            Html::input(
                                "text",
                                "fuente",
                                $data->tmp_formulario->dsfuente_encuesta,
                                [
                                    "id" => "fuente",
                                    "class" => "form-control",
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
                                    "class" => "form-control",
                                    "placeholder" => Yii::t("app", "Ingrese la fuente")
                                ]
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
                    <th scope="col"><?php echo Yii::t("app", "Enalces Interaccion"); ?></th>
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
                    <th scope="col"><?php echo Yii::t("app", "subi_calculo"); ?></th>
                    <td>
                        <?php echo implode(',', $data->indices_calcular) ?>
                    </td>
                </tr>
                <?php
                            if (count($data->indices_calcular) < 5 && !$data->preview) :
                ?>
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "agregar subi"); ?></th>
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
                                <th scope="col"><?php echo $value ?></th>
                                <td><?php echo ($data->tmp_formulario->attributes['score'] * 100) . '%' ?></td>
                            </tr>
                        <?php else : ?>
                            <tr>
                                <th scope="col"><?php echo $value ?></th>
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
            <tr>
                <th><?php echo Yii::t("app", "Centro de Costos"); ?></th>
                <td>
                    <div style="width:100%">
                        <?php 
                            $form = ActiveForm::begin([
                                'layout' => 'horizontal'
                            ]); 
                        ?>

                        <?=  $form->field($model, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->select([
                                            'cod_pcrc',
                                            'CONCAT(cod_pcrc," - ",pcrc) AS pcrc'
                                            ])->where(['=','estado',1])->andwhere(['=','anulado',0])->andwhere(['=','id_dp_clientes',$varIdClientes])->groupby(['cod_pcrc'])->orderBy(['pcrc'=> SORT_ASC])->all(), 'cod_pcrc', 'pcrc'),
                                                    [
                                                        'id' => 'idVarPcrc',
                                                        'prompt'=>'Seleccionar centro de costos...',     
                                                        'style' => 'width:400px', 
                                                        'name' => 'idVarPcrc'
                                                    ]
                                            )->label(''); 
                        ?>

                        <?= $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idVarClientes','class'=>'hidden','value'=>$varIdClientes,'name' => 'idVarClientes'])?>

                        <?php
                            ActiveForm::end();
                        ?>
                    </div>
                </td>
            </tr>


        </tbody>
    </table>

</div>
<?php echo $varFin; ?>
<?php
                        if (($contadorSecciones % $cantDivs) == 0) {
                            echo $varFin;
                        }
?>
<?php endif; ?>

<?php if ($varPcrc == '3104') { ?>
    <div class="col-sm-12">
        <div class="row seccion">
            <div class="col-md-10">
                <label class="labelseccion">
                    <?php echo Yii::t('app', 'Información Inicial') ?>
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
                            ["class" => "openSeccion", "id" => "generalreason"]
                        )
                ?>
                <?php $this->registerJs('$("#generalreason").click(function () {
                                    $("#datosGeneralreason").toggle("slow");
                                });'); ?>
            </div>
        </div>
        <div id="datosGeneralreason" style="display: none;">
            <div id="divTablaPreguntas">
                <table id="tablaPreguntas" class="table table-striped table-bordered detail-view">
                    <caption>Tabla preguntas</caption>
                    <tr>
                        <th scope="col">Motivo de contacto</th>
                        <td><?php
                            $varReason3 = Yii::$app->db->createCommand("select distinct reason3 from tbl_base_Avon where id = $varBase and arbol_id = $varPcrc")->queryScalar();
                            echo $varReason3;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Etapa del Viaje</th>
                        <td><?php
                            $varReason4 = Yii::$app->db->createCommand("select distinct reason4 from tbl_base_Avon where id = $varBase and arbol_id = $varPcrc")->queryScalar();
                            echo $varReason4;
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th scope="col">ID Encuesta AVON</th>
                        <td><?php
                            $varIdnps = Yii::$app->db->createCommand("select distinct idnps from tbl_base_Avon where id = $varBase and arbol_id = $varPcrc")->queryScalar();
                            echo $varIdnps;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Email usuario</th>
                        <td><?php
                            $varEmail = Yii::$app->db->createCommand("select distinct email from tbl_base_Avon where id = $varBase and arbol_id = $varPcrc")->queryScalar();
                            echo $varEmail;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Usuario COSV</th>
                        <td><?php
                            $varnmusuario = Yii::$app->db->createCommand("select distinct nmusuario from tbl_base_Avon where id = $varBase and arbol_id = $varPcrc")->queryScalar();
                            echo $varnmusuario;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Servicio Konecta</th>
                        <td><?php
                            $varuserseg = Yii::$app->db->createCommand("select distinct user_seg from tbl_base_Avon where id = $varBase and arbol_id = $varPcrc")->queryScalar();
                            echo $varuserseg;
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th scope="col">Nota anterior NPS</th>
                        <td><?php
                            $varnotaanterior = Yii::$app->db->createCommand("select distinct nota_anterior from tbl_base_Avon where id = $varBase and arbol_id = $varPcrc")->queryScalar();
                            echo $varnotaanterior;
                            ?>
                        </td>
                    </tr>

                    <tr>
                        <th scope="col">Buzon de usuario</th>
                        <td><?php
                            echo $data->basesatisfaccion->comentario;
                            ?>
                        </td>
                    </tr>
                    <tr style="display: none;">
                        <th scope="col">Formularios Avon</th>
                        <td>
                            <?php
                            $varIdForm = Yii::$app->db->createCommand("select distinct formulario from tbl_base_Avon where id = $varBase and arbol_id = $varPcrc")->queryScalar();

                            if ($varIdForm != null) {
                                $varNameTree = Yii::$app->db->createCommand("select distinct name from tbl_arbols where id = $varIdForm")->queryScalar();
                                echo $varNameTree;
                            } else {
                            ?>
                                <select class='form-control' id="txtVariable" data-toggle="tooltip" title="Formulario">
                                    <option value="" disabled selected>Seleccionar...</option>
                                    <?php
                                    $dataLista =  Yii::$app->db->createCommand("select * from tbl_arbols where id in (1938,3032,2988,2989,2982,2987,1943)")->queryAll();

                                    foreach ($dataLista as $key => $value) {
                                        echo "<option value = '" . $value['id'] . "'>" . $value['name'] . "</option>";
                                    }
                                    ?>
                                </select>
                            <?php } ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php } ?>

<?php else :
?>
    <?php $detalles_ids[] = $detalle->id ?>
    <?php $detallesseccion_id[] = [$detalle->id, $detalle->seccion_id, $detalle->isPits] ?>
    <?php if ($prev_seccion != $detalle->seccion_id) : ?>
        <?php if (!empty($prev_seccion) && $banderaSaltoComentario) : ?>

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
            <?php if ($detalle->isPits == 1) : ?>
                <div class="form-group col-sm-12">
                    <table <?php echo "id='tablapits" . $detalle->seccion_id . "'" ?> class="table table-striped table-bordered detail-view">
                        <caption>Tabla</caption>
                        <tbody>
                            <th scope="col"></th>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        <?php endif; ?>
        <div class="form-group" id="detalle_<?php echo $detalle->id ?>">
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
                <?php
                    $decisionSiConTipi .= "$('#detalle_" . $detalle->id . "').hide('slow');
                                        $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',true);"
                        . "$('#tipificacion_" . $detalle->id . "').hide('slow')";
                    $decisionNoConTipi .= "$('#detalle_" . $detalle->id . "').show('slow');
                                        $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',false);";
                    $decisionSi .= "$('#detalle_" . $detalle->id . "').hide('slow');
                                        $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',true);";
                    $decisionNo .= "$('#detalle_" . $detalle->id . "').show('slow');
                                        $('#detalle_" . $detalle->id . "').children().children().find('select').attr('disabled',false);";
                ?>
                <div class="col-sm-4">
                    <?php if ($data->fill_values == true) : ?>
                        <?php echo isset($data->calificaciones[$detalle->calificacion_id][$detalle->calificaciondetalle_id]) ? $data->calificaciones[$detalle->calificacion_id][$detalle->calificaciondetalle_id]["name"] : '' ?>
                    <?php else : ?>
                        <select name="calificaciones[<?php echo $detalle->id ?>]" class="form-control toggleTipificacion" data-id-detalle="<?php echo $detalle->id ?>" id="calificacion_<?php echo $detalle->id ?>">
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
        <?php
                    $prev_secccion_comentario = trim($detalle->dscomentario);
                    $cont++;
                    $banderaSaltoComentario = true;
        ?>
    <?php endif; ?>
<?php } ?>

<?php if (!empty($prev_seccion)) : ?>
    <div class="form-group" <?php
                            if ($prev_sndesplegar_comentario == 0) {
                                echo 'style="display: none"';
                            }
                            ?>>

        <?php
        if ($varPcrc == 2774) {
        ?>

            <div class="col-sm-10" id="txt_comentarios<?php echo $prev_seccion ?>">

                <?php
                if ($prev_seccion == 21388) {
                ?>

                    <?php if ($data->fill_values == true) : ?>
                        <span style="color: #ff0000;"><?php echo $prev_secccion_comentario ?></span>
                    <?php else : ?>
                        <?php if ($data->preview) : ?>
                            <?=
                            Html::textarea(
                                "comentarioSeccion[" . $prev_seccion . "]",
                                $prev_secccion_comentario,
                                [
                                    "id" => "txt_comentarios_1",
                                    "style" => "margin: 0px -5.5px 0px 0px; height: 170px; width: 1000px;",
                                    "class" => "form-control droplabel",
                                    "placeholder" => "1. Oportunidad de Mejora que impacten la Promesa de Solucion del Embajador \n\n\n\n\n2. Oportunidad de Mejora que impacten la Promesa de Solucion del Canal \n\n\n\n\n3. Oportunidad de Mejora que impacten los Productos \n\n\n\n\n4. Oportunidad de Mejora que impacten los Procedimientos/Politicas \n\n\n\n\n5. Contiene la percepcion del usuario en los siguientes aspectos: \n5.1. Segmento de Cliente\n5.2. Que piensa, siente?\n5.3. Que oye?\n5.4. Que ve?\n5.5. Que dice y hace?",
                                    "readonly" => "readonly",
                                    "onclick" => "myFunction2();"
                                ]
                            );
                            ?>
                        <?php else : ?>
                            <?=
                            //Aqui va codigo                
                            Html::textarea(
                                "comentarioSeccion[" . $prev_seccion . "]",
                                $prev_secccion_comentario,
                                [
                                    "id" => "txt_comentarios_1",
                                    "style" => "margin: 0px -5.5px 0px 0px; height: 170px; width: 1000px;",
                                    "class" => "form-control droplabel",
                                    "placeholder" => "1. Oportunidad de Mejora que impacten la Promesa de Solucion del Embajador \n\n\n\n\n2. Oportunidad de Mejora que impacten la Promesa de Solucion del Canal \n\n\n\n\n3. Oportunidad de Mejora que impacten los Productos \n\n\n\n\n4. Oportunidad de Mejora que impacten los Procedimientos/Politicas \n\n\n\n\n5. Contiene la percepcion del usuario en los siguientes aspectos: \n5.1. Segmento de Cliente\n5.2. Que piensa, siente?\n5.3. Que oye?\n5.4. Que ve?\n5.5. Que dice y hace?",
                                    "data-toggle" => "tooltip",
                                    "data-original-title" => "1. Oportunidad de Mejora que impacten la Promesa de Solucion del Embajador \n\n\n\n\n2. Oportunidad de Mejora que impacten la Promesa de Solucion del Canal \n\n\n\n\n3. Oportunidad de Mejora que impacten los Productos \n\n\n\n\n4. Oportunidad de Mejora que impacten los Procedimientos/Politicas \n\n\n\n\n5. Contiene la percepcion del usuario en los siguientes aspectos: \n5.1. Segmento de Cliente\n5.2. Que piensa, siente?\n5.3. Que oye?\n5.4. Que ve?\n5.5. Que dice y hace?",
                                    "onclick" => "myFunction2();"
                                ]
                            );
                            ?>
                        <?php endif; ?>

                    <?php endif; ?>

                <?php
                } else {
                ?>

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
                            //Aqui va codigo                
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

                <?php
                }
                ?>


            </div>

        <?php
        } else {
        ?>

            <div class="col-sm-10" id="txt_comentarios<?php echo $prev_seccion ?>">
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
                        //Aqui va codigo                
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

        <?php
        }
        ?>


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

        <!-- SECCION RESPONSABILIDAD SPC -->
        <?php if (!empty($data->responsabilidadspc)) : ?>
            <?php 

                $varIdModels = $data->basesatisfaccion->id;

                $varResponsabilidad =  (new \yii\db\Query())
                                    ->select(['responsabilidad'])
                                    ->from(['tbl_responsabilidad_satisfaccion'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','basesatisfaccion_id',$varIdModels])
                                    ->scalar();

                $varCanal =  (new \yii\db\Query())
                                    ->select(['canal'])
                                    ->from(['tbl_responsabilidad_satisfaccion'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','basesatisfaccion_id',$varIdModels])
                                    ->scalar();

                $varMarca =  (new \yii\db\Query())
                                    ->select(['marca'])
                                    ->from(['tbl_responsabilidad_satisfaccion'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','basesatisfaccion_id',$varIdModels])
                                    ->scalar();

                $varEquivocacion =  (new \yii\db\Query())
                                    ->select(['equicovacion'])
                                    ->from(['tbl_responsabilidad_satisfaccion'])            
                                    ->where(['=','anulado',0])
                                    ->andwhere(['=','basesatisfaccion_id',$varIdModels])
                                    ->scalar();

            ?>
        <div class="row seccion">
            <div class="col-md-10">
                <label class="labelseccion">
                    <?php echo Yii::t("app", "RESPONSABILIDADES SPC"); ?>
                </label>
            </div>
            <div class="col-md-2">
                <?=
                Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                            "class" => "glyphicon glyphicon-chevron-downForm",
                        ]) . "", "javascript:void(0)"
                        , ["class" => "openSeccion", "id" => "generalresponsabilidadspc"])
                ?>
                <?php $this->registerJs('$("#generalresponsabilidadspc").click(function () {
                                $("#datosresponsabilidadspc").toggle("slow");
                            });'); ?>
            </div>
        </div>
        <div id="datosresponsabilidadspc" style="display: none;">
            <div class="row well">
                <?php echo Yii::t("app", "Proceso de la mejora"); ?>
                <?php
                echo Html::tag('span', Html::img(Url::to("@web/images/Question.png")), [
                    'data-title' => Yii::t("app", "Bloques Detalles"),
                    'data-content' => Yii::t("app", "Proceso de la mejora"),
                    'data-toggle' => 'popover',
                    'style' => 'cursor:pointer;'
                ]);
                ?>                        
            </div>
            <div class="form-group">
                <div class="control-group">
                    <label class="control-label col-sm-3">
                        <?php echo Yii::t("app", "Seleccione la responsabilidad SPC"); ?>                        
                    </label>
                    <div class="col-sm-9">
                        <?php
                        echo Html::dropDownList("responsabilidadspc"
                                , $varResponsabilidad
                                , [
                                    'CANAL' => 'CANAL',
                                    'EQUIVOCADA' => 'EQUIVOCADA',
                                    'MARCA' => 'MARCA',
                                    'NA' => 'N/A'                           
                                ], 
                                [
                                    "id" => "responsabilidadspc",
                                    "class" => "form-control",
                                    'prompt' => 'Seleccione ...',
                                    "disabled" => ($data->preview) ? true : false
                                ]);
                        ?>                      
                    </div>
                </div>
            </div>

            <div class="form-group" id="divcanalspc" style="display: none">
                <div class="control-group">
                    <label class="control-label col-sm-3">
                        <?php echo Yii::t("app", "Canal"); ?>                        
                    </label>
                    <div class="col-sm-9">
                        <?php
                        echo Html::checkboxList(
                                'canalspc[]'
                                , explode(", ", $varCanal)
                                , (isset($data->responsabilidadspc['CANAL'])) ? $data->responsabilidadspc['CANAL'] : []
                                , [
                            'id' => 'canalspc'
                            , 'disabled' => ($data->preview) ? true : false
                            , 'separator' => '<br />'
                                ]
                        );
                        ?>                     
                    </div>
                </div>
            </div>

            <div class="form-group" id="divmarcaspc"  style="display: none">
                <div class="control-group">
                    <label class="control-label col-sm-3">
                        <?php echo Yii::t("app", "Marca"); ?>                          
                    </label>
                    <div class="col-sm-9">
                        <?php
                        echo Html::checkboxList(
                                'marcaspc[]'
                                , explode(", ", $varMarca)
                                , (isset($data->responsabilidadspc['MARCA'])) ? $data->responsabilidadspc['MARCA'] : []
                                , [
                            'id' => 'marcaspc'
                            , 'disabled' => ($data->preview) ? true : false
                            , 'separator' => '<br />'
                                ]
                        );
                        ?>
                    </div>
                </div>
            </div>

            <div class="form-group" id="divequivocacionspc" style="display: none">
                <div class="control-group">
                    <label class="control-label col-sm-3">
                        <?php echo Yii::t("app", "Equivocacion"); ?>                        
                    </label>
                    <div class="col-sm-9">
                        <?php
                        echo Html::checkboxList(
                                'equivocacionspc[]'
                                , explode(", ", $varEquivocacion)
                                , (isset($data->responsabilidadspc['EQUIVOCACION'])) ? $data->responsabilidadspc['EQUIVOCACION'] : []
                                , [
                            'id' => 'equivocacionspc'
                            , 'disabled' => ($data->preview) ? true : false
                            , 'separator' => '<br />'
                                ]
                        );
                        ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($data->responsabilidad)) : ?>
            <!-- SECCION PROTECCIÓN DE LA EXPERIENCIA -->
            <div class="row seccion">
                <div class="col-md-10">
                    <label class="labelseccion">
                        <?php echo Yii::t("app", "PROTECCIÓN DE LA EXPERIENCIA"); ?>
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
                        ["class" => "openSeccion", "id" => "generalProteccionExp"]
                    )
                    ?>
                    <?php $this->registerJs('$("#generalProteccionExp").click(function () {
                                $("#datosProteccionExp").toggle("slow");
                            });'); ?>
                </div>
            </div>
            <div id="datosProteccionExp" style="display: none;">
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
                            echo Html::dropDownList(
                                "responsabilidad",
                                $data->basesatisfaccion->responsabilidad,
                                [
                                    'CANAL' => 'CANAL',
                                    'MARCA' => 'MARCA',
                                    'COMPARTIDA' => 'COMPARTIDA',
                                    'EQUIVOCADA' => 'EQUIVOCADA',
                                    'NA' => 'N/A'
                                ],
                                [
                                    "id" => "responsabilidad",
                                    "class" => "form-control",
                                    'prompt' => 'Seleccione ...',
                                    "disabled" => ($data->preview) ? true : false
                                ]
                            );
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
                                'canal[]',
                                explode(", ", $data->basesatisfaccion->canal),
                                (isset($data->responsabilidad['CANAL'])) ? $data->responsabilidad['CANAL'] : [],
                                [
                                    'id' => 'canal', 'disabled' => ($data->preview) ? true : false, 'separator' => '<br />'
                                ]
                            );
                            ?>
                        </div>
                    </div>
                </div>

                <div class="form-group" id="divmarca" style="display: none">
                    <div class="control-group">
                        <label class="control-label col-sm-3">
                            <?php echo Yii::t("app", "Marca"); ?>
                        </label>
                        <div class="col-sm-9">
                            <?php
                            echo Html::checkboxList(
                                'marca[]',
                                explode(", ", $data->basesatisfaccion->marca),
                                (isset($data->responsabilidad['MARCA'])) ? $data->responsabilidad['MARCA'] : [],
                                [
                                    'id' => 'marca', 'disabled' => ($data->preview) ? true : false, 'separator' => '<br />'
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
                            echo Html::checkboxList(
                                'equivocacion[]',
                                explode(", ", $data->basesatisfaccion->equivocacion),
                                (isset($data->responsabilidad['EQUIVOCACION'])) ? $data->responsabilidad['EQUIVOCACION'] : [],
                                [
                                    'id' => 'equivocacion', 'disabled' => ($data->preview) ? true : false, 'separator' => '<br />'
                                ]
                            );
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
        <?php
        echo $varFin;
        if (($contadorSecciones % $cantDivs) == 0) {
            echo $varFin;
        }
        ?>
        <!-- FIN SECCION PROTECCIÓN DE LA EXPERIENCIA -->
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
                        <caption>Tabla Experiencias</caption>
                        <thead>
                            <tr>
                                <th scope="col">Enfoque</th>
                                <th scope="col">Problema</th>
                                <th scope="col">Comentarios</th>
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
                        <caption>Tabla tipos llamadas</caption>
                        <thead>
                            <tr>
                                <th scope="col">Tipo de Llamada</th>
                                <th scope="col">Llamada</th>
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
                                <th scope="col">Comentario Feedback</th>
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
                                    'tmpejecucionfeedbacks/index', 'tmp_formulario_id' => $data->formulario_id, 'usua_id_lider' => $data->usua_id_lider, 'evaluado_id' => $data->tmp_formulario->evaluado_id, 'basesatisfacion_id' => $data->basesatisfaccion->id
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
        <?php
        echo $varFin;
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
                    <?php echo Yii::t('app', 'Gestión del Caso') ?>
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
            <div class="row seccion">
                <?php echo Yii::t('app', 'Gestión del Caso') ?>
            </div>
            <div id="divTablaPreguntas">
                <table id="tablaPreguntas" class="table table-striped table-bordered detail-view">
                    <caption>Tabla preguntas</caption>
                    <tr>
                        <th scope="col">Estado</th>
                        <td><?php
                            echo Html::dropDownList(
                                "estado",
                                $data->basesatisfaccion->estado,
                                $data->basesatisfaccion->estadosList(),
                                ["id" => "estado", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]
                            );
                            ?>
                        </td>
                    </tr>
                </table>
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
                                "class" => "form-control",
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
                                "class" => "form-control",
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
                    <?php if (isset($data->formulario->subi_calculo)) : ?>
                        <?= Html::a(Yii::t('app', 'Calcular subi'), "javascript:void(0)", ['class' => 'btn  btn-primary soloCalcular'])
                        ?>
                    <?php endif; ?>
                    <?= Html::a(Yii::t('app', 'Cancel'), ['cancelarformulario', 'id' => $data->basesatisfaccion->id], ['class' => 'btn btn-default soloCancelar']) ?>

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
                /* BOTÓN GUARDAR Y ENVIAR */
                $(".soloCalcular").click(function() {
                    $("#submitcorrecto").val("SI");
                    $(this).attr("disabled", "disabled");
                    $(".soloCancelar").attr("disabled", "disabled");
                    $(".soloFinalizar").attr("disabled", "disabled");
                    var guardarFormulario = $("#guardarFormulario");
                    guardarFormulario.attr('action', '<?php echo Url::to(['basesatisfaccion/consultarcalificacionsubi']); ?>');
                    var valid = validarFormulario();
                    if (valid) {
                        guardarFormulario.submit();
                    } else {
                        $("#submitcorrecto").val("NO");
                        $(this).removeAttr("disabled");
                        $(".soloCancelar").removeAttr("disabled");
                        $(".soloFinalizar").removeAttr("disabled");
                    }
                });
                /* BOTÓN GUARDAR Y ENVIAR */
                $(".soloFinalizar").click(function() {
                    var varidpcrc =  document.getElementById("idVarPcrc").value;
                    
                    if (varidpcrc == "") {
                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","Debe seleccionar un Centro de costos.","warning");
                        return;
                    }else{
                        $("#submitcorrecto").val("SI");
                        $(this).attr("disabled", "disabled");
                        $(".soloCancelar").attr("disabled", "disabled");
                        $(".soloCalcular").attr("disabled", "disabled");
                        var guardarFormulario = $("#guardarFormulario");
                        guardarFormulario.attr('action', '<?php echo Url::to(['basesatisfaccion/guardaryenviarformulariogestion']); ?>');
                        var varArbols = "<?php echo $varPcrc; ?>";

                        if (varArbols == '3104') {
                            var varidformulario = document.getElementById("txtVariable").value;
                            var varEncuesta = "<?php echo $varBase; ?>";

                            $.ajax({
                                method: "post",
                                url: "listasformulario",
                                data: {
                                    txtvidformulario: varidformulario,
                                    txtvarbols: varArbols,
                                    txtvencuesta: varEncuesta,
                                },
                                success: function(response) {
                                    var Rta = JSON.parse(response);
                                    console.log(Rta);
                                }
                            });
                        }

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
                    }
                });


                /* BOTÓN PARA BORRAR EL FORMULARIO */
                $(".soloCancelar").click(function() {
                    $("#submitcorrecto").val("SI");
                    $(this).attr("disabled", "disabled");
                    $(".soloFinalizar").attr("disabled", "disabled");
                    $(".soloGuardar").attr("disabled", "disabled");
                    $(".soloCalcular").attr("disabled", "disabled");
                    var guardarFormulario = $("#guardarFormulario");
                    var tmp_form = $("#tmp_formulario_id").val();
                    ruta = '<?php echo Url::to(['eliminartmpform']); ?>?&tmp_form=' + tmp_form;
                    guardarFormulario.attr('action', ruta);
                    guardarFormulario.submit();
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

                /* RESPONSABILIDAD */
                $("#responsabilidad").change(function() {
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

                /* RESPONSABILIDAD SPC*/
                $("#responsabilidadspc").change(function () {
                    if ($(this).val() === 'CANAL') {
                        $("#divcanalspc").show('slow');
                        $("#divmarcaspc").hide('slow');
                        $("#divequivocacionspc").hide('slow');
                        $("#marcaspc").val('');
                        $("#equivocacionspc").val('');

                    }
                    if ($(this).val() === 'MARCA') {
                        $("#divmarcaspc").show('slow');
                        $("#divcanalspc").hide('slow');
                        $("#divequivocacionspc").hide('slow');
                        $("input[name='canalspc[]']:checked").attr('checked', false);
                        $("input[name='equivocacionspc[]']:checked").attr('checked', false);
                    }
                    if ($(this).val() === 'EQUIVOCADA') {
                        $("#divequivocacionspc").show('slow');
                        $("#divcanalspc").hide('slow');
                        $("#divmarcaspc").hide('slow');
                        $("input[name='canalspc[]']:checked").attr('checked', false);
                        $("input[name='marcaspc[]']:checked").attr('checked', false);
                    }
                    if ($(this).val() === 'COMPARTIDA') {
                        $("#divcanalspc").show('slow');
                        $("#divmarcaspc").show('slow');
                        $("#divequivocacionspc").hide('slow');
                        $("input[name='equivocacionspc[]']:checked").attr('checked', false);
                    }
                    if ($(this).val() === 'NA') {
                        $("#divcanalspc").hide('slow');
                        $("#divmarcaspc").hide('slow');
                        $("#divequivocacionspc").hide('slow');
                        $("input[name='equivocacionspc[]']:checked").attr('checked', false);
                        $("input[name='canalspc[]']:checked").attr('checked', false);
                        $("input[name='marcaspc[]']:checked").attr('checked', false);
                    }
                });

                if ($("#responsabilidadspc").val() !== '') {
                    if ($("#responsabilidadspc").val() === 'CANAL') {
                        $("#divcanalspc").show('slow');
                    }
                    if ($("#responsabilidadspc").val() === 'MARCA') {
                        $("#divmarcaspc").show('slow');
                    }
                    if ($("#responsabilidadspc").val() === 'EQUIVOCADA') {
                        $("#divequivocacionspc").show('slow');
                    }
                    if ($("#responsabilidadspc").val() === 'COMPARTIDA') {
                        $("#divcanalspc").show('slow');
                        $("#divmarcaspc").show('slow');
                    }
                }

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
                        ruta = '<?php echo Url::to(['formularios/adicionarform']); ?>?&tmp_form=' + tmp_form;
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
                    var banderaescalado = '<?php
                                            $banderaescalado = Yii::$app->request->get("banderaescalado");
                                            if (isset($banderaescalado)) {
                                                echo Yii::$app->request->get("banderaescalado");
                                            } ?>';
                    if ($(this).is(":checked") == true) {
                        ruta = '<?php echo Url::to(['formularios/escalarform']); ?>?&tmp_form=' + tmp_form + '&banderaescalado=' + banderaescalado;
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
                $(".soloDeclinar").on("click", function() {
                    var tmp_form = $("#tmp_formulario_id").val();
                    var varEncuesta = "<?php echo $varBase; ?>";
                    var varMotivo = document.getElementById("txtmotivo").value;
                    var banderaescalado = '<?php
                                            $banderaescalado = Yii::$app->request->get("banderaescalado");
                                            if (isset($banderaescalado)) {
                                                echo Yii::$app->request->get("banderaescalado") ;
                                            } ?>';
                    if ($(this).is(":checked") == true) {
                        ruta = '<?php echo Url::to(['formularios/escalarform']); ?>?&tmp_form=' + tmp_form + '&banderaescalado=' + banderaescalado;
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
                    if(varMotivo != ""){
                            $.ajax({
                            method: "post",
                            url: "enviarmotivos",
                            data: {
                                txtvaridtranscripcion : varEncuesta,
                                txtvarmotivo : varMotivo,
                            },
                            success : function(response){
                                numRta =   JSON.parse(response);
                                console.log(numRta);
                                location.reload();
                            },
                        });
                    }
                });

                $(".soloMostrar").on("click", function() {
                    var varidtbn1 = document.getElementById("prueba11");
                    var varidtbn2 = document.getElementById("prueba12");
                    var varPartT = document.getElementById("tablesi"); 
                    varidtbn1.style.display = 'none';
                    varidtbn2.style.display = 'inline';   
                    varPartT.style.display = 'inline';

                });
                $(".soloMostrar1").on("click", function() {
                    var varidtbn1 = document.getElementById("prueba11");
                    var varidtbn2 = document.getElementById("prueba12");
                    var varPartT = document.getElementById("tablesi"); 
                    varidtbn1.style.display = 'inline';
                    varidtbn2.style.display = 'none';   
                    varPartT.style.display = 'none';
                });
            });

            function validarFormulario() {
                var hayErrores = false;
                var idsDetalles = '<?php echo json_encode($detalles_ids); ?>';
                var cont = 0;
                var visible = false;
                var contTotal = <?php echo count($data->totalBloques) ?>;
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
                var totalBloques = new Array();
                <?php foreach ($data->totalBloques as $tmpbloque) : ?>
                    totalBloques[<?php echo $tmpbloque->bloque_id ?>] = <?php echo $tmpbloque->bloque_id ?>;
                <?php endforeach; ?>
                var idseccionesError = new Array();
                try {
                    //validar responsabilidad                
                    if ($("#responsabilidad").val() === '') {
                        $("#responsabilidad").addClass('field-error');
                        hayErrores = true;
                    } else {
                        $("#responsabilidad").removeClass('field-error');
                        if (($("#responsabilidad").val() === 'CANAL' && !$("input[name='canal[]']:checked").val()) ||
                            ($("#responsabilidad").val() === 'COMPARTIDA' && !$("input[name='canal[]']:checked").val())) {
                            $("#canal").addClass('field-error');
                            hayErrores = true;
                        } else {
                            $("#canal").removeClass('field-error');
                        }
                        if (($("#responsabilidad").val() === 'MARCA' && !$("input[name='marca[]']:checked").val()) ||
                            ($("#responsabilidad").val() === 'COMPARTIDA' && !$("input[name='marca[]']:checked").val())) {
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

                    //validar responsabilidad SPC               
                    if ($("#responsabilidadspc").val() === '') {
                        $("#responsabilidadspc").addClass('field-error');
                        hayErrores = true;
                    } else {
                        $("#responsabilidadspc").removeClass('field-error');
                        if (($("#responsabilidadspc").val() === 'CANAL' && !$("input[name='canalspc[]']:checked").val()) ||
                            ($("#responsabilidadspc").val() === 'COMPARTIDA' && !$("input[name='canalspc[]']:checked").val())) {
                            $("#canalspc").addClass('field-error');
                            hayErrores = true;
                        } else {
                            $("#canalspc").removeClass('field-error');
                        }
                        if (($("#responsabilidadspc").val() === 'MARCA' && !$("input[name='marcaspc[]']:checked").val()) ||
                            ($("#responsabilidadspc").val() === 'COMPARTIDA' && !$("input[name='marcaspc[]']:checked").val())) {
                            $("#marcaspc").addClass('field-error');
                            hayErrores = true;
                        } else {
                            $("#marcaspc").removeClass('field-error');
                        }
                        if ($("#responsabilidadspc").val() === 'EQUIVOCADA' && !$("input[name='equivocacionspc[]']:checked").val()) {
                            $("#equivocacionspc").addClass('field-error');
                            hayErrores = true;
                        } else {
                            $("#equivocacionspc").removeClass('field-error');
                        }
                    }



                    $.each(totalBloques, function(l, sel) {
                        if ($('#bloque_' + sel).is(':checked')) {
                            cont++;
                        }
                    });


                    $.each(idsDetalles, function(i, val) {
                        visible = false;
                        //Primero se valida que se haya seleccionado la calificacion.
                        var a = $("#calificacion_" + val).attr('disabled');
                        if (a !== 'disabled') {
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
                                    //Si se seleccionó calificacion y tiene la marca de desplega tipificaciones
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
                        }
                    });
                    if ($("#fuente").val() == '') {
                        $("#fuente").addClass('field-error');
                        $("#datosGenerales").show("slow");
                        $("#datosGeneralesreason").show("slow");
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
                        for (var b = 0; b < idseccionesError.length; b++) {
                            $("#seccion" + idseccionesError[b]).show("slow");
                            $("#datosSeccion" + idseccionesError[b]).show("slow");
                        }
                        return false;
                    }
                    if (cont === contTotal) {
                        $('#modalBloques').modal('show');
                        return false;
                    }
                    return true;
                } catch (err) {
                    alert("Error al validar el formulario." + err);
                    return false;
                }
            }
        </script>
    <?php else : ?>
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
                    <caption>Tabla preguntas</caption>
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
                        <caption>Tabla</caption>
                        <tbody <tr>
                            <th scope="col"><?php echo Yii::t("app", "ANI"); ?></th>
                            <td><?php echo $data->basesatisfaccion->ani ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Identificación"); ?></th>
                                <td><?php echo $data->basesatisfaccion->identificacion ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Nombre"); ?></th>
                                <td><?php echo $data->basesatisfaccion->nombre ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Ext"); ?></th>
                                <td><?php echo $data->basesatisfaccion->ext ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Tipo Servicio"); ?></th>
                                <td><?php echo $data->basesatisfaccion->tipo_servicio ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Tipo encuesta"); ?></th>
                                <td><?php echo $data->basesatisfaccion->tipo_encuesta ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Lider Equipo"); ?></th>
                                <td><?php echo $data->basesatisfaccion->lider_equipo ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Programa/PCRC"); ?></th>
                                <td><?php echo $data->basesatisfaccion->pcrc0->name ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Cliente"); ?></th>
                                <td><?php echo $data->basesatisfaccion->cliente0->name ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "RN"); ?></th>
                                <td><?php echo $data->basesatisfaccion->rn ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Agente"); ?></th>
                                <td><?php echo $data->basesatisfaccion->agente ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo Yii::t("app", "Tipología"); ?></th>
                                <td><?php
                                    echo Html::dropDownList(
                                        "categoria",
                                        $data->basesatisfaccion->tipologia,
                                        $data->recategorizar,
                                        ["id" => "categoria", "class" => "form-control", 'prompt' => 'Seleccione ...', "disabled" => ($data->preview) ? true : false]
                                    );
                                    ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->preguntas['0']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['0']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta1 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->preguntas['1']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['1']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta2 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->preguntas['2']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['2']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta3 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->preguntas['3']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['3']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta4 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->preguntas['4']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['4']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta5 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->preguntas['5']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['5']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta6 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->preguntas['6']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['6']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta7 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->preguntas['7']['enunciado_pre'] : 'NO APLICA' ?></th>
                                <td><?php echo (strtoupper($data->preguntas['7']['nombre']) != 'NO APLICA') ? $data->basesatisfaccion->pregunta8 : 'NO APLICA' ?></td>
                            </tr>
                            <tr>
                                <th scope="col"><?php echo (strtoupper($data->preguntas['8']['nombre']) != 'NO APLICA') ? $data->preguntas['8']['enunciado_pre'] : 'NO APLICA' ?></th>
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
    <script type="text/javascript">
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });

        function myFunction() {
            document.getElementById("txt_comentarios_2").value = "1. Detalle los comportamientos positivos que se indentifican en el embajador de Marca y aquellos interesados que aportaron en la gestion:  \n\n\n\n\n2. Oportunidad de Mejora que impacten la Promesa de Relacion: ";
        }

        function myFunction2() {
            document.getElementById("txt_comentarios_1").value = "1. Oportunidad de Mejora que impacten la Promesa de Solucion del Embajador:  \n\n\n\n\n2. Oportunidad de Mejora que impacten la Promesa de Solucion del Canal:  \n\n\n\n\n3. Oportunidad de Mejora que impacten los Productos:  \n\n\n\n\n4. Oportunidad de Mejora que impacten los Procedimientos/Politicas:  \n\n\n\n\n5. Contiene la percepcion del usuario en los siguientes aspectos: \n5.1. Segmento de Cliente: \n5.2. Que piensa, siente?: \n5.3. Que oye?: \n5.4. Que ve?: \n5.5. Que dice y hace?: ";
        }

        function enviarvalencia() {
            var varidselectvalencias = document.getElementById("idselectvalencias").value;
            var varconnid = "<?php echo $varConnids; ?>";

            if (varidselectvalencias == "") {
                event.preventDefault();
                swal.fire("��� Advertencia !!!", "Debe de seleccionar una valencia", "warning");
                return;
            } else {
                $.ajax({
                    method: "post",
                    url: "enviarvalencias",
                    data: {
                        txtvaridselectvalencias: varidselectvalencias,
                        txtvarconnid: varconnid,
                    },
                    success: function(response) {
                        numRta = JSON.parse(response);
                        console.log(numRta);
                        location.reload();
                    },
                });
            }
        };
        

        function enviartexto(){
            var varidtranscripcion = document.getElementById("idtranscripcion").value;
            var varconnidtexto = "<?php echo $varConnids; ?>";

            if (varidtranscripcion == "") {
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","La transcripcion no debe estar vacia","warning");
                return;
            }else{
                $.ajax({
                    method: "post",
                    url: "enviartextos",
                    data: {
                        txtvaridtranscripcion : varidtranscripcion,
                        txtvarconnidtexto : varconnidtexto,
                    },
                    success : function(response){
                        numRta =   JSON.parse(response);
                        console.log(numRta);
                        location.reload();
                    },
                });
            }
        };


    </script>