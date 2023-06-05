<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tracksale_parametrizarformulario".
 *
 * @property integer $id_parametrizarformulario
 * @property integer $arbol_id
 * @property string $trackservicio
 * @property string $Comentarios
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class TracksaleParametrizarformulario extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tracksale_parametrizarformulario';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arbol_id', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['trackservicio'], 'string', 'max' => 50],
            [['Comentarios'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_parametrizarformulario' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'trackservicio' => Yii::t('app', ''),
            'Comentarios' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}