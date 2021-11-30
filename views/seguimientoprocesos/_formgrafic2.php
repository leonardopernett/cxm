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
$this->title = 'Grafica por Valoradores';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $nombreValorador = $nameVal;

?>

    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>

<script src="../../js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<div class="row">
  <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?> 

  <div id="container" style="height: 400px; margin-top: 1em">
  </div>

  <?php
    $listadoData = $data;
    
    $titulos = array();
    $datos = array();
    for ($n=0; $n < count($data) ; $n++) { 
        array_push($titulos, $data[$n]["name"]); 
        array_push($datos, $data[$n]["Total"]); 
    }

  ?>

  <div align="center">
    <h6><strong><p style="color:red;">!!! Advertencia !!! </p></strong></h6>
    <h7><strong>Si no se visualiza la grafica, por favor habilitar los scripts en la pagina actual.</strong></h7>
  </div>
<br>
<br>
<br>
   <div class="panel panel-primary">
            <div class="panel-heading">Importante...</div>
            <div class="panel-body">Esta gráfica aparece el detalle del total de servicios o PCRC asignados al coordinador CX o responsable del tecnico  de ese mes.</div>            
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
      text: 'Detalle en Grafica -- Cantidad de Valoraciones Asignadas por Servicio o PCRC --'
    },

    subtitle: {
      text: '<?php echo $nombreValorador; ?>'
    },

    yAxis: {
      title: {
        text: 'Cantidad de dimensiones por PCRC asignado'
      }
    },        

    xAxis: {
          categories: listado,
          title: {
              text: null
          }
        },

    series: [{
      name: 'PCRC o Servicios',
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