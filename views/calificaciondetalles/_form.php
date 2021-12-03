<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Calificaciondetalles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="calificaciondetalles-form">

    <?php yii\widgets\Pjax::begin(['id' => 'form_Calificaciondetalles']); ?>

    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => ['data-pjax' => true],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
        ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>
    
    <?= $form->field($model, 'nmorden')->textInput() ?>
    
    <?= $form->field($model, 'calificacionName')->textInput(['value'=>$model->getCalificacion()->one()->name, 'disabled'=>true]) ?>

    <?= $form->field($model, 'sndespliega_tipificaciones')->checkbox() ?>  
    <?= $form->field($model, 'c_pits')->checkbox() ?>              

    <div class="row">
        <div class="col-md-2"></div>
        <div class="col-md-5">
            <?= $form->field($model, 'i1_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>
            
            <?= $form->field($model, 'i2_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>

            <?= $form->field($model, 'i3_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>

            <?= $form->field($model, 'i4_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>

            <?= $form->field($model, 'i5_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>

            <?= $form->field($model, 'i6_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>

            <?= $form->field($model, 'i7_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>

            <?= $form->field($model, 'i8_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>

            <?= $form->field($model, 'i9_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>

            <?= $form->field($model, 'i10_povalor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput() ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'i1_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i2_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i3_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i4_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i5_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i6_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i7_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i8_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i9_snopcion_na')->checkbox() ?>

            <?= $form->field($model, 'i10_snopcion_na')->checkbox() ?>
        </div>
        <div class="col-md-1"></div>
    </div>

    <?= $form->field($model, 'calificacion_id')->hiddenInput(['value' => $model->calificacion_id])->label('') ?>
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
                    ['index', 'calificacion_id' => $model->calificacion_id],
                    ['class' => 'btn btn-default'])
            ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>
</div>
