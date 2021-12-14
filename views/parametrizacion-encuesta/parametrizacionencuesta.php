<style>
    table.categoriagestion th{
        width: 15%;
    }
</style>
<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\grid\GridView;
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

<?php
$template = '<label for="programa" class="control-label col-sm-3">{label}</label><div class="col-sm-6">'
        . ' {input}{error}{hint}</div>';
?>
<?php $this->title = Yii::t('app', 'Módulo Parametrización de Encuestas');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Módulo Parametrización de Encuestas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
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

    <div class="row seccion text-center"> <?php echo Yii::t("app", "Módulo Parametrización de Encuestas") ?></div>
    <?php echo Html::beginForm(Url::to(['guardarparametrizacion']), "post", ["class" => "form-horizontal", "id" => "guardarFormulario"]); ?>
    <?php echo Html::input("hidden", "id", $model->id); ?>
    <div class="form-group">
        <div class="col-sm-12 well">
            <?= Html::a(Yii::t('app', 'Guardar Parametrización'), "javascript:void(0)", ['class' => 'btn btn-success soloFinalizar'])
            ?>
            <?=
            Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ])
            ?>
              <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #707372',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Agregar Valorado']) 
              ?>
        </div>        
    </div>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'cliente',
                'value' => $model->cliente0->name
            ],
            [
                'attribute' => 'programa',
                'value' => $model->programa0->name
            ],
        ],
    ])
    ?> 
    <div class="row seccion text-center"><?php echo Yii::t("app", "Creación de Preguntas") ?></div>
    <div id="divTablaPreguntas">
        <table id="tablaPreguntas" class="table table-striped table-bordered detail-view">
        <caption>Creación Preguntas</caption>
            <tr>
                <th scope="col"></th>
                <th scope="col">Categorías</th>
                <th scope="col">Nombre de la Pregunta</th> 
               
            </tr>
            <tr>
                <th scope="row">Pregunta 1</th>
                <td><?php
                    echo Html::dropDownList("categoria_p1"
                            , (isset($modelPregunta[0])) ? $modelPregunta[0]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p1", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_1"
                            , (isset($modelPregunta[0])) ? $modelPregunta[0]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_1",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td> 
               
            </tr>
            <tr>
                <th scope="row">Pregunta 2</th>
                <td><?php
                    echo Html::dropDownList("categoria_p2"
                            , (isset($modelPregunta[1])) ? $modelPregunta[1]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p2", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_2"
                            , (isset($modelPregunta[1])) ? $modelPregunta[1]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_2",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td>
               
            </tr>
            <tr>
                <th scope="row">Pregunta 3</th>
                <td><?php
                    echo Html::dropDownList("categoria_p3"
                            , (isset($modelPregunta[2])) ? $modelPregunta[2]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p3", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_3"
                            , (isset($modelPregunta[2])) ? $modelPregunta[2]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_3",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td>
               

            </tr>
            <tr>
                <th scope="row">Pregunta 4</th>
                <td><?php
                    echo Html::dropDownList("categoria_p4"
                            , (isset($modelPregunta[3])) ? $modelPregunta[3]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p4", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_4"
                            , (isset($modelPregunta[3])) ? $modelPregunta[3]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_4",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td> 
                
            </tr>
            <tr>
                <th scope="row">Pregunta 5</th>
                <td><?php
                    echo Html::dropDownList("categoria_p5"
                            , (isset($modelPregunta[4])) ? $modelPregunta[4]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p5", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_5"
                            , (isset($modelPregunta[4])) ? $modelPregunta[4]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_5",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td>
            </tr>
            <tr>
                <th scope="row">Pregunta 6</th>
                <td><?php
                    echo Html::dropDownList("categoria_p6"
                            , (isset($modelPregunta[5])) ? $modelPregunta[5]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p6", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_6"
                            , (isset($modelPregunta[5])) ? $modelPregunta[5]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_6",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta"),
                    ]);
                    ?>
                </td> 
                
            </tr>
            <tr>
                <th scope="row">Pregunta 7</th>
                <td><?php
                    echo Html::dropDownList("categoria_p7"
                            , (isset($modelPregunta[6])) ? $modelPregunta[6]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p7", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_7"
                            , (isset($modelPregunta[6])) ? $modelPregunta[6]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_7",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td>
                
            </tr>
            <tr>
                <th scope="row">Pregunta 8</th>
                <td><?php
                    echo Html::dropDownList("categoria_p8"
                            , (isset($modelPregunta[7])) ? $modelPregunta[7]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p8", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_8"
                            , (isset($modelPregunta[7])) ? $modelPregunta[7]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_8",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td>
               
            </tr>
            <tr>
                <th scope="row">Pregunta 9</th>
                <td><?php
                    echo Html::dropDownList("categoria_p9"
                            , (isset($modelPregunta[8])) ? $modelPregunta[8]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p9", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_9"
                            , (isset($modelPregunta[8])) ? $modelPregunta[8]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_9",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td>
                
            </tr>
            <tr>
                <th scope="row">Pregunta 10</th>
                <td><?php
                    echo Html::dropDownList("categoria_p10"
                            , (isset($modelPregunta[9])) ? $modelPregunta[9]->categoria : $modelPreguntaBase->categoria
                            , $datos->categorias
                            , ["id" => "categoria_p10", "class" => "form-control", 'prompt' => 'Seleccione ...']);
                    ?>
                </td>
                <td> <?php
                    echo Html::input("text"
                            , "pregunta_10"
                            , (isset($modelPregunta[9])) ? $modelPregunta[9]->enunciado_pre : $modelPreguntaBase->enunciado_pre
                            , [
                        "id" => "pregunta_10",
                        "class" => "form-control",
                        "placeholder" => Yii::t("app", "Ingrese la pregunta")
                    ]);
                    ?>
                </td>
               
            </tr>
        </table>
    </div>
    <?php echo Html::endForm(); ?>
    <div class="row seccion text-center">
        <?php echo Yii::t("app", "Tipología de Gestión") ?>
    </div>
    <div class="form-group">
        <div class="col-sm-12 well">
            <?php /* = Html::submitButton(Yii::t('app', 'Guargar y enviar'), ['class' => 'btn btn-success']) */ ?>
            <?php echo Html::a(Yii::t('app', 'Adicionar Categoría'), "javascript:void(0)", ['class' => 'btn btn-warning soloAdicionar'])
            ?>
        </div>        
    </div>
    <?php foreach ($datosgestion as $data): ?>
        <div class="col-sm-12 well">
            <?=
            DetailView::widget([
                'model' => $data['categoriagestion'],
                'attributes' => [
                    'name',
                    'prioridad',
                ],
                'options' => [
                    'class' => 'table table-striped table-bordered detail-view categoriagestion'
                ]
            ])
            ?>
            <?=
            GridView::widget([
                'dataProvider' => $data['dataProvider'],
                'columns' => [
                    //['class' => 'yii\grid\SerialColumn'],

                    [
                        'attribute' => 'categoria',
                        'value' => 'categoria0.nombre'
                    ],
                    'configuracion',
                ],
            ]);
            ?>

            <?= Html::a(Yii::t('app', 'Update'), "javascript:void(0)", ['class' => 'btn btn-primary soloActualizar' . $data['categoriagestion']->id]) ?>
            <?= Html::a(Yii::t('app', 'Delete'), "javascript:void(0)", ['class' => 'btn btn-danger eliminarCategoria'.$data['categoriagestion']->id]) ?>
            <?php
            $js = "$('.soloActualizar" . $data['categoriagestion']->id . "').click(function () {
            ruta = '" . Url::to(['detalleparametrizacion/index', 'id' => $model->id, 'categoriagestion' => (isset($data['categoriagestion']->id)) ? $data['categoriagestion']->id : 0]) . "';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                success: function (response) {
                    $('#ajax_result').html(response);
                }
            });
        });
        
        $('.eliminarCategoria".$data['categoriagestion']->id."').click(function () {
            var id = $('#id').val();
            ruta = '".Url::to(['eliminarcategorigestion', 'idparame' => $model->id, 'categoriagestion' => (isset($data['categoriagestion']->id)) ? $data['categoriagestion']->id : 0])."';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                success: function (response) {
                    $('#ajax_result').html(response);
                }
            });
        });";
            $this->registerJs($js);
            ?>
        </div>
    <?php endforeach; ?>

    <div class="form-group">
        <div class="col-sm-12 well">
            <?php /* = Html::submitButton(Yii::t('app', 'Guargar y enviar'), ['class' => 'btn btn-success']) */ ?>
            <?php echo Html::a(Yii::t('app', 'Adicionar Categoría'), "javascript:void(0)", ['class' => 'btn btn-warning soloAdicionar'])
            ?>
        </div>        
    </div>




    <?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
    ?> 
</div>

<script type="text/javascript">
    $(document).ready(function () {

        function fnForm2Array(strForm) {
            var arrData = new Array();
            $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
                if ($(this).attr('name')) {
                    arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
                }
            });
            return arrData;
        }

        function cambiarClass(strForm) {
            var arrData = new Array();
            $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
                if ($(this).val() == '') {
                    $(this).addClass("field-error");
                } else {
                    $(this).removeClass("field-error");
                }

            });
            return arrData;
        }
        $(".soloFinalizar").click(function () {
            datos = fnForm2Array('guardarFormulario');
            var bandera = false;
            for (i = 0; i < datos.length; i++) {
                if (datos[i]['value'] === null || datos[i]['value'] === '') {
                    $('#modalCampos').modal('show');
                    bandera = true;
                }
            }
            if (bandera) {
                cambiarClass('guardarFormulario');
                return;
            }
            ruta = '<?php echo Url::to(['guardarparametrizacion', 'forma' => true]); ?>';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: datos
            });
        });

        $(".soloAdicionar").click(function () {
            datos = fnForm2Array('guardarFormulario');
            ruta = '<?php echo Url::to(['guardarparametrizacion', 'forma' => false]); ?>';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: datos
            });
            var id = $("#id").val();
            ruta = '<?php echo Url::to(['detalleparametrizacion/index', 'id' => $model->id, 'categoriagestion' => 0]); ?>';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                success: function (response) {
                    $('#ajax_result').html(response);
                }
            });
        });

        if (<?php echo isset($modelPregunta) ?>!==1){
            for (var i = 1; i <= <?php echo count($modelPregunta) ?>; i++) {
                $('#categoria_p' + i + ' > option[value="<?php
                        foreach ($datos->categorias as $key => $value) {
                            if ($value === 'NO APLICA') {
                                echo $key;
                            }
                        }
                ?>"]').attr('selected', 'selected');
            }
        }

    });
</script>