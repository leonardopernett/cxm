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

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;
    $nombre_evaluacion = $vartipoeva['tipoevaluacion'];
    $id_nombre_evalua = $vartipoeva['id'];

    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    // $varnombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $varnombre = $datos_usuario['nombre_completo'];
    $varcargo = $datos_usuario['cargo']; 
    
    $nombre_jefe = "NO EXISTE UN JEFE DIRECTO";

    if(!empty($datos_jefe)) {
        $nombre_jefe =$datos_jefe[0]['nom_jefe']; //Toma el primer jefe que encuentra
    }
   
    
    $area_operacion = $datos_usuario['area_operacion'];
    $ciudad = $datos_usuario['ciudad'];
    $sociedad = $datos_usuario['sociedad'];

    $contador = 0; // Inicializar el contador para preguntas y respuestas

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
            border-radius: 5px;
            box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
        }

        .loader {
        border: 16px solid #f3f3f3;
        border-radius: 50%;
        border-top: 16px solid #3498db;
        width: 80px;
        height: 80px;
        -webkit-animation: spin 2s linear infinite;
        animation: spin 2s linear infinite;
        }

        @-webkit-keyframes spin {
        0% { -webkit-transform: rotate(0deg); }
        100% { -webkit-transform: rotate(360deg); }
        }

        @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
        }

        .color-required{
            color: #db2c23;
        }

    </style>
    <script src="../../js_extensions/jquery-2.1.3.min.js"></script>
    <script src="../../js_extensions/highcharts/highcharts.js"></script>
    <script src="../../js_extensions/chart.min.js"></script>
    <script src="../../js_extensions/highcharts/exporting.js"></script>
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
    <br><br>

    <div id="idCapaUno" style="display: inline"> 
        <div id="capaUno" style="display: inline">   
            <div class="row">
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label><em class="fas fa-user-circle" style="font-size: 20px; color: #2CA5FF;"></em> Usuario:</label>
                        <label><?= Yii::t('app',  $datos_usuario['nombre_completo']) ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label><em class="fas fa-question-circle" style="font-size: 20px; color: #2CA5FF;"></em> Cargo:</label>
                        <label><?= Yii::t('app',  $datos_usuario['cargo']) ?></label>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card1 mb">
                        <label><em class="fas fa-list" style="font-size: 20px; color: #2CA5FF;"></em> Tipo evaluación:</label>
                        <label><?= Yii::t('app', $nombre_evaluacion) ?></label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <br>
    <!-- SALUDO DE BIENVENIDA ------------------------------------------------------------>
    <div id="capaDos" style="display: inline">   
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 18px;"><em class="fas fa-star" style="font-size: 30px; color: #ffc034;"></em> <?= Yii::t('app', 'Hola Líder K') ?> </label>
                    <?php
                    $contenido1 = 'Hoy queremos seguir transformando vidas positivamente, es por esto que, te invitamos a desarrollar esta <span style="font-style: italic;">autovaloración</span>, para conocer como vives las competencias organizacionales. ';
                    ?> 
                    <?= Html::tag('p', Html::decode(Html::encode($contenido1))) ?>                    
                </div>
            </div>
        </div>
        <br>
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">                                  
                    <?php
                        $contenido2 = '<em class="fa fa-info-circle" style="font-size: 20px; color: #827DF9;"></em> Te invitamos a responder cada una de las preguntas teniendo en cuenta tu proceso de crecimiento y evolución.';
                        ?> 
                        <?= Html::tag('p', Html::decode(Html::encode($contenido2)))
                    ?>                        
                </div>
            </div>
        </div>
    </div>
    <br>
    <!-- FORMULARIO AUTOEVALUACION ----------------------------------------------------------------------------------------->
    <?php $form = ActiveForm::begin([
        'action' => ['crearautoevaluacion'], 
        'method' => 'post'
    ]); ?> 
    <div id="capaTres" style="display: inline">
            <!-- SECCION DATOS PERSONALES ---------------------------------------------------------------------------------------------->
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 18px; margin-bottom:10px;"><em class="fa fa-user" style="font-size: 25px; color: #ffc034;"></em> <?= Yii::t('app', 'Datos Personales') ?> </label>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Nombre de Jefe Inmediato:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>'', 'readonly' => true, 'value'=> $nombre_jefe])?>
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Área o Campaña:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>'', 'readonly' => true, 'value'=> $area_operacion])?>                                
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Ciudad:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>'', 'readonly' => true, 'value'=> $ciudad])?>                                
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Razon Social:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>'', 'readonly' => true, 'value'=> $sociedad])?>
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Tiempo total realizando el cargo administrativo:') ?><span class="color-required"> *</span></label> </label>
                                <?= $form->field($model, 'id_evaluacionnombre', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList($lista_tiempo_en_cargo, [
                                                                    'id' => 'id_tiempo_laboral',
                                                                    'prompt'=>'Seleccionar...',                                                                    
                                                                ])->label(''); 
                                                                ?>                                
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <br>

            <!-- SECCION COMPETENCIAS ----------------------------------------------------------------------------------------->                        
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <div class="row">
                            <div class="col-md-12">
                            <label style="font-size: 18px; margin-bottom:10px;"><em class="fas fa-cubes" style="font-size: 30px; color: #ffc034;"></em> <?= Yii::t('app', 'Competencias Organizacionales') ?> </label>                           
                            
                            <?php
                                $contenido = '<em class="fa fa-info-circle" style="font-size: 20px; color: #827DF9;"></em> A continuación encontrarás las <span style="font-style: italic;">competencias organizacionales</span>. Debes seleccionar una valoración para cada una de estas competencias, siguiendo este modelo de medición: ';
                                ?> 
                                <?= Html::tag('p', Html::decode(Html::encode($contenido))) ?>
                            </div>
                        </div>
                        <br>
                        <!-- TABLA MODELO DE RESPUESTAS -------------------------->
                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-hover center">
                                    <thead style="background-color: #F5F3F3;">
                                        <tr>
                                            <th scope="col"><label style="font-size: 15px; text-align: center;"><?= Yii::t('app', 'Nombre') ?></label></th>
                                            <th scope="col"><label style="font-size: 15px; text-align: center;"><?= Yii::t('app', 'Valor') ?></label></th>
                                            <th scope="col"><label style="font-size: 15px; text-align: center;"><?= Yii::t('app', 'Descripción') ?></label></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($array_respuestas as $fila): ?>
                                            <tr>
                                                <td><label style="font-size: 15px; text-align: center;"><?= $fila['nombre_respuesta']; ?></label></td>
                                                <td><label style="font-size: 15px; text-align: center;"><?= $fila['valor']; ?></label></td>
                                                <td><label style="font-size: 15px; text-align: center;"><?= $fila['descripcion_respuesta']; ?></label></td>                                        
                                            </tr>
                                        <?php endforeach; ?>                           
                                    </tbody>
                                </table>
                            </div>
                            </div>                                                        
                        </div>
                        <br>  

                        <!-- PREGUNTAS Y RESPUESTAS (COMPETENCIAS ORGANIZACIONALES) -------------------------->
                        <?php foreach ($array_preguntas as $fila): 
                            $id_pregunta = 'id_pregunta_selected_' . $contador;
                            $id_respuesta = 'id_respuesta_selected_' . $contador;
                            $id_observacion = 'id_observacion_' . $contador;
                            $id_acuerdos = 'id_acuerdos_' . $contador;
                            $id_label_pregunta = 'id_label_' . $contador;
                        ?>
                        <div class="row"> 
                            <div class="col-md-12">
                                <div class="card1 mb" style="font-size: 16px;">
                                        <label  id="<?php echo $id_label_pregunta; ?>" style="font-size: 18px; color: #002855; "><em class="fas fa-bookmark" style="font-size: 20px; color: #22D7CF;"></em> <?= Yii::t('app', $fila['nombrepregunta']) ?></label>
                                        <?= $form->field($model_rta_form, 'id_pregunta', ['options' => ['class' => 'hidden']])
                                            ->textInput(['id' => $id_pregunta, 'value' => $fila['id_pregunta']])
                                            ->label(false);
                                        ?>
                                        <div class="row">                                    
                                            <div class="col-md-6">
                                                <br>
                                                <label  style="font-size: 16px;"><?= Yii::t('app', $fila['descripcionpregunta']) ?></label>                                            
                                            </div>
                                            <div class="col-md-6"> 
                                                <label  style="font-size: 16px;"><em class="fas fa-comments" style="font-size: 20px; color: #8B70FA;"></em><?= Yii::t('app', ' Respuesta: ') ?><span class="color-required"> *</span></label>                                    
                                                <?=  $form->field($model_rta_form, 'id_respuesta', ['labelOptions' => ['class' => 'col-md-12']])->dropDownList($opcion_respuestas,
                                                                        [
                                                                            'id' => $id_respuesta,
                                                                            'prompt'=>'Seleccionar...',
                                                                            
                                                                        ]
                                                                    )->label(''); 
                                                ?>  
                                            </div>
                                        </div>                                  
                                        <hr>
                                        <div class="row">
                                            <div class="col-md-6">
                                                    <label style="font-size: 16px;"><em class="fas fa-comments" style="font-size: 20px; color: #8B70FA;"></em> Observaciones y/o Acuerdos:</label><span class="color-required"> *</span>
                                                    <?= $form->field($model_rta_form, 'observacion', ['labelOptions' => ['class' => 'col-md-12']])->textArea(['maxlength' => 300,  'id'=>$id_observacion]) ?>                                                
                                            </div>
                                        
                                        
                                            <div class="col-md-6">
                                                    <label style="font-size: 16px;"><em class="fas fa-comments" style="font-size: 20px; color: #8B70FA;"></em> Acuerdos para el desarrollo:</label><span class="color-required"> *</span>
                                                    <?= $form->field($model_rta_form, 'acuerdos', ['labelOptions' => ['class' => 'col-md-12']])->textArea(['maxlength' => 300,  'id'=>$id_acuerdos]) ?>                                                
                                            </div>
                                        </div>
                                 
                                    
                                </div>
                                <br>
                            </div>
                            <br>                       
                        </div>
                        <?php                       
                        $contador++; // Incrementar el contador
                        endforeach; ?>
                    </div>
                </div>
            </div>
    </div>
    

    <hr>
    <!-- SECCION ACCIONES-------------------------->
    <div id="capaCinco" style="display: inline"> 
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-save" style="font-size: 17px; color: #FFC72C;"></em> Guardar y Enviar: </label> 
                                <?= Html::button('Guardar y enviar', [
                                    'class' => 'btn btn-primary',
                                    'style' => 'display:inline; background-color: #337ab7;',
                                    'onclick' => 'enviar_formulario()' 
                                ]) ?>                                      
                                
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
                                <?= Html::button('Crear Novedad', ['value' => url::to(['evaluaciondesarrollo/novedadauto']), 'class' => 'btn btn-success', 'id'=>'modalButton1', 'data-toggle' => 'tooltip', 'title' => 'Crear Novedad', 'style' => 'background-color: #4298b4']) 
                                ?> 

                                <?php
                                    Modal::begin([
                                      'header' => '<h4></h4>',
                                      'id' => 'modal1',
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
<?php ActiveForm::end(); ?>

<script>

    function enviar_formulario() { 
        var id_user = '<?php echo $id_usuario_logueado; ?>';
        var id_evalua_nombre = '<?php echo $id_evaluacion_actual; ?>';
        var id_tipo_evalua = '<?php echo $id_nombre_evalua; ?>';

        var selector_tiempo_laborado = document.getElementById('id_tiempo_laboral');     
        var tiempo_laborado = selector_tiempo_laborado.value;
        var contador_pregunta_respuesta = parseInt('<?php echo $contador; ?>');   
        var datosArray = [];

        //Validacion respuesta
        if (tiempo_laborado == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Ingrese tiempo_laborado","warning");
                selector_tiempo_laborado.style.backgroundColor = '#f9dfdf';
                return; 
            } else {
                selector_tiempo_laborado.style.backgroundColor = '#fff';
            }
        
        for (let index = 0; index < contador_pregunta_respuesta; index++) {

            var selector_contenedor_html = document.getElementById('id_label_'+index);
            var selector_pregunta = document.getElementById('id_pregunta_selected_'+index);      
            var selector_rta = document.getElementById('id_respuesta_selected_'+index);
            var selector_observacion = document.getElementById('id_observacion_'+index);
            var selector_acuerdos = document.getElementById('id_acuerdos_'+index);

            var contenedor_html = selector_contenedor_html.textContent;
            var pregunta = selector_pregunta.value;      
            var rta = selector_rta.value;
            var observacion = selector_observacion.value;
            var acuerdos = selector_acuerdos.value;
                
            //Validacion respuesta
            if (rta == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Ingrese una respuesta a la competencia: "+contenedor_html,"warning");
                selector_rta.style.backgroundColor = '#f9dfdf';
                return; 
            } else {
                selector_rta.style.backgroundColor = '#fff';
            }

            //Validacion Observacion
            if (observacion == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Ingrese una Observación y/o Acuerdo a la competencia: "+contenedor_html,"warning");
                selector_observacion.style.backgroundColor = '#f9dfdf';
                return; 
            } else {
                selector_observacion.style.backgroundColor = '#fff';
            }

            //Validacion Acuerdos
            if (acuerdos == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Ingrese un Acuerdo de desarrollo para la competencia: "+contenedor_html,"warning");
                selector_acuerdos.style.backgroundColor = '#f9dfdf';
                return; 
            } else {
                selector_acuerdos.style.backgroundColor = '#fff';
            }

            var datos = {
                id_pregunta: pregunta,
                id_respuesta: rta,
                observaciones: observacion,
                acuerdos: acuerdos
            };

            datosArray.push(datos);    
            
        }

        //var jsonData = JSON.stringify(datosArray);

        // Envío de datos por AJAX
        $.ajax({
        url: 'crearautoevaluacion',
        method: 'post',
        data: {            
            id_user : id_user,
            id_evalua_nombre : id_evalua_nombre,
            id_tipo_evalua : id_tipo_evalua,
            array_preguntas_rtas : datosArray,
            _csrf:'<?=\Yii::$app->request->csrfToken?>'
        },
        success: function(response) {
            console.log("response", response);
            if (response.status === 'error') {
                swal.fire("",response.data,"error");
                return;
            } 
            // Procesar la respuesta exitosa
            if (response.status === 'success') {                           

                swal.fire("",response.data,"success");
                setTimeout(function() {
                    window.location.href = 'index';
                }, 1000); // 1000 milisegundos= 1 segundos
                return;
            }                
        },
        error: function(xhr, status, error) {
            // Manejar errores en caso de fallo en la petición AJAX
            console.log(error);
        }
        });
            }


</script>

