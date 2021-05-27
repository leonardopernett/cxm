<?php

use yii\helpers\Html;
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $model app\models\Detalleparametrizacion */

$this->title = Yii::t('app', 'Create Detalleparametrizacion');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Detalleparametrizacions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
    Modal::begin([
        'header' => 'Adicionar Configuración',
        'id' => 'modal-detalleparametrizacion',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    ?>
    <?php
    Pjax::begin(['id' => 'configuracion', 'timeout' => false,
        'enablePushState' => false]);
    ?>  
<div class="detalleparametrizacion-create">
    
    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <?= $this->render('_form', [
        'model' => $model,
        'idparame' => $idparame,
        'nombre' => $nombre,
        'idcategoriagestion'=>$idcategoriagestion,
        'prioridad' => $prioridad,
    ]) ?>

</div>

<?php Pjax::end(); ?>
<?php Modal::end(); ?>
