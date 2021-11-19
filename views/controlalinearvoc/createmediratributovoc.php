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
use yii\bootstrap\modal;
use app\models\ControlvocListadopadre;

$this->title = 'Medicion de Atributos Alinear + VOC'; 
$this->params['breadcrumbs'][] = $this->title;

    $template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
    . ' {input}{error}{hint}</div>';    

    $sessiones = Yii::$app->user->identity->id;
    $fechaactual = date("Y-m-d");
    $varidAbol = $idAbol;
    $varsesionId = $sesionId;
    $varcategoriaId = $categoriaId;
    $varmodalhide = $modalhide;
    $varIdValora = $idvalora;
    $varanulado = 0;
    $txtNamePcrc = Yii::$app->db->createCommand("select name from tbl_arbols where id = '$varidAbol'")->queryScalar();
?>
<br>
<div class="row">
      <div class="col-md-offset-2 col-sm-8">
        <h4 class="text-center"><?php echo $txtNamePcrc; ?></h4>
        <br>
      </div>
</div>
<div class="formularios-form" style="display: inline" id="dtbloque1"> 

  <table class="table table-striped table-bordered detail-view formDinamico"  id="tabladatos">
  <caption>...</caption>
    <thead>
      <tr>
        <th scope="col" class="text-center"><?= Yii::t('app', 'Nombre Sesión') ?></th>
        <th scope="col" class="text-center"><?= Yii::t('app', 'Nombre Categoría') ?></th>
        <th scope="col" class="text-center"><?= Yii::t('app', 'Nombre Atributo') ?></th>        
        <th scope="col" class="text-center"><?= Yii::t('app', 'Atributos') ?></th>
        <th scope="col" class="text-center"><?= Yii::t('app', 'Acuerdo Alineación') ?></th>
      </tr>      
    </thead>
    <tbody>    
      <?php

       $txtQuery2 =  new Query;
                $txtQuery2  ->select(['tbl_categorias_alinear.id_categ_ali','tbl_arbols.name','tbl_sesion_alinear.sesion_nombre','tbl_categorias_alinear.categoria_nombre', 'tbl_atributos_alinear.atributo_nombre', 'tbl_atributos_alinear.id_atrib_alin', 'tbl_atributos_alinear.acuerdo', 'tbl_atributos_alinear.medicion'])
                            ->from('tbl_categorias_alinear')
                            ->join('INNER JOIN', 'tbl_sesion_alinear',
                                 'tbl_categorias_alinear.sesion_id = tbl_sesion_alinear.sesion_id')
                            ->join('INNER JOIN', 'tbl_arbols',
                                 'tbl_categorias_alinear.arbol_id = tbl_arbols.id')
                            ->join('INNER JOIN', 'tbl_atributos_alinear',
                                 'tbl_categorias_alinear.id_categ_ali = tbl_atributos_alinear.id_categ_ali')
                            ->where(['tbl_arbols.id' => $varidAbol])
                            ->andwhere(['tbl_categorias_alinear.sesion_id' => $varsesionId])
                            ->andwhere(['tbl_atributos_alinear.id_categ_ali' => $varcategoriaId]);
                $command = $txtQuery2->createCommand();
                $dataProvider = $command->queryAll();
        
  $index = 0;
        foreach ($dataProvider as $key => $value) {
      $index += 1;
            $varIdList = $value['id_categ_ali'];
            $varNomList = $value['name'];         
            $varSesionnomList = $value['sesion_nombre'];
            $varCategonomList = $value['categoria_nombre'];
            $varatributonomList = $value['atributo_nombre'];
            $varIdatributoList = $value['id_atrib_alin'];
            $varAcuerdoList = $value['acuerdo'];
            $varMedicionList = $value['medicion'];
            var_dump($varIdatributoList);
      ?>
      <tr>
        <td class="text-center"><?php echo $varSesionnomList; ?></td>
        <td class="text-center"><?php echo $varCategonomList; ?></td>
        <td class="text-center"><?php echo $varatributonomList; ?></td>
        <td class="text-center">
            <select class ='form-control' id="txtCategorizada_<?php echo $index?>" data-toggle="tooltip" >
                <option value="" disabled selected>Alineado Si/No</option> 
                <?php
                    $dataIndi =  new Query;
                    $dataIndi   ->select(['tbl_controlvoc_listadopadre.nombrelistap'])->distinct()
                                ->from('tbl_controlvoc_listadopadre')
                                ->where(['tbl_controlvoc_listadopadre.idsessionvoc' => '6']);                    
                    $command = $dataIndi->createCommand();
                    $data = $command->queryAll();
                     foreach ($data as $key => $value) {
                      // echo "<option value = '".$value['nombrelistap']."'>".$value['nombrelistap'].if($varMedicionList == $value['nombrelistap']) echo 'selected'."</option>"; 
                       echo "<option value='$value[nombrelistap]'> $value[nombrelistap] </option>";
                      }                
      ?>                                   
              </select> 
            
        </td>
        <td>
            <input type="text" class="form-control" id="txtacuerdoali" data-toggle="tooltip" title="Acuerdo de alineación">
         </td> 
         <td style="display:none;">
            <input type="text" class="form-control" id="idsesion" data-toggle="tooltip" value="<?php echo $varIdatributoList; ?>" >
         </td>
      </tr>
      <?php
        }
      ?>
    </tbody>    
  </table>

  <div class="row" style="text-align: center;">      
    <div onclick="generated1();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >
            Crear Medición
        </div>    
    </div>
</div>
<br>
<hr>
<script type="text/javascript">
  
  
  function generated1(){
    var table = document.getElementById("tabladatos");
     var rowCount = table.rows.length;
    
    var nomacue = document.querySelectorAll("#txtacuerdoali");    
    //var medir = document.querySelectorAll("#txtCategorizada");
    var idatrubu = document.querySelectorAll("#idsesion");

    //var varindica= "<?php echo $varmodalhide; ?>"; 
    var varModal = "<?php echo $varmodalhide; ?>";

// Cada campo es convertido a un array bidimensional 
    var nomacueValores = [];
    var varAcuerdo = null;
    var varMedir = null;
    var varIdatrubuti = null;
    var varindica = null;
    var varIdvalorado = "<?php echo $varIdValora; ?>";
    var varfechai   = "<?php echo $fechaactual; ?>";
    var varanulado = 0;

    for (var x = 0; x < nomacue.length; x++) {
         varAcuerdo = nomacue[x].value;
         varindica = "txtCategorizada_"+(x+1);
         varMedir = document.getElementById(varindica).value;
         varIdatrubuti = idatrubu[x].value;
   
      $.ajax({
              method: "post",
              url: "createmediratributoalinearvoc",
              data : {
                txtvacuerdo : varAcuerdo,
                txtvmedir : varMedir,
                txtvatributo : varIdatrubuti,
                txtvidvalorado : varIdvalorado,
                txtvfechai : varfechai,
                txtvanulado : varanulado,
              },
              success : function(response){ 
                          var numRta =   JSON.parse(response); 

                          if (numRta != 0) {
                            //console.log(varModal);
                  $(varModal).modal("hide");
                          }else{
                            event.preventDefault();
                        swal.fire("!!! Advertencia !!!","No se pueden guardar los datos.","warning");
                      return;
                          }
                      }
      });
    }
  };
</script>