<?php
 
namespace app\models;
use yii\base\Model;
 
class FormUploadtigo extends Model{
  
    public $file;
     
    public function rules()
    {
        return [
            ['file', 'file', 
   'skipOnEmpty' => false,
   'uploadRequired' => 'No has seleccionado ningún archivo', //Error
   'extensions' => 'xlsx',
   'wrongExtension' => 'El archivo {file} no contiene una extensión permitida {extensions}', //Error
   'maxFiles' => 4,
   'tooMany' => 'El máximo de archivos permitidos son {limit}', //Error
   ],
        ]; 
    } 
 
    public function attributeLabels()
    {
      return [
      'file' => 'Seleccionar archivos:',
      ];
    }

}