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

$this->title = 'Gestor de Clientes - Envio de Data Por Servicio';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones =Yii::$app->user->identity->id;  
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
    font-family: "Nunito";
    font-size: 150%;    
    text-align: left;    
  }

</style>
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
<div id="capaIdTabla" class="capaTabla" style="display: none;">
	
	<div class="row">
		<div class="col-md-12">
			<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <caption>...</caption>
        <thead>
        	<tr>
        		<th class="text-center" scope="col" style="background-color: #b0c5f3;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Konecta - CX Management') ?></label></th>  
        	</tr>
        	<tr>
        		<th class="text-center" scope="col" style="background-color: #C6C6C6;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio') ?></label></th>
        	</tr>
        	</tr>      		
        </thead>
        <tbody>
        	<tr>
            <td class="text-center" colspan="10"><label style="font-size: 12px;"><?php echo  $varNombreClienteServicio; ?></label></td>
          </tr>
          <tr>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de PCRC') ?></label></th>
          </tr>
          <?php
          	foreach ($varNombrePcrcsServicio as $key => $value) {
         	?>
						<tr>
            	<td class="text-center" colspan="10"><label style="font-size: 12px;"><?php echo  $value['cod_pcrc'].' - '.$value['pcrc']; ?></label></td>
          	</tr>
         	<?php
          	}
          ?>
          <tr>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de Directores') ?></label></th>
          </tr>
          <?php
          	foreach ($varNombreDirectoresServicio as $key => $value) {
         	?>
						<tr>
            	<td class="text-center" colspan="10"><label style="font-size: 12px;"><?php echo  $value['director_programa']; ?></label></td>
          	</tr>
         	<?php
          	}
          ?>
          <tr>
        		<th class="text-center" scope="col" style="background-color: #b0c5f3;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos sobre el rol') ?></label></th>  
        	</tr>
        	<tr>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rol') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tramo de Control - Pricing/Racional') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tramo del Control del Contrato') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Salario') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Variable') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Total Salario') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Perfil') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Funciones') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Contiene Anexo') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexo') ?></label></th>
          </tr>
          <?php
          	foreach ($vardataProviderPersonaServicio as $key => $value) {
          		$varNombreRol = (new \yii\db\Query())
          								->select(['tbl_hojavida_roles.hvroles'])
                          ->from(['tbl_hojavida_roles'])
                          ->where(['=','tbl_hojavida_roles.id_hvroles',$value['id_hvroles']])
                          ->andwhere(['=','tbl_hojavida_roles.anulado',0])
                          ->Scalar();

              if ($varNombreRol == "") {
              	$varNombreRol = $varSinTexto;
              }

              $varRatio = $value['ratiopricing'];
              if ($varRatio == "") {
              	$varRatio = $varSinTexto;
              }

              $varTramo = $value['tramocontrol'];
              if ($varTramo == "") {
              	$varTramo = $varSinTexto;
              }

              $varSalario = $value['salario'];
              if ($varSalario == "") {
              	$varSalario = $varSinTexto;
              }

              $varVariables = $value['variable'];
              if ($varVariables == "") {
              	$varVariables = $varSinTexto;
              }

              $varTotalSalario = $value['totalsalario'];
              if ($varTotalSalario == "") {
              	$varTotalSalario = $varSinTexto;
              }

              $varPerfiles = $value['perfil'];
              if ($varPerfiles == "") {
              	$varPerfiles = $varSinTexto;
              }

              $varFunciones = $value['funciones'];
              if ($varFunciones == "") {
              	$varFunciones = $varSinTexto;
              }

         	?>
						<tr>
            	<td class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreRol; ?></label></td>
            	<td class="text-center"><label style="font-size: 12px;"><?php echo  $varRatio; ?></label></td>
            	<td class="text-center"><label style="font-size: 12px;"><?php echo  $varTramo; ?></label></td>
            	<td class="text-center"><label style="font-size: 12px;"><?php echo  '$ '.$varSalario; ?></label></td>
            	<td class="text-center"><label style="font-size: 12px;"><?php echo  '$ '.$varVariables; ?></label></td>
            	<td class="text-center"><label style="font-size: 12px;"><?php echo  '$ '.$varTotalSalario; ?></label></td>
            	<td class="text-center"><label style="font-size: 12px;"><?php echo  $varPerfiles; ?></label></td>
            	<td class="text-center"><label style="font-size: 12px;"><?php echo  $varFunciones; ?></label></td>
            	<?php if ($value['rutaanexo'] != "") { ?>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  'Si'; ?></label></td>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  $value['rutaanexo']; ?></label></td>
            	<?php
            		}else{
            	?>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  'No'; ?></label></td>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  '--'; ?></label></td>
            	<?php
            		}
            	?>
          	</tr>
         	<?php
          	}
          ?>
          <tr>
        		<th class="text-center" scope="col" style="background-color: #b0c5f3;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos sobre los entregables') ?></label></th>  
        	</tr>
        	<tr>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Entregable') ?></label></th>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alcance') ?></label></th>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Periocidad') ?></label></th>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Detalles') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Contiene Anexo') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexo') ?></label></th>
          </tr>
          <?php
          	foreach ($vardataProviderentregableServicio as $key => $value) {
          		$varNombreInforme = (new \yii\db\Query())
          								->select(['tbl_hojavida_informe.hvinforme'])
                          ->from(['tbl_hojavida_informe'])
                          ->where(['=','tbl_hojavida_informe.id_hvinforme',$value['id_hvinforme']])
                          ->andwhere(['=','tbl_hojavida_informe.anulado',0])
                          ->Scalar();
              if ($varNombreInforme == "") {
              	$varNombreInforme = $varSinTexto;
              }

              $varPeriodo = (new \yii\db\Query())
          								->select(['tbl_hojavida_periocidad.hvperiocidad'])
                          ->from(['tbl_hojavida_periocidad'])
                          ->where(['=','tbl_hojavida_periocidad.id_hvperiocidad',$value['id_hvperiocidad']])
                          ->andwhere(['=','tbl_hojavida_periocidad.anulado',0])
                          ->Scalar();
              if ($varPeriodo == "") {
              	$varPeriodo = $varSinTexto;
              }

              $varAlcance = $value['alcance'];
              if ($varAlcance == "") {
              	$varAlcance = $varSinTexto;
              }

              $varDetalle = $value['detalle'];
              if ($varDetalle == "") {
              	$varDetalle = $varSinTexto;
              }

         	?>
						<tr>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreInforme; ?></label></td>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varAlcance; ?></label></td>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varPeriodo; ?></label></td>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varDetalle; ?></label></td>
            	<?php if ($value['rutaanexoinforme'] != "") { ?>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  'Si'; ?></label></td>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  $value['rutaanexoinforme']; ?></label></td>
            	<?php
            		}else{
            	?>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  'No'; ?></label></td>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  '--'; ?></label></td>
            	<?php
            		}
            	?>
          	</tr>
         	<?php
          	}
          ?>
          <tr>
        		<th class="text-center" scope="col" style="background-color: #b0c5f3;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos sobre las herramientas') ?></label></th>  
        	</tr>
        	<tr>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Alcance') ?></label></th>
          	<th colspan="3" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Funcionalidades') ?></label></th>
          	<th colspan="3" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Detalles') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Contiene Anexo') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexo') ?></label></th>
          </tr>
          <?php
          	foreach ($vardataProviderherramientasServicio as $key => $value) {

          		$varAlcanceH = $value['alcance'];
          		if ($varAlcanceH == "") {
          			$varAlcanceH = $varSinTexto;
          		}

          		$varFuncionalidades = $value['funcionalidades'];
          		if ($varFuncionalidades == "") {
          			$varFuncionalidades = $varSinTexto;
          		}

          		$varDetalles = $value['detalle'];
          		if ($varDetalles == "") {
          			$varDetalles = $varSinTexto;
          		}
         	?>
						<tr>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varAlcanceH; ?></label></td>
            	<td colspan="3" class="text-center"><label style="font-size: 12px;"><?php echo  $varFuncionalidades; ?></label></td>
            	<td colspan="3" class="text-center"><label style="font-size: 12px;"><?php echo  $varDetalles; ?></label></td>
            	<?php if ($value['rutaanexoherramienta'] != "") { ?>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  'Si'; ?></label></td>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  $value['rutaanexoherramienta']; ?></label></td>
            	<?php
            		}else{
            	?>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  'No'; ?></label></td>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  '--'; ?></label></td>
            	<?php
            		}
            	?>
          	</tr>
         	<?php
          	}
          ?>
          <tr>
        		<th class="text-center" scope="col" style="background-color: #b0c5f3;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos sobre las métricas/KPI') ?></label></th>  
        	</tr>
        	<tr>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Métrica') ?></label></th>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Objetivo') ?></label></th>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Penalización') ?></label></th>
          	<th colspan="2" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rangos de Penalización') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Contiene Anexo') ?></label></th>
          	<th class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Anexo') ?></label></th>
          </tr>
          <?php
          	foreach ($vardataProvidermetricasServicio as $key => $value) {

          		$varNombreMetrica = (new \yii\db\Query())
          								->select(['tbl_hojavida_metricas.hvmetrica'])
                          ->from(['tbl_hojavida_metricas'])
                          ->where(['=','tbl_hojavida_metricas.id_hvmetrica',$value['id_hvmetrica']])
                          ->andwhere(['=','tbl_hojavida_metricas.anulado',0])
                          ->Scalar();

              $varObjetivo = $value['obtjetivo'];
              if ($varObjetivo == "") {
              	$varObjetivo = $varSinTexto;
              }

              $varPenalizacion = $value['penalizacion'];
              if ($varPenalizacion == "") {
              	$varPenalizacion = $varSinTexto;
              }

              $varRango = $value['rango'];
              if ($varRango == "") {
              	$varRango = $varSinTexto;
              }
         	?>
						<tr>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varNombreMetrica; ?></label></td>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varObjetivo; ?></label></td>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varObjetivo; ?></label></td>
            	<td colspan="2" class="text-center"><label style="font-size: 12px;"><?php echo  $varRango; ?></label></td>
            	<?php if ($value['rutaanexokpis'] != "") { ?>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  'Si'; ?></label></td>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  $value['rutaanexokpis']; ?></label></td>
            	<?php
            		}else{
            	?>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  'No'; ?></label></td>
            		<td class="text-center"><label style="font-size: 12px;"><?php echo  '--'; ?></label></td>
            	<?php
            		}
            	?>
          	</tr>
         	<?php
          	}
          ?>          
          <tr>
        		<th class="text-center" scope="col" style="background-color: #b0c5f3;" colspan="10"><label style="font-size: 13px;"><?= Yii::t('app', 'Requerimientos sobre los recursos fisicos') ?></label></th>  
        	</tr>
        	<tr>
          	<th colspan="5" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tiene Salas Exclusivas') ?></label></th>
          	<th colspan="5" class="text-center" scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
          </tr>
          <?php
          	foreach ($vardataExclusivasServicio as $key => $value) {

          		$varRta = $value['exclusivas'];
          		if ($varRta == 1) {
          			$varRta = "Si";
          		}else{
          			$varRta = "No";
          		}

              $varObjetivoc = $value['comentarios'];
              if ($varObjetivoc == "") {
              	$varObjetivoc = $varSinTexto;
              }

         	?>
						<tr>
            	<td colspan="5" class="text-center"><label style="font-size: 12px;"><?php echo  $varRta; ?></label></td>
            	<td colspan="5" class="text-center"><label style="font-size: 12px;"><?php echo  $varObjetivoc; ?></label></td>
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
            document.getElementById("dlink").download = "Listado_Servicio_con_Contrato";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo Servicio con Contrato CXM', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>