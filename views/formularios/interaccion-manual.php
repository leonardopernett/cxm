<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Formularios */
/* @var $form yii\widgets\ActiveForm */
?>
<?php $this->title = Yii::t('app', 'Realizar monitoreo'); ?>
<?php $this->params['breadcrumbs'][] = $this->title; ?>

<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {    
    echo '<div class="alert alert-' . $key . '" role="alert">' . $message . '</div>';
}
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Valorar-Interacción.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }
</style>
<script>
    $(document).ready(function(){
        $.fn.snow();
    });
</script>
<!-- Full Page Image Header with Vertically Centered Content -->
<script src="../../js_extensions/mijs.js"> </script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        
      </div>
    </div>
  </div>
</header>
<br><br>
<?= Yii::t('app', 'Realizar monitoreo') ?>

<div class="formularios-form">

    <?php $form = ActiveForm::begin([
      'layout' => 'horizontal',
      'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
      ]); ?>
<div class="col-md-offset-2 col-sm-8 panel panel-default">
  <div class="panel-body text-center">
    <p>Recuerda que para Valorar las dimensiones de OJT y Calidad del Entrenamiento, lo debes hacer solo con el formulario </p><p><strong>Índice de Calidad Entrenamiento Inicial</strong></p>
  </div>
</div>
    <?=
            $form->field($modelA, 'arbol_id')
            ->widget(Select2::classname(), [
                //'data' => array_merge(["" => ""], $data),
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
                //'initSelection' => new JsExpression($initScript)
                ]
                    ]
    );
    ?>

    <?=
            $form->field($modelD, 'dimension_id')
            ->dropDownList($modelD->getDimensionsList()
                    , ['prompt' => 'Seleccione ...'])
    ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-success'])
            ?>            
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
