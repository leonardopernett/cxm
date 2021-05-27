<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Noticias */

$this->title = Yii::t('app', 'Detalle Noticia');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Noticias'), 'url' => ['index']];
$this->params['breadcrumbs'][] = Yii::t('app', 'Detalle Noticia');
?>
<div class="noticias-update">

    <div class="page-header">
        <h3><?php echo $model->titulo; ?></h3>
    </div>    
    <?php echo $model->descripcion; ?>
</div>
