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
use yii\db\Query;

$this->title = 'Hoja de Vida - Permisos';
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
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
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
                        <label><em class="fas fa-user-circle" style="font-size: 20px; color: #2CA5FF;"></em> Seleccionar Usuario: </label>

                        <?=
                            $form->field($model, 'usuario_registro')->label(Yii::t('app',''))
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
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'title' => 'Buscar Usuario']) 
                        ?>
                        
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="card2 mb">
                        <label><em class="fas fa-info-circle" style="font-size: 20px; color: #2CA5FF;"></em> Notificaci&oacute;n: </label>
                        <?php
                            if ($varUsuario != "") {
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-body" style="background-color: #f0f8ff;">Procesos de permisos en el m&oacute;dulo Hoja de Vida para el usuario <?php echo $varNombre; ?>.
                                </div>
                            </div>
                        <?php
                            }else{
                        ?>
                            <div class="panel panel-default">
                                <div class="panel-body" style="background-color: #f0f8ff;">Esperando a usuario para asginar los permisos en el m&oacute;dulo Hoja de Vida.
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
            <div class="col-md-12">
                
                <div class="row">
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label><em class="fas fa-cogs" style="font-size: 20px; color: #2CA5FF;"></em> Seleccionar Permisos Acciones: </label>

                            <div class="row">
                                <div class="col-md-6">
                                    <?=  $form->field($model,'hveliminar')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'ideliminar'))->label('Eliminar Registros'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?=  $form->field($model,'hveditar')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'ideditar'))->label('Editar Registros'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=  $form->field($model,'hvcasrgamasiva')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'idmasiva'))->label('Importar Datos'); ?>
                                </div>
                                <div class="col-md-6">
                                    <?=  $form->field($model,'hvdatapersonal')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'iddata'))->label('Guardar Registros'); ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <?=  $form->field($model,'hvverresumen')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'idver'))->label('Ver Resumen General'); ?>
                                </div>
                            </div>
                            
                        </div>                        
                    </div>

                    <div class="col-md-6">
                        <div class="card1 mb">

                            <label><em class="fas fa-cogs" style="font-size: 20px; color: #2CA5FF;"></em> Seleccionar Permisos Servicios & Pcrc: </label>

                            <div class="row">
                                <div class="col-md-12">
                                    <label>Seleccionar Servicio </label>
                                    <?=  $form->field($model2, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                          [
                                              'prompt'=>'Seleccionar...',
                                              'multiple' => true,
                                              'size'=>"6",
                                          ]
                                                      )->label(''); 
                                    ?>
                                </div>
                            </div>

                        </div>                        
                    </div>
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
                        <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
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
                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Guardar Datos: </label>                       
                        <div onclick="generatedchecked();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                            Guardar
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
    function generatedchecked(){
        var varideliminar = document.getElementById("ideliminar").checked;
        var varideditar = document.getElementById("ideditar").checked;
        var varidmasiva = document.getElementById("idmasiva").checked;
        var variddata = document.getElementById("iddata").checked;
        var varidver = document.getElementById("idver").checked;
        var varlistidclientes = document.querySelectorAll('#hojavidapermisoscliente-id_dp_clientes option:checked');
        var varidservicio = Array.from(varlistidclientes).map(el => el.value);
        var variduser = "<?php echo $varUsuario; ?>";


        vareliminar = 0;
        vareditar = 0;
        varmasiva = 0;
        vardata = 0;
        varver = 0;
        varverdata = 1;

        if (varideliminar == true) {vareliminar = 1;}else{vareliminar = 0;}
        if (varideditar == true) {vareditar = 1;}else{vareditar = 0;}
        if (varidmasiva == true) {varmasiva = 1;}else{varmasiva = 0;}
        if (variddata == true) {vardata = 1;}else{vardata = 0;}
        if (varidver == true) {varver = 1;}else{varver = 0;}

        $.ajax({
            method: "get",
            url: "permisosaccion",
            data: {
              txtvarideliminar : vareliminar,
              txtvarideditar : vareditar,
              txtvaridmasiva : varmasiva,
              txtvariddata : vardata,
              txtvaridver : varver,
              txtvariduser : variduser,
              txtvarverdata : varverdata,
            },
            success : function(response){
              numRta =   JSON.parse(response); 
              
            }
        });

        if (varidservicio != "") {
            $.ajax({
                method: "get",
                url: "permisosaccioncliente",
                data: {
                  txtvaridservicio : varidservicio,
                  txtvariduser : variduser,
                },
                success : function(response){
                  numRta =   JSON.parse(response);
                }
            });
        }

        window.open('../hojavida/index','_self')
    };

   
</script>
