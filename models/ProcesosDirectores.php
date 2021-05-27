<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_procesos_directores".
 *
 * @property integer $iddirectores
 * @property string $director_programa
 * @property string $documento_director
 * @property string $ciudad
 * @property integer $anulado
 * @property string $fechacreacion
 * @property integer $usua_id
 */
class ProcesosDirectores extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_procesos_directores';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['director_programa'], 'string', 'max' => 100],
            [['documento_director'], 'string', 'max' => 20],
            [['ciudad'], 'string', 'max' => 80]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'iddirectores' => Yii::t('app', ''),
            'director_programa' => Yii::t('app', ''),
            'documento_director' => Yii::t('app', ''),
            'ciudad' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
        ];
    }
}