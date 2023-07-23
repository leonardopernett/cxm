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

$this->title = 'Héroes por el Cliente - Carga Masiva de Postulaciones';
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
    height: 270px;
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
    background-image: url('../../images/heroes.png');
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
<br>

<!-- Capa Subir -->
<div class="capaInformativa" id="capaIdInformativa" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Carga Masiva de Postulaciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>
  
    <div class="row">
    
        <div class="col-md-4">
      
            <div class="card1 mb">
                
                <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Subir Archivo') ?></label>
        
                <?= $form->field($model, "file[]")->fileInput(['id'=>'idinput','multiple' => false])->label('') ?>

                <br>

                <?= Html::submitButton("Subir", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>

            </div>
      
            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cancelar & Regresar') ?></label> 
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>

        </div>

        <div class="col-md-4">

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Archivo Base') ?></label> 
                
                <a style="font-size: 18px;" rel="stylesheet" type="text/css" href="../../downloadfiles/ArchivoBase_Heroes.xlsx" target="_blank"><?= Yii::t('app', ' Descargar Archivo Base') ?></a>

                <br>
        
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                    <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ültima Fecha de Cargue') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad') ?></label></th>
                        </tr>                        
                    </thead>
                    <tbody>
                        <tr>
                            <td><label style="font-size: 12px;"><?= Yii::t('app',  $varUltimaFecha) ?></label></td>
                            <td><label style="font-size: 12px;"><?= Yii::t('app',  $varcantidadMasivo) ?></label></td>
                        </tr>
                    </tbody>
                </table>
            </div>

        </div>

        <div class="col-md-4">

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Gráficas') ?></label> 
                <div id="containerExcel" class="highcharts-container" style="height: 210px;"></div> 
            </div>

        </div>

  </div>

  <?php ActiveForm::end(); ?>

</div>
<hr>

<script type="text/javascript">
    Highcharts.chart('containerExcel', {
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
                    foreach($varListaExcel as $value){?>
                    {
                        name: "<?php echo $value['varIngreso'];?>",
                        y: parseFloat("<?php echo $value['varCantidades'];?>"),                            
                        dataLabels: {
                            enabled: false
                        }
                    },
                <?php }?>
                        
            ]
        }]
    });
</script>