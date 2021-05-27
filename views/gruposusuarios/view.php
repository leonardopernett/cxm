<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Gruposusuarios */

$this->title = $model->grupos_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Gruposusuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($isAjax): ?>
    <div class="gruposusuarios-view">

        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>

        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->grupos_id,'usuario_id' => $usuario_id], ['class' => 'btn btn-primary']) ?>
            <?=
            Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->grupos_id,'usuario_id' => $usuario_id],
                    [
                'class' => 'btn btn-danger',
                'onclick' => "
                    if (confirm('" . Yii::t('app',
                        'Are you sure you want to delete this item?') . "')) {                                                            
                        return true;
                    }else{
                        return false;
                    }",
                'data' => [
                    'pjax' => 'w0',
                ],
            ])
            ?>
        <?= Html::a(Yii::t('app', 'Cancel'), ['index', 'usuario_id' => $usuario_id], ['class' => 'btn btn-default'])
                ?>
        </p>
<?php else: ?>
        <div class="gruposusuarios-view">

            <div class="page-header">
                <h3><?= Html::encode($this->title) ?></h3>
            </div>

            <p>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->grupos_id], ['class' => 'btn btn-primary']) ?>
                <?=
                Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->grupos_id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ])
                ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
            </p>


        <?php endif; ?>
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'grupos_id',
                'nombre_grupo',
                'per_realizar_valoracion',
            ],
        ])
        ?>

    </div>