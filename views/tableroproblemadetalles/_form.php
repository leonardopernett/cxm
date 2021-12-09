<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tableroproblemadetalles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tableroproblemadetalles-form">

    <?php yii\widgets\Pjax::begin(['id' => 'form_Tableroproblemadetalles']); ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'tableroenfoque_id')->dropDownList($model->getEnfoqueList()) ?>
    
    <?= $form->field($model, 'problemaName')->textInput(['value'=>$model->getTableroproblema()->one()->name, 'disabled'=>true]) ?>

    <?= $form->field($model, 'tableroproblema_id')->hiddenInput(['value' => $model->tableroproblema_id])->label('') ?>
    
    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app',
                                    'Create') : Yii::t('app', 'Update'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'),
                    ['index',
                'tableroproblema_id' => $model->tableroproblema_id],
                    ['class' => 'btn btn-default'])
            ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>
</div>
