<?php

namespace app\controllers;

use Yii;
use yii\base\Exception;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\filters\VerbFilter;
use yii\widgets\ActiveForm;
use yii\filters\AccessControl;
use yii\db\Query;
use yii\db\mssql\PDO;
use yii\web\UploadedFile;
use yii\web\Controller;
use yii\helpers\Url;
use app\models\UploadForm2;
use GuzzleHttp;
use app\models\HvInfopersonal;
use app\models\Hobbies;
use PHPExcel;
use PHPExcel_IOFactory;
use PHPExcel_Reader_DefaultReadFilter;
use PHPExcel_Shared_Date;


class QrController extends Controller {

    
    public function actionIndex(){
       $casos = Yii::$app->db->createCommand('SELECT c.numero_caso as id, a.nombre as area, s.tipo_de_dato, t.tipologia , comentario, cli.clientes, c.nombre, c.documento, c.correo, es.estado, c.fecha_creacion  FROM qr_casos c INNER JOIN  qr_tipos_de_solicitud s ON c.id_solicitud = s.id INNER JOIN  qr_tipologias t ON t.id = c.id_tipologia INNER JOIN qr_areas a ON a.id = t.id_areas INNER JOIN  qr_clientes cli ON cli.id = c.id_cliente INNER JOIN  qr_estados_casos es ON es.id = c.id_estado_caso')->queryAll();

       return $this->render('index',[
           'casos'=>$casos
       ]);
   }

}