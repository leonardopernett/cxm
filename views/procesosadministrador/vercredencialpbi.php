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

$this->title = 'Gestor Parametrizar Power BI - Historico';
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

?>
<!-- Data extensiones -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<!-- Capa Historico -->
<div class="capaHistorico" id="capaIdHistorico" style="display: inline;">

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', ' Lista Historico') ?></label>
        <table id="tblDataHistorico" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Parametros Azure') ?></label></th>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Contenidos Azure') ?></label></th>
              <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Registro') ?></label></th>
            </tr>
          </thead>
          <tbody>
              <?php
              foreach ($varListCredenciales as $key => $value) {
              ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  " ".$value['azure_param']; ?></label></td>
                <td><label style="font-size: 12px;"><?php echo  " ".$value['azure_content']; ?></label></td>
                <td><label style="font-size: 12px;"><?php echo  " ".$value['fechacreacion']; ?></label></td>
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