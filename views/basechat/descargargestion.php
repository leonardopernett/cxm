<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;

$this->title = 'Gestión Satisfacción Chat';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Gestión Satisfacción Chat';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;    


?>
<div class="control-procesos-index" style="display: inline" id="IdCapaCero">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]) ?>
         <div class="form-group">
	   <div class="col-md-12">

             <label for="txtmedio" style="font-size: 15px;"> Tipo Cargue de Base de Medallia:</label>
                <select id="txtmedio" class ='form-control'  onchange="accion()">
                          <option value="" disabled selected>seleccione...</option>
                          <option value="3513">Tigo Colombia</option>
                          <option value="3272">Tigo Bolivia</option>
                </select>
          </div>
	  <br>
		<div class="col-md-12">
            	                   
                   <label style="font-size: 15px;"><em class="fas fa-at" style="font-size: 15px; color: #15aabf;"></em> Ingresar Correo corporativo </label>
                    <input type="text" style="display: none" class="form-control" id="id_pcrc">   
                    <input type="email" id="id_destino" name="remitentes" class="form-control" placeholder="Destinatario" multiple required>
                </div>
        </div>
        <br>
        <div class="form-group"> 
                <div class="col-xs-9">
                    <div onclick="enviodatos();" class="btn btn-danger" method='post' id="botones1" style="text-align: left;">
                        Enviar Datos
                    </div>                                    
                </div>
         </div>
    <?php ActiveForm::end(); ?>
</div>
<div class="CapaUno" style="display: none;" id="IdCapaUno">
    <p><h3>Procesando información a enviar...</h3></p>
</div>

<script type="text/javascript">
    
    function enviodatos(){
        var varDestino = document.getElementById("id_destino").value;
        var varpcrc = document.getElementById("id_pcrc").value;

        var varIdCapaCero = document.getElementById("IdCapaCero");
        var varIdCapaUno = document.getElementById("IdCapaUno");

        varIdCapaCero.style.display = 'none';
        varIdCapaUno.style.display = 'inline';

        var varWord1 = "allus";
        var varWord2 = "multienlace";
        var varWord3 = "grupokonecta";

        var nvarWord1 = varDestino.indexOf(varWord1);
        var nvarWord2 = varDestino.indexOf(varWord2);
        var nvarWord3 = varDestino.indexOf(varWord3);

        /*if (nvarWord3 <= 0) {
            event.preventDefault();
                swal.fire("!!! Advertencia !!!","Error con el correo, por favor ingrese correo corporativo.","warning");
                varIdCapaCero.style.display = 'inline';
                varDestino.value = "";
                varIdCapaUno.style.display = 'none';
            return; 
        }*/

        if (varDestino == null || varDestino == "") {
            event.preventDefault();
            swal.fire("��� Advertencia !!!","Debe de ingresar un correo para enviar los datos..","warning");
            return;           
        }
        
        if (varpcrc == 3513){
           //alert(varpcrc);
            $.ajax({
                method: "get",
                url: "exportcol",
                data : {
                    var_Destino : varDestino,                    
                },
                success : function(response){ 
                    var numRta =  JSON.parse(response);                
                    console.log(numRta);
                    if (numRta != 0) {
                        // $("#modal2").modal("hide");
                        location.reload();
                    }                    
                }
            });  
        }

        if (varpcrc == 3272){
	//alert(varDestino);

            $.ajax({
                method: "get",
             url: "exportbol",
                data : {
                    var_Destino : varDestino,                    
                },
                success : function(response){ 
                    var numRta =  JSON.parse(response);                
                    console.log(numRta);
                    if (numRta != 0) {
                        // $("#modal2").modal("hide");
                        location.reload();
                    }                    
                }
            });  
        }
         
    };

    function accion(){
        var varIdpcrc = document.getElementById("txtmedio").value;
        document.getElementById("id_pcrc").value = varIdpcrc;        
        
    };
        
</script>