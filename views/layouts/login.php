<?php

use yii\helpers\Html;
use app\assets\AppAsset;
use yii\helpers\Url;

/* @var $this \yii\web\View */
/* @var $content string */

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<!--
To change this license header, choose License Headers in Project Properties.
To change this template file, choose Tools | Templates
and open the template in the editor.
-->
<html lang="es">
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>        
        <?= Html::tag("link", "", ["rel" => "shortcut icon", "type" => "image/x-icon", "href" => Url::to("@web/30x30.png")]); ?>
        <?php $this->head() ?>        
    </head>
    <body class="2body-login" style="background-image: linear-gradient(to bottom, #fff, #b8fcf7); !important;">
        <?php $this->beginBody() ?>
        <?= $content ?>
        <?php $this->endBody() ?> 
    </body>
</html>
<?php $this->endPage() ?>
