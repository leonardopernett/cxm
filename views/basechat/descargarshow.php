<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\helpers\ArrayHelper;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Gestión Satisfacción Chat';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sesiones =Yii::$app->user->identity->id;  
    $varconteo = 0; 

?>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-6">
            <a id="dlink" style="display:none;"></a>
            <button  class="btn btn-info" style="background-color: #4298B4" id="btn">Descargar</button>
        </div>
    </div>
</div>
<div class="capaPP" id="capaP" style="display: none;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                <caption>Gestión</caption>
                    <thead>
                        <tr>
                            <th scope="col" colspan="19" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?php echo  'Listado de gestión'; ?></label></th>
                        </tr>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Numero Ticket') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Creacion') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Respuesta') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Transaccion') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', ' Cliente') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Agente') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipologia') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Tipo Producto') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Sentir Cliente') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '¿Que tan probable es que recomiendas Tigo a tus familiares y amigos?') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '¿Que tan satisfecho estas con la atención recibida?') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '¿Que tan fácil fue resolver tu consulta/solicitud?') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '¿Resolvimos el motivo de tu solicitud?') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', '¿Qué tan satisfecho estas con el conocimiento que demostró el asesor para resolver tu consulta?') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha de calificación') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Zendesk') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Categoria & Motivos') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Solicitud') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Solución') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Observación') ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procedimiento') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            foreach ($valist as $key => $value) {
                                $varidticket = $value['NumeroTicket'];
                                $varbasesatisfaccion = $value['Basesatisfaccion'];
                                $vartransaccion = $value['FechaTransaccion'];
                                $varcliente = $value['Cliente'];
                                $varagente = $value['Agente'];
                                $vartipologia = $value['Tipologia'];
                                $vartipoproducto = $value['TipoProducto'];
                                $varsentircliente = $value['SentirCliente'];
                                $varpregunta1 = $value['Pregunta1'];
                                $varpregunta2 = $value['Pregunta2'];
                                $varpregunta3 = $value['Pregunta3'];
                                $varpregunta4 = $value['Pregunta4'];
                                $varpregunta5 = $value['Pregunta5'];
                                $varcalificacion = $value['FechaCalificacion'];
                                $varzendesk = $value['FechaZendesk'];
                                $varcreacion = $value['FechaCreacion'];
                                $varrespuesta = $value['FechaRespuesta'];

                                $varsolicitud = Yii::$app->db->createCommand("select distinct fsolicitud from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
                                $varsolucion = Yii::$app->db->createCommand("select distinct fsolucion from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
                                $varobservacion = Yii::$app->db->createCommand("select distinct fobservacion from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();
                                $varprocedimiento = Yii::$app->db->createCommand("select distinct fprocedimiento from tbl_basechat_formulario where anulado = 0 and idlista is null and idbaselista is null and ticked_id = $varidticket and basesatisfaccion_id = $varbasesatisfaccion")->queryScalar();

                                $varlistacategorias = Yii::$app->db->createCommand("select concat(bc.nombrecategoria,': ',bm.nombrelista) 'unidos' from tbl_basechat_categorias bc inner join tbl_basechat_motivos bm on bc.idlista = bm.idlista inner join tbl_basechat_formulario bf on bm.idbaselista = bf.idbaselista where  bf.ticked_id = $varidticket    and bf.basesatisfaccion_id = $varbasesatisfaccion")->queryAll();

                                $vararraymotivos = array();
                                foreach ($varlistacategorias as $key => $value) {
                                    array_push($vararraymotivos, $value['unidos']);
                                }
                                $vartextcm = implode(", ", $vararraymotivos);

                                
                                
                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $varidticket; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varcreacion; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varrespuesta; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $vartransaccion; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varcliente; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varagente; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $vartipologia; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $vartipoproducto; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varsentircliente; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varpregunta1; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varpregunta2; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varpregunta3; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varpregunta4; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varpregunta5; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varcalificacion; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varzendesk; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $vartextcm; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varsolicitud; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varsolucion; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varobservacion; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varprocedimiento; ?></label></td>
                            </tr>
                        <?php 
                            }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
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
            document.getElementById("dlink").download = "Listado degestión";
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