<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\bootstrap\modal;

/* @var $this yii\web\View */
/* @var $model app\models\Evaluados */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="evaluados-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>    

    <?= $form->field($model, 'dsusuario_red')->textInput(['maxlength' => 50]) ?>  

    <?= $form->field($model, 'identificacion')->textInput(['maxlength' => 30]) ?>

    <?= $form->field($model, 'email')->textInput(['maxlength' => 150]) ?>

    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
        <?= Html::submitButton(Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>"modalButton2"]) ?>

            <?php
                if ($query != 0) {
                    echo "<script>
                            Swal.fire('¡¡¡ Advertencia !!!','El usuario de red ya se encuentra registrado.','warning');                        
                         </script>";
                }
            ?>

        <?= Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
