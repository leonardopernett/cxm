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

$this->title = 'Alertas - Crear Alerta';
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
    height: 90px;
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
    background-image: url('../../images/Crear-Alerta.png');
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

<?php
if ($varEnvios == 0) {  
?>

<!-- Capa General que contiene todas las capas -->
<div class="capaGeneral" id="capaIdGeneral" style="display: inline;">

    <?php $form = ActiveForm::begin(["method" => "post","enableClientValidation" => true,'options' => ['enctype' => 'multipart/form-data'],'fieldConfig' => ['inputOptions' => ['autocomplete' => 'off']]]) ?>

    <!-- Capa Informativa -->
    <div class="capaInformativa" id="capaIdInformativa" style="display: inline;">
        
        <div class="row">
            <div class="col-md-6">
                <div class="card1 mb" style="background: #6b97b1; ">
                    <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Notas Informativas') ?></label>
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-6">
                <div class="card2 mb">

                    <div class="row">
                        <div class="col-md-2 text-center">
                            <label style="font-size: 15px;"><em class="fas fa-envelope" style="font-size: 50px; color: #FFC72C;"></em></label>
                        </div>

                        <div class="col-md-10 left">
                            <label style="font-size: 15px;"><?= Yii::t('app', ' ¡Qué bueno que estés aquí! Te comentamos que el módulo actual permite el envio de alertas a varios correos al tiempo y adjuntar un archivo para evidencias.') ?></label>
                        </div>
                    </div>          

                </div>        
            </div>

            <div class="col-md-6">
                <div class="card2 mb">

                    <div class="row">
                        <div class="col-md-2 text-center">
                            <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 50px; color: #FFC72C;"></em></label>
                        </div>

                        <div class="col-md-10 left">
                            <label style="font-size: 15px;"><?= Yii::t('app', ' Es importante recordar...') ?></label>  <label style="font-size: 15px; color: #981F40"> <?= Yii::t('app', ' Si se envia una alerta a varios correos, estos deben estar separados por una coma (,).') ?></label>
                        </div>
                    </div>   
                </div>
            </div>
        </div>

    </div>

    <hr>

    <!-- Capa Procesar -->
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
                            <label style="font-size: 15px;"><span class="texto" style="color: #FFC72C"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Tipo de Alerta ') ?></label>
                            <?=  $form->field($model, 'tipo_alerta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\AlertasTipoalerta::find()->where(['=','anulado',0])->orderBy(['tipoalerta'=> SORT_ASC])->all(), 'tipoalerta', 'tipoalerta'),
                                            [
                                                'id' => 'varIdPostulacion',
                                                'prompt'=>'Seleccionar Tipo Alerta...'
                                            ]
                                    )->label(''); 
                            ?>

                            <label style="font-size: 15px;"><span class="texto" style="color: #FFC72C"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Ingresar Asunto de la Alerta ') ?></label>
                            <?= $form->field($model, 'asunto', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'varIdAsuntos','placeholder'=>'Ingresar Asunto de la Alerta'])?>                                              

                            <label style="font-size: 15px;"><span class="texto" style="color: #FFC72C"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Ingresar Comentarios ') ?></label>
                            <?= $form->field($model, 'comentario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'varIdComentarios','rows'=>4,'placeholder'=>'Ingresar Comentarios'])?>
                        </div>

                        <div class="col-md-6">
                            <label style="font-size: 15px;"><span class="texto" style="color: #FFC72C"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Programa/Pcrc ') ?></label>
                            <?=
                                $form->field($model, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                    ->widget(Select2::classname(), [
                                        'language' => 'es',
                                        'options' => ['id'=>'varIdPcrc','placeholder' => Yii::t('app', 'Select ...')],
                                        'pluginOptions' => [
                                            'allowClear' => false,
                                            'minimumInputLength' => 3,
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['formularios/getarbolesbyroles']),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                            ],
                                        ]
                                            ]
                                )->label('');
                            ?>
                            

                            <label style="font-size: 15px;"><span class="texto" style="color: #FFC72C"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Ingresar Correos Destinatarios ') ?></label>

                            <?= 
                                Html::radioList('anulado', 1, 
                                ['Grupal','Normal'], 
                                ['separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','name'=>'contact','id'=>'varidInteracciones','onclick'=>'varCambios();']) 
                            ?>

                            <div class="capaEmbajadorAsesor" id="capaIdEmbajadorAsesor" style="display: inline;">
                                <?= $form->field($model, 'remitentes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id' => 'varIdNormal','placeholder'=>'Ingresar Correos Destinatarios'])?>
                            </div>
                            <div class="capaEmbajadorAdmin" id="capaIdEmbajadorAdmin" style="display: none;">

                                <?=  $form->field($model, 'archivo_adjunto', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Correogrupal::find()->orderBy(['nombre'=> SORT_ASC])->groupby(['nombre'])->all(), 'idcg', 'nombre'),
                                            [
                                                'id' => 'varIdGrupal',
                                                'prompt'=>'Seleccionar Correo Grupal...'
                                            ]
                                    )->label(''); 
                                ?>
                            </div>
                            

                            <label style="font-size: 15px;"> <?= Yii::t('app', ' ___________________ ') ?></label>
                            <?= $form->field($modelArchivo, 'file', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->fileInput(["class"=>"input-file" ,'id'=>'idfile'])->label('') ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>

    <hr>

    <!-- Capa Botonera -->
    <div class="capaBtn" id="capaIdBtn" style="display: inline;">
        
        <div class="row">
            <div class="col-md-4">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Guardar & Enviar Alerta:') ?></label> 
                    <?= Html::submitButton("Enviar", ["class" => "btn btn-primary", "onclick" => "varGenerar();"]) ?>
                </div>    
            </div>

            <div class="col-md-4">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-envelope" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Crear Correo Grupal:') ?></label>                 
                    <?= Html::a('Crear Grupos',  ['correogrupal'], ['class' => 'btn btn-success',
                                                        'data-toggle' => 'tooltip',
                                                        'title' => 'Correo Grupal']) 
                    ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card1 mb">
                    <?php
                        if ($id_procesos == "0") {
                    ?>
                        <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Nuevo Registro:') ?></label> 
                        <?= Html::a('Nuevo',  ['registraalerta','id_procesos'=>0], ['class' => 'btn btn-success',
                                                        'style' => 'background-color: #707372',
                                                        'data-toggle' => 'tooltip',
                                                        'title' => 'Nuevo Registro']) 
                        ?>
                    <?php
                        }else{
                    ?>
                        <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cancelar & Regresar:') ?></label> 
                        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                        'style' => 'background-color: #707372',
                                                        'data-toggle' => 'tooltip',
                                                        'title' => 'Regresar']) 
                        ?>
                    <?php
                        }
                    ?>
                </div>
            </div>
        </div>

    </div>

    <hr>

    <?php $form->end() ?>
</div>

<!-- Capa Mensajes de Espera -->
<div class="capaLoader" id="capaIdLoader" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table class="center">
                    <thead>
                        <tr>
                            <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                            <th><?= Yii::t('app', '') ?></th>
                            <th class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informacion del envío al correo o los correos correspondientes en la alerta...') ?></h4></th>
                        </tr>            
                    </thead>
                </table>
            </div>
        </div>
    </div>
    <hr>
</div>

<?php
}else{
?>

<!-- Capa Mensaje de Guardado con exito -->
<div class="capaMensajes" id="capaIdMensajes" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Nota Informativa') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            
                <div class="card2 mb">

                    <div class="row">
                        <div class="col-md-2 text-center">
                            <label style="font-size: 15px;"><em class="fas fa-thumbs-up" style="font-size: 50px; color: #FFC72C;"></em></label>
                        </div>

                        <div class="col-md-10 left">
                            <label style="font-size: 15px;"><?= Yii::t('app', ' En hora buena...') ?></label>  <label style="font-size: 15px; "> <?= Yii::t('app', ' La alerta ha sido enviada y registrada adecuamente, te invitamos a seguir con los procesos que necesites en CXM, gracias.') ?></label>
                        </div>
                    </div>   
                </div>
            
        </div>

        <div class="col-md-6">
            
                <div class="card2 mb">

                    
                            <?php
                                if ($id_procesos == "0") {
                            ?>
                                <?= Html::a('Nueva Alerta',  ['registraalerta','id_procesos'=>0], ['class' => 'btn btn-success',
                                                                'style' => 'background-color: #707372',
                                                                'data-toggle' => 'tooltip',
                                                                'title' => 'Nuevo Registro']) 
                                ?>
                            <?php
                                }else{
                            ?>
                                <?= Html::a('Nueva Alerta',  ['registraalerta','id_procesos'=>1], ['class' => 'btn btn-success',
                                                                'style' => 'background-color: #707372',
                                                                'data-toggle' => 'tooltip',
                                                                'title' => 'Nuevo Registro']) 
                                ?>
                            <?php
                                }
                            ?>
                          
                </div>
            
        </div>
    </div>

</div>

<hr>

<?php
}
?>

<script type="text/javascript">
    function varCambios(){
        var radios = document.getElementsByName('anulado');
        var varcapaIdEmbajadorAsesor = document.getElementById("capaIdEmbajadorAsesor");
        var varcapaIdEmbajadorAdmin = document.getElementById("capaIdEmbajadorAdmin");
        var vartipo = 0;


        for (var radio of radios) {
            if (radio.checked) {
                var varchekeo = radio.value;
                if (varchekeo == 0) {
                    varcapaIdEmbajadorAsesor.style.display = 'none';
                    varcapaIdEmbajadorAdmin.style.display = 'inline';      
                    vartipo = 1;
                }else{
                    varcapaIdEmbajadorAsesor.style.display = 'inline';
                    varcapaIdEmbajadorAdmin.style.display = 'none';
                    vartipo = 2;  
                }
            }
        }
    };

    function varGenerar(){
        var varIdPostulacion = document.getElementById("varIdPostulacion").value;
        var varIdAsuntos = document.getElementById("varIdAsuntos").value;
        var varIdComentarios = document.getElementById("varIdComentarios").value;
        var varIdPcrc = document.getElementById("varIdPcrc").value;
        var varIdNormal = document.getElementById("varIdNormal").value;
        var varIdGrupal = document.getElementById("varIdGrupal").value;

        if (varIdPostulacion == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un tipo de alerta.","warning");
            return;
        }

        if (varIdAsuntos == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un asunto de la alerta.","warning");
            return;
        }

        if (varIdComentarios == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar comentarios de la alerta.","warning");
            return;
        }

        if (varIdPcrc == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un programa/pcrc.","warning");
            return;
        }

        if (varIdNormal != "") {
            console.log("Proceso Correos Normal");
        }else{
            if (varIdGrupal != "") {
                console.log("Proceso Correos Grupal");
            }else{
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe de ingresar un correo o grupo de correos.","warning");
                return;
            }
        }

        var varcapaIdGeneral = document.getElementById("capaIdGeneral");
        var varcapaIdLoader = document.getElementById("capaIdLoader");

        varcapaIdGeneral.style.display = 'none';
        varcapaIdLoader.style.display = 'inline';


    };
</script>