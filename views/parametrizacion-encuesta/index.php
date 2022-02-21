<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ParametrizacionEncuestaSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Parametrización Encuestas');
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
<div class="parametrizacion-encuesta-index">

   <?= Html::encode($this->title) ?>

   

    <p>
        <?= Html::a(Yii::t('app', 'Create Parametrizacion Encuesta'), ['selecparametrizacion'], ['class' => 'btn btn-success crearParametrizacion']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'id',
            [
                'attribute' => 'clienteName',
                'filter' => false,
                'enableSorting' => true,
                'value' => 'cliente0.name'
            ],
            [
                'attribute' => 'pcrcName',
                'filter' => true,
                'enableSorting' => true,
                'value' => 'programa0.name'
            ],
            ['class' => 'yii\grid\ActionColumn',
                'buttons' => [        'update' => function ($url,$model) {
                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['parametrizacionencuesta',
                                            'id' => $model->id]), [
                                    'title' => Yii::t('yii', 'update'),
                                    'data-pjax' => 'w0',
                        ]);
                    },]
            ],
            
        ],
    ]);
    ?>

</div>