<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\db\Query;

/* @var $this yii\web\View */
/* @var $searchModel app\models\ReglaNegocioSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Reglanegocios');
$this->params['breadcrumbs'][] = $this->title;

$sessiones = Yii::$app->user->identity->id;

$rol =  new Query;
$rol    ->select(['tbl_roles.role_id'])
        ->from('tbl_roles')
        ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
        ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
$command = $rol->createCommand();
$roles = $command->queryScalar();

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
<div class="reglanegocio-index">

   <?= Html::encode($this->title) ?>

    <p>
        <?= Html::a(Yii::t('app', 'Create Reglanegocio'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php if ($roles == '270') { ?>

        <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,        
                'columns' => [
                    'id',
                    'rn',
                    [
                        'attribute' => 'pcrcName',
                        'value' => 'pcrc0.name'
                    ],
                    [
                        'attribute' => 'clienteName',
                        'value' => 'cliente0.name'
                    ],
                    [
                        'attribute' => 'cod_institucion',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => 'cod_institucion'
                    ],
                    [
                        'attribute' => 'cod_industria',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => 'cod_industria'
                    ],
                    [
                        'attribute' => 'tipo_regla',
                        'format' => 'raw',
                        'enableSorting' => false,
                        'filter' => false,
                        'value' => function ($data) {
                            $nm = "";
                            switch ($data->tipo_regla) {
                                case '1':
                                    $nm = "Recomendación de 0/9";
                                    break;
                                case '2':
                                    $nm = "Recomendación de Si/No";
                                    break;
                                case '3':
                                    $nm = "Recomendación de 1/5";
                                    break;
                                default:
                                    break;
                            }
                            return $nm;
                        }
                    ],
                    [
                        'attribute' => 'id_formulario',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => 'formulario0.name'
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{update}'],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{delete}'],
                ],
            ]);
        ?>

    <?php }else{ ?>

        <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,        
                'columns' => [
                    'id',
                    'rn',
                    [
                        'attribute' => 'pcrcName',
                        'value' => 'pcrc0.name'
                    ],
                    [
                        'attribute' => 'clienteName',
                        'value' => 'cliente0.name'
                    ],
                    [
                        'attribute' => 'cod_institucion',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => 'cod_institucion'
                    ],
                    [
                        'attribute' => 'cod_industria',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => 'cod_industria'
                    ],
                    [
                        'attribute' => 'tipo_regla',
                        'format' => 'raw',
                        'enableSorting' => false,
                        'filter' => false,
                        'value' => function ($data) {
                            $nm = "";
                            switch ($data->tipo_regla) {
                                case '1':
                                    $nm = "Recomendación de 0/9";
                                    break;
                                case '2':
                                    $nm = "Recomendación de Si/No";
                                    break;
                                case '3':
                                    $nm = "Recomendación de 1/5";
                                    break;
                                default:
                                    break;
                            }
                            return $nm;
                        }
                    ],
                    [
                        'attribute' => 'id_formulario',
                        'filter' => false,
                        'enableSorting' => false,
                        'value' => 'formulario0.name'
                    ],
                    ['class' => 'yii\grid\ActionColumn', 'template' => '{update}'],
                ],
            ]);
        ?>

    <?php } ?>

</div>