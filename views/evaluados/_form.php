<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\modal;

/* @var $this yii\web\View */
/* @var $model app\models\Evaluados */
/* @var $form yii\widgets\ActiveForm */

$sessiones = Yii::$app->user->identity->id;
$fechaactual = date("Y-m-d");

?>

<div class="evaluados-form">

  <div class="col-md-offset-2 col-sm-8 panel panel-default">
    <div class="panel-body" style="text-align: center;">
      <p><strong>Nota: </strong> Antes de crear un nuevo valorado, revisar que este no se encuentre creado previamente. </p>
      <p><strong>El nombre del nuevo valorado se debe ingresar con la estructura: APELLIDOS NOMBRES</strong></p>
    </div>
  </div>

  <?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
      'inputOptions' => ['autocomplete' => 'off']
    ]
  ]); ?>

  <?= $form->field($model, 'name')->textInput(['id' => 'idname', 'maxlength' => 150, 'style' => 'text-transform:uppercase;', 'onKeyUp' => 'this.value=this.value.toUpperCase();']) ?>

  <?= $form->field($model, 'dsusuario_red')->textInput(['id' => 'iddsusuario_red', 'maxlength' => 50]) ?>

  <?= $form->field($model, 'identificacion')->textInput(['id' => 'ididentificacion', 'maxlength' => 30]) ?>

  <?= $form->field($model, 'email')->textInput(['id' => 'idemail', 'maxlength' => 150]) ?>

  <?= $form->field($model, 'usua_id')->textInput(['id' => 'idusua_id', 'maxlength' => 150, 'value' => $sessiones, 'class' => 'hidden']) ?>

  <?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 150, 'value' => $fechaactual, 'class' => 'hidden']) ?>


  <div class="form-group">
    <div class="col-sm-offset-2 col-sm-10">
      <?= Html::submitButton(Yii::t('app', 'Create'), [
        'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => "modalButton2",
        'onclick' => 'validacion();'
      ]) ?>

      <?php
      if ($query2 != 0) {
        echo "<script>
                        Swal.fire('!!! Advertencia !!!','El usuario de red ya se encuentra registrado.','warning');                        
                     </script>";
      }
      ?>

      <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
    </div>
  </div>

  <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
  function validacion() {

    var varidname = document.getElementById("idname").value;
    var variddsusuario_red = document.getElementById("iddsusuario_red").value;
    var varididentificacion = document.getElementById("ididentificacion").value;

    if (varidname === '') {

      event.preventDefault();
      swal.fire("!!! Warning !!!"," Nombre no puede estar vacío ","warning");
      return;
    } else if (variddsusuario_red === '') {

      event.preventDefault();
      swal.fire("!!! Warning !!!"," Usuario de Red no puede estar vacío ","warning");
      return;
    }
    if (varididentificacion === '') {

      event.preventDefault();
      swal.fire("!!! Warning !!!"," Identificación no puede estar vacío ","warning");
      return;
    }

    if (varidname.length > 100) {

      event.preventDefault();
      swal.fire("Advertencia Nombre solo puede contener 0 - 100 caracteres");
      return;
    } else if (variddsusuario_red.length > 50) {

      event.preventDefault();
      swal.fire("Advertencia Usuario de Red solo puede contener 0 - 50 caracteres");
      return;
    } else if (varididentificacion.length > 30) {

      event.preventDefault();
      swal.fire("Advertencia Usuario de Red solo puede contener 0 - 30 caracteres");
      return;

    } 
    if (isNaN(varididentificacion)) {
      event.preventDefault();
      swal.fire("!!! Warning !!!"," Identificación no puede contener letras","warning");
    }

  }
</script>