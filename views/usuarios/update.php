<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = Yii::t('app', 'Update Usuarios: ') . ' ' . $model->usua_usuario;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Usuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->usua_id, 'url' => ['view', 'id' => $model->usua_id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="usuarios-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

     <?php if ($isAjax): ?>
        <?=
        $this->render('_form', [
            'model' => $model,
            'isAjax' => $isAjax,
            'grupo_id' => $grupo_id,
        ])
        ?>
    <?php else: ?>
        <?=
        $this->render('_form', [
            'model' => $model,
            'isAjax' => $isAjax,
	    'query' => $query,
        ])
        ?>
    <?php endif; ?>

</div>
