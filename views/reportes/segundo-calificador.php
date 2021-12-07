<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Segundo Calificador');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['reportesegundocalificador']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
?>
<style>
  .masthead {
    height: 25vh;
    min-height: 100px;
    background-image: url('../../images/Reporte-2-calificador.png');
    background-size: cover;
    background-position: center;
    background-repeat: no-repeat;
    /*background: #fff;*/
    border-radius: 5px;
    box-shadow: 0 20px 40px -10px rgba(0, 0, 0, 0.3);
  }

</style>
<!-- Full Page Image Header with Vertically Centered Content -->
<script>
    $(document).ready(function(){
        $.fn.snow();
    });
</script>
<script src="../../js_extensions/mijs.js"> </script>
<header class="masthead">
  <div class="container h-100">
    <div class="row h-100 align-items-center">
      <div class="col-12 text-center">
        <!-- <h1 class="font-weight-light">Vertically Centered Masthead Content</h1>
        <p class="lead">A great starter layout for a landing page</p> -->
      </div>
    </div>
  </div>
</header>
<br><br>

<!--<div class="page-header">
    <h3><?php //$this->title       ?></h3>
</div>-->

<div class="equipos-evaluados-form">    

    <?php //$form = ActiveForm::begin(['layout' => 'horizontal']); ?>

    <!--<div class="row">
        <div class="col-md-6">  
    <?php
    /* $form->field($model, 'created', [
      'labelOptions' => ['class' => 'col-md-12'],
      'template' => '<div class="col-md-4">{label}</div>'
      . '<div class="col-md-8"><div class="input-group">'
      . '<span class="input-group-addon" id="basic-addon1">'
      . '<i class="glyphicon glyphicon-calendar"></i>'
      . '</span>{input}</div>{error}{hint}</div>',
      'inputOptions' => ['aria-describedby' => 'basic-addon1'],
      'options' => ['class' => 'drp-container form-group']
      ])->widget(DateRangePicker::classname(), [
      'useWithAddon' => true,
      'convertFormat' => true,
      'presetDropdown' => true,
      'readonly' => 'readonly',
      'pluginOptions' => [
      'timePicker' => false,
      //'timePickerIncrement' => 15,
      'format' => 'Y-m-d',
      'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
      'endDate' => date("Y-m-d"),
      'opens' => 'right'
      ]]);
      ?>
      </div>
      <div class="col-md-6">
      <?=
      $form->field($model, 'arbol_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
      ->widget(Select2::classname(), [
      //'data' => array_merge(["" => ""], $data),
      'language' => 'es',
      'options' => ['placeholder' => Yii::t('app', 'Select ...')],
      'pluginOptions' => [
      'multiple' => true,
      'allowClear' => true,
      'minimumInputLength' => 3,
      'ajax' => [
      'url' => \yii\helpers\Url::to(['getarboles']),
      'dataType' => 'json',
      'data' => new JsExpression('function(term,page) { return {search:term}; }'),
      'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
      ],
      'initSelection' => new JsExpression('function (element, callback) {
      var id=$(element).val();
      if (id !== "") {
      $.ajax("' . Url::to(['getarboles']) . '?id=" + id, {
      dataType: "json",
      type: "post"
      }).done(function(data) { callback(data.results);});
      }
      }')
      ]
      ]
      );
      ?>
      </div>
      </div>

      <div class="row">
      <div class="col-md-6">
      <?=
      $form->field($model, 'usua_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
      ->widget(Select2::classname(), [
      'language' => 'es',
      'options' => ['placeholder' => Yii::t('app', 'Select ...'), 'multiple' => true,],
      'pluginOptions' => [
      'allowClear' => true,
      'minimumInputLength' => 4,
      'ajax' => [
      'url' => Url::to(['usuariolist']),
      'dataType' => 'json',
      'data' => new JsExpression('function(term,page) { return {search:term}; }'),
      'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
      ],
      'initSelection' => new JsExpression('function (element, callback) {
      var id=$(element).val();
      if (id !== "") {
      $.ajax("' . Url::to(['usuariolist']) . '?id=" + id, {
      dataType: "json",
      type: "post"
      }).done(function(data) { callback(data.results[0]);});
      }
      }')
      ]
      ]
      );
      ?>
      </div>
      <div class="col-md-6">
      <?=
      $form->field($model, 'evaluado_id', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
      ->widget(Select2::classname(), [
      //'data' => array_merge(["" => ""], $data),
      'language' => 'es',
      'options' => ['placeholder' => Yii::t('app', 'Select ...')],
      'pluginOptions' => [
      'allowClear' => true,
      'minimumInputLength' => 4,
      'ajax' => [
      'url' => \yii\helpers\Url::to(['evaluadolist']),
      'dataType' => 'json',
      'data' => new JsExpression('function(term,page) { return {search:term}; }'),
      'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
      ],
      'initSelection' => new JsExpression('function (element, callback) {
      var id=$(element).val();
      if (id !== "") {
      $.ajax("' . Url::to(['evaluadolist']) . '?id=" + id, {
      dataType: "json",
      type: "post"
      }).done(function(data) { callback(data.results[0]);});
      }
      }')
      ]
      ]
      );
      ?>
      </div>
      </div>
      <div class="row">
      <div class="col-md-6">
      <?=
      $form->field($model, 'usua_id_lider', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
      ->widget(Select2::classname(), [
      'language' => 'es',
      'options' => ['placeholder' => Yii::t('app', 'Select ...'),],
      'pluginOptions' => [
      'allowClear' => true,
      'minimumInputLength' => 4,
      'ajax' => [
      'url' => \yii\helpers\Url::to(['lidereslist']),
      'dataType' => 'json',
      'data' => new JsExpression('function(term,page) { return {search:term}; }'),
      'results' => new JsExpression('function(data,page) { return {results:data.results}; }'),
      ],
      'initSelection' => new JsExpression('function (element, callback) {
      var id=$(element).val();
      if (id !== "") {
      $.ajax("' . Url::to(['lidereslist']) . '?id=" + id, {
      dataType: "json",
      type: "post"
      }).done(function(data) { callback(data.results[0]);});
      }
      }')
      ]
      ]
      );
      ?>
      </div>
      <div class="col-md-6">
      <?=
      $form->field($model, 'snaviso_revisado', ['labelOptions' => ['class' => 'col-md-12'], 'template' => $template])
      ->dropDownList($model->gestionadoOptionList()) */
    ?>
        </div>    
    </div>
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <? /*=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])*/
            ?>
    <?php //Html::a(Yii::t('app', 'Cancel'), ['index'] , ['class' => 'btn btn-default'])      ?>
        </div>        
    </div>-->

    <?php //ActiveForm::end(); ?>    
</div>

<?php if ($showGrid): ?>
    <div class="col-sm-12">        
        <?php
        $gridColumns = [
            //['class' => 'yii\grid\SerialColumn'],
            's_fecha',
            'id_caso',
            [
                'header' => 'Programa/PCRC',
                'attribute' => 'id_ejecucion_formulario',
                'value' => function($data) {
                    $ef = app\models\Ejecucionformularios::findOne($data->id_ejecucion_formulario);
                    if (isset($ef)) {
                        $ar = \app\models\Arboles::findOne($ef->arbol_id);
                        return $ar->name;  
                    }else{
                       return '-';  
                    }                   
                }
            ],
            [
                'attribute' => 'id_solicitante',
                'value' => 'idSolicitante.name'
            ],
            [
                'attribute' => 'Cedula Valorado',
                'value' => 'idSolicitante.identificacion'
            ],
            [
                'attribute' => 'id_evaluador',
                'value' => 'idValorador.usua_nombre'
            ],
            [
                'attribute' => 'id_responsable',
                'value' => 'idResponsable.usua_nombre'
            ],
            [
                'attribute' => 'Cedula Responsable',
                'value' => 'idResponsable.usua_identificacion'
            ],
            [
                'attribute' => 'Rol Responsable',
                'value' => 'relUsuariosRoles.roles.role_nombre'
            ],    
            [
                'format' => 'html',
                'attribute' => 'argumento',
                'contentOptions' => [
                'style'=> (!$export) ? 'min-width: 200px; overflow: auto; word-wrap: break-word;' : ''
                ],
                'value' => (!$export) ? function($data) {
                    return substr($data->argumento, 0, 100) . '...';
                } :     function($data) {
                        return $data->argumento;
                }
                /*'contentOptions' => [
                    'style'=>'min-width: 200px; overflow: auto; word-wrap: break-word;'
                ],
                'value' => function($data) {
                    return substr($data['argumento'], 0, 100) . '...';
                }*/
            ],
            [
                'format' => 'html',
                'attribute' => 'estado',
                'value' => function($data) {
                    return $data->getEstados($data->estado_sc);
                }
            ],
        ];

        echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
            'columnSelectorOptions' => [
                'label' => Yii::t('app', 'Columns'),
            ],
            'dropdownOptions' => [
                'label' => Yii::t('app', 'Export All'),
                'class' => 'btn btn-default'
            ],
            //'fontAwesome' => true,
            'showConfirmAlert' => false,
            'target' => '_blank',
            'filename' => Yii::t('app', 'Reporte_feedback') . '_' . date('Ymd'),
            'exportRequestParam' => 'exportsegundocalificador',
            'columnBatchToggleSettings' => [
                'label' => Yii::t('app', 'All')
            ],
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_PDF => false,
                ExportMenu::FORMAT_HTML => false,
            ]
        ]);
        ?>
        <br/><br/>
        <?php
        echo kartik\grid\GridView::widget([
            'dataProvider' => $dataProvider,
            'columns' => $gridColumns,
        ]);
        ?>
    </div>
    <?php
    echo Html::tag('div', '', ['id' => 'ajax_result']);
    ?>

<?php endif; ?>