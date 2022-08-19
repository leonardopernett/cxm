<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Dashboard - evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

     $cantidadg = 10;
     $cantidadd = 10;
     $cantidada = 10;
     $idcontrol = $idopcion;
     $varListacompe = array();
     $varListanombre = array();
     $varListatotalarea = array();
     $varListatotalevaluado = array();
     $varListanombrearea = array();

     $varcantidadglobal = Yii::$app->db->createCommand("select COUNT(idresultado) FROM tbl_evaluacion_cumplimiento WHERE idtipoevalua NOT IN(4)")->queryScalar();

     $varcantidadrealizada = Yii::$app->db->createCommand("select COUNT(idresultado) FROM tbl_evaluacion_cumplimiento  WHERE idresultado = 1 and idtipoevalua NOT IN(4)")->queryScalar();
     $vartotalglobal = ($varcantidadrealizada * 100) / $varcantidadglobal;
     $vartotalresta = 100 - $vartotalglobal;

     
     $varlistaareas = Yii::$app->db->createCommand("select COUNT(directorarea)totalarea, sum(idresultado) totalevaluado, directorarea FROM tbl_evaluacion_cumplimiento WHERE directorarea IS NOT NULL AND idresultado IN(0,1) and idtipoevalua NOT IN(4) GROUP BY directorarea order by COUNT(directorarea) desc ")->queryAll();

      
     foreach ($varlistaareas as $key => $value) {
        $vartotalarea = $value['totalarea'];
        $vartotalevaluado = $value['totalevaluado'];
        $varclientearea = trim($value['directorarea']);
        array_push($varListatotalarea, $vartotalarea);
        array_push($varListatotalevaluado, $vartotalevaluado);
        array_push($varListanombrearea, $varclientearea);
    }

    $varlistaautoeval = Yii::$app->db->createCommand("SELECT COUNT(idtipoevalua) cantiautoeval, SUM(idresultado) cantievaluada  FROM tbl_evaluacion_cumplimiento WHERE idtipoevalua = 1 AND idresultado IN(0,1)")->queryAll();

      
     foreach ($varlistaautoeval as $key => $value) {
        $varcantiautoeval = $value['cantiautoeval'];
        $varcantievaluada = $value['cantievaluada'];
    }
    $varcantiautoeval = $varcantiautoeval - $varcantievaluada;


     if ($idbloque == 1) {
        $varEvaluacion = Yii::$app->db->createCommand("select FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', 
                                    ec.namecompetencia, eb.idevaluacionbloques, eb.namebloque
                                    FROM tbl_evaluacion_solucionado es
                                    INNER JOIN tbl_usuarios_evalua ue ON es.documentoevaluado = ue.documento
                                    INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                    inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                    INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                    INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques 
                                    WHERE eb.idevaluacionbloques = $idcontrol
                                    GROUP BY ec.namecompetencia 
                                    ORDER BY eb.idevaluacionbloques")->queryAll();       

        foreach ($varEvaluacion as $key => $value) {
            $varvalor = $value['%Competencia'];
            $varnombre = $value['namecompetencia'];
            array_push($varListacompe, $varvalor);
            array_push($varListanombre, $varnombre);
        }
    }

    if ($idbloque == 2) {
        $varEvaluacion = Yii::$app->db->createCommand("select FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', 
                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque
                                        FROM tbl_evaluacion_solucionado es
                                        INNER JOIN tbl_usuarios_evalua ue ON es.documentoevaluado = ue.documento
                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques
                                        WHERE en.nivel = $idcontrol
                                        GROUP BY ec.namecompetencia 
                                        ORDER BY eb.idevaluacionbloques")->queryAll();       

        foreach ($varEvaluacion as $key => $value) {
            $varvalor = $value['%Competencia'];
            $varnombre = $value['namecompetencia'];
            array_push($varListacompe, $varvalor);
            array_push($varListanombre, $varnombre);
        }
    }

    if ($idbloque == 3) {
        $varEvaluacion = Yii::$app->db->createCommand("select FORMAT((sum(er.valor)*100)/(count(es.idevaluacioncompetencia)*5),2) AS '%Competencia', 
                                        ec.namecompetencia, en.nivel, eb.idevaluacionbloques, eb.namebloque, ue.clientearea
                                        FROM tbl_evaluacion_solucionado es
                                        INNER JOIN tbl_usuarios_evalua ue ON es.documentoevaluado = ue.documento
                                        INNER JOIN tbl_evaluacion_respuestas2 er ON es.idevaluacionrespuesta = er.idevaluacionrespuesta
                                        inner join tbl_evaluacion_competencias ec  ON es.idevaluacioncompetencia = ec.idevaluacioncompetencia
                                        INNER JOIN tbl_evaluacion_nivel en ON ec.idevaluacionnivel = en.idevaluacionnivel
                                        INNER JOIN tbl_evaluacion_bloques eb ON es.idevaluacionbloques = eb.idevaluacionbloques                                                    
                                        left JOIN tbl_evaluacion_mensaje_resul ef ON es.idevaluacioncompetencia = ef.idevaluacioncompetencia
                                        WHERE ue.clientearea = '$idcontrol'
                                        GROUP BY ue.clientearea, ec.namecompetencia 
                                        ORDER BY eb.idevaluacionbloques")->queryAll();       

        foreach ($varEvaluacion as $key => $value) {
            $varvalor = $value['%Competencia'];
            $varnombre = $value['namecompetencia'];
            array_push($varListacompe, $varvalor);
            array_push($varListanombre, $varnombre);
        }
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

    .card3 {
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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }
    .card4 {
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
<?php
if($sessiones == "3205" || $sessiones == "3468" || $sessiones == "3229" || $sessiones == "2953" || $sessiones == "852" || $sessiones == "1483"|| $sessiones == "4201"|| $sessiones == "258"|| $sessiones == "4465" || $sessiones == "6080" || $sessiones == "57" || $sessiones == "69"){ ?>
<div class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="row">                
                <div class="col-md-4">
                    <div class="card4 mb">
                        <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #559FFF;"></em> Grafica cumplimiento Global: </label>
                        <div id="conatinerglobal" class="highcharts-container" style="height: 150px;">
                        </div> 
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card4 mb">
                        <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #559FFF;"></em> Grafica cumplimiento Auto Evaluados: </label>
                        <div id="conatinerauto" class="highcharts-container" style="height: 150px;">
                        </div> 
                    </div>
                </div>
                <div class="col-md-4" style="height: 150px;">
                    <div class="card4 mb">
                        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFAE58;"></em> Descargas: </label>
                        
                        <?= Html::button('Exportar', ['value' => url::to('exportarlist'), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado</h4>',
                                       'id' => 'modal1',
                                   ]);

                                   echo "<div id='modalContent1'></div>";
                                                                   
                                   Modal::end();
                               ?>
				<br>
                               <?= Html::button('Reporte Seguimiento feedback', ['value' => url::to('exportarseguimientofb'), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado</h4>',
                                       'id' => 'modal3',
                                   ]);

                                   echo "<div id='modalContent3'></div>";
                                                                   
                                   Modal::end();
                               ?>
				<br>
                               <?= Html::button('Reporte feedback', ['value' => url::to('exportarresultfb'), 'class' => 'btn btn-success', 'id'=>'modalButton5', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7'])
                               ?>
                               <?php
                                   Modal::begin([
                                       'header' => '<h4>Descargando listado</h4>',
                                       'id' => 'modal5',
                                   ]);

                                   echo "<div id='modalContent5'></div>";
                                                                   
                                   Modal::end();
                               ?>
                         
                    </div>
                </div>                 
            </div>		    
          </div>
     </div>
</div>



   <br>
 <div class="row">
 <br>
    <div class="col-md-12">
         <div class="card4 mb">
             <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #827DF9;"></em> Grafica Cumplimiento Dirección: </label>
             <div id="continerareaglobal" class="highcharts-container" style="height: 360px;">
             </div> 
         </div>
     </div>
</div>  
  

<hr>
<div class="CapaTres" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                        <?php $form = ActiveForm::begin([
                            'layout' => 'horizontal',
                            'fieldConfig' => [
                                'inputOptions' => ['autocomplete' => 'off']
                              ]
                            ]); ?> 
                            <div class="row">
                                <div class="col-md-6">
                                    <label for="txtCompetencias">Competencias</label>
                                    <select class ='form-control' id="txtCompetencias" data-toggle="tooltip" title="Competencias" onchange="listac();" class ='form-control'>
                                            <option value="" disabled selected>Seleccionar</option>                                   
                                            <option value="1">Bloque global</option>
                                            <option value="2">Bloque por nivel</option>
                                            <option value="3">Bloque por área</option>
                                    </select> 
                                </div>
                                <div class="col-md-6">
                                    <label for="txtOpciones">Opciones</label>
                                    <select class ='form-control' id="txtOpciones" data-toggle="tooltip" title="Opciones" onchange="listao();" class ='form-control'>
                                            <option value="" disabled selected>Seleccionar</option>
                                    </select>
                                </div>
                            </div>
                            <?= $form->field($model, 'idevaluacionnivel')->textInput(['class'=>'hidden','maxlength' => true, 'id'=>'idbloque'])  ?>
                            <?= $form->field($model, 'namecompetencia')->textInput(['class'=>'hidden','maxlength' => true, 'id'=>'idnombre'])  ?>
                                
                                <div class="row" class="text-center">  
                                        <?= Html::submitButton(Yii::t('app', 'Generar Grafica'),
                                                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                        'data-toggle' => 'tooltip', 'onclick' => 'openview();',
                                                        'title' => 'Generar Grafica']) 
                                        ?>
                                </div> 
                            
                        <?php ActiveForm::end(); ?> 
                        <br>
                        <?php
                        if ($idbloque) {
                        ?>
                        <div class="subcuartalinea">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="card3 mb">
                                        <table id="myTable"  class="table table-striped table-bordered detail-view formDinamico">
                                            <caption>Tabla datos</caption>
                                            <thead>
                                            <tr>
                                                <th scope="col" class="text-center" style="font-size: 15px; cursor: pointer" onclick="sortTable(0, 'str')"><?= Yii::t('app', 'Nombre Competencia') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                                                <th scope="col" class="text-center"  style="font-size: 15px; cursor: pointer" onclick="sortTable(1, 'int')"><?= Yii::t('app', '% Competencia') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                                                <th scope="col" class="text-center"  style="font-size: 15px; cursor: pointer" onclick="sortTable(4, 'int')"><?= Yii::t('app', 'Opción') ?><span class="glyphicon glyphicon-chevron-down"></span></th>
                                                
                                            </tr>           
                                            </thead>
                                            <tbody>
                                            <?php                            
                                                
                                                foreach ($varEvaluacion as $key => $value) {
                                                    $varvalor = $value['%Competencia'];
                                                    $varnombre = $value['namecompetencia'];
                                                    $varopcion1 = $value['namebloque'];
                                            ?>
                                                <tr>
                                                <td class="text-left"><?php echo $varnombre; ?></td>
                                                <td class="text-center"><?php echo $varvalor." %"; ?></td>
                                                <td class="text-center"><?php echo $varopcion1; ?></td>
                                                </tr>
                                            <?php
                                                }
                                            ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="card3 mb">
                                        <div id="containerbloques" class="highcharts-container"> 
                                        </div>
                                    </div>
                                </div>  
                            </div>                
                        </div>
                        <?php
                        }
                        ?>
                    
            </div>
        </div>
    </div>
</div>
<?php } else { ?>
  <div class="Seis">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
              <label><em class="fas fa-info-circle" style="font-size: 20px; color: #1e8da7;"></em> Información:</label>
              <label style="font-size: 14px;">No tiene los permisos para ingresar a esta opción.</label>
                </div><br>
            </div>
        </div>  
    </div>
  </div><br>

  <?php } ?>


<script type="text/javascript">
    $(function(){
        var Listado = "<?php echo implode($varListanombre,",");?>";
        Listado = Listado.split(",");        

        var Listado2 = "<?php echo implode($varListanombrearea,",");?>";
        Listado2 = Listado2.split(",");

        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

       $('#containerbloques').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: '% competencias'
              }
            }, 

            title: {
              text: 'Grafica de Competencias',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: '% Competencia',
              data: [<?= join($varListacompe, ',')?>],
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

         
        Highcharts.chart('conatinerglobal', {
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
                        name: 'Evaluados',
                        y: parseFloat("<?php echo $vartotalglobal;?>"),
                        color: '#59DE49',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'No evaluados',
                        y: parseFloat("<?php echo $vartotalresta;?>"),
                        color: '#fd7e14',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });

           Highcharts.chart('conatinerauto', {
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
                        name: 'Auto Evaluaciones realizadas',
                        y: parseFloat("<?php echo $varcantievaluada;?>"),
                        color: '#59DE49',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: 'Auto Evaluaciones no realizadas',
                        y: parseFloat("<?php echo $varcantiautoeval;?>"),
                        color: '#fd7e14',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });

        Highcharts.chart('continerareaglobal', {
              chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'column'
            },

            yAxis: {
              title: {
                text: 'Evaluados'
              }
            }, 

            title: {
              text: 'Cantidad de evaluaciones por dirección',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: Listado2,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Evaluaciones no realizadas',
              data: [<?= join($varListatotalarea, ',')?>],
              color: '#4298B5'
            },{
              name: 'Evaluaciones realizadas',
              data: [<?= join($varListatotalevaluado, ',')?>],
              color: '#3edb39'
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



        
    });

    function listac(){
    var varCompetencia = document.getElementById("txtCompetencias").value;
    
    
    $.ajax({
              method: "get",
              url: "listacompetencia",
              data : {
                txtCompetencia : varCompetencia,
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          document.getElementById("txtOpciones").innerHTML = "";
                          var node = document.createElement("OPTION");
                          node.setAttribute("value", "");
                          var textnode = document.createTextNode("Seleccionar");
                          node.appendChild(textnode);
                          document.getElementById("txtOpciones").appendChild(node);
                          for (var i = 0; i < Rta.length; i++) {
                              var node = document.createElement("OPTION");
                              node.setAttribute("value", Rta[i].id);
                              var textnode = document.createTextNode(Rta[i].nombre);
                              node.appendChild(textnode);
                              document.getElementById("txtOpciones").appendChild(node);
                          }
                      }
    });  
  };

  function listao(){
    var varCompetencia = document.getElementById("txtCompetencias").value;
    var varOpciones = document.getElementById("txtOpciones").value;
    

    $.ajax({
                method: "get",
                url: "listasopciones",
                data : {
                    txtvarCompetencia : varCompetencia,
                    txtvarOpciones : varOpciones,
                },
                success : function(response){ 
                    var Rta =   JSON.parse(response);    
                    document.getElementById("idbloque").value = Rta[0].idbloque;
                    document.getElementById("idnombre").value = Rta[0].idopcion;            
                     console.log(Rta[0].idbloque);
                     console.log(Rta[0].idopcion);
                }
            });
    
  };

  function openview(){
  var varselectindim = document.getElementById("txtCompetencias").value;
  var varselectvarm = document.getElementById("txtOpciones").value;

  if (varselectindim == "") {
    event.preventDefault();
    swal.fire("!!! Advertencia !!!","Debes de seleccionar una competencia.","warning");
    return;
  }else{
    if (varselectvarm == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debes de seleccionar una opción.","warning");
      return;
    }
  }
};

  function sortTable(n,type) {
    var table, rows, switching, i, x, y, shouldSwitch, dir, switchcount = 0;
   
    table = document.getElementById("myTable");
    switching = true;
    //Set the sorting direction to ascending:
    dir = "asc";
   
    /*Make a loop that will continue until no switching has been done:*/
    while (switching) {
      //start by saying: no switching is done:
      switching = false;
      rows = table.rows;
      /*Loop through all table rows (except the first, which contains table headers):*/
      for (i = 1; i < (rows.length - 1); i++) {
        //start by saying there should be no switching:
        shouldSwitch = false;
        /*Get the two elements you want to compare, one from current row and one from the next:*/
        x = rows[i].getElementsByTagName("TD")[n];
        y = rows[i + 1].getElementsByTagName("TD")[n];
        /*check if the two rows should switch place, based on the direction, asc or desc:*/
        if (dir == "asc") {
          if ((type=="str" && x.innerHTML.toLowerCase() > y.innerHTML.toLowerCase()) || (type=="int" && parseFloat(x.innerHTML) > parseFloat(y.innerHTML))) {
            //if so, mark as a switch and break the loop:
            shouldSwitch= true;
            break;
          }
        } else if (dir == "desc") {
          if ((type=="str" && x.innerHTML.toLowerCase() < y.innerHTML.toLowerCase()) || (type=="int" && parseFloat(x.innerHTML) < parseFloat(y.innerHTML))) {
            //if so, mark as a switch and break the loop:
            shouldSwitch = true;
            break;
          }
        }
      }
      if (shouldSwitch) {
        /*If a switch has been marked, make the switch and mark that a switch has been done:*/
        rows[i].parentNode.insertBefore(rows[i + 1], rows[i]);
        switching = true;
        //Each time a switch is done, increase this count by 1:
        switchcount ++;
      } else {
        /*If no switching has been done AND the direction is "asc", set the direction to "desc" and run the while loop again.*/
        if (switchcount == 0 && dir == "asc") {
          dir = "desc";
          switching = true;
        }
      }
    }
  };
</script>