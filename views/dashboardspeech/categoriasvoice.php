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

$this->title = 'DashBoard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'DashBoard Voz del Cliente';

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
    $varIndicador1 = null; 
    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

    $fechaI = new DateTime();
    $fechaI->modify('first day of this month');
    $fechaIni = $fechaI->format('Y-'.$MesAnterior.'-d');

    $fechaF = new DateTime();
    $fechaF->modify('last day of this month');
    $fechaFin = $fechaF->format('Y-'.$MesAnterior.'-d');

    $varIndicador = "Indicador";
    $varVariable = "Variable";

    $fechaComoEntero = strtotime($varFechaI);
    $fechaIniCat = date("Y", $fechaComoEntero).'-01-01'; 
    $fechaFinCat = date("Y", $fechaComoEntero).'-12-31';


    

?>
<div class="control-procesos-index" style="display: inline" id="IdCapaCero">
    <?php $form = ActiveForm::begin([
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]) ?>
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
    <p><h3>Procesando informacion a enviar...</h3></p>
</div>

<script type="text/javascript">
    function enviodatos(){
        var varDestino = document.getElementById("id_destino").value;
        var varArbol_idV = "<?php echo $varArbol_idV; ?>";
        var varParametros_idV = "<?php echo $varParametros_idV; ?>";
        var varCodparametrizar = "<?php echo $varCodparametrizar; ?>";
        var varIndicador = "<?php echo $varIndicador1; ?>"; 
        var varFechaIni = "<?php echo $varFechaI; ?>";
        var varFechaFin = "<?php echo $varFechaF; ?>";
        var varCodsPcrc = "<?php echo $varCodigPcrc; ?>";


        var varIdCapaCero = document.getElementById("IdCapaCero");
        var varIdCapaUno = document.getElementById("IdCapaUno");

        varIdCapaCero.style.display = 'none';
        varIdCapaUno.style.display = 'inline';

        varrevision = /@([\da-z\.-]+)\.([a-z\.]{2,6})$/;
        if (!varrevision.exec(varDestino)) {
            event.preventDefault();
                swal.fire("!!! Advertencia !!!","Error con el correo, por favor ingrese correo corporativo.","warning");
                varIdCapaCero.style.display = 'inline';
                varDestino.value = "";
                varIdCapaUno.style.display = 'none';
            return; 
        }

        if (varDestino == null || varDestino == "") {
            event.preventDefault();
            swal.fire("��� Advertencia !!!","Debe de ingresar un correo para enviar los datos..","warning");
            return;           
        }else{
            $.ajax({
                method: "post",
                url: "export2",
                data : {
                    var_Destino : varDestino,
                    varArbol_idV : varArbol_idV,
                    varParametros_idV : varParametros_idV,
                    varCodparametrizar : varCodparametrizar,
                    varIndicador : varIndicador,
                    var_FechaIni : varFechaIni,
                    var_FechaFin : varFechaFin,
                    var_CodsPcrc : varCodsPcrc,

                },
                success : function(response){ 
                    var numRta =  JSON.parse(response);                
                    console.log(numRta);
                    if (numRta != 0) {
                        location.reload();
                    }                    
                }
            });  
        }
         
    };
        
</script>