<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use app\models\ControlProcesosPlan;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Seguimiento Equipo de Trabajo';
$this->params['breadcrumbs'][] = $this->title;

$sessiones1 = Yii::$app->user->identity->id;

    $rol =  new Query;
    $rol    ->select(['tbl_roles.role_id'])
            ->from('tbl_roles')
            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                    'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
            ->where('tbl_usuarios.usua_id = '.$varidevaluados.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    $varRol = Yii::$app->get('dbslave')->createCommand("select r.role_nombre from tbl_roles r inner join rel_usuarios_roles ur on r.role_id = ur.rel_role_id  inner join tbl_usuarios u on ur.rel_usua_id = u.usua_id where u.usua_id = $varidevaluados")->queryScalar();

    $varNombre = Yii::$app->get('dbslave')->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varidevaluados")->queryScalar();

    $varIdtc = Yii::$app->get('dbslave')->createCommand("select idtc from tbl_control_procesos where anulado = 0 and id = $varids")->queryScalar();

    $varRango = Yii::$app->get('dbslave')->createCommand("select tipocortetc from tbl_tipocortes where anulado = 0 and idtc = $varIdtc")->queryScalar();

    $variables = Yii::$app->get('dbslave')->createCommand("select * from tbl_tipos_cortes where idtc = $varIdtc")->queryAll();
    $listData = ArrayHelper::map($variables, 'idtcs', 'diastcs');

    

    $vararboles = Yii::$app->get('dbslave')->createCommand("select tbl_control_params.arbol_id,  tbl_arbols.name from tbl_arbols inner join tbl_control_params on tbl_arbols.id = tbl_control_params.arbol_id inner join tbl_control_procesos on tbl_control_params.evaluados_id = tbl_control_procesos.evaluados_id and tbl_control_params.fechacreacion = tbl_control_procesos.fechacreacion where tbl_control_procesos.id = $varids order by tbl_arbols.name asc")->queryAll();
    $varlistarbols = ArrayHelper::map($vararboles, 'arbol_id', 'name');

    $var = ['Capacidad operativa' => 'Capacidad operativa', 'Incapacidad del asesor' => 'Incapacidad del asesor', 'Incapacidad del lider' => 'Incapacidad del lider', 'Incapaciddad del tecnico' => 'Incapaciddad del tecnico', 'Licencia' => 'Licencia', 'Nuevo' => 'Nuevo', 'Prestamo' => 'Prestamo', 'Retiro' => 'Retiro', 'Reuniones' => 'Reuniones', 'Suspensión' => 'Suspensión', 'Vacaciones' => 'Vacaciones', 'Otros procesos' => 'Otros procesos'];
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
            font-family: "Nunito",sans-serif;
            font-size: 150%;    
            text-align: left;    
    }

    .col-sm-6 {
        width: 100%;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }
</style>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
       
      </div>
    </div>
  </div>
</header>
<br><br><br>
<div id="capaUno" style="display: inline">   
    <div class="row">
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-question-circle" style="font-size: 20px; color: #2CA5FF;"></em> Rol del usuario:</label>
                <label><?php echo $varRol; ?></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-user-circle" style="font-size: 20px; color: #2CA5FF;"></em> Técnico/Lider:</label>
                <label><?php echo $varNombre; ?></label>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card1 mb">
                <label><em class="fas fa-calendar-alt" style="font-size: 20px; color: #2CA5FF;"></em> Corte seleccionado:</label>
                <label><?php echo $varRango; ?></label>
            </div>
        </div>
    </div> 
</div>
<hr>
<?php $form = ActiveForm::begin([
    'options' => ["id" => "buscarMasivos"],
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?>
<div id="capaDos" style="display: inline">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-plus-square" style="font-size: 20px; color: #3055FF;"></em> Justificación o Escalamientos: </label>
                <div class="row">
                    <div class="col-md-4">
                        <label for="txtIndiGlo" style="font-size: 14px;">Tipo de corte</label>
                        <?= $form->field($model, 'idtcs')->dropDownList($listData, ['prompt' => 'Seleccionar corte...', 'id'=>'idtcs']) ?>
                    </div>
                    <div class="col-md-4">
                        <label for="txtIndiGlo" style="font-size: 14px;">Programa pcrc</label>
                        <?= $form->field($model, 'arbol_id')->dropDownList($varlistarbols, ['prompt' => 'Seleccionar pcrc...', 'id'=>'idarbols']) ?>
                    </div>
                    <div class="col-md-4">                        
                        <label for="txtIndiGlo" style="font-size: 14px;">Tipo de justificación</label>
                        <?= $form->field($model, "justificacion")->dropDownList($var, ['prompt' => 'Seleccionar justificación', 'id'=>"id_argumentos"]) ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-4">
                        <?php if ($roles == '272') { ?>
                            <label for="txtIndiGlo" style="font-size: 14px;">Cantidad de justificaciones</label>
                        <?php }else{ ?>
                            <label for="txtIndiGlo" style="font-size: 14px;">Cantidad de justificaciones por asesor</label>
                        <?php } ?>
                        <?= $form->field($model, 'cantidadjustificar')->textInput(['maxlength' => 200, 'onkeypress'=>'return valida(event)', 'id'=>'txtcantidad', 'placeholder'=>'Agregar solo número'])?>
                    </div>
                    <div class="col-md-4">
                        <label for="txtIndiGlo" style="font-size: 14px;">Correo del coordinador</label>
                        <?= $form->field($model, 'correo')->textInput(['maxlength' => 200, 'id'=>'txtcorreoid','placeholder'=>'Agregar correo corporativo'])?>
                    </div>
                    <div class="col-md-4">
                        <label for="txtIndiGlo" style="font-size: 14px;">Seleccionar asesor</label>
                        <?php if($roles == '273' ||$roles == '274') { ?>
                            <?=
                                $form->field($model, 'asesorid')->label(Yii::t('app','Valorado'))
                                ->widget(Select2::classname(), [
                                    'language' => 'es',
                                    'options' => ['placeholder' => Yii::t('app', 'Seleccionar asesor...')],
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
                                )->label('');
                            ?> 
                        <?php }else{ ?>
                            <?= 
                                $form->field($model, 'asesorid')->label(Yii::t('app','Valorado'))
                                ->widget(Select2::classname(), [
                                    'language' => 'es',
                                    'options' => ['readonly' => true, 'placeholder' => Yii::t('app', 'Seleccionar asesor...')],
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
                                )->label('');
                            ?>
                        <?php } ?>
                    </div>
                </div>
                <br>
                <div class="row">
                    <div class="col-md-12">
                        <label for="txtIndiGlo" style="font-size: 14px;">Comentarios</label>
                        <?= $form->field($model, 'comentarios')->textInput(['maxlength' => 300, 'id'=>'txtcomentariosid', 'placeholder'=>'Agregar comentarios'])?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<div id="idCapaTres" style="display: inline">
    <div id="capaTres" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-save" style="font-size: 17px; color: #FFC72C;"></em> Guardar Justificación: </label> 
                                <div onclick="validarvalor();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="ButtonSearch" >
                                  Guardar Información
                                </div>                        
                            </div>
                        </div>  
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                                <?= Html::a('Regresar',  ['view','id'=>$varids,'evaluados_id'=>$varidevaluados], ['class' => 'btn btn-success',
                                        'style' => 'background-color: #707372',
                                        'data-toggle' => 'tooltip',
                                        'title' => 'Regresar']) 
                                ?>                           
                            </div>
                        </div>  
                    </div>                      
                </div>
            </div>
        </div>
    </div>
</div>
<hr>
<?php $form->end() ?> 

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

    function validarvalor(){
        var varidtcs = document.getElementById("idtcs").value;
        var varidarbols = document.getElementById("idarbols").value;
        var varid_argumentos = document.getElementById("id_argumentos").value;
        var vartxtcantidad = document.getElementById("txtcantidad").value;
        var vartxtcorreoid = document.getElementById("txtcorreoid").value;
        var varidtcgeneral = '<?php echo $varids; ?>';
        var varidusua = '<?php echo $varidevaluados; ?>';
        var varselect2chosen1 = document.getElementById("planescalamientos-asesorid").value;
        var vartxtcomentariosid = document.getElementById("txtcomentariosid").value;
        var varroles = "<?php echo $roles; ?>";

        if (varidtcs == "") {
            event.preventDefault();
            swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar el tipo de corte.","warning");
            return;
        }else{
            if (varidarbols == "") {
                event.preventDefault();
                swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar el programa pcrc.","warning");
                return;
            }else{
                if (varid_argumentos == "") {
                    event.preventDefault();
                    swal.fire("¡¡¡ Advertencia !!!","Debe de seleccionar un argumento.","warning");
                    return;
                }else{
                    if (vartxtcantidad == "") {
                        event.preventDefault();
                        swal.fire("¡¡¡ Advertencia !!!","Debe de ingresar la cantidad a justificar.","warning");
                        return;            
                    }else{
                        if (vartxtcorreoid == "") {
                            event.preventDefault();
                            swal.fire("¡¡¡ Advertencia !!!","Debe de ingresar el correo del coordinador.","warning");
                            return;
                        }else{
                            $.ajax({
                                method: "get",
                                url: "verificarcantidad",
                                data: {
                                    txtvaridtcs : varidtcs,
                                    txtvaridarbols : varidarbols,
                                    txtvartxtcantidad : vartxtcantidad,
                                    txtvaridtcgeneral : varidtcgeneral,
                                    txtvaridusua : varidusua,
                                },
                                success : function(response){
                                    numRta =   JSON.parse(response);
                                    if (numRta == 1) {
                                        event.preventDefault();
                                        swal.fire("¡¡¡ Advertencia !!!","La cantidad de justificaciones excede al valor de la meta del corte seleccionado.","warning");
                                        return;
                                    }else{
                                        if (numRta == 2) {
                                            event.preventDefault();
                                            swal.fire("¡¡¡ Advertencia !!!","Para el corte y el pcrc seleccionado ya tiene una justificación que excede el valor de la meta; debe seleccionar otros items.","warning");
                                            return;
                                        }else{
                                            $.ajax({
                                               method: "get",
                                                url: "guardarjustificacion",
                                                data: {
                                                    txtvaridtcs : varidtcs,
                                                    txtvaridarbols : varidarbols,
                                                    txtvarid_argumentos : varid_argumentos, 
                                                    txtvartxtcantidad : vartxtcantidad,
                                                    txtvartxtcorreoid : vartxtcorreoid,
                                                    txtvaridtcgeneral : varidtcgeneral,
                                                    txtvaridusua : varidusua,
                                                    txtvarselect2chosen1 : varselect2chosen1,
                                                    txtvartxtcomentariosid : vartxtcomentariosid,
                                                },
                                                success : function(response){
                                                    numRta2 =   JSON.parse(response);
                                                    window.open('../seguimientoplan/'+varidtcgeneral+'?evaluados_id='+varidusua,'_self');

                                                } 
                                            });
                                        } 
                                    }                                    
                                }
                            });
                        }
                    }
                }
            }
        }
    };
</script>