<?php
namespace frontend\dto;

/**
 * Class DeliveryAttrDTO
 * @package frontend\helpers
 */
class DeliveryAttrDTO
{
   public $id;
   public $container;
   public $inputName;

   public function __construct($id, $container, $inputName)
   {
       $this->id = $id;
       $this->container = $container;
       $this->inputName = $inputName;
   }
}


