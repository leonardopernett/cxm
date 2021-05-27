<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Noticias */

$this->title = Yii::t('app', 'Create feedback');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
if ($ajax) {
    Modal::begin([
        'header' => Yii::t('app', 'Create feedback'),
        'id' => 'modal-ejecucionfeedbacks',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
}
?>
<div class="noticias-create">
    <?php
    if ($ajax) {
        Pjax::begin(['id' => 'jecucionfeedbacks-pj', 'timeout' => false,
            'enablePushState' => false]);
    }
    ?>
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?=
    $this->render('_form', [
        'model' => $model,
        'ajax'=>$ajax,
        'id'=>$id,
    ])
    ?>

    <?php if ($ajax) {
        Pjax::end();
    } ?>
</div>
<?php if ($ajax) {
    Modal::end();
} ?> 