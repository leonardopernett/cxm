<?php

use yii\helpers\Html;
/* use yii\widgets\ActiveForm; */
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Gruposusuarios */
/* @var $form yii\widgets\ActiveForm */
?>


<?php if ($isAjax) : ?>
    <?php Pjax::begin(['id' => 'form_usuarios-pj']);
    ?>
    <div class="gruposusuarios-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true]]); ?>

        <?= $form->field($model, 'nombre_grupo')->textInput(['maxlength' => 300]) ?>
        <?= $form->field($model, 'grupo_descripcion')->textInput(['maxlength' => 300]) ?>
        <?= $form->field($model, 'per_realizar_valoracion')->dropDownList(['1' => 'Si', '0' => 'No']) ?>
        <?= $form->field($model, 'usua_id_responsable')->dropDownList($model->getResponsableList()) ?>

        <?=  Html::hiddenInput('usuario', $usuario_id)?>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['index', 'usuario_id' => $usuario_id], ['class' => 'btn btn-default'])
                ?>
            </div>        
        </div>

        <?php ActiveForm::end(); ?>

    </div>
    <?php Pjax::end(); ?>

<?php else: ?>
    <div class="gruposusuarios-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?= $form->field($model, 'nombre_grupo')->textInput(['maxlength' => 300]) ?>
        <?= $form->field($model, 'grupo_descripcion')->textInput(['maxlength' => 300]) ?>

        <?= $form->field($model, 'per_realizar_valoracion')->dropDownList(['1' => 'Si', '0' => 'No']) ?>
    <?= $form->field($model, 'usua_id_responsable')->dropDownList($model->getResponsableList()) ?>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
            </div>        
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php endif; ?>
