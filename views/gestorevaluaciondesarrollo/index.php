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



//$var_exist_jefe = Yii::$app->db->createCommand("select count(identificacion) from tbl_gestor_evaluacion_jefes where identificacion in ('$var_document')")->queryScalar();
//$var_exist_colaborador = Yii::$app->db->createCommand("select count(identificacion) from tbl_gestor_evaluacion_colaboradores where identificacion in ('$var_document')")->queryScalar();

$varcargo=0; //si realizo evaluacion a personas a cargo
$varidconteocargo= 2; // si tiene personas a cargo sino no habilitar boton para evaluar
$varresulcargo = 80; // numero de personas a cargo o porcentaje de personas evaluadas

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
                                        <?= Html::a('Realizar evaluación',  ['autoevaluacion', 'id_user' => $id_usuario, 'id_evalua'=> $id_evaluacion_actual], ['class' => 'btn btn-success',
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
                                    <?php if ($varcargo == 0) { ?>
                                        <?php if ($varidconteocargo >= 1) { ?>
                                            <em class="fas fa-book" style="font-size: 45px; color: #f7b9b9; align-self: center;"></em>
                                            <br> 
                                            <?= Html::button('Realizar evaluación', ['value' => url::to(['modalevaluacionacargo', 'id_jefe' => $id_usuario, 'id_evalua'=> $id_evaluacion_actual]), 'class' => 'btn btn-success', 'id'=>'modalButton', 'data-toggle' => 'tooltip', 'title' => 'Evaluación a Cargo', 'style' => 'background-color: #337ab7']) 
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
                                            <em class="fas fa-book" style="font-size: 45px; color: #C1C1C1; align-self: center;"></em>
                                            <br>
                                            <label style="font-size: 15px; text-align: center;"> Sin personas a cargo </label>
                                        <?php } ?>
                                    <?php }else{ ?>
                                        <?php if ($varresulcargo == 100) { ?>  
                                            <em class="fas fa-book" style="font-size: 45px; color: #5DED6C; align-self: center;"></em>
                                            <br>                                                                              
                                                
                                            <div class="row">    
                                                <div class="col-md-5" class="text-right">        
                                                    <label style="font-size: 15px; text-align: center;"> Completado </label>
                                                </div>
                                                <div class="col-md-5">
                                                    <?= Html::button('[ + ]', ['value' => url::to(['evaluaciondesarrollo/evaluacioncargo']), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Realizar más evaluaciones', 'style' => 'background-color: #4298b400; border-color: #4298b500 !important; color:#000000;']) 
                                                    ?> 

                                                    <?php
                                                        Modal::begin([
                                                        'header' => '<h4></h4>',
                                                        'id' => 'modal3',
                                                        ]);

                                                        echo "<div id='modalContent3'></div>";
                                                                            
                                                        Modal::end(); 
                                                    ?>
                                                </div>
                                            </div>

                                        <?php }else{ ?>
                                            <em class="fas fa-book" style="font-size: 45px; color: #FFAE58; align-self: center;"></em>
                                            <br>  
                                            <div class="row">                                            
                                                <div class="col-md-7"class="text-right">
                                                    <label style="font-size: 15px; text-align: center;"><?php echo 'Evaluaciones: '.$varresulcargo.'%'; ?></label>
                                                </div>
                                            </div>
                                        <?php } ?>
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


