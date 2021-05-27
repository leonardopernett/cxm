<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

$variables = Yii::$app->user->identity->rolId;

/* @var $this yii\web\View */
/* @var $model app\models\Arboles */
/* @var $form yii\widgets\ActiveForm */


$js = <<< 'SCRIPT'
/ To initialize BS3 popovers set this below /
    
$( document ).ready(function() {    
    if ($('#arboles-snhoja').is(':checked')){                
            $(".esHoja").show("slow");
        }else{  
            $(".esHoja").hide("slow");
        }
        
    if ($('#arboles-snactivar_problemas').is(':checked')){                
            $("#div-tableroproblema").show("slow");
        }else{  
            $("#div-tableroproblema").hide("slow");
        }
        
    if ($('#arboles-snactivar_tipo_llamada').is(':checked')){                
            $("#div-tiposLlamada").show("slow");
        }else{  
            $("#div-tiposLlamada").hide("slow");
        }
});

$('#arboles-snhoja').change(function () {               
        if ($(this).is(':checked')){                
            $(".esHoja").show("slow");
        }else{  
            $(".esHoja").hide("slow");
        }
    }
);

$('#arboles-snactivar_problemas').change(function () {               
        if ($(this).is(':checked')){                
            $("#div-tableroproblema").show("slow");
        }else{  
            $("#div-tableroproblema").hide("slow");
        }
    }
);

$('#arboles-snactivar_tipo_llamada').change(function () {               
        if ($(this).is(':checked')){                
            $("#div-tiposLlamada").show("slow");
        }else{  
            $("#div-tiposLlamada").hide("slow");
        }
    }
);
SCRIPT;
// Register tooltip/popover initialization javascript

?>

<?php
    if ($variables == "270" || $variables == "276") {
?>
        <div class="arboles-form">

            <?php yii\widgets\Pjax::begin(['id' => 'form_arboles']); ?>
            
            <?php $this->registerJs($js); ?>

            <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true]]); ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>  
            
            <?= $form->field($model, 'arbol_id')->dropDownList($model->getArbolPadreList()) ?>

            <?= $form->field($model, 'snhoja')->checkBox()  ?>

            <div class="esHoja well">
                <?= $form->field($model, 'formulario_id')->dropDownList($model->getFormularioList(), ['prompt'=>Yii::t('app', 'Select ...')]) ?>
                
                <?= $form->field($model, 'bloquedetalle_id')->textInput() ?>
                
                <?= $form->field($model, 'equipos')->widget(Select2::classname(), [        
                    'language' => 'es',
                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                    'pluginOptions' => [
                        'multiple'=>true,
                        'allowClear' => false,
                        'minimumInputLength' => 4,
                        'ajax' => [
                            'url' => Url::to(['equiposlist']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                        ],
                        'initSelection' => new JsExpression('function (element, callback) {
                                            var id=$(element).val();
                                            if (id !== "") {
                                                $.ajax("'.Url::to(['equiposlist']).'?id=" + id, {
                                                    dataType: "json",
                                                    type: "post"
                                                }).done(function(data) { callback(data.results);});
                                            }
                                        }')
                        ]
                    ]
                );?>
            </div>                    

            <?= $form->field($model, 'nmumbral_verde')->textInput() ?>

            <?= $form->field($model, 'nmumbral_amarillo')->textInput() ?>

            <?= $form->field($model, 'nmumbral_positivo')->textInput() ?>
            
            <?= $form->field($model, 'snactivar_problemas')->checkBox() ?>
            
            <div id="div-tableroproblema" class="well">
                <?= $form->field($model, 'tableroproblema_id')->dropDownList($model->getProblemasList()) ?>
            </div>   

            <?= $form->field($model, 'snactivar_tipo_llamada')->checkBox()  ?>
            
            <div id="div-tiposLlamada" class="well">
                <?= $form->field($model, 'tiposllamada_id')->dropDownList($model->getTiposLlamadasList()) ?>
            </div>
            
            
            <?= $form->field($model, 'responsables')->widget(Select2::classname(), [        
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'multiple'=>true,
                    'allowClear' => false,
                    'minimumInputLength' => 4,
                    'ajax' => [
                        'url' => Url::to(['usuarioslist']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression('function (element, callback) {
                                        var id=$(element).val();
                                        if (id !== "") {
                                            $.ajax("'.Url::to(['usuarioslist']).'?id=" + id, {
                                                dataType: "json",
                                                type: "post"
                                            }).done(function(data) { callback(data.results);});
                                        }
                                    }')
                    ]
                ]
            );?>

            <?php $var2 = ['0' => 'Si', '1' => 'No']; ?>
            
            <?= $form->field($model, "activo")->dropDownList($var2, ['prompt' => 'Selecciones...', 'id'=>"id_activo"]) ?>
               
            <hr>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>            
                </div>        
            </div>

            <?php ActiveForm::end(); ?>
            <?php yii\widgets\Pjax::end(); ?>
        </div>
<?php
    }
    else
    {
        if ($variables == "272") {           
?>

        <div class="arboles-form">

            <?php yii\widgets\Pjax::begin(['id' => 'form_arboles']); ?>
            
            <?php $this->registerJs($js); ?>

            <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true]]); ?>

            <?= $form->field($model, 'name')->textInput(['maxlength' => 100, 'disabled' => 'disabled']) ?>  
            
            <?= $form->field($model, 'arbol_id')->dropDownList($model->getArbolPadreList(), array('disabled'=>'disabled')) ?>

            <?= $form->field($model, 'snhoja')->checkBox(['class'=>"hidden"])  ?>

            <div class="esHoja well">
                <?= $form->field($model, 'formulario_id')->dropDownList($model->getFormularioList(), array('disabled'=>'disabled'), ['prompt'=>Yii::t('app', 'Select ...')]) ?>
                
                <?= $form->field($model, 'bloquedetalle_id')->textInput(['disabled'=>'disabled']) ?>
                
                <?= $form->field($model, 'equipos')->widget(Select2::classname(), [        
                    'language' => 'es',
                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                    'pluginOptions' => [
                        'multiple'=>true,
                        'allowClear' => false,
                        'minimumInputLength' => 4,
                        'ajax' => [
                            'url' => Url::to(['equiposlist']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                        ],
                        'initSelection' => new JsExpression('function (element, callback) {
                                            var id=$(element).val();
                                            if (id !== "") {
                                                $.ajax("'.Url::to(['equiposlist']).'?id=" + id, {
                                                    dataType: "json",
                                                    type: "post"
                                                }).done(function(data) { callback(data.results);});
                                            }
                                        }')
                        ]
                    ]
                );?>
            </div>                    

            <?= $form->field($model, 'nmumbral_verde')->textInput(['disabled'=>'disabled']) ?>

            <?= $form->field($model, 'nmumbral_amarillo')->textInput(['disabled'=>'disabled']) ?>

            <?= $form->field($model, 'nmumbral_positivo')->textInput(['disabled'=>'disabled']) ?>
            
            <?= $form->field($model, 'snactivar_problemas')->checkBox(['disabled'=>'disabled']) ?>
            
            <div id="div-tableroproblema" class="well">
                <?= $form->field($model, 'tableroproblema_id')->dropDownList($model->getProblemasList(), array('disabled'=>'disabled')) ?>
            </div>   

            <?= $form->field($model, 'snactivar_tipo_llamada')->checkBox(['disabled'=>'disabled'])  ?>
            
            <div id="div-tiposLlamada" class="well">
                <?= $form->field($model, 'tiposllamada_id')->dropDownList($model->getTiposLlamadasList()) ?>
            </div>
            
            
            <?= $form->field($model, 'responsables')->widget(Select2::classname(), [        
                'language' => 'es',
                'disabled' => 'disabled',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'multiple'=>true,
                    'allowClear' => false,
                    'minimumInputLength' => 4,
                    'ajax' => [
                        'url' => Url::to(['usuarioslist']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression('function (element, callback) {
                                        var id=$(element).val();
                                        if (id !== "") {
                                            $.ajax("'.Url::to(['usuarioslist']).'?id=" + id, {
                                                dataType: "json",
                                                type: "post"
                                            }).done(function(data) { callback(data.results);});
                                        }
                                    }')
                    ]
                ]
            );?>

            <?php $var2 = ['0' => 'Si', '1' => 'No']; ?>
            
            <?= $form->field($model, "activo")->dropDownList($var2, ['prompt' => 'Selecciones...', 'id'=>"id_activo"]) ?>
               
            <hr>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>            
                </div>        
            </div>

            <?php ActiveForm::end(); ?>
            <?php yii\widgets\Pjax::end(); ?>
        </div>

<?php
        }
    }
?>