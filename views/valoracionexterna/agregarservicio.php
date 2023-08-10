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

$this->title = 'Gestor Valoraciones Externas - Agregar Servicio';
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

?>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Proceso Informacion General -->

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

<div id="capaIdGeneral" class="capaGeneral" style="display: inline;">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Seleccionar Cliente') ?></label>
                        <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Arboles::find()->where(['=','activo',0])->andwhere(['IN','arbol_id',[2,98,4216,4008]])->andwhere(['NOT LIKE','name','no usar'])->orderBy(['name'=> SORT_ASC])->all(), 'id', 'name'),
                                                        [
                                                            'id' => 'idProcesos',
                                                            'prompt'=>'Seleccionar...',
                                                        ]
                                                )->label(''); 
                            ?> 
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-check" style="font-size: 20px; color: #337ab7;"></em><?= Yii::t('app', ' Seleccionar Sociedad') ?></label>
                        <?=  $form->field($model, 'id_sociedad', 
                          ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                          ->dropDownList(ArrayHelper::map(\app\models\Hojavidasociedad::find()->distinct()->where("anulado = 0")->all(), 'id_sociedad', 'sociedad'),
                                                          [
                                                              'prompt'=>'Seleccione Sociedad...',
                                                              'id' => 'id_sociedad',
                                                          ]
                                                  )->label('');
                        ?>
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
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Registrar') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Registro General']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #337ab7;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
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
        var varid_sociedad = document.getElementById("id_sociedad").value;
        var varidinfocliente = document.getElementById("idProcesos").value;

        if (varidinfocliente == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente","warning");
            return;
        }
        if (varid_sociedad == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar una sociedad","warning");
            return;
        }
    }

    
    <?php  
    if(base64_decode(Yii::$app->request->get("varAlerta")) === "1"){ ?>       
      swal.fire("Información","Accion ejecutada Correctamente","success"); 
    <?php } ?>
    
</script>