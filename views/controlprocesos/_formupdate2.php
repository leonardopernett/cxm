<?php

use yii\helpers\Html;
/*use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use \app\models\ControlProcesos;
use app\models\Tipocortes;
use yii\helpers\ArrayHelper;

$this->title = 'Actualizar la Valoraciones';

    $txtIdusua = $varIdusua;
    $txtCorteId = Yii::$app->db->createCommand("select idtc from tbl_control_procesos where anulado = 0 and id = $txtProcesos")->queryScalar();
    $txtPorcentaje = Yii::$app->db->createCommand("select Dedic_valora from tbl_control_procesos where anulado = 0 and id = $txtProcesos")->queryScalar();

    $txtfechainicio = Yii::$app->db->createCommand("select distinct mesyear from tbl_tipocortes inner join tbl_control_procesos on tbl_tipocortes.idtc = tbl_control_procesos.idtc  where tbl_control_procesos.anulado = 0 and tbl_control_procesos.id = $txtProcesos")->queryScalar();
    $txtfechafin = date("Y-m-t", strtotime($txtfechainicio));

    $txtCantidad = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $txtIdusua and fechacreacion between '$txtfechainicio' and '$txtfechafin'")->queryScalar();

    // $txtGrupo = Yii::$app->db->createCommand("select distinct idgrupocorte from tbl_tipocortes inner join tbl_control_procesos on tbl_tipocortes.idtc = tbl_control_procesos.idtc where
    //     tbl_control_procesos.anulado = 0 and tbl_control_procesos.evaluados_id = $txtIdusua")->queryScalar();


    // $variables = Yii::$app->db->createCommand("select * from tbl_tipocortes where anulado = 0 and idgrupocorte = '$txtGrupo'")->queryAll();
    // $listData = ArrayHelper::map($variables, 'tipocortetc', 'tipocortetc');

    $fechaactual = date("Y-m-d");    

    $sessiones = Yii::$app->user->identity->id;

    $txtCortes = $varCortes;
    $txtIdParams = $model->id;
    $varfechaCreated = Yii::$app->db->createCommand("select fechacreacion from tbl_control_procesos where id = $txtIdParams")->queryScalar();
    $varFechaEntero1 = strtotime($varfechaCreated);
    $varFechaMe1 = date("m", $varFechaEntero1);

    $varMes1 = date("n"); 
?>
<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Equipo-de-Trabajo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
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
<br><br>
<div class="capaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fas fa-address-card" style="font-size: 20px; color: #2CA5FF;"></em> Informacion del plan: </label>
                <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                <div class="row">
                    <div class="col-md-6">
                        <label  style="font-size: 17px;"><em class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></em> Nombre del técnico: </label>
                        <?= $form->field($model, "evaluados_id")->textInput(['readonly' => 'readonly', 'value' => $varName, 'id'=>'ValoradoId'])->label('') ?>
                        <?= $form->field($model, "evaluados_id")->textInput(['readonly' => 'readonly', 'value' => $txtIdusua, 'id'=>'ValoradoId', 'class'=>"hidden"])->label('') ?>    
                    </div>
                    <div class="col-md-6">
                        <label  style="font-size: 17px;"><em class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></em> Corte seleccionado: </label>
                        <?= $form->field($model, "tipo_corte")->textInput(['readonly' => 'readonly', 'value' => $varName, 'id'=>'CorteId', 'value'=>$txtCortes])->label('') ?>
                        <?= $form->field($model, "tipo_corte")->textInput(['readonly' => 'readonly', 'value' => $varName, 'id'=>'CorteId', 'value'=>$txtCorteId, 'class'=>"hidden"])->label('') ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label  style="font-size: 17px;"><em class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></em> Porcentaje de dedicación: </label>
                        <?= $form->field($model, "Dedic_valora")->textInput(['id'=>'DedicValor', 'readonly' => 'readonly', 'value'=> $txtPorcentaje.'%' ])->label('') ?> 
                    </div>
                    <div class="col-md-6">
                        <label  style="font-size: 17px;"><em class="fas fa-asterisk" style="font-size: 10px; color: #2CA5FF;"></em> Cantidad de valoración: </label>
                        <?= $form->field($model, "cant_valor")->textInput(['id'=>'CantValor', 'readonly' => 'readonly', 'value'=>$txtCantidad])->label('') ?> 
                    </div>
                </div>
                <?php $form->end() ?>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="capaDos" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-plus-square" style="font-size: 15px; color: #FFC72C;"></em> Agregar dimensionamiento: </label> 
                            <?= Html::a('Agregar',  ['createparameters2', 'id' => $txtProcesos, 'evaluados_id' => $txtIdusua], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #337ab7',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Agregar dimensiones']) 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label>
                            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                            ?> 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="capaTres" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-address-book" style="font-size: 20px; color: #B833FF;"></em> Listado del plan: </label>
                <?= 
                        GridView::widget([
                            'dataProvider' => $dataProvider,
                            //'filterModel' => $searchModel,
                            'columns' => [
                                [
                                    'attribute' => 'PCRC',
                                    'value' => 'arboles.name',
                                ],
                                [
                                    'attribute' => 'Dimensiones',
                                    'value' => 'dimensions',
                                ],
                                [
                                    'attribute' => 'Cantidad de Valoraciones',
                                    'value' => 'cant_valor',
                                ],
                                [
                                    'attribute' => 'Justificacion',
                                    'value' => 'argumentos',
                                ],
                                [
                                    'attribute' => 'Fecha creacion',
                                    'value' => 'fechacreacion',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'template' => '{update}{delete}',
                                    'buttons' => 
                                    [
                                        'update' => function ($url, $model) {
                                            $varFechacreacion = date("Y-m-d");

                                            $varId = $model->id;
                            

                                            $vartipocorte = Yii::$app->db->createCommand("select tipo_corte from tbl_control_procesos where id = $varId")->queryScalar();


                                            $varFechaInicio = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '%$vartipocorte%'")->queryScalar();
                                            $varFechaFin = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '%$vartipocorte%'")->queryScalar();



                                            //if (($varFechacreacion >= $varFechaInicio) && ($varFechacreacion <= $varFechaFin)) {
                                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update3', 'id' => $model->id, 'evaluados_id' => $model->evaluados_id], [
                                                'class' => '',
                                                'data' => [
                                                'method' => 'post',
                                            ],
                                        ]);
                            //}
                                        },
                                        'delete' => function($url, $model){
                                             $varFechacreacion = date("Y-m-d");
                                            
                                            $varId = $model->id;

                                            $vartipocorte = Yii::$app->db->createCommand("select tipo_corte from tbl_control_procesos where id = $varId")->queryScalar();

                                            $varFechaInicio = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '%$vartipocorte%'")->queryScalar();
                                            $varFechaFin = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '%$vartipocorte%'")->queryScalar();

                                            //if (($varFechacreacion >= $varFechaInicio) && ($varFechacreacion <= $varFechaFin)) {
                                            return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id], [
                                                'class' => '',
                                                'data' => [
                                                    'method' => 'post',
                                                ],
                                            ]);
                                //}
                                        }
                                    ]              
                                ],
                            ],
                        ]); 

                ?>
            </div>
        </div>
    </div>
</div>
<hr>