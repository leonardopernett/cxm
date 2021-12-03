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

?>
<br>
<div class="formularios-form" style="display: inline">
	<?php $form = ActiveForm::begin([
		'layout' => 'horizontal',
		'fieldConfig' => [
			'inputOptions' => ['autocomplete' => 'off']
		]
		]); ?>
         
        <strong>Nombre del Reporte </strong><?= Html::input('text','text','', $options=['class'=>'form-control', 'maxlength'=>80, 'id'=>'nombrerep']) ?>
        <input type="text"   id="txtnombreareat"  readonly="readonly" value="<?php echo $nombrearea; ?>"> 
       	
	<br>
    <br>			
	<?php ActiveForm::end(); ?>
    <div class="row" style="text-align: center;">      
		<div onclick="duplicarrep();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
        Duplicar reporte
        </div>    
    </div>
    <br>           
</div>
<script type="text/javascript">
	function crearws(){
		var varname = document.getElementById("nombrerep").value;

		if (varname == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Datos sin registros.","warning");
			return;
		}else{
			$.ajax({
			        method: "post",
			        url: "create_workspace",
			        data : {
                        workspace_name : varname,
			        },
			        success : function(response){ 
			                    var numRta =   JSON.parse(response);    
			                    console.log(numRta);

			                    if (numRta != 0) {
                                    event.preventDefault();
							        	swal.fire("!!! Informacion !!!","Se guardo satisfactoriamente.","success");
									$("#modal5").modal("hide");
			                    }else{
			                    	event.preventDefault();
							        	swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
							      	return;
			                    }
			                }
			});
		}
	};
</script>