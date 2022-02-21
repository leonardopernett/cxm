<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\logeventsadminSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logeventsadmin-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
    ]); ?>

    <?= $form->field($model, 'id_log') ?>

    <?= $form->field($model, 'tabla_modificada') ?>

    <?= $form->field($model, 'datos_ant') ?>

    <?= $form->field($model, 'datos_nuevos') ?>

    <?= $form->field($model, 'fecha_modificacion') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
