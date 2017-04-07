<?php
namespace app\components\helpers\packageWidget\models;

/**
 * Модель по работе с данными виджета "График".
 * Class Graph
 * @package app\components\helpers\packageWidget\models
 *
 * @param int $graphCount
 * @param array $unitX
 * @param array $unitY
 * @param array $graphName
 * @param array $axisX
 * @param array $axisY
 */

class Graph extends AbstractModel {

    public $graphCount;
    public $unitX;
    public $unitY;
    public $graphName;
    public $axisX;
    public $axisY;

    public function scenarios()
    {
        $scenarios = [
            'create' => ['graphCount', 'unitX', 'unitY', 'graphName', 'axisX', 'axisY'],
            'edit' => ['graphCount', 'unitX', 'unitY', 'graphName', 'axisX', 'axisY'],
        ];
        return array_merge_recursive(parent::scenarios(), $scenarios);
    }

    public function rules()
    {
        $rules = [
            [
                ['graphCount', 'graphName', 'axisX', 'axisY'], 'required',
                'message' => 'Поле обязательно для заполнения',
                'on' => ['create', 'edit']
            ],
            [
                ['graphCount'], 'integer',
                'message' => 'Только цифры',
                'on' => ['create', 'edit']
            ],
            [
                ['graphName'],
                    'each', 'rule' => [
                        'string', 'min' => 3, 'max' => 50,
                        'tooShort' => 'Поле не может содержать менее 3 символов',
                        'tooLong'  => 'Поле не может содержать более 50 символов'
                    ],
                'on' => ['create', 'edit']
            ],
            [
                ['axisX'],
                'each', 'rule' => [
                        'string', 'min' => 1, 'max' => 50,
                        'tooShort' => 'Поле не может содержать менее 1 символов',
                        'tooLong'  => 'Поле не может содержать более 50 символов'
                    ],
                'on' => ['create', 'edit']
            ],
            [
                ['axisX'],
                function($attributes, $params){
                    foreach ($this->$attributes as $key => $item){
                        if(!$item){
                            $this->addError($attributes, [$key => 'Поле обязательно для заполнения']);
                        }
                    }
                },
                'on' => ['create', 'edit']
            ],
            ['axisY', 'customValidation', 'on' => ['create', 'edit']],
            [
                'axisY', function($attributes, $params){
                $this->$attributes = array_combine($this->graphName, $this->$attributes);
                    if(count($this->$attributes) != $this->graphCount){
                        $this->addError($attributes,  'Названия графиков должны быть разные');
                    }
                },
                'on' => ['create', 'edit']
            ],
        ];

        return array_merge_recursive(parent::rules(), $rules);
    }

    /*
    * Validation and change array attributes (param, value)
    * @param mixed $attributes the model attributes
    * @param mixed $params the model params
    */
    public function customValidation($attributes, $params){
        foreach ($this->$attributes as $keyAttr => &$valAttr)
        {
            $valAttr = (!is_array($valAttr)) ? [$valAttr] : $valAttr;
            array_filter($valAttr, function ($elem, $elemKey) use(&$valAttr, $keyAttr, $attributes){
                if(!$elem = (float) str_replace(',', '.', $elem)){
                    $this->addError($attributes, [$keyAttr => [$elemKey => 'Только числа']]);
                    return false;
                } else {
                    $valAttr[$elemKey] = $elem;
                }
            }, ARRAY_FILTER_USE_BOTH);
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

    /*
     * @param array &$uploadFiles the ref with ObjectPack
     * @return json returned result remapped attributes
     */
    public function mapContent(&$uploadFiles)
    {
        $attr = $this->attributes;
        unset($attr['title']);
        return json_encode($attr, JSON_UNESCAPED_UNICODE);
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