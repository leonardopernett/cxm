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

$this->title = 'Monitoreo de categorización';
$this->params['breadcrumbs'][] = $this->title;



    $sessiones = Yii::$app->user->identity->id;
    $valor = null;

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

    $varBeginYear = '2019-01-01';
    $varLastYear = '2030-12-31';

    $varMonthYear = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

    $varListCorte = array();
    foreach ($varMonthYear as $key => $value) {
        $varListCort = $value['CorteTipo'];

        array_push($varListCorte, $varListCort);
    }

    $listData = ArrayHelper::map($varMonthYear, 'mesyear', 'CorteTipo');

    $varPcrc = Yii::$app->db->createCommand("select id_dp_clientes, nameArbol from tbl_speech_servicios where anulado = 0 and arbol_id != 1")->queryAll();
    $listData2 = ArrayHelper::map($varPcrc, 'id_dp_clientes', 'nameArbol');

    $varPorcentajeI = $varCateI;
    $varFaltaI = 100 - $varCateI;
    
    $varPorcentajeM = $varCateM;
    $varFaltaM = 100 - $varCateM;

    $varListServicios = array();

    if ($varPorcentajeI != 0 && $varPorcentajeM != 0) {
        $txtMesyear = $varMesyear;

        $varFechaInicio = $txtMesyear.' 05:00:00';
        $varFechaI = date("Y-m-t", strtotime($varFechaInicio));
        $varFechaF = date('Y-m-d',strtotime($varFechaI."+ 1 day"));
        $varFechaFin = $varFechaF.' 05:00:00';

        $txtListServicios = Yii::$app->db->createCommand("select id_dp_clientes, nameArbol from tbl_speech_servicios where anulado = 0 and arbol_id != 1")->queryAll();
        
        foreach ($txtListServicios as $key => $value) {
            $txtServicios = $value['nameArbol'];

            array_push($varListServicios, $txtServicios);
        }

    }

?>
<style type="text/css">
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
            font-family: 'Nunito',sans-serif;
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
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capaUno">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-object-group" style="font-size: 20px; color: #827DF9;"></em> Monitoreo de categorización general:</label>
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'inputOptions' => ['autocomplete' => 'off']
                      ]
                    ]); ?>
                <div class="col-md-12">
                    <?= $form->field($model2, 'mesyear')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'clienteID'])->label('Seleccionar Corte') ?>
                </div>
                <br>
                <div class="row" style="text-align: center;"> 
                    <?= Html::submitButton(Yii::t('app', 'Buscar Mes'),
                                                ['class' => $model2->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Buscar Corte']) 
                    ?>
                </div> 
                <?php ActiveForm::end(); ?>
                <br>
                <?php if ($varPorcentajeI != 0 && $varPorcentajeM != 0) { ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"> Porcentaje de categorización de Indicadores: </label>
                            <div id="containerI" class="highcharts-container" style="height: 300px;"></div> 
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"> Porcentaje de categorización de Motivos de contacto: </label>
                            <div id="containerM" class="highcharts-container" style="height: 300px;"></div>
                        </div>
                    </div>                    
                </div>
                <?php } ?>
            </div>
        </div>
    </div>
</div>
<hr>
<?php
    if ($sessiones == "2953") {
?>
<div class="capaTres">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></em> Procesos de categorización:</label>
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'inputOptions' => ['autocomplete' => 'off']
                      ]
                    ]); ?>
                <div class="col-md-12">
                    <?= $form->field($model2, 'mesyear')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'corteId'])->label('Seleccionar Corte') ?>
                </div>
                <br>
                <div class="row" style="text-align: center;"> 
                <br>
                    <div onclick="generated();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >  Registrar Categorizacion
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
<?php } ?>

<script type="text/javascript">
    function generated(){
        var varMes = document.getElementById("corteId").value;
        var varbtn = document.getElementById("botones2");

        varbtn.style.display = 'none';

        $.ajax({
            method: "get",
            url: "registrarcategorias",
            data: {
                txtvarMes : varMes,
            },
            success : function(response){
                var numRta =   JSON.parse(response);
                console.log(numRta);
                if (numRta == 0) {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
                    varbtn.style.display = 'inline';
                    return;
                }else{
                    varbtn.style.display = 'inline';
                }
            }
        });
    };

    $(function() {
        Highcharts.chart('containerI', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '<label style="font-size: 30px;"><?php echo round($varPorcentajeI,0).' %'; ?></label>',
                align: 'center',
                verticalAlign: 'middle',
                y: 60
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
                    center: ['50%', '75%'],
                    size: '150%'
                }
            },
            series: [{
                type: 'pie',
                name: '',
                innerSize: '50%',
                data: [
                    {
                        name: '',
                        y: parseFloat("<?php echo $varPorcentajeI;?>"),
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: '',
                        y: parseFloat("<?php echo $varFaltaI;?>"),
                        color: '#C6C6C6',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });


        Highcharts.chart('containerM', {
            chart: {
                plotBackgroundColor: null,
                plotBorderWidth: 0,
                plotShadow: false
            },
            title: {
                text: '<label style="font-size: 30px;"><?php echo round($varPorcentajeM,0).' %'; ?></label>',
                align: 'center',
                verticalAlign: 'middle',
                y: 60
            },
            tooltip: {
                pointFormat: '{series.name}: <b>{point.percentage:.1f}%</b>'
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
                    center: ['50%', '75%'],
                    size: '150%'
                }
            },
            series: [{
                type: 'pie',
                name: '',
                innerSize: '50%',
                data: [
                    {
                        name: '',
                        y: parseFloat("<?php echo $varPorcentajeM;?>"),
                        color: '#FFC72C',
                        dataLabels: {
                            enabled: false
                        }
                    },{
                        name: '',
                        y: parseFloat("<?php echo $varFaltaM;?>"),
                        color: '#C6C6C6',
                        dataLabels: {
                            enabled: false
                        }
                    }
                ]
            }]
        });

        var Listado = "<?php echo implode($varListServicios,",");?>";
        Listado = Listado.split(",");
        console.log(Listado);

        $('#conatinerG').highcharts({
            chart: {
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Indicadores y Porcentajes en '
              }
            }, 

            title: {
              text: '',
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
              name: 'Indicadores en servicios',
              data: [12],
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

    }); 
</script>