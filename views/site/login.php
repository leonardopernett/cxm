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
use app\assets\AppAsset;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$varFechas = date('m');

$this->title = 'CX-Management';
?>
<link href="//maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" rel="stylesheet" id="bootstrap-css">
<script src="../../js_extensions/bootstrap.min.js"></script>
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<?php AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<style>

  .login-block {
    float: left;
    width: 100%;
    height: 100%;
    font-family: 'Numans', sans-serif !important;
    display: flex;
    flex-direction: row;
    flex-wrap: wrap;
    justify-content: center;
    align-items: center;

  }

  .banner-sec {
    background: src="../../images/imagenn4.png"no-repeat left bottom;
    background-size: over;
    min-height: 360px;
    border-radius: 0 10px 10px 0;
    padding: 0;
  }

  .container {
    background: #fff;
    border-radius: 10px 10px 10px 10px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

  .carousel-inner {
    border-radius: 0 10px 10px 0;
  }

  .carousel-caption {
    text-align: left;
    left: 5%;
  }

  .login-sec {
    padding: 50px 30px;
    position: relative;
  }

  .login-sec .copy-text {
    position: absolute;
    width: 30%;
    bottom: 20px;
    font-size: 13px;
    text-align: center;
  }

  .login-sec .copy-text i {
    color: #FEB58A;
  }

  .login-sec .copy-text a {
    color: #E36262;
  }

  .login-sec h2 {
    margin-bottom: 30px;
    font-weight: 800;
    font-size: 30px;
    color: #DE6262;
  }

  .login-sec h2:after {
    content: " ";
    width: 100px;
    height: 5px;
    background: #FEB58A;
    display: block;
    margin-top: 20px;
    border-radius: 3px;
    margin-left: auto;
    margin-right: auto
  }

  .btn-login {
    background: #DE6262;
    color: #fff;
    font-weight: 600;
  }

  .banner-text {
    width: 80%;
    position: absolute;
    bottom: 40px;
    padding-left: 20px;
  }

  .banner-text h2 {
    color: #fff;
    font-weight: 600;
  }

  .banner-text h2:after {
    content: " ";
    width: 100px;
    height: 5px;
    background: #FFF;
    display: block;
    margin-top: 20px;
    border-radius: 3px;
  }

  .banner-text p {
    color: #fff;
  }

  .brand_logo_login {
    display: block;
    margin: auto;
  }

  .logok {
    display: block;
    margin: auto;
  }

  .sizelet {
    font-size: 15px;
  }

  .colorinp {
    color: #858A8A;
  }

  .nana {
    width: 0px;
  }

  .center-h {
    justify-content: center;
  }

  .center-v {
    align-items: center;
  }

  .boton_acep {
    width: 150px;
    margin: 0 auto;
    font-size: 16px;
  }
</style>




<div class='luces'></div>
<?php $this->beginBody() ?>
<?php $this->endBody() ?>
<?php $this->endPage() ?>

<section class="login-block">
  <div class="container">
    <div class="row">
      <div class="col-md-4 login-sec input-lg">
          <div >
                  <img src="../../images/cx.png" class="brand_logo_login" alt="logo_login">
		  
          </div>
          
          <?php
                  $form = ActiveForm::begin([
                              'id' => 'login-form',
                              'options' => ['class' => 'form-group '],
                              'errorSummaryCssClass' => 'alert alert-danger',
                              'fieldConfig' => [
                                'inputOptions' => ['autocomplete' => 'off']
                              ]
        ]);
        ?>
        <div class="form-group sizelet">
          <?=
          $form->field($model, 'username', [
            'inputOptions' => ['placeholder' => 'Usuario', 'style' => 'font-size:15px; border-radius: 13px;'],
            'inputTemplate' => '<div class="has-feedback sizelet">'
              . '<span class="glyphicon glyphicon-user form-control-feedback sizelet colorinp" ></span>'
              . '{input}</div>'
          ])->label(false)
          ?>
          <?=
          $form->field($model, 'password', [
            'inputOptions' => ['placeholder' => 'Clave', 'style' => 'font-size:15px; border-radius: 13px;'],
            'inputTemplate' => '<div class="has-feedback sizelet">'
              . '<span class="glyphicon glyphicon-lock form-control-feedback sizelet colorinp"></span>'
              . '{input}</div>'
          ])->passwordInput()->label(false)
          ?>
          <br>
        </div>
        <div class="form-group">
          <div class="" style="margin-top: 10px;text-align:center;">
            <?=
            Html::submitButton('Ingresar', ['class' => 'btn btn-primary btn-lg boton_acep', 'name' => 'login-button', 'style' => 'border-radius: 15px; background:#0EAA9F'])
            ?>
            <br>
            <br>
          </div>
        </div>
        <?php ActiveForm::end(); ?>

        <div class="copy-tex"><em class="nana"></em></div>
        <br>
        <div>
          <br>
          <br>
          <img src="../../images/knew.png" class="logok" alt="logo">
        </div>

      </div>
      <div class="col-md-8 banner-sec">
        <div id="carouselExampleIndicators" class="carousel slide" data-ride="carousel">
          <ol class="carousel-indicators">
            <li data-target="#carouselExampleIndicators" data-slide-to="0" class="active"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="1"></li>
            <li data-target="#carouselExampleIndicators" data-slide-to="2"></li>
          </ol>
          <div class="carousel-inner" role="listbox">
            <div class="carousel-item active">
              <img class="d-block img-fluid" src="../../images/login1.png" alt="First slide">
              <div class="carousel-caption d-none d-md-block">
                <div class="banner-text">
                </div>
              </div>
            </div>
            <div class="carousel-item">
              <img class="d-block img-fluid" src="../../images/login2.png" alt="First slide">
              <div class="carousel-caption d-none d-md-block">
                <div class="banner-text"></div>
              </div>
            </div>
            <div class="carousel-item">
              <img class="d-block img-fluid" src="../../images/login3.png" alt="First slide">
              <div class="carousel-caption d-none d-md-block">
                <div class="banner-text">
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
</section>

<script>
  function snow(){
    // 1. Defina una plantilla de copo de nieve
    var flake = document.createElement('div');

    // Personaje de copo de nieve ❄❉❅❆✻✼❇❈❊✥✺
    flake.innerHTML = '*';
    flake.style.cssText = 'position:fixed;color:#fff;';

    // Obtiene la altura de la página, que es equivalente a la posición del eje Y cuando caen los copos de nieve
    var documentHieght = window.innerHeight;
    // Obtenga el ancho de la página, use este número para calcular, el valor de la izquierda cuando comienza el copo de nieve
    var documentWidth = window.innerWidth;

    // Define la cantidad de milisegundos para generar un copo de nieve
    var millisec = 100;
    // 2, establece el primer temporizador, un temporizador periódico y genera un copo de nieve cada vez (milisegundos);
    setInterval(function() { 
      // Una vez que se carga la página, el temporizador comienza a funcionar      
      // Genera aleatoriamente el valor de left al principio de la caída del copo de nieve, que es equivalente a la posición del eje X al principio

      var startLeft = Math.random() * documentWidth;

      // Genera aleatoriamente el valor de left al final de la caída del copo de nieve, que es equivalente a la posición del eje X al final
      var endLeft = Math.random() * documentWidth;

      // Generar aleatoriamente el tamaño del copo de nieve
      var flakeSize = 10 + 20 * Math.random();

      // Genera aleatoriamente la duración de la caída de nieve
      var durationTime = 4000 + 7000 * Math.random();

      // Genera aleatoriamente la transparencia al comienzo de la caída del copo de nieve
      var startOpacity = 0.7 + 0.3 * Math.random();

      // Genera aleatoriamente la transparencia al final de la caída de los copos de nieve
      var endOpacity = 0.2 + 0.2 * Math.random();

      // Clonar una plantilla de copo de nieve
      var cloneFlake = flake.cloneNode(true);

      // Modifica el estilo por primera vez, define el estilo del copo de nieve clonado
      cloneFlake.style.cssText += `
        left: ${startLeft}px;
        opacity: ${startOpacity};
        font-size:${flakeSize}px;
        top:-25px;
        transition:${durationTime}ms;
      `;

      // Empalmado en la página
      document.body.appendChild(cloneFlake);

      // Establecer el segundo temporizador, temporizador de una sola vez,
      // Cuando el primer temporizador genera copos de nieve y los muestra en la página, modifique el estilo de los copos de nieve para que se muevan;
      setTimeout(function(){
        // Modifica el estilo por segunda vez
        cloneFlake.style.cssText += `
          left: ${endLeft}px;
          top:${documentHieght}px;
          opacity:${endOpacity};        
        `;

        // 4. Configure el tercer temporizador y elimine el copo de nieve cuando caiga.
        setTimeout(function() {
          cloneFlake.remove();
         }, durationTime);

      },0);

    },millisec);
  };

  snow();
</script>