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

    $varidlist = Yii::$app->db->createCommand("select ue.nombre_completo, ue.documento from tbl_usuarios_evalua ue where ue.documento_jefe = '$vardocument' and ue.documento != '$vardocument' order by ue.nombre_completo asc")->queryAll();
    $listData = ArrayHelper::map($varidlist, 'documento', 'nombre_completo');

    $varTipos = ['Persona no esta a mi cargo' => 'Persona no esta a mi cargo', 'Falta persona a mi cargo' => 'Falta persona a mi cargo', 'Otros inconvenientes' => 'Otros inconvenientes' ];

    $queryj = Yii::$app->db->createCommand("select ue.documento_jefe, ue.nombre_jefe from tbl_usuarios_evalua ue 
    group by ue.documento_jefe, ue.id_cargo_jefe order by ue.nombre_jefe asc")->queryAll();
    $listDataj = ArrayHelper::map($queryj, 'documento_jefe', 'nombre_jefe');

    $listdelete = ['Retiro konecta' => 'Retiro konecta', 'No debe realizar evaluacion' => 'No debe realizar evaluacion'];

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
                    <label style="font-size: 16px;"><em class="fas fa-bolt" style="font-size: 20px; color: #4D83FE;"></em> Seleccionar persona </label>
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
                    <label style="font-size: 16px;"><em class="fas fa-exclamation" style="font-size: 20px; color: #4D83FE;"></em> Notificación </label>
                    <label style="font-size: 14px;">Recuerda que es importante realizar las evaluaciones de todas las personas que tengas a tu cargo. Evalua solo con quien trabajas hace minimo 3 meses </label>
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
                                <?= $form->field($model2, "asunto")->dropDownList($varTipos, ['prompt' => 'Seleccionar Novedades', 'id'=>"idasuntosNcargo", 'onchange'=>'habilitarvar();']) ?>
                                <div class="row">
                                    <div class="col-md-9" id="idbotones" style="display: none">
                                        <?= $form->field($model2, 'cambios')->textInput(['maxlength' => 250,  'id'=>'IdcambiosNcargo', 'placeholder' => 'Digite documento de la persona']) ?>
                                    </div>
                                    <div class="col-md-3" class="text-left" id="idbotones1" style="display: none">
                                        <div onclick="novedadsearch();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbnsearch" >
                                            [ Buscar ] 
                                        </div> 
                                    </div>
                                    <div class="col-md-12" id="idbotones2" style="display: none">
                                        <?= $form->field($model2, "cambios")->dropDownList($listDataj, ['prompt' => 'Seleccionar Jefe', 'onchange' => 'cambiojefe();', 'id'=>"idcambiosJefes"]) ?>
                                    </div>
                                </div>
                                <?= $form->field($model2, 'comentarios')->textInput(['maxlength' => 250,  'id'=>'IdcomentariosNcargo']) ?>
                                <div onclick="savenovedadc();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="idtbnsavec" >
                                    Guardar Novedad
                                </div> 
                                <div onclick="savenovedadctwo();" class="btn btn-primary"  style="display:none; background-color: #337ab7;" method='post' id="idtbnsavectwo" >
                                    Guardar Novedad Cambio
                                </div> 
                            </div>
                        </div>
                    </div>
                </div>
                <br>
                <div class="card1 mb">
                    <label style="font-size: 16px;"><em class="fas fa-exclamation-triangle" style="font-size: 20px; color: #FF6522;"></em> Alerta de eliminación </label>
                    <label style="font-size: 14px;">Esta opción sólo aplica cuando la persona se ha RETIRADO de Konecta ó tiene alguna novedad que le impida realizar el proceso evaluativo (Licencia de maternidad, Incapacidad prolongada, tiempo en el rol, entre otras).</label>
                    <div class="row">
                        <div class="col-md-6">
                            <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 20px; color: #FF6522;"></em> Eliminar persona </label>
                        </div>
                        <div class="col-md-6" class="text-right">
                            <div onclick="opennovedads();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: inline" method='post' id="idtbns1" >
                                  [ + ]
                            </div> 
                            <div onclick="closenovedads();" class="btn btn-primary"  style="background-color: #4298b400; border-color: #4298b500 !important; color:#000000; display: none" method='post' id="idtbns2" >
                                      [ - ]
                            </div> 
                        </div>
                        <div class="col-md-12">
                            <div id="idnovedads" style="display: none">
                                <?= $form->field($model2, "documento")->dropDownList($listData, ['prompt' => 'Seleccionar Una Persona', 'id'=>"idcargos"]) ?>
                                <?= $form->field($model2, "aprobadopor")->dropDownList($listdelete, ['prompt' => 'Seleccionar Novedades...', 'id'=>"idmotivosD", 'onchange'=>'habilitarexponer();']) ?>
                                <div id="exponercoment" style="display: none;">
                                    <input type="text" id="idtxtcomentarios" name="datetimes"  class="form-control" data-toggle="tooltip" title="Ingresar el motivo" placeholder="Ingresar el motivo">
                                </div>
                                <div onclick="savenovedadcs();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="idtbnsavec" >
                                    Guardar Novedad a eliminar
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
    function savenovedadctwo(){
        var varidcambiosJefes = document.getElementById("idcambiosJefes").value;
        var varidasuntosNcargo = document.getElementById("idasuntosNcargo").value;
        var varIdcomentariosNcargo = document.getElementById("IdcomentariosNcargo").value;
        var varidpares = document.getElementById("idpares").value;
        var varvardocument = '<?php echo $vardocument; ?>';
        var vartipo = 2;

        if (varidpares == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Seleccione la persona que no pertenece a tu equipo","warning");
            return; 
        }else{
            $.ajax({
                method: "get",
                url: "ingresarnovedadcargos",
                data: {
                    txtvaridasuntosNcargo : varidasuntosNcargo,
                    txtvarIdcomentariosNovedad : varIdcomentariosNcargo,
                    txtvardocumento : varvardocument,
                    txtvaridpares : varidpares,
                    txtvartipo : vartipo,
                },
                success : function(response){
                    numRta =   JSON.parse(response);
                    window.open('../evaluaciondesarrollo/index','_self');

                }
            });
        }
    };

    function cambiojefe(){
        var varidcambiosJefes = document.getElementById("idcambiosJefes").value;
        var varIdcomentariosNcargo = document.getElementById("IdcomentariosNcargo");
        var varidtbnsavectwo = document.getElementById("idtbnsavectwo");
        var varidtbnsavec = document.getElementById("idtbnsavec");


        if (varidcambiosJefes != "") {
            varIdcomentariosNcargo.value = varidcambiosJefes;

            varidtbnsavectwo.style.display = 'inline';
            varidtbnsavec.style.display = 'none';
        }else{
            varIdcomentariosNcargo.value = "";

            varidtbnsavectwo.style.display = 'none';
            varidtbnsavec.style.display = 'inline';
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

    function habilitarvar(){
        var varidasuntosNcargo = document.getElementById("idasuntosNcargo").value;
        var varidbotones = document.getElementById("idbotones");
        var varidbotones1 = document.getElementById("idbotones1");
        var varidbotones2 = document.getElementById("idbotones2");

        if (varidasuntosNcargo == "Falta persona a mi cargo") {
            varidbotones.style.display = 'inline';
            varidbotones1.style.display = 'inline';
            varidbotones2.style.display = 'none';
            document.getElementById("IdcomentariosNcargo").readOnly = true;
        }else{
            if (varidasuntosNcargo == "Persona no esta a mi cargo") {
                varidbotones.style.display = 'none';
                varidbotones1.style.display = 'none';
                varidbotones2.style.display = 'inline';
                document.getElementById("IdcomentariosNcargo").readOnly = true;
            }else{
                varidbotones.style.display = 'none';
                varidbotones1.style.display = 'none';
                varidbotones2.style.display = 'none';
                document.getElementById("IdcomentariosNcargo").readOnly = false;
                document.getElementById("IdcomentariosNcargo").value = "";
                document.getElementById("IdcambiosNcargo").value = "";
            }
            
        }
    };

    function validarid(){
        var varidpares = document.getElementById("idpares").value;
        var varidmessage1 = document.getElementById("idmessage1");
        var varvardocument = '<?php echo $vardocument; ?>';
        var varButtonSearch = document.getElementById("ButtonSearch");

        $.ajax({
                method: "get",
                url: "verificacargo",
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
            swal.fire("!!! Advertencia !!!","Seleccione una persona","warning");
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

    function savenovedadc(){
        var varIdcomentariosNovedad = document.getElementById("IdcomentariosNcargo").value;
        var varvardocument = '<?php echo $vardocument; ?>';
        var varidasuntosNcargo = document.getElementById("idasuntosNcargo").value;
        var varidpares = document.getElementById("IdcambiosNcargo").value;
        var vartipo = 1;

        $.ajax({
                method: "get",
                url: "ingresarnovedadcargos",
                data: {
                    txtvaridasuntosNcargo : varidasuntosNcargo,
                    txtvarIdcomentariosNovedad : varIdcomentariosNovedad,
                    txtvardocumento : varvardocument,
                    txtvaridpares : varidpares,
                    txtvartipo : vartipo,
                },
                success : function(response){
                    numRta =   JSON.parse(response);
                    window.open('../evaluaciondesarrollo/index','_self');

                }
        });
    };

    function opennovedads(){
        var varidtbns1 = document.getElementById("idtbns1");
        var varidtbns2 = document.getElementById("idtbns2");
        var varidnovedads = document.getElementById("idnovedads");

        varidtbns1.style.display = 'none';
        varidtbns2.style.display = 'inline';
        varidnovedads.style.display = 'inline';

    };

    function closenovedads(){
        var varidtbns1 = document.getElementById("idtbns1");
        var varidtbns2 = document.getElementById("idtbns2");
        var varidnovedads = document.getElementById("idnovedads");

        varidtbns1.style.display = 'inline';
        varidtbns2.style.display = 'none';
        varidnovedads.style.display = 'none';
    };

    function savenovedadcs(){
        var varvardocument = '<?php echo $vardocument; ?>';
        var varidcargos = document.getElementById("idcargos").value;
        var varidmotivosD = document.getElementById("idmotivosD").value;
        var varidtxtcomentarios = document.getElementById("idtxtcomentarios").value;
        var varmotivosretiros = "";

        if (varidcargos == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Seleccione tipo de novedad para eliminar","warning");
            return; 
        }else{
            if (varvardocument == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Seleccione la persona a eliminar","warning");
                return; 
            }else{
                if (varidmotivosD == "No debe realizar evaluacion") {
                    if (varidtxtcomentarios == "") {
                        event.preventDefault();
                        swal.fire("!!! Advertencia !!!","Debe de ingresar un motivo","warning");
                        return; 

                    }else{
                        varmotivosretiros = varidmotivosD+': '+varidtxtcomentarios;
                    }                    
                }else{
                    varmotivosretiros = varidmotivosD;
                }
                

                $.ajax({
                        method: "get",
                        url: "ingresarpersonaeliminar",
                        data: {
                            txtvarvardocument : varvardocument,
                            txtvaridcargos : varidcargos,
                            txtvaridmotivosD : varmotivosretiros,
                        },
                        success : function(response){
                            numRta =   JSON.parse(response);
                            window.open('../evaluaciondesarrollo/index','_self');

                        }
                });  
            }
        }
    };

    function habilitarexponer(){
        var varidmotivosD = document.getElementById("idmotivosD").value;
        var varexponercoment = document.getElementById("exponercoment");
        var varidtxtcomentarios = document.getElementById("idtxtcomentarios").value;

        if (varidmotivosD == "No debe realizar evaluacion") {
            idtxtcomentarios.value = "";
            varexponercoment.style.display = 'inline';
        }else{
            idtxtcomentarios.value = "";
            varexponercoment.style.display = 'none';
        }
    };
</script>