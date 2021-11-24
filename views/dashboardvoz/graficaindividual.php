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

    $varMonthYear = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo, mesyear from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear between '$varBeginYear' and '$varLastYear' group by mesyear order by mesyear desc limit 7) a order by a.mesyear asc")->queryAll();

    $varListCorte = array();
    foreach ($varMonthYear as $key => $value) {
        $varListCort = $value['CorteTipo'];

        array_push($varListCorte, $varListCort);
    }

    $listData = ArrayHelper::map($varMonthYear, 'mesyear', 'CorteTipo');

    $txtMesyear = $varMesyear;
    $txtServicio = $varServicio; 
    $varFechaInicio = null;
    $month = null;
    $year = null;
    $days = null;
    $varFechaFin = null;
    $txtArbol = null;
    $txtCorte = null;
    $varListDays = array();
    $varListCant = array();
    $varListDaysAuto = array();
    $varListCantAuto = array();
    $varListDaysq = array();
    $varListCantq = array();

    $varMesYearActual = date('Y').'-'.date('m').'-01';

    $txtArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtServicio' and activo = 0")->queryScalar();

    if ($txtMesyear != null) {
        $varFechaInicio = $txtMesyear;
        $month = date('m',strtotime($txtMesyear));
        $year = date('Y',strtotime($txtMesyear));
        $days = date("d",(mktime(0,0,0,$month+1,1,$year)-1));
        $varFechaFin = $year.'-'.$month.'-'.$days;

        

        $txtCorte = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear = '$txtMesyear' group by mesyear) a where CorteMes not like '%$txtMes%'")->queryScalar();

        
        $txtListDays = Yii::$app->db->createCommand("select distinct sum(tbl_control_volumenxclientedq.cantidadvalor) as cantidadvalor, tbl_control_volumenxclientedq.fechavaloracion from tbl_control_volumenxclientedq inner join tbl_arbols on tbl_control_volumenxclientedq.idservicio = tbl_arbols.id where tbl_arbols.id in ($varServicio) and tbl_control_volumenxclientedq.fechavaloracion between '$varFechaInicio' and '$varFechaFin' group by tbl_control_volumenxclientedq.fechavaloracion")->queryAll();

        $txtListDaysAuto = Yii::$app->db->createCommand("select distinct sum(tbl_control_volumenxclienteds.cantidadvalor) as cantidadvalor, tbl_control_volumenxclienteds.fechavaloracion from tbl_control_volumenxclienteds inner join tbl_arbols on tbl_control_volumenxclienteds.idservicio = tbl_arbols.id where tbl_arbols.id in ($varServicio) and tbl_control_volumenxclienteds.fechavaloracion between '$varFechaInicio' and '$varFechaFin' group by tbl_control_volumenxclienteds.fechavaloracion")->queryAll();

               
        
        foreach ($txtListDays as $key => $value) {
            $txtDias = $value['fechavaloracion'];
            $txtValor = $value['cantidadvalor'];

            array_push($varListDays, $txtDias);
            array_push($varListCant, $txtValor);
        }

        foreach ($txtListDaysAuto as $key => $value) {
            $txtDiasAuto = $value['fechavaloracion'];
            $txtValorAuto = $value['cantidadvalor'];

            array_push($varListDaysAuto, $txtDiasAuto);
            array_push($varListCantAuto, $txtValorAuto);
        }

        $txtListDaysq = Yii::$app->db->createCommand("select distinct sum(tbl_control_volumenxencuestasdq.cantidadvalor) as cantidadvalor, tbl_control_volumenxencuestasdq.fechaencuesta from tbl_control_volumenxencuestasdq inner join tbl_arbols on tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id where tbl_arbols.id in ($varServicio) and tbl_control_volumenxencuestasdq.fechaencuesta between '$varFechaInicio' and '$varFechaFin' group by tbl_control_volumenxencuestasdq.fechaencuesta")->queryAll();

        foreach ($txtListDaysq as $key => $value) {
            $txtDias = $value['fechaencuesta'];
            $txtValor = $value['cantidadvalor'];

            array_push($varListDaysq, $txtDias);
            array_push($varListCantq, $txtValor);
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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }
</style>
<script src="../../web/js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../web/js_extensions/highcharts/highcharts.js"></script>
<script src="../../web/js_extensions/highcharts/exporting.js"></script>

<div class="page-header">
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
</div> 

<div id="dtbloque1" class="col-sm-12" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #C148D0;"></em> Métricas de experiencia emitida y percibia en detalle por dia:</label>

                <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                    <div class="col-md-6">
                        <?= $form->field($model, 'idservicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'value' =>$txtArbol, 'id'=>'id_Service'])->label('Servicio') ?>
                        
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'mesyear', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'clienteID'])->label('Seleccionar Corte') ?>
                    </div>
                    <?= $form->field($model, 'idservicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'class' => 'hidden', 'value' =>$txtServicio, 'id'=>'id_Service2']) ?>
                    <div class="row" align="center">
                    
                        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Buscar Corte',
                                                'onclick'=> 'verificar();']) 
                        ?>
                        &nbsp;
                        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #707372',
                                'data-toggle' => 'tooltip',
                                'title' => 'Regresar']) 
                        ?>
                    </div> 
                <?php ActiveForm::end(); ?> 

                <?php 
                    if ($txtMesyear != null  && $txtServicio != null) {
                ?>
                    
                        <div id="idCapa1" style="display: inline">
                            <br> 
                                <div id="conatinervaloraciones" class="highcharts-container" style="height: 350px;"></div>
                            <br>
                            <br>
                        </div>   
                        <div id="idCapa" style="display: inline">
                            <br> 
                                <div id="conatinerencuestas" class="highcharts-container" style="height: 350px;"></div>
                            <br>
                            <br>
                        </div>   
                   
                <?php
                    }
                ?>
            </div>
        </div>
        <br>
    </div>
</div>



<script type="text/javascript">
    $(document).ready(function () {
        $("#graficar").click(function () {
            $("#dtbloque4").toggle("slow");
        });

        $("#graficar1").click(function () {
            $("#dtbloque1").toggle("slow");
        });        
    });

    function verificar(){
        var varNivel = document.getElementById("id_Service2").value;
        var varCorte = document.getElementById("clienteID").value;

        if (varNivel == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar el nivel.","warning");
            return; 
        }else{
            if (varCorte == "") {
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar el corte.","warning");
                return; 
            }
        }
    };

    $(function() {
        var Listado = "<?php echo implode($varListDays,",");?>";
        Listado = Listado.split(",");

        var Listadoq = "<?php echo implode($varListDaysq,",");?>";
        Listadoq = Listadoq.split(",");
        //console.log(Listado);

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
                text: 'Cantidad Valoraciones Por Dias (Manuales & Automaticas)'
              }
            }, 

            title: {
              text: 'Nivel <?php echo $txtArbol; ?>'+' -- '+'<?php echo $txtCorte; ?>',
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
                name: 'Cantidad Total Valoracion Manuales Por Dias',
                data: [<?= join($varListCant, ',')?>],
                color: '#4298B5'
            }, {
                name: 'Cantidad Total Valoracion Automaticas Por Dias',
                data: [<?= join($varListCantAuto, ',')?>],
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

        $('#conatinerencuestas').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Encuestas por Dias'
              }
            }, 

            title: {
              text: 'Nivel <?php echo $txtArbol; ?>'+' -- '+'<?php echo $txtCorte; ?>',
            style: {
                    color: '#3C74AA'
              }
            },

            xAxis: {
                  categories: Listadoq,
                  title: {
                      text: null
                  }
                },

            series: [{
                name: 'Cantidad Encuestas Por Dias',
                data: [<?= join($varListCantq, ',')?>],
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

        Highcharts.getOptions().exporting.buttons.contextButton.menuItems.push({
            text: 'Additional Button',
            onclick: function() {
              alert('OK');
              /*call custom function here*/
            }
          });
    });

</script>