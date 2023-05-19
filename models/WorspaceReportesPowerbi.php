<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_worspace_reportes_powerbi".
 *
 * @property integer $id_pbi
 * @property string $id_workspace
 * @property string $nombre_workspace
 * @property string $id_reporte
 * @property string $nombre_reporte
 * @property string $roles
 * @property string $estatus
 */
class WorspaceReportesPowerbi extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_worspace_reportes_powerbi';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_workspace', 'id_reporte'], 'string', 'max' => 80],
            [['nombre_workspace', 'nombre_reporte', 'roles'], 'string', 'max' => 150],
            [['estatus'], 'string', 'max' => 50]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_pbi' => Yii::t('app', ''),
            'id_workspace' => Yii::t('app', ''),
            'nombre_workspace' => Yii::t('app', ''),
            'id_reporte' => Yii::t('app', ''),
            'nombre_reporte' => Yii::t('app', ''),
            'roles' => Yii::t('app', ''),
            'estatus' => Yii::t('app', ''),
        ];
    }
}