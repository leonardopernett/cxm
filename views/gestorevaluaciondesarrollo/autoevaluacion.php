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

    $vardocument = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $varnombre = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $sessiones")->queryScalar();
    $varcargo = 'Prueba de Cargo'; // Buscarlo si es jefe o colaboradore para traerme el cargo
    $vartipoeva = Yii::$app->db->createCommand("select tipoevaluacion from tbl_evaluacion_tipoeval where idevaluaciontipo = 1 and anulado = 0")->queryScalar();

    $option_nombre_evaluacion = [
        '1' => 'En Desarrollo',
        '2' => 'Satisfactorio',
        '3' => 'Potencial',
    ];
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
                        <label><?php echo $varnombre; ?></label>
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
    <!-- SECCIÓN DE AUTOEVALUACION ------------------------------------------------------------>
    <?php $form = ActiveForm::begin([
            'layout' => 'horizontal',
            'fieldConfig' => [
                'inputOptions' => ['autocomplete' => 'off']
            ]
            ]); ?> 

    <div id="capaTres" style="display: inline">         
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                    <label style="font-size: 18px; margin-bottom:10px;"><em class="fa fa-user" style="font-size: 25px; color: #ffc034;"></em> <?= Yii::t('app', 'Datos') ?> </label>
                        
                        <div class="row">
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Nombre de Jefe Inmediato:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>'', 'readonly' => true, 'value'=> 'Iveht Teresa Romero'])?>
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Área o Campaña:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>'', 'readonly' => true, 'value'=> 'Experience Learning'])?>                                
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Ciudad:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>'', 'readonly' => true, 'value'=> 'Medellín'])?>                                
                            </div>
                        </div>
                        <br>

                        <div class="row">
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Razon Social:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>'', 'readonly' => true, 'value'=> 'Comdata Colombia S.A.S.'])?>
                            </div>
                            <div class="col-md-4">
                                <label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Indique el tiempo total realizando el cargo administrativo:') ?> </label>
                                <?= $form->field($model, 'nombrepregunta', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => true,'id'=>'id_nom_pregunta','placeholder'=>''])?>                                
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>

            <br>

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

                        <div class="row">
                            <div class="col-md-12">
                                <table class="table table-bordered table-striped table-hover center">
                                <thead>
                                    <tr>
                                        <th scope="col"><label style="font-size: 15px; text-align: center;"><?= Yii::t('app', 'Nombre') ?></label></th>
                                        <th scope="col"><label style="font-size: 15px; text-align: center;"><?= Yii::t('app', 'Valor') ?></label></th>
                                        <th scope="col"><label style="font-size: 15px; text-align: center;"><?= Yii::t('app', 'Descripción') ?></label></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><label style="font-size: 15px; text-align: center;">En Desarrollo</label></td>
                                        <td><label style="font-size: 15px; text-align: center;">1</label></td>
                                        <td><label style="font-size: 15px; text-align: center;">La competencia esta en un nivel basico de desarrollo</label></td>
                                    </tr>
                                    <tr>
                                        <td><label style="font-size: 15px; text-align: center;">Satisfactorio</label></td>
                                        <td><label style="font-size: 15px; text-align: center;"> 2</label></td>
                                        <td><label style="font-size: 15px; text-align: center;">La competencia esta en un nivel satisfactorio de desarrollo</label></td>
                                    </tr>
                                    <tr>
                                        <td><label style="font-size: 15px; text-align: center;">Potencial</label></td>
                                        <td><label style="font-size: 15px; text-align: center;"> 3</label></td>
                                        <td><label style="font-size: 15px; text-align: center;"> La competencia esta en un nivel esperado y potencial de desarrollo</label></td>
                                    </tr>
                                </tbody>
                                </table>                            
                            </div>                                                        
                        </div>
                        <br>      
                                    
                        <div class="row"> 
                            <div class="col-md-12">
                                <div class="card1 mb" style="font-size: 16px;">
                                    <label style="font-size: 18px;"><em class="fas fa-bookmark" style="font-size: 20px; color: #22D7CF;"></em> <?= Yii::t('app', ' Brindamos Soluciones: ') ?></label>
                                    <div class="row">                                    
                                        <div class="col-md-6">
                                            <label  style="font-size: 16px;"><?= Yii::t('app', 'Obtener información relevante e identificar los elementos críticos de las situaciones, sus implicaciones y detalles relevantes para elegir acciones apropiadas, propones soluciones y hace que las cosas pasen.') ?></label> 
                                            
                                        </div>
                                        <div class="col-md-6"> 
                                        <label  style="font-size: 17px;"><?= Yii::t('app', 'Respuesta: ') ?></label>                                    
                                        <?=  $form->field($model, 'id_evaluacionnombre', ['labelOptions' => ['class' => 'col-md-12'],
                                                                'template' => $template])->dropDownList($option_nombre_evaluacion,
                                                                [
                                                                    'id' => 'id_nombre_evaluacion',
                                                                    'prompt'=>'Seleccionar...',
                                                                    
                                                                ]
                                                            )->label(''); 
                                                        ?>  
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-12">
                                                <label style="font-size: 16px;"><em class="fas fa-comments" style="font-size: 20px; color: #8B70FA;"></em> Observaciones y/o Acuerdos:</label>
                                                <?= $form->field($model, 'nombrepregunta')->textInput(['maxlength' => 250,  'id'=>'Idcomentarios']) ?>
                                            
                                        </div>
                                    </div>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-12">
                                                <label style="font-size: 16px;"><em class="fas fa-comments" style="font-size: 20px; color: #8B70FA;"></em> Acuerdos para el desarrollo:</label>
                                                <?= $form->field($model, 'nombrepregunta')->textArea(['maxlength' => 250,  'id'=>'Idcomentarios']) ?>
                                            
                                        </div>
                                    </div>
                                                               
                                    
                                </div>
                                <br>
                            </div>
                            <br>                       
                        </div>

                        <div class="row"> 
                            <div class="col-md-12">
                                <div class="card1 mb" style="font-size: 16px;">
                                    <label style="font-size: 18px;"><em class="fas fa-bookmark" style="font-size: 20px; color: #22D7CF;"></em> <?= Yii::t('app', ' Nos Transformamos: ') ?></label>
                                    <div class="row">                                    
                                        <div class="col-md-6">
                                            <label  style="font-size: 16px;"><?= Yii::t('app', 'Capacidad de anticiparse y aprovechar las oportunidades de cambio y realizar transformaciones exitosas en la organización.') ?></label> 
                                            
                                        </div>
                                        <div class="col-md-6"> 
                                        <label  style="font-size: 16px;"><?= Yii::t('app', 'Respuesta: ') ?></label>                                    
                                        <?=  $form->field($model, 'id_evaluacionnombre', ['labelOptions' => ['class' => 'col-md-12'],
                                                                'template' => $template])->dropDownList($option_nombre_evaluacion,
                                                                [
                                                                    'id' => 'id_nombre_evaluacion',
                                                                    'prompt'=>'Seleccionar...',
                                                                    
                                                                ]
                                                            )->label(''); 
                                                        ?>  
                                        </div>
                                    </div>
                                    <hr>
                                    <div class="row">
                                        <div class="col-md-6">
                                                <label style="font-size: 16px;"><em class="fas fa-comments" style="font-size: 20px; color: #8B70FA;"></em> Observaciones y/o Acuerdos:</label>
                                                <?= $form->field($model, 'nombrepregunta')->textArea(['maxlength' => 250,  'id'=>'Idcomentarios']) ?>
                                            
                                        </div>
                                    
                                   
                                        <div class="col-md-6">
                                                <label style="font-size: 16px;"><em class="fas fa-comments" style="font-size: 20px; color: #8B70FA;"></em> Acuerdos para el desarrollo:</label>
                                                <?= $form->field($model, 'nombrepregunta')->textArea(['maxlength' => 250,  'id'=>'Idcomentarios']) ?>
                                            
                                        </div>
                                    </div>
                                                               
                                    
                                </div>
                                <br>
                            </div>
                            <br>                       
                        </div>
                                

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
                                <div onclick="" class="btn btn-primary"  style="display:inline; background-color: #337ab7;" method='post' id="botones2" >
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


