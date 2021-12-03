<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ControlProcesosEquipos */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="control-procesos-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'usua_id') ?>

    <?= $form->field($model, 'usua_nombre') ?>

    <?= $form->field($model, 'cant_valor') ?>

    <?= $form->field($model, 'salario') ?>

    <?php // echo $form->field($model, 'tipo_corte') ?>

    <?php // echo $form->field($model, 'responsable') ?>

    <?php // echo $form->field($model, 'dimensions') ?>

    <?php // echo $form->field($model, 'arbol_id') ?>

    <?php // echo $form->field($model, 'Dedic_valora') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
