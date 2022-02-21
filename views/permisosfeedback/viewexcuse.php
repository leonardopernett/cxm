<?php

use yii\helpers\Html;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\widget\Block;

/* @var $this yii\web\View */
/* @var $model app\models\Arboles */
$this->registerJs(
   "$(function(){
       $('#modal').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
});"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', ''),
    'id' => 'modal',
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="permisosfeedback-viewexcusetwo">    

    <?=
    $this->render('viewexcusetwo',[
        'model' => $model,
        ])
    ?>

</div>
<?php Modal::end(); ?> 
