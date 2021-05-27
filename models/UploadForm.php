<?php

namespace app\models;

use yii\base\Model;
use yii\web\UploadedFile;
use Yii;

class UploadForm extends Model
{
    /**
     * @var UploadedFile[]
     */
    public $archivo_adjunto;

    public function rules()
    {
        return [
            [['archivo_adjunto'], 'file', 'skipOnEmpty' => false, 'extensions' => 'PNG, JPG, PDF', 'maxFiles' => 4],
        ];
    }
    
    public function upload()
    {
        if ($this->validate()) { 
            foreach ($this->archivo_adjunto as $file) {
                $user = Yii::$app->user->identity->username;
                $cadena = date("YmdHis") . $user . str_replace(' ', '', $file->name);
                //print_r($cadena); die;

                // try {
                //     $file->saveAs('alertas/' . $cadena . '.' . $file->extension)
                // } catch (\Exception $e) {
                //     ...
                // }
                $file->saveAs('alertas/' . $cadena);
            }
            return true;
        } else {
            return false;
        }
    }
}