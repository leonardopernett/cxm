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

  $varListarGeneralResponsabilidad = 0;
  $varRestanteGeneral = 0;
  $varTColorGeneral = 0;
  $varListasMixtas = 0;
  $varRestanteMixto = 0;
  $varTColorMixto = 0;
  $varPormedioResponsable = 0;
  $varRestanteAutos = 0;
  $varTColorAuto = 0;

$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js); 
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

  <?php if (count($varDocGerente) == 1) { ?>
  <div class="row">

    <div class="col-md-4">
      <div class="card2 mb">
        <label ><em class="fas fa-user" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Director:') ?></label>
        <label style="text-align: center;"><?= Yii::t('app', $varNombreDirector) ?></label>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card2 mb">
        <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Dimensión:') ?></label>
        <label style="text-align: center;"><?= Yii::t('app', $varTextoDimension) ?></label>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card2 mb">
        <label ><em class="fas fa-calendar-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Rango de Fecha:') ?></label>
        <label style="text-align: center;"><?= Yii::t('app', $varFechaInicioPersona.' - '.$varFechaFinPersona) ?></label>
      </div>
    </div>

  </div>

  <?php }else{ ?>

  <div class="row">
      
    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-user" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Director:') ?></label>
        <label style="text-align: center;"><?= Yii::t('app', $varNombreDirector) ?></label>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-users" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Gerentes:') ?></label>
          
        <div class="col-md-12 text-center">          
          <div onclick="opennovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbn1" >
            <label style="text-align: center;"><?= Yii::t('app', 'Abrir Listado Gerentes [ + ]') ?></label>
          </div> 
                            
          <div onclick="closenovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbn2" >
            <label style="text-align: center;"><?= Yii::t('app', 'Cerrar Listado Gerentes [ - ]') ?></label>
          </div> 
        </div>

      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Dimensión:') ?></label>
        <label style="text-align: center;"><?= Yii::t('app', $varTextoDimension) ?></label>
      </div>
    </div>

    <div class="col-md-3">
      <div class="card2 mb">
        <label ><em class="fas fa-calendar-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Rango de Fecha:') ?></label>
        <label style="text-align: center;"><?= Yii::t('app', $varFechaInicioPersona.' - '.$varFechaFinPersona) ?></label>
      </div>
    </div>

  </div>

  <div class="capaListaGerentes" id="capaListaGerentes" style="display: none;">
    <br>
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">
          <label ><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Lista Gerentes:') ?></label>          
          <label style="text-align: left;"><?= Yii::t('app', $varListasNombresGerentes) ?></label>          
        </div>
      </div>
    </div>
    <br>
  </div>

  <?php } ?>

</div>

<hr>

<?php
if (count($varListasClientesIdeal) != 0) {
?>

<!-- Capa de Resultados General -->
<div id="idCapaRtaGeneral" class="capaRtaGenerales" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados de Procesamientos') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-6">
      <div class="card3 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Detalle de Procesmiento Mixto') ?></label>
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
            <?php
              $varArraySumeAutomatico = array();
              $varArraySumaMixto = array();

              foreach ($varListasClientesIdeal as $key => $value) {
                $varNombreServiciosIdeal = $value['cliente'];
                $varIdClientesServicioIdeal =  $value['id_dp_clientes'];

                $varResponsableAutomaticoIdeal = (new \yii\db\Query())
                                            ->select(['round(AVG(agente),2) AS ProAgente','round(AVG(marca),2) AS ProMarca','round(AVG(canal),2) AS ProCanal'])
                                            ->from(['tbl_ideal_responsabilidad'])            
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','id_dp_cliente',$varIdClientesServicioIdeal])
                                            ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                            ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                            ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                            ->all(); 


                $varArrayAutomaticoIdeal = 0;
                foreach ($varResponsableAutomaticoIdeal as $key => $value) {
                  $varArrayAutomaticoIdeal = round(($value['ProAgente'] + $value['ProCanal']) / 2,2);
                }

                array_push($varArraySumeAutomatico, $varArrayAutomaticoIdeal);

                $vaListarFormulariosGIdeal = (new \yii\db\Query())
                                              ->select(['formulario_id'])
                                              ->from(['tbl_ideal_novedades'])            
                                              ->where(['=','anulado',0])
                                              ->andwhere(['=','id_dp_cliente',$varIdClientesServicioIdeal])
                                              ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                              ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                              ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                              ->groupby(['formulario_id'])
                                              ->All(); 

                $varArrayCallidProcesoIdeal =  array();
                foreach ($vaListarFormulariosGIdeal as $key => $value) {
                  $varFormularioGIdIdeal = $value['formulario_id'];

                  $varScoreGIdeal = (new \yii\db\Query())
                                    ->select(['score'])
                                    ->from(['tbl_ejecucionformularios'])         
                                    ->where(['=','id',$varFormularioGIdIdeal])
                                    ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                    ->scalar();

                  if ($varScoreGIdeal) {
                    array_push($varArrayCallidProcesoIdeal, $varScoreGIdeal);
                  }else{
                    array_push($varArrayCallidProcesoIdeal, 0);
                  }
                  

                }

                if (count($varArrayCallidProcesoIdeal) != 0) {
                  $varListarMixtasIdeal = round(array_sum($varArrayCallidProcesoIdeal) / count($varArrayCallidProcesoIdeal),2);
                }else{
                  $varListarMixtasIdeal = 0;
                }

                array_push($varArraySumaMixto, $varListarMixtasIdeal);
              
              }

              $varPormedioResponsable = round((array_sum($varArraySumeAutomatico) / count($varListasClientesIdeal)),2);


              $varListasMixtas = round((array_sum($varArraySumaMixto) / count($varListasClientesIdeal)),2);


              $varListarGeneralResponsabilidad = round(($varPormedioResponsable + $varListasMixtas) / 2,2);              

              $varRestanteGeneral = round(100 - $varListarGeneralResponsabilidad,2);
              if ($varListarGeneralResponsabilidad < '80') {
                $varTColorGeneral = $varMalo;
              }else{
                if ($varListarGeneralResponsabilidad >= '90') {
                  $varTColorGeneral = $varBueno;
                }else{
                  $varTColorGeneral = $varEstable;
                }
              }

              $varRestanteMixto = round(100 - $varListasMixtas,2);
              if ($varListasMixtas < '80') {
                $varTColorMixto = $varMalo;
              }else{
                if ($varListasMixtas >= '90') {
                  $varTColorMixto = $varBueno;
                }else{
                  $varTColorMixto = $varEstable;
                }
              }

              $varRestanteAutos = round(100 - $varPormedioResponsable,2);
              if ($varPormedioResponsable < '80') {
                $varTColorAuto = $varMalo;
              }else{
                if ($varListasMixtas >= '90') {
                  $varTColorAuto = $varBueno;
                }else{
                  $varTColorAuto = $varEstable;
                }
              }
              
            ?>
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerG"></canvas></div><span style="font-size: 15px;"><?php echo $varListarGeneralResponsabilidad.' %'; ?></span></td> 
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerM"></canvas></div><span style="font-size: 15px;"><?php echo $varListasMixtas.' %'; ?></span></td> 
            <td class="text-center" style="width: 100px;"><div style="width: 120px; height: 120px;  display:block; margin:auto;"><canvas id="chartContainerA"></canvas></div><span style="font-size: 15px;"><?php echo $varPormedioResponsable.' %'; ?></span></td> 
          </tr>
        </table>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card3 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 20px; color: #559FFF;"></em><?= Yii::t('app', ' Detalle Procesamiento Automatico') ?></label>
        <div id="chartContainerResposnabilidades" class="highcharts-container" style="height: 200px;"></div>
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Servicios -->
<div class="capaServicios" id="capaIdServicios" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados Por Servicios') ?></label>
      </div>
    </div>
  </div>

  <br>

  <?php
  foreach ($varListasClientesIdeal as $key => $value) {
    $varNombreServicios = $value['cliente'];
    $varIdClientesServicio =  $value['id_dp_clientes'];

    $varCantidadLlamadasIdeal = (new \yii\db\Query())
                                ->select(['sum(cantidad)'])
                                ->from(['tbl_ideal_llamadas'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_dp_cliente',$varIdClientesServicio])
                                ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                ->Scalar(); 

    $varResponsableAutomatico = (new \yii\db\Query())
                                ->select(['round(AVG(agente),2) AS ProAgente','round(AVG(marca),2) AS ProMarca','round(AVG(canal),2) AS ProCanal'])
                                ->from(['tbl_ideal_responsabilidad'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_dp_cliente',$varIdClientesServicio])
                                ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                ->all(); 

    $varArrayAutomatico = 0;
    $varAgentePersona = null;
    $varMarcaPersona = null;
    $varCanalPersona = null;
    foreach ($varResponsableAutomatico as $key => $value) {
      $varArrayAutomatico = round(($value['ProAgente'] + $value['ProCanal']) / 2,2);
      $varAgentePersona = $value['ProAgente'];      
      $varMarcaPersona = $value['ProMarca'];      
      $varCanalPersona = $value['ProCanal'];      
    }

    if ($varArrayAutomatico < '80') {
      $varTColorAutoIdeal = $varMalo;
    }else{
      if ($varArrayAutomatico >= '90') {
        $varTColorAutoIdeal = $varBueno;
      }else{
        $varTColorAutoIdeal = $varEstable;
      }
    }

    if ($varAgentePersona < '80') {
      $varTColorAgenteIdeal = $varMalo;
    }else{
      if ($varAgentePersona >= '90') {
        $varTColorAgenteIdeal = $varBueno;
      }else{
        $varTColorAgenteIdeal = $varEstable;
      }
    }
    if ($varMarcaPersona < '80') {
      $varTColorMarcaIdeal = $varMalo;
    }else{
      if ($varMarcaPersona >= '90') {
        $varTColorMarcaIdeal = $varBueno;
      }else{
        $varTColorMarcaIdeal = $varEstable;
      }
    }
    if ($varCanalPersona < '80') {
      $varTColorCanalIdeal = $varMalo;
    }else{
      if ($varCanalPersona >= '90') {
        $varTColorCanalIdeal = $varBueno;
      }else{
        $varTColorCanalIdeal = $varEstable;
      }
    }

    $vaListarFormulariosIdeal = (new \yii\db\Query())
                                ->select(['formulario_id'])
                                ->from(['tbl_ideal_novedades'])            
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_dp_cliente',$varIdClientesServicio])
                                ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                ->groupby(['formulario_id'])
                                ->All(); 

    $varArrayValoraciones = array();
    $varArrayCallidsIdeal = array();
    $varArrayFeedbacks = array();
    $varArrayPEC = array();
    $varArrayPENC = array();
    $varArraySFC = array();
    $varArrayProceso = array();
    $varArrayExperiencia = array();
    $varArrayPromesa = array();
    foreach ($vaListarFormulariosIdeal as $key => $value) {
      $varFormularioIdIdeal = $value['formulario_id'];
      array_push($varArrayValoraciones, 1);

      $varScoreIdeal = (new \yii\db\Query())
                      ->select(['score'])
                      ->from(['tbl_ejecucionformularios'])         
                      ->where(['=','id',$varFormularioIdIdeal])
                      ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                      ->scalar();
      array_push($varArrayCallidsIdeal, $varScoreIdeal);

      $varCantidadFeedbacksIdeal = (new \yii\db\Query())
                                    ->select(['id'])
                                    ->from(['tbl_ejecucionfeedbacks'])         
                                    ->where(['=','ejecucionformulario_id',$varFormularioIdIdeal])
                                    ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                    ->count();
      array_push($varArrayFeedbacks, $varCantidadFeedbacksIdeal);

      $varPecIdeal = (new \yii\db\Query())
                      ->select(['i1_nmcalculo'])
                      ->from(['tbl_ejecucionformularios'])         
                      ->where(['=','id',$varFormularioIdIdeal])
                      ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                      ->scalar();
      array_push($varArrayPEC, $varPecIdeal);

      $varPencIdeal = (new \yii\db\Query())
                      ->select(['i2_nmcalculo'])
                      ->from(['tbl_ejecucionformularios'])         
                      ->where(['=','id',$varFormularioIdIdeal])
                      ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                      ->scalar();
      array_push($varArrayPENC, $varPencIdeal);

      $varSfcIdeal = (new \yii\db\Query())
                      ->select(['i3_nmcalculo'])
                      ->from(['tbl_ejecucionformularios'])         
                      ->where(['=','id',$varFormularioIdIdeal])
                      ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                      ->scalar();
      array_push($varArraySFC, $varSfcIdeal);

      $varProcesoIdeal = (new \yii\db\Query())
                      ->select(['i5_nmcalculo'])
                      ->from(['tbl_ejecucionformularios'])         
                      ->where(['=','id',$varFormularioIdIdeal])
                      ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                      ->scalar();
      array_push($varArrayProceso, $varProcesoIdeal);

      $varExpIdeal = (new \yii\db\Query())
                      ->select(['i6_nmcalculo'])
                      ->from(['tbl_ejecucionformularios'])         
                      ->where(['=','id',$varFormularioIdIdeal])
                      ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                      ->scalar();
      array_push($varArrayExperiencia, $varExpIdeal);

      $varPromIdeal = (new \yii\db\Query())
                      ->select(['i7_nmcalculo'])
                      ->from(['tbl_ejecucionformularios'])         
                      ->where(['=','id',$varFormularioIdIdeal])
                      ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                      ->scalar();
      array_push($varArrayPromesa, $varPromIdeal);
    }


    $varTotalValoraciones = array_sum($varArrayValoraciones);
    $varTotalFeedbacks = array_sum($varArrayFeedbacks);

      if (array_sum($varArrayPEC) != 0 && $varTotalValoraciones != 0) {
        $varTotalPec = round((array_sum($varArrayPEC) / $varTotalValoraciones),2);
      }else{
        $varTotalPec = 0;
      }
      if (array_sum($varArrayPENC) != 0 && $varTotalValoraciones != 0) {
        $varTotalPenc = round((array_sum($varArrayPENC) / $varTotalValoraciones),2);
      }else{
        $varTotalPenc = 0;
      }
      if (array_sum($varArraySFC) != 0 && $varTotalValoraciones != 0) {
        $varTotalSfc = round((array_sum($varArraySFC) / $varTotalValoraciones),2);
      }else{
        $varTotalSfc = 0;
      }
      if (array_sum($varArrayProceso) != 0 && $varTotalValoraciones != 0) {
        $varTotalProceso = round((array_sum($varArrayProceso) / $varTotalValoraciones),2);
      }else{
        $varTotalProceso = 0;
      }
      if (array_sum($varArrayExperiencia) != 0 && $varTotalValoraciones != 0) {
        $varTotalExperiencia = round((array_sum($varArrayExperiencia) / $varTotalValoraciones),2);
      }else{
        $varTotalExperiencia = 0;
      }
      if (array_sum($varArrayPromesa) != 0 && $varTotalValoraciones != 0) {
        $varTotalPromesa = round((array_sum($varArrayPromesa) / $varTotalValoraciones),2);
      }else{
        $varTotalPromesa = 0;
      }

      if ($varTotalFeedbacks < '80') {
        $varTColorFeeback = $varMalo;
      }else{
        if ($varTotalFeedbacks >= '90') {
          $varTColorFeeback = $varBueno;
        }else{
          $varTColorFeeback = $varEstable;
        }
      }

      if (count($varArrayCallidsIdeal) != 0) {
        $varListarMixtasIdeal = round(array_sum($varArrayCallidsIdeal) / count($varArrayCallidsIdeal),2);
      }else{
        $varListarMixtasIdeal = 0;
      }

      if ($varListarMixtasIdeal < '80') {
        $varTColorMixtaAIdeal = $varMalo;
      }else{
        if ($varListarMixtasIdeal >= '90') {
          $varTColorMixtaAIdeal = $varBueno;
        }else{
          $varTColorMixtaAIdeal = $varEstable;
        }
      }

      $varGeneralConsistenciaIdeal = round((($varArrayAutomatico + $varListarMixtasIdeal) / 2),2);

      if ($varGeneralConsistenciaIdeal < '80') {
        $varTColorGeneralIdeal = $varMalo;
      }else{
        if ($varGeneralConsistenciaIdeal >= '90') {
          $varTColorGeneralIdeal = $varBueno;
        }else{
          $varTColorGeneralIdeal = $varEstable;
        }
      }
      
  ?>
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">

          <label style="font-size: 18px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', $varNombreServicios) ?></label>

          <div class="row">
            <div class="col-md-4">
              <div class="card4 mb">
                <table id="myTableInformacion" class="table table-hover table-bordered" style="margin-top:10px" >
                  <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Resultados Procesamientos') ?></label></caption>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad - General Konecta') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad & Consistencia') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorGeneralIdeal ?>"><?php echo  $varGeneralConsistenciaIdeal; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorMixtaAIdeal ?>"><?php echo  $varListarMixtasIdeal; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAutoIdeal ?>"><?php echo  $varArrayAutomatico; ?></label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card4 mb">
                <table id="myTableAuto" class="table table-hover table-bordered" style="margin-top:10px" >
                  <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Detalle Procesamiento Automático') ?></label></caption>
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
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varCantidadLlamadasIdeal; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorAgenteIdeal; ?>"><?php echo  $varAgentePersona; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorMarcaIdeal; ?>"><?php echo  $varMarcaPersona; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorCanalIdeal; ?>"><?php echo  $varCanalPersona; ?></label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

            <div class="col-md-4">
              <div class="card4 mb">
                <table id="myTableGestion" class="table table-hover table-bordered" style="margin-top:10px" >
                  <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', 'Detalle Gestión de la Mejora') ?></label></caption>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Feedbacks') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alertas') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calibraciones') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center"><label style="font-size: 12px; color: <?php echo $varTColorFeeback; ?>"><?php echo  $varTotalFeedbacks; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  '0'; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  '0'; ?></label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>

          </div>

          <br>

          <div class="row">
            <div class="col-md-12">
              <div class="card4 mb">
                <table id="myTableCalidad" class="table table-hover table-bordered" style="margin-top:10px" >
                  <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C6C6C6;"></em> <?= Yii::t('app', 'Detalle Calidad y Consistencia - Manual') ?></label></caption>
                  <thead>
                    <tr>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PEC') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PENC') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'SPC/SFR') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Proceso') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Experiencia') ?></label></th>
                      <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Promesa Marca') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <tr>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalValoraciones; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalPec; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalPenc; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalSfc; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalProceso; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalExperiencia; ?></label></td>
                      <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTotalPromesa; ?></label></td>
                    </tr>
                  </tbody>
                </table>
              </div>
            </div>
          </div>

          <br>


        </div>
      </div>
    </div>

    <br>

  <?php
  }
  ?>
  
</div>

<!-- Tabla Exportar Informacion -->
<div id="capaIdExportar" class="capaExportar" style="display: none;">
  
  <div class="row">
    <div class="col-md-12">
      
      <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <caption><?= Yii::t('app', '...') ?></caption>
        <thead>
          <tr>
            <th class="text-center" scope="col" style="background-color: #b0c5f3;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Konecta - CX Management') ?></label></th>  
          </tr>          
          <tr>
            <th class="text-center" scope="col"  colspan="7"><label style="font-size: 13px;"><?= Yii::t('app', '') ?></label></th>  
          </tr>
          <tr>
            <th class="text-center" scope="col" style="background-color: #C6C6C6;" colspan="2"><label style="font-size: 13px;"><?= Yii::t('app', 'Director') ?></label></th> 
            <th class="text-center" scope="col" style="background-color: #C6C6C6;" colspan="3"><label style="font-size: 13px;"><?= Yii::t('app', 'Gerentes') ?></label></th> 
            <th class="text-center" scope="col" style="background-color: #C6C6C6;" colspan="2"><label style="font-size: 13px;"><?= Yii::t('app', 'Dimension') ?></label></th> 
            <th class="text-center" scope="col" style="background-color: #C6C6C6;" colspan="3"><label style="font-size: 13px;"><?= Yii::t('app', 'Rango de Fecha') ?></label></th>  
          </tr> 
        </thead>
        <tbody>
          <tr>
            <td class="text-center" colspan="2"><label style="font-size: 12px;"><?php echo  $varNombreDirector; ?></label></td>
            <td class="text-center" colspan="3"><label style="font-size: 12px;"><?php echo  $varListasNombresGerentes; ?></label></td>
            <td class="text-center" colspan="2"><label style="font-size: 12px;"><?php echo  $varTextoDimension; ?></label></td>
            <td class="text-center" colspan="3"><label style="font-size: 12px;"><?php echo  $varFechaInicioPersona.' - '.$varFechaFinPersona; ?></label></td>
          </tr>
          <tr>
            <th class="text-center" scope="col"  colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', '') ?></label></th>  
          </tr>
          <tr>
            <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad: General Konecta') ?></label></th>
            <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad y Consistencia') ?></label></th>
            <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label></th>
            <th class="text-center" scope="col" colspan="4" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '---') ?></label></th>
            <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'General Agente') ?></label></th>
            <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'General Marca') ?></label></th>
            <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'General Canal') ?></label></th>
          </tr>
          <tr>
            <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varListarGeneralResponsabilidad; ?></label></td>
            <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varListasMixtas; ?></label></td>
            <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varPormedioResponsable; ?></label></td>
            <td class="text-center" colspan="4"><label style="font-size: 12px;"><?php echo  '--'; ?></label></td>
            <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varArrayAgente; ?></label></td>
            <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varArrayMarca; ?></label></td>
            <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varArrayCanal; ?></label></td>
          </tr>
          <tr>
            <th class="text-center" scope="col"  colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', '') ?></label></th>  
          </tr>
          <?php
            foreach ($varListasClientesIdeal as $key => $value) {
              $varNombreServiciosT = $value['cliente'];
              $varIdClientesServicioT =  $value['id_dp_clientes'];

              $varCantidadLlamadasIdealT = (new \yii\db\Query())
                                          ->select(['sum(cantidad)'])
                                          ->from(['tbl_ideal_llamadas'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','id_dp_cliente',$varIdClientesServicioT])
                                          ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                          ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                          ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                          ->Scalar(); 

              $varResponsableAutomaticoT = (new \yii\db\Query())
                                          ->select(['round(AVG(agente),2) AS ProAgente','round(AVG(marca),2) AS ProMarca','round(AVG(canal),2) AS ProCanal'])
                                          ->from(['tbl_ideal_responsabilidad'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','id_dp_cliente',$varIdClientesServicioT])
                                          ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                          ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                          ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                          ->all(); 

              $varArrayAutomaticoT = 0;
              $varAgentePersonaT = null;
              $varMarcaPersonaT = null;
              $varCanalPersonaT = null;
              foreach ($varResponsableAutomaticoT as $key => $value) {
                $varArrayAutomaticoT = round(($value['ProAgente'] + $value['ProCanal']) / 2,2);
                $varAgentePersonaT = $value['ProAgente'];      
                $varMarcaPersonaT = $value['ProMarca'];      
                $varCanalPersonaT = $value['ProCanal'];      
              }

              if ($varArrayAutomaticoT < '80') {
                $varTColorAutoIdealT = $varMalo;
              }else{
                if ($varArrayAutomatico >= '90') {
                  $varTColorAutoIdealT = $varBueno;
                }else{
                  $varTColorAutoIdealT = $varEstable;
                }
              }

              if ($varAgentePersonaT < '80') {
                $varTColorAgenteIdealT = $varMalo;
              }else{
                if ($varAgentePersonaT >= '90') {
                  $varTColorAgenteIdealT = $varBueno;
                }else{
                  $varTColorAgenteIdealT = $varEstable;
                }
              }
              if ($varMarcaPersonaT < '80') {
                $varTColorMarcaIdealT = $varMalo;
              }else{
                if ($varMarcaPersonaT >= '90') {
                  $varTColorMarcaIdealT = $varBueno;
                }else{
                  $varTColorMarcaIdealT = $varEstable;
                }
              }
              if ($varCanalPersonaT < '80') {
                $varTColorCanalIdealT = $varMalo;
              }else{
                if ($varCanalPersonaT >= '90') {
                  $varTColorCanalIdealT = $varBueno;
                }else{
                  $varTColorCanalIdealT = $varEstable;
                }
              }

              $vaListarFormulariosIdealT = (new \yii\db\Query())
                                          ->select(['formulario_id'])
                                          ->from(['tbl_ideal_novedades'])            
                                          ->where(['=','anulado',0])
                                          ->andwhere(['=','id_dp_cliente',$varIdClientesServicioT])
                                          ->andwhere(['in','cod_pcrc',$varLstasCodPcrcs])
                                          ->andwhere(['>=','fechainicio',$varFechaInicioPersona.' 05:00:00'])
                                          ->andwhere(['<=','fechafin',$varFechaFinPersona.' 05:00:00'])
                                          ->groupby(['formulario_id'])
                                          ->All(); 

              $varArrayValoracionesT = array();
              $varArrayCallidsIdealT = array();
              $varArrayFeedbacksT = array();
              $varArrayPECT = array();
              $varArrayPENCT = array();
              $varArraySFCT = array();
              $varArrayProcesoT = array();
              $varArrayExperienciaT = array();
              $varArrayPromesaT = array();
              foreach ($vaListarFormulariosIdealT as $key => $value) {
                $varFormularioIdIdealT = $value['formulario_id'];
                array_push($varArrayValoracionesT, 1);

                $varScoreIdealT = (new \yii\db\Query())
                                ->select(['score'])
                                ->from(['tbl_ejecucionformularios'])         
                                ->where(['=','id',$varFormularioIdIdealT])
                                ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                ->scalar();
                array_push($varArrayCallidsIdealT, $varScoreIdealT);

                $varCantidadFeedbacksIdealT = (new \yii\db\Query())
                                              ->select(['id'])
                                              ->from(['tbl_ejecucionfeedbacks'])         
                                              ->where(['=','ejecucionformulario_id',$varFormularioIdIdealT])
                                              ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                              ->count();
                array_push($varArrayFeedbacksT, $varCantidadFeedbacksIdealT);

                $varPecIdealT = (new \yii\db\Query())
                                ->select(['i1_nmcalculo'])
                                ->from(['tbl_ejecucionformularios'])         
                                ->where(['=','id',$varFormularioIdIdealT])
                                ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                ->scalar();
                array_push($varArrayPECT, $varPecIdealT);

                $varPencIdealT = (new \yii\db\Query())
                                ->select(['i2_nmcalculo'])
                                ->from(['tbl_ejecucionformularios'])         
                                ->where(['=','id',$varFormularioIdIdealT])
                                ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                ->scalar();
                array_push($varArrayPENCT, $varPencIdealT);

                $varSfcIdealT = (new \yii\db\Query())
                                ->select(['i3_nmcalculo'])
                                ->from(['tbl_ejecucionformularios'])         
                                ->where(['=','id',$varFormularioIdIdealT])
                                ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                ->scalar();
                array_push($varArraySFCT, $varSfcIdealT);

                $varProcesoIdealT = (new \yii\db\Query())
                                ->select(['i5_nmcalculo'])
                                ->from(['tbl_ejecucionformularios'])         
                                ->where(['=','id',$varFormularioIdIdealT])
                                ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                ->scalar();
                array_push($varArrayProcesoT, $varProcesoIdealT);

                $varExpIdealT = (new \yii\db\Query())
                                ->select(['i6_nmcalculo'])
                                ->from(['tbl_ejecucionformularios'])         
                                ->where(['=','id',$varFormularioIdIdealT])
                                ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                ->scalar();
                array_push($varArrayExperienciaT, $varExpIdealT);

                $varPromIdealT = (new \yii\db\Query())
                                ->select(['i7_nmcalculo'])
                                ->from(['tbl_ejecucionformularios'])         
                                ->where(['=','id',$varFormularioIdIdealT])
                                ->andwhere(['between','created',$varFechaInicioPersona.' 00:00:00',$varFechaFinPersona.' 00:00:00'])
                                ->scalar();
                array_push($varArrayPromesaT, $varPromIdealT);
              }


              $varTotalValoracionesT = array_sum($varArrayValoracionesT);
              $varTotalFeedbacksT = array_sum($varArrayFeedbacksT);

                if (array_sum($varArrayPECT) != 0 && $varTotalValoracionesT != 0) {
                  $varTotalPecT = round((array_sum($varArrayPECT) / $varTotalValoracionesT),2);
                }else{
                  $varTotalPecT = 0;
                }
                if (array_sum($varArrayPENCT) != 0 && $varTotalValoracionesT != 0) {
                  $varTotalPencT = round((array_sum($varArrayPENCT) / $varTotalValoracionesT),2);
                }else{
                  $varTotalPencT = 0;
                }
                if (array_sum($varArraySFCT) != 0 && $varTotalValoracionesT != 0) {
                  $varTotalSfcT = round((array_sum($varArraySFCT) / $varTotalValoracionesT),2);
                }else{
                  $varTotalSfcT = 0;
                }
                if (array_sum($varArrayProcesoT) != 0 && $varTotalValoracionesT != 0) {
                  $varTotalProcesoT = round((array_sum($varArrayProcesoT) / $varTotalValoracionesT),2);
                }else{
                  $varTotalProcesoT = 0;
                }
                if (array_sum($varArrayExperienciaT) != 0 && $varTotalValoracionesT != 0) {
                  $varTotalExperienciaT = round((array_sum($varArrayExperienciaT) / $varTotalValoracionesT),2);
                }else{
                  $varTotalExperienciaT = 0;
                }
                if (array_sum($varArrayPromesaT) != 0 && $varTotalValoracionesT != 0) {
                  $varTotalPromesaT = round((array_sum($varArrayPromesaT) / $varTotalValoracionesT),2);
                }else{
                  $varTotalPromesaT = 0;
                }

                if ($varTotalFeedbacksT < '80') {
                  $varTColorFeebackT = $varMalo;
                }else{
                  if ($varTotalFeedbacksT >= '90') {
                    $varTColorFeebackT = $varBueno;
                  }else{
                    $varTColorFeebackT = $varEstable;
                  }
                }

                if (count($varArrayCallidsIdealT) != 0) {
                  $varListarMixtasIdealT = round(array_sum($varArrayCallidsIdealT) / count($varArrayCallidsIdealT),2);
                }else{
                  $varListarMixtasIdealT = 0;
                }

                if ($varListarMixtasIdealT < '80') {
                  $varTColorMixtaAIdealT = $varMalo;
                }else{
                  if ($varListarMixtasIdealT >= '90') {
                    $varTColorMixtaAIdealT = $varBueno;
                  }else{
                    $varTColorMixtaAIdealT = $varEstable;
                  }
                }

                $varGeneralConsistenciaIdealT = round((($varArrayAutomaticoT + $varListarMixtasIdealT) / 2),2);

                if ($varGeneralConsistenciaIdealT < '80') {
                  $varTColorGeneralIdealT = $varMalo;
                }else{
                  if ($varGeneralConsistenciaIdealT >= '90') {
                    $varTColorGeneralIdealT = $varBueno;
                  }else{
                    $varTColorGeneralIdealT = $varEstable;
                  }
                }
                
            ?>

              <tr>
                <th class="text-center" scope="col" colspan="10" style="background-color: #b0c5f3;"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio - '.$varNombreServiciosT) ?></label></th>
              </tr>
              <tr>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad: General Konecta') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calidad & Consistencia') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento Automático') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Interacciones') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Marca') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Canal') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Feedbacks') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alertas') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Calibraciones') ?></label></th>
              </tr>
              <tr>
                <td class="text-center" colspan="1"><label style="font-size: 12px;  color: <?php echo $varTColorGeneralIdealT ?>"><?php echo  $varGeneralConsistenciaIdealT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px; color: <?php echo $varTColorMixtaAIdealT ?>"><?php echo  $varListarMixtasIdealT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px; color: <?php echo $varTColorAutoIdealT ?>"><?php echo  $varArrayAutomaticoT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varCantidadLlamadasIdealT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px; color: <?php echo $varTColorAgenteIdealT; ?>"><?php echo  $varAgentePersonaT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px; color: <?php echo $varTColorMarcaIdealT; ?>"><?php echo  $varMarcaPersonaT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px; color: <?php echo $varTColorCanalIdealT; ?>"><?php echo  $varCanalPersonaT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalFeedbacksT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  '0'; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  '0'; ?></label></td>
              </tr>
              <tr>
                <th class="text-center" scope="col" colspan="2" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Valoraciones') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PEC') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'PENC') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'SPC/SFR') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Proceso') ?></label></th>
                <th class="text-center" scope="col" colspan="1" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Experiencia') ?></label></th>
                <th class="text-center" scope="col" colspan="3" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indice de Promesa Marca') ?></label></th>
              </tr>
              <tr>
                <td class="text-center" colspan="2"><label style="font-size: 12px;"><?php echo  $varTotalValoracionesT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalPecT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalPencT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalSfcT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalProcesoT; ?></label></td>
                <td class="text-center" colspan="1"><label style="font-size: 12px;"><?php echo  $varTotalExperienciaT; ?></label></td>
                <td class="text-center" colspan="3"><label style="font-size: 12px;"><?php echo  $varTotalPromesaT; ?></label></td>
              </tr>              
              <tr>
                <th class="text-center" scope="col"  colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', '') ?></label></th>  
              </tr>
            <?php
              }
            ?>
        </tbody>
      </table>

    </div>
  </div>

</div>

<?php
}else{
?>

<!-- Capa Sin Data -->
<div id="capaIdSinData" class="capaSinData" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Información de Resultados') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <div class="panel panel-default">
          <div class="panel-body" style="background-color: #f0f8ff;">
            <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #df1919;"></em> <?= Yii::t('app', 'Actualmente el proceso de los filtros previamente seleccionados no tienen datos en la Base de Datos Ideal. Por favor generar nueva consulta.') ?></label>
          </div>
        </div>
      </div>
    </div>
  </div>

</div>

<?php
}
?>

<hr>

<!-- Capa Botones -->
<div id="CapaIdBtn" class="capaBtn" style="display: inline;">
  
  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', 'Buscar Por Persona') ?></label>
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

    <?php 
      if (count($varListasClientesIdeal) != 0) {
    ?>
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', 'Descargar Información') ?></label>
        <a id="dlink" style="display:none;"></a>
        <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Descarga') ?></button>
      </div>
    </div>
    <?php
      }
    ?>

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #FFC72C;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
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

<script type="text/javascript">
  function opennovedad(){
    var varidtbn1 = document.getElementById("idtbn1");
    var varidtbn2 = document.getElementById("idtbn2");
    var varidnovedad = document.getElementById("capaListaGerentes");

    varidtbn1.style.display = 'none';
    varidtbn2.style.display = 'inline';
    varidnovedad.style.display = 'inline';
  };

  function closenovedad(){
    var varidtbn1 = document.getElementById("idtbn1");
    var varidtbn2 = document.getElementById("idtbn2");
    var varidnovedad = document.getElementById("capaListaGerentes");

    varidtbn1.style.display = 'inline';
    varidtbn2.style.display = 'none';
    varidnovedad.style.display = 'none';
  };

  var chartContainerG = document.getElementById("chartContainerG");
  var chartContainerM = document.getElementById("chartContainerM");
  var chartContainerA = document.getElementById("chartContainerA");

  var oilDataG = {
    datasets: [
      {
        data: ["<?php echo $varListarGeneralResponsabilidad; ?>","<?php echo $varRestanteGeneral; ?>"],
        backgroundColor: [
          "<?php echo $varTColorGeneral; ?>",
                    "#D7CFC7"
        ]
      }]
  };

  var pieChart = new Chart(chartContainerG, {
    type: 'doughnut',
    data: oilDataG
  });

  var oilDataM = {
    datasets: [
      {
        data: ["<?php echo $varListasMixtas; ?>","<?php echo $varRestanteMixto; ?>"],
        backgroundColor: [
          "<?php echo $varTColorMixto; ?>",
          "#D7CFC7"
        ]
    }]
  };

  var pieChart = new Chart(chartContainerM, {
    type: 'doughnut',
    data: oilDataM
  });

  var oilDataA = {
    datasets: [
      {
        data: ["<?php echo $varPormedioResponsable; ?>","<?php echo $varRestanteAutos; ?>"],
        backgroundColor: [
          "<?php echo $varTColorAuto; ?>",
          "#D7CFC7"
        ]
    }]
  };

  var pieChart = new Chart(chartContainerA, {
    type: 'doughnut',
    data: oilDataA
  });

  $(function() {

    Highcharts.setOptions({
      lang: {
        numericSymbols: null,
        thousandsSep: ','
      }
    });

    $('#chartContainerResposnabilidades').highcharts({

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
          data: [<?= $varArrayAgente ?>],
          color: '#FBCE52'
        },{
          name: 'Marca',
          data: [<?= $varArrayMarca ?>],
          color: '#4298B5'
        },{
          name: 'Canal',
          data: [<?= $varArrayCanal ?>],
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
      document.getElementById("dlink").download = "Procesos_QyS";
      document.getElementById("dlink").traget = "_blank";
      document.getElementById("dlink").click();

    }
  })();

  function download(){
    $(document).find('tfoot').remove();
    var name = document.getElementById("name");
    tableToExcel('tblData', 'Archivo Procesos QyS', name+'.xls')
    //setTimeout("window.location.reload()",0.0000001);

  }
  var btn = document.getElementById("btn");
  btn.addEventListener("click",download);

</script>