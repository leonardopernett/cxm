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

$this->title = 'Procesos Administrador - Procesos Usuarios .sip (Encuestas)';
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';


$rol =  new Query;
$rol    ->select(['tbl_roles.role_id'])
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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css">
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capaPrincipal" style="display: inline;">
    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
        <div class="row">

            <div class="col-md-4">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #ffc034;"></em> Subir Archivo...</label>
                    <?= Html::button('Importar', ['value' => url::to(['importarusuarios']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Importar Archivo', 'style' => 'background-color: #337ab7']) 
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

                <br>

                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-spinner" style="font-size: 15px; color: #ffc034;"></em> Actualizar Procesos...</label>
                </div>

                <br>

                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em> Cancelar y Regresar...</label>
                    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                    ?>
                </div>
            </div>

            <div class="col-md-8">
                <div class="card1 mb">
                    
                </div>
            </div>

        </div>
    <?php $form->end() ?>
</div>
<hr>