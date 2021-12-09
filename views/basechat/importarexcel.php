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

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
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
<script src="../../js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<div class="capaIni" id="capaIniID" style="display: inline;">
	<?php $form = ActiveForm::begin([
        "method" => "post",
        "enableClientValidation" => true,
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]) ?>
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-upload" style="font-size: 20px; color: #FFC72C;"></em> Importar archivos</label>
                    <div class="row">                        
                        <div class="col-md-6">
                            <?= $form->field($model, "file[]")->fileInput(['multiple' => false]) ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-4">
							<div class="card1 mb">
                            	<?= Html::submitButton("Subir", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                        	<div class="card1 mb">
                            	<?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
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

		varcapaIniID.style.display = 'none';
		varcapaOneID.style.display = 'inline';
	};
</script>