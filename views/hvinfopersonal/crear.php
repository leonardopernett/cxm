


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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left; 
            height:400px;   
               }

               .select2-container--default .select2-selection--multiple .select2-selection__choice{
                   font-size:10px;
               }

    .card3{
      padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
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

.botones{
   text-align:center;
    
}

.botones .btn{
    font-size:16px;
}
   
 small{
     font-weight:bold;
 }

 .agrupar {
     display:flex;
     align-items:center;
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
<script src="../../js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
 <!-- Compiled and minified CSS -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
<script src="../../js_extensions/sweetalert2/sweetalert2.all.min.js"></script>

<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="../../js_extensions/cloudflare/toastr.min.js"></script>
 


<div class="container contenedor" style="margin-top:15px">
  <div class="row">

     <div class="col-md-12">
     <ul class="breadcrumb">

            <li class="">
            <a href="<?php echo Url::to(['/'])  ?>" > Inicio </a>
            </li>
            
            <li class="">
                <a href="<?php echo Url::to(['hvinfopersonal/index']) ?>"> Hoja de vida </a>
            </li>

                <li class="active">
                    Nuevo Registro
                </li>
       </ul>

      <?php $form = ActiveForm::begin(['action'=>['/hvinfopersonal/store'] ,'method'=>'post' ]);  ?>
      <h3 class="edit">Informaci&oacute;n Personal</h3>

        <div class="row" style="margin-top:10px">
            <!-- datos personales -->
              <div class="card2">
                    <div class="col-md-4">
                                    <small>Datos personales</small>
                                    
                                        <div class="form-group">
                                            <label for=""><span class="texto">*</span> Nombre: </label>
                                            <input type="text" name="hvnombre" class="form-control" required >
                                        </div>
                                    
                                        <div class="form-group">
                                            <label for=""><span class="texto">*</span> N&uacute;mero de Identificaci&oacute;n:</label>
                                            <input type="number" min="1900" name="hvidentificacion" class="form-control" required >
                                        </div>
                
                                        <div class="form-group">
                                            <label for=""><span class="texto">*</span> Direcci&oacute;n Oficina:</label>
                                            <input type="text" name="hvdireccionoficina" class="form-control" required >
                                        </div>
                
                                        <div class="form-group">
                                            <label for=""><span class="texto">*</span> Direcci&oacute;n Domicilio:</label>
                                            <input type="text" name="hvdireccioncasa" class="form-control" required >
                                        </div>
                    </div>

                    <div class="col-md-4" style="margin-top:24px">
                                        <div class="form-group">
                                            <label for=""><span class="texto">*</span> Correo Electr&oacute;nico Corporativo:</label>
                                            <input type="text" name="hvemailcorporativo" class="form-control" required >
                                        </div>

                                        <div class="form-group">
                                            <label for=""> <span class="texto">*</span> Celular:</label>
                                            <input type="number" min="1900" name="hvmovil" class="form-control" required >
                                        </div>

                                        <div class="form-group">
                                            <label for=""><span class="texto">*</span> Tel&eacute;fono  Oficina:</label>
                                            <input type="number" min="1900" name="hvcontactooficina" class="form-control" required >
                                        </div>


                                        <div class="form-group">
                                            <label for=""> <span class="texto">*</span> Pa&iacute;s:</label>
                                            <select name="hvpais"  class="js-example-basic-single form-control" required>
                                              <option value="">Seleccione</option>
                                                <?php foreach($paises as $p): ?>
                                                    <option value="<?php echo $p['pais'] ; ?>"> <?php echo $p['pais'] ; ?></option>
                                                <?php endforeach ?>                                            
                                                    </select>
                                             
                                            </select>
                                        </div>
                    </div>         
                
                    <div class="col-md-4" style="margin-top:24px">
                                        <div class="form-group">
                                            <label for=""><span class="texto">*</span> Ciudad:</label><br>
                                            <select name="hvciudad" class="js-example-basic-single form-control" required >
                                                <option value="">Seleccione</option>
                                                <?php foreach($ciudad as $p): ?>
                                                    <option value="<?php echo $p['ciudad'] ; ?>"> <?php echo $p['ciudad'] ; ?></option>
                                                <?php endforeach ?>                                            
                                                    </select>
                                           </select>
                                        </div>

                                        <div class="form-group">
                                        <label for=""><span class="texto">*</span> Modalidad de Trabajo:</label>
                                        <select name="hvmodalidatrabajo" class="js-example-basic-single form-control" placeholder="Cliente" required  >
                                            <option value="">Seleccione</option>
                                            <option value="Trabajo en casa">Trabajo en casa</option>
                                            <option value="Oficina">Oficina</option>
                                            <option value="Alternancia">Alternancia</option>
                                            <option value="No definido">No definido</option>
                                            <option value="Sin informacion">Sin informaci&oacute;n</option>
                                            
                                        </select>

                                        <div class="form-group">
                                            <label for=""><span class="texto">*</span> Autoriza el Tratamiento de datos Personales:</label>
                                            <select name="hvautorizacion" class="js-example-basic-single form-control" placeholder="Cliente" required >
                                                <option>Seleccione</option>
                                                <option value="si" selected>Si</option>
                                                <option value="no">No</option>
                                            </select>
                                        </div>

                                        <div class="form-group">
                                            <div class="row">
                                                <div class="col-md-6">
                                                  <label for="" style="cursor:pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="TTB" >Es susceptible de encuestar:</label>
                                                  <select name="hvsusceptible" class="form-control" style="height:30px" required>
                                                      <option value="">Seleccione</option>
                                                      <option value="Si">Si</option>
                                                      <option value="No">No</option>
                                                  </select>
                                                </div>

                                                <div class="col-md-6">
                                                  
                                                 <label for="" style="cursor:pointer" data-bs-toggle="tooltip" data-bs-placement="top" title="TTB" >Indicador Satu: (%)</label>
                                                     <div class="input-group">
                                                        <input type="number" name="hvsatu"placeholder="85.5" step=".01" class="form-control" >
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
            <div class="card3">


              <div class="col-md-4" style="margin-bottom:10px">
               
                        <small>Datos de Cuenta</small>

                        <div class="form-group">
                            <label for=""><span class="texto">*</span> Cliente:</label>
                            <select name="client"  class="js-example-basic-single form-control" placeholder="Cliente" required >
                            <option value="">Seleccione</option>
                            <?php foreach($clientes as $p): ?>
                                <option value="<?php echo $p['cliente']; ?>"> <?php echo $p['cliente'] ; ?></option>
                            <?php endforeach ?>
                            </select>
                        </div>  

                          <div class="form-group">
                          <label for=""><span class="texto">*</span> Director:</label>
                          <select name="director[]" multiple="multiple" class="js-example-basic-single form-control" placeholder="Cliente" required >
                             <option value="">Seleccione</option>   
                             <?php foreach ($director_programa as $data): ?>
                                <?php if ($data['director_programa'] !== "0"  && $data['director_programa'] !== "Sin info" )  : ?>
                                    <option value="<?php echo $data['director_programa']  ?>"><?php echo $data['director_programa']  ?></option>
                                <?php endif ?>   
                             <?php endforeach ?>                                  
                            </select>
                          </div>

                          <div class="form-group">
                          <label for=""><span class="texto">*</span> Gerente:</label>
                              <select name="gerente[]" multiple="multiple"  class="js-example-basic-single form-control" placeholder="Cliente" required >
                                  <option value="">Seleccione</option>
                                  <?php foreach ($gerente as $data): ?>
                                    <option value="<?php echo $data['gerente_cuenta']  ?>"> <?php echo $data['gerente_cuenta']  ?></option>
                                    <?php endforeach ?> 
                              </select>
                          </div>

                          <div class="form-group">
                            <label for=""><span class="texto">*</span> PCRC:</label>
                            <select name="pcrc[]" multiple="multiple" class="js-example-basic-single form-control" placeholder="Cliente" required >
                            <option value="">Seleccione</option>
                            <?php foreach($pcrc as $p): ?>
                                <option value="<?php echo $p['pcrc'] ; ?>"> <?php echo $p['pcrc'] ; ?></option>
                            <?php endforeach ?>
                            </select>
                        </div>    
              </div>

              <div class="col-md-4" style="margin-bottom:10px">
               
               <small>Datos Laborales</small>

               <div class="form-group">
                   <label for=""><span class="texto">*</span> Rol:</label>
                   <input name="rol" class="form-control" placeholder="rol" required >     
               </div>

               <div class="form-group">
                   <label for=""><span class="texto">*</span> Antiguedad del Rol:</label>
                   <select name="antiguedadrol" class="js-example-basic-single form-control" placeholder="Cliente" required >
                         <option value="">Seleccione</option>
                         <option value="0-3 meses">0-3 meses</option>
                         <option value="4-6 meses">4-6 meses</option>
                         <option value="7 meses - 1 año">7 meses - 1 a&ntilde;o</option>
                         <option value="1 años - 3 años">1 a&ntilde;os - 3 a&ntilde;os</option>
                         <option value="4 años - 6 años">4 a&ntilde;os - 6 a&ntilde;os</option>
                         <option value="mayor a 6 años">mayor a 6 a&ntilde;os</option>
                   </select>
               </div>

               <div class="form-group">
                 <label for=""><span class="texto">*</span> Fecha de inicio como contacto:</label>
                 <input type="date" name="fechacontacto" class="form-control" required  >
              </div>  

                          
            <div class="form-group">
                       <div class="d-flex">
                         <label for="" class="label"><span class="texto">*</span> Afinidad: </label> <em class="fa fa-info-circle fa-1x" data-toggle="modal" data-target="#exampleModalCenter"></em>
                       </div>
                      <select name="afinidad" id="afinidad" class="form-control" required >
                          <option value="">Seleccionar</option>
                          <option value="relacion directa">Relaci&oacute;n Directa</option>
                          <option value="de interes">Relaci&oacute;n de Inter&eacute;s</option>
                      </select>
                  </div>  
 
            </div>

            <div class="col-md-4" style="margin-top:24px">


                  
                    <div class="form-group hidden" id="group">
                        <label for=""> Tipo:</label><em class="fa fa-info-circle fa-1x" data-toggle="modal" data-target="#exampleModalCenter2"></em>
                        <br/>
                        <select name="tipo" class="form-control tipo" placeholder="Tipo" >
                                    <option value="">Seleccione</option>
                                    <option value="Decisor">Decisor</option>
                                    <option value="No Decisor">No Decisor</option>
                        </select>
                    </div>

                        <div class="form-group hidden" id="group2">
                            <label for=""> Nivel:</label> <em class="fa fa-info-circle fa-1x" data-toggle="modal" data-target="#exampleModalCenter3"></em>
                            <br/>
                            <select name="nivel" class="form-control nivel" placeholder="Nivel" >
                                    <option value="">Seleccione</option>
                                    <option value="estrategico">Estrat&eacute;gico</option>
                                    <option value="operativo">Operativo</option>
                            </select>
                        </div> 
                  



                <div class="form-group">
                    <label for=""><span class="texto">*</span> Nombre del Jefe:</label>
                    <input type="text" name="nombrejefe" class="form-control" placeholder="Nombre jefe" >
                </div>  

                <div class="form-group">
                    <label for="">Cargo del Jefe:</label>
                    <input name="cargojefe" class="form-control" placeholder="Cargo jefe" >
                      
                </div>

                <div class="form-group">
                    <label for="">Trabajo Anterior:</label>
                    <input type="text" name="rolanterior" class="form-control" >
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
                    <select name="profesion" class="js-example-basic-single form-control" placeholder="Cliente">
                    <option value="">Seleccione</option>
                        <?php foreach($profesion as $p): ?>
                            <option value="<?php echo $p['hv_cursos'] ; ?>"> <?php echo $p['hv_cursos'] ; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>  

                <div class="form-group">
                    <label for="">Especializaci&oacute;n:</label>
                    <select name="especializacion" class="js-example-basic-single form-control" placeholder="Cliente">
                    <option value="">Seleccione</option>
                        <?php foreach($especializacion as $p): ?>
                            <option value="<?php echo $p['hv_cursos'] ; ?>"> <?php echo $p['hv_cursos'] ; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="">Maestr&iacute;a:</label>
                    <select name="maestria" class="js-example-basic-single form-control" placeholder="Cliente">
                    <option value="">Seleccione</option>
                        <?php foreach($maestria as $p): ?>
                            <option value="<?php echo $p['hv_cursos'] ; ?>"> <?php echo $p['hv_cursos'] ; ?></option>
                        <?php endforeach ?>
                    </select>
                </div> 
             
                

                <div class="form-group">
                    <label for="">Doctorado:</label>
                    <select name="doctorado" class="js-example-basic-single form-control" placeholder="Cliente">
                    <option value="">Seleccione</option>
                        <?php foreach($doctorado as $p): ?>
                            <option value="<?php echo $p['hv_cursos'] ; ?>"> <?php echo $p['hv_cursos'] ; ?></option>
                        <?php endforeach ?>
                    </select>
                </div>  


                <small style="color:#000"><strong>Los campos con <span style="color:red">*</span> son requeridos</strong></small> 
 
          </div>

          <div class="col-md-4" style="margin-top:24px">


          <div class="form-group" style="margin-top:20px">
                    <label for="">Estado:</label>
                    <select class="form-control estado" name="estado">
                      <option value="">Seleccione</option>
                      <option value="Activo" selected>Activo</option>
                      <option value="Inactivo">Inactivo</option>
                    </select>
                </div>  



          </div>
             <!-- find de datos laborales -->
        </div>

        <div class="botones">
            <a class="btn btn-success"  style="margin-top:10px" href="javascript:window.history.back()">
            <em class="fa fa-backward" aria-hidden="true"></em> Atr&aacute;s 
            </a>
            <button type="submit" class="btn btn-success" style="margin-top:10px">Registrar Informaci&oacute;n</button>

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
         <p><strong>Relacion Directa:</strong> Son tus contactos del d&iacute;a a d&iacute;a, con quienes defines estrategias para  el canal y/o  haces seguimiento a los indicadores operativos. </p>
         <p><strong>Relacion Inter&eacute;s:</strong> Son aquellos contactos que no tiene relaci&oacute;n con el contrato de Konecta, sin embargo tienen cargos estrat&eacute;gicos dentro de la compa&ntilde;&iacute;a por ejemplo Directores de Tecnolog&iacute;a, Presidente, Gerentes, Vicepresidentes.</p>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success boton" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalCenter2" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
        <h2 class="modal-title" id="exampleModalCenterTitle">¿Es un decisor del contrato?</h2>
         <p><strong>Decisor:</strong>  Es aquel contacto de 'nivel superior o muy superior' que puede tomar decisiones en referencia a la relaci&oacute;n comercial con Konecta, es altamente influyente en las decisiones del cliente corporativo y su percepci&oacute;n afecta de manera cr&iacute;tica la Imagen corporativa de Konecta.
                Este contacto es un 'alto influenciador' para el mantenimiento de los contratos con Konecta Colombia.
                cumplimiento de Objetivos a nivel Regional. </p>
                        <p><strong>Nota:</strong>Cada Cuenta deber&aacute; tener al menos 2 decisores. Estos contactos ser&aacute;n objeto de seguimiento de la Junta de Konecta.</p>
                        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success boton" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<div class="modal fade" id="exampleModalCenter3" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-md" role="document">
    <div class="modal-content">
      
      <div class="modal-body">
        <h2 class="modal-title" id="exampleModalCenterTitle"></h2>
         <p><strong>Estrat&eacute;gicos:</strong> Es aquel contacto de 'nivel superior' que aunque quiz&aacute;s no posee mucho conocimiento de los resultados Operativos, con este contacto se definen las estrategias de desarrollo del canal administrado por Konecta, se definen aspectos de la operación que impactan la rentabilidad de la cuenta. Este contacto es un alto influenciador para el mantenimiento y permanencia de los negocios con Konecta. </p>
         <p><strong>Operativos:</strong> Es aquel contacto de 'nivel intermedio o bajo', que esta al frente de los resultados /m&eacute;tricas de las Operaciones, es quien realiza los escalamientos de resultados a sus superiores y con estos los contactos estrat&eacute;gicos podr&iacute;an tomar decisiones o fijar posiciones con Konecta. A pesar de ser influenciadores de los negocios, no necesariamente &eacute;stos se definen por su autonom&iacute;a.</p>
        </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-success boton" data-dismiss="modal">OK</button>
      </div>
    </div>
  </div>
</div>

<script>
  $(document).ready(function() {
      $('.js-example-basic-single').select2({
          placeholder:"Seleccione",
          allowClear:true
      });
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
</script>
  <?php if(Yii::$app->session->hasFlash('info2')):  ?>
      <script>
          toastr.info('<?php echo Yii::$app->session->getFlash('info2') ?>')
      </script>
      <?php Yii::$app->session->close(); ?>
  <?php endif  ?>