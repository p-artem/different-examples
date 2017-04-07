<?php
namespace app\components\helpers\packageWidget\models;
use yii\helpers\Html;

/**
 * Модель по работе с данными виджета "Текст".
 * Class Text
 * @package app\components\helpers\packageWidget\models
 *
 * @param string $text
 */
class Text extends AbstractModel {

    public $text;

    public function scenarios()
    {
        $scenarios = [
            'create' => ['text'],
            'edit' => ['text'],
        ];
        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            [
                'text', 'required',
                'message' => 'Поле обязаельно для заполнения',
                'on' => ['create', 'edit']
            ],
            [
                'text', 'filter',
                'filter' => function($value){ return trim(Html::encode($this->text)); },
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
        $this->attributes = $attr;
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
    public function mapContent(&$uploadFiles)
    {
        return $this->text;
    }

    /*
     * @param json $data the widget content
     * @return array returned converted content for widget
     */
    public static function convertContent($data){
        return Html::decode($data['content']);
    }
}