<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Declinaciones');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['declinaciones']];
$this->params['breadcrumbs'][] = $this->title;

$this->registerCssFile("@web/css/AdminLTE.css");
$this->registerCssFile("@web/css/ionicons.css");

$meses = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Declinaciones.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
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

<!--<div class="page-header">
    <h3><?php //$this->title ?></h3>      
</div>-->
<div class="row">

    <div class="col-lg-4 col-xs-12">
        <!-- small box -->
        <div class="small-box bg-red alto-box">
            <div class="inner">
                <h3><?= $numDeclinaciones ?></h3>
                <?php
                    if ($model->startDate != '' && $model->endDate) {
                        echo '<p>Declinaciones desde <br /> '.$model->startDate.' hasta '.$model->endDate.'</p>';
                    }else{
                        echo '<p>Declinaciones en <br />el mes de '.$meses[date('n') - 1].'</p>';
                    }                   
                    ?>
            </div>
            <div class="icon">
                <em class="ion ion-calendar"></em>
            </div>                
        </div>
    </div><!-- ./col -->

    <div class="col-lg-4 col-xs-12">
        <!-- small box -->
        <div class="small-box bg-green alto-box">
            <div class="inner">
                <span class="titu-reporte-decli">Top declinaciones</span>
                    <?php if (count($topDeclinaciones) > 0): ?>
                    <ol style="width: 100%">
                        <?php foreach ($topDeclinaciones as $data): ?>
                            <li><?= $data['nombre'] . ' <b>(' . round($data['prom'],2) . '%)</b>' ?></li>
                    <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <p>No hay declinaciones este mes</p>
<?php endif; ?>          
            </div>
            <div class="icon">
                <em class="ion ion-stats-bars"></em>
            </div>                
        </div>
    </div><!-- ./col -->

    <div class="col-lg-4 col-xs-12">
        <!-- small box -->
        <div class="small-box bg-yellow alto-box">
            <div class="inner">
                <span class="titu-reporte-decli">Top usuarios</span>          
                    <?php if (count($topUsuarios) > 0): ?>
                    <ol style="width: 100%">
                        <?php foreach ($topUsuarios as $usua): ?>
                            <li><?= $usua['usua_nombre'] . ' <b>(' . round($usua['prom'],2) . '%)</b>' ?></li>
                    <?php endforeach; ?>
                    </ol>
                <?php else: ?>
                    <p>Usuarios no declinaron este mes</p>
<?php endif; ?>
            </div>
            <div class="icon">
                <em class="ion ion-ios-people"></em>
            </div>                
        </div>
    </div><!-- ./col --> 
</div>

<div class="equipos-evaluados-form">    

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($model, 'fecha', [
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-4">{label}</div>'
                . '<div class="col-md-8"><div class="input-group">'
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
                    //'timePickerIncrement' => 15,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'right'
            ]]);
            ?>
        </div>   
        <div class="col-md-6">
            <?=
            $form->field($model, 'declinacion_id', ['labelOptions' => ['class' => 'col-md-12'],
                'template' => $template])->dropDownList(
                    $model->getDeclinacionesList(), ['prompt' => Yii::t('app', 'Select ...')])
            ?>
        </div>
    </div>    

    <div class="row">        
        <div class="col-md-6">            
            <?=
                    $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => Url::to(['usuariolist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['usuariolist']) . '?id=" + id, {
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
            <?=
                    $form->field($model, 'evaluado_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['evaluadolistmultiple']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['evaluadolistmultiple']) . '?id=" + id, {
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
            <?=
                    $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['getarboles']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['getarboles']) . '?id=" + id, {
                                        dataType: "json",
                                        type: "post"
                                    }).done(function(data) { callback(data.results);});
                                }
                            }')
                        ]
                            ]
            );
            ?>
            <?php /* =
              $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->getArbolesByRoles(), ['prompt' => 'Seleccione ...'])
             */ ?>
        </div>
        <div class="col-md-6">
            <?=
                    $form->field($model, 'rol', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'multiple' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['rollistmultiple']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['rollistmultiple']) . '?id=" + id, {
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
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
<?php //Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default'])       ?>
        </div>        
    </div>

<?php ActiveForm::end(); ?>    
</div>


<?php if ($showGrid): ?>
<br>
    <!--<div class="page-header">
        <h3><?= Yii::t('app', 'Resultados') ?></h3>
    </div>-->
    <?php
    $gridColumns = [
        //['class' => 'yii\grid\SerialColumn'],
        'id',
        'fecha',
        [
            'attribute' => 'url',
            'format' => 'html',
            'value' => function($data) {
                $arrayLlamada = json_decode($data->url);
                if (count($arrayLlamada) > 0) {
                    if (!Yii::$app->request->post('exportdeclinaciones')) {
                        $html = '';
                        foreach ($arrayLlamada as $key => $object) {
                            if (!empty($object->llamada)) {
                                $html .= ''
                                        . Html::a(Yii::t("app", "Reproducir Interaccion")
                                                . ' No. ' . ++$key, $object->llamada, ['target' => '_blank'])
                                        . '<br/><br/>';
                            }
                        }
                        $html .= '';
                        return $html;
                    } else {
                        foreach ($arrayLlamada as $key => $object) {
                            $url[] = $object->llamada;
                        }
                        return implode('      -      ', $url);
                    }
                }
            }
                ],
                'comentario',
                [
                    'attribute' => 'usua_id',
                    'value' => 'usua.usua_nombre'
                ],
                [
                    'attribute' => 'declinacion_id',
                    'value' => 'declinacion.nombre'
                ],
                [
                    'attribute' => 'arbol_id',
                    'value' => 'arbol.name'
                ],
                [
                    'attribute' => 'dimension_id',
                    'value' => 'dimension.name'
                ],
                [
                    'attribute' => 'evaluado_id',
                    'value' => 'evaluado.name'
                ],
            ];

            if (count($dataProvider->getModels())) {
                echo ExportMenu::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                    'columnSelectorOptions' => [
                        'label' => Yii::t('app', 'Columns'),
                    ],
                    'dropdownOptions' => [
                        'label' => Yii::t('app', 'Export All'),
                        'class' => 'btn btn-default'
                    ],
                    'enableFormatter' => false,
                    'showConfirmAlert' => false,
                    'exportFormOptions' => [3],
                    'target' => '_blank',
                    'filename' => Yii::t('app', 'Reporte_declinaciones') . '_' . date('Ymd'),
                    'exportRequestParam' => 'exportdeclinaciones',
                    'columnBatchToggleSettings' => [
                        'label' => Yii::t('app', 'All')
                    ],
                    'exportConfig' => [
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_PDF => false,
                        ExportMenu::FORMAT_HTML => false,
                    ]
                ]);
                echo '<br/><br/>';
            }
            ?>

            <?php
            echo kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
            ]);
            ?>

            <?php
            echo Html::tag('div', '', ['id' => 'ajax_result']);
            ?>

        <?php endif; ?>



