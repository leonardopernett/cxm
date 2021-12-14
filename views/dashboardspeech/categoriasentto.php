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

$this->title = 'Configuracion de categorias';
$this->params['breadcrumbs'][] = $this->title;

$sessiones = Yii::$app->user->identity->id;

$this->title = 'Configuración de Categorias Calidad Entto';

$varlistBolsita = Yii::$app->db->createCommand("select bolsitacategoria 'bolsita', count(idcategoria) 'cantidad' from tbl_speech_categoriascalidad where anulado = 0 group by bolsitacategoria")->queryAll();

$varquery = Yii::$app->db->createCommand("select bolsitacategoria 'bolsita' from tbl_speech_categoriascalidad where anulado = 0 group by bolsitacategoria")->queryAll();

$listData = ArrayHelper::map($varquery, 'bolsita', 'bolsita');

if ($txtservicio != null) {
    $varcantidad = Yii::$app->db->createCommand("select count(idcategoria) from tbl_dashboardcategoriascalls where anulado = 0 and servicio in ('$txtservicio') and fechallamada between '$txtfinicio 05:00:00' and '$txtffin 05:00:00' and idcategoria = 1105")->queryScalar();
    $varListProcesamientoIda = Yii::$app->db->createCommand("select * from tbl_categorias_ida where anulado = 0 and programa_pcrc in ('$txtservicio') and fechacreacion between '$txtfinicio' and '$txtffin'")->queryAll();
}
?>
<br>
    <div class="page-header" >
        <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
    </div> 
<br>
<div class="capacero" id="capaceroid" style="display: none;">
    Procesando datos, por favor espere, gracias...
</div>
<div class="capaUno" id="capaunoid" style="display: inline; text-align: center">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
        ]
        ]); ?>
    <div class="row"> 
        <div class="col-md-12">
            
                <?= $form->field($model, 'programacategoria')->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'idbolsitas'])->label('') ?> 


                <?php $var = ['01' => 'Enero', '02' => 'Febrero', '03' => 'Marzo', '04' => 'Abril', '05' => 'Mayo', '06' => 'Junio', '07' => 'Julio', '08' => 'Agosto', '09' => 'Septiembre', '10' => 'Octubre', '11' => 'Noviembre', '12' => 'Diciembre']; ?>
            
                <?= $form->field($model, "nombre")->dropDownList($var, ['prompt' => 'Seleccione un mes...', 'id'=>"id_argumentos"]) ?> 
             
        </div>
        <br>
    	<div class="col-md-12" class="text-center">
     		<?= Html::button('Importar categoria', ['value' => url::to('importarentto'), 'class' => 'btn btn-success', 'id'=>'modalButton1',  'data-toggle' => 'tooltip', 'style' => 'height: 31px', 'title' => 'Importar categoria', 'style' => 'background-color: #337ab7']) 
            ?> 

            <?php
                  Modal::begin([
                        'header' => '<h4>Importar Archivo Excel </h4>',
                        'id' => 'modal1',
                        //'size' => 'modal-lg',
                      ]);

                  echo "<div id='modalContent1'></div>";
                                            
                  Modal::end(); 
            ?> 


            <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                            'style' => 'background-color: #707372',
                                            'data-toggle' => 'tooltip',
                                            'title' => 'Regresar']) 
            ?>

	<?php if($sessiones == '2953' || $sessiones == '3205') { ?>
            <div onclick="llamadasbtn();" class="btn btn-success"  style=" background-color: #b73333;" method='post' id="botones2" >
                Llamadas desde Speech
            </div> 
	<?php } ?>

            <?= Html::submitButton(Yii::t('app', 'Cantidad llamadas'),
                                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Cantidad llamadas',
                                    'onclick' => 'btncantidadll();',
                                    'id'=>'ButtonSearch']) 
            ?>

        </div>
    </div>
    <?php $form->end() ?>
</div>
<hr>
<div class="capaDos">
    <div class="row">
    	<div class="col-md-12">
            <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
            <caption>Categorias</caption>
                <thead>
                    <tr>
                        <th scope="col" class="text-center" colspan="2" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Categorias cargadas') ?></label></th>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Bolsita') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad categorias') ?></label></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($varlistBolsita as $key => $value) {                            
                    ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['bolsita']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['cantidad']; ?></label></td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
    	</div>
    </div>
</div>
<hr>
<?php if ($txtservicio != null) { ?>
<div class="capaTres">
    <div class="row">
        <div class="col-md-12">
            <table id="tblData2" class="table table-striped table-bordered tblResDetFreed">
            <caption>Llamadas</caption>
                <thead>
                    <tr>
                        <th scope="col" class="text-center" colspan="3" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Llamadas cargadas') ?></label></th>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Bolsita') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Cantidad llamadas') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ultima fecha cargada') ?></label></th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><label style="font-size: 12px;"><?php echo  $txtservicio; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $varcantidad; ?></label></td>
                        <td><label style="font-size: 12px;"><?php echo  $txtfinicio; ?></label></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
<hr>

<?php if ($txtservicio == "CX_Entto") { ?>

<div class="capaCuatro">
    <div class="row">
        <div class="col-md-12">
            <table id="tblData3" class="table table-striped table-bordered tblResDetFreed">
            <caption>Procesamiento</caption>
                <thead>
                    <tr>
                        <th scope="col" class="text-center" colspan="17" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento IDA CX_Entto') ?></label></th>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Login Id') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Corte de llamada') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Demora en contestar') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Demora > 10 seg') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Demora 5 - 10 seg') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Reiteratividad de los silencios') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Seguridad') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Cédula') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Correo electrónico') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Nombre completo') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Teléfonos') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Silencios') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Silencios >90 seg') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Silencios 60 - 90 seg') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Vocabulario inadecuado') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'IDA') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'CantidadLlamadas') ?></label></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($varListProcesamientoIda as $key => $value) {                            
                    ?>
                        <tr>
                            <td><label style="font-size: 10px;"><?php echo  $value['usuario_red']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_corte_llamada']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_demora_contestar']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['demora_mas_10_seg']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['demora_5_10_seg']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_reiteratividad_silencios']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['Seguridad']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['cedula']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['correo_electrónico']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['nombre_completo']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['telefonos']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_silencios']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['silencios_mas_90_seg']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['silencios_60_90_seg']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_vocabulario_inadecuado']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['ida']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['valora_automatica']; ?></label></td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<hr>
<?php }else{
    if ($txtservicio == "CX_Directv") { ?>
<div class="capaCinco">
    <div class="row">
        <div class="col-md-12">
            <table id="tblData4" class="table table-striped table-bordered tblResDetFreed">
            <caption>Procesamiento</caption>
                <thead>
                    <tr>
                        <th scope="col" class="text-center" colspan="16" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Procesamiento IDA CX_Directv') ?></label></th>
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Login Id') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Corte de llamada') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Demora en Contestar') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Reiteratividad de los silencios') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Seguridad') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Cédula') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Correo electrónico') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Nombre completo') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Teléfonos') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Tiempos de Espera') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'TE o SIL De 60 a 90 Segundos') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'TE o SIL De 30 a 60 Segundos') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'TE o SIL De 90 a 120 Segundos') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'Evita Vocabulario inadecuado') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'IDA') ?></label></th>
                        <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 10px;"><?= Yii::t('app', 'CantidadLlamadas') ?></label></th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        foreach ($varListProcesamientoIda as $key => $value) {                            
                    ?>
                        <tr>
                            <td><label style="font-size: 10px;"><?php echo  $value['usuario_red']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_corte_llamada_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_demora_contestar_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_reiteratividad_silencios_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['seguridad_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['cedula_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['correo_electrónico_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['nombre_completo_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['telefonos_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_tiempos_espera_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['te_sil_60_90_seg_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['te_sil_30_60_seg_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['te_sil_90_120_seg_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['evita_vocabulario_inadecuado_d']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['ida']; ?></label></td>
                            <td><label style="font-size: 10px;"><?php echo  $value['valora_automatica']; ?></label></td>
                        </tr>
                    <?php
                        }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<hr>
<?php } } ?>
        
<?php } ?>
<script type="text/javascript">
    function llamadasbtn(){
        var varbolsitas = document.getElementById("idbolsitas").value;
        var varllamadas = document.getElementById("id_argumentos").value;
        var varcapaunoid = document.getElementById("capaunoid");
        var varcapaceroid = document.getElementById("capaceroid");

        varcapaunoid.style.display = "none";
        varcapaceroid.style.display = "inline";

        $.ajax({
            method: "get",
            url: "cantidadentto",
            data: {
                txtLlamadas : varllamadas,
                txtBolsitas : varbolsitas,
            },
            success : function(response){
                var numRta2 =   JSON.parse(response);    
                console.log(numRta2);
                window.open('../dashboardspeech/categoriasentto','_self');

            }
        });
    };

    function btncantidadll(){
        var varidbolsitas = document.getElementById("idbolsitas").value;
        var varid_argumentos = document.getElementById("id_argumentos").value;

        if (varidbolsitas == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar servicio","warning");
            return;
        }else{
            if (varid_argumentos == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe de seleccionar mes.","warning");
                return;
            }
        }

    };
</script>