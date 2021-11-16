<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hojavida_permisosacciones".
 *
 * @property integer $hv_idacciones
 * @property integer $usuario_registro
 * @property integer $hveliminar
 * @property integer $hveditar
 * @property integer $hvcasrgamasiva
 * @property integer $hvdatapersonal
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class HojavidaPermisosacciones extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_hojavida_permisosacciones';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuario_registro', 'hvverresumen', 'hveliminar', 'hveditar', 'hvcasrgamasiva', 'hvverdata', 'hvdatapersonal', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'hv_idacciones' => Yii::t('app', ''),
            'usuario_registro' => Yii::t('app', ''),
            'hveliminar' => Yii::t('app', ''),
            'hveditar' => Yii::t('app', ''),
            'hvcasrgamasiva' => Yii::t('app', ''),
            'hvdatapersonal' => Yii::t('app', ''),
            'hvverresumen' => Yii::t('app', ''),
            'hvverdata' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}