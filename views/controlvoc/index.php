<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

$this->title = 'Instrumento Escucha Focalizada - VOC -';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id; 
    $valor = null;

?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Inst.-Escucha-Focalizada.png');
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
      </div>
    </div>
  </div>
</header>
<br><br>

    <?= Html::encode($this->title) ?>

<br>
<div class="formularios-form" style="display: inline" id="idBloques0">

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
      <div class="col-md-offset-2 col-sm-8">
        <?=
                $form->field($model, 'arbol_id')->label(Yii::t('app','Programa o PCRC'))
                    ->widget(Select2::classname(), [                
                        'language' => 'es',
                        'options' => ['id'=>"pcrcid",'placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'initialize' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                            }')
                        ]
                    ]
                    );
            ?>
      </div>
    </div>
    <br>

    <div class="row" style="text-align: center;">      
      <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
			'onclick' => 'varVerificar();',
                        'title' => 'Varificar']) 
                ?>    
    </div>

  <?php ActiveForm::end(); ?>
</div>
<br>
<div class="panel panel-default">
  <div class="panel-heading">Importante...</div>
  <div class="panel-body">Es necesario seleccionar el Programa/PCRC VOC para continuar con el proceso.</div>
</div>

<script type="text/javascript">
    function varVerificar(){
        var varServicio = document.getElementById("pcrcid").value;

        if (varServicio == "" || varServicio == null) {
            event.preventDefault();
                swal.fire("!!! Advertencia !!!","No hay Programa/Servicio VOC a buscar.","warning");
            return;   
        }
    };
</script>

