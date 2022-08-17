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

$this->title = 'Gestor de Clientes - Asignar Permisos';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

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

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

</style>
<div class="capaPrincipal" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #2CA5FF;"></em> Asignar Permisos: </label>
                <?= Html::a('Crear',  ['permisoshv'], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'title' => 'Crear Modalidad Trabajo']) 
                ?>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="capaList" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?php echo "..."; ?></caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Usuario con Permisos') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Editar') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Eliminar') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($dataProviderPermisos as $key => $value) {
                               $paramsdocumento = [':documento' => $value['usuario_registro']];
                                $varNombre = Yii::$app->db->createCommand('
                                  SELECT usua_nombre FROM tbl_usuarios
                                    WHERE usua_id = :documento
                                    ')->bindValues($paramsdocumento)->queryScalar();
                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $value['hv_idacciones']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varNombre; ?></label></td>
                                <td class="text-center">
                                    <?= Html::a('<em class="fas fa-edit" style="font-size: 15px; color: #4643FC;"></em>',  ['editarpermisos','id' => $value['hv_idacciones']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Editar']) ?>
                                </td>
                                <td class="text-center">
                                    <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarpermisos','id' => $value['hv_idacciones']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
</div>



