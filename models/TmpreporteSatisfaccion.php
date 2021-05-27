<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_tmpreporte_satisfaccion".
 *
 * @property integer $id
 * @property integer $categoria_id
 * @property string $enunciado_pre
 * @property string $tb
 * @property string $ttb
 * @property string $btb
 * @property string $bb
 * @property string $promotores
 * @property string $pasivos
 * @property string $detractores
 * @property string $nps
 * @property string $solucion
 * @property integer $pcrc
 * @property integer $usua_id
 *
 * @property TblCategorias $categoria
 * @property TblArbols $pcrc0
 * @property TblUsuarios $usua
 */
class TmpreporteSatisfaccion extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_tmpreporte_satisfaccion';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['categoria_id', 'pcrc', 'usua_id'], 'required'],
            [['categoria_id', 'pcrc', 'usua_id'], 'integer'],
            [['enunciado_pre'], 'string', 'max' => 200],
            [['tb', 'ttb', 'btb', 'bb', 'promotores', 'pasivos', 'detractores', 'nps', 'solucion'], 'string', 'max' => 3]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'categoria_id' => Yii::t('app', 'Categoria ID'),
            'enunciado_pre' => Yii::t('app', 'Enunciado Pre'),
            'tb' => Yii::t('app', 'Tb'),
            'ttb' => Yii::t('app', 'Ttb'),
            'btb' => Yii::t('app', 'Btb'),
            'bb' => Yii::t('app', 'Bb'),
            'promotores' => Yii::t('app', 'Promotores'),
            'pasivos' => Yii::t('app', 'Pasivos'),
            'detractores' => Yii::t('app', 'Detractores'),
            'nps' => Yii::t('app', 'Nps'),
            'solucion' => Yii::t('app', 'Solucion'),
            'pcrc' => Yii::t('app', 'Pcrc'),
            'usua_id' => Yii::t('app', 'Usua ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCategoria()
    {
        return $this->hasOne(Categorias::className(), ['id' => 'categoria_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPcrc0()
    {
        return $this->hasOne(Arboles::className(), ['id' => 'pcrc']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua()
    {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id']);
    }
}
