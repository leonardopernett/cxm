<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\EquiposEvaluados */
/* @var $form yii\widgets\ActiveForm */
?>

<?php 
// The controller action that will render the list
$url = \yii\helpers\Url::to(['evaluadolist', 'equipo_id'=>$model->equipo_id]);

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
        
    <?= $form->field($model, 'evaluado_id')->widget(Select2::classname(), [        
        'language' => 'es',
        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
        'pluginOptions' => [
            'multiple'=>true,
            'allowClear' => false,
            'minimumInputLength' => 4,
            'ajax' => [
                'url' => $url,
                'dataType' => 'json',
                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
            ],
        ]
        ]
            );?>
    

    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Agregar') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>        
    </div>

    <?= $form->field($model, 'equipo_id')->hiddenInput()->label('') ?>
    
    <?php ActiveForm::end(); ?>    
</div>
