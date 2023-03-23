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

$this->title = 'Procesos Administrativos API - Interacciones';
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

<!-- Capa Procesos -->
<div id="capaProcesosId" class="capaProcesos" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
      
      <div class="col-md-4">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Agregar Nuevo Parametro') ?></label>

          <?= 
            Html::button('Aceptar', ['value' => url::to(['agregarnuevodatoapi']), 'class' => 'btn btn-success', 'id'=>'modalButton',
              'data-toggle' => 'tooltip',
              'title' => 'Agregar Nuevo Parametro']) 
          ?> 

          <?php
            Modal::begin([
              'header' => '<h4>Agregar Información Proceso Api</h4>',
              'id' => 'modal',
              'size' => 'modal-lg',
            ]);

            echo "<div id='modalContent'></div>";
                                                                                                  
            Modal::end(); 
          ?>
        </div>

        <br>

        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
          <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
          ?>
        </div>

      </div>

      <div class="col-md-8">
        <div class="card1 mb">

          <table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:10px" >
            <caption><label style="font-size: 15px;"> <?= Yii::t('app', 'Lista de Parametrizaciones en CXM ') ?></label></caption>
            <thead>
              <tr>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Proyecto') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tabla') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sociedad') ?></label></th>
                <th scope="col" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
              </tr>
            </thead>
            <tbody>
              <?php
                foreach ($varListaData as $key => $value) {    

                  if ($value['sociedadprovieniente'] == 1) {
                    $varSociedad = 'SAE';
                  }else{
                    if ($value['sociedadprovieniente'] == 2) {
                      $varSociedad = 'WIA';
                    }else{
                      $varSociedad = 'Otro';
                    }
                  }       
              ?>
                <tr>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $value['id_parametrizarapi']) ?> </label></td>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $value['cliente']) ?> </label></td>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $value['proyecto_id']) ?> </label></td>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $value['table_id']) ?> </label></td>
                  <td><label style="font-size: 12px;"> <?= Yii::t('app', $varSociedad) ?> </label></td>
                  <td class="text-center">
                      <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deteleapiwiasae','id'=> $value['id_parametrizarapi']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
</script>
