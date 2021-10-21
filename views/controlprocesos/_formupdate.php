<?php

use yii\helpers\Html;
/*use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use \app\models\ControlProcesos;

$textualizados = $model->dimensions;

$this->title = 'Actualizar Valoracion';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

?>

<br>
<div class="page-header" >
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
</div> 

<?php
	if ($textualizados == "PROCESO") {
?>
	<div class="formularios-form">
		<div align="center">
			<h5>Por favor seleccionar un argumento del porque se genera la actualizacion de la valoracion.</h5>
		</div>
		&nbsp;&nbsp;
		<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

		<?= $form->field($model, "cant_valor")->textInput(['type' => 'number', 'maxlength' => 10, 'id'=>'cantidad_valoraciones', 'onkeypress'=>"return soloNumeros(event);", 'required'])->label('Cantidad de Valoraciones') ?> 	

		<?php $var = ['Comparte varios PCRC' => 'Comparte varios PCRC', 'Incapacidad' => 'Incapacidad', 'Distribucion muestra en OJT y calidad Entto' => 'Distribucion muestra en OJT y calidad Entto', 'Varios t�cnicos con el mismo PCRC' => 'Varios tecnicos con el mismo PCRC', 'Licencia/Vacaciones' => 'Licencia/Vacaciones', 'En otras actividades' => 'En otras actividades', 'Suspension' => 'Suspension', 'Calibraciones Incognitas' => 'Calibraciones Incognitas', 'Conexion Tutor' => 'Conexion Tutor', 'Se realiza valoraci�n agente' => 'Se realiza valoracion agente', 'Servicio nuevo' => 'Servicio nuevo']; ?>
		
		<?= $form->field($model, "argumentos")->dropDownList($var, ['prompt' => 'Seleccione una opcion', 'id'=>"id_argumentos"]) ?>   				
	</div>
<br>
	<div align="center">  
	
		<?= Html::submitButton('Actualizar', ['class' => 'btn btn-primary', 'id'=>'btn_submit'] ) ?>
	
		&nbsp;&nbsp;
		<?= Html::a('Regresar',  ['create','usua_id' => $txtvarevaluadoId], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>
	</div>
<?php
	}
	else
	{
?>
	<div class="formularios-form">
		<div align="center">
			<h5>Por favor seleccionar un argumento del porque se genera la actualizaci�n de la valoraci�n.</h5>
		</div>
		&nbsp;&nbsp;
		<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

		<?= $form->field($model, "cant_valor")->textInput(['type' => 'number', 'maxlength' => 10, 'id'=>'cantidad_valoraciones', 'onkeypress'=>"return soloNumeros(event);", 'required'])->label('Cantidad de Valoraciones') ?> 	  				
	</div>
<br>
	<div align="center">  
	
		<?= Html::submitButton('Actualizar', ['class' => 'btn btn-primary'] ) ?>
	
		&nbsp;&nbsp;
		<?= Html::a('Regresar',  ['create','usua_id' => $txtvarevaluadoId], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>
	</div>
<?php
	}
?>

	<?php ActiveForm::end(); ?>
<br>
	<div align="center">
		<h6><strong><p style="color:red;"> Advertencia  </p></strong></h6>
		<h7><strong>Solo es posible hacer la actualizacion sobre la cantidad de valoraci�n modificada si no tiene un argumento valido.</strong></h7>
	</div>

<script type="text/javascript">

	var btn_submit = document.getElementById("btn_submit");
	btn_submit.addEventListener("click",function(e){

		e.preventDefault();
		
		var textosss
		var cantidad_valoraciones = document.getElementById("cantidad_valoraciones").value;
		var id_argumentos = document.getElementById("id_argumentos").value;	

			if(cantidad_valoraciones == "" || cantidad_valoraciones == null || cantidad_valoraciones == undefined){
				alert("El campo Cantidad de Valoraciones no puede estar vacio");
				return;		
			}

			if(id_argumentos == "" || id_argumentos == null || id_argumentos == undefined){
				alert("El campo Argumentos no puede estar vacio");
				return;
			}

			document.getElementById("w0").submit();	

	});

	function soloNumeros(e)
	{
		var keynum = window.event ? window.event.keyCode : e.which;
		if ((keynum == 8) || (keynum == 46))
		return true;
		return /\d/.test(String.fromCharCode(keynum));
	};

</script>