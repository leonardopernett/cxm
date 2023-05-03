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

$this->title = 'Detalle Alerta';//nombre del titulo de mi modulo
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';



?>
<style>



    .pruebaaaaaaa{
    height : 50%;
    width : 50%;
  }

  .pruebaaaaaaa:hover{
    height: 100%;
    width: 100%;
  }

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
            font-family: "Nunito",sans-serif;
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
        background-image: url('../../images/link.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 80px;
      height: 80px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

</style>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>



<br><br>
<div class="capaInfo" id="idCapaInfo" style="display: inline;"><!-- div principal que va llevar todo menos la imagen-->
  
  <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Detalle de la Alerta"; ?> </label><!--titulo principal de mi modulo-->
      </div>
    </div>
  </div><!-- divdel subtitilo azul principal que va llevar el nombre del modulo---------------------->

  <br>

	<div class="row">
    	<div class="col-md-12">
      		<div class="card1 mb">
				<table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:12px" >
					<caption>...</caption>
					<tr>
						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Envio') ?></label></th>
						<td><?php echo $model['Fecha'] ?></td>

						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa PCRC') ?></label></th>
						<td><?php echo $model['Programa'] ?></td>					
					</tr>
					<tr>
						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Cliente') ?></label></th>
						<td><?php echo $model['Programa'] ?></td>

						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
            <td><?php echo $model['Tecnico'] ?></td>			
					</tr>
					<tr>
						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
						<td><?php echo $model['Tipoalerta'] ?></td>

						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Destinatarios') ?></label></th>
						<td style="max-width:300px;word-wrap: break-word;"><?php echo $model['Destinatarios'] ?></td>					
					</tr>
					<tr>
						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asunto') ?></label></th>
						<td><?php echo $model['Asunto'] ?></td>

						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
						<td style="max-width:300px;"><?php echo $model['Comentario'] ?></td>
					</tr>
				</table>
			</div>
		</div>
	</div>

	<br><hr><br>
	<div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
		<div class="col-md-6">
			<div class="card1 mb" style="background: #6b97b1; ">
				<label style="font-size: 20px; color: #FFFFFF;"><?php echo "Detalle de la Encuesta"; ?> </label><!--titulo principal de mi modulo-->
			</div>
		</div>
	</div>

	<br>
	
	<div class="row">
		<div class="col-md-12">
			<div class="card1 mb">
				<table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:12px" >
					<caption> <b> ID Alerta  <?php echo $model['id'] ?></b></caption>
						<th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentarios / Respuestas') ?></label></th>
						<td style="max-width:10px;">
              <?= Html::a('Ver',  ['totalcomensaf','id'=>$id], ['class' => 'btn btn-success',//index es para que me redireccione o sea volver a el inicio 
                                          'style' => 'background-color: #b0cdd6; width:100px;',//color del boton  
                                          'data-toggle' => 'tooltip',
                                          'title' => 'Ver'])//titulo del boton  
              ?>
            </td>	 
				</table>
			</div>
		</div>
	</div>
 
  <br><hr><br>
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #707372;"></em> Cancelar y Regresar...</label><!-- label del titulo de lo que vamos a mostrar ------>
        <?= Html::a('Regresar',  ['alertasvaloracion'], ['class' => 'btn btn-success',//index es para que me redireccione o sea volver a el inicio 
                                        'style' => 'background-color: #707372',//color del boton  
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar'])//titulo del boton  
        ?>
      </div>
    </div>
    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #707372;"></em>  Archivo Adjunto de la Alerta...</label><!-- label del titulo de lo que vamos a mostrar ------>
        <img src="../../../alertas/<?php echo $model['Adjunto'] ?>" alt="Image.png">
      </div>
    </div>
  </div>
</div>  


			
		



