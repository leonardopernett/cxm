<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rel_usuarios_roles".
 *
 * @property integer $rel_usua_id
 * @property integer $rel_role_id
 */
class RelUsuariosRoles extends \yii\db\ActiveRecord {

    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'rel_usuarios_roles';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['rel_usua_id', 'rel_role_id'], 'required'],
            [['rel_usua_id', 'rel_role_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'rel_usua_id' => Yii::t('app', 'Rel Usua ID'),
            'rel_role_id' => Yii::t('app', 'Rel Role ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuarios() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'rel_usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRoles() {
        return $this->hasOne(Roles::className(), ['role_id' => 'rel_role_id']);
    }

}
