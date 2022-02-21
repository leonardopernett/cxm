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
    $txtcartxtCodPcrc = $txtCodPcrc;

?>
<style type="text/css">
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

    .masthead {
      height: 25vh;
      min-height: 100px;
      background-image: url('../../images/Parametrizador.png');
      background-size: cover;
      background-position: center;
      background-repeat: no-repeat;
      border-radius: 5px;
      box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br>
<br>
<div class="capaOne" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
            <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                            <div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #707372;" method='post' id="botones2" >
                                Regresar
                            </div> 
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #FFC72C;"></em> Actualizar Proceso: </label> 
                            <div onclick="general();" class="btn btn-primary" style="display:inline;" method='post' id="botones1" >
                                Actualizar
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
            <div class="card1 mb">
                <div class="col-sm-12" id="idCapa0" style="display: inline">
                    <?php $form = ActiveForm::begin([
                        'layout' => 'horizontal',
                        'fieldConfig' => [
                            'inputOptions' => ['autocomplete' => 'off']
                          ]
                        ]); ?>
                    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <caption>Categorias</caption>
                        <tr>
                            <th scope="text-center"><?= Yii::t('app', 'Codigo pcrc') ?></th>
                            <th scope="text-center"><?= Yii::t('app', 'Programa') ?></th>
                            <th scope="text-center"><?= Yii::t('app', 'Categoria Id') ?></th>
                            <th scope="text-center"><?= Yii::t('app', 'Nombre Categoria') ?></th>
                            <th scope="text-center"><?= Yii::t('app', 'Tipo Categoria') ?></th>
                            <th scope="text-center"><?= Yii::t('app', 'Responsabilidad') ?></th>
                            <th scope="text-center"><?= Yii::t('app', 'Seleccione') ?></th>
                            <th scope="text-center"><?= Yii::t('app', 'Vertical') ?></th>
                        </tr>
                        <?php
                            
                            $varListVar = Yii::$app->db->createCommand("select idspeechcategoria, cod_pcrc, programacategoria, idcategoria, nombre, dashboard, tipocategoria, responsable, idcategorias, responsable, componentes FROM tbl_speech_categorias  where anulado = 0 and cod_pcrc in ('$txtCodPcrc')  AND idcategorias IN (1,2)")->queryAll(); 

                            $txtConteo = 0;            
                            
                            
                            foreach ($varListVar as $key => $value) {
                                $varDash = $value['dashboard'];
                                $varIdC = $value['idspeechcategoria'];
                                $varrespon = $value['responsable'];
                                $txtConteo = $txtConteo + 1;
                                $vartipo = $value['idcategorias'];
                                $varvertical = $value['componentes'];
                                $varnomresp = $value['responsable'];
                                $varnombre = $value['nombre'];
                                if($varrespon == 1){
                                    $varnomresp = 'Agente';
                                }
                                if($varrespon == 2){
                                    $varnomresp = 'Canal';
                                }
                                if($varrespon == 3){
                                    $varnomresp = 'Marca';
                                }
                                if ($varrespon == "" && $vartipo == 1) {
                                    
                                    if ($varvertical != null) {
                                        if ($varvertical == 1) {
                                            $varnomresp = 'Insatisfacción Verbalizada';
                                        }else{
                                            if ($varvertical == 2) {
                                                $varnomresp = 'Solución';
                                            }else{
                                                if ($varvertical == 3) {
                                                    $varnomresp = 'Valores Corporativos';
                                                }else{
                                                    if ($varvertical == 4) {
                                                        $varnomresp = 'Facilidad/Esfuerzo';
                                                    }else{
                                                        if ($varvertical == 5) {
                                                            $varnomresp = 'Habilidad Comercial/Venta Responsable';
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }else{
                                        $varnomresp = '--';
                                    }
                                    
                                }

                                
                                
                                $vatiene = Yii::$app->db->createCommand("SELECT COUNT(idcategoria) FROM tbl_speech_categorias  WHERE anulado = 0 AND cod_pcrc IN ('$txtCodPcrc') AND idcategorias IN(2) AND tipoindicador in ('$varnombre')")->queryScalar(); 
                                
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

                                    <?php if($vartipo == 2) { ?>
                                        <?php $var3 = [$varIdC.'A' => 'Agente ', $varIdC.'B' => 'Canal', $varIdC.'C' => 'Marca']; ?>

                                    <?php }else{ ?>
                                        <?php $var3 = ['NA' => '']; ?>
                                    <?php } ?>

                                    
                                    <?= $form->field($model, 'dashboard')->dropDownList($var3, ['prompt' => 'Seleccione...', 'id'=>"$txtConteo" ])->label('') ?>

                                    </div>
                                    
                                </td>    

                                <td class="text-center">
                                    <div>
                                    <?php if ($varvertical == null) { ?>
                                        
                                        <?php if ($vartipo == 1) { ?>

                                            <?= 
                                                Html::a(Yii::t('app', '<i id="idimage" class="fas fa-edit" style="font-size: 17px; color: #4c6ef5; display: inline;"></i>'),
                                                                    'javascript:void(0)',
                                                                    [
                                                                        'title' => Yii::t('app', 'Escucha VOC'),
                                                                        'onclick' => "     
                                                                            $.ajax({
                                                                                type     :'get',
                                                                                cache    : false,
                                                                                url  : '" . Url::to(['editarcompetencia','varcodpcrc' => $value['cod_pcrc'], 'varidcategoria' => $varIdC, 'varnombre' => $value['nombre']]) . "',
                                                                                success  : function(response) {
                                                                                    $('#ajax_result').html(response);
                                                                                }
                                                                            });
                                                                        return false;",
                                                                    ]);                            
                                            ?>

                                        <?php } ?>

                                    <?php } ?>
                                    
                                    </div>
                                </td>         
                            </tr>
                        <?php
                            }
                        ?>
                    </table>
                    <?php ActiveForm::end(); ?>
                </div>                
            </div>
        </div>
    </div>
</div>
<hr>
<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">
    function general(){
        var varConteo = "<?php echo $txtConteo; ?>";
        var idcliente = "<?php echo $txtidcliente; ?>";
        var varcodpcrc = "<?php echo $txtcartxtCodPcrc; ?>";

        for (var i = 1; i <= varConteo; i++) {
            varc = i;
            var varlist = document.getElementById(varc).value;
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
        
        location.reload();
    };

    function regresar(){
        var idcliente = "<?php echo $txtidcliente; ?>";        
        
    window.location.href='categoriasview?txtServicioCategorias='+idcliente;
    };
</script>
