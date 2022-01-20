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

				<label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em> Lista de Datos</label>

                    <table id="tblDatas" class="table table-striped table-bordered tblResDetFreed">
                        <caption>...</caption>
                        <thead>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "ID Llamada"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Extensiones"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "ID Encuesta"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Connid"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Buzones"; ?></label></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo $idcallids; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo $varextension; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo $varencuestaid; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo $varconnid; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo $varbuzones; ?></label></td>
                            </tr>
                        </tbody>
                    </table>

			</div>	
		</div>
	</div>
</div>
<hr>
