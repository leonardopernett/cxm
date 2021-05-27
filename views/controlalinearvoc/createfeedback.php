<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use yii\web\JsExpression;

$template = '<div class="col-sm-12">'
            . ' {input}{error}{hint}</div>';

// $varvaloradoid;

    $varNamelider = Yii::$app->db->createCommand("select distinct tbl_usuarios.usua_nombre from tbl_usuarios inner join tbl_equipos on tbl_usuarios.usua_id = tbl_equipos.usua_id inner join tbl_equipos_evaluados on tbl_equipos.id = tbl_equipos_evaluados.equipo_id inner join tbl_evaluados on tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id   where tbl_evaluados.id = $varvaloradoid")->queryScalar();

    $varIdlider = Yii::$app->db->createCommand("select distinct tbl_usuarios.usua_id from tbl_usuarios inner join tbl_equipos on tbl_usuarios.usua_id = tbl_equipos.usua_id inner join tbl_equipos_evaluados on tbl_equipos.id = tbl_equipos_evaluados.equipo_id inner join tbl_evaluados on tbl_equipos_evaluados.evaluado_id = tbl_evaluados.id   where tbl_evaluados.id = $varvaloradoid")->queryScalar();

    
?>
<link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.3.1/css/all.css" integrity="sha384-mzrmE5qonljUremFsqc01SB46JvROS7bZs3IO2EmfFsd15uHvIt+Y8vEf7N7fWAU" crossorigin="anonymous">
<style type="text/css">
  @import url('https://fonts.googleapis.com/css?family=Nunito');
    .card {
            height: 200px;
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

    .card:hover  {
        top: -15%;
    }

    .control-label {
        font-size: 15px;
    }
</style>
<div class="PrimerBloque" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
          <div class="card1 mb">
            <label style="font-size: 20px;"><i class="fas fa-bookmark" style="font-size: 20px; color: #559FFF;"></i> Registro de feedback</label>
            <?php $form = ActiveForm::begin(['layout' => 'horizontal', 'id' => 'feedback']); ?>
            <div class="row">
                <div class="col-md-12">
                    <?php $var = ['Informacion correcto y completo' => 'Informacion correcto y completo', 'Procedimientos correctos y completos' => 'Procedimientos correctos y completos', 'Procedimientos en la parte de escucha' => 'Procedimientos en la parte de escucha', 'Procedimientos del tono de voz' => 'Procedimientos del tono de voz', 'Entiende la necesidad del usuario' => 'Entiende la necesidad del usuario']; ?>

                    <?= 
                        $form->field($model, 'dscausa_raiz')->dropDownList($var, ['prompt' => 'Seleccione...', 'id'=>"id_causas", ])->label('Atributos de calidad')
                    ?> 
                </div>
                <div class="col-md-12">
                    <?php
                        echo $form->field($model, 'usua_id_lider')->textInput(['maxlength' => 200, 'id'=>"Name_lider", 'readonly'=>'readonly', 'value' => $varNamelider])->label("Lider de equipo")     
                    ?>
                </div>
                <div class="col-md-12">
                    <?=
                        $form->field($model, 'catfeedback')->dropDownList(app\models\Categoriafeedbacks::getCategoriasList(), ['id' => 'cat-id', 'prompt' => Yii::t('app', 'Select ...')])->label("Categoría")
                    ?> 
                </div>
                <div class="col-md-12">
                    <?php
                        echo $form->field($model, 'tipofeedback_id')->widget(DepDrop::classname(), [
                            'options' => ['id' => 'tipo-id'],
                            'pluginOptions' => [
                                'depends' => ['cat-id'],
                                'placeholder' => Yii::t('app', 'Select ...'),
                                'url' => Url::to(['/feedback/tipofeedback'])
                            ]
                        ]);
                    ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'dscomentario')->textArea(['rows' => '6', 'id'=>'comen-id']) ?>
                    <?php
                        echo $form->field($model, 'usua_id_lider')->textInput(['maxlength' => 200, 'id'=>"id_lider", 'class'=>'hidden'])->label("")     
                    ?>
                </div>
                <div class="col-md-12">
                    <div onclick="feedback();" class="btn btn-primary" style="display:inline;" method='post' id="botones2" >  Crear feedback
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
          </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    function feedback(){
        var varid_causas = document.getElementById("id_causas").value;
        var varName_lider = document.getElementById("Name_lider").value;
        var varcat_id = document.getElementById("cat-id").value;
        var vartipo_id = document.getElementById("tipo-id").value;
        var varcomen_id = document.getElementById("comen-id").value;
        var varvalorado = "<?php echo $varvaloradoid; ?>";

        if (vartipo_id != "") {
            $.ajax({
                method: "get",
                url: "crearfeedback",
                data: {
                    txtvarid_causas : varid_causas,
                    txtvarName_lider : varName_lider,
                    txtvarcat_id : varcat_id,
                    txtvartipo_id : vartipo_id,
                    txtvarcomen_id : varcomen_id,
                    txtvarvalorado : varvalorado,
                },
                success : function(response){
                    var numRta =   JSON.parse(response);
                    console.log(numRta);
                    if (numRta != 0) {
                        $("#modal1").modal("hide");
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