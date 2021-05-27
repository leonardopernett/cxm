<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Tmpejecucionfeedbacks */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tmpejecucionfeedbacks-form">

    <?php yii\widgets\Pjax::begin(['id' => 'form_tmpejecucionfeedbacks']); ?>

    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true, 'id' => 'formFeedback']]); ?>

    <?php
    if (isset($model->tipofeedback_id)) {
        $val = \app\models\Tipofeedbacks::findOne($model->tipofeedback_id);
        $tipLla = app\models\Categoriafeedbacks::findOne($val->categoriafeedback_id);
        $model->catfeedback = $tipLla->id;
    }
    ?>
    <?=
            $form->field($model, 'catfeedback')
            ->dropDownList(app\models\Categoriafeedbacks::getCategoriasList()
                    , ['id' => 'cat-id'
                , 'prompt' => Yii::t('app', 'Select ...')])
    ?>

    <?php
    $items = [];
    if (isset($model->tipofeedback_id)) {
        $val = \app\models\Tipofeedbacks::findOne($model->tipofeedback_id);
        $items = [$model->tipofeedback_id => $val->name];
    }
    echo $form->field($model, 'tipofeedback_id')
            ->dropDownList($items)->label(Yii::t('app', 'Tipofeedback ID'));
    ?>

    <?= $form->field($model, 'dscomentario')->textarea(['rows' => 6]) ?>

    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::a($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), 'javascript:void(0)', ['class' => $model->isNewRecord ? 'btn btn-success create' : 'btn btn-primary update']) ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'), 'javascript:void(0)'
                    , ['class' => 'btn btn-default volver'])
            ?>
        </div>        
    </div>

    <div style="height: 10px">
        <?= $form->field($model, 'usua_id_lider')->hiddenInput(['value' => $model->usua_id_lider])->label(false) ?>

        <?= $form->field($model, 'evaluado_id')->hiddenInput(['value' => $model->evaluado_id])->label(false) ?>

        <?= $form->field($model, 'tmpejecucionformulario_id')->hiddenInput(['value' => $model->tmpejecucionformulario_id])->label(false) ?>

        <?= $form->field($model, 'usua_id')->hiddenInput(['value' => Yii::$app->user->identity->id])->label(false) ?>

        <?= $form->field($model, 'created')->hiddenInput(['value' => date("Y-m-d H:i:s")])->label('') ?>

        <?= $form->field($model, 'basessatisfaccion_id')->hiddenInput(['value' => $model->basessatisfaccion_id])->label('') ?>

    </div>


    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>

</div>

<script type="text/javascript">
    $("#cat-id").change(function () {
        $.ajax({
            type: 'post',
            url: '<?php echo Url::to(['/tmpejecucionfeedbacks/gettipofeedback']); ?>',
            data: {
                cat_id: $(this).val()
            },
            dataType: 'html',
            success: function (objRes) {
                $("#tmpejecucionfeedbacks-tipofeedback_id").html(objRes);
            }

        });
    });

    $(".volver").click(function () {
        ruta = '<?php
    echo Url::to(['index', 'tmp_formulario_id' => $model->tmpejecucionformulario_id
        , 'usua_id_lider' => $model->usua_id_lider
        , 'evaluado_id' => $model->evaluado_id
        , 'basessatisfaccion_id' => $model->basessatisfaccion_id]);
    ?>';
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            success: function (response) {
                $('#ajax_div_feedbacks').html(response);
            }
        });
    });

    $(".update").click(function () {
        ruta = '<?php echo Url::to(['update', 'id' => $model->id]); ?>';
        datosValidar = fnForm2ArrayValidar('formFeedback');
        for (i = 0; i < datosValidar.length; i++) {
            if (datosValidar[i]['value'] == null || datosValidar[i]['value'] == '') {
                alert("Hay campos sin diligenciar");
                return;
            }
        }
        datos = fnForm2Array('formFeedback');
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            data: datos,
            success: function (response) {
                $('#ajax_div_feedbacks').html(response);
            }
        });
    });

    $(".create").click(function () {
        ruta = '<?php
    echo Url::to(['create', 'tmp_formulario_id' => $model->tmpejecucionformulario_id
        , 'usua_id_lider' => $model->usua_id_lider
        , 'evaluado_id' => $model->evaluado_id
        , 'basessatisfaccion_id' => $model->basessatisfaccion_id]);
    ?>';
        datosValidar = fnForm2ArrayValidar('formFeedback');
        for (i = 0; i < datosValidar.length; i++) {
            if (datosValidar[i]['value'] == null || datosValidar[i]['value'] == '') {
                alert("Hay campos sin diligenciar");
                return;
            }
        }
        datos = fnForm2Array('formFeedback');
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            data: datos,
            success: function (response) {
                $('#ajax_div_feedbacks').html(response);
            }
        });
    });

    function fnForm2Array(strForm) {

        var arrData = new Array();

        $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
            if ($(this).attr('name')) {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
            }
        });

        return arrData;

    }

    function fnForm2ArrayValidar(strForm) {

        var arrData = new Array();

        $("input[type=text],  input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
            if ($(this).attr('name')) {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
            }
        });

        return arrData;

    }
</script>