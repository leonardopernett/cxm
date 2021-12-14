<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;


/* @var $this yii\web\View */
/* @var $model app\models\DeclinacionesUsuarios */
/* @var $form yii\widgets\ActiveForm */
$this->registerJs(
   "
       $('#form-Declinaciones-Usuarios').on('beforeSubmit', function(event, jqXHR, settings) {
         $('#modal-Declinaciones-Usuarios').showLoading();   
         var form = $(this);
                if(form.find('.has-error').length) {
                $('#modal-Declinaciones-Usuarios').hideLoading();
                        return false;
                }
       });
       
   "
);
?>

<div class="declinaciones-usuarios-form">
    
    <?php
        Pjax::begin(['id' => 'Declinaciones-Usuarios-pj', 'enablePushState' => false]);
    ?>
    
    <?php $form = ActiveForm::begin([
        'id'=>'form-Declinaciones-Usuarios',
        'enableClientValidation' => true,
        'enableAjaxValidation' => false,
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    
    <?= $form->field($model, 'declinacion_id')->dropDownList($model->getDeclinacionesActiveList(), ['prompt' => Yii::t('app', 'Select ...')]) ?>
    
    <?= $form->field($model, 'comentario')->textarea(['maxlength' => 500]) ?>
    
    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Guardar') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Cancelar'), ['index'] , ['class' => 'btn btn-default', 'data-dismiss'=>'modal']) ?>
        </div>        
    </div>
    
    <div style="height: 8px">
    <?= $form->field($model, 'usua_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'url')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'fecha')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'arbol_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'dimension_id')->hiddenInput()->label(false) ?>

    <?= $form->field($model, 'evaluado_id')->hiddenInput()->label(false) ?>
        
    <?= $form->field($model, 'formulario_id')->hiddenInput()->label(false) ?>
    </div>
    
    

    <?php ActiveForm::end(); ?>
    <?php Pjax::end(); ?>
</div>
