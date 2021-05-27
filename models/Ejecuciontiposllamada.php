<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_ejecuciontiposllamada".
 *
 * @property integer $id
 * @property integer $ejecucionformulario_id
 * @property integer $tiposllamadasdetalle_id
 *
 * @property TblEjecucionformularios $ejecucionformulario
 * @property TblTiposllamadasdetalles $tiposllamadasdetalle
 */
class Ejecuciontiposllamada extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_ejecuciontiposllamada';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['ejecucionformulario_id', 'tiposllamadasdetalle_id'], 'required'],
            [['ejecucionformulario_id', 'tiposllamadasdetalle_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'id' => Yii::t('app', 'ID'),
            'ejecucionformulario_id' => Yii::t('app', 'Ejecucionformulario ID'),
            'tiposllamadasdetalle_id' => Yii::t('app', 'Tiposllamadasdetalle ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEjecucionformulario() {
        return $this->hasOne(Ejecucionformularios::className(), ['id' => 'ejecucionformulario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTiposllamadasdetalle() {
        return $this->hasOne(Tiposllamadasdetalles::className(), ['id' => 'tiposllamadasdetalle_id']);
    }

    /**
     * Metodo que consulta la tabla de llamadas
     * 
     * @return array
     * @author Felipe Echeverri <felipe.echeverri@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public static function getTabLlamByIdEjeForm($ejec_form_id) {
        $sql = "SELECT `tbl_ejecuciontiposllamada`.`id` AS `id_eje_tipo_llamada`"
                . ", `tbl_ejecuciontiposllamada`.`ejecucionformulario_id`"
                . ", `tbl_tiposllamadasdetalles`.`id` AS `id_det_llamada`"
                . ", `tbl_tiposllamadas`.`id` AS `id_tipo_llamada`"
                . ", `tbl_tiposllamadasdetalles`.`name` AS `name_det_llamada`"
                . ", `tbl_tiposllamadas`.`name` AS `name_tipo_llamada` "
                . "FROM `tbl_ejecuciontiposllamada` "
                . "LEFT JOIN `tbl_tiposllamadasdetalles` "
                . "ON `tbl_ejecuciontiposllamada`.`tiposllamadasdetalle_id` = `tbl_tiposllamadasdetalles`.`id` "
                . "JOIN `tbl_tiposllamadas` "
                . "ON tbl_tiposllamadas.id = tbl_tiposllamadasdetalles.tiposllamada_id "
                . "WHERE `ejecucionformulario_id`=" . $ejec_form_id;
        return \Yii::$app->db->createCommand($sql)->queryAll();
    }

}
