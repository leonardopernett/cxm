<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Parametrización Encuestas';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;


?>
<div class="formularios-form" id="capaUno" style="display: inline">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]) ?>

        <?= $form->field($model, 'file')->fileInput()->label('') ?>

        <br>

        <button class="form-control", style="width:25%; background: #4298B4; color: #fffcfc;" id="buttonID" onclick="cargar();">Importar</button>

    <?php ActiveForm::end() ?>
</div>
