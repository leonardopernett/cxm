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
    background-image: url('../../images/Valorar-Interaccion.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
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

    <?= Yii::t('app', 'Realizar monitoreo') ?>

<div class="formularios-form">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'action' => \yii\helpers\Url::to(['guardarpaso2'])]); ?>
<div class="col-md-offset-2 col-sm-8 panel panel-default">
  <div class="panel-body" style="text-align: center;">
    <p>Recuerda que para Valorar las dimensiones de OJT y Calidad del Entrenamiento, lo debes hacer solo con el formulario </p><p><strong>Indice de Calidad Entrenamiento Inicial</strong></p>
  </div>
</div>

    <div class="form-group">
        <label class="control-label col-sm-3"><?php echo Yii::t('app', 'Arbol ID'); ?></label>
        <div class="col-sm-6">
            <?php echo $nmArbol->dsname_full; ?>
        </div>          
    </div> 

    <div class="form-group">
        <label class="control-label col-sm-3"><?php echo Yii::t('app', 'Dimension'); ?></label>
        <div class="col-sm-6">
            <?php echo $nmDimension->name; ?>
        </div>          
    </div>
    
     <div class="form-group">
        <label class="control-label col-sm-3"><?php echo Yii::t('app', 'Evaluado'); ?></label>
        <div class="col-sm-6">
            <?php echo $varNombreAsesor; ?>
        </div>          
    </div>
   
    
    <?= $form->field($modelE, 'evaluado_id')->textInput(['id'=>'evaluado_id', 'class'=>'hidden','value'=>$evaluado_id])->label(''); ?>     
    
    <?= Html::input("hidden", "arbol_id", $arbol_id); ?>
    <?= Html::input("hidden", "dimension_id", $dimension_id); ?>
    <?= Html::input("hidden", "nmArbol", $nmArbol->dsname_full); ?>
    <?= Html::input("hidden", "nmDimension", $nmDimension->name); ?>
    <?= Html::input("hidden", "formulario_id", $formulario_id); ?>
    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-success'])
            ?>            
        </div>        
    </div>

    <?php ActiveForm::end(); ?>
</div>

