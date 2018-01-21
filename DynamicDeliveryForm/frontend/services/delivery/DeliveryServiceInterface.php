<?php
namespace frontend\services\delivery;

interface DeliveryServiceInterface{

    public function getCities();
    public function getDepartments();
    public function findCity();
    public function findDepartment();
}