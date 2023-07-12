<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

?>

<div id="idCapaUno" style="display: inline">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
         <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 16px;"><em class="fas fa-bolt" style="font-size: 20px; color: #4D83FE;"></em> Ingresar comentarios: </label>
                    
                    <?= $form->field($model_entrada_feedback, "comentario", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'comentarios_feedback', 'maxlength' => true]) ?>
                    
                    <?= Html::submitButton(Yii::t('app', 'Enviar'),
                                    ['class' => 'btn btn-success',                
                                    'data-toggle' => 'tooltip',
                                    'id'=>'ButtonSearch',
                                    'style' => 'display: inline; margin-bottom: 10px; margin-top: 20px;']) 
                    ?>
                </div>                
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>