<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_estilosocial".
 *
 * @property integer $idestilosocial
 * @property string $estilosocial
 * @property integer $usua_id
 * @property integer $anulado
 * @property string $fechacreacion
 */
class HvEstilosocial extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hv_estilosocial';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['estilosocial'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idestilosocial' => Yii::t('app', ''),
            'estilosocial' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}