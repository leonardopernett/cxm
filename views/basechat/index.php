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

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="../../web/js_exporting/jquery-2.1.1.min.js"></script>
<script src="../../web/js_exporting/highcharts/highcharts.js"></script>
<script src="../../web/js_exporting/highcharts/exporting.js"></script>
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
<div id="CapaIniid" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="CapaUno" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-id-card" style="font-size: 20px; color: #827DF9;"></em> Acciones de búsqueda</label>
                    <div class="row">
                        <div class="col-md-4">
                            <label style="font-size: 15px;">Seleccionar rango de fecha de respuesta: </label>
                            <?=
                                $form->field($model, 'fecha_respuesta', [
                                    'labelOptions' => ['class' => 'col-md-12'],
                                    'template' => '<div class="col-md-6">{label}</div>'
                                    . '<div class="col-md-12"><div class="input-group">'
                                    . '<span class="input-group-addon" id="basic-addon1">'
                                    . '<i class="glyphicon glyphicon-calendar"></i>'
                                    . '</span>{input}</div>{error}{hint}</div>',
                                    'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                    'options' => ['class' => 'drp-container form-group']
                                ])->label('')->widget(DateRangePicker::classname(), [
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
                        <div class="col-md-4">
                            <label style="font-size: 15px;">Seleccionar programa/pcrc: </label>
                            <?=
                                    $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label('')->widget(Select2::classname(), [
                                                    'id' => 'ButtonSelect',
                                                    'name' => 'BtnSelectes',
                                                    'attribute' => 'Valorador',
                                                    'language' => 'es',
                                                    'options' => ['placeholder' => Yii::t('app', 'Seleccionar programa/pcrc...')],
                                                    'pluginOptions' => [
                                                        'allowClear' => true,
                                                        'minimumInputLength' => 4,
                                                        'ajax' => [
                                                            'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                                                            'dataType' => 'json',
                                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                                        ],
                                                        'initSelection' => new JsExpression('function (element, callback) {
                                                                    var id=$(element).val();
                                                                    if (id !== "") {
                                                                        $.ajax("' . Url::to(['basesatisfaccion/getarbolesbypcrc']) . '?id=" + id, {
                                                                            dataType: "json",
                                                                            type: "post"
                                                                        }).done(function(data) { callback(data.results[0]);});
                                                                    }
                                                                }')
                                                    ]
                                                        ]
                                    );
                            ?> 
                        </div>
                        <div class="col-md-4">
                            <label style="font-size: 15px;">Seleccionar valorado: </label>
                            <?=  $form->field($model, 'id_agente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\BasechatTigo::find()->distinct()->where("anulado = 0")->orderBy(['id_agente'=> SORT_ASC])->all(), 'id_agente', 'id_agente'),
                                        [
                                            'prompt'=>'Seleccione valorado...',
                                        ]
                                )->label(''); 
                            ?>
                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label style="font-size: 15px;">Seleccionar tipologia: </label>
                            <?=  $form->field($model, 'tipologia', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\BasechatTigo::find()->distinct()->where("anulado = 0")->orderBy(['tipologia'=> SORT_ASC])->all(), 'tipologia', 'tipologia'),
                                        [
                                            'prompt'=>'Seleccione tipologia...',
                                        ]
                                )->label(''); 
                            ?>
                            
                        </div>
                        <div class="col-md-4">
                            <label style="font-size: 15px;">Seleccionar estado: </label>
                            <?=  $form->field($model, 'estado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\BasechatTigo::find()->distinct()->where("anulado = 0")->orderBy(['estado'=> SORT_ASC])->all(), 'estado', 'estado'),
                                        [
                                            'prompt'=>'Seleccione estado...',
                                        ]
                                )->label(''); 
                            ?>
                            
                        </div>
                        <div class="col-md-4">
                            <label style="font-size: 15px;">Ingresar id encuesta: </label>
                            <?= $form->field($model, 'idencuesta')->textInput(['maxlength' => 250, 'placeholder' => 'Ingresar el id de la encuesta', 'id'=>'idvarencuesta']) ?>                            
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4">
                            <label style="font-size: 15px;">Seleccionar imputabilidad: </label>
                            <?=  $form->field($model, 'imputable', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\BasechatTigo::find()->distinct()->where("anulado = 0")->orderBy(['imputable'=> SORT_ASC])->all(), 'imputable', 'imputable'),
                                        [
                                            'prompt'=>'Seleccione imputabilidad...',
                                        ]
                                )->label(''); 
                            ?>
                            
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #827DF9;"></em> Buscar acciones: </label> 
                                <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                'data-toggle' => 'tooltip',
                                                'style' => 'background-color: #4298B4',
                                                'title' => 'Buscar acciones']) 
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #827DF9;"></em> Importar archivo: </label> 
                                
                                <?= Html::button('Importar', ['value' => url::to(['elegirimportar']), 'class' => 'btn btn-success', 'id'=>'modalButton5', 'data-toggle' => 'tooltip', 'title' => 'Importar Archivo', 'style' => 'background-color: #337ab7']) 
                                ?> 

                                <?php
                                    Modal::begin([
                                        'header' => '<h4></h4>',
                                        'id' => 'modal5',
                                        //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent5'></div>";
                                                                              
                                    Modal::end(); 
                                ?>                                 
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-globe" style="font-size: 15px; color: #827DF9;"></em> Buscar General: </label> 
                                <?= Html::a('Buscar',  ['index'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #707372',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Buscar General'])
                                ?>                               
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #827DF9;"></em> Descargar gestión: </label>                                 
                                <?= Html::button('Descargar', ['value' => url::to(['descargargestion']), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7']) 
                                ?> 

                                <?php
                                    Modal::begin([
                                        'header' => '<h4></h4>',
                                        'id' => 'modal3',
                                        //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent3'></div>";
                                                                              
                                    Modal::end(); 
                                ?>                               
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> 
    <hr>
    <div class="CapaDos" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-list" style="font-size: 20px; color: #2CA53F;"></em> Resultados de la búsqueda</label>
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'columns' => [
                            [
                                'attribute' => 'Id de la encuesta',
                                'value' => 'idencuesta',
                            ],
                            [
                                'attribute' => 'Fecha de respuesta',
                                'value' => 'fecha_respuesta',
                            ],
                            [
                                'attribute' => 'Tipo de canal',
                                'value' => 'tipo_canal_digital',
                            ],                            
                            [
                                'attribute' => 'Cliente',
                                'value' => function($data){
                                        return $data->getclientti($data->idbasechat_tigob);
                                }
                            ],                            
                            [
                                'attribute' => 'Programa/pcrc',
                                'value' => function($data){
                                        return $data->getpcrcti($data->idbasechat_tigob);
                                }
                            ],
                            [
                                'attribute' => 'Asesor',
                                'value' => 'email_asesor',
                            ],                            
                            [
                                'attribute' => 'Tipo',
                                'value' => function($data){
                                        return $data->getnpstipotigo($data->idbasechat_tigob);
                                }
                            ],
                            [
                                'attribute' => 'Tipología',
                                'value' => function($data){
                                        return $data->getnpstigo($data->idbasechat_tigob);
                                }
                            ],                            
                            [
                                'attribute' => 'Número Ticket',
                                'value' => 'ticked_id',
                            ],                                                         
                            [
                                'attribute' => 'FCR',
                                'value' => 'fcr',
                            ],                                                          
                            [
                                'attribute' => 'Estado Valoracion',
                                'value' => 'estado',
                            ],  
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'headerOptions' => ['style' => 'color:#337ab7'],
                                //'template' => '{view}{update}{delete}',
                                'template' => '{update}',
                                'buttons' => 
                                [
                                    'update' => function ($url, $model) {
                                                if ($model->estado != 'Cerrado') {
						    $varpcrc = $model->pcrc;
                                                    if($varpcrc == 3272) {
                                                    	return Html::a('<span class="far fa-edit" style="font-size: 20px; color: #5227d4;" ></span>'
                                                                    , Url::to(['showformulariogestion',
                                                                        'basesatisfaccion_id' => $model->basesatisfaccion_id, 'preview' => 0, 'fill_values' => false, 'banderaescalado' => false, 'aleatorio'=> false]), ['title' => Yii::t('yii', 'Gestionar Valoracion')]);
						    }


                                                }

                                            },
                                ]                              
                            ],
                             [
                                'class' => 'yii\grid\ActionColumn',
                                'headerOptions' => ['style' => 'color:#337ab7'],
                                //'template' => '{view}{update}{delete}',
                                'template' => '{update2}',
                                'buttons' => 
                                [
                                    'update2' => function ($url, $model) {
                                        $varticket = $model->ticked_id;
                                        $varbasesatis = $model->basesatisfaccion_id;
					$varpcrc = $model->pcrc;

                                        $varcomprobacion = Yii::$app->db->createCommand("select count(1) from tbl_basechat_formulario where anulado = 0 and ticked_id = $varticket and basesatisfaccion_id = $varbasesatis ")->queryScalar();
                                                if ($varcomprobacion == 0) {
                                                    if($varpcrc == 3272) {
                                                        return Html::a('<span class="far fa-sticky-note" style="font-size: 20px; color: #5227d4;" ></span>'
                                                                        , Url::to(['showbasechat',
                                                                            'basechatid' => $model->idbasechat_tigob, 'pcrc' => $model->pcrc]), ['title' => Yii::t('yii', 'Gestionar Base')]);
                                                    }
                                                    if($varpcrc == 3513) {
                                                        return Html::a('<span class="far fa-sticky-note" style="font-size: 20px; color: #5227d4;" ></span>'
                                                                        , Url::to(['showbasechatcol',
                                                                            'basechatid' => $model->idbasechat_tigob, 'pcrc' => $model->pcrc, 'idencuesta' => $model->idencuesta]), ['title' => Yii::t('yii', 'Gestionar Base')]);
                                                    }


                                                }else{
                                                    return Html::a('<span class="fas fa-eye" style="font-size: 20px; color: #4ad427;" ></span>'
                                                                    , Url::to(['showbasechatview',
                                                                        'basechatid' => $model->idbasechat_tigob]), ['title' => Yii::t('yii', 'ver Gestión')]);
                                                }
                                            },
                                ]                              
                            ]
                        ],
                     ]);?>
                </div>
            </div>
        </div>
    </div>
    <hr>
<?php ActiveForm::end(); ?>
</div>
<?php if ($sesiones == '2911' || $sesiones == '2953' || $sesiones == '438' || $sesiones == '4268' || $sesiones == '1281' || $sesiones == '54' || $sesiones == '3205') { ?>
<div id="CapaSecid" style="display: inline;">
    <div class="capaTres">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-cogs" style="font-size: 20px; color: #827DF9;"></em> Parametrización</label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <?= Html::button('Ingresar Categorias', ['value' => url::to(['basechat/registrocategorias']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Ingresar Categorias', 'style' => 'background-color: #337ab7']) 
                                ?> 

                                <?php
                                    Modal::begin([
                                        'header' => '<h4></h4>',
                                        'id' => 'modal1',
                                        //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent1'></div>";
                                                                              
                                    Modal::end(); 
                                ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <?= Html::button('Ingresar Motivos', ['value' => url::to(['basechat/registromotivos']), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Ingresar Motivos', 'style' => 'background-color: #337ab7']) 
                                ?> 

                                <?php
                                    Modal::begin([
                                        'header' => '<h4></h4>',
                                        'id' => 'modal2',
                                        //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent2'></div>";
                                                                              
                                    Modal::end(); 
                                ?>
                            </div>
                        </div>
                        <?php if ($sesiones == '2953' || $sesiones == '2911' || $sesiones == '3205' ) {?>
                            <div class="col-md-4">
                                <div class="card1 mb">
                                    <?= Html::a('Actualizar usuarios',  ['updateusuarios'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #337ab7',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Actualizar Usuarios'])
                                    ?> 
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>        
    </div>
</div>
<?php } ?>