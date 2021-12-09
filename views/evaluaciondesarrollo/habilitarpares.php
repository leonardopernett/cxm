 <?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de desarrollo';
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
            height: 150px;
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
</style>
<script src="../../js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
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
<div class="capaUno" style="display: inline;">
<?php $form = ActiveForm::begin([
  'layout' => 'horizontal',
  'fieldConfig' => [
    'inputOptions' => ['autocomplete' => 'off']
  ]
  ]); ?>
	<div class="row">
		<div class="col-md-4">
			<div class="card2 mb">
				<label style="font-size: 15px;"><em class="fas fa-user-circle" style="font-size: 15px; color: #827DF9;"></em> Buscar usuario: </label>
				<?= $form->field($model, 'documento')->textInput(['maxlength' => 250,  'id'=>'IdcambiosNcargo', 'placeholder' => 'Digite documento de la persona']) ?>
				<?= Html::submitButton(Yii::t('app', 'Buscar'),
                                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Buscar documento',
                                    'id'=>'ButtonSearch',
                                    'style' => 'display: inline']) 
                    ?>
			</div>
		</div>
		<div class="col-md-4">
			<div class="card2 mb">
				<label style="font-size: 15px;"><em class="fas fa-key" style="font-size: 15px; color: #C148D0;"></em> Habilitar evaluación: </label><br>
				<label style="font-size: 15px;"><?php echo $varname; ?></label>
				<div onclick="habilitarc();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="idtbnsavec" >
                	Habilitar
                </div> 
			</div>
		</div>		
		<div class="col-md-4">
			<div class="card2 mb">
                <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Cancelar y regresar: </label> <br><br>
                <?= Html::a('Regresar',  ['evaluaciondesarrollo/gestionnovedades'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>                            
            </div>
		</div>
	</div>
<?php ActiveForm::end(); ?>
</div>
<hr>

<script type="text/javascript">
	function habilitarc(){
		var vardocumento = "<?php echo $vardocumento; ?>";

		$.ajax({
                method: "get",
                url: "habilitarusuario",
                data: {
                    txtvardocumento : vardocumento,
                },
                success : function(response){
                    numRta =   JSON.parse(response);
                    window.open('../evaluaciondesarrollo/habilitarpares','_self');

                }
        });
	};
</script>