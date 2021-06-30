<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;

$this->title = 'Dashboard -- VOC --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Parametrización de Categorias -- QA & Speech --';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

    $txtConteo = 0;
    // $txtidcliente = $txtidcliente;
    // var_dump($txtidcliente);

?>
<!-- <style type="text/css">
    .masthead {
      height: 25vh;
      min-height: 100px;
      background-image: url('../../images/dashboardVOC.jpg');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      /*background: #fff;*/
      border-radius: 5px;
      box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style> -->
<!-- <header class="masthead"> -->
  <!-- <div class="container h-100"> -->
    <!-- <div class="row h-100 align-items-center"> -->
      <!-- <div class="col-12 text-center"> -->
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      <!-- </div> -->
    <!-- </div> -->
  <!-- </div> -->
<!-- </header> -->
<!-- <br> -->
<br>
<div class="form-group">
    <div onclick="general();" class="btn btn-primary" style="display:inline;" method='post' id="botones1" >
        Actualizar proceso
    </div>   
    &nbsp;&nbsp;
    <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #7d7d7d;" method='post' id="botones2" >
        Regresar
    </div>  
</div>
<br>
<div class="col-sm-12" id="idCapa0" style="display: inline">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
        <tr>
            <th class="text-center"><?= Yii::t('app', 'Codigo pcrc') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Programa') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Categoria Id') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Nombre Categoria') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Tipo Categoria') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Responsabilidad') ?></th>
            <th class="text-center"><?= Yii::t('app', 'Seleccione') ?></th>
        </tr>
        <?php
            $varListVar = Yii::$app->db->createCommand("select idspeechcategoria, cod_pcrc, programacategoria, idcategoria, nombre, dashboard, tipocategoria, responsable FROM tbl_speech_categorias  where anulado = 0 and cod_pcrc in ('$txtCodPcrc') AND 
                                                        nombre not IN( SELECT tipoindicador FROM tbl_speech_categorias  where anulado = 0 and cod_pcrc in ('$txtCodPcrc')) AND idcategorias IN(1,2)")->queryAll(); 
		$txtConteo = 0;            
		$varnomresp = '';
            foreach ($varListVar as $key => $value) {
                $varDash = $value['dashboard'];
                $varIdC = $value['idspeechcategoria'];
                $varrespon = $value['responsable'];
                $txtConteo = $txtConteo + 1;
                if($varrespon == 1){
                    $varnomresp = 'Agente';
                }
                if($varrespon == 2){
                    $varnomresp = 'Canal';
                }
                if($varrespon == 3){
                    $varnomresp = 'Marca';
                }
        ?>
            <tr>
            <td class="text-center"><?php echo $value['cod_pcrc']; ?></td>
                <td class="text-center"><?php echo $value['programacategoria']; ?></td>
                <td class="text-center"><?php echo $value['idcategoria']; ?></td>
                <td class="text-center"><?php echo $value['nombre']; ?></td>
		        <td class="text-center"><?php echo $value['tipocategoria']; ?></td>
                <td class="text-center"><?php echo $varnomresp; ?></td>
                  
                <td class="text-center">
                    <div class="row">
                    <?php $var3 = [$varIdC.'A' => 'Agente ', $varIdC.'B' => 'Canal', $varIdC.'C' => 'Marca']; ?>

                    <?= $form->field($model, 'dashboard')->dropDownList($var3, ['prompt' => 'Seleccione...', 'id'=>"$txtConteo"])->label('') ?>
                    </div>
                </td>             
            </tr>
        <?php
            }
        ?>
    </table>
    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    function general(){
        var varConteo = "<?php echo $txtConteo; ?>";
        var idcliente = "<?php echo $txtidcliente; ?>";

        for (var i = 1; i <= varConteo; i++) {
            varc = i;
            var varlist = document.getElementById(varc).value;
	    //alert(varConteo);
            //alert(varlist);
            if (varlist != "") {
                $.ajax({                    
                    method: "get",
                    url: "ingresardashboard",
                    data : {
                        vardash: varlist,
                        varcont : varc,
                    },
                    success : function(response){ 
                            var respuesta = JSON.parse(response);
                            console.log(respuesta);
                    }
                });
            }
        }
        
	//window.location.href='categoriasview?txtServicioCategorias='+idcliente;
    };

    function regresar(){
        var idcliente = "<?php echo $txtidcliente; ?>";        
        
	window.location.href='categoriasview?txtServicioCategorias='+idcliente;
    };
</script>