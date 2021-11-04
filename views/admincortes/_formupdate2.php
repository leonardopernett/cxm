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
use yii\jui\DatePicker;


$this->title = 'Actualizar el Corte';
$this->params['breadcrumbs'][] = ['label' => 'Actualiza el Corte'];//, 'url' => ['update']];
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $fechaActual = date('Y-m-d');

    $varidtc = $nameVal;
    $rtaCantd = $model->cantdiastcs;

?>
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
<br>
<div class="page-header" >
    <h3 class="text-center" style="color:#100048;"><?= Html::encode($this->title) ?></h3>
</div>
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
		<div class="form-group">
			<h3 style="color:#100048;"><left>Actualizar Datos Corte...</left></h3>
			

				<?= $form->field($model, 'cortetcs')->textInput(['maxlength' => 150, 'id'=>'txttipoCorte', 'readonly' => 'readonly'])->label('Corte') ?>

				<?= $form->field($model, 'diastcs')->textInput(['maxlength' => 150, 'id'=>'txttdiastcs'])->label('Dias del corte') ?>

				<?= $form->field($model, 'fechainiciotcs')->label('Fecha inicio')->widget(DatePicker::className(),['dateFormat' => 'yyyy-MM-dd', 'clientOptions' => ['yearRange' => '-115:+0', 'changeYear' => true], 'options' => ['class' => 'form-control', 'style' => 'width:25%'] ]) ?>

				<?= $form->field($model, 'fechafintcs')->label('Fecha fin')->widget(DatePicker::className(),['dateFormat' => 'yyyy-MM-dd', 'clientOptions' => ['yearRange' => '-115:+0', 'changeYear' => true], 'options' => ['class' => 'form-control', 'style' => 'width:25%']]) ?>			
				
				<?= $form->field($model, 'cantdiastcs')->textInput(['maxlength' => 150, 'id'=>'txtcantdiastcs', 'readonly' => 'readonly'])->label('Cantidad de dias') ?>	

				<?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 150, 'id'=>'txtfechaactual', 'value' => $fechaActual, 'class'=>"hidden"]) ?>

				<?= $form->field($model, 'idtc')->textInput(['maxlength' => 150, 'id'=>'txtidtc', 'class'=>'hidden']) ?>
		   
		</div>
		<div align="center">  
				<div onclick="sumadias();" class="btn btn-primary"  method='post' id="botones1" >
	        		Calcular Dias
	    		</div>   
			&nbsp;&nbsp;	    
		    	<?= Html::submitButton('Actualizar', ['class' => 'btn btn-primary', 'id'=>'btn_submit', 'style' => 'background-color: #4298b4', 'onclick' => 'validarfechastext();'] ) ?>
		    &nbsp;&nbsp;
			    <?= Html::a('Regresar',  ['update', 'idtc' => $model->idtc], ['class' => 'btn btn-success',
	                        'style' => 'background-color: #707372',
	                        'data-toggle' => 'tooltip',
	                        'title' => 'Regresar']) 
	    		?>
		</div>
	<?php $form->end() ?> 

<script type="text/javascript">
	function validarfechastext(){
		var text1 = document.getElementById("txttipoCorte").value;
		var text2 =document.getElementById("txttdiastcs").value;
		var fecha1 = document.getElementById("tiposdecortes-fechainiciotcs").value;
		var fecha2 = document.getElementById("tiposdecortes-fechafintcs").value;
		var cantdias = document.getElementById("txtcantdiastcs").value;

		var brtaCantD = "<?php echo $rtaCantd; ?>";
		
		if (text1 == null || text1 == "") 
		{
			event.preventDefault();
			swal.fire("!!! Advertencia !!!","Tipo de corte vacio, Ingrese el Tipo de corte.","warning");			
			return;
		}
		else
		{
			if (text2 == null || text2 == "") 
			{
				event.preventDefault();
				swal.fire("!!! Advertencia !!!","Los dias de corte vacio, Ingrese los dias de corte.","warning");
				return;
			}
		}

		if (fecha1 == null || fecha1 == "") 
		{
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Las fecha inicio no puede estar vacia.","warning");
			return;			
		}
		else
		{
			if (fecha2 == null || fecha2 == "") 
			{
				event.preventDefault();
				swal.fire("!!! Advertencia !!!","Las fecha fin no puede estar vacia.","warning");
				return;		
			}
		}

		if (fecha1 >= fecha2) 
		{
			event.preventDefault();
			swal.fire("!!! Advertencia !!!","Las fecha deben estar en un rango adecuado.","warning");
			return;
		}

		if (cantdias == null || cantdias == "") 
		{
			event.preventDefault();
			swal.fire("!!! Advertencia !!!","La cantidad de dias esta vacio, hacer clic en boton calcular dias para obtener el numero de dias que hay entre la fecha inicio a la fecha fin.","warning");
			return;
		}

		if (cantdias == brtaCantD) {
			event.preventDefault();
			swal.fire("!!! Advertencia !!!","Los datos a actualizar son iguales, por lo tanto no se actualiza","warning");
			return;
		}

	};

	function sumadias(){
		var fecha1 = new Date(document.getElementById("tiposdecortes-fechainiciotcs").value);
		var fecha2 = new Date(document.getElementById("tiposdecortes-fechafintcs").value);

		if (fecha1 == null || fecha1 == "") 
		{
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Las fecha inicio no puede estar vacia.","warning");
			return;			
		}
		else
		{
			if (fecha2 == null || fecha2 == "") 
			{
				event.preventDefault();
				swal.fire("!!! Advertencia !!!","Las fecha fin no puede estar vacia.","warning");
				return;		
			}
		}

		var numcantdias = fecha2.getTime()-fecha1.getTime();
		var contardias = Math.round(numcantdias/(1000*60*60*24));		

		//--------------------------------------------------------------------------------
		var bfecha1 = new Date(document.getElementById("tiposdecortes-fechainiciotcs").value);		

		var cuentaFinde = 0; //Número de Domingos
    	var array = new Array(contardias);

     	for (var i=0; i < contardias; i++) 
	    {
	        //0 => Domingo - 6 => Sábado
	        if (bfecha1.getDay() == 0 || bfecha1.getDay() == 5) {
	            cuentaFinde++;
	        }
	        bfecha1.setDate(bfecha1.getDate() + 1);
	    }		
		//--------------------------------------------------------------------------------

		document.getElementById("txtcantdiastcs").value = contardias + 2 - cuentaFinde;
	};
</script>