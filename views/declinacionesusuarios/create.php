<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\helpers\Url;
use nirvana\showloading\ShowLoadingAsset;
ShowLoadingAsset::register($this);


/* @var $this yii\web\View */
/* @var $model app\models\DeclinacionesUsuarios */

$this->title = Yii::t('app', 'Create Declinaciones Usuarios');


$this->registerJs(
   "      
       $(function(){
       $('#modal-Declinaciones-Usuarios').on('hidden.bs.modal', function (e) {
            var guardarFormulario = $('#guardarFormulario');
            guardarFormulario.attr('action', '" . Url::to(['formularios/guardarformulario']) . "');
            guardarFormulario.removeAttr('onSubmit');
            guardarFormulario.submit();
            guardarFormulario.attr('action', '" . Url::to(['formularios/guardaryenviarformulario']) . "');
            guardarFormulario.attr('onSubmit', 'return validarFormulario();');
           /*location.reload();*/
        });    
})"
);

?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Create Declinaciones Usuarios'),
    'id' => 'modal-Declinaciones-Usuarios',
    'size' => Modal::SIZE_LARGE,    
    'clientOptions' => [        
        'show' => true,        
    ],
]);
?>

<div class="declinaciones-usuarios-create">  

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
<script>
//$('#modal-Declinaciones-Usuarios').showLoading();
//$('#my-content-panel-id').hideLoading();
</script>
<?php Modal::end(); ?>