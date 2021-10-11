<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;


$this->title = 'Dashboard -- VOC --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Listado de Servicios y PCRC';

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

    $varBeginYear = '2020-01-01';
    $varLastYear = '2050-12-31';

?>
<br>
    <div class="page-header" >
        <h3><center><?= Html::encode($this->title) ?></center></h3>
    </div> 
<br>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><i class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></i> Acciones: </label>
                <div class="row">                    
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></i> Exportar a Excel: </label> 
                            <a id="dlink" style="display:none;"></a>
                            <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar a Excel</button>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></i> Regresar: </label> 
                            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-columns" style="font-size: 20px; color: #2CA5FF;"></i> Lista general: </label>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblData" class="table table-striped table-bordered detail-view formDinamico" border="0">
                            <thead>
                                <th class="text-center" style="font-size: 15px; background-color: #EEEEEE"><?= Yii::t('app', 'Servicios') ?></th>
                                <th class="text-center" style="font-size: 15px; background-color: #EEEEEE"><?= Yii::t('app', 'Pcrc') ?></th>
                                <th class="text-center" style="font-size: 15px; background-color: #EEEEEE"><?= Yii::t('app', 'Parametros') ?></th>
                                <?php

                                    $varMonthYear = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 4) a   order by a.mesyear asc")->queryAll();

                                    foreach ($varMonthYear as $key => $value) {
                                        $varMonth = $value['CorteMes'];
                                        $varYear = $value['CorteYear'];
                                        $txtTMes = $varMonth.'- '.$varYear;
                                        $varTMes = Yii::$app->db->createCommand("select vozfecha from tbl_voz_fecha where anulado = 0 and cortefecha in ('$txtTMes')")->queryScalar();
                                ?>
                                    <th class="text-center" style="font-size: 15px; background-color: #EEEEEE"><?php echo $varTMes; ?></th>
                                <?php
                                    }
                                ?>
                            </thead>
                            <tbody>
                                <?php 
                                    $varlistpcrc = Yii::$app->db->createCommand("select ss.id_dp_clientes, ss.cliente, sp.cod_pcrc from tbl_speech_parametrizar sp inner join tbl_speech_servicios ss on       sp.id_dp_clientes = ss.id_dp_clientes where ss.anulado = 0 and ss.arbol_id != 1  and sp.anulado = 0 group by  sp.cod_pcrc order by ss.cliente asc")->queryAll();

                                    foreach ($varlistpcrc as $key => $value) {
                                        $varIdService = $value['id_dp_clientes'];
                                        $varNameService = $value['cliente'];
                                        $varNamePcrc = $value['cod_pcrc'];   

                                        $varRN = Yii::$app->db->createCommand("select rn from tbl_speech_parametrizar where anulado = 0 and id_dp_clientes = $varIdService and cod_pcrc in ('$varNamePcrc') and rn != '' group by rn")->queryAll();

                                        $varExt = Yii::$app->db->createCommand("select ext from tbl_speech_parametrizar where anulado = 0 and id_dp_clientes = $varIdService and cod_pcrc in ('$varNamePcrc') and ext != '' group by ext")->queryAll();

                                        $varUsu = Yii::$app->db->createCommand("select usuared from tbl_speech_parametrizar where anulado = 0 and id_dp_clientes = $varIdService and cod_pcrc in ('$varNamePcrc') and usuared != '' group by usuared")->queryAll();

                                ?>
                                <tr>
                                    <td class="text-center"><?php echo $varNameService; ?></td>
                                    <td class="text-center"><?php echo $varNamePcrc; ?></td>

                                    <?php if (count($varRN) != 0) {
                                            $arraylistrn = array();
                                            foreach ($varRN  as $key => $value) {
                                                array_push($arraylistrn, $value['rn']);
                                            }
                                            $txtlistarn = implode("', '", $arraylistrn);
                                            $txtlistarn2 = implode(", ", $arraylistrn);
                                    ?>
                                        <td class="text-center"><?php echo $txtlistarn2; ?></td>
                                    <?php }else{ 
                                        if (count($varExt) != 0) {
                                            $arralistext = array();
                                            foreach ($varExt as $key => $value) {
                                                array_push($arralistext, $value['ext']);
                                            }
                                            $txtlistaext = implode("', '", $arralistext);
                                            $txtlistaext2 = implode(", ", $arralistext);
                                        
                                    ?>
                                        <td class="text-center"><?php echo $txtlistaext2; ?></td>
                                    <?php }else{ 
                                        if (count($varUsu) != 0) {
                                            $arralistusua = array();
                                            foreach ($varUsu as $key => $value) {
                                              array_push($arralistusua, $value['usuared']);
                                            }
                                            $txtlistausua = implode("', '", $arralistusua);
                                            $txtlistausua2 = implode(", ", $arralistusua);
                                    ?>
                                        <td class="text-center"><?php echo $txtlistausua2; ?></td>
                                    <?php } } } ?>

                                    <?php 
                                        $varMonthYear1 = Yii::$app->db->createCommand("select CorteMes, CorteYear, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 4) a   order by a.mesyear asc")->queryAll();

                                        $varBolsita = Yii::$app->db->createCommand("select distinct programacategoria from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$varNamePcrc')")->queryScalar();

                                        $varGeneral = Yii::$app->db->createCommand("select distinct idllamada from tbl_speech_servicios where anulado = 0 and id_dp_clientes = $varIdService")->queryScalar();

                                        foreach ($varMonthYear1 as $key => $value) {
                                         $varMes = $value['mesyear'];   

                                         $date =  date("Y-m-t", strtotime($varMes));
                                         $first_date = $varMes.' 05:00:00';
                                         $last_date = date('Y-m-d',strtotime($date."+ 1 days")).' 05:00:00';

                                         if ($varGeneral == "1114") {
                                            if (count($varRN) != 0) {
                                                $varCantidad = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varBolsita') and fechallamada between '$first_date' and '$last_date' and idcategoria = $varGeneral and extension in ('$txtlistarn')")->queryScalar();
                                            }else{
                                                if (count($varExt) != 0) {
                                                    $varCantidad = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varBolsita') and fechallamada between '$first_date' and '$last_date' and idcategoria = $varGeneral and extension in ('$txtlistaext')")->queryScalar();
                                                }else{
                                                    if (count($varUsu) != 0) {
                                                        $varCantidad = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varBolsita') and fechallamada between '$first_date' and '$last_date' and idcategoria = $varGeneral and extension in ('$txtlistausua')")->queryScalar();
                                                    }
                                                }
                                            }
                                            
                                         }else{
                                            if ($varGeneral == "1105") {
                                                if (count($varRN) != 0) {
                                                    $varCantidad = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varBolsita') and fechallamada between '$first_date' and '$last_date' and idcategoria = $varGeneral and extension in ('$txtlistarn')")->queryScalar();
                                                }else{
                                                    if (count($varExt) != 0) {
                                                        $varCantidad = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varBolsita') and fechallamada between '$first_date' and '$last_date' and idcategoria = $varGeneral and extension in ('$txtlistaext')")->queryScalar();
                                                    }else{
                                                        if (count($varUsu) != 0) {
                                                            $varCantidad = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varBolsita') and fechallamada between '$first_date' and '$last_date' and idcategoria = $varGeneral and extension in ('$txtlistausua')")->queryScalar();
                                                        }
                                                    }
                                                }                                                
                                            }
                                         }

                                    ?>
                                        <td class="text-center"><?php echo $varCantidad; ?></td>
                                    <?php 
                                        }
                                    ?>


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
    </div>
</div>
<hr>
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
            document.getElementById("dlink").download = "Reporte Pcrc & Llamadas";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo Reporte', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>