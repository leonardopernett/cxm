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
use \app\models\ControlParams;
use kartik\export\ExportMenu;

$this->params['breadcrumbs'][] = ['label' => 'Seguimiento del técnico', 'url' => ['index']];
$this->title = 'Comparar Equipo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $fechaActual = date("Y-m-d");

?>

    <?= Html::a('Pagina principal',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>

<div class="control-procesos-index">
    <br>

    <table class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th class="text-center" style="font-size:12px;">Valorador</th>
                <th class="text-center" style="font-size:12px;">PCRC o Servicio</th>
                <th class="text-center" style="font-size:12px;">Dimensiones</th>
                <th class="text-center" style="font-size:12px;">Cantidad de Valoracion</th>
                <th class="text-center" style="font-size:12px;">Tipo de Corte</th>
            </tr>
        </thead>

        <tbody>
            <?php 
            	$listData = $data;
            	foreach ($listData as $key => $value) {
           	?>
            	<tr class="text-center">
	                <td><p><?php echo $varIdArbol = $value['usua_nombre'];?></p></td>
	                <td><?php echo $varIdArbol = $value['name'];?></td>
	                <td><?php echo $varIdArbol = $value['dimensions'];?></td>
	                <td><?php echo $varIdArbol = $value['cant_valor'];?></td>
	                <td><?php echo $varIdArbol = $value['tipo_corte'];?></td>
            	</tr>
	        <?php } ?>            	
        </tbody>
    </table>


</div>