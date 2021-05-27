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
use yii\bootstrap\modal;


    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $fechaactual = date("Y-m-d");

    $varMes = date("n");
    $varYear = date("Y");
?>
<br>
<div class="formularios-form">
  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div id="Meses" style="display: inline">
      <select class ='form-control' id="MesesId" onchange="selecciones();">
        <option value="" disabled selected>Elegir el Mes a Actualizar</option>
        <option value="1" style="display: inline" id="1">Enero</option>
        <option value="2" style="display: inline" id="2">Febrero</option>
        <option value="3" style="display: inline" id="3">Marzo</option>
        <option value="4" style="display: inline" id="4">Abril</option>
        <option value="5" style="display: inline" id="5">Mayo</option>
        <option value="6" style="display: inline" id="6">Junio</option>
        <option value="7" style="display: inline" id="7">Julio</option>
        <option value="8" style="display: inline" id="8">Agosto</option>
        <option value="9" style="display: inline" id="9">Septiembre</option>
        <option value="10" style="display: inline" id="10">Octubre</option>
        <option value="11" style="display: inline" id="11">Noviembre</option>
        <option value="12" style="display: inline" id="12">Diciembre</option>
      </select>
    </div>

    <div id="BotonId"  style="display: none;">  
      <br>
        <div onclick="buscarMes();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Buscar
        </div>
        <input value="<?php echo $sessiones; ?>" id="txtusua_idE"  class="invisible">
    </div>

   <div id="globalId" style="display: none">
      <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <thead>
        </thead>
        <tbody>
          <tr>
            <td colspan="2">
              <label for="txtMesess">Mes</label>
              <input type="text" class="form-control" id="txtMesess" style="width:250px;" readonly="readonly">
              <input type="text" class="form-control hidden" id="txtIdDemension" style="width:250px;" readonly="readonly">
            </td>
          </tr>
          <tr>          
            <td>
              <label for="txtcant_valor">Valoración al mes</label>
              <input type="text" class="form-control" id="txtcant_valor" style="width:250px;" onkeypress="return valida(event)">           
            </td>
            <td>
              <label for="txttiempo_llamada">Duración llamadas Muestreo (Segundos)</label>
              <input type="text" class="form-control" id="txttiempo_llamada" style="width:250px;" onkeypress="return valida(event)">            
            </td>
          </tr>
          <tr>
            <td>
              <label for="txttiempoaidional">Tiempo adicional al muestreo (Seg)</label>
              <input type="text" class="form-control" id="txttiempoaidional" style="width:250px;" onkeypress="return valida(event)">            
            </td>
            <td>
              <label for="txtactuales">Tecnicos Cx Actuales (incluye encargos y Oficiales)</label>
              <input type="text" class="form-control" id="txtactuales" style="width:250px;" onkeypress="return valida(event)">            
            </td>
          </tr>
          <tr>
            <td>
              <label for="txtotras_actividad">%  del tiempo de tecnico que invierte a en otras actividades</label>
              <input type="text" class="form-control" id="txtotras_actividad" style="width:250px;" onkeypress="return valida(event)">
            </td>
            <td>
              <label for="txtturno_promedio">Turno Promedio en la semana del tecnico</label>
              <input type="text" class="form-control" id="txtturno_promedio" style="width:250px;" onkeypress="return valida(event)">
            </td>
          </tr>
          <tr>
            <td>
              <label for="txtausentismo">Ausentismo</label>
              <input type="text" class="form-control" id="txtausentismo" style="width:250px;" onkeypress="return valida(event)">
            </td>
            <td>
              <label for="txtvaca_permi_licen">Vacaciones, Permisos y Licencias</label>
              <input type="text" class="form-control" id="txtvaca_permi_licen" style="width:250px;" onkeypress="return valida(event)">
            </td>
          </tr>
        </tbody>
      </table>
      &nbsp;       
        <div onclick="generated();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Actualizacion
        </div> 
        &nbsp;
        <div onclick="calcularausentismo2();" class="btn btn-primary" style="display:inline;" method='post' id="botones5" >
          Calcular Ausentismo
        </div> 
    </div>

    <div id="calcularID2" style="display: none">
      <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
          <thead>
          </thead>
          <tbody>
            <tr>
              <td>
                <label for="txtCantTecInca2">Cantidad Tecnicos Incapacitados</label>
                <input type="text" class="form-control" id="txtCantTecInca2" style="width:250px;" onkeypress="return valida(event)">  
              </td>
              <td>
                <label for="txtCantDiaInca2">Cantidad Dias Incapacidad</label>
                <input type="text" class="form-control" id="txtCantDiaInca2" style="width:250px;" onkeypress="return valida(event)"> 
              </td>
            </tr>
          </tbody>
      </table>
      <div onclick="calculadora2();" class="btn btn-primary" style="display:inline;" method='post' id="botones5" >
        Calcular
      </div>   
    </div>
  <?php $form->end() ?>
</div>

<script type="text/javascript">
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

  var varcapaMes = document.getElementById("Meses");
  var varcapaglobalId = document.getElementById("globalId");
  var varcapaBotonId = document.getElementById("BotonId");

  document.getElementById("txtausentismo").value = "0";

  var varcapaCalcular = document.getElementById("calcularID2");


  function selecciones(){
    var meSes = parseInt(document.getElementById("MesesId").value);
    var numMes = parseInt("<?php echo $varMes; ?>");

    //Number(meSes);
    console.log(meSes);
    //Number(numMes);
    console.log(numMes);

    if (meSes < numMes) {
        event.preventDefault();
          varcapaBotonId.style.display = 'none';
          swal.fire("!!! Advertencia !!!","No es posible seleccionar mes pasado al actual.","warning");
        return; 
    }else{ 
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas2",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas2",
        data : {
          txtmeSes : meSes,
        },
        success : function(response){
          var Rta= JSON.parse(response);
          // console.log(numRta);
          document.getElementById("txtcant_valor").value = Rta.cant_valor;
          document.getElementById("txttiempo_llamada").value = Rta.tiempo_llamada;
          document.getElementById("txttiempoaidional").value = Rta.tiempoadicional;
          document.getElementById("txtactuales").value = Rta.actuales;
          document.getElementById("txtotras_actividad").value = Rta.otras_actividad;
          document.getElementById("txtturno_promedio").value = Rta.turno_promedio;
          document.getElementById("txtausentismo").value = Rta.ausentismo;
          document.getElementById("txtvaca_permi_licen").value = Rta.vaca_permi_licen;
          document.getElementById("txtMesess").value = Rta.month;
          document.getElementById("txtIdDemension").value = Rta.iddimensionamiento;
          document.getElementById("botones1").click();
          // console.log("aaa",Rta);

        }
      });
    }
  };

  function buscarMes(){
    varcapaMes.style.display = 'none';
    varcapaBotonId.style.display = 'none';
    varcapaglobalId.style.display = 'inline';
  };

  function generated(){
    var varMeses = document.getElementById("txtMesess").value;
    var varIdDimension = document.getElementById("txtIdDemension").value;
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else{
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas3",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas3",
        data : {
          txtvarMeses : varMeses,
          txtIdDimension : varIdDimension,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
        },
        success : function(response){
          var Rta= JSON.parse(response);
          console.log(Rta);
            if (Rta == 0) {
                //$("#modal1").modal("hide");
                window.location.href="http://qa.allus.com.co/qa_managementv2/web/index.php/controldimensionamiento/index";
              }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
              }
        }
      })
    }
  };

  function calcularausentismo2(){
    document.getElementById("txtausentismo").value = "0";
    document.getElementById("txtCantTecInca2").value = "";
    document.getElementById("txtCantDiaInca2").value = "";
    varcapaCalcular.style.display = 'inline';

  };

  function calculadora2(){
    var varcapaCanTecInca = document.getElementById("txtCantTecInca2").value;
    var varcapaCantDiaInca = document.getElementById("txtCantDiaInca2").value;

    // var varRta1 = varcapaCantDiaInca / varcapaCanTecInca;
    // var varRta2 = varRta1 * varcapaCanTecInca;
    var varRta3 = varcapaCantDiaInca * 9;
    var varRta4 = varRta3 / 4500;
    var varRta = Math.round(varRta4 * 100);

    var varcapaAusentismo = document.getElementById("txtausentismo").value = varRta;
    varcapaCalcular.style.display = 'none';
  };

  $(document).ready(function(){
    $('[data-toggle="tooltip"]').tooltip();
    $('[data-toggle="tooltip2"]').tooltip();
    $('[data-toggle="tooltip3"]').tooltip();
    $('[data-toggle="tooltip4"]').tooltip();
    $('[data-toggle="tooltip5"]').tooltip();
    $('[data-toggle="tooltip6"]').tooltip();
    $('[data-toggle="tooltip7"]').tooltip();
    $('[data-toggle="tooltip8"]').tooltip();
  });
</script>