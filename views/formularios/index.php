<?php

use yii\helpers\Html;
use yii\grid\GridView;
//Agregar ----------------------------------------------------
use yii\widgets\Pjax;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FormulariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Formularios');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
.masthead {
 height: 25vh;
 min-height: 100px;
 background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
 background-size: cover;
 background-position: center;
 background-repeat: no-repeat;
 border-radius: 5px;
 box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
}
</style>
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
<div class="formularios-index">

<?= Html::encode($this->title) ?>-->

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
    <p>
        <?=
        Html::a(Yii::t('app', 'Create Formularios'), ['create'], ['class' => 'btn btn-success'])
        ?>                
    </p>

    <?=
    GridView::widget([
        'id' => 'grid-formularios',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'secciones',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a('Ver secciones', 'javascript:void(0)', [
                                'title' => Yii::t('app', 'Seccions'),
                                'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['seccions/index', 'formulario_id' => $data->id]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                    ]);
                }
                    ],
                    ['class' => 'yii\grid\ActionColumn',
                        'template' => '{duplicate}{view}{update}{delete}',
                        'buttons' => [
                            'duplicate' => function($url, $model) {
                                if ($url == "asda") {
                                    #code...
                                }
                                return Html::a('<span class="glyphicon glyphicon-share"></span> ', Url::to(['duplicate', 'id' => $model->id]), [
                                            'title' => Yii::t('app', 'Duplicate'),
                                ]);
                            }
                                ],
                            ],
                        ],
                    ]);
                    ?>


                    <?php
                    echo Html::tag('div', '', ['id' => 'ajax_result']);
                    ?>  
</div>
