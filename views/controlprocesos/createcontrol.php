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
$fechaActual = Date("Y-m-d");
?>

<?php 
    $this->params['breadcrumbs'][] = $this->title; 
?>
<script type="text/javascript">
    
    var id_valorado = document.getElementById("invisible_valorado").value;
    var campo_valorado = document.getElementById("id_valorado");
    campo_valorado.value = id_valorado;
    console.log(id_valorado);

    var idTipoCorte2 = document.getElementById("TipoCort2").value;
    var Tipos_cortes2 = document.getElementById("idTipoCorte2");
    Tipos_cortes2.value = idTipoCorte2;
    console.log(idTipoCorte2);

    var idCantValor = document.getElementById("CantValor").value;
    var Cant_Valores = document.getElementById("idCantValor");
    Cant_Valores.value = idCantValor;
    console.log(idCantValor);

    var idDedicValor = document.getElementById("DedicValor").value;
    var Dedic_Valor = document.getElementById("idDedicValor");
    Dedic_Valor.value = idDedicValor;
    console.log(idDedicValor);   

    var idTipoCorte = document.getElementById("TipoCort").value;
    var Tipos_cortes = document.getElementById("idTipoCorte");
    Tipos_cortes.value = idTipoCorte;
    console.log(idTipoCorte);

    if (idTipoCorte == null || idTipoCorte == "" || idDedicValor == null || idDedicValor == "") {
        document.getElementById("fomrularioId2").style.visibility = "visible";
        console.log("Hola");
    }else{
        document.getElementById("fomrularioId1").style.visibility = "visible";
        console.log("Mundo");
    }   
</script>

<div class="formularios-form" id="fomrularioId1" style="visibility: hidden;">
    <div>
        <h4>¿Esta seguro que desea guardar los registros?</h4>
    </div>

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <?= $form->field($model2, 'evaluados_id')->textInput(['maxlength' => 200, 'id'=>"id_valorado", 'class'=>"hidden", 'label'=>""]) ?>

    <?= $form->field($model2, 'idtc')->textInput(['maxlength' => 200, 'id'=>"idTipoCorte2", 'class'=>"hidden", 'label'=>""]) ?>

    <?= $form->field($model2, 'tipo_corte')->textInput(['maxlength' => 200, 'id'=>"idTipoCorte", 'class'=>"hidden", 'label'=>""]) ?>

    <?= $form->field($model2, 'cant_valor')->textInput(['maxlength' => 200, 'id'=>"idCantValor", 'class'=>"hidden", 'label'=>""]) ?>

    <?= $form->field($model2, 'Dedic_valora')->textInput(['maxlength' => 200, 'id'=>"idDedicValor", 'class'=>"hidden", 'label'=>""]) ?>

    <?= $form->field($model2, 'responsable')->textInput(['maxlength' => 200, 'value' => $sessiones, 'class'=>"hidden", 'label'=>""]) ?>

    <?= $form->field($model2, 'fechacreacion')->textInput(['maxlength' => 200, 'value' => $fechaActual, 'class'=>"hidden", 'label'=>""]) ?>

    <?= $form->field($model2, 'anulado')->textInput(['maxlength' => 1, 'value' => 0, 'class'=>"hidden", 'label'=>""]) ?>  

    <br>

    <div class="form-group">
        <?= HTML::submitButton($model2->isNewRecord ? 'Guardar' : 'controlprocesos/createcontrol', ['class'=>$model2->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        
    </div>

    <?php ActiveForm::end(); ?>
    
</div>

<div class="formularios-form" id="fomrularioId2" style="visibility: hidden">
    <div>
        <h4>No es posible guardar los datos, existen campos incompletos.</h4>
    </div>
</div>    
