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

?>
<style type="text/css">
	.cardvideo {
            height: 300px;
            width: 568px;
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
            text-align: center;    
    }
</style>
<div class="row">
    <div id="dtbloque3" class="col-sm-12" style="display: inline"> 
    	<div class="cardvideo mb">
	    	<video class="col-sm-12" height="300" controls>
                <?php   if ($vartvideo == 1) { ?>
    	  			<source src="<?= Url::to("@web/VOC_m.mp4"); ?>" type="video/mp4">
    	  			<source src="<?= Url::to("@web/VOC_m.ogv"); ?>" type="video/ogg">
                <?php }else{
                        if ($vartvideo == 2) { ?>
                    <source src="<?= Url::to("@web/VOE_m.mp4"); ?>" type="video/mp4">
                    <source src="<?= Url::to("@web/VOE_m.ogv"); ?>" type="video/ogg">
                <?php }else{
                        if ($vartvideo == 3) {?>
                    <source src="<?= Url::to("@web/VOUX_m.mp4"); ?>" type="video/mp4">
                    <source src="<?= Url::to("@web/VOUX_m.ogv"); ?>" type="video/ogg">
                <?php } } }?>
			</video>	
    	</div>
    </div>
</div>