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
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='tooltip']").tooltip(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
?>
<?php
Modal::begin([
    'header' => Yii::t('app', 'escalate form'),
    'id' => 'modal-escalarform',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>

<?php $this->title = Yii::t('app', 'Escalar Valoración'); ?>
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
        'options' => ['data-pjax' => true],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    
    <?=
            $form->field($model, 'valorado_id')
            ->widget(Select2::classname(), [
                //'data' => array_merge(["" => ""], $data),
                'language' => 'es',
                //'value'=>$modelTmpeje->evaluado_id,
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
                //'data' => array_merge(["" => ""], $data),
                'language' => 'es',
                'readonly' => true,
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
        $form->field($model, 'valorador_id')
        ->widget(Select2::classname(), [
            //'data' => array_merge(["" => ""], $data),
            'language' => 'es',
            'options' => ['placeholder' => Yii::t('app', 'Select ...')],
            'pluginOptions' => [
                'allowClear' => false,
                'minimumInputLength' => 4,
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                ],
            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['evaluadoresbyarbolseleccescalado']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results[0]);});
                                }
                            }')
            ]
                ]
        );
    ?>

     <div class="form-group">
        <div class="col-sm-3"></div>
        <div class="col-sm-6">
            <?= Html::radioList('tipo_interaccion', 1, 
                ['Continuar llamada escalada', 'Interacción Manual'], 
                ['separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;']) ?>
        </div>
    </div> 
    <?=
            $form->field($model, 'descripcion')
            ->textarea()
    ?>
    <a href="#" data-toggle="tooltip" data-placement="top" style="display: none; color: #000" title="<?php echo Yii::t('app', 'label send form')?>">
    <?=
            $form->field($model, 'enviar_form')
            ->checkbox();
    ?>
    </a>
    <?= Html::input("hidden", "RegistroEjec[ejec_form_id]", $modelTmpeje->id); ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Escalar'), ['class' => 'btn btn-success'])
            ?>            
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php Modal::end(); ?> 
