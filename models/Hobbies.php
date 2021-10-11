<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "tbl_hv_infopersonal".
 *
 * @property integer $idhvinforpersonal
 * @property string $cliente
 * @property string $director
 * @property string $gerente
 * @property string $pcrc
 * @property string $hvnombre
 * @property integer $hvidentificacion
 * @property string $hvdireccionoficina
 * @property string $hvdireccioncasa
 * @property string $hvemailcoporativo
 * @property integer $hvmovil
 * @property integer $hvcontactooficina
 * @property string $hvpais
 * @property string $hvciudad
 * @property string $hvmodalidatrabajo
 * @property integer $hvautorizacion
 * @property integer $hvsusceptible
 * @property double $hvsatu
 * @property string $areatrabajo
 * @property string $rol
 * @property string $antiguedadrol
 * @property string $fechacontacto
 * @property string $tipo
 * @property string $nivel
 * @property string $afinidad
 * @property string $nombrejefe
 * @property string $cargojefe
 * @property string $rolanterior
 * @property string $profesion
 * @property string $especializacion
 * @property string $maestria
 * @property string $doctorado
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fehacreacion
 */
class Hobbies extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

   
    public static function tableName()
    {
        return 'tbl_hv_hobbies';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text'], 'string'],
         
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'id'),
            'text' => Yii::t('app', 'text'),
        
        ];
    }
}
