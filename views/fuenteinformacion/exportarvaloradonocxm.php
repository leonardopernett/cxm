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

?>
<div id="IdCapaCero" style="display: inline">   
    <div class="row">
    	<div class="col-md-12">
    		<label style="font-size: 15px;"><i class="fas fa-at" style="font-size: 15px; color: #15aabf;"></i> Ingresar Correo corporativo... </label>  
            <input type="email" id="id_destino" name="remitentes" class="form-control" placeholder="Destinatario" multiple required>
    	</div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div onclick="enviodatos();" class="btn btn-danger" method='post' id="botones1">
                Enviar Datos
            </div>
        </div>
    </div>
</div>
<div class="CapaUno" style="display: none;" id="IdCapaUno">
    <p><h3>Procesando informacion a enviar...</h3></p>
</div>

<script type="text/javascript">
    function enviodatos(){
        var varDestino = document.getElementById("id_destino").value
        var varIdCapaCero = document.getElementById("IdCapaCero");
        var varIdCapaUno = document.getElementById("IdCapaUno");

        if (varDestino == "") {
            event.preventDefault();
            swal.fire("Â¡Â¡Â¡ Advertencia !!!","Debe de ingresar un correo corporativo para enviar los datos","warning");
            return;  
        }else{
            varIdCapaCero.style.display = 'none';
            varIdCapaUno.style.display = 'inline';

            $.ajax({
                method: "get",
                url: "exportarlistanoactualcxm2",
                data : {
                    var_Destino : varDestino,
                },
                success : function(response){ 
                    var numRta =  JSON.parse(response);                
                    console.log(numRta);
                    if (numRta != 0) {
                        $("#modal1").modal("hide");
                    }                    
                }
            });
        }
    };
</script>
