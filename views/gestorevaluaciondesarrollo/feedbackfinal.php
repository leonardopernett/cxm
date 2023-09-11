<?php

    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\helpers\Url;
    use yii\bootstrap\ActiveForm;
    use kartik\select2\Select2;
    use yii\web\JsExpression;
    use kartik\daterange\DateRangePicker;
    use yii\bootstrap\Modal;

    $this->title = 'Feedbacks Equipo - Evaluacion de Desarrollo';
    $this->params['breadcrumbs'][] = $this->title;

        $template = '<div class="col-md-12">'
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

        body #table_feedback tbody tr td,
        body #table_feedback tbody tr td a,
        body #table_feedback thead tr th a {
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

    <!-- TABLA FEEDBACK-->
    <div class="CapaUno" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb" style="width:100%"> 
                    <label style="font-size: 20px; margin-bottom:10px;"><em class="fa fa-list-alt" style="font-size: 25px; color: #ffc034;"></em> <?= Yii::t('app', 'Feedback Equipo') ?> </label>                      

                    <label id="emptyMessage" style="font-size: 17px;"><em class="fas fa-info-circle" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'No se ha completado el proceso de evaluación de tu equipo para realizar feedbacks.') ?></label>
                    
                    <div class="table-responsive table-container" id="container_table">                                
                        <table id="table_feedback" class="table table-hover table-striped table-bordered table-condensed dataTable no-footer">
                            
                        </table>    
                    </div>           
                </div>
                <!-- Modal Ingresar Comentarios Feedback -->
                <?php
                $form = ActiveForm::begin([
                    'id' => 'form_crear_feedback_final',
                ]);

                Modal::begin([
                    'id' => 'modalCrearFeedbackFinal',
                    'header' => '<h4>Crear Feedback</h4>',
                    'footer' => Html::button('Enviar feedback', ['class' => 'btn btn-success btn-block', 'style'=>'margin-top: 1.5%; padding:0.5%', 'onClick' => 'crearAcuerdoFinal();']),
                ]);


                echo '<div class="row" id="modal_crear_feedback_final">';
                echo '<div class="col-md-12" style="margin-top: 20px">';
                echo '<label style="font-size: 15px;"><em class="fas fa-list-alt" style="font-size: 18px; color: #827DF9;"></em> Ingresar Comentarios: </label>';
                echo $form->field($model, "comentario", ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textArea(['id'=>'comentarios_feedback']);
                echo '</div>';
                echo '</div>';


                Modal::end();
                ActiveForm::end();


                ?>
                <!-- Modal Ingresar Comentarios Feedback -->

            </div>
        </div>
    </div>
    <hr>
    <!-- TABLA FEEDBACK FIN-->
    <!-- SECCION ACCIONES-->
    <div id="capaDos" style="display: inline">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-cogs" style="font-size: 20px; color: #FFC72C;"></em> Acciones: </label>
                        <div class="col-md-4">
                            <div class="card1 mb">
                                <label style="font-size: 16px;"><em class="fas fa-minus-circle" style="font-size: 17px; color: #FFC72C;"></em> Cancelar y regresar: </label> 
                                <?= Html::a('Regresar',  ['gestorevaluaciondesarrollo/resultados'], ['class' => 'btn btn-success',
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

            var data = <?php echo json_encode($data_feedbacks); ?>;
            console.log("data", data);
            console.log("tipo", typeof(data) );

            init_table_feedback(data);

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
        function init_table_feedback(data) {

            if(data.length > 0) {
                var table_feedback = $('#table_feedback').DataTable({
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
                "lengthMenu": [5, 10, 25, 50],
                "pageLength": 5,
                columnDefs: [
                    { targets: '_all', className: 'column-font-size' }
                ],
                columns: [
                    {   title: "Id",
                        data: 'id_acuerdo',
                        visible: false
                    },
                    {   title: "Nombre Completo",
                        data: 'nombre_completo'
                    },
                    {   title: "Identificación",
                        data: 'identificacion'
                    },
                    {   title: "Promedio Total",
                        data: 'nota_final',
                        render: function(data){ return parseFloat(data).toFixed(2); }
                    },
                    {   title: "Feedback Colaborador",
                        data: 'feedback_colaborador'
                    },
                    {   title: "Feedback Jefe",
                        data: 'feedback_jefe'
                    },
                    {   title: "Acuerdo Final Desarrollo",
                        data: 'acuerdo_final'
                    },
                    {   title: "Acción",
                        data: 'acuerdo_final',
                        render: function(data, type, row) { 
                            if (data !== null) {
                                return "-----"; 
                            } else {
                            return '<button class="btn btn-xs btn-info acuerdo_final_btn" data-toggle="tooltip" data-container="body" data-trigger="hover" title="Editar">Crear</button>';     
                            }
                        },  
                    }                   
                ],
                initComplete : function() {

                    // Capturar el evento click en los botones "Aprobar" y "No aprobar"
                    $('#table_feedback tbody').on('click', '.acuerdo_final_btn', function() {
                        event.preventDefault();  
                        var idAcuerdo = $(this).data('id');             
                        var fila = $(this).closest('tr'); // Obtener la fila correspondiente al click                
                        var datos = table_feedback.row(fila).data(); // Obtener los datos de la fila 
                      
                        $('#modal_crear_feedback_final').data('idAcuerdo', datos.id_acuerdo); 

                        $('#modalCrearFeedbackFinal').modal('show');
                    });
                }
                    
                });

                //Inicializar en la primer página del datatable
                $("#table_feedback").DataTable().page( 0 ).draw( false );

            }
        }

        function crearAcuerdoFinal(){
            var id_jefe = '<?= $id_jefe; ?>';
            var idAcuerdo = $('#modal_crear_feedback_final').data('idAcuerdo'); 
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
                url: "crearfeedbackfinal",
                data: {
                    id_jefe: id_jefe,
                    id_acuerdo: idAcuerdo,
                    comentarios: comentarios_feedback_txt,
                    _csrf:'<?=\Yii::$app->request->csrfToken?>'
                },
                success: function(response) {

                    console.log("response a js : ", response);

                    if(response.status=="error"){
                        swal.fire("!!! Error !!!",response.message,"error");
                        return;
                    }

                    if(response.status=="success"){
                        
                        var data = response.data;
                        comentarios_feedback_selector.value = '';
                        $('#modalCrearFeedbackFinal').modal('hide');
                        swal.fire("",response.message,"success"); 

                        // Limpiar datatable 
                        $( "#container_table" ).empty();
                        $( "#container_table" ).classList= "table-container";
                        var new_table = document.createElement("table");
                        new_table.setAttribute("id","table_feedback");                 
                        new_table.classList = "table table-hover table-striped table-bordered table-condensed dataTable no-footer";
                        document.getElementById("container_table").appendChild(new_table);
                        
                        //mostrar data actualizada
                        init_table_feedback(data);
                        return;
                       
                    }

                },
                error: function(jqXHR, textStatus, errorThrown) {
                    // Manejar el error
                    console.log("Error en petición", errorThrown);
                }
            });
            //ajax fin
        }

    </script>




