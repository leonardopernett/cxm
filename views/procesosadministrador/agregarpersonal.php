<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;

$this->title = 'Agregar Nuevo Personal';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol    ->select(['tbl_roles.role_id'])
            ->from('tbl_roles')
            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                    'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
            ->where(['=','tbl_usuarios.usua_id',$sessiones]);                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $listPosicion = ['21'=>'Director','24'=>'Gerente'];
?>
<style>
    @import url('https://fonts.googleapis.com/css?family=Nunito');

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

    .card2 {
            height: 70px;
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


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }


</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<br>
<!-- Capa Agregar Personal -->
<div id="capaPersonalId" class="capaPersonal" style="display: inline;">

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
	<div class="row">
		
		<div class="col-md-12">
			<div class="card1 mb">

                <label style="font-size: 15px;"><em class="fas fa-address-card" style="font-size: 15px; color: #ffc034;"></em><?= Yii::t('app', ' Agregar Usuarios') ?></label>

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Seleccionar Posición') ?></label>
                        <?= $form->field($model, 'id_dp_posicion')->dropDownList($listPosicion, ['prompt' => 'Seleccionar...', 'id'=>'idPosicion']) ?>
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Ingresar Nombre Completo') ?></label>
                        <?= $form->field($model, 'personalsatu')->textInput(['id'=>'idPersonal', 'placeholder'=>'Ingresar el nombre de la persona']) ?>                        
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Ingresar Documento Identidad') ?></label>
                        <?= $form->field($model, 'documentopersonalsatu')->textInput(['maxlength' => 20,'id'=>'idDoc', 'placeholder'=>'Ingresar número documento', 'onkeypress' => 'return valida(event)']) ?> 
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Ingresar Correo Corporativo') ?></label>
                        <?= $form->field($model, 'correosatu')->textInput(['id'=>'idEmail', 'placeholder'=>'Ingresar correo corporativo']) ?>                        
                    </div>
                </div>			
                

			</div>
		</div>
		
	</div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb">
                <?= Html::submitButton(Yii::t('app', 'Agregar'),
                      ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                          'data-toggle' => 'tooltip',
                          'title' => 'Buscar DashBoard',
                          'style' => 'display: inline;margin: 3px;height: 34px;',
                          'id'=>'modalButton1',
                          'onclick' => 'varVerificar();']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card2 mb">
                <?= Html::a('Cancelar',  ['adminmensajes'], ['class' => 'btn btn-success',
                               'style' => 'background-color: #707372',                        
                                'data-toggle' => 'tooltip',
                                'title' => 'Nuevo'])
                ?>
            </div>
        </div>
    </div>
    <?php $form->end() ?>
</div>

<hr>



<script type="text/javascript">
    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        if (tecla==8){
            return true;
        }

        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    };

	function varVerificar(){
        var varidPosicion = document.getElementById("idPosicion").value;
        var varidPersonal = document.getElementById("idPersonal").value;
        var varidDoc = document.getElementById("idDoc").value;
        var varidEmail = document.getElementById("idEmail").value;

        if (varidPosicion == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar una posicion","warning");
            return;
        }
        if (varidPersonal == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe ingresar el nombre del usuario","warning");
            return;
        }
        if (varidDoc == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe ingresar documento del usuario","warning");
            return;
        }
        if (varidDoc.length < 5) {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Tamaño del documento no permitido, ingrese nuevamente el documento","warning");
            return;
        }
        if (varidEmail == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe ingresar un correo electronico","warning");
            return;
        }
	};
</script>