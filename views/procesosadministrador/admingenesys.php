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

$this->title = 'Procesos Genesys';
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

<!-- Capa Procesos -->
<div id="capaProcesosId" class="capaProcesos" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
      
      <div class="col-md-3">

        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
          <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
          ?>
        </div>

        <br>

        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Genesys Por Asesor') ?></label>
          <?= Html::a('Aceptar',  ['gbuscarporasesor'], ['class' => 'btn btn-success',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Genesys Por Asesor']) 
          ?>
        </div> 

        <br>

        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-key" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Genesys Por Connid') ?></label>
          <?= Html::a('Aceptar',  ['gbuscarporconnid'], ['class' => 'btn btn-success',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Genesys Por Connid']) 
          ?>
        </div> 
        
      </div>

      <div class="col-md-9">

        <div class="row">
          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Actualizar Asesores Genesys - CXM') ?></label>
              <?= Html::a('Aceptar',  ['actualizaasesor'], ['class' => 'btn btn-success',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Actualizar Asesores Genesys - CXM']) 
              ?>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-paperclip" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Actualizar Servicios CXM - Genesys') ?></label>
              <?= Html::a('Aceptar',  ['actualizaservicio'], ['class' => 'btn btn-success',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Actualizar Servicios Genesys - CXM']) 
              ?>
            </div>
          </div>
        </div>

        <br>

        <div class="row">
          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Datos Asesores Genesys - CXM') ?></label>
              <label  style="font-size: 80px; text-align: center;"><?= Yii::t('app', $varCantidadAsesores) ?></label>
            </div>
          </div>

          <div class="col-md-6">
            <div class="card1 mb">
              <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Dato Servicios Genesys - CXM') ?></label>
              <label  style="font-size: 80px; text-align: center;"><?= Yii::t('app', $varCantidadArbol) ?></label>
            </div>
          </div>
        </div>        

        <br>
        
      </div>

    </div>

</div>

<hr>

<br>

<script type="text/javascript">

  function openCity(evt, cityName) {
    var i, x, tablinks;
    x = document.getElementsByClassName("city");
    for (i = 0; i < x.length; i++) {
      x[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablink");
    for (i = 0; i < x.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" w3-border-red", "");
    }
    document.getElementById(cityName).style.display = "block";
    evt.currentTarget.firstElementChild.className += " w3-border-red";
  };


</script>