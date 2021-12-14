<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\ReportesAdministracion;

$this->title = 'Administrar Reportes PBI';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Administrar Reportes PBI';

    $template = '<div class="col-md-3">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

$sessiones = Yii::$app->user->identity->id;
$txtIdPcrc = 1;
$txtareatrabajo = "";
$txtreporte= "";
$areatrab="prueba";
$listaworkspaces = json_decode(json_encode($listaworkspaces), true);

?>
 <style>
    #container_report{
      min-height:90vh;
    }
    #container_report iframe{
      min-height: 90vh;
      border: none !important;
      }
    /*.centro{
      width: 450px; display:block; margin:auto; 
    }*/
  </style>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card {
            height: 200px;
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
    background-image: url('../../images/Reporte-Power-BI.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }
</style>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        
      </div>
    </div>
  </div>
</header>
<br>
<?= Html::encode($this->title) ?>


                               <?php                          
                                    foreach ($listaworkspaces as $key => $value) {
                                       echo "<option value = '".$value['id']."'>".$value['name']."</option>";
                                    }
                                ?>
              
<?php if ($sessiones == "2953" || $sessiones == "3229" || $sessiones == "2991" || $sessiones == "4457" || $sessiones == "565" || $sessiones == "6639" || $sessiones == "6636") {?>
  
  
                <?= Html::button('Crear WorKSpace', ['value' => url::to('crearworkspace'), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Crear area de trabajo', 'style' => 'background-color: #337ab7']) 
                          ?> 

                          <?php
                            Modal::begin([
                                  'header' => '<h4>Creaci�n de WorkSpace</h4>',
                                  'id' => 'modal1',
                                  //'size' => 'modal-lg',
                                ]);

                            echo "<div id='modalContent1'></div>";
                                                      
                            Modal::end();                             
                          ?>
                
              
                          
                          <?php
                            Modal::begin([
                                  'header' => '<h4>Creacion de Reportes PBI </h4>',
                                  'id' => 'modal2',
                                  //'size' => 'modal-lg',
                                ]);

                            echo "<div id='modalContent2'></div>";
                                                      
                            Modal::end(); 
                          ?>
                
             
                <?= Html::button('Duplicar Reporte', ['value' => url::to(['duplicarreporte','nombrearea'=>$txtareatrabajo]), 'class' => 'btn btn-success', 'id'=>'modalButton5',
                              'data-toggle' => 'tooltip',
                              'title' => 'Duplicar reporte', 'style' => 'background-color: #5bc0de']) 
                          ?> 

                          <?php
                            Modal::begin([
                                  'header' => '<h4>Duplicar Reportes PBI</h4>',
                                  'id' => 'modal5',
                                  //'size' => 'modal-lg',
                                ]);

                            echo "<div id='modalContent5'></div>";
                                                      
                            Modal::end(); 
                           ?>
                
             
 <?php } ?>
<?php if ($sessiones == "2953" || $sessiones == "7" || $sessiones == "2991" || $sessiones == "3468" || $sessiones == "3229" || $sessiones == "57"  || $sessiones == "4457" || $sessiones == "565" || $sessiones == "6639" || $sessiones == "6636") {?>

          
<?php } ?>
<?php if ($sessiones == "2953" || $sessiones == "3229" || $sessiones == "2991"  || $sessiones == "4457" || $sessiones == "565" || $sessiones == "6639" || $sessiones == "6636") {?>

         
  <?php } ?>
<!-- Modal -->
<div id="modal_report" class="modal fade" role="dialog">
  <div class="modal-dialog modal-lg" style=" margin-top: 11px !important; width: 98% !important; margin-bottom: 0px;">
    <!-- Modal content-->
    <div class="modal-content" style = "">
      <div class="modal-header" style = "padding-top: 4px !important; padding-bottom: 9px !important;">
        <button type="button" class="close" data-dismiss="modal">&times;</button>
        <h4 class="modal-title">Reporte PBI</h4>
      </div>
      <div class="modal-body pa0" style="padding: 0px !important;" >
        <div id="container_report" style="min-height:90vh;"></div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div>

  </div>
</div>

