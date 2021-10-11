<?php

use yii\helpers\Html;
/*use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
//use yii\bootstrap\modal;
use \app\models\ControlProcesos;
use yii\bootstrap\Modal;

$this->title = 'Ver la Valoración';

    $variantes = $nameVal;
	$varidid = $txtId;

    $varNombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $variantes")->queryScalar();
    $varFecha = Yii::$app->db->createCommand("select fechacreacion from tbl_control_procesos where anulado = 0 and id = $txtId")->queryScalar();
    $varIdusua = Yii::$app->db->createCommand("select evaluados_id from tbl_control_procesos where anulado = 0 and id = $txtId")->queryScalar();
    $varCantidad = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $varIdusua and fechacreacion = '$varFecha'")->queryScalar();
    $varPorcentaje = Yii::$app->db->createCommand("select Dedic_valora from tbl_control_procesos where anulado = 0 and id = $txtId")->queryScalar();
    $varCorte = Yii::$app->db->createCommand("select tipo_corte from tbl_control_procesos where anulado = 0 and id = $txtId")->queryScalar();

    $mesactual = date("m");
    $fechaComoEntero = strtotime($varFecha);
    $mes = date("m", $fechaComoEntero);
?>
<style type="text/css">
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
        background-image: url('../../images/Equipo-de-Trabajo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
<div class="capaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><i class="fas fa-address-card" style="font-size: 20px; color: #2CA5FF;"></i> Informacion del plan: </label>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Nombre del técnico: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $varNombre; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Cantidad de valoración: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $varCantidad; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Porcentaje de dedicación: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $varPorcentaje.'%'; ?></label>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Corte seleccionado: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $varCorte; ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="capaDos" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><i class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></i> Acciones: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            
                            <label style="font-size: 15px;"><i class="fas fa-at" style="font-size: 15px; color: #FFC72C;"></i> Enviar por correo: </label> 
                            <?= Html::button('Enviar', ['value' => url::to(['gestionenviovaloracion','txtiddelevaluado'=>$varIdusua, 'txtId'=>$varidid]), 'class' => 'btn btn-success', 'id'=>'modalButton5',
                                'data-toggle' => 'tooltip',                
                                'title' => 'Enviar', 
                                'style' => 'background-color: #337ab7']) 
                            ?> 
                            <?php
                                Modal::begin([
                                    'header' => '<h4>Enviar la Valoracion</h4>',
                                    'id' => 'modal5',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent5'></div>";
                        
                                Modal::end(); 
                            ?>
                            
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></i> Regresar: </label> 
                            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-link" style="font-size: 15px; color: #FFC72C;"></i> Reporte del plan de valoracion: </label> 
                            <div onclick="goesit();" class="btn btn-primary" style="display:inline;" method='post' id="botones1" >
                                Ir a reporte
                            </div> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="capaTres" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-address-book" style="font-size: 20px; color: #B833FF;"></i> Listado del plan: </label>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    //'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'PCRC',
                            'value' => 'arboles.name',
                        ],
                        [
                            'attribute' => 'Dimensiones',
                            'value' => 'dimensions',
                        ],
                        [
                            'attribute' => 'Cantidad de Valoraciones',
                            'value' => 'cant_valor',
                        ],
                        [
                            'attribute' => 'Justificación',
                            'value' => 'argumentos',
                        ],
                    ],
                ]); 
                ?>
            </div>
        </div>
    </div>
</div>
<hr>
<script type="text/javascript">
    var varmesactual = "<?php echo $mesactual; ?>";
    var varmes = "<?php echo $mes; ?>";
    var varenvioid = document.getElementById("modalButton5");

    if (varmes != varmesactual) {
        varenvioid.style.display = 'none';
    }else{
        varenvioid.style.display = 'inline';
    }

    function goesit(){
        window.open('../planvaloracion/index','_blank');
    };
</script>