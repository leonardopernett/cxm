<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Seguimiento Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

	$sessiones1 = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones1.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $listadoData = $data;
    
    $titulos = array();
    $datos = array();
    for ($n=0; $n < count($data) ; $n++) { 
        array_push($titulos, $data[$n]["fecha"]); 
        array_push($datos, $data[$n]["total"]); 
    }
?>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
            	<div id="containervaloracion" class="highcharts-container" style="height: 300px;"></div>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">

    $(function() {

        var Listado = "<?php echo implode($titulos,",");?>";
        Listado = Listado.split(",");
        console.log(Listado);

        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

        $('#containervaloracion').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de valoraciones',
              }
            },   

            title: {
              text: 'Valoraciones del equipo sobre el mes actual'
            },  

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  },
                  crosshair: true
            },

            
            series: [{
              name: 'Total valoraciones',
              data: [<?= join($datos, ',')?>],
              color: '#FBCE52'
            }]
        });
    });
</script>