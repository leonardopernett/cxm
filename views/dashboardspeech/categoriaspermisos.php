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

$this->title = 'Configuración de Categorias -- QA & Speech --';

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

    $varAnulado = 0;
    $varMed = 2;
    $varBog = 98;
    $varK = 1;

    $perfiles =  new Query;
    $perfiles   ->select(['tbl_dashboardservicios.clientecategoria','tbl_dashboardservicios.nombreservicio','tbl_dashboardservicios.iddashboardservicios'])->distinct()
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
    $listData = ArrayHelper::map($varPerfiles, 'iddashboardservicios', 'nombreservicio');    

?>
<div class="formularios-form" style="display: inline">
    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
        <div class="row">              

                <?= $form->field($model, 'iddashservicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($listData, ['prompt' => 'Seleccionar...', 'id'=>'TipoArbol', 'onclick'=>'validarservicio();'])->label('Servicio/PCRC') ?> 

                <?=
                    $form->field($model, 'usuaid', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label('Tecnico Trainner VOC')
                                ->widget(Select2::classname(), [
                                    'id' => 'ButtonSelect',
                                    'name' => 'BtnSelectes',
                                    'attribute' => 'Valorador',
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'allowClear' => true,
                                        'minimumInputLength' => 4,
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['reportes/usuariolist']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                        'initSelection' => new JsExpression('function (element, callback) {
                                                    var id=$(element).val();
                                                    if (id !== "") {
                                                        $.ajax("' . Url::to(['reportes/usuariolist']) . '?id=" + id, {
                                                            dataType: "json",
                                                            type: "post"
                                                        }).done(function(data) { callback(data.results[0]);});
                                                    }
                                                }')
                                    ]
                                ]
                    );
                ?> 

                <?= $form->field($model, 'nombreservicio', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['readonly'=>'readonly','maxlength' => 200, 'id'=>'txtIdNombre', 'class'=>'hidden']) ?> 

                <br>
                <?= Html::submitButton(Yii::t('app', 'Guardar Permiso'),
                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                        'data-toggle' => 'tooltip',
                        'title' => 'Guardar Permiso']) 
                ?> 
            
        </div>
    <?php ActiveForm::end(); ?>    
<div>

<script type="text/javascript">
    function validarservicio(){
        var varIdPcrc = document.getElementById("TipoArbol").value;

        if (varIdPcrc != "") {
            $.ajax({
                method: "post",

                url: "prueba3",
                data : {
                    arbol_id : varIdPcrc,
                },
                success : function(response){ 
                    var numRta =   JSON.parse(response);    
                    document.getElementById("txtIdNombre").value = numRta;            
                    //console.log(numRta);
                }
            });            
        }
    };
</script>