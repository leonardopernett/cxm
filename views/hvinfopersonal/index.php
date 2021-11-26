

<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
?>
<style>

      .fs {
        display:flex;
        justify-content:space-between;
        width:100%;
        align-items:center;
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
                    font-family: "Nunito",sans-serif;
                    font-size: 150%;    
                    text-align: left;    
            }


            .col-sm-6 {
                width: 100%;
            }

            th, td {
                text-align: center;
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

            table.table tbody tr td,
            table.table tbody tr td a,
            table.table thead tr th a{    
                font-size: 12px !important ;
            }

            .fa-search {
              font-size:27px !important;
              padding-right:5px;
              cursor:pointer;
              color:#4298b4;
              background:transparent;
              border-radius:50%;
              padding-left:5px;
            }

            .fa-trash{
                font-size:27px !important;
                padding-right:5px;
                cursor:pointer;
                color:#981f40;
                background:transparent;
                border-radius:50%;
                padding-left:5px;
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

        h3 {
          font-family: "Nunito",sans-serif;
          text-align:center;
          color:#002855
        }


        .icono {
          display:flex;
          flex-direction:row;
          align-items:center;
          background:#4298b4 !important;
        }

        .fa-plus, .fa-list,.fa-bars, .fa-upload, .fa-calendar , .fa-info-circle{
          color:#fff;
          font-size:20px;

        }

        #toast-container > div {
            width: 400px !important;
            font-size:15px;
            opacity:1 !important;
          }

          .swal2-popup .swal2-styled.swal2-confirm:active{
            background:#4298b4 !important;
            border:#4298b4 !important;
            border:0 !important;
          }

          .swal2-popup .swal2-styled.swal2-confirm{
            background:#4298b4 !important;
            border:#4298b4 !important;
            border:0 !important;

          }
          .hide{
            display:none;
          }

          .input-area{
            width: 400px;
            height: 100px;
            border: 2px dotted #002855;
            margin: 0 auto;
            position:relative;
          }

          .input-text{
            
            display: flex;
            height: 100%;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            color:#002855

          }

          .input-file{
            position: absolute;
            left:0;
            right:0;
            top:0;
            bottom:0;
            opacity:0 ;
            width: 100%;
            height:100%;
            cursor:pointer;
          }

          .button{
            text-align: center;
            margin-top: 15px;
            display: block;
            position:relative;
           }
          .button .btn-success {
            padding: 5px 40px;
            
          }


          .dataTables_wrapper .dataTables_paginate .paginate_button:hover{
              color:#000 !important;
              border:none !important;
              border-radius:50%
          }
  
</style>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="../../js_extensions/jquery-2.1.1.min.js"></script>

<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>


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

<!-- sweet alert -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
<script src="../../js_extensions/sweetalert2/sweetalert2.all.min.js"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.css" integrity="sha512-5A8nwdMOWrSz20fDsjczgUidUBR8liPYU+WymTZP1lmY9G6Oc7HlZv156XqnsgNUzTyMefFTcsFH/tnJE/+xBg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="../../js_extensions/cloudflare/toastr.min.js"></script>
 
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">

      </div>
    </div>
  </div>
</header>
<br><br>

<ul class="breadcrumb">

  <li class="">
  <a href="<?php echo Url::to(['/'])  ?>" > Inicio </a>
  </li>
  
  <li class="active">
     Hoja de vida
  </li>
</ul>

<!-- botones  -->
<div class="container" >
   <div class="row" >
    <div class="col-md-12 " style="margin:30px auto;">
 <?php if( (int)$roles==301 || (int)$roles==299 || (int)$roles==270 || (int)$roles==305 || (int)$roles==309 || (int)$roles==304): ?>
    <div class="col-md-3">
          <div class="card1 mb icono" style="cursor:pointer">
          <i class="fa fa-bars" aria-hidden="true"></i>
            <?= Html::a('Resumen General', ['hvinfopersonal/resumen','id'=>Yii::$app->user->identity->id],['class'=>'btn btn-success', 'style'=>'font-size:15px;font-weight:bold'])  ?>
          </div>
      </div>
<?php endif ?>       

<?php if( (int)$roles==301 || (int)$roles==299 || (int)$roles==270 || (int)$roles==305 || (int)$roles==309 || (int)$roles==304): ?>
      <div class="col-md-3">
          <div class="card1 mb icono" style="cursor:pointer">
           <em class="fa fa-plus" style="display:block"></em>
            <?= Html::a('Informaci&oacute;n Personal', ['hvinfopersonal/crear','id'=>Yii::$app->user->identity->id],['class'=>'btn btn-success', 'style'=>'font-size:15px;font-weight:bold'])  ?>
          </div>
      </div>
<?php endif ?>       
      

       <?php if( (int)$roles==301 || (int)$roles==299 || (int)$roles==270 || (int)$roles==305): ?>
          <div class="col-md-3">
              <div class="card1 mb icono" style="cursor:pointer">
              <em class="fa fa-upload" style="display:block"></em>
                <a href="" class="btn btn-success" style="font-size:15px;font-weight:bold" data-toggle="modal" data-target="#exampleModal3">Carga Masiva</a>
              </div>
          </div> 
      <?php endif ?>

  <?php if((int)$roles==270 ): ?>
        <div class="col-md-3">
            <div class="card1 mb  icono" style="cursor:pointer" data-toggle="modal" data-target="#exampleModal2">
              <em class="fa fa-list" style="display:block"></em>
              <a  class="btn btn-success" style="font-size:17px" ><strong>Listas</strong></a>
            </div>
        </div> 
      </div>
 <?php endif ?>
   </div>
</div>



<!-- tablas -->
<div class="container-fluid">
  <div class="row" >
  <a href="" style="margin:15px" class="btn btn-success" data-toggle="modal" data-target="#exampleModal4">
     Exportar Usuarios <i class="fa fa-file-archive" aria-hidden="true"></i>
  </a>

  <a href="" style="margin:15px" class="btn btn-success" data-toggle="modal" data-target="#exampleModal5">
     Exportar Eventos <i class="fa fa-file-archive" aria-hidden="true"></i>
  </a>

      <div class="col-md-12">
          <div class="card1">
          <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
          <caption>...</caption>
              <thead>
                <tr>
                <th scope="col">Detalles</th>
                  <th scope="col">Director</th>
                  <th scope="col">Gerente</th>
                  <th scope="col">Cliente</th>
                
                  <th scope="col">Programa</th>
                  <th scope="col">Tipo</th>
                  <th scope="col">Nivel</th>
                  <th scope="col">Contacto</th>
                  <th scope="col">Cargo</th>
                
                  <th scope="col">Estado</th>
                  <th scope="col">Pais</th>
                  <th scope="col" class="hide">Nombre</th>
                  <th scope="col" class="hide">Identificacion</th>
                  <th scope="col" class="hide">Direccion Oficina</th>
                  <th scope="col" class="hide">Direccion Casa</th>
                  <th scope="col" class="hide">Email</th>
                  <th scope="col" class="hide">Movil</th>
                  <th scope="col" class="hide">Contacto oficina</th>
                  <th scope="col" class="hide">Pais</th>
                  <th scope="col" class="hide">Cuidad</th>
                  <th scope="col" class="hide">Modalidad de Trabajo</th>
                  <th scope="col" class="hide">Autoriza el Tratamiento de datos Personales</th>
                  <th scope="col" class="hide">Es susceptible de encuestar</th>

                  <th scope="col" class="hide">Area de Trabajo</th>
                  <th scope="col" class="hide">Antiguedad</th>
                
                  <th scope="col" class="hide">Fecha de inicio como contacto</th>
                  <th scope="col" class="hide">afinidad</th>
                  <th scope="col" class="hide">nombre del jefe</th>
                  <th scope="col" class="hide">cargojefe</th>
                  <th scope="col" class="hide">rol anterior</th>
                  <th scope="col" class="hide">estado civil</th>
                  <th scope="col" class="hide">dominancia</th>  
                  <th scope="col" class="hide">Numero de hijos</th>
                  <th scope="col" class="hide">Estilo social</th>
                </tr>
              </thead>
              <tbody>
                  <?php if($roles == 270 || $roles == 309): ?>
                      <?php foreach($model as $hv): ?>
                        <?php if($hv['anulado']==1): ?>
                          <tr>
                            <td style="text-align:center">

                              <a href="<?php echo Url::to(['/hvinfopersonal/detalle', 'id'=> $hv['idhvinforpersonal'] ] ) ?>">
                                  <em class="fa fa-search"></em> 
                              </a>
                              
                             
                              <a href="<?php echo Url::to(['/hvinfopersonal/delete','id'=>$hv['idhvinforpersonal'] ] )  ?>" class="text-danger">
                                 <em class="fa fa-trash"></em> 
                              </a>

                              </td>
                              <td>
                                <?php  echo strtoupper($hv['director']);?>
                            </td>
                            <td>
                               <?php  echo strtoupper($hv['gerente']);?>
                            </td>
                            <td><?= strtoupper($hv['cliente'])  ?></td>
                            <td>
                                <?php  echo strtoupper($hv['pcrc']);?>                              
                            </td>

                            <td ><?= strtoupper($hv['tipo']) ?></td>
                            <td ><?= strtoupper($hv['nivel']) ?></td>



                            <td><?= strtoupper($hv['hvnombre'])  ?></td>
                            <td><?= strtoupper($hv['rol'])  ?></td>
                            
                        
                           
                                <td class="<?= $hv['estado'] == 'Activo' ? 'text-success' : 'text-danger' ?>"><strong><?= $hv['estado'] ?></strong></td>
                                <td><?=  strtoupper($hv['hvpais'])  ?></td>
                                <td class="hide"><?= strtoupper($hv['hvnombre']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvidentificacion']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvdireccionoficina']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvdireccioncasa']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvemailcorporativo']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvmovil']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvcontactooficina']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvpais']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvciudad']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvmodalidatrabajo']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvautorizacion']) ?></td>
                                <td class="hide"><?= strtoupper($hv['hvsusceptible']) ?></td>
                                <td class="hide"><?= strtoupper($hv['areatrabajo']) ?></td>
                                <td class="hide"><?= strtoupper($hv['antiguedadrol']) ?></td>
                                
                                  <td class="hide"><?= strtoupper(Yii::$app->formatter->asDate($hv['fechacontacto'], 'd-M-Y')) ?></td>
                                  <td class="hide"><?= strtoupper($hv['afinidad']) ?></td>
                                  <td class="hide"><?= strtoupper($hv['nombrejefe']) ?></td>
                                  <td class="hide"><?= strtoupper($hv['cargojefe']) ?></td>
                                  <td class="hide"><?= strtoupper($hv['rolanterior']) ?></td>
                                  <td class="hide"><?= strtoupper($hv['estadocivil']) ?></td>
                                  <td class="hide"><?= strtoupper($hv['dominancia']) ?></td>
                                  <td class="hide"><?= strtoupper($hv['numerohijos']) ?></td>
                                  <td class="hide"><?= strtoupper($hv['estilosocial']) ?></td>
                                <!--  -->
                              
                          </tr>
                          <?php endif ?>
                      <?php endforeach ?>
                    
                  <?php endif   ?>


                  <?php foreach ($unicos as $unico ):  ?>

                    <?php if( $roles != 270  && $roles != 309 ):  ?>
                        <?php if($unico['anulado']==1): ?>
                            <tr>
                                <td style="text-align:center">
                                  <a href="<?php echo Url::to(['/hvinfopersonal/detalle', 'id'=> $unico['idhvinforpersonal'] ] ) ?>">
                                      <em class="fa fa-search"></em> 
                                  </a>
                                </td>
                                <td><?= strtoupper($unico['hvnombre'])  ?></td>
                                <td><?= strtoupper($unico['rol'])  ?></td>
                                <td>
                                   <?php  echo strtoupper($hv['director']);?>
                                </td>
                                <td>
                                  <?php  echo strtoupper($hv['gerente']);?>
                                </td>
                                <td><?= strtoupper($hv['cliente'])  ?></td>
                                <td>
                                    <?php  echo strtoupper($hv['pcrc']);?>                              
                                </td>
                                <td class="<?= $unico['estado'] == 'Activo' ? 'text-success' : 'text-danger' ?>"><strong><?= $unico['estado'] ?></strong></td>
                                <td><?= strtoupper($unico['hvpais'])  ?></td>

                                  <td class="hide"><?= strtoupper($unico['hvnombre']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvidentificacion']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvdireccionoficina']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvdireccioncasa']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvemailcorporativo']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvmovil']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvcontactooficina']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvpais']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvciudad']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvmodalidatrabajo']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvautorizacion']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['hvsusceptible']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['areatrabajo']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['antiguedadrol']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['tipo']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['nivel']) ?></td>
                                  <td class="hide"><?= strtoupper(Yii::$app->formatter->asDate($unico['fechacontacto'], 'd-M-Y')) ?></td>

                                  <td class="hide"><?= strtoupper($unico['afinidad']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['nombrejefe']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['cargojefe']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['rolanterior']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['estadocivil']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['dominancia']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['numerohijos']) ?></td>
                                  <td class="hide"><?= strtoupper($unico['estilosocial']) ?></td>

                            </tr>
                        <?php endif ?>
                    <?php endif ?>

                  <?php endforeach  ?>
              </tbody>
          </table>
          </div>
      </div>
    </div>
</div>


<!-- modal -->
<div class="modal fade" id="exampleModal2" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Lista</h3>

      </div>
      <div class="modal-body">
         <div class="row">
            <div class="col-md-6">
                <?php ActiveForm::begin(['action'=>['/hvinfopersonal/profesion'], 'method'=>'POST']);  ?>
                  <div class="form-group row">
                      <div class="col-md-10">
                      <label for="">Agregar Profesi&oacute;n</label>
                      </div>
                      <div class="col-md-10">
                        <input type="text" class="form-control" placeholder="Profesion" name="profesion" required>
                      </div>

                      <div class="col-md-2">
                        <button class="btn btn-success" >Agregar</button>
                      </div>
                  </div>
              <?php ActiveForm::end();  ?>

                <?php ActiveForm::begin(['action'=>['hvinfopersonal/especializacion'], 'method'=>'post']);  ?>
                    <div class="form-group row">
                        <div class="col-md-10">
                        <label for="">Agregar Especializaci&oacute;n</label>
                        </div>
                        <div class="col-md-10">
                          <input type="text" name="especializacion" class="form-control" placeholder="Especializacion" required>
                        </div>

                        <div class="col-md-2">
                          <button class="btn btn-success">Agregar</button>
                        </div>
                    </div>
                <?php ActiveForm::end();  ?>

                <?php ActiveForm::begin(['action'=>['hvinfopersonal/maestria'], 'method'=>'post']);  ?>
                    <div class="form-group row">
                        <div class="col-md-10">
                        <label for="">Agregar Maestr&iacute;a</label>
                        </div>
                        <div class="col-md-10">
                          <input type="text" name="maestria" class="form-control" placeholder="Maestria" required>
                        </div>

                        <div class="col-md-2">
                          <button class="btn btn-success">Agregar</button>
                        </div>
                    </div>
                <?php ActiveForm::end();  ?>


                <?php ActiveForm::begin(['action'=>['hvinfopersonal/doctorado'], 'method'=>'post']);  ?>
                    <div class="form-group row">
                        <div class="col-md-10">
                        <label for="">Agregar Doctorado</label>
                        </div>
                        <div class="col-md-10">
                          <input type="text" name="doctorado" class="form-control" placeholder="Doctorado" required>
                        </div>

                        <div class="col-md-2">
                          <button class="btn btn-success">Agregar</button>
                        </div>
                    </div>
                <?php ActiveForm::end();  ?>

            </div>
            
            <div class="col-md-6">
                        
          <?php ActiveForm::begin(['action'=>['hvinfopersonal/eliminarprofesion'],'method'=>'POST'])  ?>
              
              <div class="form-group row">
                  <div class="col-md-10">
                    <label for="">Eliminar Profesi&oacute;n</label>
                  </div>

                  <div class="col-md-9">
                    <select class="form-control" name="profesion">
                        <option value="">Seleccione</option>
                        <?php foreach ($profesion as  $p) : ?>
                            <option value="<?= $p['idhvcursosacademico'] ?>"><?= $p['hv_cursos'] ?></option>
                        <?php endforeach ?>
                    </select>
                  </div>

                  <div class="col-md-2">
                    <button class="btn btn-danger" >Eliminar</button>
                  </div>
              </div>

          <?php ActiveForm::end()  ?>

          <?php ActiveForm::begin(['action'=>['hvinfopersonal/eliminarespecializacion'],'method'=>'POST'])  ?>
              
              <div class="form-group row">
                  <div class="col-md-10">
                    <label for="">Eliminar Especializaci&oacute;n</label>
                  </div>

                  <div class="col-md-9">
                    <select class="form-control" name="especializacion">
                        <option value="">Seleccione</option>
                        <?php foreach ($especializacion as  $p) : ?>
                            <option value="<?= $p['idhvcursosacademico'] ?>" ><?= $p['hv_cursos'] ?></option>
                        <?php endforeach ?>
                    </select>
                  </div>

                  <div class="col-md-2">
                    <button class="btn btn-danger" >Eliminar</button>
                  </div>
              </div>

          <?php ActiveForm::end()  ?>


          <?php ActiveForm::begin(['action'=>['hvinfopersonal/eliminarmaestria'],'method'=>'POST'])  ?>
              
              <div class="form-group row">
                  <div class="col-md-10">
                    <label for="">Eliminar Maestr&iacute;a</label>
                  </div>

                  <div class="col-md-9">
                    <select class="form-control" name="maestria">
                        <option value="">Seleccione</option>
                        <?php foreach ($maestria as  $p) : ?>
                            <option value="<?= $p['idhvcursosacademico'] ?>"><?= $p['hv_cursos'] ?></option>
                        <?php endforeach ?>
                    </select>
                  </div>

                  <div class="col-md-2">
                    <button class="btn btn-danger" >Eliminar</button>
                  </div>
              </div>

          <?php ActiveForm::end()  ?>


          <?php ActiveForm::begin(['action'=>['hvinfopersonal/eliminardoctorado'],'method'=>'POST'])  ?>
              
              <div class="form-group row">
                  <div class="col-md-10">
                    <label for="">Eliminar Doctorado</label>
                  </div>

                  <div class="col-md-9">
                    <select class="form-control" name="doctorado">
                        <option value="">Seleccione</option>
                        <?php foreach ($doctorado as  $p) : ?>
                            <option value="<?= $p['idhvcursosacademico'] ?>"><?= $p['hv_cursos'] ?></option>
                        <?php endforeach ?>
                    </select>
                  </div>

                  <div class="col-md-2">
                    <button class="btn btn-danger" >Eliminar</button>
                  </div>
              </div>

          <?php ActiveForm::end()  ?>

               
       


             </div>
         </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModal3" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Agregar Carga Masiva</h3>

      </div>
      <div class="modal-body body">
          
            <?php $form = ActiveForm::begin(['action'=>['hvinfopersonal/export'], 'method'=>'POST', 'options'=>['enctype'=>'multipart/form-data']]) ?>
                <div class="input-area">
                      <div class="input-text" id="text">Seleccione o arrastre el archivo</div>
                      <em class="fa fa-upload"></em>
                      <?= $form->field($modelos, 'file')->fileInput(["class"=>"input-file" ,'id'=>'file']) ?>
                  </div> 


                  <div class="button">
                      <button  class="btn btn-success">Agregar <em class="fa fa-plus" style="padding-top:5px"></em> </button>
                  </div>
            <?php ActiveForm::end() ?>

      <a href="../../archivos/ClienteCXM.xlsx" download>Descargar Plantilla de ejemplo</a><em class="fa fa-upload"></em>
      </div>
      
    </div>
  </div>
</div>


<div class="modal fade" id="exampleModal4" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Enviar informaci&oacute;n por correo de los usuarios</h3>
      </div>
        <div class="modal-body">

        <?php if($roles == 270 || $roles == 309):  ?>
            <?php $form = Activeform::begin(['action'=>['hvinfopersonal/excelexportadmin'], 'method'=>'post']) ?>
              <div class="form-group">
                 <label for="">Correo Destinatario</label>
                 <input type="text" class="form-control" name="email" placeholder="Example@correo.com" required>
              </div>
              <button class="btn btn-primary btn-block" style="margin-top:10px">Enviar <em class="fa fa-paper-plane"></em> </button>
            <?php  Activeform::end() ?>
          <?php endif  ?>

          <?php if($roles != 270 && $roles != 309):  ?>
            <?php $form = Activeform::begin(['action'=>['hvinfopersonal/excelexport'], 'method'=>'post']) ?>
              <div class="form-group">
                 <label for="">Correo Destinatario</label>
                 <input type="text" class="form-control" name="email" placeholder="correo destinatario" required>
              </div>
              <button class="btn btn-primary btn-block" style="margin-top:10px">Enviar <em class="fa fa-paper-plane"></em></button>
            <?php  Activeform::end() ?>
          <?php endif  ?>
              
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModal5" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h3 class="modal-title" id="exampleModalLabel">Enviar informaci&oacute;n por correo de los eventos</h3>
      </div>
        <div class="modal-body">

        <?php if($roles == 270 || $roles == 309):  ?>
            <?php $form = Activeform::begin(['action'=>['hvinfopersonal/excelexporteventosadmin'], 'method'=>'post']) ?>
              <div class="form-group">
                 <label for="">Correo Destinatario</label>
                 <input type="text" class="form-control" name="email" placeholder="Example@correo.com" required>
              </div>
              <button class="btn btn-primary btn-block" style="margin-top:10px">Enviar <em class="fa fa-paper-plane"></em> </button>
            <?php  Activeform::end() ?>
          <?php endif  ?>

          <?php if($roles != 270 && $roles != 309):  ?>
            <?php $form = Activeform::begin(['action'=>['hvinfopersonal/excelexporteventos'], 'method'=>'post']) ?>
              <div class="form-group">
                 <label for="">Correo Destinatario</label>
                 <input type="text" class="form-control" name="email" placeholder="correo destinatario" required>
              </div>
              <button class="btn btn-primary btn-block" style="margin-top:10px">Enviar <em class="fa fa-paper-plane"></em></button>
            <?php  Activeform::end() ?>
          <?php endif  ?>
              
      </div>
    </div>
  </div>
</div>

<script>
const archivo = document.getElementById('file')
archivo.addEventListener('change',(e)=>{
     document.getElementById('text').innerHTML=e.target.files[0].name
 })

 function verify(){
    if(archivo.value==""){
      console.log("llenar campo")
    }
 }

  toastr.options = {
    "progressBar": true,
  }
</script>

  <?php if(Yii::$app->session->hasFlash('list')):  ?>
      <script>
            toastr.info('<?php echo Yii::$app->session->getFlash('list') ?>')
      </script>
  <?php endif  ?>

  <?php if(Yii::$app->session->hasFlash('info3')): ?>
   <script>
      toastr.error('<?php echo Yii::$app->session->getFlash('info3') ?>')
   </script>
  <?php endif  ?>

  <?php if(Yii::$app->session->hasFlash('info')):  ?>
      <script>
          toastr.info('<?php echo Yii::$app->session->getFlash('info') ?>')
      </script>
      <?php Yii::$app->session->close(); ?>
  <?php endif  ?>

  <?php if(Yii::$app->session->hasFlash('info2')):  ?>
      <script>
          toastr.info('<?php echo Yii::$app->session->getFlash('info2') ?>')
      </script>
      <?php Yii::$app->session->close(); ?>
  <?php endif  ?>

  <?php if(Yii::$app->session->hasFlash('verify')):  ?>
      <script>
          Swal.fire({
          icon: 'error',
          title: 'Oops...',
          showButtonColor: '#3085d6',
          text: '<?php echo Yii::$app->session->getFlash('verify') ?>!',
        })
      </script>
      <?php Yii::$app->session->close(); ?>
  <?php endif  ?>

  <?php if(Yii::$app->session->hasFlash('actualizar')): ?>
    <script>
          toastr.info('<?php echo Yii::$app->session->getFlash('actualizar') ?>')
    </script>
  <?php endif ?>

  <?php if(Yii::$app->session->hasFlash('file')): ?>
   <script>
      toastr.info('<?= Yii::$app->session->getFlash('file')  ?>')
   </script>
 <?php endif ?>


 <?php if (Yii::$app->session->hasFlash('delete') ): ?>
  <script>
      toastr.info('<?= Yii::$app->session->getFlash('delete')  ?>')
   </script>
 <?php endif  ?>

<script>
      $(document).ready( function () {
          $('#myTable').DataTable({
            responsive: true,
            fixedColumns: true,
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

          
      });

    
</script>