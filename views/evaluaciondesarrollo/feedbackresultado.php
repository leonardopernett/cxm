<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

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
    $vardocumentosijefe = Yii::$app->db->createCommand("select ue.documento_jefe from tbl_usuarios_evalua ue where ue.documento_jefe = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidcargo = Yii::$app->db->createCommand("select ue.id_dp_cargos from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidposicion = Yii::$app->db->createCommand("select ue.id_dp_posicion from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidfuncion = Yii::$app->db->createCommand("select ue.id_dp_funciones from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();

    $varidconteo = Yii::$app->db->createCommand("select count(documento) from tbl_usuarios_evalua ue where ue.documento_jefe = '$vardocumentjefe' and ue.id_dp_cargos = $varidcargo and ue.id_dp_posicion = $varidposicion and ue.id_dp_funciones = $varidfuncion")->queryScalar();
    $documento1 = 0;
    $documento2 = 1152214703;
    $modelo = 0;

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

    <div class="CapaTres" style="display: inline;">   
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-table" style="font-size: 20px; color: #D8E1D9;"></em> Evaluaciones: </label> 
                    <br>
                    <div class="row">
                        <div class="col-md-12" align="center">                            
                            <div class="panel panel-default">
                                <div class="panel-body">
                                    <label style="font-size: 20px;"> ¡Te damos la bienvenida!</label>
                                    <br>
                                    <label style="font-size: 13px;"> En este espacio podrás conocer tus resultados. Además, si tienes personas a cargo, desde aquí deberás gestionar el feedback de éstas.</label>
                                </div>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div> 
    <hr>
    <div class="CapaCuatro" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="far fa-file-alt" style="font-size: 20px; color: #1483d9;"></em> Feedback y resultados: </label>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card2 mb">
                                <label style="font-size: 15px;"><em class="far fa-comment-alt" style="font-size: 15px; color: #205d8c;"></em> Generar Feedback: </label>
                                <em class="fas fa-pencil-alt" style="font-size: 45px; color: #1da3e0; align-self: center;"></em>
                                <br>
                                <?= Html::a('Generar Feedback',  ['evaluacionfeedback','model'=>$modelo,'documento'=>$documento1], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #337ab7',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Feedback'])
                                ?>
                                 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card2 mb">
                                <label style="font-size: 15px;"><em class="fas fa-chart-pie" style="font-size: 15px; color: #205d8c;"></em> Resultados: </label>
                                <em class="fas fa-chart-bar" style="font-size: 45px; color: #1da3e0; align-self: center;"></em>
                                <br>
                                <?= Html::a('Resultados',  ['resultadoevaluacion','model'=>$modelo,'documento'=>$documento2], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Resultados']) 
                                ?>
                            </div>
                        </div> 
                                          
                    </div>
                </div>
            </div>
        </div>
    </div>

