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
use yii\db\Query;

$sesiones =Yii::$app->user->identity->id;   
//  Aqui se genera un cambio con el nuevo Escucha Focalizada
?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css" >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
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
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/GestionBT.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<div class="capaInfo" id="idcapaInfo" style="display: inline;">
	
	<div class="row">
        <div class="col-md-8">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Ficha Técnica') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
    	<div class="col-md-4">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Usuario Red') ?></label>
    			<label style="text-align: center;"><?php echo $varLoginId; ?></label>
    		</div>	
    	</div>

    	<div class="col-md-4">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Fecha Real') ?></label>
    			<label style="text-align: center;"><?php echo $varfechareal; ?></label>
    		</div>	
    	</div>

    	<div class="col-md-4">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Id Interacción') ?></label>
    			<label style="text-align: center;"><?php echo $varCallid; ?></label>
    		</div>	
    	</div>
    </div>

    <br>

    <div class="row">
    	<div class="col-md-6">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Asesor') ?></label>
    			<label style="text-align: center;"><?php echo $varNombreAsesor; ?></label>
    		</div>	
    	</div>

    	<div class="col-md-6">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Lider de Equipo') ?></label>
    			<label style="text-align: center;"><?php echo $varNombreLider; ?></label>
    		</div>	
    	</div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-8">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Procesos Encuestas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
    	<div class="col-md-3">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Id Encuesta') ?></label>
    			<label style="text-align: center; font-size: 10px;"><?php echo $varencuestaid; ?></label>
    		</div>	
    	</div>

    	<div class="col-md-9">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Url del Buzón') ?></label>
    			<label style="text-align: center; font-size: 10px;"><?php echo $varbuzones; ?></label>
    		</div>	
    	</div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-8">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Procesos de Transcripción') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Valencia Emocional') ?></label>
                <textarea type="text" class="form-control" readonly="readonly" id="txtvalencia" rows="3" value="<?php echo $varvalencia; ?>" data-toggle="tooltip" title="Observaciones"><?php echo $varvalencia; ?></textarea>
            </div>  
        </div>

        <div class="col-md-9">
            <div class="card1 mb">
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Transcripción') ?></label>
                <textarea type="text" class="form-control" readonly="readonly" id="txttranscipcion" rows="3" value="<?php echo $vartexto; ?>" data-toggle="tooltip" title="Observaciones"><?php echo $vartexto; ?></textarea>
            </div>  
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-8">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Procesamiento de Datos - Resultados') ?></label>
            </div>
        </div>
    </div>

    <br>

    <?php
        if ($varPecProceso != 0) {
    ?>
    <div class="row">
    	<div class="col-md-3">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Automatico Agente') ?></label>
    			<label style="text-align: center;"><?php echo $varResultadosIDA.' %'; ?></label>
    		</div>	
    	</div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Automatico Pec') ?></label>
                <label style="text-align: center;"><?php echo $varResultPec.' %'; ?></label>
            </div>  
        </div>

    	<div class="col-md-3">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Calidad  y Consistencia') ?></label>
    			<label style="text-align: center;"><?php echo $varScoreValoracion.' %'; ?></label>
    		</div>	
    	</div>

    	<div class="col-md-3">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Score') ?></label>
    			<label style="text-align: center;"><?php echo $varPromedioScore; ?></label>
    		</div>	
    	</div>
    </div>
    <?php
        }else{
    ?>
    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Automatico Agente') ?></label>
                <label style="text-align: center;"><?php echo $varResultadosIDA; ?></label>
            </div>  
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Calidad  y Consistencia') ?></label>
                <label style="text-align: center;"><?php echo $varScoreValoracion; ?></label>
            </div>  
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Score') ?></label>
                <label style="text-align: center;"><?php echo $varPromedioScore; ?></label>
            </div>  
        </div>
    </div>
    <?php
        }
    ?>

    <hr>

</div>