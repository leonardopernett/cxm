

<?php 
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

?>

<style>
    .card2{
      padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
            height:auto;   
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
        background-image: url('../../images/HojadevidaCliente.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
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
       font-family: "Nunito",sans-serif;
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
.mx{
    margin:auto;
}
</style>


<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <!-- Compiled and minified CSS -->

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">


<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>

<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 

 <div class="container">
     <div class="row">
         <div class="col-md-10 mr-auto">
             
           <div class="card2">
           <?php $form = ActiveForm::begin(['action'=>['/hvinfopersonal/updateevento','id'=>$eventoOne['id']], 'method'=>'post'])   ?>
              <div class="col-md-6">
                    <div class="form-group">
                        <label for="">Nombre del Evento</label>
                        <input type="text" name="nombre_evento" value="<?= $eventoOne['nombre_evento'] ?>" class="form-control" required>
                    </div>

                    <div class="form-group">
                        <label for="">Tipo de evento</label>
                        <br>
                        <select name="tipo_evento" value="<?= $eventoOne['tipo_evento'] ?>" class="form-control" required >
                            <option value="">Seleccione</option>
                            <option value="Boletin Corporativo" <?php if($eventoOne['tipo_evento'] === 'Boletin Corporativo') echo 'selected' ?> >Boletin Corporativo</option>
                            <option value="Bolet�n Operativo Trimestral" <?php if($eventoOne['tipo_evento'] === 'Bolet�n Operativo Trimestral') echo 'selected' ?>>Bolet�n Operativo Trimestral</option>
                            <option value="Conferencia" <?php if($eventoOne['tipo_evento'] === 'Conferencia') echo 'selected' ?>>Conferencia</option>
                            <option value="Congreso" <?php if($eventoOne['tipo_evento'] === 'Congreso') echo 'selected' ?>>Congreso</option>
                            <option value="Conversatorio" <?php if($eventoOne['tipo_evento'] === 'Conversatorio') echo 'selected' ?>>Conversatorio</option>
                            <option value="Curso" <?php if($eventoOne['tipo_evento'] === 'Curso') echo 'selected' ?>>Curso</option>
                            <option value="Debate" <?php if($eventoOne['tipo_evento'] === 'Debate') echo 'selected' ?>>Debate</option>
                            <option value="Feria" <?php if($eventoOne['tipo_evento'] === 'Feria') echo 'selected' ?>>Feria</option>
                            <option value="Reconexion Brand" <?php if($eventoOne['tipo_evento'] === 'Reconexion Brand') echo 'selected' ?>>Reconexion Brand</option>
                            <option value="Seminario" <?php if($eventoOne['tipo_evento'] === 'Seminario') echo 'selected' ?>>Seminario</option>
                            <option value="Simposio" <?php if($eventoOne['tipo_evento'] === 'Simposio') echo 'selected' ?>>Simposio</option>
                            <option value="Workshop" <?php if($eventoOne['tipo_evento'] === 'Workshop') echo 'selected' ?>>Workshop</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="">Fecha del evento</label>
                        <input type="text" name="fecha_evento" value="<?= $eventoOne['fecha_evento'] ?>" class="form-control" required>
                    </div>

              </div>

                
              <div class="col-md-6">

              <div class="form-group">
                    <label for="">Usuario</label><br>
                    <select name="user_id" value="<?=  $eventoOne['user_id'] ?>" class="form-control" required >
                        <option value="">Seleccione</option>
                        <?php foreach ($usuarios as $usuario): ?>
                            <option value="<?php echo $usuario['idhvinforpersonal'];  ?>" <?php if($usuario['idhvinforpersonal'] == $eventoOne['user_id']) echo 'selected' ?> > <?php echo $usuario['hvnombre']; ?> </option>
                        <?php endforeach  ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="">Asistira al Evento</label><br>
                    <select name="asistencia" value="<?=  $eventoOne['asistencia'] ?>" class="form-control" required >
                        <option value="">Seleccione</option>
                        <option value="Si" <?php if($eventoOne['asistencia'] === 'Si') echo 'selected' ?> >Si</option>
                        <option value="No" <?php if($eventoOne['asistencia'] === 'No') echo 'selected' ?> >No</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="">Cuidad del evento</label><br>
                    <select name="ciudad_evento" value="<?=  $eventoOne['ciudad_evento'] ?>" class="form-control" required >
                        <option value="">Seleccione</option>
                        <option value="Barranquilla" <?php if($eventoOne['ciudad_evento'] === 'Barranquilla') echo 'selected' ?> >Barranquilla</option>
                        <option value="Bogota" <?php if($eventoOne['ciudad_evento'] === 'Bogota') echo 'selected' ?> >Bogota</option>
                        <option value="Bucaramanga" <?php if($eventoOne['ciudad_evento'] === 'Bucaramanga') echo 'selected' ?> >Bucaramanga</option>
                        <option value="Cali" <?php if($eventoOne['ciudad_evento'] === 'Cali') echo 'selected' ?> >Cali</option>
                        <option value="Cartagena" <?php if($eventoOne['ciudad_evento'] === 'Cartagena') echo 'selected' ?> >Cartagena</option>
                        <option value="Cucuta" <?php if($eventoOne['ciudad_evento'] === 'Cucuta') echo 'selected' ?> >Cucuta</option>
                        <option value="Ibague" <?php if($eventoOne['ciudad_evento'] === 'Ibague') echo 'selected' ?> >Ibague</option>
                        <option value="Medellin" <?php if($eventoOne['ciudad_evento'] === 'Medellin') echo 'selected' ?> >Medellin</option>
                        <option value="Monteria" <?php if($eventoOne['ciudad_evento'] === 'Monteria') echo 'selected' ?> >Monteria</option>
                        <option value="Santa Marta" <?php if($eventoOne['ciudad_evento'] === 'Santa Marta') echo 'selected' ?> >Santa Marta</option>
                    </select>
                </div>

              </div>





            <div class="form-group evento">
            <button class="btn btn-success" style="margin-top:20px;font-size:20px">Editar Evento</button>
            </div>
           <?php ActiveForm::end()   ?>
           </div>
         </div>
     </div>
 </div>