<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
    <?php $form = ActiveForm::begin([
		'fieldConfig' => [
			'inputOptions' => ['autocomplete' => 'off']
		],
		'layout' => 'horizontal'
		]); ?>
        <div class="col-md-12">
            <div class="card1 mb">
            	<label style="font-size: 15px;"> Nombre de la encuesta: </label> 
            	<?= $form->field($model, 'nombreencuesta')->textInput(['maxlength' => 200, 'id'=>'IdNombreEncuesta', 'placeholder'=>'Ingresar el nombre de la encuesta']) ?> 
            	
            	<label style="font-size: 15px;"> Identificacion de la encuesta: </label> 
            	<?= $form->field($model, 'idlimeencuesta')->textInput(['maxlength' => 200, 'id'=>'IdEncuesta', 'placeholder'=>'Ingresar la identificación de la encuesta']) ?> 
            	
            	<label style="font-size: 15px;"> Comentario sobre la encuesta: </label> 
            	<?= $form->field($model, 'comentariosenc')->textInput(['maxlength' => 200, 'id'=>'IdComentario', 'placeholder'=>'Ingresar un comentario sobre la encuesta']) ?> 
            	<hr>
            	<div onclick="guardarbtn();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                              Guardar
                </div> 
           	</div>
        </div>
    <?php $form->end() ?> 
    </div>
</div>
<script type="text/javascript">
	function guardarbtn(){
		var varIdNombreEncuesta = document.getElementById("IdNombreEncuesta").value;
		var varIdEncuesta = document.getElementById("IdEncuesta").value;
		var varIdComentario = document.getElementById("IdComentario").value;

		if (varIdNombreEncuesta == "") {
			event.preventDefault();
            swal.fire("!!! Advertencia !!!","No es posible guardar, no ha ingresado el nombre de la encuesta","warning");
            return;
		}else{
			if (varIdEncuesta == "") {
				event.preventDefault();
	            swal.fire("!!! Advertencia !!!","No es posible guardar, no ingresado la identificacion de la encuesta","warning");
	            return;
			}else{
				$.ajax({
					method: "get",
                    url: "guardarencuesta",
                    data: {
						txtvarIdNombreEncuesta : varIdNombreEncuesta,
                        txtvarIdEncuesta : varIdEncuesta,
                        txtvarIdComentario : varIdComentario,
                    },
                    success : function(response){ 
                            var numRta =   JSON.parse(response);    
                            console.log(numRta);
                            location.reload();
                    }
				});
			}
		}
	}
</script>