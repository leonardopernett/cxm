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

?>
<div id="capaUno" style="display: inline">   
    <div class="row">
    	<div class="col-md-6">
    		<a id="dlink" style="display:none;"></a>
    		<button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar</button>
    	</div>
    </div>
</div>
<div id="capaDos" style="display: none">   
    <div class="row">
    	<div class="col-md-12">
    		<br>
    		<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
    			<thead>
    				<tr>
    					<th colspan="6" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Formato para distribuciÃ³n de personal') ?></label></th>
    				</tr>
    				<tr>
    					<th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Identificacion Valorado') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Valorado') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Centro de costos') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Usuario red valorado') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Identificacion Lider') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Usario de red Lider') ?></label></th>   					
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Lider') ?></label></th>
    				</tr>
    			</thead>
    			<tbody>
    				
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
            document.getElementById("dlink").download = "Formato actualizar manualmente valorados";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Formato valorados', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>