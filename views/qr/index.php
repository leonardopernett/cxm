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


?>

<style>
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


<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<div class="breadcrumb">
   <li>
       <a href="<?= Url::to(['/'])  ?>">Inicio</a>
   </li>

   <li>
       <a  class="active">Quejas y reclamos</a>
   </li>
</div>


<table class="table table-bordered" id="myTable">
    <thead>
        <tr>
            <th><span> Id casos</span> </th>
            <th><span>Tipos</span></th>
            <th> <span>Areas</span> </th>
            <th><span>Tipificación</span></th>
            <th><span>Comentarios</span></th>
            <th><span>Clientes</span></th>
            <th><span>Nombre</span></th>
            <th><span>Cedula</span></th>
            <th><span>Correo</span></th>
            <th><span>Estados</span></th>
            <th><span>Fecha de creación</span></th>
            <th>
                <span>Acciones</span>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($casos as $caso): ?>
            <tr>
                <td><span><?= $caso['id']  ?></span> </td>
                <td><span><?= $caso['tipo_de_dato'] ?></span> </td>
                <td><span><?= $caso['area'] ?></span> </td>
                <td><span><?= $caso['tipologia'] ?></span> </td>
                <td><span><?= $caso['comentario'] ?></span> </td>
                <td><span><?= $caso['clientes'] ?></span> </td>
                <td><span><?= $caso['nombre'] ?></span> </td>
                <td><span><?= $caso['documento'] ?></span> </td>
                <td><span><?= $caso['correo'] ?></span> </td>
                <td>
                    <?php if ($caso['estado'] =='abierto'): ?>
                         <div class="redondo-primary">
                            <?= $caso['estado'] ?>
                        </div>
                    <?php endif ?>

                    <?php if ($caso['estado'] =='en gestion'): ?>
                        <div class="redondo-success">
                            <?= $caso['estado'] ?>
                        </div>
                    <?php endif ?>

                    <?php if ($caso['estado'] =='cerrado'): ?>
                        <div class="redondo-danger">
                            <?= $caso['estado'] ?>
                        </div>
                    <?php endif ?>
                    
                </td>
                <td><span><?= Yii::$app->formatter->asDate($caso['fecha_creacion'], 'd-M-Y')  ?> </span></td>
                <td class="text-center">
                    <a href="#">
                        <em class="fa fa-search fa-2x"></em>
                    </a>
                </td>
            </tr>
        <?php endforeach ?>
    </tbody>
</table>



<script>
    $(function(){
        $('#myTable').DataTable({
            responsive: true,
            fixedColumns: true,
            dom: 'Bfrtip',
            buttons: [
                { 
                  extend: 'excel',
                  dom: 'Bfrtip',
                  text:'Export excel',
                  className: 'btn btn-primary',
                  title:'Quejas-reclamos'
                } 
             ],
            select: true,
             "language": {
                  "lengthMenu": "Display _MENU_ records per page",
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
    })
</script>