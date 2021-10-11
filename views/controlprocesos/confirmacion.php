<?php
    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\helpers\Url;
    use yii\bootstrap\ActiveForm;
    use kartik\select2\Select2;
    use yii\web\JsExpression;
    use kartik\daterange\DateRangePicker;
    use kartik\export\ExportMenu;
    use yii\bootstrap\modal;
?>
<script>
function cerrarVentana(){ 
	$(this).dialog();
} 
</script>

<div class="formularios-form">

	<div>
		<h4>¿Seguro que desea salir sin guardar el registro?</h4>
	</div>
	<div>
	    <a class="btn btn-default soloCancelar" style="background-color:#337ab7" data-toggle="tooltip" title="Aceptar" href="../controlprocesos/index"> Aceptar </a>	
<!-- 	   	<a class="btn btn-default soloCancelar" style="background-color:#707372" data-toggle="tooltip" title="Cerrar" onclick="CerrarVentana()" > Cancelar </a>		 -->
	</div>

</div>