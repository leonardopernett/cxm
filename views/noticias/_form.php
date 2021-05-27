<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use dosamigos\ckeditor\CKEditor;

/* @var $this yii\web\View */
/* @var $model app\models\Noticias */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="noticias-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => 255]) ?>

    <?=
    $form->field($model, 'descripcion')->widget(CKEditor::className(), [
        'options' => ['rows' => 6],
        'preset' => 'full'
    ])
    ?>

    <?php //echo $form->field($model, 'activa')->textInput() ?>
    <?php echo $form->field($model, 'activa')->checkBox(['label' => "Activo"]);  ?>
    

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
