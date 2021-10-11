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
use app\models\SpeechCategorias; 


$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='tooltip']").tooltip(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);

$this->title = 'Dashboard Escuchar + 2.0';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Dashboard Escuchar + 2.0';

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();


    $txtdsfuente = $txtConjuntoSpeech;
?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css" >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card {
            height: 80px;
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
            font-family: "Nunito";
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
            font-family: "Nunito";
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Dashboard-Escuchar-+.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br>
<br>
<div class="capaInfo" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
          <div class="card mb">
            <label><i class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></i> Servicio:</label>
            <label style="font-size: 15px; text-align: center;"><?php echo $txtvarservisioname; ?></label>
          </div>
        </div>
        <div class="col-md-6">
          <div class="card mb">
            <label><i class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></i> Programa Pcrc:</label>
            <label style="font-size: 15px; text-align: center;"><?php echo $txtvarcodpcrc; ?></label>
          </div>
        </div>        
  </div>
</div>
<hr>
<div class="capapp" style="display: inline;">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'action' => \yii\helpers\Url::to(['guardarpaso2'])]); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-bookmark" style="font-size: 20px; color: #559FFF;"></i> Selección de datos:</label>
                <div class="row">
                    <div class="col-md-6">
                        <label><?= Yii::t('app', '* Programa/PCRC') ?></label>
                        <?=
                            $form->field($modelA, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                ->widget(Select2::classname(), [
                                    //'data' => array_merge(["" => ""], $data),
                                    'language' => 'es',
                                    'options' => ['id'=>'idvararbol', 'placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'allowClear' => false,
                                        'minimumInputLength' => 3,
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['getarbolesbyroles']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                    //'initSelection' => new JsExpression($initScript)
                                    ]
                            ])->label('');
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label><?= Yii::t('app', '* Dimensión') ?></label>
                        <?=
                            $form->field($modelD, 'dimension_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($modelD->getDimensionsList(), ['id'=>'idvardimension', 'prompt' => 'Seleccione ...'])->label('');
                        ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <label><?= Yii::t('app', '* Valorado') ?></label>
                        <?= $form->field($modelE, 'name', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 250, 'readonly'=>true, 'id'=>'idname', 'value' => $txtEvaluado])->label('') ?>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-12">
                                <div class="card1 mb">                                                                      
                                    <?=
                                        Html::submitButton(Yii::t('app', 'Realizar Valoración'), ['onclick' => 'verificarvar();', 'class' => 'btn btn-success'])
                                    ?>   
                                </div>
                            </div>
                            <div class="col-md-6">
                                <?= Html::input("hidden", "evaluado_id", $txtEvaluadoid); ?>
                                <?= Html::input("hidden", "dsfuente_encuesta", $txtConjuntoSpeech); ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <hr>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    function verificarvar(){
        var vararbol = document.getElementById("idvararbol").value;
        var varidvardimension = document.getElementById("idvardimension").value;

        if (vararbol == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debes seleccionar un programa/pcrc","warning");
            return;
        }else{
            if (varidvardimension == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debes seleccionar la dimension","warning");
                return;
            }
        }
    };
</script>