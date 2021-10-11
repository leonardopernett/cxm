<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_procesos_administrador".
 *
 * @property integer $idprocesosadmin
 * @property string $procesos
 * @property integer $usua_id
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ProcesosAdministrador extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_procesos_administrador';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usua_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['procesos'], 'string', 'max' => 250]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idprocesosadmin' => Yii::t('app', ''),
            'procesos' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }


}