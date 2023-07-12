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

   
    $this->title = 'Mis resultados - Evaluación de Desarrollo';
    $this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $documento = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();
 
    
    $puntaje_final = $sumaTotalEvaluacion;
    $prom_total = $promTotalEvaluacion;
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

        .dataTables_filter input {
            width: 150px; /* Ajusta el ancho del campo de búsqueda según tus necesidades */
            font-size: 15px; /* Ajusta el tamaño de fuente del campo de búsqueda según tus necesidades */
        }

        .size_font_dataTable {
            font-size: 15px;
        }

        .pagination {
            display: inline-block;
            padding-left: 0;
            margin: 20px 0;
            border-radius: 4px;
        }
        
        .column-font-size {
            font-size: 15px;
        }

        body #table_resultados tbody tr td,
        body #table_resultados tbody tr td a,
        body #table_resultados thead tr th a {
            font-size: 15px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: 15px;
            padding: 5px 10px !important;        
        }
        
        .dataTables_wrapper .dataTables_info {
            padding-top: 0em  !important;        
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 0em  !important; 
        }

        .height-text-area {
            width: 570px;
            height: 80px;        
        }

        .table-container {
            margin: 10px;
            padding: 10px;
        }

        .font-size-title{
            font-size: 20px;        
        }

        .font-size-subtitulos {
            font-size: 18px;
        }
        
        .font-size-texto {
            font-size: 17px;
        }
        
        .color-required{
            color: #db2c23;
        }   

        .sin-borde {
            border: none;
            outline: none;
            resize: none;
        }

    </style>
    <script src="../../js_extensions/jquery-2.1.3.min.js"></script>
    <script src="../../js_extensions/highcharts/highcharts.js"></script>
    <script src="../../js_extensions/chart.min.js"></script>
    <script src="../../js_extensions/highcharts/exporting.js"></script>

    

    <!-- Datatable -->
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

    <?php 
    if ($registros_encontrados == 0) {    
    ?>
        <div class="CapaUno" style="display: inline;">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 18px; color: #db2c23;"><em class="fa fa-info-circle" style="font-size: 20px; color: #db2c23;"></em> Aviso </label>
                        <label style="font-size: 15px;"> <?= Yii::t('app', 'Tu usuario no se encuentra registrado para visualizar los resultados de la Evaluación de Desarrollo. Si crees que se trata de un error, por favor comunicarse con el administrador.') ?></label>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    <?php 
    } else if(!$existe_calificacion_total) {   
    ?>
    <div class="CapaDos" style="display: inline;">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 18px; color: #db2c23;"><em class="fa fa-info-circle" style="font-size: 20px; color: #db2c23;"></em> Aviso </label>
                        <label style="font-size: 15px;"> <?= Yii::t('app', 'Aún no es posible visualizar tus resultados, faltan evaluaciones por completar.') ?></label>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    <?php 
        } else {   
    ?>

    <div id="capaCero" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                            <label class="font-size-subtitulos"><em class="fa fa-comment" style="font-size: 20px; color: #C148D0;"></em><?= Yii::t('app', ' Crear Feedback') ?></label>        
                                <?= Html::button('Aceptar', ['value' => url::to(['modalfeedbackcolaborador', 'id_user'=>$id_user]),
                                                'class' => 'btn btn-primary', 'id'=>'modalButton',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Crear_feedback']) 
                                ?> 

                                <?php
                                    Modal::begin([
                                        'header' => '<h4>Agregar Feedback</h4>',
                                        'id' => 'modal',
                                    ]);

                                    echo "<div id='modalContent'></div>";
                                                                                                                
                                    Modal::end(); 
                                ?>
                            </div>
                        </div>
                        <!-- Alertas -->
                        <?php if (Yii::$app->session->hasFlash('success')): 
                            $mensaje = Yii::$app->session->getFlash('success');
                        ?>
                            <script>
                                swal.fire("", '<?= $mensaje ?>', "success");
                            </script>
                        <?php elseif (Yii::$app->session->hasFlash('error')):
                            $mensaje = Yii::$app->session->getFlash('error');
                        ?>
                            <script>
                                swal.fire("", '<?= $mensaje ?>', "error");
                            </script>
                        <?php endif; ?>
                        <!-- Alerta Fin-->

                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label class="font-size-subtitulos"><em class="fa fa-download" style="font-size: 17px; color: #C148D0;"></em> Descargar Reporte</label>
                                <a id="dlink" style="display:none;"></a>
                                <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Aceptar') ?></button>
                                </div>
                            </div>
                        </div>
                        
                    </div>                    
                </div>
            </div>
        </div>
    </div>
    <hr>

    <!-- SECCION FEEDBACKS-->
    <div id="capaUno" style="display: <?= $mostrar_feedbacks ?>">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                <label style="font-size: 20px;"><em class="fa fa-comments" style="font-size: 20px; color: #FFC72C;"></em> Mis Feedbacks: </label>
                    <div class="row">
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label class="font-size-subtitulos"><em class="fa fa-envelope" style="font-size: 17px; color: #C148D0;"></em> Mi Feedback</label>                                
                                <?= Html::textarea('comentarios_colaborador', $feedback_colaborador, ['readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;', 'maxlength' => 1000]) ?>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label class="font-size-subtitulos"><em class="fa fa-envelope" style="font-size: 17px; color: #C148D0;"></em> Feedback Jefe</label>
                                <?= Html::textarea('comentarios_jefe', $feedback_jefe, ['readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;', 'maxlength' => 1000]) ?>
                            </div>
                        </div>

                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label class="font-size-subtitulos"><em class="fa fa-envelope" style="font-size: 17px; color: #C148D0;"></em> Acuerdo Final de Desarrollo</label>
                                <?= Html::textarea('comentarios_acuerdo_final', $feedback_acuerdo_final, ['readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;', 'maxlength' => 1000]) ?>
                            </div>
                        </div>
                        
                    </div>                    
                </div>
            </div>
        </div>
    </div>

    <br>

    <div id="capaDos" style="display: inline">
        <div class="row">
            <div class="col-md-3">
                <div class="card1 mb">
                    <label style="font-size: 20px; margin-bottom:10px;"><em class="fa fa-user" style="font-size: 25px; color: #ffc034;"></em> <?= Yii::t('app', 'Datos Evaluación') ?> </label>
                   
                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-size-subtitulos"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Nombre:') ?> </label>
                            <?= Html::textInput('nombre_usuario',  $nombre_completo, ['readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;']) ?>
                            
                        </div>                        
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-size-subtitulos"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Identificación:') ?> </label>
                            <?= Html::textInput('documento',  $numero_documento, ['readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;']) ?>
                          
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-size-subtitulos"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Cargo Evaluado:') ?> </label>
                            <?= Html::textInput('cargo_usuario',  $cargo_dataform, ['readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;']) ?>
                                                            
                        </div>                       
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-size-subtitulos"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Nombre del Jefe:') ?></label> </label>
                            <?= Html::textInput('nombre_jefe',  $nombre_jefe_dataform, ['readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;']) ?>
                                                         
                        </div>
                    </div>
                    <br>
                    <div class="row">                   
                        <div class="col-md-12">
                            <label class="font-size-subtitulos"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Fecha autoevaluación:') ?> </label>
                            <?= Html::textInput('fecha_autoevaluacion',  $fecha_autoevaluacion, ['class' => 'form-control', 'readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;']) ?>
                                                         
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-size-subtitulos"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Fecha evaluación jefe:') ?></label> </label>
                            <?= Html::textInput('fecha_evaluacion_jefe',  $fecha_evaluacion_jefe, ['readonly' => true, 'class' => 'font-size-texto sin-borde', 'style' => 'width: 100%;']) ?>
                                                        
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                            <label class="font-size-subtitulos"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Puntaje Final:') ?> </label>
                            <?= Html::textInput('puntaje_final',  $puntaje_final, ['readonly' => true, 'class' => 'font-size-texto sin-borde']) ?>
                        </div>
                    </div>
                    <br>
                    <div class="row">
                        <div class="col-md-12">
                        <label class="font-size-subtitulos"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> <?= Yii::t('app', ' Promedio Total:') ?> </label>
                            <?= Html::textInput('prom_total',  $prom_total, ['readonly' => true, 'class' => 'font-size-texto sin-borde']) ?>                                                        
                        </div>
                    </div>
                </div> 
                <br>
            </div>

            <div class="col-md-9">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card1 mb" style="width:100%"> 
                            <label style="font-size: 20px; margin-bottom:10px;"><em class="fa fa-list-alt" style="font-size: 25px; color: #ffc034;"></em> <?= Yii::t('app', 'Reporte Competencias') ?> </label>                      

                            <!-- <label id="emptyMessage" style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'Falta por recopilar las dos evaluaciones') ?></label> -->
                            
                            <div class="table-responsive table-container" id="container_table">                                
                                <table id="table_resultados" class="table table-bordered table-hover center">
                                    
                                </table>    
                            </div>           
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <br>
    </div>
    <br>
 <?php 
    } 
?>

    <script>
        $(document).ready(function() {
            var data = <?php echo json_encode($data_competencias); ?>;
            init_table_resultados(data);
        });

        function init_table_resultados(data) {

            if(data.length > 0) { 
                var tabla_por_competencias = $('#table_resultados').DataTable({
            
                    select: true,
                    "autoWidth": true,
                    data:data,
                    select: false,
                    language: {
                        "decimal": "",
                        "emptyTable": "No hay datos disponibles en la Tabla",
                        "lengthMenu": "<span class='size_font_dataTable'> Cantidad de Datos a Mostrar _MENU_ </span>",
                        "zeroRecords": "No se encontraron datos ",
                        "info": "<span style='font-size: 14px;'> Mostrando _START_ a _END_ de _TOTAL_ registros </span>",
                        "infoEmpty": "<span style='font-size: 14px;'> Mostrando 0 de 0 registros </span>",
                        "infoFiltered": "(Filtrado un _MAX_ total)",
                        "infoPostFix": "",
                        "thousands": ",",
                        "search": "<span style='font-size: 14px;'>Buscar:</span>",
                        "loadingRecords": "Cargando...",
                        "processing":     "Procesando...",
                        "paginate": {
                        "first":      "<span class='size_font_dataTable'> Primero </span>",
                        "last":       "<span class='size_font_dataTable'> Ultimo </span>",
                        "next":       "<span class='size_font_dataTable'> Siguiente </span>",
                        "previous":   "<span class='size_font_dataTable'> Anterior </span>"
                        },
                        "order": [[ 0, "desc" ]],
                        autoWidth : false,
                        "table-layout": "fixed",
                        paging: true
                    },  
                    "lengthMenu": [5, 10, 25, 50],
                    "pageLength": 5,
                    columnDefs: [
                        {
                            targets: 4, // Índice de la columna que quieres cambiar
                            render: function(data, type, row, meta) {
                            var valorColumna = parseFloat(data); // Convierte el valor a tipo numérico

                            // Determina el contenido según el intervalo de valores
                            if (valorColumna >= 1 && valorColumna <= 1.5) {
                                return 'Desarrollo';
                            } else if (valorColumna >= 1.6 && valorColumna <= 2.5) {
                                return 'Satisfactorio';
                            } else if (valorColumna >= 2.6 && valorColumna <= 3) {
                                return 'Potencial';
                            }

                            // Si no se cumple ninguna condición, devuelve el valor original de la columna
                            return data;
                            }
                        }
                    ],
                    columns: [
                        {   title: "Competencia",
                            data: 'competencia',
                            visible : true                   
                        },
                        {   title: "Descriptivo de la competencia",
                            data: 'descripcion_competencia'
                        
                        },
                        {    title: "Calificación cualitativa",
                            data: 'descripcion_respuesta'
                        },
                        {    title: "Calificación",
                            data: 'calificacion_competencia'
                        },
                        {   title: "Ensayis",
                            data: 'calificacion_competencia',
                            visible : false                 
                        }
                    ],  
                    initComplete : function(){

                        }
                    // INITCOMPLETE END
                });

                //Inicializar en la primer página del datatable
                $("#table_resultados").DataTable().page( 0 ).draw( false );
            }
        }

        //Descargar datos de una tabla en formato Excel
        var tableToExcel = (function () {
        var uri = 'data:application/vnd.ms-excel;base64,',
            template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><meta charset="utf-8"/><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>',
            base64 = function (s) {
                return window.btoa(unescape(encodeURIComponent(s)))
            }, format = function (s, c) {
                return s.replace(/{(\w+)}/g, function (m, p) {
                    return c[p];
                })
            }
            return function (table, name) {
                if (!table.nodeType) table = document.getElementById(table)
                var ctx = {
                    worksheet: name || 'Worksheet',
                    table: table.innerHTML
                }
                console.log(uri + base64(format(template, ctx)));
                document.getElementById("dlink").href = uri + base64(format(template, ctx));
                document.getElementById("dlink").download = "Reporte Por Competencias";
                document.getElementById("dlink").target = "_blank";
                document.getElementById("dlink").click();

            }
        })();
        function download(){
            $(document).find('tfoot').remove();
            var name = document.getElementById("name");
            tableToExcel('table_resultados', 'Archivo ', name+'.xls')
            //setTimeout("window.location.reload()",0.0000001);

        }
        var btn = document.getElementById("btn");
        btn.addEventListener("click",download);
        
    </script>
    