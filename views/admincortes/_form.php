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
use yii\helpers\ArrayHelper;
use \app\models\Grupocortes;

$this->title = 'Agregar Corte General';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $fechaActual = date('Y-m-d');
    $fechaYear = date('Y');

    $variables = Grupocortes::find()
                ->where(['anulado' => 'null'])
                ->all();
    $listData = ArrayHelper::map($variables,  'idgrupocorte', 'nomgrupocorte');

?>
<br>
<div class="page-header" >
    <h3 class="text-center" style="color:#100048;"><?= Html::encode($this->title) ?></h3>
</div>

<div class="formularios-form">
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

		<?= $form->field($model, 'tipocortetc')->textInput(['maxlength' => 150, 'id'=>'txttipoCorte', 'class' => 'hidden'])?>

		<?= $form->field($model, 'incluir')->checkbox(array('label'=>''))->label('Incluir sabados'); ?>

		<?php $var2 = [$fechaYear.'-01-01' => 'Enero', $fechaYear.'-02-01' => 'Febrero', $fechaYear.'-03-01' => 'Marzo', $fechaYear.'-04-01' => 'Abril', $fechaYear.'-05-01' => 'Mayo', $fechaYear.'-06-01' => 'Junio', $fechaYear.'-07-01' => 'Julio', $fechaYear.'-08-01' => 'Agosto', $fechaYear.'-09-01' => 'Septiembre', $fechaYear.'-10-01' => 'Octubre', $fechaYear.'-11-01' => 'Noviembre', $fechaYear.'-12-01' => 'Diciembre']; ?>
		
		<?= $form->field($model, "mesyear")->dropDownList($var2, ['prompt' => 'Seleccione mes...', 'id'=>"id_Fecha"])->label('Mes Corte') ?>

		<?= $form->field($model, 'idgrupocorte')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'TipoCort', 'onclick'=>'concatenarId();'])->label('Tipo Corte') ?> 

		<?= $form->field($model, 'diastc')->textInput(['maxlength' => 150, 'id'=>'txtdiastc'])->label('Dias del corte') ?> 

		<?= $form->field($model, 'fechainiciotc')->label('Fecha inicio')->widget(DatePicker::className(),['dateFormat' => 'yyyy-MM-dd', 'clientOptions' => ['yearRange' => '-115:+0', 'changeYear' => true], 'options' => ['class' => 'form-control', 'style' => 'width:25%'] ]) ?>

		<?= $form->field($model, 'fechafintc')->label('Fecha fin')->widget(DatePicker::className(),['dateFormat' => 'yyyy-MM-dd', 'clientOptions' => ['yearRange' => '-115:+0', 'changeYear' => true], 'options' => ['class' => 'form-control', 'style' => 'width:25%']]) ?>

		<?= $form->field($model, 'cantdiastc')->textInput(['maxlength' => 150, 'id'=>'txtcantdias', 'readonly' => 'readonly'])->label('Cantidad de dias') ?>  

		<?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 150, 'id'=>'txtfechaactual', 'value' => $fechaActual, 'class'=>"hidden"]) ?>

		<?= $form->field($model, 'anulado')->textInput(['maxlength' => 1, 'value' => 0, 'class'=>"hidden", 'label'=>""]) ?>
</div>
<br>
<div style="text-align: center;"> 
	<div onclick="sumadias();" class="btn btn-primary"  method='post' id="botones1" >
            Calcular Dias
    </div>  
    &nbsp;&nbsp;
    <?= Html::submitButton(Yii::t('app', 'Guardar Corte'),
                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                'data-toggle' => 'tooltip',
                'title' => 'Guardar Corte',
                'onclick' => 'validarfechastext();',
                'id'=>'modalButton6']) 
    ?>
    &nbsp;&nbsp;
	 <a class="btn btn-default soloCancelar" style="background-color:#707372" data-toggle="tooltip" title="Aceptar" href="../admincortes/index"> Regresar </a>	
</div>

<?php $form->end() ?> 

<script type="text/javascript">
	function concatenarId(){
		var varCorte = document.getElementById("TipoCort");
		var varGetCorte = varCorte.options[varCorte.selectedIndex].text;

		var varMes = document.getElementById("id_Fecha");
		var varGetMes = varMes.options[varMes.selectedIndex].text;
		
		var varconcatenar = 'Corte '+varGetMes+' - '+varGetCorte;
		console.log(varconcatenar);

		document.getElementById("txttipoCorte").value = varconcatenar;
	};

	function validarfechastext(){
		var text1 = document.getElementById("txttipoCorte").value;
		var text2 =document.getElementById("txtdiastc").value;
		var fecha1 = document.getElementById("tipocortes-fechainiciotc").value;
		var fecha2 = document.getElementById("tipocortes-fechafintc").value;
		var cantdias = document.getElementById("txtcantdias").value;


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
		var checke = document.getElementById("tipocortes-incluir").checked;
		
		var cfecha1 = document.getElementById("tipocortes-fechainiciotc").value;
		var cfecha2 = document.getElementById("tipocortes-fechafintc").value;

		var hoy = new Date();
		var dd = hoy.getDate();
		var mm = hoy.getMonth()+1;
		var yyyy = hoy.getFullYear();

		var mesactual = mm;

		var fecha1 = cfecha1.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');
		var fecha2 = cfecha2.replace(/^(\d{4})-(\d{2})-(\d{2})$/g,'$3/$2/$1');
		
		var meses = document.getElementById("tipocortes-fechainiciotc").value.split("-")[1];
		var meses2 = document.getElementById("tipocortes-fechafintc").value.split("-")[1];
		var diaMes = document.getElementById("tipocortes-fechafintc").value.split("-")[2];

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

		//if (meses < mesactual) {
		//	event.preventDefault();
		//	swal.fire("!!! Advertencia !!!","No es posible seleccionar mes anterior, seleccionar desde el mes actual en adelante.","warning");
		//	return;			
		//}else{
		//	if (meses2 < mesactual) {
		//			event.preventDefault();
		//			swal.fire("!!! Advertencia !!!","No es posible seleccionar mes anterior, seleccionar desde el mes actual en adelante.","warning");
		//			return;		
		//	}
		//}



		var afecha1 = fecha1.split("/");
		var afecha2 = fecha2.split("/");

		var ffecha1 = Date.UTC(afecha1[2],afecha1[1]-1,afecha1[0]);
		//console.log(ffecha1);
		var ffecha2 = Date.UTC(afecha2[2],afecha2[1]-1,afecha2[0]);
		//console.log(ffecha2);

		var dif = ffecha2 - ffecha1;
		//console.log(dif);
		var diascontados = Math.round(dif / (1000 * 60 *60 *24)); 
		var contardias1 = diascontados + 1;
		console.log(contardias1);

		//--------------------------------------------------------------------------------
			
		//var meses2 = document.getElementById("tipocortes-fechainiciotc").value.split("-")[2];
		var bfecha1 = new Date(document.getElementById("tipocortes-fechainiciotc").value);	
		console.log(bfecha1);

		var cuentaFinde = 0; //N�mero de Domingos
   	 	var array = new Array(contardias1);

    	if (checke != true) {
	     	for (var i=0; i < contardias1; i++) 
		    {
		        //0 => Domingo - 6 => S�bado
		        if (bfecha1.getDay() == 6 || bfecha1.getDay() == 5) {
		            cuentaFinde++;
		        }
		        bfecha1.setDate(bfecha1.getDate() + 1);
		    }	
    	}else{
	     	for (var i=0; i < contardias1; i++) 
		    {
		        //0 => Domingo - 6 => S�bado
		        if (bfecha1.getDay() == 6) {
		            cuentaFinde++;
		        }
		        bfecha1.setDate(bfecha1.getDate() + 1);
		    }	    		
    	}			

	    //--------------------------------------------------------------------------------		
	    console.log(cuentaFinde);
	    console.log(meses2);

	var festivo = 0;
	    if (diaMes != "01") {
			    switch(meses2){
			    	case "01":
			    		festivo = 0;
			    		break;
			    	case "02":
			    		festivo = 0;
			    		break;
			    	case "03":
			    		festivo = 1;			    		
			    		break;
			    	case "04":
			    		festivo = 2;	    		
			    		break;
			    	case "05":
			    		festivo = 1;
			    		break;
			    	case "06":
			    		festivo = 2;	    		
			    		break;
			    	case "07":
			    		festivo = 2;
			    		break;
			    	case "08":
			    		festivo = 2;
			    		break;
			    	case "09":
			    		festivo = 0;
			    		break;
			    	case "10":
			    		festivo = 1;
			    		break;
			    	case "11":
			    		festivo = 2;
			    		break;
			    	case "12":
			    		festivo = 1;
			    		break;
					default:
                      
                      break;
			    }
			}	

	    console.log(festivo);

	    document.getElementById("txtcantdias").value = contardias1;
	};
</script>