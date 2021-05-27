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

$this->title = 'Creacion de Partcipantes Alinear + VOC';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $fechaactual = date("Y-m-d");

?>
<br>
<div class="formularios-form" style="display: inline">
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
		<?php 
			echo $form->field($model3, 'participan_nombre')->textInput(['maxlength' => 200, 'id'=>"nomSesionId1"])->label("Nombre del Participante")
		?>

		<?php 
			echo $form->field($model3, 'fechacreacion')->textInput(['maxlength' => 200, 'value'=> $fechaactual, 'class'=>'hidden', 'id'=>"fechaId1"])
		?>

		<?php 
			echo $form->field($model3, 'anulado')->textInput(['maxlength' => 1, 'value'=> '0', 'class'=>'hidden', 'id'=>"anuladoId1"])
		?>		
	<?php ActiveForm::end(); ?>
    <div class="row" align="center">      
		<div onclick="generated();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear participante
        </div>    
    </div>
    <br>
            <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                <thead>
                    <tr>
                        <th class="text-center">Id</th>
                        <th class="text-center">Nombre del participante</th>
                    </tr>
                </thead>
                <tbody>                    
                        <?php 
                        	$data = Yii::$app->db->createCommand("select participan_id, participan_nombre  from tbl_participantes")->queryAll(); 

                          foreach ($data as $key => $value) {
                                  $txtiD = $value['participan_id'];
                                  $txtNams = $value['participan_nombre'];
                        ?> 
                        <tr>                  
                          <td class="text-center"><?php echo $txtiD; ?></td>
                          <td class="text-center"><?php echo $txtNams; ?></td>
                        </tr>
                        <?php } ?>
                    
                </tbody>
            </table>
</div>
<script type="text/javascript">
	function generated(){
		var varname = document.getElementById("nomSesionId1").value;
		var varfecha = document.getElementById("fechaId1").value;
		var varanulado = document.getElementById("anuladoId1").value;

		if (varname == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Datos sin registros.","warning");
			return;
		}else{
			$.ajax({
			        method: "post",
			        //url: "https://172.20.100.50/qa/web/index.php/controlalinearvoc/createparticipantealinearvoc",
			        url: "createparticipantealinearvoc",
			        data : {
			          txtName : varname,
			          txtFecha : varfecha,
			          txtAnula : varanulado,
			        },
			        success : function(response){ 
			                    var numRta =   JSON.parse(response);    
			                    console.log(numRta);

			                    if (numRta != 0) {
									$("#modal2").modal("hide");
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