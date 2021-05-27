<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TipofeedbacksSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tipofeedbacks-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'categoriafeedback_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'snaccion_correctiva') ?>

    <?= $form->field($model, 'sncausa_raiz') ?>

    <?php // echo $form->field($model, 'sncompromiso') ?>

    <?php // echo $form->field($model, 'cdtipo_automatico') ?>

    <?php // echo $form->field($model, 'dsmensaje_auto') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
