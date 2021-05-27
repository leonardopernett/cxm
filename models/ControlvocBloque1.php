<?php

namespace app\models;

use Yii;
use yii\db\Query;

/**
 * This is the model class for table "tbl_controlvoc_bloque1".
 *
 * @property integer $idbloque1
 * @property integer $valorador_id
 * @property integer $arbol_id
 * @property string $dimensions
 * @property integer $lider_id
 * @property integer $tecnico_id
 * @property string $numidextsp
 * @property string $fechahora
 * @property string $usuagente
 * @property string $duracion
 * @property string $extencion
 * @property string $fechacreacion
 * @property integer $anulado
 */
class ControlvocBloque1 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_controlvoc_bloque1';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['valorador_id', 'arbol_id', 'lider_id', 'tecnico_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['dimensions', 'numidextsp', 'fechahora', 'usuagente', 'duracion', 'extencion'], 'string', 'max' => 50]
        ];    
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idbloque1' => Yii::t('app', ''),
            'valorador_id' => Yii::t('app', ''),
            'arbol_id' => Yii::t('app', ''),
            'dimensions' => Yii::t('app', ''),
            'lider_id' => Yii::t('app', ''),
            'tecnico_id' => Yii::t('app', ''),
            'numidextsp' => Yii::t('app', ''),
            'fechahora' => Yii::t('app', ''),
            'usuagente' => Yii::t('app', ''),
            'duracion' => Yii::t('app', ''),
            'extencion' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }

    public function getArboles(){
        return $this->hasOne(Arboles::className(), ['id' => 'arbol_id']);
    }

    public function getUsuarios() {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'valorador_id']);
    }

    public function getEvaluados() {
        return $this->hasOne(Evaluados::className(), ['id' => 'tecnico_id']);
    }

    public function getLideres($opcion1){
        $varId = $opcion1;

        $querys =  new Query;
        $querys     ->select(['usua_nombre'])
                    ->from('tbl_usuarios')
                    ->where('tbl_usuarios.usua_id = '.$varId.'');
                    
        $command = $querys->createCommand();
        $data = $command->queryScalar();  

        return $data;
    }
}