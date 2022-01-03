<style>
#container_preloader{
 background-color: #7373734f;
 position: fixed;
 height: 100%;
 width: 100%;
 top: 0px;
 left: 0px;
}
#preloader_automatizacion{
 position: fixed;
 top: 50%;
 left: 50%;
 transform: translate(-50%,-50%);
}

</style>

<?php
echo "<div id='container_preloader' class = 'invisible'><img src = '/qa_managementv2/web/images/loader_automatizacion.svg' id='preloader_automatizacion'></div>";

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel app\models\EquiposSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Equipos');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
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
</style>
<header class="masthead">
 <div class="container h-100">
   <div class="row h-100 align-items-center">
     <div class="col-12 text-center">
     </div>
   </div>
 </div>
</header>
<br>
<br>
<div class="equipos-index">



    <?= Html::encode($this->title) ?>

    <p>
        <?php
        echo Html::a(Yii::t('app', 'Create Equipos'), ['create'],
                ['class' => 'btn btn-success']);
        echo "&nbsp;";
        ?>
<script>
function execAuto() 
{
  if(confirm("�Ejecutar este proceso podria afectar la estructura de equipos actual, desea continua?")){
  var preloader = document.getElementById("container_preloader");
  preloader.classList.remove("invisible");
  var botonReal = document.getElementById("botonReal");
  botonReal.click();
  }
}
function execAutoSimu() 
{
  var preloader = document.getElementById("container_preloader");
  preloader.classList.remove("invisible");
  var botonRealSimu = document.getElementById("botonRealSimu");
  botonRealSimu.click();
}

</script>

    </p> 
    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            //['class' => 'yii\grid\SerialColumn'],
            'id',
            'name',
            [
                'attribute' => 'equipo',
                'format' => 'raw',
                'value' => function ($data) {
                    return Html::a(Yii::t('app', 'Ver Equipo'),
                                    'javascript:void(0)',
                                    [
                                'title' => Yii::t('app', 'Equipo'),
                                //'data-pjax' => '0',
                                'onclick' => "                                    
                                            $.ajax({
                                            type     :'POST',
                                            cache    : false,
                                            url  : '" . Url::to(['equiposevaluados/index',
                                    'equipo_id' => $data->id]) . "',
                                            success  : function(response) {
                                                $('#ajax_result').html(response);
                                            }
                                           });
                                           return false;",
                    ]);
                }
                    ],
                    //'nmumbral_verde',
                    //'nmumbral_amarillo',
                    //'usua_id',
                    ['class' => 'yii\grid\ActionColumn'],
                ],
            ]);
            ?>
            <?php
            echo Html::tag('div', '', ['id' => 'ajax_result']);
            ?>
</div>


