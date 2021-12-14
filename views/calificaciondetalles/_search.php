<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CalificaciondetallesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calificaciondetalles-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'sndespliega_tipificaciones') ?>

    <?= $form->field($model, 'calificacion_id') ?>

    <?= $form->field($model, 'nmorden') ?>

    <?php // echo $form->field($model, 'i1_povalor') ?>

    <?php // echo $form->field($model, 'i2_povalor') ?>

    <?php // echo $form->field($model, 'i3_povalor') ?>

    <?php // echo $form->field($model, 'i4_povalor') ?>

    <?php // echo $form->field($model, 'i5_povalor') ?>

    <?php // echo $form->field($model, 'i6_povalor') ?>

    <?php // echo $form->field($model, 'i7_povalor') ?>

    <?php // echo $form->field($model, 'i8_povalor') ?>

    <?php // echo $form->field($model, 'i9_povalor') ?>

    <?php // echo $form->field($model, 'i10_povalor') ?>

    <?php // echo $form->field($model, 'i1_snopcion_na') ?>

    <?php // echo $form->field($model, 'i2_snopcion_na') ?>

    <?php // echo $form->field($model, 'i3_snopcion_na') ?>

    <?php // echo $form->field($model, 'i4_snopcion_na') ?>

    <?php // echo $form->field($model, 'i5_snopcion_na') ?>

    <?php // echo $form->field($model, 'i6_snopcion_na') ?>

    <?php // echo $form->field($model, 'i7_snopcion_na') ?>

    <?php // echo $form->field($model, 'i8_snopcion_na') ?>

    <?php // echo $form->field($model, 'i9_snopcion_na') ?>

    <?php // echo $form->field($model, 'i10_snopcion_na') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
