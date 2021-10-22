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

$this->title = 'Actualizar Control de Dimensionamiento';
$this->params['breadcrumbs'][] = $this->title;

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capaPP" style="display: inline;">
  <div class="row">
    <div class="col-md-3">
            <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Actualizar Dimensionamiento: </label>
                        <?= Html::submitButton(Yii::t('app', 'Actualizar'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Actualizar Dimensionamiento',
                                    'onclick' => 'validacion();']) 
                        ?>
                        
                    </div>
                </div>
            </div>            
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Regresar: </label>
                        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #707372',
                                'data-toggle' => 'tooltip',
                                'title' => 'Regresar']) 
                        ?>
                    </div>
                </div>
            </div>  
            <br>
        </div>
        <div class="col-md-9">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-plus-square" style="font-size: 15px; color: #9F9AE1;"></em> Ingresar Registros </label>
                
                <?php
                    foreach ($varlistaresult as $key => $value) {
                        
                ?>                    
                    <div class="row">
                      <div class="col-md-6">  
                        <label style="font-size: 13px;">Mes: </label>          
                        <?= $form->field($model, 'month')->textInput(['maxlength' => 200, 'id'=>'id_Mes', 'readonly'=>'readonly', 'value' => $value['month']])->label('') ?>  

                        <label style="font-size: 13px;">Valoracion al mes: </label>
                        <?= $form->field($model, 'cant_valor')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idcantvalor', 'placeholder'=>'Valoracion al mes',  'value' => $value['cant_valor']])->label('') ?> 

                        <label style="font-size: 13px;">Tiempo adicional al muestreo(Segundos): </label>
                        <?= $form->field($model, 'tiempoadicional')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idtiempoadicional', 'placeholder'=>'Tiempo adicional al muestreo(En Segundos)',  'value' => $value['tiempoadicional']])->label('') ?> 

                        <label style="font-size: 13px;">% del tiempo que el tecnico invierte en otras actividades: </label>
                        <?= $form->field($model, 'otras_actividad')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idotrasa', 'placeholder'=>'% del tiempo que el tecnico invierte en otras actividades',  'value' => $value['otras_actividad']])->label('') ?> 

                         <label style="font-size: 13px;">Ausentismos: </label>
                        <div class="row">
                          <div class="col-md-9">
                            <?= $form->field($model, 'ausentismo')->textInput(['maxlength' => 200, 'id'=>'idausentismos', 'readonly'=>'readonly', 'placeholder'=>'Ausentismos',  'value' => $value['ausentismo']])->label('') ?> 
                          </div>
                          <div class="col-md-3">
                            <div onclick="calcularausentismo();" class="btn btn-primary" style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000;" method='post' id="botones5" >
                              [ Calcular ]
                            </div> 
                          </div>
                        </div>
                      </div>
                      <div class="col-md-6">
                        <label style="font-size: 13px;">Año: </label>
                        <?= $form->field($model, 'year')->textInput(['maxlength' => 200, 'id'=>'id_Annio', 'readonly'=>'readonly', 'value' => $value['year']])->label('') ?>  

                        <label style="font-size: 13px;">Duracion llamadas muestreo (En Segundos): </label>
                        <?= $form->field($model, 'tiempo_llamada')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idtiempollama', 'placeholder'=>'Duracion llamadas muestreo (En Segundos)',  'value' => $value['tiempo_llamada']])->label('') ?>

                        <label style="font-size: 13px;">Tecnicos CX Actuales (incluye encargos y Oficiales): </label>
                        <?= $form->field($model, 'actuales')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idactuales', 'placeholder'=>'Tecnicos CX Actuales (incluye encargos y Oficiales)',  'value' => $value['actuales']])->label('') ?>  

                        <label style="font-size: 13px;">Turno Promedio en la semana del tecnico: </label>
                        <?= $form->field($model, 'turno_promedio')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idturno', 'placeholder'=>'Turno Promedio en la semana del tecnico',  'value' => $value['turno_promedio']])->label('') ?>  

                        <label style="font-size: 13px;">Vacaciones, Permisos y Licencias: </label>
                        <?= $form->field($model, 'vaca_permi_licen')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idvacas', 'placeholder'=>'Vacaciones, Permisos y Licencias',  'value' => $value['vaca_permi_licen']])->label('') ?>  
                      </div>
                    </div>
                <?php
                    }
                ?>
                        
            </div>
            <br>
            <div class="capaDos" id="idcapaDos" style="display: none;">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-calculator" style="font-size: 15px; color: #9F9AE1;"></em> Calcular Ausentismos </label>
                            <div class="row">
                                <div class="col-md-6">
                                  <label style="font-size: 13px;">Cantidad Tecnicos Incapacitados: </label>
                                  <input type="text" class="form-control" id="txtCantTecInca" onkeypress="return valida(event)">
                                  <br>
                                    <div onclick="calculadora();" class="btn btn-primary" style="display:inline;" method='post' id="botones5" >
                                        Calcular
                                    </div>
                                </div>
                                <div class="col-md-6">
                                  <label style="font-size: 13px;">Cantidad Dias Incapacidad: </label>
                                  <input type="text" class="form-control" id="txtCantDiaInca" onkeypress="return valida(event)">
                                </div>
                                
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
              
        <?php $form->end() ?> 
  </div>  
</div>
<hr>
<script type="text/javascript">
  function calculadora(){
    var varcapaCanTecInca = document.getElementById("txtCantTecInca").value;
    var varcapaCantDiaInca = document.getElementById("txtCantDiaInca").value;
    var varcapaDos = document.getElementById("idcapaDos");

    // var varRta1 = varcapaCantDiaInca / varcapaCanTecInca;
    // var varRta2 = varRta1 * varcapaCanTecInca;
    var varRta3 = varcapaCantDiaInca * 9;
    var varRta4 = varRta3 / 4500;
    var varRta = Math.round(varRta4 * 100);

    document.getElementById("idausentismos").value = varRta;
    
    varcapaDos.style.display = 'none';
  };


  function calcularausentismo(){
    var varcapaDos = document.getElementById("idcapaDos");
    document.getElementById("txtCantTecInca").value = 0;
    document.getElementById("txtCantDiaInca").value = 0;

    
    varcapaDos.style.display = 'inline';
  };

  function validacion(){
    var varidcantvalor = document.getElementById("idcantvalor").value;
    var varidtiempoadicional = document.getElementById("idtiempoadicional").value;
    var varidotrasa = document.getElementById("idotrasa").value;
    var varidausentismos = document.getElementById("idausentismos").value;
    var varidtiempollama = document.getElementById("idtiempollama").value;
    var varidactuales = document.getElementById("idactuales").value;
    var varidturno = document.getElementById("idturno").value;
    var varidvacas = document.getElementById("idvacas").value;

    if (varidturno < '48') {
      document.getElementById("idturno").style.backgroundColor = '#f7b9b9';
      event.preventDefault();
      swal.fire("Advertencia","El turno promedio no debe ser menor a las 48 hrs. a la semana, Debe de ingresar.","warning");
      return;
    }else

    if (varidcantvalor == "") {
      document.getElementById("idcantvalor").style.backgroundColor = '#f7b9b9';
      event.preventDefault();
      swal.fire("Advertencia","Ingresar datos al campo valoración al mes","warning");
      return;
    }else{
      document.getElementById("idcantvalor").style.backgroundColor = '#fff';

      if (varidtiempoadicional == "") {
        document.getElementById("idtiempoadicional").style.backgroundColor = '#f7b9b9';
        event.preventDefault();
        swal.fire("Advertencia","Ingresar datos al campo tiempo adicional al muestreo","warning");
        return;
      }else{
        document.getElementById("idtiempoadicional").style.backgroundColor = '#fff'

        if (varidotrasa == "") {
          document.getElementById("idotrasa").style.backgroundColor = '#f7b9b9';
          event.preventDefault();
          swal.fire("Advertencia","Ingresar datos al campo % del tiempo que el tecnico invierte en otras actividades","warning");
          return;
        }else{
          document.getElementById("idotrasa").style.backgroundColor = '#fff';

          if (varidausentismos == "") {
            document.getElementById("idausentismos").style.backgroundColor = '#f7b9b9';
            event.preventDefault();
            swal.fire("Advertencia","Ingresar datos al campo ausentismos","warning");
            return;
          }else{
            document.getElementById("idausentismos").style.backgroundColor = '#fff';

            if (varidtiempollama == "") {
              document.getElementById("idtiempollama").style.backgroundColor = '#f7b9b9';
              event.preventDefault();
              swal.fire("Advertencia","Ingresar datos a la campo valoración al mes","warning");
              return;
            }else{
              document.getElementById("idtiempollama").style.backgroundColor = '#fff';

              if (varidactuales == "") {
                document.getElementById("idactuales").style.backgroundColor = '#f7b9b9';
                event.preventDefault();
                swal.fire("Advertencia","Ingresar datos a la campo valoración al mes","warning");
                return;
              }else{
                document.getElementById("idactuales").style.backgroundColor = '#fff';

                if (varidturno == "") {
                  document.getElementById("idturno").style.backgroundColor = '#f7b9b9';
                  event.preventDefault();
                  swal.fire("Advertencia","Ingresar datos a la campo valoración al mes","warning");
                  return;
                }else{
                  document.getElementById("idturno").style.backgroundColor = '#fff';

                  if (varidvacas == "") {
                    document.getElementById("idvacas").style.backgroundColor = '#f7b9b9';
                    event.preventDefault();
                    swal.fire("Advertencia","Ingresar datos a la campo valoración al mes","warning");
                    return;
                  }else{
                    document.getElementById("idvacas").style.backgroundColor = '#fff';
                  }
                }
              }
            }
          }
        }
      }
    }
  };

  function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
            return true;
        }
            
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
  };
</script>