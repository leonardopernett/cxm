<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;
use yii\helpers\ArrayHelper;

$sessiones = Yii::$app->user->identity->id;
$txtFechaActual = date("Y-m-d"); 

$txtListResponsable = Yii::$app->db->createCommand("select distinct usua_id, usua_nombre from tbl_usuarios inner join tbl_control_procesos on  tbl_usuarios.usua_id = tbl_control_procesos.responsable group by tbl_usuarios.usua_nombre")->queryAll();
$listData2 = ArrayHelper::map($txtListResponsable, 'usua_id', 'usua_nombre');
?>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<div class="CapaUno" id="CapaUno">
    <div class="row">
        <div class="col-md-12">
            <label style="font-size: 15px;">¿El técnico a desvincular, pertenecia a su equipo?...</label>
            <?php $var = ['1' => 'Si', '2' => 'No']; ?>
            <?= $form->field($model, 'motivo')->dropDownList($var, ['prompt' => 'Seleccione...', 'id'=>"selectid"])->label('') ?> 
        </div>
    </div>
</div>
<hr>
<div class="CapaDos" id="CapaDos" style="display: inline;">
    <div class="row">
        <div class="col-md-12">            
            <label style="font-size: 15px;">Buscar técnico...</label>
            <?php
                echo $form->field($model, 'evaluados_id')->label('')->widget(Select2::classname(), [
                        'language' => 'es',
                        'name' => 'subi_calculo1',
                        'class' => 'form-control',
                        'options' => [
                            'placeholder' => Yii::t('app', 'Select ...'),
                            'id' => 'subi_calculo1'
                        ],
                        'pluginOptions' => [
                            'allowClear' => true,
                            'minimumInputLength' => 3,
                            'maximumSelectionSize' => 5,
                            'ajax' => [
                                'url' => \yii\helpers\Url::to(['usuariolista']),
                                'dataType' => 'json',
                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                            ],
                            'initSelection' => new JsExpression('function (element, callback) {
                                                var id=$(element).val();
                                                if (id !== "") {
                                                    $.ajax("' . Url::to(['usuariolista']) . '?id=" + id, {
                                                        dataType: "json",
                                                        type: "post"
                                                    }).done(function(data) { callback(data.results);});
                                                }
                                            }')
                        ]
                ]);
            ?>
        </div>
        <div class="col-md-12">            
            <label style="font-size: 15px;">Indicar el motivo de desvinculación...</label>
            <?php echo $form->field($model, 'motivo')->textInput(['maxlength' => 200, 'class'=>"form-control", 'id'=>'idmotivo'])->label('') ?>
        </div>
        <div class="col-md-12">            
            <label style="font-size: 15px;">Indicar correo de respuesta en desvinculación...</label>
            <?php echo $form->field($model, 'correo')->textInput(['maxlength' => 200, 'class'=>"form-control", 'id'=>"idcorreo"])->label('') ?>

            <?php echo $form->field($model, 'solicitante_id')->textInput(['maxlength' => 200, 'value' => $sessiones, 'class'=>'hidden']) ?>            
            <?php echo $form->field($model, 'fechacreacion')->textInput(['maxlength' => 200, 'value'=>$txtFechaActual, 'class'=>"hidden form-control", 'label'=>""]) ?>
            <?php echo $form->field($model, 'anulado')->textInput(['maxlength' => 1, 'value' => 0, 'class'=>"hidden form-control", 'label'=>""]) ?>
        </div>
        <div class="col-md-12"> 
            <?= HTML::submitButton($model->isNewRecord ? 'Enviar Peticion' : 'controlprocesos/desvincular', ['onclick' => 'verificar();', 'class'=>$model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>            
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>
<script type="text/javascript">
    function verificar(){
        var varselectid = document.getElementsByTagName('selectid').value;
        var varUsuario = document.getElementById("subi_calculo1").value;
        var varidmotivo = document.getElementsByTagName('idmotivo').value;
        var varidcorreo = document.getElementsByTagName('idcorreo').value;

        if (varselectid == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","No es posible guardar, debe seleccionar si el técnico es del equipo o no","warning");
            return;
        }else{
            if (varUsuario == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","No es posible guardar, debe seleccionar el técnico","warning");
                return;
            }else{
                if (varidmotivo == "") {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","No es posible guardar, debe ingresar el motivo","warning");
                    return;
                }else{
                    if (varidcorreo == "") {
                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","No es posible guardar, debe ingresar el correo","warning");
                        return;
                    }
                }
            }
        }

    };
</script>