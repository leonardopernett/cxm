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
        <p><strong>Nota: </strong> Antes de crear un nuevo valorado, revisar que este no se encuentre creado previamente. </p><p><strong>El nombre del nuevo valorado se debe ingresar con la estructura: APELLIDOS NOMBRES</strong></p>
      </div>
    </div>

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 150, 'style' => 'text-transform:uppercase;','onKeyUp' => 'this.value=this.value.toUpperCase();']) ?>    

    <?= $form->field($model, 'dsusuario_red')->textInput(['maxlength' => 50]) ?>  

    <?= $form->field($model, 'identificacion')->textInput(['maxlength' => 30]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 150]) ?>

    <?= $form->field($model, 'usua_id')->textInput(['maxlength' => 150, 'value'=>$sessiones, 'class'=>'hidden']) ?>

    <?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 150, 'value'=>$fechaactual, 'class'=>'hidden']) ?>

    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
        <?= Html::submitButton(Yii::t('app', 'Create'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>"modalButton2"]) ?>

        <?php
            if ($query2 != 0) {
                echo "<script>
                        Swal.fire('!!! Advertencia !!!','El usuario de red ya se encuentra registrado.','warning');                        
                     </script>";        
            }
        ?>

        <?= Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
