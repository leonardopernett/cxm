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

$this->title = 'Actualizar Tipo de Corte';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

$rtaCantd = $nameVal;
?>
<br>
<div class="page-header" >
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
</div> 
<div class="control-procesos-index">
	<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

		<?= $form->field($model, 'incluir')->checkbox(array('label'=>'', 'class'=>"hidden"))?>

		<?= $form->field($model, 'tipocortetc')->textInput(['maxlength' => 150, 'id'=>'txttipoCorte', 'readonly' => 'readonly'])->label('Tipo de corte') ?>

		<?= $form->field($model, 'diastc')->textInput(['maxlength' => 150, 'id'=>'txtdiastc', 'readonly' => 'readonly'])->label('Dias del corte') ?> 

		<?= $form->field($model, 'fechainiciotc')->label('Fecha inicio')->widget(DatePicker::className(),['dateFormat' => 'yyyy-MM-dd', 'clientOptions' => ['yearRange' => '-115:+0', 'changeYear' => true], 'options' => ['class' => 'form-control', 'style' => 'width:25%']]) ?>

		<?= $form->field($model, 'fechafintc')->label('Fecha fin')->widget(DatePicker::className(),['dateFormat' => 'yyyy-MM-dd', 'clientOptions' => ['yearRange' => '-115:+0', 'changeYear' => true], 'options' => ['class' => 'form-control', 'style' => 'width:25%']]) ?>

		<?= $form->field($model, 'cantdiastc')->textInput(['maxlength' => 150, 'id'=>'txtcantdias', 'readonly' => 'readonly'])->label('Cantidad de dias') ?>  

		<?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 150, 'id'=>'txtfechaactual', 'class'=>"hidden"]) ?>

		<?= $form->field($model, 'anulado')->textInput(['maxlength' => 1, 'value' => 0, 'class'=>"hidden", 'label'=>""]) ?>
		&nbsp;&nbsp;

	<div style="text-align: center;"> 
		<div onclick="sumadias();" class="btn btn-primary"  method='post' id="botones1" >
	        Calcular Dias
	    </div>   
	&nbsp;&nbsp;	    
		    <?= Html::submitButton('Actualizar', ['class' => 'btn btn-primary', 'id'=>'btn_submit', 'style' => 'background-color: #4298b4', 'onclick' => 'validarfechastext();'] ) ?>
	&nbsp;&nbsp;
		    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    		?>
    &nbsp;&nbsp;
	</div>

	    <?= GridView::widget([
	        'dataProvider' => $dataProvider,
	        //'filterModel' => $searchModel,
	        'columns' => [
	            [
	                'attribute' => 'Corte',
	                'value' => 'cortetcs',
	            ],
	            [
	                'attribute' => 'Fecha inicio',
	                'value' => 'fechainiciotcs',
	            ],
	            [
	                'attribute' => 'Fecha fin',
	                'value' => 'fechafintcs',
	            ],
	            [
	                'attribute' => 'Dias',
	                'value' => 'diastcs',
	            ],
	            [
	                'attribute' => 'Cantidad de dias',
	                'value' => 'cantdiastcs',
	            ],
	            [
	                'class' => 'yii\grid\ActionColumn',
	                'headerOptions' => ['style' => 'color:#337ab7'],
	                'template' => '{update}',
	                'buttons' => 
	                [
	                    'update' => function ($url, $model) {
	                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>',['update2', 'idtcs' => $model->idtcs], [
	                            'class' => '',
	                            'data' => [
	                                'method' => 'post',
	                            ],
	                        ]);
	                    }
	                ]
	              
	        	],  
	        ],
	    ]); 
	    ?>

    <?php $form->end() ?>
</div>

<script type="text/javascript">
	function validarfechastext(){
		var text1 = document.getElementById("txttipoCorte").value;
		var text2 =document.getElementById("txtdiastc").value;
		var fecha1 = document.getElementById("tipocortes-fechainiciotc").value;
		var fecha2 = document.getElementById("tipocortes-fechafintc").value;
		var cantdias = document.getElementById("txtcantdias").value;

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

		if (cantdias != brtaCantD) {
			event.preventDefault();
			swal.fire("!!! Advertencia !!!","Debes actualizar las fechas desde el corte 1 hasta el corte 4.","warning");
			return;
		}

	};

	function sumadias(){
		var varincluir = document.getElementById("tipocortes-incluir").checked;

		var cfecha1 = document.getElementById("tipocortes-fechainiciotc").value;
		var cfecha2 = document.getElementById("tipocortes-fechafintc").value;

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

		var afecha1 = fecha1.split("/");
		var afecha2 = fecha2.split("/");

		var ffecha1 = Date.UTC(afecha1[2],afecha1[1]-1,afecha1[0]);
		//console.log(ffecha1);
		var ffecha2 = Date.UTC(afecha2[2],afecha2[1]-1,afecha2[0]);
		//console.log(ffecha2);

		var dif = ffecha2 - ffecha1;
		//console.log(dif);
		var contardias1 = Math.round(dif / (1000 * 60 *60 *24));

		//--------------------------------------------------------------------------------

		var meses = document.getElementById("tipocortes-fechainiciotc").value.split("-")[1];	
		var bfecha1 = new Date(document.getElementById("tipocortes-fechainiciotc").value);	

		var cuentaFinde = 0; //Número de Domingos
    	var array = new Array(contardias1);

    	if (varincluir != true) {
	     	for (var i=0; i < contardias1; i++) 
		    {
		        //0 => Domingo - 6 => Sábado
		        if (bfecha1.getDay() == 6 || bfecha1.getDay() == 5) {
		            cuentaFinde++;
		        }
		        bfecha1.setDate(bfecha1.getDate() + 1);
		    }	
    	}else{
	     	for (var i=0; i < contardias1; i++) 
		    {
		        //0 => Domingo - 6 => Sábado
		        if (bfecha1.getDay() == 6) {
		            cuentaFinde++;
		        }
		        bfecha1.setDate(bfecha1.getDate() + 1);
		    }	    		
    	}		

	    //--------------------------------------------------------------------------------

     var festivo = 0;
	    switch(meses){
	    	case "01":
	    		festivo = 0;
	    		break;
	    	case "02":
	    		festivo = 0;
	    		break;
	    	case "03":
	    		festivo = 1;
	    		//cuentaFinde = cuentaFinde + 1;
	    		break;
	    	case "04":
	    		festivo = 2;	    		
	    		break;
	    	case "05":
	    		festivo = 1;
	    		//cuentaFinde = cuentaFinde - 1;
	    		break;
	    	case "06":
	    		festivo = 2;	    		
	    		break;
	    	case "07":
	    		festivo = 2;
	    		//cuentaFinde = cuentaFinde - 1;
	    		break;
	    	case "08":
	    		festivo = 2;
	    		break;
	    	case "09":
	    		festivo = 0;
	    		//cuentaFinde = cuentaFinde + 1;
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

		//--------------------------------------------------------------------------------
		console.log(meses);
		console.log(contardias1);
		console.log(cuentaFinde);
		console.log(festivo);

		document.getElementById("txtcantdias").value = contardias1 + 1 - cuentaFinde - festivo - 2;
	};
</script>
