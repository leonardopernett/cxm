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

$varEscuchar = (new \yii\db\Query())
            ->select(['servicio'])
            ->from(['tbl_dashboardspeechcalls'])
            ->where(['=','anulado',0])
            ->andwhere(['=','callid',$varidcallids])
            ->andwhere(['=','fechareal',$varvarfechareal])
            ->andwhere(['=','login_id',$varidlogin])
            ->groupby(['servicio'])
            ->scalar();


if ($varEscuchar == "CX_Directv") {
    $varResultado = "file:\\172.20.73.205\\store3\\".$varidcallids."_3_1.00.mp3";
}  


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

    .card3 {
            height: 90px;
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
<div class="capaInfo" id="idCapaInfo" style="display: inline;">
	
	<div class="row">
        <div class="col-md-8">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Ficha Técnica') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
    	<div class="col-md-3">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Usuario Red') ?></label>
    			<label style="text-align: center;"><?php echo $varidlogin; ?></label>
    		</div>	
    	</div>

    	<div class="col-md-3">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Redbox') ?></label>
    			<label style="text-align: center;"><?php echo $varidredbox; ?></label>
    		</div>	
    	</div>

    	<div class="col-md-3">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Grabadora') ?></label>
    			<label style="text-align: center;"><?php echo $varidgrabadora; ?></label>
    		</div>	
    	</div>

    	<div class="col-md-3">
    		<div class="card1 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Extension') ?></label>
    			<label style="text-align: center;"><?php echo $varextensionnum; ?></label>
    		</div>	
    	</div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-8">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Url Llamada E Instructivo de Uso') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
    	<div class="col-md-6">
    		<div class="card3 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Copiar Url') ?></label>
    			<div onclick="generated();" class="btn btn-primary" style="display:inline;background-color: #c5cbd0;color: black;" method='post' id="botones2" ><i class="fas fa-play-circle" style="font-size: 17px; color: #ff3838;"></i>
				    Copiar Url Llamada
				</div> 
    		</div>	
    	</div>

    	<div class="col-md-6">
    		<div class="card3 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Información') ?></label>
    			<label style="text-align: center; font-size: 12px;"><?php echo "Nota: Para escuchar la llamada, debes pegar la url en el navegador de Internet Explorer."; ?></label>
    		</div>	
    	</div>    	
    </div>

    <br>

    <div class="row">
    	<div class="col-md-6">
    		<div class="card3 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Instructivo de Uso') ?></label>
    			<a style="font-size: 15px;" rel="stylesheet" type="text/css" href="../../downloadfiles/Manual Configuración Quantify Esp Version 2_Febrero 2018.pdf" target="_blank">Descargar Instructivo</a>
    		</div>	
    	</div>

    	<div class="col-md-6">
    		<div class="card3 mb">
    			<label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Url Llamada') ?></label>
    			<input type="text" rows="2" class="js-copytextarea form-control" readonly="readonly" value="<?php echo $varResultado; ?>">
    		</div>	
    	</div>    	
    </div>

    <hr>

    <div class="row">
        <div class="col-md-8">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?= Yii::t('app', 'Procesos de Transcripcion') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Valencia Emocional') ?></label>
                <textarea type="text" class="form-control" readonly="readonly" id="txtvalencia" rows="3" value="<?php echo $varvalencia; ?>" data-toggle="tooltip" title="Observaciones"><?php echo $varvalencia; ?></textarea>
            </div>  
        </div>

        <div class="col-md-9">
            <div class="card1 mb">
                <label><em class="fas fa-info-circle" style="font-size: 20px; color: #827DF9;"></em><?= Yii::t('app', ' Transcripcion') ?></label>
                <textarea type="text" class="form-control" readonly="readonly" id="txttranscipcion" rows="3" value="<?php echo $vartexto; ?>" data-toggle="tooltip" title="Observaciones"><?php echo $vartexto; ?></textarea>
            </div>  
        </div>
    </div>

    <hr>    

</div>

<script type="text/javascript">
function generated(){
	  var copyTextarea = document.querySelector('.js-copytextarea');
	  copyTextarea.select();

	  try {
	    var successful = document.execCommand('copy');
	    var msg = successful ? 'successful' : 'unsuccessful';
	    console.log('Copying text command was ' + msg);
	  } catch (err) {
	    console.log('Oops, unable to copy');
	  }
};

</script>