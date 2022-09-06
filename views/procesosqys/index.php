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
use app\models\ProcesosClienteCentrocosto;

  $this->title = 'Procesos Q&S';
  $this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol->select(['tbl_roles.role_id'])
      ->from('tbl_roles')
      ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                  'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
      ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
      ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<style type="text/css">
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
<div id="idCapaProceso" class="capaProcesos" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> <?= Yii::t('app', 'Búsqueda Por Persona') ?></label>
        <?= Html::button('Buscar', ['value' => url::to(['buscarporpersona']), 'class' => 'btn btn-success', 'id'=>'modalButton1',
          'data-toggle' => 'tooltip',
          'style' => 'background-color: #559FFF', 
          'title' => 'Buscar Por Persona']) ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Filtros - Búsqueda Por Persona CXM</h4>',
              'id' => 'modal1',
              'size' => 'modal-lg',
            ]);

            echo "<div id='modalContent1'></div>";
                                                              
            Modal::end(); 
        ?>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Búsqueda Por Proceso') ?></label>
        <?= Html::button('Buscar', ['value' => url::to(['buscarporproceso']), 'class' => 'btn btn-success', 'id'=>'modalButton2',
          'data-toggle' => 'tooltip',
          'style' => 'background-color: #827DF9', 
          'title' => 'Buscar Por Proceso']) ?> 

        <?php
            Modal::begin([
              'header' => '<h4>Filtros - Búsqueda Por Proceso CXM</h4>',
              'id' => 'modal2',
              'size' => 'modal-lg',
            ]);

            echo "<div id='modalContent2'></div>";
                                                              
            Modal::end(); 
        ?>
      </div>
    </div>
  </div>

</div>

<hr>

<!-- Capa Informativa -->
<div id="idCapaInfo" class="capaInformacion" style="display: inline;">
  
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb" style="background-color: #e9f9e8;">
        <label style="font-size: 14px;"><em class="fas fa-info-circle" style="font-size: 16px; color: #ff453c;"></em> <?= Yii::t('app', 'Importante: Para seguir con el informe es necesario realizar la búsqueda con alguno de los filtros existentes (Por Persona o Por Proceso), ingresando los datos necesarios para mostrar la información.') ?></label>
      </div>
    </div>
  </div>

</div>

<hr>