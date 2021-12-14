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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<div class="capaPP" id="capaP" style="display: inline;">
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
    ]
    ]); ?>
    <div class="capaUno" style="display: inline;">
        <div class="row">
            
            <div class="col-md-6">
                <div class="card1 mb">
                    <label><em class="fas fa-edit" style="font-size: 20px; color: #559FFF;"></em> Información general</label>
                    <div class="row">
                        <div class="col-md-12">
                            <label for="txtticket" style="font-size: 14px;">Número del ticket...</label>
                            <input type="text" id="txtticket" name="datetimes" readonly="readonly" value="<?php echo $varticket; ?>" class="form-control" data-toggle="tooltip" title="Ticket">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label for="txtemail" style="font-size: 14px;">Fecha transacción...</label>
                            <input type="text" id="txtemail" name="datetimes" readonly="readonly" value="<?php echo $varencuesta; ?>" class="form-control" data-toggle="tooltip" title="Email Cliente">
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
                    <label><em class="fas fa-bookmark" style="font-size: 20px; color: #559FFF;"></em> Preguntas generales</label>
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
                            <input type="text" id="idtxtFechaHorasendesk" name="datetimes" readonly="readonly" value="<?php echo $varzendesk; ?>" class="form-control" data-toggle="tooltip">
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
                    <label><em class="fas fa-list" style="font-size: 20px; color: #58f13c;"></em> Detalle de responsabilidad</label>
                    <div class="row">
                    <?php 
                        
                        foreach ($varlistshowbase as $key => $value) {
                            $varidcategoria = $value['idlista'];
                            $varidmotivos = $value['idbaselista'];

                            if ($varidcategoria != null && $varidmotivos != null) {
                                $varnamecategoria = Yii::$app->db->createCommand("select distinct nombrecategoria from tbl_basechat_categorias where anulado = 0 and idlista = $varidcategoria")->queryScalar();
                                $varnamemotivos = Yii::$app->db->createCommand("select distinct nombrelista from tbl_basechat_motivos where anulado = 0 and idbaselista = $varidmotivos")->queryScalar();
                            
                            

                    ?>
                            <div class="col-md-4">
                                <label id="<?php echo 'idOnevar'.$varidcategoria; ?>" style="font-size: 14px;"><?php echo $varnamecategoria.'...'; ?></label>
                                <input type="text" id="<?php echo 'idrta'.$varidmotivos; ?>" name="datetimes" readonly="readonly" value="<?php echo $varnamemotivos; ?>" class="form-control" data-toggle="tooltip">                                
                            </div>
                        
                    <?php
                            }
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
                    <label><em class="fas fa-list" style="font-size: 20px; color: #58f13c;"></em> Complemento de responsabilidad</label>
                    <div class="row">

                    <?php 
                        foreach ($varlistshowbase as $key => $value) {
                            $varidcategoria = $value['idlista'];
                            $varidmotivos = $value['idbaselista'];

                            if ($varidcategoria == null && $varidmotivos == null) {

                    ?>
                            <div class="col-md-6">
                                <label id="idvarsolicitud" style="font-size: 14px;">Solicitud...</label>
                                <input type="text" id="<?php echo 'idrtaSolicitud'; ?>" name="datetimes" readonly="readonly" value="<?php echo $value['fsolicitud']; ?>" class="form-control" data-toggle="tooltip">
                            </div>
                            <div class="col-md-6">
                                <label id="idvarsolucion" style="font-size: 14px;">Solución...</label>
                                <input type="text" id="<?php echo 'idrtaSolucion'; ?>" name="datetimes" readonly="readonly" value="<?php echo $value['fsolucion']; ?>" class="form-control" data-toggle="tooltip">
                            </div>
                            <div class="col-md-6">
                                <label id="idvarsolicitud" style="font-size: 14px;">Observación...</label>
                                <input type="text" id="<?php echo 'idrtaObservacion'; ?>" name="datetimes" readonly="readonly" value="<?php echo $value['fobservacion']; ?>" class="form-control" data-toggle="tooltip">
                            </div>
                            <div class="col-md-6">
                                <label id="idvarsolucion" style="font-size: 14px;">Procedimiento...</label>
                                <input type="text" id="<?php echo 'idrtaProcedimiento'; ?>" name="datetimes" readonly="readonly" value="<?php echo $value['fprocedimiento']; ?>" class="form-control" data-toggle="tooltip">
                            </div>
                    <?php 
                            }
                        }
                    ?>
                        
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
                    <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                    <div class="row">                        
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
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
