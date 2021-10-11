
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
    use yii\db\Query;
?>


<style>
      .select2-container--default .select2-selection--multiple .select2-selection__choice{
          font-size:10px;
      }
     /*  .select2-container--default .select2-selection--multiple .select2-selection__choice__remove{
        color: #fff !important;
      }

      .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:hover{
          background:#4298b4 !important;
          border: 1px solid #fff !important;
          color:#fff !important;
      }
      .select2-container--default .select2-selection--multiple .select2-selection__choice__remove:active{
        background:#4298b4 !important;
          border: 1px solid #fff !important;
          color:#fff !important;
      } */


          .card2{
            padding: 10px;
                  box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                  -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                  -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                  border-radius: 5px;    
                  font-family: "Nunito";
                  font-size: 150%;    
                  text-align: left;    
                  height:320px;   
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

        .group {
          display:flex;
        }

        h3 {
          text-align:center;
        }

   

        .hb{
          height:100px !important;
          overflow-y:scroll;
          height:20vh;
        }


      .botones {
        text-align:center;
        margin-top:15px;

      }
      .botones .btn {
        font-size:18px;
      }

      small{
        color:#000;
        font-size:12px;
        }
</style>


<!-- <link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  > -->
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

<!-- sweet alert -->

<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.all.min.js"></script>

<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 


<!-- sweet alert -->

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.min.css" integrity="sha512-bkB9w//jjNUnYbUpATZQCJu2khobZXvLP5GZ8jhltg7P/dghIrTaSJ7B/zdlBUT0W/LXGZ7FfCIqNvXjWKqCYA==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/standalone/selectize.min.js" integrity="sha512-pF+DNRwavWMukUv/LyzDyDMn8U2uvqYQdJN0Zvilr6DDo/56xPDZdDoyPDYZRSL4aOKO/FGKXTpzDyQJ8je8Qw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

<script src="https://unpkg.com/@yaireo/tagify"></script>
<script src="https://unpkg.com/@yaireo/tagify/dist/tagify.polyfills.min.js"></script>
<link href="https://unpkg.com/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.css" integrity="sha512-85w5tjZHguXpvARsBrIg9NWdNy5UBK16rAL8VWgnWXK2vMtcRKCBsHWSUbmMu0qHfXW2FVUDiWr6crA+IFdd1A==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/css/selectize.bootstrap4.min.css" integrity="sha512-MMojOrCQrqLg4Iarid2YMYyZ7pzjPeXKRvhW9nZqLo6kPBBTuvNET9DBVWptAo/Q20Fy11EIHM5ig4WlIrJfQw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/selectize.js/0.13.3/js/selectize.min.js" integrity="sha512-JiDSvppkBtWM1f9nPRajthdgTCZV3wtyngKUqVHlAs0d5q72n5zpM3QMOLmuNws2vkYmmLn4r1KfnPzgC/73Mw==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


<div class="container">
   <div class="row">
     <div class="col-md-12">
     <ul class="breadcrumb">

          <li class="">
          <a href="<?php echo Url::to(['/'])  ?>" > Inicio </a>
          </li>
          
          <li class="">
            <a href="<?php echo Url::to(['hvinfopersonal/index'])  ?>" > Hoja de vida </a>
          </li>

          <li class="">
            <a href="<?php echo Url::to(['hvinfopersonal/detalle/'.$usuario['idhvinforpersonal']])  ?>" > Editar registro </a>
          </li>
          
          <li class="active">
            Informaci&oacute;n Complementaria
          </li>
        </ul>


       <h3>Informaci&oacute;n Complementaria</h3>
       <?php $form = ActiveForm::begin(['action'=>['/hvinfopersonal/actualizar'] ,'method'=>'post' ]);  ?>
           <div class="row" style="margin-top:10px">
             <div class="card2">
                <div class="col-md-6">
                    <small>Informacion complementaria</small>
                    <input type="hidden" name="id" value="<?php echo $usuario['idhvinforpersonal'] ?>">
                    <div class="form-group">
                        <label for=""><span class="texto">*</span> Estado Civil:</label>
                        <select name="estadocivil" class="js-example-basic-single form-control" value="<?php echo $usuario['estadocivil'] ?>" class="js-example-basic-single form-control" placeholder="Cliente" >
                          <option value="">Seleccione</option>


                          <option value="Soltero/a" <?php if($usuario['estadocivil'] === "Soltero/a") echo 'selected' ?> >Soltero/a</option>
                          <option value="Casado/a" <?php if($usuario['estadocivil'] === "Casado/a") echo 'selected' ?> >Casado/a</option>
                          <option value="Separado/a" <?php if($usuario['estadocivil'] === "Separado/a") echo 'selected' ?> >Separado/a</option>
                          <option value="Divorciado/a" <?php if($usuario['estadocivil'] === "Divorciado/a") echo 'selected' ?> >Divorciado/a</option>
                          <option value="Viudo/a" <?php if($usuario['estadocivil'] === "Viudo/a") echo 'selected' ?> >Viudo/a</option>
                          <option value="Union libre o union de hecho" <?php if($usuario['estadocivil'] === "Union libre o union de hecho") echo 'selected' ?> >Uni&oacute;n libre o uni&oacute;n de hecho</option>
                          <option value="Sin informacion" <?php if($usuario['estadocivil'] === "Sin informacion") echo 'selected' ?> >Sin informaci&oacute;n</option>

                        </select>
                    </div>

                    <div class="form-group">
                      <label for=""><span class="texto">*</span>Numero de Hijos</label>
                      <select name="numerohijos" value="<?= $usuario['numerohijos'] ?>" class="js-example-basic-single form-control" >
                        <option value="">Seleccione</option>
                        <option value="1" <?php if($usuario['numerohijos'] == "1" ) echo 'selected' ?> >1</option>
                        <option value="2" <?php if($usuario['numerohijos'] == "2" ) echo 'selected' ?> >2</option>
                        <option value="3" <?php if($usuario['numerohijos'] == "3" ) echo 'selected' ?> >3</option>
                        <option value="4" <?php if($usuario['numerohijos'] == "4" ) echo 'selected' ?> >4</option>
                        <option value="5" <?php if($usuario['numerohijos'] == "5" ) echo 'selected' ?> >5</option>
                        <option value="No tienes hijos" <?php if($usuario['numerohijos'] == "No tienes hijos" ) echo 'selected' ?> >No tiene hijos</option>
                      </select>
                

                    </div>

                    <div class="form-group">
                      <label for=""><span class="texto"></span>Nombre de los hijos</label>
                      <div class="group">
                        
                        <input placeholder="" name="nombre" value="<?= $hijos['nombre'] ?>"  class="form-control hb"> 

                      </div>
                      <small><b>Escriba los nombres de los hijos separado con  comas <b>( , )</b> en caso tal tenga</b></small>
                    </div>       

                </div>

                <div class="col-md-6" style="margin-top:20px">
                  <div class="form-group">
                          <label for=""><span class="texto">*</span> Dominancia Cerebral:</label>
                          <select name="dominancia" value="<?= print_r($dominanciapreselect)?>"  placeholder="Seleccione" class="form-control js-example-basic-single" >
                            <option value="">Seleccione</option>
                            <?php foreach($dominancias as $p): ?>
                                <option value="<?php echo $p['dominancia'] ; ?>" <?php if( $p['dominancia'] === $data['dominancia']) echo 'selected' ?>    > <?php echo $p['dominancia'] ; ?></option>
                            <?php endforeach ?>                     
                          </select>
                      </div>

                  <div class="form-group">
                          <label for=""><span class="texto">*</span> Estilo Social:</label>

                          <select name="estilosocial" class="js-example-basic-single form-control" value="<?php echo $usuario['estilosocial'] ?>" class="js-example-basic-single form-control" placeholder="Cliente" >
                            <option value="">Seleccione</option>
                            <option value="Analitico" <?php if($usuario['estilosocial'] === "Analitico") echo 'selected' ?> >Anal&iacute;tico</option>
                            <option value="Emprendedor" <?php if($usuario['estilosocial'] === "Emprendedor") echo 'selected' ?> >Emprendedor</option>
                            <option value="Afable" <?php if($usuario['estilosocial'] === "Afable") echo 'selected' ?> >Afable</option>
                            <option value="Expresivo" <?php if($usuario['estilosocial'] === "Expresivo") echo 'selected' ?> >Expresivo</option>
                            <option value="Sin información" <?php if($usuario['estilosocial'] === "Sin información") echo 'selected' ?> >Sin informaci&oacute;n</option>

                          </select>
                      </div>
                 
                  <div class="form-group">
                  <label for=""><span class="texto">*</span> Hobbies:</label>

               <!--  -->

                  <select name="hobbies[]" multiple="multiple" value="<?= print_r($hobbieSelected)?>"  placeholder="Seleccione" class="form-control js-example-basic-single"  >
                            <option value="">Seleccione</option>
                        
                            <?php foreach ( $hobbies as $hobbie): ?>
                              <option value="<?= $hobbie['text'] ?>"  
                                  <?php foreach ( explode(",",$data['hobbies']) as $hobb): ?>
                                      <?php if($hobbie['text'] == $hobb) echo 'selected' ?>
                                  <?php endforeach ?>

                                > <?php echo $hobbie['text'] ?> </option> 
                              
                          <?php endforeach ?>
                  </select>

                  

                  </div>
                      <div class="form-group">
                          <label for=""><span class="texto">*</span> Intereses / Gustos:</label>
                            <select name="gustos[]" multiple="multiple" class="form-control js-example-basic-single"  >
                              <option value="">Seleccione</option>
                               <?php foreach ($gustos as $gusto) : ?>
                                <option value="<?= $gusto['text'] ?>"
                                
                                <?php foreach (explode(",", $data['gustos']) as $gust) : ?>
                                     <?php if($gusto['text']==$gust) echo "selected"  ?>
                                  <?php endforeach ?>
                                
                                >
                                     <?php echo $gusto['text'] ?>
                                </option>
                               <?php endforeach ?>
                            </select>
              
                      </div>
                       
                  </div>

                  

                </div>

                  <div class="botones">
                     <button class="btn btn-success" type="submit">Agregar Informaci&oacute;n Complementaria</button> 
                  </div>  
             </div>       
           </div>
       <?php ActiveForm::end()  ?>
     </div>
   </div>
</div>


<?php if (Yii::$app->session->hasFlash('actualizar') ): ?>
 
  <script>
      toastr.info('<?php echo Yii::$app->session->getFlash('actualizar') ?>')
  </script>

 <?php endif ?>
<script>
      $(document).ready(function(){
          $('.js-example-basic-single').select2()
          $('[name=tags]').tagify();
          $("#select-state").selectize({
            maxItems: 3,
          });
      })
      
      fetch('<?= Url::to(['hvinfopersonal/hobbies']) ?>')
          .then(res  => res.json())
          .then(data => console.log(data))
          .catch(err => console.log(err))

      var input    = document.querySelector('.hb')
      var selector = document.querySelector('.ss')

      new Tagify(input,{originalInputValueFormat: valor => valor.map(item => item.value)})
    
      selector.addEventListener('change',(e)=>{
          console.log(e.target.value)
      })
</script>