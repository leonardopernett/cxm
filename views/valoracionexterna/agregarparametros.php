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
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;

$this->title = 'Gestor Valoraciones Externas - Parametrizar Campos Excel';
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

  .card2 {
    height: 355px;
    width: auto;
    margin-top: auto;
    margin-bottom: auto;
    background: #FFFFFF;
    position: relative;
    display: flex;
    justify-content: center;
    flex-direction: column;
    padding: 10px;
    box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
    -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
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

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Principal -->
<div id="capaIdPrincipal" class="capaPrincipal" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Distribución de Parametros - '.$varCliente) ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb" style="background: #e6edff;">
          <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Importante: Este módulo de parametrizar es para ingresar las columnas que el excel contenga para extraer la data de las valoraciones.') ?></label>
        </div>
      </div>
    </div>

    <br>

    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">

          <div class="row">
            <div class="col-md-4">
              <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Ingresar Campo Asesor') ?></label>
              <?= $form->field($model, 'cc_asesor', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3, 'id'=>'idcc_asesor', 'placeholder' => 'Ingresar Campo Asesor - Columna de Excel'])?> 
              <br>

              <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Ingresar Campo Dimensión') ?></label>
              <?= $form->field($model, 'dimension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3, 'id'=>'iddimension', 'placeholder' => 'Ingresar Campo Dimensión - Columna de Excel'])?> 
            </div>

            <div class="col-md-4">
              <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Ingresar Campo Valorador') ?></label>
              <?= $form->field($model, 'cc_valorador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3, 'id'=>'idcc_valorador', 'placeholder' => 'Ingresar Campo Valorador - Columna de Excel'])?> 

              <br>

              <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Ingresar Campo Score') ?></label>
              <?= $form->field($model, 'score', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3, 'id'=>'idscore', 'placeholder' => 'Ingresar Campo Score - Columna de Excel'])?> 
            </div>

            <div class="col-md-4">
              <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Ingresar Campo Programa/Pcrc') ?></label>
              <?= $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3, 'id'=>'idarbol_id', 'placeholder' => 'Ingresar Campo Programa/Pcrc - Columna de Excel'])?> 

              <br>
              
              <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Ingresar Campos Comentarios') ?></label>
              <?= $form->field($model, 'comentario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 3, 'id'=>'idcomentario', 'placeholder' => 'Ingresar Campos Comentarios - Columna de Excel'])?> 
            </div>
          </div>
          
        </div>
      </div>
    </div>

</div>

<hr>

<!-- Capa Agregar Adicionales -->
<div id="capaIdSecundarios" class="capaSecundarios" style="display: inline;">

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Agregar Campos Interacciones') ?></label>
        <?= Html::button('Agregar', ['value' => url::to(['agregarinteracciones','id_general'=>$id_general]), 
            'class' => 'btn btn-success', 'id'=>'modalButton1',
            'data-toggle' => 'tooltip',
            'title' => 'Agregar Interacciones']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Agregar Información - Interacciones</h4>',
            'id' => 'modal1',

          ]);

          echo "<div id='modalContent1'></div>";
                                                                                                  
          Modal::end(); 
        ?>
        <br>
        <table id="tblDataInteracciones" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Interacción') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Respuesta Excel') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaInteracciones as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  $value['atributos']; ?></label></td>
                <td><label style="font-size: 12px;"><?php echo  $value['resp_atributos']; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarinteracciones','id_Interaccion'=> $value['id_atributo'],'id_general'=>$id_general], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Agregar Campos Especiales') ?></label>
        <?= Html::button('Agregar', ['value' => url::to(['agregarespeciales','id_general'=>$id_general]), 
            'class' => 'btn btn-success', 'id'=>'modalButton2',
            'data-toggle' => 'tooltip',
            'title' => 'Agregar Especiales']) 
        ?> 

        <?php
          Modal::begin([
            'header' => '<h4>Agregar Información - Especiales</h4>',
            'id' => 'modal2',

          ]);

          echo "<div id='modalContent2'></div>";
                                                                                                  
          Modal::end(); 
        ?>
        <br>
        <table id="tblDataItems" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Item Especial') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Item Excel') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaItemsEspeciales as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  $value['item_especial']; ?></label></td>
                <td><label style="font-size: 12px;"><?php echo  $value['campo_especial']; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminaritems','id_Items'=> $value['id_datoespecial'],'id_general'=>$id_general], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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
        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Agregar Formularios') ?></label>
        <?= Html::a('Agregar',  ['agregarformularios','id_general'=>$id_general], ['class' => 'btn btn-success',
              'data-toggle' => 'tooltip',
              'title' => 'Agregar Formularios']) 
        ?>
        
        <br>
        <table id="tblDataForms" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', 'Resultados') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio Archivo') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Programa/Pcrc') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acción') ?></label></th>
            </tr>
          </thead>
          <tbody>
            <?php
              foreach ($varListaFormularios as $key => $value) {                  
            ?>
              <tr>
                <td><label style="font-size: 12px;"><?php echo  $value['servicio_excel']; ?></label></td>
                <td><label style="font-size: 12px;"><?php echo  $value['name']; ?></label></td>
                <td class="text-center">
                  <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarforms','id_Forms'=> $value['id_formulariosexcel'],'id_general'=>$id_general], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

<!-- Capa Proceso Botones -->
<div id="capaIdBtn" class="capaBtn" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Registrar') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Registro General']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

</div>

<?php ActiveForm::end(); ?>

<hr>

<script type="text/javascript">
  function varVerificar(){
    var varidcc_asesor = document.getElementById("idcc_asesor").value;
    var variddimension = document.getElementById("iddimension").value;
    var varidcc_valorador = document.getElementById("idcc_valorador").value;
    var varidscore = document.getElementById("idscore").value;
    var varidarbol_id = document.getElementById("idarbol_id").value;
    var varidcomentario = document.getElementById("idcomentario").value;

    if (varidcc_asesor == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar la columna excel del asesor","warning");
      return;
    }
    if (variddimension == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar la columna excel de la dimension","warning");
      return;
    }
    if (varidcc_valorador == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar la columna excel del valorador","warning");
      return;
    }
    if (varidscore == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar la columna excel del score","warning");
      return;
    }
    if (varidarbol_id == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar la columna excel del programa/pcrc","warning");
      return;
    }
    if (varidcomentario == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar la columna excel del comentario valoracion","warning");
      return;
    }
  }
</script>