<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_control_desvincular".
 *
 * @property integer $iddesvincular
 * @property integer $solicitante_id
 * @property integer $evaluados_id
 * @property integer $responsable
 * @property string $motivo
 * @property string $correo
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ControlDesvincular extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_desvincular';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['solicitante_id', 'evaluados_id', 'responsable', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['motivo','correo'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddesvincular' => Yii::t('app', ''),
            'solicitante_id' => Yii::t('app', ''),
            'evaluados_id' => Yii::t('app', ''),
            'responsable' => Yii::t('app', ''),
            'motivo' => Yii::t('app', 'Motivo'),
            'correo' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }

    public function getUsuarios() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'evaluados_id']);
    }

    public function getObtenerName($opcion){
        $txtIdDesvincular = $opcion;

        $varName =  Yii::$app->db->createCommand("select solicitante_id from tbl_control_desvincular where iddesvincular = $txtIdDesvincular")->queryScalar(); 

        $data = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varName")->queryScalar(); 

        return $data;
    }

    public function getObtenerName2($opcion){
        $txtIdDesvincular = $opcion;

        $varName =  Yii::$app->db->createCommand("select responsable from tbl_control_desvincular where iddesvincular = $txtIdDesvincular")->queryScalar(); 

        $data = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $varName")->queryScalar(); 

        return $data;
    }    
}