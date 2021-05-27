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
    $varyear = date("Y");

?>
<br>
<div class="formularios-form">
  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>  
 
    <div id="seleccion"  style="display: inline;">  
      <br>
      <div id="Meses" style="display: inline">
        <select class ='form-control' id="MesesId" onchange="seleccionar();">
          <option value="" disabled selected>Elegir el Mes a Crear Dimensionamiento</option>
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
    </div>

    <div id="Enero"  style="display: none;"> 
      <br>
        <!--<div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" > -->
            <!--Cerrar -->
        <!--</div> -->
        &nbsp;&nbsp; 
        <div onclick="generatedEnero();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar enero
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idE"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearE"  class="invisible">
        <input value="Enero" id="txtmonthE"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionE"  class="invisible"> 
        <input value="0" id="txtanuladoE"  class="invisible"> 

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Enero <?php echo $varyear; ?></h3></div>
        </div>
    </div>

    <div id="Febrero"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedFebrero();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Febrero
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idF"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearF"  class="invisible">
        <input value="Febrero" id="txtmonthF"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionF"  class="invisible"> 
        <input value="0" id="txtanuladoF"  class="invisible"> 

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Febrero <?php echo $varyear; ?></h3></div>
        </div>   
    </div>

    <div id="Marzo"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedMarzo();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Marzo
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idM"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearM"  class="invisible">
        <input value="Marzo" id="txtmonthM"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionM"  class="invisible"> 
        <input value="0" id="txtanuladoM"  class="invisible"> 

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Marzo <?php echo $varyear; ?></h3></div>
        </div>      
    </div>

    <div id="Abril"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedAbril();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Abril
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idA"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearA"  class="invisible">
        <input value="Abril" id="txtmonthA"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionA"  class="invisible"> 
        <input value="0" id="txtanuladoA"  class="invisible">  

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Abril <?php echo $varyear; ?></h3></div>
        </div>      
    </div>

    <div id="Mayo"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedMayo();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Mayo
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idMM"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearMM"  class="invisible">
        <input value="Mayo" id="txtmonthMM"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionMM"  class="invisible"> 
        <input value="0" id="txtanuladoMM"  class="invisible"> 

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Mayo <?php echo $varyear; ?></h3></div>
        </div>       
    </div>

    <div id="Junio"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedJunio();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Junio
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idJ"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearJ"  class="invisible">
        <input value="Junio" id="txtmonthJ"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionJ"  class="invisible"> 
        <input value="0" id="txtanuladoJ"  class="invisible">  

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Junio <?php echo $varyear; ?></h3></div>
        </div>     
    </div>

    <div id="Julio"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedJulio();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Julio
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idJJ"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearJJ"  class="invisible">
        <input value="Julio" id="txtmonthJJ"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionJJ"  class="invisible"> 
        <input value="0" id="txtanuladoJJ"  class="invisible"> 

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Julio <?php echo $varyear; ?></h3></div>
        </div>      
    </div>

    <div id="Agosto"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedAgosto();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Agosto
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idAA"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearAA"  class="invisible">
        <input value="Agosto" id="txtmonthAA"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionAA"  class="invisible"> 
        <input value="0" id="txtanuladoAA"  class="invisible">  

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Agosto <?php echo $varyear; ?></h3></div>
        </div> 
    </div>
    <div id="Septiembre"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedSeptiembre();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Septiembre
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idS"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearS"  class="invisible">
        <input value="Septiembre" id="txtmonthS"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionS"  class="invisible"> 
        <input value="0" id="txtanuladoS"  class="invisible">   

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Septiembre <?php echo $varyear; ?></h3></div>
        </div>      
    </div>
    <div id="Octubre"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedOctubre();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Octubre
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idO"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearO"  class="invisible">
        <input value="Octubre" id="txtmonthO"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionO"  class="invisible"> 
        <input value="0" id="txtanuladoO"  class="invisible"> 

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Octubre <?php echo $varyear; ?></h3></div>
        </div>        
    </div>
    <div id="Noviembre"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedNoviembre();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Noviembre
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idN"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearN"  class="invisible">
        <input value="Noviembre" id="txtmonthN"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionN"  class="invisible"> 
        <input value="0" id="txtanuladoN"  class="invisible"> 

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Noviembre <?php echo $varyear; ?></h3></div>
        </div>        
    </div>
    <div id="Diciembre"  style="display: none;">  
      <br>
        <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones1" >
            Cerrar
        </div>
        &nbsp;&nbsp;       
        <div onclick="generatedDiciembre();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          Guardar Diciembre
        </div> 
        <input value="<?php echo $sessiones; ?>" id="txtusua_idD"  class="invisible">
        <input value="<?php echo $varyear; ?>" id="txtyearD"  class="invisible">
        <input value="Diciembre" id="txtmonthD"  class="invisible"> 
        <input value="<?php echo $fechaactual; ?>" id="txtfechacreacionD"  class="invisible"> 
        <input value="0" id="txtanuladoD"  class="invisible"> 

        <div class="panel panel-default">
          <div class="panel-heading" align="center"><h3>Diciembre <?php echo $varyear; ?></h3></div>
        </div>        
    </div>

    <div id="globalId2" style="display: none">
      <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <thead>
        </thead>
        <tbody>
          <tr>
            <td>
              <label for="txtcant_valor">Valoración al mes</label>
              <input type="text" class="form-control" id="txtcant_valor" style="width:250px;" onkeypress="return valida(event)" data-toggle="tooltip" title="La cantidad de valoraciones por servicios o clientes.">           
            </td>
            <td>
              <label for="txttiempo_llamada">Duración llamadas Muestreo (Segundos)</label>
              <input type="text" class="form-control" id="txttiempo_llamada" style="width:250px;" onkeypress="return valida(event)" data-toggle="tooltip2" title="Duración del servicio. Cuanto en promedio se demora escuchando las llamadas debe ser parecido al AHT del servicio del mes.">            
            </td>
          </tr>
          <tr>
            <td>
              <label for="txttiempoaidional">Tiempo adicional al muestreo (Seg)</label>
              <input type="text" class="form-control" id="txttiempoaidional" style="width:250px;" onkeypress="return valida(event)" data-toggle="tooltip3" title="Tiempo documentación, como sugerencia en promedio de 7 a 10 minutos de documentación.">            
            </td>
            <td>
              <label for="txtactuales">Tecnicos Cx Actuales (incluye encargos y Oficiales)</label>
              <input type="text" class="form-control" id="txtactuales" style="width:250px;" onkeypress="return valida(event)" data-toggle="tooltip4" title="Técnicos actuales.">            
            </td>
          </tr>
          <tr>
            <td>
              <label for="txtotras_actividad">%  del tiempo de tecnico que invierte a en otras actividades</label>
              <input type="text" class="form-control" id="txtotras_actividad" style="width:250px;" onkeypress="return valida(event)" data-toggle="tooltip5" title="Se recomienda que este valor no sea mayor al 40%, ya que afecta la eficiencia de la linea.">
            </td>
            <td>
              <label for="txtturno_promedio">Turno Promedio en la semana del tecnico</label>
              <input type="text" class="form-control" id="txtturno_promedio" style="width:250px;" onkeypress="return valida(event)" data-toggle="tooltip6" title="Se recomienda que este valor sea 45 de los contrario podria alterar los resultados deseados. No deberiamos incluir los descansos y tampo almuerzos.">
            </td>
          </tr>
          <tr>
            <td>
              <label for="txtausentismo">Ausentismo</label>
              <input type="text" class="form-control" id="txtausentismo" style="width:250px;" readonly="readonly" data-toggle="tooltip7" title="Se recomienda que este valor sea maximo 7%; de lo contrario podria alterar los resultados deseados. Los técnicos de tu servicio por días laborados.">
            </td>
            <td>
              <label for="txtvaca_permi_licen">Vacaciones, Permisos y Licencias</label>
              <input type="text" class="form-control" id="txtvaca_permi_licen" style="width:250px;" onkeypress="return valida(event)" data-toggle="tooltip8" title="Se recomienda que este valor sea 5% de lo contrario podria alterar los resultados deseados. Cuantos de tus técnicos se encuentran en vacaciones del total de técnicos.">
            </td>
          </tr>
        </tbody>
      </table> 
        <div onclick="calcularausentismo();" class="btn btn-primary" style="display:inline;" method='post' id="botones5" >
          Calcular Ausentismo
        </div> 
    </div>
	<br>
	<br>
    <div id="calcularID" style="display: none">
      <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
          <thead>
          </thead>
          <tbody>
            <tr>
              <td>
                <label for="txtCantTecInca">Cantidad Tecnicos Incapacitados</label>
                <input type="text" class="form-control" id="txtCantTecInca" style="width:250px;" onkeypress="return valida(event)">  
              </td>
              <td>
                <label for="txtCantDiaInca">Cantidad Dias Incapacidad</label>
                <input type="text" class="form-control" id="txtCantDiaInca" style="width:250px;" onkeypress="return valida(event)"> 
              </td>
            </tr>
          </tbody>
      </table>
      <div onclick="calculadora();" class="btn btn-primary" style="display:inline;" method='post' id="botones5" >
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

  function regresar(){
    $("#modal1").modal("hide");
  };
  var varcapaCalcular = document.getElementById("calcularID");

  document.getElementById("txtausentismo").value = "0";

  var varcapaEnero = document.getElementById("Enero");
  var varcapaFebrero = document.getElementById("Febrero");
  var varcapaMarzo = document.getElementById("Marzo");
  var varcapaAbril = document.getElementById("Abril");
  var varcapaMayo = document.getElementById("Mayo");
  var varcapaJunio = document.getElementById("Junio");
  var varcapaJulio = document.getElementById("Julio");
  var varcapaAgosto = document.getElementById("Agosto");
  var varcapaSeptiembre = document.getElementById("Septiembre");
  var varcapaOctubre = document.getElementById("Octubre");
  var varcapaNoviembre = document.getElementById("Noviembre");
  var varcapaDiciembre = document.getElementById("Diciembre");

  var varGlobal = document.getElementById("globalId2");

  function seleccionar(){
    var meSes1 = parseInt(document.getElementById("MesesId").value);
    var numMes = parseInt("<?php echo $varMes; ?>");

    if (meSes1 < (numMes - 1)) {
        event.preventDefault();
	  varGlobal.style.display = 'none';
	  varcapaEnero.style.display = 'none';
          varcapaFebrero.style.display = 'none';
          varcapaMarzo.style.display = 'none';
          varcapaAbril.style.display = 'none';
          varcapaMayo.style.display = 'none';
          varcapaJunio.style.display = 'none';
          varcapaJulio.style.display = 'none';
          varcapaAgosto.style.display = 'none';
          varcapaSeptiembre.style.display = 'none';
          varcapaOctubre.style.display = 'none';
          varcapaNoviembre.style.display = 'none';
          varcapaDiciembre.style.display = 'none';
	  varcapaCalcular.style.display = 'none';
          swal.fire("!!! Advertencia !!!","No es posible seleccionar mes pasado al actual.","warning");
        return;
    }else{
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas4",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas4",
        data : {
          txtmeSes : meSes1,
        },
        success : function(response){
          var meSes= JSON.parse(response);

          if (meSes == "1") {
            event.preventDefault();
	      	varGlobal.style.display = 'none';
		varcapaEnero.style.display = 'none';
                varcapaFebrero.style.display = 'none';
                varcapaMarzo.style.display = 'none';
                varcapaAbril.style.display = 'none';
                varcapaMayo.style.display = 'none';
                varcapaJunio.style.display = 'none';
                varcapaJulio.style.display = 'none';
                varcapaAgosto.style.display = 'none';
                varcapaSeptiembre.style.display = 'none';
                varcapaOctubre.style.display = 'none';
                varcapaNoviembre.style.display = 'none';
                varcapaDiciembre.style.display = 'none';
	        varcapaCalcular.style.display = 'none';
    		document.getElementById("txtausentismo").value = "";
		document.getElementById("txtCantTecInca").value = "";
    		document.getElementById("txtCantDiaInca").value = "";
		document.getElementById("txtausentismo").value = "0";
              swal.fire("!!! Advertencia !!!","No es posible crear dimension al mes seleccionado ya que tiene datos existentes.","warning");
            return; 
          }else{
            if (meSes1 == "1") {
                    varcapaEnero.style.display = 'inline';
                    varcapaFebrero.style.display = 'none';
                    varcapaMarzo.style.display = 'none';
                    varcapaAbril.style.display = 'none';
                    varcapaMayo.style.display = 'none';
                    varcapaJunio.style.display = 'none';
                    varcapaJulio.style.display = 'none';
                    varcapaAgosto.style.display = 'none';
                    varcapaSeptiembre.style.display = 'none';
                    varcapaOctubre.style.display = 'none';
                    varcapaNoviembre.style.display = 'none';
                    varcapaDiciembre.style.display = 'none';
	            varcapaCalcular.style.display = 'none';
                    varGlobal.style.display = 'inline'; 
			document.getElementById("txtausentismo").value = "0";
			document.getElementById("txtausentismo").value = "";
			document.getElementById("txtCantTecInca").value = "";
	    		document.getElementById("txtCantDiaInca").value = "";       
                  }else{
                    if (meSes1 == "2") {
                        varcapaEnero.style.display = 'none';
                        varcapaFebrero.style.display = 'inline';
                        varcapaMarzo.style.display = 'none';
                        varcapaAbril.style.display = 'none';
                        varcapaMayo.style.display = 'none';
                        varcapaJunio.style.display = 'none';
                        varcapaJulio.style.display = 'none';
                        varcapaAgosto.style.display = 'none';
                        varcapaSeptiembre.style.display = 'none';
                        varcapaOctubre.style.display = 'none';
                        varcapaNoviembre.style.display = 'none';
                        varcapaDiciembre.style.display = 'none';
                      varGlobal.style.display = 'inline';
		        varcapaCalcular.style.display = 'none';
    			document.getElementById("txtausentismo").value = "";
    			document.getElementById("txtCantTecInca").value = "";
    			document.getElementById("txtCantDiaInca").value = "";
			document.getElementById("txtausentismo").value = "0";
                    }else{
                      if (meSes1 == "3") {
                        varcapaEnero.style.display = 'none';
                        varcapaFebrero.style.display = 'none';
                        varcapaMarzo.style.display = 'inline';
                        varcapaAbril.style.display = 'none';
                        varcapaMayo.style.display = 'none';
                        varcapaJunio.style.display = 'none';
                        varcapaJulio.style.display = 'none';
                        varcapaAgosto.style.display = 'none';
                        varcapaSeptiembre.style.display = 'none';
                        varcapaOctubre.style.display = 'none';
                        varcapaNoviembre.style.display = 'none';
                        varcapaDiciembre.style.display = 'none';
                        varGlobal.style.display = 'inline';
		        varcapaCalcular.style.display = 'none';
    			document.getElementById("txtausentismo").value = "";
    			document.getElementById("txtCantTecInca").value = "";
   			document.getElementById("txtCantDiaInca").value = "";
			document.getElementById("txtausentismo").value = "0";
                      }else{
                        if (meSes1 == "4") {
                          varcapaEnero.style.display = 'none';
                          varcapaFebrero.style.display = 'none';
                          varcapaMarzo.style.display = 'none';
                          varcapaAbril.style.display = 'inline';
                          varcapaMayo.style.display = 'none';
                          varcapaJunio.style.display = 'none';
                          varcapaJulio.style.display = 'none';
                          varcapaAgosto.style.display = 'none';
                          varcapaSeptiembre.style.display = 'none';
                          varcapaOctubre.style.display = 'none';
                          varcapaNoviembre.style.display = 'none';
                          varcapaDiciembre.style.display = 'none';
                          varGlobal.style.display = 'inline';
		        varcapaCalcular.style.display = 'none';
    			document.getElementById("txtausentismo").value = "";
    			document.getElementById("txtCantTecInca").value = "";
    			document.getElementById("txtCantDiaInca").value = "";
			document.getElementById("txtausentismo").value = "0";
                        }else{
                          if (meSes1 == "5") {
                            varcapaEnero.style.display = 'none';
                            varcapaFebrero.style.display = 'none';
                            varcapaMarzo.style.display = 'none';
                            varcapaAbril.style.display = 'none';
                            varcapaMayo.style.display = 'inline';
                            varcapaJunio.style.display = 'none';
                            varcapaJulio.style.display = 'none';
                            varcapaAgosto.style.display = 'none';
                            varcapaSeptiembre.style.display = 'none';
                            varcapaOctubre.style.display = 'none';
                            varcapaNoviembre.style.display = 'none';
                            varcapaDiciembre.style.display = 'none';
                            varGlobal.style.display = 'inline';
		            varcapaCalcular.style.display = 'none';
    				document.getElementById("txtausentismo").value = "";
    				document.getElementById("txtCantTecInca").value = "";
    				document.getElementById("txtCantDiaInca").value = "";
				document.getElementById("txtausentismo").value = "0";
                          }else{
                            if (meSes1 == "6") {
                              varcapaEnero.style.display = 'none';
                              varcapaFebrero.style.display = 'none';
                              varcapaMarzo.style.display = 'none';
                              varcapaAbril.style.display = 'none';
                              varcapaMayo.style.display = 'none';
                              varcapaJunio.style.display = 'inline';
                              varcapaJulio.style.display = 'none';
                              varcapaAgosto.style.display = 'none';
                              varcapaSeptiembre.style.display = 'none';
                              varcapaOctubre.style.display = 'none';
                              varcapaNoviembre.style.display = 'none';
                              varcapaDiciembre.style.display = 'none';
                              varGlobal.style.display = 'inline';
		              varcapaCalcular.style.display = 'none';
    				document.getElementById("txtausentismo").value = "";
    				document.getElementById("txtCantTecInca").value = "";
    				document.getElementById("txtCantDiaInca").value = "";
				document.getElementById("txtausentismo").value = "0";
                            }else{
                              if (meSes1 == "7") {
                                varcapaEnero.style.display = 'none';
                                varcapaFebrero.style.display = 'none';
                                varcapaMarzo.style.display = 'none';
                                varcapaAbril.style.display = 'none';
                                varcapaMayo.style.display = 'none';
                                varcapaJunio.style.display = 'none';
                                varcapaJulio.style.display = 'inline';
                                varcapaAgosto.style.display = 'none';
                                varcapaSeptiembre.style.display = 'none';
                                varcapaOctubre.style.display = 'none';
                                varcapaNoviembre.style.display = 'none';
                                varcapaDiciembre.style.display = 'none';
                                varGlobal.style.display = 'inline';
			        varcapaCalcular.style.display = 'none';
    				document.getElementById("txtausentismo").value = "";
    				document.getElementById("txtCantTecInca").value = "";
    				document.getElementById("txtCantDiaInca").value = "";
				document.getElementById("txtausentismo").value = "0";
                              }else{
                                if (meSes1 == "8") {
                                  varcapaEnero.style.display = 'none';
                                  varcapaFebrero.style.display = 'none';
                                  varcapaMarzo.style.display = 'none';
                                  varcapaAbril.style.display = 'none';
                                  varcapaMayo.style.display = 'none';
                                  varcapaJunio.style.display = 'none';
                                  varcapaJulio.style.display = 'none';
                                  varcapaAgosto.style.display = 'inline';
                                  varcapaSeptiembre.style.display = 'none';
                                  varcapaOctubre.style.display = 'none';
                                  varcapaNoviembre.style.display = 'none';
                                  varcapaDiciembre.style.display = 'none';
                                  varGlobal.style.display = 'inline';
		    	          varcapaCalcular.style.display = 'none';
    					document.getElementById("txtausentismo").value = "";
    					document.getElementById("txtCantTecInca").value = "";
    					document.getElementById("txtCantDiaInca").value = "";
					document.getElementById("txtausentismo").value = "0";
                                }else{
                                  if (meSes1 == "9") {
                                    varcapaEnero.style.display = 'none';
                                    varcapaFebrero.style.display = 'none';
                                    varcapaMarzo.style.display = 'none';
                                    varcapaAbril.style.display = 'none';
                                    varcapaMayo.style.display = 'none';
                                    varcapaJunio.style.display = 'none';
                                    varcapaJulio.style.display = 'none';
                                    varcapaAgosto.style.display = 'none';
                                    varcapaSeptiembre.style.display = 'inline';
                                    varcapaOctubre.style.display = 'none';
                                    varcapaNoviembre.style.display = 'none';
                                    varcapaDiciembre.style.display = 'none';
                                    varGlobal.style.display = 'inline';
			            varcapaCalcular.style.display = 'none';
    					document.getElementById("txtausentismo").value = "";
    					document.getElementById("txtCantTecInca").value = "";
    					document.getElementById("txtCantDiaInca").value = "";
					document.getElementById("txtausentismo").value = "0";
                                  }else{
                                    if (meSes1 == "10") {
                                      varcapaEnero.style.display = 'none';
                                      varcapaFebrero.style.display = 'none';
                                      varcapaMarzo.style.display = 'none';
                                      varcapaAbril.style.display = 'none';
                                      varcapaMayo.style.display = 'none';
                                      varcapaJunio.style.display = 'none';
                                      varcapaJulio.style.display = 'none';
                                      varcapaAgosto.style.display = 'none';
                                      varcapaSeptiembre.style.display = 'none';
                                      varcapaOctubre.style.display = 'inline';
                                      varcapaNoviembre.style.display = 'none';
                                      varcapaDiciembre.style.display = 'none';
                                      varGlobal.style.display = 'inline';
			              varcapaCalcular.style.display = 'none';
    					document.getElementById("txtausentismo").value = "";
    					document.getElementById("txtCantTecInca").value = "";
    					document.getElementById("txtCantDiaInca").value = "";
					document.getElementById("txtausentismo").value = "0";
                                    }else{
                                      if (meSes1 == "11") {
                                        varcapaEnero.style.display = 'none';
                                        varcapaFebrero.style.display = 'none';
                                        varcapaMarzo.style.display = 'none';
                                        varcapaAbril.style.display = 'none';
                                        varcapaMayo.style.display = 'none';
                                        varcapaJunio.style.display = 'none';
                                        varcapaJulio.style.display = 'none';
                                        varcapaAgosto.style.display = 'none';
                                        varcapaSeptiembre.style.display = 'none';
                                        varcapaOctubre.style.display = 'none';
                                        varcapaNoviembre.style.display = 'inline';
                                        varcapaDiciembre.style.display = 'none';
                                        varGlobal.style.display = 'inline';
				        varcapaCalcular.style.display = 'none';
    					document.getElementById("txtausentismo").value = "";
    					document.getElementById("txtCantTecInca").value = "";
    					document.getElementById("txtCantDiaInca").value = "";
					document.getElementById("txtausentismo").value = "0";
                                      }else{
                                        if (meSes1 == "12") {
                                          varcapaEnero.style.display = 'none';
                                          varcapaFebrero.style.display = 'none';
                                          varcapaMarzo.style.display = 'none';
                                          varcapaAbril.style.display = 'none';
                                          varcapaMayo.style.display = 'none';
                                          varcapaJunio.style.display = 'none';
                                          varcapaJulio.style.display = 'none';
                                          varcapaAgosto.style.display = 'none';
                                          varcapaSeptiembre.style.display = 'none';
                                          varcapaOctubre.style.display = 'none';
                                          varcapaNoviembre.style.display = 'none';
                                          varcapaDiciembre.style.display = 'inline';
                                          varGlobal.style.display = 'inline';
				          varcapaCalcular.style.display = 'none';
    						document.getElementById("txtausentismo").value = "";
    						document.getElementById("txtCantTecInca").value = "";
    						document.getElementById("txtCantDiaInca").value = "";
						document.getElementById("txtausentismo").value = "0";
                                        }
                                      }
                                    }
                                  }
                                }
                              }
                            }
                          }
                        }
                      }
                    }
                  }
          }
        }
      });
    }
  };
 

  function generatedEnero(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idE").value;
    var varyear = document.getElementById("txtyearE").value;
    var varmonth = document.getElementById("txtmonthE").value;
    var varfechacreacion = document.getElementById("txtfechacreacionE").value;
    var varanulado = document.getElementById("txtanuladoE").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="https://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };

    function generatedFebrero(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idF").value;
    var varyear = document.getElementById("txtyearF").value;
    var varmonth = document.getElementById("txtmonthF").value;
    var varfechacreacion = document.getElementById("txtfechacreacionF").value;
    var varanulado = document.getElementById("txtanuladoF").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };

  function generatedMarzo(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idM").value;
    var varyear = document.getElementById("txtyearM").value;
    var varmonth = document.getElementById("txtmonthM").value;
    var varfechacreacion = document.getElementById("txtfechacreacionM").value;
    var varanulado = document.getElementById("txtanuladoM").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);                        
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };

  function generatedAbril(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idA").value;
    var varyear = document.getElementById("txtyearA").value;
    var varmonth = document.getElementById("txtmonthA").value;
    var varfechacreacion = document.getElementById("txtfechacreacionA").value;
    var varanulado = document.getElementById("txtanuladoA").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };  

  function generatedMayo(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idMM").value;
    var varyear = document.getElementById("txtyearMM").value;
    var varmonth = document.getElementById("txtmonthMM").value;
    var varfechacreacion = document.getElementById("txtfechacreacionMM").value;
    var varanulado = document.getElementById("txtanuladoMM").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };   

  function generatedJunio(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idJ").value;
    var varyear = document.getElementById("txtyearJ").value;
    var varmonth = document.getElementById("txtmonthJ").value;
    var varfechacreacion = document.getElementById("txtfechacreacionJ").value;
    var varanulado = document.getElementById("txtanuladoJ").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };   

  function generatedJulio(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idJJ").value;
    var varyear = document.getElementById("txtyearJJ").value;
    var varmonth = document.getElementById("txtmonthJJ").value;
    var varfechacreacion = document.getElementById("txtfechacreacionJJ").value;
    var varanulado = document.getElementById("txtanuladoJJ").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };     

  function generatedAgosto(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idAA").value;
    var varyear = document.getElementById("txtyearAA").value;
    var varmonth = document.getElementById("txtmonthAA").value;
    var varfechacreacion = document.getElementById("txtfechacreacionAA").value;
    var varanulado = document.getElementById("txtanuladoAA").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };  


  function generatedSeptiembre(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idS").value;
    var varyear = document.getElementById("txtyearS").value;
    var varmonth = document.getElementById("txtmonthS").value;
    var varfechacreacion = document.getElementById("txtfechacreacionS").value;
    var varanulado = document.getElementById("txtanuladoS").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };    

  function generatedOctubre(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idO").value;
    var varyear = document.getElementById("txtyearO").value;
    var varmonth = document.getElementById("txtmonthO").value;
    var varfechacreacion = document.getElementById("txtfechacreacionO").value;
    var varanulado = document.getElementById("txtanuladoO").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  }; 

  function generatedNoviembre(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idN").value;
    var varyear = document.getElementById("txtyearN").value;
    var varmonth = document.getElementById("txtmonthN").value;
    var varfechacreacion = document.getElementById("txtfechacreacionN").value;
    var varanulado = document.getElementById("txtanuladoN").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };   

  function generatedDiciembre(){
    var varcantvalor = document.getElementById("txtcant_valor").value;
    var vartiempollamada = document.getElementById("txttiempo_llamada").value;
    var vartiempoadicional = document.getElementById("txttiempoaidional").value;
    var varactuales = document.getElementById("txtactuales").value;
    var varotrasactividad = document.getElementById("txtotras_actividad").value;
    var varturnopromedio = document.getElementById("txtturno_promedio").value;
    var varausentismo = document.getElementById("txtausentismo").value;
    var varvacapermilicen = document.getElementById("txtvaca_permi_licen").value; 

    var varusu = document.getElementById("txtusua_idD").value;
    var varyear = document.getElementById("txtyearD").value;
    var varmonth = document.getElementById("txtmonthD").value;
    var varfechacreacion = document.getElementById("txtfechacreacionD").value;
    var varanulado = document.getElementById("txtanuladoD").value;

    if (varcantvalor == "" || vartiempollamada == "" || vartiempoadicional == "" || varactuales == "" || varotrasactividad == "" || varturnopromedio == "" || varausentismo == "" || varvacapermilicen == "") {
          event.preventDefault();
            swal.fire("!!! Advertencia !!!","Campos vacios, por favor ingresar los datos correspondientes.","warning");
          return; 
    }
    else
    {
      $.ajax({
        method: "post",
        //url: "https://172.20.100.50/qa/web/index.php/controldimensionamiento/crearpruebas",
        //url: "http://qa.allus.com.co/qa_managementv2/web/index.php/basesatisfaccion/pruebagrupal",
	url: "crearpruebas",
        data : {
          txtusuaid : varusu,
          txtyear : varyear,
          txtmonth : varmonth,
          txtcantvalor : varcantvalor,
          txttiempollamada : vartiempollamada,
          txttiempoadicional : vartiempoadicional,
          txtactuales : varactuales,
          txtotrasactividad : varotrasactividad,
          txtturnopromedio : varturnopromedio,
          txtausentismo : varausentismo,
          txtvacapermilicen : varvacapermilicen,
          txtfechacreacion : varfechacreacion,
          txtanulado : varanulado,
        },
        success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    console.log(numRta);

                    if (numRta == 0) {
			window.location.href="http://qa.grupokonecta.local/qa_managementv2/web/index.php/controldimensionamiento/index";
                    }else{
                      event.preventDefault();
                        swal.fire("!!! Error !!!","No es posible guardar los datos, por favor verificar bien la informacion.","error");
                      return; 
                    }
                }
      });
    }
  };    

  function calcularausentismo(){
    var varcapaSeleccion = document.getElementById("seleccion");
    document.getElementById("txtausentismo").value = "0";
    document.getElementById("txtCantTecInca").value = "";
    document.getElementById("txtCantDiaInca").value = "";
    varcapaCalcular.style.display = 'inline';
  };

  function calculadora(){
    var varcapaCanTecInca = document.getElementById("txtCantTecInca").value;
    var varcapaCantDiaInca = document.getElementById("txtCantDiaInca").value;

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