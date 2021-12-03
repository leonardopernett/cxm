<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EvaluadosSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Evaluados');
$this->params['breadcrumbs'][] = $this->title;

$usuarioId = Yii::$app->user->identity->id;
$rolId = Yii::$app->db->createCommand("select rel_role_id from rel_usuarios_roles where rel_usua_id = $usuarioId")->queryScalar();
?>
<style type="text/css">
.masthead {
 height: 25vh;
 min-height: 100px;
 background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
 background-size: cover;
 background-position: center;
 background-repeat: no-repeat;
 /*background: #fff;*/
 border-radius: 5px;
 box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
}
</style>
<header class="masthead">
 <div class="container h-100">
   <div class="row h-100 align-items-center">
     <div class="col-12 text-center">
       <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
       <p class="lead">A great starter layout for a landing page</p> -->
     </div>
   </div>
 </div>
</header>
<br>
<br>

<div class="evaluados-index">

<!--    <div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>-->
    <?php
    foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
        echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
    }
    ?>
    <?php
    //Html::beginForm(Url::to(['roles/index']), "post", ["class" => "form-horizontal", "id" => "permisosMasivos"]);
    $form = ActiveForm::begin([
        'options' => ["id" => "formEvaluados"], 'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ])
    ?>
    <p>
        <?php if ($rolId == 282 || $rolId == 270) { ?>
	        <?= Html::a(Yii::t('app', 'Create Evaluados'), ['create'], ['class' => 'btn btn-success']) ?>
        <?php } ?>
        <?= Html::a(Yii::t('app', 'Export Logeventsadmin'), "javascript:void(0)", ['class' => 'btn btn-danger', "id" => "exportar"]) ?>
        <?= Html::a(Yii::t('app', 'Limpiar Filtros'), ['limpiarfiltros'], ['class' => 'btn btn-default']) ?>
    </p>

    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],

            'id',
            'name',
            //'telefono',
            'dsusuario_red',
            //'cdestatus',
            'identificacion',
            'email:email',
            [
                'attribute' => 'Equipos',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a(Yii::t('app', 'Ver Equipos'), 'javascript:void(0)', [
                                'title' => Yii::t('app', 'Ver Equipos'),
                                //'data-pjax' => '0',
                                'onclick' => "                                    
                            $.ajax({
                            type     :'POST',
                            cache    : false,
                            url  : '" . Url::to(['equiposevaluados/equipos'
                                    , 'evaluado_id' => $data->id]) . "',
                            success  : function(response) {
                                $('#ajax_result').html(response);
                            }
                           });
                           return false;",
                    ]);
                }
                    ],
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            ?>
            <?php ActiveForm::end(); ?>

            <?php echo Html::tag('div', '', ['id' => 'ajax_result']); ?>
</div>
<script type="text/javascript">
    $(document).ready(function () {
        $('#exportar').click(function () {

            var datosErroressatu = $("#formEvaluados");
            datosErroressatu.attr('action', '<?php echo Url::to(['evaluados/export']); ?>');
            datosErroressatu.submit();
        });
        $('#filtrar').click(function () {
            var actualizarRolesMasivos = $("#formEvaluados");
            actualizarRolesMasivos.attr('action', '<?php echo Url::to(['evaluados/index']); ?>');
            actualizarRolesMasivos.submit();
        });
    });
</script>