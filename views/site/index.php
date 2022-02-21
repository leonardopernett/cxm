<?php

use yii\helpers\Html;
use yii\bootstrap\Carousel;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
$this->title = 'CX-Management';
?>

<style type="text/css">
    .card {
            height: auto;
            width: 400px;
            margin: auto;
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
            text-align: center;    
            
    }

    .card5 {
            height: auto;
            width: auto;
            margin: auto;
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
            text-align: center;    
            
    }

    .card:hover .card1 {
        top: -15%;
    }

    .card .card1 {
        width: 95%;
        height: 240px;
        background: #fff;
        border-radius: 5px;
        background-size: cover;
        background-position: center;
        position: absolute;
        left: 50%;
        top: -10%;
        -webkit-transform: translateX(-50%);
                transform: translateX(-50%);
        z-index: 10;
        -webkit-transition: 0.3s cubic-bezier(0.67, -0.12, 0.45, 1.5);
        transition: 0.3s cubic-bezier(0.67, -0.12, 0.45, 1.5);
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .card2 {
        height: 80px;
        width: 150px;
        
        display:block;
        margin:auto;
    }
    .container1 {
        margin: 0px;
        padding: 0px !important;
    }

    .row {
        margin-top: 19px;
    }

</style>

<div class="Principal">
    <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
    ?>
    <div class="container1">
    <br><br><br><br><br>
        <div class="row">
            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card">
                            <img class="card1" src="<?= Url::to("@web/images/VOC.png"); ?>" alt="Card image cap">
                            <div class="card-body">
                            <br><br><br><br><br><br><br>
                                <hr>
                                <p class="card-text">Escuchar, entender y analizar la percepción que tiene el cliente Corporativo sobre Konecta.</p>
                                <br>
                                <?= Html::button('<i class="fas fa-play-circle"></i>  Más información', ['value' => url::to(['dashboardvoz/vocvideo','varvideo' => 1]), 'class' => 'botonimagen btn btn-success', 
                                      'id'=>'modalButton1',
                                      'data-toggle' => 'tooltip',
                                      'title' => 'Más información >', 
                                      'style' => 'background-color: #2387A9']) ?> 

                                <?php
                                    Modal::begin([
                                                'header' => '<h4></h4>',
                                                'id' => 'modal1',
                                              ]);

                                    echo "<div id='modalContent1'></div>";
                                                                            
                                    Modal::end(); 
                                ?>
                                <hr>
                                <img class="card2" src="<?= Url::to("@web/images/CXManagement-82.png"); ?>" alt="Card image cap">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <img class="card1" src="<?= Url::to("@web/images/VOEC.png"); ?>" alt="Card image cap">
                            <div class="card-body">
                            <br><br><br><br><br><br><br>
                                <hr>
                                <p class="card-text">Conocer y gestionar la satisfacción de Konecta, ¡Empleados felices, clientes felices!</p>
                                <br>
                                <?= Html::button('<i class="fas fa-play-circle"></i>  Más información', ['value' => url::to(['dashboardvoz/vocvideo','varvideo' => 2]), 'class' => 'btn btn-success', 
                                      'id'=>'modalButton2',
                                      'data-toggle' => 'tooltip',
                                      'title' => 'Más información >', 
                                      'style' => 'background-color: #2387A9']) ?> 

                                <?php
                                    Modal::begin([
                                                'header' => '<h4></h4>',
                                                'id' => 'modal2',
                                              ]);

                                    echo "<div id='modalContent2'></div>";
                                                                            
                                    Modal::end(); 
                                ?>
                                <hr>
                                <img class="card2" src="<?= Url::to("@web/images/CXManagement-83.png"); ?>" alt="Card image cap">
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card">
                            <img class="card1" src="<?= Url::to("@web/images/VOUX2.png"); ?>" alt="Card image cap">
                            <div class="card-body">
                            <br><br><br><br><br><br><br>
                                <hr>
                                <p class="card-text">Escuchar, interpretar y analizar la Experiencia del Usuario con la Marca, Canal y Agente.</p>
                                <br>
                                <?= Html::button('<i class="fas fa-play-circle"></i>  Más información', ['value' => url::to(['dashboardvoz/vocvideo','varvideo' => 3]), 'class' => 'btn btn-success', 
                                      'id'=>'modalButton3',
                                      'data-toggle' => 'tooltip',
                                      'title' => 'Más información >', 
                                      'style' => 'background-color: #2387A9']) ?> 

                                <?php
                                    Modal::begin([
                                                'header' => '<h4></h4>',
                                                'id' => 'modal3',
                                              ]);

                                    echo "<div id='modalContent3'></div>";
                                                                            
                                    Modal::end(); 
                                ?>
                                <hr>
                                <img class="card2" src="<?= Url::to("@web/images/CXManagement-84.png"); ?>" alt="Card image cap">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>
<br>
<br>
<hr>