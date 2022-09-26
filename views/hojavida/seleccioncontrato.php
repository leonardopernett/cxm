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

$this->title = 'Gestor de Clientes -- Seleccion de Clientes';
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
<div id="capaIdGeneral" class="capaGeneral" style="display: inline;">
    
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Información General') ?></label>

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span><?= Yii::t('app', ' Seleccionar Cliente') ?></label>

                        <?=  $form->field($model, 'cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                          [
                                            'id' => 'idinfocliente',
                                            'prompt'=>'Seleccionar Servicio...',
                                            'onchange' => '
                                                $.get(
                                                    "' . Url::toRoute('hojavida/listardirectores') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#requester2").html(res);
                                                    }
                                                );
                                            ',

                                          ]
                            )->label(''); 
                        ?>
                    </div>

                    <div class="col-md-6">
                        <div class="panel panel-default">
                          <div class="panel-body">
                            <label style="font-size: 13px;"><?= Yii::t('app', ' Importante: Tener presente que para seleccionar mas de un dato en el centro de costos y/o director, se debe hacer con la tecla Ctrl sostenida y dando clic a los items deseados.') ?></label>
                          </div>
                        </div>
                    </div>                    

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span><?= Yii::t('app', ' Seleccionar Director') ?></label>
                        <?= $form->field($model,'director', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar Director...',
                                            'id' => 'requester2',
                                            'multiple' => true,
                                            'onclick' => '
                                                
                                                $.get(
                                                    "' . Url::toRoute('hojavida/listarpcrcindex') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                          $("#requester").html(res);
                                                    }
                                                );
                                            ',
                                        ]
                                    )->label('');
                        ?>
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span><?= Yii::t('app', ' Seleccionar Centro de Costos') ?></label>
                        <?= $form->field($model,'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccionar Pcrc...',
                                            'id' => 'requester',
                                            'multiple' => true,
                                        ]
                                    )->label('');
                        ?>
                    </div>
                    
                </div>

            </div>
        </div>
    </div>

</div>

<hr>

<div id="capaIdBtns" class="capaBtns" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em><?= Yii::t('app', ' Información Cliente') ?></label> 
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                          ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                              'data-toggle' => 'tooltip',
                              'title' => 'Aceptar Informacion',
                              'onclick' => 'verificardata();',
                              'id'=>'modalButton']) 
                ?> 
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label> 
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-primary',
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
    function verificardata(){
        var varidinfocliente = document.getElementById("idinfocliente").value;
        var varrequester = document.getElementById("requester").value;
        var varrequester2 = document.getElementById("requester2").value;

        if (varidinfocliente == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un servicio","warning");
            return;
        }

        if (varrequester == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un pcrc","warning");
            return;
        }

        if (varrequester2 == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un director","warning");
            return;
        }
    };
</script>