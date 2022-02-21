<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Formularios */
/* @var $form yii\widgets\ActiveForm */
?>
<?php
Modal::begin([
    'header' => Yii::t('app', 'add form'),
    'id' => 'modal-adicionarform',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>


<?php $this->title = Yii::t('app', 'Adicionar Valoración'); ?>
<?php $this->params['breadcrumbs'][] = $this->title; ?>

<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    echo '<div class="alert alert-' . $key . '" role="alert">' . $message . '</div>';
}
?>

<?= Yii::t('app', 'Realizar monitoreo') ?>

<div class="formularios-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ],
        'options' => ['data-pjax' => true]
        ]); ?>

    <?=
            $form->field($model, 'valorado_id')
            ->widget(Select2::classname(), [
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'allowClear' => false,
                    'minimumInputLength' => 4,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['evaluadosbyarbol', "arbol_id" => $model->pcrc_id]),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['evaluadosbyform']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results[0]);});
                                }
                            }')
                ]
                    ]
    );
    ?>
    <?=
            $form->field($model, 'pcrc_id')
            ->widget(Select2::classname(), [
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'allowClear' => false,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['getarbolesbyroles']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['getarbolesbyroles']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results[0]);});
                                }
                            }')
                ]
                    ]
    );
    ?>

    <?=
            $form->field($model, 'dimension_id')
            ->dropDownList($model->getDimensionsList()
                    , ['prompt' => 'Seleccione ...'])
    ?>
    <?= Html::input("hidden", "RegistroEjec[ejec_form_id]", $modelTmpeje->id); ?>

    <div class="form-group">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <?=
            Html::radioList('tipo_interaccion', 1, ['Continuar llamada escalada', 'Interacción Manual'], ['separator' => '&nbsp;&nbsp;&nbsp;&nbsp;'])
            ?>
        </div>
    </div> 

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Crear'), ['class' => 'btn btn-success', 'formtarget' => '_blank','id'=>'adicionarValoracion'])
            ?>   
        </div>        
    </div>

<?php ActiveForm::end(); ?>

</div>
<?php Modal::end(); ?> 
