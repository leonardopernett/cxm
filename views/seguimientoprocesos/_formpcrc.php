<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use app\models\Tipocortes;
use yii\helpers\ArrayHelper;

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
                'title' => 'Buscar por Fechas']) 
        ?> 

        <?php if ($sessiones1 == 2953 || $sessiones1 == 2911 || $sessiones1 == 7 || $sessiones1 == 1525 || $sessiones1 == 438 || $sessiones1 == 1083 ) { ?>
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
    <br>
    <br>

    <?php $form = ActiveForm::begin([
        'options' => ["id" => "buscarMasivos"],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?> 

    <div class="row">
        <?php if ($sessiones1 == 2953 || $sessiones1 == 2911 || $sessiones1 == 7 || $sessiones1 == 1525 || $sessiones1 == 70 || $sessiones1 == 438 || $sessiones1 == 1083) { ?>
        <div class="col-md-12">
            <?=
                $form->field($model, 'arbol_id')
                    ->widget(Select2::classname(), [                
                        'language' => 'es',
                        'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                        'pluginOptions' => [
                            'initialize' => true,
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                var id=$(element).val();
                            }')
                        ]
                    ]
                    );
            ?> 
            
            <div class="hidden">
                <?= $form->field($model, 'responsable')->textInput(['maxlength' => 200, 'value' => $sessiones, 'class'=>"hidden", 'label'=>""]) ?>         
            </div>

            <form align="center" method="post">            
                <input type="" name="valorado_id" class="invisible" id="valorado_id">
                <input value="<?php echo $txtvalorador ?>" id="txtvaloradorId"  class="invisible">   

                    <div class="text-center center-block">
                            <?= Html::submitButton(Yii::t('app', 'Buscar PCRC'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Buscar Pcrc']) 
                            ?>
                    </div>
            </form>            
        </div>
        <?php } ?>
    </div>
    <br>    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,        
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'PCRC',
                'value' => 'arboles.name',
            ],
            [
                'attribute' => 'Meta',
                'value' => function($data) {
                    return $data->getMetascp($data->arbol_id);
                }
            ],
            [
                'attribute' => 'Realizadas',
                'value' => function($data) {
                    return $data->getRealizadascp($data->arbol_id);
                }
            ],
            [
                'attribute' => '% de Cumplimiento',
                'value' => function($data){
                    return $data->getCumplimientocp($data->arbol_id);
                }
            ],
            [
                'attribute' => 'Fecha Inicio',
                'value' => function($data){
                    return $data->getFechaInicial($data->arbol_id);
                }
            ],
            [
                'attribute' => 'Fecha Fin',
                'value' => function($data){
                    return $data->getFechaFinal($data->arbol_id);
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'headerOptions' => ['style' => 'color:#337ab7'],
                'template' => '{view}{stats}',
                'buttons' => 
                [
                    'view' => function ($url, $model) {                        
                        if ($url == "asda") {
                            #code...
                        }
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['viewpcrc', 'arbolid' => $model->arbol_id], [
                            'class' => '',
                            'data' => [
                                'method' => 'post',
                            ],
                        ]);
                    },                   
                    'stats' => function ($url, $model) {
                        if ($url == "asda") {
                            #code...
                        }                        
                        return Html::a('<span class=" glyphicon glyphicon-signal"></span>',  ['graficos4', 'arbolid' => $model->arbol_id], [
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
    

    $varGrid = $dataProvider->getModels();

    $sumatoria1 = null;
    foreach ($varGrid as $key => $value) {
        $varIdArbol = $value['arbol_id'];
        $valores = Yii::$app->db->createCommand('select sum(cant_valor) from tbl_control_params where anulado = 0 and arbol_id ='.$varIdArbol.' and fechacreacion between "'.$fechainicio1.'" and "'.$fechafin1.'"')->queryScalar();

        $sumatoria1 = $sumatoria1 + $valores;
    }
    $totalMeta = $sumatoria1;

    
    $sumatoria2 = null;
    foreach ($varGrid as $key => $value) {
        $varIdArbol = $value['arbol_id'];

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_arbols.name'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')

                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')

                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')

                    ->join('LEFT OUTER JOIN', 'tbl_roles',
                            'rel_usuarios_roles.rel_role_id  = tbl_roles.role_id')

                    ->where(['between','tbl_ejecucionformularios.created', $fechainicio1, $fechafin1])
                    ->andwhere('tbl_arbols.id = '.$varIdArbol.'')
                    ->andwhere(['in','tbl_roles.role_id',[272,292]]);    
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();   

        $query = count($queryss);

        $sumatoria2 = $sumatoria2 + $query;        
    }
    $totalRealizadas = $sumatoria2;   


if($totalMeta != 0 || $totalRealizadas != 0){
    $totalCumplimiento = round(($totalRealizadas / $totalMeta) * 100);
}else{
$totalCumplimiento = 0;
}
    ?>

    <?php $form->end() ?>
    <hr>
    <br>
    <table class="table table-striped table-hover table-bordered">
    <caption>Total</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center" style="font-size:12px;">Total Resultados...</th>
                <th scope="col" class="text-center" style="font-size:12px;">Total Meta:</th>
                <th scope="col" class="text-center" style="font-size:12px;">Total Realizadas:</th>
                <th scope="col" class="text-center" style="font-size:12px;">Total % Cumplimiento: </th>
            </tr>
        </thead>

        <tbody>
            <tr class="text-center">
                <td></td>
                <td> <?php echo $totalMeta; ?> </td>
                <td> <?php echo $totalRealizadas; ?> </td>
                <td> <?php echo $totalCumplimiento; ?> % </td>
            </tr>
        </tbody>
    </table>

</div>