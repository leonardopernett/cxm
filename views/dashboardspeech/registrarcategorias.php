<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\grid\GridView;
use yii\helpers\Url;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use app\models\Dashboardcategorias;


$this->title = 'DashBoard -- Voice Of Customer --';
$this->params['breadcrumbs'][] = $this->title;

$this->title = 'Registro de Categorias DashBoard Speech';

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

    //$txtnombre = Yii::$app->db->createCommand("select director_programa from tbl_procesos_directores where iddirectores = '$txtRadicado' and anulado = 0")->queryScalar();
?>
&nbsp; 
  <?= Html::a('Regresar',  ['categoriasconfig'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
  ?>
<br>
    <div class="page-header" >
        <h3><center><?= Html::encode($this->title) ?></center></h3>
    </div> 
<br>
<div class="formularios-form" >
    <div class="row">
        <div  class="col-sm-12">
            <div class="well well-sm">
                <?=
                    Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                "class" => "glyphicon glyphicon-chevron-down",
                            ]) . " " . Html::encode('INFORMACION GENERAL'), "javascript:void(0)"
                            , ["class" => "openVistas", "id" => "graficar", "style" => "text-transform: uppercase"])
                ?> 
            </div> 
        </div>

        <div id="dtbloque" class="form-group col-sm-12" style="display: inline;">
            <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                <div class="row">
                    <div class="col-md-6">                        
                        <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosVolumendirector::find()->distinct()->where("anulado = 0")->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                        [
                                            'prompt'=>'Seleccione Cliente Speech...',
                                            'onchange' => '
                                                $.post(
                                                    "' . Url::toRoute('dashboardspeech/listarpcrc') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#requester").html(res);
                                                    }
                                                );
                                            ',

                                        ]
                                )->label('Cliente Speech'); 
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                        [],
                                        [
                                            'prompt' => 'Seleccione Centro de Costos...',
                                            'id' => 'requester',
                                            'onclick' => 'varDesbloqueo();'
                                        ]
                                    )->label('Centro de Costos');
                        ?>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
            <br>
        </div>
    </div>
    <div class="row">
        <div  class="col-sm-12">
            <div class="well well-sm">
                <?=
                    Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                "class" => "glyphicon glyphicon-chevron-down",
                            ]) . " " . Html::encode('REGISTRO MASIVO DE CATEGORIAS'), "javascript:void(0)"
                            , ["class" => "openVistas", "id" => "graficar0", "style" => "text-transform: uppercase"])
                ?> 
            </div> 
        </div>

        <div id="dtbloque0" class="form-group col-sm-12" style="display: none;">
            <?= Html::button('Importar Categorias', ['value' => url::to(['exportarcategorias']), 'class' => 'btn btn-success',  'id'=>'modalButton1',
                        'data-toggle' => 'tooltip',
                        'title' => 'Importar Categorias', 'style' => 'background-color: #337ab7']) 
            ?> 

            <?php
                Modal::begin([
                  'header' => '<h4>Subir archivos con las categorias...</h4>',
                  'id' => 'modal1',
                  //'size' => 'modal-lg',
                ]);

                echo "<div id='modalContent1'></div>";
                                              
                Modal::end(); 
            ?>
            <br>
            <br>
        </div>
    </div>
    <div class="row">
        <div  class="col-sm-12">
            <div class="well well-sm">
                <?=
                    Html::a(Html::tag("span", "", ["aria-hidden" => "true",
                                "class" => "glyphicon glyphicon-chevron-down",
                            ]) . " " . Html::encode('REGISTRO POR CATEGORIAS'), "javascript:void(0)"
                            , ["class" => "openVistas", "id" => "graficar1", "style" => "text-transform: uppercase"])
                ?> 
            </div> 
        </div>

        <div id="dtbloque1" class="form-group col-sm-12" style="display: none;">
            <div id="idCapaBloque0" class="CapaBloque0" style="display: inline;">
                <label>Debes seleccionar el centro de costos para seguir con el proceso.</label>
            </div>
            <div id="idCapaBloque1" class="CapaBloque1" style="display: none;">
                <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model3, 'rn', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200,  'id'=>'idRn', 'readonly' => 'readonly'])->label('Regla de Negocio') ?>

                            <?= $form->field($model3, 'usua_usuario', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200,  'id'=>'idRed', 'readonly' => 'readonly'])->label('Usuario de Red') ?>

                            <?= $form->field($model3, 'idcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'type' => 'number', 'id'=>'txtIdCategorias',  'onkeypress' => 'return valida(event)'])->label('IDCategoria en Speech') ?> 

                            <?php $var2 = ['1' => 'DashBoard Speech', '2' => 'DashBoard IDA', '3' => 'Ambos DashBoard']; ?>

                            <?= $form->field($model3, 'dashboard', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var2, ['prompt' => 'Seleccione...', 'id'=>"id_usabilidad"])->label('Usar en Dashboard') ?>

                            <div class="CapaSubCuatro" style="display: none" id="CapaUno4">
                            <?php $var4 = ['1' => 'Positivo', '2' => 'Negativo']; ?>

                            <?= $form->field($model3, 'orientacionform', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var4, ['prompt' => 'Seleccione...', 'id'=>"id_form"])->label('Presentar en Speech - QA') ?>
                            </div>

                            <div class="CapaSubCero" style="display: none" id="CapaUno0">
                                <?php $var5 = ['1' => 'Estrategico', '2' => 'Desempeño']; ?>

                                <?= $form->field($model3, 'tipoparametro', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var5, ['prompt' => 'Seleccione...', 'id'=>"id_parametro"])->label('Tipo de parametro') ?> 
                            </div>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model3, 'extension', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200,  'id'=>'idext', 'readonly' => 'readonly'])->label('Extensión') ?>

                            <?php $var = ['0' => 'Programa', '1' => 'Indicador', '2' => 'Variable', '3' => 'Motivos de contacto', '4' => 'Detalle motivo contacto']; ?>

                            <?= $form->field($model3, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'txtIdNombre'])->label('Nombre Categoria') ?> 

                            <?= $form->field($model3, 'tipocategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var, ['prompt' => 'Seleccione...', 'id'=>"id_categorias", 'onclick'=>'varDimension();'])->label('Tipo Categoria') ?>

                            <div class="CapaSubCinco" style="display: none" id="CapaUno5">
                            <?php $var3 = ['1' => 'Positivo', '2' => 'Negativo']; ?>

                            <?= $form->field($model3, 'orientacionsmart', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var3, ['prompt' => 'Seleccione...', 'id'=>"id_smart"])->label('Orientación en Smart') ?>  
                            </div>

                            <div class="CapaSubUno" style="display: none" id="CapaUno1">
                                <?= $form->field($model3, 'tipoindicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'id'=>'txtIdIndicador'])->label('Tipo Parametro') ?>
                            </div>   
                            <div class="CapaSubDos" style="display: none" id="CapaUno2">
                                <select id="idTipoIndi" class ='form-control col-md-10'>
                                    <option value="" disabled selected>Seleccionar Parametro...</option>
                                </select> 
                            </div>    

                <?= $form->field($model3, 'programacategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'id'=>'txtIdprograma'])->label('Programa Categoria') ?>                            

                                <?= $form->field($model3, 'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'class' => 'hidden', 'id'=>'idPcrc']) ?>
                              
                        </div>
                    </div>
                    <div id="idbtnG" class="form-group" align="center"  >
                        <?= Html::submitButton(Yii::t('app', 'Guardar Categoria'),
                                ['class' => $model3->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'onclick' => 'varVerificar();',
                                    'title' => 'Guardar Categoria']) 
                        ?> 
                    </div>
                <?php ActiveForm::end() ?>
            </div>
            <br>
        </div>
    </div>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        $("#graficar").click(function () {
            $("#dtbloque").toggle("slow");
        }); 
        $("#graficar0").click(function () {
            $("#dtbloque0").toggle("slow");
        });   
        $("#graficar1").click(function () {
            $("#dtbloque1").toggle("slow");
        });      
    });

    function varDesbloqueo(){
        var varCC = document.getElementById("requester").value;
        var varBloque0 = document.getElementById("idCapaBloque0");
        var varBloque1 = document.getElementById("idCapaBloque1");
        var varPcrc = document.getElementById("idPcrc");
        var varidRn = document.getElementById("idRn");
        var varidext = document.getElementById("idext");
        var varidRed = document.getElementById("idRed");

        var varString = "";
        var varString1 = "";
        var varString2 = "";

        if (varCC == "" || varCC == "No hay datos") {
            varBloque0.style.display = 'inline';
            varBloque1.style.display = 'none';
        }else{
            varBloque0.style.display = 'none';
            varBloque1.style.display = 'inline';
            varPcrc.value = varCC;

            $.ajax({
                method: "post",

                url: "listaracciones",
                data : {
                    txtCC : varCC,
                },
                success : function(response){ 
                    var Rta =   JSON.parse(response);    
                    console.log(Rta);
                    for (var i = 0; i < Rta[0].length; i++) {
                        varString += Rta[0][i]["rn"]+"  ";                                                          
                    }
                    varString = varString.slice(0,-2);
                    varidRn.value = varString;

                    for (var i = 0; i < Rta[1].length; i++) {
                        varString1 += Rta[1][i]["ext"]+"  ";                                                          
                    }
                    varString1 = varString1.slice(0,-2);
                    varidext.value = varString1;

                    for (var i = 0; i < Rta[2].length; i++) {
                        varString2 += Rta[2][i]["usuared"]+"  ";                                                          
                    }
                    varString2 = varString2.slice(0,-2);
                    varidRed.value = varString2;
                    
                }
            }); 
        }
    };

    function varDimension(){
        var varid_categorias = document.getElementById("id_categorias").value;
        var varrequester = document.getElementById("requester").value;
        var varCapaUno1 = document.getElementById("CapaUno1");
        var varCapaUno2 = document.getElementById("CapaUno2");
        var vartxtIdIndicador = document.getElementById("txtIdIndicador");
        var varCapaUno0 = document.getElementById("CapaUno0");
        var vartxtIdNombre = document.getElementById("txtIdNombre").value;
        var vartxtIdprograma = document.getElementById("txtIdprograma");
        var varCapaUno4 = document.getElementById("CapaUno4");
        var varCapaUno5 = document.getElementById("CapaUno5");

        if (varid_categorias == '0') {
            vartxtIdIndicador.value = 'Programa';
            varCapaUno1.style.display = 'inline';
            varCapaUno2.style.display = 'none';
            varCapaUno0.style.display = 'none';            
            varCapaUno4.style.display = 'none';
            varCapaUno5.style.display = 'none';
            vartxtIdprograma.value = vartxtIdNombre;
        }else{
            if (varid_categorias == '1') {
                vartxtIdIndicador.value = 'Indicador';
                varCapaUno1.style.display = 'inline';
                varCapaUno2.style.display = 'none';
                varCapaUno0.style.display = 'inline';
                varCapaUno4.style.display = 'inline';
                varCapaUno5.style.display = 'inline';
                $.ajax({
                    method: "post",
                    url: "listaprograma",
                    data : {
                                txtCC : varrequester,
                            },
                    success : function(response){ 
                                            var Rta =   JSON.parse(response);    
                                            console.log(Rta);
                                            vartxtIdprograma.value = Rta;
                                          }
                });
            }else{
                if (varid_categorias == '3') {
                    vartxtIdIndicador.value = 'Indicador';
                    varCapaUno1.style.display = 'inline';
                    varCapaUno2.style.display = 'none';
                    varCapaUno0.style.display = 'none';
                    varCapaUno4.style.display = 'inline';
                    varCapaUno5.style.display = 'inline';
                    $.ajax({
                        method: "post",

                        url: "listaprograma",
                        data : {
                                    txtCC : varrequester,
                                },
                        success : function(response){ 
                                                var Rta =   JSON.parse(response);    
                                                console.log(Rta);
                                                vartxtIdprograma.value = Rta;
                                              }
                    });
                }else{
                    if (varid_categorias == '4') {
                        vartxtIdIndicador.value = 'Motivos de Contacto';
                        varCapaUno1.style.display = 'inline';
                        varCapaUno2.style.display = 'none';
                        varCapaUno0.style.display = 'none';            
                        varCapaUno4.style.display = 'none';
                        varCapaUno5.style.display = 'none';
                        $.ajax({
                            method: "post",

                            url: "listaprograma",
                            data : {
                                        txtCC : varrequester,
                                    },
                            success : function(response){ 
                                                    var Rta =   JSON.parse(response);    
                                                    console.log(Rta);
                                                    vartxtIdprograma.value = Rta;
                                                  }
                        });
                    }else{
                        if (varid_categorias == '2') {
                            $.ajax({
                                method: "post",

                                url: "listaprograma",
                                data : {
                                            txtCC : varrequester,
                                        },
                                success : function(response){ 
                                                        var Rta =   JSON.parse(response);    
                                                        console.log(Rta);
                                                        vartxtIdprograma.value = Rta;
                                                      }
                            });
                            varCapaUno1.style.display = 'none';
                            varCapaUno2.style.display = 'inline';
                            varCapaUno0.style.display = 'none';            
                            varCapaUno4.style.display = 'none';
                            varCapaUno5.style.display = 'none';

                            $.ajax({
                                  method: "post",

                                  url: "listacategorias",
                                  data : {
                                    txtCategoria : varid_categorias,
                                    txtCC : varrequester,
                                  },
                                  success : function(response){ 
                                              var Rta =   JSON.parse(response);    
                                              console.log(Rta);
                                              document.getElementById("idTipoIndi").innerHTML = "";
                                              for (var i = 0; i < Rta.length; i++) {
                                                  var node = document.createElement("OPTION");
                                                  node.setAttribute("value", Rta[i].nombre);
                                                  var textnode = document.createTextNode(Rta[i].nombre);
                                                  node.appendChild(textnode);
                                                  document.getElementById("idTipoIndi").appendChild(node);
                                              }
                                          }
                              }); 
                        }
                    }
                }
            }
        }
        
    };

    function varVerificar(){
        var varid_categorias = document.getElementById("id_categorias").value;
        var vartxtIdCategorias = document.getElementById("txtIdCategorias").value;
        var vartxtIdNombre = document.getElementById("txtIdNombre").value;
        var varid_usabilidad = document.getElementById("id_usabilidad").value;
        var vartxtIdprograma = document.getElementById("txtIdprograma").value;
        var varidRn = document.getElementById("idRn").value;
        var varidext = document.getElementById("idext").value;
        var varidRed = document.getElementById("idRed").value;

        if (varidRn == "" && varidext == "" && varidRed == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","No es posible guardar, no tiene ningun parametro establecido (RN, Ext o Usuario de Red)","warning");
            return;
        }

        if (varid_categorias == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe seleccionar un tipo de categoria.","warning");
            return;
        }else{
            if (vartxtIdCategorias == "") {
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","Campo del Id de la categoria no puede estar vacio.","warning");
                return;
            }else{
                if (vartxtIdNombre == "") {
                    event.preventDefault();
                    swal.fire("¡¡¡ Advertencia !!!","Campo del nombre de la categoria no puede estar vacio.","warning");
                    return;
                }else{
                    if (varid_usabilidad == "") {
                        event.preventDefault();
                        swal.fire("¡¡¡ Advertencia !!!","Debe seleccionar a que Dashboard se va a usar.","warning");
                        return;
                    }else{
                        if (vartxtIdprograma == "false") {
                            event.preventDefault();
                            swal.fire("¡¡¡ Advertencia !!!","No es posible guardar, no tiene un programa categoria, debes crear un programa","warning");
                            return;
                        }
                    }
                }
            }
        }
    };
</script>