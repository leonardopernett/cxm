<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\Url;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Reglanegocio */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs(
        "$(document).ready(function(){
            $('#reglanegocio-rn').tagsinput({
                tagClass: function(item) {
                  return 'label label-danger label-important';
                }
            });
            $('#promotores').tagsinput({
                tagClass: function(item) {
                  return 'label label-danger label-important';
                }
            });
            $('#neutros').tagsinput({
                tagClass: function(item) {
                  return 'label label-danger label-important';
                }
            });
            $('#detractores').tagsinput({
                tagClass: function(item) {
                  return 'label label-danger label-important';
                }
            });
            $('#reglanegocio-cliente').prop('readonly',true); 
            $('#reglanegocio-encu_diarias').prop('readonly',true); 
            
            var valor=$('#tipo_regla').val();
            if(valor!=''){
               $('.field-reglanegocio-promotores').show();
               $('.field-reglanegocio-neutros').show();
               $('.field-reglanegocio-detractores').show();
            }else{
                $('.field-reglanegocio-promotores').hide();
               $('.field-reglanegocio-neutros').hide();
               $('.field-reglanegocio-detractores').hide();
            }
        });
    function changePcrc(varPcrc){
        if(varPcrc != ''){
            $.ajax({
                url: '" . Url::to(['padreclientedato']) . "',
                type:'POST',
                dataType: 'json',
                data: {
                    'pcrc' : varPcrc
                },
                success : function(objRes){
                    $('#reglanegocio-cliente').prop('value',objRes.value);
                }
            });
        }else{
            $('#reglanegocio-cliente').prop('value','');
        }
    }
    
    $(document).ready(function(){
         $('#tipo_regla').change(function(){
            var valor=$('#tipo_regla').val();
            if(valor!=2){
                if(valor==1){
                   $('#promotores').tagsinput('removeAll');
                   $('#neutros').tagsinput('removeAll');
                   $('#detractores').tagsinput('removeAll');
                   $('#promotores').tagsinput('add','9,8'); 
                   $('#neutros').tagsinput('add','7,6,5');
                   $('#detractores').tagsinput('add','4,3,2,1,0');
                }else if(valor==3){
                   $('#promotores').tagsinput('removeAll');
                   $('#neutros').tagsinput('removeAll');
                   $('#detractores').tagsinput('removeAll');                
                   $('#promotores').tagsinput('add','5'); 
                   $('#neutros').tagsinput('add','4,3,30');
                   $('#detractores').tagsinput('add','2,1');
                }else{
                $('#promotores').tagsinput('removeAll');
                   $('#neutros').tagsinput('removeAll');
                   $('#detractores').tagsinput('removeAll');                
                   $('#promotores').tagsinput('add', '9,10'); 
                   $('#neutros').tagsinput('add','7,8');
                   $('#detractores').tagsinput('add','0,1,2,3,4,5,6');
                }
               $('.field-reglanegocio-promotores').show();
               $('.field-reglanegocio-neutros').show();
               $('.field-reglanegocio-detractores').show();
            }else{
               $('.field-reglanegocio-promotores').hide();
               $('.field-reglanegocio-neutros').hide();
               $('.field-reglanegocio-detractores').hide();
            }
        });
    });
"
);
?>

<div class="reglanegocio-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'rn')->textInput(['maxlength' => 255]) ?>

    <?php
    $template = '<label for="pcrc" class="control-label col-sm-3">{label}</label><div class="col-sm-6">'
            . ' {input}{error}{hint}</div>';
    ?>
    <?=
            $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
            ->widget(Select2::classname(), [
                //'data' => array_merge(["" => ""], $data),
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['getarbolehoja']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();                                
                                if (id !== "") {
                                    $.ajax({
                                        url: "' . Url::to(['padreclientedato']) . '",
                                        type:"POST",
                                        dataType: "json",
                                        data: {
                                            "pcrc" : id
                                        },
                                        success : function(objRes){
                                            $("#reglanegocio-cliente").prop("value",objRes.value);
                                        }
                                    });
                                    $.ajax("' . Url::to(['getarbolehoja']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post",
                                    }).done(function(data) {
                                        callback(data.results[0]); 
                                         
                                    });
                                }
                            }')
                ],
                'pluginEvents' => [
                    "change" => "function() { changePcrc($(this).val()); }",
                ]
                    ]
    );
    ?>

    <?= $form->field($model, 'cliente')->textInput() ?>

    <?= $form->field($model, 'tipo_regla')->dropDownList(['1' => 'Recomendación de 0/9', '2' => 'Recomendación de Si/No', '3' => 'Recomendación de 1/5', '4' => 'Recomendación de 0/10',], ['id' => 'tipo_regla', 'prompt' => 'Seleccione ...']) ?>
    <?= $form->field($model, 'cod_industria')->textInput() ?>
    <?= $form->field($model, 'cod_institucion')->textInput() ?>
    <?php if (!isset($model->id)): ?>
    <?= $form->field($model, 'promotores', ['options' => ['style' => 'display:none', 'class' => 'form-group']])->textInput(['maxlength' => 255, 'id' => 'promotores', 'value'=>'']) ?>
    <?= $form->field($model, 'neutros', ['options' => ['style' => 'display:none', 'class' => 'form-group']])->textInput(['maxlength' => 255, 'id' => 'neutros', 'value'=>'']) ?>
    <?= $form->field($model, 'detractores', ['options' => ['style' => 'display:none', 'class' => 'form-group']])->textInput(['maxlength' => 255, 'id' => 'detractores', 'value'=>'']) ?>
    <?php else : ?>
    <?= $form->field($model, 'promotores', ['options' => ['style' => 'display:none', 'class' => 'form-group']])->textInput(['maxlength' => 255, 'id' => 'promotores']) ?>
    <?= $form->field($model, 'neutros', ['options' => ['style' => 'display:none', 'class' => 'form-group']])->textInput(['maxlength' => 255, 'id' => 'neutros']) ?>
    <?= $form->field($model, 'detractores', ['options' => ['style' => 'display:none', 'class' => 'form-group']])->textInput(['maxlength' => 255, 'id' => 'detractores']) ?>
    <?php endif; ?>
    <?= $form->field($model, 'encu_diarias')->textInput(); ?>
    <?= $form->field($model, 'encu_mes')->textInput(); ?>
    <?= $form->field($model, 'tramo1')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo2')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo3')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo4')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo5')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo6')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo7')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo8')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo9')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo10')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo11')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo12')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo13')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo14')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo15')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo16')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo17')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo18')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo19')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo20')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo21')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo22')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo23')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'tramo24')->textInput(['style' => 'width:40% !important']); ?>
    <?= $form->field($model, 'correos_notificacion')->textInput(); ?>
    <?=
            $form->field($model, 'id_formulario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
            ->widget(Select2::classname(), [
                //'data' => array_merge(["" => ""], $data),
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['getformularios']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();                                
                                if (id !== "") {                                
                                    $.ajax("' . Url::to(['getformularios']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post",
                                    }).done(function(data) {
                                        callback(data.results[0]);                                          
                                    });
                                }
                            }')
                ]
                    ]
    );
    ?>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
