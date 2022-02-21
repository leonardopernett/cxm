<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $searchModel app\models\logeventsadminSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Logeventsadmins');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
.masthead {
 height: 25vh;
 min-height: 100px;
 background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
 background-size: cover;
 background-position: center;
 background-repeat: no-repeat;
 border-radius: 5px;
 box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
}
</style>
<header class="masthead">
 <div class="container h-100">
   <div class="row h-100 align-items-center">
     <div class="col-12 text-center">
     </div>
   </div>
 </div>
</header>
<br>
<br>
<div class="logeventsadmin-index">

    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
    
    <?php

    $form = ActiveForm::begin([
        'options' => ["id" => "formLogaadmin"],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ])
    ?>

    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($searchModel, 'fecha_modificacion', [
                'labelOptions' => ['class' => 'col-md-12'],
                'template' => '<div class="col-md-4">{label}</div>'
                . '<div class="col-md-8"><div class="input-group">'
                . '<span class="input-group-addon" id="basic-addon1">'
                . '<i class="glyphicon glyphicon-calendar"></i>'
                . '</span>{input}</div>{error}{hint}</div>',
                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                'options' => ['class' => 'drp-container form-group']
            ])->widget(DateRangePicker::classname(), [
                'useWithAddon' => true,
                'convertFormat' => true,
                'presetDropdown' => true,
                'readonly' => 'readonly',
                'pluginOptions' => [
                    'timePicker' => false,
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'left'
            ]]);
            ?>
        </div>    
    </div>  
    <p>
        <?= Html::a(Yii::t('app', 'Buscar'), "javascript:void(0)", ['class' => 'btn btn-primary', "id" => "filtrar"]) ?>
        <?= Html::a(Yii::t('app', 'Export Logeventsadmin'), "javascript:void(0)", ['class' => 'btn btn-success', "id" => "exportar"]) ?>
        <?= Html::a(Yii::t('app', 'Delete'), "javascript:void(0)", ['class' => 'btn btn-danger', "id" => "borrar"]) ?>
        <?= Html::a(Yii::t('app', 'Limpiar Filtros'), ['limpiarfiltros'], ['class' => 'btn btn-default']) ?>

    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'tabla_modificada',
            'datos_ant:ntext',
            'datos_nuevos:ntext',
            [
                'attribute' => 'fecha_modificacion',
                'value' => 'fecha_modificacion',
                'filter' => false,
            ],
            'usuario_modificacion',
            'id_usuario_modificacion',
            [
                'class' => 'yii\grid\CheckboxColumn',
            // you may configure additional properties here
            ],
            ['class' => 'yii\grid\ActionColumn', 'template' => '{view}{delete}'],
        ],
    ]);
    ?>
    <?php ActiveForm::end(); ?>
</div>


<br/><br/>
<script type="text/javascript">
    $(document).ready(function () {
        $('#exportar').click(function () {
            var actualizarRolesMasivos = $("#formLogaadmin");
            actualizarRolesMasivos.attr('action', '<?php echo Url::to(['logeventsadmin/export']); ?>');
            actualizarRolesMasivos.submit();
        });
        $('#borrar').click(function () {
            var datosErroressatu = $("#formLogaadmin");
            datosErroressatu.attr('action', '<?php echo Url::to(['logeventsadmin/eliminacionmasiva']); ?>');
            datosErroressatu.submit();
        });
        $('#filtrar').click(function () {
            var actualizarRolesMasivos = $("#formLogaadmin");
            actualizarRolesMasivos.attr('action', '<?php echo Url::to(['logeventsadmin/index']); ?>');
            actualizarRolesMasivos.submit();
        });
    });
</script>