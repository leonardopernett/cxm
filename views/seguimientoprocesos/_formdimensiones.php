<?php

use yii\helpers\Html;
/*use yii\widgets\ActiveForm;*/
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use \app\models\ControlProcesos;
use \app\models\ControlParams;
use kartik\export\ExportMenu;
use yii\db\Query;

$this->title = 'Seguimiento del tecnico';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $month = date('m');
    $year = date('Y');
    $day = date("d", mktime(0,0,0, $month+1, 0, $year));

$sessiones1 = Yii::$app->user->identity->id;

?>
<div class="control-procesos-index">
    <div class="text-center" style="text-align:left;">
    	<?= Html::a('Buscar por Valorador',  ['index'], ['class' => 'btn btn-success',
                'style' => 'background-color: #337ab7',
                'data-toggle' => 'tooltip',
                'title' => 'Buscar por Valorador']) 
        ?>        
        
        <?= Html::a('Buscar por PCRC',  ['searchpcrc'], ['class' => 'btn btn-success',
                'style' => 'background-color: #4298B4',
                'data-toggle' => 'tooltip',
                'title' => 'Buscar por PCRC']) 
        ?>
        
        <?= Html::a('Buscar por Fechas',  ['searchdate'], ['class' => 'btn btn-success',
                'style' => 'background-color: #707372',
                'data-toggle' => 'tooltip',
                'title' => 'Buscar por Fecha']) 
        ?>

        <?php if ($sessiones1 == 2953 || $sessiones1 == 2911 || $sessiones1 == 7 || $sessiones1 == 1525 || $sessiones1 == 438 || $sessiones1 == 1083) { ?>
            <?= Html::a('Buscar por Dimension',  ['dimensiones'], ['class' => 'btn btn-success',
                    'style' => 'background-color: #337ab7',
                    'data-toggle' => 'tooltip',
                    'title' => 'Buscar por Dimension']) 
            ?>  
            <?= Html::a('Buscar por Arbol Padre',  ['pcrcpadre'], ['class' => 'btn btn-success',
                    'style' => 'background-color: #337ab7',
                    'data-toggle' => 'tooltip',
                    'title' => 'Buscar por Dimension']) 
            ?>  
        <?php } ?> 
    </div>

    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?>

    <div class="row">
        <div class="col-md-offset-2 col-sm-8">
            
        </div>
    </div>

    <br>
    <br>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,        
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'Dimensiones',
                'value' => 'dimensions',
            ],
            [
                'attribute' => 'Meta',
                'value' => function($data) {
                    return $data->getMetasd($data->dimensions);
                }
            ],
            [
                'attribute' => 'Realizadas',
                'value' => function($data) {
                    return $data->getRealizadasd($data->dimensions);
                }
            ],
            [
                'attribute' => '% de Cumplimiento',
                'value' => function($data){
                    return $data->getCumplimientod($data->dimensions);
                }
            ],
            [
                'attribute' => 'Fecha Inicio',
                'value' => function($data){
                    return $data->getFechaInicial($data->fechacreacion);
                }
            ],
            [
                'attribute' => 'Fecha Fin',
                'value' => function($data){
                    return $data->getFechaFinal($data->fechacreacion);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{view}{stats}',
                'buttons' => 
                [
                    'view' => function ($url, $model) {                        
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['viewdimensions', 'dimensions' => $model->dimensions], [
                            'class' => '',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]);
                    },                   
                    'stats' => function ($url, $model) {                        
                        return Html::a('<span class=" glyphicon glyphicon-signal"></span>',  ['graficos5', 'dimensions' => $model->dimensions], [
                            'class' => '',
                            'title' => 'Detalles',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]);
                    }
                ]
              
            ],
        ],
    ]);   

    $fechainicio1 = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    $fechafin1 = date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    //$fechainicio = date('2018-08-22');
    //$fechafin = date('2018-08-30');  

    $varGrid = $dataProvider->getModels();
    
    $sumatoria1 = null;
    foreach ($varGrid as $key => $value) {
        $varTxt = $value['dimensions'];

        // $varRta = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and dimensions like '%$varTxt%' and fechacreacion between '$fechainicio1' and '$fechafin1'")->queryScalar();

        $querys =  new Query;
        $querys     ->select(['sum(cant_valor)'])
                    ->from('tbl_control_params')
                    ->where(['anulado' => '0'])
                    ->andwhere('dimensions like "%'.$varTxt.'%"')
                    ->andwhere(['between','fechacreacion', $fechainicio1, $fechafin1]);
                    
        $command = $querys->createCommand();
        $varRta = $command->queryScalar(); 
        
        $datosNum1 = $varRta;
        $sumatoria1 = $sumatoria1 + $datosNum1;  

    }
    $totalMeta = $sumatoria1;


    $sumatoria2 = null;
    foreach ($varGrid as $key => $value) {
        $varTxt = $value['dimensions'];

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_dimensions.name'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                            'tbl_ejecucionformularios.dimension_id = tbl_dimensions.id')
                    ->where(['between','tbl_ejecucionformularios.created', $fechainicio1, $fechafin1])
                    ->andwhere('tbl_dimensions.name like "%'.$varTxt.'%"');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();   

        $query = count($queryss);
        
        $sumatoria2 = $sumatoria2 + $query;
    }
    $totalRealizadas = $sumatoria2;



    //$totalCumpli = round(($totalRealizadas / $totalMeta) * 100);

        if($totalMeta!= 0 || $totalRealizadas != 0){
	        $totalCumpli = round(($totalRealizadas / $totalMeta) * 100);
	}else{
		$totalCumpli = 0;
	}

    ?>

    <?php $form->end() ?>
        <hr>
    <br>
    <table class="table table-striped table-hover table-bordered">
        <thead>
            <tr>
                <th class="text-center" style="font-size:12px;">Total Resultados...</th>
                <th class="text-center" style="font-size:12px;">Total Meta:</th>
                <th class="text-center" style="font-size:12px;">Total Realizadas:</th>
                <th class="text-center" style="font-size:12px;">Total % Cumplimiento: </th>
            </tr>
        </thead>

        <tbody>
            <tr class="text-center">
                <td></td>
                <td> <?php echo $totalMeta; ?> </td>
                <td> <?php echo $totalRealizadas; ?> </td>
                <td> <?php echo $totalCumpli; ?> % </td>
            </tr>
        </tbody>
    </table>
    
</div>