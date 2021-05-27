<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Buzones Kaliope';
$this->params['breadcrumbs'][] = $this->title;



    $sessiones = Yii::$app->user->identity->id;

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
            font-family: "Nunito";
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
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<?php if ($sessiones == '2953' || $sessiones == '2911' || $sessiones == '3205' || $sessiones == '3229') { ?>
<div class="capaUno" style="display: inline">
<?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-id-card" style="font-size: 20px; color: #827DF9;"></i> Acciones de búsqueda</label>
                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;">Seleccionar rango de fecha: </label>
                            <?=
                                $form->field($model, 'fechacreacion', [
                                    'labelOptions' => ['class' => 'col-md-12'],
                                    'template' => '<div class="col-md-6">{label}</div>'
                                    . '<div class="col-md-12"><div class="input-group">'
                                    . '<span class="input-group-addon" id="basic-addon1">'
                                    . '<i class="glyphicon glyphicon-calendar"></i>'
                                    . '</span>{input}</div>{error}{hint}</div>',
                                    'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                                    'options' => ['class' => 'drp-container form-group']
                                ])->label('')->widget(DateRangePicker::classname(), [
                                    'useWithAddon' => true,
                                    'convertFormat' => true,
                                    'presetDropdown' => true,
                                    'readonly' => 'readonly',
                                    'pluginOptions' => [
                                        'timePicker' => false,
                                        'format' => 'Y-m-d',
                                        'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                                        'endDate' => date("Y-m-d"),
                                        'opens' => 'right',
                                ]]);
                            ?>
                    </div>
                    <div class="col-md-6">
                        <label style="font-size: 15px;">Seleccionar Programa/PCRC: </label>
                            <?=
                                    $form->field($model, 'arbol_id')->label('')->widget(Select2::classname(), [
                                                    'id' => 'ButtonSelect',
                                                    'name' => 'BtnSelectes',
                                                    'attribute' => 'Valorador',
                                                    'language' => 'es',
                                                    'options' => ['placeholder' => Yii::t('app', 'Seleccionar programa/pcrc...')],
                                                    'pluginOptions' => [
                                                        'allowClear' => true,
                                                        'minimumInputLength' => 4,
                                                        'ajax' => [
                                                            'url' => \yii\helpers\Url::to(['basesatisfaccion/getarbolesbypcrc']),
                                                            'dataType' => 'json',
                                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                                        ],
                                                        'initSelection' => new JsExpression('function (element, callback) {
                                                                    var id=$(element).val();
                                                                    if (id !== "") {
                                                                        $.ajax("' . Url::to(['basesatisfaccion/getarbolesbypcrc']) . '?id=" + id, {
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
                <br> 
                <div class="row">
                    <div class="col-md-12">
                        <label style="font-size: 15px;">Ruta inicio: </label>
                        <?= $form->field($model, 'ruta_inicio')->textInput(['maxlength' => 250, 'value'=>$varbuzones, 'id'=>'idruta']) ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-6">
                        <div class="card1 mb">
                                <?= Html::submitButton(Yii::t('app', 'Buscar'),
                                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                'data-toggle' => 'tooltip',
                                                'style' => 'background-color: #4298B4',
                                                'title' => 'Buscar']) 
                                ?>
                        </div>  
                    </div>
                    <div class="col-md-6">
                        <div class="card1 mb">
                            <div onclick="savegeneral();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                      Guardar
                            </div>
                        </div>  
                    </div>
                </div>                                           
            </div>
        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<hr>
<?php } ?>
<div class="capaDos" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-list" style="font-size: 20px; color: #827DF9;"></i> Listado</label>
                <br>
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                    <thead>
                        <tr>
                        <th colspan="3" class="text-center" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Listado de buzones registrados') ?></label></th>
                        </tr>
                        <tr>
                            <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Nombre Pcrc') ?></label></th>
                            <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Codigo Pcrc') ?></label></th>
                            <th style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', 'Ruta') ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $varlistarta = Yii::$app->db->createCommand("select * from tbl_buzones_kaliope bk where anulado = 0 ")->queryAll();

                            foreach ($varlistarta as $key => $value) {
                                
                        ?>
                        <tr>
                            <td><label style="font-size: 12px;"><?php echo  $value['arbol_name']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['cod_pcrc']; ?></label></td>
                            <td><label style="font-size: 12px;"><?php echo  $value['ruta_inicio']; ?></label></td>
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
<div class="capaTres" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><i class="fas fa-eye" style="font-size: 20px; color: #ff8a25;"></i> Verificar Buzones Kaliope</label>
                <br>
                <label for="txtconnid" style="font-size: 14px;">Ingresar Connid...</label>
                <input type="text" id="idtxtconnid" name="connid" class="form-control" data-toggle="tooltip" title="Connid">
                <br>
                <label for="txttranscrip" style="font-size: 14px;">Transcripcion...</label>
                <input type="text" id="idtxtrta" name="transcripcion" class="form-control" data-toggle="tooltip" title="Trasncripcion">
                <br>
                <div onclick="conexiones();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                    Verificar Conexión
                </div>
            </div>
        </div>
    </div>
</div>
<hr>

<script type="text/javascript">
    function conexiones(){
        var varidtxtconnid = document.getElementById("idtxtconnid").value;

        if (varidtxtconnid == "") {

        }else{
            $.ajax({
                method: "post",
                url: "transcripcionkaliope",
                data: {
                    txtvaridruta : varidtxtconnid,
                },
                success : function(response){ 
                    numRta =   JSON.parse(response);
                    console.log(numRta);
                    // window.open('https://qa.grupokonecta.local/qa_managementv2/web/index.php/buzoneskaliope/index','_self');
                    // window.open('http://127.0.0.1/qa_pruebas/web/index.php/buzoneskaliope/index','_self');
                }
            });
        }
    };

    function savegeneral(){
        var varidruta = document.getElementById("idruta").value;
        var varpcrc  = "<?php echo $varpcrc; ?>";
        var varnombrepcrc = "<?php echo $varnombrepcrc; ?>";
        var varrest = "<?php echo $rest; ?>";

        if (varidruta == "") {

        }else{
            $.ajax({
                method: "get",
                url: "ingresarruta",
                data: {
                    txtvaridruta : varidruta,
                    txtvarpcrc : varpcrc,
                    txtvarnombrepcrc : varnombrepcrc,
                    txtvarrest : varrest,
                },
                success : function(response){ 
                    numRta =   JSON.parse(response);
                    // window.open('https://qa.grupokonecta.local/qa_managementv2/web/index.php/buzoneskaliope/index','_self');
                    window.open('http://127.0.0.1/qa_pruebas/web/index.php/buzoneskaliope/index','_self');
                }
            });
        }
    };
</script>