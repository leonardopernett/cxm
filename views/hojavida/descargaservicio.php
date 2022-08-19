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
       $('#modal3').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
});"
);
//  Aqui se genera un cambio con el nuevo Escucha Focalizada
?>

<?php
Modal::begin([
    'header' => Yii::t('app', ''),
    'id' => 'modal3',
    // 'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>
<div class="dashboardspeech-descargaserviciotwo">    

    <?=
    $this->render('descargaserviciotwo',[
        'model' => $model,
        'varNombreClienteServicio' => $varNombreClienteServicio,
        'varNombrePcrcsServicio' => $varNombrePcrcsServicio,
        'varNombreDirectoresServicio' => $varNombreDirectoresServicio,
        'vardataProviderPersonaServicio' => $vardataProviderPersonaServicio,
        'vardataProviderentregableServicio' => $vardataProviderentregableServicio,
        'vardataProviderherramientasServicio' => $vardataProviderherramientasServicio,
        'vardataProvidermetricasServicio' => $vardataProvidermetricasServicio,
        'vardataExclusivasServicio' => $vardataExclusivasServicio,
        'id_contrato' => $id_contrato,
        ])
    ?>

</div>
<?php Modal::end(); ?> 
