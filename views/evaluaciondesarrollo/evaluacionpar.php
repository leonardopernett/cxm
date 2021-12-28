<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\bootstrap\Modal;
use yii\helpers\ArrayHelper;

$this->title = 'Evaluacion de desarrollo';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();

    $vardocumentjefe = Yii::$app->db->createCommand("select ue.documento_jefe from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidcargo = Yii::$app->db->createCommand("select ue.id_dp_cargos from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidposicion = Yii::$app->db->createCommand("select ue.id_dp_posicion from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();
    $varidfuncion = Yii::$app->db->createCommand("select ue.id_dp_funciones from tbl_usuarios_evalua ue where ue.documento = '$vardocument' group by ue.documento_jefe")->queryScalar();

    $varidlist = Yii::$app->db->createCommand("select ue.nombre_completo, ue.documento from tbl_usuarios_evalua ue where ue.documento_jefe = '$vardocumentjefe' and ue.documento != '$vardocument' order by ue.nombre_completo asc")->queryAll();
    $listData = ArrayHelper::map($varidlist, 'documento', 'nombre_completo');

    $varTipos = ['Otros inconvenientes' => 'Otros inconvenientes' ];


?>
<div id="idCapaUno" style="display: inline">
<?php $form = ActiveForm::begin([
    'layout' => 'horizontal',
    'fieldConfig' => [
        'inputOptions' => ['autocomplete' => 'off']
      ]
    ]); ?> 
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label style="font-size: 16px;"><eem class="fas fa-bolt" style="font-size: 20px; color: #4D83FE;"></eem> Seleccionar el Par </label>
                <?= $form->field($model, "documento")->dropDownList($listData, ['prompt' => 'Seleccionar Una Persona', 'id'=>"idpares", 'onchange' => 'validarid();']) ?>
                <div id="idmessage1" style="display: none">
                    <div class="panel panel-default">
                        <div class="panel-body" style="background-color: #f7b9b9;"><label style="font-size: 15px;">Ya has realizado la evaluación al usuario seleccionado, por favor elija otro persona para realizar la evaluación, gracias.</label>
                        </div>
                    </div>
                </div>
                <?= Html::submitButton(Yii::t('app', 'Aceptar'),
                                    ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',                
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Seleccionar a par',
                                    'onclick' => 'validarpar();',
                                    'id'=>'ButtonSearch',
                                    'style' => 'display: inline']) 
                ?>
            </div>
            <br>
            <div class="card1 mb">
                <label style="font-size: 16px;"><eem class="fas fa-exclamation-circle" style="font-size: 20px; color: #FF441A;"></eem> No realizar evaluación de pares </label>
                <label style="font-size: 14px;">Si seleccionas esta opción NO podrás realizar la evaluación a ninguno de tus pares. </label>
                <?= Html::a('Aceptar',  ['evaluaciondesarrollo/restringirevalua'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #FF441A',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Aceptar'])
                ?>  
            </div>
            <br>
            <div class="card1 mb">
                <label style="font-size: 16px;"><em class="fas fa-exclamation" style="font-size: 20px; color: #4D83FE;"></em> Notificación </label>
                <label style="font-size: 14px;">Valida que del listado relacionado estén tus compañeros de trabajo, evalúa sólo aquellos con quienes tienes una relación directa en terminos de responsabilidades y funciones. </label>

                <div class="row">
                    <div class="col-md-6">
                        <label style="font-size: 16px;"><em class="fas fa-plus-square" style="font-size: 20px; color: #4D83FE;"></em> Ingresar novedad </label>
                    </div>
                    <div class="col-md-6" class="text-right">
                        <div onclick="opennovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbn1" >
                                  [ + ]
                        </div> 
                        <div onclick="closenovedad();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbn2" >
                                  [ - ]
                        </div> 
                    </div>
                    <div class="col-md-12">
                        <div id="idnovedad" style="display: none">
                            <?= $form->field($model, "asunto")->dropDownList($varTipos, ['prompt' => 'Seleccionar Novedades', 'id'=>"idasuntosNcargo", 'onchange'=>'habilitarvar();']) ?>
                                <div class="row">
                                    <div class="col-md-9" id="idbotones" style="display: none">
                                        <?= $form->field($model, 'cambios')->textInput(['maxlength' => 250,  'id'=>'IdcambiosNcargo', 'placeholder' => 'Digite documento de la persona']) ?>
                                    </div>
                                    <div class="col-md-3" class="text-left" id="idbotones1" style="display: none">
                                        <div onclick="novedadsearch();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbnsearch" >
                                            [ Buscar ] 
                                        </div> 
                                    </div>
                                </div>
                                <?= $form->field($model, 'comentarios')->textInput(['maxlength' => 250,  'id'=>'IdcomentariosNcargo']) ?>
                            <div onclick="savenovedad();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="idtbnsave" >
                            Guardar Novedad
                            </div> 
                        </div>                
                    </div>
                </div>
                 
            </div>

        </div>
    </div>
<?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    function validarid(){
        var varidpares = document.getElementById("idpares").value;
        var varidmessage1 = document.getElementById("idmessage1");
        var varvardocument = '<?php echo $vardocument; ?>';
        var varButtonSearch = document.getElementById("ButtonSearch");

        $.ajax({
                method: "get",
                url: "verificapar",
                data: {
                    txtvaridpares : varidpares,
                    txtvardocumento : varvardocument,
                },
                success : function(response){
                    numRta =   JSON.parse(response);
                    if (numRta >= 1) {
                        varidmessage1.style.display = 'inline';
                        varButtonSearch.style.display = 'none';
                    }else{
                        varidmessage1.style.display = 'none';
                        varButtonSearch.style.display = 'inline';
                    }
                }
            });
    };

    function validarpar(){
        var varidpares = document.getElementById("idpares").value;


        if (varidpares == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Seleccione un par","warning");
            return; 
        }
    };

    function opennovedad(){
        var varidtbn1 = document.getElementById("idtbn1");
        var varidtbn2 = document.getElementById("idtbn2");
        var varidnovedad = document.getElementById("idnovedad");

        varidtbn1.style.display = 'none';
        varidtbn2.style.display = 'inline';
        varidnovedad.style.display = 'inline';

    };

    function closenovedad(){
        var varidtbn1 = document.getElementById("idtbn1");
        var varidtbn2 = document.getElementById("idtbn2");
        var varidnovedad = document.getElementById("idnovedad");

        varidtbn1.style.display = 'inline';
        varidtbn2.style.display = 'none';
        varidnovedad.style.display = 'none';
    };

    function savenovedad(){
        // var varIdcomentariosNovedad = document.getElementById("IdcomentariosNovedad").value;
        // var varvardocument = '<?php echo $vardocument; ?>';
        var varIdcomentariosNovedad = document.getElementById("IdcomentariosNcargo").value;
        var varvardocument = '<?php echo $vardocument; ?>';
        var varidasuntosNcargo = document.getElementById("idasuntosNcargo").value;
        var varidpares = document.getElementById("IdcambiosNcargo").value;

        $.ajax({
                method: "get",
                url: "ingresarnovedadpares",
                data: {
                    txtvaridasuntosNcargo : varidasuntosNcargo,
                    txtvarIdcomentariosNovedad : varIdcomentariosNovedad,
                    txtvardocumento : varvardocument,
                    txtvaridpares : varidpares,
                },
                success : function(response){
                    numRta =   JSON.parse(response);
                    window.open('../evaluaciondesarrollo/index','_self');

                }
        });
    };

    function habilitarvar(){
        var varidasuntosNcargo = document.getElementById("idasuntosNcargo").value;
        var varidbotones = document.getElementById("idbotones");
        var varidbotones1 = document.getElementById("idbotones1");

        if (varidasuntosNcargo == "No esta persona en lista") {
            varidbotones.style.display = 'inline';
            varidbotones1.style.display = 'inline';
            document.getElementById("IdcomentariosNcargo").readOnly = true;
        }else{
            varidbotones.style.display = 'none';
            varidbotones1.style.display = 'none';
            document.getElementById("IdcomentariosNcargo").readOnly = false;
            document.getElementById("IdcomentariosNcargo").value = "";
            document.getElementById("IdcambiosNcargo").value = "";
        }
    };

     function novedadsearch(){
        var varIdcambiosNcargo = document.getElementById("IdcambiosNcargo").value;

        if (varIdcambiosNcargo == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Ingrese la cedula","warning");
            return; 
        }else{
            $.ajax({
                method: "get",
                url: "verificapersona",
                data: {
                    txtvarIdcambiosNcargo : varIdcambiosNcargo,
                },
                success : function(response){
                    numRta =   JSON.parse(response);
                    if (numRta == "") {
                        document.getElementById("IdcomentariosNcargo").value = "No hay registros";
                    }else{
                        document.getElementById("IdcomentariosNcargo").value = numRta;
                    }
                }
            });
        }
    };
</script>