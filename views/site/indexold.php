<?php

use yii\helpers\Html;
use yii\bootstrap\Carousel;
use yii\helpers\Url;

/* @var $this yii\web\View */
$this->title = 'QA Management';
?>

<div class="site-index">
    <?php
        foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
            echo '<div class="alert alert-' . $key . '">' . $message . '</div>';
        }
    ?>
    <div class="row">
        <div class="col-lg-5 noticias">

            <div class="info-usuario">
                <table>
                    <tr>
                        <td><?php echo Html::img(Url::to("@web/images/ico-user.png"), ["alt" => "Noticias", "style" => "align: left"]); ?></td>
                        <td>
                            <span class="bienvenido">
                                <?php echo Yii::t('app', 'Bienvenido'); ?>                                
                            </span>
                            <br />
                            <span class="inicio-noticias">                                
                                <?= Yii::$app->user->identity->fullName ?>                                                                                                
                            </span>
                        </td>
                    </tr>
                </table>
            </div>

            <?php echo Html::img(Url::to("@web/images/ico-noticia.png"), ["alt" => "Noticias"]); ?>
            <span class="inicio-noticias"><?php echo Yii::t('app', 'Lo que debes saber'); ?></span>
            <?php if (empty($noticias)) : ?>
                <p>
                    <?php echo Yii::t('app', 'No hay noticias para mostrar'); ?>
                </p>
            <?php else: ?>

                <?php foreach ($noticias as $noticia) : ?>
                    <h1 class="titu-noticia"><?php echo $noticia->titulo ?></h1>  
                    <?php echo substr($noticia->descripcion, 0, 250) . "..."; ?>
                    <br /><br />
                    <?php
                    echo Html::a(Yii::t('app', 'read more')
                            , \yii\helpers\Url::to(['noticias/detalle'
                                , 'id' => $noticia->id]));
                    ?>
                <?php endforeach; ?>
            <?php endif; ?>

        </div>

        <div class="col-lg-7" style="">
            <?php
            foreach ($galeria as $gal) {
                $content[] = [
                    'content' => Html::img(Url::to("@web/images/uploads/") . $gal->imagen),
                    'caption' => '<h4>' . $gal->titulo . '</h4><p>' . $gal->descripcion . '</p>',
                ];
            }

            echo Carousel::widget([
                'items' => $content,
                'controls' => [
                    '<span aria-hidden="true" class="glyphicon glyphicon-chevron-left"></span>
                    <span class="sr-only">Previous</span>',
                    '<span aria-hidden="true" class="glyphicon glyphicon-chevron-right"></span>
                    <span class="sr-only">Next</span>'
                ],
                'options' => [
                    'class' => 'slide',
                    'id' => 'carousel-home'
                ]
            ]);
            ?>            
        </div>
    </div>
</div>