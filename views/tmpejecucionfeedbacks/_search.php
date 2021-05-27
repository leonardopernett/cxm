<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TmpejecucionfeedbacksSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tmpejecucionfeedbacks-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tipofeedback_id') ?>

    <?= $form->field($model, 'tmpejecucionformulario_id') ?>

    <?= $form->field($model, 'usua_id') ?>

    <?= $form->field($model, 'created') ?>

    <?php // echo $form->field($model, 'usua_id_lider') ?>

    <?php // echo $form->field($model, 'evaluado_id') ?>

    <?php // echo $form->field($model, 'snavisar') ?>

    <?php // echo $form->field($model, 'snaviso_revisado') ?>

    <?php // echo $form->field($model, 'dsaccion_correctiva') ?>

    <?php // echo $form->field($model, 'feaccion_correctiva') ?>

    <?php // echo $form->field($model, 'nmescalamiento') ?>

    <?php // echo $form->field($model, 'feescalamiento') ?>

    <?php // echo $form->field($model, 'dscausa_raiz') ?>

    <?php // echo $form->field($model, 'dscompromiso') ?>

    <?php // echo $form->field($model, 'dscomentario') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
