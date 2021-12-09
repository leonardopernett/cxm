<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\ActiveForm;

$this->title = 'Hoja de Vida - Resumen General';
$this->params['breadcrumbs'][] = $this->title;

$varCiudad = "Ciudad";
$varTotal = "Total";
$varBogota = "Bogotá";
$varMedellin = "Medellín";

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
                font-size: 15px !important ;
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
          font-family: "Nunito", sans-serif;
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

        p{
            text-align:left !important;
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
<script src="../../js_extensions/jquery-2.1.1.min.js"></script>

<script src="../../js_extensions/highcharts/highcharts.js"></script>
<script src="../../js_extensions/highcharts/exporting.js"></script>


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

<!-- sweet alert -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.1.2/dist/sweetalert2.min.css">
<script src="../../js_extensions/sweetalert2/sweetalert2.all.min.js"></script>

<!-- toastr -->
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
<script src="../../js_extensions/cloudflare/toastr.min.js"></script>
 
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


<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            
            <ul class="breadcrumb">

            <li class="">
               <a href="<?php echo Url::to(['/hojavida/index'])  ?>" > Inicio </a>
            </li>

            <li class="active">
               Resumen
            </li>
            </ul>


        </div>
    </div>
</div>

<div class="container-fluid" style="margin-top:10px">
  <div class="row">

    <div class="col-md-3">
        <div class="card1">
            <div class="details">
               <em class="fa fa-angle-double-right"></em>
               <h4>Total clientes </h4>
            </div>
            <div class="content">
                <table id="tblDataclientes" class="table table-striped table-bordered tblResDetFreed">
                <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $varCiudad) ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $varTotal) ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>                            
                            <td style="text-align:center"><?php echo  $varBogota; ?></td>
                            <td>
                                <?php foreach($clientsTotalBogota as $bogota): ?> 
                                    <span id="bogota"> <?php echo $bogota['total']   ?></span>                               
                                <?php endforeach ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:center"><?php echo  $varMedellin; ?></td>
                            <td>
                                <?php foreach($clientsTotalMedellin as $medellin): ?> 
                                    <span id="medellin"> <?php echo $medellin['total']   ?></span>                                 
                                <?php endforeach ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:center"><?php echo  $varTotal; ?></td>
                            <td>
                                <?php foreach($clientsTotalAdmin as $client): ?> 
                                    <span id="clienteAdmin"><?php echo $client['total']   ?></span>
                                <?php endforeach ?> 
                            </td>
                        </tr>  
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card1">
            <div class="details">
               <em class="fa fa-angle-double-right"></em>
               <h4>Total decisores </h4>
            </div>
            <div class="content">
                <table id="tblDatadecisores" class="table table-striped table-bordered tblResDetFreed">
                <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $varCiudad) ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $varTotal) ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align:center"><?php echo  $varBogota; ?></td>
                            <td>
                                <?php foreach($clientDecisorBogota as $bogota): ?> 
                                    <?php echo $bogota['total']   ?>
                                <?php endforeach ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:center"><?php echo  $varMedellin; ?></td>
                            <td>
                                <?php foreach($clientDecisorMedellin as $medellin): ?> 
                                    <?php echo $medellin['total']   ?>
                                <?php endforeach ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:center"><?php echo  $varTotal; ?></td>
                            <td>
                                <?php foreach($clientDecisor as $decisor): ?> 
                                    <span id="decisorAdmin">
                                        <?php echo $decisor['total']   ?>
                                    </span>                               
                                <?php endforeach ?> 
                            </td>
                        </tr>  
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card1">
            <div class="details">
               <em class="fa fa-angle-double-right"></em>
               <h4>Total clientes estratégicos  </h4>
            </div>
            <div class="content">
                <table id="tblDataestrategicos" class="table table-striped table-bordered tblResDetFreed">
                <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $varCiudad) ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $varTotal) ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align:center"><?php echo  $varBogota; ?></td>
                            <td>
                                <?php foreach($clientEstrategicoBogota as $bogotaEs): ?> 
                                    <?php echo $bogotaEs['total']   ?>
                                <?php endforeach ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:center"><?php echo  $varMedellin; ?></td>
                            <td>
                                <?php foreach($clientEstrategicoMedellin as $medellinEs): ?> 
                                    <?php echo $medellinEs['total']   ?>
                                <?php endforeach ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:center"><?php echo  $varTotal; ?></td>
                            <td>
                                <?php foreach($clientEstrategico as $decisorEs): ?> 
                                    <span id="estrategicoAdmin">
                                         <?php echo $decisorEs['total']   ?>
                                    </span>                             
                                <?php endforeach ?> 
                            </td>
                        </tr>  
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="card1">
            <div class="details">
               <em class="fa fa-angle-double-right"></em>
               <h4>Total clientes operativos </h4>
            </div>
            <div class="content">
                <table id="tblDataoperativos" class="table table-striped table-bordered tblResDetFreed">
                <caption>Resultados</caption>
                    <thead>
                        <tr>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $varCiudad) ?></label></th>
                            <th scope="col" style="background-color: #C6C6C6;"><label style="font-size: 13px;"><?= Yii::t('app', $varTotal) ?></label></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td style="text-align:center"><?php echo  $varBogota; ?></td>
                            <td>
                                <?php foreach($clientOperativoBogota as $bogotaOp): ?> 
                                    <?php echo $bogotaOp['total']   ?>
                                <?php endforeach ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:center"><?php echo  $varMedellin; ?></td>
                            <td>
                                <?php foreach($clientOperativoMedellin as $medellinOp): ?> 
                                    <?php echo $medellinOp['total']   ?>
                                <?php endforeach ?> 
                            </td>
                        </tr>

                        <tr>
                            <td style="text-align:center"><?php echo  $varTotal; ?></td>
                            <td>
                                <?php foreach($clienOperativo as $decisorOpera): ?> 
                                    <span id="operativoAdmin">
                                        <?php echo $decisorOpera['total']   ?>  
                                    </span>                               
                                <?php endforeach ?> 
                            </td>
                        </tr>  
                        
                    </tbody>
                </table>
            </div>
        </div>
    </div>

  </div>
</div>

<div class="container" style="margin-top:20px">
      
        <div class="row">
            <div class="col-md-6 ">
                <div class="card1">
                    <canvas id="myChartDirector"></canvas>
                </div>
            </div>

            <div class="col-md-6 ">
                <div class="card1">
                    <canvas id="myChartCliente"></canvas>
                </div>
            </div>
        </div>


    <div class="row" style="margin-top:20px">
       <div class="col-md-6">
           <div class="card1">
              <canvas id="myChartAdmin"></canvas>
           </div>
       </div>
       
       <div class="col-md-6">
           <div class="card1">
              <canvas id="myChartAdmin2"></canvas>
           </div>
       </div>

      <!--  <div class="col-md-5 col-md-offset-1" >
           <div class="card1" style="margin:20px;">
            <table class="table table-bordered" style="text-align-center">
                <thead>
                    <tr>
                        <td></td>
                        <th>Estratégico</th>
                        <th>Operativo</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td>Decisor</td>
                        <td>
                            <?php foreach($totalDecisorEstrategico as $total1): ?>
                                 <?php echo $total1['total'] ?>
                            <?php endforeach ?>
                        </td>
                        <td>
                            <?php foreach($totalDecisorOperativo as $total2): ?>
                                 <?php echo $total2['total'] ?>
                             <?php endforeach ?>
                          </td>
                    </tr>

                    <tr>
                        <td>No decisor</td>
                        <td>
                            <?php foreach($totalNoDecisorEstrategico as $total3): ?>
                                 <?php echo $total3['total'] ?>
                            <?php endforeach ?>
                        </td>
                        <td>
                            <?php foreach($totalNoDecisorOperativo as $total4): ?>
                                 <?php echo  $total4['total'] ?>
                             <?php endforeach ?>
                        </td>
                    </tr>
                </tbody>
            </table>
           </div>
       </div> -->
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

  const ctxd = document.getElementById('myChartDirector').getContext('2d')

  const ctxc = document.getElementById('myChartCliente').getContext('2d')

  const ctx  = document.getElementById('myChartAdmin').getContext('2d')
  const ctx2 = document.getElementById('myChartAdmin2').getContext('2d')

  const clienteAdmin = document.getElementById('clienteAdmin').innerHTML
  const decisorAdmin = document.getElementById('decisorAdmin').innerHTML
  const estrategicoAdmin = document.getElementById('estrategicoAdmin').innerHTML
  const operativoAdmin = document.getElementById('operativoAdmin').innerHTML


  const bogota = document.getElementById('bogota').innerHTML
  const medellin = document.getElementById('medellin').innerHTML



fetch('<?php echo Url::to(['/hojavida/resumenapi']) ?>')
    .then(res  => res.json())
    .then(data => {

        const chart = new Chart(ctxd,{
            type: 'bar',
            data: {
                labels:    data.map(item => item.nombre),
                datasets: [{
                    label: 'Totales de clientes',
                    data:  data.map(item => item.total),
                    backgroundColor: [
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        
                    ],
                    borderColor: [
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        '#4298B5',
                        

                    ],
                    borderWidth: 1
                    /* s */
                }]
            },
            options: options
        })
         
    })

fetch('<?php echo Url::to(['/hojavida/resumenapicliente']) ?>')
    .then(res  => res.json())
    .then(data => {
        const chart = new Chart(ctxc,{
            type: 'bar',
            data: {
                labels:    data.map(item => item.cliente),
                datasets: [{
                    label: 'Totales de Clientes',
                    data:  data.map(item => item.total),
                    backgroundColor: [
                        '#ccc',
                        '#ccc',
                        '#ccc',
                        '#ccc',
                        
                    ],
                    borderColor: [
                        '#ccc',
                        '#ccc',
                        '#ccc',
                        '#ccc',
                    ],
                    borderWidth: 1
                    /* s */
                }]
            },
            options: options
        })
    })


const chart = new Chart(ctx,{
    type: 'bar',
    data: {
        labels: ['Total Clientes', 'Total Decisor', 'Total Estrategicos', 'Total Operativo'],
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
            /* s */
        }]
    },
    options: options
  })



const charts = new Chart(ctx2,{
    type: 'doughnut',
    data: {
        labels: ['Total Clientes Bogota', 'Total Clientes Medellin'],
        datasets: [{
            label: 'Totales de Clientes por clasificacion de ciudad',
            data: [bogota, medellin, ],
            backgroundColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)', 
                
            ],
            borderColor: [
                'rgba(75, 192, 192, 1)',
                'rgba(255, 99, 132, 1)', 
            ],
            borderWidth: 1
        }]
    },
    options: options
  })

</script>

 