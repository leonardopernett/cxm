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

    $query2 = Yii::$app->db->createCommand("select nombreencuesta, idlimeencuesta from tbl_control_encuestas where anulado = 0")->queryAll();

    $listData2 = ArrayHelper::map($query2, 'idlimeencuesta', 'nombreencuesta');

?>
<div class="formularios-form" id="capaUno" style="display: inline">
    <?php $form = ActiveForm::begin([
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ],
        'options' => ['enctype' => 'multipart/form-data']
        ]) ?>

    	<?php  echo $form->field($model2, 'idlimeencuesta')->dropDownList($listData2, ['prompt' => 'Seleccionar...', 'id'=>'TipoArbol'])->label('Seleccionar encuesta') ?> 

        <?= $form->field($model, 'file')->fileInput()->label('') ?>

        <br>

        <button class="form-control", style="width:25%; background: #4298B4;" id="buttonID" onclick="cargar();">Importar</button>

    <?php ActiveForm::end() ?>
</div>
