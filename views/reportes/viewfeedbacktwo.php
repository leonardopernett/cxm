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

$this->title = 'Feedback - Reporte Feeback Asesor Vista';
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

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<!-- Capa Informativa -->
<div class="capaInfo" id="capaIdInfo" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Información') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Lista de Feedbacks') ?></label>

                <table id="tblListadoPFeedbacks" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Ejecución del Feedback') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Categoría Feedback') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Feedback') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Causa Raíz') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Problema') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción para la mejora') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        foreach ($varViewsFeedbacks as $key => $value) {
                            # code...
                        }
                        ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['feaccion_correctiva']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['namecategoria']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['nametipo']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['dscausa_raiz']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['dsaccion_correctiva']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['dscompromiso']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['dscomentario']; ?></label></td>
                        </tr>
                        <?php
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

</div>