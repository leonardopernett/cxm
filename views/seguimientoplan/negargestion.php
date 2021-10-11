<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Seguimiento Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;

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


    $month = date('m');
    $year = date('Y');
    $day = date("d", mktime(0,0,0, $month+1, 0, $year));
         
    $varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    $varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

    $varlistnegar = Yii::$app->db->createCommand("select * from tbl_plan_escalamientos where idplanjustificar = $txtvaridplan")->queryAll();
?>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito');

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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br><br>
<div class="capaPP" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></i> Información de la justificación...</label>
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <thead>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Rol"; ?></label></th>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tecnico/Lider"; ?></label></th>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Corte"; ?></label></th>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tipo Corte"; ?></label></th>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Justificacion"; ?></label></th>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad Justificaciones"; ?></label></th>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Comentarios"; ?></label></th>
                        <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Asesor"; ?></label></th>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($varlistnegar as $key => $value) {
                                $varidplan = $value['idplanjustificar'];
                                $varidtcts = $value['idtcs'];
                                $vanameidtcs = Yii::$app->get('dbslave')->createCommand("select diastcs from tbl_tipos_cortes where idtcs = $varidtcts")->queryScalar();
                                $varEstado = $value['Estado'];
                                $txtestado = null;
                                $varidusua = $value['tecnicolider'];
                                $varnameusu = Yii::$app->get('dbslave')->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varidusua")->queryScalar();

                                $varRango = Yii::$app->get('dbslave')->createCommand("select tc.tipocortetc from tbl_tipocortes tc inner join tbl_tipos_cortes tcs on tc.idtc = tcs.idtc where  tcs.idtcs = $varidtcts")->queryScalar();

                                        $varRol = Yii::$app->get('dbslave')->createCommand("select r.role_id from tbl_roles r inner join rel_usuarios_roles ru on r.role_id = ru.rel_role_id where ru.rel_usua_id = $varidusua")->queryScalar();
                                
                                        $varnamerol = Yii::$app->get('dbslave')->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ru on r.role_id = ru.rel_role_id where ru.rel_usua_id = $varidusua")->queryScalar();
                                $varasesor = $value['asesorid'];
                                if ($varasesor != null) {
                                    $txtasesor = Yii::$app->get('dbslave')->createCommand("select distinct name from tbl_evaluados where id = $varasesor")->queryScalar();
                                }else{
                                    $txtasesor = "---";
                                }
                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $varnamerol; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varnameusu; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varRango; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $vanameidtcs; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['justificacion']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['cantidadjustificar']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $txtasesor; ?></label></td>
                            </tr>
                        <?php 
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="capaOne" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></i> Comentario por el que no aprueba la justificación...</label>
                <?= $form->field($model, 'negargestion')->textInput(['maxlength' => 250,  'id'=>'idnegargestion']) ?>
                <br>
                <?= Html::submitButton('Guardar (No Aprobar)', ['class' => 'btn btn-primary'] ) ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<hr>
<div class="capaTwo" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-exclamation" style="font-size: 20px; color: #ff532c;"></i> Es importante indicar que no necesariamente hay que colocar un comentario al la no aprobación de la justificación. Solo debe dar clic en guardar.</label>
            </div>
        </div>
    </div>
</div>
      