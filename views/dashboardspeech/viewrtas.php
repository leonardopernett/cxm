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
<div class="dashboardspeech-viewrtastwo">    

    <?=
    $this->render('viewrtastwo',[
        'resultadosIDA' => $resultadosIDA,
        'countpositivas' => $countpositivas,
        'countnegativas' => $countnegativas,
        'countpositicasc' => $countpositicasc,
        'countnegativasc' => $countnegativasc,
        'totalvariables' => $totalvariables,
        'txtejecucion' => $txtejecucion,
        'txtpromediorta' => $txtpromediorta,
        'txtvarcallid' => $txtvarcallid,
        'txtvarhoras' => $txtvarhoras,
        'txtusuarios' => $txtusuarios,
        ])
    ?>

</div>
<?php Modal::end(); ?> 
