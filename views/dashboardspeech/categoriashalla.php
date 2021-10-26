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
    // $txtidcliente = $txtidcliente;
    // var_dump($txtidcliente);

?>

<br>
<br>
<div class="form-group">
<div onclick="regresar();" class="btn btn-primary" style="display:inline; background-color: #7d7d7d;" method='post' id="botones2" >
        Regresar
    </div>   
    &nbsp;&nbsp;    
    <div onclick="general();" class="btn btn-primary" style="display:inline;" method='post' id="botones1" >
        Actualizar hallazgos
    </div>  
</div>
<br>
<br>
<div class="col-sm-12" id="idCapa0" style="display: inline">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
    <caption>Categorias</caption>
        <tr>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Id Categoria') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Codigo pcrc') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Programa') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Categoria Id') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Nombre indicador') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Hallazgo') ?></th>
            <th scope="col" class="text-center"><?= Yii::t('app', 'Mes') ?></th>
        </tr>
        <?php
        //$varListVar = Yii::$app->db->createCommand("select idspeechcategoria, cod_pcrc, programacategoria, idcategoria, nombre, dashboard from tbl_speech_categorias where anulado = 0 and cod_pcrc in ('$txtCodPcrc') and idcategorias= 1")->queryAll(); 
            $varListVar = Yii::$app->db->createCommand("select sc.idspeechcategoria, sc.cod_pcrc, sc.programacategoria, sc.idcategoria, sc.nombre, sc.dashboard, sc.definicion, sh.hallazgo 
                                                        from tbl_speech_categorias sc 
                                                        LEFT JOIN tbl_speech_hallazgos sh ON sc.idspeechcategoria = sh.idspeechcategoria and sh.mes = $txtmes
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
                    <textarea type="text" class="form-control" id="txthallazgo" value="<?php echo $value['hallazgo']; ?>" data-toggle="tooltip" title="Hallazgos"><?php echo $value['hallazgo']; ?></textarea>                                                      
                </td>
                <td>
                    <select id="txtmessel" class ='form-control' onchange="cambiomes();">
                        <option value="" disabled selected>Seleccione mes</option>
                        <?php
                       if($txtmes==1){
                        echo '<option value="1" selected>Enero</option>';
                       }else{
                        echo '<option value="1" >Enero</option>';  
                       }
                       if($txtmes==2){
                        echo '<option value="2" selected>Febrero</option>';
                       }else{
                        echo '<option value="2">Febrero</option>';  
                       }
                       if($txtmes==3){
                        echo '<option value="3" selected>Marzo</option>';
                       }else{
                        echo '<option value="3">Marzo</option>';  
                       }
                       if($txtmes==4){
                        echo '<option value="4" selected>Abril</option>';
                       }else{
                        echo '<option value="4">Abril</option>';  
                       }
                       if($txtmes==5){
                        echo '<option value="5" selected>Mayo</option>';
                       }else{
                        echo '<option value="5">Mayo</option>';  
                       }
                       if($txtmes==6){
                        echo '<option value="6" selected>Junio</option>';
                       }else{
                        echo '<option value="6">Junio</option>';  
                       }
                       if($txtmes==7){
                        echo '<option value="7" selected>Julio</option>';
                       }else{
                        echo '<option value="7">Julio</option>';  
                       }
                       if($txtmes==8){
                        echo '<option value="8" selected>Agosto</option>';
                       }else{
                        echo '<option value="8">Agosto</option>';  
                       }
                       if($txtmes==9){
                        echo '<option value="9" selected>Septiembre</option>';
                       }else{
                        echo '<option value="9">Septiembre</option>';  
                       }
                       if($txtmes==10){
                        echo '<option value="10" selected>Octubre</option>';
                       }else{
                        echo '<option value="10">Octubre</option>';  
                       }
                       if($txtmes==11){
                        echo '<option value="11" selected>Noviembre</option>';
                       }else{
                        echo '<option value="11">Noviembre</option>';  
                       }
                       if($txtmes==12){
                        echo ' <option value="12" selected>Diciembre</option>';
                       }else{
                        echo ' <option value="12">Diciembre</option>';  
                       }
                     ?>   
                    </select>
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
        var varhallazgo = document.querySelectorAll("#txthallazgo");        
        var vartxtmes = document.getElementById('txtmessel').value;        
        var idcliente = "<?php echo $txtidcliente; ?>";

        for (var x = 0; x < varidspeech.length; x++) {
         vartxtidspeech = varidspeech[x].value;
         vartxthallazgo = varhallazgo[x].value;

         if (vartxthallazgo != "") {
            $.ajax({                    
                method: "post",
                url: "ingresarhallazgo",
                data : {
                    varidspeechcat: vartxtidspeech,
                    varhallazgocat : vartxthallazgo,
                    varmes : vartxtmes,
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
    function cambiomes(){
        var idcliente = "<?php echo $txtidcliente; ?>";
        var varmes = document.getElementById('txtmessel').value;
        var varcodpcrc = "<?php echo $txtCodPcrc; ?>";
       
        window.location.href='categoriashalla?txtServicioCategorias='+varcodpcrc+'&txtmes='+varmes;
    };
    
</script>