<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of dashboardalert
 *
 * @author ingeneo
 */

use yii\helpers\Html;
use yii\helpers\Url;
$this->title = Yii::t('app', 'Alerta resumen');
$this->params['breadcrumbs'][] = $this->title;
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Alerta-Resumen.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }
</style>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<div class="container">
    <div class="row">

        <div class="col-lg-12">
            <h4></h4>
            <table class="table table-striped table-bordered">
            <caption>Tabla datos</caption>
                <thead>                    
                    <tr>
                        <th scope="col"><?= Yii::t('app', 'Acciones') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Fecha') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Tipo de notificación') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Id Solicitante') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Lider') ?></th>
                        
                        <th scope="col"><?= Yii::t('app', 'Id Evaluador') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data->alertasList as $alerta): ?>                                                                     
                        <tr>
                            <td>
                                <?=
                                Html::a('<span class="glyphicon glyphicon-pencil"></span>', '', [
                                    'title' => Yii::t('yii', 'Update'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['reportes/updatefeedbackcm'
                                        , 'id' => $alerta["id"]]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                                ]);
                                ?>
                            </td>
                            <td><?php echo $alerta["created"] ?></td>
                            <td><?= Yii::t('app', 'Alerta resumen') ?></td>
                            <td><?php echo $alerta["evaluado"] ?></td>
                            <td><?php echo $alerta["lider"] ?></td>
                       
                            <td><?php echo $alerta["usuario"] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php
                    echo Html::tag('div', '', ['id' => 'ajax_result']);
                    ?>
    </div>

</div>

