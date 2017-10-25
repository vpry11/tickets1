<?php
namespace frontend\models;

use yii;
use yii\base\Model;
use yii\web\UploadedFile;
use yii\helpers\FileHelper;

class UploadImage extends Model
{
    /**
     * @var UploadedFile
     */
    const UPLOADPATH  = 'uploads';
    const WEBPATH  = 'web';
    public $imageFile;

    public function rules()
    {
        return [
            [['imageFile'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }
    public function attributeLabels()
    {
        return [
            'imageFile' => 'Выберите файл:',
        ];
    }
    
    public function upload($ticode)
    {
        $uploadpath = Yii::getAlias('@app').DIRECTORY_SEPARATOR.self::WEBPATH.DIRECTORY_SEPARATOR.self::UPLOADPATH.DIRECTORY_SEPARATOR;
        $opdate = date("ymdHis_");

        if ($this->validate()) {
            $this->imageFile->saveAs( $uploadpath . $ticode.'_'.$opdate.$this->imageFile->baseName . '.' . $this->imageFile->extension);
            return true;
        } else {
            return false;
        }
    }
    /**
     * Returns 1-dimensional array of filenames matched given pattern in upload the site directory
     * @param $filepattern, string or array of strings with patterns for filtering
     */
    public static function getUploadedFileList($filepattern){
        
        $uploadpath = Yii::getAlias('@app').DIRECTORY_SEPARATOR.self::WEBPATH.DIRECTORY_SEPARATOR.self::UPLOADPATH.DIRECTORY_SEPARATOR;
        $fileHelper = new FileHelper();

        if(is_string($filepattern) ) $pattern[]=$filepattern;
        else if(is_array($filepattern))$pattern = $filepattern;
        else $pannern = null;

        $files = $fileHelper->findFiles($uploadpath,['only'=>$pattern]);

        //Yii::warning($files,__METHOD__);
        return $files;
    }
}