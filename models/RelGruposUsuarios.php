<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "rel_grupos_usuarios".
 *
 * @property integer $id_rel_grupos_usuarios
 * @property integer $usuario_id
 * @property integer $grupo_id
 *
 * @property TblUsuarios $usuario
 * @property TblGruposUsuarios $grupo
 */
class RelGruposUsuarios extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rel_grupos_usuarios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['usuario_id', 'grupo_id'], 'required'],
            [['usuario_id', 'grupo_id'], 'integer']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id_rel_grupos_usuarios' => Yii::t('app', 'Id Rel Grupos Usuarios'),
            'usuario_id' => Yii::t('app', 'Usuario ID'),
            'grupo_id' => Yii::t('app', 'Grupo ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsuario()
    {
        return $this->hasOne(TblUsuarios::className(), ['usua_id' => 'usuario_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getGrupo()
    {
        return $this->hasOne(TblGruposUsuarios::className(), ['grupos_id' => 'grupo_id']);
    }
}
