<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TmpejecucionfeedbacksSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tmpejecucionfeedbacks-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tipofeedback_id') ?>

    <?= $form->field($model, 'tmpejecucionformulario_id') ?>

    <?= $form->field($model, 'usua_id') ?>

    <?= $form->field($model, 'created') ?>
    
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
