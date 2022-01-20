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


$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='tooltip']").tooltip(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);
$js = <<< 'SCRIPT'
/* To initialize BS3 popovers set this below */
$(function () { 
    $("[data-toggle='popover']").popover(); 
});
SCRIPT;
// Register tooltip/popover initialization javascript
$this->registerJs($js);

$this->title = 'Dashboard Escuchar + 2.0';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Dashboard Escuchar + 2.0';

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

    $txtnombrepcrc = Yii::$app->db->createCommand("select s.pcrc from tbl_speech_categorias s where s.cod_pcrc in ('$txtvarcodigopcrc') and s.anulado = 0 group by s.pcrc")->queryScalar();

    $txttxtvarcantllamadas = $txtvarcantllamadas;

    $txttxtvarfechainireal = $txtvarfechainireal;
    $txttxtvarfechafinreal = $txtvarfechafinreal;

    $txtListLideres = Yii::$app->db->createCommand("select distinct u.usua_id, u.usua_nombre, e.id from tbl_roles r     inner join rel_usuarios_roles ur on r.role_id = ur.rel_role_id inner join tbl_usuarios u on ur.rel_usua_id = u.usua_id inner join tbl_equipos e on u.usua_id = e.usua_id inner join tbl_arbols_equipos ae on e.id = ae.equipo_id  inner join tbl_arbols a on ae.arbol_id = a.id  inner join tbl_speech_servicios ss on a.arbol_id = ss.arbol_id inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.cod_pcrc in ('$txtvarcodigopcrc') and a.activo = 0  and r.role_id = 273")->queryAll();
    $listDatalideres = ArrayHelper::map($txtListLideres, 'id', 'usua_nombre');

    $txtservicios = Yii::$app->db->createCommand("select distinct ss.nameArbol from tbl_speech_servicios ss inner join tbl_speech_parametrizar sp on ss.id_dp_clientes = sp.id_dp_clientes where sp.cod_pcrc in ('$txtvarcodigopcrc')")->queryScalar();


?>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css" >
<link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Nunito">
<style type="text/css">
    .card {
            height: 80px;
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
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/chart.min.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>
<div class="capaPone" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">
            <label><em class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></em></label>
            <div class="row">
              <div class="col-md-3">
                <label><?= Yii::t('app', 'Indicadores') ?></label>
                <?=  $form->field($model, 'idcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere("idcategorias = 1")->andwhere("cod_pcrc in ('$txtvarcodigopcrc')")->orderBy(['nombre'=> SORT_ASC])->all(), 'idspeechcategoria', 'nombre'),
                                            [
                                                'prompt'=>'Seleccionar indicador...',
                                                'onchange' => '
                                                    $.get(
                                                        "' . Url::toRoute('dashboardspeech/listarvariablesx') . '", 
                                                        {id: $(this).val()}, 
                                                        function(res){
                                                            $("#requester").html(res);
                                                        }
                                                    );
                                                ',

                                            ]
                                )->label(''); 
                ?>
              </div>
              <div class="col-md-3">
                <label><?= Yii::t('app', 'Variables') ?></label>
                <?= $form->field($model,'nombreCategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                            [],
                                            [
                                                'prompt' => 'Seleccionar variable...',
                                                'id' => 'requester'
                                            ]
                                        )->label('');
                ?>
              </div>
              <div class="col-md-3">
                <label><?= Yii::t('app', 'Motivos de contacto') ?></label>
                <?=  $form->field($model, 'extension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechCategorias::find()->distinct()->where("anulado = 0")->andwhere("idcategorias = 3")->andwhere("cod_pcrc in ('$txtvarcodigopcrc')")->orderBy(['nombre'=> SORT_ASC])->all(), 'idcategoria', 'nombre'),
                                            [
                                                'prompt'=>'Seleccionar motivo de contacto...',
                                            ]
                                )->label(''); 
                ?>
              </div>
              <div class="col-md-3">
                <label><?= Yii::t('app', 'Contiene/No contiene') ?></label>
                <?php $var = ['1' => 'Contiene', '2' => 'No contiene']; ?>
                <?= $form->field($model, 'login_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var, ['prompt' => 'Seleccionar...', 'id'=>"idcontact"])->label('') ?> 
              </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <label><?= Yii::t('app', 'Seleccionar Lider') ?></label>
                    <?=  $form->field($model, 'servicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listDatalideres,
                                            [
                                                'prompt'=>'Seleccionar lider...',
                                                'onchange' => '
                                                    $.get(
                                                        "' . Url::toRoute('dashboardspeech/listarlideresx') . '", 
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
                    <label><?= Yii::t('app', 'Seleccionar Asesor') ?></label>
                    <?= $form->field($model,'fechallamada', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                            [],
                                            [
                                                'prompt' => 'Seleccionar asesor...',
                                                'id' => 'requester2'
                                            ]
                                        )->label('');
                    ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <label><?= Yii::t('app', 'Seleccionar Tipologias') ?></label>
                    <?=
                        $form->field($model, 'idredbox', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($model->tipologiasList(), ['prompt' => Yii::t('app', 'Seleccionar tipologia...')]);
                    ?>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-3">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #827DF9;"></em> Buscar llamadas: </label> 
                        <?= Html::submitButton(Yii::t('app', 'Buscar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Buscar']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-globe" style="font-size: 15px; color: #827DF9;"></em> Buscar general: </label> 
                        <?= Html::a('Buscar',  ['searchllamadas', 'varprograma'=>$txtvarprograma, 'varcodigopcrc'=>$txtvarcodigopcrc, 'varidcategoria'=>$txtvaridcategoria, 'varextension'=>$txtvarextension, 'varfechasinicio'=>$txtvarfechasinicio, 'varfechasfin'=>$txtvarfechasfin, 'varcantllamadas'=>$txtvarcantllamadas, 'varfechainireal'=>$txtvarfechainireal, 'varfechafinreal'=>$txtvarfechafinreal,'varcodigos'=>$txtvarcodigos], ['class' => 'btn btn-success',
                            'style' => 'background-color: #337ab7',
                            'data-toggle' => 'tooltip',
                            'title' => 'Buscar llamadas']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-backward" style="font-size: 15px; color: #827DF9;"></em> Regresar: </label> 
                        <?= Html::a('Regresar',  ['indexvoice','arbol_idV'=>$txtvarprograma,'codpcrc'=>$txtvarcodigopcrc,'parametros_idV'=>$txtvarextension,'codparametrizar'=>$txtvarcodigos,'dateini'=>$txtvarfechainireal,'datefin'=>$txtvarfechafinreal], ['class' => 'btn btn-success',
                            'style' => 'background-color: #707372',
                            'data-toggle' => 'tooltip',
                            'title' => 'Regresar']) 
                        ?>
                    </div>
                </div>

                <?php if ($txttxtvarcantllamadasb != 0) {  ?>
                <div class="col-md-3">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-download" style="font-size: 15px; color: #827DF9;"></em> Descargar gestiones: </label> 
                        <?= Html::button('Descargar', ['value' => url::to(['descargarcalls','varprograma'=>$txtvarprograma, 'varcodigopcrc'=>$txtvarcodigopcrc, 'varidcategoria'=>$varcategoriass, 'varextension'=>$txtvarextension, 'varfechasinicio'=>$txtvarfechasinicio, 'varfechasfin'=>$txtvarfechasfin, 'varcantllamadas'=>$txtvarcantllamadas, 'varfechainireal'=>$txtvarfechainireal, 'varfechafinreal'=>$txtvarfechafinreal,'consinmotivos'=>$varidloginid]), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Descargar', 'style' => 'background-color: #337ab7']) 
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
        </div>
    </div>
<?php ActiveForm::end(); ?>
  <hr>
    <div class="row">
        <div class="col-md-2">
          <div class="card mb">
            <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #559FFF;"></em> Rango de Fechas:</label>
            <label style="font-size: 14px; text-align: center;"><?php echo $txttxtvarfechainireal.' - '.$txttxtvarfechafinreal; ?></label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card mb">
            <label><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em> Servicio:</label>
            <label style="font-size: 15px; text-align: center;"><?php echo $txtservicios; ?></label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card mb">
            <label><em class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></em> Programa Pcrc:</label>
            <label style="font-size: 15px; text-align: center;"><?php echo $txtvarcodigopcrc.' - '.$txtnombrepcrc; ?></label>
          </div>
        </div>        
        <div class="col-md-2">
          <div class="card mb">
            <label><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em> Interacciones General:</label>
            <label  style="font-size: 15px; text-align: center;"><?php echo $txttxtvarcantllamadas; ?></label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card mb">
            <label><em class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></em> Interacciones Buscadas:</label>
            <label  style="font-size: 15px; text-align: center;"><?php echo $txttxtvarcantllamadasb; ?></label>
          </div>
        </div>
  </div>
</div>
<br>
<div id="capaGtwo" class="capaPtwo">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><em class="fas fa-list" style="font-size: 20px; color: #00968F;"></em> Resultados:</label>
        <br>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'Fecha',
                        'value' => 'fechareal',
                    ],
                    [
                        'attribute' => 'Asesor',
                        'value' => 'login_id',
                    ],
                    [
                        'attribute' => 'redbox',
                        'value' => 'idredbox',
                    ],                   
                    [
                        'attribute' => 'Tipologia Encuesta',
                        'value' => function($data){
                            return $data->getstipologia($data->connid);
                        }
                    ],                                                             
                    [
                        'attribute' => 'Estado',
                        'value' => function($data){
                            return $data->getsestado($data->callId,$data->servicio,$data->fechareal);
                        }
                    ],                                         
                    [
                        'attribute' => 'Valorador',
                        'value' => function($data){
                            return $data->getsresposanble($data->callId,$data->servicio,$data->fechareal);
                        }
                    ],
                    [
                        'attribute' => 'Marca',
                        'value' => function($data){
                            return $data->getsmarca($data->callId,$data->servicio,$data->extension);
                        }
                    ],
                    [
                        'attribute' => 'Canal',
                        'value' => function($data){
                            return $data->getscanal($data->callId,$data->servicio,$data->extension);
                        }
                    ],
                    [
                        'attribute' => 'Agente',
                        'value' => function($data){
                            return $data->getsagente($data->callId,$data->servicio,$data->extension);
                        }
                    ], 
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'color:#337ab7;',],
                        'contentOptions' => ['style' => 'text-align: center;',],
                        'template' => '{update}',
                        'buttons' => 
                                [
                                   'update' => function ($url, $model) {
                                        return Html::a(Yii::t('app', '<i id="idimage" class="fas fa-paperclip" style="font-size: 17px; color: #495057; display: inline;"></i>'),'javascript:void(0)',
                                        [
                                            'title' => Yii::t('app', 'Datos de las Interacciones'),
                                            'onclick' => "                       
                                                $.ajax({
                                                    type     :'get',
                                                    cache    : false,
                                                    url  : '" . Url::to(['viewcallids',
                                                        'idcallids' => $model->callId,'varfechareal' => $model->fechareal, 'varconnid'=>$model->connid, 'varcategolias' => $model->idcategoria]) . "',
                                                        success  : function(response) {
                                                            $('#ajax_result').html(response);
                                                        }
                                                });
                                            return false;",
                                        ]);
                                                                               
                                    }
                                ]                              
                    ], 
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'color:#337ab7;',],
                        'contentOptions' => ['style' => 'text-align: center;',],
                        //'template' => '{view}{update}{delete}',
                        'template' => '{update}',
                        'buttons' => 
                                [
                                   'update' => function ($url, $model) {
                                                                                
                                            $varextensiones = $model->extension;
                                            $varservicios = $model->servicio;
                                            if (strlen($varextensiones) <= 3) {
                                                $varcomprobacion = Yii::$app->db->createCommand("SELECT distinct sc.cod_pcrc FROM tbl_speech_parametrizar sp INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc WHERE sc.programacategoria IN ('$varservicios') AND sp.rn IN ('$varextensiones')")->queryScalar();

                                                
                                            }else{
                                                if (strlen($varextensiones) >= 4 || strlen($varextensiones) <= 6) {
                                                    $varcomprobacion = Yii::$app->db->createCommand("SELECT distinct sc.cod_pcrc FROM tbl_speech_parametrizar sp INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc WHERE sc.programacategoria IN ('$varservicios') AND sp.ext IN ('$varextensiones')")->queryScalar();

                                                }else{
                                                    $varcomprobacion = Yii::$app->db->createCommand("SELECT distinct sc.cod_pcrc FROM tbl_speech_parametrizar sp INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc WHERE sc.programacategoria IN ('$varservicios') AND sp.usuared IN ('$varextensiones')")->queryScalar();
                                                }
                                            }
                                                        

                                                        return Html::a(Yii::t('app', '<i id="idimage" class="fas fa-calculator" style="font-size: 17px; color: #9F9AE1; display: inline;"></i>'),
                                                            'javascript:void(0)',
                                                            [
                                                                'title' => Yii::t('app', 'Resultados VOC'),
                                                                //'data-pjax' => '0',
                                                                'onclick' => "
                                                                    generarcarga2();                        
                                                                    $.ajax({
                                                                        type     :'get',
                                                                        cache    : false,
                                                                        url  : '" . Url::to(['viewrtas',
                                                                        'idspeechcalls' => $model->iddashboardspeechcalls, 'varcodpcrc'=>$varcomprobacion]) . "',
                                                                        success  : function(response) {
                                                                            $('#ajax_result').html(response);
                                                                        }
                                                                    });
                                                                return false;",
                                                            ]);


                                                                               
                                    }
                                ]                              
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'color:#337ab7;',],
                        'contentOptions' => ['style' => 'text-align: center;',],
                        //'template' => '{view}{update}{delete}',
                        'template' => '{view}',
                        'buttons' => 
                                [
                                    'view' => function ($url, $model) {   
                                        $txtidredbox = $model->idredbox;
                                        $txtidgrabadora = $model->idgrabadora;

                                        if ($txtidredbox != null && $txtidgrabadora != null) {
                                           
                                            return Html::a(Yii::t('app', '<i id="idimage" class="fas fa-play-circle" style="font-size: 17px; color: #ff3838; display: inline;"></i>'),
                                                'javascript:void(0)',
                                                [
                                                    'title' => Yii::t('app', 'Escucha VOC'),
                                                    //'data-pjax' => '0',
                                                    'onclick' => "
                                                        generarcarga();                        
                                                        $.ajax({
                                                            type     :'get',
                                                            cache    : false,
                                                            url  : '" . Url::to(['viewcalls',
                                                            'idlogin' => $model->login_id, 'idredbox' => $model->idredbox, 'idgrabadora' => $model->idgrabadora, 'idconnid' => $model->connid]) . "',
                                                            success  : function(response) {
                                                                $('#ajax_result').html(response);
                                                            }
                                                        });
                                                    return false;",
                                                ]);


                                            
                                        }
                                    }
                                ]                              
                    ],
                    [
                        'class' => 'yii\grid\ActionColumn',
                        'headerOptions' => ['style' => 'color:#337ab7;',],
                        'contentOptions' => ['style' => 'text-align: center;',],
                        //'template' => '{view}{update}{delete}',
                        'template' => '{update}',
                        'buttons' => 
                                [
                                   'update' => function ($url, $model) {
                                        $idloginid = $model->login_id;
                                        $concatenarspeech = $model->callId.'; '.$model->fechareal;
                                        $txtejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_ejecucionformularios te WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();
                                        $txttmpejecucion = Yii::$app->db->createCommand("SELECT COUNT(te.id) FROM tbl_tmpejecucionformularios te WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();
                                        $txtidejecucion = Yii::$app->db->createCommand("SELECT te.id FROM tbl_ejecucionformularios te WHERE te.dsfuente_encuesta like '%$concatenarspeech%'")->queryScalar();

                                        $idconnId = $model->connid;
                                        if ($idconnId != "") {
                                            $idbase = Yii::$app->db->createCommand("select b.id from tbl_base_satisfaccion b where b.connid in ('$idconnId')")->queryScalar();
                                        }else{
                                            $idbase = "";
                                        }
                                        

                                        if ($txtejecucion == 0 && $txttmpejecucion == 0) {
                                            $varextensiones = $model->extension;
                                            $varservicios = $model->servicio;
                                            if (strlen($varextensiones) <= 3) {
                                                $varcomprobacion = Yii::$app->db->createCommand("SELECT distinct CONCAT(sc.cod_pcrc,' - ',sc.pcrc) FROM tbl_speech_parametrizar sp INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc WHERE sc.programacategoria IN ('$varservicios') AND sp.rn IN ('$varextensiones')")->queryScalar();

                                                $varnombreservicio = Yii::$app->db->createCommand("SELECT DISTINCT ss.nameArbol FROM tbl_speech_servicios ss INNER JOIN tbl_speech_parametrizar sp ON ss.id_dp_clientes = sp.id_dp_clientes INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc  WHERE sc.programacategoria IN ('$varservicios') AND sp.rn IN ('$varextensiones')")->queryScalar();
                                            }else{
                                                if (strlen($varextensiones) >= 4 || strlen($varextensiones) <= 6) {
                                                    $varcomprobacion = Yii::$app->db->createCommand("SELECT distinct CONCAT(sc.cod_pcrc,' - ',sc.pcrc) FROM tbl_speech_parametrizar sp INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc WHERE sc.programacategoria IN ('$varservicios') AND sp.ext IN ('$varextensiones')")->queryScalar();

                                                    $varnombreservicio = Yii::$app->db->createCommand("SELECT DISTINCT ss.nameArbol FROM tbl_speech_servicios ss INNER JOIN tbl_speech_parametrizar sp ON ss.id_dp_clientes = sp.id_dp_clientes INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc  WHERE sc.programacategoria IN ('$varservicios') AND sp.ext IN ('$varextensiones')")->queryScalar();
                                                }else{
                                                    $varcomprobacion = Yii::$app->db->createCommand("SELECT distinct CONCAT(sc.cod_pcrc,' - ',sc.pcrc) FROM tbl_speech_parametrizar sp INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc WHERE sc.programacategoria IN ('$varservicios') AND sp.usuared IN ('$varextensiones')")->queryScalar();

                                                    $varnombreservicio = Yii::$app->db->createCommand("SELECT DISTINCT ss.nameArbol FROM tbl_speech_servicios ss INNER JOIN tbl_speech_parametrizar sp ON ss.id_dp_clientes = sp.id_dp_clientes INNER JOIN tbl_speech_categorias sc ON sp.cod_pcrc = sc.cod_pcrc  WHERE sc.programacategoria IN ('$varservicios') AND sp.usuared IN ('$varextensiones')")->queryScalar();
                                                }
                                            }

                                                    if ($idbase != "") {

                                                        if (strlen($idloginid) > 7) {
                                                         
                                                            return Html::a('<i id="idimage" class="fas fa-edit" style="font-size: 17px; color: #4dbdff; display: inline;"></i>'
                                                                    , Url::to(['basesatisfaccion/showformulariogestion',
                                                                        'basesatisfaccion_id' => $idbase, 'preview' => 5, 'fill_values' => false, 'banderaescalado' => false, 'aleatorio'=> 0]), ['title' => Yii::t('yii', 'Gestionar'),'target' => "_blank"]);
                                                        }

                                                        
                                                    }else{

                                                        if (strlen($idloginid) > 7) {
                                                            
                                                            return Html::a(
                                                                '<i id="idimage" class="fas fa-edit" style="font-size: 17px; color: #4c6ef5; display: inline;"></i>', 
                                                                Url::to(['valoraspeech', 'idspeechcalls' => $model->iddashboardspeechcalls, 'varcodpcrc'=>$varcomprobacion, 'varservisioname' => $varnombreservicio]), ['title' => Yii::t('yii', 'Valoraci�n VOC'), 'data-pjax' => 0, 'target' => "_blank"]
                                                            );
                                                        }
                                                    }
                                                        


                                                   
                                        }else{
                                            if ($idbase == "") {
                                                return Html::a('<i id="idimage" class="fas fa-search" style="font-size: 17px; color: #4c6ef5; display: inline;"></i>'
                                                        , Url::to(['formularios/showformulariodiligenciadohistorico'
                                                        , 'tmp_id' => $txtidejecucion,'view'=>"reportes/historicoformularios"]), [
                                                        'title' => Yii::t('yii', 'ver formulario'),
                                                        'target' => "_blank"
                                                ]);
                                            }else{
                                                return Html::a('<i id="idimage" class="fas fa-search" style="font-size: 17px; color: #4dbdff; display: inline;"></i>', Url::to(['basesatisfaccion/showformulariogestion'
                                                    , 'basesatisfaccion_id' => $idbase, 'banderaescalado'=> 0, 'aleatorio' => false ,'preview' => 1, 'fill_values' => true,'view'=>"reportes/historicoformularios"]), [
                                                    'title' => Yii::t('yii', 'ver formulario'),
                                                    'target' => "_blank"
                                                ]);
                                            }                                  
                                        }
                                        
                                    }
                                ]                              
                    ],
                ],
            ]);?>
            <?php
            echo Html::tag('div', '', ['id' => 'ajax_result']);
            ?>
      </div>
    </div>
  </div>
</div>
<hr>

<script type="text/javascript">
    function varVerificar(){
        var varidcontact = document.getElementById("idcontact").value;
        var varidindicador = document.getElementById("dashboardspeechcalls-idcategoria").value;
        var varidvariables = document.getElementById("requester").value;
        var varidmotivos = document.getElementById("dashboardspeechcalls-extension").value;

        if (varidcontact == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debes seleccionar si contiene o no contiene parametrizaci�n","warning");
            return;
        }else{
            if (varidindicador == "" && varidmotivos == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debes seleccionar un filtro de busqueda","warning");
                return;
            }
        }
    };

    function generarcarga(){
        Swal.fire({
          position: 'top-end',
          icon: 'success',
          title: 'Buscando llamada seleccionada...',
          showConfirmButton: false,
          timer: 4500
        })
    };

    function generarcarga2(){
        Swal.fire({
          position: 'top-end',
          icon: 'success',
          title: 'Realizando calculo...',
          showConfirmButton: false,
          timer: 400
        })
    };
</script>