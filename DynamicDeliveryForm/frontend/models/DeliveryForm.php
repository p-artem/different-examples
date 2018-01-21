<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use common\models\Order;
use frontend\managers\DeliveryManager;
use frontend\dto\DeliveryAttrDTO;
use borales\extensions\phoneInput\PhoneInputValidator;
/**
 * @property string $delivery_id
 * @property string $city_id
 * @property string $department_id
 * @property string $address
 * @property string $recipient_name
 * @property string $recipient_surname
 * @property string $recipient_phone
 * @property string $phone_input
 */
class DeliveryForm extends Model
{
    public $delivery_id;
    public $city_id;
    public $department_id;
    public $address;
    public $recipient_name;
    public $recipient_surname;
    public $recipient_phone;
    public $phone_input;

    /** @var mixed */
    protected $_service;

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['city_id', 'recipient_name', 'recipient_surname', 'phone_input'], 'required', 'when' => function(DeliveryForm $model){
                return in_array($model->delivery_id , [Order::DELIVERY_NOVA_POSHTA,Order::DELIVERY_NOVA_POSHTA_EXPRESS]);
            }],
            [['department_id'], 'required', 'when' => function(DeliveryForm $model){
                return $model->delivery_id == Order::DELIVERY_NOVA_POSHTA;
            }],
            [['address'], 'required', 'when' => function(DeliveryForm $model){
                return $model->delivery_id == Order::DELIVERY_NOVA_POSHTA_EXPRESS;
            }],
            [['recipient_phone', 'phone_input'], PhoneInputValidator::className()],
            [['city_id', 'department_id', 'recipient_name', 'recipient_surname', 'recipient_phone', 'phone_input'], 'string'],
            [['address'], 'filter', 'filter' => function($value) {
                return trim(htmlentities(strip_tags($value), ENT_QUOTES, 'UTF-8'));
            }],
            [['delivery_id'], 'safe'],
        ];
    }

    /**
     * @param $attribute
     * @param bool $forMobile
     * @return DeliveryAttrDTO
     */
    public function getAttrParams($attribute, $forMobile = false){
        $inputId =  Html::getInputId($this, $attribute);
        $formName = strtolower($this->formName());

        $inputId = $forMobile ? $inputId . '-mobile' : $inputId;
        $container = $forMobile
            ? 'field-' . $formName . '-mobile-' . $attribute
            : 'field-' . $formName. '-' . $attribute;
        $inputName = $this->formName() . '['. $attribute .']';
        return new DeliveryAttrDTO($inputId, $container, $inputName);
    }

    /**
     * @param array $attrNames
     * @return array
     */
    public function getAttrValidators($attrNames = [])
    {
        $result = [];
        foreach ($this->attributes as $attribute => $val)
        {
            if(!$attrNames || in_array($attribute, $attrNames)){
                $validators = [];
                foreach ($this->getActiveValidators($attribute) as $validator) {
                    /* @var $validator \yii\validators\Validator */

                    $js = $validator->clientValidateAttribute($this, $attribute, Yii::$app->controller->getView());
                    if ($validator->enableClientValidation && $js != '') {
                        if ($validator->whenClient !== null) {
                            $js = "if (({$validator->whenClient})(attribute, value)) { $js }";
                        }
                        $validators[] = $js;
                    }
                }

                $inputId =  Html::getInputId($this, $attribute);
                $formName = strtolower($this->formName());

                $desktop = [
                    'id'        => $inputId,
                    'name'      => $attribute,
                    'container' => '.field-' . $formName. '-' . $attribute,
                    'input'     => '#' . $inputId,
                    'error'     => '.help-block',
                ];

                $mobile = [
                    'id'        => $inputId . '-mobile',
                    'name'      => $attribute,
                    'container' => '.field-' . $formName . '-mobile-' . $attribute,
                    'input'     => '#' . $inputId. '-mobile',
                    'error'     => '.help-block',
                ];
                $validators = ['validate' => '(function (attribute, value, messages, deferred, $form) { ' . implode(' ', $validators) . ' })'];

                $result['desktop'][] = $desktop + $validators;
                $result['mobile'][] = $mobile + $validators;
            }
        }
        return $result;
    }

    /**
     * @return string
     */
    public function getFullDeliveryAddress(){
        $service = $this->getService();
        $service->cityKey = $this->city_id;
        $service->departmentKey = $this->department_id;

        $cityName = $service->findCity();
        $department = $this->department_id
            ? ', ' . $service->findDepartment()
            : '';
        $address = $this->address
            ? ', ' . $this->address
            : '';

        return implode('',[$cityName, $department, $address]);
    }

    /**
     * @return \frontend\services\delivery\NovaPoshtaService|\frontend\services\delivery\OurService
     */
    public function getService(){
        if(!$this->_service && $this->delivery_id){
            $this->_service = (new DeliveryManager)->getService($this->delivery_id);
        }
        return $this->_service;
    }

    /**
     * @return string
     */
    public function getDataRecipient(){
        return $this->recipient_surname
            ? trim($this->recipient_surname . ' ' . $this->recipient_name)
            : '';
    }

    public function getPhoneNumber(){
        return $this->recipient_phone ?: '';
    }

    /**
     * @return mixed
     */
    public function getDeliveryView(){
        $data = [
            Order::DELIVERY_SELF_IMPOSED        => '_self_imposed',
            Order::DELIVERY_NOVA_POSHTA         => '_nova_poshta',
            Order::DELIVERY_NOVA_POSHTA_EXPRESS => '_nova_poshta_express',
        ];
        return ArrayHelper::getValue($data, $this->delivery_id);
    }

    /**
     * @return string
     */
    public function getJsonValidators(){
        $data = [
            Order::DELIVERY_NOVA_POSHTA         => ['city_id', 'department_id', 'recipient_surname', 'recipient_name', 'recipient_phone', 'phone_input'],
            Order::DELIVERY_NOVA_POSHTA_EXPRESS => ['city_id', 'address', 'recipient_surname', 'recipient_name', 'recipient_phone', 'phone_input'],
        ];
        $result = '';
        if($itemData = ArrayHelper::getValue($data, $this->delivery_id)){
            $result = \yii\helpers\Json::encode($this->getAttrValidators($itemData));
        }
        return $result;
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'city_id'           => Yii::t('site', 'City'),
            'department_id'     => Yii::t('site', 'Department'),
            'address'           => Yii::t('site', 'Address'),
            'recipient_name'    => Yii::t('site', 'Recipient Name'),
            'recipient_surname' => Yii::t('site', 'Recipient Surname'),
            'recipient_phone'   => Yii::t('site', 'Recipient Phone'),
            'phone_input'       => Yii::t('site', 'Recipient Phone'),
        ];
    }
}