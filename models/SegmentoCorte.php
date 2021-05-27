<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_segmento_corte".
 *
 * @property integer $segmento_corte_id
 * @property integer $corte_id
 * @property string $segmento_nombre
 * @property string $segmento_fecha_inicio
 * @property string $segmento_fecha_fin
 *
 * @property TblCorteFecha $corte
 */
class SegmentoCorte extends \yii\db\ActiveRecord {
    
    //VARIABLES para el manejo de datos desde la vista viewCortes
    public $semana1;
    public $semana2;
    public $semana3;
    public $semana4;
    public $semana5;
    public $mes;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_segmento_corte';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['corte_id'], 'integer'],
            [['segmento_fecha_inicio', 'semana1', 'semana2', 'semana3', 'semana4', 'semana5', 'mes', 'segmento_fecha_fin'], 'safe'],
            [['segmento_nombre'], 'string', 'max' => 250],
            [['semana1', 'semana2', 'semana3', 'semana4'], 'required', 'on' => 'corteSemana'],
            [['mes'], 'required', 'on' => 'corteMes']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'segmento_corte_id' => Yii::t('app', 'Segmento Corte ID'),
            'corte_id' => Yii::t('app', 'Corte ID'),
            'segmento_nombre' => Yii::t('app', 'Segmento Nombre'),
            'segmento_fecha_inicio' => Yii::t('app', 'Segmento Fecha Inicio'),
            'segmento_fecha_fin' => Yii::t('app', 'Segmento Fecha Fin'),
            'semana1' => Yii::t('app', 'Semana 1'),
            'semana2' => Yii::t('app', 'Semana 2'),
            'semana3' => Yii::t('app', 'Semana 3'),
            'semana4' => Yii::t('app', 'Semana 4'),
            'semana5' => Yii::t('app', 'Semana 5'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCorte() {
        return $this->hasOne(TblCorteFecha::className(), ['corte_id' => 'corte_id']);
    }

}
