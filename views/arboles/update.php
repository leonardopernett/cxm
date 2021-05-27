<?php

use yii\helpers\Html;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Arboles */

$this->title = Yii::t('app', 'Update Arboles: ') . ' ' . $model->name;
$this->registerJs(
   "$(function(){
       $('#modal-Arboles').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
});"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Update Arboles: ') . ' ' . $model->name,
    'id' => 'modal-Arboles',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="arboles-update">    

    <?=
    $this->render('_form', [
        'model' => $model,
    ])
    ?>

</div>
<?php Modal::end(); ?> 
