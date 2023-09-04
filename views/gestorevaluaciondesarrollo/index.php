<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de Desarrollo';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
. ' {input}{error}{hint}</div>';

$varidlist = Yii::$app->db->createCommand("select tipoeval.idevaluaciontipo AS id_tipoevaluacion, tipoeval.tipoevaluacion AS nom_eval from tbl_gestor_evaluacion_nombre_tipoeval eval_habilitadas
INNER JOIN tbl_evaluacion_tipoeval tipoeval
ON eval_habilitadas.id_evaluacion_tipoeval= tipoeval.idevaluaciontipo
WHERE eval_habilitadas.id_evaluacion_nombre=2")->queryAll();

$opcion_tipo_evaluacion = ArrayHelper::map($varidlist, 'id_tipoevaluacion', 'nom_eval'); 

$listData = ArrayHelper::map($varidlist, 'idevaluaciontipo', 'tipoevaluacion');
$varTipos = ['Eliminar evaluación' => 'Eliminar evaluación'];

$options_tipo_novedad = [        
    'eliminacion_evaluacion' => 'Eliminación Evaluación',
    'otra_novedad' => 'Otra novedad'
];

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
        background-image: url('../../images/Banner_Ev_Desarrollo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .color-required{
        color: #db2c23;
    }

</style>

<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<?php 
    if ($existe_usuario['cant_registros'] == '0') {    
?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 18px; color: #db2c23;"><em class="fa fa-info-circle" style="font-size: 20px; color: #db2c23;"></em> Aviso </label>
                <label style="font-size: 15px;"> <?= Yii::t('app', 'Tu usuario no se encuentra registrado para realizar la Evaluación de Desarrollo. Si crees que se trata de un error, por favor comunicarse con el administrador.') ?></label>
            </div>
        </div>
    </div>
</div>
<hr>
<?php 
    } else {   
?>
<div class="CapaDos" style="display: inline;">   
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <br>
                    <div class="row">
                        <div class="col-md-12" class="text-center">                            
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <label style="font-size: 20px;"> ¡Te damos la bienvenida!</label>
                                    <br>
                                    <!-- <label style="font-size: 13px;"> Evalúa sólo las personas que lleven mínimo 3 meses trabajando contigo.</label> -->
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card2 mb">
                                <label style="font-size: 23px; text-align: center;"> Autoevaluación </label>
                                <?php if ($estado_autoevaluacion == 0) { ?>
                                    <?php if ($novedades_autoevaluacion == "En espera") { ?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #FFAE58; align-self: center;"></em>
                                        <br>
                                        <label style="font-size: 15px; text-align: center;"> Novedad en espera de ser aprobada </label>
                                    <?php }else{?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #f7b9b9; align-self: center;"></em>
                                        <br>
                                        <?= Html::a('Realizar evaluación',  ['autoevaluacion', 'id_user' => $id_usuario, 'id_evalua'=> $id_evaluacion_actual], ['class' => 'btn btn-info',
                                                    'style' => 'background-color: #337ab7',
                                                    'data-toggle' => 'tooltip',
                                                    'id'=> 'btn_autoevaluacion',
                                                    'title' => 'Autoevaluación'])
                                        ?>  
                                    <?php } ?>
                                <?php }else{ ?>
                                    <em class="fas fa-book" style="font-size: 45px; color: #5DED6C; align-self: center;"></em>
                                    <br>
                                    <label style="font-size: 15px; text-align: center;"> Completado </label>
                                <?php } ?>
                            </div>
                        </div>                        
                        
                        <?php 
                       
                        if($id_usuario && $esjefe==1){ ?>
                        <div class="col-md-3">
                            <div class="card2 mb">
                                <label style="font-size: 23px; text-align: center;"> Evaluación a Cargo </label>
                                <?php if ($varcargo > 0) { ?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #f7b9b9; align-self: center;"></em>
                                        <br> 
                                        <?= Html::button('Realizar evaluación', ['value' => url::to(['modalevaluacionacargo', 'id_jefe' => $id_usuario, 'id_evalua'=> $id_evaluacion_actual]), 'class' => 'btn btn-info', 'id'=>'modalButton', 'data-toggle' => 'tooltip', 'title' => 'Evaluación a Cargo', 'style' => 'background-color: #337ab7']) 
                                            ?> 

                                            <?php
                                                Modal::begin([
                                                        'header' => '<h4></h4>',
                                                        'id' => 'modal',
                                                ]);

                                                echo "<div id='modalContent'></div>";
                                                                            
                                                Modal::end(); 
                                            ?>
                                <?php }else{ ?>
                                    <em class="fas fa-book" style="font-size: 45px; color: #5DED6C; align-self: center;"></em>
                                    <br>
                                    <label style="font-size: 15px; text-align: center;"> Completado </label>
                                <?php } ?>
                            </div>
                        </div>  
                    <?php } ?>                  
                    </div>
                    <br>

                    <!-- Mensajes si completo las evaluaciones asociadas a su usuario-->
                    <?php if ($estado_autoevaluacion != 0) {  ?>
                        <?php if ($no_tiene_jefe_directo) { ?>                           
                                <div class="row">
                                    <div class="col-md-12" class="text-center">                            
                                        <div class="panel panel-default">
                                            <div class="panel-body" style="background-color: #dfffdc;">
                                                <label style="font-size: 20px;"><em class="fas fa-check-circle" style="font-size: 25px; color: #64ea57;"></em> ¡Gracias! No encontramos un jefe directo; por ende, solo podrás visualizar tu autoevaluación en los resultados.</label>                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php } else if($completo_todas_las_evaluaciones_asociadas) { ?>
                            <div class="row">
                                    <div class="col-md-12" class="text-center">                            
                                        <div class="panel panel-default">
                                            <div class="panel-body" style="background-color: #dfffdc;">
                                                <label style="font-size: 20px;"><em class="fas fa-check-circle" style="font-size: 25px; color: #64ea57;"></em> ¡Gracias! Tu jefe ya te evalúo, ya puedes ir a conocer tus resultados. </label>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php } else { ?>
                        <div class="row">
                                    <div class="col-md-12" class="text-center">                            
                                        <div class="panel panel-default">
                                            <div class="panel-body" style="background-color: #dfffdc;">
                                                <label style="font-size: 20px;"><em class="fas fa-check-circle" style="font-size: 25px; color: #64ea57;"></em> ¡Gracias! Sólo queda pendiente la evaluación de tu jefe para conocer tus resultados.</label>
                                                
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        <?php } ?>
                        
                    <?php } ?>
                     <!-- Mensaje si completo las evaluaciones asociadas a su usuario FIN-->

                </div>
            </div>
        </div>
    </div> 
    <br>
    <div class="CapaTres" id="capa_novedad_general" style="display: inline;">
        <?php $form = ActiveForm::begin([
            'action' => 'crearnovedadgeneral',
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
            ]); ?> 
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="col-md-6">
                                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em> Novedad general</label>
                            </div>
                            <div class="col-md-6" class="text-right">
                                <div onclick="opennovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbn1" >
                                    [ Abrir + ]
                                </div> 
                                <div onclick="closenovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbn2" >
                                    [ Cerrar - ]
                                </div> 
                            </div>
                        </div>                        
                    </div>
                    <br>
                    <div class="capaExt" id="capa00" style="display: none;">
                        <!-- Datos Generales de Novedad-->
                        <div class="row">                           
                            <div class="col-md-4" style="display: inline;">
                                <label style="font-size: 15px;"> Nombre Solicitante </label>
                                <?= Html::textInput('nombre_usuario',  $nom_solicitante, ['readonly' => true, 'style' => 'width: 100%;']) ?>     
                                <?= $form->field($model, 'id_solicitante', ['options' => ['class' => 'hidden']])->textInput(['id' => 'id_persona_solicitante', 'value'=>$id_usuario])->label(false); ?>
                                <?= $form->field($model, 'cc_solicitante', ['options' => ['class' => 'hidden']])->textInput(['id' => 'cc_persona_solicitante', 'value'=>$documento])->label(false); ?>                         
                            </div>
                            <div class="col-md-4" style="display: inline;">
                                <label style="font-size: 15px;"> Selecciona tipo de novedad <span class="color-required"> *</span></label>
                                <?= Html::dropDownList('seleccion', "", $options_tipo_novedad, 
                                            [
                                            "id"=>"tipo_novedad",
                                            "class" => "form-control",
                                            'prompt' => 'Seleccione novedad...',
                                            'onChange'=>'habilitar_tipo_evaluacion(this.value)']); 
                                ?>
                            </div>                            
                        </div>
                        <br>
                        <!-- Lista de personas a cargo-->
                        <div class="row"> 
                            <div class="col-md-4" style="display: inline;">
                                <label style="font-size: 15px;"> Ingresar comentario <span class="color-required"> *</span> </label>
                                <?= $form->field($model, 'comentarios_solicitud')->textArea(['maxlength' => true, 'placeholder'=>'Agregar la justificación de la novedad', 'id'=>'comentarios_solicitante']) ?>
                            </div>
                            <div class="col-md-4" id="capatipoEvaluacion" style="display: inline;">
                                <label style="font-size: 15px;"> Selecciona tipo evaluación <span class="color-required"> *</span></label>
                                <?=  $form->field($model, 'id_tipo_evaluacion')->dropDownList($opcion_tipo_evaluacion, [
                                                'id' => 'idTipoEval',
                                                'prompt'=>'Seleccione el tipo de evaluación...',
                                                'onChange'=>'habilitarPersonal(this.value)'                                                
                                            ])->label('');
                                ?>
                            </div>                           
                            <div class="col-md-4" id="capaP" style="display: none;">
                                <label style="font-size: 15px;"> Seleccionar persona <span class="color-required"> *</span></label>
                                <?= $form->field($model,'id_evaluado')->dropDownList( $opcion_personas_a_cargo, [
                                            'prompt' => 'Seleccione Una Persona',
                                            'id' => 'id_usuario_evaluado' 
                                            ])->label('');
                                ?>
                            </div>                            
                        </div>
                        <br>
                        <!-- Acciones: Guardar novedad-->
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <label style="font-size: 16px;"><em class="fas fa-save" style="font-size: 17px; color: #FFC72C;"></em> Guardar novedad: </label> 
                                    <div onclick="savegeneral();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                        Guardar
                                    </div>  
                                </div>
                            </div>                            
                        </div> 

                    </div>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
    <hr>
    <?php 
        } 
    ?>

<?php if (Yii::$app->session->hasFlash('success_novedad')): ?>
  <script>
    swal.fire("", '<?= Yii::$app->session->getFlash('success_novedad') ?>', "success");
  </script>
<?php elseif (Yii::$app->session->hasFlash('error_novedad')): ?>
  <script>
    swal.fire("", '<?= Yii::$app->session->getFlash('error_novedad') ?>', "error");
  </script>
<?php endif; ?>

<script type="text/javascript">
    function opennovedad(){
        var varidtbn1 = document.getElementById("idtbn1");
        var varidtbn2 = document.getElementById("idtbn2");
        var varidnovedad = document.getElementById("capa00");

        varidtbn1.style.display = 'none';
        varidtbn2.style.display = 'inline';
        varidnovedad.style.display = 'inline';

    };

    function closenovedad(){
        var varidtbn1 = document.getElementById("idtbn1");
        var varidtbn2 = document.getElementById("idtbn2");
        var varidnovedad = document.getElementById("capa00");

        varidtbn1.style.display = 'inline';
        varidtbn2.style.display = 'none';
        varidnovedad.style.display = 'none';
    };

    function habilitar_tipo_evaluacion(valorSeleccionado){
        
        var varcapaTipoEval = document.getElementById("capatipoEvaluacion");
        var varcapaP = document.getElementById("capaP"); //capa Personal

        if (valorSeleccionado == "otra_novedad") {
            varcapaTipoEval.style.display = "none"; 
             varcapaP.style.display = "none";
        } else {
            varcapaTipoEval.style.display = "inline"; 
        }

    }

    function habilitarPersonal(valorSeleccionado){
         // Obtener el div para mostrar u ocultar
        var varcapaP = document.getElementById("capaP");

        // Mostrar u ocultar el div según el valor seleccionado
        if (valorSeleccionado == 3) {
            varcapaP.style.display = "inline"; // Mostrar el div
        } else {
            varcapaP.style.display = "none"; // Ocultar el div
        }

    };

    function savegeneral(){
       
        var id_solicitante = '<?= $id_usuario ?>';
        var cc_solicitante = '<?= $documento ?>';
        
        var selector_tipo_novedad = document.getElementById("tipo_novedad");
        var tipo_novedad = selector_tipo_novedad.value; //eliminar evaluacio 
        var id_evaluado = document.getElementById("id_usuario_evaluado").value;
        
        var selector_comentarios_solicitud = document.getElementById("comentarios_solicitante");
        var txt_comentarios_solicitud = selector_comentarios_solicitud.value;
        
        var selector_id_tipo_evaluacion = document.getElementById("idTipoEval");
        var txt_id_tipo_evaluacion = selector_id_tipo_evaluacion.value;
    
        //Validacion vacío
        if (tipo_novedad == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Ingrese tipo de la novedad","warning");
            return;
        }

        if (txt_comentarios_solicitud == "") {
            swal.fire("!!! Advertencia !!!","Campo Comentarios esta vacío","warning");
            return;
        }

        if (tipo_novedad == "eliminacion_evaluacion" && txt_id_tipo_evaluacion == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Ingrese tipo de evaluación","warning");
            return;
        }

        
        if(txt_id_tipo_evaluacion == 3 && id_evaluado=="") {
            swal.fire("!!! Advertencia !!!","Escoger una persona de la lista","warning");
            return;
        }
        
        $.ajax({
            method: "post",
            url: "crearnovedadgeneral",
            data: {
                tipo_novedad : tipo_novedad,
                id_nom_evaluacion: txt_id_tipo_evaluacion,
                id_solicitante: id_solicitante,
                cc_solicitante:cc_solicitante,
                id_evaluado: id_evaluado,
                comentarios_solicitud: txt_comentarios_solicitud,
                _csrf:'<?=\Yii::$app->request->csrfToken?>'
            },
            success : function(response){

                if(response.status==="error"){
                    selector_comentarios_solicitud.value = "";
                    selector_tipo_novedad.value ="";
                    swal.fire("!!! Error !!!",response.data,"error");
                    return;
                }

                if(response.status==="success"){
                    selector_tipo_novedad.value="";
                    selector_comentarios_solicitud.value = "";
                    swal.fire("",response.data,"success");    
                    return; 
                }

            }
        });   

    };
</script>


