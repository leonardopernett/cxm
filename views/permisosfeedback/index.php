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

$this->title = 'Permisos Historico de Feedback';
$this->params['breadcrumbs'][] = $this->title;

    $sesiones =Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

   
    $varListmeses = array();
    $varListCantidad = array();
    $varListYear = array();
    foreach ($txtListMeses as $key => $value) {
        array_push($varListmeses, $value['Mes']);
        array_push($varListCantidad, $value['Conteo']);
    }


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
<script src="../../js_extensions/jquery-2.1.1.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>
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
<div class="capapp" style="display: inline;">
    <div class="row">
        <div class="col-md-9">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-bar" style="font-size: 15px; color: #fd7e14;"></em> Grafica general: </label>
                    <div id="conatinergeneric" class="highcharts-container" style="height: 360px;"></div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-hashtag" style="font-size: 15px; color: #559FFF;"></em> Cantidad de permisos: </label>
                        <label  style="font-size: 70px; text-align: center;"><?php echo $txtConteo; ?></label>
                    </div>
                </div>
            </div>
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-save" style="font-size: 15px; color: #C148D0;"></em> Procesos en Permisos: </label>
                        <?=  Html::a(Yii::t('app', 'Asignar Permiso'),
                                                'javascript:void(0)',
                                                [
                                                    'class' =>  'btn btn-primary',
                                                    'title' => Yii::t('app', 'Asignar permiso'),
                                                    //'data-pjax' => '0',
                                                    'onclick' => "                                                                                
                                                        $.ajax({
                                                            type     :'get',
                                                            cache    : false,
                                                            url  : '" . Url::to(['viewexcuse']) . "',
                                                            success  : function(response) {
                                                                $('#ajax_result').html(response);
                                                            }
                                                        });
                                                    return false;",
                                                ]);
                        ?>
                    </div>
                </div>
            </div>  
            <br>
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 15px; color: #C178;"></em> Buscar Permisos: </label>
                        <?php $form = ActiveForm::begin([
                            'options' => ["id" => "buscarMasivos"],
                            'layout' => 'horizontal',
                            'fieldConfig' => [
                                'inputOptions' => ['autocomplete' => 'off']
                              ]
                            ]); ?>
                            <div class="row">
                                <div class="col-md-8">
                                    <?= $form->field($model, 'idusuarios')->textInput(['maxlength' => 300, 'id'=>'txtidusuario', 'placeholder'=>'Usuario de red'])?>
                                </div>
                                <div class="col-md-4">
                                    <?= Html::button('[ Buscar ]', ['onclick' => 'validar();', 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Busca Permisos', 'style' => 'background-color: #4298b400; border-color: #4298b500 !important; color:#000000;']) 
                                            ?> 
                                </div>
                            </div>
                            <br>
                            <div id="idresutlone" style="display: none;">
                                <label>Usuario no registrado en CXM</label>
                            </div>  
                            <div id="idresutltwo" style="display: none;">
                                <label>Usuario ya esta registrado en CXM</label>
                            </div> 
                            <div id="idresutlthree" style="display: inline;">
                                <label>--</label>
                            </div>                          

                        <?php $form->end() ?> 
                    </div>
                </div>   
            </div>  
        </div>
    </div>
</div>
<hr>
<?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
?>
<script type="text/javascript">
    function validar(){
        var vartxtusuarioid = document.getElementById("txtidusuario").value;
        var varcapaUno = document.getElementById("idresutlone");
        var varcapaDos = document.getElementById("idresutltwo");
        var varcapaTres = document.getElementById("idresutlthree");

        if (vartxtusuarioid == "") {
            varcapaUno.style.display = 'none';
            varcapaDos.style.display = 'none';
            varcapaTres.style.display = 'inline';
            event.preventDefault();
            swal.fire("��� Advertencia !!!","Debe de ingresar un usuario de red","warning");
            return;
            
        }else{
            $.ajax({
                method: "get",
                url: "validarregistro",
                data: {
                    txtusuarios : vartxtusuarioid,
                },
                success : function(response){
                    numRta =   JSON.parse(response);
                    console.log(numRta);

                    if (numRta == 1) {
                        console.log('Aqui');
                        varcapaUno.style.display = 'none';
                        varcapaDos.style.display = 'inline';
                        varcapaTres.style.display = 'none';
                    }else{
                        console.log("Vamos");
                        varcapaUno.style.display = 'inline';
                        varcapaDos.style.display = 'none';
                        varcapaTres.style.display = 'none';
                    }
                }
            });
        }
    }

    $(function(){
        var Listado = "<?php echo implode($varListmeses,",");?>";
        Listado = Listado.split(",");
        //console.log(Listado);

        Highcharts.setOptions({
                lang: {
                  numericSymbols: null,
                  thousandsSep: ','
                }
        });

        $('#conatinergeneric').highcharts({
            chart: {
                borderColor: '#DAD9D9',
                borderRadius: 7,
                borderWidth: 1,
                type: 'line'
            },

            yAxis: {
              title: {
                text: 'Cantidad Encuestas'
              }
            }, 

            title: {
              text: '',
            style: {
                    color: '#3C74AA'
              }

            },

            xAxis: {
                  categories: Listado,
                  title: {
                      text: null
                  }
                },

            series: [{
              name: 'Cantidad Permisos',
              data: [<?= join($varListCantidad, ',')?>],
              color: '#559FFF'
            }],

            responsive: {
                rules: [{
                    condition: {
                        maxWidth: 500
                    },
                    chartOptions: {
                        legend: {
                            layout: 'horizontal',
                            align: 'center',
                            verticalAlign: 'bottom'
                        }
                    }
                }]
            }
          });
    });
</script>