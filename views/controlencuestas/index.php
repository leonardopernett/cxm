<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;

$this->title = 'Parametrización Encuestas';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    // $query = Yii::$app->get('dbjarvis')->createCommand("select nombre_completo from dp_datos_generales where documento = '1035832753'")->queryAll();
    // var_dump($query);

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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
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
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Importar - SATU cliente corporativo: </label> 
                            <?= 
                                Html::button('Importar encuesta', ['value' => url::to(['importarexcel']), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Importar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Importar Archivo</h4>',
                                    'id' => 'modal2',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent2'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Importar - Encuestas de retiros: </label> 
                            <?= 
                                Html::button('Importar encuesta', ['value' => url::to(['importarexcel2']), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Importar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Importar Archivo</h4>',
                                    'id' => 'modal3',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent3'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Importar - Encuestas laboral: </label> 
                            <?= 
                                Html::button('Importar encuesta', ['value' => url::to(['importarexcel3']), 'class' => 'btn btn-success', 'id'=>'modalButton5', 'data-toggle' => 'tooltip', 'title' => 'Importar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Importar Archivo</h4>',
                                    'id' => 'modal5',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent5'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Importar - Encuestas de Héroes: </label> 
                            <?= 
                                Html::button('Importar encuesta', ['value' => url::to(['importarexcel4']), 'class' => 'btn btn-success', 'id'=>'modalButton6', 'data-toggle' => 'tooltip', 'title' => 'Importar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Importar Archivo</h4>',
                                    'id' => 'modal6',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent6'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #FFC72C;"></em> Importar - Encuestas ACI: </label> 
                            <?= 
                                Html::button('Importar encuesta', ['value' => url::to(['importarexcel5']), 'class' => 'btn btn-success', 'id'=>'modalButton7', 'data-toggle' => 'tooltip', 'title' => 'Importar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Importar Archivo</h4>',
                                    'id' => 'modal7',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent7'></div>";
                                                        
                                Modal::end(); 
                            ?> 
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 15px; color: #FFC72C;"></em> Registrar Encuestas: </label> 
                            <?= 
                                Html::button('Registrar', ['value' => url::to(['registrarencuestas']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Registrar'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4>Registrar Encuestas</h4>',
                                    'id' => 'modal1',
                                    //'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent1'></div>";
                                                        
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
<div class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em> Encuestas Cargadas: </label>
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                <caption>Encuestas</caption>
                    <thead>
                        <th scope="col" class="text-center"><?= Yii::t('app', 'Id Encuesta') ?></th>
                        <th scope="col" class="text-center"><?= Yii::t('app', 'Encuesta') ?></th>
                        <th scope="col" class="text-center"><?= Yii::t('app', 'Fecha Actualizada') ?></th>
                    </thead>
                    <tbody>
                        <?php
                            $varlist = Yii::$app->db->createCommand("select * from tbl_control_encuestas where anulado = 0")->queryAll();

                            foreach ($varlist as $key => $value) {    
                                $varidencuesta = $value['idlimeencuesta'];                            

                                $varfecha = Yii::$app->db->createCommand("select distinct max(fechacreacion) from tbl_control_encuestasatu where anulado = 0 and idlimeencuesta = $varidencuesta")->queryscalar();
                                $varfecha1 = Yii::$app->db->createCommand("select distinct max(fechacreacion) from tbl_control_encuestaretiro where anulado = 0 and idlimeencuesta = $varidencuesta")->queryscalar();
                                $varfecha2 = Yii::$app->db->createCommand("select distinct max(fechacreacion) from tbl_control_encuestalaboral where anulado = 0 and idlimeencuesta = $varidencuesta")->queryscalar();
                                $varfecha3 = Yii::$app->db->createCommand("select distinct max(fechacreacion) from tbl_control_encuestaheroes where anulado = 0 and idlimeencuesta = $varidencuesta")->queryscalar();
                                $varfecha4 = Yii::$app->db->createCommand("select distinct max(fechacreacion) from tbl_control_encuestaaci where anulado = 0 and idlimeencuesta = $varidencuesta")->queryscalar();

                                if ($varfecha != null) {
                                    $txtfecha = $varfecha;
                                }else{
                                    if ($varfecha1 != null) {
                                        $txtfecha = $varfecha1;
                                    }else{
                                        if ($varfecha2 != null) {
                                            $txtfecha = $varfecha2;
                                        }else{
                                            if ($varfecha3 != null) {
                                                $txtfecha = $varfecha3;
                                            }else{
                                                if ($varfecha4 != null) {
                                                    $txtfecha = $varfecha4;
                                                }else{
                                                    $txtfecha = "Sin Fecha";
                                                }
                                            }
                                        }
                                    }
                                }                        ?>
                        <tr>
                            <td class="text-center"><?php echo $value['idlimeencuesta']; ?></td>
                            <td class="text-center"><?php echo $value['nombreencuesta']; ?></td>
                            <td class="text-center"><?php echo $txtfecha; ?></td>
                        </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<hr>