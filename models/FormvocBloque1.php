<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * This is the model class for table "tbl_formvoc_bloque1".
 *
 * @property integer $idformvocbloque1
 * @property integer $idpcrccxm
 * @property integer $idpcrcspeech
 * @property string $cod_pcrc
 * @property string $pcrc
 * @property integer $idvalorado
 * @property string $idspeech
 * @property string $fechahora
 * @property string $usuarioagente
 * @property string $duracions
 * @property string $extension
 * @property string $dimensionform
 * @property integer $anulado
 * @property integer $usua_id
 * @property string $fechacreacion
 */
class FormvocBloque1 extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_formvoc_bloque1';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idpcrccxm', 'idpcrcspeech', 'idvalorado', 'anulado', 'usua_id'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['cod_pcrc'], 'string', 'max' => 20],
            [['pcrc', 'fechahora', 'usuarioagente', 'extension', 'dimensionform'], 'string', 'max' => 100],
            [['idspeech', 'duracions'], 'string', 'max' => 50]
        ];
        
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idformvocbloque1' => Yii::t('app', ''),
            'idpcrccxm' => Yii::t('app', ''),
            'idpcrcspeech' => Yii::t('app', ''),
            'cod_pcrc' => Yii::t('app', ''),
            'pcrc' => Yii::t('app', ''),
            'idvalorado' => Yii::t('app', ''),
            'idspeech' => Yii::t('app', ''),
            'fechahora' => Yii::t('app', ''),
            'usuarioagente' => Yii::t('app', ''),
            'duracions' => Yii::t('app', ''),
            'extension' => Yii::t('app', ''),
            'dimensionform' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
            'usua_id' => Yii::t('app', ''),
            'fechacreacion' => Yii::t('app', ''),
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    public function buscarformvoc($params){        
        
        $query = FormvocBloque1::find()
                    ->where(['anulado' => '0'])
                    ->orderBy([
                              'idformvocbloque1' => SORT_DESC
                            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'idpcrcspeech' => $this->idpcrcspeech,
            'cod_pcrc' => $this->cod_pcrc,
            'idvalorado' => $this->idvalorado,
            'usua_id' => $this->usua_id,
        ]);

        $query->andFilterWhere(['like', 'idpcrcspeech', $this->idpcrcspeech])
                ->andFilterWhere(['like', 'cod_pcrc', $this->cod_pcrc])
                ->andFilterWhere(['like', 'idvalorado', $this->idvalorado])
                ->andFilterWhere(['like', 'usua_id', $this->usua_id]);
	
	$txtFecha = explode(" ", $this->fechahora);
                $txtcanti = count($txtFecha);
                
                 if ($txtcanti > 1) {
                    $txtfechaini = $txtFecha[0];
                    $txtfechafin = $txtFecha[2];
                    
                    $query->andFilterWhere(['like','idpcrcspeech',$this->idpcrcspeech])
                    ->andFilterWhere(['like','cod_pcrc',$this->cod_pcrc])
                    ->andFilterWhere(['like','idvalorado',$this->idvalorado])
                    ->andFilterWhere(['like','usua_id',$this->usua_id])
                    ->andFilterWhere(['between', 'fechahora', $txtfechaini, $txtfechafin]);
                 }
                 else
                 {
                    $query->andFilterWhere(['like','idpcrcspeech',$this->idpcrcspeech])
                    ->andFilterWhere(['like','cod_pcrc',$this->cod_pcrc])
                    ->andFilterWhere(['like','idvalorado',$this->idvalorado])
                    ->andFilterWhere(['like','usua_id',$this->usua_id])
                    ->andFilterWhere(['like','fechahora',$this->fechahora]);
                 }

        return $dataProvider;
    }

    public function getformvoc1($opcion){
        $data = Yii::$app->db->createCommand("select usua_nombre from tbl_usuarios where usua_id = $opcion")->queryScalar();

        return $data;
    }

    public function getformvoc2($opcion){
        $data = Yii::$app->db->createCommand("select name from tbl_evaluados where id = $opcion")->queryScalar();

        return $data;
    }

    public function getformvoc3($opcion){
        $data = Yii::$app->db->createCommand("select identificacion from tbl_evaluados where id = $opcion")->queryScalar();

        return $data;
    }

    public function getformvoc4($opcion){
        $data = Yii::$app->db->createCommand("select nameArbol from tbl_speech_servicios where arbol_id = $opcion")->queryScalar();

        return $data;
    }

    public function getformvoc5($opcion){
        $varcod_pcrc = Yii::$app->db->createCommand("select cod_pcrc from tbl_formvoc_bloque1 where idformvocbloque1 = $opcion")->queryScalar();
        $varpcrc = Yii::$app->db->createCommand("select pcrc from tbl_formvoc_bloque1 where idformvocbloque1 = $opcion")->queryScalar();

        $data = $varcod_pcrc.' - '.$varpcrc;
        return $data;
    }
}