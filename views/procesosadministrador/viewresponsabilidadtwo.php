<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$varListTipo = ['CANAL'=>'CANAL', 'EQUIVOCACION' =>'EQUIVOCACION', 'MARCA'=>'MARCA'];

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css" >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card2 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card3 {
            height: 86px;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

</style>
<br>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<div class="capaInfo" id="idcapaInfo" style="display: inline;">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 15px; color: #FFFFFF;"><?= Yii::t('app', 'Acciones de Clonar') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-8">
            <div class="card1 mb">
                <label  style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #981F40;"></em> <?= Yii::t('app', 'Formulario Con Responsabilidad') ?></label>
                <?=
                    $form->field($model, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['id'=>'idarbolescon', 'placeholder' => Yii::t('app', 'Seleccionar...')],
                        'pluginOptions' => [
                            'multiple' => false,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/getarboles']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/getarboles']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
                    );
                ?>
            </div>

            <br>

            <div class="card1 mb">
                <label  style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #981F40;"></em> <?= Yii::t('app', 'Formulario Sin Responsabilidad') ?></label>
                <?=
                    $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['id'=>'idarbolessin', 'placeholder' => Yii::t('app', 'Seleccionar...')],
                        'pluginOptions' => [
                            'multiple' => false,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/getarboles']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/getarboles']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
                    );
                ?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card3 mb">
                <label  style="font-size: 15px;"><em class="fas fa-clone" style="font-size: 15px; color: #981F40;"></em> <?= Yii::t('app', 'Responsabilidades (Encuestas)') ?></label>
                <div onclick="clonardatos();" class="btn btn-danger" method='post' id="botones1" style="text-align: center;">
                    Clonar
                </div>   
            </div>
        </div>
    </div>

</div>
<hr>
<div class="capaRegistro" id="idcapaRegistro" style="display: inline;">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 15px; color: #FFFFFF;"><?= Yii::t('app', 'Acciones de Registro') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-8">
            <div class="card1 mb">
                <label  style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #4298B4;"></em> <?= Yii::t('app', 'Seleccionar Programa/Pcrc') ?></label>
                <?=
                    $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['id'=>'idarboles', 'placeholder' => Yii::t('app', 'Seleccionar...')],
                        'pluginOptions' => [
                            'multiple' => false,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/getarboles']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['getarboles']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
                    );
                ?>
            </div>            
        
            <br>

            <div class="card1 mb">
                <label  style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #4298B4;"></em> <?= Yii::t('app', 'Seleccionar Tipo') ?></label>
                <?= $form->field($model, "nameArbol", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varListTipo, ['prompt' => 'Seleccionar Tipo...', 'id'=>"idTipos"]) ?>
            </div>     

            <br>

            <div class="card1 mb">
                <label  style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 15px; color: #4298B4;"></em> <?= Yii::t('app', 'Ingresar Responsabilidad') ?></label>
                <?= $form->field($model, 'comentarios', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 100, 'id'=>'idComentarios', 'placeholder'=>'Ingresar Responsabilidad'])->label('') ?>
            </div>  

        </div>    


        <div class="col-md-4">
            <div class="card2 mb">
                <label  style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 15px; color: #4298B4;"></em> <?= Yii::t('app', 'Guardar Datos') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Guardar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'verificar();',
                                'title' => 'Guardar Datos']) 
                ?>
            </div>            
        </div>

    </div>

</div>
<hr>
<?php ActiveForm::end(); ?>

<script type="text/javascript">
    function clonardatos(){
        var varidarbolescon = document.getElementById("idarbolescon").value;
        var varidarbolessin = document.getElementById("idarbolessin").value;

        if (varidarbolescon == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe de seleccionar el Programa/Pcrc que contiene responsabilidades","warning");
            return;
        }else{
            if (varidarbolessin == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Se debe de seleccionar el Programa/Pcrc que no contiene responsabilidades","warning");
                return;
            }else{
                $.ajax({
                    method: "get",
                    url: "guardarclon",
                    data: {
                        txtvaridarbolescon : varidarbolescon,
                        txtvaridarbolessin : varidarbolessin,
                    },
                    success : function(response){
                        numRta =   JSON.parse(response);

                        if (numRta == 0) {
                            event.preventDefault();
                            swal.fire("¡¡¡ Advertencia !!!","El formulario seleccionado no contiene tema de responsabilidades.","warning");
                            return;
                        }else{
                            window.location.href='../../index.php/procesosadministrador/parametrizarresponsabilidad';
                        }
                    }
                });
            }
        }

    };


    function verificar(){
        var varidarboles = document.getElementById("idarboles").value;
        var varidTipos = document.getElementById("idTipos").value;
        var varcomentarios = document.getElementById("comentarios").value;

        if (varidarboles == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe de seleccionar el Programa/Pcrc","warning");
            return;
        }

        if (varidTipos == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe de seleccionar el tipo","warning");
            return;
        }

        if (varcomentarios == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe de ingresar una responsabilidad","warning");
            return;
        }

    };
</script>