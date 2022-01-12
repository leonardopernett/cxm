<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_usuarios".
 *
 * @property integer $usua_id
 * @property string $usua_usuario
 * @property string $usua_nombre
 * @property string $usua_email
 * @property string $usua_identificacion
 * @property string $usua_activo
 * @property string $usua_estado
 * @property string $usua_fechhoratimeout
 * @property string $fechacreacion
 *
 * @property TblArbolsEvaluadores[] $tblArbolsEvaluadores
 * @property TblArbolsUsuarios[] $tblArbolsUsuarios
 * @property TblEquipos[] $tblEquipos
 * @property TblPlanmonitoreos[] $tblPlanmonitoreos
 * @property TblPreferencias[] $tblPreferencias
 */
class Usuarios extends \yii\db\ActiveRecord {

    public $rol;
    public $grupo;
    public $nombregrupo;
    /**
     * @inheritdoc
     */
    public static function tableName() {
        return 'tbl_usuarios';
    }

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['usua_activo'], 'string'],
            [['usua_usuario'], 'unique'],
            [['usua_usuario'], 'string', 'max' => 100],
            [['usua_usuario', 'usua_nombre', 'rol', 'usua_email'], 'required'],
            [['usua_nombre', 'usua_email'], 'string', 'min' => 2, 'max' => 100],
            [['usua_identificacion'], 'string', 'max' => 30],
            [['usua_usuario'], 'match', 'not' => true, 'pattern' => '/[^a-zA-Z0-9\s.()_-]/'],
            [['usua_email'], 'match', 'pattern' => '/[\w-\.]{2,}@([\w-]{2,}\.)*([\w-]{2,}\.)[\w-]{2,4}/'],
            [['grupo'],'integer'],
            [['fechacreacion'], 'safe'],
            ['usua_nombre','filter', 'filter' => function($value){
               return filter_var($value,FILTER_SANITIZE_STRING) ;
            } ],
            ['usua_email','filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_EMAIL) ;
             } ], 
            ['usua_identificacion','filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_NUMBER_FLOAT) ;
             } ],
             ['usua_usuario','filter', 'filter' => function($value){
                return filter_var($value,FILTER_SANITIZE_STRING) ;
             } ]
            
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels() {
        return [
            'usua_id' => Yii::t('app', 'Usua ID'),
            'usua_usuario' => Yii::t('app', 'Usua Usuario'),
            'usua_nombre' => Yii::t('app', 'Usua Nombre'),
            'usua_email' => Yii::t('app', 'Usua Email'),
            'usua_identificacion' => Yii::t('app', 'Usua Identificacion'),
            'usua_activo' => Yii::t('app', 'Usua Activo'),
            'usua_estado' => Yii::t('app', 'Usua Estado'),
            'usua_fechhoratimeout' => Yii::t('app', 'Usua Fechhoratimeout'),
	    'fechacreacion' => Yii::t('app', ''),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbolsEvaluadores() {
        return $this->hasMany(ArbolsEvaluadores::className(), ['evaluador_id' => 'usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getArbolsUsuarios() {
        return $this->hasMany(ArbolsUsuarios::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEquipos() {
        return $this->hasMany(Equipos::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlanmonitoreos() {
        return $this->hasMany(Planmonitoreos::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPreferencias() {
        return $this->hasMany(Preferencias::className(), ['usua_id' => 'usua_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelUsuariosRoles() {
        return $this->hasOne(RelUsuariosRoles::className(), ['rel_usua_id' => 'usua_id']);
    }

    /**
     * Metodo que retorna el listado de roles
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getRolesList() {
        return ArrayHelper::map(Roles::find()->orderBy('role_descripcion')
                                ->asArray()->all(), 'role_id', 'role_descripcion');
    }

    /**
     * 23/02/2016 -> Funcion que permite llevar un log o registro de los datos modificados
     * @param type $insert
     * @return boolean
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     */
    public function beforeSave($insert) {
        if (parent::beforeSave($insert)) {
            if ($insert == false) {
                $modelLog = new Logeventsadmin();
                $modelLog->datos_nuevos = print_r($this->attributes, true);
                $modelLog->datos_ant = print_r($this->oldAttributes, true);
                $modelLog->fecha_modificacion = date("Y-m-d H:i:s");
                $modelLog->usuario_modificacion = Yii::$app->user->identity->username;
                $modelLog->id_usuario_modificacion = Yii::$app->user->identity->id;
                $modelLog->tabla_modificada = $this->tableName();
                $modelLog->save();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * Funcion que genera el excel con los datos seleccionados en los filtros que tiene la vista
     *  index de usuarios
     * @author Sebastian Orozco <sebastian.orozco@ingeneo.com.co>
     * @param type $models
     * @return boolean
     */
    public function generarReporteUsuarios($models = null) {
        set_time_limit(0);
        $objPHPexcel = new \PHPExcel();
        $objPHPexcel->setActiveSheetIndex(0);        
        $titulos = [
            'usua_id' => 'ID',
            'usua_usuario' => 'Id de Usuario',
            'usua_nombre' => 'Nombres y Apellidos',
            'usua_identificacion' => 'Identificacion',
            'usua_activo' => 'Activo',
            'role_id' => 'Rol ID',
            'role_nombre' => 'Nombre Rol',
            'role_descripcion' => 'Descripción Rol',
        ];  
        $column = 'A';
        $row = 2;
        try {
            foreach ($titulos as $titulo) {
                $objPHPexcel->getActiveSheet()->setCellValue($column . '1', $titulo);
                $column++;
            }

            for ($index = 0; $index < count($models); $index++) {
                $column = 'A';                
                $model = $models[$index];
                unset($model['usua_email']);
                unset($model['usua_estado']);
                unset($model['usua_fechhoratimeout']);
                foreach ($model as $value) {
                    $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value);
                    $column++;
                }
                $row++;
            }
            $self = Url::to(['usuarios/index']);
            header("refresh:1; url='$self'");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Reporte_Usuarios.xlsx"');
            header('Cache-Control: max-age=1');

            $objWriter = new \PHPExcel_Writer_Excel2007();
            $objWriter->setPHPExcel($objPHPexcel);
            $objWriter->save('php://output');
            Yii::$app->session->setFlash('success', Yii::t('app', 'Reporte generado exitosamente'));
            return true;
        } catch (Exception $exc) {
            return false;
        }
        return false;
    }
    
    /**
     * Metodo que retorna el listado de roles
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getGruposusuariosList() {
        return ArrayHelper::map(Gruposusuarios::find()->orderBy('grupo_descripcion')
                                ->asArray()->all(), 'grupos_id', 'grupo_descripcion');
    }

    //Automatizacion equipos Teo
    public function getUserByIdentify($identity) 
    {
        $model = Usuarios::find()->Select(["usua_usuario", "usua_id"])
        ->where('usua_identificacion = :identity', [':identity' => $identity])
        ->one();
        return $model;
    }
}
