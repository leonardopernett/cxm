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

<div class="arboles-form">

    <?php yii\widgets\Pjax::begin(['id' => 'form_arboles']); ?>
    
    <?php $this->registerJs($js); ?>

    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true]]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100, 'disabled'=>'disabled']) ?>  
    
    <?= $form->field($model, 'arbol_id')->dropDownList($model->getArbolPadreList(), array('disabled'=>'disabled')) ?>

    <?= $form->field($model, 'snhoja')->checkBox(['disabled'=>'disabled'])  ?>

    <div class="esHoja well">
        <?= $form->field($model, 'formulario_id')->dropDownList($model->getFormularioList(),  array('disabled'=>'disabled'), ['prompt'=>Yii::t('app', 'Select ...')]) ?>
        
        <?= $form->field($model, 'bloquedetalle_id')->textInput(['disabled'=>'disabled']) ?>
        
        <?= $form->field($model, 'equipos')->widget(Select2::classname(), [        
            'language' => 'es',
	    'disabled' => 'disabled',
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
       
    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>            
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>
</div>

<script type="text/javascript">
    
$(document).ready(function(){

    var varRol = '<?php echo $variables ?>';

    var nombrePCRC = document.getElementById("arboles-name");
    var programaPCRC = document.getElementById("arboles-arbol_id");
    var esHoja = document.getElementById("arboles-snhoja");

    var formulariosId = document.getElementById("arboles-formulario_id");
    var pecRack = document.getElementById("arboles-bloquedetalle_id");
    var equiposArboles = document.getElementById("arboles-equipos");

    var umbralVerde = document.getElementById("arboles-nmumbral_verde");
    var umbralAmarillo = document.getElementById("arboles-nmumbral_amarillo");
    var umbralPositivo = document.getElementById("arboles-nmumbral_positivo");
    var activarProblems = document.getElementById("arboles-snactivar_problemas");
    var activarTLlamadas = document.getElementById("arboles-snactivar_tipo_llamada");
    var responsablesArbol = document.getElementById("arboles-responsables");

    if (varRol == "270"  || varRol == "276") 
    {
        nombrePCRC.disabled = false;
        programaPCRC.disabled = false;
        esHoja.disabled = false;

        formulariosId.disabled = false;
        pecRack.disabled = false;
        equiposArboles.disabled = false;

        umbralVerde.disabled = false;
        umbralAmarillo.disabled = false;
        umbralPositivo.disabled = false;
        activarProblems.disabled = false;
        activarTLlamadas.disabled = false;
        responsablesArbol.disabled = false;
    }
    else
    {
        if (varRol == "272") 
        {
            equiposArboles.disabled = false;
        }
        else
        {
            nombrePCRC.disabled = true;
            programaPCRC.disabled = true;
            esHoja.disabled = true;

            formulariosId.disabled = true;
            pecRack.disabled = true;
            equiposArboles.disabled = true;

            umbralVerde.disabled = true;
            umbralAmarillo.disabled = true;
            umbralPositivo.disabled = true;
            activarProblems.disabled = true;
            activarTLlamadas.disabled = true;
            responsablesArbol.disabled = true;
        }        
    }
});

</script>
