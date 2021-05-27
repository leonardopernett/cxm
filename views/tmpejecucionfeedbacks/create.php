<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Tmpejecucionfeedbacks */

$this->title = Yii::t('app', 'Create feedback');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tmpejecucionfeedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
    Modal::begin([
        'id' => 'modal-detalleparametrizacion',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>
<div class="tmpejecucionfeedbacks-create">    

    <h3><?= Html::encode($this->title) ?></h3>    

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
<?php Modal::end(); ?>
