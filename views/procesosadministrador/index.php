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

$this->title = 'Procesos Administrador';
$this->params['breadcrumbs'][] = $this->title;

    $sesiones =Yii::$app->user->identity->id;

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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<div id="IdCapaUno" class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-star" style="font-size: 40px; color: #ffc034;"></em> Es importante indicar que las acciones que se representan en el actual modulo solo es permitido para usuarios que tengan rol administrativo. </label>
            </div>
        </div>
    </div>
</div>
<hr>
<?php if ($roles == '270') { ?>
<div id="IdCapaDos" class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
        
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-address-card" style="font-size: 15px; color: #ffc034;"></em> Permisos Feedback</label>
                        <?= Html::a('Aceptar',  ['permisosfeedback/index'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Permisos Feedback']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em> Categorias Feedback</label>
                        <?= Html::a('Aceptar',  ['categoriascxm'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Categorias Feedback']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-clone" style="font-size: 15px; color: #ffc034;"></em> Responsabilidades</label>
                        <?= Html::a('Aceptar',  ['viewresponsability'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Permisos Feedback']) 
                        ?>
                    </div>
                </div>
            </div>

            <br>

            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-key" style="font-size: 15px; color: #ffc034;"></em> Permisos Servicios Escuchar +</label>
                        <?= Html::a('Aceptar',  ['viewescucharmas'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Permisos Escuchar +']) 
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<hr>
<?php }else{ ?>
    <div id="IdCapaTres" class="CapaTres" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 40px; color: #ffc034;"></em> Para acceder al modulo contactar al administrador de la herramienta para asignar permisos a su usuario. </label>
            </div>
        </div>
    </div>
</div>
<hr>
<?php } ?>