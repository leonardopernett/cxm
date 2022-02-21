<?php
/* @var $this yii\web\View */

use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use kartik\select2\Select2;
use yii\web\JsExpression;
use kartik\daterange\DateRangePicker;
use kartik\export\ExportMenu;
use yii\helpers\Url;

$this->title = Yii::t('app', 'Reporte Feedback Express');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Repotes'), 'url' => ['feedbackexpress']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php
$template = '<div class="col-md-4">{label}</div><div class="col-md-8">'
        . ' {input}{error}{hint}</div>';
$diasMes=date("d");
?>

<div class="page-header">
    <h3><?= $this->title ?></h3>
</div>
<?php
foreach (Yii::$app->session->getAllFlashes() as $key => $message) {
    echo '<div class="alert alert-' . $key . '" role="alert">' . $message . '</div>';
}
?>
<div class="equipos-evaluados-form">    

    <?php $form = ActiveForm::begin([
        'layout' => 'horizontal',
        'fieldConfig' => [
            'inputOptions' => ['autocomplete' => 'off']
          ]
        ]); ?>
    
    <?php echo Html::hiddenInput('Ejecucionfeedbacks[evaluado_id]', $model->evaluado_id); ?>
    <div class="row">
        <div class="col-md-6">  
            <?=
            $form->field($model, 'created', [
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
                    'format' => 'Y-m-d',
                    'startDate' => date("Y-m-d", strtotime(date("Y-m-d") . " -1 day")),
                    'endDate' => date("Y-m-d"),
                    'opens' => 'center',
                    'minDate'=>date("Y-m-d", strtotime(date("Y-m-d") . "-$diasMes day -1 month")),
                    'maxDate'=>date("Y-m-d", strtotime(date("Y-m-d") . " now"))
            ]]);
            ?>
        </div>    
    </div>    

   
  
         
   
    <div class="form-group">
        <div class="col-sm-offset-2 col-sm-10">
            <?=
            Html::submitButton(Yii::t('app', 'Buscar'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary'])
            ?>
        </div>        
    </div>

    <?php ActiveForm::end(); ?>    
</div>


<?php if ($showGrid): ?>
    <div class="page-header">
        <h3><?= Yii::t('app', 'Resultados') ?></h3>
    </div>
    <?php
    $gridColumns = [
        'created',
        [
            'attribute' => 'snaviso_revisado',
            'value' => function($data) {
                return $data->getGestionado($data->snaviso_revisado);
            }
        ],
        [
            'attribute' => 'identificacion lider',
            'value' => 'usuariolider.usua_identificacion'
        ],
        [
            'attribute' => 'usua_id_lider',
            'value' => 'usuariolider.usua_nombre'
        ],
        [
            'attribute' => 'usua_id',
            'value' => 'usuario.usua_nombre'
        ],
        [
            'attribute' => 'identificacion evaluado',
            'value' => 'evaluado.identificacion'
        ],
        [
            'attribute' => 'evaluado_id',
            'value' => 'evaluado.name'
        ],
        [
            'attribute' => 'ejecucionformulario_id',
            'value' => 'ejecucionformulario.formulario.name'
        ],
        'feaccion_correctiva',
        [
            'attribute' => 'categoriaFeedback',
            'value' => 'tipofeedback.categoriafeedback.name'
        ],
        [
            'attribute' => 'tipofeedback_id',
            'value' => 'tipofeedback.name'
        ],
        'dscausa_raiz',
        'dsaccion_correctiva',
        'dscompromiso',
        'dscomentario',
        'basessatisfaccion_id',
        ['class' => 'yii\grid\ActionColumn',
            'template' => '{preview}',
            'buttons' => [
                'preview' => function ($url, $model) {
                    if ($url == "asda") {
                        #code...
                    }
                    $ejecucion = \app\models\Ejecucionformularios::findOne(["id"=>$model->ejecucionformulario_id]);
                    if (isset($ejecucion->basesatisfaccion_id)) {
                        $modelBase = app\models\BaseSatisfaccion::findOne($ejecucion->basesatisfaccion_id);
                    }  
                    
                    if (!isset($ejecucion->basesatisfaccion_id)) {
                            return app\models\Ejecucionfeedbacks::hasFormulario($model->id) ? Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['formularios/showformulariodiligenciadoamigo'
                                                , 'form_id' => base64_encode($model->id)]), [
                                        'title' => Yii::t('yii', 'ver formulario'),
                                        'target' => "_blank"
                                    ]) : false;                      
                    }else { 
             
                                return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['basesatisfaccion/showformulariogestionamigo'
                                                    ,'basesatisfaccion_id' => base64_encode($modelBase->id),'preview'=>1,'fill_values'=>true]), [
                                            'title' => Yii::t('yii', 'ver formulario'),
                                            'target' => "_blank"
                                ]);
                           
                        }
                },
                        'update' => function ($url, $model) {
                    $page = Yii::$app->request->get('page');
                    $numPage = (empty($page)) ? 1 : $page;
                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', '', [
                                'title' => Yii::t('yii', 'Update'),
                                'data-pjax' => 'w0',
                                'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['reportes/updatefeedback'
                                    , 'id' => $model->id, 'page' => $numPage]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                    ]);
                },
                        'calculate' => function ($url, $model) {
                        return Html::a('<span class="glyphicon glyphicon-stats"></span>', '', [
                                    'title' => Yii::t('yii', 'Calculos'),
                                    'data-pjax' => 'w0',
                                    'onclick' => "                                    
                                $.ajax({
                                type     :'POST',
                                cache    : false,
                                url  : '" . Url::to(['reportes/calculatefeedback'
                                        , 'formulario_id' => $model->ejecucionformulario_id]) . "',
                                success  : function(response) {
                                    $('#ajax_result').html(response);
                                }
                               });
                               return false;",
                        ]);
                    
                },
                    ]
                ],
            ];

           
            ?>
            <br/><br/>
            <?php
            echo kartik\grid\GridView::widget([
                'dataProvider' => $dataProvider,
                'columns' => $gridColumns,
            ]);
            ?>

            <?php
            echo Html::tag('div', '', ['id' => 'ajax_result']);
            ?>

        <?php endif; ?>



