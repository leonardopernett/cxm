<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Tmpejecucionfeedbacks */
Modal::begin([
        'id' => 'modal-detalleparamet',
        'size' => Modal::SIZE_LARGE,
        'clientOptions' => [
            'show' => true,
        ],
    ]);
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Tmpejecucionfeedbacks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="tmpejecucionfeedbacks-view">

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

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'tipofeedback_id',
            'tmpejecucionformulario_id',
            'usua_id',
            'created',
            'usua_id_lider',
            'evaluado_id',
            //'snavisar',
            //'snaviso_revisado',
            //'dsaccion_correctiva:ntext',
            //'feaccion_correctiva',
            //'nmescalamiento',
            //'feescalamiento',
            //'dscausa_raiz:ntext',
            //'dscompromiso:ntext',
            'dscomentario:ntext',
        ],
    ]) ?>

</div>
<script type="text/javascript">
    $(".volver").click(function () {
        ruta = '<?php echo Url::to(['index','tmp_formulario_id' => $model->tmpejecucionformulario_id
                , 'usua_id_lider' => $model->usua_id_lider
                , 'evaluado_id' => $model->evaluado_id
                , 'basessatisfaccion_id' => $model->basessatisfaccion_id]); ?>';
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            success: function (response) {
                $('#ajax_div_feedbacks').html(response);
            }
        });
    });
    
    $(".update").click(function () {
        ruta = '<?php echo Url::to(['update','id' => $model->id]); ?>';
        $.ajax({
            type: 'POST',
            cache: false,
            url: ruta,
            success: function (response) {
                $('#ajax_div_feedbacks').html(response);
            }
        });
    });
</script>
<?php Modal::end(); ?>
