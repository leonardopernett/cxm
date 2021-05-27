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

/*$this->registerJs(
   "$(function(){
       $('#modal-rolesArboles').on('hidden.bs.modal', function (e) {
           location.reload();
        });    
})"
);*/

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
            //['class' => 'yii\grid\SerialColumn'],             
            [
                'attribute' => 'rolName',
                'enableSorting' => true,
                'value' => 'role.nombre_grupo'
            ],            
            [
                'attribute' => 'rolDescripcion',
                'value' => 'role.grupo_descripcion'
            ],            
            /*[
                'attribute' => 'sncrear_formulario',
                'enableSorting' => false,
                'filter' => false,
                'format'=>'raw',
                'value' => function($model){
                    return Html::checkbox('',
                                        $model->sncrear_formulario,
                                    [
                                    'data-pjax' => 'w0',
                                    'onchange' => "    
                                        isChecked = $(this).is(':checked') ? 1:0;
                                    $.ajax({
                                        type     :'POST',
                                        cache    : false,
                                        data     : {id: "
                                        .$model->id.", sncrear_formulario: isChecked},
                                        url  : '" . Url::to(['arboles/roles', 'arbol_id'=>$model->arbol_id]) . "',                                        
                                   });
                                   return false;",
                        ]);
                }
            ],            
            [
                'attribute' => 'snver_grafica',
                'enableSorting' => false,
                'filter' => false,
                'format'=>'raw',
                'value' => function($model){
                    return Html::checkbox('',
                                        $model->snver_grafica,
                                    [
                                    'data-pjax' => 'w0',
                                    'onchange' => "
                                    isChecked = $(this).is(':checked') ? 1:0;                                    
                                    $.ajax({
                                        type     :'POST',
                                        cache    : false,
                                        data     : {id: "
                                        .$model->id.", snver_grafica: isChecked},
                                        url  : '" . Url::to(['arboles/roles', 'arbol_id'=>$model->arbol_id]) . "',                                        
                                   });
                                   return false;",
                        ]);
                }
            ], */           
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