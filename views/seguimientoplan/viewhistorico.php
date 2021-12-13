<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Seguimiento Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

$sesiones =Yii::$app->user->identity->id;

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

$varRol = Yii::$app->get('dbslave')->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ur on r.role_id = ur.rel_role_id  inner join tbl_usuarios u on ur.rel_usua_id = u.usua_id where u.usua_id = $sesiones")->queryScalar();

$varNombre = Yii::$app->get('dbslave')->createCommand("select usua_nombre from tbl_usuarios where usua_id = $sesiones")->queryScalar();

$month = date('m');
$year = date('Y');
$day = date("d", mktime(0,0,0, $month+1, 0, $year));
     
$varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
$varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

$varmes = date('m') - 2;

$varlistcortes = null;
if ($roles == "270" || $roles == "309") {
	$varlistcortes = Yii::$app->get('dbslave')->createCommand("select idtc, tipocortetc 'tipo' from tbl_tipocortes where mesyear between '$year-$varmes-01' and '$year-$month-01' group by tipocortetc order by idtc asc")->queryAll();	
}else{
	if ($roles == "274" || $roles == "276") {
		$varlistcortes = Yii::$app->get('dbslave')->createCommand("select tipo_corte 'tipo', idtc from tbl_control_procesos where responsable = $sesiones  and anulado = 0  group by idtc order by idtc desc limit 2")->queryAll();	
	}
}

$listData = ArrayHelper::map($varlistcortes, 'idtc', 'tipo');

$txtFechainicio = null;
$txtFechafin = null;
if ($varidtc != null) {
	$txtnombrecorte = Yii::$app->db->createCommand("select tipocortetc from tbl_tipocortes where idtc = $varidtc")->queryScalar();
	$txtFechainicio = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where idtc = $varidtc")->queryScalar();
	$txtFechafin = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where idtc = $varidtc")->queryScalar();

	if ($roles == "270" || $roles == "309") {
		$varlistaplan = Yii::$app->get('dbslave')->createCommand("select * from tbl_control_procesos where idtc = $varidtc and anulado = 0")->queryAll();	
	}else{
		if ($roles == "274" || $roles == "276") {
			$varlistaplan = Yii::$app->get('dbslave')->createCommand("select * from tbl_control_procesos where idtc = $varidtc and responsable in ('$sesiones')  and anulado = 0")->queryAll();
		}
	}
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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
	    width: 100%;
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
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<br><br><br>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label><em class="fas fa-question-circle" style="font-size: 20px; color: #2CA5FF;"></em> Rol del usuario:</label>
                <label><?php echo $varRol; ?></label>
                <br>
                <label><em class="fas fa-user-circle" style="font-size: 20px; color: #2CA5FF;"></em> Usuario:</label>
                <label><?php echo $varNombre; ?></label>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card1 mb">
                <label><em class="fas fa-search" style="font-size: 20px; color: #2CA5FF;"></em> Corte a seleccionar:</label>
                <?php $form = ActiveForm::begin([
                    'options' => ["id" => "buscarMasivos"],
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'inputOptions' => ['autocomplete' => 'off']
                      ]
                    ]); ?>
        			<?= $form->field($model, 'idtc')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'idtcs']) ?> 
        			<br>
        			<div class="row" align="center">  
		              <?= Html::submitButton(Yii::t('app', 'Buscar corte'),
		                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
		                            'data-toggle' => 'tooltip', 'style' => 'height: 37px;',
		                            'title' => 'Buscar']) 
		              ?>
		            </div>
        		<?php $form->end() ?> 
            </div>
        </div>
    </div> 
</div>
<hr>
<div id="capaDos" style="display: inline">   
    <div class="row">
        <div class="col-md-12">
        	<div class="card1 mb">
                <label><em class="fas fa-list" style="font-size: 20px; color: #FFC72C;"></em> Lista de valoraciones:</label>
                <?php if ($varidtc != null) { ?>
                <br>
	                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Lista Valoraciones</caption>
	                	<thead>	  
	                		<tr>
	                			<th scope="col" colspan="5" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo  $txtnombrecorte; ?></label></th>
	                		</tr>              		
	                		<tr>
		                		<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rol') ?></label></th>
		                		<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tecnico/Lider') ?></label></th>
		                		<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Meta') ?></label></th>
		                		<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Realizadas') ?></label></th>
		                		<th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '% de cumplimiento') ?></label></th>
	                		<tr>
	                	</thead>
	                	<tbody>
	                		<?php
	                			$txtcumplimiento = 0;
	                			foreach ($varlistaplan as $key => $value) {
	                				$txtEvaluadoId = $value['evaluados_id'];
	                				$txtRol = Yii::$app->get('dbslave')->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ur on r.role_id = ur.rel_role_id  inner join tbl_usuarios u on ur.rel_usua_id = u.usua_id where u.usua_id = $txtEvaluadoId")->queryScalar();
	                				$txtNombreE = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $txtEvaluadoId")->queryScalar();

	                				$txtcantidad = Yii::$app->get('dbslave')->createCommand("select sum(cant_valor) from tbl_control_params where evaluados_id = $txtEvaluadoId and fechacreacion between '$txtFechainicio' and '$txtFechafin' and anulado = 0")->queryScalar();                                    

	                				$txtrealizadas = Yii::$app->get('dbslave')->createCommand("select count(ef.created) from tbl_ejecucionformularios ef inner join tbl_usuarios u on ef.usua_id = u.usua_id where ef.created between '$txtFechainicio 00:00:00' and '$txtFechafin 23:59:59' and u.usua_id = $txtEvaluadoId")->queryScalar();

	                				if ($txtcantidad != 0 && $txtcantidad !=  null) {
	                					$txtcumplimiento = round(($txtrealizadas / $txtcantidad) * 100);
	                				}else{
	                					$txtcumplimiento = 0;
	                				}
	                				
	                		?>
	                			<tr>
	      							<td><label style="font-size: 12px;"><?php echo  $txtRol; ?></label></td>
	      							<td><label style="font-size: 12px;"><?php echo  $txtNombreE; ?></label></td>
	      							<td><label style="font-size: 12px;"><?php echo  $txtcantidad; ?></label></td>
	      							<td><label style="font-size: 12px;"><?php echo  $txtrealizadas; ?></label></td>
	      							<td><label style="font-size: 12px;"><?php echo  $txtcumplimiento.'%'; ?></label></td>
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
<div id="capaTres" style="display: inline">   
    <div class="row">
    	<div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                	<div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #FFC72C;"></em> Exportar Archivo: </label> 
                            <a id="dlink" style="display:none;"></a>
    						<button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<script type="text/javascript" charset="UTF-8">
var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
        return function (table, name) {
            if (!table.nodeType) table = document.getElementById(table)
            var ctx = {
                worksheet: name || 'Worksheet',
                table: table.innerHTML
            }
            console.log(uri + base64(format(template, ctx)));
            document.getElementById("dlink").href = uri + base64(format(template, ctx));
            document.getElementById("dlink").download = "Seguimiento Equipo de trabajo";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData', 'Archivo Seguimiento', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>