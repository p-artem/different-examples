<?php
namespace app\components\helpers\packageWidget\models;

use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
/**
 * Модель по работе с данными виджета "Таблица".
 * Class Table
 * @package app\components\helpers\packageWidget\models
 *
 */

class Table extends AbstractModel {

    public $group;

    public function scenarios()
    {
        $scenarios = [
            'create' => ['group'],
            'edit' => ['group'],
        ];
        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            ['group', 'groupValidate']
        ];
        return array_merge_recursive(parent::rules(), $rules);
    }

    public function groupValidate($attributes, $params){
        $groups = $this->$attributes;

        $keyErr = 'w[' . $this->positionW .'][wTable][group]';
        if(!is_array($groups)) {
            $this->addError($keyErr, 'Неверные входные параметры группы');
            return false;
        }

        foreach ($groups as $keyGroup => $group){
            if(!isset($group['groupTitle']) || !$group['groupTitle']){
                $keyErrTitle = $keyErr . '[' . $keyGroup . '][groupTitle]';
                $this->addError($keyErrTitle, 'Заголовок группы обязателен для заполнения');
            }

            $strLenTitle = mb_strlen($group['groupTitle']);
            if($strLenTitle < 3 || $strLenTitle > 255){
                $keyErrTitle  = $keyErr .  '[' . $keyGroup . '][groupTitle]';
                $this->addError($keyErrTitle, 'Длина поля от 3-х до 255 символов');
            }

            if(!isset($group['blocks']) || !is_array($group['blocks']) || !$group['blocks']){
                $keyErrParams = $keyErr .  '[' . $keyGroup . '][blocks]';
                $this->addError($keyErrParams, 'Неверные входные параметры параметров группы');
            }

            $keyErrBlock = $keyErr . '[' . $keyGroup . '][blocks]';
            foreach ($group['blocks'] as $keyBlock => $block){

                if(!isset($block['param']) || !$block['param']){
                    $keyErrBlockTitle = $keyErrBlock . '[' . $keyBlock . '][param]';
                    $this->addError($keyErrBlockTitle, 'Поле обязательно для заполнения');
                }
                $strLenTitle = mb_strlen($block['param']);
                if($block['param'] && $strLenTitle < 3 || $strLenTitle > 255){
                    $keyErrBlockTitle = $keyErrBlock . '[' . $keyBlock . '][param]';
                    $this->addError($keyErrBlockTitle, 'Длина поля от 3-х до 255 символов');
                }

                if(!isset($block['value']) || !is_array($block['value']) || !$block['value']){
                    $keyErrBlockParams = $keyErrBlock . '[' . $keyBlock . '][value]';
                    $this->addError($keyErrBlockParams, 'Неверные входные параметры параметров блока группы');
                }

                $keyErrBlockParams = $keyErrBlock . '[' . $keyBlock . '][value]';

                foreach ($block['value'] as $keyItem => $item){
                    if(!$item){
                        $keyErrItemDesc = $keyErrBlockParams . '[' . $keyItem . ']';
                        $this->addError($keyErrItemDesc, 'Поле обязательно для заполнения');
                    }

                    $strLenDesc = mb_strlen($item);

                    if($item && $strLenDesc < 1 || $strLenDesc > 255){
                        $keyErrItemTitle = $keyErrBlockParams . '[' . $keyItem . ']';
                        $this->addError($keyErrItemTitle, 'Длина поля от 1-го до 255 символов');
                    }
                }
            }

        }
    }

    /*
     * Usage DynamicModel for validation block params
     * @param string $attr the POST attributes for validation
     * @return bool returned result validation model attributes
     */
    public function validateWidgetAttr($attr){
        if(!is_array($this->attributes)) return false;
        $this->attributes = $attr;

        return (!$this->validate()) ? false : true;
    }

    /*
     * @return array $wErrors or default model errors
     */
//    public function getFirstErrors()
//    {
//        return ($this->wErrors) ? $this->wErrors : $this->errors;
//    }

    /*
     * @param array &$uploadFiles the ref with ObjectPack
     * @return json returned result remapped attributes
     */
    public function mapContent(&$uploadFiles)
    {
        return json_encode($this->group, JSON_UNESCAPED_UNICODE);
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