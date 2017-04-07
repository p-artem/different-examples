<?php
namespace app\components\helpers\packageWidget\models;

use yii\web\UploadedFile;
/**
 * Модель по работе с данными виджета "Сравнивание фотографий".
 * Class ImageCompare
 * @package app\components\helpers\packageWidget\models
 *
 * @param array $images instances of UploadedFile
 * @param array $oldImages
 * @param string $position
 */

class ImageCompare extends AbstractModel {

    public $images;
    public $oldImages;
    public $position;

    const POSITION_V = 'V';
    const POSITION_G = 'G';

    public function scenarios()
    {
        $scenarios = [
            'create' => ['position', 'images', 'oldImages'],
            'edit' => ['position', 'images', 'oldImages'],
        ];
        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            ['oldImages', 'default', 'value' => []],
            [
                'position', 'filter',
                'filter' => 'trim', 'on' => ['create', 'edit']
            ],
            [
                'position', 'required',
                'message' => 'Параметр позиции обязателен',
                'on' => ['create', 'edit']
            ],
            [
                'oldImages', 'filterOldFiles',
                'on' => 'edit'
            ],
            [
                'images', 'required',
                'message' => 'Пожалуста, загрузите фотографии',
                'when' => function($model){ return (empty($model->images) && empty($model->oldImages)); },
                'on' => ['create', 'edit']
            ],
            [
                ['images'],
                function($attributes, $params){
                    if(count($this->$attributes) + count($this->oldImages) !== 2){
                        $this->addError($attributes, 'Должно быть загружено 2 фотографии');
                    }
                },
                'on' => ['create', 'edit']
            ],
            [
                'position', 'in',
                'range' => [self::POSITION_V, self::POSITION_G],
                'message' => 'Неверный параметр позиции',
                'on' => ['create', 'edit']
            ],
            [
                'images',
                'file', 'maxSize' => 1048576, 'mimeTypes' => ['image/jpeg', 'image/png'],
                'message'       => 'Ошибка загрузки файла',
                'tooBig'        => 'Размер фото должен быть до 1Мб',
                'wrongMimeType' => 'Неверный формат файла',
                'maxFiles' => 2,
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
        $this->oldImages = [];
        $this->attributes = $attr;
        $this->images = UploadedFile::getInstancesByName('w[' . $this->positionW. '][wImageCompare]');
        if (!$this->validate()) {
            return false;
        } else {
            return true;
        }
    }

    /*
     * @param array &$uploadFiles the ref with ObjectPack
     * @return json returned result remapped attributes
     */
    public function mapContent(&$uploadFiles){
        $data = $this->prepareImages($this->images, $this->oldImages);
        $uploadFiles = array_merge($uploadFiles, $data['fileObj']);
        $revertData = ['images' => $data['images'], 'position' => $this->position];
        return json_encode($revertData, JSON_UNESCAPED_UNICODE);
    }

    /*
     * @param json $data the widget content
     * @return array returned converted content for widget
     */
    public static function convertContent($data){

        $parseData = json_decode($data['content'], JSON_UNESCAPED_UNICODE);
        foreach ($parseData['images'] as &$image){
            $pathToFile = \Yii::getAlias('@webroot') .  $data['filePath'] . '/' . $image;
            if(file_exists($pathToFile)){
                $url = $data['filePath'] . '/' . $image;
            } else {
                $url = '';
            }
            $image = ['url' => $url, 'imageName' => $image];
        }
        return $parseData;
    }
}