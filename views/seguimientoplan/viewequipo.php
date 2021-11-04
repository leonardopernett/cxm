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

$this->title = 'Seguimiento Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

$template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

$month = date('m');
$year = date('Y');
$day = date("d", mktime(0,0,0, $month+1, 0, $year));
             
$varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
$varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

$sessiones1 = Yii::$app->user->identity->id;


if ($varcordi != null) {
	$varlistusuarios = Yii::$app->db->createCommand("select u.usua_id, u.usua_nombre, sum(cp2.cant_valor) 'Cantidad', cp1.tipo_corte from  tbl_usuarios u 	inner join tbl_control_params cp2 on u.usua_id = cp2.evaluados_id inner join tbl_control_procesos cp1 on 	cp2.evaluados_id = cp1.evaluados_id where cp1.responsable = $varcordi and cp2.anulado = 0 and cp1.anulado = 0 and cp2.fechacreacion between '$varfechainicio' and '$varfechafin' and cp1.fechacreacion between '$varfechainicio' and '$varfechafin' group by cp1.evaluados_id")->queryAll();

	$varNamecoordi = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varcordi")->queryScalar();
}

if ($varusuar != null) {
	$varlistcoordi = Yii::$app->db->createCommand("select u.usua_id, u.usua_nombre, cp.tipo_corte from tbl_usuarios u inner join tbl_control_procesos cp on u.usua_id = cp.responsable where cp.evaluados_id = $varusuar and cp.anulado = 0 and cp.fechacreacion between '$varfechainicio' and '$varfechafin'")->queryAll();

	$varNameusua = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varusuar")->queryScalar();
}

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
            font-family: "Nunito";
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
        /*background: #fff;*/
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
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">            	
            	<label><em class="fas fa-id-card" style="font-size: 20px; color: #ff2c2c;"></em> </label>
            	<?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]) ?>
            		<div class="row">
                    	<div class="col-md-6">
                    	<label style="font-size: 15px;">* Seleccionar Coordinador... </label>
                    	<?=
                            $form->field($model, 'responsable', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label(Yii::t('app',''))
                                  ->widget(Select2::classname(), [
                                      //'data' => array_merge(["" => ""], $data),
                                      'language' => 'es',
                                      'options' => ['id'=>"coordinadoresid",'placeholder' => Yii::t('app', 'Seleccionar Coordinador...')],
                                      'pluginOptions' => [
                                          'allowClear' => true,
                                          'minimumInputLength' => 4,
                                          'ajax' => [
                                              'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                              'dataType' => 'json',
                                              'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                              'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                          ],
                                          'initSelection' => new JsExpression('function (element, callback) {
                                              var id=$(element).val();
                                              if (id !== "") {
                                                  $.ajax("' . Url::to(['controlvoc/evaluadolistmultiple']) . '?id=" + id, {
                                                      dataType: "json",
                                                      type: "post"
                                                  }).done(function(data) { callback(data.results);});
                                              }
                                          }')
                                      ]
                                ]
                            );
                        ?>
	                    </div>
	                    <div class="col-md-6">
	                    <label style="font-size: 15px;">* Seleccionar Tecnico/Lider... </label>
                    	<?=
                            $form->field($model, 'evaluados_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label(Yii::t('app',''))
                                  ->widget(Select2::classname(), [
                                      //'data' => array_merge(["" => ""], $data),
                                      'language' => 'es',
                                      'options' => ['id'=>"tecnicosid",'placeholder' => Yii::t('app', 'Seleccionar Tecnico/Lider...')],
                                      'pluginOptions' => [
                                          'allowClear' => true,
                                          'minimumInputLength' => 4,
                                          'ajax' => [
                                              'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                              'dataType' => 'json',
                                              'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                              'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                          ],
                                          'initSelection' => new JsExpression('function (element, callback) {
                                              var id=$(element).val();
                                              if (id !== "") {
                                                  $.ajax("' . Url::to(['controlvoc/evaluadolistmultiple']) . '?id=" + id, {
                                                      dataType: "json",
                                                      type: "post"
                                                  }).done(function(data) { callback(data.results);});
                                              }
                                          }')
                                      ]
                                ]
                            );
                        ?>
	                    </div>
	                </div>
	                <br>
	                <div class="row">
	                    <div class="col-md-3">
                            <div class="card1 mb">
	                    		<?= Html::submitButton(Yii::t('app', 'Buscar'),
		                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
		                            'data-toggle' => 'tooltip', 'style' => 'height: 37px;',
		                            'title' => 'Buscar']) 
		              ?>
	                    	</div>
	                    </div>
	                    <div class="col-md-3">
                            <div class="card1 mb">
	                    		<?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
                                ?>
	                    	</div>
	                    </div>
	                    <div class="col-md-6">
                            <div class="card1 mb">
	                    		<label style="font-size: 17px;"><em class="fas fa-hand-paper" style="font-size: 20px; color: #ff2c2c;"></em> Importante indicar que solo se realiza la consulta sobre datos del mes actual. </label>
	                    	</div>
	                    </div>
	                </div>
            	<?php ActiveForm::end() ?>
            </div>
        </div>
    </div>
</div>
<hr>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">            	
            	<label><em class="fas fa-list" style="font-size: 20px; color: #ff2c2c;"></em> </label>
            	<?php if($varcordi != null) { ?>
            		<br>
            		<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Equipo</caption>
            			<thead>
            				<tr>
            					<th scope="col" colspan="5" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo  'Coordinador: '.$varNamecoordi; ?></label></th>
            				</tr>
            				<tr>
	            				<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tecnico/Lider') ?></label></th>
	            				<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Meta') ?></label></th>
	            				<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo corte') ?></label></th>
            				</tr>
            			</thead>
            			<tbody>
            				<?php 
            					foreach ($varlistusuarios as $key => $value) {            						
            				?>
            					<tr>
	      							<td><label style="font-size: 12px;"><?php echo  $value['usua_nombre']; ?></label></td>
	      							<td><label style="font-size: 12px;"><?php echo  $value['Cantidad']; ?></label></td>
	      							<td><label style="font-size: 12px;"><?php echo  $value['tipo_corte']; ?></label></td>
	      						</tr>
            				<?php
            					}
            				?>
            			</tbody>
            		</table>
            	<?php } ?>
            	<?php if($varusuar != null) { ?>
            		<br>
            		<table id="tblData2" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Corte</caption>
            			<thead>
            				<tr>
            					<th scope="col" colspan="5" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo  'Tecnico/Lider: '.$varNameusua; ?></label></th>
            				</tr>
            				<tr>
	            				<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Coordinador A Cargo') ?></label></th>
	            				<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Corte') ?></label></th>
	            			</tr>
            			</thead>
            			<tbody>
            				<?php 
            					foreach ($varlistcoordi as $key => $value) {            						
            				?>
            					<tr>
	      							<td><label style="font-size: 12px;"><?php echo  $value['usua_nombre']; ?></label></td>
	      							<td><label style="font-size: 12px;"><?php echo  $value['tipo_corte']; ?></label></td>
	      						</tr>
            				<?php
            					}
            				?>
            			</tbody>
            		</table>
            	<?php } ?>
            </div>
        </div>
    </div>
</div>
<hr>