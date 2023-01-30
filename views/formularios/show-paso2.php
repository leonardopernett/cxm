<?php

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

$this->title = 'Valorar Interaccion - Selección de Asesor';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$varConteoVerificar = (new \yii\db\Query())
    ->select(['*'])
    ->from(['tbl_genesys_formularios'])
    ->where(['=','tbl_genesys_formularios.anulado',0])
    ->andwhere(['=','tbl_genesys_formularios.arbol_id',$arbol_id])
    ->count();

$varFechaPrincipal = date("Y-m-d");
$varFechaInicio = date('Y-m-d',strtotime($varFechaPrincipal."- 7 days"));
$varFechaFin = date('Y-m-d',strtotime($varFechaPrincipal."- 1 days"));

?>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<style>
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

    .card2 {
            height: 170px;
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


<?php $form = ActiveForm::begin(['layout' => 'horizontal', 'action' => \yii\helpers\Url::to(['guardarpaso2'])]); ?>

<div class="capaPrincipal" id="idcapaPrincipal" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Información') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-star" style="font-size: 25px; color: #ffc034;"></em><?= Yii::t('app', ' Recuerda que para Valorar las dimensiones de OJT y Calidad del Entrenamiento, lo debes hacer solo con el formulario -> Indice de Calidad Entrenamiento Inicial ') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Guia de Inspiración') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-6">
            <div class="card2 mb">

                <label style="font-size: 15px;"><em class="fas fa-address-card" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Programa/PCRC...') ?></label>
                <label style="font-size: 15px; font-weight: 100;"><?= Yii::t('app', $nmArbol->dsname_full) ?></label>

                <br>

                <label style="font-size: 15px;"><em class="fas fa-cog" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Dimension...') ?></label>
                <label style="font-size: 15px;  font-weight: 100;"><?= Yii::t('app', $nmDimension->name) ?></label>                             
                
            </div>
        </div>

        <div class="col-md-6">
            <div class="card2 mb">

                <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Seleccionar Valorado...') ?></label>


                <?php 
                    if ($varConteoVerificar != 0) {
                ?>
                    <div class="row">

                        <div class="col-md-8">
                            <?=
                                $form->field($modelE, 'evaluado_id')
                                        ->widget(Select2::classname(), [
                                            'language' => 'es',
                                            'options' => ['id'=>'varIdAsesor','placeholder' => Yii::t('app', 'Select ...')],
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
                                )->label('');
                            ?>
                        </div>

                        <div class="col-md-4">
                            <div onclick="varBuscarLlamadas();" id="idbtnBuscar" class="btn btn-success"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; margin: 50px; margin-block: auto; display: inline;" method='post'  >
                                <?= Yii::t('app', '[ Buscar Llamadas ]') ?> 
                            </div>

                            <div id="idbtnBuscando" style="display: none;">
                                <?= Yii::t('app', 'Buscando llamadas...') ?> 
                            </div>
                        </div>
                    </div>
                <?php
                    }else{
                ?>
                            <?=
                                $form->field($modelE, 'evaluado_id')
                                        ->widget(Select2::classname(), [
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
                                )->label('');
                            ?>         
                <?php
                    }
                ?>

                <?= 
                    Html::radioList('tipo_interaccion', 1, 
                    ['Interaccion Automatica', 'Interaccion Manual'], 
                    ['separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','name'=>'contact','id'=>'varidInteracciones','onclick'=>'varCambios();']) 
                ?>

                <?= Html::input("hidden", "arbol_id", $arbol_id); ?>
                <?= Html::input("hidden", "dimension_id", $dimension_id); ?>
                <?= Html::input("hidden", "nmArbol", $nmArbol->dsname_full); ?>
                <?= Html::input("hidden", "nmDimension", $nmDimension->name); ?>
                <?= Html::input("hidden", "formulario_id", $formulario_id); ?>               

                <?=
                    Html::submitButton(Yii::t('app', 'Buscar Formulario'), ['class' => 'btn btn-success'])
                ?>

            </div>
        </div>

    </div>

</div>

<hr>

<div class="capaSecundaria" id="idcapaSecundaria" style="display: none;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Lista de llamadas - Genesys') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-6">
            
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Información Llamadas...') ?></label>
                        
                <label style="font-size: 15px;"><?= Yii::t('app', ' La búsqueda de las llamadas a Genesys en CXM, actualmente esta sujeta por defecto a un rango de fecha de los últimos 7 dias a partir de la fecha actual.') ?></label>
            </div>

        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                
                <label style="font-size: 15px;"><em class="fas fa-phone" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Seleccionar una llamada...') ?></label>

                <?= $form->field($modelE,'identificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar...',
                                            'id' => 'requester',
                                            'multiple' => true,
                                            'style' => 'height: 200px;',
                                            'onclick' => 'varVarCambiar();',
                                        ]
                                    )->label('');
                ?>

                <?= $form->field($modelE, 'identificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'requestergeneral', 'class'=>'hidden']) ?>                        

            </div>
        </div>
    </div>

</div>

<?php ActiveForm::end(); ?>

<br>

<script type="text/javascript">
    function varCambios(){
        var radios = document.getElementsByName('tipo_interaccion');
        var varidbtnBuscar = document.getElementById("idbtnBuscar");

        var varAsesor = document.getElementById("varIdAsesor").value;
        var varFechaInicios = "<?php echo $varFechaInicio; ?>";
        var varFechaFines = "<?php echo $varFechaFin; ?>";

        var varVerificaciones = "<?php echo $varConteoVerificar; ?>";

        if (varVerificaciones  != 0) {

            if (varAsesor == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Se debe de seleccionar un asesor para buscar las llamadas.","warning");
                return;
            }else{
                for (var radio of radios)
                {
                    if (radio.checked) {                
                        var varchekeo = radio.value;

                        if (varchekeo == 0) {
                            varidbtnBuscar.style.display = 'none';

                            $.ajax({
                                method: "get",
                                url: "listarllamadasgenesysauto",
                                data : {
                                    txtvarAsesorAuto : varAsesor,     
                                    txtvarFechaIniciosAuto : varFechaInicios,
                                    txtvarFechaFinesAuto : varFechaFines,          
                                },
                                success : function(response){
                                    var Rta =  $.parseJSON(response);

                                    if (Rta.length != 0) {
                                        document.getElementById('requestergeneral').value = Rta;
                                    }
                                }
                            });

                        }else{
                            varidbtnBuscar.style.display = 'inline';
                        }
                    }
                }
            }

        }

        
    };

    function varBuscarLlamadas(){
        var varAsesor = document.getElementById("varIdAsesor").value;
        var varFechaInicios = "<?php echo $varFechaInicio; ?>";
        var varFechaFines = "<?php echo $varFechaFin; ?>";

        var varSecundaria = document.getElementById("idcapaSecundaria");
        var varidbtnBuscar = document.getElementById("idbtnBuscar");
        var varidbtnBuscando = document.getElementById("idbtnBuscando");

        if (varAsesor == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe de seleccionar un asesor para buscar las llamadas.","warning");
            return;
        }else{
            varidbtnBuscar.style.display = 'none';
            varidbtnBuscando.style.display = 'inline';


            $.ajax({
                method: "get",
                url: "listarllamadasgenesys",
                data : {
                    txtvarAsesor : varAsesor,     
                    txtvarFechaInicios : varFechaInicios,
                    txtvarFechaFines : varFechaFines,          
                },
                success : function(response){ 
                    var Rta =  $.parseJSON(response);

                    if (Rta.length != 0) {

                        varSecundaria.style.display = 'inline';

                        varidbtnBuscar.style.display = 'inline';
                        varidbtnBuscando.style.display = 'none';

                        document.getElementById("requester").innerHTML = "";
                        var node = document.createElement("OPTION");
                        node.setAttribute("value", "");
                              
                        var textnode = document.createTextNode("Seleccionar Llamadas...");
                        node.appendChild(textnode);
                        document.getElementById("requester").appendChild(node);
                              
                        for (var i = 0; i < Rta.length; i++) {
                            var node = document.createElement("OPTION");
                            node.setAttribute("value", Rta[i]);
                            var textnode = document.createTextNode("* Llamada con connid ==> "+Rta[i]);
                            node.appendChild(textnode);
                            document.getElementById("requester").appendChild(node);
                        }
                              
                        document.getElementById("requester").options[0].disabled = true;
                        var x=document.getElementById("requester");
                        x.disabled=false;

                    }else{
                        varidbtnBuscar.style.display = 'inline';
                        varidbtnBuscando.style.display = 'none';

                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","No se encontraron llamadas para el asesor en cuestion dentro de los ultimos 7 dias.","warning");
                        return;
                    }
                    
                }

            });
        }
    };

    var ultimoValorValido = null;
    $("#requester").on("change", function() {
        if ($("#requester option:checked").length > 1) {
            $("#requester").val(ultimoValorValido);
        } else {
            ultimoValorValido = $("#requester").val();
        }
    });

    function varVarCambiar(){
        var varRequestGeneral = document.getElementById('requester').value;

        document.getElementById('requestergeneral').value = varRequestGeneral;
    };
</script>