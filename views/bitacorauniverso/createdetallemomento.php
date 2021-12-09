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
use app\models\ControlvocListadopadre;

$this->title = 'Creacion detalle momento'; 
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $fechaactual = date("Y-m-d");

	$variables = Yii::$app->db->createCommand("select * from tbl_momento_bit_uni where anulado = 0")->queryAll();
    $listData = ArrayHelper::map($variables, 'id_momento', 'nombre_momento');
?>
<br>
<div class="formularios-form" style="display: inline" id="dtbloque1">
	<?php $form = ActiveForm::begin([
		'layout' => 'horizontal',
		'fieldConfig' => [
			'inputOptions' => ['autocomplete' => 'off']
		]
		]); ?>
		
		<?php 
			echo $form->field($model3, 'id_momento')->dropDownList($listData, ['prompt' => 'Seleccione...',  'id'=>'selectedID'])->label('Momentos') 
		?> 

		<?php 
			echo $form->field($model3, 'detalle_momento')->textInput(['maxlength' => 200, 'id'=>"nomdetallem"])->label("Ingresar nombre detalle")
		?>

        <?php 
			echo $form->field($model3, 'usua_id')->textInput(['maxlength' => 11, 'value'=> $sessiones, 'class'=>'hidden', 'id'=>"UsuaId"])
		?>
        
        <?php 
			echo $form->field($model3, 'fechacreacion')->textInput(['maxlength' => 200, 'value'=> $fechaactual, 'class'=>'hidden', 'id'=>"fechaId"])
		?>

		<?php 
			echo $form->field($model3, 'anulado')->textInput(['maxlength' => 1, 'value'=> '0', 'class'=>'hidden', 'id'=>"anuladoId"])
		?>			

	<?php ActiveForm::end(); ?>
	<div class="row" style="text-align: center;">      
		<div onclick="generated2();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear Categoria
        </div>    
    </div>
</div>
<br>
<hr>
<div class="formularios-form" style="display: none" id="dtbloque3">
	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
	<caption>Detalle momento</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center">Id Det. Momento</th>
                <th scope="col" class="text-center">Momento</th>
                <th scope="col" class="text-center">Detalle momento</th>
            </tr>
        </thead>
        <tbody>                    
            <?php 
            	$dataIndi =  new Query;
                $dataIndi   ->select(['tbl_detalle_momento_bit_uni.id_momento','tbl_momento_bit_uni.nombre_momento','tbl_detalle_momento_bit_uni.detalle_momento'])
                            ->from('tbl_detalle_momento_bit_uni')
                            ->join('INNER JOIN', 'tbl_momento_bit_uni',
                                        'tbl_momento_bit_uni.id_momento = tbl_detalle_momento_bit_uni.id_momento');
                $command = $dataIndi->createCommand();
                $data = $command->queryAll();
                

                foreach ($data as $key => $value) {
                    $txtiD = $value['id_momento'];
                    $txtNams = $value['nombre_momento'];
                    $txtDetalle = $value['detalle_momento'];
            ?> 
                <tr>                  
                    <td class="text-center"><?php echo $txtiD; ?></td>
                    <td class="text-center"><?php echo $txtNams; ?></td>
                    <td class="text-center"><?php echo $txtDetalle; ?></td>
                </tr>
            <?php } ?>                    
        </tbody>
    </table>
</div>
<script type="text/javascript">
	
    function generated2(){
		var varMomentoid = document.getElementById("selectedID").value;
		var varNomdet = document.getElementById("nomdetallem").value;
		var varUsuaid = document.getElementById("UsuaId").value;
		var varFechas = document.getElementById("fechaId").value;
		var varAnular = document.getElementById("anuladoId").value;

		if (varMomentoid == "" || varNomdet == "") {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","No hay datos a registrar.","warning");
			return;
		}else{
			$.ajax({
			        method: "post",
			        url: "../../bitacorauniverso/createdetallemomentolis",
			        data : {
			          txtvmomentoid : varMomentoid,
			          txtvanomdet : varNomdet,
			          txtvusuaid : varUsuaid,
			          txtvfechas : varFechas,
			          txtvanular : varAnular,
			        },
			        success : function(response){ 
			                    var numRta =   JSON.parse(response);    
			                    console.log(numRta);

			                    if (numRta != 0) {
                                    swal.fire("!!! Información !!!","Los datos se guardaron","warning");
                                    var varNomdet = document.getElementById("nomdetallem").value = '';
									$("#modal3").modal("hide");
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