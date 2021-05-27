<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmpejecucionfeedbacks".
 *
 * @property integer $id
 * @property integer $tipofeedback_id
 * @property integer $tmpejecucionformulario_id
 * @property integer $usua_id
 * @property string $created
 * @property integer $usua_id_lider
 * @property integer $evaluado_id
 * @property integer $snavisar
 * @property integer $snaviso_revisado
 * @property string $dsaccion_correctiva
 * @property string $feaccion_correctiva
 * @property integer $nmescalamiento
 * @property string $feescalamiento
 * @property string $dscausa_raiz
 * @property string $dscompromiso
 * @property string $dscomentario
 * @property integer $basessatisfaccion_id
 * 
 * @property TblTipofeedbacks $tipofeedback
 * @property TblTmpejecucionformularios $tmpejecucionformulario
 */
class Tmpejecucionfeedbacks extends \yii\db\ActiveRecord {

    public $catfeedback;

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_tmpejecucionfeedbacks';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['tipofeedback_id', 'tmpejecucionformulario_id', 'usua_id', 'catfeedback'], 'required'],
            [['tipofeedback_id', 'tmpejecucionformulario_id', 'usua_id', 'usua_id_lider', 'evaluado_id', 'snavisar', 'snaviso_revisado', 'nmescalamiento', 'basessatisfaccion_id'], 'integer'],
            [['created', 'feaccion_correctiva', 'feescalamiento'], 'safe'],
            [['dsaccion_correctiva', 'dscausa_raiz', 'dscompromiso', 'dscomentario'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'tipofeedback_id' => Yii::t('app', 'Tipofeedback ID'),
            'tmpejecucionformulario_id' => Yii::t('app', 'Tmpejecucionformulario ID'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'created' => Yii::t('app', 'Created'),
            'usua_id_lider' => Yii::t('app', 'Usua Id Lider'),
            'evaluado_id' => Yii::t('app', 'Evaluado ID'),
            'snavisar' => Yii::t('app', 'Snavisar'),
            'snaviso_revisado' => Yii::t('app', 'Snaviso Revisado'),
            'dsaccion_correctiva' => Yii::t('app', 'Dsaccion Correctiva'),
            'feaccion_correctiva' => Yii::t('app', 'Feaccion Correctiva'),
            'nmescalamiento' => Yii::t('app', 'Nmescalamiento'),
            'feescalamiento' => Yii::t('app', 'Feescalamiento'),
            'dscausa_raiz' => Yii::t('app', 'Dscausa Raiz'),
            'dscompromiso' => Yii::t('app', 'Dscompromiso'),
            'dscomentario' => Yii::t('app', 'Dscomentario'),
            'catfeedback' => Yii::t('app', 'Categoriafeedbacks'),
            'basessatisfaccion_id' => Yii::t('app', 'basessatisfaccion_id'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTipofeedback() {
        return $this->hasOne(Tipofeedbacks::className(), ['id' => 'tipofeedback_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTmpejecucionformulario() {
        return $this->hasOne(Tmpejecucionformularios::className(), ['id' => 'tmpejecucionformulario_id']);
    }

    /**
     * Metodo que realiza el join de feedbacks
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getJoinTipoFeedbacks($form_id) {
        return \app\models\Tmpejecucionfeedbacks::find()
                        ->joinWith('tipofeedback')
                        ->join('JOIN', 'tbl_categoriafeedbacks', 'tbl_categoriafeedbacks.id = tbl_tipofeedbacks.categoriafeedback_id')
                        ->select('tbl_tmpejecucionfeedbacks.created'
                                . ', tbl_tipofeedbacks.name AS nameTipo'
                                . ', tbl_categoriafeedbacks.name AS nameCate'
                                . ', tbl_tmpejecucionfeedbacks.evaluado_id AS evaluado'
                                . ', tbl_tmpejecucionfeedbacks.id'
                                . ', tbl_tmpejecucionfeedbacks.dscomentario'
                                . ', tbl_tmpejecucionfeedbacks.tmpejecucionformulario_id As idEjecucionFormulario'
                                . ', tbl_tipofeedbacks.id AS idTipo'
                                . ', tbl_categoriafeedbacks.id AS cateId')
                        ->where('tbl_tmpejecucionfeedbacks.tmpejecucionformulario_id = ' . $form_id)
                        ->all();
    }

}
