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
use app\models\SpeechCategorias;
use app\models\Dashboardservicios;

$this->title = 'Agregar Parametros Nuevos - API';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from(['tbl_roles'])
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $var = ['1' => 'SAE', '2' => 'WIA', '3' => 'Otro'];
?>

<div id="capaidPrincipal" class="capaPrincipal" style="display: inline;">
  
  <?php 
    $form = ActiveForm::begin([
      'layout' => 'horizontal',
      'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); 
  ?>

  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Seleccionar Cliente') ?></label>
            <?=  $form->field($model, 'id_dp_clientes', 
              ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
              ->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                [
                  'id'=>'idcliente',
                  'prompt'=>'Seleccione Cliente...',//placeholder de lo que se va a mostrar
                ]
              )->label('');  // para que tome el lavel de arriba 
            ?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Ingresar Proyect_id') ?></label>
            <?= $form->field($model, 'proyecto_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idproyecto', 'placeholder'=>'Ingresar Nombre del Proyecto'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Ingresar dataset_id') ?></label>
            <?= $form->field($model, 'dataset_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'iddataset', 'placeholder'=>'Ingresar el DataSet'])?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Ingresar tabla id') ?></label>
            <?= $form->field($model, 'table_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'table_id', 'placeholder'=>'Ingresar Nombre de la Tabla'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Ingresar Limite de datos') ?></label>
            <?= $form->field($model, 'limit', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idlimit', 'placeholder'=>'Ingresar Limite de data'])?>
          </div>

          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Ingresar Offset') ?></label>
            <?= $form->field($model, 'offset', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'offset_id', 'placeholder'=>'Ingresar Offset'])?>
          </div>
        </div>

        <div class="row">
          <div class="col-md-4">
            <label style="font-size: 15px;"><em class="fas fa-hand-pointer" style="font-size: 15px; color: #b52aef;"></em> <?= Yii::t('app', 'Ingresar Sociedad') ?></label>            
            <?= $form->field($model, 'sociedadprovieniente')->dropDownList($var, ['prompt' => 'Seleccionar...', 'id'=>'idsociedad']) ?>
          </div>
        </div>
        
      </div>
    </div>
  </div>

  <br>

  <div class="row">
    <div class="col-md-6">
      <div class="card1 mb">
        <?= Html::submitButton(Yii::t('app', 'Agregar'),
            [
              'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
              'data-toggle' => 'tooltip',
              'title' => 'Agregar Parametros',
              'id'=>'modalButton1',
              'onclick' => 'varVerificar();'
            ]) 
        ?>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card1 mb">
        <?= Html::a('Cancelar',  ['adminapiwiasae'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Cancelar']) 
          ?>
      </div>
    </div>
  </div>

  <?php $form->end() ?>

</div>

<script type="text/javascript">
  function varVerificar(){
    var varidcliente = document.getElementById("idcliente").value;
    var varidproyecto = document.getElementById("idproyecto").value;
    var variddataset = document.getElementById("iddataset").value;
    var vartable_id = document.getElementById("table_id").value;
    var varidlimit = document.getElementById("idlimit").value;
    var varoffset_id = document.getElementById("offset_id").value;
    var varidsociedad = document.getElementById("idsociedad").value;

    if (varidcliente == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente","warning");
      return;
    }
    if (varidproyecto == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar el nombre del proyecto","warning");
      return;
    }
    if (variddataset == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar el data set del proyecto","warning");
      return;
    }
    if (vartable_id == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar el nombre de la tabla del proyecto","warning");
      return;
    }
    if (varidlimit == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar el limite de data del proyecto","warning");
      return;
    }
    if (varoffset_id == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de ingresar el offset","warning");
      return;
    }
    if (varidsociedad == "") {
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Debe de seleccionar de donde proviene el proyecto","warning");
      return;
    }

  }
</script>