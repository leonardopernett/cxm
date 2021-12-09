<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\UsuariosSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="usuarios-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
    ]); ?>

    <?= $form->field($model, 'usua_id') ?>

    <?= $form->field($model, 'usua_usuario') ?>

    <?= $form->field($model, 'usua_nombre') ?>

    <?= $form->field($model, 'usua_email') ?>

    <?= $form->field($model, 'usua_identificacion') ?>

    <?php // echo $form->field($model, 'usua_activo') ?>

    <?php // echo $form->field($model, 'usua_estado') ?>

    <?php // echo $form->field($model, 'usua_fechhoratimeout') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
