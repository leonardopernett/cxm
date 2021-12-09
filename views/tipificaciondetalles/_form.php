<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tipificaciondetalles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tipificaciondetalles-form">

    <?php yii\widgets\Pjax::begin(['id' => 'form_Tipificaciondetalles']); ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 250]) ?>    

    <?= $form->field($model, 'subtipificacion_id')->dropDownList($model->getSubTipificacionList(), ["prompt" => Yii::t("app", "Ninguno")]) ?>

    <?= $form->field($model, 'nmorden')->textInput() ?>

    <?= $form->field($model, 'snen_uso')->checkbox() ?>
    
    <?= $form->field($model, 'tipificacionName')->textInput(['value'=>$model->getTipificacion()->one()->name, 'disabled'=>true]) ?>
    
    <?= $form->field($model, 'tipificacion_id')->hiddenInput(['value' => $model->tipificacion_id])->label('') ?>
    
    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app',
                                    'Update'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'),
                    ['index', 'tipificacion_id' => $model->tipificacion_id],
                    ['class' => 'btn btn-default'])
            ?>
        </div>        
    </div>
    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>
</div>
