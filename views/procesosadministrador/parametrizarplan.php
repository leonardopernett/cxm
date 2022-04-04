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

$this->title = 'Procesos Administrador - Parametrizar Plan de Valoración';
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

    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 80px;
      height: 80px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
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
<div class="capaInfo" id="idCapaInfo" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Acciones Plan de Valoración"; ?> </label>
      </div>
    </div>
  </div>

  <br>

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
  <div class="row">
    
    <div class="col-md-4">
      <div class="card1 mb">
        <label><em class="fas fa-check" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Bloqueo de Plan Valoración') ?></label>

        <br>

        <label style="font-size: 15px;"><?= Yii::t('app', '* Seleccionar Rango de Fecha') ?></label>

        <?=
            $form->field($model, 'fechacreacion', [
                                'labelOptions' => ['class' => 'col-md-12'],
                                'template' => 
                                '<div class="col-md-12"><div class="input-group">'
                                . '<span class="input-group-addon" id="basic-addon1">'
                                . '<i class="glyphicon glyphicon-calendar"></i>'
                                . '</span>{input}</div>{error}{hint}</div>',
                                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                'options' => ['class' => 'drp-container form-group']
                            ])->label('')->widget(DateRangePicker::classname(), [
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'right',
            ]]);
        ?>

        <br>

        <?= Html::submitButton(Yii::t('app', 'Bloquear'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'verificar();',
                                'title' => 'Bloquear Plan']) 
        ?>

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

        <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
          <caption><label><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> <?= Yii::t('app', 'Histórico de Bloqueos') ?></label></caption>
          <thead>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Mes Bloqueo') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Inicio') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha Fin') ?></label></th>            
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Estado') ?></label></th>
            <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acción Eliminar') ?></label></th>
          </thead>
          <tbody>
            <?php
              foreach ($varListBloqueos as $key => $value) {
                $varMesNum = date('m',strtotime($value['fecha_inicio']));
                $varFechaInicio = $value['fecha_inicio'];
                $varFechaFin = $value['fecha_fin'];
                $varAnulado = $value['anulado'];

                $txtMes = null;
                switch ($varMesNum) {
                    case '1':
                        $txtMes = "Enero";
                        break;
                    case '2':
                        $txtMes = "Febrero";
                        break;
                    case '3':
                        $txtMes = "Marzo";
                        break;
                    case '4':
                        $txtMes = "Abril";
                        break;
                    case '5':
                        $txtMes = "Mayo";
                        break;
                    case '6':
                        $txtMes = "Junio";
                        break;
                    case '7':
                        $txtMes = "Julio";
                        break;
                    case '8':
                        $txtMes = "Agosto";
                        break;
                    case '9':
                        $txtMes = "Septiembre";
                        break;
                    case '10':
                        $txtMes = "Octubre";
                        break;
                    case '11':
                        $txtMes = "Noviembre";
                        break;
                    case '12':
                        $txtMes = "Diciembre";
                        break;
                    default:
                        # code...
                        break;
                }  

                $varEstado = null;
                if ($varAnulado == '1') {
                  $varEstado = 'Cerrado';
                }else{
                  $varEstado = 'Bloqueado';
                }
            ?>
              <tr>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $txtMes; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varFechaInicio; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varFechaFin; ?></label></td>
                <td class="text-center"><label style="font-size: 12px;"><?php echo  $varEstado; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletecontrol','id'=> $value['idcontrolprocesos']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                </td>
              </tr>
            <?php
              }
            ?>
          </tbody>
        </table>

      </div>

    </div>

  </div>
  <?php ActiveForm::end(); ?>

</div>
<hr>

<script type="text/javascript">
  function verificar(){
    var varfecha = document.getElementById("controlprocesos-fechacreacion").value;

    if (varfecha == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Se debe de ingresar un rango de fecha.","warning");
      return;
    }
  };
</script>