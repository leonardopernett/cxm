<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\ControlProcesosReportebitacorauni;
use app\models\Controlbitacorauniv;

$this->title = 'Reporte Bitácora Universo';
$this->params['breadcrumbs'][] = $this->title;

    /*$template = '<div class="col-md-3">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';*/
    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id; 
    $valor = null;
    /*$txtIds = $txtIdBloques1;
    $txtValorador = $txtIdBloques1[1];
    $txtArbol_id = $txtIdBloques1[2];
    $txtFechacreacion = $txtIdBloques1[3];
    $txtTecnico = $txtIdBloques1[4]; 
    //var_dump($txtFechacreacion); */

?>

<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card {
            height: 500px;
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

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Bitacora_univer1.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/font_awesome_local/css.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
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
 <div class="capaUno">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-filter" style="font-size: 20px; color: #827DF9;"></i> Filtros</label>
                    <div class="row">
                        <div class="col-md-6">
                            <label for="txtPcrc" style="font-size: 14px;">Cliente</label>
                                    <?=  $form->field($model, 'id_cliente', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                                [
                                                                    'prompt'=>'Seleccione Cliente...',
                                                                    'onchange' => '
                                                                        $.post(
                                                                            "' . Url::toRoute('listarpcrc') . '", 
                                                                            {id: $(this).val()}, 
                                                                            function(res){
                                                                                $("#requester").html(res);
                                                                            }
                                                                        );
                                                                    ',

                                                                ]
                                                    ); 
                                    ?>              
                        </div> 
                        <div class="col-md-6">
                               <label for="txtPcrc" style="font-size: 14px;">Centro de Costos</label>
                                <?= $form->field($model,'pcrc', ['labelOptions' => [], 'template' => $template])->dropDownList(
                                                                [],
                                                                [
                                                                    'prompt' => 'Seleccione Centro de Costos...',
                                                                    "onchange"=>"carguedato();",                                                            
                                                                    'id' => 'requester'
                                                                ]
                                                            );
                                ?>
                        </div>
                    </div>
                    <div class="row">
                            <div class="col-md-6">
                                <label for="txtCiudad" style="font-size: 14px;">Momento</label>
                                    <?=  $form->field($model, 'id_momento', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Controlmomento::find()->distinct()->where("anulado = 0")->orderBy(['id_momento'=> SORT_ASC])->all(), 'id_momento', 'nombre_momento'),
                                                                        [
                                                                            'prompt'=>'Seleccione Momento...',
                                                                            'onchange' => '
                                                                                $.post(
                                                                                    "' . Url::toRoute('listarmomentos') . '", 
                                                                                    {id: $(this).val()}, 
                                                                                    function(res){
                                                                                        $("#requester2").html(res);
                                                                                    }
                                                                                );
                                                                            ',

                                                                        ]
                                                            ); 
                                        ?>
                            </div>
                            <div class="col-md-6">
                               <label for="requester2" style="font-size: 14px;">Motivos</label>                      
                                <?= $form->field($model,'id_detalle_momento', ['labelOptions' => [], 'template' => $template])->dropDownList(
                                                                    [],
                                                                    [
                                                                        'prompt' => 'Seleccione Motivo...',
                                                                        'id' => 'requester2'
                                                                    ]
                                                                );
                                ?>              
                            </div>
                    </div>
                    <div class="row">
                            <div class="col-md-6">
                                <label for="txtCiudad" style="font-size: 14px;">Fecha de registro</label>
                                <?=
                                    $form->field($model, 'fecha_registro', [
                                        'labelOptions' => ['class' => 'col-md-12'],
                                        'template' => '<div class="col-md-6">{label}</div>'
                                        . '<div class="col-md-12"><div class="input-group">'
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
                                    'opens' => 'right',
                                 ]]);
                                ?>
                            </div>    
                            <div class="col-md-6">
                                <label for="txtCiudad" style="font-size: 14px;">Cédula</label>
                                    <?=  $form->field($model, 'cedula', ['labelOptions' => [], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Controlbitacorauniv::find()->distinct()->where("estado = 'abierto'")->orderBy(['cedula'=> SORT_ASC])->all(), 'cedula', 'cedula'),
                                                                        [
                                                                            'prompt'=>'Seleccione Cédula...',                                                                           

                                                                        ]
                                                            ); 
                                 ?>
                            </div>                            
                    </div>
                </div>
        </div>
    </div>
</div>
<hr>

    <div class="CuartoBloque" style="display: inline;">
        <div class="row">
            <div class="col-md-6">
                <div class="card1 mb">
                    <label><i class="fas fa-cogs" style="font-size: 20px; color: #1e8da7;"></i> Acciones:</label>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="card1 mb">  
                                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                                ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card1 mb">  
                                <?= Html::submitButton(Yii::t('app', 'Buscar Reporte'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Buscar Reporte']) 
                                ?>
                            </div>
                        </div>                        
                    </div>
                </div>
            </div>
        </div>
    </div>
        <br>    
           
    <?php ActiveForm::end(); ?>
</div>
<hr>




<hr>  
<div class="TercerBloque" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="far fa-list-alt" style="font-size: 20px; color: #5ff21b;"></i> Listado estados abiertos:</label>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'Id',
                        'value' => 'id_bitacora_uni',                
                    ],
                    [
                        'attribute' => 'Fecha reg.',
                        'value' => 'fecha_registro',
                    ],
                    [
                        'attribute' => 'Cliente',
                        'value' => function($data){
                            return $data->getcliente($data->id_bitacora_uni);
                        }
                    ],           
                    [   
                        'attribute' => 'Centro Costo',
                        'value' => function($data){
                            return $data->getpcrc($data->id_bitacora_uni);
                        }
                    ],                  
                    [   
                        'attribute' => 'Cédula',
                        'value' => 'cedula',
                    ],
                    [
                        'attribute' => 'Nombre',
                        'value' => 'nombre',
                    ],
                    [
                        'attribute' => 'Momento',
                        'value' => function($data){
                            return $data->getmomento($data->id_bitacora_uni);
                        }
                    ],
                    [
                        'attribute' => 'Motivo',
                        'value' => function($data){
                            return $data->getmotivo($data->id_bitacora_uni);
                        }
                    ],
                    
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'color:#337ab7'],
                        'template' => '{view}',
                        'buttons' => 
                        [
                            'view' => function ($url, $model) {                        
                                return Html::a('<img src="../../../web/images/ico-edit.png">',  ['editarbitacora', 'id' => $model->id_bitacora_uni], [
                                    'class' => '',
                                    'title' => 'Editar',
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