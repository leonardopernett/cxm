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

$this->title = 'Gestor Procesos GenesysCloud - Listado Colas';
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

<!-- Capa Mensaje -->
<div class="capaMensaje" id="capaIdMensaje" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: Para ingresar nuevos servicio o colas GNS a CXM verificar con el administrador del sistema.') ?></label>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
        ?>
      </div>
    </div>
  </div>

</div>

<br>

<!-- Capa de Lista -->
<div class="capaListados" id="capaIdListados" style="display: inline;">
    
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb" style="font-size: 15px;">
        <table id="tblDataColas" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados Servicios') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa/Pcrc - CXM') ?></label></th>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cola/Servicio - GNS') ?></label></th>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id Cola/Servicio - GNS') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListadoColas as $value) {
            ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  $value['name']; ?></label></td>
                <td><label style="font-size: 12px;"><?php echo  $value['cola_genesys']; ?></label></td>
                <td><label style="font-size: 12px;"><?php echo  $value['id_cola_genesys']; ?></label></td>
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

<script type="text/javascript">
  $(document).ready( function () {
        $('#tblDataColas').DataTable({
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