<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

use yii\db\Query;
use app\models\SpeechCategorias;
use app\models\Dashboardservicios;

$this->title = 'Envío de Alertas - Participación Encuestas';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from(['tbl_roles'])
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $varListDias = array();
    $varListCantidad = array();
    foreach ($varDataList as $key => $value) {
        array_push($varListDias, $value['fecha']);
        array_push($varListCantidad, $value['conteo']);
    }

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .lds-ring {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 100px;
    }
    .lds-ring div {
      box-sizing: border-box;
      display: block;
      position: absolute;
      width: 80px;
      height: 80px;
      margin: 10px;
      border: 10px solid #3498db;
      border-radius: 70%;
      animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      border-color: #3498db transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
      animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
      animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
      animation-delay: -0.15s;
    }
    @keyframes lds-ring {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

</style>
<!-- Extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>

<!-- Capa Procesos del encabezado -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>

<!-- Capa Informacion Dash -->
<div id="capaInfoId" class="capaInfo" style="display: inline;">
    
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
    <div class="col-md-3">
        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Actualizar Personal') ?></label>
            
            <?= $form->field($model, 'fechacreacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['class' => 'hidden', 'value' => date('Y-m-d')])->label('') ?>

            <?= Html::submitButton(Yii::t('app', 'Actualizar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                // 'onclick' => 'varOpciones();',
                                'title' => 'Procesar Distribucion']) 
            ?> 
            
        </div>

        <br>

        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Agregar Nuevo Personal') ?></label>

            <div class="text-center">
              <?= 
                Html::a(Yii::t('app', 'Agregar [+]'),
                            'javascript:void(0)',
                            [
                                'title' => Yii::t('app', 'Agregar Integrante'),
                                'onclick' => "
                                    $.ajax({
                                        type     :'get',
                                        cache    : false,
                                        url  : '" . Url::to(['addpersonal']) . "',
                                        success  : function(response) {
                                            $('#ajax_result').html(response);
                                        }
                                    });
                                return false;",
                ]);
              ?>
            </div>

        </div>

        <br>

        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-envelope" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Envio de Alertas') ?></label>

            <div class="text-center">
              <?= 
                Html::a(Yii::t('app', 'Enviar [+]'),
                            'javascript:void(0)',
                            [
                                'title' => Yii::t('app', 'Enviar alertas'),
                                'onclick' => "
                                    $.ajax({
                                        type     :'get',
                                        cache    : false,
                                        url  : '" . Url::to(['sendalerts']) . "',
                                        success  : function(response) {
                                            $('#ajax_result_send').html(response);
                                        }
                                    });
                                return false;",
                ]);
              ?>
            </div>

        </div>

        <br>

        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Envio de Alertas Masivos - Excel') ?></label>

            <div class="text-center">
              <?= 
                Html::a(Yii::t('app', 'Enviar Masivo [+]'),
                            'javascript:void(0)',
                            [
                                'title' => Yii::t('app', 'Enviar alertas masivo'),
                                'onclick' => "
                                    $.ajax({
                                        type     :'get',
                                        cache    : false,
                                        url  : '" . Url::to(['sendalertsmore']) . "',
                                        success  : function(response) {
                                            $('#ajax_result_sendmore').html(response);
                                        }
                                    });
                                return false;",
                ]);
              ?>
            </div>

        </div>

        <br>

        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Reportes de Envios') ?></label>

            <?= Html::a('Reportes de Envios',  ['enviarreportes'], ['class' => 'btn btn-success',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Enviar Reportes']) 
            ?>
           

        </div>

        <br>

        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Reportes de No Envios') ?></label>

            <?= Html::a('Reportes de No Envios',  ['enviarnoreportes'], ['class' => 'btn btn-success',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Enviar No Reportes']) 
            ?>

        </div>

        <br>

        <div class="card1 mb">
            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
            ?>
        </div>

    </div>

    <div class="col-md-9">

        <div class="row">
            <div class="col-md-4">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Última Fecha de Envio') ?></label>
                    <label  style="font-size: 40px; text-align: center;"><?php echo $varUltimaFecha; ?></label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Cantidad de Usuarios Registrados') ?></label>
                    <label  style="font-size: 40px; text-align: center;"><?php echo $varConteoRegistrados; ?></label>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Cantidad de USuarios No Registrados') ?></label>
                    <label  style="font-size: 40px; text-align: center;"><?php echo $varConteoNoResgitrados; ?></label>
                </div>
            </div>
        </div>

        <hr>

        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Gráficas de Envio') ?></label>
                    <div id="conatinergeneric" class="highcharts-container" style="height: 350px;"></div>
                </div>
            </div>
        </div>


    </div>

    <?php $form->end() ?>

  </div>

</div>

<hr>

<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
    echo Html::tag('div', '', ['id' => 'ajax_result_send']);
    echo Html::tag('div', '', ['id' => 'ajax_result_sendmore']);
    echo Html::tag('div', '', ['id' => 'ajax_result_sendreports']);
    echo Html::tag('div', '', ['id' => 'ajax_result_sendnoreports']);
?>

<script type="text/javascript">
    $(function(){
        var Listado = "<?php echo implode($varListDias,",");?>";
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
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad De Envios'
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
              name: 'Cantidad de Envios',
              data: [<?= join($varListCantidad, ',')?>],
              color: '#559FFF'
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