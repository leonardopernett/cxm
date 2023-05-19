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

$this->title = 'Gestor Parametrizar Power BI';
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

  $varPBIUrl = 'https://powerbi-experiencia.fly.dev/';
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
   	font-family: "Nunito";
    font-size: 150%;    
    text-align: left;    
  }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Reporte-Power-BI.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>

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

<!-- Capa Principal -->
<div class="capaPrincipal" id="capaIdPrincipal" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Vistas') ?></label>
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Ingresar Nuevas Credenciales') ?></label>

        <?= Html::button('Aceptar', ['value' => url::to(['agregarcredencialespbi']), 'class' => 'btn btn-success', 'id'=>'modalButton',
                                'data-toggle' => 'tooltip',
                                'title' => 'Ingresar Nuevas Credenciales']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Ingresar Credenciales</h4>',
            'id' => 'modal',
            'size' => 'modal-lg',
          ]);

          echo "<div id='modalContent'></div>";
                                                                                                  
          Modal::end(); 
        ?>
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Revisar Historico Credenciales') ?></label>

        <?= Html::button('Aceptar', ['value' => url::to(['vercredencialpbi']), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                'data-toggle' => 'tooltip',
                                'title' => 'Ver Credenciales']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Historico Credenciales</h4>',
            'id' => 'modal1',
            'size' => 'modal-lg',
          ]);

          echo "<div id='modalContent1'></div>";
                                                                                                  
          Modal::end(); 
        ?>
      </div>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
        ?>
      </div>
    </div>

    <div class="col-md-8">
      <div class="card1 mb">
        <div id="container_report" style="min-height:80vh;">
          <iframe title="new-page"  style="position: absolute; top: 0; left: 0; width: 100%; height: 100%;" src='<?php echo $varPBIUrl; ?>'
            allowfullscreen=""></iframe>
        </div>
      </div>
    </div>
  </div>

</div>

<hr>
