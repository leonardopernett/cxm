 <?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    
    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $varnombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $sessiones")->queryScalar();   

    $vareliminadas = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_novedadeslog where anulado = 0 and aprobado = 1 ")->queryScalar();
    $varnoelimanadas = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_novedadeslog where anulado = 0 and aprobado = 2 ")->queryScalar();
    $vareneliminadas = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_novedadeslog where anulado = 0 and aprobado = 0 ")->queryScalar();
    $vartotal = Yii::$app->db->createCommand("select count(1) from tbl_evaluacion_novedadeslog where anulado = 0 and aprobado in (0,1,2) ")->queryScalar();

    $varlistnovedades = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadeslog where anulado = 0 and aprobado in (0,1,2) ")->queryAll();

?>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito');

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

    .card2 {
            height: 174px;
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
        background-image: url('../../images/Banner_Ev_Desarrollo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        
      </div>
    </div>
  </div>
</header>
<br><br>
<div id="idCapaUno" style="display: inline">
	<div class="capauno" style="display: inline;">
		<div class="row">
			<div class="col-md-3">
				<div class="card2 mb">
					<label><em class="fas fa-user-circle" style="font-size: 20px; color: #559FFF;"></em></em> Usuario:</label>
                    <label style="font-size: 16px;"><?php echo $varnombre; ?></label>
                    <br>
                    <label><em class="fas fa-minus-circle" style="font-size: 20px; color: #559FFF;"></em></em> Proceso:</label>
                    <label style="font-size: 16px;"><?php echo "Eliminación de novedades"; ?></label>
				</div>
			</div>
			<div class="col-md-6">
				<div class="card2 mb">
					<label><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em></em> Resultados de novedades en proceso de eliminar:</label>					
					<div class="row">
						<div class="col-md-4">
							<label><em class="fas fa-square" style="font-size: 20px; color: #59DE49;"></em></em> Eliminadas</label>
							<label><em class="fas fa-square" style="font-size: 20px; color: #f7b9b9;"></em></em> No elimanadas</label>
							<label><em class="fas fa-square" style="font-size: 20px; color: #FFC251;"></em></em> En espera de eliminar</label>
						</div>
						<div class="col-md-8" class="text-center">
							<div id="containerE" class="highcharts-container" style="height: 120px;"></div> 
						</div>
					</div>
				</div>
			</div>
			<div class="col-md-3">
				<div class="card2 mb">
					<label><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em> Cantidad de novedades para eliminar:</label>
                    <label  style="font-size: 70px; text-align: center;"><?php echo $vartotal; ?></label>
				</div>
			</div>
		</div>
	</div>
	<hr>
	<div id="capaDos" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-list" style="font-size: 20px; color: #00968F;"></em> Listado de novedades a eliminar</label>
                    <br>
                    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Tabla datos</caption>
                        <thead>
                            <th id="id" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Id"; ?></label></th>
                            <th id="solicitanteNovedad" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Solicitante novedad"; ?></label></th>
                            <th id="asunto" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Asunto"; ?></label></th>
                            <th id="descripcion" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Descripción"; ?></label></th>
                            <th id="tipoEvaluacion" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tipo evaluación"; ?></label></th>
                            <th id="estado" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Estado"; ?></label></th>
                            <th id="aprobar" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Aprobar"; ?></label></th>
                            <th id="noAprobar" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "No aprobar"; ?></label></th>
                        </thead>
                        <tbody>

                                <?php foreach ($varlistnovedades as $key => $value) {
                                    $varidnovedad = $value['idnovedadeslog'];
                                                                        
                                    $varidnombre = $value['evaluadorid'];
                                    $varnombresolicitante = Yii::$app->db->createCommand("select nombre_completo from tbl_usuarios_evalua where anulado = 0 and documento = $varidnombre")->queryScalar();
                                    $varidestado = $value['aprobado'];
                                    $varestados = null;
                                    if ($varidestado == 1) {
                                        $varestados = "Eliminado";
                                    }else{
                                        if ($varidestado == 2) {
                                            $varestados = "No eliminado";
                                        }else{
                                            if ($varidestado == 0) {
                                                $varestados = "En esperade eliminar";
                                            }
                                        }
                                    }
                                    $txtvarevalua = $value['tipo_eva'];
                                    $varevalua = Yii::$app->db->createCommand("select distinct tipoevaluacion from tbl_evaluacion_tipoeval where idevaluaciontipo = $txtvarevalua")->queryScalar();
                                ?>
                                    <tr>
                                        <td><label style="font-size: 12px;"><?php echo  $varidnovedad; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varnombresolicitante; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['asunto']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varevalua; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varestados; ?></label></td>
                                        <?php if ($varidestado == 1 || $varidestado == 2) {?>
                                            <td><label style="font-size: 12px;"><?php echo  "---"; ?></label></td>
                                            <td><label style="font-size: 12px;"><?php echo  "---"; ?></label></td>
                                        <?php }else{ ?>
                                            <td class="text-center">
                                                    <?= Html::a('<i class="fas fa-thumbs-up" style="font-size: 20px; color: #2cdc5a;"></i>',  ['editarnovedaddelete','varidnombre'=>$varidnombre,'varidevaluado' => $value['evaluado'], 'varestado' => 1,'vartipoeva'=>$txtvarevalua,'varidevalua'=>$varidnovedad], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Aprobar']) ?>
                                            </td>
                                            <td class="text-center">
                                                <?= Html::a('<i class="fas fa-thumbs-down" style="font-size: 20px; color: #ff3838;"></i>',  ['editarnovedaddelete','varidnombre'=>$varidnombre,'varidevaluado' => $value['evaluado'], 'varestado' => 2,'vartipoeva'=>$txtvarevalua,'varidevalua'=>$varidnovedad], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'No Aprobar']) ?>
                                            </td>
                                        <?php } ?>
                                    </tr>
                                <?php } ?>

                        </tbody>                        
                    </table>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div id="capaTres" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                                <?= Html::a('Regresar',  ['evaluaciondesarrollo/gestionnovedades'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                                ?>                            
                            </div>
                        </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
</div>
<script type="text/javascript">
    $(function() {
        Highcharts.chart('containerE', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '<label style="font-size: 20px;"><?php echo ''; ?></label>',
                align: 'center',
                verticalAlign: 'middle',
                y: 60
            },
            accessibility: {
                point: {
                    valueSuffix: '%'
                }
            },
            plotOptions: {
                pie: {
                    dataLabels: {
                        enabled: true,
                        distance: -50,
                        style: {
                            fontWeight: 'bold',
                            color: 'white'
                        }
                    },
                    startAngle: -90,
                    endAngle: 90,
                    center: ['50%', '110%'],
                    size: '220%',
                    width: '200%'
                }
            },
            series: [{
                type: 'pie',
                name: '',
                innerSize: '50%',
                data: [
                    {
                        name: 'Eliminadas',
                        y: parseFloat("<?php echo $vareliminadas;?>"),
                        color: '#59DE49',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'No eliminadas',
                        y: parseFloat("<?php echo $varnoelimanadas;?>"),
                        color: '#f7b9b9',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'Sin eliminar',
                        y: parseFloat("<?php echo $vareneliminadas;?>"),
                        color: '#FFC251',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });

    }); 
</script>