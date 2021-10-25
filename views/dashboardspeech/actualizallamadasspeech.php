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



$this->title = 'DashBoard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Configuración de Categorias -- CXM & Speech --';

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

// Html::a('Auto Grupo A',  Url::to(['automaticspeecha','varNumber' => 1,'varArbol'=>$model->id_dp_clientes]), ['class' => 'btn btn-success',
                            // 'style' => 'background-color: #337ab7',
                            // 'data-toggle' => 'tooltip',
                            // 'title' => 'Automaticas Speech']) 

?>
<div id="capaOne" class="capaPP" style="display: inline;">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', 'Seleccione Servicio') ?></label>
                        <?=  $form->field($model, 'cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'nameArbol'),
                                            [
                                                'prompt'=>'Seleccionar motivo de contacto...',
                                            ]
                                )->label(''); 
                        ?>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', 'Seleccione Rango de fecha') ?></label>
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
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= Html::submitButton(Yii::t('app', 'Actualizar llamadas'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'activar();',
                                'title' => 'Buscar']) 
                        ?>
                    </div>                    
                </div>
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
                <caption>Llamadas</caption>
                    <thead>
                        <tr>
                            <th scope="col" class="text-center"><div class="loader"></div></th>
                            <th scope="col"><?= Yii::t('app', '') ?></th>
                            <th scope="col" class="text-justify"><h4><?= Yii::t('app', 'Actualizando llamadas de speech, por favor espere...') ?></h4></th>
                        </tr>            
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    function activar(){
        var varcapaOne = document.getElementById("capaOne");
        var varcapaTwo = document.getElementById("capaTwo");

        var vararbol = document.getElementById("speechservicios-cliente").value;
        var varfechas = document.getElementById("speechservicios-fechacreacion").value;

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