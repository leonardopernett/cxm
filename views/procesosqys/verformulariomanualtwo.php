<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$sesiones =Yii::$app->user->identity->id;   
?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css" >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
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

    .card:hover .card1:hover {
        top: -15%;
    }

</style>
<!-- Capa Procesos Formularios -->
<div id="capaFormsId" class="capaForms" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Ficha Técnica '.$varNombreCodPcrcIdeal) ?></label>
            </div>
        </div>
    </div>

    <br>

    <?php
    $varContadorForms = 0;
    foreach ($varListaIdForms as $key => $value) {
        $varContadorForms += 1;   
        $usua_id =  $varUsua_id.$varContadorForms;
                
        //Conulta de calificaciones --------------------------------------------        
        $data = \app\models\Tmpreportes::find()->where(['usua_id' => $usua_id])
                ->orderBy('id ASC')->asArray()->all();

        if (!empty($data) && count($data) > 0) {
            $arrIgnoredCols = array();    
            $html = '';
            $thead = '<thead><tr>';
            $tbody = '<tbody>';
            foreach ($data as $i => $row) {
                $tbody.= '<tr>';
                foreach ($row as $cell_name => $cell_val) {
                    if ($cell_name == 'id' || $cell_name == 'usua_id'){ 
                        continue;                 
                    }
                    if ($i == 0) {
                        if (empty($cell_val)) {
                            $arrIgnoredCols[] = $cell_name;
                            continue;
                        }
                        $thead.= '<th>' . $cell_val . '</th>';
                    } else {
                        if (in_array($cell_name, $arrIgnoredCols)){
                            continue;
                        }
                        $tbody.= '<td>' . $cell_val . '</td>';
                    }
                }
                $tbody.= '</tr>';
            }

            $thead.= '</tr></thead>';
            $tbody.= '</tbody>';
            $html = '<table class="table table-striped table-bordered">' . $thead 
                    . $tbody . '</table>';
            echo $html;
        }
    }
    ?>

</div>