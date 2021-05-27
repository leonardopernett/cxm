<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = Yii::t('app', 'Create Usuarios');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Usuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="usuarios-create">
    
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
