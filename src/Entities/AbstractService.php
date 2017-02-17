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
 * Class AbstractService
 * @package Descent\Services\Entities
 */
abstract class AbstractService implements ServiceInterface
{
    /**
     * @var string
     */
    private $interface;

    /**
     * @var callable|string
     */
    private $concrete;

    /**
     * @var bool
     */
    private $isSingleton = false;

    /**
     * @var array
     */
    private $parameterBindings = [];

    /**
     * @var string[]
     */
    private $enforcedParameters = [];

    /**
     * @var object|null
     */
    private $instance;

    /**
     * AbstractService constructor.
     * @param string $interface
     * @param $concrete
     */
    final public function __construct(string $interface, $concrete)
    {
        $this->interface = $interface;
        $this->concrete = $this->dispatchConcrete($concrete);
    }

    /**
     * returns the service interface.
     *
     * @return string
     */
    final public function getInterface(): string
    {
        return $this->interface;
    }

    /**
     * returns the concrete.
     *
     * @return string|\Closure
     */
    final public function getConcrete()
    {
        return $this->concrete;
    }

    /**
     * defines the singleton state of the service.
     *
     * @param bool $flag
     * @return ServiceInterface
     */
    final public function singleton(bool $flag = true): ServiceInterface
    {
        $this->isSingleton = $flag;

        return $this;
    }

    /**
     * checks whether the incubated instance will be served as an instance or not.
     *
     * @return bool
     */
    final public function isSingleton(): bool
    {
        return $this->isSingleton;
    }

    /**
     * binds parameter assignments to the service.
     *
     * @param array $parameters
     * @return ServiceInterface
     */
    final public function withParameters(array $parameters): ServiceInterface
    {
        $this->parameterBindings = $parameters;

        return $this;
    }

    /**
     * returns the parameter bindings of the service.
     *
     * @return array
     */
    final public function getParameters(): array
    {
        return $this->parameterBindings;
    }

    /**
     * binds the optional parameters to be resolved.
     *
     * @param \string[] ...$parameters
     * @return ServiceInterface
     */
    final public function enforceParameters(string ... $parameters): ServiceInterface
    {
        $this->enforcedParameters = $parameters;

        return $this;
    }

    /**
     * returns an array of optional parameters being resolved.
     *
     * @return string[]
     */
    final public function getEnforcedParameters(): array
    {
        return $this->enforcedParameters;
    }

    /**
     * returns the current singleton instance of this object.
     *
     * @throws ServiceException when no instance is stored
     * @throws ServiceException when service does not result into a singleton
     * @return object
     */
    final public function getInstance()
    {
        if ( ! $this->isSingleton ) {
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
    final public function hasInstance(): bool
    {
        return $this->isSingleton && is_object($this->instance);
    }

    /**
     * sets the current instance of the service.
     *
     * @param $object
     * @throws ServiceException when service does not result into a singleton
     * @throws ServiceException when the provided object does not implement the serviced interface
     * @return ServiceInterface
     */
    final public function withInstance($object): ServiceInterface
    {
        if ( ! $this->isSingleton ) {
            throw new ServiceException(
                'This service can not hold an instance, the service is not defined as singleton'
            );
        }

        if ( ! is_a($object, $this->interface) ) {
            throw new ServiceException(
                'The provided object does not implement the interface of this service'
            );
        }

        $this->instance = $object;

        return $this;
    }

    /**
     * Dispatches the provided concrete.
     *
     * @param $concrete
     * @return string|callable
     */
    abstract protected function dispatchConcrete($concrete);
}