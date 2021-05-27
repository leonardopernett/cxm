<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

$this->registerJs(
        "$(function(){
       $('#modal-segundocalificador').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
});"
);
$this->title = Yii::t('app', 'Segundo Calificadors');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Segundo Calificadors'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php
Modal::begin([    
    'id' => 'modal-segundocalificador',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>

<div class="segundo-calificador-create">
    <?php
    Pjax::begin(['id' => 'segundocalificador-pj', 'timeout' => false,
        'enablePushState' => false]);
    ?>  
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?=
    $this->render('_form', [
        'model' => $model,
        'isAjax' => $isAjax,
        'fid' => $fid,
        'scid' => $scid,
        'bandera' => $bandera,
        'historico' => $historico,
        'esLider' => $esLider,
        'arrayCadena' => $arrayCadena,
        'modelCaso' => $modelCaso,
    ])
    ?>
    <?php Pjax::end(); ?>

</div>
<?php Modal::end(); ?>