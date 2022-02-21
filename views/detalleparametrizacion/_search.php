<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DetalleparametrizacionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="detalleparametrizacion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name_parametrizacion') ?>

    <?= $form->field($model, 'id_parametrizacion') ?>

    <?= $form->field($model, 'satisfaccion_general_cal') ?>

    <?= $form->field($model, 'tiempo_espera_cal') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
