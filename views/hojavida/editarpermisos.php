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

$this->title = 'Gestor de Clientes - Permisos';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

?>
<style>
    .card1 {
            height: 213px;
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
            height: 152px;
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

    .card3 {
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


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<?php $form = ActiveForm::begin([
    'options' => ["id" => "buscarMasivos"],
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>
<div class="capaPrincipal" style="display: inline;">
    <div class="row">
        <div class="col-md-12">

        	<div class="row">
        		<div class="col-md-6">
        			<div class="card3 mb">
        				<label><em class="fas fa-info-circle" style="font-size: 20px; color: #2CA5FF;"></em> Notificaci&oacute;n: </label>
	        			<div class="panel panel-default">
	                        <div class="panel-body" style="background-color: #f0f8ff;">Procesos de permisos en el m&oacute;dulo Hoja de Vida para el usuario <?php echo $varNombre; ?>.
	                        </div>
	                    </div>
        			</div>        			
        		</div>
        	</div>

        </div>
    </div>
</div>
<hr>
<div class="capaDos" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			<div class="card3 mb">
				<label><em class="fas fa-cogs" style="font-size: 20px; color: #2CA5FF;"></em> Seleccionar Permisos Acciones: </label>

				<div class="row">
					<div class="col-md-2">
						<?=  $form->field($model,'hveliminar')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'ideliminar'))->label('Eliminar Registros'); ?>
					</div>
					<div class="col-md-2">
						<?=  $form->field($model,'hveditar')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'ideditar'))->label('Editar Registros'); ?>
					</div>
					<div class="col-md-2">
						<?=  $form->field($model,'hvcasrgamasiva')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'idmasiva'))->label('Importar Datos'); ?>
					</div>
					<div class="col-md-2">
						<?=  $form->field($model,'hvdatapersonal')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'iddata'))->label('Guardar Registros'); ?>
					</div>
					<div class="col-md-2">
						<?=  $form->field($model,'hvverresumen')->checkbox(array('value' => '1', 'uncheckValue'=>'0', 'id'=>'idver'))->label('Ver Resumen General'); ?>
					</div>
				</div>
				<br>
				
				<?= Html::submitButton('Actualizar Permisos', ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'btn_submit'] ) ?>					
				
			</div>
		</div>
	</div>
</div>
<hr>
<div class="capaTres" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			<div class="card3 mb">
				<label><em class="fas fa-cogs" style="font-size: 20px; color: #2CA5FF;"></em> Seleccionar Permisos Servicios & Pcrc: </label>

				<div class="row">
					<div class="col-md-12">
						<br>
						<?= 
			                Html::button('Ingresar Nuevo Servicio', ['value' => url::to(['createdservicio','idusuario'=>$varUsuario,'idaccion'=>$idacciones]), 'class' => 'btn btn-success', 'style' => 'background-color: #337ab7', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Ingresar Servicio'])
			            ?>
			            <?php
			                Modal::begin([
			                    'header' => '<h4></h4>',
			                    'id' => 'modal2',
			                ]);

			                echo "<div id='modalContent2'></div>";
			                                                        
			                Modal::end(); 
			            ?> 						
					</div>
				</div>

				<div class="row">
					<div class="col-md-12">
						<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
							<caption><?php echo "Resultados Servicios: " ?></caption>
							<thead>
								<tr>
									<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Id') ?></label></th>
						            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Servicio') ?></label></th>
						            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Eliminar') ?></label></th>
								</tr>
							</thead>
							<tbody>
								<?php
									foreach ($dataProviderClientes as $key => $value) {
								?>
									<tr>
										<td><label style="font-size: 12px;"><?php echo  $value['hv_idpermisocliente']; ?></label></td>
						                <td><label style="font-size: 12px;"><?php echo  $value['cliente']; ?></label></td>
						                <td class="text-center">
						                    <?= Html::a('<em class="fas fa-times" style="font-size: 15px; color: #FC4343;"></em>',  ['eliminarservicio','id'=> $model->hv_idacciones, 'idDos' => $value['hv_idpermisocliente']], ['class' => 'btn btn-primary', 'data-toggle' => 'tooltip', 'style' => " background-color: #337ab700;", 'title' => 'Eliminar']) ?>
						                </td>
									</tr>
								<?php
									}
								?>
							</tbody>
						</table>
					</div>
				</div>

				<br>



			</div>
		</div>
	</div>
</div>
<hr>
<div class="capaCuatro" style="display: inline;">
	<div class="row">
		<div class="col-md-12">
			

			<div class="row">
				<div class="col-md-6">
					<div class="card3 mb">
						<label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                        <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                        'style' => 'background-color: #707372',
                                                        'data-toggle' => 'tooltip',
                                                        'title' => 'Regresar']) 
                        ?>
					</div>
				</div>
			</div>

		</div>
	</div>
</div>
