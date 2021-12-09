<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$variables  = Yii::$app->user->identity->id;

$this->title = Yii::t('app', 'Historico de Alertas');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<script src="../../web/js_extensions/jquery-2.1.3.min.js"></script>
<script type="text/javascript" src="tableExport.js"></script>
<script type="text/javascript" src="jquery.base64.js"></script>
<script type="text/javascript" src="html2canvas.js"></script>
<script type="text/javascript" src="jspdf/libs/sprintf.js"></script>
<script type="text/javascript" src="jspdf/jspdf.js"></script>
<script type="text/javascript" src="jspdf/libs/base64.js"></script>

<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Alertas-Valoración.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Full Page Image Header with Vertically Centered Content -->
<script>
    $(document).ready(function(){
        $.fn.snow();
    });
</script>
<script src="../../js_extensions/mijs.js"> </script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($model, 'fecha', [
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-4">{label}</div>'
                . '<div class="col-md-8"><div class="input-group">'
                . '<span class="input-group-addon" id="basic-addon1">'
                . '<i class="glyphicon glyphicon-calendar"></i>'
                . '</span>{input}</div>{error}{hint}</div>',
                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                'options' => ['class' => 'drp-container form-group']
            ])->widget(DateRangePicker::classname(), [
                'useWithAddon' => true,
                'convertFormat' => true,
                'presetDropdown' => true,
                'readonly' => 'readonly',
                'pluginOptions' => [
                    'timePicker' => false,
                    //'timePickerIncrement' => 15,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'right'
            ]]);
            ?>
        </div>
        <div class="col-md-6">
            <?=
            $form->field($model, 'pcrc', ['template' => $template])
            ->widget(Select2::classname(), [
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                'allowClear' => true,
                'minimumInputLength' => 3,
                'ajax' => [
                'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                'dataType' => 'json',
                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                ],
                'initSelection' => new JsExpression('function (element, callback) {
                    var id=$(element).val();
                    if (id !== "") {
                        $.ajax("' . Url::to(['basesatisfaccion/getarbolesbypcrc']) . '?id=" + id, {
                            dataType: "json",
                            type: "post"
                        }).done(function(data) { callback(data.results[0]);});
                    }
                }')
                ]
                ]
                );
                ?>

        </div>
    </div> 

    <div class="row">        
        <div class="col-md-6">            
            <?=
                        $form->field($model, 'responsable', ['template' => $template])
                        ->widget(Select2::classname(), [
                            //'data' => array_merge(["" => ""], $data),
                            'language' => 'es',
                            'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                            'pluginOptions' => [
                                'allowClear' => true,
                                'minimumInputLength' => 4,
                                'ajax' => [
                                    'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                    'dataType' => 'json',
                                    'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                ],
                                'initSelection' => new JsExpression('function (element, callback) {
                                            var id=$(element).val();
                                            if (id !== "") {
                                                $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                                    dataType: "json",
                                                    type: "post"
                                                }).done(function(data) { callback(data.results[0]);});
                                            }
                                        }')
                            ]
                                ] 
                    );
            ?> 
        </div>  
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
            <?php //Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default'])      ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>    
</div>


    <div class="col-sm-6" style="padding-right: 0px;">
        <div class="page-header">
            <h3><?= Yii::t('app', 'Resumen Proceso') ?></h3>
        </div>
        <div style="max-height: 300px; overflow: auto">
            <table class="table table-striped table-bordered tblResDetFeed">
            <caption>Tabla Proceso</caption>
                <thead>
                    <tr>
                        <th id="programa"><?= Yii::t('app', 'Programa') ?></th>
                        <th id="cliente"><?= Yii::t('app', 'Cliente') ?></th>
                        <th id="cantidadAlertas"><?= Yii::t('app', 'Cantidad de alertas') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resumenFeedback as $value) : ?>
                        <tr>
                            <td><?= $value["Programa"]; ?></td>
                            <td><?= $value["Cliente"]; ?></td>
                            <td><?= $value["Count"]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>    
   
    <div class="col-sm-6" style="padding-right: 0px;">
        <div class="page-header">
            <h3><?= Yii::t('app', 'Resumen Tecnico') ?></h3>
        </div>
        <div style="max-height: 300px; overflow: auto">
            <table class="table table-striped table-bordered tblResDetFeed">
            <caption>Tabala Resumen Tecnico</caption>
                <thead>
                    <tr>
                        <th id="tecnico"><?= Yii::t('app', 'Tecnico') ?></th>
                        <th id="programa"><?= Yii::t('app', 'Programa') ?></th>
                        <th id="cliente"><?= Yii::t('app', 'Cliente') ?></th>
                        <th id="cantidadAlertas"><?= Yii::t('app', 'Cantidad de alertas') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalleLiderFeedback as $value) : ?>
                        <tr>
                            <td><?= $value["Tecnico"]; ?></td>
                            <td><?= $value["Programa"]; ?></td>
                            <td><?= $value["Cliente"]; ?></td>
                            <td><?= $value["Count"]; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<div class="col-sm-6" style="padding-right: 0px;">
    &nbsp;
    	<div class="page-header">
    		<br>
        	<h3><?= Yii::t('app', 'Vista Global') ?></h3>
	</div>
</div>

	<div class="btn-group" style=" padding: 10px; margin-right:1200px">
		<div class="dropdown open">
  			<button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
     				<span class="glyphicon glyphicon-th-list"></span> Exportar
   			</button>
			<ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
				<li><a href="#" onclick="javascript:xport.toCSV('Alertas');"> <em class="glyphicon glyphicon-export"></em> Excel 2010</a></li>			
				<li><a href="#" onclick="javascript:xport.toCSV('Alertas');"> <em class="glyphicon glyphicon-export"></em> Excel 2007</a></li>				
				<li class="divider"></li>				
			</ul>
		</div>
	</div>

<?php
	if($variables == "70" || $variables == "415" || $variables == "7" || $variables == "438" || $variables == "991" || $variables == "1609" ||
		$variables == "1796" || $variables == "223" || $variables == "556" || $variables == "1251" || $variables == "790" || $variables == "473" ||
			$variables == "777" || $variables == "2953" || $variables == "3229" || $variables == "3468" || $variables == "2913" || $variables == "2911" || $variables == "2991" || $variables == "2990" || $variables == "57" ){
?>
    <div class="col-sm-12">    
	<?php
		$var = Yii::$app->db->createCommand("select count(*) from tbl_alertascx")->queryScalar();
		echo "Mostrando " .$var." elementos."
	?>
	<br>    
        <div style="max-height: 300px; overflow: auto">
            <table id="Alertas" class="table table-striped table-bordered tblResDetFeed">
            <caption>Tabla alertas </caption>
                <thead>
                    <tr>
                        <th id="fecha"><?= Yii::t('app', 'Fecha') ?></th>
                        <th id="servicioPcrc"><?= Yii::t('app', 'Servicio / PCRC') ?></th>
                        <th id="valorador"><?= Yii::t('app', 'Valorador') ?></th>
                        <th id="tipoAlerta"><?= Yii::t('app', 'Tipo de Alerta') ?></th>
                        <th id="detalle"><?= Yii::t('app', 'Detalle') ?></th>
                        <th id="eliminar"><?= Yii::t('app', 'Eliminar') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataProvider as $value) : ?>
                        <tr>
                            <td><?= $value["fecha"]; ?></td>
                            <td><?= $value["Programa"]; ?></td>
                            <td><?= $value["Tecnico"]; ?></td>
                            <td><?= $value["tipo_alerta"]; ?></td>
                            <td><a href="veralertas/<?php echo $value["xid"] ?>"  href="veralertas/" . <?= $value["xid"]; ?>><img src="../../../web/images/ico-view.png" alt="icon-view"></a></td>
			    <td><input type="image" src="../../../web/images/ico-delete.png" alt="icon-delete" name="imagenes" style="cursor:hand" id="imagenes" onclick="eliminarDato(<?php echo $value["xid"] ?>);" /></td> 
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}
else
{
?>
    <div class="col-sm-12">    
	<?php
		$var = Yii::$app->db->createCommand("select count(*) from tbl_alertascx")->queryScalar();
		echo "Mostrando " .$var." elementos."
	?>
	<br>    
        <div style="max-height: 300px; overflow: auto">
            <table id="Alertas" class="table table-striped table-bordered tblResDetFeed">
            <caption>Tabla alertas</caption>
                <thead>
                    <tr>
                        <th id="fecha"><?= Yii::t('app', 'Fecha') ?></th>
                        <th id="servicioPcrc"><?= Yii::t('app', 'Servicio / PCRC') ?></th>
                        <th id="valorador"><?= Yii::t('app', 'Valorador') ?></th>
                        <th id="tipoAlerta"><?= Yii::t('app', 'Tipo de Alerta') ?></th>
                        <th id="detalle"><?= Yii::t('app', 'Detalle') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dataProvider as $value) : ?>
                        <tr>
                            <td><?= $value["fecha"]; ?></td>
                            <td><?= $value["Programa"]; ?></td>
                            <td><?= $value["Tecnico"]; ?></td>
                            <td><?= $value["tipo_alerta"]; ?></td>
                            <td><a href="veralertas/<?php echo $value["xid"] ?>"  href="veralertas/" . <?= $value["xid"]; ?>><img src="../../../web/images/ico-view.png" alt="icon-view" style="cursor:hand"></a></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php
}
?>


<script type="text/javascript">

function eliminarDato(params1){
	var cajaNum = params1;

    var opcion = confirm("Confirmar la eliminación de la alerta");

    if (opcion == true){
	 $.ajax({
                method: "post",
		url: "pruebas",
                data : {
                    alertas_cx: cajaNum,
                },
                success : function(response){ 
			console.log(response);
			var respuesta = JSON.parse(response);
			console.log(respuesta);
			if(respuesta == 1){
				window.location.href = "../basesatisfaccion/alertasvaloracion";

			}else{
				alert("Error al intentar eliminar la alerta");
			}
                }
            });
    }
};

var xport = {
  _fallbacktoCSV: true,  
  toXLS: function(tableId, filename) {   
    this._filename = (typeof filename == 'undefined') ? tableId : filename;
    
    //var ieVersion = this._getMsieVersion();
    //Fallback to CSV for IE & Edge
    if ((this._getMsieVersion() || this._isFirefox()) && this._fallbacktoCSV) {
      return this.toCSV(tableId);
    } else if (this._getMsieVersion() || this._isFirefox()) {
      alert("Not supported browser");
    }

    //Other Browser can download xls
    var htmltable = document.getElementById(tableId);
    var html = htmltable.outerHTML;

    this._downloadAnchor("data:application/vnd.ms-excel" + encodeURIComponent(html), 'xls'); 
  },
  toCSV: function(tableId, filename) {
    this._filename = (typeof filename === 'undefined') ? tableId : filename;
    // Generate our CSV string from out HTML Table
    var csv = this._tableToCSV(document.getElementById(tableId));
    // Create a CSV Blob
    var blob = new Blob([csv], { type: "text/csv" });

    // Determine which approach to take for the download
    if (navigator.msSaveOrOpenBlob) {
      // Works for Internet Explorer and Microsoft Edge
      navigator.msSaveOrOpenBlob(blob, this._filename + ".csv");
    } else {      
      this._downloadAnchor(URL.createObjectURL(blob), 'csv');      
    }
  },
  _getMsieVersion: function() {
    var ua = window.navigator.userAgent;

    var msie = ua.indexOf("MSIE ");
    if (msie > 0) {
      // IE 10 or older => return version number
      return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)), 10);
    }

    var trident = ua.indexOf("Trident/");
    if (trident > 0) {
      // IE 11 => return version number
      var rv = ua.indexOf("rv:");
      return parseInt(ua.substring(rv + 3, ua.indexOf(".", rv)), 10);
    }

    var edge = ua.indexOf("Edge/");
    if (edge > 0) {
      // Edge (IE 12+) => return version number
      return parseInt(ua.substring(edge + 5, ua.indexOf(".", edge)), 10);
    }

    // other browser
    return false;
  },
  _isFirefox: function(){
    if (navigator.userAgent.indexOf("Firefox") > 0) {
      return 1;
    }
    
    return 0;
  },
  _downloadAnchor: function(content, ext) {
      var anchor = document.createElement("a");
      anchor.style = "display:none !important";
      anchor.id = "downloadanchor";
      document.body.appendChild(anchor);

      // If the [download] attribute is supported, try to use it
      
      if ("download" in anchor) {
        anchor.download = this._filename + "." + ext;
      }
      anchor.href = content;
      anchor.click();
      anchor.remove();
  },
  _tableToCSV: function(table) {
    // We'll be co-opting `slice` to create arrays
    var slice = Array.prototype.slice;

    return slice
      .call(table.rows)
      .map(function(row) {
        return slice
          .call(row.cells)
          .map(function(cell) {
            return '"t"'.replace("t", cell.textContent);
          })
          .join(",");
      })
      .join("\r\n");
  }
};

</script>

<script>

	$.ajaxSetup({
        data: <?= \yii\helpers\Json::encode([
            \yii::$app->request->csrfParam => \yii::$app->request->csrfToken,
        ]) ?>
    });

</script>

