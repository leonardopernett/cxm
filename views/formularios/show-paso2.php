<?php

use yii\helpers\Html;

use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;

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

$varTexto = " La búsqueda de las llamadas a Genesys en CXM, actualmente esta sujeta por defecto a un rango de fecha de los últimos 7 dias a partir de la fecha actual.";

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
            height: 250px;
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

    .card3 {
            height: 100px;
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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
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
            <?php
                if ($varConteoVerificar == 0) {
            ?>
                <div class="card1 mb">
            <?php
                }else{ 
            ?>
                <div class="card2 mb">
            <?php
                }
            ?>

                <label style="font-size: 15px;"><em class="fas fa-address-card" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Programa/PCRC...') ?></label>
                <label style="font-size: 15px; font-weight: 100;"><?= Yii::t('app', $nmArbol->dsname_full) ?></label>

                <br>

                <label style="font-size: 15px;"><em class="fas fa-cog" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Dimension...') ?></label>
                <label style="font-size: 15px;  font-weight: 100;"><?= Yii::t('app', $nmDimension->name) ?></label>        

                <?php
                    if ($varConteoVerificar != 0) {

                        $varNombreCola = (new \yii\db\Query())
                                ->select(['cola_genesys'])
                                ->from(['tbl_genesys_formularios'])
                                ->where(['=','tbl_genesys_formularios.anulado',0])
                                ->andwhere(['=','tbl_genesys_formularios.arbol_id',$arbol_id])
                                ->scalar();
                ?>

                    <br>

                    <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Relación con Genesys...') ?></label>
                    <label style="font-size: 15px;  font-weight: 100;"><?= Yii::t('app', $varNombreCola) ?></label>  

                <?php
                    }
                ?>                     
                
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">

                <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Seleccionar Valorado...') ?></label>

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

                <?php 
                    if ($varConteoVerificar != 0) {
                ?>
                    <br>

                    <div class="row">

                        <div class="col-md-8">
                            <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Seleccionar Fecha...') ?></label>

                            <?=
                                $form->field($modelE, 'fechacreacion', [
                                    'labelOptions' => ['class' => 'col-md-12'],
                                    'template' => 
                                     '<div class="col-md-12"><div class="input-group">'
                                    . '<span class="input-group-addon" id="basic-addon1">'
                                    . '<i class="glyphicon glyphicon-calendar"></i>'
                                    . '</span>{input}</div>{error}{hint}</div>',
                                    'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                    'options' => ['class' => 'drp-container form-group']
                                ])->label('')->widget(DateRangePicker::classname(), [
                                    'useWithAddon' => true,
                                    'convertFormat' => true,
                                    'presetDropdown' => true,
                                    'readonly' => 'readonly',
                                    'pluginOptions' => [
                                        'timePicker' => false,
                                        'format' => 'Y-m-d',
                                        'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                        'endDate' => date("Y-m-d"),
                                        'opens' => 'right',
                                ]]);
                            ?>
                        </div>

                        <div class="col-md-4">
                            

                            <div onclick="varBuscarLlamadas();" id="idbtnBuscar" class="btn btn-success"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000;  margin-block: auto; display: inline;" method='post'  >
                                <?= Yii::t('app', '[ Buscar Llamadas ]') ?> 
                            </div>

                            <div id="idbtnBuscando" style="display: none;">
                                <?= Yii::t('app', 'Buscando llamadas...') ?> 
                            </div>
                        </div>
                    </div>

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
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Lista de llamadas - Genesys - Interacción Manual') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-6">
            
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Información Llamadas...') ?></label>
                        
                <label style="font-size: 15px;"><?= Yii::t('app', $varTexto) ?></label>
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

            </div>
        </div>
    </div>

</div>

<div class="capaTercera" id="idcapaTercera" style="display: none;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Llamada - Genesys - Interacción Automática') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-6">
            
            <div class="card3 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Información Llamadas...') ?></label>
                        
                <label style="font-size: 15px;"><?= Yii::t('app', $varTexto) ?></label>
            </div>

        </div>

        <div class="col-md-6">
            <div class="card3 mb">
                
                <label style="font-size: 15px;"><em class="fas fa-link" style="font-size: 20px; color: #ffc034;"></em><?= Yii::t('app', ' Link de llamada...') ?></label>

                <input type="text" rows="2"  id="idcapaulrconnid" readonly="readonly" >                                       

            </div>
        </div>
    </div>

</div>

<div id="idcapaLlamadaPrincipal" class="capaLlamadaPrincipal" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($modelE, 'identificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'requestergeneral', 'class'=>'hidden']) ?> 
        </div>
    </div>
</div>



<?php ActiveForm::end(); ?>

<br>

<script type="text/javascript">
    function varCambios(){
        var radios = document.getElementsByName('tipo_interaccion');
        var varidbtnBuscar = document.getElementById("idbtnBuscar");
        var varArbol = "<?php echo $arbol_id; ?>";

        var varAsesor = document.getElementById("varIdAsesor").value;
        var varVerificaciones = "<?php echo $varConteoVerificar; ?>";
        var varidbtnBuscando = document.getElementById("idbtnBuscando");

        var varFechasJS = document.getElementById("evaluados-fechacreacion").value;
        var varArrayFechas = varFechasJS.split(" ",10);
        var varFechaInicios = new Date(varArrayFechas[0]);
        var varFechaFines = new Date(varArrayFechas[2]);

        var varidcapaTercera = document.getElementById("idcapaTercera");

        var varDiferencia = varFechaFines.getTime() - varFechaInicios.getTime();
        var varDias = varDiferencia / 1000 / 60 / 60 / 24;

        if (varVerificaciones  != 0) {

            if (varFechasJS != "") {

                if (varDias <= 7) {

                    if (varAsesor == "") {
                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","Se debe de seleccionar un asesor para buscar las llamadas.","warning");
                        return;
                    }else{

                        for (var radio of radios) {

                            if (radio.checked) {
                                var varchekeo = radio.value;

                                if (varchekeo == 0) {
                                    varidbtnBuscar.style.display = 'none';
                                    varidcapaTercera.style.display = 'inline';

                                    $.ajax({
                                        method: "get",
                                        url: "listarllamadasgenesysauto",
                                        data : {
                                            txtvarAsesorAuto : varAsesor,     
                                            txtvarFechaIniciosAuto : varArrayFechas[0],
                                            txtvarFechaFinesAuto : varArrayFechas[2],  
                                            txtvarArbolAuto : varArbol,        
                                        },
                                        success : function(response){
                                            var Rta =  $.parseJSON(response);

                                            if (Rta.length != 0) {

                                                if (Rta != "Interacción no encontrada. Vuelva a realizar la búsqueda.") {
                                                    document.getElementById("idcapaulrconnid").value = 'https://apps.mypurecloud.com/directory/#/engage/admin/interactions/'+Rta;
                                                    document.getElementById('requestergeneral').value = Rta;
                                                }else{
                                                    document.getElementById("idcapaulrconnid").value = Rta;
                                                }
                                                
                                            }else{
                                                event.preventDefault();
                                                swal.fire("!!! Advertencia !!!","No se encontraron interacciones para el asesor en cuestion dentro de los ultimos 7 dias. Vuelva a seleccionar Interacción automática.","warning");
                                                return;   
                                            }
                                        }
                                    });

                                }else{
                                    varidbtnBuscar.style.display = 'inline';
                                    varidcapaTercera.style.display = 'none';
                                }

                            }

                        }

                    }

                }else{
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","Rango de días seleccionado supera los 7 dias. Por favor ingresar nuevo rango de fecha menor a 7 días.","warning");
                    return;
                }

            }else{
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe seleccionar un rango de fecha para realizar la busqueda de llamadas en Genesys.","warning");
                return;
            }

        }
        
    };

    function varBuscarLlamadas(){
        var varAsesor = document.getElementById("varIdAsesor").value;
        var varArbol = "<?php echo $arbol_id; ?>";

        var varSecundaria = document.getElementById("idcapaSecundaria");
        var varidbtnBuscar = document.getElementById("idbtnBuscar");
        var varidbtnBuscando = document.getElementById("idbtnBuscando");

        var varFechasJS = document.getElementById("evaluados-fechacreacion").value;
        var varArrayFechas = varFechasJS.split(" ",10);
        var varFechaInicios = new Date(varArrayFechas[0]);
        var varFechaFines = new Date(varArrayFechas[2]);

        var varDiferencia = varFechaFines.getTime() - varFechaInicios.getTime();
        var varDias = varDiferencia / 1000 / 60 / 60 / 24;


        if (varFechasJS != "") {

            if (varDias <= 7) {

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
                            txtvarFechaInicios : varArrayFechas[0],
                            txtvarFechaFines : varArrayFechas[2],  
                            txtvarArbol : varArbol,        
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

            }else{
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Rango de días seleccionado supera los 7 dias. Por favor ingresar nuevo rango de fecha menor a 7 días.","warning");
                return;
            }

        }else{
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar un rango de fecha para realizar la busqueda de llamadas en Genesys.","warning");
            return;
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
        var varRequestGeneral = document.getElementById('idcapaulrconnid').value;

        document.getElementById('requestergeneral').value = varRequestGeneral;
    };
</script>