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

$this->title = 'Instrumento Escucha Focalizada - VOC -';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$varDatalista = $datalista;

?>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/font_awesome_local/css.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<style type="text/css">
  @import url('https://fonts.googleapis.com/css?family=Nunito');
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
        background-image: url('../../images/Reporte-VOC.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
      }
</style>
<link rel="stylesheet" type="text/css" href="web/../../../assets/6418f0aa/css/daterangepicker.css">
<link rel="stylesheet" type="text/css" href="web/../../../assets/6418f0aa/css/daterangepicker-kv.css">
<script type="text/javascript" src="web/../../../assets/6418f0aa/js/moment.js"></script>
<script type="text/javascript" src="web/../../../assets/6418f0aa/js/daterangepicker.js"></script>
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
<div class="CeroBloque" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="PrimerBloque" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><i class="fas fa-search" style="font-size: 20px; color: #559FFF;"></i> Filtro general:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-size: 15px;">Seleccionar cliente: </label>
                            <?=  $form->field($model, 'idpcrcspeech', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
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
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-size: 15px;">Seleccionar valorador: </label>
                            <?=
                                $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label(Yii::t('app',''))
                                      ->widget(Select2::classname(), [
                                          //'data' => array_merge(["" => ""], $data),
                                          'language' => 'es',
                                          'options' => ['id'=>"valoradorid",'placeholder' => Yii::t('app', 'Seleccionar valorador...')],
                                          'pluginOptions' => [
                                              'allowClear' => true,
                                              'minimumInputLength' => 4,
                                              'ajax' => [
                                                  'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                                  'dataType' => 'json',
                                                  'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                  'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                              ],
                                              'initSelection' => new JsExpression('function (element, callback) {
                                                  var id=$(element).val();
                                                  if (id !== "") {
                                                      $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
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
                            <label style="font-size: 15px;">Seleccionar valorado: </label>
                            <?=
                                $form->field($model, 'idvalorado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label(Yii::t('app',''))
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
                    </div>
		    <div class="row">
                            <div class="col-md-6">
                                <label for="txtCiudad" style="font-size: 14px;">Fecha de registro</label>
                                <?=
                                    $form->field($model, 'fechahora', [
                                        'labelOptions' => ['class' => 'col-md-12'],
                                        'template' => '<div class="col-md-6">{label}</div>'
                                        . '<div class="col-md-12"><div class="input-group">'
                                        . '<span class="input-group-addon" id="basic-addon1">'
                                        . '<i class="glyphicon glyphicon-calendar"></i>'
                                        . '</span>{input}</div>{error}{hint}</div>',
                                        'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                        'options' => ['class' => 'drp-container form-group']
                                    ])->widget(DateRangePicker::classname(), [
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'right',
                                 ]]);
                                ?>
                            </div>    
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-size: 15px;"></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Buscar']) 
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <?= Html::a('Nueva consulta',  ['reportformvoc'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Nueva Consulta']) 
                                        ?>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="card1 mb">
                                        <?= Html::a('Ir al formulario',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #337ab7',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Ir al formulario']) 
                                    ?>
                                    </div>
                                </div>                                
                            </div>
                        </div>
			<div class="col-md-6">
                            <label style="font-size: 15px;"></label>
                            <div class="row">
                                <div class="col-md-4">
                                    <div class="card1 mb">
					
                                        <?= 
                                            Html::button('Descargar Archivo', ['value' => url::to(['downloadlist','datos'=>$varDatalista
                                                ]), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Descargar'                                        
                                                ])
                                        ?>
                                        <?php
                                            Modal::begin([
                                                'header' => '<h4>Procesando datos en el archivo de excel...</h4>',
                                                'id' => 'modal1',
                                                //'size' => 'modal-lg',
                                            ]);

                                            echo "<div id='modalContent1'></div>";
                                                                    
                                            Modal::end(); 
                                        ?> 
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div class="SegundoBloque" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><i class="fas fa-list-alt" style="font-size: 20px; color: #B833FF;"></i> Listado general:</label>
                     <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            [
                                'attribute' => 'Id',
                                'value' => 'idformvocbloque1',
                            ],
                            [
                                'attribute' => 'Fecha',
                                'value' => 'fechacreacion',
                            ],
                            [
                                'attribute' => 'Nombre del valorador',
                                'value' => function($data){
                                        return $data->getformvoc1($data->usua_id);
                                }
                            ],
                            [
                                'attribute' => 'Nombre del valorado',
                                'value' => function($data){
                                        return $data->getformvoc2($data->idvalorado);
                                }
                            ],
                            [
                                'attribute' => 'Documento del valorado',
                                'value' => function($data){
                                        return $data->getformvoc3($data->idvalorado);
                                }
                            ],
                            [
                                'attribute' => 'Servicio',
                                'value' => function($data){
                                        return $data->getformvoc4($data->idpcrccxm);
                                }
                            ],                            
                            [
                                'attribute' => 'Fomulario VOC',
                                'value' => function($data){
                                        return $data->getformvoc5($data->idformvocbloque1);
                                }
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'headerOptions' => ['style' => 'color:#337ab7'],
                                'template' => '{view}',
                                'buttons' => 
                                [
                                    'view' => function ($url, $model) {                        
                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['formlistavoc', 'id' => $model->idformvocbloque1], [
                                            'class' => '',
                                            'title' => 'Ver',
                                            'data' => [
                                                'method' => 'post',
                                            ],
                                        ]);  
                                    }
                                ]            
                            ],
                        ],
                     ]);?>
                </div>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
<div>
<hr>
</div>
