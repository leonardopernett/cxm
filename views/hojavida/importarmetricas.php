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
use app\models\Hojavidainforme;
use app\models\Hojavidaperiocidad;
use app\models\Hojavidametricas;

$this->title = 'Gestor de Clientes - Agregar Bloque Persona';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    $sesiones =Yii::$app->user->identity->id;

    $varPenaliza = ['1'=>'Si','2'=>'No'];
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
                    <div class="col-md-6">                
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Métrica') ?></label>                          
                        <?=  $form->field($modelpersona, 'clasificacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Hojavidametricas::find()->orderBy(['id_hvmetrica'=> SORT_DESC])->all(), 'id_hvmetrica', 'hvmetrica'),
                                                    [
                                                        'id' => 'idmetrica',
                                                        'prompt'=>'Seleccionar...',
                                                    ]
                                            )->label(''); 
                        ?>                   
                    </div>

                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Objetivo') ?></label>
                        <?= $form->field($modelpersona, 'numero_fijo', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idobjetivo', 'placeholder'=>'Ingresar Objetivo'])?>
                                               
                    </div>
                </div>  

                <div class="row">
                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Penalización') ?></label>
                        <?= $form->field($modelpersona, "email", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varPenaliza, ['prompt' => 'Seleccionar...', 'id'=>"idpenaliza"]) ?> 
                    </div>
                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Rangos de Penalización') ?></label>
                        <?= $form->field($modelpersona, 'direccion_oficina', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idrango', 'placeholder'=>'Ingresar rango de penalización'])?>    
                    </div>
                </div>   

                <div class="row">
                    <div class="col-md-12">                
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Anexo del Contrato') ?></label>
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
        var varidroles = document.getElementById("idmetrica").value;
        var varidsalario = document.getElementById("idobjetivo").value;
        var varidtramo = document.getElementById("idpenaliza").value;
        var varidrango = document.getElementById("idrango").value;

        if (varidroles == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar metrica","warning");
            return;
        }
        if (varidsalario == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar objetivo","warning");
            return;
        }
        if (varidtramo == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar Penalizacion","warning");
            return;
        }
        if (varidrango == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar rango de penalizacion","warning");
            return;
        }
    };
</script>