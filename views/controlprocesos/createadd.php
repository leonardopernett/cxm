<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\bootstrap\modal;


$template = '<label for="pcrc" class="control-label col-sm-3">{label}</label><div class="col-sm-6">'
            . ' {input}{error}{hint}</div>';

$this->title = 'Agregar Parametros';
$this->params['breadcrumbs'][] = ['label' => 'Equipo de Trabajo', 'url' => ['index']];

$valor = null;
?>

<?php 
    $this->params['breadcrumbs'][] = $this->title; 
?>

<script>
function valida(e){
    tecla = (document.all) ? e.keyCode : e.which;

    //Tecla de retroceso para borrar, siempre la permite
    if (tecla==8){
        return true;
    }
        
    // Patron de entrada, en este caso solo acepta numeros
    patron =/[0-9]/;
    tecla_final = String.fromCharCode(tecla);
    return patron.test(tecla_final);
}
</script>

<script type="text/javascript">
    
    var ididCantVal = document.getElementById("idCantVal").value;
    var Cant_Val = document.getElementById("ididCantVal");
    Cant_Val.value = ididCantVal;
    console.log(ididCantVal);

    var ididDimensions = document.getElementById("idDimensions").value;
    var Dimensiones = document.getElementById("ididDimensions");
    Dimensiones.value = ididDimensions;
    console.log(ididDimensions); 

</script>

<div class="formularios-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model, 'arbol_id')->textInput(['maxlength' => 200, 'id'=>"<?php echo $id_arbol ?>", 'class'=>'hidden']) ?> 

    <?= $form->field($model, 'cant_valor')->textInput(['maxlength' => 200, 'id'=>'ididCantVal', 'class'=>'hidden']) ?> 

    <?= $form->field($model, 'dimensions')->textInput(['maxlength' => 200, 'id'=>'ididDimensions', 'class'=>'hidden']) ?>

    <?= $form->field($model, 'evaluados_id')->textInput(['maxlength' => 200, 'id'=>"<?php echo $id_valorado ?>", 'class'=>"hidden", 'label'=>""]) ?>

    <br>

    <div class="form-group">
        <?= HTML::submitButton($model->isNewRecord ? 'Agregar' : 'controlprocesos/createadd', ['class'=>$model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        
    </div>

    <?php ActiveForm::end(); ?>
    
</div>