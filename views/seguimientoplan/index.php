<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;

$this->title = 'Seguimiento Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';
    $sesiones =Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol    ->select(['tbl_roles.role_id'])
            ->from('tbl_roles')
            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                    'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
            ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();


    $month = date('m');
    $year = date('Y');
    $day = date("d", mktime(0,0,0, $month+1, 0, $year));
     
    $varfechainicio = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
    $varfechafin = date('Y-m-d', mktime(0,0,0, $month, $day, $year));



    $sessiones = Yii::$app->user->identity->id;
    $sumatoria1 = null;
    $varCantSpeech = null;
    $variablesId1 = $dataProvider->getModels();
    $varsumarray = array();
    foreach ($variablesId1 as $key => $value) {
        $textoss1 = $value['evaluados_id'];

        $varMetas = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $textoss1 and fechacreacion between '$varfechainicio' and '$varfechafin'")->queryScalar();

        $datosNum1 = $varMetas;
        $sumatoria1 = $sumatoria1 + $datosNum1;

        $varCantSpeech = Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_focalizada where anulado = 0 and fechacreacion between '$varfechainicio' and '$varfechafin' and evaluados_id = $textoss1")->queryScalar();

        $varescaladas = Yii::$app->get('dbslave')->createCommand("select sum(cantidadjustificar) from tbl_plan_escalamientos where anulado = 0 and tecnicolider = $textoss1 and fechacreacion between '$varfechainicio' and '$varfechafin' and Estado =  1")->queryScalar();

        array_push($varsumarray, $varescaladas);
    }
    $varscalaas = array_sum($varsumarray);

    $sumaMetas = ($sumatoria1 + $varCantSpeech) - $varscalaas;

        $sumatoria2 = null;
        $variablesId2 = $dataProvider->getModels();
        foreach ($variablesId2 as $key => $value) {
                $textoss2 = $value['evaluados_id'];

                $querys =  new Query;
                $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                                ->from('tbl_ejecucionformularios')
                                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                                        'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                                ->where("tbl_ejecucionformularios.created between '$varfechainicio 00:00:00' and '$varfechafin 23:59:59'")
                                ->andwhere('tbl_usuarios.usua_id = '.$textoss2.'');
                                
                $command = $querys->createCommand();
                $queryss = $command->queryAll();   

                $varRealizadas = count($queryss);

                $datosNum = $varRealizadas;
                $sumatoria2 = $sumatoria2 + $datosNum;
        }

        $sumaRealizadas = $sumatoria2;


        $sumaCumplimiento = 0;
        $sumatoria2 = null;
        $variablesId2 = $dataProvider->getModels();
        foreach ($variablesId2 as $key => $value) {
                $textoss2 = $value['evaluados_id'];
                $txtcorte = $value['tipo_corte'];

                $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();

                $fechafinC = Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte' and anulado = 0")->queryScalar();               

	$txtidtc = $value['idtc'];
	$txtlistidtcs = Yii::$app->db->createCommand("SELECT idtcs FROM tbl_tipos_cortes WHERE idtc = $txtidtc")->queryAll();
	$vararrayidtcs = Array();
        foreach ($txtlistidtcs as $key => $value){
		 array_push($vararrayidtcs, $value['idtcs']);
	}
	
	
	$txtlistacortes = implode("', '", $vararrayidtcs);
	
	$varsumagestion = Yii::$app->db->createCommand("SELECT SUM(estado) FROM tbl_plan_escalamientos WHERE tecnicolider = $textoss2 AND estado = 1 AND idtcs in ('$txtlistacortes')")->queryScalar();
	if ($varsumagestion == null){
		$varsumagestion = 0;
	}


        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_usuarios.usua_nombre'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'tbl_ejecucionformularios.usua_id = tbl_usuarios.usua_id')
                    ->where("tbl_ejecucionformularios.created between '$fechainiC 00:00:00' and '$fechafinC 23:59:59'")
                    ->andwhere('tbl_usuarios.usua_id = '.$textoss2.'');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();    

                $varRealizadas = count($queryss);

                $datosNum = $varRealizadas;
                $sumatoria2 = $sumatoria2 + $varRealizadas;
        }

        $sumaRealizadas = $sumatoria2;

        if($sumaMetas != 0 || $sumaRealizadas != 0){
            $sumaCumplimiento = round(($sumaRealizadas / $sumaMetas) * 100);
        }else{
            $sumaCumplimiento = 0;
        }

?>
<style>
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


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>


<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<script>
    $(document).ready(function(){
        $.fn.snow();
    });
</script>
<br><br>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-user-circle" style="font-size: 20px; color: #2CA5FF;"></em> Buscar técnico/lider: </label>
                <?php $form = ActiveForm::begin([
                    'options' => ["id" => "buscarMasivos"],
                    'layout' => 'horizontal',
                    'fieldConfig' => [
                        'inputOptions' => ['autocomplete' => 'off']
                      ]
                    ]); ?> 
                <div class="row">
                    <div class="col-sm-12">
                        <?=
                            $form->field($model, 'evaluados_id')->label(Yii::t('app',''))
                            ->widget(Select2::classname(), [
                                //'data' => array_merge(["" => ""], $data),
                                'language' => 'es',
                                'options' => ['placeholder' => Yii::t('app', 'Seleccionar el técnico/lider...')],
                                'pluginOptions' => [
                                    'allowClear' => true,
                                    'minimumInputLength' => 4,
                                    'ajax' => [
                                        'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                        'dataType' => 'json',
                                        'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                        'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                    ],
                                    'initSelection' => new JsExpression('function (element, callback) {
                                                var id=$(element).val();
                                                if (id !== "") {
                                                    $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                                        dataType: "json",
                                                        type: "post"
                                                    }).done(function(data) { callback(data.results[0]);});
                                                }
                                            }')
                                ]
                                    ] 
                            );
                        ?>
                    </div>
                </div>
                <div class="text-center">
                    <?= Html::submitButton(Yii::t('app', 'Buscar técnico/lider'),
                        ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                            'data-toggle' => 'tooltip',
                            'title' => 'Buscar Valorado']) 
                    ?>
                    <?= Html::a('Buscar Todo el equipo',  ['index'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Equipo global']) 
                    ?>
                </div>
                <?php $form->end() ?> 
            </div>
        </div>
        <div class="col-md-8">
            <div class="card1 mb">
                <label><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                <div class="row">
                <?php if($roles == "270" || $roles == "309" || $roles == "274" || $roles == "276") {?>
                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 15px; color: #FFC72C;"></em> Gráfica del equipo: </label>                    
                            <?= 
                                Html::button('Aceptar', ['value' => url::to(['graficasequipo']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Ver Grafica', 'style' => 'background-color: #337ab7'                                        
                                    ])
                            ?>
                            <?php
                                 Modal::begin([
                                    'header' => '<h4></h4>',
                                    'id' => 'modal1',
                                    'size' => 'modal-lg',
                                ]);

                                echo "<div id='modalContent1'></div>";
                                                        
                                Modal::end(); 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-history" style="font-size: 15px; color: #FFC72C;"></em> Verificar histórico: </label>
                            <?= Html::a('Aceptar',  ['viewhistorico'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #337ab7',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Ver Historico']) 
                            ?> 
                        </div>
                    </div>
                    <?php if ($roles == "270" || $roles == "309" || $roles == "274" || $roles == "276") { ?>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #FFC72C;"></em> Escalamientos: </label>
                                <?= Html::a('Aceptar',  ['gestionarescalamientos'], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #337ab7',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Ver Escalamientos']) 
                                ?> 
                            </div>
                        </div>
                    <?php } ?>                    
                <?php }else{ ?>
                    <div class="col-md-12">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-hand-paper" style="font-size: 15px; color: #FFC72C;"></em> Estas acciones solo son vistas al igual que operativas por coordinadores CX, coordinadores OP y administradores del sistema... </label>                                
                        </div>
                    </div>
                <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div id="capaCuatro" style="display: inline">   
    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-hashtag" style="font-size: 20px; color: #C148D0;"></em> Total meta del equipo:</label>
                <label  style="font-size: 50px; text-align: center;"><?php echo $sumaMetas; ?></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-hashtag" style="font-size: 20px; color: #C148D0;"></em> Total realizadas del equipo:</label>
                <label  style="font-size: 50px; text-align: center;"><?php echo $sumaRealizadas; ?></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-hashtag" style="font-size: 20px; color: #C148D0;"></em> Total % de cumplimiento del equipo:</label>
                <label  style="font-size: 50px; text-align: center;"><?php echo $sumaCumplimiento.'%'; ?></label>
            </div>
        </div>
    </div> 
</div>
<br>
<div class="CapaUno" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-list" style="font-size: 20px; color: #2CA53F;"></em> Listado del equipo: </label>
                    <?= GridView::widget([
                            'dataProvider' => $dataProvider,        
                            //'filterModel' => $searchModel,
                            'columns' => [
                                [
                                    'attribute' => 'Rol',
                                    'value' => function($data){
                                        return $data->getRol($data->evaluados_id);
                                    }
                                ],
                                [
                                    'attribute' => 'Tecnico/Lider',
                                    'value' => 'usuarios.usua_nombre',
                                ],
                                [
                                    'attribute' => 'Meta',
                                    'value' => function($data){
                                        return $data->getMetas($data->id, $data->evaluados_id);
                                    }
                                ],
                                [
                                    'attribute' => 'Realizadas',
                                    'value' => function($data) {
                                        return $data->getRealizadas($data->evaluados_id);
                                    }
                                ],
                                [
                                    'attribute' => 'Novedades aprobadas',
                                    'value' => function($data){
                                        return $data->getEscaladas($data->evaluados_id);
                                    }
                                ],
                                [
                                    'attribute' => '% de Cumplimiento',
                                    'value' => function($data){
                                        return $data->getCumplimiento($data->evaluados_id);
                                    }
                                ],
                                [
                                    'attribute' => 'Tipo de Corte',
                                    'value' => 'tipo_corte',
                                ],
                                [
                                    'class' => 'yii\grid\ActionColumn',
                                    'headerOptions' => ['style' => 'color:#337ab7'],
                                    'template' => '{view}{stats}',
                                    'buttons' => 
                                    [
                                        'view' => function ($url, $model) {                        
                                            return Html::a('<i class="fas fa-search" style="font-size: 18px; color: #002855;"></i> ',  ['view', 'id' => $model->id, 'evaluados_id' => $model->evaluados_id], [
                                                'class' => '',
                                                'title' => 'Ver',
                                                'data' => [
                                                    'method' => 'post',
                                                ],
                                            ]);  
                                        }
                                    ]
                                  
                                ],
                            ]
                        ]);
                    ?>
            </div>
        </div>
    </div>
</div>
<hr>

<?php if ($roles == "274" || $roles == "276" || $roles == "272" || $roles == "273") { ?>
<div class="capaextra" style="display: inline;">
    <div class="row">
        <div class="col-md-4">
            <div class="card2 mb">
                <label style="font-size: 16px;"><em class="fas fa-exclamation-triangle" style="font-size: 15px; color: #ff2c2c;"></em> Fechas de vencimiento para escalar y/o aprobar las justificaciones de cada corte: </label> 
                <br> 
                <?php
                    if ($roles == "274" || $roles == "276") {
                        $varlistcortes = Yii::$app->db->createCommand("select cp.tipo_corte, tc.cortetcs, tc.fechainiciotcs, tc.fechafintcs from tbl_tipos_cortes tc  inner join tbl_control_procesos cp on tc.idtc = cp.idtc  where cp.anulado = 0 and cp.responsable = $sessiones and cp.fechacreacion between '$varfechainicio' and '$varfechafin' group by tc.cortetcs")->queryAll();
                        foreach ($varlistcortes as $key => $value) {
                            $varWeek = date("l", strtotime($value['fechafintcs']));
                            $varcort = $value['cortetcs'];
                            
                            if ($varcort != 'Corte 4') {
                                if ($varWeek == "Friday") {
                                    $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 4 days"));
                                }else{
                                    if ($varWeek == "Saturday") {
                                        $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 3 days"));
                                    }else{
                                        $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 2 days"));
                                    }
                                }
                            }else{
                                $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']));
                            }                                                        
                ?>
                        <label style="font-size: 14px;"><?php echo '* Aprobar escalamientos y/o novedades '.$value['cortetcs'].': </label><label style="font-size: 14px; color: #FE3F10;"> '.$txtfechas; ?></label> 
                <?php
                        }
                    }else{
                        if ($roles == "272" || $roles == "273") {
                            $varlistcortes = Yii::$app->db->createCommand("select cp.tipo_corte, tc.cortetcs, tc.fechainiciotcs, tc.fechafintcs from tbl_tipos_cortes tc  inner join tbl_control_procesos cp on tc.idtc = cp.idtc  where cp.anulado = 0 and cp.evaluados_id = $sessiones and cp.fechacreacion between '$varfechainicio' and '$varfechafin' group by tc.cortetcs")->queryAll();
                            foreach ($varlistcortes as $key => $value) {
                                $varWeek = date("l", strtotime($value['fechafintcs']));
                                $varcort = $value['cortetcs'];
                            
                                if ($varcort != 'Corte 4') {
                                    if ($varWeek == "Friday") {
                                        $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 4 days"));
                                    }else{
                                        if ($varWeek == "Saturday") {
                                            $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 3 days"));
                                        }else{
                                            $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 2 days"));
                                        }
                                    }
                                }else{
                                    $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']));
                                }                                
                ?> 
                            <label style="font-size: 14px;"><?php echo '* Aprobar escalamientos y/o novedades '.$value['cortetcs'].': </label><label style="font-size: 14px; color: #FE3F10;"> '.$txtfechas; ?></label> 
                <?php
                            }
                        }else{
                ?>
                            <label style="font-size: 14px;">Visual solo para coordinadores, técnicos y lideres por tema de los cortes.</label> 
                <?php
                        }
                    }
                ?>
            </div>
        </div>
        <div class="col-md-4">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 15px; color: #ff2c2c;"></em> Plazos para escalar novedades del plan a cierre de mes: </label>

                        <?php if ($roles == "274" || $roles == "276") { 
                            $vardatenext = Yii::$app->db->createCommand("select distinct DATE_ADD(t.fechafintc, INTERVAL 1 DAY) 'fechaplazo' from tbl_tipocortes t inner join tbl_control_procesos cp on t.idtc = cp.idtc where cp.anulado = 0 and cp.responsable = $sessiones and cp.fechacreacion between '$varfechainicio' and '$varfechafin'")->queryScalar();
                        ?>
                            <br>
                             <label style="font-size: 14px;"><?php echo '</label><label style="font-size: 14px; color: #FE3F10;"> '.date("d-m-Y", strtotime($vardatenext)).' (Solicitudes para realizar ajustes al plan de valoración por OTRS)'; ?></label> 
                        <?php } ?>

                        <?php if ($roles == "272" || $roles == "273") { 
                            $vardatenext = Yii::$app->db->createCommand("select distinct DATE_ADD(t.fechafintc, INTERVAL 1 DAY) 'fechaplazo' from tbl_tipocortes t inner join tbl_control_procesos cp on t.idtc = cp.idtc where cp.anulado = 0 and cp.evaluados_id = $sessiones and cp.fechacreacion between '$varfechainicio' and '$varfechafin'")->queryScalar();
                        ?>
                            <br>
                             <label style="font-size: 14px;"><?php echo '</label><label style="font-size: 14px; color: #FE3F10;"> '.date("d-m-Y", strtotime($vardatenext)).' (Solicitudes para realizar ajustes al plan de valoración por OTRS)' ?></label> 
                        <?php } ?>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 15px; color: #ff2c2c;"></em> Plazo para reportar novedades del informe a cierre de mes: </label>
                        <?php if ($roles == "274" || $roles == "276") { 

                            $vardateextendido = Yii::$app->db->createCommand("select distinct DATE_ADD(t.fechafintc, INTERVAL 6 DAY) 'fechaplazo' from tbl_tipocortes t inner join tbl_control_procesos cp on t.idtc = cp.idtc where cp.anulado = 0 and cp.responsable = $sessiones and cp.fechacreacion between '$varfechainicio' and '$varfechafin'")->queryScalar();

                            $varWeekext = date("l", strtotime($vardateextendido));
                            
                            if ($varWeekext == "Friday") {
                                $txtfechasext = date("d-m-Y", strtotime($vardateextendido."+ 4 days"));
                            }else{
                                if ($varWeekext == "Saturday") {
                                    $txtfechasext = date("d-m-Y", strtotime($vardateextendido."+ 3 days"));
                                }else{
                                    $txtfechasext = date("d-m-Y", strtotime($vardateextendido."+ 2 days"));
                                }
                            }
                        ?>
                            
                             <label style="font-size: 14px;"><?php echo '* Plazo para reportar novedades en los resultados a cierre de mes teniendo en cuenta el informe final: </label><label style="font-size: 14px; color: #FE3F10;"> '.$txtfechasext; ?></label> 
                        <?php } ?>

                        <?php if ($roles == "272" || $roles == "273") { 
                            $vardateextendido = Yii::$app->db->createCommand("select distinct DATE_ADD(t.fechafintc, INTERVAL 6 DAY) 'fechaplazo' from tbl_tipocortes t inner join tbl_control_procesos cp on t.idtc = cp.idtc where cp.anulado = 0 and cp.evaluados_id = $sessiones and cp.fechacreacion between '$varfechainicio' and '$varfechafin'")->queryScalar();

                            $varWeekext = date("l", strtotime($vardateextendido));
                            
                            if ($varWeekext == "Friday") {
                                $txtfechasext = date("d-m-Y", strtotime($vardateextendido."+ 4 days"));
                            }else{
                                if ($varWeekext == "Saturday") {
                                    $txtfechasext = date("d-m-Y", strtotime($vardateextendido."+ 3 days"));
                                }else{
                                    $txtfechasext = date("d-m-Y", strtotime($vardateextendido."+ 2 days"));
                                }
                            }
                        ?>
                            
                             <label style="font-size: 14px;"><?php echo '* Plazo para reportar novedades en los resultados a cierre de mes teniendo en cuenta el informe final: </label><label style="font-size: 14px; color: #FE3F10;"> '.$txtfechasext; ?></label>
                        <?php } ?>
                    </div>
                </div>
            </div>            
        </div>
    </div>
</div>
<hr>
<?php }?>

<?php if($roles == "270") { ?>
<div class="capaextraAdmin" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 16px;"><em class="fas fa-exclamation-triangle" style="font-size: 15px; color: #ff2c2c;"></em> Fechas de vencimiento para escalar y/o aprobar las justificaciones de cada corte: </label>
                <div class="row">
                    <?php 
                        $varlistadocortes = Yii::$app->db->createCommand("select t.idtc, t.tipocortetc, t.cantdiastc, g.nomgrupocorte from tbl_grupo_cortes g  inner join tbl_tipocortes t on g.idgrupocorte = t.idgrupocorte where t.anulado = 0")->queryAll();

                        foreach ($varlistadocortes as $key => $value) {
                            $vartxtidtc = $value['idtc'];
                    ?>
                        <div class="col-md-3">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-calendar" style="font-size: 15px; color: #ff2c2c;"> </em><?php echo ' '.$value['tipocortetc'].': '?></label> 
                                <?php
                                    $varlistcortesAdmin = Yii::$app->db->createCommand("select tc.cortetcs, tc.fechainiciotcs, tc.fechafintcs from tbl_tipos_cortes tc  where            tc.idtc = $vartxtidtc group by tc.cortetcs")->queryAll();

                                    foreach ($varlistcortesAdmin as $key => $value) {
                                        $varWeek = date("l", strtotime($value['fechafintcs']));
                                        $varcort = $value['cortetcs'];

                                        if ($varcort != 'Corte 4') {
                                            if ($varWeek == "Friday") {
                                                $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 4 days"));
                                            }else{
                                                if ($varWeek == "Saturday") {
                                                    $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 3 days"));
                                                }else{
                                                    $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']."+ 2 days"));
                                                }
                                            }
                                        }else{
                                             $txtfechas = date("d-m-Y", strtotime($value['fechafintcs']));
                                        }
                                ?>
                                    <label style="font-size: 14px;"><?php echo '* Aprobar escalamientos y/o novedades '.$value['cortetcs'].': </label><label style="font-size: 14px; color: #FE3F10;"> '.$txtfechas; ?></label>
                                <?php } ?>
                            </div>
                        </div>
                    <?php 
                        }
                    ?>
                </div>
            </div>
        </div>
    </div>            
</div>
<hr>
<div class="CapaAdmin" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-lock" style="font-size: 20px; color: #ff2c2c;"></em> Administrativo: </label>
                <div class="row">
                    <div class="col-md-4">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-user" style="font-size: 15px; color: #ff2c2c;"></em> Asignar permisos: </label>                            
                                <?= Html::button('Aceptar', ['value' => url::to('asignarpermisos'), 'class' => 'btn btn-success', 'id'=>'modalButton2', 'data-toggle' => 'tooltip', 'title' => 'Permisos', 'style' => 'background-color: #337ab7']) 
                                ?> 
                                <?php
                                    Modal::begin([
                                        'header' => '<h4>Asignar Permisos</h4>',
                                        'id' => 'modal2',
                                        //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent2'></div>";
                                                                    
                                    Modal::end(); 
                                ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-users" style="font-size: 15px; color: #ff2c2c;"></em> Verificar Equipo: </label>                            
                                <?= Html::a('Aceptar',  ['viewequipo'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #337ab7',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Ver Equipo']) 
                            ?>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="card1 mb">
                            <label style="font-size: 15px;"><em class="fas fa-unlock" style="font-size: 15px; color: #ff2c2c;"></em> Administrativo </label>                            
                                <?= Html::a('Aceptar',  ['administrar'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #337ab7',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Administrativo']) 
                            ?>
                        </div>
                    </div>
                    <?php if($sessiones == '2953' || $sessiones == '3205' || $sessiones == '3468' || $sessiones == '3229'  || $sessiones == '57' || $sessiones == '565' || $sessiones = '4457') {?>
                        <div class="col-md-2">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #ff2c2c;"></em> Descargar plan: </label>                            
                                <?= Html::button('Mes Pasado', ['value' => url::to('excelplanpast'), 'class' => 'btn btn-success', 'id'=>'modalButton3', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7']) 
                                ?> 
                                <?php
                                    Modal::begin([
                                        'header' => '<h4>Descargando plan...</h4>',
                                        'id' => 'modal3',
                                        //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent3'></div>";
                                                                    
                                    Modal::end(); 
                                ?>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="card1 mb">
                                <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #ff2c2c;"></em> Descargar plan: </label>  
                                <?= Html::button('Mes Actual', ['value' => url::to('excelplanx'), 'class' => 'btn btn-success', 'id'=>'modalButton5', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7']) 
                                ?> 
                                <?php
                                    Modal::begin([
                                        'header' => '<h4>Descargando plan...</h4>',
                                        'id' => 'modal5',
                                        //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent5'></div>";
                                                                    
                                    Modal::end(); 
                                ?>
                            </div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<?php } ?>