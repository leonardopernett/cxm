<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Bloquedetalles */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="bloquedetalles-form">

    <?php
    if ($isAjax) {
        yii\widgets\Pjax::begin(['id' => 'form_bloquesDetalles']);
        $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => ['data-pjax' => true],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
        ]);
    } else {
        $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
        ]);
    }
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 150]) ?>

    <?php if ($isAjax): ?>
        <?php if ($model->isNewRecord): ?>            
            <?= $form->field($model, 'bloqueName')->textInput(['value' => $model->getBloque()->one()->name, 'disabled' => true]) ?>
            <?= $form->field($model, 'seccionName')->textInput(['value' => $model->getBloque()->one()->seccion->name, 'disabled' => true]) ?>
            <?= $form->field($model, 'formularioName')->textInput(['value' => $model->getBloque()->one()->seccion->formulario->name, 'disabled' => true]) ?>
            <?= $form->field($model, 'bloque_id')->hiddenInput(['value' => $bloque_id])->label('') ?>
        <?php else: ?>
            <?= $form->field($model, 'bloqueName')->textInput(['value' => $model->getBloque()->one()->name, 'disabled' => true]) ?>
            <?= $form->field($model, 'seccionName')->textInput(['value' => $model->getBloque()->one()->seccion->name, 'disabled' => true]) ?>
            <?= $form->field($model, 'formularioName')->textInput(['value' => $model->getBloque()->one()->seccion->formulario->name, 'disabled' => true]) ?>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($model->isNewRecord): ?>
            <?= $form->field($model, 'bloque_id')->dropDownList($model->getBloqueList()) ?>
        <?php else: ?>
            <?= $form->field($model, 'bloqueName')->textInput(['value' => $model->getBloque()->one()->name, 'disabled' => true]) ?>
            <?= $form->field($model, 'seccionName')->textInput(['value' => $model->getBloque()->one()->seccion->name, 'disabled' => true]) ?>
            <?= $form->field($model, 'formularioName')->textInput(['value' => $model->getBloque()->one()->seccion->formulario->name, 'disabled' => true]) ?>
        <?php endif; ?>
    <?php endif; ?>        

    <?= $form->field($model, 'calificacion_id')->dropDownList($model->getCalificacionList()) ?>

    <?= $form->field($model, 'tipificacion_id')->dropDownList($model->getTipificacionList(), ["prompt" => Yii::t("app", "Ninguno")]) ?>

    <?= $form->field($model, 'nmorden')->textInput() ?>

    <?= $form->field($model, 'i1_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i2_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i3_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i4_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i5_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i6_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i7_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i8_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i9_nmfactor')->textInput() ?>

    <?= $form->field($model, 'i10_nmfactor')->textInput() ?>

    <?= $form->field($model, 'c_pits')->dropDownList([1 => 'Si', 0 => 'No']) ?>

    <?= $form->field($model, 'id_seccion_pits')->dropDownList(\yii\helpers\ArrayHelper::map(\app\models\Seccions::find()->where(['formulario_id'=>($model->getBloque()->one()->seccion->formulario->id)])->all(), 'id', 'name'),['prompt' => 'Seleccione ...']) ?>  
    
    <?= $form->field($model, 'descripcion')->textarea() ?>

    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
            <?php if ($isAjax || (isset($filterBloque) && $filterBloque)) : ?>
                <?=
                Html::a(Yii::t('app', 'Cancel'), ['index', 'bloque_id' => $model->bloque_id], ['class' => 'btn btn-default'])
                ?>
            <?php else: ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default'])
                ?>
            <?php endif; ?>
        </div>        
    </div>

            <?php ActiveForm::end(); ?>
<?php
if ($isAjax) {
    yii\widgets\Pjax::end();
}
?>
</div>
