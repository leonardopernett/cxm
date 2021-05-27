<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Tmptiposllamada */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tmptiposllamadas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tmptiposllamada-view">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>       
        <?=
        Html::a(Yii::t('app', 'Cancel')
                , ['index', 'tmp_formulario_id' => $model->tmpejecucionformulario_id]
                , ['class' => 'btn btn-default'])
        ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tiposllamadasdetalle_id',
            'tmpejecucionformulario_id',
        ],
    ]) ?>

</div>
