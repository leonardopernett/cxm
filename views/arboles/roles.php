<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
//Agregar-----------------------------------------------------------------------
use yii\bootstrap\Modal;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ArbolesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Programa/PCRC');
$this->params['breadcrumbs'][] = $this->title;

?>

<?php
Modal::begin([
    'header' => Yii::t('app', 'Editar Roles') . ' - Arbol: ' . $searchModel->getArbol()->one()->name,
    'id' => 'modal-rolesArboles',
    'size' => Modal::SIZE_LARGE,
    'clientOptions' => [
        'show' => true,
    ],
]);
?>

<div class="arboles-index">
    <?php Pjax::begin(['id' => 'rolesArboles-pj', 'timeout' => false,
            'enablePushState' => true]);
    ?>
    <?php echo $this->render('_formRoles', ['model' => $searchModel]); ?>
    <?=
    GridView::widget([
        'id'=>'grid-rolesArboles',
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [     
            [
                'attribute' => 'rolName',
                'enableSorting' => true,
                'value' => 'role.nombre_grupo'
            ],            
            [
                'attribute' => 'rolDescripcion',
                'value' => 'role.grupo_descripcion'
            ],
            ['class' => 'yii\grid\ActionColumn',
                'template' => '{delete}',
                'buttons' => [
                    'delete' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>',
                        Url::to(['deleterol', 'id'=>$model->id, 'arbol_id'=>$model->arbol_id]),
                                        [
                                    'title' => Yii::t('yii', 'delete'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "
                                            if (confirm('" . Yii::t('app',
                                            'Are you sure you want to delete this item?') . "')) {                                                            
                                                return true;
                                            }else{
                                                return false;
                                            }",
                        ]);
                    }                                           
                ]
            ],
        ],
    ]);
    ?>
    <?php Pjax::end(); ?>
</div>
<?php Modal::end(); ?> 