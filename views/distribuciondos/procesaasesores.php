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

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

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
            height: 115px;
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

<div class="capaGestion" style="display: inline;" id="idCapaGestion">
  <div class="row">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="col-md-4">
      <div class="card2 mb">
        <label style="font-size: 15px;"><em class="fas fa-check-circle" style="font-size: 15px; color: #38d043;"></em> Actualiza Datos Asesores: </label><br>

          <?= $form->field($model, 'anulado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idnombreevento', 'class'=>'hidden','value'=>0])?>
          
          <?= Html::submitButton(Yii::t('app', 'Procesar'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Procesar Datos',
                                    'onclick' => 'validaasesores();']) 
          ?>

      </div>
    </div>

    <div class="col-md-8">
      <div class="card2 mb">
        <div class="panel panel-default">
          <div class="panel-body " style="background-color: #f0f8ff; text-align: center; font-size: 15px;">
            <?= Yii::t('app', '* Segundo Proceso: permite guardar asesores que no estan registrados en CXM y permite actualizar los usuarios de red de los existentes.') ?>
          </div>
        </div>
      </div>
    </div>
    <?php $form->end() ?>
  </div>
</div>
<hr>


<script type="text/javascript">
  function validaasesores(){
    var varidCapaCero = document.getElementById("idCapaCero");
    var varidCapaGestion = document.getElementById("idCapaGestion");

    varidCapaCero.style.display = 'inline';
    varidCapaGestion.style.display = 'none';
    
  };
</script>