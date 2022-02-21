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

$this->title = 'Registro de Categorias';

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
    $FechaValor = date("2020-01-01");


    $varIndicador = "Indicador";
    $varMotivos = "Detalle motivo contacto";

    $varAnulado = 0;
    $varMed = 2;
    $varBog = 98;
    $varK = 1;

    $perfiles =  new Query;
    $perfiles   ->select(['tbl_dashboardservicios.clientecategoria','tbl_dashboardservicios.nombreservicio'])->distinct()
                ->from('tbl_usuarios')
                ->join('LEFT OUTER JOIN', 'rel_grupos_usuarios',
                    'tbl_usuarios.usua_id = rel_grupos_usuarios.usuario_id')
                ->join('LEFT OUTER JOIN', 'tbl_grupos_usuarios',
                    'rel_grupos_usuarios.grupo_id = tbl_grupos_usuarios.grupos_id')
                ->join('LEFT OUTER JOIN', 'tbl_permisos_grupos_arbols',
                    'tbl_grupos_usuarios.grupos_id = tbl_permisos_grupos_arbols.grupousuario_id')
                ->join('LEFT OUTER JOIN', 'tbl_arbols',
                    'tbl_permisos_grupos_arbols.arbol_id = tbl_arbols.id')
                ->join('LEFT OUTER JOIN', 'tbl_dashboardservicios',
                    'tbl_arbols.id = tbl_dashboardservicios.arbol_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'')
                ->andwhere('tbl_arbols.activo = '.$varAnulado.'')
                ->andwhere('tbl_arbols.arbol_id != '.$varK.'')
                ->andwhere('tbl_dashboardservicios.anulado ='.$varAnulado.'')
                ->orderBy(['tbl_arbols.name'=> SORT_ASC]);
    $command2 = $perfiles->createCommand();
    $varPerfiles = $command2->queryAll(); 
    $listData = ArrayHelper::map($varPerfiles, 'clientecategoria', 'nombreservicio');


    $varNomCategorias = Yii::$app->db->createCommand("select idcategoria, nombre from tbl_dashboardcategorias where idcategorias = 1 and anulado = 0 and fechacreacion between '2020-01-01' and '2020-12-31'")->queryAll();
    $listData2 = ArrayHelper::map($varNomCategorias, 'nombre', 'idcategoria');



    $NombreMotivos  =  new Query;
    $NombreMotivos  ->select(['concat(tbl_dashboardcategorias.idcategoria, " - ",tbl_dashboardcategorias.nombre) as concatenar','tbl_dashboardcategorias.idcategoria','tbl_dashboardcategorias.nombre'])->distinct()
                    ->from('tbl_dashboardcategorias')
                    ->join('LEFT OUTER JOIN', 'tbl_dashboardpermisos',
                        'tbl_dashboardcategorias.iddashservicio = tbl_dashboardpermisos.iddashservicio')   
                    ->where("tbl_dashboardcategorias.idcategorias = 3")
                    ->andwhere('tbl_dashboardcategorias.anulado = '.$varAnulado.'')
                    ->andwhere("tbl_dashboardcategorias.fechacreacion > $FechaValor")
                    ->orderBy(['tbl_dashboardcategorias.nombre' => SORT_ASC]);
    $command4 = $NombreMotivos->createCommand();
    $varNomMotivos = $command4->queryAll();
    $listData3 = ArrayHelper::map($varNomMotivos, 'nombre', 'concatenar');    
      
    

?>
<div class="formularios-form" style="display: inline">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>

    <div class="CapaCero" style="display: inline">
        <?php $var = ['Detalle motivo contacto' => 'Motivo de contacto', 'Indicador' => 'Indicador', 'Programa' => 'Programa', 'SubMotivo' => 'Sub Motivo', 'Variable' => 'Variable']; ?>

        <?= $form->field($model, 'tipocategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var, ['prompt' => 'Seleccione...', 'id'=>"id_categorias", 'onclick'=>'dimensiones2();'])->label('Tipo Categoria') ?> 

        <?= $form->field($model, 'clientecategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'TipoArbol', 'onclick'=>'validarservicio();'])->label('Servicio/PCRC') ?> 
    </div>
    <hr>
    <div class="CapaUno" style="display: inline">
        <div class="row">              
            <div class="col-md-6">
                <?= $form->field($model, 'idcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'type' => 'number', 'id'=>'txtIdCategorias',  'onkeypress' => 'return valida(event)'])->label('IDCategoria en Speech') ?>                 

                <?= $form->field($model, 'ciudadcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'id'=>'id_city'])->label('Ciudad de la Categoria') ?>

                <?php $var3 = ['Positivo' => 'Positivo', 'Negativo' => 'Negativo']; ?>

                <?= $form->field($model, 'orientacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var3, ['prompt' => 'Seleccione...', 'id'=>"idOrientacion"])->label('Orientación de la Categoria') ?>
                
            </div>
            <div class="col-md-6">
                <?= $form->field($model, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'txtIdNombre'])->label('Nombre Categoria') ?> 

                <?php $var2 = ['1' => 'DashBoard Speech', '2' => 'DashBoard IDA', '3' => 'Ambos DashBoard']; ?>

                <?= $form->field($model, 'usabilidad', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var2, ['prompt' => 'Seleccione...', 'id'=>"id_usabilidad"])->label('Usar en Dashboard') ?>               

                <div class="CapaSubUno" style="display: none" id="CapaUno1">
                    <?= $form->field($model, 'tipoindicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'id'=>'txtIdIndicador', 'onclick'=>'validarservicio2();'])->label('Tipo Padre') ?>
                </div>
                <div class="CapaSubDos" style="display: none" id="CapaDos2">
                    <?= $form->field($model, 'tipoindicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData2, ['prompt' => 'Seleccionar...', 'id'=>'txtIdIndicador2', 'onclick'=>'validarservicio2();'])->label('Tipo Padre') ?> 
                </div>  
                <div class="CapaSubTres" style="display: none" id="CapaDos3">
                    <?= $form->field($model, 'tipoindicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData3, ['prompt' => 'Seleccionar...', 'id'=>'txtIdIndicador3', 'onclick'=>'validarservicio3();'])->label('Tipo Padre') ?> 
                </div>                 

                <?= $form->field($model, 'tipoindicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'class' => 'hidden', 'id'=>'txtIdIndicadorRTA']) ?>

            </div>            
        </div> 
        <div class="form-group" style="text-align: center;">
            <?= Html::submitButton(Yii::t('app', 'Guardar Categoria'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'onclick' => 'verificar();',
                        'title' => 'Guardar Categoria']) 
            ?> 
        </div>
    </div>

    <?php ActiveForm::end(); ?>

<div>


<script type="text/javascript">
    function valida(e){
        tecla = (document.all) ? e.keyCode : e.which;

        //Tecla de retroceso para borrar, siempre la permite
        if (tecla==8){
            return true;
        }
            
        // Patron de entrada, en este caso solo acepta numeros
        patron =/[0-9]/;
        tecla_final = String.fromCharCode(tecla);
        return patron.test(tecla_final);
    };

    function dimensiones2(){
        var varnombre = document.getElementById("txtIdNombre").value;
        var varcategoria = document.getElementById("id_categorias").value;
        var varindicador = document.getElementById("txtIdIndicador");
        var varTipoArbol = document.getElementById("TipoArbol");
        var varCapaUno1 = document.getElementById("CapaUno1");
        var varCapaDos2 = document.getElementById("CapaDos2");
        var varCapaDos3 = document.getElementById("CapaDos3");
        var vartxtIdIndicadorRTA = document.getElementById("txtIdIndicadorRTA");
        var varCity = document.getElementById("id_city");
        
        if (varcategoria == "Detalle motivo contacto") {
            varindicador.value = "Motivos de Contacto";
            vartxtIdIndicadorRTA.value = "Motivos de Contacto";
            varTipoArbol.value = "";
            varCapaUno1.style.display = 'none';
            varCapaDos2.style.display = 'none';
            varCity.value = "";
        }else{
            if (varcategoria == "Indicador") {
                varindicador.value = "Indicador";
                varTipoArbol.value = "";
                varCapaUno1.style.display = 'none';
                varCapaDos2.style.display = 'none';
                vartxtIdIndicadorRTA.value = "Indicador";
                varCity.value = "";
            }else{
                if (varcategoria == "Programa") {
                    varindicador.value = "Variable";
                    varTipoArbol.value = "";
                    varCapaUno1.style.display = 'none';
                    varCapaDos2.style.display = 'none';
                    vartxtIdIndicadorRTA.value = "Variable";
                    varCity.value = "";
                }else{
                    if (varcategoria == "Variable") {
                        varTipoArbol.value = "";
                        varCapaUno1.style.display = 'none';
                        varCapaDos2.style.display = 'none';
                        varCity.value = "";
                    }else{
                        if (varcategoria == "SubMotivo") {
                            varTipoArbol.value = "";
                            varCapaUno1.style.display = 'none';
                            varCapaDos2.style.display = 'none';
                            varCapaDos3.style.display = 'none';
                            varCity.value = "";
                        }
                    }                    
                }
            }
        }
    };

    function validarservicio(){
        var varTipoArbol = document.getElementById("TipoArbol").value;
        var varcategoria = document.getElementById("id_categorias").value;
        var varCapaUno1 = document.getElementById("CapaUno1");
        var varCapaDos2 = document.getElementById("CapaDos2");
        var varCapaDos3 = document.getElementById("CapaDos3");

        if (varTipoArbol != "") {
            $.ajax({
                method: "post",

                url: "prueba2",
                data : {
                    arbol_id : varTipoArbol,
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    document.getElementById("id_city").value = numRta;         
                }
            });            
        }

        if (varTipoArbol != "" && varcategoria != "Variable" && varcategoria != "SubMotivo") {
            varCapaUno1.style.display = 'inline';
            varCapaDos2.style.display = 'none';
            varCapaDos3.style.display = 'none';
        }else{
            if (varTipoArbol != "" && varcategoria == "Variable" && varcategoria != "SubMotivo") {
                varCapaUno1.style.display = 'none';
                varCapaDos2.style.display = 'inline';
                varCapaDos3.style.display = 'none';
            }else{
                if (varTipoArbol != "" && varcategoria != "Variable" && varcategoria == "SubMotivo") {
                    varCapaUno1.style.display = 'none';
                    varCapaDos2.style.display = 'none';
                    varCapaDos3.style.display = 'inline';
                }else{
                    varCapaUno1.style.display = 'none';
                    varCapaDos2.style.display = 'none';
                    varCapaDos3.style.display = 'none';
                }
            }            
        }
    };

    function verificar(){
        var vartxtIdCategorias = document.getElementById("txtIdCategorias").value;
        var varid_categorias = document.getElementById("id_categorias").value;
        var vartxtIdNombre = document.getElementById("txtIdNombre").value;
        var vartxtIdIndicador = document.getElementById("txtIdIndicador").value;
        var varid_city = document.getElementById("id_city").value;
        var varTipoArbol = document.getElementById("TipoArbol").value;
        var varOrientacion = document.getElementById("idOrientacion").value;
        var varid_usabilidad = document.getElementById("id_usabilidad").value;


        if (vartxtIdCategorias == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","El Id Categoria no puede estar vacia.","warning");
            return; 
        }else{
            if (varid_categorias == "") {
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","Debe seleccionar un tipo de categoria.","warning");
                return; 
            }else{
                if (vartxtIdNombre == "") {
                    event.preventDefault();
                    swal.fire("¡¡¡ Advertencia !!!","El nombre de la categoria puede estar vacia.","warning");
                    return;
                }else{
                    if (varid_usabilidad == "") {
                        event.preventDefault();
                        swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar el uso del DashBoard.","warning");
                        return;
                    }else{
                        if (varid_city == "") {
                            event.preventDefault();
                            swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar una ciudad.","warning");
                            return;
                        }else{
                            if (varTipoArbol == "") {
                                event.preventDefault();
                                swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar un Servicio/PCRC.","warning");
                                return;
                            }else{
                                if (varOrientacion == "") {
                                    event.preventDefault();
                                    swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar una orientacion.","warning");
                                    return;
                                }
                            }
                        }
                    }
                }
            }
        }
    };

    function validarservicio2(){
        var varTipoArbol = document.getElementById("txtIdIndicador2").value;
        var vartxtIdIndicadorRTA = document.getElementById("txtIdIndicadorRTA");

        vartxtIdIndicadorRTA.value = varTipoArbol;
    };

    function validarservicio3(){
        var varTipoArbol = document.getElementById("txtIdIndicador3").value;
        var vartxtIdIndicadorRTA = document.getElementById("txtIdIndicadorRTA");

        vartxtIdIndicadorRTA.value = varTipoArbol;
    };    
</script>
