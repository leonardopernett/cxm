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

$this->title = 'Héroes por el Cliente - Registrar Postulación';
$this->params['breadcrumbs'][] = $this->title;

  $template = '<div class="col-md-12">'
  . ' {input}{error}{hint}</div>';

  $sessiones = Yii::$app->user->identity->id;

  $rol =  new Query;
  $rol    ->select(['tbl_roles.role_id'])
          ->from('tbl_roles')
          ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                	'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
          ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                  'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
          ->where(['=','tbl_usuarios.usua_id',$sessiones]);                      
  $command = $rol->createCommand();
  $roles = $command->queryScalar();

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

  .card2 {
    height: 90px;
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
    background-image: url('../../images/heroes.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Data extensiones -->
<script src="../../js_extensions/jquery-2.1.3.min.js"></script>
<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>

<script src="../../js_extensions/chart.min.js"></script>

<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">

<script src="../../js_extensions/datatables/jquery.dataTables.min.js"></script>
<script src="../../js_extensions/datatables/dataTables.buttons.min.js"></script>
<script src="../../js_extensions/cloudflare/jszip.min.js"></script>
<script src="../../js_extensions/cloudflare/pdfmake.min.js"></script>
<script src="../../js_extensions/cloudflare/vfs_fonts.js"></script>
<script src="../../js_extensions/datatables/buttons.html5.min.js"></script>
<script src="../../js_extensions/datatables/buttons.print.min.js"></script>
<script src="../../js_extensions/mijs.js"> </script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >


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

<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => [
            'autocomplete' => 'off'
        ]
    ]
  ]); 
?>

<!-- Capa Informativa -->
<div class="capaInformativa" id="capaIdInformativa" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Notas Informativas') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-6">
            <div class="card2 mb">

                <div class="row">
                    <div class="col-md-2" align="text-center">
                        <label style="font-size: 15px;"><em class="fas fa-hand-point-right" style="font-size: 50px; color: #C148D0;"></em></label>
                    </div>

                    <div class="col-md-10" align="left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' ¡Qué bueno que estés aquí! Cuando sentimos pasión por el servicio, hacemos que nuestros usuarios queden tranquilos y satisfechos con nuestra labor.') ?></label>
                    </div>
                </div>          

            </div>        
        </div>

        <div class="col-md-6">
            <div class="card2 mb">

                <div class="row">
                    <div class="col-md-2" align="text-center">
                        <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 50px; color: #C148D0;"></em></label>
                    </div>

                    <div class="col-md-10" align="left">
                        <label style="font-size: 15px;"><?= Yii::t('app', ' Demuéstranos aquí en tu postulación, que lo más importante es que la experiencia de nuestros usuarios se transforme positivamente y genere memorabilidad hacía la marca que estás representando.') ?></label>
                    </div>
                </div>   
            </div>
        </div>
    </div>

</div>

<hr>

<!-- Capa Registro -->
<div class="capaRegistro" id="capaIdRegistro" style="display: inline;">
  
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"> <?= Yii::t('app', 'Procesos & Acciones') ?></label>
            </div>
        </div>
    </div>

    <br>

    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Formulario de Postulación') ?></label>
                <br>

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Tipo de Postulación: ') ?></label>
                        <?=  $form->field($model, 'id_tipopostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HeroesTipopostulacion::find()->where(['=','anulado',0])->orderBy(['tipopostulacion'=> SORT_ASC])->all(), 'id_tipopostulacion', 'tipopostulacion'),
                                        [
                                            'id' => 'id_tipopostulacion',
                                            'prompt'=>'Seleccionar Tipo Postulación...',
                                            'onclick' => 'varCambiarTipo()',
                                        ]
                                )->label(''); 
                        ?>

                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Nombre de Quién Postula: ') ?></label>
                        <?= $form->field($model, 'id_postulador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_postulador', 'readonly'=>'readonly', 'value'=>$varNombrePotulador])?>
                        <?= $form->field($model, 'id_postulador', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_postulador', 'class'=>'hidden', 'readonly'=>'readonly', 'value'=>$sessiones])?>

                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Cargo de Quién Postula: ') ?></label>
                        <?=  $form->field($model, 'id_cargospostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HeroesCargospostulacion::find()->where(['=','anulado',0])->orderBy(['cargospostulacion'=> SORT_ASC])->all(), 'id_cargospostulacion', 'cargospostulacion'),
                                        [
                                            'id' => 'id_cargospostulacion',
                                            'prompt'=>'Seleccionar Cargo Quién Postula...',
                                        ]
                                )->label(''); 
                        ?>


                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Embajador/Persona a Postular: ') ?></label>
                        <?= 
                            Html::radioList('anulado', 1, 
                            ['Administrativo','Asesor'], 
                            ['separator'=>'&nbsp;&nbsp;&nbsp;&nbsp;','name'=>'contact','id'=>'varidInteracciones','onclick'=>'varCambios();']) 
                        ?>

                        <div class="capaEmbajadorAsesor" id="capaIdEmbajadorAsesor" style="display: inline;">
                            <?=
                                    $form->field($model, 'procesos', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
                                    ->widget(Select2::classname(), [
                                        //'data' => array_merge(["" => ""], $data),
                                        'language' => 'es',
                                        'options' => ['id'=>'varIdAsesor','placeholder' => Yii::t('app', 'Seleccionar Usuario Asesor...')],
                                        'pluginOptions' => [
                                            'allowClear' => true,
                                            'minimumInputLength' => 4,
                                            'ajax' => [
                                                'url' => \yii\helpers\Url::to(['reportes/evaluadolist']),
                                                'dataType' => 'json',
                                                'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                                'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                            ],
                                            'initSelection' => new JsExpression('function (element, callback) {
                                                var id=$(element).val();
                                                if (id !== "") {
                                                    $.ajax("' . Url::to(['reportes/evaluadolist']) . '?id=" + id, {
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

                        <div class="capaEmbajadorAdmin" id="capaIdEmbajadorAdmin" style="display: none;">
                            <?=
                                $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->label(Yii::t('app',''))
                                ->widget(Select2::classname(), [
                                    'language' => 'es',
                                    'options' => ['id'=>'varIdAdmin', 'placeholder' => Yii::t('app', 'Seleccionar Usuario Administristrativo...')],
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
                        </div>

                        <?= $form->field($model, 'id_postulante', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'id_postulante', 'readonly'=>'readonly', 'class'=>'hidden'])?>
                        <?= $form->field($model, 'tipo_postulante', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idtipo_postulante', 'readonly'=>'readonly', 'class'=>'hidden'])?>


                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Cliente: ') ?></label>
                        <?=  $form->field($model, 'id_dp_clientes', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\ProcesosClienteCentrocosto::find()->select(['tbl_proceso_cliente_centrocosto.id_dp_clientes','CONCAT(tbl_proceso_cliente_centrocosto.cliente," - ",tbl_proceso_cliente_centrocosto.id_dp_clientes) AS cliente'])->where(['=','estado',1])->andwhere(['=','anulado',0])->groupBy(['tbl_proceso_cliente_centrocosto.id_dp_clientes'])->orderBy(['cliente'=> SORT_ASC])->all(), 'id_dp_clientes', 'cliente'),
                                        [
                                            'id' => 'id_dp_clientes',
                                            'prompt'=>'Seleccionar Cliente...',
                                            'onchange' => '
                                                $.get(
                                                    "' . Url::toRoute('listarcentrocostos') . '", 
                                                    {id: $(this).val()}, 
                                                    function(res){
                                                        $("#requester").html(res);
                                                    }
                                                );
                                            ',
                                        ]
                                )->label(''); 
                        ?>


                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Centro de Costos: ') ?></label>
                        <?= $form->field($model,'cod_pcrc', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(
                                                [],
                                                [
                                                    
                                                    'prompt' => 'Seleccionar...',
                                                    'id' => 'requester',
                                                ]
                                            )->label('');
                        ?> 
                    </div>

                    <div class="col-md-6">
                        <label style="font-size: 15px;"><span class="texto" style="color: #FC4343"><?= Yii::t('app', '*') ?></span> <?= Yii::t('app', ' Seleccionar Ciudad de Postulación: ') ?></label>
                        <?=  $form->field($model, 'id_ciudadpostulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\HeroesCiudadpostulacion::find()->where(['=','anulado',0])->orderBy(['ciudadpostulacion'=> SORT_ASC])->all(), 'id_ciudadpostulacion', 'ciudadpostulacion'),
                                        [
                                            'id' => 'id_ciudadpostulacion',
                                            'prompt'=>'Seleccionar Ciudad Postulación...',
                                        ]
                                )->label(''); 
                        ?>

                        <div class="capaFechaInteraccion" id="capaIdFechaInteraccion" style="display: none;">
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Ingresar fecha & hora de la Interacción: Ej: 2023-05-26 22:37:06 ') ?></label>
                            <input type="datetime-local" id="meeting_time" name="meeting-time">
                            <?= $form->field($model, 'fecha_interaccion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idfecha_interaccion','class'=>'hidden'])?>
                        </div>

                        <div class="capaExtension" id="capaIdExtension" style="display: none;">
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Ingresar Extensión de la Interacción: ') ?></label>
                            <?= $form->field($model, 'ext_interaccion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idext_interaccion','placeholder'=>'Ingresar Extensión de la Interacción'])?>
                        </div>

                        <div class="capaUsuarioViveExp" id="capaIdUsuarioViveExp" style="display: none;">
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Nombre del Usuario que Vive la Experiencia: ') ?></label>
                            <?= $form->field($model, 'usuario_interaccion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['id'=>'idusuario_interaccion','placeholder'=>'Nombre del Usuario que Vive la Experiencia'])?>
                        </div>

                        <div class="capaHistoria" id="capaIdHistoria" style="display: none;">
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Cuéntanos: (La historia que merece ser reconocida como gente buena, buena gente) ') ?></label>
                            <?= $form->field($model, 'historia_interaccion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'idhistoria_interaccion','placeholder'=>'Ingresa Tú Historia'])?>
                        </div>

                        <div class="capaIdea" id="capaIdIdea" style="display: none;">
                            <label style="font-size: 15px;"> <?= Yii::t('app', ' Cuéntanos: (Esa idea que nos ayudará a mejorar las experiencias y fortalecer la cultura de relacionamiento) ') ?></label>
                            <?= $form->field($model, 'idea_postulacion', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'ididea_postulacion','placeholder'=>'Ingresa Tú Idea'])?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>

<hr>

<!-- Capa Botones -->
<div class="capaBtn" id="capaIdBtn" style="display: inline;">
    
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-edit" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Guardar Postulación:') ?></label> 
                <?= Html::submitButton(Yii::t('app', 'Guardar'),
                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                'data-toggle' => 'tooltip',
                                'onclick' => 'varVerificar();',
                                'title' => 'Registrar Datos']) 
                ?>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label style="font-size: 15px;"><em class="fas fa-arrow-left" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Cancelar & Regresar:') ?></label> 
                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                ?>
            </div>
        </div>
    </div>

</div>

<hr>

<?php $form->end() ?>

<script type="text/javascript">
    function varCambios(){
        var radios = document.getElementsByName('anulado');
        var varcapaIdEmbajadorAsesor = document.getElementById("capaIdEmbajadorAsesor");
        var varcapaIdEmbajadorAdmin = document.getElementById("capaIdEmbajadorAdmin");
        var vartipo = 0;


        for (var radio of radios) {
            if (radio.checked) {
                var varchekeo = radio.value;
                if (varchekeo == 0) {
                    varcapaIdEmbajadorAsesor.style.display = 'none';
                    varcapaIdEmbajadorAdmin.style.display = 'inline';      
                    vartipo = 1;
                }else{
                    varcapaIdEmbajadorAsesor.style.display = 'inline';
                    varcapaIdEmbajadorAdmin.style.display = 'none';
                    vartipo = 2;  
                }
            }
        }

        
        document.getElementById("idtipo_postulante").value = vartipo;
        
    };

    function varCambiarTipo(){
        var varid_tipopostulacion = document.getElementById('id_tipopostulacion').value;

        var varcapaIdFechaInteraccion = document.getElementById("capaIdFechaInteraccion");
        var varcapaIdExtension = document.getElementById("capaIdExtension");
        var varcapaIdUsuarioViveExp = document.getElementById("capaIdUsuarioViveExp");
        var varcapaIdHistoria = document.getElementById("capaIdHistoria");
        var varcapaIdIdea = document.getElementById("capaIdIdea");

        if (varid_tipopostulacion == "") {
            varcapaIdFechaInteraccion.style.display = 'none';
            varcapaIdExtension.style.display = 'none';
            varcapaIdUsuarioViveExp.style.display = 'none';
            varcapaIdHistoria.style.display = 'none';
            varcapaIdIdea.style.display = 'none';
        }

        if (varid_tipopostulacion == "1") {
            varcapaIdFechaInteraccion.style.display = 'none';
            varcapaIdExtension.style.display = 'none';
            varcapaIdUsuarioViveExp.style.display = 'none';
            varcapaIdHistoria.style.display = 'none';
            varcapaIdIdea.style.display = 'inline';
        }

        if (varid_tipopostulacion == "2") {
            varcapaIdFechaInteraccion.style.display = 'inline';
            varcapaIdExtension.style.display = 'inline';
            varcapaIdUsuarioViveExp.style.display = 'inline';
            varcapaIdHistoria.style.display = 'none';
            varcapaIdIdea.style.display = 'none';
        }

        if (varid_tipopostulacion == "3") {
            varcapaIdFechaInteraccion.style.display = 'none';
            varcapaIdExtension.style.display = 'none';
            varcapaIdUsuarioViveExp.style.display = 'none';
            varcapaIdHistoria.style.display = 'inline';
            varcapaIdIdea.style.display = 'none';
        }

    };

    function varVerificar(){
        var var_IdAsesor = document.getElementById("varIdAsesor").value;
        var var_IdAdmin = document.getElementById("varIdAdmin").value;

        if (var_IdAsesor != "") {
            document.getElementById("id_postulante").value = var_IdAsesor;
        }else{
            if (var_IdAdmin != "") {
                document.getElementById("id_postulante").value = var_IdAdmin;
            }else{
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe de seleccionar un embajador a postular.","warning");
                return;
            }
        }

        var varmeeting_time = document.getElementById("meeting_time").value;

        if (varmeeting_time != "") {
            document.getElementById("idfecha_interaccion").value = varmeeting_time;
        }

        var varidtipo_postulante = document.getElementById("idtipo_postulante").value;

        if (varidtipo_postulante == 0) {
            document.getElementById("idtipo_postulante").value = 2;
        }


        var varid_tipopostulacion = document.getElementById("id_tipopostulacion").value;
        var varid_postulador = document.getElementById("id_postulador").value;
        var varid_cargospostulacion = document.getElementById("id_cargospostulacion").value;
        var varid_dp_clientes = document.getElementById("id_dp_clientes").value;
        var varrequester = document.getElementById("requester").value;
        var varid_ciudadpostulacion = document.getElementById("id_ciudadpostulacion").value;

        if (varid_tipopostulacion == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un tipo de postulación.","warning");
            return;
        }
        if (varid_postulador == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar un postulador.","warning");
            return;
        }
        if (varid_cargospostulacion == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar el cargo de quien postula.","warning");
            return;
        }
        if (varid_dp_clientes == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un cliente.","warning");
            return;
        }
        if (varrequester == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar un centro de costos.","warning");
            return;
        }
        if (varid_ciudadpostulacion == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar una ciudad.","warning");
            return;
        }

    };
</script>