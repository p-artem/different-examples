<?php
namespace console\extensions\fileService;

/**
 * the Service must implement the ServiceInterface
 */

abstract class AbstractDriver
{
    /**
     * @var ServiceInterface
     */
    protected $wrapped;

    /**
     * @param ServiceInterface $service
     */
    public function __construct(ServiceInterface $service)
    {
        $this->wrapped = $service;
    }

    abstract function replaceData();
}