<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\BaseSatisfaccion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="base-satisfaccion-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'identificacion')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => 200]) ?>

    <?= $form->field($model, 'ani')->textInput(['maxlength' => 200]) ?>

    <?= $form->field($model, 'agente')->textInput(['maxlength' => 200]) ?>

    <?= $form->field($model, 'agente2')->textInput(['maxlength' => 200]) ?>

    <?= $form->field($model, 'ano')->textInput() ?>

    <?= $form->field($model, 'mes')->textInput() ?>

    <?= $form->field($model, 'dia')->textInput() ?>

    <?= $form->field($model, 'hora')->textInput() ?>

    <?= $form->field($model, 'chat_transfer')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'ext')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'rn')->textInput(['maxlength' => 2]) ?>

    <?= $form->field($model, 'industria')->textInput(['maxlength' => 3]) ?>

    <?= $form->field($model, 'institucion')->textInput(['maxlength' => 3]) ?>

    <?= $form->field($model, 'pcrc')->textInput() ?>

    <?= $form->field($model, 'cliente')->textInput() ?>

    <?= $form->field($model, 'tipo_servicio')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'pregunta1')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta2')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta3')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta4')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta5')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta6')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta7')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta8')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta9')->textInput(['maxlength' => 10]) ?>

    <?= $form->field($model, 'connid')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'tipo_encuesta')->textInput(['maxlength' => 4]) ?>

    <?= $form->field($model, 'comentario')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'lider_equipo')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'coordinador')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'jefe_operaciones')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'tipologia')->textInput(['maxlength' => 2]) ?>

    <?= $form->field($model, 'estado')->dropDownList([ 'Abierto' => 'Abierto', 'En Proceso' => 'En Proceso', 'Por Contestar' => 'Por Contestar', 'Cerrado' => 'Cerrado', 'Escalado' => 'Escalado', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'llamada')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'buzon')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'responsable')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'usado')->dropDownList([ 'SI' => 'SI', 'NO' => 'NO', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'fecha_gestion')->textInput() ?>

    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
