<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Gruposusuarios */

$this->title = Yii::t('app', 'Create Gruposusuarios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Gruposusuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gruposusuarios-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?php if ($isAjax): ?>
        <?=
        $this->render('_form', [
            'model' => $model,
            'isAjax' => $isAjax,
            'usuario_id' => $usuario_id,
        ])
        ?>
    <?php else: ?>
        <?=
        $this->render('_form', [
            'model' => $model,
            'isAjax' => $isAjax,
        ])
        ?>
    <?php endif; ?>

</div>
