<?php

use yii\helpers\Html;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;
use yii\widget\Block;


/* @var $this yii\web\View */
/* @var $model app\models\Arboles */

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
<div class="valoracionexterna-seleccionarpcrc">    

    <?=
    $this->render('seleccionarpcrc',[
       'model' => $model,
       'varData' => $varData,
       'id_dp_clientes' => $id_dp_clientes,
    ]);
    ?>

</div>
<?php Modal::end(); ?> 
