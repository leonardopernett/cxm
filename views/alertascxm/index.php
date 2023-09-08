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
use app\models\Dashboardservicios;

$this->title = 'Alertas - Gestor Administrativa';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                	'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
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
   	font-family: "Nunito",sans-serif;
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 180px;
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
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>

<br>
<br>

<?php
if ($roles == '270') {
?>
<!-- Capa Principal -->
<div class="capaPrincipal" id="capaIdPrincipal" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Gráficas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Registrar Alerta') ?></label>
                <?= Html::a('Registrar',  ['registraalerta', 'id_procesos'=>1], ['class' => 'btn btn-success',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Registrar Alerta']) 
                ?>
            </div>

            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Buscar Alertas') ?></label>
                <?= Html::a('Buscar',  ['reportealerta'], ['class' => 'btn btn-success',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Buscar Alertas']) 
                ?>
            </div>

            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Buscar Alertas Eliminadas') ?></label>
                <?= Html::a('Buscar',  ['reportealertaeliminadas'], ['class' => 'btn btn-success',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Buscar Alertas Eliminadas']) 
                ?>
            </div>

            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Descargar Datos') ?></label>
                <?= Html::a('Descargar',  ['descargaralertas'], ['class' => 'btn btn-danger',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Descargar Alertas']) 
                ?>
            </div>

            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Parametrizar Datos') ?></label>
                <?= Html::a('Parametrizar',  ['parametrizaralertas'], ['class' => 'btn btn-danger',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Parametrizar Alertas']) 
                ?>
            </div>
        </div>

        <div class="col-md-8">

            <div class="row">
                <div class="col-md-6">
                    <div class="card2 mb">
                        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Información Alertas') ?></label>
                        <label  style="font-size: 15px; text-align: left;"><?= Yii::t('app', ' Hola '.$varNameJarvis_Index.' te comentamos que el actual módulo te permite conocer los diferentes estados y acciones permitidos al modulo de Alertas 3.0.') ?></label>                
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card2 mb">
                        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Información Alertas Eliminadas') ?></label>
                        <label  style="font-size: 15px; text-align: left;"><?= Yii::t('app', ' Actualmente existen un total de '.$varEliminadasMesActual.' alertas eliminadas sobre el mes actual.') ?></label>                
                    </div>
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-md-6">
                    <div class="card2 mb">
                        <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Tipo de Alertas') ?></label>
                        <div id="containerA" class="highcharts-container" style="height: 150px;"></div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card2 mb">
                        <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Tipo de Encuestas') ?></label>
                        <div id="containerB" class="highcharts-container" style="height: 150px;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>

<?php
}else{
?>

<!-- Capa Informativa -->
<div class="capaInformativa" id="capaIdInformativa" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Notas Informativas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-2" align="text-center">
                        <label style="font-size: 15px;"><em class="fas fa-hand-stop" style="font-size: 50px; color: #FFC72C;"></em></label>
                    </div>

                    <div class="col-md-10" align="left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Ok, actualmente tu rol no te permite ingresar al módulo administrador de alertas. Por favor contactate con el administrador de la herramienta si necesitas ingresar a verificar el proceso del módulo.') ?></label>
                    </div>
                </div>          

            </div>        
        </div>

    </div>

</div>

<?php
}
?>
<hr>

<script type="text/javascript">
    Highcharts.chart('containerA', {
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
                <?php                         
                    foreach($varAlertasMesActual as $value){?>
                    {
                        name: "<?php echo $value['tipo_alerta'];?>",
                        y: parseFloat("<?php echo $value['varCantidadTipo'];?>"),                            
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php }?>
                        
            ]
        }]
    });

    Highcharts.chart('containerB', {
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
                <?php                         
                    foreach($varEncuestasMesActual as $value){?>
                    {
                        name: "<?php echo $value['tipoencuestas'];?>",
                        y: parseFloat("<?php echo $value['varCantidadEncuestas'];?>"),                            
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php }?>
                        
            ]
        }]
    });
</script>