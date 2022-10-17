<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use yii\db\Query;

$sesiones = Yii::$app->user->identity->id;   
?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<!-- Capa Informativa -->
<div id="capaInfoId" class="capaInfo" style="display: inline;">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidad Actual de Dedicación') ?></label>
                <?= $form->field($model, "Dedic_valora")->textInput(['id'=>'idDedicaActual', 'readonly' => 'readonly', 'value'=>$varDedicadion])->label('') ?> 
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidad Nueva de Dedicación') ?></label>
                <?= $form->field($model, "cant_valor")->textInput(['maxlength' => 2, 'id'=>'idDedicaActualNew', 'onkeypress' => 'return valida(event)', 'placeholder'=>'Ingresar Nueva Cantidad'])->label('') ?>
                <label style="font-size: 15px;"><em class="fas fa-comments" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Motivo del Cambio') ?></label>
                <?= $form->field($model, "tipo_corte")->textInput(['maxlength' => 100, 'id'=>'idComentarios', 'placeholder'=>'Ingresar Comentario'])->label('') ?>
                <br>

                <?= Html::submitButton(Yii::t('app', 'Modificar'),
                              ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                  'data-toggle' => 'tooltip',
                                  'onclick' => 'validaComentario();',
                                  'title' => 'Modificar']) 
                ?>

            </div>
        </div>
    </div>
    <?php $form->end() ?>
</div>

<script type="text/javascript">
    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
          return true;
        }
                
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    };

    function validaComentario(){
        var varidDedicaActualNew = document.getElementById("idDedicaActualNew").value;
        var varidComentarios = document.getElementById("idComentarios").value;

        if (varidDedicaActualNew == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe ingresar una cantidad de dedicacion diferentes a la actual.","warning");
            return;
        }

        if (varidComentarios == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe ingresar un comentario por el cual se hara el cambio de la cantidad de dedicación.","warning");
            return;
        }
    };
</script>