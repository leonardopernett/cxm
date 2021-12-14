<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

// The controller action that will render the list
/*
 *(18-03-2016) Se realiza el ajuste en el cual la tabla de permisos ya no apunta a roles
 * si no que esta asociada a grupos de usuario (sebastian.orozco@ingeneo.com.co)
 */
$url = \yii\helpers\Url::to(['roleslist', 'arbol_id' => $model->arbol_id]);
?>

<div class="equipos-evaluados-form">
    <?php
    $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
    ]);
    ?>

    <?=
    $form->field($model, 'grupousuario_id')->widget(Select2::classname(), [
        'language' => 'es',
        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
        'pluginOptions' => [
            'multiple' => true,
            'allowClear' => false,
            'minimumInputLength' => 4,
            'ajax' => [
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
            ],
        //'initSelection' => new JsExpression($initScript)
        ]
            ]
    );
    ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(
                    $model->isNewRecord ? Yii::t('app', 'Agregar') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>            
        </div>        
    </div>

<?= $form->field($model, 'arbol_id')->hiddenInput()->label('') ?>

<?php ActiveForm::end(); ?>    
</div>
