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
		<div class="col-md-8">
			<div class="card1 mb">
				<label style="font-size: 18px;"><em class="fas fa-phone-square" style="font-size: 25px; color: #827DF9;"></em> Datos de la llamada</label>
				<div class="row">
					<div class="col-md-6">
						<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
						<caption>Datos Llamada</caption>
							<tr>
								<th scope="row"><label style="font-size: 15px;"><?php echo "Usuario red:"; ?></label></th>
								<td><label style="font-size: 15px;"><?php echo $varidlogin; ?></label></td>
							</tr>
							<tr>
								<th scope="row"><label style="font-size: 15px;"><?php echo "Redbox:"; ?></label></th>
								<td><label style="font-size: 15px;"><?php echo $varidredbox; ?></label></td>
							</tr>
							<tr>
								<th scope="row"><label style="font-size: 15px;"><?php echo "Grabadora:"; ?></label></th>
								<td><label style="font-size: 15px;"><?php echo $varidgrabadora; ?></label></td>
							</tr>
							<tr>
								<td><label style="font-size: 15px;"><?php echo "Extension:"; ?></label></td>
								<td><label style="font-size: 15px;"><?php echo $varextensionnum; ?></label></td>
							</tr>
						</table>
					</div>
					<div class="col-md-6">						
						<div class="row">
							<div class="col-md-12" style="text-align: center;">
	  							<div onclick="generated();" class="btn btn-primary" style="display:inline;background-color: #c5cbd0;color: black;" method='post' id="botones2" ><em class="fas fa-play-circle" style="font-size: 17px; color: #ff3838;"></em>
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
		<div class="col-md-4">
			<div class="row">
				<div class="col-md-12">
					<div class="card1 mb">
						<label style="font-size: 18px;"><em class="fas fa-key" style="font-size: 25px; color: #827DF9;"></em> Instructivo de uso</label>
						<div class="row">
							<div class="col-md-12" style="text-align: center;">
								<em class="fas fa-download" style="font-size: 60px; color: #ceabab; align-self: center;"></em>
							
								<a style="font-size: 13px;" rel="stylesheet" type="text/css" href="/qa_managementv2/web/downloadfiles/Manual Configuración Quantify Esp Version 2_Febrero 2018.pdf" target="_blank">Descargar Instructivo</a>
							</div>
						</div>
					</div>
					<br>
					<div class="card1 mb">
						<input type="text" class="js-copytextarea form-control" readonly="readonly" value="<?php echo $varResultado; ?>">
					</div>
				</div>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="row">
				<div class="col-md-8">
					<div class="card1 mb">
						<label style="font-size: 18px;"><em class="fas fa-file-alt" style="font-size: 25px; color: #827DF9;"></em> Transcrici�n</label>
						<label style="font-size: 15px;"><?php echo $vartexto; ?></label>
					</div>
				</div>
				<div class="col-md-4">
					<div class="card1 mb">
						<label style="font-size: 18px;"><em class="fas fa-smile" style="font-size: 25px; color: #827DF9;"></em> Valencia emocional</label>
						<label style="font-size: 15px;"><?php echo $varvalencia; ?></label>
					</div>
				</div>
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