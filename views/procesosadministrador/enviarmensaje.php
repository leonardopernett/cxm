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

$this->title = 'Enviar Alertas';
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

    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card2 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #ffe6e6;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

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

<!-- Capa Por Personal -->
<div id="capaPersonalId" class="capaPersonal" style="display: inline;">
    
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Buscar Usuario') ?></label>

                <div class="row">
                    <div class="col-md-4">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' * Seleccionar Rol') ?></label>
                        <?=  $form->field($model, 'id_dp_posicion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Encuestaspersonalsatu::find()->distinct()->where("anulado = 0")->orderBy(['id_dp_posicion'=> SORT_ASC])->all(), 'id_dp_posicion', 'posicion'),
                                                    [
                                                        'id' => 'IdPosicion',
                                                        'prompt'=>'Seleccionar Posicion...',
                                                        'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('listarnombres') . '", 
                                                                {id: $(this).val()}, 
                                                                function(res){
                                                                    $("#requester").html(res);
                                                                }
                                                            );
                                                            
                                                        ',

                                                    ]
                                        )->label(''); 
                        ?>
                    </div>

                    <div class="col-md-8">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' * Seleccionar Usuario') ?></label>
                        <?= $form->field($model,'personalsatu', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                    
                                                    'prompt' => 'Seleccionar Usuario...',
                                                    'id' => 'requester',
                                                ]
                                            )->label('');
                        ?>
                    </div>

                </div>

                <br>

                <div class="row">
                    <div class="col-md-12">
                        <?= Html::submitButton(Yii::t('app', 'Enviar Alerta'),
                                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                            'data-toggle' => 'tooltip', 'onclick' => 'openview2();',
                                            'title' => 'Buscar']) 
                        ?>
                    </div>
                </div>

                
                
            </div>
        </div>
    </div>

    <?php $form->end() ?>

</div>
<br>
<!-- Capa Envio Masivo -->
<div id="capaMasivoId" class="capaMasivo" style="display: inline;">
    
    <div class="row">

        <div class="col-md-12">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-envelope" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Enviar Alertas Masivas') ?></label>

                <label style="font-size: 15px;"> <?= Yii::t('app', 'Importante: Esta opción permite enviar alertas a los usuarios tipo Director y Gerente que se encuentren registrados en la herramienta CXM.') ?></label>

                <?= Html::a('Enviar Masivo',  ['enviomasivo'], ['class' => 'btn btn-danger',             
                                'data-toggle' => 'tooltip', 'onclick' => 'openview2();',
                                'title' => 'Enviar Alertas Masivas'])
                ?>

            </div>
        </div>        
        
    </div>

</div>

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
                            <th class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informacion para el envio de las alertas.') ?></h4></th>
                        </tr>            
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function openview2(){
        var varcapaPersonalId = document.getElementById("capaPersonalId");
        var varcapaMasivoId = document.getElementById("capaMasivoId");
        var varcapaEsperaId = document.getElementById("capaEsperaId");

        varcapaPersonalId.style.display = 'none';
        varcapaMasivoId.style.display = 'none';
        varcapaEsperaId.style.display = 'inline';
    };
</script>