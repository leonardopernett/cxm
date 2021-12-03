<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Tmptableroexperiencias */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tmptableroexperiencias-form">
    <?php
    if (!$model->isNewRecord) {
        //CONSULTO EL PROBLEMA ID
        $tmp_problema_id = \app\models\Arboles::find()
                ->select("tableroproblema_id")
                ->where(["id" => $arbol_id])
                ->all();
        $problema_id = $tmp_problema_id[0]["tableroproblema_id"];
    }
    ?>

    <?php yii\widgets\Pjax::begin(['id' => 'form_tmptableroexperiencias']); ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true, 'id' => 'formProblems'],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'tmpejecucionformulario_id')->hiddenInput(['value' => $model->tmpejecucionformulario_id])->label('') ?>

    <?=
            $form->field($model, 'tableroenfoque_id')
            ->dropDownList(\yii\helpers\ArrayHelper::map(
                            app\models\Tableroenfoques::find()
                            ->join("JOIN"
                                    , "tbl_tableroproblemadetalles"
                                    , "`tbl_tableroenfoques`.id = `tbl_tableroproblemadetalles`.`tableroenfoque_id`")
                            ->where(["tableroproblema_id" => $problema_id])
                            ->groupBy("`tableroenfoque_id`")
                            ->orderBy('name')
                            ->all(), 'id', 'name')
                    , ['prompt' => 'Seleccione ...', 'id' => 'tab-id'])->label(Yii::t("app", "Seleccione enfoque"));
    ?>

    <?php
    $items = [];
    if (isset($model->tableroproblemadetalle_id)) {
        $val = \app\models\Tableroproblemadetalles::findOne($model->tableroproblemadetalle_id);
        $items = [$model->tableroproblemadetalle_id => $val->name];
    }
    echo $form->field($model, 'tableroproblemadetalle_id')
            ->dropDownList($items)->label(Yii::t('app', 'Tableroproblemadetalle ID'));
    ?>

    <?= $form->field($model, 'detalle')->textarea(['rows' => 6]) ?>

    <div style="height: 10px">
        <?= $form->field($model, 'arbol_id')->hiddenInput(['value' => Yii::$app->request->get('arbol_id')])->label(false) ?>
    </div>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::a($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), 'javascript:void(0)'
                    , ['class' => $model->isNewRecord ? 'btn btn-success create' : 'btn btn-primary update'])
            ?>           
            <?=
            Html::a(Yii::t('app', 'Cancel'), 'javascript:void(0)'
                    , ['class' => 'btn btn-default volver'])
            ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>

</div>

<script type="text/javascript">
    $("#tab-id").change(function () {
        $.ajax({
            type: 'post',
            url: '<?php echo Url::to(['/tmptableroexperiencias/gettableroproblemadetalle']); ?>',
            data: {
                tab_id: $(this).val()
            },
            dataType: 'html',
            success: function (objRes) {
                $("#tmptableroexperiencias-tableroproblemadetalle_id").html(objRes);
            }

        });
    });



    $(".volver").click(function () {
        ruta = '<?php echo Url::to(['index', 'tmp_formulario_id' => $model->tmpejecucionformulario_id, 'arbol_id' => (isset($arbol_id))?$arbol_id:Yii::$app->request->get('arbol_id')]); ?>';
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            success: function (response) {
                $('#ajax_div_problemas').html(response);
            }
        });
    });

    $(".update").click(function () {
        ruta = '<?php echo Url::to(['update', 'id' => $model->id, 'arbol_id' => (isset($arbol_id))?$arbol_id:Yii::$app->request->get('arbol_id')]); ?>';
        datosValidar = fnForm2ArrayValidar('formProblems');
        for (i = 0; i < datosValidar.length; i++) {
            if (datosValidar[i]['value'] == null || datosValidar[i]['value'] == '') {
                alert("Hay campos sin diligenciar");
                return;
            }
        }
        datos = fnForm2Array('formProblems');
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            data: datos,
            success: function (response) {
                $('#ajax_div_problemas').html(response);
            }
        });
    });

    $(".create").click(function () {
        ruta = '<?php echo Url::to(['create', 'tmp_formulario_id' => $model->tmpejecucionformulario_id, 'arbol_id' => Yii::$app->request->get('arbol_id')]); ?>';
        datosValidar = fnForm2ArrayValidar('formProblems');
        for (i = 0; i < datosValidar.length; i++) {
            if (datosValidar[i]['value'] == null || datosValidar[i]['value'] == '') {
                alert("Hay campos sin diligenciar");
                return;
            }
        }
        datos = fnForm2Array('formProblems');
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            data: datos,
            success: function (response) {
                $('#ajax_div_problemas').html(response);
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