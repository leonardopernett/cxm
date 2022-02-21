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

    if ($txtMesyear != null) {
        $varFechaInicio = $txtMesyear;
        $month = date('m',strtotime($txtMesyear));
        $year = date('Y',strtotime($txtMesyear));
        $days = date("d",(mktime(0,0,0,$month+1,1,$year)-1));
        $varFechaFin = $year.'-'.$month.'-'.$days;

        $txtArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$txtServicio' and activo = 0")->queryScalar();

        $txtCorte = Yii::$app->db->createCommand("select concat_ws('- ', CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1)) as CorteTipo from (select distinct substring_index(replace((replace(tipocortetc,'<b>','')),'</b>',''),'-',1) as CorteMes, substring_index(replace((replace(mesyear,'<b>','')),'</b>',''),'-',1) as CorteYear, mesyear from tbl_tipocortes where mesyear = '$txtMesyear' group by mesyear) a where CorteMes not like '%$txtMes%'")->queryScalar();


        if ($txtServicio == '1') {
            $txtListDays = Yii::$app->db->createCommand("select distinct sum(tbl_control_volumenxencuestasdq.cantidadvalor) as cantidadvalor, tbl_control_volumenxencuestasdq.fechaencuesta from tbl_control_volumenxencuestasdq inner join tbl_arbols on tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id where tbl_arbols.arbol_id in (2,98) and tbl_control_volumenxencuestasdq.fechaencuesta between '$varFechaInicio' and '$varFechaFin' group by tbl_control_volumenxencuestasdq.fechaencuesta")->queryAll();
        }else{
            $txtListDays = Yii::$app->db->createCommand("select distinct sum(tbl_control_volumenxencuestasdq.cantidadvalor) as cantidadvalor, tbl_control_volumenxencuestasdq.fechaencuesta from tbl_control_volumenxencuestasdq inner join tbl_arbols on tbl_control_volumenxencuestasdq.idservicio = tbl_arbols.id where tbl_arbols.arbol_id in ($txtServicio) and tbl_control_volumenxencuestasdq.fechaencuesta between '$varFechaInicio' and '$varFechaFin' group by tbl_control_volumenxencuestasdq.fechaencuesta")->queryAll();
        }

        foreach ($txtListDays as $key => $value) {
            $txtDias = $value['fechaencuesta'];
            $txtValor = $value['cantidadvalor'];

            array_push($varListDays, $txtDias);
            array_push($varListCant, $txtValor);
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
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<div class="page-header">
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
</div> 
<br>
<div id="dtbloque1" class="col-sm-12" style="display: inline">
    <div id="idCapa0" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #C148D0;"></em> Métricas de encuestas en detalle por dia:</label>
                    <?php $form = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'inputOptions' => ['autocomplete' => 'off']
                          ]
                        ]); ?>
                        <div class="col-md-6">
                            <?=  $form->field($model, 'idservicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Arboles::find()->distinct()->where("id in (98, 2, 1)")->andwhere("activo = 0")->orderBy(['id'=> SORT_ASC])->all(), 'id', 'name'),
                                                        [
                                                            'prompt'=>'Seleccionar Nivel...',

                                                        ]
                                                )->label('Seleccionar Nivel'); 
                            ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'mesyear', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'clienteID'])->label('Seleccionar Corte') ?>
                        </div>
                        <br>
                        <br> 
                        <div class="row" style="text-align: center;"> 
                            <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Buscar Corte',
                                                    'onclick'=> 'verificar();']) 
                            ?>
                            &nbsp;
                            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success', 'style' => 'background-color: #707372', 'data-toggle' => 'tooltip', 'title' => 'Regresar']) 
                            ?>
                        </div> 

                    <?php ActiveForm::end(); ?> 

                    <?php 
                        if ($txtMesyear != null  && $txtServicio != null) {
                    ?>
                        
                            <div id="idCapa1" style="display: inline">
                                <br>
                                <br>
                                    <div id="conatinervaloraciones" class="highcharts-container" style="height: 350px;"></div>
                                <br>
                                <br>
                            </div>     
                        
                    <?php
                        }
                    ?>
                </div>
                <br>
            </div>
        </div>    
    </div>
</div>                

<script type="text/javascript">
    
    function verificar(){
        var varNivel = document.getElementById("controlvolumenxclientedq-idservicio").value;
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
                text: 'Cantidad Encuestas'
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
                name: 'Cantidad Total Encuestas Por Dias',
                data: [<?= implode($varListCant, ',')?>],
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