<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Formularios */
/* @var $form yii\widgets\ActiveForm */

$sessiones = Yii::$app->user->identity->id;
?>

<?php $this->title = Yii::t('app', 'Realizar Encuesta Telefónica'); ?>
<?php $this->params['breadcrumbs'][] = $this->title; ?>
<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
}
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Encuestas-telefonicas.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }
</style>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<!--<div class="page-header">
    <h3><?= Yii::t('app', 'Realizar Encuesta Telefónica') ?></h3>
</div>-->

<div class="formularios-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <?= $form->field($model, 'identificacion')->textInput(['maxlength' => 45]) ?>

    <?= $form->field($model, 'nombre')->textInput(['maxlength' => 200]) ?>

    <?=
            $form->field($model, 'pcrc')
            ->widget(Select2::classname(), [
                //'data' => array_merge(["" => ""], $data),
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 3,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['reglanegocio/getarbolehoja']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();                                
                                if (id !== "") {
                                    $.ajax({
                                        url: "' . Url::to(['reglanegocio/padreclientedato']) . '",
                                        type:"POST",
                                        dataType: "json",
                                        data: {
                                            "pcrc" : id
                                        },
                                        success : function(objRes){
                                            $("#reglanegocio-cliente").prop("value",objRes.value);
                                        }
                                    });
                                    $.ajax("' . Url::to(['reglanegocio/getarbolehoja']) . '?id=" + id, {
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
    <?php /*=
            $form->field($model, 'rn')
            ->widget(Select2::classname(), [
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 1,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['reglanegocio']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression('function (element, callback) {
                            var id=$(element).val();
                            if (id !== "") {
                                $.ajax("' . Url::to(['reglanegocio']) . '?id=" + id, {
                                    dataType: "json",
                                    type: "post"
                                }).done(function(data) { callback(data.results[0]);});
                            }
                        }')
                ]
                    ]
    );*/
    ?>

    <?=
            $form->field($model, 'agente')
            ->widget(Select2::classname(), [
                //'data' => array_merge(["" => ""], $data),
                'language' => 'es',
                'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                'pluginOptions' => [
                    'allowClear' => true,
                    'minimumInputLength' => 4,
                    'ajax' => [
                        'url' => \yii\helpers\Url::to(['reportes/evaluadolist']),
                        'dataType' => 'json',
                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                    ],
                    'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['reportes/evaluadolist']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results[0]);});
                                }
                            }')
                ]
                    ]
    );
    ?>

    <?= $form->field($model, 'ani')->textInput(['maxlength' => 100]) ?>
    
    <?= $form->field($model, 'tipo_inbox')->dropDownList(['NORMAL' => 'NORMAL', 'ALEATORIO' => 'ALEATORIO']) ?>
    <?php //= $form->field($model, 'industria')->textInput(['maxlength' => 3]) ?>
    <?php //= $form->field($model, 'institucion')->textInput(['maxlength' => 3]) ?>
    <br>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Guardar'), ['class' => 'btn btn-success'])
            ?>     


                    
                <?= Html::button('Importar Encuestas', ['value' => url::to('importarencuesta'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                      'data-toggle' => 'tooltip',
                                      'title' => 'Importar Encuestas', 'style' => 'background-color: #337ab7']) 
                ?> 

                <?php
                  Modal::begin([
                        'header' => '<h4>Importar Archivo Excel </h4>',
                        'id' => 'modal1',
                        //'size' => 'modal-lg',
                      ]);

                  echo "<div id='modalContent1'></div>";
                                            
                  Modal::end(); 
                ?>  

      
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>

