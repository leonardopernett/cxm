<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_basechat_categorias".
 *
 * @property integer $idlista
 * @property string $nombrecategoria
 * @property string $fechacreacion
 * @property integer $anulado
 * @property integer $usua_id
 * @property integer $pcrc
 */
class BasechatCategorias extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_basechat_categorias';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechacreacion'], 'safe'],
            [['anulado', 'usua_id', 'pcrc'], 'integer'],
            [['nombrecategoria'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idlista' => Yii::t('app', ''),
            'nombrecategoria' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'pcrc' => Yii::t('app', ''),
        ];
    }
}