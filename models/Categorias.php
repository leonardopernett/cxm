<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_categorias".
 *
 * @property integer $id
 * @property string $nombre
 *
 * @property TblTmpreporteSatisfaccion[] $tblTmpreporteSatisfaccions
 */
class Categorias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_categorias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre'], 'required'],
            [['nombre'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nombre' => Yii::t('app', 'Nombre'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblTmpreporteSatisfaccions()
    {
        return $this->hasMany(TmpreporteSatisfaccion::className(), ['categoria_id' => 'id']);
    }
}
