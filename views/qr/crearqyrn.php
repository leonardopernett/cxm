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

$this->title = 'Gestor de PQRS - Crear Casos PQRS';
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
<?php 
    $form = ActiveForm::begin([
        'layout' => 'horizontal',
        "method" => "post",
        "enableClientValidation" => true,
        'options' => ['enctype' => 'multipart/form-data'],
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
    ]) 
?>

<!-- Capa Procesos de Creacion -->
<div id="capaIdProceso" class="capaProceso" style="display: inline;">

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <div class="row">

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Cliente') ?></label>
                        <?=  $form->field($modelcaso, 'cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->where(['=','estado',1])->andwhere(['=','anulado',0])->groupby(['cliente'])->orderBy(['cliente'=> SORT_ASC])->all(), 'idvolumendirector', 'cliente'),
                            [
                                'prompt'=>'Seleccionar...',
                                'id'=>'idvarCliente',
                            ])->label(''); 
                        ?> 
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Seleccionar Tipo Solicitud') ?></label>
                        <?=  $form->field($modelcaso, 'id_estado_caso', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Tipopqrs::find()->orderBy(['id'=> SORT_DESC])->all(), 'id', 'tipo_de_dato'),
                            [
                                'id' => 'idvarSolicitud',
                                'prompt'=>'Seleccionar...',
                            ])->label(''); 
                        ?>   
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Ingresar Comentarios') ?></label>
                        <?= $form->field($modelcaso, 'comentario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['maxlength' => 500, 'id'=>'idvarComentarios', 'placeholder'=>'Ingresar Comentarios', 'style' => 'resize: vertical;'])?>
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><em class="fas fa-list" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Anexar Documentos') ?></label>
                        <?= $form->field($model, 'file', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->fileInput(["class"=>"input-file" ,'id'=>'idvarFile', 'style'=>'font-size: 18px;'])->label('') ?>
                    </div>

                </div>

            </div>
        </div>
    </div>

    <br>

    <div class="row">

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #C148D0;"></em><?= Yii::t('app', ' Agregar Datos') ?></label>
                
                <?= Html::submitButton("Guardar", ["class" => "btn btn-primary", "onclick" => "verificardata();"]) ?>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #C148D0;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
                <?= Html::a('Cancelar',  ['index'], ['class' => 'btn btn-success',
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
        var varidvarCliente = document.getElementById("idvarCliente").value;
        var varidvarSolicitud = document.getElementById("idvarSolicitud").value;
        var varidvarComentarios = document.getElementById("idvarComentarios").value;

        if (varidvarCliente == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente","warning");
            return;
        }
        if (varidvarSolicitud == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un tipo de solicitud","warning");
            return;
        }
        if (varidvarComentarios == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar comentarios","warning");
            return;
        }
    }
</script>