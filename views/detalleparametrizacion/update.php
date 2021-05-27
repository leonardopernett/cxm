<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Detalleparametrizacion */

$this->title = Yii::t('app', 'Update Detalleparametrizacion: ') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Detalleparametrizacions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<div class="detalleparametrizacion-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'idparame' => $idparame,
        'nombre' => $nombre,
        'idcategoriagestion'=>$idcategoriagestion,
        'prioridad' => $prioridad,
    ]) ?>

</div>
