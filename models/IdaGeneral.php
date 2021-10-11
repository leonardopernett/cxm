<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_ida_general".
 *
 * @property integer $idcenttog
 * @property string $usuariored
 * @property string $tipoproceso
 * @property string $fechainicio
 * @property string $fechafin
 * @property string $servicio
 * @property double $vinsatu
 * @property double $vsolucion
 * @property double $vvalores
 * @property double $vfacilidad
 * @property double $vhabilidad
 * @property double $totalentto
 * @property double $totalojt
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class IdaGeneral extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_ida_general';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['fechainicio', 'fechafin', 'fechacreacion'], 'safe'],
            [['vinsatu', 'vsolucion', 'vvalores', 'vfacilidad', 'vhabilidad', 'totalentto', 'totalojt'], 'number'],
            [['anulado', 'usua_id'], 'integer'],
            [['usuariored', 'servicio'], 'string', 'max' => 250],
            [['tipoproceso'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcenttog' => Yii::t('app', ''),
            'usuariored' => Yii::t('app', ''),
            'tipoproceso' => Yii::t('app', ''),
            'fechainicio' => Yii::t('app', ''),
            'fechafin' => Yii::t('app', ''),
            'servicio' => Yii::t('app', ''),
            'vinsatu' => Yii::t('app', ''),
            'vsolucion' => Yii::t('app', ''),
            'vvalores' => Yii::t('app', ''),
            'vfacilidad' => Yii::t('app', ''),
            'vhabilidad' => Yii::t('app', ''),
            'totalentto' => Yii::t('app', ''),
            'totalojt' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }
}