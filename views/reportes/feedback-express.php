<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Feedback Express');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['feedbackexpress']];
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;


// $varidgrupos = Yii::$app->get('dbslave')->createCommand("select gu.grupos_id from tbl_grupos_usuarios gu inner join rel_grupos_usuarios r on gu.grupos_id = r.grupo_id inner join tbl_usuarios u on r.usuario_id = u.usua_id where u.usua_id = $sesiones  group by gu.grupos_id")->queryScalar();

$varidgrupos = Yii::$app->get('dbslave')->createCommand("select count(1) from tbl_permisosfeedback where anulado = 0 and idusuarios = $sesiones")->queryScalar();

$roles = Yii::$app->get('dbslave')->createCommand("select r.rel_role_id from rel_usuarios_roles r inner join tbl_usuarios u on r.rel_usua_id = u.usua_id  where u.usua_id = $sesiones")->queryScalar();

?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Gestión-Feedback.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }
</style>
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

<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($model, 'created', [
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
                    $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        //'data' => array_merge(["" => ""], $data),
                        'language' => 'es',
                        'options' => ['id'=>'idselectarbol','placeholder' => Yii::t('app', 'Select ...')],
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
        </div>
    </div> 

    <div class="row">        
        <div class="col-md-6">            
            <?=
                    $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...'), 'multiple' => true,],
                        'pluginOptions' => [
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
                                    }).done(function(data) { callback(data.results[0]);});
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
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['evaluadolist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['evaluadolist']) . '?id=" + id, {
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
    </div>
    <div class="row">
        <div class="col-md-6">
            <?=
                    $form->field($model, 'usua_id_lider', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['id'=>'idselectlider','placeholder' => Yii::t('app', 'Select ...'),],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 4,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['lidereslist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['lidereslist']) . '?id=" + id, {
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
        <div class="col-md-6">
            <?=
                    $form->field($model, 'snaviso_revisado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->dropDownList($model->gestionadoOptionList())
            ?>
        </div>    
    </div>
    <div class="row">  
       <div class="col-md-6">            
            <?=
                    $form->field($model, 'dimension_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                    ->widget(Select2::classname(), [
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...'), 'multiple' => true,],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 0,
                            'ajax' => [
                                'url' => Url::to(['dimensionlist']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                                if (id !== "") {
                                    $.ajax("' . Url::to(['dimensionlist']) . '?id=" + id, {
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
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','onclick'=>'verificarpcrc();'])
            ?>
            <?php //Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default'])      ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>    
</div>

<?php if (!empty($resumenFeedback)): ?>
    <div class="col-sm-6" style="padding-right: 0px;">
        <div class="page-header">
            <h3><?= Yii::t('app', 'Resumen Proceso') ?></h3>
        </div>
        <div style="max-height: 300px; overflow: auto">
            <table class="table table-striped table-bordered tblResDetFeed">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'Cliente') ?></th>
                        <th><?= Yii::t('app', 'Programa') ?></th>
                        <th><?= Yii::t('app', 'Cantidad feedback recibidos') ?></th>
                        <th><?= Yii::t('app', 'Cantidad de feedback Gestionados') ?></th>
                        <th><?= Yii::t('app', 'porcentaje De Gestion') ?></th>
                        <th><?= Yii::t('app', 'Cantidad de Asesores') ?></th>
                        <th><?= Yii::t('app', 'Tiempo promedio de Gestion') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($resumenFeedback as $value) : ?>
                        <tr>
                            <td><?= $value["cliente"]; ?></td>
                            <td><?= $value["pcrc"]; ?></td>
                            <td><?= $value["totalFeedbacks"]; ?></td>
                            <td><?= $value["cantidadG"]; ?></td>
                            <td><?php echo ($value["PorcGest"] != 0) ? (number_format(($value["PorcGest"]), 2) . ' %') : '-'; ?></td>
                            <td><?= $value["totalEvaluados"]; ?></td>
                            <td><?php echo ($value["cantidadG"] != 0) ? (number_format((abs($value["promedioGest"]) / 60), 2) . ' Hrs') : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>    

<?php endif; ?>
<?php if (!empty($detalleLiderFeedback)): ?>    
    <div class="col-sm-6" style="padding-right: 0px;">
        <div class="page-header">
            <h3><?= Yii::t('app', 'Detalle lider') ?></h3>
        </div>
        <div style="max-height: 300px; overflow: auto">
            <table class="table table-striped table-bordered tblResDetFeed">
                <thead>
                    <tr>
                        <th><?= Yii::t('app', 'Lider de Equipo') ?></th>
                        <th><?= Yii::t('app', 'Cliente') ?></th>
                        <th><?= Yii::t('app', 'Programa') ?></th>
                        <th><?= Yii::t('app', 'Cantidad feedback recibidos') ?></th>
                        <th><?= Yii::t('app', 'Cantidad de feedback Gestionados') ?></th>
                        <th><?= Yii::t('app', 'porcentaje De Gestion') ?></th>
                        <th><?= Yii::t('app', 'Cantidad de Asesores') ?></th>
                        <th><?= Yii::t('app', 'Tiempo promedio de Gestion') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($detalleLiderFeedback as $value) : ?>
                        <tr>
                            <td><?= $value["nombreLider"]; ?></td>
                            <td><?= $value["cliente"]; ?></td>
                            <td><?= $value["pcrc"]; ?></td>
                            <td><?= $value["totalFeedbacks"]; ?></td>
                            <td><?= $value["cantidadG"]; ?></td>
                            <td><?php echo ($value["PorcGest"] != 0) ? (number_format(($value["PorcGest"]), 2) . ' %') : '-'; ?></td>
                            <td><?= $value["totalEvaluados"]; ?></td>
                            <td><?php echo ($value["cantidadG"] != 0) ? (number_format((abs($value["promedioGest"]) / 60), 2) . ' Hrs') : '-'; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>

<?php endif; ?>
<br/>
<?php if ($showGrid): ?>
    <div class="col-sm-12"> 
	<br>
	<br>       
        <?php
        $gridColumns = [
            //['class' => 'yii\grid\SerialColumn'],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{preview}{update}{calculate}',
                'buttons' => [
                    'preview' => function ($url, $model) {
                        $ejecucion = \app\models\Ejecucionformularios::findOne(["id" => $model->ejecucionformulario_id]);
                        if (isset($ejecucion->basesatisfaccion_id)) {
                            $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                        }
                        if (isset($ejecucion)) {
                            if ($ejecucion->basesatisfaccion_id == '' || empty($ejecucion->basesatisfaccion_id) || is_null($ejecucion->basesatisfaccion_id)) {
                                return app\models\Ejecucionfeedbacks::hasFormulario($model->id) ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['formularios/showformulariodiligenciado'
                                                    , 'feedback_id' => $model->id,'view'=>"reportes/feedbackexpress", 'aleatorio' => 1]), [
                                            'title' => Yii::t('yii', 'ver formulario'),
                                            'target' => "_blank"
                                        ]) : false;
                            } else {
                                //if ($modelBase->estado == "Cerrado") {
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                    , 'basesatisfaccion_id' => $modelBase->id, 'preview' => 1, 'fill_values' => true,'view'=>"reportes/feedbackexpress", 'aleatorio' => 1,]), [
                                            'title' => Yii::t('yii', 'ver formulario'),
                                            'target' => "_blank"
                                ]);
                                //}
                            }
                        }
                    },
                            'update' => function ($url, $model) {
                        $page = Yii::$app->request->get('page');
                        $numPage = (empty($page)) ? 1 : $page;
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '', [
                                    'title' => Yii::t('yii', 'Update'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['reportes/updatefeedback'
                                        , 'id' => $model->id, 'page' => $numPage]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                        ]);
                    },
                            'calculate' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-stats"></span>', '', [
                                    'title' => Yii::t('yii', 'Calculos'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['reportes/calculatefeedback'
                                        , 'formulario_id' => $model->ejecucionformulario_id]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                        ]);
                    },
                        ]
                    ],
            'created',
            [
                'attribute' => 'snaviso_revisado',
                'value' => function($data) {
                    return $data->getGestionado($data->snaviso_revisado);
                }
            ],
            [
                'attribute' => 'identificacion lider',
                'value' => 'usuariolider.usua_identificacion'
            ],
            [
                'attribute' => 'usua_id_lider',
                'value' => 'usuariolider.usua_nombre'
            ],
            [
                'attribute' => 'usua_id',
                'value' => 'usuario.usua_nombre'
            ],
            [
                'attribute' => 'identificacion evaluado',
                'value' => 'evaluado.identificacion'
            ],
            [
                'attribute' => 'evaluado_id',
                'value' => 'evaluado.name'
            ],
            [
                'attribute' => 'ejecucionformulario_id',
                'value' => 'ejecucionformulario.formulario.name'
            ],
            [
                'attribute' => 'dimension_id',
                'value' => 'ejecucionformulario.dimension.name'
            ],
            'feaccion_correctiva',
            [
                'attribute' => 'categoriaFeedback',
                'value' => 'tipofeedback.categoriafeedback.name'
            ],
            [
                'attribute' => 'tipofeedback_id',
                'value' => 'tipofeedback.name'
            ],
            //'dscausa_raiz',
            //'dsaccion_correctiva',
            //'dscompromiso',
            //'dscomentario',
            [
                'header' => Yii::t('app', 'Dscausa Raiz'),
                'attribute' => 'dscausa_raiz',
                'contentOptions' => [
                    'style'=> (!$export) ? 'min-width: 200px; overflow: auto; word-wrap: break-word;' : ''
                ],
                'value' => (!$export) ? function($data) {
                        return substr($data->dscausa_raiz, 0, 100) . '...';
                    } : function($data) {
                    return $data->dscausa_raiz;
                }
            ],
            [
                'header' => Yii::t('app', 'Dsaccion Correctiva'),
                'attribute' => 'dsaccion_correctiva',
                'contentOptions' => [
                    'style'=> (!$export) ? 'min-width: 200px; overflow: auto; word-wrap: break-word;' : ''
                ],
                'value' => (!$export) ? function($data) {
                        return substr($data->dsaccion_correctiva, 0, 100) . '...';
                    } : function($data) {
                    return $data->dsaccion_correctiva;
                }
            ],
            [
                'header' => Yii::t('app', 'Dscompromiso'),
                'attribute' => 'dscompromiso',
                'contentOptions' => [
                    'style'=> (!$export) ? 'min-width: 200px; overflow: auto; word-wrap: break-word;' : ''
                ],
                'value' => (!$export) ? function($data) {
                        return substr($data->dscompromiso, 0, 100) . '...';
                    } : function($data) {
                    return $data->dscompromiso;
                }
            ],
            [
                'header' => Yii::t('app', 'Dscomentario'),
                'attribute' => 'dscomentario',
                'contentOptions' => [
                    'style'=> (!$export) ? 'min-width: 200px; overflow: auto; word-wrap: break-word;' : ''
                ],
                'value' => (!$export) ? function($data) {
                        return substr($data->dscomentario, 0, 100) . '...';
                    } : function($data) {
                    return $data->dscomentario;
                }
            ],
            'basessatisfaccion_id',            
                ];

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
                    //'fontAwesome' => true,
                    'showConfirmAlert' => false,
                    'target' => '_blank',
                    'filename' => Yii::t('app', 'Reporte_feedback') . '_' . date('Ymd'),
                    'exportRequestParam' => 'exportfeedback',
                    'columnBatchToggleSettings' => [
                        'label' => Yii::t('app', 'All')
                    ],
                    'exportConfig' => [
                        ExportMenu::FORMAT_TEXT => false,
                        ExportMenu::FORMAT_PDF => false,
                        ExportMenu::FORMAT_HTML => false,
                    ]
                ]);
                ?>
                <br/><br/>
                <?php
                echo kartik\grid\GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => $gridColumns,
                ]);
                ?>
            </div>
            <?php
            echo Html::tag('div', '', ['id' => 'ajax_result']);
            ?>

        <?php endif; ?>

<script type="text/javascript">
    function verificarpcrc(){
        var vararbol = document.getElementById("idselectarbol").value;
        var varlider = document.getElementById("idselectlider").value;
        var vargrupos = "<?php echo $varidgrupos; ?>";
        var varsesiones = "<?php echo $roles; ?>";

        if (vargrupos != '1') {
            if (vararbol == "") {
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar al menos un servicio o pcrc.","warning");
                return; 
            }            
        }else{
            if (varsesiones != '270') {
                if (varsesiones != '277') {
                   if (varlider == "") {
                        event.preventDefault();
                        swal.fire("¡¡¡ Advertencia !!!","Debes de seleccionar al lider para la busqueda.","warning");
                        return; 
                    } 
                }                
            }
        }

        // if (vargrupos != "81") {
        //     if (vargrupos != "76") {
        //         if (vargrupos != "171") {
        //             if (vargrupos != "79") {                        
        //                 if (vargrupos != "1") {
        //                     if (vararbol == "") {
        //                         event.preventDefault();
        //                         swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar al menos un servicio o pcrc.","warning");
        //                         return; 
        //                     }                
        //                 } 
        //             }           
        //         }
        //     }
        // }

    };
</script>

