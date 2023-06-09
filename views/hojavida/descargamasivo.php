<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Gestor de Clientes - Descarga Masiva';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

  $varconteo = 0; 
  $varSinTexto = "--";

?>
<style>
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

</style>
<br>
<!-- Capa Procesos -->
<div id="capaIdProcesos" class="capaProcesos" style="display: inline;">

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Descarga Proceso') ?></label>
        <a id="dlink" style="display:none;"></a>
        <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Descarga') ?></button>
      </div>
    </div>
  </div>

</div>

<!-- Capa Tabla -->
<div class="capaTabla" id="capaIdTabla" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        
        <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Konecta - CX Management') ?></caption>
          <thead>
            <tr>
              <td class="text-center" scope="col" colspan="29" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Gestor Clientes - Contratos') ?></td>
            </tr>
            <tr>
              <td class="text-center" scope="col" rowspan="2" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Id') ?></td>
              <td class="text-center" scope="col" rowspan="2" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Servicio') ?></td>
              <td class="text-center" scope="col" rowspan="2" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Listado Pcrc') ?></td>
              <td class="text-center" scope="col" rowspan="2" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Listado Directores') ?></td>
              <td class="text-center" scope="col" colspan="9" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Requerimientos Sobre Rol') ?></td>
              <td class="text-center" scope="col" colspan="5" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Requerimientos Sobre Entregables') ?></td>
              <td class="text-center" scope="col" colspan="4" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Requerimientos Sobre Herramientas') ?></td>
              <td class="text-center" scope="col" colspan="5" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Requerimientos Sobre Metricas/KPI') ?></td>
              <td class="text-center" scope="col" colspan="2" style="background-color: #b0c5f3;"><?= Yii::t('app', 'Requerimientos Sobre Recursos Fisicos') ?></td>
            </tr>
            <tr>
              <!-- Requerimientos Sobre Rol -->
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Rol') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Tramo de Control - Pricing/Racional') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Tramo del Control del Contrato') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Salario') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Variable') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Total Salario') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Perfil') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Funciones') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Contiene Anexo') ?></td>

              <!-- Requerimientos Sobre Entregables -->
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Entregable') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Alcance') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Periocidad') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Detalles') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Contiene Anexo') ?></td>

              <!-- Requerimientos Sobre Herramientas -->
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Alcance') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Funcionalidades') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Detalles') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Contiene Anexo') ?></td>

              <!-- Requerimientos Sobre Metricas/KPI -->
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Metrica') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Objetivo') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Penalización') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Rango Penalización') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Contiene Anexo') ?></td>

              <!-- Requerimientos Sobre Recursos -->
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Tiene Salas Exclusivas') ?></td>
              <td class="text-center" scope="col" style="background-color: #C6C6C6;"><?= Yii::t('app', 'Comentarios') ?></td>
            </tr>
          </thead>
          <tbody>
            <?php
            foreach ($varListaContratosMasivos as $value) {
              $id_contrato = $value['varContrato'];
              $varNombreClienteServicio = $value['varCliente'];
              $varPcrcServiciosList = $value['varDirector'];
              $varNombreDirectoresServicio = $value['varPcrc'];

              $varNombreRol = $value['varRoles'];
              if ($varNombreRol == "") {
                $varNombreRol = $varSinTexto;
              }
              $varRatio = $value['varTramosPricing'];
              if ($varRatio == "") {
                $varRatio = $varSinTexto;
              }
              $varTramo = $value['varTramosControl'];
              if ($varTramo == "") {
                $varTramo = $varSinTexto;
              }
              $varSalario = $value['varSalario'];
              if ($varSalario == "") {
                $varSalario = $varSinTexto;
              }
              $varVariables = $value['varVariable'];
              if ($varVariables == "") {
                $varVariables = $varSinTexto;
              }
              $varTotalSalario = $value['varTotalSalario'];
              if ($varTotalSalario == "") {
                $varTotalSalario = $varSinTexto;
              }
              $varPerfiles = $value['varPerfil'];
              if ($varPerfiles == "") {
                $varPerfiles = $varSinTexto;
              }
              $varFunciones = $value['varFunciones'];
              if ($varFunciones == "") {
                $varFunciones = $varSinTexto;
              }
              $varAnexos = $value['varTieneAnexoUno'];


              $varNombreInforme = $value['varEntregable'];
              if ($varNombreInforme == "") {
                $varNombreInforme = $varSinTexto;
              }
              $varAlcance = $value['varInforme'];
              if ($varAlcance == "") {
                $varAlcance = $varSinTexto;
              }
              $varPeriodo = $value['varPeriocidad'];
              if ($varPeriodo == "") {
                $varPeriodo = $varSinTexto;
              }
              $varDetalle = $value['varDetalles'];
              if ($varDetalle == "") {
                $varDetalle = $varSinTexto;
              }
              $varAnexos_dos = $value['varTieneAnexoDos'];


              $varAlcanceH = $value['varAlcance'];
              if ($varAlcanceH == "") {
                $varAlcanceH = $varSinTexto;
              }
              $varFuncionalidades = $value['varFuncionalidades'];
              if ($varFuncionalidades == "") {
                $varFuncionalidades = $varSinTexto;
              }
              $varDetalles = $value['varDetallesHerramientas'];
              if ($varDetalles == "") {
                $varDetalles = $varSinTexto;
              }
              $varAnexos_tres = $value['varTieneAnexoTres'];


              $varNombreMetrica = $value['varMetrica'];
              if ($varNombreMetrica == "") {
                $varNombreMetrica = $varSinTexto;
              }
              $varObjetivo = $value['varObjetivo'];
              if ($varObjetivo == "") {
                $varObjetivo = $varSinTexto;
              }
              $varPenalizaciones = $value['varPenalizacion'];
              if ($varPenalizaciones == "") {
                $varPenalizaciones = $varSinTexto;
              }
              $varRango = $value['varRangos'];
              if ($varRango == "") {
                $varRango = $varSinTexto;
              }
              $varAnexos_Cuatro = $value['varTieneAnexoCuatro'];


              $varRta = $value['varSalas'];
              if ($varRta == "") {
                $varRta = $varSinTexto;
              }
              $varObjetivoc = $value['varComentariosSalas'];
              if ($varObjetivoc == "") {
                $varObjetivoc = $varSinTexto;
              }



            ?>
              <tr>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $id_contrato; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreClienteServicio; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPcrcServiciosList; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreDirectoresServicio; ?></label></td>
                
                <!-- Requerimientos Sobre Rol -->
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreRol; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varRatio; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varTramo; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  '$ '.$varSalario; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  '$ '.$varVariables; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  '$ '.$varTotalSalario; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPerfiles; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varFunciones; ?></label></td>
                <?php if ($varAnexos != "") { ?>
                  <td class="text-center"><label style="font-size: 12px;"><?php echo  'Si'; ?></label></td>
                <?php
                  }else{
                ?>
                  <td class="text-center"><label style="font-size: 12px;"><?php echo  'No'; ?></label></td>
                <?php
                  }
                ?>

                <!-- Requerimientos Sobre Entregables -->
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreInforme; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varAlcance; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varPeriodo; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varDetalle; ?></label></td>
                <?php if ($varAnexos_dos != "") { ?>
                  <td class="text-center"><label style="font-size: 12px;"><?php echo  'Si'; ?></label></td>
                <?php
                  }else{
                ?>
                  <td class="text-center"><label style="font-size: 12px;"><?php echo  'No'; ?></label></td>
                <?php
                  }
                ?>

                <!-- Requerimientos Sobre Herramientas -->
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varAlcanceH; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varFuncionalidades; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varDetalles; ?></label></td>
                <?php if ($varAnexos_tres != "") { ?>
                  <td class="text-center"><label style="font-size: 12px;"><?php echo  'Si'; ?></label></td>
                <?php
                  }else{
                ?>
                  <td class="text-center"><label style="font-size: 12px;"><?php echo  'No'; ?></label></td>
                <?php
                  }
                ?>

                <!-- Requerimientos Sobre Metricas/KPI -->
                <td  class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreMetrica; ?></label></td>
                <td  class="text-center"><label style="font-size: 12px;"><?php echo  $varObjetivo; ?></label></td>
                <td  class="text-center"><label style="font-size: 12px;"><?php echo  $varPenalizaciones; ?></label></td>
                <td  class="text-center"><label style="font-size: 12px;"><?php echo  $varRango; ?></label></td>
                <?php if ($varAnexos_Cuatro != "") { ?>
                  <td class="text-center"><label style="font-size: 12px;"><?php echo  'Si'; ?></label></td>                  
                <?php
                  }else{
                ?>
                  <td class="text-center"><label style="font-size: 12px;"><?php echo  'No'; ?></label></td>
                <?php
                  }
                ?>

                <!-- Requerimientos Sobre Recursos -->
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varRta; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varObjetivoc; ?></label></td>

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
            document.getElementById("dlink").download = "Gestor Cliente - Contratos";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo Servicio con Contrato Masivo CXM', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>