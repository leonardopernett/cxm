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

$this->title = 'Control de Dimensionamiento';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

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

    $yearActual = date("Y");
    $mesActual = date("m");
    
    $varmeses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];


$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
//echo Html::jsFile("js/qa.js")    

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
<?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
<div class="capaPP" id="idcapapp" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><i class="fas fa-mouse-pointer" style="font-size: 15px; color: #C178G9;"></i> Seleccionar Mes </label>
        <?= $form->field($model, 'month')->dropDownList($varmeses, ['prompt' => 'Seleccione Mes...', 'id'=>"idMeses", 'onchange' => 'validames();' ])->label('') ?>

        <?= Html::submitButton(Yii::t('app', 'Registrar'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Registrar Dimensionamiento',
                                    'id'=>'modalButton1',
                                    'style' => 'color: #fff;    background-color: #5c74b8;',
                                    'onclick' => 'validacion();']) 
        ?>
      </div>
    </div>
  </div>
</div>
<hr>
<div class="capaUno" id="idcapaUno" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><i class="fas fa-plus-square" style="font-size: 15px; color: #C178G9;"></i> Ingresar Registros </label>
        <div class="row">
          <div class="col-md-6">          
             
            <?= $form->field($model, 'month')->textInput(['maxlength' => 200, 'id'=>'id_Mes', 'readonly'=>'readonly', 'title'=>'La cantidad de valoraciones por servicios o clientes'])->label('') ?>  

            <?php
                echo Html::tag('span', '<i class="fas fa-question-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                            'data-title' => Yii::t("app", ""),
                            'data-content' => 'La cantidad de valoraciones por servicios o clientes.',
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                ]);
            ?> 
            <label style="font-size: 13px;">Valoraci&oacute;n al mes: </label>
            <?= $form->field($model, 'cant_valor')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idcantvalor', 'placeholder'=>'Valoracion al mes'])->label('') ?> 

            <?php
                echo Html::tag('span', '<i class="fas fa-question-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                            'data-title' => Yii::t("app", ""),
                            'data-content' => 'Tiempo documentaciÃ³n, como sugerencia en promedio de 7 a 10 minutos de documentaciÃ³n.',
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                ]);
            ?> 
            <label style="font-size: 13px;">Tiempo adicional al muestreo(Segundos): </label>
            <?= $form->field($model, 'tiempoadicional')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idtiempoadicional', 'placeholder'=>'Tiempo adicional al muestreo(En Segundos)'])->label('') ?> 

            <?php
                echo Html::tag('span', '<i class="fas fa-question-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                            'data-title' => Yii::t("app", ""),
                            'data-content' => 'Se recomienda que este valor no sea mayor al 40%, ya que afecta la eficiencia de la linea. Insertar solo números',
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                ]);
            ?> 
            <label style="font-size: 13px;">% del tiempo que el t&eacute;cnico invierte en otras actividades: </label>
            <?= $form->field($model, 'otras_actividad')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idotrasa', 'placeholder'=>'% del tiempo que el tecnico invierte en otras actividades'])->label('') ?> 

            <?php
                echo Html::tag('span', '<i class="fas fa-question-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                            'data-title' => Yii::t("app", ""),
                            'data-content' => 'Se recomienda que este valor sea maximo 7%; de lo contrario podria alterar los resultados deseados. Los tecnicos de tu servicio por dias laborados.',
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                ]);
            ?> 
            <label style="font-size: 13px;">Ausentismos: </label>
            <div class="row">
              <div class="col-md-9">
                <?= $form->field($model, 'ausentismo')->textInput(['maxlength' => 200, 'id'=>'idausentismos', 'readonly'=>'readonly', 'placeholder'=>'Ausentismos'])->label('') ?> 
              </div>
              <div class="col-md-3">
                <div onclick="calcularausentismo();" class="btn btn-primary" style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000;" method='post' id="botones5" >
                  [ Calcular ]
                </div> 
              </div>
            </div>
          </div>
          <div class="col-md-6">
            <?= $form->field($model, 'year')->textInput(['maxlength' => 200, 'id'=>'id_Annio', 'readonly'=>'readonly'])->label('') ?>  

            <?php
                echo Html::tag('span', '<i class="fas fa-question-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                            'data-title' => Yii::t("app", ""),
                            'data-content' => 'DuraciÃ³n del servicio. Cuanto en promedio se demora escuchando las llamadas debe ser parecido al AHT del servicio del mes.',
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                ]);
            ?> 
            <label style="font-size: 13px;">Duraci&oacute;n llamadas muestreo (En Segundos): </label>
            <?= $form->field($model, 'tiempo_llamada')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idtiempollama', 'placeholder'=>'Duracion llamadas muestreo (En Segundos)'])->label('') ?>

            <?php
                echo Html::tag('span', '<i class="fas fa-question-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                            'data-title' => Yii::t("app", ""),
                            'data-content' => 'Cantidad de tecnicos actuales',
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                ]);
            ?> 
            <label style="font-size: 13px;">T&eacute;cnicos CX Actuales (incluye encargos y Oficiales): </label>
            <?= $form->field($model, 'actuales')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idactuales', 'placeholder'=>'Tecnicos CX Actuales (incluye encargos y Oficiales)'])->label('') ?>  

            <?php
                echo Html::tag('span', '<i class="fas fa-question-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                            'data-title' => Yii::t("app", ""),
                            'data-content' => 'Se recomienda que este valor sea 48 de los contrario podria alterar los resultados deseados. No deberiamos incluir los descansos y tiempo de almuerzos.',
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                ]);
            ?> 
            <label style="font-size: 13px;">Turno Promedio en la semana del t&eacute;cnico: </label>
            <?= $form->field($model, 'turno_promedio')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idturno', 'placeholder'=>'Turno Promedio en la semana del tecnico'])->label('') ?>  

            <?php
                echo Html::tag('span', '<i class="fas fa-question-circle" style="font-size: 18px; color: #C178G9;"></i>', [
                            'data-title' => Yii::t("app", ""),
                            'data-content' => 'Se recomienda que este valor sea 5% de lo contrario podria alterar los resultados deseados. Cuantos de tus tÃ©cnicos se encuentran en vacaciones del total de tÃ©cnicos.',
                            'data-toggle' => 'popover',
                            'style' => 'cursor:pointer;'
                ]);
            ?> 
            <label style="font-size: 13px;">Vacaciones, Permisos y Licencias: </label>
            <?= $form->field($model, 'vaca_permi_licen')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event);', 'id'=>'idvacas', 'placeholder'=>'Vacaciones, Permisos y Licencias'])->label('') ?>  
          </div>
        </div>
        
      </div>
    </div>
  </div>
</div>
<div class="capaDos" id="idcapaDos" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><i class="fas fa-calculator" style="font-size: 15px; color: #C178G9;"></i> Calcular Ausentismos </label>
        <div class="row">
          <div class="col-md-6">
            <label style="font-size: 13px;">Cantidad Tecnicos Incapacitados: </label>
            <input type="text" class="form-control" id="txtCantTecInca" onkeypress="return valida(event)">
          </div>
          <div class="col-md-6">
            <label style="font-size: 13px;">Cantidad Dias Incapacidad: </label>
            <input type="text" class="form-control" id="txtCantDiaInca" onkeypress="return valida(event)">
          </div>
        </div>
        <br>
        <div onclick="calculadora();" class="btn btn-primary" style="display:inline;" method='post' id="botones5" >
          Calcular
        </div>
      </div>
    </div>
  </div>
</div>
<?php $form->end() ?>
<script type="text/javascript">
  function calculadora(){
    var varcapaCanTecInca = document.getElementById("txtCantTecInca").value;
    var varcapaCantDiaInca = document.getElementById("txtCantDiaInca").value;
    var varcapaUno = document.getElementById("idcapaUno");
    var varcapaDos = document.getElementById("idcapaDos");
    var varcapapp = document.getElementById("idcapapp");

    // var varRta1 = varcapaCantDiaInca / varcapaCanTecInca;
    // var varRta2 = varRta1 * varcapaCanTecInca;
    var varRta3 = varcapaCantDiaInca * 9;
    var varRta4 = varRta3 / 4500;
    var varRta = Math.round(varRta4 * 100);

    document.getElementById("idausentismos").value = varRta;
    varcapaUno.style.display = 'inline';
    varcapaDos.style.display = 'none';
    varcapapp.style.display = 'inline';
  };


  function calcularausentismo(){
    var varcapaUno = document.getElementById("idcapaUno");
    var varcapaDos = document.getElementById("idcapaDos");
    var varcapapp = document.getElementById("idcapapp");
    document.getElementById("txtCantTecInca").value = 0;
    document.getElementById("txtCantDiaInca").value = 0;

    varcapaUno.style.display = 'none';
    varcapaDos.style.display = 'inline';
    varcapapp.style.display = 'none';
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
      swal.fire("¡¡¡ Advertencia !!!","El turno promedio no debe ser menor a las 48 hrs. a la semana, Debe de ingresar.","warning");
      return;
    }else

    if (varidcantvalor == "") {
      document.getElementById("idcantvalor").style.backgroundColor = '#f7b9b9';
      event.preventDefault();
      swal.fire("¡¡¡ Advertencia !!!","Ingresar datos al campo valoración al mes","warning");
      return;
    }else{
      document.getElementById("idcantvalor").style.backgroundColor = '#fff';

      if (varidtiempoadicional == "") {
        document.getElementById("idtiempoadicional").style.backgroundColor = '#f7b9b9';
        event.preventDefault();
        swal.fire("¡¡¡ Advertencia !!!","Ingresar datos al campo tiempo adicional al muestreo","warning");
        return;
      }else{
        document.getElementById("idtiempoadicional").style.backgroundColor = '#fff'

        if (varidotrasa == "") {
          document.getElementById("idotrasa").style.backgroundColor = '#f7b9b9';
          event.preventDefault();
          swal.fire("¡¡¡ Advertencia !!!","Ingresar datos al campo % del tiempo que el tecnico invierte en otras actividades","warning");
          return;
        }else{
          document.getElementById("idotrasa").style.backgroundColor = '#fff';

          if (varidausentismos == "") {
            document.getElementById("idausentismos").style.backgroundColor = '#f7b9b9';
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Ingresar datos al campo ausentismos","warning");
            return;
          }else{
            document.getElementById("idausentismos").style.backgroundColor = '#fff';

            if (varidtiempollama == "") {
              document.getElementById("idtiempollama").style.backgroundColor = '#f7b9b9';
              event.preventDefault();
              swal.fire("¡¡¡ Advertencia !!!","Ingresar datos a la campo valoración al mes","warning");
              return;
            }else{
              document.getElementById("idtiempollama").style.backgroundColor = '#fff';

              if (varidactuales == "") {
                document.getElementById("idactuales").style.backgroundColor = '#f7b9b9';
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","Ingresar datos a la campo valoración al mes","warning");
                return;
              }else{
                document.getElementById("idactuales").style.backgroundColor = '#fff';

                if (varidturno == "") {
                  document.getElementById("idturno").style.backgroundColor = '#f7b9b9';
                  event.preventDefault();
                  swal.fire("¡¡¡ Advertencia !!!","Ingresar datos a la campo valoración al mes","warning");
                  return;
                }else{
                  document.getElementById("idturno").style.backgroundColor = '#fff';

                  if (varidvacas == "") {
                    document.getElementById("idvacas").style.backgroundColor = '#f7b9b9';
                    event.preventDefault();
                    swal.fire("¡¡¡ Advertencia !!!","Ingresar datos a la campo valoración al mes","warning");
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

  function validames(){
    var varidMeses = document.getElementById("idMeses").value;
    var varMes = "<?php echo $mesActual; ?>";
    var varcapaUno = document.getElementById("idcapaUno");

    if (varidMeses < varMes) {
      varcapaUno.style.display = 'none';
      event.preventDefault();
      swal.fire("¡¡¡ Advertencia !!!","El mes seleccionado no puede ser menor o igual al mes actual, por favor seleccionar otro mes.","warning");
      return;
    }else{
      varcapaUno.style.display = 'inline';
      varmonth = null;
      if (varidMeses == '01') {
        varmonth = "Enero";
      }
      if (varidMeses == '02') {
        varmonth = "Febrero";
      }
      if (varidMeses == '03') {
        varmonth = "Marzo";
      }
      if (varidMeses == '04') {
        varmonth = "Abril";
      }
      if (varidMeses == '05') {
        varmonth = "Mayo";
      }
      if (varidMeses == '06') {
        varmonth = "Junio";
      }
      if (varidMeses == '07') {
        varmonth = "Julio";
      }
      if (varidMeses == '08') {
        varmonth = "Agosto";
      }
      if (varidMeses == '09') {
        varmonth = "Septiembre";
      }
      if (varidMeses == '10') {
        varmonth = "Octubre";
      }
      if (varidMeses == '11') {
        varmonth = "Noviembre";
      }
      if (varidMeses == '12') {
        varmonth = "Diciembre";
      }
      document.getElementById("id_Mes").value = varmonth;
      document.getElementById("id_Annio").value = "<?php echo $yearActual; ?>";
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