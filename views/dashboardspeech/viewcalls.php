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
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="dashboardspeech-viewcallstwo">    

    <?=
    $this->render('viewcallstwo',[
        'varidlogin' => $varidlogin,
        'varidredbox' => $varidredbox,
        'varidgrabadora' => $varidgrabadora,        
        'varResultado' => $varResultado,
        'vartexto' => $vartexto,
        'varvalencia' => $varvalencia,
        ])
    ?>

</div>
<?php Modal::end(); ?> 
