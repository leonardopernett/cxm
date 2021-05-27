<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tiposllamadasdetalles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tiposllamadasdetalles-form">

    <?php yii\widgets\Pjax::begin(['id' => 'form_Tiposllamadasdetalles']); ?>

    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true]]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>    
    
    <?= $form->field($model, 'tipollamadaName')->textInput(['value'=>$model->getTiposllamada()->one()->name, 'disabled'=>true]) ?>
    
    <?= $form->field($model, 'tiposllamada_id')->hiddenInput(['value' => $model->tiposllamada_id])->label('') ?>
    
    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app',
                                    'Update'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'),
                    ['index', 'tiposllamada_id' => $model->tiposllamada_id],
                    ['class' => 'btn btn-default'])
            ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>
</div>
