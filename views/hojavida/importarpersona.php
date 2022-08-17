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
use app\models\Hojavidaroles;

$this->title = 'Gestor de Clientes - Agregar Bloque Persona';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    $sesiones =Yii::$app->user->identity->id;

    
?>
<!-- Capa Proceso -->
<div id="capaIdProceso" class="capaProceso" style="display: inline;">

    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            "method" => "post",
            "enableClientValidation" => true,
            'options' => ['enctype' => 'multipart/form-data'],
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
        ]) 
    ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-4">                
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Rol') ?></label>
                        <?=  $form->field($modelpersona, 'clasificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Hojavidaroles::find()->orderBy(['id_hvroles'=> SORT_DESC])->all(), 'id_hvroles', 'hvroles'),
                                                    [
                                                        'id' => 'idroles',
                                                        'prompt'=>'Seleccionar...',
                                                    ]
                                            )->label(''); 
                        ?>                        
                    </div>

                    
                    <div class="col-md-4">                
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Tramo de Control - Pricing/Racional') ?></label>
                        <?= $form->field($modelpersona, 'nombre_full', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idratio', 'placeholder'=>'Ingresar ratio en el pricing'])?>                       
                    </div>

                    <div class="col-md-4">                        
                        <label style="font-size: 15px;"> <?= Yii::t('app', ' Tramo del Control del Contrato') ?></label>
                        <?= $form->field($modelpersona, 'numero_fijo', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idtramo', 'placeholder'=>'Ingresar tramo del control'])?>
                                               
                    </div>
                </div>  

                <div class="row">
                    <div class="col-md-4">                        
                        <label style="font-size: 15px;"> <?= Yii::t('app', ' Salario') ?></label>
                        <?= $form->field($modelpersona, 'numero_movil', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 10, 'id'=>'idsalario', 'placeholder'=>'Ingresar el salario','onkeypress' => 'return valida(event)','onchange'=>'sumar(this.value)'])?>
                                               
                    </div>

                    <div class="col-md-4">                        
                        <label style="font-size: 15px;"> <?= Yii::t('app', ' Variable') ?></label>
                        <?= $form->field($modelpersona, 'direccion_casa', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 10, 'id'=>'idvariable', 'placeholder'=>'Ingresar la variable','onkeypress' => 'return valida(event)','onchange'=>'sumar(this.value)'])?>
                                               
                    </div>

                    <div class="col-md-4">                        
                        <label style="font-size: 15px;"> <?= Yii::t('app', ' Total Salario') ?></label>
                        <?= $form->field($modelpersona, 'identificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 10, 'id'=>'idtotalsalario', 'readonly'=>'readonly'])?>
                                               
                    </div>

                </div>

                <div class="row">

                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Perfil') ?></label>
                        <?= $form->field($modelpersona, 'direccion_oficina', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['maxlength' => 300, 'id'=>'idperfil', 'placeholder'=>'Ingresar el perfil'])?>
                    </div>

                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Funciones') ?></label>
                        <?= $form->field($modelpersona, 'email', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['maxlength' => 300, 'id'=>'idfunciones', 'placeholder'=>'Ingresar las funciones'])?>    
                    </div>
                </div>   

                <div class="row">
                    <div class="col-md-12">                
                        <label style="font-size: 15px;"> <?= Yii::t('app', ' Anexo del Contrato') ?></label>
                        <div class="row">
                            <div class="col-md-12">
                                <?= $form->field($model, 'file')->fileInput(["class"=>"input-file" ,'id'=>'idfile'])->label('') ?>
                            </div>
                        </div>
                        
                    </div>
                </div>             

            </div>
        </div>
        
    </div>
    <br>
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Agregar Datos') ?></label>
               
                <?= Html::submitButton("Subir", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
                <?= Html::a('Cancelar',  ['informacioncontrato','id_contrato'=>$id], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #707372',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
          return true;
        }
                
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    };

    function verificardata(){
        var varidroles = document.getElementById("idroles").value;
        var varidratio = document.getElementById("idratio").value;
        var varidperfil = document.getElementById("idperfil").value;
        var varidfunciones = document.getElementById("idfunciones").value;

        if (varidroles == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un rol","warning");
            return;
        }
        if (varidratio == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar ratio en el pricing","warning");
            return;
        }
        if (varidperfil == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar el perfil","warning");
            return;
        }
        if (varidfunciones == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar las funcione","warning");
            return;
        }
    };

    function sumar(valor){
        var total = 0;
        valor = parseInt(valor);

        total = document.getElementById('idtotalsalario').value;
        total = (total == null || total == undefined || total == "") ? 0 : total;
        total = (parseInt(total) + parseInt(valor));
        document.getElementById('idtotalsalario').value  = total;
    };
</script>