<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_genesys_novedades".
 *
 * @property integer $id_novedades
 * @property string $id_genesys
 * @property string $nombre_asesor
 * @property string $sociedad
 * @property string $title
 * @property string $selfUri
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class GenesysNovedades extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_genesys_novedades';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['id_genesys', 'selfUri'], 'string', 'max' => 200],
            [['nombre_asesor', 'title'], 'string', 'max' => 100],
            [['sociedad'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_novedades' => Yii::t('app', ''),
            'id_genesys' => Yii::t('app', ''),
            'nombre_asesor' => Yii::t('app', ''),
            'sociedad' => Yii::t('app', ''),
            'title' => Yii::t('app', ''),
            'selfUri' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}
