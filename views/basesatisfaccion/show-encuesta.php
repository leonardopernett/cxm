<?php 

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;
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
?>

<?php $this->title = Yii::t('app', 'Realizar Encuesta Telefónica'); ?>

<div class="form-horizontal">
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

    <?= Html::beginForm(Url::to(['basesatisfaccion/guardarEncuesta']), "post", ["class" => "form-horizontal", "id" => "guardarFormulario"]); ?>
    <?= Html::input("hidden", "id_encuesta", $model->id, ["id" => "id_encuesta"]); ?>
    <div class="form-group">
        <div class="col-sm-12 well">
            <?= Html::a(Yii::t('app', 'Guardar'), "javascript:void(0)", ['class' => 'btn btn-warning soloGuardar'])
            ?>            
            <?= Html::a(Yii::t('app', 'Borrar'), ['delete', 'id' => $model->id], [
                 'class' => 'btn btn-danger',
                 'data' => [
   
                 ],
             ]) ?>
        </div>        
    </div>


    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'identificacion',
            'nombre',
            [
                'attribute' => 'pcrc',
                'value' => $model->pcrc0->name
            ],
            [
                'attribute' => 'cliente',
                'value' => $model->cliente0->name
            ],
            'rn',
            'agente',
        ],
    ])
    ?> 
    <?php foreach ($data->datoSeccion as $seccion): ?>
        <div class="col-sm-12">
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
                    </div>
                    <?php foreach ($data->preguntas as $pregunta): ?>
                        <?php foreach ($data->datoBloqueDetalle as $detalle): ?>
                            <?php if ($detalle->name == $pregunta->categoria): ?>
                                <?php if ($detalle->bloque_id == $bloque->id): ?>
                                    <div class="form-group">
                                        <div class="control-group">
                                            <label class="control-label col-sm-9">
                                                <?php echo $pregunta->enunciado_pre; ?>
                                            </label>
                                            <div class="col-sm-3">
                                                <select 
                                                    name="<?php echo $pregunta->pre_indicador ?>" 
                                                    class="form-control toggleTipificacion" 
                                                    data-id-detalle="<?php echo $detalle->id ?>" 
                                                    id="<?php echo $pregunta->pre_indicador ?>">
                                                    <option value=""></option>
                                                    <?php foreach ($detalle->calificaciones as $calificacion): ?>
                                                        <option value="<?php
                                                        if (is_numeric($calificacion['name'])) {
                                                            echo $calificacion['name'];
                                                        } else {
                                                            if (strtoupper($calificacion['name'])=='NO APLICA' ||strtoupper($calificacion['name'])=='NA') {
                                                                echo strtoupper($calificacion['name']);
                                                            } else {
                                                                echo (strtoupper($calificacion['name']) == 'SI') ? '1' : '2';
                                                            }
                                                        }
                                                        ?>"><?php echo $calificacion['name']; ?></option>
                                                            <?php endforeach; ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                <?php endif; ?>
                            <?php endif; ?>

                        <?php endforeach; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    <?php endforeach; ?>

    <div class="col-sm-12">                
        <span style="color: #ff0000;"><?php echo "Comentario" ?></span>
        <?=
        Html::textarea("comentario"
                , $model->comentario
                , [
            "id" => "comentario",
            "class" => "form-control",
            "placeholder" => "Comentario..."
        ]);
        ?>
    </div>
    <div class="col-sm-6">                
        <span style="color: #ff0000;"><?php echo "Tipo encuesta" ?></span>
        <?=
        Html::dropDownList("tipo_encuesta"
                , (!empty($model->tipo_encuesta)) ? $model->tipo_encuesta : "No"
                , ["A" => "Automatica", "M" => "Manual", "R" => "Remarcado"]
                , [
            "id" => "tipo_encuesta",
            "class" => "form-control",
        ]);
        ?>
    </div>
    <div class="form-group">
        <div class="col-sm-12 well" style="margin-top: 20px">
            <?= Html::a(Yii::t('app', 'Guardar'), "javascript:void(0)", ['class' => 'btn btn-warning soloGuardar'])
            ?>            
            <?= Html::a(Yii::t('app', 'Borrar'), ['delete', 'id' => $model->id], [
                 'class' => 'btn btn-danger',
                 'data' => [
   
                 ],
             ]) ?>
        </div>        
    </div>
    <?php echo Html::endForm(); ?>

</div> 
<script type="text/javascript">
    /* BOTÓN GUARDAR VALORACIÓN SIN ENVIAR */
    $(document).ready(function () {
        /* BOTÓN GUARDAR VALORACIÓN SIN ENVIAR */
        $(".soloGuardar").click(function () {
            var guardarFormulario = $("#guardarFormulario");
            var id = $("#id_encuesta").val();
            var arrData = fnForm2ArrayValidar('guardarFormulario');
            for (i = 0; i < arrData.length; i++) {
                if (arrData[i]['value'] == null || arrData[i]['value'] == '') {
                    $('#modalCampos').modal('show');
                    return;
                }
            }
            $(this).attr("disabled", "disabled");
            //$(".soloFinalizar").attr("disabled", "disabled");
            var datos = fnForm2Array('guardarFormulario');
            $(".soloCancelar").attr("disabled", "disabled");
            ruta = '<?php echo Url::to(['basesatisfaccion/guardarencuesta']); ?>?&id=' + id;
            guardarFormulario.attr('action', ruta);
            $.ajax({
                url: ruta,
                type: 'post',
                dataType: 'text',
                encoding: "UTF-8",
                data: datos
            });
        });


        /* BOTÓN PARA BORRAR EL FORMULARIO */
        $(".soloCancelar").click(function () {
            $(this).attr("disabled", "disabled");
            $(".soloGuardar").attr("disabled", "disabled");
            var guardarFormulario = $("#guardarFormulario");
            var id = $("#id_encuesta").val();
            ruta = '<?php echo Url::to(['basesatisfaccion/eliminarencuesta']); ?>?&id=' + id;
            guardarFormulario.attr('action', ruta);
            guardarFormulario.submit();
        });
    });


    function fnForm2Array(strForm) {

        var arrData = new Array();

        $("select, textarea", $('#' + strForm)).each(function () {
            if ($(this).attr('name')) {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
            }
        });

        return arrData;

    }

    function fnForm2ArrayValidar(strForm) {

        var arrData = new Array();

        $("select", $('#' + strForm)).each(function () {
            if ($(this).attr('name')) {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
            }
        });

        return arrData;

    }
</script>