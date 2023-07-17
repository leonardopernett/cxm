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


$this->title = 'Héroes por el Cliente - Procesos Parametrizador';
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

    $varConteoEquipo = (new \yii\db\Query())
                              ->select(['tbl_equipos_evaluados.equipo_id'])
                              ->from(['tbl_equipos_evaluados'])   
                              ->join('LEFT OUTER JOIN', 'tbl_evaluados',
                                    'tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id')         
                              ->where(['=','tbl_evaluados.id',$txtEvaluadoid])                              
                              ->count();
    
    $txtConjuntoSpeech = "Id_Heroes: ".$varPostulacion_id;
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
if ($varConteoEquipo != "0") { 

    $form = ActiveForm::begin(['layout' => 'horizontal', 'action' => \yii\helpers\Url::to(['guardarpaso2'])]);

?>

<!-- Capa Procesamiento -->
<div class="capaInfo" id="idCapaInfo" style="display: inline;">


    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Ficha Técnica & Acciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Formulario de Valoración') ?></label><br>
                <label  style="font-size: 15px; text-align: center;"><?php echo $varNombreArbol; ?></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Dimensión a Valorar') ?></label><br>
                <label  style="font-size: 15px; text-align: center;"><?php echo $varNombreDimension; ?></label>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Asesor a Valorar') ?></label><br>
                <label  style="font-size: 15px; text-align: center;"><?php echo $varNombreAsesor; ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Tipo Postulación') ?></label><br>
                <label  style="font-size: 15px; text-align: center;"><?php echo $varTipoPostula; ?></label>
            </div>
        </div>

        <div class="col-md-8">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Datos Postulación') ?></label><br>
                <label  style="font-size: 15px; text-align: center;"><?php echo $varDatosPostula; ?></label>
            </div>
        </div>

    </div>

</div>

<hr>

<!-- Capa Btn -->
<div class="capaValora" id="idCapaValora" style="display: inline;">

    <div class="capaInvisible" id="capaIdInvisible" style="display: none;">
        <?= $form->field($modelA, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idvararbol', 'class'=>'hidden', 'readonly'=>'readonly', 'value'=>$varArbol_id])?>

        <?= Html::input("hidden", "evaluado_id", $txtEvaluadoid); ?>

        <?= $form->field($modelD, 'dimension_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idvardimension', 'class'=>'hidden', 'readonly'=>'readonly', 'value'=>$vardimensiones])?>

        <?= Html::input("hidden", "dsfuente_encuesta", $txtConjuntoSpeech); ?>

        <?= $form->field($modelE, 'name', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 250, 'class'=>'hidden', 'id'=>'idname', 'value' => $varNombreAsesor])->label('') ?>

    </div>

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Realizar Valoración') ?></label>
                <?=
                            Html::submitButton(Yii::t('app', 'Aceptar'), ['class' => 'btn btn-success'])
                ?> 
            </div>
        </div>

    </div>
    
</div>
<?php ActiveForm::end(); ?>

<?php }else{ ?>

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
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-2" align="text-center">
                        <label style="font-size: 15px;"><em class="fas fa-hand-stop" style="font-size: 50px; color: #C148D0;"></em></label>
                    </div>

                    <div class="col-md-10" align="left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Ok, actualmente el asesor seleccionado no esta asignado a ningún equipo, por lo tanto no es posible realizar niguna valoración. Por favor verificar correctamente el asesor desde el módulo de reporte de héroes.') ?></label>
                    </div>
                </div>          

            </div>        
        </div>

    </div>

</div>

<?php } ?>


<hr>
