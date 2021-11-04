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

$this->title = 'Seguimiento del técnico';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $month = date('m');
    $year = date('Y');
    $day = date("d", mktime(0,0,0, $month+1, 0, $year));

    $sessiones1 = Yii::$app->user->identity->id;
    $txtArbolPadre = $varArbolPadre;

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

        <?php if ($sessiones1 == 2953 || $sessiones1 == 2911 || $sessiones1 == 7 || $sessiones1 == 1525 || $sessiones1 == 1083) { ?>
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

    <?php $form = ActiveForm::begin(['options' => ["id" => "buscarMasivos"],  'layout' => 'horizontal']); ?> 

    <div class="row">
        <?php if ($sessiones1 == 2953 || $sessiones1 == 2911 || $sessiones1 == 7 || $sessiones1 == 1525 || $sessiones1 == 1083) { ?>
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
            // [
            //     'class' => 'yii\grid\ActionColumn',
            //     'headerOptions' => ['style' => 'color:#337ab7'],
            //     'template' => '{view}{stats}',
            //     'buttons' => 
            //     [
            //         'view' => function ($url, $model) {                        
            //             return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['viewpcrc', 'arbolid' => $model->arbol_id], [
            //                 'class' => '',
            //                 'data' => [
            //                     'method' => 'post',
            //                 ],
            //             ]);
            //         },                   
            //         'stats' => function ($url, $model) {                        
            //             return Html::a('<span class=" glyphicon glyphicon-signal"></span>',  ['graficos4', 'arbolid' => $model->arbol_id], [
            //                 'class' => '',
            //                 'title' => 'Detalles',
            //                 'data' => [
            //                     'method' => 'post',
            //                 ],
            //             ]);
            //         }
            //     ]
              
            // ],
        ],
    ]);   

    $fechainicio1 = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    $fechafin1 = date('Y-m-d', mktime(0,0,0, $month, $day, $year));
    //$fechainicio = date('2018-08-22');
    //$fechafin = date('2018-08-30');  

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
                    ->where(['between','tbl_ejecucionformularios.created', $fechainicio1, $fechafin1])
                    ->andwhere('tbl_arbols.id = '.$varIdArbol.'');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();   

        $query = count($queryss);

        $sumatoria2 = $sumatoria2 + $query;        
    }
    $totalRealizadas = $sumatoria2;   


    $sumatoria3 = null;
    foreach ($varGrid as $key => $value) {
        $varIdArbol = $value['arbol_id'];

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_arbols.name'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                    ->where(['between','tbl_ejecucionformularios.created', $fechainicio1, $fechafin1])
                    ->andwhere('tbl_arbols.id = '.$varIdArbol.'');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();   

        $query1 = count($queryss);

        $query = new Query;
        $query  ->select(['round(('.$query1.' / (select sum(cant_valor) from tbl_control_params where anulado = 0 and arbol_id = '.$varIdArbol.' and fechacreacion between "'.$fechainicio1.'" and "'.$fechafin1.'")) * 100) as cumplimiento'])
                ->from('tbl_control_params');
        $command = $query->createCommand();
        $data = $command->queryScalar();


        $sumatoria3 = $sumatoria3 + $data;
    }


    $totalCumplimiento = $sumatoria3;

        if($totalMeta!= 0 || $totalRealizadas != 0){
            $totalCumplimiento = round(($totalRealizadas / $totalMeta) * 100);
    }else{
        $totalCumplimiento = 0;
    }

    ?>

    <?php $form->end() ?>
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
    <br>
	&nbsp;&nbsp;
        <button  class="btn btn-info" style="background-color: #337ab7" onclick="exportTableToExcel('tblData', 'Detalle Vista por PCRC_Padre')">Exportar a Excel por PCRC</button>
        &nbsp;&nbsp;
        <button  class="btn btn-info" style="background-color: #337ab7" onclick="exportTableToExcel('tblData2', 'Detalle Vista General')">Exportar a Excel General</button>
    <br>
    <br>
    <br>
</div>

<div style="display: none">

    <?php
        $txtNameArbol = Yii::$app->db->createCommand('select name from tbl_arbols where id = "'.$txtArbolPadre.'"')->queryScalar();

        $querys =  new Query;
        $querys     ->select(['tbl_arbols.id', 'tbl_arbols.name as PCRC', 'tbl_usuarios.usua_id', 'tbl_usuarios.usua_nombre as Valorador','tbl_usuarios.usua_identificacion as Identificacion'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                    ->where(['between','tbl_ejecucionformularios.created', $fechainicio1, $fechafin1])
                    ->andwhere(['tbl_arbols.arbol_id' => $txtArbolPadre]);
                    
        $command = $querys->createCommand();
        $query = $command->queryAll();   

    ?>
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
    <caption>Tabla datos</caption>
        <thead>
            <tr>
                <th id="pcrc" class="text-center" colspan="4"><?= Yii::t('app', 'PCRC') ?></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-center" colspan="4"><?= $txtNameArbol; ?></td>
            </tr>
            <tr>
                <th id="pcrc" class="text-center" ><?= Yii::t('app', 'PCRC') ?></th>
                <th id="identificacion" class="text-center" ><?= Yii::t('app', 'Identificacion') ?></th>
                <th id="valorador" class="text-center" ><?= Yii::t('app', 'Valorador') ?></th>
                <th id="realizadas" class="text-center" ><?= Yii::t('app', 'Realizadas') ?></th>  
            </tr>
            <?php 
                foreach ($query as $key => $value) {
                    $varID = $value['id'];
                    $txtpcrc = $value['PCRC'];
                    $txtIdUsu = $value['usua_id'];
                    $txtNameVal = $value['Valorador'];
                    $txtIdentidad = $value['Identificacion'];

                    $queryss =  new Query;
                    $queryss     ->select(['tbl_ejecucionformularios.created'])->distinct()
                                ->from('tbl_ejecucionformularios')
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                        'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')                                
                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                        'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                ->where(['between','tbl_ejecucionformularios.created', $fechainicio1, $fechafin1])
                                ->andwhere('tbl_arbols.id = '.$varID.'')
                                ->andwhere('tbl_usuarios.usua_id = '.$txtIdUsu.'');
                                
                    $command = $queryss->createCommand();
                    $query2 = $command->queryAll();   


                    $query23 = count($query2);

            ?>
            <tr>
                <td class="text-center"><?= $txtpcrc; ?></td>
                <td class="text-center"><?= $txtIdentidad; ?></td>
                <td class="text-center"><?= $txtNameVal; ?></td>
                <td class="text-center"><?= $query23; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<div style="display: none">

    <?php
        
        $querysx =  new Query;
        $querysx     ->select(['tbl_arbols.arbol_id','tbl_arbols.id', 'tbl_arbols.name as PCRC', 'tbl_usuarios.usua_id', 'tbl_usuarios.usua_nombre as Valorador','tbl_usuarios.usua_identificacion as Identificacion','tbl_roles.role_nombre'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')

                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')

                    ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')

                    ->join('LEFT OUTER JOIN', 'tbl_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')  

                    ->where(['between','tbl_ejecucionformularios.created', $fechainicio1, $fechafin1]);
                    
        $commandx = $querysx->createCommand();
        $queryx = $commandx->queryAll();   

    ?>
    <table id="tblData2" class="table table-striped table-bordered tblResDetFreed">
    <caption>...</caption>
        <thead>
            <tr>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'ArbolPadre') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'PCRC') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Roles') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Identificacion') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Valorador') ?></th>
                <th scope="col" class="text-center" ><?= Yii::t('app', 'Realizadas') ?></th>  
            </tr>
        </thead>
        <tbody>
            <?php 
                foreach ($queryx as $key => $value) {
                    $varArbolx = $value['arbol_id'];
                    $varIDx = $value['id'];
                    $txtpcrcx = $value['PCRC'];
                    $txtIdUsux = $value['usua_id'];
                    $txtNameValx = $value['Valorador'];
                    $txtIdentidadx = $value['Identificacion'];
                    $txtRoles = $value['role_nombre'];

                    $txtNameArbolx = Yii::$app->db->createCommand('select name from tbl_arbols where id = "'.$varArbolx.'"')->queryScalar();

                    $queryssx =  new Query;
                    $queryssx     ->select(['tbl_ejecucionformularios.created'])->distinct()
                                ->from('tbl_ejecucionformularios')
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                        'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')                                
                                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                                        'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                                ->where(['between','tbl_ejecucionformularios.created', $fechainicio1, $fechafin1])
                                ->andwhere('tbl_arbols.id = '.$varIDx.'')
                                ->andwhere('tbl_usuarios.usua_id = '.$txtIdUsux.'');
                                
                    $commandx = $queryssx->createCommand();
                    $query2x = $commandx->queryAll();   


                    $query23x = count($query2x);

            ?>
            <tr>
                <td class="text-center"><?= $txtNameArbolx; ?></td>
                <td class="text-center"><?= $txtpcrcx; ?></td>
                <td class="text-center"><?= $txtRoles; ?></td>
                <td class="text-center"><?= $txtIdentidadx; ?></td>
                <td class="text-center"><?= $txtNameValx; ?></td>
                <td class="text-center"><?= $query23x; ?></td>
            </tr>
            <?php } ?>
        </tbody>
    </table>
</div>


<script type="text/javascript">
  function exportTableToExcel(tableID, filename = ''){
      var downloadLink;
      var dataType = 'application/vnd.ms-excel';
      var tableSelect = document.getElementById(tableID);
      var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
      
      // Specify file name
      filename = filename?filename+'.xls':'excel_data.xls';
      
      // Create download link element
      downloadLink = document.createElement("a");
      
      document.body.appendChild(downloadLink);
      
      if(navigator.msSaveOrOpenBlob){
          var blob = new Blob(['\ufeff', tableHTML], {
              type: dataType
          });
          navigator.msSaveOrOpenBlob( blob, filename);
      }else{
          // Create a link to the file
          downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
      
          // Setting the file name
          downloadLink.download = filename;
          
          //triggering the function
          downloadLink.click();
      }
  }
</script>