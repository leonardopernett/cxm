<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Novedad Evaluacion a Cargo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $document_user = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $document_user = 456;
    $options = [        
        'falta_persona' => 'Falta persona a mi cargo',
        'no_esta_a_mi_cargo' => 'Persona no está a mi cargo',
        'persona_retirada' => 'Persona retirada'
    ];
    
?>
<div id="idCapaUno" style="display: inline">
<?php $form = ActiveForm::begin([
	'layout' => 'horizontal',
	'fieldConfig' => [
		'inputOptions' => ['autocomplete' => 'off']
	  ]
	]); ?> 
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<label style="font-size: 16px;"><em class="fas fa-bolt" style="font-size: 20px; color: #FFC72C;"></em> Tipo de novedad <span class="color-required">*</span></label>               
                <?= Html::dropDownList('seleccion', "", $options, 
                            [
                            "id"=>"tipo_novedad_a_cargo",
                            "class" => "form-control",
                            'prompt' => 'Seleccione novedad...',
                            'onChange'=>'habilitar_nuevo_usuario(this.value)'                            
                        ]); 
                ?>
                <div id="campo_cc_nueva" style="display: none;">
                    <label style="font-size: 16px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em> Documento Usuario Nuevo <span class="color-required">*</span></label>
                    <?= $form->field($model, 'cc_colaborador_nuevo', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['onkeypress'=>'return valida(event);', 'id'=>'cc_usuario_nuevo', 'placeholder'=>'Ingrese solo números']) ?>
                </div>

                <label style="font-size: 16px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C; margin-top:10px"></em> Comentarios</label>
                <?= $form->field($model, 'comentarios_solicitud', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true, 'placeholder'=>'Opcional', 'id'=>'comentarios']) ?>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
                <?= Html::submitButton(Yii::t('app', 'Guardar'),
					                ['class' => 'btn btn-primary',                
					                'data-toggle' => 'tooltip',
                                    'onclick' => 'validarvalor();',
					                'title' => 'Guardar'])
				?>
			</div>
		</div>
	</div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript"> 

    function habilitar_nuevo_usuario(valorSeleccionado){

        var campo_documento_nuevo = document.getElementById("campo_cc_nueva");

        if(valorSeleccionado=="falta_persona"){ //mostrar campo cc_usuario_nuevo
            campo_documento_nuevo.style.display = "inline"
        } else { //ocultar campo cc_usuario_nuevo
            campo_documento_nuevo.style.display = "none"
        }

    }

	function validarvalor(){
		var tipo_novedad = document.getElementById("tipo_novedad_a_cargo").value;
		var cc_usuario_nuevo= document.getElementById("cc_usuario_nuevo").value;

		if (tipo_novedad == "") {
			event.preventDefault();
            swal.fire("!!! Advertencia !!!","Ingrese tipo de la novedad","warning");
            return; 
		} 

        if (tipo_novedad == "falta_persona" && cc_usuario_nuevo == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!", "Ingrese número de documento del usuario faltante", "warning");
            return;
        }
	}

    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
            return true;
        }
            
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    };

</script>