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

$varIdtc = Yii::$app->db->createCommand("select idtc from tbl_control_procesos where anulado = 0 and id = $txtid")->queryScalar();

$varRango = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where anulado = 0 and idtc = $varIdtc")->queryScalar();

$varfechainicio = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where anulado = 0 and idtc = $varIdtc")->queryScalar();
$varfechafin = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where anulado = 0 and idtc = $varIdtc")->queryScalar();

$varListEscalamientos = Yii::$app->db->createCommand("select * from tbl_plan_escalamientos where anulado = 0 and tecnicolider = $txtevaluados_id and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><i class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></i> Ver escalamientos:</label>
            	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
            		<thead>    			
            			<th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Corte"; ?></label></th>
            			<th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tipo Corte"; ?></label></th>
            			<th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Justificación"; ?></label></th>
            			<th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Comentarios"; ?></label></th>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Asesor"; ?></label></th>
            			<th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Estado"; ?></label></th>    			
            		</thead>
            		<tbody>
            			<?php
            				foreach ($varListEscalamientos as $key => $value) {
            					$varidtcts = $value['idtcs'];
            					$vanameidtcs = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where idtcs = $varidtcts")->queryScalar();
            					$varEstado = $value['Estado'];
            					$txtestado = null;
            					if ($varEstado == 0) {
            						$txtestado = "Abierto";
            					}else{
            						if ($varEstado == 1) {
            							$txtestado = "Aprobado";
            						}else{
            							if ($varEstado == 2) {
            								$txtestado = "No Aprobado";
            							}
            						}
            					}

                                $varidasersor = $value['asesorid'];
                                if ($varidasersor != null) {
                                    $varnameasesor = Yii::$app->db->createCommand("select name from tbl_evaluados where id = $varidasersor")->queryScalar();
                                }else{
                                    $varnameasesor = null;
                                }
                                
            			?>
            				<tr>
        	      				<td><label style="font-size: 12px;"><?php echo  $varRango; ?></label></td>
        	      				<td><label style="font-size: 12px;"><?php echo  $vanameidtcs; ?></label></td>
        	      				<td><label style="font-size: 12px;"><?php echo  $value['justificacion']; ?></label></td>
        	      				<td><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varnameasesor; ?></label></td>
        	      				<td><label style="font-size: 12px;"><?php echo  $txtestado; ?></label></td>
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
