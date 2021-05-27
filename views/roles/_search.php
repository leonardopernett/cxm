<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\RolesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="roles-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'role_id') ?>

    <?= $form->field($model, 'role_nombre') ?>

    <?= $form->field($model, 'role_descripcion') ?>

    <?= $form->field($model, 'per_cuadrodemando') ?>

    <?= $form->field($model, 'per_estadisticaspersonas') ?>

    <?php // echo $form->field($model, 'per_hacermonitoreo') ?>

    <?php // echo $form->field($model, 'per_reportes') ?>

    <?php // echo $form->field($model, 'per_modificarmonitoreo') ?>

    <?php // echo $form->field($model, 'per_adminsistema') ?>

    <?php // echo $form->field($model, 'per_adminprocesos') ?>

    <?php // echo $form->field($model, 'per_editarequiposvalorados') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
