<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Bloques */
/* @var $form yii\widgets\ActiveForm */

?>

<div class="bloques-form">  

    <?php
    if ($isAjax) {
        yii\widgets\Pjax::begin(['id' => 'form_bloques']);
        $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true]]);
    } else {
        $form = ActiveForm::begin(['layout' => 'horizontal']);
    }
    ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => 100]) ?>
                      
    <?= $form->field($model, 'tipobloque_id')->dropDownList($model->getTipoBloquesList()) ?>

    <?= $form->field($model, 'nmorden')->textInput() ?>    

    <?= $form->field($model, 'dsdescripcion')->textInput(['maxlength' => 550]) ?>

    <?php if ($isAjax): ?>
        <?php if ($model->isNewRecord): ?>            
            <?= $form->field($model, 'seccionName')->textInput(['value'=>$model->getSeccion()->one()->name, 'disabled'=>true]) ?>
            <?= $form->field($model, 'formularioName')->textInput(['value'=>$model->getSeccion()->one()->formulario->name, 'disabled'=>true]) ?>
            <?= $form->field($model, 'seccion_id')->hiddenInput(['value'=>$seccion_id])->label('') ?>
        <?php else: ?>
            <?= $form->field($model, 'seccionName')->textInput(['value'=>$model->getSeccion()->one()->name, 'disabled'=>true]) ?>
            <?= $form->field($model, 'formularioName')->textInput(['value'=>$model->getSeccion()->one()->formulario->name, 'disabled'=>true]) ?>
        <?php endif; ?>
    <?php else: ?>
        <?php if ($model->isNewRecord): ?>
            <?= $form->field($model, 'seccion_id')->dropDownList($model->getSeccionsList()) ?>
        <?php else: ?>
            <?= $form->field($model, 'seccionName')->textInput(['value'=>$model->getSeccion()->one()->name, 'disabled'=>true]) ?>
            <?= $form->field($model, 'formularioName')->textInput(['value'=>$model->getSeccion()->one()->formulario->name, 'disabled'=>true]) ?>
        <?php endif; ?>
    <?php endif; ?>

    <div class="row">
        <div class="col-md-6">
            <p>
            <?= Yii::t('app', 'secciones_msg1') ?>
            </p><hr>          
            <?=
            $form->field($model, 'i1_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i2_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i3_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i4_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i5_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i6_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i7_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i8_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i9_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>

            <?=
            $form->field($model, 'i10_cdtipo_eval',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getOptionsList())
            ?>
        </div>                     
        <div class="col-md-6">
            <p>
            <?= Yii::t('app', 'secciones_msg2') ?>
            </p><hr>   
            <?=
            $form->field($model, 'i1_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i2_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i3_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i4_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i5_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i6_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i7_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i8_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i9_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?=
            $form->field($model, 'i10_nmfactor',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>
        </div>    
    </div>

    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app',
                                    'Update'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
            <?php if ($isAjax || (isset($filterSeccion) && $filterSeccion)) : ?>
                <?=
                Html::a(Yii::t('app', 'Cancel'),
                        ['index', 'seccion_id' => $model->seccion_id],
                        ['class' => 'btn btn-default'])
                ?>
            <?php else: ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['index'],
                        ['class' => 'btn btn-default']) ?>
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
