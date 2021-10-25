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
<div class="container">
    <div class="row">

        <div class="col-lg-12">
            <h4><?php //echo Yii::t("app", "Feedback express"); ?></h4>
            <table class="table table-striped table-bordered">
            <caption>Tabla datos</caption>
                <thead>                    
                    <tr>
                        <th scope="col"><?= Yii::t('app', 'Acciones') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Fecha') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Tipo de notificación') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Id Solicitante') ?></th>
                        <th scope="col"><?= Yii::t('app', 'Lider') ?></th>
                        <!--<th><?php //Yii::t('app', 'Formulario') ?></th>-->
                        <th scope="col"><?= Yii::t('app', 'Id Evaluador') ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data->alertasList as $alerta): ?>                                                                     
                        <tr>
                            <td>
                                <?php
                                /*
                                $id = $alerta['idForm'];
                                $eje = \app\models\Ejecucionformularios::findOne(['id' => $id]);
                                if (isset($eje->basesatisfaccion_id)) {

                                    $modelBase = app\models\BaseSatisfaccion::findOne($eje->basesatisfaccion_id);
                                }
                                if (!isset($eje->basesatisfaccion_id)) {

                                    echo Html::a('<span class="glyphicon glyphicon-eye-open">'
                                            . '</span>'
                                            , Url::to(['formularios/showformulariodiligenciado'
                                                , 'feedback_id' => $alerta["id"],'view'=>"segundocalificador"]), [
                                        'title' => Yii::t('yii', 'ver formulario'),
                                        'target' => "_blank"
                                    ]);
                                } else {

                                    //if ($modelBase->estado == "Cerrado") {
                                    echo Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['basesatisfaccion/showformulariogestion'
                                                , 'basesatisfaccion_id' => $modelBase->id, 'preview' => 1, 'fill_values' => true,'view'=>"segundocalificador"]), [
                                        'title' => Yii::t('yii', 'ver formulario'),
                                        'target' => "_blank"
                                    ]);
                                    //}
                                }*/
                                ?>
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
                                <?php
                                /*echo Html::a('<span class="glyphicon glyphicon-stats"></span>', '', [
                                    'title' => Yii::t('yii', 'Calculos'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['reportes/calculatefeedback'
                                        , 'formulario_id' => $alerta["idForm"]]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                                ]);*/
                                ?>
                            </td>
                            <td><?php echo $alerta["created"] ?></td>
                            <td><?= Yii::t('app', 'Alerta resumen') ?></td>
                            <td><?php echo $alerta["evaluado"] ?></td>
                            <td><?php echo $alerta["lider"] ?></td>
                            <!--<td><?php //echo $alerta["formulario"] ?></td>-->
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

