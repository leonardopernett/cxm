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
use app\models\ControlProcesosPlan;
use app\models\ProcesosClienteCentrocosto;
use yii\db\Query;

$this->title = 'Reportes Comdata - Permisos';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

?>
<style>
    .card1 {
            height: 213px;
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
            height: 152px;
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

    .card3 {
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


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Gestor_de_Clientes.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<?php $form = ActiveForm::begin([
    'options' => ["id" => "buscarMasivos"],
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>
<div class="capaPrincipal" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card2 mb">
                        <label><em class="fas fa-user-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Seleccionar Usuario') ?> </label>

                        <?=
                            $form->field($modelpermiso, 'usua_id')->label(Yii::t('app',''))
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Seleccionar...')],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 4,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                                var id=$(element).val();
                                                if (id !== "") {
                                                    $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                                        dataType: "json",
                                                        type: "post"
                                                    }).done(function(data) { callback(data.results[0]);});
                                                }
                                            }')
                                ]
                                    ] 
                            );
                        ?>
                        <br>
                        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                            ['class' => $modelpermiso->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'title' => 'Buscar Usuario']) 
                        ?>
                        
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card2 mb">
                        <label><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Notificación') ?> </label>
                        <?php
                            if ($varUsuario != "") {
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-body" style="background-color: #f0f8ff;">
                                <?= Yii::t('app', 'Permisos para la búsqueda de clientes en el módulo de reporteria LookerStudio al usuario '.$varNombre) ?>
                                </div>
                            </div>
                        <?php
                            }else{
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-body" style="background-color: #f0f8ff;">
                                <?= Yii::t('app', 'Esperando a usuario para asginar los permisos para la búsqueda de clientes en el módulo de reporteria LookerStudio al usuario.') ?>
                                </div>
                            </div>
                        <?php
                            }
                        ?>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
<hr>
<?php
    if ($varUsuario != "") {
?>
    <div class="capaDos" style="display: inline;">
        <div class="row">
            <div class="col-md-6">
                
                <div class="card1 mb">

                    <label><em class="fas fa-cogs" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Seleccionar Permisos Servicios & Pcrc') ?> </label>

                    <?=  $form->field($modelpermiso, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->where(['=','anulado',0])->andwhere(['=','estado',1])->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                          [
                                              'prompt'=>'Seleccionar...',
                                              'multiple' => true,
                                              'size'=>"6",
                                          ]
                                                      )->label(''); 
                    ?>

                </div>                        


            </div>

            <div class="col-md-6">
                
                <div class="card3 mb">
                    <label><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Lista de Servicios con los permisos ') ?> </label>

                    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?php echo "Resultados Servicios: " ?></caption>
                        <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($dataProviderClientes as $key => $value) {
                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $value['id_dp_clientes']; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $value['cliente']; ?></label></td>                                
                            </tr>
                        <?php
                            }
                        ?>
                    </tbody>
                    </table>
                </div>

            </div>
        </div>

        
    </div>
    <hr>
<?php
    }
?>
<?php $form->end() ?> 
<div class="capaTres" style="display: inline;">
    <div class="row">
        <div class="col-md-12">

            <div class="row">
                <div class="col-md-6">
                    <div class="card3 mb">
                        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?> </label> 
                        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                        'style' => 'background-color: #707372',
                                                        'data-toggle' => 'tooltip',
                                                        'title' => 'Regresar']) 
                        ?>
                    </div>
                </div>  
                <?php
                    if ($varUsuario != "") {
                ?>

                <div class="col-md-6">
                    <div class="card3 mb">  
                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Guardar Datos') ?> </label>                       
                        <div onclick="generarprocesos();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                            <?= Yii::t('app', 'Guardar') ?>
                        </div>
                    </div>
                </div>  

                <?php 
                    }
                ?>            
            </div>
            
        </div>
    </div>
</div>

<hr>

<script type="text/javascript">
    function generarprocesos(){
        var varlistidclientescomdata = document.querySelectorAll('#comdatareportestudio-id_dp_clientes option:checked');
        var varidserviciocomdata = Array.from(varlistidclientescomdata).map(el => el.value);
        var varidusercomdata = "<?php echo $varUsuario; ?>";

        if (varidserviciocomdata != "") {
            $.ajax({
                method: "get",
                url: "permisosclientecomdata",
                data: {
                  txtvaridserviciocomdata : varidserviciocomdata,
                  txtvaridusercomdata : varidusercomdata,
                },
                success : function(response){
                  numRta =   JSON.parse(response);
                }
            });
        }

        window.open('../dashboardcomdata/index','_self')
    };
</script>