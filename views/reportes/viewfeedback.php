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
//  Aqui se genera un cambio con el nuevo Escucha Focalizada
?>

<?php
Modal::begin([
    'header' => Yii::t('app', ''),
    'id' => 'modal',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="reportes-viewfeedbacktwo">    

    <?=
    $this->render('viewfeedbacktwo',[
        'varViewsFeedbacks' => $varViewsFeedbacks,
        ])
    ?>

</div>
<?php Modal::end(); ?> 
