<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Seccions */

$this->title = Yii::t('app', 'Create Seccions');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Seccions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="seccions-create">
    <?php if ($isAjax): ?>
        <h3><?= Html::encode($this->title) ?></h3>

        <?=
        $this->render('_form',
                [
            'model' => $model,
            'isAjax' => $isAjax,
            'formulario_id' => $formulario_id,
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
        ])
        ?>
    <?php endif; ?>    

</div>
