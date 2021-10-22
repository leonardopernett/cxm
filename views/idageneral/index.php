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

$this->title = 'Permisos Historico de Feedback';
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

    $vartipo = ['1' => 'Calidad de Entrenamiento', '2' => 'OJT'];

    $vararboles = Yii::$app->db->createCommand("SELECT ss.nameArbol, ss.id_dp_clientes FROM tbl_speech_servicios ss WHERE ss.anulado = 0 AND ss.arbol_id != 1  ORDER BY ss.nameArbol")->queryAll();
    $varlistarbols = ArrayHelper::map($vararboles, 'id_dp_clientes', 'nameArbol');

    $varlistadortac = Yii::$app->db->createCommand("SELECT ss.nameArbol AS Arbol, COUNT(ig.usuariored) AS Cantidad, ig.fechamodificacion AS fechamodificacion FROM tbl_speech_servicios ss INNER JOIN tbl_ida_general ig ON ss.id_dp_clientes = ig.servicio WHERE ig.anulado = 0 AND ig.tipoproceso = 'Calidad de Entrenamiento' GROUP BY ss.id_dp_clientes")->queryAll();

    $varlistadortao = Yii::$app->db->createCommand("SELECT ss.nameArbol AS Arbol, COUNT(ig.usuariored) AS Cantidad, ig.fechamodificacion AS fechamodificacion FROM tbl_speech_servicios ss INNER JOIN tbl_ida_general ig ON ss.id_dp_clientes = ig.servicio WHERE ig.anulado = 0 AND ig.tipoproceso = 'OJT' GROUP BY ss.id_dp_clientes")->queryAll();

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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
<div class="capapp" style="display: inline;">
    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><i class="fas fa-clock" style="font-size: 15px; color: #559FFF;"></i> Generar acciones: </label>
                        <!-- La fecha se inhabilita para hacer el proceso solo con el tipo, ya que en la tabla del ida de Experience esta la fecha -->
                        <!-- <?=
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
                        ?> -->

                        <?= $form->field($model, 'servicio')->dropDownList($varlistarbols, ['prompt' => 'Seleccionar Servicio...', 'id'=>'idarbols']) ?>
                        <?= $form->field($model, 'tipoproceso')->dropDownList($vartipo, ['prompt' => 'Seleccione Proceso...', 'id'=>"txtidtipo" ])->label('') ?>
                        <br>
                        <?= Html::submitButton(Yii::t('app', 'Registrar'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Generar Acciones',
                                    'id'=>'modalButton1',
                                    'onclick' => 'verificar();']) 
                        ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><i class="fas fa-download" style="font-size: 15px; color: #559FFF;"></i> Descargar Informaci�n: </label>
                        <?= Html::button('Descargar', ['value' => url::to('enviararchivo'), 'class' => 'btn btn-success', 'id'=>'modalButton6',
                                'data-toggle' => 'tooltip',
                                'title' => 'Desargar', 'style' => 'background-color: #337ab7']) 
                            ?>  
                            <?php
                                Modal::begin([
                                    'header' => '<h4></h4>',
                                    'id' => 'modal6',
                                       //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent6'></div>";
                                                                                
                                Modal::end(); 
                            ?>

                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><i class="fas fa-list" style="font-size: 15px; color: #559FFF;"></i> Procesar datos Entto: </label>
                        <?= Html::a('Procesar',  ['procesarentto'], ['class' => 'btn btn-danger',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Procesar']) 
                        ?>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-9">
            <div class="card1 mb">
                <label style="font-size: 15px;"><i class="fas fa-list" style="font-size: 15px; color: #559FFF;"></i> Resultados: </label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label style="font-size: 15px;">Calidad de Entrenamiento </label>
                            <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                                <thead>
                                    <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Servicio"; ?></label></th>
                                    <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad asesores"; ?></label></th>
                                    <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Ultima fecha registro"; ?></label></th>
                                </thead>
                           
                                <tbody>
                                    <?php
                                    foreach ($varlistadortac as $key => $value) {
                                        $txtfechamodifica = $value['fechamodificacion'];
                                    ?>
                                        <td><label style="font-size: 12px;"><?php echo  $value['Arbol']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $value['Cantidad']; ?></label></td>
                                        <td><label style="font-size: 12px;"><?php echo  $txtfechamodifica; ?></label></td>
                                    <?php
                                    }
                                    ?>
                                </tbody>
                             </table>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label style="font-size: 15px;">OJT </label>
                            <table id="tblData2" class="table table-striped table-bordered tblResDetFreed">
                                <thead>
                                    <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Servicio"; ?></label></th>
                                    <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad asesores"; ?></label></th>
                                    <th class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Ultima fecha registro"; ?></label></th>
                                </thead>
                                <tbody>
                                <?php
                                foreach ($varlistadortao as $key => $value) {
                                    $txtfechamodifica = $value['fechamodificacion'];
                                ?>
                                    <td><label style="font-size: 12px;"><?php echo  $value['Arbol']; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $value['Cantidad']; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo  $txtfechamodifica; ?></label></td>
                                <?php
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
    <?php $form->end() ?>
</div>
<hr>
<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">
    function verificar(){
        var varidarbols = document.getElementById("idarbols").value;
        var vartxtidtipo = document.getElementById("txtidtipo").value;

        if (varidarbols == "") {
            event.preventDefault();
            swal.fire("��� Advertencia !!!","Debe de seleccionar un servicio","warning");
            return;
        }else{
            if (vartxtidtipo == "") {
                event.preventDefault();
                swal.fire("��� Advertencia !!!","Debe de seleccionar un tipo de proceso","warning");
                return;
            }
        }
    };
</script>
