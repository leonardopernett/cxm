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
<div class="alertascxm-veralertatwo">    

    <?=
        $this->render('veralertatwo',[
            'idalerta' => $idalerta,
            'varId_ver' => $varId_ver,
            'varFecha_ver' => $varFecha_ver,
            'varName_ver' => $varName_ver,
            'varUsuaNombre' => $varUsuaNombre,
            'varTipoAlerta_ver' => $varTipoAlerta_ver,
            'varArchivo_ver' => $varArchivo_ver,
            'varRemitentes_ver' => $varRemitentes_ver,
            'varAsunto_ver' => $varAsunto_ver,
            'varComentarios_ver' => $varComentarios_ver,
            'varid_tipoencuestas_ver' => $varid_tipoencuestas_ver,
            'varcomentariosencuestas_ver' => $varcomentariosencuestas_ver,
            'varDataListEncuesta' => $varDataListEncuesta,
            'varDataListVerAlerta' => $varDataListVerAlerta,
        ])
    ?>

</div>
<?php Modal::end(); ?> 
