<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CategoriafeedbacksSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Categoriafeedbacks');
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
<div class="categoriafeedbacks-index">

    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>


        <?= Html::encode($this->title) ?>
    

    <p>
        <?= Html::a(Yii::t('app', 'Create Categoriafeedbacks'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'id',
            'name',
            [
                'attribute' => 'Detalle',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a(Yii::t('app', 'Ver detalle tipo feedbacks'), 'javascript:void(0)', [
                                'title' => Yii::t('app', 'Ver detalle tipo feedbacks'),
                                'onclick' => "                                    
                            $.ajax({
                            type     :'POST',
                            cache    : false,
                            url  : '" . Url::to(['tipofeedbacks/index'
                                    , 'categoriafeedback_id' => $data->id]) . "',
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
