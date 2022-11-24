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
use yii\bootstrap\modal;

$this->title = 'Creacion de Wordspace';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $fechaactual = date("Y-m-d");
	//$varid = $_GET['id'];
?>
<br>
<div class="formularios-form" style="display: inline">
	<?php $form = ActiveForm::begin([
		'layout' => 'horizontal',
		'fieldConfig' => [
			'inputOptions' => ['autocomplete' => 'off']
		]
		]); ?>
         
        <strong>Area de trabajo </strong><?= Html::input('text','text','', $options=['class'=>'form-control', 'maxlength'=>100, 'id'=>'nombrearea']) ?>
		
         <label for="txtmedio" style="font-size: 14px;">Medio</label>
         <select id="txtmedio" class ='form-control'>
                <option value="" disabled selected>seleccione...</option>
                <option value="1">Voc</option>
                <option value="2">Voe</option>
                <option value="3">Voux</option>
         </select>
      
			
	<br>
    <br>			
	<?php ActiveForm::end(); ?>
    <div class="row" style="text-align: center;">      
		<div onclick="crearws();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear area de trabajo
        </div>    
    </div>
    <br>           
</div>
<script type="text/javascript">
	function crearws(){
		var varname = document.getElementById("nombrearea").value;
		var varId = document.getElementById("txtmedio").value;
		alert(varname);
		if (varname == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Datos sin registros.","warning");
			return;
		}else{
			$.ajax({
			        method: "post",
			        url: "create_workspace",
			        data : {
                        workspace_name: varname,
						var_Id: varId,
			        },
			        success : function(response){ 
			                    var numRta =   JSON.parse(response);    
			                    console.log(numRta);

			                    if (numRta == 1) {
                                    event.preventDefault();
							        	swal.fire("!!! Informacion !!!","Se guardo satisfactoriamente.","success");
									$("#modal1").modal("hide");
			                    }else{
			                    	event.preventDefault();
							        	swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
										window.location.href='reporte';
							      	//return;
			                    }
			                }
			});
		}
	};
</script>