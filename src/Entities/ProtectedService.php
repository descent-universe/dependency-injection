<?php
/**
 * This file is part of the Descent Framework.
 *
 * (c)2017 Matthias Kaschubowski
 *
 * This code is licensed under the MIT license,
 * a copy of the license is stored at the project root.
 */

namespace Descent\Services\Entities;


use Descent\Services\Exceptions\ServiceException;

/**
 * Class ProtectedService
 * @package Descent\Services\Entities
 */
final class ProtectedService implements ServiceInterface
{
    /**
     * @var ServiceInterface
     */
    private $service;

    /**
     * @var object|null
     */
    private $instance;

    /**
     * ProtectedService constructor.
     * @param ServiceInterface $service
     */
    public function __construct(ServiceInterface $service)
    {
        $this->service = $service;

        if ( $service->hasInstance() ) {
            $this->instance = $service->getInstance();
        }
    }

    /**
     * returns the service interface.
     *
     * @return string
     */
    public function getInterface(): string
    {
        return $this->service->getInterface();
    }

    /**
     * returns the concrete.
     *
     * @return string|\Closure
     */
    public function getConcrete()
    {
        return $this->service->getConcrete();
    }

    /**
     * defines the singleton state of the service.
     *
     * @param bool $flag
     * @return ServiceInterface
     */
    public function singleton(bool $flag = true): ServiceInterface
    {
        throw new ServiceException(
            'You can not modify a protected service'
        );
    }

    /**
     * checks whether the incubated instance will be served as an instance or not.
     *
     * @return bool
     */
    public function isSingleton(): bool
    {
        return $this->service->isSingleton();
    }

    /**
     * binds parameter assignments to the service.
     *
     * @param array $parameters
     * @return ServiceInterface
     */
    public function withParameters(array $parameters): ServiceInterface
    {
        throw new ServiceException(
            'You can not modify a protected service'
        );
    }

    /**
     * returns the parameter bindings of the service.
     *
     * @return array
     */
    public function getParameters(): array
    {
        return $this->service->getParameters();
    }

    /**
     * binds the optional parameters to be resolved.
     *
     * @param \string[] ...$parameters
     * @return ServiceInterface
     */
    public function enforceParameters(string ... $parameters): ServiceInterface
    {
        throw new ServiceException(
            'You can not modify a protected service'
        );
    }

    /**
     * returns an array of optional parameters being resolved.
     *
     * @return string[]
     */
    public function getEnforcedParameters(): array
    {
        return $this->service->getEnforcedParameters();
    }

    /**
     * returns the current singleton instance of this object.
     *
     * @throws ServiceException when no instance is stored
     * @throws ServiceException when service does not result into a singleton
     * @return object
     */
    public function getInstance()
    {
        if ( ! $this->isSingleton() ) {
            throw new ServiceException(
                'This service can not hold an instance, the service is not defined as singleton'
            );
        }

        if ( ! is_object($this->instance) ) {
            throw new ServiceException(
                'This service has no instance yet'
            );
        }

        return $this->instance;
    }

    /**
     * checks whether the service holds an instance and the service aims for a singleton.
     *
     * @return bool
     */
    public function hasInstance(): bool
    {
        return $this->isSingleton() && is_object($this->instance);
    }

    /**
     * sets the current instance of the service.
     *
     * @param $object
     * @throws ServiceException when service does not result into a singleton
     * @throws ServiceException when the provided object does not implement the serviced interface
     * @return ServiceInterface
     */
    public function withInstance($object): ServiceInterface
    {
        if ( ! $this->isSingleton() ) {
            throw new ServiceException(
                'This service can not hold an instance, the service is not defined as singleton'
            );
        }

        if ( ! is_a($object, $this->getInterface()) ) {
            throw new ServiceException(
                'The provided object does not implement the interface of this service'
            );
        }

        $this->instance = $object;

        return $this;
    }

}