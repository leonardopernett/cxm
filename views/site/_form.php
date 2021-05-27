<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

?>
<?php if ($bandera == 0): ?>
    <div class="segundo-calificador-form">

        <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true, 'id' => 'createSC']]); ?>


        <?= $form->field($model, 'argumentoAsesor')->textarea(['rows' => 6]) ?>

        <?= Html::hiddenInput('historico', $historico) ?>

        <?= Html::hiddenInput('fid', $fid) ?>
        <?= Html::hiddenInput('bandera', $bandera) ?>
        <?= Html::hiddenInput('esLider', $esLider) ?>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <?= Html::a($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), "javascript:void(0)", ['class' => $model->isNewRecord ? 'btn btn-success .soloEnviar' : 'btn btn-primary .soloEnviarUpdate', 'id' => 'enviarForm'])
                ?>                
            </div>        
        </div>

        <?php ActiveForm::end(); ?>

    </div>

<?php else: ?>

    <div class="segundo-calificador-form">


        <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true, 'id' => 'createSC']]); ?>
        <?php if (isset($model->argumento)): ?>
            <div class="row">
                <div class="col-lg-10 col-sm-offset-1" style="height: 300px; overflow: auto">
                    <table class="table table-striped table-bordered">
                        <thead>                    
                            <tr>
                                <th><?= Yii::t('app', 'Fecha') ?></th>
                                <th><?= Yii::t('app', 'Seguimiento de la solicitud') ?></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($modelCaso as $argumento): ?>
                                <tr>
                                    <td><?php echo $argumento->s_fecha ?></td>
                                    <td><?php echo $argumento->argumento ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table> 
                </div>

            </div>
        <?php endif; ?>

        <?php if ($model->b_editar == 1): ?>
            <?= $form->field($model, 'argumentoAsesor')->textarea(['rows' => 6]) ?>

            <?= $form->field($model, 'estado_sc')->dropDownList(['Escalado' => 'Escalado']) ?>
        <?php else: ?>
            <?= $form->field($model, 'estado_sc')->dropDownList(['Escalado' => 'Escalado', 'Rechazado' => 'Rechazado', 'Aceptado' => 'Aceptado'], ['readonly' => true, 'disabled' => true]) ?>
        <?php endif; ?>

        <?= Html::hiddenInput('bandera', $bandera) ?>
        <?= Html::hiddenInput('historico', $historico) ?>

        <?= Html::hiddenInput('scid', $scid) ?>
        <?php if ($model->b_editar == 1): ?>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <?= Html::a(Yii::t('app', 'enviar'), "javascript:void(0)", ['class' => $model->isNewRecord ? 'btn btn-success .soloEnviar' : 'btn btn-primary .soloEnviarUpdate', 'id' => 'enviarForm'])
                    ?>                    
                </div>        
            </div>
        <?php endif; ?>

        <?php ActiveForm::end(); ?>

    </div>
<?php endif; ?>
<script type="text/javascript">
    $(document).ready(function () {
        $("#enviarForm").click(function () {
            var guardarFormulario = $("#createSC");
            guardarFormulario.submit();
            $('#modal-segundocalificador').modal('hide');
        });
    });
</script>