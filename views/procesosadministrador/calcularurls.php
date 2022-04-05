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

$this->title = 'Procesos Administrador - Actualizar Url Encuestas';
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

    .card2 {
            height: 100px;
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
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<div id="capaPrincipal" class="capaPrincipal" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;">Ficha Procesamiento </label>
            </div>
        </div>
    </div>
    
    <br>

    <div class="row">
        <div class="col-md-4">
            <div class="card2 mb">
                <label><em class="fas fa-calendar" style="font-size: 20px; color: #FFC72C;"></em> Encuestas Actualizadas con Buzones</label>
                <label  style="font-size: 20px; text-align: center;"><?php echo $varCantidadUrl; ?></label>
            </div>            
        </div>

        <div class="col-md-4">
            <div class="card2 mb">
                <label><em class="fas fa-info" style="font-size: 20px; color: #FFC72C;"></em> Fecha del Proceso Actualización</label>
                <label  style="font-size: 19px; text-align: center;"><?php echo $txtfechainicio.' - '.$txtfechafin; ?></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card2 mb">
                <label><em class="fas fa-save" style="font-size: 20px; color: #FFC72C;"></em> Actualizar Transcripciones</label>
                

                    <?= $form->field($model, 'fecha_gestion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['class' => 'hidden', 'value' => $txtfechainicio])->label('') ?>
                    <?= $form->field($model, 'created', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['class' => 'hidden', 'value' => $txtfechafin])->label('') ?>

                    <?= Html::submitButton(Yii::t('app', 'Actualizar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'cargar();',
                                'title' => 'Procesar Transcripciones']) 
                    ?> 

                
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<br>
<div id="capaSecundaria" class="capaSecundaria" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em> Buscar & Guardar Datos Transcripciones...</label>
                <div class="col-md-12">
                    <table>
                    <caption>...</caption>
                        <tr>
                            <th scope="col" class="text-center"><div class="loader"> </div></th>
                            <th scope="col" class="text-center"><label><?= Yii::t('app', ' Buscando y Guardando las transcipciones de los buzones con urls.') ?></label></th>
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