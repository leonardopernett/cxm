

<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
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
                    padding: 20px;
                    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                    -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                    border-radius: 5px;    
                    font-family: "Nunito",sans-serif;
                    font-size: 150%;    
                    text-align: left;
            }

            .card3 {
                    height: auto;
                    width: auto;
                    margin-top: auto;
                    margin-bottom: auto;
                    background: #FFFFFF;
                    position: relative;
                    display: flex;
                    justify-content: center;
                    flex-direction: column;
                    padding: 20px;
                    box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                    -webkit-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                    -moz-box-shadow: 0 4px 8px 0 rgba(0, 0, 0, 0.2), 0 6px 20px 0 rgba(0, 0, 0, 0.19);
                    border-radius: 5px;    
                    font-family: "Nunito",sans-serif;
                    font-size: 150%;    
                    text-align: left;
            }


            .card2 {
                    height: auto;
                    width: auto;
                    margin-top: auto;
                    margin-bottom: auto;
                    background: #FFFFFF;
                    position: relative;
                    display: flex;
                    justify-content: center;
                    flex-direction: column;
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

            th, td {
                text-align: center;
            }

            .masthead {
                height: 25vh;
                min-height: 100px;
                background-image: url('<?php echo Yii::$app->request->baseUrl ?>/images/ADMINISTRADOR-GENERAL.png');
                background-size: cover;
                background-position: center;
                background-repeat: no-repeat;
                border-radius: 5px;
                box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
            }

            table.table tbody tr td,
            table.table tbody tr td a,
            table.table thead tr th a{    
                font-size: 12px !important ;
            }

            .fa-search, .fa-trash {
              font-size:35px !important;
              padding-right:10px;
              cursor:pointer;
              color:#fff;
              background:#4298b4;
              border-radius:50%;
              padding-left:10px;
            }

          
            .dataTables_wrapper .dataTables_paginate .paginate_button.current, 
            .dataTables_wrapper .dataTables_paginate .paginate_button.current{
              background:#4298b4 !important;
              color:#fff !important;
              border:none !important;
              border-radius:50%;
            }

          
        button.dt-button, div.dt-button, a.dt-button, input.dt-button{
              background:#4298b4 !important;
              color:#fff !important;
              border:none !important;
        }

        h3 {
          font-family: "Nunito";
          text-align:center;
          color:#002855
        }


        .icono {
          display:flex;
          flex-direction:row;
          align-items:center;
          background:#4298b4 !important;
        }

        .fa-plus, .fa-list, .fa-upload, .fa-calendar , .fa-info-circle{
          color:#fff;
          font-size:20px;

        }

        #toast-container > div {
            width: 400px !important;
            font-size:15px;
            opacity:1 !important;
          }

          .swal2-popup .swal2-styled.swal2-confirm:active{
            background:#4298b4 !important;
            border:#4298b4 !important;
            border:0 !important;
          }

          .swal2-popup .swal2-styled.swal2-confirm{
            background:#4298b4 !important;
            border:#4298b4 !important;
            border:0 !important;

          }
          .hide{
            display:none;
          }

          .input-area{
            width: 400px;
            height: 100px;
            border: 4px dotted #002855;
            margin: 0 auto;
            position:relative;
          }

          .input-text{
            
            display: flex;
            height: 100%;
            justify-content: center;
            align-items: center;
            font-size: 16px;
            color:#002855

          }

          .input-file{
            position: absolute;
            left:0;
            right:0;
            top:0;
            bottom:0;
            opacity:0 ;
            width: 100%;
            height:100%;
            cursor:pointer;
          }

          .button{
            text-align: center;
            margin-top: 15px;
            display: block;
           }
          .button .btn-success {
            padding: 5px 40px;
          }

          h4, h5{
              font-weight:bold;
              text-align:center;
          }

          h4{
              padding-left:10px;
              font-size:18px;
          }
          p{
              text-align:center;
              margin-left:10px;
          }

          .details{
              display:flex;
              align-items:center;
              justify-content:center;
              flex-wrap:wrap;
            
          }
          .fa-outdent {
              color:#ffc72c;
          }
          .fa-angle-double-right{
            color:#ffc72c;
            font-size:18px;
          }

          .myChart{
              width:50px;
              height:50px;
          }
  
</style>

<link rel="stylesheet" href="../../css/font-awesome/css/font-awesome.css"  >
<script src="https://ajax.googleapis.com/ajax/libs/jquery/2.1.1/jquery.min.js"></script>

<script src="https://code.highcharts.com/highcharts.js"></script>
<script src="https://code.highcharts.com/modules/exporting.js"></script>


<!-- datatable -->
<link rel="stylesheet" href="//cdn.datatables.net/1.10.25/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.7.1/css/buttons.dataTables.min.css">


<script src="https://cdn.datatables.net/1.10.25/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.7.1/js/buttons.print.min.js"></script>

<!-- sweet alert -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.all.min.js"></script>

<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
 
<script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.9.3/Chart.bundle.js" integrity="sha512-G8JE1Xbr0egZE5gNGyUm1fF764iHVfRXshIoUWCTPAbKkkItp/6qal5YAHXrxEu4HNfPTQs6HOu3D5vCGS1j3w==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>


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

<div class="container">
    <div class="row">
        <div class="col-md-12">
            
            <ul class="breadcrumb">

            <li class="">
            <a href="<?php echo Url::to(['/'])  ?>" > Inicio </a>
            </li>

            <li class="">
               <a href="<?php echo Url::to(['/hvinfopersonal/index'])  ?>">Hoja de vida</a>
            </li>

            <li class="active">
               Resumen
            </li>
            </ul>

            <?php if ($roles != 270 &&  $roles != 309  ): ?>                 
                    <div class="card1">
                    <div class="details">
                    <i class="fa fa-outdent" aria-hidden="true"></i>
                    <h4>Resumen general</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card2">
                            <div class="details">
                                    <em class="fa fa-angle-double-right"></em>
                                    <h5>Total Clientes</h5>
                                </div>
                                <p id="cliente">
                                    <?php 
                                        foreach ($clients as  $value) {
                                            echo array_sum($value) ;
                                        }                           
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card2">
                                <div class="details">
                                    <em class="fa fa-angle-double-right"></em>
                                    <h5>Total Decisores</h5>
                                </div>
                                <p  id="decisor">
                                    <?php 
                                        foreach ($decisor as  $value) {
                                            echo array_sum($value) ;
                                        }                           
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card2">
                            <div class="details">
                                    <em class="fa fa-angle-double-right"></em>
                                    <h5>Total Estrat&eacute;gicos</h5>
                                </div>
                                <p  id="estrategico">
                                    <?php 
                                        foreach ($estrategico as  $value) {
                                            echo array_sum($value) ;
                                        }                           
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card2">
                            <div class="details">
                                    <em class="fa fa-angle-double-right"></em>
                                    <h5>Total Operativos</h5>
                                </div>
                                <p id="operativo">
                                    <?php 
                                        foreach ($operativo as  $value) {
                                            echo array_sum($value) ;
                                        }                           
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>

            <?php if ($roles == 270  || $roles == 309  ): ?>                 
                    <div class="card1">
                    <div class="details">
                    <i class="fa fa-outdent" aria-hidden="true"></i>
                    <h4>Resumen general</h4>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="card2">
                            <div class="details">
                                    <em class="fa fa-angle-double-right"></em>
                                    <h5>Total Clientes</h5>
                                </div>
                                <p id="clienteAdmin">
                                    <?php 
                                        foreach ($clientsAdmin as  $value) {
                                            echo array_sum($value) ;
                                        }                           
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card2">
                                <div class="details">
                                    <em class="fa fa-angle-double-right"></em>
                                    <h5>Total Decisores</h5>
                                </div>
                                <p id="decisorAdmin">
                                    <?php 
                                        foreach ($decisorAdmin as  $value) {
                                            echo array_sum($value) ;
                                        }                           
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card2">
                            <div class="details">
                                    <em class="fa fa-angle-double-right"></em>
                                    <h5>Total Estrat&eacute;gicos</h5>
                                </div>
                                <p id="estrategicoAdmin">
                                    <?php 
                                        foreach ($estrategicoAdmin as  $value) {
                                            echo array_sum($value) ;
                                        }                           
                                    ?>
                                </p>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="card2">
                            <div class="details">
                                    <em class="fa fa-angle-double-right"></em>
                                    <h5>Total Operativos</h5>
                                </div>
                                <p id="operativoAdmin">
                                    <?php 
                                        foreach ($operativoAdmin as  $value) {
                                            echo array_sum($value) ;
                                        }                           
                                    ?>
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif ?>



        </div>
    </div>
</div>

<div class="container" style="margin-top:10px">
<div class="row">
  <div class="col-md-6">
    <?php if($roles == 270 || $roles == 309 ): ?>
            <div class="card3">
            <canvas id="myChartAdmin" class="myChart"></canvas>
            </div>
    <?php endif ?>

    <?php if($roles != 270 &&  $roles != 309  ): ?>
        <div class="card3">
        <canvas id="myChart" class="myChart"></canvas>
        </div> 
    <?php endif ?>  

  </div>

  <div class="col-md-6">
    <?php if($roles == 270 || $roles == 309 ): ?>
            <div class="card1">
               <table class="table table-bordered">
               <caption>...</caption>
                  <thead>
                      <tr>
                        <th scope="col">Tipo Cliente / Nivel Cliente</th>
                        <th scope="col">Estrat&eacute;gicos</th>
                        <th scope="col">Operativo</th>
                      </tr>
                      <tbody>
                            <tr>
                                <td>
                                    Decisor
                                    
                                </td>
                                <td>
                                  <?php foreach ($decisorEstrategico as  $value1) {
                                      echo $value1['total'];
                                  } ?>
                                </td>

                                <td>
                                    <?php foreach ($decisorOperativo as  $value2) {
                                        echo $value2['total'];
                                    } ?>
                                </td>
                                
                            </tr>

                            <tr>
                                  <td>
                                      No Decisor
                                  </td>
                                  <td>
                                  <?php foreach ($nodecisorEstrategico as  $value3) {
                                      echo $value3['total'];
                                  } ?>
                                </td>

                                <td>
                                    <?php foreach ($nodecisorOperativo as  $value4) {
                                        echo $value4['total'];
                                    } ?>
                                </td>

                            </tr>
                                   
                      </tbody>
                  </thead>
              </table> 
            </div>

            <div class="card1" style="margin-top:5px">
                <h4 style="color:#000 !important; font-weight:bold">Total clientes de inter&eacute;s</h4>
                <p style="font-size:25px; color:#000 !important">
                    <?php  

                      foreach ($clienteInteresAdmin as $value) {
                          echo $value['total'];
                      }

                    ?>
                </p>
            </div>
    <?php endif ?>

    <?php if($roles != 270 && $roles != 309  ): ?>
        <div class="card1">
               <table class="table table-bordered">
               <caption>...</caption>
                  <thead>
                      <tr>
                        <th scope="col">Tipo Cliente / Nivel Cliente</th>
                        <th scope="col">Estrat&eacute;gicos</th>
                        <th scope="col">Operativo</th>
                      </tr>
                      <tbody>
                            <tr>
                                <td>
                                    Decisor
                                    
                                </td>
                                <td>
                                  <?php foreach ($decisorEstrategicoU as  $value1) {
                                      echo $value1['total'];
                                  } ?>
                                </td>

                                <td>
                                    <?php foreach ($decisorOperativoU as  $value2) {
                                        echo $value2['total'];
                                    } ?>
                                </td>
                                
                            </tr>

                            <tr>
                                  <td>
                                      No Decisor
                                  </td>
                                  <td>
                                  <?php foreach ($nodecisorEstrategicoU as  $value3) {
                                      echo $value3['total'];
                                  } ?>
                                </td>

                                <td>
                                    <?php foreach ($nodecisorOperativoU as  $value4) {
                                        echo $value4['total'];
                                    } ?>
                                </td>

                            </tr>
                                   
                      </tbody>
                  </thead>
              </table> 
            </div>

            <div class="card1" style="margin-top:5px">
                <h4 style="color:#000 !important; font-weight:bold">Total clientes de inter&eacute;s</h4>
                <p style="font-size:25px; color:#000 !important">
                    <?php  

                      foreach ($clienteInteresAdmin as $value) {
                          echo $value['total'];
                      }

                    ?>
                </p>
            </div>
    <?php endif ?> 
  </div>

  
</div>
</div>

<script>

  var options ={
    scales: {
        xAxes: [{
            gridLines: {
                drawOnChartArea: false
            }
        }],
        yAxes: [{
            gridLines: {
                drawOnChartArea: false
            },
            display: true,
                ticks: {
                    beginAtZero: true
                }
        }]
    }
    
}

  const ctx = document.getElementById('myChartAdmin').getContext('2d')

  const clienteAdmin = document.getElementById('clienteAdmin').innerHTML
  const decisorAdmin = document.getElementById('decisorAdmin').innerHTML
  const estrategicoAdmin = document.getElementById('estrategicoAdmin').innerHTML
  const operativoAdmin = document.getElementById('operativoAdmin').innerHTML

  const chart = new Chart(ctx,{
    type: 'bar',
    data: {
        labels: ['Clientes', 'Decisor', 'Estrategicos', 'Operativo'],
        datasets: [{
            label: 'Resumen General',
            data: [clienteAdmin, decisorAdmin, estrategicoAdmin, operativoAdmin],
            backgroundColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)', 
                
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)', 
            ],
            borderWidth: 1
        }]
    },
    options: options
  })

  const ctx3 = document.getElementById('myChartDiscAdmin').getContext('2d')
  const chart3 = new Chart(ctx3,{
    type: 'pie',
    data: {
        labels: ['Clientes', 'Decisor', 'Estrategicos', 'Operativo'],
        datasets: [{
            label: 'Resumen General',
            data: [clienteAdmin, decisorAdmin, estrategicoAdmin, operativoAdmin],
            backgroundColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)', 
                
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)', 
            ],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
        datalabels: {
        display: false
        }
    },
    }
  })


  const ctx2 = document.getElementById('myChart').getContext('2d')
  const cliente = document.getElementById('cliente').innerHTML
  const decisor = document.getElementById('decisor').innerHTML
  const estrategico = document.getElementById('estrategico').innerHTML
  const operativo = document.getElementById('operativo').innerHTML

  const chart2 = new Chart(ctx2,{
    type: 'bar',
    data: {
        labels: ['Clientes', 'Decisor', 'Estrategicos', 'Operativo'],
        datasets: [{
            label: 'Resumen General',
            data: [cliente, decisor, estrategico, operativo],
            backgroundColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)', 
                
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)', 
            ],
            borderWidth: 1
        }]
    },
    options: options
  })


  const ctx4 = document.getElementById('myChartDisc').getContext('2d')

  const chart4 = new Chart(ctx4,{
    type: 'doughnut',
    data: {
        labels: ['Clientes', 'Decisor', 'Estrategicos', 'Operativo'],
        datasets: [{
            label: 'Resumen General',
            data: [cliente, decisor, estrategico, operativo],
            backgroundColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)', 
                
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(255, 99, 132, 1)', 
            ],
            borderWidth: 1
        }]
    },
    options: {
        plugins: {
            datalabels: {
            display: false
            }
        },
    }
  })


</script>