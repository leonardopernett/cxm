<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$txtvaridspeech  = $varidcategoria;
// var_dump($txtvaridspeech);
$txtvarvarpcrc = $varpcrc;
?>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<div class="capaUno" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<div class="row">
					<div class="col-md-6">
						<label style="font-size: 15px;"><em class="fas fa-paperclip" style="font-size: 15px; color: #FFC72C;"></em> Categorias: </label> 
						<?= $form->field($model, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 250, 'readonly'=>true, 'value'=>$varnombres, 'id'=>'txtidruta']) ?>
					</div>
					<div class="col-md-6">
						<label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></em> Seleccion: </label> 
						<?php $var3 = ['1' => 'Insatisfaccion Verbalizada', '2' => 'Solucion', '3' => 'Valores Corporativos', '4' => 'Facilidad/Esfuerzo', '5' => 'Habilidad Comercial/Venta Responsable']; ?>

                        <?= $form->field($model, 'componentes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var3, ['prompt' => 'Seleccione...', 'id'=>"txtConteoid"])->label('') ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
<hr>
<div class="capaOne" style="display: inline;">
    <div class="row">
                    
                    <div class="col-md-12">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Guardar Proceso: </label> 
                            <div onclick="general();" class="btn btn-primary" style="display:inline;" method='post' id="botones1" >
                                Guardar
                            </div>
                        </div>
                    </div>

    </div>
</div>
<?php ActiveForm::end(); ?>
<script type="text/javascript">
	function general(){
		var vartxtidspeech = "<?php echo $txtvaridspeech; ?>";
		var vartxtvarvarpcrc = "<?php echo $txtvarvarpcrc; ?>";
		var vartxtConteoid = document.getElementById("txtConteoid").value;

		if (vartxtConteoid == "") {
			event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar el componente","warning");
            return;
		}else{
			$.ajax({
				method: "get",
				url: "actualizaindicador",
				data : {
					idspeech : vartxtidspeech,
					varpcrc : vartxtvarvarpcrc,
					txtvartxtConteoid : vartxtConteoid,
				},
				success : function(response){
					numRta =   JSON.parse(response); 
					// console.log(numRta);
					location.reload();
				}
			});			
		}

	};
</script>
