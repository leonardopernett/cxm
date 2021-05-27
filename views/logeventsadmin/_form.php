<?php

use yii\helpers\Html;
/*use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Logeventsadmin */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="logeventsadmin-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'tabla_modificada')->textInput(['maxlength' => 300]) ?>

    <?= $form->field($model, 'datos_ant')->textInput(['maxlength' => 300]) ?>

    <?= $form->field($model, 'datos_nuevos')->textInput(['maxlength' => 300]) ?>

    <?= $form->field($model, 'fecha_modificacion')->textInput() ?>

    <?= $form->field($model, 'usuario_modificacion')->textInput(['maxlength' => 300]) ?>

    <?= $form->field($model, 'id_usuario_modificacion')->textInput() ?>

    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
