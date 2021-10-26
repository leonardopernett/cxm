<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $txtlistrespuestas = Yii::$app->db->createCommand("select * from tbl_evaluacion_respuestas2 where anulado = 0")->queryAll();

?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></em> Ver respuestas creadas:</label>
            	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
					<caption>Tabla datos</caption>
            		<thead>    			
            			<th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Respuesta"; ?></label></th>
            			<th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Valor"; ?></label></th>
            			<th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Evaluaci�n"; ?></label></th>  			
            		</thead>
            		<tbody>
            			<?php
            				foreach ($txtlistrespuestas as $key => $value) {
                                $varIdEva = $value['idevaluacionnombre'];
                                $varnameeva = Yii::$app->db->createCommand("select nombreeval from tbl_evaluacion_nombre where anulado = 0 and idevaluacionnombre = $varIdEva")->queryScalar();

                        ?>
            				<tr>
        	      				<td><label style="font-size: 12px;"><?php echo  $value['namerespuesta']; ?></label></td>
        	      				<td><label style="font-size: 12px;"><?php echo  $value['valor']; ?></label></td>
        	      				<td><label style="font-size: 12px;"><?php echo  $varnameeva; ?></label></td>
        	      			</tr>
            			<?php
            				}
            			?>
            		</tbody>
            	</table>
            </div>
        </div>
    </div>
</div>
