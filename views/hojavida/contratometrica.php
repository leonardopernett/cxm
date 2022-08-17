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

$this->title = 'Gestor de Clientes - Agregar Entregables en Contratos';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Gestor de Clientes - Agregar Entregables en Contratos';

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
    font-family: "Nunito";
    font-size: 150%;    
    text-align: left;    
  }

  .card2 {
    height: 200px;
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
    background-image: url('../../images/Gestor_de_Clientes.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Data extensiones -->

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

<!-- Capa Procesos Roles -->
<div id="capaIdRoles" class="capaRoles" style="display: inline;">
  
  <div class="row">
    <div class="col-md-6">
      
      <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-comments" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Metrica') ?></label>
        <?= $form->field($model, 'hvmetrica', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 100, 'id'=>'idmetrica', 'placeholder'=>'Ingresar Metrica'])?>
        <br>
        <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                          ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                              'data-toggle' => 'tooltip',
                              'title' => 'Aceptar Informacion',
                              'onclick' => 'verificardatametrica();',
                              'id'=>'modalButton']) 
                ?> 
      </div>
      <?php ActiveForm::end(); ?>

      <br>

      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
        ?>
      </div>

    </div>

    <div class="col-md-6">
      
      <div class="card1 mb">
        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Lista de Metricas') ?></label>

        <table id="tblDataCivil" class="table table-striped table-bordered tblResDetFreed">
          <caption><?= Yii::t('app', '.') ?></caption>
          <thead>
            <tr>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'ID') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Metrica') ?></label></th>
              <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Eliminar') ?></label></th>
            </tr>
          </thead>
          <tbody>
          <?php
            foreach ($vardataProvidermetrica as $key => $value) {                    
          ?>
            <tr>
              <td><label style="font-size: 12px;"><?php echo  $value['id_hvmetrica']; ?></label></td>
              <td><label style="font-size: 12px;"><?php echo  $value['hvmetrica']; ?></label></td>
              <td class="text-center">
                <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarmetrica','id'=> $value['id_hvmetrica']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
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

<script type="text/javascript">
  function verificardatametrica(){
    var varidmetrica = document.getElementById("idmetrica").value;

    if (varidmetrica == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar una metrica","warning");
      return;
    }
  };
</script>