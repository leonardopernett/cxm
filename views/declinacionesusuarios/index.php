<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DeclinacionesUsuariosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Declinaciones Usuarios');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="declinaciones-usuarios-index">
    
<div class="page-header">
    <h3><?= Html::encode($this->title) ?></h3>
</div>
    
    
    <p>
        <?= Html::a(Yii::t('app', 'Create Declinaciones Usuarios'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'url:url',
            'fecha',
            'comentario',
            'usua_id',
            // 'declinacion_id',
            // 'arbol_id',
            // 'dimension_id',
            // 'evaluado_id',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

</div>
