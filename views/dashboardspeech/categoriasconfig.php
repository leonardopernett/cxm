<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;


$this->title = 'DashBoard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Configuración de Categorias -- CXM & Speech --';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card {
            height: 80px;
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

    .card2 {
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
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Parametrizador.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<div class="capabtn" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Regresar: </label> 
                            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #707372',
                                'data-toggle' => 'tooltip',
                                'title' => 'Regresar']) 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Registrar Categorias: </label>
                            <?= Html::a('Registrar',  ['registrarcategorias'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #337ab7',
                                'data-toggle' => 'tooltip',
                                'title' => 'Registrar']) 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-pencil-alt" style="font-size: 15px; color: #FFC72C;"></em> Parametrizar Categorias: </label>
                            <?= Html::button('Parametrizar', ['value' => url::to(['parametrizarcategorias']), 'class' => 'btn btn-success', 'id'=>'modalButton5',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Parametrizar Categorias', 'style' => 'background-color: ##4298B4']) ?> 

                            <?php
                                Modal::begin([
                                  'header' => '<h4>Parametrizar Speech</h4>',
                                  'id' => 'modal5',
                                  //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent5'></div>";
                                                              
                                Modal::end(); 
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div class="capalist" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-list" style="font-size: 20px; color: #2CA53F;"></em> Listado configuracion de categorias: </label>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,

                        //'filterModel' => $searchModel,
                    'columns' => [            
                            [
                                'attribute' => 'Id Servicio',
                                'value' => 'id_dp_clientes',
                            ],
                            [
                                'attribute' => 'Cliente',

                                'value' => function($data){
                                    return $data->getArbolPadre($data->id_dp_clientes);
                                },
                            ],
                            [
                                'attribute' => 'Total Centros de Costos',
                                'value' => function($data){
                                    return $data->getTotalCC($data->id_dp_clientes);
                                },
                            ],
                            [
                                'attribute' => 'Total Categorias',
                                'value' => function($data){
                                    return $data->getTotalSCategorias($data->id_dp_clientes);
                                },
                            ],
                            [
                                'class' => 'yii\grid\ActionColumn',
                                'headerOptions' => ['style' => 'color:#337ab7'],
                                'template' => '{view}',
                                'buttons' => 
                                [
                                    'view' => function ($url, $model) {                        
                                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['categoriasview', 'txtServicioCategorias' => $model->id_dp_clientes], [
                                            'class' => '',
                                            'title' => 'Ver',
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
<?php 
    if ($sessiones == "2911" || $sessiones == "2953" || $sessiones == "3205" || $sessiones == "3229") {
?>
<div class="capaAdmin" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-lock" style="font-size: 20px; color: #ff2c2c;"></em> Administrativo: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <?= Html::a('Listar PCRC',  ['marcacionpcrc'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #4298B4',
                                'data-toggle' => 'tooltip',
                                'title' => 'Listar PCRC']) 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <?= Html::a('Categorias Calidad',  ['categoriasentto'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #337ab7',
                                'data-toggle' => 'tooltip',
                                'title' => 'Categorias Calidad']) 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <?= Html::button('Actualizar llamadas', ['value' => url::to(['actualizallamadasspeech']), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                'data-toggle' => 'tooltip',
                                'title' => 'Actualizar']) ?> 

                            <?php
                                Modal::begin([
                                  'header' => '<h4>Actualizar llamadas</h4>',
                                  'id' => 'modal1',
                                  //'size' => 'moda2-lg',
                                ]);

                                echo "<div id='modalContent1'></div>";
                                                              
                                Modal::end(); 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <?= Html::a('Clonar Extensiones',  ['clonarextension'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #337ab7',
                                'data-toggle' => 'tooltip',
                                'title' => 'Clonar extensiones']) 
                            ?>
                        </div>
                    </div>
                </div>
                <?php
                    if ($sessiones == '0') {            
                ?>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <?= Html::button('Generar Formula', ['value' => url::to(['generarformula']), 'class' => 'btn btn-danger', 'id'=>'modalButton2',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Generar Formula']) ?> 

                                <?php
                                    Modal::begin([
                                      'header' => '<h4>Generar Formula</h4>',
                                      'id' => 'modal2',
                                      //'size' => 'moda2-lg',
                                    ]);

                                    echo "<div id='modalContent2'></div>";
                                                                  
                                    Modal::end(); 
                                ?>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <?= Html::button('Parametrizar Datos', ['value' => url::to(['parametrizardatos']), 'class' => 'btn btn-danger', 'id'=>'modalButton3',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Parametrizar Datos']) ?> 

                                <?php
                                    Modal::begin([
                                      'header' => '<h4>Parametrizar Datos</h4>',
                                      'id' => 'modal3',
                                      //'size' => 'moda2-lg',
                                    ]);

                                    echo "<div id='modalContent3'></div>";
                                                                  
                                    Modal::end(); 
                                ?> 
                            </div>
                        </div>
                    </div>
                <?php }else{ if ($sessiones == "1543") {?>
                    <br>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <?= Html::a('Listar PCRC',  ['marcacionpcrc'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298B4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Listar PCRC']) 
                                ?>
                            </div>
                        </div>
                    </div>
                <?php } } ?>
            </div>
        </div>
    </div>
</div>

<?php } ?>
