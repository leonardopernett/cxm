<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use yii\db\Query;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\modal;

$this->title = 'Permisos para reportes Power BI'; 
$this->params['breadcrumbs'][] = $this->title;
$varid = $_GET['id'];

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';
$idreportepbi = "'".$idreporte."'";
$areatrabajopbi = "'".$areatrabajo."'";
?>
<br>
<?= Html::a('Regresar',  ['reporte','varid'=>$varid], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
    ?>
<br>
<div class="page-header" >
    <h3 style="text-align: center;"><?= Html::encode($this->title) ?></h3>
    <h3 style="text-align: center;"><?= $nombrerepor ?></h3>
</div>

<br>
	<?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>
    <div class="row">
        <div class="col-md-offset-2 col-sm-8">
            <?=
                $form->field($model, 'evaluados_id')->label(Yii::t('app','Usuario'))
                ->widget(Select2::classname(), [
                    'language' => 'es',
                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
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
                                    $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                        dataType: "json",
                                    type: "post"
                                    }).done(function(data) { callback(data.results[0]);});
                                                }
                                }')
                    ]
                ] 
                );
            ?>
        </div>
    </div>        
        
   <br>
   <br>
	<?php ActiveForm::end(); ?>
	<div class="row" style="text-align: center">      
        <div onclick="permisousa();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
          	Crear permiso
        </div>
    </div>

<br>
<div id="dtbloque2" class="col-sm-12">
	<br>
	<table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <caption>Tabla datos</caption>
		<tr>
			<th scope="col" class="text-center"><?= Yii::t('app', 'Nombre') ?></th>
			<th scope="col" class="text-center"><?= Yii::t('app', 'cédula') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Usuario Red') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Usuario Id') ?></th>
			<th scope="col" class="text-center"><?= Yii::t('app', '') ?></th>
		</tr>
		
			<?php
			    $txtQuery2 =  new Query;
                $txtQuery2  ->select(['tbl_usuarios.usua_nombre','tbl_usuarios.usua_identificacion','tbl_usuarios.usua_usuario','tbl_usuarios.usua_id'])
                            ->from('tbl_usuarios')
                            ->join('LEFT OUTER JOIN', 'tbl_permisos_reportes_powerbi',
                            	   'tbl_usuarios.usua_id = tbl_permisos_reportes_powerbi.id_usuario')
                            ->where('tbl_permisos_reportes_powerbi.id_reporte ='.$idreportepbi.'')
                            ->andwhere('tbl_permisos_reportes_powerbi.id_workspace ='.$areatrabajopbi.'');
                $command = $txtQuery2->createCommand();
                $dataProvider = $command->queryAll();				

				foreach ($dataProvider as $key => $value) {			
					
			?>
			<tr>
				<td class="text-center"><?php echo $value['usua_nombre']; ?></td>
				<td class="text-center"><?php echo $value['usua_identificacion']; ?></td>
                <td class="text-center"><?php echo $value['usua_usuario']; ?></td>
                <td class="text-center"><?php echo $value['usua_id']; ?></td>
                <td class="text-center">
				    <?= Html::button('<span class="fa fa-trash"></span>', ['class' => 'btn btn-danger',
	                                    'data-toggle' => 'tooltip',
	                                    'onclick' => "eliminarDato('".$value['usua_id']."')",
	                                    'title' => 'Eliminar']) 
	                ?>
				</td> 
			</tr>
			<?php
				}
			?>
		
	</table>
</div>

<hr>
<script type="text/javascript">
	
function permisousa(){
    var varidusurio = document.getElementById("controlprocesosequipos-evaluados_id").value;
	var varidrepor = "<?php echo $idreporte; ?>";
    var vararearab = "<?php echo $areatrabajo; ?>";
	var varnombrerep = "<?php echo $nombrerepor; ?>";
    if (varidusurio == "" ) {
			event.preventDefault();
				swal.fire("!!! Advertencia !!!","Falta seleccionar el usuario","warning");
			return;
      }else{
        $.ajax({
              method: "post",
              url: "crearpermiso",
              data : {
                  var_Idusuario: varidusurio,
	              var_Idrepor: varidrepor,
	              var_Areatrab: vararearab,                
              },
              success : function(response){ 
                          var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          if (Rta != "") {
                                        window.location.href='permisosreporte?model='+Rta+'&workspace='+vararearab+'&reporte='+varidrepor+'&nombrerepor='+varnombrerep; 
                      }
              }
        }); 
      }
    
    };
    
    function eliminarDato(params1){
		var varidusurio = params1;
		var varidrepor = "<?php echo $idreporte; ?>";
        var vararearab = "<?php echo $areatrabajo; ?>";
		var varnombrerep = "<?php echo $nombrerepor; ?>";

	    var opcion = confirm("Confirmar la eliminación del item de la lista...");

	    if (opcion == true){
		 $.ajax({
	                method: "post",
			url: "eliminarpermi",
	                data : {
	                    var_Idusuario: varidusurio,
	                    var_Idrepor: varidrepor,
	                    var_Areatrab: vararearab,
	                },
	                success : function(response){ 
				console.log(response);
				var respuesta = JSON.parse(response);
				console.log(respuesta);
				if(respuesta != ""){
                    window.location.href='permisosreporte?model='+respuesta+'&workspace='+vararearab+'&reporte='+varidrepor+'&nombrerepor='+varnombrerep;

				}else{
					alert("Error al intentar eliminar la alerta");
				}
	                }
	            });
	    }		
	};
    
</script>