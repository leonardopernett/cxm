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
    // 'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="dashboardspeech-viewcallidstwo">    

    <?=
    $this->render('viewcallidstwo',[
        'varextension' => $varextension,
        'idcallids' => $idcallids,
        'varconnid' => $varconnid,
        'varencuestaid' => $varencuestaid,
        'varbuzones' => $varbuzones,
        ])
    ?>

</div>
<?php Modal::end(); ?> 
