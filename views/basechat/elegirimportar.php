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

$this->title = 'Gestión Satisfacción Chat';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    $sesiones =Yii::$app->user->identity->id;

    

?>
<div class="CapaCero" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
            <label for="txtmedio" style="font-size: 14px;"> Tipo Cargue de Base de Medallia</label>
                <select id="txtmedio" class ='form-control'  onchange="accion()">
                          <option value="" disabled selected>seleccione...</option>
                          <option value="Colombia">Tigo Colombia</option>
                          <option value="Bolivia">Tigo Bolivia</option>
                </select>                 
            </div>            
        </div>
        
    </div>
</div> 
<hr>
<div class="CapaPP" id="tablebol" style="display: none"> 
    <div class="row">
        <div class="col-md-6" >
            <div class="card1 mb">
                <label style="font-size: 15px;"><i class="fas fa-upload" style="font-size: 15px; color: #827DF9;"></i> Importar base pura: </label> 
                <?= Html::a('Importar',  ['importarexcel'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #337ab7',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Importar Archivo'])
                ?>
            </div>            
        </div>
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><i class="fas fa-upload" style="font-size: 15px; color: #827DF9;"></i> Importar base imputabilidad: </label> 
                <?= Html::a('Importar',  ['importarexceltwo'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #337ab7',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Importar Archivo'])
                ?>
            </div>
             
        </div>
    </div>    
</div>
<div class="CapaP2" id="tablecol" style="display: none"> 
    <div class="row">
        <div class="col-md-12" >
            <div class="card1 mb">
                <label style="font-size: 15px;"><i class="fas fa-upload" style="font-size: 15px; color: #827DF9;"></i> Importar base: </label> 
                <?= Html::a('Importar',  ['importarexcelcol'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #337ab7',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Importar Archivo'])
                ?>
            </div>            
        </div>
    </div>    
</div>        
<script type="text/javascript">
function accion(){
    var varRta = document.getElementById("txtmedio").value;
    
    var varPartT = document.getElementById("tablebol");
    var varPartT2 = document.getElementById("tablecol");
    if (varRta == "Bolivia") {
      varPartT.style.display = 'inline';
      varPartT2.style.display = 'none';      
    }else{
      varPartT.style.display = 'none';
      varPartT2.style.display = 'inline';
    }    
  };

  </script>