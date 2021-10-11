<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Roles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="roles-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'role_nombre')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'role_descripcion')->textInput(['maxlength' => 50]) ?>

    <?= $form->field($model, 'per_cuadrodemando')->checkbox() ?>

    <?= $form->field($model, 'per_estadisticaspersonas')->checkbox() ?>

    <?= $form->field($model, 'per_hacermonitoreo')->checkbox() ?>

    <?= $form->field($model, 'per_reportes')->checkbox() ?>

    <?= $form->field($model, 'per_modificarmonitoreo')->checkbox() ?>

    <?= $form->field($model, 'per_adminsistema')->checkbox() ?>

    <?= $form->field($model, 'per_adminprocesos')->checkbox() ?>

    <?= $form->field($model, 'per_editarequiposvalorados')->checkbox() ?>
    
    <?= $form->field($model, 'per_inboxaleatorio')->checkbox() ?>

    <?= $form->field($model, 'per_desempeno')->checkbox() ?>

    <?= $form->field($model, 'per_abogado')->checkbox() ?>

    <?= $form->field($model, 'per_jefeop')->checkbox() ?>

    <?= $form->field($model, 'per_tecdesempeno')->checkbox() ?>

    <?= $form->field($model, 'per_alertas')->checkbox() ?>

    <?= $form->field($model, 'per_evaluacion')->checkbox() ?>

    <?= $form->field($model, 'per_externo')->checkbox() ?>

    <?= $form->field($model, 'per_ba')->checkbox() ?>

    <?= $form->field($model, 'per_directivo')->checkbox() ?>


    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), Yii::$app->session['rolPage'] , ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
