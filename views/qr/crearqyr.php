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
use app\models\Casosqyr;

	$this->title = 'Crear Caso QyR';
	$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';
    $sessiones = Yii::$app->user->identity->id;

?>

<style type="text/css">
    @import url('https://fonts.googleapis.com/css?family=Nunito');

    .card {
            height: 500px;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card1 {
            height: auto;
            width: auto;
            margin-top: auto;
            margin-bottom: auto;
            background: #FFFFFF;
            position: relative;
            display: flex;
            justify-content: center;
            flex-direction: column;
            padding: 10px;
            box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/qyr.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../../css/font-awesome/css/font-awesome.css"  >
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br> 
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
        <div class="col-md-6">
                <div class="card1 mb" style="background: #6b97b1; ">
                    <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Reporte de Casos de Quejas y Reclamos "; ?> </label>
                </div>
            </div>
        </div>   
        <br>
        <div class="col-md-12">
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-6">                
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Cliente') ?></label>
                        <?=  $form->field($modelcaso, 'cliente', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\UsuariosEvalua::find()->groupby('clientearea')->orderBy(['clientearea'=> SORT_ASC])->all(), 'idusuarioevalua', 'clientearea'),
                                          [
                                              'prompt'=>'Seleccionar...',
                                              'id'=>'txtcliente',
                                          ]
                                  )->label(''); 
                          ?>                       
                    </div>
                    <div class="col-md-6">                
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Tipo Solicitud') ?></label>
                        <?=  $form->field($modelcaso, 'id_estado_caso', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Tipopqrs::find()->orderBy(['id'=> SORT_DESC])->all(), 'id', 'tipo_de_dato'),
                                                    [
                                                        'id' => 'idperiodo',
                                                        'prompt'=>'Seleccionar...',
                                                    ]
                                            )->label(''); 
                        ?>                       
                    </div>                    
                </div>  
                <div class="row">
                    
                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Nombre Usuario') ?></label>
                        <?= $form->field($modelcaso, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idalcance', 'placeholder'=>'Ingresar nombre completo'])?>
                                               
                    </div>

                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Ingresar Documento') ?></label>
                        <?= $form->field($modelcaso, 'documento', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idfunciones', 'placeholder'=>'Ingresar Número Documento'])?>    
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Ingresar Correo') ?></label>
                        <?= $form->field($modelcaso, 'correo', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idfunciones', 'placeholder'=>'Ingresar Correo Electrónico'])?>    
                    </div>
                    <div class="col-md-6">                        
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343">*</span> <?= Yii::t('app', ' Comentarios') ?></label>
                        <?= $form->field($modelcaso, 'comentario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['maxlength' => 500, 'id'=>'idperfil', 'placeholder'=>'Ingresar Comentario', 'style' => 'resize: vertical;'])?>
                    </div> 
                </div>   

                <div class="row">
                    <div class="col-md-12">                
                        <label style="font-size: 18px;"><em class="fas fa-hand-pointer" style="font-size: 18px; color: #3d7d58;"></em><?= Yii::t('app', ' Anexar Documento') ?></label>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'file')->fileInput(["class"=>"input-file" ,'id'=>'idfile', 'style'=>'font-size: 18px;'])->label('') ?>
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
                
                    <?= Html::submitButton("Guardar", ["class" => "btn btn-primary", "onclick" => "cargar();"]) ?>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card1 mb">
                    <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #981F40;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
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
        var varidroles = document.getElementById("identregable").value;
        var varidsalario = document.getElementById("idalcance").value;
        var varidtramo = document.getElementById("idperiodo").value;
        var varidratio = document.getElementById("idfunciones").value;

        if (varidroles == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un entregable","warning");
            return;
        }
        if (varidsalario == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un alcance","warning");
            return;
        }
        if (varidtramo == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar una periocidad","warning");
            return;
        }
        if (varidratio == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar las funciones","warning");
            return;
        }
    };
</script>