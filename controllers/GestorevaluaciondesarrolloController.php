<?php

namespace app\controllers;

ini_set('upload_max_filesize', '50M');

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
use PHPExcel;
use PHPExcel_IOFactory;
use yii\base\Exception;
use app\models\GestorEvaluacionPreguntas;
use app\models\EvaluacionRespuestas;

class GestorevaluaciondesarrolloController extends \yii\web\Controller {

    public function behaviors(){
        return[
        'access' => [
            'class' => AccessControl::classname(),
            'only' => ['index','parametrizador'],
            'rules' => [
                [
                'allow' => true,
                'roles' => ['@'],
                'matchCallback' => function() {
                            return Yii::$app->user->identity->isCuadroMando()  || Yii::$app->user->identity->isVerexterno() || Yii::$app->user->identity->isVerevaluacion() || Yii::$app->user->identity->isVerdirectivo();
                        },
                ],
            ]
            ],
        'verbs' => [          
            'class' => VerbFilter::className(),
            'actions' => [
            'delete' => ['post'],
            ],
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
      return $this->render('index');
    }

    public function actionParametrizador(){
        $modalPreguntas = new GestorEvaluacionPreguntas();
        $modalRespuestas = new EvaluacionRespuestas();       

        $array_preguntas = [
            ['pregunta' => 'Brindamos soluciones','descripcion' => "Obtener información relevante e identificar los elementos críticos de las situaciones, sus implicaciones y detalles relevantes para elegir acciones apropiadas, propones soluciones y hace que las cosas pasen"],
            ['pregunta' => 'Nos transformamos', 'descripcion' => "Capacidad de anticiparse y aprovechar las oportunidades de cambio y realizar transformaciones exitosas en la organización."],
            ['pregunta' => 'Servimos con pasión', 'descripcion' => "Pasión por brindar un excelente trato y una experiencia agradable y memorable tanto a clientes internos como externos. Representa servir a las personas, generando conexión, empatía y un impacto positivo en sus clientes."],
            ['pregunta' => 'Excelencia en Resultados', 'descripcion' => "Capacidad de promover acciones específicas y de alto valor para la consecución de los mejores resultados para si mismo, su equipo de trabajo, organización y clientes, por medio de la innovación, medición y retroalimentación de  los logros obtenidos."],
            ['pregunta' => 'Autocuidado', 'descripcion' => "Capacidad para elegir libremente una forma segura de trabajar, reconoce los Factores de Riesgo que pueden afectar su salud y de las personas a su alrededor y que pueden influir en el desempeño y/o producir accidentes de trabajo o enfermedades profesionales."],
        ];


        return $this->render('parametrizador', [
            'modalPreguntas' => $modalPreguntas,
            'modalRespuestas' => $modalRespuestas,
            'array_preguntas' => $array_preguntas
        ]);
    }   

}
?>