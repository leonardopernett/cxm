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
use app\models\Hojavidaroles;

$this->title = 'Gestor Plan de Satisfacción - Subir Archivos';
$this->params['breadcrumbs'][] = $this->title;

    $sesiones =Yii::$app->user->identity->id;

    $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';
    
?>
<!-- Capa Proceso -->
<div id="capaIdProceso" class="capaProceso" style="display: inline;">

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <a id="dlink" style="display:none;"></a>
                <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Exportar Información') ?></button>
            </div>
        </div>        
    </div>
</div>

<!-- Capa Listado -->
<div id="capaIdProceso" class="capaProceso" style="display: none;">

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="tblDataListPlan" class="table table-striped table-bordered tblResDetFreed">
                    <caption><?= Yii::t('app', 'Resultados del Listado Planes') ?></caption>
                    <thead>
                        <tr>
                            <th class="text-center" scope="col" colspan="14" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Konecta - CXM') ?></label></th>
                        </tr>                    
                        <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Proceso') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Activdad') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Dirección') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Responsable') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Rol Responsable') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Estado') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Implementación') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Definición') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Fecha Cierre') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Indicador') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Acciones') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Puntaje Meta') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Puntaje Actual') ?></label></th>
                          <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Puntaje Cierre') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                          foreach ($varListaPlanesSatu as $key => $value) {  
                            $varNombreProcesos = (new \yii\db\Query())
                                            ->select(['tbl_plan_procesos.proceso'])
                                            ->from(['tbl_plan_procesos'])
                                            ->where(['=','anulado',0])
                                            ->andwhere(['=','tbl_plan_procesos.id_procesos',$value['id_proceso']])
                                            ->scalar(); 

                            if ($value['id_actividad'] == 1) {
                                $varNombreActividad = "Área";
                            }else{
                                $varNombreActividad = "Operación";
                            }

                            if ($value['id_dp_clientes'] != "") {
                                $varNombreDirecionString = (new \yii\db\Query())
                                        ->select([
                                          'tbl_usuarios_evalua.clientearea'
                                        ])
                                        ->from(['tbl_plan_generalsatu'])
                                        ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                          'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.id_dp_clientes')
                                        ->where(['=','tbl_plan_generalsatu.anulado',0])
                                        ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$value['id_generalsatu']])
                                        ->scalar();
                            }else{
                                if ($value['id_dp_area'] != "") {
                                    
                                    $varNombreDirecionString = (new \yii\db\Query())
                                                    ->select([
                                                      'tbl_areasapoyo_gptw.nombre'
                                                    ])
                                                    ->from(['tbl_plan_generalsatu'])
                                                    ->join('LEFT OUTER JOIN', 'tbl_areasapoyo_gptw',
                                                      'tbl_areasapoyo_gptw.id_areaapoyo = tbl_plan_generalsatu.id_dp_area')
                                                    ->where(['=','tbl_plan_generalsatu.anulado',0])
                                                    ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$value['id_generalsatu']])
                                                    ->scalar();
                                    
                                }else{
                                    $varDirecionString = 'N/A';
                                }
                            }

                            $varNombreResponsable = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_evalua.nombre_completo'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_evalua',
                                  'tbl_usuarios_evalua.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$value['id_generalsatu']])
                                ->scalar(); 

                            $varNombreRolResponsable = (new \yii\db\Query())
                                ->select([
                                  'tbl_usuarios_jarvis_cliente.posicion'
                                ])
                                ->from(['tbl_plan_generalsatu'])
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios_jarvis_cliente',
                                  'tbl_usuarios_jarvis_cliente.idusuarioevalua = tbl_plan_generalsatu.cc_responsable')
                                ->where(['=','tbl_plan_generalsatu.anulado',0])
                                ->andwhere(['=','tbl_plan_generalsatu.id_generalsatu',$value['id_generalsatu']])
                                ->scalar();

                            if ($value['estado'] == 1) {
                                $varNombreEstado = "Abierto";
                            }else{
                                $varNombreEstado = "Cerrado";
                            }

                            $varNombreIndicador = (new \yii\db\Query())
                                ->select(['nombre'])
                                ->from(['tbl_indicadores_satisfaccion_cliente'])
                                ->where(['=','anulado',0])
                                ->andwhere(['=','id_indicador',$value['indicador']])
                                ->scalar();
                                       
                        ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $varNombreProcesos; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNombreActividad; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNombreDirecionString; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNombreResponsable; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNombreRolResponsable; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNombreEstado; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['fecha_implementacion']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['fecha_definicion']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['fecha_cierre']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $varNombreIndicador; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['acciones']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['puntaje_meta']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['puntaje_actual']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['puntaje_final']; ?></label></td>
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

<hr>
<!-- Capa Btn -->
<div id="capaIdBtn" class="capaBtn" style="display: inline;">
    <div class="row">        
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #C148D0;"></em><?= Yii::t('app', ' Cancelar y Regresar') ?></label>
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
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
            document.getElementById("dlink").download = "Reporte Plan de Satisfaccion";
            document.getElementById("dlink").target = "_blank";
            document.getElementById("dlink").click();

        }
    })();
    function download(){
        $(document).find('tfoot').remove();
        var name = document.getElementById("name");
        tableToExcel('tblDataListPlan', 'Archivo ', name+'.xls')
        //setTimeout("window.location.reload()",0.0000001);

    }
    var btn = document.getElementById("btn");
    btn.addEventListener("click",download);
</script>