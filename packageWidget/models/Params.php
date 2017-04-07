<?php
namespace app\components\helpers\packageWidget\models;

use yii\base\DynamicModel;
use yii\helpers\ArrayHelper;
/**
 * Модель по работе с данными виджета "Параметры".
 * Class Params
 * @package app\components\helpers\packageWidget\models
 *
 * @param array $params
 * @param array $wError
 */

class Params extends AbstractModel {

    public $params;
    public $param;
    public $value;
    private $wErrors;

    public function scenarios()
    {
        $scenarios = [
            'create' => ['params', 'param', 'value'],
            'edit' => ['params', 'param', 'value'],
        ];
        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            [
                ['params'], 'required',
                'message' => 'Параметры обязательны для заполнения',
                'on' => ['create', 'edit']
            ],
            [
                ['param', 'value'], 'paramsValidate', 'on' => ['create', 'edit']
            ],
        ];
        return array_merge_recursive(parent::rules(), $rules);
    }

    public function paramsValidate($attributes, $params){
        foreach ($this->$attributes as $key => $value){
            $this->$attributes[$key] = trim($value);
            $strLen = mb_strlen($value);
            $keyErr = 'w[' . $this->positionW .'][wParams][params][' . $key .'][' . $attributes . ']';
                if(!$value){
                    $this->addError($keyErr, 'Поле обязательно для заполнения');

                }
                if($value && !($strLen >= 1 && $strLen <= 255)){
                    $this->addError($keyErr, 'Длина поля от 1-го до 255 символов');
                }
        }
    }

    /*
     * Usage DynamicModel for validation block params
     * @param string $attr the POST attributes for validation
     * @return bool returned result validation model attributes
     */
    public function validateWidgetAttr($attr){
        $this->attributes = $attr;
        $this->param = ArrayHelper::getColumn($attr['params'], 'param');
        $this->value = ArrayHelper::getColumn($attr['params'], 'value');

//
//        foreach ($this->params as $keyParam => $valParam){
//            $model = DynamicModel::validateData($valParam, [
//                [['param', 'value'], 'filter', 'filter' => 'trim', 'skipOnArray' => true],
//                [
//                    ['param', 'value'], 'required',
//                    'message' => 'Поле обязательно для заполнения',
//                ],
//                [
//                    'param',
//                    'string', 'min' => 2, 'max' => 255,
//                    'tooShort' => 'Поле не может содержать менее 2 символов',
//                    'tooLong'  => 'Поле не может содержать более 255 символов',
//                ],
//                [
//                    'value',
//                    'string', 'min' => 1, 'max' => 255,
//                    'tooShort' => 'Поле не может содержать менее 1 символа',
//                    'tooLong'  => 'Поле не может содержать более 255 символов',
//                ],
//            ]);
//            if ($model->hasErrors()) {
//
//                $error =  $model->firstErrors;
//                $keyErr = 'w[' . $this->positionW .'][wParams][params][' . $keyParam . '][' . key($error) .']';
//                $this->wErrors[$keyErr] = [$error[key($error)]];
//
//                //$this->wErrors['params'][$keyParam] = $model->firstErrors;
//            } else {
//                $this->params[$keyParam] = $valParam;
//            }
//        }

//        if($this->wErrors || !$this->validate()){
//            return false;
//        } else {
//            return true;
//        }
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
        return json_encode($this->params, JSON_UNESCAPED_UNICODE);
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