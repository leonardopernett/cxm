<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\DeclinacionesUsuariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="declinaciones-usuarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'url') ?>

    <?= $form->field($model, 'fecha') ?>

    <?= $form->field($model, 'comentario') ?>

    <?= $form->field($model, 'usua_id') ?>

    <?php // echo $form->field($model, 'declinacion_id') ?>

    <?php // echo $form->field($model, 'arbol_id') ?>

    <?php // echo $form->field($model, 'dimension_id') ?>

    <?php // echo $form->field($model, 'evaluado_id') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
