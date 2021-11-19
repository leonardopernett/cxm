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

$this->title = 'Actualizar Categorias -- QA & Speech --';

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

    $varIndicador = "Indicador";

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
    $listData = ArrayHelper::map($varPerfiles, 'clientecategoria', 'clientecategoria');

    $NomCategorias  =  new Query;
    $NomCategorias  ->select(['tbl_dashboardcategorias.nombre'])->distinct()
                    ->from('tbl_dashboardcategorias')
                    ->where(['like','tbl_dashboardcategorias.tipocategoria',$varIndicador])
                    ->andwhere('tbl_dashboardcategorias.usua_id = '.$sessiones.'')
                    ->andwhere('tbl_dashboardcategorias.anulado = '.$varAnulado.'')
                    ->orderBy(['tbl_dashboardcategorias.nombre' => SORT_ASC]);
    $command3 = $NomCategorias->createCommand();
    $varNomCategorias = $command3->queryAll();
    $listData2 = ArrayHelper::map($varNomCategorias, 'nombre', 'nombre');

?>
&nbsp; 
  <?= Html::a('Regresar',  ['categoriasconfig'], ['class' => 'btn btn-success',
                        'style' => 'background-color: #707372',
                        'data-toggle' => 'tooltip',
                        'title' => 'Regresar']) 
  ?>
<div class="page-header" >
    <h3 class="text-center"><?= Html::encode($this->title) ?></h3>
</div> 
<br>
<div class="CapaCero" style="display: inline">
    <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
    <div class="row">              
        <div class="col-md-6">
            <?= $form->field($model, 'idcategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'type' => 'number', 'id'=>'txtIdCategorias',  'onkeypress' => 'return valida(event)'])->label('ID Categoria') ?>

            <?= $form->field($model, 'tipocategoria', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'id'=>'txtIdTipoCategorias'])->label('Tipo Categoria') ?>

            
            <?php $var2 = ['Positivo' => 'Positivo', 'Negativo' => 'Negativo']; ?>
        
            <?= $form->field($model, "orientacion", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($var2, ['prompt' => 'Selecciones...', 'id'=>"txtIdorientacion"])->label('Orientacion') ?> 
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'nombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'id'=>'txtIdNombre'])->label('Nombre Categoria') ?> 

            <?= $form->field($model, 'tipoindicador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 200, 'readonly' => 'readonly', 'id'=>'txtIdTipoIndicador'])->label('Tipo') ?> 
        </div>
    </div>
    <br>
    <div class="form-group" style="text-align: center;">
        <?= Html::submitButton('Actualizar', ['class' => 'btn btn-primary', 'id'=>'btn_submit'] ) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>

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

    var btn_submit = document.getElementById("btn_submit");
    btn_submit.addEventListener("click",function(e){

        e.preventDefault();
        
        var textosss
        var cantidad_valoraciones = document.getElementById("txtIdCategorias").value;
        var id_argumentos = document.getElementById("txtIdNombre").value; 

            if(cantidad_valoraciones == "" || cantidad_valoraciones == null || cantidad_valoraciones == undefined){
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","El campo Id-Categoria no puede estar vacio.","warning");
                return;   
            }

            if(id_argumentos == "" || id_argumentos == null || id_argumentos == undefined){
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","El campo Nombre Categoria no puede estar vacio.","warning");
                return;                 
            }


            document.getElementById("w0").submit(); 

    });
</script>