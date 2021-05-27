<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FormulariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="formularios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'nmorden') ?>

    <?= $form->field($model, 'i1_cdtipo_eval') ?>

    <?= $form->field($model, 'i2_cdtipo_eval') ?>

    <?php // echo $form->field($model, 'i3_cdtipo_eval') ?>

    <?php // echo $form->field($model, 'i4_cdtipo_eval') ?>

    <?php // echo $form->field($model, 'i5_cdtipo_eval') ?>

    <?php // echo $form->field($model, 'i6_cdtipo_eval') ?>

    <?php // echo $form->field($model, 'i7_cdtipo_eval') ?>

    <?php // echo $form->field($model, 'i8_cdtipo_eval') ?>

    <?php // echo $form->field($model, 'i9_cdtipo_eval') ?>

    <?php // echo $form->field($model, 'i10_cdtipo_eval') ?>

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
