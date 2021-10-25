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

	$this->title = 'Vista Escucha Focalizada - VOC -';
	$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

?>
  <?= Html::a('Regresar',  ['reportevoc'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>
<br>
<div class="page-header" >
    <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
</div> 
<br>
<div  class="col-md-12">
    <div class="row seccion-data">
      <div class="col-md-10">
        <label class="labelseccion">
          INFORMACIÓN DE PARTIDA
        </label>      
      </div>    
      <div class="col-md-2">
        <?=
          Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                      "class" => "glyphicon glyphicon-chevron-downForm",
                                  ]) . "", "javascript:void(0)"
                                  , ["class" => "openSeccion", "id" => "bloqueOne"])
        ?>
      </div>
      <?php $this->registerJs('$("#bloqueOne").click(function () {
                                  $("#dtbloque1").toggle("slow");
                              });'); ?>
    </div>
</div>
<div id="dtbloque1" class="col-sm-12" style="display: none">
	<table class="table table-striped table-bordered detail-view formDinamico">
  <caption>Taba de datos</caption>
		<thead>
    <th scope="col"></th>
		</thead>
		<tbody>
          <tr>
            <td>
              <label for="txtPcrc">Programa o PCRC</label>
                    <input type="text" class="form-control" readonly="readonly" id="txtPcrc" value="<?php echo $txtArbol; ?>" data-toggle="tooltip" title="Programa o PCRC.">              
            </td> 
            <td>
              <label for="txtValorado">Valorado</label>
              <input type="text" id="txtValorado" name="datetimes" readonly="readonly" value="<?php echo $txtNombreTecnico; ?>" class="form-control" data-toggle="tooltip" title="Valorado">
            </td>
          </tr>  
          <tr>
            <td>
              <label for="txtIDExtSp">ID Externo Speech</label>
                    <input type="text" readonly="readonly" value="<?php echo $txtSpeech; ?>" class="form-control" id="txtIDExtSp" data-toggle="tooltip" title="Id Externo Speech.">            
            </td> 
            <td>
              <label for="txtFechaHora">Fecha y hora</label>
                  <input type="datetime-local" id="txtFechaHora" name="datetimes" readonly="readonly" value="<?php echo $txtFecha; ?>" class="form-control" data-toggle="tooltip" title="Fecha & Hora">
            </td>
          </tr>   
          <tr>
            <td>
              <label for="txtUsuAge">Usuario de Agente</label>
                    <input type="text" class="form-control" readonly="readonly" value="<?php echo $txtAgente; ?>" id="txtUsuAge" data-toggle="tooltip" title="Usuario de Agente">              
            </td> 
            <td>
              <label for="txtDuracion">Duración</label>                    
                  <input type="text" class="form-control" readonly="readonly" value="<?php echo $txtDureacion; ?>"  id="txtDuracion" data-toggle="tooltip" title="Duracion de la llamada">
            </td>
          </tr> 
          <tr>
            <td>
              <label for="txtExtencion">Extensión</label>
                    <input type="text" class="form-control" readonly="readonly" value="<?php echo $txtExtension; ?>" id="txtExtencion" onkeypress="return valida(event)" data-toggle="tooltip" title="Extensión">               
            </td> 
            <td>
              <label for="txtDimension">Dimensión</label>
                    <input type="text" class="form-control" id="txtDimension" readonly="readonly" value="<?php echo $txtDimensiones; ?>" data-toggle="tooltip" title="Dimensión">                                                 
            </td>
          </tr> 
		</tbody>
	</table>
</div>

  <div  class="col-md-12">
    <div class="row seccion">
      <div class="col-md-10">
        <label class="labelseccion">
          ESCUCHA FOCALIZADA
        </label>      
      </div>    
      <div class="col-md-2">
        <?=
          Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                      "class" => "glyphicon glyphicon-chevron-downForm",
                                  ]) . "", "javascript:void(0)"
                                  , ["class" => "openSeccion", "id" => "bloqueTwo"])
        ?>
      </div>
      <?php $this->registerJs('$("#bloqueTwo").click(function () {
                                  $("#dtbloque2").toggle("slow");
                              });'); ?>
    </div>
  </div>
  <div id="dtbloque2" class="col-sm-12" style="display: none">
  	<table class="table table-striped table-bordered detail-view formDinamico">
    <caption>Tabla de datos</caption>
  		<thead>
      <th scope="col"></th>
  		</thead>
  		<tbody>
  			<tr>
	            <td>
	              <label for="txtIndiGlo">Indicadores Globales</label>
	                    <input type="text" class="form-control" id="txtIndiGlo" readonly="readonly" value="<?php echo $txtIndiGlo; ?>" data-toggle="tooltip" title="Indicador">                                                 
	            </td>
	            <td>
	              <label for="txtVariable">Variable</label>
	                    <input type="text" class="form-control" id="txtVariable" readonly="readonly" value="<?php echo $txtVariable; ?>" data-toggle="tooltip" title="Variable">                                                 
	            </td>	            
            </tr> 
            </tr> 
  			<tr>
	            <td>
	              <label for="txtMotivoC">Motivo de contacto o Tipo de Servicio</label>
	                    <input type="text" class="form-control" id="txtMotivoC" readonly="readonly" value="<?php echo $txtMotivoContacto; ?>" data-toggle="tooltip" title="MotivoContacto">                                                 
	            </td>
	            <td>
	              <label for="txtMotivoL">Motivos de Llamadas</label>
	                    <input type="text" class="form-control" id="txtMotivoL" readonly="readonly" value="<?php echo $txtMotivoLlamada; ?>" data-toggle="tooltip" title="MotivosLlamadas">                                                 
	            </td>	            
            </tr> 
            <tr>
	            <td>
	              <label for="txtPuntoD">Punto de Dolor</label>
	                    <input type="text" class="form-control" id="txtPuntoD" readonly="readonly" value="<?php echo $txtPuntoDolor; ?>" data-toggle="tooltip" title="PuntoDolor">                                                 
	            </td>
	            <td>
	              <label for="txtCategorizada">Esta llamada esta bien categorizada? SI/NO</label>
	                    <input type="text" class="form-control" id="txtCategorizada" readonly="readonly" value="<?php echo $txtLlamadaCategorizada; ?>" data-toggle="tooltip" title="LlamaCategorizada">                                                 
	            </td>	            
            </tr> 
	    <tr>
	            <td>
	              <label for="txtPorcentajeAfe">% Indicador afectado de la variable o motivo o el punto de dolor</label>
	                    <input type="text" class="form-control" id="txtPorcentajeAfe" readonly="readonly" value="<?php echo $txtPorcentaje; ?> %" data-toggle="tooltip" title="PorcentajeAfectacion">                                                 
	            </td>
	            <td>
	              <label for="txtAgente">Agente (Detalle de Responsabilidad)</label>
	                    <input type="text" class="form-control" id="txtAgente" readonly="readonly" value="<?php echo $txtAgente; ?>" data-toggle="tooltip" title="Agente">                                                 
	            </td>	            
            </tr> 
	    <tr>
	            <td>
	              <label for="txtMarca">Marca (Detalle de Responsabilidad)</label>
	                    <input type="text" class="form-control" id="txtMarca" readonly="readonly" value="<?php echo $txtMarca; ?>" data-toggle="tooltip" title="PorcentajeAfectacion">                                                 
	            </td>
	            <td>
	              <label for="txtCanal">Canal (Detalle de Responsabilidad)</label>
	                    <input type="text" class="form-control" id="txtCanal" readonly="readonly" value="<?php echo $txtCanal; ?>" data-toggle="tooltip" title="Agente">                                                 
	            </td>	            
            </tr> 
	    <tr>
	            <td>
	              <label for="txtDcualitativo">Detalle cualitativo (Detalle de Responsabilidad)</label>
	                    <input type="text" class="form-control" id="txtDcualitativo" readonly="readonly" value="<?php echo $txtDcualitativos; ?>" data-toggle="tooltip" title="DetalleCualitativo">                                                 
	            </td>
	            <td>
	              <label for="txtMapa1">Mapa de Interesados 1</label>
	                    <input type="text" class="form-control" id="txtMapa1" readonly="readonly" value="<?php echo $txtMapaInteresados1; ?>" data-toggle="tooltip" title="MapaInteresados">                                                 
	            </td>	            
            </tr> 
  			<tr>
	            <td>
	              <label for="txtMapa2">Mapa de Interesados 2</label>
	                    <input type="text" class="form-control" id="txtMapa2" readonly="readonly" value="<?php echo $txtMapaInteresados2; ?>" data-toggle="tooltip" title="MapaInteresados2">                                                 
	            </td>
	            <td>
	              <label for="txtatributos">Atributos de Calidad</label>
	                    <input type="text" class="form-control" id="txtatributos" readonly="readonly" value="<?php echo $txtatributos; ?>" data-toggle="tooltip" title="AtributosCalidad">                                                 
	            </td>	            
            </tr> 

  		</tbody>
  	</table>
  </div>
