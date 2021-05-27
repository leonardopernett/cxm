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
<html>
    <head>
        <meta charset="<?= Yii::$app->charset ?>"/>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <?= Html::csrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>        
        <?= Html::tag("link", "", ["rel" => "shortcut icon", "type" => "image/x-icon", "href" => Url::to("@web/favicon.ico")]); ?>
        <?php $this->head() ?>        
    </head>
    <body class="2body-login">
        <?php $this->beginBody() ?>
        <?= $content ?>
        <?php $this->endBody() ?>
        <video id="my-video" class="video" muted loop>
            <source src="<?= Url::to("@web/demo.mp4"); ?>" type="video/mp4">
            <source src="<?= Url::to("@web/demo.ogv"); ?>" type="video/ogg">
            <source src="<?= Url::to("@web/demo.webm"); ?>" type="video/webm">
        </video><!-- /video -->
        <script>
            (function () {
                /**
                 * Video element
                 * @type {HTMLElement}
                 */
                var video = document.getElementById("my-video");

                /**
                 * Check if video can play, and play it
                 */
                video.addEventListener("canplay", function () {
                    video.play();
                });
            })();
        </script>
    </body>
</html>
<?php $this->endPage() ?>