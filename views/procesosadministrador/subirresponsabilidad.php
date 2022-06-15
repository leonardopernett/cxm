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

$this->title = 'Procesos Administrador - Procesos Responsabilidades Formularios Manual';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    
    $sesiones =Yii::$app->user->identity->id;    

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


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 80px;
      height: 80px;
      -webkit-animation: spin 2s linear infinite; /* Safari */
      animation: spin 2s linear infinite;
    }

    /* Safari */
    @-webkit-keyframes spin {
      0% { -webkit-transform: rotate(0deg); }
      100% { -webkit-transform: rotate(360deg); }
    }

    @keyframes spin {
      0% { transform: rotate(0deg); }
      100% { transform: rotate(360deg); }
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<div class="capaPrincipal" style="display: inline;">
    
    <?php 
        $form = ActiveForm::begin([
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
                <div class="card1 mb ">
                    <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #ffc034;"></em> Subir Archivo...</label>
                    <?= $form->field($model, "file[]", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->fileInput(['id'=>'idinput','multiple' => false])->label("") ?>
                    <br>
                    <?= Html::submitButton("Subir", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>
                </div>
            </div>
        </div>
    <?php ActiveForm::end() ?>
</div>

<div class="capaOne" id="capaOneID" style="display: none;">
	<div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em> Procesando importación de archivos</label>
                <div class="col-md-12">
                    <table>
                    <caption>Guardando...</caption>
                        <tr>
                            <th scope="col" class="text-center"><div class="loader"> </div></th>
                            <th scope="col" class="text-center"><label><?= Yii::t('app', ' Guardando datos del archivo...') ?></label></th>
                        </tr>
                    </table>                                       
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<script type="text/javascript">
	function cargar(){
		var varcapaIniID = document.getElementById("capaIniID");
		var varcapaOneID = document.getElementById("capaOneID");
        var varidinput = document.getElementById("idinput").value;

        if(varidinput === '')
        {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No se ha seleccionado ningún archivo.","warning");
            return;
        }
        
		varcapaIniID.style.display = 'none';
		varcapaOneID.style.display = 'inline';
	};
</script>