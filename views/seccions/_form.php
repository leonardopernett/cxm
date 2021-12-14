<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Seccions */
/* @var $form yii\widgets\ActiveForm */
?>


<div class="seccions-form">
    <?php
    if ($isAjax) {
        yii\widgets\Pjax::begin(['id' => 'form_seccions']);
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

    <div class="row">
        <div class="col-md-6">        
            <?=
            $form->field($model, 'name',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput(['maxlength' => 100])
            ?>

            <?=
            $form->field($model, 'tiposeccion_id',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getTipoSeccionsList())
            ?>           
            <?= $form->field($model, 'is_pits',
                    ['labelOptions' => ['class' => 'col-md-6']])->dropDownList([0=>'No',1=>'Si'])
            ?>   
            <?= $form->field($model, 'sdescripcion',
                    ['labelOptions' => ['class' => 'col-md-6']])->textarea()
            ?>
        </div>           
        <div class="col-md-6">
            <?= $form->field($model, 'nmorden',
                    ['labelOptions' => ['class' => 'col-md-6']])->textInput()
            ?>

            <?= $form->field($model, 'sndesplegar_comentario',
                    ['labelOptions' => ['class' => 'col-md-6']])->checkbox()
            ?>           
        </div>        
    </div>

    <?php if ($isAjax): ?>    
        <?= $form->field($model, 'formulario_id')->hiddenInput(['value'=>$formulario_id])->label('') ?>
    <?php else: ?>
        <?php if ($model->isNewRecord): ?>
        <div class="row">
            <div class="col-md-6">
                <?=
                $form->field($model, 'formulario_id',
                        ['labelOptions' => ['class' => 'col-md-6']])->dropDownList($model->getFormulariosList())
                ?>
            </div>
            <div class="col-md-6"></div>
        </div>
        <?php else: ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                    <?= Html::label(Yii::t('app', 'Formulario'), null,
                            ['class' => 'col-sm-6 col-md-6']) ?>
                    <?= Html::label($model->getFormulario()->one()->name,
                            null, ['class' => 'col-sm-6  col-md-6']) ?>
                    </div>
                </div>
            </div>
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
            $form->field($model, 'i10_nmfactor', ['labelOptions' => ['class' => 'col-md-6']])->textInput()
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
            <?php if ($isAjax) : ?>
                <?=
                Html::a(Yii::t('app', 'Cancel'),
                        ['index', 'formulario_id' => $formulario_id],
                        ['class' => 'btn btn-default'])
                ?>
            <?php else: ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
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



