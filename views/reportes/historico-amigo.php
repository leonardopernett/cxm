<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Mis valoraciones');
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
$diasMes = date("d");
?>

<div class="page-header">
    <h3><?= $this->title ?></h3>
</div>

<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    echo '<div class="alert alert-' . $key . '" role="alert">' . $message . '</div>';
}
?>

<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>
    <?php echo Html::hiddenInput('Ejecucionformularios[evaluado_id]', $model->evaluado_id); ?>
    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($model, 'created', [                
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-3">{label}</div>'
                . '<div class="col-md-9"><div class="input-group">'
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
                    'opens' => 'center',
                    'minDate' => date("Y-m-d", strtotime(date("Y-m-d") . "-$diasMes day -1 month")),
                    'maxDate' => date("Y-m-d", strtotime(date("Y-m-d") . " now"))
            ]]);
            ?>
        </div>
        <div class="col-md-6">
            <div class="form-group">               
                    <?=
                    Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
                    ?>                      
            </div>
        </div>
    </div>


    <?php ActiveForm::end(); ?>    
</div>

<?php if ($showGrid): ?>    
    <?php
    $text = app\models\Textos::find()->asArray()->all();
    $gridColumns = [
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{preview}{notificacion}',
            'buttons' => [
                'preview' => function ($url, $model) {
                    $ejecucion = \app\models\Ejecucionformularios::findOne(["id" => $model["fid"]]);
                    if (isset($ejecucion->basesatisfaccion_id)) {
                        $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                    }
                    if ($ejecucion->basesatisfaccion_id == '') {
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['formularios/showformulariodiligenciadoamigo'
                                            , 'form_id' => base64_encode($model["id"])]), [
                                    'title' => Yii::t('yii', 'ver formulario'),
                                    'target' => "_blank"
                        ]);
                    } else {
                        
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['basesatisfaccion/showformulariogestionamigo'
                                            , 'basesatisfaccion_id' => base64_encode($modelBase->id), 'preview' => 1, 'fill_values' => true]), [
                                    'title' => Yii::t('yii', 'ver formulario'),
                                    'target' => "_blank"
                        ]);
                        
                    }
                },
                        'update' => function ($url, $model) {
                    $ejecucion = \app\models\Ejecucionformularios::findOne(["id" => $model["fid"]]);
                    if (isset($ejecucion->basesatisfaccion_id)) {
                        $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                    }
                    if ($ejecucion->basesatisfaccion_id == '') {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>'
                                        , Url::to(['formularios/editarformulariodiligenciado'
                                            , 'tmp_id' => $model["id"]]), [
                                    'title' => Yii::t('yii', 'Update'),
                                    'target' => "_blank",
                        ]);
                    } else {
                        if ($modelBase->estado == "Cerrado" && (Yii::$app->user->identity->isAdminSistema() || Yii::$app->user->identity->id == $model['ideva'])) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                , 'basesatisfaccion_id' => $modelBase->id, 'preview' => 0, 'fill_values' => false]), [
                                        'title' => Yii::t('yii', 'Update'),
                                        'target' => "_blank"
                            ]);
                        }
                    }
                },
                    /*
                     *TO-DO : DESCOMENTAR EN EL MOMENTO DE USAR LA PARTE DE DESHBOARD NUEVA------
                     */     'notificacion' => function ($url, $model) {
                    $notificacion = \app\models\SegundoCalificador::find()->where(['id_ejecucion_formulario' => $model["fid"]])->one();

                    if (!isset($notificacion)) {
                        return Html::a('<span style="color: #fa142f;" class="glyphicon glyphicon-hand-up"></span>', '', [
                                    'title' => Yii::t('yii', 'notificaciones segundo calificador'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['site/create'
                                        , 'id' => $model["fid"],'bandera'=>0,'historico'=>0]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                        ]);
                    }else{
                        return Html::a('<span style="color: green" class="glyphicon glyphicon-hand-up"></span>', '', [
                                    'title' => Yii::t('yii', 'notificaciones segundo calificador'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['site/create'
                                        , 'id' => $model["fid"],'bandera'=>1,'historico'=>0]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                        ]);
                    }
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
                                    , 'formulario_id' => $model["id"]]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                    ]);
                },
                        'delete' => function ($url, $model) {
                    //ENLACE PARA BORRAR VALORACIONES
                    if (in_array(Yii::$app->user->identity->id, Yii::$app->params["idUsersDelete"])) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['formularios/borrarformulariodiligenciado',
                                            'tmp_id' => $model["id"]]), [
                                    'title' => Yii::t('yii', 'delete'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "
                                            if (confirm('"
                                    . Yii::t('app', 'Are you sure '
                                            . 'you want to delete '
                                            . 'this item?') . "')) {                                                            
                                                return true;
                                            }else{
                                                return false;
                                            }",
                                        ]
                        );
                    }
                },
                    ]
                ],
        [
            'header' => 'Fecha',
            'value' => 'created'
        ],
        [
            'header' => 'Valorador',
            'value' => 'usuario'
        ],
        [
            'header' => 'Equipo',
            'value' => 'equipoName'
        ],
        [
            'header' => 'Líder',
            'value' => 'usuarioLider'
        ],
        [
            'header' => 'Valorado Cédula',
            'attribute' => 'eidentificacion'
        ],
        [
            'header' => 'Valorado',
            'value' => 'evaluado'
        ],
        [
            'header' => 'Formulario',
            'value' => 'formulario'
        ],
        [
            'header' => 'Programa/PCRC',
            'value' => 'nmarbol'
        ],
        [
            'header' => 'Rol',
            'value' => 'role_nombre'
        ],
        [
            'header' => 'Dimensión',
            'value' => 'dimension'
        ],
        'score',
        'pec_rac',
        [
            'header' => $text[0]['detexto'],
            'attribute' => 'i1_nmcalculo'
        ],
        [
            'header' => $text[1]['detexto'],
            'attribute' => 'i2_nmcalculo'
        ],
        [
            'header' => $text[2]['detexto'],
            'attribute' => 'i3_nmcalculo'
        ],
        [
            'header' => $text[3]['detexto'],
            'attribute' => 'i4_nmcalculo'
        ],
        [
            'header' => $text[4]['detexto'],
            'attribute' => 'i5_nmcalculo'
        ],
        [
            'header' => $text[5]['detexto'],
            'attribute' => 'i6_nmcalculo'
        ],
        [
            'header' => $text[6]['detexto'],
            'attribute' => 'i7_nmcalculo'
        ],
        [
            'header' => $text[7]['detexto'],
            'attribute' => 'i8_nmcalculo'
        ],
        [
            'header' => $text[8]['detexto'],
            'value' => 'i9_nmcalculo'
        ],
        [
            'header' => $text[9]['detexto'],
            'attribute' => 'i10_nmcalculo'
        ],
        [
            'header' => Yii::t('app', 'Dscomentario'),
            'attribute' => 'dscomentario',
            /*'contentOptions' => [
                'style'=>'min-width: 200px; overflow: auto; word-wrap: break-word;'
            ],
            'value' => function($data) {
                return substr($data['dscomentario'], 0, 100) . '...';
            }*/
        ],
        [
            'header' => Yii::t('app', 'Id Usuario/Modificación'),
            'attribute' => 'usua_id_modifica'
        ],
        [
            'header' => Yii::t('app', 'Fecha de Modificación'),
            'attribute' => 'modified'
        ],
        [
            'header' => 'ID Evaluado',
            'value' => 'evaluado_id'
        ],
        'formulario_id',
        [
            'header' => 'ID Arbol',
            'value' => 'fid'
        ],
        
            ];
            ?>
            <br/><br/>
            <?php
            echo kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
            ]);
            ?>
        <?php endif; ?>
        <?php
        echo Html::tag('div', '', ['id' => 'ajax_result']);
        ?>