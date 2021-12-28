<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\bootstrap\ActiveForm;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use yii\db\Query;
use yii\helpers\ArrayHelper;

$this->title = 'Plan de Valoracion Tecnico';
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';

    $sessiones = Yii::$app->user->identity->id;


    $rol =  new Query;
    $rol     ->select(['tbl_roles.role_id'])
                ->from('tbl_roles')
                ->join('LEFT OUTER JOIN', 'rel_usuarios_roles',
                            'tbl_roles.role_id = rel_usuarios_roles.rel_role_id')
                ->join('LEFT OUTER JOIN', 'tbl_usuarios',
                            'rel_usuarios_roles.rel_usua_id = tbl_usuarios.usua_id')
                ->where('tbl_usuarios.usua_id = '.$sessiones.'');                    
    $command = $rol->createCommand();
    $roles = $command->queryScalar();

?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Reporte-valorados.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Full Page Image Header with Vertically Centered Content -->
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br>
<div class="control-procesos-index">

<br>



<?php if ($roles == "272") { ?>
    
    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            //'filterModel' => $searchModel,
            'columns' => [

                [
                    'attribute' => 'Valorador',
                    'value' => 'usuarios.usua_nombre',
                ],
                [
                    'attribute' => 'Cantidad de Valoraciones',
                    'value' => function($data){
                        return $data->getMetas($data->id, $data->evaluados_id);
                    }
                ],
                // [
                //     'attribute' => 'Salario - $',
                //     'value' => 'salario',
                // ],
                [
                    'attribute' => 'Tipo de Corte',
                    'value' => 'tipo_corte',
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'headerOptions' => ['style' => 'color:#337ab7'],                
                    'template' => '{view}',
                    'buttons' => 
                    [
                        'view' => function ($url, $model) {
                           
                            return Html::a('<span class="glyphicon glyphicon-eye-open"></span>',  ['view', 'id' => $model->id, 'evaluados_id' => $model->evaluados_id], [
                                'class' => '',
                                'data' => [
                                    'method' => 'post',
                                ],
                            ]);
                        }                    
                    ]
                  
                ],
            ],
        ]); 
    ?>
<?php }else{ ?>
    <?php if ($roles == "276" || $roles == "270" || $roles == "274") { ?> 
        <div class="panel panel-primary">
            <div class="panel-heading">Informacion...</div>
            <div class="panel-body">Solo los coordinadores de los equipos pueden generar la opcion de exportar a su equipo en un archivo de excel sobre el mes actual.</div>            
        </div>
        <br>
        <button  class="btn btn-info" style="background-color: #4298B4" onclick="exportTableToExcel('tblData', 'Equipo de Valoradores')">Exportar a Excel</button>
        <br>
        <?php
            $varMes = date("n");
                $txtMes = null;
                switch ($varMes) {
                    case '1':
                        $txtMes = "Enero";
                        break;
                    case '2':
                        $txtMes = "Febrero";
                        break;
                    case '3':
                        $txtMes = "Marzo";
                        break;
                    case '4':
                        $txtMes = "Abril";
                        break;
                    case '5':
                        $txtMes = "Mayo";
                        break;
                    case '6':
                        $txtMes = "Junio";
                        break;
                    case '7':
                        $txtMes = "Julio";
                        break;
                    case '8':
                        $txtMes = "Agosto";
                        break;
                    case '9':
                        $txtMes = "Septiembre";
                        break;
                    case '10':
                        $txtMes = "Octubre";
                        break;
                    case '11':
                        $txtMes = "Noviembre";
                        break;
                    case '12':
                        $txtMes = "Diciembre";
                        break;
                    default:
                        # code...
                        break;
                }

            $txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where anulado = 0 and tipo_corte like "%'.$txtMes.'%"')->queryScalar();             
                    
            $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();
            $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();  

            $query = Yii::$app->db->createCommand("select * from tbl_control_procesos where responsable = '$sessiones' and anulado = 0 and fechacreacion between '$fechainiC' and '$fechafinC'")->queryAll();  

        ?>
        <br>
            <div style="display:none">
                <table id="tblData" class="table table-striped table-bordered tblResDetFreed">
                <caption style="display: none;">...</caption>
                    <thead>
                        <th scope="col" class="text-center" ><?= Yii::t('app', 'Tecnico/Lider') ?></th>
                        <th scope="col" class="text-center" ><?= Yii::t('app', 'Cantidad de del Plan') ?></th>
                        <th scope="col" class="text-center" ><?= Yii::t('app', 'Tipo de Corte') ?></th>
                    </thead>
                    <tbody>
                        <?php
                          $queryControl = Yii::$app->db->createCommand("select * from tbl_control_procesos where anulado = 0 and responsable = $sessiones")->queryAll(); 

                            foreach ($queryControl as $key => $value) {
                                $varUsuario = $value['evaluados_id'];
                                $varFecha = $value['fechacreacion'];
                                $txtTipocorte = $value['tipo_corte'];
                                $txtUsuario = Yii::$app->db->createCommand("select distinct usua_nombre from tbl_usuarios where usua_id = $varUsuario")->queryScalar();  
                                $txtCantidad =  Yii::$app->db->createCommand("select sum(cant_valor) from tbl_control_params where anulado = 0 and evaluados_id = $varUsuario and fechacreacion = '$varFecha'")->queryScalar();                 
                        ?>
                            <tr>     
                              <td><?=  $txtUsuario; ?></td>         
                              <td><?=  $txtCantidad; ?></td>
                              <td><?=  $txtTipocorte; ?></td>
                            </tr>
                        <?php } ?>                    
                    </tbody>
                </table>            
            </div>

    <?php }else{ ?>
        <div class="panel panel-warning">
          <div class="panel-heading">Importante</div>
          <div class="panel-body">No es posible ver resultados ya que no tiene pemisos a utilizar este módulo.</div>
        </div>        
    <?php } ?>
<?php } ?>
</div>
<script type="text/javascript">
  function exportTableToExcel(tableID, filename = ''){
      var downloadLink;
      var dataType = 'application/vnd.ms-excel';
      var tableSelect = document.getElementById(tableID);
      var tableHTML = tableSelect.outerHTML.replace(/ /g, '%20');
      
      // Specify file name
      filename = filename?filename+'.xls':'excel_data.xls';
      
      // Create download link element
      downloadLink = document.createElement("a");
      
      document.body.appendChild(downloadLink);
      
      if(navigator.msSaveOrOpenBlob){
          var blob = new Blob(['\ufeff', tableHTML], {
              type: dataType
          });
          navigator.msSaveOrOpenBlob( blob, filename);
      }else{
          // Create a link to the file
          downloadLink.href = 'data:' + dataType + ', ' + tableHTML;
      
          // Setting the file name
          downloadLink.download = filename;
          
          //triggering the function
          downloadLink.click();
      }
  }
  </script>
