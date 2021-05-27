<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_corte_fecha".
 *
 * @property integer $corte_id
 * @property integer $tipo_corte
 * @property integer $band_repetir
 * @property integer $usua_id
 * @property string $corte_descripcion
 * @property TblUsuarios $usua
 * @property TblSegmentoCorte[] $tblSegmentoCortes
 */
class CorteFecha extends \yii\db\ActiveRecord {

    public $fecha;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_corte_fecha';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tipo_corte', 'band_repetir', 'usua_id'], 'integer'],
            [['corte_descripcion','fecha'], 'string', 'max' => 250],
            [['fecha'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'corte_id' => Yii::t('app', 'Corte ID'),
            'tipo_corte' => Yii::t('app', 'Tipo Corte'),
            'band_repetir' => Yii::t('app', 'Band Repetir'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'corte_descripcion'=>  \Yii::t('app', 'Mes/Semana'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua() {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblSegmentoCortes() {
        return $this->hasMany(TblSegmentoCorte::className(), ['corte_id' => 'corte_id']);
    }

    public function search($params,$tipo) {
        $query = CorteFecha::find();
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        $query->andFilterWhere([
            'corte_id' => $this->corte_id,
            'tipo_corte' => $tipo,
            'band_repetir' => $this->band_repetir,
            'usua_id' => Yii::$app->user->identity->id,
        ]);
        return $dataProvider;
    }

}
