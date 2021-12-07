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

    $FechaActual = date("Y-m-d");
    $MesAnterior = date("m") - 1;

    $varextensiones = ['3' => 'Todos', '0' => 'Procesos', '1' => 'Calidad de entrenamiento', '2' => 'Ojt'];

?>
<style>
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
<script>
    $(document).ready(function(){
        $.fn.snow();
    });
</script>
<script src="../../js_extensions/mijs.js"> </script>
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
<?php
  if ($txtvarNew == null) {
?>
<br>
<br>
<div class="capaLoader" id="idCapa" style="display: none;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">
        <table style="margin: auto;">
        <caption>...</caption>
          <thead>
            <tr>
              <th scope="col" class="text-center"><div class="lds-ring"><div></div><div></div><div></div><div></div></div></th>
              <th scope="col"><?= Yii::t('app', '') ?></th>
              <th scope="col" class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informaci&oacute;n de los filtros para el Dashboard Escuchar + en la versi&oacute;n 2.0...') ?></h4></th>
            </tr>            
          </thead>
        </table>
      </div>
    </div>
  </div>
  <br>
  <hr>
</div>

<div class="capaForm" id="idCapa0" style="display: inline;">
  <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>

  <!-- Aqui va lacapa de los botones -->
  <div class="capabtn1" style="display: inline;">
    <div class="row">
      <div class="col-md-12">
       

          <div class="row">

            <div id="recarga1" style="display: none;" class="col-md-6">
              <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-spinner" style="font-size: 20px; color: #FFC72C;"></em> Nueva b&uacute;squeda </label>
                <?= Html::a('Nuevo',  ['index'], ['class' => 'btn btn-success',
                                'style' => 'display: inline;margin: 3px;height: 34px;display: inline;height: 34px;background-color: #707372;',                            
                                'data-toggle' => 'tooltip',
                                'title' => 'Nuevo'])
                ?>
              </div>
            </div>


            <div id="botones2" style="display:inline;" class="col-md-6">
              <div class="card1 mb">       
                <label style="font-size: 15px;"><em class="fas fa-search" style="font-size: 20px; color: #FFC72C;"></em> Buscar programa </label>        
                <div onclick="carga_programa();" class="btn btn-success"  style="display:inline;  margin: 3px; height: 34px;" method='post'  >
                    Buscar 
                </div>
              </div>
            </div>     

            <div id="idBlock1" style="display: none;" class="col-md-6">
              <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-chart-line" style="font-size: 20px; color: #FFC72C;"></em> Buscar dashboard </label>
                  <?= Html::submitButton(Yii::t('app', 'Buscar'),
                      ['class' => $model3->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                          'data-toggle' => 'tooltip',
                          'title' => 'Buscar DashBoard',
                          'style' => 'display: inline;margin: 3px;height: 34px;',
                          'id'=>'modalButton1',
                          'onclick' => 'verifica();']) 
                  ?>
              </div>
            </div> 
            

        
      </div>
    </div>
  </div>

  <hr>

  <!-- Aqui va la capa de los selects -->
  <div class="capabtn2" style="display: inline;">
    <div class="row">
      <div class="col-md-12">
        <div class="card1 mb">

          <div class="row">
            <div class="col-md-4">
              <label><em class="fas fa-check" style="font-size: 20px; color: #559FFF;"></em> Seleccionar cliente: </label>
              <?=  $form->field($model3, 'clientecategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosVolumendirector::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                        [
                                            'prompt'=>'Seleccione Cliente Speech...',
                                            'onchange' => '
                                                $.post(
                                                    "' . Url::toRoute('dashboardspeech/listarpcrcindex') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#requester").html(res);
                                                    }
                                                );
                                            ',

                                        ]
                                )->label(''); 
                ?>
                <br>
                <label ><em class="fas fa-check-square" style="font-size: 20px; color: #559FFF;"></em> Seleccionar centro de costos: </label>
                <?=
                    $form->field($model3, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->checkboxList(
                        [],
                        [
                            "id" =>"requester",
                            'item'=>function ($index, $label, $name, $checked, $value)
                            {
                                return '<div class="col-md-12">
                                            <input type="checkbox"/>'.$label.'
                                        </div>';
                            }

                      ])->label('');
                ?>
            </div>

            <div class="col-md-4">
              <label><em class="fas fa-hand-pointer" style="font-size: 20px; color: #559FFF;"></em> Seleccionar proceso: </label>
                <?= $form->field($model3, 'dashboard', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($varextensiones, ['prompt' => 'Seleccionar procesos...', 'id'=>'iddashboard']) ?>
                <br>
                <label ><em class="fas fa-paperclip" style="font-size: 20px; color: #559FFF;"></em> Seleccionar par&aacute;metros: </label>
                <label id="labeltodos" style="display: none;">
                <input type="checkbox" value="todos" id="todos"  onclick="selectodo()" style="padding-right: 390px; display: none;" /> Todos</label>
                <?=
                    $form->field($model3, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-8'], 'template' => $template])->checkboxList(
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
                 <?= $form->field($model3, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 350, 'class' => 'hidden',  'id'=>'txtIdCod_pcrc']) ?>

                <?= $form->field($model3, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 350, 'class' => 'hidden',  'id'=>'txtIdProgramas']) ?>
            </div>

            
            <div class="col-md-4">
              <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #559FFF;"></em> Seleccionar rango de fecha: </label>
              <?=
                    $form->field($model3, 'fechacreacion', [
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

        </div>
      </div>
    </div>
  </div>

  <hr>

  <?php ActiveForm::end(); ?>
</div>
<?php
  }
?>

<?php
  if ($txtvarNew == 1) {
?>
<div class="capaMensaje"  style="display: inline;">
  <div class="row">
    <div class="col-md-12">
      <div class="card1 mb">

        <div class="panel-body">
          <p class="text-center"><strong>Importante: </strong> No se encontraron llamadas para la busqueda anterior, por favor vuelva a realizar una nueva consulta. </p>
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
<hr>
<?php
  }
?>

<script type="text/javascript">
    function soloadmin(){
      event.preventDefault();
      swal.fire("!!! Advertencia !!!","Acci�n no permitida, acceso para usuarios especificos.","warning");
      return;
    };

    function verifica(){
        var varCliente = document.getElementById("speechcategorias-clientecategoria").value;
        var varFecha = document.getElementById("speechcategorias-fechacreacion").value;   
        // var varidtitle = document.getElementById("idTitle");
        var varidCapa = document.getElementById("idCapa");
        var varidCapa0 = document.getElementById("idCapa0");
        var variddashboard = document.getElementById("iddashboard").value;
        
        var varCheckes = document.getElementById("requester2").querySelectorAll(".prog1");
        var varChekeados = null;
        var varValueCheck = [];
        for (var i = 0; i < varCheckes.length; i++) {
            varChekeados = document.getElementById("txtprograma_"+(i+1)).checked;
            if (varChekeados == true) {
                varValueCheck.push(document.getElementById("txtprograma_"+(i+1)).value);
            }
        }
        // console.log(varValueCheck);
        document.getElementById("txtIdProgramas").value = varValueCheck;

        if (varCliente == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente.","warning");
            return;
        }else{
            if (varFecha == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe de seleccionar un rango fecha.","warning");
                return;
            }else{
              if (variddashboard == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe de seleccionar un proceso.","warning");
                return;
              }else{
                // varidtitle.style.display = 'none';
                varidCapa.style.display = 'inline';
                varidCapa0.style.display = 'none';
              }              
            }
        }
    };

    function selectodo(){

         var canti = document.getElementById("requester2").querySelectorAll(".prog1");
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

    function carga_programa(){
     //var varCliente = document.getElementById("procesosvolumendirector-id_dp_clientes").value;
     //var varpcrc = document.getElementById("lista_5").value;
     
         
     var cant = document.getElementById("requester").querySelectorAll(".listach");
       
     var varpcrc = "";
     for (var x = 0; x < cant.length; x++) {
         if(document.getElementById("lista_"+(x+1)).checked){
            varpcrc = varpcrc + "'" + document.getElementById("lista_"+(x+1)).value + "'" + ",";
         }

     }
     varpcrc = varpcrc.substring(0,varpcrc.length - 2);
     varpcrc = varpcrc.substring(1);

     document.getElementById("txtIdCod_pcrc").value = varpcrc;
     var variddashboard = document.getElementById("iddashboard").value;

     if (varpcrc == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar un centro de costos.","warning");
            return;
        }else{
          if (variddashboard == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar un proceso.","warning");
            return;
          }
        }
     
    var varPartT = document.getElementById("idBlock1");
      varPartT.style.display = 'inline';
    var varPartT1 = document.getElementById("recarga1");
      varPartT1.style.display = 'inline';
    var varPartT2 = document.getElementById("botones2");
      varPartT2.style.display = 'none';      
    var varPartT3 = document.getElementById("labeltodos");
      varPartT3.style.display = 'inline';
    var varPartT4 = document.getElementById("todos");
     varPartT4.style.display = 'inline';  

     $.ajax({
              method: "post",

              url: "listarprogramaindex",
              data : {
                cod_pcrc : varpcrc,     
                txtvariddashboard : variddashboard,          
              },
               success : function(response){ 
                          var Rta = $.parseJSON(response);                         
                       //   var Rta =   JSON.parse(response);    
                          console.log(Rta);
                          document.getElementById("requester2").innerHTML = "";
                         
                          //var div = document.createElement('input');
                          for (var i = 0; i < Rta.length ; i++) {                               
                                
                                var lista = document.getElementById("requester2");                             
                                var checkbox = document.createElement('input');
                                checkbox.setAttribute("type", "checkbox");
                                checkbox.setAttribute("class", "prog1");
                                checkbox.setAttribute("name", "txtprograma_"+(i+1));
                                checkbox.setAttribute("id", "txtprograma_"+(i+1));
                                checkbox.setAttribute("value", Rta[i].programacategoria + "," + Rta[i].rn);
                               
                                var varparams = Rta[i].tipoparametro;
                                // console.log(varparams);
                              /*  checkbox.type = "checkbox"; 
                                checkbox.name = "txtprograma_"+(i+1); 
                                checkbox.value = Rta[i].programacategoria; 
                                checkbox.id = "txtprograma_"+(i+1);*/                                  
                                
                                var label = document.createElement('label'); 
                                
                                label.htmlFor = "txtprograma_"+(i+1);                                   
                                

                                if (varparams == 1) {
                                  label.appendChild(document.createTextNode("\u00a0" + "\u00a0" + Rta[i].programacategoria + " - " + Rta[i].rn + ' - Calidad del entrenamiento'));
                                }else{
                                  if (varparams == 2) {
                                    label.appendChild(document.createTextNode("\u00a0" + "\u00a0" + Rta[i].programacategoria + " - " + Rta[i].rn + ' - Ojt'));
                                  }else{
                                    label.appendChild(document.createTextNode("\u00a0" + "\u00a0" + Rta[i].programacategoria + " - " + Rta[i].rn));
                                  }
                                }

                                
                               
                               // lista.appendChild(checkbox);
                                var salto = document.createElement('br'); 
                                lista.appendChild(checkbox); 
                                lista.appendChild(label);
                                lista.appendChild(salto);
                            }
                           // lista.appendChild(div);
                        }                               
      });

    };

    function carga_histo_usua(){
        var varCliente = document.getElementById("procesosvolumendirector-id_dp_clientes").value;
        var varFecha = document.getElementById("dashboardcategorias-fechacreacion").value;
        var cant = document.getElementById("requester").querySelectorAll(".listach");
        var valor_chek = "";
     
     for (var x = 0; x < cant.length; x++) {
         valor_chek = valor_chek + document.getElementById("lista_"+(x+1)).value + ", ";
         if(document.getElementById("lista_"+(x+1)).checked){
            varpcrc = varpcrc + "'" + document.getElementById("lista_"+(x+1)).value + "'" + ",";
         }

     }

    };
</script>