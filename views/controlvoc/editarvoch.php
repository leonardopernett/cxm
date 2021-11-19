<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

$this->title = 'Instrumento Escucha Focalizada - VOC -';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;
    

?>
<div class="control-procesos-index" id="actualizarID">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
	<?= $form->field($model, "idlistahijovoc")->textInput(['readonly' => 'readonly', 'value' => $txtIdList, 'id'=>'invisible_valorado', 'class'=>"hidden"])->label(' ') ?>   

	<?= $form->field($model, "nombrelistah")->textInput(['value' => $txtNombreList, 'id'=>'idListtxt'])->label('Nombre Lista') ?>   

	<div style="text-align: center;"> 
        <div style="display:inline;" method='post' id="botones1">
            <?= Html::submitButton('Guardar Actualizacion', ['class' => 'btn btn-primary', 'id'=>'btn_submit'] ) ?>
        </div>  
    </div>
<?php $form->end() ?>
</div>
