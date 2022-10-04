<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Noticias */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="noticias-form">

    <?php if (Yii::$app->session->hasFlash('success')): ?>
        <div class="alert alert-success" role="alert">
            <?= Yii::$app->session->getFlash('success') ?>
        </div>
    <?php endif; ?>
    <?php if (Yii::$app->session->hasFlash('danger')): ?>
        <div class="alert alert-danger" role="alert">
            <?= Yii::$app->session->getFlash('danger') ?>
        </div>
    <?php endif; ?>
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal', 'id' => 'feedback',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?=
            $form->field($model, 'catfeedback')
            ->dropDownList(app\models\Categoriafeedbacks::getCategoriasList(), ['id' => 'cat-id', 'prompt' => Yii::t('app', 'Select ...')])
    ?>    

    <?php
    echo $form->field($model, 'tipofeedback_id')->widget(DepDrop::classname(), [
        'options' => ['id' => 'tipo-id'],
        'pluginOptions' => [
            'depends' => ['cat-id'],
            'placeholder' => Yii::t('app', 'Select ...'),
            'url' => Url::to(['/feedback/tipofeedback'])
        ]
    ]);
    ?>

    <?=    
        $form->field($model, 'arbol_id')
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['id'=>'idselectarbol','placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['reportes/getarboles']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['getarboles']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
            );            
    ?>
    
    <?php
    if (!$ajax) {
        echo $form->field($model, 'usua_id_lider')
                ->widget(Select2::classname(), [
                    'language' => 'es',
                    'options' => ['placeholder' => Yii::t('app', 'Select ...'),],
                    'pluginOptions' => [
                        'allowClear' => false,
                        'minimumInputLength' => 4,
                        'ajax' => [
                            'url' => Url::to(['lidereslist']),
                            'dataType' => 'json',
                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                        ],
                    ]
                        ]
        );
    }
    ?>

    <?php
    if (!$ajax) {

        echo $form->field($model, 'evaluado_id')->widget(Select2::classname(), [
                        'language' => 'es',
                        'name' => 'subi_calculo',
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select ...'),
                            'id' => 'subi_calculo'
                        ],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'maximumSelectionSize' => 5,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['evaluadolist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                                var id=$(element).val();
                                                if (id !== "") {
                                                    $.ajax("' . Url::to(['evaluadolist']) . '?id=" + id, {
                                                        dataType: "json",
                                                        type: "post"
                                                    }).done(function(data) { callback(data.results);});
                                                }
                                            }')
                        ]
                    ]);

    }
    ?>

    <?= $form->field($model, 'dscomentario')->textArea(['rows' => '6']) ?>
<?php if ($ajax) {
    echo Html::input("hidden", "id", $id, ["id" => "id"]);
} ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?php
            if ($ajax) {
                echo Html::a(Yii::t('app', 'Crear'), "javascript:void(0)", ['class' => 'btn btn-success soloCrear']);
            } else {
                echo Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']);
            }
            ?>

        </div>        
    </div>

<?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    $(document).ready(function () {
        $(".soloCrear").click(function () {
            datos = fnForm2Array('feedback');
            var bandera = false;
            for (i = 0; i < datos.length - 1; i++) {
                if (datos[i]['value'] === null || datos[i]['value'] === '') {
                    alert("Existen campos sin diligenciar" + datos[i]['name']);
                    return;
                }
            }
            ruta = '<?php echo Url::to(['create']); ?>';
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: datos
            });
        });
    });

    function fnForm2Array(strForm) {
        var arrData = new Array();
        $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
            if ($(this).attr('name')) {
                arrData.push({'name': $(this).attr('name'), 'value': $(this).val(), 'habilitado': $(this).attr("disabled")});
            }
        });
        return arrData;
    }
</script>
