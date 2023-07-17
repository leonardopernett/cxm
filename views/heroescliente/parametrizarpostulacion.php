<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

$this->title = 'Héroes por el Cliente - Procesos Parametrizador';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                	'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
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
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

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

<br>
<br>

<?php $form = ActiveForm::begin(
  [
    'layout' => 'horizontal',
    'fieldConfig' => [
      'inputOptions' => ['autocomplete' => 'off']
    ]
  ]); 

?>

<!-- Capa Principal -->
<div class="capaPrincipal" id="capaIdPrincipal" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
      <div class="col-md-6">
        <div class="card1 mb">
          <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cancelar & Regresar:') ?></label> 
          <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
          ?>
        </div>
      </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Parametrizaciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Parametrizar Tipo de Postulación') ?></label>
                <?= $form->field($modeltipos, 'tipopostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 150, 'id'=>'idtipopostula', 'placeholder'=>'Ingresar Tipo de Postulación'])?>

                <div onclick="generatedtipo();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  <?= Yii::t('app', ' Guardar') ?>
                </div>
                <br>
                <table id="tbltipoPostula" class="table table-striped table-bordered tblResDetFreed">
                  <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                  <thead>
                    <tr>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Postulación') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      foreach ($varDataTipos as $key => $value) {                    
                    ?>
                      <tr>
                        <td><label style="font-size: 12px;"><?php echo  $value['id_tipopostulacion']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['tipopostulacion']; ?></label></td>
                        <td class="text-center">
                          <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminartipo','id'=> $value['id_tipopostulacion']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                        </td>
                      </tr>
                    <?php
                      }
                    ?>
                  </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Parametrizar Ciudades que Pueden Postular') ?></label>
                <?= $form->field($modelciudad, 'ciudadpostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 150, 'id'=>'idciudadpostula', 'placeholder'=>'Ingresar Ciudad de Postulación'])?>

                <div onclick="generatedciudad();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  <?= Yii::t('app', ' Guardar') ?>
                </div>
                <br>
                <table id="tblCiudadPostula" class="table table-striped table-bordered tblResDetFreed">
                  <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                  <thead>
                    <tr>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ciudad Postulación') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      foreach ($varDataCiudad as $key => $value) {                    
                    ?>
                      <tr>
                        <td><label style="font-size: 12px;"><?php echo  $value['id_ciudadpostulacion']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['ciudadpostulacion']; ?></label></td>
                        <td class="text-center">
                          <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarciudad','id'=> $value['id_ciudadpostulacion']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Parametrizar Tipo de Postulación') ?></label>
                <?= $form->field($modelcargos, 'cargospostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 150, 'id'=>'idcargopostula', 'placeholder'=>'Ingresar Cargo para la Postulación'])?>

                <div onclick="generatedcargo();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  <?= Yii::t('app', ' Guardar') ?>
                </div>
                <br>
                <table id="tblcargoPostula" class="table table-striped table-bordered tblResDetFreed">
                  <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                  <thead>
                    <tr>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cargo Postulación') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      foreach ($varDataCargos as $key => $value) {                    
                    ?>
                      <tr>
                        <td><label style="font-size: 12px;"><?php echo  $value['id_cargospostulacion']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $value['cargospostulacion']; ?></label></td>
                        <td class="text-center">
                          <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarcargo','id'=> $value['id_cargospostulacion']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

<hr>

<?php $form->end() ?>

<script type="text/javascript">
  function generatedtipo(){
    var varidtipopostula = document.getElementById("idtipopostula").value;

    if (varidtipopostula == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar un tipo de postulación","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresartipopostula",
        data: {
          txtvaridtipopostula : varidtipopostula,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };

  function generatedciudad(){
    var varidciudadpostula = document.getElementById("idciudadpostula").value;

    if (varidciudadpostula == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar una ciudad de postulación","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresarciudadpostula",
        data: {
          txtvaridciudadpostula : varidciudadpostula,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };

  function generatedcargo(){
    var varidcargopostula = document.getElementById("idcargopostula").value;

    if (varidcargopostula == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar cargo para la postulación","warning");
      return;
    }else{
      $.ajax({
        method: "get",
        url: "ingresarcargopostula",
        data: {
          txtvaridcargopostula : varidcargopostula,
        },
        success : function(response){
          numRta =   JSON.parse(response);          
          location.reload();
        }
      });
    }
  };
</script>