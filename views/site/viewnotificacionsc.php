<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\bootstrap\ActiveForm;

$this->title = $model->id_segundo_calificador;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Segundo Calificadors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
Modal::begin([
    //'header' => Yii::t('app', 'Tbl Opcions'),
    'id' => 'modal-segundocalificador-dashboard',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="segundo-calificador-view">
    <?php
    Pjax::begin(['id' => 'segundocalificador-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true, 'id' => 'createSC'],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <div class="row">
        <div class="col-lg-10 col-sm-offset-1" style="height: 300px; overflow: auto">
            <table class="table table-striped table-bordered">
            <caption>Tabla datos</caption>
                <thead>                    
                    <tr>
                        <th scope="col"><?= Yii::t('app', 'Fecha') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Argumento') ?></th>
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

    <?= $form->field($model, 'argumentoLider')->textarea(['rows' => 6]) ?>
    <?php if ($model->id_responsable == $model->id_evaluador): ?>
        <?php $arrayEstados = ['Aceptado' => 'Aceptado', 'Rechazado' => 'Rechazado'] ?>
    <?php else: ?>
        <?php if ($model->b_segundo_envio == 1): ?>
            <?php $arrayEstados = ['Aceptado' => 'Aceptado', 'Rechazado' => 'Rechazado'] ?>
        <?php else: ?>
            <?php $arrayEstados = ['Escalado' => 'Escalado', 'Rechazado' => 'Rechazado'] ?>
        <?php endif; ?>
    <?php endif; ?>
    <?= $form->field($model, 'estado_sc')->dropDownList($arrayEstados) ?>

    <?= Html::hiddenInput('scid', $scid) ?>
    <?= Html::hiddenInput('id_caso', $id_caso) ?>
    <?= Html::hiddenInput('fid', $id_ejecucion_formulario) ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::a(Yii::t('app', 'enviar'), "javascript:void(0)", ['class' => $model->isNewRecord ? 'btn btn-success .soloEnviar' : 'btn btn-primary .soloEnviarUpdate', 'id' => 'enviarForm'])
            ?>            
        </div>        
    </div>

    <?php ActiveForm::end(); ?>


    <?php Pjax::end(); ?>

</div>
<script type="text/javascript">
    $(document).ready(function () {
        $("#enviarForm").click(function () {
            var guardarFormulario = $("#createSC");
            guardarFormulario.submit();
        });
    });
</script>
<?php Modal::end(); ?>