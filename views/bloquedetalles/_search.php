<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BloquedetallesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bloquedetalles-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'bloque_id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'calificacion_id') ?>

    <?= $form->field($model, 'tipificacion_id') ?>

    <?php // echo $form->field($model, 'nmorden') ?>

    <?php // echo $form->field($model, 'i1_nmfactor') ?>

    <?php // echo $form->field($model, 'i2_nmfactor') ?>

    <?php // echo $form->field($model, 'i3_nmfactor') ?>

    <?php // echo $form->field($model, 'i4_nmfactor') ?>

    <?php // echo $form->field($model, 'i5_nmfactor') ?>

    <?php // echo $form->field($model, 'i6_nmfactor') ?>

    <?php // echo $form->field($model, 'i7_nmfactor') ?>

    <?php // echo $form->field($model, 'i8_nmfactor') ?>

    <?php // echo $form->field($model, 'i9_nmfactor') ?>

    <?php // echo $form->field($model, 'i10_nmfactor') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
