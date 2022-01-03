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
    /*background: #4298B5;*/
    /* fallback for old browsers * #5cc9ed/
  background: -webkit-linear-gradient(to bottom, #fff, #b7dfeb);  /* Chrome 10-25, Safari 5.1-6 */
    /*background: linear-gradient(to bottom, #fff, #b7dfeb); */
    /* W3C, IE 10+/ Edge, Firefox 16+, Chrome 26+, Opera 12+, Safari 7+ */
    float: left;
    width: 100%;
    /*padding : 125px 0;*/
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
    /*box-shadow:15px 20px 0px rgba(0,0,0,0.2)*/
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

  /*.logok{width: 120px; height: 35px; margin-bottom:20px; margin-top:25px;margin-left:115px;margin-right:20px;} */
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
                              //'options' => ['class' => 'form-horizontal'],
                              'options' => ['class' => 'form-group '],
                              'errorSummaryCssClass' => 'alert alert-danger',
                              'fieldConfig' => [
                                'inputOptions' => ['autocomplete' => 'off']
                              ]
                                  /* 'fieldConfig' => [
                                  'template' => "{label}\n<div class=\"col-lg-4\">{input}</div>\n<div class=\"col-lg-8\">{error}</div>",
                                  'labelOptions' => ['class' => 'col-lg-4 control-label'],
                                  ], */
        ]);
        ?>
        <div class="form-group sizelet">
          <?=
          $form->field($model, 'username', [
            'inputOptions' => ['placeholder' => 'Usuario', 'style' => 'font-size:15px; border-radius: 13px;'],
            'inputTemplate' => '<div class="has-feedback sizelet">'
              . '<span class="glyphicon glyphicon-user form-control-feedback sizelet colorinp" ></span>'
              . '{input}</div>'
          ])->label(false/*
                              Html::img(Url::to("@web/images/ico-login.png")
                              , ["class" => "img-responsive",
                              "style" => "text-align: left; float: left; height: 30px;"])
                              . Yii::t("app", "Usuario") */)
          ?>
          <?=
          $form->field($model, 'password', [
            'inputOptions' => ['placeholder' => 'Clave', 'style' => 'font-size:15px; border-radius: 13px;'],
            'inputTemplate' => '<div class="has-feedback sizelet">'
              . '<span class="glyphicon glyphicon-lock form-control-feedback sizelet colorinp"></span>'
              . '{input}</div>'
          ])->passwordInput()->label(false/*
                              Html::img(Url::to("@web/images/clave_ic.png")
                              , ["class" => "img-responsive",
                              "style" => "text-align: left; float: left; height: 30px;"])
                              . Yii::t("app", "Clave") */)
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