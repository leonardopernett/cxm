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

$this->title = 'Procesos Administrador - Agregar Asesores Masivos';
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
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capaInfo" id="idCapaInfo" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb" style="background: #6b97b1; ">
        <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Acciones Agregar Asesores Masivos"; ?> </label>
      </div>
    </div>
  </div>

  <br>

  <?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>
  <div class="row">
    
    <div class="col-md-4">
      
      <div class="card1 mb">
        <label><em class="fas fa-upload" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Subir archivo con Asesores') ?></label>
        
        <?= $form->field($model, "file[]")->fileInput(['id'=>'idinput','multiple' => false])->label('') ?>

        <br>

        <?= Html::submitButton("Subir", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>

      </div>
      
      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #b52aef;"></em> Cancelar y Regresar...</label>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
        ?>
      </div>

    </div>

    <div class="col-md-4">
      
      <div class="card1 mb">
        <label><em class="fas fa-chart-line" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Cantidades Asesores') ?></label>
        <table id="myTable2" class="table table-hover table-bordered" style="margin-top:20px" >
          <caption>.</caption>
          <thead>
            <th>
              <td  class="text-center" style="background-color: #F5F3F3;" colspan="3"><label style="font-size: 15px;"><?= Yii::t('app', 'Aliados & Cantidades') ?></label></td>
            </th>
            <th>
              <td  class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'KONECTA') ?></label></td>
              <td  class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'TLMARK') ?></label></td>
              <td  class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'AST') ?></label></td>
            </th>
          </thead>
          <tbody>
            <tr>
              <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varAsesoresKnt) ?></label></td>
              <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varAsesoresTlm) ?></label></td>
              <td class="text-center"><label style="font-size: 12px;"><?= Yii::t('app', $varAsesoresAst) ?></label></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

    <div class="col-md-4">

      <div class="card1 mb">
        <label><em class="fas fa-file" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Archivo Base') ?></label>
        <a style="font-size: 18px;" rel="stylesheet" type="text/css" href="../../downloadfiles/Cargue_General_Asesores.xlsx" target="_blank">Descargar Archivo Base</a>
        
        <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
          <caption>Información</caption>
          <thead>
            <th>
              <td  class="text-center" style="background-color: #F5F3F3;" colspan="3"><label style="font-size: 15px;"><?= Yii::t('app', 'Es importante indicar que el campo "Aliados" debe de ir la notacion de tres caracteres de la siguiente forma...') ?></label></td>
            </th>
          </thead>
          <tbody>
            <tr>
              <td><label style="font-size: 12px;"><?= Yii::t('app', 'KONECTA >> KNT') ?></label></td>
              <td><label style="font-size: 12px;"><?= Yii::t('app', 'TLMARK >> TLM') ?></label></td>
              <td><label style="font-size: 12px;"><?= Yii::t('app', 'AST >> AST') ?></label></td>
            </tr>
          </tbody>
        </table>
      </div>

    </div>

  </div>
  <?php ActiveForm::end(); ?>

</div>
<hr>

<script type="text/javascript">

</script>