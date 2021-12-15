<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;

$this->title = 'DashBoard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
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

?>
<div class="formularios-form" style="display: inline" id="CapaCero">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
        <?=  $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Arboles::find()->distinct()->where("arbol_id in (2,98)")->andwhere("activo = 0")->all(), 'id', 'name'),['prompt'=>'Seleccione Cliente QA...'])->label('Cliente Desde QA'); 
        ?>

        <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosVolumendirector::find()->distinct()->where("anulado = 0")->all(), 'id_dp_clientes', 'cliente'),
                                        [
                                            'prompt'=>'Seleccione Cliente Speech...',
                                            'onchange' => '
                                                $.post(
                                                    "' . Url::toRoute('dashboardspeech/listarpcrc') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#requester").html(res);
                                                    }
                                                );
                                            ',

                                        ]
                            )->label('Cliente Speech'); 
        ?>

        <br>
        <div class="form-group" class="text-center">
            <?= Html::submitButton(Yii::t('app', 'Guardar Parametros'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'onclick' => 'verificar();',
                        'title' => 'Guardar Parametros']) 
            ?> 
        </div>

    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    function verificar(){
        var varCQA = document.getElementById("speechservicios-arbol_id").value;
        var varCSpeech = document.getElementById("speechservicios-id_dp_clientes").value;

        if (varCQA == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar un Cliente QA.","warning");
            return;
        }else{
            if (varCSpeech == "") {
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar un Cliente Speech.","warning");
                return;
            }
        }

    };
</script>