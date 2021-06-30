<?php

namespace app\models;

use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Query;

/**
 * This is the model class for table "tbl_control_params".
 *
 * @property integer $id
 * @property integer $arbol_id
 * @property string $dimensions
 * @property string $cant_valor
 * @property string $tipo_corte
 * @property string $argumentos
 * @property string $fechacreacion
 * @property string $anulado
 */
class ControlParams extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_control_params';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['arbol_id'], 'required', 'on' => 'create'],
            [['arbol_id','evaluados_id', 'anulado'], 'integer'],
            [['fechacreacion'], 'safe'],
            [['dimensions', 'cant_valor'], 'string', 'max' => 200],
            [['argumentos'], 'string', 'max' => 150]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'arbol_id' => Yii::t('app', 'Arbol ID'),
            'dimensions' => Yii::t('app', 'Dimensions'),
            'cant_valor' => Yii::t('app', 'Cant Valor'),            
            'evaluados_id' => Yii::t('app', ''),
            'argumentos' => Yii::t('app', 'Argumentos'),
            'fechacreacion' => Yii::t('app', ''),
            'anulado' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArboles() {
        return $this->hasOne(Arboles::className(), ['id' => 'arbol_id']);
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function Obtener($params)
    {
        
        $txtfechacreacion = date("Y-m-01");

        $query = ControlParams::find()
                    ->joinWith('arboles')
                    ->where(['anulado' => '0'])
                    ->andwhere("fechacreacion > '$txtfechacreacion'")
                    ->andwhere(['evaluados_id' => $params]);
                    //->andwhere(['responsable' => Yii::$app->user->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // if (!($this->load($params) && $this->validate())) {
        //      return $dataProvider;
        // }

        // $query->andFilterWhere([
        //     'evaluados_id' => $this->evaluados_id,
        // ]);

        // $query->andFilterWhere(['like', 'evaluados_id', $this->evaluados_id]);

        return $dataProvider;
        

    }


     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search2($params)
    {
        $query = ControlParams::find()
                ->where(['anulado' => '0'])
                ->andwhere('evaluados_id = 0');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'evaluados_id' => $this->evaluados_id,
        ]);

        $query->andFilterWhere(['like', 'evaluados_id', $this->evaluados_id]);

        return $dataProvider;
    }

     /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function Obtener2($id, $evaluados_id)
    {
	$txtfechacreacion = Yii::$app->db->createCommand("select distinct fechacreacion from tbl_control_procesos where anulado = 0 and id = $id and evaluados_id = $evaluados_id")->queryScalar();
        $txtcorte = Yii::$app->db->createCommand('select tipo_corte from tbl_control_procesos where evaluados_id ='.$evaluados_id.' and id ='.$id.'')->queryScalar();
        $fechainiC = Yii::$app->db->createCommand("select fechainiciotc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();
        $fechafinC =  Yii::$app->db->createCommand("select fechafintc from tbl_tipocortes where tipocortetc like '$txtcorte'")->queryScalar();  


        $query = ControlParams::find()
                    ->joinWith('arboles')                    
                    ->where(['anulado' => 'null'])
                    ->andwhere('evaluados_id ='.$evaluados_id)
                    ->andwhere(['between','fechacreacion', $txtfechacreacion, $txtfechacreacion]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($evaluados_id) && $this->validate())) {
             return $dataProvider;
        }

        return $dataProvider;       
    }

    public function Obtenerseguimiento($params)
    {        
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
 	// $fechainiC = date('2019-04-01');
        // $fechafinC = date('2019-04-30');
        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year));  
  
        //$query = ControlParams::find()->distinct()
        //            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
        //            ->joinWith('arboles')
        //            ->where(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
        //            //-> where(['responsable' => Yii::$app->user->identity->id]);

        $txtvarBanco = 'Bancolombia';
        $txtvarDirectv = 'Directv';
        $txtFechaIniBank = Yii::$app->db->createCommand('select fechainiciotc from tbl_tipocortes where anulado = 0 and tipocortetc like "%'.$txtvarBanco.'%"')->queryScalar();
        $txtFechaFinBank = Yii::$app->db->createCommand('select fechafintc from tbl_tipocortes where anulado = 0 and tipocortetc like "%'.$txtvarBanco.'%"')->queryScalar();

        $txtFechaIniDirect = Yii::$app->db->createCommand('select fechainiciotc from tbl_tipocortes where anulado = 0 and tipocortetc like "%'.$txtvarDirectv.'%"')->queryScalar();
        $txtFechaFinDirect = Yii::$app->db->createCommand('select fechafintc from tbl_tipocortes where anulado = 0 and tipocortetc like "%'.$txtvarDirectv.'%"')->queryScalar();

        $sessiones1 = Yii::$app->user->identity->id;


        if ($sessiones1 == 2953 || $sessiones1 == 1525 || $sessiones1 == 7 || $sessiones1 == 438) {
            $query = ControlParams::find()->distinct()
                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                    ->joinWith('arboles')
                    ->where(['tbl_control_params.anulado' => 'null'])
                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                    //-> where(['responsable' => Yii::$app->user->identity->id]);
        }
        else
        {
            if ($sessiones1 == 70 || $sessiones1 == 384 || $sessiones1 == 1366) {
                $query = ControlParams::find()->distinct()
                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                    ->joinWith('arboles')
                    ->where(['tbl_control_params.anulado' => 'null'])
                    ->andwhere(['tbl_arbols.arbol_id' => '17'])
                    ->andwhere(['between','tbl_control_params.fechacreacion', $txtFechaIniBank, $txtFechaFinBank]);
                    //-> where(['responsable' => Yii::$app->user->identity->id]);
            }
            else
            {
                if ($sessiones1 == 1028) {
                    $query = ControlParams::find()->distinct()
                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                        ->joinWith('arboles')
                        ->where(['tbl_control_params.anulado' => 'null'])
                        ->andwhere(['in','tbl_arbols.arbol_id',[678, 358]])
                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                }
                else
                {
                    if ($sessiones1 == 2398) {
                        $query = ControlParams::find()->distinct()
                            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                            ->joinWith('arboles')
                            ->where(['tbl_control_params.anulado' => 'null'])
                            ->andwhere(['tbl_arbols.arbol_id' => '1750'])
                            ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                            //-> where(['responsable' => Yii::$app->user->identity->id]);
                    }
                    else
                    {
                        if ($sessiones1 == 644) {
                            $query = ControlParams::find()->distinct()
                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                ->joinWith('arboles')
                                ->where(['tbl_control_params.anulado' => 'null'])
                                ->andwhere(['in','tbl_arbols.arbol_id',[677, 559, 2767]])
                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                //-> where(['responsable' => Yii::$app->user->identity->id]);
                        }
                        else
                        {
                            if ($sessiones1 == 793) {
                                $query = ControlParams::find()->distinct()
                                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                    ->joinWith('arboles')
                                    ->where(['tbl_control_params.anulado' => 'null'])
                                    ->andwhere(['in','tbl_arbols.arbol_id',[2457, 800, 2764]])
                                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                    //-> where(['responsable' => Yii::$app->user->identity->id]);
                            }
                            else
                            {
                                if ($sessiones1 == 924) {
                                    $query = ControlParams::find()->distinct()
                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                        ->joinWith('arboles')
                                        ->where(['tbl_control_params.anulado' => 'null'])
                                        ->andwhere(['tbl_arbols.arbol_id' => '675'])
                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                }
                                else
                                {
                                    if ($sessiones1 == 495) {
                                        $query = ControlParams::find()->distinct()
                                            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                            ->joinWith('arboles')
                                            ->where(['tbl_control_params.anulado' => 'null'])
                                            ->andwhere(['tbl_arbols.arbol_id' => '1851'])
                                            ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                            //-> where(['responsable' => Yii::$app->user->identity->id]);
                                    }
                                    else
                                    {
                                        if ($sessiones1 == 44) {
                                            $query = ControlParams::find()->distinct()
                                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                ->joinWith('arboles')
                                                ->where(['tbl_control_params.anulado' => 'null'])
                                                ->andwhere(['tbl_arbols.arbol_id' => '1822'])
                                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                //-> where(['responsable' => Yii::$app->user->identity->id]);
                                        }
                                        else
                                        {
                                            if ($sessiones1 == 1053) {
                                                $query = ControlParams::find()->distinct()
                                                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                    ->joinWith('arboles')
                                                    ->where(['tbl_control_params.anulado' => 'null'])
                                                    ->andwhere(['tbl_arbols.arbol_id' => '1312'])
                                                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                    //-> where(['responsable' => Yii::$app->user->identity->id]);
                                            }
                                            else
                                            {
                                                if ($sessiones1 == 694) {
                                                    $query = ControlParams::find()->distinct()
                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                        ->joinWith('arboles')
                                                        ->where(['tbl_control_params.anulado' => 'null'])
                                                        ->andwhere(['tbl_arbols.arbol_id' => '1302'])
                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                }
                                                else
                                                {
                                                    if ($sessiones1 == 2715) {
                                                        $query = ControlParams::find()->distinct()
                                                            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                            ->joinWith('arboles')
                                                            ->where(['tbl_control_params.anulado' => 'null'])
                                                            ->andwhere(['tbl_arbols.arbol_id' => '2440'])
                                                            ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                            //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                    }
                                                    else
                                                    {
                                                        if ($sessiones1 == 2399 || $sessiones1 == 2962) {
                                                            $query = ControlParams::find()->distinct()
                                                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                ->joinWith('arboles')
                                                                ->where(['tbl_control_params.anulado' => 'null'])
                                                                ->andwhere(['in','tbl_arbols.arbol_id',[2451, 2614, 2764]])
                                                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                        }
                                                        else
                                                        {
                                                            if ($sessiones1 == 1938) {
                                                                $query = ControlParams::find()->distinct()
                                                                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                    ->joinWith('arboles')
                                                                    ->where(['tbl_control_params.anulado' => 'null'])
                                                                    ->andwhere(['tbl_arbols.arbol_id' => '1652'])
                                                                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                    //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                            }
                                                            else
                                                            {
                                                                if ($sessiones1 == 655) {
                                                                    $query = ControlParams::find()->distinct()
                                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                        ->joinWith('arboles')
                                                                        ->where(['tbl_control_params.anulado' => 'null'])
                                                                        ->andwhere(['tbl_arbols.arbol_id' => '563'])
                                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                }
                                                                else
                                                                {
                                                                    if ($sessiones1 == 1968) {
                                                                        $query = ControlParams::find()->distinct()
                                                                            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                            ->joinWith('arboles')
                                                                            ->where(['tbl_control_params.anulado' => 'null'])
                                                                            ->andwhere(['tbl_arbols.arbol_id' => '1494'])
                                                                            ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                            //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                    }
                                                                    else
                                                                    {
                                                                        if ($sessiones1 == 415) {
                                                                            $query = ControlParams::find()->distinct()
                                                                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                ->joinWith('arboles')
                                                                                ->where(['tbl_control_params.anulado' => 'null'])
                                                                                ->andwhere(['tbl_arbols.arbol_id' => '1667'])
                                                                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                        }
                                                                        else
                                                                        {
                                                                            if ($sessiones1 == 1396 || $sessiones1 == 2846) {
                                                                                $query = ControlParams::find()->distinct()
                                                                                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                    ->joinWith('arboles')
                                                                                    ->where(['tbl_control_params.anulado' => 'null'])
                                                                                    ->andwhere(['tbl_arbols.arbol_id' => '1323'])
                                                                                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                    //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                            }
                                                                            else
                                                                            {
                                                                                if ($sessiones1 == 2973) {
                                                                                    $query = ControlParams::find()->distinct()
                                                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                        ->joinWith('arboles')
                                                                                        ->where(['tbl_control_params.anulado' => 'null'])
                                                                                        ->andwhere(['tbl_arbols.arbol_id' => '2789'])
                                                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                }
                                                                                else
                                                                                {
                                                                                    if ($sessiones1 == 1251) {
                                                                                        $query = ControlParams::find()->distinct()
                                                                                            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                            ->joinWith('arboles')
                                                                                            ->where(['tbl_control_params.anulado' => 'null'])
                                                                                            ->andwhere(['tbl_arbols.arbol_id' => '2692'])
                                                                                            ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                            //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                    }
                                                                                    else
                                                                                    {
                                                                                        if ($sessiones1 == 657) {
                                                                                            $query = ControlParams::find()->distinct()
                                                                                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                ->joinWith('arboles')
                                                                                                ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                ->andwhere(['tbl_arbols.arbol_id' => '1841'])
                                                                                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                        }
                                                                                        else
                                                                                        {
                                                                                            if ($sessiones1 == 1981) {
                                                                                                $query = ControlParams::find()->distinct()
                                                                                                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                    ->joinWith('arboles')
                                                                                                    ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                    ->andwhere(['tbl_arbols.arbol_id' => '2382'])
                                                                                                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                    //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                            }
                                                                                            else
                                                                                            {
                                                                                                if ($sessiones1 == 739) {
                                                                                                    $query = ControlParams::find()->distinct()
                                                                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                        ->joinWith('arboles')
                                                                                                        ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                        ->andwhere(['tbl_arbols.arbol_id' => '2411'])
                                                                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                }
                                                                                                else
                                                                                                {
                                                                                                    if ($sessiones1 == 3166) {
                                                                                                        $query = ControlParams::find()->distinct()
                                                                                                            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                            ->joinWith('arboles')
                                                                                                            ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                            ->andwhere(['tbl_arbols.arbol_id' => '2523'])
                                                                                                            ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                            //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                    }
                                                                                                    else
                                                                                                    {
                                                                                                        if ($sessiones1 == 669) {
                                                                                                            $query = ControlParams::find()->distinct()
                                                                                                                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                    ->joinWith('arboles')
                                                                                                                    ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                    ->andwhere(['tbl_arbols.arbol_id' => '2593'])
                                                                                                                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                    //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                        }
                                                                                                        else
                                                                                                        {
                                                                                                            if ($sessiones1 == 933) {
                                                                                                                $query = ControlParams::find()->distinct()
                                                                                                                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                    ->joinWith('arboles')
                                                                                                                    ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                    ->andwhere(['tbl_arbols.arbol_id' => '606'])
                                                                                                                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                    //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                            }
                                                                                                            else
                                                                                                            {
                                                                                                                if ($sessiones1 == 2793) {
                                                                                                                    $query = ControlParams::find()->distinct()
                                                                                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                        ->joinWith('arboles')
                                                                                                                        ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                        ->andwhere(['tbl_arbols.arbol_id' => '2604'])
                                                                                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                }
                                                                                                                else
                                                                                                                {
                                                                                                                    if ($sessiones1 == 963) {
                                                                                                                        $query = ControlParams::find()->distinct()
                                                                                                                            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                            ->joinWith('arboles')
                                                                                                                            ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                            ->andwhere(['in','tbl_arbols.id',[1437, 1438, 1808, 2003, 2256, 2307, 2330, 2389]])
                                                                                                                            ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                    }
                                                                                                                    else
                                                                                                                    {
                                                                                                                        if ($sessiones1 == 113) {
                                                                                                                            $query = ControlParams::find()->distinct()
                                                                                                                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                ->joinWith('arboles')
                                                                                                                                ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                ->andwhere(['in','tbl_arbols.id',[1450, 1944, 2364, 2372, 2373]])
                                                                                                                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                        }
                                                                                                                        else
                                                                                                                        {
                                                                                                                            if ($sessiones1 == 244 || $sessiones1 == 2414 || $sessiones1 == 2190 || $sessiones1 == 2163) {
                                                                                                                                $query = ControlParams::find()->distinct()
                                                                                                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                        ->joinWith('arboles')
                                                                                                                                        ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                        ->andwhere(['tbl_arbols.arbol_id' => '2016'])
                                                                                                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                            }
                                                                                                                            else
                                                                                                                            {
                                                                                                                                if ($sessiones1 == 2351) {
                                                                                                                                    $query = ControlParams::find()->distinct()
                                                                                                                                            ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                            ->joinWith('arboles')
                                                                                                                                            ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                            ->andwhere(['tbl_arbols.arbol_id' => '2633'])
                                                                                                                                            ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                            //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                                }
                                                                                                                                else
                                                                                                                                {
                                                                                                                                    if ($sessiones1 == 372) {
                                                                                                                                        $query = ControlParams::find()->distinct()
                                                                                                                                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                                ->joinWith('arboles')
                                                                                                                                                ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                                ->andwhere(['tbl_arbols.arbol_id' => '1841'])
                                                                                                                                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                                //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                                    }
                                                                                                                                    else
                                                                                                                                    {
                                                                                                                                        if ($sessiones1 == 476) {
                                                                                                                                            $query = ControlParams::find()->distinct()
                                                                                                                                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                                ->joinWith('arboles')
                                                                                                                                                ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                                ->andwhere(['tbl_arbols.id' => '1453'])
                                                                                                                                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                                //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                                        }
                                                                                                                                        else
                                                                                                                                        {
                                                                                                                                            if ($sessiones1 == 542) {
                                                                                                                                                $query = ControlParams::find()->distinct()
                                                                                                                                                    ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                                    ->joinWith('arboles')
                                                                                                                                                    ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                                    ->andwhere(['tbl_arbols.id' => '18'])
                                                                                                                                                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                                    //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                                            }
                                                                                                                                            else
                                                                                                                                            {
                                                                                                                                                if ($sessiones1 == 473) {
                                                                                                                                                    $query = ControlParams::find()->distinct()
                                                                                                                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                                        ->joinWith('arboles')
                                                                                                                                                        ->where(['tbl_control_params.anulado' => 'null'])
																			>andwhere(['in','tbl_arbols.arbol_id',[676, 2253]])
                                                                                                                                                        //->andwhere(['tbl_arbols.id' => '2253'])
                                                                                                                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                                                }
                                                                                                                                                else
                                                                                                                                                {
                                                                                                                                                    if ($sessiones1 == 196 || $sessiones1 == 105 || $sessiones1 == 1179) {
                                                                                                                                                        $query = ControlParams::find()->distinct()
                                                                                                                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                                        ->joinWith('arboles')
                                                                                                                                                        ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                                        ->andwhere(['tbl_arbols.arbol_id' => '8'])
                                                                                                                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                                                    }
                                                                                                                                                    else
                                                                                                                                                    {
                                                                                                                                                        if ($sessiones1 == 1707) {
                                                                                                                                                            $query = ControlParams::find()->distinct()
                                                                                                                                                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                                        ->joinWith('arboles')
                                                                                                                                                        ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                                        ->andwhere(['tbl_arbols.arbol_id' => '204'])
                                                                                                                                                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                                        //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                                                        }
                                                                                                                                                        else
                                                                                                                                                        {
                                                                                                                                                            $query = ControlParams::find()->distinct()
                                                                                                                                                                ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                                                                                                                                                                ->joinWith('arboles')
                                                                                                                                                                ->where(['tbl_control_params.anulado' => 'null'])
                                                                                                                                                                ->andwhere(['tbl_arbols.arbol_id' => '1'])
                                                                                                                                                                ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                                                                                                                                                                //-> where(['responsable' => Yii::$app->user->identity->id]);
                                                                                                                                                        }
                                                                                                                                                    }
                                                                                                                                                }
                                                                                                                                            }
                                                                                                                                        }
                                                                                                                                    }
                                                                                                                                }
                                                                                                                            }   
                                                                                                                        }                              
                                                                                                                    }
                                                                                                                }
                                                                                                            }
                                                                                                        }
                                                                                                    }
                                                                                                }
                                                                                            }
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                                }
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
             return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_arbols.id' => $this->arbol_id,
        ]);

        $query->andFilterWhere(['like', 'tbl_arbols.id', $this->arbol_id]);

        return $dataProvider;        
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getMetascp($opcion){
        $varMeta = $opcion;
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));
 	// $fechainiC = date('2019-04-01');
        // $fechafinC = date('2019-04-30');
        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year));  


        $querys =  new Query;
        $querys     ->select(['sum(tbl_control_params.cant_valor)'])
                    ->from('tbl_control_params')
                    ->where(['anulado' => 'null'])
                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC])
                    ->andwhere('tbl_control_params.arbol_id = '.$varMeta.'');
                    
        $command = $querys->createCommand();
        $data = $command->queryScalar();  


        return $data;         
    }

    public function getRealizadascp($opcion) {
        $varRealizadas = $opcion;
 	// $fechainiC = date('2019-04-01');
        // $fechafinC = date('2019-04-30');    
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year));                      

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_arbols.name'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
                    ->andwhere('tbl_arbols.id = '.$varRealizadas.'');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();   

        $query = count($queryss);
        return $query; 
    }

    public function getCumplimientocp($opcion){
        $varCumplimiento = $opcion;
 	// $fechainiC = date('2019-04-01');
        // $fechafinC = date('2019-04-30'); 
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year)); 

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_arbols.name'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_arbols',
                            'tbl_ejecucionformularios.arbol_id = tbl_arbols.id')
                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
                    ->andwhere('tbl_arbols.id = '.$varCumplimiento.'');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();
        $query2 = count($queryss);

        $query = new Query;
        $query  ->select(['round(('.$query2.' / (select sum(cant_valor) from tbl_control_params where anulado = 0  and arbol_id = '.$varCumplimiento.' and fechacreacion between "'.$fechainiC.'" and "'.$fechafinC.'")) * 100) as cumplimiento'])
                ->from('tbl_control_params');
        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $value) {
            $data = $value['cumplimiento'];
            return $data;
        }  
    }

    /**
     *
     * @param array $params
     *
     * @return ActiveDataProvider
    */
    public function Obtenerpcrc($arbol_id)
    {        
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year)); 
 	// $fechainiC = date('2019-04-01');
        // $fechafinC = date('2019-04-30');

        $query = ControlParams::find()
                    ->joinWith('arboles')
                    ->where(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC])
                    ->andwhere('tbl_arbols.id ='.$arbol_id);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($arbol_id) && $this->validate())) {
             return $dataProvider;
        }

        return $dataProvider;    
    }

    /**
     *
     * @param array $params
     *
     * @return ActiveDataProvider
    */
    public function getFechaInicial($opcion){
        $txtvarPcrc = $opcion;

        if ($txtvarPcrc == 18) {
            $txtvarBanco = 'Bancolombia';
            $fechainiC = Yii::$app->db->createCommand('select fechainiciotc from tbl_tipocortes where anulado = 0 and tipocortetc like "%'.$txtvarBanco.'%"')->queryScalar();
        }
        else
        {
            $month = date('m');
            $year = date('Y');
            $day = date("d", mktime(0,0,0, $month+1, 0, $year));

            $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        }

        return $fechainiC;
    }  

    /**
     *
     * @param array $params
     *
     * @return ActiveDataProvider
    */
    public function getFechaFinal($opcion){
        $txtvarPcrc = $opcion;

        if ($txtvarPcrc == 18) {
            $txtvarBanco = 'Bancolombia';
            $fechafinC = Yii::$app->db->createCommand('select fechafintc from tbl_tipocortes where anulado = 0 and tipocortetc like "%'.$txtvarBanco.'%"')->queryScalar();
        }
        else
        {
            $month = date('m');
            $year = date('Y');
            $day = date("d", mktime(0,0,0, $month+1, 0, $year));

            $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year)); 
        } 

        return $fechafinC;
    }  


    public function Obtenerdimensiones($params)
    {         
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

        $query = ControlParams::find()->distinct()
                    ->select(['tbl_control_params.dimensions'])                    
                    ->where(['tbl_control_params.anulado' => 'null'])
                    ->where(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);
                    //-> where(['responsable' => Yii::$app->user->identity->id]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($params) && $this->validate())) {
             return $dataProvider;
        }

        $query->andFilterWhere([
            'tbl_control_params.dimensions' => $this->dimensions,
        ]);

        $query->andFilterWhere(['like', 'tbl_control_params.dimensions', $this->dimensions]);

        return $dataProvider;       
    }

    /**
    * @return \yii\db\ActiveQuery
    */
    public function getMetasd($opcion){
        $varMeta = $opcion;

        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year)); 

        $querys =  new Query;
        $querys     ->select(['sum(tbl_control_params.cant_valor) as metas'])
                    ->from('tbl_control_params')
                    ->where(['tbl_control_params.anulado' => 'null']) 
                    ->andwhere('tbl_control_params.dimensions like "%'.$varMeta.'%"') 
                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC])                    
                    ->groupBy('tbl_control_params.dimensions');
                    
        $command = $querys->createCommand();
        $data = $command->queryScalar();  

        return $data;          
    }

    public function getRealizadasd($opcion) {
        $varRealizadas = $opcion;
 	// $fechainiC = date('2019-04-01');
        // $fechafinC = date('2019-04-30'); 
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year));                      

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_dimensions.name'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                            'tbl_ejecucionformularios.dimension_id = tbl_dimensions.id')
                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
                    ->andwhere('tbl_dimensions.name like "%'.$varRealizadas.'%"');
                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();   

        $query = count($queryss);
        return $query; 
    }

    public function getCumplimientod($opcion){
        $varCumplimiento = $opcion;
 	// $fechainiC = date('2019-04-01');
        // $fechafinC = date('2019-04-30'); 
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year)); 

        $querys =  new Query;
        $querys     ->select(['tbl_ejecucionformularios.created', 'tbl_dimensions.name'])->distinct()
                    ->from('tbl_ejecucionformularios')
                    ->join('LEFT OUTER JOIN', 'tbl_dimensions',
                            'tbl_ejecucionformularios.dimension_id = tbl_dimensions.id')
                    ->where(['between','tbl_ejecucionformularios.created', $fechainiC, $fechafinC])
                    ->andwhere('tbl_dimensions.name like "%'.$varCumplimiento.'%"');                    
        $command = $querys->createCommand();
        $queryss = $command->queryAll();
        $query2 = count($queryss);

        $query = new Query;
        $query  ->select(["round(($query2 / (select sum(cant_valor) from tbl_control_params where anulado = 0  and dimensions like '%$varCumplimiento%' and fechacreacion between '$fechainiC' and '$fechafinC')) * 100) as cumplimiento"])
                ->from('tbl_control_params');
        // $query  ->select(['round(('.$query2.' / (select sum(cant_valor) from tbl_control_params where anulado = 0  and dimensions like "%'.$varCumplimiento.'%")) * 100) as cumplimiento'])
        //         ->from('tbl_control_params');                ->from('tbl_control_params');
        $command = $query->createCommand();
        $data = $command->queryAll();

        foreach ($data as $key => $value) {
            $data = $value['cumplimiento'];
            return $data;
        }   
    }

    /**
     *
     * @param array $params
     *
     * @return ActiveDataProvider
    */
    public function Obtenerdimensions($varDimens)
    {        
        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year));

        $query = ControlParams::find()
                    ->where(['anulado' => 'null'])
                    ->andwhere('tbl_control_params.dimensions like "%'.$varDimens.'%"')
                    ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!($this->load($varDimens) && $this->validate())) {
             return $dataProvider;
        }

        return $dataProvider;   
    }


    public function Obtenerpcrcpadre($varAcciones1){
        (int)$txtPcrc = $varAcciones1;


        $month = date('m');
        $year = date('Y');
        $day = date("d", mktime(0,0,0, $month+1, 0, $year));

        $fechainiC = date('Y-m-d', mktime(0,0,0, $month, 1, $year));
        $fechafinC = date('Y-m-d', mktime(0,0,0, $month, $day, $year));  
                    
        $query = ControlParams::find()->distinct()
                        ->select(['tbl_control_params.arbol_id','tbl_arbols.name'])
                        ->joinWith('arboles')
                        ->where(['tbl_control_params.anulado' => 'null'])
                        ->andwhere(['tbl_arbols.arbol_id' => $txtPcrc])
                        ->andwhere(['between','tbl_control_params.fechacreacion', $fechainiC, $fechafinC]);        


            $dataProvider = new ActiveDataProvider([
                'query' => $query,
            ]);

            if (!($this->load($varAcciones1) && $this->validate())) {
                 return $dataProvider;
            }

            $query->andFilterWhere([
                'tbl_arbols.id' => $this->arbol_id,
            ]);

            $query->andFilterWhere(['like', 'tbl_arbols.id', $this->arbol_id]);

            return $dataProvider;  
    }


}
