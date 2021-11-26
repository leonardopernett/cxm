<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use app\models\Tipocortes;
use yii\helpers\ArrayHelper;
use yii\bootstrap\modal;
use miloschuman\highcharts\Highcharts;

$this->params['breadcrumbs'][] = ['label' => 'Seguimiento del tecnico', 'url' => ['index']];
$this->title = 'Grafica Global Equipo';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';   

$varMesCorte = $txtcorte; 

?>

<script src="../../js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<div class="text-center" style="text-align:left;">
 
    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>    

    <?= Html::a('Ver por Mes Actual',  ['formglobal'], ['class' => 'btn btn-success',
            'style' => 'background-color: #337ab7',
            'data-toggle' => 'tooltip',
            'title' => 'Verificar Detalle']) 
    ?> 

    <?= Html::a('Ver por Tipo Corte',  ['formglobal2'], ['class' => 'btn btn-success',
            'style' => 'background-color: #337ab7',
            'data-toggle' => 'tooltip',
            'title' => 'Verificar Detalle']) 
    ?>     
</div>

<div class="text-center" style="text-align:right;">
    <?= Html::a('Verificar Detalle',  ['detallemescorte'], ['class' => 'btn btn-success',
            'style' => 'background-color: #4298b4',
            'data-toggle' => 'tooltip',
            'title' => 'Verificar Detalle']) 
    ?>  
</div>

<div class="row">
  <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?> 

  <div id="container" style="height: 400px; margin-top: 1em">
  </div>

  <?php
    $listadoData = $data;
    
    $titulos = array();
    $datos = array();
    for ($n=0; $n < count($data) ; $n++) { 
        array_push($titulos, $data[$n]["fecha"]); 
        array_push($datos, $data[$n]["total"]); 
    }
  ?>

  <div align="center">
    <h6><strong><p style="color:red;">!!! Advertencia !!! </p></strong></h6>
    <h7><strong>Si no se visualiza la grafica, por favor habilitar los scribpts en la pagina actual.</strong></h7>
  </div>

  <?php $form->end() ?>
</div>

<script type="text/javascript">
	$(function() {

		var listado = '<?php echo implode($titulos,","); ?>';
		    listado = listado.split(",");

		  $('#container').highcharts({
		    chart: {
		        type: 'column'
		    },
		    title: {
		      text: 'Detalle en Grafica -- Global del Equipo - ' + '<?php echo $varMesCorte; ?> --' 
		    },
 
		    subtitle: {
		      text: '<?php echo "Coordinador a cargo: $nameSessions"; ?>'
		    },

		    yAxis: {
		      title: {
		        text: 'Cantidad Valoraciones Realizadas'
		      }
		    },        

		    xAxis: {
		          categories: listado,
		          title: {
		              text: null
		          }
		        },

		    series: [{
		      name: 'Totalidad: ',
		      data: [<?= join($datos, ',')?>]
		    }]
		  });

		  Highcharts.getOptions().exporting.buttons.contextButton.menuItems.push({
		    text: 'Additional Button',
		    onclick: function() {
		      alert('OK');
		      /*call custom function here*/
		    }
		  });
	});
</script>