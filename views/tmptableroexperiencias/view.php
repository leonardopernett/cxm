<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\Tmptableroexperiencias */


    Modal::begin([
        'id' => 'modal-detalleparamet',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
    
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tmptableroexperiencias'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="tmptableroexperiencias-view">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>

    <p>
        <?= Html::a(Yii::t('app', 'Update'),  'javascript:void(0)', ['class' => 'btn btn-primary update']) ?>        
        <?=
        Html::a(Yii::t('app', 'Cancel')
                , 'javascript:void(0)'
                , ['class' => 'btn btn-default volver'])
        ?>
    </p>

    <?=
    DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tmpejecucionformulario_id',
            'tableroenfoque_id',
            'tableroproblemadetalle_id',
            'detalle:ntext',
        ],
    ])
    ?>

</div>

<script type="text/javascript">
    $(".volver").click(function () {
        ruta = '<?php echo Url::to(['index', 'tmp_formulario_id' => $model->tmpejecucionformulario_id, 'arbol_id' => (isset($arbol_id))?$arbol_id:Yii::$app->request->get('arbol_id')]); ?>';
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            success: function (response) {
                $('#ajax_div_problemas').html(response);
            }
        });
    });
    
    $(".update").click(function () {
        ruta = '<?php echo Url::to(['update','id' => $model->id,'arbol_id' => (isset($arbol_id))?$arbol_id:Yii::$app->request->get('arbol_id')]); ?>';
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            success: function (response) {
                $('#ajax_div_problemas').html(response);
            }
        });
    });
</script><?php Modal::end(); ?>