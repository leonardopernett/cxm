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

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';
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

    $varmes = date('m') - 2;
     
    $varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    $varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

    $varlistcortes = Yii::$app->db->createCommand("select idtc, tipocortetc 'tipo' from tbl_tipocortes where mesyear between '$year-$varmes-01' and '$year-$month-01' group by tipocortetc order by idtc asc")->queryAll(); 
    $listData = ArrayHelper::map($varlistcortes, 'idtc', 'tipo');

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
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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
<br><br>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<div class="CapaCero" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-user-circle" style="font-size: 20px; color: #ff2c2c;"></i> Buscar Tecnico/Lider: </label>
                <div class="row">
                    <div class="col-md-6">
                        <label>Seleccionar Tecnico/Lider...</label>  
                        <?=
                            $form->field($model, 'evaluados_id')->label(Yii::t('app',''))
                            ->widget(Select2::classname(), [
                                //'data' => array_merge(["" => ""], $data),
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Seleccionar el técnico/lider...')],
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
                    </div>
                    <div class="col-md-6">      
                        <label>Corte a seleccionar...</label>                  
                        <?= $form->field($model, 'idtc')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'idtcs']) ?>
                    </div>                    
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card1 mb">
                                    <?= Html::submitButton(Yii::t('app', 'Buscar Plan'),
                                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                'data-toggle' => 'tooltip', 'style' => 'height: 37px;',
                                                'title' => 'Buscar']) 
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card1 mb">
                                    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-bars" style="font-size: 20px; color: #ff2c2c;"></i> Planes de Valoracion: </label>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                            <thead>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Data Id') ?></label></th>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Responsable') ?></label></th>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tecnico/Lider') ?></label></th>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rol') ?></label></th>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dimension') ?></label></th>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Pcrc') ?></label></th>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad Plan') ?></label></th>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Argumentos') ?></label></th>
                                <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Corte') ?></label></th>
                            </thead>
                            <tbody>
                                <?php
                                if ($varlistadimension != null) {
                                    
                                    foreach ($varlistadimension as $key => $value) {
                                        $txtusuaid = $value['evaluados_id'];
                                        $txtarbolid = $value['arbol_id'];
                                        $varnamerespon = Yii::$app->db->createCommand("select u.usua_nombre from tbl_usuarios u inner join tbl_control_procesos c on u.usua_id = c.responsable where c.evaluados_id = $txtusuaid group by u.usua_id")->queryScalar();

                                        $varnameevaluado = Yii::$app->db->createCommand("select u.usua_nombre from tbl_usuarios u where u.usua_id = $txtusuaid group by u.usua_id")->queryScalar();

                                        $txtRol = Yii::$app->db->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ur on r.role_id = ur.rel_role_id  inner join tbl_usuarios u on ur.rel_usua_id = u.usua_id where u.usua_id = $txtusuaid")->queryScalar();

                                        $txtArbol = Yii::$app->db->createCommand("select name from tbl_arbols where id = $txtarbolid")->queryScalar();
                                ?>
                                    <tr>
                                        <td><label style="font-size: 12px;"><?php echo  $value['id']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varnamerespon; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varnameevaluado; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $txtRol; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['dimensions']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $txtArbol; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['cant_valor']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['argumentos']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varcorte; ?></label></td>
                                    </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<div class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-list" style="font-size: 20px; color: #ff2c2c;"></i> Novedades Escaladas: </label>
                <div class="row">
                    <div class="col-md-12">
                        <table id="tblData2" class="table table-striped table-bordered tblResDetFreed">
                            <thead>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Data Id"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Rol"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tecnico/Lider"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Corte"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Tipo Corte"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Justificacion"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad Justificacion"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Comentarios"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Aprobar"; ?></label></th>
                                <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "No Aprobar"; ?></label></th>  
                            </thead>
                            <tbody>
                                <?php
                                if ($varlistadimension != null) {
                                    $varlistacortes = Yii::$app->db->createCommand("select idtcs from tbl_tipos_cortes where idtc = $varidtc group by idtcs")->queryAll();
                                    $vararraycortes = array();
                                    foreach ($varlistacortes as $key => $value) {
                                        array_push($vararraycortes, $value['idtcs']);
                                    }
                                    $varlistidtc = implode(", ", $vararraycortes);


                                    $varlistplan = Yii::$app->db->createCommand("select * from tbl_plan_escalamientos where tecnicolider = $varusuaid and idtcs in ($varlistidtc)")->queryAll();

                                    foreach ($varlistplan as $key => $value) {
                                        $varidplan = $value['idplanjustificar'];
                                        $varidtcts = $value['idtcs'];
                                        $vanameidtcs = Yii::$app->db->createCommand("select diastcs from tbl_tipos_cortes where idtcs = $varidtcts")->queryScalar();
                                        $varEstado = $value['Estado'];
                                        $txtestado = null;
                                        $varidusua = $value['tecnicolider'];
                                        $varnameusu = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varidusua")->queryScalar();

                                        $varRango = Yii::$app->db->createCommand("select tc.tipocortetc from tbl_tipocortes tc inner join tbl_tipos_cortes tcs on tc.idtc = tcs.idtc where  tcs.idtcs = $varidtcts")->queryScalar();

                                        $varRol = Yii::$app->db->createCommand("select r.role_id from tbl_roles r inner join rel_usuarios_roles ru on r.role_id = ru.rel_role_id where ru.rel_usua_id = $varidusua")->queryScalar();
                                        
                                        $varnamerol = Yii::$app->db->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ru on r.role_id = ru.rel_role_id where ru.rel_usua_id = $varidusua")->queryScalar();
                                ?>
                                    <tr>
                                        <td><label style="font-size: 12px;"><?php echo  $value['idplanjustificar']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varnamerol; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varnameusu; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $varRango; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $vanameidtcs; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['justificacion']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['cantidadjustificar']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['comentarios']; ?></label></td>
                                         <td class="text-center">
                                                    <?= Html::a('<i class="fas fa-thumbs-up" style="font-size: 20px; color: #2cdc5a;"></i>',  ['editarplan','varidplan' => $varidplan, 'varestado' => 1], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Aprobar']) ?>
                                            </td>
                                        <td class="text-center">
                                                <?= Html::a('<i class="fas fa-thumbs-down" style="font-size: 20px; color: #ff3838;"></i>',  ['editarplan','varidplan' => $varidplan, 'varestado' => 2], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'No Aprobar']) ?>
                                            </td>
                                    </tr>
                                <?php
                                    }
                                }
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<br>
<?php ActiveForm::end(); ?>
