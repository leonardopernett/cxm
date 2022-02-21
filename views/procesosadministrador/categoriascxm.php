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


$this->title = 'Procesos Categorias';
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

    .masthead {
        height: 25vh;
        min-height: 100px;
        background-image: url('../../images/ADMINISTRADOR-GENERAL.png');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        border-radius: 5px;
        box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
    }

</style>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
      </div>
    </div>
  </div>
</header>
<br><br>
<?php if ($roles == '270') { ?>
<div class="CapaPp" style="display : inline;">
    <?php $form = ActiveForm::begin([
        'options' => ["id" => "buscarMasivos"],
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="card1 mb">
                <label for=""><em class="fas fa-save" style="font-size: 20px; color: #ffc034;"></em> Ingresar Categorias</label>
                
                <?= $form->field($modelpadre, 'name', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idtxtcategoria', 'placeholder'=>'Ingresar nueva categoria'])?>

                <?= Html::submitButton(Yii::t('app', 'Guardar'),
                                ['class' => $modelpadre->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Guardar Categoria',
                                    'onclick' => 'validar();']) 
                ?> 
                   
            </div>
        </div>

        <div class="col-md-6">
            <div class="card1 mb">
                <label for=""><em class="fas fa-save" style="font-size: 20px; color: #ffc034;"></em> Ingresar Categoria Hijo</label>
                <div class="row">
                    <div class="col-md-6">
                        <?=  $form->field($modelhijo, 'categoriafeedback_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->dropDownList(ArrayHelper::map(\app\models\Categoriafeedbacks::find()->orderBy(['id'=> SORT_DESC])->all(), 'id', 'name'),
                                        [
                                            'prompt'=>'Seleccione Categoria...',
                                        ]
                                )->label(''); 
                        ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($modelhijo, 'name', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])->textInput(['maxlength' => 300, 'id'=>'idtxtcategoriahijo', 'placeholder'=>'Ingresar categoria hijo'])?>
                    </div>
                </div>
                
                <?= Html::submitButton(Yii::t('app', 'Guardar'),
                                ['class' => $modelhijo->isNewRecord ? 'btn btn-success' : 'btn btn-primary',
                                    'data-toggle' => 'tooltip',
                                    'title' => 'Guardar Categoria hijo',
                                    'onclick' => 'validarhijo();']) 
                ?> 
                
            </div>
        </div>
    </div>
    <?php $form->end() ?> 
</div>
<hr>
<div class="capaList" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label for=""><em class="fas fa-list" style="font-size: 20px; color: #ffc034;"></em> Lista de Categorias</label>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'attribute' => 'Id categoria',
                            'value' => 'id',
                        ],
                        [
                            'attribute' => 'Nombre categoria',
                            'value' => 'name',
                        ],
                    ]
                ]);?>   
            </div>        
        </div>
    </div>
</div>
<hr>
<?php }else{ ?>
<div class="capaMensaje" style="display: inline;">
    <div class="row">
        <div class="col-md-12">
            <div class="card1 mb">
                <label for=""><em class="fas fa-info" style="font-size: 40px; color: #ffc034;"></em> Su rol actualmente no tiene permisos para ver este modulo; si necesita verlo solicitar acceso con el administrador de la herramienta.</label>
            </div>        
        </div>
    </div>
</div>
<hr>
<?php } ?>


<script type="text/javascript">
    function validar(){
        var varidtxtcategoria =  document.getElementById("idtxtcategoria").value;

        if (varidtxtcategoria == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de ingresar una categoria","warning");
            return;
        }
    };

    function validarhijo(){
        var varcategoriafeedbacksid = document.getElementById("categoriafeedbacks-id").value;
        var varidtxtcategoriahijo =  document.getElementById("idtxtcategoriahijo").value; 

        if (varcategoriafeedbacksid == "") {
            event.preventDefault();
            swal.fire("!!! Advertencia !!!","Debe de seleccionar una categoria","warning");
            return;
        }else{
            if (varidtxtcategoriahijo == "") {
                event.preventDefault();
                swal.fire("!!! Advertencia !!!","Debe de ingresar una categoria hijo","warning");
                return;
            }
        }
    };
</script>