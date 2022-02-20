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

    $variables = Tipocortes::find()->where(['anulado' => '0'])->all();
    $listData = ArrayHelper::map($variables, 'tipocortetc', 'tipocortetc');

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
        <div class="col-md-offset-2 col-sm-8">
            <?= $form->field($model, 'tipo_corte')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'TipoCort'])->label('Tipo Corte') ?> 

            <div class="hidden">
                <?= $form->field($model, 'responsable')->textInput(['maxlength' => 200, 'value' => $sessiones, 'class'=>"hidden", 'label'=>""]) ?>         
            </div>

            <form align="center" method="post">            
                <input type="" name="valorado_id" class="invisible" id="valorado_id">
                <input value="<?php echo $txtvalorador ?>" id="txtvaloradorId"  class="invisible">   

                    <div class="text-center center-block">
                            <?= Html::submitButton(Yii::t('app', 'Buscar Fechas'),
                                ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Buscar Fechas']) 
                            ?>
                    </div>
            </form>            
        </div>
    </div>
    <br>    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,        
        //'filterModel' => $searchModel,
        'columns' => [
            [
                'attribute' => 'Valorador',
                'value' => 'usuarios.usua_nombre',
            ],
            [
                'attribute' => 'Meta',
                'value' => function($data){
                    return $data->getMetas($data->id, $data->evaluados_id);
                }
            ],
            [
                'attribute' => 'Realizadas',
                'value' => function($data) {
                    return $data->getRealizadas($data->evaluados_id);
                }
            ],
            [
                'attribute' => '% de Cumplimiento',
                'value' => function($data){
                    return $data->getCumplimiento($data->evaluados_id);
                }
            ],
            [
                'attribute' => 'Fecha Inicio',
                'value' => function($data){
                    return $data->getInicio($data->evaluados_id);
                }               
            ],
            [
                'attribute' => 'Fecha Fin',
                'value' => function($data){
                    return $data->getFin($data->evaluados_id);
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
                        return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['view', 'id' => $model->id, 'evaluados_id' => $model->evaluados_id], [
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
                        return Html::a('<span class=" glyphicon glyphicon-signal"></span>',  ['graficos3', 'evaluados_id' => $model->evaluados_id], [
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
        

        $sumatoria1 = null;
        $variablesId1 = $dataProvider->getModels();
        foreach ($variablesId1 as $key => $value) {
                $textoss1 = $value['evaluados_id'];
                $txtcorte = $value['tipo_corte'];

                $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();

             	$fechafinC = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();

                $varMetas = Yii::$app->db->createCommand('select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id ='.$textoss1.'')->queryScalar();

                $datosNum1 = $varMetas;
                $sumatoria1 = $sumatoria1 + $datosNum1;
        }

        $sumaMetas = $sumatoria1;


        $sumatoria2 = null;
        $variablesId2 = $dataProvider->getModels();
        foreach ($variablesId2 as $key => $value) {
                $textoss2 = $value['evaluados_id'];
                $txtcorte = $value['tipo_corte'];

                $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();

             	$fechafinC = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();

                

                $querys =  new Query;
                $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                                ->from('tbl_ejecucionformularios')
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                        'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                                ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
                                ->andwhere('tbl_usuarios.usua_id = '.$textoss2.'');
                                
                $command = $querys->createCommand();
                $queryss = $command->queryAll();   

                $varRealizadas = count($queryss);

                $datosNum = $varRealizadas;
                $sumatoria2 = $sumatoria2 + $datosNum;
        }

        $sumaRealizadas = $sumatoria2;

	if($sumaRealizadas != 0 || $sumaMetas != 0){
	        $sumaCumplimiento = round(($sumaRealizadas / $sumaMetas) * 100);
	}else{
		$sumaCumplimiento = 0;
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
                <td><?php echo $sumaMetas; ?></td>
                <td><?php echo $sumaRealizadas; ?></td>
                <td><?php echo $sumaCumplimiento; ?> % </td>
            </tr>
        </tbody>
    </table>

</div>