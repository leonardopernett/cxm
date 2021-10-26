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

$txttitulo = "-----";
?>
<div class="capaCero" style="display: inline">
    <a id="dlink" style="display:none;"></a>
    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar a Excel</button>
</div>
<div class="capaUno" style="display: none;">
	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
	<caption>Exportar</caption>
		<thead>			
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Cliente') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Formulario VOC') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Nombre valorador') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Nombre valorado') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Id Speech') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Fecha/Hora') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Usuario') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Extensión') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Duración') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Dimensión') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', '-----') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Indicador') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Variable') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Atributo de calidad') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Motivos de contacto') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Detalles motivo contacto') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Punto de dolor') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Llamada categorizada') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Procentaje de indicador') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Agente (Detalle de Responsabilidad)') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Marca (Detalle de Responsabilidad)') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Canal (Detalle de Responsabilidad)') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Mapa de interesados 1') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Mapa de interesados 2') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Mapa de interesados 3') ?></th>
			<th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Detalle cualitativo') ?></th>
		</thead>
		<tbody>
			<tr>
				<td class="text-center"><?php echo $txtNombreArbol; ?></td>
				<td class="text-center"><?php echo $txtServicio; ?></td>
				<td class="text-center"><?php echo $txtNombreValorador; ?></td>
				<td class="text-center"><?php echo $txtNombreValorado; ?></td>
				<td class="text-center"><?php echo $txtSpeech; ?></td>
				<td class="text-center"><?php echo $txtFechaHor; ?></td>
				<td class="text-center"><?php echo $txtUsers; ?></td>
				<td class="text-center"><?php echo $txtextension; ?></td>
				<td class="text-center"><?php echo $txtDuraciones.' Segundos'; ?></td>
				<td class="text-center"><?php echo $txtDimensiones; ?></td>				
				<td class="text-center" style="background-color: #4A7EC0; color: #fff"><?php echo $txttitulo; ?></td>
				<td class="text-center"><?php echo $txtIndicador; ?></td>	
				<td class="text-center"><?php echo $txtVariable; ?></td>	
				<td class="text-center"><?php echo $txtatributos; ?></td>	
				<td class="text-center"><?php echo $txtMotivos; ?></td>	
				<td class="text-center"><?php echo $txtDetalles; ?></td>	
				<td class="text-center"><?php echo $txtatributos; ?></td>	
				<td class="text-center"><?php echo $txtCategoria; ?></td>	
				<td class="text-center"><?php echo $txtPorcentaje.'%'; ?></td>	
				<td class="text-center"><?php echo $txtAgente; ?></td>	
				<td class="text-center"><?php echo $txtMarca; ?></td>	
				<td class="text-center"><?php echo $txtCanal; ?></td>	
				<td class="text-center"><?php echo $txtMapa1; ?></td>	
				<td class="text-center"><?php echo $txtMapa2; ?></td>	
				<td class="text-center"><?php echo $txtMapa3; ?></td>	
				<td class="text-center"><?php echo $txtDetalleCuali; ?></td>	
			</tr>
		</tbody>
	</table>
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
            document.getElementById("dlink").download = "DashBoard Formulario Voc";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo Voc', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>