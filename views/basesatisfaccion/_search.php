<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BaseSatisfaccionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="base-satisfaccion-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'identificacion') ?>

    <?= $form->field($model, 'nombre') ?>

    <?= $form->field($model, 'ani') ?>

    <?= $form->field($model, 'agente') ?>

    <?php // echo $form->field($model, 'agente2') ?>

    <?php // echo $form->field($model, 'ano') ?>

    <?php // echo $form->field($model, 'mes') ?>

    <?php // echo $form->field($model, 'dia') ?>

    <?php // echo $form->field($model, 'hora') ?>

    <?php // echo $form->field($model, 'chat_transfer') ?>

    <?php // echo $form->field($model, 'ext') ?>

    <?php // echo $form->field($model, 'rn') ?>

    <?php // echo $form->field($model, 'industria') ?>

    <?php // echo $form->field($model, 'institucion') ?>

    <?php // echo $form->field($model, 'pcrc') ?>

    <?php // echo $form->field($model, 'cliente') ?>

    <?php // echo $form->field($model, 'tipo_servicio') ?>

    <?php // echo $form->field($model, 'pregunta1') ?>

    <?php // echo $form->field($model, 'pregunta2') ?>

    <?php // echo $form->field($model, 'pregunta3') ?>

    <?php // echo $form->field($model, 'pregunta4') ?>

    <?php // echo $form->field($model, 'pregunta5') ?>

    <?php // echo $form->field($model, 'pregunta6') ?>

    <?php // echo $form->field($model, 'pregunta7') ?>

    <?php // echo $form->field($model, 'pregunta8') ?>

    <?php // echo $form->field($model, 'pregunta9') ?>

    <?php // echo $form->field($model, 'connid') ?>

    <?php // echo $form->field($model, 'tipo_encuesta') ?>

    <?php // echo $form->field($model, 'comentario') ?>

    <?php // echo $form->field($model, 'lider_equipo') ?>

    <?php // echo $form->field($model, 'coordinador') ?>

    <?php // echo $form->field($model, 'jefe_operaciones') ?>

    <?php // echo $form->field($model, 'tipologia') ?>

    <?php // echo $form->field($model, 'estado') ?>

    <?php // echo $form->field($model, 'llamada') ?>

    <?php // echo $form->field($model, 'buzon') ?>

    <?php // echo $form->field($model, 'responsable') ?>

    <?php // echo $form->field($model, 'usado') ?>

    <?php // echo $form->field($model, 'fecha_gestion') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
