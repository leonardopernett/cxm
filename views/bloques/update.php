<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Bloques */

$this->title = Yii::t('app', 'Update Bloques: ') . ' ' . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Bloques'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="bloques-update">

    <?php if ($isAjax): ?>
        <h3><?= Html::encode($this->title) ?></h3>
        <?=
        $this->render('_form',
                [
            'model' => $model,
            'isAjax' => $isAjax,
            'seccion_id' => $seccion_id,
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
            'filterSeccion' => $filterSeccion,
        ])
        ?>
    <?php endif; ?>

</div>
