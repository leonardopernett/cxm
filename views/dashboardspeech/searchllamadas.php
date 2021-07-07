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

$this->title = 'Dashboard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Dashboard Voz del Cliente';

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

?>
<link rel="stylesheet" href="https://qa.grupokonecta.local/qa_managementv2/web/font_awesome_local/css.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
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
            font-family: "Nunito";
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
            font-family: "Nunito";
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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }

    .card:hover .card1:hover {
        top: -15%;
    }

  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Dashboard-Escuchar-+.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
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
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
<div class="capaPone" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">
            <label><i class="fas fa-list-alt" style="font-size: 20px; color: #559FFF;"></i> Selección de datos:</label>
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
            <br>
            <div class="row">
                <div class="col-md-3">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><i class="fas fa-search" style="font-size: 15px; color: #827DF9;"></i> Buscar llamadas: </label> 
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
                        <label style="font-size: 15px;"><i class="fas fa-globe" style="font-size: 15px; color: #827DF9;"></i> Buscar general: </label> 
                        <?= Html::a('Buscar',  ['searchllamadas', 'varprograma'=>$txtvarprograma, 'varcodigopcrc'=>$txtvarcodigopcrc, 'varidcategoria'=>$txtvaridcategoria, 'varextension'=>$txtvarextension, 'varfechasinicio'=>$txtvarfechasinicio, 'varfechasfin'=>$txtvarfechasfin, 'varcantllamadas'=>$txtvarcantllamadas, 'varfechainireal'=>$txtvarfechainireal, 'varfechafinreal'=>$txtvarfechafinreal,'varcodigos'=>$txtvarcodigos], ['class' => 'btn btn-success',
                            'style' => 'background-color: #337ab7',
                            'data-toggle' => 'tooltip',
                            'title' => 'Buscar llamadas']) 
                        ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><i class="fas fa-backward" style="font-size: 15px; color: #827DF9;"></i> Regresar: </label> 
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
                        <label style="font-size: 15px;"><i class="fas fa-download" style="font-size: 15px; color: #827DF9;"></i> Descargar gestión: </label> 
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
        <div class="col-md-4">
          <div class="card mb">
            <label><i class="fas fa-info-circle" style="font-size: 20px; color: #C148D0;"></i> Pcrc Seleccionado:</label>
            <label><?php echo $txtvarcodigopcrc.' - '.$txtnombrepcrc; ?></label>
          </div>
        </div>
        <div class="col-md-4">
          <div class="card mb">
            <label><i class="fas fa-calendar-alt" style="font-size: 20px; color: #559FFF;"></i> Rango de Fechas:</label>
            <label><?php echo $txttxtvarfechainireal.' - '.$txttxtvarfechafinreal; ?></label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card mb">
            <label><i class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></i> Llamadas general:</label>
            <label  style="font-size: 23px; text-align: center;"><?php echo $txttxtvarcantllamadas; ?></label>
          </div>
        </div>
        <div class="col-md-2">
          <div class="card mb">
            <label><i class="fas fa-hashtag" style="font-size: 20px; color: #FFC72C;"></i> Llamadas buscadas:</label>
            <label  style="font-size: 23px; text-align: center;"><?php echo $txttxtvarcantllamadasb; ?></label>
          </div>
        </div>
  </div>
</div>
<br>
<div id="capaGtwo" class="capaPtwo">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <label><i class="fas fa-list" style="font-size: 20px; color: #00968F;"></i> Resultados:</label>
        <br>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => [
                    [
                        'attribute' => 'Id de llamadas',
                        'value' => 'callId',
                    ],
                    [
                        'attribute' => 'Fecha y hora real',
                        'value' => 'fechareal',
                    ],
                    [
                        'attribute' => 'Servicio',
                        'value' => 'servicio',
                    ],
                    [
                        'attribute' => 'Agente',
                        'value' => 'login_id',
                    ],
                    [
                        'attribute' => 'Id redbox',
                        'value' => 'idredbox',
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

                                            return Html::a(Yii::t('app', '<i id="idimage" class="fas fa-play-circle" style="font-size: 17px; color: #ff3838; display: inline;"></i>'),
                                                'javascript:void(0)',
                                                [
                                                    'title' => Yii::t('app', 'Llamadas'),
                                                    //'data-pjax' => '0',
                                                    'onclick' => "
                                                        generarcarga();                        
                                                        $.ajax({
                                                            type     :'get',
                                                            cache    : false,
                                                            url  : '" . Url::to(['viewcalls',
                                                            'idlogin' => $model->login_id, 'idredbox' => $model->idredbox, 'idgrabadora' => $model->idgrabadora]) . "',
                                                            success  : function(response) {
                                                                $('#ajax_result').html(response);
                                                            }
                                                        });
                                                    return false;",
                                                ]);


                                            // return Html::a('<i class="fas fa-play-circle" style="font-size: 17px; color: #ff3838;"></i>',  ['viewcalls', 'idredbox' => $model->idredbox, 'idgrabadora' => $model->idgrabadora], [
                                            //     'class' => '',
                                            //     'title' => 'Verificar audio',
                                            //     'data' => [
                                            //         'method' => 'post',
                                            //     ],
                                            // ]);  
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
            swal.fire("!!! Advertencia !!!","Debes seleccionar si contiene o no contiene parametrización","warning");
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
          timer: 9000
        })
    };
</script>