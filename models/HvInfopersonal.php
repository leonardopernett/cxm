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
class HvInfopersonal extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */

   /*   public $hvidentificacion;
     public $hvmovil;
     public $hvcontactooficina;
     public $hvautorizacion;
     public $hvsusceptible;
     public $anulado;
     public $usua_id;
     public $hvsatu;
     public $fechacontacto;
     public $fehacreacion;
     public $cliente;
     public $director;
     public $gerente;
     public $pcrc;
     public $hvnombre;
     public $nombrejefe;
     public $hvdireccionoficina;
     public $hvdireccioncasa;
     public $hvemailcorporativo;
     public $hvpais;
     public $hvciudad;
     public $hvmodalidatrabajo;
     public $areatrabajo;
     public $rol;
     public $antiguedadrol;
     public $tipo;
     public $nivel;
     public $afinidad;
     public $cargojefe;
     public $rolanterior;
     public $profesion;
     public $especializacion;
     public $maestria;
     public $doctorado; */

    public static function tableName()
    {
        return 'tbl_hv_infopersonal';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['hvidentificacion', 'hvmovil', 'hvcontactooficina', 'hvautorizacion', 'hvsusceptible', 'anulado', 'usua_id'], 'integer'],
            [['hvsatu'], 'integer'],
            [['fechacontacto', 'fehacreacion'], 'safe'],
            [['cliente', 'director', 'gerente', 'pcrc', 'hvnombre', 'nombrejefe','hvmodalidatrabajo'], 'string', 'max' => 250],
            [['hvdireccionoficina', 'hvdireccioncasa', 'hvemailcoporativo'], 'string', 'max' => 300],
            [['hvpais', 'hvciudad', 'hvmodalidatrabajo', 'areatrabajo', 'rol', 'antiguedadrol'], 'string', 'max' => 50],
            [['tipo', 'nivel', 'afinidad', 'cargojefe', 'rolanterior', 'profesion', 'especializacion', 'maestria', 'doctorado'], 'string', 'max' => 100]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idhvinforpersonal' => Yii::t('app', 'Idhvinforpersonal'),
            'cliente' => Yii::t('app', 'Cliente'),
            'director' => Yii::t('app', 'Director'),
            'gerente' => Yii::t('app', 'Gerente'),
            'pcrc' => Yii::t('app', 'Pcrc'),
            'hvnombre' => Yii::t('app', 'Hvnombre'),
            'hvidentificacion' => Yii::t('app', 'Hvidentificacion'),
            'hvdireccionoficina' => Yii::t('app', 'Hvdireccionoficina'),
            'hvdireccioncasa' => Yii::t('app', 'Hvdireccioncasa'),
            'hvemailcorporativo' => Yii::t('app', 'Hvemailcoporativo'),
            'hvmovil' => Yii::t('app', 'Hvmovil'),
            'hvcontactooficina' => Yii::t('app', 'Hvcontactooficina'),
            'hvpais' => Yii::t('app', 'Hvpais'),
            'hvciudad' => Yii::t('app', 'Hvciudad'),
            'hvmodalidatrabajo' => Yii::t('app', 'Hvmodalidatrabajo'),
            'hvautorizacion' => Yii::t('app', 'Hvautorizacion'),
            'hvsusceptible' => Yii::t('app', 'Hvsusceptible'),
            'hvsatu' => Yii::t('app', 'Hvsatu'),
            'areatrabajo' => Yii::t('app', 'Areatrabajo'),
            'rol' => Yii::t('app', 'Rol'),
            'antiguedadrol' => Yii::t('app', 'Antiguedadrol'),
            'fechacontacto' => Yii::t('app', 'Fechacontacto'),
            'tipo' => Yii::t('app', 'Tipo'),
            'nivel' => Yii::t('app', 'Nivel'),
            'afinidad' => Yii::t('app', 'Afinidad'),
            'nombrejefe' => Yii::t('app', 'Nombrejefe'),
            'cargojefe' => Yii::t('app', 'Cargojefe'),
            'rolanterior' => Yii::t('app', 'Rolanterior'),
            'profesion' => Yii::t('app', 'Profesion'),
            'especializacion' => Yii::t('app', 'Especializacion'),
            'maestria' => Yii::t('app', 'Maestria'),
            'doctorado' => Yii::t('app', 'Doctorado'),
         /*    'hobbies' => Yii::t('app', 'hobbies'), */
            'anulado' => Yii::t('app', 'Anulado'),
            'usua_id' => Yii::t('app', 'Usua ID'),
            'fehacreacion' => Yii::t('app', 'Fehacreacion'),
            'file' => Yii::t('app', 'File'),
        ];
    }
}
