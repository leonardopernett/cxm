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

$this->title = 'Detalle Pcrc y Dimensiones';
$this->params['breadcrumbs'][] = ['label' => 'Seguimiento del t�cnico', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

?>
    <?= Html::a('Regresar',  ['searchpcrc'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>
<br>
<div class="page-header" >
    <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
</div> 
    <table align="center" border="1" class="egt table table-hover table-striped table-bordered">
	<caption>Tabla datos</caption>
        <tr>
            <th scope="col"><p>PCRC: </p><?php echo $varNomArbol; ?></th>
        </tr>
    </table>    
    <hr>
    <br>
   	<?= GridView::widget([
	        'dataProvider' => $dataProvider,
	        //'filterModel' => $searchModel,
	        'columns' => [
	        	// [
	        	// 	'class' => 'yii\grid\SerialColumn'
	        	// ],
	            [
	                'attribute' => 'Dimensiones',
	                'value' => 'dimensions',
	            ],
	            [
	                'attribute' => 'Metas',
	                'value' => 'cant_valor',
	            ],
	        ],
	    ]); 
	?>
	<br>
	<!-- <button type="submit" id="export_data" name="export_data" value="Export to excel" class="btn btn-info" style="background-color: #4298B4">Exrpotar a Excel</button> -->