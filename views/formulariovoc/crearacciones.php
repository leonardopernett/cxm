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

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

?>
<div class="capauno">
	<?php $form = ActiveForm::begin([
		'layout' => 'horizontal',
		'fieldConfig' => [
			'inputOptions' => ['autocomplete' => 'off']
		  ]
		]); ?>
	<div class="row">
		<div class="col-md-12">
			<label style="font-size: 15px;"> Seleccionar acción:</label>
			<?php $var = ['1' => 'Puntos de dolor', '2' => 'Detalle de Responsabilidad', '3' => 'Atributos de calidad', '4' => 'Mapa de interesados']; ?>
            <?= $form->field($model, 'idacciones', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var, ['prompt' => 'Seleccione...', 'id'=>"id_responsabilidad", 'onclick'=>'acciondetalle();'])->label('') ?> 
		</div>
		<div class="col-md-12">
			<label style="font-size: 15px;"> Escribir detalle:</label>
			<?= $form->field($model, 'acciones', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200,  'id'=>'id_acciones']) ?> 
		</div>
		<div class="col-md-12">
			<label style="font-size: 15px;"> Seleccionar detalle de responsabilidad:</label>
			<?php $vard = ['1' => 'Agente', '2' => 'Canal', '3' => 'Marca', '4' => 'No Aplica']; ?>
            <?= $form->field($model, 'iddetalle', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($vard, ['prompt' => 'Seleccione...', 'id'=>"id_detalles"])->label('') ?> 
		</div>
		<div class="col-md-12">
			<?= Html::submitButton('Guardar Acciones', ['class' => 'btn btn-primary', 'id'=>'btn_submit', 'onclick' => 'verificaracciones();'] ) ?>
		</div>
	</div>
	<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
	function verificaracciones(){
		var varid_responsabilidad = document.getElementById("id_responsabilidad").value;
		var varid_acciones = document.getElementById("id_acciones").value;
		var varid_detalles = document.getElementById("id_detalles").value;

		if (varid_responsabilidad == "") {
			event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar una acción.","warning");
            return;
		}else{
			if (varid_acciones == "") {
				event.preventDefault();
	            swal.fire("!!! Advertencia !!!","Debe ingresar el detalle de la acción.","warning");
	            return;
			}else{
				if (varid_detalles == "") {
					event.preventDefault();
		            swal.fire("!!! Advertencia !!!","Debe seleccionar la responsabilidad de la acción.","warning");
		            return;
				}
			}
		}
	};
</script>