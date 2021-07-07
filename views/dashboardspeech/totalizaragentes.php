<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;

$this->title = 'DashBoard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'DashBoard Voz del Cliente';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;
    
    $txtlistatopvar = Yii::$app->db->createCommand("SELECT t.usuario_red, COUNT(t.usuario_red), SUM(t.subtotalagente), FORMAT((SUM(t.subtotalagente) * 100) / COUNT(t.usuario_red),2) AS promedio 
                                                    FROM tbl_tmpcategoriaagente t 
                                                    WHERE t.fecha_ini >= '$varFechaI' AND t.fecha_fin <= '$varFechaF'
                                                    AND t.id_pcrc = '$varCodigPcrc'
                                                    GROUP BY t.usuario_red ORDER BY promedio desc")->queryAll();

    /*$varListLogin0 =  array();
    $varListCantiVar0 =  array();
    $varListidcateAgente = array();
    $vartotallogin = 0;*/
    

    $varListLogin =  array();
    $varListCantiVar =  array();
    $varListLogin5 =  array();
    $varListCantiVar5 =  array();
    $varListLogin0 =  array();
    $varListCantiVar0 =  array();
    $varListidcateAgente = array();
    $vartotallogin = 0;
    $canlogin = 0;
    $contador = 0;

    $vartotalreg = count($txtlistatopvar);
    if($vartotalreg > 20){
      $varulti = 9;
      $varprim = 11;
    } else {
      $varulti = 4;
      $varprim = 6;
    }
    $varultimo5 = $vartotalreg - $varulti;
    foreach ($txtlistatopvar as $key => $value) {
      $varlogin = $value['usuario_red'];      
      $varcantivar = $value['promedio'];
      $canlogin = substr_count($varlogin,".");
      
      if($canlogin > 0){
        $vartotallogin = $vartotallogin + 1;
      }
      $contador += 1;
      if($contador < $varprim){
        array_push($varListLogin, $varlogin);
        array_push($varListCantiVar, $varcantivar);
      }
      if($contador == $varultimo5){
        $varultimo5 += 1;
        array_push($varListLogin5, $varlogin);
        array_push($varListCantiVar5, $varcantivar);
      }
      if($vartotalreg < 11){
        array_push($varListLogin0, $varlogin);
        array_push($varListCantiVar0, $varcantivar);
      }
      
    }



    /*foreach ($txtlistatopvar as $key => $value) {
        $varlogin = $value['usuario_red'];      
        $varcateAgente = $value['promedio'];
        $vartotallogin = $vartotallogin + 1;
        array_push($varListLogin0, $varlogin);
        array_push($varListidcateAgente, $varcateAgente);
    }*/

?>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/font_awesome_local/css.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card {
            height: 200px;
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
            font-family: "Nunito";
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card2 {
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
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }
    .card3 {
           height: 260px;
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
           font-family: "Nunito";
           font-size: 150%;    
           text-align: left;    
   }

    .card:hover .card1:hover {
        top: -15%;
    }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Dashboard-Escuchar-+.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>

<div id="idCapaUno" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 16px;"><i class="far fa-chart-bar" style="font-size: 20px; color: #4D83FE;"></i> Reporte... </label>
               
                <?php
              if ($vartotalreg != 0) {
                if($vartotalreg > 10){
            ?>
                <div class="subcuartalinea">
                  <div class="row">
                    <div class="col-md-6">
                      <div class="card2 mb">
                        <div id="containerTOP" class="highcharts-container"></div>
                      </div>
                    </div>
                    <div class="col-md-6">
                      <div class="card2 mb">
                        <div id="containerTOP5" class="highcharts-container"></div>
                      </div>
                    </div>
                  </div>
                </div>
            <?php } else { ?>
                <div class="subcuartalinea">
                  <div class="row">
                    <div class="col-md-12">
                      <div class="card2 mb">
                        <div id="containerTOP0" class="highcharts-container"></div>
                      </div>
                    </div>
                  </div>
                </div>
            <?php 
                }
              }
            ?>
          </div>
        </div>      
    </div>
</div>

<script type="text/javascript">
$(function() {
        
        // Para Agentes       
        
        var ListadoL = "<?php echo implode($varListLogin,",");?>";
        ListadoL = ListadoL.split(",");        

          var ListadoL5 = "<?php echo implode($varListLogin5,",");?>";
        ListadoL5 = ListadoL5.split(",");        

        var ListadoL0 = "<?php echo implode($varListLogin0,",");?>";
        ListadoL0 = ListadoL0.split(",");



        $('#containerTOP5').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: '% Resultado'
              }
            }, 

            title: {
              text: 'Grafica Top de Agente Menores resultados / ',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: ListadoL5,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: '% Agentes',
              data: [<?= join($varListCantiVar5, ',')?>],
              color: '#4298B5'
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

          $('#containerTOP').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: '% Resultado'
              }
            }, 

            title: {
              text: 'Grafica Top de Agente Mejores Resultados',
            style: {
                    color: '#ffa126'
              }

            },

            xAxis: {
                  categories: ListadoL,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: '% Agentes',
              data: [<?= join($varListCantiVar, ',')?>],
              color: '#ffa126'
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

          $('#containerTOP0').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: '% Resultados'
              }
            }, 

            title: {
              text: 'Grafica Top de Agentes Resultados ',
            style: {
                    color: '#4298B5'
              }

            },

            xAxis: {
                  categories: ListadoL0,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: '% Agentes',
              data: [<?= join($varListCantiVar0, ',')?>],
              color: '#ffa126'
            }],


            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 300
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
        });

</script>