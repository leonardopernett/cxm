<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tipofeedbacks */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tipofeedbacks-form">

    <?php yii\widgets\Pjax::begin(['id' => 'form_Tipofeedbacks']); ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'categoriaFeedName')->textInput(['value'=>$model->getCategoriafeedback()->one()->name, 'disabled'=>true]) ?>
    
    <?= $form->field($model, 'name')->textInput(['id' => 'idnombre','maxlength' => 255]) ?>

    <?= $form->field($model, 'snaccion_correctiva')->checkbox() ?>

    <?= $form->field($model, 'sncausa_raiz')->checkbox() ?>

    <?= $form->field($model, 'sncompromiso')->checkbox() ?>     
    
    
    <?= $form->field($model, 'categoriafeedback_id')->hiddenInput(['value' => $model->categoriafeedback_id])->label('') ?>
    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app',
                                    'Update'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                    'onclick' => 'validacion();'])
            ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'),
                    ['index', 'categoriafeedback_id' => $model->categoriafeedback_id],
                    ['class' => 'btn btn-default'])
            ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>
</div>
<script type="text/javascript">
    function validacion() {

        var varidnombre = document.getElementById("idnombre").value;
       

        if (varidnombre === '') {

            event.preventDefault();
            swal.fire("!!! Warning !!!"," Nombre no puede estar vacío","warning");
            return;
        }else if (varidnombre.length > 255) {

            event.preventDefault();
            swal.fire("!!! Warning !!!","Nombre solo puede contener 0 - 255 caracteres","warning");
            return;
        }
    }
</script>
