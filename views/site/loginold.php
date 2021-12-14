<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model app\models\LoginForm */

$this->title = 'Login';
?>    


<div class="site-login" style="text-align: center;">     
    <div class="login-box">
        <div class="login-box-body">
            <h4>QA Management</h4>            
            
            <?php
            $form = ActiveForm::begin([
                        'id' => 'login-form',
                        //'options' => ['class' => 'form-horizontal'],
                        'options' => ['class' => 'form-group'],
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

          

            <div class="form-group">


                <?=
                $form->field($model, 'username', ['inputOptions' => ['placeholder' => 'Usuario'],
                    'inputTemplate' => '<div class="has-feedback">'
                    . '<span class="glyphicon glyphicon-user form-control-feedback"></span>'
                    . '{input}</div>'])->label(false/*
                          Html::img(Url::to("@web/images/ico-login.png")
                          , ["class" => "img-responsive",
                          "style" => "text-align: left; float: left; height: 30px;"])
                          . Yii::t("app", "Usuario") */)
                ?>
                <?=
                $form->field($model, 'password', ['inputOptions' => ['placeholder' => 'Clave'],
                    'inputTemplate' => '<div class="has-feedback">'
                    . '<span class="glyphicon glyphicon-lock form-control-feedback"></span>'
                    . '{input}</div>'])->passwordInput()->label(false/*
                          Html::img(Url::to("@web/images/clave_ic.png")
                          , ["class" => "img-responsive",
                          "style" => "text-align: left; float: left; height: 30px;"])
                          . Yii::t("app", "Clave") */)
                ?>

                <div class="form-group">
                    <div class="" style="margin-top: 10px;">
                        <?=
                        Html::submitButton('Ingresar', ['class' => 'btn btn-success', 'name' => 'login-button'])
                        ?>
                    </div>        
                </div>

            </div>

            <div class="logoAllus">
                <?= Html::img("@web/images/Allus.png", ['style' => 'width: 100px;']); ?>
            </div>

            <?php ActiveForm::end(); ?>

        </div>    
    </div>    
</div>    


