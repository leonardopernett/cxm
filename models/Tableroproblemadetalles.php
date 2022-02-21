<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "tbl_tableroproblemadetalles".
 *
 * @property integer $id
 * @property string $name
 * @property integer $tableroproblema_id
 * @property integer $tableroenfoque_id
 *
 * @property TblEjecuciontableroexperiencias[] $tblEjecuciontableroexperiencias
 * @property TblTableroenfoques $tableroenfoque
 * @property TblTableroproblemas $tableroproblema
 * @property TblTmptableroexperiencias[] $tblTmptableroexperiencias
 */
class Tableroproblemadetalles extends \yii\db\ActiveRecord {

    public $problemaName;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tableroproblemadetalles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['name', 'tableroproblema_id', 'tableroenfoque_id'], 'required'],
            [['tableroproblema_id', 'tableroenfoque_id'], 'integer'],
            [['name'], 'string', 'max' => 100],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'name' => Yii::t('app', 'Name'),
            'tableroproblema_id' => Yii::t('app', 'Tableroproblema ID'),
            'problemaName' => Yii::t('app', 'Tableroproblema ID'),
            'tableroenfoque_id' => Yii::t('app', 'Tableroenfoque ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecuciontableroexperiencias() {
        return $this->hasMany(Ejecuciontableroexperiencias::className(), ['tableroproblemadetalle_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTableroenfoque() {
        return $this->hasOne(Tableroenfoques::className(), ['id' => 'tableroenfoque_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTableroproblema() {
        return $this->hasOne(Tableroproblemas::className(), ['id' => 'tableroproblema_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmptableroexperiencias() {
        return $this->hasMany(Tmptableroexperiencias::className(), ['tableroproblemadetalle_id' => 'id']);
    }

    /**
     * Retorna el listado de enroques
     * 
     * @return Array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getEnfoqueList() {
        return ArrayHelper::map(Tableroenfoques::find()->orderBy('name')->asArray()->all(), 'id', 'name');
    }

    /**
     * Retorna el detalle de tablero problemas
     * 
     * @param int $tableroenfoque_id
     * @return Array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getAllProblemsDetByEnfoqueID($tableroenfoque_id) {
        return Tableroproblemadetalles::find()
                        ->select("id, name")
                        ->where("tableroenfoque_id = " . $tableroenfoque_id)
                        ->orderBy('name ASC')
                        ->asArray()
                        ->all();
    }

}
