


<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\Url;
?>
<style>
        .select2-container--default .select2-selection--multiple .select2-selection__choice{
                   font-size:10px;
               }
    .card2{
      padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
            height:400px;     
    }


    .col-sm-6 {
        width: 100%;
    }


    th {
        text-align: left;
        font-size: smaller;
    }
    .icono {
          display:flex;
          margin-top:10px;
          flex-direction:row;
          align-items:center;
          justify-content:center;
          background:#4298b4 !important;
        }

    .form-control{
        height:28px;
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
   .label {
       margin-bottom:0px;
       color:#000;
       padding: 5px 0px;
   }
   .fa-info-circle{
       cursor:pointer;
   }
   h2{
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

.btn-success{
    margin:0 auto;
    display:block;
    
}
small{
     font-weight:bold;
 }

      .central {
          display:flex;
          flex-direction:row;
          align-items:center;
          margin-top:10px;
          justify-content:center;
          border-radius: 5px;
        }

        .fa-plus, .fa-list, .fa-calendar , .fa-info-circle{
          color:#fff;
          font-size:20px;

        }

        .complemento{
            margin-top:10px;
            padding:8px
        }
        .sus {
            margin: 10px 10px 10px 0;
            font-size:15px;
            font-weight:bold;
        }

        .data{
            font-weight:bold;
            width:100%;
        }

        .hidden{
            display:none;
        }

        .block{
            display:block;
        }
        #afinidad, .nivel, .tipo, .estado{
            height:33px;
        }
</style>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <!-- Compiled and minified CSS -->
<!-- <div class="masthead"></div> -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.all.min.js"></script>

<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 



     <div class="container" style="margin-top:15px">
        <div class="row">
          <div class="col-md-12">
          <ul class="breadcrumb">
                <li class="">
                    <a href="<?php echo Url::to(['/'])  ?>" > Inicio </a>
                </li>
                <li>
                   <a href=" <?php echo Url::to(['hvinfopersonal/index']) ?>">
                       Hoja de vida
                    </a>
                </li>
                
                <li class="active">
                    Editar Registro
                </li>
            </ul>

            <?php $form = ActiveForm::begin(['action'=>['/hvinfopersonal/update', 'id'=>$data['idhvinforpersonal'] ] ,'method'=>'post' ]);  ?>

            <div class="row">
                
            <h3 class="edit">Editando Informaci&oacute;n Personal</h3>
                    <div class="col-md-4" >
                      <button type="submit" class="btn btn-success data" style="margin-top:10px">Actualizar Informaci&oacute;n</button>
                    </div>

                    <div class="col-md-4" >
                      <?= Html::a('Informaci&oacute;n Complementaria', ['hvinfopersonal/complementaria','id'=>$data['idhvinforpersonal']],['class'=>'btn btn-success sus'])  ?>
                    </div>

                    <div class="col-md-4" >
                          <div class="card1 mb icono">
                                  <?= Html::a('Eventos', ['/hvinfopersonal/eventos','id'=>$data['idhvinforpersonal']],['class'=>'btn btn-success estilo','style'=>'font-size:15px;font-weight:bold'])  ?>
                          </div>
                    </div>

                  </div>

                <div class="row" style="margin-top:10px">
                    <!-- datos personales -->
                        <div class="card2">
                            <div class="col-md-4">
                                            <small>Datos personales</small>
                                            
                                            
                                                <div class="form-group">
                                                    <label for=""><span class="texto">*</span> Nombre: </label>
                                                    <input type="text" name="hvnombre" value="<?php echo $data['hvnombre'];  ?>"  class="form-control" required >
                                                </div>
                                            
                                                <div class="form-group">
                                                    <label for=""><span class="texto">*</span> N&uacute;mero de Identificaci&oacute;n:</label>
                                                    <input type="number" min="1900" name="hvidentificacion" value="<?php echo $data['hvidentificacion'];  ?>"  class="form-control" required>
                                                </div>
                        
                                                <div class="form-group">
                                                    <label for=""><span class="texto">*</span> Direcci&oacute;n Oficina:</label>
                                                    <input type="text" name="hvdireccionoficina" value="<?php echo $data['hvdireccionoficina'];  ?>" class="form-control" required>
                                                </div>
                        
                                                <div class="form-group">
                                                    <label for=""><span class="texto">*</span> Direcci&oacute;n Domicilio:</label>
                                                    <input type="text" name="hvdireccioncasa" value="<?php echo $data['hvdireccioncasa'];  ?>" class="form-control"required >
                                                </div>
                            </div>

                            <div class="col-md-4" style="margin-top:24px">
                                                <div class="form-group">
                                                    <label for=""><span class="texto">*</span> Correo Electr&oacute;nico Corporativo:</label>
                                                    <input type="text" name="hvemailcorporativo" value="<?php echo $data['hvemailcorporativo'];  ?>" class="form-control" required >
                                                </div>

                                                <div class="form-group">
                                                    <label for=""> <span class="texto">*</span> Celular:</label>
                                                    <input type="number" min="1900" name="hvmovil" value="<?php echo $data['hvmovil'];  ?>" class="form-control"required >
                                                </div>

                                                <div class="form-group">
                                                    <label for=""><span class="texto">*</span> Tel&eacute;fono  Oficina:</label>
                                                    <input type="number" min="1900" name="hvcontactooficina" value="<?php echo $data['hvcontactooficina'];  ?>" class="form-control" required>
                                                </div>


                                                <div class="form-group">
                                                    <label for=""> <span class="texto">*</span> Pa&iacute;s:</label>
                                                    <select name="hvpais"  class="js-example-basic-single form-control">
                                                        <option value="">Seleccione</option>
                                                          <?php foreach($paises as $p): ?>
                                                              <option value="<?php echo $p['pais'] ; ?>" <?php if( $p['pais'] === $data['hvpais']) echo 'selected' ?>    > <?php echo $p['pais'] ; ?></option>
                                                          <?php endforeach ?>                                            
                                                    </select>
                                                </div>
                            </div>         
                        
                                            <div class="col-md-4" style="margin-top:24px">
                                                <div class="form-group">
                                                    <label for=""><span class="texto">*</span> Ciudad:</label>
                                                    <select name="hvciudad" value="<?php echo $data['hvciudad'];  ?>"  class="js-example-basic-single form-control" required >
                                                        <option value="">Seleccione</option>
                                                        <?php foreach($ciudad as $p): ?>
                                                              <option value="<?php echo $p['ciudad'] ; ?>" <?php if( $p['ciudad'] === $data['hvciudad']) echo 'selected' ?>    > <?php echo $p['ciudad'] ; ?></option>
                                                          <?php endforeach ?>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                <label for=""><span class="texto">*</span> Modalidad de Trabajo:</label>
                                                <select name="hvmodalidatrabajo" value="<?php echo $data['hvmodalidatrabajo'] ?>" class="js-example-basic-single form-control" placeholder="Cliente" required >
                                                    <option value="">Seleccione</option>
                                                    <option value="Trabajo en casa" <?php if($data['hvmodalidatrabajo']==="Trabajo en casa") echo 'selected' ?> >Trabajo en casa</option>
                                                    <option value="Oficina" <?php if($data['hvmodalidatrabajo']==="Oficina") echo 'selected' ?> >Oficina</option>
                                                    <option value="Alternancia" <?php if($data['hvmodalidatrabajo']==="Alternancia") echo 'selected' ?> >Alternancia</option>
                                                    <option value="No definido" <?php if($data['hvmodalidatrabajo']==="No definido") echo 'selected' ?> >No definido</option>
                                                    <option value="Sin informacion" <?php if($data['hvmodalidatrabajo']==="Sin informacion") echo 'selected' ?> >Sin informaci&oacute;n</option>
                                                </select>
                                                
                                                <div class="form-group">
                                                    <label for="">Autoriza el Tratamiento de datos Personales:</label>
                                                    <select name="hvautorizacion" value="<?=$data['hvautorizacion']  ?>" class="js-example-basic-single form-control" placeholder="Cliente" >
                                                        <option>Seleccione</option>
                                                        <option value="si"  <?php if($data['hvautorizacion'] == "si") echo 'selected' ?> >Si</option>
                                                        <option value="no"  <?php if($data['hvautorizacion'] == "no") echo 'selected' ?> >No</option>
                                                    </select>
                                                </div>

                                                <div class="form-group">
                                                <div class="row">
                                                <div class="col-md-6">
                                                  <label for="" style="cursor:pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="TTB" >Es susceptible de encuestar:</label>
                                                  <select name="hvsusceptible" value="<?= $data['hvsusceptible'] ?>" class="form-control" style="height:30px">
                                                      <option value="">Seleccione</option>
                                                      <option value="Si" <?php if($data['hvsusceptible'] =="Si") echo 'selected' ?> >Si</option>
                                                      <option value="No" <?php if($data['hvsusceptible'] =="No") echo 'selected' ?>>No</option>
                                                  </select>
                                                </div>

                                                <div class="col-md-6">
                                                  <label for="" style="cursor:pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="TTB" >Indicador Satu: (%)</label>
                                                  <div class="input-group">
                                                  <input type="number" name="hvsatu" value="<?= $data['hvsatu'] ?>" step="0.01" class="form-control" >
                                                  <span class="input-group-addon" id="basic-addon1">%</span>
                                                  </div>

                                                </div>
                                            </div>
                                                </div>                
                                    </div>
                                
                                </div>
                        </div>
                        <!-- find datos personales -->   
                </div>

                <div class="row" style="margin-top:10px">
                    <!-- datos laborales -->
                    <div class="card2">


                        <div class="col-md-4" style="margin-bottom:10px">
                        
                                <small>Datos de Cuenta</small>

                                <div class="form-group">
                                    <label for=""> Cliente: </label>
                                    <select name="client" value="<?php echo $data['cliente']; ?>" class="js-example-basic-single form-control" placeholder="Cliente" >
                                    <option value="">Seleccione</option>
                                    <?php foreach($clientes as $p): ?>                                  
                                        <option value="<?php echo $p['cliente']; ?>" <?php if( $p['cliente'] === $data['cliente'] ) echo 'selected' ?> > <?php echo $p['cliente'] ; ?></option>
                                    <?php endforeach ?>
                                    </select>
                                </div>  

                               

                                    <div class="form-group">
                                    <label for=""> Director:</label>
                                    <select name="director[]" multiple="multiple" value="<?=$data['director']?>"  class="js-example-basic-single form-control" placeholder="Cliente" >
                                    <option value="">Seleccione</option>  
                                      <?php foreach ($director_programa as $p): ?>
                                         <?php if ($p['director_programa'] !== "0"  && $p['director_programa'] !== "Sin info" )  : ?>
                                                <option value="<?php echo $p['director_programa']?>" 
                                                    <?php foreach(explode( "," , $data['director']) as $director): ?>  
                                                        <?php if($p['director_programa']== $director ) echo 'selected' ?> 
                                                    <?php endforeach ?>   
                                                    >
                                                    <?= $p['director_programa'] ;  ?>
                                               </option>                                  
                                         <?php endif ?> 
                                    <?php endforeach ?>                                  
                                    </select>
                                    </div>

                                    <div class="form-group">
                                    <label for=""> Gerente:  </label>
                                        <select name="gerente[]" multiple="multiple" value="<?=$data['gerente']?>"  class="js-example-basic-single form-control" placeholder="Cliente" >
                                            <option value="">Seleccione</option>
                                            <?php foreach (explode( "," , $data['gerente']) as $gerent): ?>
                                                <?php foreach ($gerente as $p): ?>
                                                  <option value="<?php echo $p['gerente_cuenta']?>"  <?php if($p['gerente_cuenta'] === $gerent) echo 'selected' ?>   >
                                                     <?php echo $p['gerente_cuenta']  ?>
                                                  </option>
                                                <?php endforeach ?> 
                                            <?php endforeach ?> 
                                        </select>
                                    </div>

                                    <div class="form-group">
                                    <label for=""> Pcrc:</label>
                                    <select name="pcrc[]" multiple="multiple" value="<?=$data['pcrc']?>" class="js-example-basic-single form-control" placeholder="Cliente" >
                                    <option value="">Seleccione</option>
                                    <?php foreach (explode( "," , $data['pcrc']) as $programa): ?>
                                        <?php foreach($pcrc as $p): ?>
                                            <option value="<?php echo $p['pcrc'] ; ?>"  <?php if($p['pcrc'] === $programa) echo 'selected' ?> > <?php echo $p['pcrc'] ; ?></option>
                                        <?php endforeach ?>
                                    <?php endforeach ?>
                                    </select>
                                </div>    
                        </div>

                        <div class="col-md-4" style="margin-bottom:10px">
                        
                        <small>Datos Laborales</small>

                        <div class="form-group">
                            <label for=""><span class="texto">*</span> Rol:</label>
                            <input name="rol" value="<?=$data['rol']?>"  class="form-control" placeholder="Rol">
                             
                        </div>

                        <div class="form-group">
                            <label for=""><span class="texto">*</span> Antiguedad del Rol:</label>
                            <select name="antiguedadrol" value="<?= $data['antiguedadrol'] ?>" class="js-example-basic-single form-control" placeholder="Cliente">
                                <option value="">Seleccione</option>
                                <option value="0-3 meses" <?php if($data['antiguedadrol']=== '0-3 meses') echo 'selected' ?> >0-3 meses</option>
                                <option value="4-6 meses" <?php if($data['antiguedadrol']=== '4-6 meses') echo 'selected' ?> >4-6 meses</option>
                                <option value="7 meses - 1 año" <?php if($data['antiguedadrol']=== '7 meses - 1 año') echo 'selected' ?> >7 meses - 1 a&ntilde;o</option>
                                <option value="1 años - 3 años" <?php if($data['antiguedadrol']=== '1 años - 3 años') echo 'selected' ?> >1 a&ntilde;os - 3 a&ntilde;os</option>
                                <option value="4 años - 6 años" <?php if($data['antiguedadrol']=== '4 años - 6 años') echo 'selected' ?> >4 a&ntilde;os - 6 a&ntilde;os</option>
                                <option value="mayor a 6 años" <?php if($data['antiguedadrol']=== 'mayor a 6 años') echo 'selected' ?> >mayor a 6 a&ntilde;os</option>
                            </select>
                        </div>

                        <div class="form-group">
                          <label for=""><span class="texto">*</span> Fecha de inicio como contacto:</label>
                          <input type="date" name="fechacontacto" value="<?php 
                               $createDate = new DateTime($data['fechacontacto'] );
                               echo $createDate->format('Y-m-d'); 
                          ?>" class="form-control" required >
                        </div>  

                        <div class="form-group">
                                <div class="d-flex">
                                    <label for="" class="label"><span class="texto">*</span> Afinidad:</label> <i class="fa fa-info-circle fa-1x" data-toggle="modal" data-target="#exampleModalCenter"></i>
                                </div>
                                <select name="afinidad" id="afinidad" value="<?php echo $data['afinidad']?>" class="form-control">
                                    <option value="">Seleccionar</option>
                                    <option value="relacion directa" <?php if($data['afinidad'] === 'relacion directa' ) echo 'selected' ?> >Relaci&oacute;n Directa</option>
                                    <option value="de interes" <?php if($data['afinidad'] === 'de interes') echo 'selected' ?> >Relaci&oacute;n de Inter&eacute;s</option>
                                </select>
                            </div> 

                        
                          <div  class="row">                            
                            <div class="col-md-12" style="display: inline;"><br>
                              <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                                <thead>
                                  <tr>
                                    <th scope="text-center"><?= Yii::t('app', 'Tipo de afinidad') ?></th>
                                    <th scope="text-center"><?= Yii::t('app', 'Nivel de afinidad') ?></th>
                                  </tr>
                                </thead>
                                <tbody>
                                  <tr>
                                    <td scope="text-center"><?= Yii::t('app', $data['tipo']) ?></td>
                                    <td scope="text-center"><?= Yii::t('app', $data['nivel']) ?></td>
                                  </tr>
                                </tbody>
                              </table>
                            </div>
                          </div>
                        

                    </div>

                    <div class="col-md-4" style="margin-top:24px">
                          

                            <div class="form-group hidden" id="group" >
                                <label for=""><span class="texto">*</span> Tipo:</label>
                                <select name="tipo" value="<?= $data['tipo'] ?>" class="form-control tipo" placeholder="Cliente" required>
                                        <option value="">Seleccione</option>
                                        <option value="Decisor"  <?php if($data['tipo']=== 'Decisor') echo 'selected' ?> >Decisor</option>
                                        <option value="No Decisor"  <?php if($data['tipo']=== 'No Decisor') echo 'selected' ?> >No Decisor</option>
                                </select>
                            </div>

                            <div class="form-group hidden" id="group2">
                                    <label for=""><span class="texto">*</span> Nivel:</label>
                                    <select name="nivel" value="<?= $data['nivel'] ?>" class="form-control nivel" placeholder="Cliente" required>
                                        <option value="">Seleccione</option>
                                        <option value="estrategico" <?php if($data['nivel']=== 'estrategico') echo 'selected' ?> >Estrategico</option>
                                        <option value="operativo" <?php if($data['nivel']=== 'operativo') echo 'selected' ?> >Operativo</option>
                                </select>
                            </div> 

                        <div class="form-group">
                            <label for=""><span class="texto">*</span> Nombre del Jefe:</label>
                            <input type="text" name="nombrejefe" value="<?= $data['nombrejefe'] ?>"  class="form-control" >
                        </div>  

                        <div class="form-group">
                            <label for="">Cargo del Jefe:</label>
                            <input name="cargojefe" value="<?= $data['cargojefe'] ?>" class="form-control" placeholder="Cliente" >
                               
                        </div> 

                        
                        <div class="form-group">
                            <label for="">Trabajo Anterior:</label>
                            <input type="text" name="rolanterior" value="<?= $data['rolanterior'] ?>"  class="form-control" >
                        </div>  

                        
                    </div>   




                    </div>   
                    <!-- find de datos laborales -->
                </div>

                <div class="row" style="margin-top:10px">
                    <!-- datos laborales -->
                    <div class="card2">
            
                    <div class="col-md-4" style="margin-top:24px">
                   
                    <small>Datos acad&eacute;micos </small>

                        <div class="form-group">
                            <label for="">Profesi&oacute;n:</label>
                            <select name="profesion"  value="<?= $data['profesion'] ?>" class="js-example-basic-single form-control" placeholder="Cliente">
                            <option value="">Seleccione</option>
                                <?php foreach($profesion as $p): ?>
                                    <option value="<?php echo $p['hv_cursos'] ; ?>" <?php if( $p['hv_cursos'] === $data['profesion']) echo 'selected' ?>    > <?php echo $p['hv_cursos'] ; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>  

                        <div class="form-group">
                            <label for="">Especializaci&oacute;n:</label>
                            <select name="especializacion" value="<?= $data['especializacion'] ?>" value="" class="js-example-basic-single form-control" placeholder="Cliente">
                            <option value="">Seleccione</option>
                                <?php foreach($especializacion as $p): ?>
                                    <option value="<?php echo $p['hv_cursos'] ; ?>" <?php if( $p['hv_cursos'] === $data['especializacion']) echo 'selected' ?> > <?php echo $p['hv_cursos'] ; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>

                        
                    <div class="form-group">
                            <label for="">Maestr&iacute;a:</label>
                            <select name="maestria" value="<?= $data['maestria'] ?>"  class="js-example-basic-single form-control" placeholder="Cliente">
                            <option value="">Seleccione</option>
                                <?php foreach($maestria as $p): ?>
                                    <option value="<?php echo $p['hv_cursos'] ; ?>" <?php if( $p['hv_cursos'] === $data['maestria']) echo 'selected' ?> > <?php echo $p['hv_cursos'] ; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div> 
                        
                        <div class="form-group">
                            <label for="">Doctorado:</label>
                            <select name="doctorado" value="<?= $data['doctorado'] ?>"  class="js-example-basic-single form-control" placeholder="Cliente">
                            <option value="">Seleccione</option>
                                <?php foreach($doctorado as $p): ?>
                                    <option value="<?php echo $p['hv_cursos'] ; ?>" <?php if( $p['hv_cursos'] === $data['doctorado']) echo 'selected' ?>> <?php echo $p['hv_cursos'] ; ?></option>
                                <?php endforeach ?>
                            </select>
                        </div>  

         


                        <small style="color:#000">Los campos con <span style="color:red">*</span> son requeridos</small> 
        
                    </div>

                    <div class="col-md-4" style="margin-top:24px">

                    <div class="form-group" style="margin-top:20px">
                            <label for="">Estado:</label>
                            <select class="form-control estado" name="estado">
                            <option value="">Seleccione</option>
                            <option value="Activo" <?php if( $data['estado'] =="Activo" ) echo 'selected' ?>  >Activo</option>
                            <option value="Inactivo" <?php if( $data['estado'] =="Inactivo" ) echo 'selected' ?> >Inactivo</option>
                            </select>
                       </div> 


                    </div>
                    <!-- find de datos laborales -->
                </div>

                <!-- here -->

                </div>
            
            <?php ActiveForm::end();  ?>
          </div>
      </div>
   </div>

<!-- Modal -->
<div class="modal fade" id="exampleModalCenter" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
        <h2 class="modal-title" id="exampleModalCenterTitle">Afinidad con Konecta</h2>
         <p><b>Relacion Directa:</b> Son tus contactos del d&iacute;a a d&iacute;a, con quienes defines estrategias para  el canal y/o  haces seguimiento a los indicadores operativos. </p>
         <p><b>Relacion Inter&eacute;s:</b> Son aquellos contactos que no tiene relaci&oacute;n con el contrato de Konecta, sin embargo tienen cargos estrat&eacute;gicos dentro de la compa&ntilde;&iacute;a por ejemplo Directores de Tecnolog&iacute;a, Presidente, Gerentes, Vicepresidentes.</p>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success boton" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>


<?php if(Yii::$app->session->hasFlash('info2')):  ?>
      <script>
          toastr.info('<?php echo Yii::$app->session->getFlash('info2') ?>')
      </script>
      <?php Yii::$app->session->close(); ?>
  <?php endif  ?>


<script>
  $(document).ready(function() {
      $('.js-example-basic-single').select2();
      $('.tabs').tabs();
  });

  const agrupar = document.getElementById('group')
  const agrupar2 = document.getElementById('group2')


  document.getElementById('afinidad').addEventListener('change',e =>{
       console.log(e.target.value)
       if(e.target.value==="relacion directa"){
        agrupar.classList.add('block');
        agrupar.classList.remove('hidden')

        agrupar2.classList.add('block');
        agrupar2.classList.remove('hidden')
       }else{
        agrupar.classList.add('hidden');
        agrupar.classList.remove('block')

        agrupar2.classList.add('hidden');
        agrupar2.classList.remove('block')
       }
  })

  document.getElementById('afinidad').addEventListener('active',e =>{
       console.log(e.target.value)
       if(e.target.value==="relacion directa"){
        agrupar.classList.add('block');
        agrupar.classList.remove('hidden')

        agrupar2.classList.add('block');
        agrupar2.classList.remove('hidden')
       }else{
        agrupar.classList.add('hidden');
        agrupar.classList.remove('block')

        agrupar2.classList.add('hidden');
        agrupar2.classList.remove('block')
       }
  })


</script>