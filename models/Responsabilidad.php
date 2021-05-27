<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_responsabilidad".
 *
 * @property integer $id
 * @property integer $arbol_id
 * @property string $nombre
 * @property string $tipo
 *
 * @property TblArbols $arbol
 */
class Responsabilidad extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_responsabilidad';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['arbol_id', 'nombre', 'tipo'], 'required'],
            [['arbol_id'], 'integer'],
            [['tipo'], 'string'],
            [['nombre'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'nombre' => Yii::t('app', 'Nombre'),
            'tipo' => Yii::t('app', 'Tipo'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbol() {
        return $this->hasOne(TblArbols::className(), ['id' => 'arbol_id']);
    }

}
