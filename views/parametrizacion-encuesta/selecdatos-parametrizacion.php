<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Formularios */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs(
        "
    function changePcrc(varPcrc){
        if(varPcrc != ''){
            $.ajax({
                url: '" . Url::to(['padreclientedato']) . "',
                type:'POST',
                dataType: 'json',
                data: {
                    'programa' : varPcrc
                },
                success : function(objRes){
                    $('#cliente').prop('value',objRes.value);
                }
            });
        }else{
            $('#cliente').prop('value','');
        }
    }

"
);
?>

<?php $this->title = Yii::t('app', 'Seleccionar Cliente y Programa');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Módulo Parametrización de Encuestas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
}
?>

<?php
$template = '<label for="pcrc" class="control-label col-sm-3">{label}</label><div class="col-sm-6">'
        . ' {input}{error}{hint}</div>';
?>
<div class="page-header">
    <h3><?= Yii::t('app', 'Seleccionar Cliente y Programa') ?></h3>
</div>

<div class="formularios-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?=
            $form->field($model, 'programa', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
            ->widget(Select2::classname(), [
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['getarbolehoja']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();                                
                                if (id !== "") {
                                    $.ajax({
                                        url: "' . Url::to(['padreclientedato']) . '",
                                        type:"POST",
                                        dataType: "json",
                                        data: {
                                            "pcrc" : id
                                        },
                                        success : function(objRes){
                                            $("#cliente").prop("value",objRes.value);
                                        }
                                    });
                                    $.ajax("' . Url::to(['getarbolehoja']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post",
                                    }).done(function(data) {
                                        callback(data.results[0]); 
                                         
                                    });
                                }
                            }')
                ],
                'pluginEvents' => [
                    "change" => "function() { changePcrc($(this).val()); }",
                ]
                    ]
    );
    ?>

<?= $form->field($model, 'cliente')->textInput(['maxlength' => 45, 'id' => 'cliente', 'readonly' => true]) ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Guardar'), ['class' => 'btn btn-success'])
            ?>            
        </div>        
    </div>

<?php ActiveForm::end(); ?>

</div>

