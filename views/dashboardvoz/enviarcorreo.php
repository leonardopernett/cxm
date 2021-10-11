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


$this->title = 'DashBoard -- Voz del Cliente --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Escuchar + (Programa VOC - Konecta)';


    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

    $txtvarPcrc = $varPcrc;

?>
<div class="control-procesos-index" style="display: inline" id="IdCapaCero">
    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
         <div class="form-group">
            <label class="control-label col-xs-3">Destinatario:</label>
                <div class="col-xs-9">
                    <input type="email" id="id_destino" name="remitentes" class="form-control" placeholder="Destinatario" multiple required>
                </div>
        </div>
        <br>
        <br>
        <div class="form-group"> 
                <div class="col-xs-15">
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
        var varPcrc = "<?php echo $txtvarPcrc; ?>";

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

        if (nvarWord3 <= 0) {
            event.preventDefault();
                swal.fire("!!! Advertencia !!!","Error con el correo, por favor ingrese correo corporativo.","warning");
                varIdCapaCero.style.display = 'inline';
                varDestino.value = "";
                varIdCapaUno.style.display = 'none';
            return; 
        }

        if (varDestino == null || varDestino == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de ingresar un correo para enviar los datos..","warning");
            return;           
        }else{
            $.ajax({
                method: "post",
                url: "exportar",
                data : {
                    var_Destino : varDestino,
                    var_Pcrc : varPcrc,
                },
                success : function(response){ 
                    var numRta =  JSON.parse(response);                
                    console.log(numRta);
                    if (numRta != 0) {
                        window.location.href = "../dashboardvoz/detallevoz?varCodificacion="+varPcrc;
                    }                    
                }
            });  
        }
         
    };
        
</script>