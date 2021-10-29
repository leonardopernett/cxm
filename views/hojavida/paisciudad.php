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

$this->title = 'Hoja de Vida - Pais & Ciudad';
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
            height: 216px;
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
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>


<div class="capaPrincipal" style="display: inline;">
  <div class="row">

    <div class="col-md-6">
      <div class="card2 mb">
        <label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></i> Registro de Paises: </label>
      
        <div class="row">
          <div class="col-md-12">
            <label style="font-size: 15px;">Nombre del Pais: </label>
            <?= $form->field($modelpais, 'pais', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnombrepais', 'placeholder'=>'Ingresar Nombre del Pais'])?>
          
            <?= Html::submitButton(Yii::t('app', 'Guardar Pais'),
                                ['class' => $modelpais->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Guardar pais',
                                    'onclick' => 'validarpais();']) 
            ?> 
          </div>
        </div>
      </div>
    </div>

    <div class="col-md-6">
      <div class="card2 mb">
        <label style="font-size: 15px;"><i class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></i> Registros de Ciudades: </label>
      
        <div class="row">
          <div class="col-md-12">
            <label style="font-size: 15px;">Seleccionar Pais: </label>
            <?=  $form->field($modelciudad, 'pais_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HvPais::find()->orderBy(['hv_idpais'=> SORT_DESC])->all(), 'hv_idpais', 'pais'),
                                        [
                                            'prompt'=>'Seleccionar Pais...',
                                        ]
                                )->label(''); 
            ?>

            <label style="font-size: 15px;">Nombre de la Ciudad: </label>
            <?= $form->field($modelciudad, 'ciudad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idnombreciudad', 'placeholder'=>'Ingresar Nombre de la Ciudad'])?>

            <?= Html::submitButton(Yii::t('app', 'Guardar Ciudad'),
                                ['class' => $modelciudad->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Guardar Ciudad',
                                    'onclick' => 'validarciudad();']) 
            ?> 
          </div>

        </div>
      </div>
    </div>

  </div>
</div>
<hr>
<div class="capaBotone" style="display: inline;">
  <div class="row">

    <div class="col-md-4">
      <div class="card1 mb">
        <label style="font-size: 15px;"><i class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></i> Cancelar y regresar: </label> 
        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
        ?>
      </div>  
    </div>

  </div>
</div>

<hr>
<div class="capaLista" style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <label style="font-size: 15px;"><i class="fas fa-list" style="font-size: 15px; color: #FFC72C;"></i> Lista de Paises y Ciudades: </label>
        
    </div>
  </div>
</div>
<?php $form->end() ?>
<hr>

<script type="text/javascript">
  function validarpais(){
    
  };
</script>