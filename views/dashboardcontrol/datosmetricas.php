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

$this->title = 'DashBoard -- Métricas de Productividad Valoración --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Métricas de Productividad/Valoración';

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

    // $varAnio = date('Y');
    // $varBeginYear = $varAnio.'-01-01';
    // $varLastYear = $varAnio.'-12-31';  
    $varBeginYear = $varAnio.'-01-01';
    $varLastYear = $varAnio.'-12-31';   
    
    $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();  

    $varMonthYear2 = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll(); 


?>
<meta charset="ISO-8859-1">
<div class="capaCero" style="display: inline">
    <a id="dlink" style="display:none;"></a>
    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar a Excel</button>
</div>

<div class="capaUno" style="display: none">
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <thead>
            <tr>
                <th class="text-center" colspan="8"><h1><?= Yii::t('app', 'Dashboard Métricas de Productividad Valoración') ?></h1></th>
            </tr>
            <tr>
                <th class="text-center" colspan="8"></th>
            </tr>
            <tr>
                <th bgcolor="#5D92E2" class="text-center" colspan="8"><?= Yii::t('app', 'VOLUMEN DE GESTION') ?></th>
            </tr>
            <tr>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Nivel') ?></th>
                <?php
                    foreach ($varMonthYear as $key => $value) {
                        $varMonth = $value['CorteMes'];
                        $varYear = $value['CorteYear'];                    
                ?>
                    <th bgcolor="#5D92E2" class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
                <?php
                    }
                ?>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Promedio '.$varYear) ?></th> 
            </tr>
        </thead>
        <tbody>
            <?php
                $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 

                $varListMonth2 = Yii::$app->db->createCommand("select distinct mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' and tipocortetc not like '%$txtMes%' group by mesyear order by mesyear desc limit 6")->queryAll();                    

                $varFisrtDate = date($varListMonth2[0]["mesyear"]);
                $varLastDate = date($varListMonth2[5]["mesyear"]);
                $txtTotalAVG = null;                             

                foreach ($txtCiudades as $key => $value) {
                    $txtNameCity = $value['name'];
                    $varIdPcrc = $value['id'];

                    $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();


                    if ($varIdPcrc == 1) {
                        $txtQuery2 =  new Query;
                        $txtQuery2   ->select(['round(sum(tbl_control_volumenxcliente.cantidadvalor)/6)'])->distinct()
                                    ->from('tbl_control_volumenxcliente')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id in (2, 98)')
                                    ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varLastDate, $varFisrtDate]);                    
                        $command = $txtQuery2->createCommand();
                        $txtTotalAVG1 = $command->queryScalar();

                        $txtQuery22 =  new Query;
                        $txtQuery22   ->select(['round(sum(tbl_control_volumenxclienteS.cantidadvalorS)/6)'])->distinct()
                                    ->from('tbl_control_volumenxclienteS')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id in (2, 98)')
                                    ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varLastDate, $varFisrtDate]);                    
                        $command2 = $txtQuery22->createCommand();
                        $txtTotalAVG2 = $command2->queryScalar();  

                        $txtTotalAVG = $txtTotalAVG1 + $txtTotalAVG2;

                    }else{
                        $txtQuery2 =  new Query;
                        $txtQuery2   ->select(['round(sum(tbl_control_volumenxcliente.cantidadvalor)/6)'])->distinct()
                                    ->from('tbl_control_volumenxcliente')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                    ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varLastDate, $varFisrtDate]);                    
                        $command = $txtQuery2->createCommand();
                        $txtTotalAVG1 = $command->queryScalar(); 

                        $txtQuery22 =  new Query;
                        $txtQuery22   ->select(['round(sum(tbl_control_volumenxclienteS.cantidadvalorS)/6)'])->distinct()
                                    ->from('tbl_control_volumenxclienteS')
                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_control_volumenxclienteS.idservicio = tbl_arbols.id')
                                    ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                    ->andwhere(['between','tbl_control_volumenxclienteS.mesyear', $varFisrtDate, $varLastDate]);                    
                        $command22 = $txtQuery22->createCommand();
                        $txtTotalAVG2 = $command22->queryScalar(); 

                        $txtTotalAVG = $txtTotalAVG1 + $txtTotalAVG2;                    
                    }

            ?>
            <tr>
                <td bgcolor="#A7B3C5" class="text-center"><?php echo $txtNameCity; ?></td>

                <?php
                    $txtTotalMonth = null;
                    foreach ($varListMonth as $key => $value) {
                        $varListYear = $value['mesyear'];                     

                        if ($varIdPcrc == 1){
                            $txtQuery =  new Query;
                            $txtQuery   ->select(['sum(tbl_control_volumenxcliente.cantidadvalor)'])->distinct()
                                        ->from('tbl_control_volumenxcliente')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                                        ->where('tbl_arbols.arbol_id in (2, 98)')
                                        ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varListYear, $varListYear]);                    
                            $command = $txtQuery->createCommand();
                            $txtTotalMonth = $command->queryScalar();
                        }else{

                            $txtQuery =  new Query;
                            $txtQuery   ->select(['sum(tbl_control_volumenxcliente.cantidadvalor)'])->distinct()
                                        ->from('tbl_control_volumenxcliente')
                                        ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_control_volumenxcliente.idservicio = tbl_arbols.id')
                                        ->where('tbl_arbols.arbol_id = '.$varIdPcrc.'')
                                        ->andwhere(['between','tbl_control_volumenxcliente.mesyear', $varListYear, $varListYear]);                    
                            $command = $txtQuery->createCommand();
                            $txtTotalMonth = $command->queryScalar();                       
                        }

                ?>

                    <td class="text-center"><?php echo round($txtTotalMonth); ?></td>

                <?php
                    }
                ?>
                <td class="text-center"><?php echo $txtTotalAVG; ?></td>
            </tr>
            <?php } ?>  
            <tr>
                <th  class="text-center" colspan="8"></th>
            </tr>   
            <tr>
                <th bgcolor="#5D92E2" class="text-center" colspan="8"><?= Yii::t('app', 'COSTO X GESTION') ?></th>
            </tr>   
            <tr>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Nivel') ?></th>
                <?php
                    foreach ($varMonthYear as $key => $value) {
                        $varMonth = $value['CorteMes'];
                        $varYear = $value['CorteYear'];                    
                ?>
                    <th bgcolor="#5D92E2" class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
                <?php
                    }
                ?>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Promedio '.$varYear) ?></th>                 
            </tr> 
                <?php
                    $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 

                    foreach ($txtCiudades as $key => $value) {
                        $txtNameCity = $value['name'];
                        $varIdPcrc = $value['id'];

                ?>
                <tr>
                    <td bgcolor="#A7B3C5" class="text-center"><?php echo $txtNameCity; ?></td>
                </tr>
                <?php } ?> 
            <tr>
                <th class="text-center" colspan="8"></th>
            </tr>     
            <tr>
                <th bgcolor="#5D92E2" class="text-center" colspan="9"><?= Yii::t('app', 'VOLUMEN X CLIENTE') ?></th>
            </tr> 
            <tr>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Ciudad') ?></th>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Cliente') ?></th>
                <?php
                    foreach ($varMonthYear as $key => $value) {
                        $varMonth = $value['CorteMes'];
                        $varYear = $value['CorteYear'];
                ?>
                    <th bgcolor="#5D92E2" class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
                <?php
                    }
                ?>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Promedio '.$varYear) ?></th>                
            </tr>
            <?php
                $varPCRCPadres = Yii::$app->db->createCommand("select * from tbl_arbols where snhoja = 0 and arbol_id in (98, 2) and activo = 0")->queryAll();

                foreach ($varPCRCPadres as $key => $value) {
                    $varNameCityID = $value['arbol_id'];
                    $varNameCity = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$varNameCityID'")->queryScalar();
                    $varNamePcrc = $value['name'];  
                    $varIdPcrc = $value['id'];                                     


                    $varMeses = Yii::$app->db->createCommand("select  mesyear from (select cantidadvalor, mesyear from tbl_control_volumenxcliente where idservicio = '$varIdPcrc' and anuladovxc = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryAll();

                    $varPromedio = Yii::$app->db->createCommand("select avg(cantidadvalor) from (select cantidadvalor, mesyear from tbl_control_volumenxcliente where idservicio = '$varIdPcrc' and anuladovxc = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryScalar();


                    $varPromedio1 = Yii::$app->db->createCommand("select avg(cantidadvalorS) from (select cantidadvalorS, mesyear from tbl_control_volumenxclienteS where idservicio = '$varIdPcrc' and anuladovxcS = 0 order by mesyear desc limit 6) a order by a.mesyear asc")->queryScalar();

                    $txtRtaPromedio = $varPromedio + $varPromedio1;
            ?>
            <tr>
                <td bgcolor="#A7B3C5" class="text-center"><?php echo $varNameCity; ?></td>
                <td bgcolor="#2EECF5" class="text-center"><?php echo $varNamePcrc; ?></td>
                <?php
                foreach ($varMeses as $key => $value) {
                    $txtvarMes = $value['mesyear'];

                    $varControl = Yii::$app->db->createCommand("select cantidadvalor from tbl_control_volumenxcliente where mesyear = '$txtvarMes' and idservicio = '$varIdPcrc' and anuladovxc = 0")->queryScalar();

                    $varControl1 = Yii::$app->db->createCommand("select cantidadvalorS from tbl_control_volumenxclienteS where mesyear = '$txtvarMes' and idservicio = '$varIdPcrc' and anuladovxcS = 0")->queryScalar();

                    $txtRtaControl = $varControl + $varControl1;
                    
                ?>  
                    <td class="text-center"><?php echo $txtRtaControl; ?></td>
                <?php } ?>
                <td class="text-center"><?php echo round($txtRtaPromedio); ?></td>
            </tr>
            <?php           
                }
            ?> 
            <tr>
                <th class="text-center" colspan="9"></th>
            </tr>     
            <tr>
                <th bgcolor="#5D92E2" class="text-center" colspan="11"><?= Yii::t('app', 'VOLUMEN X VALORADOR') ?></th>
            </tr> 
            <tr>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Ciudad') ?></th>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Cliente') ?></th>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Identificación') ?></th>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Valorador') ?></th>
                <?php

                    foreach ($varMonthYear as $key => $value) {
                        $varMonth = $value['CorteMes'];
                        $varYear = $value['CorteYear'];
                ?>
                    <th bgcolor="#5D92E2" class="text-center"><?php echo $varMonth.' - '.$varYear; ?></th>
                <?php
                    }
                ?>
                <th bgcolor="#5D92E2" class="text-center"><?= Yii::t('app', 'Promedio '.$varYear) ?></th>
            </tr>     
            <?php

                $querys =  new Query;
                $querys     ->select(['aa.name as Ciudad', 'a.name as Servicio', 'cv.usua_id as IdUsu', 'cv.identificacion as Identificacion','cv.nombres as Nombres'])->distinct()
                            ->from('tbl_control_volumenxvalorador cv')
                            ->join('LEFT OUTER JOIN', 'tbl_arbols a',
                                    'cv.idservicio = a.id')
                            ->join('LEFT OUTER JOIN', 'tbl_arbols aa',
                                    'aa.id = a.arbol_id');                    
                $command = $querys->createCommand();
                $query = $command->queryAll();

                foreach ($query as $key => $value) {
                    $varCiudad = $value['Ciudad'];
                    $varServicios = $value['Servicio'];
                    $varUsuID = $value['IdUsu'];
                    $varIdentidad = $value['Identificacion'];
                    $varNombres = $value['Nombres'];

                    $varMonthYear2 = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a     where CorteMes not like '%$txtMes%' order by a.mesyear asc")->queryAll();

                    $varPromedio = Yii::$app->db->createCommand("select avg(totalrealizadas) from tbl_control_volumenxvalorador where usua_id = '$varUsuID' and anuladovxv = 0")->queryScalar();

            ?>
            <tr>
                <td bgcolor="#A7B3C5" class="text-center"><?php echo $varCiudad; ?></td>
                <td bgcolor="#2EECF5" class="text-center"><?php echo $varServicios; ?></td>
                <td class="text-center"><?php echo $varIdentidad; ?></td>
                <td class="text-center"><?php echo $varNombres; ?></td>
                <?php
                    $varRealizadas = 0;
                    foreach ($varMonthYear2 as $key => $value) {
                        $varMesYear = $value['mesyear'];

                        $varControl = Yii::$app->db->createCommand("select round(sum(totalrealizadas)/6) from tbl_control_volumenxvalorador where usua_id = '$varUsuID' and mesyear = '$varMesYear' and anuladovxv = 0")->queryScalar();

                        if ($varControl != null) {
                            $varRealizadas = $varControl;
                        }else{
                            $varRealizadas = 0;
                        }
                    
                ?>
                    <td class="text-center"><?php echo $varRealizadas; ?></td>
                <?php
                        
                    }
                ?>
                <td class="text-center"><?php echo round($varPromedio); ?></td>
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
        tableToExcel('tblData', 'Control Procesos', name+'.xls');
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>