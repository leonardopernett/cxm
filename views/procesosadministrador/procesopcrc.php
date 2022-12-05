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

use yii\db\Query;
use app\models\SpeechCategorias;
use app\models\Dashboardservicios;

$this->title = 'Actualización de Centros de Costos Por Servicio';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from(['tbl_roles'])
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();


?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
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
<!-- Extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">

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

<!-- Capa ProcesosPor Pcrc -->
<div class="capaPcrc" id="capaIdPcrc" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones - '.$varNombreCliente) ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>

      <div class="col-md-3">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Actualizar Pcrc de '.$varNombreCliente) ?></label>

          <?= Html::a('Actualizar',  ['actualizapcrccliente','idpcrc'=>$iddpclientes], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Actualizar Centros de Costos'])
          ?>
        </div>

        <br>

        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Descargar Información') ?></label>
          <a id="dlink" style="display:none;"></a>           
          <button  class="btn btn-download" style="background-color: #4298B4;color: #fff;" id="btn"><?= Yii::t('app', ' Descargar') ?></button>
        </div>

        <br>

        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
          <?= Html::a('Regresar',  ['adminpcrc'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
          ?>
        </div>

      </div>

      <div class="col-md-9">
        <div class="card1 mb">
          <table id="myTableInformacion" class="table table-hover table-bordered" style="margin-top:10px" >
            <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Lista de Centros de Costos - '.$varNombreCliente) ?></label></caption>
            <thead>
              <tr>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pcrc') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cod_Pcrc') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado Jarvis') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
              </tr>
            </thead>
            <tbody>
              <?php
                foreach ($varListaPcrc as $key => $value) {                    
              ?>
                  <tr>
                    <td><label style="font-size: 12px;"><?php echo  $value['pcrc']; ?></label></td>
                    <td><label style="font-size: 12px;"><?php echo  $value['cod_pcrc']; ?></label></td>
                    <td><label style="font-size: 12px;"><?php echo  $value['estado']; ?></label></td>
                    <td class="text-center">
                      <?= Html::a('<em class="fas fa-thumbs-down" style="font-size: 15px; color: #FC4343;"></em>',  ['anularpcrc','id'=> $value['idvolumendirector'],'iddpclientes'=>$iddpclientes], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Anular']) ?>

                      <?= Html::a('<em class="fas fa-thumbs-up" style="font-size: 15px; color: #2cdc5a;"></em>',  ['activarpcrc','id'=> $value['idvolumendirector'],'iddpclientes'=>$iddpclientes], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Activar']) ?>

                    </td>
                  </tr>
              <?php
                }
              ?>
            </tbody>
          </table>
        </div>
      </div>

    <?php $form->end() ?>

  </div>

</div>

<hr>

<div id="capaListadoCompletoId" class="capaListadoCompleto" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <table id="myTableListado" class="table table-hover table-bordered" style="margin-top:10px" >
        <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Listado Centros de Costos - '.$varNombreCliente) ?></label></caption>
        <thead>
          <tr>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Pcrc') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Centro Costos') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado Jarvis') ?></label></th>               
          </tr>
        </thead>
        <tbody>
          <?php
            foreach ($varListaPcrc as $key => $value) {                    
          ?>
            <tr>
              <td><label style="font-size: 12px;"><?php echo  $value['pcrc']; ?></label></td>
              <td><label style="font-size: 12px;"><?php echo  $value['cod_pcrc']; ?></label></td>
              <td><label style="font-size: 12px;"><?php echo  $value['estado']; ?></label></td>
            </tr>
          <?php
            }
          ?>
        </tbody>
      </table>
    </div>
  </div>
</div>

<script type="text/javascript">
  var tableToExcel = (function () {
    var uri = 'data:application/vnd.ms-excel;base64,',
    template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
    base64 = function (s) {
      return window.btoa(unescape(encodeURIComponent(s)))
    }, 
    format = function (s, c) {
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
      document.getElementById("dlink").download = "Seguimiento Centros de Costos Jarvis";
      document.getElementById("dlink").traget = "_blank";
      document.getElementById("dlink").click();

    }
  })();
  
  function download(){
    $(document).find('tfoot').remove();
    var name = document.getElementById("name");
    tableToExcel('myTableListado', 'Archivo Seguimiento', name+'.xls')
    
  }
  
  var btn = document.getElementById("btn");
  btn.addEventListener("click",download);

  $(document).ready( function () {
    $('#myTableInformacion').DataTable({
      responsive: true,
      fixedColumns: true,
      select: true,
      "language": {
        "lengthMenu": "Cantidad de Datos a Mostrar _MENU_",
        "zeroRecords": "No se encontraron datos ",
        "info": "Mostrando p&aacute;gina _PAGE_ a _PAGES_ de _MAX_ registros",
        "infoEmpty": "No hay datos aun",
        "infoFiltered": "(Filtrado un _MAX_ total)",
        "search": "Buscar:",
        "paginate": {
          "first":      "Primero",
          "last":       "Ultimo",
          "next":       "Siguiente",
          "previous":   "Anterior"
        }
      } 
    });
  });
</script>