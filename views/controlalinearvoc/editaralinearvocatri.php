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

$this->title = 'Instrumento Alinear + VOC';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;
    

?>
<div class="control-procesos-index" id="actualizarID">
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>
	<?= $form->field($model, "id_atrib_alin")->textInput(['readonly' => 'readonly', 'value' => $txtIdAtri, 'id'=>'invisible_valorado', 'class'=>"hidden"])->label(' ') ?>   
    
        <label for="txtIDExtSp" style="margin-left: 300px;">Sesion - Categoria:</label>
        <input type="text" style="width : 645px; display: block; margin-left: auto; margin-right: auto;" readonly="readonly" value="<?php echo $txtNomSes; ?>" class="form-control" id="txtIDExtSp" >            
    
	<?= $form->field($model, "atributo_nombre")->textInput(['value' => $txtNombreList, 'id'=>'idListtxt'])->label('Nombre Lista') ?>   

	<div style="text-align: center;"> 
        <div style="display:inline;" method='post' id="botones1">
            <?= Html::submitButton('Guardar Actualizacion', ['class' => 'btn btn-primary', 'id'=>'btn_submit'] ) ?>
        </div>  
    </div>
<?php $form->end() ?>
</div>