<?php

namespace common\models;

use common\commands\SendEmailCommand;
use Yii;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use yii\db\ActiveQuery;
use common\models\query\OrderQuery;
use yii\helpers\ArrayHelper;
use yii\validators\EmailValidator;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $payment_id
 * @property integer $delivery_id
 * @property integer $promo_id
 * @property integer $fast
 * @property string $recipient
 * @property string $recipient_phone
 * @property string $declaration
 * @property string $destination
 * @property float $sale
 * @property float $promo_sale
 * @property string $text
 * @property string $notes
 * @property integer $status
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property float $sum
 * @property string $statusName
 *
 * @property User $user
 * @property OrderHistory[] $orderHistories
 * @property OrderProduct[] $orderProducts
 * @property Product[] $products
 * @property Promo $promo
 */
class Order extends ActiveRecord
{
    const STATUS_PROCESS = 1;
    const STATUS_PAYMENT = 2;
    const STATUS_SHIPMENT = 3;
    const STATUS_CANCELED = 4;
    const STATUS_SEND = 5;
    const STATUS_DELIVERED = 6;
    const STATUS_RETURN = 7;
    const STATUS_RECIEVED = 8;

    const FAST = 1;

    const PAYMENT_TO_BANK_CARD = 1;
    const PAYMENT_RECEIPT_OF_ORDER = 2;

    const DELIVERY_SELF_IMPOSED = 1;
    const DELIVERY_NOVA_POSHTA = 2;
    const DELIVERY_INTIME = 3;
    const DELIVERY_NOVA_POSHTA_EXPRESS = 4;

    /**
     * Return order statuses list
     * @return array
     */
    public static function statuses()
    {
        return [
            self::STATUS_PROCESS => Yii::t('site', 'In process'),
            self::STATUS_PAYMENT => Yii::t('site', 'Waiting of payment'),
            self::STATUS_SHIPMENT => Yii::t('site', 'Waiting of shipment'),
            self::STATUS_CANCELED => Yii::t('site', 'Canceled'),
            self::STATUS_SEND => Yii::t('site', 'Sent'),
            self::STATUS_DELIVERED => Yii::t('site', 'Delivered'),
            self::STATUS_RETURN => Yii::t('site', 'Return'),
            self::STATUS_RECIEVED => Yii::t('site', 'Received'),
        ];
    }

    /**
     * Return status
     * @param integer $status
     * @return string
     */
    public static function getStatus($status)
    {
        $statuses = static::statuses();
        return isset($statuses[$status]) ? $statuses[$status] : '';
    }

    /**
     * @return array
     */
    public static function getPaymentMethods()
    {
        return [
            self::PAYMENT_TO_BANK_CARD     => Yii::t('site', 'Transfer of funds to the card'),
            self::PAYMENT_RECEIPT_OF_ORDER => Yii::t('site', 'When you receive an order'),
        ];
    }

    /**
     * @return mixed
     */
    public function getPaymentMethod(){
        return ArrayHelper::getValue(static::getPaymentMethods(), $this->payment_id);
    }

    /**
     * @return array
     */
    public static function getDeliveryMethods(){
        return [
            self::DELIVERY_SELF_IMPOSED        => Yii::t('site', 'Self-imposed'),
            self::DELIVERY_NOVA_POSHTA         => Yii::t('site', 'Nova Poshta'),
            self::DELIVERY_NOVA_POSHTA_EXPRESS => Yii::t('site', 'Nova Poshta (express delivery)'),
            self::DELIVERY_INTIME              => Yii::t('site', 'Intime'),
        ];
    }

    /**
     *
     */
    public static function getActiveDeliveryMethods(){
        return [
            self::DELIVERY_SELF_IMPOSED        => Yii::t('site', 'Self-imposed'),
            self::DELIVERY_NOVA_POSHTA         => Yii::t('site', 'Nova Poshta'),
            self::DELIVERY_NOVA_POSHTA_EXPRESS => Yii::t('site', 'Nova Poshta (express delivery)'),
//            self::DELIVERY_INTIME              => Yii::t('site', 'Intime'),
        ];
    }

    /**
     * @return mixed
     */
    public function getDeliveryMethod(){
        return ArrayHelper::getValue(static::getDeliveryMethods(), $this->delivery_id);
    }

    /**
     * Return status
     * @return string
     */
    public function getStatusName()
    {
        $statuses = static::statuses();
        return $statuses[$this->status];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    public function behaviors()
    {
        return [
            TimestampBehavior::className()
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['text', 'notes', 'declaration', 'recipient', 'recipient_phone', 'destination'], 'trim'],
            [['delivery_id', 'payment_id', 'user_id', 'status', 'created_at', 'updated_at', 'fast'], 'integer'],
            [['text', 'notes'], 'string'],
            [['declaration', 'recipient', 'recipient_phone'], 'string', 'max' => 255],
            [['destination'], 'string', 'max' => 512],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['user_id' => 'id']],
            [['sale', 'promo_sale', 'fast'], 'default', 'value' => 0],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id'              => Yii::t('site', 'Order number'),
            'user_id'         => Yii::t('site', 'Buyer'),
            'delivery_id'     => Yii::t('site', 'Delivery'),
            'payment_id'      => Yii::t('site', 'Payment method'),
            'promo_id'        => Yii::t('site', 'Promo'),
            'recipient'       => Yii::t('site', 'Recipient'),
            'recipient_phone' => Yii::t('site', 'Recipient phone'),
            'declaration'     => Yii::t('site', 'Declaration'),
            'destination'     => Yii::t('site', 'Destination'),
            'sale'            => Yii::t('site', 'Sale'),
            'promo_sale'      => Yii::t('site', 'Promo'),
            'text'            => Yii::t('site', 'Comments to the order'),
            'notes'           => Yii::t('site', 'Notes'),
            'status'          => Yii::t('site', 'Status'),
            'created_at'      => Yii::t('site', 'Order date'),
            'updated_at'      => Yii::t('site', 'Updated date'),
            'user_phone'      => Yii::t('site', 'Phone'),
            'user_email'      => Yii::t('site', 'E-mail'),
            'fast'            => Yii::t('backend', 'Fast'),
        ];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id'])->inverseOf('orders');
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderHistory()
    {
        return $this->hasMany(OrderHistory::className(), ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * @return ActiveQuery
     */
    public function getOrderProducts()
    {
        return $this->hasMany(OrderProduct::className(), ['order_id' => 'id'])->inverseOf('order');
    }

    /**
     * @return ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasMany(Product::className(), ['id' => 'product_id'])->viaTable('{{%order_product}}', ['order_id' => 'id']);
    }

    /**
     * @return ActiveQuery
     */
    public function getPromo()
    {
        return $this->hasOne(Promo::className(), ['id' => 'promo_id'])->inverseOf('orders');
    }

    /**
     * @inheritdoc
     * @return OrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new OrderQuery(get_called_class());
    }

    /**
     * @return null
     */
    public function sendOrderEmails()
    {
        $validator = new EmailValidator();
        if ($validator->validate($this->user->email)) {
            $this->sendEmail($this->user->email, Yii::t('site', 'Order success'), 'order_buyer');
        }
        $this->sendEmail(env('STORE_EMAIL'), Yii::t('site', 'New order'), 'order_buyer');
        return null;
    }

    /**
     * @param $to
     * @param $subject
     * @param $view
     * @return mixed
     */
    public function sendEmail($to, $subject, $view = '')
    {
        if (!$view) $view = '../../frontend/mail/order_buyer';
        $result = Yii::$app->commandBus->handle(new SendEmailCommand([
            'nameComponent' => 'mailerSale',
            'subject' => $subject,
            'view' => $view,
            'from' => env('STORE_EMAIL'),
            'to' => $to,
            'params' => [
                'order' => $this
            ]
        ]));
        return $result;
    }

    /**
     * @return float
     */
    public function getSum()
    {
        $sum = 0;
        foreach ($this->orderProducts as $product){
            $sum += $product->price * $product->quantity;
        }
        return $sum;
    }

    /**
     * @return mixed
     */
    public function getTotalSum()
    {
        return $this->getSum() - $this->sale - $this->promo_sale;
    }

    /**
     * @return bool
     */
    public function sms()
    {
        $card = '5457 0923 0334 7284';
        $url = 'http://atompark.com/api/sms/3.0/sendSMS';
        $openKey = 'f918062730acf63bb1eb33ee0448e79b';
        $privateKey = '5e997431d80e6148bb55240ce29ac168';
        $sender = 'macuser.ua';
        $datetime = '';
        $sms_lifetime = '0';

        $user = $this->user;
        $order = $this;

        // check phone
        $phone = '';
        for ($i = 0; $i < strlen($user->phone); $i++) {
            if ($i == 0 && $user->phone{$i} == '+') {
                $phone = '+';
            }
            if (is_numeric($user->phone{$i})) {
                $phone .= $user->phone{$i};
            }
        }
        if ((strlen($phone) == 12 && $phone{0} == '3') || (strlen($phone) == 13 && $phone{0} == '+')) {
            $phone = '' . $phone;
        } elseif (strlen($phone) == 11) {
            $phone = '+3' . $phone;
        } elseif (strlen($phone) == 10) {
            $phone = '+38' . $phone;
        } elseif (strlen($phone) == 9) {
            $phone = '+380' . $phone;
        } else {
            return false;
        }

        $text = 'Ваш заказ ' . $order->id . ' получен. Ожидайте звонка оператора.';

        $data = array(
            'version'      => '3.0',
            'action'       => 'sendSMS',
            'key'          => $openKey,
            'sender'       => $sender,
            'text'         => $text,
            'phone'        => $phone,
            'datetime'     => $datetime,
            'sms_lifetime' => $sms_lifetime
        );

        ksort($data);
        $sum = '';
        foreach ($data as $k => $v) {
            $sum .= $v;
        }
        $sum .= $privateKey; // your private key
        $control_sum = md5($sum);
        $data['sum'] = $control_sum.'dgfhjfghj';

        $options = array(
            'http' => array(
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => http_build_query($data),
            ),
        );

        $context = stream_context_create($options);
        $result = json_decode(file_get_contents($url, false, $context));

        Yii::warning([
            'Запрос' => ['phone'=>$phone,'text'=>$text],
            'Ответ'=>ArrayHelper::toArray($result)
        ], 'sms');

        if (empty($result->result)){
            return false;
        }
        return true;
    }
}