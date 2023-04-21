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

$this->title = 'Gestor Valoraciones Externas';
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
   	font-family: "Nunito";
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 355px;
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

<!-- Capa Principal -->
<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Gráficas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
      <div class="col-md-4">

        <?php
          if ($roles == '270') {
            
        ?>  

          <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Crear Procesos Parametrizador') ?></label>
            <?= Html::button('Aceptar', ['value' => url::to(['agregarservicio']), 'class' => 'btn btn-success', 'id'=>'modalButton',
                                  'data-toggle' => 'tooltip',
                                  'title' => 'Crear Procesos Parametrizador']) 
            ?> 

            <?php
              Modal::begin([
                'header' => '<h4>Selección de Filtros</h4>',
                'id' => 'modal',
                'size' => 'modal-lg',
              ]);

              echo "<div id='modalContent'></div>";
                                                                                                    
              Modal::end(); 
            ?>
            <br>
            <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Importante: Esta opción permite parametrizar las acciones para obtener la data del excel el cual estan las valoraciones.') ?></label>          
          </div>

          <br>

        <?php
          }
        ?>

        
      </div>

      <div class="col-md-8">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Listado de Clientes con Valoración Externa') ?></label>

          <table id="tblDataValoracionE" class="table table-striped table-bordered tblResDetFreed">
            <caption><?= Yii::t('app', 'Resultados') ?></caption>
            <thead>
              <tr>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cliente') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sociedad') ?></label></th>
                <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
              </tr>
            </thead>
            <tbody>
              <?php
                foreach ($varListaGeneral as $key => $value) {
                
              ?>
                <tr>
                  <td><label style="font-size: 12px;"><?php echo  $value['cliente']; ?></label></td>
                  <td><label style="font-size: 12px;"><?php echo  $value['sociedad']; ?></label></td>
                  <td class="text-center">

                    <?= 
                      Html::a('<em class="fas fa-upload" style="font-size: 12px; color: #B833FF;"></em>',  ['agregarvaloraciones','id_general'=> $value['id_clientenuevo']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Subir Archivo de Valoraciones']) 
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
        $('#tblDataValoracionE').DataTable({
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