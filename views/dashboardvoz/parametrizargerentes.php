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

$this->title = 'Dashboard Ejecutivo';
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

    $query = new Query;
    $query      ->select(['tbl_arbols.id','tbl_arbols.name'])->distinct()
                ->from('tbl_arbols')
                ->where("tbl_arbols.arbol_id in (2, 98)")
                ->andwhere("tbl_arbols.activo = 0")
                ->orderBy(['tbl_arbols.name'=> SORT_ASC]);
    $command = $query->createCommand();
    $querys = $command->queryAll();
    $listData = ArrayHelper::map($querys, 'id', 'name');

?>
<div class="form" style="display: inline" id="IdCapaCero">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

        <?=  $form->field($model, 'ciudad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Arboles::find()->distinct()->where("id in (2, 98)")->all(), 'name', 'name'),
                        [
                            'prompt'=>'Seleccione Ciudad...',
                            'onchange' => '
                                $.post(
                                    "' . Url::toRoute('dashboardvoz/listasciudad') . '", 
                                    {id: $(this).val()}, 
                                    function(res){
                                        $("#requester").html(res);
                                    }
                                );
                            ',

                        ]
                    )->label('Ciudad'); 
        ?>

        <?= $form->field($model,'iddirectores', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                        [],
                        [
                            'prompt' => 'Seleccione Director...',
                            'id' => 'requester'
                        ]
                    )->label('Director');
        ?>

        <?= $form->field($model, 'gerentes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'id_gerente'])->label('Nombre Gerente') ?>

        <?= $form->field($model, 'documentogerentes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'id_documentogerentes'])->label('Documento Gerente') ?>

        <?= $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'id_Arbol'])->label('Cleinte') ?>

        <br>
        <div class="form-group" align="center">
            <?= Html::submitButton(Yii::t('app', 'Guardar Parametrización'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'onclick' => 'verificar();',
                        'title' => 'Guardar Parametrización']) 
            ?> 
        </div>


    <?php ActiveForm::end(); ?>
</div>

<script type="text/javascript">
    function verificar(){
        var varCiudad = document.getElementById("vozseleccion-ciudad").value;
        var varDirector = document.getElementById("requester").value;
        var varNomGerente = document.getElementById("id_gerente").value;
        var varDocGerente = document.getElementById("id_documentogerentes").value;
        var varArbol = document.getElementById("id_Arbol").value;

        if (varCiudad == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe seleccionar la ciudad.","warning");
            return;
        }else{
            if (varDirector == "") {
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","Debe seleccionar el director","warning");
                return;
            }else{
                if (varNomGerente == "") {
                    event.preventDefault();
                    swal.fire("¡¡¡ Advertencia !!!","Nombre del Gerente Vacio, por favor ingresarlo","warning");
                    return;
                }else{
                    if (varDocGerente == "") {
                        event.preventDefault();
                        swal.fire("¡¡¡ Advertencia !!!","Documento del Gerente Vacio, por favor ingresarlo","warning");
                        return;
                    }else{
                        if (varArbol == "") {
                            event.preventDefault();
                            swal.fire("¡¡¡ Advertencia !!!","Debe seleccionar el Cliente","warning");
                            return;
                        }
                    }
                }
            }
        }

    };
</script>