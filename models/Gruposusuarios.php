<?php

namespace app\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\helpers\Url;
use yii\base\Exception;

/**
 * This is the model class for table "tbl_grupos_usuarios".
 *
 * @property integer $grupos_id
 * @property string $nombre_grupo
 * @property string $grupo_descripcion
 * @property integer $per_realizar_valoracion
 * @property integer $usua_id_responsable`
 *
 * @property TblUsuarios $usua
 */
class GruposUsuarios extends \yii\db\ActiveRecord
{
    public $usuario;
    public $usuarioname;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'tbl_grupos_usuarios';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['nombre_grupo'], 'required'],
            [['per_realizar_valoracion', 'usua_id_responsable'], 'integer'],
            [['nombre_grupo', 'grupo_descripcion'], 'string', 'max' => 300]
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'grupos_id' => Yii::t('app', 'Grupos ID'),
            'nombre_grupo' => Yii::t('app', 'Nombre Grupo'),
            'grupo_descripcion' => Yii::t('app', 'Grupo Descripcion'),
            'per_realizar_valoracion' => Yii::t('app', 'Per Realizar Valoracion'),
            'usua_id_responsable`' => Yii::t('app', 'Usua ID'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUsua()
    {
        return $this->hasOne(Usuarios::className(), ['usua_id' => 'usua_id_responsable`']);
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRelGruposUsuarios()
    {
        return $this->hasMany(RelGruposUsuarios::className(), ['grupo_id' => 'grupos_id']);
    }
    
    /**
     * Metodo que retorna el listado de los responsables
     * 
     * @return array
     * @author Alexander Arcila <alexander.arcila@ingeneo.com.co>
     * @copyright 2015 INGENEO S.A.S.
     * @license http://www.opensource.org/licenses/bsd-license.php  BSD License
     * @version Release: $Id$
     */
    public function getResponsableList() {
        return ArrayHelper::map(Usuarios::find()->select(['usua_id', 'nombre' => 'concat(usua_nombre, \' - \', usua_usuario)'])->orderBy('usua_nombre')->asArray()->all(), 'usua_id', 'nombre');
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
    public function generarReporteUsuariosgrupos($models = null) {
        set_time_limit(0);
        $objPHPexcel = new \PHPExcel();
        $objPHPexcel->setActiveSheetIndex(0);        
        $titulos = [
            'usua_id' => 'ID',
            'usua_usuario' => 'Id de Usuario',
            'usua_nombre' => 'Nombres y Apellidos',
            'usua_email' => 'Email',
            'usua_identificacion' => 'Identificacion',
            'usua_activo' => 'Activo',
            //'role_id' => 'Rol ID',
            'role_nombre' => 'Nombre Rol',
            'role_descripcion' => 'Descripción Rol',
            'nombre_grupo'=>'Nombre del grupo',
            'grupo_descripcion'=>'Descripción grupo',
            'per_realizar_valoracion'=>'Realizar valoración'
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
                unset($model['usua_estado']);
                unset($model['usua_fechhoratimeout']);
                foreach ($model as $key=>$value) {
                    if ($key=='per_realizar_valoracion') {
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, ($value==1)?'Si':'No');  
                    }else{
                        $objPHPexcel->getActiveSheet()->setCellValue($column . '' . $row, $value);   
                    }
                    $column++;
                }
                $row++;
            }
            $self = Url::to(['gruposusuarios/index']);
            header("refresh:1; url='$self'");
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Reporte_GruposUsuarios.xlsx"');
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
}
