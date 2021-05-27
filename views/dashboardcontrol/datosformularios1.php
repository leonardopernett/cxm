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


    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

    // $varAnio = date('Y');
    // $varBeginYear = $varAnio.'-01-01';
    // $varLastYear = $varAnio.'-12-31';
    $varBeginYear = '2015-01-01';
    $varLastYear = '2025-12-31';

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

    $querys =  new Query;
    $querys     ->select(['tbl_arbols.id as ArbolID','tbl_arbols.name as ArbolName'])->distinct()
                ->from('tbl_control_volumenxcliente')
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_control_volumenxcliente.idservicio = tbl_arbols.id');                    
    $command = $querys->createCommand();
    $query = $command->queryAll();

    $listData = ArrayHelper::map($query, 'ArbolID', 'ArbolName');

    $txtvarServicio = $txtIdSevicio; 


?>
<div class="capaCero" style="display: inline">
    <a id="dlink" style="display:none;"></a>
    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar a Excel</button>
</div>

<div class="capaUno" style="display: none">
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <thead>
            <th class="text-center"><?= Yii::t('app', 'Formularios') ?></th>
            <?php
                $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                foreach ($varMonthYear as $key => $value) {
                    $varMonth = $value['CorteMes'];
                    $varYear = $value['CorteYear'];
            ?>
                <th class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
            <?php
                    }
            ?>
            <th class="text-center"><?= Yii::t('app', 'Promedio '.$varYear) ?></th>
        </thead>
        <tbody>
            <?php   

                foreach ($varFormularios as $key => $value) {
            $txtNameForm = $value['nombreservicio'];
                    $txtFormulario = $value['clientecategoria'];
            ?>
            <tr>
                <td class="text-center"><?php echo $txtNameForm; ?></td>
                <?php
                    $varMeses = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                    $varSumPromedio = 0;
                    foreach ($varMeses as $key => $value) {
                        $txtMes1 = $value['mesyear'];

                        if ($txtvarServicio == '17' || $txtvarServicio == '118') {
                            $varInicioF = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where mesyear = '$txtMes1' and idgrupocorte = 1")->queryScalar();
                            //$varInicio = $varInicioF.' 05:00:00';
                            $varInicio = $txtMes1.' 05:00:00';

                            $varFinF = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where mesyear = '$txtMes1' and idgrupocorte = 1")->queryScalar();
                            // $varFecha = date('Y-m-d',strtotime($varFinF."+ 1 days"));
                            // $varFin = $varFecha.' 05:00:00';
                            $varFecha = date('Y-m-d',strtotime($txtMes1."+ 1 month"));
                            $varFin = $varFecha.' 05:00:00';

                            $varcount = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where servicio like '%$txtFormulario%' and idcategoria = '2681' and fechallamada between '$varInicio' and '$varFin'")->queryScalar();

                            $varSumPromedio = $varSumPromedio + $varcount;
                        }else{
                            if ($txtvarServicio == '237' || $txtvarServicio == '1358' || $txtvarServicio == '105' || $txtvarServicio == '8' || $txtvarServicio == '99') {
                                $varInicioF = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where mesyear = '$txtMes1' and idgrupocorte = 2")->queryScalar();
                                //$varInicio = $varInicioF.' 05:00:00';
                                $varInicio = $txtMes1.' 05:00:00';

                                $varFinF = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where mesyear = '$txtMes1' and idgrupocorte = 2")->queryScalar();
                                // $varFecha = date('Y-m-d',strtotime($varFinF."+ 1 days"));
                                // $varFin = $varFecha.' 05:00:00';
                                $varFecha = date('Y-m-d',strtotime($txtMes1."+ 1 month"));
                                $varFin = $varFecha.' 05:00:00';

                                $varcount = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where servicio like '%$txtFormulario%' and idcategoria = '2681' and fechallamada between '$varInicio' and '$varFin'")->queryScalar();
                                $varSumPromedio = $varSumPromedio + $varcount;
                            }else{
                                $varInicioF = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where mesyear = '$txtMes1' and idgrupocorte = 3")->queryScalar();
                                //$varInicio = $varInicioF.' 05:00:00';
                                $varInicio = $txtMes1.' 05:00:00';
                                
                                $varFinF = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where mesyear = '$txtMes1' and idgrupocorte = 1")->queryScalar();
                                //$varFin = $varFinF .' 05:00:00';
                                $varFecha = date('Y-m-d',strtotime($txtMes1."+ 1 month"));
                                $varFin = $varFecha.' 05:00:00';


                                $varcount = Yii::$app->db->createCommand("select count(*) from tbl_dashboardspeechcalls where servicio like '%$txtFormulario%' and idcategoria = '2681' and fechallamada between '$varInicio' and '$varFin'")->queryScalar(); 
                                $varSumPromedio = $varSumPromedio + $varcount;                               
                            }
                        }
                ?>
                    <td class="text-center"><?php echo $varcount; ?></td>
                <?php 
                    } 
                ?>
                <td class="text-center"><?php echo round(($varSumPromedio*100)/6); ?></td>
            </tr>
            <?php
                }
            ?>
        </tbody>
    </table>
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
            document.getElementById("dlink").download = "DashBoard Productividad/Valoración";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Control Procesos', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>