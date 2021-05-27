<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmpejecucionformularios".
 *
 * @property integer $id
 * @property integer $dimension_id
 * @property integer $arbol_id
 * @property integer $usua_id
 * @property integer $evaluado_id
 * @property integer $formulario_id
 * @property string $created
 * @property integer $snavisar
 * @property string $dscomentario
 * @property integer $sneditable
 * @property integer $snaviso_revisado
 * @property integer $usua_id_responsable
 * @property string $dsaccion_correctiva
 * @property string $feaccion_correctiva
 * @property integer $nmescalamiento
 * @property string $feescalamiento
 * @property string $dscausa_raiz
 * @property string $dscompromiso
 * @property string $dsfuente_encuesta
 * @property integer $ejecucionformulario_id
 * @property double $score
 * @property integer $transacion_id
 * @property string $dsruta_arbol
 * @property integer $usua_id_lider
 * @property integer $equipo_id
 * @property integer $usua_id_actual
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
 * @property integer $basesatisfaccion_id
 * 
 * @property TblTmpejecucionbloquedetalles[] $tblTmpejecucionbloquedetalles
 * @property TblTmpejecucionbloques[] $tblTmpejecucionbloques
 * @property TblTmpejecucionfeedbacks[] $tblTmpejecucionfeedbacks
 * @property TblTransacions $transacion
 * @property TblDimensions $dimension
 * @property TblTmpejecucionsecciones[] $tblTmpejecucionsecciones
 * @property TblTmptableroexperiencias[] $tblTmptableroexperiencias
 * @property TblTmptiposllamada[] $tblTmptiposllamadas
 */
class Tmpejecucionformularios extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmpejecucionformularios';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['dimension_id', 'arbol_id', 'usua_id', 'formulario_id'], 'required'],
            [['dimension_id', 'arbol_id', 'usua_id', 'evaluado_id', 'formulario_id', 'snavisar', 'sneditable', 'snaviso_revisado', 'usua_id_responsable', 'nmescalamiento', 'ejecucionformulario_id', 'transacion_id', 'usua_id_lider', 'equipo_id', 'usua_id_actual'], 'integer'],
            [['created', 'feaccion_correctiva', 'feescalamiento'], 'safe'],
            [['dscomentario', 'dsaccion_correctiva', 'dscausa_raiz', 'dscompromiso'], 'string'],
            [['score', 'i1_nmcalculo', 'i2_nmcalculo', 'i3_nmcalculo', 'i4_nmcalculo', 'i5_nmcalculo', 'i6_nmcalculo', 'i7_nmcalculo', 'i8_nmcalculo', 'i9_nmcalculo', 'i10_nmcalculo', 'i1_nmfactor', 'i2_nmfactor', 'i3_nmfactor', 'i4_nmfactor', 'i5_nmfactor', 'i6_nmfactor', 'i7_nmfactor', 'i8_nmfactor', 'i9_nmfactor', 'i10_nmfactor', 'basesatisfaccion_id'], 'number'],
            [['dsfuente_encuesta'], 'string', 'max' => 500],
            [['dsruta_arbol'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'dimension_id' => Yii::t('app', 'Dimension ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'evaluado_id' => Yii::t('app', 'Evaluado ID'),
            'formulario_id' => Yii::t('app', 'Formulario ID'),
            'created' => Yii::t('app', 'Created'),
            'snavisar' => Yii::t('app', 'Snavisar'),
            'dscomentario' => Yii::t('app', 'Dscomentario'),
            'sneditable' => Yii::t('app', 'Sneditable'),
            'snaviso_revisado' => Yii::t('app', 'Snaviso Revisado'),
            'usua_id_responsable' => Yii::t('app', 'Usua Id Responsable'),
            'dsaccion_correctiva' => Yii::t('app', 'Dsaccion Correctiva'),
            'feaccion_correctiva' => Yii::t('app', 'Feaccion Correctiva'),
            'nmescalamiento' => Yii::t('app', 'Nmescalamiento'),
            'feescalamiento' => Yii::t('app', 'Feescalamiento'),
            'dscausa_raiz' => Yii::t('app', 'Dscausa Raiz'),
            'dscompromiso' => Yii::t('app', 'Dscompromiso'),
            'dsfuente_encuesta' => Yii::t('app', 'Dsfuente Encuesta'),
            'ejecucionformulario_id' => Yii::t('app', 'Ejecucionformulario ID'),
            'score' => Yii::t('app', 'Score'),
            'transacion_id' => Yii::t('app', 'Transacion ID'),
            'dsruta_arbol' => Yii::t('app', 'Dsruta Arbol'),
            'usua_id_lider' => Yii::t('app', 'Usua Id Lider'),
            'equipo_id' => Yii::t('app', 'Equipo ID'),
            'usua_id_actual' => Yii::t('app', 'Usua Id Actual'),
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
            'basesatisfaccion_id' => Yii::t('app', 'ID Gestion Satisfaccion'),
        ];
    }

    /*
     * @return \yii\db\ActiveQuery
     */

    public function getTmpejecucionbloquedetalles() {
        return $this->hasMany(Tmpejecucionbloquedetalles::className(), ['tmpejecucionformulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionbloques() {
        return $this->hasMany(Tmpejecucionbloques::className(), ['tmpejecucionformulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionfeedbacks() {
        return $this->hasMany(Tmpejecucionfeedbacks::className(), ['tmpejecucionformulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTransacion() {
        return $this->hasOne(Transacions::className(), ['id' => 'transacion_id']);
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
    public function getTmpejecucionsecciones() {
        return $this->hasMany(Tmpejecucionsecciones::className(), ['tmpejecucionformulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmptableroexperiencias() {
        return $this->hasMany(Tmptableroexperiencias::className(), ['tmpejecucionformulario_id' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmptiposllamadas() {
        return $this->hasMany(Tmptiposllamada::className(), ['tmpejecucionformulario_id' => 'id']);
    }

    /**
     * Metodo para guardar el formulario
     * 
     * @param int   $id     Id Tmp Formulario     
     * 
     * @author Felipe Echeverro <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function guardarFormulario($id) {
        /*
         * TRANSACCIÓNN PARA GARANTIZAR QUE SE PASÓ A TABLAS DE EJECUCIÓN
         */
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try {
            $sql = "CALL sp_formulario_guardar(" . $id . ");";
            $command = \Yii::$app->db->createCommand($sql);
            $command->execute();
            $transaction->commit();
        } catch (\yii\base\Exception $e) { // If a query fails, an exception is raised            
            $transaction->rollBack();
            throw new \yii\base\Exception("Hubo un error pasando el formulario de TMP a Ejecucion: " . $e->getMessage());
        }
    }

}
