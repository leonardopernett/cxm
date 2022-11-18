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
    'size' => 'modal-big',
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="procesosqys-verformulariomanualtwo">    

    <?=
    $this->render('verformulariomanualtwo',[
        'varListaIdForms' => $varListaIdForms,
        'varNombreCodPcrcIdeal' => $varNombreCodPcrcIdeal,
        'varUsua_id' => $varUsua_id,
      ])
    ?>

</div>
<?php Modal::end(); ?> 
