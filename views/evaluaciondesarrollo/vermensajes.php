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

    $txtlistmensajes = Yii::$app->db->createCommand("select ec.namecompetencia, ef.mensaje, en.nombreeval FROM tbl_evaluacion_feedback_mensaje ef
                        INNER JOIN tbl_evaluacion_competencias ec ON ef.idevaluacioncompetencia = ec.idevaluacioncompetencia
                        INNER JOIN tbl_evaluacion_nombre en ON ef.idevaluacionnombre = en.idevaluacionnombre where ef.anulado = 0")->queryAll();

?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><i class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></i> Ver mensajes de feedback:</label>
            	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
            		<thead>    			
            			<th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Competencia"; ?></label></th>
            			<th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Mensaje"; ?></label></th>
            			<th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Evaluaci n"; ?></label></th>  			
            		</thead>
            		<tbody>
            			<?php
            				foreach ($txtlistmensajes as $key => $value) {
                               
                        ?>
            				<tr>
        	      				<td><label style="font-size: 12px;"><?php echo  $value['namecompetencia']; ?></label></td>
        	      				<td><label style="font-size: 12px;"><?php echo  $value['mensaje']; ?></label></td>
        	      				<td><label style="font-size: 12px;"><?php echo  $value['nombreeval']; ?></label></td>
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
