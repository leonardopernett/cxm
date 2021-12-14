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

$this->title = 'Instrumento escucha focalizada - VOC -';
$this->params['breadcrumbs'][] = $this->title;
    
    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $valor = null;

?>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card {
            height: 500px;
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
            font-family: "Nunito"sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Inst.-Escucha-Focalizada.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="capaUno">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-id-card" style="font-size: 20px; color: #827DF9;"></em> </label>
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;">Seleccionar cliente: </label>
                        <?=  $form->field($model, 'programacategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                        [
                                            'prompt'=>'Seleccione el cliente...',
                                            'onchange' => '
                                                $.post(
                                                    "' . Url::toRoute('formulariovoc/listarpcrcindex') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#requester").html(res);
                                                    }
                                                );
                                            ',

                                        ]
                            )->label(''); 
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;">Seleccionar servicio: </label>
                        <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccione el servicio...',
                                            'id' => 'requester'
                                        ]
                                    )->label('');
                        ?>
                    </div>
                </div><br>
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;">Seleccionar valorado: </label>
                        <?=
                            $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label(Yii::t('app',''))
                                  ->widget(Select2::classname(), [
                                      //'data' => array_merge(["" => ""], $data),
                                      'language' => 'es',
                                      'options' => ['id'=>"tecnicosid",'placeholder' => Yii::t('app', 'Seleccionar valorado...')],
                                      'pluginOptions' => [
                                          'allowClear' => true,
                                          'minimumInputLength' => 4,
                                          'ajax' => [
                                              'url' => \yii\helpers\Url::to(['controlvoc/evaluadolistmultiple']),
                                              'dataType' => 'json',
                                              'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                              'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                          ],
                                          'initSelection' => new JsExpression('function (element, callback) {
                                              var id=$(element).val();
                                              if (id !== "") {
                                                  $.ajax("' . Url::to(['controlvoc/evaluadolistmultiple']) . '?id=" + id, {
                                                      dataType: "json",
                                                      type: "post"
                                                  }).done(function(data) { callback(data.results);});
                                              }
                                          }')
                                      ]
                                ]
                            );
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"></label>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::submitButton(Yii::t('app', 'Realizar valoración'),
                                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                            'data-toggle' => 'tooltip',
                                            'onclick' => 'varVerificar();',
                                            'title' => 'Realizar valoración']) 
                                    ?> 
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Nueva consulta',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Nueva Consulta']) 
                                    ?>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Ir al reporte',  ['reportformvoc'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Ir al reporte']) 
                                    ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div><br>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
</div>
<hr>
<?php if($sessiones == "2953" || $sessiones == "7"){ ?>
<div class="capaDos">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-object-group" style="font-size: 20px; color: #FFC72C;"></em> </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb"> 
                            <label style="font-size: 15px;"><em class="fas fa-plus-square" style="font-size: 15px; color: #FFC72C;"></em> Guardar Acciones: </label> 
                            <?= 
                                Html::button('Agregar', ['value' => url::to(['crearacciones']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Agregar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Agregar Acciones</h4>',
                                    'id' => 'modal1',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent1'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb"> 
                            <label style="font-size: 15px;"><em class="fas fa-pen-square" style="font-size: 15px; color: #FFC72C;"></em> Modificar Acciones: </label> 
                            <div onclick="savedetalles();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                Modificar
                            </div> 
                        </div>
                    </div>                    
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<?php } ?>
<script type="text/javascript">
    function varVerificar(){
        var varcliente = document.getElementById("speechcategorias-programacategoria").value;
        var varservicio = document.getElementById("requester").value;
        var varvalorado = document.getElementById("tecnicosid").value;

        if (varcliente == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar el cliente.","warning");
            return;
        }else{
            if (varservicio == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe seleccionar el servicio.","warning");
                return;
            }else{
                if (varvalorado == "") {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","Debe seleccionar el valorado.","warning");
                    return;
                }
            }
        }

    };
</script>