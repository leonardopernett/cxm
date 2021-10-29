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

$this->title = 'Dashboard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Dashboard Voz del Cliente';

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


    // $varListIndicadores = "select distinct sc.cod_pcrc, sc.idcategoria, sc.nombre, sc.tipoparametro, sc.orientacionsmart, sc.orientacionform, sc.programacategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1  and sc.programacategoria in ('$txtServicio') ";

    //$varListIndicadores = "select  sc.idcategoria, sc.nombre, sc.tipoparametro, sc.orientacionsmart, sc.orientacionform, sc.programacategoria from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sc.cod_pcrc in ('$txtCodPcrcok') and sc.programacategoria in ('$txtServicio') ";

    $varListIndicadores = "select sc.idcategoria, sc.nombre, sc.tipoparametro, sc.orientacionsmart, sc.orientacionform, 
    sc.programacategoria, sc.definicion, sh.hallazgo from tbl_speech_categorias sc 
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
      if ($idArbol == '17' || $idArbol == '8' || $idArbol == '105' || $idArbol == '2575' || $idArbol == '485' || $idArbol == '3263' || $idArbol == '1371' || $idArbol == '2253' || $idArbol == '675' || $idArbol == '3070' ||  $idArbol == '3071' ||  $idArbol == '3077' || $idArbol == '3069' || $idArbol == '3110' || $idArbol == '2919' || $idArbol == '3350' || $idArbol == '3110' || $idArbol == '3410' || $idArbol == '3310' || $idArbol == '3436') {
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
//var_dump($varProgramas);
//var_dump($txtParametros);
//var_dump($txtIdCatagoria1);
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
    //$txtlistatopvar = Yii::$app->db->createCommand("select distinct callid from tbl_dashboardspeechcalls where anulado = 0 and servicio in ('$varProgramas') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF'")->queryAll();
    $varListLogin =  array();
    $varListCantiVar =  array();
    $varListLogin5 =  array();
    $varListCantiVar5 =  array();
    $varListLogin0 =  array();
    $varListCantiVar0 =  array();    
    $varListidcateAgente = array();
    $vartotallogin = 0;
    $canlogin = 0;
    $contador = 0;
    $vartotalreg = 0;
    //var_dump($varNametop);
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
    //para responsable Agente
    $txtlistaidcatagente = Yii::$app->db->createCommand("select idcategoria FROM tbl_speech_categorias  where anulado = 0 and cod_pcrc in ('$txtCodPcrcok') AND 
    nombre not IN( SELECT tipoindicador FROM tbl_speech_categorias  where anulado = 0 
    and cod_pcrc in ('$txtCodPcrcok')) AND idcategorias IN(1,2) AND responsable = 1")->queryAll();
    foreach ($txtlistaidcatagente as $key => $value) {
    $varIdcat = $value['idcategoria'];                                
    array_push($varListidcateAgente, $varIdcat); 
    } 
    $arrayVaridcatAgente = implode(", ", $varListidcateAgente);
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
            font-family: "Nunito",sans-serif;
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
            font-family: "Nunito",sans-serif;
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
            font-family: "Nunito",sans-serif;
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
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>

<br>
<!--  <div class="page-header" >
    <p style="font-family: 'Nunito';"><h3><center><?= Html::encode($this->title) ?></center></h3></p>
  </div>  -->
<br>
<div class="Principal">
<div id="capaUno" style="display: inline">    
    <div id="dtbloque0" class="form-group col-sm-12" style="display: inline;">
      <div class="primeralinea">
        <div class="row">
          <div class="col-md-3">
              <div class="card mb">
                <?php 
                  $varSelect = Yii::$app->db->createCommand("select distinct servicio from tbl_dashboardspeechcalls where anulado = 0 and fechacreacion > '2020-01-01' and servicio in ('$txtServicio')")->queryScalar();
                  if ($varSelect == "CX_Directv") { 
                      $varSelect1 = Yii::$app->db->createCommand("select distinct id_dp_clientes from tbl_speech_parametrizar where anulado = 0 and cod_pcrc in ('$txtCodPcrcok')")->queryScalar();
                      $varSelect2 = $varSelect.'_'.$varSelect1;                
                ?>
                  <img src="<?= Url::to("@web/images/servicios/$varSelect2.png"); ?>" alt="<?= $varSelect2 ?>">
                <?php
                  }else{

                ?>
                  <img src="<?= Url::to("@web/images/servicios/$varSelect.png"); ?>" alt="<?= $varSelect ?>">
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
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em> Parametros Seleccionados:</label>
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
                <label><?php echo $varParams; ?></label>
              </div>
          </div>
          <div class="col-md-3">
              <div class="card mb">
                <label><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em> Cantidad de Llamadas:</label>
                <label  style="font-size: 90px; text-align: center;"><?php echo $txtTotalLlamadas; ?></label>
              </div>
          </div>
        </div>
      </div>
    </div>
</div>
<br>
<div id="capaDos" style="display: inline">
  <div id="dtbloque1" class="form-group col-sm-12" style="display: inline;">
    <hr>
    <div class="segundalinea">
        <div class="row">
          <div class="col-md-12">
            <div class="card1 mb">
              <table style="width:100%">
              <caption>...</caption>
                <tr>
                 <?php
                    foreach ($varListIndicadores as $key => $value) {
                      $txtIndicadores = $value['nombre'];
                      $definicion = $value['definicion'];

                  ?>
                    <th scope="col" class="text-center" width="100"><strong><?php 
                        echo Html::tag('span', $txtIndicadores, [
                            'data-title' => $definicion,
                            'data-toggle' => 'tooltip',
                            'style' => 'cursor:pointer;'
                        ]);
                      ?></strong>
                      </th>
                  <?php
                    }
                  ?>
                </tr>
                <tr>
                  <?php 
                    $arrayVarresponagenteMas = null;
                    $arrayVarresponcanalMas = null;
                    $arrayVarresponmarcaMas = null;
                    $arrayVarresponagenteMenos = null;
                    $arrayVarresponcanalMenos = null;
                    $arrayVarresponmarcaMenos = null;
                 
            //inicio c�lculo del IDA
                    $varNum  = 0;
                    $titulos = array();
                    $txtRtaProcentaje = 0;
                    $txtIndicadores = null;
                    //Diego
                    $txtRtaProcentaje1 = 0;
                    $txtRtaProcentaje2 = 0;
                    $txtRtaProcentaje3 = 0;
                    $txtacumulapromedio1 = 0;
                    $txtacumulapromedio2 = 0;
                    $txtacumulapromedio3 = 0;
                    $arraylistaagente = array();
                    $arraylistacanal = array();
                    $arraylistamarca = array();
                    $arraylistanombre = array();
                    $arraylistaresponsables = array();
                    $arraylistaresponsable = array();
                $arraytotalagente = 0;
                    $arraytotalcanal = 0;
                    $arraytotalmarca = 0;
                    $responsable = array("Total Agente","Total Canal","Total Marca");
                    //f Diego
                    foreach ($varListIndicadores as $key => $value) {
                      // $varCodPcrc = $value['cod_pcrc'];
                      $txtIdIndicadores = $value['idcategoria'];
                      // var_dump($txtIdIndicadores);
                      $txtNombreCategoria = $value['nombre']; 
                      $txtTipoSmart2 = $value['orientacionsmart']; 
                      $txtTipoFormIndicador = $value['orientacionform'];
                      $txtPrograma = $value['programacategoria']; 

                      // $arrayvarCodPcrc = array();
                      // $varListCod_Pcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_speech_categorias where anulado = 0 and programacategoria in ('$txtServicio') and idcategorias = 1 and idcategoria = $txtIdIndicadores")->queryAll();

                      // foreach ($varListCod_Pcrc as $key => $value) {
                      //   array_push($arrayvarCodPcrc, $value['cod_pcrc']);
                      // }
                      $varCodPcrc = $txtCodPcrcok;
                      $varListadorespo = Yii::$app->db->createCommand("select idcategoria, nombre, idcategorias, responsable from tbl_speech_categorias where anulado = 0 and idcategorias in (1,2,3) and programacategoria in ('$txtServicio') and cod_pcrc in ('$varCodPcrc') and responsable is not null group by idcategoria order by idcategorias asc")->queryAll();
                        if ($varCodigo == 1) {
                          // var_dump("RN");
                          $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.rn in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores' and sc.dashboard in (1,3)")->queryScalar();

                          //diego
                          $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform, sc.responsable from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.rn in ('$txtParametros')    and sc.cod_pcrc in ('$varCodPcrc') and sc.dashboard in (1,3) group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();
                          //f

                          $arrayListOfVar = array();
                          $arraYListOfVarMas = array();
                          $arraYListOfVarMenos = array();

                          //diego
                          $arraYListresponagenteMas = array();
                          $arraYListresponagenteMenos = array();
                          $arraYListresponcanalMas = array();
                          $arraYListresponcanalMenos = array();
                          $arraYListresponmarcaMas = array();
                          $arraYListresponmarcaMenos = array();
                          //f

                          foreach ($varListVariables as $key => $value) {
                            $varOrienta = $value['orientacionsmart'];
                            $varresponsable = $value['responsable'];

                            array_push($arrayListOfVar, $value['idcategoria']);

                            if ($varOrienta == 1) {
                                //diego
                              if($varListadorespo){
                                if($varresponsable == 1){
                                  array_push($arraYListresponagenteMenos, $value['idcategoria']);
                                }else{
                                  if($varresponsable == 2){
                                    array_push($arraYListresponcanalMenos, $value['idcategoria']);
                                  } else{
                                      array_push($arraYListresponmarcaMenos, $value['idcategoria']);
                                  }
                                }
                              }
                              //f
                              array_push($arraYListOfVarMenos, $value['idcategoria']);
                            }else{
                              if ($varOrienta == 2) {
                                  //diego
                                if($varListadorespo){
                                    if($varresponsable ==1){
                                      array_push($arraYListresponagenteMas, $value['idcategoria']);
                                    }else{
                                      if($varresponsable ==2){
                                        array_push($arraYListresponcanalMas, $value['idcategoria']);
                                       } else{
                                           array_push($arraYListresponmarcaMas, $value['idcategoria']);
                                       }
                                    }
                                  }
                                    //f
                                array_push($arraYListOfVarMas, $value['idcategoria']);
                              }
                            }                      
                          }
                          $arrayVariable = implode(", ", $arrayListOfVar);
                          $arrayVariableMas = implode(", ", $arraYListOfVarMas);
                          $arrayVariableMenos = implode(", ", $arraYListOfVarMenos);
                          //diego
                          if($varListadorespo){
                            $arrayVarresponagenteMas = implode(", ", $arraYListresponagenteMas);
                            $arrayVarresponcanalMas = implode(", ", $arraYListresponcanalMas);
                            $arrayVarresponmarcaMas = implode(", ", $arraYListresponmarcaMas);
                            $arrayVarresponagenteMenos = implode(", ", $arraYListresponagenteMenos);
                            $arrayVarresponcanalMenos = implode(", ", $arraYListresponcanalMenos);
                            $arrayVarresponmarcaMenos = implode(", ", $arraYListresponmarcaMenos);
                          }
                          //var_dump($arrayVariable);
                          //var_dump($arrayVariableMas);
                          //var_dump($arrayVariableMenos);


                        }else{
                          if ($varCodigo == 2) {
                            // var_dump("Ext");
                            $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.ext in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores' and sc.dashboard in (1,3)")->queryScalar();

                            $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.ext in ('$txtParametros')  and sc.cod_pcrc in ('$varCodPcrc') and sc.dashboard in (1,3) group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();

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
                            // var_dump("UsuaRed");
                            $varTipoPAram = Yii::$app->db->createCommand("select distinct sc.tipoparametro from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 1 and sp.usuared in ('$txtParametros') and sc.programacategoria in ('$txtServicio') and sc.idcategoria = '$txtIdIndicadores' and sc.dashboard in (1,3)")->queryScalar();

                            $varListVariables = Yii::$app->db->createCommand("select sc.idcategoria, sc.orientacionsmart, sc.orientacionform from tbl_speech_categorias sc inner join tbl_speech_parametrizar sp on     sc.cod_pcrc = sp.cod_pcrc where sc.anulado = 0 and sc.idcategorias = 2 and sc.tipoindicador in ('$txtNombreCategoria') and sc.programacategoria in ('$txtServicio') and sp.usuared in ('$txtParametros')  and sc.cod_pcrc in ('$varCodPcrc') and sc.dashboard in (1,3) group by sc.idcategoria, sc.orientacionsmart, sc.orientacionform")->queryAll();

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

    //cuenta la cantidad de positivas y negativas

                        $varArrayInidicador = 0;
                        $varArrayPromedio = array();
                        $varArrayInidicador1 = 0;
                        $varArrayPromedio1 = array();
                        $varArrayInidicador2 = 0;
                        $varArrayPromedio2 = array();
                        $varArrayInidicador3 = 0;
                        $varArrayPromedio3 = array();
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
                              $varconteo1 = 0;
                              $varconteo2 = 0;
                              $varconteo3 = 0;
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
                                //para el calculo por responsabilidad
                                //diego
                                if($varListadorespo){
                                    $txtRtaIndicador1 = null;
                                    $txtRtaIndicador2 = null;
                                    $txtRtaIndicador3 = null;
                                    $varArrayVariable1 = array();
                                    $varArrayVariable2 = array();
                                    $varArrayVariable3 = array();
                                    $varListVariables2 = Yii::$app->db->createCommand("SELECT idcategoria, responsable FROM tbl_speech_categorias WHERE idcategoria IN ($arrayVariable)")->queryAll();
                                    //$varListVariables2 = Yii::$app->db->createCommand("SELECT idcategoria, responsable FROM tbl_speech_categorias WHERE idcategoria IN ($arrayVariable)")->queryScalar();
                                    foreach ($varListVariables2 as $key => $value) {
                                      $varidcategoria = $value['idcategoria'];
                                      $varresponsable = $value['responsable'];
                                      if ($varresponsable == 1){
                                          array_push($varArrayVariable1, $varidcategoria);
                                      }
                                      else{
                                        if ($varresponsable == 2){
                                            array_push($varArrayVariable2, $varidcategoria);
                                        }else{
                                          if ($varresponsable == 3){
                                              array_push($varArrayVariable3, $varidcategoria);
                                          }
                                        }
                                      }
                                    }
                                    
                                    if (count($varArrayVariable1) > 0){
                                      $arrayVariable1 = implode(", ", $varArrayVariable1);
                                      $varconteo1 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable1) and idvariable in ($arrayVariable1)")->queryScalar();
                                      if ($varconteo1 == 0 || $varconteo1 == null) {
                                        $txtRtaIndicador1 = 0;
                                      }else{
                                        $txtRtaIndicador1 = 1;
                                    }
                                    
                                      array_push($varArrayPromedio1, $txtRtaIndicador1);
                                    }
                                    if (count($varArrayVariable2) > 0){
                                        $arrayVariable2 = implode(", ", $varArrayVariable2); 
                                        
                                          $varconteo2 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable2) and idvariable in ($arrayVariable2)")->queryScalar();
                                          if ($varconteo2 == 0 || $varconteo2 == null) {
                                            $txtRtaIndicador2 = 0;
                                          }else{
                                            $txtRtaIndicador2 = 1;
                                          }
                                          array_push($varArrayPromedio2, $txtRtaIndicador2);
                                    }                                    
                                          if (count($varArrayVariable3) > 0){
                                              $arrayVariable3 = implode(", ", $varArrayVariable3);
                                              $varconteo3 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable3) and idvariable in ($arrayVariable3)")->queryScalar();
                                              if ($varconteo3 == 0 || $varconteo3 == null) {
                                                $txtRtaIndicador3 = 0;
                                              }else{
                                                $txtRtaIndicador3 = 1;
                                              }
                                              array_push($varArrayPromedio3, $txtRtaIndicador3);
                                          }                    
                                  
                                  }
                                array_push($varArrayPromedio, $txtRtaIndicador);                          
                              }                      

                              $varArrayInidicador = array_sum($varArrayPromedio);
                              if($varListadorespo){
                                $varArrayInidicador1 = array_sum($varArrayPromedio1);
                                $varArrayInidicador2 = array_sum($varArrayPromedio2);
                                $varArrayInidicador3 = array_sum($varArrayPromedio3);
                              }
                            }else{
                           
                              $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();

                                //para el calculo por responsabilidad
                                if($varListadorespo){
                                    $txtRtaIndicador1 = null;
                                    $txtRtaIndicador2 = null;
                                    $txtRtaIndicador3 = null;
                                    $varArrayVariable1 = array();
                                    $varArrayVariable2 = array();
                                    $varArrayVariable3 = array();
                                    $varListVariables2 = Yii::$app->db->createCommand("SELECT idcategoria, responsable FROM tbl_speech_categorias WHERE idcategoria IN ($arrayVariableMenos) AND cod_pcrc in ('$varCodPcrc')")->queryAll();
                                    //$varListVariables2 = Yii::$app->db->createCommand("SELECT idcategoria, responsable FROM tbl_speech_categorias WHERE idcategoria IN ($arrayVariable)")->queryScalar();
                                    foreach ($varListVariables2 as $key => $value) {
                                      $varidcategoria = $value['idcategoria'];
                                      $varresponsable = $value['responsable'];
                                      if ($varresponsable == 1){
                                          array_push($varArrayVariable1, $varidcategoria);
                                      }
                                      else{
                                        if ($varresponsable == 2){
                                              array_push($varArrayVariable2, $varidcategoria);
                                        }else{
                                          if ($varresponsable == 3){
                                                array_push($varArrayVariable3, $varidcategoria);
                                          }
                                        }
                                      }
                                    }
                                  }

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
                             // diego
                                if($varListadorespo){  
                                    if (count($varArrayVariable1) != 0){
                                      $arrayVariable1 = implode(", ", $varArrayVariable1);
                                      $varconteo1 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable1) and idvariable in ($arrayVariable1)")->queryScalar();
                                      if ($varconteo1 == 0 || $varconteo1 == null) {
                                        $txtRtaIndicador1 = 1;
                                      }else{
                                        $txtRtaIndicador1 = 0;
                                      }
                                      
                                      array_push($varArrayPromedio1, $txtRtaIndicador1);
                                    }
                                    if (count($varArrayVariable2) != 0){
                                        $arrayVariable2 = implode(", ", $varArrayVariable2); 
                                                                            
                                          $varconteo2 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable2) and idvariable in ($arrayVariable2)")->queryScalar();
                                          if ($varconteo2 == 0 || $varconteo2 == null) {
                                            $txtRtaIndicador2 = 1;
                                          }else{
                                            $txtRtaIndicador2 = 0;
                                          }
                                          array_push($varArrayPromedio2, $txtRtaIndicador2);
                                    }                                    
                                          if (count($varArrayVariable3) != 0){
                                              $arrayVariable3 = implode(", ", $varArrayVariable3);
                                              $varconteo3 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable3) and idvariable in ($arrayVariable3)")->queryScalar();
                                              if ($varconteo3 == 0 || $varconteo3 == null) {
                                                $txtRtaIndicador3 = 1;
                                              }else{
                                                $txtRtaIndicador3 = 0;
                                              }
                                              array_push($varArrayPromedio3, $txtRtaIndicador3);
                                          } 
                                  }

                                array_push($varArrayPromedio, $txtRtaIndicador);                          
                              }

                              $varArrayInidicador = array_sum($varArrayPromedio);
                              if($varListadorespo){
                                $varArrayInidicador1 = array_sum($varArrayPromedio1);
                                $varArrayInidicador2 = array_sum($varArrayPromedio2);
                                $varArrayInidicador3 = array_sum($varArrayPromedio3);
                              }
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

                                    //para el calculo por responsabilidad
                                    //diego
                                    if($varListadorespo){
                                      $txtRtaIndicador1 = null;
                                      $txtRtaIndicador2 = null;
                                      $txtRtaIndicador3 = null;
                                      $varArrayVariable1 = array();
                                      $varArrayVariable2 = array();
                                      $varArrayVariable3 = array();
                                      $varListVariables2 = Yii::$app->db->createCommand("SELECT idcategoria, responsable FROM tbl_speech_categorias WHERE idcategoria IN ($arrayVariable)")->queryAll();
                                      //$varListVariables2 = Yii::$app->db->createCommand("SELECT idcategoria, responsable FROM tbl_speech_categorias WHERE idcategoria IN ($arrayVariable)")->queryScalar();
                                      foreach ($varListVariables2 as $key => $value) {
                                        $varidcategoria = $value['idcategoria'];
                                        $varresponsable = $value['responsable'];
                                        if ($varresponsable == 1){
                                            array_push($varArrayVariable1, $varidcategoria);
                                        }
                                        else{
                                          if ($varresponsable == 2){
                                              array_push($varArrayVariable2, $varidcategoria);
                                          }else{
                                            if ($varresponsable == 3){
                                                array_push($varArrayVariable3, $varidcategoria);
                                            }
                                          }
                                        }
                                      }

                                      $varTotalvariables1 = count($varArrayVariable1);
                                      if (count($varArrayVariable1) > 0){
                                        $arrayVariable1 = implode(", ", $varArrayVariable1);
                                        $varconteo1 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable1) and idvariable in ($arrayVariable1)")->queryScalar();
                                        if ($varconteo1 == $varTotalvariables1 || $varconteo1 == null) {
                                          $txtRtaIndicador1 = 0;
                                        }else{
                                          $txtRtaIndicador1 = 1;
                                      }
                                      
                                        array_push($varArrayPromedio1, $txtRtaIndicador1);
                                      }
                                      $varTotalvariables2 = count($varArrayVariable2);
                                      if (count($varArrayVariable2) > 0){
                                        $arrayVariable2 = implode(", ", $varArrayVariable2); 
                                        
                                          $varconteo2 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable2) and idvariable in ($arrayVariable2)")->queryScalar();
                                          if ($varconteo2 == $varTotalvariables2 || $varconteo2 == null) {
                                            $txtRtaIndicador2 = 0;
                                          }else{
                                            $txtRtaIndicador2 = 1;
                                          }
                                          array_push($varArrayPromedio2, $txtRtaIndicador2);
                                      }    
                                        $varTotalvariables3 = count($varArrayVariable3);                                
                                        if (count($varArrayVariable3) > 0){
                                            $arrayVariable3 = implode(", ", $varArrayVariable3);
                                            $varconteo3 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVariable3) and idvariable in ($arrayVariable3)")->queryScalar();
                                            if ($varconteo3 == $varTotalvariables3 || $varconteo3 == null) {
                                              $txtRtaIndicador3 = 0;
                                            }else{
                                              $txtRtaIndicador3 = 1;
                                            }
                                            array_push($varArrayPromedio3, $txtRtaIndicador3);
                                        }                    
                              
                                    }

                                  array_push($varArrayPromedio, $txtRtaIndicador); 
                                }
                                if($varListadorespo){
                                  $varArrayInidicador1 = array_sum($varArrayPromedio1);
                                  $varArrayInidicador2 = array_sum($varArrayPromedio2);
                                  $varArrayInidicador3 = array_sum($varArrayPromedio3);
                                }

                                $varArrayInidicador = array_sum($varArrayPromedio);


                              }else{
                                $varListCallid = Yii::$app->db->createCommand("select callid from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and  extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' group by callid")->queryAll();                          

                                  //para el calculo por responsabilidad
                                  if($varListadorespo){
                                    $txtRtaIndicador1 = null;
                                    $txtRtaIndicador2 = null;
                                    $txtRtaIndicador3 = null;
                                    $varArrayVariable1 = array();
                                    $varArrayVariable2 = array();
                                    $varArrayVariable3 = array();
                                    $varListVariables2 = Yii::$app->db->createCommand("SELECT idcategoria, responsable FROM tbl_speech_categorias WHERE idcategoria IN ($arrayVariableMenos) AND cod_pcrc in ('$varCodPcrc')")->queryAll();
                                    //$varListVariables2 = Yii::$app->db->createCommand("SELECT idcategoria, responsable FROM tbl_speech_categorias WHERE idcategoria IN ($arrayVariable)")->queryScalar();
                                    foreach ($varListVariables2 as $key => $value) {
                                      $varidcategoria = $value['idcategoria'];
                                      $varresponsable = $value['responsable'];
                                      if ($varresponsable == 1){
                                          array_push($varArrayVariable1, $varidcategoria);
                                      }
                                      else{
                                        if ($varresponsable == 2){
                                              array_push($varArrayVariable2, $varidcategoria);
                                        }else{
                                          if ($varresponsable == 3){
                                                array_push($varArrayVariable3, $varidcategoria);
                                          }
                                        }
                                      }
                                    }
                                  }



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
                                
                                

                                    //diego
                                    //agente
                                    if ($arrayVarresponagenteMas != "") {
                                      $varconteomas1 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVarresponagenteMas) and idvariable in ($arrayVarresponagenteMas)")->queryScalar();
                                    }else{
                                      $varconteomas1 = 0;
                                    }
                                    

                                    if ($arrayVarresponagenteMenos != "") {
                                      $varconteomeno1 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVarresponagenteMenos) and idvariable in ($arrayVarresponagenteMenos)")->queryScalar();
                                    }else{
                                      $varconteomeno1 = 0;
                                    }

                                    if ($varconteomeno1 == null || $varconteomeno1 == 0 && $varconteomas1 == $varTotalvariables) {
                                      $txtRtaIndicador1 = 1;
                                    }else{
                                      $txtRtaIndicador1 = 0;
                                    }

                                    array_push($varArrayPromedio1, $txtRtaIndicador1); 
                                                                                                     
                                  
                                    //Canal
                                    if ($arrayVarresponcanalMas != "") {
                                      $varconteomas2 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVarresponcanalMas) and idvariable in ($arrayVarresponcanalMas)")->queryScalar();
                                    }else{
                                      $varconteomas2 = 0;
                                    }
                                    

                                    if ($arrayVarresponcanalMenos != "") {
                                      $varconteomeno2 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVarresponcanalMenos) and idvariable in ($arrayVarresponcanalMenos)")->queryScalar();
                                    }else{
                                      $varconteomeno2 = 0;
                                    }

                                    if ($varconteomeno2 == null || $varconteomeno2 == 0 && $varconteomas2 == $varTotalvariables) {
                                      $txtRtaIndicador2 = 1;
                                    }else{
                                      $txtRtaIndicador2 = 0;
                                    }

                                    array_push($varArrayPromedio2, $txtRtaIndicador2);                                  
                                 

                                  //Marca
                                  if ($arrayVarresponmarcaMas != "") {
                                    $varconteomas3 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVarresponmarcaMas) and idvariable in ($arrayVarresponmarcaMas)")->queryScalar();
                                  }else{
                                    $varconteomas3 = 0;
                                  }
                                  

                                  if ($arrayVarresponmarcaMenos != "") {
                                    $varconteomeno3 = Yii::$app->db->createCommand("select sum(cantproceso) from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid and idindicador in ($arrayVarresponmarcaMenos) and idvariable in ($arrayVarresponmarcaMenos)")->queryScalar();
                                  }else{
                                    $varconteomeno3 = 0;
                                  }

                                  if ($varconteomeno3 == null || $varconteomeno3 == 0 && $varconteomas3 == $varTotalvariables) {
                                    $txtRtaIndicador3 = 1;
                                  }else{
                                    $txtRtaIndicador3 = 0;
                                  }

                                  array_push($varArrayPromedio3, $txtRtaIndicador3); 
                            
                                }

                                $varArrayInidicador = array_sum($varArrayPromedio);
                                $varArrayInidicador1 = array_sum($varArrayPromedio1);
                                $varArrayInidicador2 = array_sum($varArrayPromedio2);
                                $varArrayInidicador3 = array_sum($varArrayPromedio3);                       



                                // var_dump($varArrayInidicador);
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
                              // $varcantidadproceso = Yii::$app->db->createCommand("select cantproceso from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid")->queryScalar();
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

                                // $varcantidadproceso = Yii::$app->db->createCommand("select cantproceso from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and callid = $txtCallid")->queryScalar();

                                if ($varcantidadproceso == null) {
                                  $varcantidadproceso = 0;
                                }

                                array_push($varArrayPromedio, $varcantidadproceso);
                              }

                              $varArrayInidicador = array_sum($varArrayPromedio);
                            }
                          }
                        }
            //  Calcula el promedio
                        if ($varArrayInidicador != 0) { 
                          if ($txtTipoFormIndicador == 0) {
                             //var_dump($varArrayInidicador);
                             //var_dump($txtTotalLlamadas);
                            $txtRtaProcentaje = (round(($varArrayInidicador / $txtTotalLlamadas) * 100, 1));
                            if($varListadorespo){
                                $txtRtaProcentaje1 = (round(($varArrayInidicador1 / $txtTotalLlamadas) * 100, 1));
                                $txtRtaProcentaje2 = (round(($varArrayInidicador2 / $txtTotalLlamadas) * 100, 1));
                                $txtRtaProcentaje3 = (round(($varArrayInidicador3 / $txtTotalLlamadas) * 100, 1));
                                $txtacumulapromedio1 = $txtacumulapromedio1 + $txtRtaProcentaje1;
                                $txtacumulapromedio2 = $txtacumulapromedio2 + $txtRtaProcentaje2;
                                $txtacumulapromedio3 = $txtacumulapromedio3 + $txtRtaProcentaje3;
                              }
                          }else{
                            if ($txtTipoFormIndicador == 1) {
                              // var_dump("Hola Uno");
                              $txtRtaProcentaje = (100 - (round(($varArrayInidicador / $txtTotalLlamadas) * 100, 1)));
                              if($varListadorespo){
                                $txtRtaProcentaje1 = (100 - (round(($varArrayInidicador1 / $txtTotalLlamadas) * 100, 1)));
                                $txtRtaProcentaje2 = (100 - (round(($varArrayInidicador2 / $txtTotalLlamadas) * 100, 1)));
                                $txtRtaProcentaje3 = (100 - (round(($varArrayInidicador3 / $txtTotalLlamadas) * 100, 1)));
                                $txtacumulapromedio1 = $txtacumulapromedio1 + $txtRtaProcentaje1;
                                $txtacumulapromedio2 = $txtacumulapromedio2 + $txtRtaProcentaje2;
                                $txtacumulapromedio3 = $txtacumulapromedio3 + $txtRtaProcentaje3;
                              
                              
                                if($varArrayInidicador1== 0){
                                  $txtRtaProcentaje1 = 0;
                                }
                                if($varArrayInidicador2== 0){
                                  $txtRtaProcentaje2 = 0;
                                }
                                if($varArrayInidicador3== 0){
                                  $txtRtaProcentaje3 = 0;
                                }
                              }
                            }                      
                          }     
                        }else{
                          if ($txtTipoFormIndicador == 1) {
                            $txtRtaProcentaje = 100;
                            if($varListadorespo){
                                foreach ($varListVariables2 as $key => $value) {
                                  $varresponsable = $value['responsable'];
                                  if ($varresponsable == 1){
                                    $txtRtaProcentaje1 = 100;
                                  }
                                  else{
                                    if ($varresponsable == 2){
                                      $txtRtaProcentaje2 = 100;
                                    }else{
                                      if ($varresponsable == 3){
                                        $txtRtaProcentaje3 = 100;
                                      }
                                    }
                                  }
                                }
                              }

                          }else{
                            if ($txtTipoFormIndicador == 0) {
                              $txtRtaProcentaje = 0;
                              if($varListadorespo){
                                foreach ($varListVariables2 as $key => $value) {
                                  $varresponsable = $value['responsable'];
                                  if ($varresponsable == 1){
                                    $txtRtaProcentaje1 = 0;
                                  }
                                  else{
                                    if ($varresponsable == 2){
                                      $txtRtaProcentaje2 = 0;
                                    }else{
                                      if ($varresponsable == 3){
                                        $txtRtaProcentaje3 = 0;
                                      }
                                    }
                                  }
                                }
                              }
                            }                            
                          }   
                        }
                        

                        array_push($titulos, $txtRtaProcentaje);

                        $varNum += 1;
                        $prueba = "doughnut-chart".$varNum;  
                        $prueba2 = "idchart_indi".$varNum; 
                        $prueba3 = "idchart_rta".$varNum;
                        if($varListadorespo){
                            array_push($arraylistaagente, $txtRtaProcentaje1);
                            array_push($arraylistacanal, $txtRtaProcentaje2);
                            array_push($arraylistamarca, $txtRtaProcentaje3);
                            array_push($arraylistanombre, $txtNombreCategoria);
                          }
                          $arraylistaresponsables = array($txtNombreCategoria,$txtRtaProcentaje1,$txtRtaProcentaje2,$txtRtaProcentaje3);
                    ?>
                    <input type="text" id="<?php echo $prueba2; ?>" name="datetimes" readonly="readonly" value="<?php echo  $txtNombreCategoria; ?>" class="hidden">
                    <input type="text" id="<?php echo $prueba3; ?>" name="datetimes" readonly="readonly" value="<?php echo  round($txtRtaProcentaje,2); ?>" class="hidden">
                <?php

                        if (count($varListIndicadores) <= 8) { 
                            if($varListadorespo){ 
                                $txtacumulapromedio1 = $txtacumulapromedio1 + $txtRtaProcentaje1;
                                $txtacumulapromedio2 = $txtacumulapromedio2 + $txtRtaProcentaje2;
                                $txtacumulapromedio3 = $txtacumulapromedio3 + $txtRtaProcentaje3;
                            } 
                  ?>
                    <td class="text-center" width="100"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="<?php echo $prueba; ?>"></canvas></div><?php echo round($txtRtaProcentaje,2).' %'; ?></td>   
                  <?php
                      }else{
                  ?>    
                      <td class="text-center" width="100"><div style="width: 70px; height: 70px;  display:block; margin:auto;"><canvas id="<?php echo $prueba; ?>"></canvas></div><?php echo round($txtRtaProcentaje,2).' %'; ?></td>     
                  <?php } } 
                  if($varListadorespo){
                    $txttotalpromedio1 = $txtacumulapromedio1 / count($varListIndicadores);
                    $txttotalpromedio2 = $txtacumulapromedio2 / count($varListIndicadores);
                    $txttotalpromedio3 = $txtacumulapromedio3 / count($varListIndicadores);
                    //$arraylistaresponsable = array($txttotalpromedio1,$txttotalpromedio2,$txttotalpromedio3);
                    $txtsinvalor = '--';  
                  }
                  ?>
                </tr>
              </table>              
            <br>
            <?php if($varListadorespo){ 
                  if($arrayVaridcatAgente != ""){ 
             $varcolor2 = "color: #f5500f;";
             ?>
            
            <div class="row">
              <div class="col-md-6">              
                <div class="card2 mb"> 
                                                  
                     <table id="myTable0"  class="table table-striped table-bordered detail-view formDinamico">
                     <caption>...</caption>
                          <thead>
                            <tr>
                              <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable1(4, 'str')"><?= Yii::t('app', 'Indicadores ') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                              <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable1(0, 'str')"><?= Yii::t('app', 'Agente ') ?><span class="glyphicon glyphicon-chevron-down"></span></th>                  
                              <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable1(2, 'int')"><?= Yii::t('app', 'Canal ') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                              <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable1(2, 'int')"><?= Yii::t('app', 'Marca ') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                            </tr>
                          </thead>
                          <tbody>
                          
                          <?php  
                        $sumaagente = 0; 
                              $sumacanal = 0;
                              $sumamarca = 0;                        
                            for($i = 0; $i < count($arraylistaagente); $i++){
                               if($arraylistaagente[$i]) {
                                 $sumaagente = $sumaagente + 1;  
                               }
                               if($arraylistacanal[$i]) {
                                 $sumacanal = $sumacanal + 1;  
                               }
                               if($arraylistamarca[$i]) {
                                 $sumamarca = $sumamarca + 1; 
                               }
                                $arraytotalagente = $arraytotalagente + $arraylistaagente[$i];
                                $arraytotalcanal = $arraytotalcanal + $arraylistacanal[$i];
                                $arraytotalmarca = $arraytotalmarca + $arraylistamarca[$i];
                              ?>
                              <tr>
                                <td class="text-left"><?php echo $arraylistanombre[$i]; ?></td>
                                <td class="text-center" width="100" <?php  if(round($arraylistaagente[$i],2) >= 95) {?>style="font-weight: bold; color: #298f09;"<?php } ?> <?php if(round($arraylistaagente[$i],2) < 95 && round($arraylistaagente[$i],2) > 75) {?>style="font-weight: bold; color: #f5e50a;"<?php } ?> <?php  if(round($arraylistaagente[$i],2) < 75 && round($arraylistaagente[$i],2) > 1) {?>style="font-weight: bold; color: #db6d07;"<?php } ?> ><div ></div><?php if(round($arraylistaagente[$i],2) == 0){ } else{ echo round($arraylistaagente[$i],2).' % ';}?></td>
                                <td class="text-center" width="100" <?php  if(round($arraylistacanal[$i],2) >= 95)  {?>style="font-weight: bold; color: #298f09;"<?php } ?> <?php if(round($arraylistacanal[$i],2) < 95 && round($arraylistacanal[$i],2) > 75) {?>style="font-weight: bold; color: #f5e50a;"<?php } ?> <?php  if(round($arraylistacanal[$i],2) < 75 && round($arraylistacanal[$i],2) > 1) {?>style="font-weight: bold; color: #db6d07;"<?php } ?> ><div ></div><?php if(round($arraylistacanal[$i],2) == 0){ } else{ echo round($arraylistacanal[$i],2).' % ';}?></td>
                                <td class="text-center" width="100" <?php  if(round($arraylistamarca[$i],2) >= 95)  {?>style="font-weight: bold; color: #298f09;"<?php } ?> <?php if(round($arraylistamarca[$i],2) < 95 && round($arraylistamarca[$i],2) > 75) {?>style="font-weight: bold; color: #f5e50a;"<?php } ?> <?php  if(round($arraylistamarca[$i],2) < 75 && round($arraylistamarca[$i],2) > 1) {?>style="font-weight: bold; color: #db6d07;"<?php } ?>><div ></div><?php if(round($arraylistamarca[$i],2) == 0){ } else{ echo round($arraylistamarca[$i],2).' % ';}?></td>
                              <?php } ?>  
                              </tr>                              
                          </tbody>
                      </table>
                </div>
              </div> 
              <div class="col-md-6">
                <div class="card2 mb">
         <?php

                    if ($arraytotalagente != 0 && $sumaagente !=0){
                        $txttotalpromedio1 = $arraytotalagente / $sumaagente;
                      }
                      if ($arraytotalcanal != 0 && $sumacanal !=0){
                      $txttotalpromedio2 = $arraytotalcanal / $sumacanal;
                      }
                    if ($arraytotalmarca != 0 && $sumamarca !=0){
                    $txttotalpromedio3 = $arraytotalmarca / $sumamarca;}
                    $arraylistaresponsable = array($txttotalpromedio1,$txttotalpromedio2,$txttotalpromedio3);
                     ?>
                    <div id="containerResponsable" class="highcharts-container" style="width: 600px; height: 180px;  display:block; margin:auto;"></div>
                     <br>               
                     <div class="row" align="center">
            
                      <?= Html::button('Total Agentes', ['value' => url::to(['totalizaragentes', 'arbol_idV' => $txtServicio, 'parametros_idV' => $txtParametros, 'codparametrizar' => $txtCodParametrizar, 'codigoPCRC' => $txtCodPcrcok, 'indicador' => $varindica, 'nomFechaI' => $txtFechaIni, 'nomFechaF' => $txtFechaFin]), 'class' => 'btn btn-success', 'id'=>'modalButton3',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'enviodatosr()', 
                                'title' => 'Total Agentes', 'style' => 'background-color: #337ab7', 'style' => 'width:200px']) ?> 

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
            <?php } } ?> 
            <br>
            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>  
              <?= $form->field($model, 'idcategoria')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'indicadorID'])->label('Indicadores...') ?>
              <?= $form->field($model, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 350, 'class' => 'hidden', 'value'=>$txtCodPcrcok, 'id'=>'txtIdCod_pcrc']) ?>
            <br>
            <div class="row" align="center">  
              <?= Html::submitButton(Yii::t('app', 'Buscar Indicador'),
                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'onclick' => 'openview();',
                            'title' => 'Buscar']) 
              ?>
            </div>
            <?php ActiveForm::end(); ?>
            <br>     
              <div id="panelvar" class="panelvariables" style="display: none;">
                <div class="subterceralinea">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="card2 mb">
                        <table id="myTable0"  class="table table-striped table-bordered detail-view formDinamico">
                        <caption>...</caption>
                          <thead>
                            <tr>
                              <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable1(4, 'str')"><?= Yii::t('app', 'Código Pcrc ') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                              <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable1(0, 'str')"><?= Yii::t('app', 'Variables ') ?><span class="glyphicon glyphicon-chevron-down"></span></th>                  
                              <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable1(2, 'int')"><?= Yii::t('app', 'cantidad Llamadas ') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                            </tr>
                          </thead>
                          <tbody>
                              <?php            
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
                                  //$txtvCantVari = Yii::$app->db->createCommand("select count(cantproceso)  from tbl_speech_general where anulado = 0 and programacliente in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and idindicador = $txtIdCatagoria and idvariable = $txtIdCatagoria")->queryScalar(); 

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
                                          // $txtParticipacion = 1 - $txtParticipacion;

                                        }
                                      }
                                    }
                                  }else{
                                    $txtParticipacion = 0;
                                  }

                                  array_push($arraylistvariable, $txtVariables);
                                  array_push($arraylistparticipacion, $txtParticipacion);
                                  
                              ?>
                                <tr>
                                  <td class="text-left"><?php echo $txtCodigoPcrc; ?></td>
                                  <td class="text-left"><?php echo $txtVariables; ?></td>
                                  <td class="text-center"><?php echo $txtvCantVari; ?></td>
                                </tr>
                              <?php 
                                } 
                              ?>
                          </tbody>
                        </table> 
                      </div>
                    </div>

                    <div class="col-md-6">
                      <div class="card2 mb">
                        <div id="containerVariable" class="highcharts-container"></div>
                      </div>
                    </div>
                  </div>
                  <br>
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card1 mb">
          <?php
                      $varhallazgo= "";
                      foreach ($varListIndicadores as $key => $value) {
                         
                        if($varName == $value['idcategoria']){
                          $varhallazgo = $value['hallazgo'];
                        }
                    ?>
                      
                    <?php 
                      }
                    ?>
                      <label style="font-size: 15px;"><em class="fas fa-comment-dots" style="font-size: 20px; color: #00968F;"></em>  Hallazgo: </label>
                      <label style="font-size: 15px;"><?php echo $varhallazgo ?></label>
                      </div>
                    </div>
                  </div>
                </div>
              </div>

            </div>
          </div>
        </div>
      </div>
    </div>
    <br>  
</div>
<?php
  if (count($txtvDatosMotivos) != 0) {    
?>
<div id="capaCuatro" style="display: inline">
  <div id="dtbloque3" class="col-sm-12" style="display: inline">
    <hr>
    <div class="cuartalinea">
      <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">
            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?> 
                <div class="row">
                  <div class="col-sm-6">
                    <label for="txtIndicadores">Indicadores...</label>
                      <select class ='form-control' id="txtIndicadores" data-toggle="tooltip" title="Indicadores" onchange="listai();" class ='form-control'>
                              <option value="" disabled selected>Seleccionar...</option>  
                                  <?php                          
                                      foreach ($varListIndicadores as $key => $value) {
                                        echo "<option value = '".$value['idcategoria']."'>".$value['nombre']."</option>";
                                      }
                                  ?>
                      </select> 
                  </div>
                  <div class="col-sm-6">
                    <label for="txtVariables">Variables...</label>
                      <select class ='form-control' id="txtVariables" data-toggle="tooltip" title="Variables" onchange="listav();" class ='form-control'>
                              <option value="" disabled selected>Seleccionar...</option>
                      </select>
                  </div>
                </div>
                  <?= $form->field($model, 'idcategoria')->textInput(['class'=>'hidden','maxlength' => true, 'id'=>'indicadorID2'])  ?>
                  <?= $form->field($model, 'nombre')->textInput(['class'=>'hidden','maxlength' => true, 'value' => $txtCodPcrcok])  ?>
                    
                    <div class="row" align="center">  
                              <?= Html::submitButton(Yii::t('app', 'Buscar Variable'),
                                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                            'data-toggle' => 'tooltip', 'onclick' => 'openview2();',
                                            'title' => 'Buscar']) 
                              ?>
                    </div> 
                  
            <?php ActiveForm::end(); ?> 
            <br>

            <div class="subcuartalinea">
              <div class="row">
                <div class="col-md-6">
                  <div class="card2 mb">
                    <table id="myTable"  class="table table-striped table-bordered detail-view formDinamico">
                    <caption>...</caption>
                        <thead>
                          <tr>
                            <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable(0, 'str')"><?= Yii::t('app', 'Motivos de Llamada') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                            <th scope="col" class="text-center"  style="font-size: 15px; cursor: pointer" onclick="sortTable(1, 'int')"><?= Yii::t('app', '% de Llamadas') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                            <th scope="col" class="text-center"  style="font-size: 15px; cursor: pointer" onclick="sortTable(4, 'int')"><?= Yii::t('app', ' '.$varName2.' Por Motivo Llamada') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                            
                          </tr>           
                        </thead>
                        <tbody>
                          <?php                            
                            
                            foreach ($txtvDatosMotivos as $key => $value) {
                              $varMotivos = $value['nombre'];               
                              $varIdCatagoria = $value['idcategoria'];
                              // var_dump($varIdCatagoria);

                              $txtvCantMotivos1 = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardspeechcalls  where idcategoria = '$varIdCatagoria' and servicio in ('$txtServicio') and extension in ('$txtParametros') and fechallamada between '$varInicioF' and '$varFinF' and anulado = 0")->queryScalar();
                              $txtvCantMotivos = intval($txtvCantMotivos1);
                              // var_dump($varIdCatagoria);

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
                                //$txtRtaVariable = round(($txtcoincidencia / $txtrtaConteo)*100,2);
                              }else{
                                // var_dump($txtcoincidencia);
                                // var_dump($txtvCantMotivos);
                                // var_dump($txtTotalLlamadas);
                                $txtRtaVar = 0;
                                $txtRtaVariable = 0;
                              }

                              array_push($varListName, $varMotivos);
                              array_push($varListDuracion, round($txtvCantSeg2));
                              array_push($varListCantidad, $txtvCantMotivos);
                          ?>
                            <tr>
                              <td class="text-left"><?php echo $varMotivos; ?></td>
                              <td class="text-center"><?php echo $txtParticipación2." %"; ?></td>
                              <td class="text-center"><?php echo " ".$txtRtaVar." %"; ?></td>
                            </tr>
                          <?php
                            }
                          ?>
                        </tbody>
                      </table>
                  </div>
                </div>

                <div class="col-md-6">
                  <div class="card2 mb">
                    <div id="containerTMO" class="highcharts-container"> 
                  </div>
                </div>
              </div>              
            </div>

          </div>
        </div>
      </div>
    </div><br>
  </div>  
  </div>
</div>
<?php } ?>
</div>
<hr>
<?php if ($sessiones == '7' || $sessiones == '2953' || $sessiones == '69' || $sessiones == '438' || $sessiones == '1083' || $sessiones == '3595' || $sessiones == '3656' || $sessiones == '3205' || $sessiones == '312') {
 ?>
<div id="capaCinco" style="display: inline">
  <div id="dtbloque5" class="col-sm-12" style="display: inline">
    <hr>
    <div class="quintalinea">
      <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">
            <div class="row">
              <div class="col-md-6">
                <div class="card2 mb">
                  <label style="font-size: 15px;"><em class="fas fa-percent" style="font-size: 15px; color: #827DF9;"></em> Porcentaje de Indicadores: <?php echo $txtRtaProcentajeindicador.' %'; ?></label>
                </div>
              </div>
              <div class="col-md-6">
                <div class="card2 mb">
                  <label style="font-size: 15px;"><em class="fas fa-percent" style="font-size: 15px; color: #827DF9;"></em> Porcentaje de Motivos Contacto: <?php echo $txtRtaProcentajeMotivos.' %'; ?></label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <br>
  </div>
</div>
<?php } ?>

<div id="capaCuatro" style="display: inline">
  <div id="dtbloque3" class="col-sm-12" style="display: inline">
    <hr>
    <div class="cuartalinea">
      <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">
            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?> 
              <div class="row">
                <div class="col-sm-6">
                  <label for="txtIndicadores">Indicadores...</label>
                  <select class ='form-control' id="txtIndicadores1" data-toggle="tooltip" title="Indicadores" onchange="listai1();" class ='form-control'>
                    <option value="" disabled selected>Seleccionar...</option>  
                      <?php                          
                        foreach ($varListIndicadores as $key => $value) {
                          echo "<option value = '".$value['idcategoria']."'>".$value['nombre']."</option>";
                        }
                      ?>
                  </select> 
                </div>
                <div class="col-sm-6">
                  <label for="txtVariables">Variables...</label>
                  <select class ='form-control' id="txtVariables1" data-toggle="tooltip" title="Variables" onchange="listav1();" class ='form-control'>
                    <option value="" disabled selected>Seleccionar...</option>
                  </select>
                </div>
                <?= $form->field($model3, 'extension')->textInput(['class'=>'hidden','maxlength' => true, 'id'=>'indicadorID21'])  ?>
                <?= $form->field($model3, 'nombreCategoria')->textInput(['class'=>'hidden','maxlength' => true, 'value' => $txtCodPcrcok])  ?>
                <div class="row" align="center">
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
    </div><br>
  </div>
</div>

<div id="CapaCero" style="display: inline"> 
<div class="col-md-12">
<hr>
  <div class="card1 mb">
    <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
    <div class="row">
      <div class="col-md-3">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar dashboard: </label>
          <?= Html::button('Exportar', ['value' => url::to(['categoriasvoice', 'arbol_idV' => $txtServicio, 'parametros_idV' => $txtParametros, 'codigoPCRC' => $txtCodPcrcok, 'codparametrizar' => $txtCodParametrizar, 'indicador' => $varindica, 'nomFechaI' => $txtFechaIni, 'nomFechaF' => $txtFechaFin]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                        'data-toggle' => 'tooltip',
                        'title' => 'Exportar', 'style' => 'background-color: #337ab7']) ?> 

          <?php
            Modal::begin([
              'header' => '<h4>Procesando datos en archivo de excel...</h4>',
              'id' => 'modal1',
              // 'size' => 'modal-lg',
            ]);

            echo "<div id='modalContent1'></div>";
                                          
            Modal::end(); 
          ?>  
        </div>
      </div>
      <div class="col-md-3">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar general: </label>
          <?= Html::button('Exportar', ['value' => url::to(['categoriasgeneral', 'arbol_idV' => $txtServicio, 'parametros_idV' => $txtParametros, 'codparametrizar' => $txtCodParametrizar, 'codigoPCRC' => $txtCodPcrcok, 'indicador' => $varindica, 'nomFechaI' => $txtFechaIni, 'nomFechaF' => $txtFechaFin]), 'class' => 'btn btn-success', 'id'=>'modalButton2',
                        'data-toggle' => 'tooltip',
                        'title' => 'Exportar', 'style' => 'background-color: #337ab7']) ?> 

          <?php
            Modal::begin([
              'header' => '<h4>Envio de datos al correo corporativo...</h4>',
              'id' => 'modal2',
              // 'size' => 'modal-lg',
            ]);

            echo "<div id='modalContent2'></div>";
                                          
            Modal::end(); 
          ?>
        </div>
      </div>
      <div class="col-md-3">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Regresar: </label> 
          <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
          ?>
        </div>
      </div>
      <?php // if ($sessiones != '0') {  ?>
        <div class="col-md-3">
          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-phone-square" style="font-size: 15px; color: #FFC72C;"></em> Buscar llamadas: </label>
            <?= Html::a('Llamadas',  ['searchllamadas', 'varprograma'=>$varNamePCRC, 'varcodigopcrc'=>$txtCodPcrcok, 'varidcategoria'=>$txtIdCatagoria1, 'varextension'=>$txtParametros, 'varfechasinicio'=>$varInicioF, 'varfechasfin'=>$varFinF, 'varcantllamadas'=>$txtTotalLlamadas, 'varfechainireal'=>$txtFechaIni, 'varfechafinreal'=>$txtFechaFin,'varcodigos'=>$varCodigo], ['class' => 'btn btn-success',
                          'style' => 'background-color: #337ab7', 'target' => "_blank",
                          'data-toggle' => 'tooltip',
                          'title' => 'Buscar llamadas']) 
            ?>
          </div>
        </div>
      <?php // } ?>
    </div>

    <?php if ($sessiones == '57' || $sessiones == '2953' || $sessiones == '3229' || $sessiones == '3468' ) { ?>
    <br>
    <div class="row">
      <div class="col-md-3">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Guardar valores: </label>
            <div onclick="saveindicadores();" class="btn btn-primary" style="display:inline; height: 34px;" method='post' id="botones2" >
                Guardar
            </div>
        </div>
      </div>
    </div>
    <?php } ?>

  </div>
</div>
</div>


<script type="text/javascript">
var varDataNum = [<?= join($titulos, ',')?>];
// console.log(varDataNum);
$(document).ready(function(){
  console.log(varDataNum.length);
  for (var i = 0; i < varDataNum.length; i++) {  
    var varNume = i + 1;  
    var nombre_Chart = "doughnut-chart" + varNume;
    // console.log(nombre_Chart);
    var array_Temp = [];
    array_Temp.push(varDataNum[i]);
    array_Temp.push(100-varDataNum[i]);
    // console.log(array_Temp);
    new Chart(document.getElementById(nombre_Chart), {
        type: 'doughnut',
        data: {      
          datasets: [
            {         
              labels : ['Porcentaje: '],
              backgroundColor: ["#559FFF"],
              data: array_Temp
            }
          ]
        }
    });  
  }
});

function sortTable(n,type) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
   
    table = document.getElementById("myTable");
    switching = true;
    //Set the sorting direction to ascending:
    dir = "asc";
   
    /*Make a loop that will continue until no switching has been done:*/
    while (switching) {
      //start by saying: no switching is done:
      switching = false;
      rows = table.rows;
      /*Loop through all table rows (except the first, which contains table headers):*/
      for (i = 1; i < (rows.length - 1); i++) {
        //start by saying there should be no switching:
        shouldSwitch = false;
        /*Get the two elements you want to compare, one from current row and one from the next:*/
        x = rows[i].getElementsByTagName("TD")[n];
        y = rows[i + 1].getElementsByTagName("TD")[n];
        /*check if the two rows should switch place, based on the direction, asc or desc:*/
        if (dir == "asc") {
          if ((type=="str" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) || (type=="int" && parseFloat(x.innerHTML) > parseFloat(y.innerHTML))) {
            //if so, mark as a switch and break the loop:
            shouldSwitch= true;
            break;
          }
        } else if (dir == "desc") {
          if ((type=="str" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) || (type=="int" && parseFloat(x.innerHTML) < parseFloat(y.innerHTML))) {
            //if so, mark as a switch and break the loop:
            shouldSwitch = true;
            break;
          }
        }
      }
      if (shouldSwitch) {
        /*If a switch has been marked, make the switch and mark that a switch has been done:*/
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        //Each time a switch is done, increase this count by 1:
        switchcount ++;
      } else {
        /*If no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again.*/
        if (switchcount == 0 && dir == "asc") {
          dir = "desc";
          switching = true;
        }
      }
    }
  };

    function sortTable1(n,type) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
   
    table = document.getElementById("myTable0");
    switching = true;
    //Set the sorting direction to ascending:
    dir = "asc";
   
    /*Make a loop that will continue until no switching has been done:*/
    while (switching) {
      //start by saying: no switching is done:
      switching = false;
      rows = table.rows;
      /*Loop through all table rows (except the first, which contains table headers):*/
      for (i = 1; i < (rows.length - 1); i++) {
        //start by saying there should be no switching:
        shouldSwitch = false;
        /*Get the two elements you want to compare, one from current row and one from the next:*/
        x = rows[i].getElementsByTagName("TD")[n];
        y = rows[i + 1].getElementsByTagName("TD")[n];
        /*check if the two rows should switch place, based on the direction, asc or desc:*/
        if (dir == "asc") {
          if ((type=="str" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) || (type=="int" && parseFloat(x.innerHTML) > parseFloat(y.innerHTML))) {
            //if so, mark as a switch and break the loop:
            shouldSwitch= true;
            break;
          }
        } else if (dir == "desc") {
          if ((type=="str" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) || (type=="int" && parseFloat(x.innerHTML) < parseFloat(y.innerHTML))) {
            //if so, mark as a switch and break the loop:
            shouldSwitch = true;
            break;
          }
        }
      }
      if (shouldSwitch) {
        /*If a switch has been marked, make the switch and mark that a switch has been done:*/
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        //Each time a switch is done, increase this count by 1:
        switchcount ++;
      } else {
        /*If no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again.*/
        if (switchcount == 0 && dir == "asc") {
          dir = "desc";
          switching = true;
        }
      }
    }
  };

  function listai(){
    var varIndicador = document.getElementById("txtIndicadores").value;
    var varServicios = "<?php echo $varProgramas; ?>";
    var varParametros = "<?php echo $txtParametros; ?>";
    var varfechainic = "<?php echo $fechaIniCat; ?>";
    var varfechafinc = "<?php echo $fechaFinCat; ?>";
    var varcodigo = "<?php echo $txtCodPcrcok; ?>";
    // console.log(varServicios);
    // console.log(varParametros);
    // console.log(varIndicador);
    // console.log(varfechainic);
    // console.log(varfechafinc);
    // console.log(varcodigo);
    // document.getElementById("indicadorID3").value = varIndicador;

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
                          // console.log(Rta);
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
                    // console.log(numRta);
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
    // console.log(varServicios);
    // console.log(varParametros);
    // console.log(varIndicador);
    // console.log(varfechainic);
    // console.log(varfechafinc);
    // console.log(varcodigo);
    // document.getElementById("indicadorID3").value = varIndicador;

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
                          // console.log(Rta);
                          document.getElementById("txtVariables1").innerHTML = "";
                          var node = document.createElement("OPTION");
                          node.setAttribute("value", "");
                          var textnode = document.createTextNode("Seleccionar...");
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
                    // console.log(numRta);
                }
            });
    
  };

  var varview = document.getElementById("panelvar");
  var varfunciona = "<?php echo $varindica; ?>";
  console.log(varfunciona);

  if (varfunciona != "") {
  varview.style.display = 'inline';
  }
  function openview() {
    var varselectindi = document.getElementById("indicadorID").value;

    if (varselectindi == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debes de seleccionar un indicador.","warning");
      return;
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

  $(function() {
        var Listado2 = "<?php echo implode($arraylistvariable,",");?>";
        Listado2 = Listado2.split(",");

        var Listado = "<?php echo implode($varListName,",");?>";
        Listado = Listado.split(",");

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

        var Listadorespon = "<?php echo implode($responsable,",");?>";
        Listadorespon = Listadorespon.split(",");

        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

                $('#containerVariable').highcharts({
            chart: {
                borderColor: '#DAD9D9',
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
                    color: '#3C74AA'
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
              name: 'Porcentaje de participación',
              data: [<?= join($arraylistparticipacion, ',')?>],
              color: '#C148D0'
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
                text: 'Cantidad de Llamadas'
              }
            },{
              title: {
                text: 'Duracion de Llamadas'
              },
              opposite: true
            }],  

            title: {
              text: 'Grafica de Motivos de Contacto vs TMO',
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
              name: 'Cantidad de llamadas',
              data: [<?= join($varListCantidad, ',')?>],
              color: '#4298B5'
            },{
              name: 'Duración de llamadas',
              data: [<?= join($varListDuracion, ',')?>],
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
                text: 'Cantidad de Llamadas'
              }
            }, 

            title: {
              text: 'Grafica Top de Asesores MAS categorizadas / ' + varNombretop,
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
              name: 'Cantidad de llamadas',
              data: [<?= join($varListCantiVar5, ',')?>],
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
                text: 'Cantidad de Llamadas'
              }
            }, 

            title: {
              text: 'Grafica Top de Asesores MENOS categorizadas / ' + varNombretop,
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
              name: 'Cantidad de llamadas',
              data: [<?= join($varListCantiVar, ',')?>],
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
                text: 'Cantidad de Llamadas'
              }
            }, 

            title: {
              text: 'Grafica Top de Asesores categorizadas / ' + varNombretop,
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
              name: 'Cantidad de llamadas',
              data: [<?= join($varListCantiVar0, ',')?>],
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

          // para el IDA responsables

          $('#containerResponsable').highcharts({
            chart: {
                borderColor: '#DAD9D9',
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
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: Listadorespon,
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
              name: '% de responsable',
              data: [<?= join($arraylistaresponsable, ',')?>],
              color: '#627cf0'
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

        Highcharts.getOptions().exporting.buttons.contextButton.menuItems.push({
            text: 'Additional Button',
            onclick: function() {
              alert('OK');
              /*call custom function here*/
            }
        });
    });

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
                      // $("#modal2").modal("hide");
                   //   location.reload();
                  }                    
              }
          });
    }

    function saveindicadores(){
        var varvarServicio = "<?php echo $varServicio; ?>";
        var vartxtCodPcrcokc1 = "<?php echo $txtCodPcrcokc1; ?>";
        var vartxtnombrepcrc = "<?php echo $txtnombrepcrc; ?>";
        var vartxtFechaIni = "<?php echo $txtFechaIni; ?>";
        var vartxtFechaFin = "<?php echo $txtFechaFin; ?>";
        var varvarParams = "<?php echo $varParams; ?>";
        var vartxtTotalLlamadas = "<?php echo $txtTotalLlamadas; ?>";
        var varvarCodigo = "<?php echo $varCodigo; ?>";

        var varDataNumeros = [<?= join($titulos, ',')?>];

        for (var i = 0; i < varDataNumeros.length; i++) {
            var varNumbers = i + 1;  
            var varidnameindi = document.getElementById('idchart_indi'+varNumbers).value;
            var varidporcentaje = document.getElementById('idchart_rta'+varNumbers).value;

            $.ajax({
                method: "get",
                url: "guardarindicadores",
                data: {
                    txtvarvarServicio : varvarServicio,
                    txtvartxtCodPcrcokc1 : vartxtCodPcrcokc1,
                    txtvartxtnombrepcrc : vartxtnombrepcrc,
                    txtvartxtFechaIni : vartxtFechaIni,
                    txtvartxtFechaFin : vartxtFechaFin,
                    txtvarvarParams : varvarParams,
                    txtvartxtTotalLlamadas : vartxtTotalLlamadas,
                    txtvarvarCodigo : varvarCodigo,
                    txtvaridnameindi : varidnameindi,
                    txtvaridporcentaje : varidporcentaje,
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);
                }
            });
        }       

        window.open('../dashboardspeech/index','_self');

    };

</script>