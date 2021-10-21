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

$this->title = 'Creacion de Sesiones Alinear + VOC';
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
			echo $form->field($model2, 'sesion_nombre')->textInput(['maxlength' => 200, 'id'=>"nomSesionId"])->label("Nombre de la sesion")
		?>

		<?php 
			echo $form->field($model2, 'fechacreacion')->textInput(['maxlength' => 200, 'value'=> $fechaactual, 'class'=>'hidden', 'id'=>"fechaId"])
		?>

		<?php 
			echo $form->field($model2, 'anulado')->textInput(['maxlength' => 1, 'value'=> '0', 'class'=>'hidden', 'id'=>"anuladoId"])
		?>		
	<?php ActiveForm::end(); ?>
    <div class="row" align="center">      
		<div onclick="generated5();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear sesion
        </div>    
    </div>
    <br>
            <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
			<caption>Sesion</caption>
                <thead>
                    <tr>
                        <th scope="col" class="text-center">Id</th>
                        <th scope="col" class="text-center">Nombre de la sesion</th>
                    </tr>
                </thead>
                <tbody>                    
                        <?php 
                        	$data = Yii::$app->db->createCommand("select sesion_id, sesion_nombre  from tbl_sesion_alinear")->queryAll(); 

                          foreach ($data as $key => $value) {
                                  $txtiD = $value['sesion_id'];
                                  $txtNams = $value['sesion_nombre'];
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
	function generated5(){
		var varname = document.getElementById("nomSesionId").value;
		var varfecha = document.getElementById("fechaId").value;
		var varanulado = document.getElementById("anuladoId").value;

		if (varname == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Datos sin registros.","warning");
			return;
		}else{
			$.ajax({
			        method: "post",
			        url: "createsesionalinearvoc",
			        data : {
			          txtName : varname,
			          txtFecha : varfecha,
			          txtAnula : varanulado,
			        },
			        success : function(response){ 
			                    var numRta =   JSON.parse(response);    
			                    console.log(numRta);

			                    if (numRta != 0) {
									$("#modal1").modal("hide");
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