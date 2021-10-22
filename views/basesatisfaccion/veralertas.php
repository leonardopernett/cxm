<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Visualizar Alerta');
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>

<style type="text/css">

	.pruebaaaaaaa{
		height : 50%;
		width : 50%;
	}

	.pruebaaaaaaa:hover{
		height: 100%;
		width: 100%;
	}
</style>


<div class="col-md-offset-2 col-sm-8 panel panel-default">
  <div class="panel-body">
	<a class="btn btn-default soloCancelar" href="/qa_managementv2/web/index.php/basesatisfaccion/alertasvaloracion"> Regresar </a>
	<br>
  	<table class="table table-striped">
	  <caption>Tabla alertas</caption>
  		<thead>
  			<tr>
  				<th id="FechaEnvio">Fecha de Envio</th>
  				<th id="Programa">Programa</th>
  				<th id="valorador">Valorador</th>
  				<th id="tipoAlerta">Tipo de Alerta</th>
  				<th id="destinatarios">Destinatarios</th>
  				<th id="asunto">Asunto</th>
  				<th id="comentario">Comentario</th>
  			</tr>
  		</thead>
  		<tbody>
  			<tr>
  				<td><?php echo $model['Fecha'] ?></td>
				<td><?php echo $model['Programa'] ?></td>
				<td><?php echo $model['Tecnico'] ?></td>
				<td><?php echo $model['Tipoalerta'] ?></td>
				<td><?php echo $model['Destinatarios'] ?></td>
				<td><?php echo $model['Asunto'] ?></td>
				<td><?php echo $model['Comentario'] ?></td>
  			</tr>
  		</tbody>
  	</table>
  	<center><img class="pruebaaaaaaa" src="../../../alertas/<?php echo $model['Adjunto'] ?>" alt="Image-view" ></center>
  </div>
</div>



