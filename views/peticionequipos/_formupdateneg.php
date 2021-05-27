<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\modal;

$this->title = 'Desvincular Tecnico';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $varDesvin = $txtIdDesvin;

    $varvalorador = $txtvalorador;
    $varCoordinador = $txtCoordinador;
    $varcorreo = $txtCorreo;

    $varNameT = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varvalorador")->queryScalar();
    $varNameC = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varCoordinador")->queryScalar(); 

?>
<div class="control-procesos-index">
    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                'style' => 'background-color: #707372',
                'data-toggle' => 'tooltip',
                'title' => 'Regresar']) 
    ?> 
    <br>
    <br>
    <div class="page-header" >
        <h3 style="color:#100048;"><center><?= Html::encode($this->title) ?></center></h3>
    </div> 
    <br>
    <br>
    <table align="center" border="1" class="egt table table-hover table-striped table-bordered">
    	<thead>
    		<tr>
	    		<th><?= Yii::t('app', 'Coordinador Actual') ?></th>
	    		<th><?= Yii::t('app', 'Valorador') ?></th>
    		</tr>
    	</thead>
    	<tbody>
    		<tr>
    			<td><?=  $varNameC; ?></td>
    			<td><?=  $varNameT; ?></td>
    		</tr>
    	</tbody>
    </table>
    <br>
    <br>
    <div onclick="nodesvincular();" class="btn btn-primary" style="display:inline; width:25%; background: #4298B4;" method='post' id="botones5" >
        Rechazar Desvinculación
    </div> 
</div>

<script type="text/javascript">
	function nodesvincular(){
		var varNamesC = '<?php echo $varCoordinador; ?>';		
		var varNamesT = '<?php echo $varvalorador; ?>';
		var varDesvin = '<?php echo $varDesvin; ?>';
		var varEmail = '<?php echo $varcorreo; ?>';

		    $.ajax({
                method: "post",
                //url: "https://172.20.100.50/qa/web/index.php/peticionequipos/desvincular",
                url: "nodesvincular",
                data : {         
                	txtNamesC : varNamesC,
                	txtNamceT : varNamesT,
                	txtDesvin : varDesvin,
			txtEmail  : varEmail,
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response); 
                    // console.log("a",numRta);

                    if (numRta == 1) {
                        location.href ="https://qa.grupokonecta.local/qa_managementv2/web/index.php/peticionequipos/index";
                    }else{
                        event.preventDefault();
                            swal.fire("!!! Advertencia !!!","No es posible realizar dicha accion.","warning");
                        return;
                    }
                }
            });
	};
</script>