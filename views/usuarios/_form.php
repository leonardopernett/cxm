<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */
/* @var $form yii\widgets\ActiveForm */

    $fechaactual = date("Y-m-d");
?>
<?php if ($isAjax) : ?>

    <div class="usuarios-form">
        <?php Pjax::begin(['id' => 'form_usuarios-pj']);
        ?> 
        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'options' => [ 'data-pjax' => true],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
            ]); ?>

        <?= $form->field($model, 'usua_usuario')->textInput(['maxlength' => 50]) ?>

        <?= $form->field($model, 'usua_nombre')->textInput(['maxlength' => 150]) ?>

        <?= $form->field($model, 'usua_identificacion')->textInput(['maxlength' => 30]) ?>

        <?= $form->field($model, 'usua_activo')->dropDownList(['S' => 'Si', 'N' => 'No'], ['prompt' => ''])
        ?>
        <?= $form->field($model, 'usua_email')->textInput(['maxlength' => 150]) ?>

        <?= $form->field($model, 'rol')->dropDownList($model->getRolesList(), ['prompt' => Yii::t('app', 'Select ...')]); ?>

        <?= Html::hiddenInput('grupo', $grupo_id) ?>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?=
                Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                ?>

                <?php
                    if ($query != 0) {
                        echo "<script>
                                Swal.fire('¡¡¡ Advertencia !!!','El usuario de red ya se encuentra registrado.','warning');                        
                             </script>";
                    }
                ?>

                <?= Html::a(Yii::t('app', 'Cancel'), ['index', 'grupo_id' => $grupo_id], ['class' => 'btn btn-default'])
                ?>
            </div>        
        </div>

        <?php ActiveForm::end(); ?>
        <?php Pjax::end(); ?>
    </div>
<?php else: ?>
    <div class="usuarios-form">

        <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
              ]
            ]); ?>

        <?= $form->field($model, 'usua_usuario')->textInput(['id'=>'idusua_usuario','maxlength'=>50]) ?>

        <?= $form->field($model, 'usua_nombre')->textInput(['id'=>'idusua_nombre','maxlength'=>150]) ?>

        <?= $form->field($model, 'usua_identificacion')->textInput(['id'=>'idusua_identificacion','maxlength'=>30]) ?>

        <?= $form->field($model, 'usua_activo')->dropDownList(['S' => 'Si', 'N' => 'No'], ['prompt' => ''])
        ?>
        <?= $form->field($model, 'usua_email')->textInput(['maxlength' => 150]) ?>


        <?= $form->field($model, 'rol')->dropDownList($model->getRolesList(), ['prompt' => Yii::t('app', 'Select ...')]); ?>
        <?= $form->field($model, 'grupo')->dropDownList($model->getGruposusuariosList(), ['prompt' => Yii::t('app', 'Select ...')]); ?>

	<?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 150, 'value'=>$fechaactual, 'class'=>'hidden']) ?>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?=
                Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'send_button',
                'onclick'=>'validacion()'])
                ?>
                <?php
                    if ($query != 0) {
                        echo "<script>
                                Swal.fire('¡¡¡ Advertencia !!!','El usuario de red ya se encuentra registrado.','warning');                        
                             </script>";
                    }
                ?>

                <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default'])
                ?>
            </div>        
        </div>

        <?php ActiveForm::end(); ?>

    </div>
<?php endif; ?>

<script type="text/javascript">
    $(function(){
        $('#send_button').on("click", function(e){
            e.preventDefault()
                        
            $('#w0').submit()
                       
        })
    })

    function validacion(){
           var varidusua_usuario = document.getElementById("idusua_usuario").value
           var varidusua_nombre = document.getElementById("idusua_nombre").value
           var varidusua_identificacion = document.getElementById("idusua_identificacion").value

           if (varidusua_usuario === '') {
               swal.fire('¡¡¡ Advertencia !!!','Por favor ingresar un Usuario')
               return;
           }else if (varidusua_usuario.length>50) {
               swal.fire('¡¡¡ Advertencia !!!','Por favor ingresar en Usuario caracteres 0 - 50')
               return;
           }
           if (varidusua_nombre === '') {
               swal.fire('¡¡¡ Advertencia !!!','Por favor ingresar un Apellido y Nombres')
               return;
           }else if (varidusua_nombre.length>150) {
               swal.fire('¡¡¡ Advertencia !!!','Por favor ingresar en Apellido y Nombres caracteres 0 - 150')
               return;
           }
           if (varidusua_identificacion === '') {
               swal.fire('¡¡¡ Advertencia !!!','Por favor ingresar una identificación')
               return;
           }else if (varidusua_identificacion.length>30){
            swal.fire('¡¡¡ Advertencia !!!','Por favor ingresar en identificación caracteres 0 - 30')
               return;
           }

    }
</script>
