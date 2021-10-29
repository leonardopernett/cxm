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
<div class="form-group">
    <div onclick="general();" class="btn btn-primary" style="display:inline;" method='post' id="botones1" >
        Actualizar proceso
    </div>   
</div>
<br>
<div class="row">
    <div class="col-sm-12" id="idCapa0" style="display: inline">
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
            <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
            <caption>Tabla datos</caption>
                <tr>
                    <th scope="col" class="text-center"><?= Yii::t('app', 'Id Parametro') ?></th>
                    <th scope="col" class="text-center"><?= Yii::t('app', 'Codigo pcrc') ?></th>
                    <th scope="col" class="text-center"><?= Yii::t('app', 'Nombre pcrc') ?></th>
                    <th scope="col" class="text-center"><?= Yii::t('app', 'Parametros') ?></th>
                    <th scope="col" class="text-center"><?= Yii::t('app', 'Seleccion parametro') ?></th>
                    <th scope="col" class="text-center"><?= Yii::t('app', 'Nuevo parametro') ?></th>
                <?php
                    $varlist = Yii::$app->db->createCommand("select tbl_speech_parametrizar.idspeechparametrizar, tbl_speech_parametrizar.cod_pcrc, tbl_speech_categorias.pcrc, tbl_speech_parametrizar.rn, tbl_speech_parametrizar.ext, tbl_speech_parametrizar.usuared, tbl_speech_parametrizar.comentarios from tbl_speech_parametrizar inner join tbl_speech_categorias on tbl_speech_parametrizar.cod_pcrc = tbl_speech_categorias.cod_pcrc where tbl_speech_parametrizar.anulado = 0 and tbl_speech_categorias.anulado = 0 and tbl_speech_parametrizar.id_dp_clientes = $txtServid group by tbl_speech_parametrizar.rn, tbl_speech_parametrizar.ext, tbl_speech_parametrizar.usuared order by     tbl_speech_categorias.pcrc desc")->queryAll(); 

                    foreach ($varlist as $key => $value) {
                        $varIdspeech = $value['idspeechparametrizar'];
                        $varRn = $value['rn'];
                        $varExt = $value['ext'];
                        $varUsua = $value['usuared'];
                        $varOtros = $value['comentarios'];

                        $txtConteo = $txtConteo + 1;
                ?>
                    <tr>
                        <td class="text-center"><?php echo $varIdspeech; ?></td>
                        <td class="text-center"><?php echo $value['cod_pcrc']; ?></td>
                        <td class="text-center"><?php echo $value['pcrc']; ?></td>
                        <?php
                            if ($varRn != null) {
                        ?>
                            <td class="text-center"><?php echo $varRn; ?></td>
                        <?php
                            }else{
                                if ($varExt != null) {
                        ?>
                                    <td class="text-center"><?php echo $varExt; ?></td>
                        <?php
                                }else{
                                    if ($varUsua != null) {
                        ?>
                                        <td class="text-center"><?php echo $varExt; ?></td>
                        <?php
                                    }else{
                                        if ($varOtros != null) {
                        ?>
                                            <td class="text-center"><?php echo $varOtros; ?></td>
                        <?php
                                        }
                                    }
                                }
                            }
                        ?>
                        <td class="text-center">
                            <div class="row">
                                <?php $var3 = [$varIdspeech.'rn' => 'Regla Negocio', $varIdspeech.'ex' => 'Extensión', $varIdspeech.'us' => 'Usuario Red', $varIdspeech.'ot' => 'Otros', $varIdspeech.'na' => 'No usar parametro']; ?>

                                <?= $form->field($model, 'cod_pcrc')->dropDownList($var3, ['prompt' => 'Seleccione...', 'id'=>$txtConteo.'Select']) ?>
                            </div>
                        </td>  
                        <td class="text-center">
                            <?= $form->field($model, 'comentarios')->textInput(['style' => 'width: 150%;', 'id' => $txtConteo.'Text']) ?>
                        </td>
                    </tr>
                <?php
                    }
                ?>
            </table>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<script type="text/javascript">
    function general(){
        var varConteo = "<?php echo $txtConteo; ?>"; 
        var idcliente = "<?php echo $txtServid; ?>";       

        for (var i = 1; i <= varConteo; i++) {
            varc = i;
            var vardash = document.getElementById(varc+"Select").value;
            
            if (vardash != null && vardash != "") {
                var varparam = vardash.substr(-2);
                var varText = document.getElementById(varc+"Text").value;

                if (varparam == "na") {
                    varText = 'na';
                }


                if (varText != null && varText != "") {
                    $.ajax({                    
                        method: "get",
                        url: "modificardashboard",

                        data : {
                            vardash: vardash,
                            varcont : varText,
                        },
                        success : function(response){ 
                                var respuesta = JSON.parse(response);
                                console.log(respuesta);
                        }
                    });
                }
            }
        }

        window.open('../dashboardspeech/categoriasview?txtServicioCategorias='+idcliente,'_self');
    };
</script>