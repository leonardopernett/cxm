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

$this->title = 'Procesos Administrador';
$this->params['breadcrumbs'][] = $this->title;

    $sesiones =Yii::$app->user->identity->id;

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
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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
<div id="IdCapaUno" class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
          <div class="card1 mb" style="background: #6b97b1; ">
            <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Información"; ?> </label>
          </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-star" style="font-size: 40px; color: #ffc034;"></em> Es importante indicar que las acciones que se representan en el actual módulo solo es permitido para usuarios que tengan rol administrativo. </label>
            </div>
        </div>
    </div>
</div>
<hr>
<?php if ($roles == '270') { ?>
<div id="IdCapaDos" class="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
          <div class="card1 mb" style="background: #6b97b1; ">
            <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Acciones Administrativas"; ?> </label>
          </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
        
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-address-card" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Permisos Feedback') ?></label>
                        <?= Html::a('Aceptar',  ['permisosfeedback/index'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Permisos Feedback']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Categorias Feedback') ?></label>
                        <?= Html::a('Aceptar',  ['categoriascxm'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Categorias Feedback']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-clone" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Responsabilidades') ?></label>
                        <?= Html::a('Aceptar',  ['viewresponsability'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Permisos Feedback']) 
                        ?>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-key" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Permisos Servicios Escuchar +') ?></label>
                        <?= Html::a('Aceptar',  ['viewescucharmas'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Permisos Escuchar +']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Procesos Usuarios .sip (Encuestas)') ?></label>
                        <?= Html::a('Aceptar',  ['viewusuariosencuestas'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Procesos Usuarios .sip']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-paperclip" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Actualizar Url & Transcipciones Encuestas') ?></label>
                        <?= Html::a('Aceptar',  ['buscarurls'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Actualizar url encuestas']) 
                        ?>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-cogs" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Plan de Valoración') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarplan'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Plan Valoración']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Equipos Fuera de Distribución') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarequipos'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Plan Valoración']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-upload" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Agregar Asesores Masivos') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarasesores'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Agregar Asesores Masivos']) 
                        ?>
                    </div>
                </div>

            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Formularios, No Guardar Pcrc') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarpcrc'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Agregar Formularios, Sin Pcrc']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Formularios, Agregar No Aplica') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarfuncionapcrc'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Agregar Formularios, Agregar N/A']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Formularios, Responsabilidades Manuales') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarresponsabilidad'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Agregar Formularios con Responsabilidades']) 
                        ?>
                    </div>
                </div>
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-envelope" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Enviar Mensajes - Encuestas de Satisfacción') ?></label>
                        <?= Html::a('Aceptar',  ['adminmensajes'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Enviar Mensajes']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-cog" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Actualizar Centros de Costos') ?></label>
                        <?= Html::a('Aceptar',  ['adminpcrc'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Actualizar Centros de Costos']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-paperclip" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Verificar Acciones Genesys Banco') ?></label>
                        <?= Html::a('Aceptar',  ['admingenesys'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Verificar Acciones Genesys']) 
                        ?>
                    </div>
                </div>
                
            </div>

            <hr>

            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Servicios en Cortes ') ?></label>
                        <?= Html::a('Aceptar',  ['cortesyservicios'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Servicios en Cortes']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Motivos de Declinaciones Encuestas') ?></label>
                        <?= Html::a('Aceptar',  ['viewmotivosdeclinacion'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Motivos de Declinaciones Encuestas']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Pilares GPTW') ?></label>
                        <?= Html::a('Aceptar',  ['viewpilaresgptw'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Pilares GPTW']) 
                        ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Áreas de Apoyo GPTW') ?></label>
                        <?= Html::a('Aceptar',  ['viewareaapoyogptw'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Áreas de Apoyo GPTW']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Procesos Satisfacción Clientes') ?></label>
                        <?= Html::a('Aceptar',  ['viewprocesossatisfaccion'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Procesos Satisfacción Clientes']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Detalle de Pilares de GPTW') ?></label>
                        <?= Html::a('Aceptar',  ['viewdetallepilaresgptw'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Detalle de Pilares de GPTW']) 
                        ?>
                    </div>
                </div>
            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Indicadores de Satisfacción de Clientes') ?></label>
                        <?= Html::a('Aceptar',  ['viewindicadores'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Indicadores de Satisfacción de Clientes']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Agregar Usuarios Administrativos - Masivos') ?></label>
                        <?= Html::a('Aceptar',  ['adminusuarios'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Agregar Usuarios Administrativos']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar API - Interacciones') ?></label>
                        <?= Html::a('Aceptar',  ['adminapiwiasae'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Adminitrar configuración API']) 
                        ?>
                    </div>
                </div>
            </div> 
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Ejecutar Encuestas - Genesys') ?></label>
                        <?= Html::a('Aceptar',  ['gnssatisfaccion/index'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Adminitrar configuración API']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar PCRC para Métrica Comdata') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarpcrccomdata'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar PCRC para Métrica Comdata - Didi']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Credenciales Power BI') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarpbi'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Credenciales PBI']) 
                        ?>
                    </div>
                </div>
            </div> 
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Encuestas Aleatorias') ?></label>
                        <?= Html::a('Procesos Aleatorios',  ['aleatorioencuestas'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Procesos Aleatorios Encuestas']) 
                            ?>
                    </div>
                </div>
               
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Procesos de Héroes') ?></label>
                        <?= Html::a('Descargar Listados',  ['viewheroes'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Descargar Listados']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Agregar PCRC para atributos críticos ') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarpcrcatributoscriticos'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar PCRC para atributos críticos']) 
                        ?>
                    </div>
                </div>

            </div>
            <hr>
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar PCRC valoraciones Comdata') ?></label>
                        <?= Html::a('Aceptar',  ['parametrizarpcrcvaloracionescomdata'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar PCRC valoraciones Comdata']) 
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<br>
<div id="IdCapaCuatro" class="CapaCuatro" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
          <div class="card1 mb" style="background: #6b97b1; ">
            <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Acciones Administrativas Quejas y Reclamos"; ?> </label>
          </div>
        </div>
    </div>

    <br>
    
    <div class="row">
        <div class="col-md-12">
        
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Tipo de Alertas Q&R') ?></label>
                        <?= Html::a('Aceptar',  ['viewtipoalertasqyr'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Parametrizar Tipo de Alertas Q&R']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Áreas Q&R') ?></label>
                        <?= Html::a('Aceptar',  ['viewareasqyr'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Áreas Q&R']) 
                        ?>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-clone" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Parametrizar Tipologías') ?></label>
                        <?= Html::a('Aceptar',  ['viewtipologiasqyr'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Tipologías']) 
                        ?>
                    </div>
                </div>
            </div>       
       
        <hr>
        <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Respuestas Automáticas Q&R') ?></label>
                        <?= Html::a('Aceptar',  ['viewrespuestaautomaticaqyr'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Respuesta Automática Q&R']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Estados Q&R') ?></label>
                        <?= Html::a('Aceptar',  ['viewestadosqyr'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Estados Q&R']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Cumplimiento y alertas Q&R') ?></label>
                        <?= Html::a('Aceptar',  ['viewalertacumplimientoqyr'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Cumplimiento y alertas Q&R']) 
                        ?>
                    </div>
                </div>
        </div>
        <hr>
    <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-file" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Actualización Carta Respuesta') ?></label>
                        <?= Html::a('Aceptar',  ['viewcartarespuestaqyr'], ['class' => 'btn btn-primary',                                        
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Actualización Carta de Respuesta']) 
                        ?>
                    </div>
                </div>
                
        </div>
    <br>
    </div>
</div>
<?php }else{ ?>
    <div id="IdCapaTres" class="CapaTres" style="display: inline;">
        
        <div class="row">
            <div class="col-md-6">
                <div class="card1 mb" style="background: #6b97b1; ">
                    <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Informacón Importante"; ?> </label>
                </div>
            </div>
        </div>

        <br>

        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-info" style="font-size: 40px; color: #ffc034;"></em> Para acceder al modulo contactar al administrador de la herramienta para asignar permisos a su usuario. </label>
                </div>
            </div>
        </div>
    </div>
<hr>
<?php } ?>