<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\helpers\Url;
use yii\bootstrap\Modal;

/* @var $this yii\web\View */
/* @var $model app\models\BaseSatisfaccion */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="base-satisfaccion-form">

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>

    <?= $form->field($model, 'identificacion')->textInput(['id'=>'ididentificacion','maxlength' => 45]) ?>

    <?= $form->field($model, 'nombre')->textInput(['id'=>'idnombre','maxlength' => 200]) ?>

    <?= $form->field($model, 'ani')->textInput(['id'=>'idani','maxlength' => 200]) ?>

    <?= $form->field($model, 'agente')->textInput(['id'=>'idagente','maxlength' => 200]) ?>

    <?= $form->field($model, 'agente2')->textInput(['id'=>'idagente2','maxlength' => 200]) ?>

    <?= $form->field($model, 'ano')->textInput(['id'=>'idano','maxlength' => 11]) ?>

    <?= $form->field($model, 'mes')->textInput(['id'=>'idmes','maxlength' => 11]) ?>

    <?= $form->field($model, 'dia')->textInput(['id'=>'iddia','maxlength' => 11]) ?>

    <?= $form->field($model, 'hora')->textInput(['id'=>'idhora','maxlength' => 11]) ?>

    <?= $form->field($model, 'chat_transfer')->textInput(['id'=>'idchat_transfer','maxlength' => 45]) ?>

    <?= $form->field($model, 'ext')->textInput(['id'=>'idext','maxlength' => 100]) ?>

    <?= $form->field($model, 'rn')->textInput(['id'=>'idrn','maxlength' => 2]) ?>

    <?= $form->field($model, 'industria')->textInput(['id'=>'idindustria','maxlength' => 3]) ?>

    <?= $form->field($model, 'institucion')->textInput(['id'=>'idinstitucion','maxlength' => 3]) ?>

    <?= $form->field($model, 'pcrc')->textInput(['id'=>'idpcrc','maxlength' => 11]) ?>

    <?= $form->field($model, 'cliente')->textInput(['id'=>'idcliente','maxlength' => 11]) ?>

    <?= $form->field($model, 'tipo_servicio')->textInput(['id'=>'idtipo_servicio','maxlength' => 45]) ?>

    <?= $form->field($model, 'pregunta1')->textInput(['id'=>'idpregunta1','maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta2')->textInput(['id'=>'idpregunta2','maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta3')->textInput(['id'=>'idpregunta3','maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta4')->textInput(['id'=>'idpregunta4','maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta5')->textInput(['id'=>'idpregunta5','maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta6')->textInput(['id'=>'idpregunta6','maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta7')->textInput(['id'=>'idpregunta7','maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta8')->textInput(['id'=>'idpregunta8','maxlength' => 10]) ?>

    <?= $form->field($model, 'pregunta9')->textInput(['id'=>'idpregunta9','maxlength' => 10]) ?>

    <?= $form->field($model, 'connid')->textInput(['id'=>'idconnid','maxlength' => 100]) ?>

    <?= $form->field($model, 'tipo_encuesta')->textInput(['id'=>'idtipo_encuesta','maxlength' => 4]) ?>

    <?= $form->field($model, 'comentario')->textarea(['id'=>'idcomentario','rows' => 6]) ?>

    <?= $form->field($model, 'lider_equipo')->textInput(['id'=>'idlider_equipo','maxlength' => 100]) ?>

    <?= $form->field($model, 'coordinador')->textInput(['id'=>'idcoordinador','maxlength' => 100]) ?>

    <?= $form->field($model, 'jefe_operaciones')->textInput(['id'=>'idjefe_operaciones','maxlength' => 100]) ?>

    <?= $form->field($model, 'tipologia')->textInput(['id'=>'idtipologia','maxlength' => 2]) ?>

    <?= $form->field($model, 'estado')->dropDownList([ 'Abierto' => 'Abierto', 'En Proceso' => 'En Proceso', 'Por Contestar' => 'Por Contestar', 'Cerrado' => 'Cerrado', 'Escalado' => 'Escalado', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'llamada')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'buzon')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'responsable')->textInput(['id'=>'idresponsable','maxlength' => 100]) ?>

    <?= $form->field($model, 'usado')->dropDownList([ 'SI' => 'SI', 'NO' => 'NO', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'fecha_gestion')->textInput() ?>

    
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
            'onclick' => 'validacion();']) ?>
            <?= Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default']) ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>

    <script type="text/javascript">
function validacion(){
    var varididentificacion = document.getElementById("ididentificacion").value;
    var varidnombre = document.getElementById("idnombre").value;
    var varidani = document.getElementById("idani").value;
    var varidagente = document.getElementById("idagente").value;
    var varidagente2 = document.getElementById("idagente2").value;
    var varidchat_transfer = document.getElementById("idchat_transfer").value;
    var varidext = document.getElementById("idext").value;
    var varidrn = document.getElementById("idrn").value;
    var varidindustria = document.getElementById("idindustria").value;
    var varidinstitucion = document.getElementById("idinstitucion").value;
    var varidpcrc = document.getElementById("idpcrc").value;
    var varidcliente = document.getElementById("idcliente").value;
    var varidtipo_servicio = document.getElementById("idtipo_servicio").value;
    var varidpregunta1 = document.getElementById("idpregunta1").value;
    var varidpregunta2 = document.getElementById("idpregunta2").value;
    var varidpregunta3 = document.getElementById("idpregunta3").value;
    var varidpregunta4 = document.getElementById("idpregunta4").value;
    var varidpregunta5 = document.getElementById("idpregunta5").value;
    var varidpregunta6 = document.getElementById("idpregunta6").value;
    var varidpregunta7 = document.getElementById("idpregunta7").value;
    var varidpregunta8 = document.getElementById("idpregunta8").value;
    var varidpregunta9 = document.getElementById("idpregunta9").value;
    var varidconnid = document.getElementById("idconnid").value;
    var varidtipo_encuesta = document.getElementById("idtipo_encuesta").value;
    var varidcomentario = document.getElementById("idcomentario").value;
    var varidlider_equipo = document.getElementById("idlider_equipo").value;
    var varidcoordinador = document.getElementById("idcoordinador").value;
    var varidjefe_operaciones = document.getElementById("idjefe_operaciones").value;
    var varidtipologia = document.getElementById("idtipologia").value;
    var varidresponsable = document.getElementById("idresponsable").value;
    var varidano = document.getElementById("idano").value;
    var varidmes = document.getElementById("idmes").value;
    var variddia = document.getElementById("iddia").value;
    var varidhora = document.getElementById("idhora").value;
    
    if (varididentificacion.length>45) {
      alert(" identificacion " + " Solo se permiten 0 - 45 caracteres") ;
      return;
    }else if(varidnombre.length>200)
    {
        alert(" nombre " + " Solo se permiten 0 - 200 caracteres") ;
      return;

    }else if(varidani.length>200)
    {
        alert(" ani " + " Solo se permiten 0 - 200 caracteres") ;
      return;

    }else if(varidagente.length>200)
    {
        alert(" agente " + " Solo se permiten 0 - 200 caracteres") ;
      return;

    }else if(varidagente2.length>200)
    {
        alert(" agente2 " + " Solo se permiten 0 - 200 caracteres") ;
      return;

    }else if(varidchat_transfer.length>45)
    {
        alert(" chat transfer " + " Solo se permiten 0 - 45 caracteres") ;
      return;

    }else if(varidext.length>100)
    {
        alert(" ext " + " Solo se permiten 0 - 100 caracteres") ;
      return;

    }else if(varidrn.length>2)
    {
        alert(" rn " + " Solo se permiten 0 - 2 caracteres") ;
      return;

    }
    else if(varidindustria.length>3)
    {
        alert(" industria " + " Solo se permiten 0 - 3 caracteres") ;
      return;

    }
    else if(varidinstitucion.length>3)
    {
        alert(" institucion " + " Solo se permiten 0 - 3 caracteres") ;
      return;

    }else if(varidpcrc.length>11)
    {
        alert(" pcrc " + " Solo se permiten 0 - 11 caracteres") ;
      return;

    }else if(varidcliente.length>11)
    {
        alert(" cliente " + " Solo se permiten 0 - 11 caracteres") ;
      return;

    }else if(varidtipo_servicio.length>45)
    {
        alert(" tipo servicio " + " Solo se permiten 0 - 45 caracteres") ;
      return;

    }else if(varidpregunta1.length>10)
    {
        alert(" pregunta1 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidpregunta2.length>10)
    {
        alert(" pregunta2 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidpregunta3.length>10)
    {
        alert(" pregunta3 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidpregunta4.length>10)
    {
        alert(" pregunta4 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidpregunta5.length>10)
    {
        alert(" pregunta5 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidpregunta6.length>10)
    {
        alert(" pregunta6 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidpregunta7.length>10)
    {
        alert(" pregunta7 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidpregunta8.length>10)
    {
        alert(" pregunta8 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidpregunta9.length>10)
    {
        alert(" pregunta9 " + " Solo se permiten 0 - 10 caracteres") ;
      return;

    }else if(varidconnid.length>100)
    {
        alert(" connid " + " Solo se permiten 0 - 100 caracteres") ;
      return;

    }else if(varidtipo_encuesta.length>4)
    {
        alert(" tipo encuesta " + " Solo se permiten 0 - 4 caracteres") ;
      return;

    }
    else if(varidlider_equipo.length>100)
    {
        alert(" lider equipo " + " Solo se permiten 0 - 100 caracteres") ;
      return;

    }else if(varidcoordinador.length>100)
    {
        alert(" coordinador " + " Solo se permiten 0 - 100 caracteres") ;
      return;

    }else if(varidjefe_operaciones.length>100)
    {
        alert(" jefe operaciones " + " Solo se permiten 0 - 100 caracteres") ;
      return;

    }else if(varidtipologia.length>2)
    {
        alert(" tipologia " + " Solo se permiten 0 - 2 caracteres") ;
      return;

    }else if(varidresponsable .length>100)
    {
        alert(" responsable " + " Solo se permiten 0 - 100 caracteres") ;
      return;

    }
    else if(varidano === '')
    {
        alert("El año no puede estar vacío.") ;
        
      return;

    }
    else if(varidmes === '')
    {
        alert("El Mes no puede estar vacío.") ;
      return;
      

    }
    else if(variddia === '')
    {
        alert("El dia no puede estar vacío.") ;
      return;

    }else if(varidhora === '')
    {
        alert("La Hora no puede estar vacía.") ;
      return;

    }else if(isNaN(varidano)  || (isNaN(varidmes)) || (isNaN(variddia)) || (isNaN(varidhora))){
        alert("Ingresar Solo numeros en Año,Mes,Día,Hora ") ;
      return;
    }

    

}
    </script>
</div>

