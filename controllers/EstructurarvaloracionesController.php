<?php

namespace app\controllers;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\web\Response;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\db\mssql\PDO;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\helpers\Url;
use PHPExcel;
use PHPExcel_IOFactory;
use app\models\UploadForm2;
use app\models\FormUploadtigo;
use app\models\SpeechServicios;
use Exception;

  class EstructurarvaloracionesController extends Controller {

    public function behaviors(){
        return[
            'access' => [
                'class' => AccessControl::classname(),
                'only' => ['index'],
                'rules' => [
                    [
                        'allow' => true,
                        'roles' => ['@'],
                        'matchCallback' => function() {
                            return Yii::$app->user->identity->isAdminSistema();
                        },
                    ],
                ]
            ],
            'verbs' => [          
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['get'],
                ],
            ],

            'corsFilter' => [
                'class' => \yii\filters\Cors::class,
            ],
        ];
    } 

    public function actions() {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ]
        ];
    }
   
    public function actionIndex(){  
        $model = new SpeechServicios();
        $arrayDataPcrc = array();
        $varListaValoraciones = array();
        $varNombres = null;

        $form = Yii::$app->request->post();
        if ($model->load($form)) {
            $varFecha_BD = explode(" ", $model->fechacreacion);

            $varFechaInicio_BD = $varFecha_BD[0];
            $varFechaFin_BD = date('Y-m-d',strtotime($varFecha_BD[2]));

            $varNombres = (new \yii\db\Query())
                            ->select([
                                'tbl_arbols.name'
                            ])
                            ->from(['tbl_arbols'])
                            ->where(['in','tbl_arbols.id',$model->arbol_id])
                            ->andwhere(['=','tbl_arbols.snhoja',0])
                            ->scalar();

            $varClientes = (new \yii\db\Query())
                            ->select([
                                'tbl_arbols.id'
                            ])
                            ->from(['tbl_arbols'])
                            ->where(['in','tbl_arbols.id',$model->arbol_id])
                            ->andwhere(['=','tbl_arbols.snhoja',0])
                            ->all();

            if (count($varClientes) > 0) {
                $arrayListaPcrcClientes = array();
                foreach ($varClientes as $value) {
                    array_push($arrayListaPcrcClientes, $value['id']);
                }

                $varListaPcrcCliente = (new \yii\db\Query())
                                        ->select([
                                            'tbl_arbols.id'
                                        ])
                                        ->from(['tbl_arbols'])
                                        ->where(['in','tbl_arbols.arbol_id',$arrayListaPcrcClientes])
                                        ->andwhere(['=','tbl_arbols.activo',0])     
                                        ->all();

                foreach ($varListaPcrcCliente as $value) {
                    array_push($arrayDataPcrc, $value['id']);
                }
                
            }

            $varListaValoraciones = (new \yii\db\Query())
                                    ->select([
                                        '*'
                                    ])
                                    ->from(['tbl_ejecucionformularios'])
                                    ->where(['>','tbl_ejecucionformularios.score',1])
                                    ->andwhere(['between','tbl_ejecucionformularios.created',$varFechaInicio_BD.' 00:00:00',$varFechaFin_BD.' 23:59:59'])   
                                    ->andwhere(['in','tbl_ejecucionformularios.arbol_id',$arrayDataPcrc])  
                                    ->all();


            // $varRta2 = (0*1)+(0*0.98)+(1*0.86979);
            // var_dump($varRta2);
            // die(json_encode("Aqui seguimos"));
            if (count($varListaValoraciones) > 0) {
                $varValoracionId = null;
                
                foreach ($varListaValoraciones as $value) {
                    $varValoracionId = $value['id'];

                    $varPeso_i1_nmfactor = 0;
                    $varPeso_i2_nmfactor = 0;
                    $varPeso_i3_nmfactor = 0;
                    $varPeso_i4_nmfactor = 0;
                    $varPeso_i5_nmfactor = 0;
                    $varPeso_i6_nmfactor = 0;
                    $varPeso_i7_nmfactor = 0;
                    $varPeso_i8_nmfactor = 0;
                    $varPeso_i9_nmfactor = 0;
                    $varPeso_i10_nmfactor = 0;
                    $varCalculo_i1_nmcalculo = 0;
                    $varCalculo_i2_nmcalculo = 0;
                    $varCalculo_i3_nmcalculo = 0;
                    $varCalculo_i4_nmcalculo = 0;
                    $varCalculo_i5_nmcalculo = 0;
                    $varCalculo_i6_nmcalculo = 0;
                    $varCalculo_i7_nmcalculo = 0;
                    $varCalculo_i8_nmcalculo = 0;
                    $varCalculo_i9_nmcalculo = 0;
                    $varCalculo_i10_nmcalculo = 0;

                    if ($value['i1_nmfactor']) {
                        $varPeso_i1_nmfactor = $value['i1_nmfactor'];
                    }
                    if ($value['i2_nmfactor']) {
                        $varPeso_i2_nmfactor = $value['i2_nmfactor'];
                    }
                    if ($value['i3_nmfactor']) {
                        $varPeso_i3_nmfactor = $value['i3_nmfactor'];
                    }
                    if ($value['i4_nmfactor']) {
                        $varPeso_i4_nmfactor = $value['i4_nmfactor'];
                    }
                    if ($value['i5_nmfactor']) {
                        $varPeso_i5_nmfactor = $value['i5_nmfactor'];
                    }
                    if ($value['i6_nmfactor']) {
                        $varPeso_i6_nmfactor = $value['i6_nmfactor'];
                    }
                    if ($value['i7_nmfactor']) {
                        $varPeso_i7_nmfactor = $value['i7_nmfactor'];
                    }
                    if ($value['i8_nmfactor']) {
                        $varPeso_i8_nmfactor = $value['i8_nmfactor'];
                    }
                    if ($value['i9_nmfactor']) {
                        $varPeso_i9_nmfactor = $value['i9_nmfactor'];
                    }
                    if ($value['i10_nmfactor']) {
                        $varPeso_i10_nmfactor = $value['i10_nmfactor'];
                    }


                    if ($value['i1_nmcalculo']) {
                        $varCalculo_i1_nmcalculo = $value['i1_nmcalculo'];
                    }
                    if ($value['i2_nmcalculo']) {
                        $varCalculo_i2_nmcalculo = $value['i2_nmcalculo'];
                    }
                    if ($value['i3_nmcalculo']) {
                        $varCalculo_i3_nmcalculo = $value['i3_nmcalculo'];
                    }
                    if ($value['i4_nmcalculo']) {
                        $varCalculo_i4_nmcalculo = $value['i4_nmcalculo'];
                    }
                    if ($value['i5_nmcalculo']) {
                        $varCalculo_i5_nmcalculo = $value['i5_nmcalculo'];
                    }
                    if ($value['i6_nmcalculo']) {
                        $varCalculo_i6_nmcalculo = $value['i6_nmcalculo'];
                    }
                    if ($value['i7_nmcalculo']) {
                        $varCalculo_i7_nmcalculo = $value['i7_nmcalculo'];
                    }
                    if ($value['i8_nmcalculo']) {
                        $varCalculo_i8_nmcalculo = $value['i8_nmcalculo'];
                    }
                    if ($value['i9_nmcalculo']) {
                        $varCalculo_i9_nmcalculo = $value['i9_nmcalculo'];
                    }
                    if ($value['i10_nmcalculo']) {
                        $varCalculo_i10_nmcalculo = $value['i10_nmcalculo'];
                    }

                    $varScore = ($varPeso_i1_nmfactor*$varCalculo_i1_nmcalculo)+($varPeso_i2_nmfactor*$varCalculo_i2_nmcalculo)+($varPeso_i3_nmfactor*$varCalculo_i3_nmcalculo)+($varPeso_i4_nmfactor*$varCalculo_i4_nmcalculo)+($varPeso_i5_nmfactor*$varCalculo_i5_nmcalculo)+($varPeso_i6_nmfactor*$varCalculo_i6_nmcalculo)+($varPeso_i7_nmfactor*$varCalculo_i7_nmcalculo)+($varPeso_i8_nmfactor*$varCalculo_i8_nmcalculo)+($varPeso_i9_nmfactor*$varCalculo_i9_nmcalculo)+($varPeso_i10_nmfactor*$varCalculo_i10_nmcalculo);

                    Yii::$app->db->createCommand()->update('tbl_ejecucionformularios',[
                        'score' => $varScore,                                                
                    ],'id ='.$varValoracionId.'')->execute();
                    
                }
            }else{
                return $this->redirect(['index']);
            }           

        }
      
        return $this->render('index',[
            'model' => $model,
            'varListaValoraciones' => $varListaValoraciones,
            'varNombres' => $varNombres,
        ]);
    }


}

?>


