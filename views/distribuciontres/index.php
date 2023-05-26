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

$this->title = 'Distirbución de Personal - Versión 3.0';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                	'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

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

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


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

<!-- Capa Informativa -->
<div id="capaIdInformativa" class="capaInformativa" style="display: inline;">
    
    <div class="row">
      <div class="col-md-6">
        <div class="card1 mb" style="background: #6b97b1; ">
          <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Procesos') ?></label>
        </div>
      </div>
    </div>

    <br>

    <div class="row">
      
      <div class="col-md-6">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Parametrizar Servicios Con Distribución Externa') ?></label>
          <?= Html::a('Aceptar',  ['parametrizarservicios'], ['class' => 'btn btn-success',
                                                  'data-toggle' => 'tooltip',
                                                  'title' => 'Parametrizar Servicios con Distribución Externa']) 
          ?>
        </div>
      </div>

      <div class="col-md-6">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Descargar Plantilla') ?></label>
            
            <a style=" background-color: #337ab7" class="btn btn-success" rel="stylesheet" type="text/css" href="..\..\downloadfiles\Plantilla_ParametrizarNuevos.xlsx" title="Descagar Plantilla" target="_blank"> <?= Yii::t('app', ' Descargar') ?></a>
        </div>
      </div>


    </div>

</div>

<hr>

<!-- Capa Principal -->
<div class="capaPrincipal" id="capaIdPrincipal" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados & Gráficas') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Cantidad de Clientes') ?></label>
        <label  style="font-size: 20px; text-align: center;"><?php echo count($varListado); ?></label>
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Cantidad en Sociedad') ?></label>
        <div id="containerA" class="highcharts-container" style="height: 150px;"></div> 
      </div>

    </div>

    <div class="col-md-8">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Listado de Servicios') ?></label>
        <table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:10px" >
          <caption><label style="font-size: 15px;"> <?= Yii::t('app', '...') ?></label></caption>
            <thead>
              <tr>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Socidad') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Actualización') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
              </tr>
            </thead>
            <tbody>
              <?php
              foreach ($varListado as $key => $value) {
                $varFechaActual = (new \yii\db\Query())
                                ->select(['MAX(tbl_distribucionexterna_ultimaactualizacion.fecha_hora) AS varFechaActual'])
                                ->from(['tbl_distribucionexterna_ultimaactualizacion'])
                                ->where(['=','tbl_distribucionexterna_ultimaactualizacion.anulado',0])
                                ->andwhere(['=','tbl_distribucionexterna_ultimaactualizacion.id_formularios',$value['id_formularios']])
                                ->scalar(); 
              ?>
                <tr>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $value['id']) ?> </label></td>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $value['name']) ?> </label></td>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $value['sociedad']) ?> </label></td>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $varFechaActual) ?> </label></td>
                  <td class="text-center">
                    <?= 
                      Html::a('<em class="fas fa-upload" style="font-size: 15px; color: #827DF9;"></em>',  ['ingresardistribucion','id_general'=> $value['id_formularios']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; width: 41px;", 'title' => 'Subir Archivo de Asesores']) 
                    ?>

                    <?php
                      if ($roles == '270') {
                    ?>
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarformulario','id'=> $value['id_formularios']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700; width: 41px;", 'title' => 'Eliminar']) ?>
                    <?php
                      }
                    ?>
                    </td>
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

<script type="text/javascript">
  $(document).ready( function () {
    $('#myTableInfo').DataTable({
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

  Highcharts.chart('containerA', {
                 
    chart: {
      plotBackgroundColor: null,
      plotBorderWidth: 0,
      plotShadow: false
    },
    
    title: {
      text: '<label style="font-size: 20px;"><?php echo ''; ?></label>',
      align: 'center',
      verticalAlign: 'middle',
      y: 60
    },
    
    accessibility: {
      point: {
        valueSuffix: '%'
      }
    },

    plotOptions: {
      pie: {
        dataLabels: {
          enabled: true,
          distance: -50,
          style: {
            fontWeight: 'bold',
            color: 'white'
          }
        },
        startAngle: -90,
        endAngle: 90,
        center: ['50%', '110%'],
        size: '220%',
        width: '200%'
      }
    },

    series: [{
      type: 'pie',
      name: '',
      innerSize: '50%',
      data: [
        <?php foreach($varGraficaSociedad as $value){ ?>
          {
            name: "<?php echo $value['sociedad'];?>",
            y: parseFloat("<?php echo $value['varCantidad'];?>"),                            
            dataLabels: {
              enabled: false
            }
          },
        <?php } ?>
      ]
    }]
  });
</script>