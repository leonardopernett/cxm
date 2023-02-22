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
use app\models\Pilaresgptw;
use app\models\DetallePilaresGptw;

$this->title = 'Procesos Administrador - Detalle de Pilares GPTW';
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
<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>

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
    <div class="row">
        <div class="col-md-6">
          <div class="card1 mb" style="background: #6b97b1; ">
            <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Detalles de Pilares GPTW"; ?> </label>
          </div>
        </div>
    </div>

    <br>

    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
    <div class="row">

        <div class="col-md-4">
            <div class="CapaSubUno" style="display: inline;">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #ffc034;"></em> Ingresar Datos...</label>
                    

                        <label style="font-size: 15px;">* Seleccionar Pilares...</label>
                        <?=  $form->field($model, 'id_pilares', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Pilaresgptw::find()->distinct()->where("anulado = 0")->orderBy(['nombre_pilar'=> SORT_ASC])->all(), 'id_pilares', 'nombre_pilar'),
                                        [
                                            'prompt'=>'Seleccione Pilar...',
                                        ]
                                )->label(''); 
                        ?>
                        <br>
                        <label style="font-size: 15px;">* Ingresar detalle...</label>
                        <?= $form->field($model, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 50, 'id' => 'idTexto', 'placeholder'=>'Ingresar nombre del detalle para el Pilar'])->label('') ?>

                        <br>

                        <?= Html::submitButton(Yii::t('app', 'Guardar '),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'title' => 'Guardar ']) 
                        ?> 
                    
                </div>
            </div>
            <br>
            <div class="CapaSubDos" style="display: inline;">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #ffc034;"></em> Cancelar y Regresar...</label>
                    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                    ?>
                </div>
            </div>
        </div>
        
        <div class="col-md-8">
            <div class="CapaSubTres" style="display: inline;">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em> Lista de Datos</label>

                    <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                        <caption>...</caption>
                        <thead>
                            <tr>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Pilar') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Detalle') ?></label></th>
                                <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                                
                               $varLista = (new \yii\db\Query())
                                ->select(['tbl_detalle_pilaresgptw.id_detalle_pilar', 'tbl_pilares_gptw.nombre_pilar', 'tbl_detalle_pilaresgptw.nombre'])
                                ->from(['tbl_detalle_pilaresgptw'])
                                ->join('INNER JOIN', 'tbl_pilares_gptw','tbl_detalle_pilaresgptw.id_pilares = tbl_pilares_gptw.id_pilares')
                                ->All();                                                         
                                
                                foreach ($varLista as $key => $value) {
                                    
                            ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo $value['nombre_pilar']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo $value['nombre']; ?></label></td>
                                <td class="text-center">
                                    <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletedetallepilar','id'=> $value['id_detalle_pilar']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
    <?php $form->end() ?> 

</div>
<hr>
