<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArbolesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$variables = Yii::$app->user->identity->rolId;

$this->title = Yii::t('app', 'Programa/PCRC');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="arboles-index">

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>

    <p>
        <?=
        Html::a(Yii::t('app', 'Create Arboles'), ['create'], ['class' => 'btn btn-success', 'data-pjax' => 'w0', 'id' => 'botonCrear',
            'onclick' => "                                    
                        $.ajax({
                        type     :'POST',
                        cache    : false,
                        url  : '" . Url::to(['arboles/create']) . "',
                        success  : function(response) {
                            $('#ajax_result').html(response);
                        }
                       });
                       return false;"
        ])
        ?>
    </p>

    <?php
        if ($variables == "270" || $variables == "276") {
    ?>
        <?=
        GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'columns' => [
                [
                    'attribute' => 'id',
                    'contentOptions' => ['style' => 'width: 5%;']
                ],
                [
                    'attribute' => 'name',
                    'value' => function($data) {
                        return str_pad("", (strlen($data->dsorden)) - 2, "--", STR_PAD_LEFT) . ' ' . $data->name;
                    }
                ],
                [
                    'attribute' => 'arbolPadreName',
                    'value' => 'arbol.name'
                ],
                [
                    'attribute' => 'snhoja',
                    'filter' => false,
                    'enableSorting' => false,
                    'format' => 'raw',
                    'value' => function($data) {
                            if ($data->snhoja) {
                                return Html::a(Yii::t('app', 'Vista Previa Formulario'), ['formularios/guardarpaso3',
                                            'preview' => 1,
                                            'arbol_id' => $data->id,
                                            'formulario_id' => $data->formulario_id,'view' => "arboles/index"], ['target'=>'_blank']);
                            } else {
                                return '';
                            }
                        }
                ],
                ['class' => 'yii\grid\ActionColumn',
                    'template' => '{permissions}{update}{delete}',
                    'buttons' => [
                        'update' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '', [
                                        'title' => Yii::t('yii', 'update'),
                                        'id' => 'botonactu',
                                        'data-pjax' => 'w0',
                                        'onclick' => "                                    
                                        $.ajax({
                                        type     :'POST',
                                        cache    : false,
                                        url  : '" . Url::to(['arboles/update'
                                            , 'id' => $model->id]) . "',
                                        success  : function(response) {
                                            $('#ajax_result').html(response);
                                        }
                                       });
                                       return false;",
                            ]);
                        },
                                'permissions' => function ($url, $model) {
                            return Html::a('<span class="glyphicon glyphicon-lock"></span>', '', [
                                        'title' => Yii::t('yii', 'Permisos'),
                                        'id' => 'botonpermi',
                                        'data-pjax' => 'w0',
                                        'onclick' => "                                    
                                        $.ajax({
                                        type     :'POST',
                                        cache    : false,
                                        url  : '" . Url::to(['arboles/roles'
                                            , 'arbol_id' => $model->id]) . "',
                                        success  : function(response) {
                                            $('#ajax_result').html(response);
                                        }
                                       });
                                       return false;",
                            ]);
                        },
                            ]
                        ],
                    ],
                ]);
                ?>
                <?php
                echo Html::tag('div', '', ['id' => 'ajax_result']);
                ?>
    <?php } 
    else
    {
    ?>

        <?php
            if ($variables == "272") {               
        ?>
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    [
                        'attribute' => 'id',
                        'contentOptions' => ['style' => 'width: 5%;']
                    ],
                    [
                        'attribute' => 'name',
                        'value' => function($data) {
                            return str_pad("", (strlen($data->dsorden)) - 2, "--", STR_PAD_LEFT) . ' ' . $data->name;
                        }
                    ],
                    [
                        'attribute' => 'arbolPadreName',
                        'value' => 'arbol.name'
                    ],
                    [
                        'attribute' => 'snhoja',
                        'filter' => false,
                        'enableSorting' => false,
                        'format' => 'raw',
                        'value' => function($data) {
                                if ($data->snhoja) {
                                    return Html::a(Yii::t('app', 'Vista Previa Formulario'), ['formularios/guardarpaso3',
                                                'preview' => 1,
                                                'arbol_id' => $data->id,
                                                'formulario_id' => $data->formulario_id,'view' => "arboles/index"], ['target'=>'_blank']);
                                } else {
                                    return '';
                                }
                            }
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{update}',
                        'buttons' => [
                            'update' => function ($url, $model) {
                                return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '', [
                                            'title' => Yii::t('yii', 'update'),
                                            'id' => 'botonactu',
                                            'data-pjax' => 'w0',
                                            'onclick' => "                                    
                                            $.ajax({
                                            type     :'POST',
                                            cache    : false,
                                            url  : '" . Url::to(['arboles/update'
                                                , 'id' => $model->id]) . "',
                                            success  : function(response) {
                                                $('#ajax_result').html(response);
                                            }
                                           });
                                           return false;",
                                ]);
                            },                                   
                                ]
                            ],
                        ],
                    ]);
                    ?>
                    <?php
                    echo Html::tag('div', '', ['id' => 'ajax_result']);
                    ?>
        <?php
            }
            else
               { 
        ?>
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'id',
                            'contentOptions' => ['style' => 'width: 5%;']
                        ],
                        [
                            'attribute' => 'name',
                            'value' => function($data) {
                                return str_pad("", (strlen($data->dsorden)) - 2, "--", STR_PAD_LEFT) . ' ' . $data->name;
                            }
                        ],
                        [
                            'attribute' => 'arbolPadreName',
                            'value' => 'arbol.name'
                        ],
                        [
                            'attribute' => 'snhoja',
                            'filter' => false,
                            'enableSorting' => false,
                            'format' => 'raw',
                            'value' => function($data) {
                                    if ($data->snhoja) {
                                        return Html::a(Yii::t('app', 'Vista Previa Formulario'), ['formularios/guardarpaso3',
                                                    'preview' => 1,
                                                    'arbol_id' => $data->id,
                                                    'formulario_id' => $data->formulario_id,'view' => "arboles/index"], ['target'=>'_blank']);
                                    } else {
                                        return '';
                                    }
                                }
                        ]
                            ],
                        ]);
                        ?>
                        <?php
                        echo Html::tag('div', '', ['id' => 'ajax_result']);
                        ?>
        <?php
            }
        ?>
    <?php
    }
    ?>

</div>

<script type="text/javascript">
    $(document).ready(function(){
        var varRol = '<?php echo $variables ?>';

        var botonCreated = document.getElementById("botonCrear");
        var botonupdate = document.getElementById("botonactu");
        var botonpermission = document.getElementById("botonpermi");

        if (varRol != "270") 
        {
            botonCreated.style.display = 'none';
        }
    });
</script>
