<?php

use yii\bootstrap\ActiveForm;
?>
<div class="segundo-calificador-form">

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '" role="alert">' . $message . '</div>';
    }
    ?>
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true, 'id' => 'createSC'],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    <?php if (isset($model->argumento)): ?>
        <div class="row">
            <div class="col-lg-6 col-sm-offset-3">
                <h4><?= Yii::t('app', 'Información de la valoración') ?></h4>
                <br />
                <table class="table table-striped table-bordered detail-view">
                <caption>Tabla datos</caption>
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "Fecha"); ?></th>
                        <td><?= $formulario->created; ?></td>
                    </tr>
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "Instrumento para la Valoracion"); ?></th>
                        <td><?= $formulario->dsruta_arbol; ?></td>
                    </tr>
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "Dimension"); ?></th>
                        <td><?= $dimension; ?></td>
                    </tr>
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "Evaluador"); ?></th>
                        <td><?= $nmEvaluador; ?></td>
                    </tr>
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "Lider"); ?></th>
                        <td><?= $nmLider; ?></td>
                    </tr>
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "Evaluado ID"); ?></th>
                        <td><?= $modelEvaluado->name; ?></td>
                    </tr>    
                    <tr>
                        <th scope="col"><?php echo Yii::t("app", "Valoración"); ?></th>
                        <td>
                            <?=
                            \yii\helpers\Html::a(\yii\helpers\Html::img("@web/images/ico-view.png") .
                                    " Ver aquí"
                                    , \yii\helpers\Url::to(['formularios/showformulariodiligenciadoamigo'])
                                    . '?form_id=' . base64_encode($formulario->id)
                                    , ['style' => 'color: #fa142f!important']);
                            ?>
                        </td>
                    </tr>
                </table>
                <br />
                <h4><?= Yii::t('app', 'Seguimiento de la solicitud') ?></h4>
                <br />
                <table class="table table-striped table-bordered">   
                <caption>Tabla datos</caption>                 
                    <tbody>
                        <?php foreach ($modelCaso as $argumento): ?>
                            <tr>
                                <th scope="col"><?php echo $argumento->s_fecha ?></td>
                                <th scope="col"><?php echo $argumento->argumento ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table> 
            </div>

        </div>

        <?= $form->field($model, 'estado_sc')->dropDownList(['Escalado' => 'Escalado', 'Rechazado' => 'Rechazado', 'Aceptado' => 'Aceptado'], ['readonly' => true, 'disabled' => true]) ?>

    <?php endif; ?>
</div>