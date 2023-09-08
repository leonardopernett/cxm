<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;

$this->title = 'Estructurar Valoraciones - Index';
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
       	font-family: "Nunito",sans-serif;
        font-size: 150%;    
        text-align: left;    
    }

    .card2 {
        height: 130px;
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
        font-family: "Nunito",sans-serif;
        font-size: 150%;    
        text-align: left;    
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
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
<!-- Data extensiones -->
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>

<br>
<br>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<?php
if ($varListaValoraciones == null) {
?>

<!-- Capa Procesos -->
<div class="capaProcesos" id="capaIdProcesos" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones & Procesos') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Seleccionar Rango de Fechas') ?></label>
                        <?=
                                $form->field($model, 'fechacreacion', [
                                    'labelOptions' => ['class' => 'col-md-12'],
                                    'template' => 
                                     '<div class="col-md-12"><div class="input-group">'
                                    . '<span class="input-group-addon" id="basic-addon1">'
                                    . '<i class="glyphicon glyphicon-calendar"></i>'
                                    . '</span>{input}</div>{error}{hint}</div>',
                                    'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                    'options' => ['class' => 'drp-container form-group']
                                ])->label('')->widget(DateRangePicker::classname(), [
                                    'useWithAddon' => true,
                                    'convertFormat' => true,
                                    'presetDropdown' => true,
                                    'readonly' => 'readonly',
                                    'pluginOptions' => [
                                        'timePicker' => false,
                                        'format' => 'Y-m-d',
                                        'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                        'endDate' => date("Y-m-d"),
                                        'opens' => 'right',
                                ]]);
                        ?>
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Seleccionar Cliente') ?></label>
                        <?=
                            $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['id'=>'varIdPcrc','placeholder' => Yii::t('app', 'Seleccionar Programa/Pcrc')],
                                'pluginOptions' => [
                                    'multiple' => true,
                                    'allowClear' => true,
                                    'minimumInputLength' => 3,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/getarboles']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                        var id=$(element).val();
                                        if (id !== "") {
                                            $.ajax("' . Url::to(['getarboles']) . '?id=" + id, {
                                                dataType: "json",
                                                type: "post"
                                            }).done(function(data) { callback(data.results);});
                                        }
                                    }')
                                ]
                                    ]
                            );
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<br>

<!-- Capa Btns -->
<div class="capaBtn" id="capaIdBtn" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Buscar Alertas') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Buscar'),
                          ['class' => $model->isNewRecord ? 'btn btn-primary' : 'btn btn-primary',
                              'data-toggle' => 'tooltip',
                              'title' => 'Buscar Datos',
                              'onclick' => 'varVerificar();']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Nueva Búsqueda') ?></label>
                <?= Html::a('Nuevo',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Nueva Búsqueda']) 
                ?>
            </div>
        </div>
    </div>

</div>

<hr>

<?php
}else{
?>

<!-- Capa Resultados -->
<div class="capaResultados" id="capaIdResultados" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Resultados') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        
        <div class="col-md-4">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cantidad de Valoraciones Solucionadas') ?></label><br>
                <label  style="font-size: 40px; text-align: center;"><?php echo count($varListaValoraciones); ?></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Nombre Cliente') ?></label><br>
                <label  style="font-size: 40px; text-align: center;"><?php echo $varNombres; ?></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card2 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Nueva Búsqueda') ?></label>
                <?= Html::a('Nuevo',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Nueva Búsqueda']) 
                ?>
            </div>
        </div>

    </div>

</div>

<hr>

<?php
}
?>

<?php ActiveForm::end(); ?>

<!-- Capa Mensajes de Espera -->
<div class="capaLoader" id="capaIdLoader" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table align="center">
                    <thead>
                        <tr>
                            <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                            <th><?= Yii::t('app', '') ?></th>
                            <th class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informacion calculando nuevamente el score de cada valoracion encontrada con un score mayor a 1 o 100...') ?></h4></th>
                        </tr>            
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <hr>
</div>


<script type="text/javascript">
    function varVerificar(){
        var varfechas = document.getElementById("speechservicios-fechacreacion").value;
        var varIdPcrc = document.getElementById("varIdPcrc").value;

        var varcapaIdLoader = document.getElementById("capaIdLoader");
        var varcapaIdProcesos = document.getElementById("capaIdProcesos");
        var varcapaIdBtn = document.getElementById("capaIdBtn");

        if (varfechas == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un rango de fechas.","warning");
            return;
        }
        if (varIdPcrc == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente.","warning");
            return;
        }

        varcapaIdLoader.style.display = 'inline';
        varcapaIdProcesos.style.display = 'none';
        varcapaIdBtn.style.display = 'none';
    };
</script>