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

$this->title = 'Creacion de Sesiones VOC';
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
			echo $form->field($model2, 'nombresession')->textInput(['maxlength' => 200, 'id'=>"nomSesionId"])->label("Nombre de la sesion")
		?>

		<?php 
			echo $form->field($model2, 'fechacreacion')->textInput(['maxlength' => 200, 'value'=> $fechaactual, 'class'=>'hidden', 'id'=>"fechaId"])
		?>

		<?php 
			echo $form->field($model2, 'anulado')->textInput(['maxlength' => 1, 'value'=> '0', 'class'=>'hidden', 'id'=>"anuladoId"])
		?>		
	<?php ActiveForm::end(); ?>
    <div class="row" align="center">      
		<div onclick="generated();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear sesion
        </div>    
    </div>
    <br>
            <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
			<caption>Tabla datos</caption>
                <thead>
                    <tr>
                        <th id="id" class="text-center">Id</th>
                        <th id="nombreBloqueoSesion" class="text-center">Nombre del bloque o sesion</th>
                    </tr>
                </thead>
                <tbody>                    
                        <?php 
                        	$data = Yii::$app->db->createCommand("select idsessionvoc, nombresession  from tbl_controlvoc_sessionlista")->queryAll(); 

                          foreach ($data as $key => $value) {
                                  $txtiD = $value['idsessionvoc'];
                                  $txtNams = $value['nombresession'];
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
			        url: "createsesionvoc",
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