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

$this->title = 'DashBoard -- Métricas de Productividad Valoración --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Métricas de Productividad/Valoración';

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
<div class="container-fluid">
    <div class="panel-group">
        <div class="panel panel-primary">
            <div class="panel-heading">VOLUMEN POR CLIENTE</div>
            <div class="panel-body">
                <?= Html::a('Actualizar Datos de QA',  ['parametrizardatos2'], ['class' => 'btn btn-success',
                            'style' => 'background-color: #4298b4',
                            'data-toggle' => 'tooltip',
                            'title' => 'Actualizar Volúmen x Cliente']) 
                ?>  

                <?= Html::a('Actualizar Datos de Speech',  ['parametrizardatos5'], ['class' => 'btn btn-success',
                            'style' => 'background-color: #4298b4',
                            'data-toggle' => 'tooltip',
                            'title' => 'Actualizar Volúmen x Cliente']) 
                ?>  
            </div>
        </div>


        <div class="panel panel-primary">
            <div class="panel-heading">VOLUMEN POR DIA</div>
            <div class="panel-body">
                <?= Html::a('Actualizar Datos de QA Diario',  ['parametrizardatosdayqa'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Actualizar Volúmen x Cliente Diario']) 
                ?> 

                <?= Html::a('Actualizar Datos de Speech Diario',  ['parametrizardatosdaysp'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Actualizar Volúmen x Cliente Diario']) 
                ?> 

 
            </div>
        </div>


        <div class="panel panel-primary">
            <div class="panel-heading">VOLUMEN POR VALORADOR</div>
            <div class="panel-body">
                <?= Html::a('Actualizar Datos de QA',  ['parametrizardatos3'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Actualizar Volúmen x Valorador']) 
                ?> 


            </div>
        </div>

        <div class="panel panel-primary">
            <div class="panel-heading">COSTOS POR CLIENTE</div>
            <div class="panel-body">
                <?= Html::a('Actualizar Datos de QA',  ['parametrizardatos4'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #4298b4',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Actualizar Costos x Cliente']) 
                ?>  

 
            </div>
        </div>  

	<div class="panel panel-primary">
            <div class="panel-heading">ENCUESTAS POR DIA</div>
            <div class="panel-body">
                <?= Html::a('Actualizar Datos de QA',  ['parametrizarencuestasdq'], ['class' => 'btn btn-success',
                            'style' => 'background-color: #4298b4',
                            'data-toggle' => 'tooltip',
                            'title' => 'Actualizar Volúmen x Cliente']) 
                ?>             
            </div>
        </div>  
    </div>
</div>