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

$this->title = 'Alertas - Reporte de Alertas Eliminadas';
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

  .card2 {
    height: 145px;
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
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => [
            'autocomplete' => 'off'
        ]
    ]
  ]); 
?>

<!-- Capa Informativas y Acciones -->
<div class="capaMixta" id="capaIdMixta" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones E Información') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-arrow-right" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Seleccionar Rango de Fecha') ?></label>     
                <?=
                                $form->field($model, 'fechacreacion', [
                                    'labelOptions' => ['class' => 'col-md-12'],
                                    'template' => 
                                     '<div class="col-md-12"><div class="input-group">'
                                    . '<span class="input-group-addon" id="basic-addon1">'
                                    . '<i class="glyphicon glyphicon-calendar"></i>'
                                    . '</span>{input}</div>{error}{hint}</div>',
                                    'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                    'options' => ['class' => 'drp-container form-group']
                                ])->label('')->widget(DateRangePicker::classname(), [
                                    'useWithAddon' => true,
                                    'convertFormat' => true,
                                    'presetDropdown' => true,
                                    'readonly' => 'readonly',
                                    'pluginOptions' => [
                                        'timePicker' => false,
                                        'format' => 'Y-m-d',
                                        'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                        'endDate' => date("Y-m-d"),
                                        'opens' => 'right',
                                ]]);
                ?> 

                <br>

                <?= Html::submitButton(Yii::t('app', 'Buscar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Buscar Datos']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card2 mb">
                <div class="row">
                    <div class="col-md-2" align="text-center">
                        <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 50px; color: #FFC72C;"></em></label>
                    </div>

                    <div class="col-md-10" align="left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Es importante recordar...') ?></label>  <label style="font-size: 15px; color: #981F40"> <?= Yii::t('app', ' Si se desea restaurar una alerta ya eliminada, se debe de buscar por rango de fecha, ya en la tabla de resultados dar luego clic en restaurar.') ?></label>
                    </div>
                </div>

                <br>

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

<?php
if ($varDataListEliminados != null) {
    
?>

<!-- Capa Resultados -->
<div class="capaResultados" id="capaIdResultados" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Lista de Alertas Eliminadas') ?></label>

                <table id="tblListadoAlertasEliminadas" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados de Alertas Eliminadas...') ?></caption>
                    <thead>
                        <tr>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Alerta Eliminada') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Quien Elimino') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id Alerta Original') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Alerta Original') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa/Pcrc  Original') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador  Original') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Alerta  Original') ?></label></th>
                          <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($varDataListEliminados as $value) {
                            
                        ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['varFechaEliminacion']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['varQuienElimina']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['varIdAlertaOriginal']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['varFechaAlertaOriginal']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['varPcrcOriginal']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['varValorador']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['vartipoAlertaOriginal']; ?></label></td>
                            <td class="text-center">
                                <?= Html::a('<em class="fas fa-edit" style="font-size: 15px; color: #4298B4;"></em>',  ['restauraralerta','ideliminaralertas'=> $value['id_eliminaralertas'],'idcopiaseliminar'=>$value['id_copiaalertaseliminar']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Restaurar Alerta']) ?>
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

<?php
}
?>

<?php $form->end() ?>

<script type="text/javascript">
    $(document).ready( function () {
        $('#tblListadoAlertasEliminadas').DataTable({
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

    function varVerificar(){
        var varFechas = document.getElementById("alertaseliminaralertas-fechacreacion").value;

        if (varFechas == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Para seguir con la búsqueda, debe de seleccionar un rango de fecha.","warning");
            return;
        }
    };
</script>