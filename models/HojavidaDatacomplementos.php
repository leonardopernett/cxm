<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_datacomplementos".
 *
 * @property integer $hv_idcomplemento
 * @property integer $hv_idpersonal
 * @property integer $hv_idcivil
 * @property integer $cantidadhijos
 * @property string $NombreHijos
 * @property integer $iddominancia
 * @property integer $idestilosocial
 * @property integer $idgustos
 * @property integer $idhobbies
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaDatacomplementos extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_datacomplementos';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hv_idpersonal', 'hv_idcivil', 'cantidadhijos', 'iddominancia', 'idestilosocial', 'idgustos', 'idhobbies', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['NombreHijos'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idcomplemento' => Yii::t('app', ''),
            'hv_idpersonal' => Yii::t('app', ''),
            'hv_idcivil' => Yii::t('app', ''),
            'cantidadhijos' => Yii::t('app', ''),
            'NombreHijos' => Yii::t('app', ''),
            'iddominancia' => Yii::t('app', ''),
            'idestilosocial' => Yii::t('app', ''),
            'idgustos' => Yii::t('app', ''),
            'idhobbies' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}