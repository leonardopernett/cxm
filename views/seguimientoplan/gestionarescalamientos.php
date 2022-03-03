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

$sesiones =Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol    ->select(['tbl_roles.role_id'])
            ->from('tbl_roles')
            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                    'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
            ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();


$month = date('m');
$year = date('Y');
$day = date("d", mktime(0,0,0, $month+1, 0, $year));
     
$varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
$varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));


?>
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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
        width: 100%;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
  
      </div>
    </div>
  </div>
</header>
<br><br><br>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em> Lista de escalamientos corte actual:</label>
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                <caption>Lista</caption>
                    <thead>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Rol"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Técnico/Lider"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Corte"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tipo Corte"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Justificación"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad Justificaciones"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Comentarios"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Asesor"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Aprobar"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "No Aprobar"; ?></label></th>                    
                    </thead>
                    <tbody>
                        <?php
                            if ($roles == 270) {
                                $varlistEscalamientos = Yii::$app->get('dbslave')->createCommand("select distinct evaluados_id from tbl_control_procesos where anulado = 0 and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
                                                            }else{
                                $varlistEscalamientos = Yii::$app->get('dbslave')->createCommand("select distinct evaluados_id from tbl_control_procesos where anulado = 0 and responsable = $sesiones and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
                                
                            }
                            
                            if ($varlistEscalamientos != null) {
                                $varlistasusuarios = array();
                                foreach ($varlistEscalamientos as $key => $value) {
                                    array_push($varlistasusuarios, $value['evaluados_id']);
                                }
                                $varlistasgestionados = implode(", ", $varlistasusuarios);


                                $txtlistgestion = Yii::$app->get('dbslave')->createCommand("select * from tbl_plan_escalamientos where anulado = 0 and tecnicolider in ($varlistasgestionados) and estado  = 0 and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
                            
                            

                            foreach ($txtlistgestion as $key => $value) {
                                $varidplan = $value['idplanjustificar'];
                                $varidtcts = $value['idtcs'];
                                $vanameidtcs = Yii::$app->get('dbslave')->createCommand("select diastcs from tbl_tipos_cortes where idtcs = $varidtcts")->queryScalar();
                                $varEstado = $value['Estado'];
                                $txtestado = null;
                                $varidusua = $value['tecnicolider'];
                                $varnameusu = Yii::$app->get('dbslave')->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varidusua")->queryScalar();

                                $varRango = Yii::$app->get('dbslave')->createCommand("select tc.tipocortetc from tbl_tipocortes tc inner join tbl_tipos_cortes tcs on tc.idtc = tcs.idtc where  tcs.idtcs = $varidtcts")->queryScalar();

                                        $varRol = Yii::$app->get('dbslave')->createCommand("select r.role_id from tbl_roles r inner join rel_usuarios_roles ru on r.role_id = ru.rel_role_id where ru.rel_usua_id = $varidusua")->queryScalar();
                                
                                        $varnamerol = Yii::$app->get('dbslave')->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ru on r.role_id = ru.rel_role_id where ru.rel_usua_id = $varidusua")->queryScalar();
                                $varasesor = $value['asesorid'];
                                if ($varasesor != null) {
                                    $txtasesor = Yii::$app->get('dbslave')->createCommand("select distinct name from tbl_evaluados where id = $varasesor")->queryScalar();
                                }else{
                                    $txtasesor = "---";
                                }
                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $varnamerol; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varnameusu; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varRango; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $vanameidtcs; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['justificacion']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['cantidadjustificar']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $txtasesor; ?></label></td>
                        
                                    <td class="text-center">
                                                    <?= Html::a('<i class="fas fa-thumbs-up" style="font-size: 20px; color: #2cdc5a;"></i>',  ['editarplan','varidplan' => $varidplan, 'varestado' => 1], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Aprobar']) ?>
                                            </td>
                                    <td class="text-center">
                                                <?= Html::a('<i class="fas fa-thumbs-down" style="font-size: 20px; color: #ff3838;"></i>',  ['negargestion','varidplan' => $varidplan, 'varestado' => 2], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'No Aprobar']) 
                                            ?>                                            </td>

                            </tr>
                        <?php
                            } }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<hr>
<br>
<div id="capaTres" style="display: inline">   
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Descargar Escalamientos: </label> 
                            <a id="dlink" style="display:none;"></a>
                            <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Descargar</button>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-body" style="background-color: #f0f8ff;">Nota: Los datos que se visualizan y se ingresan de las justificaciones corresponden al mes actual.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div id="capaDos" style="display: none">   
    <div class="row">
                <table id="tblDatas" class="table table-striped table-bordered tblResDetFreed">
                <caption>...</caption>
                    <thead>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Responsable"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tecnico/Lider"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Rol"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Corte"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tipo Corte"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Justificacion"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad Justificaciones"; ?></label></th>
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Comentarios"; ?></label></th>  
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Asesor"; ?></label></th> 
                        <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Estado"; ?></label></th>                
                    </thead>
                    <tbody>
                        <?php
                            if ($roles == 270) {
                                $varlistEscalamientos = Yii::$app->get('dbslave')->createCommand("select distinct evaluados_id from tbl_control_procesos where anulado = 0 and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
                            }else{
                                $varlistEscalamientos = Yii::$app->get('dbslave')->createCommand("select distinct evaluados_id from tbl_control_procesos where anulado = 0 and responsable = $sesiones and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
                            }

                            if ($varlistEscalamientos != null) {
                                $varlistasusuarios = array();
                                foreach ($varlistEscalamientos as $key => $value) {
                                    array_push($varlistasusuarios, $value['evaluados_id']);
                                }
                                $varlistasgestionados = implode(", ", $varlistasusuarios);


                                $txtlistgestion = Yii::$app->get('dbslave')->createCommand("select * from tbl_plan_escalamientos where anulado = 0 and tecnicolider in ($varlistasgestionados) and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
                            }else{
                                $txtlistgestion = Yii::$app->get('dbslave')->createCommand("select * from tbl_plan_escalamientos where anulado = 0  and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();
                            }
                            
                            

                            foreach ($txtlistgestion as $key => $value) {
                                $varidplan = $value['idplanjustificar'];
                                $varidtcts = $value['idtcs'];
                                $vanameidtcs = Yii::$app->get('dbslave')->createCommand("select diastcs from tbl_tipos_cortes where idtcs = $varidtcts")->queryScalar();
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
                                
                                $varidusua = $value['tecnicolider'];
                                $varnameusu = Yii::$app->get('dbslave')->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varidusua")->queryScalar();

                                $varRango = Yii::$app->get('dbslave')->createCommand("select tc.tipocortetc from tbl_tipocortes tc inner join tbl_tipos_cortes tcs on tc.idtc = tcs.idtc where  tcs.idtcs = $varidtcts")->queryScalar();
                                $varnameresp = Yii::$app->get('dbslave')->createCommand("select distinct u.usua_nombre from tbl_control_procesos c 
                                INNER JOIN tbl_usuarios u ON c.responsable = u.usua_id
                                WHERE c.evaluados_id = $varidusua")->queryScalar();

                                if ($varnameresp == "") {
                                    $varnameresp = "Sin Responsable";
                                }

                                $varnamerol = Yii::$app->get('dbslave')->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ru on r.role_id = ru.rel_role_id where ru.rel_usua_id = $varidusua")->queryScalar();

                                $varasesor = $value['asesorid'];
                                if ($varasesor != null) {
                                    $txtasesors = Yii::$app->get('dbslave')->createCommand("select distinct name from tbl_evaluados where id = $varasesor")->queryScalar();
                                }else{
                                    $txtasesors = '---';
                                }

                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $varnameresp; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varnameusu; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varnamerol; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varRango; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $vanameidtcs; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['justificacion']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['cantidadjustificar']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $txtasesors; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $txtestado; ?></label></td>
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
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
        tableToExcel('tblDatas', 'Archivo Seguimiento', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>