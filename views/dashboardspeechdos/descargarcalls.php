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
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Dashboars Escuchar + 2.0';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones =Yii::$app->user->identity->id;  
    $varconteo = 0; 
?>
<div class="capaOne" id="IdCapaCero" style="display: inline;">
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<label style="font-size: 18px;"><i class="fas fa-at" style="font-size: 25px; color: #827DF9;"></i> Ingresar el correo corporativo </label> 					
				<?= $form->field($model, 'servicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'id_destino', 'placeholder' => 'Destinatario'])->label('') ?>  
				<br>
				<?= Html::submitButton(Yii::t('app', 'Enviar archivo'),
                    ['class' => $model->isNewRecord ? 'btn btn-danger' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'onclick' => 'enviodatos();',
                        'title' => 'Enviar datos']) 
                ?>			
			</div>
		</div>
	</div>
	<?php $form->end() ?>
</div>
<div class="CapaUno" style="display: none;" id="IdCapaUno">
    <p><h3>Procesando informacion a enviar...</h3></p>
</div>
<script type="text/javascript" charset="UTF-8">
	function enviodatos(){
        var varDestino = document.getElementById("id_destino").value
        var varIdCapaCero = document.getElementById("IdCapaCero");
        var varIdCapaUno = document.getElementById("IdCapaUno");

        if (varDestino == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de ingresar un correo corporativo para enviar los datos","warning");
            return;  
        }else{
            varIdCapaCero.style.display = 'none';
            varIdCapaUno.style.display = 'inline';
        }
    };
</script>