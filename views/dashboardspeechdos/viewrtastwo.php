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
<div class="capaUno" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
				<caption>...</caption>
					<tr>
						<th scope="col"><label style="font-size: 15px;"><?php echo "Usuario red"; ?></label></th>
						<th scope="col"><label style="font-size: 15px;"><?php echo "Fecha y Hora real"; ?></label></th>
						<th scope="col"><label style="font-size: 15px;"><?php echo "ID llamada"; ?></label></th>
					</tr>
					<tr>
						<td><label style="font-size: 15px;"><?php echo $txtusuarios; ?></label></td>
						<td><label style="font-size: 15px;"><?php echo $txtvarhoras; ?></label></td>		
						<td><label style="font-size: 15px;"><?php echo $txtvarcallid; ?></label></td>
					</tr>
				</table>
			</div>	
		</div>
	</div>
</div>
<hr>
<div class="capaDos" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
				<caption>...</caption>
					<tr>
						<th scope="col"><label style="font-size: 15px;"><?php echo "Resultado IDA"; ?></label></th>
						<th scope="col"><label style="font-size: 15px;"><?php echo "Calidad y Consistencia"; ?></label></th>
						<th scope="col"><label style="font-size: 15px;"><?php echo "Score Valoracion"; ?></label></th>
					</tr>
					<tr>
						<td><label style="font-size: 15px;"><?php echo $resultadosIDA; ?></label></td>
						<td><label style="font-size: 15px;"><?php echo $txtejecucion; ?></label></td>		
						<td><label style="font-size: 15px;"><?php echo $txtpromediorta; ?></label></td>
					</tr>
				</table>
			</div>	
		</div>
	</div>
</div>