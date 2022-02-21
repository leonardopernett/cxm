<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use \app\models\ControlProcesos;


$this->title = 'Agregar Dimensiones';
$this->params['breadcrumbs'][] = ['label' => 'Equipo de Trabajo', 'url' => ['index']];

$valor = null;
$StrArbol = 0;
$NumArbol = null;
$txtIdusua = $varIdusua;

$fechaActual = Yii::$app->db->createCommand("select distinct fechacreacion from tbl_control_procesos where anulado = 0 and id = $IDvar and evaluados_id = $varIdusua")->queryScalar();

?>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Equipo-de-Trabajo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
    ]
    ]); ?>
<div class="capaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fas fa-plus-square" style="font-size: 20px; color: #2CA5FF;"></em> Agregar Dimensionamiento: </label>
                
                <div class="row">
                    <div class="col-md-6">
                        <label  style="font-size: 17px;"><em class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></em> Seleccion de PCRC: </label><br>
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
                    <div class="col-md-6">    
                        <label  style="font-size: 17px;"><em class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></em> Seleccion de Dimension: </label><br>                
                        <?php $var = ['AGENTES' => 'AGENTES', 'ALTO VALOR' => 'ALTO VALOR', 'CALIDAD DEL ENTRENAMIENTO' => 'CALIDAD DEL ENTRENAMIENTO', 'HEROES POR EL CLIENTE' => 'HEROES POR EL CLIENTE', 'OJT' => 'OJT', 'OTROS' => 'OTROS', 'PRECISION Y APRENDIZAJE' => 'PRECISION Y APRENDIZAJE', 'PROCESO' => 'PROCESO', 'PROTECCION DE LA EXPERIENCIA' => 'PROTECCION DE LA EXPERIENCIA', 'PRUEBAS' => 'PRUEBAS']; ?>
                        <?= $form->field($model, 'dimensions')->dropDownList($var, ['prompt' => 'Seleccione...', 'id'=>"id_argumentos", 'onclick'=>'dimensiones2();'])->label('') ?>  
                    </div>
                    <div class="col-md-6">
                        <label  style="font-size: 17px;"><em class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></em> Cantidad de valoracion: </label><br>
                        <?= $form->field($model, 'cant_valor')->textInput(['maxlength' => 200, 'type' => 'number',  'onkeypress' => 'return valida(event)', 'value' => $StrArbol])->label('') ?> 
                    
                        <?= $form->field($model, 'evaluados_id')->textInput(['maxlength' => 200, 'value'=>$txtIdusua, 'class'=>"hidden", 'label'=>""]) ?>

                        <?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 200, 'value' => $fechaActual, 'class'=>"hidden", 'label'=>""]) ?>

                        <?= $form->field($model, 'anulado')->textInput(['maxlength' => 1, 'value' => 0, 'class'=>"hidden", 'label'=>""]) ?>
 
                        <?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 200, 'type' => 'number',  'class' => 'hidden', 'value' => $fechaActual])->label('') ?> 
                    </div>
                </div>
                
            </div>
        </div>
    </div>
</div>
<hr>    
<div class="capaDos" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Guardar: </label> 
                            <?= HTML::submitButton($model->isNewRecord ? 'Guardar' : 'controlprocesos/createparameters2', ['class'=>$model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'BtnSubmitValorar']) ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Calcular las valoraciones: </label> 
                            <div onclick="calculate();" class="btn btn-primary" style="display:none;" method='post' id="botones1" >
                                Calcular Valoraciones
                            </div> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label>
                            <?= Html::a('Regresar',  ['update2', 'id' => $IDvar, 'evaluados_id' => $varIdusua], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                            ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>
<hr>


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

    function calculate(){
        var arbolTxt = document.getElementById("s2id_controlparams-arbol_id").innerText; 
        var arbolTxt2 = document.getElementById("s2id_controlparams-arbol_id").innerText;
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

    var id_valorado = "<?php echo $txtIdusua; ?>";
    var campo_valorado = document.getElementById("id_valorado");
    //campo_valorado.value = id_valorado;
    console.log(id_valorado); 


    document.getElementById("BtnSubmitValorar").addEventListener("click", function(event){            


        var arbolTxt = document.getElementById("select2-chosen-1").innerText;
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
</script>