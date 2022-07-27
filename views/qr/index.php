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

$this->title = 'Quejas y Reclamos';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-12">'
. ' {input}{error}{hint}</div>';

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

    .redondo-primary {
        background: #337ab7;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:60px
    }

    .redondo-danger {
        background: red;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:60px
    }

    .redondo-success {
        background: #4298b4;
        color: #fff;
        padding: 2px 0px;
        border-radius: 20px;
        /* font-size: 13px; */
        text-align: center;
        font-weight: bold;
        width:70px
    }
    span {
        font-size:14px !important;
    }

    button.dt-button, div.dt-button, a.dt-button, input.dt-button{
        background-color:#4298b4 !important;
        color:#fff !important;
    }

    .text-center{
      
    align-items: center;
    flex-direction: row;
    justify-content:center;
    padding: 10px 5px !important;
    }

    label {
        font-size: 15px;
    }

</style>

<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<!-- Extensiones -->
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
<div class="capaInformacion" id="capaIdInfo" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Listado Quejas y Reclamos') ?></label>

                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                    <caption>.</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Id') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Identificación del Caso ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Dato ') ?></label></th>                            
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cliente ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Nombre Usuario ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Documento Usuario ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Correo Electrónico ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Estado ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentarios ') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Creación ') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($model as $key => $value) {
                                $varIdCaso = $value['idcaso'];
                                $varNumCaso = $value['numero_caso'];
                                $varTipoDato = $value['tipo_de_dato'];
                                $varComentarios = $value['comentario'];
                                $varCliente = $value['cliente'];
                                $varUsuario = $value['nombre'];
                                $varDocUsuario = $value['documento'];
                                $varEmail = $value['correo'];
                                $varEstado = $value['estado'];
                                $varIdEstado = $value['idestado'];
                                $varFechaCreacion = $value['fecha_creacion'];

                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $varIdCaso; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varNumCaso; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varTipoDato; ?></label></td>                                
                                <td><label style="font-size: 12px;"><?php echo  $varCliente; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varUsuario; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varDocUsuario; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varEmail; ?></label></td>

                                <?php
                                    if ($varIdEstado == '1') {
                                ?>
                                        <td><label style="font-size: 12px; color: #559FFF"><?php echo  $varEstado; ?></label></td>
                                <?php
                                    }
                                ?>

                                <?php
                                    if ($varIdEstado == '3') {
                                ?>
                                        <td><label style="font-size: 12px; color: #00968F"><?php echo  $varEstado; ?></label></td>
                                <?php
                                    }
                                ?>

                                <?php
                                    if ($varIdEstado == '2') {
                                ?>
                                        <td><label style="font-size: 12px; color: #D01E53"><?php echo  $varEstado; ?></label></td>
                                <?php
                                    }
                                ?>



                                
                                <td><label style="font-size: 12px;"><?php echo  $varComentarios; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  Yii::$app->formatter->asDate($varFechaCreacion); ?></label></td>
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
        $('#myTable').DataTable({
            responsive: true,
            fixedColumns: true,
            dom: 'Bfrtip',
            buttons: [
                { 
                  extend: 'excel',
                  dom: 'Bfrtip',
                  text:'Exportar a excel',
                  className: 'btn btn-primary',
                  title:'Quejas-reclamos'
                } 
             ],
            select: true,
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
