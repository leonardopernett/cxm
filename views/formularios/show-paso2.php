<?php

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
?>


<?php $this->title = Yii::t('app', 'Seleccionar Evaluado'); ?>
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
<!-- Full Page Image Header with Vertically Centered Content -->
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


    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'action' => \yii\helpers\Url::to(['guardarpaso2'])]); ?>


    <?php echo Yii::t('app', 'Arbol ID'); ?>
            <?php echo $nmArbol->dsname_full; ?>
        

    <?php echo Yii::t('app', 'Dimension'); ?>
            <?php echo $nmDimension->name; ?>
        
    
    <?=
        $form->field($modelE, 'evaluado_id')
        ->widget(Select2::classname(), [
            //'data' => array_merge(["" => ""], $data),
            'language' => 'es',
            'options' => ['placeholder' => Yii::t('app', 'Select ...')],
            'pluginOptions' => [
                'allowClear' => false,
                'minimumInputLength' => 4,
                'ajax' => [
                    'url' => \yii\helpers\Url::to(['evaluadosbyarbol', "arbol_id" => $arbol_id]),
                    'dataType' => 'json',
                    'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                    'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                ],
            
            ]
                ]
        );
    ?>
    
    
            <?= Html::radioList('tipo_interaccion', 1, 
                ['Interacción Automática', 'Interacción Manual'], 
                ['separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;']) ?>
      
    <?= Html::input("hidden", "arbol_id", $arbol_id); ?>
    <?= Html::input("hidden", "dimension_id", $dimension_id); ?>
    <?= Html::input("hidden", "nmArbol", $nmArbol->dsname_full); ?>
    <?= Html::input("hidden", "nmDimension", $nmDimension->name); ?>
    <?= Html::input("hidden", "formulario_id", $formulario_id); ?>
    
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-success'])
            ?>            
        

    <?php ActiveForm::end(); ?>
