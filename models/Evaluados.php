<?php

namespace app\models;

use Yii;
use yii\helpers\Url;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_evaluados".
 *
 * @property integer $id
 * @property string $name
 * @property string $telefono
 * @property string $dsusuario_red
 * @property string $cdestatus
 * @property string $identificacion
 * @property string $email
 * @property integer $usua_id
 * @property string $fechacreacion
 * @property int|null $es_operativo
 * @property int|null $excepcion_jarvis
 *
 * @property TblEjecucionformularios[] $tblEjecucionformularios
 * @property TblEquiposEvaluados[] $tblEquiposEvaluados
 * @property TblEstadisticaevaluados[] $tblEstadisticaevaluados
 * @property TblPlanmonitoreos[] $tblPlanmonitoreos
 */
class Evaluados extends \yii\db\ActiveRecord {

    public $evaluado_id;
    public $equId;
    public $eqName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_evaluados';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['usua_activo'], 'string'],
            [['identificacion', 'name', 'dsusuario_red'], 'required'],
            [['email'], 'email'],
            [['usua_id', 'es_operativo', 'excepcion_jarvis'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['evaluado_id'], 'required', 'on' => 'monitoreo'],
            [['name', 'email'], 'string', 'max' => 150],
            [['telefono'], 'string', 'max' => 15],
            [['dsusuario_red'], 'string', 'max' => 50],
            [['cdestatus'], 'string', 'max' => 1],
            [['identificacion'], 'string', 'max' => 30],
            [['identificacion'], 'match', 'not' => true, 'pattern' => '/[^0-9]/'],
            [['name','telefono','dsusuario_red','identificacion'],'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             }],
             [['email'], 'filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_EMAIL) ;
             }]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'telefono' => Yii::t('app', 'Telefono'),
            'dsusuario_red' => Yii::t('app', 'Dsusuario Red'),
            'cdestatus' => Yii::t('app', 'Cdestatus'),
            'identificacion' => Yii::t('app', 'Identificacion'),
            'email' => Yii::t('app', 'Email'),
            'evaluado_id' => Yii::t('app', 'Evaluado ID'),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'es_operativo' => Yii::t('app', ''),
            'usua_activo' => Yii::t('app', ''), 
            'excepcion_jarvis' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionformularios() {
        return $this->hasMany(Ejecucionformularios::className(), ['evaluado_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquiposevaluados() {
        return $this->hasMany(EquiposEvaluados::className(), ['evaluado_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEstadisticaevaluados() {
        return $this->hasMany(Estadisticaevaluados::className(), ['evaluado_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanmonitoreos() {
        return $this->hasMany(Planmonitoreos::className(), ['evaluado_id' => 'id']);
    }

    /**
     * Metodo que retorna el listado de todos los evaluados
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getEvaluadosList($search) {
        return Evaluados::find()
                        ->select(['id' => 'tbl_evaluados.id', 'text' => 'UPPER(name)'])
                        ->where('name LIKE "%' . $search . '%"')
                        ->orderBy('name')
                        ->asArray()
                        ->all();
    }

    /**
     * 23/02/2016 -> Funcion que permite llevar un log o registro de los datos modificados
     * @param type $insert
     * @return boolean
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert == false) {
                $modelLog = new Logeventsadmin();
                $modelLog->datos_nuevos = print_r($this->attributes, true);
                $modelLog->datos_ant = print_r($this->oldAttributes, true);
                $modelLog->fecha_modificacion = date("Y-m-d H:i:s");
                $modelLog->usuario_modificacion = Yii::$app->user->identity->username;
                $modelLog->id_usuario_modificacion = Yii::$app->user->identity->id;
                $modelLog->tabla_modificada = $this->tableName();
                $modelLog->save();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Funcion que genera el excel con los datos seleccionados en los filtros que tiene la vista
     *  index de de evaluados
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $models
     * @return boolean
     */
    public function generarReporteEvaluados($models = null) {
        set_time_limit(0);
        $objPHPexcel = new \PHPExcel();
        $objPHPexcel->setActiveSheetIndex(0);
        $titulos = [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'dsusuario_red' => Yii::t('app', 'Dsusuario Red'),
            'identificacion' => Yii::t('app', 'Identificacion'),
            'email' => Yii::t('app', 'Email'),
            'equId' => Yii::t('app', 'Equipo ID'),
            'eqName' => Yii::t('app', 'Nombre Equipo'),
        ];
        $column = 'A';
        $row = 2;
        try {
            foreach ($titulos as $titulo) {
                $objPHPexcel->getActiveSheet()->setCellValue($column . '1', $titulo);
                $column++;
            }
            
            for ($index = 0; $index < count($models); $index++) {
                $column = 'A';
                $model = $models[$index];
                unset($model['id']);
                unset($model['name']);
                unset($model['telefono']);
                unset($model['cdestatus']);
                unset($model['evaluado_id']);
                unset($model['nmumbral_verde']);
                unset($model['nmumbral_amarillo']);
                unset($model['usua_id']);
                unset($model['es_operativo']);     
                unset($model['usua_activo']);  
                unset($model['excepcion_jarvis']);        
                foreach ($model as $value) {
                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value);
                    $column++;
                }
                $row++;
            }
            $self = Url::to(['evaluados/index']);
            header("refresh:1; url='$self'");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Reporte_Evaluados.xlsx"');
            header('Cache-Control: max-age=1');

            $objWriter = new \PHPExcel_Writer_Excel2007();
            $objWriter->setPHPExcel($objPHPexcel);
            $objWriter->save('php://output');
            Yii::$app->session->setFlash('success', Yii::t('app', 'Reporte generado exitosamente'));
            return true;
        } catch (Exception $exc) {
            return false;
        }
        return false;
    }

    //Automatizacion equipos Teo
    public static function getEvaluadoByNetUser($search) {
        $sql = "SELECT * FROM tbl_evaluados WHERE identificacion = :search";
        $command = \Yii::$app->db->createCommand($sql);
        $command->bindParam(":search", $search);
        $users                           = $command->queryAll();
        if ($users) {
            return $users;
        }
    }

}
