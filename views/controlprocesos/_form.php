<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use \app\models\ControlProcesos;
use app\models\Tipocortes;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;

$this->title = 'Agregar Valorador';


    $variables = Tipocortes::find()->where(['anulado' => '0'])->all();
    $listData = ArrayHelper::map($variables, 'idtc', 'tipocortetc');

    $fechaactual = date("Y-m-d");
    $Mesactual = date("Y-m-01");
    $procesos = $count3;
    $sessiones = Yii::$app->user->identity->id;
    $varValorador = $id_valorado;

    $varNombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $txtusua_id")->queryScalar();

    $varTotal = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and fechacreacion > '$Mesactual' and evaluados_id = $txtusua_id")->queryScalar();
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
            font-family: "Nunito",sans-serif;
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
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fas fa-address-card" style="font-size: 20px; color: #2CA5FF;"></em> Informacion del plan: </label>
                <?php $form = ActiveForm::begin([
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'inputOptions' => ['autocomplete' => 'off']
                      ]
                    ]); ?>
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Nombre del tecnico: </label>
                        <?= $form->field($model, 'evaluados_id')->textInput(['maxlength' => 200, 'type' => 'number', 'readonly' => 'readonly', 'value' => $varNombre, 'id'=>'idName']) ?> 
                        <?= $form->field($model2, 'evaluados_id')->textInput(['maxlength' => 200, 'type' => 'number', 'class' => 'hidden', 'value' => $varNombre, 'id'=>'idName']) ?>   
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Porcentaje de dedicacion: </label>
                        <?= $form->field($model2, 'Dedic_valora')->textInput(['maxlength' => 200, 'type' => 'number', 'onkeypress' => 'return valida(event)', 'id'=>'DedicValor', 'placeholder'=>'Ingresar el numero del porcentaje']) ?> 
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> Total del valoraciones: </label>
                        <?= $form->field($model2, 'cant_valor')->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'onkeypress' => 'return valida(event)', 'id'=>'CantValor','value' => $varTotal]) ?>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"> seleccionar tipo de corte: </label>
                        <?= $form->field($model2, 'idtc')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'TipoCort2','onchange' => 'asignaridtc();']) ?> 
                        <?= $form->field($model2, 'tipo_corte')->textInput(['maxlength' => 200, 'class'=>'hidden', 'id'=>'Idcortes']) ?> 
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
                            <label style="font-size: 15px;"><em class="fas fa-plus-square" style="font-size: 15px; color: #FFC72C;"></em> Agregar Pcrc / dimensión: </label> 
                            <?= 
                                Html::button('Agregar', ['value' => url::to(['createparameters','usua_id'=>$txtusua_id
                                    ]), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Agregar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Agregar Pcrc-dimension</h4>',
                                    'id' => 'modal1',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent1'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Guardar plan de valoracion: </label> 
                            <div onclick="guardarbtn();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                              Guardar
                            </div> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                            <?= Html::a('Regresar',  ['indexeliminar','evaluadoID' => $varValorador], ['class' => 'btn btn-success',
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
                <label style="font-size: 20px;"><em class="fas fa-object-ungroup" style="font-size: 20px; color: #FF3F33;"></em> Planes de valoración: </label>
                <?= GridView::widget([
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
                                'class' => 'yii\grid\ActionColumn',
                                'headerOptions' => ['style' => 'color:#337ab7'],
                                'template' => '{update}{delete}',
                                'buttons' => 
                                [
                                    'update' => function ($url, $model) {
                                        return Html::a('<span class="glyphicon glyphicon-pencil"></span>', ['update', 'id' => $model->id, 'evaluadoId' => $model->evaluados_id]);
                                    },
                                    'delete' => function($url, $model){
                                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete', 'id' => $model->id, 'evaluadoId' => $model->evaluados_id], [
                                            'class' => '',
                                            'data' => [
                                                'method' => 'post',
                                            ],
                                        ]);
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
<script type="text/javascript">
    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        if (tecla==8){
            return true;
        }

        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    };

    function guardarbtn(){
        var varValoradoid = "<?php echo $txtusua_id; ?>";
        var varvarTotal = "<?php echo $varTotal; ?>";
        var varDedicValor = document.getElementById("DedicValor").value;
        var varTipoCort2 = document.getElementById("TipoCort2").value;
        var varIdcortes = document.getElementById("Idcortes").value;

        if (varvarTotal == "" || varvarTotal == "0") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No es posible guardar, no existen plan de valoración asignado.","warning");
            return;
        }else{
            if (varDedicValor == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No es posible guardar, no tiene porcentaje de dedicacion.","warning");
                return;
            }else{
                if (varTipoCort2 == "") {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","No es posible guardar, no tiene el corte del mes.","warning");
                    return;
                }else{
                    $.ajax({
                        method: "get",
                        url: "guardarplan",
                        data: {
                                txtvarValoradoid : varValoradoid,
                                txtvarvarTotal : varvarTotal,
                                txtvarDedicValor : varDedicValor,
                                txtvarTipoCort2 : varTipoCort2,
                                txtvarIdcortes : varIdcortes,
                        },
                        success : function(response){ 
                            var numRta =   JSON.parse(response);    
                            console.log(numRta);

                            window.open('../controlprocesos/index','_self');

                    }});
                }
            }
        }

    };

    function asignaridtc(){
        var varTipoCort2 = document.getElementById("TipoCort2").value;

        $.ajax({
                        method: "get",
                        url: "buscaridtc",
                        data: {
                                txtvarTipoCort2 : varTipoCort2,
                        },
                        success : function(response){ 
                            var numRta =   JSON.parse(response);    
                            console.log(numRta);

                            document.getElementById("Idcortes").value = numRta;
        }});

    };

</script>