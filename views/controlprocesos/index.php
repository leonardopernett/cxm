<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $varMes = date("n");
    $txtMes = null;
            switch ($varMes) {
                case '1':
                    $txtMes = "Enero";
                    break;
                case '2':
                    $txtMes = "Febrero";
                    break;
                case '3':
                    $txtMes = "Marzo";
                    break;
                case '4':
                    $txtMes = "Abril";
                    break;
                case '5':
                    $txtMes = "Mayo";
                    break;
                case '6':
                    $txtMes = "Junio";
                    break;
                case '7':
                    $txtMes = "Julio";
                    break;
                case '8':
                    $txtMes = "Agosto";
                    break;
                case '9':
                    $txtMes = "Septiembre";
                    break;
                case '10':
                    $txtMes = "Octubre";
                    break;
                case '11':
                    $txtMes = "Noviembre";
                    break;
                case '12':
                    $txtMes = "Diciembre";
                    break;
                default:
                    # code...
                    break;
            }  


	$txtConteo = Yii::$app->db->createCommand("select count(*) from tbl_control_procesos where responsable = $sessiones and anulado = 0 and tipo_corte like '%$txtMes%'")->queryScalar();    


?>
<style>
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
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-user-circle" style="font-size: 20px; color: #2CA5FF;"></em> Buscar técnico/lider: </label>
                <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?> 
                <div class="row">
                    <div class="col-sm-12">
                        <?=
                            $form->field($model, 'evaluados_id')->label(Yii::t('app',''))
                            ->widget(Select2::classname(), [
                                //'data' => array_merge(["" => ""], $data),
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Seleccionar el técnico/lider...')],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 4,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                                var id=$(element).val();
                                                if (id !== "") {
                                                    $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                                        dataType: "json",
                                                        type: "post"
                                                    }).done(function(data) { callback(data.results[0]);});
                                                }
                                            }')
                                ]
                                    ] 
                            );
                        ?>
                    </div>
                </div>
                <div align="center">
                    <?= Html::submitButton(Yii::t('app', 'Buscar técnico/lider'),
                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'title' => 'Buscar Valorado']) 
                    ?>
                </div>
                <?php $form->end() ?> 
            </div>
        </div>
        <div class="col-md-8">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"> agregar técnico/lider: </label> 
                            <?= Html::button('Agregar', ['value' => url::to('selecciontecnico'), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Agregar Valorado', 'style' => 'background-color: #337ab7']) 
                            ?> 
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Agregar tecnico</h4>',
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
                            <label style="font-size: 15px;"> verificar cortes: </label>
                            <?= Html::a('Verificar',  ['vercortes'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #337ab7',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Ver Cortes']) 
                            ?> 
                        </div>
                    </div>

                    <?php   // if($sessiones == 2953) { ?>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;">remover técnico/lider: </label>
                            <?= Html::button('Desvincular', ['value' => url::to('desvincular'), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Desvincular técinco', 'style' => 'background-color: #337ab7']) 
                            ?> 
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Petición para desvincular técnico</h4>',
                                    'id' => 'modal2',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent2'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                    <?php   // } ?>
		
                    <?php if($txtConteo == "0") { ?>
                    <?php  //   if($sessiones == 2953) { ?>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"> clonar plan: </label>                            
                                <div onclick="actualizar();" class="btn btn-primary" style="display:inline; width:auto; background: #337ab7;" method='post' id="botones5" >
                                    Clonar
                                </div>                            
                        </div>
                    </div>
                    <?php  //   } ?>
                    <?php } ?>
		
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-address-book" style="font-size: 20px; color: #B833FF;"></em> Listado del equipo: </label>
                    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            //'filterModel' => $searchModel,
                            'columns' => [
                                [
                                    'attribute' => 'Rol',
                                    'value' => function($data){
                                        return $data->getRol($data->evaluados_id);
                                    }
                                ],
                                [
                                    'attribute' => 'Técnico/lider',
                                    'value' => 'usuarios.usua_nombre',
                                ],
                                [
                                    'attribute' => 'Cantidad de Valoraciones',
                                    'value' => function($data){
                                        return $data->getMetas1($data->id, $data->evaluados_id);
                                    }
                                ],
                                [
                                    'attribute' => 'Salario - $',
                                    'value' => 'salario',
                                ],
                                [
                                    'attribute' => 'Tipo de Corte',
                                    'value' => 'tipo_corte',
                                ],

                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'template' => '{view}{update}{delete}',
                            //'template' => '{view}{delete}',
                                    'buttons' => 
                                    [
                                        'view' => function ($url, $model) {
                                            //return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', $url, ['Ver'=>Yii::t('app','view'),]);
                                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['view', 'id' => $model->id, 'evaluados_id' => $model->evaluados_id], [
                                                'class' => '',
                                                'data' => [
                                                    'method' => 'post',
                                                ],
                                            ]);
                                        },
                                        'update' => function ($url, $model) {  
                                                $varMes = date("n");
                                                $txtMes = null;
                                                        switch ($varMes) {
                                                            case '1':
                                                                $txtMes = "Enero";
                                                                break;
                                                            case '2':
                                                                $txtMes = "Febrero";
                                                                break;
                                                            case '3':
                                                                $txtMes = "Marzo";
                                                                break;
                                                            case '4':
                                                                $txtMes = "Abril";
                                                                break;
                                                            case '5':
                                                                $txtMes = "Mayo";
                                                                break;
                                                            case '6':
                                                                $txtMes = "Junio";
                                                                break;
                                                            case '7':
                                                                $txtMes = "Julio";
                                                                break;
                                                            case '8':
                                                                $txtMes = "Agosto";
                                                                break;
                                                            case '9':
                                                                $txtMes = "Septiembre";
                                                                break;
                                                            case '10':
                                                                $txtMes = "Octubre";
                                                                break;
                                                            case '11':
                                                                $txtMes = "Noviembre";
                                                                break;
                                                            case '12':
                                                                $txtMes = "Diciembre";
                                                                break;
                                                            default:
                                                                # code...
                                                                break;
                                                        } 
                                  if ($model->tipo_corte == "Corte ".$txtMes." - General Konecta" || $model->tipo_corte == "Corte ".$txtMes." - Grupo Bancolombia" || $model->tipo_corte == "Corte ".$txtMes." - Directv" || $model->tipo_corte == "Corte ".$txtMes." - Nutresa") {                          
                                            //return Html::a('<span class="glyphicon glyphicon-pencil"></span>', $url, ['create'=>Yii::t('app','create'),]);
                                            return Html::a('<span class="glyphicon glyphicon-pencil"></span>',['update2', 'id' => $model->id, 'evaluados_id' => $model->evaluados_id], [
                                                'class' => '',
                                                'data' => [
                                                    'method' => 'post',
                                                ],
                                            ]);
                                  }
                                
                                 },
                                         'delete' => function($url, $model){
                                                $varMes = date("n");
                                                $txtMes = null;
                                                        switch ($varMes) {
                                                            case '1':
                                                                $txtMes = "Enero";
                                                                break;
                                                            case '2':
                                                                $txtMes = "Febrero";
                                                                break;
                                                            case '3':
                                                                $txtMes = "Marzo";
                                                                break;
                                                            case '4':
                                                                $txtMes = "Abril";
                                                                break;
                                                            case '5':
                                                                $txtMes = "Mayo";
                                                                break;
                                                            case '6':
                                                                $txtMes = "Junio";
                                                                break;
                                                            case '7':
                                                                $txtMes = "Julio";
                                                                break;
                                                            case '8':
                                                                $txtMes = "Agosto";
                                                                break;
                                                            case '9':
                                                                $txtMes = "Septiembre";
                                                                break;
                                                            case '10':
                                                                $txtMes = "Octubre";
                                                                break;
                                                            case '11':
                                                                $txtMes = "Noviembre";
                                                                break;
                                                            case '12':
                                                                $txtMes = "Diciembre";
                                                                break;
                                                            default:
                                                                # code...
                                                                break;
                                                        } 
                                if ($model->tipo_corte == "Corte ".$txtMes." - General Konecta" || $model->tipo_corte == "Corte ".$txtMes." - Grupo Bancolombia" || $model->tipo_corte == "Corte ".$txtMes." - Directv" || $model->tipo_corte == "Corte ".$txtMes." - Nutresa") {
                                             return Html::a('<span class="glyphicon glyphicon-trash"></span>', ['delete2', 'id' => $model->id, 'evaluados_id' => $model->evaluados_id], [
                                                 'class' => '',
                                                 'data' => [
                                     'confirm' =>"Por favor confirmar eliminación del plan de valoración...",
                                                     'method' => 'post',
                                                 ],
                                             ]);
                                }
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
<script type="text/javascript">
    function actualizar(){
        var varCoordi = "<?php echo $sessiones; ?>";

        $.ajax({
            method: "get",
            url: "clonargrupo",
            data: {
                    txtvarCoordi : varCoordi,
            },
            success : function(response){ 
                var numRta =   JSON.parse(response);    
                console.log(numRta);

                location.reload();
            }
        });
    };
</script>