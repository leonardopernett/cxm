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
use yii\db\Query;

$sesiones =Yii::$app->user->identity->id;   

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$varListadoNovedades = (new \yii\db\Query())
                                ->select(['*'])
                                ->from(['tbl_genesys_novedades'])
                                ->where(['=','tbl_genesys_novedades.anulado',0])
                                ->andwhere(['=','tbl_genesys_novedades.id_novedades',$id])
                                ->all();

$varIdGenesys = null;
$varIdNombre = null;
$varSelfuri = null;
foreach ($varListadoNovedades as $value) {
    $varIdGenesys = $value['id_genesys'];
    $varIdNombre = $value['nombre_asesor'];
    $varSelfuri = $value['selfUri'];
}

?>
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

<!-- Capa Datos -->
<div class="capaDatos" id="capaIdDatos" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Procesos de Información') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">

                <div class="row">
                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Id Genesys') ?></label>
                        <?= $form->field($model, 'id_genesys', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idgenesys', 'readonly'=>'readonly', 'value'=>$varIdGenesys])->label('');?>  
                    </div>

                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Nombre Asesor') ?></label>
                        <?= $form->field($model, 'nombre_asesor', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idnombre', 'readonly'=>'readonly', 'value'=>$varIdNombre])->label('');?>  
                    </div>

                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Self Url') ?></label>
                        <?= $form->field($model, 'selfUri', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idselfuir', 'readonly'=>'readonly', 'value'=>$varSelfuri])->label('');?>   
                    </div>
                </div>    

                <div class="row">
                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Documento Asesor') ?></label>
                        <?= $form->field($model, 'documento_asesor', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 20,'id'=>'iddocumento','onkeypress' => 'return valida(event)'])->label('');?> 
                    </div>

                    <div class="col-md-4">
                        <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Correo Institucional Asesor') ?></label>
                        <?= $form->field($model, 'username_asesor', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 150,'id'=>'iduser'])->label('');?> 
                    </div>
                </div>              
                
            </div>
        </div>
    </div>

</div>

<br>

<!-- Capa Botones -->
<div class="capaBtn" id="capaIdBtn" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Registro General') ?></label>
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Registro General']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #C148D0;"></em> <?= Yii::t('app', 'Cancelar y Regresar') ?></label>
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
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

    function varVerificar(){
        var variddocumento = document.getElementById("iddocumento").value;
        var variduser = document.getElementById("iduser").value;

        if (variddocumento == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar documento asesor","warning");
            return;
        }

        if (variduser == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar correo asesor","warning");
            return;
        }
    };
</script>