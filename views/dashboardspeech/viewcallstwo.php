<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$sesiones =Yii::$app->user->identity->id;   

?>
<?php
if ($varidredbox != "" && $varidgrabadora != "") {
?>
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<label style="font-size: 18px;"><i class="fas fa-phone-square" style="font-size: 25px; color: #827DF9;"></i> Datos de la llamada</label>
				<div class="row">
					<div class="col-md-6">
						<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
							<tr>
								<td><label style="font-size: 15px;"><?php echo "Usuario red:"; ?></label></td>
								<td><label style="font-size: 15px;"><?php echo $varidlogin; ?></label></td>
							</tr>
							<tr>
								<td><label style="font-size: 15px;"><?php echo "Redbox:"; ?></label></td>
								<td><label style="font-size: 15px;"><?php echo $varidredbox; ?></label></td>
							</tr>
							<tr>
								<td><label style="font-size: 15px;"><?php echo "Grabadora:"; ?></label></td>
								<td><label style="font-size: 15px;"><?php echo $varidgrabadora; ?></label></td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">						
						<div class="row">
							<div class="col-md-12" align="center">
	  							<div onclick="generated();" class="btn btn-primary" style="display:inline;background-color: #c5cbd0;color: black;" method='post' id="botones2" ><i class="fas fa-play-circle" style="font-size: 17px; color: #ff3838;"></i>
				                  Copiar Url Llamada
				                </div> 
							</div>
						</div>		
						<br>
						<div class="row">
							<div class="col-md-12">
								<label style="font-size: 12px;"><?php echo "Nota: Para escuchar la llamada, debes pegar la url en el navegador de Internet Explorer."; ?></label>
							</div>
						</div>				
					</div>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<label style="font-size: 18px;"><i class="fas fa-key" style="font-size: 25px; color: #827DF9;"></i> Instructivo de uso</label>
				<div class="row">
					<div class="col-md-12">
						<label style="font-size: 15px;"><?php echo "Es importante indicar que antes de realizar cualquier acción, revisar el archivo del instructivo para que la llamada funcione en internet explorer."; ?></label>
					</div>
					<div class="col-md-12">
						<a style="font-size: 15px;" rel="stylesheet" type="text/css" href="https://qa.grupokonecta.local/qa_managementv2/web/downloadfiles/Manual Configuración Quantify Esp Version 2_Febrero 2018.pdf" target="_blank">Descargar Instructivo</a>
					</div>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<input type="text" class="js-copytextarea form-control" readonly="readonly" value="<?php echo $varResultado; ?>">
			</div>
		</div>
	</div>
<?php
}else{
?>
<div class="cappapp" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<label style="font-size: 15px;"><?php echo "Esta llamada no tienen el numero de la grabadora."; ?></label>
			</div>
		</div>
	</div>
</div>
<?php
}
?>
<script type="text/javascript">
function generated(){
	  var copyTextarea = document.querySelector('.js-copytextarea');
	  copyTextarea.select();

	  try {
	    var successful = document.execCommand('copy');
	    var msg = successful ? 'successful' : 'unsuccessful';
	    console.log('Copying text command was ' + msg);
	  } catch (err) {
	    console.log('Oops, unable to copy');
	  }
};

</script>