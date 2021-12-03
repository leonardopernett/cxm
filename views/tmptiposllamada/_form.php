<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Tmptiposllamada */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="tmptiposllamada-form">

    <?php yii\widgets\Pjax::begin(['id' => 'form_tmptiposllamada']); ?>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'options' => ['data-pjax' => true],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'tmpejecucionformulario_id')->hiddenInput(['value' => $model->tmpejecucionformulario_id])->label('') ?>

    <?php
    if (isset($model->tiposllamadasdetalle_id)) {
        $val = \app\models\Tiposllamadasdetalles::findOne($model->tiposllamadasdetalle_id);
        $tipLla = app\models\Tiposllamadas::findOne($val->tiposllamada_id);
        $model->tiposllamadas = $tipLla->id;
    }
    ?>
    <?=
            $form->field($model, 'tiposllamadas')
            ->dropDownList(\yii\helpers\ArrayHelper::map(app\models\Tiposllamadas::find()->orderBy('name')
                            ->all(), 'id', 'name'), ['id' => 'tiposllamadas-id', 'prompt' => Yii::t('app', 'Select ...')])
    ?> 

    <?php
    $items = [];
    if (isset($model->tiposllamadasdetalle_id)) {
        $val = \app\models\Tiposllamadasdetalles::findOne($model->tiposllamadasdetalle_id);
        $items = [$model->tiposllamadasdetalle_id => $val->name];
    }
    echo $form->field($model, 'tiposllamadasdetalle_id')
            ->dropDownList($items)->label(Yii::t('app', 'Tiposllamadasdetalle ID'));
    ?>

    <hr>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            <?=
            Html::a(Yii::t('app', 'Cancel'), ['index',
                'tmp_formulario_id' => $model->tmpejecucionformulario_id]
                    , ['class' => 'btn btn-default'])
            ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
    <?php yii\widgets\Pjax::end(); ?>

</div>

<script type="text/javascript">
    $("#tiposllamadas-id").change(function () {
        $.ajax({
            type: 'post',
            url: '<?php echo Url::to(['/tmptiposllamada/gettiposllamadasdetalles']); ?>',
            data: {
                tiposllamadas_id: $(this).val()
            },
            dataType: 'html',
            success: function (objRes) {
                $("#tmptiposllamada-tiposllamadasdetalle_id").html(objRes);
            }

        });
    });
</script>