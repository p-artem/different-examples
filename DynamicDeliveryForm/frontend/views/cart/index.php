<?php
/**
 * @var $this yii\web\View
 * @var $positions \common\models\ProductCartPosition[]
 * @var $form yii\widgets\ActiveForm
 * @var $orderForm \frontend\models\OrderForm
 * @var $loginForm \frontend\modules\user\models\LoginForm
 * @var $signupForm \frontend\modules\user\models\SignupFastForm
 * @var $promoForm \frontend\models\PromoForm
 * @var $deliveryForm \frontend\models\DeliveryForm
 */

use yii\bootstrap\ActiveForm;
use yii\helpers\Url;
use frontend\widgets\SiteBreadcrumbs;
use yii\widgets\Pjax;
use frontend\helpers\Number;
use frontend\helpers\Currency;
use yii\helpers\ArrayHelper;
use common\models\Order;

$this->title = Yii::t('site', 'Cart');
?>
<div class="container">
    <div class="breadcrumbs hidden-xs hidden-sm">
        <?= SiteBreadcrumbs::widget(['links' => ['label' => $this->title]]) ?>
    </div>
</div>

<div class="page-header">
    <div class="h2-title"><?= Yii::t('site', 'Ordering') ?></div>
</div>

<?php if (empty($positions = Yii::$app->cart->positions)): ?>

    <div class="cart-empty-wrap">
        <div class="cart-empty">

            <p><?= Yii::t('site', 'No items found in your shopping cart.') ?></p>

            <div class="btn-wrap">
                <a href="<?= Url::to(['/category/view']) ?>" class="btn-accent"><?= Yii::t('site', 'Run to catalog') ?></a>
            </div>
        </div>
    </div>

<?php else : ?>

    <div class="cart-inner-wrap">
        <div class="container">

            <div class="order-reg-wrap">

                <?php $form = ActiveForm::begin([
                    'id'=>'order-form',
                    'action' => ['/cart'],
                    'options'=>['class' => 'order-form'],
                    'fieldConfig'=>[
                        'inputOptions'=>['class' => false],
                        'options'=>[
                            'tag'=> 'label',
                            'class'=>'cust-inp',
                        ],
                        'template'=>'{input}{error}',
                    ],
                ]);
                $paymentOptions = ['tag'=> 'div', 'class'=>'cust-sel field_payment', 'data-sibling' => true];
                $deliveryOptions = ['tag'=> 'div', 'class'=>'cust-sel field_delivery', 'data-sibling' => true];
                ?>

                    <div class="row">
                        <div class="col-md-9">

                            <?php if (Yii::$app->user->isGuest): ?>

                                <div class="info-tabs cart-tabs ">
                                    <div class="tabs__caption-wrap">
                                        <ul class="tabs__caption">
                                            <li class="active">
                                                <span><?= Yii::t('site', 'New customer') ?></span>
                                            </li>
                                            <li>
                                                <span><?= Yii::t('site', 'I\'m already registered') ?></span>
                                            </li>
                                        </ul>
                                        <div class="tab-marker"></div>
                                    </div>

                                    <div class="tabs__content-wrap">
                                        <div class="tabs__content active">

                                            <div class="order-auth">

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <?= $form->field($signupForm, 'username')
                                                            ->textInput(['placeholder'=>$signupForm->getAttributeLabel('username')]) ?>
                                                        <?= $form->field($signupForm, 'email')
                                                            ->textInput(['placeholder'=>$signupForm->getAttributeLabel('email')]) ?>
                                                        <?= $form->field($signupForm, 'phone_input')
                                                            ->widget(\borales\extensions\phoneInput\PhoneInput::className(), [
                                                            'jsOptions' => [
                                                                'allowExtensions' => true,
                                                                'preferredCountries' => ['ua'],
                                                                'separateDialCode'=> true,
                                                                'nationalMode'=> true
                                                            ]
                                                        ]); ?>
                                                        <?= $form->field($signupForm, 'phone', ['template'=>'{input}'])->hiddenInput() ?>
                                                    </div>

                                                    <div class="col-sm-6">
                                                        <?= $form->field($orderForm, 'text', ['options' => ['class' => 'cust-textarea']])
                                                            ->textarea(['placeholder' => $orderForm->getAttributeLabel('text')]) ?>
                                                    </div>
                                                </div>

                                                    <div class="check-line">
                                                        <?= $form->field(
                                                            $signupForm,
                                                            'agreement',
                                                            [
                                                                'options' => ['class' => 'cust-check'],
                                                                'template' => '{error}{input}
                                                                    <i class="check-ic"></i>
                            
                                                                    <div class="check-descr">
                                                                        <span>'.  Yii::t('site', 'I agree with') .' </span>
                                                                        <a href="'. Url::to(['page/view', 'slug' => 'soglashenie']) .'"><span>'. Yii::t('site', 'the terms of the public offer') .'</span></a>
                                                                    </div>
                                                                    <p class="help-block"></p>'
                                                            ])
                                                            ->checkbox([], false) ?>
                                                    </div>

                                            </div>

                                        </div>


                                        <div class="tabs__content">
                                            <div class="order-auth">

                                                <div class="row">
                                                    <div class="col-sm-6">
                                                        <?= $form->field($loginForm, 'email')
                                                            ->textInput(['placeholder'=>$loginForm->getAttributeLabel('email')]) ?>
                                                        <?= $form->field($loginForm, 'password')
                                                            ->passwordInput(['placeholder'=>$loginForm->getAttributeLabel('password')]) ?>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <?= $form->field($orderForm, 'text', ['options' => ['class' => 'cust-textarea']])
                                                            ->textarea(['placeholder' => $orderForm->getAttributeLabel('text')]) ?>
                                                    </div>
                                                </div>

                                            </div>
                                        </div>

                                    </div>

                                </div>
                            <?php else: ?>

                                <div class="order-auth">
                                    <div class="order-signup">
                                        <div class="row">
                                            <div class="col-sm-6">
                                                <?= $form->field($signupForm, 'username')
                                                    ->textInput([
                                                        'value' => Yii::$app->user->identity->username,
                                                        'disabled' => true,
                                                        'placeholder' => $loginForm->getAttributeLabel('username')]) ?>
                                                <?= $form->field($signupForm, 'email')
                                                    ->textInput([
                                                        'value' => Yii::$app->user->identity->email,
                                                        'disabled' => true,
                                                        'placeholder' => $loginForm->getAttributeLabel('email')]) ?>
                                                <?= $form->field($signupForm, 'phone_input')
                                                    ->widget(\borales\extensions\phoneInput\PhoneInput::className(), [
                                                        'options' => [
                                                            'value' => Yii::$app->user->identity->phone,
                                                            'placeholder' => $signupForm->getAttributeLabel('phone'),
                                                        ],
                                                        'jsOptions' => [
                                                            'allowExtensions' => true,
                                                            'preferredCountries' => ['ua'],
                                                            'separateDialCode' => true,
                                                            'nationalMode' => true
                                                        ]
                                                    ]); ?>
                                                <?= $form->field($signupForm, 'phone', ['template' => '{input}'])
                                                    ->hiddenInput(['value' => Yii::$app->user->identity->phone]) ?>
                                            </div>

                                            <div class="col-sm-6">
                                                <label class="cust-textarea">
                                                    <?= $form->field($orderForm, 'text')
                                                        ->textarea(['placeholder' => $orderForm->getAttributeLabel('text')]) ?>
                                                </label>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            <?php endif; ?>

                           <div class="cart-pay-form cart-pay-top hidden-md hidden-lg">
                                <div class="cart-pay-sett">
                                    <div class="clearfix">
                                        <div class="promo-content-form">
                                            <?= $this->render('_promo_form', ['promoForm' => $promoForm]) ?>
                                        </div>

                                        <?= $form->field(
                                            $orderForm, 'payment_id',
                                            ['inputOptions' => ['id' => $form->id . '_payment_id_mobile'], 'options' => $paymentOptions
                                            ])->dropDownList(Order::getPaymentMethods(), ['prompt' => $orderForm->getAttributeLabel('payment_id')]) ?>

                                        <?= $form->field(
                                            $orderForm,
                                            'delivery_id',
                                            ['inputOptions' => ['id' => $form->id . '_delivery_id_mobile'], 'options' => $deliveryOptions
                                            ])->dropDownList(Order::getActiveDeliveryMethods(), ['prompt' => $orderForm->getAttributeLabel('delivery_id')]) ?>
                                    </div>

                                    <div class="delivery-mobile-content">
                                        <?php if($view = $deliveryForm->getDeliveryView()): ?>
                                            <?= $this->render('delivery/'. $view, [
                                                'model'     => $deliveryForm,
                                                'forMobile' => true
                                            ]);?>
                                        <?php endif ?>
                                    </div>

                                </div>
                            </div>

                            <?php Pjax::begin([
                                'id' => 'cart-content',
                                'linkSelector' => false
                            ]) ?>

                            <?php
                                $total = Number::format(Yii::$app->cart->getCost());
                                $totalWithDiscount = Number::format(Yii::$app->cart->getCost(true) - $promoForm->sale);
                            ?>
                            <div class="cart-set">
                                <div hidden>
                                    <div data-promo-block><?= $this->render('_promo_form', ['promoForm' => $promoForm]) ?></div>
                                    <div data-order-total-block><?= $this->render('_order_total_info', ['total' => $total, 'totalWithDiscount' => $totalWithDiscount]) ?></div>
                                </div>

                                <ul class="cart-lst">

                                    <?php foreach ($positions as $position):
                                        $product = $position->product;
                                        $url = Url::to(['product/view', 'slug' => $product->slug]);

                                        /** @var $itemSize \common\models\ProductSize */
                                        $itemSize = ArrayHelper::getValue($product->getSizes(), $position->size_id);
                                    ?>
                                        <li class="cart-itm wish-prod-item" data-position="<?= $position->getId() ?>">

                                        <div class="cart-itm-cell">
                                            <a href="<?= $url ?>" class="cart-itm-img" style="background-image: url(<?= $product->imageUrl ?>);">
                                                <img src="/img/_style/square.png" alt="cart-itm-img">
                                            </a>
                                        </div>

                                        <div class="cart-itm-cell">
                                            <div class="cart-itm-top">
                                                <div class="cart-itm-top-l">
                                                    <a href="<?= $url ?>" class="cart-itm-title">
                                                        <span><?= $product->name ?></span>
                                                    </a>
                                                    <div class="cart-prod-info">
                                                        <div class="cart-itm-part"><?= $product->part_numb ?></div>
                                                        <div class="cart-fin-price">
                                                            <div class="price"><?= Number::format($position->price) ?> <span class="price-currency"><?= Currency::getSymbol() ?></span></div>
                                                            <div class="price-count">x<?= $position->quantity ?></div>

                                                        </div>
                                                    </div>
                                                </div>

                                                <div class="cart-itm-top-r">
                                                    <div class="cart-itm-price">
                                                        <span class="cart-price"><?= Number::format($position->price) ?> <span class="price-currency"><?= Currency::getSymbol() ?></span></span>
                                                        <?php if($position->getPrice('price_old') > 0): ?>
                                                            <span class="cart-old-price"><?= Number::format($position->getPrice('price_old')) ?> <span class="price-currency"><?= Currency::getSymbol() ?></span></span>
                                                        <?php endif ?>
                                                    </div>

                                                    <a href="#" class="cart-itm-del wish-remove">
                                                        <i class="del-ic">&nbsp;</i>
                                                    </a>
                                                </div>
                                            </div>

                                            <table class="cart-itm-bottom">
                                                <tbody><tr>
                                                    <td>
                                                        <strong><?= Yii::t('site', 'Size:') ?></strong>
                                                        <span><?= ($itemSize) ? $itemSize->size->name : '-' ?></span>
                                                    </td>
                                                    <td>
                                                        <strong><?= Yii::t('site', 'Color:') ?></strong>
                                                        <span><?= ($color = $product->color) ? $color->name : '-' ?></span>
                                                    </td>
                                                    <td>
                                                        <strong><?= Yii::t('site', 'Quantity:') ?></strong>
                                                        <span>
                                                            <div class="prod-count">
                                                                <span class="wish-dn">&nbsp;</span>
                                                                <input class="wish-input" type="text" name="quantity" value="<?= $position->quantity ?>" required>
                                                                <span class="wish-up">&nbsp;</span>
                                                            </div>
                                                        </span>

                                                    </td>
                                                </tr></tbody>
                                            </table>
                                        </div>

                                    </li>

                                    <?php endforeach; ?>
                                </ul>

                            </div>

                            <?php Pjax::end() ?>
                        </div>

                        <div class="col-md-3">

                            <div class="cart-info-wrap">

                                <div class="cart-pay-form cart-pay-top hidden-xs hidden-sm">
                                    <div class="cart-pay-sett">

                                        <div class="promo-content-form">
                                            <?= $this->render('_promo_form', ['promoForm' => $promoForm]) ?>
                                        </div>

                                        <?= $form->field($orderForm, 'payment_id', ['options' => $paymentOptions])
                                            ->dropDownList(Order::getPaymentMethods(), ['prompt' => $orderForm->getAttributeLabel('payment_id')]) ?>

                                        <?= $form->field($orderForm, 'delivery_id', ['options' => $deliveryOptions])
                                            ->dropDownList(Order::getActiveDeliveryMethods(), ['prompt' => $orderForm->getAttributeLabel('delivery_id')]) ?>

                                        <div class="delivery-content">
                                            <?php if($view = $deliveryForm->getDeliveryView()): ?>
                                                <?= $this->render('delivery/'. $view, [
                                                    'model'     => $deliveryForm,
                                                    'forMobile' => false
                                                ]);?>
                                            <?php endif ?>
                                        </div>

                                    </div>
                                </div>


                                <div class="cart-pay-bottom">

                                    <div class="order-total-info">
                                        <?= $this->render('_order_total_info', ['total' => $total, 'totalWithDiscount' => $totalWithDiscount]) ?>
                                    </div>

                                    <div class="order-btns">
                                        <div class="order-btns-cell">
                                            <a href="#" class="btn-back hidden-xs hidden-md hidden-lg">
                                                <i class="arr-l-ic">&nbsp;</i>
                                                <span><?= Yii::t('site', 'Continue shopping')?></span>
                                            </a>
                                        </div>
                                        <div class="order-btns-cell">
                                            <a href="<?= Url::to(['/cart']) ?>" class="btn-accent" id="add-order" data-method="post"><?= Yii::t('site', 'Add order') ?> </a>
                                        </div>
                                        <div class="order-btns-cell">
                                            <a href="<?= Url::to(['/category/view']) ?>" class="btn-back hidden-sm">
                                                <i class="arr-l-ic">&nbsp;</i>
                                                <span><?= Yii::t('site', 'Back to shopping') ?></span>
                                            </a>
                                        </div>
                                    </div>
                                </div>


                            </div>

                        </div>
                    </div>

                <?php ActiveForm::end() ?>

            </div>

        </div>
    </div>

<?php $this->registerJs(<<<JS

$('#{$form->id}').on('beforeSubmit', function (e) {
    $(this).find('#add-order').attr('dusabled','true');
});
$('#{$form->id}').on('beforeValidate', function() {
    
     if($('#signupfastform-phone_input').length){
         $('#signupfastform-phone').val($('#signupfastform-phone_input').intlTelInput('getNumber'));
     }
    
    if($('#deliveryform-phone_input').length){
        $('#deliveryform-recipient_phone').val($('#deliveryform-phone_input').intlTelInput('getNumber'));
    }
    if($('#deliveryform-phone_input-mobile').length){
        $('#deliveryform-recipient_phone-mobile').val($('#deliveryform-phone_input_mobile').intlTelInput('getNumber'));
    } 
    
});
JS
, \yii\web\View::POS_READY); ?>

<?php endif ?>
