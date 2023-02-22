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
       $('#modal6').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
});"
);
?>

<?php
Modal::begin([
    'header' => Yii::t('app', ''),
    'id' => 'modal6',
    'size' => Modal::SIZE_DEFAULT,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="hojavida-veranexometri">    

    <?=
    $this->render('veranexometri',[
        'varRuta'=> $varRuta,       
        ])
    ?>

</div>
<?php Modal::end(); ?> 