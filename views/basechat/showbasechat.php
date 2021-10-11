<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Gestión Satisfacción Chat';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones =Yii::$app->user->identity->id;  
    $varconteo = 0; 
    $varpcrc = $_GET["pcrc"];

?>
<style>
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
            font-family: "Nunito";
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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capaPP" id="capaP" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="capaUno" style="display: inline;">
        <div class="row">
            
            <div class="col-md-6">
                <div class="card1 mb">
                    <label><i class="fas fa-edit" style="font-size: 20px; color: #559FFF;"></i> Información general</label>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtticket" style="font-size: 14px;">Número del ticket...</label>
                            <input type="text" id="txtticket" name="datetimes" readonly="readonly" value="<?php echo $varticket; ?>" class="form-control" data-toggle="tooltip" title="Ticket">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="txtemail" style="font-size: 14px;">Fecha transacción...</label>
                            <input type="text" id="txttransa" name="datetimes" readonly="readonly" value="<?php echo $varencuesta; ?>" class="form-control" data-toggle="tooltip" title="Email Cliente">
                        </div>
                        <div class="col-md-4">
                            <label for="txtemail" style="font-size: 14px;">Fecha respuesta...</label>
                            <input type="text" id="txtrta" name="datetimes" readonly="readonly" value="<?php echo $varrta; ?>" class="form-control" data-toggle="tooltip" title="Email Cliente">
                        </div>
                        <div class="col-md-4">
                            <label for="txtemail" style="font-size: 14px;">Fecha creación...</label>
                            <input type="text" id="txtcreacion" name="datetimes" readonly="readonly" value="<?php echo $varcreacion; ?>" class="form-control" data-toggle="tooltip" title="Email Cliente">
                        </div>
                     </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtcliente" style="font-size: 14px;">Nombre del cliente...</label>
                            <input type="text" id="txtcliente" name="datetimes" readonly="readonly" value="<?php echo $varcliente; ?>" class="form-control" data-toggle="tooltip" title="Cliente">
                        </div>
                    </div>                    
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtagente" style="font-size: 14px;">Agente...</label>
                            <input type="text" id="txtagente" name="datetimes" readonly="readonly" value="<?php echo $varagente; ?>" class="form-control" data-toggle="tooltip" title="Agente">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="txttipologia" style="font-size: 14px;">Tipología...</label>
                            <input type="text" id="txttipologia" name="datetimes" readonly="readonly" value="<?php echo $vartipologia; ?>" class="form-control" data-toggle="tooltip" title="Tipología">
                        </div>
                        <div class="col-md-6">
                            <label for="txttipoporducto" style="font-size: 14px;">Tipo producto...</label>
                            <input type="text" id="txttipoproducto" name="datetimes" readonly="readonly" value="<?php echo $vartipo_producto; ?>" class="form-control" data-toggle="tooltip" title="Tipo producto">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtelsentir" style="font-size: 14px;">El sentir del cliente...</label>
                            <input type="text" id="txtelsentir" name="datetimes" readonly="readonly" value="<?php echo $varSentir; ?>" class="form-control" data-toggle="tooltip" title="Sentir del cliente">
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                <div class="card1 mb">
                    <label><i class="fas fa-bookmark" style="font-size: 20px; color: #559FFF;"></i> Preguntas generales</label>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtpregunta1" style="font-size: 14px;">¿Que tan probable es que recomiendas Tigo a tus familiares y amigos?</label>
                            <input type="text" id="txtpregunta1" name="datetimes" readonly="readonly" value="<?php echo $varPregunta1; ?>" class="form-control" data-toggle="tooltip" title="¿Que tan probable es que recomiendas Tigo a tus familiares y amigos?">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtpregunta2" style="font-size: 14px;">¿Que tan satisfecho estas con la atención recibida?</label>
                            <input type="text" id="txtpregunta2" name="datetimes" readonly="readonly" value="<?php echo $varPregunta2; ?>" class="form-control" data-toggle="tooltip" title="¿Que tan satisfecho estas con la atención recibida?">
                        </div>
                     </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtpregunta3" style="font-size: 14px;">¿Que tan fácil fue resolver tu consulta/solicitud?</label>
                            <input type="text" id="txtpregunta3" name="datetimes" readonly="readonly" value="<?php echo $varPregunta3; ?>" class="form-control" data-toggle="tooltip" title="¿Que tan fácil fue resolver tu consulta/solicitud?">
                        </div>
                    </div>                  
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtpregunta4" style="font-size: 14px;">¿Resolvimos el motivo de tu solicitud?</label>
                            <input type="text" id="txtpregunta4" name="datetimes" readonly="readonly" value="<?php echo $varPregunta4; ?>" class="form-control" data-toggle="tooltip" title="¿Resolvimos el motivo de tu solicitud?">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtpregunta5" style="font-size: 14px;">¿Qué tan satisfecho estas con el conocimiento que demostró el asesor para resolver tu consulta?</label>
                            <input type="text" id="txtpregunta5" name="datetimes" readonly="readonly" value="<?php echo $varPregunta5; ?>" class="form-control" data-toggle="tooltip" title="¿Qué tan satisfecho estas con el conocimiento que demostró el asesor para resolver tu consulta?">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="txtIDExtSp" style="font-size: 14px;">Fecha de calificación</label>
                                <input type="text" id="idtxtFechaHoraclasifi" readonly="readonly" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha & Hora" value="<?php echo date("Y-m-d"); ?>">
                        </div>
                    
                        <div class="col-md-6">
                            <label for="txtIDExtSp" style="font-size: 14px;">Fecha Zendesk</label>
                                <input type="datetime-local" id="idtxtFechaHorasendesk" name="datetimes" class="form-control" data-toggle="tooltip" title="Fecha & Hora">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="capaDos" style="display: inline;">
        <div class="row">
            
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><i class="fas fa-list" style="font-size: 20px; color: #58f13c;"></i> Detalle de responsabilidad</label>
                    <div class="row">
                    <?php 
                        $varlistcategorias1 = Yii::$app->db->createCommand("select * from tbl_basechat_categorias where anulado = 0 and pcrc = $varpcrc")->queryAll();

                        foreach ($varlistcategorias1 as $key => $value) {
                            $varconteo = $varconteo+ 1;
                            $varidlistas1 = $value['idlista'];
                            $varlistmotivos1 = Yii::$app->db->createCommand("select * from tbl_basechat_motivos where anulado = 0 and idlista = $varidlistas1 ")->queryAll();
                            $listData1 = ArrayHelper::map($varlistmotivos1, 'idbaselista', 'nombrelista');
                    ?>
                            <div class="col-md-4">
                                <label id="<?php echo 'idOnevar'.$value['idlista']; ?>" style="font-size: 14px;"><?php echo $value['nombrecategoria'].'...'; ?></label>
                                <?php  echo $form->field($model, 'idbaselista')->dropDownList($listData1, ['prompt' => 'Seleccionar Respuesta...', 'id'=>'Idrta'.$value['idlista']])?>  
                            </div>
                        
                    <?php
                        }
                    ?>                          
                    </div>
                </div>
            </div>

        </div>
    </div>
    <hr>
    <div class="capaTres" style="display: inline;">
        <div class="row">

            <div class="col-md-12">
                <div class="card1 mb">
                    <label><i class="fas fa-list" style="font-size: 20px; color: #58f13c;"></i> Complemento de responsabilidad</label>
                    <div class="row">
                        <div class="col-md-6">
                            <label id="idvarsolicitud" style="font-size: 14px;">Solicitud...</label>
                            <?= $form->field($model, 'fsolicitud')->textInput(['maxlength' => 250, 'placeholder'=>'Ingresar Solicitud', 'id'=>'idfsolicitud']) ?>
                        </div>
                        <div class="col-md-6">
                            <label id="idvarsolucion" style="font-size: 14px;">Solución...</label>
                            <?= $form->field($model, 'fsolucion')->textInput(['maxlength' => 250, 'placeholder'=>'Ingresar Solución', 'id'=>'idfsolucion']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label id="idvarsolicitud" style="font-size: 14px;">Observación...</label>
                            <?= $form->field($model, 'fobservacion')->textInput(['maxlength' => 250, 'placeholder'=>'Ingresar Observación', 'id'=>'idfobservacion']) ?>
                        </div>
                        <div class="col-md-6">
                            <label id="idvarsolucion" style="font-size: 14px;">Procedimiento...</label>
                            <?= $form->field($model, 'fprocedimiento')->textInput(['maxlength' => 250, 'placeholder'=>'Ingresar Procedimiento', 'id'=>'idfprocedimiento']) ?>
                            <?= $form->field($model, 'fechazendeks')->textInput(['maxlength' => 250, 'class' => 'hidden', 'id'=>'idfechazendeks']) ?>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
    <hr>
    <div id="capaCinco" style="display: inline"> 
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><i class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></i> Acciones: </label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><i class="fas fa-save" style="font-size: 17px; color: #FFC72C;"></i> Guardar Información: </label> 
                                <div onclick="generated();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                  Guardar Informacion
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><i class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></i> Cancelar y regresar: </label> 
                                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
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
</div>

<script type="text/javascript">
    function generated(){
        var varvasrchatid = "<?php echo $varvasrchatid; ?>";
        var varvarconteo = "<?php echo $varconteo; ?>";
        var vartxtticket = document.getElementById("txtticket").value;
        var varidtxtFechaHoraclasifi = document.getElementById("idtxtFechaHoraclasifi").value;
        var varidtxtFechaHorasendesk = document.getElementById("idtxtFechaHorasendesk").value;
        var varidfsolicitud = document.getElementById("idfsolicitud").value;
        var varidfsolucion = document.getElementById("idfsolucion").value;
        var varidfobservacion = document.getElementById("idfobservacion").value;
        var varidfprocedimiento = document.getElementById("idfprocedimiento").value;

        if (varidtxtFechaHorasendesk == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar la fecha Zendesk","warning");
            document.getElementById('idtxtFechaHorasendesk').style.backgroundColor = '#f7b9b9';
            return; 
        }else{

            varidbloque = 0;
            for (var i = 0; i < varvarconteo; i++) {
                varbloque = i + 1;
                var varidbloque = document.getElementById('Idrta'+varbloque).value;

                var varidtext = document.getElementById('idOnevar'+varbloque).innerHTML;

                if (varidbloque != "") {
                //     event.preventDefault();
                //     swal.fire("!!! Advertencia !!!","Debe seleccionar datos a la categoria "+varidtext,"warning");
                //     document.getElementById('Idrta'+varbloque).style.backgroundColor = '#f7b9b9';
                //     return; 
                // }else{
                //     document.getElementById('Idrta'+varbloque).style.backgroundColor = '#fff';



                    $.ajax({
                        method: "get",
                        url: "createshowbasepart1",
                        data: {
                            txtvarbloque : varbloque,
                            txtvaridbloque : varidbloque,
                            txtvartxtticket : vartxtticket,
                            txtvaridtxtFechaHoraclasifi : varidtxtFechaHoraclasifi,
                            txtvaridtxtFechaHorasendesk : varidtxtFechaHorasendesk,
                        },
                        success : function(response){
                            numRta =   JSON.parse(response);
                        }
                    });
                }            
            }
        
            document.getElementById('idtxtFechaHorasendesk').style.backgroundColor = '#fff';

            $.ajax({
                method: "get",
                url: "createshowbasepart2",
                data: {
                    txtvartxtticket : vartxtticket,
                    txtvaridtxtFechaHoraclasifi : varidtxtFechaHoraclasifi,
                    txtvaridtxtFechaHorasendesk : varidtxtFechaHorasendesk,
                    txtvaridfsolicitud : varidfsolicitud,
                    txtvaridfsolucion : varidfsolucion,
                    txtvaridfobservacion : varidfobservacion,
                    varidfprocedimiento : varidfprocedimiento,
                },
                success : function(response){
                    numRta =   JSON.parse(response);
                    window.open('../basechat/index','_self');
                }
            });
        }        

    };
</script>