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
use app\models\Dashboardcategorias;
use app\models\Dashboardservicios;

$this->title = 'Alertas - Ver Alerta';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol        ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                        'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                      'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $varPeso =  (new \yii\db\Query())
                ->select(['ROUND(AVG(tbl_alertas_tipoencuestas.peso)) AS peso'])
                ->from(['tbl_alertas_tipoencuestas'])
                ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                        'tbl_alertas_tipoencuestas.id_tipoencuestas = tbl_alertas_encuestasalertas.id_tipoencuestas')
                ->where(['=','tbl_alertas_encuestasalertas.id_alerta',$idalerta])
                ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                ->scalar(); 

    $varEncuestas = null;
    $varTipoEncuesta = null;
    if ($varPeso == "1") {
        $varEncuestas = "<img src='../../images/insatisfecho.png' alt='insatisfecho'> style='height: 50px; width: 50px;'";
        $varTipoEncuesta = "Insatisfecho";
    }
                                
    if ($varPeso == "2") {
        $varEncuestas = "<img src='../../images/medioinsatisfecho.png' alt='medioinsatisfecho' style='height: 50px; width: 50px;'>";
        $varTipoEncuesta = "Medio Insatisfecho";
    }
                            
    if ($varPeso == "3") {
        $varEncuestas = "<img src='../../images/neutro.png' alt='neutro' style='height: 50px; width: 50px;'>";
        $varTipoEncuesta = "Neutro";
    }
                            
    if ($varPeso == "4") {
        $varEncuestas = "<img src='../../images/mediosatisfecho.png' alt='mediosatisfecho' style='height: 50px; width: 50px;'>";
        $varTipoEncuesta = "Medio Satisfecho";
    }
                            
    if ($varPeso == "5") {
        $varEncuestas = "<img src='../../images/satisfecho.png' alt='satisafecho' style='height: 50px; width: 50px;'>";
        $varTipoEncuesta = "Satisfecho";
    }

    $varListaComentarios = (new \yii\db\Query())
                            ->select(['tbl_alertas_tipoencuestas.tipoencuestas','tbl_alertas_encuestasalertas.comentarios'])
                            ->from(['tbl_alertas_tipoencuestas'])
                            ->join('LEFT OUTER JOIN', 'tbl_alertas_encuestasalertas',
                                    'tbl_alertas_tipoencuestas.id_tipoencuestas = tbl_alertas_encuestasalertas.id_tipoencuestas')
                            ->where(['=','tbl_alertas_encuestasalertas.id_alerta',$idalerta])
                            ->andwhere(['=','tbl_alertas_encuestasalertas.anulado',0])
                            ->all(); 


?>
<style>
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

  .card2 {
    height: 90px;
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
    background-image: url('../../images/Alertas-Valoración.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >

<?php
if ($varDataListEncuesta != null) {

?>

<!-- Capa Encuestas -->
<div class="capaEncuestas" id="capaIdEncuestas" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Información Encuestas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Dato de la Encuesta') ?></label>

                <div class="row">
                    <div class="col-md-2 text-center">
                        <label style="font-size: 15px;"><?php echo $varEncuestas; ?><?php echo $varTipoEncuesta; ?></label>
                    </div>

                    <div class="col-md-10 left">

                        <?php
                            foreach ($varListaComentarios as $value) {
                            
                        ?>

                            <label style="font-size: 15px;"><label style="font-size: 15px;"> <?= Yii::t('app', '* Encuesta: '.$value['tipoencuestas'].' - Comentario: '.$value['comentarios']) ?>

                        <?php
                            }
                        ?>
                    </div>
                </div> 
            </div>
        </div>
    </div>

</div>

<br>

<?php
}
?>

<!-- Capa Alertas -->
<div class="capaAlerta" id="capaIdAlerta" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Información Alertas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">               

                <table id="myTableInfo" class="table table-hover table-bordered" style="margin-top:12px">
                    <caption><label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Datos de la Alerta') ?></label></caption>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha de Envio') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varFecha_ver; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Programa PCRC') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varName_ver; ?></label></td>         
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipo de Alerta') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varTipoAlerta_ver; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varUsuaNombre; ?></label></td>      
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asunto') ?></label></th>
                        <td><label style="font-size: 12px;"><?php echo $varAsunto_ver; ?></label></td>

                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Destinatarios') ?></label></th>
                        <td ><label style="font-size: 12px;"><?php echo $varRemitentes_ver; ?></label></td>         
                    </tr>
                    <tr>
                        <th scope="col" style="background-color: #b0cdd6;"><label style="font-size: 15px;"><?= Yii::t('app', 'Comentarios') ?></label></th>
                        <td colspan="3"><label style="font-size: 12px;"><?php echo $varComentarios_ver; ?></label></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>

</div>

<hr>

<!-- Capa Alertas -->
<div class="capaAlerta" id="capaIdAlerta" style="display: inline;">

    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Archivo Adjunto') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb"> 
                <?php
                $varConteoArchivo_two = strlen($varArchivo_ver);
                $varConteoUrl_two = substr($varConteoArchivo_two, -3);
                if ($varConteoUrl_two == "png" || $varConteoUrl_two == "jpg" || $varConteoUrl_two == "bmp" || $varConteoUrl_two == "gif") {
                    
                ?>
                    <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Archivo Adjunto Tipo Imagen') ?></label>
                    <br>
                    <img src="<?= Url::to("@web/alertas/".$varArchivo_ver.""); ?>" alt="Card image cap" > 
                <?php
                }else{
                ?>
                    <label style="font-size: 15px;"><em class="fas fa-paper-plane" style="font-size: 20px; color: #FFC72C;"></em><?= Yii::t('app', ' Archivo Adjunto Tipo Documento') ?></label>
                    <br>
                    <a style="font-size: 18px;" rel="stylesheet" type="text/css" href="<?= Url::to("@web/alertas/".$varArchivo_ver.""); ?>" target="_blank"><?= Yii::t('app', ' Descargar Archivo') ?></a>
                <?php
                }
                ?>
            </div>
        </div>
    </div>

</div>

<hr>