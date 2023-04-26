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
use app\models\Planprocesos;

$this->title = 'Reportes Comdata - Configuraciones';
$this->params['breadcrumbs'][] = $this->title;

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
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $varConteoExistconfig = (new \yii\db\Query())
                                ->select(['tbl_comdata_permisosreportestudio.id_dp_clientes'])
                                ->from(['tbl_comdata_permisosreportestudio'])            
                                ->where(['=','tbl_comdata_permisosreportestudio.anulado',0])
                                ->andwhere(['=','tbl_comdata_permisosreportestudio.usuario_permiso',$sessiones])
                                ->all();

  $varlistiddpclientesconfig = array();
  $varserviciosconfig = null;
  if (count($varConteoExistconfig) != 0) {
    foreach ($varConteoExistconfig as $key => $value) {
      array_push($varlistiddpclientesconfig, $value['id_dp_clientes']);
    }
    $varConfiguracion  = implode(", ", $varlistiddpclientesconfig);
    $varserviciosconfig = explode(",", str_replace(array("#", "'", ";", " "), '', $varConfiguracion));
  }

?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Proceso Informacion General -->
<div id="capaIdGeneral" class="capaGeneral" style="display: inline;">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Seleccionar Cliente') ?></label>

                        
                        <?=  $form->field($modelconf, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosVolumendirector::find()->distinct()->where(['=','anulado',0])->andwhere(['=','estado',1])->andwhere(['in','id_dp_clientes',$varserviciosconfig])->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                                    [
                                                        'id' => 'txtidclientesconfig',
                                                        'prompt'=>'Seleccionar...',
                                                        'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('listarpcrcs') . '", 
                                                                {id: $(this).val()}, 
                                                                function(res){
                                                                    $("#requesterconfig").html(res);
                                                                }
                                                            );
                                                            
                                                        ',

                                                    ]
                                        )->label(''); 
                        ?>


                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Seleccionar Centros de Costos') ?></label>
                        <?= 
                          $form->field($modelconf,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                              [],
                              [                  
                                'prompt' => 'Seleccionar Pcrc...',
                                'id' => 'requesterconfig',
                              ]
                          )->label('');
                        ?> 
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-12">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Ingresar Url LockerStudio') ?></label>
                        <?= $form->field($modelconf, 'table_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idtable_id', 'placeholder'=>'Ingresar NUrl del LockerStudio'])?>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<br>

<!-- Capa Proceso Botones -->
<div id="capaIdBtn" class="capaBtn" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Registro General') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                            ['class' => $modelconf->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Registro General']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">
  function varVerificar(){
    var vartxtidclientesconfig = document.getElementById("txtidclientesconfig").value;
    var varrequesterconfig = document.getElementById("requesterconfig").value;
    var varidtable_id = document.getElementById("idtable_id").value;

    if (vartxtidclientesconfig == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente","warning");
        return;
    }

    if (varrequesterconfig == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debe de seleccionar un centro de costos","warning");
        return;
    }

    if (varidtable_id == "") {
        event.preventDefault();
        swal.fire("!!! Advertencia !!!","Debe de ingresar la url de LokerStudio","warning");
        return;
    }
  };
</script>