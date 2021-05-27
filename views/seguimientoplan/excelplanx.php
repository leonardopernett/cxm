<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

?>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-6">
            <a id="dlink" style="display:none;"></a>
            <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar</button>
        </div>
    </div>
</div>
<div id="capaDos" style="display: none">   
    <div class="row">
        <div class="col-md-12">
            <br>
            <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                <thead>
                    <tr>
                        <th colspan="22" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo  'Rango de fecha '.$varfechainicio.' - '.$varfechafin; ?></label></th>
                    </tr>
                    <tr>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cedula Responsable') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Responsable') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cedula Tecnico/Lider') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tecnico/Lider') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rol') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Meta') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Realizadas') ?></label></th>                        
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Novedades') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% Cumplimineto') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo de Corte') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Realizadas Corte 1') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Aprobadas Corte 1') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% Cumplimineto Corte 1') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Realizadas Corte 2') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Aprobadas Corte 2') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% Cumplimineto Corte 2') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Relizadas Corte 3') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Aprobadas Corte 3') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% Cumplimineto Corte 3') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Realizadas Corte 4') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Aprobadas Corte 4') ?></label></th>
                        <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% Cumplimineto Corte 4') ?></label></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $varMes = date("n");
                    $txtMes = null;
                    switch ($varMes) {
                        case '1':
                            $txtMes = "Enero";
                            break;
                        case '2':
                            $txtMes = "Febrero";
                            break;
                        case '3':
                            $txtMes = "Marzo";
                            break;
                        case '4':
                            $txtMes = "Abril";
                            break;
                        case '5':
                            $txtMes = "Mayo";
                            break;
                        case '6':
                            $txtMes = "Junio";
                            break;
                        case '7':
                            $txtMes = "Julio";
                            break;
                        case '8':
                            $txtMes = "Agosto";
                            break;
                        case '9':
                            $txtMes = "Septiembre";
                            break;
                        case '10':
                            $txtMes = "Octubre";
                            break;
                        case '11':
                            $txtMes = "Noviembre";
                            break;
                        case '12':
                            $txtMes = "Diciembre";
                            break;
                        default:
                            # code...
                            break;
                    }   
        
                        
                        foreach ($varlistaPlanx as $key => $value) { 
                            $varcumplicorte1 = 0;
                            $varcumplicorte2 = 0;
                            $varcumplicorte3 = 0;
                            $varcumplicorte4 = 0; 
                            $varmeta = $value['Meta'];
                            $varIdResponsable = $value['IdResponsable'];
                            $varRepsonsable = $value['Repsonsable'];
                            $varIdTecnico_Lider = $value['IdTecnico/Lider']; 
                            $varTecnico_Lider = $value['Tecnico/Lider']; 
                            $varRol = $value['Rol'];
                            $varMeta = $value['Meta'];

                            $varRealizadas = $value['Realizadas'];
                            $varTipoCorte = $value['TipoCorte'];                            
                            $varidevaluado = $value['idevaluado'];
                            $varRealizadas_Corte1 = $value['Realizadas_Corte1'];
                            $varAprobadas_Corte1 = $value['NovedadesAprobada_Corte1'];                            
                            $varRealizadas_Corte2 = $value['Realizadas_Corte2'];
                            $varAprobadas_Corte2 = $value['NovedadesAprobada_Corte2']; 
                            $varRealizadas_Corte3 = $value['Realizadas_Corte3'];
                            $varAprobadas_Corte3 = $value['NovedadesAprobada_Corte3']; 
                            $varRealizadas_Corte4 = $value['Realizadas_Corte4'];
                            $varAprobadas_Corte4 = $value['NovedadesAprobada_Corte4']; 
                            $varNovedades = $value['NovedadesAprobadas'];
    
                            
                            $novedad = Yii::$app->db->createCommand("select count(*) from tbl_plan_escalamientos where anulado = 0 and tecnicolider = $varidevaluado and Estado = 1 and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryScalar();
                            if ($varmeta != 0) {
if ($novedad >= $varmeta) {
                                    $novedad = $varmeta - 1;
                                }
                                $varcumplimiento = round(($varRealizadas / ($varmeta - $varNovedades)) * 100,2);

                                
                                $varcorts1 = round($varmeta / 4) - $varAprobadas_Corte1;
                                $varcorts2 = round($varmeta / 4) - $varAprobadas_Corte2;
                                $varcorts3 = round($varmeta / 4) - $varAprobadas_Corte3;
                                $varcorts4 = round($varmeta / 4) - $varAprobadas_Corte4;

                                if ($varcorts1 != 0) {
                                    $varcumplicorte1 = round(($varRealizadas_Corte1 / $varcorts1) * 100,2);
                                }else{
                                    $varcumplicorte1 = 0;
                                }

                                if ($varcorts2 != 0) {
                                    $varcumplicorte2 = round(($varRealizadas_Corte2 / $varcorts2) * 100,2);
                                }else{
                                    $varcumplicorte2 = 0;
                                }

                                if ($varcorts3 != 0) {
                                    $varcumplicorte3 = round(($varRealizadas_Corte3 / $varcorts3) * 100,2);
                                }else{
                                    $varcumplicorte3 = 0;
                                }

                                if ($varcorts4 != 0) {
                                    $varcumplicorte4 = round(($varRealizadas_Corte4 / $varcorts4) * 100,2);
                                }else{
                                    $varcumplicorte4 = 0;
                                }
                                
                                
                            }else{
                                $varcumplimiento = 0;
                                $varcumplicorte1 = 0;
                                $varcumplicorte2 = 0;
                                $varcumplicorte3 = 0;
                                $varcumplicorte4 = 0;
                            }
                            $varidtc = $value['idcorte'];
                            $varCero = 0;
                            

                            /*$txtidtc = Yii::$app->db->createCommand('select idtc from tbl_control_procesos where evaluados_id ='.$varidevaluado.' and tipo_corte like "%'.$txtMes.'%" and anulado ='.$varCero.'')->queryScalar();
                            $txtlistidtcs = Yii::$app->db->createCommand("select idtcs FROM tbl_tipos_cortes WHERE idtc = $txtidtc")->queryAll();
                            $vararrayidtcs = Array();
                                foreach ($txtlistidtcs as $key => $value){
                                array_push($vararrayidtcs, $value['idtcs']);
                            }
                            
                            
                            $txtlistacortes = implode("', '", $vararrayidtcs);
                            
                            $varsumagestion = Yii::$app->db->createCommand("SELECT SUM(estado) FROM tbl_plan_escalamientos WHERE tecnicolider = $varidevaluado AND estado = 1 AND idtcs in ('$txtlistacortes')")->queryScalar();
                            if ($varsumagestion == null){
                                $varsumagestion = 0;
                            }*/

                        
                    ?>
                        <tr>
                        
                            <td><label style="font-size: 12px;"><?php echo  $varIdResponsable; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varRepsonsable; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varIdTecnico_Lider; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varTecnico_Lider; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varRol; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varMeta; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varRealizadas; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNovedades; ?></label></td>  
                            <td><label style="font-size: 12px;"><?php echo  $varcumplimiento.'%'; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varTipoCorte; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varRealizadas_Corte1; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varAprobadas_Corte1; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varcumplicorte1; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varRealizadas_Corte2; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varAprobadas_Corte2; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varcumplicorte2; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varRealizadas_Corte3; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varAprobadas_Corte3; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varcumplicorte3; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varRealizadas_Corte4; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varAprobadas_Corte4; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varcumplicorte4; ?></label></td>
                        </tr>
                    <?php 
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script type="text/javascript" charset="UTF-8">
var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "Seguimiento Equipo de trabajo";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo Seguimiento', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>