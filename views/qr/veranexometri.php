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
use app\models\Hojavidainforme;
use app\models\Hojavidaperiocidad;
use app\models\Hojavidametricas;

$this->title = 'Gestor de PQRS - Ver Imagen';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    $sesiones =Yii::$app->user->identity->id;

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<style>
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
    .img-container-inline {
        text-align: center;
        display: block;}

</style>
<!-- Capa Proceso -->
<div id="capaIdProceso" class="capaProceso" style="display: inline;">

    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            "method" => "post",
            "enableClientValidation" => true,
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
        ]) 
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">            
            <?php
             ?>
             <h4>Documento Anexo de Informe</h4>
             <?php
            
            $validar = substr($varRuta,-3);
           //var_dump($varRuta);
            if ($validar == 'png' || $validar == 'jpg' || $validar == 'bmpg') {  
              // var_dump($varRuta);
                //echo '<img src="'.Yii::$app->getUrlManager()->getBaseUrl().''.'/'.''.$varRuta.'" width="300" height="300" class="img-container-inline" alt="">';
                echo '<div class="img-container-inline"><img src="'.Yii::$app->getUrlManager()->getBaseUrl().''.'/'.''.$varRuta.'" width="300" height="300"></div>'; 
            } else {
               // var_dump($varRuta);
           ?>
           <a style="font-size: 13px;" rel="stylesheet" type="text/css" href="<?php echo Yii::$app->getUrlManager()->getBaseUrl().'/'.$varRuta;?>" target="_blank">Descargar Documento</a>          
           
           <?php
            }
           ?>
            </div>      
        </div>
        
    </div>
    <br>
    
    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
          return true;
        }
                
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    };

    
</script>