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

    $this->title = 'Histórico Valoraciones Mixtas';
    $this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

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

    $paramsBusqueda = [':varSesion' => $sesiones, ':anulado' => 0];

    $varConteoExist = Yii::$app->db->createCommand('
      SELECT d.iddashservicio FROM tbl_dashboardpermisos d 
        WHERE d.usuaid = :varSesion 
          AND anulado = :anulado
        GROUP BY d.iddashservicio')->bindValues($paramsBusqueda)->queryAll();

    $varlistiddpclientes = array();
    $varservicios = null;
    if (count($varConteoExist) != 0) {
      foreach ($varConteoExist as $key => $value) {
        array_push($varlistiddpclientes, $value['iddashservicio']);
      }
      $varservicios = implode(", ", $varlistiddpclientes);
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

    .lds-ring {
      display: inline-block;
      position: relative;
      width: 100px;
      height: 100px;
    }
    .lds-ring div {
      box-sizing: border-box;
      display: block;
      position: absolute;
      width: 80px;
      height: 80px;
      margin: 10px;
      border: 10px solid #3498db;
      border-radius: 70%;
      animation: lds-ring 1.2s cubic-bezier(0.5, 0, 0.5, 1) infinite;
      border-color: #3498db transparent transparent transparent;
    }
    .lds-ring div:nth-child(1) {
      animation-delay: -0.45s;
    }
    .lds-ring div:nth-child(2) {
      animation-delay: -0.3s;
    }
    .lds-ring div:nth-child(3) {
      animation-delay: -0.15s;
    }
    @keyframes lds-ring {
      0% {
        transform: rotate(0deg);
      }
      100% {
        transform: rotate(360deg);
      }
    }

</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<?php if ($txtCumple == "1") { ?>
    <hr>
    <div class="capaMensaje"  style="display: inline;">
      <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">

            <div class="panel-body">
              <p class="text-center"><strong>Importante: </strong> No se encontraron datos en la consulta para generar el hitórico requerido, por favor vuelva a realizar una nueva consulta. </p>
              <div class="row" style="text-align: center;">
                <?= Html::a('Nueva consulta',  ['index'], ['class' => 'btn btn-success',
                                'style' => 'background-color: #707372',
                                'data-toggle' => 'tooltip',
                                'title' => 'Nueva consulta']) 
                ?>
              </div>
            </div>

          </div>
        </div>
      </div>
    </div>
<?php }else{ ?>
    <div class="capaLoader" id="idCapa" style="display: none;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <table class="text-center">
                        <caption>-</caption>
                        <thead>
                            <tr>
                                <th class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
                                <th><?= Yii::t('app', '') ?></th>
                                <th class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informacion de los filtros para buscar los resultados de los asesores...') ?></h4></th>
                            </tr>            
                        </thead>
                    </table>
                </div>
            </div>
        </div>
        <hr>
    </div>

    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="capaFiltros" style="display: inline;" id="capaIdFiltros">
        <div class="row">

            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-chart-line" style="font-size: 20px; color: #559FFF;"></em> Escuchar + </label>
                    <?= Html::a('Aceptar',  ['dashboardspeechdos/index'], ['class' => 'btn btn-success',
                                    'style' => 'display: inline;margin: 3px;height: 34px;display: inline;height: 34px;background-color: #337ab7;',                            
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Escuchar +',
                                    'target' => '_blank'])
                    ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-search" style="font-size: 20px; color: #559FFF;"></em> Buscar Datos </label>
                    <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                          ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                              'data-toggle' => 'tooltip',
                              'title' => 'Buscar Datos',
                              'style' => 'display: inline;margin: 3px;height: 34px;',
                              'id'=>'modalButton1',
                              'onclick' => 'verifica();']) 
                      ?>
                </div>
            </div>

            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-spinner" style="font-size: 20px; color: #559FFF;"></em> Nueva Búsqueda </label>
                    <?= Html::a('Aceptar',  ['index'], ['class' => 'btn btn-success',
                                    'style' => 'display: inline;margin: 3px;height: 34px;display: inline;height: 34px;background-color: #707372;',                            
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Nuevo'])
                    ?>
                </div>
            </div>
        </div>
        
        <hr>

        <div class="row">
            
            <div class="col-md-12">
                <div class="card1 mb">
                    

                    <div class="row">

                        <br>
                        
                        <div class="col-md-4">
                            <label><em class="fas fa-check" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Cliente: </label>
                            <?php
                                if (count($varConteoExist) != 0) {                                  
                            ?>
                                <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->andwhere("id_dp_clientes in ($varservicios)")->orderBy(['nameArbol'=> SORT_ASC])->all(), 'id_dp_clientes', 'nameArbol'),
                                                    [
                                                        'id' => 'txtidclientes',
                                                        'prompt'=>'Seleccionar ',
                                                        'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('listarpcrcs') . '", 
                                                                {id: $(this).val()}, 
                                                                function(res){
                                                                    $("#requester").html(res);
                                                                }
                                                            );
                                                            
                                                        ',

                                                    ]
                                        )->label(''); 
                                ?>
                            <?php
                                } else{
                            ?>
                                <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\SpeechServicios::find()->distinct()->where("anulado = 0")->andwhere("id_dp_clientes != 1")->orderBy(['nameArbol'=> SORT_ASC])->all(), 'id_dp_clientes', 'nameArbol'),
                                                    [
                                                        'id' => 'txtidclientes',
                                                        'prompt'=>'Seleccionar',
                                                        'onchange' => '
                                                            $.get(
                                                                "' . Url::toRoute('listarpcrcs') . '", 
                                                                {id: $(this).val()}, 
                                                                function(res){
                                                                    $("#requester").html(res);
                                                                }
                                                            );
                                                            
                                                        ',

                                                    ]
                                        )->label(''); 
                                ?>
                            <?php                                    
                                }                                 
                            ?>
                        </div>

                        <div class="col-md-4">
                            <label><em class="fas fa-hand-pointer" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Procesos: </label>
                            <?= $form->field($model, 'anulado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varextensiones, ['prompt' => 'Seleccionar...', 'id'=>'iddashboard','onclick'=>'verProcesos();']) ?>
                        </div>

                        <div class="col-md-4">
                            <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Rango de Fechas: </label>
                            <?=
                                $form->field($model, 'fechacreacion', [
                                    'labelOptions' => ['class' => 'col-md-12'],
                                    'template' => 
                                     '<div class="col-md-12"><div class="input-group">'
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

                    </div>

                    <br>

                    <div class="row">
                        
                        <div class="col-md-4">
                            <label><em class="fas fa-list" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Programa/Pcrc: </label>
                            <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                    
                                                    'prompt' => 'Seleccionar...',
                                                    'id' => 'requester',
                                                    'onclick' => 'carga_programa();',
                                                ]
                                            )->label('');
                            ?> 
                        </div>

                        <div class="col-md-4">
                            <label><em class="fas fa-check-square" style="font-size: 20px; color: #559FFF;"></em> Seleccionar Parámetros: </label>

                            <div id="idParametros" class="capaParametros" style="display: none;">
                                <label id="labeltodos" style="display: none;"> 
                                <input type="checkbox" value="todos" id="todos"  onclick="selectodo1()" style="padding-right: 390px; display: none;" /> Todos</label>
                                <?=
                                    $form->field($model,'rn', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->checkboxList(
                                        [],
                                        [
                                            "id" =>"requester1",
                                            'item'=>function ()
                                            {
                                                return '<div class="col-md-8"></div>';
                                            }

                                      ])->label('');
                                ?>
                            </div>

                            <div id="idCalidad" class="capaCalidad" style="display: none;">
                                <label id="labeltodos2" style="display: none;"> 
                                <input type="checkbox" value="todos" id="todos2"  onclick="selectodo2()" style="padding-right: 390px; display: none;" /> Todos</label>
                                <?=
                                    $form->field($model,'ext', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->checkboxList(
                                        [],
                                        [
                                            "id" =>"requester2",
                                            'item'=>function ()
                                            {
                                                return '<div class="col-md-8">
                                                        </div>';
                                            }

                                      ])->label('');
                                ?>
                            </div>

                            <div id="idOjt" class="capaOjt" style="display: none;">
                                <label id="labeltodos3" style="display: none;"> 
                                <input type="checkbox" value="todos" id="todos3"  onclick="selectodo3()" style="padding-right: 390px; display: none;" /> Todos</label>
                                <?=
                                    $form->field($model,'usuared', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->checkboxList(
                                        [],
                                        [
                                            "id" =>"requester3",
                                            'item'=>function ()
                                            {
                                                return '<div class="col-md-8">
                                                        </div>';
                                            }

                                      ])->label('');
                                ?>
                            </div>


                            
                        </div>

                        <div class="col-md-4">
                            
                        </div>

                    </div>

                    <br>


                </div>
            </div>        

        </div>
    </div>
    <?php ActiveForm::end(); ?>
<?php } ?>
<hr>

<script type="text/javascript">
    function selectodo1(){

         var canti = document.getElementById("requester1").querySelectorAll(".prog1");
         var isChecked = document.getElementById('todos').checked;
         if(isChecked){
              for (var x = 0; x < canti.length; x++) {
                   document.getElementById("txtprograma_"+(x+1)).checked = true;
               }
         }
         else{
             for (var x = 0; x < canti.length; x++) {
                   document.getElementById("txtprograma_"+(x+1)).checked = false;
               }
         }
    };

    function selectodo2(){

         var canti2 = document.getElementById("requester2").querySelectorAll(".prog2");
         var isChecked2 = document.getElementById('todos2').checked;
         if(isChecked2){
              for (var x = 0; x < canti2.length; x++) {
                   document.getElementById("txtcprograma_"+(x+1)).checked = true;
               }
         }
         else{
             for (var x = 0; x < canti2.length; x++) {
                   document.getElementById("txtcprograma_"+(x+1)).checked = false;
               }
         }
    };

    function selectodo3(){

         var canti3 = document.getElementById("requester3").querySelectorAll(".prog3");
         var isChecked3 = document.getElementById('todos3').checked;
         if(isChecked3){
              for (var x = 0; x < canti3.length; x++) {
                   document.getElementById("txtoprograma_"+(x+1)).checked = true;
               }
         }
         else{
             for (var x = 0; x < canti3.length; x++) {
                   document.getElementById("txtoprograma_"+(x+1)).checked = false;
               }
         }
    };

    function carga_programa(){
        
        var varpcrc = document.getElementById("requester").value;
  
        var varPartT3 = document.getElementById("labeltodos");
        varPartT3.style.display = 'inline';
        var varPartT4 = document.getElementById("todos");
        varPartT4.style.display = 'inline';  
        var varPartT5 = document.getElementById("todos2");
        varPartT5.style.display = 'inline';  
        var varPartT6 = document.getElementById("todos3");
        varPartT6.style.display = 'inline';  
        var varPartT7 = document.getElementById("labeltodos2");
        varPartT7.style.display = 'inline';
        var varPartT8 = document.getElementById("labeltodos3");
        varPartT8.style.display = 'inline';

        $.ajax({
            method: "get",

            url: "extensiones",
            data : {
                cod_pcrc : varpcrc,          
            },
            success : function(response){ 
                var Rta = $.parseJSON(response);   
                // console.log(Rta);                      
                    
                document.getElementById("requester1").innerHTML = "";
                    
                for (var i = 0; i < Rta.length ; i++) {                               
                                    
                    var lista = document.getElementById("requester1");                             
                    var checkbox = document.createElement('input');
                    checkbox.setAttribute("type", "checkbox");
                    checkbox.setAttribute("class", "prog1");
                    checkbox.setAttribute("name", "txtprograma_"+(i+1));
                    checkbox.setAttribute("id", "txtprograma_"+(i+1));
                    checkbox.setAttribute("value", Rta[i].bolsitaservicio + "," + Rta[i].extension);
                                   
                    var varparams = Rta[i].tipoparametro;                                                
                                    
                    var label = document.createElement('label'); 
                                    
                    label.htmlFor = "txtprograma_"+(i+1); 

                    label.appendChild(document.createTextNode("\u00a0" + "\u00a0" + Rta[i].bolsitaservicio + " - " + Rta[i].extension));

                    var salto = document.createElement('br'); 
                    lista.appendChild(checkbox); 
                    lista.appendChild(label);
                    lista.appendChild(salto);
                }
            }                               
        });

        $.ajax({
            method: "get",

            url: "calidadentto",
            data : {
                cod_pcrc : varpcrc,          
            },
            success : function(response){ 
                var Rta = $.parseJSON(response);   
                // console.log(Rta);                      
                    
                document.getElementById("requester2").innerHTML = "";
                    
                for (var i = 0; i < Rta.length ; i++) {                               
                                    
                    var lista = document.getElementById("requester2");                             
                    var checkbox = document.createElement('input');
                    checkbox.setAttribute("type", "checkbox");
                    checkbox.setAttribute("class", "prog2");
                    checkbox.setAttribute("name", "txtcprograma_"+(i+1));
                    checkbox.setAttribute("id", "txtcprograma_"+(i+1));
                    checkbox.setAttribute("value", Rta[i].bolsitaservicio + "," + Rta[i].extension);
                                   
                    var varparams = Rta[i].tipoparametro;                                                
                                    
                    var label = document.createElement('label'); 
                                    
                    label.htmlFor = "txtcprograma_"+(i+1); 

                    label.appendChild(document.createTextNode("\u00a0" + "\u00a0" + Rta[i].bolsitaservicio + " - " + Rta[i].extension));

                    var salto = document.createElement('br'); 
                    lista.appendChild(checkbox); 
                    lista.appendChild(label);
                    lista.appendChild(salto);
                }
            }                               
        });

        $.ajax({
            method: "get",

            url: "ojts",
            data : {
                cod_pcrc : varpcrc,          
            },
            success : function(response){ 
                var Rta = $.parseJSON(response);   
                // console.log(Rta);                      
                    
                document.getElementById("requester3").innerHTML = "";
                    
                for (var i = 0; i < Rta.length ; i++) {                               
                                    
                    var lista = document.getElementById("requester3");                             
                    var checkbox = document.createElement('input');
                    checkbox.setAttribute("type", "checkbox");
                    checkbox.setAttribute("class", "prog3");
                    checkbox.setAttribute("name", "txtoprograma_"+(i+1));
                    checkbox.setAttribute("id", "txtoprograma_"+(i+1));
                    checkbox.setAttribute("value", Rta[i].bolsitaservicio + "," + Rta[i].extension);
                                   
                    var varparams = Rta[i].tipoparametro;                                                
                                    
                    var label = document.createElement('label'); 
                                    
                    label.htmlFor = "txtoprograma_"+(i+1); 

                    label.appendChild(document.createTextNode("\u00a0" + "\u00a0" + Rta[i].bolsitaservicio + " - " + Rta[i].extension));

                    var salto = document.createElement('br'); 
                    lista.appendChild(checkbox); 
                    lista.appendChild(label);
                    lista.appendChild(salto);
                }
            }                               
        });

    };

    function verProcesos(){
        var variddashboard = document.getElementById("iddashboard").value;
        var varParametros = document.getElementById("idParametros");
        var varCalidad = document.getElementById("idCalidad");
        var varOjt = document.getElementById("idOjt");
        
        if (variddashboard == "0") {
            varParametros.style.display = 'inline';
            varCalidad.style.display = 'none';
            varOjt.style.display = 'none';
        }
        
        if (variddashboard == "1") {
            varParametros.style.display = 'none';
            varCalidad.style.display = 'inline';
            varOjt.style.display = 'none';
        }

        if (variddashboard == "2") {
            varParametros.style.display = 'none';
            varCalidad.style.display = 'none';
            varOjt.style.display = 'inline';
        }

    };

    function verifica(){        
        var vartxtidclientes = document.getElementById("txtidclientes").value;
        var varrequester = document.getElementById("requester").value;
        var variddashboard = document.getElementById("iddashboard").value;
        var varfechacreacion = document.getElementById("speechparametrizar-fechacreacion").value;

        var varcapaIdFiltros =document.getElementById("capaIdFiltros");
        var varidCapa = document.getElementById("idCapa");

        if (vartxtidclientes == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar un servicio.","warning");
            return;
        }else{
            if (varrequester == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe seleccionar un Programa/Pcrc.","warning");
                return;
            }else{
                if (variddashboard == "") {
                    event.preventDefault();
                    swal.fire("!!! Advertencia !!!","Debe seleccionar un Proceso.","warning");
                    return;
                }else{
                    if (varfechacreacion == "") {
                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","Debe seleccionar un rango de fecha.","warning");
                        return;
                    }else{
                        varcapaIdFiltros.style.display = 'none';
                        varidCapa.style.display = 'inline';
                    }
                }
            }
        }
        
    };
</script>