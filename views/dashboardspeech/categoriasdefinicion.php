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

$this->title = 'Visualización de Categorias -- QA & Speech --';

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
    if(isset($_GET['txtmes'])) {
        $txtmes = $_GET['txtmes'];
    }
    $txtConteo = 0;
  

?>
<br>
<br>
<div class="form-group">
<div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #7d7d7d;" method='post' id="botones2" >
        Regresar
    </div>   
    &nbsp;&nbsp;    
    <div onclick="general();" class="btn btn-primary" style="display:inline;" method='post' id="botones1" >
        Actualizar definicion
    </div>  
</div>
<br>
<div class="col-sm-12" id="idCapa0" style="display: inline">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
    <caption>Categoria</caption>
        <tr>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Id Categoria') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Codigo pcrc') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Programa') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Categoria Id') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Nombre indicador') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Definicion') ?></th>
        </tr>
        <?php
        
            $varListVar = Yii::$app->db->createCommand("select sc.idspeechcategoria, sc.cod_pcrc, sc.programacategoria, sc.idcategoria, sc.nombre, sc.dashboard, sc.definicion 
                                                        from tbl_speech_categorias sc 
                                                        where anulado = 0 and cod_pcrc in ('$txtCodPcrc') and idcategorias= 1 order by sc.idspeechcategoria")->queryAll(); 
            
            foreach ($varListVar as $key => $value) {
                $varDash = $value['dashboard'];
                $varIdC = $value['idspeechcategoria'];
        ?>
            <tr>
                <td class="text-center" id="idspeech"><?php echo $value['idspeechcategoria']; ?></td>
                <td class="text-center"><?php echo $value['cod_pcrc']; ?></td>
                <td class="text-center"><?php echo $value['programacategoria']; ?></td>
                <td class="text-center"><?php echo $value['idcategoria']; ?></td>
                <td class="text-center"><?php echo $value['nombre']; ?>
                <input type="text" class="form-control" style="display: none" id="txtidspeech" value="<?php echo $value['idspeechcategoria']; ?>" >
                </td>
                <td>
                    <textarea type="text" class="form-control" id="txtdefinicion" value="<?php echo $value['definicion']; ?>" data-toggle="tooltip" title="Definicion"><?php echo $value['definicion']; ?></textarea>
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
        var varidspeech = document.querySelectorAll("#txtidspeech");
        var vardefinicion = document.querySelectorAll("#txtdefinicion");
                
        var idcliente = "<?php echo $txtidcliente; ?>";

        for (var x = 0; x < varidspeech.length; x++) {
         vartxtidspeech = varidspeech[x].value;
         vartxtdefinicion = vardefinicion[x].value;

         if (vartxtdefinicion != "") {
            $.ajax({                    
                method: "post",
                url: "ingresardefinicion",
                data : {
                    varidspeechcat: vartxtidspeech,
                    vardefinicioncat : vartxtdefinicion,
                    },
                    success : function(response){ 
                            var respuesta = JSON.parse(response);
                            console.log(respuesta);
                    }
                });
            }
        }

        window.location.href='categoriasview?txtServicioCategorias='+idcliente;      
    };

    function regresar(){
        var idcliente = "<?php echo $txtidcliente; ?>";
        

        window.location.href='categoriasview?txtServicioCategorias='+idcliente;

    };
    
</script>