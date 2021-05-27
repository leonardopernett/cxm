<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\file\FileInput;
use yii\helpers\Url;
use yii\bootstrap\Alert;

/* @var $this yii\web\View */
/* @var $model app\models\Slides */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="slides-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= $form->field($model, 'titulo')->textInput(['maxlength' => 100]) ?>

    <?= $form->field($model, 'descripcion')->textInput(['maxlength' => 255]) ?>

    <?php if (isset($model->imagen)) : ?>
        <div class="form-group">
            <div class="col-sm-3"></div>
            <div class="col-sm-6">
                <?php echo Html::img(Url::to("@web/images/uploads/") . $model->imagen, ["style" => "width: 400px;"]); ?>
                <br /><br />
                <?php
                echo Alert::widget([
                    'options' => [
                        'class' => 'alert-info',
                    ],
                    'body' => Yii::t('app', 'Para cambiar esta imagen'),
                ]);
                ?>
            </div>
        </div>
    <?php endif; ?>
    <?php
    echo $form->field($model, 'imagen')->widget(FileInput::classname(), [
        'options' => [
            'accept' => 'image/*',
        ],
        'pluginOptions' => [
            'showUpload' => false,
        ]
    ]);
    ?>

<?php echo $form->field($model, 'activo')->checkBox(['label' => "Activo"]); ?>


    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

<?php ActiveForm::end(); ?>

</div>
