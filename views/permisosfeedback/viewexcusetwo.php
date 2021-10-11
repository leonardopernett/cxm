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

?>
<style>
    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

</style>
<div class="capaPP" style="display: inline;">
	<?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #C148D0;"></i> Acciones a registrar... </label>
				<?= $form->field($model, 'idusuarios')->textInput(['maxlength' => 300, 'id'=>'txtusuarioid', 'placeholder'=>'Agregar usuario de red'])?>
				<div onclick="validarvalor();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="ButtonSearch" >
                    Guardar Informacion
                </div> 
			</div>
		</div>
	</div>
	<?php $form->end() ?> 
</div>
<script type="text/javascript">
	function validarvalor(){
		var vartxtusuarioid = document.getElementById("txtusuarioid").value;

		if (vartxtusuarioid == "") {
			event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de ingresar un usuario de red","warning");
            return;
		}else{
			$.ajax({
				method: "get",
				url: "generarregistro",
				data: {
					txtusuarios : vartxtusuarioid,
				},
				success : function(response){
					numRta =   JSON.parse(response);

					if (numRta == 1) {
						event.preventDefault();
			            swal.fire("¡¡¡ Advertencia !!!","El usuario de red ya tiene el permiso registrado","warning");
			            return;
					}else{
						if (numRta == 2) {
							event.preventDefault();
				            swal.fire("¡¡¡ Advertencia !!!","Problemas con el usuario de red, por favor validarlo","warning");
				            return;
						}else{
							window.location.href='index';
						}
					}
				}
			});
		}
	}
</script>