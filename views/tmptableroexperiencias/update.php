<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Tmptableroexperiencias */

$this->title = Yii::t('app', 'Update Tmptableroexperiencias: ') . ' ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tmptableroexperiencias'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>

<?php
    Modal::begin([
        'id' => 'modal-detalleparamet',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>
<div class="tmptableroexperiencias-update">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?=
    $this->render('_form', [
        'model' => $model,
        'arbol_id'=>$arbol_id,
    ])
    ?>

</div>
<?php Modal::end(); ?>