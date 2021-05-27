<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;
use yii\data\SqlDataProvider;

/**
 * This is the model class for table "tbl_ejecucionbloquedetalles".
 *
 * @property integer $id
 * @property integer $ejecucionbloque_id
 * @property integer $bloquedetalle_id
 * @property integer $calificaciondetalle_id
 * @property string $name
 * @property double $i1_nmcalculo
 * @property double $i2_nmcalculo
 * @property double $i3_nmcalculo
 * @property double $i4_nmcalculo
 * @property double $i5_nmcalculo
 * @property double $i6_nmcalculo
 * @property double $i7_nmcalculo
 * @property double $i8_nmcalculo
 * @property double $i9_nmcalculo
 * @property double $i10_nmcalculo
 * @property double $i1_nmfactor
 * @property double $i2_nmfactor
 * @property double $i3_nmfactor
 * @property double $i4_nmfactor
 * @property double $i5_nmfactor
 * @property double $i6_nmfactor
 * @property double $i7_nmfactor
 * @property double $i8_nmfactor
 * @property double $i9_nmfactor
 * @property double $i10_nmfactor
 *
 * @property TblBloquedetalles $bloquedetalle
 * @property TblCalificaciondetalles $calificaciondetalle
 * @property TblEjecucionbloques $ejecucionbloque
 * @property TblEjecucionbloquedetallesTipificaciones[] $tblEjecucionbloquedetallesTipificaciones
 */
class Ejecucionbloquedetalles extends \yii\db\ActiveRecord {

    public $created;
    public $arbol_id;
    public $startDate;
    public $endDate;
    public $dimension;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_ejecucionbloquedetalles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ejecucionbloque_id', 'bloquedetalle_id', 'calificaciondetalle_id'], 'integer'],
            [['i1_nmcalculo', 'i2_nmcalculo', 'i3_nmcalculo', 'i4_nmcalculo', 'i5_nmcalculo', 'i6_nmcalculo', 'i7_nmcalculo', 'i8_nmcalculo', 'i9_nmcalculo', 'i10_nmcalculo', 'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor','c_pits'], 'number'],
            [['name'], 'string', 'max' => 150],
            [['created', 'arbol_id', 'dimension'], 'safe'],
            [['created'], 'required']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'arbol_id' => Yii::t('app', 'Arbol'),
            'created' => Yii::t('app', 'Fecha de Valoración'),
            'ejecucionbloque_id' => Yii::t('app', 'Ejecucionbloque ID'),
            'bloquedetalle_id' => Yii::t('app', 'Bloquedetalle ID'),
            'calificaciondetalle_id' => Yii::t('app', 'Calificaciondetalle ID'),
            'name' => Yii::t('app', 'Nombre'),
            'i1_nmcalculo' => Yii::t('app', 'I1 Nmcalculo'),
            'i2_nmcalculo' => Yii::t('app', 'I2 Nmcalculo'),
            'i3_nmcalculo' => Yii::t('app', 'I3 Nmcalculo'),
            'i4_nmcalculo' => Yii::t('app', 'I4 Nmcalculo'),
            'i5_nmcalculo' => Yii::t('app', 'I5 Nmcalculo'),
            'i6_nmcalculo' => Yii::t('app', 'I6 Nmcalculo'),
            'i7_nmcalculo' => Yii::t('app', 'I7 Nmcalculo'),
            'i8_nmcalculo' => Yii::t('app', 'I8 Nmcalculo'),
            'i9_nmcalculo' => Yii::t('app', 'I9 Nmcalculo'),
            'i10_nmcalculo' => Yii::t('app', 'I10 Nmcalculo'),
            'i1_nmfactor' => Yii::t('app', 'I1 Nmfactor'),
            'i2_nmfactor' => Yii::t('app', 'I2 Nmfactor'),
            'i3_nmfactor' => Yii::t('app', 'I3 Nmfactor'),
            'i4_nmfactor' => Yii::t('app', 'I4 Nmfactor'),
            'i5_nmfactor' => Yii::t('app', 'I5 Nmfactor'),
            'i6_nmfactor' => Yii::t('app', 'I6 Nmfactor'),
            'i7_nmfactor' => Yii::t('app', 'I7 Nmfactor'),
            'i8_nmfactor' => Yii::t('app', 'I8 Nmfactor'),
            'i9_nmfactor' => Yii::t('app', 'I9 Nmfactor'),
            'i10_nmfactor' => Yii::t('app', 'I10 Nmfactor'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getBloquedetalle() {
        return $this->hasOne(TblBloquedetalles::className(), ['id' => 'bloquedetalle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCalificaciondetalle() {
        return $this->hasOne(TblCalificaciondetalles::className(), ['id' => 'calificaciondetalle_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionbloque() {
        return $this->hasOne(TblEjecucionbloques::className(), ['id' => 'ejecucionbloque_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTblEjecucionbloquedetallesTipificaciones() {
        return $this->hasMany(TblEjecucionbloquedetallesTipificaciones::className(), ['ejecucionbloquedetalle_id' => 'id']);
    }

    /**
     * 
     * @return array
     * @author Felipe echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getArbolesByRoles() {

        $rol = Yii::$app->user->identity->rolId;

        return ArrayHelper::map(Arboles::find()
                                ->joinWith('permisosGruposArbols')
                                ->where([
                                    "sncrear_formulario" => 1,
                                    "snhoja" => 1,
                                    "role_id" => $rol,
                                    "snver_grafica" => 1])
                                ->andWhere(['not', ['formulario_id' => null]])
                                ->orderBy("dsorden ASC")
                                ->all(), 'id', 'dsname_full');
    }

    /**
     * Metodo que retorna la consulta para mostrar el reporte de prom de Calificaciones
     * @return array
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function reporteCalificaciones($arbol, $feini, $fefin, $bandera, $dimension) {
        try {


            if ($arbol != "") {
                $arbol_where = " AND ef.`arbol_id` IN ($arbol) ";
            } else {
                $arbol_where = "";
            }
            if ($dimension != "") {
                $dimension_where = " AND ef.`dimension_id` IN ($dimension) ";
            } else {
                $dimension_where = "";
            }
            $sql = "SELECT  ta.name AS arbol, td.name AS tdimension, ebd.`bloquedetalle_id` as id, max(ebd.`name`) as pregunta,
            ROUND( AVG( ebd.`i1_nmcalculo` )*100,2 ) as i1,
            ROUND( AVG( ebd.`i2_nmcalculo` )*100,2 ) as i2,
            ROUND( AVG( ebd.`i3_nmcalculo` )*100,2 ) as i3,
            ROUND( AVG( ebd.`i4_nmcalculo` )*100,2 ) as i4,
            ROUND( AVG( ebd.`i5_nmcalculo` )*100,2 ) as i5,
            ROUND( AVG( ebd.`i6_nmcalculo` )*100,2 ) as i6,
            ROUND( AVG( ebd.`i7_nmcalculo` )*100,2 ) as i7,
            ROUND( AVG( ebd.`i8_nmcalculo` )*100,2 ) as i8,
            ROUND( AVG( ebd.`i9_nmcalculo` )*100,2 ) as i9,
            ROUND( AVG( ebd.`i10_nmcalculo` )*100,2 ) as i10,
            COUNT(ebd.`name`) as registros,
            s.nmorden as orden
            FROM `tbl_ejecucionformularios` ef, `tbl_ejecucionseccions` es,`tbl_ejecucionbloques` eb , `tbl_ejecucionbloquedetalles` ebd,
            tbl_arbols ta, tbl_dimensions td, tbl_seccions s
            WHERE ef.`id` = es.`ejecucionformulario_id` AND 
            es.`id` = eb.`ejecucionseccion_id` AND 
            eb.`id` = ebd.`ejecucionbloque_id` AND 
            ta.id = ef.arbol_id AND
            td.id = ef.dimension_id AND
            s.id = es.seccion_id AND
            ef.`created`<'$fefin' AND 
            ef.`created`>'$feini'";
            if ($arbol_where != "") {
                $sql.=$arbol_where;
            }
            if ($dimension_where != "") {
                $sql.=$dimension_where;
            }
            $sql.=" GROUP BY 1,2,3 ORDER BY arbol,tdimension,orden,id ASC";
            $count = Yii::$app->db->createCommand($sql)->queryAll();

            $dataProvider = new SqlDataProvider([
                'sql' => $sql,
                'totalCount' => count($count),
                'pagination' => ['pageSize' => ($bandera) ? 20 : count($count)],
            ]);




            return $dataProvider;
        } catch (Exception $e) {
            \Yii::error($e->getMessage(), 'exception');
        }
    }

}
