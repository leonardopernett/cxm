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
use app\models\ProcesosClienteCentrocosto;

  $this->title = 'Procesos Q&S - Búsqueda Por Personas';
  $this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol->select(['tbl_roles.role_id'])
      ->from('tbl_roles')
      ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                  'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
      ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
      ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

  // Colores en Codigo;
  $varMalo = '#D01E53';
  $varBueno = '#00968F';
  $varEstable = '#FFC72C';

  $varSinData = '--';


?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css">
<style type="text/css">
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
        height: 80px;
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

    .card3 {
        height: 270px;
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

    .card4 {
        height: 180px;
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

<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>

<!-- Capa Procesos del encabezado -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<!-- Capa de Informacion -->
<div id="idCapaInformativa" class="capaInformativas" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Ficha Técnica') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Servicios:') ?></label>  

        <div class="col-md-12 text-center">          
          <div onclick="opennovedads();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbn1" >
            <label style="text-align: center;"><?= Yii::t('app', 'Abrir Listado Servicios [ + ]') ?></label>
          </div> 
                              
          <div onclick="closenovedads();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbn2" >
            <label style="text-align: center;"><?= Yii::t('app', 'Cerrar Listado Servicios [ - ]') ?></label>
          </div> 
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-users" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Listado Personal:') ?></label>  

        <div class="col-md-12 text-center">          
          <div onclick="opennovedadp();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbn3" >
            <label style="text-align: center;"><?= Yii::t('app', 'Abrir Listado Personal [ + ]') ?></label>
          </div> 
                              
          <div onclick="closenovedadp();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbn4" >
            <label style="text-align: center;"><?= Yii::t('app', 'Cerrar Listado Personal [ - ]') ?></label>
          </div> 
        </div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Dimensión:') ?></label>  
        <label style="text-align: center;"><?= Yii::t('app', $varTextoDimensionp) ?></label>       
      </div> 
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-calendar" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Rango de Fechas:') ?></label>  
        <label style="text-align: center;"><?= Yii::t('app', $varFechainicial.' - '.$varFechaFinal) ?></label>       
      </div> 
    </div>

  </div>


  <div class="capaListaServicios" id="capaListaServicios" style="display: none;">
    <br>
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Listado de Servicios:') ?></label>   
          <?php
            foreach ($varListaServicios as $key => $value) {
          ?>
            <label style="text-align: left;"><?= Yii::t('app', '* '.$value['cliente']) ?></label>
          <?php
            }
          ?>
        </div>
      </div>
    </div>
    <br>
  </div>

  <div class="capaListaPersonal" id="capaListaPersonal" style="display: none;">
    <br>
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Listado de Personal:') ?></label>   
          <?php
            foreach ($varListaNombresGerentes as $key => $value) {
          ?>
            <label style="text-align: left;"><?= Yii::t('app', '* '.$value['gerente_cuenta']) ?></label>
          <?php
            }
          ?>
        </div>
      </div>
    </div>
    <br>
  </div>


</div>

<hr>

<!-- Capa de Estadistica -->
<div id="idCapaEstadistica" class="capaEstadistica" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados por Procesamientos') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">

    <?php
    $varArrayRtaAutoGeneral = array();
    $varArrayRtaAgenteGeneral = array();
    $varArrayRtaCanalGeneral = array();
    $varArrayRtaMarcaGeneral = array();
    $varCantidadCodPcrcGeneral = 0;
    foreach ($varListaServicios as $key => $value) {
      $varIdDpCliente = $value['id_dp_clientes'];

      $varCantidadCodPcrcGeneral += 1;

      $varAgenteGeneral =  (new \yii\db\Query())
                            ->select(['ROUND(AVG(tbl_ideal_responsabilidad.agente),1) AS varMarca'])
                            ->from(['tbl_ideal_responsabilidad']) 
                            ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                            ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                            ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                            ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                            ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                            ->scalar();                

      array_push($varArrayRtaAgenteGeneral, $varAgenteGeneral);

      $varMarcaGeneral = (new \yii\db\Query())
                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.marca),1) AS varMarca'])
                          ->from(['tbl_ideal_responsabilidad']) 
                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                          ->scalar();

      array_push($varArrayRtaMarcaGeneral, $varMarcaGeneral);

      $varCanalMixtos = (new \yii\db\Query())
                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.canal),1) AS varMarca'])
                          ->from(['tbl_ideal_responsabilidad']) 
                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                          ->scalar();


      array_push($varArrayRtaCanalGeneral, $varCanalMixtos);

    }

    $varArrayAgenteP = round( array_sum($varArrayRtaAgenteGeneral) / $varCantidadCodPcrcGeneral,2);
    $varArrayMarcaP = round( array_sum($varArrayRtaMarcaGeneral) / $varCantidadCodPcrcGeneral,2);
    $varArrayCanalP = round( array_sum($varArrayRtaCanalGeneral) / $varCantidadCodPcrcGeneral,2);

    $varArrayRtaScoreGeneral = (new \yii\db\Query())
                          ->select(['ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore'])
                          ->from(['tbl_ejecucionformularios']) 

                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['in','tbl_dashboardspeechcalls.servicio',$varBolsitas])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionRN,$varExtensionExt,$varExtensionUsua])
                          ->andwhere(['in','tbl_dashboardspeechcalls.idcategoria',$varLlamadaId])
                          ->scalar();

      if ($varArrayRtaScoreGeneral == "") {
        $varArrayRtaScoreGeneral = 0;
      }

      $varArrayRestanteScoreGeneral = round( 100-$varArrayRtaScoreGeneral,2);

      if ($varArrayRestanteScoreGeneral < '80') {
        $varTColorMixtoP = $varMalo;
      }else{
        if ($varArrayRestanteScoreGeneral >= '90') {
          $varTColorMixtoP = $varBueno;
        }else{
          $varTColorMixtoP = $varEstable;
        }
      }

      $varArrayPromedioAutoGeneral = round( ($varArrayAgenteP + $varArrayCanalP) / 2,2);
      $varArrayRestanteAutoGeneral = round( 100 - $varArrayPromedioAutoGeneral,2);

      if ($varArrayPromedioAutoGeneral < '80') {
        $varTColorAutoP = $varMalo;
      }else{
        if ($varArrayPromedioAutoGeneral >= '90') {
          $varTColorAutoP = $varBueno;
        }else{
          $varTColorAutoP = $varEstable;
        }
      }

      if ($varArrayRtaScoreGeneral != 0) {
        $varArrayKonectaGeneral = round( ($varArrayRtaScoreGeneral+$varArrayPromedioAutoGeneral)/2,2);
        $varArrayRestanteKGeneral = round(100-$varArrayKonectaGeneral,2);
      }else{
        $varArrayKonectaGeneral = 0;
        $varArrayRestanteKGeneral = 100;
      }
      
      if ($varArrayKonectaGeneral < '80') {
        $varTColorGeneralP = $varMalo;
      }else{
        if ($varArrayKonectaGeneral >= '90') {
          $varTColorGeneralP = $varBueno;
        }else{
          $varTColorGeneralP = $varEstable;
        }
      }


    ?>

    <div class="col-md-6">
      <div class="card3 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Detalle de Procesamiento Mixto') ?></label>

        <table style="width:100%">
          <caption><?= Yii::t('app', ' .') ?></caption>
          <tr>
            <td scope="col" class="text-center" style="width: 100px;">
              <label style="font-size: 15px;"><?= Yii::t('app', 'Calidad: General Konecta') ?></label>
            </td>
            <td scope="col" class="text-center" style="width: 100px;">
              <label style="font-size: 15px;"><?= Yii::t('app', 'Calidad y Consistencia') ?></label>
            </td>
            <td scope="col" class="text-center" style="width: 100px;">
              <label style="font-size: 15px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label>
            </td>
          </tr>
          <tr>
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerGP"></canvas></div><span style="font-size: 15px;"><?php echo $varArrayKonectaGeneral.' %'; ?></span></td> 
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerMP"></canvas></div><span style="font-size: 15px;"><?php echo $varArrayRtaScoreGeneral.' %'; ?></span></td> 
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerAP"></canvas></div><span style="font-size: 15px;"><?php echo $varArrayPromedioAutoGeneral.' %'; ?></span></td>
          </tr>
        </table>

      </div>
    </div>

    <div class="col-md-6">
      <div class="card3 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Detalle Procesamiento Automatico') ?></label>
        <div id="chartContainerResposnabilidadesP" class="highcharts-container" style="height: 200px;"></div>
      </div>
    </div>

    
  </div>

</div>

<hr>

<!-- Capa de Acciones -->
<div id="idCapaAcciones" class="capaAcciones" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultado del Proceso - Lista de Servicios') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">     

      <?php

        $varConteoProcesoMixto = 0;
        foreach ($varListaServicios as $key => $value) {
          $varIdDpCliente = $value['id_dp_clientes'];

          $varConteoProcesoMixto += 1;

          if ($varIdExtensionc > '1') {
            $varRnIdealM =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','id_dp_clientes',$varIdDpCliente])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['rn'])
                                ->all();
          }else{
            $varRnIdealM =  (new \yii\db\Query())
                                ->select(['rn'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','id_dp_clientes',$varIdDpCliente])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','rn',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['rn'])
                                ->all();
          }

          if (count($varRnIdealM) != 0) {
            $varArrayRnM = array();
            foreach ($varRnIdealM as $key => $value) {
              array_push($varArrayRnM, $value['rn']);
            }

            $varExtensionesArraysM = implode("', '", $varArrayRnM);
            $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
            $varExtensionesMixtas = explode(",", $arrayExtensiones_downM);
          }else{

            if ($varIdExtensionc > '1') {
              $varExtM =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','id_dp_clientes',$varIdDpCliente])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['ext'])
                                ->all();
            }else{
              $varExtM =  (new \yii\db\Query())
                                ->select(['ext'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','id_dp_clientes',$varIdDpCliente])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','ext',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['ext'])
                                ->all();
            }

            if (count($varExtM) != 0) {
              $varArrayExtM = array();
              foreach ($varExtM as $key => $value) {
                array_push($varArrayExtM, $value['ext']);
              }

              $varExtensionesArraysM = implode("', '", $varArrayExtM);
              $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
              $varExtensionesMixtas = explode(",", $arrayExtensiones_downM);
            }else{

              if ($varIdExtensionc > '1') {
                $varUsuaM =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','id_dp_clientes',$varIdDpCliente])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                ->groupby(['usuared'])
                                ->all();
              }else{
                $varUsuaM =  (new \yii\db\Query())
                                ->select(['usuared'])
                                ->from(['tbl_speech_parametrizar'])            
                                ->where(['=','id_dp_clientes',$varIdDpCliente])
                                ->andwhere(['=','anulado',0])
                                ->andwhere(['=','usabilidad',1])
                                ->andwhere(['!=','usuared',''])
                                ->andwhere(['is','tipoparametro',null])
                                ->groupby(['usuared'])
                                ->all();
              }


              if (count($varUsuaM) != 0) {
                $varArrayUsuaM = array();
                foreach ($varUsuaM as $key => $value) {
                  array_push($varArrayUsuaM, $value['usuared']);
                }

                $varExtensionesArraysM = implode("', '", $varArrayUsuaM);
                $arrayExtensiones_downM = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysM);
                $varExtensionesMixtas = explode(",", $arrayExtensiones_downM);
              }else{
                $varExtensionesMixtas = "N0A";
              }
            }
          }

          $varNombreClienteMixtos = (new \yii\db\Query())
                                          ->select(['cliente'])
                                          ->from(['tbl_proceso_cliente_centrocosto'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','id_dp_clientes',$varIdDpCliente])
                                          ->groupby(['id_dp_clientes'])
                                          ->Scalar();

          $varAgenteMixtos = (new \yii\db\Query())
                                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.agente),1) AS varMarca'])
                                          ->from(['tbl_ideal_responsabilidad']) 
                                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                          ->scalar();       

          if ($varAgenteMixtos < '80') {
            $varTColorAgenteIdealP = $varMalo;
          }else{
            if ($varAgenteMixtos >= '90') {
              $varTColorAgenteIdealP = $varBueno;
            }else{
              $varTColorAgenteIdealP = $varEstable;
            }
          }

          $varMarcaMixtos = (new \yii\db\Query())
                                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.marca),1) AS varMarca'])
                                          ->from(['tbl_ideal_responsabilidad']) 
                                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                          ->scalar();

          if ($varMarcaMixtos < '80') {
            $varTColorMarcaIdealP = $varMalo;
          }else{
            if ($varMarcaMixtos >= '90') {
              $varTColorMarcaIdealP = $varBueno;
            }else{
              $varTColorMarcaIdealP = $varEstable;
            }
          }

          $varCanalMixtos = (new \yii\db\Query())
                                          ->select(['ROUND(AVG(tbl_ideal_responsabilidad.canal),1) AS varMarca'])
                                          ->from(['tbl_ideal_responsabilidad']) 
                                          ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpCliente])
                                          ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                          ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                          ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                          ->scalar();

          if ($varCanalMixtos < '80') {
            $varTColorCanalIdealP = $varMalo;
          }else{
            if ($varCanalMixtos >= '90') {
              $varTColorCanalIdealP = $varBueno;
            }else{
              $varTColorCanalIdealP = $varEstable;
            }
          }

          $varArrayRtaAutoMixta = round( ($varAgenteMixtos+$varCanalMixtos)/2,2);

          if ($varArrayRtaAutoMixta < '80') {
            $varTColorAutoIdealP = $varMalo;
          }else{
            if ($varArrayRtaAutoMixta >= '90') {
              $varTColorAutoIdealP = $varBueno;
            }else{
              $varTColorAutoIdealP = $varEstable;
            }
          }

          $varBolsitaCliente = (new \yii\db\Query())
                                ->select(['tbl_speech_categorias.programacategoria'])
                                ->from(['tbl_speech_categorias'])       

                                ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                  'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')

                                ->where(['=','tbl_speech_parametrizar.id_dp_clientes',$varIdDpCliente])
                                ->andwhere(['is','tbl_speech_parametrizar.tipoparametro',null])
                                ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                ->groupby(['tbl_speech_categorias.programacategoria'])
                                ->Scalar();

          $varIdLlamadaGeneral = (new \yii\db\Query())
                                ->select(['tbl_speech_servicios.idllamada'])
                                ->from(['tbl_speech_servicios'])       
                                ->where(['=','tbl_speech_servicios.id_dp_clientes',$varIdDpCliente])
                                ->andwhere(['!=','tbl_speech_servicios.arbol_id',1])
                                ->andwhere(['=','tbl_speech_servicios.anulado',0])
                                ->groupby(['tbl_speech_servicios.id_dp_clientes'])
                                ->Scalar();

          $varArrayRtaScoreMixta = (new \yii\db\Query())
                                          ->select(['ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore'])
                                          ->from(['tbl_ejecucionformularios']) 

                                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varBolsitaCliente])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtas])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varIdLlamadaGeneral])
                                          ->scalar();

          if ($varArrayRtaScoreMixta == "") {
            $varArrayRtaScoreMixta = 0;
          }

          if ($varArrayRtaScoreMixta < '80') {
            $varTColorMixtaAIdealP = $varMalo;
          }else{
            if ($varArrayRtaScoreMixta >= '90') {
              $varTColorMixtaAIdealP = $varBueno;
            }else{
              $varTColorMixtaAIdealP = $varEstable;
            }
          }

          $varArrayKonectaMixta = round( ($varArrayRtaScoreMixta+$varArrayRtaAutoMixta)/2,2);

          if ($varArrayKonectaMixta < '80') {
            $varTColorGeneralIdealP = $varMalo;
          }else{
            if ($varArrayKonectaMixta >= '90') {
              $varTColorGeneralIdealP = $varBueno;
            }else{
              $varTColorGeneralIdealP = $varEstable;
            }
          }

          $varArrayCantidadSpeechMixta = (new \yii\db\Query())
                                          ->select(['tbl_dashboardspeechcalls.callid'])
                                          ->from(['tbl_dashboardspeechcalls']) 
                                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varBolsitaCliente])
                                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtas])
                                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varIdLlamadaGeneral])
                                          ->count();

          $varListaDataArbolsMixtos = (new \yii\db\Query())
                                                ->select(['*'])
                                                ->from(['tbl_speech_pcrcformularios'])            
                                                ->where(['=','anulado',0])
                                                ->andwhere(['=','id_dp_clientes',$varIdDpCliente])
                                                ->all();

          $varArrayArbolsMixtos = array();
          foreach ($varListaDataArbolsMixtos as $key => $value) {
            array_push($varArrayArbolsMixtos, $value['arbol_id']);
          }
          $varArray_ArbolMixtos = implode(", ", $varArrayArbolsMixtos);
          $arrayArboles_downM = str_replace(array("#", "'", ";", " "), '', $varArray_ArbolMixtos);
          $varArbolesMixtas = explode(",", $arrayArboles_downM);

          $varListaDataCalibracionesMixto = (new \yii\db\Query())
                                                ->select(['*'])
                                                ->from(['tbl_arbols'])            
                                                ->where(['=','tbl_arbols.activo',0])
                                                ->andwhere(['in','tbl_arbols.id',$varArray_ArbolMixtos])
                                                ->all();

          $varArrayArbolsCalibracionesMixto = array();
          foreach ($varListaDataCalibracionesMixto as $key => $value) {
            array_push($varArrayArbolsCalibracionesMixto, $value['arbol_id']);
          }
          $varArray_ArbolCaliMixto = implode(", ", $varArrayArbolsCalibracionesMixto);

          $varTotalFeedbacksP = (new \yii\db\Query())
                                                ->select(['tbl_ejecucionfeedbacks.snaviso_revisado'])
                                                ->from(['tbl_ejecucionfeedbacks'])  

                                                ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                    'tbl_ejecucionfeedbacks.ejecucionformulario_id = tbl_ejecucionformularios.id') 

                                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                    'tbl_ejecucionformularios.arbol_id = tbl_arbols.id') 

                                                ->where(['in','tbl_arbols.id',$varArray_ArbolMixtos])
                                                ->andwhere(['=','tbl_arbols.activo',0])
                                                ->andwhere(['between','tbl_ejecucionfeedbacks.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                                ->andwhere(['in','tbl_ejecucionfeedbacks.snaviso_revisado',[0,1]])
                                                ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                                ->count();

          if ($varTotalFeedbacksP < '80') {
            $varTColorFeebackMixta = $varMalo;
          }else{
            if ($varTotalFeedbacksP >= '90') {
              $varTColorFeebackMixta = $varBueno;
            }else{
              $varTColorFeebackMixta = $varEstable;
            }
          }

          $varConteoAlertasMixtas = (new \yii\db\Query())
                                      ->select(['tbl_alertascx.id'])
                                      ->from(['tbl_alertascx'])  
                                      
                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_alertascx.pcrc = tbl_arbols.id')
                                      
                                      ->where(['in','tbl_arbols.id',$varArray_ArbolMixtos])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_alertascx.fecha',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->count();

          if ($varConteoAlertasMixtas < '80') {
            $varTColorAlertasMixta = $varMalo;
          }else{
            if ($varConteoAlertasMixtas >= '90') {
              $varTColorAlertasMixta = $varBueno;
            }else{
              $varTColorAlertasMixta = $varEstable;
            }
          }

          $varConteoCalibracionesMixtas = (new \yii\db\Query())
                                      ->select(['tbl_ejecucionformularios.id'])
                                      ->from(['tbl_ejecucionformularios'])

                                      ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                          'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')

                                      ->where(['in','tbl_arbols.arbol_id',$varArray_ArbolCaliMixto])
                                      ->andwhere(['like','tbl_arbols.name','alibra'])
                                      ->andwhere(['=','tbl_arbols.activo',0])
                                      ->andwhere(['between','tbl_ejecucionformularios.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                      ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                      ->count();

          if ($varConteoCalibracionesMixtas < '80') {
            $varTColorCaliMixta = $varMalo;
          }else{
            if ($varConteoCalibracionesMixtas >= '90') {
              $varTColorCaliMixta = $varBueno;
            }else{
              $varTColorCaliMixta = $varEstable;
            }
          }

      ?>

        <div class="card1 mb" style="font-size: 15px;">

          
          <div class="row">
            <div class="col-md-12">
              <div class="card1 mb" style="background: #6b97b1; text-align: center;" >
                <label style="font-size: 18px; color: #FFFFFF;"> <?= Yii::t('app', $varNombreClienteMixtos) ?></label>
              </div>
            </div>
          </div>

          <br>

          

          <div class="row">
            
            <div class="col-md-4">
              <div class="card4 mb">
                <table id="myTableInformacion" class="table table-hover table-bordered" style="margin-top:10px" >
                  <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Resultados Procesamientos') ?></label></caption>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad - General Konecta') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad & Consistencia') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorGeneralIdealP ?>"><?php echo  $varArrayKonectaMixta.' %'; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorMixtaAIdealP ?>"><?php echo  $varArrayRtaScoreMixta.' %'; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAutoIdealP ?>"><?php echo  $varArrayRtaAutoMixta.' %'; ?></label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card4 mb">
                <table id="myTableAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                  <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Detalle Procesamiento Automático') ?></label></caption>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Interacciones') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Marca') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varArrayCantidadSpeechMixta; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAgenteIdealP; ?>"><?php echo  $varAgenteMixtos.' %'; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorMarcaIdealP; ?>"><?php echo  $varMarcaMixtos.' %'; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorCanalIdealP; ?>"><?php echo  $varCanalMixtos.' %'; ?></label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card4 mb">
                <table id="myTableGestion" class="table table-hover table-bordered" style="margin-top:10px" >
                  <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Detalle Gestión de la Mejora') ?></label></caption>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Feedbacks') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alertas') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calibraciones') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorFeebackMixta; ?>"><?php echo  $varTotalFeedbacksP; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAlertasMixta; ?>"><?php echo  $varConteoAlertasMixtas; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorCaliMixta; ?>"><?php echo  $varConteoCalibracionesMixtas; ?></label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <br>

          <div class="row">
            
            <div class="col-md-12">
              <div class="card1 mb">
                <table id="myTableCalidad" class="table table-hover table-bordered" style="margin-top:10px" >
                  <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Detalle Calidad y Consistencia - Manual') ?></label></caption>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Score General') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PEC') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PENC') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'SPC/SFR') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Proceso') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Experiencia') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Promesa Marca') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      $varListRtaFormulariosMixtos = (new \yii\db\Query())
                          ->select([
                            'COUNT(tbl_ejecucionformularios.id) AS varConteo',
                            'ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore',
                            'ROUND(AVG(tbl_ejecucionformularios.i1_nmcalculo)*100,2) AS varPec',
                            'ROUND(AVG(tbl_ejecucionformularios.i2_nmcalculo)*100,2) AS varPenc',
                            'ROUND(AVG(tbl_ejecucionformularios.i3_nmcalculo)*100,2) AS varSfc',
                            'ROUND(AVG(tbl_ejecucionformularios.i5_nmcalculo)*100,2) AS varIndiceProceso',
                            'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo)*100,2) AS varIndiceExperienca',
                            'ROUND(AVG(tbl_ejecucionformularios.i7_nmcalculo)*100,2) AS varIndicePromesa'
                            ])
                          ->from(['tbl_ejecucionformularios']) 

                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varBolsitaCliente])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtas])
                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varIdLlamadaGeneral])
                          ->all();

                      foreach ($varListRtaFormulariosMixtos as $key => $value) {
                                      
                        if ($value['varConteo'] != "") {
                          $varConteoFormMixto = $value['varConteo'];
                        }else{
                          $varConteoFormMixto = $varSinData;
                        }

                        if ($value['varScore'] != "") {
                          $varScoreFormMixto = $value['varScore'].' %';
                        }else{
                          $varScoreFormMixto = $varSinData;
                        }

                        if ($value['varPec'] != "") {
                          $varPecFormMixto = $value['varPec'].' %';
                        }else{
                          $varPecFormMixto = $varSinData;
                        }

                        if ($value['varPenc'] != "") {
                          $varPencFormMixto = $value['varPec'].' %';
                        }else{
                          $varPencFormMixto = $varSinData;
                        }

                        if ($value['varSfc'] != "") {
                          $varSfcFormMixto = $value['varSfc'].' %';
                        }else{
                          $varSfcFormMixto = $varSinData;
                        }

                        if ($value['varIndiceProceso'] != "") {
                          $varProcesoFormMixto = $value['varIndiceProceso'].' %';
                        }else{
                          $varProcesoFormMixto = $varSinData;
                        }

                        if ($value['varIndiceExperienca'] != "") {
                          $varExperienciaFormMixta = $value['varIndiceExperienca'].' %';
                        }else{
                          $varExperienciaFormMixta = $varSinData;
                        }

                        if ($value['varIndicePromesa'] != "") {
                          $varPromesa = $value['varIndicePromesa'].' %';
                        }else{
                          $varPromesa = $varSinData;
                        }
                    ?>
                      <tr>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varConteoFormMixto; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varScoreFormMixto; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPecFormMixto; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPencFormMixto; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varSfcFormMixto; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varProcesoFormMixto; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varExperienciaFormMixta; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPromesa; ?></label></td>
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

        <hr>

      <?php

        }

      ?>        
    
    </div>
  </div>

</div>


<!-- Capa Botones -->
<div id="CapaIdBtn" class="capaBtn" style="display: inline;">

  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', ' Búsqueda Por Persona') ?></label>
        <?= Html::button('Buscar', ['value' => url::to(['buscarporpersona']), 'class' => 'btn btn-success', 'id'=>'modalButton1',
          'data-toggle' => 'tooltip',
          'style' => 'background-color: #559FFF', 
          'title' => 'Buscar Por Persona']) ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Filtros - Búsqueda Por Persona CXM</h4>',
              'id' => 'modal1',
              'size' => 'modal-lg',
            ]);

            echo "<div id='modalContent1'></div>";
                                                              
            Modal::end(); 
        ?>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', ' Descargar Información') ?></label>
        <a id="dlink" style="display:none;"></a>
        <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Descargar') ?></button>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', ' Cancelar y Regresar') ?></label>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
        ?>
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Informativa Tabla -->
<div id="idCapaInfotabla" class="capaInformaciontabla" style="display: none;">

  <div class="row">
    <div class="col-md-12">
      <table id="myTableInforPorPersona" class="table table-hover table-bordered" style="margin-top:10px" >
        <caption><label style="font-size: 15px;"> <?= Yii::t('app', 'KONECTA - CX MANAGEMENT') ?></label></caption>
        <thead>
          <tr>
            <th scope="col" class="text-center" colspan="10" style="background-color: #7e99c3;"><label style="font-size: 13px; color: #FFFFFF;"><?= Yii::t('app', 'Resultados Informe Q&S - Por Persona') ?></label></th>
          </tr>
          <tr>
            <th scope="col" class="text-center" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Ficha Técnica') ?></label></th>
          </tr>
          <tr>
            <th scope="col" class="text-center" colspan="2" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Lista de Servicios') ?></label></th>
            <th scope="col" class="text-center" colspan="2" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Lista Del Personal') ?></label></th>
            <th scope="col" class="text-center" colspan="2" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dimensión') ?></label></th>
            <th scope="col" class="text-center" colspan="2" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rango de Fechas') ?></label></th>
          </tr>
        </thead>
        <tbody>

          <?php

            $varArrayListaServiciosTabla = array();
            foreach ($varListaServicios as $key => $value) {
              array_push($varArrayListaServiciosTabla, $value['cliente']);
            }
            $ArrayListaServiciosTabla = implode(", ", $varArrayListaServiciosTabla);

            $varArrayListaNombreGerentesTabla = array();
            foreach ($varListaNombresGerentes as $key => $value) {
              array_push($varArrayListaNombreGerentesTabla, $value['gerente_cuenta']);
            }
            $ArrayListaNombreGerentesTabla = implode(", ", $varArrayListaNombreGerentesTabla);


          ?>

          <tr>
            <td class="text-center" colspan="2"><label style="font-size: 12px;"><?php echo  $ArrayListaServiciosTabla; ?></label></td>
            <td class="text-center" colspan="2"><label style="font-size: 12px;"><?php echo  $ArrayListaNombreGerentesTabla; ?></label></td>
            <td class="text-center" colspan="2"><label style="font-size: 12px;"><?php echo  $varTextoDimensionp; ?></label></td>
            <td class="text-center" colspan="2"><label style="font-size: 12px;"><?php echo  $varFechainicial.' - '.$varFechaFinal; ?></label></td>
          </tr>    
          <tr>
            <th scope="col" class="text-center" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Resultados Por Procesamiento') ?></label></th>
          </tr> 
          <tr>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad: General Konecta') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad y Consistencia') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '--') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '--') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Marca') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
          </tr>    
          <tr>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayKonectaGeneral.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayRtaScoreGeneral.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayPromedioAutoGeneral.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  '--'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  '--'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayAgenteP.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayMarcaP.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayCanalP.' %'; ?></label></td>
          </tr>  
          <tr>
            <th scope="col" class="text-center" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Resultado del Proceso - Lista de Servicios') ?></label></th>     
          </tr> 
          <?php

            $varConteoProcesoMixtoTabla = 0;
            foreach ($varListaServicios as $key => $value) {
              $varIdDpClienteTabla = $value['id_dp_clientes'];

              $varConteoProcesoMixtoTabla += 1;

              if ($varIdExtensionc > '1') {
                $varRnIdealMTabla =  (new \yii\db\Query())
                                    ->select(['rn'])
                                    ->from(['tbl_speech_parametrizar'])            
                                    ->where(['=','id_dp_clientes',$varIdDpClienteTabla])
                                    ->andwhere(['=','anulado',0])
                                    ->andwhere(['=','usabilidad',1])
                                    ->andwhere(['!=','rn',''])
                                    ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                    ->groupby(['rn'])
                                    ->all();
              }else{
                $varRnIdealMTabla =  (new \yii\db\Query())
                                    ->select(['rn'])
                                    ->from(['tbl_speech_parametrizar'])            
                                    ->where(['=','id_dp_clientes',$varIdDpClienteTabla])
                                    ->andwhere(['=','anulado',0])
                                    ->andwhere(['=','usabilidad',1])
                                    ->andwhere(['!=','rn',''])
                                    ->andwhere(['is','tipoparametro',null])
                                    ->groupby(['rn'])
                                    ->all();
              }

              if (count($varRnIdealMTabla) != 0) {
                $varArrayRnMTabla = array();
                foreach ($varRnIdealMTabla as $key => $value) {
                  array_push($varArrayRnMTabla, $value['rn']);
                }

                $varExtensionesArraysMTabla = implode("', '", $varArrayRnMTabla);
                $arrayExtensiones_downMTabla = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysMTabla);
                $varExtensionesMixtasTabla = explode(",", $arrayExtensiones_downMTabla);
              }else{

                if ($varIdExtensionc > '1') {
                  $varExtMTabla =  (new \yii\db\Query())
                                    ->select(['ext'])
                                    ->from(['tbl_speech_parametrizar'])            
                                    ->where(['=','id_dp_clientes',$varIdDpClienteTabla])
                                    ->andwhere(['=','anulado',0])
                                    ->andwhere(['=','usabilidad',1])
                                    ->andwhere(['!=','ext',''])
                                    ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                    ->groupby(['ext'])
                                    ->all();
                }else{
                  $varExtMTabla =  (new \yii\db\Query())
                                    ->select(['ext'])
                                    ->from(['tbl_speech_parametrizar'])            
                                    ->where(['=','id_dp_clientes',$varIdDpClienteTabla])
                                    ->andwhere(['=','anulado',0])
                                    ->andwhere(['=','usabilidad',1])
                                    ->andwhere(['!=','ext',''])
                                    ->andwhere(['is','tipoparametro',null])
                                    ->groupby(['ext'])
                                    ->all();
                }

                if (count($varExtMTabla) != 0) {
                  $varArrayExtMTabla = array();
                  foreach ($varExtMTabla as $key => $value) {
                    array_push($varArrayExtMTabla, $value['ext']);
                  }

                  $varExtensionesArraysMTabla = implode("', '", $varArrayExtMTabla);
                  $arrayExtensiones_downMTabla = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysMTabla);
                  $varExtensionesMixtasTabla = explode(",", $arrayExtensiones_downMTabla);
                }else{

                  if ($varIdExtensionc > '1') {
                    $varUsuaMTabla =  (new \yii\db\Query())
                                    ->select(['usuared'])
                                    ->from(['tbl_speech_parametrizar'])            
                                    ->where(['=','id_dp_clientes',$varIdDpClienteTabla])
                                    ->andwhere(['=','anulado',0])
                                    ->andwhere(['=','usabilidad',1])
                                    ->andwhere(['!=','usuared',''])
                                    ->andwhere(['=','tipoparametro',$varIdExtensionc])
                                    ->groupby(['usuared'])
                                    ->all();
                  }else{
                    $varUsuaMTabla =  (new \yii\db\Query())
                                    ->select(['usuared'])
                                    ->from(['tbl_speech_parametrizar'])            
                                    ->where(['=','id_dp_clientes',$varIdDpClienteTabla])
                                    ->andwhere(['=','anulado',0])
                                    ->andwhere(['=','usabilidad',1])
                                    ->andwhere(['!=','usuared',''])
                                    ->andwhere(['is','tipoparametro',null])
                                    ->groupby(['usuared'])
                                    ->all();
                  }


                  if (count($varUsuaMTabla) != 0) {
                    $varArrayUsuaMTabla = array();
                    foreach ($varUsuaMTabla as $key => $value) {
                      array_push($varArrayUsuaMTabla, $value['usuared']);
                    }

                    $varExtensionesArraysMTabla = implode("', '", $varArrayUsuaMTabla);
                    $arrayExtensiones_downMTabla = str_replace(array("#", "'", ";", " "), '', $varExtensionesArraysMTabla);
                    $varExtensionesMixtasTabla = explode(",", $arrayExtensiones_downMTabla);
                  }else{
                    $varExtensionesMixtasTabla = "N0A";
                  }
                }
              }

              $varNombreClienteMixtosTabla = (new \yii\db\Query())
                                              ->select(['cliente'])
                                              ->from(['tbl_proceso_cliente_centrocosto'])            
                                              ->where(['=','anulado',0])
                                              ->andwhere(['=','id_dp_clientes',$varIdDpClienteTabla])
                                              ->groupby(['id_dp_clientes'])
                                              ->Scalar();

              $varAgenteMixtosTabla = (new \yii\db\Query())
                                              ->select(['ROUND(AVG(tbl_ideal_responsabilidad.agente),1) AS varMarca'])
                                              ->from(['tbl_ideal_responsabilidad']) 
                                              ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpClienteTabla])
                                              ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                              ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                              ->scalar();     
              

              $varMarcaMixtosTabla = (new \yii\db\Query())
                                              ->select(['ROUND(AVG(tbl_ideal_responsabilidad.marca),1) AS varMarca'])
                                              ->from(['tbl_ideal_responsabilidad']) 
                                              ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpClienteTabla])
                                              ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                              ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                              ->scalar();

              
              $varCanalMixtosTabla = (new \yii\db\Query())
                                              ->select(['ROUND(AVG(tbl_ideal_responsabilidad.canal),1) AS varMarca'])
                                              ->from(['tbl_ideal_responsabilidad']) 
                                              ->where(['=','tbl_ideal_responsabilidad.id_dp_cliente',$varIdDpClienteTabla])
                                              ->andwhere(['>=','tbl_ideal_responsabilidad.fechainicio',$varFechainicial.' 05:00:00'])
                                              ->andwhere(['<=','tbl_ideal_responsabilidad.fechafin',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['=','tbl_ideal_responsabilidad.extension',$varIdExtensionc])
                                              ->andwhere(['=','tbl_ideal_responsabilidad.anulado',0])
                                              ->scalar();

              

              $varArrayRtaAutoMixtaTabla = round( ($varAgenteMixtosTabla+$varCanalMixtosTabla)/2,2);


              $varBolsitaClienteTabla = (new \yii\db\Query())
                                    ->select(['tbl_speech_categorias.programacategoria'])
                                    ->from(['tbl_speech_categorias'])       

                                    ->join('LEFT OUTER JOIN', 'tbl_speech_parametrizar',
                                      'tbl_speech_categorias.cod_pcrc = tbl_speech_parametrizar.cod_pcrc')

                                    ->where(['=','tbl_speech_parametrizar.id_dp_clientes',$varIdDpClienteTabla])
                                    ->andwhere(['is','tbl_speech_parametrizar.tipoparametro',null])
                                    ->andwhere(['=','tbl_speech_parametrizar.anulado',0])
                                    ->groupby(['tbl_speech_categorias.programacategoria'])
                                    ->Scalar();

              $varIdLlamadaGeneralTabla = (new \yii\db\Query())
                                    ->select(['tbl_speech_servicios.idllamada'])
                                    ->from(['tbl_speech_servicios'])       
                                    ->where(['=','tbl_speech_servicios.id_dp_clientes',$varIdDpClienteTabla])
                                    ->andwhere(['!=','tbl_speech_servicios.arbol_id',1])
                                    ->andwhere(['=','tbl_speech_servicios.anulado',0])
                                    ->groupby(['tbl_speech_servicios.id_dp_clientes'])
                                    ->Scalar();

              $varArrayRtaScoreMixtaTabla = (new \yii\db\Query())
                                              ->select(['ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore'])
                                              ->from(['tbl_ejecucionformularios']) 

                                              ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                                      'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                                              ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                                      'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                                              ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                              ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varBolsitaClienteTabla])
                                              ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtasTabla])
                                              ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varIdLlamadaGeneralTabla])
                                              ->scalar();

              if ($varArrayRtaScoreMixtaTabla == "") {
                $varArrayRtaScoreMixtaTabla = 0;
              }

              $varArrayKonectaMixtaTabla = round( ($varArrayRtaScoreMixtaTabla+$varArrayRtaAutoMixtaTabla)/2,2);

              $varArrayCantidadSpeechMixtaTabla = (new \yii\db\Query())
                                              ->select(['tbl_dashboardspeechcalls.callid'])
                                              ->from(['tbl_dashboardspeechcalls']) 
                                              ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                                              ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varBolsitaClienteTabla])
                                              ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                                              ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtasTabla])
                                              ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varIdLlamadaGeneralTabla])
                                              ->count();

              $varListaDataArbolsMixtosTabla = (new \yii\db\Query())
                                                    ->select(['*'])
                                                    ->from(['tbl_speech_pcrcformularios'])            
                                                    ->where(['=','anulado',0])
                                                    ->andwhere(['=','id_dp_clientes',$varIdDpClienteTabla])
                                                    ->all();

              $varArrayArbolsMixtosTabla = array();
              foreach ($varListaDataArbolsMixtosTabla as $key => $value) {
                array_push($varArrayArbolsMixtosTabla, $value['arbol_id']);
              }
              $varArray_ArbolMixtosTabla = implode(", ", $varArrayArbolsMixtosTabla);
              $arrayArboles_downMTabla = str_replace(array("#", "'", ";", " "), '', $varArray_ArbolMixtosTabla);
              $varArbolesMixtasTabla = explode(",", $arrayArboles_downMTabla);

              $varListaDataCalibracionesMixtoTabla = (new \yii\db\Query())
                                                    ->select(['*'])
                                                    ->from(['tbl_arbols'])            
                                                    ->where(['=','tbl_arbols.activo',0])
                                                    ->andwhere(['in','tbl_arbols.id',$varArray_ArbolMixtosTabla])
                                                    ->all();

              $varArrayArbolsCalibracionesMixtoTabla = array();
              foreach ($varListaDataCalibracionesMixtoTabla as $key => $value) {
                array_push($varArrayArbolsCalibracionesMixtoTabla, $value['arbol_id']);
              }
              $varArray_ArbolCaliMixtoTabla = implode(", ", $varArrayArbolsCalibracionesMixtoTabla);

              $varTotalFeedbacksPTabla = (new \yii\db\Query())
                                                    ->select(['tbl_ejecucionfeedbacks.snaviso_revisado'])
                                                    ->from(['tbl_ejecucionfeedbacks'])  

                                                    ->join('LEFT OUTER JOIN', 'tbl_ejecucionformularios',
                                                        'tbl_ejecucionfeedbacks.ejecucionformulario_id = tbl_ejecucionformularios.id') 

                                                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                                        'tbl_ejecucionformularios.arbol_id = tbl_arbols.id') 

                                                    ->where(['in','tbl_arbols.id',$varArray_ArbolMixtosTabla])
                                                    ->andwhere(['=','tbl_arbols.activo',0])
                                                    ->andwhere(['between','tbl_ejecucionfeedbacks.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                                    ->andwhere(['in','tbl_ejecucionfeedbacks.snaviso_revisado',[0,1]])
                                                    ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                                    ->count();


              $varConteoAlertasMixtasTabla = (new \yii\db\Query())
                                          ->select(['tbl_alertascx.id'])
                                          ->from(['tbl_alertascx'])  
                                          
                                          ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                              'tbl_alertascx.pcrc = tbl_arbols.id')
                                          
                                          ->where(['in','tbl_arbols.id',$varArray_ArbolMixtosTabla])
                                          ->andwhere(['=','tbl_arbols.activo',0])
                                          ->andwhere(['between','tbl_alertascx.fecha',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                          ->count();


              $varConteoCalibracionesMixtasTabla = (new \yii\db\Query())
                                          ->select(['tbl_ejecucionformularios.id'])
                                          ->from(['tbl_ejecucionformularios'])

                                          ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                              'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')

                                          ->where(['in','tbl_arbols.arbol_id',$varArray_ArbolCaliMixtoTabla])
                                          ->andwhere(['like','tbl_arbols.name','alibra'])
                                          ->andwhere(['=','tbl_arbols.activo',0])
                                          ->andwhere(['between','tbl_ejecucionformularios.created',$varFechainicial.' 00:00:00',$varFechaFinal.' 00:00:00'])
                                          ->andwhere(['in','tbl_ejecucionformularios.dimension_id',$varDimensionesId])
                                          ->count();



          ?>

          <tr>
            <th scope="col" class="text-center" colspan="10" style="background-color: #7e99c3;"><label style="font-size: 13px;"><?= Yii::t('app', $varNombreClienteMixtosTabla) ?></label></th>     
          </tr>
          <tr>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad - General Konecta') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad & Consistencia') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Interacciones') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Marca') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Feedbacks') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alertas') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calibraciones') ?></label></th>
          </tr>
          <tr>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayKonectaMixtaTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayRtaScoreMixtaTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayRtaAutoMixtaTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varArrayCantidadSpeechMixtaTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varAgenteMixtosTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varMarcaMixtosTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varCanalMixtosTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varTotalFeedbacksPTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varConteoAlertasMixtasTabla.' %'; ?></label></td>
            <td class="text-center" ><label style="font-size: 12px;"><?php echo  $varConteoCalibracionesMixtasTabla.' %'; ?></label></td>
          </tr>
          <tr>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Score General') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PEC') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PENC') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'SPC/SFR') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Proceso') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Experiencia') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Promesa Marca') ?></label></th>
          </tr>
         <?php
                      $varListRtaFormulariosMixtosTabla = (new \yii\db\Query())
                          ->select([
                            'COUNT(tbl_ejecucionformularios.id) AS varConteo',
                            'ROUND(AVG(tbl_ejecucionformularios.score)*100,2) AS varScore',
                            'ROUND(AVG(tbl_ejecucionformularios.i1_nmcalculo)*100,2) AS varPec',
                            'ROUND(AVG(tbl_ejecucionformularios.i2_nmcalculo)*100,2) AS varPenc',
                            'ROUND(AVG(tbl_ejecucionformularios.i3_nmcalculo)*100,2) AS varSfc',
                            'ROUND(AVG(tbl_ejecucionformularios.i5_nmcalculo)*100,2) AS varIndiceProceso',
                            'ROUND(AVG(tbl_ejecucionformularios.i6_nmcalculo)*100,2) AS varIndiceExperienca',
                            'ROUND(AVG(tbl_ejecucionformularios.i7_nmcalculo)*100,2) AS varIndicePromesa'
                            ])
                          ->from(['tbl_ejecucionformularios']) 

                          ->join('LEFT OUTER JOIN', 'tbl_speech_mixta',
                                  'tbl_ejecucionformularios.id = tbl_speech_mixta.formulario_id')

                          ->join('LEFT OUTER JOIN', 'tbl_dashboardspeechcalls',
                                  'tbl_speech_mixta.callid = tbl_dashboardspeechcalls.callId')

                          ->where(['=','tbl_dashboardspeechcalls.anulado',0])
                          ->andwhere(['=','tbl_dashboardspeechcalls.servicio',$varBolsitaClienteTabla])
                          ->andwhere(['between','tbl_dashboardspeechcalls.fechallamada',$varFechainicial.' 05:00:00',$varFechaFinal.' 05:00:00'])
                          ->andwhere(['in','tbl_dashboardspeechcalls.extension',$varExtensionesMixtasTabla])
                          ->andwhere(['=','tbl_dashboardspeechcalls.idcategoria',$varIdLlamadaGeneralTabla])
                          ->all();

                      foreach ($varListRtaFormulariosMixtosTabla as $key => $value) {
                                      
                        if ($value['varConteo'] != "") {
                          $varConteoFormMixtoTabla = $value['varConteo'];
                        }else{
                          $varConteoFormMixtoTabla = $varSinData;
                        }

                        if ($value['varScore'] != "") {
                          $varScoreFormMixtoTabla = $value['varScore'].' %';
                        }else{
                          $varScoreFormMixtoTabla = $varSinData;
                        }

                        if ($value['varPec'] != "") {
                          $varPecFormMixtoTabla = $value['varPec'].' %';
                        }else{
                          $varPecFormMixtoTabla = $varSinData;
                        }

                        if ($value['varPenc'] != "") {
                          $varPencFormMixtoTabla = $value['varPec'].' %';
                        }else{
                          $varPencFormMixtoTabla = $varSinData;
                        }

                        if ($value['varSfc'] != "") {
                          $varSfcFormMixtoTabla = $value['varSfc'].' %';
                        }else{
                          $varSfcFormMixtoTabla = $varSinData;
                        }

                        if ($value['varIndiceProceso'] != "") {
                          $varProcesoFormMixtoTabla = $value['varIndiceProceso'].' %';
                        }else{
                          $varProcesoFormMixtoTabla = $varSinData;
                        }

                        if ($value['varIndiceExperienca'] != "") {
                          $varExperienciaFormMixtaTabla = $value['varIndiceExperienca'].' %';
                        }else{
                          $varExperienciaFormMixtaTabla = $varSinData;
                        }

                        if ($value['varIndicePromesa'] != "") {
                          $varPromesaTabla = $value['varIndicePromesa'].' %';
                        }else{
                          $varPromesaTabla = $varSinData;
                        }
                    ?>
                      
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varConteoFormMixtoTabla; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varScoreFormMixtoTabla; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPecFormMixtoTabla; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPencFormMixtoTabla; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varSfcFormMixtoTabla; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varProcesoFormMixtoTabla; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varExperienciaFormMixtaTabla; ?></label></td>
                        <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPromesaTabla; ?></label></td>
                      
                    <?php
                      }
                    ?>

          <?php
            }
          ?>
          
        </tbody>
      </table>
    </div>
  </div>

</div>

<script type="text/javascript">
  function opennovedads(){
    var varidtbn1 = document.getElementById("idtbn1");
    var varidtbn2 = document.getElementById("idtbn2");
    var varidnovedad = document.getElementById("capaListaServicios");

    varidtbn1.style.display = 'none';
    varidtbn2.style.display = 'inline';
    varidnovedad.style.display = 'inline';
  };

  function closenovedads(){
    var varidtbn1 = document.getElementById("idtbn1");
    var varidtbn2 = document.getElementById("idtbn2");
    var varidnovedad = document.getElementById("capaListaServicios");

    varidtbn1.style.display = 'inline';
    varidtbn2.style.display = 'none';
    varidnovedad.style.display = 'none';
  };

  function opennovedadp(){
    var varidtbn3 = document.getElementById("idtbn3");
    var varidtbn4 = document.getElementById("idtbn4");
    var varidnovedadp = document.getElementById("capaListaPersonal");

    varidtbn3.style.display = 'none';
    varidtbn4.style.display = 'inline';
    varidnovedadp.style.display = 'inline';
  };

  function closenovedadp(){
    var varidtbn3 = document.getElementById("idtbn3");
    var varidtbn4 = document.getElementById("idtbn4");
    var varidnovedadp = document.getElementById("capaListaPersonal");

    varidtbn3.style.display = 'inline';
    varidtbn4.style.display = 'none';
    varidnovedadp.style.display = 'none';
  };

  var chartContainerGP = document.getElementById("chartContainerGP");
  var chartContainerMP = document.getElementById("chartContainerMP");
  var chartContainerAP = document.getElementById("chartContainerAP");

  var oilDataGP = {
    datasets: [
      {
        data: ["<?php echo $varArrayKonectaGeneral; ?>","<?php echo $varArrayRestanteKGeneral; ?>"],
        backgroundColor: [
          "<?php echo $varTColorGeneralP; ?>",
                    "#D7CFC7"
        ]
      }]
  };

  var pieChart = new Chart(chartContainerGP, {
    type: 'doughnut',
    data: oilDataGP
  });

  var oilDataMP = {
    datasets: [
      {
        data: ["<?php echo $varArrayRtaScoreGeneral; ?>","<?php echo $varArrayRestanteScoreGeneral; ?>"],
        backgroundColor: [
          "<?php echo $varTColorMixtoP; ?>",
          "#D7CFC7"
        ]
    }]
  };

  var pieChart = new Chart(chartContainerMP, {
    type: 'doughnut',
    data: oilDataMP
  });


  var oilDataAP = {
    datasets: [
      {
        data: ["<?php echo $varArrayPromedioAutoGeneral; ?>","<?php echo $varArrayRestanteAutoGeneral; ?>"],
        backgroundColor: [
          "<?php echo $varTColorAutoP; ?>",
          "#D7CFC7"
        ]
    }]
  };

  var pieChart = new Chart(chartContainerAP, {
    type: 'doughnut',
    data: oilDataAP
  });

  $(function() {

    Highcharts.setOptions({
      lang: {
        numericSymbols: null,
        thousandsSep: ','
      }
    });

    $('#chartContainerResposnabilidadesP').highcharts({

      chart: {
        borderColor: '#DAD9D9',
        borderRadius: 7,
        borderWidth: 1,
        type: 'column'
      }, 

      title: {
        text: '.',
        style: {
          color: '#3C74AA'
        }
      },

      xAxis: {
        categories: '_',
        title: {
          text: null
        },
        crosshair: true
      },


      series: [
        {
          name: 'Agente',
          data: [<?= $varArrayAgenteP ?>],
          color: '#FBCE52'
        },{
          name: 'Marca',
          data: [<?= $varArrayMarcaP ?>],
          color: '#4298B5'
        },{
          name: 'Canal',
          data: [<?= $varArrayCanalP ?>],
          color: '#C6C6C6'
        }
      ]

    });

  });

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
            document.getElementById("dlink").download = "Informe Q&S - Por Persona";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

    }
  })();
  function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('myTableInforPorPersona', 'Archivo Informe Q&S - Por Persona', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

  }
  var btn = document.getElementById("btn");
  btn.addEventListener("click",download);

</script>