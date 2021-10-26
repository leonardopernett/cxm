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

$txttitulo = "-----";
$txtcontar = count($txtidbloque);
?>

<div class="capaCero" style="display: inline">
    <a id="dlink" style="display:none;"></a>
    <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Exportar a Excel</button>
</div>

<div class="capaUno" style="display: none;">
    <table id="tblData2" class="table table-striped table-bordered tblResDetFreed">
    <caption>Exportar</caption>
        <thead>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Fecha valoracion') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Cliente') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Formulario VOC') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Nombre valorador') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Nombre valorado') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Id Speech') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Fecha/Hora') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Usuario') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Extensión') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Duración') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Dimensión') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', '-----') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Indicador') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Variable') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Atributo de calidad') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Motivos de contacto') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Detalles motivo contacto') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Punto de dolor') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Llamada categorizada') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Procentaje de indicador') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Agente (Detalle de Responsabilidad)') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Marca (Detalle de Responsabilidad)') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Canal (Detalle de Responsabilidad)') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Mapa de interesados 1') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Mapa de interesados 2') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Mapa de interesados 3') ?></th>
            <th scope="col" class="text-center" style="background-color: #4A7EC0; color: #fff"><?= Yii::t('app', 'Detalle cualitativo') ?></th>
        </thead>
        <tbody>
        <?php
            $txtidpcrcspeech = null;
            $txtcod_pcrc = null;
            $txtusua_id = null;
            $txtidvalorado = null;
            $txtfecha = null;

        if ($txtcontar > 1) {
            $txtidpcrcspeech = $txtidbloque[1];
            $txtcod_pcrc = $txtidbloque[2];
            $txtusua_id = $txtidbloque[3];
            $txtidvalorado = $txtidbloque[4];
            $txtfecha = $txtidbloque[5];
        }

        $anulado = 0;
        $querys =  new Query;
                           $querys  ->select(['idformvocbloque1'])->distinct()
                                    ->from('tbl_formvoc_bloque1');
                           $querys  -> where('anulado = '.$anulado.'');
                           $querys  ->orderBy("idformvocbloque1 DESC");

            if ($txtidpcrcspeech != null) { 
               $querys  -> andwhere('idpcrcspeech = '.$txtidpcrcspeech.'');
                }
            if ($txtcod_pcrc != null)  { 
               $querys  -> andwhere('cod_pcrc = '.$txtcod_pcrc.'');
                }
            if ($txtusua_id != null) { 
               $querys  -> andwhere('usua_id = '.$txtusua_id.'');
                }
            if ($txtidvalorado != null)  { 
                $querys  -> andwhere('idvalorado = '.$txtidvalorado.'');
                }
           if ($txtfecha != null)  { 

                $txtFecha = explode(" ", $txtfecha);
                $txtcanti = count($txtFecha);
                
                 if ($txtcanti > 1) {
                    $txtfechaini = $txtFecha[0];
                    $txtfechafin = $txtFecha[2];
                    $querys->andWhere('fechahora BETWEEN "' . $txtfechaini . '" AND "' . $txtfechafin . '"');                    
                 }
            }   
            var_dump($querys);                               
            $command = $querys->createCommand();
            $query = $command->queryAll();

        foreach ($query as $key => $value) {
            $idform = $value['idformvocbloque1'];

                       
            $txtNombreArbol = Yii::$app->db->createCommand("select a.name from tbl_arbols a inner join tbl_formvoc_bloque1 fb1 on a.id = fb1.idpcrccxm where fb1.idformvocbloque1 = $idform and fb1.anulado = 0")->queryScalar();
                        
            $txtNombreValorador = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios u inner join tbl_formvoc_bloque1 fb on u.usua_id = fb.usua_id where fb.idformvocbloque1 = $idform")->queryScalar();
            $txtNombreValorado = Yii::$app->db->createCommand("select name from tbl_evaluados inner join tbl_formvoc_bloque1 on tbl_evaluados.id = tbl_formvoc_bloque1.idvalorado where  tbl_formvoc_bloque1.idformvocbloque1 = $idform")->queryScalar();
            
            $txtTodo = Yii::$app->db->createCommand("select cod_pcrc, pcrc, idspeech, fechahora, usuarioagente, extension, duracions, dimensionform, fechacreacion from tbl_formvoc_bloque1 where idformvocbloque1 = $idform and anulado = 0")->queryAll();
            
            foreach ($txtTodo as $key => $value) {
                $varcodpcrc = $value['cod_pcrc'];
                $varpcrc = $value['pcrc'];
                $txtServicio = $varcodpcrc.' - '.$varpcrc;
                $txtSpeech = $value['idspeech'];
                $txtFechaHor = $value['fechahora'];
                $txtUsers = $value['usuarioagente'];
                $txtextension = $value['extension'];
                $txtDuraciones = $value['duracions'];
                $txtDimensiones = $value['dimensionform'];
                $txtfechavaloracion = $value['fechacreacion'];
            }

            /*$varcodpcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_formvoc_bloque1 where idformvocbloque1 = $idform and anulado = 0")->queryScalar();
            $varpcrc = Yii::$app->db->createCommand("select pcrc from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtServicio = $varcodpcrc.' - '.$varpcrc;
            $txtSpeech = Yii::$app->db->createCommand("select idspeech from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtFechaHor = Yii::$app->db->createCommand("select fechahora from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtUsers = Yii::$app->db->createCommand("select usuarioagente from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            
            $txtextension = Yii::$app->db->createCommand("select extension from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtDuraciones = Yii::$app->db->createCommand("select duracions from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtDimensiones = Yii::$app->db->createCommand("select dimensionform from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            $txtfechavaloracion = Yii::$app->db->createCommand("select fechacreacion from tbl_formvoc_bloque1 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            */

            // Segundo Bloque


            $varIndicador = null;

            $vartodo = Yii::$app->db->createCommand("select indicadorglobal, variable, puntodolor, moticocontacto, motivollamadas, categoria, indicadorvar from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryAll();
            
            foreach ($vartodo as $key => $value) {
                $varIndicador = $value['indicadorglobal'];
                $varVariable = $value['variable'];
                $txtatributos = $value['puntodolor'];
                $varMotivo = $value['moticocontacto'];
                $varDetalle = $value['motivollamadas'];
                $varCategoria = $value['categoria'];
                $txtPorcentaje = $value['indicadorvar'];
            }

            //$varIndicador = Yii::$app->db->createCommand("select indicadorglobal from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if($varIndicador) {
                $varIndicador = $varIndicador;
            } else {
                $varIndicador = 0;
            }
            $txtIndicador = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varIndicador")->queryScalar();

            //$varVariable = Yii::$app->db->createCommand("select variable from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if($varVariable) {
                $varVariable = $varVariable ;
            } else {
                $varVariable = 0;
            }

            $txtVariable = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varVariable")->queryScalar();

            //$txtatributos = Yii::$app->db->createCommand("select puntodolor from tbl_formvoc_bloque2 fb2  where fb2.anulado = 0 and fb2.idformvocbloque1 = $idform")->queryScalar();
            if ($txtatributos == null) {
                $txtatributos = "Sin registros";
            }

            //$varMotivo = Yii::$app->db->createCommand("select moticocontacto from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMotivo != null) {
                $txtMotivos = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varMotivo")->queryScalar();
            }else{
                $txtMotivos = "Sin registro";
            }            

            //$varDetalle = Yii::$app->db->createCommand("select motivollamadas from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varDetalle != null) {
                $txtDetalles = Yii::$app->db->createCommand("select nombre from tbl_speech_categorias where anulado = 0 and idspeechcategoria = $varDetalle")->queryScalar();
            }else{
                $txtDetalles = "Sin registro";
            }

            //$varCategoria = Yii::$app->db->createCommand("select categoria from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
           if ($varCategoria == 1) {
                $txtCategoria = "Si esta categorizada";
            }else{
                $txtCategoria = Yii::$app->db->createCommand("select ajustecategoia from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            }

            $txtPorcentaje = Yii::$app->db->createCommand("select indicadorvar from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();



            $varAgente = Yii::$app->db->createCommand("select agente from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varAgente != null) {
                $txtAgente = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varAgente")->queryScalar();
            }else{
                $txtAgente = "Sin registro";
            }  

            $varMarca = Yii::$app->db->createCommand("select marca from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMarca != null) {
                $txtMarca = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMarca")->queryScalar();
            }else{
                $txtMarca = "Sin registro";
            } 

            $varCanal = Yii::$app->db->createCommand("select canal from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varCanal != null) {
                $txtCanal = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varCanal")->queryScalar();
            }else{
                $txtCanal = "Sin registro";
            }  

            $varMapa1 = Yii::$app->db->createCommand("select mapa1 from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMapa1 != null) {
                $txtMapa1 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa1")->queryScalar();
            }else{
                $txtMapa1 = "Sin registro";
            }

            $varMapa2 = Yii::$app->db->createCommand("select mapa2 from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMapa2 != null) {
                $txtMapa2 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa2")->queryScalar();
            }else{
                $txtMapa2 = "Sin registro";
            }

            $varMapa3 = Yii::$app->db->createCommand("select interesados from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();
            if ($varMapa3 != null) {
                $txtMapa3 = Yii::$app->db->createCommand("select acciones from tbl_formvoc_acciones where idformvocacciones = $varMapa3")->queryScalar();
            }else{
                $txtMapa3 = "Sin registro";
            }

            $txtDetalleCuali = Yii::$app->db->createCommand("select detalle from tbl_formvoc_bloque2 where anulado = 0 and idformvocbloque1 = $idform")->queryScalar();

        ?>
            <tr>
                <td class="text-center"><?php echo $txtfechavaloracion; ?></td>
                <td class="text-center"><?php echo $txtNombreArbol; ?></td>
                <td class="text-center"><?php echo $txtServicio; ?></td>
                <td class="text-center"><?php echo $txtNombreValorador; ?></td>
                <td class="text-center"><?php echo $txtNombreValorado; ?></td>
                <td class="text-center"><?php echo $txtSpeech; ?></td>
                <td class="text-center"><?php echo $txtFechaHor; ?></td>
                <td class="text-center"><?php echo $txtUsers; ?></td>
                <td class="text-center"><?php echo $txtextension; ?></td>
                <td class="text-center"><?php echo $txtDuraciones.' Segundos'; ?></td>
                <td class="text-center"><?php echo $txtDimensiones; ?></td>
                <td class="text-center" style="background-color: #4A7EC0; color: #fff"><?php echo $txttitulo; ?></td>
                <td class="text-center"><?php echo $txtIndicador; ?></td>   
                <td class="text-center"><?php echo $txtVariable; ?></td>    
                <td class="text-center"><?php echo $txtatributos; ?></td>   
                <td class="text-center"><?php echo $txtMotivos; ?></td> 
                <td class="text-center"><?php echo $txtDetalles; ?></td>    
                <td class="text-center"><?php echo $txtatributos; ?></td>                   
                <td class="text-center"><?php echo $txtCategoria; ?></td>   
                <td class="text-center"><?php echo $txtPorcentaje.'%'; ?></td>
                <td class="text-center"><?php echo $txtAgente; ?></td>  
                <td class="text-center"><?php echo $txtMarca; ?></td>   
                <td class="text-center"><?php echo $txtCanal; ?></td>   
                <td class="text-center"><?php echo $txtMapa1; ?></td>   
                <td class="text-center"><?php echo $txtMapa2; ?></td>   
                <td class="text-center"><?php echo $txtMapa3; ?></td>   
                <td class="text-center"><?php echo $txtDetalleCuali; ?></td>
                                              
                
            </tr>
        <?php }
        ?>   
        </tbody>
    </table>
</div>

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
            document.getElementById("dlink").download = "Escucha Focalizada VOC";
            document.getElementById("dlink").traget = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblData2', 'Listado Escucha Focalizada VOC', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);

</script>