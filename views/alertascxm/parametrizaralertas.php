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

$this->title = 'Alertas - Procesos Parametrizador';
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

</style>
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
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

<?php $form = ActiveForm::begin(
  [
    'layout' => 'horizontal',
    'fieldConfig' => [
      'inputOptions' => ['autocomplete' => 'off']
    ]
  ]); 
?>

<!-- Capa Principal -->
<div class="capaPrincipal" id="capaIdPrincipal" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Acciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cancelar & Regresar:') ?></label> 
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Parametrizaciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Parametrizar Tipo de Alertas') ?></label>
                <?= $form->field($modelTipo, 'tipoalerta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 150, 'id'=>'idtipoalerta', 'placeholder'=>'Ingresar Tipo de Alerta'])?>

                <div onclick="generartipo();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  <?= Yii::t('app', ' Guardar') ?>
                </div>
                <br>
                <table id="tbltipoAlerta" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Alerta') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                      foreach ($varDataListTipo as $key => $value) {                    
                    ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['id_tipoalerta']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['tipoalerta']; ?></label></td>
                            <td class="text-center">
                          <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminartipo','id'=> $value['id_tipoalerta']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                            </td>
                        </tr>
                    <?php
                      }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Parametrizar Tipo de Encuesta Alerta') ?></label>

                <div class="row">
                    <div class="col-md-9">
                        <?= $form->field($modelEncuestas, 'tipoencuestas', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 150, 'id'=>'idtipoencuesta', 'placeholder'=>'Ingresar Tipo de Encuesta'])?>
                    </div>

                    <div class="col-md-3">
                        <?= $form->field($modelEncuestas, 'peso', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 2, 'id'=>'idPeso', 'placeholder'=>'Ingresar Peso','onkeypress' => 'return valida(event)'])?>
                    </div>
                </div>

                

                <div onclick="generarencuesta();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  <?= Yii::t('app', ' Guardar') ?>
                </div>
                <br>
                <table id="tbltipoEncuesta" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Encuesta') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Peso Encuesta') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                      foreach ($varDataListEncuesta as $key => $value) {                    
                    ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['id_tipoencuestas']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['tipoencuestas']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['peso']; ?></label></td>
                            <td class="text-center">
                                <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarencuesta','id'=> $value['id_tipoencuestas']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                            </td>
                        </tr>
                    <?php
                      }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <hr>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Parametrizar Permisos Alertas') ?></label>
                <?=
                    $form->field($modelPermisos, 'id_usuario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label(Yii::t('app',''))
                            ->widget(Select2::classname(), [
                                'language' => 'es',
                                'options' => ['id'=>'varIdUsuario','placeholder' => Yii::t('app', 'Seleccionar Usuario...')],
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

                <div onclick="generarpermiso();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                  <?= Yii::t('app', ' Guardar') ?>
                </div>
                <br>
                <table id="tbltipoPermiso" class="table table-striped table-bordered tblResDetFreed">
                  <caption><?= Yii::t('app', ' Resultados...') ?></caption>
                  <thead>
                    <tr>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Usuario Con Perimso') ?></label></th>
                      <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                    </tr>
                  </thead>
                  <tbody>
                    <?php
                      foreach ($varDataListPermisos as $key => $value) {    
                        $varNombreUsuario = (new \yii\db\Query())
                                            ->select(['tbl_usuarios.usua_nombre'])
                                            ->from(['tbl_usuarios'])
                                            ->where(['=','tbl_usuarios.usua_id',$value['id_usuario']])
                                            ->scalar();                
                    ?>
                      <tr>
                        <td><label style="font-size: 12px;"><?php echo  $value['id_permisoseliminar']; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $varNombreUsuario; ?></label></td>
                        <td class="text-center">
                          <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarpermiso','id'=> $value['id_permisoseliminar']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
                        </td>
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

<?php $form->end() ?>


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

    function generartipo(){
        var varidtipoalerta = document.getElementById("idtipoalerta").value;

        if (varidtipoalerta == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un tipo de alerta","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresartipoalerta",
                data: {
                  txtvaridtipoalerta : varidtipoalerta,
                },
                success : function(response){
                  numRta =   JSON.parse(response);          
                  location.reload();
                }
            });
        }
    };

    function generarencuesta(){
        var varidtipoencuesta = document.getElementById("idtipoencuesta").value;
        var varidPeso = document.getElementById("idPeso").value;

        if (varidtipoencuesta == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un tipo de encuesta","warning");
            return;
        }else{
            if (varidPeso == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe de ingresar un peso a la encuesta","warning");
                return;
            }else{
                $.ajax({
                    method: "get",
                    url: "ingresartipoencuesta",
                    data: {
                      txtvaridtipoencuesta : varidtipoencuesta,
                      txtvaridPeso : varidPeso,
                    },
                    success : function(response){
                      numRta =   JSON.parse(response);          
                      location.reload();
                    }
                });
            }
        }
    };

    function generarpermiso(){
        var varIdUsuario = document.getElementById("varIdUsuario").value;

        if (varIdUsuario == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar a un usuario","warning");
            return;
        }else{
            $.ajax({
                method: "get",
                url: "ingresarpermisos",
                data: {
                    txtvarIdUsuario : varIdUsuario,
                },
                success : function(response){
                    numRta =   JSON.parse(response);          
                    location.reload();
                }
            });
        }
    };
</script>