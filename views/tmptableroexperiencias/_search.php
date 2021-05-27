<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TmptableroexperienciasSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tmptableroexperiencias-search">

    <?php
    $form = ActiveForm::begin([
                'action' => ['index'],
                'method' => 'get',
    ]);
    ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tmpejecucionformulario_id') ?>

    <?= $form->field($model, 'tableroenfoque_id') ?>

    <?= $form->field($model, 'tableroproblemadetalle_id') ?>

        <?= $form->field($model, 'detalle') ?>

    <div class="form-group">
<?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-primary']) ?>
    <?= Html::resetButton(Yii::t('app', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

<?php ActiveForm::end(); ?>

</div>
