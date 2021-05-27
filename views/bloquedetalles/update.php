<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bloquedetalles */

$this->title = Yii::t('app', 'Update Bloquedetalles: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bloquedetalles'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bloquedetalles-update">

    <?php if ($isAjax): ?>
        <h3><?= Html::encode($this->title) ?></h3>
        <?=
        $this->render('_form',
                [
            'model' => $model,
            'isAjax' => $isAjax,
            'bloque_id' => $bloque_id,
        ])
        ?>
    <?php else: ?>
        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>

        <?=
        $this->render('_form',
                [
            'model' => $model,
            'isAjax' => $isAjax,
            'filterBloque' => $filterBloque,
        ])
        ?>
    <?php endif; ?>

</div>
