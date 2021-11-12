<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use \app\models\ControlProcesos;
use yii\jui\DatePicker;


$this->title = 'Agregar Tipos de Cortes';
$this->params['breadcrumbs'][] = ['label' => 'Administracion de Cortes'];//, 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $fechaActual = date('Y-m-d');

    $nom = $nombrecortes;
    $id = Yii::$app->db->createCommand("select idtc from tbl_tipocortes where tipocortetc = '$nom' and anulado = 0")->queryScalar();
    $incluirsabados = $incluir;
?>
<link rel='stylesheet' href='https://use.fontawesome.com/releases/v5.7.0/css/all.css' integrity='sha384-lZN37f5QGtY3VHgisS14W3ExzMWZxybE1SJSEsQp9S+oqd12jhcu+A56Ebc1zFSJ' crossorigin='anonymous'>
<br>
<div class="page-header" >
    <h3 class="text-center" style="color:#100048;"><?= Html::encode($this->title) ?></h3>
</div>
<div class="formularios-form">
	<div class="form-group">
		<table style="width:100%">
		<caption>Tabla cortes3</caption>
			<tr>
				<th scope="col">
					<label>Tipo de corte: </label> 
				</th>
				<td>
					<label>Dias del corte: </label>
				</td>
				<td>
					<label>Fecha de inicio: </label>
				</td>
				<td>
					<label>Fecha fin: </label>					
				</td>
				<td>
					<label>Cantidad de dias: </label>					
				</td>
			</tr>
			<tr>
				<td>
					<input value="<?php echo $nombrecortes ?>" class ='form-control' style ='width:90%' disabled>
				</td>
				<td>
					<input value="<?php echo $nombredias ?>" class ='form-control' style ='width:90%' disabled>
				</td>
								<td>
					<input value="<?php echo $fechai ?>" id="id_figeneral" class ='form-control' style ='width:90%' disabled>
				</td>
				<td>
					<input value="<?php echo $fechaf ?>" id="id_ffgeneral" class ='form-control' style ='width:90%' disabled>
				</td>
				<td>
					<input value="<?php echo $days ?>" class ='form-control' style ='width:90%' disabled>
				</td>
			</tr>
		</table>
	</div>
	<hr>
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
	<div class="form-group">
		<h3 style="color:#100048;"><left>Ingresar Datos Corte 3</left></h3>
		

			<?= $form->field($model1, 'cortetcs')->textInput(['maxlength' => 150, 'id'=>'txttipoCorte', 'readonly' => 'readonly', 'value' => 'Corte 3'])->label('Corte') ?>

			<?= $form->field($model1, 'diastcs')->textInput(['maxlength' => 150, 'id'=>'txttdiastcs'])->label('Dias del corte') ?>

			<?= $form->field($model1, 'fechainiciotcs')->label('Fecha inicio')->widget(DatePicker::className(),['dateFormat' => 'yyyy-MM-dd', 'clientOptions' => ['yearRange' => '-115:+0', 'changeYear' => true], 'options' => ['class' => 'form-control', 'style' => 'width:25%'] ]) ?>

			<?= $form->field($model1, 'fechafintcs')->label('Fecha fin')->widget(DatePicker::className(),['dateFormat' => 'yyyy-MM-dd', 'clientOptions' => ['yearRange' => '-115:+0', 'changeYear' => true], 'options' => ['class' => 'form-control', 'style' => 'width:25%']]) ?>			
			
			<?= $form->field($model1, 'cantdiastcs')->textInput(['maxlength' => 150, 'id'=>'txtcantdiastcs', 'readonly' => 'readonly'])->label('Cantidad de dias') ?>	

			<?= $form->field($model1, 'fechacreacion')->textInput(['maxlength' => 150, 'id'=>'txtfechaactual', 'value' => $fechaActual, 'class'=>"hidden"]) ?>

			<?= $form->field($model1, 'idtc')->textInput(['maxlength' => 150, 'id'=>'txtidtc', 'value' => $id, 'class'=>'hidden']) ?>
	   
	</div>

	<div style="text-align: center;">
		<div onclick="sumadias();" class="btn btn-primary"  method='post' id="botones1" >
            Calcular Dias
    	</div>  
		&nbsp;&nbsp;
		<?= Html::submitButton(Yii::t('app', 'Guardar Corte 3'),
	                ['class' => $model1->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
	                'data-toggle' => 'tooltip',
	                'title' => 'Guardar Cortes',
	                'onclick' => 'validarfechastext();',
	                'id'=>'modalButton7']) 
	    ?>
    </div>
    <br>

	<?php $form->end() ?> 
</div>
<br>
<br>
<br>
<div class="formularios-form">
	<table style="margin: auto;">
	<caption>Tabla cortes</caption>
		<tr>
			<th scope="col" style="width: 70px;">
				<em class='fas fa-check-circle' style='font-size:48px;color:green'></em>
			</th>
			<td style="width: 70px;">
				<em class='fas fa-check-circle' style='font-size:48px;color:green'></em>
			</td>
			<td style="width: 70px;">
				<em class='fas fa-check-circle' style='font-size:48px'></em>
			</td>
			<td style="width: 70px;">
				<em class='fas fa-check-circle' style='font-size:48px'></em>
			</td>
		</tr>	
		<tr>
			<td>
				Corte 1
			</td>
			<td>
				Corte 2
			</td>
			<td>
				Corte 3
			</td>
			<td>
				Corte 4
			</td>
		</tr>	
	</table>	
</div>

<script type="text/javascript">
	function validarfechastext(){
		var text1 = document.getElementById("txttipoCorte").value;
		var text2 =document.getElementById("txttdiastcs").value;
		var fecha1 = document.getElementById("tiposdecortes-fechainiciotcs").value;
		var fecha2 = document.getElementById("tiposdecortes-fechafintcs").value;
		var cantdias = document.getElementById("txtcantdiastcs").value;

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
	};

	function sumadias(){
		var checke = "<?php echo $incluirsabados; ?>";
		console.log(checke);

		// var fecha1 = new Date(document.getElementById("tiposdecortes-fechainiciotcs").value);
		// var fecha2 = new Date(document.getElementById("tiposdecortes-fechafintcs").value);

		var figeneral = document.getElementById("id_figeneral").value;
		var ffgeneral = document.getElementById("id_ffgeneral").value;

		var cfecha1 = document.getElementById("tiposdecortes-fechainiciotcs").value;
		var cfecha2 = document.getElementById("tiposdecortes-fechafintcs").value;

		var fecha1 = cfecha1.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');
		var fecha2 = cfecha2.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');
		
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

		if (cfecha1 < figeneral) {
				event.preventDefault();
				swal.fire("!!! Advertencia !!!","La fecha inicio corte 1 no puede ser menor a la fecha inicio de la general","warning");
				return;				
		}else{
			if (cfecha2 < figeneral) {
					event.preventDefault();
					swal.fire("!!! Advertencia !!!","La fecha fin corte 1 no puede ser menor a la fecha inicio de la general","warning");
					return;					
			}
		}

		if (cfecha2 > ffgeneral) {		
				event.preventDefault();
				swal.fire("!!! Advertencia !!!","La fecha fin corte 1 no puede ser mayor a la fecha fin de la general","warning");
				return;					
		}else{
			if (cfecha1 > ffgeneral) {
					event.preventDefault();
					swal.fire("!!! Advertencia !!!","La fecha inicio corte 1 no puede ser mayor a la fecha fin de la general","warning");
					return;							
			}
		}

		var afecha1 = fecha1.split("/");
		var afecha2 = fecha2.split("/");

		var ffecha1 = Date.UTC(afecha1[2],afecha1[1]-1,afecha1[0]);
		//console.log(ffecha1);
		var ffecha2 = Date.UTC(afecha2[2],afecha2[1]-1,afecha2[0]);
		//console.log(ffecha2);

		var dif = ffecha2 - ffecha1;
		//console.log(dif);
		var diascontados = Math.round(dif / (1000 * 60 *60 *24)); 
		var contardias = diascontados + 1;

		//--------------------------------------------------------------------------------
		var bfecha1 = new Date(document.getElementById("tiposdecortes-fechainiciotcs").value);	
		console.log(bfecha1.getDay());

		var cuentaFinde = 0; //N�mero de Domingos
    	var array = new Array(contardias);

    	if (checke != 1) {
	     	for (var i=0; i < contardias; i++) 
		    {
		        //0 => Domingo - 6 => S�bado
		        if (bfecha1.getDay() == 6 || bfecha1.getDay() == 5) {
		            cuentaFinde++;
		        }
		        bfecha1.setDate(bfecha1.getDate() + 1);
		    }	
    	}else{
	     	for (var i=0; i < contardias; i++) 
		    {
		        //0 => Domingo - 6 => S�bado
		        if (bfecha1.getDay() == 6) {
		            cuentaFinde++;
		        }
		        bfecha1.setDate(bfecha1.getDate() + 1);
		    }	    		
    	}	
		//--------------------------------------------------------------------------------
		console.log(contardias);
		console.log(cuentaFinde);

		document.getElementById("txtcantdiastcs").value = contardias;
	};
</script>