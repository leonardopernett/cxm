<?php

use yii\helpers\Html;

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

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => ['data-pjax' => true],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
        ]); ?>

        <?= $form->field($model, 'nombre_grupo')->textInput(['id' => 'idnombre_grupo1', 'maxlength' => 300]) ?>
        <?= $form->field($model, 'grupo_descripcion')->textInput(['maxlength' => 300]) ?>
        <?= $form->field($model, 'per_realizar_valoracion')->dropDownList(['1' => 'Si', '0' => 'No']) ?>
        <?= $form->field($model, 'usua_id_responsable')->dropDownList($model->getResponsableList()) ?>

        <?= Html::hiddenInput('usuario', $usuario_id) ?>


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

<?php else : ?>
    <div class="gruposusuarios-form">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
        ]); ?>

        <?= $form->field($model, 'nombre_grupo')->textInput(['id' => 'idnombre_grupo','maxlength' => 300]) ?>
        <?= $form->field($model, 'grupo_descripcion')->textInput(['id' => 'idgrupo_descripcion','maxlength' => 300]) ?>

        <?= $form->field($model, 'per_realizar_valoracion')->dropDownList(['1' => 'Si', '0' => 'No']) ?>
        <?= $form->field($model, 'usua_id_responsable')->dropDownList($model->getResponsableList()) ?>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), [
                    'class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                    'onclick' => 'validacion();']) ?>
                <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
            </div>
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php endif; ?>
<script type="text/javascript">
    function validacion() {

        var varidnombre_grupo = document.getElementById("idnombre_grupo").value;
     


        if (varidnombre_grupo === '') {
            event.preventDefault();
            swal.fire("!!! Warning !!!"," Nombre no puede estar vacío ","warning");
            return;
        }
        if (varidnombre_grupo.length>300) {
            event.preventDefault();
            swal.fire("!!! Warning !!!","No se pueden 0 - 300 caracteres","warning");
            return;
        }else if (idgrupo_descripcion.length>300)
        {
            event.preventDefault();
            swal.fire("!!! Warning !!!","No se pueden 0 - 300 caracteres","warning");
            return;
        }



    }
   
</Script>