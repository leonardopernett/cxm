<?php
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\bootstrap\Modal;


    $valor = null;
    $StrArbol = 0;
    $NumArbol = null;
      
    $fechaActual = date("Y-m-d");
?>
<style type="text/css">
    .form-horizontal .control-label {
        /* padding-top: 7px; */
        /* margin-bottom: 0; */
        /* text-align: right; */
    }
</style>
<div class="CapaUno" style="display: inline;">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
        <div class="row">
            <div class="col-md-12">
                <label style="font-size: 15px;">Seleccionar Pcrc: </label>
                        <?=
                            $form->field($model, 'arbol_id')
                                ->widget(Select2::classname(), [                
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
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

                                            if (id !== "") {
                                                $.ajax("' . $valor . '" + id, {
                                                    dataType: "json",
                                                    type: "post"
                                                }).done(function(data) { callback(data.results[0]);});
                                            }
                                        }')
                                    ]
                                ]
                                )->label('')
                        ?>

            </div>
            <div class="col-md-12">
                <label style="font-size: 15px;">Seleccionar Dimension: </label>
                <?php $var = ['AGENTES' => 'AGENTES', 'ALTO VALOR' => 'ALTO VALOR', 'CALIDAD DEL ENTRENAMIENTO' => 'CALIDAD DEL ENTRENAMIENTO', 'HEROES POR EL CLIENTE' => 'HEROES POR EL CLIENTE', 'OJT' => 'OJT', 'OTROS' => 'OTROS', 'PRECISIÓN Y APRENDIZAJE' => 'PRECISIÓN Y APRENDIZAJE', 'PROCESO' => 'PROCESO', 'PROTECCIÓN DE LA EXPERIENCIA' => 'PROTECCIÓN DE LA EXPERIENCIA', 'PRUEBAS' => 'PRUEBAS']; ?>
                <?= $form->field($model, 'dimensions')->dropDownList($var, ['prompt' => 'Seleccione...', 'id'=>"id_argumentos", 'onclick'=>'dimensiones2();'])->label('') ?> 
            </div>
            <div class="col-md-12">
                <label style="font-size: 15px;">Ingresar Cantidad: </label>
                <?= $form->field($model, 'cant_valor')->textInput(['maxlength' => 5, 'type' => 'number', 'onkeypress' => 'return valida(event)', 'value' => $StrArbol])->label('') ?>
                <?= $form->field($model, 'evaluados_id')->textInput(['maxlength' => 200, 'id'=>"id_valorado", 'class'=>"hidden", 'value'=>$txtusua_id, 'label'=>""]) ?>

                <?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 200, 'value' => $fechaActual, 'class'=>"hidden", 'label'=>""]) ?>

                <?= $form->field($model, 'anulado')->textInput(['maxlength' => 1, 'value' => 0, 'class'=>"hidden", 'label'=>""]) ?>
            </div>
            <div class="col-md-12">
                <?= HTML::submitButton($model->isNewRecord ? 'Guardar dimension' : 'controlprocesos/createparameters', ['class'=>$model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'BtnSubmitValorar']) ?>
                <div onclick="calculate();" class="btn btn-primary" style="display:none;" method='post' id="botones1" >
                    Calcular Valoraciones
                </div>   
            </div>
        </div>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
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
    };

    document.getElementById("BtnSubmitValorar").addEventListener("click", function(event){ 
        var arbolTxt = document.getElementById("select2-chosen-2").innerText;
        var argumentos = document.getElementById("id_argumentos").value;
        var textoCantidad2 = document.getElementById("controlparams-cant_valor").value;
                
            if(arbolTxt == 'Seleccione ...' || arbolTxt == undefined || arbolTxt == null){
                event.preventDefault();
                alert("Seleccione un programa/PCRC");
                return;
            }           

            if (argumentos == "") {
                event.preventDefault();
                alert("Debe seleccionar una dimension");
                return;
            }   

        if(textoCantidad2 == "0" || textoCantidad2 == "")
        {
                event.preventDefault();
                alert("La cantidad no debe ser 0 o vacio");
                return;
        }
    });

    function dimensiones2(){
        var selectDimension = document.getElementById("id_argumentos").value;
        var textoCantidad = document.getElementById("controlparams-cant_valor");
        var botonCalcular = document.getElementById("botones1");

        if (selectDimension == "PROCESO") 
        {
            document.getElementById("controlparams-cant_valor").readOnly = true;
            botonCalcular.style.display = 'inline';
        }
        else
        {
            if (selectDimension == "") 
            {
        document.getElementById("controlparams-cant_valor").readOnly = true;
                botonCalcular.style.display = 'none';
            }
            else
            {
        document.getElementById("controlparams-cant_valor").readOnly = false;
                botonCalcular.style.display = 'none';
            }
        }     
    };

    function calculate(){

        var arbolTxt = document.getElementById("select2-chosen-1").innerText; 
        var arbolTxt2 = document.getElementById("select2-chosen-1").innerText;
        var arregloarbol = arbolTxt2.replace(/>/gi," > ");
        console.log(arregloarbol);

        arbolTxt = arbolTxt.replace(/[^\d]/g,'');
        console.log(arbolTxt);

        if(arbolTxt == 'Seleccione ...' || arbolTxt == undefined || arbolTxt == null){
            
            arbol_id : 0;
        }
        else{
            $.ajax({
                method: "post",
		url: "prueba",
                data : {
                    arbol_id : arbolTxt,
                    pcrc_text : arregloarbol,
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    document.getElementById("controlparams-cant_valor").value = numRta;            
                    console.log(numRta);
                }
            });
        }
        
    };
</script>
