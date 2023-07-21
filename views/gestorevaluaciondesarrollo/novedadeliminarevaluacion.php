<?php

    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\helpers\Url;
    use yii\bootstrap\ActiveForm;
    use kartik\select2\Select2;
    use yii\web\JsExpression;
    use kartik\daterange\DateRangePicker;
    use yii\bootstrap\Modal;

    $this->title = 'Gestion de novedades - Eliminar evaluación de desarrollo';
    $this->params['breadcrumbs'][] = $this->title;

        $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';

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

        .card2 {
                height: 170px;
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

        body #table_eliminar_evaluacion tbody tr td,
        body #table_eliminar_evaluacion tbody tr td a,
        body #table_eliminar_evaluacion thead tr th a {
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
            font-size: 15px;        
        }        

    </style>

    <!-- JQuery -->
    <script src="../../js_extensions/jquery-2.1.3.min.js"></script>
    <!-- Highcharts -->
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

    <!-- TABLA NOVEDADES ELIMINAR EVALUACION-->
    <div class="CapaUno" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb" style="width:100%"> 
                    <label style="font-size: 20px; margin-bottom:10px;"><em class="fa fa-list-alt" style="font-size: 25px; color: #ffc034;"></em> <?= Yii::t('app', 'Novedades Eliminar evaluaciones') ?> </label>                      

                    <label id="emptyMessage" style="font-size: 15px;"><em class="fas fa-info-circle" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'Sin novedades a gestionar') ?></label>
                    
                    <div class="table-responsive table-container" id="container_table">                                
                        <table id="table_eliminar_evaluacion" class="table table-hover table-striped table-bordered table-condensed dataTable no-footer">
                            
                        </table>    
                    </div>           
                </div>
            </div>
        </div>
    </div>
    <hr>
    <!-- TABLA NOVEDADES ELIMINAR EVALUACION FIN-->

    <!-- SECCION ACCIONES-->
    <div id="capaDos" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                                <?= Html::a('Regresar',  ['gestorevaluaciondesarrollo/novedades'], ['class' => 'btn btn-success',
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
    <hr>
    <!-- SECCION ACCIONES FIN-->

    <script>
        $(document).ready(function() {

            var data = <?php echo json_encode($data_datatable); ?>;
            init_table_eliminar_evaluacion(data);
            if(data.length==0){
                    $( "#container_table").hide();
                    $( "#emptyMessage" ).show();
            }
            else{
                $( "#container_table").show();
                $( "#emptyMessage" ).hide();
            }
        });


        //Novedades por jefe incorrecto
        function init_table_eliminar_evaluacion(data) {
            console.log("data datable:", data );

            if(data.length > 0) {
                var table_eliminar_evaluacion = $('#table_eliminar_evaluacion').DataTable({
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
                    autoWidth : true,
                    "table-layout": "fixed",
                    paging: true
                },
                "lengthMenu": [10, 20, 40, 50],
                "order": [[1, "desc"]],
                columns: [
                    {   title: "Id",
                        data: 'id_novedad',
                        width: '2%'
                    },
                    {   title: "Fecha Solicitud",
                        data: 'fechacreacion'
                    },
                    {   title: "Motivo",
                        defaultContent: 'Eliminar evaluación'
                    },
                    {   title: "Nombre Evaluación",
                        data: 'nombre_evaluacion',
                        visible: false
                    },
                    {   title: "Solicitante",
                        data: 'solicitante'
                    },
                    {   title: "Documento Solicitante",
                        data: 'cc_solicitante'
                    },
                    {   title: "Tipo de Evaluación",
                        data: 'tipoevaluacion'
                    },
                    {   title: "Documento Evaluado",
                        data: 'cc_evaluado'
                    },
                    {   title: "Comentarios Solicitud",
                        data: 'comentarios_solicitud'
                    },
                    {   title: "Estado",
                        data: 'estado'
                    },
                    {   title: "Aprobar",
                        data: 'aprobado',
                        render: function(data, type, row) { 
                            if (data == 1 || data == 0 ) {
                                return "-----"; 
                            } else {
                                return '<button id="aprobado_btn" class="btn btn-primary" onclick="actualizarEstado(' + row.id_novedad + ',1)">Si</button>'; // Mostrar el botón si el valor es 1
                            }       
                        },
                    },
                    {   title: "No aprobar",
                        data: 'aprobado',
                        render: function(data, type, row) { 
                            if (data == 1 || data == 0) {
                                return "-----";  
                            } else {
                                return '<button class="btn btn-danger" onclick="actualizarEstado(' + row.id_novedad + ',0)">No</button>'; // Mostrar el botón si el valor es 1
                            }       
                        },
                    }                    
                ],
                initComplete : function() {
                     // Capturar el evento click en los botones "Aprobar" y "No aprobar"
                    $('#table_eliminar_evaluacion').on('click', 'button[data-id]', function() {
                        var idNovedad = $(this).data('id');
                        var estado = $(this).data('estado');

                        actualizarEstado(idNovedad, estado);
                    });
                }
                // INITCOMPLETE END
            });

            //Inicializar en la primer página del datatable
            $("#table_eliminar_evaluacion").DataTable().page( 0 ).draw( false );

            }
        }

        // Función para actualizar el estado
        function actualizarEstado(idNovedad, estadoAprobacion) {
          
            $.ajax({
                url: "eliminarevaluacionusuario",
                method: "POST",
                data: {
                    id_novedad: idNovedad,
                    estado_aprobacion: estadoAprobacion,
                    _csrf:'<?=\Yii::$app->request->csrfToken?>'
                },
                success: function(response) {

                    if (response.status === 'error') {
                    //estado de la solicitud debe quedar en error

                        swal.fire("",response.message,"error");
                        return;
                    } 

                    if (response.status === 'success') {

                        var data = response.data;

                        swal.fire("",response.message,"success");                   

                        // Limpiar datatable 
                        $( "#container_table" ).empty();
                        $( "#container_table" ).classList= "table-container";
                        var new_table = document.createElement("table");
                        new_table.setAttribute("id","table_eliminar_evaluacion");                 
                        new_table.classList = "table table-hover table-striped table-bordered table-condensed dataTable no-footer";
                        document.getElementById("container_table").appendChild(new_table);
                        
                        init_table_eliminar_evaluacion(data);  
                        
                        return;

                    } 
                },
                error: function(xhr, status, error) {
                console.error(xhr.responseText);
                }
            });
        }
        

    </script>




