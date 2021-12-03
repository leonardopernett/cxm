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
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Distribucion Personal - Version 2.0';
$this->params['breadcrumbs'][] = $this->title;

    $sesiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
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
            height: 100px;
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
      background-image: url('../../images/Dashboard-Escuchar-+.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      /*background: #fff;*/
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

<div class="capaCero" style="display: none;" id="idCapaCero">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
          <caption><?php echo "Actualmente CXM esta procesando los datos de la distribucion en conjunto con Jarvis."; ?></caption>
          <thead>
            <tr>
              <th scope="col" class="text-center"><div class="lds-ring"><div></th>
            </tr>
          </thead>
        </table>

      </div>
    </div>
  </div>
</div>

<div class="capaPrincipal" style="display: inline;" id="idCapaPrincipal">
  <div class="row">

    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-spinner" style="font-size: 15px; color: #FFC72C;"></em> Procesar los datos: </label>
        <?= Html::a('Procesar',  ['procesadistribucion'], ['class' => 'btn btn-primary',
                                        'data-toggle' => 'tooltip',
                                        'onclick' => 'generated();',
                                        'title' => 'Procesar la Distribucion']) 
        ?>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Actualizar Usuarios Red: </label>
        <?= 
          Html::button('Subir Archivo', ['value' => url::to(['actualizausuarios']), 'class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Subir Archivo'])
        ?>
        <?php
          Modal::begin([
            'header' => '<h4></h4>',
            'id' => 'modal1',
          ]);

          echo "<div id='modalContent1'></div>";
                                                          
          Modal::end(); 
        ?> 
      </div>
    </div>

  </div>
</div>
<hr>
<div class="capaSecundario" style="display: inline;" id="idCapaSecundario">
  <div class="row">
    
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 15px; color: #1993a5;"></em> Fecha Ultimo Procesamiento: </label>

        <div class="text-center">
          <label style="font-size: 25px;"><?= Yii::t('app', $varUltimaFecha) ?></label>
        </div>
        
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 15px; color: #1993a5;"></em> Cantidad de Asesores: </label>

        <div class="text-center">
          <label style="font-size: 25px;"><?= Yii::t('app', $varCantAsesores) ?></label>
        </div>
        
      </div>
    </div>

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 15px; color: #1993a5;"></em> Cantidad de Lideres: </label>

        <div class="text-center">
          <label style="font-size: 25px;"><?= Yii::t('app', $varCantLideres) ?></label>
        </div>
        
      </div>
    </div>

  </div>
</div>
<hr>

<script type="text/javascript">
  function validaprocesar(){
    var varidCapaCero = document.getElementById("idCapaCero");
    var varidCapaPrincipal = document.getElementById("idCapaPrincipal");
    var varidCapaSecundario = document.getElementById("idCapaSecundario");

    varidCapaCero.style.display = 'inline';
    varidCapaPrincipal.style.display = 'none';
    varidCapaSecundario.style.display = 'none';
    
  };

  function generated(){
    var varidCapaCero = document.getElementById("idCapaCero");
    var varidCapaPrincipal = document.getElementById("idCapaPrincipal");
    var varidCapaSecundario = document.getElementById("idCapaSecundario");

    varidCapaCero.style.display = 'none';
    varidCapaPrincipal.style.display = 'none';
    varidCapaSecundario.style.display = 'none';
  };
</script>