<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Gestion de novedades - evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $varEvaluacion = Yii::$app->get('dbslave')->createCommand("select * from tbl_evaluacion_tipoeval where anulado = 0")->queryAll();
    $varListCorte = array();
    foreach ($varEvaluacion as $key => $value) {

        array_push($varListCorte, $value['tipoevaluacion']);
    }

    $vartotalAuto = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesauto where anulado = 0")->queryScalar();
    $vartotalJefe = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesjefe where anulado = 0")->queryScalar();
    $vartotalPar = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadescargo where anulado = 0")->queryScalar();
    $vartotalCargo = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadespares where anulado = 0")->queryScalar();


    $varaprobadasa = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesauto where anulado = 0 and aprobado = 1")->queryScalar();
    $varreprobadasa = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesauto where anulado = 0 and aprobado = 0")->queryScalar();
    $varnoaprobadasa = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesauto where anulado = 0 and aprobado = 2")->queryScalar();
    $varlistnovedadesa = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadesauto where anulado = 0")->queryAll();
    if ($vartotalAuto == 0) {
        $varPorcentajea = 0;
    }else{
        $varPorcentajea = round(($varaprobadasa/$vartotalAuto)*100);    
    }
    
    $varaprobadasj = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesjefe where anulado = 0 and aprobado = 1")->queryScalar();
    $varreprobadasj = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesjefe where anulado = 0 and aprobado = 0")->queryScalar();
    $varnoaprobadasj = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadesjefe where anulado = 0 and aprobado = 2")->queryScalar();
    $varlistnovedadesj = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadesjefe where anulado = 0")->queryAll();
    if ($vartotalJefe == 0) {
        $varPorcentajej = 0;
    }else{
        $varPorcentajej = round(($varaprobadasj/$vartotalJefe)*100);    
    }

    $varaprobadasp = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadescargo where anulado = 0 and aprobado = 1")->queryScalar();
    $varreprobadasp = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadescargo where anulado = 0 and aprobado = 0")->queryScalar();
    $varnoaprobadasp = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadescargo where anulado = 0 and aprobado = 2")->queryScalar();
    $varlistnovedadesp = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadescargo where anulado = 0")->queryAll();
    if ($vartotalPar == 0) {
        $varPorcentajep = 0;
    }else{
        $varPorcentajep = round(($varaprobadasp/$vartotalPar)*100);    
    }


    $varaprobadasc = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadespares where anulado = 0 and aprobado = 1")->queryScalar();
    $varreprobadasc = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadespares where anulado = 0 and aprobado = 0")->queryScalar();
    $varnoaprobadasc = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_novedadespares where anulado = 0 and aprobado = 2")->queryScalar();
    $varlistnovedadesc = Yii::$app->db->createCommand("select * from tbl_evaluacion_novedadespares where anulado = 0")->queryAll();
    if ($vartotalCargo == 0) {
        $varPorcentajec = 0;
    }else{
        $varPorcentajec = round(($varaprobadasc/$vartotalCargo)*100);    
    }
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
            height: 170px;
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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<script src="../../js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-eye" style="font-size: 15px; color: #FFC72C;"></em> Gestionar novedades: </label>
                    <?= Html::button('Verificar', ['value' => url::to(['evaluaciondesarrollo/novedadgeneral']), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Verficar', 'style' => 'background-color: #337ab7']) 
                    ?> 

                    <?php
                                Modal::begin([
                                    'header' => '<h4></h4>',
                                    'id' => 'modal3',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent3'></div>";
                                                                              
                                Modal::end(); 
                    ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-eye" style="font-size: 15px; color: #FFC72C;"></em> Gestionar eliminación: </label>
                    <?= Html::a('Verificar',  ['eliminarnovedades'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #337ab7',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Varificar']) 
                    ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-exclamation-triangle" style="font-size: 15px; color: #FF6522;"></em> Eliminar novedades de usuarios: </label>
                    <?= Html::a('Eliminar',  ['eliminarusuarios'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #337ab7',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Eliminar']) 
                    ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-key" style="font-size: 15px; color: #ffd43b;"></em> Habilitar pares: </label>
                    <?= Html::a('Habilitar',  ['habilitarpares'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #337ab7',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Habilitar']) 
                    ?>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 15px; color: #fd7e14;"></em> Grafica general: </label>
                    <div id="conatinergeneric" class="highcharts-container" style="height: 360px;"></div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-6">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #559FFF;"></em> Grafica autoevaluación: </label>
                        <div id="containerA" class="highcharts-container" style="height: 150px;"></div> 
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #827DF9;"></em> Grafica evaluación Jefe: </label>
                        <div id="containerJ" class="highcharts-container" style="height: 150px;"></div> 
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-6">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #C148D0;"></em> Grafica evaluación Par: </label>
                        <div id="containerP" class="highcharts-container" style="height: 150px;"></div> 
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #FFC72C;"></em> Grafica evaluación a cargo: </label>
                        <div id="containerC" class="highcharts-container" style="height: 150px;"></div> 
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>
<hrem
<div class="CapaTres" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <div class="row">
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-body" style="background-color: #f0f8ff;">
                                <label style="font-size: 20px;"> Importante</label>
                                    <br>
                                <label style="font-size: 15px;"> Este visual permite generar como va el proceso de las novedades en cuanto a cantidades; ya sean a nivel general o por cada evaluación.</label>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-body" style="background-color: #f0f8ff;">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><em class="fas fa-square" style="font-size: 20px; color: #59DE49;"></em></em> Novedades aprobadas</label>
                                    </div>
                                    <div class="col-md-6">
                                        <label><em class="fas fa-square" style="font-size: 20px; color: #fd7e14;"></em></em> Novedades no aprobadas </label>
                                    </div>
                                </div>
                                <br>
                                <div class="row">
                                    <div class="col-md-6">
                                        <label><em class="fas fa-square" style="font-size: 20px; color: #FFC251;"></em></em> Novedades en espera</label>
                                    </div>
                                    <div class="col-md-6">
                                        <label><em class="fas fa-square" style="font-size: 20px; color: #FFFFFF;"></em></em> Sin novedades</label>
                                    </div>
                                </div>  
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>

<script type="text/javascript">
    $(function(){
        var Listado = "<?php echo implode($varListCorte,",");?>";
        Listado = Listado.split(",");
        //console.log(Listado);

        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

        $('#conatinergeneric').highcharts({
            chart: {                
                type: 'bar'
            },

            yAxis: {
              title: {
                text: 'Cantidad de novedades'
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

            series: [{
              name: 'Novedades de  Autoevaluación',
              data: [<?= $vartotalAuto?>],
              color: '#559FFF'
            },{
              name: 'Novedades de  Evaluación Jefe',
              data: [<?= $vartotalJefe?>],
              color: '#827DF9'
            },{
              name: 'Novedades de  Evaluación Pares',
              data: [<?= $vartotalCargo?>],
              color: '#C148D0'
            },{
              name: 'Novedades de  Evaluación a Cargo',
              data: [<?= $vartotalPar?>],
              color: '#FFC72C'
            }],

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
                    {
                        name: 'Aprobadas',
                        y: parseFloat("<?php echo $varaprobadasa;?>"),
                        color: '#59DE49',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'No Aprobadas',
                        y: parseFloat("<?php echo $varnoaprobadasa;?>"),
                        color: '#fd7e14',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'Sin aprobar',
                        y: parseFloat("<?php echo $varreprobadasa;?>"),
                        color: '#FFC251',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });

        Highcharts.chart('containerJ', {
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
                        name: 'Aprobadas',
                        y: parseFloat("<?php echo $varaprobadasj;?>"),
                        color: '#59DE49',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'No Aprobadas',
                        y: parseFloat("<?php echo $varnoaprobadasj;?>"),
                        color: '#fd7e14',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'Sin aprobar',
                        y: parseFloat("<?php echo $varreprobadasj;?>"),
                        color: '#FFC251',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });

        Highcharts.chart('containerP', {
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
                        name: 'Aprobadas',
                        y: parseFloat("<?php echo $varaprobadasc;?>"),
                        color: '#59DE49',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'No Aprobadas',
                        y: parseFloat("<?php echo $varnoaprobadasc;?>"),
                        color: '#fd7e14',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'Sin aprobar',
                        y: parseFloat("<?php echo $varreprobadasc;?>"),
                        color: '#FFC251',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });

        Highcharts.chart('containerC', {
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
                        name: 'Aprobadas',
                        y: parseFloat("<?php echo $varaprobadasp;?>"),
                        color: '#59DE49',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'No Aprobadas',
                        y: parseFloat("<?php echo $varnoaprobadasp;?>"),
                        color: '#fd7e14',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'Sin aprobar',
                        y: parseFloat("<?php echo $varreprobadasp;?>"),
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