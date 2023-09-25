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
use yii\db\Query;
use app\models\SpeechCategorias; 

$this->title = 'Análisis Focalizados - Escuchar +';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Análisis Focalizados - Escuchar +';

    $template = '<div class="col-md-12">'
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

    

    $paramsBuscarTipologia = [':varFechaInicios' => $varfechainireal.' 00:00:00', ':varFechaFins' => $varfechafinreal.' 23:59:59'];
    $txttipologias = Yii::$app->db->createCommand("
        SELECT b.tipologia FROM tbl_base_satisfaccion b
        WHERE 
            b.fecha_satu BETWEEN :varFechaInicios AND :varFechaFins
                AND b.tipologia IS NOT NULL 
        GROUP BY b.tipologia
        ")->bindValues($paramsBuscarTipologia)->queryAll();

    $listDatatipologias = ArrayHelper::map($txttipologias, 'tipologia', 'tipologia');

    $varIdClientes = (new \yii\db\Query())
                        ->select(['id_dp_clientes'])
                        ->from(['tbl_speech_parametrizar'])
                        ->where(['=','cod_pcrc',$paramsvarcodigopcrc])
                        ->andwhere(['=','anulado',0])
                        ->groupby(['id_dp_clientes'])
                        ->scalar();   

    $txtListLideres = (new \yii\db\Query())
                        ->select(['tbl_usuarios.usua_id', 'tbl_usuarios.usua_nombre', 'tbl_equipos.id'])
                        ->from(['tbl_distribucion_asesores'])   
    
                        ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                'tbl_usuarios.usua_identificacion = tbl_distribucion_asesores.cedulalider') 
    
                        ->join('LEFT OUTER JOIN', 'tbl_equipos',
                                'tbl_equipos.usua_id = tbl_usuarios.usua_id') 
    
                        ->where(['=','tbl_distribucion_asesores.id_dp_clientes',$varIdClientes])
                        ->andwhere(['not like','tbl_equipos.name','o usar'])
                        ->groupby(['tbl_distribucion_asesores.cedulalider'])
                        ->orderBy(['tbl_usuarios.usua_nombre' => SORT_ASC])
                        ->all(); 
    
    $listDatalideres = ArrayHelper::map($txtListLideres, 'id', 'usua_nombre');
    
    $varFechaActualInicio = date('Y-m-d');

?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css" >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card {
            height: 200px;
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
            box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -webkit-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            -moz-box-shadow: 0 2px 4px 0 rgba(0, 0, 0, 0.2), 0 4px 10px 0 rgba(0, 0, 0, 0.19);
            border-radius: 5px;    
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/GestionBT.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- datatable -->
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
<div class="capaInfo" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;">Ficha T&eacute;cnica - <?php echo $datanamearbol; ?> </label>
            </div>
        </div>
    </div>
    <br>
    <div class="row">

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em> Pcrc Seleccionado:</label>
                <label style="font-size: 15px; text-align: center;"><?php echo $dataprograma; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #559FFF;"></em> Rango de Fechas:</label>
                <label style="text-align: center;"><?php echo $datafechas; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-hashtag" style="font-size: 20px; color: #559FFF;"></em> Interacciones General:</label>
                <label style="text-align: center;"><?php echo $varcantllamadas; ?></label>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-hashtag" style="font-size: 20px; color: #559FFF;"></em> Interacciones Encontradas:</label>
                <label style=" text-align: center;"><?php echo count($dataProvider); ?></label>
            </div>
        </div>

    </div>
</div>

<hr>

<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<div class="capaSeleccion" style="display: inline;">   
    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;">Selecci&oacute;n de Filtros</label>
            </div>
        </div>
    </div>
    <br> 
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                
                <div class="row">
                    <div class="col-md-3">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Seleccionar Indicadores') ?></label>
                        <?=  $form->field($model, 'idcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere("idcategorias = 1")->andwhere("cod_pcrc in ('$paramsvarcodigopcrc')")->orderBy(['nombre'=> SORT_ASC])->all(), 'idspeechcategoria', 'nombre'),
                                            [
                                                'id' => 'idtxtcategoria',
                                                'prompt'=>'Seleccionar Indiciador...',
                                                'onchange' => '
                                                    $.get(
                                                        "' . Url::toRoute('listarvariablesx') . '", 
                                                        {id: $(this).val()}, 
                                                        function(res){
                                                            $("#requester").html(res);
                                                        }
                                                    );
                                                    dataContiene();
                                                ',

                                            ]
                                )->label(''); 
                        ?>
                    </div>

                    <div class="col-md-3">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Seleccionar Variables') ?></label>
                        <?= $form->field($model,'nombreCategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                            [],
                                            [
                                                
                                                'prompt' => 'Seleccionar Variable...',
                                                'id' => 'requester'
                                            ]
                                        )->label('');
                        ?>
                    </div>

                    <div class="col-md-3">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Seleccionar Motivos de contacto') ?></label>
                        <?=  $form->field($model, 'extension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere("idcategorias = 3")->andwhere("cod_pcrc in ('$paramsvarcodigopcrc')")->orderBy(['nombre'=> SORT_ASC])->all(), 'idcategoria', 'nombre'),
                                            [
                                                'prompt'=>'Seleccionar Motivo...',
                                            ]
                                )->label(''); 
                        ?>
                    </div>

                    <div class="col-md-3">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Contiene/No Contiene') ?></label>
                        <div class="capaContiene" id="CapaIdContiene" style="display: none;">
                            <?php $var = ['1' => 'Contiene', '2' => 'No contiene']; ?>                        
                            <?= $form->field($model, 'login_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var, ['prompt' => 'Seleccionar Contenedor...', 'id'=>"idcontact"])->label('') ?> 
                        </div>
                        
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Seleccionar Lider') ?></label>
                        <?=  $form->field($model, 'servicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listDatalideres,
                                            [
                                                'prompt'=>'Seleccionar Lider...',
                                                'onchange' => '
                                                    $.get(
                                                        "' . Url::toRoute('listarlideresx') . '", 
                                                        {id: $(this).val()}, 
                                                        function(res){
                                                            $("#requester2").html(res);
                                                        }
                                                    );
                                                ',

                                            ]
                                )->label(''); 
                        ?>
                    </div>

                    <div class="col-md-6">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Seleccionar Asesor') ?></label>
                        <?= $form->field($model,'fechallamada', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                            [],
                                            [
                                                'prompt' => 'Seleccionar Asesor...',
                                                'id' => 'requester2'
                                            ]
                                        )->label('');
                        ?>
                    </div>
                </div>

                <div class="row">
                    <div class="col-md-6">
                        <label><em class="fas fa-check" style="font-size: 20px; color: #827DF9;"></em> <?= Yii::t('app', 'Seleccionar Tipologias') ?></label>
                        <?=
                            $form->field($model, 'idredbox', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listDatatipologias, ['prompt' => Yii::t('app', 'Seleccionar Tipología...')]);
                        ?>
                    </div>
                </div>

            </div>
        </div>
    </div>    
</div>
<br>
<br>
<div class="capaBtn" style="display: inline;">
    <div class="row">
        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-search" style="font-size: 20px; color: #FFC72C;"></em> Buscar Interacción:</label>
                <?= Html::submitButton(Yii::t('app', 'Buscar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'dataVerifica();',
                                'title' => 'Buscar']) 
                ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-random" style="font-size: 20px; color: #FFC72C;"></em> Buscar Aleatorio:</label>
                <?= Html::a('Buscar',  ['llamadafocalizada', 'varprograma'=>$dataprograma, 'varcodigopcrc'=>$paramsvarcodigopcrc, 'varidcategoria'=>$dataidgeneral, 'varextension'=>$paramsvarextension, 'varfechasinicio'=>$paramsvarfechasinicio, 'varfechasfin'=>$paramsvarfechasfin, 'vartcantllamadas'=>$varcantllamadas, 'varfechainireal'=>$varfechainireal, 'varfechafinreal'=>$varfechafinreal,'varcodigos'=>$varcodigos, 'varaleatorios' => 1], ['class' => 'btn btn-success',
                            'style' => 'background-color: #337ab7',
                            'data-toggle' => 'tooltip',
                            'onclick' => 'varAleatorio();',
                            'title' => 'Buscar llamadas']) 
                ?>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-globe" style="font-size: 20px; color: #FFC72C;"></em> Buscar General:</label>
                <?= Html::a('Buscar',  ['llamadafocalizada', 'varprograma'=>$dataprograma, 'varcodigopcrc'=>$paramsvarcodigopcrc, 'varidcategoria'=>$dataidgeneral, 'varextension'=>$paramsvarextension, 'varfechasinicio'=>$paramsvarfechasinicio, 'varfechasfin'=>$paramsvarfechasfin, 'vartcantllamadas'=>$varcantllamadas, 'varfechainireal'=>$varfechainireal, 'varfechafinreal'=>$varfechafinreal,'varcodigos'=>$varcodigos, 'varaleatorios' => 0], ['class' => 'btn btn-success',
                            'style' => 'background-color: #337ab7',
                            'data-toggle' => 'tooltip',
                            'onclick' => 'varAleatorio();',
                            'title' => 'Buscar llamadas']) 
                ?>
            </div>
        </div>

<?php if ($sessiones == '2953') { ?>
        <div class="col-md-3">
            <div class="card1 mb">
                <label><em class="fas fa-download" style="font-size: 20px; color: #FFC72C;"></em> Descargar Gestión:</label>
                <?= Html::button('Descargar', ['value' => url::to(['enviarfocalizada','varprogramad'=>$dataprograma, 'varcodigopcrcd'=>$paramsvarcodigopcrc, 'varidcategoriad'=>$dataidgeneral, 'varextensiond'=>$paramsvarextension, 'varfechasiniciod'=>$paramsvarfechasinicio, 'varfechasfind'=>$paramsvarfechasfin]), 'class' => 'btn btn-success', 'id'=>'modalButton1',
                                'data-toggle' => 'tooltip',
                                'style' => 'background-color: #337ab7',
                                'title' => 'Desargar']) 
                ?>  
                
                <?php
                    Modal::begin([
                        'header' => '<h4></h4>',
                        'id' => 'modal1',
                                       //'size' => 'modal-lg',
                    ]);

                    echo "<div id='modalContent1'></div>";
                                                                                
                    Modal::end(); 
                ?>
            </div>
        </div>
<?php } ?>
    </div>
</div>
<?php ActiveForm::end(); ?>

<hr>

<div class="capaListado" style="display: inline;">
    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;">Resultados</label>
            </div>
        </div>
    </div>
    <br>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <table id="myTable" class="table table-hover table-bordered" style="margin-top:20px" >
                    <caption><?= Yii::t('app', 'Resultados') ?></caption>
                    <thead>
                      <tr>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Fecha') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Asesor') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Redbox') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Tipologia Encuesta') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Estado') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorador') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Agente') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Canal') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Marca') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Info.') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Reprod.') ?></label></th>
                          <th scope="col" class="text-center" style="background-color: #F5F3F3;"><label style="font-size: 15px;"><?= Yii::t('app', 'Valorar') ?></label></th>
                      </tr>
                    </thead>
                    <tbody>
                        <?php
                            foreach ($dataProvider as $key => $value) {
                                $txtconnids = $value['connid'];
                                $txtcallid = $value['callId'];
                                $txtfechasreal = $value['fechareal'];
                                $txtloginid = $value['login_id'];
                                $txtidredbox = $value['idredbox'];
                                $txtextension = $value['extensiones'];
                                $txtidgrabadora = $value['idgrabadora'];
                                $txtidcategoria = $value['idcategoria'];
                                $txtServicio = $value['servicio'];
                                $varsindata = '--';                                
                                $varvalorador = $varsindata;

                                if (!is_numeric($txtidredbox)) {
                                    $vartxtidredbox = 'NA';
                                }else{
                                    $vartxtidredbox = $txtidredbox;
                                }

                                $paramsBuscarConnid = [':varConnid' => $txtconnids];                                

                                if ($txtconnids != "") {
                                    $varbaseconnid = Yii::$app->db->createCommand('
                                        SELECT b.tipologia FROM tbl_base_satisfaccion b
                                            WHERE 
                                                b.connid IN (:varConnid)')->bindValues($paramsBuscarConnid)->queryScalar();
                                    $txtvarConnid = $txtconnids;

                                    $varbaseid = Yii::$app->db->createCommand('
                                        SELECT b.id FROM tbl_base_satisfaccion b
                                            WHERE 
                                                b.connid IN (:varConnid)')->bindValues($paramsBuscarConnid)->queryScalar();
                                }else{
                                    $varbaseconnid = $varsindata;
                                    $txtvarConnid = $varsindata;
                                    $varbaseid = $varsindata;
                                }
                                
                                $paramsBuscarValoracion = [':varCallids' => $txtcallid];
                                $varFormularioId = Yii::$app->db->createCommand('
                                    SELECT sm.formulario_id FROM tbl_speech_mixta sm
                                        WHERE 
                                            sm.callid IN (:varCallids)')->bindValues($paramsBuscarValoracion)->queryScalar();                                

                                if ($varFormularioId != null) {
                                    $paramsBuscarValorador = [':varIdFormulario' => $varFormularioId];
                                    $varvalorador = Yii::$app->db->createCommand('
                                    SELECT u.usua_nombre FROM tbl_usuarios u 
                                        INNER JOIN tbl_ejecucionformularios ef ON 
                                            u.usua_id = ef.usua_id
                                        WHERE 
                                            ef.id IN (:varIdFormulario)
                                        GROUP BY u.usua_id')->bindValues($paramsBuscarValorador)->queryScalar();

                                    $varvaloraestado = "Cerrado";
                                }else{
                                    $paramsBuscarValoradorTmp = [':varUsuaid' => $sessiones, ':varComentario' => $txtcallid.'; '.$txtfechasreal, ':varFechaInicio'=> $varFechaActualInicio.' 00:00:00', ':varFechaFin' => $varFechaActualInicio.' 23:59:59'];
                                    $varTmpValorador = Yii::$app->db->createCommand('
                                        SELECT u.usua_nombre FROM tbl_usuarios u 
                                            INNER JOIN tbl_tmpejecucionformularios ef ON 
                                                u.usua_id = ef.usua_id
                                            WHERE 
                                                ef.usua_id IN (:varUsuaid)
                                                    AND ef.created BETWEEN :varFechaInicio AND :varFechaFin
                                                        AND ef.dsfuente_encuesta IN (:varComentario)
                                            GROUP BY u.usua_id')->bindValues($paramsBuscarValoradorTmp)->queryScalar();

                                    if ($varTmpValorador != null) {
                                        $varvalorador = $varTmpValorador;
                                        $varvaloraestado = "En Proceso";
                                    }else{
                                        $varvaloraestado = "Abierto";
                                    }
                                }

                                $paramsBuscarAgente = [':varIdCallA' => $txtcallid, ':varBolsitaCXA' => $txtServicio, ':varAgente' => 1];
                                $varDataAgente = Yii::$app->db->createCommand('
                                    SELECT sc.nombre AS nombre FROM tbl_speech_categorias sc
                                        INNER JOIN tbl_dashboardspeechcalls d ON
                                            sc.idcategoria = d.idcategoria 
                                        WHERE 
                                            d.callId = :varIdCallA
                                                AND sc.programacategoria IN (:varBolsitaCXA)
                                                    AND sc.responsable = :varAgente
                                        GROUP BY sc.idcategoria')->bindValues($paramsBuscarAgente)->queryAll();

                                if (count($varDataAgente) == 0) {
                                    $varDataAgente = $varsindata;
                                }else{
                                    $arrayVariablesAgente = array();
                                    foreach ($varDataAgente as $key => $value) {
                                        array_push($arrayVariablesAgente, $value['nombre']);
                                    }

                                    $varDataAgente = implode(" - ", $arrayVariablesAgente);
                                }

                                $paramsBuscarCanal = [':varIdCallC' => $txtcallid, ':varBolsitaCXC' => $txtServicio, ':varCanal' => 2];
                                $varDataCanal = Yii::$app->db->createCommand('
                                    SELECT sc.nombre AS nombre FROM tbl_speech_categorias sc
                                        INNER JOIN tbl_dashboardspeechcalls d ON
                                            sc.idcategoria = d.idcategoria 
                                        WHERE 
                                            d.callId = :varIdCallC
                                                AND sc.programacategoria IN (:varBolsitaCXC)
                                                    AND sc.responsable = :varCanal
                                        GROUP BY sc.idcategoria')->bindValues($paramsBuscarCanal)->queryAll();

                                if (count($varDataCanal) == 0) {
                                    $varDataCanal = $varsindata;
                                }else{
                                    $arrayVariablesCanal = array();
                                    foreach ($varDataCanal as $key => $value) {
                                        array_push($arrayVariablesCanal, $value['nombre']);
                                    }

                                    $varDataCanal = implode(" - ", $arrayVariablesCanal);
                                }

                                $paramsBuscarMarca = [':varIdCallM' => $txtcallid, ':varBolsitaCXM' => $txtServicio, ':varMarca' => 3];
                                $varDataMarca = Yii::$app->db->createCommand('
                                    SELECT sc.nombre AS nombre FROM tbl_speech_categorias sc
                                        INNER JOIN tbl_dashboardspeechcalls d ON
                                            sc.idcategoria = d.idcategoria 
                                        WHERE 
                                            d.callId = :varIdCallM
                                                AND sc.programacategoria IN (:varBolsitaCXM)
                                                    AND sc.responsable = :varMarca
                                        GROUP BY sc.idcategoria')->bindValues($paramsBuscarMarca)->queryAll();


                                if (count($varDataMarca) == 0) {
                                    $varDataMarca = $varsindata;
                                }else{
                                    $arrayVariablesMarca = array();
                                    foreach ($varDataMarca as $key => $value) {
                                        array_push($arrayVariablesMarca, $value['nombre']);
                                    }

                                    $varDataMarca = implode(" - ", $arrayVariablesMarca);
                                }


                                $paramsEvaluado = [':varLogin' => $txtloginid];
                                $varEvaluado_id_Jarvis = null;
                                if (is_numeric($txtloginid)) {
                                    $varEvaluado_id_Jarvis = Yii::$app->dbjarvis->createCommand('
                                        SELECT du.documento FROM  dp_usuarios_red du 
                                          WHERE 
                                            du.documento = :varLogin ')->bindValues($paramsEvaluado)->queryScalar();
                            
                                    
                                }else{
                                    $varEvaluado_id_Jarvis = Yii::$app->dbjarvis->createCommand('
                                    SELECT du.documento FROM  dp_usuarios_red du 
                                      WHERE 
                                        du.usuario_red = :varLogin ')->bindValues($paramsEvaluado)->queryScalar();

                                    if ($varEvaluado_id_Jarvis == "") {
                                        $varEvaluado_id_Jarvis = Yii::$app->dbjarvis->createCommand('
                                        SELECT du.documento FROM  dp_usuarios_actualizacion du 
                                        WHERE 
                                            du.usuario = :varLogin ')->bindValues($paramsEvaluado)->queryScalar();
                                    }                                    
                                }

                                if ($varEvaluado_id_Jarvis != "") {
                                    
                                    $varEvaluado_id = (new \yii\db\Query())
                                                ->select(['tbl_evaluados.id'])
                                                ->from(['tbl_evaluados'])
                                                ->where(['=','tbl_evaluados.identificacion',$varEvaluado_id_Jarvis])
                                                ->scalar();
                                }else{
                                    $varEvaluado_id = null;
                                }
                                                                 
                                
                                if ($varEvaluado_id == "" && $varIdClientes == "289") {
                                    $varEvaluado_id = "0";
                                }

                                if ($varEvaluado_id == "" && $varIdClientes == "303") {
                                    $varEvaluado_id = "0";
                                }

                                if ($varEvaluado_id == "" && $varIdClientes == "276") {
                                    $varEvaluado_id = "0";
                                }


                        ?>
                            <tr>
                                <td><label style="font-size: 12px;"><?php echo  $txtfechasreal; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $txtloginid; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $vartxtidredbox; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varbaseconnid; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varvaloraestado; ?></label></td>
                                <td><label style="font-size: 12px;"><?php echo  $varvalorador; ?></label></td>
                                <?php if ($varDataAgente == "--") { ?>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $varDataAgente; ?></label></td>
                                <?php }else{ ?>
                                    <td class="text-center">
                                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-trigger="hover" title="<?php echo $varDataAgente; ?>">
                                            <em class="fas fa-info-circle" style="font-size: 18px; color: #ef7c05;pointer-events: none;"></em>

                                        </span> 
                                    </td>
                                <?php } ?>
                                <?php if ($varDataCanal == "--") { ?>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $varDataCanal; ?></label></td>
                                <?php }else{ ?>
                                    <td class="text-center">
                                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-trigger="hover" title="<?php echo $varDataCanal; ?>">
                                            <em class="fas fa-info-circle" style="font-size: 18px; color: #ef7c05;pointer-events: none;"></em>

                                        </span> 
                                    </td>
                                <?php } ?>
                                <?php if ($varDataMarca == "--") { ?>
                                    <td class="text-center"><label style="font-size: 12px;"><?php echo  $varDataMarca; ?></label></td>
                                <?php }else{ ?>
                                    <td class="text-center">
                                        <span class="d-inline-block" tabindex="0" data-toggle="tooltip" data-trigger="hover" title="<?php echo $varDataMarca; ?>">
                                            <em class="fas fa-info-circle" style="font-size: 18px; color: #ef7c05;pointer-events: none;"></em>

                                        </span> 
                                    </td>
                                <?php } ?>
                                <td class="text-center">
                                    <?= 
                                        Html::a(Yii::t('app', '<i id="idimage" class="fas fa-calculator" style="font-size: 17px; color: #C178G9; display: inline;"></i>'),
                                                                'javascript:void(0)',
                                                                [
                                                                    'title' => Yii::t('app', 'Resultados VOC'),
                                                                    'onclick' => "
                                                                        generarcarga();    
                                                                        $.ajax({
                                                                            type     :'get',
                                                                            cache    : false,
                                                                            url  : '" . Url::to(['viewrtas',
                                                                            'idspeechcalls' => $txtcallid, 'varcodpcrc'=>$paramsvarcodigopcrc, 'idvarfechareal'=>$txtfechasreal,'idvarloginid'=>$txtloginid,'idvarconnid'=>$txtvarConnid,'idvarextensiones'=>$txtextension]) . "',
                                                                            success  : function(response) {
                                                                                $('#ajax_result').html(response);
                                                                            }
                                                                        });
                                                                    return false;",
                                                                ]);
                                    ?>

                                </td>
                                <td class="text-center">
                                    <?php
                                        if ($varIdClientes == "255") {
                                            $txtidredbox = "001";
                                            $txtidgrabadora = "002";
                                        }

                                        if ($varIdClientes == "310") {
                                            $txtidredbox = "001";
                                            $txtidgrabadora = "002";
                                        }

                                        if ($varIdClientes == "262") {
                                            $txtidredbox = "001";
                                            $txtidgrabadora = "002";
                                        }
                                        

                                        $varLinkUrl = null;

                                        $varUlComprobacion = (new \yii\db\Query())
                                                            ->select([
                                                                'tbl_comdata_llamadaurl.idredbox',
                                                                'tbl_comdata_llamadaurl.url_llamada'
                                                            ])
                                                            ->from(['tbl_comdata_llamadaurl'])
                                                            ->where(['=','tbl_comdata_llamadaurl.anulado',0])
                                                            ->andwhere(['=','tbl_comdata_llamadaurl.cod_pcrc',$paramsvarcodigopcrc])
                                                            ->andwhere(['=','tbl_comdata_llamadaurl.id_dp_clientes',$varIdClientes])
                                                            ->andwhere(['=','tbl_comdata_llamadaurl.idredbox',$txtidredbox])
                                                            ->andwhere(['=','tbl_comdata_llamadaurl.fechareal',$txtfechasreal])
                                                            ->andwhere(['=','tbl_comdata_llamadaurl.bolsita',$txtServicio])
                                                            ->all();

                                        if (count($varUlComprobacion) != 0) {
                                            $varIdRedbox_url = null;
                                            $varIdUrl_url = null;
                                            foreach ($varUlComprobacion as $value) {
                                                $varIdRedbox_url = $value['idredbox'];
                                                $varIdUrl_url = $value['url_llamada'];
                                            }

                                            if ($varIdUrl_url == 'nan' || $varIdUrl_url == '<NA>') {
                                                $varLinkUrl = $varIdRedbox_url;
                                            }else{
                                                $varLinkUrl = $varIdUrl_url;
                                            }
                                        }
                                        

                                        if ($varLinkUrl != null) {               
                                    ?>
                                    
                                        <a title="Interacciones" href="<?php echo $varLinkUrl; ?>" target="_blank"><em id="idimage" class="fas fa-play-circle" style="font-size: 17px; color: #4CB0EC; display: inline;"></em></a>
                                    
                                    <?php
                                        }else{
                                    ?>

                                        <?php
                                            if ($txtidredbox != null && $txtidredbox != "NA" && $txtidgrabadora != null && $txtidgrabadora != "NA") {
                                        
                                        ?>

                                            <?php
                                                if (!is_numeric($txtidredbox)) {                                                    
                                            ?>
                                                <a title="Interacciones" href="<?php echo $txtidredbox; ?>" target="_blank"><em id="idimage" class="fas fa-play-circle" style="font-size: 17px; color: #4CB0EC; display: inline;"></em></a>
                                            <?php
                                                }else{
                                            ?>
                                                <?= 
                                                    Html::a(Yii::t('app', '<i id="idimage" class="fas fa-play-circle" style="font-size: 17px; color: #E61313; display: inline;"></i>'),
                                                            'javascript:void(0)',
                                                            [
                                                                'title' => Yii::t('app', 'Escuchar + VOC'),
                                                                'onclick' => "
                                                                    generarcarga2();                        
                                                                    $.ajax({
                                                                        type     :'get',
                                                                        cache    : false,
                                                                        url  : '" . Url::to(['viewcalls',
                                                                        'idlogin' => $txtloginid, 'idredbox' => $txtidredbox, 'idgrabadora' => $txtidgrabadora, 'idconnid' => $txtconnids, 'idcallids' => $txtcallid, 'varfechareal' => $txtfechasreal,'varcategolias' => $txtidcategoria]) . "',
                                                                        success  : function(response) {
                                                                            $('#ajax_result').html(response);
                                                                        }
                                                                    });
                                                                return false;",
                                                            ]);
                                                ?>
                                            <?php
                                                }
                                            ?>                                            
                                        
                                        <?php
                                            }else{
                                        ?>
                                            
                                            <?= 
                                                Html::a(Yii::t('app', '<i id="idimage" class="fas fa-play-circle" style="font-size: 17px; color: #C7C7C7; display: inline;"></i>'),
                                                        'javascript:void(0)',
                                                        [
                                                            'title' => Yii::t('app', 'Escucha VOC'),
                                                            'onclick' => "                       
                                                                $.ajax({
                                                                    type     :'get',
                                                                    cache    : false,
                                                                    url  : '" . Url::to(['viewna']) . "',
                                                                    success  : function(response) {
                                                                        $('#ajax_result').html(response);
                                                                    }
                                                                });
                                                            return false;",
                                                        ]);
                                            ?>

                                        <?php
                                            }
                                        ?>
                                    <?php
                                        }
                                    ?>

                                </td>
                                <td class="text-center">

                                    <?php if ($varEvaluado_id != null) { ?>

                                        <?php 
                                            if ($varFormularioId != null) {
                                                $paramsBaseFormulario = [':varBaseFormulario'=>$varFormularioId];

                                                $varExistBase = Yii::$app->db->createCommand('
                                                SELECT ef.basesatisfaccion_id FROM tbl_ejecucionformularios ef
                                                    WHERE 
                                                        ef.id IN (:varBaseFormulario)')->bindValues($paramsBaseFormulario)->queryScalar();

                                                if ($varExistBase != null) {
                                        ?>
                                                <?=
                                                    Html::a('<em id="idimage" class="fas fa-search" style="font-size: 17px; color: #4dbdff; display: inline;"></em>', Url::to(['basesatisfaccion/showformulariogestion'
                                                            , 'basesatisfaccion_id' => $varExistBase, 'banderaescalado'=> 0, 'aleatorio' => false ,'preview' => 1, 'fill_values' => true,'view'=>"reportes/historicoformularios"]), [
                                                            'title' => Yii::t('yii', 'ver formulario'),
                                                            'target' => "_blank"
                                                    ]);
                                                ?>
                                        <?php
                                                }else{
                                        ?>
                                                <?=
                                                    Html::a('<em id="idimage" class="fas fa-search" style="font-size: 17px; color: #4c6ef5; display: inline;"></em>'
                                                                , Url::to(['formularios/showformulariodiligenciadohistorico'
                                                                , 'tmp_id' => $varFormularioId,'view'=>"reportes/historicoformularios"]), [
                                                                'title' => Yii::t('yii', 'ver formulario'),
                                                                'target' => "_blank"
                                                        ]);
                                                ?>
                                        <?php
                                                }

                                            }else{
                                                
                                                if ($varbaseid != null && $varbaseid != "--") {
                                        ?>
                                                    <?=
                                                        Html::a('<em id="idimage" class="fas fa-edit" style="font-size: 17px; color: #4dbdff; display: inline;"></em>'
                                                            , Url::to(['basesatisfaccion/showformulariogestion',
                                                            'basesatisfaccion_id' => $varbaseid, 'preview' => 5, 'fill_values' => false, 'banderaescalado' => false, 'aleatorio'=> 0]), ['title' => Yii::t('yii', 'Gestionar'),'target' => "_blank"]);
                                                    ?>
                                        <?php
                                                }else{
                                        ?>
                                                    <?=
                                                        Html::a(
                                                        '<em id="idimage" class="fas fa-edit" style="font-size: 17px; color: #4c6ef5; display: inline;"></em>', 
                                                         Url::to(['valoraspeech', 'varloginid'=>$txtloginid, 'varinteraccion'=>$txtcallid, 'varfechasreals'=>$txtfechasreal, 'varcodpcrc'=>$paramsvarcodigopcrc, 'varservisioname' => $datanamearbol]), ['title' => Yii::t('yii', 'Valoración VOC'), 'data-pjax' => 0, 'target' => "_blank"]
                                                                    );
                                                    ?>
                                        <?php
                                                }

                                            }
                                        ?>

                                    <?php }else{ ?>
                                        <?= 
                                            Html::a(Yii::t('app', '<i id="idimage" class="fas fa-info-circle" style="font-size: 17px; color: #C7C7C7; display: inline;"></i>'),
                                                    'javascript:void(0)',
                                                    [
                                                        'title' => Yii::t('app', 'Escucha VOC'),
                                                        'onclick' => "                       
                                                            $.ajax({
                                                                type     :'get',
                                                                cache    : false,
                                                                url  : '" . Url::to(['viewna']) . "',
                                                                success  : function(response) {
                                                                    $('#ajax_result').html(response);
                                                                }
                                                            });
                                                        return false;",
                                                    ]);
                                        ?>
                                    <?php } ?>
                                </td>
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
<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">
    $(document).ready( function () {
        $('#myTable').DataTable({
            responsive: true,
            fixedColumns: true,
            select: false,
            "language": {
                "lengthMenu": "Cantidad de Datos a Mostrar _MENU_ ",
                "zeroRecords": "No se encontraron datos ",
                "info": "Mostrando p&aacute;gina _PAGE_ a _PAGES_ de _MAX_ registros",
                "infoEmpty": "No hay datos aun",
                "infoFiltered": "(Filtrado un _MAX_ total)",
                "search": "Buscar:",
                "paginate": {
                    "first":      "Primero",
                    "last":       "Ultimo",
                    "next":       "Siguiente",
                    "previous":   "Anterior"
                }
            } 
        });
    });

  function dataContiene(){
    var varidtxtcategoria = document.getElementById("idtxtcategoria").value;
    var varcapaContiene = document.getElementById("CapaIdContiene");

    if (varidtxtcategoria != "") {
        varcapaContiene.style.display = 'inline';
    }else{
        varcapaContiene.style.display = 'none';
    }

  };

  function dataVerifica(){
    var varIndicador = document.getElementById('idtxtcategoria').value;
    var varVariables = document.getElementById('requester').value;
    var varMotivos = document.getElementById('dashboardspeechcalls-extension').value;
    var varLider = document.getElementById('dashboardspeechcalls-servicio').value;
    var varAsesor = document.getElementById('requester2').value;
    var varTipologias = document.getElementById('dashboardspeechcalls-idredbox').value;

    if (varIndicador == "" && varVariables == "" && varMotivos == "" && varLider == "" && varAsesor == "" && varTipologias == "") {
        event.preventDefault();
        swal.fire("¡¡¡ Advertencia !!!","Busqueda no realizada, es necesario tener aunque sea un solo filtro seleccionado","warning");
        return;
    }

  };

  function generarcarga(){
    Swal.fire({
        position: 'top-end',
        icon: 'success',
        title: 'Realizando procesamiento para resultados...',
        showConfirmButton: false,
        timer: 400
    })
  };

  function generarcarga2(){
        Swal.fire({
          position: 'top-end',
          icon: 'success',
          title: 'Buscando llamada seleccionada...',
          showConfirmButton: false,
          timer: 6000
        })
    };
</script>