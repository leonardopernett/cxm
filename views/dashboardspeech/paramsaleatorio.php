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

$this->title = 'Dashboard - Cantidad Aleatoria Escuchar +';
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

    $paramsBusqueda = [':varcodpcrcs' => $txtServicioCategorias, ':variddpclientes' => $variddpcliente, ':anulado' => 0];

    $varlistcantidad = Yii::$app->db->createCommand('
        SELECT * FROM tbl_speech_aleatoridad sa
            WHERE
                sa.id_dp_clientes = :variddpclientes
                    AND sa.cod_pcrc IN (:varcodpcrcs)
                        AND sa.anulado = :anulado
            GROUP BY sa.idaleatorio ')->bindValues($paramsBusqueda)->queryAll();

    $varservicio = Yii::$app->db->createCommand('
        SELECT ss.nameArbol FROM tbl_speech_servicios ss
            INNER JOIN tbl_speech_aleatoridad sa ON 
                ss.id_dp_clientes = sa.id_dp_clientes
            WHERE
                sa.id_dp_clientes = :variddpclientes
                    AND sa.cod_pcrc IN (:varcodpcrcs)
                        AND sa.anulado = :anulado
            GROUP BY sa.idaleatorio ')->bindValues($paramsBusqueda)->queryScalar();

    $varcodpcrc = Yii::$app->db->createCommand('
        SELECT CONCAT(sc.cod_pcrc," - ",sc.pcrc) AS codpcrc FROM tbl_speech_categorias sc
            INNER JOIN tbl_speech_aleatoridad sa ON 
                sc.cod_pcrc = sa.cod_pcrc
            WHERE
                sa.id_dp_clientes = :variddpclientes
                    AND sa.cod_pcrc IN (:varcodpcrcs)
                        AND sa.anulado = :anulado
            GROUP BY sa.idaleatorio ')->bindValues($paramsBusqueda)->queryScalar();

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
<div class="CapaCero" id="capaCero" style="display: inline;">

    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <?php
                    if (count($varlistcantidad) == 0) {
                        
                ?>

                    <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #ffc034;"></em> Registrar Datos</label>
                    <label style="font-size: 15px;">Ingresar Cantidad...</label>
                    <?= $form->field($model, 'cantidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id' => 'idcantidad', 'placeholder'=>'Ingresar Cantidad']) ?>
                    <br>
                    <label style="font-size: 15px;">Ingresar Comentarios...</label>
                    <?= $form->field($model, 'comentarios', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id' => 'idcomentarios', 'placeholder'=>'Ingresar Comentarios a la cantidad']) ?>
                    <br>
                    <?= Html::submitButton(Yii::t('app', 'Guardar Permiso'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Guardar Permiso']) 
                    ?> 

                <?php
                    }else{
                ?>
                    <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 15px; color: #ffc034;"></em> Nota Importante</label>
                    <label style="font-size: 15px;">Para realizar el ingreso de la cantidad de datos para aleatoriedad es necesario que no este parametrizado el cod_pcrc del actual servicio. Actualmente tienes una cantidad establecida, para colocar otra valor por favor eliminar la que tiene actualmente a ingresar la nueva.</label>
                <?php
                    }
                ?>
            </div>
            <br>
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em> Cancelar y Regresar...</label>
                <?= Html::a('Regresar',  ['categoriasview', 'txtServicioCategorias'=>$variddpcliente], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em> Lista de Datos</label>
                    <table id="tblDatas" class="table table-striped table-bordered tblResDetFreed">
                        <caption>...</caption>
                        <thead>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Servicio"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "cod_pcrc"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Comentarios"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Accion"; ?></label></th>
                        </thead>
                        <tbody>
                            <?php
                                


                                foreach ($varlistcantidad as $key => $value) {
                                    
                            ?>
                                <tr>
                                    <td><label style="font-size: 12px;"><?php echo $varservicio; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo $varcodpcrc; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo $value['cantidad']; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo $value['comentarios']; ?></label></td>
                                    <td class="text-center">
                                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletepermisos','id'=> $value['idaleatorio'],'codpcrc'=> $txtServicioCategorias], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
    <?php $form->end() ?> 

</div>
<hr>