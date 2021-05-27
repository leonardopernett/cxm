<?php

use yii\helpers\Html;
/*use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use \app\models\ControlProcesos;
use \app\models\Tiposdecortes;
use yii\helpers\ArrayHelper;

$this->params['breadcrumbs'][] = 'Justificar Rendimiento';
$this->title = 'Justificar Rendimiento';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $idvar = $varidtc;
    $variables = Tiposdecortes::find()
    				->select(['*'])
    				->where('idtc = '.$idvar.'')
    				->all();
    $listData = ArrayHelper::map($variables, 'idtcs', 'diastcs');

    //$nameVar = Yii::$app->db->createCommand('select usua_nombre from tbl_usuarios where usua_id ='.$idvar.'')->queryScalar();

    $fechaactual = date("Y-m-d");
?>
<br>
<div class="page-header" >
    <h3><center><?= Html::encode($this->title) ?></center></h3>
</div> 
<br>
<div class="control-procesos-index">

    <table align="center" border="1" class="egt table table-hover table-striped table-bordered">
        <tr>
            <th><p>Valorador: </p><?php echo $nameVar; ?></th>
        </tr>
    </table>    
    <hr>
    <br>

	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

		<?= $form->field($model, 'idtiposcortes')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'idtcs'])->label('Tipos de Cortes') ?> 

		<?php $var = ['Incapacidad' => 'Incapacidad', 'Capacidad Operativa' => 'Capacidad Operativa', 'Licencia/Vacaciones' => 'Licencia/Vacaciones', 'Reuniones' => 'Reuniones', 'Otras' => 'Otras (Relaciones detalle)']; ?>
		
		<?= $form->field($model, "justificacion")->dropDownList($var, ['prompt' => 'Seleccione una opción', 'id'=>"id_argumentos"])->label('Justificacion') ?> 

		<?= $form->field($model, 'correo')->textInput(['maxlength' => 200, 'id'=>'txtcorreoid'])->label('Correo')?>

		<?= $form->field($model, 'fechacreacion')->textInput(['class' => 'hidden', 'id'=>'fechavalorid','value' => $fechaactual])  ?>

		<?= $form->field($model, 'evaluados_id')->textInput(['class' => 'hidden', 'id'=>'txtevaluadoid','value' => $varIdUsua])?>

		<?= $form->field($model, 'responsable')->textInput(['class' => 'hidden', 'id'=>'txtevaluadoid','value' => $sessiones])?>

		<div align="center">
			<?= Html::submitButton(Yii::t('app', 'Enviar Correo'),
	                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
	                'data-toggle' => 'tooltip',
	                'title' => 'Enviar Correo',
	                'onclick' => 'validarvalor();',
	                'id'=>'ButtonSearch']) 
	    	?>

	    	<?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Valorador']) 
        	?>
		</div>

	<?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
	function validarvalor(){
		var varcortes = document.getElementById("idtcs").value;
		var varjustificacion = document.getElementById("id_argumentos").value;
		var varcorreo = document.getElementById("txtcorreoid").value;

		if (varcortes == "" || varcortes == null) {
			event.preventDefault();
			swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar el tipo de corte.","warning");
			return;	
		}

		if (varjustificacion == "" || varjustificacion == null) {
			event.preventDefault();
			swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar una justificacion.","warning");
			return;				
		}

		if (varcorreo == "" || varcorreo == null) {
			event.preventDefault();
			swal.fire("¡¡¡ Advertencia !!!","Debe de ingresar el correo electronico","warning");
			return;							
		}
	}
</script>