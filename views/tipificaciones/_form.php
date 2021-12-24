<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Tipificaciones */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tipificaciones-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
    ]); ?>

    <?= $form->field($model, 'name')->textInput(['id' => 'idname','maxlength'=>100]) ?>

    <?= $form->field($model, 'nmorden')->textInput(['id' => 'idnmorden','maxlength'=>4]) ?>


    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                'onclick' => 'validacion();'
            ]) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>


</div>
<script type="text/javascript">
    function validacion() {

        var varidname = document.getElementById("idname").value;
        var varidnmorden = document.getElementById("idnmorden").value;

        if (varidname === '') {

            event.preventDefault();
            swal.fire("!!! Warning !!!"," Nombre no puede estar vacío ","warning");
            return;
        }
        if (varidnmorden === '') {

            event.preventDefault();
            swal.fire(" nmorden no puede estar vacío ");
            return;
        }

        if (varidname.length > 100) {

            event.preventDefault();
            swal.fire("Advertencia Nombre solo puede contener 0 - 100 caracteres");
            return;
        }

        if (varidnmorden.length > 4 ) {

            event.preventDefault();
            swal.fire("Advertencia nmorden solo puede contener 0 - 4 caracteres");
            return;
        }

        if (isNaN(varidnmorden)) {
            event.preventDefault();
            swal.fire("Advertencia solo ingresar numeros");
            return;
            
        }




    }
</script>