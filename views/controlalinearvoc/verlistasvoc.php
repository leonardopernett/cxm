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
    <h3><center><?= Html::encode($this->title) ?></center></h3>
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
		<thead>
			
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
          PLAN DE ACCION
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
  		<thead>
  			
  		</thead>
  		<tbody>
  			<tr>
	            <td>
	              <label for="txtResponsable">Responsabilidad</label>
	                    <input type="text" class="form-control" id="txtResponsable" readonly="readonly" value="<?php echo $txtResponsable; ?>" data-toggle="tooltip" title="Responsabilidad">                                                 
	            </td>
	            <td>
	              <label for="txtActividad">Actividad</label>
	                    <input type="text" class="form-control" id="txtActividad" readonly="readonly" value="<?php echo $txtActividad; ?>" data-toggle="tooltip" title="Actividad">                                                 
	            </td>	            
            </tr> 
            </tr> 
  			<tr>
	            <td>
	              <label for="txtFechaActual">Fecha Actual</label>
	                    <input type="text" class="form-control" id="txtFechaActual" readonly="readonly" value="<?php echo $txtFechaActual; ?>" data-toggle="tooltip" title="Fecha Actual">                                                 
	            </td>
	            <td>
	              <label for="txtFechaInicio">Fecha Inicio</label>
	                    <input type="text" class="form-control" id="txtFechaInicio" readonly="readonly" value="<?php echo $txtFechaInicio; ?>" data-toggle="tooltip" title="Fecha Inicio">                                                 
	            </td>	            
            </tr> 
            <tr>
	            <td>
	              <label for="txtFechaFin">Fecha Fin</label>
	                    <input type="text" class="form-control" id="txtFechaFin" readonly="readonly" value="<?php echo $txtFechaFin; ?>" data-toggle="tooltip" title="PuntoDolor">                                                 
	            </td>
	            <td>
	              <label for="txtRecursos">Recursos</label>
	                    <input type="text" class="form-control" id="txtRecursos" readonly="readonly" value="<?php echo $txtRecursos; ?>" data-toggle="tooltip" title="Recursos">                                                 
	            </td>	            
            </tr> 
	          <tr>
	            <td>
	              <label for="txtSeguimiento">Seguimineto</label>
	                    <input type="text" class="form-control" id="txtSeguimiento" readonly="readonly" value="<?php echo $txtSeguimiento; ?> %" data-toggle="tooltip" title="Seguimiento">                                                 
	            </td>
	            <td>
	              <label for="txtEstado">Estado</label>
	                    <input type="text" class="form-control" id="txtEstado" readonly="readonly" value="<?php echo $txtEstado; ?>" data-toggle="tooltip" title="Estado">                                                 
	            </td>	            
            </tr> 
	          <tr>
	            <td>
	              <label for="txtAlertas">Alertas</label>
	                    <input type="text" class="form-control" id="txtAlertas" readonly="readonly" value="<?php echo $txtAlertas; ?>" data-toggle="tooltip" title="Alertas">                                                 
	            </td>
	            <td>
	              <label for="txtObservacion">Observaciones</label>
	                    <input type="text" class="form-control" id="txtObservacion" readonly="readonly" value="<?php echo $txtObservacion; ?>" data-toggle="tooltip" title="Observaciones">                                                 
	            </td>	            
            </tr>      

  		</tbody>
  	</table>
  </div>
