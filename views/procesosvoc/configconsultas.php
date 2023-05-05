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

$this->title = 'Procesos Voc - Configurar & Actualizar Procesos Speech';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Procesos Voc - Configurar & Actualizar Procesos Speech';

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
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
<!-- datatable -->
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
<div class="CapaEstuctura" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card2 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', ' Parametrizar Estructuras') ?> </label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-cogs" style="font-size: 20px; color: #ff8c55;"></em><?= Yii::t('app', 'Parametrizar Consultas Speech') ?></label>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-cogs" style="font-size: 20px; color: #ff8c55;"></em><?= Yii::t('app', 'Ingresar Parametros Querys') ?></label>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-cogs" style="font-size: 20px; color: #ff8c55;"></em><?= Yii::t('app', 'Observar Estructuras') ?></label>
      </div>
    </div>

  </div>

</div>
<hr>
<div class="CapaLlamadas" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card2 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Actualizar Llamadas Speech a CXM') ?> </label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-cogs" style="font-size: 20px; color: #ff8c55;"></em><?= Yii::t('app', 'Procesar BOT') ?></label>
        <?= Html::a('Aceptar',  ['botconfigurar'], ['class' => 'btn btn-success',
                                'style' => 'display: inline;',                            
                                'data-toggle' => 'tooltip',
                                'title' => 'Aceptar'])
        ?>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card1 mb">
        <label><em class="fas fa-list" style="font-size: 20px; color: #ff8c55;"></em><?= Yii::t('app', ' Últimas Actualizaciones') ?></label>
      </div>
    </div>

  </div>

</div>
<hr>
<div class="CapaIdeal" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card2 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', ' Actualizar Base de Datos Ideal') ?> </label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-cogs" style="font-size: 20px; color: #ff8c55;"></em> <?= Yii::t('app', 'Procesar Datos') ?></label>
        <?= Html::button('Aceptar', ['value' => url::to(['bdideal']), 'class' => 'btn btn-success', 'id'=>'modalButton3',  'data-toggle' => 'tooltip', 'title' => 'Aceptar']) ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Seleccionar Datos...</h4>',
            'id' => 'modal3',
            //'size' => 'moda2-lg',
          ]);

          echo "<div id='modalContent3'></div>";
                                                                  
          Modal::end(); 
        ?>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card1 mb">
        <label><em class="fas fa-list" style="font-size: 20px; color: #ff8c55;"></em> <?= Yii::t('app', 'Últimas Actualizaciones') ?></label>
      </div>
    </div>

  </div>

</div>