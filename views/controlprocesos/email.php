<?php

use yii\helpers\Html;
/*use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use \app\models\ControlProcesos;

$this->title = 'Enviar la Valoracion';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $txtEvaludos = $variddelevaluado;

?>
<script type="text/javascript">

    function enviodatos(){
        var bdestino = document.getElementById("id_destino").value;
        var bId = "<?php echo $txtEvaludos; ?>";
        var bicontrol = "<?php echo $vartxtId; ?>";
console.log(bicontrol);

            $.ajax({
                method: "post",
        url: "enviocorreo",
                data : {
                    var_bdestino : bdestino,
                    var_bId : bId,
                    var_bicontrol : bicontrol,
                },
                success : function(response){ 
                    var numRta =  JSON.parse(response);                
                    console.log(numRta);

    if(numRta != 1) {
        alert("Problemas al realizar el envio de la valoracion");
        return;
    }else{
        location.href ="../controlprocesos/index";

    }
                }
            });    


    };
</script>

<div class="control-procesos-index">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?> 
        <div class="form-group">
            <label class="control-label col-xs-3">Destinatario:</label>
                <div class="col-xs-9">
                    <input type="email" id="id_destino" name="remitentes" class="form-control" placeholder="Destinatario" multiple required>
                </div>
        </div>
        <div class="form-group">
                <div class="col-xs-9">
                    <input type="text" id="identificacion" name="identificaciones" class="form-control invisible" placeholder="identificacion" multiple required>
                </div>
        </div>
        <div class="form-group">
                <div class="col-xs-15">
                    <div onclick="enviodatos();" class="btn btn-primary" method='post' id="botones1">
                        Envio de Informacion
                    </div>                                    
                </div>
         </div>

    <?php ActiveForm::end(); ?>
</div>
