




<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<style>
    .card2{
      padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
            height:auto;   
    }
     .hide{
         opacity:0
     }

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: 15px !important;
    }

    table.table tbody tr td, table.table tbody tr td a, table.table thead tr th a {
    font-size: 15px !important;
}
    
    label{
      font-size:12px;
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

    small{
      color:#ccc;
    }
    option{
      font-size:15px;
    }

.float{
  float:left
}
    .tabs{
      background:transparent !important
    }

    .contenido{
      display:flex;
      justify-content:space-between;
      align-items:center;
    }

   .texto{
       color:red
   }

   .edit {
       font-weight:600;
       font-family:'nunito';
       text-align:center;
   }
   .d-flex {
       display:flex;
       align-items:center;
       margin:5px 0;
       
   }
   .label {
       margin-bottom:0px;
       color:#000;
       padding: 5px 0px;
   }
   .fa-info-circle{
       cursor:pointer;
   }
   h3{
       text-align:center;
   }

   .modal-footer{
       text-align:center !important;
   }

   p{
       font-size:15px;
       text-align:justify;
       margin-top:15px;
   }
   .boton {
    padding: 10px 30px !important;
    font-size: 20px;
}

.botones{
   text-align:center;
    
}

.botones .btn{
    font-size:16px;
}
   
 small{
     font-weight:bold;
 }

 .evento{
     display:block;
     text-align:center;
     margin-top:10px;
 }
 .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
    .dataTables_wrapper .dataTables_paginate .paginate_button.current{
       background:#4298b4 !important;
       color:#fff !important;
       border:none !important;
       border-radius:50%;
    }

  
button.dt-button, div.dt-button, a.dt-button, input.dt-button{
       background:#4298b4 !important;
       color:#fff !important;
       border:none !important;
}

#toast-container > div {
    width: 400px !important;
    font-size:15px;
    opacity:1 !important;
  }

  .hide{
      display:none;
  }
  .dataTables_wrapper .dataTables_paginate .paginate_button.current{
      color:#fff !important;
  }


 /*  .select2-container--default .select2-selection--single .select2-selection__arrow{
      right:-460px !important;
  }

  .select2-container--open .select2-dropdown--below{
    width: 560px !important;
  }
  .select2-container--default .select2-selection--single{
    width: 560px !important;
  }
 */
</style>


<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>


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


<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>



<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 

<div class="container">
    <div class="row">
        <div class="col-md-12 mx-auto">

        <ul class="breadcrumb">

                <li class="">
                  <a href="<?php echo Url::to(['/'])  ?>" > Inicio </a>
                </li>

                <li class="">
                <a href="<?php echo Url::to(['hvinfopersonal/index'])  ?>" > Hoja de vida </a>
                </li>

                <li class="">
                    <a href="<?php echo Url::to([ 'hvinfopersonal/detalle/'.$usuarioSelected['idhvinforpersonal'] ])  ?>" > Editar registro </a>
                </li>

                <li class="active">
                Evento
                </li>
                </ul>

            <div class="card2">
                <a href="" class="btn btn-success" style="margin-bottom:40px;float:right" data-toggle="modal" data-target="#example">
                    Crear Evento
                </a>

                <table class="table table-bordered"  id="myTable" style="margin-top:20px">
                   <thead>
                       <tr>
                           
                           <th>Nombre</th>
                           <th class="hide">Identificacion</th>
                           <th class="hide">Direccion Oficina</th>
                           <th class="hide">Direccion Casa</th>
                           <th class="hide">Email</th>
                           <th class="hide">Movil</th>
                           <th class="hide">Contacto oficina</th>
                           <th class="hide">Pais</th>
                           <th class="hide">Cuidad</th>
                           <th class="hide">Modalidad de Trabajo</th>
                           <th class="hide">Area de Trabajo</th>

                           <th class="hide">Cliente</th>
                           <th class="hide">Director</th>
                           <th class="hide">Gerente</th>
                           <th class="hide">PCRC</th>

                           <th class="hide">Antiguedad</th>
                           <th class="hide">Tipo</th>
                           <th class="hide">Nivel</th>
                           <th class="hide">afinidad</th>
                           <th class="hide">nombre del jefe</th>
                           <th class="hide">cargojefe</th>
                           <th class="hide">rol anterior</th>
                           <th class="hide">estado civil</th>
                           <th class="hide">dominancia</th>
                           <th class="hide">Numero de hijos</th>
                           <th class="hide">Estilo social</th>


                           <th>Nombre Evento</th>
                           <th>Tipo de Evento</th>
                           <th>Ciudad de Evento</th>
                           <th>Fecha del Evento</th>
                           <th>Asistio</th>

                           <th>Accion</th>
                       </tr>
                   </thead>
                   <tbody>
                       <?php foreach ($eventos as $evento): ?>
                         <tr>
                            <td><?= $evento['hvnombre'] ?></td>
                            <td class="hide"><?= strtoupper($evento['hvidentificacion']) ?></td>
                            <td class="hide"><?= strtoupper($evento['hvdireccionoficina']) ?></td>
                            <td class="hide"><?= strtoupper($evento['hvdireccioncasa']) ?></td>
                            <td class="hide"><?= strtoupper($evento['hvemailcorporativo']) ?></td>
                            <td class="hide"><?= strtoupper($evento['hvmovil']) ?></td>
                            <td class="hide"><?= strtoupper($evento['hvcontactooficina']) ?></td>
                            <td class="hide"><?= strtoupper($evento['hvpais']) ?></td>
                            <td class="hide"><?= strtoupper($evento['hvciudad']) ?></td>
                            <td class="hide"><?= strtoupper($evento['hvmodalidatrabajo']) ?></td>
                            <td class="hide"><?= strtoupper($evento['areatrabajo']) ?></td>
                            
                            <td class="hide"><?= strtoupper($evento['cliente']) ?></td>
                            <td class="hide"><?= strtoupper($evento['director']) ?></td>
                            <td class="hide"><?= strtoupper($evento['gerente']) ?></td>
                            <td class="hide"><?= strtoupper($evento['pcrc']) ?></td>

                            <td class="hide"><?= strtoupper($evento['antiguedadrol']) ?></td>
                            <td class="hide"><?= strtoupper($evento['tipo']) ?></td>
                            <td class="hide"><?= strtoupper($evento['nivel']) ?></td>
                            <td class="hide"><?= strtoupper($evento['afinidad']) ?></td>
                            <td class="hide"><?= strtoupper($evento['nombrejefe']) ?></td>
                            <td class="hide"><?= strtoupper($evento['cargojefe']) ?></td>
                            <td class="hide"><?= strtoupper($evento['rolanterior']) ?></td>
                            <td class="hide"><?= strtoupper($evento['estadocivil']) ?></td>
                            <td class="hide"><?= strtoupper($evento['dominancia']) ?></td>
                            <td class="hide"><?= strtoupper($evento['numerohijos']) ?></td>
                            <td class="hide"><?= strtoupper($evento['estilosocial']) ?></td>


                            <td><?= strtoupper($evento['nombre_evento']) ?></td>
                            <td><?= strtoupper($evento['tipo_evento']) ?></td>
                            <td><?= strtoupper($evento['ciudad_evento']) ?></td>
                            <td><?php echo Yii::$app->formatter->asDate($evento['fecha_evento'])  ?></td>
                            <td>
                                <?php if($evento['asistencia'] == null): ?>
                                     N/A
                                <?php else : ?>
                                    <?= $evento['asistencia']?>
                                 <?php endif ?>
                                </td>
                            <td style="text-align:center">
                            
                              <a href="<?php echo Url::to(['/hvinfopersonal/editarevento','id'=> $evento['id']])  ?>" data-toggle="tooltip" data-placement="top" title="Editar Evento">
                                   <i class="fa fa-search"></i>
                               </a>

                                <a style="margin-left:10px" href="<?php echo Url::to([ '/hvinfopersonal/eliminarevento', 'id' => $evento['id'],'id_user'=>$evento['idhvinforpersonal'] ]) ?>" data-toggle="tooltip" data-placement="top" title="Eliminar Evento">
                                    <i class="fa fa-trash text-danger text-center"></i>
                                </a>

                            </td>
                         </tr>
                       <?php endforeach  ?>
                   </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="example" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Agregar nuevo evento</h3>

      </div>
      <div class="modal-body">
           <div class="row">
               <div class="col-md-12">
               <?php $form = ActiveForm::begin(['action'=>['/hvinfopersonal/crearevento'], 'method'=>'post'])   ?>
               <input type="hidden" name="idhvinforpersonal" value="<?= $usuarioSelected['idhvinforpersonal']  ?>" class="form-control" required>

                    <div class="form-group">
                        <label for="">Nombre del Evento</label>
                        <input type="text" name="nombre_evento" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="">Tipo de evento</label>
                        <br>
                        <select name="tipo_evento" class="form-control" required >
                            <option value="">Seleccione</option>
                            <option value="Boletin Corporativo">Bolet&iacute;n Corporativo</option>
                            <option value="Boletin Operativo Trimestral">Bolet&iacute;n Operativo Trimestral</option>
                            <option value="Conferencia">Conferencia</option>
                            <option value="Congreso">Congreso</option>
                            <option value="Conversatorio">Conversatorio</option>
                            <option value="Curso">Curso</option>
                            <option value="Debate">Debate</option>
                            <option value="Feria">Feria</option>
                            <option value="Reconexion Brand">Reconexion Brand</option>
                            <option value="Seminario">Seminario</option>
                            <option value="Simposio">Simposio</option>
                            <option value="Workshop">Workshop</option>
                        </select>
                    </div>
                      
                    <div class="form-group">
                        <label for="">Fecha del evento</label>
                        <input type="date" name="fecha_evento" class="form-control" required>
                    </div>

                    <div class="form-group hide">
                        <label for="">Usuario</label><br>
                        <select type="hidden"  name="user_id" class="form-control" required >
                                <option value="<?php echo $usuarioSelected['idhvinforpersonal']; ?> "> <?php echo $usuarioSelected['hvnombre']; ?> </option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Cuidad del evento</label><br>
                        <select name="ciudad_evento" class="form-control" required >
                            <option value="">Seleccione</option>
                            <option value="Barranquilla">Barranquilla</option>
                            <option value="Bogota">Bogota</option>
                            <option value="Bucaramanga">Bucaramanga</option>
                            <option value="Cali">Cali</option>
                            <option value="Cartagena">Cartagena</option>
                            <option value="Cucuta">Cucuta</option>
                            <option value="Ibague">Ibague</option>
                            <option value="Medellin">Medellin</option>
                            <option value="Monteria">Monteria</option>
                            <option value="Santa Marta">Santa Marta</option>
                        </select>
                    </div>

                     <div class="form-group evento">
                        <button class="btn btn-success">Agregar Evento</button>
                     </div>
             <?php ActiveForm::end()   ?>
               </div>
           </div>
      </div>

    </div>
  </div>
</div>



<script>
   toastr.options={
    "progressBar": true,
   }
</script>

<?php if(Yii::$app->session->hasFlash('eventos')):  ?>
    <script>
       toastr.info('<?php echo Yii::$app->session->getFlash('eventos')  ?>')
   </script>
<?php endif ?>


<script>
    $(document).ready(function(){
        $('.js-example-basic-single').select2();
        $('#myTable').DataTable({
            /* dom: 'Bfrtip', */
             /*  buttons: [
                  {
                    extend: 'excelHtml5',
                    text: 'Exportar',
                    title: 'Evento_CXM',
                  }
              ], */
              "language": {
                  "lengthMenu": "Display _MENU_ records per page",
                  "zeroRecords": "No se encontraron datos ",
                  "info": "Mostrando pagina _PAGE_ de _PAGES_",
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