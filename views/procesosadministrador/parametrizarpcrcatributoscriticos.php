<?php

    use yii\helpers\Html;
    use yii\grid\GridView;
    use yii\helpers\Url;
    use yii\bootstrap\ActiveForm;
    use kartik\select2\Select2;
    use yii\web\JsExpression;
    use kartik\daterange\DateRangePicker;
    use yii\helpers\ArrayHelper;
    use yii\bootstrap\Modal;
    use yii\db\Query;

    $this->title = 'Procesos Administrador - Parametrizar PCRC para atributos críticos';
    $this->params['breadcrumbs'][] = $this->title;

    $sesiones =Yii::$app->user->identity->id;   

    $template = '<div class="col-md-12">'
        . ' {input}{error}{hint}</div>';


    $rol =  new Query;
    $rol    ->select(['tbl_roles.role_id'])
            ->from('tbl_roles')
            ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                    'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
            ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                    'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
            ->where('tbl_usuarios.usua_id = '.$sesiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

    ?>
    <style>
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

        th {
            text-align: left;
            font-size: smaller;
        }

        .masthead {
            height: 25vh;
            min-height: 100px;
            background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
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

        body #table_pcrc tbody tr td,
        body #table_pcrc tbody tr td a,
        body #table_pcrc thead tr th a {
            font-size: 13px !important;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button {
            font-size: 14px;
            padding: 5px 10px !important;        
        }
        
        .dataTables_wrapper .dataTables_info {
            padding-top: 0em  !important;        
        }

        .dataTables_wrapper .dataTables_paginate {
            padding-top: 0em  !important; 
        }

        table.table tbody tr td, table.table tbody tr td a, table.table thead tr th a {
            font-size: 12px !important;
        }

    </style>

    <!-- datatable -->
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
    <header class="masthead">    
        <div class="container h-100">
            <div class="row h-100 align-items-center">
            <div class="col-12 text-center">
            </div>
            </div>
        </div>
    </header>
    <br><br>

    <div class="capaUno" id="idCapaUno" style="display: inline;">
        <div class="row">
            <div class="col-md-12">
                <div class="card1 mb" style="background: #6b97b1; ">
                <label style="font-size: 20px; color: #FFFFFF;"><?php echo "Agregar PCRC para ajustar el score según atributos con métrica PEC"; ?> </label>
                </div>
            </div>
        </div>
        <br>

        <?php $form = ActiveForm::begin(['layout' => 'horizontal']); ?>
        <div class="row">
            <div class="col-md-4">
                <div class="card1 mb">
                    <label><em class="fas fa-search" style="font-size: 20px; color: #b52aef;"></em> <?= Yii::t('app', 'Buscar Programa/Pcrc') ?></label>
                    
                    <?=
                            $form->field($model, 'arbol_id')
                                ->widget(Select2::classname(), [
                                    //'data' => array_merge(["" => ""], $data),
                                    'language' => 'es',
                                    'options' => ['id'=>'idvarArbol','placeholder' => Yii::t('app', 'Select ...')],
                                    'pluginOptions' => [
                                        'allowClear' => false,
                                        'minimumInputLength' => 3,
                                        'ajax' => [
                                            'url' => \yii\helpers\Url::to(['formularios/getarbolesbyroles']),
                                            'dataType' => 'json',
                                            'data' => new JsExpression('function(term,page) { return {search:term}; }'),
                                            'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
                                        ],
                                    //'initSelection' => new JsExpression($initScript)
                                    ]
                                        ]
                            )->label('');
                        ?>
                    
                        <br>
                        <!-- Boton Guardar PCRC-->
                        <?= Html::submitButton(Yii::t('app', 'Guardar Pcrc'),
                                            ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                                'data-toggle' => 'tooltip',
                                                'title' => 'Guardar Pcrc']) 
                    ?>
                </div>

                <br>

                <!-- Boton Cancelar-->                
                <div class="card1 mb">
                    <label style="font-size: 17px;"><em class="fas fa-minus-circle" style="font-size: 15px; color: #b52aef;"></em> Cancelar y Regresar</label>
                    <?= Html::a('Regresar',  ['index'], ['class' => 'btn btn-success',
                                                    'style' => 'background-color: #707372',
                                                    'data-toggle' => 'tooltip',
                                                    'title' => 'Regresar']) 
                    ?>
                </div>
            </div>

            <!-- Tabla donde están los PCRC que deben tener el ajuste-->
            <div class="col-md-8">

                <div class="card1 mb" style="width:100%">
                    <label ><em class="fas fa-list" style="font-size: 20px; color: #b52aef; "></em> <?= Yii::t('app', 'Histórico Programa/Pcrc ') ?></label>
                    <label id="emptyMessage_table_pcrc" style="font-size: 16px;"><em class="fas fa-info-circle" style="font-size: 18px; color: #827DF9; margin-top:1.5%;"></em> <?= Yii::t('app', 'No hay datos para mostrar') ?></label>
                    <div class="table-responsive table-container" id="container_table"> 
                        <table id="table_pcrc" class="table table-hover table-bordered" style="margin-top:20px" >
                            
                        </table>
                    </div>
                </div>
                
            </div>
        </div>

        <?php ActiveForm::end(); ?>  
    </div>
    <hr>
    <!-- Mostrar alertas Flash-->
    <?php if (Yii::$app->session->hasFlash('success_creacion')): ?>
        <script>
            swal.fire("", '<?= Yii::$app->session->getFlash('success_creacion') ?>', "success");
        </script>
    <?php elseif (Yii::$app->session->hasFlash('error_creacion')): ?>
        <script>
            swal.fire("", '<?= Yii::$app->session->getFlash('error_creacion') ?>', "error");
        </script>
    <?php endif; ?>
    <!-- Mostrar alertas Flash Fin-->

    <script type="text/javascript">

        $(document).ready( function () {

            cargar_lista_pcrc();

        });    
          

        function init_table_lista_pcrc(data) {

            if (data.length > 0) {            
                var table_pcrc = $('#table_pcrc').DataTable({
                responsive: true,
                select: true,
                fixedColumns: true,
                "autoWidth": true,
                data:data,
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
                    "order": [[ 0, "asc" ]],
                    autoWidth : false,
                    "table-layout": "fixed",
                    paging: true,      
                },
                columns: [
                    {   title: "ID Programa/Pcrc",
                        data: 'id_pcrc',
                        width: '20%' 
                    },
                    {   title: "Programa/Pcrc",
                        data: 'nom_pcrc',
                        width: '60%'

                    },
                    {   title: "Acción",
                        defaultContent : "<button class='btn btn-xs btn-danger btn_delete' style='background-color: #337ab700;' data-toggle='tooltip' data-container='body' data-trigger='hover' title='Eliminar' > <span class='fas fa-times' style='font-size: 18px; color: #FC4343;' </span> </button>",
                        width: '20%'
                                
                    }
                ],
                initComplete : function(){

                    // Click Boton Eliminar
                    $('#table_pcrc tbody').on( 'click', '.btn_delete', function () {
                        event.preventDefault();
                        var datos = table_pcrc.row($(this).closest('tr')).data(); // Obtener los datos de la fila                        
                        const id_pcrc = datos['id_pcrc'];                        
                        eliminar_pcrc(id_pcrc);
                    });
                    // Click Boton Eliminar Fin

                }
                // INITCOMPLETE END
                });        

                //Inicializar en la primer página del datatable
                $("#table_pcrc").DataTable().page( 0 ).draw( false );


            }

        }

        function eliminar_pcrc(id_pcrc){

            if (id_pcrc == "") {
                swal.fire("!!! Advertencia !!!","Problemas con obtener el programa/PCRC, por favor inténtalo de nuevo","error");
                return;
            }

            // Enviar los datos a través de AJAX
            $.ajax({
                method: "GET",
                url: "deletepcrcatributoscriticos",
                data: {
                    id_pcrc:id_pcrc                    
                },
                success: function(response) {

                    if ( response.status === 'error') {
                        swal.fire("!!! Error !!!", response.data ,"error");
                        return;
                    }
                   
                    if ( response.status === 'success') {
                        
                        //cargar de nuevo la lista PCRC con el dato nuevo
                        cargar_lista_pcrc();
                        swal.fire("", response.data ,"success");                        
                        return;

                    }                  
                },
                error: function() {
                    swal.fire("!!! Error !!!", 'Hubo un error en la petición al eliminar los datos' ,"error");
                }
            });
            
        }

        function cargar_lista_pcrc()  {

            // Enviar los datos a través de AJAX
            $.ajax({
                method: "GET",
                url: "cargarlistapcrc",
                success: function(response) {

                    if ( response.status === 'error') {
                        swal.fire("!!! Error !!!", response.data ,"error");
                        return;
                    }
                   
                    if ( response.status === 'success') {

                        console.log("size daata: ", response.data.length);
                        size_data = response.data.length;
                        
                        if(size_data == 0){
                            $( "#container_table" ).hide();
                            $( "#emptyMessage_table_pcrc" ).show();
                            return;
                        }

                        if(size_data > 0) {
                            $( "#emptyMessage_table_pcrc" ).hide();

                            // CLEAR DATATABLE
                            $( "#container_table" ).empty();
                            var new_table = document.createElement("table");
                            new_table.setAttribute("id","table_pcrc");                  
                            new_table.classList = "table table-hover table-bordered dataTable no-footer";
                            document.getElementById("container_table").appendChild(new_table);  
                            // CLEAR DATATABLE END

                            init_table_lista_pcrc(response.data);
                            
                            return;
                        }
                        
                    }             
                },
                error: function() {
                    swal.fire("!!! Error !!!", 'Hubo un error en petición para mostrar histórico Programa/PCRC' ,"error");
                }
            });

        }
        
        


    </script>