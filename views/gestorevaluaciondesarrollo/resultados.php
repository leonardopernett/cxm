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


    $this->title = 'Resultados Evaluación de Desarrollo';
    $this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-12">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;

    $documento = Yii::$app->db->createCommand("select usua_identificacion from tbl_usuarios where usua_id = $sessiones")->queryScalar();


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
            font-size: 14px;
        }

        body #table_resultados tbody tr td,
        body #table_resultados tbody tr td a,
        body #table_resultados thead tr th a {
            font-size: 15px !important;
        }

        body #table_competencias tbody tr td,
        body #table_competencias tbody tr td a,
        body #table_competencias thead tr th a {
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
        <div class="CapaCero" style="display: inline;">
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
    } else if(empty($personas_a_cargo)) {
    ?>
    <div class="CapaUno" style="display: inline;">
            <div class="row">
                <div class="col-md-12">
                    <div class="card1 mb">
                        <label style="font-size: 18px; color: #db2c23;"><em class="fa fa-info-circle" style="font-size: 20px; color: #db2c23;"></em> Aviso </label>
                        <label style="font-size: 15px;"> <?= Yii::t('app', 'Tu usuario no tiene personal a cargo. Si crees que se trata de un error, por favor comunicarse con el administrador.') ?></label>
                    </div>
                </div>
            </div>
        </div>
        <hr>
    <?php
        } else {
    ?>
    <div id="capaUno" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1">
                    <label style="font-size: 20px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                    <div class="row">  
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fa fa-download" style="font-size: 17px; color: #FFC72C;"></em> Ver Feedbacks</label>
                                <?= Html::a('Aceptar', ['feedbackfinal', 'id_jefe'=> $id_user], [
                                    'class' => 'btn btn-primary',
                                    'style' => 'display:inline; background-color: #337ab7;',
                                    'title' => 'ver_feedback_final'                                    
                                ]) ?> 
                            </div>
                        </div> 
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fa fa-download" style="font-size: 17px; color: #FFC72C;"></em> Ver Reporte por Competencias</label>
                                <?= Html::button('Aceptar', [
                                    'class' => 'btn btn-primary',
                                    'style' => 'display:inline; background-color: #337ab7;',
                                    'onclick' => 'init_table_competencias((' . $data_competencias . '))' 
                                ]) ?> 
                            </div>
                        </div>   
                                           
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fa fa-download" style="font-size: 17px; color: #FFC72C;"></em> Descargar Reporte General</label>
                                <a id="dlink" style="display:none;"></a>
                                <button  class="btn btn-info" style="background-color: #4298B4" id="btn"><?= Yii::t('app', ' Aceptar') ?></button>
                            </div>
                        </div>
                    </div>
                    <br>
                </div>
            </div>
        </div>
    </div>

    <hr>

    <div id="capaDos" style="display: inline">
        <div class="row">           

            <div class="col-md-12">
                <div class="row">
                    <div class="col-md-12">
                        <div class="card1 mb" style="width:100%">
                            <label style="font-size: 20px; margin-bottom:10px;"><em class="fa fa-user" style="font-size: 25px; color: #ffc034;"></em> <?= Yii::t('app', 'Reporte General Evaluación de Desarrollo') ?> </label>

                            <label id="emptyMessageTableResultados" style="font-size: 17px;"><em class="fas fa-info-circle" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'No hay datos para mostrar.') ?></label>

                            <div class="table-responsive table-container" id="container_table">
                                <table id="table_resultados" class="table table-bordered table-hover center">

                                </table>
                            </div>
                        </div>
                        <!-- Modal Ingresar Comentarios Feedback -->
                        <?php

                          $form = ActiveForm::begin([
                              'id' => 'form_crear_feedback',
                          ]);

                            Modal::begin([
                                'id' => 'modalCrearFeedback',
                                'header' => '<h4>Crear Feedback</h4>',
                                'footer' => Html::button('Enviar', ['class' => 'btn btn-success btn-block', 'style'=>'margin-top: 1.5%; padding:0.5%', 'onClick' => 'crearFeedbackEvaluacion();']),
                            ]);


                            echo '<div class="row" id="modal_crear_feedback">';
                            echo '<div class="col-md-12" style="margin-top: 20px">';
                            echo '<label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> Ingresar Comentarios: </label>';
                            echo $form->field($model_feedback_entrada, "comentario", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'comentarios_feedback']);
                            echo '</div>';
                            echo '</div>';


                            Modal::end();
                            ActiveForm::end();


                        ?>
                        <!-- Modal Ingresar Comentarios Feedback -->
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>

    <div id="capaTres" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="row" id="div_competencias" style="display: none">
                    <div class="col-md-12">
                        <div class="card1 mb" style="width:100%">
                            <label style="font-size: 20px; margin-bottom:5px;"><em class="fa fa-user" style="font-size: 25px; color: #ffc034;"></em> <?= Yii::t('app', 'Reporte Evaluación por Competencias') ?> </label>
                            <label id="emptyMessage_table_competencias" style="font-size: 17px;"><em class="fas fa-info-circle" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'Sin datos para mostrar') ?></label>
                            <div class="table-responsive table-container" id="container_table_competencias">
                                <table id="table_competencias" class="table table-bordered table-hover center">

                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <br>
<?php
    }
?>

    <script>
        $(document).ready(function() {
            var data = <?php echo json_encode($data_calificacion_total); ?>;
            var varcapaP = document.getElementById("div_competencias");
            varcapaP.style.display = "none"; // Mostrar el div
            init_table_resultados(data);
        });        

        function init_table_resultados(data) {

            if(data.length > 0) {
                $( "#emptyMessageTableResultados" ).hide();
                var table_resultados = $('#table_resultados').DataTable({

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
                    paging: true,
                },                          
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 5,
                columnDefs: [
                    {
                        targets: 3, // Índice de la columna que quieres cambiar
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
                    {   title: "id",
                        data: 'id_user',
                        visible : false
                    },
                    {   title: "Nombre Completo",
                        data: 'nombre_completo'
                    },
                    {   title: "Documento",
                        data: 'identificacion'
                    },
                    {   title: "Calificación Cualitativa",
                        data: 'promedio_total_evalua'
                    },
                    {   title: "Promedio Total",
                        data: 'promedio_total_evalua',
                        render: function(data){ return parseFloat(data).toFixed(2); }
                    },
                    {   title: "Crear Feedback",
                        data: 'id_destinatario',
                        render: function(data, type, row) { 
                            if (data !== null) {
                                return "-----";  
                            } else {
                                return '<button id="crear_feedback_btn" class="btn btn-xs btn-danger" data-toggle="tooltip" data-container="body" data-trigger="hover" title="Crear Feedback"><span class="fa fa-envelope"></span></button>';
                            }       
                        },
                        width: '8%'

                    }
                ],
                rowCallback: function(row, data, index) {
                    var counter = index; // Calcula el contador único

                    // Concatena el contador único al ID del botón
                    var btnId = "crear_feedback_btn_" + counter;
                    
                    // Agrega el atributo 'id' al botón
                    $('button#crear_feedback_btn', row).attr('id', btnId);
                },
                drawCallback: function() {
                    var api = this.api();

                    api.rows().every(function() {
                        var rowData = this.data();
                        var promedioTotal = parseFloat(rowData.promedio_total_evalua);

                        $(this.node()).find('[id^="crear_feedback_btn_"]').each(function() {
                            var botonCrearFeedback = $(this);

                            if (promedioTotal > 2.9) {
                                botonCrearFeedback.prop('disabled', true);
                                botonCrearFeedback.hide();
                            } else {
                                botonCrearFeedback.prop('disabled', false);
                                botonCrearFeedback.show();
                            }
                        });
                    });
                },
                initComplete : function() {
                    // Click Boton Crear Feedback
                    $('#table_resultados tbody').on('click', '[id^="crear_feedback_btn_"]', function() {
                        event.preventDefault();
                        var fila = $(this).closest('tr');
                        var datos = table_resultados.row(fila).data();

                        // Obtener el índice de la fila seleccionada
                        var filaIndex = table_resultados.row(fila).index();

                        $('#modal_crear_feedback').data('id_colaborador', datos.id_user);
                        $('#modal_crear_feedback').data('fila', filaIndex); // Almacenar el índice de la fila

                        $('#modalCrearFeedback').modal('show');
                    });
                    // Click Boton Crear Feedback Fin
                }
                // INITCOMPLETE END
            });

            //Inicializar en la primer página del datatable
            $("#table_resultados").DataTable().page( 0 ).draw( false );

            } else {
                $( "#emptyMessageTableResultados" ).show();
            }
        }


        //REPORTE POR COMPETENCIAS
        function init_table_competencias(data) {

            if(data.length > 0) {

                // Obtener el div para mostrar u ocultar
            var varcapaP = document.getElementById("div_competencias");

                varcapaP.style.display = "inline"; // Mostrar el div

                var table_competencias = $('#table_competencias').DataTable({

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
                columns: [
                    {   title: "id",
                        data: 'id_evaluado',
                        visible : false
                    },
                    {   title: "Nombre Completo",
                        data: 'nombre_completo'
                    },
                    {   title: "Documento",
                        data: 'identificacion'
                    },
                    {   title: "Competencia",
                        data: 'competencia'
                    },
                    {   title: "Descriptivo de la competencia",
                        data: 'descripcion_competencia'
                    },
                    {   title: "Calificación cualitativa ",
                        data: 'descripcion_respuesta'
                    },
                    {   title: "Calificación",
                        data: 'calificacion_competencia'
                    }                    
                ],
                initComplete : function() {
                  
                }
                // INITCOMPLETE END
            });

            //Inicializar en la primer página del datatable
            $("#table_competencias").DataTable().page( 0 ).draw( false );

            } else {
                swal.fire("!!! Advertencia !!!","No existen datos para mostrar","warning");
                return;
            }
        }

        function crearFeedbackEvaluacion(){
            var id_jefe = '<?= $id_user; ?>'
            var filaIndex = $('#modal_crear_feedback').data('fila');            
            var btnId = 'crear_feedback_btn_' + filaIndex; // Construir el ID único del botón  
            var id_colaborador = $('#modal_crear_feedback').data("id_colaborador");
            var comentarios_feedback_selector = document.getElementById("comentarios_feedback");
            var comentarios_feedback_txt = comentarios_feedback_selector.value;

            //Validacion vacío
            if (comentarios_feedback_txt == "") {
                swal.fire("!!! Advertencia !!!","Campo Comentarios esta vacío","warning");
                return;
            }
            
            //ajax
            $.ajax({
                method: "post",
                url: "crearfeedback",
                data: {
                    id_jefe: id_jefe,
                    id_colaborador: id_colaborador,
                    comentarios: comentarios_feedback_txt,
                    _csrf:'<?=\Yii::$app->request->csrfToken?>'
                },
                success: function(response) {
                    if(response.status=="error"){
                        swal.fire("!!! Error !!!",response.message,"error");
                        return;
                    }

                    if(response.status=="success"){
                        var data = response.data;

                        comentarios_feedback_selector.value = '';
                        $('#modalCrearFeedback').modal('hide');

                        swal.fire("",response.message,"success");                        

                       // Limpiar datatable 
                       $( "#container_table" ).empty();
                        $( "#container_table" ).classList= "table-container";
                        var new_table = document.createElement("table");
                        new_table.setAttribute("id","table_resultados");                 
                        new_table.classList = "table table-hover table-striped table-bordered table-condensed dataTable no-footer";
                        document.getElementById("container_table").appendChild(new_table);
                        
                        init_table_resultados(data);
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Manejar el error
                    console.log("Error al cargar los datos ", errorThrown);
                }
            });
            //ajax fin
        }


        //Funcion para descargar archivo en excel
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
                document.getElementById("dlink").download = "Reporte General";
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
