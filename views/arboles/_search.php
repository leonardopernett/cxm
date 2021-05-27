<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ArbolesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="arboles-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'name') ?>

    <?= $form->field($model, 'arbol_id') ?>

    <?= $form->field($model, 'snhoja') ?>

    <?= $form->field($model, 'formulario_id') ?>

    <?php // echo $form->field($model, 'nmfactor_proceso') ?>

    <?php // echo $form->field($model, 'nmumbral_verde') ?>

    <?php // echo $form->field($model, 'nmumbral_amarillo') ?>

    <?php // echo $form->field($model, 'nmumbral_positivo') ?>

    <?php // echo $form->field($model, 'usua_id_responsable') ?>

    <?php // echo $form->field($model, 'dsorden') ?>

    <?php // echo $form->field($model, 'tableroproblema_id') ?>

    <?php // echo $form->field($model, 'tiposllamada_id') ?>

    <?php // echo $form->field($model, 'dsname_full') ?>

    <?php // echo $form->field($model, 'bloquedetalle_id') ?>

    <?php // echo $form->field($model, 'snactivar_problemas') ?>

    <?php // echo $form->field($model, 'snactivar_tipo_llamada') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
