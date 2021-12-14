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

$this->title = 'Ver Tipo de Corte';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';



$numdias  = Yii::$app->db->createCommand("select sum(cantdiastcs) from tbl_tipos_cortes where idtc = '$CorteID'")->queryScalar();


?>
<br>
<div class="page-header" >
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
</div> 
<div class="control-procesos-index">
	<?php $form = ActiveForm::begin([
		'layout' => 'horizontal',
		'fieldConfig' => [
			'inputOptions' => ['autocomplete' => 'off']
		  ]
		]); ?>

		<?= $form->field($model, "tipocortetc")->textInput(['readonly' => 'readonly', 'id' => 'txttipo_id'])->label('Tipo de corte') ?> 

		<?= $form->field($model, "diastc")->textInput(['readonly' => 'readonly', 'id' => 'txtdiastc'])->label('Dias del corte') ?> 

		<?= $form->field($model, "fechainiciotc")->textInput(['readonly' => 'readonly', 'id' => 'txtfechainiciotc'])->label('Fecha inicio') ?> 

		<?= $form->field($model, "fechafintc")->textInput(['readonly' => 'readonly', 'id' => 'txtfechafintc'])->label('Fecha fin') ?>

		<?= $form->field($model, "cantdiastc")->textInput(['readonly' => 'readonly', 'id' => 'txtcantdiastc', 'value' => $numdias])->label('Cantidad de dias') ?> 
		&nbsp;&nbsp;

	<div style="margin: auto;">  
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
	        ],
	    ]); 
	    ?>

    <?php $form->end() ?>
</div>
&nbsp;&nbsp;
<div align="center">
		<label>Dias festivos existentes entre la fecha inicio y fecha fin: </label><label id="resultId"> </label>
</div>

<script type="text/javascript">
	var diasCant = "<?php echo $numdias ?>";
	console.log(diasCant);

	var meses2 = document.getElementById("txtfechafintc").value.split("-")[1];
	console.log(meses2);

	var idtipocort = document.getElementById("txttipo_id").value;

	var festivo = 0;

		     
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
	
	if(idtipocort == "Corte Marzo - Grupo Bancolombia (Inactivo)"){
		document.getElementById("txtcantdiastc").value = 18;

		document.getElementById("resultId").innerHTML = 0;
	}
	else
	{
		if(idtipocort == "Corte Abril - Grupo Bancolombia"){
			document.getElementById("txtcantdiastc").value = 18;
			
			document.getElementById("resultId").innerHTML = 2;
		}
		else
		{
			if(idtipocort == "Corte Abril - Directv"){
				document.getElementById("txtcantdiastc").value = diasCant;

				document.getElementById("resultId").innerHTML = 0;
			}			
			else
			{
				//document.getElementById("txtcantdiastc").value = diasCant - festivo;

				document.getElementById("resultId").innerHTML = 0;				
			}
		}

	}

</script>