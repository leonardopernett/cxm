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
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;

$this->title = 'Procesos Voc - Configurar Categorias & Pcrc';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Procesos Voc - Configurar Categorias & Pcrc';

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
                ->where(['=','tbl_usuarios.usua_id',$sessiones]);                     
    $command = $rol->createCommand();
    $roles = $command->queryScalar();


?>
<style>
    .lds-ring {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 100px;
    }
    .lds-ring div {
      box-sizing: border-box;
      display: block;
      position: absolute;
      width: 80px;
      height: 80px;
      margin: 10px;
      border: 10px solid #3498db;
      border-radius: 70%;
      animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      border-color: #3498db transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
      animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
      animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
      animation-delay: -0.15s;
    }
    @keyframes lds-ring {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
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
<!-- datatable -->
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
<div class="CapaInfo" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card2 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Funciones Voc"; ?> </label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    
    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-minus-circle" style="font-size: 20px; color: #FFC72C;"></em> Cancelar y regresar</label>
        <?= Html::a('Aceptar',  ['index'], ['class' => 'btn btn-success',
                               'style' => 'background-color: #707372',                        
                                'data-toggle' => 'tooltip',
                                'title' => 'Nuevo'])
        ?>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-save" style="font-size: 20px; color: #FFC72C;"></em> Registrar Categorias</label>
        <?= Html::a('Aceptar',  ['registarcategorias'], ['class' => 'btn btn-success',                                                      
                                'data-toggle' => 'tooltip',
                                'title' => 'Aceptar'])
        ?>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em> Registrar Extensiones</label>
        <?= Html::a('Aceptar',  ['registarextensiones'], ['class' => 'btn btn-success',                                                      
                                'data-toggle' => 'tooltip',
                                'title' => 'Aceptar'])
        ?>
      </div>
    </div>

  </div>

</div>
<br>
<hr>
<br>
<div class="CapaList" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card2 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Listado Servicios Configurados"; ?> </label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    
    <div class="col-md-12">
      <div class="card1 mb">
        <label><em class="fas fa-list" style="font-size: 20px; color: #ff8c55;"></em> Listado</label>
        <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
          <caption>...</caption>
          <thead>
            <tr>
              <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id Servicio') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Servicios') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cantidad Pcrc Configurados') ?></label></th>
              <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListadoServicios as $key => $value) {                               
                $txtServicioId = $value['id_dp_clientes'];
                $txtSericios = $value['nameArbol'];

                $txtCantidadPcrc = (new \yii\db\Query())
                    ->select(['cod_pcrc'])
                    ->from(['tbl_speech_parametrizar'])
                    ->where('id_dp_clientes = :varIdClientes',[':varIdClientes'=>$txtServicioId])
                    ->andwhere('tipoparametro IS NULL')
                    ->groupby(['cod_pcrc'])
                    ->count();
            ?>
              <tr>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $txtServicioId; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $txtSericios; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $txtCantidadPcrc; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?= Html::a('<em class="fas fa-search" style="font-size: 20px; color: #ff8c55;"></em>',  ['viewpcrc','idservicios' => $txtServicioId], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Ver']) ?></td>
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
        $('#myTable').DataTable({
            responsive: true,
            fixedColumns: true,
            select: false,
            "language": {
                "lengthMenu": "Cantidad de Datos a Mostrar _MENU_ ",
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