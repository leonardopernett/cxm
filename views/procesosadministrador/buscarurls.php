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

$this->title = 'Procesos Administrador - Actualizar Url Encuestas & Transcripciones';
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
                    <label><em class="fas fa-envelope" style="font-size: 20px; color: #FFC72C;"></em> Actualizar Datos...</label>

                    <?=
                        $form->field($model, 'fecha_gestion', [
                            'labelOptions' => ['class' => 'col-md-12'],
                            'template' => '<div class="col-md-12">{label}</div>'
                            . '<div class="col-md-12"><div class="input-group">'
                            . '<span class="input-group-addon" id="basic-addon1">'
                            . '<i class="glyphicon glyphicon-calendar"></i>'
                            . '</span>{input}</div>{error}{hint}</div>',
                            'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                            'options' => ['class' => 'drp-container form-group']
                        ])->widget(DateRangePicker::classname(), [
                            'useWithAddon' => true,
                            'convertFormat' => true,
                            'presetDropdown' => true,
                            'readonly' => 'readonly',
                            'useWithAddon' => true,
                            'pluginOptions' => [
                                'autoApply' => true,
                                'clearBtn' => true,
                                'timePicker' => false,
                                'format' => 'Y-m-d',
                                'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                'endDate' => date("Y-m-d"),
                                'opens' => 'left'
                            ],
                            'pluginEvents' => [
                            ]
                        ])->label('');
                    ?>

                    <br>

                    <?= Html::submitButton(Yii::t('app', 'Actualizar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'cargar();',
                                'title' => 'Procesar Urls']) 
                    ?> 
                </div>

                <br>

                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 15px; color: #ffc034;"></em> Última fecha de actualización - Cantidad de urls encontrados...</label>
                    <label  style="font-size: 70px; text-align: center;"><?php echo $varDataMax; ?></label>
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
                    <label><em class="fas fa-chart-bar" style="font-size: 20px; color: #FFC72C;"></em> Estadisticas del proceso...</label>
                </div>
            </div>

        </div>

    <?php $form->end() ?>
</div>

<div id="capaSecundaria" class="capaSecundaria" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em> Actualizar Datos Encuestas...</label>
                <div class="col-md-12">
                    <table>
                    <caption>...</caption>
                        <tr>
                            <th scope="col" class="text-center"><div class="loader"> </div></th>
                            <th scope="col" class="text-center"><label><?= Yii::t('app', ' Actualizando las encuestas para verificar su correspondientes buzones. Actualizando transcripciones.') ?></label></th>
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