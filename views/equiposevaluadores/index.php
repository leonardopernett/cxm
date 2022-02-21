<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EquiposSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Equipos Valoradores');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="equipos-index">


        <?= Html::encode($this->title) ?>
 

    <p>
        <?=
        Html::a(Yii::t('app', 'Create Equipos valoradores'), ['create'],
                ['class' => 'btn btn-success'])
        ?>
    </p>   

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'equipo',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a(Yii::t('app', 'Ver Equipo valoradores'),
                                    'javascript:void(0)',
                                    [
                                'title' => Yii::t('app', 'Equipo'),
                                'onclick' => "                                    
                                            $.ajax({
                                            type     :'POST',
                                            cache    : false,
                                            url  : '" . Url::to(['relequiposevaluadores/index',
                                    'equipo_id' => $data->id]) . "',
                                            success  : function(response) {
                                                $('#ajax_result').html(response);
                                            }
                                           });
                                           return false;",
                    ]);
                }
                    ],
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            ?>
            <?php
            echo Html::tag('div', '', ['id' => 'ajax_result']);
            ?>
</div>


