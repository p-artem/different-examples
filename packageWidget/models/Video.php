<?php
namespace app\components\helpers\packageWidget\models;

/**
 * Модель по работе с данными виджета "Видео".
 * Class Video
 * @package app\components\helpers\packageWidget\models
 *
 * @param string $url
 */

class Video extends AbstractModel {

    public $url;

    public function scenarios()
    {
        $scenarios = [
            'create' => ['url'],
            'edit' => ['url'],
        ];

        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            [
                'url', 'required',
                'message' => 'Поле обязаельно для заполнения',
                'on' => ['create', 'edit']
            ],
//            [
//                'url',
//                'each', 'rule' => [
//                    'match', 'pattern' => '/^[a-zA-Z0-9\_]+$/',
//                    'message' => 'Только буквы латинского алфавита и цифры',
//                ],
//                'on' => ['create', 'edit']
//
//            ],
            [
                ['url'], 'urlValidate', 'on' => ['create', 'edit']
            ],
//            [
//                'url',
//                'string', 'min' => 2, 'max' => 255,
//                'tooShort' => 'Поле не может содержать менее 2 символов',
//                'tooLong'  => 'Поле не может содержать более 255 символов',
//                'on' => ['create', 'edit']
//            ],
        ];
        return array_merge_recursive(parent::rules(), $rules);
    }

    public function urlValidate($attributes, $params){

        if(!$this->$attributes && !is_array($this->$attributes)){
            $this->addError($attributes, 'Виджет не может быть пустым');
            return false;
        }

        foreach ($this->$attributes as $key => $value){
            $this->$attributes[$key] = trim($value);
            $strLen = mb_strlen($value);
            $keyErr = 'w[' . $this->positionW .'][wVideo][url][' . $key .']';
            if(!$value){
                $this->addError($keyErr, 'Поле обязательно для заполнения');
            }

            if(!preg_match('/^[a-zA-Z0-9\_\-]+$/', $value)){
                $this->addError($keyErr, 'Только буквы латинского алфавита и цифры');
            }

            if($value && !($strLen >= 2 && $strLen <= 255)){
                $this->addError($keyErr, 'Длина поля от 2-го до 255 символов');
            }
        }
    }

    /*
     * @param string $attr the POST attributes for validation
     * @return bool returned result validation model attributes
     */
    public function validateWidgetAttr($attr){
        $this->attributes = $attr;

        if (!$this->validate()) {
            return false;
        } else {
            return true;
        }
    }

    /**
     * Returns the value for the specified attribute.
     * @param string $names the attribute name
     * @param array $except
     * @return string the attribute value
     */
    public function getAttributes($names = null, $except = [])
    {
        return $this->url;
    }

    /*
    * @param array &$uploadFiles the ref with ObjectPack
    * @return json returned result remapped attributes
    */
//    public function mapContent(&$uploadFiles)
//    {
//        return $this->url;
//        // TODO: Implement mapContent() method.
//    }

    /*
     * @param json $data the widget content
     * @return array returned converted content for widget
     */
//    public static function convertContent($data){
//        return $data['content'];
//    }

    /*
     * @param array &$uploadFiles the ref with ObjectPack
     * @return json returned result remapped attributes
     */
    public function mapContent(&$uploadFiles)
    {
        return json_encode($this->url, JSON_UNESCAPED_UNICODE);
        // TODO: Implement mapContent() method.
    }

    /*
     * @param json $data the widget content
     * @return array returned converted content for widget
     */
    public static function convertContent($data){
        return json_decode($data['content'], JSON_UNESCAPED_UNICODE);
    }
}