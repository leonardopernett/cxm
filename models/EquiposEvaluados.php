<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_equipos_evaluados".
 *
 * @property integer $id
 * @property integer $evaluado_id
 * @property integer $equipo_id
 *
 * @property TblEquipos $equipo
 * @property TblEvaluados $evaluado
 */
class EquiposEvaluados extends \yii\db\ActiveRecord {

    public $evaluadoName;
    public $equipoName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_equipos_evaluados';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['evaluado_id', 'equipo_id'], 'required'],
            //[['evaluado_id', 'equipo_id'], 'integer']
            //[['equipo_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'evaluado_id' => Yii::t('app', 'Evaluado ID'),
            'equipo_id' => Yii::t('app', 'Equipo'),
            'evaluadoName' => Yii::t('app', 'Evaluado'),
            'equipoName' => Yii::t('app', 'Equipo'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipo() {
        return $this->hasOne(Equipos::className(), ['id' => 'equipo_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluado() {
        return $this->hasOne(Evaluados::className(), ['id' => 'evaluado_id']);
    }

    /**
     * Metodo que retorna el listado de los responsables
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getEvaluadoList() {
        return ArrayHelper::map(Evaluados::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    //Automatizacion equipos Teo
    public function deleteEquipo($idEquipo = 0){
        $model = \Yii::$app->db->createCommand('DELETE FROM tbl_equipos_evaluados WHERE equipo_id =:idEquipo');
        $model->bindParam(':idEquipo', $idEquipo);
        $model->execute();
    }

    //Automatizacion equipos Teo
    public function deleteEvaluado($idEvaluado = 0, $idEquipo){
        $model = \Yii::$app->db->createCommand('DELETE FROM tbl_equipos_evaluados WHERE evaluado_id =:idEvaluado and equipo_id = :idEquipo');
        $model->bindParam(':idEvaluado', $idEvaluado);
        $model->bindParam(':idEquipo', $idEquipo);
        $model->execute();
    }

    //Automatizacion equipos Teo
    public function cleanEvaluado($idEvaluado = 0){
        $model = \Yii::$app->db->createCommand('DELETE FROM tbl_equipos_evaluados WHERE evaluado_id =:idEvaluado');
        $model->bindParam(':idEvaluado', $idEvaluado);
        $model->execute();
    }

    //Automatizacion equipos Teo
    public static function getEvaluadosEquipo($idequipo = 0 ) {
        $sql = "SELECT REPLACE(ev.dsusuario_red, 'ñ', 'n') as dsusuario_red, ee.evaluado_id, ev.identificacion as identificacion FROM tbl_equipos_evaluados ee INNER JOIN tbl_evaluados ev on ev.id = ee.evaluado_id WHERE ee.equipo_id = :idequipo";
        $command = \Yii::$app->db->createCommand($sql);
        $command->bindParam(":idequipo", $idequipo);
        $users                           = $command->queryAll();
        if ($users) 
        {
            return $users;
        }
        return null;
    }


}
