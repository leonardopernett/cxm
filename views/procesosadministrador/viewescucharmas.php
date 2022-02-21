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

$this->title = 'Procesos Administrador - Permisos Escuchar +';
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

        <div class="col-md-4">
            <div class="CapaSubUno" style="display: inline;">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #ffc034;"></em> Ingresar Datos...</label>
                    

                        <label style="font-size: 15px;">* Seleccionar Servicios...</label>
                        <?=  $form->field($model, 'iddashservicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosVolumendirector::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                        [
                                            'prompt'=>'Seleccione Servicio...',
                                        ]
                                )->label(''); 
                        ?>
                        <br>
                        <label style="font-size: 15px;">* Seleccionar Usuario...</label>
                        <?=
                            $form->field($model, 'usuaid', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label('Tecnico Trainner VOC')
                                        ->widget(Select2::classname(), [
                                            'id' => 'ButtonSelect',
                                            'name' => 'BtnSelectes',
                                            'attribute' => 'Valorador',
                                            'language' => 'es',
                                            'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                            'pluginOptions' => [
                                                'allowClear' => true,
                                                'minimumInputLength' => 4,
                                                'ajax' => [
                                                    'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                                    'dataType' => 'json',
                                                    'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                                ],
                                                'initSelection' => new JsExpression('function (element, callback) {
                                                            var id=$(element).val();
                                                            if (id !== "") {
                                                                $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                                                    dataType: "json",
                                                                    type: "post"
                                                                }).done(function(data) { callback(data.results[0]);});
                                                            }
                                                        }')
                                            ]
                                        ]
                            );
                        ?>

                        <br>

                        <?= Html::submitButton(Yii::t('app', 'Guardar Permiso'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'title' => 'Guardar Permiso']) 
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

                    <table id="tblDatas" class="table table-striped table-bordered tblResDetFreed">
                        <caption>...</caption>
                        <thead>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Servicio"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Usuario"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Acciones"; ?></label></th>
                        </thead>
                        <tbody>
                            <?php
                                $varLista = Yii::$app->db->createCommand('
                                SELECT d.iddashboardpermisos AS Id, d.nombreservicio AS Servicios, u.usua_nombre AS Usuarios  FROM tbl_usuarios u
                                    INNER JOIN tbl_dashboardpermisos d ON 
                                        u.usua_id = d.usuaid')->queryAll();
                                
                                foreach ($varLista as $key => $value) {
                                    
                            ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo $value['Servicios']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo $value['Usuarios']; ?></label></td>
                                <td class="text-center">
                                    <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletepermisos','id'=> $value['Id']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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