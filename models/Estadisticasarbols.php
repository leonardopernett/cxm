<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_estadisticasarbols".
 *
 * @property integer $id
 * @property integer $nmanio
 * @property integer $nmmes
 * @property integer $arbol_id
 * @property integer $dimension_id
 * @property double $nmsumatoria
 * @property double $i1_nmsumatoria
 * @property double $i2_nmsumatoria
 * @property double $i3_nmsumatoria
 * @property double $i4_nmsumatoria
 * @property double $i5_nmsumatoria
 * @property double $i6_nmsumatoria
 * @property double $i7_nmsumatoria
 * @property double $i8_nmsumatoria
 * @property double $i9_nmsumatoria
 * @property double $i10_nmsumatoria
 * @property integer $nmnumero
 *
 * @property TblArbols $arbol
 * @property TblDimensions $dimension
 * @property TblEstadisticasseccions[] $tblEstadisticasseccions
 */
class Estadisticasarbols extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_estadisticasarbols';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['nmanio', 'nmmes', 'arbol_id'], 'required'],
            [['nmanio', 'nmmes', 'arbol_id', 'dimension_id', 'nmnumero'], 'integer'],
            [['nmsumatoria', 'i1_nmsumatoria', 'i2_nmsumatoria', 'i3_nmsumatoria', 'i4_nmsumatoria', 'i5_nmsumatoria', 'i6_nmsumatoria', 'i7_nmsumatoria', 'i8_nmsumatoria', 'i9_nmsumatoria', 'i10_nmsumatoria'], 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'nmanio' => Yii::t('app', 'Nmanio'),
            'nmmes' => Yii::t('app', 'Nmmes'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'dimension_id' => Yii::t('app', 'Dimension ID'),
            'nmsumatoria' => Yii::t('app', 'Nmsumatoria'),
            'i1_nmsumatoria' => Yii::t('app', 'I1 Nmsumatoria'),
            'i2_nmsumatoria' => Yii::t('app', 'I2 Nmsumatoria'),
            'i3_nmsumatoria' => Yii::t('app', 'I3 Nmsumatoria'),
            'i4_nmsumatoria' => Yii::t('app', 'I4 Nmsumatoria'),
            'i5_nmsumatoria' => Yii::t('app', 'I5 Nmsumatoria'),
            'i6_nmsumatoria' => Yii::t('app', 'I6 Nmsumatoria'),
            'i7_nmsumatoria' => Yii::t('app', 'I7 Nmsumatoria'),
            'i8_nmsumatoria' => Yii::t('app', 'I8 Nmsumatoria'),
            'i9_nmsumatoria' => Yii::t('app', 'I9 Nmsumatoria'),
            'i10_nmsumatoria' => Yii::t('app', 'I10 Nmsumatoria'),
            'nmnumero' => Yii::t('app', 'Nmnumero'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbol() {
        return $this->hasOne(Arbols::className(), ['id' => 'arbol_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getDimension() {
        return $this->hasOne(Dimensions::className(), ['id' => 'dimension_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblEstadisticasseccions() {
        return $this->hasMany(Estadisticasseccions::className(), ['estadisticasarbol_id' => 'id']);
    }
    
    /**
     * Metodo para las graficas del Dashboard
     * 
     * @param int $arbol_id Id del arbol
     * @param int $dimension_id Id de la dimension
     * @param string $mes Mes
     * @param string $ano Año
     * @return array 
     * 
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getAll_ByMonthAndDimension($arbol_id, $dimension_id, $mes, $ano, $metrica) {
        $baseConsulta = '';
        $metrica = (int)$metrica;
        switch ($metrica) {
            case 1:
                $baseConsulta = 'i1_nmsumatoria';
                break;
            case 2:
                $baseConsulta = 'i2_nmsumatoria';
                break;
            case 3:
                $baseConsulta = 'i3_nmsumatoria';
                break;
            case 4:
                $baseConsulta = 'i4_nmsumatoria';
                break;
            case 5:
                $baseConsulta = 'i5_nmsumatoria';
                break;
            case 6:
                $baseConsulta = 'i6_nmsumatoria';
                break;
            case 7:
                $baseConsulta = 'i7_nmsumatoria';
                break;
            case 8:
                $baseConsulta = 'i8_nmsumatoria';
                break;
            case 9:
                $baseConsulta = 'i9_nmsumatoria';
                break;
            case 10:
                $baseConsulta = 'i10_nmsumatoria';
                break;
            case 11:
                $baseConsulta = 'nmsumatoria';
                break;
        }
        return Estadisticasarbols::find()
                        ->select("`id`, `".$baseConsulta."`/`nmnumero` `promedio`, arbol_id, nmmes")
                        ->where(["nmanio" => $ano, "nmmes" => $mes, "arbol_id" => $arbol_id, "dimension_id" => $dimension_id])
                        ->asArray()
                        ->all();
    }
    
    public static function getAll_Bydimension($arbol_id, $dimension_id, $mes, $ano){
        return Estadisticasarbols::find()
                        ->select("`id`, `nmnumero` `cantidad`, arbol_id, nmmes")
                        ->where(["nmanio" => $ano, "nmmes" => $mes, "arbol_id" => $arbol_id, "dimension_id" => $dimension_id])
                        ->asArray()
                        ->all();
    }

}
