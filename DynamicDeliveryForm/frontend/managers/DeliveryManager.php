<?php
namespace frontend\managers;

use yii\db\Exception;
use common\models\Order;
use frontend\services\delivery\DeliveryServiceInterface;
use frontend\services\delivery\NovaPoshtaService;
use frontend\services\delivery\OurService;

/**
 * Class DeliveryManager
 * @package frontend\managers
 */
class DeliveryManager {

    /**
     * @param $deliveryId
     * @return NovaPoshtaService|OurService
     * @throws Exception
     */
    public function getService($deliveryId){
        $service = $this->loadService($deliveryId);
        if(!($service instanceof DeliveryServiceInterface)){
            throw new Exception('Class not instanceof from DeliveryServiceInterface');
        }
        return $service;
    }

    /**
     * @param $deliveryId
     * @return NovaPoshtaService|OurService
     */
    private function loadService($deliveryId){
        switch ($deliveryId){
            case in_array($deliveryId, [Order::DELIVERY_NOVA_POSHTA_EXPRESS, Order::DELIVERY_NOVA_POSHTA]):
                return new NovaPoshtaService();
            default: return new OurService();
        }
    }
}