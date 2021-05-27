<?php

use yii\helpers\Html;
/* use yii\widgets\ActiveForm; */
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $model app\models\Detalleparametrizacion */
/* @var $form yii\widgets\ActiveForm */
$preCadenaDiv = explode("-", $model->configuracion);
$cadenaDiv = explode("||", $preCadenaDiv[0]);
?>
<?php yii\widgets\Pjax::begin(['id' => 'form_detalle']); ?>  
<div class="detalleparametrizacion-form">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'options' => ['data-pjax' => true, 'id' => 'formConfiguracion']]); ?>
    <?= $form->field($model, 'id')->hiddenInput(['id' => 'id'])->label(false) ?>
    <?= $form->field($model, 'categoria')->dropDownList($model->getCategorias(), ['id' => 'categoria', 'prompt' => 'Seleccione ...']) ?>
    <?= $form->field($model, 'configuracion')->radioList(['Y' => 'Y(AND)', 'O' => 'O(OR)'], ["id" => "operadores"]); ?>


    <?php echo $form->field($model, 'configuracion')->dropDownList(['<' => '<', '>' => '>', '<=' => '<=', '>=' => '>=', '==' => '=='], ["id" => "operacion", 'prompt' => 'Seleccione ...'])->label(false); ?>
    <?php
    echo $form->field($model, 'configuracion')->dropDownList(['0' => '0', '1' => '1', '2' => '2', '3' => '3', '4' => '4', '5' => '5', '6' => '6', '7' => '7', '8' => '8', '9' => '9']
            , ["id" => "numero", 'prompt' => 'Seleccione ...'])->label(false);
    ?>
    <?= $form->field($model, 'configuracion')->textInput(["id" => "configuracion","readonly"=>true]) ?>
    
    <?php $displayCheck = ($model->isNewRecord)? 'none' : 'block'?>
    <div id="checkAddNA" style="display: <?= $displayCheck ?>">
    <?= $form->field($model, 'addNA')->checkBox(['label' => "Activo"]); ?>    
    </div>
    
    <?php echo Html::hiddenInput('idparame', $idparame, ['id' => 'idparame']); ?>
    <?php echo Html::hiddenInput('nombre', $nombre, ['id' => 'nombre']); ?>
    <?php echo Html::hiddenInput('prioridad', $prioridad, ['id' => 'prioridad']); ?>
    <?php echo Html::hiddenInput('idcategoriagestion', $idcategoriagestion, ['id' => 'idcategoriagestion']); ?>

    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::a($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), "javascript:void(0)", ['class' => $model->isNewRecord ? 'btn btn-success soloGuardar' : 'btn btn-primary soloGuardar']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), "javascript:void(0)", ['class' => 'btn btn-default soloVolver']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?php yii\widgets\Pjax::end(); ?>

<script type="text/javascript">
    $(document).ready(function () {        
        $("#detalleparametrizacion-addna").change(function () {
            temp = $('input:text[name="Detalleparametrizacion[configuracion]"]').val();            
            if(this.checked) {
                $('input:text[name="Detalleparametrizacion[configuracion]"]').val(temp + '-O||==||NO APLICA');
            }else{                
                deleteNA();
            }
        });
                
        function deleteNA(){
            temp = $('input:text[name="Detalleparametrizacion[configuracion]"]').val();            
            ntemp = temp.replace('-O||==||NO APLICA', '');
            $('input:text[name="Detalleparametrizacion[configuracion]"]').val(ntemp);
            $( "#detalleparametrizacion-addna" ).prop( "checked", false );
        }
        
        function fnForm2Array(strForm) {

            var arrData = new Array();

            $("input[type=text], input[type=hidden], input[type=password], input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
                if ($(this).attr('name')) {
                    arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
                }
            });

            return arrData;

        }
        
        function fnForm2ArrayValidar(strForm) {

            var arrData = new Array();

            $("input[type=text],  input[type=checkbox]:checked, input[type=radio]:checked, select, textarea", $('#' + strForm)).each(function () {
                if ($(this).attr('name')) {
                    arrData.push({'name': $(this).attr('name'), 'value': $(this).val()});
                }
            });

            return arrData;

        }

        $(".soloGuardar").click(function () {
            ruta = '<?php echo Url::to(['create']); ?>';
            datosValidar = fnForm2ArrayValidar('formConfiguracion');
            for (i = 0; i < datosValidar.length; i++) {
                if (datosValidar[i]['value'] == null || datosValidar[i]['value'] == '') {
                    alert("Hay campos sin diligenciar");
                    return;
                }
            }
            datos = fnForm2Array('formConfiguracion');
            $.ajax({
                type: 'POST',
                cache: false,
                url: ruta,
                data: datos,
                success: function (response) {
                    $('#ajax_result').html(response);
                }
            });
        });


        $(".soloVolver").click(function () {
            ruta = '<?php echo Url::to(['index', 'id' => $idparame, 'categoriagestion' => $idcategoriagestion]); ?>';
            $.ajax({
                type: 'POST',
                cache: false,
                //cambiar url
                url: ruta,
                success: function (response) {
                    $('#ajax_result').html(response);
                }
            });
        });

        $('input:radio[name="Detalleparametrizacion[configuracion]"]').change(function () {
            console.log('entra');
            var operadorlogico = $('input:radio[name="Detalleparametrizacion[configuracion]"]:checked').val();
            var operacion = $("#operacion").val();
            var numero = $("#numero").val();
            if (operadorlogico != '' && operacion != '' && numero != '') {
                $('input:text[name="Detalleparametrizacion[configuracion]"]').val(operadorlogico + "||" + operacion + "||" + numero);
                deleteNA();
                $("#checkAddNA").show();
            }else{
                deleteNA();                
                $("#checkAddNA").hide();
            }
        });
        $('#operacion').change(function () {
            var operadorlogico = $('input:radio[name="Detalleparametrizacion[configuracion]"]:checked').val();
            var operacion = $("#operacion").val();
            var numero = $("#numero").val();
            if (operadorlogico != null && operacion != '' && numero != '') {
                $('input:text[name="Detalleparametrizacion[configuracion]"]').val(operadorlogico + "||" + operacion + "||" + numero);
                $("#checkAddNA").show();
                deleteNA();
            }else{
                deleteNA();
                $("#checkAddNA").hide();
            }
        });
        $('#numero').change(function () {
            var operadorlogico = $('input:radio[name="Detalleparametrizacion[configuracion]"]:checked').val();
            var operacion = $("#operacion").val();
            var numero = $("#numero").val();
            if (operadorlogico != null && operacion != '' && numero != '') {
                $('input:text[name="Detalleparametrizacion[configuracion]"]').val(operadorlogico + "||" + operacion + "||" + numero);
                $("#checkAddNA").show();
                deleteNA();
            }else{
                deleteNA();
                $("#checkAddNA").hide();
            }
        });
    });    
    
     $(document).ready(function () {
        $('input:radio[value="<?php echo $cadenaDiv[0] ?>"]').attr('checked','checked');
        $('#operacion > option[value="<?php echo (isset($cadenaDiv[1]))? $cadenaDiv[1]:''; ?>"]').attr('selected', 'selected');
        $('#numero > option[value="<?php echo (isset($cadenaDiv[2]))? $cadenaDiv[2]:''; ?>"]').attr('selected', 'selected');        
        });
</script>