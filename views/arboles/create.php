<?php

use yii\helpers\Html;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Arboles */

$this->title = Yii::t('app', 'Create Arboles');

?>
<?php
Modal::begin([
    'header' => Yii::t('app', 'Create Arboles') . ' ' . $model->name,
    'id' => 'modal-Arboles',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>

<?php
Pjax::begin(['id' => 'Arboles-pj', 'timeout' => false,
    'enablePushState' => false]);
?>
<div class="arboles-update">    

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
<?php Pjax::end(); ?>
<?php Modal::end(); ?> 
