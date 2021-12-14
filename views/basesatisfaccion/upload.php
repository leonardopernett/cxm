<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\base\model;
?>

<?= $msg ?>

<h3>Subir archivos</h3>

<?php $form = ActiveForm::begin([
     "method" => "post",
     "enableClientValidation" => true,
     "options" => ["enctype" => "multipart/form-data"],
     'fieldConfig' => [
          'inputOptions' => ['autocomplete' => 'off']
     ]
     ]);
?>

<?= $form->field($model, "file[]")->fileInput(['multiple' => false]) ?>

<?= Html::submitButton("Subir", ["class" => "btn btn-primary"]) ?>

<?php $form->end() ?>