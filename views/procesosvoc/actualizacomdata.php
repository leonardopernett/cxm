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


$this->title = 'DashBoard Escuchar + - Actualizar Llamadas - Especiales --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Configuración de Categorias -- Actualizar Llamadas - Especiales --';

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

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

$this->registerJs(
   "$(function(){
       $('#modal1').on('hidden.bs.modal1', function (e) {
           location.reload();
        });    
});"
);

?>
<div id="capaOne" class="capaPP" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <div class="row">                 

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', 'Seleccionar Rango de Fecha') ?></label>
                        <?=
                            $form->field($model, 'fechacreacion', [
                                'labelOptions' => ['class' => 'col-md-12'],
                                'template' => 
                                '<div class="col-md-12"><div class="input-group">'
                                . '<span class="input-group-addon" id="basic-addon1">'
                                . '<i class="glyphicon glyphicon-calendar"></i>'
                                . '</span>{input}</div>{error}{hint}</div>',
                                'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                'options' => ['class' => 'drp-container form-group']
                            ])->label('')->widget(DateRangePicker::classname(), [
                                'useWithAddon' => true,
                                'convertFormat' => true,
                                'presetDropdown' => true,
                                'readonly' => 'readonly',
                                'pluginOptions' => [
                                    'timePicker' => false,
                                    'format' => 'Y-m-d',
                                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                    'endDate' => date("Y-m-d"),
                                    'opens' => 'right',
                            ]]);
                        ?>
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', 'Seleccionar Servicio') ?></label>

                        <?=  $form->field($model, 'tipoparametro', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                        [
                                            'id' => 'idClientesEsp',
                                            'prompt'=>'Seleccione Cliente Speech...',
                                            'onchange' => '
                                                $.post(
                                                    "' . Url::toRoute('procesosvoc/listarpcrcespecialcomdata') . '", 
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

                    <div class="col-md-12">
                        <label style="font-size: 15px;"><?= Yii::t('app', 'Seleccionar Pcrc') ?></label>
                        <?=
                            $form->field($model, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->checkboxList(
                                [],
                                [
                                    "id" =>"requester",
                                    'item'=>function ($index, $label, $name, $checked, $value)
                                    {
                                        return '<div class="col-md-12">
                                                    <input type="checkbox"/>'.$label.'
                                                </div>';
                                    }

                              ])->label('');
                        ?>
                        <?= $form->field($model, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 350, 'class' => 'hidden',  'id'=>'txtIdCod_pcrc']) ?>
                        
                    </div>


                    
                </div>

                <br>

                <?= Html::submitButton(Yii::t('app', 'Actualizar llamadas'),
                            ['class' => $model->isNewRecord ? 'btn btn-danger' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'activar();',
                                'title' => 'Buscar']) 
                ?>
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<div id="capaTwo" class="capaPT" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table align="center">
                    <thead>
                        <tr>
                            <th class="text-center"><div class="loader"></div></th>
                            <th><?= Yii::t('app', '') ?></th>
                            <th class="text-justify"><h4><?= Yii::t('app', 'Actualizando llamadas de WIA & SAE, por favor espere...') ?></h4></th>
                        </tr>            
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function activar(){
        var cant = document.getElementById("requester").querySelectorAll(".listach");
       
        var varpcrc = "";
        for (var x = 0; x < cant.length; x++) {
            if(document.getElementById("lista_"+(x+1)).checked){
                varpcrc = varpcrc + "'" + document.getElementById("lista_"+(x+1)).value + "'" + ",";
            }

        }
        varpcrc = varpcrc.substring(0,varpcrc.length - 2);
        varpcrc = varpcrc.substring(1);

        document.getElementById("txtIdCod_pcrc").value = varpcrc;

        var varcapaOne = document.getElementById("capaOne");
        var varcapaTwo = document.getElementById("capaTwo");

        var vararbol = document.getElementById("idClientesEsp").value;
        var varfechas = document.getElementById("speechcategorias-fechacreacion").value;

        if (vararbol == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar un servicio","warning");
            return;
        }else{
            if (varfechas == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe seleccionar un rango de fecha","warning");
                return;
            }else{
                varcapaOne.style.display = 'none';
                varcapaTwo.style.display = 'inline';
            }
        }

        
    };

</script>