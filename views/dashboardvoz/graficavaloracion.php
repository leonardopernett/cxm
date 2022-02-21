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

$this->title = 'Dashboard Ejecutivo';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Dashboard Ejecutivo (Programa VOC - Konecta)';


    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
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

    $varMes = date("n");
            $txtMes = null;
            switch ($varMes) {
                case '1':
                    $txtMes = "Enero";
                    break;
                case '2':
                    $txtMes = "Febrero";
                    break;
                case '3':
                    $txtMes = "Marzo";
                    break;
                case '4':
                    $txtMes = "Abril";
                    break;
                case '5':
                    $txtMes = "Mayo";
                    break;
                case '6':
                    $txtMes = "Junio";
                    break;
                case '7':
                    $txtMes = "Julio";
                    break;
                case '8':
                    $txtMes = "Agosto";
                    break;
                case '9':
                    $txtMes = "Septiembre";
                    break;
                case '10':
                    $txtMes = "Octubre";
                    break;
                case '11':
                    $txtMes = "Noviembre";
                    break;
                case '12':
                    $txtMes = "Diciembre";
                    break;
                default:
                    # code...
                    break;
            } 

    $varBeginYear = '2019-01-01';
    $varLastYear = '2030-12-31';

    $varMonthYear = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a   order by a.mesyear asc")->queryAll();

    $varListCorte = array();
    foreach ($varMonthYear as $key => $value) {
        $varListCort = $value['CorteTipo'];

        array_push($varListCorte, $varListCort);
    }

    $txtCiudades = Yii::$app->db->createCommand(" select id, name, arbol_id from tbl_arbols where id in (98, 2, 1)")->queryAll(); 
    $varListMonth2 = Yii::$app->db->createCommand("select distinct mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 6")->queryAll();                    

    $varFisrtDate = date($varListMonth2[0]["mesyear"]);
    $varLastDate = date($varListMonth2[2]["mesyear"]);

    $varListMonth = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

    $varListMonthMed = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

    $varListMonthBog = Yii::$app->db->createCommand("select mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

    $txtTotalKonecta = null;   
    $varListKonecta = array(); 
    $varListKonectaManual = array();
    $varListKonectaSpeech = array();   
    foreach ($varListMonth as $key => $value) {
        $varListYear = $value['mesyear']; 
            
        $txtQuery =  new Query;
        $txtQuery   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclientedq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
        $command = $txtQuery->createCommand();
        $txtTotalMonth1 = $command->queryScalar();



        $txtQuery1 =  new Query;
        $txtQuery1  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclienteds')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id in (2, 98)')
                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]);
        $command1 = $txtQuery1->createCommand();
        $txtTotalMonth2 = $command1->queryScalar(); 

        $txtTotalKonecta = $txtTotalMonth1 + $txtTotalMonth2;
        array_push($varListKonecta, $txtTotalKonecta);
        array_push($varListKonectaManual, $txtTotalMonth1);
        array_push($varListKonectaSpeech, $txtTotalMonth2);
    }

    $txtTotalMed = null;
    $varListMed = array();
    $varListMedManual = array();
    $varListMedSpeech = array();
    foreach ($varListMonthMed as $key => $value) {
        $varListYear = $value['mesyear']; 

        $txtQuery1 =  new Query;
        $txtQuery1   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclientedq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 2')
                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
        $command1 = $txtQuery1->createCommand();
        $txtTotalMonth11 = $command1->queryScalar();                       

        $txtQuery21 =  new Query;
        $txtQuery21  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclienteds')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 2')
                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]); 
        $command21 = $txtQuery21->createCommand();
        $txtTotalMonth21 = $command21->queryScalar(); 

        $txtTotalMed = $txtTotalMonth11 + $txtTotalMonth21;
        array_push($varListMed, $txtTotalMed);
        array_push($varListMedManual, $txtTotalMonth11);
        array_push($varListMedSpeech, $txtTotalMonth21);
    }

    $txtTotalBog = null;
    $varListBog = array();
    $varListBogManual = array();
    $varListBogSpeech = array();
    foreach ($varListMonthBog as $key => $value) {
        $varListYear = $value['mesyear']; 

        $txtQuery2 =  new Query;
        $txtQuery2   ->select(['sum(tbl_control_volumenxclientedq.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclientedq')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclientedq.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 98')
                    ->andwhere(['between','tbl_control_volumenxclientedq.mesyear', $varListYear, $varListYear]);
        $command2 = $txtQuery2->createCommand();
        $txtTotalMonth12 = $command2->queryScalar();                       

        $txtQuery22 =  new Query;
        $txtQuery22  ->select(['sum(tbl_control_volumenxclienteds.cantidadvalor)'])->distinct()
                    ->from('tbl_control_volumenxclienteds')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                'tbl_control_volumenxclienteds.idservicio = tbl_arbols.id')
                    ->where('tbl_arbols.arbol_id = 98')
                    ->andwhere(['between','tbl_control_volumenxclienteds.mesyear', $varListYear, $varListYear]); 
        $command22 = $txtQuery22->createCommand();
        $txtTotalMonth22 = $command22->queryScalar(); 

        $txtTotalBog = $txtTotalMonth12 + $txtTotalMonth22;
        array_push($varListBog, $txtTotalBog);
        array_push($varListBogManual, $txtTotalMonth12);
        array_push($varListBogSpeech, $txtTotalMonth22);
    }

?>
<div class="row">
    <div  class="col-md-12">
        <div class="well well-sm">
            <?=
                Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                        "class" => "glyphicon glyphicon-chevron-down",
                    ]) . " " . Html::encode('METRICA GENERAL'), "javascript:void(0)"
                    , ["class" => "openVistas", "id" => "graficar3", "style" => "text-transform: uppercase"])
            ?> 
        </div>  
    </div>
    <div id="dtbloque3" class="col-sm-12" style="display: none"> 
        <div id="conatinervaloraciones" class="highcharts-container" style="height: 350px;"></div>
    </div>

    <div  class="col-md-12">
        <div class="well well-sm">
            <?=
                Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                        "class" => "glyphicon glyphicon-chevron-down",
                    ]) . " " . Html::encode('NIVEL KONECTA'), "javascript:void(0)"
                    , ["class" => "openVistas", "id" => "graficar0", "style" => "text-transform: uppercase"])
            ?> 
        </div>  
    </div>
    <div id="dtbloque0" class="col-sm-12" style="display: none"> 
        <div id="conatinerkonecta" class="highcharts-container" style="height: 350px;"></div>
    </div>

    <div  class="col-md-12">
        <div class="well well-sm">
            <?=
                Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                        "class" => "glyphicon glyphicon-chevron-down",
                    ]) . " " . Html::encode('NIVEL MEDELLIN'), "javascript:void(0)"
                    , ["class" => "openVistas", "id" => "graficar1", "style" => "text-transform: uppercase"])
            ?> 
        </div>  
    </div>
    <div id="dtbloque1" class="col-sm-12" style="display: none"> 
        <div id="conatinermedellin" class="highcharts-container" style="height: 350px;"></div>
    </div>

    <div  class="col-md-12">
        <div class="well well-sm">
            <?=
                Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                        "class" => "glyphicon glyphicon-chevron-down",
                    ]) . " " . Html::encode('NIVEL BOGOTA'), "javascript:void(0)"
                    , ["class" => "openVistas", "id" => "graficar2", "style" => "text-transform: uppercase"])
            ?> 
        </div>  
    </div>
    <div id="dtbloque2" class="col-sm-12" style="display: none"> 
        <div id="conatinerbogota" class="highcharts-container" style="height: 350px;"></div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#graficar3").click(function () {
            $("#dtbloque3").toggle("slow");
        });

        $("#graficar0").click(function () {
            $("#dtbloque0").toggle("slow");
        });

        $("#graficar1").click(function () {
            $("#dtbloque1").toggle("slow");
        });

        $("#graficar2").click(function () {
            $("#dtbloque2").toggle("slow");
        });
        
    });

    $(function() {

        var Listado = "<?php echo implode($varListCorte,",");?>";
        Listado = Listado.split(",");
        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

        $('#conatinervaloraciones').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones (Manuales & Automaticas)'
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
              name: 'Cantidad Total Valoracion Nivel Konecta',
              data: [<?= implode($varListKonecta, ',')?>],
              color: '#4298B5'
            },{
              name: 'Cantidad Total Valoracion Medellin',
              data: [<?= implode($varListMed, ',')?>],
              color: '#615E9B'
            },{
              name: 'Cantidad Total Valoracion Bogota',
              data: [<?= implode($varListBog, ',')?>],
              color: '#FFc72C'
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

          $('#conatinerkonecta').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones Nivel Konecta (Manuales & Automaticas)'
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
              name: 'Cantidad Total Valoracion Nivel Konecta',
              data: [<?= implode($varListKonecta, ',')?>],
              color: '#4298B5'
            },{
              name: 'Cantidad Total Valoracion Manual Konecta',
              data: [<?= implode($varListKonectaManual, ',')?>],
              color: '#615E9B'
            },{
              name: 'Cantidad Total Valoracion Automatico Konecta',
              data: [<?= implode($varListKonectaSpeech, ',')?>],
              color: '#FFc72C'
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


          $('#conatinermedellin').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones Nivel Medellin (Manuales & Automaticas)'
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
              name: 'Cantidad Total Valoracion Nivel Medellin',
              data: [<?= implode($varListMed, ',')?>],
              color: '#4298B5'
            },{
              name: 'Cantidad Total Valoracion Manual Medellin',
              data: [<?= implode($varListMedManual, ',')?>],
              color: '#615E9B'
            },{
              name: 'Cantidad Total Valoracion Automatico Medellin',
              data: [<?= implode($varListMedSpeech, ',')?>],
              color: '#FFc72C'
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

          $('#conatinerbogota').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Valoraciones Nivel Bogota (Manuales & Automaticas)'
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
              name: 'Cantidad Total Valoracion Nivel Bogota',
              data: [<?= implode($varListBog, ',')?>],
              color: '#4298B5'
            },{
              name: 'Cantidad Total Valoracion Manual Bogota',
              data: [<?= implode($varListBogManual, ',')?>],
              color: '#615E9B'
            },{
              name: 'Cantidad Total Valoracion Automatico Bogota',
              data: [<?= implode($varListBogSpeech, ',')?>],
              color: '#FFc72C'
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



          Highcharts.getOptions().exporting.buttons.contextButton.menuItems.push({
            text: 'Additional Button',
            onclick: function() {
              alert('OK');
              /*call custom function here*/
            }
          });
    }); 
</script>