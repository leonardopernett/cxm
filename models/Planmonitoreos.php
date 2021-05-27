<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_planmonitoreos".
 *
 * @property integer $id
 * @property string $created
 * @property integer $usua_id
 * @property integer $arbol_id
 * @property integer $dimension_id
 * @property integer $evaluado_id
 * @property string $dsfuente_encuesta
 *
 * @property TblArbols $arbol
 * @property TblDimensions $dimension
 * @property TblEvaluados $evaluado
 * @property TblUsuarios $usua
 */
class Planmonitoreos extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_planmonitoreos';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['created', 'usua_id', 'arbol_id', 'dimension_id', 'evaluado_id'], 'required'],
            [['created'], 'safe'],
            [['usua_id', 'arbol_id', 'dimension_id', 'evaluado_id'], 'integer'],
            [['dsfuente_encuesta'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => 'ID',
            'created' => 'Created',
            'usua_id' => 'Usua ID',
            'arbol_id' => 'Arbol ID',
            'dimension_id' => 'Dimension ID',
            'evaluado_id' => 'Evaluado ID',
            'dsfuente_encuesta' => 'Dsfuente Encuesta',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbol() {
        return $this->hasOne(Arbols::className(), ['id' => 'arbol_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDimension() {
        return $this->hasOne(Dimensions::className(), ['id' => 'dimension_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvaluado() {
        return $this->hasOne(Evaluados::className(), ['id' => 'evaluado_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id']);
    }

}