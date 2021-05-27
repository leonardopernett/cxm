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
<br>
<br>
<div class="parametrizacion-encuesta-index">

<!--    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>-->

    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Parametrizacion Encuesta'), ['selecparametrizacion'], ['class' => 'btn btn-success crearParametrizacion']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

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
            /*[
                'attribute' => 'cliente',
                'value' => 'cliente0.name'
            ],
            [
                'attribute' => 'programa',
                'value' => 'programa0.name'
            ],*/
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