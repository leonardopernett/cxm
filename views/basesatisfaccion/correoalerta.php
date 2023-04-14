<?php

use yii\helpers\Html;



?>




        <!-- div del subtitilo azul principal que va llevar el nombre del modulo-->
        <div class="col-md-6">
            <div class="card1 mb" style="display:grid; place-items:center;">
                <img src='../../../images/cxx.png' style="width:120px;" >
                <h2>¡Hola Equipo!</h2>
                <h3><b>Haz recibido una nueva alerta</b></h3>
                <h4>Fecha de envio  :</h4>. $fecha 
                <h4>Tipo de alerta:</h4>. $tipo_alerta 
                <h4>Asunto:</h4> . $asunto  
                <h4>Programa PCRC:</h4> . $equipos['0']->name 
                <h4>Valorador:</h4> . $usuario['0']->usua_nombre  
                <h4>Comentarios:</h4>. $comentario  
                <br><br>
                <h4>Nos encataría saber tu opinión te invitamos a ingresar a <b>CXM</b> y responder nuestra encuesta.</h4>
                <br>
                <div style="heigth: 200px;">
                    <a href="encuestasatifaccion" class="btn btn-primary" target="_blank" >Ingresar a CXM</a>
                </div>
                <br>
                <img src='../../../images/link.png' class="img-responsive">  
            </div>
        </div>
  