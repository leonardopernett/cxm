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

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();

    $varexist = Yii::$app->db->createCommand("select count(documento) from tbl_usuarios_evalua where documento in ('$vardocument')")->queryScalar();

    $varauto = Yii::$app->db->createCommand("select count(idevaluaciontipo) from tbl_evaluacion_desarrollo where idevaluador in ('$vardocument') and idevaluaciontipo = 1 and realizada is not null")->queryScalar();

    $varjefe = Yii::$app->db->createCommand("select count(idevaluaciontipo) from tbl_evaluacion_desarrollo where idevaluador in ('$vardocument') and idevaluaciontipo = 2 and realizada is not null")->queryScalar();

    $varpar = Yii::$app->db->createCommand("select count(idevaluaciontipo) from tbl_evaluacion_desarrollo where idevaluador in ('$vardocument') and idevaluaciontipo = 4 and realizada is not null")->queryScalar();

    $varcargo = Yii::$app->db->createCommand("select count(idevaluaciontipo) from tbl_evaluacion_desarrollo where idevaluador in ('$vardocument') and idevaluaciontipo = 3 and realizada is not null")->queryScalar();

    $vardocumentjefe = Yii::$app->db->createCommand("select ue.documento_jefe from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidcargo = Yii::$app->db->createCommand("select ue.id_dp_cargos from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidposicion = Yii::$app->db->createCommand("select ue.id_dp_posicion from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidfuncion = Yii::$app->db->createCommand("select ue.id_dp_funciones from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();

    $varidconteo = Yii::$app->db->createCommand("select count(documento) from tbl_usuarios_evalua ue where ue.documento_jefe = '$vardocumentjefe' ")->queryScalar();


    $varnovedadesa = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_novedadesauto ue where ue.documento = '$vardocument' and cambios is not null and aprobado = 0")->queryScalar();
    $varnovedadesj = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_novedadesjefe ue where ue.documento = '$vardocument' and cambios is not null and aprobado = 0")->queryScalar();
    $varnovedadesg = Yii::$app->db->createCommand("select count(documento) from tbl_evaluacion_novedadesgeneral ue where ue.documento = '$vardocument' and aprobado = 1")->queryScalar();

    $varidconteocargo = Yii::$app->db->createCommand("select count(*) from tbl_usuarios_evalua ue where ue.documento_jefe = '$vardocument' and ue.documento != '$vardocument'")->queryScalar();
    $varidconteocargo2 = Yii::$app->db->createCommand("select count(*) from tbl_evaluacion_desarrollo ed where ed.idevaluador = '$vardocument' and idevaluaciontipo = 3 and realizada is not null")->queryScalar();

    if ($varidconteocargo2 != 0 && $varidconteocargo != 0) {
        $varresulcargo = ($varidconteocargo2/$varidconteocargo)*100;
    }else{
        $varresulcargo = 0;
    }

    $varidlist = Yii::$app->db->createCommand("select * from tbl_evaluacion_tipoeval where anulado = 0 order by tipoevaluacion")->queryAll();
    $listData = ArrayHelper::map($varidlist, 'idevaluaciontipo', 'tipoevaluacion');
    
    $varTipos = ['Eliminar evaluación' => 'Eliminar evaluación'];

    
    $varidlistC = Yii::$app->db->createCommand("select ue.nombre_completo, ue.documento from tbl_usuarios_evalua ue 
    inner join tbl_evaluacion_solucionado es on ue.documento = es.documentoevaluado where es.documento = $vardocument and es.anulado = 0 group by ue.documento")->queryAll();
    $listDataC = ArrayHelper::map($varidlistC, 'documento', 'nombre_completo');

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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>

<script src="../../js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<script>
    $(document).ready(function(){
        $.fn.snow();
    });
</script>
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
    if ($sessiones == '0') {    
?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;">emi class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Subir información: </label>
                            <?= Html::a('Actualiza Usuarios',  ['usuarios_evalua'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Actualiza Usuarios'])
                            ?>
                             
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></em> Parametrizar datos: </label>
                            <?= Html::a('Parametrizar',  ['parametrizardatos'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #337ab7',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar']) 
                                ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-check-circle" style="font-size: 15px; color: #FFC72C;"></em> Importar Usuarios: </label> 
                            <?= Html::button('Importar Usuarios', ['value' => url::to('importarusuarioseval'), 'class' => 'btn btn-success', 'id'=>'modalButton6',
                                'data-toggle' => 'tooltip',
                                'title' => 'Importar Usuarios', 'style' => 'background-color: #337ab7']) 
                            ?>  
                            <?php
                                Modal::begin([
                                    'header' => '<h4>Importar Usuarios </h4>',
                                    'id' => 'modal6',
                                       //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent6'></div>";
                                                                                
                                Modal::end(); 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-eye" style="font-size: 15px; color: #FFC72C;"></em> Verificar Novedades: </label>
                            <?= Html::button('Verificar', ['value' => url::to(['evaluaciondesarrollo/novedadgeneral']), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Verficar', 'style' => 'background-color: #337ab7']) 
                            ?> 

                            <?php
                                Modal::begin([
                                    'header' => '<h4></h4>',
                                    'id' => 'modal3',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent3'></div>";
                                                                              
                                Modal::end(); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<?php 
    }    
?>
<?php 
    //Este proceso se comenta pero despues se quita if ($sessiones != '852' || $sessiones != '2953' || $sessiones != '6080' || $sessiones != '57' || $sessiones != '3229') {    
    if ($sessiones == '0') {
?>
    <br>
    <div class="CapaDos" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-exclamation" style="font-size: 20px; color: #ff2c2c;"></em> Notificación: </label>
                    <br>
                    <label>CXM informa que hemos terminado la fase de evaluación, gracias.</label> 
                </div>
            </div>
        </div>
    </div> 
    <hr>
<?php 
    }else{
?>
    <div class="CapaTres" style="display: inline;">   
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <br>
                    <div class="row">
                        <div class="col-md-12" align="center">                            
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <label style="font-size: 20px;"> ¡Te damos la bienvenida!</label>
                                    <br>
                                    <label style="font-size: 13px;"> Evalúa sólo las personas que lleven mínimo 3 meses trabajando contigo.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card2 mb">
                                <label style="font-size: 23px; text-align: center;"> Autoevaluación </label>
                                <?php if ($varauto == 0) { ?>
                                    <?php if ($varnovedadesa == 1) { ?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #FFAE58; align-self: center;"></em>
                                        <br>
                                        <label style="font-size: 15px; text-align: center;"> Novedad en espera de ser aprobado </label>
                                    <?php }else{?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #f7b9b9; align-self: center;"></em>
                                        <br>
                                        <?= Html::a('Realizar evaluación',  ['evaluacionauto'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #337ab7',
                                                    'data-toggle' => 'tooltip',
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
                        <div class="col-md-3">
                            <div class="card2 mb">
                                <label style="font-size: 23px; text-align: center;"> Evaluación Jefe </label>
                                <?php if ($varjefe == 0) { ?>
                                    <?php if ($vardocument != "3353483") { ?>
                                    <?php if ($varnovedadesj == 1) { ?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #FFAE58; align-self: center;"></em>
                                        <br>
                                        <label style="font-size: 15px; text-align: center;"> Novedad en espera de ser aprobado </label>
                                    <?php }else{?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #f7b9b9; align-self: center;"></em>
                                        <br>
                                        <?= Html::a('Realizar evaluación',  ['evaluacionjefe'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #337ab7',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Evaluación Jefe'])
                                        ?>   
                                    <?php } ?>
                                    <?php }else{ ?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #C1C1C1; align-self: center;"></em>
                                            <br>
                                        <label style="font-size: 15px; text-align: center;"> Sin jefe a evaluar </label>
                                    <?php    } ?>
                                <?php }else{ ?>
                                    <em class="fas fa-book" style="font-size: 45px; color: #5DED6C; align-self: center;"></em>
                                    <br>
                                    <label style="font-size: 15px; text-align: center;"> Completado </label>
                                <?php } ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card2 mb">                            
                                <label style="font-size: 23px; text-align: center;"> Evaluación de Pares </label>
                                <?php if ($varpar == 0) { ?>
                                    <?php if ($varidconteo > 1 ) { ?>
                                        <?php if ($vardocument != "3353483") { ?>
                                        <?php if ($varnovedadesg == 1) { ?>
                                            <em class="fas fa-book" style="font-size: 45px; color: #C1C1C1; align-self: center;"></em>
                                            <br>
                                            <label style="font-size: 15px; text-align: center;"> Pares no evaluados </label>
                                        <?php }else{ ?>
                                            <em class="fas fa-book" style="font-size: 45px; color: #f7b9b9; align-self: center;"></em>
                                            <br>                                    
                                            <?= Html::button('Realizar evaluación', ['value' => url::to(['evaluaciondesarrollo/evaluacionpar']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Evaluación Personas', 'style' => 'background-color: #337ab7']) 
                                            ?> 

                                            <?php
                                                Modal::begin([
                                                          'header' => '<h4></h4>',
                                                          'id' => 'modal1',
                                                          //'size' => 'modal-lg',
                                                ]);

                                                echo "<div id='modalContent1'></div>";
                                                                              
                                                Modal::end(); 
                                            ?>
                                        <?php } ?>

                                        <?php }else{ ?>
                                            <em class="fas fa-book" style="font-size: 45px; color: #C1C1C1; align-self: center;"></em>
                                            <br>
                                            <label style="font-size: 15px; text-align: center;"> Sin pares a evaluar </label>
                                        <?php } ?>

                                    <?php }else{ ?>                                        
                                            <em class="fas fa-book" style="font-size: 45px; color: #C1C1C1; align-self: center;"></em>
                                            <br>
                                            <label style="font-size: 15px; text-align: center;"> Sin pares a evaluar </label>                                                                            
                                    <?php } ?>                                    
                                <?php }else{ ?>
                                    <em class="fas fa-book" style="font-size: 45px; color: #5DED6C; align-self: center;"></em>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-6" align="right" >
                                            <label style="font-size: 15px; text-align: center;"> Completado </label>
                                        </div>
                                        <div class="col-md-6">
                                            <?= Html::button('[ + ]', ['value' => url::to(['evaluaciondesarrollo/evaluacionpar']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Realizar más evaluaciones', 'style' => 'background-color: #4298b400; border-color: #4298b500 !important; color:#000000;']) 
                                            ?> 

                                            <?php
                                                Modal::begin([
                                                  'header' => '<h4></h4>',
                                                  'id' => 'modal1',
                                                  //'size' => 'modal-lg',
                                                ]);

                                                echo "<div id='modalContent1'></div>";
                                                                      
                                                Modal::end(); 
                                            ?>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>                            
                        </div>
                        <div class="col-md-3">
                            <div class="card2 mb">
                                <label style="font-size: 23px; text-align: center;"> Evaluación a Cargo </label>
                                <?php if ($varcargo == 0) { ?>
                                    <?php if ($varidconteocargo >= 1) { ?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #f7b9b9; align-self: center;"></em>
                                        <br> 
                                        <?= Html::button('Realizar evaluación', ['value' => url::to(['evaluaciondesarrollo/evaluacioncargo']), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Evaluación a Cargo', 'style' => 'background-color: #337ab7']) 
                                            ?> 

                                            <?php
                                                Modal::begin([
                                                          'header' => '<h4></h4>',
                                                          'id' => 'modal2',
                                                          //'size' => 'modal-lg',
                                                ]);

                                                echo "<div id='modalContent2'></div>";
                                                                              
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
                                            <div class="col-md-5" align="right">        
                                                <label style="font-size: 15px; text-align: center;"> Completado </label>
                                            </div>
                                            <div class="col-md-5">
                                                <?= Html::button('[ + ]', ['value' => url::to(['evaluaciondesarrollo/evaluacioncargo']), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Realizar más evaluaciones', 'style' => 'background-color: #4298b400; border-color: #4298b500 !important; color:#000000;']) 
                                                ?> 

                                                <?php
                                                    Modal::begin([
                                                      'header' => '<h4></h4>',
                                                      'id' => 'modal2',
                                                      //'size' => 'modal-lg',
                                                    ]);

                                                    echo "<div id='modalContent2'></div>";
                                                                          
                                                    Modal::end(); 
                                                ?>
                                            </div>
                                        </div>

                                    <?php }else{ ?>
                                        <em class="fas fa-book" style="font-size: 45px; color: #FFAE58; align-self: center;"></em>
                                        <br>  
                                        <div class="row">
                                        
                                            <div class="col-md-7" align="right" >
                                                <label style="font-size: 15px; text-align: center;"><?php echo 'Evaluaciones: '.$varresulcargo.'%'; ?></label>
                                            </div>
                                            <div class="col-md-5">
                                                <?= Html::button('[ + ]', ['value' => url::to(['evaluaciondesarrollo/evaluacioncargo']), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Realizar más evaluaciones', 'style' => 'background-color: #4298b400; border-color: #4298b500 !important; color:#000000;']) 
                                                ?> 

                                                <?php
                                                    Modal::begin([
                                                      'header' => '<h4></h4>',
                                                      'id' => 'modal2',
                                                      //'size' => 'modal-lg',
                                                    ]);

                                                    echo "<div id='modalContent2'></div>";
                                                                          
                                                    Modal::end(); 
                                                ?>
                                            </div>
                                        
                                        </div>
                                    <?php } ?>
                                <?php } ?>
                            </div>
                        </div>                    
                    </div>
                    <br>

                    <?php if ($varjefe != 0 && $varauto != 0) {  ?>
                        <?php if ($varpar != 0 || $varidconteo == 0 || $varnovedadesg == 1) { ?>
                            <?php if ($varidconteocargo == 0 || $varresulcargo == 100) { ?>
                                <div class="row">
                                    <div class="col-md-12" align="center">                            
                                        <div class="panel panel-default">
                                            <div class="panel-body" style="background-color: #dfffdc;">
                                                <label style="font-size: 20px;"><em class="fas fa-check-circle" style="font-size: 25px; color: #64ea57;"></em> ¡Gracias! Has finalizado todas las evaluaciones pendientes.</label>
                                                <br>
                                                <label style="font-size: 13px;"> Nota: Si deseas evaluar más pares puedes hacerlo mientras estén habilitados.</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                        <?php } ?>
                    <?php } ?>

                </div>
            </div>
        </div>
    </div> 
    <hr>
    <div class="CapaCuatro" style="display: inline;">
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?> 
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #3339fb;"></em> Eliminar evaluación</label>
                        </div>
                        <div class="col-md-6" align="right">
                            <div onclick="opennovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbn1" >
                                [ Abrir + ]
                            </div> 
                            <div onclick="closenovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbn2" >
                                [ Cerrar - ]
                            </div> 
                        </div>
                    </div>
                    <br>
                    <div class="capaExt" id="capa00" style="display: none;">
                        <div class="row">
                            <div class="col-md-4">
                                <label style="font-size: 15px;"> Selecciona tipo evaluación</label>
                                 <?=  $form->field($model, 'tipo_eva')->dropDownList(ArrayHelper::map(\app\models\EvaluacionTipoeval::find()->distinct()->where("anulado = 0")->orderBy(['tipoevaluacion'=> SORT_ASC])->all(), 'idevaluaciontipo', 'tipoevaluacion'),
                                        [
                                            'prompt'=>'Seleccione el tipo de evaluacion',
                                            'onchange' => '
                                                $.get(
                                                    "' . Url::toRoute('evaluaciondesarrollo/listarcedulas') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#requester").html(res);
                                                    }
                                                );
                                            ',

                                        ]
                                    )->label(''); 
                                ?>                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 15px;"> Selecciona un motivo</label>
                                <?= $form->field($model, "asunto")->dropDownList($varTipos, ['prompt' => 'Seleccionar Novedades', 'id'=>"idasuntosN"]) ?>
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 15px;"> Ingresar comentario</label>
                                <?= $form->field($model, 'comentarios')->textInput(['maxlength' => 250, 'placeholder'=>'Agregar la justificación de la novedad', 'id'=>'Idcomentarios']) ?>
                            </div>
                        </div> 
                        <br>
                        <div class="row">
                            <div class="col-md-4" id="capaP" style="display: inline;">
                                <label style="font-size: 15px;"> Seleccionar persona</label>
                                <?= $form->field($model,'evaluado')->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccione Una Persona',
                                            'id' => 'requester'
                                        ]
                                    )->label('');
                                ?>
                            </div>
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
    <div class="CapaSeis" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #3339fb;"></em> Información General</label>
                    <br>
                    <label style="font-size: 15px;">Si tienes alguna novedad o dificultad que requiera otro tipo de gestión, escribe tu caso al correo maria.vera@grupokonecta.com</label>                        
                </div>
            </div>
        </div>
    </div>
    <hr>
<?php 
    }
?>
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

    function habilitarvar(){
        var varidasuntosNcargo = document.getElementById("idtipoeva").value;
        var varcapaP = document.getElementById("capaP");

        if (varidasuntosNcargo == 3 || varidasuntosNcargo == 4) {
            varcapaP.style.display = 'inline';
        }else{
            varcapaP.style.display = 'none';
        }

    };

    function savegeneral(){
        var varidasuntosN = document.getElementById("idasuntosN").value;
        var varidasuntosNcargo = document.getElementById("evaluacionnovedadeslog-tipo_eva").value;
        var varIdcomentarios = document.getElementById("Idcomentarios").value;
        var varidpares = document.getElementById("requester").value;
        var varvardocument = "<?php echo $vardocument; ?>";

        if (varidasuntosN == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debes de seleccionar el asunto","warning");
            return;
        }else{
            if (varidasuntosNcargo == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debes de seleccionar el tipo de evaluación","warning");
                return;
            }else{
                if (varIdcomentarios == "") {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","Debes ingresar el comentario","warning");
                    return;
                }else{
                    $.ajax({
                        method: "get",
                        url: "createnovedadgeneral",
                        data: {
                            txtvaridasuntosNcargo : varidasuntosNcargo,
                            txtvaridasuntosN : varidasuntosN,
                            txtvarIdcomentarios : varIdcomentarios,
                            txtvaridpares : varidpares,
                            txtvarvardocument : varvardocument,
                        },
                        success : function(response){ 
                            numRta =   JSON.parse(response);
                            window.open('../evaluaciondesarrollo/index','_self');

                        }
                    });
                }
            }
        }

    };
</script>