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


$this->title = 'Dashboard -- VOC --';

$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Dashboard Voz del Cliente';

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
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

?>
<?php
  if ($txtvarNew == null) {
?>
<style>
.loader {
  border: 16px solid #f3f3f3;
  border-radius: 50%;
  border-top: 16px solid #3498db;
  width: 120px;
  height: 120px;
  -webkit-animation: spin 2s linear infinite; /* Safari */
  animation: spin 2s linear infinite;
}

/* Safari */
@-webkit-keyframes spin {
  0% { -webkit-transform: rotate(0deg); }
  100% { -webkit-transform: rotate(360deg); }
}

@keyframes spin {
  0% { transform: rotate(0deg); }
  100% { transform: rotate(360deg); }
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
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        
      </div>
    </div>
  </div>
</header>
<br>
   
       <?= Html::encode($this->title) ?>   
<br>
<div class="formularios-form" id="idCapa"  style="display: none; text-align: center;">
  <table style="margin: auto;" style="width: 700px;" >
  <caption>Tabla datos</caption>
    <thead>
      <tr>
        <th scope="col" class="text-center"><div class="loader"></div></th>
        <th scope="col" ><?= Yii::t('app', '') ?></th>
        <th scope="col" class="text-justify"><h4><?= Yii::t('app', 'Actualmente CXM esta procesando la informacion de los filtros para el Dashboard Speech...') ?></h4></th>
      </tr>            
    </thead>
  </table>
</div>
<div class="formularios-form" id="idCapa0" style="display: inline">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
        <div class="row" style="text-align: center;">
            &nbsp; 
          <?= Html::a('Recargar',  ['index'], ['class' => 'btn btn-success',
                                'id'=>'recarga1',                                
                                'style' => 'background-color: #707372',
                                'style' => 'height: 31px',
                                'style' => 'display: none',
                                'data-toggle' => 'tooltip',
                                'title' => 'Recargar'])
          ?>
            <div onclick="carga_programa();" class="btn btn-success"  style="display:inline; background-color: #337ab7; margin: 3px; height: 34px;" method='post' id="botones2" >
                Buscar Programas 
            </div>
            
            <div id="idBlock1" style="display: none">
                <?= Html::submitButton(Yii::t('app', 'Buscar DashBoard'),
                    ['class' => $model3->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'title' => 'Buscar DashBoard', 'style' => 'height: 31px',
                'id'=>'modalButton1',
                'onclick' => 'verifica();']) 
          ?>
            </div>
             <?php
            if ($sessiones == '2953' || $sessiones == '7' || $sessiones == '3205' || $sessiones == '1525') {
                
             ?>
             <?= Html::button('Importar Llamadas', ['value' => url::to('importarexcel2'), 'class' => 'btn btn-success', 'id'=>'modalButton3',
                                  'data-toggle' => 'tooltip',
                                  'title' => 'Importar Llamadas',  'style' => 'height: 31px; background-color: #337ab7']) 
            ?> 

            <?php
              Modal::begin([
                    'header' => '<h4>Importar Archivo Excel </h4>',
                    'id' => 'modal3',
                    //'size' => 'modal-lg',
                  ]);

              echo "<div id='modalContent3'></div>";
                                        
              Modal::end(); 
            ?>  
            <?php } ?>
            <div id="idBlock2" style="display: inline">
                <?= Html::a('Configurar Categorias',  ['categoriasconfig'], ['class' => 'btn btn-success',
                                    'style' => 'background-color: #337ab7',
                                    'style' => 'height: 31px',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Agregar Valorado']) 
                ?>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-6">                        
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
                                )->label('Cliente Speech'); 
                ?>
                <br>
                <?=
                    $form->field($model3, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->checkboxList(
                        [],
                        [
                            "id" =>"requester",
                            'item'=>function ($index, $label, $name, $checked, $value)
                            {
                                return '<div class="col-md-12">
                                            <input type="checkbox" />'.$label.'
                                        </div>';
                            }

                      ])->label('Centro de Costos');
                ?>
            </div>
            <div class="col-md-6">
                <?=
                    $form->field($model3, 'fechacreacion', [
                        'labelOptions' => ['class' => 'col-md-12'],
                        'template' => '<div class="col-md-3">{label}</div>'
                        . '<div class="col-md-9"><div class="input-group">'
                        . '<span class="input-group-addon" id="basic-addon1">'
                        . '<i class="glyphicon glyphicon-calendar"></i>'
                        . '</span>{input}</div>{error}{hint}</div>',
                        'inputOptions' => ['aria-describedby' => 'basic-addon1'],
                        'options' => ['class' => 'drp-container form-group']
                    ])->label('Rango de Fecha')->widget(DateRangePicker::classname(), [
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
                <label id="labeltodos" style="float: right; padding-right: 360px; display: none;">&nbsp; Todos...</label>
                <input type="checkbox" value="todos" id="todos"  onclick="selectodo()" style="float: right; padding-right: 390px; display: none;" />
                <br>
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

                      ])->label('Parametros ');
                ?>
                
    <?= $form->field($model3, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 350, 'class' => 'hidden',  'id'=>'txtIdCod_pcrc']) ?>

    <?= $form->field($model3, 'pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 350, 'class' => 'hidden',  'id'=>'txtIdProgramas']) ?>

            </div>
        </div>
        <br>
       
    <?php ActiveForm::end(); ?>
</div>
<?php
  }
?>

<?php
  if ($txtvarNew == 1) {
?>
    <div class="col-md-offset-2 col-sm-8 panel panel-warning">
      <div style="text-align: center;" class="panel-body">
        <p><strong>Importante: </b> No se encontraron llamadas para la busqueda anterior, por favor vuelva a realizar una nueva consulta. </strong>
        <div class="row" style="text-align: center;">
        <?= Html::a('Nueva consulta',  ['index'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Nueva consulta']) 
        ?>
        </div>
      </div>
    </div>
<?php
  }
?>

<script type="text/javascript">
    function verifica(){
        var varCliente = document.getElementById("speechcategorias-clientecategoria").value;
        var varFecha = document.getElementById("speechcategorias-fechacreacion").value;   
        //var varidtitle = document.getElementById("idTitle");
        var varidCapa = document.getElementById("idCapa");
        var varidCapa0 = document.getElementById("idCapa0");
        
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
              //varidtitle.style.display = 'none';
              varidCapa.style.display = 'inline';
              varidCapa0.style.display = 'none';
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

     if (varpcrc == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe seleccionar un centro de costos.","warning");
            return;
        }
     
    var varPartT = document.getElementById("idBlock1");
      varPartT.style.display = 'inline';
      varPartT.style.height = '31px';
    var varPartT1 = document.getElementById("recarga1");
      varPartT1.style.display = 'inline';
      varPartT1.style.height = '31px';
      varPartT1.style.backgroundColor = '#337ab7';
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
                               

                              /*  checkbox.type = "checkbox"; 
                                checkbox.name = "txtprograma_"+(i+1); 
                                checkbox.value = Rta[i].programacategoria; 
                                checkbox.id = "txtprograma_"+(i+1);*/                                  
                                
                                var label = document.createElement('label'); 
                                
                                label.htmlFor = "txtprograma_"+(i+1);                                   
                                
                                label.appendChild(document.createTextNode("\u00a0" + "\u00a0" + Rta[i].programacategoria + " - " + Rta[i].rn));
                               
                               // lista.appendChild(checkbox);
                                var salto = document.createElement('br'); 
                                lista.appendChild(checkbox); 
                                lista.appendChild(label);
                                lista.appendChild(salto);
                            }
                           // lista.appendChild(div);
                        }                               
      });

    }
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

    }
</script>