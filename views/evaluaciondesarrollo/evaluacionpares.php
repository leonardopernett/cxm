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
    $varnombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $sessiones")->queryScalar();


    $varcargo = Yii::$app->db->createCommand("select distinct concat(posicion,' - ',funcion) from  tbl_usuarios_evalua where documento in ('$vardocument2')")->queryScalar();
    $vartipoeva = Yii::$app->db->createCommand("select tipoevaluacion from tbl_evaluacion_tipoeval where idevaluaciontipo = 4 and anulado = 0")->queryScalar();
    $varnombrepar = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_identificacion = $vardocument2")->queryScalar();

    $last_word_start = strrpos($varnombre, ' ') + 1;
    $last_word = substr($varnombre, $last_word_start);

    $query2 = Yii::$app->db->createCommand("select * from tbl_evaluacion_respuestas2 where anulado = 0")->queryAll();
    $listData2 = ArrayHelper::map($query2, 'idevaluacionrespuesta', 'namerespuesta');

    $varlistbloques = Yii::$app->db->createCommand("select * from tbl_evaluacion_bloques where anulado = 0")->queryAll();

    $varconteobloque = 0;
    $varconteocompetencia = 0;
    $varconteopregunta = 0;

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
            font-family: "Nunito";
            font-size: 150%;    
            text-align: left;    
    }


    .col-sm-6 {
        width: 100%;
    }

    th {
        text-align: left;
        font-size: smaller;
    }

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/Banner_Ev_Desarrollo.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        /*background: #fff;*/
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

    .loader {
      border: 16px solid #f3f3f3;
      border-radius: 50%;
      border-top: 16px solid #3498db;
      width: 80px;
      height: 80px;
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

</style>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>
<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.5.0/Chart.min.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>
<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>
<div id="idCapa" style="display: none">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label><em class="fas fa-spinner" style="font-size: 20px; color: #2CA5FF;"></em> Procesando datos</label>
                <div class="col-md-12">
                    <table>
                    <caption>Tabla datos</caption>
                        <tr>
                            <th id="loader" class="text-center"><div class="loader"> </div></td>
                            <th id="guardarDatos" class="text-center"><label><?= Yii::t('app', ' Guardando datos de la evaluación realizada') ?></label></td>
                        </tr>
                    </table>                                       
                </div>
            </div>
        </div>
    </div>
</div>
<div id="idCapaUno" style="display: inline"> 
    <div id="capaUno" style="display: inline">   
        <div class="row">
            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-user-circle" style="font-size: 20px; color: #2CA5FF;"></em> Usuario Par:</label>
                    <label><?php echo $varnombrepar; ?></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-question-circle" style="font-size: 20px; color: #2CA5FF;"></em> Cargo:</label>
                    <label><?php echo $varcargo; ?></label>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-list" style="font-size: 20px; color: #2CA5FF;"></em> Tipo evaluación:</label>
                    <label><?php echo $vartipoeva; ?></label>
                </div>
            </div>
        </div> 
    </div>
    <hr>
    <div id="capaDos" style="display: inline">   
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label><em class="fas fa-exclamation" style="font-size: 20px; color: #827DF9;"></em> Notificaciones:</label>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-body" style="background-color: #f0f8ff;">Hola <?php echo $last_word; ?>, en este espacio podrás evaluar los comportamientos observables de la persona relacionada, ten en cuenta cómo se desenvuelve en el día a día y en la mayoría de las situaciones laborales.
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-body" style="background-color: #f0f8ff;">Por favor selecciona la opción que consideres en cada comportamiento, responde con objetividad, transparencia y realiza observaciones con asertividad, así contribuirás con su desarrollo personal y profesional.                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php
        foreach ($varlistbloques as $key => $value) {
            $varidbloque = $value['idevaluacionbloques'];  
            $varconteobloque = $varconteobloque + 1;

            $varcolor = null;
            if ($varidbloque == 1) {
                $varcolor = "color: #F9BD4C;";
            }else{
                if ($varidbloque == 2) {
                    $varcolor = "color: #22D7CF;";
                }else{
                    if ($varidbloque == 3) {
                        $varcolor = "color: #49de70;";
                    }
                }
            }
    ?>
        <hr>
        <div id="capaTres" style="display: inline">  
        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?> 
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label><em class="fas fa-cubes" style="font-size: 20px; <?php echo $varcolor; ?>"></em> <?php echo $value['namebloque'].': '; ?></label>
                        <?= $form->field($model, 'valor')->textInput(['maxlength' => 250,  'id'=>'Idbloque'.$varconteobloque, 'class' => 'hidden', 'value' => $value['idevaluacionbloques']]) ?>
                        
                        <div class="row">                    
                            <?php 
                                $varlistcompetencias = Yii::$app->db->createCommand("select ec.idevaluacioncompetencia, ec.namecompetencia from tbl_evaluacion_competencias ec inner join tbl_evaluacion_nivel en on ec.idevaluacionnivel = en.nivel inner join tbl_usuarios_evalua ue on en.cargo = ue.id_dp_posicion where ue.documento in ('$vardocument2') and idevaluacionbloques = $varidbloque and ec.anulado = 0 and en.anulado = 0")->queryAll();
                                
                                foreach ($varlistcompetencias as $key => $value) {
                                    $varidcompetencia = $value['idevaluacioncompetencia'];

                                    if ($vardocument2 == "43271670") {
                                        $varpregunta = Yii::$app->db->createCommand("select idevaluacionpregunta, namepregunta from tbl_evaluacion_comportamientos where idevaluacioncompetencia = $varidcompetencia and permisos in (0,1) and anulado = 0")->queryAll();
                                    }else{
                                        if ($vardocument == "1024587498" || $vardocument == "1001525004" || $vardocument == "1022382442" || $vardocument == "1026288508" || $vardocument == "1013623333" || $vardocument == "84455793" || $vardocument == "80842889" || $vardocument == "43996192" || $vardocument == "1093754569" || $vardocument == "1044504945" || $vardocument == "1128439596" || $vardocument == "71526676" || $vardocument == "1128450824" || $vardocument == "1065583210" || $vardocument == "1044120677" || $vardocument == "1014197603" || $vardocument == "70330633" || $vardocument == "53103081" || $vardocument == "1037448603") {
                                            $varpregunta = Yii::$app->db->createCommand("select idevaluacionpregunta, namepregunta from tbl_evaluacion_comportamientos where idevaluacioncompetencia = $varidcompetencia and permisos in (0,2) and anulado = 0")->queryAll();
                                        }else{
                                            $varpregunta = Yii::$app->db->createCommand("select idevaluacionpregunta, namepregunta from tbl_evaluacion_comportamientos where idevaluacioncompetencia = $varidcompetencia and permisos in (0) and anulado = 0")->queryAll();
                                        }   
                                    }
                                    

                                    $varconteocompetencia = $varconteocompetencia + 1;
                            ?>
                            <br>
                            <div class="col-md-12">
                                <div class="card1 mb">
                                    <label style="font-size: 16px;"><em class="fas fa-bookmark" style="font-size: 17px; <?php echo $varcolor; ?>"></em> <?php echo $value['namecompetencia'].''; ?></label>
                                    <?= $form->field($model, 'valor')->textInput(['maxlength' => 250,  'id'=>'IdCompetencia'.$varconteocompetencia.$varconteobloque, 'class' => 'hidden', 'value' => $value['idevaluacioncompetencia']]) ?>   
                                    <?php 
                                        foreach ($varpregunta as $key => $value) {   
                                            $varconteopregunta = $varconteopregunta + 1;                         
                                    ?>
                                    <div class="row">                                    
                                        <div class="col-md-6">
                                            <label id="<?php echo 'idtext'.$varconteopregunta.$varconteocompetencia.$varconteobloque; ?>" style="font-size: 16px; font-weight: normal;"> <?php echo '* '.$value['namepregunta']; ?></label> 
                                            <?= $form->field($model, 'namerespuesta')->textInput(['maxlength' => 250,  'id'=>'Idpre'.$varconteopregunta.$varconteocompetencia.$varconteobloque, 'class' => 'hidden', 'value' => $value['idevaluacionpregunta']]) ?>                                       
                                        </div>
                                        <div class="col-md-6">                                    
                                            <?php  echo $form->field($model, 'idevaluacionrespuesta')->dropDownList($listData2, ['prompt' => 'Seleccionar Respuesta', 'id'=>'Idrta'.$varconteopregunta.$varconteocompetencia.$varconteobloque])?>  
                                        </div>
                                    </div>                            
                                    <?php 
                                        } 
                                    ?>
                                </div>
                                <br>
                            </div>
                            <br>
                            <?php } ?>                        
                        </div>
                    </div>
                </div>
            </div>
        <?php ActiveForm::end(); ?>
        </div>
    <?php } ?>
    <hr>
    <div id="capaCuatro" style="display: inline"> 
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-comments" style="font-size: 17px; color: #8B70FA;"></em> Ingresa aquí las sugerencias y felicitaciones que consideras pueden potenciar las competencias de la persona que estás evaluando </label>
                    <?= $form->field($model, 'namerespuesta')->textInput(['maxlength' => 250,  'id'=>'Idcomentarios']) ?>
                </div>
            </div>
        </div>
    </div>
    <hr>
    <div id="capaCinco" style="display: inline"> 
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-save" style="font-size: 17px; color: #FFC72C;"></em> Guardar y Enviar: </label> 
                                <div onclick="generated();" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
                                  Guardar y Enviar
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                                <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                'style' => 'background-color: #707372',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Regresar']) 
                                ?>                            
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-eye" style="font-size: 17px; color: #FFC72C;"></em> Crear Novedad: </label> 
                                <?= Html::button('Crear Novedad', ['value' => url::to(['evaluaciondesarrollo/novedadpares','idvardocumentpar' => $vardocument2]), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Crear Novedad', 'style' => 'background-color: #4298b4']) 
                                ?> 

                                <?php
                                    Modal::begin([
                                      'header' => '<h4></h4>',
                                      'id' => 'modal1',
                                      //'size' => 'modal-lg',
                                    ]);

                                    echo "<div id='modalContent1'></div>";
                                                          
                                    Modal::end(); 
                                ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <hr>
</div>

<script type="text/javascript">
    function generated(){
        var varconteobloque = '<?php echo $varconteobloque; ?>';
        var varconteocompetencia = '<?php echo $varconteocompetencia; ?>';
        var varconteopregunta = '<?php echo $varconteopregunta; ?>';
        var vardocumento = '<?php echo $vardocument; ?>';
        var vardocument2 = '<?php echo $vardocument2; ?>';
        var numRta = 0;
        var varidCapa = document.getElementById("idCapa");
        var varidCapaUno = document.getElementById("idCapaUno");

        varidbloque = 0;
        for (var i = 0; i < varconteobloque; i++) {
            varbloque = i + 1;
            var varidbloque = document.getElementById('Idbloque'+varbloque).value;
            // console.log('El bloque es: '+varidbloque);

            varcompetencia = 0;
            for (var j = 0; j < varconteocompetencia; j++) {
                varcompetencia = j + 1;
                var varcompara = document.getElementById('IdCompetencia'+varcompetencia+varidbloque);                
                
                if (varcompara != null) {
                    var varidcompetencia = document.getElementById('IdCompetencia'+varcompetencia+varidbloque).value;
                    // console.log('La competencia es: '+varidcompetencia);

                    for (var k = 0; k < varconteopregunta; k++) {
                        varpreguntas = k + 1;

                        var varpreg = document.getElementById('Idpre'+varpreguntas+varcompetencia+varidbloque);

                        if (varpreg != null) {
                            var varidtext = document.getElementById('idtext'+varpreguntas+varcompetencia+varidbloque).innerHTML;
                            // console.log(varidtext);
                            var varidpreg = document.getElementById('Idpre'+varpreguntas+varcompetencia+varidbloque).value;
                            // console.log('La pregunta es: '+varidpreg);
                            var varidrta = document.getElementById('Idrta'+varpreguntas+varcompetencia+varidbloque).value;
                            // console.log('La respuesta es: '+varidrta);

                            if (varidrta == "") {
                                event.preventDefault();
                                swal.fire("!!! Advertencia !!!","Ingrese una respuesta a la pregunta: "+varidtext,"warning");
                                document.getElementById('Idrta'+varpreguntas+varcompetencia+varidbloque).style.backgroundColor = '#f7b9b9';
                                return; 
                            }else{
                                document.getElementById('Idrta'+varpreguntas+varcompetencia+varidbloque).style.backgroundColor = '#fff';

                                $.ajax({
                                    method: "get",
                                    url: "createautopares",
                                    data: {
                                        txtvardocumento : vardocumento,
                                        txtvardocument2 : vardocument2,
                                        txtvaridbloque : varidbloque,
                                        txtvaridcompetencia : varidcompetencia,
                                        txtvaridpreg : varidpreg,
                                        txtvaridrta : varidrta,
                                    },
                                    success : function(response){ 
                                            numRta =   JSON.parse(response);    
                                            
                                    }
                                });
                            }
                        }
                    }
                }               
                
            }
        }
        

        varidCapa.style.display = 'inline';
        varidCapaUno.style.display = 'none';

        var varocmentario = document.getElementById("Idcomentarios").value;
        $.ajax({
            method: "get",
            url: "createpardesarrollo",
            data: {
                txtvarocmentario : varocmentario,
                txtvardocumento : vardocumento,
                txtvardocument2 : vardocument2,
            },
            success : function(response){
                numRta2 =   JSON.parse(response);
                window.open('../evaluaciondesarrollo/index','_self');

            }
        });
    };


</script>