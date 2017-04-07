<?php
namespace app\components\helpers\packageWidget\models;

use yii\web\UploadedFile;
/**
 * Модель по работе с данными виджета "Главное фото".
 * Class Image
 * @package app\components\helpers\packageWidget\models
 *
 * @param array $image instances of UploadedFile
 * @param array $oldImage
 */

class Image extends AbstractModel {

    public $image;
    public $oldImage;

    public function scenarios()
    {
        $scenarios = [
            'create' => ['image', 'oldImage'],
            'edit' => ['image', 'oldImage'],
        ];
        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            ['oldImage', 'default', 'value' => []],
            [
                'oldImage', 'filterOldFiles',
                'on' => 'edit'
            ],
            [
                'image', 'filter',
                'filter' => function($value){ return ($value) ? $value[0] : []; },
                'on' => ['create', 'edit']
            ],
            [
                'image', 'required',
                'message' => 'Пожалуста, загрузите фотографию',
                'when' => function($model){ return (empty($model->image) && empty($model->oldImage)) ? true :false; },
                'on' => ['create', 'edit']
            ],
            [
                'image',
                'file', 'maxSize' => 1048576, 'mimeTypes' => ['image/jpeg', 'image/png'],
                'message'       => 'Ошибка загрузки файла',
                'tooBig'        => 'Размер фото должен быть до 1Мб',
                'wrongMimeType' => 'Неверный формат файла',
                'on' => ['create', 'edit']
            ],
        ];
        return array_merge_recursive(parent::rules(), $rules);
    }

    /*
     * @param string $attr the POST attributes for validation
     * @return bool returned result validation model attributes
     */
    public function validateWidgetAttr($attr){
        $this->oldImage = [];
        $this->attributes = $attr;
        $this->image = UploadedFile::getInstancesByName('w[' . $this->positionW. '][wImage]');
        if (!$this->validate()) {
            return false;
        } else {
            return true;
        }
    }

    /*
    * @param array &$uploadFiles the ref with ObjectPack
    * @return string returned result remapped attributes
    */
    public function mapContent(&$uploadFiles){
        $data = $this->prepareImages([$this->image], $this->oldImage);
        $uploadFiles = array_merge($uploadFiles, $data['fileObj']);
        return $data['images'][0];
    }

    /*
     * @param string $data the widget content
     * @return array returned converted content for widget
     */
    public static function convertContent($data){
        $content = [];
        $pathToFile =  \Yii::getAlias('@webroot') .  $data['filePath'] . '/' . $data['content'];
        if(file_exists($pathToFile)){
            $url = $data['filePath'] . '/' . $data['content'];
            $content = ['url' => $url, 'imageName' => $data['content']];
        }
        return $content;
    }
}