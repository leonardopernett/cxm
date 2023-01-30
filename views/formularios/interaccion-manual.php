<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;

/* @var $this yii\web\View */
/* @var $model app\models\Formularios */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Valoración Manual - Realizar Monitoreo';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

?>

<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {    
    echo '<div class="alert alert-' . $key . '" role="alert">' . $message . '</div>';
}
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

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Valorar_Interaccion.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }
</style>
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

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<div class="capaPrincipal" id="idcapaPrincipal" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Información') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-star" style="font-size: 25px; color: #ffc034;"></em><?= Yii::t('app', ' Recuerda que para Valorar las dimensiones de OJT y Calidad del Entrenamiento, lo debes hacer solo con el formulario -> Indice de Calidad Entrenamiento Inicial ') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Guia de Inspiración') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', 'Seleccionar Programa/PCRC...') ?></label>
                        <?=
                            $form->field($modelA, 'arbol_id')
                                ->widget(Select2::classname(), [
                                    //'data' => array_merge(["" => ""], $data),
                                    'language' => 'es',
                                    'options' => ['id'=>'idvarArbol','placeholder' => Yii::t('app', 'Select ...')],
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
                                        ]
                            )->label('');
                        ?>
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', 'Seleccionar Dimensión...') ?></label>
                        <?=
                            $form->field($modelD, 'dimension_id')
                                ->dropDownList($modelD->getDimensionsList()
                                    , ['prompt' => 'Seleccione ...'])->label('')
                        ?>
                    </div>
                </div>

                <br>

                <?=
                    Html::submitButton(Yii::t('app', 'Buscar'), ['class' => 'btn btn-success','onclick'=>'varVerificar();'])
                ?>                  

            </div>
        </div>
    </div>

</div>
<?php ActiveForm::end(); ?>

<hr>

<script type="text/javascript">
    function varVerificar(){
        var vararbol = document.getElementById("idvarArbol").value;

        if (vararbol == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Se debe de seleccionar un programa/pcrc.","warning");
            return;
        }
    };
</script>