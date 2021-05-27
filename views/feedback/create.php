<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model app\models\Noticias */

$this->title = Yii::t('app', 'Create feedback');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Feedback-Express.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }
</style>
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
<div class="noticias-create">
    
    <!--<div class="page-header">
        <h3><?= Html::encode($this->title) ?></h3>
    </div>-->

    <?= $this->render('_form', [
        'model' => $model,
        'ajax' => $ajax,
    ]) ?>

</div>