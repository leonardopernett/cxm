<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $model app\models\Tmptableroexperiencias */

$this->title = Yii::t('app', 'Create Tmptableroexperiencias');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tmptableroexperiencias'), 'url' => ['index']];
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

<div class="tmptableroexperiencias-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?=
    $this->render('_form', [
        'model' => $model,
        'problema_id' => $problema_id
    ])
    ?>

</div>
    <?php Modal::end(); ?>
