<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Calificaciondetalles */
/* @var $form yii\widgets\ActiveForm */
?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Actualizar Feedback'),
    'id' => 'modal-updateFeedback',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>

<div class="calificaciondetalles-form">    
    <?php
    yii\widgets\Pjax::begin(['id' => 'form_updateFeedback',
        'timeout' => false, 'enablePushState' => false]);
    ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    
    <?=
    $activador[0]["snaccion_correctiva"] != 0 ? $form->field($model, 'dsaccion_correctiva')->textarea() : "";
    ?>
    
    <?=
    $activador[0]["sncausa_raiz"] != 0 ? $form->field($model, 'dscausa_raiz')->textarea() : "";
    ?>                          

    <?=
    $activador[0]["sncompromiso"] != 0 ? $form->field($model, 'dscompromiso')->textarea() : "";
    ?>  

    <?= $form->field($model, 'dscomentario')->textarea(["readonly" => "readonly"]) ?> 

    <?php $var = [0 => 'NO', 1 => 'SI', 2 => 'NA' ]; ?>

    <?= $form->field($model, "snaviso_revisado")->dropDownList($var, ['id'=>"id_argumentos"]) ?>                  

    <?= $form->field($model, 'id')->hiddenInput(['value' => $model->id])->label('') ?>
    <hr>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>            
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>
</div>
<?php Modal::end(); ?> 


