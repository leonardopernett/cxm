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

$this->title = 'Procesos Administrador - Procesos Usuarios .sip (Encuestas)';
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
<div id="capaPrincipal" class="capaPrincipal" style="display: inline;">
    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>
        <div class="row">

            <div class="col-md-4">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #ffc034;"></em> Subir Archivo...</label>
                    <?= Html::button('Importar', ['value' => url::to(['importarusuarios']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Importar Archivo', 'style' => 'background-color: #337ab7']) 
                    ?> 

                    <?php
                        Modal::begin([
                            'header' => '<h4></h4>',
                            'id' => 'modal1',
                        ]);

                        echo "<div id='modalContent1'></div>";
                                                                              
                        Modal::end(); 
                    ?> 
                </div>    

                <br>

                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 15px; color: #ffc034;"></em> Buscar Asesor...</label>
                    <?=
                        $form->field($model, 'evaluados_id')->label(Yii::t('app','Valorado'))
                                ->widget(Select2::classname(), [
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Seleccionar asesor...')],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 4,
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['reportes/evaluadolist']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                        'initSelection' => new JsExpression('function (element, callback) {
                                                    var id=$(element).val();
                                                    if (id !== "") {
                                                        $.ajax("' . Url::to(['reportes/evaluadolist']) . '?id=" + id, {
                                                            dataType: "json",
                                                            type: "post"
                                                        }).done(function(data) { callback(data.results[0]);});
                                                    }
                                                }')
                                    ]
                                ] 
                        )->label('');
                    ?> 

                    <br>

                    <?= Html::submitButton(Yii::t('app', 'Buscar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'title' => 'Buscar Asesor']) 
                    ?> 
                </div>

                <br>

                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-spinner" style="font-size: 15px; color: #ffc034;"></em> Actualizar Procesos...</label>
                    <?= Html::a('Procesar',  ['actualizaprocesos'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #337ab7',
                                        'data-toggle' => 'tooltip',
                                        "onclick" => "cargar();",
                                        'title' => 'Regresar']) 
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
                    <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em> Datos del Asesor...</label>

                    <table id="tblDatas" class="table table-striped table-bordered tblResDetFreed">
                        <caption>...</caption>
                        <thead>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Asesor"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Identificación"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Usuario de Red"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Usuario .Sip"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Modificado Encuestas"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Fecha Modificado"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Acción"; ?></label></th>
                        </thead>
                        <tbody>
                            <?php

                                if ($dataList != 0) {
                                    
                                    foreach ($ListaRegistro as $key => $value) {
                                        
                            ?>
                                <tr>
                                    <td><label style="font-size: 12px;"><?php echo $value['comentarios']; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo $value['identificacion']; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo $value['usuariored']; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo $value['usuariosip']; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo $value['cambios']; ?></label></td>
                                    <td><label style="font-size: 12px;"><?php echo $value['fechacambios']; ?></label></td>
                                    <td class="text-center">
                                        <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['deletesip','id'=> $value['idusuariossip']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                                    </td>
                                </tr>
                            <?php 
                                    }
                                }
                            ?>
                        </tbody>
                    </table>

                    <hr>

                    <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #ffc034;"></em> Estadísticas...</label>

                    <table id="tblDatasEstadistico" class="table table-striped table-bordered tblResDetFreed">
                        <caption>...</caption>
                        <thead>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad Asesores Modificados"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad Asesores No Modificados"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Cantidad Asesores Ingresados"; ?></label></th>
                            <th scope="col" class="text-center"  style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo "Última Fecha Archivo Importado"; ?></label></th>
                        </thead>
                        <tbody>
                            <tr>
                                <td class="text-center"><label style="font-size: 20px;"><?php echo $varCambiado; ?></label></td>
                                <td class="text-center"><label style="font-size: 20px;"><?php echo $varNoCambiado; ?></label></td>
                                <td class="text-center"><label style="font-size: 20px;"><?php echo $varTotalAsesores; ?></label></td>
                                <td class="text-center"><label style="font-size: 20px;"><?php echo $varFechaMax; ?></label></td>
                            </tr>
                        </tbody>
                    </table>

                </div>
            </div>

        </div>
    <?php $form->end() ?>
</div>

<div id="capaSecundaria" class="capaSecundaria" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em> Actualizar Datos...</label>
                <div class="col-md-12">
                    <table>
                    <caption>...</caption>
                        <tr>
                            <th scope="col" class="text-center"><div class="loader"> </div></th>
                            <th scope="col" class="text-center"><label><?= Yii::t('app', ' Actualizando los usuarios de red de los asesores en las encuestas, por favor esperar a que termine el proceso.') ?></label></th>
                        </tr>
                    </table>                                       
                </div>
            </div>
        </div>
    </div>
</div>
<hr>

<script type="text/javascript">
    function cargar(){
        var varcapaIniID = document.getElementById("capaPrincipal");
        var varcapaOneID = document.getElementById("capaSecundaria");
        
        varcapaIniID.style.display = 'none';
        varcapaOneID.style.display = 'inline';
    };

</script>