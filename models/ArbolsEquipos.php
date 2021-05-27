<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_arbols_equipos".
 *
 * @property integer $id
 * @property integer $arbol_id
 * @property integer $equipo_id
 *
 * @property TblArbols $arbol
 * @property TblEquipos $equipo
 */
class ArbolsEquipos extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_arbols_equipos';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['arbol_id', 'equipo_id'], 'required'],
            [['arbol_id', 'equipo_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'equipo_id' => Yii::t('app', 'Equipo ID'),
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
    public function getEquipo() {
        return $this->hasOne(Equipos::className(), ['id' => 'equipo_id']);
    }

}
