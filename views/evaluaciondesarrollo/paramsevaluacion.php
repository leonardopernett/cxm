<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Modulo Parametrizador';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    

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

    .card2 {
            height: 170px;
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
        background-image: url('../../images/Banner_Ev_Desarrollo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
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
<?php
if($sessiones == "3205" || $sessiones == "3468" || $sessiones == "3229" || $sessiones == "2953" || $sessiones == "852" || $sessiones == "1483"|| $sessiones == "4201"|| $sessiones == "258"|| $sessiones == "4465"|| $sessiones == "6080" || $sessiones == "69"){ ?>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
		<?php if($sessiones == "3205" || $sessiones == "2911" || $sessiones == "2953" ){ ?>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Subir información: </label>
                            <?= Html::a('Actualiza Usuarios',  ['usuarios_evalua'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Actualiza Usuarios'])
                            ?>
                             
                        </div>
                    </div>
		<?php } ?>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></em> Parametrizar datos: </label>
                            <?= Html::a('Parametrizar',  ['parametrizardatos'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #337ab7',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar']) 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-check-circle" style="font-size: 15px; color: #FFC72C;"></em> Importar Usuarios: </label> 
                            <?= Html::button('Importar Usuarios', ['value' => url::to('importarusuarioseval'), 'class' => 'btn btn-success', 'id'=>'modalButton6',
                                'data-toggle' => 'tooltip',
                                'title' => 'Importar Usuarios', 'style' => 'background-color: #337ab7']) 
                            ?>  
                            <?php
                                Modal::begin([
                                    'header' => '<h4>Importar Usuarios </h4>',
                                    'id' => 'modal6',
                                    ]);

                                    echo "<div id='modalContent6'></div>";
                                                                                
                                Modal::end(); 
                            ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
</div>
<?php } ?>
<hr>
<?php if ($sessiones == 2953 || $sessiones == 2911 ||  $sessiones == 3205 || $sessiones == 3468 || $sessiones == 69) { ?>
<div class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-wrench" style="font-size: 20px; color: #15aabf;"></em> Configuraciones Generales: </label>
                <div class="row">
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'inputOptions' => ['autocomplete' => 'off']
                      ]
                    ]); ?> 
                	<?= $form->field($model, 'fechacreacion')->textInput(['maxlength' => 250, 'class'=>'hidden', 'id'=>'IdFechaN', 'value'=> date("Y-m-d")]) ?>
                	<div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-wrench" style="font-size: 15px; color: #15aabf;"></em> Generar BD: </label>                           

	                            <?= Html::submitButton(Yii::t('app', 'Generar'),
						                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
						                'data-toggle' => 'tooltip',
						                'title' => 'Generar',
						                'id'=>'ButtonSearch']) 
								?>
							
                        </div>
                	</div>
                	<div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-wrench" style="font-size: 15px; color: #15aabf;"></em> Actualizar BD: </label>                           

	                            <?= Html::a('Actualizar',  ['actualizarbdparams'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #337ab7',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Actualizar']) 
                            	?>
							
                        </div>
                	</div>
                <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<?php  } ?>