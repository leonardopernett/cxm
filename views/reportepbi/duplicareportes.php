<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\modal;

$this->title = 'Creacion de Atributos Alinear + VOC'; 
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

?>
<br>
<div class="formularios-form" style="display: inline" id="dtbloque1">
	<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
      'inputOptions' => ['autocomplete' => 'off']
    ]
    ]); ?>		

	<strong>Nombre del Reporte </strong><?= Html::input('text','text','', $options=['class'=>'form-control', 'maxlength'=>80, 'id'=>'nombrerep']) ?>
   
	<?php ActiveForm::end(); ?>
  <br>
  <br>
	<div class="row" style="text-align: center">      
		  <div onclick="duplicarep();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Duplicar reporte
      </div>    
    </div>
</div>
<br>
<hr>
<script type="text/javascript">
	
function duplicarep(){
	var varareatrabajoid = document.getElementById("txtAreatrabajo").value;
    var varreporteid = document.getElementById("txtReportes").value;
    var tipo = 2;
    var varnamerep = document.getElementById("nombrerep").value;
    if (varareatrabajoid == "" || varreporteid == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Falta seleccionar un dato.","warning");
			return;
      }else{
        $.ajax({
              method: "post",
              url: "alter_report",
              data : {
                tipo : tipo,
                workspace : varareatrabajoid,
                reporte : varreporteid,
                new_name_report : new_name_report,                
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          if (Rta.status = "1") {
                                    event.preventDefault();
							        	swal.fire("!!! Informacion !!!","Se duplico satisfactoriamente el reporte.","success"); 
                      }
              }
        }); 
      }
    
	};
    
</script>