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

	$this->title = 'Vista Escucha Focalizada - VOC -';
	$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

?>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card {
            height: 500px;
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

      .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Reporte-VOC2.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
      }
</style>
<link rel="stylesheet" type="text/css" href="web/../../../assets/6418f0aa/css/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="web/../../../assets/6418f0aa/css/daterangepicker-kv.css">
<script type="text/javascript" src="web/../../../assets/6418f0aa/js/moment.js"></script>
<script type="text/javascript" src="web/../../../assets/6418f0aa/js/daterangepicker.js"></script>
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
<div class="BloqueUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-paperclip" style="font-size: 20px; color: #2CA5FF;"></i> Información general: </label>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Cliente: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtNombreArbol; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Formulario VOC: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtServicio; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Nombre del agente: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtNombreValorado; ?></label>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Id externo speech: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtSpeech; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Fecha y hora: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtFechaHor; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Usuario: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtUsers; ?></label>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Extensión: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtextension; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Duración: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtDuraciones.' Segundos'; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></i> Dimensión: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtDimensiones; ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="BloqueDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-headphones" style="font-size: 20px; color: #C148D0;"></i> Escucha focalizada:</label>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Indicador: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtIndicador; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Variable: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtVariable; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Atributo de calidad: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtatributos; ?></label>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Motivos de contacto: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtMotivos; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Detalles motivo contacto: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtDetalles; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Puntos de dolor: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtPuntodDolor; ?></label>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Llamada categorizada: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtCategoria; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> % de indicador afectado: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtPorcentaje.'%'; ?></label>
                    </div>
                    <div class="col-md-4">
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Agente (Detalle de Responsabilidad): </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtAgente; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Marca (Detalle de Responsabilidad): </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtMarca; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Canal (Detalle de Responsabilidad): </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtCanal; ?></label>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Mapa de interesados 1: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtMapa1; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Mapa de interesados 2: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtMapa2; ?></label>
                    </div>
                    <div class="col-md-4">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Mapa de interesados 3: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtMapa3; ?></label>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-12">
                        <label  style="font-size: 17px;"><i class="fas fa-asterisk" style="font-size: 10px; color: #C148D0;"></i> Detalle cualitativo: </label><br>
                        <label  style="font-size: 15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?php echo $txtDetalleCuali; ?></label>
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
                            <label style="font-size: 15px;"><i class="fas fa-plus-square" style="font-size: 15px; color: #FFC72C;"></i> Descargar Archivo: </label> 
                            <?= 
                                Html::button('Descargar', ['value' => url::to(['downloadparameters','idform'=>$id
                                    ]), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Descargar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Descargar</h4>',
                                    'id' => 'modal1',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent1'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><i class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></i> Cancelar y regresar: </label> 
                            <?= Html::a('Regresar',  ['reportformvoc'], ['class' => 'btn btn-success',
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