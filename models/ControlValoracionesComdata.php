<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_control_valoraciones_comdata".
 *
 * @property int $id
 * @property int $arbol_id
 * @property int|null $anulado
 * @property string|null $fecha_creacion
 * @property int|null $usua_id
 */
class ControlValoracionesComdata extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'tbl_control_valoraciones_comdata';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['arbol_id'], 'required'],
            [['arbol_id', 'anulado', 'usua_id'], 'integer'],
            [['fecha_creacion'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'anulado' => Yii::t('app', 'Anulado'),
            'fecha_creacion' => Yii::t('app', 'Fecha Creacion'),
            'usua_id' => Yii::t('app', 'Usua ID'),
        ];
    }
}