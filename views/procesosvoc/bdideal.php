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
use app\models\SpeechServicios;

$this->title = 'Procesos Voc - Procesar Base de Datos Ideal';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Procesos Voc - Procesar Base de Datos Ideal';

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where(['=','tbl_usuarios.usua_id',$sessiones]);                     
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $varMeses = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre'];

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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card2 {
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
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }
</style>
<div class="CapaEstuctura" style="display: inline;">

  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label style="font-size: 18px;"><em class="fas fa-hand-pointer" style="font-size: 20px; color: #ff8c55;"></em><?= Yii::t('app', ' Seleccionar Servicio') ?></label>

        <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->select(['id_dp_clientes','cliente'])->where(['=','anulado',0])->andwhere(['!=','arbol_id',1])->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                            [
                                                'id' => 'idServicios',
                                                'prompt'=>'Seleccionar...',
                                            ]
                                )->label(''); 
        ?>

        <br>

        <label style="font-size: 18px;"><em class="fas fa-hand-pointer" style="font-size: 20px; color: #ff8c55;"></em><?= Yii::t('app', ' Seleccionar Mes') ?></label>

        <?= $form->field($model, "pcrc", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varMeses, ['prompt' => 'Seleccionar...', 'id'=>"idMes"]) ?>

        <hr>

        <?= Html::submitButton(Yii::t('app', 'Procesar Datos'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'verificar();',
                                'title' => 'Procesar datos']) 
        ?>

      </div>
    </div>

  </div>
  <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
  function verificar(){
    var varidServicios = document.getElementById("idServicios").value;
    var varidMes = document.getElementById("idMes").value;

    if (varidServicios == "") {
      event.preventDefault();
      swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar un servicio","warning");
      return;
    }else{
      if (varidMes == "") {
        event.preventDefault();
        swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar un mes","warning");
        return;
      }
    }
  }
</script>