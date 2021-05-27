<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Usuarios */

$this->title = $model->usua_id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Usuarios'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<?php if ($isAjax): ?>
    <div class="usuarios-view">

        <div class="page-header">
            <h3><?= Html::encode($this->title) ?></h3>
        </div>

        <p>
            <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->usua_id, 'grupo_id' => $grupo_id], ['class' => 'btn btn-primary']) ?>        
            <?= Html::a(Yii::t('app', 'Cancel'), ['index', 'grupo_id' => $grupo_id], ['class' => 'btn btn-default']) ?>

        </p>
    <?php else: ?>
        <div class="usuarios-view">

            <div class="page-header">
                <h3><?= Html::encode($this->title) ?></h3>
            </div>

            <p>
                <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->usua_id], ['class' => 'btn btn-primary']) ?>        
                <?= Html::a(Yii::t('app', 'Cancel'), ['index'], ['class' => 'btn btn-default']) ?>
            </p>
        <?php endif; ?>
        <?=
        DetailView::widget([
            'model' => $model,
            'attributes' => [
                'usua_id',
                'usua_usuario',
                'usua_nombre',
                'usua_email:email',
                'usua_identificacion',
                'usua_activo',
                'usua_estado',
                'usua_fechhoratimeout',
                [
                    'attribute' => 'rol',
                    'value' => (isset($model->relUsuariosRoles->roles->role_descripcion))?$model->relUsuariosRoles->roles->role_descripcion:null
                ],
            ],
        ])
        ?>

    </div>
