<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Transacions */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transacions-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'name')->textInput(['id' => 'idname','maxlength'=>150]) ?>

    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
            'onclick' => 'validacion();']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    function validacion() {

        var varidname = document.getElementById("idname").value;
       

        if (varidname === '') {

            event.preventDefault();
            swal.fire("!!! Warning !!!"," Nombre no puede estar vacío ","warning");
            return;
        }else if (varidname.length > 150) {

            event.preventDefault();
            swal.fire("Advertencia Nombre solo puede contener 0 - 150 caracteres");
            return;
        }

      



    }
</script>
