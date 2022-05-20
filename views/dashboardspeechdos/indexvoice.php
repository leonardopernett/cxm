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

$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='tooltip']").tooltip(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);

$this->title = 'Dashboard Escuchar + 2.0';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Dashboard Escuchar + 2.0';

    $template = '<div class="col-md-12">'
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

    $fechaI = new DateTime();
    $fechaI->modify('first day of this month');
    $fechaIni = $fechaI->format('Y-'.$MesAnterior.'-d');

    $fechaF = new DateTime();
    $fechaF->modify('last day of this month');
    $fechaFin = $fechaF->format('Y-'.$MesAnterior.'-d');

    $varInicioF = $txtFechaIni.' 05:00:00';
    $varFecha = date('Y-m-d',strtotime($txtFechaFin."+ 1 days"));
    $varFinF = $varFecha.' 05:00:00';


    $fechaComoEntero = strtotime($varInicioF);
    $fechaIniCat = date("Y", $fechaComoEntero).'-01-01'; 
    $fechaFinCat = date("Y", $fechaComoEntero).'-12-31';
    $fechaEntera = strtotime($txtFechaIni);    
    $txtmes = date("m", $fechaEntera); 

    $varCodigo = $txtCodParametrizar;    


    
    $varListIndicadoresH = Yii::$app->db->createCommand("SELECT CONCAT(UPPER(sc.nombre),': ',sh.hallazgo) AS texto FROM tbl_speech_hallazgos sh   LEFT JOIN tbl_speech_categorias sc ON sh.idspeechcategoria = sc.idspeechcategoria WHERE sc.anulado = 0 AND sc.idcategorias IN (1) AND sc.cod_pcrc IN ('$txtCodPcrcok') AND sc.programacategoria IN ('$txtServicio') AND sh.mes = $txtmes")->queryAll();

    $varListIndicadores = "select sc.idcategoria, sc.nombre, sc.tipoparametro, sc.orientacionsmart, sc.orientacionform, 
    sc.programacategoria, sc.definicion, sh.hallazgo, sc.responsable from tbl_speech_categorias sc 
    inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc
    LEFT JOIN tbl_speech_hallazgos sh ON sc.idspeechcategoria = sh.idspeechcategoria AND sh.mes = $txtmes 
    where sc.anulado = 0 and sc.idcategorias = 1 and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio') ";


    $txtvDatosMotivos = "select distinct sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 3 and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio')";

    $txtlistDatas = "select distinct  sp.rn, sp.ext, sp.usuared, sp.comentarios, sc.programacategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sp.cod_pcrc = sc.cod_pcrc where sc.anulado = 0 and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio')";

    if ($varCodigo == 1) {
      $varServicio = Yii::$app->db->createCommand("select distinct a.name from tbl_arbols a inner join tbl_speech_servicios ss on a.id = ss.arbol_id inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where     sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.rn in ('$txtParametros')")->queryScalar();

      $idArbol = Yii::$app->db->createCommand("select distinct ss.arbol_id from tbl_speech_servicios ss   inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.rn in ('$txtParametros')")->queryScalar();

      $varListIndicadores = Yii::$app->db->createCommand($varListIndicadores." and sp.rn in ('$txtParametros') group by sc.idcategoria")->queryAll();
      $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.rn in ('$txtParametros')")->queryAll();
      $txtlistDatas = Yii::$app->db->createCommand($txtlistDatas." and sp.rn in ('$txtParametros')")->queryAll();
    }else{
      if ($varCodigo == 2) {
        $varServicio = Yii::$app->db->createCommand("select distinct a.name from tbl_arbols a inner join tbl_speech_servicios ss on a.id = ss.arbol_id inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.ext in ('$txtParametros')")->queryScalar();

        $idArbol = Yii::$app->db->createCommand("select distinct ss.arbol_id from tbl_speech_servicios ss   inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.ext in ('$txtParametros')")->queryScalar();

        $varListIndicadores = Yii::$app->db->createCommand($varListIndicadores." and sp.ext in ('$txtParametros') group by sc.idcategoria")->queryAll();
        $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.ext in ('$txtParametros')")->queryAll();
        $txtlistDatas = Yii::$app->db->createCommand($txtlistDatas." and sp.ext in ('$txtParametros')")->queryAll();
      }else{        
        $varServicio = Yii::$app->db->createCommand("select distinct a.name from tbl_arbols a inner join tbl_speech_servicios ss on a.id = ss.arbol_id inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where  sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.usuared in ('$txtParametros')")->queryScalar();

        $idArbol = Yii::$app->db->createCommand("select distinct ss.arbol_id from tbl_speech_servicios ss   inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.anulado = 0 and sp.cod_pcrc in ('$txtCodPcrcok') and sp.usuared in ('$txtParametros')")->queryScalar();

        $varListIndicadores = Yii::$app->db->createCommand($varListIndicadores." and sp.usuared in ('$txtParametros') group by sc.idcategoria")->queryAll();
        $txtvDatosMotivos = Yii::$app->db->createCommand($txtvDatosMotivos." and sp.usuared in ('$txtParametros')")->queryAll();
        $txtlistDatas = Yii::$app->db->createCommand($txtlistDatas." and sp.usuared in ('$txtParametros')")->queryAll();
      }
    }

    $listData = ArrayHelper::map($varListIndicadores, 'idcategoria', 'nombre');

    $txtIdCatagoria1 = 0;
    if ($fechaIniCat < '2020-01-01') {
      $txtIdCatagoria1 = 2681;
    }else{
      if ($idArbol == '17' || $idArbol == '8' || $idArbol == '105' || $idArbol == '2575' || $idArbol == '3263' || $idArbol == '1371' || $idArbol == '2253' || $idArbol == '675' || $idArbol == '3070' ||  $idArbol == '3071' ||  $idArbol == '3077' || $idArbol == '3069' || $idArbol == '3110' || $idArbol == '2919' || $idArbol == '3350' || $idArbol == '3110' || $idArbol == '3436' || $idArbol == '485'  || $idArbol == '3410' || $idArbol == '678' || $idArbol == '2919' || $idArbol == '3310' || $idArbol == '679') {
        $txtIdCatagoria1 = 1105;
      }else{
        $txtIdCatagoria1 = 1114;
      }
    }  
    $varProgramas = $txtServicio;
    $varNamePCRC = $txtServicio;
    $txtConteoIndicador = count($varListIndicadores);    
    $varindica = $txtIndicador;
    $varCodiPcrc = $txtCodpcrc;

    $txtTotalLlamadas = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varProgramas') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and idcategoria = $txtIdCatagoria1")->queryScalar();

    $txtTotalCallid = Yii::$app->db->createCommand("select distinct callid from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varProgramas') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF'")->queryAll();

    $txtTotalRedbox = Yii::$app->db->createCommand("select distinct count(idredbox) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varProgramas') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF'")->queryScalar();    

    $txtTotalGrabadora = Yii::$app->db->createCommand("select distinct count(idgrabadora) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varProgramas') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF'")->queryScalar();

    $txtnombrepcrc = Yii::$app->db->createCommand("select distinct pcrc from tbl_speech_categorias where anulado = 0  and cod_pcrc in ('$txtCodPcrcok') and programacategoria in ('$varProgramas')")->queryScalar();
    $txtCodPcrcokc1 = Yii::$app->db->createCommand("select distinct cod_pcrc from tbl_speech_categorias where anulado = 0  and cod_pcrc in ('$txtCodPcrcok') and programacategoria in ('$varProgramas')")->queryScalar();

    $arralistmotivos = array();
    foreach ($txtTotalCallid as $key => $value) {
      array_push($arralistmotivos, $value['callid']);
    }
    $arraycallids = implode(", ", $arralistmotivos);

    $varcountindicador = 0;
    $arraylistindicador = array();
    foreach ($varListIndicadores as $key => $value) {
      $vararrayid = $value['idcategoria'];

      $varconteoindicador = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varProgramas') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and idcategoria = $vararrayid and callid in ($arraycallids)")->queryScalar();

      if ($varconteoindicador > 0) {
        $varcountindicador = 1;
      }else{
        $varcountindicador = 0;
      }

      array_push($arraylistindicador, $varcountindicador);
    } 

    $varArraysumaInidicador = array_sum($arraylistindicador);

    $txtRtaProcentajeindicador = (round(($varArraysumaInidicador / count($varListIndicadores)) * 100, 1));

    if (count($txtvDatosMotivos) != 0) {
      $varcountmotivo = 0;
      $arraylistmotivoc = array();
      foreach ($txtvDatosMotivos as $key => $value) {
        $vararrayidm = $value['idcategoria'];

        $varconteomotivo = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varProgramas') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and idcategoria = $vararrayidm and callid in ($arraycallids)")->queryScalar();

        if ($varconteomotivo > 0) {
          $varcountmotivo = 1;
        }else{
          $varcountmotivo = 0;
        }

        array_push($arraylistmotivoc, $varcountmotivo);
      }

      $varArraysumaMotivos = array_sum($arraylistmotivoc);

      $txtRtaProcentajeMotivos = (round(($varArraysumaMotivos / count($txtvDatosMotivos)) * 100, 1));
    }else{
      $txtRtaProcentajeMotivos = 0;
    }
    
    $varListName =  array();
    $varListDuracion = array();
    $varListCantidad = array();

    //Diego
    $varListLogin =  array();
    $varListCantiVar =  array();
    $varListLogin5 =  array();
    $varListCantiVar5 =  array();
    $varListLogin0 =  array();
    $varListCantiVar0 =  array();
    $vartotallogin = 0;
    $canlogin = 0;
    $contador = 0;
    $vartotalreg  = 0;
    
    if ($varNametop){
    $txtlistatopvar = Yii::$app->db->createCommand("select COUNT(*) AS cantidad, login_id FROM tbl_dashboardspeechcalls WHERE anulado = 0 AND servicio IN('$varProgramas') AND fechallamada BETWEEN '$varInicioF' AND '$varFinF' AND extension IN ('$txtParametros') AND idcategoria IN ($varNametop) GROUP BY login_id ORDER BY cantidad")->queryAll();
    $varNametop = Yii::$app->db->createCommand("select distinct nombre from tbl_speech_categorias where anulado = 0 and idcategoria = $varNametop and cod_pcrc in ('$txtCodPcrcok')")->queryScalar();
    $vartotalreg = count($txtlistatopvar);
    if($vartotalreg > 20){
      $varulti = 9;
      $varprim = 11;
    } else {
      $varulti = 4;
      $varprim = 6;
    }
    $varultimo5 = $vartotalreg - $varulti;
    foreach ($txtlistatopvar as $key => $value) {
      $varlogin = $value['login_id'];      
      $varcantivar = $value['cantidad'];
      $canlogin = substr_count($varlogin,".");
      
      if($canlogin > 0){
        $vartotallogin = $vartotallogin + 1;
      }
      $contador += 1;
      if($contador < $varprim){
        array_push($varListLogin, $varlogin);
        array_push($varListCantiVar, $varcantivar);
      }
      if($contador == $varultimo5){
        $varultimo5 += 1;
        array_push($varListLogin5, $varlogin);
        array_push($varListCantiVar5, $varcantivar);
      }
      if($vartotalreg < 11){
        array_push($varListLogin0, $varlogin);
        array_push($varListCantiVar0, $varcantivar);
      }
      
    }
   }

$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js); 

$varrtaA = null;
$varrtaC = null;
$varrtaM = null;

$varColorA = null;
$varColorC = null;
$varColorM = null;

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card {
            height: 200px;
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

    .card2 {
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
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card3 {
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

    .card:hover .card1:hover {
        top: -15%;
    }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Dashboard-Escuchar-+.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br>
<br>
<div id="capaUno" style="display: inline">
  <div class="row">
    <div class="col-md-6">
      <div class="card3 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;">Ficha T&eacute;cnica</label>
      </div>
    </div>
  </div>
  <br>
  <div class="row">

    <div class="col-md-3">
      <div class="card mb">
        <?php 
          $varSelect = Yii::$app->db->createCommand("SELECT DISTINCT programacategoria FROM tbl_speech_categorias WHERE cod_pcrc IN ('$txtCodPcrcok')")->queryScalar();
          if ($varSelect == "CX_Directv") { 
              $varSelect1 = Yii::$app->db->createCommand("select distinct id_dp_clientes from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$txtCodPcrcok')")->queryScalar();
              $varSelect2 = $varSelect.'_'.$varSelect1;                
        ?>
          <img src="<?= Url::to("@web/images/servicios/$varSelect2.png"); ?>" alt="<?=$varSelect2?>">
        <?php
          }else{
        ?>
          <img src="<?= Url::to("@web/images/servicios/$varSelect.png"); ?>" alt="<?=$varSelect?>">
        <?php 
          } 
        ?>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card mb">
        <label><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> Cliente/Servicio:</label>
        <label><?php echo $varServicio; ?></label>
        <hr>  
        <label><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> Pcrc Seleccionado:</label>
        <label><?php echo $txtCodPcrcokc1.' - '.$txtnombrepcrc; ?></label>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card mb">
        <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #C148D0;"></em> Rango de Fechas:</label>
        <label><?php echo $txtFechaIni.' - '.$txtFechaFin; ?></label>
        <hr>  
        <label><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em> Par&aacute;metros Seleccionados:</label>
        <?php
          $txtnombreParametro = null;
          $txtnombrePrograma = null;
          $vararraynombreParametro = array();
          foreach ($txtlistDatas as $key => $value) {  
            $txtnombrePrograma = $value['programacategoria'];

            if ($varCodigo == 1) {            
              $txtnombreParametro = $value['rn'];
            }else{
                if ($varCodigo == 2) {
                  $txtnombreParametro = $value['ext'];
                }else{
                  $txtnombreParametro = $value['usuared'];
                }
            }
                    
            array_push($vararraynombreParametro, $txtnombreParametro);
          }
          $varParams = implode(" - ", $vararraynombreParametro);
        ?>    
        <?php if ($varCodigo >= 2) { ?>
            <label style="font-size: 10px;"><?php echo $varParams; ?></label>
        <?php }else{ ?>
            <label ><?php echo $varParams; ?></label>
        <?php } ?>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card mb">
        <label><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em> Cantidad de Interacciones:</label>
        <label  style="font-size: 90px; text-align: center;"><?php echo $txtTotalLlamadas; ?></label>
      </div>
    </div>

  </div>
</div>
<hr>
<div class="capaDos" id="capaDos" style="display: inline">
  <div class="row">
    <div class="col-md-6">
      <div class="card3 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;">Resultados por Responsables</label>
      </div>
    </div>
  </div>
  <br>
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <div class="row">

          <div class="col-md-6">            
              <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
              <caption>Resultados</caption>
                <thead>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo 'Indicadores'; ?></label></th>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo 'Total'; ?></label></th>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo 'Agente'; ?></label></th>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo 'Canal'; ?></label></th>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo 'Marca'; ?></label></th>
                </thead> 
                <tbody>
                  <?php
                    $varNum  = 0;
                    $titulos = array();
                    $txtrtasporcentajes = array();
                    $txtRtaProcentaje = 0;
                    $txtIndicadores = null;

                    $cantidadAgenda = 0;
                    $cantidadCanal = 0;
                    $cantidadMarca = 0;
                    $arrayAgente = array();
                    $arrayCanal = array();
                    $arrayMarca = array();

                    foreach ($varListIndicadores as $key => $value) {
                     
                      $txtIdIndicadores = $value['idcategoria'];
                     
                      $txtNombreCategoria = $value['nombre']; 
                      $txtTipoSmart2 = $value['orientacionsmart']; 
                      $txtTipoFormIndicador = $value['orientacionform'];
                      $txtPrograma = $value['programacategoria']; 
                      $txtdefinicion = $value['definicion'];

                      $txtresponsable = $value['responsable'];
                      
                      $varCodPcrc = $txtCodPcrcok;
                        
                        if ($varCodigo == 1) {
                       
                          $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.rn in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores' and sc.dashboard in (1,3)")->queryScalar();

                          $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform, sc.responsable from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.rn in ('$txtParametros')    and sc.cod_pcrc in ('$varCodPcrc') and sc.dashboard in (1,3) group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();

                          $arrayListOfVar = array();
                          $arraYListOfVarMas = array();
                          $arraYListOfVarMenos = array();
                          foreach ($varListVariables as $key => $value) {
                            $varOrienta = $value['orientacionsmart'];

                            array_push($arrayListOfVar, $value['idcategoria']);

                            if ($varOrienta == 1) {
                              array_push($arraYListOfVarMenos, $value['idcategoria']);
                            }else{
                              if ($varOrienta == 2) {
                                array_push($arraYListOfVarMas, $value['idcategoria']);
                              }
                            }                      
                          }
                          $arrayVariable = implode(", ", $arrayListOfVar);
                          $arrayVariableMas = implode(", ", $arraYListOfVarMas);
                          $arrayVariableMenos = implode(", ", $arraYListOfVarMenos);
                         


                        }else{
                          if ($varCodigo == 2) {
                          
                            $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.ext in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores' and sc.dashboard in (1,3)")->queryScalar();

                            $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform, sc.responsable from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.ext in ('$txtParametros')  and sc.cod_pcrc in ('$varCodPcrc') and sc.dashboard in (1,3) group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();

                            $arrayListOfVar = array();
                            $arraYListOfVarMas = array();
                            $arraYListOfVarMenos = array();
                            foreach ($varListVariables as $key => $value) {
                              $varOrienta = $value['orientacionsmart'];

                              array_push($arrayListOfVar, $value['idcategoria']);

                              if ($varOrienta == 1) {
                                array_push($arraYListOfVarMenos, $value['idcategoria']);
                              }else{
                                if ($varOrienta == 2) {
                                  array_push($arraYListOfVarMas, $value['idcategoria']);
                                }
                              }                      
                            }
                            $arrayVariable = implode(", ", $arrayListOfVar);
                            $arrayVariableMas = implode(", ", $arraYListOfVarMas);
                            $arrayVariableMenos = implode(", ", $arraYListOfVarMenos);
                          }else{
                           
                            $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.usuared in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores' and sc.dashboard in (1,3)")->queryScalar();

                            $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform, sc.responsable from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.usuared in ('$txtParametros')  and sc.cod_pcrc in ('$varCodPcrc') and sc.dashboard in (1,3) group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();

                            $arrayListOfVar = array();
                            $arraYListOfVarMas = array();
                            $arraYListOfVarMenos = array();
                            foreach ($varListVariables as $key => $value) {
                              $varOrienta = $value['orientacionsmart'];

                              array_push($arrayListOfVar, $value['idcategoria']);

                              if ($varOrienta == 1) {
                                array_push($arraYListOfVarMenos, $value['idcategoria']);
                              }else{
                                if ($varOrienta == 2) {
                                  array_push($arraYListOfVarMas, $value['idcategoria']);
                                }
                              }                      
                            }
                            $arrayVariable = implode(", ", $arrayListOfVar);
                            $arrayVariableMas = implode(", ", $arraYListOfVarMas);
                            $arrayVariableMenos = implode(", ", $arraYListOfVarMenos);

                          }
                        }

                        $varArrayInidicador = 0;
                        $varArrayPromedio = array();
                        if (count($varListVariables) != 0) {
                          // Tipo indicador Normal
                          if ($varTipoPAram == 2) {                  
                            // Cantidad variables positivas y negativas
                            $varSumarPositivas = 0;
                            $varSumarNegativas = 0;
                            foreach ($varListVariables as $key => $value) {
                              $varSmart = $value['orientacionsmart'];

                              if ($varSmart == 2) {
                                $varSumarPositivas = $varSumarPositivas + 1;
                              }else{
                                if ($varSmart == 1) {
                                  $varSumarNegativas = $varSumarNegativas + 1;
                                }
                              }
                            }
                            
                            $varTotalvariables = count($varListVariables);

                            if ($varSumarPositivas == $varTotalvariables) {   
                            
                              $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                              $varconteo = 0;
                              foreach ($varListCallid as $key => $value) {
                                $txtCallid = $value['callid'];

                                if (count($arrayVariableMenos) != 0) {
                                                    $varconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable) and idvariable in ($arrayVariable)")->queryScalar();
                                }else{
                                    $varconteo = 0;
                                }

                                if ($varconteo == 0 || $varconteo == null) {
                                  $txtRtaIndicador = 0;
                                }else{
                                  $txtRtaIndicador = 1;
                                }

                                array_push($varArrayPromedio, $txtRtaIndicador);                          
                              }

                              $varArrayInidicador = array_sum($varArrayPromedio);
                            }else{
                           
                              $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                              foreach ($varListCallid as $key => $value) {
                                $txtCallid = $value['callid'];
                                
                                if (count($arrayVariableMenos) != 0) {
                                                    $varconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariableMenos) and idvariable in ($arrayVariableMenos)")->queryScalar();
                                }else{
                                  $varconteo = 0;
                                }

                                if ($varconteo == 0 || $varconteo == null) {                         
                                  $txtRtaIndicador = 1;
                                }else{                            
                                  $txtRtaIndicador = 0;
                                }

                                array_push($varArrayPromedio, $txtRtaIndicador);                          
                              }
                              $varArrayInidicador = array_sum($varArrayPromedio);
                            }                      
                          }else{
                            // Tipo indicador Auditoria
                            if ($varTipoPAram == 1) {      
                              // Cantidad variables positivas y negativas
                              $varSumarPositivas = 0;
                              $varSumarNegativas = 0;
                              foreach ($varListVariables as $key => $value) {
                                $varSmart = $value['orientacionsmart'];

                                if ($varSmart == 2) {
                                  $varSumarPositivas = $varSumarPositivas + 1;
                                }else{
                                  if ($varSmart == 1) {
                                    $varSumarNegativas = $varSumarNegativas + 1;
                                  }
                                }
                              }
                              
                              $varTotalvariables = count($varListVariables);

                              if ($varSumarPositivas == $varTotalvariables) {
                                $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                                foreach ($varListCallid as $key => $value) {
                                  $txtCallid = $value['callid'];

                                  $varconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable) and idvariable in ($arrayVariable)")->queryScalar();

                                  if ($varconteo == $varTotalvariables || $varconteo != null) {
                                    $txtRtaIndicador = 1;
                                  }else{
                                    $txtRtaIndicador = 0;
                                  }

                                  array_push($varArrayPromedio, $txtRtaIndicador); 
                                }
                                $varArrayInidicador = array_sum($varArrayPromedio);
                              }else{
                                $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();                          

                                foreach ($varListCallid as $key => $value) {
                                  $txtCallid = $value['callid'];
                                  
                                  $varconteomas = 0;
                                  $varconteomeno = 0;


                                  if ($arrayVariableMas != "") {
                                    $varconteomas = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariableMas) and idvariable in ($arrayVariableMas)")->queryScalar();
                                  }else{
                                    $varconteomas = 0;
                                  }
                                  

                                  if ($arrayVariableMenos != "") {
                                    $varconteomeno = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariableMenos) and idvariable in ($arrayVariableMenos)")->queryScalar();
                                  }else{
                                    $varconteomeno = 0;
                                  }
                                  

                                  if ($varconteomeno == null || $varconteomeno == 0 && $varconteomas == $varTotalvariables) {
                                    $txtRtaIndicador = 1;
                                  }else{
                                    $txtRtaIndicador = 0;
                                  }

                                  array_push($varArrayPromedio, $txtRtaIndicador); 
                                }
                                $varArrayInidicador = array_sum($varArrayPromedio);
                               
                              }
                            }
                          }
                        }else{
                          // Indicador Normal
                          if ($varTipoPAram == 2) {
                            
                            $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                            $varconteo = 0;
                            foreach ($varListCallid as $key => $value) {
                              $txtCallid = $value['callid'];

                              $varcantidadproceso = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid   and idcategoria = $txtIdIndicadores")->queryScalar();
                             
                              if ($varcantidadproceso == null) {
                                $varcantidadproceso = 0;
                              }

                              array_push($varArrayPromedio, $varcantidadproceso);
                            }

                            $varArrayInidicador = array_sum($varArrayPromedio);                      
                          }else{
                            // Indicador Auditoria
                            if ($varTipoPAram == 1) {
                              $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                              $varconteo = 0;
                              foreach ($varListCallid as $key => $value) {
                                $txtCallid = $value['callid'];

                                $varcantidadproceso = Yii::$app->db->createCommand("select count(callid) from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid   and idcategoria = $txtIdIndicadores")->queryScalar();

                                

                                if ($varcantidadproceso == null) {
                                  $varcantidadproceso = 0;
                                }

                                array_push($varArrayPromedio, $varcantidadproceso);
                              }

                              $varArrayInidicador = array_sum($varArrayPromedio);
                            }
                          }
                        }

                        if ($varArrayInidicador != 0) { 
                          if ($txtTipoFormIndicador == 0) {
                            $txtRtaProcentaje = (round(($varArrayInidicador / $txtTotalLlamadas) * 100, 1));
                          }else{
                            if ($txtTipoFormIndicador == 1) {
                  
                              $txtRtaProcentaje = (100 - (round(($varArrayInidicador / $txtTotalLlamadas) * 100, 1)));
                            }                      
                          }     
                        }else{
                          if ($txtTipoFormIndicador == 1) {
                            $txtRtaProcentaje = 100;
                          }else{
                            if ($txtTipoFormIndicador == 0) {
                              $txtRtaProcentaje = 0;
                            }                            
                          }   
                        }
                        

                        array_push($titulos, $txtNombreCategoria);

                        array_push($txtrtasporcentajes, $txtRtaProcentaje);

                        $varNum += 1;
                        $prueba = "doughnut-chart".$varNum;  
                        $prueba2 = "idchart_indi".$varNum; 
                        $prueba3 = "idchart_rta".$varNum;

                        if ($txtdefinicion != "") {
                          $txtdefinicion = $txtdefinicion;
                        }else{
                          $txtdefinicion = "--";
                        }


                        $varlistarCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();                        


                        $varArrayPromedioA = array();
                        $txttotalidadA = 0;
                        $varArrayPromedioC = array();
                        $txttotalidadC = 0;
                        $varArrayPromedioM = array();
                        $txttotalidadM = 0;
                        foreach ($varlistarCallid as $key => $value) {
                          $varCallid = $value['callid'];

                          // Variables que esten en Agente
                          $varvairablesAgente = Yii::$app->db->createCommand("SELECT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc  WHERE sc.anulado = 0 AND sc.cod_pcrc IN ('$txtCodPcrcok') AND sc.tipoindicador = '$txtNombreCategoria' AND sc.idcategorias in (2) AND sc.responsable IN (1)")->queryAll();

                          $varSumarPositivasA = 0;
                          $varSumarNegativasA = 0;
                          
                          $arravarAgenteP = array();
                          $arravarAgenteN = array();
                          foreach ($varvairablesAgente as $key => $value) {
                            $varSmartA = $value['orientacionsmart'];

                            if ($varSmartA == 2) {
                                $varSumarPositivasA = $varSumarPositivasA + 1;
                                array_push($arravarAgenteP, $value['idcategoria']);
                            }else{
                              if ($varSmartA == 1) {
                                $varSumarNegativasA = $varSumarNegativasA + 1;
                                array_push($arravarAgenteN, $value['idcategoria']);
                              }
                            }
                            
                          }
                          $vararraAgenteMas = implode("', '", $arravarAgenteP);
                          $vararraAgenteMen = implode("', '", $arravarAgenteN);


                          $varTotalvariablesA = count($varvairablesAgente);
                          
                          if ($varSumarPositivasA == $varTotalvariablesA) {
                            $varAgenteconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $varCallid and idindicador in ('$vararraAgenteMas') and idvariable in ('$vararraAgenteMas')")->queryScalar();

                            if ($varAgenteconteo == 0 || $varAgenteconteo == null) {
                              $txtRtaIDAA = 0;
                            }else{
                              $txtRtaIDAA = 1;
                            }

                          }else{
                            $varAgenteconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $varCallid and idindicador in ('$vararraAgenteMen') and idvariable in ('$vararraAgenteMen')")->queryScalar();

                            if ($varAgenteconteo == 0 || $varAgenteconteo == null) {
                              $txtRtaIDAA = 1;
                            }else{
                              $txtRtaIDAA = 0;
                            }
                          }
                          
                          array_push($varArrayPromedioA, $txtRtaIDAA); 


                          // Variables que esten en Canal
                          $varvairablesCanal = Yii::$app->db->createCommand("SELECT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc  WHERE sc.anulado = 0 AND sc.cod_pcrc IN ('$txtCodPcrcok') AND sc.tipoindicador = '$txtNombreCategoria' AND sc.idcategorias in (2) AND sc.responsable IN (2)")->queryAll();
                          
                          $varSumarPositivasC = 0;
                          $varSumarNegativasC = 0;
                          
                          $arravarCanalP = array();
                          $arravarCanalN = array();
                          foreach ($varvairablesCanal as $key => $value) {
                            $varSmartC = $value['orientacionsmart'];

                            if ($varSmartC == 2) {
                                $varSumarPositivasC = $varSumarPositivasC + 1;
                                array_push($arravarCanalP, $value['idcategoria']);
                            }else{
                              if ($varSmartC == 1) {
                                $varSumarNegativasC = $varSumarNegativasC + 1;
                                array_push($arravarCanalN, $value['idcategoria']);
                              }
                            }
                            
                          }
                          $vararraCanalMas = implode("', '", $arravarCanalP);
                          $vararraCanalMen = implode("', '", $arravarCanalN);
                          

                          $varTotalvariablesC = count($varvairablesCanal);

                          if ($varSumarPositivasC == $varTotalvariablesC) {
                            $varCanalconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $varCallid and idindicador in ('$vararraCanalMas') and idvariable in ('$vararraCanalMas')")->queryScalar();
                          
                            if ($varCanalconteo == 0 || $varCanalconteo == null) {
                              $txtRtaIDAC = 0;
                            }else{
                              $txtRtaIDAC = 1;
                            }
                          }else{
                            $varCanalconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $varCallid and idindicador in ('$vararraCanalMen') and idvariable in ('$vararraCanalMen')")->queryScalar();
                          
                            if ($varCanalconteo == 0 || $varCanalconteo == null) {
                              $txtRtaIDAC = 1;
                            }else{
                              $txtRtaIDAC = 0;
                            }
                          }

                          array_push($varArrayPromedioC, $txtRtaIDAC); 


                          // Variables que esten en Marca
                          $varvairablesMarca = Yii::$app->db->createCommand("SELECT sc.idcategoria, sc.orientacionsmart, sc.programacategoria FROM tbl_speech_categorias sc  WHERE sc.anulado = 0 AND sc.cod_pcrc IN ('$txtCodPcrcok') AND sc.tipoindicador = '$txtNombreCategoria' AND sc.idcategorias in (2) AND sc.responsable IN (3)")->queryAll();


                          $varSumarPositivasM = 0;
                          $varSumarNegativasM = 0;

                          $arravarMarcaP = array();
                          $arravarMarcaN = array();
                          foreach ($varvairablesMarca as $key => $value) {
                            $varSmartM = $value['orientacionsmart'];

                            if ($varSmartM == 2) {
                                $varSumarPositivasM = $varSumarPositivasM + 1;
                                array_push($arravarMarcaP, $value['idcategoria']);
                            }else{
                              if ($varSmartM == 1) {
                                $varSumarNegativasM = $varSumarNegativasM + 1;
                                array_push($arravarMarcaN, $value['idcategoria']);
                              }
                            }
                          }

                          $vararraMarcaMas = implode("', '", $arravarMarcaP);
                          $vararraMarcaMen = implode("', '", $arravarMarcaN);

                          $varTotalvariablesM = count($varvairablesMarca);

                          if ($varSumarPositivasM == $varTotalvariablesM) {
                            $varMarcaconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $varCallid and idindicador in ('$vararraMarcaMas') and idvariable in ('$vararraMarcaMas')")->queryScalar();
                          
                            if ($varMarcaconteo == 0 || $varMarcaconteo == null) {
                              $txtRtaIDAM = 0;
                            }else{
                              $txtRtaIDAM = 1;
                            }
                          }else{
                            $varMarcaconteo = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $varCallid and idindicador in ('$vararraMarcaMen') and idvariable in ('$vararraMarcaMen')")->queryScalar();
                          
                            if ($varMarcaconteo == 0 || $varMarcaconteo == null) {
                              $txtRtaIDAM = 1;
                            }else{
                              $txtRtaIDAM = 0;
                            }
                          }    

                          array_push($varArrayPromedioM, $txtRtaIDAM); 

                        }
                        $txtrtaidaA = array_sum($varArrayPromedioA);
                        $txtrtaidaC = array_sum($varArrayPromedioC);
                        $txtrtaidaM = array_sum($varArrayPromedioM);


                        if ($varArrayInidicador != 0) { 
                          if ($txtTipoFormIndicador == 0) {
                           
                            if ($txtrtaidaA != 0) {
                              $txttotalidadA = (round(($txtrtaidaA / $txtTotalLlamadas) * 100, 1)).'%';                              
                              
                              if ($txtNombreCategoria == "Insatisfacción") {
                                $varrtacolorA = (round(100 - ($txtrtaidaA / $txtTotalLlamadas) * 100, 1));
                                $varrtaA = (round(100 - ($txtrtaidaA / $txtTotalLlamadas) * 100, 1));
                              }else{
                                $varrtacolorA = (round(($txtrtaidaA / $txtTotalLlamadas) * 100, 1));
                                $varrtaA = (round(($txtrtaidaA / $txtTotalLlamadas) * 100, 1));
                              }

                              $cantidadAgenda = $cantidadAgenda + 1;

                              if ($varrtacolorA > '20') {
                                  $varColorA = '#D01E53';
                              }else{
                                if ($varrtacolorA <= '10') {
                                    $varColorA = '#00968F';
                                }else{
                                    $varColorA = '#FFC72C';
                                }
                              }

                            }else{
                              $txttotalidadA = '--';
                              $varColorA = '#000000';
                              $varrtaA = '--';
                            }

                            if ($txtrtaidaC != 0) {
                              $txttotalidadC = (round(($txtrtaidaC / $txtTotalLlamadas) * 100, 1)).'%';
                              
                              if ($txtNombreCategoria == "Insatisfacción") {
                                $varrtacolorC = (round(100 - ($txtrtaidaC / $txtTotalLlamadas) * 100, 1));
                                $varrtaC = (round(100 - ($txtrtaidaC / $txtTotalLlamadas) * 100, 1));
                              }else{
                                $varrtacolorC = (round(($txtrtaidaC / $txtTotalLlamadas) * 100, 1));
                                $varrtaC = (round(($txtrtaidaC / $txtTotalLlamadas) * 100, 1));
                              }

                              $cantidadCanal = $cantidadCanal + 1;

                              if ($varrtacolorC > '20') {
                                  $varColorC = '#D01E53';
                              }else{
                                if ($varrtacolorC <= '10') {
                                    $varColorC = '#00968F';
                                }else{
                                    $varColorC = '#FFC72C';
                                }
                              }

                            }else{
                              $txttotalidadC = '--';
                              $varColorA = '#000000';
                              $varrtaC = '--';
                            }

                            if ($txtrtaidaM != 0) {
                              $txttotalidadM = (round(($txtrtaidaM / $txtTotalLlamadas) * 100, 1)).'%';
                              
                              if ($txtNombreCategoria == "Insatisfacción") {
                                $varrtacolorM = (round(100 - ($txtrtaidaM / $txtTotalLlamadas) * 100, 1));
                                $varrtaM = (round(100 - ($txtrtaidaM / $txtTotalLlamadas) * 100, 1));
                              }else{
                                $varrtacolorM = (round(($txtrtaidaM / $txtTotalLlamadas) * 100, 1));
                                $varrtaM = (round(($txtrtaidaM / $txtTotalLlamadas) * 100, 1));
                              }

                              $cantidadMarca = $cantidadMarca + 1;

                              if ($varrtacolorM > '20') {
                                  $varColorM = '#D01E53';
                              }else{
                                if ($varrtacolorM <= '10') {
                                    $varColorM = '#00968F';
                                }else{
                                    $varColorM = '#FFC72C';
                                }
                              }

                            }else{
                              $txttotalidadM = '--';
                              $varColorA = '#000000';
                              $varrtaM = '--';
                            }
                            
                          }else{
                            if ($txtTipoFormIndicador == 1) {
                             
                              if ($txtrtaidaA != 0) {
                                $txttotalidadA = (100 - (round(($txtrtaidaA / $txtTotalLlamadas) * 100, 1))).'%';
                                $varrtacolorA = (100 - (round(($txtrtaidaA / $txtTotalLlamadas) * 100, 1)));
                                $varrtaA = (100 - (round(($txtrtaidaA / $txtTotalLlamadas) * 100, 1)));
                                $cantidadAgenda = $cantidadAgenda + 1;
                                
                                if ($varrtacolorA < '80') {
                                  $varColorA = '#D01E53';
                                }else{
                                  if ($varrtacolorA >= '90') {
                                    $varColorA = '#00968F';
                                  }else{
                                    $varColorA = '#FFC72C';
                                  }
                                }

                              }else{
                                $txttotalidadA = '--';
                                $varColorA = '#000000';
                                $varrtaA = '--';
                              }
                              

                              if ($txtrtaidaC != 0) {
                                $txttotalidadC = (100 - (round(($txtrtaidaC / $txtTotalLlamadas) * 100, 1))).'%';
                                $varrtacolorC = (100 - (round(($txtrtaidaC / $txtTotalLlamadas) * 100, 1)));
                                $varrtaC = (100 - (round(($txtrtaidaC / $txtTotalLlamadas) * 100, 1)));
                                $cantidadCanal = $cantidadCanal + 1;

                                if ($varrtacolorC < '80') {
                                  $varColorC = '#D01E53';
                                }else{
                                  if ($varrtacolorC >= '90') {
                                    $varColorC = '#00968F';
                                  }else{
                                    $varColorC = '#FFC72C';
                                  }
                                }

                              }else{
                                $txttotalidadC = '--';
                                $varColorC = '#000000';
                                $varrtaC = '--';
                              }
                              

                              if ($txtrtaidaM != 0) {
                                $txttotalidadM = (100 - (round(($txtrtaidaM / $txtTotalLlamadas) * 100, 1))).'%';
                                $varrtacolorM = (100 - (round(($txtrtaidaM / $txtTotalLlamadas) * 100, 1)));
                                $varrtaM = (100 - (round(($txtrtaidaM / $txtTotalLlamadas) * 100, 1)));
                                $cantidadMarca = $cantidadMarca + 1;

                                if ($varrtacolorM < '80') {
                                  $varColorM = '#D01E53';
                                }else{
                                  if ($varrtacolorM >= '90') {
                                    $varColorM = '#00968F';
                                  }else{
                                    $varColorM = '#FFC72C';
                                  }
                                }

                              }else{
                                $txttotalidadM = '--';
                                $varColorM = '#000000';
                                $varrtaM = '--';
                              }
                              
                            }                      
                          }     
                        }else{
                          if ($txtTipoFormIndicador == 1) {
                            $txttotalidadA =  '--';

                            $txttotalidadC =  '--';

                            $txttotalidadM = '--';
                            $varColorA = '#000000';
                            $varColorC = '#000000';
                            $varColorM = '#000000';
                          }else{
                            if ($txtTipoFormIndicador == 0) {
                              $txttotalidadA = '--';

                              $txttotalidadC = '--';

                              $txttotalidadM = '--';

                              $varColorA = '#000000';
                              $varColorC = '#000000';
                              $varColorM = '#000000';
                            }                            
                          }   
                        }

                  ?>
                    <tr>
                      <td>
                        <label style="font-size: 15px;">
                          <?php
                              echo Html::tag('span', '<i class="fas fa-info-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                                          'data-title' => Yii::t("app", ""),
                                          'data-content' => $txtdefinicion,
                                          'data-toggle' => 'popover',
                                          'style' => 'cursor:pointer;'
                              ]);
                          ?>
                          <?php echo $txtNombreCategoria; ?>                  
                        </label>
                      </td>
                      <td class="text-center">
                        <label style="font-size: 15px;">
                          <?php echo $txtRtaProcentaje.'%'; ?>              
                        </label>
                      </td>
                      <td class="text-center">
                        <label style="font-size: 15px; color: <?php echo $varColorA; ?>">
                          <?php echo $txttotalidadA; 
                            array_push($arrayAgente, $varrtaA);
                          ?>                  
                        </label>
                      </td>              
                      <td class="text-center">
                        <label style="font-size: 15px; color: <?php echo $varColorC; ?>">
                          <?php echo $txttotalidadC; 
                            array_push($arrayCanal, $varrtaC);
                          ?>                  
                        </label>
                      </td>              
                      <td class="text-center">
                        <label style="font-size: 15px; color: <?php echo $varColorM; ?>">
                          <?php echo $txttotalidadM; 
                            array_push($arrayMarca, $varrtaM);
                          ?>                  
                        </label>
                      </td>
                    </tr>
                  <?php 
                    }
                  ?>
                </tbody>
              </table>            
          </div>

          <div class="col-md-6">
            <div id="conatinergeneric" class="highcharts-container" style="height: 300px;"></div>
          </div>

        </div>

        <hr>

        <div class="row">
          <div class="col-md-12">
            <table style="width:100%">
            <caption>...</caption>
              <tr>
                <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><?php echo "Agente"; ?></label>
                </th>
                <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><?php echo "Canal"; ?></label>
                </th>
                <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><?php echo "Marca"; ?></label>
                </th>
                <th scope="col" class="text-center" style="width: 100px;">
                    <label style="font-size: 15px;"><?php echo "Calidad: General Konecta"; ?></label>
                </th>
              </tr>
              <?php

                $arrayAgenteSum = array();
                foreach ($arrayAgente as $key => $value) {
                  $calcularAgente = $value;

                  if ($calcularAgente != '--') {

                    if ($cantidadAgenda != 0) {
                      $varcalA = round(($calcularAgente / (100 * $cantidadAgenda)) * 100, 2);
                    }else{
                      $varcalA = 0;
                    }
                    
                    array_push($arrayAgenteSum, $varcalA);
                  }
                }
                $totalAgenteDonalds = array_sum($arrayAgenteSum);
                $totalvieAgente = 100 - $totalAgenteDonalds;
                if ($totalAgenteDonalds < '80') {
                  $varTColorA = '#D01E53';
                }else{
                  if ($totalAgenteDonalds >= '90') {
                    $varTColorA = '#00968F';
                  }else{
                    $varTColorA = '#FFC72C';
                  }
                }


                $arrayCanalSum = array();
                foreach ($arrayCanal as $key => $value) {
                  $calcularCanal = $value;

                  if ($calcularCanal != '--') {

                    if ($cantidadCanal != 0) {
                      $varcalC = round(($calcularCanal / (100 * $cantidadCanal)) * 100, 2);
                    }else{
                      $varcalC = 0;
                    }
                    
                    array_push($arrayCanalSum, $varcalC);
                  }
                }
                $totalCanalDonalds = array_sum($arrayCanalSum);
                $totalvieCanal = 100 - $totalCanalDonalds;
                if ($totalCanalDonalds < '80') {
                  $varTColorC = '#D01E53';
                }else{
                  if ($totalCanalDonalds >= '90') {
                    $varTColorC = '#00968F';
                  }else{
                    $varTColorC = '#FFC72C';
                  }
                }


                $arrayMarcaSum = array();
                foreach ($arrayMarca as $key => $value) {
                  $calcularMarca = $value;

                  if ($calcularMarca != '--') {

                    if ($cantidadMarca != 0) {
                      $varcalM = round(($calcularMarca / (100 * $cantidadMarca)) * 100, 2);
                    }else{
                      $varcalM = 0;
                    }
                    
                    array_push($arrayMarcaSum, $varcalM);
                  }
                }
                $totalMarcaDonalds = array_sum($arrayMarcaSum);
                $totalvieMarca = 100 - $totalMarcaDonalds;
                if ($totalMarcaDonalds < '80') {
                  $varTColorM = '#D01E53';
                }else{
                  if ($totalMarcaDonalds >= '90') {
                    $varTColorM = '#00968F';
                  }else{
                    $varTColorM = '#FFC72C';
                  }
                }


                $totalkonecta = round((($totalAgenteDonalds + $totalCanalDonalds + $totalMarcaDonalds) / (100 * 3)) * 100, 2);
                $totalvieK = 100 - $totalkonecta;
                if ($totalkonecta < '80') {
                  $varTColorK = '#D01E53';
                }else{
                  if ($totalkonecta >= '90') {
                    $varTColorK = '#00968F';
                  }else{
                    $varTColorK = '#FFC72C';
                  }
                }
              ?>
              <tr>
                <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerA"></canvas></div><span style="font-size: 15px;"><?php echo $totalAgenteDonalds.' %'; ?></span></td> 
                <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerC"></canvas></div><span style="font-size: 15px;"><?php echo $totalCanalDonalds.' %'; ?></span></td> 
                <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerM"></canvas></div><span style="font-size: 15px;"><?php echo $totalMarcaDonalds.' %'; ?></span></td> 
                <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerK"></canvas></div><span style="font-size: 15px;"><?php echo $totalkonecta.' %'; ?></span></td> 
              </tr>
            </table>
          </div>
        </div>

        <br>

      </div>
    </div>
  </div>
</div>

<hr>

<div class="capaDos" id="capaDos" style="display: inline">
  <div class="row">
    <div class="col-md-6">
      <div class="card3 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;">Detalle por Indicador</label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        
        <div class="row">
          <div class="col-md-12">
            <?php $form = ActiveForm::begin([
              'layout' => 'horizontal',
              'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
              ]); ?>
            <div class="row">
              <div class="col-md-10">
                <?= $form->field($model, 'idcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData, ['prompt' => 'Seleccionar Indicador...', 'id'=>'indicadorID'])->label('Seleccionar Indicador...') ?>
                <?= $form->field($model, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 350, 'class' => 'hidden', 'value'=>$txtCodPcrcok, 'id'=>'txtIdCod_pcrc']) ?>
              </div>
              <div class="col-md-2">
                <div style="display: inline;" id="idindibtn">
                  <div class="row" style="text-align: center;">
                    <?= Html::submitButton(Yii::t('app', 'Buscar Indicador'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'openview();',
                                'title' => 'Buscar']) 
                    ?>
                  </div>
                </div>   
                <div style="display: none;" id="idrtabtn">
                  <div class="row" style="text-align: center;">
                    <label style="font-size: 15px;"><?php echo 'Buscando Variables...'; ?></label>
                  </div> 
                </div>                         
              </div>
            </div> 
            <?php ActiveForm::end(); ?>
          </div>
        </div>

        <hr>

        <div id="panelvar" class="panelvariables" style="display: none;">
          <div class="row">
            <div class="col-md-6">
              <table id="myTable0"  class="table table-striped table-bordered detail-view formDinamico">
              <caption>Resultados</caption>
                <thead>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo 'Responsable'; ?></label></th>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo 'C&oacute;digo Pcrc'; ?></label></th>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo 'Variables'; ?></label></th>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo ' Interacciones / Afectaciones'; ?></label></th>
                  <th scope="col" class="text-center"><label style="font-size: 15px;"><?php echo ' % de Participaci&oacute;n'; ?></label></th>
                </thead>
                <tbody>
                  <?php 
                    if ($varindica != null) {   
                      $txtvDatosR = Yii::$app->db->createCommand("SELECT if(COUNT(sc.responsable) = 0, COUNT(sc.idcategoria),COUNT(sc.responsable)) AS conteor, sc.responsable FROM tbl_speech_categorias sc WHERE sc.anulado = 0 AND sc.idcategorias = 2 AND sc.tipoindicador IN ('$varindica') AND sc.programacategoria IN ('$txtServicio') AND sc.cod_pcrc IN ('$txtCodPcrcok') AND dashboard IN (1,3) GROUP BY sc.responsable")->queryAll(); 
                    }else{
                      $txtvDatosR = Yii::$app->db->createCommand("SELECT if(COUNT(sc.responsable) = 0, COUNT(sc.idcategoria),COUNT(sc.responsable)) AS conteor, sc.responsable FROM tbl_speech_categorias sc WHERE sc.anulado = 0 AND sc.idcategorias = 2  AND sc.programacategoria IN ('$txtServicio') AND sc.cod_pcrc IN ('$txtCodPcrcok') AND dashboard IN (1,3) GROUP BY sc.responsable")->queryAll();                    
                    }  

                  foreach ($txtvDatosR as $key => $value) {
                    $txtresponsabilidades = $value['responsable'];
                    $varconteosr = $value['conteor']; 

                    if ($txtresponsabilidades == "1") {
                      $varresponsility = 'Agente';
                    }else{
                      if ($txtresponsabilidades == "2") {
                        $varresponsility = 'Canal';
                      }else{
                        if ($txtresponsabilidades == "3") {
                          $varresponsility = 'Marca';
                        }else{
                          if ($txtresponsabilidades == null) {
                            $varresponsility = 'Sin Responsabilidad';
                          }   
                        }
                      }
                    }

                ?>
                  <tr>
                    <td class="text-center" rowspan="<?php echo $varconteosr; ?>" ><label style="font-size: 13px;"><?php echo $varresponsility; ?></label></td>
                    <?php
                      if ($varindica != null) {
                                    if ($varCodigo == 1) {
                                      if ($txtresponsabilidades != null) {
                                        $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.rn in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) and sc.responsable in ($txtresponsabilidades) group by sc.nombre, sc.idcategoria")->queryAll(); 
                                      }else{
                                        $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.rn in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) and sc.responsable IS NULL group by sc.nombre, sc.idcategoria")->queryAll(); 
                                      }
                                       
                                    }else{
                                      if ($varCodigo == 2) {  
                                        if ($txtresponsabilidades != null) {
                                          $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.ext in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) and sc.responsable in ($txtresponsabilidades) group by sc.nombre, sc.idcategoria")->queryAll(); 
                                        }else{
                                          $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.ext in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) and sc.responsable IS NULL group by sc.nombre, sc.idcategoria")->queryAll(); 
                                        }                  
                                        
                                      }else{
                                        if ($txtresponsabilidades != null) {
                                          $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.usuared in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) and sc.responsable in ($txtresponsabilidades) group by sc.nombre, sc.idcategoria")->queryAll();
                                        }else{
                                          $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.usuared in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) and sc.responsable IS NULL  group by sc.nombre, sc.idcategoria")->queryAll();
                                        }                                             
                                      }
                                    }
                                  }else{
                                    if ($varCodigo == 1) {
                                      if ($txtresponsabilidades != null) {
                                        $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.rn in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and sc.responsable in ($txtresponsabilidades) group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll();
                                      }else{
                                        $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.rn in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll();
                                      }
                                        
                                    }else{
                                      if ($varCodigo == 2) {   
                                        if ($txtresponsabilidades != null) {
                                          $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.ext in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and sc.responsable in ($txtresponsabilidades) group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll();
                                        }else{
                                          $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.ext in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok')  group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll();
                                        }
                                          
                                      }else{
                                        if ($txtresponsabilidades != null) {
                                          $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.usuared in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and sc.responsable in ($txtresponsabilidades) group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll(); 
                                        }else{
                                          $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.usuared in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok')  group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll(); 
                                        }
                                         
                                      }
                                    }
                                  }       

                                  $arraylistvariable = array();
                                  $arraylistparticipacion = array();
                                  foreach ($txtvDatos as $key => $value) {
                                    $txtCodigoPcrc = $value['cod_pcrc'];
                                    $txtVariables = $value['nombre'];
                                    $txtIdCatagoria = $value['idcategoria'];                 

                                    $txtvCantVari = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls   where idcategoria = $txtIdCatagoria and servicio in ('$txtServicio') and extension in ('$txtParametros')  and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar(); 
                                   

                                    $txtvCantSeg = Yii::$app->db->createCommand("select AVG(callduracion) from tbl_dashboardspeechcalls   where idcategoria = $txtIdCatagoria and servicio in ('$txtServicio') and extension in ('$txtParametros')  and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar();

                                    $varListValidar  = null;
                                    if ($varCodigo == 1) {
                                      $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sp.rn in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();                  
                                    }else{
                                      if ($varCodigo == 2) {
                                        $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sp.ext in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();                    
                                      }else{
                                        $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sp.usuared in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();
                                      }
                                    }

                                    $txtParticipacion = 0;
                                    if ($txtvCantVari != 0 && $txtTotalLlamadas != 0) {
                                      foreach ($varListValidar as $key => $value) {
                                        $varSmart = $value['orientacionsmart'];
                                        $varForm = $value['orientacionform'];

                                        if ($varSmart ==  2 && $varForm == 0) {                      
                                          $txtParticipacion = round(($txtvCantVari / $txtTotalLlamadas) * 100,2);
                                        }else{
                                          if ($varSmart ==  1 && $varForm == 1) {
                                            $txtParticipacion = round(($txtvCantVari / $txtTotalLlamadas) * 100,2);
                                          }else{
                                            $txtParticipacion = (100 - (round(($txtvCantVari / $txtTotalLlamadas) * 100, 1)));
                                            

                                          }
                                        }
                                      }
                                    }else{
                                      $txtParticipacion = 0;
                                    }

                                    array_push($arraylistvariable, $txtVariables);
                                    array_push($arraylistparticipacion, $txtParticipacion);
                    ?>
                        <td class="text-center"><label style="font-size: 11px;"><?php echo $txtCodigoPcrc; ?></label></td>
                        <td class="text-center"><label style="font-size: 13px;"><?php echo $txtVariables; ?></label></td>
                        <td class="text-center"><label style="font-size: 13px;"><?php echo $txtvCantVari; ?></label></td>
                        <td class="text-center"><label style="font-size: 13px;"><?php echo $txtParticipacion.'%'; ?></label></td>
                      </tr>
                    <?php
                      }
                        if ($varindica != null) {
                                    if ($varCodigo == 1) {
                                      $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.rn in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) group by sc.nombre, sc.idcategoria")->queryAll();  
                                    }else{
                                      if ($varCodigo == 2) {                    
                                        $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.ext in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) group by sc.nombre, sc.idcategoria")->queryAll(); 
                                      }else{
                                        $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sp.usuared in ('$txtParametros') and sc.tipoindicador in ('$varindica') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') and dashboard in (1,3) group by sc.nombre, sc.idcategoria")->queryAll();       
                                      }
                                    }
                                  }else{
                                    if ($varCodigo == 1) {
                                      $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.rn in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll();  
                                    }else{
                                      if ($varCodigo == 2) {                    
                                        $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.ext in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll();  
                                      }else{
                                        $txtvDatos = Yii::$app->db->createCommand("select sc.cod_pcrc, sc.nombre, sc.idcategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where     sc.anulado = 0 and sc.idcategorias = 2 and sp.usuared in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.cod_pcrc in ('$txtCodPcrcok') group by sc.nombre, sc.idcategoria order by sc.cod_pcrc desc")->queryAll();  
                                      }
                                    }
                                  }       

                                  $arraylistvariable = array();
                                  $arraylistparticipacion = array();
                                  foreach ($txtvDatos as $key => $value) {
                                    $txtCodigoPcrc = $value['cod_pcrc'];
                                    $txtVariables = $value['nombre'];
                                    $txtIdCatagoria = $value['idcategoria'];                 

                                    $txtvCantVari = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls   where idcategoria = $txtIdCatagoria and servicio in ('$txtServicio') and extension in ('$txtParametros')  and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar(); 
                                    

                                    $txtvCantSeg = Yii::$app->db->createCommand("select AVG(callduracion) from tbl_dashboardspeechcalls   where idcategoria = $txtIdCatagoria and servicio in ('$txtServicio') and extension in ('$txtParametros')  and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar();

                                    $varListValidar  = null;
                                    if ($varCodigo == 1) {
                                      $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sp.rn in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();                  
                                    }else{
                                      if ($varCodigo == 2) {
                                        $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sp.ext in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();                    
                                      }else{
                                        $varListValidar = Yii::$app->db->createCommand("select sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.programacategoria in ('$txtServicio') and sp.usuared in ('$txtParametros') and sc.cod_pcrc in ('$txtCodPcrcok')  and sc.idcategoria = '$txtIdCatagoria'")->queryAll();
                                      }
                                    }

                                    $txtParticipacion = 0;
                                    if ($txtvCantVari != 0 && $txtTotalLlamadas != 0) {
                                      foreach ($varListValidar as $key => $value) {
                                        $varSmart = $value['orientacionsmart'];
                                        $varForm = $value['orientacionform'];

                                        if ($varSmart ==  2 && $varForm == 0) {                      
                                          $txtParticipacion = round(($txtvCantVari / $txtTotalLlamadas) * 100,1);
                                        }else{
                                          if ($varSmart ==  1 && $varForm == 1) {
                                            $txtParticipacion = round(($txtvCantVari / $txtTotalLlamadas) * 100,1);
                                          }else{
                                            $txtParticipacion = (100 - (round(($txtvCantVari / $txtTotalLlamadas) * 100, 1)));


                                          }
                                        }
                                      }
                                    }else{
                                      $txtParticipacion = 0;
                                    }

                                    array_push($arraylistvariable, $txtVariables);
                                    array_push($arraylistparticipacion, $txtParticipacion);
                                }

                    ?>                
                  <tr>
                    <td class="text-center" colspan="5"  style="background-color: #C6C6C6;"></td>
                  </tr>
                <?php
                  }
                ?>
                </tbody>
              </table>
            </div>

            <div class="col-md-6">
              <div id="containerVariable" class="highcharts-container"></div>
            </div>

          </div>

          <hr>

          <div class="row">
            <div class="col-md-12">
              <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-comment" style="font-size: 20px; color: #00968F;"></em>  Hallazgo: </label>
                <?php
                  $varhallazgo =  array();
                  foreach ($varListIndicadoresH as $key => $value) {
                    array_push($varhallazgo, $value['texto']);
                  }
                  $cantidadtexto = implode(' --- ', $varhallazgo);
                ?>
                <label style="font-size: 15px;"><?php echo $cantidadtexto; ?></label>
              </div>
            </div>
          </div>

          <br>

        </div>        

      </div>
    </div>
  </div>

</div>

<hr>
<?php
  if (count($txtvDatosMotivos) != 0) {    
?>
<div class="capaDos" id="capaDos" style="display: inline">
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;">An&aacutelisis por Motivos de Contacto</label>
      </div>
    </div>
  </div>
  
  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <?php $form = ActiveForm::begin([
          'layout' => 'horizontal',
          'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
          ]); ?> 
          <div class="row">
            <div class="col-md-5">
              <select class ='form-control' id="txtIndicadores" data-toggle="tooltip" title="Indicadores" onchange="listai();" class ='form-control'>
                <option value="" disabled selected>Seleccionar Indicador...</option>  
                                  <?php                          
                                      foreach ($varListIndicadores as $key => $value) {
                                        echo "<option value = '".$value['idcategoria']."'>".$value['nombre']."</option>";
                                      }
                                  ?>
              </select> 
              <?= $form->field($model, 'idcategoria')->textInput(['class'=>'hidden','maxlength' => true, 'id'=>'indicadorID2'])  ?>
            </div>

            <div class="col-md-5">
              <select class ='form-control' id="txtVariables" data-toggle="tooltip" title="Variables" onchange="listav();" class ='form-control'>
                  <option value="" disabled selected>Seleccionar Variable...</option>
              </select>
              <?= $form->field($model, 'nombre')->textInput(['class'=>'hidden','maxlength' => true, 'value' => $txtCodPcrcok])  ?>
            </div>

            <div class="col-md-2" style="text-align: center;">
              <?= Html::submitButton(Yii::t('app', 'Buscar Motivos'),
                                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                            'data-toggle' => 'tooltip', 'onclick' => 'openview2();',
                                            'title' => 'Buscar']) 
              ?>
            </div>
          </div>
        <?php ActiveForm::end(); ?> 
          
          <br>

          <div class="row">

            <div class="col-md-6">
              <table id="myTable"  class="table table-striped table-bordered detail-view formDinamico">
              <caption>...</caption>
                <thead>
                  <tr>
                    <th scope="col" class="text-center"><label style="font-size: 15px;"><?= Yii::t('app', '') ?></th>
                    <th scope="col" class="text-center" colspan="2"><label style="font-size: 13px;"><?= Yii::t('app', 'Por Motivos') ?></th>
                    <th scope="col" class="text-center" colspan="2"><label style="font-size: 13px;"><?= Yii::t('app', 'Por Variable '.$varName2) ?></th>
                  </tr>
                  <tr>
                    <th scope="col" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', 'Motivos de Interacciones') ?></label></th>
                    <th scope="col" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', '% de Interacciones') ?></label></th>
                    <th scope="col" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad de Interacciones') ?></label></th>
                    <th scope="col" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', '% de Interacciones') ?></label></th>
                    <th scope="col" class="text-center"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad de Interacciones') ?></label></th>
                  </tr>           
                </thead>
                <tbody>
                  <?php                           
                                    
                            foreach ($txtvDatosMotivos as $key => $value) {
                              $varMotivos = $value['nombre'];               
                              $varIdCatagoria = $value['idcategoria'];
                              

                              $txtvCantMotivos1 = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls  where idcategoria = '$varIdCatagoria' and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar();
                              $txtvCantMotivos = intval($txtvCantMotivos1);
                             

                              if ($txtvCantMotivos != 0 && $txtTotalLlamadas != 0) {
                                $txtParticipación2 = round(($txtvCantMotivos / $txtTotalLlamadas) * 100,2);
                              }else{
                                $txtParticipación2 = 0;
                              } 

                              $txtvCantSeg2 = Yii::$app->db->createCommand("select AVG(callduracion) from tbl_dashboardspeechcalls   where idcategoria = '$varIdCatagoria' and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar(); 

                              $txtcoincidencia1 = Yii::$app->db->createCommand("select callId from tbl_dashboardspeechcalls where     idcategoria in ('$varIdCatagoria', '$txtCategoria') and servicio in ('$varProgramas') and extension in ('$txtParametros')  and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0 group by callId HAVING COUNT(1) > 1")->queryAll();

                              $txtcoincidencia = count($txtcoincidencia1);


                              if ($txtcoincidencia != 0 && $txtvCantMotivos != 0 && $txtTotalLlamadas != 0) {
                                
                                $txtRtaVar = round(($txtcoincidencia / $txtvCantMotivos) * 100,2);
                                
                              }else{
                               
                                $txtRtaVar = 0;
                                $txtRtaVariable = 0;
                              }

                              array_push($varListName, $varMotivos);
                              array_push($varListDuracion, round($txtvCantSeg2));
                              array_push($varListCantidad, $txtvCantMotivos);
                  ?>
                    <tr>
                      <td class="text-left"><label style="font-size: 13px;"><?php echo $varMotivos; ?></label></td>
                      <td class="text-center"><label style="font-size: 13px;"><?php echo $txtParticipación2." %"; ?></label></td>
                      <td class="text-center"><label style="font-size: 13px;"><?php echo $txtvCantMotivos; ?></label></td>
                      <td class="text-center"><label style="font-size: 13px;"><?php echo " ".$txtRtaVar." %"; ?></label></td>
                      <td class="text-center"><label style="font-size: 13px;"><?php echo $txtcoincidencia; ?></label></td>
                    </tr>
                  <?php
                    }
                  ?>
                </tbody>
              </table>
            </div>

            <div class="col-md-6">
              <div id="containerTMO" class="highcharts-container"> 
            </div>
          </div>

          <br>

      </div>
    </div>
  </div>
</div>
<hr>
<?php
}
?>

<div id="capaCuatro" style="display: inline">
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;">Top de Asesores</label>
      </div>
    </div>
  </div>
  
  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        
        <?php $form = ActiveForm::begin([
          'layout' => 'horizontal',
          'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
          ]); ?> 
              <div class="row">
                <div class="col-sm-5">
                  <select class ='form-control' id="txtIndicadores1" data-toggle="tooltip" title="Indicadores" onchange="listai1();" class ='form-control'>
                    <option value="" disabled selected>Seleccionar Indicador...</option>  
                      <?php                          
                        foreach ($varListIndicadores as $key => $value) {
                          echo "<option value = '".$value['idcategoria']."'>".$value['nombre']."</option>";
                        }
                      ?>
                  </select> 
                  <?= $form->field($model3, 'extension')->textInput(['class'=>'hidden','maxlength' => true, 'id'=>'indicadorID21'])  ?>
                </div>
                <div class="col-sm-5">
                  <select class ='form-control' id="txtVariables1" data-toggle="tooltip" title="Variables" onchange="listav1();" class ='form-control'>
                    <option value="" disabled selected>Seleccionar Variable...</option>
                  </select>
                  <?= $form->field($model3, 'nombreCategoria')->textInput(['class'=>'hidden','maxlength' => true, 'value' => $txtCodPcrcok])  ?>
                </div>               
                
                <div class="col-md-2" style="text-align: center;">
                  <?= Html::submitButton(Yii::t('app', 'Buscar Top Asesores'),
                                                ['class' => $model3->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                    'data-toggle' => 'tooltip', 'onclick' => 'openview3();',
                                                    'title' => 'Buscar']) 
                  ?>
                </div>
              </div>
        <?php ActiveForm::end(); ?> 

            <br>

            <?php
              if ($vartotalreg != 0) {
                if($vartotalreg > 10){
            ?>
                <div class="subcuartalinea">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="card2 mb">
                        <div id="containerTOP5" class="highcharts-container"></div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="card2 mb">
                        <div id="containerTOP" class="highcharts-container"></div>
                      </div>
                    </div>
                  </div>
                </div>
            <?php } else { ?>
                <div class="subcuartalinea">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card2 mb">
                        <div id="containerTOP0" class="highcharts-container"></div>
                      </div>
                    </div>
                  </div>
                </div>
            <?php 
                }
              }
            ?>

      </div>
    </div>
  </div>
</div>

<hr>

<div id="CapaCero" style="display: inline"> 
  <div class="row">
    <div class="col-md-3">
      <div  class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Regresar: </label> 
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
        ?>
      </div>
    </div>

    <div class="col-md-2">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar Reporte: </label>
        <?= Html::button('Exportar', ['value' => url::to(['dashboardspeech/categoriasvoice', 'arbol_idV' => $txtServicio, 'parametros_idV' => $txtParametros, 'codigoPCRC' => $txtCodPcrcok, 'codparametrizar' => $txtCodParametrizar, 'indicador' => $varindica, 'nomFechaI' => $txtFechaIni, 'nomFechaF' => $txtFechaFin]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                        'data-toggle' => 'tooltip',
                        'title' => 'Exportar', 'style' => 'background-color: #337ab7']) ?> 

          <?php
            Modal::begin([
              'header' => '<h4>Procesando datos en archivo de excel...</h4>',
              'id' => 'modal1',
            ]);

            echo "<div id='modalContent1'></div>";
                                          
            Modal::end(); 
          ?>  
      </div>
    </div>

    <div class="col-md-2">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar Base: </label>
        <?= Html::button('Exportar', ['value' => url::to(['categoriasgeneral', 'arbol_idV' => $txtServicio, 'parametros_idV' => $txtParametros, 'codparametrizar' => $txtCodParametrizar, 'codigoPCRC' => $txtCodPcrcok, 'indicador' => $varindica, 'nomFechaI' => $txtFechaIni, 'nomFechaF' => $txtFechaFin]), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                        'data-toggle' => 'tooltip',
                        'title' => 'Exportar', 'style' => 'background-color: #337ab7']) 
        ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Envio de datos al correo corporativo...</h4>',
              'id' => 'modal2',
            ]);

            echo "<div id='modalContent2'></div>";
                                          
            Modal::end(); 
        ?>
      </div>
    </div>

    
    <div class="col-md-2">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-phone-square" style="font-size: 15px; color: #FFC72C;"></em> Análisis Focalizado: </label>
        <?= Html::a('Interacciones',  ['llamadafocalizada', 'varprograma'=>$varNamePCRC, 'varcodigopcrc'=>$txtCodPcrcok, 'varidcategoria'=>$txtIdCatagoria1, 'varextension'=>$txtParametros, 'varfechasinicio'=>$varInicioF, 'varfechasfin'=>$varFinF, 'vartcantllamadas'=>$txtTotalLlamadas, 'varfechainireal'=>$txtFechaIni, 'varfechafinreal'=>$txtFechaFin,'varcodigos'=>$varCodigo, 'varaleatorios' => 0], ['class' => 'btn btn-success',
                          'style' => 'background-color: #337ab7', 'target' => "_blank",
                          'data-toggle' => 'tooltip',
                          'title' => 'Buscar Interacciones']) 
        ?>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 15px; color: #FFC72C;"></em> Top IDA: </label>
        <?= Html::button('Verificar', ['value' => url::to(['totalizaragentes', 'arbol_idV' => $txtServicio, 'parametros_idV' => $txtParametros, 'codparametrizar' => $txtCodParametrizar, 'codigoPCRC' => $txtCodPcrcok, 'indicador' => $varindica, 'nomFechaI' => $txtFechaIni, 'nomFechaF' => $txtFechaFin]), 'class' => 'btn btn-success', 'id'=>'modalButton3',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'enviodatosr()', 
                                'title' => 'Total Agentes', 'style' => 'background-color: #337ab7']) 
        ?> 

        <?php
                    Modal::begin([
                      'header' => '<h4>Calcula total de agentes...</h4>',
                      'id' => 'modal3',
                      'size' => 'modal-lg',
                    ]);

                    echo "<div id='modalContent3'></div>";
                                                  
                    Modal::end(); 
        ?>
      </div>
    </div>
  </div>
</div>
<hr>

<script type="text/javascript">
    var chartContainerA = document.getElementById("chartContainerA");
    var chartContainerC = document.getElementById("chartContainerC");
    var chartContainerM = document.getElementById("chartContainerM");
    var chartContainerK = document.getElementById("chartContainerK");

    Chart.defaults.global.defaultFontFamily = "Lato";
    Chart.defaults.global.defaultFontSize = 12;

    var oilDataA = {
        datasets: [
            {
                data: ["<?php echo $totalAgenteDonalds; ?>","<?php echo $totalvieAgente; ?>"],
                backgroundColor: [
                    "<?php echo $varTColorA; ?>",
                    "#D7CFC7"
                ]
            }]
    };

    var pieChart = new Chart(chartContainerA, {
      type: 'doughnut',
      data: oilDataA
    });

    var oilDataC = {
        datasets: [
            {
                data: ["<?php echo $totalCanalDonalds; ?>","<?php echo $totalvieCanal; ?>"],
                backgroundColor: [
                    "<?php echo $varTColorC; ?>",
                    "#D7CFC7"
                ]
            }]
    };

    var pieChart = new Chart(chartContainerC, {
      type: 'doughnut',
      data: oilDataC
    });

    var oilDataM = {
        datasets: [
            {
                data: ["<?php echo $totalMarcaDonalds; ?>","<?php echo $totalvieMarca; ?>"],
                backgroundColor: [
                    "<?php echo $varTColorM; ?>",
                    "#D7CFC7"
                ]
            }]
    };

    var pieChart = new Chart(chartContainerM, {
      type: 'doughnut',
      data: oilDataM
    });

    var oilDataK = {
        datasets: [
            {
                data: ["<?php echo $totalkonecta; ?>","<?php echo $totalvieK; ?>"],
                backgroundColor: [
                    "<?php echo $varTColorK; ?>",
                    "#D7CFC7"
                ]
            }]
    };

    var pieChart = new Chart(chartContainerK, {
      type: 'doughnut',
      data: oilDataK
    });

    $(function(){
        var Listadot = "<?php echo implode($titulos,",");?>";
        Listadot = Listadot.split(",");

        var Listado = "<?php echo implode($varListName,",");?>";
        Listado = Listado.split(",");

        var Listado2 = "<?php echo implode($arraylistvariable,",");?>";
        Listado2 = Listado2.split(",");

        // Para Top asesores
        var varNombretop = "<?php echo $varNametop; ?>";
        var ListadoL = "<?php echo implode($varListLogin,",");?>";
        ListadoL = ListadoL.split(",");
        var ListadoC = "<?php echo implode($varListCantiVar,",");?>";
        ListadoC = ListadoC.split(",");

          var ListadoL5 = "<?php echo implode($varListLogin5,",");?>";
        ListadoL5 = ListadoL5.split(",");

        var ListadoC5 = "<?php echo implode($varListCantiVar5,",");?>";
        ListadoC5 = ListadoC5.split(",");

        var ListadoL0 = "<?php echo implode($varListLogin0,",");?>";
        ListadoL0 = ListadoL0.split(",");


        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

        $('#conatinergeneric').highcharts({
            chart: {
                borderColor: '#F0F0F0',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Procentajes de categorizaci&oacuten'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: Listadot,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Indicadores',
              data: [<?= implode($txtrtasporcentajes, ',')?>],
              color: '#4298B5'
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
          });


        $('#containerVariable').highcharts({
            chart: {
                borderColor: '#F0F0F0',
                borderRadius: 7,
                borderWidth: 1,
                type: 'bar'
            },

            yAxis: {
              title: {
                text: ''
              }
            }, 

            title: {
              text: '',
              style: {
                      color: '#615E9B'
                }
            },

            xAxis: {
                  categories: Listado2,
                  title: {
                      text: null
                  }
                },

            tooltip: {
                headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
                pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
                    '<td style="padding:0"><b>{point.y:.1f} %</b></td></tr>',
                footerFormat: '</table>',
                shared: true,
                useHTML: true
            },

            series: [{
              name: 'Porcentaje de participaci&oacuten',
              data: [<?= implode($arraylistparticipacion, ',')?>],
              color: '#615E9B'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
        });


        $('#containerTMO').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: [{
              title: {
                text: 'Cantidad de Interacciones'
              }
            },{
              title: {
                text: 'Duracion de Interacciones'
              },
              opposite: true
            }],  

            title: {
              text: 'Motivos de Contacto vs TMO',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de Interacciones',
              data: [<?= implode($varListCantidad, ',')?>],
              color: '#4298B5'
            },{
              name: 'Duración de Interacciones',
              data: [<?= implode($varListDuracion, ',')?>],
              color: '#FFc72C',
              type: 'line',
              yAxis: 1
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

          });


         $('#containerTOP5').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Interacciones'
              }
            }, 

            title: {
              text: 'Top de Asesores MAS categorizadas / ' + varNombretop,
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: ListadoL5,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de Interacciones',
              data: [<?= implode($varListCantiVar5, ',')?>],
              color: '#4298B5'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
          });

          $('#containerTOP').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Interacciones'
              }
            }, 

            title: {
              text: 'Top de Asesores MENOS categorizadas / ' + varNombretop,
            style: {
                    color: '#ffa126'
              }

            },

            xAxis: {
                  categories: ListadoL,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de Interacciones',
              data: [<?= implode($varListCantiVar, ',')?>],
              color: '#ffa126'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

          });

          $('#containerTOP0').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Interacciones'
              }
            }, 

            title: {
              text: 'Top de Asesores categorizadas / ' + varNombretop,
            style: {
                    color: '#4298B5'
              }

            },

            xAxis: {
                  categories: ListadoL0,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad de Interacciones',
              data: [<?= implode($varListCantiVar0, ',')?>],
              color: '#ffa126'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }

          });


    });

    var varview = document.getElementById("panelvar");
    var varfunciona = "<?php echo $varindica; ?>";
    // console.log(varfunciona);

    if (varfunciona != "") {
      varview.style.display = 'inline';
    }

    function openview() {
      var varselectindi = document.getElementById("indicadorID").value;
      var varbtnone = document.getElementById("idindibtn");
      var varbtntwo = document.getElementById("idrtabtn");

      if (varselectindi == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debes de seleccionar un indicador.","warning");
        return;
      }else{
        varbtnone.style.display = 'none';
        varbtntwo.style.display = 'inline';
      }
    };

    function openview2(){
      var varselectindim = document.getElementById("txtIndicadores").value;
      var varselectvarm = document.getElementById("txtVariables").value;

      if (varselectindim == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debes de seleccionar un indicador.","warning");
        return;
      }else{
        if (varselectvarm == "") {
          event.preventDefault();
          swal.fire("!!! Advertencia !!!","Debes de seleccionar una variable.","warning");
          return;
        }
      }
    };

    function enviodatosr(){
      
      var varArbol_idV = "<?php echo $txtServicio; ?>";
      var varParametros_idV = "<?php echo $txtParametros; ?>";
      var varCodparametrizar = "<?php echo $txtCodParametrizar; ?>";
      var varindicador = "<?php echo $varindica; ?>";        
      var varFechaIni = "<?php echo $txtFechaIni; ?>";
      var varFechaFin = "<?php echo $txtFechaFin; ?>";
      var varCodsPcrc = "<?php echo $txtCodPcrcok; ?>";


          $.ajax({
              method: "get",
              url: "totalagente",
              data : {
                  varArbol_idV : varArbol_idV,
                  varParametros_idV : varParametros_idV,
                  varCodparametrizar : varCodparametrizar,
                  var_FechaIni : varFechaIni,
                  var_FechaFin : varFechaFin,
                  var_CodsPcrc : varCodsPcrc,
                  varIndicador : varindicador,
              },
              success : function(response){ 
                  var numRta =  JSON.parse(response);                
                  console.log(numRta);
                  if (numRta != 0) {
                  }                    
              }
          });
    };

  function listai(){
    var varIndicador = document.getElementById("txtIndicadores").value;
    var varServicios = "<?php echo $varProgramas; ?>";
    var varParametros = "<?php echo $txtParametros; ?>";
    var varfechainic = "<?php echo $fechaIniCat; ?>";
    var varfechafinc = "<?php echo $fechaFinCat; ?>";
    var varcodigo = "<?php echo $txtCodPcrcok; ?>";
    $.ajax({
              method: "get",

              url: "listashijo",
              data : {
                txtvindicador : varIndicador,
                txtvservicios : varServicios,
                txtvparametros : varParametros,
                txtvfechainic : varfechainic,
                txtvfechafinc : varfechafinc,
                txtvcodigo : varcodigo,
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          document.getElementById("txtVariables").innerHTML = "";
                          var node = document.createElement("OPTION");
                          node.setAttribute("value", "");
                          var textnode = document.createTextNode("Seleccionar...");
                          node.appendChild(textnode);
                          document.getElementById("txtVariables").appendChild(node);
                          for (var i = 0; i < Rta.length; i++) {
                              var node = document.createElement("OPTION");
                              node.setAttribute("value", Rta[i].nombre);
                              var textnode = document.createTextNode(Rta[i].nombre);
                              node.appendChild(textnode);
                              document.getElementById("txtVariables").appendChild(node);
                          }
                      }
    });  
  };

  function listav(){
    var varIndicador = document.getElementById("txtIndicadores").value;
    var varVariables = document.getElementById("txtVariables").value;
    var varServicios = "<?php echo $varProgramas; ?>";
    var varParametros = "<?php echo $txtParametros; ?>";
    var varCodigo = "<?php echo $txtCodPcrcok; ?>";

    $.ajax({
                method: "get",

                url: "listashijos",
                data : {
                    txtvindicador : varIndicador,
                    txtvvariables : varVariables,
                    txtvservicios : varServicios,
                    txtvparametros : varParametros,
                    txtvcodigos : varCodigo,
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    document.getElementById("indicadorID2").value = numRta;            
                }
            });
    
  };

  function listai1(){
    var varIndicador = document.getElementById("txtIndicadores1").value;
    var varServicios = "<?php echo $varProgramas; ?>";
    var varParametros = "<?php echo $txtParametros; ?>";
    var varfechainic = "<?php echo $fechaIniCat; ?>";
    var varfechafinc = "<?php echo $fechaFinCat; ?>";
    var varcodigo = "<?php echo $txtCodPcrcok; ?>";
    $.ajax({
              method: "get",

              url: "listashijo1",
              data : {
                txtvindicador : varIndicador,
                txtvservicios : varServicios,
                txtvparametros : varParametros,
                txtvfechainic : varfechainic,
                txtvfechafinc : varfechafinc,
                txtvcodigo : varcodigo,
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          document.getElementById("txtVariables1").innerHTML = "";
                          var node = document.createElement("OPTION");
                          node.setAttribute("value", "");
                          var textnode = document.createTextNode("Seleccionar Variable...");
                          node.appendChild(textnode);
                          document.getElementById("txtVariables1").appendChild(node);
                          for (var i = 0; i < Rta.length; i++) {
                              var node = document.createElement("OPTION");
                              node.setAttribute("value", Rta[i].nombre);
                              var textnode = document.createTextNode(Rta[i].nombre);
                              node.appendChild(textnode);
                              document.getElementById("txtVariables1").appendChild(node);
                          }
                      }
    });  
  };

  function listav1(){
    var varIndicador = document.getElementById("txtIndicadores1").value;
    var varVariables = document.getElementById("txtVariables1").value;
    var varServicios = "<?php echo $varProgramas; ?>";
    var varParametros = "<?php echo $txtParametros; ?>";
    var varCodigo = "<?php echo $txtCodPcrcok; ?>";

    $.ajax({
                method: "get",

                url: "listashijos1",
                data : {
                    txtvindicador : varIndicador,
                    txtvvariables : varVariables,
                    txtvservicios : varServicios,
                    txtvparametros : varParametros,
                    txtvcodigos : varCodigo,
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    document.getElementById("indicadorID21").value = numRta;            
                }
            });
    
  };

  function openview3(){
    var varselectindim = document.getElementById("txtIndicadores1").value;
    var varselectvarm = document.getElementById("txtVariables1").value;

    if (varselectindim == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debes de seleccionar un indicador.","warning");
      return;
    }else{
      if (varselectvarm == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debes de seleccionar una variable.","warning");
        return;
      }
    }
  };

</script>