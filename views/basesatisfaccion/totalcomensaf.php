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
use app\models\Dashboardcategorias;
use app\models\Planprocesos;


$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
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

    $varTipoResp = (new \yii\db\Query())
    ->select(['COUNT(resp_encuesta_saf), resp.descripcion'])
    ->from(['tbl_encuesta_saf'])
    ->join('INNER JOIN', 'tbl_respuesta_encuesta_saf resp', 'resp.id_respuesta = tbl_encuesta_saf.resp_encuesta_saf')       
    ->where(['=','id_alerta',$id])
    ->groupBy(['resp_encuesta_saf'])
    ->all();

?>


<style>
    .card13 {
            height: 240px;
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
    
     .card12 {
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
    .card123 {
            height: 580px;
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
        background-image: url('../../images/Alertas-Valoración.png');
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
<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>

<script src="https://code.highcharts.com/modules/accessibility.js"></script>
<script src="http://code.highcharts.com/highcharts.js"></script>

<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>

<br>
<div class="capaInfo" id="idCapaInfo" style="display: inline;">

    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
		<div class="col-md-6" >
			<div class="card1 mb" style="background: #6b97b1; ">
				<label style="font-size: 20px; color: #FFFFFF;"><?php echo "Detalle de la Encuesta / Comentarios"; ?> </label><!--titulo principal de mi modulo-->
			</div>
		</div>
	</div>

    <br>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="myTablee" class="table table-hover table-bordered" style="margin-top:20px;" >
                    <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Resumen Comentarios ') ?></label></caption><!--Titulo de la tabla si se muestra-->
                        <thead><!--Emcabezados de la tabla -->
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;" ><?= Yii::t('app', 'Respuesta Encuesta') ?></label></th>
                            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px; width:140px;"><?= Yii::t('app', 'Comentario') ?></label></th>
                        </thead>
  
                        <tbody>

                        <?php  
                        
                        foreach ($DataInfo as $key => $value) {
                            
                        ?>
                            <tr>
                                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['descripcion']; ?></label></td>
                                <td class="text-center"><label style="font-size: 12px;"><?php echo $value['comentario_saf']; ?></label></td>
                            </tr>
                        <?php  }   ?>  
                        </tbody>
                </table>
            </div>
        </div>
    </div>
    <br><hr><br>
    <div class="row"><!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
		<div class="col-md-6" >
			<div class="card1 mb" style="background: #6b97b1; ">
				<label style="font-size: 20px; color: #FFFFFF;"><?php echo "Detalle de la Encuesta / Resultados"; ?> </label><!--titulo principal de mi modulo-->
			</div>
        </div>
	</div>
    <br><br>
	</div>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table style="width:100%">
                    <th scope="col" class="text-center" style="width: 100px;">
                        <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #ffc034;"></em> <?= Yii::t('app', 'Grafica Cantidad de Encuestas') ?></label>
                        <div id="containerA" class="highcharts-container" style="height:250px;"></div>        
                    </th>
                </table>
            </div>
        </div>
    </div>
    <br><hr><br>
    <div class="row">
        <div class="col-md-6"> 
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #707372;"></em> Cancelar y Regresar...</label><!-- label del titulo de lo que vamos a mostrar ------>
                <?= Html::a('Regresar',  ['veralertas','id'=>$id], ['class' => 'btn btn-success',//index es para que me redireccione o sea volver a el inicio 
                                                'style' => 'background-color: #707372',//color del boton  
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar'])//titulo del boton  
                ?>
            </div>
        </div>
    </div>
    <br><hr><br>
</div>

<script>

$('#containerA').highcharts({
            chart: {                
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Cantidad de Respuestas de la Encuesta'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }
            },

            xAxis: {
                  categories: " ",
                  title: {
                      text: null
                  }
                },

            series: [              
                
                <?php                         
                     foreach($varTipoResp as $key => $value){?>                        
                        {
                            name: "<?php echo $value['descripcion'];?>",
                            data: [<?php echo $value['COUNT(resp_encuesta_saf)'];?> ]                         
                        },
                        <?php }?> 
            ],
            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }        
          });
</script>

