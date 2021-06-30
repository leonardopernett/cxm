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
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Gestión Satisfacción Chat';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones =Yii::$app->user->identity->id;  
    $varconteo = 0; 



?>
<div class="capaPP" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<label style="font-size: 15px;"><i class="fas fa-file" style="font-size: 15px; color: #827DF9;"></i> Descargar archivo: </label> 
				<a id="dlink" style="display:none;"></a>
            	<button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar</button>
			</div>
		</div>
	</div>
</div>
<br>
<div class="capaOne" style="display: none;">
	<div class="row">
		<div class="col-md-12">
			<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
				<thead>
					<tr>
						<th colspan="10" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Speech & llamadas Redbox') ?></label></th>
					</tr>
					<tr>
						<th colspan="3" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pcrc seleccionado') ?></label></th>
						<th colspan="3" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rango de fechas') ?></label></th>
						<th colspan="2" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Llamadas general') ?></label></th>
						<th colspan="2" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Llamadas buscadas') ?></label></th>
					</tr>
					<tr>
						<th colspan="3" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txtvarcodigopcrc.' - '.$txtnombrepcrc) ?></label></th>
						<th colspan="3" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txtvarfechainireal.' - '.$txtvarfechafinreal) ?></label></th>
						<th colspan="2" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txtvarcantllamadas) ?></label></th>
						<th colspan="2" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', $txttotalllamadasd) ?></label></th>
					</tr>					
					<tr>
						<th colspan="2" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ID de las llamadas') ?></label></th>
						<th colspan="2" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha y hora real') ?></label></th>
						<th colspan="2" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio') ?></label></th>
						<th colspan="2" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
						<th colspan="2" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id redbox') ?></label></th>
					</tr>
				</thead>
				<tbody>
					<?php
						foreach ($varlistcalls as $key => $value) {
							$txtcallids = $value['callId'];
							$txtfechareals = $value['fechareal'];
							$txtservicios = $value['servicio'];
							$txtlogins = $value['login_id'];
							$txtredobxs = $value['idredbox'];
					?>
						<tr>
							<td colspan="2"><label style="font-size: 12px;"><?php echo  $txtcallids; ?></label></td>
							<td colspan="2"><label style="font-size: 12px;"><?php echo  $txtfechareals; ?></label></td>
							<td colspan="2"><label style="font-size: 12px;"><?php echo  $txtservicios; ?></label></td>
							<td colspan="2"><label style="font-size: 12px;"><?php echo  $txtlogins; ?></label></td>
							<td colspan="2"><label style="font-size: 12px;"><?php echo  $txtredobxs; ?></label></td>
						</tr>
					<?php
						}
					?>
				</tbody>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript" charset="UTF-8">
var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "Speech & Redbox";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo de llamadas Speech', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>