<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Enviar Alertas Masivos - Excel';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol    ->select(['tbl_roles.role_id'])
            ->from('tbl_roles')
            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                    'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
            ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

?>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito');


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }



    .lds-ring {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 100px;
    }
    .lds-ring div {
      box-sizing: border-box;
      display: block;
      position: absolute;
      width: 80px;
      height: 80px;
      margin: 10px;
      border: 10px solid #3498db;
      border-radius: 70%;
      animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      border-color: #3498db transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
      animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
      animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
      animation-delay: -0.15s;
    }
    @keyframes lds-ring {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<br>

<!-- Capa Esperar proceso -->
<div id="capaEsperaId" class="capaEspera" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table class="text-center">
                    <thead>
                        <tr>
                            <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                            <th><?= Yii::t('app', '') ?></th>
                            <th class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informacion para el envio de las alertas a nivel masivo.') ?></h4></th>
                        </tr>            
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>



<!-- Capa Envio -->
<div id="capaEnvioId" class="capaEnvio" style="display: inline;">
    <?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb" style="background: #ffe6e6;">
                <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Subir Archivo') ?></label>

                <label style="font-size: 15px;"> <?= Yii::t('app', 'Importante: Esta opción permite enviar alertas a los usuarios tipo Director y Gerente que se encuentren en el archivo del excel que se sube.') ?></label>

                <?= $form->field($model, "file[]")->fileInput(['id'=>'idinput','multiple' => false])->label('') ?>

                <br>

                <?= Html::submitButton("Subir", ["class" => "btn btn-danger", "onclick" => "cargar();"]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    function cargar(){
        var varcapaEnvioId = document.getElementById("capaEnvioId");
        var varcapaEsperaId = document.getElementById("capaEsperaId");

        varcapaEnvioId.style.display = 'none';
        varcapaEsperaId.style.display = 'inline';
    };
</script>