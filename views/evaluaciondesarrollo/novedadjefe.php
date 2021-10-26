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

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $varnombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $varcargo = Yii::$app->db->createCommand("select distinct concat(posicion,' - ',funcion) from  tbl_usuarios_evalua where documento in ('$vardocument')")->queryScalar();
    $vartipoeva = Yii::$app->db->createCommand("select tipoevaluacion from tbl_evaluacion_tipoeval where idevaluaciontipo = 1 and anulado = 0")->queryScalar();

    $last_word_start = strrpos($varnombre, ' ') + 1;
    $last_word = substr($varnombre, $last_word_start);

    $varjefe = "No es mi feje actual";

    $query = Yii::$app->db->createCommand("select ue.documento_jefe, ue.nombre_jefe from tbl_usuarios_evalua ue 
	group by ue.documento_jefe, ue.id_cargo_jefe order by ue.nombre_jefe asc")->queryAll();
    $listData = ArrayHelper::map($query, 'documento_jefe', 'nombre_jefe');

    $varTipos = ['Jefe incorrecto' => 'Jefe incorrecto', 'Otros inconvenientes' => 'Otros inconvenientes' ];

?>
<div id="idCapaUno" style="display: inline">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?> 
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<label style="font-size: 16px;"><em class="fas fa-bolt" style="font-size: 20px; color: #FFC72C;"></em> Tipo de novedad:</label>
				<?= $form->field($model, "asunto")->dropDownList($varTipos, ['prompt' => 'Seleccionar Novedad', 'id'=>"idAsuntoN", 'onchange' => 'varhabiita();']) ?>
				<div id="idcambiod" style="display: none;">
					<label style="font-size: 16px;"><em class="fas fa-pencil-ruler" style="font-size: 20px; color: #FFC72C;"></em> Elegir jefe:</label>
					<?= $form->field($model, "cambios")->dropDownList($listData, ['prompt' => 'Seleccionar Jefe', 'id'=>"idcambiosN"]) ?>
				</div>				
				<?= $form->field($model, 'documento')->textInput(['maxlength' => 250,  'id'=>'iddocumentoN', 'class' => 'hidden', 'value' => $vardocument]) ?>
                <label style="font-size: 16px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em> Comentarios:</label>
                <?= $form->field($model, 'comentarios')->textInput(['maxlength' => 250,  'id'=>'IdcomentariosN']) ?>
			</div>
		</div>
	</div>
	<br>
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
                <?= Html::submitButton(Yii::t('app', 'Guardar'),
					                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
					                'data-toggle' => 'tooltip',
					                'title' => 'Guardar',
					                'onclick' => 'validarvalor();',
					                'id'=>'ButtonSearch']) 
				?>
			</div>
		</div>
	</div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
	function varhabiita(){
		var varidAsuntoN2 = document.getElementById("idAsuntoN").value;
		var varidcambiod = document.getElementById("idcambiod");

		if (varidAsuntoN2 == "Jefe incorrecto") {
			varidcambiod.style.display = 'inline';
		}else{
			varidcambiod.style.display = 'none';
		}
	};

	function validarvalor(){
		var varidasuntosN = document.getElementById("idasuntosN").value;
		var varIdcomentariosN = document.getElementById("IdcomentariosN").value;
		var varidcambiosN = document.getElementById("idcambiosN").value;

		if (varidasuntosN == "") {
			event.preventDefault();
            swal.fire("!!! Advertencia !!!","Ingrese el asunto de la novedad","warning");
            return; 
		}else{
			if (varIdcomentariosN == "") {
				event.preventDefault();
	            swal.fire("!!! Advertencia !!!","Ingrese el comentario de la novedad","warning");
	            return; 
	        }
		}
	};
</script>