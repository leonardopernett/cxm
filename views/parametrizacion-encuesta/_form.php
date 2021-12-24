<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\ParametrizacionEncuesta */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="parametrizacion-encuesta-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
    ]); ?>

    <?= $form->field($model, 'cliente')->textInput(['id' => 'idcliente']) ?>

    <?= $form->field($model, 'programa')->textInput(['id' => 'idprograma']) ?>


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

        var varidcliente = document.getElementById("idcliente").value;
        var varidprograma = document.getElementById("idprograma").value;


        if (varidcliente === '') {

            event.preventDefault();
            swal.fire("!!! Warning !!!", " Cliente no puede estar vacío", "warning");
            return;
        } else if (varidcliente.length > 11) {

            event.preventDefault();
            swal.fire("!!! Warning !!!", "Nombre solo puede contener 0 - 11 caracteres", "warning");
            return;
        }
        if (varidprograma === '') {

            event.preventDefault();
            swal.fire("!!! Warning !!!", " Programa/PCRC no puede estar vacío", "warning");
            return;
        } else if (varidprograma.length > 11) {

            event.preventDefault();
            swal.fire("!!! Warning !!!", "Programa/PCRC solo puede contener 0 - 11 caracteres", "warning");
            return;
        }
    }
</script>