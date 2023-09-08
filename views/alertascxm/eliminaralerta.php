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

$this->title = 'Alertas - Eliminar Alerta';
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
    background-image: url('../../../images/ADMINISTRADOR-GENERAL.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Data extensiones -->
<link rel="stylesheet" href="../../../css/font-awesome/css/font-awesome.css"  >


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
        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-calendar" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Fecha de Alerta') ?></label>
                <label style="font-size: 12px;"><?= Yii::t('app', $varFecha_Eliminar) ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Programa/Pcrc') ?></label>
                <label style="font-size: 12px;"><?= Yii::t('app', $varPcrc_Eliminar) ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Valorador') ?></label>
                <label style="font-size: 12px;"><?= Yii::t('app', $varValorador_Eliminar) ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Tipo de Alerta') ?></label>
                <label style="font-size: 12px;"><?= Yii::t('app', $varTipoAlerta_Eliminar) ?></label>
            </div>
        </div>
    </div>

</div>

<hr>

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => [
            'autocomplete' => 'off'
        ]
    ]
  ]); 
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
        <div class="col-md-8">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Ingresar Comentarios de Eliminar Alerta') ?></label>
                <?= $form->field($model, 'comentarios', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'varIdComentarios','rows'=>6,'placeholder'=>'Ingresar Comentarios'])?>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Guardar & Eliminar') ?></label> 
                <?= Html::submitButton(Yii::t('app', 'Guardar & Eliminar'),
                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                        'data-toggle' => 'tooltip',
                        'title' => 'Buscar',
                        'onclick' => 'varVerificar();',
                        'id'=>'ButtonSearch']) 
                ?>
            </div>

            <br>

            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Cancelar & Regresar') ?></label> 
                <?= Html::a('Regresar',  ['reportealerta'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #707372',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

</div>

<hr>

<?php $form->end() ?>

<script type="text/javascript">
    function varVerificar(){
        var varIdComentarios = document.getElementById("varIdComentarios").value;

        if (varIdComentarios == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No es posible eliminar la alerta, no contiene una justificación para la eliminación.","warning");
            return;
        }
    };
</script>