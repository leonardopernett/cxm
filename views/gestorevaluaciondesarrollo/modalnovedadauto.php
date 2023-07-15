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

$this->title = 'Novedad Autoevaluación';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $document_user = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $document_user = 456;
    $options = [
        'jefe_incorrecto' => 'Jefe Incorrecto'
    ];
  

   
    $varTipos = ['jefe_incorrecto' => 'Jefe Incorrecto', 'otros_inconvenientes' => 'Otros inconvenientes' ];

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
                            "id"=>"tipo_novedad_auto",
                            "class" => "form-control",
                            'prompt' => 'Seleccione novedad...']); 
                ?>
				<?= $form->field($model_jefe_incorrecto, 'cc_colaborador')->textInput(['class' => 'hidden', 'value' => $document_user])->label(false); ?>

                <label style="font-size: 16px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em> Documento Jefe Correcto <span class="color-required">*</span></label>
                <?= $form->field($model_jefe_incorrecto, 'cc_jefe_correcto')->textInput(['onkeypress'=>'return valida(event);', 'id'=>'jefe_correcto', 'placeholder'=>'Ingrese solo números']) ?>

                <label style="font-size: 16px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em> Comentarios</label>
                <?= $form->field($model_jefe_incorrecto, 'comentarios_solicitud')->textInput(['maxlength' => true, 'placeholder'=>'Opcional', 'id'=>'comentarios']) ?>
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

	function validarvalor(){
		var tipo_novedad = document.getElementById("tipo_novedad_auto").value;
		var jefe_correcto= document.getElementById("jefe_correcto").value;

		if (tipo_novedad == "") {
			event.preventDefault();
            swal.fire("!!! Advertencia !!!","Ingrese tipo de la novedad","warning");
            return; 
		}else{
			if (jefe_correcto == "") {
				event.preventDefault();
	            swal.fire("!!! Advertencia !!!","Ingrese número de documento del Jefe correcto","warning");
	            return; 
	        }
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